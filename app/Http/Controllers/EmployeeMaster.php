<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\EmployeeModel;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\Session;
use DB;
use App\LookupMaster;
use App\Rules\UniqueEmployeeCode;
use App\CityMasterModel;
use App\StateMasterModel;
use App\ProbationPolicyMasterModel;
use Illuminate\Support\Facades\Response;
use App\CountryMasterModel;
use App\DocumentFolderModel;
use App\EmployeeDocumentModel;
use App\SalaryGroupModel;
use App\SalaryComponentsModel;
use App\SalaryGroupDetailsModel;
use App\Models\EmployeeDataUpdateRequest;
use App\ShiftMasterModel;
use App\Models\EmployeeSalaryDetailModel;
use App\Models\EmployeeDesignationHistory;
use Symfony\Component\HttpKernel\DataCollector\AjaxDataCollector;
use App\Models\EmployeeResignHistory;
use App\WeeklyOffMasterModel;
use App\Models\VillageMasterModel;
use App\SubDesignationMasterModel;
use App\Rules\UniqueSuspendDate;
use App\LeaveTypeMasterModel;
use App\Models\LeaveBalanceModel;
use App\Rules\UniquePersonalEmailId;
use App\DocumentTypeModel;
use App\Models\SuspendHistory;
use function GuzzleHttp\json_encode;
use App\Models\Report;
use App\Models\ReviseSalaryMaster;

class EmployeeMaster extends MasterController{
 	public function __construct(){
    	parent::__construct();
    	
    	$this->crudModel =  new EmployeeModel();
    	$this->moduleName = trans('messages.employee');
    	$this->perPageRecord = config('constants.PER_PAGE');
    	$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
    	$this->tableName = config('constants.EMPLOYEE_MASTER_TABLE');
    	$this->folderName = config('constants.ADMIN_FOLDER'). 'employee-master/' ;
    	$this->documentFolder = 'document/' ;
    	$this->redirectUrl = config('constants.EMPLOYEE_MASTER_URL');
    	$this->documentTypeModel =  new DocumentFolderModel();
    	$this->employeeDocumentTypeModel = new EmployeeDocumentModel();
    	$this->resignationReportModel =  new Report();
    	
    }
    
    public function index(){
    	
    	//$ddd = $this->crudModel->getRecordDetails();
    	//echo "<pre>";print_r($ddd);die;
    	/* if( ( checkPermission('view_employee_list') == false ) && ( session()->has('is_supervisor') == false ) ){
    		if( in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) ){
    			return redirect(config('constants.DASHBORD_MASTER_URL'));
    		}
    	} */
    	
    	if( session()->has('is_supervisor') == false){
    		if( checkPermission('view_employee_list') != true ){
    			return redirect(config('constants.DASHBORD_MASTER_URL'));    			
    		} 
    	} else {
    		if( in_array( session()->get('role') , [ config('constants.ROLE_HR_TEAM') ] ) ){
    			if( checkPermission('view_employee_list') != true ){
    				return redirect(config('constants.DASHBORD_MASTER_URL'));
    			}
    		}
    	} 
    	
    	$data = $whereData = [];
    	$data['pageTitle'] = trans('messages.employee-list');
    	$page = $this->defaultPage;
    	$data['genderRecordDetails'] = genderMaster();
    	$data['bloodGroupRecordDetails'] = bloodGroupMaster();
    	$data['designationRecordDetails'] = LookupMaster::where('v_module_name',config('constants.DESIGNATION_LOOKUP'))->orderBy('v_value', 'ASC')->get();
    	$data['teamRecordDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->orderBy('v_value', 'ASC')->get();
    	$data['recruitmentSourceDetails'] = LookupMaster::where('v_module_name',config('constants.RECRUITMENT_SOURCE_LOOKUP'))->orderBy('v_value', 'ASC')->get();
    	$data['employmentStatusInfo'] = employmentStatusMaster();
    	$data['referenceEmployeeRecords'] = EmployeeModel::orderBy('v_employee_full_name', 'ASC')->get();
    	
    	$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
    	$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
    	
    	$selectedStatus = (!empty(session()->get('selected_employee_status')) ? session()->get('selected_employee_status') : null );
    	
    	if(!empty($selectedStatus)){
    		$data['selectedEmployeeStatus'] = $selectedStatus;
    	}
    	
    	if(!empty($selectedStatus)){
    		$whereData['e_employment_status'] = [ $selectedStatus ];
    	}
    	
    	$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
    	$data['allPermissionId'] = $allPermissionId;
    	
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$whereData['show_all'] = true;
    	}
    	
    	$data['leaderDetails'] = EmployeeModel::orderBy('v_employee_full_name', 'ASC')->get();
    	$data['shifyDetails'] = ShiftMasterModel::orderBy('v_shift_name', 'ASC')->get();
    	$data['weekOffDetails'] = WeeklyOffMasterModel::orderBy('v_weekly_off_name', 'ASC')->get();
    	$data['recordDetails'] = $this->crudModel->getRecordDetails( $whereData );
    	//echo "<pre>";print_r(session()->all());die;
    	
    	return view( $this->folderName . 'employee-master')->with($data);
    }
    
	public function create(){
    	$data = [] ;
    	$data['pageTitle'] = trans('messages.add-employee');
    	$data['genderRecordDetails'] = genderMaster();
    	$data['bloodGroupRecordDetails'] = bloodGroupMaster();
    	$data['cityRecordDetails'] = CityMasterModel::with(['stateMaster' , 'stateMaster.countryMaster'])->where('t_is_active',1)->orderBy('v_city_name', 'ASC')->get();
    	$data['stateRecordDetails'] = StateMasterModel::where('t_is_active',1)->orderBy('v_state_name', 'ASC')->get();
    	$data['bankRecordDetails'] = LookupMaster::where('v_module_name',config ( 'constants.BANK_LOOKUP'))->where('t_is_active',1)->orderBy('v_value', 'ASC')->get();
    	$data['probationPolicyRecordDetails'] = ProbationPolicyMasterModel::where('e_record_status',config ( 'constants.PROBATION_POLICY'))->where('t_is_active',1)->orderBy('v_probation_policy_name', 'ASC')->get();
    	$data['noticePeriodPolicyRecordDetails'] = ProbationPolicyMasterModel::where('e_record_status',config ( 'constants.NOTICE_PERIOD_POLICY'))->where('t_is_active',1)->orderBy('v_probation_policy_name', 'ASC')->get();
    	$data['teamRecordDetails'] = LookupMaster::where('v_module_name',config ( 'constants.TEAM_LOOKUP'))->where('t_is_active',1)->orderBy('v_value', 'ASC')->get();
    	$data['designationRecordDetails'] = LookupMaster::where('v_module_name',config ( 'constants.DESIGNATION_LOOKUP'))->where('t_is_active',1)->orderBy('v_value', 'ASC')->get();
    	$data['recruitmentSourceRecordDetails'] = LookupMaster::where('v_module_name',config ( 'constants.RECRUITMENT_SOURCE_LOOKUP'))->where('t_is_active',1)->orderBy('v_value', 'ASC')->get();
    	$data['leaderRecordDetails'] = EmployeeModel::where('t_is_active',1)->orderBy('v_employee_name', 'ASC')->get();
    	$data['referenceEmployeeRecords'] = EmployeeModel::where('t_is_active',1)->orderBy('v_employee_full_name', 'ASC')->get();
    	$data['countryRecordDetails'] = CountryMasterModel::where('t_is_active',1)->orderBy('v_country_name', 'ASC')->get();
    	$data['salaryGroupRecordDetails'] = SalaryGroupModel::where('t_is_active',1)->orderBy('v_group_name', 'ASC')->get();
    	$data['shifyDetails'] = ShiftMasterModel::where('t_is_active',1)->orderBy('v_shift_name', 'ASC')->get();
    	$data['villageRecordDetails'] = VillageMasterModel::with(['cityMaster.stateMaster.countryMaster'])->where('t_is_active',1)->orderBy('v_village_name', 'ASC')->get();
    	$data['weekOffDetails'] = WeeklyOffMasterModel::where('t_is_active',1)->orderBy('v_weekly_off_name', 'ASC')->get();
    	$data['employeeCode'] =  $this->autoGenerateEmployeeCode(config('constants.SELECTION_YES')); 
    	$data['maritalStatusInfo'] = maritalStatusInfo();
    	
    	return view( $this->folderName . 'add-employee-master')->with($data);
    	
    }
 	public function filter(Request $request){
    	
    	if ($request->ajax ()) {
    		$whereData = $likeData = $additionalData = $data = [ ];
	    	$page = (! empty($request->post('page')) ? $request->post('page') : 1);
	    	$paginationData = [];
	    
	    	if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ){
	    		$whereData['show_all'] = true;
	    	}
	    	
	    	$fieldData = $this->convertFiletrRequest($request);
	    
	    	$searchValue = $fieldData['tableSearch'];
	    	$columnName = $fieldData['sortColumnName'];
	    	$columnSortOrder = $fieldData['columnSortOrder'];
	    	$offset = $fieldData['offset'];
	    	$draw = $fieldData['draw'];
	    	$limit = $fieldData['limit'];
    			
    		if (!empty($request->post('search_by'))) {
    			$searchByName = trim($request->post('search_by'));
    			$likeData ['searchBy'] = $searchByName;
    		}
    		if( ( !empty($request->post('search_gender') ) )){
    			$whereData['gender'] =  ( trim($request->input('search_gender')) == config('constants.GENDER_MALE') ? config('constants.GENDER_MALE') :  config('constants.GENDER_FEMALE') );
    		}
    		if( ( !empty($request->post('search_blood_group') ) ) ){
    			$whereData['blood_group'] = ($request->post('search_blood_group'));
    		} 
    		
    		if( ( !empty($request->post('search_from_date') ) ) ){
    			$whereData['joining_from_date'] = ($request->post('search_from_date'));
    		}
    		if( ( !empty($request->post('search_to_date') ))){
    			$whereData['joining_to_date'] = ($request->post('search_to_date'));
    		}
    		if(!empty($request->post('search_designation'))){
    			$whereData['designation'] = (int)Wild_tiger::decode($request->post('search_designation'));
    		}
    		if(!empty($request->post('search_team'))){
    			$whereData['team_record'] = (int)Wild_tiger::decode($request->post('search_team'));
    		}
    		if(!empty($request->post('search_recruitment_source'))){
    			$whereData['recruitment_source'] = (int)Wild_tiger::decode($request->post('search_recruitment_source'));
    		}
    		
    		if(!empty($request->post('search_reference_name'))){
    			$whereData['reference_name'] = (int)Wild_tiger::decode($request->post('search_reference_name'));
    		}
    		if( ( !empty($request->post('search_login_status') ) )){
    			$whereData['login_status'] =  ( trim($request->input('search_login_status')) == config('constants.ENABLE_STATUS') ? 1 :  0 );
    		}
    		if( ( !empty($request->post('search_employment_status') ) )){
    			$whereData['employment_status'] =  $request->post('search_employment_status') ;
    		}
    		if(!empty($request->post('search_leader_name_reporting_manager'))){
    			$whereData['leader_name'] = (int)Wild_tiger::decode($request->post('search_leader_name_reporting_manager'));
    		}
    		if(!empty($request->post('search_shift'))){
    			$whereData['shift_record'] = (int)Wild_tiger::decode($request->post('search_shift'));
    		}
    		
    		if(!empty($request->post('search_weekly_off'))){
    			$whereData['weekly_off_record'] = (int)Wild_tiger::decode($request->post('search_weekly_off'));
    		}
    		if(!empty($columnName)) {
    			switch($columnName){
    				case 'employee_code':
    					$columnName = 'v_employee_code';
    					break;
    				case 'name':
    					$columnName = 'v_employee_name';
    					break;
    				case 'full_name':
    					$columnName = 'v_employee_full_name';
    					break;
    				case 'gender':
    					$columnName = 'e_gender';
    					break;
    				case 'joining_date':
    					$columnName = 'dt_joining_date';
    					break;
    				case 'designation':
    					$columnName = 'i_designation_id';
    					break;
    				 case 'leader_name':
    					$columnName = 'i_leader_id';
    					break;
    				case 'recruitment_source':
    					$columnName = 'i_recruitment_source_id';
    					break;
    				case 'shift':
    					$columnName = 'i_shift_id';
    					break;
    				case 'contact_number':
    					$columnName = 'v_contact_no';
    					break;
    				case 'employment_status':
    					$columnName = 'e_employment_status';
    					break;
    				
    			}
    			$whereData['order_by'] = [ $columnName =>  ( (!empty($columnSortOrder)) ? $columnSortOrder : 'DESC' ) ];
    		}
    		
    		$totalRecords = count($this->crudModel->getRecordDetails ( $whereData  , $likeData  ));
    			
    		$whereData['offset'] = $offset ;
    			
    		$whereData['limit'] = $limit;
    	
    		$recordDetails = $this->crudModel->getRecordDetails ( $whereData , $likeData );
    		$finalData = [];
    		if(!empty($recordDetails)){
    			$index = $offset;
    			$allSalesRole = [];
    			foreach($recordDetails as $key => $recordDetail){
    				$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
    				$rowData = [];
    				$rowData['sr_no'] = '<span style="text-align:center !important;display:block">'.++$index.'</span>';
    				$rowData['employee_code'] = (isset($recordDetail->v_employee_code) ? '<a href="'.route('employee-master.profile', $encodeRecordId ).'" target="_blank" title="'.trans("messages.view-profile").'">' .($recordDetail->v_employee_code) .'</a>'  : '');
    				$rowData['name'] = (isset($recordDetail->v_employee_name) ? '<a href="'.route('employee-master.profile', $encodeRecordId ).'" target="_blank" title="'.trans("messages.view-profile").'">' . $recordDetail->v_employee_name . '</a>' :'');
    				$rowData['full_name'] = (isset($recordDetail->v_employee_full_name) ? '<a href="'.route('employee-master.profile', $encodeRecordId ).'" target="_blank" title="'.trans("messages.view-profile").'" >' .($recordDetail->v_employee_full_name) .'</a>'  : '');
    				$rowData['gender'] = (isset($recordDetail->e_gender) ? $recordDetail->e_gender .(isset($recordDetail->v_blood_group) ? '<br>'. $recordDetail->v_blood_group : '') :'');
    				$rowData['joining_date'] =(isset($recordDetail->dt_joining_date) ? clientDate($recordDetail->dt_joining_date) :'');
    				$rowData['designation'] = (isset($recordDetail->designationInfo->v_value) ? ($recordDetail->designationInfo->v_value) .(isset($recordDetail->teamInfo->v_value) ? '<br>'. $recordDetail->teamInfo->v_value : ''): '');
    				$rowData['sub_designation'] = (isset($recordDetail->subDesignationInfo->v_sub_designation_name) ? $recordDetail->subDesignationInfo->v_sub_designation_name : '');
    				$rowData['leader_name'] = (isset($recordDetail->leaderInfo->v_employee_full_name) ? ($recordDetail->leaderInfo->v_employee_full_name)  .(!empty($recordDetail->leaderInfo->v_employee_code) ? ' (' .$recordDetail->leaderInfo->v_employee_code .')' : '' ): '');
    				$rowData['recruitment_source'] = (isset($recordDetail->recruitmentSourceInfo->v_value) ? ($recordDetail->recruitmentSourceInfo->v_value) .(isset($recordDetail->employeeInfo->v_employee_full_name) ?  '<br>'.($recordDetail->employeeInfo->v_employee_full_name).(!empty($recordDetail->employeeInfo->v_employee_code) ? ' (' .$recordDetail->employeeInfo->v_employee_code .')' : '' ):''): '');
    				$rowData['shift'] = (isset($recordDetail->shiftInfo->v_shift_name) ? ($recordDetail->shiftInfo->v_shift_name) .(isset($recordDetail->weekOffInfo->v_weekly_off_name) ? '<br>' .($recordDetail->weekOffInfo->v_weekly_off_name) : ''): '');
    				$rowData['contact_number'] = (isset($recordDetail->v_contact_no) ? ($recordDetail->v_contact_no) .(isset($recordDetail->v_outlook_email_id) ? '<br>' .($recordDetail->v_outlook_email_id) : ''): '');
    				$rowData['employment_status'] = (isset($recordDetail->e_employment_status) ? ($recordDetail->e_employment_status) . ( ( $recordDetail->e_employment_status == config('constants.PROBATION_EMPLOYMENT_STATUS') ) ? ( isset($recordDetail->probationPeriodInfo->v_probation_period_duration)  ? '<br>' .  $recordDetail->probationPeriodInfo->v_probation_period_duration . ( isset($recordDetail->probationPeriodInfo->e_months_weeks_days) ? ' '. $recordDetail->probationPeriodInfo->e_months_weeks_days : ''  ) : '' )  : '' ) : '');
    				
    				$disabledStatus = "";
    				if( $recordDetail->e_employment_status == config('constants.RELIEVED_EMPLOYMENT_STATUS')) {
    					$disabledStatus = "disabled";
    				}
    				if(!in_array( session()->get('role') ,  [ config('constants.ROLE_ADMIN') ]  ) ) {
    					if( ( checkPermission('edit_employee_list') != false ) && ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ){
    						
    					} else {
    						$disabledStatus = "disabled";
    					}
    					
    				}
    				
    				$rowData['login_status'] = '';
    				if( $recordDetail->e_employment_status != config('constants.RELIEVED_EMPLOYMENT_STATUS')) {
    					$rowData['login_status'] .= '<div class="custom-control custom-switch login-manage-switch">';
             			$rowData['login_status'] .= '<input type="checkbox" class="custom-control-input enable-disable-status" '.$disabledStatus.' id="disable_'.$index.'" '.( $recordDetail->t_is_active == 1 ? 'checked' : '' ) .'  data-record-id="'.$encodeRecordId.'" data-current-status="'.( $recordDetail->t_is_active == 1 ? config('constants.ACTIVE_STATUS') : config('constants.INACTIVE_STATUS') ) .'" onclick="disableLogin(this);" >';
                  		$rowData['login_status'] .= '<label class="custom-control-label login-manage-text" for="disable_'.$index.'">'.( $recordDetail->t_is_active == 1 ? trans('messages.enable') : trans('messages.disable') ).'</label>';
            			$rowData['login_status'] .= '</div>';
    				}
    				if( ( checkPermission('edit_employee_list') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) )  ) {
	    				$rowData['action'] = '';
						$rowData['action'] .= '<div class="actions-button">';
						$rowData['action'] .= '<a title="'.trans('messages.edit').'" href="'.route('employee-master.profile', $encodeRecordId ).'" class="btn btn-sm mb-1 btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></a>';
						$rowData['action'] .= '</div>';
    				}
    				$finalData[] = $rowData;
    				
    			}
    		}
    		$response = array(
    				"draw" => intval($draw),
    				"iTotalRecords" => count($finalData),
    				"iTotalDisplayRecords" => $totalRecords,
    				"aaData" => $finalData
    		);
    			
