<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\EmployeeModel;
use App\LookupMaster;
use App\ShiftMasterModel;
use App\ProbationPolicyMasterModel;
use App\WeeklyOffMasterModel;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\Response;
use App\CityMasterModel;
use App\EmployeeDocumentModel;
use App\DocumentFolderModel;
use App\DocumentTypeModel;
use App\SalaryComponentsModel;
use App\Models\EmployeeResignHistory;
use App\Models\Report;
use DB;
use App\MyAttendanceModel;
use App\HolidayMasterModel;
use App\Models\Salary;
use App\SalaryGroupDetailsModel;
use App\SalaryGroupModel;
use App\Models\RolePermission;


class ReportController extends MasterController
{
	
    public function __construct(){
    	parent::__construct();
    	$this->perPageRecord = config('constants.PER_PAGE');
    	$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
    	$this->tableName = config('constants.EMPLOYEE_MASTER_TABLE');
    	$this->folderName = config('constants.ADMIN_FOLDER'). 'report/' ;
    	$this->employeeCrudModel = new EmployeeModel();
    	$this->employeeDocumentModel = new EmployeeDocumentModel();
    	$this->crudModel =  new Report();
    }
    public function employeeReport(){
    	/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
    		return redirect('access-denied');
    	} */
    	$data = [];
    	$data['pageTitle'] = trans('messages.master-sheet');
    	$page = $this->defaultPage;
    	
    	$allPermissionId = config('permission_constants.ALL_EMPLOYEE_REPORT');
    	$data['allPermissionId'] = $allPermissionId;
    	
    	#store pagination data array
    	$whereData = $paginationData = [];
    	
    	$customListing = false; 
    	
    	$selectedEmployeeStatus = ( session()->has('filter_employee_status') ? session()->get('filter_employee_status') : null );  
    	
