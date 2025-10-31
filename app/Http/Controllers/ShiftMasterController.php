<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\ShiftMasterModel;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\Response;
use App\Rules\UniqueShiftName;
class ShiftMasterController extends MasterController
{
    
	public  function __construct(){
		parent:: __construct();
		$this->crudModel = new ShiftMasterModel();
		$this->tableName = config('constants.SHIFT_MASTER_TABLE');
		$this->moduleName = trans('messages.shift');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'shift-master/' ;
		$this->redirectUrl = config('constants.SHIFT_MASTER_URL');
	}
	public function index(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		$data = [];
		$data['pageTitle'] = trans('messages.shift-master');
		$page = $this->defaultPage;
		
		$data['typeOfShiftInfo'] = typeOfShiftInfo();
		return view( $this->folderName . 'shift-master')->with($data);
	}
	
	public function create(){
		$data = [];
		$data ['pageTitle'] = trans('messages.add-shift');
		return view ( $this->folderName . 'add-shift-master' )->with ( $data );
	}
	
	public function add(Request $request){
		
		if(!empty($request->input())){
			$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0 );
			$shiftType = (!empty($request->input('shift_type')) ? trim($request->input('shift_type')) : config('constants.MORNING_SHIFT'));	
			
			$formValidation = [];
			$formValidation['shift_name'] = ['required',new UniqueShiftName($recordId ,$shiftType)];
			$formValidation['shift_code'] = ['required'];
			$formValidation['shift_type'] = ['required'];
			
			$validator = Validator::make ($request->all(),$formValidation,[
					'shift_name.required' => __( 'messages.require-shift-name'), 
					'shift_code.required' => __( 'messages.require-shift-code'),
					'shift_type.required' => __( 'messages.require-shift-type'),
					
			]);
			if( $validator->fails() ){
				return redirect()->back()->withErrors ( $validator )->withInput ();
			}
			
			$result = false;
			$successMessage =  trans('messages.success-create',['module'=> $this->moduleName ]);
			$errorMessages = trans('messages.error-create',['module'=> $this->moduleName ]);
			
			
			DB::beginTransaction();
			try{
				$recordData = $shiftTimingRecordData = [];
				$recordData['v_shift_name'] = (!empty($request->input('shift_name')) ? trim($request->input('shift_name')) :'');
				$recordData['v_shift_code'] = (!empty($request->input('shift_code')) ? trim($request->input('shift_code')) :'');
				$recordData['e_shift_type'] = $shiftType;
				$recordData['v_description'] = (!empty($request->input('description')) ? trim($request->input('description')) : null );
				$recordData['e_different_week_day_time'] = (!empty($request->input('shift_start_time_checkbox')) ? trim($request->input('shift_start_time_checkbox')) : config('constants.SELECTION_NO'));
				$recordData['e_different_week_day_break_time'] = (!empty($request->input('shift_break_time_checkbox')) ? trim($request->input('shift_break_time_checkbox')) : config('constants.SELECTION_NO'));
				
				$shiftTimingRecordData['v_monday_start_time'] = (!empty($request->input('monday_shift_start_time')) ? dbTime($request->input('monday_shift_start_time')) :'');
				$shiftTimingRecordData['v_monday_end_time'] = (!empty($request->input('monday_shift_end_time')) ? dbTime($request->input('monday_shift_end_time')) :'');
				$shiftTimingRecordData['v_tuesday_start_time'] = (!empty($request->input('tuesday_shift_start_time')) ? dbTime($request->input('tuesday_shift_start_time')) :'');
				$shiftTimingRecordData['v_tuesday_end_time'] = (!empty($request->input('tuesday_shift_end_time')) ? dbTime($request->input('tuesday_shift_end_time')) :'');
				$shiftTimingRecordData['v_wednesday_start_time'] = (!empty($request->input('wednesday_shift_start_time')) ? dbTime($request->input('wednesday_shift_start_time')) :'');
				$shiftTimingRecordData['v_wednesday_end_time'] = (!empty($request->input('wednesday_shift_end_time')) ? dbTime($request->input('wednesday_shift_end_time')) :'');
				$shiftTimingRecordData['v_thursday_start_time'] = (!empty($request->input('thursday_shift_start_time')) ? dbTime($request->input('thursday_shift_start_time')) :'');
				$shiftTimingRecordData['v_thursday_end_time'] = (!empty($request->input('thursday_shift_end_time')) ? dbTime($request->input('thursday_shift_end_time')) :'');
				$shiftTimingRecordData['v_friday_start_time'] = (!empty($request->input('friday_shift_start_time')) ? dbTime($request->input('friday_shift_start_time')) :'');
				$shiftTimingRecordData['v_friday_end_time'] = (!empty($request->input('friday_shift_end_time')) ? dbTime($request->input('friday_shift_end_time')) :'');
				$shiftTimingRecordData['v_saturday_start_time'] = (!empty($request->input('saturday_shift_start_time')) ? dbTime($request->input('saturday_shift_start_time')) :'');
				$shiftTimingRecordData['v_saturday_end_time'] = (!empty($request->input('saturday_shift_end_time')) ? dbTime($request->input('saturday_shift_end_time')) :'');
				$shiftTimingRecordData['v_sunday_start_time'] = (!empty($request->input('sunday_shift_start_time')) ? dbTime($request->input('sunday_shift_start_time')) :'');
				$shiftTimingRecordData['v_sunday_end_time'] = (!empty($request->input('sunday_shift_end_time')) ? dbTime($request->input('sunday_shift_end_time')) :'');
				$shiftTimingRecordData['v_monday_break_start_time'] = (!empty($request->input('monday_shift_break_time')) ? dbTime($request->input('monday_shift_break_time')) : null );
				$shiftTimingRecordData['v_monday_break_end_time'] = (!empty($request->input('monday_shift_break_end_time')) ? dbTime($request->input('monday_shift_break_end_time')) : null );
				$shiftTimingRecordData['v_tuesday_break_start_time'] = (!empty($request->input('tuesday_shift_break_time')) ? dbTime($request->input('tuesday_shift_break_time')) : null );
				$shiftTimingRecordData['v_tuesday_break_end_time'] = (!empty($request->input('tuesday_shift_break_end_time')) ? dbTime($request->input('tuesday_shift_break_end_time')) : null );
				$shiftTimingRecordData['v_wednesday_break_start_time'] = (!empty($request->input('wednesday_shift_break_time')) ? dbTime($request->input('wednesday_shift_break_time')) : null );
				$shiftTimingRecordData['v_wednesday_break_end_time'] = (!empty($request->input('wednesday_shift_break_end_time')) ? dbTime($request->input('wednesday_shift_break_end_time')) : null );
				$shiftTimingRecordData['v_thursday_break_start_time'] = (!empty($request->input('thursday_shift_break_time')) ? dbTime($request->input('thursday_shift_break_time')) : null );
				$shiftTimingRecordData['v_thursday_break_end_time'] = (!empty($request->input('thursday_shift_break_end_time')) ? dbTime($request->input('thursday_shift_break_end_time')) : null );
				$shiftTimingRecordData['v_friday_break_start_time'] = (!empty($request->input('friday_shift_break_time')) ? dbTime($request->input('friday_shift_break_time')) : null );
				$shiftTimingRecordData['v_friday_break_end_time'] = (!empty($request->input('friday_shift_break_end_time')) ? dbTime($request->input('friday_shift_break_end_time')) : null );
				$shiftTimingRecordData['v_saturday_break_start_time'] = (!empty($request->input('saturday_shift_break_time')) ? dbTime($request->input('saturday_shift_break_time')): null );
				$shiftTimingRecordData['v_saturday_break_end_time'] = (!empty($request->input('saturday_shift_break_end_time')) ? dbTime($request->input('saturday_shift_break_end_time')) : null );
				$shiftTimingRecordData['v_sunday_break_start_time'] = (!empty($request->input('sunday_shift_break_time')) ? dbTime($request->input('sunday_shift_break_time')) : null );
				$shiftTimingRecordData['v_sunday_break_end_time'] = (!empty($request->input('sunday_shift_break_end_time')) ? dbTime($request->input('sunday_shift_break_end_time')) : null );
				//echo "<pre>";print_r($shiftTimingRecordData);die;
				if($recordId > 0 ){
					$successMessage =  trans('messages.success-update',['module'=> $this->moduleName ]);
					$errorMessages = trans('messages.error-update',['module'=> $this->moduleName ]);
					$result = $this->crudModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId ]);
					$insertRecord = $recordId;
					
					$updateShiftTimingRecord = $this->crudModel->updateTableData(config('constants.SHIFT_TIMING_TABLE'), $shiftTimingRecordData, ['i_shift_master_id' => $recordId ]);
					
				} else {
					$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData);
				}
				if( $insertRecord > 0 ){
					$shiftTimingRecordData['i_shift_master_id'] = $insertRecord;
					$insertShiftTimingRecord = $this->crudModel->insertTableData(config('constants.SHIFT_TIMING_TABLE') , $shiftTimingRecordData);
					$result = true;
				}
				
				$result = true;
			}catch(\Exception $e){
			
			DB::rollback();
			$result = false;
			}
			if( $result != false ){
			
				DB::commit();
			
				Wild_tiger::setFlashMessage ( 'success', $successMessage  );
			
				return redirect ( $this->redirectUrl );
			
			}
			DB::rollback();
			Wild_tiger::setFlashMessage ( 'danger', $errorMessages  );
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		
	}
	public function edit($id){
		$errorFound = true;
		$recordId = (int) Wild_tiger::decode($id);
		$data ['pageTitle'] = trans('messages.update-shift');
		if( $recordId > 0 ){
			$whereData = [];
			$whereData['master_id'] = $recordId;
			$whereData['singleRecord'] = true;
			$recordInfo = $this->crudModel->getRecordDetails($whereData);
			if(!empty($recordInfo)){
				$errorFound = false;
				$data['recordInfo'] = $recordInfo;
				
				$data['shiftTimingInfo'] = (!empty($recordInfo->shiftTimingInfo[0]) ? $recordInfo->shiftTimingInfo[0] :'');
				
				return view ( $this->folderName . 'add-shift-master' )->with ( $data );
			
			}
		}
		if( $errorFound != false ){
			return redirect ( config('constants.404_PAGE') );
		}
	}
	public function filter(Request $request){
		if ($request->ajax ()) {
			$whereData = $likeData = $additionalData = $data = [ ];
			$page = (! empty($request->post('page')) ? $request->post('page') : 1);
			$paginationData = [];
			 
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
			if(!empty($request->post('search_shift_type'))){
				$whereData['shift_type'] =  trim($request->input('search_shift_type'));
			}
			
			if(!empty($request->post('search_status'))){
				$whereData['active_status'] =  ( trim($request->input('search_status')) == config('constants.ACTIVE_STATUS') ? 1 :  0 );
			}
			
			if(!empty($columnName)) {
				switch($columnName){
					case 'shift_name':
						$columnName = 'v_shift_name';
						break;
					case 'shift_code':
						$columnName = 'v_shift_code';
						break;
					case 'shift_type':
						$columnName = 'e_shift_type';
						break;
					case 'description':
						$columnName = 'v_description';
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
					$rowData['shift_name'] = (isset($recordDetail->v_shift_name) ? $recordDetail->v_shift_name :'');
					$rowData['shift_code'] = (isset($recordDetail->v_shift_code) ? $recordDetail->v_shift_code :'');
					$rowData['shift_type'] = (isset($recordDetail->e_shift_type) ? $recordDetail->e_shift_type :'');
					$rowData['description'] = (isset($recordDetail->v_description) ? $recordDetail->v_description :'');
					$rowData['status'] = '';
					$rowData['status'] .= '<div class="status-update">';
					$rowData['status'] .= ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans("messages.active") : trans("messages.inactive") );
					$rowData['status'] .= '</div>';
					if((checkPermission('edit_shifts') != false ) || (checkPermission('delete_shifts') != false)){
						$rowData['action'] = '';
						$rowData['action'] .= '<div class="d-flex align-items-center justify-content-center">';
						if(checkPermission('edit_shifts') != false){
							$rowData['action'] .= '<a title="'.trans("messages.edit").'" href="'.route("shift-master.edit",$encodeRecordId).'" class="btn btn-sm btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></a>';
						}
						if(checkPermission('delete_shifts') != false){
							$rowData['action'] .= '<button title="'.trans("messages.delete").'" data-record-id="'. $encodeRecordId .'" data-module-name="shift-master" onclick="deleteRecord(this);" class="btn btn-sm btn-delete btn-color-text"><i class="fa fa-trash"></i></button>';
						}
						if(checkPermission('edit_shifts') != false){
							$rowData['action'] .= '<button  title="'.( ( (int)$recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') ).'" onclick="updateMasterStatusRecord(this,\'shift-master\')" data-record-id="'.$encodeRecordId .'" class="btn btn-sm btn-active btn-color-text"><i class="'.( ( (int)$recordDetail->t_is_active == 1) ? "fa fa-eye-slash" : "fa fa-eye" ).'"></i></button>';
						}
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
	public function delete(Request $request){
		
		if(!empty($request->input())){
			$deleteShiftData = [];
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			$moduleName = (!empty($request->input('delete_module_name')) ? $request->input('delete_module_name') : '');
			$successMessage =  trans('messages.success-delete',['module'=> $this->moduleName ]);
			$errorMessages = trans('messages.error-delete',['module'=> $this->moduleName ] );
			
			$deleteShiftData['t_is_active'] = 0;
			$deleteShiftData['t_is_deleted'] = 1;
			
			DB::beginTransaction();
			
			$result = false;
			try{
				$this->crudModel->deleteTableData( config('constants.SHIFT_TIMING_TABLE') ,  $deleteShiftData , [ 'i_shift_master_id' => $recordId ] );
				$this->crudModel->deleteTableData($this->tableName,  $deleteShiftData , [ 'i_id' => $recordId ] );
			
				$result = true;
			}catch(\Exception $e){
					
			}
			if( $result != false ){
					
				DB::commit();
					
				Wild_tiger::setFlashMessage ( 'success', $successMessage );
					
				return redirect()->back();
			}
			else {
					
				DB::rollback();
					
				Wild_tiger::setFlashMessage ( 'danger',$errorMessages);
					
				return redirect()->back();
			}
		}
	}
	 public function updateStatus(Request $request){
	 	if(!empty($request->input())){
			return $this->updateStatusMaster($request,$this->tableName,trans('messages.shift-master'));
	
		}
	} 
	public function checkUniqueShiftName(Request $request){
	
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0  );
		$shiftType = (!empty($request->input('shift_type')) ? trim($request->input('shift_type')) : config('constants.MORNING_SHIFT'));
		
		$validator = Validator::make ( $request->all (), [
				'shift_name' => [ 'required' , new UniqueShiftName($recordId,$shiftType) ]  ,
		], [
				'shift_name.required' => __ ( 'messages.require-shift-name' ),
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
