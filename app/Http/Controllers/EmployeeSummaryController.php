<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\EmployeeModel;
use DB;
use App\LookupMaster;
use App\Helpers\Twt\Wild_tiger;
use App\StateMasterModel;
use App\ProbationPolicyMasterModel;
use App\CityMasterModel;

class EmployeeSummaryController extends MasterController{
	public function __construct(){
    	parent::__construct();
		$this->employeeModel = new EmployeeModel();
		$this->moduleName = trans('messages.employees-summary');
		$this->folderName =config('constants.ADMIN_FOLDER').'employees-summary/' ;
	}
	
	public function index(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */		
		$data = [] ;
		$data['pageTitle'] = trans('messages.employees-summary');
		
		$teamDetails = LookupMaster::with(['employeeTeamInfo'])->where('v_module_name' , config('constants.TEAM_LOOKUP'))->get(); 
		$stateMasterDetails = StateMasterModel::with(['stateEmployee'])->get();
		$cityMasterDetails = CityMasterModel::with(['currentCityEmployee'])->get();
		$data['designationDetails'] = $teamDetails ;
		$data['stateDetails'] = $stateMasterDetails ;
		$data['cityDetails'] = $cityMasterDetails ;
		return view( $this->folderName . 'employees-summary')->with($data);
	}

	public function filterEmployeeSummary(Request $request){
		
		if(!empty($request->post())){
			
			$teamId = (!empty($request->post('search_designation_id')) ? (int)Wild_tiger::decode($request->post('search_designation_id')) : 0);
			$employeeCityId = (!empty($request->post('search_city_id')) ? (int)Wild_tiger::decode($request->post('search_city_id')) : 0);
			
			$employeeWhere = [];
			if( $teamId > 0 ){
				$employeeWhere['team_record'] = $teamId;
			}
			
			if( $employeeCityId > 0 ){
				$employeeWhere['current_address_city_id'] = $employeeCityId;
			}
			
			if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_SUMMARY'), session()->get('user_permission')  ) ) ){
				$employeeWhere['show_all'] = true;
			}
			
			$employeeWhere['employment_status'] =  config('constants.WORKING_EMPLOYMENT_STATUS');
			$employeeDetails = $this->employeeModel->getRecordDetails($employeeWhere);
			$employeeDetails = (!empty($employeeDetails) ? objectToArray($employeeDetails) : [] );
			
			
			$inProbationCount = array_map(function($employeeDetail){
				if( $employeeDetail['e_employment_status'] == config('constants.PROBATION_EMPLOYMENT_STATUS')  ){
					return $employeeDetail;
				}
			}, $employeeDetails);
			
			$noticePeriodCount = array_map(function($employeeDetail){
				if( $employeeDetail['e_employment_status'] == config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')  ){
					return $employeeDetail;
				}
			}, $employeeDetails);
			
			$designationWhere = [];
			$designationWhere['v_module_name'] = config('constants.TEAM_LOOKUP');
			if( $teamId > 0 ){
				$designationWhere['i_id'] = $teamId;
			}
			
			
			$getTeamWiseDetailsQuery = LookupMaster::with(['employeeTeamInfo' => function($q) use ($employeeCityId,$employeeWhere){
				if( $employeeCityId > 0 ){
					$q->where('i_current_address_city_id',$employeeCityId);
				}
				
				if(session()->get('role') == config('constants.ROLE_USER')){
					if(  isset($employeeWhere['show_all']) && ( $employeeWhere['show_all'] == true ) ){
						
					} else {
						$employeeId = session()->get('user_employee_id');
						$q->where(function ($q)use($employeeId){
							//$q->whereRaw( "( i_id = '".$employeeId."' or i_leader_id = '".$employeeId."' ) " );
							
							$allChildEmployeeIds = $this->employeeModel->childEmployeeIds();
							if(!empty($allChildEmployeeIds)){
								$q->whereIn('i_id', $allChildEmployeeIds);
							}
						});
					}
				}
				
				
				$q->where('e_employment_status','!=',config('constants.RELIEVED_EMPLOYMENT_STATUS'));
			}])->where($designationWhere);
			
			$teamDetails = $getTeamWiseDetailsQuery->get();
			
			$teamCount = [];
			if(!empty($teamDetails)){
				foreach($teamDetails as $teamDetail){
					$recordData = [];
					$recordData['team_name'] = (!empty($teamDetail->v_value) ? $teamDetail->v_value :'');
					$recordData['color_code'] = (!empty($teamDetail->v_chart_color) ? $teamDetail->v_chart_color : '8d191a');
					$recordData['count_info'] = ( !empty($teamDetail->employeeTeamInfo) ? count($teamDetail->employeeTeamInfo) : 0 ) ;
					$teamCount[] = $recordData;
				}
			}
			
			$cityWhere = [];
			if( $employeeCityId > 0 ){
				$cityWhere['i_id'] = $employeeCityId;
			}
			$getCityWiseDetailsQuery = CityMasterModel::with(['currentCityEmployee' => function($q) use ($teamId,$employeeWhere){
				if( $teamId > 0 ){
					$q->where('i_team_id',$teamId);
				}
				if(session()->get('role') == config('constants.ROLE_USER')){
					if(  isset($employeeWhere['show_all']) && ( $employeeWhere['show_all'] == true ) ){
				
					} else {
						$employeeId = session()->get('user_employee_id');
						$q->where(function ($q)use($employeeId){
							//$q->whereRaw( "( i_id = '".$employeeId."' or i_leader_id = '".$employeeId."' ) " );
							
							$allChildEmployeeIds = $this->employeeModel->childEmployeeIds();
							if(!empty($allChildEmployeeIds)){
								$q->whereIn('i_id', $allChildEmployeeIds);
							}
						});
					}
				}
				$q->where('e_employment_status','!=',config('constants.RELIEVED_EMPLOYMENT_STATUS'));
			}])->where($cityWhere);
			
			$cityMasterDetails = $getCityWiseDetailsQuery->get();
			
			$cityCount = [];
			if(!empty($cityMasterDetails)){
				foreach($cityMasterDetails as $cityMasterDetail){
					$rowData = [];
					$rowData['city_name'] = (!empty($cityMasterDetail->v_city_name) ? $cityMasterDetail->v_city_name :'');
					$rowData['color_code'] = (!empty($cityMasterDetail->v_chart_color) ? $cityMasterDetail->v_chart_color :'');
					$rowData['count_info'] = (!empty($cityMasterDetail->currentCityEmployee) ? count($cityMasterDetail->currentCityEmployee) :0);
					$cityCount[] = $rowData;
				}
			}
			
			
			$data['totalEmpCount'] = count($employeeDetails);
			$data['totalEmpInProbationCount'] = (!empty($inProbationCount) ? count(array_filter($inProbationCount)) : 0 );
			$data['totalEmpInNoticePeriodCount'] = (!empty($noticePeriodCount) ? count(array_filter($noticePeriodCount)) : 0 );
			$data['teamWiseCountDetails'] = $teamCount;
			$data['cityWiseCountDetails'] = $cityCount;
			$data['employeeTeamId'] = $teamId;
			$data['employeeCityId'] = $employeeCityId;
			//dd($data);
			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employees-summary/filter-employee-summary')->with ( $data )->render();
			
			echo $html;die;
			
		}
	}
}