    	if(!empty($selectedEmployeeStatus)){
    		switch($selectedEmployeeStatus){
    			case config('constants.PROBATION_EMPLOYMENT_STATUS'):
    				$customListing = true;
    				$data['selectedEmployeeStatus'] = $selectedEmployeeStatus; 
    				$whereData['employment_status'] =  [ config('constants.PROBATION_EMPLOYMENT_STATUS') ]; 
    				break;
    			case config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS'):
    				$customListing = true;
    				$data['selectedEmployeeStatus'] = $selectedEmployeeStatus ;
    				$whereData['employment_status'] =  [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') ];
    				break;
    		}
    		
    	}
    	
    	$selectedEmployeeCityId = ( session()->has('filter_employee_city_id') ? session()->get('filter_employee_city_id') : null );
    	
    	if( $selectedEmployeeCityId > 0 ){
    		$customListing = true;
    		$whereData['current_address_city_id'] = $selectedEmployeeCityId;
    	}
    	
    	$selectedEmployeeTeamId = ( session()->has('filter_employee_team_id') ? session()->get('filter_employee_team_id') : null );
    	
    	if( $selectedEmployeeTeamId > 0 ){
    		$customListing = true;
    		$whereData['team_record'] = $selectedEmployeeTeamId;
    	}
    	
    	if( $customListing != true ){
    		$selectEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
    		$data['selectedEmployeeStatus'] = $selectEmployeeStatus;
    		$whereData['employment_status'] = $selectEmployeeStatus;
    	}
    	
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	## bydefault relived vala record nai aave
    	$whereData['employment_relieved_status'] = [config('constants.RELIEVED_PROBATION_EMPLOYMENT_STATUS')];
    	
    	#get pagination data for first page
    	if($page == $this->defaultPage ){
    	
    		$totalRecords = count($this->employeeCrudModel->getRecordDetails($whereData));
    	
    		$lastPage = ceil($totalRecords/$this->perPageRecord);
    	
    		$paginationData['current_page'] = $this->defaultPage;
    	
    		$paginationData['per_page'] = $this->perPageRecord;
    	
    		$paginationData ['last_page'] = $lastPage;
    	
    	}
    	$whereData ['limit'] = $this->perPageRecord;
    	
    	$data['recordDetails'] = $this->employeeCrudModel->getRecordDetails( $whereData );
    	
    	$data['pagination'] = $paginationData;
    		
    	$data['page_no'] = $page;
    		
    	$data['perPageRecord'] = $this->perPageRecord;
    	
    	$data['totalRecordCount'] = $totalRecords;
    	
    	$data['genderDetails'] = genderMaster(); 
    	$data['bloodGroupDetails'] = bloodGroupMaster();
    	$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
    	$data['designationDetails'] = LookupMaster::where('v_module_name' , config('constants.DESIGNATION_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value' , 'asc')->get();
    	$data['recruitmentSourceDetails'] = LookupMaster::where('v_module_name',config ( 'constants.RECRUITMENT_SOURCE_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
    	$data['referenceEmployeeDetails'] = EmployeeModel::where('t_is_deleted',0)->orderBy('v_employee_full_name', 'ASC')->get();
    	$data['shiftDetails'] = ShiftMasterModel::where('t_is_deleted',0)->orderBy('v_shift_name', 'ASC')->get();
    	$data['probationPolicyDetails'] = ProbationPolicyMasterModel::where('e_record_status',config ( 'constants.PROBATION_POLICY'))->where('t_is_deleted',0)->orderBy('v_probation_policy_name', 'ASC')->get();
    	$data['noticePeriodPolicyDetails'] = ProbationPolicyMasterModel::where('e_record_status',config ( 'constants.NOTICE_PERIOD_POLICY'))->where('t_is_deleted',0)->orderBy('v_probation_policy_name', 'ASC')->get();
    	$data['weeeklyOffDetails'] = WeeklyOffMasterModel::orderBy('v_weekly_off_name', 'ASC')->where('t_is_deleted',0)->get();
    	$data['leaderDetails'] = EmployeeModel::orderBy('v_employee_full_name', 'ASC')->where('t_is_deleted',0)->get();
    	$data['employeeStatusDetails'] = employmentStatusMaster();
    	$data['bankDetails'] = LookupMaster::where('v_module_name' , config('constants.BANK_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value' , 'asc')->get();
    	$data['cityDetails'] = CityMasterModel::where('t_is_deleted',0)->orderBy('v_city_name', 'ASC')->get();
    	$data['salaryGroupDetails'] = SalaryGroupModel::where('t_is_deleted',0)->orderBy('v_group_name', 'ASC')->get();
    	
    	$data['teamRecordId'] = $selectedEmployeeTeamId;
    	$data['currentAddressCityId'] = $selectedEmployeeCityId;
    	
    	$where = [];
    	$where['employment_status'] = $selectedEmployeeStatus;
    	
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$where['show_all'] = true;
    	}
    	
    	$data['employeeDetails'] = $this->getEmployeeDropdownDetails($where);
    	
    	$data['terminationReasonDetails'] = LookupMaster::where('v_module_name',config('constants.TERMINATION_REASONS_LOOKUP'))->orderBy('v_value', 'ASC')->get();
    	$data['resignationReasonDetails'] = LookupMaster::where('v_module_name',config('constants.RESIGN_REASONS_LOOKUP'))->orderBy('v_value', 'ASC')->get();
    	$data['roleDetails'] = RolePermission::where('t_is_deleted',0)->orderBy('v_role_name', 'ASC')->get();
    	$data['maritalStatusDetails'] = maritalStatusInfo();
    	return view( $this->folderName . 'employee-report')->with($data);
    }
    public function employeeReportfilter(Request $request){
    	//variable defined
    	$whereData = $likeData = [];
    	
    	$page = (! empty($request->post('page')) ? $request->post('page') : 1);
    	
    	if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_REPORT'), session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	//search record
    	if (!empty($request->post('search_by'))) {
    		$likeData ['searchBy'] = trim($request->post('search_by'));
    	}
    	if(!empty($request->post('search_gender'))){
    		$whereData['gender'] = trim($request->post('search_gender'));
    	}
    	
    	if(!empty($request->post('search_marital_status'))){
    		$whereData['marital_status'] = trim($request->post('search_marital_status'));
    	}
    	
    	if(!empty($request->post('search_blood_group'))){
    		$whereData['blood_group'] = trim($request->post('search_blood_group'));
    	}
    	if(!empty($request->post('search_joining_from_date'))){
    		$whereData['joining_from_date'] = ($request->post('search_joining_from_date'));
    	}
    	if(!empty($request->post('search_joining_to_date'))){
    		$whereData['joining_to_date'] = ($request->post('search_joining_to_date'));
    	}
    	if(!empty($request->post('search_team_name'))){
    		$whereData['team_record'] = (int)Wild_tiger::decode($request->post('search_team_name'));
    	}
    	if(!empty($request->post('search_designation'))){
    		$whereData['designation'] = (int)Wild_tiger::decode($request->post('search_designation'));
    	}
    	if(!empty($request->post('search_leader_name_reporting_manager'))){
    		$whereData['leader_name'] = (int)Wild_tiger::decode($request->post('search_leader_name_reporting_manager'));
    	}
    	if(!empty($request->post('search_recruitment_source'))){
    		$whereData['recruitment_source'] = (int)Wild_tiger::decode($request->post('search_recruitment_source'));
    	}
    	if(!empty($request->post('search_reference_name'))){
    		$whereData['reference_name'] = (int)Wild_tiger::decode($request->post('search_reference_name'));
    	}
    	if(!empty($request->post('search_shift'))){
    		$whereData['shift_record'] = (int)Wild_tiger::decode($request->post('search_shift'));
    	}
    	
    	if( ( !empty($request->post('search_employment_status') ) )){
    		$whereData['employment_status'] =  $request->post('search_employment_status') ;
    	}else{
			## bydefault relived vala record nai aave
			//$whereData['employment_relieved_status'] = [config('constants.RELIEVED_PROBATION_EMPLOYMENT_STATUS')];
		}
		if(!empty($request->post('search_employee_name'))){
			$whereData['master_id'] = (int)Wild_tiger::decode($request->post('search_employee_name'));
		}
		
    	if(!empty($request->post('search_probation_period'))){
    		$whereData['probation_id'] = (int)Wild_tiger::decode($request->post('search_probation_period'));
    	}
    	if(!empty($request->post('search_notice_period'))){
    		$whereData['notice_period_id'] = (int)Wild_tiger::decode($request->post('search_notice_period'));
    	}
    	if(!empty($request->post('search_weekly_off'))){
    		$whereData['weekly_off_id'] = (int)Wild_tiger::decode($request->post('search_weekly_off'));
    	}
    	if(!empty($request->post('search_login_status'))){
    		$whereData['login_status'] = ( trim($request->input('search_login_status')) == config('constants.ENABLE_STATUS') ? 1 :  0 );
    	}
    	if(!empty($request->post('search_bank_name'))){
    		$whereData['bank_id'] = (int)Wild_tiger::decode($request->post('search_bank_name'));
    	}
    	if(!empty($request->post('search_current_city'))){
    		$whereData['current_address_city_id'] = (int)Wild_tiger::decode($request->post('search_current_city'));
    	}
    	if(!empty($request->post('search_perm_city'))){
    		$whereData['permanent_address_city_id'] = (int)Wild_tiger::decode($request->post('search_perm_city'));
    	}
    	
    	if(!empty($request->post('search_assign_role'))){
    		$whereData['employee_assign_role'] = (int)Wild_tiger::decode($request->post('search_assign_role'));
    	}
    	
    	
    	
    	if(!empty($request->post('search_salary_group'))){
    		$whereData['salary_group'] = (int)Wild_tiger::decode($request->post('search_salary_group'));
    	}
    	
    	if(!empty($request->post('search_deduction_of_pf_status'))){
    		$whereData['deduction_of_pf_status'] = trim($request->post('search_deduction_of_pf_status'));
    	}
    	 
    	$selectedEmployeeTeamId = (!empty($request->post('employee_team_id')) ? (int)Wild_tiger::decode($request->post('employee_team_id')) : 0);
    	$selectedEmployeeCityId = (!empty($request->post('employee_current_city_id')) ? (int)Wild_tiger::decode($request->post('employee_current_city_id')) : 0);
    	
    	if( $selectedEmployeeCityId > 0 ){
    		$whereData['current_address_city_id'] = $selectedEmployeeCityId;
    	}
    	
    	if( $selectedEmployeeTeamId > 0 ){
    		$whereData['team_record'] = $selectedEmployeeTeamId;
    	}
    	
    	if(!empty($request->post('search_date_type'))){
    		$whereData['date_type'] = ($request->post('search_date_type'));
    	}
    	
    	if(!empty($request->post('search_date_selection_from_date'))){
    		$whereData['date_selection_from_date'] = ($request->post('search_date_selection_from_date'));
    	}
    	
    	if(!empty($request->post('search_date_selection_to_date'))){
    		$whereData['date_selection_to_date'] = ($request->post('search_date_selection_to_date'));
    	}
    	
    	if(!empty($request->post('search_resignation_type'))){
    		$whereData['resignation_type'] = ($request->post('search_resignation_type'));
    	}
    	
    	if( ( !empty($request->post('search_resign_status') ) )){
    		if(!empty($request->post('search_resignation_type')) && ( $request->post('search_resignation_type') == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) ){
    			$whereData['resign_status'] =  (int)Wild_tiger::decode( $request->post('search_resign_status') ) ;
    		}
    	}
    	 
    	if( ( !empty($request->post('search_terminate_status') ) )){
    		if(!empty($request->post('search_resignation_type')) && ( $request->post('search_resignation_type') == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) ){
    			$whereData['terminate_status'] =   (int)Wild_tiger::decode( $request->post('search_terminate_status') ) ;
    		}
    	}
    	
    	
    	$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
    	
    	if ($exportAction == 'export') {
    		$finalExportData = [];
    		$getExportRecordDetails = $this->employeeCrudModel->getRecordDetails($whereData, $likeData);
    		if (!empty($getExportRecordDetails)) {
    			$excelIndex = 0;
    			foreach ($getExportRecordDetails as $getExportRecordDetail) {
    				
    				$rowExcelData = [];
    				$rowExcelData['sr_no'] = ++$excelIndex;
    				$rowExcelData['employee_code'] = ( isset($getExportRecordDetail->v_employee_code) ?  ($getExportRecordDetail->v_employee_code) :'' );
    				$rowExcelData['employee_name'] = ( isset($getExportRecordDetail->v_employee_name) ?  ($getExportRecordDetail->v_employee_name) :'' );
    				$rowExcelData['full_name'] = ( isset($getExportRecordDetail->v_employee_full_name) ?  ($getExportRecordDetail->v_employee_full_name) :'' );
    				$rowExcelData['gender'] = ( isset($getExportRecordDetail->e_gender) ?  ($getExportRecordDetail->e_gender) :'' );
    				$rowExcelData['blood_group'] = ( isset($getExportRecordDetail->v_blood_group) ?  ($getExportRecordDetail->v_blood_group) :'' );
    				$rowExcelData['joining_date'] = (!empty($getExportRecordDetail->dt_joining_date) ? convertDateFormat($getExportRecordDetail->dt_joining_date,'d.m.Y') :'');
    				$rowExcelData['designation'] = ( isset($getExportRecordDetail->designationInfo->v_value) ?  ($getExportRecordDetail->designationInfo->v_value) :'' );
    				$rowExcelData['team'] = ( isset($getExportRecordDetail->teamInfo->v_value) ?  ($getExportRecordDetail->teamInfo->v_value) :'' );
    				$rowExcelData['leader_name_/_reporting_manager'] = ( isset($getExportRecordDetail->leaderInfo->v_employee_full_name) ?  ($getExportRecordDetail->leaderInfo->v_employee_full_name) .( isset($getExportRecordDetail->leaderInfo->v_employee_code) ?  ' (' .$getExportRecordDetail->leaderInfo->v_employee_code .')' :'' ):'' );
    				$rowExcelData['employment_status'] = ( isset($getExportRecordDetail->e_employment_status) ?  ($getExportRecordDetail->e_employment_status) :'' );
    				$rowExcelData['recruitment_source'] = ( isset($getExportRecordDetail->recruitmentSourceInfo->v_value) ?  ($getExportRecordDetail->recruitmentSourceInfo->v_value) :'' );
    				$rowExcelData['reference_name'] = ( isset($getExportRecordDetail->employeeInfo->v_employee_full_name) ?  ($getExportRecordDetail->employeeInfo->v_employee_full_name) :'' );
    				$rowExcelData['aadhar_number'] = ( isset($getExportRecordDetail->v_aadhar_no) ?  ($getExportRecordDetail->v_aadhar_no) :'' );
    				$rowExcelData['pan'] = ( isset($getExportRecordDetail->v_pan_no) ?  ($getExportRecordDetail->v_pan_no) :'' );
    				$rowExcelData['education'] = ( isset($getExportRecordDetail->v_education) ?  ($getExportRecordDetail->v_education) :'' );
    				$rowExcelData['cgpa_/_percentage'] = ( isset($getExportRecordDetail->v_cgpa) ?  ($getExportRecordDetail->v_cgpa) :'' );
    				$rowExcelData['marital_status'] = ( isset($getExportRecordDetail->e_marital_status) ?  ($getExportRecordDetail->e_marital_status) :'' );
    				$rowExcelData['Shift'] = ( isset($getExportRecordDetail->shiftInfo->v_shift_name) ?  ($getExportRecordDetail->shiftInfo->v_shift_name) :'' );
    				$rowExcelData['weekly_off'] = ( isset($getExportRecordDetail->weekOffInfo->v_weekly_off_name) ?  ($getExportRecordDetail->weekOffInfo->v_weekly_off_name) :'' );
    				$rowExcelData['outlook_email_id'] = ( isset($getExportRecordDetail->v_outlook_email_id) ?  ($getExportRecordDetail->v_outlook_email_id) :'' );
    				$rowExcelData['contact_number'] = ( isset($getExportRecordDetail->v_contact_no) ?  ($getExportRecordDetail->v_contact_no) :'' );
    				$rowExcelData['personal_email_id'] = ( isset($getExportRecordDetail->v_personal_email_id) ?  ($getExportRecordDetail->v_personal_email_id) :'' );
    				$rowExcelData['date_of_birth'] = ( isset($getExportRecordDetail->dt_birth_date) ?  convertDateFormat($getExportRecordDetail->dt_birth_date,'d.m.Y') :'' );
    				$rowExcelData['current_address'] = (!empty($getExportRecordDetail->v_current_address_line_first) ? $getExportRecordDetail->v_current_address_line_first .(!empty($getExportRecordDetail->v_current_address_line_second) ? ', '.$getExportRecordDetail->v_current_address_line_second .(!empty($getExportRecordDetail->cityCurrentInfo->v_city_name) ? ', '.$getExportRecordDetail->cityCurrentInfo->v_city_name .(!empty($getExportRecordDetail->v_current_address_pincode) ? ', '.$getExportRecordDetail->v_current_address_pincode :'') :'') :''):'');
    				$rowExcelData['permanent_address'] = (!empty($getExportRecordDetail->v_permanent_address_line_first) ? $getExportRecordDetail->v_permanent_address_line_first .(!empty($getExportRecordDetail->v_permanent_address_line_second) ? ', '.$getExportRecordDetail->v_permanent_address_line_second .(!empty($getExportRecordDetail->cityPermanentInfo->v_city_name) ? ', '.$getExportRecordDetail->cityPermanentInfo->v_city_name .(!empty($getExportRecordDetail->v_permanent_address_pincode) ? ', '.$getExportRecordDetail->v_permanent_address_pincode :'') :''):''):'');
    				$rowExcelData['probation_period'] = ( isset($getExportRecordDetail->probationPeriodInfo->v_probation_policy_name) ?  ($getExportRecordDetail->probationPeriodInfo->v_probation_policy_name) . ( isset($getExportRecordDetail->probationPeriodInfo->v_probation_period_duration) ? ' - '. ($getExportRecordDetail->probationPeriodInfo->v_probation_period_duration) :'' ) . ( isset($getExportRecordDetail->probationPeriodInfo->e_months_weeks_days) ?  ' '. ($getExportRecordDetail->probationPeriodInfo->e_months_weeks_days) :'' ) :'' );
    				$rowExcelData['notice_period'] = ( isset($getExportRecordDetail->noticePeriodInfo->v_probation_policy_name) ?  ($getExportRecordDetail->noticePeriodInfo->v_probation_policy_name)  . ( isset($getExportRecordDetail->noticePeriodInfo->v_probation_period_duration) ? ' - '. ($getExportRecordDetail->noticePeriodInfo->v_probation_period_duration) :'' ) . ( isset($getExportRecordDetail->noticePeriodInfo->e_months_weeks_days) ?  ' '. ($getExportRecordDetail->noticePeriodInfo->e_months_weeks_days) :'' )  :'' );
    				$rowExcelData['bank_name'] = ( isset($getExportRecordDetail->bankInfo->v_value) ?  ($getExportRecordDetail->bankInfo->v_value) :'' );
    				$rowExcelData['account_number'] = ( isset($getExportRecordDetail->v_bank_account_no) ?  ($getExportRecordDetail->v_bank_account_no) :'' );
    				$rowExcelData['ifsc_code'] = ( isset($getExportRecordDetail->v_bank_account_ifsc_code) ?  ($getExportRecordDetail->v_bank_account_ifsc_code) :'' );
    				$rowExcelData['uan_number'] = ( isset($getExportRecordDetail->v_uan_no) ?  ($getExportRecordDetail->v_uan_no) :'' );
    				$rowExcelData['salary_group'] = (isset($getExportRecordDetail->salaryInfo->salaryGroup->v_group_name) ? $getExportRecordDetail->salaryInfo->salaryGroup->v_group_name :'');
    				$rowExcelData['deduction_of_pf'] = (isset($getExportRecordDetail->salaryInfo->e_pf_deduction) ? $getExportRecordDetail->salaryInfo->e_pf_deduction :'');
    				$rowExcelData['role'] = ( isset($getExportRecordDetail->employeeAssignRole->v_role_name) ?  ($getExportRecordDetail->employeeAssignRole->v_role_name) :'' );
    				$rowExcelData['last_working_date'] = ( ( ( isset($getExportRecordDetail->e_employment_status) ) && ( in_array( $getExportRecordDetail->e_employment_status , [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS') ] ) ) && ( isset( $getExportRecordDetail->dt_notice_period_start_date ) ) ) ? clientDate($getExportRecordDetail->dt_notice_period_start_date) : '' );
    				$rowExcelData['pf_exit_date'] = ( ( ( isset($getExportRecordDetail->e_employment_status) ) && ( in_array( $getExportRecordDetail->e_employment_status , [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS') ] ) ) && ( isset( $getExportRecordDetail->dt_pf_expiry_date ) ) ) ? clientDate($getExportRecordDetail->dt_pf_expiry_date) : '' );
    				$rowExcelData['monthly_salary'] = ( isset($getExportRecordDetail->salaryInfo->d_net_pay_monthly) ?  ($getExportRecordDetail->salaryInfo->d_net_pay_monthly) : '' );
    				$rowExcelData['exit_type'] = ( ( ( isset($getExportRecordDetail->e_employment_status) ) && ( in_array( $getExportRecordDetail->e_employment_status , [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS') ] ) ) && ( isset( $getExportRecordDetail->latestDisplayResignHistory->e_initiate_type ) ) ) ? ( (  $getExportRecordDetail->latestDisplayResignHistory->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) ? trans('messages.resignation')  : ( ( $getExportRecordDetail->latestDisplayResignHistory->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) ? trans('messages.termination')  : '' ) )  : '' );
    				$rowExcelData['reason_for_leaving'] = ( ( ( isset($getExportRecordDetail->e_employment_status) ) && ( in_array( $getExportRecordDetail->e_employment_status , [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS') ] ) ) && ( isset( $getExportRecordDetail->latestDisplayResignHistory->e_initiate_type ) ) ) ? ( (  $getExportRecordDetail->latestDisplayResignHistory->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) ?  (!empty($getExportRecordDetail->latestDisplayResignHistory->resignation->v_value) ? ($getExportRecordDetail->latestDisplayResignHistory->resignation->v_value) : '' ) : ( ( $getExportRecordDetail->latestDisplayResignHistory->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) ?  (!empty($getExportRecordDetail->latestDisplayResignHistory->termination->v_value) ? ( $getExportRecordDetail->latestDisplayResignHistory->termination->v_value ) : '' ) : '' ) )  : '' );
    				$rowExcelData['resignation_date	'] = ( ( ( isset($getExportRecordDetail->e_employment_status) ) && ( in_array( $getExportRecordDetail->e_employment_status , [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS') ] ) ) && ( isset( $getExportRecordDetail->latestDisplayResignHistory->e_initiate_type ) ) ) ? ( (  $getExportRecordDetail->latestDisplayResignHistory->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) ?  (!empty($getExportRecordDetail->latestDisplayResignHistory->dt_employee_notice_date) ? clientDate($getExportRecordDetail->latestDisplayResignHistory->dt_employee_notice_date) : '' ) : ( ( $getExportRecordDetail->latestDisplayResignHistory->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) ? (!empty($getExportRecordDetail->latestDisplayResignHistory->dt_termination_notice_date) ? clientDate( $getExportRecordDetail->latestDisplayResignHistory->dt_termination_notice_date ) : '' ) : '' ) )  : '' );
    				$rowExcelData['login_status'] = ((!empty($getExportRecordDetail->t_is_active)) && ($getExportRecordDetail->t_is_active == 1) ? config("constants.ENABLE_STATUS") :  config("constants.DISABLE_STATUS"));
    				
    				$finalExportData[] = $rowExcelData;
    					
    			}
    		}
    			
    		if (!empty($finalExportData)) {
    	
    			$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.master-sheet')]);
    	
    			$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.master-sheet')]);
    			$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
    		} else {
    	
    			$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
    		}
    	
    		return Response::json($response);
			die;
    	}
    	
    	$paginationData = [];
    	
    	if ($page == $this->defaultPage) {
    	
    		$totalRecords = count($this->employeeCrudModel->getRecordDetails( $whereData , $likeData ));
    	
    	
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
    	
    	$data['recordDetails'] = $this->employeeCrudModel->getRecordDetails( $whereData, $likeData );
    	
    	if(isset($totalRecords)){
    		$data ['totalRecordCount'] = $totalRecords;
    	}
    	$data['pagination'] = $paginationData;
    	
    	$data['page_no'] = $page;
    	
    	$data['perPageRecord'] = $this->perPageRecord;
    	
    	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/employee-report-list' )->with ( $data )->render();
    	
    	echo $html;die;
    }
    public function documentReport(){
    	$data = [];
    	$data['pageTitle'] = trans('messages.document-report');
    	$page = $this->defaultPage;
    	
    	$allPermissionId = config('permission_constants.ALL_DOCUMENT_REPORT');
    	$data['allPermissionId'] = $allPermissionId;
    	
    	#store pagination data array
    	$whereData = $paginationData = [];
    	$whereData['edit_record'] = true;
    	
    	## bydefault relived vala record nai aave
    	
    	$whereData['employment_relieved_status'] = [config('constants.RELIEVED_PROBATION_EMPLOYMENT_STATUS')];
    	
    	$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
    	$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
    	$whereData['employment_status'] = $selectedEmployeeStatus;
    	
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	#get pagination data for first page
    	if($page == $this->defaultPage ){
    	
    		$totalRecords = count($this->employeeDocumentModel->getRecordDetails($whereData));
    	
    		$lastPage = ceil($totalRecords/$this->perPageRecord);
    	
    		$paginationData['current_page'] = $this->defaultPage;
    	
    		$paginationData['per_page'] = $this->perPageRecord;
    	
    		$paginationData ['last_page'] = $lastPage;
    	
    	}
    	
    	$whereData ['limit'] = $this->perPageRecord;
    	
    	$data['recordDetails'] = $this->employeeDocumentModel->getRecordDetails( $whereData );
    	//echo '<pre>';print_r($whereData);die;
    	$data['pagination'] = $paginationData;
    		
    	$data['page_no'] = $page;
    		
    	$data['perPageRecord'] = $this->perPageRecord;
    	
    	$data['totalRecordCount'] = $totalRecords;
    	
    	$employeeWhere = [];
    	$employeeWhere['employment_status'] = $selectedEmployeeStatus;
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$employeeWhere['show_all'] = true;
    	}
    	$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
    	
    	$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->orderBy('v_value', 'ASC')->get();
    	$data['documentFolderDetails'] = DocumentFolderModel::orderBy('v_document_folder_name', 'ASC')->get();
    	$data['documentTypeDetails'] = DocumentTypeModel::orderBy('v_document_type', 'ASC')->get();
    	$data['designationDetails'] = LookupMaster::where('v_module_name',config('constants.DESIGNATION_LOOKUP'))->orderBy('v_value', 'ASC')->get();
    	$data['employmentStatusInfo'] = employmentStatusMaster();
    	
    	return view( $this->folderName . 'document-report')->with($data);
    }
    
    public function employeeReportStatusFilter($employeeLinkInfo = null ){
    	
    	if(!empty($employeeLinkInfo)){
    		$employeeStatus = trim(Wild_tiger::decode($employeeLinkInfo));
    		$employeeStatusInfo = json_decode($employeeStatus,true);
    		
    		if( isset($employeeStatusInfo) && ( !empty($employeeStatusInfo['status']) ) ){
    			session()->flash('filter_employee_status' ,  trim($employeeStatusInfo['status']) );
    		}
    		
    		if( isset($employeeStatusInfo) && ( (!empty($employeeStatusInfo['team_id'])) ) ){
    			session()->flash('filter_employee_team_id' ,  (int)($employeeStatusInfo['team_id']) );
    		}
    		
    		if( isset($employeeStatusInfo) && ( (!empty($employeeStatusInfo['city_id'])) ) ){
    			session()->flash('filter_employee_city_id' ,  (int)($employeeStatusInfo['city_id']) );
    		}
    	}
    	
    	
    	/* if(!empty($employeeStatus)){
    		session()->flash('filter_employee_status' ,  trim(Wild_tiger::decode($employeeStatus)) ); 
    	} */
    	
    	return redirect( config('constants.EMPLOYEE_REPORT_URL') );
    }
    
    public function employeeDurationReport(){
    	/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
    		return redirect('access-denied');
    	} */
    	$data = [];
    	$data['pageTitle'] = trans('messages.employee-duration-report');
    	$page = $this->defaultPage;
    	
    	$allPermissionId = config('permission_constants.ALL_EMPLOYEE_DURATION_REPORT');
    	$data['allPermissionId'] = $allPermissionId;    	
    	
    	#store pagination data array
    	$whereData = $paginationData = [];
    	
    	$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
    	$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
    	$whereData['employment_status'] = $selectedEmployeeStatus;
    	
    	## bydefault relived vala record nai aave
    	$whereData['employment_relieved_status'] = [config('constants.RELIEVED_PROBATION_EMPLOYMENT_STATUS')];
    	
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	#get pagination data for first page
    	if($page == $this->defaultPage ){
    		 
    		$totalRecords = count($this->employeeCrudModel->getRecordDetails($whereData));
    		 
    		$lastPage = ceil($totalRecords/$this->perPageRecord);
    		 
    		$paginationData['current_page'] = $this->defaultPage;
    		 
    		$paginationData['per_page'] = $this->perPageRecord;
    		 
    		$paginationData ['last_page'] = $lastPage;
    		 
    	}
    	$whereData ['limit'] = $this->perPageRecord;
    	 
    	$data['recordDetails'] = $this->employeeCrudModel->getRecordDetails( $whereData );
    	
    	$data['genderDetails'] = genderMaster();
    	$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->orderBy('v_value', 'ASC')->get();
    	//$data['employeeDetails'] = EmployeeModel::orderBy('v_employee_full_name', 'ASC')->get();
    	$data['shiftDetails'] = ShiftMasterModel::orderBy('v_shift_name', 'ASC')->get();
    	$data['employmentDateStatusInfo'] = employmentDateStatus();
    	$data['employmentStatusInfo'] = employmentStatusMaster();
    	
    	$data['pagination'] = $paginationData;
    	
    	$data['page_no'] = $page;
    	
    	$data['perPageRecord'] = $this->perPageRecord;
    	 
    	$data['totalRecordCount'] = $totalRecords;
    	
    	$where = [];
    	
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$where['show_all'] = true;
    	}
    	$where['employment_status'] = $selectedEmployeeStatus;
    	$data['employeeDetails'] = $this->getEmployeeDropdownDetails($where);
    	 
    	return view( $this->folderName . 'employee-duration-report')->with($data);
    }
    public function employeeDurationFilter(Request $request){
    	//variable defined
    	$whereData = $likeData = [];
    	
    	if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_DURATION_REPORT'), session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	$page = (! empty($request->post('page')) ? $request->post('page') : 1);
    	$dateFilter = (!empty($request->post('search_date_filter')) ? $request->post('search_date_filter') : '');
    	
    	if(!empty($request->post('search_employee_name_code'))){
    		$whereData['master_id'] = (int)Wild_tiger::decode($request->post('search_employee_name_code'));
    	}
    	if(!empty($request->post('search_gender'))){
    		$whereData['gender'] = trim($request->post('search_gender'));
    	}
    	if(!empty($request->post('search_team'))){
    		$whereData['team_record'] = (int)Wild_tiger::decode($request->post('search_team'));
    	}
    	if(!empty($request->post('search_shift'))){
    		$whereData['shift_record'] = (int)Wild_tiger::decode($request->post('search_shift'));
    	}
    	$searchFromDate = (!empty($request->post('search_from_date')) ? $request->post('search_from_date'):'');
    	$searchToDate = (!empty($request->post('search_to_date')) ? $request->post('search_to_date'):'');
    	
    	if(!empty($dateFilter)){
    		switch ($dateFilter){
    			case config('constants.JOINING_DATE_STATUS'):
    				$whereData['joining_from_date'] = $searchFromDate;
    				$whereData['joining_to_date'] = $searchToDate;
    				break;
    			case config('constants.PROBATION_PERIOD_STATUS'):
    				$whereData['joining_from_date'] = $searchFromDate;
    				$whereData['probation_to_date'] = $searchToDate;
    				break;
    			case config('constants.NOTICE_PERIOD_STATUS'):
    				$whereData['notice_period_start_date'] = $searchFromDate;
    				$whereData['notice_period_end_date'] = $searchToDate;
    				break;
    			case config('constants.LAST_WORKING_DATE_STATUS'):
    				$whereData['start_working_date'] = $searchFromDate;
    				$whereData['end_working_date'] = $searchToDate;
    				break;
    		}
    	}
    	
    	
    	## employment status filter
    	if(!empty($request->post('search_employment_status'))){
    			$whereData['employment_status'] =  $request->post('search_employment_status');
    	}
    	//echo '<pre>';print_r($whereData['employment_status']);die;
    	$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
    	 
    	if ($exportAction == 'export') {
    		$finalExportData = [];
    		$getExportRecordDetails = $this->employeeCrudModel->getRecordDetails($whereData, $likeData);
    		if (!empty($getExportRecordDetails)) {
    			$excelIndex = 0;
    			foreach ($getExportRecordDetails as $getExportRecordDetail) {
    	
    				$rowExcelData = [];
    				$rowExcelData['sr_no'] = ++$excelIndex;
    				$rowExcelData['employee_name'] = ( isset($getExportRecordDetail->v_employee_full_name) ?  ($getExportRecordDetail->v_employee_full_name) :'' );
    				$rowExcelData['employee_code'] = ( isset($getExportRecordDetail->v_employee_code) ?  ($getExportRecordDetail->v_employee_code) :'' );
    				$rowExcelData['team'] = ( isset($getExportRecordDetail->teamInfo->v_value) ?  ($getExportRecordDetail->teamInfo->v_value) :'' );
    				$rowExcelData['gender'] = ( isset($getExportRecordDetail->e_gender) ?  ($getExportRecordDetail->e_gender) :'' );
    				$rowExcelData['date_of_birth'] = ( isset($getExportRecordDetail->dt_birth_date) ?  convertDateFormat($getExportRecordDetail->dt_birth_date,'d.m.Y') :'' );
    				$rowExcelData['joining_date'] = ( isset($getExportRecordDetail->dt_joining_date) ?  convertDateFormat($getExportRecordDetail->dt_joining_date,'d.m.Y') :'' );
    				$rowExcelData['Shift'] = ( isset($getExportRecordDetail->shiftInfo->v_shift_name) ?  ($getExportRecordDetail->shiftInfo->v_shift_name) :'' );
    				$rowExcelData['probation_start_date'] = ( isset($getExportRecordDetail->dt_joining_date) && (!empty($getExportRecordDetail->dt_probation_end_date))  ?  convertDateFormat($getExportRecordDetail->dt_joining_date,'d.m.Y') :'' );
    				$rowExcelData['probation_end_date'] = ( isset($getExportRecordDetail->dt_probation_end_date) && (!empty($getExportRecordDetail->dt_probation_end_date))  ?  convertDateFormat($getExportRecordDetail->dt_probation_end_date,'d.m.Y') :'' );
    				$rowExcelData['notice_period_start_date'] = ( isset($getExportRecordDetail->dt_notice_period_start_date) ?  convertDateFormat($getExportRecordDetail->dt_notice_period_start_date,'d.m.Y') :'' );
    				$rowExcelData['notice_period_end_date'] = ( isset($getExportRecordDetail->dt_notice_period_end_date) ?  convertDateFormat($getExportRecordDetail->dt_notice_period_end_date,'d.m.Y') :'' );
    				$rowExcelData['last_working_date'] = ( isset($getExportRecordDetail->dt_notice_period_end_date) ?  convertDateFormat($getExportRecordDetail->dt_notice_period_end_date,'d.m.Y') :'' );
    				
    				$finalExportData[] = $rowExcelData;
    					
    			}
    		}
    		 
    		if (!empty($finalExportData)) {
    			 
    			$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.employee-duration-report')]);
    			 
    			$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.employee-duration-report')]);
    			$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
    		} else {
    			 
    			$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
    		}
    		 
    		return Response::json($response);
    		die;
    	}
    	$paginationData = [];
    	 
    	if ($page == $this->defaultPage) {
    		 
    		$totalRecords = count($this->employeeCrudModel->getRecordDetails( $whereData , $likeData ));
    		 
    		 
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
    	 
    	$data['recordDetails'] = $this->employeeCrudModel->getRecordDetails( $whereData, $likeData );
    	 
    	if(isset($totalRecords)){
    		$data ['totalRecordCount'] = $totalRecords;
    	}
    	$data['pagination'] = $paginationData;
    	 
    	$data['page_no'] = $page;
    	 
    	$data['perPageRecord'] = $this->perPageRecord;
    	 
    	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/employee-duration-report-list' )->with ( $data )->render();
    	 
    	echo $html;die;
    }
    public function documentReportFilter(Request $request){
    	//variable defined
    	$whereData = $likeData = [];
    	 
    	$page = (! empty($request->post('page')) ? $request->post('page') : 1);
    	
    	if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_DOCUMENT_REPORT'), session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	if(!empty($request->post('search_employee_name'))){
    		$whereData['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee_name'));
    	}
    	if(!empty($request->post('search_joining_from_date'))){
    		$whereData['joining_from_date'] = trim($request->post('search_joining_from_date'));
    	}
    	if(!empty($request->post('search_joining_to_date'))){
    		$whereData['joining_to_date'] = trim($request->post('search_joining_to_date'));
    	}
    	
    	if(!empty($request->post('search_team'))){
    		$whereData['team_record_id'] = (int)Wild_tiger::decode($request->post('search_team'));
    	}
    	if(!empty($request->post('search_designation'))){
    		$whereData['designation_id'] = (int)Wild_tiger::decode($request->post('search_designation'));
    	}
    	if(!empty($request->post('search_document_folder'))){
    		$whereData['document_folder_id'] = (int)Wild_tiger::decode($request->post('search_document_folder'));
    	}
    	if(!empty($request->post('search_document_name'))){
    		$whereData['master_id'] = (int)Wild_tiger::decode($request->post('search_document_name'));
    	}
    	if(!empty($request->post('search_employment_status'))){
    		$whereData['employment_status'] =  $request->post('search_employment_status');
    	}
    	
    	$whereData['edit_record'] = true;
    	$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
    	
    	if ($exportAction == config('constants.EXCEL_EXPORT')) {
			$finalExportData = [];
			
			$getExportRecordDetails = $this->employeeDocumentModel->getRecordDetails($whereData, $likeData);
				
			if (!empty($getExportRecordDetails)) {
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail) {
					$fromTime[] = (!empty($getExportRecordDetail->t_from_time) ? $getExportRecordDetail->t_from_time :'');
					$toTime[] = (!empty($getExportRecordDetail->t_to_time) ? $getExportRecordDetail->t_to_time :'');
					$totalHours = array_diff($fromTime, $toTime);
					
					$rowExcelData = [];
					$rowExcelData['sr_no'] = ++$excelIndex;
					$rowExcelData['document_folder'] = ( isset($getExportRecordDetail->documentType->documentFolderMaster->v_document_folder_name) ? ($getExportRecordDetail->documentType->documentFolderMaster->v_document_folder_name):'' );
					$rowExcelData['employee_name'] = ( isset($getExportRecordDetail->employeeInfo->v_employee_full_name) ? ($getExportRecordDetail->employeeInfo->v_employee_full_name):'' );
					$rowExcelData['employee_code'] = ( isset($getExportRecordDetail->employeeInfo->v_employee_code) ? ($getExportRecordDetail->employeeInfo->v_employee_code):'' );
					$rowExcelData['employee_designation'] = ( isset($getExportRecordDetail->employeeInfo->designationInfo->v_value) ? ($getExportRecordDetail->employeeInfo->designationInfo->v_value):'' );
					$rowExcelData['employee_team'] = ( isset($getExportRecordDetail->employeeInfo->teamInfo->v_value) ? ($getExportRecordDetail->employeeInfo->teamInfo->v_value):'' );
					$rowExcelData['employee_joining_date'] = ( isset($getExportRecordDetail->employeeInfo->dt_joining_date) ? convertDateFormat($getExportRecordDetail->employeeInfo->dt_joining_date ,'d.m.Y'):'' );
					$rowExcelData['document_type'] = ( isset($getExportRecordDetail->documentType->v_document_type) ? ($getExportRecordDetail->documentType->v_document_type):'' );
					$finalExportData[] = $rowExcelData;
	
				}
			}
	
			if (!empty($finalExportData)) {
	
				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.document-report') ]);
	
				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.document-report')]);
				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
			} else {
	
				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
			}
	
			return Response::json($response);
			die;
		}
		$paginationData = [];
		
		if ($page == $this->defaultPage) {
	
			$totalRecords = count($this->employeeDocumentModel->getRecordDetails( $whereData , $likeData ));
				
	
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
		
		$data['recordDetails'] = $this->employeeDocumentModel->getRecordDetails( $whereData, $likeData );
		
		if(isset($totalRecords)){
			$data ['totalRecordCount'] = $totalRecords;
		}
		$data['pagination'] = $paginationData;
	
		$data['page_no'] = $page;
	
		$data['perPageRecord'] = $this->perPageRecord;
    	
    	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/document-report-list' )->with ( $data )->render();
    	
    	echo $html;die;
    }
    public function employeeLeaveReport(){
    	$data = [];
    	
    	$data['pageTitle'] = trans('messages.leave-report-month-wise-count');
    	
    	$allPermissionId = config('permission_constants.ALL_LEAVE_REPORT_MONTH_WISE_COUNT');
    	$data['allPermissionId'] = $allPermissionId;
    	
    	$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
    	$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
    	
    	$data['employmentStatusInfo'] = employmentStatusMaster();
    	
    	$data['recordDetails'] = [];
    	
    	$employeeWhere = [];
    	$employeeWhere['employment_status'] = $selectedEmployeeStatus;
    	
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$employeeWhere['show_all'] = true;
    	}
    	
    	$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
    	
    	$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
    	 
    	$data['yearDetails'] = yearDetails();
    	
    	$data['allMonths'] = [];
    	
    	$data['requestPageNo'] = 1;
    	
    	$data['totalRecordCount'] = 0;
    	
    	return view( $this->folderName . 'leave-report-month-wise-count')->with($data);
    }
    public function leaveReportMonthFilter(Request $request){
    	if(!empty($request->post())){
    		
    		$selectedYear = (!empty($request->post('search_year')) ? $request->post('search_year') : date('Y') );
    		
    		$allMonths = [];
    		for($i = 1 ; $i <= 12 ; $i++ ){
				if( $i <= 9 ){
					$i = "0".$i;
				}
				$allMonths[] = date('Y-m-01' , strtotime($selectedYear.$i.'01'));
			}
    		
			$page = (! empty($request->post('page')) ? $request->post('page') : 1);
			
			$limit = $this->perPageRecord;
			$offset = 0;
			$employeeWhere = [];
			
			$employeeWhere['t_is_deleted'] = 0;
			$employeeWhere['order_by'] = [  'v_employee_full_name' => 'asc' ];
			if(!empty($request->post('search_team'))){
				$employeeWhere['team_record'] = (int)Wild_tiger::decode($request->post('search_team'));
			}
			
			if(!empty($request->post('search_employee_name_code'))){
				$employeeWhere['master_id'] = (int)Wild_tiger::decode($request->post('search_employee_name_code'));
			}
			## employment status filter
			if(!empty($request->post('search_employment_status'))){
				$employeeWhere['employment_status'] =  $request->post('search_employment_status');					
			} 
			$paginationData = [];
			
			//echo "limit = ".$limit;echo "<br><br>";
			//echo "offset = ".$offset;echo "<br><br>";
			$this->employeeModel = new EmployeeModel();
			
			if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_LEAVE_REPORT_MONTH_WISE_COUNT'), session()->get('user_permission')  ) ) ){
				$employeeWhere['show_all'] = true;
			}
			
			$getAllEmployeeCountDetails = 0;
			if ($page == $this->defaultPage) {
			
				$getAllEmployeeCountDetails = $this->employeeModel->getRecordDetails( $employeeWhere );
					
				$getAllEmployeeCountDetails = count($getAllEmployeeCountDetails);
			
				$lastpage = ceil($getAllEmployeeCountDetails / $this->perPageRecord);
			
				$paginationData['current_page'] = $this->defaultPage;
					
				$paginationData['per_page'] = $this->perPageRecord;
					
				$paginationData['last_page'] = $lastpage; 
			}
			
			//$getAllEmployeeDetails = EmployeeModel::with(['myLeaveMaster' , 'myLeaveMaster.leaveSummaryInfo'])->where($employeeWhere)->take($limit)->skip($offset)->orderBy('v_employee_full_name', 'ASC')->get();
			
			$finalData = [];
			
			$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
				
			if ($exportAction == 'export') {
				$getAllEmployeeDetails = $this->employeeModel->getRecordDetails( $employeeWhere );
				if(!empty($getAllEmployeeDetails)){
					$exportIndex = 0;
					foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
						$rowData = [];
						$rowData['sr_no'] = ++$exportIndex;
						$rowData['employee_name'] = (!empty($getAllEmployeeDetail->v_employee_full_name) ? $getAllEmployeeDetail->v_employee_full_name : '');
						$rowData['employee_code'] = (!empty($getAllEmployeeDetail->v_employee_code) ? $getAllEmployeeDetail->v_employee_code : '');
						$rowData['team'] = ( isset($getAllEmployeeDetail->teamInfo->v_value) ? $getAllEmployeeDetail->teamInfo->v_value : '' );
						$rowData['joining_date'] = (!empty($getAllEmployeeDetail->dt_joining_date) ? convertDateFormat($getAllEmployeeDetail->dt_joining_date , 'd.m.Y') : '' ) ;
						$totalLeave = 0;
						$totalSaveLeave = 0;
						if(!empty($allMonths)){
							foreach($allMonths as $allMonth){
								$rowData[leaveMonthReportValue($allMonth)] = 0;
							}
							//echo "<pre>";print_r($rowData);
							if( isset($getAllEmployeeDetail->myLeaveMaster) && (!empty($getAllEmployeeDetail->myLeaveMaster)) ){
								foreach($getAllEmployeeDetail->myLeaveMaster as $leaveMaster){
									if( in_array( $leaveMaster->e_status , [ config('constants.APPROVED_STATUS') ] )  ){
										if( isset($leaveMaster->leaveSummaryInfo) && (!empty($leaveMaster->leaveSummaryInfo)) ){
											foreach($leaveMaster->leaveSummaryInfo as $leaveSummaryInfo ){
												if( isset($leaveSummaryInfo->dt_added_used_at) && (!empty($leaveSummaryInfo->dt_added_used_at)) ){
													$leaveMonth  = leaveDateMonth($leaveSummaryInfo->dt_added_used_at);
													$leaveMonth = leaveMonthReportValue($leaveMonth);
													//echo "leave month = ".$leaveMonth;echo "<br><br><br><br>";
													if(!empty($leaveMonth)){
														if( $leaveSummaryInfo->d_no_of_days > 0 ){
															//echo "employee name = ".	$rowData['v_employee_full_name'];echo "<br><br><br>";
														}
														if(isset($rowData[$leaveMonth])){
															
															//echo "leave month = ".$leaveMonth;echo "<br><br><br><br>";
															$rowData[$leaveMonth] =  $rowData[$leaveMonth] +(!empty($leaveSummaryInfo->d_no_of_days) ? $leaveSummaryInfo->d_no_of_days : 0 );
														}
				
														if(!empty($leaveSummaryInfo->d_no_of_days)){
															$totalLeave += $leaveSummaryInfo->d_no_of_days;
														}
													}
												}
											}
										}
									}
								}
							}
							$rowData['total'] = $totalLeave;
							
							if (isset($getAllEmployeeDetail->academicSavedLeaveInfo) && count($getAllEmployeeDetail->academicSavedLeaveInfo) > 0){
								foreach ($getAllEmployeeDetail->academicSavedLeaveInfo as $academicLeaveInfo){
									if (!empty($academicLeaveInfo->v_year) && $academicLeaveInfo->v_year == $selectedYear && !empty($academicLeaveInfo->d_save_leave)){
										$totalSaveLeave = $academicLeaveInfo->d_save_leave;
									}
								}
							}
							
							$rowData['saved_leave'] = $totalSaveLeave;
							
							$finalData[] = $rowData;
						}
					}
				}
				
				if (!empty($finalData)) {
						
					$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.leave-report-month-wise-count')]);
						
					$xlsData = $this->generateSpreadsheet(['record_detail' => $finalData, 'title' => trans('messages.leave-report-month-wise-count')]);
					$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
				} else {
						
					$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
				}
					
				return Response::json($response);
				die;
			}
			if ($page == $this->defaultPage) {
				$offset = 0;
				$limit  = $this->perPageRecord;
				$employeeWhere['limit'] = $limit;
				$employeeWhere['offset'] = $offset;
			} else if ($page > $this->defaultPage) {
				$offset = ($page - 1) * $this->perPageRecord;
				$limit = $this->perPageRecord;
				$employeeWhere['limit'] = $limit;
				$employeeWhere['offset'] = $offset;
			}
			$getAllEmployeeDetails = $this->employeeModel->getRecordDetails( $employeeWhere );
			
			if(!empty($getAllEmployeeDetails)){
				foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
					$rowData = [];
					$rowData['i_id'] = $getAllEmployeeDetail->i_id;
					$rowData['i_employee_id'] = $getAllEmployeeDetail->i_id;
					$rowData['v_employee_full_name'] = $getAllEmployeeDetail->v_employee_full_name;
					$rowData['v_employee_code'] = $getAllEmployeeDetail->v_employee_code;
					$rowData['v_designation_name'] = ( isset($getAllEmployeeDetail->teamInfo->v_value) ? $getAllEmployeeDetail->teamInfo->v_value : '' );
					$rowData['dt_joining_date'] = (!empty($getAllEmployeeDetail->dt_joining_date) ? convertDateFormat($getAllEmployeeDetail->dt_joining_date , 'd.m.Y') : '' ) ;
					$totalLeave = 0;
					$totalSaveLeave = 0;
					if(!empty($allMonths)){
						foreach($allMonths as $allMonth){
							$rowData[$allMonth] = 0;
						}
						
						if( isset($getAllEmployeeDetail->myLeaveMaster) && (!empty($getAllEmployeeDetail->myLeaveMaster)) ){
							foreach($getAllEmployeeDetail->myLeaveMaster as $leaveMaster){
								if( in_array( $leaveMaster->e_status , [ config('constants.APPROVED_STATUS') ] )  ){	
									if( isset($leaveMaster->leaveSummaryInfo) && (!empty($leaveMaster->leaveSummaryInfo)) ){
										foreach($leaveMaster->leaveSummaryInfo as $leaveSummaryInfo ){
											if( isset($leaveSummaryInfo->dt_added_used_at) && (!empty($leaveSummaryInfo->dt_added_used_at)) ){
												$leaveMonth  = leaveDateMonth($leaveSummaryInfo->dt_added_used_at);
												//echo "leave month = ".$leaveMonth;echo "<br><br><br><br>";
												if(!empty($leaveMonth)){
													if( $leaveSummaryInfo->d_no_of_days > 0 ){
														//echo "employee name = ".	$rowData['v_employee_full_name'];echo "<br><br><br>";
													}
													if(isset($rowData[$leaveMonth])){
														$rowData[$leaveMonth] += (!empty($leaveSummaryInfo->d_no_of_days) ? $leaveSummaryInfo->d_no_of_days : 0 );
													}
													
													if(!empty($leaveSummaryInfo->d_no_of_days)){
														$totalLeave += $leaveSummaryInfo->d_no_of_days;
													}
												}
											}
										}
									}
								}
							}	
						}
						$rowData['d_total_leave'] = $totalLeave;
						
						if (isset($getAllEmployeeDetail->academicSavedLeaveInfo) && count($getAllEmployeeDetail->academicSavedLeaveInfo) > 0){
							foreach ($getAllEmployeeDetail->academicSavedLeaveInfo as $academicLeaveInfo){
								if (!empty($academicLeaveInfo->v_year) && $academicLeaveInfo->v_year == $selectedYear && !empty($academicLeaveInfo->d_save_leave)){
									$totalSaveLeave = $academicLeaveInfo->d_save_leave;
								}
							}
						}
						$rowData['d_save_leave'] = $totalSaveLeave;
						$finalData[] = $rowData;
					}
				}
			}
			
			$data = [];
			$data['recordDetails'] = $finalData;
			$data['allMonths'] = $allMonths;
			$data['pagination'] = $paginationData;
			if ($page == $this->defaultPage) {
				$data['totalRecordCount'] = $getAllEmployeeCountDetails;
			}
			$data['requestPageNo'] = $page;
			$data['page_no'] = $page;
			$data['perPageRecord'] = $this->perPageRecord;
			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/leave-report-month-wise-count-list' )->with ( $data )->render();
			echo $html;die;
    	}
    }
    
    public function form16Report(){
    	$financialYear = getCurrentFinancialYear();
    	$finanaceYearDetails = ( isset($financialYear) ? explode("-" , $financialYear ) : [] );
    	$startYear = ( $finanaceYearDetails[0] ? $finanaceYearDetails[0] : [] );
    	$endYear = ( $finanaceYearDetails[1] ? $finanaceYearDetails[1] : [] );
    	 
    	$data = [];
    	
    	$allPermissionId = config('permission_constants.ALL_FORM_16_REPORT');
    	$data['allPermissionId'] = $allPermissionId;
    	
    	$startMonth =  date('M-Y' , strtotime("first day of april this year" , strtotime(date($startYear . '-01-01')) ));
    	$endMonth =  date('M-Y' , strtotime("last day of march this year" , strtotime(date($endYear . '-01-01')) ));
    	
    	$data['pageTitle'] = trans('messages.form-16-report');
    	$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
    	
    	$employeeWhere = [];
    	$employeeWhere['employment_status'] = $selectedEmployeeStatus;
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$employeeWhere['show_all'] = true;
    	}
    	$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
    	
    	$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
    	
    	$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
    
    	$data['salaryComponentDetails'] = SalaryComponentsModel::where('t_is_deleted' , 0 )->get();
    	
    	$data['earningComponentDetails'] = SalaryComponentsModel::where('t_is_deleted' , 0 )->where('e_salary_components_type' ,  config('constants.SALARY_COMPONENT_TYPE_EARNING'))->orderBy('i_sequence' , 'asc')->get();;
    	
    	$data['deductComponentDetails'] = SalaryComponentsModel::where('t_is_deleted' , 0 )->where('e_salary_components_type' ,  config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'))->orderBy('i_sequence' , 'asc')->get();;
    	
    	$data['designationDetails'] = LookupMaster::where('v_module_name' , config('constants.DESIGNATION_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value' , 'asc')->get();
    	
    	$data['recordDetails'] = [];
    	
    	$data['requestPageNo'] = 1;
    	 
    	$data['totalRecordCount'] = 0;
    	
    	$data['startMonth'] = $startMonth;
    	 
    	$data['endMonth'] = $endMonth;
    	
    	return view( $this->folderName . 'form-16-report')->with($data);
    }
    public function form16ReportFilter(Request $request){
    	
    	if(!empty($request->post())){
    
    		$selectedYear = (!empty($request->post('search_year')) ? $request->post('search_year') : date('Y') );
    
    		$searchStartDate = (!empty($request->post('search_start_date')) ? dbDate( $request->post('search_start_date') ) : date('Y-m-d') );
    		$searchEndDate = (!empty($request->post('search_end_date')) ? dbDate( $request->post('search_end_date') ) : date('Y-m-d') );
    		
    		$allMonths = monthListBetweenDates($searchStartDate, $searchEndDate);
    		
    		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
    			
    		$limit = $this->perPageRecord;
    		$offset = 0;
    		$employeeWhere = [];
    		$employeeWhere['order_by'] = [  'v_employee_full_name' => 'asc' ];
    		$employeeWhere['t_is_deleted'] = 0;
    		
    		if( ( !empty($request->post('search_employment_status') ) )){
    			$employeeWhere['employment_status'] =  $request->post('search_employment_status') ;
    		}
    			
    		if(!empty($request->post('search_employee_name_code'))){
    			$employeeWhere['master_id'] = (int)Wild_tiger::decode($request->post('search_employee_name_code'));
    		}
    			
    		if(!empty($request->post('search_team'))){
    			$employeeWhere['team_record'] = (int)Wild_tiger::decode($request->post('search_team'));
    		}
    		
    		if(!empty($request->post('search_designation'))){
    			$employeeWhere['designation'] = (int)Wild_tiger::decode($request->post('search_designation'));
    		}
    		
    		/* if(!empty($request->post('search_start_date'))){
    			$employeeWhere['salary_start_month'] = ($request->post('search_start_date'));
    		}
    		if(!empty($request->post('search_end_date'))){
    			$employeeWhere['salary_end_month'] = ($request->post('search_end_date'));
    		} */
    		
    		$paginationData = [];
			
			$getAllEmployeeCountDetails = [];
    			
    		$this->employeeModel = new EmployeeModel();
    		
    		$earningComponentDetails = SalaryComponentsModel::where('t_is_deleted' , 0 )->where('e_salary_components_type' ,  config('constants.SALARY_COMPONENT_TYPE_EARNING'))->orderBy('i_sequence' , 'asc')->get();;
    		 
    		$deductComponentDetails = SalaryComponentsModel::where('t_is_deleted' , 0 )->where('e_salary_components_type' ,  config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'))->orderBy('i_sequence' , 'asc')->get();;
    		
    		$duration = "";
    		$duration = ( isset($allMonths[0]['month']) ? date('M Y', strtotime($allMonths[0]['month'])) : '' ) . ' - ' . ( !empty(end($allMonths)['month']) ? date('M Y', strtotime(end($allMonths)['month']))  : '' );
    		
    		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
    		
    		$financialYear = getCurrentFinancialYear();
    		$finanaceYearDetails = ( isset($financialYear) ? explode("-" , $financialYear ) : [] );
    		$startYear = ( $finanaceYearDetails[0] ? $finanaceYearDetails[0] : [] );
    		$endYear = ( $finanaceYearDetails[1] ? $finanaceYearDetails[1] : [] );
    		$salaryStartMonth =  date('Y-m-d' , strtotime("first day of april this year" , strtotime(date($startYear . '-01-01')) ));
    		$salaryEndMonth =  date('Y-m-d' , strtotime("last day of march this year" , strtotime(date($endYear . '-01-01')) ));
    		
    		if(!empty($request->post('search_start_date'))){
    			$salaryStartMonth = dbDate($request->post('search_start_date'));
    		}
    		if(!empty($request->post('search_end_date'))){
    			$salaryEndMonth = dbDate($request->post('search_end_date'));
    		}
    		
    		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_FORM_16_REPORT'), session()->get('user_permission')  ) ) ){
    			$employeeWhere['show_all'] = true;
    		}
    		
    		if ($exportAction == 'export') {
    			$finalData = [];
    			$getAllEmployeeDetails = $this->employeeModel->getRecordDetails( $employeeWhere );
    			if(!empty($getAllEmployeeDetails)){
    				$exportIndex = 0;
    				foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
    					$rowExcelData = [];
    					$rowExcelData['sr_no'] = ++$exportIndex;
    					$rowExcelData['employee_name'] = $getAllEmployeeDetail->v_employee_full_name;
    					$rowExcelData['employee_code'] = $getAllEmployeeDetail->v_employee_code;
    					$rowExcelData['designation'] = ( isset($getAllEmployeeDetail->designationInfo->v_value) ? $getAllEmployeeDetail->designationInfo->v_value : '' );
    					$rowExcelData['team'] = ( isset($getAllEmployeeDetail->teamInfo->v_value) ? $getAllEmployeeDetail->teamInfo->v_value : '' );
    					$rowExcelData['joining_date'] = (!empty($getAllEmployeeDetail->dt_joining_date) ? convertDateFormat($getAllEmployeeDetail->dt_joining_date , 'd.m.Y') : '' ) ;
    					$rowExcelData['pan'] = (!empty($getAllEmployeeDetail->v_pan_no) ? $getAllEmployeeDetail->v_pan_no : '');
    					$rowExcelData['duration'] = $duration;
    					
    					$getHeadWiseAmountDetails = form16ReportInfo($earningComponentDetails, $deductComponentDetails, $getAllEmployeeDetail , $salaryStartMonth , $salaryEndMonth );
    					$exportData = ( isset($getHeadWiseAmountDetails['export']) ? $getHeadWiseAmountDetails['export'] : [] );
    					$rowExcelData = (!empty($rowExcelData) ? array_merge($rowExcelData,$exportData) : $exportData );
    					
    					
    					$finalData[] = $rowExcelData;
    					
    				}
    			}
    			
    			if (!empty($finalData)) {
    		
    				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.form-16-report')]);
    		
    				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalData, 'title' => trans('messages.form-16-report')]);
    				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
    			
    			} else {
    		
    				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
    			}
    				
    			return Response::json($response);
    			die;
    		}
    		
    		
    		
    		if ($page == $this->defaultPage) {
    				
    			$getAllEmployeeCountDetails = $this->employeeModel->getRecordDetails( $employeeWhere );
    				
    			$getAllEmployeeCountDetails = count($getAllEmployeeCountDetails);
    				
    			$lastpage = ceil($getAllEmployeeCountDetails / $this->perPageRecord);
    				
    			$paginationData['current_page'] = $this->defaultPage;
    				
    			$paginationData['per_page'] = $this->perPageRecord;
    				
    			$paginationData['last_page'] = $lastpage;
    		}
    			
    			
    		if ($page == $this->defaultPage) {
    			$offset = 0;
    			$limit  = $this->perPageRecord;
    			$employeeWhere['limit'] = $limit;
    			$employeeWhere['offset'] = $offset;
    		} else if ($page > $this->defaultPage) {
    			$offset = ($page - 1) * $this->perPageRecord;
    			$limit = $this->perPageRecord;
    			$employeeWhere['limit'] = $limit;
    			$employeeWhere['offset'] = $offset;
    		}
    			
    		//$getAllEmployeeDetails = EmployeeModel::with(['myLeaveMaster' , 'myLeaveMaster.leaveSummaryInfo'])->where($employeeWhere)->take($limit)->skip($offset)->orderBy('v_employee_full_name', 'ASC')->get();
    		$getAllEmployeeDetails = $this->employeeModel->getRecordDetails( $employeeWhere );
    			
    		$finalData = [];
    		
    		if(!empty($getAllEmployeeDetails)){
    			foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
    				$rowData = [];
    				$rowData['i_id'] = $getAllEmployeeDetail->i_id;
    				$rowData['v_employee_full_name'] = $getAllEmployeeDetail->v_employee_full_name . ' ('.$getAllEmployeeDetail->v_employee_code .')' ;
    				$rowData['v_designation_name'] = ( isset($getAllEmployeeDetail->designationInfo->v_value) ? $getAllEmployeeDetail->designationInfo->v_value : '' );
    				$rowData['v_team_name'] = ( isset($getAllEmployeeDetail->teamInfo->v_value) ? $getAllEmployeeDetail->teamInfo->v_value : '' );
    				$rowData['dt_joining_date'] = (!empty($getAllEmployeeDetail->dt_joining_date) ? convertDateFormat($getAllEmployeeDetail->dt_joining_date , 'd.m.Y') : '' ) ;
    				$rowData['v_pan_no'] = (!empty($getAllEmployeeDetail->v_pan_no) ? $getAllEmployeeDetail->v_pan_no : '');
    				$rowData['v_duration'] = $duration;
    				
    				$getHeadWiseAmountDetails = form16ReportInfo($earningComponentDetails, $deductComponentDetails, $getAllEmployeeDetail , $salaryStartMonth , $salaryEndMonth  );
    				$displayData = ( isset($getHeadWiseAmountDetails['display']) ? $getHeadWiseAmountDetails['display'] : [] );
    				//echo "<pre>";print_r($displayData);
    				$rowData = (!empty($rowData) ? array_merge($rowData,$displayData) : $displayData );
    				
    				$finalData[] = $rowData;
    				
    			}
    		}
    		//echo "<pre>";print_r($finalData);die;
    		$data = [];
    		$data['recordDetails'] = $finalData;
    		$data['allMonths'] = $allMonths;
    		$data['pagination'] = $paginationData;
    		$data['earningComponentDetails'] = $earningComponentDetails;
    		$data['deductComponentDetails'] = $deductComponentDetails;
    		if ($page == $this->defaultPage) {
    			$data['totalRecordCount'] = $getAllEmployeeCountDetails;
    		}
    		$data['requestPageNo'] = $page;
    		$data['page_no'] = $page;
    		$data['perPageRecord'] = $this->perPageRecord;
    		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/form-16-report-list' )->with ( $data )->render();
    		echo $html;die;
    	}
    }
    
    public function statutoryBonusReport(){
    
    	$data = [];
    
    	$data['pageTitle'] = trans('messages.statutory-bonus-report');
    	$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
    	
    	$allPermissionId = config('permission_constants.ALL_STATUTORY_REPORT');
    	$data['allPermissionId'] = $allPermissionId;
    	
    	$employeeWhere = [];
    	$employeeWhere['employment_status'] = $selectedEmployeeStatus;
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$employeeWhere['show_all'] = true;
    	}
    	$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
    	 
    	$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
    	
    	$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
    
    	$data['yearDetails'] = yearDetails();
    	 
    	$data['allMonths'] = [];
    	
    	$data['recordDetails'] = [];
    	
    	$data['requestPageNo'] = 1;
    	
    	$data['totalRecordCount'] = 0;
    	 
    	return view( $this->folderName . 'statutory-report')->with($data);
    }
    public function statutoryBonusReportFilter(Request $request){
    	 
    	if(!empty($request->post())){
    
    		$selectedYear = (!empty($request->post('search_year')) ? $request->post('search_year') : date('Y') );
    		
    		//echo "selecte year  = ".$selectedYear;echo "<br><br><br>";
    		
    		$selectedDate = date('Y-m-d' , strtotime("first day of january this year" , strtotime($selectedYear)));
    		
    		//echo "selecte date  = ".$selectedDate;echo "<br><br><br>";
    		
    		$getFinancialYear = getCurrentFinancialYear($selectedYear);
    		
    		//echo "financial  year  = ".$getFinancialYear;echo "<br><br><br>";
    		
    		
    		$academicYearInfo = explode("-" , $getFinancialYear );
    		//echo "<pre>";print_r($academicYearInfo);
    		$startYear = isset($academicYearInfo[0]) ? $academicYearInfo[0] : date('Y');
    		$endYear = isset($academicYearInfo[1]) ? $academicYearInfo[1] : date('Y');
    		
    		//echo "start year = ".$startYear;echo "<br><br>";
    		//echo "start year = ".$endYear;echo "<br><br>";
    		
    		$allMonths = yearAllMonthDetails($startYear, $endYear);
    		
    		//echo "<pre>";print_r($allMonths);die;
    		
    		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
    		 
    		$limit = $this->perPageRecord;
    		$offset = 0;
    		$employeeWhere = [];
    		 
    		$employeeWhere['t_is_deleted'] = 0;
    		$employeeWhere['order_by'] = [  'v_employee_full_name' => 'asc' ];
    		if(!empty($request->post('search_team'))){
    			$employeeWhere['team_record'] = (int)Wild_tiger::decode($request->post('search_team'));
    		}
    		 
    		if( ( !empty($request->post('search_employment_status') ) )){
    			$employeeWhere['employment_status'] =  $request->post('search_employment_status') ;
    		}
    		
    		if(!empty($request->post('search_employee_name_code'))){
    			$employeeWhere['master_id'] = (int)Wild_tiger::decode($request->post('search_employee_name_code'));
    		}
    		 
    		$paginationData = [];
    		
    		$getAllEmployeeCountDetails = [];
    		 
    		$this->employeeModel = new EmployeeModel();
    		
    		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
    		
    		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_STATUTORY_REPORT'), session()->get('user_permission')  ) ) ){
    			$employeeWhere['show_all'] = true;
    		}
    		
    		if ($exportAction == 'export') {
    			$finalData = [];
    			$getAllEmployeeDetails = $this->employeeModel->getRecordDetails( $employeeWhere );
    			if(!empty($getAllEmployeeDetails)){
    				$exportIndex = 0;
    				foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
    					$rowExcelData = [];
    					$rowExcelData['sr_no'] = ++$exportIndex;
    					$rowExcelData['employee_name'] = $getAllEmployeeDetail->v_employee_full_name;
    					$rowExcelData['employee_code'] = $getAllEmployeeDetail->v_employee_code;
    					$rowExcelData['team'] = ( isset($getAllEmployeeDetail->teamInfo->v_value) ? $getAllEmployeeDetail->teamInfo->v_value : '' );
    					
    					$getMonthWiseBonusInfo = statutoryBonusReportInfo($allMonths, $getAllEmployeeDetail);
    					$exportData = ( isset($getMonthWiseBonusInfo['export']) ? $getMonthWiseBonusInfo['export'] : [] );
    					
    					$rowExcelData = (!empty($rowExcelData) ? array_merge($rowExcelData,$exportData) : $exportData );
    					$finalData[] = $rowExcelData;
    						
    				}
    			}
    			 
    			if (!empty($finalData)) {
    		
    				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.statutory-bonus-report')]);
    		
    				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalData, 'title' => trans('messages.statutory-bonus-report')]);
    				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
    				 
    			} else {
    		
    				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
    			}
    		
    			return Response::json($response);
    			die;
    		}
    		
    		 
    		if ($page == $this->defaultPage) {
    
    			$getAllEmployeeCountDetails = $this->employeeModel->getRecordDetails( $employeeWhere );
    
    			$getAllEmployeeCountDetails = count($getAllEmployeeCountDetails);
    
    			$lastpage = ceil($getAllEmployeeCountDetails / $this->perPageRecord);
    
    			$paginationData['current_page'] = $this->defaultPage;
    
    			$paginationData['per_page'] = $this->perPageRecord;
    
    			$paginationData['last_page'] = $lastpage;
    		}
    		 
    		 
    		if ($page == $this->defaultPage) {
    			$offset = 0;
    			$limit  = $this->perPageRecord;
    			$employeeWhere['limit'] = $limit;
    			$employeeWhere['offset'] = $offset;
    		} else if ($page > $this->defaultPage) {
    			$offset = ($page - 1) * $this->perPageRecord;
    			$limit = $this->perPageRecord;
    			$employeeWhere['limit'] = $limit;
    			$employeeWhere['offset'] = $offset;
    		}
    		 
    		//$getAllEmployeeDetails = EmployeeModel::with(['myLeaveMaster' , 'myLeaveMaster.leaveSummaryInfo'])->where($employeeWhere)->take($limit)->skip($offset)->orderBy('v_employee_full_name', 'ASC')->get();
    		$getAllEmployeeDetails = $this->employeeModel->getRecordDetails( $employeeWhere );
    		 
    		$finalData = [];
    
    		$duration = "";
    		$duration = ( isset($allMonths[0]['month']) ? date('M Y', strtotime($allMonths[0]['month'])) : '' ) . ' - ' . ( !empty(end($allMonths)['month']) ? date('M Y', strtotime(end($allMonths)['month']))  : '' );
    
    		if(!empty($getAllEmployeeDetails)){
    			foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
    				$rowData = [];
    				$rowData['i_id'] = $getAllEmployeeDetail->i_id;
    				$rowData['v_employee_full_name'] = $getAllEmployeeDetail->v_employee_full_name;
    				$rowData['v_employee_code'] = $getAllEmployeeDetail->v_employee_code;
    				$rowData['team'] = ( isset($getAllEmployeeDetail->teamInfo->v_value)  ? $getAllEmployeeDetail->teamInfo->v_value : '' );
    				$getMonthWiseBonusInfo =  statutoryBonusReportInfo($allMonths ,  $getAllEmployeeDetail );
    				$displayData = ( isset($getMonthWiseBonusInfo['display']) ? $getMonthWiseBonusInfo['display'] : [] );
    				$rowData = (!empty($rowData) ? array_merge($rowData,$displayData) : $displayData );
    				$finalData[] = $rowData;
    
    			}
    		}
    		 
    		$data = [];
    		$data['recordDetails'] = $finalData;
    		$data['allMonths'] = $allMonths;
    		$data['pagination'] = $paginationData;
    		$data['requestPageNo'] = $page;
    		$data['perPageRecord'] = $this->perPageRecord;
    		$data['page_no'] = $page;
    		if ($page == $this->defaultPage) {
    			$data['totalRecordCount'] = $getAllEmployeeCountDetails;
    		}
    		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/statutory-report-list' )->with ( $data )->render();
    		echo $html;die;
    	}
    }
    public function resignationReport(Request $erquest){
    	
    	/* if( ( session()->get('is_supervisor') == false )  &&  ( session()->get('role') != config('constants.ROLE_ADMIN') )  ){
    		return redirect(config('constants.DASHBORD_MASTER_URL'));
    	} */
    	
    	$data = $where = [];
    	$data['pageTitle'] = trans('messages.resignation-report');
    	$page = $this->defaultPage;
    	
    	$allPermissionId = config('permission_constants.ALL_RESIGNATION_REPORT');
    	$data['allPermissionId'] = $allPermissionId;
    	
    	#store pagination data array
    	$whereData = $paginationData = [];
    	 
    	$notificationRecordId = ( session()->has('notification_resignation_record_id') ? session()->get('notification_resignation_record_id') : 0 );
    	
    	if( $notificationRecordId > 0 ){
    		$whereData['master_id'] = $notificationRecordId;
    	}
    	## bydefault relived vala record nai aave
    	$whereData['employment_relieved_status'] = [config('constants.RELIEVED_PROBATION_EMPLOYMENT_STATUS')];
    	
    	$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
    	$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
    	$whereData['employment_status'] = $selectedEmployeeStatus;
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	#get pagination data for first page
    	if($page == $this->defaultPage ){
    		 
    		$totalRecords = count($this->crudModel->getResignRecordDetails($whereData));
    		 
    		$lastPage = ceil($totalRecords/$this->perPageRecord);
    		 
    		$paginationData['current_page'] = $this->defaultPage;
    		 
    		$paginationData['per_page'] = $this->perPageRecord;
    		 
    		$paginationData ['last_page'] = $lastPage;
    		 
    	}
    	$whereData ['limit'] = $this->perPageRecord;
    	 
    	$data['recordDetails'] = $this->crudModel->getResignRecordDetails( $whereData );
    	//echo "<pre>";print_r($data['recordDetails']);die; 
    	$data['pagination'] = $paginationData;
    	
    	$data['page_no'] = $page;
    	
    	$data['perPageRecord'] = $this->perPageRecord;
    	 
    	$data['totalRecordCount'] = $totalRecords;
    	$employeeWhere = [];
    	if(session()->get('role') == config('constants.ROLE_USER')){
    		$employeeWhere['employee_leader_name'] = (!empty(session()->get('user_employee_id')) ? session()->get('user_employee_id') : '');
    		$employeeWhere['employee_login_id'] = (!empty(session()->get('user_id')) ? session()->get('user_id') : '');
    	}
    	$employeeWhere['employment_status'] = $selectedEmployeeStatus;
    	$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
    	//$data['employeeDetails'] = EmployeeModel::orderBy('v_employee_full_name', 'ASC')->where('t_is_deleted',0)->get();
    	$employeeWhere['order_by'] = [ 'v_employee_full_name'  => 'asc'];
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$employeeWhere['show_all'] = true;
    	}
    	$data['employeeDetails'] = $this->employeeCrudModel->getRecordDetails($employeeWhere);
    	$data['designationDetails'] = LookupMaster::where('v_module_name' , config('constants.DESIGNATION_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value' , 'asc')->get();
    	$data['leaderDetails'] = EmployeeModel::orderBy('v_employee_full_name', 'ASC')->where('t_is_deleted',0)->get();
    	$data['stausInfo'] = stausInfo();
    	$data['employmentStatusInfo'] = employmentStatusMaster();
    	
    	$data['terminationReasonDetails'] = LookupMaster::where('v_module_name',config('constants.TERMINATION_REASONS_LOOKUP'))->orderBy('v_value', 'ASC')->get();
    	$data['resignationReasonDetails'] = LookupMaster::where('v_module_name',config('constants.RESIGN_REASONS_LOOKUP'))->orderBy('v_value', 'ASC')->get();
    	
    	if ($page == $this->defaultPage) {
    		$data['totalRecordCount'] = $totalRecords;
    	}
    	
    	return view( $this->folderName . 'resignation-report')->with($data);
    }
    public function resignationReportFilter(Request $request){
    	//variable defined
    	$whereData = $likeData = [];
    	
    	
    	$page = (! empty($request->post('page')) ? $request->post('page') : 1);
    	
    	if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_RESIGNATION_REPORT'), session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	//search record
    	if(!empty($request->post('search_employee_name_code'))){
    		$whereData['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee_name_code'));
    	}
    	if(!empty($request->post('search_team'))){
    		$whereData['team_record'] = (int)Wild_tiger::decode($request->post('search_team'));
    	}
    	if(!empty($request->post('search_designation'))){
    		$whereData['designation'] = (int)Wild_tiger::decode($request->post('search_designation'));
    	}
    	if(!empty($request->post('search_report_to'))){
    		$whereData['leader_name'] = (int)Wild_tiger::decode($request->post('search_report_to'));
    	}
    	
    	if(!empty($request->post('search_notice_period_start_from_date'))){
    		$whereData['notice_period_start_from_date'] = ($request->post('search_notice_period_start_from_date'));
    	}
    	if(!empty($request->post('search_notice_period_start_to_date'))){
    		$whereData['notice_period_start_to_date'] = ($request->post('search_notice_period_start_to_date'));
    	}
    	
    	if(!empty($request->post('search_notice_period_end_from_date'))){
    		$whereData['notice_period_end_from_date'] = ($request->post('search_notice_period_end_from_date'));
    	}
    	if(!empty($request->post('search_notice_period_end_to_date'))){
    		$whereData['notice_period_end_to_date'] = ($request->post('search_notice_period_end_to_date'));
    	}
    	
    	if(!empty($request->post('search_status'))){
    		$whereData['e_status'] = ($request->post('search_status'));
    	}
    	if(!empty($request->post('search_type'))){
    		$whereData['type'] = ($request->post('search_type'));
    	}
    	## employment status filter
    	if( ( !empty($request->post('search_employment_status') ) )){
    		$whereData['employment_status'] =  $request->post('search_employment_status') ;
    	}
    	
    	if( ( !empty($request->post('search_resign_status') ) )){
    		if(!empty($request->post('search_type')) && ( $request->post('search_type') == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) ){
    			$whereData['resign_status'] =  (int)Wild_tiger::decode( $request->post('search_resign_status') ) ;
    		}
    	}
    	
    	if( ( !empty($request->post('search_terminate_status') ) )){
    		if(!empty($request->post('search_type')) && ( $request->post('search_type') == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) ){
    			$whereData['terminate_status'] =   (int)Wild_tiger::decode( $request->post('search_terminate_status') ) ;
    		}
    	}
    	
    	
    	
    	$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
    	 
    	if ($exportAction == 'export') {
    		$finalExportData = [];
    		$getExportRecordDetails = $this->crudModel->getResignRecordDetails($whereData, $likeData);
    		if (!empty($getExportRecordDetails)) {
    			$excelIndex = 0;
    			foreach ($getExportRecordDetails as $getExportRecordDetail) {
    	
    				$rowExcelData = [];
    				$rowExcelData['sr_no'] = ++$excelIndex;
    				if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_RESIGNATION_REPORT'), session()->get('user_permission')  ) ) ) ) ){
    					$rowExcelData['exit_type'] = ( ( $getExportRecordDetail->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE')  ) ? trans('messages.resignation')   :  trans('messages.termination') );
    					$rowExcelData['reason_for_leaving'] = ( ( $getExportRecordDetail->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE')  ) ? ( isset($getExportRecordDetail->resignation->v_value) ?   $getExportRecordDetail->resignation->v_value : ''  )  :   ( isset($getExportRecordDetail->termination->v_value) ?  $getExportRecordDetail->termination->v_value : ''  ) );
    				}
    				
    				$rowExcelData['employee_name'] = ( isset($getExportRecordDetail->employee->v_employee_full_name) ?  ($getExportRecordDetail->employee->v_employee_full_name) :'' );
    				$rowExcelData['employee_code'] = ( isset($getExportRecordDetail->employee->v_employee_code) ?  ($getExportRecordDetail->employee->v_employee_code) :'' );
    				$rowExcelData['team'] = ( isset($getExportRecordDetail->employee->teamInfo->v_value) ?  ($getExportRecordDetail->employee->teamInfo->v_value) :'' );
    				$rowExcelData['designation'] = ( isset($getExportRecordDetail->employee->designationInfo->v_value) ?  ($getExportRecordDetail->employee->designationInfo->v_value) :'' );
    				$rowExcelData['leader_name_/_reporting_manager'] = ( isset($getExportRecordDetail->employee->leaderInfo->v_employee_full_name) ?  ($getExportRecordDetail->employee->leaderInfo->v_employee_full_name) .( isset($getExportRecordDetail->employee->leaderInfo->v_employee_code) ?  ' (' .$getExportRecordDetail->employee->leaderInfo->v_employee_code .')' :'' ):'' );
    				$rowExcelData['mobile'] = ( isset($getExportRecordDetail->employee->v_contact_no) ?  ($getExportRecordDetail->employee->v_contact_no) :'' );
    				$rowExcelData['email'] = ( isset($getExportRecordDetail->employee->v_outlook_email_id) ?  ($getExportRecordDetail->employee->v_outlook_email_id) :'' );
    				$rowExcelData['notice_period_start_date'] = ( isset($getExportRecordDetail->dt_notice_start_date) ?  convertDateFormat($getExportRecordDetail->dt_notice_start_date,'d.m.Y') :'' );
    				$rowExcelData['notice_period_expected_end_date'] = ( isset($getExportRecordDetail->dt_notice_end_date) ?  convertDateFormat($getExportRecordDetail->dt_notice_end_date,'d.m.Y') :'' );
    				$rowExcelData['status'] = ( isset($getExportRecordDetail->e_status) ?  ($getExportRecordDetail->e_status ) :'' );
    				$rowExcelData['action_taken_by'] = "";
    				$rowExcelData['remark'] = "";
    				if( $getExportRecordDetail->e_status != config('constants.PENDING_STATUS') ){
    					$rowExcelData['action_taken_by'] = ( isset($getExportRecordDetail->approveEmployeeInfo->v_name)  ? ' ' . $getExportRecordDetail->approveEmployeeInfo->v_name : '' );
    					$rowExcelData['remark'] = ( isset($getExportRecordDetail->v_approval_remark)  ? ' ' . $getExportRecordDetail->v_approval_remark : '' );
    					
    				}
    				$finalExportData[] = $rowExcelData;
    					
    			}
    		}
    		 
    		if (!empty($finalExportData)) {
    			 
    			$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.resignation-report')]);
    			 
    			$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.resignation-report')]);
    			$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
    		} else {
    			 
    			$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
    		}
    		 
    		return Response::json($response);
    		die;
    	}
    	 
    	$paginationData = [];
    	 
    	if ($page == $this->defaultPage) {
    		 
    		$totalRecords = count($this->crudModel->getResignRecordDetails( $whereData , $likeData ));
    		 
    		 
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
    	 
    	$data['recordDetails'] = $this->crudModel->getResignRecordDetails( $whereData, $likeData );
    	 
    	if(isset($totalRecords)){
    		$data ['totalRecordCount'] = $totalRecords;
    	}
    	$data['pagination'] = $paginationData;
    	 
    	$data['page_no'] = $page;
    	 
    	$data['perPageRecord'] = $this->perPageRecord;
    	 
    	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/resignation-report-list' )->with ( $data )->render();
    	 
    	echo $html;die;
    }
    
    public function showResignationNotificationRecord( $notiRecordId = null ,  $recordId = null){
    
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
    			session()->flash('notification_resignation_record_id' , $recordId);
    		}
    	}
    	return redirect( config('constants.REGIGNATION_REPORT_URL') );
    }
    
    public function salaryReport(Request $erquest){
    	 
    	/* if( ( session()->get('is_supervisor') == false )  &&  ( session()->get('role') != config('constants.ROLE_ADMIN') )  ){
    		return redirect(config('constants.DASHBORD_MASTER_URL'));
    	} */
    	 
    	$data = $where = [];
    	$data['pageTitle'] = trans('messages.pay-slips-report');
    	$page = $this->defaultPage;
    	
    	$allPermissionId = config('permission_constants.ALL_SALARY_REPORT');
    	$data['allPermissionId'] = $allPermissionId;
    	
    	$whereData = $paginationData = [];
    	
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	$getMaximumSalaryGenearteInfo = Salary::where('t_is_deleted' , 0 )->orderBy('dt_salary_month' , 'desc')->first();
    	$maxDate = (!empty($getMaximumSalaryGenearteInfo) ? $getMaximumSalaryGenearteInfo->dt_salary_month : date('Y-m-d') );
    	
    	$whereData['salary_start_month'] = $maxDate;
    	$whereData['salary_end_month'] = $maxDate;
    	
    	$data['startMonth'] = date('M-Y' ,strtotime($maxDate));
    	$data['endMonth'] = date('M-Y' ,strtotime($maxDate) );
    	
    	#store pagination data array
    	
    	$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
    	$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
    	$whereData['employment_status'] = $selectedEmployeeStatus;
    	#get pagination data for first page
    	if($page == $this->defaultPage ){
    		 
    		$totalRecords = count($this->crudModel->getSalaryReportRecordDetails($whereData));
    		 
    		$lastPage = ceil($totalRecords/$this->perPageRecord);
    		 
    		$paginationData['current_page'] = $this->defaultPage;
    		 
    		$paginationData['per_page'] = $this->perPageRecord;
    		 
    		$paginationData ['last_page'] = $lastPage;
    		 
    	}
    	$whereData ['limit'] = $this->perPageRecord;
    
    	$data['recordDetails'] = $this->crudModel->getSalaryReportRecordDetails( $whereData );
    	//
    	$data['pagination'] = $paginationData;
    	 
    	$data['pageNo'] = $page;
    	 
    	$data['perPageRecord'] = $this->perPageRecord;
    
    	$data['totalRecordCount'] = $totalRecords;
    	 
    	$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
    	
    	$where = [];
    	$where['employment_status'] = $selectedEmployeeStatus;
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$where['show_all'] = true;
    	}
    	$data['employeeDetails'] = $this->getEmployeeDropdownDetails($where);
    	$data['designationDetails'] = LookupMaster::where('v_module_name' , config('constants.DESIGNATION_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value' , 'asc')->get();
    	
    	return view( $this->folderName . 'salary-report')->with($data);
    }
    
    
    public function filterSalaryReport(Request $request){
    	//variable defined
    	$whereData = $likeData = [];
    	
    	if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_SALARY_REPORT'), session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	$page = (! empty($request->post('page')) ? $request->post('page') : 1);
    
    	if(!empty($request->post('search_by'))){
    		$likeData['searchBy'] = trim($request->post('search_by'));
    	}
    	
    	if( ( !empty($request->post('search_employment_status') ) )){
    		$whereData['employment_status'] =  $request->post('search_employment_status') ;
    	}
    	
    	//search record
    	if(!empty($request->post('search_employee'))){
    		$whereData['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee'));
    	}
    	if(!empty($request->post('search_team'))){
    		$whereData['team'] = (int)Wild_tiger::decode($request->post('search_team'));
    	}
    	if(!empty($request->post('search_designation'))){
    		$whereData['designation'] = (int)Wild_tiger::decode($request->post('search_designation'));
    	}
    	if(!empty($request->post('search_start_month'))){
    		$whereData['salary_start_month'] = ($request->post('search_start_month'));
    	}
    	if(!empty($request->post('search_end_month'))){
    		$whereData['salary_end_month'] = ($request->post('search_end_month'));
    	}
    	
    	if(!empty($request->post('search_bank'))){
    		$whereData['bank'] = ($request->post('search_bank'));
    	}
    	
    	$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
    
    	if ($exportAction == 'export') {
    		$finalExportData = [];
    		$getExportRecordDetails = $this->crudModel->getSalaryReportRecordDetails($whereData, $likeData);
    		if (!empty($getExportRecordDetails)) {
    			$excelIndex = 0;
    			foreach ($getExportRecordDetails as $getExportRecordDetail) {
    				 
    				$rowExcelData = [];
    				$rowExcelData['sr_no'] = ++$excelIndex;
    				$rowExcelData['month'] = ( isset($getExportRecordDetail->dt_salary_month) ?  convertDateFormat($getExportRecordDetail->dt_salary_month,'m.Y') :'' );
    				$rowExcelData['employee_name'] = ( isset($getExportRecordDetail->employee->v_employee_full_name) ?  ($getExportRecordDetail->employee->v_employee_full_name) :'' );
    				$rowExcelData['employee_code'] = ( isset($getExportRecordDetail->employee->v_employee_code) ?  ($getExportRecordDetail->employee->v_employee_code) :'' );
    				$rowExcelData['team'] = ( isset($getExportRecordDetail->employee->teamInfo->v_value) ?  ($getExportRecordDetail->employee->teamInfo->v_value) :'' );
    				$rowExcelData['designation'] = ( isset($getExportRecordDetail->employee->designationInfo->v_value) ?  ($getExportRecordDetail->employee->designationInfo->v_value) :'' );
    				$rowExcelData['contact_number'] = ( isset($getExportRecordDetail->employee->v_contact_no) ?  ($getExportRecordDetail->employee->v_contact_no) :'' );
    				$rowExcelData['pan'] = ( isset($getExportRecordDetail->employee->v_pan_no) ?  ($getExportRecordDetail->employee->v_pan_no) :'' );
    				$rowExcelData['uan_number'] = ( isset($getExportRecordDetail->employee->v_uan_no) ?  ($getExportRecordDetail->employee->v_uan_no) :'' );
    				$rowExcelData['aadhaar_card'] = ( isset($getExportRecordDetail->employee->v_aadhar_no) ?  ($getExportRecordDetail->employee->v_aadhar_no) :'' );
    				$rowExcelData['bank'] = ( isset($getExportRecordDetail->employee->bankInfo->v_value) ?  ($getExportRecordDetail->employee->bankInfo->v_value) :'' );
    				$rowExcelData['account_number'] = ( isset($getExportRecordDetail->employee->v_bank_account_no) ?  ($getExportRecordDetail->employee->v_bank_account_no) :'' );
    				$rowExcelData['total_earnings'] = ( isset($getExportRecordDetail->d_total_earning_amount) ?  $getExportRecordDetail->d_total_earning_amount :'' );
    				$rowExcelData['total_deductions'] = ( isset($getExportRecordDetail->d_total_deduct_amount) ?  $getExportRecordDetail->d_total_deduct_amount :'' );
    				$rowExcelData['net_pay'] = ( isset($getExportRecordDetail->d_net_pay_amount) ?  $getExportRecordDetail->d_net_pay_amount :'' );
    				$finalExportData[] = $rowExcelData;
    					
    			}
    		}
    		 
    		if (!empty($finalExportData)) {
    
    			$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.pay-slips-report')]);
    
    			$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.pay-slips-report')]);
    			$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
    		} else {
    
    			$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
    		}
    		 
    		return Response::json($response);
    		die;
    	}
    
    	$paginationData = [];
    
    	if ($page == $this->defaultPage) {
    		 
    		$totalRecords = count($this->crudModel->getSalaryReportRecordDetails( $whereData , $likeData ));
    		 
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
    
    	$data['recordDetails'] = $this->crudModel->getSalaryReportRecordDetails( $whereData, $likeData );
    
    	if(isset($totalRecords)){
    		$data ['totalRecordCount'] = $totalRecords;
    	}
    	$data['pagination'] = $paginationData;
    
    	$data['pageNo'] = $page;
    
    	$data['perPageRecord'] = $this->perPageRecord;
    
    	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/salary-report-list' )->with ( $data )->render();
    
    	echo $html;die;
    }
    
    public function accountTeamSalaryReport(Request $erquest){
    
    	/* if( ( session()->get('is_supervisor') == false )  &&  ( session()->get('role') != config('constants.ROLE_ADMIN') )  ){
    		return redirect(config('constants.DASHBORD_MASTER_URL'));
    	} */
    
    	$data = $where = [];
    	$data['pageTitle'] = trans('messages.salary-report-for-account-team');
    	
    	$page = $this->defaultPage;
    
    	$allPermissionId = config('permission_constants.ALL_SALARY_REPORT_FOR_ACCOUNT_TEAM');
    	$data['allPermissionId'] = $allPermissionId;
    	
    	$getMaximumSalaryGenearteInfo = Salary::where('t_is_deleted' , 0 )->orderBy('dt_salary_month' , 'desc')->first();
    	$maxDate = (!empty($getMaximumSalaryGenearteInfo) ? $getMaximumSalaryGenearteInfo->dt_salary_month : date('Y-m-d') );
    	$data['startMonth'] = date('M-Y' ,strtotime($maxDate));
    	$data['endMonth'] = date('M-Y' ,strtotime($maxDate) );
    	
    	#store pagination data array
    	$whereData = $paginationData = [];
    	$whereData['accountReport'] = true;
    	$whereData['salary_start_month'] = $data['startMonth'];
    	$whereData['salary_end_month'] = $data['endMonth'];
    	
    	$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
    	$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
    	$whereData['employment_status'] = $selectedEmployeeStatus;
    	
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	#get pagination data for first page
    	if($page == $this->defaultPage ){
    		 
    		$totalRecords = count($this->crudModel->getSalaryReportRecordDetails($whereData));
    		 
    		$lastPage = ceil($totalRecords/$this->perPageRecord);
    		 
    		$paginationData['current_page'] = $this->defaultPage;
    		 
    		$paginationData['per_page'] = $this->perPageRecord;
    		 
    		$paginationData ['last_page'] = $lastPage;
    		 
    	}
    	$whereData ['limit'] = $this->perPageRecord;
    
    	$data['recordDetails'] = $this->crudModel->getSalaryReportRecordDetails( $whereData );
    	 
    	$data['pagination'] = $paginationData;
    
    	$data['pageNo'] = $page;
    
    	$data['perPageRecord'] = $this->perPageRecord;
    
    	$data['totalRecordCount'] = $totalRecords;
    
    	$employeeWhere = [];
    	$employeeWhere['employment_status'] = $selectedEmployeeStatus;
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$employeeWhere['show_all'] = true;
    	}
    	$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
    	$data['teamRecordDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
    	return view( $this->folderName . 'salary-report-for-account-team')->with($data);
    }
    public function filterAccountTeamSalaryReport(Request $request){
    	//variable defined
    	$whereData = $likeData = [];
    
    	$page = (! empty($request->post('page')) ? $request->post('page') : 1);
    	$whereData['accountReport'] = true;
    	
    	if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_SALARY_REPORT_FOR_ACCOUNT_TEAM'), session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	if(!empty($request->post('search_by'))){
    		$likeData['specifiyColumns'] = ['v_bank_account_no'];
    		$likeData['searchBy'] = trim($request->post('search_by'));
    	}
    	 
    	if( ( !empty($request->post('search_employment_status') ) )){
    		$whereData['employment_status'] =  $request->post('search_employment_status') ;
    	}
    	
    	//search record
    	if(!empty($request->post('search_employee'))){
    		$whereData['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee'));
    	}
    	
    	if(!empty($request->post('search_start_month'))){
    		$whereData['salary_start_month'] = ($request->post('search_start_month'));
    	}
    	if(!empty($request->post('search_end_month'))){
    		$whereData['salary_end_month'] = ($request->post('search_end_month'));
    	}
    	if(!empty($request->post('search_team'))){
    		$whereData['team'] = (int)Wild_tiger::decode($request->post('search_team'));
    	}
    	if(!empty($request->post('search_bank'))){
    		$whereData['bank'] = ($request->post('search_bank'));
    	}
    	
    	$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
    
    	if ($exportAction == 'export') {
    		$finalExportData = [];
    		$getExportRecordDetails = $this->crudModel->getSalaryReportRecordDetails($whereData, $likeData);
    		if (!empty($getExportRecordDetails)) {
    			$excelIndex = 0;
    			foreach ($getExportRecordDetails as $getExportRecordDetail) {
    					
    				$rowExcelData = [];
    				$rowExcelData['sr_no'] = ++$excelIndex;
    				$rowExcelData['month'] = ( isset($getExportRecordDetail->dt_salary_month) ?  convertDateFormat($getExportRecordDetail->dt_salary_month,'m.Y') :'' );
    				$rowExcelData['employee_name'] = ( isset($getExportRecordDetail->employee->v_employee_full_name) ?  ($getExportRecordDetail->employee->v_employee_full_name) :'' );
    				$rowExcelData['employee_code'] = ( isset($getExportRecordDetail->employee->v_employee_code) ?  ($getExportRecordDetail->employee->v_employee_code) :'' );
    				$rowExcelData['team'] = ( isset($getExportRecordDetail->employee->teamInfo->v_value) && !empty($getExportRecordDetail->employee->teamInfo->v_value) ? $getExportRecordDetail->employee->teamInfo->v_value : '' );
    				$rowExcelData['bank'] = ( isset($getExportRecordDetail->employee->bankInfo->v_value) ?  ($getExportRecordDetail->employee->bankInfo->v_value) :'' );
    				$rowExcelData['account_number'] = ( ( isset($getExportRecordDetail->employee->bankInfo->i_id) && ( $getExportRecordDetail->employee->bankInfo->i_id == config('constants.HDFC_BANK_ID') ) )  ? (  isset( $getExportRecordDetail->employee->v_bank_account_no) ?  $getExportRecordDetail->employee->v_bank_account_no : '-'  ) : '-' );
    				$rowExcelData['net_pay'] = ( isset($getExportRecordDetail->d_net_pay_amount) ?  $getExportRecordDetail->d_net_pay_amount :'' );
    				
    				$professionTaxAmount = "";
    				if( isset($getExportRecordDetail->generatedSalaryInfo) && (!empty($getExportRecordDetail->generatedSalaryInfo)) ){
    					foreach($getExportRecordDetail->generatedSalaryInfo as $salaryDetails){
    						if(  ( isset($salaryDetails->i_component_id) && ( $salaryDetails->i_component_id == config('constants.PT_SALARY_COMPONENT_ID') ) ) ){
    							$professionTaxAmount = ( isset($salaryDetails->d_paid_amount) ? ($salaryDetails->d_paid_amount) : '' );
    						}
    					}
    				}
    				$rowExcelData['professional_tax'] = $professionTaxAmount;
    				$finalExportData[] = $rowExcelData;
    					
    			}
    		}
    		 
    		if (!empty($finalExportData)) {
    
    			$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.salary-report-for-account-team')]);
    
    			$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.salary-report-for-account-team')]);
    			$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
    		} else {
    
    			$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
    		}
    		 
    		return Response::json($response);
    		die;
    	}
    
    	$paginationData = [];
    
    	if ($page == $this->defaultPage) {
    		 
    		$totalRecords = count($this->crudModel->getSalaryReportRecordDetails( $whereData , $likeData ));
    		 
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
    
    	$data['recordDetails'] = $this->crudModel->getSalaryReportRecordDetails( $whereData, $likeData );
    
    	if(isset($totalRecords)){
    		$data ['totalRecordCount'] = $totalRecords;
    	}
    	$data['pagination'] = $paginationData;
    
    	$data['pageNo'] = $page;
    
    	$data['perPageRecord'] = $this->perPageRecord;
    
    	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/salary-report-for-account-team-list' )->with ( $data )->render();
    
    	echo $html;die;
    }
    
    public function attendanceReportDayWise(Request $erquest){
    
    	/* if( ( session()->get('is_supervisor') == false )  &&  ( session()->get('role') != config('constants.ROLE_ADMIN') )  ){
    		return redirect(config('constants.DASHBORD_MASTER_URL'));
    	} */
    	$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
    	$data = $where = [];
    	$data['pageTitle'] = trans('messages.hr-attendance-day-wise');

    	$allPermissionId = config('permission_constants.ALL_ATTENDANCE_REPORT_DAY_WISE');
    	$data['allPermissionId'] = $allPermissionId;
    	
    	$page = $this->defaultPage;
    
    	$data['recordDetails'] = [];
    	
    	$data['requestPageNo'] = 1;
    	 
    	$data['totalRecordCount'] = 0;
    	
    	$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
    
    	$employeeWhere = [];
    	$employeeWhere['employment_status'] = $selectedEmployeeStatus;
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$employeeWhere['show_all'] = true;
    	}
    	$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
    	
    	$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
    	
    	$data['monthAllDates'] = [];
    	 
    	return view( $this->folderName . 'attendance-report-day-wise')->with($data);
    }
    public function filterAttendanceReportDayWise(Request $request){
    	//variable defined
    	$whereData = $likeData = [];
    
    	$page = (! empty($request->post('page')) ? $request->post('page') : 1);
    
    	$selectedYear = (!empty($request->post('search_year')) ? $request->post('search_year') : date('Y') );
    	
    	$searchStartDate = (!empty($request->post('search_start_date')) ? dbDate( $request->post('search_start_date') ) : date('Y-m-d') );
    	$searchEndDate = (!empty($request->post('search_end_date')) ? dbDate( $request->post('search_end_date') ) : date('Y-m-d') );
    	
    	$allMonths = monthListBetweenDates($searchStartDate, $searchEndDate);
    	
    	$limit = $this->perPageRecord;
    	$offset = 0;
    	$employeeWhere = [];
    	$employeeWhere['order_by'] = [  'v_employee_full_name' => 'asc' ];
    	$employeeWhere['t_is_deleted'] = 0;
    	 
    	if(!empty($request->post('search_team'))){
    		$employeeWhere['team'] = (int)Wild_tiger::decode($request->post('search_team'));
    	}
    	 
    	if( ( !empty($request->post('search_employment_status') ) )){
    		$employeeWhere['employment_status'] =  $request->post('search_employment_status') ;
    	}
    	
    	if(!empty($request->post('search_employee'))){
    		$employeeWhere['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee'));
    	}
    	
    	$selectedMonth = (!empty($request->input('search_attendance_month')) ? dbDate($request->input('search_attendance_month')) : null );
    	
    	$month = date('m' , strtotime($selectedMonth));
    		
    	$year = date('Y' , strtotime($selectedMonth));
    	
    	$monthAllDates = Wild_tiger::getAllDateOfSalaryMonth($month , $year );
    	
    	$holidayWhere['holiday_date'] = $monthAllDates;
    	
    	$monthStartDate = attendanceStartDate( $month , $year);
    	$monthEndDate = attendanceEndDate( $month , $year);
    	
    	$monthHolidayDates = $this->getAllHoliDayDetails($monthAllDates);
    	
    	$paginationData = [];
    		
    	$getAllEmployeeCountDetails = [];
    	 
    	$this->employeeModel = new EmployeeModel();
    	
    	$suspendWhere = [];
    	$suspendWhere['startDate'] = $monthStartDate;
    	$suspendWhere['endDate'] = $monthEndDate;
    	$suspendWhere['monthAllDates'] = $monthAllDates;
    	
    	$employeeWiseSuspendRecordDetails = $this->getAllSuspendDateWiseRecords( $suspendWhere );
    	
    	if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_ATTENDANCE_REPORT_DAY_WISE'), session()->get('user_permission')  ) ) ){
    		$employeeWhere['show_all'] = true;
    	}
    	
    	$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
    	
    	if ($exportAction == 'export') {
    		$finalExportData = [];
    		$getExportRecordDetails = $this->crudModel->employeeAttendanceDetails($employeeWhere, $likeData);
    		
    		$employeeWiseWeekOffDates = [];
    		if(!empty($getExportRecordDetails)){
    			foreach($getExportRecordDetails as $getExportRecordDetail){
    				$getEmployeeWeekOffDates = $this->getEmployeeMonthlyWeekOff( ['employeeId' => $getExportRecordDetail->i_id , 'month' => $year.'-'.$month.'-01' , 'attendanceView' => true ] );
    				$monthAllWeekOfDates = ( isset($getEmployeeWeekOffDates['weekOffDates']) ? $getEmployeeWeekOffDates['weekOffDates'] : [] );
    				$employeeWiseWeekOffDates[$getExportRecordDetail->i_id] = $monthAllWeekOfDates;
    			}
    		}
    		
    		if (!empty($getExportRecordDetails)) {
    			$excelIndex = 0;
    			foreach ($getExportRecordDetails as $getExportRecordDetail) {
    				
    			
    				$attendanceInfo = attendanceDayWiseReportInfo($getExportRecordDetail, $monthAllDates , $employeeWiseSuspendRecordDetails , $employeeWiseWeekOffDates ,  $monthHolidayDates  );
    				//echo "<pre>";print_r($attendanceInfo);die;
    				$dateWiseStatus = ( isset($attendanceInfo['dateWiseStatus']) ? $attendanceInfo['dateWiseStatus'] : 0 );
    				$absentCount = ( isset($attendanceInfo['absentCount']) ? $attendanceInfo['absentCount'] : 0 );
    				$halfLeaveCount = ( isset($attendanceInfo['halfLeaveCount']) ? $attendanceInfo['halfLeaveCount'] : 0 );
    				
    				$rowExcelData = [];
    				$rowExcelData['sr_no'] = ++$excelIndex;
    				$rowExcelData['employee_name'] = ( isset($getExportRecordDetail->v_employee_full_name) ?  ($getExportRecordDetail->v_employee_full_name) :'' );
    				$rowExcelData['employee_code'] = ( isset($getExportRecordDetail->v_employee_code) ?  ($getExportRecordDetail->v_employee_code) :'' );
    				$rowExcelData['team'] = ( isset($getExportRecordDetail->teamInfo->v_value) ?  ($getExportRecordDetail->teamInfo->v_value) :'' );
    				
    				if(count($monthAllDates) > 0 ){
    					foreach($monthAllDates as $monthAllDate){
    						$rowExcelData[convertDateFormat($monthAllDate,  'd.m.Y')] = ( isset($dateWiseStatus[$monthAllDate]) ? $dateWiseStatus[$monthAllDate] : '' );
    					}
    				}
    				$rowExcelData['absent'] = $absentCount;
    				$rowExcelData['half_leaves'] = $halfLeaveCount;
    				$rowExcelData['total'] = ( $halfLeaveCount + $absentCount ) ;
    				$finalExportData[] = $rowExcelData;
    					
    			}
    		}
    		 
    		if (!empty($finalExportData)) {
    	
    			$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.hr-attendance-day-wise')]);
    	
    			$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.hr-attendance-day-wise')]);
    			$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
    		} else {
    	
    			$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
    		}
    		 
    		return Response::json($response);
    		die;
    	} 
    	
    	if ($page == $this->defaultPage) {
    	
    		$getAllEmployeeCountDetails = $this->crudModel->employeeAttendanceDetails( $employeeWhere );
    	
    		$getAllEmployeeCountDetails = count($getAllEmployeeCountDetails);
    	
    		$lastpage = ceil($getAllEmployeeCountDetails / $this->perPageRecord);
    	
    		$paginationData['current_page'] = $this->defaultPage;
    	
    		$paginationData['per_page'] = $this->perPageRecord;
    	
    		$paginationData['last_page'] = $lastpage;
    	}
    	 
    	 
    	if ($page == $this->defaultPage) {
    		$offset = 0;
    		$limit  = $this->perPageRecord;
    		$employeeWhere['limit'] = $limit;
    		$employeeWhere['offset'] = $offset;
    	} else if ($page > $this->defaultPage) {
    		$offset = ($page - 1) * $this->perPageRecord;
    		$limit = $this->perPageRecord;
    		$employeeWhere['limit'] = $limit;
    		$employeeWhere['offset'] = $offset;
    	}
    	 
    	//$getAllEmployeeDetails = EmployeeModel::with(['myLeaveMaster' , 'myLeaveMaster.leaveSummaryInfo'])->where($employeeWhere)->take($limit)->skip($offset)->orderBy('v_employee_full_name', 'ASC')->get();
    	$getAllEmployeeDetails = $this->crudModel->employeeAttendanceDetails( $employeeWhere );
    	
    	$employeeWiseWeekOffDates = [];
    	if(!empty($getAllEmployeeDetails)){
    		foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
    			$getEmployeeWeekOffDates = $this->getEmployeeMonthlyWeekOff( ['employeeId' => $getAllEmployeeDetail->i_id , 'month' => $year.'-'.$month.'-01' ,  'attendanceView'  => true ] );
    			$monthAllWeekOfDates = ( isset($getEmployeeWeekOffDates['weekOffDates']) ? $getEmployeeWeekOffDates['weekOffDates'] : [] );
    			$employeeWiseWeekOffDates[$getAllEmployeeDetail->i_id] = $monthAllWeekOfDates;
    			
    		}
    	}
    	
    	$finalData = [];
    	
    	$duration = "";
    	$duration = ( isset($allMonths[0]['month']) ? date('M Y', strtotime($allMonths[0]['month'])) : '' ) . ' - ' . ( !empty(end($allMonths)['month']) ? date('M Y', strtotime(end($allMonths)['month']))  : '' );
    	
    	$data = [];
    	$data['recordDetails'] = $getAllEmployeeDetails;
    	$data['monthAllDates'] = $monthAllDates;
    	$data['pagination'] = $paginationData;
    	
    	if ($page == $this->defaultPage) {
    		$data['totalRecordCount'] = $getAllEmployeeCountDetails;
    	}
    	$data['requestPageNo'] = $page;
    	$data['monthHolidayDates'] = $monthHolidayDates;
    	$data['employeeWiseSuspendRecordDetails'] = $employeeWiseSuspendRecordDetails;
    	$data['employeeWiseWeekOffDates'] = $employeeWiseWeekOffDates ;
    	$data['page_no'] = $page;
    	$data['perPageRecord'] = $this->perPageRecord;
    	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/attendance-report-day-wise-list' )->with ( $data )->render();
    	echo $html;die;
    }
    
    public function onHoldSalaryReport(){
    
    	/* if( ( session()->get('is_supervisor') == false )  &&  ( session()->get('role') != config('constants.ROLE_ADMIN') )  ){
    		return redirect(config('constants.DASHBORD_MASTER_URL'));
    	} */
    
    	$data = $where = [];
    	
    	$data['pageTitle'] = trans('messages.on-hold-salary-report');
    	 
    	$page = $this->defaultPage;
    
    	$allPermissionId = config('permission_constants.ALL_ON_HOLD_SALARY_REPORT');
    	$data['allPermissionId'] = $allPermissionId;
    	
    	#store pagination data array
    	$whereData = $paginationData = [];
    	
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
    	$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
    	$whereData['employment_status'] = $selectedEmployeeStatus;
    	
    	$data['recordDetails'] = $this->crudModel->getOnHoldSalaryDetails( $whereData );
    	
    	/*
    	if(!empty($data['recordDetails'])){
    		foreach($data['recordDetails'] as $ecordDetail){
    			$getHoldAmountInfo = getHoldAmountInfo($ecordDetail);
    			 
    			$totalOnHoldSalaryAmount = ( isset($getHoldAmountInfo['totalOnHoldSalaryAmount']) ? $getHoldAmountInfo['totalOnHoldSalaryAmount'] : 0 ) ;
    			$decuctOnHoldSalaryAmount = ( isset($getHoldAmountInfo['deductOnHoldSalaryAmount']) ? $getHoldAmountInfo['deductOnHoldSalaryAmount'] : 0 ) ;
    			$expectedReleasedDate = ( isset($getHoldAmountInfo['expectedReleaseDate']) ? $getHoldAmountInfo['expectedReleaseDate'] : 0 ) ;
    			$expectedReleasedDate = ( isset($getHoldAmountInfo['expectedReleaseDate']) ? $getHoldAmountInfo['expectedReleaseDate'] : null ) ;
    			$releaseDate = ( isset($getHoldAmountInfo['releaseDate']) ? $getHoldAmountInfo['releaseDate'] : null ) ;
    			 
    			$updateEmployeeSalaryInfo = [];
    			$updateEmployeeSalaryInfo['dt_on_hold_expected_release_date'] = $expectedReleasedDate;
    			$updateEmployeeSalaryInfo['dt_on_hold_release_date'] = $releaseDate;
    			$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , $updateEmployeeSalaryInfo , [ 'i_id' => $ecordDetail->i_id ] );
    			//echo $this->crudModel->last_query();echo "<br><br><br>";
    		}
    	}
    	*/
    	$data['pagination'] = $paginationData;
    
    	$data['pageNo'] = $page;
    
    	$data['perPageRecord'] = $this->perPageRecord;
    
    	$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
    	
    	$where = [];
    	$where['hold_salary_status'] = config('constants.SELECTION_YES');
    	$where['employment_status'] = $selectedEmployeeStatus;
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$where['show_all'] = true;
    	}
    	$data['employeeDetails'] = $this->getEmployeeDropdownDetails($where);
    	
    	$data['designationDetails'] = LookupMaster::where('v_module_name' , config('constants.DESIGNATION_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value' , 'asc')->get();
    	
    	$data ['totalRecordCount'] = count($data['recordDetails']);
    	
    	$data['holdAmountStatusDetails'] = holdAmountStatusDetails();
    	
    	return view( $this->folderName . 'on-hold-salary-report')->with($data);
    }
    public function filterOnHoldSalaryReport(Request $request){
    	//variable defined
    	$whereData = $likeData = [];
    
    	$page = (! empty($request->post('page')) ? $request->post('page') : 1);
    
    	if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_ON_HOLD_SALARY_REPORT'), session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	//search record
    	if(!empty($request->post('search_employee'))){
    		$whereData['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee'));
    	}
    	
    	if( ( !empty($request->post('search_employment_status') ) )){
    		$whereData['employment_status'] =  $request->post('search_employment_status') ;
    	}
    	//echo '<pre>';print_r($whereData);die;
    	if(!empty($request->post('search_team'))){
    		$whereData['team'] = (int)Wild_tiger::decode($request->post('search_team'));
    	}
    	
    	if(!empty($request->post('search_designation'))){
    		$whereData['designation'] = (int)Wild_tiger::decode($request->post('search_designation'));
    	}
    	
    	if(!empty($request->post('search_joining_from_date'))){
    		$whereData['search_joining_from_date'] = trim($request->post('search_joining_from_date'));
    	}
    	if(!empty($request->post('search_joining_to_date'))){
    		$whereData['search_joining_to_date'] = trim($request->post('search_joining_to_date'));
    	}
    	
    	if(!empty($request->post('search_date_filter'))){
    		switch(trim($request->post('search_date_filter'))){
    			case config('constants.EXPECTED_RELEASE_DATE'):
    				if(!empty($request->post('search_from_month'))){
    					$whereData['search_expected_released_from_date'] = trim($request->post('search_from_month'));
    				}
    				if(!empty($request->post('search_to_month'))){
    					$whereData['search_expected_released_to_date'] = trim($request->post('search_to_month'));
    				}
    				break;
    			case config('constants.RELEASE_DATE'):
    				if(!empty($request->post('search_from_month'))){
    					$whereData['search_released_from_date'] = trim($request->post('search_from_month'));
    				}
    				if(!empty($request->post('search_to_month'))){
    					$whereData['search_released_to_date'] = trim($request->post('search_to_month'));
    				}
    				break;
    		}
    	}
    	
    	if(!empty($request->post('search_hold_amount_status'))){
    		$whereData['hold_amount_status'] = trim($request->post('search_hold_amount_status'));
    	}
    	
    	$data['holdAmountStatusDetails'] = holdAmountStatusDetails();
    	
    	$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
    
    	if ($exportAction == 'export') {
    		$finalExportData = [];
    		$getExportRecordDetails = $this->crudModel->getOnHoldSalaryDetails($whereData, $likeData);
    		if (!empty($getExportRecordDetails)) {
    			$excelIndex = 0;
    			foreach ($getExportRecordDetails as $getExportRecordDetail) {
    				
    				$getHoldAmountInfo = getHoldAmountInfo($getExportRecordDetail);
    				
    				$totalOnHoldSalaryAmount = ( isset($getHoldAmountInfo['totalOnHoldSalaryAmount']) ? $getHoldAmountInfo['totalOnHoldSalaryAmount'] : 0 ) ;
    				$decuctOnHoldSalaryAmount = ( isset($getHoldAmountInfo['deductOnHoldSalaryAmount']) ? $getHoldAmountInfo['deductOnHoldSalaryAmount'] : 0 ) ;
    				$expectedReleasedDate = ( isset($getHoldAmountInfo['expectedReleaseDate']) ? $getHoldAmountInfo['expectedReleaseDate'] : null ) ;
    				$leftAmount = ( isset($getHoldAmountInfo['leftOnHoldSalaryAmount']) ? $getHoldAmountInfo['leftOnHoldSalaryAmount'] : 0 ) ;
    				
    				
    				$rowExcelData = [];
    				$rowExcelData['sr_no'] = ++$excelIndex;
    				$rowExcelData['employee_name'] = ( isset($getExportRecordDetail->v_employee_full_name) ?  ($getExportRecordDetail->v_employee_full_name) :'' );
    				$rowExcelData['employee_code'] = ( isset($getExportRecordDetail->v_employee_code) ?  ($getExportRecordDetail->v_employee_code) :'' );
    				$rowExcelData['contact_number'] = ( isset($getExportRecordDetail->v_contact_no) ?  ($getExportRecordDetail->v_contact_no) :'' );
    				$rowExcelData['team'] = ( ( isset($getExportRecordDetail->teamInfo->v_value) )  ?  $getExportRecordDetail->teamInfo->v_value : '' );
    				$rowExcelData['designation'] = ( ( isset($getExportRecordDetail->designationInfo->v_value) )  ?  $getExportRecordDetail->designationInfo->v_value : '' );
    				$rowExcelData['joining_date'] = ( isset($getExportRecordDetail->dt_joining_date) ?  convertDateFormat($getExportRecordDetail->dt_joining_date,'d.m.Y') :'' );
    				$rowExcelData['planned_hold_amount'] = ( ( isset($getHoldAmountInfo['totalOnHoldSalaryAmount']) )  ?  $getHoldAmountInfo['totalOnHoldSalaryAmount'] : '' );
    				$rowExcelData['deducted_amount'] = ( ( isset($getHoldAmountInfo['deductOnHoldSalaryAmount']) )  ?  $getHoldAmountInfo['deductOnHoldSalaryAmount'] : '' );
    				$rowExcelData['left_amount'] = ( ( isset($getHoldAmountInfo['leftOnHoldSalaryAmount']) )  ?  $getHoldAmountInfo['leftOnHoldSalaryAmount'] : '' );
    				$rowExcelData['expected_finish_month'] = ( ( isset($getHoldAmountInfo['expectedReleaseDate']) )  ?  convertDateFormat(  $getHoldAmountInfo['expectedReleaseDate'] , 'M-Y' ) : '' );
    				$rowExcelData['finish_month'] = ( ( isset($getHoldAmountInfo['releaseDate']) )  ?  convertDateFormat( $getHoldAmountInfo['releaseDate'] , 'M-Y' ) : '' );
    				$rowExcelData['hold_amount_status'] = ( isset($getExportRecordDetail->e_hold_salary_payment_status) ?  ($getExportRecordDetail->e_hold_salary_payment_status) :'' );
    				$finalExportData[] = $rowExcelData;
    					
    			}
    		}
    		 
    		if (!empty($finalExportData)) {
    
    			$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.on-hold-salary-report')]);
    
    			$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.on-hold-salary-report')]);
    			$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
    		} else {
    
    			$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
    		}
    		 
    		return Response::json($response);
    		die;
    	}
    
    	$paginationData = [];
    	
    	$data['recordDetails'] = $this->crudModel->getOnHoldSalaryDetails( $whereData, $likeData );
    
    	$data['totalRecordCount'] = count($data['recordDetails']);
    	
    	$data['pagination'] = $paginationData;
    
    	$data['pageNo'] = $page;
    
    	$data['perPageRecord'] = $this->perPageRecord;
    
    	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/on-hold-salary-report-list' )->with ( $data )->render();
    
    	echo $html;die;
    }
    
    public function updateOnHoldSalaryStatus(Request $request){
    
    	$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
    
    	if( $recordId > 0 ){
    			
    		$updatedStatus = (!empty($request->input('update_status')) ? trim($request->input('update_status')) : null );
    			
    		$recordInfo = EmployeeModel::where('i_id' , $recordId)->first();
    
    		if(!empty($recordInfo)){
    			$oldStatus = null;
    			switch($updatedStatus){
    				case config('constants.NOT_TO_PAY_STATUS'):
    					$oldStatus = config('constants.PENDING_STATUS');
    					if( $recordInfo->e_hold_salary_payment_status != $oldStatus ){
    						$this->ajaxResponse(101, trans('messages.error-invalid-status-info' , [ 'status' =>  $recordInfo->e_hold_salary_payment_status  ] ) );
    					}
    					break;
    				case config('constants.DONATED_STATUS'):
    					$oldStatus = config('constants.NOT_TO_PAY_STATUS');
    					if( $recordInfo->e_hold_salary_payment_status != $oldStatus ){
    						$this->ajaxResponse(101, trans('messages.error-invalid-status-info' , [ 'status' =>  $recordInfo->e_hold_salary_payment_status  ] ) );
    					}
    					break;
    			}
    
    			$updateData = [];
    			$updateData['e_hold_salary_payment_status'] = $updatedStatus;
    
    			$result = false;
    			DB::beginTransaction();
    
    			try{
    					
    				$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateData , [ 'i_id' => $recordId ] );
    					
    				$historyRecord = [];
    				$historyRecord['i_employee_id'] = $recordId;
    				$historyRecord['e_old_status'] = $oldStatus;
    				$historyRecord['e_new_status'] = $updatedStatus;
    					
    				$this->crudModel->insertTableData(config('constants.ON_HOLD_SALARY_STATUS_HISTORY'), $historyRecord );
    					
    				$result = true;
    					
    			}catch(\Exception $e){
    				$result = false;
    				DB::rollback();
    			}
    			if( $result != false ){
    				DB::commit();
    					
    				$recordDetail =
    					
    				$recordInfo = [];
    				$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
    				
    				$onholdSalaryWhere = [];
    				$onholdSalaryWhere['employee_id'] = $recordId;
    				$onholdSalaryWhere['singleRecord'] = true;
    				if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_ON_HOLD_SALARY_REPORT'), session()->get('user_permission')  ) ) ){
    					$onholdSalaryWhere['show_all'] = true;
    				}
    				
    				
    				$recordInfo['recordDetail'] = $this->crudModel->getOnHoldSalaryDetails( $onholdSalaryWhere );
    				$recordInfo['holdAmountStatusDetails'] = holdAmountStatusDetails();
    				$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/single-on-hold-salary-report')->with ( $recordInfo )->render();
    					
    				$this->ajaxResponse(1, trans('messages.success-update' , [ 'module' => trans('messages.on-hold-salary-status') ]) , [ 'html' => $html  ] );
    			} else {
    				DB::rollback();
    				$this->ajaxResponse(101, trans('messages.error-update' , [ 'module' => trans('messages.on-hold-salary-status') ]));
    			}
    
    		}
    
    	}
    	$this->ajaxResponse(101, trans('messages.system-error' ) );
    }
    
    
    public function plannedOnHoldSalaryHistory(Request $request){
    	 
    	$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id'))  : 0 );
    	 
    	if( $recordId > 0 ){
    		$onholdSalaryWhere = [];
    		$onholdSalaryWhere['employee_id'] = $recordId;
    		$onholdSalaryWhere['singleRecord'] = true;
    		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_ON_HOLD_SALARY_REPORT'), session()->get('user_permission')  ) ) ){
    			$onholdSalaryWhere['show_all'] = true;
    		}
    		$recordInfo = $this->crudModel->getOnHoldSalaryDetails( $onholdSalaryWhere );
    		//echo "<pre>";print_r($recordInfo);
    		$html  = "";
    		$srIndex = 1;
    		$totalAmount = 0;
    		if(  (!empty($recordInfo)) && (isset($recordInfo->onHoldSalaryInfo)) && (!empty($recordInfo->onHoldSalaryInfo)) ){
    			foreach($recordInfo->onHoldSalaryInfo as $onHoldSalaryAmount){
    				if ( isset($onHoldSalaryAmount->d_amount) && (!empty($onHoldSalaryAmount->d_amount)) ){
    					$totalAmount += (!empty($onHoldSalaryAmount->d_amount) ? $onHoldSalaryAmount->d_amount : 0 );
    					$html .= '<tr class="text-left">';
    					$html .= '<td class="text-center">'.$srIndex.'</td>';
    					$html .= '<td>';
    					if( isset($onHoldSalaryAmount->dt_month)  && (!empty($onHoldSalaryAmount->dt_month)) ){
    						$html .= convertDateFormat($onHoldSalaryAmount->dt_month, 'M - Y');
    					}
    					$html .= '</td>';
    					$html .= '<td>'.decimalAmount($onHoldSalaryAmount->d_amount).'</td>';
    					$html .= '</tr>';
    					$srIndex++;
    				}
    			}
    		}
    
    		if(!empty($html)){
    			$html .= '<tr>';
    			$html .= '<th colspan="2" class="text-center">'.trans('messages.total').'</th>';
    			$html .= '<td>'.decimalAmount($totalAmount).'</td>';
    			$html .= '</tr>';
    		} else {
    			$html .= '<tr class="text-center">';
    			$html .= '<td colspan="3" >'.trans('messages.no-record-found').'</td>';
    			$html .= '</tr>';
    		}
    		echo $html;die;
    	}
    }
    
    public function deductedOnHoldSalaryHistory(Request $request){
    	
    	$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id'))  : 0 );
    	
    	if( $recordId > 0 ){
    		
    		$onholdSalaryWhere = [];
    		$onholdSalaryWhere['employee_id'] = $recordId;
    		$onholdSalaryWhere['singleRecord'] = true;
    		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_ON_HOLD_SALARY_REPORT'), session()->get('user_permission')  ) ) ){
    			$onholdSalaryWhere['show_all'] = true;
    		}
    		
    		$recordInfo = $this->crudModel->getOnHoldSalaryDetails( $onholdSalaryWhere );
    		$html  = "";
    		$srIndex = 1;
    		$totalAmount = 0;
    		//echo "<pre>";print_r($recordDetail);
    		if(!empty($recordInfo) && isset($recordInfo->generatedSalaryMaster) && (!empty($recordInfo->generatedSalaryMaster)) ){
    			foreach($recordInfo->generatedSalaryMaster as $salaryMaster){
    				if(isset($salaryMaster->generatedSalaryInfo) && (!empty($salaryMaster->generatedSalaryInfo))){
    					foreach($salaryMaster->generatedSalaryInfo as $salaryDetail ){
    						if ( isset($salaryDetail->d_paid_amount) && (!empty($salaryDetail->d_paid_amount)) && ( $salaryDetail->i_component_id == config('constants.ON_HOLD_SALARY_COMPONENT_ID') ) ){
    							$totalAmount += (!empty($salaryDetail->d_paid_amount) ? $salaryDetail->d_paid_amount : 0 );
    							$html .= '<tr class="text-left">';
    							$html .= '<td class="text-center">'.$srIndex.'</td>';
    							$html .= '<td>';
    							if( isset($salaryMaster->dt_salary_month)  && (!empty($salaryMaster->dt_salary_month)) ){
    								$html .= convertDateFormat($salaryMaster->dt_salary_month, 'M - Y');
    							}
    							$html .= '</td>';
    							$html .= '<td>'.decimalAmount($salaryDetail->d_paid_amount).'</td>';
    							$html .= '</tr>';
    							$srIndex++;
    						}
    					}
    				}
    			}
    		}
    		
    		if(!empty($html)){
    			$html .= '<tr>';
				$html .= '<th colspan="2" class="text-center">'.trans('messages.total').'</th>';
				$html .= '<td>'.decimalAmount($totalAmount).'</td>';
                $html .= '</tr>';
    		} else {
    			$html .= '<tr class="text-center">';
    			$html .= '<td colspan="3" >'.trans('messages.no-record-found').'</td>';
    			$html .= '</tr>';
    		}
    		
    		echo $html;die;
    	}
   	}
   	
	public function attendenceReport(){
   		$data = [];
   		$data['pageTitle'] = trans('messages.attendance-report-daily-monthly');
   		$crudModel = new MyAttendanceModel();
   		$whereData = $likeData = [];
   		
   		$page = $this->defaultPage;

   		$allPermissionId = config('permission_constants.ALL_ATTENDANCE_REPORT');
   		$data['allPermissionId'] = $allPermissionId;
   		
   		$paginationData = [];
   		
   		$starDate = date('Y-m-01');
   		$endDate = date('Y-m-t');
   		
   		$whereData['attendance_from_month'] = $starDate;
   		$whereData['attendance_to_month'] = $endDate;
   		
   		$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
   		$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
   		$whereData['employment_status'] = $selectedEmployeeStatus;
   		$whereData['editAttendanceSreen'] = true;
   		
   		if(session()->get('role') != config("constants.ROLE_ADMIN")){
   			$whereData['em.i_id'] = session()->get('user_employee_id');
   		}
   		
   		$data['selectedUserId'] = session()->get('user_employee_id');
   		
   		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
   			$whereData['show_all'] = true;
   		}
   		
   		if($page == $this->defaultPage ){
   		
   			$totalRecords = count($crudModel->getManageAttendanceRecordDetails($whereData));
   		
   			$lastPage = ceil($totalRecords/$this->perPageRecord);
   		
   			$paginationData['current_page'] = $this->defaultPage;
   		
   			$paginationData['per_page'] = $this->perPageRecord;
   		
   			$paginationData ['last_page'] = $lastPage;
   		
   		}
   		
   		$data['pagination'] = $paginationData;
   		$data['pageNo'] = $page;
   		$data['perPageRecord'] = $this->perPageRecord;
   		
   		$whereData['limit'] = $this->perPageRecord;
   		
   		$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
   		
   		$employeeWhere = [];
   		$employeeWhere['employment_status'] = $selectedEmployeeStatus;
   		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
   			$employeeWhere['show_all'] = true;
   		}
   		$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
   		
   		$data['recordDetails'] = $crudModel->getManageAttendanceRecordDetails( $whereData, $likeData );
   		
   		$data['startDate'] = $starDate;
   		$data['endDate'] = $endDate;
   		if (isset($totalRecords)){
   			$data['totalRecordCount'] = $totalRecords;
   		}
   		$data['arrivalDepartureDetails'] = arrivalDepartureList();
   		
   		return view($this->folderName . 'attendance-report')->with($data);
   	}
   	
   	public function attendanceReportFilter(Request $request){
   		if (!empty($request->input())){
   			
   			$crudModel = new MyAttendanceModel();
   			
   			$data = $whereData = $likeData = [];
   			$whereData['editAttendanceSreen'] = true;
   			
   			if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_ATTENDANCE_REPORT'), session()->get('user_permission')  ) ) ){
   				$whereData['show_all'] = true;
   			}
   			
   			if( ( !empty($request->post('search_employment_status') ) )){
   				$whereData['employment_status'] =  $request->post('search_employment_status') ;
   			}
   			if (!empty($request->input('search_employee_name'))){
   				$whereData['employee_id'] = (int)Wild_tiger::decode($request->input('search_employee_name'));
   			}
   			if (!empty($request->input('search_from_date'))){
   				$whereData['attendance_from_month'] = dbDate($request->input('search_from_date'));
   			}
   			if (!empty($request->input('search_to_date'))){
   				$whereData['attendance_to_month'] = dbDate($request->input('search_to_date'));
   			}
   			if (!empty($request->input('search_team'))){
   				$whereData['team'] = (int)Wild_tiger::decode($request->input('search_team'));
   			}
   			
   			if (!empty($request->input('search_attendance_status'))){
   				$whereData['status'] = trim($request->input('search_attendance_status'));
   			}
   			
   			if (!empty($request->input('search_arrival_status'))){
   				$whereData['arrival_status'] = trim($request->input('search_arrival_status'));
   			}
   			
   			if (!empty($request->input('search_departure_status'))){
   				$whereData['departure_status'] = trim($request->input('search_departure_status'));
   			}
   			
   			if (!empty($request->input('search_break_time'))){
   				$whereData['break_time'] = trim($request->input('search_break_time'));
   			}
   			
   			
   			
   			$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
   			if ($exportAction == config('constants.EXCEL_EXPORT')) {

   				$finalExportData = [];
   					
   				$getExportRecordDetails = $crudModel->getManageAttendanceRecordDetails($whereData, $likeData);
   				
   				if (!empty($getExportRecordDetails)) {
   					$excelIndex = 0;
   					foreach ($getExportRecordDetails as $getExportRecordDetail) {
   						$totalHours = "";
   						if( (!empty($getExportRecordDetail->t_start_time)) && (!empty($getExportRecordDetail->t_end_time)) && ( $getExportRecordDetail->t_start_time != config('constants.TIME_DEFAULT_VALUE') ) && ( $getExportRecordDetail->t_end_time != config('constants.TIME_DEFAULT_VALUE')  ) ){
   							$totalHours = diffBetweenTime( $getExportRecordDetail->t_start_time , $getExportRecordDetail->t_end_time );
   						}
   						
   						$arrivalDepartureInfo = displayOnTime($getExportRecordDetail);
   						$arrivalText = ( isset($arrivalDepartureInfo['arrivalInfo']) ? $arrivalDepartureInfo['arrivalInfo'] : '' ) ;
   						$departureText = ( isset($arrivalDepartureInfo['departureInfo']) ? $arrivalDepartureInfo['departureInfo'] : '' )  ;
   						//$totalHours = (!empty($getExportRecordDetail->t_start_time) && !empty($getExportRecordDetail->t_end_time) ? ( strtotime($getExportRecordDetail->t_start_time) - strtotime($getExportRecordDetail->t_end_time) ) : '');
   						$workingHours = (!empty(workingHoursByTotalAndBreakTime($getExportRecordDetail)) ? workingHoursByTotalAndBreakTime($getExportRecordDetail) : '');
   						$rowExcelData = [];
   						$rowExcelData['sr_no'] = ++$excelIndex;
   						$rowExcelData['date'] = (!empty($getExportRecordDetail->dt_date) ? date('d.m.Y' , strtotime($getExportRecordDetail->dt_date)) : '') ;
   						$rowExcelData['day'] = (!empty($getExportRecordDetail->dt_date) ? date('l' , strtotime($getExportRecordDetail->dt_date)) : '');
   						$rowExcelData['employee_name'] = (!empty($getExportRecordDetail->v_employee_full_name) ? $getExportRecordDetail->v_employee_full_name : '');
   						$rowExcelData['employee_code'] = (!empty($getExportRecordDetail->v_employee_code) ? $getExportRecordDetail->v_employee_code : '');
   						$rowExcelData['team'] = (isset($getExportRecordDetail->team) ? $getExportRecordDetail->team : '');
   						$rowExcelData['shift'] = (!empty($getExportRecordDetail->t_original_start_time) && ( $getExportRecordDetail->t_original_start_time != config('constants.TIME_DEFAULT_VALUE')  ) ? clientTime ( $getExportRecordDetail->t_original_start_time )  . (!empty($getExportRecordDetail->t_original_end_time) && ( $getExportRecordDetail->t_original_end_time != config('constants.TIME_DEFAULT_VALUE')  ) ? ' - ' . clientTime ( $getExportRecordDetail->t_original_end_time )    : '')     : '') ;
   						$rowExcelData['in_time'] = (!empty($getExportRecordDetail->t_start_time) && ( $getExportRecordDetail->t_start_time != config('constants.TIME_DEFAULT_VALUE')) ? clientTime($getExportRecordDetail->t_start_time ) : '');
   						$rowExcelData['arrival'] = ( ( (!empty($arrivalText)) && (!empty($rowExcelData['in_time'])) ) ? $arrivalText : '' );;
   						$rowExcelData['out_time'] = (!empty($getExportRecordDetail->t_end_time) && ( $getExportRecordDetail->t_end_time != config('constants.TIME_DEFAULT_VALUE')) ? clientTime($getExportRecordDetail->t_end_time ): '');
   						$rowExcelData['departure'] = ( ( (!empty($departureText)) && (!empty($rowExcelData['out_time'])) ) ? $departureText : '' );
   						$rowExcelData['total_hours'] = $totalHours;
   						$rowExcelData['break_time'] = ( ( (!empty($getExportRecordDetail->t_total_break_time)) && ( $getExportRecordDetail->t_total_break_time != config('constants.TIME_DEFAULT_VALUE') ) ) ? convertSecondIntoHourMinute( strtotime ( $getExportRecordDetail->t_total_break_time )  - strtotime('TODAY') ) : '');
   						$rowExcelData['working_hours'] = $workingHours;
   						$rowExcelData['status'] = (isset($getExportRecordDetail->e_status) ? $getExportRecordDetail->e_status : '');;
   						
   						$finalExportData[] = $rowExcelData;
   					}
   				}
   				
   				if (!empty($finalExportData)) {
   				
   					$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.attendance-report-daily-monthly') ]);
   				
   					$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.attendance-report-daily-monthly')]);
   					$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
   				} else {
   				
   					$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
   				}
   				
   				return Response::json($response);
   				die;
   			}
   			
   			$page =  (!empty($request->input('page')) ? $request->input('page') : 1);
   				
   			$paginationData = [];
   				
   			if($page == $this->defaultPage ){
   					
   				$totalRecords = count($crudModel->getManageAttendanceRecordDetails($whereData , $likeData));
   					
   				$lastPage = ceil($totalRecords/$this->perPageRecord);
   					
   				$paginationData['current_page'] = $this->defaultPage;
   					
   				$paginationData['per_page'] = $this->perPageRecord;
   					
   				$paginationData ['last_page'] = $lastPage;
   					
   			}
   				
   			$data['pagination'] = $paginationData;
   			$data['pageNo'] = $page;
   			$data['perPageRecord'] = $this->perPageRecord;
   				
   			if ($page == $this->defaultPage) {
   				$whereData['offset'] = 0;
   				$whereData['limit'] = $this->perPageRecord;
   			} else if ($page > $this->defaultPage) {
   				$whereData['offset'] = ($page - 1) * $this->perPageRecord;
   				$whereData['limit'] = $this->perPageRecord;
   			}
   			
   			$data['recordDetails'] = $crudModel->getManageAttendanceRecordDetails( $whereData, $likeData );
   			//echo $crudModel->last_query();die;
	   		if (isset($totalRecords)){
	   			$data['totalRecordCount'] = $totalRecords;
	   		}
   			
   			$html = view(config('constants.AJAX_VIEW_FOLDER') . 'report/attendance-report-list')->with($data)->render();
   			echo $html;die;
   		}
   	}
   	
