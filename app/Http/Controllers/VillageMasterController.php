<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\VillageMasterModel;
use App\CityMasterModel;
use App\StateMasterModel;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use App\Rules\UniqueVillageName;
class VillageMasterController extends MasterController {
	public function __construct(){
	
		parent::__construct();
		$this->crudModel =  new VillageMasterModel();
		$this->moduleName = trans('messages.village');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->tableName = config('constants.VILLAGE_MASTER_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'village-master/' ;
		$this->redirectUrl = config('constants.VILLAGE_MASTER_URL');
	}
	
	public function index(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		$data = [];
		$data['pageTitle'] = trans('messages.village');
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
		$data['stateRecordDetails'] = StateMasterModel::orderBy('v_state_name', 'ASC')->get();
		$data['cityRecordDetails'] = CityMasterModel::orderBy('v_city_name', 'ASC')->get();
	 	return view( $this->folderName . 'village-master')->with($data);
	}
	
	public function edit(Request $request){
		$data = $whereData = $stateWhere = $cityWhere =  [];
		$recordId = (!empty($request->input('village_record_id')) ? $request->input('village_record_id') : 0 );
		$stateWhere['t_is_active'] = 1;
		$cityWhere['t_is_active'] = 1;
		if(!empty($recordId)){
			$recordId = (int)Wild_tiger::decode($recordId);
			$whereData['master_id'] = $recordId;
			$whereData['singleRecord'] = true;
			$recordInfo = $this->crudModel->getRecordDetails($whereData);
			if(!empty($recordInfo)){
				$data ['recordInfo']= $recordInfo;
				unset($stateWhere['t_is_active']);
				unset($cityWhere['t_is_active']);
			}
		}
		$data['stateRecordDetails'] = StateMasterModel::where($stateWhere)->orderBy('v_state_name', 'ASC')->get();
		$data['cityRecordDetails'] = CityMasterModel::where($cityWhere)->orderBy('v_city_name', 'ASC')->get();
		$html = view ($this->folderName . 'add-village-master')->with ( $data )->render();
		echo $html;die;
	
	}
	public function add(Request $request){
		if(!empty($request->input())){
			$recordId = (!empty($request->post('village_record_id')) ? (int)Wild_tiger::decode($request->post('village_record_id')) : 0);
			$cityId = (!empty($request->post('city')) ? (int)Wild_tiger::decode($request->post('city')) : 0);
			$stateId = (!empty($request->post('state')) ? (int)Wild_tiger::decode($request->post('state')) : 0);
				
			$formValidation =[];
			$formValidation['village_name'] = ['required' , new UniqueVillageName($recordId, $cityId ,$stateId)];
			$formValidation['city'] = ['required'];
			$formValidation['state'] = ['required'];
			$checkValidation =Validator::make($request->all(),$formValidation,
					[
						'village_name.required' => __('messages.required-village-name'),
						'city.required' => __('messages.required-city'),
						'state.required' => __('messages.required-state-name'),
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
			$recordData['v_village_name'] = (!empty($request->post('village_name')) ? trim($request->post('village_name')) :'');
			$recordData['i_city_id'] = $cityId;
			if($recordId > 0 ){
				$successMessage =  trans('messages.success-update',['module'=> $this->moduleName]);
				$errorMessages = trans('messages.error-update',['module'=> $this->moduleName]);
					
				$result = $this->crudModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId]);
				$recordDetail = $this->crudModel->getRecordDetails( [ 'master_id' => $recordId , 'singleRecord' => true  ] );
				$recordInfo = [];
				$recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1 );
				$recordInfo['recordDetail'] = $recordDetail;
				$html = view (config('constants.AJAX_VIEW_FOLDER') . 'village-master/single-village-master')->with ( $recordInfo )->render();
			} else{
				$insertRecord = $this->crudModel->insertTableData( $this->tableName , $recordData);
				if($insertRecord > 0){
					$villageCrudModule = (!empty($request->input('village_crud_module')) ? trim($request->input('village_crud_module')) : config('constants.SELECTION_YES'));
					if( $villageCrudModule ==  config('constants.SELECTION_NO') ){
						$allVillageDetails = VillageMasterModel::with(['cityMaster.stateMaster.countryMaster'])->where('t_is_active',1)->orderBy('v_village_name', 'ASC')->get();
						$html = '<option value="">'.trans ( 'messages.select').'</option>';
						if(!empty($allVillageDetails)){
							foreach($allVillageDetails as $allVillageDetail){
								$encodeVillageId = Wild_tiger::encode($allVillageDetail->i_id);
								$html .= '<option value="'.$encodeVillageId.'" data-cur-village-city-id="'.$allVillageDetail->i_city_id.'" data-cur-village-country-id="'.(isset($allVillageDetail->cityMaster->stateMaster->i_country_id) ? $allVillageDetail->cityMaster->stateMaster->i_country_id : '').'" data-cur-village-state-id="'. ( isset($allVillageDetail->cityMaster->i_state_id) ? $allVillageDetail->cityMaster->i_state_id : '' ).'" data-village-id="'.$allVillageDetail->i_id.'">'.$allVillageDetail->v_village_name.'</option>';
							}
						}
					}  
					Wild_tiger::setFlashMessage ('success', $successMessage  );
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
			return $this->removeRecord($this->tableName, $recordId, trans('messages.village') );
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
		if(!empty($request->post('search_state'))){
			$whereData['state_id'] = (int)Wild_tiger::decode($request->post('search_state'));
		} 
		if(!empty($request->post('search_city'))){
			$whereData['city_id'] = (int)Wild_tiger::decode($request->post('search_city'));
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
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'village-master/village-master-list' )->with ( $data )->render();
		echo $html;die;
	}
	public function updateStatus(Request $request){
		if(!empty($request->input())){
			return $this->updateStatusMaster($request,$this->tableName,trans('messages.village'));
		}
	}
	public function checkUniqueVillageName(Request $request){
		$recordId = (!empty($request->input('village_record_id')) ? (int)Wild_tiger::decode($request->input('village_record_id')) : 0  );
		$cityId = (!empty($request->input('city')) ? (int)Wild_tiger::decode($request->input('city')) : 0  );
		$stateId = (!empty($request->input('state')) ? (int)Wild_tiger::decode($request->input('state')) : 0  );
	
		$validator = Validator::make ( $request->all (), [
				'village_name' => ['required' , new UniqueVillageName($recordId , $cityId ,$stateId)],
		], [
				'village_name.required' => __ ( 'messages.required-village-name' ),
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
