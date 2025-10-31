<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\MyLeaveModel;
use App\EmployeeModel;
use App\LeaveTypeMasterModel;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\Response;
use App\LookupMaster;
class LeaveReportController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->moduleName = trans('messages.leave-report');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'leave-report/' ;
		$this->redirectUrl = config('constants.LEAVE_REPORT_URL');
		$this->crudModel = new MyLeaveModel();
	}
	public function index($employeeId = null){
		
		$data = [];
		$data['pageTitle'] = trans('messages.leave-report');
		$page = $this->defaultPage;
		$employeeWhere = [];
		$employeeCrudModel = new EmployeeModel();
		
		$allPermissionId = config('permission_constants.ALL_LEAVE_REPORT');
		$data['allPermissionId'] = $allPermissionId;
		
		#store pagination data array
		$whereData = $paginationData = [];
	
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$whereData['show_all'] = true;
		}
		
		$customListing = false;
		$notificationRecordId = ( session()->has('notification_leave_id') ? session()->get('notification_leave_id') : 0 );
		
		if(!empty($employeeId)){
			$employeeId = (int)Wild_tiger::decode($employeeId);
			if( $employeeId > 0 ){
				$customListing = true;
				$whereData['employee_id'] = $employeeId;
				$data['selectedEmployeeId'] = $employeeId;
			}
		}
		
		$notificationRecordId = ( session()->has('notification_leave_id') ? session()->get('notification_leave_id') : 0 );
		
		$viewPendingLeaveEmployeeId = ( session()->has('view_pending_leave_employee') ? session()->get('view_pending_leave_employee') : 0 );
		$viewPendingLeaveTeamId = ( session()->has('view_pending_leave_team') ? session()->get('view_pending_leave_team') : 0 );
		$viewPendingLeaveDesignationId = ( session()->has('view_pending_leave_designation') ? session()->get('view_pending_leave_designation') : 0 );
		$viewPendingLeaveStartDate = ( session()->has('view_pending_leave_start_date') ? session()->get('view_pending_leave_start_date') : null );
		$viewPendingLeaveEndDate = ( session()->has('view_pending_leave_end_date') ? session()->get('view_pending_leave_end_date') : null );
		
		$viewTodayLeave = ( session()->has('view_today_leave_date') ? session()->get('view_today_leave_date') : null );
		
		$viewPendingLeave = ( session()->has('view_pending_leave') ? session()->get('view_pending_leave') : false );
		
		if( $viewPendingLeave != false ){
			$whereData['leave_status'] = [ config('constants.PENDING_STATUS')  ];
			$data['selectedLeaveStatus'] = $whereData['leave_status'];
		}
		
		if( $viewPendingLeaveEmployeeId > 0 ){
			//$customListing = true;
			//$whereData['employee_id'] = $viewPendingLeaveEmployeeId ;
		}
		if( $viewPendingLeaveTeamId > 0 ){
			//$customListing = true;
			//$whereData['employee_team'] = $viewPendingLeaveTeamId ;
		}
		if( $viewPendingLeaveDesignationId > 0 ){
			//$customListing = true;
			//$whereData['employee_designation'] = $viewPendingLeaveDesignationId ;
		}
		if(!empty($viewPendingLeaveStartDate)){
			$customListing = true;
			$whereData['leave_from_date'] = attendanceStartDate(date('m' , strtotime($viewPendingLeaveStartDate) ), date('Y' , strtotime($viewPendingLeaveStartDate) ));
			$data['startDate'] = $whereData['leave_from_date'];
		}
		
		if(!empty($viewPendingLeaveStartDate)){
			$customListing = true;
			$whereData['leave_to_date'] = attendanceEndDate(date('m' , strtotime($viewPendingLeaveStartDate) ), date('Y' , strtotime($viewPendingLeaveStartDate) ));
			$data['endDate'] = $whereData['leave_to_date']; 
		}
		
		if(!empty($viewTodayLeave)){
			$customListing = true;
			$whereData['leave_from_date'] = $viewTodayLeave;
			$whereData['leave_to_date'] = $viewTodayLeave;
			$whereData['leave_status'] = [ config('constants.PENDING_STATUS') , config('constants.APPROVED_STATUS') ];
			$data['startDate'] = $whereData['leave_from_date'];
			$data['endDate'] = $whereData['leave_to_date'];
			
		}
		
		
		if( $notificationRecordId > 0 ){
			$customListing = true;
			$whereData['master_id'] = $notificationRecordId;
		}
		## bydefault relived vala record nai aave 
		$whereData['employment_relieved_status'] = [config('constants.RELIEVED_PROBATION_EMPLOYMENT_STATUS')];
		
		$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
		$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
		$whereData['employment_status'] = $selectedEmployeeStatus;
		
		if($customListing != false ){
			unset($data['selectedEmployeeStatus']);
			unset($whereData['employment_status']);
			unset($whereData['employment_relieved_status'] );
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
		
		//$data['employeeDetails'] = EmployeeModel::orderBy('v_employee_full_name', 'ASC')->get();
		$data['leaveTypeDetails'] = LeaveTypeMasterModel::orderBy('v_leave_type_name', 'ASC')->get();
		$data['stausInfo'] = stausInfo();
		
		if(session()->get('role') == config('constants.ROLE_USER')){
			$employeeWhere['employee_leader_name'] = (!empty(session()->get('user_employee_id')) ? session()->get('user_employee_id') : '');
			$employeeWhere['employee_login_id'] = (!empty(session()->get('user_id')) ? session()->get('user_id') : '');
		}
		
		$employeeWhere['order_by'] = [ 'v_employee_full_name'  => 'asc'];
		$employeeWhere['employment_status'] = $selectedEmployeeStatus;
		
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$employeeWhere['show_all'] = true;
		}
		
		$data['employeeDetails'] = $employeeCrudModel->getRecordDetails($employeeWhere);
		$data['teamRecordDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
		$data['employmentStatusInfo'] = employmentStatusMaster();
		
		return view( $this->folderName . 'leave-report')->with($data);
	}
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData = [];
	
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		
		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_LEAVE_REPORT'), session()->get('user_permission')  ) ) ){
			$whereData['show_all'] = true;
		}
		
		if(!empty($request->post('search_employee_name'))){
			$whereData['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee_name'));
		}
		if(!empty($request->post('search_team'))){
			$whereData['team_record'] = (int)Wild_tiger::decode($request->post('search_team'));
		}
		if(!empty($request->post('search_leave_from_date'))){
			$whereData['leave_from_date'] = trim($request->post('search_leave_from_date'));
		}
		if(!empty($request->post('search_leave_to_date'))){
			$whereData['leave_to_date'] = trim($request->post('search_leave_to_date'));
		}
		if(!empty($request->post('search_leave_type'))){
			$whereData['leave_type'] = (int)Wild_tiger::decode($request->post('search_leave_type'));
		}
		if(!empty($request->post('search_leave_status'))){
			$whereData['leave_status'] = [$request->post('search_leave_status')];
		}
		if(!empty($request->post('search_leave_duration'))){
			$whereData['leave_duration'] = trim($request->post('search_leave_duration'));
		}
		## employment status filter
		if(!empty($request->post('search_employment_status'))){
			$whereData['employment_status'] =  $request->post('search_employment_status');
		} 
		
		if(!empty($request->input('search_auto_approve_leave'))){
			switch($request->input('search_auto_approve_leave')){
				case config('constants.SELECTION_YES'):
					$whereData['auto_approve_leave'] = 1;
					break;
				case config('constants.SELECTION_NO'):
					$whereData['auto_approve_leave'] = 0;
					break;
			}
		}
		
		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
		if ($exportAction == config('constants.EXCEL_EXPORT')) {
			$finalExportData = [];
			$getExportRecordDetails = $this->crudModel->getRecordDetails($whereData, $likeData);
			
			if (!empty($getExportRecordDetails)) {
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail) {
					$rowExcelData = [];
					$rowExcelData['sr_no'] = ++$excelIndex;
					$rowExcelData['employee_name'] = ( !empty($getExportRecordDetail->employeeInfo->v_employee_full_name) ? ($getExportRecordDetail->employeeInfo->v_employee_full_name) : '' );
					$rowExcelData['employee_code'] = ( !empty($getExportRecordDetail->employeeInfo->v_employee_code) ? ($getExportRecordDetail->employeeInfo->v_employee_code) : '' );
					$rowExcelData['contact_number'] = (!empty($getExportRecordDetail->employeeInfo->v_contact_no) ? $getExportRecordDetail->employeeInfo->v_contact_no :'');
					$rowExcelData['team'] = ( isset($getExportRecordDetail->employeeInfo->teamInfo->v_value) && !empty($getExportRecordDetail->employeeInfo->teamInfo->v_value) ? $getExportRecordDetail->employeeInfo->teamInfo->v_value : '' );
					$rowExcelData['leave_dates'] = ( isset($getExportRecordDetail->dt_leave_from_date) ? clientDate($getExportRecordDetail->dt_leave_from_date) .(!empty($getExportRecordDetail->e_from_duration) ? ' (' .$getExportRecordDetail->e_from_duration .')' .(!empty($getExportRecordDetail->dt_leave_to_date) ? ' - ' .clientDate($getExportRecordDetail->dt_leave_to_date) .(!empty($getExportRecordDetail->e_to_duration) ? ' (' .$getExportRecordDetail->e_to_duration. ')' : ''):'') :''):'' );
					$rowExcelData['no_._of_days'] = (!empty($getExportRecordDetail->d_no_days) ? $getExportRecordDetail->d_no_days :'');
					$rowExcelData['leave_type'] = (!empty($getExportRecordDetail->leaveTypeInfo->v_leave_type_name) ? $getExportRecordDetail->leaveTypeInfo->v_leave_type_name :'');
					$rowExcelData['requested_on'] = (!empty($getExportRecordDetail->dt_created_at) ? clientDate($getExportRecordDetail->dt_created_at) :'');
					$rowExcelData['requested_by'] = (!empty($getExportRecordDetail->createdInfo->v_name) ? $getExportRecordDetail->createdInfo->v_name :'');
					$rowExcelData['status'] = (!empty($getExportRecordDetail->e_status) ? $getExportRecordDetail->e_status :'');
					$rowExcelData['action_taken_by'] = (!empty($getExportRecordDetail->approvedByInfo->v_name) ? $getExportRecordDetail->approvedByInfo->v_name : (  $getExportRecordDetail->t_is_auto_approve == 1  ? trans('messages.auto-approved') : '' ) );
					
					$rowExcelData['action_taken_on'] = (!empty($getExportRecordDetail->dt_approved_at) ? clientDate($getExportRecordDetail->dt_approved_at) .(!empty($getExportRecordDetail->dt_approved_at) ? ' '.clientTime($getExportRecordDetail->dt_approved_at) : '') :'');
						
					$finalExportData[] = $rowExcelData;
						
				}
			}
				
			if (!empty($finalExportData)) {
		
				$fileName = trans('messages.export-module-file-name', ['moduleName' => $this->moduleName ]);
		
				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.leave-report')]);
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'leave-report/leave-report-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function approveLeave(Request $request){
		$data = $whereData = [];
		$leaveReportId = (!empty($request->post('leave_report_id')) ? (int)Wild_tiger::decode($request->post('leave_report_id')) : 0);
		$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0);
		if(!empty($employeeId)){
			$whereData['employee_id'] = $employeeId;
			$whereData['master_id'] = $leaveReportId;
			$whereData['singleRecord'] = true;
			if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){
				$whereData['show_all'] = true;
			}
			$employeeRecordInfo = $this->crudModel->getRecordDetails($whereData);
			if(!empty($employeeRecordInfo)){
				$data['employeeRecordInfo'] = $employeeRecordInfo;
			}
		}
		$status = (!empty($request->post('status')) ? trim($request->post('status')) : null );
		$data['requestStatus'] = $status;
		$html = view ($this->folderName . 'add-approve-leave')->with ( $data )->render();
		echo $html;die;
	}
	
	public function showLeaveNotificationRecord( $notiRecordId ,  $leaveRecordId = null){
		
		if(!empty($notiRecordId)){
			$notiRecordId = (int)Wild_tiger::decode($notiRecordId);
			if( $notiRecordId > 0 ){
				$updateNotificationData = [];
				$updateNotificationData['t_read_notification'] = 1;
				$updateNotificationData['dt_read_notification_at'] = date('Y-m-d H:i:s');
		
				$this->crudModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateNotificationData , [ 'i_id' =>$notiRecordId , 't_read_notification' => 0 ] );
			}
		}
		
		if(!empty($leaveRecordId)){
			$leaveRecordId = (int)Wild_tiger::decode($leaveRecordId);
			if( $leaveRecordId > 0 ){
				session()->flash('notification_leave_id' , $leaveRecordId);
			}
		}
		return redirect( config('constants.LEAVE_REPORT_URL') );
	}
	
	public function viewPendingLeave(Request $request){
	
		if(!empty($request->input('view_pending_leave_employee'))){
			$employeeId = (int)Wild_tiger::decode($request->input('view_pending_leave_employee'));
			if(!empty($employeeId)){
				session()->flash('view_pending_leave_employee' , $employeeId );
			}
		}
	
		if(!empty($request->input('view_pending_leave_team'))){
			$teamId = (int)Wild_tiger::decode($request->input('view_pending_leave_team'));
			if(!empty($teamId)){
				session()->flash('view_pending_leave_team' , $teamId );
			}
		}
	
		if(!empty($request->input('view_pending_leave_designation'))){
			$designationId = (int)Wild_tiger::decode($request->input('view_pending_leave_designation'));
			if(!empty($designationId)){
				session()->flash('view_pending_leave_designation' , $designationId );
			}
		}
	
		if(!empty($request->input('view_pending_leave_start_date'))){
			$startDate = trim(dbDate($request->input('view_pending_leave_start_date')));
			if(!empty($startDate)){
				session()->flash('view_pending_leave_start_date' , $startDate );
			}
		}
	
		if(!empty($request->input('view_pending_leave_end_date'))){
			$endDate = trim(dbDate($request->input('view_pending_leave_end_date')));
			if(!empty($endDate)){
				session()->flash('view_pending_leave_end_date' , $endDate );
			}
		}
		session()->flash('view_pending_leave' , true );
		return redirect( config('constants.LEAVE_REPORT_URL') );
	
	
	}
	
	public function viewTodayLeave(){
		session()->flash('view_today_leave_date' , date('Y-m-d') );
		return redirect( config('constants.LEAVE_REPORT_URL') );
	}
	
	
}