   	public function salaryIncrementReport(){
   	
   		$data = $whereData = [];
   	
   		$data['pageTitle'] = trans('messages.salary-increment-report');
   		
   		$allPermissionId = config('permission_constants.ALL_INCREMENT_SALARY_REPORT');
   		$data['allPermissionId'] = $allPermissionId;
   		
   		$data['employeeDetails'] = EmployeeModel::where('t_is_deleted',0)->orderBy('v_employee_full_name', 'ASC')->get();
   	
   		$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
   		
   		$data['designationDetails'] = LookupMaster::where('v_module_name' , config('constants.DESIGNATION_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value' , 'asc')->get();
   	
   		$page = $this->defaultPage;
   		
   		$selectedYear = date('Y');
   		
   		$incrementHeaders = salaryIncrementReportHeader( $selectedYear );
   		
   		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
   			$whereData['show_all'] = true;
   		}
   		
   		$startDate = min($incrementHeaders);
   		$endDate = max($incrementHeaders);
   		
   		$whereData['salary_start_month'] = $startDate;
   		$whereData['salary_end_month'] = $endDate;
   		$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
   		$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
   		$whereData['employment_status'] = $selectedEmployeeStatus;
   		#get pagination data for first page
   		if($page == $this->defaultPage ){
   		
   			$totalRecords = count($this->crudModel->incrementReportDetails($whereData));
   		
   			$lastPage = ceil($totalRecords/$this->perPageRecord);
   		
   			$paginationData['current_page'] = $this->defaultPage;
   		
   			$paginationData['per_page'] = $this->perPageRecord;
   		
   			$paginationData ['last_page'] = $lastPage;
   		
   		}
   		$whereData ['limit'] = $this->perPageRecord;
   		
   		$data['recordDetails'] = $this->crudModel->incrementReportDetails( $whereData );
   		
   		$data['requestPageNo'] = 1;
   	
   		$data['totalRecordCount'] = ( isset($totalRecords) ? ($totalRecords) : 0 ) ;
   		
   		$data['pagination'] = $paginationData;
   		
   		$data['pageNo'] = $page;
   		
   		$data['incrementHeaders'] = $incrementHeaders;
   		
   		$data['perPageRecord'] = $this->perPageRecord;
   		
   		$data['yearDetails'] = yearDetails();
   		
   		$data['selectedYear'] = $selectedYear;
   		
   		$employeeWhere = [];
   		$employeeWhere['employment_status'] = $selectedEmployeeStatus;
   		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
   			$employeeWhere['show_all'] = true;
   		}
   		$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
   		