    		return Response::json($response);die;
    	}
    	
    } 
   
    public function getEmployeeCodeDetails(Request $request){
    	$data = [] ;
    	$autoCalculateEmployeeCode = (!empty($request->input('auto_calculate_employee_code')) ? $request->input('auto_calculate_employee_code') :null);
    	if( !empty($autoCalculateEmployeeCode) ){
    		$data['recordInfo'] = $this->autoGenerateEmployeeCode($autoCalculateEmployeeCode);
    		$this->ajaxResponse(1, trans('messages.success') , $data );
    	}
     }
    
    // Ajax: return Sub-Designations for a selected Designation as <option> HTML
    public function getSubDesignationsByDesignation(Request $request){
        if ($request->ajax()) {
            $designationId = (!empty($request->post('designation')) ? (int)Wild_tiger::decode($request->post('designation')) : 0);
            $html = '<option value="">'.trans('messages.select').'</option>';
            if ($designationId > 0) {
                $subDesignationModel = new SubDesignationMasterModel();
                $records = $subDesignationModel->getRecordDetails([ 'designation_id' => $designationId, 'active_status' => 1 ]);
                if (!empty($records)) {
                    foreach ($records as $record) {
                        $encodeId = Wild_tiger::encode($record->i_id);
                        $name = (!empty($record->v_sub_designation_name) ? $record->v_sub_designation_name : '');
                        $html .= '<option value="'.$encodeId.'">'.$name.'</option>';
                    }
                }
            }
            return Response::json([ 'status_code' => 1, 'message' => trans('messages.success'), 'html' => $html ]);
        }
    }
     
     private function autoGenerateEmployeeCode( $autoCalculateEmployeeCode ){
     	
     	
     	
     	$whereData = [];
     	$whereData['e_auto_generate_no'] = $autoCalculateEmployeeCode ;
     	$whereData['t_is_deleted !='] = 1 ;
     	$getCountDetails = $this->crudModel->getSingleRecordById(config('constants.EMPLOYEE_MASTER_TABLE') , [  DB::raw('count(i_id) as record_count')  ] , $whereData );
     	$getCountInfo = ( ( (!empty($getCountDetails->record_count)) && ( $getCountDetails->record_count > 0 ) ) ? ( $getCountDetails->record_count + 1  ) : 1 );
     	$getCountInfo = ( config('constants.SYSTEM_DEFAULT_EMPLOYEE_AUTOGENERATE_COUNT') + $getCountInfo ) ;
     	
     	if( $autoCalculateEmployeeCode == config('constants.SELECTION_YES') ){
     		$findUniqueCode = false;
     		while( $findUniqueCode != true ){
     			$checkUniqueCode = $this->crudModel->getSingleRecordById( config('constants.EMPLOYEE_MASTER_TABLE') , [ '*' ] , [ 'v_employee_code' => $getCountInfo ] );
     			if(empty($checkUniqueCode)){
     				$findUniqueCode = true;
     			} else {
     				$getCountInfo++;
     			}
     			
     		}
     	}
     	
     	return $getCountInfo;
     	$data['recordInfo'] = $getCountInfo;
     }
     
     
	public function add(Request $request){
		
		$recordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0);
     	$villageId = (!empty($request->post('current_village')) ? (int)Wild_tiger::decode($request->post('current_village')) : 0);
     	$permanentVillageId = (!empty($request->post('permanent_village')) ? (int)Wild_tiger::decode($request->post('permanent_village')) : 0);
     	$recruitmentSource = (!empty($request->post('recruitment_source')) ? (int)Wild_tiger::decode($request->post('recruitment_source')) :0);
     	
     	$formValidation =[];
     	$formValidation['employee_code'] = ['required' ,new UniqueEmployeeCode($recordId)];
     	$formValidation['employee_name'] = ['required'];
     	$formValidation['full_name'] = ['required'];
     	$formValidation['gender'] = ['required'];
     	$formValidation['date_of_birth'] = ['required'];
     	$formValidation['personal_email_id'] = ['required'];
     	
     	$formValidation['contact_number'] = ['required'];
     	$formValidation['address_line_1'] = ['required'];
     	//$formValidation['current_state'] = ['required'];
     	//$formValidation['current_country'] = ['required'];
     	$formValidation['address_permanent_line_1'] = ['required'];
     	//$formValidation['per_state'] = ['required'];
     	//$formValidation['per_country'] = ['required'];
     	$formValidation['joining_date'] = ['required'];
     	$formValidation['designation'] = ['required'];
     	$formValidation['team'] = ['required'];
     	//$formValidation['leader_name_reporting_manager'] = ['required'];
     	$formValidation['recruitment_source'] = ['required'];
     	$formValidation['notice_period'] = ['required'];
     	$formValidation['assign_salary_employee'] = ['required'];
     	$formValidation['shift'] = ['required'];
     	$formValidation['new_weekly_off'] = ['required'];
     	if($recruitmentSource == config('constants.EMPLOYEE_RECRUITMENT_SOURCE_ID')){
     		$formValidation['reference_name'] = ['required'];
     	} 
     	if (empty($villageId)){
     		$formValidation['current_city'] = ['required'];
     	}
     	if (empty($permanentVillageId)){
     		$formValidation['per_city'] = ['required'];
     	}
     	$validator = Validator::make ( $request->all (), $formValidation , [
     		'employee_code.required' => __ ( 'messages.require-employee-code' ),
     		'employee_name.required' => __ ( 'messages.require-employee-name' ),
     		'full_name.required' => __ ( 'messages.require-full-name' ),
     		'gender.required' => __ ( 'messages.require-select-gender' ),
     		'date_of_birth.required' => __ ( 'messages.require-enter-date-of-birth' ),
     		'personal_email_id.required' => __ ( 'messages.require-enter-personal-email-id' ),
     		'contact_number.required' => __ ( 'messages.require-enter-contact-number' ),
     		'address_line_1.required' => __ ( 'messages.require-enter-address-line-1' ),
     		'current_city.required' => __ ( 'messages.require-select-city' ),
     		'current_state.required' => __ ( 'messages.require-select-state' ),
     		'current_country.required' => __ ( 'messages.require-select-country' ),
     		'address_permanent_line_1.required' => __ ( 'messages.require-enter-address-line-1' ),
     		'per_city.required' => __ ( 'messages.require-select-city' ),
     		'per_state.required' => __ ( 'messages.require-select-state' ),
     		'per_country.required' => __ ( 'messages.require-select-country' ),
     		'joining_date.required' => __ ( 'messages.require-enter-joining-date' ),
     		'designation.required' => __ ( 'messages.require-select-designation' ),
     		'team.required' => __ ( 'messages.require-select-team' ),
     		'leader_name_reporting_manager.required' => __ ( 'messages.require-select-leader-name-reporting-manager' ),
     		'recruitment_source.required' => __ ( 'messages.require-select-recruitment-source' ),
     		'notice_period.required' => __ ( 'messages.require-select-notice-period' ),
     		'assign_salary_employee.required' => __ ( 'messages.require-select-assign-salary-employee' ),
     		'shift.required' => __ ( 'messages.require-select-shift' ),
     		'new_weekly_off.required' => __ ( 'messages.require-select-weekly-off' ),
     		//'reference_name.required' => __ ( 'messages.require-select-reference-name' ),
     	
     	] );
     	if ($validator->fails ()) {
     		
     		return redirect()->back()->withErrors ( $validator )->withInput ();
		}
     	
     	$successMessage =  trans('messages.success-create',['module'=> $this->moduleName]);
     	$errorMessages = trans('messages.error-create',['module'=> $this->moduleName]);
     	$result = false;
     	$recordData = [];
     	$recordData['e_auto_generate_no'] = (!empty($request->post('auto_calculate_employee_code')) ? $request->post('auto_calculate_employee_code') : config('constants.SELECTION_NO'));
     	$recordData['v_employee_code'] = (!empty($request->post('employee_code')) ? $request->post('employee_code') :'');
     	$recordData['v_employee_name'] = (!empty($request->post('employee_name')) ? $request->post('employee_name') :'');
     	$recordData['v_employee_full_name'] = (!empty($request->post('full_name')) ? $request->post('full_name') :'');
     	$recordData['e_gender'] = (!empty($request->post('gender')) ? $request->post('gender') :null);
     	$recordData['v_blood_group'] = (!empty($request->post('blood_group')) ? $request->post('blood_group') :'');
     	$recordData['dt_birth_date'] = (!empty($request->post('date_of_birth')) ? dbDate($request->post('date_of_birth')) :'');
     	$recordData['v_outlook_email_id'] = (!empty($request->post('outlook_email_id')) ? $request->post('outlook_email_id') :null);
     	$recordData['v_personal_email_id'] = (!empty($request->post('personal_email_id')) ? $request->post('personal_email_id') :'');
     	$recordData['v_contact_no'] = (!empty($request->post('contact_number')) ? $request->post('contact_number') :'');
     	$recordData['v_education'] = (!empty($request->post('education')) ? $request->post('education') : null);
     	$recordData['v_cgpa'] = (!empty($request->post('cgpa_percentage')) ? $request->post('cgpa_percentage') : null );
     	$recordData['e_marital_status'] = (!empty($request->post('marital_status')) ? $request->post('marital_status') : null );
     	$recordData['v_current_address_line_first'] = (!empty($request->post('address_line_1')) ? $request->post('address_line_1') :'');
     	$recordData['v_current_address_line_second'] = (!empty($request->post('address_line_2')) ? $request->post('address_line_2') : null );
     	$recordData['i_current_address_city_id'] = (!empty($request->post('current_city')) ? (int)Wild_tiger::decode($request->post('current_city')) :0);
     	$recordData['v_current_address_pincode'] = (!empty($request->post('pincode')) ? $request->post('pincode') : null );
     	$recordData['v_permanent_address_line_first'] = (!empty($request->post('address_permanent_line_1')) ? $request->post('address_permanent_line_1') :'');
     	$recordData['v_permanent_address_line_second'] = (!empty($request->post('address_permanent_line_2')) ? $request->post('address_permanent_line_2') : null );
     	$recordData['i_permanent_address_city_id'] = (!empty($request->post('per_city')) ? (int)Wild_tiger::decode($request->post('per_city')) :0);
     	$recordData['v_permanent_address_pincode'] = (!empty($request->post('pincode_permanent')) ? $request->post('pincode_permanent') : null );
     	$recordData['v_aadhar_no'] = (!empty($request->post('aadhaar_number')) ? $request->post('aadhaar_number') :'');
   		$recordData['v_pan_no'] = (!empty($request->post('pan_number')) ? $request->post('pan_number') :null);
   		$recordData['dt_joining_date'] = (!empty($request->post('joining_date')) ? dbDate($request->post('joining_date')) :'');
     	$recordData['i_designation_id'] = (!empty($request->post('designation')) ? (int)Wild_tiger::decode($request->post('designation')) :0);
     	$recordData['i_sub_designation_id'] = (!empty($request->post('sub_designation')) ? (int)Wild_tiger::decode($request->post('sub_designation')) :0);
     	$recordData['i_team_id'] = (!empty($request->post('team')) ? (int)Wild_tiger::decode($request->post('team')) :0);
   		$recordData['i_leader_id'] = (!empty($request->post('leader_name_reporting_manager')) ? (int)Wild_tiger::decode($request->post('leader_name_reporting_manager')) :0);
   		$recordData['i_probation_period_id'] = (!empty($request->post('probation_period')) ? (int)Wild_tiger::decode($request->post('probation_period')) :0);
     	$recordData['i_notice_period_id'] = (!empty($request->post('notice_period')) ? (int)Wild_tiger::decode($request->post('notice_period')) :0);
     	$recordData['i_bank_id'] = (!empty($request->post('bank_name')) ? (int)Wild_tiger::decode($request->post('bank_name')) :0);
   		$recordData['v_bank_account_no'] = (!empty($request->post('account_number')) ? $request->post('account_number') :null);
     	$recordData['v_bank_account_ifsc_code'] = (!empty($request->post('ifsc_code')) ? $request->post('ifsc_code') :null);
     	$recordData['v_uan_no'] = (!empty($request->post('uan_number')) ? $request->post('uan_number') :null);
     	$recordData['e_assign_salary'] = (!empty($request->post('assign_salary_employee')) ? $request->post('assign_salary_employee') :null);
     	$recordData['e_pf_deduction'] = (!empty($request->post('deduction_of_pf_yes')) ? $request->post('deduction_of_pf_yes') : config('constants.SELECTION_NO') );
     	$recordData['i_role_permission'] = config('constants.DEFAULT_EMPLOYEE_ROLE_ID'); 
     	
     	$recordData['i_current_village_id'] = $villageId ;
     	$recordData['i_permanent_village_id'] = $permanentVillageId ;
     	$recordData['e_same_current_address'] = (!empty($request->post('same_current_address')) ? $request->post('same_current_address') :null);
     	
     	$recordData['i_recruitment_source_id'] = (!empty($request->post('recruitment_source')) ? (int)Wild_tiger::decode($request->post('recruitment_source')) :0);
     	$recordData['i_reference_emp_id'] = (!empty($request->post('reference_name')) ? (int)Wild_tiger::decode($request->post('reference_name')) :0);
     	$recordData['i_shift_id'] = (!empty($request->post('shift')) ? (int)Wild_tiger::decode($request->post('shift')) :0);
     	$recordData['i_weekoff_id'] = (!empty($request->post('new_weekly_off')) ? (int)Wild_tiger::decode($request->post('new_weekly_off')) :0);
     	
     	$weekOffEffectiveDate = (!empty($request->post('week_off_effective_date')) ? dbDate($request->post('week_off_effective_date')) : null);
     	
     	$recordData['dt_week_off_effective_date'] =  $weekOffEffectiveDate;
     	$recordData['dt_last_update_designation'] =  $recordData['dt_joining_date'];
     	$recordData['dt_last_update_team'] =  $recordData['dt_joining_date'];
     	$recordData['dt_last_update_week_off'] = (!empty($weekOffEffectiveDate) ? $weekOffEffectiveDate : $recordData['dt_joining_date'] ) ;
     	$recordData['dt_last_update_shift'] =  $recordData['dt_joining_date'];
     		
     	if(!empty($recordData['i_probation_period_id'])){
     		$probationInfo = ProbationPolicyMasterModel::where('i_id' , $recordData['i_probation_period_id'] )->first();
     		$joiningDate = $recordData['dt_joining_date'];
     		if( (!empty($probationInfo->v_probation_period_duration)) && (!empty($probationInfo->e_months_weeks_days)) ){
     			$duration = ( $probationInfo->v_probation_period_duration . ' ' . $probationInfo->e_months_weeks_days );
     			$recordData['dt_probation_end_date'] = date('Y-m-d' , strtotime("+" . $duration , strtotime($joiningDate)) );
     		}
     		$recordData['e_employment_status'] = config('constants.PROBATION_EMPLOYMENT_STATUS');
     		$recordData['e_in_probation'] = config('constants.SELECTION_YES');
     	} else {
     		$recordData['e_in_probation'] = config('constants.SELECTION_NO');
     		$recordData['e_employment_status'] = config('constants.CONFIRMED_EMPLOYMENT_STATUS');
     	}
     	
     	
     	
     	//dt_probation_end_date
     	
     	$result = false;
     		
     	DB::beginTransaction();
     	try{
     		if( $recordId > 0 ){
     			$successMessage =  trans('messages.success-update',['module'=> $this->moduleName]);
     			$errorMessages = trans('messages.error-update',['module'=> $this->moduleName]);
     					
     			$this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id' =>  $recordId ] );
     		} else {
     			
     			$generatePassword = config('constants.STATIC_EMPLOYEE_PASSWORD');
     			
     			$insertLoginData = [];
     			$insertLoginData['v_name'] = $recordData['v_employee_full_name'];
     			$insertLoginData['v_email'] = $recordData['v_outlook_email_id'];
     			$insertLoginData['v_mobile'] = $recordData['v_contact_no'];
     			$insertLoginData['v_role'] = config('constants.ROLE_USER');
     			$insertLoginData['v_password'] = null;
     				
     			$insertLogin = $this->crudModel->insertTableData( config('constants.LOGIN_MASTER_TABLE'), $insertLoginData );
     				
     			$recordData['i_login_id'] = $insertLogin;
     			$recordData['e_hold_salary_status'] = config('constants.SELECTION_NO');
     			if( $recordData['e_assign_salary'] == config('constants.SELECTION_YES') ){
     				$recordData['e_hold_salary_status'] =  (!empty($request->post('hold_salary')) ?  $request->post('hold_salary') : config('constants.SELECTION_NO') );
     			} 
     			//$recordData['e_hold_salary_status'] =  (!empty($request->post('hold_salary')) ?  $request->post('hold_salary') : config('constants.SELECTION_NO') );
     			
     			if( $recordData['e_hold_salary_status'] == config('constants.SELECTION_YES')){
     				$recordData['e_hold_salary_payment_status'] = config('constants.PENDING_STATUS');
     			}
     			
     			
     			$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData );
     				
     			$insertDesignationHistory = [];
     			$insertDesignationHistory['i_designation_id'] =  $recordData['i_designation_id'];
     			$insertDesignationHistory['i_employee_id'] =  $insertRecord;
     			$insertDesignationHistory['dt_start_date'] =  $recordData['dt_joining_date'];
     			$insertDesignationHistory['e_record_type'] =  config('constants.DESIGNATION_LOOKUP');
     				
     			$this->crudModel->insertTableData( config('constants.EMPLOYEE_DESIGNATION_HISTORY') , $insertDesignationHistory  );
     				
     			$insertTeamHistory = [];
     			$insertTeamHistory['i_designation_id'] =  $recordData['i_team_id'];
     			$insertTeamHistory['i_employee_id'] =  $insertRecord;
     			$insertTeamHistory['dt_start_date'] =  $recordData['dt_joining_date'];
     			$insertTeamHistory['e_record_type'] =  config('constants.TEAM_LOOKUP');
     				 
     			$this->crudModel->insertTableData( config('constants.EMPLOYEE_DESIGNATION_HISTORY') , $insertTeamHistory  );
     				
     			$insertShiftHistory = [];
     			$insertShiftHistory['i_designation_id'] =  $recordData['i_shift_id'];
     			$insertShiftHistory['i_employee_id'] =  $insertRecord;
     			$insertShiftHistory['dt_start_date'] =  $recordData['dt_joining_date'];
     			$insertShiftHistory['e_record_type'] =  config('constants.SHIFT_RECORD_TYPE');
     				 
     			$this->crudModel->insertTableData( config('constants.EMPLOYEE_DESIGNATION_HISTORY') , $insertShiftHistory  );
     				
     			$insertWeekOffHistory = [];
     			$insertWeekOffHistory['i_designation_id'] =  $recordData['i_weekoff_id'];
     			$insertWeekOffHistory['i_employee_id'] =  $insertRecord;
     			$insertWeekOffHistory['dt_start_date'] =  (!empty($weekOffEffectiveDate) ? $weekOffEffectiveDate : $recordData['dt_joining_date'] ) ;;
     			$insertWeekOffHistory['e_record_type'] =  config('constants.WEEK_OFF_RECORD_TYPE');
     				 
     			$this->crudModel->insertTableData( config('constants.EMPLOYEE_DESIGNATION_HISTORY') , $insertWeekOffHistory  );
     			
     			if( isset($recordData['e_employment_status']) && ( $recordData['e_employment_status'] == config('constants.PROBATION_EMPLOYMENT_STATUS') )  ){
     				$probationHistoryData = [];
     				$probationHistoryData['i_employee_id'] = $insertRecord;
     				$probationHistoryData['dt_start_date'] = $recordData['dt_joining_date'];
     				$probationHistoryData['dt_end_date'] = ( isset($recordData['dt_probation_end_date']) ? $recordData['dt_probation_end_date'] : null ); 
     				
     				$this->crudModel->insertTableData( config('constants.EMPLOYEE_PROBATION_HISTORY') , $probationHistoryData  );
     				
     			} 
     			
     			
     			//$recordData['e_assign_salary'] = config('constants.SELECTION_YES') ;
     			if( $recordData['e_assign_salary']  == config('constants.SELECTION_YES') ){
     				$salaryGroupId = (!empty($request->post('salary_group')) ? (int)Wild_tiger::decode($request->post('salary_group')) :0 );
     				$salaryComponentDetails = SalaryGroupDetailsModel::with(['salaryComponentInfo'])->where('i_salary_group_id' , $salaryGroupId )->get();
     				$allSalaryDetails = [];
     				$totalEarning = 0;
     				$totalDeduct = 0;
     				if(!empty($salaryComponentDetails)){
     					foreach($salaryComponentDetails as $salaryComponentDetail){
     						$rowSalaryData = [];
     						$rowSalaryData['i_salary_component_id'] = $salaryComponentDetail->i_salary_components_id;
     						$rowSalaryData['d_amount'] = (!empty($request->input('salary_compoent_id_'.$salaryComponentDetail->i_salary_components_id )) ? $request->input('salary_compoent_id_'.$salaryComponentDetail->i_salary_components_id ) : 0 );
     							
     						/* if(!empty($rowSalaryData['d_amount'])){ */
     							switch($salaryComponentDetail->e_type){
     								case config('constants.SALARY_COMPONENT_TYPE_EARNING'):
     									$totalEarning += $rowSalaryData['d_amount'];
     									break;
     								case config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'):
     									$totalDeduct += $rowSalaryData['d_amount'];
     									break;
     							}
     							$allSalaryDetails[] = $rowSalaryData;
     						/* } */
     					}
     				}
     				
     				$salaryMasterData = [];
     				$salaryMasterData['i_employee_id'] = $insertRecord;
     				$salaryMasterData['i_salary_group_id'] = $salaryGroupId;
     				$salaryMasterData['e_pf_by_employer'] = (!empty($request->post('deduction_employer_from_employee')) ?  $request->post('deduction_employer_from_employee') : config('constants.SELECTION_NO') );
     				$salaryMasterData['e_pf_deduction'] = (!empty($request->post('deduction_of_pf')) ?  $request->post('deduction_of_pf') : config('constants.SELECTION_NO') );
     				/* $salaryMasterData['d_jan_hold_amount'] = (!empty($request->input('first_month_hold_salary')) ? $request->input('first_month_hold_salary') : null );
     				$salaryMasterData['d_feb_hold_amount'] = (!empty($request->input('second_month_hold_salary')) ? $request->input('second_month_hold_salary') : null );
     				$salaryMasterData['d_mar_hold_amount'] = (!empty($request->input('third_month_hold_salary')) ? $request->input('third_month_hold_salary') : null );
     				$salaryMasterData['d_apr_hold_amount'] = (!empty($request->input('fourth_month_hold_salary')) ? $request->input('fourth_month_hold_salary') : null );
     				$salaryMasterData['d_may_hold_amount'] = (!empty($request->input('fifth_month_hold_salary')) ? $request->input('fifth_month_hold_salary') : null );
     				$salaryMasterData['d_jun_hold_amount'] = (!empty($request->input('six_month_hold_salary')) ? $request->input('six_month_hold_salary') : null ); */
     					
     				$joiningDate = $recordData['dt_joining_date'];
     				
     				$allSixMonths = [ 'first'  , 'second' , 'third' , 'fourth' , 'fifth' , 'six' ];
     				$holdSalaryInfo = [];
     				$totalHoldAmount  = 0 ;
     				if( $recordData['e_assign_salary'] == config('constants.SELECTION_YES') ){
	     				foreach($allSixMonths as $key => $allSixMonth){
	     					$nextDate = date('Y-m-01' , strtotime("+" . $key ." month" , strtotime($joiningDate)  ) );
	     					$monthName = strtolower(date("M" ,strtotime($nextDate)));
	     					$salaryMasterData['d_'.$monthName.'_hold_amount'] = (!empty($request->input($allSixMonth.'_month_hold_salary')) ? $request->input($allSixMonth.'_month_hold_salary') : null );
	     					$holdSalaryInfo[$nextDate] =  (!empty($request->input($allSixMonth.'_month_hold_salary')) ? $request->input($allSixMonth.'_month_hold_salary') : null );
	     					if(!empty($holdSalaryInfo[$nextDate])){
	     						$totalHoldAmount += $holdSalaryInfo[$nextDate];
	     					}
	     				}
     				}
     				$salaryMasterData['v_hold_salary_info'] = (!empty($holdSalaryInfo) ? json_encode($holdSalaryInfo) : null );
     				$salaryMasterData['d_total_hold_amount'] = $totalHoldAmount;
     				$salaryMasterData['d_total_earning'] = $totalEarning;
     				$salaryMasterData['d_total_deduction'] = $totalDeduct;
     				$salaryMasterData['d_net_pay_monthly'] = ( $totalEarning - $totalDeduct );
     				$salaryMasterData['d_net_pay_annually'] =  round( ( ( $totalEarning - $totalDeduct ) * 12 ) , 2 );
     				
     				$insertSalary  = $this->crudModel->insertTableData( config('constants.EMPLOYEE_SALARY_MASTER_TABLE')  , $salaryMasterData  );
     				
     				if(!empty($salaryMasterData['v_hold_salary_info'])){
     					$empHoldSalaryDetails = json_decode($salaryMasterData['v_hold_salary_info'],true);
     					$allHoldSalaryDetails = [];
     					if(!empty($empHoldSalaryDetails)){
     						foreach($empHoldSalaryDetails as $empHoldSalaryKey =>  $empHoldSalaryDetail){
     							if(!empty($empHoldSalaryDetail)){
     								$rowHoldSalary = [];
     								$rowHoldSalary['i_employee_id'] = $insertRecord;
     								$rowHoldSalary['dt_month'] = $empHoldSalaryKey;
     								$rowHoldSalary['d_amount'] = $empHoldSalaryDetail;
     								$this->crudModel->insertTableData( config('constants.EMPLOYEE_HOLD_SALARY_INFO')  , $rowHoldSalary  );
     								$allHoldSalaryDetails[] = $rowHoldSalary;
     							}
     						}
     					}
     					$expectedHoldSalaryReleaseMonth = (!empty($allHoldSalaryDetails) ? max(array_column($allHoldSalaryDetails, 'dt_month')) : null );
     					if(!empty($expectedHoldSalaryReleaseMonth)){
     						$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , [ 'dt_on_hold_expected_release_date' => $expectedHoldSalaryReleaseMonth  ] , [ 'i_id' => $insertRecord  ]  );
     					}
     				}
     				
     				if(!empty($allSalaryDetails)){
     					
     					$reviseSalaryMaster = [];
     					$reviseSalaryMaster['i_employee_id'] = $salaryMasterData['i_employee_id'];
     					$reviseSalaryMaster['dt_effective_date'] = $recordData['dt_joining_date'];
     					$reviseSalaryMaster['t_is_updated'] = 1;
     					$reviseSalaryMaster['i_salary_group_id'] = $salaryMasterData['i_salary_group_id'];
     					$reviseSalaryMaster['e_pf_by_employer'] = $salaryMasterData['e_pf_by_employer'];
     					$reviseSalaryMaster['e_pf_deduction'] = $salaryMasterData['e_pf_deduction'];
     					$reviseSalaryMaster['d_total_earning'] = $salaryMasterData['d_total_earning'];
     					$reviseSalaryMaster['d_total_deduction'] = $salaryMasterData['d_total_deduction'];
     					$reviseSalaryMaster['d_net_pay_monthly'] = $salaryMasterData['d_net_pay_monthly'];
     					$reviseSalaryMaster['d_net_pay_annually'] = $salaryMasterData['d_net_pay_annually'];
     					
     					$insertReviseSalary = $this->crudModel->insertTableData( config('constants.REVISE_SALARY_MASTER_TABLE')  , $reviseSalaryMaster  );
     					
     					$salaryOtherDetails = array_map(function($allSalaryDetail) use ($insertSalary) {
     						$allSalaryDetail['i_employee_salary_id'] = $insertSalary;
     						$allSalaryDetail ['i_created_id'] = session()->get('user_id');
     						$allSalaryDetail ['dt_created_at'] = date('Y-m-d H:i:s');
     						return $allSalaryDetail;
     					} , $allSalaryDetails );
     					
     					$reviseSalaryInfo = [];
     					if(!empty($salaryOtherDetails)){
     						foreach($salaryOtherDetails as $salaryOtherDetail){
     							$reviseSalaryRowData = [];
     							$reviseSalaryRowData['i_employee_revise_salary_id'] = $insertReviseSalary;
     							$reviseSalaryRowData['i_salary_component_id'] = $salaryOtherDetail['i_salary_component_id'];
     							$reviseSalaryRowData['d_amount'] = $salaryOtherDetail['d_amount'];
     							$reviseSalaryRowData ['i_created_id'] = session()->get('user_id');
     							$reviseSalaryRowData ['dt_created_at'] = date('Y-m-d H:i:s');
     							$reviseSalaryInfo[] = $reviseSalaryRowData;
     						}
     					}
     						
						DB::table(config('constants.EMPLOYEE_SALARY_DETAIL_TABLE'))->insert($salaryOtherDetails);
						
						DB::table(config('constants.REVISE_SALARY_INFO_TABLE'))->insert($reviseSalaryInfo);
     						
     				};
     					
     			}
     			
     			$leaveTypeDetails = LeaveTypeMasterModel::where('t_is_deleted' , 0 )->get();
     				
     			if(!empty($leaveTypeDetails)){
     				foreach($leaveTypeDetails as $leaveTypeDetail){
     					$leaveBalanceData = [];
     					$leaveBalanceData['i_employee_id'] = $insertRecord;
     					$leaveBalanceData['i_leave_type_id'] = $leaveTypeDetail->i_id;
     					$leaveBalanceData['d_current_balance'] = 0;
     						
     					$checkLeaveBalanceWhere = [];
     					$checkLeaveBalanceWhere['i_employee_id'] = $insertRecord;
     					$checkLeaveBalanceWhere['i_leave_type_id'] = $leaveTypeDetail->i_id;
     					$checkLeaveBalanceWhere['t_is_deleted'] = 0;
     					$checkLeaveBalanceEntry = LeaveBalanceModel::where($checkLeaveBalanceWhere)->first();
     						
     					if(!empty($checkLeaveBalanceEntry)){
     						$this->crudModel->updateTableData( config('constants.LEAVE_BALANCE_TABLE')  , $leaveBalanceData , [ 'i_id' => $checkLeaveBalanceEntry->i_id  ]  );
     					} else {
     						$this->crudModel->insertTableData( config('constants.LEAVE_BALANCE_TABLE')  , $leaveBalanceData  );
     					}	
     				}
     			}	
     				
     		}
     		if( $insertRecord > 0 ){
     			$result = true;
     		}
     		$result = true;
     	}catch(\Exception $e){
     		//var_dump($e->getMessage());die;
     		DB::rollback();
     		$result = false;
     	}
     	//var_dump($result);die;
     	if( $result != false ){
     		DB::commit();
     		Wild_tiger::setFlashMessage ('success', $successMessage  );
     		return redirect($this->redirectUrl);
     		
     	}
     	DB::rollback();
     	Wild_tiger::setFlashMessage ( 'danger', $errorMessages  );
     	return redirect()->back()->withErrors ( $validator )->withInput ();
     		
     }
     
     
     public function checkUniqueEmployeeCode(Request $request){
     	$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0  );
     	$validator = Validator::make ( $request->all (), [
     			'employee_code' => [ 'required' , new UniqueEmployeeCode($recordId) ],
     	], [
     			'employee_code.required' => __ ( 'messages.require-employee-code' ),
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
     
     public function profile($id = null ){
     	
     	if( ( session()->has('is_supervisor') == false ) && (  in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) ) ){
     		if(!empty($id)){
     			//return redirect('access-denied');
     		}
     	}
     	
     	$recordId = null;
     	$errorFound = true;
     	$data['pageTitle'] = trans('messages.profile');
     	if((!empty(session()->get('user_employee_id'))) && ( in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ))){
     		
     	}
     	
     	if(!empty($id)){
     		$recordId = (int) Wild_tiger::decode($id);
     		/* if( session()->get('role') != config('constants.ROLE_ADMIN') ){
     			return redirect ( config('constants.404_PAGE') ); 
     		} */
     	}
     	
     	if( empty($recordId) ){
     		$recordId = (int) session()->get('user_employee_id');
     	}
     	
     	if( ( $recordId == session()->get('user_employee_id') )  ){
     		$data['pageTitle'] = trans('messages.my-profile');
     	}
     	
     	
     	if($recordId > 0 ){
     		$data['relationInof'] = relationInfo();
     		$whereData['master_id'] = $recordId;
     		$whereData['singleRecord'] = true;
     		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     			$whereData['show_all'] = true;
     		}
     		
     		$data['employeeRecordInfo'] = $this->crudModel->getRecordDetails( $whereData );
     		
     		//dd($data['employeeRecordInfo']);
     		//dd($data['employeeRecordInfo']);
     		if(!empty($data['employeeRecordInfo'])){
     			$data['allowedLastEffDate'] = lastAllowedDate($data['employeeRecordInfo']);
     			$errorFound = false;
     			$data['empId'] = Wild_tiger::encode($recordId);
     			return view( $this->folderName . 'employee-profile')->with($data);
     		}
     	}
     	if( $errorFound != false ){
     		return redirect ( config('constants.DASHBORD_MASTER_URL') );
     	}
     }
     
    public function editRealtion(Request $request){
     	$data = $whereData = [];
     	$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0 );
     	if($employeeId > 0 ){
     		$whereData['master_id'] = $employeeId;
     		$whereData['singleRecord'] = true;
     		
     		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     			$whereData['show_all'] = true;
     		}
     		
     		$recordDetails = $this->crudModel->getRecordDetails($whereData);
     		if(!empty($recordDetails)){
     			$data['employeeRelationinfo'] = $recordDetails;
     		}
     	}
     	$data['relationInof'] = relationInfo();
     	$html = view ($this->folderName . 'add-relation-master')->with ( $data )->render();
     	echo $html;die;
     }
     public function addRelation(Request $request){
     	
     	if(!empty($request->post())){
     		$successMessage =  trans('messages.success-update',['module'=> trans('messages.relation')]);
     		$errorMessages = trans('messages.required-add-relation');
     		 
     		$result = false;
     		$html = null;
     		$employeeId = (!empty($request->post('employee_record_id')) ? (int)Wild_tiger::decode($request->post('employee_record_id')) : 0 );
     		$employeeRelationCount = (!empty($request->post('relation_count')) ? trim($request->post('relation_count')) : 0 );
     		
     		if($employeeId > 0 ){
     			
     			$whereData = [];
     			$whereData['master_id'] = $employeeId;
     			$whereData['singleRecord'] = true;
     			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     				$whereData['show_all'] = true;
     			}
     			
     			$employeeRelationRecordDetails = $this->crudModel->getRecordDetails($whereData);
     			
     			if(!empty($employeeRelationRecordDetails->employeeRelation)){
     				foreach ($employeeRelationRecordDetails->employeeRelation as $employeeRelationDetails){
     					$employeeRelationId = $employeeRelationDetails->i_id;
     					if(!empty($request->input('edit_relation_'.$employeeRelationId))){
     						$relationData = [];
     						$relationData['e_employee_relation'] = (!empty($request->post('edit_relation_'.$employeeRelationId)) ? trim($request->post('edit_relation_'.$employeeRelationId)) : '');
     						$relationData['v_relation_name'] = (!empty($request->post('edit_name_'.$employeeRelationId)) ? trim($request->post('edit_name_'.$employeeRelationId)) : '');
     						$relationData['v_mobile_number'] = (!empty($request->post('edit_mobile_'.$employeeRelationId)) ? trim($request->post('edit_mobile_'.$employeeRelationId)) : null);
     						$relationData['dt_birth_date'] = (!empty($request->post('edit_rel_date_of_birth_'.$employeeRelationId)) ? dbDate($request->post('edit_rel_date_of_birth_'.$employeeRelationId)) : null);
     						if((!empty($relationData['e_employee_relation'])) && (!empty($relationData['v_relation_name']))){
     							$result = $this->crudModel->updateTableData( config('constants.EMPLOYEE_RELATION_DETAILS_TABLE') , $relationData , [ 'i_id' => $employeeRelationId] );
     						}
     						
     					} else {
     						$deleteRecordData = [];
     						$deleteRecordData ['t_is_active'] = 0;
     						$deleteRecordData ['t_is_deleted'] = 1;
     						$result = $this->crudModel->deleteTableData( config('constants.EMPLOYEE_RELATION_DETAILS_TABLE') , $deleteRecordData , [ 'i_id' => $employeeRelationId] );
     							
     					}
     				}
     			}
     			
     			for ($i = 1; $i <= $employeeRelationCount ;$i++){
     				$rowData = [];
     				$rowData['i_employee_id'] = $employeeId;
     				$rowData['e_employee_relation'] = (!empty($request->post('relation_'.$i)) ? trim($request->post('relation_'.$i)) : '');
     				$rowData['v_relation_name'] = (!empty($request->post('name_'.$i)) ? trim($request->post('name_'.$i)) : '');
     				$rowData['v_mobile_number'] = (!empty($request->post('mobile_'.$i)) ? trim($request->post('mobile_'.$i)) : null);
     				$rowData['dt_birth_date'] = (!empty($request->post('rel_date_of_birth_'.$i)) ? dbDate($request->post('rel_date_of_birth_'.$i)) : null);
     				if((!empty($rowData['e_employee_relation'])) && (!empty($rowData['v_relation_name']))){
     					$insertRecord = $this->crudModel->insertTableData( config('constants.EMPLOYEE_RELATION_DETAILS_TABLE') , $rowData);
     					if($insertRecord > 0){
     						$result = true;
     					}
     				}
     			}
     			
     		}
     		
     		$empWhere = [];
     		$empWhere['master_id'] = $employeeId;
     		$empWhere['singleRecord'] = true;
     		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     			$empWhere['show_all'] = true;
     		}
     		
     		$employeeRelationRecord = $this->crudModel->getRecordDetails($empWhere);
     		$recordInfo = [];
     		$recordInfo['employeeRecordInfo'] = $employeeRelationRecord;
     		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/relation-list')->with ( $recordInfo )->render();
     		
     		if($result != false){
     				
     			$this->ajaxResponse(1, $successMessage,['html' => $html] );
     		}else {
     				
     			$this->ajaxResponse(101, $errorMessages);
     		}
     	}
     }
     
     public function editPrimaryDetail(Request $request){
     	$data = $whereData = [];
     	$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0 );
     	if($employeeId > 0 ){
     		$whereData['master_id'] = $employeeId;
     		$whereData['singleRecord'] = true;
     		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     			$whereData['show_all'] = true;
     		}
     		
     		$recordDetails = $this->crudModel->getRecordDetails($whereData);
     		if(!empty($recordDetails)){
     			$data['employeeRecordInfo'] = $recordDetails;
     		}
     	}
     	$data['genderMasterInfo'] = genderMaster();
     	$data['bloodGroupInfo'] = bloodGroupMaster();
     	$data['maritalStatusInfo'] = maritalStatusInfo();
     	$html = view ($this->folderName . 'add-primary-details-model')->with ( $data )->render();
     	echo $html;die;
     }
	public function addPrimaryDetails(Request $request){
     	
     	if(!empty($request->post())){
     		
     		$formValidation =[];
     		if( session()->get('role') == config('constants.ROLE_ADMIN')){
     			$formValidation['employee_name'] = ['required'];
     			$formValidation['full_name'] = ['required'];
     			$formValidation['gender'] = ['required'];
     			$formValidation['date_of_birth'] = ['required'];
     		}
     		 
     		$validator = Validator::make ( $request->all (), $formValidation , [
     				'employee_name.required' => __ ( 'messages.require-employee-name' ),
     				'full_name.required' => __ ( 'messages.require-full-name' ),
     				'gender.required' => __ ( 'messages.require-select-gender' ),
     				'date_of_birth.required' => __ ( 'messages.require-enter-date-of-birth' ),
     		] );
     		if ($validator->fails ()) {
     			$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.primary-details') ] ) ) );
     		}
     	
     		$successMessage =  trans('messages.success-update',['module'=> trans('messages.primary-details')]);
     		$errorMessages = trans('messages.error-update',['module'=> trans('messages.primary-details')]);
     	
     		$result = false;
     		$employeeId = (!empty($request->post('employee_record_id')) ? (int)Wild_tiger::decode($request->post('employee_record_id')) : 0 );
     		
     		if($employeeId > 0 ){
     			$recordData = [];
     			if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){
	     			$recordData['v_employee_name'] = (!empty($request->post('employee_name')) ? trim($request->post('employee_name')) : null);
	     			$recordData['v_employee_full_name'] = (!empty($request->post('full_name')) ? trim($request->post('full_name')) : null);
	     			$recordData['e_gender'] = (!empty($request->post('gender')) ? trim($request->post('gender')) : null);
	     			$recordData['dt_birth_date'] = (!empty($request->post('date_of_birth')) ? dbDate($request->post('date_of_birth')) : null);
	     		}
     			$recordData['v_blood_group'] = (!empty($request->post('blood_group')) ? trim($request->post('blood_group')) : null);
     			$recordData['v_education'] = (!empty($request->post('education')) ? trim($request->post('education')) : null);
     			$recordData['v_cgpa'] = (!empty($request->post('cgpa_percentage')) ? trim($request->post('cgpa_percentage')) : null);
     			$recordData['e_marital_status'] = (!empty($request->post('marital_status')) ? trim($request->post('marital_status')) : null);
     				
     			$updateRecord = $this->crudModel->updateTableData( $this->tableName , $recordData,['i_id' => $employeeId ]);
     			
     			$empInfo = EmployeeModel::where('i_id' , $employeeId )->first();
     			
     			if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){
     				if( (!empty($empInfo)) && ( isset($empInfo->i_login_id) ) && ( isset($recordData['v_employee_full_name']) && (!empty($recordData['v_employee_full_name'])) )  ){
     					$this->crudModel->updateTableData( config('constants.LOGIN_MASTER_TABLE') , [ 'v_name' => $recordData['v_employee_full_name']  ],['i_id' => $empInfo->i_login_id ] );
     				}
     			}
     			
     			$empWhere = [];
     			$empWhere['master_id'] = $employeeId;
     			$empWhere['singleRecord'] = true;
     			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     				$empWhere['show_all'] = true;
     			}
     			$recordDetail = $this->crudModel->getRecordDetails($empWhere);
     			$recordInfo = $data = [];
     			
     			$recordInfo['employeeRecordInfo'] = $recordDetail;
     			$recordInfo['empId'] = Wild_tiger::encode($employeeId);
     			$data['primaryDetailsInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/primary-details-list')->with ( $recordInfo )->render();
     			$data['mainProfileInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/main-profile-info')->with ( $recordInfo )->render();
     			
     			if($updateRecord > 0){
     				$result = true;
     			} 
     		}
     		
     		if($result != false){
     			
     			$this->ajaxResponse(1, $successMessage ,$data);
     		}else {
     			 
     			$this->ajaxResponse(101, $errorMessages);
     		} 
     		
     	}
     }
     
 	public function addUploadDocumentDetails(Request $request){
 		
     	if(!empty($request->post())){
     		$finalSelectedImages = (!empty($request->post('final_selected_image')) ? $request->post('final_selected_image') :'');
     		$removeImages = (!empty($request->post('remove_image')) ? $request->post('remove_image') :'');
     		 
     		$formValidation =[];
     		$formValidation['upload_document_file[]'] = 'file|mimes:jpg,png,pdf,jpeg,pdf,doc,docx,xlsx,xls|max:'.( config('constants.UPLOAD_FILE_LIMIT_SIZE') * 1024 );
     		//$formValidation['upload_document_file[]'] = ['required' ,'file|mimes:jpg,png,pdf,jpeg,pdf,doc,docx,xlsx,xls|max:'.( config('constants.UPLOAD_FILE_LIMIT_SIZE') * 1024 )];
     		
     		$validator = Validator::make ( $request->all (), $formValidation , [
     				//'upload_document_file[].required' => __ ( 'messages.required-upload-document-file' ),
     				'upload_document_file[].file' => __ ( 'messages.required-upload-document-file-valid' ),
     				
     		] );
     		if ($validator->fails ()) {
     			$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.document') ] ) ) );
     		}
     
     		$successMessage =  trans('messages.document-uploaded-success');// uploaded
     		$errorMessages = trans('messages.document-uploaded-error');
     
     		$result = false;
     		$recordData = [] ;
     		$employeeDocumentId = (!empty($request->post('document_type_record_id')) ? (int)Wild_tiger::decode($request->post('document_type_record_id')) : 0 );
     		$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0 );
     		$recordData['i_document_type_id'] = $employeeDocumentId;
     		$recordData['i_employee_id'] = $employeeId;
     		$recordData['v_remark'] = (!empty($request->post('remark')) ? $request->post('remark') : null);
     		
     		$getEmployeeWhere = [];
     		$getEmployeeWhere['i_id'] = $recordData['i_employee_id']; 
     		$getEmployeeInfo = EmployeeModel::where($getEmployeeWhere)->first();
     		
     		$employeeCode = (!empty($getEmployeeInfo) ? $getEmployeeInfo->v_employee_code : null );
     		
     		$documentTypeInfo = DocumentTypeModel::with(['documentFolderMaster'])->where('i_id' , $employeeDocumentId)->first();
     		
     		$additionalFolderName = '';
     		if(!empty($documentTypeInfo)){
     			if(isset($documentTypeInfo->documentFolderMaster->v_document_folder_name)){
     				$additionalFolderName .= createSlug($documentTypeInfo->documentFolderMaster->v_document_folder_name) . '/';
     			}
     			if(isset($documentTypeInfo->v_document_type)){
     				$request->specific_file_name = $documentTypeInfo->v_document_type;
     				//$additionalFolderName .= createSlug($documentTypeInfo->v_document_type) ;
     			}
     			$additionalFolderName = (!empty($additionalFolderName) ? "/" . rtrim($additionalFolderName,'/') : "" );
     		}
     		$uploadFiles = [];
     		if($request->hasFile('upload_document_file')){
     			$uploadFiles = $this->uploadMultipleFiles($request,'upload_document_file' , $this->documentFolder . $employeeCode . $additionalFolderName.'/',$finalSelectedImages ,$removeImages);
     			
     			if( (!empty($uploadFiles)) && ( isset($uploadFiles['status']) ) && ( $uploadFiles['status'] != false )  ){
     				$uploadFiles = $uploadFiles['uploadedImagePath'];
     			} else {
     				$this->ajaxResponse(101, ( isset($uploadFiles['message']) ? $uploadFiles['message'] : trans('messages.system-error') ) );
     			}
     			
     		}
     		//echo '<pre>';print_r($uploadFile);die;
     		//$recordData['v_document_file'] = (!empty($uploadFiles) ? json_encode($uploadFiles) : null ) ;
     		$where = [];
     		$where['master_id'] = $employeeDocumentId;
     		$where['employee_id'] = $employeeId;
     		$where['singleRecord'] = true;
     		
     		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     			$where['show_all'] = true;
     		}
     		$getEmployeeDocumentDetails = $this->employeeDocumentTypeModel->getRecordDetails($where);
     		$getEmployeeDocumentDetails = [];
     		
     		if(!empty($getEmployeeDocumentDetails)){
     			
     			$successMessage =  trans('messages.success-update',['module'=> trans('messages.document')]);
     			$errorMessages = trans('messages.error-update',['module'=> trans('messages.document')]);
     			$empDocumentId = (!empty($getEmployeeDocumentDetails->i_id) ? $getEmployeeDocumentDetails->i_id : "");
     			$allExistingFilesDetails = (!empty($getEmployeeDocumentDetails->v_document_file) ? json_decode($getEmployeeDocumentDetails->v_document_file,true) : [] );
     			$removeFilesArray = (!empty($removeImages) ? explode(",", $removeImages) : []);
     			
     			
     			$serverRemoveFileArray = $documentFiles =  [];
     			if(!empty($allExistingFilesDetails)){
     				foreach($allExistingFilesDetails as $allExistingFilesDetail){
     					if( (!empty($allExistingFilesDetail)) && !in_array( basename($allExistingFilesDetail) , $removeFilesArray ) ){
     						$documentFiles[] = $allExistingFilesDetail;
     					} else {
     						$serverRemoveFileArray[] = $allExistingFilesDetail;
     					}
     				}
     			}
     			if(!empty($serverRemoveFileArray)) {
     				foreach($serverRemoveFileArray as $serverRemoveFile){
     					if(file_exists(config('constants.FILE_STORAGE_PATH').config('constants.UPLOAD_FOLDER') . $serverRemoveFile)){
     						unlink(config('constants.FILE_STORAGE_PATH').config('constants.UPLOAD_FOLDER') . $serverRemoveFile);
     					}
     				}
     			}
     			$empDocumentFiles = (!empty($documentFiles) ? (!empty($uploadFile) ? array_merge($uploadFile,$documentFiles) :  $documentFiles ) : $uploadFile );
     			
     			$recordData['v_document_file'] = (!empty($empDocumentFiles) ? json_encode($empDocumentFiles) : null );
     			
     			$result = $this->crudModel->updateTableData(config('constants.EMPLOYEE_DOCUMENT_MASTER_DETAILS_TABLE'), $recordData, ['i_id' =>$empDocumentId ]);
     		} else{
     			$insertRecord = 0;
     			if(!empty($uploadFiles)){
     				foreach ($uploadFiles as $uploadFile){
     					$recordData['v_document_file'] = (!empty($uploadFile) ? $uploadFile : null ) ;
     					$insertRecord = $this->crudModel->insertTableData( config('constants.EMPLOYEE_DOCUMENT_MASTER_DETAILS_TABLE') , $recordData);
     					
     				}
     			}
     			
     			if($insertRecord > 0){
     				$result = true;
     			}
     		}
     		
     		if($result != false){
     
     			$this->ajaxResponse(1, $successMessage );
     		}else {
     			 
     			$this->ajaxResponse(101, $errorMessages);
     		}
     	}
     }
     
     public function viewDocumentDetails(Request $request){
     	$data = $whereData = [];
     	$documentTypeId = (!empty($request->post('documet_type_id')) ? (int)Wild_tiger::decode($request->post('documet_type_id')) : 0 );
     	$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0 );
     	if($documentTypeId > 0 ){
     		$whereData['master_id'] = $documentTypeId;
     		$whereData['employee_id'] = $employeeId;
     		//$whereData['singleRecord'] = true;
     		
     		if( session()->has('user_permission') && ( ( in_array(config('permission_constants.ALL_DOCUMENT_REPORT'), session()->get('user_permission')  ) ) || ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) )  ) ){
     			$whereData['show_all'] = true;
     		}
     		
     		$recordDetails = $this->employeeDocumentTypeModel->getRecordDetails($whereData);
     		if(!empty($recordDetails)){
     			$data['employeeDocumentRecordDetails'] = $recordDetails;
     		}
     	}
     	
     	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/view-document-list' )->with ( $data )->render();
     	echo $html;die;
     }
     
     public function editContactDetails(Request $request){
     	$data = $whereData = [];
     	$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0 );
     	if($employeeId > 0 ){
     		$whereData['master_id'] = $employeeId;
     		$whereData['singleRecord'] = true;
     		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     			$whereData['show_all'] = true;
     		}
     		$recordDetails = $this->crudModel->getRecordDetails($whereData);
     		if(!empty($recordDetails)){
     			$data['employeeRecordInfo'] = $recordDetails;
     		}
     	}
     	
     	$html = view ($this->folderName . 'add-contact-details-model')->with ( $data )->render();
     	echo $html;die;
     }
     
     public function addContactDetails(Request $request){
     	
     	if(!empty($request->post())){
     		$employeeId = (!empty($request->post('employee_record_id')) ? (int)Wild_tiger::decode($request->post('employee_record_id')) : 0 );
     		$outlookEmail = (!empty($request->post('outlook_email_id')) ? trim($request->post('outlook_email_id')) : null);
     		$formValidation =[];
     		if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){
     			$formValidation['personal_email_id'] = ['required'];
     			$formValidation['contact_number'] = ['required','max:15'];
     			if(!empty($outlookEmail)){
     				$formValidation['outlook_email_id'] = [new UniquePersonalEmailId($employeeId)];
     			}
     		}
     		
     		$validator = Validator::make ( $request->all (), $formValidation , [
     				'personal_email_id.required' => __ ( 'messages.require-enter-personal-email-id' ),
     				'contact_number.required' => __ ( 'messages.require-enter-contact-number' ),
     				'outlook_email_id' => __ ( 'messages.require-enter-outlook-email-id' ),
     				
     		] );
     		if ($validator->fails ()) {
     			$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.contact-details') ] ) ) );
     		}
     	
     		$successMessage =  trans('messages.success-update',['module'=> trans('messages.contact-details')]);
     		$errorMessages = trans('messages.error-update',['module'=> trans('messages.contact-details')]);
     	
     		$result = false;
     		//$html = null;
     		 
     		if($employeeId > 0 ){
     			$recordData = [];
     			if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){
	     			$recordData['v_outlook_email_id'] = (!empty($outlookEmail) ? $outlookEmail : null);
	     			$recordData['v_personal_email_id'] = (!empty($request->post('personal_email_id')) ? trim($request->post('personal_email_id')) : null);
	     			$recordData['v_contact_no'] = (!empty($request->post('contact_number')) ? trim($request->post('contact_number')) :null);
	     			
     			} 
	     		$recordData['v_emergency_contact_person_name'] = (!empty($request->post('person_name')) ? trim($request->post('person_name')) :null);
	     		$recordData['v_emergency_contact_relation'] = (!empty($request->post('person_relation')) ? trim($request->post('person_relation')) : null);
	     		$recordData['v_emergency_contact_person_no'] = (!empty($request->post('person_no')) ? trim($request->post('person_no')) : null);
     				
     			$updateRecord = $this->crudModel->updateTableData( $this->tableName , $recordData,['i_id' => $employeeId ]);
     			
     			$empWhere = [];
     			$empWhere['master_id'] = $employeeId;
     			$empWhere['singleRecord'] = true;
     			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     				$empWhere['show_all'] = true;
     			}
     			
     			
     			$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
     			
     			if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){
	     			$loginData = [];
	     			$loginData['v_email'] = (!empty($outlookEmail) ? $outlookEmail : null);
	     			$loginData['v_mobile'] = (!empty($request->post('contact_number')) ? trim($request->post('contact_number')) :null);
	     			//dd($loginData);
	     			$this->crudModel->updateTableData( config('constants.LOGIN_MASTER_TABLE') , $loginData,['i_id' => (!empty($recordDetail->loginInfo->i_id) ? $recordDetail->loginInfo->i_id : '') ]);
     			}
     			
     			$recordInfo = $data = [];
     			$recordInfo['employeeRecordInfo'] = $recordDetail;
     			$recordInfo['empId'] = Wild_tiger::encode($employeeId);
     			$data['contactDetailsInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/contact-details-list')->with ( $recordInfo )->render();
     			$data['mainProfileInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/main-profile-info')->with ( $recordInfo )->render();
     			
     			if($updateRecord > 0){
     				$result = true;
     			}
     		}
     		 
     		if($result != false){
     	
     			$this->ajaxResponse(1, $successMessage, $data );
     		}else {
     			 
     			$this->ajaxResponse(101, $errorMessages);
     		}
     	}
     }
     public function editAddressModel(Request $request){
     	$data = $whereData = [];
     	$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0 );
     	if($employeeId > 0 ){
     		$whereData['master_id'] = $employeeId;
     		$whereData['singleRecord'] = true;
     		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     			$whereData['show_all'] = true;
     		}
     		$recordDetails = $this->crudModel->getRecordDetails($whereData);
     		if(!empty($recordDetails)){
     			$data['employeeRecordInfo'] = $recordDetails;
     		}
     	}
     	$data['cityRecordDetails'] = CityMasterModel::orderBy('v_city_name', 'ASC')->get();
     	$data['contryRecordDetails'] = CountryMasterModel::orderBy('v_country_name', 'ASC')->get();
     	$data['stateRecordDetails'] = StateMasterModel::orderBy('v_state_name', 'ASC')->get();
     	$data['villageRecordDetails'] = VillageMasterModel::with(['cityMaster.stateMaster.countryMaster'])->orderBy('v_village_name', 'ASC')->get();
     	
     	
     	$html = view ($this->folderName . 'add-address-model')->with ( $data )->render();
     	echo $html;die;
     }
     public function editBankDetails(Request $request){
     	$data = $whereData = [];
     	$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0 );
     	if($employeeId > 0 ){
     		$whereData['master_id'] = $employeeId;
     		$whereData['singleRecord'] = true;
     		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     			$whereData['show_all'] = true;
     		}
     		$recordDetails = $this->crudModel->getRecordDetails($whereData);
     		if(!empty($recordDetails)){
     			$data['employeeRecordInfo'] = $recordDetails;
     		}
     	}
     	$bankWhereData = [] ;
     	$bankWhereData['t_is_active'] = 1;
     	if ($employeeId > 0){
     		unset($bankWhereData['t_is_active']);
     	}
     	
     	$data['bankRecordDetails'] = LookupMaster::where($bankWhereData)->where('t_is_deleted',0)->where('v_module_name',config ( 'constants.BANK_LOOKUP'))->orderBy('v_value', 'ASC')->get();
     	$html = view ($this->folderName . 'add-bank-details-model')->with ( $data )->render();
     	echo $html;die;
     }
     public function addBankDetails(Request $request){
     
     	if(!empty($request->post())){
     
     		$successMessage =  trans('messages.success-update',['module'=> trans('messages.bank-details')]);
     		$errorMessages = trans('messages.error-update',['module'=> trans('messages.bank-details')]);
     
     		$result = false;
     		$html = null;
     		$employeeId = (!empty($request->post('employee_record_id')) ? (int)Wild_tiger::decode($request->post('employee_record_id')) : 0 );
     
     		if($employeeId > 0 ){
     			$recordData = [];
     			$recordData['i_bank_id'] = (!empty($request->post('bank_name')) ? (int)Wild_tiger::decode($request->post('bank_name')) : 0);
     			$recordData['v_bank_account_no'] = (!empty($request->post('account_number')) ? trim($request->post('account_number')) : null);
     			$recordData['v_bank_account_ifsc_code'] = (!empty($request->post('ifsc_code')) ? trim($request->post('ifsc_code')) :null);
     			
     			$updateRecord = $this->crudModel->updateTableData( $this->tableName , $recordData,['i_id' => $employeeId ]);
     			
     			$empWhere = [];
     			$empWhere['master_id'] = $employeeId;
     			$empWhere['singleRecord'] = true;
     			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     				$empWhere['show_all'] = true;
     			}
     			
     			$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
     			$recordInfo = [];
     			$recordInfo['employeeRecordInfo'] = $recordDetail;
     			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/bank-details-list')->with ( $recordInfo )->render();
     
     			if($updateRecord > 0){
     				$result = true;
     			}
     		}
     
     		if($result != false){
     
     			$this->ajaxResponse(1, $successMessage, ['html' => $html] );
     		}else {
     			 
     			$this->ajaxResponse(101, $errorMessages);
     		}
     	}
     }
     public function editIdentityPfAccountDetails(Request $request){
     	$data = $whereData = [];
     	$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0 );
     	if($employeeId > 0 ){
     		$whereData['master_id'] = $employeeId;
     		$whereData['singleRecord'] = true;
     		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     			$whereData['show_all'] = true;
     		}
     		$recordDetails = $this->crudModel->getRecordDetails($whereData);
     		if(!empty($recordDetails)){
     			$data['employeeRecordInfo'] = $recordDetails;
     		}
     	}
     	$html = view ($this->folderName . 'add-identity-account-model')->with ( $data )->render();
     	echo $html;die;
     }
     public function addIdentityPfAccountDetails(Request $request){
     	 
     	if(!empty($request->post())){
     		 
     		$formValidation =[];
     		$formValidation['aadhaar_number'] = ['required','max:14'];
     		
     		$validator = Validator::make ( $request->all (), $formValidation , [
     				'aadhaar_number.required' => __ ( 'messages.require-enter-aadhaar-number' ),
     		] );
     		if ($validator->fails ()) {
     			$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.identity-pf-account-information') ] ) ) );
     		}
     	
     		$successMessage =  trans('messages.success-update',['module'=> trans('messages.identity-pf-account-information')]);
     		$errorMessages = trans('messages.error-update',['module'=> trans('messages.identity-pf-account-information')]);
     	
     		$result = false;
     		$html = null;
     		$employeeId = (!empty($request->post('employee_record_id')) ? (int)Wild_tiger::decode($request->post('employee_record_id')) : 0 );
     		 
     		if($employeeId > 0 ){
     			$recordData = [];
     			$recordData['v_aadhar_no'] = (!empty($request->post('aadhaar_number')) ? trim($request->post('aadhaar_number')) : '');
     			$recordData['v_pan_no'] = (!empty($request->post('pan_no')) ? trim($request->post('pan_no')) : null);
     			$recordData['v_uan_no'] = (!empty($request->post('uan_number')) ? trim($request->post('uan_number')) :null);
     			
     			$updateRecord = $this->crudModel->updateTableData( $this->tableName , $recordData,['i_id' => $employeeId ]);
     			
     			
     			$empWhere = [];
     			$empWhere['master_id'] = $employeeId;
     			$empWhere['singleRecord'] = true;
     			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     				$empWhere['show_all'] = true;
     			}
     			
     			$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
     			$recordInfo = [];
     			$recordInfo['employeeRecordInfo'] = $recordDetail;
     			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/identity-list')->with ( $recordInfo )->render();
     			
     			if($updateRecord > 0){
     				$result = true;
     			}
     		}
     		 
     		if($result != false){
     	
     			$this->ajaxResponse(1, $successMessage, ['html' => $html] );
     		}else {
     			 
     			$this->ajaxResponse(101, $errorMessages);
     		}
     	}
     }
    
     public function editJobDetails(Request $request){
     	$data = $whereData = [];
     	$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0 );
     	if($employeeId > 0 ){
     		$whereData['master_id'] = $employeeId;
     		$whereData['singleRecord'] = true;
     		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     			$whereData['show_all'] = true;
     		}
     		$recordDetails = $this->crudModel->getRecordDetails($whereData);
     		if(!empty($recordDetails)){
     			$data['employeeRecordInfo'] = $recordDetails;
     		}
     	}
     	$data['getSelectionYesNoRecordInfo'] = getSelectionYesNoRecordInfo();
     	
     	$leaderWhere = [];
     	$leaderWhere['t_is_deleted'] = 0;
     	$data['ledaerEmployeeRecords'] = EmployeeModel::where($leaderWhere)->where('i_id' , '!=' , $employeeId )->where('e_employment_status' , '!=' , config('constants.RELIEVED_EMPLOYMENT_STATUS') )->where('t_is_deleted',0)->orderBy('v_employee_full_name', 'ASC')->get();
     	$data['noticePeriodPolicyRecordDetails'] = ProbationPolicyMasterModel::where('e_record_status',config ( 'constants.NOTICE_PERIOD_POLICY'))->where('t_is_deleted',0)->orderBy('v_probation_policy_name', 'ASC')->get();
     	$data['recruitmentSourceDetails'] = LookupMaster::where('v_module_name',config('constants.RECRUITMENT_SOURCE_LOOKUP'))->orderBy('v_value', 'ASC')->get();
     	$html = view ($this->folderName . 'add-job-details-model')->with ( $data )->render();
     	echo $html;die;
     }
     
     public function addJobDetails(Request $request){
     	 
     	if(!empty($request->post())){
     		$employeeId = (!empty($request->post('employee_record_id')) ? (int)Wild_tiger::decode($request->post('employee_record_id')) : 0 );
     		$formValidation =[];
     		$formValidation['joining_date_edit'] = ['required'];
     		$formValidation['edit_week_off_effective_date'] = ['required'];
     		//$formValidation['leader_name_reporting_manager'] = ['required'];
     		//$formValidation['in_probation'] = ['required'];
     		$formValidation['notice_period'] = ['required'];
     		$formValidation['recruitment_source'] = ['required'];
     		//$formValidation['employee_code'] = ['required' ,new UniqueEmployeeCode($employeeId)];
     		
     		 
     		$validator = Validator::make ( $request->all (), $formValidation , [
     				'joining_date_edit.required' => __ ( 'messages.require-enter-joining-date' ),
     				'edit_week_off_effective_date.required' => __ ( 'messages.require-week-off-effective-date' ),
     				'leader_name_reporting_manager.required' => __ ( 'messages.require-select-leader-name-reporting-manager' ),
     				'in_probation.required' => __ ( 'messages.require-select-probation' ),
     				'notice_period.required' => __ ( 'messages.require-select-notice-period' ),
     				'employee_code.required' => __ ( 'messages.require-employee-code' ),
     				'recruitment_source.required' => __ ( 'messages.require-select-recruitment-source' ),
     				 
     		] );
     		if ($validator->fails ()) {
     			$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.job') ] ) ) );
     		}
     
     		$successMessage =  trans('messages.success-update',['module'=> trans('messages.job')]);
     		$errorMessages = trans('messages.error-update',['module'=> trans('messages.job')]);
     
     		$result = false;
     		$html = null;
     		
     		if($employeeId > 0 ){
     			
     			
     			
     			
     			$recordData = [];
     			//$recordData['v_employee_code'] = (!empty($request->post('employee_code')) ? trim($request->post('employee_code')) : null );
     			$recordData['dt_joining_date'] = (!empty($request->post('joining_date_edit')) ? dbDate($request->post('joining_date_edit')) : '');
     			$recordData['dt_week_off_effective_date'] = (!empty($request->post('edit_week_off_effective_date')) ? dbDate($request->post('edit_week_off_effective_date')) : null);
     			$recordData['i_leader_id'] = (!empty($request->post('leader_name_reporting_manager')) ? (int)Wild_tiger::decode($request->post('leader_name_reporting_manager')) : 0 );
     			//$recordData['e_in_probation'] = (!empty($request->post('in_probation')) ? trim($request->post('in_probation')) : null);
     			$recordData['i_notice_period_id'] = (!empty($request->post('notice_period')) ? (int)Wild_tiger::decode($request->post('notice_period')) : 0 );
     			$recordData['i_recruitment_source_id'] = (!empty($request->post('recruitment_source')) ? (int)Wild_tiger::decode($request->post('recruitment_source')) : 0 );
     			$recordData['i_reference_emp_id'] = null;
     			
     			if( $recordData['i_recruitment_source_id'] ==  config('constants.EMPLOYEE_RECRUITMENT_SOURCE_ID') ){
     				$recordData['i_reference_emp_id'] = (!empty($request->post('reference_name')) ? (int)Wild_tiger::decode($request->post('reference_name')) : null );
     			}
     			
     			$whereData = [];
     			$whereData['master_id'] = $employeeId;
     			$whereData['singleRecord'] = true;
     			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     				$whereData['show_all'] = true;
     			}
     			$employeeInfo = $this->crudModel->getRecordDetails( $whereData );
     			
     			//echo "<pre>";print_r($employeeInfo);
     			//echo "<pre>";print_r($recordData);die;
     			
     			if( (!empty($employeeInfo)) && ( strtotime($recordData['dt_joining_date']) != strtotime($employeeInfo->dt_joining_date) )  &&  ( ( strtotime($employeeInfo->dt_last_update_designation) != strtotime($employeeInfo->dt_joining_date) ) || ( strtotime($employeeInfo->dt_last_update_team) != strtotime($employeeInfo->dt_joining_date) )  || ( strtotime($employeeInfo->dt_last_update_shift) != strtotime($employeeInfo->dt_joining_date) ) ) ){
     				//echo "1";die;
     				$this->ajaxResponse(101, trans('messages.error-date-related-issue'));
     			}
     			
     			if( (!empty($employeeInfo)) && ( strtotime($recordData['dt_week_off_effective_date']) != strtotime($employeeInfo->dt_week_off_effective_date) )  &&  ( ( strtotime($employeeInfo->dt_week_off_effective_date) != strtotime($employeeInfo->dt_last_update_week_off) ) ) ){
     				//echo "2";die;
     				$this->ajaxResponse(101, trans('messages.error-date-related-issue'));
     			}
     			
     			if( ( strtotime($recordData['dt_joining_date']) != strtotime($employeeInfo->dt_joining_date) ) ){
     				$recordData['dt_last_update_designation'] = $recordData['dt_joining_date'];
     				$recordData['dt_last_update_team'] = $recordData['dt_joining_date'];
     				//$recordData['dt_last_update_week_off'] = $recordData['dt_joining_date'];
     				$recordData['dt_last_update_shift'] = $recordData['dt_joining_date'];
     				
     				$getDesignationHistoryLastRecord = $this->crudModel->getSingleRecordById(config('constants.EMPLOYEE_DESIGNATION_HISTORY') , [ 'i_id' ] , [ 't_is_deleted' => 0 , 'e_record_type' => config('constants.DESIGNATION_LOOKUP') , 'i_employee_id' => $employeeId , 'order_by' => [ 'i_id' => 'desc' ] ] );
     				
     				if(!empty($getDesignationHistoryLastRecord)){
     					$this->crudModel->updateTableData(config('constants.EMPLOYEE_DESIGNATION_HISTORY'), ['dt_start_date' =>  $recordData['dt_joining_date'] ], [ 'i_id' => $getDesignationHistoryLastRecord->i_id  ] );
     				}
     				
     				$getTeamHistoryLastRecord = $this->crudModel->getSingleRecordById(config('constants.EMPLOYEE_DESIGNATION_HISTORY') , [ 'i_id' ] , [ 't_is_deleted' => 0 , 'e_record_type' => config('constants.TEAM_LOOKUP') , 'i_employee_id' => $employeeId , 'order_by' => [ 'i_id' => 'desc' ] ] );
     				 
     				if(!empty($getTeamHistoryLastRecord)){
     					$this->crudModel->updateTableData(config('constants.EMPLOYEE_DESIGNATION_HISTORY'), ['dt_start_date' =>  $recordData['dt_joining_date'] ], [ 'i_id' => $getTeamHistoryLastRecord->i_id  ] );
     				}
     				
     				$getShiftHistoryLastRecord = $this->crudModel->getSingleRecordById(config('constants.EMPLOYEE_DESIGNATION_HISTORY') , [ 'i_id' ] , [ 't_is_deleted' => 0 , 'e_record_type' => config('constants.SHIFT_RECORD_TYPE') , 'i_employee_id' => $employeeId , 'order_by' => [ 'i_id' => 'desc' ] ] );
     				 
     				if(!empty($getShiftHistoryLastRecord)){
     					$this->crudModel->updateTableData(config('constants.EMPLOYEE_DESIGNATION_HISTORY'), ['dt_start_date' =>  $recordData['dt_joining_date'] ], [ 'i_id' => $getShiftHistoryLastRecord->i_id  ] );
     				}
     				
     				$getAssignSalaryInfo = ReviseSalaryMaster::where('i_employee_id' , $employeeId )->orderBy('i_id' , 'desc')->first();
     				 
     				if( (!empty($getAssignSalaryInfo)) &&  isset($getAssignSalaryInfo->dt_effective_date) && ( strtotime($getAssignSalaryInfo->dt_effective_date) == strtotime($employeeInfo->dt_joining_date) ) ){
     					$this->crudModel->updateTableData(config('constants.REVISE_SALARY_MASTER_TABLE'), ['dt_effective_date' =>  $recordData['dt_joining_date'] ], [ 'i_id' => $getAssignSalaryInfo->i_id  ] );
     				}
     				
     				
     			}
     			
     			if( ( strtotime($recordData['dt_week_off_effective_date']) != strtotime($employeeInfo->dt_week_off_effective_date) ) ){
     				$getWeekOffHistoryLastRecord = $this->crudModel->getSingleRecordById(config('constants.EMPLOYEE_DESIGNATION_HISTORY') , [ 'i_id' ] , [ 't_is_deleted' => 0 , 'e_record_type' => config('constants.WEEK_OFF_RECORD_TYPE') , 'i_employee_id' => $employeeId , 'order_by' => [ 'i_id' => 'desc' ] ] );
     				$recordData['dt_last_update_week_off'] = $recordData['dt_week_off_effective_date'];
     				if(!empty($getWeekOffHistoryLastRecord)){
     					$this->crudModel->updateTableData(config('constants.EMPLOYEE_DESIGNATION_HISTORY'), ['dt_start_date' =>  $recordData['dt_week_off_effective_date'] ], [ 'i_id' => $getWeekOffHistoryLastRecord->i_id  ] );
     				}
     				
     			}
     			
     			if( ( strtotime($recordData['dt_joining_date']) != strtotime($employeeInfo->dt_joining_date) ) ){
     				if( $employeeInfo->e_in_probation  == config('constants.SELECTION_YES') && ( $employeeInfo->i_probation_period_id > 0  ) ){
     					$probationInfo = ProbationPolicyMasterModel::where('i_id' , $employeeInfo->i_probation_period_id )->first();
     					$previousProbationDate  = ( isset($employeeInfo->dt_probation_end_date) ? $employeeInfo->dt_probation_end_date : "" );
     					$joiningDate = $recordData['dt_joining_date'];
     					if( (!empty($probationInfo->v_probation_period_duration)) && (!empty($probationInfo->e_months_weeks_days)) ){
     						$duration = ( $probationInfo->v_probation_period_duration . ' ' . $probationInfo->e_months_weeks_days );
     						$probationEndDateAsPerOldJoiningDate = date('Y-m-d' , strtotime("+" . $duration , strtotime($employeeInfo->dt_joining_date)) );
     						if( strtotime($probationEndDateAsPerOldJoiningDate) == strtotime($previousProbationDate) ){
     							$recordData['dt_probation_end_date'] = date('Y-m-d' , strtotime("+" . $duration , strtotime($joiningDate)) );
     						}
     					}
     				} 
     			} 
     			
     			
     			$updateRecord = $this->crudModel->updateTableData( $this->tableName , $recordData,['i_id' => $employeeId ]);
     			
     			$empWhere = [];
     			$empWhere['master_id'] = $employeeId;
     			$empWhere['singleRecord'] = true;
     			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
     			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
     				$empWhere['show_all'] = true;
     			}
     			
     			$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
     			$recordInfo = [];
     			$recordInfo['employeeRecordInfo'] = $recordDetail;
     			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/job-list')->with ( $recordInfo )->render();
     
     			if($updateRecord > 0){
     				$result = true;
     			}
     		}
     
     		if($result != false){
     
     			$this->ajaxResponse(1, $successMessage, ['html' => $html] );
     		}else {
     			 
     			$this->ajaxResponse(101, $errorMessages);
     		}
     	}
     }
     
    /*  public function delete(Request $request){
     	if(!empty($request->input())){
     		$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
     		return $this->removeRecord( config('constants.EMPLOYEE_DOCUMENT_MASTER_DETAILS_TABLE'), $recordId, trans('messages.upload') );
     	}
      } */
      
     
