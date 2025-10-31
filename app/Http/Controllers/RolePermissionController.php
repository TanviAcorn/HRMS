<?php

namespace App\Http\Controllers;

use App\EmployeeModel;
use App\Models\Module;
use App\Models\MenuModel;
use Illuminate\Http\Request;
use App\Models\RolePermission;
use App\Helpers\Twt\Wild_tiger;
use App\Rules\CheckUniqueRoleName;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\LookupMaster;

class RolePermissionController extends MasterController
{
	//
	public function __construct()
	{
		parent::__construct();
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->tableName = config('constants.ROLE_PERMISSION_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER') . 'role-permission/';
		$this->crudModel = new Module();
		$this->moduleName = trans('messages.roles-permissions');
		$this->redirectUrl = config('constants.ROLES_AND_PERMISSION_MASTER_URL');
		$this->rolePermissionModel = new RolePermission();
		$this->employeeModel = new EmployeeModel();
		$this->employeesTableName = config('constants.EMPLOYEE_MASTER_TABLE');
	}

	public function index()
	{
		if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		}
		$data = $whereData = [];
		$data['pageTitle'] = trans('messages.roles-permissions');
		$page = $this->defaultPage;

		#store pagination data array
		$whereData = $paginationData = [];

		#get pagination data for first page
		if ($page == $this->defaultPage) {

			$totalRecords = count($this->rolePermissionModel->getRecordDetails($whereData));

			$lastPage = ceil($totalRecords / $this->perPageRecord);

			$paginationData['current_page'] = $this->defaultPage;

			$paginationData['per_page'] = $this->perPageRecord;

			$paginationData['last_page'] = $lastPage;
		}
		$whereData['limit'] = $this->perPageRecord;

		$data['recordDetails'] = $this->rolePermissionModel->getRecordDetails($whereData);

		$data['pagination'] = $paginationData;

		$data['page_no'] = $page;

		$data['perPageRecord'] = $this->perPageRecord;

		$data['totalRecordCount'] = $totalRecords;

