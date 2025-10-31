<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\SalaryGroupModel;
use App\SalaryComponentsModel;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use DB;
use App\Rules\UniqueSalaryGroupName;
class SalaryGroupController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->crudModel =  new SalaryGroupModel();
		$this->moduleName = trans('messages.salary-groups');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->tableName = config('constants.SALARY_GROUP_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'salary-groups/' ;
		$this->redirectUrl = config('constants.SALARY_GROUPS_MASTER_URL');
	
	}
	public function index(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		$data = [];
		$data['pageTitle'] = trans('messages.salary-groups');
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
		$whereData['limit'] = $this->perPageRecord;
	
		$data['recordDetails'] = $this->crudModel->getRecordDetails($whereData);
		
		$data['pagination'] = $paginationData;
			
		$data['page_no'] = $page;
	
		$data['perPageRecord'] = $this->perPageRecord;
	
		$data['totalRecordCount'] = $totalRecords; 
	
	
		return view( $this->folderName . 'salary-groups')->with($data);
	}
	public function edit(Request $request){
		$data = $whereData = [];
		$whereData['t_is_active'] = 1;
		
		$recordId = (!empty($request->input('salary_group_record_id')) ? $request->input('salary_group_record_id') : '' );
		if(!empty($recordId)){
			$recordId = (int)Wild_tiger::decode($recordId);
			$recordInfo = $this->crudModel->getRecordDetails(['master_id' =>$recordId,'singleRecord' =>true ]);
		
			if(!empty($recordInfo)){
				$data['recordInfo']= $recordInfo;
				unset($whereData['t_is_active']);
			}
		}
		$data['salaryComponentsDetails'] = SalaryComponentsModel::where($whereData)->orderBy('i_sequence', 'ASC')->get();
		
		$html = view ($this->folderName . 'add-salary-groups')->with ( $data )->render();
		echo $html;die;
	
	}
	public function add(Request $request){
		
		if(!empty($request->input())){
			$salaryComponentsDeductionIds = (!empty($request->post('salary_components_deduction')) ? ($request->post('salary_components_deduction')) :null);
			$salaryComponentsEarningsIds = (!empty($request->post('salary_components_earning')) ? ($request->post('salary_components_earning')) :null);
			$recordId = (!empty($request->post('salary_group_record_id')) ? (int)Wild_tiger::decode($request->post('salary_group_record_id')) : 0);
				
			$formValidation =[];
			$formValidation['group_name'] = ['required' , new UniqueSalaryGroupName($recordId) ];
			$checkValidation = Validator::make($request->all(),$formValidation,
					[
							'group_name.required' => __('messages.require-group-name'),
								
					]
			);
			if($checkValidation->fails() != false){
				$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => $this->moduleName ] ) ) );
			}
			$successMessage =  trans('messages.success-create',['module'=> $this->moduleName]);
			$errorMessages = trans('messages.error-create',['module'=> $this->moduleName]);
			$result = false;
			$html= null;
			
			$salaryComponentsDeductionRecordId = $salaryComponentsEarningsRecordId = [];
			
			if(!empty($salaryComponentsDeductionIds)){
				foreach ($salaryComponentsDeductionIds as $salaryComponentsDeductionId){
					$salaryComponentsDeductionRecordId[] = (int)Wild_tiger::decode($salaryComponentsDeductionId);
				}
			}
			if(!empty($salaryComponentsEarningsIds)){
				foreach ($salaryComponentsEarningsIds as $salaryComponentsEarningsId){
					$salaryComponentsEarningsRecordId[] = (int)Wild_tiger::decode($salaryComponentsEarningsId);
				}
			}
			
			DB::beginTransaction();
			try{ 
				$recordData = [];
				$recordData['v_group_name'] = (!empty($request->post('group_name')) ? trim($request->post('group_name')) :'');
				$recordData['v_group_description'] = (!empty($request->post('group_description')) ? trim($request->post('group_description')) :null);
				$recordData['v_salary_components_earnings_ids'] = (!empty($salaryComponentsEarningsRecordId) ? implode(',', $salaryComponentsEarningsRecordId) : null);
				$recordData['v_salary_components_deduction_ids'] = (!empty($salaryComponentsDeductionRecordId) ? implode(',', $salaryComponentsDeductionRecordId) : null);
				$previewsComponentId = [];
				if($recordId > 0 ){
					$successMessage =  trans('messages.success-update',['module'=> $this->moduleName]);
					$errorMessages = trans('messages.error-update',['module'=> $this->moduleName]);
						
					$result = $this->crudModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId]);
					$insertRecord = $recordId;
					
					$getSalaryGruopRecordDetail = $this->crudModel->getRecordDetails( [ 'master_id' => $recordId , 'singleRecord' => true  ] );
						
					if(!empty($getSalaryGruopRecordDetail->salaryGroupDetails)){
						foreach ($getSalaryGruopRecordDetail->salaryGroupDetails as $getSalaryGruopRecordInfo){
							$previewsComponentId[] = $getSalaryGruopRecordInfo->i_salary_components_id;
							if((!in_array($getSalaryGruopRecordInfo->i_salary_components_id, $salaryComponentsEarningsRecordId) && $getSalaryGruopRecordInfo->e_type ==  config('constants.SALARY_COMPONENT_TYPE_EARNING')) || (!in_array($getSalaryGruopRecordInfo->i_salary_components_id, $salaryComponentsDeductionRecordId) && $getSalaryGruopRecordInfo->e_type ==  config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'))){
								$deleteRecordData = [];
								$deleteRecordData ['t_is_active'] = 0;
								$deleteRecordData ['t_is_deleted'] = 1;
								
								$delteRecord = $this->crudModel->deleteTableData( config('constants.SALARY_GROUP_COMPONENTS_DETAILS_TABLE') , $deleteRecordData , [ 'i_salary_components_id' => $getSalaryGruopRecordInfo->i_salary_components_id , 'i_salary_group_id' => $recordId , 't_is_deleted' => 0   ] );
								
							}
						}
					}
					$recordInfo = [];
					$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
					$recordInfo['recordDetail'] = $getSalaryGruopRecordDetail;
					$html = view (config('constants.AJAX_VIEW_FOLDER') . 'salary-groups/single-salary-groups')->with ( $recordInfo )->render();

				} else{
					$insertRecord = $this->crudModel->insertTableData( $this->tableName , $recordData);
					
					if($insertRecord > 0){
						$result = true;
					}
				}
				if(!empty($salaryComponentsEarningsRecordId)){
					foreach ($salaryComponentsEarningsRecordId as $salaryComponentsEarningsRecord){
						if(!in_array($salaryComponentsEarningsRecord, $previewsComponentId)){
							$rowData = [];
							$rowData['i_salary_group_id'] = $insertRecord;
							$rowData['i_salary_components_id'] = $salaryComponentsEarningsRecord;
							$rowData['e_type'] = config('constants.SALARY_COMPONENT_TYPE_EARNING');
							$salaryComponentsEarnings = $this->crudModel->insertTableData(config('constants.SALARY_GROUP_COMPONENTS_DETAILS_TABLE') , $rowData);
							
						}
							
					}
				}
				if(!empty($salaryComponentsDeductionRecordId)){
					foreach ($salaryComponentsDeductionRecordId as $salaryComponentsDeductionRecord){
						if(!in_array($salaryComponentsDeductionRecord, $previewsComponentId)){
							$rowRecordData = [];
							$rowRecordData['i_salary_group_id'] = $insertRecord;
							$rowRecordData['i_salary_components_id'] = $salaryComponentsDeductionRecord;
							$rowRecordData['e_type'] = config('constants.SALARY_COMPONENT_TYPE_DEDUCTION');
							$salaryComponentsDeduction = $this->crudModel->insertTableData(config('constants.SALARY_GROUP_COMPONENTS_DETAILS_TABLE') , $rowRecordData);
						}	
					}
				}
				
			$result = true;
			}catch(\Exception $e){
				DB::rollback();
				$result = false;
			}
			if($result != false){
				DB::commit();
				$this->ajaxResponse(1, $successMessage ,['html' => $html]);
			}else {
				DB::rollback();
				$this->ajaxResponse(101, $errorMessages);
			} 
		}
	}
	public function delete(Request $request){
		if(!empty($request->input())){
	
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$componentDetailData['t_is_active'] = 0;
			$componentDetailData['t_is_deleted'] = 1;
				
			$this->crudModel->deleteTableData(  config('constants.SALARY_GROUP_COMPONENTS_DETAILS_TABLE') ,  $componentDetailData , [ 'i_salary_group_id' => $recordId ] );
				
			return $this->removeRecord($this->tableName, $recordId, trans('messages.salary-groups') );
	
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
		if(!empty($request->post('search_status'))){
			$whereData['active_status'] =  ( trim($request->input('search_status')) == config('constants.ACTIVE_STATUS') ? 1 :  0 );
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'salary-groups/salary-groups-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function updateStatus(Request $request){
		if(!empty($request->input())){
			return $this->updateStatusMaster($request,$this->tableName,trans('messages.salary-groups'));
	
		}
	}
	public function checkUniqueSalaryGroupName(Request $request){
	
		$recordId = (!empty($request->input('salary_group_record_id')) ? (int)Wild_tiger::decode($request->input('salary_group_record_id')) : 0  );
	
		$validator = Validator::make ( $request->all (), [
				'group_name' => ['required' , new UniqueSalaryGroupName($recordId) ],
		], [
				'group_name.required' => __ ( 'messages.require-group-name' ),
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
