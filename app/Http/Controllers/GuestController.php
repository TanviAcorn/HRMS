<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Helpers\Twt\Wild_tiger;
use App\BaseModel;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Rules\UniqueEmail;
use App\Rules\UniqueSalesEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Rules\CheckLastPassword;
use App\Models\SuspendHistory;
use App\MyLeaveModel;
use App\EmployeeModel;
use App\Models\EmployeeDesignationHistory;
use App\HolidayMasterModel;
use App\MyAttendanceModel;
use App\Models\AttendanceSummaryModel;
use App\Models\Salary;
use App\Models\ApiAttendanceInfo;

class GuestController extends Controller
{
    //
    public $mailTemplateFolderPath = '';
    public $defaultPage;
    public $perPageRecord;
	public function __construct(){
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->perPageRecord = config('constants.PER_PAGE');
    	$this->BaseModel = new BaseModel();
    	$this->mailTemplateFolderPath = config('constants.ADMIN_FOLDER') . config('constants.MAIL_TEMPLATE_FOLDER_PATH');
    	
    }
	
	public function customLogEntry(){
		//Log::info(print_r($request->all(),true));
	}
	
	public function expectionLogEntry($postData , $e){
		//Log::info(print_r($postData,true));
		//Log::info(print_r($e->getMessage(),true));
	}
	
	public function checkUniqueUserEmail(Request $request) {
    	
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
    	
    	$validator = Validator::make ( $request->all (), [
    			'email' => [ 'required' ,new UniqueEmail($recordId) ]  ,
    	], [
    			'email.required' => __ ( 'messages.required-login-email' ),
    	] );
    
    	$result = [];
    	$result['status_code'] = 1;
    	$result['message'] = trans('messages.success');
    	if ($validator->fails ()) {
    		$result['status_code'] = 101;
    		$result['message'] = trans('messages.error');
    	}
    	echo json_encode($result);die;
    }
    public function customErrorPage() {
    	$data['pageTitle'] = trans ( 'messages.page-not-found');
    	return view ( 'errors/404' )->with ( $data );
    }
    
    public function accessDenidePage() {
    	$data['pageTitle'] = trans ( 'messages.access-denied');
    	return view (  config('constants.ADMIN_FOLDER') . 'access-denied' )->with ( $data );
    }
    
    public function checkStrongPassword(Request $request) {
    
    	$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
    
    	$validator = Validator::make ( $request->all (), [
    			'new_password' => [ 'required' , new CheckLastPassword($request) ]  ,
    	], [
    			'new_password.required' => __ ( 'messages.error-strong-password' ),
    	] );
    
    	$result = [];
    	$result['status_code'] = 1;
    	$result['message'] = trans('messages.success');
    	if ($validator->fails ()) {
    		$result['status_code'] = 101;
    		$result['message'] = $validator->errors()->first();
    	}
    	echo json_encode($result);die;
    }
    
    public function employeeLeaveInfo( $where = [] ){
    	
    	$startDate = ( isset($where['startDate']) ? $where['startDate'] : attendanceStartDate(date('m'), date('Y')) );
    	$endDate = ( isset($where['endDate']) ? $where['endDate'] : attendanceEndDate(date('m'), date('Y')) );
    	$employeeId = ( isset($where['employeeId']) ? $where['employeeId'] : []);
    	$monthAllDates = ( isset($where['monthAllDates']) ? $where['monthAllDates'] : date('Y-m-d') );
    	//echo "<pre>";print_r($monthAllDates);
    	$employeeAppliedLeaveDetails = [];
    	$employeeAppliedLeaveQuery = MyLeaveModel::where('t_is_deleted' , 0 )->whereIn( 'e_status' , [ config('constants.APPROVED_STATUS') , config('constants.PENDING_STATUS')  ] )->whereIn(  'i_leave_type_id' ,   [ config('constants.PAID_LEAVE_TYPE_ID')  , config('constants.EARNED_LEAVE_TYPE_ID')  , config('constants.CARRY_FORWARD_LEAVE_TYPE_ID') , config('constants.UNPAID_LEAVE_TYPE_ID')  ] );
    	if( (!empty($employeeId)) ) {
    		if(is_array($employeeId)){
    			$employeeAppliedLeaveQuery->whereIn('i_employee_id',$employeeId);
    		} else {
    			$employeeAppliedLeaveQuery->where('i_employee_id',$employeeId);
    		}
    		
    	}
    	$employeeAppliedLeaveQuery->whereRaw("(  dt_leave_from_date >= '".$startDate."'  or dt_leave_to_date >= '".$startDate."'  )");
    	$employeeAppliedLeaveQuery->whereRaw("(  dt_leave_from_date <= '".$endDate."'  or dt_leave_to_date <= '".$endDate."'  )");
    	$employeeAppliedLeaveDetails =  $employeeAppliedLeaveQuery->get();
    	
    	//echo "<pre>";print_r($employeeAppliedLeaveDetails);
    	
    	$allAppliedLeaveDates = [];
    	$unPaidLeaveDates = [];
    	$unPaidHalfLeaveDates = [];
    	$paidLeaveDates = [];
    	$paidHalfLeaveDates = [];
    	$employeeWiseAbsentLeaveCount =  [];
    	$employeeWisePresentLeaveCount = [];
    	//echo "<pre>";print_r(array_column(objectToArray($employeeAppliedLeaveDetails), 'i_id'));
    	if(!empty($employeeAppliedLeaveDetails)){
    		foreach($employeeAppliedLeaveDetails as $employeeAppliedLeaveDetail){
    			$leaveCount = 0;
    			if( strtotime($employeeAppliedLeaveDetail->dt_leave_from_date) !=  strtotime($employeeAppliedLeaveDetail->dt_leave_to_date) ){
    				$leaveDateRanges = getDatesFromRange($employeeAppliedLeaveDetail->dt_leave_from_date,$employeeAppliedLeaveDetail->dt_leave_to_date);
    				//echo "<pre>";print_r($leaveDateRanges);die;
    				if(!empty($leaveDateRanges)){
    					foreach($leaveDateRanges as $leaveDateRange){
    						if( in_array($leaveDateRange,$monthAllDates) ){
    							$allAppliedLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $leaveDateRange;
								if( ( strtotime($employeeAppliedLeaveDetail->dt_leave_from_date) == strtotime( $leaveDateRange ) ) ||  ( strtotime($employeeAppliedLeaveDetail->dt_leave_to_date) ==  strtotime( $leaveDateRange ) ) ){
    								//echo "daadassa = ".$leaveDateRange;echo "<br><br>";
									if($employeeAppliedLeaveDetail->e_from_duration == config('constants.FIRST_HALF_LEAVE')){
    									$leaveCount = $leaveCount + config('constants.FULL_LEAVE_VALUE');
    									if( in_array( $employeeAppliedLeaveDetail->i_leave_type_id , [ config('constants.UNPAID_LEAVE_TYPE_ID') ] ) ){
    										$unPaidLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $leaveDateRange;
    									} else {
    										$paidLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $leaveDateRange;
    									}
    								} else {
    									$leaveCount = $leaveCount + config('constants.HALF_LEAVE_VALUE');
    									if( in_array( $employeeAppliedLeaveDetail->i_leave_type_id , [ config('constants.UNPAID_LEAVE_TYPE_ID') ] ) ){
    										$unPaidHalfLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $leaveDateRange;
    									} else {
    										$paidHalfLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $leaveDateRange;
    									}
    								}
    								
    								if($employeeAppliedLeaveDetail->e_to_duration == config('constants.FIRST_HALF_LEAVE')){
    									$leaveCount = $leaveCount + config('constants.HALF_LEAVE_VALUE');
    									if( in_array( $employeeAppliedLeaveDetail->i_leave_type_id , [ config('constants.UNPAID_LEAVE_TYPE_ID') ] ) ){
    										$unPaidHalfLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $leaveDateRange;
    									} else {
    										$paidHalfLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $leaveDateRange;
    									}
    								} else {
    									$leaveCount = $leaveCount + config('constants.FULL_LEAVE_VALUE');
    									if( in_array( $employeeAppliedLeaveDetail->i_leave_type_id , [ config('constants.UNPAID_LEAVE_TYPE_ID') ] ) ){
    										$unPaidLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $leaveDateRange;
    									} else {
    										$paidLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $leaveDateRange;
    									}
    								}
    							
    							} else {
    								//echo "ffee = ".$leaveDateRange;echo "<br><br>";
    								$leaveCount = $leaveCount + config('constants.FULL_LEAVE_VALUE');
    								if( in_array( $employeeAppliedLeaveDetail->i_leave_type_id , [ config('constants.UNPAID_LEAVE_TYPE_ID') ] ) ){
    									$unPaidLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $leaveDateRange;
    								} else {
    									$paidLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $leaveDateRange;
    								}
    							}
    						}
    	
    					}
    				}
    			} else {
    				if( in_array($employeeAppliedLeaveDetail->dt_leave_from_date,$monthAllDates) ){
    					//var_dump($employeeAppliedLeaveDetail->dt_leave_from_date);echo "<br><br>";
    					//echo "<pre>";print_r($employeeAppliedLeaveDetail);
    					$allAppliedLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $employeeAppliedLeaveDetail->dt_leave_from_date;
    					if( in_array( $employeeAppliedLeaveDetail->e_duration , [ config('constants.FIRST_HALF_LEAVE') , config('constants.SECOND_HALF_LEAVE') ]  ) ){
    						$leaveCount = $leaveCount + config('constants.HALF_LEAVE_VALUE');
    						if( in_array( $employeeAppliedLeaveDetail->i_leave_type_id , [ config('constants.UNPAID_LEAVE_TYPE_ID') ] ) ){
    							$unPaidHalfLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $employeeAppliedLeaveDetail->dt_leave_from_date;
    						} else {
    							$paidHalfLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $employeeAppliedLeaveDetail->dt_leave_from_date;
    						}
    						//echo "<pre>unPaidHalfLeaveDates";print_r($unPaidHalfLeaveDates);
    					} else {
    						$leaveCount = $leaveCount + config('constants.FULL_LEAVE_VALUE');
    						if( in_array( $employeeAppliedLeaveDetail->i_leave_type_id , [ config('constants.UNPAID_LEAVE_TYPE_ID') ] ) ){
    							$unPaidLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $employeeAppliedLeaveDetail->dt_leave_from_date;
    						} else {
    							$paidLeaveDates[$employeeAppliedLeaveDetail->i_employee_id][] = $employeeAppliedLeaveDetail->dt_leave_from_date;
    						}
    					}
    				}
    					
    			}
    				
    			if( in_array( $employeeAppliedLeaveDetail->i_leave_type_id , [ config('constants.UNPAID_LEAVE_TYPE_ID') ] ) ){
    				if(isset($employeeWiseAbsentLeaveCount[$employeeAppliedLeaveDetail->i_employee_id])){
    					$employeeWiseAbsentLeaveCount[$employeeAppliedLeaveDetail->i_employee_id] += $leaveCount;
    				} else {
    					$employeeWiseAbsentLeaveCount[$employeeAppliedLeaveDetail->i_employee_id] = $leaveCount;
    				}
    	
    			} else {
    	
    				if(isset($employeeWisePresentLeaveCount[$employeeAppliedLeaveDetail->i_employee_id])){
    					$employeeWisePresentLeaveCount[$employeeAppliedLeaveDetail->i_employee_id] += $leaveCount;
    				} else {
    					$employeeWisePresentLeaveCount[$employeeAppliedLeaveDetail->i_employee_id] = $leaveCount;
    				}
    			}
    				
    				
    		}
    	}
    	$result = [];
    	$result['paidLeaveDates'] = $paidLeaveDates;
    	$result['paidHalfLeaveDates'] = $paidHalfLeaveDates;
    	$result['unPaidLeaveDates'] = $unPaidLeaveDates;
    	$result['unPaidHalfLeaveDates'] = $unPaidHalfLeaveDates;
    	$result['paidLeaveCount'] = $employeeWisePresentLeaveCount;
    	$result['unPaidLeaveCount'] = $employeeWiseAbsentLeaveCount;
    	$result['allAppliedLeaveDates'] = $allAppliedLeaveDates;
    	//echo "<pre>";print_r($result);
    	return $result;
    	
    	
    }
    
