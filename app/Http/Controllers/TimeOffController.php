<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\TimeOff;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use App\Rules\checkTimeOffAdjustmentRequest;
use App\Rules\checkTimeOffRequest;
use App\Models\SettingsModel;
class TimeOffController extends MasterController
{
    //
	public function __construct(){
	
		parent::__construct();
		$this->crudModel = new TimeOff();
		$this->moduleName = trans('messages.my-time-off');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->tableName = config('constants.TIME_OFF_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'time-off/' ;
		$this->redirectUrl = config('constants.TIME_OFF_MASTER_URL');
		$this->employeeId = 1;
		$this->settingsModel =  new SettingsModel();
	}
	
	public function index( Request $request ){
		$ajaxRequest = false;
		if($request->ajax()){
			$ajaxRequest = true;
			$employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		} else {
			$employeeId = session()->get('user_employee_id');
		}
		$data = [];
		$data['pageTitle'] = trans('messages.my-time-off');
		$currentYear = currentSystemYear();
		
		$startDate = getYearStartDate($currentYear);
		$endDate = getYearEndDate($currentYear);
		
		$data['yearDetails'] = yearDetails();
		$data['currentYear'] = $currentYear;
		$data['startDate'] = $startDate;
		$data['endDate'] = $endDate;
		$data['timeOffSelectionDetails'] = timeOffSelectionInfo();
		
		$timeOffSummary = $this->getTimeOffCountInfo( $employeeId , $startDate , $endDate  );
		
		$data['timeOffCountDetails'] = ( isset($timeOffSummary['timeOffCountSummary']) ? $timeOffSummary['timeOffCountSummary'] : [] );
		$data['monthWiseTimeOffCountDetails'] = ( isset($timeOffSummary['monthWiseCount']) ? $timeOffSummary['monthWiseCount'] : [] );
		$data['weekWiseTimeOffCountDetails'] = ( isset($timeOffSummary['weekDayWiseCount']) ? $timeOffSummary['weekDayWiseCount'] : [] );
		$data['employeeId'] = Wild_tiger::encode($employeeId);
		
		
		$whereData = [] ;
		$whereData['singleRecord'] = true;
		$settingsInfo = $this->settingsModel->getRecordDetails($whereData);
		$data['settingsInfo'] = $settingsInfo ;
		
		if( $ajaxRequest != false ){
			$html = view ( $this->folderName  . 'time-off-info' )->with ( $data )->render();
			echo $html;die;
		}
		
		return view( $this->folderName . 'time-off')->with($data);
	}
	
	public function filterTimeOffDashboard(Request $request){
		
		if(!empty($request->post())){
			
			$academicYear = (!empty($request->post('academic_year')) ? $request->post('academic_year') : date("Y") );
			$employeeId = (!empty($request->post('employee_id')) ? $request->post('employee_id') : $this->employeeId );
			$startDate = getYearStartDate($academicYear);
			$endDate = getYearEndDate($academicYear);
			
			$timeOffSummary = $this->getTimeOffCountInfo( $employeeId , $startDate , $endDate  );
			
			$data['timeOffCountDetails'] = ( isset($timeOffSummary['timeOffCountSummary']) ? $timeOffSummary['timeOffCountSummary'] : [] );
			$data['monthWiseTimeOffCountDetails'] = ( isset($timeOffSummary['monthWiseCount']) ? $timeOffSummary['monthWiseCount'] : [] );
			$data['weekWiseTimeOffCountDetails'] = ( isset($timeOffSummary['weekDayWiseCount']) ? $timeOffSummary['weekDayWiseCount'] : [] );
			
			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'time-off/time-off-summary' )->with ( $data )->render();
			
			echo $html;die;
			
		}
		
		
	}
	
