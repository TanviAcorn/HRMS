<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncidentReport;
use Validator;
use App\Helpers\Twt\Wild_tiger;
use DB;
use App\EmployeeModel;
use App\Lookup;
use App\LookupMaster;
use function GuzzleHttp\json_encode;


class IncidentReportController extends MasterController
{
    //
	public function __construct(){
		parent::__construct();
		$this->folderName = config('constants.ADMIN_FOLDER') . 'incident-report/';
		$this->moduleName = trans('messages.incident-report');
		$this->crudModel = new IncidentReport();
		$this->tableName = config('constants.INCIDENT_REPORT_TABLE');
		$this->redirectUrl = config('constants.INCIDENT_REPORT_URL');
		$this->employeeCrudModel = new EmployeeModel();
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
	}
	public function index(){
		
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		$data = $whereData = [];
		$data['pageTitle'] = $this->moduleName;
		$page = $this->defaultPage;
		
		$allPermissionId = config('permission_constants.ALL_INCIDENT_REPORT');
		$data['allPermissionId'] = $allPermissionId;
		
		$teamId = ( session()->has('filter_employee_team_id') ? session()->get('filter_employee_team_id') : "" );
		$incidentCloseStatusCount = ( session()->has('filter_close_count_status') ? session()->get('filter_close_count_status') : null);
		$incidentOpenStatusCount = ( session()->has('filter_open_count_status') ? session()->get('filter_open_count_status') : null);
		
		if($teamId > 0 ){
			$whereData['team_record'] = $teamId;
		}
		
		if(!empty($incidentCloseStatusCount) && ($incidentCloseStatusCount == config("constants.CLOSE"))){
			$whereData['e_status'] = $incidentCloseStatusCount;
		}
		if(!empty($incidentOpenStatusCount) && ($incidentOpenStatusCount == config("constants.OPEN"))){
			$whereData['e_status'] = $incidentOpenStatusCount;
		}
		
		$selectEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
		$data['selectedEmployeeStatus'] = $selectEmployeeStatus;
		$whereData['employment_status'] = $selectEmployeeStatus;
		#store pagination data array
		$paginationData = [];
		
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$whereData['show_all'] = true;
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
		
		$empWhere = [];
		
		$empWhere['employment_status'] = config('constants.WORKING_EMPLOYMENT_STATUS');
		
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$empWhere['show_all'] = true;
		}
		
		$data['employeeRecordDetails'] = $this->getEmployeeDropdownDetails($empWhere);
		