    public function getAllSuspendDateWiseRecords( $where = [] , $startDate = null , $endDate = null ){
    
    	$monthAllDates = ( isset($where['monthAllDates']) ? $where['monthAllDates'] : date('Y-m-d') );
    	
    	$getSuspendRecordQuery = SuspendHistory::where('t_is_deleted' ,  0);
    	if( isset($where['startDate']) && (!empty($where['startDate']))){
    		$getSuspendRecordQuery->whereRaw("(  dt_start_date >= '".dbDate($where['startDate'])."'  or dt_end_date >= '".dbDate($where['startDate'])."'  )");
    	}
    
    	if( isset($where['endDate']) && (!empty($where['endDate']))){
    		$getSuspendRecordQuery->whereRaw("(  dt_start_date <= '".dbDate($where['endDate'])."'  or dt_end_date <= '".dbDate($where['endDate'])."'  )");
    	}
    	
    	if( isset($where['employeeId']) && (!empty($where['employeeId']))){
    		if( is_array( $where['employeeId'] ) ) {
    			$getSuspendRecordQuery->whereIn('i_employee_id' , $where['employeeId']  );
    		} else {
    			$getSuspendRecordQuery->where('i_employee_id' , $where['employeeId']  );
    		}
    		
    	}
    	
    	$getSuspendRecordDetails =  $getSuspendRecordQuery->get();
    
    	$employeeWiseSuspendRecorDetails = [];
    
    	if(!empty($getSuspendRecordDetails)){
    		foreach($getSuspendRecordDetails as $getSuspendRecordDetail){
    			if( strtotime($getSuspendRecordDetail->dt_start_date) !=  strtotime($getSuspendRecordDetail->dt_end_date) ){
    				$suspendDateRanges = getDatesFromRange($getSuspendRecordDetail->dt_start_date,$getSuspendRecordDetail->dt_end_date);
    				if(!empty($suspendDateRanges)){
    					foreach($suspendDateRanges as $suspendDateRange){
    						if(in_array( $suspendDateRange , $monthAllDates  ) ) {
    							$employeeWiseSuspendRecorDetails[$getSuspendRecordDetail->i_employee_id][] = $suspendDateRange;
    						} else {
    							//$employeeWiseSuspendRecorDetails[$getSuspendRecordDetail->i_employee_id][] = $suspendDateRange;
    						}
    						
    					}
    				}
    			} else {
    				if(in_array( $getSuspendRecordDetail->dt_start_date , $monthAllDates  ) ) {
    					$employeeWiseSuspendRecorDetails[$getSuspendRecordDetail->i_employee_id][] = $getSuspendRecordDetail->dt_start_date;
    				} else {
    					//$employeeWiseSuspendRecorDetails[$getSuspendRecordDetail->i_employee_id][] = $getSuspendRecordDetail->dt_start_date;
    				}
    			}
    		}
    	}
    	return $employeeWiseSuspendRecorDetails;
    
    }
    
    public function getAllHoliDayDetails( $monthAllDates = [] ){
    	
    	$this->holidayModule = new HolidayMasterModel();
    	$holidayWhere = [];
    	if(!empty($monthAllDates)){
    		$holidayWhere['holiday_date'] = $monthAllDates;
    	}
    	$getHolidayDetails = $this->holidayModule->getRecordDetails($holidayWhere);
    	$monthHolidayDates =  ((!empty($getHolidayDetails)) ? array_column(objectToArray($getHolidayDetails), 'dt_holiday_date') : []);
    	return $monthHolidayDates;
    }
    