   		return view( $this->folderName . 'salary-increment-report')->with($data);
   	}
   	public function filterSalaryIncrementReport(Request $request){
   		 
   		if(!empty($request->post())){
   	
   			$whereData = [];
   			
   			if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_INCREMENT_SALARY_REPORT'), session()->get('user_permission')  ) ) ){
   				$whereData['show_all'] = true;
   			}
   			
   			$selectedYear = (!empty($request->input('search_year')) ? $request->input('search_year') : date('Y') );
   			
   			$incrementHeaders = salaryIncrementReportHeader( $selectedYear );
   			//echo "<pre>";print_r($incrementHeaders);die;
   			$page = (! empty($request->post('page')) ? $request->post('page') : 1);
   			 
   			$whereData['order_by'] = [  'v_employee_full_name' => 'asc' ];
   			$whereData['t_is_deleted'] = 0;
   			 
   			if(!empty($request->post('search_team'))){
   				$whereData['team'] = (int)Wild_tiger::decode($request->post('search_team'));
   			}
   			 
   			if( ( !empty($request->post('search_employment_status') ) )){
   				$whereData['employment_status'] =  $request->post('search_employment_status') ;
   			}
   			
   			if(!empty($request->post('search_employee'))){
   				$whereData['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee'));
   			}
   			
   			if(!empty($request->post('search_designation'))){
   				$whereData['designation'] = (int)Wild_tiger::decode($request->post('search_designation'));
   			}
   			
   			$startDate = min($incrementHeaders);
   			$endDate = max($incrementHeaders);
   			
   			$whereData['salary_start_month'] = $startDate;
   			$whereData['salary_end_month'] = $endDate;
   			
   			$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
   			
   			if ($exportAction == 'export') {
   				$finalData = [];
   				$getExportRecordDetails = $this->crudModel->incrementReportDetails( $whereData );
   				if(!empty($getExportRecordDetails)){
   					$exportIndex = 0;
   					foreach($getExportRecordDetails as $getExportRecordDetail){
   						$rowExcelData = [];
   						$rowExcelData['sr_no'] = ++$exportIndex;
   						$rowExcelData['employee_name'] = $getExportRecordDetail->v_employee_full_name;
   						$rowExcelData['employee_code'] = $getExportRecordDetail->v_employee_code;
   						$rowExcelData['team'] = ( isset($getExportRecordDetail->teamInfo->v_value) ? $getExportRecordDetail->teamInfo->v_value : '' );
   						$rowExcelData['designation'] = ( isset($getExportRecordDetail->designationInfo->v_value) ? $getExportRecordDetail->designationInfo->v_value : '' );
   						$rowExcelData['joining_date'] = (!empty($getExportRecordDetail->dt_joining_date) ? convertDateFormat($getExportRecordDetail->dt_joining_date , 'd.m.Y') : '' ) ;
   						
   						$salaryIncrementDetails = salaryIncrementReportInfo($incrementHeaders , $getExportRecordDetail);
   						$salaryIncrementInfo = ( isset($salaryIncrementDetails['export']) ? $salaryIncrementDetails['export'] : [] ); 
   						//echo "<pre>";print_r($salaryIncrementDetails);
   						//echo "<pre>";print_r($incrementHeaders);
   						
   						if(!empty($incrementHeaders)){
   							foreach($incrementHeaders as $incrementHeader){
   								$rowExcelData[convertDateFormat($incrementHeader,  'F-Y')] = ( isset($salaryIncrementInfo[$incrementHeader]) ? $salaryIncrementInfo[$incrementHeader] : '' );
   							}
   						}
   						$finalData[] = $rowExcelData;
   			
   					}
   				}
   				//echo "<pre>";print_r($finalData);die;
   				if (!empty($finalData)) {
   			
   					$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.salary-increment-report')]);
   			
   					$xlsData = $this->generateSpreadsheet(['record_detail' => $finalData, 'title' => trans('messages.salary-increment-report')]);
   					$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
   						
   				} else {
   			
   					$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
   				}
   			
   				return Response::json($response);
   				die;
   			}
   			
   			$paginationData = [];
   			
   			if ($page == $this->defaultPage) {
   	
   				$totalRecords = $this->crudModel->incrementReportDetails( $whereData );
   	
   				$lastpage = ceil( count($totalRecords) / $this->perPageRecord);
   	
   				$paginationData['current_page'] = $this->defaultPage;
   	
   				$paginationData['per_page'] = $this->perPageRecord;
   	
   				$paginationData['last_page'] = $lastpage;
   			}
   			 
   			 
   			if ($page == $this->defaultPage) {
   				$offset = 0;
   				$limit  = $this->perPageRecord;
   				$whereData['limit'] = $limit;
   				$whereData['offset'] = $offset;
   			} else if ($page > $this->defaultPage) {
   				$offset = ($page - 1) * $this->perPageRecord;
   				$limit = $this->perPageRecord;
   				$whereData['limit'] = $limit;
   				$whereData['offset'] = $offset;
   			}
   			 
   			$recordDetails = $this->crudModel->incrementReportDetails( $whereData );
   			 
   			$data = [];
   			$data['recordDetails'] = $recordDetails;
   			$data['incrementHeaders'] = $incrementHeaders;
   			$data['pagination'] = $paginationData;
   			if ($page == $this->defaultPage) {
   				$data['totalRecordCount'] = ( isset($totalRecords) ? count($totalRecords) : 0 ) ;
   			}
   			$data['requestPageNo'] = $page;
   			$data['pageNo'] = $page;
   			$data['perPageRecord'] = $this->perPageRecord;
   			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/salary-increment-report-list' )->with ( $data )->render();
   			echo $html;die;
   		}
   	}
   	
   	public function punchReport(){
   	
   		/* if( ( session()->get('is_supervisor') == false )  &&  ( session()->get('role') != config('constants.ROLE_ADMIN') )  ){
   		 return redirect(config('constants.DASHBORD_MASTER_URL'));
   		 } */
   	
   		$data = $where = [];
   		$data['pageTitle'] = trans('messages.punch-report-live');
   		 
   		$allPermissionId = config('permission_constants.ALL_PUNCH_REPORT');
   		$data['allPermissionId'] = $allPermissionId;
   		
   		$page = $this->defaultPage;
   	
   		$data['startDate'] = date('Y-m-d' );
   		$data['endDate'] =  date('Y-m-d' );
   		 
   		#store pagination data array
   		$whereData = $paginationData = [];
   		$whereData['search_start_date'] = $data['startDate'];
   		$whereData['search_end_date'] = $data['endDate'];
   		 
   		$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
   		$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
   		$whereData['employment_status'] = $selectedEmployeeStatus;
   		
   		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
   			$whereData['show_all'] = true;
   		}
   		
   		#get pagination data for first page
   		if($page == $this->defaultPage ){
   			 
   			$totalRecords = count($this->crudModel->getPunchReportDetails($whereData));
   			 
   			$lastPage = ceil($totalRecords/$this->perPageRecord);
   			 
   			$paginationData['current_page'] = $this->defaultPage;
   			 
   			$paginationData['per_page'] = $this->perPageRecord;
   			 
   			$paginationData ['last_page'] = $lastPage;
   			 
   		}
   		$whereData ['limit'] = $this->perPageRecord;
   	
   		$data['recordDetails'] = $this->crudModel->getPunchReportDetails( $whereData );
   	
   		$data['pagination'] = $paginationData;
   	
   		$data['pageNo'] = $page;
   	
   		$data['perPageRecord'] = $this->perPageRecord;
   	
   		$data['totalRecordCount'] = $totalRecords;
   	
   		$employeeWhere = [];
   		$employeeWhere['employment_status'] = $selectedEmployeeStatus;
   		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
   			$employeeWhere['show_all'] = true;
   		}
   		$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
   		$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
   		return view( $this->folderName . 'punch-report')->with($data);
   	}
   	public function filterPunchReport(Request $request){
   		//variable defined
   		$whereData = $likeData = [];
   	
   		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_PUNCH_REPORT'), session()->get('user_permission')  ) ) ){
   			$whereData['show_all'] = true;
   		}
   		
   		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
   		
   	
   		if( ( !empty($request->post('search_employment_status') ) )){
   			$whereData['employment_status'] =  $request->post('search_employment_status') ;
   		}
   		
   		//search record
   		if(!empty($request->post('search_employee'))){
   			$whereData['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee'));
   		}
   		
   		if(!empty($request->post('search_team'))){
   			$whereData['team'] = (int)Wild_tiger::decode($request->post('search_team'));
   		}
   		
   		if(!empty($request->post('search_start_date'))){
   			$whereData['search_start_date'] = ($request->post('search_start_date'));
   		}
   		if(!empty($request->post('search_end_date'))){
   			$whereData['search_end_date'] = ($request->post('search_end_date'));
   		}
   		 
   		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
   	
   		if ($exportAction == 'export') {
   			$finalExportData = [];
   			$getExportRecordDetails = $this->crudModel->getPunchReportDetails($whereData, $likeData);
   			if (!empty($getExportRecordDetails)) {
   				$excelIndex = 0;
   				foreach ($getExportRecordDetails as $getExportRecordDetail) {
   						
   					$rowExcelData = [];
   					$rowExcelData['sr_no'] = ++$excelIndex;
   					$rowExcelData['date'] = ( isset($getExportRecordDetail->dt_entry_date_time) ?  convertDateFormat($getExportRecordDetail->dt_entry_date_time,'d.m.Y') :'' );
   					$rowExcelData['employee_name'] = ( isset($getExportRecordDetail->punchEmployee->v_employee_full_name) ?  ($getExportRecordDetail->punchEmployee->v_employee_full_name) :'' );
   					$rowExcelData['employee_code'] = ( isset($getExportRecordDetail->punchEmployee->v_employee_code) ?  ($getExportRecordDetail->punchEmployee->v_employee_code) :'' );
   					$rowExcelData['contact_number'] = ( isset($getExportRecordDetail->punchEmployee->v_contact_no) ?  ($getExportRecordDetail->punchEmployee->v_contact_no) :'' );
   					$rowExcelData['team'] = ( ( isset($getExportRecordDetail->punchEmployee->teamInfo->v_value) )  ? $getExportRecordDetail->punchEmployee->teamInfo->v_value  : '' );
   					//$rowExcelData['punch_time'] = "";
   					$rowExcelData['time_stamp'] = ( isset($getExportRecordDetail->dt_entry_date_time) ?  convertDateFormat($getExportRecordDetail->dt_entry_date_time,'h:i A') :'' );
   					$finalExportData[] = $rowExcelData;
   				}
   			}
   			 
   			if (!empty($finalExportData)) {
   	
   				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.punch-report-live')]);
   	
   				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.punch-report-live')]);
   				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
   			} else {
   	
   				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
   			}
   			 
   			return Response::json($response);
   			die;
   		}
   	
   		$paginationData = [];
   	
   		if ($page == $this->defaultPage) {
   			 
   			$totalRecords = count($this->crudModel->getPunchReportDetails( $whereData , $likeData ));
   			 
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
   	
   		$data['recordDetails'] = $this->crudModel->getPunchReportDetails( $whereData, $likeData );
   	
   		if(isset($totalRecords)){
   			$data ['totalRecordCount'] = $totalRecords;
   		}
   		$data['pagination'] = $paginationData;
   	
   		$data['pageNo'] = $page;
   	
   		$data['perPageRecord'] = $this->perPageRecord;
   	
   		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/punch-report-list' )->with ( $data )->render();
   	
   		echo $html;die;
   	}
   	
   	public function getDocumentTypes(Request $request) {
   		if(!empty($request->input())) {
   			$documentFolderId = (int) Wild_tiger::decode($request->input('document_folder_id'));
   	
   			$whereData = $documentTypeDetails = [];
   	
   			if($documentFolderId > 0) {
   				$whereData['i_document_folder_id'] = $documentFolderId;
   			}
   			$whereData['order_by'] = ['v_document_type' => 'asc'];
   	
   			$documentTypeDetails = $this->crudModel->selectData(config('constants.DOCUMENT_TYPE_MASTER_TABLE') , ['i_id' , 'v_document_type'] , $whereData);
   	
   			$html = "<option value=''>" . trans('messages.select') . "</option>";
   			if(!empty($documentTypeDetails)) {
   					
   				foreach ( $documentTypeDetails as $documentTypeDetail ) {
   					$encodedId = (!empty($documentTypeDetail->i_id) ? Wild_tiger::encode($documentTypeDetail->i_id) : '' );
   	
   					$html .= "<option value='". $encodedId ."'>";
   					$html .= (!empty($documentTypeDetail->v_document_type) ? $documentTypeDetail->v_document_type : '');
   					$html .= "</option>";
   				}
   			}
   			echo $html;die;
   		}
   	}
   	
   	public function missingPunch(){
   		
   		$data = [];
   		$data['pageTitle'] = trans('messages.missing-punch');
   	
   		$page = $this->defaultPage;
   	
   		$data['startDate'] = date('Y-m-d' );
   		$data['endDate'] =  date('Y-m-d' );
   	
   		$allPermissionId = config('permission_constants.ALL_MISSING_PUNCH_REPORT');
   		$data['allPermissionId'] = $allPermissionId;
   		
   		$whereData = $paginationData = [];
   		$whereData['search_start_date'] = $data['startDate'];
   		$whereData['search_end_date'] = $data['endDate'];
   	
   		$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
   		$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
   		$whereData['employment_status'] = $selectedEmployeeStatus;
   	
   		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
   			$whereData['show_all'] = true;
   		}
   		
   		if($page == $this->defaultPage ){
   	
   			$totalRecords = count($this->crudModel->getMissingPunchDetails($whereData));
   	
   			$lastPage = ceil($totalRecords/$this->perPageRecord);
   	
   			$paginationData['current_page'] = $this->defaultPage;
   	
   			$paginationData['per_page'] = $this->perPageRecord;
   	
   			$paginationData ['last_page'] = $lastPage;
   	
   		}
   		$whereData ['limit'] = $this->perPageRecord;
   	
   		$data['recordDetails'] = $this->crudModel->getMissingPunchDetails( $whereData );
   	
   		$data['pagination'] = $paginationData;
   	
   		$data['pageNo'] = $page;
   	
   		$data['perPageRecord'] = $this->perPageRecord;
   	
   		$data['totalRecordCount'] = $totalRecords;
   	
   		$employeeWhere = [];
   		$employeeWhere['employment_status'] = $selectedEmployeeStatus;
   		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
   			$employeeWhere['show_all'] = true;
   		}
   		$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
   	
   		$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
   	
   		return view( $this->folderName . 'missing-punch' , $data );
   	}
   	
   	public function filterMissingPunch(Request $request) {
   	
   		$whereData = $likeData = [];
   	
   		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
   		
   		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_MISSING_PUNCH_REPORT'), session()->get('user_permission')  ) ) ){
   			$whereData['show_all'] = true;
   		}
   		
   	
   		if( ( !empty($request->post('search_employment_status') ) )){
   			$whereData['employment_status'] =  $request->post('search_employment_status');
   		}
   	
   		if(!empty($request->post('search_employee'))){
   			$whereData['employee_id'] = (int)Wild_tiger::decode($request->post('search_employee'));
   		}
   			
   		if(!empty($request->post('search_team'))){
   			$whereData['team'] = (int)Wild_tiger::decode($request->post('search_team'));
   		}
   			
   		if(!empty($request->post('search_start_date'))){
   			$whereData['search_start_date'] = ($request->post('search_start_date'));
   		}
   		if(!empty($request->post('search_end_date'))){
   			$whereData['search_end_date'] = ($request->post('search_end_date'));
   		}
   	
   		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
   	
   		if ($exportAction == 'export') {
   			$finalExportData = [];
   			$getExportRecordDetails = $this->crudModel->getMissingPunchDetails($whereData, $likeData);
   			if (!empty($getExportRecordDetails)) {
   				$excelIndex = 0;
   				foreach ($getExportRecordDetails as $getExportRecordDetail) {
   	
   					$rowExcelData = [];
   					$rowExcelData['sr_no'] = ++$excelIndex;
   					$rowExcelData['date'] = ( isset($getExportRecordDetail->action_date) ?  convertDateFormat($getExportRecordDetail->action_date,'d.m.Y') :'' );
   					$rowExcelData['employee_name'] = ( isset($getExportRecordDetail->punchEmployee->v_employee_full_name) ?  ($getExportRecordDetail->punchEmployee->v_employee_full_name) :'' );
   					$rowExcelData['employee_code'] = ( isset($getExportRecordDetail->punchEmployee->v_employee_code) ?  ($getExportRecordDetail->punchEmployee->v_employee_code) :'' );
   					$rowExcelData['contact_number'] = ( isset($getExportRecordDetail->punchEmployee->v_contact_no) ?  ($getExportRecordDetail->punchEmployee->v_contact_no) :'' );
   					$rowExcelData['team'] = ( ( isset($getExportRecordDetail->punchEmployee->teamInfo->v_value) )  ? $getExportRecordDetail->punchEmployee->teamInfo->v_value  : '' );
   					$rowExcelData['time_stamp'] = ( isset($getExportRecordDetail->time) ?  convertDateFormat($getExportRecordDetail->time,'h:i A') :'' );
   					$finalExportData[] = $rowExcelData;
   				}
   			}
   	
   			if (!empty($finalExportData)) {
   	
   				$fileName = trans('messages.export-module-file-name', ['moduleName' => trans('messages.missing-punch')]);
   	
   				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.missing-punch')]);
   				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
   			} else {
   	
   				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
   			}
   	
   			return Response::json($response);
   			die;
   		}
   	
   		$paginationData = [];
   	
   		if ($page == $this->defaultPage) {
   	
   			$totalRecords = count($this->crudModel->getMissingPunchDetails( $whereData , $likeData ));
   	
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
   	
   		$data['recordDetails'] = $this->crudModel->getMissingPunchDetails( $whereData, $likeData );
   	
   		if(isset($totalRecords)){
   			$data ['totalRecordCount'] = $totalRecords;
   		}
   		$data['pagination'] = $paginationData;
   	
   		$data['pageNo'] = $page;
   	
   		$data['perPageRecord'] = $this->perPageRecord;
   	
   		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/missing-punch-list' )->with ( $data )->render();
   	
   		echo $html;die;
   	
   	}
}