<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\WeeklyOffMasterModel;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use App\Rules\UniqueWeeklyOffName;
use DB;

class WeeklyOffMasterController extends MasterController
{
	
	public  function __construct(){
		parent:: __construct();
		$this->crudModel = new WeeklyOffMasterModel();
		$this->tableName = config('constants.WEEKLY_OFF_MASTER_TABLE');
		$this->moduleName = trans('messages.weekly-off-master');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'weekly-off-master/' ;
		$this->redirectUrl = config('constants.WEEKLY_OFF_MASTER_URL');
	}
	public function index(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		$data = [];
		$data['pageTitle'] = trans('messages.weekly-off-master');
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
		
		return view( $this->folderName . 'weekly-off-master')->with($data);
	}
	public function edit(Request $request){
		$data = $whereData = [];
		$recordId = (!empty($request->input('record_id')) ? $request->input('record_id') : '' );
		if(!empty($recordId)){
			$recordId = (int)Wild_tiger::decode($recordId);
			$whereData['master_id'] = $recordId;
			$whereData['singleRecord'] = true;
			$recordInfo = $this->crudModel->getRecordDetails($whereData);
		
			if(!empty($recordInfo)){
				$data ['recordInfo']= $recordInfo;
			}
		}
		$data['weekDayDetails'] = weekDayDetails();
		$html = view ($this->folderName . 'add-weekly-off-master')->with ( $data )->render();
		echo $html;die;
	}
	public function add(Request $request){
		if(!empty($request->input())){
			
			//echo "<pre>";print_r($request->all());die;
			
			$recordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0);
				
			$formValidation =[];
			$formValidation['weekly_off_name'] = ['required', new UniqueWeeklyOffName($recordId)];
				
			$checkValidation = Validator::make ($request->all(),$formValidation,[
					'weekly_off_name.required' => __( 'messages.require-weekly-off-name'),
					
			]);
			if($checkValidation->fails() != false){
				$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans ( 'messages.error-create', [ 'module' => $this->moduleName ] ) ) );
			}
			
			$successMessage =  trans('messages.success-create',['module'=> $this->moduleName]);
			$errorMessages = trans('messages.error-create',['module'=> $this->moduleName]);
			$result = false;
			$html= null;
			
			$recordData = [];
			$recordData['v_weekly_off_name'] = (!empty($request->input('weekly_off_name')) ? trim($request->input('weekly_off_name')) :'');
			$recordData['v_description'] = (!empty($request->input('week_off_description')) ? trim($request->input('week_off_description')) : null);
			
			$weekDayDetails = weekDayDetails();
			
			$historyDetails = [];
			
			$alternateWeekOffFound = false;
			$allWeekOffFound = false;
			if(!empty($weekDayDetails)){
				foreach($weekDayDetails as $weekKey =>  $weekDayDetail){
					$historyDetails['v_'.$weekKey.'_all_off'] =  config('constants.SELECTION_NO');
					$historyDetails['v_'.$weekKey.'_alternate_off'] =  config('constants.SELECTION_NO');
					$recordData['t_is_' . $weekKey ] = ((!empty($request->input($weekKey))) && (($request->input($weekKey) == config('constants.SELECTION_YES')) ? 1 : 0 ));
					if( !empty($request->post('alternate_off_'.$weekKey)) ){
						if( $recordData['t_is_' . $weekKey ] == 1 ){
							if( $request->post('alternate_off_'.$weekKey) == config('constants.ALTERNATE_STATUS') ){
								$alternateWeekOffFound = true;
								$historyDetails['v_'.$weekKey.'_alternate_off'] =  config('constants.SELECTION_YES');
							}
							if( $request->post('alternate_off_'.$weekKey) == config('constants.ALL_STATUS') ){
								$allWeekOffFound = true;
								$historyDetails['v_'.$weekKey.'_all_off'] =  config('constants.SELECTION_YES');
							}	
						}
					}
				}
			}
			
			if( $alternateWeekOffFound != false &&  $allWeekOffFound != true ){
				$this->ajaxResponse(101, trans('messages.required-one-all-off-selection-for-alternate'));
			}
			
			
			$result = false;
			DB::beginTransaction();
			
			try{
				if($recordId > 0 ){
					
					$successMessage =  trans('messages.success-update',['module'=> $this->moduleName]);
					$errorMessages = trans('messages.error-update',['module'=> $this->moduleName]);
						
					$this->crudModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId]);
					
					$this->crudModel->updateTableData( config('constants.WEEKLY_OFF_INFO_TABLE') , $historyDetails , ['i_weekly_off_master_id' => $recordId]);
					
					$recordDetail = $this->crudModel->getRecordDetails( [ 'master_id' => $recordId , 'singleRecord' => true  ] );
					$recordInfo = [];
					$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
					$recordInfo['recordDetail'] = $recordDetail;
					$html = view (config('constants.AJAX_VIEW_FOLDER') . 'weekly-off-master/single-weekly-off-master')->with ( $recordInfo )->render();
						
				} else {
					
					$insertWeekOffMaster = $this->crudModel->insertTableData($this->tableName , $recordData);
					$historyDetails['i_weekly_off_master_id'] = $insertWeekOffMaster;
					$this->crudModel->insertTableData( config('constants.WEEKLY_OFF_INFO_TABLE') , $historyDetails  );
					
				}
				$result = true;
			}catch(\Exception $e){
				DB::rollback();
				
			}
			
			
			
			if($result != false){
				DB::commit();	
				$this->ajaxResponse(1, $successMessage , ['html' => $html]);
			}else {
				DB::rollback();
				$this->ajaxResponse(101, $errorMessages);
			}
		}
	}
	public function delete(Request $request){
		if(!empty($request->input())){
	
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
	
			return $this->removeRecord($this->tableName, $recordId, trans('messages.weekly-off-master') );
	
		}
	}
	public function updateStatus(Request $request){
		if(!empty($request->input())){
			return $this->updateStatusMaster($request,$this->tableName,trans('messages.weekly-off-master'));
	
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
	
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'weekly-off-master/weekly-off-master-list' )->with ( $data )->render();
	
		echo $html;die;
	}
	
	public function checkUniqueWeeklyOffName(Request $request){
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0  );
		
		$validator = Validator::make ( $request->all (), [
				'weekly_off_name' => [ 'required' , new UniqueWeeklyOffName($recordId) ],
		], [
				'weekly_off_name.required' => __ ( 'messages.require-weekly-off-name' ),
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