    public function getEmployeeMonthlyWeekOff($data = [] ){
    	 
    	$employeeId = isset($data['employeeId']) ? $data['employeeId'] :  session()->get('user_employee_id');
    	$selectedMonth = isset($data['month']) ? $data['month'] :  date('Y-m-d');
    	 
    	$startDate = date("Y-m-01", strtotime($selectedMonth));
    	$endDate = date("Y-m-t", strtotime($selectedMonth));
    	 
    	/* echo "month = ".$selectedMonth;echo "<br><br>";
    	 echo "month = ".attendanceStartDate(date('m' , strtotime($selectedMonth) ), date('Y' , strtotime($selectedMonth)) );echo "<br><br>";
    	 echo "start date = ".$startDate;echo "<br><br>";
    	echo "start date = ".$startDate;echo "<br><br>"; */
    	 
    	if( isset($data['attendanceView']) && ( $data['attendanceView'] != false ) ){
    		$startDate = attendanceStartDate(date('m' , strtotime($selectedMonth) ), date('Y' , strtotime($selectedMonth)) );
    		$endDate = attendanceEndDate(date('m' , strtotime($selectedMonth) ), date('Y' , strtotime($selectedMonth)) );
    		$startDate = date('Y-m-01' , strtotime($startDate));
    		$endDate = date('Y-m-t' , strtotime($endDate));
    	}
    	 
    	 
    	$getEmployeeWhere = [];
    	$getEmployeeWhere['i_id'] = $employeeId;
    	$getEmployeeInfo = EmployeeModel::with(['shiftInfo' , 'shiftInfo.shiftTimingInfo' , 'weekOffInfo' ])->where($getEmployeeWhere)->first();
    	
    	$employeeAllWeekOffWhere = [];
    	$employeeAllWeekOffWhere['t_is_deleted'] = 0;
    	$employeeAllWeekOffWhere['i_employee_id'] = $employeeId;
    	$employeeAllWeekOffWhere['e_record_type'] = config('constants.WEEK_OFF_RECORD_TYPE');
    	$employeeAllWeekOffHistoryDetails = EmployeeDesignationHistory::where($employeeAllWeekOffWhere)->get();
    	$allWeekOffHistoryDates = (!empty($employeeAllWeekOffHistoryDetails) ? array_column(objectToArray($employeeAllWeekOffHistoryDetails), 'dt_start_date') : [] );
    	
    	$employeeJoiningDate = ( isset($getEmployeeInfo->dt_joining_date) ? $getEmployeeInfo->dt_joining_date : null );
    	$shiftLastUpdateDate = ( isset($getEmployeeInfo->dt_last_update_shift) ? $getEmployeeInfo->dt_last_update_shift : null );
    	$weekOffLastUpdateDate = ( isset($getEmployeeInfo->dt_last_update_week_off) ? $getEmployeeInfo->dt_last_update_week_off : null );
    	$weekOffLastUpdateDate = ( isset($getEmployeeInfo->dt_last_update_week_off) ? $getEmployeeInfo->dt_last_update_week_off : null );
    	$intialWeekOffDate = ( isset($getEmployeeInfo->dt_week_off_effective_date) ? $getEmployeeInfo->dt_week_off_effective_date : null );
    	
    	
    	if( isset($data['calendarView']) && ( $data['calendarView'] != false ) && (!empty($intialWeekOffDate)) ){
    		$startDate = $intialWeekOffDate;
    	}
    	
    	if( isset($data['attendanceView']) && ( $data['attendanceView'] != false ) && (!empty($intialWeekOffDate)) ){
    		$startDate = $intialWeekOffDate;
    	}
    	
    	$currentShiftInfo = ( isset($getEmployeeInfo->shiftInfo->shiftTimingInfo[0]) ? $getEmployeeInfo->shiftInfo->shiftTimingInfo[0] : [] );
    	$currentWeekOffInfo = ( isset($getEmployeeInfo->weekOffInfo->weeklyOffDetail) ? $getEmployeeInfo->weekOffInfo->weeklyOffDetail : [] );
    	 
    	$allWeekDetails = weekDayDetails();
    	 
    	$allWeekAlternateCount = [];
    	foreach($allWeekDetails as $allWeekKey  => $allWeekDetail){
    		$allWeekAlternateCount[$allWeekKey] = 0;
    	}
    	 
    	$allDates = getDatesFromRange( $startDate , $endDate );
    	//var_dump($weekOffLastUpdateDate);echo "<br><br>";
    	//echo "<pre>";print_r($allDates);echo "<br><br>";
    	///echo "last update date = ".$weekOffLastUpdateDate;echo "<br><br>";
    	/* echo "last update date = ".$weekOffLastUpdateDate;
    	 echo "<pre>";print_r($allDates); */
    	//echo "<pre>";print_r($allDates);
    	//var_dump($weekOffLastUpdateDate);
    	$employeeWeekOffDates = [];
    	if(!empty($allDates)){
    		foreach($allDates as $allDate){
    			
    			
    			
    			$beforeFirstWeekOffDate = true;
    			
    			$weekDay = strtolower( date('l' , strtotime($allDate) ) );
    			$alternateColumnName = 'v_'.$weekDay.'_alternate_off';
    			$allColumnName = 'v_'.$weekDay.'_all_off';
    			
    			if(in_array( $allDate , $allWeekOffHistoryDates ) != false ){
	    			foreach($allWeekDetails as $allWeekKey  => $allWeekDetail){
			    		$allWeekAlternateCount[$allWeekKey] = 0;
			    	}
    			}
    			 
    			//echo "week day = ".$weekDay;echo "<br><br>";
    			//echo "alternate Column = ".$alternateColumnName;echo "<br><br>";
    			//echo "all Column = ".$allColumnName;echo "<br><br>";
    			//echo "week off start date = ".$weekOffLastUpdateDate;echo "<br><br>";
    			//echo "<pre>";print_r($currentWeekOffInfo);
    			if( strtotime($weekOffLastUpdateDate) <= strtotime($allDate) ){
    				//echo "ififif";echo "<br><br>";
    				$weekOffStartDate = $weekOffLastUpdateDate;
    				$dayWeekOffInfo = $currentWeekOffInfo;
    				
    			} else {
    				/* 
    				if( strtotime($employeeJoiningDate) < strtotime($allDate) ){
    					echo "aaa = ".$allDate;echo "<br><br>";
    				} else {
    					echo "bbb = ".$allDate;echo "<br><br>";
    				} */
    				
    				
    				//echo "<br><br>";
    				
    				//echo "elseelseelse";echo "<br><br>";
    				$weekOffStartDate = null;
    				$dayWeekOffInfo = [];
    				$getParticularDayWeekOffWhere = "i_employee_id = '".$employeeId."' and t_is_deleted != 1 and '".$allDate."' between dt_start_date and dt_end_date";
    				//echo $getParticularDayWeekOffWhere;echo  "<br><br>";
    				$getParticularDayWeekOffDetails = EmployeeDesignationHistory::with( [ 'weeklyOffInfo' , 'weeklyOffInfo.weeklyOffDetail'  ] )->whereRaw($getParticularDayWeekOffWhere)->first();
    
    				
    				if(!empty($getParticularDayWeekOffDetails)){
    					$weekOffStartDate = $getParticularDayWeekOffDetails->dt_start_date;
    					$dayWeekOffInfo  = ( isset($getParticularDayWeekOffDetails->weeklyOffInfo->weeklyOffDetail) ? $getParticularDayWeekOffDetails->weeklyOffInfo->weeklyOffDetail : [] );
    				} else {
    					//var_dump(( strtotime($allDate) >= strtotime($employeeJoiningDate) ));
    					//var_dump(( strtotime($allDate) < strtotime($weekOffLastUpdateDate) ));
    					//echo "ccccc = ".$allDate;echo "<br><br>";
    					if( ( strtotime($allDate) >= strtotime($employeeJoiningDate) ) && ( strtotime($allDate) < strtotime($weekOffLastUpdateDate)) ){
    						//echo "ddddddd = ".$allDate;echo "<br><br>";die;
    						$startWeekOffDetails = EmployeeDesignationHistory::with( [ 'weeklyOffInfo' , 'weeklyOffInfo.weeklyOffDetail'  ] )->where('t_is_deleted' , 0 )->where(  'e_record_type' ,  config('constants.WEEK_OFF_RECORD_TYPE') )->where('i_employee_id' , $employeeId)->orderBy('i_id' , 'asc')->first();
    						if(!empty($startWeekOffDetails)){
    							$beforeFirstWeekOffDate = false;
    							$weekOffStartDate = $employeeJoiningDate;
    							$dayWeekOffInfo  = ( isset($startWeekOffDetails->weeklyOffInfo->weeklyOffDetail) ? $startWeekOffDetails->weeklyOffInfo->weeklyOffDetail : [] );
    						} else {
    							$weekOffStartDate = $weekOffLastUpdateDate;
    							$dayWeekOffInfo = $currentWeekOffInfo;
    						}
    					} else {
    						$weekOffStartDate = $weekOffLastUpdateDate;
    						$dayWeekOffInfo = $currentWeekOffInfo;
    					} 
    					
    					
    					
    					
    				}
    			}
    			//echo "<pre>";print_r($weekOffStartDate);
    			//echo "<pre>";print_r($dayWeekOffInfo); 
    			if( (!empty($weekOffStartDate)) && (!empty($dayWeekOffInfo)) ){
    
    				
    				//echo "<pre>";print_r($dayWeekOffInfo);
    				//echo "weekOffStartDate = ".$weekOffStartDate;echo "<br><br>";
    				//echo "dayWeekOffInfo = ".$dayWeekOffInfo;echo "<br><br>";
    
    				//echo "<pre>";print_r($dayWeekOffInfo);
    				if( isset($dayWeekOffInfo->$alternateColumnName) && ( $dayWeekOffInfo->$alternateColumnName == config('constants.SELECTION_YES')) ){
    					if( (!empty($weekOffStartDate)) && ( strtotime($weekOffStartDate) ) <= strtotime($allDate) ){
    						if( $beforeFirstWeekOffDate != false ){
    							$allWeekAlternateCount[$weekDay] = ( $allWeekAlternateCount[$weekDay] + 1 );
    						}
    						
    					}
    				}
    
    				if( isset($dayWeekOffInfo->$allColumnName) && ( $dayWeekOffInfo->$allColumnName == config('constants.SELECTION_YES')) ){
    					if( (!empty($weekOffStartDate)) && ( strtotime($weekOffStartDate) ) <= strtotime($allDate) ){
    						$employeeWeekOffDates[] = $allDate;
    					}
    				}
    					
    				if( isset($dayWeekOffInfo->$alternateColumnName) && ( $dayWeekOffInfo->$alternateColumnName == config('constants.SELECTION_YES')) ){
    					//echo "week off start date = ".$weekOffStartDate;echo "<br><br>";
    					//echo "all date = ".$allDate;echo "<br><br>";
    					//echo "<pre>";print_r($allWeekAlternateCount);
    					if( $allWeekAlternateCount[$weekDay] % 2 != 0 ){
    						if( (!empty($weekOffStartDate)) && ( strtotime($weekOffStartDate) ) <= strtotime($allDate) ){
    							if( $beforeFirstWeekOffDate != false ){
    								$employeeWeekOffDates[] = $allDate;
    							}
    						}
    							
    					}
    					 
    				}
    					
    			}
    		}
    	}
    	//echo "<pre>employeeWeekOffDates";print_r($employeeWeekOffDates);
    	$response = [];
    	$response['weekOffDates'] = $employeeWeekOffDates;
    	return $response;
    }
    
