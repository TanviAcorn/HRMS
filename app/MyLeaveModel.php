<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use DB;
use App\Models\LeaveSummaryModel;

class MyLeaveModel extends BaseModel
{
	use MySoftDeletes;
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.APPLY_LEAVE_MASTER_TABLE');
	}
	public function leaveTypeInfo(){
		return $this->belongsTo(LeaveTypeMasterModel::class,'i_leave_type_id');
	}
	public function employeeInfo(){
		return $this->belongsTo(EmployeeModel::class,'i_employee_id');
	}
	
	public function leaveSummaryInfo(){
		return $this->hasMany(LeaveSummaryModel::class,'i_apply_leave_id');
	}
	/* public function approvedByInfo(){
		return $this->belongsTo(Login::class,'i_approved_by_id');
	} */
	public function approvedByInfo(){
		return $this->belongsTo(Login::class,'i_approved_by_id');
	}
	public function createdInfo(){
		return $this->belongsTo(Login::class,'i_created_id');
	}
	public function getRecordDetails( $where = [] , $likeData = [] ){
		//$query = MyLeaveModel::where('t_is_deleted' ,0);
		$query = MyLeaveModel::with(['createdInfo','leaveTypeInfo','employeeInfo','approvedByInfo' , 'employeeInfo.loginInfo' , 'employeeInfo.leaderInfo.loginInfo' , 'employeeInfo.latestGeneratedSalary' ]);
		$query->select( [ '*' , DB::raw('dt_created_at as action_date') , DB::raw('dt_leave_from_date as compare_date')  , DB::raw('"applied_leave" as record_type')] );
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			$query->where('i_id','=',$masterRecordId);
		}
		
		if(isset($where['active_employee_leave']) && (!empty($where['active_employee_leave'])) ){
			$query->whereHas('employeeInfo' , function($query){
				$query->where('e_employment_status', '!='  , config('constants.RELIEVED_EMPLOYMENT_STATUS') );
			});
		}
		
		if(isset($where['leave_status']) && (!empty($where['leave_status'])) ){
			$leaveStatus = $where['leave_status'];
			$query->whereIn('e_status', $leaveStatus);
		}
		
		if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
			$employeeId = $where['employee_id'];
			$query->where('i_employee_id','=',$employeeId);
		}
		if(isset($where['team_record']) && (!empty($where['team_record'])) ){
			$teamRecordId = $where['team_record'];
			
			$query->whereHas('employeeInfo' , function($q) use($teamRecordId){
				$q->where('i_team_id',$teamRecordId);
			});
			
		}
		if(isset($where['leave_from_date']) && (!empty($where['leave_from_date'])) ){
			$fromDate = dbDate ( $where['leave_from_date'] );
			$query->whereRaw("(  dt_leave_from_date >= '".$fromDate."'  or dt_leave_to_date >= '".$fromDate."'  )");
		}
		if(isset($where['leave_to_date']) && (!empty($where['leave_to_date'])) ){
			$toDate = dbDate ( $where['leave_to_date'] );
			$query->whereRaw("(  dt_leave_from_date <= '".$toDate."'  or dt_leave_to_date <= '".$toDate."'  )");
		}
		if(isset($where['leave_type']) && (!empty($where['leave_type'])) ){
			$leaveTypeId = $where['leave_type'];
			$query->where('i_leave_type_id','=',$leaveTypeId);
		}
		
		if(isset($where['employee_team']) && (!empty($where['employee_team'])) ){
			$teamId = $where['employee_team'];
			$query->whereHas('employeeInfo' , function($query) use($teamId) {
				$query->where('i_team_id',$teamId);
			});
		}
		
		if(isset($where['employee_designation']) && (!empty($where['employee_designation'])) ){
			$designationId = $where['employee_designation'];
			$query->whereHas('employeeInfo' , function($query) use($designationId) {
				$query->where('i_designation_id',$designationId);
			});
		}
		if(isset($where['leave_duration']) && (!empty($where['leave_duration']))){
			$leaveDuration = $where['leave_duration'];
			$query->whereRaw("(  e_duration = '".$leaveDuration."'  or e_to_duration = '".$leaveDuration."' or e_from_duration = '".$leaveDuration."'  )");
		}
		
		if(isset($where['auto_approve_leave'])){
			$query->where( 't_is_auto_approve' , $where['auto_approve_leave']  );
		}
		
		
		
		if((!empty($where)) && array_key_exists('order_by', $where)){
			$orderByColumn = $where['order_by'];
		
			if(!empty($orderByColumn)){
				foreach($orderByColumn as  $key => $value){
					$query->orderBy($key, (!empty($value) ? $value : 'DESC' ) );
				}
			}
		} else {
			$query->orderBy('dt_leave_from_date', "DESC" ) ;
		}
		
		if( isset($where['offset']) ){
			$query->skip($where ['offset']);
		}
		
		if(session()->get('role') == config('constants.ROLE_USER')){
			
			if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
				unset($where['show_all']);
			} else {
				$employeeId = session()->get('user_employee_id');
				$query->whereHas('employeeInfo' , function($query) use($employeeId) {
					/* $query->where('i_id',$employeeId);
					$query->orWhere('i_leader_id',$employeeId); */
					
					$allChildEmployeeIds = $this->childEmployeeIds();
					if(!empty($allChildEmployeeIds)){
						$query->whereIn('i_id', $allChildEmployeeIds);
					}
				});
			}
			
			
		} else {
			if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
				$employeeId = $where['employee_id'];
				
				$query->whereHas('employeeInfo' , function($query) use($employeeId) {
					/* $query->where('i_id',$employeeId);
					$query->orWhere('i_leader_id',$employeeId); */

					$allChildEmployeeIds = $this->childEmployeeIds($employeeId);
					if(!empty($allChildEmployeeIds)){
						$query->whereIn('i_id', $allChildEmployeeIds);
					}
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