public function addAddressDetails(Request $request){
		
		if(!empty($request->post())){
			$villageId = (!empty($request->post('current_village')) ? (int)Wild_tiger::decode($request->post('current_village')) : 0);
			$permanentVillageId = (!empty($request->post('permanent_village')) ? (int)Wild_tiger::decode($request->post('permanent_village')) : 0);
		
			$formValidation =[];
			$formValidation['address_line_1'] = ['required'];
			//$formValidation['current_city'] = ['required'];
			$formValidation['current_state'] = ['required'];
			$formValidation['current_country'] = ['required'];
			$formValidation['per_address_line_1'] = ['required'];
			//$formValidation['per_city'] = ['required'];
			$formValidation['per_state'] = ['required'];
			$formValidation['per_country'] = ['required'];
			
			if (empty($villageId)){
				$formValidation['current_city'] = ['required'];
			}
			if (empty($permanentVillageId)){
				$formValidation['per_city'] = ['required'];
			}
		
			$validator = Validator::make ( $request->all (), $formValidation , [
					'address_line_1.required' => __ ( 'messages.require-enter-address-line-1' ),
					'current_city.required' => __ ( 'messages.require-select-city' ),
					'current_state.required' => __ ( 'messages.require-select-state' ),
					'current_country.required' => __ ( 'messages.require-select-country' ),
					'per_address_line_1.required' => __ ( 'messages.require-enter-address-line-1' ),
					'per_city.required' => __ ( 'messages.require-select-city' ),
					'per_state.required' => __ ( 'messages.require-select-state' ),
					'per_country.required' => __ ( 'messages.require-select-country' ),
		
			] );
			if ($validator->fails ()) {
				$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.address') ] ) ) );
			}
		
			$successMessage =  trans('messages.success-update',['module'=> trans('messages.address')]);
			$errorMessages = trans('messages.error-update',['module'=> trans('messages.address')]);
		
			$result = false;
			$employeeId = (!empty($request->post('employee_record_id')) ? (int)Wild_tiger::decode($request->post('employee_record_id')) : 0 );
		
			if($employeeId > 0 ){
				$recordData = [];
				$recordData['v_current_address_line_first'] = (!empty($request->post('address_line_1')) ? trim($request->post('address_line_1')) : '');
				$recordData['v_current_address_line_second'] = (!empty($request->post('address_line_2')) ? trim($request->post('address_line_2')) : '');
				$recordData['i_current_address_city_id'] = (!empty($request->post('current_city')) ? (int)Wild_tiger::decode($request->post('current_city')):0);
				$recordData['v_current_address_pincode'] = (!empty($request->post('current_pincode')) ? trim($request->post('current_pincode')) : '');
				$recordData['v_permanent_address_line_first'] = (!empty($request->post('per_address_line_1')) ? trim($request->post('per_address_line_1')) : '');
				$recordData['v_permanent_address_line_second'] = (!empty($request->post('per_address_line_2')) ? trim($request->post('per_address_line_2')) : '');
				$recordData['i_permanent_address_city_id'] = (!empty($request->post('per_city')) ? (int)Wild_tiger::decode($request->post('per_city')):0);
				$recordData['v_permanent_address_pincode'] = (!empty($request->post('per_pincode')) ? trim($request->post('per_pincode')) :null);
				$recordData['i_current_village_id'] = $villageId ;
				$recordData['i_permanent_village_id'] = $permanentVillageId ;
				$recordData['e_same_current_address'] = (!empty($request->post('same_current_address')) ? trim($request->post('same_current_address')) :null);
				
				$updateRecord = $this->crudModel->updateTableData( $this->tableName , $recordData,['i_id' => $employeeId ]);
		
				$empWhere = [];
				$empWhere['master_id'] = $employeeId;
				$empWhere['singleRecord'] = true;
				$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
				if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
					$empWhere['show_all'] = true;
				}
				
				$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
				$recordInfo = $data = [];
				$recordInfo['employeeRecordInfo'] = $recordDetail;
				$recordInfo['empId'] = Wild_tiger::encode($employeeId);
				$data['addressRecordInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/address-list')->with ( $recordInfo )->render();
				$data['mainProfileInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/main-profile-info')->with ( $recordInfo )->render();
				
				if($updateRecord > 0){
					$result = true;
				}
			}
		
			if($result != false){
		
				$this->ajaxResponse(1, $successMessage, $data );
			}else {
					
				$this->ajaxResponse(101, $errorMessages);
			}
		}
	}
	
	/* public function getEmployeeDocumentList( Request $request){
		if(!empty($request->all())){
			
			$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0 );
			
			if( $employeeId > 0 ){
				$data ['documentRecordDetails'] = $this->documentTypeModel->getRecordDetails();
				$data['employeeId'] = $employeeId;
				$html = view (  config('constants.AJAX_VIEW_FOLDER') . 'employee-master/document-info' )->with ( $data )->render();
				echo $html;die;
			}
			
			
		}	
	}   */
	
	public function documentDelete(Request $request){
		if(!empty($request->input())){
			$recordId = (!empty($request->input('document_record_id')) ? (int)Wild_tiger::decode( $request->input('document_record_id') ) : 0 );
			$recordData = [] ;
			$recordData ['t_is_active'] = 0;
			$recordData ['t_is_deleted'] = 1;
	
			$successMessage =  trans('messages.success-delete-document',['module'=> trans('messages.document')]);
			$errorMessages = trans('messages.error-delete',['module'=> trans('messages.document')]);
	
			$getRecordInfo = $this->crudModel->getSingleRecordById(config('constants.EMPLOYEE_DOCUMENT_MASTER_DETAILS_TABLE'), [ 'i_id' , 'v_document_file' ] , [  'i_id' => $recordId ] );
			
			$deletedRecord = $this->crudModel->deleteTableData ( config('constants.EMPLOYEE_DOCUMENT_MASTER_DETAILS_TABLE') , $recordData, ['i_id' => $recordId	] );
			if( $deletedRecord != false ){
				
				if( (!empty($getRecordInfo)) && (!empty($getRecordInfo->v_document_file)) &&  file_exists(config('constants.FILE_STORAGE_PATH').config('constants.UPLOAD_FOLDER') . $getRecordInfo->v_document_file) ){
					unlink(config('constants.FILE_STORAGE_PATH').config('constants.UPLOAD_FOLDER') . $getRecordInfo->v_document_file);
				}
				
				$this->ajaxResponse(1, $successMessage);
			} else {
				$this->ajaxResponse(101, $errorMessages);
			}
		}
	}
	
	public function getGroupComponent(Request $request){
		if(!empty($request->input())){
			
			$salaryGroupdId = (!empty($request->post('salary_group_id')) ? (int)Wild_tiger::decode($request->post('salary_group_id')) : 0 );
			
			if( $salaryGroupdId > 0 ){
				$salaryComponentWhere = [];
				$salaryComponentWhere['t_is_deleted'] = 0;
				$salaryComponentWhere['t_is_active'] = 1;
				$salaryComponentWhere['i_salary_group_id'] = $salaryGroupdId;
				$salaryComponentDetails = SalaryGroupDetailsModel::with(['salaryComponentInfo'])->whereHas('salaryComponentInfo', function($q){
					$q->where('t_is_active', 1);
					$q->where('t_is_deleted', 0);
				})->where($salaryComponentWhere)->get()->sortBy('salaryComponentInfo.i_sequence',SORT_REGULAR);
				//$salaryComponentDetails = SalaryGroupDetailsModel::where($salaryComponentWhere)->get();
				$data['salaryComponentDetails'] = $salaryComponentDetails;
				//echo "<pre>";print_r($salaryComponentDetails);die;
				$html = view (  config('constants.AJAX_VIEW_FOLDER') . 'employee-master/salary-break-up' )->with ( $data )->render();
				echo $html;die;
			}
			
		}
	}
	
	public function getSalaryGroup(Request $request){
		if(!empty($request->input())){
				
			$pdDeduct = (!empty($request->post('deduction_employer_from_employee')) ? trim($request->post('deduction_employer_from_employee')) : config('constants.SELECTION_NO') );
				
			if(!empty($pdDeduct)){
				$salaryGroupWhere = [];
				$salaryGroupWhere['t_is_deleted'] = 0;
				$salaryGroupWhere['t_is_active'] = 1;
				
				if( $pdDeduct == config('constants.SELECTION_YES') ){
					$salaryGroupWhere['custom_function'][] = "( find_in_set( '".config("constants.PF_SALARY_COMPONENT_ID")."' , v_salary_components_earnings_ids ) or  find_in_set( '".config("constants.PF_SALARY_COMPONENT_ID")."' , v_salary_components_deduction_ids ) )";
				}
				
				$salaryGroupDetails = $this->crudModel->selectData( config('constants.SALARY_GROUP_MASTER_TABLE') , [ 'v_group_name' , 'i_id' ] , $salaryGroupWhere  ) ;
				$html = '<option value="">'.trans('messages.select').'</option>';
				if(!empty($salaryGroupDetails)){
					foreach($salaryGroupDetails as $salaryGroupDetail){
						$encodeSalaryGroupId = Wild_tiger::encode($salaryGroupDetail->i_id);
						$html .= '<option value="'.$encodeSalaryGroupId.'" data-id="'.$salaryGroupDetail->i_id.'">'.$salaryGroupDetail->v_group_name.'</option>';
					}
				}
				echo $html;die;
			}
				
		}
	}
	
	public function getEmployeeDesignationInfo(Request $request){
		if(!empty($request->input())){
			
			$employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			
			$lookupWhere= [];
			$lookupWhere['v_module_name'] = config('constants.DESIGNATION_LOOKUP');
			$lookupWhere['t_is_active'] = 1;
			$data['designationDetails'] = LookupMaster::where($lookupWhere)->orderBy('v_value' , 'asc')->get();
			
			$getEmployeeWhere  = [];
			$getEmployeeWhere['master_id'] = $employeeId;
			$getEmployeeWhere['singleRecord'] = true;
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$getEmployeeWhere['show_all'] = true;
			}
			$data['recordInfo'] =  $this->crudModel->getRecordDetails($getEmployeeWhere);
			
			//echo "<pre>";print_r($data['recordInfo']);die;
			
			/* $checkUpdateRequestWhere = [];
			$checkUpdateRequestWhere['i_employee_id']  = $employeeId;
			$checkUpdateRequestWhere['e_record_type']  = config('constants.DESIGNATION_LOOKUP');
			$checkUpdateRequestWhere['t_is_updated']  = 0;
				
			$checkUpdateRequestExist = EmployeeDataUpdateRequest::where($checkUpdateRequestWhere)->first();
			$data['existingRecordInfo'] = $checkUpdateRequestExist; */
			
			$html = view ($this->folderName . 'employee-designation-info')->with ( $data )->render();
			echo $html;die;
		}
	}
	
	public function getEmployeeTeamInfo(Request $request){
		if(!empty($request->input())){
				
			$employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			$lookupWhere = [];
			$lookupWhere['v_module_name'] = config('constants.TEAM_LOOKUP');
			$lookupWhere['t_is_active'] = 1;
			$data['teamDetails'] = LookupMaster::where($lookupWhere)->orderBy('v_value' , 'asc')->get();
				
			$getEmployeeWhere  = [];
			$getEmployeeWhere['master_id'] = $employeeId;
			$getEmployeeWhere['singleRecord'] = true;
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$getEmployeeWhere['show_all'] = true;
			}
			$data['recordInfo'] =  $this->crudModel->getRecordDetails($getEmployeeWhere);
				
			$checkUpdateRequestWhere = [];
			$checkUpdateRequestWhere['i_employee_id']  = $employeeId;
			$checkUpdateRequestWhere['e_record_type']  = config('constants.TEAM_LOOKUP');
			$checkUpdateRequestWhere['t_is_updated']  = 0;
	
			$checkUpdateRequestExist = EmployeeDataUpdateRequest::where($checkUpdateRequestWhere)->first();
			$data['existingRecordInfo'] = $checkUpdateRequestExist;
				
			$html = view ($this->folderName . 'employee-team-info')->with ( $data )->render();
			echo $html;die;
		}
	}
	
	public function getEmployeeShiftInfo(Request $request){
		if(!empty($request->input())){
	
			$employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			$shiftWhere = [];
			$shiftWhere['t_is_active'] = 1;
			$data['shiftDetails'] = ShiftMasterModel::where($shiftWhere)->orderBy('v_shift_name' , 'asc')->get();
	
			$getEmployeeWhere  = [];
			$getEmployeeWhere['master_id'] = $employeeId;
			$getEmployeeWhere['singleRecord'] = true;
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$getEmployeeWhere['show_all'] = true;
			}
			$data['recordInfo'] =  $this->crudModel->getRecordDetails($getEmployeeWhere);
	
			$checkUpdateRequestWhere = [];
			$checkUpdateRequestWhere['i_employee_id']  = $employeeId;
			$checkUpdateRequestWhere['e_record_type']  = config('constants.SHIFT_RECORD_TYPE');
			$checkUpdateRequestWhere['t_is_updated']  = 0;
	
			$checkUpdateRequestExist = EmployeeDataUpdateRequest::where($checkUpdateRequestWhere)->first();
			$data['existingRecordInfo'] = $checkUpdateRequestExist;
	
			$html = view ($this->folderName . 'employee-shift-info')->with ( $data )->render();
			echo $html;die;
		}
	}
	
	public function getEmployeeWeekOffInfo(Request $request){
		if(!empty($request->input())){
	
			$employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			$weekOffWhere = [];
			$weekOffWhere['t_is_active'] = 1;
			$data['weekOffDetails'] = WeeklyOffMasterModel::where($weekOffWhere)->orderBy('v_weekly_off_name' , 'asc')->get();
	
			$getEmployeeWhere  = [];
			$getEmployeeWhere['master_id'] = $employeeId;
			$getEmployeeWhere['singleRecord'] = true;
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$getEmployeeWhere['show_all'] = true;
			}
			$data['recordInfo'] =  $this->crudModel->getRecordDetails($getEmployeeWhere);
	
			$checkUpdateRequestWhere = [];
			$checkUpdateRequestWhere['i_employee_id']  = $employeeId;
			$checkUpdateRequestWhere['e_record_type']  = config('constants.TIME_OFF_RECORD_TYPE');
			$checkUpdateRequestWhere['t_is_updated']  = 0;
	
			$checkUpdateRequestExist = EmployeeDataUpdateRequest::where($checkUpdateRequestWhere)->first();
			$data['existingRecordInfo'] = $checkUpdateRequestExist;
	
			$html = view ($this->folderName . 'employee-week-off-info')->with ( $data )->render();
			echo $html;die;
		}
	}
	
	
	
	public function updateEmployeeDataInfo(Request $request){
		if(!empty($request->input())){
			
			//echo "<pre>";print_r($request->all());
			
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
			$designationId = (!empty($request->input('update_data_value')) ? (int)Wild_tiger::decode($request->input('update_data_value')) : 0 );
			$effectiveDate = (!empty($request->input('effective_date')) ? dbDate($request->input('effective_date')) : null );
			$updateRequestModule = (!empty($request->input('update_request')) ? trim($request->input('update_request')) : null );
			$historyRecordId = (!empty($request->input('history_record_id')) ? (int)Wild_tiger::decode($request->input('history_record_id')) : 0 );
			
			$getEmployeeWhere  = [];
			$getEmployeeWhere['master_id'] = $employeeId;
			$getEmployeeWhere['singleRecord'] = true;
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$getEmployeeWhere['show_all'] = true;
			}
			$employeeInfo =  $this->crudModel->getRecordDetails($getEmployeeWhere);
			
			
			
			$designationHistoryData = [];
			
			$employeeDateColumnName = "";
			$employeeRecordColumnName = "";
			switch($updateRequestModule){
				case config('constants.DESIGNATION_LOOKUP'):
					$employeeDateColumnName =  'dt_last_update_designation';
					$employeeRecordColumnName = "i_designation_id";
					break;
				case config('constants.TEAM_LOOKUP'):
					$employeeDateColumnName =  'dt_last_update_team';
					$employeeRecordColumnName = "i_team_id";
					break;
				case config('constants.SHIFT_RECORD_TYPE'):
					$employeeDateColumnName =  'dt_last_update_shift';
					$employeeRecordColumnName = "i_shift_id";
					break;
				case config('constants.WEEK_OFF_RECORD_TYPE'):
					$employeeDateColumnName =  'dt_last_update_week_off';
					$employeeRecordColumnName = "i_weekoff_id";
					break;
			}
			$startDate = null;
			if( $updateRequestModule == config('constants.SHIFT_RECORD_TYPE')){
				if( $historyRecordId > 0 ){
					$startDate = (!empty($request->input('shift_effective_from_date')) ? dbDate($request->input('shift_effective_from_date')) : null );
					$endDate = (!empty($request->input('effective_date')) ? dbDate($request->input('effective_date')) : $startDate );
					/* if(!empty($endDate)){ */
						$allDates = getDatesFromRange( $startDate ,  $endDate );
						$customWhere = "";
						if( !empty($allDates) ){
							$customWhere .= " ( ";
							foreach($allDates as $allDate){
								$customWhere .= " '".$allDate."' between dt_start_date and ifnull( dt_end_date , dt_start_date ) or ";
							}
							$customWhere = rtrim($customWhere , "or ");
							$customWhere .= " ) ";
						}
						if((!empty($customWhere))){
							$checkOverlapWhere = [];
							$checkOverlapWhere['t_is_deleted'] = 0;
							$checkOverlapWhere['e_record_type'] = config('constants.SHIFT_RECORD_TYPE');
							$checkOverlapWhere['i_employee_id'] = $employeeId;
							$checkOverlapWhere['custom_function'][] = $customWhere;
							if( $historyRecordId > 0 ){
								$checkOverlapWhere['i_id != '] = $historyRecordId;
							}
							$checkOverLayExists = $this->crudModel->getSingleRecordById( config('constants.EMPLOYEE_DESIGNATION_HISTORY') , [ 'i_id' ] , $checkOverlapWhere );
							if(!empty($checkOverLayExists)){
								$this->ajaxResponse(101, trans('messages.shift-overlap-issue'));
							}
							//echo $this->crudModel->last_query();
						}
					/* } */
					
					//var_dump($customWhere);
					//echo "<pre>";print_r($allDates);die;
				}
			}
			
			$result = false;
			DB::beginTransaction();
			
			try{
				
				if( $historyRecordId > 0 ){
				
					if( $updateRequestModule == config('constants.SHIFT_RECORD_TYPE')){
						$designationHistoryData['dt_start_date'] = $startDate;
					}
					
					$designationHistoryData['dt_end_date'] = $effectiveDate;
					$this->crudModel->updateTableData( config('constants.EMPLOYEE_DESIGNATION_HISTORY') , $designationHistoryData , [ 'i_id' => $historyRecordId ]  );
				
					$getHistoryWhere = [];
					$getHistoryWhere['i_employee_id'] = $employeeId;
					$getHistoryWhere['e_record_type'] = $updateRequestModule;
					$getHistoryInfo = EmployeeDesignationHistory::where($getHistoryWhere)->whereNull('dt_end_date')->orderBy('i_id' , 'desc')->first();
					
					if(!empty($getHistoryInfo) && $updateRequestModule != config('constants.SHIFT_RECORD_TYPE') ){
						$this->crudModel->updateTableData( config('constants.EMPLOYEE_DESIGNATION_HISTORY') , [ 'dt_start_date' => $effectiveDate ] , [ 'i_id' => $getHistoryInfo->i_id ]  );
					}
					
					if( $updateRequestModule != config('constants.SHIFT_RECORD_TYPE')){
						$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , [ $employeeDateColumnName => $effectiveDate , $employeeRecordColumnName => $designationId ] , [ 'i_id' => $employeeId ]  );
					} else {
						if( (!empty($getHistoryInfo)) ){
							if (( strtotime(date('Y-m-d')) >= strtotime( $getHistoryInfo->dt_start_date ) ) && ( strtotime( date('Y-m-d') ) <= strtotime( $getHistoryInfo->dt_end_date ))){
								$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , [ $employeeDateColumnName => $effectiveDate , $employeeRecordColumnName => $designationId ] , [ 'i_id' => $employeeId ]  );
							}
						}
					}
					
					
				} else {
				
					$getHistoryWhere = [];
					$getHistoryWhere['i_employee_id'] = $employeeId;
					$getHistoryWhere['e_record_type'] = $updateRequestModule;
					$getHistoryInfo = EmployeeDesignationHistory::where($getHistoryWhere)->whereNull('dt_end_date')->orderBy('i_id' , 'desc')->first();
					//var_dump($effectiveDate);
					//echo "<pre>";print_r($getHistoryInfo);
					
					if(!empty($getHistoryInfo)){
						
						if(  strtotime($getHistoryInfo->dt_start_date) > strtotime($effectiveDate) ){
							DB::rollback();
							$this->ajaxResponse(101, trans('messages.system-error'));
						}
						if( $updateRequestModule != config('constants.SHIFT_RECORD_TYPE')){
							$this->crudModel->updateTableData( config('constants.EMPLOYEE_DESIGNATION_HISTORY') , [ 'dt_end_date' => $effectiveDate ] , [ 'i_id' => $getHistoryInfo->i_id ]  );
						} else {
							if( strtotime($effectiveDate) <= strtotime(date('Y-m-d'))){
								$this->crudModel->updateTableData( config('constants.EMPLOYEE_DESIGNATION_HISTORY') , [ 'dt_end_date' => date( 'Y-m-d' , strtotime("-1 days" , strtotime($effectiveDate) ) ) ] , [ 'i_id' => $getHistoryInfo->i_id ]  );
							}	
						}
						
					}
				
					$designationHistoryData['i_employee_id'] = $employeeId;
					$designationHistoryData['i_designation_id'] = $designationId;
					$designationHistoryData['dt_start_date'] = $effectiveDate;
					$designationHistoryData['e_record_type'] = $updateRequestModule;
				
					$this->crudModel->insertTableData( config('constants.EMPLOYEE_DESIGNATION_HISTORY') , $designationHistoryData  );
				
					if( $updateRequestModule != config('constants.SHIFT_RECORD_TYPE')){
						$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , [ $employeeDateColumnName => $effectiveDate , $employeeRecordColumnName => $designationId   ] , [ 'i_id' => $employeeId ]  );
					} else {
						if( strtotime($effectiveDate) <= strtotime(date('Y-m-d'))){
							$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , [ $employeeDateColumnName => $effectiveDate , $employeeRecordColumnName => $designationId   ] , [ 'i_id' => $employeeId ]  );
						}	
					}
					
					
					
					
						
				}
				$result = true;
			}catch(\Exception $e){
				var_dump($e->getMessage());die;
				DB::rollback();
			}
			
			$empWhere = [];
			$empWhere['master_id'] = $employeeId;
			$empWhere['singleRecord'] = true;
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$empWhere['show_all'] = true;
			}
			
			$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
			$recordInfo = [];
			$recordInfo['employeeRecordInfo'] = $recordDetail;
			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/job-list')->with ( $recordInfo )->render();
			
			$successMessage = trans('messages.success-update' , [ 'module' => enumText($updateRequestModule)  ] ) ;
			$errorMessage = trans('messages.error-update' , [ 'module' => enumText($updateRequestModule) ] ) ;
			
			//var_dump($result);die;
			
			if( $result != false ){
				DB::Commit();
				$this->ajaxResponse(1, $successMessage , [ 'html' => $html  ]);
			} 
			
			DB::rollback();
			$this->ajaxResponse(101, $errorMessage);
			
			
			
			
			
			$html = view ($this->folderName . 'employee-designation-info')->with ( $data )->render();
			echo $html;die;
		}
		
	}
	
	public function getEmployeeDesignationHistory(Request $request){
		
		if(!empty($request->input())){
		
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
			$recordType = (!empty($request->input('record_type')) ? trim($request->input('record_type')) : config('constants.DESIGNATION_LOOKUP') );
			
			$getHistoryWhere = [];
			$getHistoryWhere['i_employee_id'] = $employeeId;
			$getHistoryWhere['e_record_type'] = $recordType;
			if( $recordType == config('constants.SHIFT_RECORD_TYPE')){
				$getHistoryDetails = EmployeeDesignationHistory::where($getHistoryWhere)->orderBy('i_id' , 'desc')->get();
			} else {
				$getHistoryDetails = EmployeeDesignationHistory::where($getHistoryWhere)->whereNotNull('dt_end_date')->orderBy('i_id' , 'desc')->get();
			}
			
			
			//echo "<pre>";print_r($getHistoryDetails);die;
			
			$html = "";
			if(!empty($getHistoryDetails)){
				$historyIndex = 0;
				foreach($getHistoryDetails as $getHistoryKey =>  $getHistoryDetail){
					$html .= '<tr>';
					$html .= '<td class="text-center">'.++$historyIndex.'</td>';
					$editButtonHtml = '';
					switch($getHistoryDetail->e_record_type){
						case config('constants.DESIGNATION_LOOKUP'):
							$html .= '<td>'.(  isset($getHistoryDetail->designationInfo->v_value) ? $getHistoryDetail->designationInfo->v_value : ''  ).'</td>';
							$editButtonHtml = '<button type="button" data-end-date="'.$getHistoryDetail->dt_end_date.'" data-start-date="'.$getHistoryDetail->dt_start_date .'" class="btn btn-sm btn-theme text-white" title="'.trans('messages.edit').'" onclick="editJobDesignationHistory(this);" data-record-id="'.Wild_tiger::encode($getHistoryDetail->i_id).'" ><i class="fas fa-pencil-alt"></button></div>';
							break;
						case config('constants.TEAM_LOOKUP'):
							$html .= '<td>'.(  isset($getHistoryDetail->teamInfo->v_value) ? $getHistoryDetail->teamInfo->v_value : ''  ).'</td>';
							$editButtonHtml = '<button type="button" data-end-date="'.$getHistoryDetail->dt_end_date.'" data-start-date="'.$getHistoryDetail->dt_start_date .'" class="btn btn-sm btn-theme text-white" title="'.trans('messages.edit').'" onclick="editTeamHistory(this);" data-record-id="'.Wild_tiger::encode($getHistoryDetail->i_id).'" ><i class="fas fa-pencil-alt"></button></div>';
							break;
						case config('constants.SHIFT_RECORD_TYPE'):
							$html .= '<td>'.(  isset($getHistoryDetail->shiftInfo->v_shift_name) ? $getHistoryDetail->shiftInfo->v_shift_name : ''  ).'</td>';
							$editButtonHtml = '<button type="button" data-end-date="'.$getHistoryDetail->dt_end_date.'" data-start-date="'.$getHistoryDetail->dt_start_date .'" class="btn btn-sm btn-theme text-white" title="'.trans('messages.edit').'" onclick="editShiftHistory(this);" data-record-id="'.Wild_tiger::encode($getHistoryDetail->i_id).'" ><i class="fas fa-pencil-alt"></button></div>';
							break;
						case config('constants.WEEK_OFF_RECORD_TYPE'):
							$html .= '<td>'.(  isset($getHistoryDetail->weeklyOffInfo->v_weekly_off_name) ? $getHistoryDetail->weeklyOffInfo->v_weekly_off_name : ''  ).'</td>';
							$editButtonHtml = '<button type="button" data-end-date="'.$getHistoryDetail->dt_end_date.'" data-start-date="'.$getHistoryDetail->dt_start_date .'" class="btn btn-sm btn-theme text-white" title="'.trans('messages.edit').'" onclick="editWeekOffHistory(this);" data-record-id="'.Wild_tiger::encode($getHistoryDetail->i_id).'" ><i class="fas fa-pencil-alt"></button></div>';
							break;
					}
					
					
					$html .= '<td>'.(  isset($getHistoryDetail->dt_start_date) ? convertDateFormat($getHistoryDetail->dt_start_date) : ''  ).'</td>';
					$html .= '<td>'.(  isset($getHistoryDetail->dt_end_date) ? convertDateFormat($getHistoryDetail->dt_end_date) : ''  ).'</td>';
					
					if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){  
						if(  in_array( $getHistoryKey , [ 0  ] ) || (  $recordType == config('constants.SHIFT_RECORD_TYPE')  ) ){
							$html .= '<td>';
							$html .= '<div class="d-flex align-items-center justify-content-center">';
							$html .= $editButtonHtml;
							$html .= '</td>';
						} else {
							$html .= '<td></td>';
						}
					}
					$html .= '</tr>';
				}
			}
			
			if(empty($html)){
				$html = '<tr><td class="text-center" colspan="5">'.trans('messages.no-record-found').'</td></tr>';
			}
			
			echo $html;die;
		}
	}
	
	public function getDesignationHistoryInfo(Request $request){
		if(!empty($request->input())){
			
			$historyRecordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			
			$getHistoryWhere = [];
			$getHistoryWhere['i_id'] = $historyRecordId;
			$getHistoryInfo = EmployeeDesignationHistory::where($getHistoryWhere)->first();
			
			$data['designationDetails'] = LookupMaster::where('v_module_name' , config('constants.DESIGNATION_LOOKUP'))->orderBy('v_value' , 'asc')->get();
				
			$getEmployeeWhere  = [];
			$getEmployeeWhere['master_id'] = $getHistoryInfo->i_employee_id;
			$getEmployeeWhere['singleRecord'] = true;
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$getEmployeeWhere['show_all'] = true;
			}
			$data['recordInfo'] =  $this->crudModel->getRecordDetails($getEmployeeWhere);
			$data['historyInfo'] =  $getHistoryInfo;
			$data['editHistory'] = true;
			
			
			$html = view ($this->folderName . 'employee-designation-info')->with ( $data )->render();
			echo $html;die;
		}
	}
	
	public function getEmployeeShiftHistory(Request $request){
	
		if(!empty($request->input())){
	
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
	
			$getHistoryWhere = [];
			$getHistoryWhere['i_employee_id'] = $employeeId;
			$getHistoryDetails = EmployeeDesignationHistory::where($getHistoryWhere)->whereNotNull('dt_end_date')->orderBy('i_id' , 'desc')->get();
				
			$html = "";
			if(!empty($getHistoryDetails)){
				$historyIndex = 0;
				foreach($getHistoryDetails as $getHistoryKey =>  $getHistoryDetail){
					$html .= '<tr>';
					$html .= '<td class="text-center">'.++$historyIndex.'</td>';
					$html .= '<td>'.(  isset($getHistoryDetail->designationInfo->v_value) ? $getHistoryDetail->designationInfo->v_value : ''  ).'</td>';
					$html .= '<td>'.(  isset($getHistoryDetail->dt_start_date) ? convertDateFormat($getHistoryDetail->dt_start_date) : ''  ).'</td>';
					$html .= '<td>'.(  isset($getHistoryDetail->dt_end_date) ? convertDateFormat($getHistoryDetail->dt_end_date) : ''  ).'</td>';
					if( $getHistoryKey == 0 ){
						$html .= '<td><div class="d-flex align-items-center justify-content-center"><button type="button" data-end-date="'.$getHistoryDetail->dt_end_date.'" data-start-date="'.$getHistoryDetail->dt_start_date .'" class="btn btn-sm btn-theme text-white" title="'.trans('messages.edit').'" onclick="editJobDesignationHistory(this);" data-record-id="'.Wild_tiger::encode($getHistoryDetail->i_id).'" ><i class="fas fa-pencil-alt"></button></div></td>';
					} else {
						$html .= '<td></td>';
					}
					$html .= '</tr>';
				}
			}
				
			if(empty($html)){
				$html = '<tr><td class="text-center" colspan="5">'.trans('messages.no-record-found').'</td></tr>';
			}
				
			echo $html;die;
		}
	}
	
	public function getEmployeeTeamHistory(Request $request){
	
		if(!empty($request->input())){
	
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
	
			$getHistoryWhere = [];
			$getHistoryWhere['i_employee_id'] = $employeeId;
			$getHistoryDetails = EmployeeDesignationHistory::where($getHistoryWhere)->whereNotNull('dt_end_date')->orderBy('i_id' , 'desc')->get();
				
			$html = "";
			if(!empty($getHistoryDetails)){
				$historyIndex = 0;
				foreach($getHistoryDetails as $getHistoryKey =>  $getHistoryDetail){
					$html .= '<tr>';
					$html .= '<td class="text-center">'.++$historyIndex.'</td>';
					$html .= '<td>'.(  isset($getHistoryDetail->designationInfo->v_value) ? $getHistoryDetail->designationInfo->v_value : ''  ).'</td>';
					$html .= '<td>'.(  isset($getHistoryDetail->dt_start_date) ? convertDateFormat($getHistoryDetail->dt_start_date) : ''  ).'</td>';
					$html .= '<td>'.(  isset($getHistoryDetail->dt_end_date) ? convertDateFormat($getHistoryDetail->dt_end_date) : ''  ).'</td>';
					if( $getHistoryKey == 0 ){
						$html .= '<td><div class="d-flex align-items-center justify-content-center"><button type="button" data-end-date="'.$getHistoryDetail->dt_end_date.'" data-start-date="'.$getHistoryDetail->dt_start_date .'" class="btn btn-sm btn-theme text-white" title="'.trans('messages.edit').'" onclick="editJobDesignationHistory(this);" data-record-id="'.Wild_tiger::encode($getHistoryDetail->i_id).'" ><i class="fas fa-pencil-alt"></button></div></td>';
					} else {
						$html .= '<td></td>';
					}
					$html .= '</tr>';
				}
			}
				
			if(empty($html)){
				$html = '<tr><td class="text-center" colspan="5">'.trans('messages.no-record-found').'</td></tr>';
			}
				
			echo $html;die;
		}
	}
	
	public function getTeamHistoryInfo(Request $request){
		if(!empty($request->input())){
				
			$historyRecordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
				
			$getHistoryWhere = [];
			$getHistoryWhere['i_id'] = $historyRecordId;
			$getHistoryInfo = EmployeeDesignationHistory::where($getHistoryWhere)->first();
				
			$data['teamDetails'] = LookupMaster::where('v_module_name' , config('constants.TEAM_LOOKUP'))->orderBy('v_value' , 'asc')->get();
	
			$getEmployeeWhere  = [];
			$getEmployeeWhere['master_id'] = $getHistoryInfo->i_employee_id;
			$getEmployeeWhere['singleRecord'] = true;
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$getEmployeeWhere['show_all'] = true;
			}
			$data['recordInfo'] =  $this->crudModel->getRecordDetails($getEmployeeWhere);
			$data['historyInfo'] =  $getHistoryInfo;
			$data['editHistory'] = true;
			
				
			$html = view ($this->folderName . 'employee-team-info')->with ( $data )->render();
			echo $html;die;
		}
	}
	
	public function getShiftHistoryInfo(Request $request){
		if(!empty($request->input())){
	
			$historyRecordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
	
			$getHistoryWhere = [];
			$getHistoryWhere['i_id'] = $historyRecordId;
			$getHistoryInfo = EmployeeDesignationHistory::where($getHistoryWhere)->first();
	
			$secondLastRecordWhere = [];
			$secondLastRecordWhere['t_is_deleted'] = 0;
			if( (!empty($getHistoryInfo)) && isset($getHistoryInfo->i_id) && ( $getHistoryInfo->i_id > 0 ) ) {
				$secondLastRecordInfo = EmployeeDesignationHistory::where($secondLastRecordWhere)->where('i_id' , '!=' ,  $getHistoryInfo->i_id )->orderBy('i_id' , 'desc')->limit(1)->first();
			} else {
				$secondLastRecordInfo = EmployeeDesignationHistory::where($secondLastRecordWhere)->orderBy('i_id' , 'desc')->limit(1)->first();
			}
			
			//echo "<pre>";print_r($secondLastRecordInfo);die;
			$data['shiftDetails'] = ShiftMasterModel::orderBy('v_shift_name' , 'asc')->get();
	
			$getEmployeeWhere  = [];
			$getEmployeeWhere['master_id'] = $getHistoryInfo->i_employee_id;
			$getEmployeeWhere['singleRecord'] = true;
			
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$getEmployeeWhere['show_all'] = true;
			}
			
			$data['recordInfo'] =  $this->crudModel->getRecordDetails($getEmployeeWhere);
			$data['historyInfo'] =  $getHistoryInfo;
			$data['editHistory'] = true;
			$data['editRecordType'] = ( ( (!empty($getHistoryInfo)) && ( isset($getHistoryInfo->e_record_type) ) ) ? $getHistoryInfo->e_record_type : "" ) ;
			
			//echo "<pre>";print_r($data['historyInfo']);
			
			$html = view ($this->folderName . 'employee-shift-info')->with ( $data )->render();
			echo $html;die;
		}
	}
	
	public function getWeekOffHistoryInfo(Request $request){
		if(!empty($request->input())){
	
			$historyRecordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
	
			$getHistoryWhere = [];
			$getHistoryWhere['i_id'] = $historyRecordId;
			$getHistoryInfo = EmployeeDesignationHistory::where($getHistoryWhere)->first();
	
			$weekOffWhere = [];
			$data['weekOffDetails'] = WeeklyOffMasterModel::where($weekOffWhere)->orderBy('v_weekly_off_name' , 'asc')->get();
	
			$getEmployeeWhere  = [];
			$getEmployeeWhere['master_id'] = $getHistoryInfo->i_employee_id;
			$getEmployeeWhere['singleRecord'] = true;
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$getEmployeeWhere['show_all'] = true;
			}
			$data['recordInfo'] =  $this->crudModel->getRecordDetails($getEmployeeWhere);
			$data['historyInfo'] =  $getHistoryInfo;
			$data['editHistory'] = true;
	
			$html = view ($this->folderName . 'employee-week-off-info')->with ( $data )->render();
			echo $html;die;
		}
	}
	
	public function getEmployeeProbationInfo(Request $request){
		if(!empty($request->input())){
			$employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			
			$getEmployeeWhere  = [];
			$getEmployeeWhere['master_id'] = $employeeId;
			$getEmployeeWhere['singleRecord'] = true;
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$getEmployeeWhere['show_all'] = true;
			}
			$data['recordInfo'] =  $this->crudModel->getRecordDetails($getEmployeeWhere);
			
			$data['currentProbationEndDate'] = ( ( isset($data['recordInfo']->dt_probation_end_date) && (!empty($data['recordInfo']->dt_probation_end_date)) ) ? $data['recordInfo']->dt_probation_end_date : null ) ; 
			$joiningDate = ( isset($data['recordInfo']->dt_joining_date) ? $data['recordInfo']->dt_joining_date : null );
			
			$getLastRecord = $this->crudModel->getSingleRecordById( config('constants.EMPLOYEE_PROBATION_HISTORY') , [ 'i_id' , 'dt_start_date' , 'dt_end_date'] , [ 'i_employee_id' => $employeeId ,    't_is_deleted' => 0 , 'order_by' => [ 'i_id' => 'desc' ] ] );
			
			$allowedExtendMinDate = null;
			if(!empty($getLastRecord)){
				$allowedExtendMinDate = $getLastRecord->dt_start_date;
				//$allowedExtendMinDate = $getLastRecord->dt_end_date;
			}
			
			
			/* if( isset($data['recordInfo']->i_probation_period_id) && (!empty($data['recordInfo']->i_probation_period_id)) ){
				if( isset($data['recordInfo']->probationPeriodInfo->v_probation_period_duration) && (!empty($data['recordInfo']->probationPeriodInfo->v_probation_period_duration))  ){
					$duration = ( $data['recordInfo']->probationPeriodInfo->v_probation_period_duration . ' ' . $data['recordInfo']->probationPeriodInfo->e_months_weeks_days );
					if(!empty($joiningDate) && (!empty($duration))){
						$data['currentProbationEndDate'] = date('Y-m-d' , strtotime("+" . $duration , strtotime($joiningDate)) );
					} 
				}
			} */
			$data['probationPolicyRecordDetails'] = ProbationPolicyMasterModel::where('e_record_status',config ( 'constants.PROBATION_POLICY'))->where('t_is_active',1)->orderBy('v_probation_policy_name', 'ASC')->get();
			$data['allowedMinDate'] = $allowedExtendMinDate;
			$data['empJoiningDate'] = $joiningDate;
			$html = view ($this->folderName . 'employee-probation-info')->with ( $data )->render();
			echo $html;die;
		}
	}
	
	public function updateProbation(Request $request){
		if(!empty($request->input())){
			
			$confirmEmployeeJoiningDate = (!empty($request->input('confirm_employee_joining_date')) ? dbDate($request->input('confirm_employee_joining_date')) : null );
			
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
			$probationEndDate = (!empty($request->input('probation_end_date')) ? dbDate($request->input('probation_end_date')) : null );
			$probationRemark = (!empty($request->input('probation_remark')) ? ($request->input('probation_remark')) : null );
			$probationStatus = (!empty($request->input('probation_status')) ? ($request->input('probation_status')) : null );
			
			$employeeInfo = EmployeeModel::where('i_id' ,  $employeeId)->first();
			
			if( $probationStatus == config('constants.END_PROBATION') ){
				if(!empty($employeeInfo) && isset($employeeInfo->dt_probation_end_date)   && ( strtotime($employeeInfo->dt_probation_end_date) > strtotime($probationEndDate) ) ){
					if( ( strtotime( $employeeInfo->dt_joining_date ) !=  strtotime( $confirmEmployeeJoiningDate ) ) ){
						$this->ajaxResponse(101, trans('messages.error-invalid-joining-date'));
					}	
				}
			}
			
			
			$result = false;
			DB::beginTransaction();
			
			
			
			try{
				$probationHistoryData['i_employee_id'] = $employeeId;
				$probationHistoryData['e_status'] = $probationStatus;
				$probationHistoryData['dt_start_date'] = $probationEndDate;
				$probationHistoryData['dt_end_date'] = $probationEndDate;
				$probationHistoryData['v_remark'] = $probationRemark;
				
				if( $probationStatus == config('constants.END_PROBATION') ){
					if( isset($employeeInfo->dt_probation_end_date) && (!empty($employeeInfo->dt_probation_end_date))  ){
						if(strtotime($employeeInfo->dt_probation_end_date) < strtotime($probationEndDate) ){
							$probationHistoryData = [];
							$probationHistoryData['i_employee_id'] = $employeeId;
							$probationHistoryData['e_status'] = $probationStatus;
							$probationHistoryData['dt_start_date'] = $employeeInfo->dt_probation_end_date;
							$probationHistoryData['dt_end_date'] = $probationEndDate;
							$probationHistoryData['v_remark'] = $probationRemark;
							$this->crudModel->insertTableData( config('constants.EMPLOYEE_PROBATION_HISTORY') , $probationHistoryData  );
						} else {
							
							$getLastRecord = $this->crudModel->getSingleRecordById( config('constants.EMPLOYEE_PROBATION_HISTORY') , [ 'i_id' ] , [ 'i_employee_id' =>  $employeeId ,   't_is_deleted' => 0 , 'order_by' => [ 'i_id' => 'desc' ] ] );
							
							if(!empty($getLastRecord)){
								$updateProbationData = [];
								$updateProbationData['dt_end_date'] = $probationEndDate;
								$updateProbationData['v_remark'] = $probationRemark;
								$this->crudModel->updateTableData( config('constants.EMPLOYEE_PROBATION_HISTORY') , $updateProbationData  , [ 'i_id' => $getLastRecord->i_id  ] );
							}
						}
					} else {
						$probationHistoryData = [];
						$probationHistoryData['i_employee_id'] = $employeeId;
						$probationHistoryData['e_status'] = $probationStatus;
						$probationHistoryData['dt_start_date'] = $employeeInfo->dt_joining_date;
						$probationHistoryData['dt_end_date'] = $probationEndDate;
						$probationHistoryData['v_remark'] = $probationRemark;
						$this->crudModel->insertTableData( config('constants.EMPLOYEE_PROBATION_HISTORY') , $probationHistoryData  );
					}
				}
				
				if( $probationStatus == config('constants.EXTEND_PROBATION') ){
					
					$getLastRecord = $this->crudModel->getSingleRecordById( config('constants.EMPLOYEE_PROBATION_HISTORY') , [ 'i_id' , 'dt_end_date' ] , [  'i_employee_id' => $employeeId , 't_is_deleted' => 0 , 'order_by' => [ 'i_id' => 'desc' ] ] );
				
					$probationStartDate = $employeeInfo->dt_joining_date;
					
					
					if(!empty($getLastRecord)){
						
						if( strtotime($getLastRecord->dt_end_date) >= strtotime($probationEndDate)){
							$this->ajaxResponse(101, trans('messages.system-error'));
						}
						$probationStartDate = $getLastRecord->dt_end_date;
						
						
					}
					
					$probationHistoryData = [];
					$probationHistoryData['i_employee_id'] = $employeeId;
					$probationHistoryData['e_status'] = $probationStatus;
					$probationHistoryData['dt_start_date'] = $probationStartDate;
					$probationHistoryData['dt_end_date'] = $probationEndDate;
					$probationHistoryData['v_remark'] = $probationRemark;
					
					$this->crudModel->insertTableData( config('constants.EMPLOYEE_PROBATION_HISTORY') , $probationHistoryData  );
					
				}
				$updateEmployeeData = [];
				if(empty($probationStatus)){
					$updateEmployeeData['i_probation_period_id'] = (!empty($request->input('probation_policy_id')) ? (int)Wild_tiger::decode($request->input('probation_policy_id')) : 0 );;
					$probationHistoryData = [];
					$probationHistoryData['i_employee_id'] = $employeeId;
					$probationHistoryData['e_status'] = $probationStatus;
					$probationHistoryData['dt_start_date'] = $employeeInfo->dt_joining_date;
					$probationHistoryData['dt_end_date'] = $probationEndDate;
					$probationHistoryData['v_remark'] = $probationRemark;
					$this->crudModel->insertTableData( config('constants.EMPLOYEE_PROBATION_HISTORY') , $probationHistoryData  );
				}
				
				
				
				$updateEmployeeData['v_probation_remark'] = $probationRemark;
				$updateEmployeeData['dt_probation_end_date'] = $probationEndDate;
				$updateEmployeeData['e_in_probation'] = config('constants.SELECTION_YES');
				$updateEmployeeData['t_is_probation_completed'] = 0;
				$updateEmployeeData['e_employment_status'] = config('constants.PROBATION_EMPLOYMENT_STATUS');
				if( $probationStatus == config('constants.END_PROBATION') ){
					$updateEmployeeData['e_in_probation'] = config('constants.SELECTION_NO');
					$updateEmployeeData['t_is_probation_completed'] = 1;
					$updateEmployeeData['i_probation_update_id'] = session()->get('user_id');
					$updateEmployeeData['e_employment_status'] = config('constants.CONFIRMED_EMPLOYMENT_STATUS');
				}
				
				$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , $updateEmployeeData , [ 'i_id' => $employeeId ]  );
				
				if( $probationStatus == config('constants.END_PROBATION') ){
					
					$effectiveDate = $probationEndDate;
					$leaveTypeId = config("constants.PAID_LEAVE_TYPE_ID");
					$noOfLeave = 1;
					
					$leaveBalanceData = [];
					$leaveBalanceData['i_employee_id'] = $employeeId;
					$leaveBalanceData['i_leave_type_id'] = $leaveTypeId;
					$leaveBalanceData['dt_effective_date'] = $effectiveDate;
					$leaveBalanceData['d_no_of_days_assign'] = $noOfLeave;
					$leaveBalanceData['v_remark'] = "Leave Balance";
					
					$checkLeaveAssignHistoryWhere = [];
					$checkLeaveAssignHistoryWhere['i_employee_id'] = $employeeId;
					$checkLeaveAssignHistoryWhere['i_leave_type_id'] = $leaveTypeId;
					$checkLeaveAssignHistoryWhere['dt_effective_date'] = $effectiveDate;
					$checkLeaveAssignHistoryWhere['t_is_deleted'] = 0;
					$checkLeaveAssignHistory = $this->crudModel->getSingleRecordById(config('constants.LEAVE_ASSIGN_HISTORY_TABLE'), [ 'i_id' ] , $checkLeaveAssignHistoryWhere );
					
					if(empty($checkLeaveAssignHistory)){
						$insertLeaveASsign = $this->crudModel->insertTableData(config('constants.LEAVE_ASSIGN_HISTORY_TABLE'), $leaveBalanceData );
					
						$leaveBalanceWhere  = [];
						$leaveBalanceWhere['i_employee_id'] = $employeeId;
						$leaveBalanceWhere['i_leave_type_id'] = $leaveTypeId;
						$leaveBalanceWhere['t_is_deleted != '] = 1;
						$checkLeaveAssigned = $this->crudModel->getSingleRecordById(config('constants.LEAVE_BALANCE_TABLE') , [ 'i_id' ] ,  $leaveBalanceWhere );
					
						if(!empty($checkLeaveAssigned)){
							$updateLeaveBalance = [];
							$updateLeaveBalance['d_current_balance'] = DB::raw("CONCAT(d_current_balance+".$noOfLeave.")");
							$this->crudModel->updateTableData(config('constants.LEAVE_BALANCE_TABLE'), $updateLeaveBalance , [ 'i_id' =>  $checkLeaveAssigned->i_id  ] );
							//echo $this->crudModel->last_query();echo "<br><br><br>";
						} else {
							$insertLeaveBalance = [];
							$insertLeaveBalance['i_employee_id'] = $employeeId;
							$insertLeaveBalance['i_leave_type_id'] = $leaveTypeId;
							$insertLeaveBalance['d_current_balance'] = $noOfLeave;
					
							$this->crudModel->insertTableData(config('constants.LEAVE_BALANCE_TABLE'), $insertLeaveBalance );
							//echo $this->crudModel->last_query();echo "<br><br><br>";
						}
					}
					
				}
				
				$result = true;
			}catch(\Exception $e){
				var_dump($e->getMessage());die;
				DB::rollback();	
			}
			//die("welcome");
			$successMessage = trans('messages.success-update' , [ 'module' => trans('messages.probation-period')  ] ) ;
			$errorMessage = trans('messages.error-update' , [ 'module' => trans('messages.probation-period')  ] ) ;
			
			if( $result != false ){
				
				$empWhere = [];
				$empWhere['master_id'] = $employeeId;
				$empWhere['singleRecord'] = true;
				$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
				if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
					$empWhere['show_all'] = true;
				}
				
				$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
				$recordInfo = [];
				$recordInfo['employeeRecordInfo'] = $recordDetail;
				$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/job-list')->with ( $recordInfo )->render();
				
				DB::commit();
				$this->ajaxResponse(1, $successMessage , [ 'html' => $html  ]);
			}
			DB::rollback();
			$this->ajaxResponse(101, $errorMessage);
			
		}
	}
	
	public function updateLoginStatus(Request $request){
		
		if(!empty($request->input())){
				
			$employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			$status = (!empty($request->input('current_status')) ? trim($request->input('current_status')) : null );
			
			if( (!empty($employeeId)) && (!empty($status)) ){
				$successMessage = trans('messages.success-update' , [ 'module' => trans("messages.login-status") ] );
				$errorMessage = trans('messages.error-update' , [ 'module' => trans("messages.login-status") ] );
				$updateData = [];
				$updateStatus = "";
				$updateStatusText = "";
				$enableDisableStatusText = "";
				switch($status){
					case config("constants.ACTIVE_STATUS"):
						$updateData['t_is_active'] = 0;
						$updateStatus = config("constants.INACTIVE_STATUS"); 
						$updateStatusText = trans('messages.enable-login');
						$enableDisableStatusText = trans('messages.disable');
						break;
					case config("constants.INACTIVE_STATUS"):
						$updateData['t_is_active'] = 1;
						$updateStatus = config("constants.ACTIVE_STATUS");
						$updateStatusText = trans('messages.disable-login');
						$enableDisableStatusText = trans('messages.enable');
						break;
				}
				
				if(!empty($updateData)){
					
					$result = $this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , $updateData , [ 'i_id' => $employeeId ] );
					$employeeInfo = EmployeeModel::where('i_id' , $employeeId )->first();
					if( $status ==  config("constants.ACTIVE_STATUS") ){
						if( isset($employeeInfo->i_login_id) ){
							removeSession($employeeInfo->i_login_id);
						}
					}
					
					
					if( $result != false ){
						$this->ajaxResponse(1, $successMessage , [ 'update_status' =>  $updateStatus , 'status_text'  => $updateStatusText , 'enable_disable_status_text' => $enableDisableStatusText ] );
					}
					$this->ajaxResponse(101, $errorMessage );
				}
				
			}
			$this->ajaxResponse(101, trans('messages.system-error') );
			
		}
	}
	
	public function getInitiateExitInfo(Request $request){
		
		if(!empty($request->input())){
		
			
			
			$employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			$status = (!empty($request->input('record_type')) ? trim($request->input('record_type')) : null );
			
			$getResignRecordWhere = null;
			//$getResignRecordWhere['i_employee_id'] = $employeeId;
			$getResignRecordWhere = "( i_employee_id = '".$employeeId."' and e_status not in ( '".config('constants.REJECTED_STATUS')."' ,  '".config('constants.CANCELLED_STATUS')."' ) )";
			$recordInfo = [];
			
			$recordInfo['resignInfo'] = EmployeeResignHistory::with( ['employee' , 'resignation' , 'termination' , 'employee.noticePeriodInfo' ] )->whereRaw($getResignRecordWhere)->first();
			$recordInfo['employeeInfo'] =  EmployeeModel::with(['noticePeriodInfo'])->where('i_id' , $employeeId )->first();
			$recordInfo['empJoiningDate'] = ( isset($recordInfo['employeeInfo']->dt_joining_date) ? $recordInfo['employeeInfo']->dt_joining_date : '' );
			$recordInfo['noticePeriodDuration'] = ( isset($recordInfo['employeeInfo']->noticePeriodInfo->v_probation_period_duration) ? $recordInfo['employeeInfo']->noticePeriodInfo->v_probation_period_duration . ( isset($recordInfo['employeeInfo']->noticePeriodInfo->e_months_weeks_days) ? ' ' . $recordInfo['employeeInfo']->noticePeriodInfo->e_months_weeks_days : ''  ) : '' );
			
			$terminationReasonWhere = [];
			$terminationReasonWhere['v_module_name'] = config('constants.TERMINATION_REASONS_LOOKUP');
			$resignationReasonWhere = [];
			$resignationReasonWhere['v_module_name'] = config('constants.RESIGN_REASONS_LOOKUP');
			
			if(empty($recordInfo['resignInfo'])){
				$resignationReasonWhere['t_is_active'] = 1;
			}
			
			if(empty($recordInfo['resignInfo'])){
				$terminationReasonWhere['t_is_active'] = 1;
			}
			
			$recordInfo['terminationReasonDetails'] = LookupMaster::where($terminationReasonWhere)->orderBy('v_value', 'ASC')->get();
			$recordInfo['resignationReasonDetails'] = LookupMaster::where($resignationReasonWhere)->orderBy('v_value', 'ASC')->get();
			
			
			$recordInfo['employeeId'] = $employeeId;
			$html = view ($this->folderName . 'edit-initiate-exit-form')->with ( $recordInfo )->render();
			echo $html;die;
		
		}
	}
	
	public function addInitiateExitForm(Request $request){
		
		if(!empty($request->input())){
			$removeImage = (!empty($request->input('remove_image')) ? trim($request->input('remove_image')) : null );
				
			$employeeId = (!empty($request->input('initiate_exit_employee_id')) ? (int)Wild_tiger::decode($request->input('initiate_exit_employee_id')) : 0 );
			//$employeeId = 1;
			$exitReason = (!empty($request->input('initiating_exit_reason')) ? trim($request->input('initiating_exit_reason')) : null );
			$discussionEmployee = (!empty($request->input('initial_exit_discussion_with_employee')) ? trim($request->input('initial_exit_discussion_with_employee')) : null );
			$discussionEmployeeSummary = (!empty($request->input('initial_exit_discussion_with_employee_summary')) ? trim($request->input('initial_exit_discussion_with_employee_summary')) : null );
			
			$resignationId = (!empty($request->input('initial_exit_reason_for_resignation')) ? (int)Wild_tiger::decode($request->input('initial_exit_reason_for_resignation')) : 0 );
			$terminationId = (!empty($request->input('initial_exit_reason_for_termination')) ? (int)Wild_tiger::decode($request->input('initial_exit_reason_for_termination')) : 0 );
			
			$terminationDate = (!empty($request->input('initial_exit_termination_date')) ? dbDate($request->input('initial_exit_termination_date')) : null );
			$employeeProvideExitDate = (!empty($request->input('initial_exit_employee_provide_notice_exit_date')) ? dbDate($request->input('initial_exit_employee_provide_notice_exit_date')) : null );
			
			$lastWorkingDayType = (!empty($request->input('initial_exit_recommend_last_working_day_type')) ? trim($request->input('initial_exit_recommend_last_working_day_type')) : null );
			$otherLastWorkingDate = (!empty($request->input('initial_exit_other_last_working_date')) ? dbDate($request->input('initial_exit_other_last_working_date')) : null );
			
			$comment = (!empty($request->input('initiating_exit_comment')) ? trim($request->input('initiating_exit_comment')) : null );
			$reHireStatus = (!empty($request->input('initiating_exit_ok_to_hire')) ? trim($request->input('initiating_exit_ok_to_hire')) : null );
			
			
			$formValidation =[];
			//$formValidation['employee_id'] = ['required'];
			$formValidation['initial_exit_discussion_with_employee'] = ['required'];
			$formValidation['initiating_exit_reason'] = ['required'];
			$formValidation['initiating_exit_comment'] = ['required'];
			//$formValidation['initial_exit_recommend_last_working_day_type'] = ['required'];
			
			switch($exitReason){
				case config('constants.EMPLOYEE_INITIATE_EXIT_TYPE'):
					$formValidation['initial_exit_reason_for_resignation'] = ['required'];
					$formValidation['initial_exit_employee_provide_notice_exit_date'] = ['required'];
					break;
				case config('constants.EMPLOYER_INITIATE_EXIT_TYPE'):
					$formValidation['initial_exit_reason_for_termination'] = ['required'];
					$formValidation['initial_exit_termination_date'] = ['required'];
					break;
			}
			
			if( $lastWorkingDayType ==  config('constants.OTHER') ){
				$formValidation['initial_exit_other_last_working_date'] = ['required'];
			}
			
			if( $lastWorkingDayType ==  config('constants.SELECTION_YES') ){
				$formValidation['initial_exit_discussion_with_employee_summary'] = ['required'];
			}
			
			$checkValidation = Validator::make($request->all(),$formValidation,
					[
							'employee_id.required' => __('messages.require-employee'),
							'initiating_exit_reason.required' => __('messages.require-initiating-exit-reason'),
							'initial_exit_discussion_with_employee.required' => __('messages.require-discussion-with-employee-decision'),
							'initial_exit_discussion_with_employee_summary.required' => __('messages.require-summary-of-the-discussion'),
							'comment.required' => __('messages.required-comment'),
							'initial_exit_recommend_last_working_day_type.required' => __('messages.require-last-working-day'),
							'initial_exit_reason_for_resignation.required' => __('messages.require-please-select-reason-for-resignation'),
							'initial_exit_employee_provide_notice_exit_date.required' => __('messages.require-employee-provide-notice-exit'),
							'initial_exit_reason_for_termination.required' => __('messages.require-please-select-reason-for-termination'),
							'initial_exit_termination_date.required' => __('messages.require-termination-date'),
							'initial_exit_other_last_working_date.required' => __('messages.require-forresign-last-working-date'),
					]
			);
			if($checkValidation->fails() != false){
				$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.initiate-exit-request') ] ) ) );
			}
			
			
			$exitFormData = [];
			$exitFormData['e_initiate_type'] = $exitReason;
			$exitFormData['i_employee_id'] = $employeeId;
			$exitFormData['e_employee_discuss'] = $discussionEmployee;
			$exitFormData['v_discuss_summary'] = $discussionEmployeeSummary;
			$exitFormData['i_termination_reason_id'] = $terminationId;
			$exitFormData['i_resign_reason_id'] = $resignationId;
			$exitFormData['dt_termination_notice_date'] = $terminationDate;
			$exitFormData['dt_employee_notice_date'] = $employeeProvideExitDate;
			$exitFormData['e_last_working_day'] = $lastWorkingDayType;
			$exitFormData['e_rehire_status'] = $reHireStatus;
			$exitFormData['dt_last_working_date'] = $otherLastWorkingDate;
			$exitFormData['v_remark'] = $comment;
			
			
			
			//$exitFormData['v_attachment'] = $exitReason;
			
			if (!empty($request->file('initiating_exit_file_upload'))){
				
				$getEmployeeWhere = [];
				$getEmployeeWhere['i_id'] = $exitFormData['i_employee_id'];
				$getEmployeeInfo = EmployeeModel::where($getEmployeeWhere)->first();
				
				$employeeCode = (!empty($getEmployeeInfo) ? $getEmployeeInfo->v_employee_code : null );
				
				$fileUpload = $this->uploadFile($request, 'initiating_exit_file_upload' , $this->documentFolder . $employeeCode . '/initiating_exit/' ,  ['jpg' , 'jpeg' , 'png' , 'pdf' , 'xls' , 'xlsx' , 'doc' , 'docx']   );
					
				if (isset($fileUpload['status']) && $fileUpload['status'] == 1){
					$exitFormData['v_attachment'] = $fileUpload['filePath'];
				}else {
					$this->ajaxResponse(101, ( isset($fileUpload['message']) ? $fileUpload['message'] : trans('messages.system-error') ) );
				}
			} else{
				if(!empty($removeImage)){
					$exitFormData['v_attachment'] = null;
				}
			}
			
			
			if( $exitReason == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE')){
				$exitFormData['i_termination_reason_id'] = null;
				$exitFormData['dt_termination_notice_date'] = null;
				$exitFormData['dt_notice_start_date'] = $exitFormData['dt_employee_notice_date'];
			}else if( $exitReason == config('constants.EMPLOYER_INITIATE_EXIT_TYPE')){
				$exitFormData['i_resign_reason_id'] = null;
				$exitFormData['dt_employee_notice_date'] = null;
				$exitFormData['dt_notice_start_date'] = $exitFormData['dt_termination_notice_date'];
			}
			$employeeInfo =  EmployeeModel::with(['noticePeriodInfo'])->where('i_id' , $employeeId )->first();
			$noticePeriodDuration = ( isset($employeeInfo->noticePeriodInfo->v_probation_period_duration) ? $employeeInfo->noticePeriodInfo->v_probation_period_duration . ( isset($employeeInfo->noticePeriodInfo->e_months_weeks_days) ? ' ' . $employeeInfo->noticePeriodInfo->e_months_weeks_days : ' '.config('constants.DEFAULT_NOTICE_PERIOD_DURATION')  ) : '' );
			$systemLastWorkingDate = null;
			
			if(!empty($noticePeriodDuration)){
				$lastDate = null;
				switch($exitReason){
					case config('constants.EMPLOYEE_INITIATE_EXIT_TYPE'):
						$lastDate = date("Y-m-d" , strtotime("+" .$noticePeriodDuration , strtotime($exitFormData['dt_employee_notice_date']) ) );
						break;
					case config('constants.EMPLOYER_INITIATE_EXIT_TYPE'):
						$lastDate = date("Y-m-d" , strtotime("+" .$noticePeriodDuration , strtotime($exitFormData['dt_termination_notice_date']) ) );
						break;
					default:
						$lastDate = date("Y-m-d" , strtotime("+" .$noticePeriodDuration , strtotime(date('Y-m-d')) ) );
						break;
				}
				
				if(!empty($lastDate)){
					$systemLastWorkingDate = $lastDate;
				}
			}
				
			$exitFormData['dt_system_last_working_date'] = $systemLastWorkingDate;
			$exitFormData['dt_notice_end_date'] = $exitFormData['dt_system_last_working_date'];
			
			if( $lastWorkingDayType ==  config('constants.OTHER') ){
				$exitFormData['dt_notice_end_date'] = $exitFormData['dt_last_working_date'];
			}
			
			$checkExitRecordWhere = [];
			//$checkExitRecordWhere['i_employee_id'] = $employeeId;
			$checkExitRecordWhere['custom_function'][] = "( i_employee_id = '".$employeeId."' and e_status not in ( '".config('constants.REJECTED_STATUS')."' ,  '".config('constants.CANCELLED_STATUS')."' ) )";
			$checkRecordExits = $this->crudModel->getSingleRecordById( config('constants.EMPLOYEE_RESIGN_HISTORY') , [ 'i_id' , 'e_status' , 'dt_last_working_date' , 'e_last_working_day' , 'dt_system_last_working_date' ] , $checkExitRecordWhere  );
			
			$result = false;
			DB::beginTransaction();
			
			$successMessage = trans('messages.success-module-create' , [ 'module' => trans('messages.initiate-exit-request')  ] );
			$errorMessage = trans('messages.error-create' , [ 'module' => trans('messages.initiate-exit-request')  ] );
			
			
			
			//echo "<pre>";print_r($exitFormData);
			try{
				if( !empty($checkRecordExits) )  {
					
					
					$successMessage = trans('messages.success-update' , [ 'module' => trans('messages.initiate-exit-request')  ] );
					$errorMessage = trans('messages.update' , [ 'module' => trans('messages.initiate-exit-request')  ] );
					
					$this->crudModel->updateTableData(config('constants.EMPLOYEE_RESIGN_HISTORY'), $exitFormData, ['i_id' => $checkRecordExits->i_id ] );
					
					if( $checkRecordExits->e_status == config('constants.APPROVED_STATUS') ){
						$updateEmployeeData = [];
						if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ){
							$updateEmployeeData['dt_pf_expiry_date'] = (!empty($request->input('pf_exit_date')) ? dbDate($request->input('pf_exit_date')) : null );
						}
						$updateEmployeeData['dt_notice_period_start_date'] = $exitFormData['dt_termination_notice_date'];
						if( $exitFormData['e_last_working_day'] == config('constants.NOTICE_PERIOD') ){
							$updateEmployeeData['dt_notice_period_end_date'] = $exitFormData['dt_system_last_working_date'];
						} else {
							$updateEmployeeData['dt_notice_period_end_date'] = $exitFormData['dt_last_working_date'];
						}
						
					
						$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateEmployeeData, ['i_id' => $employeeId ] );
					}
					
					//var_dump($checkRecordExits->e_last_working_day != $exitFormData['e_last_working_day']);echo "<br><br>";
					//var_dump(( strtotime($checkRecordExits->dt_last_working_date) !=  strtotime($exitFormData['dt_last_working_date']) ));echo "<br><br>";
					//var_dump(( strtotime($checkRecordExits->dt_system_last_working_date) !=  strtotime($exitFormData['dt_system_last_working_date']) ));echo "<br><br>";
					
					if( ( $checkRecordExits->e_last_working_day != $exitFormData['e_last_working_day'] ) ||  ( strtotime($checkRecordExits->dt_last_working_date) !=  strtotime($exitFormData['dt_last_working_date']) ) || ( strtotime($checkRecordExits->dt_system_last_working_date) !=  strtotime($exitFormData['dt_system_last_working_date']) ) ){
						$this->sendResignTerminationMail( $exitFormData['i_employee_id'] , $checkRecordExits->i_id , config('constants.UPDATE_LAST_WORKING_DATE') );
					}
					
					
				}  else {
					
					$resignRecordId = $this->crudModel->insertTableData(config('constants.EMPLOYEE_RESIGN_HISTORY'), $exitFormData  );
					
					if( $exitFormData['e_initiate_type'] == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) {
						$this->sendResignTerminationMail( $exitFormData['i_employee_id'] , $resignRecordId , config('constants.RESIGN_REQUEST') );
					} else if( $exitFormData['e_initiate_type'] == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) {
						$this->sendResignTerminationMail( $exitFormData['i_employee_id'] , $resignRecordId , config('constants.TERMINATION_REQUEST') );
					}
						
					
					
				}	
				$result = true;
			}catch(\Exception $e){
				var_dump($e->getMessage());die;
				DB::rollback();
			}
			//var_dump($result);die;
			if( $result != false ){
				DB::commit();
				
				$initateExitInfo = EmployeeModel::with(['latestResignHistory'])->where('i_id' , $employeeId)->first();
				$initateExitData = [];
				$initateExitData['employeeRecordInfo'] = $initateExitInfo;
				$initateExitHtml = view ($this->folderName . 'employee-notice-period-alert')->with ( $initateExitData )->render();
				
				$this->ajaxResponse(1, $successMessage , [ 'html' => $initateExitHtml  ]);
			} else {
				DB::rollback();
				$this->ajaxResponse(101, $errorMessage);
			}
		}
		
	}
	
	public function getResignInfo(Request $request){
	
		if(!empty($request->input())){
	
			$employeeId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			$status = (!empty($request->input('record_type')) ? trim($request->input('record_type')) : null );
				
			$getResignRecordWhere = [];
			$getResignRecordWhere['i_employee_id'] = $employeeId;
	
			$recordInfo = [];
			$recordInfo['terminationReasonDetails'] = LookupMaster::where('v_module_name',config('constants.TERMINATION_REASONS_LOOKUP'))->where('t_is_active',1)->orderBy('v_value', 'ASC')->get();
			$recordInfo['resignationReasonDetails'] = LookupMaster::where('v_module_name',config('constants.RESIGN_REASONS_LOOKUP'))->where('t_is_active',1)->orderBy('v_value', 'ASC')->get();
			$recordInfo['resignInfo'] = EmployeeResignHistory::with( ['employee' , 'resignation' , 'termination' ] )->where($getResignRecordWhere)->whereNotIn( 'e_status' , [ config('constants.CANCELLED_STATUS') , config('constants.REJECTED_STATUS') ] )->first();
			$recordInfo['employeeInfo'] =  EmployeeModel::with(['noticePeriodInfo'])->where('i_id' , $employeeId )->first();
			
			$resignationReasonWhere = [];
			$resignationReasonWhere['v_module_name'] = config('constants.RESIGN_REASONS_LOOKUP');
			
			if(empty($recordInfo['resignInfo'])){
				$resignationReasonWhere['t_is_active'] = 1;
			}
			$recordInfo['resignationReasonDetails'] = LookupMaster::where($resignationReasonWhere)->orderBy('v_value', 'ASC')->get();
			
			$recordInfo['empJoiningDate'] = ( isset($recordInfo['employeeInfo']->dt_joining_date) ? $recordInfo['employeeInfo']->dt_joining_date : '' );
			$recordInfo['noticePeriodDuration'] = ( isset($recordInfo['employeeInfo']->noticePeriodInfo->v_probation_period_duration) ? $recordInfo['employeeInfo']->noticePeriodInfo->v_probation_period_duration . ( isset($recordInfo['employeeInfo']->noticePeriodInfo->e_months_weeks_days) ? ' ' . $recordInfo['employeeInfo']->noticePeriodInfo->e_months_weeks_days : ' '.config('constants.DEFAULT_NOTICE_PERIOD_DURATION')  ) : '' );
			$recordInfo['noticePeriodLastDate'] = null;
			
			if(!empty($recordInfo['noticePeriodDuration'])){
				$lastDate = date("Y-m-d" , strtotime("+" .$recordInfo['noticePeriodDuration'] , strtotime(date('Y-m-d')) ) );
				if(!empty($lastDate)){
					$recordInfo['noticePeriodLastDate'] = $lastDate;
				}
			}
			
			$recordInfo['employeeId'] = Wild_tiger::encode($employeeId);
			
			if( isset($recordInfo['resignInfo']) && ( isset($recordInfo['resignInfo']->e_status)) && ( $recordInfo['resignInfo']->e_status == config('constants.APPROVED_STATUS') ) ){
				$recordInfo['allChildEmployeeDetails'] = EmployeeModel::where('i_leader_id' , $employeeId )->where('e_employment_status' , '!=' , config('constants.RELIEVED_EMPLOYMENT_STATUS'))->get();
				$recordInfo['allEmployeeDetails'] = EmployeeModel::where('e_employment_status' , '!=' , config('constants.RELIEVED_EMPLOYMENT_STATUS'))->where('i_id' , '!=' , $employeeId )->get();
			}
			
			
			$html = view ($this->folderName . 'edit-resign-form')->with ( $recordInfo )->render();
			
			echo $html;die;
	
		}
	}
	
	
	public function addResignForm(Request $request){
	
		if(!empty($request->input())){
				
			$employeeId = (!empty($request->input('resign_employee_id')) ? (int)Wild_tiger::decode($request->input('resign_employee_id')) : 0 );
			//$employeeId = 10;
			
			$discussionWithManager = (!empty($request->input('resign_discussion_with_manager')) ? trim($request->input('resign_discussion_with_manager')) : null );
			$discussionManager = (!empty($request->input('resign_summary_of_discussion')) ? trim($request->input('resign_summary_of_discussion')) : null );
			$resignationId = (!empty($request->input('resign_reason_for_resignation')) ? (int)Wild_tiger::decode($request->input('resign_reason_for_resignation')) : 0 );
			$comment = (!empty($request->input('resign_comment')) ? trim($request->input('resign_comment')) : null );
			$preferenceLastWorkingDay = (!empty($request->input('resign_preference_last_working_day')) ? trim($request->input('resign_preference_last_working_day')) : null );
			$lastWorkingDate = (!empty($request->input('resign_preference_last_working_date')) ? dbDate($request->input('resign_preference_last_working_date')) : null );
			
			$formValidation =[];
			//$formValidation['employee_id'] = ['required'];
			$formValidation['resign_discussion_with_manager'] = ['required'];
			
			if( $discussionWithManager == config('constants.SELECTION_YES') ){
				$formValidation['resign_summary_of_discussion'] = ['required'];
			} 
			
			$formValidation['resign_reason_for_resignation'] = ['required'];
			$formValidation['resign_comment'] = ['required'];
			$formValidation['resign_preference_last_working_day'] = ['required'];
			
			if( $preferenceLastWorkingDay == config('constants.SELECTION_YES') ){
				$formValidation['resign_preference_last_working_date'] = ['required'];
			}
			
			
			
			$checkValidation = Validator::make($request->all(),$formValidation,
					[
							'resign_discussion_with_manager.required' => __('messages.require-discussion-with-manager'),
							'resign_summary_of_discussion.required' => __('messages.require-summary-of-the-discussion'),
							'resign_reason_for_resignation.required' => __('messages.require-please-select-reason-for-resignation'),
							'resign_comment.required' => __('messages.require-resign-comment'),
							'resign_preference_last_working_day.required' => __('messages.required-early-last-working-day'),
							'resign_preference_last_working_date.required' => __('messages.require-forresign-last-working-date'),
					]
			);
			if($checkValidation->fails() != false){
				$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.initiate-exit-request') ] ) ) );
			}
			
			$checkExitRecordWhere = [];
			$checkExitRecordWhere['i_employee_id'] = $employeeId;
			$checkExitRecordWhere['custom_function'][] = "e_status in ( '".config('constants.APPROVED_STATUS')."' ,  '".config('constants.PENDING_STATUS')."' )";
				
			$checkRecordExits = $this->crudModel->getSingleRecordById( config('constants.EMPLOYEE_RESIGN_HISTORY') , [ 'i_id' , 'e_status' , 'dt_last_working_date' , 'dt_system_last_working_date' , 'e_last_working_day' ] , $checkExitRecordWhere  );
			
				
			$exitFormData = [];
			$exitFormData['e_initiate_type'] = config('constants.EMPLOYEE_INITIATE_EXIT_TYPE');
			$exitFormData['i_employee_id'] = $employeeId;
			$exitFormData['e_employee_discuss'] = $discussionWithManager;
			$exitFormData['v_discuss_summary'] = $discussionManager;
			
			
			$exitFormData['i_resign_reason_id'] = $resignationId;
			$exitFormData['dt_employee_notice_date'] = (!empty($checkRecordExits) ? ( isset($checkRecordExits->dt_employee_notice_date) ? $checkRecordExits->dt_employee_notice_date : date('Y-m-d') )  : date('Y-m-d') )  ;
			$exitFormData['e_last_working_day'] = config('constants.NOTICE_PERIOD');
			$exitFormData['e_last_working_day'] = config('constants.NOTICE_PERIOD');
			$exitFormData['dt_last_working_date'] = null;
			if( $preferenceLastWorkingDay == config('constants.SELECTION_YES') ){
				$exitFormData['e_last_working_day'] = config('constants.OTHER');
				$exitFormData['dt_last_working_date'] = $lastWorkingDate;
			}
			$exitFormData['dt_notice_start_date'] = $exitFormData['dt_employee_notice_date'];
			$employeeInfo =  EmployeeModel::with(['noticePeriodInfo'])->where('i_id' , $employeeId )->first();
			$noticePeriodDuration = ( isset($employeeInfo->noticePeriodInfo->v_probation_period_duration) ? $employeeInfo->noticePeriodInfo->v_probation_period_duration . ( isset($employeeInfo->noticePeriodInfo->e_months_weeks_days) ? ' ' . $employeeInfo->noticePeriodInfo->e_months_weeks_days : ' '.config('constants.DEFAULT_NOTICE_PERIOD_DURATION')  ) : '' );
			
			$systemLastWorkingDate = null;	
			if(!empty($noticePeriodDuration)){
				$lastDate = date("Y-m-d" , strtotime("+" .$noticePeriodDuration , strtotime($exitFormData['dt_employee_notice_date']) ) );
				if(!empty($lastDate)){
					$systemLastWorkingDate = $lastDate;
				}
			}
			
			$exitFormData['dt_system_last_working_date'] = $systemLastWorkingDate;
			$exitFormData['v_remark'] = $comment;
			
			if( $preferenceLastWorkingDay == config('constants.SELECTION_YES') ){
				$exitFormData['dt_notice_end_date'] = $exitFormData['dt_last_working_date'];
			} else {
				$exitFormData['dt_notice_end_date'] = $exitFormData['dt_system_last_working_date'];
			}
			
			
			
			$result = false;
			DB::beginTransaction();
				
			$successMessage = trans('messages.success-module-create' , [ 'module' => trans('messages.resign-request')  ] );
			$errorMessage = trans('messages.error-create' , [ 'module' => trans('messages.resign-request')  ] );
			
			
			
				
			try{
				if( !empty($checkRecordExits) )  {
					$exitFormData['dt_employee_notice_date'] = date('Y-m-d');
					//unset($exitFormData['e_initiate_type']);
					
					//dd($exitFormData);
					$successMessage = trans('messages.success-update' , [ 'module' => trans('messages.resign-request')  ] );
					$errorMessage = trans('messages.error-update' , [ 'module' => trans('messages.resign-request')  ] );
					
					
					if( $checkRecordExits->e_status == config('constants.APPROVED_STATUS') ) {
						
						$allChildEmployeeDetails = EmployeeModel::where('i_leader_id' , $employeeId )->where('e_employment_status' , '!=' , config('constants.RELIEVED_EMPLOYMENT_STATUS'))->get();
						$upcomingLeaderDetails = [];
						if(!empty($allChildEmployeeDetails)){
							foreach($allChildEmployeeDetails as $allChildEmployeeDetail){
								if( !empty($request->input('leader_for_' . $allChildEmployeeDetail->i_id)) ){
									$rowUpcomingLeader = [];
									$rowUpcomingLeader['employee_id'] = $allChildEmployeeDetail->i_id;
									$rowUpcomingLeader['leader_id'] = (int)Wild_tiger::decode($request->input('leader_for_' . $allChildEmployeeDetail->i_id));
									$upcomingLeaderDetails[] = $rowUpcomingLeader;
								}
							}
						}
						$exitFormData['v_upcoming_leader_info'] = (!empty($upcomingLeaderDetails) ? json_encode($upcomingLeaderDetails) : null  );
					
						
						
					}
					
					
					
					
					$this->crudModel->updateTableData(config('constants.EMPLOYEE_RESIGN_HISTORY'), $exitFormData, ['i_id' => $checkRecordExits->i_id ] );
					
					if( $checkRecordExits->e_status == config('constants.APPROVED_STATUS') ){
						$updateEmployeeData = [];
						$updateEmployeeData['dt_notice_period_start_date'] = $exitFormData['dt_employee_notice_date'];
						if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ){
							$updateEmployeeData['dt_pf_expiry_date'] = (!empty($request->input('pf_exit_date')) ? dbDate($request->input('pf_exit_date')) : null );
						}
						if( $exitFormData['e_last_working_day'] ==  config('constants.OTHER') ){
							$updateEmployeeData['dt_notice_period_end_date'] = $exitFormData['dt_last_working_date'];
						} else {
							$updateEmployeeData['dt_notice_period_end_date'] = $exitFormData['dt_system_last_working_date'];
						}
						$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateEmployeeData, ['i_id' => $employeeId ] );
					}
					
				}  else {
					
					$resignRecordId = $this->crudModel->insertTableData(config('constants.EMPLOYEE_RESIGN_HISTORY'), $exitFormData  );
					
					$this->sendResignTerminationMail( $exitFormData['i_employee_id'] , $resignRecordId , config('constants.RESIGN_REQUEST') );
	
				}
				$result = true;
			}catch(\Exception $e){
				//var_dump($e->getMessage());die;
				DB::rollback();
			}
				
			if( $result != false ){
				DB::commit();
				
				if(!empty($checkRecordExits)){
					if( $checkRecordExits->e_status == config('constants.APPROVED_STATUS') ) {
						if( ( $checkRecordExits->e_last_working_day != $exitFormData['e_last_working_day'] ) ||  ( strtotime($checkRecordExits->dt_last_working_date) !=  strtotime($exitFormData['dt_last_working_date']) ) || ( strtotime($checkRecordExits->dt_system_last_working_date) !=  strtotime($exitFormData['dt_system_last_working_date']) ) ){
							$this->sendResignTerminationMail( $exitFormData['i_employee_id'] , $checkRecordExits->i_id , config('constants.UPDATE_LAST_WORKING_DATE') );
						}
					}
				}
				
				
				$empWhere = [];
				$empWhere['master_id'] = $employeeId;
				$empWhere['singleRecord'] = true;
				$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
				if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
					$empWhere['show_all'] = true;
				}
				if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_RESIGNATION_REPORT'), session()->get('user_permission')  ) ) ){
					$empWhere['show_all'] = true;
				}
				
				$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
				$recordInfo = $data = [];
					
				$recordInfo['employeeRecordInfo'] = $recordDetail;
				$recordInfo['empId'] = Wild_tiger::encode($employeeId);
				$data['primaryDetailsInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/primary-details-list')->with ( $recordInfo )->render();
				$data['mainProfileInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/main-profile-info')->with ( $recordInfo )->render();
				
				$initateExitInfo = EmployeeModel::with(['latestResignHistory'])->where('i_id' , $employeeId)->first();
				$initateExitData = [];
				$initateExitData['employeeRecordInfo'] = $initateExitInfo;
				$initateExitHtml = view ($this->folderName . 'employee-notice-period-alert')->with ( $initateExitData )->render();
				
				$data['initateExitHtml'] = $initateExitHtml;
				$this->ajaxResponse(1, $successMessage , $data );
			} else {
				DB::rollback();
				$this->ajaxResponse(101, $errorMessage);
			}
		}
	
	}
	
	public function updateResignStatus(Request $request){
		if(!empty($request->post())){
			
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
			$status = (!empty($request->input('status')) ? $request->input('status') : null  );
			$remark = (!empty($request->input('remark')) ? $request->input('remark') : null  );
			
			if( (!empty($employeeId)) && (!empty($status)) ){
				$checkExitRecordWhere = [];
				$checkExitRecordWhere['i_employee_id'] = $employeeId;
				$checkExitRecordWhere['custom_function'][] = " e_status not in ( '".config('constants.REJECTED_STATUS')."' , '".config('constants.CANCELLED_STATUS')."'   ) ";
				$checkRecordExits = $this->crudModel->getSingleRecordById( config('constants.EMPLOYEE_RESIGN_HISTORY') , [ 'i_id' , 'e_status' ,'e_initiate_type' , 'dt_termination_notice_date'  , 'dt_employee_notice_date' , 'i_employee_id' ] , $checkExitRecordWhere  );
				
				if(!empty($checkRecordExits)){
					
					if( $checkRecordExits->e_status != config('constants.PENDING_STATUS')  ){
						$this->ajaxResponse(101, trans('messages.error-invalid-status-info' , [ 'status' => $checkRecordExits->e_status ] ) );
					}
					
					$updateData = [];
					$updateData['e_status'] = $status;
					$updateData['i_approved_by_id'] = session()->get('user_id');
					$updateData['dt_approved_at'] = date('Y-m-d H:i:s');
					$updateData['v_approval_remark'] = $remark;
				
					$updateEmployeeData = [];
					if( $status == config('constants.APPROVED_STATUS') ) {
						if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ){
							$updateEmployeeData['dt_pf_expiry_date'] =  (!empty($request->input('pf_exit_date')) ? dbDate($request->input('pf_exit_date')) : null );
						}
						
						$updateData['e_last_working_day'] = (!empty($request->input('approve_resign_initial_exit_recommend_last_working_day_type')) ? trim($request->input('approve_resign_initial_exit_recommend_last_working_day_type')) : config('constants.NOTICE_PERIOD') );
						$updateEmployeeData['e_employment_status'] = config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS');
						if( $checkRecordExits->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ){
							$updateData['dt_termination_notice_date'] = (!empty($request->input('approve_resign_initial_exit_employee_provide_notice_exit_date')) ? dbDate($request->input('approve_resign_initial_exit_employee_provide_notice_exit_date')) : $checkRecordExits->dt_termination_notice_date );
							$updateEmployeeData['dt_notice_period_start_date'] = $updateData['dt_termination_notice_date'];
							$updateData['dt_notice_start_date'] = $updateData['dt_termination_notice_date'];
						} else {
							$updateData['dt_employee_notice_date'] = (!empty($request->input('approve_resign_initial_exit_employee_provide_notice_exit_date')) ? dbDate($request->input('approve_resign_initial_exit_employee_provide_notice_exit_date')) : $checkRecordExits->dt_employee_notice_date );
							$updateEmployeeData['dt_notice_period_start_date'] = $updateData['dt_employee_notice_date'];
							$updateData['dt_notice_start_date'] = $updateData['dt_employee_notice_date'];
						}
						if(!empty($request->input('approve_resign_initial_exit_other_last_working_date'))){
							$updateData['dt_last_working_date'] = dbDate($request->input('approve_resign_initial_exit_other_last_working_date'));
							$updateEmployeeData['dt_notice_period_end_date'] = $updateData['dt_last_working_date'];
							$updateData['dt_notice_end_date'] = $updateData['dt_last_working_date'];
							
						}
						//echo "<pre>";print_r($updateData['e_last_working_day']);
						if( $updateData['e_last_working_day'] == config('constants.NOTICE_PERIOD') ){
						
							$employeeMasterInfo =  EmployeeModel::with(['noticePeriodInfo'])->where('i_id' , $checkRecordExits->i_employee_id )->first();
								
							$recordInfo['empJoiningDate'] = ( isset($employeeMasterInfo->dt_joining_date) ? $employeeMasterInfo->dt_joining_date : '' );
							$noticePeriodDuration = ( isset($employeeMasterInfo->noticePeriodInfo->v_probation_period_duration) ? $employeeMasterInfo->noticePeriodInfo->v_probation_period_duration . ( isset($employeeMasterInfo->noticePeriodInfo->e_months_weeks_days) ? ' ' . $employeeMasterInfo->noticePeriodInfo->e_months_weeks_days : ' '.config('constants.DEFAULT_NOTICE_PERIOD_DURATION')  ) : '' );
							
							//var_dump($noticePeriodDuration);
							
							if( isset($updateEmployeeData['dt_notice_period_start_date']) && (!empty($updateEmployeeData['dt_notice_period_start_date'])) ){
								$updateEmployeeData['dt_notice_period_end_date'] = date('Y-m-d' ,  strtotime("+ " . $noticePeriodDuration , strtotime($updateEmployeeData['dt_notice_period_start_date'])));
							} else {
								$updateEmployeeData['dt_notice_period_end_date'] = date('Y-m-d' ,  strtotime("+ " . $noticePeriodDuration , strtotime(date('Y-m-d'))));
							}
							$updateData['dt_notice_end_date'] = $updateEmployeeData['dt_notice_period_end_date'];
						}
						
						
						
						
						if( empty($updateData['e_last_working_day'])) {
							
							$this->ajaxResponse(101, trans('messages.system-error'));
						}
						
					
						
						
					}
					
					$successMessage = trans('messages.success-approve' , [ 'module' => trans('messages.resign-request') ] );
					$errorMessage = trans('messages.reject-approve' , [ 'module' => trans('messages.resign-request') ] );
					
					if( $status == config('constants.REJECTED_STATUS') ) {
						$successMessage = trans('messages.success-reject' , [ 'module' => trans('messages.resign-request') ] );
						$errorMessage = trans('messages.reject-reject' , [ 'module' => trans('messages.resign-request') ] );
					}
					//dd($updateEmployeeData);
					$result = false;
					DB::beginTransaction();
					$html = null;
					try{
						$upcomingLeaderDetails = [];
						if( $status == config('constants.APPROVED_STATUS') ) {
								
							$allChildEmployeeDetails = EmployeeModel::where('i_leader_id' , $employeeId )->where('e_employment_status' , '!=' , config('constants.RELIEVED_EMPLOYMENT_STATUS'))->get();
							
							if(!empty($allChildEmployeeDetails)){
								foreach($allChildEmployeeDetails as $allChildEmployeeDetail){
									if( !empty($request->input('leader_for_' . $allChildEmployeeDetail->i_id)) ){
										$rowUpcomingLeader = [];
										$rowUpcomingLeader['employee_id'] = $allChildEmployeeDetail->i_id;
										$rowUpcomingLeader['leader_id'] = (int)Wild_tiger::decode($request->input('leader_for_' . $allChildEmployeeDetail->i_id));
										$upcomingLeaderDetails[] = $rowUpcomingLeader;
									}
								}
							}
								
						}
						
						$updateData['v_upcoming_leader_info'] = (!empty($upcomingLeaderDetails) ? json_encode($upcomingLeaderDetails) : null  );
						
						$this->crudModel->updateTableData(config('constants.EMPLOYEE_RESIGN_HISTORY'), $updateData,  [ 'i_id' => $checkRecordExits->i_id  ] );
						
						if(!empty($updateEmployeeData)){
							$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateEmployeeData,  [ 'i_id' => $checkRecordExits->i_employee_id  ] );
						}
						
						$result = true;
					}catch(\Exception $e){
						//var_dump($e->getMessage());die;
						$result = false;
						DB::rollback();
					}
					
					//var_dump($result);die;
					if( $result != false ){
						DB::commit();
						
						$initateExitInfo = EmployeeModel::with(['latestResignHistory'])->where('i_id' , $employeeId)->first();
						$initateExitHtml = "";
						
						if( (!empty($initateExitInfo)) && ( $initateExitInfo->e_status != config('constants.REJECTED_STATUS') ) ){
							$initateExitData = [];
							$initateExitData['employeeRecordInfo'] = $initateExitInfo;
							$initateExitHtml = view ($this->folderName . 'employee-notice-period-alert')->with ( $initateExitData )->render();
						}
						
						$empWhere = [];
						$empWhere['master_id'] = $employeeId;
						$empWhere['singleRecord'] = true;
						$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
						if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
							$empWhere['show_all'] = true;
						}
						$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
						$recordInfo['employeeRecordInfo'] = $recordDetail;
						$recordInfo['empId'] = Wild_tiger::encode($employeeId);
						$primaryDetailsInfo = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/primary-details-list')->with ( $recordInfo )->render();
						$mainProfileInfo = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/main-profile-info')->with ( $recordInfo )->render();
						
						$resignationDataWhere = [];
						if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ){
							$resignationDataWhere['show_all'] = true;
						}
						$resignationDataWhere['employee_id'] = $employeeId;
						$resignationDataWhere['singleRecord'] = true;
						$resignationRecordDetail = $this->resignationReportModel->getResignRecordDetails( $resignationDataWhere );
						
						$reportRecordInfo = [];
						$reportRecordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
						$reportRecordInfo['recordDetail'] = $resignationRecordDetail;
						$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/single-resignation-report')->with ( $reportRecordInfo )->render();
							
						
						if( $checkRecordExits->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) {
							$this->sendResignTerminationMail( $employeeId , $checkRecordExits->i_id , config('constants.ACTION_RESIGN_REQUEST') );
						} else if( $checkRecordExits->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) {
							$this->sendResignTerminationMail( $employeeId  , $checkRecordExits->i_id , config('constants.ACTION_TERMINATION_REQUEST') );
						}
						
						
						$this->ajaxResponse(1, $successMessage , [ 'initateExitHtml' => $initateExitHtml , 'primaryDetailsInfo' => $primaryDetailsInfo , 'mainProfileInfo' => $mainProfileInfo ,'html' => $html  ]);
					}
					DB::rollback();
					$this->ajaxResponse(101, $errorMessage);
				}
			
			}
		}
		$this->ajaxResponse(101, trans('messages.system-error'));
	}
	
	public function empFilterByStatus(Request $request){
		
		if($this->secondUriSegment == 'probation-period-employee' ){
			session()->flash('selected_employee_status' , config('constants.PROBATION_EMPLOYMENT_STATUS'));
		}
		
		if($this->secondUriSegment == 'notice-period-employee' ){
			session()->flash('selected_employee_status' , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS'));
		}
		
		return redirect($this->redirectUrl);
		
	}
	
	public function uploadProfilePic(Request $request){
		if(!empty($request->post())){
			
			//dd($request->all());
			
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
			$formValidation =[];
			$formValidation['crop_profile_pic_image'] = 'required';
			
			 
			$validator = Validator::make ( $request->all (), $formValidation , [
					'crop_profile_pic_image.required' => __ ( 'messages.required-crop-image' ),
			]);
			if ($validator->fails ()) {
				$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.update-profile-picture') ] ) ) );
			} 
			
			$successMessage =  trans('messages.success-update',['module'=> trans('messages.profile-picture')]);
			$errorMessages = trans('messages.error-update',['module'=> trans('messages.profile-picture')]);
			
			$recordData = [] ;
			
			$getEmployeeWhere = [];
			$getEmployeeWhere['i_id'] = $employeeId;
			$getEmployeeInfo = EmployeeModel::where($getEmployeeWhere)->first();
			
			$employeeCode = (!empty($getEmployeeInfo) ? $getEmployeeInfo->v_employee_code : null );
			
			$uploadCropImage  =  $this->uploadCropFile($request, 'crop_profile_pic_image',  $this->documentFolder . $employeeCode . '/profile_pic/');
			
			if( ( isset($uploadCropImage) ) && ( $uploadCropImage['status']  != false )  ){
				
				$recordData['v_profile_pic'] = $uploadCropImage['filePath'];
				
				$result = false;
				DB::beginTransaction();
				try{
					
					$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , $recordData ,['i_id'=>$employeeId ]);
					
					$empWhere = [];
					$empWhere['master_id'] = $employeeId;
					$empWhere['singleRecord'] = true;
					$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
					if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
						$empWhere['show_all'] = true;
					}
					
					$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
					$recordInfo = $data = [];
					$recordInfo['employeeRecordInfo'] = $recordDetail;
					$recordInfo['empId'] = Wild_tiger::encode($employeeId);
					$data['mainProfileInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/main-profile-info')->with ( $recordInfo )->render();
					
					$result = true;
				}catch(\Exception $e){
					DB::rollback();
				}
				
				if( $result != false ){
					DB::Commit();
					$this->ajaxResponse(1, $successMessage ,$data);
				}
				DB::rollback();
				$this->ajaxResponse(101, $errorMessages);
				
			} else {
				$this->ajaxResponse(101, isset($uploadCropImage['message']) ? $uploadCropImage['message'] : trans('messages.error-file-upload') );
			}
		}
	}
	
	public function getSuspendInfo(Request $request){
		
		if(!empty($request->post())){
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
			$recordId = (!empty($request->input('suspend_record_id')) ? (int)Wild_tiger::decode($request->input('suspend_record_id')) : 0 );
			
			$getSuspendWhere = [];
			if( $employeeId > 0 ){
				$getSuspendWhere['i_employee_id'] = $employeeId;
				$getSuspendWhere['t_is_cancelled'] = 0;
			} else {
				//$getSuspendWhere['custom_function'][] =  "date(dt_start_date) >=  '".date('Y-m-d')."'";
			}
			if( $recordId > 0 ){
				$getSuspendWhere['i_id'] = $recordId;
			} else {
				$getSuspendWhere['custom_function'][] =  "date(dt_end_date) >=  '".date('Y-m-d')."'";
			}
			$getSuspendWhere['order_by'] = [ 'dt_start_date' => 'asc' ];
			
			$getSuspendInfo = $this->crudModel->getSingleRecordById(config('constants.SUSPEND_HISTORY_TABLE') , [ 'i_id' , 'dt_start_date' , 'dt_end_date' , 'v_suspend_reason' ] , $getSuspendWhere  );
			
			$data['suspendInfo'] = $getSuspendInfo;
			$data['suspendRecordId'] = (!empty($recordId) ? $recordId : (!empty($getSuspendInfo) ? $getSuspendInfo->i_id : 0 ) );
			
			$html = view( config('constants.AJAX_VIEW_FOLDER') . 'employee-master/employee-suspend', $data)->render();
			echo $html;die;
		}
		
	}
	public function addSuspendHistory(Request $request){
		
		if(!empty($request->post())){
			
			$suspendRecordId = (!empty($request->input('suspend_record_id')) ? (int)Wild_tiger::decode($request->input('suspend_record_id')) : 0 );
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
			
			$formValidation =[];
			$formValidation['suspend_from_date'] = ['required' , new UniqueSuspendDate($request)];
			$formValidation['suspend_to_date'] = ['required' , new UniqueSuspendDate($request)];
			$formValidation['suspension_reason'] = ['required'];
			
			$validator = Validator::make ( $request->all (), $formValidation , [
					'suspend_from_date.required' => __ ( 'messages.please-enter-from-date' ),
					'suspend_to_date.required' => __ ( 'messages.please-enter-to-date' ),
					'suspension_reason.required' => __ ( 'messages.please-enter-suspension-reason' ),
					
			] );
			if ($validator->fails ()) {
				$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.suspend') ] ) ) );
			}
			$result = false;
			DB::beginTransaction();
			
			$successMessage =  trans('messages.success-create',['module'=> trans('messages.suspend-info')]);
			$errorMessages = trans('messages.error-create',['module'=> trans('messages.suspend-info')]);
			
			
			$recordData = [] ;
			$recordData['i_employee_id'] = $employeeId;
			$recordData['dt_start_date'] = (!empty($request->post('suspend_from_date')) ? dbDate($request->post('suspend_from_date')) : '');
			$recordData['dt_end_date'] = (!empty($request->post('suspend_to_date')) ? dbDate($request->post('suspend_to_date')) : '');
			$recordData['v_suspend_reason'] = (!empty($request->post('suspension_reason')) ? $request->post('suspension_reason') : null);
			
			$updateSuspendHtml = false;
			$data = [];
			try{
				
				
				if( $suspendRecordId > 0 ) {
					
					$successMessage =  trans('messages.success-update',['module'=> trans('messages.suspend-info')]);
					$errorMessages = trans('messages.error-update',['module'=> trans('messages.suspend-info')]);
					
					/* $getSuspendRecordInfo = SuspendHistory::where('i_id' ,  $suspendRecordId )->where('t_is_deleted' , 0 )->get();
					
					$currentSuspendDates = getDatesFromRange( $recordData['dt_start_date'] ,  $recordData['dt_end_date'] );
					
					if(!empty($getSuspendRecordInfo)){
						$previousAllDates = getDatesFromRange( $getSuspendRecordInfo->dt_start_date ,  $getSuspendRecordInfo->dt_end_date );
						
					} */
					 
					
					$this->crudModel->updateTableData(config('constants.SUSPEND_HISTORY_TABLE'), $recordData , [ 'i_id' => $suspendRecordId  ]  );
				
					if( ( strtotime(date('Y-m-d')) >= strtotime($recordData['dt_start_date']) ) && ( strtotime($recordData['dt_end_date']) >= strtotime(date('Y-m-d'))  ) ){
						
						$updateSuspendHtml = true;
						
						$updateEmployeeData = [];
						$updateEmployeeData['t_is_suspended'] = 1;
						$updateEmployeeData['dt_suspended_start_date'] = $recordData['dt_start_date'];
						$updateEmployeeData['dt_suspended_end_date'] = $recordData['dt_end_date'];
						$updateEmployeeData['i_last_suspend_record_id'] = $suspendRecordId;
							
						$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateEmployeeData , [ 'i_id' => $employeeId  ]  );
						
					} else {
						
						$updateSuspendHtml = false;
						
						$updateEmployeeData = [];
						$updateEmployeeData['t_is_suspended'] = 0;
						$updateEmployeeData['dt_suspended_start_date'] = null;
						$updateEmployeeData['dt_suspended_end_date'] = null;
						$updateEmployeeData['i_last_suspend_record_id'] = null;
							
						$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateEmployeeData , [ 'i_id' => $employeeId  ]  );
					}
					
				} else {
					$insertSuspendRecord = $this->crudModel->insertTableData(config('constants.SUSPEND_HISTORY_TABLE'), $recordData  );
					
					/* $getAllDatesBetweenDates = getDatesFromRange( $recordData['dt_start_date'] ,  $recordData['dt_end_date'] );
					
					if(!empty($getAllDatesBetweenDates)){
						foreach($getAllDatesBetweenDates as $getAllDatesBetweenDate){
							$rowData = [];
							$rowData['i_employee_id'] = $employeeId;
							$rowData['dt_date'] = $getAllDatesBetweenDate;
							$rowData['e_status'] = config('constants.SUSPEND_STATUS');
							$this->crudModel->insertTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData );
						}
					} */
					
					
					if( ( strtotime(date('Y-m-d')) >= strtotime($recordData['dt_start_date']) ) && ( strtotime($recordData['dt_end_date']) >= strtotime(date('Y-m-d'))  ) ){
						
						$updateSuspendHtml = true;
						
						$updateEmployeeData = [];
						$updateEmployeeData['t_is_suspended'] = 1;
						$updateEmployeeData['dt_suspended_start_date'] = $recordData['dt_start_date'];
						$updateEmployeeData['dt_suspended_end_date'] = $recordData['dt_end_date'];
						$updateEmployeeData['i_last_suspend_record_id'] = $insertSuspendRecord;
							
						$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateEmployeeData , [ 'i_id' => $employeeId  ]  );
					}
					
					$this->sendSuspenderMail( $employeeId ,  $insertSuspendRecord , config('constants.SUSPEND_REQUEST') );
				}
				
				$empWhere = [];
				$empWhere['master_id'] = $employeeId;
				$empWhere['singleRecord'] = true;
				$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
				if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
					$empWhere['show_all'] = true;
				}
				
				$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
				$recordInfo = [];
				$recordInfo['employeeRecordInfo'] = $recordDetail;
				$recordInfo['empId'] = Wild_tiger::encode($employeeId);
				$data['primaryDetailsInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/primary-details-list')->with ( $recordInfo )->render();
				$data['mainProfileInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/main-profile-info')->with ( $recordInfo )->render();
				
			
				$result = true;
			}catch(\Exception $e){
				DB::rollback();
			}
			$data['updateSuspendHtml'] = $updateSuspendHtml;
			
			if( $result != false ){
				
				DB::commit();
				$this->ajaxResponse(1, $successMessage , $data );
			} else {
				DB::rollback();
				$this->ajaxResponse(101, $errorMessages);
			}
		}
	}
	
	public function checkUniqueSuspendDate(Request $request){
		
		$validator = Validator::make ( $request->all (), [
				'suspend_from_date' => ['required' , new UniqueSuspendDate($request)],
				'suspend_to_date' => ['required' , new UniqueSuspendDate($request)],
		], [
				'suspend_from_date.required' => __ ( 'messages.please-enter-from-date' ),
				'suspend_to_date.required' => __ ( 'messages.please-enter-to-date' ),
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
	
	public function checkUniquePersonalEmailId(Request $request){
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0  );
		$validator = Validator::make ( $request->all (), [
				'outlook_email_id' => [new UniquePersonalEmailId($recordId) ],
		], [
				'outlook_email_id' => __ ( 'messages.require-enter-outlook-email-id' ),
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
	
	public function showProbationHistory(Request $request){
		$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0  );
		$html = "";
		if( $employeeId > 0 ){
			$probationHistoryWhere = [];
			$probationHistoryWhere['t_is_deleted'] = 0;
			$probationHistoryWhere['i_employee_id'] = $employeeId;
			$probationHistoryDetails = $this->crudModel->selectData( config('constants.EMPLOYEE_PROBATION_HISTORY') , [ 'dt_start_date' , 'dt_end_date' , 'v_remark' ] , $probationHistoryWhere );
			
			
			if(!empty($probationHistoryDetails)){
				$rowIndex = 0;
				foreach($probationHistoryDetails as $probationHistoryDetail){
					$html .= '<tr>';
					$html .= '<td class="text-center">'.++$rowIndex.'</td>';
					$html .= '<td>'.convertDateFormat($probationHistoryDetail->dt_start_date).'</td>';
					$html .= '<td>'.convertDateFormat($probationHistoryDetail->dt_end_date).'</td>';
					$html .= '<td>'.(!empty($probationHistoryDetail->v_remark) ? $probationHistoryDetail->v_remark : '' ).'</td>';
					$html .= '</tr>';
				}
			}
		}
		if(empty($html)){
			$html = '<tr class="text-center"><td colspan="4">'.trans('messages.no-record-found').'</td></tr>';
		}
		echo $html;die;
		
	}
	
	public function sendLoginInvitation(Request $request){
		
		$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0  );
		if( $employeeId > 0 ){
			$whereData = [];
			$whereData['master_id'] = $employeeId;
			$whereData['singleRecord'] = true;
			$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
			if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
				$whereData['show_all'] = true;
			}
			$employeeInfo = $this->crudModel->getRecordDetails( $whereData );
			
			if( isset($employeeInfo->loginInfo) && (!empty($employeeInfo->loginInfo)) ){
				$email = ( isset($employeeInfo->loginInfo->v_email) ? $employeeInfo->loginInfo->v_email : '' ); 
				$password = randomPassword();
				if( config('constants.SEND_STATIC_PASSWORD') == 1 ){
					$password = "admin";
				}
				 
				
				if( (!empty($email)) ){
					
					$mailData = [];
					$mailData['employeeName'] =  ( isset($employeeInfo->v_employee_full_name) ? $employeeInfo->v_employee_full_name: 'User' );  ;
					$mailData['email'] = $email;
					$mailData['password'] =  $password ;
					$mailData['link'] = config('app.url');
					
					$mailTemplate = view( $this->mailTemplateFolderPath .  'login-invitation', $mailData)->render();
					//echo $mailTemplate;die;
					//var_dump($mailTemplate);die;
					$config['mailData'] = $mailData;
					$config['viewName'] =  $this->mailTemplateFolderPath .  'login-invitation' ;
					$config['v_mail_content'] = $mailTemplate;
					$config['subject'] = trans('messages.login-invitation-mail-subject' , [ 'companyName' => config('constants.COMPANY_NAME') ] );
					$config['to'] = $email ;
					
					$sendMail = [];
					try{
						$sendMail = sendMailSMTP($config);
					}catch(\Exception $e){
						
					}
					//var_dump($sendMail);die;
					if( isset($sendMail['status']) && ( $sendMail['status']  != false  ) ){
						
						$updatePassword = $this->crudModel->updateTableData( config('constants.LOGIN_MASTER_TABLE') , [ 'v_password' => password_hash($password, PASSWORD_DEFAULT ) ] , [ 'i_id' => $employeeInfo->i_login_id  ] );
						
						if( $updatePassword != false ){
							$this->ajaxResponse(1, trans('messages.success-login-invitation-sent'));
						} else {
							$this->ajaxResponse(101, trans('messages.error-login-invitation-sent'));
							
						}
						
						
					}
					$this->ajaxResponse(101, trans('messages.error-mail-send'));
				}  
				
			}
		}
		$this->ajaxResponse(101, trans('messages.system-error'));
	}
	
	public function getResignTerminateRequestInfo(Request $request){
		
		if(!empty($request->all())){
			
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0  );
			
			$getResignRecordWhere = null;
			//$getResignRecordWhere['i_employee_id'] = $employeeId;
			$getResignRecordWhere = "i_employee_id = '".$employeeId."' and  e_status in ( '".config('constants.APPROVED_STATUS')."' ,  '".config('constants.PENDING_STATUS')."' )";
			
			$recordInfo = [];
			$recordInfo['resignInfo'] = EmployeeResignHistory::with( ['employee' , 'resignation' , 'termination' ,  'employee.noticePeriodInfo' ] )->whereRaw($getResignRecordWhere)->first();
			
			$recordInfo['employeeId'] = Wild_tiger::encode($employeeId);
			
			
			$recordInfo['allChildEmployeeDetails'] = EmployeeModel::where('i_leader_id' , $employeeId )->where('e_employment_status' , '!=' , config('constants.RELIEVED_EMPLOYMENT_STATUS'))->get();
			$recordInfo['allEmployeeDetails'] = EmployeeModel::where('e_employment_status' , '!=' , config('constants.RELIEVED_EMPLOYMENT_STATUS'))->where('i_id' , '!=' , $employeeId )->get();
			
			$html = view ($this->folderName . 'approve-reject-resign-exit-form')->with ( $recordInfo )->render();
				
			echo $html;die;
			
		}
		
	}
	
	public function cancelResignation(Request $request){
		if(!empty($request->all())){
			$recordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0 );
			$recordType = (!empty($request->post('record_type')) ? trim($request->post('record_type')) : null );
			
			$checkExitRecordWhere['i_id'] = $recordId;
			
			$checkRecordExits = $this->crudModel->getSingleRecordById( config('constants.EMPLOYEE_RESIGN_HISTORY') , [ 'i_id' , 'e_status' ,'e_initiate_type' , 'dt_termination_notice_date'  , 'dt_employee_notice_date' , 'i_employee_id' ] , $checkExitRecordWhere  );
			
			$requestMessage = trans('messages.resign-request');
			
			if(!empty($checkRecordExits)){
				
				if( !in_array( $checkRecordExits->e_status , [ config('constants.PENDING_STATUS') , config('constants.APPROVED_STATUS') ] ) ){
					$this->ajaxResponse(101, trans('messages.error-invalid-status-info' , [ 'status' => $checkRecordExits->e_status ] ) );
				}
				
				if( $checkRecordExits->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE')  ){
					$requestMessage = trans('messages.termination-request');
				}
				
				$updateData = [];
				$updateData['e_status'] = config('constants.CANCELLED_STATUS');
				$updateData['i_approved_by_id'] = session()->get('user_id');
				
				$result = false;
				DB::beginTransaction();
				
				try{
					
					$this->crudModel->updateTableData( config('constants.EMPLOYEE_RESIGN_HISTORY') , $updateData , [ 'i_id' => $checkRecordExits->i_id  ] );
					
					$employeeInfo  = EmployeeModel::where('i_id'  , $checkRecordExits->i_employee_id )->first();
					
					if(!empty($employeeInfo)){
						$updateEmployeeData = [];
						if( $employeeInfo->e_in_probation == config('constants.SELECTION_YES') ){
							$updateEmployeeData['e_employment_status'] = config('constants.PROBATION_EMPLOYMENT_STATUS');
						} else {
							$updateEmployeeData['e_employment_status'] = config('constants.CONFIRMED_EMPLOYMENT_STATUS');
						}
						$updateEmployeeData['dt_notice_period_start_date'] = null;
						$updateEmployeeData['dt_notice_period_end_date'] = null;
						
						$this->crudModel->updateTableData( config('constants.EMPLOYEE_MASTER_TABLE') , $updateEmployeeData , [ 'i_id' => $checkRecordExits->i_employee_id  ] );
					}
					
					if( $checkRecordExits->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) {
						$this->sendResignTerminationMail( $checkRecordExits->i_employee_id , $checkRecordExits->i_id , config('constants.ACTION_RESIGN_REQUEST') );
					} else if( $checkRecordExits->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) {
						$this->sendResignTerminationMail( $checkRecordExits->i_employee_id  , $checkRecordExits->i_id , config('constants.ACTION_TERMINATION_REQUEST') );
					}
					
					$result = true;
					
				}catch(\Exception $e){
					//var_dump($e->getMessage());die;
					DB::rollback();
				}
				
				
				
				//var_dump($result);die;
				if( $result != false ){
					DB::commit();
					
					$empWhere = [];
					$empWhere['master_id'] = $checkRecordExits->i_employee_id;
					$empWhere['singleRecord'] = true;
					$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
					if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
						$empWhere['show_all'] = true;
					}
					
					$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
					$recordInfo = $data = [];
					
					$recordInfo['employeeRecordInfo'] = $recordDetail;
					$recordInfo['empId'] = Wild_tiger::encode($checkRecordExits->i_employee_id);
					$data['primaryDetailsInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/primary-details-list')->with ( $recordInfo )->render();
					$data['mainProfileInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/main-profile-info')->with ( $recordInfo )->render();
					
					
					$this->ajaxResponse(1, trans('messages.success-cancelled' , [ 'module' => $requestMessage ]) , $data);
				} else {
					DB::rollback();
					$this->ajaxResponse(101, trans('messages.error-cancelled' , [ 'module' => $requestMessage ]));
				}
				
			}
			
			
		}
	}
	
	public function showSuspendHistory(Request $request){
		$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0  );
		$html = "";
		if( $employeeId > 0 ){
			$suspendedHistoryDetails = SuspendHistory::where('i_employee_id',$employeeId)->where('t_is_deleted',0)->orderBy('dt_start_date', 'DESC')->get();
			if(!empty($suspendedHistoryDetails)){
				$rowIndex = 0;
				foreach ($suspendedHistoryDetails as $suspendedHistoryDetail){
					$encodeSuspensionId = Wild_tiger::encode($suspendedHistoryDetail->i_id);
					$html .= '<tr>';
					$html .= '<td class="text-center">'.++$rowIndex.'</td>';
					$html .= '<td>'.(!empty($suspendedHistoryDetail->dt_created_at) ? convertDateFormat($suspendedHistoryDetail->dt_created_at) :'').'</td>';
					$html .= '<td>'.(!empty($suspendedHistoryDetail->dt_start_date) ? convertDateFormat($suspendedHistoryDetail->dt_start_date) :'').'</td>';
					$html .= '<td>'.(!empty($suspendedHistoryDetail->dt_end_date) ? convertDateFormat($suspendedHistoryDetail->dt_end_date) :'').'</td>';
					$html .= '<td>'.(!empty($suspendedHistoryDetail->v_suspend_reason) ? $suspendedHistoryDetail->v_suspend_reason :'' ).'</td>';
					
					if( $suspendedHistoryDetail->t_is_cancelled == 1 ){
						$html .= '<td>'.trans('messages.cancelled').'</td>';
					} else {
						if( ( strtotime($suspendedHistoryDetail->dt_start_date) >= strtotime(date('Y-m-d')) ) && ( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )  ){
							$html .= '<td><button type="button" title="'.trans('messages.cancel').'" data-record-id="'.$encodeSuspensionId.'" onclick="cancelSuspension(this)" class="btn btn-sm bg-warning text-white btn-delete-icon"><i class="fa fa-solid fa-ban"></i></button></td>';
						} else {
							$html .= '<td></td>';
						}
					}
					
					
					$html .= '</tr>';
				}
			}
		}
		if(empty($html)){
			$html = '<tr class="text-center"><td colspan="6">'.trans('messages.no-record-found').'</td></tr>';
		}
		echo $html;die;
	
	}
	public function editUploadFileDocument(Request $request){
		$data = $where = [];
		$employeeId = (!empty($request->post('employee_id')) ? (int)Wild_tiger::decode($request->post('employee_id')) : 0 );
		$documentTypeId = (!empty($request->post('document_type_id')) ? (int)Wild_tiger::decode($request->post('document_type_id')) : 0 );
		
		$where['master_id'] = $documentTypeId;
		$where['employee_id'] = $employeeId;
		$where['singleRecord'] = true;
		
		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$where['show_all'] = true;
		}
		
		$getEmployeeDocumentRecordInfo = $this->employeeDocumentTypeModel->getRecordDetails($where);
		
		$getEmployeeDocumentRecordInfo = [];
		if(!empty($getEmployeeDocumentRecordInfo)){
			$data['recordInfo'] = $getEmployeeDocumentRecordInfo;
		}
		$html = view ($this->folderName . 'edit-emp-upload-document')->with ( $data )->render();
		echo $html;die;
	}
	
	public function getProfilePicInfo(Request $request){
		
		if(!empty($request->post())){
			
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
			//$employeeId = 10;
			
			$getEmployeeInfo = EmployeeModel::where('i_id' ,  $employeeId )->first();
			
			$profileFile = '' ;
			if(!empty( $getEmployeeInfo->v_profile_pic ) && file_exists( config('constants.FILE_STORAGE_PATH') . config('constants.UPLOAD_FOLDER') . $getEmployeeInfo->v_profile_pic ) ){
				$profileFile =  config('constants.FILE_STORAGE_PATH_URL') .  config('constants.UPLOAD_FOLDER') .  $getEmployeeInfo->v_profile_pic;
			}
			
			$response = [];
			$response['profile_pic'] = $profileFile;
			//$response['profile_pic'] = null;
			$this->ajaxResponse(1, trans("messages.success") , $response );
			
		}
		
	}
	
	public function sendResignTerminationMail($employeeId , $recordId ,  $action ){
	
		$applyLeaveWhere = [];
		$applyLeaveWhere['master_id'] = $recordId;
		$applyLeaveWhere['singleRecord'] = true;
		//var_dump($employeeId)	;
		$employeeInfo = EmployeeModel::where('i_id' , $employeeId)->first();
		$recordInfo = EmployeeResignHistory::with(['approveEmployeeInfo'])->where('i_id' , $recordId)->first(); 
		
		if( in_array( $action , [ config('constants.ACTION_TERMINATION_REQUEST') , config('constants.TERMINATION_REQUEST') ] ) ){
			return true;
		}
		
		if( (!empty($employeeInfo)) ){
			//echo "<pre>";print_r($applyLeaveInfo);die;
	
			$employeeName = ( isset($employeeInfo->v_employee_full_name) ? $employeeInfo->v_employee_full_name : 'User' );
			$employeeId = ( isset($employeeInfo->v_employee_code) ? $employeeInfo->v_employee_code : 'Id' );
			$supervisorName = ( isset($employeeInfo->leaderInfo->v_employee_full_name) ? $employeeInfo->leaderInfo->v_employee_full_name : config('constants.SYSTEM_ADMIN_NAME') );
	
			$employeeEmail = ( isset($employeeInfo->v_outlook_email_id) ? $employeeInfo->v_outlook_email_id : '' );
			$supervisorEmail = ( isset($employeeInfo->leaderInfo->v_outlook_email_id) ? $employeeInfo->leaderInfo->v_outlook_email_id : '' );
			$supervisorId = ( isset($employeeInfo->leaderInfo->i_login_id) ? $employeeInfo->leaderInfo->i_login_id : 0 );
	
			$recordStatus = ( isset($recordInfo->e_status) ? $recordInfo->e_status : '' );;
			$actionStatus = $recordStatus;
			switch($recordStatus){
				case config('constants.APPROVED_STATUS'):
					$recordStatus = trans('messages.accepted');
					break;
				case config('constants.REJECTED_STATUS'):
					$recordStatus = trans('messages.declined');
					break;
				case config('constants.CANCELLED_STATUS'):
					$recordStatus = trans('messages.cancelled');
					break;
			}
			
			$lastWorkingDate = date('Y-m-d');
			if(!empty($recordInfo)){
				switch($recordInfo->e_last_working_day){
					case config('constants.NOTICE_PERIOD'):
						$lastWorkingDate = $recordInfo->dt_system_last_working_date;
						break;
					case config('constants.OTHER'):
						$lastWorkingDate = $recordInfo->dt_last_working_date;
						break;
				}
			}
			
			$approvalName = ( isset($recordInfo->approveEmployeeInfo->v_name) ? $recordInfo->approveEmployeeInfo->v_name : '' );
			$actionTakenByName = session()->get('name');
			if(!empty($employeeEmail)){
	
				$mailData = [];
				$mailData['employeeName'] =  $employeeName;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['supervisorName'] =  $supervisorName;
				$mailData['recordStatus'] = $recordStatus;
				$mailData['lastWorkingDate'] = $lastWorkingDate;
				$mailData['approvalName'] = $approvalName;
				$mailData['actionStatus'] = $actionStatus;
				$mailData['actionTakenByName'] = $actionTakenByName;
				$mailData['supervisorMail'] = false;
	
				$mailTemplate = $viewName = $subject = null;
	
				switch($action){
					case config('constants.RESIGN_REQUEST'):
						$mailTemplate = view( $this->mailTemplateFolderPath .  'resign-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'resign-request-mail';
						$subject = trans('messages.resign-request-mail-subject' , [ 'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
					case config('constants.ACTION_RESIGN_REQUEST'):
						$mailData['sendCommonFooter'] = false;
						if( session()->get('user_employee_id') == $employeeId ){
							$mailData['sendCommonFooter'] = true;
						}
						if( $actionStatus == config('constants.CANCELLED_STATUS') ){
							$mailData['sendCommonFooter'] = false;
						}
						$mailTemplate = view( $this->mailTemplateFolderPath .  'approve-resign-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'approve-resign-request-mail';
						$subject = trans('messages.resign-request-action-mail-subject' , [  'status' => strtoupper($recordStatus) ,  'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
					case config('constants.ACTION_TERMINATION_REQUEST'):
						$mailData['sendCommonFooter'] = false;
						//$mailTemplate = view( $this->mailTemplateFolderPath .  'approve-termination-request-mail', $mailData)->render();
						//$viewName = $this->mailTemplateFolderPath .  'approve-termination-request-mail';
						//$subject = trans('messages.termination-request-supervisor-mail-subject' , [ 'status' => strtoupper($recordStatus) ,   'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
					case config('constants.UPDATE_LAST_WORKING_DATE'):
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'update-last-working-date-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'update-last-working-date-mail';
						$subject = trans('messages.update-last-working-date-mail-subject' , [ 'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
				}
	
				//var_dump($viewName);
				//var_dump($subject);
				//var_dump($mailTemplate);
				
				if( (!empty($mailTemplate)) && (!empty($viewName)) && (!empty($subject)) ){
					$emailHistoryData = [];
					$emailHistoryData['i_login_id'] = ( isset($employeeInfo->i_login_id) ? $employeeInfo->i_login_id : 0 );
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
					
					//var_dump($sendMail);
					
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
	
			if(!empty($supervisorEmail)){
	
				$mailData = [];
				$mailData['employeeName'] =  $employeeName;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['supervisorName'] =  $supervisorName;
				$mailData['recordStatus'] = $recordStatus;
				$mailData['lastWorkingDate'] = $lastWorkingDate;
				$mailData['approvalName'] = $approvalName;
				$mailData['actionStatus'] = $actionStatus;
				$mailData['actionTakenByName'] = $actionTakenByName;
				$mailData['actionDoneByName'] = $actionTakenByName;
				$mailData['userNameVerb'] = "has";
				$mailData['supervisorMail'] = true;
	
				if( isset($employeeInfo->leaderInfo->i_login_id) && ( $employeeInfo->leaderInfo->i_login_id == session()->get('user_id') ) ){
					$mailData['actionDoneByName'] = "You";
					$mailData['userNameVerb'] = "have";
				
				}
				
				switch($action){
					case config('constants.RESIGN_REQUEST'):
						//$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'resign-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'resign-request-mail';
						$subject = trans('messages.resign-request-mail-subject' , [  'employeeId' => $employeeId , 'employeeName' => $employeeName ]);
						break;
					case config('constants.ACTION_RESIGN_REQUEST'):
						if( in_array( $actionStatus , [ config('constants.CANCELLED_STATUS') , config('constants.REJECTED_STATUS') , config('constants.APPROVED_STATUS')  ]   ) ){
							$mailData['sendCommonFooter'] = false;
						}
						$mailTemplate = view( $this->mailTemplateFolderPath .  'approve-resign-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'approve-resign-request-mail';
						$subject = trans('messages.resign-request-action-mail-subject' , [ 'status' => strtoupper($recordStatus) ,   'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
					case config('constants.TERMINATION_REQUEST'):
						//$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'termination-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'termination-request-mail';
						$subject = trans('messages.termination-request-mail-subject' , [  'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
					case config('constants.ACTION_TERMINATION_REQUEST'):
						$mailTemplate = view( $this->mailTemplateFolderPath .  'approve-termination-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'approve-termination-request-mail';
						$subject = trans('messages.termination-request-supervisor-mail-subject' , [ 'status' => strtoupper($recordStatus) ,   'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
					case config('constants.UPDATE_LAST_WORKING_DATE'):
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'update-last-working-date-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'update-last-working-date-mail';
						$subject = trans('messages.update-last-working-date-mail-subject' , [ 'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
				}
				
	
				$emailHistoryData = [];
				$emailHistoryData['i_login_id'] = $supervisorId;
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
	
			if( isset($employeeInfo->i_id) && ( $employeeInfo->i_login_id != config('constants.ADMIN_LOGIN_ID') ) && ( ( empty($employeeInfo->i_leader_id) ) ||  (  isset($employeeInfo->leaderInfo->i_login_id) && ( $employeeInfo->leaderInfo->i_login_id != config('constants.ADMIN_LOGIN_ID') ) ) )  ){
				$mailData = [];
					
				$mailData['employeeName'] =  $employeeName;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['supervisorName'] =  config('constants.SYSTEM_ADMIN_NAME');
				$mailData['recordStatus'] = $recordStatus;
				$mailData['lastWorkingDate'] = $lastWorkingDate;
				$mailData['approvalName'] = $approvalName;
				$mailData['actionStatus'] = $actionStatus;
				$mailData['actionTakenByName'] = $actionTakenByName;
				$mailData['actionDoneByName'] = "You";
				$mailData['userNameVerb'] = "have";
				$mailData['supervisorMail'] = true;
				
				if( isset($recordInfo->i_approved_by_id) && ( $recordInfo->i_approved_by_id != config('constants.ADMIN_LOGIN_ID') ) ){
					$mailData['actionDoneByName'] = $actionTakenByName;
					$mailData['userNameVerb'] = "has";
				}
				
				switch($action){
					case config('constants.RESIGN_REQUEST'):
						$mailData['sendCommonFooter'] = false;
						if( $recordInfo->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ){
							$mailData['sendCommonFooter'] = true;
						}
						$mailTemplate = view( $this->mailTemplateFolderPath .  'resign-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'resign-request-mail';
						$subject = trans('messages.resign-request-mail-subject' , [  'employeeId' => $employeeId , 'employeeName' => $employeeName ]);
						break;
					case config('constants.ACTION_RESIGN_REQUEST'):
						if( in_array( $actionStatus , [ config('constants.CANCELLED_STATUS') , config('constants.REJECTED_STATUS') , config('constants.APPROVED_STATUS')  ]   ) ){
							$mailData['sendCommonFooter'] = false;
						}
						$mailTemplate = view( $this->mailTemplateFolderPath .  'approve-resign-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'approve-resign-request-mail';
						$subject = trans('messages.resign-request-action-mail-subject' , [ 'status' => strtoupper($recordStatus) ,   'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
					case config('constants.TERMINATION_REQUEST'):
						//$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'termination-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'termination-request-mail';
						$subject = trans('messages.termination-request-mail-subject' , [  'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
					case config('constants.ACTION_TERMINATION_REQUEST'):
						$mailTemplate = view( $this->mailTemplateFolderPath .  'approve-termination-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'approve-termination-request-mail';
						$subject = trans('messages.termination-request-supervisor-mail-subject' , [ 'status' => strtoupper($recordStatus) ,   'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
					case config('constants.UPDATE_LAST_WORKING_DATE'):
						$mailData['sendCommonFooter'] = false;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'update-last-working-date-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'update-last-working-date-mail';
						$subject = trans('messages.update-last-working-date-mail-subject' , [ 'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
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
	
	public function sendSuspenderMail($employeeId , $recordId ,   $action ){
	
		$applyLeaveWhere = [];
		$applyLeaveWhere['master_id'] = $recordId;
		$applyLeaveWhere['singleRecord'] = true;
			
		$employeeInfo = EmployeeModel::where('i_id' , $employeeId)->first();
		$recordInfo = SuspendHistory::where('i_id' , $recordId)->first();
	
		if( (!empty($employeeInfo)) ){
			//echo "<pre>";print_r($applyLeaveInfo);die;
	
			$employeeName = ( isset($employeeInfo->v_employee_full_name) ? $employeeInfo->v_employee_full_name : 'User' );
			$employeeId = ( isset($employeeInfo->v_employee_code) ? $employeeInfo->v_employee_code : 'Id' );
			$supervisorName = ( isset($employeeInfo->leaderInfo->v_employee_full_name) ? $employeeInfo->leaderInfo->v_employee_full_name : config('constants.SYSTEM_ADMIN_NAME') );
	
			$employeeEmail = ( isset($employeeInfo->v_outlook_email_id) ? $employeeInfo->v_outlook_email_id : '' );
			$supervisorEmail = ( isset($employeeInfo->leaderInfo->v_outlook_email_id) ? $employeeInfo->leaderInfo->v_outlook_email_id : '' );
			$supervisorId = ( isset($employeeInfo->leaderInfo->i_login_id) ? $employeeInfo->leaderInfo->i_login_id : '' );
	
			if(!empty($employeeEmail)){
	
				$mailData = [];
				$mailData['employeeName'] =  $employeeName;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['supervisorName'] =  $supervisorName;
				$mailData['suspendDuration'] = ( isset($recordInfo->dt_start_date) ? convertDateFormat($recordInfo->dt_start_date )  : '' ) . ' - ' .  ( isset($recordInfo->dt_end_date) ? convertDateFormat($recordInfo->dt_end_date )  : '' ) ;
				$mailData['supervisorMail'] = false;
	
	
				switch($action){
					case config('constants.SUSPEND_REQUEST'):
						$mailTemplate = view( $this->mailTemplateFolderPath .  'suspend-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'suspend-request-mail';
						$subject = trans('messages.suspend-request-mail' , [ 'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
				}
	
	
				$emailHistoryData = [];
				$emailHistoryData['i_login_id'] = ( isset($employeeInfo->i_login_id) ? $employeeInfo->i_login_id : 0 );
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
				$mailData['employeeName'] =  $employeeName;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['supervisorName'] =  $supervisorName;
				$mailData['suspendDuration'] = ( isset($recordInfo->dt_start_date) ? convertDateFormat($recordInfo->dt_start_date )  : '' ) . ' - ' .  ( isset($recordInfo->dt_end_date) ? convertDateFormat($recordInfo->dt_end_date )  : '' ) ;
				$mailData['supervisorMail'] = true;
	
				
				
				switch($action){
					case config('constants.SUSPEND_REQUEST'):
						$mailTemplate = view( $this->mailTemplateFolderPath .  'suspend-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'suspend-request-mail';
						$subject = trans('messages.suspend-request-supervisor-mail' , [ 'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
						break;
				}
	
	
				$emailHistoryData = [];
				$emailHistoryData['i_login_id'] = $supervisorId;
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
			
			if( isset($employeeInfo->i_id) && ( $employeeInfo->i_login_id != config('constants.ADMIN_LOGIN_ID') ) && ( ( empty($employeeInfo->i_leader_id) ) || (  isset($employeeInfo->leaderInfo->i_login_id) && ( $employeeInfo->leaderInfo->i_login_id != config('constants.ADMIN_LOGIN_ID') ) ) )  ){
				$mailData = [];
				$mailData['employeeName'] =  $employeeName;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['supervisorName'] =  config('constants.SYSTEM_ADMIN_NAME');
				$mailData['suspendDuration'] = ( isset($recordInfo->dt_start_date) ? convertDateFormat($recordInfo->dt_start_date )  : '' ) . ' - ' .  ( isset($recordInfo->dt_end_date) ? convertDateFormat($recordInfo->dt_end_date )  : '' ) ;			$mailData['supervisorMail'] = true;
				
				
				switch($action){
					case config('constants.SUSPEND_REQUEST'):
						$mailTemplate = view( $this->mailTemplateFolderPath .  'suspend-request-mail', $mailData)->render();
						$viewName = $this->mailTemplateFolderPath .  'suspend-request-mail';
						$subject = trans('messages.suspend-request-supervisor-mail' , [ 'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
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
	
	public function cancelSuspension(Request $request){
		
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
		
		if( $recordId > 0 ){
			$getRecordInfo = SuspendHistory::where('i_id',$recordId)->where('t_is_deleted',0)->first();
			
			if(empty($getRecordInfo)){
				$this->ajaxResponse(101, trans('messages.system-error'));
			}
			
			if( strtotime($getRecordInfo->dt_start_date) < strtotime(date('Y-m-d')) ){
				$this->ajaxResponse(101, trans('messages.past-date-cancellation-not-allowed'));
			}
			
			if( $getRecordInfo->t_is_cancelled == 1 ){
				$this->ajaxResponse(101, trans('messages.error-invalid-status-info' , [ 'status' => trans('messages.cancelled') ] ) );
			}
			
			$updateSuspendHtml = false;
			
			$updateData = [];
			$updateData['t_is_cancelled'] = 1;
			
			$updateRecord = $this->crudModel->updateTableData(config('constants.SUSPEND_HISTORY_TABLE'), $updateData, [ 'i_id' => $recordId ] );
			
			if( strtotime($getRecordInfo->dt_start_date) == strtotime(date('Y-m-d')) ){
				
				$employeeId = $getRecordInfo->i_employee_id;
				
				$updateSuspendHtml = true;
				
				$updateEmployeeData = [];	
				$updateEmployeeData['t_is_suspended'] = 0;
				$updateEmployeeData['dt_suspended_start_date'] = null;
				$updateEmployeeData['dt_suspended_end_date'] = null;
				$updateEmployeeData['i_last_suspend_record_id'] = null;
					
				$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateEmployeeData , [ 'i_id' => $employeeId  ]  );
				
				$empWhere = [];
				$empWhere['master_id'] = $checkRecordExits->i_employee_id;
				$empWhere['singleRecord'] = true;
				$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
				if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
					$empWhere['show_all'] = true;
				}
				
				$recordDetail = $this->crudModel->getRecordDetails( $empWhere );
				$recordInfo = [];
				$recordInfo['employeeRecordInfo'] = $recordDetail;
				$recordInfo['empId'] = Wild_tiger::encode($employeeId);
				$data['primaryDetailsInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/primary-details-list')->with ( $recordInfo )->render();
				$data['mainProfileInfo'] = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/main-profile-info')->with ( $recordInfo )->render();
				
			}
			$data['updateSuspendHtml'] = $updateSuspendHtml;
			
			if( $updateRecord != false ){
				$this->ajaxResponse(1, trans('messages.success-cancel' , [  'module' => trans('messages.suspension') ]  ) , $data );
			} else {
				$this->ajaxResponse(101, trans('messages.error-cancel' , [  'module' => trans('messages.suspension') ]  ) );
			}
			
		}
		$this->ajaxResponse(101, trans('messages.system-error'));
	}
	
public function importLeaveBalance(Request $request){
		
		if (!empty($request->file('upload_excel'))){
			$uploadedFilePath = null;
			
			$convertExcelToCSV = $this->convertExcelToCSV($request, 'upload_excel' , 'upload_excel/' );
			
			echo "<pre>";print_r($convertExcelToCSV);
			
			$csvFilePath = ( isset($convertExcelToCSV['csv_file_path']) ? $convertExcelToCSV['csv_file_path'] : null );
			$importFile = ( isset($convertExcelToCSV['excel_file_path']) ? $convertExcelToCSV['excel_file_path'] : null );
			
			$rowDetails = [];
			$excelKeys = [];
			if( (!empty($csvFilePath)) && file_exists($csvFilePath) ){
				$row = 1;
					
				if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
					while ( ( $data = fgetcsv($handle, 1000, ",")) !== FALSE ) {
						if( $row == 1 ){
							//echo "<pre>";print_r($data);
							$excelKeys = array_values($data);
							//echo "<pre>";print_r($excelKeys);die;
						} else {
							if(!empty($excelKeys)){
								$rowDetail = [];
								$rowDetail = array_combine($excelKeys, $data);
								if(!empty($rowDetail)){
									$rowDetails[] = $rowDetail;
								}
							}
						}
						$row++;
					}
					fclose($handle);
				}
			}
			//echo "<pre>";print_r($rowDetails);die;
			$finalExcelData = [];
			$finalExcelData = [];
			if(!empty($rowDetails)){
				foreach($rowDetails as $rowKey =>  $rowDetail){
					$rowExcelData = [];
					foreach( $rowDetail as $rowKey => $rowValue){
						$rowKey = strtolower( trim($rowKey) );
						$rowKey = str_replace(" ", "_", $rowKey);
						$rowValue = ( trim($rowValue) );
						switch($rowKey){
							case 'emp_code':
								$rowExcelData['employee_code'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'emp_full_name':
								$rowExcelData['full_name'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'available':
								$rowExcelData['paid_leave'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'earned_leave':
								$rowExcelData['earned_leave'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'carry_forward':
								$rowExcelData['carry_forward_leave'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'total':
								$rowExcelData['total'] = (!empty($rowValue) ? $rowValue : null);
								break;
						}
					}
					$finalExcelData[] = $rowExcelData;
				}
			}
			echo "<pre>";print_r($finalExcelData);
			
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
			$successMessage = trans('messages.success-sheet-import');
			$errorMessages = trans('messages.error-sheet-import');
			if(!empty($finalExcelData)){
			
				$result = false;
				DB::beginTransaction();
			
				$leaveTypes = [];
				//$leaveTypes[config('constants.PAID_LEAVE_TYPE_ID')] = 0;
				//$leaveTypes[config('constants.EARNED_LEAVE_TYPE_ID')] = 0;
				$leaveTypes[config('constants.CARRY_FORWARD_LEAVE_TYPE_ID')] = 0;
				$effectiveDate = date('Y-m-d');
				try{
					foreach($finalExcelData as $finalExcel){
						$employeeCode = ( isset($finalExcel['employee_code']) ? $finalExcel['employee_code'] : null ); 
						if(  in_array( $employeeCode , $allEmployeeCodeDetails ) ){
							$searchEmployeeKey = array_search($employeeCode , $allEmployeeCodeDetails  );
							if( strlen($searchEmployeeKey) > 0 && isset($getAllEmployeeDetails[$searchEmployeeKey]->i_id) && ( $getAllEmployeeDetails[$searchEmployeeKey]->i_id > 0 )  ){
								$employeeId = $getAllEmployeeDetails[$searchEmployeeKey]->i_id;
								
								if(!empty($leaveTypes)){
									foreach($leaveTypes as $leaveTypeKey => $leaveType){
										$leaveTypeId = $leaveTypeKey;
										
										if( $leaveTypeId == config('constants.PAID_LEAVE_TYPE_ID') && empty($finalExcel['paid_leave']) ){
											continue;
										}
										
										if( $leaveTypeId == config('constants.EARNED_LEAVE_TYPE_ID') && empty($finalExcel['earned_leave']) ){
											continue;
										} 
										
										if( $leaveTypeId == config('constants.CARRY_FORWARD_LEAVE_TYPE_ID') && empty($finalExcel['total']) ){
											continue;
										}
										$balance = 0;
										switch($leaveTypeId){
											case config('constants.PAID_LEAVE_TYPE_ID'):
												$balance = $finalExcel['paid_leave'];
												break;
											case config('constants.EARNED_LEAVE_TYPE_ID'):
												$balance = $finalExcel['earned_leave'];
												break;
											case config('constants.CARRY_FORWARD_LEAVE_TYPE_ID'):
												$balance = $finalExcel['total'];
												break;
										}
										
										
										//echo "<pre>";print_r($finalExcel);
										//var_dump($leaveTypeId);
										//var_dump($balance);die;
										if( $balance >  0 ){
											$leaveBalanceData = [];
											$leaveBalanceData['i_employee_id'] = $employeeId;
											$leaveBalanceData['i_leave_type_id'] = $leaveTypeId;
											$leaveBalanceData['dt_effective_date'] = $effectiveDate;
											$leaveBalanceData['d_no_of_days_assign'] = $balance;
											$leaveBalanceData['v_remark'] = "Leave Balance";
											
											$checkLeaveAssignHistoryWhere = [];
											$checkLeaveAssignHistoryWhere['i_employee_id'] = $employeeId;
											$checkLeaveAssignHistoryWhere['i_leave_type_id'] = $leaveTypeId;
											$checkLeaveAssignHistoryWhere['dt_effective_date'] = $effectiveDate;
											$checkLeaveAssignHistoryWhere['t_is_deleted'] = 0;
											$checkLeaveAssignHistory = $this->crudModel->getSingleRecordById(config('constants.LEAVE_ASSIGN_HISTORY_TABLE'), [ 'i_id' ] , $checkLeaveAssignHistoryWhere );
											echo $this->crudModel->last_query();echo "<br><br>";
											
											if(empty($checkLeaveAssignHistory)){
												$insertLeaveASsign = $this->crudModel->insertTableData(config('constants.LEAVE_ASSIGN_HISTORY_TABLE'), $leaveBalanceData );
												echo $this->crudModel->last_query();echo "<br><br>";
											}
											
											$leaveBalanceWhere  = [];
											$leaveBalanceWhere['i_employee_id'] = $employeeId;
											$leaveBalanceWhere['i_leave_type_id'] = $leaveTypeId;
											$leaveBalanceWhere['t_is_deleted != '] = 1;
											$checkLeaveAssigned = $this->crudModel->getSingleRecordById(config('constants.LEAVE_BALANCE_TABLE') , [ 'i_id' ] ,  $leaveBalanceWhere );
											echo $this->crudModel->last_query();echo "<br><br>";
											
											if(!empty($checkLeaveAssigned)){
												$updateLeaveBalance = [];
												$updateLeaveBalance['d_current_balance'] = $balance;
												$this->crudModel->updateTableData(config('constants.LEAVE_BALANCE_TABLE'), $updateLeaveBalance , [ 'i_id' =>  $checkLeaveAssigned->i_id  ] );
												echo $this->crudModel->last_query();echo "<br><br>";
											} else {
												$insertLeaveBalance = [];
												$insertLeaveBalance['i_employee_id'] = $employeeId;
												$insertLeaveBalance['i_leave_type_id'] = $leaveTypeId;
												$insertLeaveBalance['d_current_balance'] = $balance;
											
												$this->crudModel->insertTableData(config('constants.LEAVE_BALANCE_TABLE'), $insertLeaveBalance );
												echo $this->crudModel->last_query();echo "<br><br>";
											}
										}
									}
								}
							}
						}
					}
					$result = true;
				}catch(\Exception $e){
					var_dump($e->getMessage());die;
					$result = false;
					DB::rollback();
				}
				//die($result);
				if($result != false){
					DB::commit();
					Wild_tiger::setFlashMessage('success', $successMessage);
				}else {
					DB::rollback();
					Wild_tiger::setFlashMessage('danger', $errorMessages);
				}
				die("done");
				return redirect()->back();
			
			
			}
			
		}
		Wild_tiger::setFlashMessage('danger', trans('messages.no-record-found-for-import'));
		return redirect()->back();
	}
}