    public function commonAttendanceSummary($attendanceDate , $employeeIds = []){
    	//var_dump($attendanceDate);
    	$month = date('m' , strtotime($attendanceDate));
    	$year = date('Y' , strtotime($attendanceDate));
    	
    	$getLastMonthStartDate = attendanceStartDate($month, $year);
    	$getLastMonthEndDate = attendanceEndDate($month, $year);
    	//var_dump($getLastMonthStartDate);
    	//var_dump($getLastMonthEndDate);die;
    	//echo "start date = ".$getLastMonthStartDate;echo "<br><br>";
    	//echo "end date = ".$getLastMonthEndDate;echo "<br><br>";
    		
    	$getAllEmployeeDetails = MyAttendanceModel::where('dt_date' , '>=' , $getLastMonthStartDate )->where('dt_date' , '<=' , $getLastMonthEndDate )->get();
    	if(!empty($employeeIds)) {
    		$getAllEmployeeDetails = MyAttendanceModel::where('dt_date' , '>=' , $getLastMonthStartDate )->where('dt_date' , '<=' , $getLastMonthEndDate )->whereIn('i_employee_id' , $employeeIds)->get();
    	}
    	$employeeDateWiseDetails = [];
    	if(!empty($getAllEmployeeDetails)){
    		foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
    			$employeeDateWiseDetails[$getAllEmployeeDetail->i_employee_id][] = $getAllEmployeeDetail;
    		}
    	}
    		
    	//get salary all dates
    	$monthAllDates = Wild_tiger::getAllDateOfSalaryMonth($month , $year );
    		
    	$employeeWisePresentLeaveCount = [];
    	$employeeWiseAbsentLeaveCount = [];
    		
    	//get salary month all leaves
    	$employeeLeaveWhere = [];
    	$employeeLeaveWhere['startDate'] = $getLastMonthStartDate;
    	$employeeLeaveWhere['endDate'] = $getLastMonthEndDate;
    	$employeeLeaveWhere['monthAllDates'] = $monthAllDates;
    	if(!empty($employeeIds)) {
    		$employeeLeaveWhere['employeeId'] = $employeeIds;
    	}
    	$employeeLeaveDetails = $this->employeeLeaveInfo($employeeLeaveWhere);
    	
    	//echo "<pre>";print_r($employeeLeaveDetails);
    	
    	$paidLeaveDates = ( isset($employeeLeaveDetails['paidLeaveDates']) ? $employeeLeaveDetails['paidLeaveDates'] : [] );
    	$paidHalfLeaveDates = ( isset($employeeLeaveDetails['paidHalfLeaveDates']) ? $employeeLeaveDetails['paidHalfLeaveDates'] : [] );
    	$unPaidLeaveDates = ( isset($employeeLeaveDetails['unPaidLeaveDates']) ? $employeeLeaveDetails['unPaidLeaveDates'] : [] );
    	$unPaidHalfLeaveDates = ( isset($employeeLeaveDetails['unPaidHalfLeaveDates']) ? $employeeLeaveDetails['unPaidHalfLeaveDates'] : [] );
    	$paidLeaveCount = ( isset($employeeLeaveDetails['paidLeaveCount']) ? $employeeLeaveDetails['paidLeaveCount'] : [] );
    	$unPaidLeaveCount = ( isset($employeeLeaveDetails['unPaidLeaveCount']) ? $employeeLeaveDetails['unPaidLeaveCount'] : [] );
    	$allAppliedLeaveDates = ( isset($employeeLeaveDetails['allAppliedLeaveDates']) ? $employeeLeaveDetails['allAppliedLeaveDates'] : [] );
    		
    	//get month Holiday List
    	$monthHolidayDates =  $this->getAllHoliDayDetails($monthAllDates);
    		
