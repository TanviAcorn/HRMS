<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use App\LeaveTypeMasterModel;
use App\EmployeeModel;
use App\MyLeaveModel;
use DB;

class LeaveSummaryModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
    const CREATED_AT = 'dt_created_at';
    const UPDATED_AT = 'dt_updated_at';
    const DELETED_AT = 'dt_deleted_at';
    
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.LEAVE_SUMMARY_TABLE');
    }
    
    protected $fillable = ['i_employee_id', 'i_leave_type_id', 'e_leave_type'];
    
    public function leaveTypeInfo(){
    	return $this->belongsTo(LeaveTypeMasterModel::class,'i_leave_type_id');
    }
    public function employeeInfo(){
    	return $this->belongsTo(EmployeeModel::class,'i_employee_id');
    }
    
    public function getTakenLeaveDetails($where = [] ){
    	
    	$query = MyLeaveModel::with(['employeeInfo']);
    	
    	if( isset($where['startDate']) && (!empty($where['startDate'])) ){
    		$startDate = $where['startDate'];
    		$query->whereRaw("( (  dt_leave_from_date >= '".$startDate."'  or dt_leave_to_date >= '".$startDate."'  ) )");
    	}
    	 
    	if( isset($where['endDate']) && (!empty($where['endDate'])) ){
    		$endDate = $where['endDate'];
    		$query->whereRaw("( (  dt_leave_from_date <= '".$endDate."'  or dt_leave_to_date <= '".$endDate."'  ) )");
    	}
    	
    	if( isset($where['leaveStatus']) && (!empty($where['leaveStatus'])) ){
    		$query->whereIn( 'e_status' ,  $where['leaveStatus'] );
    	}
    	
    	if(session()->get('role') == config('constants.ROLE_USER')){
    		if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
    			unset($where['show_all']);
    		} else {
    			$employeeId = session()->get('user_employee_id');
    			$query->whereHas('employeeInfo' , function ($q) use($employeeId){
    				$q->where(function ($q1) use($employeeId){
    					/* $q1->where('i_id',$employeeId);
    					$q1->orWhere('i_leader_id',$employeeId); */
    					
    					$allChildEmployeeIds = $this->childEmployeeIds();
    					if(!empty($allChildEmployeeIds)){
    						$q1->whereIn('i_id', $allChildEmployeeIds);
    					}
    				});
    			});
    		}
    	}
    	
    	if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
    		$employmentStatus = $where['employment_status'];
    		$query->whereHas('employeeInfo' , function($query) use($employmentStatus) {
    			if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
    				$query->whereIn('e_employment_status',[config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')]);
    			}else {
    				$query->where('e_employment_status',$employmentStatus);
    			}
    		});
    	}
    	
    	
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	
    	return $data;
    }
    
    public function getNotTakenLeaveDetails( $where = [] , $likeData = [] , $additionalData = []  ){
    	
    	$defaultWhere = [];
    	$defaultWhere['em.t_is_deleted != ' ] = 1 ;
    	$defaultWhere['group_by'] = "em.i_id";
    	
    	$tableName = config('constants.EMPLOYEE_MASTER_TABLE'). ' as em';
    		
    	$selectData = [
    			'em.i_id',
    			'em.v_employee_full_name',
    			'em.v_employee_name',
    			DB::raw('sum(alm.d_no_days) as apply_leave_count')
    	];
    	
    	$startDate = monthStartDate();
    	$endDate = monthEndDate();
    	
    	if(isset($where['startDate'])){
    		$startDate = $where['startDate'];
    		unset($where['startDate']);
    	}
    	
    	if(isset($where['endDate'])){
    		$endDate = $where['endDate'];
    		unset($where['endDate']);
    	}
    	
    	if(isset($where['endDate'])){
    		$endDate = $where['endDate'];
    		unset($where['endDate']);
    	}
    	
    	if(session()->get('role') == config('constants.ROLE_USER')){
    		if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
    			unset($where['show_all']);
    		} else {
    			/* $employeeId = session()->get('user_employee_id');
    			$where['custom_function'][] = "(em.i_id = '".$employeeId."' or  em.i_leader_id = '".$employeeId."')"; */
    			
    			$allChildEmployeeIds = $this->childEmployeeIds();
    			if(!empty($allChildEmployeeIds)){
    				$additionalData['whereIn'][] = ['em.i_id' , $allChildEmployeeIds];
    			}
    		}
    	}
    	
    	if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
    		$employmentStatus = $where['employment_status'];
    		if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
    			$additionalData['whereIn'][] = [ 'e_employment_status' ,   [config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')] ];
    		} else {
    			$where['e_employment_status'] = $employmentStatus;
    		}
    		unset($where['employment_status']);
    	}
    		
    	$joinData = [
    			[
    					'tableName' =>	config('constants.LOOKUP_MASTER_TABLE') . ' as designation',
    					'condition' =>	"designation.i_id = em.i_designation_id",
    			],
    			[
    					'tableName' =>	config('constants.APPLY_LEAVE_MASTER_TABLE') . ' as alm',
    					'condition' => [ 'custom' => "em.i_id = alm.i_employee_id and (alm.e_status in ('".config("constants.PENDING_STATUS")."' , '".config("constants.APPROVED_STATUS")."') ) and (alm.dt_leave_from_date) >= '".$startDate."' and (alm.dt_leave_to_date) <= '".$endDate."'" ],
    					'type' => 'left'
    			],
    	];
    		
    	$whereData = (!empty($where) ? array_merge( $defaultWhere , $where) : $defaultWhere );
    		
    	if( $this->singleRecord == true ){
    		$data = $this->getSingleRecordWithJoinById( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
    	} else {
    		$data = $this->selectJoinData( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
    	}
    	
    	return $data;
    	
    } 
}
