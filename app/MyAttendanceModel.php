<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use DB;
class MyAttendanceModel extends BaseModel
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
		$this->table = config('constants.EMPLOYEE_ATTENDANCE_TABLE');
	}
	public function employeeInfo(){
		//return $this->belongsTo(EmployeeModel::class,'i_employee_id');
		return $this->belongsTo(EmployeeModel::class,'i_employee_id')->select('*', DB::raw('LOWER(v_employee_full_name) as full_name_lower'));
	}
	public function employeeInfoNameWise(){
		return $this->belongsTo(EmployeeModel::class,'i_employee_id')->orderByRaw('v_employee_full_name ASC');
	}
	
	
	public function getManageAttendanceRecordDetails( $where = [] , $likeData = [] , $additionalData = [] ){
		unset($where['editAttendanceSreen']);
		$defaultWhere = [];
		$defaultWhere['ea.t_is_deleted != ' ] = 1 ;
		$defaultWhere['group_by'] = "ea.i_id";
		$defaultWhere['order_by'] = [ 'ea.dt_date' => 'asc' , 'em.v_employee_full_name' => 'asc' ];
		$tableName = config('constants.EMPLOYEE_ATTENDANCE_TABLE'). ' as ea';
		
		$selectData = [
				'ea.*',
				'em.v_employee_full_name',
				'em.v_employee_code',
				'designation.v_value as designation',
				'team.v_value as team',
				DB::raw('TIMESTAMPDIFF(SECOND, t_original_end_time , t_end_time ) AS departure_time_diff'),
				DB::raw('TIMESTAMPDIFF(SECOND, t_original_start_time , t_start_time ) AS arrival_time_diff'),
				DB::raw('TIME_TO_SEC(t_total_break_time ) AS break_time_into_sec'),
		];
		
		$whereInData = [];
		if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
			$employmentStatus = $where['employment_status'];
			if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
				$whereInData[] = [ 'em.e_employment_status' ,   [config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')] ];
			} else {
				$where['em.e_employment_status'] = $employmentStatus;
			}
			unset($where['employment_status']);
		}
		
		$joinData = [
				[
						'tableName' =>	config('constants.EMPLOYEE_MASTER_TABLE') . ' as em',
						'condition' =>	"em.i_id = ea.i_employee_id",
				],
				[
						'tableName' =>	config('constants.LOOKUP_MASTER_TABLE') . ' as designation',
						'condition' =>	"designation.i_id = em.i_designation_id",
				],
				[
						'tableName' =>	config('constants.LOOKUP_MASTER_TABLE') . ' as team',
						'condition' =>	"team.i_id = em.i_team_id",
				],
		];
		
		if(isset($where['attendance_from_month']) && (!empty($where['attendance_from_month'])) ){
			$attendanceFromDate = dbDate($where['attendance_from_month']);
			$where['custom_function'][] =  "date(ea.dt_date) >=  '".$attendanceFromDate."'";
			unset($where['attendance_from_month']);
		}
		if(isset($where['attendance_to_month']) && (!empty($where['attendance_to_month'])) ){
			$attendanceToDate = dbDate($where['attendance_to_month']);
			$where['custom_function'][] =  "date(ea.dt_date) <=  '".$attendanceToDate."'";
			unset($where['attendance_to_month']);
		}
		
		if(isset($where['status']) && (!empty($where['status'])) ){
			$attendanceStatus = $where['status'];
			$where['e_status'] = $attendanceStatus;
			unset($where['status']);
		}
		
		
		
		
		if(isset($where['manually_change_status']) && (!empty($where['manually_change_status'])) ){
			$manuallyChangeStatus = $where['manually_change_status'];
			switch($manuallyChangeStatus){
				case config('constants.SELECTION_YES'):
					$where['t_manually_change'] = 1;
					break;
				case config('constants.SELECTION_NO'):
					$where['t_manually_change'] = 0;
					break;
			}
			unset($where['manually_change_status']);
		}
		
		$customArrivalDepartureStatus = "";
		$customArrivalStatus = [];
		if(isset($where['arrival_status']) && (!empty($where['arrival_status'])) ){
			$arrivalStatus = $where['arrival_status'];
			switch($arrivalStatus){
				case config('constants.EARLY_STATUS'):
					$customArrivalStatus[] = "( arrival_time_diff < 0 )";
					//$where['t_manually_change'] = 1;
					break;
				case config('constants.ON_TIME_STATUS'):
					$customArrivalStatus[] = "( arrival_time_diff >= 0  and arrival_time_diff <= '".config('constants.ON_TIME_BUFFER_DURATION_INTO_SEC')."'  )";
					//$where['t_manually_change'] = 0;
					break;
				case config('constants.LATE_STATUS'):
					$customArrivalStatus[] = "( arrival_time_diff > '".config('constants.ON_TIME_BUFFER_DURATION_INTO_SEC')."'  )";
					//$where['t_manually_change'] = 0;
					break;
			}
			unset($where['arrival_status']);
		}
		
		$customDepartureStatus = [];
		if(isset($where['departure_status']) && (!empty($where['departure_status'])) ){
			$departureStatus = $where['departure_status'];
			switch($departureStatus){
				case config('constants.EARLY_STATUS'):
					$customDepartureStatus[] = "( departure_time_diff < 0 ) ";
					//$where['t_manually_change'] = 1;
					break;
				case config('constants.ON_TIME_STATUS'):
					$customDepartureStatus[] = "( departure_time_diff = 0 )";
					//$where['t_manually_change'] = 0;
					break;
				case config('constants.LATE_STATUS'):
					//$where['t_manually_change'] = 0;
					$customDepartureStatus[] = "( departure_time_diff > 0  )";
					break;
			}
			unset($where['departure_status']);
		}
		$breakTimeStatus = [];
		if(isset($where['break_time']) && (!empty($where['break_time'])) ){
			$breakTime = $where['break_time'];
			switch($breakTime){
				case config('constants.GREATER_THAN_MIN'):
					$breakTimeStatus[] = "( break_time_into_sec > '".config('constants.BREAK_TIME_FILTER_VALUE')."' )";
					//$where['t_manually_change'] = 1;
					break;
				case config('constants.LESS_THAN_MIN'):
					$breakTimeStatus[] = "( break_time_into_sec <= '".config('constants.BREAK_TIME_FILTER_VALUE')."' )";
					//$where['t_manually_change'] = 0;
					break;
			}
			unset($where['break_time']);
		}
		
		
		//echo "<pre>";print_r($customArrivalStatus);
		//echo "<pre>";print_r($customDepartureStatus);
		//echo "<pre>";print_r($breakTimeStatus);
		
		$havingMerge = array_merge($customArrivalStatus , $customDepartureStatus ,  $breakTimeStatus  );
		if(!empty($havingMerge)){
			//echo "<pre>";print_r($havingMerge);die;
			$where['having'] = "( ". implode(" and " , $havingMerge ) . " )";
			//var_dump($where['having']);die;
		}
		//echo "<pre>";print_r($havingMerge);die;
		/* if( (!empty($customArrivalStatus)) && (!empty($customDepartureStatus)) ){
			$where['having'] = "(".$customArrivalStatus." and " .$customDepartureStatus . " ) ";
		} else {
			if(!empty($customArrivalStatus)){
				$where['having'] = $customArrivalStatus;
			}
			if(!empty($customDepartureStatus)){
				$where['having'] = $customDepartureStatus;
			}
		} */
		
		
		if(isset($where['team']) && (!empty($where['team'])) ){
			$teamId = $where['team'];
			$where['em.i_team_id'] = $teamId;
			unset($where['team']);
		}
		
		if(session()->get('role') == config('constants.ROLE_USER')){
			if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
				if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
					$employeeId = $where['employee_id'];
					if( is_array($employeeId) ){
						$whereInData[] = [ 'i_id' , $employeeId  ];
					} else {
						$where['em.i_id'] = $employeeId;
					}
					unset($where['employee_id']);
				}
				unset($where['show_all']);
			} else {
				/* $employeeId = session()->get('user_employee_id');
				$where['custom_function'][] = "(em.i_id = '".$employeeId."' or  em.i_leader_id = '".$employeeId."')"; */

				$allChildEmployeeIds = $this->childEmployeeIds();
				if(!empty($allChildEmployeeIds)){
					$whereInData[] = ['em.i_id' , $allChildEmployeeIds];
				}
				
				if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
					$employeeId = $where['employee_id'];
					if( is_array($employeeId) ){
						$whereInData[] = [ 'i_id' , $employeeId  ];
					} else {
						$where['em.i_id'] = $employeeId;
					}
					unset($where['employee_id']);
				}	
			}
			
		} else {
			if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
				$employeeId = $where['employee_id'];
				if( is_array($employeeId) ){
					$whereInData[] = [ 'i_id' , $employeeId  ];
				} else {
					$where['em.i_id'] = $employeeId;
				}
				unset($where['employee_id']);
			}
		}
		
		$whereData = (!empty($where) ? array_merge( $defaultWhere , $where) : $defaultWhere );
		
		if(!empty($whereInData)){
			$additionalData['whereIn'] = $whereInData;
		}
		//echo "<pre>";print_r($whereData);
		//echo "<pre>";print_r($additionalData);
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordWithJoinById( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectJoinData( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
		}
		//echo $this->last_query();die; 
		return $data;
		
	}
	
	
	public function getRecordDetails( $where = [] , $likeData = [] ){
		$query = MyAttendanceModel::with(['employeeInfo' , 'employeeInfo.teamInfo' ]);
	
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			$query->where('i_id','=',$masterRecordId);
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
		
		if(session()->get('role') == config('constants.ROLE_USER')){
			if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
				unset($where['show_all']);
				if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
					$employeeId = $where['employee_id'];
					$query->whereHas('employeeInfo' , function($query) use($employeeId) {
						if( is_array($employeeId) ){
							$query->whereIn('i_id',$employeeId);
						} else {
							$query->where('i_id',$employeeId);
				
						}
					});
				}
			} else {
				if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
					$employeeId = $where['employee_id'];
					$query->whereHas('employeeInfo' , function($query) use($employeeId) {
						if( is_array($employeeId) ){
							$query->whereIn('i_id',$employeeId);
						} else {
							$query->where('i_id',$employeeId);
				
						}
					});
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
			}
			
		} else {
			if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
				$employeeId = $where['employee_id'];
				$query->whereHas('employeeInfo' , function($query) use($employeeId) {
					if( is_array($employeeId) ){
						$query->whereIn('i_id',$employeeId);
					} else {
						$query->where('i_id',$employeeId);
						
					}
				});
			}
		}
		
		if(isset($where['attendance_from_month']) && (!empty($where['attendance_from_month'])) ){
			$attendanceFromDate = dbDate($where['attendance_from_month']);
			$query->where('dt_date','>=',$attendanceFromDate);
		}
		if(isset($where['attendance_to_month']) && (!empty($where['attendance_to_month'])) ){
			$attendanceToDate = dbDate($where['attendance_to_month']);
			$query->where('dt_date','<=',$attendanceToDate);
			
		}
		if(isset($where['status']) && (!empty($where['status'])) ){
			$attendanceStatus = $where['status'];
			$query->where('e_status',$attendanceStatus);
		}
		if(isset($where['attendance_date']) && (!empty($where['attendance_date'])) ){
			$attendanceDate = $where['attendance_date'];
			if(is_array($attendanceDate)){
				$query->whereIn('dt_date',$attendanceDate);
			}
		}
		
		if(isset($where['team']) && (!empty($where['team'])) ){
			$teamId = $where['team'];
			$query->whereHas('employeeInfo' , function($query) use($teamId) {
				$query->where('i_team_id',$teamId);
			});
		}
		
		if( isset($where ['order_by']) ){
			if(!empty($where ['order_by'])){
				foreach($where ['order_by'] as  $key => $value){
					$query->orderBy($key, (!empty($value) ? $value : 'DESC' ) );
				}
			}
		} else {
			$query->orderBy('dt_date', "asc" ) ;
		}
		
		
		if( isset($where['offset']) ){
			$query->skip($where ['offset']);
		}
		 
		if( isset($where['limit']) ){
			$query->take($where['limit']);
		}
		
		if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
			$data = $query->first();
		} else {
			
			if( isset( $where ['editAttendanceSreen'] ) && ( $where ['editAttendanceSreen'] != false ) ){
				$records  = $query->get();
				if(!empty($records)){
					$data = $records->sortBy([
							['dt_date','asc'],
							['employeeInfo.v_employee_full_name','asc'],
					]);
				} else {
					$data = [];
				}
			} else {
				$data = $query->get();
			}
		}
		return $data;
	
	}
}
