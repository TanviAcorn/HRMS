<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\MyLeaveModel;
use App\LeaveTypeMasterModel;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use App\Models\LeaveBalanceModel;
use App\HolidayMasterModel;
use DB;
use App\Models\LeaveSummaryModel;
use App\Models\LeaveAssignHistoryModel;
use App\EmployeeModel;
use App\Rules\CheckDuplicateLeave;
use App\Models\AttendanceSummaryModel;
use App\Models\SettingsModel;
use App\Rules\CheckBalanceLeave;

class MyLeaveMasterController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->crudModel =  new MyLeaveModel();
		$this->moduleName = trans('messages.my-leaves');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->tableName = config('constants.APPLY_LEAVE_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'my-leaves/' ;
		$this->redirectUrl = config('constants.MY_LEAVES_MASTER_URL');
		$this->leaveBalanceModel = new LeaveBalanceModel();
		$this->leaveAssignHistoryModel = new LeaveAssignHistoryModel();
		$this->employeeId = 1;
		$this->documentFolder = 'document/' ;
		$this->settingsModel =  new SettingsModel();
	
	}
	public function index(Request $request){
		$ajaxRequest = false;
		
		if($request->ajax()){
			$ajaxRequest = true;
			$this->employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		} else {
			$this->employeeId = session()->get('user_employee_id');
		}
		
		$employeeInfo = EmployeeModel::where('i_id'  , $this->employeeId)->first();
		$employeeJoiningDate = (!empty($employeeInfo) ? $employeeInfo->dt_joining_date : null );
		
		$data = [];
		$data['pageTitle'] = trans('messages.my-leaves');
		$currentYear = currentSystemYear();
		
		$startDate = getYearStartDate($currentYear);
		$endDate = getYearEndDate($currentYear);
		
		$minYear = null;
		if(!empty($employeeJoiningDate)){
			$empJoiningYear = date('Y', strtotime($employeeJoiningDate));
			if( $empJoiningYear > config('constants.SYSTEM_START_YEAR')){
				$minYear = $empJoiningYear;
			}
		}
		
		$data['yearDetails'] = yearDetails($minYear);
		
		$data['currentYear'] = $currentYear;
		$data['startDate'] = $startDate;
		$data['endDate'] = $endDate;
		$data['leaveCountDetails'] = $this->getLeaveCountInfo( $this->employeeId , $startDate , $endDate  );
		
		$getLeaveSummary = $this->getMonthWiseWeekWiseLeaveCount( $this->employeeId , $startDate , $endDate  );
		$data['weekDayWiseCount'] = $getLeaveSummary['weekDayWiseCount'];
		$data['monthWiseCount'] = $getLeaveSummary['monthWiseCount'];
		$data['leaveConsumeInfo'] = $getLeaveSummary['leaveConsumeInfo'];
		$data['leaveAvailableInfo'] = $getLeaveSummary['leaveAvailableInfo'];
		
		/* echo "<pre>";print_r($data['leaveConsumeInfo']);
		echo "<pre>";print_r($data['leaveAvailableInfo']);die; */
		
		$data['leaveTypeDetails'] = LeaveTypeMasterModel::where('t_is_deleted' , 0 )->get();
		$data['employeeId'] = Wild_tiger::encode($this->employeeId);
		//echo "<pre>";print_r($data);die;
		
		$whereData = [] ;
		$whereData['singleRecord'] = true;
		$settingsInfo = $this->settingsModel->getRecordDetails($whereData);
		$data['settingsInfo'] = $settingsInfo ;
		
		if( $ajaxRequest != false ){
			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'my-leaves/my-leaves-main-list' )->with ( $data )->render();
			echo $html;die;
		}
		
		return view( $this->folderName . 'my-leaves')->with($data);
	}
	
	private function getLeaveCountInfo( $employeeId , $startDate , $endDate ){
	
		$leaveCountDetails =  self::CallRaw ( 'get_employee_leave_summary' , [ $employeeId , $startDate , $endDate  ]);
		$leaveCountDetails = ( isset( $leaveCountDetails[0][0] ) ? $leaveCountDetails[0] : []  );
		//echo "<pre>";print_r($leaveCountDetails);
		$leaveCount = [];
		$leaveCount['approved_count'] = 0;
		$leaveCount['pending_count'] = 0;
		$leaveCount['rejected_count'] = 0;
		$leaveCount['cancelled_count'] = 0;

		if(!empty($leaveCountDetails)){
			$leaveCountDetails = objectToArray($leaveCountDetails);
			///echo "<pre>";print_r($leaveCountDetails);
			$allStatusDetails = array_column($leaveCountDetails,'e_status');
			//echo "<pre>";print_r($allStatusDetails);
			if(in_array(config("constants.PENDING_STATUS"), $allStatusDetails)){
				$searchKey = array_search(config("constants.PENDING_STATUS"), $allStatusDetails);
				if(strlen($searchKey) > 0 ){
					$leaveCount['pending_count'] = isset($leaveCountDetails[$searchKey]['record_count']) ? $leaveCountDetails[$searchKey]['record_count'] : 0 ;;
				}
			}
			if(in_array(config("constants.APPROVED_STATUS"), $allStatusDetails)){
				$searchKey = array_search(config("constants.APPROVED_STATUS"), $allStatusDetails);
				if(strlen($searchKey) > 0 ){
					$leaveCount['approved_count'] = isset($leaveCountDetails[$searchKey]['record_count']) ? $leaveCountDetails[$searchKey]['record_count'] : 0 ;;
				}
			}
			if(in_array(config("constants.REJECTED_STATUS"), $allStatusDetails)){
				$searchKey = array_search(config("constants.REJECTED_STATUS"), $allStatusDetails);
				if(strlen($searchKey) > 0 ){
					$leaveCount['rejected_count'] = isset($leaveCountDetails[$searchKey]['record_count']) ? $leaveCountDetails[$searchKey]['record_count'] : 0 ;;
				}
			}
			if(in_array(config("constants.CANCELLED_STATUS"), $allStatusDetails)){
				$searchKey = array_search(config("constants.CANCELLED_STATUS"), $allStatusDetails);
				if(strlen($searchKey) > 0 ){
					$leaveCount['cancelled_count'] = isset($leaveCountDetails[$searchKey]['record_count']) ? $leaveCountDetails[$searchKey]['record_count'] : 0 ;;
				}
			}
		}
		
		$leaveCount['total_count'] = ( $leaveCount['pending_count'] + $leaveCount['approved_count'] + $leaveCount['rejected_count']  + $leaveCount['cancelled_count'] );
		//dd($leaveCount);
		return $leaveCount;
	}
	public function editApplyLeave(Request $request){
		$data = $whereData = $where = [];
		$where['t_is_active'] = 1;
		$where['t_is_show'] = 1;
		$recordId = (!empty($request->input('apply_leave_id')) ? $request->input('apply_leave_id') : '' );
		
		/*
		if(!empty($recordId)){
			//$recordId = (int)Wild_tiger::decode($recordId);
			$whereData['master_id'] = $recordId;
			$whereData['singleRecord'] = true;
			$recordInfo = $this->crudModel->getRecordDetails($whereData);
			if(!empty($recordInfo)){
				$data['recordInfo']= $recordInfo;
				unset($where['t_is_active']);
			}
		}
		*/
		$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : session()->get('user_employee_id') );
		
		$getYearAppliedLeaveWhere = [];
		$getYearAppliedLeaveWhere['leave_from_date'] = date('Y-01-01');
		$getYearAppliedLeaveWhere['leave_to_date'] = date('Y-12-31');
		$getYearAppliedLeaveWhere['employee_id'] = $employeeId;
		$getYearAppliedLeaveWhere['leave_status'] = [ config('constants.APPROVED_STATUS') , config('constants.PENDING_STATUS')  ];
		
		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$getYearAppliedLeaveWhere['show_all'] = true;
		}
		//$getAppliedLeaveWhere['employee_team'] = $employeeTeamId;
		//echo "<pre>";print_r($getYearAppliedLeaveWhere);
		$getYearAppliedLeaveDetails = $this->crudModel->getRecordDetails($getYearAppliedLeaveWhere);
		//echo "<pre>";print_r($getYearAppliedLeaveDetails);die;
		 
		$leaveConsumeInfo = [];
		$leaveConsumeInfo[config("constants.EARNED_LEAVE_TYPE_ID")] = 0;
		$leaveConsumeInfo[config("constants.CARRY_FORWARD_LEAVE_TYPE_ID")] = 0;
		$leaveConsumeInfo[config("constants.PAID_LEAVE_TYPE_ID")] = 0;
		$leaveConsumeInfo[config("constants.UNPAID_LEAVE_TYPE_ID")] = 0;
		 
		if(!empty($getYearAppliedLeaveDetails)){
			foreach($getYearAppliedLeaveDetails as $getYearAppliedLeaveDetail){
				$leaveConsumeInfo[$getYearAppliedLeaveDetail->i_leave_type_id] += $getYearAppliedLeaveDetail->d_no_days;
			}
		}
		$data['leaveConsumeInfo'] = $leaveConsumeInfo;
		
		
		
		
		$data['leaveTypeDetails'] = LeaveTypeMasterModel::where($where)->orderBy('v_leave_type_name', 'ASC')->get();
		
		$employeeInfo = EmployeeModel::where('i_id' , $employeeId )->first();
		$employeeTeamId = ( isset($employeeInfo->i_team_id) ? $employeeInfo->i_team_id : null );
		
		$leaveBalanceWhere = [];
		$leaveBalanceWhere['i_employee_id'] = $employeeId;
		$leaveBalanceWhere['order_by'] = [ 'i_leave_type_id' => 'asc' ];
		$data['leaveBalanceDetails'] = $this->leaveBalanceModel->getRecordDetails($leaveBalanceWhere);
		
		$data['employeeLeaveDetails'] = [];
		
		$selectedMonth = date('Y-m-d');
		$startDate = monthStartDate($selectedMonth);
    	$endDate = monthEndDate($selectedMonth);
    	
    	$calendarDetails = $this->getCalendarLeaveDetail( ['employee_team_id' => $employeeTeamId , 'employee_id' => $employeeId ,   'month' => $selectedMonth ]  );
		
    	//echo "<pre>";print_r($calendarDetails['employeeWeekOffDates']);die;
    	
		$data['weekOffDates'] = ( isset($calendarDetails['employeeWeekOffDates']) ? $calendarDetails['employeeWeekOffDates'] : [] );
		$data['teamLeaveDetails'] = ( isset($calendarDetails['teamLeaveDetails']) ? $calendarDetails['teamLeaveDetails'] : [] );
		$data['appliedLeaveDates'] = ( isset($calendarDetails['appliedLeaveDates']) ? $calendarDetails['appliedLeaveDates'] : [] );
		$data['holidayDetails'] = ( isset($calendarDetails['holidayDetails']) ? $calendarDetails['holidayDetails'] : [] );
		//echo "<pre>";print_r($data['weekOffDates']);die;
		//dd($data['teamLeaveDetails']);
		
		
		$response = [];
		$response['leaveFormHtml'] = view ($this->folderName . 'add-apply-leave-model')->with ( $data )->render();
		$response['leaveBalanceHtml'] = view ($this->folderName . 'leave-balance-modal-html')->with ( $data )->render();
		$response['leaveCalendarHtml'] = view ($this->folderName . 'leave-calendar')->with ( $data )->render();
		$response['employeeName'] = ( isset($employeeInfo->v_employee_full_name) ? $employeeInfo->v_employee_full_name : null );
		$response['employeeJoiningDate'] = ( isset($employeeInfo->dt_joining_date) ? $employeeInfo->dt_joining_date : null );
		$response['allowedLastEffDate'] = lastAllowedDate($employeeInfo);
		
		$this->ajaxResponse(1, trans('messages.success') , $response );
		
		
		
		
		
		$html = view ($this->folderName . 'add-apply-leave-model')->with ( $data )->render();
		echo $html;die;
	}
	
	private function getCalendarLeaveDetail( $selection = [] ){
		
		$employeeTeamId = ( isset($selection['employee_team_id']) ? $selection['employee_team_id'] : 0 );
		$employeeId = ( isset($selection['employee_id']) ? $selection['employee_id'] : 0 );
		$selectedMonth = ( isset($selection['month']) ? $selection['month'] : date('Y-m-d') );
		
		$startDate = monthStartDate($selectedMonth);
		$endDate = monthEndDate($selectedMonth);
		
		$appliedLeaveDates = [];
		$teamLeaveDetails = [];
		$getSuspendHistoryDetail = [];
		if( $employeeTeamId > 0 ){
			$getAppliedLeaveWhere = [];
			$getAppliedLeaveWhere['leave_status'] = [ config('constants.APPROVED_STATUS') , config('constants.PENDING_STATUS')  ];
			$getAppliedLeaveWhere['leave_from_date'] = $startDate;
			$getAppliedLeaveWhere['leave_to_date'] = $endDate;
			$getAppliedLeaveWhere['order_by'] = [ 'dt_leave_from_date'  => 'asc'];
			//$getAppliedLeaveWhere['employee_team'] = $employeeTeamId;
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$getAppliedLeaveWhere['show_all'] = true;
			}
				
			$getAppliedLeaveDetails = $this->crudModel->getRecordDetails($getAppliedLeaveWhere);
			
			if(!empty($getAppliedLeaveDetails)){
				foreach($getAppliedLeaveDetails as $getAppliedLeaveDetail){
					$rowData = [];
					$rowData['employee_name'] = ( isset($getAppliedLeaveDetail->employeeInfo->v_employee_full_name) ? $getAppliedLeaveDetail->employeeInfo->v_employee_full_name : null );
					$rowData['employee_code'] = ( isset($getAppliedLeaveDetail->employeeInfo->v_employee_code) ? $getAppliedLeaveDetail->employeeInfo->v_employee_code : null );
					$rowData['profile_pic'] = ( ( isset($getAppliedLeaveDetail->employeeInfo->v_profile_pic) && file_exists(config('constants.FILE_STORAGE_PATH') .  config('constants.UPLOAD_FOLDER')  . $getAppliedLeaveDetail->employeeInfo->v_profile_pic  ) )  ? config('constants.FILE_STORAGE_PATH_URL') . config('constants.UPLOAD_FOLDER') .   $getAppliedLeaveDetail->employeeInfo->v_profile_pic : null );
					if( strtotime($getAppliedLeaveDetail->dt_leave_from_date)  != strtotime($getAppliedLeaveDetail->dt_leave_to_date) ){
						$rowData['leave_duration'] = convertDateFormat($getAppliedLeaveDetail->dt_leave_from_date, 'd M') . ' - ' . convertDateFormat($getAppliedLeaveDetail->dt_leave_to_date, 'd M') ;
						/* if(!empty($getSuspendHistoryDetail)){ */
							$getAllDates = getDatesFromRange($getAppliedLeaveDetail->dt_leave_from_date, $getAppliedLeaveDetail->dt_leave_to_date);
							if(!empty($getAllDates)){
								foreach($getAllDates as $getAllDate){
									$appliedLeaveDates[] = $getAllDate;
								}
							}
						/* } */
						
					} else {
						
						$rowData['leave_duration'] = convertDateFormat($getAppliedLeaveDetail->dt_leave_from_date, 'd M') ;
						$appliedLeaveDates[] = (isset($getAppliedLeaveDetail->dt_leave_from_date) ? $getAppliedLeaveDetail->dt_leave_from_date :'');
					}
					$rowData['days'] = ( isset($getAppliedLeaveDetail->d_no_days) ? $getAppliedLeaveDetail->d_no_days : null );
					$teamLeaveDetails[] = $rowData;
				}
			}
				
		}
		
		$this->holidayModel = new HolidayMasterModel();
		
		$holidayWhere = [];
		$holidayWhere['holiday_from_date'] = $startDate;
		$holidayWhere['holiday_to_date'] = $endDate;
		$holidayWhere['active_status'] = 1;
		$getAllHolidayDetails = $this->holidayModel->getRecordDetails($holidayWhere);
		
		$getEmployeeWeekOffDates = $this->getEmployeeMonthlyWeekOff( ['employeeId' => $employeeId , 'month' => $selectedMonth , 'calendarView' => true  ] );
		//echo "<pre>";print_r($getEmployeeWeekOffDates);die;
		$response = [];
		$response['teamLeaveDetails'] = $teamLeaveDetails;
		$response['appliedLeaveDates'] = $appliedLeaveDates;
		$response['holidayDetails'] = $getAllHolidayDetails;
		$response['calendarStartDate'] = $startDate;
		$response['employeeWeekOffDates'] = ( isset($getEmployeeWeekOffDates['weekOffDates']) ? $getEmployeeWeekOffDates['weekOffDates'] : [] );
		return $response;
		
	}
	
	public function addApplyLeave(Request $request){
		
		if(!empty($request->input())){
			$leaveRemoveImages = (!empty($request->post('remove_image')) ? trim($request->post('remove_image')) : '' );
			$leaveFinalSelectedImages = (!empty($request->post('final_selected_image')) ? trim($request->post('final_selected_image')) : '' );
			
			$durationCount = (!empty($request->post('duration_count')) ? trim($request->post('duration_count')) : 1 );
			//$leaveRecordId = (!empty($request->post('apply_leave_id')) ? (int)Wild_tiger::decode($request->post('apply_leave_id')) : 0 );	
			$leaveRecordId = (!empty($request->post('apply_leave_id')) ? trim($request->post('apply_leave_id')) : '' );
			$leaveRecordId = 0;
			$formValidation =[];
			$formValidation['leave_from_date'] = ['required' ,  new CheckDuplicateLeave($request)  ];
			$formValidation['leave_to_date'] = ['required'];
			$formValidation['leave_types'] = ['required' , new CheckBalanceLeave($request)];
			$formValidation['leave_note'] = ['required'];
			$checkValidation = Validator::make($request->all(),$formValidation,
					[
							'leave_from_date.required' => __('messages.please-enter-from-date'),
							'leave_to_date.required' => __('messages.please-enter-to-date'),
							'leave_types.required' => __('messages.please-select-leave-types'),
							'leave_note.required' => __('messages.please-enter-note'),
					]
			);
			if($checkValidation->fails() != false){
				$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.apply-leave') ] ) ) );
			}
				
			$successMessage =  trans('messages.success-create',['module'=> trans('messages.leave')]);
			$errorMessages = trans('messages.error-create',['module'=> trans('messages.leave')]);
			$result = false;
			
			$dualDateFromSession = (!empty($request->input('dual_date_from_session')) ? trim($request->input('dual_date_from_session')) : null );
			$dualDateToSession = (!empty($request->input('dual_date_to_session')) ? trim($request->input('dual_date_to_session')) : null );
			$singleDateSession = (!empty($request->input('single_date_session')) ? trim($request->input('single_date_session')) : null );
			
			$leaveFromDate = (!empty($request->post('leave_from_date')) ? dbDate($request->post('leave_from_date')) : '');
			$leaveToDate = (!empty($request->post('leave_to_date')) ? dbDate($request->post('leave_to_date')) : '');
			
			$recordData = [];
			$recordData['dt_leave_from_date'] = $leaveFromDate;
			$recordData['dt_leave_to_date'] = $leaveToDate;
			
			$allLeaveDates = [];
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : session()->get('user_employee_id') );
			
			if( strtotime($leaveFromDate) == strtotime($leaveToDate) ){
				$recordData['e_duration'] = $singleDateSession ;
				$recordData['e_from_duration'] = null ;
				$recordData['t_is_multiple'] = 0;
				$recordData['e_to_duration'] = null ;
				$recordData['t_is_half_leave'] = 0;
				$recordData['d_no_days'] = config('constants.FULL_LEAVE_VALUE');
				if( in_array( $recordData['e_duration'] , [ config('constants.FIRST_HALF_LEAVE') , config('constants.SECOND_HALF_LEAVE') ] ) ){
					$recordData['t_is_half_leave'] = 1;
					$recordData['d_no_days'] = config('constants.HALF_LEAVE_VALUE');
				}
			} else {
				$recordData['e_duration'] = null ;
				$recordData['e_from_duration'] = $dualDateFromSession ;
				$recordData['e_to_duration'] = $dualDateToSession ;
				$recordData['t_is_multiple'] = 1;
				$recordData['t_is_half_leave'] = 0;
				$allLeaveDates = getDatesFromRange( $leaveFromDate , $leaveToDate );
				$noOfDays = count($allLeaveDates);
				
				
				
				if( ( in_array( $dualDateFromSession , [  config('constants.SECOND_HALF_LEAVE') ] ) ) ){
					$noOfDays -= config('constants.HALF_LEAVE_VALUE');
				}
				if( ( in_array( $dualDateToSession , [ config('constants.FIRST_HALF_LEAVE') ] ) ) ){
					$noOfDays -= config('constants.HALF_LEAVE_VALUE');
				}
				
				$recordData['d_no_days'] = $noOfDays;
				
			}
			
			$leaveTypeId = (!empty($request->post('leave_types')) ? (int)Wild_tiger::decode($request->post('leave_types')) : 0);
			$recordData['i_leave_type_id'] = $leaveTypeId;
			$recordData['v_leave_note'] = (!empty($request->post('leave_note')) ? trim($request->post('leave_note')) : '');
			$recordData['i_employee_id'] = $employeeId;
			
			if($request->hasFile('file_upload')){
     			$uploadFile = $this->uploadMultipleFiles($request,'file_upload' , $this->documentFolder ,$leaveFinalSelectedImages,$leaveRemoveImages);
     			
     			if( (!empty($uploadFile)) && ( isset($uploadFile['status']) ) && ( $uploadFile['status'] != false )  ){
     				$uploadFile = $uploadFile['uploadedImagePath'];
     				$recordData['v_file'] = (!empty($uploadFile) ? json_encode($uploadFile) : null ) ;
     			} else {
     				$this->ajaxResponse(101, isset($uploadFile['message']) ? $uploadFile['message'] : trans('messsages.system-error'));
     			} 
     		}
     		//echo "<pre>";print_r($recordData);die;
     		
			$result = false;
			DB::beginTransaction();
			try{
				if($leaveRecordId > 0 ){
				
					$successMessage =  trans('messages.success-update',['module'=> trans('messages.leave')]);
					$errorMessages = trans('messages.error-update',['module'=> trans('messages.leave')]);
					$result = $this->crudModel->updateTableData($this->tableName, $recordData, ['i_id' => $leaveRecordId]);
						
				} else {
					
					$insertRecord = $this->crudModel->insertTableData( $this->tableName , $recordData  );
					
					$getEmployeeWhere = [];
					$getEmployeeWhere['t_is_deleted != '] = 1;
					$getEmployeeWhere['i_id'] = $employeeId;
					$getEmployeeInfo = $this->crudModel->getSingleRecordById(config('constants.EMPLOYEE_MASTER_TABLE') , [ 'dt_probation_end_date' , 'dt_notice_period_start_date' , 'dt_notice_period_end_date' ] , $getEmployeeWhere );
					
					$probationEndDate = (!empty($getEmployeeInfo) ? (!empty($getEmployeeInfo->dt_probation_end_date) ? $getEmployeeInfo->dt_probation_end_date : null ) : null );
					$noticePeriodStartDate = (!empty($getEmployeeInfo) ? (!empty($getEmployeeInfo->dt_notice_period_start_date) ? $getEmployeeInfo->dt_notice_period_start_date : null ) : null );
					$noticePeriodEndDate = (!empty($getEmployeeInfo) ? (!empty($getEmployeeInfo->dt_notice_period_end_date) ? $getEmployeeInfo->dt_notice_period_end_date : null ) : null );
					
					$allNoticePeriodDates = [];
					if( (!empty($noticePeriodStartDate)) && (!empty($noticePeriodEndDate)) ){
						$allNoticePeriodDates = getDatesFromRange($noticePeriodStartDate ,  $noticePeriodEndDate );
					}
					//var_dump($leaveTypeId);
					$leaveBalanceWhere = [];
					$leaveBalanceWhere['i_employee_id'] = $employeeId;
					$leaveBalanceWhere['i_leave_type_id'] = $leaveTypeId;
					$leaveBalanceWhere['singleRecord'] = true;
					//$leaveAssignHistoryWhere['t_is_used_status'] = 0;
					//$leaveAssignHistoryWhere['custom_function'][] =  "date(dt_effective_date) <=  '" . $leaveToDate . "'";
					
					//$leaveBalanceWhere['singleRecord'] = true;
					//$whereData['custom_function'][] =  "date(osh.dt_effective_date) >=  '" . $startDate . "'";
					
					//$leaveBalanceInfo = $this->leaveAssignHistoryModel->getRecordDetails($leaveAssignHistoryWhere);
					
					//echo "<pre>";print_r($leaveBalanceWhere);
					
					$leaveBalanceInfo = $this->leaveBalanceModel->getRecordDetails($leaveBalanceWhere);
					//echo $this->leaveBalanceModel->last_query();
					
					$monthWiseLeaveDetails = [];
					
					$leavePaidUnpaidDayWiseInfo = [];
					
					$totalPaidLeaveCount = 0;
					$totalUnPaidLeaveCount = 0;
					
					if(!empty($leaveBalanceInfo)){
						//echo "<pre>";print_r($leaveBalanceInfo);
						$leaveBalance = ( isset($leaveBalanceInfo->d_current_balance) ? $leaveBalanceInfo->d_current_balance : 0 );
						$minEffectativeDate = date("Y-m-d");
						/* $minEffectativeDate = null;
						$effectativeDataArray = (!empty($leaveBalanceInfo) ? array_column(objectToArray($leaveBalanceInfo), 'dt_effective_date') : [] );
						if(!empty($effectativeDataArray)){
							$minEffectativeDate = min($effectativeDataArray);
						}
						
						$totalLeaveBalance = 0;
						if(!empty($leaveBalanceInfo)){
							foreach($leaveBalanceInfo as $leaveBalance){
								if( ( $leaveBalance->d_no_of_days_assign - $leaveBalance->d_no_of_days_used ) > 0 ){
									$totalLeaveBalance += ( $leaveBalance->d_no_of_days_assign - $leaveBalance->d_no_of_days_used );
								}
							}
						}
						
						$leaveBalance = $totalLeaveBalance; */
						
						
						if( strtotime($leaveFromDate) == strtotime($leaveToDate) ){
							$leaveSummaryInfo = [];
							$leaveSummaryInfo['i_employee_id'] = $employeeId;
							$leaveSummaryInfo['i_leave_type_id'] = $leaveTypeId;
							$leaveSummaryInfo['e_leave_type'] = config('constants.DEDUCT_LEAVE');
							$leaveSummaryInfo['d_current_before_balance'] = $leaveBalance;
							$leaveSummaryInfo['d_no_of_days'] = config('constants.FULL_LEAVE_VALUE');
							if( ( in_array( $singleDateSession , [ config('constants.FIRST_HALF_LEAVE') , config('constants.SECOND_HALF_LEAVE') ] ) ) ){
								$usedBalanceValue = config('constants.HALF_LEAVE_VALUE');
								$leaveSummaryInfo['d_no_of_days'] = config('constants.HALF_LEAVE_VALUE');
							} else {
								$usedBalanceValue = config('constants.FULL_LEAVE_VALUE');
							}
							
							$getLeaveMonth = getAppliedLeaveMonth($leaveFromDate);
							
							$checkPaidLeaveStatus = $this->checkPaidLeaveApplicable( $leaveBalance , $usedBalanceValue,  $leaveFromDate , $minEffectativeDate , $probationEndDate , $allNoticePeriodDates );
							if( $checkPaidLeaveStatus != false ){
								$leaveSummaryInfo['e_leave_mode'] = config('constants.PAID_LEAVE');
								$leaveBalance -=  $usedBalanceValue;
								$totalPaidLeaveCount += $usedBalanceValue;
								if(isset($monthWiseLeaveDetails[config('constants.PAID_LEAVE')][$getLeaveMonth])){
									$monthWiseLeaveDetails[config('constants.PAID_LEAVE')][$getLeaveMonth] += $usedBalanceValue;
								} else {
									$monthWiseLeaveDetails[config('constants.PAID_LEAVE')][$getLeaveMonth] = $usedBalanceValue;
								}
							} else {
								$leaveSummaryInfo['e_leave_mode'] = config('constants.UNPAID_LEAVE');
								$totalUnPaidLeaveCount += $usedBalanceValue;
								if(isset($monthWiseLeaveDetails[config('constants.UNPAID_LEAVE')][$getLeaveMonth])){
									$monthWiseLeaveDetails[config('constants.UNPAID_LEAVE')][$getLeaveMonth] += $usedBalanceValue;
								} else {
									$monthWiseLeaveDetails[config('constants.UNPAID_LEAVE')][$getLeaveMonth] = $usedBalanceValue;
								}
							}
							$leaveSummaryInfo['d_current_after_balance'] = $leaveBalance;
							$leaveSummaryInfo['i_apply_leave_id'] = $insertRecord;
							$leaveSummaryInfo['dt_added_used_at'] = $leaveFromDate;
							//$leaveSummaryInfo = array_merge ( $this->crudModel->insertDateTimeData () , $leaveSummaryInfo );
						
							//echo "<pre>";print_r($leaveSummaryInfo);echo "<br><br>";
							//LeaveSummaryModel::create($leaveSummaryInfo);
							$this->leaveBalanceModel->insertTableData(config('constants.LEAVE_SUMMARY_TABLE'), $leaveSummaryInfo );
						
							$rowData = [];
							$rowData['date'] = $leaveFromDate;
							$rowData['count'] = $usedBalanceValue;
							$rowData['paid_status'] = false;
							$rowData['unpaid_status'] = false;
							
							//var_dump($checkPaidLeaveStatus);
							
							if( $checkPaidLeaveStatus != false ){
								$rowData['paid_status'] = true ;
							} else {
								$rowData['unpaid_status'] = true ;
							}
							$leavePaidUnpaidDayWiseInfo[] = $rowData;
						
						} else {
						
							
							if(!empty($allLeaveDates)){
								foreach($allLeaveDates as $allLeaveDate){
						
									$getLeaveMonth = getAppliedLeaveMonth($allLeaveDate);
									
									$leaveSummaryInfo = [];
									$leaveSummaryInfo['i_employee_id'] = $employeeId;
									$leaveSummaryInfo['i_leave_type_id'] = $leaveTypeId;
									$leaveSummaryInfo['dt_added_used_at'] = $allLeaveDate;
									$leaveSummaryInfo['e_leave_type'] = config('constants.DEDUCT_LEAVE');
									$leaveSummaryInfo['d_current_before_balance'] = $leaveBalance;
									$leaveSummaryInfo['i_apply_leave_id'] = $insertRecord;
									$leaveSummaryInfo['d_no_of_days'] = config('constants.FULL_LEAVE_VALUE');
						
									if( strtotime($allLeaveDate) == strtotime($leaveFromDate)  ){
										if( ( in_array( $dualDateFromSession , [  config('constants.SECOND_HALF_LEAVE') ] ) ) ) {
											$usedBalanceValue = config('constants.HALF_LEAVE_VALUE');
											$leaveSummaryInfo['d_no_of_days'] = config('constants.HALF_LEAVE_VALUE');
										} else {
											$usedBalanceValue = config('constants.FULL_LEAVE_VALUE');
										}
									} else if( strtotime($allLeaveDate) == strtotime($leaveToDate)  ){
										if( ( in_array( $dualDateToSession , [ config('constants.FIRST_HALF_LEAVE') ] ) ) ) {
											$usedBalanceValue = config('constants.HALF_LEAVE_VALUE');
											$leaveSummaryInfo['d_no_of_days'] = config('constants.HALF_LEAVE_VALUE');
										} else {
											$usedBalanceValue = config('constants.FULL_LEAVE_VALUE');
										}
									} else {
										$usedBalanceValue = config('constants.FULL_LEAVE_VALUE');
									}
						
									$checkPaidLeaveStatus = $this->checkPaidLeaveApplicable( $leaveBalance , $usedBalanceValue,  $allLeaveDate , $minEffectativeDate , $probationEndDate , $allNoticePeriodDates );
									if( $checkPaidLeaveStatus != false ){
										$leaveSummaryInfo['e_leave_mode'] = config('constants.PAID_LEAVE');
										$leaveBalance -=  $usedBalanceValue;
										$totalPaidLeaveCount += $usedBalanceValue;
										
										if(isset($monthWiseLeaveDetails[config('constants.PAID_LEAVE')][$getLeaveMonth])){
											$monthWiseLeaveDetails[config('constants.PAID_LEAVE')][$getLeaveMonth] += $usedBalanceValue;
										} else {
											$monthWiseLeaveDetails[config('constants.PAID_LEAVE')][$getLeaveMonth] = $usedBalanceValue;
										}
										
									} else {
										$leaveSummaryInfo['e_leave_mode'] = config('constants.UNPAID_LEAVE');
										$totalUnPaidLeaveCount += $usedBalanceValue;
										
										if(isset($monthWiseLeaveDetails[config('constants.UNPAID_LEAVE')][$getLeaveMonth])){
											$monthWiseLeaveDetails[config('constants.UNPAID_LEAVE')][$getLeaveMonth] += $usedBalanceValue;
										} else {
											$monthWiseLeaveDetails[config('constants.UNPAID_LEAVE')][$getLeaveMonth] = $usedBalanceValue;
										}
									}
						
									$leaveSummaryInfo['d_current_after_balance'] = $leaveBalance;
									
									//echo "<pre>";print_r($leaveSummaryInfo);
									//echo "<pre>";print_r($monthWiseLeaveDetails);
									
									$this->leaveBalanceModel->insertTableData(config('constants.LEAVE_SUMMARY_TABLE'), $leaveSummaryInfo );
									
									$rowData = [];
									$rowData['date'] = $allLeaveDate;
									$rowData['count'] = $usedBalanceValue;
									$rowData['paid_status'] = false;
									$rowData['unpaid_status'] = false;
										
									if( $checkPaidLeaveStatus != false ){
										$rowData['paid_status'] = true ;
									} else {
										$rowData['unpaid_status'] = true ;
									}
									$leavePaidUnpaidDayWiseInfo[] = $rowData;
									
									//$leaveSummaryInfo = array_merge ( $this->crudModel->insertDateTimeData () , $leaveSummaryInfo );
						
									//echo "<pre>";print_r($leaveSummaryInfo);echo "<br><br>";
									
									//LeaveSummaryModel::create($leaveSummaryInfo);
						
								}
							}
						}
						
						//echo "<pre>";print_r($monthWiseLeaveDetails);die;
						
						$leaveBalanceUpdateData = [];
						$leaveBalanceUpdateData['d_paid_leave_count'] = DB::raw("CONCAT(d_paid_leave_count+".$totalPaidLeaveCount.")");
						$leaveBalanceUpdateData['d_unpaid_leave_count'] = DB::raw("CONCAT(d_unpaid_leave_count+".$totalUnPaidLeaveCount.")");
						
						if( $leaveBalance > 0 ){
							$leaveBalanceUpdateData['d_current_balance'] =  $leaveBalance;
						} else {
							$leaveBalanceUpdateData['d_current_balance'] =  0;
						}
						//$leaveBalanceUpdateData = array_merge ( $this->crudModel->updateDateTimeData () , $leaveBalanceUpdateData );
						//echo "<pre>";print_r($leaveBalanceInfo);
						//die("ddd");
						LeaveBalanceModel::where('i_id' , $leaveBalanceInfo->i_id )->update($leaveBalanceUpdateData);  
					}
					//echo "<pre>";print_r($monthWiseLeaveDetails);
					if(!empty($monthWiseLeaveDetails)){
						foreach($monthWiseLeaveDetails as $monthWiseLeavePaidUnpiadType =>  $monthWiseLeaveDetail){
							if(!empty($monthWiseLeaveDetail)){
								foreach($monthWiseLeaveDetail as $monthWiseLeaveMonthName => $monthWiseLeaveValue){
									$checkAttendanceSummaryWhere = [];
									$checkAttendanceSummaryWhere['dt_month'] = $monthWiseLeaveMonthName;
									$checkAttendanceSummaryWhere['i_employee_id'] = $employeeId;
									$checkAttendanceSummaryWhere['t_is_deleted'] = 0;
									$checkEntryExists = AttendanceSummaryModel::where($checkAttendanceSummaryWhere)->first();
									
									$summaryCountInfo = [];
									
									if( $monthWiseLeavePaidUnpiadType == config('constants.UNPAID_LEAVE')){
										$summaryCountInfo['d_unpaid_leave_count'] = DB::raw("d_unpaid_leave_count + " . $monthWiseLeaveValue );
									} else if ( $monthWiseLeavePaidUnpiadType == config('constants.PAID_LEAVE')){
										$summaryCountInfo['d_paid_leave_count'] = DB::raw("d_paid_leave_count + " . $monthWiseLeaveValue );
									}
									
									if(!empty($checkEntryExists)){
										$this->crudModel->updateTableData(config('constants.ATTENDANCE_SUMMARY_TABLE'), $summaryCountInfo , [ 'i_id' => $checkEntryExists->i_id  ] );
										
										//echo $this->crudModel->last_query();echo "<br><br>";
										
									} else {
										$summaryCountInfo['dt_month'] = $monthWiseLeaveMonthName;
										$summaryCountInfo['i_employee_id'] = $employeeId;
										$this->crudModel->insertTableData(config('constants.ATTENDANCE_SUMMARY_TABLE'), $summaryCountInfo);
										
										//echo $this->crudModel->last_query();echo "<br><br>";
										
									}
									
								}
							}
							
						}
					}
					
					$usedBalanceIds = [];
					$updateUsedBalanceDatas  = [];
					if( in_array(  $leaveTypeId , [ config('constants.PAID_LEAVE_TYPE_ID') ] )  ){
						$totalNeededLeave = $totalPaidLeaveCount;
						$getPaidLeaveBalance = [];
						$getPaidLeaveBalance['t_is_deleted'] = 0;
						$getPaidLeaveBalance['i_employee_id'] = $employeeId;
						$getPaidLeaveBalance['i_leave_type_id'] = $leaveTypeId;
						$getAllAssignPaidLeaveBalanceDetails = LeaveAssignHistoryModel::where($getPaidLeaveBalance)->whereRaw(' ( d_no_of_days_assign - d_no_of_days_used ) > 0')->orderBy('dt_effective_date' , 'asc')->get();
						
						if(!empty($getAllAssignPaidLeaveBalanceDetails)){
							//echo "dd";
							
							foreach($getAllAssignPaidLeaveBalanceDetails as $getAllAssignPaidLeaveBalanceDetail){
								if( $totalNeededLeave > 0 ){
									if(  ( $getAllAssignPaidLeaveBalanceDetail->d_no_of_days_assign - $getAllAssignPaidLeaveBalanceDetail->d_no_of_days_used ) >= $totalNeededLeave  ){
										//echo "iff";echo "<br><br>";
										$rowupdateUsedBalanceInfo  = [];
										$rowupdateUsedBalanceInfo['i_id'] = $getAllAssignPaidLeaveBalanceDetail->i_id;
										$rowupdateUsedBalanceInfo['v_used_leave_ids'] = $getAllAssignPaidLeaveBalanceDetail->v_used_leave_ids;
										$rowupdateUsedBalanceInfo['used_balance'] = $totalNeededLeave ;
										$updateUsedBalanceDatas[] = $rowupdateUsedBalanceInfo;
										$totalNeededLeave = 0;
									} else {
										//echo "else";echo "<br><br>";
										$totalNeededLeave = $totalNeededLeave - ( $getAllAssignPaidLeaveBalanceDetail->d_no_of_days_assign - $getAllAssignPaidLeaveBalanceDetail->d_no_of_days_used );
										$rowupdateUsedBalanceInfo  = [];
										$rowupdateUsedBalanceInfo['i_id'] = $getAllAssignPaidLeaveBalanceDetail->i_id;
										$rowupdateUsedBalanceInfo['v_used_leave_ids'] = $getAllAssignPaidLeaveBalanceDetail->v_used_leave_ids;
										$rowupdateUsedBalanceInfo['used_balance'] = ( $getAllAssignPaidLeaveBalanceDetail->d_no_of_days_assign - $getAllAssignPaidLeaveBalanceDetail->d_no_of_days_used ) ;
										$updateUsedBalanceDatas[] = $rowupdateUsedBalanceInfo;
										
									}
								}
							}
							
							if(!empty($updateUsedBalanceDatas)){
								foreach($updateUsedBalanceDatas as $updateUsedBalanceDataId =>  $updateUsedBalanceData){
									$usedBalanceIds[] = $updateUsedBalanceData['i_id']; 
									$updateBalanceInfo = [];
									
									$updateBalanceInfo['d_no_of_days_used'] = DB::raw('d_no_of_days_used + ' . $updateUsedBalanceData['used_balance'] ) ;
									$updateBalanceInfo['v_used_leave_ids'] = (!empty($updateUsedBalanceData['v_used_leave_ids']) ? $updateUsedBalanceData['v_used_leave_ids'] .',' . $leaveRecordId  : $leaveRecordId ) ;
									LeaveAssignHistoryModel::where('i_id', $updateUsedBalanceData['i_id'] )->update( $updateBalanceInfo );
								}
							}
						}
						//echo "<pre>";print_r($getAllAssignPaidLeaveBalanceDetails);die;
					}
					
				//	var_dump($totalUnPaidLeaveCount);
				//	var_dump($totalPaidLeaveCount);die;
					
					
					
					$updateLeaveInfo = [];
					$updateLeaveInfo['d_no_of_unpaid_leave'] = $totalUnPaidLeaveCount;
					$updateLeaveInfo['d_no_of_paid_leave'] = $totalPaidLeaveCount;
					$updateLeaveInfo['v_leave_summary'] = (!empty($leavePaidUnpaidDayWiseInfo) ? json_encode($leavePaidUnpaidDayWiseInfo) : null );
					$updateLeaveInfo['v_month_wise_count'] = (!empty($monthWiseLeaveDetails) ? json_encode($monthWiseLeaveDetails) : null );
					
					if( in_array(  $leaveTypeId , [ config('constants.PAID_LEAVE_TYPE_ID') ] )  ){
						$updateLeaveInfo['v_used_balance_ids'] = (!empty($usedBalanceIds) ? implode("," , $usedBalanceIds ) : null );
						$updateLeaveInfo['v_used_balance_info'] = (!empty($updateUsedBalanceDatas) ? json_encode($updateUsedBalanceDatas) : null );
					}
					//echo "<pre>";print_r($updateLeaveInfo);die;
					//dd($updateLeaveInfo);
					$this->crudModel->updateTableData(config('constants.APPLY_LEAVE_MASTER_TABLE'), $updateLeaveInfo , [ 'i_id' => $insertRecord  ] );
					
					$this->sendLeaveMail($insertRecord);
					
					//echo "<pre>";print_r($applyLeaveEmployeeInfo);
					
					//die("welcome");
					
					if($insertRecord > 0){
						$result = true;
					}
				}
			}catch(\Exception $e){
				var_dump($e->getMessage());die;
				errorLogEntry('leave added' , $e->getMessage());
			}
			
			if($result != false){
				//die("welcome");
				DB::commit();
				$this->ajaxResponse(1, $successMessage);
			}
			DB::rollback();
			$this->ajaxResponse(101, $errorMessages);
		}
	}
	
	private function checkPaidLeaveApplicable(  $leaveBalance , $usedBalance,  $leaveDate , $effectiveDate ,  $probationEndDate , $noticePreiodDate){
		$paidLeaveStatus = "";
		/* var_dump($probationEndDate);echo "<br><br>";
		var_dump($leaveDate);echo "<br><br>";
		var_dump($noticePreiodDate);echo "<br><br>";
		var_dump($effectiveDate);echo "<br><br>";
		var_dump($leaveBalance);echo "<br><br>";
		var_dump($usedBalance);echo "<br><br>"; */
		
		
		if( $leaveBalance >= $usedBalance ){
			//echo "333";echo "<br><br>";
			$paidLeaveStatus = true;
		} else {
			//echo "444";echo "<br><br>";
			$paidLeaveStatus = false;
		}
		
		return $paidLeaveStatus;
		if( (!empty($probationEndDate)) && (strtotime($probationEndDate) > strtotime($leaveDate))){
			//echo "111";echo "<br><br>";
			$paidLeaveStatus = false;
		} else {
			if( (!empty($noticePreiodDate)) && in_array( $leaveDate , $noticePreiodDate) ){
				//echo "222";echo "<br><br>";
				$paidLeaveStatus = false;
			} else {
				/* if(strtotime($effectiveDate) <= strtotime($leaveDate)){ */
					if( $leaveBalance >= $usedBalance ){
						//echo "333";echo "<br><br>";
						$paidLeaveStatus = true;
					} else {
						//echo "444";echo "<br><br>";
						$paidLeaveStatus = false;
					}
				/* } else {
					//echo "555";echo "<br><br>";
					$paidLeaveStatus = false;
				} */
				
				
			}
		}
		return $paidLeaveStatus;
	}
	
	public function getLeaveCalendar(Request $request){
		if(!empty($request->post())){
			
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : session()->get('user_employee_id') );
			$selectedMonth = (!empty($request->input('selected_month')) ? $request->input('selected_month') : date("Y-m-d") );
			
			$employeeInfo = EmployeeModel::where('i_id' , $employeeId )->first();
			$employeeTeamId = ( isset($employeeInfo->i_team_id) ? $employeeInfo->i_team_id : null );
			
			$calendarDetails = $this->getCalendarLeaveDetail( ['employee_team_id' => $employeeTeamId , 'employee_id' => $employeeId ,   'month' => $selectedMonth ]  );
			
			//echo "<pre>calendarDetails";print_r($calendarDetails);die;
			
			$data['weekOffDates'] = ( isset($calendarDetails['employeeWeekOffDates']) ? $calendarDetails['employeeWeekOffDates'] : [] );
			$data['teamLeaveDetails'] = ( isset($calendarDetails['teamLeaveDetails']) ? $calendarDetails['teamLeaveDetails'] : [] );
			$data['appliedLeaveDates'] = ( isset($calendarDetails['appliedLeaveDates']) ? $calendarDetails['appliedLeaveDates'] : [] );
			$data['holidayDetails'] = ( isset($calendarDetails['holidayDetails']) ? $calendarDetails['holidayDetails'] : [] );
			$data['calendarStartDate'] = monthStartDate($selectedMonth);
			
			$response['leaveCalendarHtml'] = view ($this->folderName . 'leave-calendar')->with ( $data )->render();
			$this->ajaxResponse(1, trans('messages.success') , $response );
		}
	}
	
	public function addLeaveBalance(Request $request){
		
		if(!empty($request->input())){
			
			$noOfLeave = (!empty($request->input('no_of_earned_leaves')) ? $request->input('no_of_earned_leaves') : 0 );
			$leaveRemark = (!empty($request->input('leave_balance_remark')) ? $request->input('leave_balance_remark') : null );
			$effectiveDate = (!empty($request->input('effective_date')) ? dbDate( $request->input('effective_date') ) : null );
			$leaveTypeId = (!empty($request->input('leave_type_id')) ? (int)( $request->input('leave_type_id') ) : 0 );
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : session()->get('user_employee_id') );
			
			
			if( (!empty($leaveTypeId)) && (!empty($employeeId)) &&  (!empty($noOfLeave)) && (!empty($leaveRemark)) ){
			
				/* if( ( abs($noOfLeave) % 0.5 == 0 ) == false ){
					$this->ajaxResponse(101, trans('messages.messages.invalid-leave-balance-value'  ) );
				} */
				
				
				
				$academicYear = (!empty($request->post('search_academic_year')) ? trim($request->post('search_academic_year')) : date("Y") );
				
				$leaveBalanceData = [];
				$leaveBalanceData['i_employee_id'] = $employeeId;
				$leaveBalanceData['i_leave_type_id'] = $leaveTypeId;
				$leaveBalanceData['dt_effective_date'] = $effectiveDate;
				$leaveBalanceData['d_no_of_days_assign'] = $noOfLeave;
				$leaveBalanceData['v_remark'] = $leaveRemark;
				
				DB::beginTransaction();
				$result = false;
				$html = "";
				try{
					
					$insertLeaveASsign = $this->leaveBalanceModel->insertTableData(config('constants.LEAVE_ASSIGN_HISTORY_TABLE'), $leaveBalanceData );
					
					$leaveBalanceWhere  = [];
					$leaveBalanceWhere['i_employee_id'] = $employeeId;
					$leaveBalanceWhere['i_leave_type_id'] = $leaveTypeId;
					$leaveBalanceWhere['t_is_deleted != '] = 1;
					$checkLeaveAssigned = $this->leaveBalanceModel->getSingleRecordById(config('constants.LEAVE_BALANCE_TABLE') , [ 'i_id' , 'd_current_balance' ] ,  $leaveBalanceWhere );
					
					if(!empty($checkLeaveAssigned)){
						
						if( ( $noOfLeave < 0 ) && ( $checkLeaveAssigned->d_current_balance < abs($noOfLeave) ) ){
							DB::rollback();
							$this->ajaxResponse(101, trans('messages.error-minus-leave-balance'  ) );
						}
						
						$updateLeaveBalance = [];
						$updateLeaveBalance['d_current_balance'] = DB::raw("CONCAT(d_current_balance+".$noOfLeave.")");
						$this->leaveBalanceModel->updateTableData(config('constants.LEAVE_BALANCE_TABLE'), $updateLeaveBalance , [ 'i_id' =>  $checkLeaveAssigned->i_id  ] );
						
					} else {
						$insertLeaveBalance = [];
						$insertLeaveBalance['i_employee_id'] = $employeeId;
						$insertLeaveBalance['i_leave_type_id'] = $leaveTypeId;
						$insertLeaveBalance['d_current_balance'] = $noOfLeave;
						
						$this->leaveBalanceModel->insertTableData(config('constants.LEAVE_BALANCE_TABLE'), $insertLeaveBalance );
					}
					
					$recordInfo = [];
					
					$startDate = getYearStartDate($academicYear);
					$endDate = getYearEndDate($academicYear);
					
					$getLeaveSummary = $this->getMonthWiseWeekWiseLeaveCount( $employeeId , $startDate , $endDate  );
					$recordInfo['leaveConsumeInfo'] = $getLeaveSummary['leaveConsumeInfo'];
					$recordInfo['leaveAvailableInfo'] = $getLeaveSummary['leaveAvailableInfo'];
					$recordInfo['employeeId'] = Wild_tiger::encode($employeeId);
					$recordInfo['leaveTypeDetail'] = LeaveTypeMasterModel::where('i_id' , $leaveTypeId)->first();
					$html = view (config('constants.AJAX_VIEW_FOLDER') . 'my-leaves/leave-type-count-chart')->with ( $recordInfo )->render();
					
					$result = true;
				}catch(\Exception $e){
					//var_dump($e->getMessage());die;
					DB::rollback();
					$result = false;
				}
				
				if($result != false ){
					//die("www");
					DB::commit();
					$this->ajaxResponse(1, trans('messages.success-create' , [ 'module' => trans('messages.leave-balance') ] ) , [  'html' => $html ] );
				} else {
					DB::rollback();
					$this->ajaxResponse(101, trans('messages.error-create' , [ 'module' => trans('messages.leave-balance') ] ) );
				}
			}
			$this->ajaxResponse(101, trans('message.system-error' ) );
			
		}
	}
	
	public function updateLeaveStatus(Request $request){
		
		if(!empty($request->input())){
			
			$leaveRecordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0 );
			$status = (!empty($request->post('status')) ? trim($request->post('status')) : null );
			
			if(!empty($status) && (!empty($leaveRecordId))){
				
				$getLeaveRecordWhere = [];
				$getLeaveRecordWhere['master_id'] = $leaveRecordId;
				$getLeaveRecordWhere['singleRecord'] = true;
				if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){
					$getLeaveRecordWhere['show_all'] = true;
				}
				$getLeaveRecordInfo = $this->crudModel->getRecordDetails($getLeaveRecordWhere);
				
				if( (!empty($getLeaveRecordInfo)) ){
					if( $status == config("constants.APPROVED_STATUS") ){
						if( !in_array( $getLeaveRecordInfo->e_status , [ config("constants.PENDING_STATUS") ] ) ){
							$this->ajaxResponse(101, trans('messages.error-invalid-leave-status' , [ 'status' =>  $getLeaveRecordInfo->e_status ] ) );
						}
					} else if( $status == config("constants.REJECTED_STATUS") ){
						if( !in_array( $getLeaveRecordInfo->e_status , [ config("constants.PENDING_STATUS") ] ) ){
							$this->ajaxResponse(101, trans('messages.error-invalid-leave-status' , [ 'status' =>  $getLeaveRecordInfo->e_status ] ) );
						}
					} else if( $status == config("constants.CANCELLED_STATUS") ){
						if( !in_array( $getLeaveRecordInfo->e_status , [ config("constants.PENDING_STATUS") , config("constants.APPROVED_STATUS") ] ) ){
							$this->ajaxResponse(101, trans('messages.error-invalid-leave-status' , [ 'status' =>  $getLeaveRecordInfo->e_status ] ) );
						}
					} 
					
					DB::beginTransaction();
					
					$updateLeaveData = [];
					$updateLeaveData['e_status'] = $status;
					$updateLeaveData['v_approve_reject_remark'] = (!empty($request->input('leave_approve_reject_reason')) ? $request->input('leave_approve_reject_reason') : null );
					$updateLeaveData['i_approved_by_id'] = session()->get('user_id');
					$updateLeaveData['dt_approved_at'] = date("Y-m-d H:i:s");
					
					$result = true;
					$html = null;
					try{
						
						$this->crudModel->updateTableData(config('constants.APPLY_LEAVE_MASTER_TABLE'), $updateLeaveData , [ 'i_id' =>  $leaveRecordId  ] );
						
						$leavegetWhere = [];
						$leavegetWhere['master_id'] = $leaveRecordId;
						$leavegetWhere['singleRecord'] = true;
						if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){
							$leavegetWhere['show_all'] = true;
						}
						
						$recordDetail = $this->crudModel->getRecordDetails( $leavegetWhere  );
						
						if( in_array( $status , [  config("constants.CANCELLED_STATUS") , config("constants.REJECTED_STATUS") ] ) ){
							
							if(isset($recordDetail->d_no_of_paid_leave)  && ( $recordDetail->d_no_of_paid_leave > 0 )){
								$leaveBalanceInfo  = [];
								$leaveBalanceInfo['d_current_balance'] = DB::raw("d_current_balance + " . $recordDetail->d_no_of_paid_leave );
								$this->crudModel->updateTableData(config('constants.LEAVE_BALANCE_TABLE'), $leaveBalanceInfo , [ 'i_employee_id' => $recordDetail->i_employee_id ,  'i_leave_type_id' => $recordDetail->i_leave_type_id  ] );
							}
							
							if(isset($recordDetail->v_month_wise_count) && (!empty($recordDetail->v_month_wise_count))){
								$monthWiseCountDetails = json_decode($recordDetail->v_month_wise_count,true);
								if(!empty($monthWiseCountDetails)){
									foreach($monthWiseCountDetails as $monthWiseCountType =>  $monthWiseCountDetail){
										if(!empty($monthWiseCountDetail)){
											foreach($monthWiseCountDetail as $monthWiseLeaveMonthName => $monthWiseLeaveValue){
												if( $monthWiseCountType == config('constants.UNPAID_LEAVE')){
													$summaryCountInfo['d_unpaid_leave_count'] = DB::raw("d_unpaid_leave_count - " . $monthWiseLeaveValue );
												} else if ( $monthWiseCountType == config('constants.PAID_LEAVE')){
													$summaryCountInfo['d_paid_leave_count'] = DB::raw("d_paid_leave_count - " . $monthWiseLeaveValue );
												}
												$this->crudModel->updateTableData(config('constants.ATTENDANCE_SUMMARY_TABLE'), $summaryCountInfo , [ 'i_employee_id' => $recordDetail->i_employee_id ,  'dt_month' => $monthWiseLeaveMonthName  ] );
											}
										}
									}
								}
								
							}
							$this->crudModel->deleteTableData(config('constants.LEAVE_SUMMARY_TABLE'), [ 't_is_active' => 0 , 't_is_deleted' => 1 ] , [ 'i_apply_leave_id' => $recordDetail->i_id  ] );
							
							if(isset($recordDetail->i_leave_type_id) && ( $recordDetail->i_leave_type_id == config('constants.PAID_LEAVE_TYPE_ID') ) && (!empty($recordDetail->v_used_balance_info))){
								$usedBalanceDetails = json_decode($recordDetail->v_used_balance_info,true);
								if(!empty($usedBalanceDetails)){
									foreach($usedBalanceDetails as $usedBalanceDetail){
										$updateBalanceInfo = [];
										$updateBalanceInfo['d_no_of_days_used'] = DB::raw('d_no_of_days_used - ' . $usedBalanceDetail['used_balance'] ) ;
										//$updateBalanceInfo['v_used_leave_ids'] = (!empty($updateUsedBalanceData['v_used_leave_ids']) ? $updateUsedBalanceData['v_used_leave_ids'] .',' . $leaveRecordId  : $leaveRecordId ) ;
										LeaveAssignHistoryModel::where('i_id', $usedBalanceDetail['i_id'] )->update( $updateBalanceInfo );
									}
								}
							}
							
							
							
						}
						
						$recordInfo = [];
						$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
						$recordInfo['recordDetail'] = $recordDetail;
						
						$html = view (config('constants.AJAX_VIEW_FOLDER') . 'leave-report/single-leave-report-list')->with ( $recordInfo )->render();
							
						/* if( $getLeaveRecordInfo->d_no_of_paid_leave > 0  ){
							$updateLeaveBalance = [];
							$updateLeaveBalance['d_current_balance'] = DB::raw("CONCAT(d_current_balance+".$getLeaveRecordInfo->d_no_of_paid_leave.")");
							$this->crudModel->updateTableData(config('constants.LEAVE_BALANCE_TABLE'), $updateLeaveBalance , [ 'i_leave_type_id' =>  $getLeaveRecordInfo->i_leave_type_id , 'i_employee_id'   => $getLeaveRecordInfo->i_employee_id  ] );
						} */
						
						$result = true;
						
					}catch(\Exception $e){
						
						DB::rollback();
						$result = false;
					}
					
					if($result != false ){
						$this->sendLeaveMail($leaveRecordId, config('constants.ACTION_LEAVE') );
						DB::commit();
						$this->ajaxResponse(1, trans('messages.success-update' , [ 'module' => trans('messages.leave') . ' ' .$status . ' ' . trans('messages.status') ] ),['html' => $html] );
					} else {
						DB::rollback();
						$this->ajaxResponse(101, trans('messages.error-update' , [ 'module' => trans('messages.leave') . ' ' .$status. ' '.trans('messages.status') ] ) );
					}
				}
			}
			$this->ajaxResponse(101, trans('messages.system-error') );
			
			
		}
		
	}
	
	
	private function getMonthWiseWeekWiseLeaveCount($employeeId , $startDate , $endDate ){
		$leaveCommonWhere = [];
		$leaveCommonWhere['leave_from_date'] = $startDate;
		$leaveCommonWhere['leave_to_date'] = $endDate;
			
		$leaveWhere = [];
		$leaveWhere = $leaveCommonWhere;
		$leaveWhere['employee_id'] = $employeeId;
		
		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ){
			$leaveWhere['show_all'] = true;
		}
		
		$leaveWhere['leave_status'] = [ config("constants.APPROVED_STATUS") , config("constants.PENDING_STATUS")  ];
		$getLeaveDetils = $this->crudModel->getRecordDetails($leaveWhere);
			
		$weekDayWiseCount = [];
		$weekDayWiseCount["Monday"] = 0;
		$weekDayWiseCount["Tuesday"] = 0;
		$weekDayWiseCount["Wednesday"] = 0;
		$weekDayWiseCount["Thursday"] = 0;
		$weekDayWiseCount["Friday"] = 0;
		$weekDayWiseCount["Saturday"] = 0;
		$weekDayWiseCount["Sunday"] = 0;
			
		$monthWiseCount = [];
		for($i = 1 ; $i <= 12 ; $i++ ){
			$monthWiseCount[date("F" ,strtotime(date("Y-". $i."-01")))] = 0;
		}
		$leaveConsumeInfo = [];
		$leaveConsumeInfo[config("constants.EARNED_LEAVE_TYPE_ID")] = 0;
		$leaveConsumeInfo[config("constants.CARRY_FORWARD_LEAVE_TYPE_ID")] = 0;
		$leaveConsumeInfo[config("constants.PAID_LEAVE_TYPE_ID")] = 0;
		$leaveConsumeInfo[config("constants.UNPAID_LEAVE_TYPE_ID")] = 0;
		
		$leaveAvailableInfo = [];
		$leaveAvailableInfo[config("constants.EARNED_LEAVE_TYPE_ID")] = 0;
		$leaveAvailableInfo[config("constants.CARRY_FORWARD_LEAVE_TYPE_ID")] = 0;
		$leaveAvailableInfo[config("constants.PAID_LEAVE_TYPE_ID")] = 0;
		$leaveAvailableInfo[config("constants.UNPAID_LEAVE_TYPE_ID")] = 0;
		
		
		//echo "<pre>";print_r($monthWiseCount);die;
		if(!empty($getLeaveDetils)){
			foreach($getLeaveDetils as $getLeaveDetil){
				$leaveConsumeInfo[$getLeaveDetil->i_leave_type_id] += $getLeaveDetil->d_no_days;
				
				if( $getLeaveDetil->e_status == config("constants.APPROVED_STATUS") ){
					if( $getLeaveDetil->d_no_days > config("constants.FULL_LEAVE_VALUE") ){
						$allLeaveDates = getDatesFromRange( $getLeaveDetil->dt_leave_from_date , $getLeaveDetil->dt_leave_to_date );
						if(!empty($allLeaveDates)){
							foreach($allLeaveDates as $allLeaveDate){
								$leaveWeekDay = date("l" , strtotime($allLeaveDate) );
								if(!empty($leaveWeekDay)){
									
									if( ( strtotime($allLeaveDate) ==  strtotime($getLeaveDetil->dt_leave_from_date) ) || ( strtotime($allLeaveDate) ==  strtotime($getLeaveDetil->dt_leave_to_date) )  ){
										if( ( strtotime($allLeaveDate) ==  strtotime($getLeaveDetil->dt_leave_from_date) ) && ( $getLeaveDetil->e_from_duration == config("constants.SECOND_HALF_LEAVE") ) ){
											$weekDayWiseCount[$leaveWeekDay] += config("constants.HALF_LEAVE_VALUE");
										} else if( ( strtotime($allLeaveDate) ==  strtotime($getLeaveDetil->dt_leave_to_date) ) && ( $getLeaveDetil->e_to_duration == config("constants.FIRST_HALF_LEAVE") ) ){
											$weekDayWiseCount[$leaveWeekDay] += config("constants.HALF_LEAVE_VALUE");
										} else {
											$weekDayWiseCount[$leaveWeekDay] += config("constants.FULL_LEAVE_VALUE");
										}
									} else {
										$weekDayWiseCount[$leaveWeekDay] += config("constants.FULL_LEAVE_VALUE");
									}
									
								}
								$leaveMonth = date("F" , strtotime($allLeaveDate) );
								if(!empty($leaveMonth)){
									
									if( ( strtotime($allLeaveDate) ==  strtotime($getLeaveDetil->dt_leave_from_date) ) || ( strtotime($allLeaveDate) ==  strtotime($getLeaveDetil->dt_leave_to_date) )  ){
										if( ( strtotime($allLeaveDate) ==  strtotime($getLeaveDetil->dt_leave_from_date) ) && ( $getLeaveDetil->e_from_duration == config("constants.SECOND_HALF_LEAVE") ) ){
											$monthWiseCount[$leaveMonth] += config("constants.HALF_LEAVE_VALUE");
										} else if( ( strtotime($allLeaveDate) ==  strtotime($getLeaveDetil->dt_leave_to_date) ) && ( $getLeaveDetil->e_to_duration == config("constants.FIRST_HALF_LEAVE") ) ){
											$monthWiseCount[$leaveMonth] += config("constants.HALF_LEAVE_VALUE");
										} else {
											$monthWiseCount[$leaveMonth] += config("constants.FULL_LEAVE_VALUE");
										}
									} else {
										$monthWiseCount[$leaveMonth] += config("constants.FULL_LEAVE_VALUE");
									}
									
									//$monthWiseCount[$leaveMonth] += config("constants.FULL_LEAVE_VALUE");
								}
							}
						}
					} else {
						if( $getLeaveDetil->d_no_days == config("constants.HALF_LEAVE_VALUE") ){
							$leaveWeekDay = date("l" , strtotime($getLeaveDetil->dt_leave_from_date) );
							
							if(!empty($leaveWeekDay)){
								$weekDayWiseCount[$leaveWeekDay] += config("constants.HALF_LEAVE_VALUE");
							}
							$leaveMonth = date("F" , strtotime($getLeaveDetil->dt_leave_from_date) );
							if(!empty($leaveMonth)){
								$monthWiseCount[$leaveMonth] += config("constants.HALF_LEAVE_VALUE");
							}
						} else {
							if( strtotime($getLeaveDetil->dt_leave_from_date) ==  strtotime($getLeaveDetil->dt_leave_to_date) ){
								$leaveWeekDay = date("l" , strtotime($getLeaveDetil->dt_leave_from_date) );
								
								if(!empty($leaveWeekDay)){
									$weekDayWiseCount[$leaveWeekDay] += config("constants.FULL_LEAVE_VALUE");
								}
								$leaveMonth = date("F" , strtotime($getLeaveDetil->dt_leave_from_date) );
								if(!empty($leaveMonth)){
									$monthWiseCount[$leaveMonth] += config("constants.FULL_LEAVE_VALUE");
								}
							} else {
			
							if(  in_array( $getLeaveDetil->e_from_duration , [ config("constants.FIRST_HALF_LEAVE") ,  config("constants.SECOND_HALF_LEAVE") ] ) ){
									if( $getLeaveDetil->e_from_duration == config("constants.SECOND_HALF_LEAVE") ){
										$leaveWeekDay = date("l" , strtotime($getLeaveDetil->dt_leave_from_date) );
											
										if(!empty($leaveWeekDay)){
											$weekDayWiseCount[$leaveWeekDay] += config("constants.HALF_LEAVE_VALUE");
										}
										$leaveMonth = date("F" , strtotime($getLeaveDetil->dt_leave_from_date) );
										if(!empty($leaveMonth)){
											$monthWiseCount[$leaveMonth] += config("constants.HALF_LEAVE_VALUE");
										}
									} else {
										$leaveWeekDay = date("l" , strtotime($getLeaveDetil->dt_leave_from_date) );
											
										if(!empty($leaveWeekDay)){
											$weekDayWiseCount[$leaveWeekDay] += config("constants.FULL_LEAVE_VALUE");
										}
										$leaveMonth = date("F" , strtotime($getLeaveDetil->dt_leave_from_date) );
										if(!empty($leaveMonth)){
											$monthWiseCount[$leaveMonth] += config("constants.FULL_LEAVE_VALUE");
										}
									}
									
									
								}
								if(  in_array( $getLeaveDetil->e_to_duration , [ config("constants.FIRST_HALF_LEAVE") ,  config("constants.SECOND_HALF_LEAVE") ] ) ){
									
									if( $getLeaveDetil->e_to_duration == config("constants.FIRST_HALF_LEAVE") ){
										$leaveWeekDay = date("l" , strtotime($getLeaveDetil->dt_leave_to_date) );
											
										if(!empty($leaveWeekDay)){
											$weekDayWiseCount[$leaveWeekDay] += config("constants.HALF_LEAVE_VALUE");
										}
										$leaveMonth = date("F" , strtotime($getLeaveDetil->dt_leave_to_date) );
										if(!empty($leaveMonth)){
											$monthWiseCount[$leaveMonth] += config("constants.HALF_LEAVE_VALUE");
										}
									} else {
										$leaveWeekDay = date("l" , strtotime($getLeaveDetil->dt_leave_to_date) );
											
										if(!empty($leaveWeekDay)){
											$weekDayWiseCount[$leaveWeekDay] += config("constants.FULL_LEAVE_VALUE");
										}
										$leaveMonth = date("F" , strtotime($getLeaveDetil->dt_leave_to_date) );
										if(!empty($leaveMonth)){
											$monthWiseCount[$leaveMonth] += config("constants.FULL_LEAVE_VALUE");
										}
									}
									
									
								}
			
							}
						}
						
					}
				}
			}
		}
			
		$leaveAssignWhere = [];
		$leaveAssignWhere['i_employee_id'] = $employeeId;
		$leaveAssignWhere['custom_function'][] = "dt_effective_date  between '".$startDate."'  and '".$endDate."'";
			
		$leaveAssignDetails = $this->leaveAssignHistoryModel->getRecordDetails($leaveAssignWhere);
		$leaveBalanceDetails = LeaveBalanceModel::where('t_is_deleted' , 0 )->where('i_employee_id' , $employeeId )->get();
		if(!empty($leaveAssignDetails)){
			foreach($leaveAssignDetails as $leaveAssignDetail){
				//$leaveAvailableInfo[$leaveAssignDetail->i_leave_type_id] += $leaveAssignDetail->d_no_of_days_assign;
			}
		}
		
		if(!empty($leaveBalanceDetails)){
			foreach($leaveBalanceDetails as $leaveBalanceDetail){
				$leaveAvailableInfo[$leaveBalanceDetail->i_leave_type_id] += $leaveBalanceDetail->d_current_balance;
			}
		}
		//echo "<pre>";print_r($monthWiseCount);die;
		$response = [];
		$response['weekDayWiseCount'] = $weekDayWiseCount;
		$response['monthWiseCount'] = $monthWiseCount;
		$response['leaveConsumeInfo'] = $leaveConsumeInfo;
		$response['leaveAvailableInfo'] = $leaveAvailableInfo;
		return $response;
		
	}
	
	
	public function filterLeaveDashboard(Request $request){
		
		if(!empty($request->input())){ 
			
			$academicYear =  (!empty($request->input('academic_year')) ? trim($request->input('academic_year')) : date("Y") );
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : session()->get('user_employee_id') );
			
			$startDate = getYearStartDate($academicYear);
			$endDate = getYearEndDate($academicYear);
			
			$data['yearDetails'] = yearDetails();
			
			$data['currentYear'] = $academicYear;
			$data['startDate'] = $startDate;
			$data['endDate'] = $endDate;
			$data['leaveCountDetails'] = $this->getLeaveCountInfo( $employeeId , $startDate , $endDate  );
			
			$getLeaveSummary = $this->getMonthWiseWeekWiseLeaveCount( $employeeId , $startDate , $endDate  );
			$data['weekDayWiseCount'] = $getLeaveSummary['weekDayWiseCount'];
			$data['monthWiseCount'] = $getLeaveSummary['monthWiseCount'];
			$data['leaveConsumeInfo'] = $getLeaveSummary['leaveConsumeInfo'];
			$data['leaveAvailableInfo'] = $getLeaveSummary['leaveAvailableInfo'];
			
			$data['leaveTypeDetails'] = LeaveTypeMasterModel::where('t_is_deleted' , 0 )->where('t_is_show' , 1 )->get();
			$data['employeeId'] = Wild_tiger::encode($employeeId);
			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'my-leaves/leave-summary')->with ( $data )->render();
			
			echo $html;die;
		} 
	}
	
	public function checkDuplicateLeave(Request $request) {
			
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		$employeeId = (!empty($request->employee_id) ? (int)Wild_tiger::decode($request->employee_id) : $this->employeeId  );
			
		$validator = Validator::make ( $request->all (), [
				'leave_from_date' => [ 'required' ,new CheckDuplicateLeave($request) ]  ,
		], [
				'leave_from_date.required' => __ ( 'messages.error-duplicate-leave-request' ),
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
	
	public function checkLeaveBalance(Request $request) {
			
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		$employeeId = (!empty($request->employee_id) ? (int)Wild_tiger::decode($request->employee_id) : $this->employeeId  );
			
		$validator = Validator::make ( $request->all (), [
				'leave_from_date' => [ 'required' ,new CheckBalanceLeave($request) ]  ,
		], [
				'leave_from_date.required' => __ ( 'messages.error-enought-leave-balance-issue' ),
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
	
	public function sendLeaveMail($leaveRecordId , $action = "Apply Leave"){
		
		$applyLeaveWhere = [];
		$applyLeaveWhere['master_id'] = $leaveRecordId;
		$applyLeaveWhere['singleRecord'] = true;
			
		$applyLeaveInfo = $this->crudModel->getRecordDetails($applyLeaveWhere);
			
		if( (!empty($applyLeaveInfo)) ){
			//echo "<pre>";print_r($applyLeaveInfo);die;
		
			$leaveTypeName = ( isset($applyLeaveInfo->leaveTypeInfo->v_leave_type_name) ? $applyLeaveInfo->leaveTypeInfo->v_leave_type_name : '' );
			$leaveStartDate = ( isset($applyLeaveInfo->dt_leave_from_date) ? $applyLeaveInfo->dt_leave_from_date : '' );
			$leaveEndDate = ( isset($applyLeaveInfo->dt_leave_to_date) ? $applyLeaveInfo->dt_leave_to_date : '' );
			
			$leaveDurationText = "";
			if( (!empty($leaveStartDate)) && (!empty($leaveStartDate)) && ( strtotime($leaveStartDate) != strtotime($leaveEndDate) ) ){
				$leaveDuration = convertDateFormat($leaveStartDate) .' - ' . convertDateFormat($leaveEndDate);
				$leaveDurationText = "from";
			} else {
				$leaveDuration = convertDateFormat($leaveStartDate) ;
				$leaveDurationText = "on";
			}
				
			$employeeName = ( isset($applyLeaveInfo->employeeInfo->v_employee_full_name) ? $applyLeaveInfo->employeeInfo->v_employee_full_name : 'User' );
			$employeeId = ( isset($applyLeaveInfo->employeeInfo->v_employee_code) ? $applyLeaveInfo->employeeInfo->v_employee_code : 'Id' );
			$supervisorName = ( isset($applyLeaveInfo->employeeInfo->leaderInfo->v_employee_full_name) ? $applyLeaveInfo->employeeInfo->leaderInfo->v_employee_full_name : config('constants.SYSTEM_ADMIN_NAME') );
				
			$employeeEmail = ( isset($applyLeaveInfo->employeeInfo->v_outlook_email_id) ? $applyLeaveInfo->employeeInfo->v_outlook_email_id : '' );
			$supervisorEmail = ( isset($applyLeaveInfo->employeeInfo->leaderInfo->v_outlook_email_id) ? $applyLeaveInfo->employeeInfo->leaderInfo->v_outlook_email_id : '' );
				
			$noOfDays = ( isset($applyLeaveInfo->d_no_days) ? $applyLeaveInfo->d_no_days : '' );
			$note = ( isset($applyLeaveInfo->v_leave_note) ? $applyLeaveInfo->v_leave_note : '' );
		
			$leaveStatus = ( isset($applyLeaveInfo->e_status) ? $applyLeaveInfo->e_status : '' );;
			$actionTakeName = ( session()->has('name') ? session()->get('name')  : '' ); 
			if(!empty($employeeEmail)){
		
				$mailData = [];
				$mailData['leaveTypeName'] =  $leaveTypeName;
				$mailData['leaveStartDate'] =  $leaveStartDate;
				$mailData['leaveEndDate'] =  $leaveEndDate;
				$mailData['note'] =  $note;
				$mailData['employeeName'] =  $employeeName;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['supervisorName'] =  $supervisorName;
				$mailData['noOfDays'] = $noOfDays;
				$mailData['leaveStatus'] = $leaveStatus;
				$mailData['leaveDuration'] = $leaveDuration;
				$mailData['leaveDurationText'] = $leaveDurationText;
				$mailData['actionTakeName'] = $actionTakeName;
				$mailData['supervisorMail'] = false;
				
				
				switch($action){
					case config('constants.APPLY_LEAVE'):
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'apply-leave-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'apply-leave-mail';
						$subject = trans('messages.apply-leave-mail-subject' , [  'leaveDuration'  => $leaveDuration ] );
						break;
					case config('constants.ACTION_LEAVE'):
						$mailData['sendCommonFooter'] = false;
						if( $leaveStatus == config('constants.CANCELLED_STATUS') ){
							$mailData['sendCommonFooter'] = true;
						}
						$mailTemplate = view( $this->mailTemplateFolderPath .  'approve-leave-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'approve-leave-mail';
						$subject = trans('messages.approve-reject-leave-supervisor-mail-subject' , [ 'status' => strtoupper($leaveStatus) ,   'employeeId' => $employeeId , 'leaveDuration'  => $leaveDuration ,  'employeeName' => $employeeName ] );
						break;
				}
				
				
				$emailHistoryData = [];
				$emailHistoryData['i_login_id'] = ( isset($applyLeaveInfo->employeeInfo->i_login_id) ? $applyLeaveInfo->employeeInfo->i_login_id : 0 );
				$emailHistoryData['i_related_record_id'] = $leaveRecordId;
				$emailHistoryData['v_event'] = $action ;
				$emailHistoryData['v_receiver_email'] = $employeeEmail;
				$emailHistoryData['v_subject'] = $subject;
				$emailHistoryData['v_mail_content'] = $mailTemplate;
				$emailHistoryData['v_notification_title'] = $subject;
					
				$insertEmail = $this->crudModel->insertTableData(config('constants.EMAIL_HISTORY_TABLE'), $emailHistoryData);
					
					
				$config['mailData'] = $mailData;
				$config['viewName'] =  $viewName;
				$config['v_mail_content'] = $mailTemplate;
				$config['subject'] = $subject;
				$config['to'] = $employeeEmail;
		
				$sendMail = [];
				$mailSendError = null;
				try{
					$sendMail = sendMailSMTP($config);
				}catch(\Exception $e){
					$mailSendError = $e->getMessage();
				}
					
				$updateEmailData = [];
				if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
					$updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
				} else {
					$updateEmailData['e_status'] = config('constants.FAILED_STATUS');
					$updateEmailData['v_response'] = (!empty($mailSendError) ? $mailSendError : ( isset($sendMail['msg']) ? $sendMail['msg'] : null  ) ); 
				}
		
				$this->crudModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateEmailData , [ 'i_id' => $insertEmail ] );
					
			}
		
			if(!empty($supervisorEmail)){
		
				$mailData = [];
				$mailData['leaveTypeName'] =  $leaveTypeName;
				$mailData['leaveStartDate'] =  $leaveStartDate;
				$mailData['leaveEndDate'] =  $leaveEndDate;
				$mailData['note'] =  $note;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['employeeName'] =  $employeeName;
				$mailData['supervisorName'] =  $supervisorName;
				$mailData['noOfDays'] = $noOfDays;
				$mailData['leaveStatus'] = $leaveStatus;
				$mailData['leaveDuration'] = $leaveDuration;
				$mailData['actionTakeName'] = $actionTakeName;
				$mailData['actionTakenByName'] = $actionTakeName;
				$mailData['userNameVerb'] = "has";
				$mailData['leaveDurationText'] = $leaveDurationText;
				$mailData['supervisorMail'] = true;
				
				if( isset($applyLeaveInfo->employeeInfo->leaderInfo->i_login_id) && ( $applyLeaveInfo->employeeInfo->leaderInfo->i_login_id == session()->get('user_id') ) ){
					$mailData['actionTakenByName'] = "You";
					$mailData['userNameVerb'] = "have";
					
				}
				
		
				switch($action){
					case config('constants.APPLY_LEAVE'):
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'apply-leave-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'apply-leave-mail';
						$subject = trans('messages.apply-leave-supervisor-mail-subject' , [ 'employeeId' => $employeeId , 'leaveDuration'  => $leaveDuration ,  'employeeName' => $employeeName ] );
						break;
					case config('constants.ACTION_LEAVE'):
						if( $leaveStatus == config('constants.CANCELLED_STATUS') ){
							$mailData['sendCommonFooter'] = false;
						}
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'approve-leave-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'approve-leave-mail';
						$subject = trans('messages.approve-reject-leave-supervisor-mail-subject' , [ 'status' => strtoupper($leaveStatus) ,   'employeeId' => $employeeId , 'leaveDuration'  => $leaveDuration ,  'employeeName' => $employeeName ] );
						break;
				}
				
				
				//echo $subject;echo "<br><br><br>";
					
					
				$emailHistoryData = [];
				$emailHistoryData['i_login_id'] = ( isset($applyLeaveInfo->employeeInfo->leaderInfo->i_login_id) ? $applyLeaveInfo->employeeInfo->leaderInfo->i_login_id : 0 );
				$emailHistoryData['i_related_record_id'] = $leaveRecordId;
				$emailHistoryData['v_event'] = $action;
				$emailHistoryData['v_receiver_email'] = $supervisorEmail;
				$emailHistoryData['v_subject'] = $subject;
				$emailHistoryData['v_mail_content'] = $mailTemplate;
				$emailHistoryData['v_notification_title'] = $subject;
		
				$insertEmail = $this->crudModel->insertTableData(config('constants.EMAIL_HISTORY_TABLE'), $emailHistoryData);
		
		
				$config['mailData'] = $mailData;
				$config['viewName'] =  $viewName;
				$config['v_mail_content'] = $mailTemplate;
				$config['subject'] = $subject;
				$config['to'] = $supervisorEmail;
		
				$sendMail = [];
				$mailSendError = null;
				try{
					$sendMail = sendMailSMTP($config);
				}catch(\Exception $e){
					$mailSendError = $e->getMessage();
				}
		
				$updateEmailData = [];
				if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
					$updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
				} else {
					$updateEmailData['e_status'] = config('constants.FAILED_STATUS');
					$updateEmailData['v_response'] = (!empty($mailSendError) ? $mailSendError : ( isset($sendMail['msg']) ? $sendMail['msg'] : null  ) ); 
				}
		
				$this->crudModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateEmailData , [ 'i_id' => $insertEmail ] );
		
			}
		
			if( isset($applyLeaveInfo->employeeInfo->i_id) && ( $applyLeaveInfo->employeeInfo->i_login_id != config('constants.ADMIN_LOGIN_ID') ) && ( ( empty($applyLeaveInfo->employeeInfo->i_leader_id) ) || (  isset($applyLeaveInfo->employeeInfo->leaderInfo->i_login_id) && ( $applyLeaveInfo->employeeInfo->leaderInfo->i_login_id != config('constants.ADMIN_LOGIN_ID') ) ) )  ){
				$mailData = [];
				$mailData['leaveTypeName'] =  $leaveTypeName;
				$mailData['leaveStartDate'] =  $leaveStartDate;
				$mailData['leaveEndDate'] =  $leaveEndDate;
				$mailData['note'] =  $note;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['employeeName'] =  $employeeName;
				$mailData['supervisorName'] =  config('constants.SYSTEM_ADMIN_NAME');
				$mailData['noOfDays'] = $noOfDays;
				$mailData['leaveDuration'] = $leaveDuration;
				$mailData['leaveStatus'] = $leaveStatus;
				$mailData['actionTakeName'] = $actionTakeName;
				$mailData['actionTakenByName'] = "You";
				$mailData['userNameVerb'] = "Have";
				$mailData['leaveDurationText'] = $leaveDurationText;
				$mailData['supervisorMail'] = true;
				
				if( isset($applyLeaveInfo->i_approved_by_id) && ( $applyLeaveInfo->i_approved_by_id != config('constants.ADMIN_LOGIN_ID') ) ){
					$mailData['actionTakenByName'] = $actionTakeName;
					$mailData['userNameVerb'] = "has";
				}
				
				switch($action){
					case config('constants.APPLY_LEAVE'):
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'apply-leave-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'apply-leave-mail';
						$subject = trans('messages.apply-leave-supervisor-mail-subject' , [ 'employeeId' => $employeeId , 'leaveDuration'  => $leaveDuration ,  'employeeName' => $employeeName ] );
						break;
					case config('constants.ACTION_LEAVE'):
						if( $leaveStatus == config('constants.CANCELLED_STATUS') ){
							$mailData['sendCommonFooter'] = false;
						}
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'approve-leave-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'approve-leave-mail';
						$subject = trans('messages.approve-reject-leave-supervisor-mail-subject' , [ 'status' => strtoupper($leaveStatus) ,   'employeeId' => $employeeId , 'leaveDuration'  => $leaveDuration ,  'employeeName' => $employeeName ] );
						break;
				}
					
					
				$emailHistoryData = [];
				$emailHistoryData['i_login_id'] = config('constants.ADMIN_LOGIN_ID');
				$emailHistoryData['i_related_record_id'] = $leaveRecordId;
				$emailHistoryData['v_event'] = $action;
				$emailHistoryData['v_receiver_email'] = config('constants.SYSTEM_ADMIN_EMAIL');
				$emailHistoryData['v_subject'] = $subject;
				$emailHistoryData['v_mail_content'] = $mailTemplate;
				$emailHistoryData['v_notification_title'] = $subject;
				
				$insertEmail = $this->crudModel->insertTableData(config('constants.EMAIL_HISTORY_TABLE'), $emailHistoryData);
				
				
				$config['mailData'] = $mailData;
				$config['viewName'] =  $viewName ;
				$config['v_mail_content'] = $mailTemplate;
				$config['subject'] = $subject;
				$config['to'] = config('constants.SYSTEM_ADMIN_EMAIL');;
				
				$sendMail = [];
				$mailSendError = null;
				try{
					$sendMail = sendMailSMTP($config);
				}catch(\Exception $e){
					$mailSendError = $e->getMessage();
				}
				
				$updateEmailData = [];
				if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
					$updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
				} else {
					$updateEmailData['e_status'] = config('constants.FAILED_STATUS');
					$updateEmailData['v_response'] = (!empty($mailSendError) ? $mailSendError : ( isset($sendMail['msg']) ? $sendMail['msg'] : null  ) );
				}
				
				$this->crudModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateEmailData , [ 'i_id' => $insertEmail ] );
			}
			
			
		
		
		}
		
	}
	
	public function leaveTypeHistory(REquest $request){
	
		if(!empty($request->all())){
			
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
			
			$leaveTypeId = (!empty($request->input('leave_type_id')) ? (int)($request->input('leave_type_id')) : 0 );
			
			if( ( $employeeId > 0 ) && ( $leaveTypeId > 0 ) ){
				
				//var_dump($employeeId);die;
				//var_dump($leaveTypeId);
				
				$academicYear = (!empty($request->post('search_academic_year')) ? trim($request->post('search_academic_year')) : date("Y") );
				
				$startDate = getYearStartDate($academicYear);
				$endDate = getYearEndDate($academicYear);
				
				//var_dump($startDate);
				//var_dump($endDate);
				
				$appliedLeaveWhere = [];
				$appliedLeaveWhere['leave_status'] = [ config('constants.APPROVED_STATUS') , config('constants.PENDING_STATUS')  ];
				$appliedLeaveWhere['employee_id'] = $employeeId;
				$appliedLeaveWhere['leave_type'] = $leaveTypeId;
				$appliedLeaveWhere['leave_from_date'] = $startDate;
				$appliedLeaveWhere['leave_to_date'] = $endDate;
				
				$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
				if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
					$appliedLeaveWhere['show_all'] = true;
				}
				
				$appliedLeaveDetails = $this->crudModel->getRecordDetails($appliedLeaveWhere); 
				
				$assignBalanceWhere = [];
				$assignBalanceWhere['i_employee_id'] = $employeeId;
				$assignBalanceWhere['i_leave_type_id'] = $leaveTypeId;
				$assignBalanceWhere['leave_from_date'] = $startDate;
				$assignBalanceWhere['leave_to_date'] = $endDate;
				
				$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
				if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
					$assignBalanceWhere['show_all'] = true;
				}
				$assignBalanceDetails = $this->leaveAssignHistoryModel->getRecordDetails($assignBalanceWhere);
				
				$appliedLeaveDetails = (!empty($appliedLeaveDetails) ? objectToArray($appliedLeaveDetails) : [] );
				$assignBalanceDetails = (!empty($assignBalanceDetails) ? objectToArray($assignBalanceDetails) : [] );
				
				$combineLeaveDetails = (!empty($appliedLeaveDetails) ? array_merge($appliedLeaveDetails,$assignBalanceDetails) : $assignBalanceDetails );
				
				//echo "<pre> appliedLeave";print_r($combineLeaveDetails);die;
				//echo "<pre> appliedLeave";print_r($finalLeaveDetails);
				if(!empty($combineLeaveDetails)){
					usort($combineLeaveDetails, 'action_date');
				}
				//echo "<pre> appliedLeave";print_r($combineLeaveDetails);die;
				//echo "<pre>";print_r($finalLeaveDetails);die;
				$data = [];
				
				//echo "<pre>";print_r($finalLeaveDetails);
				$displayDetails = [];
				$leaveSummaryDetails = [];
				if(!empty($combineLeaveDetails)){
					
					$balance = 0;
					if( $leaveTypeId == config('constants.CARRY_FORWARD_LEAVE_TYPE_ID')){
						$savedLeaveInfo = [];
						$savedLeaveInfo['i_employee_id'] = $employeeId;
						$savedLeaveInfo['v_year'] = ( $academicYear - 1  );
						$savedLeaveInfo['t_is_deleted'] = 0;
						$getYearLeaveBalanceDetails =  $this->leaveAssignHistoryModel->getSingleRecordById(config('constants.ACADEMIC_SAVED_LEAVE_INFO') , [ 'd_save_leave' ] , $savedLeaveInfo );
						if(!empty($getYearLeaveBalanceDetails) && ($getYearLeaveBalanceDetails->d_save_leave > 0 ) ){
							$balance = $getYearLeaveBalanceDetails->d_save_leave;
						}	
					}
					
					foreach($combineLeaveDetails as $combineLeaveDetail){
						if( isset($combineLeaveDetail['record_type'])  && (!empty($combineLeaveDetail['record_type'])) ) {
							$rowData = [];
							switch($combineLeaveDetail['record_type']){
								case 'applied_leave':
									if(!empty($combineLeaveDetail['d_no_days'])){
										if( $balance > 0 ){
											$balance -= $combineLeaveDetail['d_no_days'];
										}
										
									}
									$rowData['date'] = ( isset($combineLeaveDetail['compare_date']) ? ($combineLeaveDetail['compare_date']) : '' );
									
									if( strtotime($combineLeaveDetail['dt_leave_from_date']) == strtotime($combineLeaveDetail['dt_leave_to_date']) ){
										$rowData['date'] = convertDateFormat( $combineLeaveDetail['dt_leave_from_date'] ) . ( ( $combineLeaveDetail['e_duration'] != config('constants.FULL_DAY_LEAVE') ) ? ' ('. $combineLeaveDetail['e_duration'] . ')' : ''  );
									} else {
										$rowData['date'] = convertDateFormat( $combineLeaveDetail['dt_leave_from_date'] ) . ( ( $combineLeaveDetail['e_from_duration'] != config('constants.FIRST_HALF_LEAVE') ) ? ' ('. $combineLeaveDetail['e_from_duration'] . ')' : ''  ) . ' - ' . convertDateFormat( $combineLeaveDetail['dt_leave_to_date'] ) .  ( ( $combineLeaveDetail['e_to_duration'] != config('constants.SECOND_HALF_LEAVE') ) ? ' ('. $combineLeaveDetail['e_to_duration'] . ')' : ''  ) ;
									}
									
									$rowData['action_date'] = ( isset($combineLeaveDetail['action_date']) ? ($combineLeaveDetail['action_date']) : '' );
									$rowData['change'] = ( isset($combineLeaveDetail['d_no_days']) ? ' - '. ($combineLeaveDetail['d_no_days']) : '' );
									$rowData['balance'] = $balance;
									$rowData['remark'] = ( isset($combineLeaveDetail['v_leave_note']) ? ($combineLeaveDetail['v_leave_note']) : '' );
									break;
								case 'assign_leave':
									if(!empty($combineLeaveDetail['d_no_of_days_assign'])){
										$balance += $combineLeaveDetail['d_no_of_days_assign'];
									}
									$rowData['date'] = ( isset($combineLeaveDetail['compare_date']) ? convertDateFormat($combineLeaveDetail['compare_date']) : '' );
									$rowData['action_date'] = ( isset($combineLeaveDetail['action_date']) ? ($combineLeaveDetail['action_date']) : '' );
									$rowData['change'] =  ( isset($combineLeaveDetail['d_no_of_days_assign']) ? ($combineLeaveDetail['d_no_of_days_assign']) : '' );;
									$rowData['balance'] = $balance;
									$rowData['remark'] = ( ( isset($combineLeaveDetail['v_leave_note']) && (!empty($combineLeaveDetail['v_leave_note'])) ) ? PHP_EOL . ($combineLeaveDetail['v_leave_note']) : 'Leave Added' ); ;
									break;
							}
							//echo "balance  = ".$balance;echo "<br><br>";
							if(!empty($rowData)){
								$leaveSummaryDetails[] = $rowData; 
							}
						}
					}
					
					if(!empty($leaveSummaryDetails)){
						$cnt = count($leaveSummaryDetails)-1;
						$newAr =array();
						for($i=$cnt; $i >= 0; $i--){
							$displayDetails[] = $leaveSummaryDetails[$i];
						}	
					}
				}
				
				
				$data['recordDetails'] = $displayDetails;
				
				$html = view (config('constants.AJAX_VIEW_FOLDER') . 'my-leaves/leave-history-modal')->with ( $data )->render();
				
				$this->ajaxResponse(1, trans('messages.success') , [ 'html' => $html ] );
			} else {
				$this->ajaxResponse(101, trans('messages.system-error'));
			}
			
		}
		
	}
	
	
}