	private function getTimeOffCountInfo( $employeeId , $startDate , $endDate ){
		/*
		var_dump($employeeId);echo "<br><br>";
		var_dump($startDate);echo "<br><br>";
		var_dump($endDate);echo "<br><br>";
		*/
		$getEmployeeTimeOffSummary =  self::CallRaw ( 'get_employee_time_off_summary' , [ $employeeId , $startDate , $endDate  ]);
		//echo "<pre>";print_r($getEmployeeTimeOffSummary);die;
		/* var_dump(isset($getEmployeeTimeOffSummary[0][0]));echo "<br><br>";
		var_dump(isset($getEmployeeTimeOffSummary[1][0]));echo "<br><br>";
		var_dump(isset($getEmployeeTimeOffSummary[2][0]));echo "<br><br>";
		echo "<pre>";print_r($getEmployeeTimeOffSummary);
		 */
		$timeoffCountDetails = ( isset($getEmployeeTimeOffSummary[0][0]) ? (array)$getEmployeeTimeOffSummary[0] : [] );
		$weekWiseTimeoffCountDetails = ( isset($getEmployeeTimeOffSummary[1][0]) ? (array)$getEmployeeTimeOffSummary[1] : [] );
		$monthWiseTimeoffCountDetails = ( isset($getEmployeeTimeOffSummary[2][0]) ? (array)$getEmployeeTimeOffSummary[2] : [] );
		//echo "<pre>";print_r($getEmployeeTimeOffSummary);
		//dd($timeoffCountDetails);
		$timeoffCount = [];
		$timeoffCount['approved_count'] = 0;
		$timeoffCount['pending_count'] = 0;
		$timeoffCount['rejected_count'] = 0;
		$timeoffCount['cancelled_count'] = 0;
	
		if(!empty($timeoffCountDetails)){
			$timeoffCountDetails = objectToArray($timeoffCountDetails);
			$allStatusDetails = array_column($timeoffCountDetails,'e_status');
			if(in_array(config("constants.PENDING_STATUS"), $allStatusDetails)){
				$searchKey = array_search(config("constants.PENDING_STATUS"), $allStatusDetails);
				if(strlen($searchKey) > 0 ){
					$timeoffCount['pending_count'] = isset($timeoffCountDetails[$searchKey]['record_count']) ? $timeoffCountDetails[$searchKey]['record_count'] : 0 ;;
				}
			}
			if(in_array(config("constants.APPROVED_STATUS"), $allStatusDetails)){
				$searchKey = array_search(config("constants.APPROVED_STATUS"), $allStatusDetails);
				if(strlen($searchKey) > 0 ){
					$timeoffCount['approved_count'] = isset($timeoffCountDetails[$searchKey]['record_count']) ? $timeoffCountDetails[$searchKey]['record_count'] : 0 ;;
				}
			}
			if(in_array(config("constants.REJECTED_STATUS"), $allStatusDetails)){
				$searchKey = array_search(config("constants.REJECTED_STATUS"), $allStatusDetails);
				if(strlen($searchKey) > 0 ){
					$timeoffCount['rejected_count'] = isset($timeoffCountDetails[$searchKey]['record_count']) ? $timeoffCountDetails[$searchKey]['record_count'] : 0 ;;
				}
			}
			if(in_array(config("constants.CANCELLED_STATUS"), $allStatusDetails)){
				$searchKey = array_search(config("constants.CANCELLED_STATUS"), $allStatusDetails);
				if(strlen($searchKey) > 0 ){
					$timeoffCount['cancelled_count'] = isset($timeoffCountDetails[$searchKey]['record_count']) ? $timeoffCountDetails[$searchKey]['record_count'] : 0 ;;
				}
			}
		}
	
		$timeoffCount['total_count'] = ( $timeoffCount['pending_count'] + $timeoffCount['approved_count'] + $timeoffCount['rejected_count']  + $timeoffCount['cancelled_count'] );
		
		
		$weekDayWiseCount = [];
		//$allStatusDetails = [ config('constants.PENDING_STATUS') , config('constants.APPROVED_STATUS'), config('constants.REJECTED_STATUS') , config('constants.CANCELLED_STATUS') ];
		$allWeekDays =  [ 'Monday' , 'Tuesday' , 'Wednesday' , 'Thursday' , 'Friday' , 'Saturday' , 'Sunday'  ];
		
		if(!empty($allWeekDays)){
			foreach($allWeekDays as $allWeekDay){
				$weekDayWiseCount[$allWeekDay] = 0;
			}
		}
		if(!empty($weekWiseTimeoffCountDetails)){
			foreach($weekWiseTimeoffCountDetails as $weekWiseTimeoffCountDetail){
				$weekDayWiseCount[$weekWiseTimeoffCountDetail->week_day] = $weekWiseTimeoffCountDetail->record_count;
			}
		}
		
		//echo "<pre>";print_r($weekDayWiseCount);
		
		$monthWiseCount = [];
		for($i = 1 ; $i <= 12 ; $i++ ){
			$monthWiseCount[date("F" ,strtotime(date("Y-". $i."-01")))] = 0;
		}
		
		if(!empty($monthWiseTimeoffCountDetails)){
			foreach($monthWiseTimeoffCountDetails as $monthWiseTimeoffCountDetails){
				$monthWiseCount[$monthWiseTimeoffCountDetails->month] = $monthWiseTimeoffCountDetails->record_count;
			}
		}
		
		
		$response = [];
		$response['timeOffCountSummary'] = $timeoffCount;
		$response['monthWiseCount'] = $monthWiseCount;
		$response['weekDayWiseCount'] = $weekDayWiseCount;
		
		//echo "<pre>";print_r($response);
		return $response;
	}
	
