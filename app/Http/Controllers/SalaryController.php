<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\Salary;
use App\SalaryComponentsModel;
use App\LookupMaster;
use App\EmployeeModel;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\SalaryGroupModel;
use App\SalaryGroupDetailsModel;
use App\Models\EmployeeSalaryDetailModel;
use App\Models\ReviseSalaryInfo;
use App\Models\EmployeeSalaryModel;
use App\Models\ReviseSalaryMaster;
use App\Models\SalaryInfo;
use App\MyLeaveModel;
use App\Models\AttendanceSummaryModel;
use Carbon\Exceptions\Exception;
use App\Login;
use Illuminate\Support\Facades\Response;

class SalaryController extends MasterController
{
	//
	public function __construct()
	{
		parent::__construct();
		$this->crudModel =  new Salary();
		$this->moduleName = trans('messages.salary');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->tableName = config('constants.SALARY_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER') . 'salary/';
		$this->redirectUrl = config('constants.SALARY_MASTER_URL');
		$this->employeeModel = new EmployeeModel();
	}

	public function index()
	{
		$data = [];
		$data['pageTitle'] = trans('messages.salary-calculation');
		
		$allPermissionId = config('permission_constants.ALL_SALARY_CALCULATION');
		$data['allPermissionId'] = $allPermissionId;
		
		$salaryComponentDetails = SalaryComponentsModel::where('t_is_active', 1)->orderBy('i_sequence' , 'asc')->get();
		$allEarningComponentDetails = $allDeductComponentDetails = [];
		if (!empty($salaryComponentDetails)) {
			foreach ($salaryComponentDetails as $salaryComponentDetail) {
				switch ($salaryComponentDetail->e_salary_components_type) {
					case config('constants.SALARY_COMPONENT_TYPE_EARNING'):
						$allEarningComponentDetails[] = $salaryComponentDetail;
						break;
					case config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'):
						$allDeductComponentDetails[] = $salaryComponentDetail;
						break;
				}
			}
		}
		//echo "<pre>";print_r(array_column(objectToArray($allEarningComponentDetails), 'i_id'));
		//echo "<pre>";print_r(array_column(objectToArray($allDeductComponentDetails), 'i_id'));die;
		
		$data['earningComponentDetails'] = $allEarningComponentDetails;
		$data['deductComponentDetails'] = $allDeductComponentDetails;
		
		$startMonth = ( ( date('d') >= config('constants.SALARY_CYCLE_START_DATE') ) ?  date('M-Y', strtotime("+1 Month")  ) : date('M-Y') ) ;
		$endMonth = ( ( date('d') >= config('constants.SALARY_CYCLE_START_DATE') ) ?  date('M-Y' , strtotime("+1 Month") ) : date('M-Y') ) ;
		
		$whereData = [];
		$whereData['start_month'] = $startMonth;
		$whereData['end_month'] = $endMonth;
		
		$data['designationDetails'] = LookupMaster::where('v_module_name', config('constants.DESIGNATION_LOOKUP'))->orderBy('v_value', 'ASC')->get();
		$data['teamDetails'] = LookupMaster::where('v_module_name', config('constants.TEAM_LOOKUP'))->orderBy('v_value', 'ASC')->get();
		
		$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
		
		$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
		
		$whereData['employment_status'] = $selectedEmployeeStatus;
		
		$employeeWhere = [];
		$employeeWhere['employment_status'] = $selectedEmployeeStatus;
		 
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$employeeWhere['show_all'] = true;
			$whereData['show_all'] = true;
		}
		 
		$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
		
		$data['startMonth'] = $startMonth;
		$data['endMonth'] = $endMonth;
		
		$data['recordDetails'] = $this->crudModel->getRecordDetails($whereData);
		
