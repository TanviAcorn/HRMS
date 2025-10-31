<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;
use App\EmployeeModel;

class Report  extends BaseModel
{
    use HasFactory,SoftDeletes;
    
    public function __construct(){
    	parent::__construct();
    }
    
    public function getResignRecordDetails( $where = [] , $likeData = [] ){
    	
    	$query = EmployeeResignHistory::with( ['employee' , 'resignation' , 'termination' , 'employee.noticePeriodInfo' , 'employee.designationInfo' , 'employee.teamInfo' , 'approveEmployeeInfo'  ] );
    	
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$masterRecordId = $where['master_id'];
    		$query->where('i_id','=',$masterRecordId);
    	}
    	
    	if( session()->get('role') == config('constants.ROLE_USER') ){
    		if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
    			
    		} else {
    			$loginUserId = session()->get('user_employee_id');
    			$query->whereHas('employee' , function($query) use($loginUserId) {
    				//$query->where('i_leader_id','=',$loginUserId );
    				//$query->where('i_id',$loginUserId);
    				//$query->orWhere('i_leader_id',$loginUserId);
    				 
    				//$query->where('i_leader_id',$loginUserId);
    				
    				$allChildEmployeeIds = $this->childEmployeeIds(null, true);
    				if(!empty($allChildEmployeeIds)){
    					$query->whereIn('i_id', $allChildEmployeeIds);
    				} else {
    					$query->where('i_leader_id',$loginUserId);
    				}
    				
    				//$query->orWhere('i_leader_id',$loginUserId);
    			});
    		}
    	}
    	
   	 	if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
    		$employeeId = $where['employee_id'];
    		$query->whereHas('employee' , function($query) use($employeeId) {
    			$query->where('i_id',$employeeId);
    		});
    	}
    	
    	if(isset($where['resign_status']) && (!empty($where['resign_status'])) ){
    		$resignStatus = $where['resign_status'];
    		$query->where('e_initiate_type',config('constants.EMPLOYEE_INITIATE_EXIT_TYPE'));
    		$query->whereHas('resignation' , function($query) use($resignStatus) {
    			$query->where('i_id',$resignStatus);
    		});
    	}
    	
    	if(isset($where['terminate_status']) && (!empty($where['terminate_status'])) ){
    		$terminateStatus = $where['terminate_status'];
    		$query->where('e_initiate_type',config('constants.EMPLOYER_INITIATE_EXIT_TYPE'));
    		$query->whereHas('termination' , function($query) use($terminateStatus) {
    			$query->where('i_id',$terminateStatus);
    		});
    	}
    	
    	if(isset($where['team_record']) && (!empty($where['team_record'])) ){
    		$teamId = $where['team_record'];
    		$query->whereHas('employee' , function($query) use($teamId) {
    			$query->where('i_team_id',$teamId);
    		});
    	}
    	
    	if(isset($where['designation']) && (!empty($where['designation'])) ){
    		$designationId = $where['designation'];
    		$query->whereHas('employee' , function($query) use($designationId) {
    			$query->where('i_designation_id',$designationId);
    		});
    	}
    	
    	
    	if(isset($where['leader_name']) && (!empty($where['leader_name'])) ){
    		$leaderId = $where['leader_name'];
    		$query->whereHas('employee' , function($query) use($leaderId) {
    			$query->where('i_leader_id',$leaderId);
    		});
    	}
    	
    	
    	if(isset($where['team_record']) && (!empty($where['team_record'])) ){
    		$teamId = $where['team_record'];
    		$query->whereHas('employee' , function($query) use($teamId) {
    			$query->where('i_team_id',$teamId);
    		});
    	}
    	
    	if(isset($where['notice_period_start_date']) && (!empty($where['notice_period_start_date'])) ){
    		$noticePeriodFromDate = dbDate($where['notice_period_start_date']);
    		$query->whereRaw("(  dt_notice_start_date >= '".$noticePeriodFromDate."'  or dt_notice_end_date >= '".$noticePeriodFromDate."'  )");
    	}
    	if(isset($where['notice_period_end_date']) && (!empty($where['notice_period_end_date'])) ){
    		$noticePeriodToDate = dbDate($where['notice_period_end_date']);
    		$query->whereRaw("(  dt_notice_start_date <= '".$noticePeriodToDate."'  or dt_notice_end_date <= '".$noticePeriodToDate."'  )");
    	}
    	
    	if(isset($where['notice_period_start_from_date']) && (!empty($where['notice_period_start_from_date'])) ){
    		$noticePeriodStartFromDate = dbDate($where['notice_period_start_from_date']);
    		$query->whereRaw("(  dt_notice_start_date >= '".$noticePeriodStartFromDate."' )");
    	}
    	if(isset($where['notice_period_start_to_date']) && (!empty($where['notice_period_start_to_date'])) ){
    		$noticePeriodStartToDate = dbDate($where['notice_period_start_to_date']);
    		$query->whereRaw("(  dt_notice_start_date <= '".$noticePeriodStartToDate."')");
    	}
    	
    	if(isset($where['notice_period_end_from_date']) && (!empty($where['notice_period_end_from_date'])) ){
    		$noticePeriodEndFromDate = dbDate($where['notice_period_end_from_date']);
    		$query->whereRaw("(  dt_notice_end_date >= '".$noticePeriodEndFromDate."' )");
    	}
    	if(isset($where['notice_period_end_to_date']) && (!empty($where['notice_period_end_to_date'])) ){
    		$noticePeriodEndToDate = dbDate($where['notice_period_end_to_date']);
    		$query->whereRaw("(  dt_notice_end_date <= '".$noticePeriodEndToDate."')");
    	}
    	
    	
    	
    	if(isset($where['e_status']) && (!empty($where['e_status'])) ){
    		$status = $where['e_status'];
    		$query->where('e_status',$status);
    	}
    	
    	if(isset($where['type']) && (!empty($where['type'])) ){
    		$status = $where['type'];
    		$query->where('e_initiate_type',$status);
    	}
    	
    	##employment relieved status
    	if(isset($where['employment_relieved_status']) && (!empty($where['employment_relieved_status'])) ){
    		$employmentRelievedStatus = $where['employment_relieved_status'];
    		$query->whereHas('employee' , function($query) use($employmentRelievedStatus) {
    			$query->whereNotIn('e_employment_status',$employmentRelievedStatus);
    		});
    	}
    	##employment status where
    	if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
    		$employmentStatus = $where['employment_status'];
    		$query->whereHas('employee' , function($query) use($employmentStatus) {
    			if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
    				$query->whereIn('e_employment_status',[config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')]);
    			}else {
    				$query->where('e_employment_status',$employmentStatus);
    			}
    		});
    	}
    	
    	if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    	
    		$searchString = ( $likeData['searchBy'] );
    	
    		$allLikeColumns = [ 'v_document_folder_name','v_document_folder_description' ];
    	
    		$query->where(function($q) use ($allLikeColumns,$searchString){
    			foreach($allLikeColumns as $key => $allLikeColumn){
    				$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    			}
    		});
    				
    	}
    	
    	$query->orderBy('i_id', "DESC" ) ;
    	
    	if( isset($where['offset']) ){
    		$query->skip($where ['offset']);
    	}
    	
    	if( isset($where['limit']) ){
    		$query->take($where['limit']);
    	}
    	
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	return $data;
    	
    }
    
    public function employeeAttendanceDetails( $where = [] , $likeData = [] ){
    
    	$query = EmployeeModel::with( [ 'attendanceEmployeeInfo' , 'teamInfo' , 'employeeSuspendHistory' , 'timeOffMaster'  , 'myLeaveMaster'  ] );
    	
    	if(session()->get('role') == config('constants.ROLE_USER')){
    		if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
    			unset($where['show_all']);
    		} else {
    			$employeeId = session()->get('user_employee_id');
    			
    			$query->where(function ($q)use($employeeId){
    				/* $q->where('i_id',$employeeId);
    				$q->orWhere('i_leader_id',$employeeId); */
    				
    				$allChildEmployeeIds = $this->childEmployeeIds();
    				if(!empty($allChildEmployeeIds)){
    					$q->whereIn('i_id', $allChildEmployeeIds);
    				}
    			});
    		}
    	}
    	
    	if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
    		$employeeId = $where['employee_id'];
    		$query->where('i_id',$employeeId);
    	}
    	
    	if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
    		$employmentStatus = $where['employment_status'];
    		if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
    			$query->whereIn('e_employment_status',[config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')]);
    		}else {
    			$query->where('e_employment_status',$employmentStatus);
    		}
    	}
    	 
    	if(isset($where['employment_relieved_status']) && (!empty($where['employment_relieved_status'])) ){
    		$query->whereNotIn('e_employment_status',$where['employment_relieved_status']);
    	}
    
    	if(isset($where['team']) && (!empty($where['team'])) ){
    		$teamId = $where['team'];
    		$query->where('i_team_id',$teamId);
    	}
    
    	if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    		 
    		$searchString = ( $likeData['searchBy'] );
    
    		$query->where(function ($q1) use($searchString){
    			$q1->whereHas('employee' , function($query) use($searchString) {
    				$allLikeColumns = [ 'v_pan_no' , 'v_aadhar_no' , 'v_contact_no' ];
    
    				$query->where(function($q) use ($allLikeColumns,$searchString){
    					foreach($allLikeColumns as $key => $allLikeColumn){
    						$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    					}
    				});
    			});
    
    				/* $q1->orWhere(function($q) use ($searchString){
    				 $q->orWhere('v_pan_no', 'like', "%" .$searchString . "%");
    				}); */
    		});
    	}
    
    	$query->orderBy('i_id', "DESC" ) ;
    
    	if( isset($where['offset']) ){
    		$query->skip($where ['offset']);
    	}
    
    	if( isset($where['limit']) ){
    		$query->take($where['limit']);
    	}
    
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	return $data;
    
    }
    
    public function getSalaryReportRecordDetails( $where = [] , $likeData = [] ){
    	 
    	$query = Salary::with( [ 'employee' , 'employee.designationInfo' , 'employee.teamInfo' , 'employee.bankInfo'  , 'generatedSalaryInfo'  ] );
    	$query->where('t_is_salary_generated' , 1 );
    	
    	if(session()->get('role') == config('constants.ROLE_USER')){
    		if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
    			unset($where['show_all']);
    		} else {
    			$employeeId = session()->get('user_employee_id');
    			$query->whereHas('employee' , function ($q) use($employeeId){
    				$q->where(function ($q1)use($employeeId){
    					/* $q1->where('i_id',$employeeId);
    					$q1->orWhere('i_leader_id',$employeeId); */
    					
    					$allChildEmployeeIds = $this->childEmployeeIds();
    					if(!empty($allChildEmployeeIds)){
    						$q1->whereIn('i_id', $allChildEmployeeIds);
    					}
    				});
    			});
    		}
    	}
    	
    	if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
    		$employeeId = $where['employee_id'];
    		$query->where('i_employee_id',$employeeId);
    	}
    	 
    	if(isset($where['team']) && (!empty($where['team'])) ){
    		$teamId = $where['team'];
    		$query->whereHas('employee' , function($query) use($teamId) {
    			$query->where('i_team_id',$teamId);
    		});
    	}
    	 
    	if(isset($where['designation']) && (!empty($where['designation'])) ){
    		$designationId = $where['designation'];
    		$query->whereHas('employee' , function($query) use($designationId) {
    			$query->where('i_designation_id',$designationId);
    		});
    	}
    	 
    	if(isset($where['salary_start_month']) && (!empty($where['salary_start_month'])) ){
    		$startDate = dbDate($where['salary_start_month']);
    		$query->whereRaw("(  dt_salary_month >= '".$startDate."')");
    	}
    	
    	if(isset($where['salary_end_month']) && (!empty($where['salary_end_month'])) ){
    		$endDate = dbDate($where['salary_end_month']);
    		$query->whereRaw("(  dt_salary_month <= '".$endDate."')");
    	}
    	
    	if(isset($where['bank']) && (!empty($where['bank'])) ){
    		$searchBank = ($where['bank']);
    		switch($searchBank){
    			case config('constants.HDFC_BANK'):
    				$query->whereHas('employee' , function($query) {
    					$query->where('i_bank_id',config('constants.HDFC_BANK_ID'));
    				});
    				break;
    			case config('constants.OTHER_BANK'):
    				$query->whereHas('employee' , function($query){
    					$query->where('i_bank_id', '!=' , config('constants.HDFC_BANK_ID'));
    				});
    				break;
    		}
    	}
    	
    	if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    		 
    		$searchString = ( $likeData['searchBy'] );
    		$specificColumns = ( isset($likeData['specifiyColumns']) ? $likeData['specifiyColumns'] : [] ); 
    		
    		$query->where(function ($q1) use($searchString , $specificColumns ){
    			$q1->whereHas('employee' , function($query) use($searchString , $specificColumns ) {
    				$allLikeColumns = ( (!empty($specificColumns)) ? $specificColumns :  [ 'v_pan_no' , 'v_aadhar_no' , 'v_contact_no' , 'v_uan_no' ] );
    		
    				$query->where(function($q) use ($allLikeColumns,$searchString){
    					foreach($allLikeColumns as $key => $allLikeColumn){
    						$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    					}
    				});
    			});
    		
    			/* $q1->orWhere(function($q) use ($searchString){
    				$q->orWhere('v_pan_no', 'like', "%" .$searchString . "%");
				}); */
    		});
    	}
    	 
    	if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
    		$employmentStatus = $where['employment_status'];
    		$query->whereHas('employee' , function($query) use($employmentStatus) {
    			if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
    				$query->whereIn('e_employment_status',[config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')]);
    			}else {
    				$query->where('e_employment_status',$employmentStatus);
    			}
    		});
    	}
    	
    	if(isset($where['employment_relieved_status']) && (!empty($where['employment_relieved_status'])) ){
    		$employmentRelievedStatus = $where['employment_relieved_status'];
    		$query->whereHas('employee' , function($query) use($employmentRelievedStatus) {
    			$query->whereNotIn('e_employment_status',$employmentRelievedStatus);
    		});
    	}
    	
    	if( isset($where ['order_by']) ){
    		if(!empty($where ['order_by'])){
    			foreach($where ['order_by'] as  $key => $value){
    				$query->orderBy($key, (!empty($value) ? $value : 'DESC' ) );
    					
    			}
    		}
    	} else {
    		$query->orderBy('dt_salary_month', "DESC" )->orderBy('i_id' , 'desc') ;
    	}
    	
    	//$query->orderBy('i_id', "DESC" ) ;
    	 
    	if( isset($where['offset']) ){
    		$query->skip($where ['offset']);
    	}
    	 
    	if( isset($where['limit']) ){
    		$query->take($where['limit']);
    	}
    	 
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		
    		if( isset( $where ['accountReport'] ) && ( $where ['accountReport'] != false ) ){
    			$records  = $query->get();
    			if(!empty($records)){
    				$data = $records->sortBy([
    						['dt_salary_month','desc'],
    						['employee.v_employee_full_name','asc'],
    				]);
    			} else {
    				$data = [];
    			}
    		} else {
    			$data = $query->get();
    		}
    		
    		//$data = $query->get();
    	}
    	return $data;
    	 
    }
    
    public function getOnHoldSalaryDetails( $where = [] , $likeData = [] ){
    
    	$query = EmployeeModel::with( [ 'onHoldSalaryInfo' , 'designationInfo' , 'teamInfo'  , 'generatedSalaryMaster' , 'generatedSalaryMaster.generatedSalaryInfo'  ] );
    	$query->where('e_hold_salary_status' , config('constants.SELECTION_YES') );
    	if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
    		$employeeId = $where['employee_id'];
    		$query->where('i_id',$employeeId);
    	}
    	
    	if(session()->get('role') == config('constants.ROLE_USER')){
    		if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
    			unset($where['show_all']);
    		} else {
    			$employeeId = session()->get('user_employee_id');
    			$query->where(function ($q)use($employeeId){
    				/* $q->where('i_id',$employeeId);
    				$q->orWhere('i_leader_id',$employeeId); */
    				
    				$allChildEmployeeIds = $this->childEmployeeIds();
    				if(!empty($allChildEmployeeIds)){
    					$q->whereIn('i_id', $allChildEmployeeIds);
    				}
    			});
    		}
    		
    	}
    	
    	if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
    		$employmentStatus = $where['employment_status'];
    		if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
    			$query->whereIn('e_employment_status',[config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')]);
    		}else {
    			$query->where('e_employment_status',$employmentStatus);
    		}
    	}
    	 
    	if(isset($where['employment_relieved_status']) && (!empty($where['employment_relieved_status'])) ){
    		$query->whereNotIn('e_employment_status',$where['employment_relieved_status']);
    	}
    	
    	if(isset($where['team']) && (!empty($where['team'])) ){
    		$teamId = $where['team'];
    		$query->where('i_team_id',$teamId);
    		
    	}
    
    	if(isset($where['designation']) && (!empty($where['designation'])) ){
    		$designationId = $where['designation'];
    		$query->where('i_designation_id',$designationId);
    	}
    	
    	
    	if(isset($where['search_joining_from_date']) && (!empty($where['search_joining_from_date'])) ){
    		$startDate = dbDate($where['search_joining_from_date']);
    		$query->whereRaw("(  dt_joining_date >= '".$startDate."')");
    	}
    	
    	if(isset($where['search_joining_to_date']) && (!empty($where['search_joining_to_date'])) ){
    		$endDate = dbDate($where['search_joining_to_date']);
    		$query->whereRaw("(  dt_joining_date <= '".$endDate."')");
    	}
    	
    	if(isset($where['search_expected_released_from_date']) && (!empty($where['search_expected_released_from_date'])) ){
    		$startDate = dbDate($where['search_expected_released_from_date']);
    		$query->whereRaw("(  date(dt_on_hold_expected_release_date) >= '".$startDate."')");
    		/* $query->whereHas('onHoldSalaryInfo' , function($query) use($startDate) {
    			$query->whereRaw("(  dt_salary_month >= '".$startDate."')");
    		}); */
    	}
    	
    	if(isset($where['search_expected_released_to_date']) && (!empty($where['search_expected_released_to_date'])) ){
    		$endDate = dbDate($where['search_expected_released_to_date']);
    		$query->whereRaw("(  date(dt_on_hold_expected_release_date) <= '".$endDate."')");
    		/* $query->whereHas('onHoldSalaryInfo' , function($query) use($endDate) {
    			$query->whereRaw("(  dt_salary_month <= '".$endDate."')");
    		}); */
    	}
    	
    
    	if(isset($where['search_released_from_date']) && (!empty($where['search_released_from_date'])) ){
    		$startDate = dbDate($where['search_released_from_date']);
    		$query->whereRaw("(  date(dt_on_hold_release_date) >= '".$startDate."')");
    		/* $query->whereHas('generatedSalaryMaster' , function($query) use($startDate) {
    			$query->whereRaw("(  dt_salary_month >= '".$startDate."')");
    		}); */
    	}
    	 
    	if(isset($where['search_released_to_date']) && (!empty($where['search_released_to_date'])) ){
    		$endDate = dbDate($where['search_released_to_date']);
    		$query->whereRaw("(  date(dt_on_hold_release_date) <= '".$endDate."')");
    		/* $query->whereHas('generatedSalaryMaster' , function($query) use($endDate) {
    			$query->whereRaw("(  dt_salary_month <= '".$endDate."')");
    		}); */
    	}
    	
    	if(isset($where['hold_amount_status']) && (!empty($where['hold_amount_status'])) ){
    		$query->where('e_hold_salary_payment_status',trim($where['hold_amount_status']));
    	}
    	 
    	$query->orderBy('i_id', "DESC" ) ;
    
    	if( isset($where['offset']) ){
    		$query->skip($where ['offset']);
    	}
    
    	if( isset($where['limit']) ){
    		$query->take($where['limit']);
    	}
    
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	return $data;
    
    }
    
    public function incrementReportDetails( $where = [] , $likeData = [] ){
    	
    	$query = EmployeeModel::with( [ 'employeeReviseSalary' , 'employeeReviseSalary.assignSalaryInfo' , 'teamInfo'  , 'designationInfo'] );
    	
    	if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
    		$employmentStatus = $where['employment_status'];
    		if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
    			$query->whereIn('e_employment_status',[config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')]);
    		}else {
    			$query->where('e_employment_status',$employmentStatus);
    		}
    	}
    	 
    	if(session()->get('role') == config('constants.ROLE_USER')){
    		if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
    			unset($where['show_all']);
    		} else {
    			$employeeId = session()->get('user_employee_id');
    			 
    			$query->where(function ($q)use($employeeId){
    				/* $q->where('i_id',$employeeId);
    				$q->orWhere('i_leader_id',$employeeId); */
    				
    				$allChildEmployeeIds = $this->childEmployeeIds();
    				if(!empty($allChildEmployeeIds)){
    					$q->whereIn('i_id', $allChildEmployeeIds);
    				}
    			});
    		}
    	}
    	
    	if(isset($where['employment_relieved_status']) && (!empty($where['employment_relieved_status'])) ){
    		$query->whereNotIn('e_employment_status',$where['employment_relieved_status']);
    	}
    	
    	if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
    		$employeeId = $where['employee_id'];
    		$query->where('i_id',$employeeId);
    	}
    	
    	if(isset($where['team']) && (!empty($where['team'])) ){
    		$teamId = $where['team'];
    		$query->where('i_team_id',$teamId);
    	
    	}
    	
    	if(isset($where['designation']) && (!empty($where['designation'])) ){
    		$designationId = $where['designation'];
    		$query->where('i_designation_id',$designationId);
    	}
    	 
    	 
    	if(isset($where['salary_start_month']) && (!empty($where['salary_start_month'])) ){
    		$startDate = dbDate($where['salary_start_month']);
    		$query->whereHas('employeeReviseSalary' , function($query) use($startDate) {
    			$query->whereRaw("(  dt_effective_date >= '".$startDate."')");
    		});
    	}
    	
    	if(isset($where['salary_end_month']) && (!empty($where['salary_end_month'])) ){
    		$endDate = dbDate($where['salary_end_month']);
    		$query->whereHas('employeeReviseSalary' , function($query) use($endDate) {
    			$query->whereRaw("(  dt_effective_date <= '".$endDate."')");
    		});
    	}
    	
    	$query->orderBy('i_id', "DESC" ) ;
    	
    	if( isset($where['offset']) ){
    		$query->skip($where ['offset']);
    	}
    	
    	if( isset($where['limit']) ){
    		$query->take($where['limit']);
    	}
    	
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	return $data;
    	
    }
    
    public function getPunchReportDetails( $where = [] , $likeData = [] ){
    
    	//$query = PunchModel::with( [ 'onHoldSalaryInfo' , 'designationInfo' , 'teamInfo'  , 'generatedSalaryMaster' , 'generatedSalaryMaster.generatedSalaryInfo'  ] );
    	$query = PunchModel::with(['punchEmployee' , 'punchEmployee.teamInfo'])->where('t_is_deleted' , 0 );
    	
    	
    	 
    	if(session()->get('role') == config('constants.ROLE_USER')){
    		if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
    			unset($where['show_all']);
    		} else {
    			$employeeId = session()->get('user_employee_id');
    			$query->whereHas('punchEmployee' , function ($q) use($employeeId){
    				$q->where(function ($q1)use($employeeId){
    					/* $q1->where('i_id',$employeeId);
    					$q1->orWhere('i_leader_id',$employeeId); */
    					
    					$allChildEmployeeIds = $this->childEmployeeIds();
    					if(!empty($allChildEmployeeIds)){
    						$q1->whereIn('i_id', $allChildEmployeeIds);
    					}
    				});
    			});
    		}
    	
    	} else {
    		
    		
    	}
    	
    	if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
    		$employeeId = $where['employee_id'];
    		$query->whereHas('punchEmployee' , function ($q) use($employeeId){
    			$q->where('i_id',$employeeId);
    		});
    	}
    	 
   		if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
    		$employmentStatus = $where['employment_status'];
    		$query->whereHas('punchEmployee' , function($query) use($employmentStatus) {
    			if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
    				$query->whereIn('e_employment_status',[config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')]);
    			}else {
    				$query->where('e_employment_status',$employmentStatus);
    			}
    		});
    	}
    
    	if(isset($where['team']) && (!empty($where['team'])) ){
    		$teamId = $where['team'];
    		$query->whereHas('punchEmployee' , function($query) use($teamId) {
    			$query->where('i_team_id',$teamId);
    		});
    	}
    
    	if(isset($where['search_start_date']) && (!empty($where['search_start_date'])) ){
    		$startDate = dbDate($where['search_start_date']);
    		$query->whereRaw("(  date(dt_entry_date_time) >= '".$startDate."')");
    	}
    	 
    	if(isset($where['search_end_date']) && (!empty($where['search_end_date'])) ){
    		$endDate = dbDate($where['search_end_date']);
    		$query->whereRaw("(  date(dt_entry_date_time) <= '".$endDate."')");
    	}
    	 
    	$query->orderByRaw('date(dt_entry_date_time) asc') ;
    
    	if( isset($where['offset']) ){
    		$query->skip($where ['offset']);
    	}
    
    	if( isset($where['limit']) ){
    		$query->take($where['limit']);
    	}
    
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	return $data;
    
    }
    
    public function getMissingPunchDetails($where = [], $likeData = []) {
    	 
    	$selectData = [
    			'i_id',
    			'v_user_id',
    			'i_employee_id',
    			'action_date',
    			'time'
    	];
    	 
    	$query = MissingPunchInfo::select($selectData)->with(['punchEmployee', 'punchEmployee.teamInfo'])->where('t_is_deleted', 0);
    	 
   		 if(session()->get('role') == config('constants.ROLE_USER')){
   		 	if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
   		 		unset($where['show_all']);
   		 	} else {
   		 		$employeeId = session()->get('user_employee_id');
   		 		$query->whereHas('punchEmployee' , function ($q) use($employeeId){
   		 			$q->where(function ($q1)use($employeeId){
   		 				/* $q1->where('i_id',$employeeId);
   		 				$q1->orWhere('i_leader_id',$employeeId); */
   		 				
   		 				$allChildEmployeeIds = $this->childEmployeeIds();
   		 				if(!empty($allChildEmployeeIds)){
   		 					$q1->whereIn('i_id', $allChildEmployeeIds);
   		 				}
   		 			});
   		 		});
   		 	}
   		}
    	
    	if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
    		$employeeId = $where['employee_id'];
    		$query->whereHas('punchEmployee' , function ($q) use($employeeId){
    			$q->where('i_id',$employeeId);
    		});
    	}
    	 
    	if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
    		$employmentStatus = $where['employment_status'];
    		$query->whereHas('punchEmployee' , function($query) use($employmentStatus) {
    			if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
    				$query->whereIn('e_employment_status',[config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')]);
    			}else {
    				$query->where('e_employment_status',$employmentStatus);
    			}
    		});
    	}
    	 
    	
    	 
    	if(isset($where['team']) && (!empty($where['team'])) ){
    		$teamId = $where['team'];
    		$query->whereHas('punchEmployee' , function($query) use($teamId) {
    			$query->where('i_team_id',$teamId);
    		});
    	}
    	 
    	if(isset($where['search_start_date']) && (!empty($where['search_start_date'])) ){
    		$startDate = dbDate($where['search_start_date']);
    		$query->whereRaw("(  date(action_date) >= '".$startDate."')");
    	}
    	 
    	if(isset($where['search_end_date']) && (!empty($where['search_end_date'])) ){
    		$endDate = dbDate($where['search_end_date']);
    		$query->whereRaw("(  date(action_date) <= '".$endDate."')");
    	}
    	 
    	$query->orderByRaw('date(action_date) asc') ;
    	 
    	if( isset($where['offset']) ){
    		$query->skip($where ['offset']);
    	}
    	 
    	if( isset($where['limit']) ){
    		$query->take($where['limit']);
    	}
    	 
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	return $data;
    	 
    }
}
