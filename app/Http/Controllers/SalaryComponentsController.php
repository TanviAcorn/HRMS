<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\SalaryComponentsModel;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use App\Rules\UniqueSalaryGroupName;
use App\Rules\UniqueSalaryComponentsName;
class SalaryComponentsController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->crudModel =  new SalaryComponentsModel();
		$this->moduleName = trans('messages.salary-components');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->tableName = config('constants.SALARY_COMPONENTS_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'salary-components/' ;
		$this->redirectUrl = config('constants.SALARY_COMPONENTS_MASTER_URL');
	
	}
	public function index(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		$data = [];
		$data['pageTitle'] = trans('messages.salary-components');
		$page = $this->defaultPage;
	
		#store pagination data array
		$whereData = $paginationData = [];
	
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
		
		$data['getSalaryComponentsTypeInfo'] = getSalaryComponentsTypeInfo();
		
		
		$startTime = new \DateTime('06:10:00'); 
		$endTime = new \DateTime('06:15:00'); 
		$difference = $startTime->diff($endTime);
		$hours = (!empty($difference->h) ? rtrim($difference->h.' ' ."Hours") : '' );
		$minute = (!empty($difference->i) ? rtrim($difference->i.' ' ."Minute") : '' );
		
		if( (!empty($hours)) && (!empty($minute))  ){
			$totalHourseAndMinute =  $hours . ' ' . $minute;
		} else {
			$totalHourseAndMinute = (!empty($hours) ? $hours : '' ) . (!empty($minute) ? $minute : ''); 
		}
		
		
		return view( $this->folderName . 'salary-components')->with($data);
	}
	public function edit(Request $request){
		$data = $whereData = [];
		$recordId = (!empty($request->input('salary_components_record_id')) ? $request->input('salary_components_record_id') : '' );
		if(!empty($recordId)){
			$recordId = (int)Wild_tiger::decode($recordId);
			$whereData['master_id'] = $recordId;
			$whereData['singleRecord'] = true;
			$recordInfo = $this->crudModel->getRecordDetails($whereData);
		
			if(!empty($recordInfo)){
				$data ['recordInfo']= $recordInfo;
			}
		}
		$html = view ($this->folderName . 'add-salary-components')->with ( $data )->render();
		echo $html;die;
	
	}
	public function add(Request $request){
		if(!empty($request->input())){
			$recordId = (!empty($request->post('salary_components_record_id')) ? (int)Wild_tiger::decode($request->post('salary_components_record_id')) : 0);
			$salaryComponentsType = (!empty($request->post('salary_components_type')) ? trim($request->post('salary_components_type')) :'');
			$formValidation =[];
			$formValidation['component_name'] = ['required' , new UniqueSalaryComponentsName($recordId, $salaryComponentsType)];
			$formValidation['salary_components_type'] = ['required'];
			
			$checkValidation = Validator::make($request->all(),$formValidation,
					[
							'component_name.required' => __('messages.require-component-name'),
							'salary_components_type.required' => __('messages.require-type'),
								
					]
			);
			if($checkValidation->fails() != false){
				$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => $this->moduleName ] ) ) );
			}
	
			$successMessage =  trans('messages.success-create',['module'=> $this->moduleName]);
			$errorMessages = trans('messages.error-create',['module'=> $this->moduleName]);
			$result = false;
			$html= null;
			$recordData = [];
			$recordData['v_component_name'] = (!empty($request->post('component_name')) ? trim($request->post('component_name')) :'');
			$recordData['v_component_description'] = (!empty($request->post('component_description')) ? trim($request->post('component_description')) :null);
			$recordData['e_salary_components_type'] = $salaryComponentsType;
			$recordData['e_consider_for_pf_calculation'] = null;
			
			if( $recordData['e_salary_components_type'] == config('constants.SALARY_COMPONENT_TYPE_EARNING')){
				$recordData['e_consider_for_pf_calculation'] = (!empty($request->post('consider_for_pf_calculation')) ? trim($request->post('consider_for_pf_calculation')) : null );;
			}
			
			if($recordId > 0 ){
				$successMessage =  trans('messages.success-update',['module'=> $this->moduleName]);
				$errorMessages = trans('messages.error-update',['module'=> $this->moduleName]);
					
				$result = $this->crudModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId]);
				$recordDetail = $this->crudModel->getRecordDetails( [ 'master_id' => $recordId , 'singleRecord' => true  ] );
				$recordInfo = [];
				$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
				$recordInfo['recordDetail'] = $recordDetail;
				$html = view (config('constants.AJAX_VIEW_FOLDER') . 'salary-components/single-salary-components')->with ( $recordInfo )->render();
					
					
			} else{
				$insertRecord = $this->crudModel->insertTableData( $this->tableName , $recordData);
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
	
			return $this->removeRecord($this->tableName, $recordId, trans('messages.salary-components') );
	
		}
	}
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData = [];
	
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
	
		//search record
		if (!empty($request->post('search_by'))) {
			$searchByName = trim($request->post('search_by'));
			$likeData ['searchBy'] = $searchByName;
	
		}
		if(!empty($request->post('search_salary_components_type'))){
			$whereData['salary_components_type'] =  ( trim($request->input('search_salary_components_type')) == config('constants.SALARY_COMPONENT_TYPE_EARNING') ? config('constants.SALARY_COMPONENT_TYPE_EARNING') :  config('constants.SALARY_COMPONENT_TYPE_DEDUCTION') );
		}
		if(!empty($request->post('search_status'))){
			$whereData['active_status'] =  ( trim($request->input('search_status')) == config('constants.ACTIVE_STATUS') ? 1 :  0 );
		}
		
		if(!empty($request->post('search_consider_under_pf'))){
			$whereData['consider_under_pf'] =  ( trim($request->input('search_consider_under_pf')) );
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'salary-components/salary-components-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function updateStatus(Request $request){
		if(!empty($request->input())){
			return $this->updateStatusMaster($request,$this->tableName,trans('messages.salary-components'));
	
		}
	}
	public function checkUniqueSalaryComponentsName(Request $request){
		$recordId = (!empty($request->input('salary_components_record_id')) ? (int)Wild_tiger::decode($request->input('salary_components_record_id')) : 0  );
		$salaryComponentsType = (!empty($request->input('salary_components_type')) ? trim($request->input('salary_components_type')) : '' );
		
		$validator = Validator::make ( $request->all (), [
				'component_name' => ['required' , new UniqueSalaryComponentsName($recordId, $salaryComponentsType)],
		], [
				'component_name.required' => __ ( 'messages.require-component-name' ),
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
}