		$data['totalRecordCount'] = count($data['recordDetails']);
		return view($this->folderName . 'generate-salary')->with($data);
	}

	public function viewSalary(Request $request)
	{
		//$_SESSION['show_salary_info'] = config('constants.SELECTION_NO');
		//session()->put('show_salary_info',config('constants.SELECTION_NO'));
		errorLogEntry("after session" , session()->all());
		//Session::put('show_salary_info', config('constants.SELECTION_NO'));die;
		$data['pageTitle'] = trans('messages.my-salary');
		$data['showPageContent'] = false;
		
		//Session::put('show_salary_info', config('constants.SELECTION_NO') );

		if (session()->has('show_salary_info') && (session()->get('show_salary_info') == config('constants.SELECTION_YES'))) {
			//echo "s";die;
			Session::put('show_salary_info', config('constants.SELECTION_NO'));
			$data['showPageContent'] = true;
		}
		
		if( config('constants.SHOW_SALARY_WITHOUT_PASSWORD') == 1 ){
			$data['showPageContent'] = true;
		}

		$ajaxRequest = false;

		if ($request->ajax()) {
			$ajaxRequest = true;
			$employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0);
		} else {
			$employeeId = session()->get('user_employee_id');
		}

		$getSalaryWhere =  [];
		$getSalaryWhere['i_id'] = $employeeId;
		$getSalaryDetails = EmployeeModel::with(['salaryInfo', 'salaryDetail', 'salaryDetail.salaryComponentInfo'])->where($getSalaryWhere)->first();
		$getSalaryDetails = EmployeeSalaryModel::where('i_employee_id', $employeeId)->first();

		if (in_array(session()->get('role'), [config('constants.ROLE_ADMIN')])) {
			//$data['showPageContent'] = true;
		}
		errorLogEntry("before session" , session()->all());
		
		
		//$data['showPageContent'] = true;
		$data['employeeId'] = $employeeId;
		$data['salaryMasterInfo'] = $getSalaryDetails;

		$data['employeeInfo'] = EmployeeModel::where('i_id', $employeeId)->first();
		$data['reviseSalaryDetails'] = ReviseSalaryMaster::where('i_employee_id', $employeeId)->orderBy('dt_effective_date' , 'desc')->get();

		$data['ajaxRequest'] = $ajaxRequest;
		if ($ajaxRequest != false) {
			$html = view(config('constants.ADMIN_FOLDER') . 'salary/salary-edit')->with($data)->render();
			
			$response = [];
			$response['status_code'] = 1;
			$response['message'] = trans('messages.success');
			$response['data']['html'] = $html;
			return response()->json($response);
			
			echo $html;
			die;
		}

		return view($this->folderName . 'salary')->with($data);
	}

	public function filter(Request $request)
	{
		$salaryComponentDetails = SalaryComponentsModel::where('t_is_active', 1)->orderBy('i_sequence' , 'asc')->get();
		$allEarningComponentDetails = $allDeductComponentDetails = [];
		if (!empty($salaryComponentDetails)) {
			foreach ($salaryComponentDetails as $salaryComponentDetail) {
				switch ($salaryComponentDetail->e_salary_components_type) {
					case config('constants.SALARY_COMPONENT_TYPE_EARNING'):
						$allEarningComponentDetails[] = $salaryComponentDetail;
						break;
					case config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'):
						$allDeductComponentDetails[] = $salaryComponentDetail;
						break;
				}
			}
		}
		$data['earningComponentDetails'] = $allEarningComponentDetails;
		$data['deductComponentDetails'] = $allDeductComponentDetails;

		$whereData = $likeData = [];
		
		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_SALARY_CALCULATION'), session()->get('user_permission')  ) ) ){
			$whereData['show_all'] = true;
		}

		if (!empty($request->post('search_by'))) {
			$likeData['searchBy'] = trim($request->post('search_by'));
		}
		
		if( ( !empty($request->post('search_employment_status') ) )){
			$whereData['employment_status'] =  $request->post('search_employment_status') ;
		}
		
		if (!empty($request->post('search_start_month'))) {
			$whereData['salary_month'] =  getMonthStartDate($request->post('search_start_month'));
		}

		if (!empty($request->post('search_end_month'))) {
			//$whereData['end_month'] =  getMonthStartDate($request->post('search_end_month'));
		}

		if (!empty($request->post('search_employee'))) {
			$whereData['employee'] =  (int)Wild_tiger::decode($request->post('search_employee'));
		}

		if (!empty($request->post('search_team'))) {
			$whereData['team'] =  (int)Wild_tiger::decode($request->post('search_team'));
		}

		if (!empty($request->post('search_designation'))) {
			$whereData['designation'] =  (int)Wild_tiger::decode($request->post('search_designation'));
		}
		
		if (!empty($request->post('search_salary_generate_status'))) {
			switch($request->post('search_salary_generate_status')){
				case config('constants.SELECTION_YES'):
					$whereData['salary_generate_status'] = 1;
					break;
				case config('constants.SELECTION_NO'):
					$whereData['salary_generate_status'] = 0;
					break;
			}
		}
		
		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
		
		if (!empty($request->post('search_salary_generate_status')) && ($request->post('search_salary_generate_status') == config('constants.SELECTION_YES')) && $exportAction == config('constants.EXCEL_EXPORT')) {
			$finalExportData = [];
				
			$getExportRecordDetails = $this->crudModel->getRecordDetails($whereData, $likeData);
		
			if (!empty($getExportRecordDetails)) {
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail) {
					
					$assignSalaryDetails = assignMonthWiseSalaryInfo( $getExportRecordDetail->i_employee_id , $getExportRecordDetail->dt_month );
					
					$generateSalaryInfo = ( ( isset($getExportRecordDetail->attendanceSalary) && (!empty($getExportRecordDetail->attendanceSalary)) ) ? $getExportRecordDetail->attendanceSalary : [] );
					
					$generateSalaryDetails = ( ( isset($getExportRecordDetail->attendanceSalary->generatedSalaryInfo) && (!empty($getExportRecordDetail->attendanceSalary->generatedSalaryInfo)) ) ? $getExportRecordDetail->attendanceSalary->generatedSalaryInfo : [] );
					
					$generateSalaryComponentIds = (!empty($generateSalaryDetails) ? array_column(objectToArray($generateSalaryDetails),'i_component_id') : []);
					
					$pfDeductionStatus = ( isset($assignSalaryDetails->e_pf_deduction) ? $assignSalaryDetails->e_pf_deduction : config('constants.SELECTION_NO') );
					if(!empty($generateSalaryInfo) && (isset($generateSalaryInfo->e_pf_deduction)) ){
						$pfDeductionStatus = $generateSalaryInfo->e_pf_deduction;
					}
					
					$totalPresentDays = 0;
					$salary = ( ( isset($assignSalaryDetails) && (!empty($assignSalaryDetails->d_total_earning)) ) ? $assignSalaryDetails->d_total_earning : 0 );
					$fullDaySalary = (!empty($salary) ? round( ( $salary /  config('constants.SALARY_COUNT_DAYS') ) , 0  ) : 0 );
					$halfDaySalary = (!empty($fullDaySalary) ? round( ( $fullDaySalary /  2 ) , 0  ) : 0 );
					if(!empty($getExportRecordDetail->d_present_count)){
						$totalPresentDays += $getExportRecordDetail->d_present_count;
					}
					
					$paidHalfLeaveCount = ( isset($getExportRecordDetail->d_paid_half_leave_count) ? $getExportRecordDetail->d_paid_half_leave_count : 0 );
					$paidFullLeaveCount = ( isset($getExportRecordDetail->d_paid_full_leave_count) ? $getExportRecordDetail->d_paid_full_leave_count : 0 );
					$attendDayCount = ( isset($getExportRecordDetail->d_present_count) ? $getExportRecordDetail->d_present_count : 0 );
					if(!empty($paidFullLeaveCount)){
						$attendDayCount = ( $attendDayCount - $paidFullLeaveCount );
					}
					if(!empty($paidHalfLeaveCount)){
						$attendDayCount = ( $attendDayCount - $paidHalfLeaveCount );
					}
					
					$rowData = [];
					$rowData['sr_no'] = ++$excelIndex;
					$rowData['month'] = (!empty($getExportRecordDetail->dt_month) ? convertDateFormat($getExportRecordDetail->dt_month, 'm.Y') : '' );
					$rowData['employee_name'] = (!empty($getExportRecordDetail->employeeAttendance->v_employee_full_name) ? $getExportRecordDetail->employeeAttendance->v_employee_full_name : '' );
					$rowData['employee_code'] = (!empty($getExportRecordDetail->employeeAttendance->v_employee_code) ? $getExportRecordDetail->employeeAttendance->v_employee_code : '' );
					$rowData['team'] = (isset($getExportRecordDetail->employeeAttendance->teamInfo->v_value) && !empty($getExportRecordDetail->employeeAttendance->teamInfo->v_value) ? $getExportRecordDetail->employeeAttendance->teamInfo->v_value : '' );
					$rowData['designation'] = (isset($getExportRecordDetail->employeeAttendance->designationInfo->v_value) && !empty($getExportRecordDetail->employeeAttendance->designationInfo->v_value) ? $getExportRecordDetail->employeeAttendance->designationInfo->v_value : '' );
					$rowData['bank'] = (isset($getExportRecordDetail->employeeAttendance->bankInfo->v_value) && !empty($getExportRecordDetail->employeeAttendance->bankInfo->v_value) ? $getExportRecordDetail->employeeAttendance->bankInfo->v_value : '' );
					$rowData['account_number'] = ( ( isset($getExportRecordDetail->employeeAttendance->bankInfo->i_id) && ( $getExportRecordDetail->employeeAttendance->bankInfo->i_id == config('constants.HDFC_BANK_ID') ) )  ? (  isset( $getExportRecordDetail->employeeAttendance->v_bank_account_no) ?  $getExportRecordDetail->employeeAttendance->v_bank_account_no : '-'  ) : '-' );
					$rowData['working_days'] = config('constants.SALARY_COUNT_DAYS');
					$rowData['attend_days'] = $attendDayCount;
					$rowData['ph'] = $paidHalfLeaveCount;
					$rowData['pl'] = $paidFullLeaveCount;
					$rowData['total_paid_days'] = $totalPresentDays;
					$rowData['salary'] = $salary;
					$rowData['full_day'] = $fullDaySalary;
					$rowData['half_day'] = $halfDaySalary;
					
					$earningHeadRowData = $earningHeadWithoutPFRowData = [];
					
					$totalOriginalEarningValue = $dayWiseTotalEarning = $hraAmount = $totalDeductionValue = $totalEarningValue = $totalEarningWithoutPfValue = 0;
					
					if (isset($allEarningComponentDetails) && count($allEarningComponentDetails) > 0){
						foreach ($allEarningComponentDetails as $allEarningComponentDetail){
							$assignedValue = config('constants.DEFAULT_SALARY_VALUE');
							$enteredValue = config('constants.DEFAULT_SALARY_VALUE');
							
							$searchKey = array_search($allEarningComponentDetail->i_id, $generateSalaryComponentIds);
							if(strlen($searchKey) > 0 ){
								$assignedValue = ( isset($generateSalaryDetails[$searchKey]->d_actual_amount) ? $generateSalaryDetails[$searchKey]->d_actual_amount : config('constants.DEFAULT_SALARY_VALUE') );
								$enteredValue = ( isset($generateSalaryDetails[$searchKey]->d_paid_amount) ? $generateSalaryDetails[$searchKey]->d_paid_amount : config('constants.DEFAULT_SALARY_VALUE') );
								if(!empty($assignedValue)){
									$totalOriginalEarningValue += $assignedValue;
								}
								if(!empty($enteredValue)){
									if( $allEarningComponentDetail->e_consider_for_pf_calculation == config('constants.SELECTION_YES') ){
										$dayWiseTotalEarning += $enteredValue;
										$totalEarningValue += $enteredValue;
									} else {
										$totalEarningWithoutPfValue += $enteredValue;
									}
								}
							}
							
							if( $allEarningComponentDetail->i_id == config('constants.HRA_SALARY_COMPONENT_ID') ){
								$hraAmount = $enteredValue;
							}
							
							if (!empty($allEarningComponentDetail->e_consider_for_pf_calculation) && $allEarningComponentDetail->e_consider_for_pf_calculation == config('constants.SELECTION_YES')){
								$earningHeadRowData['working_-_' . $allEarningComponentDetail->v_component_name] = $enteredValue;
							} else {
								$earningHeadWithoutPFRowData['working_-_' . $allEarningComponentDetail->v_component_name] = $enteredValue;
							}
							
							$rowData[$allEarningComponentDetail->v_component_name] = $assignedValue;
						}
						
						$rowData['total_earnings'] = $totalOriginalEarningValue;
							
						if (!empty($earningHeadRowData)){
							$rowData = array_merge($rowData,$earningHeadRowData);
						}
						
						$rowData['working_-_total_earnings'] = $dayWiseTotalEarning;
						
						$rowData['salary_(total_earning_-_hra)'] = $dayWiseTotalEarning - (!empty($hraAmount) ? $hraAmount : 0);
					}

					if (isset($allDeductComponentDetails) && count($allDeductComponentDetails) > 0){
						foreach ($allDeductComponentDetails as $allDeductComponentDetail){
							$assignedValue = config('constants.DEFAULT_SALARY_VALUE');
							$enteredValue = config('constants.DEFAULT_SALARY_VALUE');
								
							$searchKey = array_search($allDeductComponentDetail->i_id, $generateSalaryComponentIds);
							if(strlen($searchKey) > 0 ){
								$assignedValue = ( isset($generateSalaryDetails[$searchKey]->d_actual_amount) ? $generateSalaryDetails[$searchKey]->d_actual_amount : config('constants.DEFAULT_SALARY_VALUE') );
								$enteredValue = ( isset($generateSalaryDetails[$searchKey]->d_paid_amount) ? $generateSalaryDetails[$searchKey]->d_paid_amount : config('constants.DEFAULT_SALARY_VALUE') );
					
								if( $allDeductComponentDetail->i_id == config('constants.PF_SALARY_COMPONENT_ID') ){
									if( $pfDeductionStatus == config('constants.SELECTION_NO') ){
										$assignedValue = 0;
									} else {
										$assignedValue = $enteredValue;
									}
								} else {
									$assignedValue = $enteredValue;
								}
					
								if(!empty($assignedValue)){
									$totalDeductionValue += $assignedValue;
								}
							}
								
							$rowData['working_-_' . $allDeductComponentDetail->v_component_name] = $assignedValue;
						}
					
						$rowData['total_deductions'] = $totalDeductionValue;
					}
					
					$rowData['total_pay_amount'] = $totalEarningValue - $totalDeductionValue;
					
					if (!empty($earningHeadWithoutPFRowData)){
						$rowData = array_merge($rowData,$earningHeadWithoutPFRowData);
					}
					
					$rowData['net_payable_amount'] = $totalEarningValue - $totalDeductionValue + $totalEarningWithoutPfValue;
					
					$finalExportData[] = $rowData;
				}
			}
				
			if (!empty($finalExportData)) {
				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.salary-calculation') ]);
		
				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.salary-calculation')]);
				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
			} else {
				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
			}
				
			return Response::json($response);
		}
		
		$data['recordDetails'] = $this->crudModel->getRecordDetails($whereData, $likeData);
		$data['totalRecordCount'] = count($data['recordDetails']);
		
		$html = view(config('constants.AJAX_VIEW_FOLDER') . 'salary/generate-salary-list')->with($data)->render();
		echo $html;die;
	}

	public function generateSalary(Request $request)
	{
		$buttonAction = (!empty($request->input('button_action')) ? trim($request->input('button_action')) : null );
		
		$salaryComponentDetails = SalaryComponentsModel::where('t_is_active', 1)->get();
		$allEarningComponentDetails = $allDeductComponentDetails = [];
		if (!empty($salaryComponentDetails)) {
			foreach ($salaryComponentDetails as $salaryComponentDetail) {
				switch ($salaryComponentDetail->e_salary_components_type) {
					case config('constants.SALARY_COMPONENT_TYPE_EARNING'):
						$allEarningComponentDetails[] = $salaryComponentDetail;
						break;
					case config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'):
						$allDeductComponentDetails[] = $salaryComponentDetail;
						break;
				}
			}
		}
		$earningComponentDetails = $allEarningComponentDetails;
		$deductComponentDetails = $allDeductComponentDetails;
		
		$selectedRecordEncodeIds = (!empty($request->input('employee_selection')) ? $request->input('employee_selection') : []  );
		$selectedRecordDecodeIds = [];
		
		if(!empty($selectedRecordEncodeIds)){
			$selectedRecordDecodeIds = array_map(function($selectedRecordEncodeId){
				return (int)Wild_tiger::decode($selectedRecordEncodeId);
			} , $selectedRecordEncodeIds );
		}
		
		$recordDetails = [];
		if(!empty($selectedRecordDecodeIds)){
			$getRecordWhere = [];
			$getRecordWhere['master_id'] = $selectedRecordDecodeIds;
			if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_SALARY_CALCULATION'), session()->get('user_permission')  ) ) ){
				$getRecordWhere['show_all'] = true;
			}
			$recordDetails = $this->crudModel->getRecordDetails( $getRecordWhere );
		}
		
		
		
		$successMessage =  trans('messages.success-save', ['module' => trans('messages.salary') ] );
		$errorMessage = trans('messages.error-save', ['module' => trans('messages.salary') ] );
		
		switch($buttonAction){
			case config('constants.SUBMIT_ACTION'):
				$successMessage =  trans('messages.success-generate', ['module' => trans('messages.pay-slip') ] );
				$errorMessage = trans('messages.error-generate', ['module' => trans('messages.pay-slip') ] );
				break;
			case config('constants.SAVE_ACTION'):
				$successMessage =  trans('messages.success-save', ['module' => trans('messages.salary') ] );
				$errorMessage = trans('messages.error-save', ['module' => trans('messages.salary') ] );
				break;
		}
		//echo "<pre>";print_r($request->all());
		//var_dump($buttonAction);

		
		//echo "<pre>";print_r($request->all());
		/* echo "<pre>";print_r($selectedRecordEncodeIds);
		echo "<pre>";print_r($selectedRecordDecodeIds);
		die; */
		$result = false;
		DB::beginTransaction();
		try {

			if (!empty($recordDetails)) {
				foreach ($recordDetails as $recordDetail) {
					$uniqueRecordId = $recordDetail->i_id;
					
					$assignSalaryDetails = assignMonthWiseSalaryInfo( $recordDetail->i_employee_id , $recordDetail->dt_month );
					
					
					$allocatedComponentId =  ((isset($assignSalaryDetails->assignSalaryInfo) && (!empty($assignSalaryDetails->assignSalaryInfo))) ? array_column(objectToArray($assignSalaryDetails->assignSalaryInfo), 'i_salary_component_id') : []);
					//echo "<pre>";print_r($allocatedComponentId);
					$totalOriginalEarningValue = $totalOriginalDeductionValue =  0;
					$totalEarningValue = $totalDeductionValue =  0;
					$salaryMasterInfo = [];
					$salaryMasterInfo['i_employee_id'] = $recordDetail->i_employee_id;
					$salaryMasterInfo['dt_salary_month'] = $recordDetail->dt_month;
					$salaryMasterInfo['e_pf_deduction'] = ( isset($assignSalaryDetails->e_pf_deduction) ? $assignSalaryDetails->e_pf_deduction : config('constants.SELECTION_NO') ) ;

					$month = date('m' , strtotime($salaryMasterInfo['dt_salary_month']));
					
					$year = date('Y' , strtotime($salaryMasterInfo['dt_salary_month']));
					
					$getAttendanceInfo = $this->getAttendanceInfo($month, $year, $salaryMasterInfo['i_employee_id'] );
						
					$getAttendanceInfo = ( isset($getAttendanceInfo['attendanceData']) ? $getAttendanceInfo['attendanceData'] : [] );
					
					$calendarInfo = [];
					
					$calendarInfo['allDates'] = ( isset($getAttendanceInfo['allDates']) ? $getAttendanceInfo['allDates'] : [] );
					$calendarInfo['presentDates'] = ( isset($getAttendanceInfo['presentDates']) ? $getAttendanceInfo['presentDates'] : [] );
					$calendarInfo['absentDates'] = ( isset($getAttendanceInfo['absentDates']) ? $getAttendanceInfo['absentDates'] : [] );
					$calendarInfo['holidatDates'] = ( isset($getAttendanceInfo['holidatDates']) ? $getAttendanceInfo['holidatDates'] : [] );
					$calendarInfo['weekOffDates'] = ( isset($getAttendanceInfo['weekOffDates']) ? $getAttendanceInfo['weekOffDates'] : [] );
					$calendarInfo['presentDayCount'] = ( isset($getAttendanceInfo['presentDayCount']) ? $getAttendanceInfo['presentDayCount'] : 0 );
					$calendarInfo['onlyPresentCount'] = ( isset($getAttendanceInfo['onlyPresentCount']) ? $getAttendanceInfo['onlyPresentCount'] : 0 );
					$calendarInfo['absentDayCount'] = ( isset($getAttendanceInfo['absentDayCount']) ? $getAttendanceInfo['absentDayCount'] : 0 );
					$calendarInfo['approvedHalfLeaveDates'] = ( isset($getAttendanceInfo['approvedHalfLeaveDates']) ? $getAttendanceInfo['approvedHalfLeaveDates'] : [] );
					$calendarInfo['approvedLeaveDates'] = ( isset($getAttendanceInfo['approvedLeaveDates']) ? $getAttendanceInfo['approvedLeaveDates'] : [] );
					$calendarInfo['suspendDates'] = ( isset($getAttendanceInfo['suspendDates']) ? $getAttendanceInfo['suspendDates'] : [] );
					$calendarInfo['adjustmentDates'] = ( isset($getAttendanceInfo['adjustmentDates']) ? $getAttendanceInfo['adjustmentDates'] : [] );
					$calendarInfo['halfLeaveDates'] = ( isset($getAttendanceInfo['halfLeaveDates']) ? $getAttendanceInfo['halfLeaveDates'] : [] );
					$calendarInfo['calendarViewUnpaidHalfLeaveDates'] = ( isset($getAttendanceInfo['calendarViewUnpaidHalfLeaveDates']) ? $getAttendanceInfo['calendarViewUnpaidHalfLeaveDates'] : [] );
					$calendarInfo['salaryPaidDayCount'] = ( isset($getAttendanceInfo['salaryPaidDayCount']) ? $getAttendanceInfo['salaryPaidDayCount'] : 0 );
					//echo "<pre>";print_r($calendarInfo);die;
					$salaryMasterInfo['v_calendar_info'] = (!empty($calendarInfo) ? json_encode($calendarInfo) : null );
					
					$salaryDetailsData = [];

					$employeeAllOnHoldSalaryDetails = ( isset($recordDetail->employeeAttendance->onHoldSalaryInfo) ? $recordDetail->employeeAttendance->onHoldSalaryInfo : [] );
					$allOnHoldSalaryMonths = (!empty($employeeAllOnHoldSalaryDetails) ? array_column(objectToArray($employeeAllOnHoldSalaryDetails),'dt_month') : [] );
					
					if (count($earningComponentDetails) > 0) {
						foreach ($earningComponentDetails as $earningComponentDetail) {
							$encodeId = Wild_tiger::encode($earningComponentDetail->i_id);
							$assignedValue = config('constants.DEFAULT_SALARY_VALUE');
							$searchHeadKey = array_search($earningComponentDetail->i_id, $allocatedComponentId);
							if (strlen($searchHeadKey)  > 0) {
								$assignedValue = (isset($assignSalaryDetails->assignSalaryInfo[$searchHeadKey]->d_amount) ? $assignSalaryDetails->assignSalaryInfo[$searchHeadKey]->d_amount : config('constants.DEFAULT_SALARY_VALUE'));
								if (!empty($assignedValue)) {
									$totalOriginalEarningValue += $assignedValue;
								}
								$rowData = [];
								$rowData['i_component_id'] = $earningComponentDetail->i_id;
								$rowData['d_actual_amount'] = $assignedValue;
								$rowData['d_paid_amount'] = ((strlen($request->input('salary_amount_' . $uniqueRecordId . '_' . $earningComponentDetail->i_id)) > 0) ? $request->input('salary_amount_' . $uniqueRecordId . '_' . $earningComponentDetail->i_id) : config('constants.DEFAULT_SALARY_VALUE'));
								if (!empty($rowData['d_paid_amount'])) {
									$totalEarningValue += $rowData['d_paid_amount'];
								}
								$salaryDetailsData[] = $rowData;
							}
						}
					}
					if (count($deductComponentDetails) > 0) {
						foreach ($deductComponentDetails as $deductComponentDetail) {
							
							if( $deductComponentDetail->i_id == config('constants.ON_HOLD_SALARY_COMPONENT_ID') ){
								continue;
							}
							//var_dump($allocatedComponentId);die;
							$encodeId = Wild_tiger::encode($deductComponentDetail->i_id);
							$assignedValue = config('constants.DEFAULT_SALARY_VALUE');
							$searchHeadKey = array_search($deductComponentDetail->i_id, $allocatedComponentId);
							if (strlen($searchHeadKey)  > 0) {
								$assignedValue = (isset($assignSalaryDetails->assignSalaryInfo[$searchHeadKey]->d_amount) ? $assignSalaryDetails->assignSalaryInfo[$searchHeadKey]->d_amount : config('constants.DEFAULT_SALARY_VALUE'));
								if (!empty($assignedValue)) {
									$totalOriginalDeductionValue += $assignedValue;
								}
								$rowData = [];
								$rowData['i_component_id'] = $deductComponentDetail->i_id;
								$rowData['d_actual_amount'] = $assignedValue;
								$rowData['d_paid_amount'] = ((strlen($request->input('salary_amount_' . $uniqueRecordId . '_' . $deductComponentDetail->i_id)) > 0) ? $request->input('salary_amount_' . $uniqueRecordId . '_' . $deductComponentDetail->i_id) : config('constants.DEFAULT_SALARY_VALUE'));
								if (!empty($rowData['d_paid_amount'])) {
									$totalDeductionValue += $rowData['d_paid_amount'];
								}
								$salaryDetailsData[] = $rowData;
							} else {
								if( $deductComponentDetail->i_id == config('constants.PF_SALARY_COMPONENT_ID') ){
									$rowData = [];
									$rowData['i_component_id'] = $deductComponentDetail->i_id;
									$rowData['d_actual_amount'] = 0;
									$rowData['d_paid_amount'] = ((strlen($request->input('salary_amount_' . $uniqueRecordId . '_' . $deductComponentDetail->i_id)) > 0) ? $request->input('salary_amount_' . $uniqueRecordId . '_' . $deductComponentDetail->i_id) : config('constants.DEFAULT_SALARY_VALUE'));
									if (!empty($rowData['d_paid_amount'])) {
										$totalDeductionValue += $rowData['d_paid_amount'];
									}
									$salaryDetailsData[] = $rowData;
								}
								if( $deductComponentDetail->i_id == config('constants.PT_SALARY_COMPONENT_ID') ){
									$rowData = [];
									$rowData['i_component_id'] = $deductComponentDetail->i_id;
									$rowData['d_actual_amount'] = 0;
									$rowData['d_paid_amount'] = ((strlen($request->input('salary_amount_' . $uniqueRecordId . '_' . $deductComponentDetail->i_id)) > 0) ? $request->input('salary_amount_' . $uniqueRecordId . '_' . $deductComponentDetail->i_id) : config('constants.DEFAULT_SALARY_VALUE'));
									if (!empty($rowData['d_paid_amount'])) {
										$totalDeductionValue += $rowData['d_paid_amount'];
									}
									$salaryDetailsData[] = $rowData;
								}
								
							}
						}
					}
					$cutOnHoldAmount = 0;
					if( in_array( $recordDetail->dt_month ,  $allOnHoldSalaryMonths ) ){
						$searchOnHoldSalaryInfoKey = array_search($recordDetail->dt_month ,  $allOnHoldSalaryMonths);
						if( ( strlen($searchOnHoldSalaryInfoKey) > 0 ) && (isset($employeeAllOnHoldSalaryDetails[$searchOnHoldSalaryInfoKey]->d_amount)) && (!empty($employeeAllOnHoldSalaryDetails[$searchOnHoldSalaryInfoKey]->d_amount))  ){
							$onHoldSalaryAmount = $employeeAllOnHoldSalaryDetails[$searchOnHoldSalaryInfoKey]->d_amount;
							$rowData = [];
							$rowData['i_component_id'] = config('constants.ON_HOLD_SALARY_COMPONENT_ID');
							$rowData['d_actual_amount'] = $onHoldSalaryAmount;
							$rowData['d_paid_amount'] = ((strlen($request->input('salary_amount_' . $uniqueRecordId . '_' . $rowData['i_component_id'])) > 0) ? $request->input('salary_amount_' . $uniqueRecordId . '_' . $rowData['i_component_id']) : config('constants.DEFAULT_SALARY_VALUE'));
							if (!empty($rowData['d_paid_amount'])) {
								$cutOnHoldAmount = $rowData['d_paid_amount'];
								$totalDeductionValue += $rowData['d_paid_amount'];
							}
							$salaryDetailsData[] = $rowData;
						}
					}
					
					$salaryMasterInfo['d_paid_days_count'] = $recordDetail->d_present_count;
					//$salaryMasterInfo['d_deduct_days_count'] = 24;
					$salaryMasterInfo['d_actual_total_earning_amount'] = $totalOriginalEarningValue;
					$salaryMasterInfo['d_actual_total_deduct_amount'] = $totalOriginalDeductionValue;
					$salaryMasterInfo['d_total_earning_amount'] = $totalEarningValue;
					$salaryMasterInfo['d_total_deduct_amount'] = $totalDeductionValue;
					$salaryMasterInfo['d_net_pay_amount'] = ( $totalEarningValue - $totalDeductionValue );
					$salaryMasterInfo['d_cut_hold_amount'] = $cutOnHoldAmount;
					$salaryMasterInfo['t_is_salary_generated'] = 0;

					if( ( $buttonAction == config('constants.SUBMIT_ACTION') ) &&  in_array( $uniqueRecordId , $selectedRecordDecodeIds ) ){
						$salaryMasterInfo['t_is_salary_generated'] = 1;
					}
					//echo "<pre>";print_r($salaryMasterInfo);
					//echo "<pre> before";print_r($salaryDetailsData);die;
					if (!empty($salaryDetailsData)) {
						$checkSalaryWhere = [];
						$checkSalaryWhere['t_is_deleted'] = 0;
						$checkSalaryWhere['i_employee_id'] = $recordDetail->i_employee_id;;
						$checkSalaryWhere['dt_salary_month'] = $recordDetail->dt_month;

						$checkSalaryExist = Salary::where($checkSalaryWhere)->first();

						if(!empty($checkSalaryExist)){
							
							if( $checkSalaryExist->t_is_salary_generated == 1 ){
								continue;
							}
							
							$salaryMasterId = $checkSalaryExist->i_id;
							
							$this->crudModel->updateTableData(config('constants.SALARY_MASTER_TABLE'), $salaryMasterInfo , [ 'i_id' => $checkSalaryExist->i_id  ] );
							
							$allComponentIds = (!empty($salaryDetailsData) ? array_column($salaryDetailsData, "i_component_id") : [] );
							
							$getExistingSalaryDetails =  SalaryInfo::where('t_is_deleted' ,  0 )->where('i_salary_master_id' , $checkSalaryExist->i_id )->get();
							
							if(!empty($getExistingSalaryDetails)){
								foreach($getExistingSalaryDetails as $getExistingSalaryDetail){
									if(in_array( $getExistingSalaryDetail->i_component_id ,  $allComponentIds  ) ){
										$getInfoKey = array_search($getExistingSalaryDetail->i_component_id ,  $allComponentIds  );
										if( strlen($getInfoKey) > 0 ){
											$updateSalaryInfo = [];
											$updateSalaryInfo['d_actual_amount'] = ( isset($salaryDetailsData[$getInfoKey]['d_actual_amount']) ? $salaryDetailsData[$getInfoKey]['d_actual_amount'] : 0 );
											$updateSalaryInfo['d_paid_amount'] = ( isset($salaryDetailsData[$getInfoKey]['d_paid_amount']) ? $salaryDetailsData[$getInfoKey]['d_paid_amount'] : 0 );
											$this->crudModel->updateTableData( config('constants.SALARY_INFO_TABLE'), $updateSalaryInfo , [ 'i_id' => $getExistingSalaryDetail->i_id  ] );
											unset($salaryDetailsData[$getInfoKey]);
										}
									} else {
										$this->crudModel->deleteTableData( config('constants.SALARY_INFO_TABLE'), [ 't_is_active' => 0 , 't_is_deleted' => 1 ] , [ 'i_id' => $getExistingSalaryDetail->i_id  ] );
									}
								}
							}
							//echo "<pre>after ";print_r($salaryDetailsData);
							if (!empty($salaryDetailsData)) {
								
								$salaryOtherDetails = array_map(function ($salaryDetail) use ($salaryMasterId) {
									$salaryDetail['i_salary_master_id'] = $salaryMasterId;
									$salaryDetail['i_created_id'] = session()->get('user_id');
									$salaryDetail['dt_created_at'] = date('Y-m-d H:i:s');
									return $salaryDetail;
								}, $salaryDetailsData);
								
								DB::table(config('constants.SALARY_INFO_TABLE'))->insert($salaryOtherDetails);
							};
						
						} else {
							
							$insertSalaryMaster = $this->crudModel->insertTableData(config('constants.SALARY_MASTER_TABLE'), $salaryMasterInfo);
							
							if (!empty($salaryDetailsData)) {
							
								$salaryOtherDetails = array_map(function ($salaryDetail) use ($insertSalaryMaster) {
									$salaryDetail['i_salary_master_id'] = $insertSalaryMaster;
									$salaryDetail['i_created_id'] = session()->get('user_id');
									$salaryDetail['dt_created_at'] = date('Y-m-d H:i:s');
									return $salaryDetail;
								}, $salaryDetailsData);
							
								DB::table(config('constants.SALARY_INFO_TABLE'))->insert($salaryOtherDetails);
							};
						}
						
						if( !in_array(  $recordDetail->employeeAttendance->e_hold_salary_payment_status  , [ config('constants.NOT_TO_PAY_STATUS') , config('constants.DONATED_STATUS') ] ) ){
							$updateEmployeeSalaryInfo['e_hold_salary_payment_status'] = config('constants.PENDING_STATUS');
							$updateEmployeeSalaryInfo['dt_on_hold_release_date'] = null;
							$onHoldSalaryDetails = EmployeeModel::with( [ 'onHoldSalaryInfo' , 'generatedSalaryMaster' , 'generatedSalaryMaster.generatedSalaryInfo'  ] )->where('i_id' , $recordDetail->i_employee_id)->first();
							if(!empty($onHoldSalaryDetails)){
								$getHoldAmountInfo = getHoldAmountInfo($onHoldSalaryDetails);
								$totalOnHoldSalaryAmount = ( isset($getHoldAmountInfo['totalOnHoldSalaryAmount']) ? $getHoldAmountInfo['totalOnHoldSalaryAmount'] : 0 ) ;
								$decuctOnHoldSalaryAmount = ( isset($getHoldAmountInfo['deductOnHoldSalaryAmount']) ? $getHoldAmountInfo['deductOnHoldSalaryAmount'] : 0 ) ;
								$leftAmount = ( isset($getHoldAmountInfo['leftOnHoldSalaryAmount']) ? $getHoldAmountInfo['leftOnHoldSalaryAmount'] : 0 ) ;
								if( $leftAmount <= 0 ){
									$updateEmployeeSalaryInfo['e_hold_salary_payment_status'] = config('constants.PAID_STATUS');
									$updateEmployeeSalaryInfo['dt_on_hold_release_date'] = $salaryMasterInfo['dt_salary_month'];
								}
							}
							$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , $updateEmployeeSalaryInfo , [ 'i_id' => $recordDetail->i_employee_id ] );
						}
					}
				}
				$result = true;
			}
		} catch (\Exception $e) {
			$result = false;
			DB::rollback();
		}
		//var_dump($result);die;
		if ($result != false) {
			DB::commit();
			Wild_tiger::setFlashMessage('success', $successMessage);
			return redirect( config('constants.CALCULATE_SALARY_URL') );
		}

		DB::rollback();
		Wild_tiger::setFlashMessage('danger', $errorMessage);
		return redirect( config('constants.CALCULATE_SALARY_URL'));
	}

	public function employeeSalaryInfo(Request $request)
	{
		if (!empty($request->input())) {

			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0);

			if ($employeeId > 0) {
				
				//echo "<pre>";print_r($request->all());die;
				//$employeeId = 3;
				$salaryMonth = (!empty($request->input('salary_month')) ? trim($request->input('salary_month')) : date('Y-m-d'));
				

				$month = date('m' , strtotime($salaryMonth));
				
				$year = date('Y' , strtotime($salaryMonth));
				
				//echo "<pre>";print_r($salaryMonth);
				//echo "<pre>";print_r($employeeId);
				
				$checkSalaryWhere = [];
				$checkSalaryWhere['dt_salary_month'] = $salaryMonth;
				$checkSalaryWhere['i_employee_id'] = $employeeId;
				$checkSalaryWhere['t_is_deleted'] = 0;
				$checkSalaryGenerated = Salary::with(['generatedSalaryInfo' , 'generatedSalaryInfo.generateSalaryComponent' , 'employee' ])->where($checkSalaryWhere)->first();
				
				$salaryGenerated = false;
				$allowEditSalary = true;
				
				$allowedAmendment = false;
				
				if(!empty($checkSalaryGenerated)){
					if( $checkSalaryGenerated->t_is_salary_generated == 1 ){
						$allowEditSalary = false;
						if( $request->has('ament_salary') && ( $request->has('ament_salary') == config('constants.SELECTION_YES')  ) ){
							$allowedAmendment = true;
						}
						
						$calendarDetails = (!empty($checkSalaryGenerated->v_calendar_info) ? json_decode($checkSalaryGenerated->v_calendar_info,true) : [] );
						
						$data['allDates'] = ( isset($calendarDetails['allDates']) ? $calendarDetails['allDates'] : [] );
						$data['presentDates'] = ( isset($calendarDetails['presentDates']) ? $calendarDetails['presentDates'] : [] );
						$data['absentDates'] = ( isset($calendarDetails['absentDates']) ? $calendarDetails['absentDates'] : [] );
						$data['holidatDates'] = ( isset($calendarDetails['holidatDates']) ? $calendarDetails['holidatDates'] : [] );
						$data['weekOffDates'] = ( isset($calendarDetails['weekOffDates']) ? $calendarDetails['weekOffDates'] : [] );
						$data['presentDayCount'] = ( isset($calendarDetails['presentDayCount']) ? $calendarDetails['presentDayCount'] : 0 );
						$data['onlyPresentCount'] = ( isset($calendarDetails['onlyPresentCount']) ? $calendarDetails['onlyPresentCount'] : 0 );
						$data['absentDayCount'] = ( isset($calendarDetails['absentDayCount']) ? $calendarDetails['absentDayCount'] : 0 );
						$data['approvedHalfLeaveDates'] = ( isset($calendarDetails['approvedHalfLeaveDates']) ? $calendarDetails['approvedHalfLeaveDates'] : [] );
						$data['approvedLeaveDates'] = ( isset($calendarDetails['approvedLeaveDates']) ? $calendarDetails['approvedLeaveDates'] : [] );
						$data['suspendDates'] = ( isset($calendarDetails['suspendDates']) ? $calendarDetails['suspendDates'] : [] );
						$data['adjustmentDates'] = ( isset($calendarDetails['adjustmentDates']) ? $calendarDetails['adjustmentDates'] : [] );
						$data['halfLeaveDates'] = ( isset($calendarDetails['halfLeaveDates']) ? $calendarDetails['halfLeaveDates'] : [] );
						$data['calendarViewUnpaidHalfLeaveDates'] = ( isset($calendarDetails['calendarViewUnpaidHalfLeaveDates']) ? $calendarDetails['calendarViewUnpaidHalfLeaveDates'] : [] );
						$data['salaryPaidDayCount'] = ( isset($calendarDetails['salaryPaidDayCount']) ? $calendarDetails['salaryPaidDayCount'] : 0 );
						//echo "<pre>";print_r($data['absentDates'] );die;
					}
					
					$salaryGenerated = true;
					$data['generatedSalaryDetails'] = $checkSalaryGenerated;
							
				} 
				
				
				if( $allowEditSalary != false ){
					
					$getAttendanceInfo = $this->getAttendanceInfo($month, $year, $employeeId );
					
					$getAttendanceInfo = ( isset($getAttendanceInfo['attendanceData']) ? $getAttendanceInfo['attendanceData'] : [] );
					//echo "<pre>";print_r($getAttendanceInfo);
					
					$data['allDates'] = ( isset($getAttendanceInfo['allDates']) ? $getAttendanceInfo['allDates'] : [] );
					$data['presentDates'] = ( isset($getAttendanceInfo['presentDates']) ? $getAttendanceInfo['presentDates'] : [] );
					$data['absentDates'] = ( isset($getAttendanceInfo['absentDates']) ? $getAttendanceInfo['absentDates'] : [] );
					$data['holidatDates'] = ( isset($getAttendanceInfo['holidatDates']) ? $getAttendanceInfo['holidatDates'] : [] );
					$data['weekOffDates'] = ( isset($getAttendanceInfo['weekOffDates']) ? $getAttendanceInfo['weekOffDates'] : [] );
					$data['presentDayCount'] = ( isset($getAttendanceInfo['presentDayCount']) ? $getAttendanceInfo['presentDayCount'] : 0 );
					$data['onlyPresentCount'] = ( isset($getAttendanceInfo['onlyPresentCount']) ? $getAttendanceInfo['onlyPresentCount'] : 0 );
					$data['absentDayCount'] = ( isset($getAttendanceInfo['absentDayCount']) ? $getAttendanceInfo['absentDayCount'] : 0 );
					$data['approvedHalfLeaveDates'] = ( isset($getAttendanceInfo['approvedHalfLeaveDates']) ? $getAttendanceInfo['approvedHalfLeaveDates'] : [] );
					$data['approvedLeaveDates'] = ( isset($getAttendanceInfo['approvedLeaveDates']) ? $getAttendanceInfo['approvedLeaveDates'] : [] );
					$data['suspendDates'] = ( isset($getAttendanceInfo['suspendDates']) ? $getAttendanceInfo['suspendDates'] : [] );
					$data['adjustmentDates'] = ( isset($getAttendanceInfo['adjustmentDates']) ? $getAttendanceInfo['adjustmentDates'] : [] );
					$data['halfLeaveDates'] = ( isset($getAttendanceInfo['halfLeaveDates']) ? $getAttendanceInfo['halfLeaveDates'] : [] );
					$data['calendarViewUnpaidHalfLeaveDates'] = ( isset($getAttendanceInfo['calendarViewUnpaidHalfLeaveDates']) ? $getAttendanceInfo['calendarViewUnpaidHalfLeaveDates'] : [] );
					$data['salaryPaidDayCount'] = ( isset($getAttendanceInfo['salaryPaidDayCount']) ? $getAttendanceInfo['salaryPaidDayCount'] : 0 );
					
					//var_dump($data['presentDayCount']);die;
					
					//dd($data['approvedLeaveDates']);
					
				}
				
				if($salaryGenerated != true ){
					
					$getSalaryWhere =  [];
						
					$getSalaryWhere['i_id'] = $employeeId;
					
					//$getSalaryDetails = EmployeeModel::with(['salaryInfo', 'salaryDetail', 'salaryDetail.salaryComponentInfo'])->where($getSalaryWhere)->first();
					$getSalaryDetails = assignMonthWiseSalaryInfo( $employeeId  , $salaryMonth );
					//echo "<pre>";print_r($getSalaryDetails);
					$data['salaryDetails'] = $getSalaryDetails;
				}
				
				$data['masterSalaryDetails'] = assignMonthWiseSalaryInfo( $employeeId  , $salaryMonth );
				
				
				$data['month'] = $month;
				
				$data['year'] = $year;
				
				$currentSalaryComponentValues = (!empty($request->post('component_wise_salary_value')) ? $request->post('component_wise_salary_value') : []  );
				$currentAllComponentIds = (!empty($currentSalaryComponentValues) ? array_column($currentSalaryComponentValues, 'component_id') : [] );
				
				//echo "<pre>";print_r($currentAllComponentIds);die;
				
				$data['currentComponentIds'] = $currentAllComponentIds;
				$data['currentSalaryComponentValues'] = $currentSalaryComponentValues;
				//echo "<pre>";print_r($data['currentSalaryComponentValues']);
				$data['calendarStartDate'] = attendanceStartDate( $month , $year);
				$data['calendarEndDate'] = attendanceEndDate( $month , $year);
				$data['salaryGenerated'] = $salaryGenerated;
				$data['allowEditSalary'] = $allowEditSalary;
				
				$getAttendanceSummaryInfo = AttendanceSummaryModel::where('t_is_deleted' , 0 )->where('i_employee_id' , $employeeId )->where('dt_month' , $salaryMonth)->first();
				
				if(!empty($getAttendanceSummaryInfo)){
					$data['presentDayCount'] = $getAttendanceSummaryInfo->d_present_count;
				}
				$data['allowedAmendment'] = $allowedAmendment;
				
				//echo "<pre>";print_r($data);die;
				
				//var_dump($salaryGenerated);die;
				$html = view($this->folderName . 'employee-salary-view')->with($data)->render();
				echo $html;die;
				//echo "<pre>";print_r($getSalaryDetails);
			}
		}
	}
	
	public function verifyPassword(Request $request)
	{
		$where = [];
		if (!empty($request->post())) {

			$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0);
			$verifyPassword = (!empty($request->post('verify_password')) ? $request->post('verify_password') : '');

			if (!empty($employeeId)) {
				$where['master_id'] = $employeeId;
				$where['singleRecord'] = true;
				
				if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.ALL_VIEW_SALARY'), session()->get('user_permission')  ) ) ) ) ) ){
					$employeeInfo = Login::where('i_id' , session()->get('user_id'))->first();
					$loginPassword = (!empty($employeeInfo->v_password) ? $employeeInfo->v_password : '');
				} else {
					$employeeInfo = $this->employeeModel->getRecordDetails($where);
					$loginPassword = (!empty($employeeInfo->loginInfo->v_password) ? $employeeInfo->loginInfo->v_password : '');
				}
				
				
				if (!empty($loginPassword)) {
					if (password_verify($verifyPassword, $loginPassword)) {
						Session::put('show_salary_info', config('constants.SELECTION_YES'));
						$response = [];
						$response['status_code'] = 1;
						$response['message'] = trans('messages.verify-password-successfully');
						return response()->json($response);
					} else {
						$this->ajaxResponse(101, trans('messages.invalid-password'));
					}
				} else {
					$this->ajaxResponse(101, trans('messages.invalid-password'));
				}
			}
		}
	}
	public function editSalaryModel(Request $request)
	{
		$data = [];
		
		$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0);
		$reviseRecordId = (!empty($request->post('revise_record_id')) ? (int)Wild_tiger::decode($request->post('revise_record_id')) : 0);

		$data['salaryGroupDetails'] = SalaryGroupModel::orderBy('v_group_name', 'ASC')->get();

		$employeeSalaryDetails = [];


		if ($reviseRecordId > 0) {

			$employeeSalary = ReviseSalaryInfo::with(['reviseSalaryMaster']);

			$employeeSalary->whereHas('reviseSalaryMaster', function ($employeeSalary) use ($reviseRecordId) {
				$employeeSalary->where('i_id', $reviseRecordId);
			});

			$employeeSalaryDetails = $employeeSalary->get();

			$data['employeeSalaryDetails'] = $employeeSalaryDetails;

			$data['salaryMasterInfo'] = (isset($data['employeeSalaryDetails'][0]->reviseSalaryMaster) ? $data['employeeSalaryDetails'][0]->reviseSalaryMaster : []);
		} else {
			$employeeSalary = EmployeeSalaryDetailModel::with(['employeeSalaryMaster']);

			$employeeSalary->whereHas('employeeSalaryMaster', function ($employeeSalary) use ($employeeId) {
				$employeeSalary->where('i_employee_id', $employeeId);
			});

			$employeeSalaryDetails = $employeeSalary->get();

			$data['employeeSalaryDetails'] = $employeeSalaryDetails;

			$data['salaryMasterInfo'] = (isset($data['employeeSalaryDetails'][0]->employeeSalaryMaster) ? $data['employeeSalaryDetails'][0]->employeeSalaryMaster : []);
		}
		
		$lastAddedSalaryInfo = ReviseSalaryMaster::where('i_employee_id' , $employeeId)->orderBy('dt_effective_date' , 'desc')->first();
		
		
		$salaryComponentDetails = [];

		if (isset($data['salaryMasterInfo'])  && (!empty($data['salaryMasterInfo']))) {
			$salaryComponentWhere = [];
			$salaryComponentWhere['t_is_deleted'] = 0;
			$salaryComponentWhere['i_salary_group_id'] = $data['salaryMasterInfo']->i_salary_group_id;
			$salaryComponentDetails = SalaryGroupDetailsModel::with(['salaryComponentInfo'])->where($salaryComponentWhere)->get();
		}

		$data['salaryComponentDetails'] = $salaryComponentDetails;
		$data['salaryUsedComponentIds'] = (!empty($data['employeeSalaryDetails']) ?  array_column(objectToArray($data['employeeSalaryDetails']), 'i_salary_component_id') : []);

		$data['employeeId'] = $employeeId;
		$data['reviseRecordId'] = $reviseRecordId;
		$data['employeeInfo'] = EmployeeModel::with(['latestGeneratedSalary'])->where('i_id' , $employeeId )->first();
		$data['minEffectativeDate'] = "";
		if( empty($data['salaryMasterInfo']) ){
			$data['minEffectativeDate'] = ( isset( $data['employeeInfo']->dt_joining_date )  ? $data['employeeInfo']->dt_joining_date : date('Y-m-d') );
		} else {
			$data['minEffectativeDate'] = ( isset( $data['salaryMasterInfo']->dt_effective_date )  ? $data['salaryMasterInfo']->dt_effective_date : date('Y-m-d') );
		}
		$lastSalaryMonth = lastAllowedDate($data['employeeInfo']);
		
		$data['lastSalaryAssignDate'] = (!empty($lastAddedSalaryInfo) && (isset($lastAddedSalaryInfo->dt_effective_date)) ? $lastAddedSalaryInfo->dt_effective_date : ( isset( $data['employeeInfo']->dt_joining_date )  ? $data['employeeInfo']->dt_joining_date : date('Y-m-d') ) );
		
		
		if( $reviseRecordId == 0 ){
			$data['minEffectativeDate'] = $data['lastSalaryAssignDate'];
			if( (!empty($lastSalaryMonth)) && strtotime($lastSalaryMonth) > strtotime($data['lastSalaryAssignDate']) ){
				if(config('constants.LAST_SALARY_GENERATED_DATE_CHECK') == 1 ){
					$data['minEffectativeDate'] = $lastSalaryMonth;
				}
				
			}
		}
		$allowedChangePFSelection = true;
		$getPFSalaryInfo = Salary::where('i_employee_id' , $employeeId)->where('e_pf_deduction' , config('constants.SELECTION_YES'))->where('t_is_salary_generated' , 1 )->first();
		//echo "<pre> getPFSalaryInfo";print_r($getPFSalaryInfo);die;
		if(count(objectToArray($getPFSalaryInfo)) > 0 ){
			$allowedChangePFSelection = false;
		}
		if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ){
			$allowedChangePFSelection = true;
		}
		
		//var_dump($allowedChangePFSelection);die;
		//echo "<pre>";print_r($data['salaryMasterInfo']);die;
		$data['allowedChangePFSelection'] = $allowedChangePFSelection;
		//var_dump($data['allowedChangePFSelection']);die;
		$html = view($this->folderName . 'add-salary-model')->with($data)->render();

		echo $html;
		die;
	}
	public function getGroupSalaryComponent(Request $request)
	{

		if (!empty($request->input())) {

			$salaryGroupdId = (!empty($request->post('salary_group')) ? (int)Wild_tiger::decode($request->post('salary_group')) : 0);

			if ($salaryGroupdId > 0) {
				
				$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0);
				$reviseRecordId = (!empty($request->post('revise_record_id')) ? (int)Wild_tiger::decode($request->post('revise_record_id')) : 0);
				
				if ($reviseRecordId > 0) {
				
					$employeeSalary = ReviseSalaryInfo::with(['reviseSalaryMaster']);
				
					$employeeSalary->whereHas('reviseSalaryMaster', function ($employeeSalary) use ($reviseRecordId) {
						$employeeSalary->where('i_id', $reviseRecordId);
					});
				
					$employeeSalaryDetails = $employeeSalary->get();
			
					$data['employeeSalaryDetails'] = $employeeSalaryDetails;
			
					$data['salaryMasterInfo'] = (isset($data['employeeSalaryDetails'][0]->reviseSalaryMaster) ? $data['employeeSalaryDetails'][0]->reviseSalaryMaster : []);
					
				} else {
					$employeeSalary = EmployeeSalaryDetailModel::with(['employeeSalaryMaster']);
				
					$employeeSalary->whereHas('employeeSalaryMaster', function ($employeeSalary) use ($employeeId) {
						$employeeSalary->where('i_employee_id', $employeeId);
					});
				
					$employeeSalaryDetails = $employeeSalary->get();
			
					$data['employeeSalaryDetails'] = $employeeSalaryDetails;
			
					$data['salaryMasterInfo'] = (isset($data['employeeSalaryDetails'][0]->employeeSalaryMaster) ? $data['employeeSalaryDetails'][0]->employeeSalaryMaster : []);
			}
			
			$data['salaryUsedComponentIds'] = (!empty($data['employeeSalaryDetails']) ?  array_column(objectToArray($data['employeeSalaryDetails']), 'i_salary_component_id') : []);
				
				$salaryComponentWhere = [];
				$salaryComponentWhere['t_is_deleted'] = 0;
				$salaryComponentWhere['t_is_active'] = 1;
				$salaryComponentWhere['i_salary_group_id'] = $salaryGroupdId;
				$salaryComponentDetails = SalaryGroupDetailsModel::with(['salaryComponentInfo'])->where($salaryComponentWhere)->get();
				$data['salaryComponentDetails'] = $salaryComponentDetails;

				//echo "<pre>";print_r($salaryComponentDetails);
				
				$html = view(config('constants.AJAX_VIEW_FOLDER') . 'employee-master/salary-group-components-breakup')->with($data)->render();
				echo $html;
				die;
			}
		}
	}

	public function updateReviseSalary(Request $request)
	{
		if (!empty($request->input())) {
			
			$employeeId = (!empty($request->input('revise_salary_employee_id')) ? (int)Wild_tiger::decode($request->input('revise_salary_employee_id')) : 0);
			$reviseSalaryRecordId = (!empty($request->input('revise_salary_record_id')) ? (int)Wild_tiger::decode($request->input('revise_salary_record_id')) : 0);
			$reviseSalary = true;

			
			
			$successMessage  = trans('messages.success-salary-edit');
			$errorMessage  = trans('messages.error-salary-edit');

			if ($reviseSalaryRecordId == 0) {
				$reviseSalary = false;
				$successMessage  = trans('messages.success-salary-revise');
				$errorMessage  = trans('messages.error-salary-revise');
			}

			$salaryGroupId = (!empty($request->post('salary_group')) ? (int)Wild_tiger::decode($request->post('salary_group')) : 0);
			
			$salaryComponentDetails = SalaryGroupDetailsModel::with(['salaryComponentInfo'])->where('i_salary_group_id', $salaryGroupId)->get();

			
			
			$allSalaryDetails = [];
			$totalEarning = 0;
			$totalDeduct = 0;
			if (!empty($salaryComponentDetails)) {
				foreach ($salaryComponentDetails as $salaryComponentDetail) {
					
					if( in_array( $salaryComponentDetail->i_salary_components_id , [ config('constants.PF_SALARY_COMPONENT_ID') , config('constants.ON_HOLD_SALARY_COMPONENT_ID') ] ) ){
						continue;
					}
					
					$rowSalaryData = [];
					$rowSalaryData['i_salary_component_id'] = $salaryComponentDetail->i_salary_components_id;
					$rowSalaryData['d_amount'] = (!empty($request->input('salary_compoent_id_' . $salaryComponentDetail->i_salary_components_id)) ? $request->input('salary_compoent_id_' . $salaryComponentDetail->i_salary_components_id) : 0);

					if (!empty($rowSalaryData['d_amount'])) {
						switch ($salaryComponentDetail->e_type) {
							case config('constants.SALARY_COMPONENT_TYPE_EARNING'):
								$totalEarning += $rowSalaryData['d_amount'];
								break;
							case config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'):
								$totalDeduct += $rowSalaryData['d_amount'];
								break;
						}
					}
					$allSalaryDetails[] = $rowSalaryData;
				}
			}
			
			
			
			$deductEmployerFromEmployee = (!empty($request->post('deduction_employer_from_employee')) ?  $request->post('deduction_employer_from_employee') : config('constants.SELECTION_NO'));
			$deductPF = (!empty($request->post('deduction_of_pf')) ?  $request->post('deduction_of_pf') : config('constants.SELECTION_NO'));
			$effectativeFromDate = (!empty($request->post('effective_from')) ? dbDate($request->post('effective_from')) : date('Y-m-d'));

			$salaryMasterData['e_pf_by_employer'] = (!empty($request->post('deduction_employer_from_employee')) ?  $request->post('deduction_employer_from_employee') : config('constants.SELECTION_NO'));
			$salaryMasterData['e_pf_deduction'] = (!empty($request->post('deduction_of_pf')) ?  $request->post('deduction_of_pf') : config('constants.SELECTION_NO'));
			$salaryMasterData['d_total_earning'] = $totalEarning;
			$salaryMasterData['d_total_deduction'] = $totalDeduct;
			$salaryMasterData['d_net_pay_monthly'] = ($totalEarning - $totalDeduct);
			$salaryMasterData['d_net_pay_annually'] =  round((($totalEarning - $totalDeduct) * 12), 2);

			$totalNetPayMonthly = ($totalEarning - $totalDeduct);
			$totalNetPayAnnually =  round((($totalEarning - $totalDeduct) * 12), 2);

			$editSalaryReviseId = 0;

			$newSalaryDetails = $allSalaryDetails;
			$newSalaryDetailsCompoentIds = (!empty($newSalaryDetails) ? array_column($newSalaryDetails, 'i_salary_component_id') : []);
			
			$newReviseSalaryDetails = $allSalaryDetails;
			$newReviseSalaryDetailsCompoentIds = (!empty($newReviseSalaryDetails) ? array_column($newReviseSalaryDetails, 'i_salary_component_id') : []);

			$result = false;
			DB::beginTransaction();

			try {

				if( strtotime( $effectativeFromDate ) <= strtotime(date("Y-m-d")) ){
				
					$employeeSalaryMaster = [];
					$employeeSalaryMaster['i_employee_id'] = $employeeId;
					$employeeSalaryMaster['i_salary_group_id'] = $salaryGroupId;
					$employeeSalaryMaster['e_pf_by_employer'] = $deductEmployerFromEmployee;
					$employeeSalaryMaster['e_pf_deduction'] = $deductPF;
					$employeeSalaryMaster['d_total_earning'] = $totalEarning;
					$employeeSalaryMaster['d_total_deduction'] = $totalDeduct;
					$employeeSalaryMaster['d_net_pay_monthly'] = $totalNetPayMonthly;
					$employeeSalaryMaster['d_net_pay_annually'] = $totalNetPayAnnually;
				
					$checkSalaryMasterExist = EmployeeSalaryModel::where('i_employee_id' , $employeeId )->first();
				
					if(!empty($checkSalaryMasterExist)){
						$salaryMasterId = $checkSalaryMasterExist->i_id;
						$this->crudModel->updateTableData(config('constants.EMPLOYEE_SALARY_MASTER_TABLE'), $employeeSalaryMaster, ['i_id' => $checkSalaryMasterExist->i_id]);
					} else {
						$salaryMasterId = $this->crudModel->insertTableData(config('constants.EMPLOYEE_SALARY_MASTER_TABLE'), $employeeSalaryMaster);
					}
				
				
					$employeeSalary = EmployeeSalaryDetailModel::with(['employeeSalaryMaster']);
				
					$employeeSalary->whereHas('employeeSalaryMaster', function ($employeeSalary) use ($employeeId) {
						$employeeSalary->where('i_employee_id', $employeeId);
					});
				
					$previousSalaryDetails = $employeeSalary->get();
			
					if (!empty($previousSalaryDetails)) {
						foreach ($previousSalaryDetails as $previousSalaryDetails) {
							if ( ($request->has('salary_compoent_id_' . $previousSalaryDetails->i_salary_component_id))) {
								if (in_array($previousSalaryDetails->i_salary_component_id, $newSalaryDetailsCompoentIds)) {
									$searchKey = array_search($previousSalaryDetails->i_salary_component_id, $newSalaryDetailsCompoentIds);
									if (strlen($searchKey) > 0) {
										unset($newSalaryDetails[$searchKey]);
									}
								}
								$updateValue = $request->input('salary_compoent_id_' . $previousSalaryDetails->i_salary_component_id);
								$this->crudModel->updateTableData(config('constants.EMPLOYEE_SALARY_DETAIL_TABLE'), ['d_amount' => $updateValue], ['i_id' => $previousSalaryDetails->i_id]);
							} else {
								$this->crudModel->deleteTableData(config('constants.EMPLOYEE_SALARY_DETAIL_TABLE'), ['t_is_active' => 0, 't_is_deleted' => 1], ['i_id' => $previousSalaryDetails->i_id]);
							}
						}
					}
			
					$salaryInfo = [];
					if (!empty($newSalaryDetails)) {
						foreach ($newSalaryDetails as $newSalaryDetail) {
							$salaryRowData = [];
							$salaryRowData['i_employee_salary_id'] = $salaryMasterId;
							$salaryRowData['i_salary_component_id'] = $newSalaryDetail['i_salary_component_id'];
							$salaryRowData['d_amount'] = $newSalaryDetail['d_amount'];
							$salaryRowData ['i_created_id'] = session()->get('user_id');
							$salaryRowData ['dt_created_at'] = date('Y-m-d H:i:s');
							$salaryInfo[] = $salaryRowData;
						}
					}
			
					DB::table(config('constants.EMPLOYEE_SALARY_DETAIL_TABLE'))->insert($salaryInfo);
							
				}
				
				
				if ($reviseSalaryRecordId == 0 ) {

					$reviseSalaryMaster = [];
					$reviseSalaryMaster['i_employee_id'] = $employeeId;
					$reviseSalaryMaster['dt_effective_date'] =  $effectativeFromDate;
					$reviseSalaryMaster['i_salary_group_id'] = $salaryGroupId;
					$reviseSalaryMaster['e_pf_by_employer'] = $deductEmployerFromEmployee;
					$reviseSalaryMaster['e_pf_deduction'] = $deductPF;
					$reviseSalaryMaster['d_total_earning'] = $totalEarning;
					$reviseSalaryMaster['d_total_deduction'] = $totalDeduct;
					$reviseSalaryMaster['d_net_pay_monthly'] = $totalNetPayMonthly;
					$reviseSalaryMaster['d_net_pay_annually'] = $totalNetPayAnnually;

					$insertReviseSalary = $this->crudModel->insertTableData(config('constants.REVISE_SALARY_MASTER_TABLE'), $reviseSalaryMaster);

					$reviseSalaryInfo = [];
					if (!empty($allSalaryDetails)) {
						foreach ($allSalaryDetails as $allSalaryDetail) {
							$reviseSalaryRowData = [];
							$reviseSalaryRowData['i_employee_revise_salary_id'] = $insertReviseSalary;
							$reviseSalaryRowData['i_salary_component_id'] = $allSalaryDetail['i_salary_component_id'];
							$reviseSalaryRowData['d_amount'] = $allSalaryDetail['d_amount'];
							$reviseSalaryRowData ['i_created_id'] = session()->get('user_id');
							$reviseSalaryRowData ['dt_created_at'] = date('Y-m-d H:i:s');
							$reviseSalaryInfo[] = $reviseSalaryRowData;
						}
					}

					DB::table(config('constants.REVISE_SALARY_INFO_TABLE'))->insert($reviseSalaryInfo);
				
				} else {

					$reviseSalaryMaster = [];
					$reviseSalaryMaster['i_employee_id'] = $employeeId;
					$reviseSalaryMaster['dt_effective_date'] =  $effectativeFromDate;
					$reviseSalaryMaster['i_salary_group_id'] = $salaryGroupId;
					$reviseSalaryMaster['e_pf_deduction'] = $deductPF;
					$reviseSalaryMaster['e_pf_by_employer'] = $deductEmployerFromEmployee;
					$reviseSalaryMaster['d_total_earning'] = $totalEarning;
					$reviseSalaryMaster['d_total_deduction'] = $totalDeduct;
					$reviseSalaryMaster['d_net_pay_monthly'] = $totalNetPayMonthly;
					$reviseSalaryMaster['d_net_pay_annually'] = $totalNetPayAnnually;
					
					$insertReviseSalary = $this->crudModel->updateTableData(config('constants.REVISE_SALARY_MASTER_TABLE'), $reviseSalaryMaster , [ 'i_id' => $reviseSalaryRecordId ] );
					//echo $this->crudModel->last_query();

					$previousReviseSalaryDetails = ReviseSalaryInfo::where('i_employee_revise_salary_id', $reviseSalaryRecordId)->get();

					if (!empty($previousReviseSalaryDetails)) {
						foreach ($previousReviseSalaryDetails as $previousReviseSalaryDetail) {
							if ( ($request->has('salary_compoent_id_' . $previousReviseSalaryDetail->i_salary_component_id))) {
								if (in_array($previousReviseSalaryDetail->i_salary_component_id, $newReviseSalaryDetailsCompoentIds)) {
									$searchKey = array_search($previousReviseSalaryDetail->i_salary_component_id, $newReviseSalaryDetailsCompoentIds);
									if (strlen($searchKey) > 0) {
										unset($newReviseSalaryDetails[$searchKey]);
									}
								}
								$updateValue = $request->input('salary_compoent_id_' . $previousReviseSalaryDetail->i_salary_component_id);
								$this->crudModel->updateTableData(config('constants.REVISE_SALARY_INFO_TABLE'), ['d_amount' => $updateValue], ['i_id' => $previousReviseSalaryDetail->i_id]);
							} else {
								$this->crudModel->deleteTableData(config('constants.REVISE_SALARY_INFO_TABLE'), ['t_is_active' => 0, 't_is_deleted' => 1], ['i_id' => $previousReviseSalaryDetail->i_id]);
							}
						}
					}

					$reviseSalaryInfo = [];
					if (!empty($newReviseSalaryDetails)) {
						foreach ($newReviseSalaryDetails as $newReviseSalaryDetail) {
							$reviseSalaryRowData = [];
							$reviseSalaryRowData['i_employee_revise_salary_id'] = $reviseSalaryRecordId;
							$reviseSalaryRowData['i_salary_component_id'] = $newReviseSalaryDetail['i_salary_component_id'];
							$reviseSalaryRowData['d_amount'] = $newReviseSalaryDetail['d_amount'];
							$reviseSalaryRowData ['i_created_id'] = session()->get('user_id');
							$reviseSalaryRowData ['dt_created_at'] = date('Y-m-d H:i:s');
							$reviseSalaryInfo[] = $reviseSalaryRowData;
						}
					}

					DB::table(config('constants.REVISE_SALARY_INFO_TABLE'))->insert($reviseSalaryInfo);
				}

				$result = true;
			} catch (\Exception $ex) {
				var_dump($ex->getMessage());
				DB::rollback();
				$result = false;
			}

			//var_dump($result);die;
			if ($result != false) {
				DB::commit();
				$this->ajaxResponse(1, $successMessage);
			} else {
				DB::rollback();
				$this->ajaxResponse(101, $errorMessage);
			}
		}
	}

	
	public function myPayslip(Request $request)
	{
	
		$selectedYear = (!empty($request->input('selected_year')) ? $request->input('selected_year') : date('Y') );
		
		$startDate = date('Y-m-d', strtotime( 'first day of January ' . $selectedYear));
		$endDate = date('Y-m-d', strtotime( 'first day of December ' . $selectedYear));
		
		$paginationData = $whereData =  [];
		
		$page = $this->defaultPage;

		$data = [];
		$data['pageTitle'] = trans('messages.my-pay-slip');
		$data['pagination'] = $paginationData;
		$data['pageNo'] = $page;
		$data['perPageRecord'] = $this->perPageRecord;

		$ajaxRequest = false;
		
		if ($request->ajax()) {
			$ajaxRequest = true;
			$employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0);
		} else {
			$employeeId = session()->get('user_employee_id');
		}
		
		$salaryWhere = [];
		$salaryWhere['employee_id'] = $employeeId;
		$salaryWhere['salary_start_month'] = $startDate;
		$salaryWhere['salary_end_month'] = $endDate;
		
		$data['recordDetails'] = $this->crudModel->getSalaryRecordDetails($salaryWhere);
		
		
		$data['ajaxRequest'] = $ajaxRequest;
		$data['employeeId'] = $employeeId;
		
		$employeeInfo = EmployeeModel::where('i_id'  , $employeeId )->first();
		$employeeJoiningDate = (!empty($employeeInfo) ? $employeeInfo->dt_joining_date : null );
		
		$minYear = null;
		if( !empty($employeeJoiningDate) ){
			$empJoiningYear = date('Y', strtotime($employeeJoiningDate));
			if( $empJoiningYear > config('constants.SYSTEM_START_YEAR')){
				$minYear = $empJoiningYear;
			}
		}
		$data['yearDetails'] = yearDetails($minYear);
		
		if ($ajaxRequest != false) {
			$html = view(config('constants.ADMIN_FOLDER') . 'salary/employee-payslip')->with($data)->render();
			echo $html;
			die;
		}
		
		return view($this->folderName . 'my-payslip')->with($data);
	}

	public function myPayslipFilter(Request $request)
	{
		//variable defined
		$whereData = $likeData = $additionalData =  [];

		$page = (!empty($request->post('page')) ? $request->post('page') : 1);

		$selectedYear = date('Y');
		//search record
		if (!empty($request->post('search_year'))) {
			$searchByYear = trim($request->post('search_year'));
		}
		
		$employeeId =  (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : session()->get('user_employee_id') ); ;
		
		$startDate = date('Y-m-d', strtotime( 'first day of January ' . $selectedYear));
		$endDate = date('Y-m-d', strtotime( 'first day of December ' . $selectedYear));

		$paginationData = [];
		
		$salaryWhere = [];
		$salaryWhere['employee_id'] = $employeeId;
		$salaryWhere['salary_start_month'] = $startDate;
		$salaryWhere['salary_end_month'] = $endDate;
		
		$data['recordDetails'] = $this->crudModel->getSalaryRecordDetails($salaryWhere);

		$data['pagination'] = $paginationData;
		$data['pageNo'] = $page;
		$data['perPageRecord'] = $this->perPageRecord;

		$html = view(config('constants.AJAX_VIEW_FOLDER') . 'salary/my-payslip')->with($data)->render();

		echo $html;
		die;
	}
	
	public function deleteReviseSalaryRecord(Request $request){
		if(!empty($request->all())){
			$reviseSalaryRecordId = (!empty($request->input('revise_record_id')) ? (int)Wild_tiger::decode($request->input('revise_record_id')) : 0);
			if( $reviseSalaryRecordId > 0 ){
				$getReviseRecodInfo = ReviseSalaryMaster::where('i_id' , $reviseSalaryRecordId)->first();
				if( (!empty($getReviseRecodInfo)) ){
					
					if( strtotime( $getReviseRecodInfo->dt_effective_date ) > strtotime('now') ){
						
						$result = false;
						DB::beginTransaction();
						
						try{
							$deleteRecord = $this->crudModel->deleteTableData( config('constants.REVISE_SALARY_MASTER_TABLE') , [ 't_is_active' => 0 , 't_is_deleted' => 1 ], [ 'i_id' => $reviseSalaryRecordId ] );
							
							$checkRevisedSalaryRecord = ReviseSalaryMaster::where('i_employee_id' , $getReviseRecodInfo->i_employee_id )->first();
							
							if( count(objectToArray($checkRevisedSalaryRecord)) == 0 ){
									
								$getSalaryMasterRecord = EmployeeSalaryModel::where('i_employee_id' ,  $getReviseRecodInfo->i_employee_id )->first();
									
								if(!empty($getSalaryMasterRecord)){
									$this->crudModel->deleteTableData( config('constants.EMPLOYEE_SALARY_MASTER_TABLE') , [ 't_is_active' => 0 , 't_is_deleted' => 1 ], [ 'i_id' => $getSalaryMasterRecord->i_id ] );
									$this->crudModel->deleteTableData( config('constants.EMPLOYEE_SALARY_DETAIL_TABLE') , [ 't_is_active' => 0 , 't_is_deleted' => 1 ], [ 'i_employee_salary_id' => $getSalaryMasterRecord->i_id ] );
								}
									
							}
							$result = true;
						}catch(Exception $e){
							DB::commit();
							$result = false;
						}
						
						
						
						if( $result != false ){
							DB::commit();
							$this->ajaxResponse(1, trans ( 'messages.success-delete', [ 'module' => trans('messages.revise-salary') ] ) );
						} else {
							DB::rollback();
							$this->ajaxResponse(101, trans ( 'messages.error-delete', [ 'module' => trans('messages.revise-salary') ] ) );
						}
						
					}
					
				}
			}
			$this->ajaxResponse(101, trans('messages.system-error'));
		}
	}
	public function editOnHoldSalaryModel(Request $request){
		
		$data = $where = [];
		$employeeId = (!empty($request->post('employee_record_id')) ? (int)Wild_tiger::decode($request->post('employee_record_id')) : 0 );
		$joiningDate = (!empty($request->post('joining_date')) ? ($request->post('joining_date')) : "");
		
		if($employeeId > 0 ){
			$where['master_id'] = $employeeId;
			$where['singleRecord'] = true;
			if( session()->has('user_permission') && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.ALL_VIEW_SALARY'), session()->get('user_permission')  ) ) ){
				$where['show_all'] = true;
			}
			$employeeInfo = $this->employeeModel->getRecordDetails($where);
			
			if(!empty($employeeInfo)){
				$data['holdSalaryDetails'] = (!empty($employeeInfo->onHoldSalaryInfo) ? $employeeInfo->onHoldSalaryInfo : "");
				$data['onholdRecordId'] = (!empty($employeeInfo->onHoldSalaryInfo[0]->i_id) ? $employeeInfo->onHoldSalaryInfo[0]->i_id : "" );
				
			}
		}
		$data['onHoldSalaryjoiningDate'] = (!empty($employeeInfo->dt_joining_date) ? convertDateFormat($employeeInfo->dt_joining_date,  'M-Y') :'');
		
		$html = view ($this->folderName . 'add-on-hold-salary-model')->with ( $data )->render();
		echo $html;die;
	}
	public function addOnHoldSalary(Request $request){
		
		if(!empty($request->post())){
			
			$employeeId = (!empty($request->post('employee_id')) ? (int) Wild_tiger::decode($request->post('employee_id')) : 0 );
			$selectedMonthCount  = (!empty($request->post('on_hold_salary_count')) ? $request->post('on_hold_salary_count') : 0 );
			$onHoldSalaryRecordId  = (!empty($request->post('remove_data_id')) ? $request->post('remove_data_id') : 0 );
			$onHoldSalaryRemoveIds = (isset($onHoldSalaryRecordId) ? explode(",", $onHoldSalaryRecordId) : []);
				
			$result = false;
			$html = null;
			$successMessages =  trans('messages.success-create',['module'=> trans('messages.on-hold-salary')]);
			$errorMessages =  trans('messages.error-create',['module'=> trans('messages.on-hold-salary')]);
			
			if($employeeId > 0 ){
				$where = [];
				$where['master_id'] = $employeeId;
				$where['singleRecord'] = true;
				if( session()->has('user_permission') && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.ALL_VIEW_SALARY'), session()->get('user_permission')  ) ) ){
					$where['show_all'] = true;
				}
				
				$haveRecord = false;
     			$result = false;
     			DB::beginTransaction();
     			try{
     				
     				$employeeInfo = EmployeeModel::where('i_id'  , $employeeId )->first();
     				
     				
     				$emplyeeOnHoldSalaryDetails = $this->employeeModel->getRecordDetails($where);
     				
     				if(!empty($emplyeeOnHoldSalaryDetails->onHoldSalaryInfo)){
     						
     					$successMessages =  trans('messages.success-update',['module'=> trans('messages.on-hold-salary')]);
     					$errorMessages =  trans('messages.error-update',['module'=> trans('messages.on-hold-salary')]);
     						
     					foreach ($emplyeeOnHoldSalaryDetails->onHoldSalaryInfo as $onHoldSalaryInfo){
     						$onHoldSalaryId = (!empty($onHoldSalaryInfo->i_id) ? $onHoldSalaryInfo->i_id : '');
     				
     						if(!empty($request->post('edit_month_'.$onHoldSalaryId))){
     							$rowData = [];
     							$monthDate = (!empty($request->post('edit_month_'.$onHoldSalaryId)) ? date('Y-m-d',strtotime('01-'.$request->post('edit_month_'.$onHoldSalaryId))) : '');
     							$rowData['dt_month'] = (!empty($monthDate) ? dbDate($monthDate) : '');
     							$rowData['d_amount'] = (!empty($request->post('edit_amount_'.$onHoldSalaryId)) ? trim($request->post('edit_amount_'.$onHoldSalaryId)) : null);
     							if((!empty($rowData['dt_month']))){
     								$haveRecord = true;
     								$result = $this->crudModel->updateTableData(config('constants.EMPLOYEE_HOLD_SALARY_INFO'),$rowData, ['i_id' => $onHoldSalaryId ]);
     							}
     						} else {
     							$deleteRecordData = [];
     							$deleteRecordData ['t_is_active'] = 0;
     							$deleteRecordData ['t_is_deleted'] = 1;
     							$result = $this->crudModel->deleteTableData( config('constants.EMPLOYEE_HOLD_SALARY_INFO') , $deleteRecordData , [ 'i_id' => $onHoldSalaryId] );
     						}
     				
     				
     				
     					}
     				}
     				for ($i = 1; $i <= $selectedMonthCount; $i++){
     					$rowData = [];
     					$monthDate = (!empty($request->post('month_'.$i)) ? date('Y-m-d',strtotime('01-'.$request->post('month_'.$i))) : '');
     				
     					$rowData['i_employee_id'] = $employeeId;
     					$rowData['dt_month'] = (!empty($monthDate) ? dbDate($monthDate) : '');
     					$rowData['d_amount'] = (!empty($request->post('amount_'.$i)) ? trim($request->post('amount_'.$i)) : null);
     						
     					if((!empty($rowData['dt_month']))){
     						$insertRecord = $this->crudModel->insertTableData( config('constants.EMPLOYEE_HOLD_SALARY_INFO') , $rowData);
     						if($insertRecord > 0){
     							$haveRecord = true;
     							$result = true;
     						}
     					}
     				}
     				if( $haveRecord  != false ){
     					$updateEmployeeSalaryInfo = [];
     					$updateEmployeeSalaryInfo['e_hold_salary_status'] = config('constants.SELECTION_YES');
     					$updateEmployeeSalaryInfo['dt_on_hold_expected_release_date'] = $employeeInfo->dt_on_hold_expected_release_date;
     					if( !in_array(  $employeeInfo->e_hold_salary_payment_status  , [ config('constants.NOT_TO_PAY_STATUS') , config('constants.DONATED_STATUS') ] ) ){
     						$updateEmployeeSalaryInfo['e_hold_salary_payment_status'] = config('constants.PENDING_STATUS');
     						$onHoldSalaryDetails = EmployeeModel::with( [ 'onHoldSalaryInfo' , 'generatedSalaryMaster' , 'generatedSalaryMaster.generatedSalaryInfo'  ] )->where('i_id' , $employeeId)->first();
     						if(!empty($onHoldSalaryDetails)){
     							$getHoldAmountInfo = getHoldAmountInfo($onHoldSalaryDetails);
     							$totalOnHoldSalaryAmount = ( isset($getHoldAmountInfo['totalOnHoldSalaryAmount']) ? $getHoldAmountInfo['totalOnHoldSalaryAmount'] : 0 ) ;
     							$decuctOnHoldSalaryAmount = ( isset($getHoldAmountInfo['deductOnHoldSalaryAmount']) ? $getHoldAmountInfo['deductOnHoldSalaryAmount'] : 0 ) ;
     							$leftAmount = ( isset($getHoldAmountInfo['leftOnHoldSalaryAmount']) ? $getHoldAmountInfo['leftOnHoldSalaryAmount'] : 0 ) ;
     							$expectedReleaseDate = ( isset($getHoldAmountInfo['expectedReleaseDate']) ? $getHoldAmountInfo['expectedReleaseDate'] : null ) ;
     							$releaseDate = ( isset($getHoldAmountInfo['releaseDate']) ? $getHoldAmountInfo['releaseDate'] : null ) ;
     							if( $leftAmount == 0 ){
     								$updateEmployeeSalaryInfo['e_hold_salary_payment_status'] = config('constants.PAID_STATUS');
     							}
     							$updateEmployeeSalaryInfo['dt_on_hold_expected_release_date'] = $expectedReleaseDate;
     							$updateEmployeeSalaryInfo['dt_on_hold_release_date'] = $releaseDate;
     						}
     						
     					}
     					$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , $updateEmployeeSalaryInfo , [ 'i_id' => $employeeId ] );
     				} else {
     					$updateEmployeeSalaryInfo['e_hold_salary_status'] = config('constants.SELECTION_NO');
     					$updateEmployeeSalaryInfo['e_hold_salary_payment_status'] = null;
     					$updateEmployeeSalaryInfo['dt_on_hold_expected_release_date'] = null;
     					$updateEmployeeSalaryInfo['dt_on_hold_release_date'] = null;
     					$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , $updateEmployeeSalaryInfo , [ 'i_id' => $employeeId ] );
     				}
     				$result = true; 
     			}catch(\Exception $e){
     				DB::rollback();
     				$result = false;
     			}
				
			}
			
			if($result != false){
				 DB::commit();
				$this->ajaxResponse(1, $successMessages);
			}else {
				DB::rollback();
				$this->ajaxResponse(101, $errorMessages);
			}
		}
		
	}
	public function checkUniqueMonthName(Request $request){
		
		
	}
	
	public function generateSalarySlip( $salaryInfo = []){
		
		$data = [];
		$data['employeeId'] = ( isset($salaryInfo['employeeId']) ? $salaryInfo['employeeId'] : "" );
		$data['employeeInfo'] = ( isset($salaryInfo['employeeInfo']) ? $salaryInfo['employeeInfo'] : [] );
		$data['salaryMonth'] = ( isset($salaryInfo['salaryMonth']) ? date('F-Y' ,strtotime($salaryInfo['salaryMonth'])) : "" ); 
		$employeeCode = ( isset($data['employeeInfo']->v_employee_code) ? $data['employeeInfo']->v_employee_code : '' );
		$employeeFullName = ( isset($data['employeeInfo']->v_employee_full_name) ? $data['employeeInfo']->v_employee_full_name : '' );
		$html = view( $this->folderName . 'salary-slip-pdf')->with($data);
		$fileName = (!empty($employeeFullName) ? $employeeFullName . '-' . (!empty($employeeCode) ? $employeeCode . '-' : '' ) : '' ) . date('F-Y' ,strtotime($data['salaryMonth']));
		$result = generatePDF($html,$fileName,'store');
		return $result;
		
	}
	
	public function viewSalarySlip($salaryRecordId = null , $actionType = "view" ){
		$errorFound = true;
		if(!empty($salaryRecordId)){
			$salaryRecordId = (int)Wild_tiger::decode($salaryRecordId);
			$whereData = [];
			$whereData['master_id'] = $salaryRecordId;
			$salaryRecordDetails =  $this->crudModel->getSalaryRecordDetails($whereData);
			$data['recordInfo'] = ( isset($salaryRecordDetails[0]) ? $salaryRecordDetails[0] : [] );
			if(!empty($data['recordInfo'])){
				$errorFound = false;
				$html = view( $this->folderName . 'salary-slip-pdf')->with($data);
				$fileName = (isset($data['recordInfo']->employee->v_employee_full_name) ? $data['recordInfo']->employee->v_employee_full_name . '-' . (isset($data['recordInfo']->employee->v_employee_code) ? $data['recordInfo']->employee->v_employee_code . '-' : '' ) : '' ) . convertDateFormat($data['recordInfo']->dt_salary_month, 'F-Y')  ;
				$result = generatePDF($html,$fileName , $actionType );
				if( $actionType ==  config('constants.STORE_PDF') ){
					$result['recordInfo'] = $data['recordInfo'];
					return $result;
				}
			}
		}
		if( $errorFound != false ){
			echo "error";
		}
		
	}
	
	public function downloadSalary( $salaryRecordId = null ){
		$this->viewSalarySlip( $salaryRecordId , config('constants.DOWNLOAD_PDF') );
	}
	
	public function sendSinglePaySlip( Request $request){
		if(!empty($request->input())){
			$salaryRecordId = (!empty($request->input('record_id')) ? ($request->input('record_id')) : 0 );
			if( !empty($salaryRecordId)){
				
				$result = $this->viewSalarySlip( $salaryRecordId , config('constants.STORE_PDF') );
				
				if( isset($result['status']) && ( $result['status'] != false ) ){
					
					$allSalarySlips = [];
					$allSalarySlips[] = $result['filePath'];
					
					$recordInfo = ( isset($result['recordInfo']) ? $result['recordInfo'] : [] ); 
					
					$sendSlip = $this->commonSendPaySlip( $recordInfo , $allSalarySlips );
					
					if( $sendSlip != false ){
						
						$receiveEmail = ( isset($recordInfo->employee->v_personal_email_id) ? $recordInfo->employee->v_personal_email_id : config('constants.SYSTEM_ADMIN_EMAIL') ) ;
						
						$this->ajaxResponse(1, trans('messages.success-send-pay-slip' , [ 'receiveEmail' => $receiveEmail ]) );
					} else {
						$this->ajaxResponse(101, trans('messages.error-send-pay-slip') );
					}
					
				}
				
			}
		}
	}
	
	private function commonSendPaySlip( $recordInfo , $allSalarySlips = [] ){
		//echo "<pre>";print_r($recordInfo);die;
		$mailData = [];
		$mailData['employeeName'] = ( isset($recordInfo->employee->v_employee_full_name) ? $recordInfo->employee->v_employee_full_name : 'User' );
		
		$subject = trans('messages.pay-slip-subject');
		$mailTemplate = view( $this->mailTemplateFolderPath .  'send-pay-slip-mail', $mailData)->render();
		$receiveEmail = ( isset($recordInfo->employee->v_personal_email_id) ? $recordInfo->employee->v_personal_email_id : config('constants.SYSTEM_ADMIN_EMAIL') ) ;
		$emailHistoryData = [];
		$emailHistoryData['i_login_id'] = ( isset($recordInfo->employee->i_login_id) ? $recordInfo->employee->i_login_id : 0 ) ;
		$emailHistoryData['i_related_record_id'] = null;
		$emailHistoryData['v_event'] = "Send Salay Slip";
		$emailHistoryData['v_receiver_email'] = $receiveEmail;
		$emailHistoryData['v_subject'] = $subject;
		$emailHistoryData['v_mail_content'] = $mailTemplate;
		$emailHistoryData['v_notification_title'] = null;
			
		$insertEmail = $this->crudModel->insertTableData(config('constants.EMAIL_HISTORY_TABLE'), $emailHistoryData);
			
		$viewName = "";
		$config['mailData'] = $mailData;
		$config['viewName'] = $this->mailTemplateFolderPath . 'send-pay-slip-mail' ;
		$config['v_mail_content'] = $mailTemplate;
		$config['subject'] = $subject;
		$config['to'] = $receiveEmail;;
		if(!empty($allSalarySlips)){
			$config['attachment'] = json_encode($allSalarySlips);
		}
		
			
			
		$sendMail = [];
		$mailSendError = null;
		try{
			$sendMail = sendMailSMTP($config);
		}catch(\Exception $e){
			//var_dump($e->getMessage());die;
			$mailSendError = $e->getMessage();
		}
		//var_dump($sendMail);die;
		$updateEmailData = [];
		if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
			$updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
		} else {
			$updateEmailData['e_status'] = config('constants.FAILED_STATUS');
			$updateEmailData['v_response'] = $mailSendError;
		}
			
		$this->crudModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateEmailData , [ 'i_id' => $insertEmail ] );
			
		if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
			return true;
			//$this->ajaxResponse(1, trans('messages.success-send-pay-slip' , [ 'receiveEmail' => $receiveEmail ]) );
		} else {
			return false;
			//$this->ajaxResponse(101, trans('messages.error-send-pay-slip') );
		}
		
	}
	
	public function sendMultiplePaySlip(Request $request){
		
		if(!empty($request->input())){
			$salaryRecordIds = (!empty($request->input('record_id')) ? ($request->input('record_id')) : [] );
			if( !empty($salaryRecordIds)){
		
				if(!empty($salaryRecordIds)){
					$allSalarySlips = [];
					$employeeWiseSalarySlips = [];
					$result = [];
					foreach($salaryRecordIds as $salaryRecordId){
						$result = $this->viewSalarySlip( $salaryRecordId , config('constants.STORE_PDF') );
						if( isset($result['status']) && ( $result['status'] != false ) ){
							$recordInfo = ( isset($result['recordInfo']) ? $result['recordInfo'] : [] );
							if( isset($recordInfo->i_employee_id) && (!empty($recordInfo->i_employee_id)) ){
								//unset($result['recordInfo']);
								$employeeWiseSalarySlips[$recordInfo->i_employee_id][] =  $result;
							}
						}
					}
					
					$sendSalarySlip = false;
					if(!empty($employeeWiseSalarySlips)){
						foreach($employeeWiseSalarySlips as $employeeWiseSalarySlipKey => $employeeWiseSalarySlip){
							
							$generateSlipPays = [];
							$salaryRecordInfo = [];
							if(!empty($employeeWiseSalarySlip)){
								foreach($employeeWiseSalarySlip as $employeeWiseSalary){
									if(empty($salaryRecordInfo)){
										$salaryRecordInfo = ( isset($employeeWiseSalary['recordInfo']) ? $employeeWiseSalary['recordInfo'] : [] );
									}
									if( isset($employeeWiseSalary['filePath']) && (!empty($employeeWiseSalary['filePath'])) ){
										$generateSlipPays[] = $employeeWiseSalary['filePath'];
									}
									
								}
							}
							if(!empty($generateSlipPays)){
								$sendSalarySlip = $this->commonSendPaySlip( $salaryRecordInfo , $generateSlipPays );
							}
						}
					}
					
					if( $sendSalarySlip != false ){
						$this->ajaxResponse(1, trans('messages.success-multiple-send-pay-slip' ) );
					} else {
						$this->ajaxResponse(101, trans('messages.error-send-pay-slip') );
					}
					
				}
			}
			$this->ajaxResponse(101, trans('messages.sytem-error') );
		}
		
	}
	
	public function sendSalarySlip(Request $request){
		
		$employeeId = (!empty($request->post('employee_id')) ? (int) Wild_tiger::decode($request->post('employee_id')) : 0 );
		$slipYear  = (!empty($request->post('salary_slip_year')) ? trim($request->post('salary_slip_year')) : date('Y') );
		$selectedMonth  = (!empty($request->post('salary_months')) ? ($request->post('salary_months')) : [] );
		
		if(!empty($employeeId) && (!empty($slipYear))){
			$allMonths = [];
			
			$curruntYearLastMonth = date('Y-m-d', strtotime('last day of previous month'));
			
			for($i = 1 ; $i <= 12 ; $i++ ){
				if( $i <= 9 ){
					$i = "0".$i;
				}
				if( strtotime($curruntYearLastMonth) >= strtotime(date('Y-m-01' , strtotime($slipYear.$i.'01')) ) ){
					if(in_array( date('Y-m-01' , strtotime($slipYear.$i.'01')) , $selectedMonth )){
						$allMonths[] = date('Y-m-01' , strtotime($slipYear.$i.'01'));
					}
					
				}
					
			}
			
			$employeeInfo = [];
			$employeeInfo = EmployeeModel::with(['bankInfo' , 'designationInfo'])->where('i_id' , $employeeId)->first();
			
			$allSalarySlips = [];
			if(!empty($allMonths)){
				foreach($allMonths as $allMonth){
					$salaryInfo  = [];
					$salaryInfo['employeeId'] = $employeeId;
					$salaryInfo['salaryMonth'] = $allMonth;
					$salaryInfo['employeeInfo'] = $employeeInfo;
					
					$createSlip = $this->generateSalarySlip($salaryInfo);
					if( isset($createSlip['status']) && ( $createSlip['status'] != false ) ){
						$allSalarySlips[] = $createSlip['filePath'];
					}
				}
			}
			
			if(!empty($allSalarySlips)){
				
				$mailData = [];
				$mailData['employeeName'] = ( isset($employeeInfo->v_employee_full_name) ? $employeeInfo->v_employee_full_name : 'User' );
			
				$subject = trans('messages.pay-slip-subject');
				$mailTemplate = view( $this->mailTemplateFolderPath .  'send-pay-slip-mail', $mailData)->render();
				$receiveEmail = ( isset($employeeInfo->v_personal_email_id) ? $employeeInfo->v_personal_email_id : config('constants.SYSTEM_ADMIN_EMAIL') ) ;
				$emailHistoryData = [];
				$emailHistoryData['i_login_id'] = ( isset($employeeInfo->i_login_id) ? $employeeInfo->i_login_id : 0 ) ;
				$emailHistoryData['i_related_record_id'] = null;
				$emailHistoryData['v_event'] = "Send Salay Slip";
				$emailHistoryData['v_receiver_email'] = $receiveEmail;
				$emailHistoryData['v_subject'] = $subject;
				$emailHistoryData['v_mail_content'] = $mailTemplate;
				$emailHistoryData['v_notification_title'] = null;
				
				$insertEmail = $this->crudModel->insertTableData(config('constants.EMAIL_HISTORY_TABLE'), $emailHistoryData);
				
				$viewName = "";
				$config['mailData'] = $mailData;
				$config['viewName'] = $this->mailTemplateFolderPath . 'send-pay-slip-mail' ;
				$config['v_mail_content'] = $mailTemplate;
				$config['subject'] = $subject;
				$config['to'] = $receiveEmail;;
				$config['attachment'] = json_encode($allSalarySlips);
				
				
				$sendMail = [];
				$mailSendError = null;
				try{
					$sendMail = sendMailSMTP($config);
				}catch(\Exception $e){
					//var_dump($e->getMessage());die;
					$mailSendError = $e->getMessage();
				}
				//var_dump($sendMail);die;
				$updateEmailData = [];
				if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
					$updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
				} else {
					$updateEmailData['e_status'] = config('constants.FAILED_STATUS');
					$updateEmailData['v_response'] = $mailSendError;
				}
				
				$this->crudModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateEmailData , [ 'i_id' => $insertEmail ] );
				
				if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
					$this->ajaxResponse(1, trans('messages.success-send-pay-slip' , [ 'receiveEmail' => $receiveEmail ]) );
				} else {
					$this->ajaxResponse(101, trans('messages.error-send-pay-slip') );
				}
			}
			$this->ajaxResponse(101, trans('messages.error-generate-pay-slip') );
			
			
		}
		$this->ajaxResponse(101, trans('messages.system-error') );
		
	}
	
	public function summary()
	{
		$this->salarySummaryInfo();
		$data = [];
		$data['pageTitle'] = trans('messages.salary-summary');
		
		$selectYear = date('Y');
		$teamId = 0;
		
		$salaryInfo = $this->salarySummaryInfo($selectYear , $teamId );
		
		$data = (!empty($data) ? array_merge($data , $salaryInfo) : $salaryInfo ); 
		
		$data['selectedYear'] = $selectYear;
		
		$data['yearDetails'] = yearDetails();
		 
		$data['teamDetails'] = LookupMaster::where('v_module_name', config('constants.TEAM_LOOKUP'))->orderBy('v_value', 'ASC')->get();
	
		return view($this->folderName . 'salary-summary')->with($data);
	}
	
	private function salarySummaryInfo( $year = null , $teamId = 0 ){
		
		$showAllEmployee = $employeeId = 0;
		if(  ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) ||  ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_SALARY_SUMMARY'), session()->get('user_permission')  ) ) ) ){
			$showAllEmployee = 1;
		} else {
			$allChildEmployeeIds = $this->crudModel->childEmployeeIds();
			$employeeId = (!empty($allChildEmployeeIds) ? implode(',', $allChildEmployeeIds) : 0);
		}
		
		$selectYear = (!empty($year) ? $year : date('Y') )  ;
		$salarySummaryInfo =  self::CallRaw ( 'salary_summary' , [ $selectYear , $teamId , $employeeId , $showAllEmployee ]);
		$lastSixMonthRecords = ( isset($salarySummaryInfo[0][0]) ? $salarySummaryInfo[0] : [] );
		$lastMonthNetSalaryAmount = ( ( isset($salarySummaryInfo[1][0]) && (isset($salarySummaryInfo[1][0]->last_month_net_pay_amount)) ) ? $salarySummaryInfo[1][0]->last_month_net_pay_amount : 0 );
		$lastMonthSalaryMonthName = ( ( isset($salarySummaryInfo[1][0]) && (isset($salarySummaryInfo[1][0]->dt_salary_month)) ) ? convertDateFormat($salarySummaryInfo[1][0]->dt_salary_month,  'M-Y') : null );
		$lastMonthOnHoldSalaryAmount = ( ( isset($salarySummaryInfo[2][0]) && (isset($salarySummaryInfo[2][0]->last_month_hold_amount)) ) ? $salarySummaryInfo[2][0]->last_month_hold_amount : 0 );
		
		$teamWiseHighestSalaryAmount = ( ( isset($salarySummaryInfo[3][0]) && (isset($salarySummaryInfo[3][0]->salary_amount)) ) ? $salarySummaryInfo[3][0]->salary_amount : 0 );
		$teamWiseHighestSalaryName = ( ( isset($salarySummaryInfo[3][0]) && (isset($salarySummaryInfo[3][0]->v_value)) ) ? $salarySummaryInfo[3][0]->v_value : 0 );
		
		$lastMonthOnHoldSalaryRecords = ( isset($salarySummaryInfo[4][0]) ? $salarySummaryInfo[4][0] : [] );
		$teamWiseHighestSalaryRecords = ( isset($salarySummaryInfo[5][0]) ? $salarySummaryInfo[5][0] : [] );
		
		$teamWiseSalaryRecords = ( isset($salarySummaryInfo[4][0]) ? $salarySummaryInfo[4] : [] );
		$monthWiseSalaryRecords = ( isset($salarySummaryInfo[5][0]) ? $salarySummaryInfo[5] : [] );
		
		$data = [];
		$data['lastSixMonthNetPayAmount'] = (!empty($lastMonthNetSalaryAmount) ? array_sum(array_column(objectToArray($lastSixMonthRecords), 'salary_amount')) : 0 );
		$data['lastMonthSalaryMonthName'] = $lastMonthSalaryMonthName;
		$data['lastMonthNetSalaryAmount'] = $lastMonthNetSalaryAmount;
		$data['lastMonthOnHoldSalaryAmount'] = $lastMonthOnHoldSalaryAmount;
		$data['teamWiseHighestSalaryAmount'] = $teamWiseHighestSalaryAmount;
		$data['teamWiseHighestSalaryName'] = $teamWiseHighestSalaryName;
		
		$data['teamWiseSalaryDetails'] = $teamWiseSalaryRecords;
		$data['monthWiseSalaryDetails'] = $monthWiseSalaryRecords;
		
		return $data;
	}
	
	public function filterSalarySummary(Request $request)
	{
		$year = (!empty($request->input('search_year')) ? trim($request->input('search_year')) : null );
		$teamId = (!empty($request->input('search_team')) ? (int)Wild_tiger::decode($request->input('search_team')) : 0 );
		$data = [];
		$salaryInfo = $this->salarySummaryInfo($year , $teamId );
		
		$data = (!empty($data) ? array_merge($data , $salaryInfo) : $salaryInfo );
		//echo "<pre>";print_r($data);
		$html = view(config('constants.AJAX_VIEW_FOLDER') . 'salary/filter-salary-summary')->with($data)->render();
	
		echo $html;
		die;
	}
	
	public function autoApprovePendingLeave(Request $request){
		
		$salaryMonth =   (!empty($request->input('search_start_month'))? dbDate($request->input('search_start_month')) : date('Y-m-d'));
		
		$month = date('m', strtotime($salaryMonth));
		$year = date('Y' , strtotime($salaryMonth) );
		
		$startDate = attendanceStartDate( $month , $year);
		$endDate = attendanceEndDate( $month , $year);
		
		$employeeAppliedLeaveDetails = [];
		$employeeAppliedLeaveQuery = MyLeaveModel::where('t_is_deleted' , 0 )->whereIn( 'e_status' , [ config('constants.PENDING_STATUS')  ] );
		$employeeAppliedLeaveQuery->whereRaw("(  dt_leave_from_date >= '".$startDate."'  or dt_leave_to_date >= '".$startDate."'  )");
		$employeeAppliedLeaveQuery->whereRaw("(  dt_leave_from_date <= '".$endDate."'  or dt_leave_to_date <= '".$endDate."'  )");
		$employeeAppliedLeaveDetails =  $employeeAppliedLeaveQuery->get();
		
		
		
		if(count($employeeAppliedLeaveDetails) == 0 ){
			$checkSummaryStatus = $this->commonAttendanceSummary($endDate);
			//var_dump($checkSummaryStatus);die;
			if ( $checkSummaryStatus != true ){
				$this->ajaxResponse(101, trans('messages.system-error'));
			}
			$this->ajaxResponse(1, trans('messages.no-record-found-for-auto-approve-leave'));	
		}
		$result = false;
		DB::beginTransaction();
		$updateRecordCount = 0;
		$allEmployeeIds = [];
		try{
			if(!empty($employeeAppliedLeaveDetails)){
				foreach($employeeAppliedLeaveDetails as $employeeAppliedLeaveDetail){
					$updateLeaveData = [];
					$updateLeaveData['t_is_auto_approve'] = 1;
					$updateLeaveData['e_status'] = config('constants.APPROVED_STATUS');
					$updateLeaveData['v_approve_reject_remark'] = config('constants.AUTO_APPROVE_LEAVE_REMARK');
					$updateLeaveData['i_approved_by_id'] = null;
					//$updateLeaveData['i_approved_by_id'] = session()->get('user_id');
					$updateLeaveData['dt_approved_at'] = date("Y-m-d H:i:s");
					$allEmployeeIds[] = $employeeAppliedLeaveDetail->i_employee_id;
					$this->crudModel->updateTableData(config('constants.APPLY_LEAVE_MASTER_TABLE'), $updateLeaveData , [ 'i_id' => $employeeAppliedLeaveDetail->i_id , 'e_status' =>   config('constants.PENDING_STATUS') ] );
					++$updateRecordCount;
				}
			}
			$result = true;
		}catch(\Exception $e){
			//var_dump($e->getMessage());die;
			DB::rollback();
			$result = false;
		}
		//var_dump($result);die;
		if( $result != false ){
			if( $updateRecordCount > 0 ){
				
				DB::commit();
				$checkSummaryStatus = $this->commonAttendanceSummary($endDate);
				//var_dump($checkSummaryStatus);die;
				if ( $checkSummaryStatus != true ){
					$this->ajaxResponse(101, trans('messages.system-error'));
				}
				$this->ajaxResponse(1, trans('messages.auto-approve-leave-msg' , [ 'count' => $updateRecordCount ] ) ) ;	
			} else {
				DB::rollback();
				$checkSummaryStatus = $this->commonAttendanceSummary($endDate);
				//var_dump($checkSummaryStatus);die;
				if ( $checkSummaryStatus != true ){
					$this->ajaxResponse(101, trans('messages.system-error'));
				}
				$this->ajaxResponse(1, trans('messages.no-record-found-for-auto-approve-leave'));
			}
		} else {
			$checkSummaryStatus = $this->commonAttendanceSummary($endDate);
			//var_dump($checkSummaryStatus);die;
			if ( $checkSummaryStatus != true ){
				$this->ajaxResponse(101, trans('messages.system-error'));
			}
			$this->ajaxResponse(101, trans('messages.no-record-found-for-auto-approve-leave'));
		}
	}
	
	public function amendmentSalary(Request $request){
		
		$amentdmentSalaryRecordId = (!empty($request->input('amendment_salary_id')) ? (int)Wild_tiger::decode($request->input('amendment_salary_id')) : 0 );
		if( $amentdmentSalaryRecordId > 0 ){
			$checkSalaryWhere = [];
			$checkSalaryWhere['i_id'] = $amentdmentSalaryRecordId;
			$getSalaryDetails = Salary::with(['generatedSalaryInfo'])->where($checkSalaryWhere)->first();
			
			if(!empty($getSalaryDetails)){
				
				$employeeId = ( isset($getSalaryDetails->i_employee_id) ? $getSalaryDetails->i_employee_id : 0 ); 
				
				$totalEarningValue = 0;
				$totalDeductionValue = 0;
				
				$particularChange = [];
				$currentSalaryDetails = [];
				$previouSalaryDetails = [];
				$result = false;
				DB::beginTransaction();
				
				try{
					foreach( $getSalaryDetails->generatedSalaryInfo as $generatedSalaryInfo ){
						$updatedValue = 0;
						$currentData = [];
						$previousData = [];
						$previousData['i_component_id'] = $generatedSalaryInfo->i_component_id;
						$previousData['d_actual_amount'] = $generatedSalaryInfo->d_actual_amount;
						$previousData['d_paid_amount'] = $generatedSalaryInfo->d_paid_amount;
							
						$currentData['i_component_id'] = $generatedSalaryInfo->i_component_id;
						$currentData['d_actual_amount'] = $generatedSalaryInfo->d_actual_amount;
							
						if(strlen($request->input('salary_info_' . $generatedSalaryInfo->i_id))){
							if( isset($generatedSalaryInfo->generateSalaryComponent->e_salary_components_type) ){
								switch( $generatedSalaryInfo->generateSalaryComponent->e_salary_components_type ){
									case config('constants.SALARY_COMPONENT_TYPE_EARNING'):
										$totalEarningValue += $request->input('salary_info_' . $generatedSalaryInfo->i_id);
										$updatedValue = $request->input('salary_info_' . $generatedSalaryInfo->i_id);
										break;
									case config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'):
										$totalDeductionValue += $request->input('salary_info_' . $generatedSalaryInfo->i_id);
										$updatedValue = $request->input('salary_info_' . $generatedSalaryInfo->i_id);
										break;
								}
							}
						}
						$currentData['d_paid_amount'] = $updatedValue;
						if( $currentData['d_paid_amount'] != $previousData['d_paid_amount'] ){
							$particularRow = [];
							$particularRow['i_component_id'] = $generatedSalaryInfo->i_component_id;
							$particularRow['d_before_paid_amount'] = $previousData['d_paid_amount'];
							$particularRow['d_after_paid_amount'] = $currentData['d_paid_amount'];
							$particularChange[] = $particularRow;
						}
						$currentSalaryDetails[] = $currentData;
						$previouSalaryDetails[] = $previousData;
						$this->crudModel->updateTableData(config('constants.SALARY_INFO_TABLE'), ['d_paid_amount' =>$updatedValue  ] , [ 'i_id' => $generatedSalaryInfo->i_id  ] );
					}
					$result = true;
				}catch(\Exception $e){
					DB::rollback();
					$result = false;
				}
				
				if( $result != false ){
					
					$masterSalaryData = [];
					$masterSalaryData['d_total_earning_amount'] = $totalEarningValue;
					$masterSalaryData['d_total_deduct_amount'] = $totalDeductionValue;
					$masterSalaryData['d_net_pay_amount'] = round( ($totalEarningValue - $totalDeductionValue) , 2 );
					$masterSalaryData['t_is_amendment'] = 1;
					
					$this->crudModel->updateTableData(config('constants.SALARY_MASTER_TABLE'), $masterSalaryData , [ 'i_id' => $amentdmentSalaryRecordId  ] );
					
					$amentdmentSalaryInfo  = [];
					$amentdmentSalaryInfo['i_salary_master_id'] = $amentdmentSalaryRecordId;
					$amentdmentSalaryInfo['v_previous_info'] = (!empty($previouSalaryDetails) ? json_encode($previouSalaryDetails) : [] );
					$amentdmentSalaryInfo['v_current_info'] = (!empty($currentSalaryDetails) ? json_encode($currentSalaryDetails) : [] );;
					$amentdmentSalaryInfo['v_change_info'] = (!empty($particularChange) ? json_encode($particularChange) : null );;
					
					//echo "<pre>";print_r($amentdmentSalaryInfo);die;
					
					$this->crudModel->insertTableData(config('constants.AMENDMENT_HISTOY_TABLE'), $amentdmentSalaryInfo  );
					//var_dump($employeeId);die;
					if( $employeeId > 0 ){
					
						$employeeInfo = EmployeeModel::where('i_id' , $employeeId)->first();
						
						if( isset($employeeInfo) && (!empty($employeeInfo)) ){
							$updateEmployeeSalaryInfo = [];
							$updateEmployeeSalaryInfo['e_hold_salary_status'] = config('constants.SELECTION_YES');
							$updateEmployeeSalaryInfo['dt_on_hold_release_date'] = null;
							//var_dump($employeeInfo->e_hold_salary_payment_status);die;
							if( (isset($employeeInfo->e_hold_salary_payment_status)) && !in_array(  $employeeInfo->e_hold_salary_payment_status  , [ config('constants.NOT_TO_PAY_STATUS') , config('constants.DONATED_STATUS') ] ) ){
								$updateEmployeeSalaryInfo['e_hold_salary_payment_status'] = config('constants.PENDING_STATUS');
								$onHoldSalaryDetails = EmployeeModel::with( [ 'onHoldSalaryInfo' , 'generatedSalaryMaster' , 'generatedSalaryMaster.generatedSalaryInfo'  ] )->where('i_id' , $employeeId)->first();
								if(!empty($onHoldSalaryDetails)){
									$getHoldAmountInfo = getHoldAmountInfo($onHoldSalaryDetails);
									$totalOnHoldSalaryAmount = ( isset($getHoldAmountInfo['totalOnHoldSalaryAmount']) ? $getHoldAmountInfo['totalOnHoldSalaryAmount'] : 0 ) ;
									$decuctOnHoldSalaryAmount = ( isset($getHoldAmountInfo['deductOnHoldSalaryAmount']) ? $getHoldAmountInfo['deductOnHoldSalaryAmount'] : 0 ) ;
									$leftAmount = ( isset($getHoldAmountInfo['leftOnHoldSalaryAmount']) ? $getHoldAmountInfo['leftOnHoldSalaryAmount'] : 0 ) ;
									if( $leftAmount == 0 ){
										$updateEmployeeSalaryInfo['e_hold_salary_payment_status'] = config('constants.PAID_STATUS');
										$updateEmployeeSalaryInfo['dt_on_hold_release_date'] = ( isset($getSalaryDetails->dt_salary_month) ? $getSalaryDetails->dt_salary_month : null );;
									}
								}
							
							}
							$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , $updateEmployeeSalaryInfo , [ 'i_id' => $employeeId ] );
						}
					}
					
					DB::commit();
					
					$this->ajaxResponse(1, trans('messages.salary-amendment-success'));
					
				} else {
					$this->ajaxResponse(1, trans('messages.error-amendment-success'));
				}
				
			}
			
			
		}
		$this->ajaxResponse(1, trans('messages.system-error'));
	}
	
	function updateSalaryPaidDay(Request $request){
		
		$salaryRecordId = (!empty($request->input('salary_record_id')) ? (int)Wild_tiger::decode($request->input('salary_record_id')) : 0 );
		$paidDay = (!empty($request->input('update_salary_paid_count')) ? trim($request->input('update_salary_paid_count')) : 0 );
		
		if( $salaryRecordId > 0 ){
			$result = $this->crudModel->updateTableData(config('constants.ATTENDANCE_SUMMARY_TABLE'), [ 'd_present_count' => $paidDay ] , [ 'i_id' => $salaryRecordId  ]  );
			$successMessage =  trans('messages.success-update',['module'=> trans('messages.paid-days')]);
			$errorMessages = trans('messages.error-update',['module'=> trans('messages.paid-days') ]);
			if( $result != false ){
				$this->ajaxResponse(1, $successMessage );
			} else {
				$this->ajaxResponse(1, $errorMessages );
			}
		}
		$this->ajaxResponse(1, trans('messages.system-error'));
		
	}
	
}
