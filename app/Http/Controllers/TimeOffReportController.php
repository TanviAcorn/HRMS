<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeOff;
use App\EmployeeModel;
use App\LeaveTypeMasterModel;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\Response;
use App\LookupMaster;
class TimeOffReportController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->moduleName = trans('messages.time-off-report');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'time-off-report/' ;
		$this->redirectUrl = config('constants.TIME_OFF_REPORT_URL');
		$this->crudModel = new TimeOff();
	}
	public function index($employeeId = null){
		$data = [];
		$data['pageTitle'] = trans('messages.time-off-report');
		
		$allPermissionId = config('permission_constants.ALL_TIME_OFF_REPORT');
		$data['allPermissionId'] = $allPermissionId;
		
		$page = $this->defaultPage;
		$employeeWhere = [];
		$employeeCrudModel = new EmployeeModel();
		
		#store pagination data array
		$whereData = $paginationData = [];
		$customListing = false;
		$notificationRecordId = ( session()->has('notification_timeoff_id') ? session()->get('notification_timeoff_id') : 0 ); 
		
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$whereData['show_all'] = true;
		}
		
		if(!empty($employeeId)){
			$employeeId = (int)Wild_tiger::decode($employeeId);
			if( $employeeId > 0 ){
				$customListing = true;
				$whereData['employee_id'] = $employeeId;
				$data['selectedEmployeeId'] = $employeeId;
			}
		}
		
		if( $notificationRecordId > 0 ){
			$customListing = true;
			$whereData['master_id'] = $notificationRecordId;
		}
		
		$viewTodayAdjustment = ( session()->has('view_today_adjustment') ? session()->get('view_today_adjustment') : null );
		
		## bydefault relived vala record nai aave
		$whereData['employment_relieved_status'] = [config('constants.RELIEVED_PROBATION_EMPLOYMENT_STATUS')];
		
		$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
		$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
		$whereData['employment_status'] = $selectedEmployeeStatus;
		
		if(!empty($viewTodayAdjustment)){
			$whereData['time_off_from_date'] = $viewTodayAdjustment;
			$whereData['time_off_to_date'] = $viewTodayAdjustment;
			$whereData['time_off_type'] = config('constants.ADJUSTMENT_STATUS');
			$data['startDate'] = $viewTodayAdjustment;
			$data['endDate'] = $viewTodayAdjustment;
			$data['selectedTimeOffStatus'] = $whereData['time_off_type'];
			$whereData['time_off_status'] = [ config('constants.PENDING_STATUS') , config('constants.APPROVED_STATUS') ];
			
			$customListing = true;
		}
		
		if( $customListing != false ){
			unset($data['selectedEmployeeStatus']);
			unset($whereData['employment_status']);
			unset($whereData['employment_relieved_status']);
		}
		
		#get pagination data for first page
		if($page == $this->defaultPage ){
		
			$totalRecords = count($this->crudModel->getRecordDetails($whereData));
		
			$lastPage = ceil($totalRecords/$this->perPageRecord);
		
			$paginationData['current_page'] = $this->defaultPage;
		
			$paginationData['per_page'] = $this->perPageRecord;
		
			$paginationData ['last_page'] = $lastPage;
		
		}
		$whereData ['limit'] = $this->perPageRecord;
		
		$data['recordDetails'] = $this->crudModel->getRecordDetails( $whereData );
		
		$data['pagination'] = $paginationData;
			
		$data['page_no'] = $page;
			
		$data['perPageRecord'] = $this->perPageRecord;
		
		$data['totalRecordCount'] = $totalRecords;
		
		if(session()->get('role') == config('constants.ROLE_USER')){
			$employeeWhere['employee_leader_name'] = (!empty(session()->get('user_employee_id')) ? session()->get('user_employee_id') : '');
			$employeeWhere['employee_login_id'] = (!empty(session()->get('user_id')) ? session()->get('user_id') : '');
		}
		$employeeWhere['order_by'] = [ 'v_employee_full_name'  => 'asc'];
		$employeeWhere['employment_status'] = $selectedEmployeeStatus;
		//$data['employeeDetails'] = EmployeeModel::orderBy('v_employee_full_name', 'ASC')->get();
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$employeeWhere['show_all'] = true;
		}
		$data['employeeDetails'] = $employeeCrudModel->getRecordDetails($employeeWhere);
		
		$data['teamRecordDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
		
		$data['typeInfo'] = typeInfo();
		
		$data['stausInfo'] = stausInfo();
		
		$data['employmentStatusInfo'] = employmentStatusMaster();
		
		return view( $this->folderName . 'time-off-report')->with($data);
	}
	
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData = [];
	
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		
		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_TIME_OFF_REPORT'), session()->get('user_permission')  ) ) ){
			$whereData['show_all'] = true;
		}
	
		if(!empty($request->post('search_employee_name'))){
			$whereData['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee_name'));
		}
		if(!empty($request->post('search_team'))){
			$whereData['team_record'] = (int)Wild_tiger::decode($request->post('search_team'));
		}
		if(!empty($request->post('search_time_off_from_date'))){
			$whereData['time_off_from_date'] = trim($request->post('search_time_off_from_date'));
		}
		if(!empty($request->post('search_time_off_to_date'))){
			$whereData['time_off_to_date'] = trim($request->post('search_time_off_to_date'));
		}
		
		if(!empty($request->post('search_time_off_back_from_date'))){
			$whereData['time_off_back_from_date'] = trim($request->post('search_time_off_back_from_date'));
		}
		if(!empty($request->post('search_time_off_back_to_date'))){
			$whereData['time_off_back_to_date'] = trim($request->post('search_time_off_back_to_date'));
		}
		
		if(!empty($request->post('search_leave_type'))){
			$whereData['time_off_type'] = trim($request->post('search_leave_type'));
		}
		if(!empty($request->post('search_leave_status'))){
			$whereData['time_off_status'] = [$request->post('search_leave_status')];
		}
		if( ( !empty($request->post('search_employment_status') ) )){
    		$whereData['employment_status'] =  $request->post('search_employment_status') ;
    	}
		
		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
		if ($exportAction == config('constants.EXCEL_EXPORT')) {
			$finalExportData = [];
			
			$getExportRecordDetails = $this->crudModel->getRecordDetails($whereData, $likeData);
				
			if (!empty($getExportRecordDetails)) {
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail) {
					$fromTime[] = (!empty($getExportRecordDetail->t_from_time) ? $getExportRecordDetail->t_from_time :'');
					$toTime[] = (!empty($getExportRecordDetail->t_to_time) ? $getExportRecordDetail->t_to_time :'');
					$totalHours = array_diff($fromTime, $toTime);
					$diffBetweenTimes = differenceTimeAndHours( $getExportRecordDetail->t_from_time , $getExportRecordDetail->t_to_time );
					$diffBetweenBackTime = "";
					if( (!empty($getExportRecordDetail->t_from_back_time)) && (!empty($getExportRecordDetail->t_to_back_time)) ){
						$diffBetweenBackTime = differenceTimeAndHours( $getExportRecordDetail->t_from_back_time , $getExportRecordDetail->t_to_back_time );
					}  
					
					$rowExcelData = [];
					$rowExcelData['sr_no'] = ++$excelIndex;
					$rowExcelData['employee_name'] = (!empty($getExportRecordDetail->employeeInfo->v_employee_full_name) ? $getExportRecordDetail->employeeInfo->v_employee_full_name :'');
					$rowExcelData['employee_code'] = (!empty($getExportRecordDetail->employeeInfo->v_employee_code) ? $getExportRecordDetail->employeeInfo->v_employee_code :'');
					$rowExcelData['contact_number'] = (!empty($getExportRecordDetail->employeeInfo->v_contact_no) ? $getExportRecordDetail->employeeInfo->v_contact_no :'');
					$rowExcelData['team'] = ( isset($getExportRecordDetail->employeeInfo->teamInfo->v_value) && !empty($getExportRecordDetail->employeeInfo->teamInfo->v_value) ? $getExportRecordDetail->employeeInfo->teamInfo->v_value : '' );
					$rowExcelData['date'] = (!empty($getExportRecordDetail->dt_time_off_date) ? convertDateFormat($getExportRecordDetail->dt_time_off_date,'d.m.Y') :'');
					$rowExcelData['from_time_-_to_time'] = (!empty($getExportRecordDetail->t_from_time) ? clientTime($getExportRecordDetail->t_from_time) .(!empty($getExportRecordDetail->t_to_time) ? ' - ' .clientTime($getExportRecordDetail->t_to_time) : '' ) :'');
					$rowExcelData['no._of_hours'] = (!empty($diffBetweenTimes) ? ($diffBetweenTimes) : '' );
					$rowExcelData['time_back_date'] = (!empty($getExportRecordDetail->dt_time_off_back_date) ? convertDateFormat($getExportRecordDetail->dt_time_off_back_date,'d.m.Y') :'');
					$rowExcelData['back_from_time_-_back_to_time'] = (!empty($getExportRecordDetail->t_from_back_time) ? clientTime($getExportRecordDetail->t_from_back_time) .(!empty($getExportRecordDetail->t_to_back_time) ? ' - ' .clientTime($getExportRecordDetail->t_to_back_time) : '' ) :'');
					$rowExcelData['time_back_no._of_hours'] = (!empty($diffBetweenBackTime) ? ($diffBetweenBackTime) : '' );
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['requested_on'] = (!empty($getExportRecordDetail->dt_created_at) ? convertDateFormat($getExportRecordDetail->dt_created_at,'d.m.Y') :'');
					$rowExcelData['status'] = (!empty($getExportRecordDetail->e_status) ? $getExportRecordDetail->e_status :'');
					$rowExcelData['action_taken_by'] = (!empty($getExportRecordDetail->approvedByInfo->v_name) ? $getExportRecordDetail->approvedByInfo->v_name :'');
					$rowExcelData['requested_by'] = (!empty($getExportRecordDetail->createdInfo->v_name) ? $getExportRecordDetail->createdInfo->v_name :'');
					$rowExcelData['action_taken_on'] = (!empty($getExportRecordDetail->dt_approved_at) ? convertDateFormat($getExportRecordDetail->dt_approved_at,'d.m.Y') .(!empty($getExportRecordDetail->dt_approved_at) ? ' '. clientTime($getExportRecordDetail->dt_approved_at) : '' ) :'');
	
					$finalExportData[] = $rowExcelData;
	
				}
			}
	
			if (!empty($finalExportData)) {
	
				$fileName = trans('messages.export-module-file-name', ['moduleName' => $this->moduleName ]);
	
				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.time-off-report')]);
				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
			} else {
	
				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
			}
	
			return Response::json($response);
			die;
		}
		$paginationData = [];
	
		if ($page == $this->defaultPage) {
				
			$totalRecords = count($this->crudModel->getRecordDetails( $whereData , $likeData ));
	
	
			$lastpage = ceil($totalRecords / $this->perPageRecord);
	
			$paginationData['current_page'] = $this->defaultPage;
	
			$paginationData['per_page'] = $this->perPageRecord;
	
			$paginationData['last_page'] = $lastpage;
		}
	
		if ($page == $this->defaultPage) {
			$whereData['offset'] = 0;
			$whereData['limit'] = $this->perPageRecord;
	
		} else if ($page > $this->defaultPage) {
			$whereData['offset'] = ($page - 1) * $this->perPageRecord;
			$whereData['limit'] = $this->perPageRecord;
		}
	
		$data['recordDetails'] = $this->crudModel->getRecordDetails( $whereData, $likeData );
	
		if(isset($totalRecords)){
			$data ['totalRecordCount'] = $totalRecords;
		}
		$data['pagination'] = $paginationData;
	
		$data['page_no'] = $page;
	
		$data['perPageRecord'] = $this->perPageRecord;
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'time-off-report/time-off-report-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function timeOffApprove(Request $request){
		
		$data = $whereData = [];
		$timeOffReportId = (!empty($request->post('time_off_id')) ? (int)Wild_tiger::decode($request->post('time_off_id')) : 0);
		$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0);
		if(!empty($timeOffReportId)){
			//$whereData['employee_id'] = $employeeId;
			$whereData['master_id'] = $timeOffReportId;
			$whereData['singleRecord'] = true;
			if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){
				$whereData['show_all'] = true;
			}
			$employeeRecordInfo = $this->crudModel->getRecordDetails($whereData);
			if(!empty($employeeRecordInfo)){
				
				$fromTime = (!empty($employeeRecordInfo->t_from_time) ? ($employeeRecordInfo->t_from_time) :'');
				$toTime = (!empty($employeeRecordInfo->t_to_time) ? ($employeeRecordInfo->t_to_time) :'');
				$data['employeeRecordInfo'] = $employeeRecordInfo;
				$data['totalHour'] = differenceTimeAndHours($fromTime,$toTime);
				
				$backFromTime = (!empty($employeeRecordInfo->t_from_back_time) ? ($employeeRecordInfo->t_from_back_time) :'');
				$backToTime = (!empty($employeeRecordInfo->t_to_back_time) ? ($employeeRecordInfo->t_to_back_time) :'');
				
				$data['totalBackHour'] = "";
				if(!empty($backFromTime) && (!empty($backToTime))){
					$data['totalBackHour'] = differenceTimeAndHours($backFromTime,$backToTime);
				}
				
				$status = (!empty($request->post('status')) ? trim($request->post('status')) : null );
				$data['requestStatus'] = $status;
				
				$html = view ($this->folderName . 'add-time-off-approve')->with ( $data )->render();
				echo $html;die;
				
			}
		}
		
		
	}
	public function timeOffSummaryIndex(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		$data = [];
		
		$startDate = monthStartDate();
		$endDate = monthEndDate();
		
		$data = $this->getTimeOffSummaryDetails( $startDate , $endDate );
		$data['pageTitle'] = trans('messages.time-off-detailed-summary');
		$data['startDate'] = $startDate;
		$data['endDate'] = $endDate;
		
		return view( $this->folderName . 'time-off-summary')->with($data);
	}
	public function timeOffSummaryfilter(Request $request){
	
		$startDate = (!empty($request->post('timeoff_filter_from_date')) ? dbDate($request->post('timeoff_filter_from_date')) : monthStartDate() );
		$endDate = (!empty($request->post('timeoff_filter_to_date')) ? dbDate($request->post('timeoff_filter_to_date')) : monthStartDate() );
		$data = $this->getTimeOffSummaryDetails( $startDate , $endDate );
		$html = view( config('constants.AJAX_VIEW_FOLDER') .'time-off-report/time-off-summary-list' )->with($data)->render();
		echo $html;die;
	}
	private function getTimeOffSummaryDetails( $startDate , $endDate ){
		$where = [];
		$where['time_off_from_date'] = $startDate;
		$where['time_off_to_date'] = $endDate;
		$where['time_off_status'] = [ config('constants.APPROVED_STATUS') , config('constants.PENDING_STATUS')  ];
		$where['employment_status'] = config('constants.WORKING_EMPLOYMENT_STATUS');
		
		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_TIME_OFF_SUMMARY'), session()->get('user_permission')  ) ) ){
			$where['show_all'] = true;
		}
		
		$allTimeOffSummaryDetails = $this->crudModel->getRecordDetails($where);
		
		$timeOffSummaryDetails = $uniqueEmployeeIds = [];
		if(!empty($allTimeOffSummaryDetails)){
			foreach ($allTimeOffSummaryDetails as $allTimeOffSummaryDetail){
				$fromTime = (!empty($allTimeOffSummaryDetail->t_from_time) ? $allTimeOffSummaryDetail->t_from_time :'');
				$toTime = (!empty($allTimeOffSummaryDetail->t_to_time) ? $allTimeOffSummaryDetail->t_to_time :'');
				$diffrenceIntoSecond = diffBetweenTimeIntoSecond($toTime,$fromTime);
				
				if(in_array($allTimeOffSummaryDetail->i_employee_id , $uniqueEmployeeIds) ){
					$timeOffSummaryDetails[$allTimeOffSummaryDetail->i_employee_id]['timeOffDuration'] += $diffrenceIntoSecond;
					$timeOffSummaryDetails[$allTimeOffSummaryDetail->i_employee_id]['timeOffOccurence'] += 1;
				} else {
					$rowData = [];
					$rowData['profilePic'] = ( isset($allTimeOffSummaryDetail->employeeInfo->v_profile_pic) ? $allTimeOffSummaryDetail->employeeInfo->v_profile_pic : '' );
					$rowData['employeeName'] = (!empty($allTimeOffSummaryDetail->employeeInfo->v_employee_full_name) ? $allTimeOffSummaryDetail->employeeInfo->v_employee_full_name :'');
					$rowData['designationName'] = ( isset($allTimeOffSummaryDetail->employeeInfo->designationInfo->v_value) ? $allTimeOffSummaryDetail->employeeInfo->designationInfo->v_value : '' );
					$rowData['timeOffDuration'] = $diffrenceIntoSecond;
					$rowData['timeOffOccurence'] = 1;
					$timeOffSummaryDetails[$allTimeOffSummaryDetail->i_employee_id] = $rowData;
					$uniqueEmployeeIds[] = $allTimeOffSummaryDetail->i_employee_id;
				}
			}
		}
		
		if(!empty($timeOffSummaryDetails)){
			array_multisort(array_column($timeOffSummaryDetails, 'timeOffDuration'), SORT_DESC, $timeOffSummaryDetails);
		}
		
		$result = [];
		$result['timeOffSummaryDetails'] = $timeOffSummaryDetails;
		$result['searchDateRange'] = convertDateFormat($startDate,  'd/m/Y') . ' - ' . convertDateFormat($endDate,  'd/m/Y');
		return $result;
	}
	
	public function showTimeoffNotificationRecord( $notiRecordId = null ,  $recordId = null){
	
		if(!empty($notiRecordId)){
			$notiRecordId = (int)Wild_tiger::decode($notiRecordId);
			if( $notiRecordId > 0 ){
				$updateNotificationData = [];
				$updateNotificationData['t_read_notification'] = 1;
				$updateNotificationData['dt_read_notification_at'] = date('Y-m-d H:i:s');
				
				$this->crudModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateNotificationData , [ 'i_id' =>$notiRecordId , 't_read_notification' => 0 ] );
			}
		}
		
		if(!empty($recordId)){
			$recordId = (int)Wild_tiger::decode($recordId);
			if( $recordId > 0 ){
				session()->flash('notification_timeoff_id' , $recordId);
			}
		}
		return redirect( config('constants.TIME_OFF_REPORT_URL') );
	}
	
	public function viewTodayAdjustment(){
		session()->flash('view_today_adjustment' , date('Y-m-d') );
		return redirect( config('constants.TIME_OFF_REPORT_URL') );
	}
}
