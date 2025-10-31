<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\MasterController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Helpers\Twt\Wild_tiger;
use App\Users;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Rules\UniqueEmail;
use App\Rules\UniqueSalesEmail;
use App\Rules\FreeEmailCheck;
use App\Rules\InternationMobileFormat;

class UsersController extends MasterController
{
    //
	public function __construct(){
		
		parent::__construct();
		//$this->middleware('checklogin');
		$this->curdModel =  New Users();
		$this->moduleName = 'users';
		$this->perPageRecord = Config::get ( 'constants.PER_PAGE' );
		$this->tableName = Config::get('constants.LOGIN_MASTER_TABLE') ;
		$this->defaultPage = Config::get ( 'constants.DEFAULT_PAGE_INDEX' );
		$this->folderName = Config::get ( 'constants.ADMIN_FOLDER' ) . 'users/' ;
		
		
	}
	
	public function index(){
		
		$page = $this->defaultPage;
			
		$paginationData = $whereData =  [ ];
	
		$data['pageTitle'] = trans ( 'messages.users');
	
		return view( $this->folderName . 'users')->with($data);
			
			
	}
	
	public function filter(Request $request) {
		if ($request->ajax ()) {
				
			$whereData = $likeData  = $additionalData =  [];
				
			$draw = (!empty($request->input('draw')) ? $request->input('draw') : 1 );
				
			$offset = (!empty($request->input('start')) ? $request->input('start') : 0 );
				
			$limit = (!empty($request->input('length')) ? $request->input('length') : 10 );// Rows display per page
				
			$columnName = $columnIndex =  $columnSortOrder = '';
				
			$columnIndex = (!empty($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : '' )  ; // Column index
			$columnName = (!empty($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : '' );// Column name
			$columnSortOrder = (!empty($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : '' )  ;// asc or desc
				
			$searchValue = (!empty($_POST['search']['value']) ? $_POST['search']['value'] : '' );
				
			$page_no = (! empty ( $request->input ( 'page' ) )) ? ( int ) $request->input ( 'page' ) : 1;
	
			$searchTerm = ( (!empty( $request->post ( 'search_user' ) ) ) ? trim( $request->post ( 'search_user' )) : "" );
			
			if( (! empty ($searchTerm)) || (!empty($searchValue)) ) {
				$likeData ['lm.v_name'] = (!empty($searchTerm) ? $searchTerm : $searchValue);
				$likeData ['lm.v_mobile'] = (!empty($searchTerm) ? $searchTerm : $searchValue);
				$likeData ['lm.v_email'] = (!empty($searchTerm) ? $searchTerm : $searchValue);
			}
			
			if( (!empty($request->input('search_status'))) ){
				$whereData['t_is_active'] =  ( trim($request->input('search_status')) == config('constants.DISABLE_STATUS') ? 0 :  1 ); 
			}  
			
				
			if(!empty($columnName)) {
				switch($columnName){
					case 'name':
						$columnName = 'lm.v_name';
						break;
					case 'email':
						$columnName = 'lm.v_email';
						break;
					case 'mobile':
						$columnName = 'lm.v_mobile';
						break;
				}
				$whereData['order_by'] = [ $columnName =>  ( (!empty($columnSortOrder)) ? $columnSortOrder : 'DESC' ) ];
			} else {
				$whereData['order_by'] =  [ 'lm.i_id' =>  'desc'] ;
			}
	
			$totalRecords = count($this->curdModel->getUserDetail ( $whereData  , $likeData  ));
	
			$whereData['offset'] = $offset ;
	
			$whereData['limit'] = $limit;
			
			$recordDetails = $this->curdModel->getUserDetail ( $whereData , $likeData );
			
			$finalData = [];
				
			if(!empty($recordDetails)){
				$index = $offset;
				$allSalesRole = []; // Wild_tiger::salesPersonRoleSelection();
				foreach($recordDetails as $key => $recordDetail){
					
					$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
					$rowData = [];
					$rowData['sr_no'] = ++$index;
					$rowData['name'] = (!empty($recordDetail->v_name) ? $recordDetail->v_name : '' );
					$rowData['email'] = (!empty($recordDetail->v_email) ? ( $recordDetail->v_email ) : '' );
					$rowData['mobile'] = (!empty($recordDetail->v_mobile) ? $recordDetail->v_mobile : '' );
					
					$checked = '';
					if($recordDetail->t_is_active == 1){
						$checked = 'checked="checked"';
					}
					
					$rowData['status'] = '<div class="custom-control custom-switch status-class">';
					$rowData['status'] .= '<input type="checkbox" class="custom-control-input" data-record-id="'.$encodeRecordId.'" id="disable_'.$key.'" onclick="updateRecordStatus(this,\'users\')">';
					$rowData['status'] .= '<label class="custom-control-label record-status" for="disable_'.$key.'">'.(!empty($recordDetail->t_is_active == 1) ? trans("messages.enable") : trans("messages.disable") ) .'</label>';
					$rowData['status'] .= '</div>';
					
					$rowData['action'] = '<div class="d-flex justify-content-center">';
					$rowData['action'] .= '<a href="'. route('user.edit', $encodeRecordId ).'" title="'.trans("messages.edit-record").'" class="btn action-btn btn-info btn-sm mr-2"><i class="fas fa-pencil-alt"></i></a>';
					$rowData['action'] .= '<button title="'.trans("messages.delete-record").'" data-record-id="'.$encodeRecordId.'" data-module-name="users"  onclick="deleteRecord(this);" type="button" class="btn action-btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
					$rowData['action'] .= '</div>';
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
	
	public function create() {
	
		$data['pageTitle'] = trans ( 'messages.add-user');
				
		return view ( $this->folderName . 'add-users' )->with ( $data );
	
		
	}
	
	public function edit($id) {
	
		$id = (int) Wild_tiger::decode($id);
	
		if( $id > 0 ){
				
			$whereData = [];
			$whereData['singleRecord'] = true;
			$whereData['lm.i_id'] = $id;
			$userInfo = $this->curdModel->getUserDetail (  $whereData);
			
			$data ['recordInfo'] = $userInfo;
				
			$data['pageTitle'] = trans ( 'messages.update-user');
			
			return view ( $this->folderName . 'add-users' )->with ( $data );
	
		}
	}
	
	public function add(Request $request){
		
		$formValidation = [];
		$formValidation['name'] = 'required';
		
		$formValidation['mobile'] = [ 'required' ] ;
		
		$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
		$formValidation['email'] = [ 'required' , new UniqueEmail($recordId) ];
		if( $recordId == 0 ){
			$formValidation['password'] = 'required';
			$formValidation['confirm_password'] = 'required';
		}
		
		$validator = Validator::make ( $request->all (), $formValidation , [
				'name.required' => __ ( 'messages.required-sales-person-name' ),
				'password.required' => __ ( 'messages.required-password' ),
				'confirm_password.required' => __ ( 'messages.required-confirm-password' ),
				'email.required' => __ ( 'messages.required-login-email' ),
				'mobile.required' => __ ( 'messages.required-enter-mobile' ),
		] );
		
		
		
		if ($validator->fails ()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		
		$salesPersonData = [];
		
		$name = (!empty($request->input('name')) ? trim($request->input('name')) : null );
		$password = (!empty($request->input('password')) ? trim($request->input('password')) : null );
		$email = (!empty($request->input('email')) ? trim($request->input('email')) : null );
		$mobile = (!empty($request->input('mobile')) ? trim($request->input('mobile')) : null );
		
		$loginData = [];
		$loginData['v_name'] =  $name;
		$loginData['v_email'] =  $email;
		$loginData['v_mobile'] =  $mobile;
		$loginData['v_password'] =  password_hash($password, PASSWORD_DEFAULT);
		$loginData['v_role'] =  config('constants.ROLE_USER');
		
		$result = false;
		
		$successMessage = trans ( 'messages.success-module-create', [ 'module' => $this->moduleName ] );
		$errorMessage = trans ( 'messages.error-create', [ 'module' => $this->moduleName ] );
		
		DB::beginTransaction();
		try{
			
			if( $recordId > 0 ){
				
				$successMessage = trans ( 'messages.success-update', [ 'module' => $this->moduleName ] );
				$errorMessage = trans ( 'messages.error-update', [ 'module' => $this->moduleName ] );
				
				$this->curdModel->updateTableData( config('constants.LOGIN_MASTER_TABLE') , $loginData , [ 'i_id' =>  $recordId ] );
			} else {
				$this->curdModel->insertTableData( config('constants.LOGIN_MASTER_TABLE') , $loginData );
			}
			
			
			
			$result = true;
		}catch(\Exception $e){
			DB::rollback();
			$result = false;
		}
		
		if( $result != false ){
				
			DB::commit();
				
			Wild_tiger::setFlashMessage ( 'success', $successMessage  );
				
			return redirect ($this->moduleName);
				
		}
		
		DB::rollback();
		
		Wild_tiger::setFlashMessage ( 'danger', $errorMessage  );
		return redirect()->back()->withErrors ( $validator )->withInput ();
	}
	
	public function update(Request $request) {
		
		
		
		$formValidation = [];
		$formValidation['name'] = 'required';
		$formValidation['email'] = [ 'required' , new UniqueEmail($recordId)   ];
		$formValidation['mobile'] = [ 'required',  new InternationMobileFormat ] ;
		
		$validator = Validator::make ( $request->all (), $formValidation , [
				'name.required' => __ ( 'messages.required-sales-person-name' ),
				'password.required' => __ ( 'messages.required-password' ),
				'confirm_password.required' => __ ( 'messages.required-confirm-password' ),
				'email.required' => __ ( 'messages.required-login-email' ),
				'mobile.required' => __ ( 'messages.required-enter-mobile' ),
		] );
		
		if ($validator->fails ()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
		
		$salesPersonId = (!empty($request->hidden_sales_person_id) ? (int)Wild_tiger::decode($request->hidden_sales_person_id) : 0  );
		
		
		$salesPersonData = [];
		
		$name = (!empty($request->input('name')) ? trim($request->input('name')) : null );
		$email = (!empty($request->input('email')) ? trim($request->input('email')) : null );
		$mobile = (!empty($request->input('mobile')) ? trim($request->input('mobile')) : null );
		
		$loginData = [];
		$loginData['v_name'] =  $name;
		$loginData['v_email'] =  $email;
		$loginData['v_mobile'] =  $mobile;
		
		if(!empty($request->input('password'))){
			$loginData['v_password'] =  password_hash($request->input('password'), PASSWORD_DEFAULT);
		}
		
		
		$result = false;
		
		DB::beginTransaction();
		try{
				
			$updateLogin = $this->curdModel->updateTableData(config('constants.LOGIN_MASTER_TABLE'), $loginData , [ 'i_id' => $salesPersonLoginId  ] );
				
			$result = true;
		}catch(\Exception $e){
			DB::rollback();
			$result = false;
		}
		
		if( $result != false ){
		
			DB::commit();
		
			Wild_tiger::setFlashMessage ( 'success', trans ( 'messages.success-update', [
					'module' => $this->moduleName
			] ) );
		
			return redirect ($this->moduleName);
		
		}
		
		DB::rollback();
		
		Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-update', [
				'module' => $this->moduleName
		] ) );
		return redirect()->back()->withErrors ( $validator )->withInput ();
	}
	
	public function delete(Request $request){
		
		if(!empty($request->input())){
			
			$recordId = (!empty($request->input('delete_record_id')) ? (int)Wild_tiger::decode( $request->input('delete_record_id') ) : 0 );
			
			return $this->removeRecord($this->tableName, $recordId, trans('messages.user'));
			
		}
	}
	
	public function updateStatus(Request $request){
		
		if(!empty($request->input())){
		
			return $this->updateMasterStatus($request , $this->tableName,  trans('messages.user'));
		
		}
	}
}
