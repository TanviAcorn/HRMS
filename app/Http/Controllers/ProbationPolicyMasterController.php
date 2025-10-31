<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\ProbationPolicyMasterModel;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
class ProbationPolicyMasterController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->crudModel =  new ProbationPolicyMasterModel();
		$this->moduleName = trans('messages.probation-policy-master');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->tableName = config('constants.PROBATION_POLICY_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'probation-policy-master/' ;
		$this->redirectUrl = config('constants.PROBATION_POLICY_MASTER_URL');
	
	}
	public function index(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		$data = [];
		$data['pageTitle'] = trans('messages.probation-policy-master');
		
		$page = $this->defaultPage;
		#store pagination data array
		$whereData = $paginationData = [];
		
		if(  $this->firstUriSegment == config('constants.NOTICE_PERIOD_POLICY_MODULE_SLUG')  ){
			$whereData['record_status'] = config('constants.NOTICE_PERIOD_POLICY');
			$data ['pageTitle'] = trans('messages.notice-period-policy-master');
			$data ['addPageTitle'] = trans('messages.add-notice-period-policy');
			$data['recordType'] = config('constants.NOTICE_PERIOD_POLICY');
				
		} else {
			$whereData['record_status'] = config('constants.PROBATION_POLICY');
			$data ['addPageTitle'] = trans('messages.add-probation-policy');
			$data['recordType'] = config('constants.PROBATION_POLICY');
			
		}
		$whereData['months_weeks_days'] = config('constants.MONTH_DURATION');
		#get pagination data for first page
		if($page == $this->defaultPage ){
			
			$totalRecords = count($this->crudModel->getRecordDetails($whereData));
			
			$lastPage = ceil($totalRecords/$this->perPageRecord);
	
			$paginationData['current_page'] = $this->defaultPage;
	
			$paginationData['per_page'] = $this->perPageRecord;
	
			$paginationData ['last_page'] = $lastPage;
	
		}
		$whereData ['limit'] = $this->perPageRecord;
		
		$data['recordDetails'] = $this->crudModel->getRecordDetails($whereData );
		
		$data['pagination'] = $paginationData;
			
		$data['page_no'] = $page;
			
		$data['perPageRecord'] = $this->perPageRecord;
		
		$data['totalRecordCount'] = $totalRecords;
	
		$data['getMonthWeeksDaysInfo'] = getMonthWeeksDaysInfo();
		
		return view( $this->folderName . 'probation-policy-master')->with($data);
	}
	public function edit(Request $request){
		$data = $whereData = [];
		$recordId = (!empty($request->input('record_id')) ? trim($request->input('record_id')) : '' );
		$recordType = (!empty($request->input('record_type')) ? trim($request->input('record_type')) : '' );
		if(!empty($recordId)){
			$recordId = (int)Wild_tiger::decode($recordId);
			$whereData['master_id'] = $recordId;
			$whereData['singleRecord'] = true;
			$recordInfo = $this->crudModel->getRecordDetails($whereData);
			
			if(!empty($recordInfo)){
				$data ['recordInfo'] = (!empty($recordInfo)  ? $recordInfo : '');
			}
		}
		$data['getMonthWeeksDaysInfo'] = getMonthWeeksDaysInfo();
		$data['recordType'] = $recordType;
		$html = view ($this->folderName . 'add-probation-policy-master')->with ( $data )->render();
		echo $html;die;
	
	}
	public function add(Request $request){
		if(!empty($request->input())){
			$recordType = (!empty($request->input('record_type')) ? trim($request->input('record_type')) :'');
			
			if($recordType == config('constants.NOTICE_PERIOD_POLICY')){
				$probationPolicyName = trans('messages.require-notice-policy-name');
				$probationPolicyDescription = trans('messages.require-notice-policy-description');
				$probationPolicyDuration = trans('messages.require-notice-policy-duration');
				$successResponse = trans('messages.notice-period-policy-master');
			
			} else{
				$probationPolicyName = trans('messages.require-probation-policy-name');
				$probationPolicyDescription = trans('messages.require-probation-policy-description');
				$probationPolicyDuration = trans('messages.require-probation-policy-duration');
				$successResponse = trans('messages.probation-policy-master');
				
			}
			$formValidation =[];
			$formValidation['probation_policy_name'] = ['required'];
			//$formValidation['probation_policy_description'] = ['required'];
			$formValidation['probation_policy_duration'] = ['required'];
			$formValidation['months_weeks_days'] = ['required'];
				
			$checkValidation = Validator::make($request->all(),$formValidation,
					[
							'probation_policy_name.required' => $probationPolicyName,
							'probation_policy_description.required' => $probationPolicyDescription,
							'probation_policy_duration.required' => $probationPolicyDuration,
							'months_weeks_days.required' => __('messages.require-months-weeks-days'),
					]
			);
			if($checkValidation->fails() != false){
				$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => $successResponse ] ) ) );
			}
			$recordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0);
			
			$successMessage =  trans('messages.success-create',['module'=> $successResponse]);
			$errorMessages = trans('messages.error-create',['module'=> $successResponse]);
			$result = false;
			$html= null;
			$recordData = [];
			$recordData['v_probation_policy_name'] = (!empty($request->post('probation_policy_name')) ? trim($request->post('probation_policy_name')) :'');
			$recordData['v_probation_policy_description'] = (!empty($request->post('probation_policy_description')) ? trim($request->post('probation_policy_description')) :null);
			$recordData['v_probation_period_duration'] = (!empty($request->post('probation_policy_duration')) ? trim($request->post('probation_policy_duration')) :'');
			$recordData['e_months_weeks_days'] = (!empty($request->post('months_weeks_days')) ? trim($request->post('months_weeks_days')) :'');
			$recordData['e_record_status'] = $recordType;
			
			if($recordId > 0 ){
				$successMessage =  trans('messages.success-update',['module'=> $successResponse]);
				$errorMessages = trans('messages.error-update',['module'=> $successResponse]);
				 
				$result = $this->crudModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId]);
				$whereData['master_id'] = $recordId;
				$whereData['singleRecord'] = true;
				$recordDetail = $this->crudModel->getRecordDetails($whereData);
				
				$recordInfo = [];
				$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
				$recordInfo['recordDetail'] = (!empty($recordDetail)  ? $recordDetail : '');
				$html = view (config('constants.AJAX_VIEW_FOLDER') . 'probation-policy-master/single-probation-policy-master')->with ( $recordInfo )->render();
				 
				 
			} else{
				$insertRecord = $this->crudModel->insertTableData( $this->tableName , $recordData  );
				if($insertRecord > 0){
					$result = true;
				}
			}
			
			if($result != false){
			
				$this->ajaxResponse(1, $successMessage ,['html' => $html]);
			}else {
			
				$this->ajaxResponse(101, $errorMessages);
			}
		}
	}
	public function delete(Request $request){
		if(!empty($request->input())){
			 
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$moduleName = (!empty($request->input('delete_module_name')) ? $request->input('delete_module_name'): '' );
			
			if($moduleName == config('constants.NOTICE_PERIOD_POLICY_MODULE_SLUG')){
				$deleteModule = trans('messages.notice-period-policy-master');
			} else {
				$deleteModule = trans('messages.probation-policy-master');
			}
			return $this->removeRecord($this->tableName, $recordId, $deleteModule );
			 
		}
	}
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData = [];
	
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		
		$recordType = (! empty($request->post('record_type')) ? trim($request->post('record_type')) : '');
		
		//search record
		if (!empty($request->post('search_by'))) {
			$searchByName = trim($request->post('search_by'));
			$likeData ['searchBy'] = $searchByName;
			 
		}
		if(!empty($request->post('search_months_weeks_days'))){
			$whereData['months_weeks_days'] = trim($request->post('search_months_weeks_days'));
		}
		if(!empty($request->post('search_status'))){
			$whereData['active_status'] =  ( trim($request->input('search_status')) == config('constants.ACTIVE_STATUS') ? 1 :  0 );
		}
		if( $recordType == config('constants.NOTICE_PERIOD_POLICY')  ){
			$whereData['record_status'] = config('constants.NOTICE_PERIOD_POLICY');
		} else {
			$whereData['record_status'] = config('constants.PROBATION_POLICY');
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'probation-policy-master/probation-policy-master-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function updateStatus(Request $request){
		if(!empty($request->input())){
			$moduleName = (!empty($request->post('module_name')) ? $request->post('module_name') :'');
			if($moduleName == config('constants.NOTICE_PERIOD_POLICY_MODULE_SLUG')){
				$moduleStatus = trans('messages.notice-period-policy-master');
			} else {
				$moduleStatus = trans('messages.probation-policy-master');
			}
			return $this->updateStatusMaster($request,$this->tableName,$moduleStatus);
	
		}
	}
}