		$data['teamRecordDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
		
		$data['teamId'] = $teamId;
		$data['incidentCloseCount'] = $incidentCloseStatusCount;
		$data['incidentOpenCount'] = $incidentOpenStatusCount;
		
		return view($this->folderName . 'incident-report' , $data);
	}
	public function filter(Request $request){
		if (!empty($request->input())){
			$likeData = $whereData = $data = [];
			$page = (! empty($request->post('page')) ? $request->post('page') : 1);
			
			if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_INCIDENT_REPORT'), session()->get('user_permission')  ) ) ){
				$whereData['show_all'] = true;
			}
			
			if (!empty($request->input('search_by'))){
				$searchValue = trim($request->input('search_by'));
				$likeData['v_subject'] = $searchValue;
			}
			if (!empty($request->input('search_from_date'))){
				$whereData['report_from_date'] = trim($request->input('search_from_date'));
			}
			if (!empty($request->input('search_to_date'))){
				$whereData['report_to_date'] = trim($request->input('search_to_date'));
			}
			if( ( !empty($request->post('search_employment_status') ) )){
				$whereData['employment_status'] =  $request->post('search_employment_status') ;
			}
			if (!empty($request->input('status'))){
				$whereData['e_status'] = (trim($request->input('status')) == config('constants.OPEN') ? config('constants.OPEN') : config('constants.CLOSE'));
			}
			if (!empty($request->input('search_employee'))){
				$searchEmployeeIds = explode(',', $request->input('search_employee'));
				if (!empty($searchEmployeeIds)){
					$searchEmployeeIds = array_map(function ($searchEmployeeId){
						return (int)Wild_tiger::decode($searchEmployeeId);
					}, $searchEmployeeIds);
				}
				if (!empty($searchEmployeeIds)){
					$whereData['employee_name'] = $searchEmployeeIds;
				}
			}
			if(!empty($request->post('search_team'))){
				$whereData['team_record'] = (int)Wild_tiger::decode($request->post('search_team'));
			}
			/*if (!empty($request->input('search_employee'))){
				$searchEmployeeIds = explode(',', $request->input('search_employee'));
				if (!empty($searchEmployeeIds)){
					$searchEmployeeIds = array_map(function ($searchEmployeeId){
						return (int)Wild_tiger::decode($searchEmployeeId);
					}, $searchEmployeeIds);
					
					$employeeSearch = '( ';
					if (!empty($searchEmployeeIds)){
						foreach ($searchEmployeeIds as $searchEmployeeId){
							$employeeSearch .= "find_in_set(".$searchEmployeeId." , v_employee_ids ) OR ";
						}
						$employeeSearch = rtrim($employeeSearch , "OR ");
						$employeeSearch .= " )";
						$whereData['custom_function'][] = $employeeSearch;
					}
				}
			} */
			$teamId = (!empty($request->input('employee_team_id')) ? (int)Wild_tiger::decode($request->input('employee_team_id')) : 0 );
			$incidentCloseStatusCount = (!empty($request->input('incident_close_count')) ? $request->input('incident_close_count') : "" );
			$incidentOpenStatusCount = (!empty($request->input('incident_open_count')) ? $request->input('incident_open_count') : "" );
			
			if($teamId > 0 ){
				$whereData['team_record'] = $teamId;
			}
			
			if(!empty($incidentCloseStatusCount) && ($incidentCloseStatusCount == config("constants.CLOSE"))){
				$whereData['e_status'] = $incidentCloseStatusCount;
			}
			if(!empty($incidentOpenStatusCount) && ($incidentOpenStatusCount == config("constants.OPEN"))){
				$whereData['e_status'] = $incidentOpenStatusCount;
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
			
			$html = view(config('constants.AJAX_VIEW_FOLDER') . 'incident-report/incident-report-list')->with($data)->render();
			echo $html;die;
		}
	}
	public function showAddForm(){
		$data = [];
		$data['pageTitle'] = trans('messages.add-incident-report');
		
		$empWhere = [];
		$empWhere['employment_status'] = config('constants.WORKING_EMPLOYMENT_STATUS');
		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_INCIDENT_REPORT'), session()->get('user_permission')  ) ) ){
			$empWhere['show_all'] = true;
		}
		$empWhere['login_status'] = 1;
		$data['employeeDetails'] = $this->getEmployeeDropdownDetails($empWhere);
		
