<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Twt;
use App\DocumentFolderModel;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use App\Rules\UniqueDocumentFolderName;
class DocumentFolderMasterController extends MasterController
{
	public function __construct(){
		
		parent::__construct();
		$this->crudModel =  new DocumentFolderModel();
		$this->moduleName = trans('messages.document-folder');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->tableName = config('constants.DOCUMENT_FOLDER_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'document-folder-master/' ;
		$this->redirectUrl = config('constants.DOCUMENT_FOLDER_MASTER_URL');
		
	}
	public function index(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		$data = [];
		$data['pageTitle'] = trans('messages.document-folder');
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
		
		return view( $this->folderName . 'document-folder-master')->with($data);
	}
	public function edit(Request $request){
		
		$data = [];
		$recordId = (!empty($request->input('record_id')) ? trim($request->input('record_id')) : '' );
		if(!empty($recordId)){
			$recordId = (int)Wild_tiger::decode($recordId);
			$recordInfo = $this->crudModel->getRecordDetails(['master_id' => $recordId ,'singleRecord'=> true ]);
		
			if(!empty($recordInfo)){
				$data ['recordInfo']= $recordInfo;
			}
		}
		$html = view ($this->folderName . 'add-document-folder-master')->with ( $data )->render();
		echo $html;die;
	
	}
    public function add(Request $request){
    	
    	if(!empty($request->input())){
    		$recordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0);
    		
    		$formValidation =[];
    		$formValidation['document_folder_name'] = ['required',new UniqueDocumentFolderName($recordId)];
    		
    		$checkValidation =Validator::make($request->all(),$formValidation,
    				[
    					'document_folder_name.required' => __('messages.require-document-master-name'),		
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
    		$recordData['v_document_folder_name'] = (!empty($request->post('document_folder_name')) ? trim($request->post('document_folder_name')) :'');
    		$recordData['v_document_folder_description'] = (!empty($request->post('document_folder_description')) ? trim($request->post('document_folder_description')) :null);
    		
    		if($recordId > 0 ){
    			$successMessage =  trans('messages.success-update',['module'=> $this->moduleName]);
    			$errorMessages = trans('messages.error-update',['module'=> $this->moduleName]);
    			
    			$result = $this->crudModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId]);
    			$recordDetail = $this->crudModel->getRecordDetails( [ 'master_id' => $recordId , 'singleRecord' => true  ] );
    			$recordInfo = [];
    			$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
    			$recordInfo['recordDetail'] = $recordDetail;
    			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'document-folder-master/single-document-folder-master')->with ( $recordInfo )->render();
    			
    			
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
    			
    		return $this->removeRecord($this->tableName, $recordId, trans('messages.document-folder') );
    			
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
    
    	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'document-folder-master/document-folder-master-list' )->with ( $data )->render();
    
    	echo $html;die;
    }
	public function updateStatus(Request $request){
		if(!empty($request->input())){
			return $this->updateStatusMaster($request,$this->tableName,trans('messages.document-folder'));
	
		}
	}
	public function checkUniqueDocumentFolderName(Request $request){
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0  );
		
		$validator = Validator::make ( $request->all (), [
				'document_folder_name' => [ 'required' , new UniqueDocumentFolderName($recordId) ]  ,
		], [
				'document_folder_name.required' => __ ( 'messages.require-document-master-name' ),
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
