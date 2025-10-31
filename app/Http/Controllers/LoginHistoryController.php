<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MasterController;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\LoginHistory;
use App\EmployeeModel;
use App\Helpers\Twt\Wild_tiger;
use App\LookupMaster;

class LoginHistoryController extends MasterController
{
    //
	public function __construct(){
		//parent::__construct();
		$this->middleware('checklogin');
		$this->crudModel = new LoginHistory();
		$this->perPageRecord = 20;
	}
	
	public function index(){
		 
		$data['pageTitle'] =  trans('messages.login-history');;
		
		$where = $additionalData = [];
		
		$paginationData = [];
		
		$pageNo = config('constants.DEFAULT_PAGE_INDEX');
		
		$totalRecords = 0;
		
		$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
		$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
		$additionalData['whereIn'][] = [ 'em.e_employment_status' ,   [config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') ]  ];
		
		if(session()->get('role') != config("constants.ROLE_ADMIN")){
			$where['lm.i_id'] = session()->get('user_id');
		}
		
		$data['selectedUserId'] = ( session()->has('user_employee_id') ? session()->get('user_employee_id') : 0 );
		
		$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
		$data['allPermissionId'] = $allPermissionId;
		
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$where['show_all'] = true;
		}
		
		if ($pageNo == config('constants.DEFAULT_PAGE_INDEX') ){
			
			$total = count($this->crudModel->getRecordDetail($where, [], $additionalData));
			$totalRecords = $total;
				
			$lastpage = ceil($total/$this->perPageRecord);
				
			$paginationData['current_page'] = config('constants.DEFAULT_PAGE_INDEX');
				
			$paginationData['per_page'] = $this->perPageRecord;
				
			$paginationData['last_page'] = $lastpage;
			
		}
		
		$where['limit'] = $this->perPageRecord;
		
		$data['page_no'] = $pageNo;
			
		$data['perPageRecord'] = $this->perPageRecord;
			
		$data['recordDetails'] = $this->crudModel->getRecordDetail ($where, [], $additionalData);
		
		$data['pagination'] = $paginationData;
		
		$data['totalRecordCount'] = $totalRecords;
		
		$employeeWhere['order_by'] = [ 'v_employee_full_name'  => 'asc'];
		$employeeWhere['employment_status'] = $selectedEmployeeStatus;
		
		if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
			$employeeWhere['show_all'] = true;
		}
		
		$employeeCrudModel = new EmployeeModel();
		$data['employeeDetails'] = $employeeCrudModel->getRecordDetails($employeeWhere);
		$data['teamRecordDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
		
		return view('admin/login-history' , $data);
	}
	
	public function filter(Request $request) {
		if ($request->ajax ()) {
			$whereData = $likeData  = $additionalData =  [];
			
			$pageNo = (! empty ( $request->input ( 'page' ) )) ? ( int ) $request->input ( 'page' ) : 1;
			
			if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ){
				$whereData['show_all'] = true;
			}
			
			$paginationData = [];
			
			if ((! empty ( $request->input ( 'search_status' ) )) && ($request->input ( 'search_status' ) != 'all')) {
				$whereData ['lh.t_is_active'] = strtolower($request->input ( 'search_status' ) == config('constants.ENABLE_STATUS') ? 1 : 0);
			}
			
			if(!empty($request->post('search_employee'))){
				$whereData['em.i_id'] = (int)Wild_tiger::decode($request->post('search_employee'));
			}
			
			## employment status filter
			if(!empty($request->post('search_employee_status'))){
				$employmentStatus = trim($request->post('search_employee_status'));
				
				if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
					
					$additionalData['whereIn'][] = [ 'em.e_employment_status' ,   [config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') ]  ];
				} else {
					
					$whereData['em.e_employment_status'] = $employmentStatus;
				}
			}
			
			if(!empty($request->post('search_team'))){
				$whereData['em.i_team_id'] = (int)Wild_tiger::decode($request->post('search_team'));
			}
			
			if (! empty ( $request->post ( 'search_start_date' ) )) {
				$startDate = dbDate( $request->input ( 'search_start_date' ) );
				$whereData['custom_function'][] =  "date(lh.dt_created_at) >=  '".$startDate."'";
			}
			
			if (! empty ( $request->post ( 'search_end_date' ) )) {
				$endDate = dbDate( $request->input ( 'search_end_date' ) );
				$whereData['custom_function'][] =  "date(lh.dt_created_at) <=  '".$endDate."'";
			}
				
			
			
			if ($pageNo == config('constants.DEFAULT_PAGE_INDEX') ){
					
				$total = count($this->crudModel->getRecordDetail($whereData , $likeData , $additionalData ));
			
				$lastpage = ceil($total/$this->perPageRecord);
			
				$paginationData['current_page'] = config('constants.DEFAULT_PAGE_INDEX');
			
				$paginationData['per_page'] = $this->perPageRecord;
			
				$paginationData['last_page'] = $lastpage;
					
			}
			//dd($whereData);
			if ($pageNo == config('constants.DEFAULT_PAGE_INDEX')) {
				$whereData['offset'] = 0;
				$whereData['limit'] = $this->perPageRecord;
			} else if ($pageNo > config('constants.DEFAULT_PAGE_INDEX')) {
				$whereData['offset'] = ($pageNo - 1) * $this->perPageRecord;
				$whereData['limit'] = $this->perPageRecord;
			}
				
			$recordDetails = $this->crudModel->getRecordDetail ( $whereData, $likeData , $additionalData );
				
			$data = [];
			
			$data['page_no'] = $pageNo;
			
			$data['perPageRecord'] = $this->perPageRecord;
			
			$data['recordDetails'] = $recordDetails;
			
			$data['pagination'] = $paginationData;
			
			if(isset($total)){
				$data ['totalRecordCount'] = $total;
			}
			
			$html = view ( config('constants.AJAX_VIEW_FOLDER') . 'login-history/login-history-list' )->with ( $data )->render();
			
			return response ( $html );
		}
	}
}
