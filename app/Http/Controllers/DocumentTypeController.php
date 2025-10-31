<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\DocumentTypeModel;
use App\Helpers\Twt;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use App\DocumentFolderModel;
class DocumentTypeController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->crudModel =  new DocumentTypeModel();
		$this->moduleName = trans('messages.document-type');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->tableName = config('constants.DOCUMENT_TYPE_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'document-type-master/' ;
		$this->redirectUrl = config('constants.DOCUMENT_TYPE_MASTER_URL');
	
	}
	public function index(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		$data = [];
		$data['pageTitle'] = trans('messages.document-type');
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
		
		$data['documentFolderRecordDetails'] = DocumentFolderModel::orderBy('v_document_folder_name', 'ASC')->get();
		
		$data['getSelectionYesNoRecordInfo'] = getSelectionYesNoRecordInfo();
		
		$data['totalRecordCount'] = $totalRecords;
		
		return view( $this->folderName . 'document-type-master')->with($data);
	}
	public function edit(Request $request){
		
		$data = $whereData = [];
		$whereData['t_is_active'] = 1;
		$recordId = (!empty($request->input('record_id')) ? trim($request->input('record_id')) : '' );
		if(!empty($recordId)){
			$recordId = (int)Wild_tiger::decode($recordId);
			$recordInfo = $this->crudModel->getRecordDetails(['master_id' => $recordId ,'singleRecord'=> true ]);
			
			if(!empty($recordInfo)){
				$data['recordInfo']= $recordInfo;
				unset($whereData['t_is_active']);
			}
		} 
		$data['documentFolderRecordDetails'] = DocumentFolderModel::where($whereData)->orderBy('v_document_folder_name', 'ASC')->get();
		
		$html = view ($this->folderName . 'add-document-type-master')->with ( $data )->render();
		echo $html;die;
	
	}
	public function add(Request $request){
		
		if(!empty($request->input())){
			$formValidation =[];
			$formValidation['document_folder'] = ['required'];
			$formValidation['document_type'] = ['required'];
			
			$checkValidation =Validator::make($request->all(),$formValidation,
					[
							'document_folder.required' => __('messages.please-select-document-folder'),
							'document_type.required' => __('messages.please-enter-document-type'),
							
					]
			);
			if($checkValidation->fails() != false){
				$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => $this->moduleName ] ) ) );
			}
			$recordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0);
		
			$successMessage =  trans('messages.success-create',['module'=> $this->moduleName]);
			$errorMessages = trans('messages.error-create',['module'=> $this->moduleName]);
			$result = false;
			$html= null;
			$recordData = [];
			$recordData['i_document_folder_id'] = (!empty($request->post('document_folder')) ? (int)Wild_tiger::decode($request->post('document_folder')) : 0);
			$recordData['v_document_type'] = (!empty($request->post('document_type')) ? trim($request->post('document_type')) :'');
			$recordData['v_document_description'] = (!empty($request->post('document_description')) ? trim($request->post('document_description')) :null);
			$recordData['e_multiple_allowed_employee'] = (!empty($request->post('is_multiple_allowed')) ? trim($request->post('is_multiple_allowed')) :'');
			$recordData['e_visible_to_employee'] = (!empty($request->post('is_visible_to_employee')) ? trim($request->post('is_visible_to_employee')) :'');
			$recordData['e_modifiable_employee'] = (!empty($request->post('is_modifiable')) ? trim($request->post('is_modifiable')) :'');
			
			if($recordId > 0 ){
				$successMessage =  trans('messages.success-update',['module'=> $this->moduleName]);
				$errorMessages = trans('messages.error-update',['module'=> $this->moduleName]);
				 
				$result = $this->crudModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId]);
				$recordDetail = $this->crudModel->getRecordDetails( ['master_id' => $recordId , 'singleRecord' => true  ] );
				$recordInfo = [];
				$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
				$recordInfo['recordDetail'] = $recordDetail;
				$html = view (config('constants.AJAX_VIEW_FOLDER') . 'document-type-master/single-document-type-master')->with ( $recordInfo )->render();
				 
				 
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
	public function filter(Request $request){
		//variable defined
		$whereData = $likeData = [];
	
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
	
		//search record
		if (!empty($request->post('search_by'))) {
			$searchByName = trim($request->post('search_by'));
			$likeData ['searchBy'] = $searchByName;
			 
		}
		if(!empty($request->post('search_document_folder'))){
			$whereData['document_folder_id'] = (int)Wild_tiger::decode($request->post('search_document_folder'));
		}
		if(!empty($request->post('search_is_multiple_allowed'))){
			$whereData['multiple_allowed_employee'] =  ( trim($request->input('search_is_multiple_allowed')) == config('constants.SELECTION_YES') ? config('constants.SELECTION_YES') :  config('constants.SELECTION_NO') );
		}
		if(!empty($request->post('search_is_visible_to_employee'))){
			$whereData['visible_to_employee'] =  ( trim($request->input('search_is_visible_to_employee')) == config('constants.SELECTION_YES') ? config('constants.SELECTION_YES') :  config('constants.SELECTION_NO') );
		}
		if(!empty($request->post('search_is_modifiable'))){
			$whereData['modifiable_employee'] =  ( trim($request->input('search_is_modifiable')) == config('constants.SELECTION_YES') ? config('constants.SELECTION_YES') :  config('constants.SELECTION_NO') );
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'document-type-master/document-type-master-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	public function delete(Request $request){
		if(!empty($request->input())){
			 
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			 
			return $this->removeRecord($this->tableName, $recordId, trans('messages.document-type') );
			 
		}
	}
	public function updateStatus(Request $request){
		if(!empty($request->input())){
			return $this->updateStatusMaster($request,$this->tableName,trans('messages.document-type'));
	
		}
	}
}