	public function applyTimeOff(Request $request){
		
		$formValidation['time_off_date'] = ['required'];
		$formValidation['time_off_type'] = ['required' , new checkTimeOffAdjustmentRequest($request)];
		$formValidation['time_off_from'] = ['required' , new checkTimeOffRequest($request) ];
		$formValidation['time_off_to'] = ['required'];
		$timeOffType = (!empty($request->post('time_off_type')) ? ($request->post('time_off_type')) : null );
		if( $timeOffType == config('constants.ADJUSTMENT_STATUS') ){
			$formValidation['time_off_back_date'] = ['required'];
			$formValidation['time_off_back_from_time'] = ['required'];
			$formValidation['time_off_back_to_time'] = ['required'];
		}
		
		//$formValidation['employee_id'] = ['required'];
		
		$checkValidation = Validator::make($request->all(),$formValidation,
				[
						'time_off_date.required' => __('messages.please-enter-date'),
						'time_off_type.required' => __('messages.require-type'),
						'time_off_from.required' => __('messages.require-from-time'),
						'time_off_to.required' => __('messages.require-to-time'),
						'time_off_back_date.required' => __('messages.required-time-back-date'),
						'time_off_back_from_time.required' => __('messages.require-from-time'),
						'time_off_back_to_time.required' => __('messages.require-to-time'),
				]
		);
		if($checkValidation->fails() != false){
			$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.apply-leave') ] ) ) );
		}
		$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : session()->get('user_employee_id') );
		$timeOffDate = (!empty($request->post('time_off_date')) ? dbDate($request->post('time_off_date')) : '');
		$timeOffFromTime = (!empty($request->post('time_off_from')) ? dbTime($request->post('time_off_from')) : '');
		$timeOffToTime = (!empty($request->post('time_off_to')) ? dbTime($request->post('time_off_to')) : '');
		$timeOffType = (!empty($request->post('time_off_type')) ? ($request->post('time_off_type')) : null );
		$remark = (!empty($request->post('time_off_remark')) ? trim($request->post('time_off_remark')) : null );
		
		if( ( $timeOffType == config('constants.ADJUSTMENT_TIME_OFF') ) &&  ( strtotime($timeOffToTime) - strtotime($timeOffFromTime) ) > config('constants.ADJUSTMENT_TIME_OFF_REQUEST_LIMIT') ){
			$this->ajaxResponse(101 ,  trans('messages.error-adjustment-time-limit') );
		} 
		
		$recordData  = [];
		$recordData['i_employee_id'] = $employeeId;
		$recordData['dt_time_off_date'] = $timeOffDate;
		$recordData['e_record_type'] = $timeOffType;
		$recordData['t_from_time'] = $timeOffFromTime;
		$recordData['t_to_time'] = $timeOffToTime;
		
		$recordData['dt_time_off_back_date'] = null;
		$recordData['t_from_back_time'] = null;
		$recordData['t_to_back_time'] = null;
		