    	//get Employee Suspend
    	$employeeWiseSuspendDates = $this->getAllSuspendDateWiseRecords($employeeLeaveWhere);
    		
    	//echo "<pre>";print_r($employeeWisePresentLeaveCount);
    	//echo "<pre>";print_r($employeeWiseAbsentLeaveCount);
    	//echo "<pre>";print_r($employeeDateWiseDetails);
    	//echo "<pre>";print_r($paidLeaveDates);
    	//echo "<pre>";print_r($paidHalfLeaveDates);
    	//echo "<pre>";print_r($unPaidLeaveDates);
    	//echo "<pre>";print_r($unPaidHalfLeaveDates);
    	//echo "<pre>";print_r($employeeDateWiseDetails);die;
    	if(!empty($employeeDateWiseDetails)){
    			
    		$result = false;
    		DB::beginTransaction();
    			
    		try{
    			foreach($employeeDateWiseDetails as $employeeId =>  $employeeDateWiseDetail){
    				if(!empty($employeeDateWiseDetail)){
    					
    					$checkSalaryWhere = [];
						$checkSalaryWhere['t_is_deleted'] = 0;
						$checkSalaryWhere['i_employee_id'] = $employeeId;;
						$checkSalaryWhere['dt_salary_month'] = date('Y-m-01' , strtotime($attendanceDate));
						$checkSalaryWhere['t_is_salary_generated'] = 1;

						$checkSalaryExist = Salary::where($checkSalaryWhere)->first();
						
						if(!empty($checkSalaryExist) && count(objectToArray($checkSalaryExist))  > 0 ){
							continue;
						}
    					
    					
    					$employeeInfo = EmployeeModel::where('i_id' , $employeeId )->first();
    					$employeeJoiningDate = (!empty($employeeInfo->dt_joining_date) ? $employeeInfo->dt_joining_date : null );
    					$releaseDate = (!empty($employeeInfo->dt_release_date) ? $employeeInfo->dt_release_date : null );
    					$getPresentDates = $employeeDateWiseDetail;
    					$allPresentDates = [];
    					$systemHalfLeaveDates = [];
    					$absentDates = [];
    					
    					if(!empty($getPresentDates)){
    						foreach($getPresentDates as $getPresentDate){
    							if($getPresentDate->e_status == config('constants.PRESENT_STATUS') ){
    								$allPresentDates[] = $getPresentDate->dt_date;
    							} else if($getPresentDate->e_status == config('constants.HALF_LEAVE_STATUS') ){
    								$systemHalfLeaveDates[] = $getPresentDate->dt_date;
    							} else if($getPresentDate->e_status == config('constants.ABSENT_STATUS') ){
    								$absentDates[] = $getPresentDate->dt_date;
    							}
    						}
    					}
    					//echo "<pre>";print_r($allPresentDates);die;
    					$monthAllWeekOfDates = [];
    					$employeeWiseWeekOffDates = [];
    					$getEmployeeWeekOffDates = $this->getEmployeeMonthlyWeekOff( ['employeeId' => $employeeId , 'month' => $year.'-'.$month.'-01' , 'attendanceView' => true ] );
    					$monthAllWeekOfDates = ( isset($getEmployeeWeekOffDates['weekOffDates']) ? $getEmployeeWeekOffDates['weekOffDates'] : [] );
    						
    					//echo "<pre>";print_r($monthAllWeekOfDates);
    					//echo "<pre> monthAllWeekOfDates for emp id = ". $employeeId ;print_r($monthAllWeekOfDates);
    						
    					$employeeSuspendDates = ( isset($employeeWiseSuspendDates[$employeeId])  ? $employeeWiseSuspendDates[$employeeId] : [] );
    						
    					$employeeWisePaidLeaveDates = ( ( isset($paidLeaveDates[$employeeId]) && (!empty($paidLeaveDates[$employeeId])) ) ? $paidLeaveDates[$employeeId] : [] );
    					$employeeWisePaidHalfLeaveDates = ( ( isset($paidHalfLeaveDates[$employeeId]) && (!empty($paidHalfLeaveDates[$employeeId])) ) ? $paidHalfLeaveDates[$employeeId] : [] );
    					$employeeWiseUnPaidLeaveDates = ( ( isset($unPaidLeaveDates[$employeeId]) && (!empty($unPaidLeaveDates[$employeeId])) ) ? $unPaidLeaveDates[$employeeId] : [] );
    					$employeeWiseUnPaidHalfLeaveDates = ( ( isset($unPaidHalfLeaveDates[$employeeId]) && (!empty($unPaidHalfLeaveDates[$employeeId])) ) ? $unPaidHalfLeaveDates[$employeeId] : [] );
    					
    					//echo "<pre>employeeWisePaidHalfLeaveDates";print_r($employeeWisePaidHalfLeaveDates);
    					//echo "<pre>employeeWiseUnPaidHalfLeaveDates";print_r($employeeWiseUnPaidHalfLeaveDates);
    					
    					$presentCount = 0;
    					$absentCount = 0;
    					$halfLeaveCount = 0;
    					$paidLeaveCount = 0;
    					$unPaidLeaveCount = 0;
    					$suspendCount = 0;
    					$paidHalfLeaveCount =  $paidFullLeaveCount = 0;
    					if(!empty($monthAllDates)){
    						foreach($monthAllDates as $monthAllDate){
    							if((!empty($employeeJoiningDate)) && (strtotime($employeeJoiningDate) > strtotime($monthAllDate))){
    								if( !in_array($monthAllDate,$monthAllWeekOfDates)  && !in_array($monthAllDate,$monthHolidayDates) ){
    									if( !in_array( strtolower( date('D' , strtotime($monthAllDate) ) ) , [ 'sun' , 'sat' ]  ) ){
    										$absentCount = $absentCount + config('constants.FULL_LEAVE_VALUE');
    									}
    								}
    								continue;
    							}
    								
    							if((!empty($releaseDate)) && (strtotime($releaseDate) <= strtotime($monthAllDate))){
    								if( !in_array($monthAllDate,$monthAllWeekOfDates)  && !in_array($monthAllDate,$monthHolidayDates) ){
    									if( !in_array( strtolower( date('D' , strtotime($monthAllDate) ) ) , [ 'sun' , 'sat' ]  ) ){
    										$absentCount = $absentCount + config('constants.FULL_LEAVE_VALUE');
    									}
    								}
    								continue;
    							}
    								
    							if(in_array($monthAllDate,$allPresentDates)){
    								$presentCount = $presentCount + config('constants.FULL_LEAVE_VALUE');
    								continue;
    							}
    								
    							if(in_array($monthAllDate,$systemHalfLeaveDates)){
    								$presentCount = $presentCount + config('constants.HALF_LEAVE_VALUE');
    								
    								if( !in_array($monthAllDate,$monthAllWeekOfDates)  && !in_array($monthAllDate,$monthHolidayDates) ){
    									//echo "<pre>";print_r($employeeWiseUnPaidHalfLeaveDates);
    									//echo "<pre>";print_r($employeeWisePaidHalfLeaveDates);
    									
    									if( in_array($monthAllDate,$employeeWisePaidHalfLeaveDates) || in_array($monthAllDate,$employeeWiseUnPaidHalfLeaveDates) ){
    										
    										if(in_array($monthAllDate,$employeeWisePaidHalfLeaveDates)){
    											if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    												echo "employeeWisePaidHalfLeaveDates date = ".$monthAllDate;echo "<br><br>";
    												echo "absent date = ".$monthAllDate;echo "<br><br>";
    											}
    											$paidHalfLeaveCount += config('constants.HALF_LEAVE_VALUE');
    											$paidLeaveCount = $paidLeaveCount + config('constants.HALF_LEAVE_VALUE');
    											$absentCount = $absentCount + config('constants.HALF_LEAVE_VALUE');
    										}
    										//echo "<pre> employeeWiseUnPaidHalfLeaveDates";print_r($employeeWiseUnPaidHalfLeaveDates)	;
    										if(in_array($monthAllDate,$employeeWiseUnPaidHalfLeaveDates)){
    											if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    												echo "employeeWiseUnPaidHalfLeaveDates date = ".$monthAllDate;echo "<br><br>";
    											}
    											
    											$unPaidLeaveCount = $unPaidLeaveCount + config('constants.HALF_LEAVE_VALUE');
    											$absentCount = $absentCount + config('constants.HALF_LEAVE_VALUE');
    											if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    												echo "absent date = ".$monthAllDate;echo "<br><br>";
    											}
    										} else {
    											if( (!in_array($monthAllDate,$employeeWisePaidHalfLeaveDates) ) ){
	    											$unPaidLeaveCount = $unPaidLeaveCount + config('constants.HALF_LEAVE_VALUE');
	    											$absentCount = $absentCount + config('constants.HALF_LEAVE_VALUE');
	    											if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
	    												echo "absent date = ".$monthAllDate;echo "<br><br>";
	    											}
    											}
    										}
    									} else {
    										$unPaidLeaveCount = $unPaidLeaveCount + config('constants.HALF_LEAVE_VALUE');
    										$absentCount = $absentCount + config('constants.HALF_LEAVE_VALUE');
    									}
    								}
    								continue;
    							}
    								
    								
    								
    								
    							if( !in_array($monthAllDate,$monthAllWeekOfDates)  && !in_array($monthAllDate,$monthHolidayDates) ){
    								if(in_array($monthAllDate,$employeeWisePaidLeaveDates)){
    									$paidFullLeaveCount += config('constants.FULL_LEAVE_VALUE');
    									
    									$paidLeaveCount = $paidLeaveCount + config('constants.FULL_LEAVE_VALUE');
    									$absentCount = $absentCount + config('constants.FULL_LEAVE_VALUE');
    									if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    										echo "employeeWisePaidLeaveDates date = ".$monthAllDate;echo "<br><br>";
    										echo "paidLeaveCount date = ".$monthAllDate;echo "<br><br>";
    										echo "absent date = ".$absentCount;echo "<br><br>";
    									}
    									continue;
    								}
    									
    								if(in_array($monthAllDate,$employeeWiseUnPaidLeaveDates)){
    									if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    										echo "employeeWiseUnPaidLeaveDates date = ".$monthAllDate;echo "<br><br>";
    									}
    									
    									$unPaidLeaveCount = $unPaidLeaveCount + config('constants.FULL_LEAVE_VALUE');
    									$absentCount = $absentCount + config('constants.FULL_LEAVE_VALUE');
    									if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    										echo "absent date = ".$monthAllDate;echo "<br><br>";
    									}
    									continue;
    								}
    	
