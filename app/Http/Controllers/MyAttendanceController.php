<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MyAttendanceModel;
use App\Helpers\Twt\Wild_tiger;
use App\HolidayMasterModel;
use Illuminate\Support\Facades\DB;
use App\LookupMaster;
use App\EmployeeModel;

class MyAttendanceController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->moduleName = trans('messages.my-attendance');
		$this->tableName = config('constants.EMPLOYEE_ATTENDANCE_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'my-attendance/' ;
		$this->crudModule = new MyAttendanceModel();
		$this->holidayModule = new HolidayMasterModel();
		$this->employeeId = session()->get('user_employee_id');
	}
	public function index(Request $request){
		$ajaxRequest = false;
		if($request->ajax()){
			$ajaxRequest = true;
			$this->employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		} else {
			$this->employeeId = session()->get('user_employee_id');
		}
		
		$data = [];
		$data['pageTitle'] = trans('messages.my-attendance');
		$data['employeeId'] = Wild_tiger::encode($this->employeeId);
		$data['startMonth'] = date(config('constants.DEFAULT_PHP_MONTH_FORMAT'));
		$data['endMonth'] = date(config('constants.DEFAULT_PHP_MONTH_FORMAT'));
		
		$employeeInfo = EmployeeModel::where('i_id' , $this->employeeId )->first();
		$empJoiningDate = (isset($employeeInfo->dt_joining_date) ?  date('Y-m-01' , strtotime($employeeInfo->dt_joining_date)) : null );
		$data['empJoiningDate'] = $empJoiningDate;
		
		if( $ajaxRequest != false ){
			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'my-attendance/my-attendance-main-list' )->with ( $data )->render();
			echo $html;die;
		}
		
		return view( $this->folderName . 'my-attendance')->with($data);
		
	}
	
	public function getAttendanceRecord(Request $request){
		
		$whereData = [];
		$attendanceStartDate = (!empty($request->input('attendance_filter_from_month')) ? dbDate( $request->input('attendance_filter_from_month') ) :'');
		$attendanceEndDate = (!empty($request->input('attendance_filter_to_month')) ? dbDate( $request->input('attendance_filter_to_month') ) :'');
		$attendanceEndDate = date('Y-m-t' , strtotime($attendanceEndDate));
		$selectionStartDate = $attendanceStartDate;
		$selectionEndDate = $attendanceEndDate;
		//var_dump($attendanceStartDate);
		//var_dump($attendanceEndDate);
		//$startDate = \DateTime::createFromFormat(config('constants.DEFAULT_PHP_MONTH_FORMAT'), $attendanceStartDate);
		//$selectionStartDate = $startDate->format('Y-m-01');
		$whereData['attendance_from_month'] = $selectionStartDate;
		
		//$endDate = \DateTime::createFromFormat(config('constants.DEFAULT_PHP_MONTH_FORMAT'), $attendanceEndDate);
		//$selectionEndDate = $endDate->format('Y-m-t');
		$whereData['attendance_to_month'] = $selectionEndDate;
		$html = "";
		//echo "<pre>";print_r($whereData);
		$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : session()->get('user_employee_id') );
		
		
		$whereData['employee_id'] = $employeeId;
		//var_dump($selectionStartDate);
		//var_dump($selectionEndDate);
		$attendanceRecordDetails = $this->crudModule->getRecordDetails($whereData);
		$allMonths = monthListBetweenDates($selectionStartDate , $selectionEndDate );
		//echo "<pre>";print_r($allMonths);die;
		$totalDays = array_sum(array_column($allMonths, 'total'));
		
		$totalWeekOfHolidayDays = $totalPresentDays = $totalHalfLeaveDays = $totalAbsentDays = $totalSuspendDays = $totalAdjustmentDays = 0;
		$totalApproveLeaveCount = $totalApproveHalfLeaveCount = 0;
		$html = "";
		
		//echo "<pre>";print_r($allMonths);die;
		$totalDisplayDays = 0;
		if(!empty($allMonths)){
			//$html .= view ( config('constants.ADMIN_FOLDER') .'attendance-calendar' )->render();
			foreach($allMonths as $key => $allMonth){
				
				$recordInfo = [];
				$month = date("m",strtotime($allMonth['month']));
				$year = date("Y",strtotime($allMonth['month']));
				
				$getAttendanceInfo = $this->getAttendanceInfo($month, $year, $employeeId );
				$recordInfo = ( isset($getAttendanceInfo['attendanceData']) ? $getAttendanceInfo['attendanceData'] : [] );
				
				
				
				$recordInfo['allDates'] = ( isset($recordInfo['allDates']) ? $recordInfo['allDates'] : [] );
				$recordInfo['presentDates'] = ( isset($recordInfo['presentDates']) ? $recordInfo['presentDates'] : [] );
				$recordInfo['absentDates'] = ( isset($recordInfo['absentDates']) ? $recordInfo['absentDates'] : [] );
				$recordInfo['holidatDates'] = ( isset($recordInfo['holidatDates']) ? $recordInfo['holidatDates'] : [] );
				$recordInfo['weekOffDates'] = ( isset($recordInfo['weekOffDates']) ? $recordInfo['weekOffDates'] : [] ); 
				$recordInfo['presentDayCount'] = ( isset($recordInfo['presentDayCount']) ? $recordInfo['presentDayCount'] : 0 );
				$recordInfo['onlyPresentCount'] = ( isset($recordInfo['onlyPresentCount']) ? $recordInfo['onlyPresentCount'] : 0 );
				$recordInfo['absentDayCount'] = ( isset($recordInfo['absentDayCount']) ? $recordInfo['absentDayCount'] : 0 );
				$recordInfo['approvedHalfLeaveDates'] = ( isset($recordInfo['approvedHalfLeaveDates']) ? $recordInfo['approvedHalfLeaveDates'] : [] );
				$recordInfo['approvedLeaveDates'] = ( isset($recordInfo['approvedLeaveDates']) ? $recordInfo['approvedLeaveDates'] : [] );
				$recordInfo['suspendDates'] = ( isset($recordInfo['suspendDates']) ? $recordInfo['suspendDates'] : [] );
				$recordInfo['adjustmentDates'] = ( isset($recordInfo['adjustmentDates']) ? $recordInfo['adjustmentDates'] : [] );
				$recordInfo['halfLeaveDates'] = ( isset($recordInfo['halfLeaveDates']) ? $recordInfo['halfLeaveDates'] : [] );
				$recordInfo['calendarViewUnpaidHalfLeaveDates'] = ( isset($recordInfo['calendarViewUnpaidHalfLeaveDates']) ? $recordInfo['calendarViewUnpaidHalfLeaveDates'] : [] );
				$recordInfo['salaryPaidDayCount'] = ( isset($recordInfo['salaryPaidDayCount']) ? $recordInfo['salaryPaidDayCount'] : 0 );
				
				
				
				//echo "<pre>";print_r($recordInfo);die;
				
				
				if( isset($recordInfo['allDates']) && (!empty($recordInfo['allDates'])) ) {
					$totalDisplayDays += count($recordInfo['allDates']);
				}
				
				$recordInfo['totalDays'] = $totalDays;
				$recordInfo['totalDisplayDays'] = $totalDisplayDays;
				
				
				$recordInfo['calendarStartDate'] = attendanceStartDate( $month , $year);
				$recordInfo['calendarEndDate'] = attendanceEndDate( $month , $year);
				
				if(!empty($recordInfo['onlyPresentCount'])){
					$totalPresentDays += ($recordInfo['onlyPresentCount']);
				}
				
				if(!empty($recordInfo['absentDayCount'])){
					$totalAbsentDays += ($recordInfo['absentDayCount']);
				}
				
				if(!empty($recordInfo['halfLeaveDates'])){
					$totalHalfLeaveDays += count($recordInfo['halfLeaveDates']);
				}
				
				if(!empty($recordInfo['suspendDates'])){
					$totalSuspendDays += count($recordInfo['suspendDates']);
				}
				
				if(!empty($recordInfo['adjustmentDates'])){
					$totalAdjustmentDays += count($recordInfo['adjustmentDates']);
				}
				
				if(!empty($recordInfo['holidatDates'])){
					$totalWeekOfHolidayDays += count($recordInfo['holidatDates']);
				}
				
				if(!empty($recordInfo['weekOffDates'])){
					$totalWeekOfHolidayDays += count($recordInfo['weekOffDates']);
				}
				
				if(!empty($recordInfo['approvedLeaveDates'])){
					$totalApproveLeaveCount += count($recordInfo['approvedLeaveDates']);
				}
				
				if(!empty($recordInfo['approvedHalfLeaveDates'])){
					$totalApproveHalfLeaveCount += count($recordInfo['approvedHalfLeaveDates']);
				}
				
				$recordInfo['totalPresentDays'] = $totalPresentDays ;
				$recordInfo['totalWeekOfHolidayDays'] = $totalWeekOfHolidayDays;
				$recordInfo['totalHalfLeaveDays'] = $totalHalfLeaveDays;
				$recordInfo['totalAbsentDays'] = $totalAbsentDays;
				$recordInfo['totalSuspendDays'] = $totalSuspendDays;
				$recordInfo['totalAdjustmentDays'] = $totalAdjustmentDays;
				$recordInfo['totalApproveHalfLeaveCount'] = $totalApproveHalfLeaveCount;
				$recordInfo['totalApproveLeaveCount'] = $totalApproveLeaveCount;
				
				//echo "<pre>";print_r($recordInfo);die;
				$recordInfo['month'] = $month;
				$recordInfo['year'] = $year;
				$html .= view (config('constants.AJAX_VIEW_FOLDER') . 'my-attendance/my-attendance-calender-month-wise' )->with ( $recordInfo )->render();
				
				//echo $html;die;
				
			}
		}
				
		echo $html;die;
		
	}
	
	
	public function editAttendance(){
		
		if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			//return redirect('access-denied');
		}
		
		//
		$data = [];
		$data['pageTitle'] = trans('messages.manage-attendance-manually');
		
		$allPermissionId = config('permission_constants.ALL_MANAGE_ATTENDANCE_LIST');
		$data['allPermissionId'] = $allPermissionId;
		
		$page = $this->defaultPage ;
		$startDate = date('Y-m-d');
		$endDate = date('Y-m-d');
		
		$data['startDate'] = $startDate;
		$data['endDate'] = $endDate;
		
		$whereData['attendance_from_month'] = $startDate;
		$whereData['attendance_to_month'] = $endDate;
		$whereData['editAttendanceSreen'] = true;
		
		$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
		$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
		$whereData['employment_status'] = $selectedEmployeeStatus;
		
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$whereData['show_all'] = true;
		}
		
		$paginationData = [];
		#get pagination data for first page
		if($page == $this->defaultPage ){
			
			$totalRecords = count($this->crudModule->getManageAttendanceRecordDetails($whereData));
			
			$lastPage = ceil($totalRecords/$this->perPageRecord);
		
			$paginationData['current_page'] = $this->defaultPage;
		
			$paginationData['per_page'] = $this->perPageRecord;
		
			$paginationData ['last_page'] = $lastPage;
		
		}
		$whereData ['limit'] = $this->perPageRecord;
		
		$data['recordDetails'] = $this->crudModule->getManageAttendanceRecordDetails( $whereData );
		$data['pageNo'] = $page;
		$data['perPageRecord'] = $this->perPageRecord;;
		$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
		//$data['employeeDetails'] = EmployeeModel::where('t_is_deleted',0)->orderBy('v_employee_full_name', 'ASC')->get();
		
		$employeeWhere = [];
		$employeeWhere['employment_status'] = $selectedEmployeeStatus;
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$employeeWhere['show_all'] = true;
		}
		$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
		
		$data['attendanceStatusDetails'] = attendanceStatus();
		$data['totalRecordCount'] = $totalRecords;
		$data['pagination'] = $paginationData;
		//echo "<pre>";print_r($data['recordDetails']);die;
		
		$monthAllDates = getDatesFromRange($startDate,$endDate);
		
		$suspendWhere = [];
		$suspendWhere['startDate'] = $startDate;
		$suspendWhere['endDate'] = $endDate;
		$suspendWhere['monthAllDates'] = $monthAllDates;
		
		$data['employeeWiseSuspendRecordDetails'] = $this->getAllSuspendDateWiseRecords( $suspendWhere );
		return view( $this->folderName . 'edit-attendance')->with($data);
	}
	
	public function filterEditAttendance(Request $request){
		//variable defined
		$whereData = $likeData = [];
		 
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		 
		if(!empty($request->post('search_employee'))){
			$whereData['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee'));
		}
		
		if(!empty($request->post('search_team'))){
			$whereData['team'] = (int)Wild_tiger::decode($request->post('search_team'));
		}
		$startDate = $endDate = null;
		if(!empty($request->post('search_from_date'))){
			$startDate = $request->post('search_from_date');
			$whereData['attendance_from_month'] = ($request->post('search_from_date'));
		}
		
		if(!empty($request->post('search_to_date'))){
			$endDate = $request->post('search_to_date');
			$whereData['attendance_to_month'] = ($request->post('search_to_date'));
		}
		
		if(!empty($request->post('search_attendance_status'))){
			$whereData['status'] = trim($request->post('search_attendance_status'));
		}
		
		if(!empty($request->post('search_employment_status'))){
			$whereData['employment_status'] = trim($request->post('search_employment_status'));
		}
		
		if(!empty($request->post('search_manually_change_status'))){
			$whereData['manually_change_status'] = trim($request->post('search_manually_change_status'));
		}
		
		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_MANAGE_ATTENDANCE_LIST'), session()->get('user_permission')  ) ) ){
			$whereData['show_all'] = true;
		}
		
		$paginationData = [];
		
		$whereData['editAttendanceSreen'] = true;
		
		if ($page == $this->defaultPage) {
		
			$totalRecords = count($this->crudModule->getManageAttendanceRecordDetails( $whereData , $likeData ));
		
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
		
		
		$data['recordDetails'] = $this->crudModule->getManageAttendanceRecordDetails( $whereData, $likeData );
		$monthAllDates = [];
		if( (!empty($startDate)) && (!empty($endDate)) ){
			$monthAllDates = getDatesFromRange($startDate,$endDate);
		}
		
		
		$suspendWhere = [];
		$suspendWhere['startDate'] = $startDate;
		$suspendWhere['endDate'] = $endDate;
		$suspendWhere['monthAllDates'] = $monthAllDates;
		//echo "<pre>";print_r($suspendWhere);
		$data['employeeWiseSuspendRecordDetails'] = $this->getAllSuspendDateWiseRecords( $suspendWhere ); 
		
		if(isset($totalRecords)){
			$data ['totalRecordCount'] = $totalRecords;
		}
		
		$data['pagination'] = $paginationData;
		 
		$data['pageNo'] = $page;
		
		$data['perPageRecord'] = $this->perPageRecord;
		
		$data['attendanceStatusDetails'] = attendanceStatus();
		 
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'my-attendance/edit-attedance-list' )->with ( $data )->render();
		 
		echo $html;die;
	}
	
	public function updateAttendance(Request $request){
		
		$displayRecordEncodeIds = ($request->has('display_record_ids') ? explode("," , $request->input('display_record_ids') ) : [] );
		
		$displayRecordIds = [];
		if(!empty($displayRecordEncodeIds)){
			$displayRecordIds = array_map(function($displayRecordEncodeId){
				return (int)($displayRecordEncodeId);
			}, $displayRecordEncodeIds);
		}
		
		$whereData = $likeData = [];
		if(!empty($request->post('search_employee'))){
			$whereData['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee'));
		}
		
		if(!empty($request->post('search_team'))){
			//$whereData['designation'] = (int)Wild_tiger::decode($request->post('search_team'));
		}
		
		if(!empty($request->post('search_from_date'))){
			$whereData['attendance_from_month'] = ($request->post('search_from_date'));
		}
		
		if(!empty($request->post('search_to_date'))){
			$whereData['attendance_to_month'] = ($request->post('search_to_date'));
		}
		
		$paginationData = [];
			
		$recordDetails = $this->crudModule->getRecordDetails( $whereData, $likeData );
		
		if(!empty($recordDetails)){
			
			$result = false;
     		DB::beginTransaction();
     		
     		try{
     			foreach($recordDetails as $recordDetail ){
     			
     				$rowData = [];
     				$rowData['t_start_time'] = (!empty($request->input('start_time_'.$recordDetail->i_id)) ? dbTime($request->input('start_time_'.$recordDetail->i_id)) : null );
     				$rowData['t_end_time'] = (!empty($request->input('end_time_'.$recordDetail->i_id)) ? dbTime($request->input('end_time_'.$recordDetail->i_id)) : null );
     				$rowData['e_status'] = (!empty($request->input('status_'.$recordDetail->i_id)) ? trim($request->input('status_'.$recordDetail->i_id)) : null );
     				if(in_array( $recordDetail->i_id , $displayRecordIds )){
     					
     					
     					if( ( strtotime( date('H:i' , strtotime( $recordDetail->dt_matrix_start_time ) ) ) != strtotime( $rowData['t_start_time'] ) ) || ( strtotime( date('H:i' , strtotime( $recordDetail->dt_matrix_end_time ) ) )  !=  strtotime( $rowData['t_end_time'] ) ) ){
     						$rowData['t_manually_change'] = 1;
     					} else {
     						$rowData['t_manually_change'] = 0;
     					}
     					//echo "<pre>";print_r($rowData);
     					$this->crudModule->updateTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData, ['i_id' => $recordDetail->i_id ] );
     				}
     				
     			}
     			$result = true;
     		}catch(\Exception $e){
     			DB::rollback();
     			Wild_tiger::setFlashMessage('danger', $e->getMessage());
     			return redirect()->back();
     		}
     		//die("ss");
     		$successMessage =  trans('messages.success-update',['module'=> trans('messages.attendance')]);
     		$errorMessages = trans('messages.error-update',['module'=> trans('messages.attendance')]);
     		
     		if( $result != false ){
     			DB::commit();
     			Wild_tiger::setFlashMessage('success', $successMessage);
     		} else {
     			DB::rollback();
     			Wild_tiger::setFlashMessage('danger', $errorMessages);
     		}
     		
     		return redirect()->back();
     	}
     	Wild_tiger::setFlashMessage('danger', trans('messages.no-record-found'));
     	return redirect()->back();
	}
	
	public function attendanceSummary(){
		$data = [];
		$data = $this->attendanceSummaryInfo();
		//echo "<pre>";print_r($data);die;
		$data['teamDetails'] = LookupMaster::where('v_module_name', config('constants.TEAM_LOOKUP'))->orderBy('v_value', 'ASC')->get();
		$data['pageTitle'] = trans('messages.present-summary');
		return view($this->folderName . 'attendance-summary')->with($data);
	}
	
	public function attendanceSummaryFilter(Request $request){
		
		$data = [];
		$teamId = (!empty($request->input('search_team')) ? (int)Wild_tiger::decode($request->input('search_team')) : 0 );
		$data = $this->attendanceSummaryInfo( $teamId );
		$html = view(config('constants.AJAX_VIEW_FOLDER') . 'my-attendance/attendance-summary-filter')->with($data)->render();
		
		echo $html;
		die;
	}
	
	private function attendanceSummaryInfo( $teamId = 0 ){
		$showAllEmployee = $employeeId = 0;
		
		$allChildEmployeeIds = [];
		
		if(  ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) ||  ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_ATTENDANCE_SUMMARY'), session()->get('user_permission')  ) ) ) ){
			$showAllEmployee = 1;
		} else {
			$allChildEmployeeIds = $this->crudModule->childEmployeeIds();
			$employeeId = (!empty($allChildEmployeeIds) ? implode(',', $allChildEmployeeIds) : 0);
		}
		
		$attendanceSummaryInfo = self::CallRaw ( 'attendance_summary' , [  $teamId , $employeeId , $showAllEmployee  ]);
		
		$data['allEmployeeCount'] = ( ( isset($attendanceSummaryInfo[0][0]) && (isset($attendanceSummaryInfo[0][0]->all_emp_count)) ) ? $attendanceSummaryInfo[0][0]->all_emp_count : 0 );
		$data['leaveCount'] = ( ( isset($attendanceSummaryInfo[1][0]) && (isset($attendanceSummaryInfo[1][0]->leave_count)) ) ? $attendanceSummaryInfo[1][0]->leave_count : 0 );
		$data['adjustmentCount'] = ( ( isset($attendanceSummaryInfo[2][0]) && (isset($attendanceSummaryInfo[2][0]->adjustment_request_count)) ) ? $attendanceSummaryInfo[2][0]->adjustment_request_count : 0 );
		
		$where = [];
		$where['t_is_deleted'] = 0;
		if(!empty($teamId)){
			$where['i_team_id'] = $teamId;
		}
		
		$employeeQuery = EmployeeModel::where($where)->where('e_employment_status' , '!=' , config('constants.RELIEVED_EMPLOYMENT_STATUS'));
		
		if($showAllEmployee == 1){
			
		} else {
			//$employeeQuery->whereRaw("( i_id = '".$employeeId."' or i_leader_id = '".$employeeId."')");
			
			if(!empty($allChildEmployeeIds)){
				$employeeQuery->whereIn('i_id', $allChildEmployeeIds);
			}
		}
		
		$employeeDetails = $employeeQuery->get();
		
		$weekOffEmployees = [];
		if(!empty($employeeDetails)){
			foreach($employeeDetails as $employeeDetail){
				$employeeId = $employeeDetail->i_id;
				$year = date('Y');
				$month = date('m');
				$getEmployeeWeekOffDates = $this->getEmployeeMonthlyWeekOff( ['employeeId' => $employeeId , 'month' => $year.'-'.$month.'-01' , 'attendanceView' => true ] );
				$weekOffDates = ( isset($getEmployeeWeekOffDates['weekOffDates']) ? $getEmployeeWeekOffDates['weekOffDates'] : [] );
				if( in_array(date('Y-m-d') , $weekOffDates)){
					$weekOffEmployees[] = $employeeDetail->i_id;
				}
			}
		}
		$data['weekOffCount'] = count($weekOffEmployees);
		$data['availableCount'] = $data['allEmployeeCount'] - $data['leaveCount'] - $data['weekOffCount'];
		return $data;
		
	}
	
	public function syncAttendanceDate(Request $request){
		
		$date = (!empty($request->input('sync_attendance_date')) ? dbDate($request->input('sync_attendance_date')) : null );
		if(!empty($date)){
			
			$syncData = [];
			$syncData['dt_sync_date'] = $date;
			
			$insertSync = $this->crudModule->insertTableData(config('constants.SYNC_ATTENDANCE_HISTORY_TABLE'), $syncData);
			
			if( $insertSync > 0 ){
				$updateSyncData = [];
				$response = $this->apiAttenndance(date('dmY', strtotime($date)), true);
				//var_dump($response);die;
				$message = ( isset($response['msg']) ? $response['msg'] : null );
				if( isset($response['status']) && ( $response['status'] != false ) ){
					$updateSyncData['e_status'] = config('constants.SUCCESS_STATUS') ;
					$this->crudModule->insertTableData(config('constants.SYNC_ATTENDANCE_HISTORY_TABLE'), $updateSyncData , [ 'i_id' => $insertSync  ]);
					Wild_tiger::setFlashMessage ('success', (!empty($message) ? $message : null ) );
					return  redirect()->back();
				} else {
					$updateSyncData['e_status'] = config('constants.FAILED_STATUS') ;
					$updateSyncData['v_reason'] = $message ;
					$this->crudModule->insertTableData(config('constants.SYNC_ATTENDANCE_HISTORY_TABLE'), $updateSyncData , [ 'i_id' => $insertSync  ]);
					Wild_tiger::setFlashMessage ('danger', (!empty($message) ? $message : null ) );
					return  redirect()->back();
				}
			}
		}
		Wild_tiger::setFlashMessage ('danger', trans('messages.system-error') );
		return  redirect()->back();
	}
}