		if( $timeOffType == config('constants.ADJUSTMENT_STATUS') ){
			$recordData['dt_time_off_back_date'] = (!empty($request->post('time_off_back_date')) ? dbDate($request->post('time_off_back_date')) : '');
			$recordData['t_from_back_time'] = (!empty($request->post('time_off_back_from_time')) ? dbTime($request->post('time_off_back_from_time')) : '');;
			$recordData['t_to_back_time'] = (!empty($request->post('time_off_back_to_time')) ? dbTime($request->post('time_off_back_to_time')) : null );;
		}
		
		
		$recordData['v_remark'] = $remark;
		
		$result = false;
		DB::beginTransaction();
		
		try{
			$insertTimeOff = $this->crudModel->insertTableData($this->tableName , $recordData);
			$result = true;
		}catch(\Exception $e){
			DB::rollback();
			$result = false;
		}
		
		$successMessage = trans( 'messages.success-create' , [ 'module' => trans('messages.time-off') ] );
		$errorMessage = trans( 'messages.error-create' , [ 'module' => trans('messages.time-off') ] );
		
		if($result != false ){
			
			DB::commit();
			
			$this->sendTimeOffMail( $insertTimeOff );
			
			$academicYear = (!empty($request->post('search_academic_year')) ? $request->post('search_academic_year') : date("Y") );
			$employeeId = $employeeId;
			$startDate = getYearStartDate($academicYear);
			$endDate = getYearEndDate($academicYear);
				
			$timeOffSummary = $this->getTimeOffCountInfo( $employeeId , $startDate , $endDate  );
			
			//echo "<pre>";print_r($timeOffSummary);die;
			
			$data['timeOffCountDetails'] = ( isset($timeOffSummary['timeOffCountSummary']) ? $timeOffSummary['timeOffCountSummary'] : [] );
			$data['monthWiseTimeOffCountDetails'] = ( isset($timeOffSummary['monthWiseCount']) ? $timeOffSummary['monthWiseCount'] : [] );
			$data['weekWiseTimeOffCountDetails'] = ( isset($timeOffSummary['weekDayWiseCount']) ? $timeOffSummary['weekDayWiseCount'] : [] );
			$data['employeeId']  = Wild_tiger::encode($employeeId);
			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'time-off/time-off-summary' )->with ( $data )->render();
			