    								if(in_array($monthAllDate,$employeeSuspendDates)){
    									//echo "susdate = ".$monthAllDate;echo "<br><br>";
    									
    									
    									if( in_array($monthAllDate,$employeeWisePaidHalfLeaveDates) || in_array($monthAllDate,$employeeWiseUnPaidHalfLeaveDates) ){
    										if( (in_array($monthAllDate,$employeeWisePaidHalfLeaveDates) ) ){
    											$paidHalfLeaveCount += config('constants.HALF_LEAVE_VALUE');
    											$paidLeaveCount = $paidLeaveCount + config('constants.HALF_LEAVE_VALUE');
    											$absentCount = $absentCount + config('constants.HALF_LEAVE_VALUE');
    											if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    												
    												echo "paid leave count = ".$paidLeaveCount;echo "<br><br>";
    												if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    													echo "absent date = ".$monthAllDate;echo "<br><br>";
    												}
    											}
    											
    										}
    										if( (in_array($monthAllDate,$employeeWiseUnPaidHalfLeaveDates) ) ){
    											$unPaidLeaveCount = $unPaidLeaveCount + config('constants.HALF_LEAVE_VALUE');
    											$absentCount = $absentCount + config('constants.HALF_LEAVE_VALUE');
    											if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    												echo "absent date = ".$monthAllDate;echo "<br><br>";
    											}
    										} else {
    											//if( (!in_array($monthAllDate,$employeeWiseUnPaidHalfLeaveDates) ) ){
    												$unPaidLeaveCount = $unPaidLeaveCount + config('constants.HALF_LEAVE_VALUE');
    												$absentCount = $absentCount + config('constants.HALF_LEAVE_VALUE');
    												if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    													echo "absent date = ".$monthAllDate;echo "<br><br>";
    												}	
    											//}
    											
    										}
    									} else {
    										$suspendCount = $suspendCount + config('constants.FULL_LEAVE_VALUE');
    										$absentCount = $absentCount + config('constants.FULL_LEAVE_VALUE');
    										if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    											//$absentDayCount +=  config('constants.FULL_LEAVE_VALUE');
    										}
    										if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    											echo "absent date = ".$monthAllDate;echo "<br><br>";
    										}
    										
    									}
    									
    									
    									
    									
    									
    									
    									
    									
    									
    									
    									continue;
    								}
    	
    								if( in_array($monthAllDate,$employeeWisePaidHalfLeaveDates) || in_array($monthAllDate,$employeeWiseUnPaidHalfLeaveDates) ){
    									
    									if(in_array($monthAllDate,$employeeWisePaidHalfLeaveDates)){
    										if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    											echo "paid leave count = ".$paidLeaveCount;echo "<br><br>";
    											echo "date = ".$monthAllDate;echo "<br><br>";
    											echo "paidLeaveCount date = ".$monthAllDate;echo "<br><br>";
    											echo "paidLeaveCount date = ".$monthAllDate;echo "<br><br>";
    										}
    										
    										$paidHalfLeaveCount += config('constants.HALF_LEAVE_VALUE');
    										$paidLeaveCount = $paidLeaveCount + config('constants.HALF_LEAVE_VALUE');
    										$absentCount = $absentCount + config('constants.HALF_LEAVE_VALUE');
    										if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    											echo "absent date = ".$monthAllDate;echo "<br><br>";
    										}
    										
    									}
    										
    									if(in_array($monthAllDate,$employeeWiseUnPaidHalfLeaveDates)){
    										if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    											//echo "date = ".$monthAllDate;echo "<br><br>";
    										}
    										
    										if( (!in_array($monthAllDate,$systemHalfLeaveDates) )  && (!in_array($monthAllDate,$employeeWisePaidHalfLeaveDates) ) ){
    											$absentCount = $absentCount + config('constants.FULL_LEAVE_VALUE');
    											if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    												echo "absent date = ".$absentCount;echo "<br><br>";
    											}
    										} else {
    											$unPaidLeaveCount = $unPaidLeaveCount + config('constants.HALF_LEAVE_VALUE');
    											$absentCount = $absentCount + config('constants.HALF_LEAVE_VALUE');
    											if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    												echo "absent date = ".$absentCount;echo "<br><br>";
    											}
    										}
    									} else {
    										//if(!in_array($monthAllDate,$employeeWisePaidHalfLeaveDates)){
    											$unPaidLeaveCount = $unPaidLeaveCount + config('constants.HALF_LEAVE_VALUE');
    											$absentCount = $absentCount + config('constants.HALF_LEAVE_VALUE');
    										//}
    									}
    								} else {
    									
    									$absentCount = $absentCount + config('constants.FULL_LEAVE_VALUE');
    									if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    										echo "absent date = ".$monthAllDate;echo "<br><br>";
    									}
    								}
    							} else {
    	
    							}
    							if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    								echo "date = ".$monthAllDate." is absent count = ".$absentCount;echo "<br><br><br><br>";
    							}
    						}
    					}
    					$absentCount = ( $absentCount >= config('constants.SALARY_COUNT_DAYS') ? config('constants.SALARY_COUNT_DAYS') : $absentCount );
    					//echo "absent count = ".$absentCount." for employee id ".$employeeId;echo "<br><br>";
    					if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    						echo "paid leave count  = ".$paidLeaveCount;echo "<br><br>";
    						echo "absent count  = ".$absentCount;echo "<br><br>";
    						echo "present count  = ".$presentCount;echo "<br><br>";
    					}
    					$presentCount = ( config('constants.SALARY_COUNT_DAYS') - $absentCount + $paidLeaveCount );
    					//echo "present count  = ".$presentCount;echo "<br><br>";
    					/* $presentCount = ( $presentCount >= config('constants.SALARY_COUNT_DAYS') ? config('constants.SALARY_COUNT_DAYS') : $presentCount );
    					 	
    					if( $unPaidLeaveCount > 0 ) {
    					$presentCount = $presentCount - $unPaidLeaveCount;
    					}
    						
    					if( $paidLeaveCount > 0 ) {
    					$presentCount = $presentCount + $paidLeaveCount;
    					} */
    						
    					$presentCount = ( $presentCount >= config('constants.SALARY_COUNT_DAYS') ? config('constants.SALARY_COUNT_DAYS') : $presentCount );
    					if( config('constants.SALARY_DEVELOPER_DEBUG') != false ){
    						echo "final present count = ".$presentCount;
    					}
    					
    					$rowData = [];
    					$rowData['i_employee_id'] = $employeeId;
    					$rowData['dt_month'] = date('Y-m-01' ,strtotime($attendanceDate));
    					$rowData['d_present_count'] = $presentCount;
    					$rowData['d_paid_leave_count'] =  $paidLeaveCount;
    					$rowData['d_unpaid_leave_count'] = $unPaidLeaveCount;
    					$rowData['d_absent_count'] = $absentCount;
    					$rowData['d_suspend_count'] = $suspendCount;
    					$rowData['d_half_leave_count'] = count($systemHalfLeaveDates);
    					
    					$rowData['d_paid_half_leave_count'] = $paidHalfLeaveCount;
    					$rowData['d_paid_full_leave_count'] = $paidFullLeaveCount;
    						
    					$checkRecordWhere = [];
    					$checkRecordWhere['i_employee_id'] = $rowData['i_employee_id'];
    					$checkRecordWhere['dt_month'] = $rowData['dt_month'];
    					$checkRecordWhere['t_is_deleted'] = 0;
    					$checkRecordExist = AttendanceSummaryModel::where($checkRecordWhere)->first();
    	
    					//echo "<pre>";print_r($rowData);
    	
    					if(!empty($checkRecordExist)){
    						$this->crudModel->updateTableData(config('constants.ATTENDANCE_SUMMARY_TABLE'), $rowData , [ 'i_id' => $checkRecordExist->i_id ] );
    					} else {
    						$this->crudModel->insertTableData(config('constants.ATTENDANCE_SUMMARY_TABLE'), $rowData);
    					}
    						
    				}
    			}
    			$result = true;
    		}catch(\Exception $e){
    			$result = false;
    			DB::rollback();
    			//Log::info('error occured while attendance summary on date = ' .$getLastMonthStartDate );
    			var_dump($e->getMessage());echo "<br><br>";
    			var_dump($e->getLine());echo "<br><br>";
    			var_dump($e->getFile());echo "<br><br>";
    			die;
    			//Log::info($e->getMessage());
    			//die("check log");
    		}
    		//var_dump($result);
    		if( $result != false ){
    			DB::commit();
    			//Log::info('success daily attendance summary on date  = ' . $getLastMonthStartDate);
    			return $result;
    			//die("done");
    		} else {
    			//Log::info('error occured while attendance summary on date = ' .$getLastMonthStartDate);
    			DB::rollback();
    			return $result;
    			//die("error");
    		}
    	} else {
    		return true;
    	}
    }
    
    public function apiAttenndance( $attedanceDate , $redirectUrl = false ){
    	
    	$url = config('constants.MATRIX_API_BASE_URL');
    	$url .= config('constants.DAILY_ATTENDANCE_API_URL') . '?';
    	
    	$attedanceEndDate = $attedanceDate;
    	
    	/* if( strtotime( 'now' ) <= strtotime( date('15:00')) ){
    		$convertAttedanceEndDate = \DateTime::createFromFormat('dmY', $attedanceEndDate);
    		$newAttedanceEndDate =  $convertAttedanceEndDate->format('Y-m-d');
    		$attedanceEndDate = date('dmY', strtotime("-1 day" , strtotime($newAttedanceEndDate)));
    	} */
    	
    	
    	$fieldNames = [];
    	$fieldNames[] = 'userid';
    	$fieldNames[] = 'orgid';
    	$fieldNames[] = 'processdate';
    	$fieldNames[] = 'punch1_time';
    	$fieldNames[] = 'outpunch_time';
    	$fieldNames[] = 'firsthalf';
    	$fieldNames[] = 'secondhalf';
    	$fieldNames[] = 'lunchstart';
    	$fieldNames[] = 'lunchend';
    	$fieldNames[] = 'lunchduration_hhmm';
    	$fieldNames[] = 'worktime_hhmm';
    	
    	$apiParams = [];
    	$apiParams['action'] = 'get';
    	$apiParams['format'] = 'json';
    	$apiParams['id'] = '105';
    	$apiParams['date-range'] = $attedanceEndDate.'-'.$attedanceDate;
    	//$apiParams['field-name'] = implode("," , $fieldNames );
    	
    	$params = "";
    	if(!empty($apiParams)){
    		foreach($apiParams as $apiParamKey =>  $apiParam){
    			$params .= $apiParamKey.'='.$apiParam.';';
    		}
    		$params = rtrim($params,";");
    	}
    	$url .= $params;
    	//echo $url;echo "<br><br>";
    	//var_dump($params);die;
    	
    	$getApiDetails = Wild_tiger::curlRequest($url , [] , [ "Content-Type: application/json" ] );
    	//echo "<pre>";print_r($getApiDetails);
    	
    	$getAllEmployeeDetails = EmployeeModel::where('t_is_deleted' , 0 )->get();
    	
    	$allEmployeeCodeDetails = [];
    	if(!empty($getAllEmployeeDetails)){
    		foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
    			if( $getAllEmployeeDetail->e_auto_generate_no == config('constants.SELECTION_YES') ){
    				$allEmployeeCodeDetails[] = threeNumberSeries($getAllEmployeeDetail->v_employee_code);
    			} else {
    				$allEmployeeCodeDetails[] = ($getAllEmployeeDetail->v_employee_code);
    			}
    		}
    	}
    	
    	//echo "<pre>";print_r($getApiDetails);
    	
    	$response = [];
    	$response['status'] = false;
    	
    	if( isset($getApiDetails['status']) && ( $getApiDetails['status'] != false ) ){
    			
    		$this->crudModel =  New ApiAttendanceInfo();
    			
    		$attendanceDetails = ( isset($getApiDetails['msg']) ? json_decode( $getApiDetails['msg'] , true  ) : [] );
    			
    		$recordDetails = ( isset($attendanceDetails['template-data']) ? $attendanceDetails['template-data'] : [] );
    		if( $redirectUrl != true ){
    			echo "<pre>";print_r($recordDetails);
    		}
    		//echo "<pre>";print_r($recordDetails);die;
    		if(!empty($recordDetails)){
    	
    			$result = false;
    			DB::beginTransaction();
    	
    			try{
    				foreach($recordDetails as $recordDetail){
    					
    					$multipleRows = [];
    					
    					$userId = ( isset($recordDetail['userid']) ? $recordDetail['userid'] : null );
    					$inDate = ( isset($recordDetail['punch1_date']) ? dbDate( $recordDetail['punch1_date'] ) : null );
    					$outDate = ( isset($recordDetail['outpunch_date']) ? dbDate( $recordDetail['outpunch_date'] ) : null );
    					$inTime = ( ( isset($recordDetail['punch1_time']) && (!empty($recordDetail['punch1_time'])) ) ? $recordDetail['punch1_time'] : null );
    					$outTime = ( ( isset($recordDetail['outpunch_time']) && (!empty($recordDetail['outpunch_time'])) ) ? $recordDetail['outpunch_time'] : null );
    					$firstHalf = ( isset($recordDetail['firsthalf']) ? $recordDetail['firsthalf'] : null );
    					$secondHalf = ( isset($recordDetail['secondhalf']) ? $recordDetail['secondhalf'] : null );
    					$lunchTime = ( ( isset($recordDetail['outtime']) && (!empty($recordDetail['outtime'])) ) ? convertSecondIntoHour( ( $recordDetail['outtime'] * 60 ) ,"H:i:s")  : null );
    					$workingTime  = ( ( isset($recordDetail['worktime_hhmm']) && (!empty($recordDetail['worktime_hhmm'])) ) ? dbTime ( $recordDetail['worktime_hhmm'] ) : null );
    					
    					$rowData = [];
    					$rowData['v_user_id'] = $userId;
    					$rowData['dt_process_date'] = (!empty($inDate) ?  $inDate : date('Y-m-d' , strtotime($attedanceDate)));
    					$rowData['t_start_time'] = $inTime;
    					$rowData['t_end_time'] = $outTime;
    					$rowData['v_first_half'] = $firstHalf;
    					$rowData['v_second_half'] = $secondHalf;
    					$rowData['t_lunch_start_time'] = ( ( isset($recordDetail['lunchstart']) && (!empty($recordDetail['lunchstart'])) ) ? date( 'Y-m-d H:i:s' ,strtotime( $recordDetail['lunchstart'] ) ) : null );
    					$rowData['t_lunch_end_time'] = ( ( isset($recordDetail['lunchend']) && (!empty($recordDetail['lunchend'])) ) ? date( 'Y-m-d H:i:s' ,strtotime( $recordDetail['lunchend'] ) ) : null );
    					$rowData['t_lunch_time'] = $lunchTime;
    					$rowData['t_work_time'] = $workingTime;
    					$rowData['v_api_response']  = (!empty($recordDetail) ? json_encode($recordDetail) : null );
    	
    					//echo "<pre>";print_r($rowData);
    	
    					if( (!empty($rowData['v_user_id'])) && (!empty($rowData['dt_process_date'])) ){
    						$checkRecordWhere = [];
    						$checkRecordWhere['v_user_id'] = $rowData['v_user_id'];
    						$checkRecordWhere['dt_process_date'] = $rowData['dt_process_date'];
    						$checkRecordWhere['t_is_deleted'] = 0;
    							
    						//echo "<pre>";print_r($checkRecordWhere);
    							
    						$checkRecordExist = ApiAttendanceInfo::where($checkRecordWhere)->first();
    	
    						if(!empty($checkRecordExist)){
    							$this->crudModel->updateTableData(config('constants.API_ATTENDANCE_DETAILS'), $rowData , [ 'i_id' => $checkRecordExist->i_id ] );
    						} else {
    							$this->crudModel->insertTableData(config('constants.API_ATTENDANCE_DETAILS'), $rowData);
    						}
    							
    							
    						$employeeId = 0;
    						if( in_array($rowData['v_user_id'] ,  $allEmployeeCodeDetails) ){
    							$searchKey = array_search($rowData['v_user_id'] ,  $allEmployeeCodeDetails);
    							if(strlen($searchKey) > 0 ){
    								$employeeId = ( isset($getAllEmployeeDetails[$searchKey]->i_id) ? $getAllEmployeeDetails[$searchKey]->i_id : 0 );
    								//echo "emp id = ".$employeeId;
    								if( $employeeId > 0 ){
    	
    									$attedanceData = [];
    									$attedanceData['t_start_time'] = $rowData['t_start_time'];
    									$attedanceData['t_end_time'] = $rowData['t_end_time'];
    									$attedanceData['dt_matrix_start_time'] = $rowData['t_start_time'];
    									$attedanceData['dt_matrix_end_time'] = $rowData['t_end_time'];
    									$attedanceData['t_total_working_time'] = $rowData['t_work_time'];
    									$attedanceData['t_break_start_time'] = $rowData['t_lunch_start_time'];
    									$attedanceData['t_break_end_time'] = $rowData['t_lunch_end_time'];
    									$attedanceData['t_total_break_time'] = $rowData['t_lunch_time'];
    	
    									$dayCount = 0;
    									if( isset($rowData['v_first_half']) && ( $rowData['v_first_half'] == "PR") ){
    										$dayCount =  $dayCount + config('constants.HALF_LEAVE_VALUE');
    									}
    	
    									if( isset($rowData['v_second_half']) && ( $rowData['v_second_half'] == "PR") ){
    										$dayCount =  $dayCount + config('constants.HALF_LEAVE_VALUE');
    									}
    	
    									$presentStatus = config('constants.ABSENT_STATUS');
    									switch($dayCount){
    										case config('constants.HALF_LEAVE_VALUE'):
    											$presentStatus = config('constants.HALF_LEAVE_STATUS');
    											break;
    										case config('constants.FULL_LEAVE_VALUE'):
    											$presentStatus = config('constants.PRESENT_STATUS');
    											break;
    									}
    									$attedanceData['e_status'] = $presentStatus;
    									if( $presentStatus == config('constants.HALF_LEAVE_VALUE') ){
    										$attedanceData['t_is_half_leave'] = 1;
    									}
    	
    									$checkRecordWhere = [];
    									$checkRecordWhere['i_employee_id'] = $employeeId;
    									$checkRecordWhere['dt_date'] = $rowData['dt_process_date'];
    									$checkRecordWhere['t_is_deleted'] = 0;
    									$checkRecordExist = MyAttendanceModel::where($checkRecordWhere)->first();
    	
    									//echo "<pre>";print_r($attedanceData);
    	
    									if(!empty($checkRecordExist)){
    										$this->crudModel->updateTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $attedanceData , [ 'i_id' => $checkRecordExist->i_id ] );
    									} else {
    										$attedanceData['i_employee_id'] = $employeeId;
    										$attedanceData['dt_date'] = $rowData['dt_process_date'];
    										$this->crudModel->insertTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $attedanceData);
    									}
    	
    								}
    							}
    						}
    					}
    				}
    				$result = true;
    			}catch(\Exception $e){
    				var_dump($e->getMessage());
    				DB::rollback();
    				//Log::info('error occured while fetch api attendance deatils');
    			}
    	
    			if( $result != false ){
    				DB::commit();
    				
    				//Log::info('success fetch api attendance info');
    				if( $redirectUrl != false ){
    					$response['status'] = true;
    					$response['msg'] = trans('messages.success-sync-attendance');
    					return $response;
    				} else {
    					echo "success fetch api attendance info";
    				}
    			} else {
    				
    				//Log::info('error occured while fetch api attendance info');
    				DB::rollback();
    				if( $redirectUrl != false ){
    					$response['msg'] = trans('messages.error-sync-attendance');
    					return $response;
    				} else {
    					echo "error occured while fetch api attendance info";
    				}
    			}
    		}
    		if( $redirectUrl != false ){
    			$response['msg'] = trans('messages.no-record-found');
    			return $response;
    		} else {
    			echo trans('messages.no-record-found');
    		}
    		
    			
    	}
    	if( $redirectUrl != false ){
    		$response['msg'] = trans('messages.system-error');
    		return $response;
    		//Log::info('error occured while fetch api attendance deatils');
    		//Log::info(print_r($response,true));
    	} else {
    		echo trans('messages.system-error');
    		//Log::info('error occured while fetch api attendance deatils');
    		//Log::info(print_r($response,true));
    	}
    }
}