		return view($this->folderName . 'add-incident-report' , $data);
	}
	public function showEditForm($id = NULL){
		$errorFound = true;
		if (!empty($id)){
			$data = $where = [];
			$data['pageTitle'] = trans('messages.update-incident-report');
			
			$empWhere = [];
			$empWhere['employment_status'] = config('constants.WORKING_EMPLOYMENT_STATUS');
			if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_INCIDENT_REPORT'), session()->get('user_permission')  ) ) ){
				$empWhere['show_all'] = true;
				$where['show_all'] = true;
			}
			$data['employeeDetails'] = $this->getEmployeeDropdownDetails($empWhere);
			
			$recordId = (int)Wild_tiger::decode($id);
			if ($recordId > 0){
				$where['master_id'] = $recordId;
				$where['singleRecord'] = true;
				$data['recordInfo'] = $this->crudModel->getRecordDetails($where);
				if (!empty($data['recordInfo'])){
					$errorFound = false;
					return view($this->folderName . 'add-incident-report' , $data);
				}
			}
		}
		
		if ($errorFound != false){
			return redirect ( config('constants.404_PAGE') );
		}
	}
	public function add(Request $request){
		
		if (!empty($request->input())){
			$formValidation = [];
			$formValidation['date'] = ['required'];
			$formValidation['employee'] = ['required'];
			$formValidation['subject'] = ['required'];
			
			$validator = Validator::make($request->all() , $formValidation , [
					'date.required' => __ ('messages.require-report-date'),
					'employee.required' => __ ( 'messages.require-employee' ),
					'subject.required' => __ ( 'messages.require-subject' ),
			]);
			if ($validator->fails()){
				return redirect()->back()->withErrors($validator)->withInput();
			}else{
				$successMessage = trans('messages.success-create' , ['module' => $this->moduleName]);
    			$errorMessage = trans('messages.error-create' , ['module' => $this->moduleName]);
    			
    			$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0);
    			
    			$otherAttachmentCount = (!empty($request->input('other_attachment_count')) ? $request->input('other_attachment_count') : 1);
    			$removeImageId = (!empty($request->post('remove_image_id')) ? $request->post('remove_image_id') :'');
    			
    			$allOtherAttachmentDetails = [];
    			for($i = 1 ; $i <= $otherAttachmentCount ; $i++ ){
    				$rowData = [];
    				if (!empty($request->file('document_'.$i))){
    					$fileUpload = $this->uploadFile($request, 'document_'.$i ,config('constants.UPLOAD_FOLDER'), ['jpg' , 'jpeg' , 'png' , 'pdf' , 'excel' , 'doc']);
    					
    					if (isset($fileUpload['status']) && $fileUpload['status'] == 1){
    						$rowData['v_file_path'] = $fileUpload['filePath'];
    					}else {
    						Wild_tiger::setFlashMessage('danger', isset($fileUpload['message']) ? $fileUpload['message'] : trans('messages.system-error'));
    						return redirect()->back()->withInput();
    					}
    				}
    				$rowData['v_remarks'] = (!empty($request->input('remark_'.$i)) ? trim($request->input('remark_'.$i)) : null);
    				
    				if (isset($rowData['v_file_path']) || !empty($rowData['v_file_path'])){
    					$allOtherAttachmentDetails[] = $rowData;
    				}
    				
    			}
    			
    			$recordData = [];
    			
    			$recordData['dt_report_date'] = (!empty($request->input('date')) ? dbDate($request->input('date')) : '');
    			$tagEncodedIds = (!empty($request->input('employee')) ? $request->input('employee') : []);
    			
    			$tagDecodeIds = array_map(function($tagEncodedIds){
    				return (int)Wild_tiger::decode($tagEncodedIds);
    			}, $tagEncodedIds);
    			
    			$recordData['v_employee_ids'] = (!empty($tagDecodeIds) ? json_encode($tagDecodeIds) : []);
    			$recordData['v_subject'] = (!empty($request->input('subject')) ? trim($request->input('subject')) : '');
    			$recordData['v_went_wrong'] = (!empty($request->input('what_went_wrong')) ? trim(htmlentities($request->input('what_went_wrong'))) : null);
    			$recordData['v_actions_taken'] = (!empty($request->input('what_actions_have_been_taken')) ? trim(htmlentities($request->input('what_actions_have_been_taken'))) : null);
    			$recordData['v_prevent_in_future'] = (!empty($request->input('what_we_do_prevent_in_future')) ? trim(htmlentities($request->input('what_we_do_prevent_in_future'))) : null);
    			$recordData['v_comments'] = (!empty($request->input('hr_comments')) ? trim(htmlentities($request->input('hr_comments'))) : null);
    			
    			$removeAttachmentIds = (isset($removeImageId) ? explode(",", $removeImageId) : []);
    			
    			DB::beginTransaction();
    			$result = false;
    			try {
    				if ($recordId > 0){
    					
    					$successMessage = trans('messages.success-update' , ['module' => $this->moduleName]);
    					$errorMessage = trans('messages.error-update' , ['module' => $this->moduleName]);
    					
    					$where = [];
    					$where['singleRecord'] = true;
    					$where['master_id'] = $recordId;
    					if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_INCIDENT_REPORT'), session()->get('user_permission')  ) ) ){
    						$where['show_all'] = true;
    					}
    					$recordDetails = $this->crudModel->getRecordDetails($where);
    					
    					if (isset($recordDetails) && !empty($recordDetails['incidentAttachment'])){
    						
    						foreach ($recordDetails['incidentAttachment'] as $incidentAttachment){
    							$incidentAttachmentId = (!empty($incidentAttachment->i_id) ? $incidentAttachment->i_id :'');
    							$rowData = [];
    							if( (!empty($incidentAttachmentId)) && (in_array($incidentAttachmentId, $removeAttachmentIds))){
    								$deleteRecordData = [];
    								$deleteRecordData ['t_is_active'] = 0;
    								$deleteRecordData ['t_is_deleted'] = 1;
    								
    								$this->crudModel->deleteTableData( config('constants.INCIDENT_ATTACHMENT_TABLE') , $deleteRecordData , [ 'i_id' => $incidentAttachmentId] );
    							} else{
    								$fileUpload = $this->uploadFile($request, 'edit_document_'.$incidentAttachmentId , $this->folderName , ['jpg' , 'jpeg' , 'png' , 'pdf' , 'excel' , 'doc']);
    								
    								if (isset($fileUpload['status']) && $fileUpload['status'] == 1){
    									$rowData['v_file_path'] = $fileUpload['filePath'];
    								}else {
    									Wild_tiger::setFlashMessage('danger', isset($fileUpload['message']) ? $fileUpload['message'] : trans('messages.system-error'));
    								}
    								
    								$rowData['v_remarks'] = (!empty($request->post('edit_remark_'.$incidentAttachmentId)) ? trim($request->post('edit_remark_'.$incidentAttachmentId)) : null );
    								$this->crudModel->updateTableData( config('constants.INCIDENT_ATTACHMENT_TABLE') , $rowData, ['i_id' => $incidentAttachmentId]);
    							}
    							
    						}
    					}
    					
    					$result = $this->crudModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId]);
    					
    				}else {
    					
    					$incidentReportDetails = IncidentReport::all();
    					$incidentReportDate = (!empty($request->input('date')) ? dbDate($request->input('date')) : '');
    					$generateIncidentReportNo = config('constants.INCNT').'-'.config('constants.INCNT_REPORT_NO').'-'.convertDateFormat( $incidentReportDate , 'dmY' );
    					
    					if(!empty($incidentReportDetails)){
    						$incidentReportRecordCount = count($incidentReportDetails);
    						$count = (!empty($incidentReportRecordCount) && $incidentReportRecordCount > 0 ? $incidentReportRecordCount + 1 : 1);
    						$generateNumber = threeNumberSeries($count);
    						$generateIncidentReportNo = config('constants.INCNT').'-'.$generateNumber.'-'.convertDateFormat( $incidentReportDate , 'dmY' );
    					}
    					$recordData['v_report_no'] = (!empty($generateIncidentReportNo)  ? $generateIncidentReportNo : '');
    					
    					$insertAttachmentId = $this->crudModel->insertTableData($this->tableName, $recordData);
    					$recordId = $insertAttachmentId;
    					if ($insertAttachmentId > 0){
    						$result = true;
    					}
    				}
    				
    				if (!empty($allOtherAttachmentDetails)){
    					$allOtherAttachmentDetails = array_map(function($allOtherAttachmentDetail) use ($recordId){
    						$allOtherAttachmentDetail['i_incedent_id'] = $recordId;
    						$allOtherAttachmentDetail ['i_created_id'] = session()->get('user_id');
    						$allOtherAttachmentDetail ['dt_created_at'] = date('Y-m-d H:i:s');
    						return $allOtherAttachmentDetail;
    					}, $allOtherAttachmentDetails);
    					DB::table(config('constants.INCIDENT_ATTACHMENT_TABLE'))->insert($allOtherAttachmentDetails);
    				}
    			}catch (\Exception $e){
    				var_dump($e);
    				DB::rollback();
    				die;
    			}
    			
    			if ($result != false){
    				DB::commit();
    				Wild_tiger::setFlashMessage('success', $successMessage);
    			}else {
    				DB::rollback();
    				Wild_tiger::setFlashMessage('danger', $errorMessage);
    			}
    			return redirect($this->redirectUrl);
			}
		}
	}
	public function delete(Request $request){
		if (!empty($request->input())){
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode($request->input('delete_record_id')) : 0);
			return $this->removeRecord($this->tableName, $recordId, $this->moduleName);
		}
	}
	public function viewIncidentReport(Request $request){
		return $this->ajaxResponse(1, 'success');
	}
	public function updateStatus(Request $request){
		if (!empty($request->input())){
			$successMessage = trans('messages.success-update' , ['module' => $this->moduleName]);
			$errorMessage = trans('messages.error-update' , ['module' => $this->moduleName]);
			
			$result = false;
			
			$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0);
			
			$updateData = [];
			$updateData['e_status'] = config('constants.CLOSE');
			$updateData['dt_close_date'] = (!empty($request->input('close_date')) ? dbDate(trim($request->input('close_date'))) : dbDate(config('constants.CURRENT_DATE')));
			$updateData['v_remarks'] = (!empty($request->input('remarks')) ? trim($request->input('remarks')) : null);
			$updateData['i_close_by_id'] = session()->get('user_id');
			$updateData['dt_system_closed_at'] = dbDate(config('constants.CURRENT_DATE'));
			$result = $this->crudModel->updateTableData($this->tableName, $updateData, ['i_id' => $recordId]);
			$recordInfo = [];
			$where = [];
			$where['master_id'] = $recordId;
			$where['singleRecord'] = true;
			$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
			
			if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_INCIDENT_REPORT'), session()->get('user_permission')  ) ) ){
				$where['show_all'] = true;
			}
			$recordInfo['recordDetail'] = $this->crudModel->getRecordDetails($where);
			$html = view(config('constants.AJAX_VIEW_FOLDER').'incident-report/single-incident-report-list')->with($recordInfo)->render();
			
			if ($result != false){
				$this->ajaxResponse(1, $successMessage , ['html' => $html]);
			}else {
				$this->ajaxResponse(101, $errorMessage);
			}
		}
	}
	public function incidentSummary(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		$data = [];
		$data['pageTitle'] = trans('messages.incident-summary');
		$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->orderBy('v_value', 'ASC')->get();
		$employeeDetails = EmployeeModel::where('t_is_deleted' , 0)->where('e_employment_status' , '!=' , config('constants.RELIEVED_EMPLOYMENT_STATUS'))->get();
		$incidentDetails = IncidentReport::where('t_is_deleted' , 0)->get();
		$incidentOpenDetails = IncidentReport::where('t_is_deleted' , 0)->where('e_status',config('constants.OPEN'))->get();
		$incidentCloseDetails = IncidentReport::where('t_is_deleted' , 0)->where('e_status',config('constants.CLOSE'))->get();
		
		$data['employeeCountInfo'] = count($employeeDetails);
		$data['incidentCountInfo'] = count($incidentDetails);
		$data['incidentOpenCountInfo'] = count($incidentOpenDetails);
		$data['incidentCloseCountInfo'] = count($incidentCloseDetails);
		
		return view( $this->folderName . 'incident-summary')->with($data);
	}
	public function incidentSummaryFilter(Request $request){
		$whereData = $openIncidentWhere = $closeIncidentWhere = $empWhere = [];
		
		$teamId = (!empty($request->post('search_team')) ? (int)Wild_tiger::decode($request->post('search_team')) : 0);
		
		$empWhere['team_record'] = $teamId = $whereData['team_record'] = $openIncidentWhere['team_record'] = $closeIncidentWhere['team_record'] = $teamId;
		
		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_INCIDENT_SUMMARY'), session()->get('user_permission')  ) ) ){
			$whereData['show_all'] = true;
			$empWhere['show_all'] = true;
			$openIncidentWhere['show_all'] = true;
			$closeIncidentWhere['show_all'] = true;
		}
		
		$empWhere['employment_status'] = config('constants.WORKING_EMPLOYMENT_STATUS');
		$employeeDetails = $this->employeeCrudModel->getRecordDetails( $empWhere);
		
		$incidentDetails = $this->crudModel->getRecordDetails($whereData);
		
		$openIncidentWhere['e_status'] = config('constants.OPEN');
		$incidentOpenDetails = $this->crudModel->getRecordDetails($openIncidentWhere);
		
		$closeIncidentWhere['e_status'] = config('constants.CLOSE');
		$incidentCloseDetails = $this->crudModel->getRecordDetails($closeIncidentWhere);
		
		$data['employeeCountInfo'] = count($employeeDetails);
		$data['incidentCountInfo'] = count($incidentDetails);
		$data['incidentOpenCountInfo'] = count($incidentOpenDetails);
		$data['incidentCloseCountInfo'] = count($incidentCloseDetails);
		$data['employeeTeamId'] = $teamId;
		
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employees-summary/incident-summary-filter')->with ( $data )->render();
		echo $html;die;
	}
	public function incidentReportStatusFilter($incidentLinkInfo = null){
		
		if(!empty($incidentLinkInfo)){
			$incidentInfo = trim(Wild_tiger::decode($incidentLinkInfo));
			$incidentStatusInfo = json_decode($incidentInfo,true);
			
			if( isset($incidentStatusInfo) && ( (!empty($incidentStatusInfo['team_id'])) ) ){
				session()->flash('filter_employee_team_id' , (int)($incidentStatusInfo['team_id']) );
			}
			if( isset($incidentStatusInfo) && ( !empty($incidentStatusInfo['incident_open_status']) ) ){
				session()->flash('filter_open_count_status' ,  trim($incidentStatusInfo['incident_open_status']) );
			}
			if( isset($incidentStatusInfo) && ( !empty($incidentStatusInfo['incident_close_status']) ) ){
				session()->flash('filter_close_count_status' ,  trim($incidentStatusInfo['incident_close_status']) );
			}
		}
		 
		return redirect( config('constants.INCIDENT_REPORT_URL') );
	}
	public function viewIncidentStatus(Request $request){
		
		$where = $data = [];
		if(!empty($request->post())){
			$recordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0 );
			if($recordId > 0 ){
				if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_INCIDENT_REPORT'), session()->get('user_permission')  ) ) ){
					$where['show_all'] = true;
				}
				$where['master_id'] = $recordId;
				$where['singleRecord'] = true;
				$incidentReportInfo = $this->crudModel->getRecordDetails($where);
				
				if(!empty($incidentReportInfo)){
					$data['incidentReportInfo'] = $incidentReportInfo;
				}
			}
			
			$html = view ($this->folderName . 'view-incident-close-status')->with ( $data )->render();
			echo $html;die;
		}
	}
}
