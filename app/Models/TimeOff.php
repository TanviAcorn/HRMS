<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use App\EmployeeModel;
use App\Login;

class TimeOff extends BaseModel
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
    	$this->table = config('constants.TIME_OFF_MASTER_TABLE');
    }
    public function approvedByInfo(){
    	return $this->belongsTo(Login::class,'i_approved_by_id');
    }
    public function employeeInfo(){
    	return $this->belongsTo(EmployeeModel::class,'i_employee_id');
    }
    public function createdInfo(){
    	return $this->belongsTo(Login::class,'i_created_id');
    }
    public function getRecordDetails( $where = [] , $likeData = [] , $additionalData = [] ){
    	//$query = TimeOff::where('t_is_deleted' ,0);
    	$query = TimeOff::with(['createdInfo', 'employeeInfo' ,'employeeInfo.designationInfo',  'approvedByInfo' ]);
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$masterRecordId = $where['master_id'];
    		$query->where('i_id','=',$masterRecordId);
    	}
    	
    	if(isset($where['time_off_status']) && (!empty($where['time_off_status'])) ){
    		$timeOffStatus = $where['time_off_status'];
    		$query->whereIn('e_status', $timeOffStatus);
    	}
    	
    	if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
    		$employeeId = $where['employee_id'];
    		$query->where('i_employee_id','=',$employeeId);
    	}

    	if(isset($where['team_record']) && (!empty($where['team_record'])) ){
    		$teamRecordId = $where['team_record'];
    		$query->whereHas('employeeInfo' , function ($q) use($teamRecordId){
    			$q->where('i_team_id',$teamRecordId);
    		});
    	}
    	if(isset($where['time_off_from_date']) && (!empty($where['time_off_from_date'])) ){
    		$fromDate = dbDate ( $where['time_off_from_date'] );
    		$query->whereRaw("(  dt_time_off_date >= '".$fromDate."' )");
    	}
    	if(isset($where['time_off_to_date']) && (!empty($where['time_off_to_date'])) ){
    		$toDate = dbDate ( $where['time_off_to_date'] );
    		$query->whereRaw("(  dt_time_off_date <= '".$toDate."')");
    	}
    	
    	
    	if(isset($where['time_off_back_from_date']) && (!empty($where['time_off_back_from_date'])) ){
    		$fromBackDate = dbDate ( $where['time_off_back_from_date'] );
    		$query->whereRaw("(  dt_time_off_back_date >= '".$fromBackDate."' )");
    	}
    	if(isset($where['time_off_back_to_date']) && (!empty($where['time_off_back_to_date'])) ){
    		$toBackDate = dbDate ( $where['time_off_back_to_date'] );
    		$query->whereRaw("(  dt_time_off_back_date <= '".$toBackDate."')");
    	}
    	
    	
    	
    	if(isset($where['time_off_type']) && (!empty($where['time_off_type'])) ){
    		$timeOffType = $where['time_off_type'];
    		$query->where('e_record_type', $timeOffType);
    	}
    	
    	if(session()->get('role') == config('constants.ROLE_USER')){
    		
    		if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
    			unset($where['show_all']);
    		} else {
    			$employeeId = session()->get('user_employee_id');
    			$query->whereHas('employeeInfo' , function ($q) use($employeeId){
    				$q->where(function ($q1)use($employeeId){
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
    	
    	if(isset($where['employment_relieved_status']) && (!empty($where['employment_relieved_status'])) ){
    		$employmentRelievedStatus = $where['employment_relieved_status'];
    		$query->whereHas('employeeInfo' , function($query) use($employmentRelievedStatus) {
    			$query->whereNotIn('e_employment_status',$employmentRelievedStatus);
    		});
    	}
    	
    	if((!empty(session()->get('user_employee_id'))) && ( in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ))){
    		$where['employee_id'] = session()->get('user_employee_id');
    	}
    	
    	$query->orderBy('i_id', "DESC" );
    	
    	if( isset($where['offset']) ){
    		$query->skip($where ['offset']);
    	}
    	
    	if( isset($where['limit']) ){
    		$query->take($where['limit']);
    	}
    	
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	
    	return $data;
    }
}