			$this->ajaxResponse(1, $successMessage , [ 'html' => $html ] );
		} else {
			DB::rollback();
			$this->ajaxResponse(101 ,  $errorMessage );
		}
	}
	
	public function updateTimeOffStatus(Request $request){
		
		if(!empty($request->post())){
			
			$recordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0 );
			$status = (!empty($request->post('status')) ? trim($request->post('status')) : null );
			
			if(!empty($status) && (!empty($recordId))){
				
				$getTimeOffRecordWhere = [];
				$getTimeOffRecordWhere['master_id'] = $recordId;
				$getTimeOffRecordWhere['singleRecord'] = true;
				
				if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){
					$getTimeOffRecordWhere['show_all'] = true;
				}
				
				$getRecordInfo = $this->crudModel->getRecordDetails($getTimeOffRecordWhere);
				
				if( (!empty($getRecordInfo)) ){
					
					if( $status == config("constants.APPROVED_STATUS") ){
						if( !in_array( $getRecordInfo->e_status , [ config("constants.PENDING_STATUS") ] ) ){
							$this->ajaxResponse(101, trans('messages.error-invalid-time-off-status' , [ 'status' =>  $getRecordInfo->e_status ] ) );
						}
					} else if( $status == config("constants.REJECTED_STATUS") ){
						if( !in_array( $getRecordInfo->e_status , [ config("constants.PENDING_STATUS") ] ) ){
							$this->ajaxResponse(101, trans('messages.error-invalid-time-off-status' , [ 'status' =>  $getRecordInfo->e_status ] ) );
						}
					} else if( $status == config("constants.CANCELLED_STATUS") ){
						if( !in_array( $getRecordInfo->e_status , [ config("constants.PENDING_STATUS"), config("constants.APPROVED_STATUS") ] ) ){
							$this->ajaxResponse(101, trans('messages.error-invalid-time-off-status' , [ 'status' =>  $getRecordInfo->e_status ] ) );
						}
					}
					
					DB::beginTransaction();
					
					$updateRecordData = [];
					$updateRecordData['e_status'] = $status;
					$updateRecordData['v_approve_reject_remark'] = (!empty($request->post('approve_reject_time_off_reason')) ? $request->post('approve_reject_time_off_reason') : null );
					$updateRecordData['i_approved_by_id'] = session()->get('user_id');
					$updateRecordData['dt_approved_at'] = date("Y-m-d H:i:s");
					
					$result = true;
					$html = null;
					try{
						
						$this->crudModel->updateTableData($this->tableName , $updateRecordData , [ 'i_id' =>  $recordId  ] );
						
						$timeOffRecordWhere = [];
						$timeOffRecordWhere['master_id'] = $recordId;
						$timeOffRecordWhere['singleRecord'] = true;
						
						if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){
							$timeOffRecordWhere['show_all'] = true;
						}
						
						$recordDetail = $this->crudModel->getRecordDetails( $timeOffRecordWhere );
						
						$recordInfo = [];
						$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
						$recordInfo['recordDetail'] = $recordDetail;
						
						$html = view (config('constants.AJAX_VIEW_FOLDER') . 'time-off-report/single-time-off-report')->with ( $recordInfo )->render();
						
						$result = true;
						
					}catch(\Exception $e){
						DB::rollback();
						$result = false;
					}
					
					if($result != false ){
						DB::commit();
						$this->sendTimeOffMail( $recordId , config('constants.ACTION_TIME_OFF') );
						$this->ajaxResponse(1, trans('messages.time-off-success' , [ 'module' => trans('messages.time-off') . ' ' .$status ] ),['html' => $html] );
					} else {
						DB::rollback();
						$this->ajaxResponse(101, trans('messages.error-time-off' , [ 'module' => trans('messages.time-off') . ' ' .$status ] ) );
					}
				}
			}
			$this->ajaxResponse(101, trans('messages.system-error') );
			
		}
	}
	
	public function checkDuplicateTimeOffAdjustmentRequest(Request $request) {
		 
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		$employeeId = (!empty($request->employee_id) ? (int)Wild_tiger::decode($request->employee_id) : $this->employeeId  );
		 
		$validator = Validator::make ( $request->all (), [
				'time_off_date' => [ 'required' ,new checkTimeOffAdjustmentRequest($request) ]  ,
		], [
				'time_off_date.required' => __ ( 'messages.please-enter-date' ),
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
	
	public function checkDuplicateTimeOffRequest(Request $request) {
			
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		$employeeId = (!empty($request->employee_id) ? (int)Wild_tiger::decode($request->employee_id) : $this->employeeId  );
			
		$validator = Validator::make ( $request->all (), [
				'time_off_date' => [ 'required' ,new checkTimeOffRequest($request) ]  ,
		], [
				'time_off_date.required' => __ ( 'messages.please-enter-date' ),
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
	
	public function sendTimeOffMail($recordId , $action = "Apply Timeoff"){
	
		$applyTimeOffWhere = [];
		$applyTimeOffWhere['master_id'] = $recordId;
		$applyTimeOffWhere['singleRecord'] = true;
			
		$applyTimeOffInfo = $this->crudModel->getRecordDetails($applyTimeOffWhere);
			
		if( (!empty($applyTimeOffInfo)) ){
			//echo "<pre>";print_r($applyLeaveInfo);die;
	
			$timeoffTypeName = ( isset($applyTimeOffInfo->e_record_type) ? $applyTimeOffInfo->e_record_type : '' );
			$timeOffDate = ( isset($applyTimeOffInfo->dt_time_off_date) ? $applyTimeOffInfo->dt_time_off_date : '' );
			$timeOffFromTime = ( isset($applyTimeOffInfo->t_from_time) ? $applyTimeOffInfo->t_from_time : '' );
			$timeOffToTime = ( isset($applyTimeOffInfo->t_to_time) ? $applyTimeOffInfo->t_to_time : '' );
			
			$timeOffBackDate = ( isset($applyTimeOffInfo->dt_time_off_back_date) ? $applyTimeOffInfo->dt_time_off_back_date : '' );
			$timeOffBackFromTime = ( isset($applyTimeOffInfo->t_from_back_time) ? $applyTimeOffInfo->t_from_back_time : '' );
			$timeOffBackToTime = ( isset($applyTimeOffInfo->t_to_back_time) ? $applyTimeOffInfo->t_to_back_time : '' );
	
			$duration = convertDateFormat($timeOffDate) .' - '. clientTime($timeOffFromTime) .' - ' . clientTime($timeOffToTime); ;
			$backDuration = 'Date:- '.convertDateFormat($timeOffBackDate) .', From Time:- '. clientTime($timeOffBackFromTime) .' To Time:- ' . clientTime($timeOffBackToTime); ;
			
	
			$employeeName = ( isset($applyTimeOffInfo->employeeInfo->v_employee_full_name) ? $applyTimeOffInfo->employeeInfo->v_employee_full_name : 'User' );
			$employeeId = ( isset($applyTimeOffInfo->employeeInfo->v_employee_code) ? $applyTimeOffInfo->employeeInfo->v_employee_code : 'Id' );
			$supervisorName = ( isset($applyTimeOffInfo->employeeInfo->leaderInfo->v_employee_full_name) ? $applyTimeOffInfo->employeeInfo->leaderInfo->v_employee_full_name : config('constants.SYSTEM_ADMIN_NAME')  );
	
			$employeeEmail = ( isset($applyTimeOffInfo->employeeInfo->v_outlook_email_id) ? $applyTimeOffInfo->employeeInfo->v_outlook_email_id : '' );
			$supervisorEmail = ( isset($applyTimeOffInfo->employeeInfo->leaderInfo->v_outlook_email_id) ? $applyTimeOffInfo->employeeInfo->leaderInfo->v_outlook_email_id : '' );
		
			$recordStatus = ( isset($applyTimeOffInfo->e_status) ? $applyTimeOffInfo->e_status : '' );;
			$actionTakeName = ( session()->has('name') ? session()->get('name')  : '' );
			
			if(!empty($employeeEmail)){
	
				$mailData = [];
				$mailData['timeoffTypeName'] =  $timeoffTypeName;
				$mailData['timeOffDate'] =  $timeOffDate;
				$mailData['employeeName'] =  $employeeName;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['supervisorName'] =  $supervisorName;
				$mailData['recordStatus'] = $recordStatus;
				$mailData['duration'] = $duration;
				$mailData['backDuration'] = null;
				if( $timeoffTypeName == config('constants.ADJUSTMENT_STATUS') ){
					$mailData['backDuration'] = $backDuration;
				}
				$mailData['actionTakeName'] = $actionTakeName;
				$mailData['supervisorMail'] = false;
	
	
				switch($action){
					case config('constants.APPLY_TIME_OFF'):
						$mailTemplate = view( $this->mailTemplateFolderPath .  'apply-timeoff-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'apply-timeoff-mail';
						$subject = trans('messages.apply-timeoff-mail-subject' , [ 'timeoffTypeName' =>  $timeoffTypeName , 'duration'  => $duration ] );
						break;
					case config('constants.ACTION_TIME_OFF'):
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'approve-timeoff-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'approve-timeoff-mail';
						$subject = trans('messages.approve-reject-timeoff-mail-subject' , [ 'timeoffTypeName' =>  $timeoffTypeName , 'status' => strtoupper($recordStatus) ,   'employeeId' => $employeeId , 'duration'  => $duration ,  'employeeName' => $employeeName ] );
						break;
				}
	
	
				$emailHistoryData = [];
				$emailHistoryData['i_login_id'] = ( isset($applyTimeOffInfo->employeeInfo->i_login_id) ? $applyTimeOffInfo->employeeInfo->i_login_id : 0 );
				$emailHistoryData['i_related_record_id'] = $recordId;
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
				$mailData['timeoffTypeName'] =  $timeoffTypeName;
				$mailData['timeOffDate'] =  $timeOffDate;
				$mailData['employeeName'] =  $employeeName;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['supervisorName'] =  $supervisorName;
				$mailData['recordStatus'] = $recordStatus;
				$mailData['duration'] = $duration;
				$mailData['backDuration'] = null;
				if( $timeoffTypeName == config('constants.ADJUSTMENT_STATUS') ){
					$mailData['backDuration'] = $backDuration;
				}
				$mailData['actionTakeName'] = $actionTakeName;
				$mailData['actionTakenByName'] = $actionTakeName;
				$mailData['userNameVerb'] = "has";
				$mailData['supervisorMail'] = true;
	
				if( isset($applyTimeOffInfo->employeeInfo->leaderInfo->i_login_id) && ( $applyTimeOffInfo->employeeInfo->leaderInfo->i_login_id == session()->get('user_id') ) ){
					$mailData['actionTakenByName'] = "You";
					$mailData['userNameVerb'] = "have";
						
				}
				
				switch($action){
					case config('constants.APPLY_TIME_OFF'):
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'apply-timeoff-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'apply-timeoff-mail';
						$subject = trans('messages.apply-timeoff-supervisor-mail-subject' , [  'timeoffTypeName' =>  $timeoffTypeName , 'employeeId' => $employeeId , 'duration'  => $duration ,  'employeeName' => $employeeName ] );
						break;
					case config('constants.ACTION_TIME_OFF'):
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'approve-timeoff-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'approve-timeoff-mail';
						$subject = trans('messages.approve-reject-timeoff-supervisor-mail-subject' , [  'timeoffTypeName' =>  $timeoffTypeName , 'status' => strtoupper($recordStatus) ,   'employeeId' => $employeeId , 'duration'  => $duration ,  'employeeName' => $employeeName ] );
						break;
				}
	
	
				//echo $subject;echo "<br><br><br>";
					
					
				$emailHistoryData = [];
				$emailHistoryData['i_login_id'] = ( isset($applyTimeOffInfo->employeeInfo->leaderInfo->i_login_id) ? $applyTimeOffInfo->employeeInfo->leaderInfo->i_login_id : 0 );
				$emailHistoryData['i_related_record_id'] = $recordId;
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
	
			if( isset($applyTimeOffInfo->employeeInfo->i_id) && ( $applyTimeOffInfo->employeeInfo->i_login_id != config('constants.ADMIN_LOGIN_ID') ) && (  ( empty($applyTimeOffInfo->employeeInfo->i_leader_id) ) || (  isset($applyTimeOffInfo->employeeInfo->leaderInfo->i_login_id) && ( $applyTimeOffInfo->employeeInfo->leaderInfo->i_login_id != config('constants.ADMIN_LOGIN_ID') ) ) )  ){
				$mailData = [];
				$mailData['timeoffTypeName'] =  $timeoffTypeName;
				$mailData['timeOffDate'] =  $timeOffDate;
				$mailData['employeeName'] =  $employeeName;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['supervisorName'] =  config('constants.SYSTEM_ADMIN_NAME');
				$mailData['recordStatus'] = $recordStatus;
				$mailData['duration'] = $duration;
				$mailData['backDuration'] = null;
				if( $timeoffTypeName == config('constants.ADJUSTMENT_STATUS') ){
					$mailData['backDuration'] = $backDuration;
				}
				$mailData['actionTakeName'] = $actionTakeName;
				$mailData['actionTakenByName'] = "You";
				$mailData['userNameVerb'] = "Have";
				$mailData['supervisorMail'] = true;
				
				if( isset($applyTimeOffInfo->i_approved_by_id) && ( $applyTimeOffInfo->i_approved_by_id != config('constants.ADMIN_LOGIN_ID') ) ){
					$mailData['actionTakenByName'] = $actionTakeName;
					$mailData['userNameVerb'] = "has";
				}
				
				
				switch($action){
					case config('constants.APPLY_TIME_OFF'):
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'apply-timeoff-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'apply-timeoff-mail';
						$subject = trans('messages.apply-timeoff-supervisor-mail-subject' , [ 'timeoffTypeName' =>  $timeoffTypeName ,  'employeeId' => $employeeId , 'duration'  => $duration ,  'employeeName' => $employeeName ] );
						break;
					case config('constants.ACTION_TIME_OFF'):
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'approve-timeoff-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'approve-timeoff-mail';
						$subject = trans('messages.approve-reject-timeoff-supervisor-mail-subject' , [  'timeoffTypeName' =>  $timeoffTypeName , 'status' => strtoupper($recordStatus) ,   'employeeId' => $employeeId , 'duration'  => $duration ,  'employeeName' => $employeeName ] );
						break;
				}
				
				
				$emailHistoryData = [];
				$emailHistoryData['i_login_id'] = config('constants.ADMIN_LOGIN_ID');
				$emailHistoryData['i_related_record_id'] = $recordId;
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
}