		return view($this->folderName . 'role-permission')->with($data);
	}

	public function create()
	{
		if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		}
		$data['pageTitle'] = trans('messages.add-roles-permissions');
		$data['moduleDetails'] = $this->crudModel->getModuleDetails();

		return view($this->folderName . 'add-role-permission')->with($data);
	}
	public function add(Request $request)
	{

		if (!empty($request->post())) {
			$recordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0);

			$formValidation = [];
			$formValidation['role_name'] = ['required', new CheckUniqueRoleName($recordId)];
			$checkValidation = Validator::make(
				$request->all(),
				$formValidation,
				[
					'role_name.required' => __('messages.require-role-name'),
				]
			);
			if ($checkValidation->fails() != false) {
				$this->ajaxResponse(101, (!empty($checkValidation->errors()->first()) ? $checkValidation->errors()->first() :  trans('messages.error-create', ['module' => $this->moduleName])));
			}
			$successMessage =  trans('messages.success-create', ['module' => $this->moduleName]);
			$errorMessages = trans('messages.error-create', ['module' => $this->moduleName]);
			$result = false;

			DB::beginTransaction();
			try {
				$recordData = $employeeViewIds =  [];
				$viewEmployeeIds = (!empty($request->post('view_employees')) ? $request->post('view_employees') : '');
				if (!empty($viewEmployeeIds)) {
					foreach ($viewEmployeeIds as $viewEmployeeId) {
						$employeeViewIds[] = (!empty($viewEmployeeId) ? (int) Wild_tiger::decode($viewEmployeeId) : 0);
					}
				}

				$recordData['v_role_name'] = (!empty($request->post('role_name')) ? $request->post('role_name') : '');
				$recordData['v_role_description'] = (!empty($request->post('role_description')) ? $request->post('role_description') : null);
				$recordData['v_permission_ids'] = (isset($employeeViewIds) ? implode(',', $employeeViewIds) : null);
				if ($recordId  > 0) {
					$successMessage =  trans('messages.success-update', ['module' => $this->moduleName]);
					$errorMessages = trans('messages.error-update', ['module' => $this->moduleName]);
					$result = $this->crudModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId]);
				} else {
					$insertRecord = $this->crudModel->insertTableData($this->tableName, $recordData);

					if ($insertRecord > 0) {
						$result = true;
					}
				}
				$result = true;
			} catch (\Exception $e) {
				DB::rollback();
				$result = false;
			}
			if ($result != false) {
				DB::commit();
				Wild_tiger::setFlashMessage('success', $successMessage);
				return redirect($this->redirectUrl);
			} else {
				DB::rollback();
				Wild_tiger::setFlashMessage('danger', $errorMessages);
				return redirect($this->redirectUrl);
			}
		}
	}

	public function showEditForm($id = null)
	{
		if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		}
		$recordId = (!empty($id) ? $id : null);
		$data['pageTitle'] = trans('messages.edit-roles-permissions');
		$errorFound = true;
		$whereData = [];
		if (!empty($recordId)) {
			$errorFound = false;
			$recordId = (int)Wild_tiger::decode($recordId);
			$whereData['master_id'] = $recordId;
			$whereData['singleRecord'] = true;
			$recordInfo = $this->rolePermissionModel->getRecordDetails($whereData);
			if (!empty($recordInfo)) {
				$data['recordInfo'] = $recordInfo;
			}
			$data['moduleDetails'] = $this->crudModel->getModuleDetails();
			return view($this->folderName . 'add-role-permission')->with($data);
		}
		if ($errorFound != false) {
			return redirect(config('constants.404_PAGE'));
		}
	}
	public function filter(Request $request)
	{
		$whereData = $likeData = [];

		$page = (!empty($request->post('page')) ? $request->post('page') : 1);

		//search record
		if (!empty($request->post('search_by'))) {
			$searchByName = trim($request->post('search_by'));
			$likeData['searchBy'] = $searchByName;
		}
		$paginationData = [];

		if ($page == $this->defaultPage) {

			$totalRecords = count($this->rolePermissionModel->getRecordDetails($whereData, $likeData));


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

		$data['recordDetails'] = $this->rolePermissionModel->getRecordDetails($whereData, $likeData);

		if (isset($totalRecords)) {
			$data['totalRecordCount'] = $totalRecords;
		}
		$data['pagination'] = $paginationData;

		$data['page_no'] = $page;

		$data['perPageRecord'] = $this->perPageRecord;

		$html = view(config('constants.AJAX_VIEW_FOLDER') . 'role-permission/role-permission-list')->with($data)->render();

		echo $html;
		die;
	}
	public function checkUniqueRoleName(Request $request)
	{
		$recordId = (!empty($request->post('record_id')) ? (int)Wild_tiger::decode($request->post('record_id')) : 0);
		$roleName = (!empty($request->post('role_name')) ? $request->post('role_name') : '');

		$validator = Validator::make($request->all(), [
			'role_name' => ['required', new CheckUniqueRoleName($recordId)],
		], [
			'role_name.required' => __('messages.require-role-name'),
		]);

		$result = [];
		$result['status_code'] = 1;
		$result['message'] = trans('messages.success');
		if ($validator->fails()) {

			$result['status_code'] = 101;
			$result['message'] = trans('messages.error');
		}
		echo json_encode($result);
		die;
	}
	public function delete($id = null)
	{
		$recordId = (!empty($id) ? $id : null);

		if (!empty($recordId)) {
			$recordId = (int)Wild_tiger::decode($recordId);
			$updateEMployee = EmployeeModel::where('i_role_permission', $recordId)->update(['i_role_permission' => null]);
			return $this->removeRecord($this->tableName, $recordId, trans('messages.roles-permissions'));
		}
	}

	public function getEmployees(Request $request)
	{
		$data = $where = $wherePermission = [];
		$html = '';
		$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0);

		$where['login_status'] = 1;
		$where['delete_check'] = 0;

		if (isset($recordId) && !empty($recordId) && $recordId > 0) {
			$wherePermission['master_id'] = $recordId;
			$wherePermission['singleRecord'] = true;
			$rolePermissionInfo = $this->rolePermissionModel->getRecordDetails($wherePermission);

			if (isset($rolePermissionInfo) && !empty($rolePermissionInfo)) {
				$where['role_permission'] = isset($rolePermissionInfo->v_assign_employees) && !empty($rolePermissionInfo->v_assign_employees) ? $rolePermissionInfo->v_assign_employees : 'null';
				$data['rolePermissionInfo'] = $this->rolePermissionModel->getRecordDetails($wherePermission);
				$data['recordDetails'] = $this->employeeModel->getRecordDetails($where);
				
				$html = view($this->folderName . 'add-role-permission-employees-modal')->with($data)->render();
			}
		}

		echo $html;
		die;
	}

	public function assignEmployee(Request $request)
	{
		$result = false;
		if (!empty($request->input())) {
			$recordId = (!empty($request->input('record_id')) ? (int)Wild_tiger::decode($request->input('record_id')) : 0);
			
			$formValidation = [];
			// $formValidation['employees'] = ['required'];

			$validator = Validator::make($request->all(), $formValidation, [
				// 'state.employees' => __(''),
			]);
			if ($validator->fails()) {
				$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() : trans('messages.system-error')));
			} else {
				
				$result = false;
				$successMessage = trans('messages.success-create', ['module' => $this->moduleName]);
				$errorMessage = trans('messages.error-create', ['module' => $this->moduleName]);

				$whereData['master_id'] = $recordId;
				$whereData['singleRecord'] = true;
				$recordInfo = $this->rolePermissionModel->getRecordDetails($whereData);

				$recordData = $removeEmployees = [];
				if (isset($recordInfo) && !empty($recordInfo)) {
					
					$employeeIds = $request->input('employees');
					$employeesEncodedIds = (!empty($employeeIds) ? explode(',', $employeeIds) : []);
					$oldAssignEmployees = isset($recordInfo) && !empty($recordInfo->v_assign_employees) ? explode(',', $recordInfo->v_assign_employees) : [];

					$employeesDecodeIds = [];
					if (!empty($employeesEncodedIds)) {
						$employeesDecodeIds = array_map(function ($employeesEncodedId) {
							return (int) Wild_tiger::decode($employeesEncodedId);
						}, $employeesEncodedIds);
					}

					if (isset($oldAssignEmployees) && !empty($oldAssignEmployees)) {
						$removeEmployees = array_diff($oldAssignEmployees, $employeesDecodeIds);
					}
				}
				
				$recordData['v_assign_employees'] = isset($employeesDecodeIds) && !empty($employeesDecodeIds) ? implode(',', $employeesDecodeIds) : null;

				$successMessage = trans('messages.success-update', ['module' => $this->moduleName]);
				$errorMessage = trans('messages.error-update', ['module' => $this->moduleName]);
				if ($recordId > 0) {
					DB::beginTransaction();
					$result = false;
					
					try {
						$result = $this->rolePermissionModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId]);
						$employeeRecordData = [];						
						if ($result) {
							if(isset($employeesDecodeIds) && !empty($employeesDecodeIds)){
								foreach ($employeesDecodeIds as $employeeId) {
									$employeeRecordData['i_role_permission'] = $recordId;
									$this->employeeModel->updateTableData($this->employeesTableName, $employeeRecordData, ['i_login_id' =>  $employeeId]);
								}
							}
							
							if(isset($removeEmployees) && !empty($removeEmployees)){
								foreach ($removeEmployees as $removeEmployee) {
									$employeeRecordData['i_role_permission'] = null;
									$this->employeeModel->updateTableData($this->employeesTableName, $employeeRecordData, ['i_login_id' =>  $removeEmployee]);
								}
							}
						}
					} catch (\Exception $e) {
						Log::error($e->getMessage());
						DB::rollback();
						$result = false;
					}

					if ($result != false) {
						DB::commit();
					}
				}
			}
		}

		if ($result != false) {
			$this->ajaxResponse(1, $successMessage, ['moduleName' => $this->moduleName]);
		} else {
			$this->ajaxResponse(101, $errorMessage);
		}
	}
	
	public function viewAssignToEmployees($id = null) {
		
		if( session()->get('role') != config('constants.ROLE_ADMIN') ){
			return redirect(config('constants.DASHBORD_MASTER_URL'));
		}
		
		$recordId = (isset($id) && !empty($id) ? (int)Wild_tiger::decode($id) : 0);
		
		$roleInfo = RolePermission::where('i_id' , $recordId )->first();
		
		$data['pageTitle'] = trans('messages.roles-permissions');
		
		$data['rolePermissionId'] = $id;
		
		$where['login_status'] = 1;
		$where['delete_check'] = 0;		
		
		$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
		$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
		$where['employment_status'] = $selectedEmployeeStatus;
		
		$where['order_by'] = ['v_employee_full_name' => 'asc'];
		
		$where['role_assign_user'] = $recordId;
		
		$data['recordDetails'] = $this->employeeModel->getRecordDetails($where);
		
		$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
		 
		$data['designationDetails'] = LookupMaster::where('v_module_name' , config('constants.DESIGNATION_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value' , 'asc')->get();
		
		$data['roleInfo'] = $roleInfo;
		
		return view( $this->folderName . 'assign-to-employees')->with($data);
	}
	
	public function filterEmployee(Request $request){

		$whereData = $likeData = [];
		
		$recordId = (isset($request->record_id) && !empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0);
		
		//search record
		if (!empty($request->post('search_by'))) {
			$searchByName = trim($request->post('search_by'));
			$likeData['assignto_searchBy'] = $searchByName;
		}
		
		$whereData['role_assign_user'] = $recordId;
		
		if(!empty($request->post('search_team'))){
			$whereData['team_record'] = (int)Wild_tiger::decode($request->post('search_team'));
		}
		if(!empty($request->post('search_designation'))){
			$whereData['designation'] = (int)Wild_tiger::decode($request->post('search_designation'));
		}
		
		if(!empty($request->post('search_employment_status'))){
			$whereData['employment_status'] = trim($request->post('search_employment_status'));
		}
		
		if(!empty($request->post('search_employee_name'))){
			$whereData['master_id'] = (int)Wild_tiger::decode($request->post('search_employee_name'));
		}
		
		$whereData['order_by'] = ['v_employee_full_name' => 'asc'];
		
		$data['recordDetails'] = $this->employeeModel->getRecordDetails($whereData, $likeData);
		
		$html = view(config('constants.AJAX_VIEW_FOLDER') . 'role-permission/assign-to-employees-list')->with($data)->render();
		
		echo $html;
		die;
	}
	
	public function addAssginToEmployee(Request $request){
		$result = false;
		if (!empty($request->input())) {
			$recordId = (!empty($request->input('role_permission_id')) ? (int)Wild_tiger::decode($request->input('role_permission_id')) : 0);
			$result = false;
			$successMessage = trans('messages.success-create', ['module' => $this->moduleName]);
			$errorMessage = trans('messages.error-create', ['module' => $this->moduleName]);
	
			$whereData['master_id'] = $recordId;
			$whereData['singleRecord'] = true;
			$recordInfo = $this->rolePermissionModel->getRecordDetails($whereData);
	
			$recordData = $removeEmployees = [];
			if (isset($recordInfo) && !empty($recordInfo)) {					
				$employeeIds = $request->input('record_id');
				$unassignEmployeeEncodeIds = $request->input('unassing_user');
				
				$employeesEncodedIds = (!empty($employeeIds) ? $employeeIds : []);
				$oldAssignEmployees = isset($recordInfo) && !empty($recordInfo->v_assign_employees) ? explode(',', $recordInfo->v_assign_employees) : [];
				
				$unassignEmployeesDecodeIds = [];
				if (isset($unassignEmployeeEncodeIds) && !empty($unassignEmployeeEncodeIds)) {
					$unassignEmployeeEncodeIds = explode(',', $unassignEmployeeEncodeIds);
					$unassignEmployeesDecodeIds = array_map(function ($unassignEmployeeEncodeId) {
						return (int) Wild_tiger::decode($unassignEmployeeEncodeId);
					}, $unassignEmployeeEncodeIds);
				}
				
				$oldAssignEmployees = isset($unassignEmployeesDecodeIds) && !empty($unassignEmployeesDecodeIds) ? $unassignEmployeesDecodeIds : [];
	
				$employeesDecodeIds = [];
				if (!empty($employeesEncodedIds)) {
					$employeesDecodeIds = array_map(function ($employeesEncodedId) {
						return (int) Wild_tiger::decode($employeesEncodedId);
					}, $employeesEncodedIds);
					
					$where = [];
					$where['check_duplicate_role_user'] = $employeesDecodeIds;
					$where['check_duplicate_role'] = $recordId;
					
					$employeeDetails = $this->employeeModel->getRecordDetails($where);
					
					if(isset($employeeDetails) && !empty($employeeDetails) && count($employeeDetails)){
						$this->ajaxResponse(101, trans('messages.duplicate-employee-assign'));
					}
				}
	
				if (isset($oldAssignEmployees) && !empty($oldAssignEmployees)) {
					$removeEmployees = array_diff($oldAssignEmployees, $employeesDecodeIds);
				}
			}
	
			$recordData['v_assign_employees'] = isset($employeesDecodeIds) && !empty($employeesDecodeIds) ? implode(',', $employeesDecodeIds) : null;
	
			$successMessage = trans('messages.success-assign-role-to-employee' );
			$errorMessage = trans('messages.error-assign-role-to-employee' );
			
			if ($recordId > 0) {
				DB::beginTransaction();
				$result = false;					
				try {
					$result = $this->rolePermissionModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId]);
					$employeeRecordData = [];
					if ($result) {
						if(isset($employeesDecodeIds) && !empty($employeesDecodeIds)){
							foreach ($employeesDecodeIds as $employeeId) {
								$employeeRecordData['i_role_permission'] = $recordId;
								$this->employeeModel->updateTableData($this->employeesTableName, $employeeRecordData, ['i_id' =>  $employeeId]);
							}
						}
							
						if(isset($removeEmployees) && !empty($removeEmployees)){
							foreach ($removeEmployees as $removeEmployee) {
								$employeeRecordData['i_role_permission'] = null;
								$this->employeeModel->updateTableData($this->employeesTableName, $employeeRecordData, ['i_id' =>  $removeEmployee]);
							}
						}
					}
				} catch (\Exception $e) {
					Log::error($e->getMessage());
					DB::rollback();
					$result = false;
				}
	
				if ($result != false) {
					DB::commit();
				}
			}
		}
		
		if ($result != false) {
			$this->ajaxResponse(1, $successMessage, ['moduleName' => $this->moduleName]);
		} else {
			$this->ajaxResponse(101, $errorMessage);
		}
	}
}
