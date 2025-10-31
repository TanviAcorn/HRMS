<?php

namespace App;

use App\BaseModel;
use App\Traits\MySoftDeletes;
use App\Models\EmployeeSalaryModel;
use App\Models\EmployeeSalaryDetailModel;
use App\Models\TimeOff;
use App\Models\EmployeeResignHistory;
use App\Models\LeaveSummaryModel;
use App\Models\VillageMasterModel;
use App\Models\SuspendHistory;
use App\Models\EmployeeProbationHistory;
use App\Models\OnHoldSalaryModel;
use App\Models\Salary;
use App\Models\ReviseSalaryMaster;
use App\Models\RolePermission;
use App\Models\AcademicSavedLeaveInfo;
use App\Models\ProbationAssessment;

class EmployeeModel extends BaseModel{
	
	use MySoftDeletes;
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected $table = 'employees';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
    
    /**
     * Get all probation assessments for the employee
     */
    public function probationAssessments()
    {
        return $this->hasMany(ProbationAssessment::class, 'i_employee_id', 'i_id');
    }
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.EMPLOYEE_MASTER_TABLE');
    	//$this->perPage = config ( 'constants.PER_PAGE' );
    }
    public function cityCurrentInfo(){
    	return $this->belongsTo(CityMasterModel::class , 'i_current_address_city_id');
    }
    
    public function cityPermanentInfo(){
    	return $this->belongsTo(CityMasterModel::class , 'i_permanent_address_city_id');
    }
    public function designationInfo(){
        return $this->belongsTo(LookupMaster::class , 'i_designation_id');
    }
    public function subDesignationInfo(){
        return $this->belongsTo(SubDesignationMasterModel::class , 'i_sub_designation_id');
    }
    public function teamInfo(){
        return $this->belongsTo(LookupMaster::class , 'i_team_id');
    }
   /*  public function leaderInfo(){
    	return $this->belongsTo(LookupMaster::class , 'i_leader_id');
    } */
    public function recruitmentSourceInfo(){
    	return $this->belongsTo(LookupMaster::class , 'i_recruitment_source_id');
    }
    
   /*  public function referenceEmployeeInfo(){
    	return $this->belongsTo(LookupMaster::class , 'i_reference_emp_id');
    } */
    
    public function probationPeriodInfo(){
    	return $this->belongsTo(ProbationPolicyMasterModel::class , 'i_probation_period_id');
    }
    
    public function noticePeriodInfo(){
    	return $this->belongsTo(ProbationPolicyMasterModel::class , 'i_notice_period_id');
    }
    
    public function bankInfo(){
    	return $this->belongsTo(LookupMaster::class , 'i_bank_id');
    }
    
    public function employeeInfo(){
    	return $this->belongsTo(EmployeeModel::class , 'i_reference_emp_id');
    }
    
    public function employeeRelation(){
    	return $this->hasMany(EmployeeRelationModel::class,'i_employee_id');
    }
    public function leaderInfo(){
    	return $this->belongsTo(EmployeeModel::class , 'i_leader_id');
    }
    public function childInfo(){
    	return $this->hasMany(EmployeeModel::class , 'i_leader_id');
    }
    public function attendanceEmployeeInfo(){
    	return $this->hasMany(MyAttendanceModel::class,'i_employee_id');
    }
    public function salaryInfo(){
    	return $this->hasOne(EmployeeSalaryModel::class,'i_employee_id');
    }
    public function salaryDetail(){
    	return $this->hasManyThrough( EmployeeSalaryDetailModel::class ,  EmployeeSalaryModel::class, 'i_employee_id' , 'i_employee_salary_id');
    }
    public function myLeaveMaster(){
    	return $this->hasMany(MyLeaveModel::class,'i_employee_id');
    }
    public function leaveAppove(){
    	return $this->hasMany(MyLeaveModel::class,'i_approved_by_id');
    }
    public function timeOffAppove(){
    	return $this->hasMany(TimeOff::class,'i_approved_by_id');
    }
    public function timeOffMaster(){
    	return $this->hasMany(TimeOff::class,'i_employee_id');
    }
    
    public function shiftInfo(){
    	return $this->belongsTo(ShiftMasterModel::class , 'i_shift_id');
    }
    
    public function weekOffInfo(){
    	return $this->belongsTo(WeeklyOffMasterModel::class , 'i_weekoff_id');
    }
    
    public function latestGeneratedSalary(){
    	return $this->hasOne(Salary::class, 'i_employee_id')->where('t_is_deleted' , 0)->where('t_is_salary_generated' , 1)->orderBy('dt_salary_month','desc')->limit(1);
    }
    
    public function latestResignHistory(){
    	//return $this->hasOne(EmployeeResignHistory::class , 'i_employee_id' ) ->orderBy('i_id','desc')->limit(1);
    	return $this->hasOne(EmployeeResignHistory::class , 'i_employee_id' ) ->whereNotIn( 'e_status' , [ config('constants.CANCELLED_STATUS') , config('constants.REJECTED_STATUS') ] )-> orderBy('i_id','desc')->limit(1);
    }
    
    public function latestSalaryMaster(){
    	//return $this->hasOne(EmployeeResignHistory::class , 'i_employee_id' ) ->orderBy('i_id','desc')->limit(1);
    	return $this->hasOne(EmployeeSalaryModel::class , 'i_employee_id' ) ->where( 't_is_deleted' , 0  )->latest();
    }
    
    public function latestDisplayResignHistory(){
    	//return $this->hasOne(EmployeeResignHistory::class , 'i_employee_id' ) ->orderBy('i_id','desc')->limit(1);
    	return $this->hasOne(EmployeeResignHistory::class , 'i_employee_id' ) ->whereNotIn( 'e_status' , [ config('constants.CANCELLED_STATUS') , config('constants.REJECTED_STATUS') ] )->latest();
    }
    
    public function validLatestResignHistory(){
    	return $this->hasOne(EmployeeResignHistory::class , 'i_employee_id' ) ->whereNotIn( 'e_status' , [ config('constants.CANCELLED_STATUS') ] )-> orderBy('i_id','desc')->limit(1);
    }
	
	public function employeeLeaveSummaryInfo(){
    	return $this->hasMany(LeaveSummaryModel::class,'i_employee_id');
    }
    
    public function currentVillageInfo(){
    	return $this->belongsTo(VillageMasterModel::class,'i_current_village_id');
    }
    
    public function permentVillageInfo(){
    	return $this->belongsTo(VillageMasterModel::class,'i_permanent_village_id');
    }
    public function employeeDocumentInfo(){
    	return $this->hasMany(EmployeeDocumentModel::class,'i_employee_id');
    }
    public function loginInfo(){
    	return $this->belongsTo(Login::class,'i_login_id');
    }
    
    public function employeeSuspendHistory(){
    	return $this->hasMany( SuspendHistory::class , 'i_employee_id' );
    }
    
    public function employeeProbationHistory(){
    	return $this->hasMany( EmployeeProbationHistory::class , 'i_employee_id' )->whereNotNull('dt_end_date' );
    }
    public function onHoldSalaryInfo(){
    	return $this->hasMany(OnHoldSalaryModel::class,'i_employee_id');
    }
    
    public function generatedSalaryMaster(){
    	return $this->hasMany(Salary::class,'i_employee_id');
    }
    
    public function employeeReviseSalary(){
    	return $this->hasMany(ReviseSalaryMaster::class,'i_employee_id');
    }
    
    public function employeeAssignRole(){
    	return $this->belongsTo( RolePermission::class , 'i_role_permission' );
    }
  	public function getRecordDetails( $where = [] , $likeData = [] , $additionalData = [] ){
     $query = EmployeeModel::with([ 'loginInfo' , 'leaderInfo.loginInfo' , 'cityCurrentInfo.stateMaster.countryMaster' , 'cityPermanentInfo.stateMaster.countryMaster' ,'designationInfo' , 'subDesignationInfo' ,'teamInfo' ,'leaderInfo' ,'recruitmentSourceInfo' ,'probationPeriodInfo' ,'noticePeriodInfo' ,'bankInfo','employeeRelation' , 'shiftInfo' , 'latestResignHistory', 'latestDisplayResignHistory' ,  'weekOffInfo','employeeInfo','currentVillageInfo','permentVillageInfo','onHoldSalaryInfo','myLeaveMaster','myLeaveMaster.leaveSummaryInfo' ,'generatedSalaryMaster' , 'generatedSalaryMaster.generatedSalaryInfo' , 'generatedSalaryMaster.generatedSalaryInfo.generateSalaryComponent' , 'latestGeneratedSalary' , 'salaryInfo' , 'salaryInfo.salaryGroup' , 'employeeAssignRole'  ]);
     
    /*  
     if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) )){
     	$where['show_all'] = true;
     } */
     
     if(session()->get('role') == config('constants.ROLE_USER')){
     	if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
     		unset($where['show_all']);
     	} else {
     		$query->where(function ($q){
     			//$q->whereRaw( "( i_id = '".$employeeId."' or i_leader_id = '".$employeeId."' ) " );
     			
     			$allChildEmployeeIds = $this->childEmployeeIds();
     			if(!empty($allChildEmployeeIds)){
     				$q->whereIn('i_id', $allChildEmployeeIds);
     			}
     		});
     	}
     }
     
     if(isset($where['master_id']) && (!empty($where['master_id'])) ){
     	$masterRecordId = $where['master_id'];
     	$query->where('i_id','=',$masterRecordId);
     }
     if(isset($where['check_duplicate_role_user']) && (!empty($where['check_duplicate_role_user'])) ){
     	$employeesRecordId = $where['check_duplicate_role_user'];
     	$query->whereIn('i_id', $employeesRecordId);
     }
     if(isset($where['check_duplicate_role']) && (!empty($where['check_duplicate_role'])) ){
     	$employeesRoleRecordId = $where['check_duplicate_role'];
     	$query->whereNotNull('i_role_permission');
     	$query->where('i_role_permission', '!=', $employeesRoleRecordId);
     }
     if(isset($where['current_address_city_id']) && (!empty($where['current_address_city_id'])) ){
     	$currentCityId = $where['current_address_city_id'];
     	$query->where('i_current_address_city_id',$currentCityId);
     }
     if(isset($where['permanent_address_city_id']) && (!empty($where['permanent_address_city_id'])) ){
     	$permentCityId = $where['permanent_address_city_id'];
     	$query->where('i_permanent_address_city_id',$permentCityId);
     }
     if(isset($where['gender']) && (!empty($where['gender'])) ){
     	$genderRecord = $where['gender'];
     	$query->where('e_gender',$genderRecord);
     }
     
     if(isset($where['marital_status']) && (!empty($where['marital_status'])) ){
     	$maritalStatus = $where['marital_status'];
     	$query->where('e_marital_status',$maritalStatus);
     }
     
     if(isset($where['blood_group']) && (!empty($where['blood_group'])) ){
     	$bloodGroupRecord = $where['blood_group'];
     	$query->where('v_blood_group',$bloodGroupRecord);
     }
     if(isset($where['joining_from_date']) && (!empty($where['joining_from_date'])) ){
     	$joiningFromDate = dbDate($where['joining_from_date']);
     	//$query->where('dt_joining_date','>=',dbDate($joiningFromDate));
     	$query->whereRaw("(  dt_joining_date >= '".$joiningFromDate."')");
     }
     if(isset($where['joining_to_date']) && (!empty($where['joining_to_date'])) ){
     	$joiningToDate = dbDate($where['joining_to_date']);
     	//$query->where('dt_joining_date','<=',dbDate($joiningToDate));
     	$query->whereRaw("(  dt_joining_date <= '".$joiningToDate."')");
     }
     if(isset($where['designation']) && (!empty($where['designation'])) ){
     	$designationId = $where['designation'];
     	$query->where('i_designation_id',$designationId);
     }
     if(isset($where['team_record']) && (!empty($where['team_record'])) ){
     	$teamRecordId = $where['team_record'];
     	$query->where('i_team_id',$teamRecordId);
     }
     if(isset($where['recruitment_source']) && (!empty($where['recruitment_source'])) ){
     	$recruitmentRecordId = $where['recruitment_source'];
     	$query->where('i_recruitment_source_id',$recruitmentRecordId);
     }
     if(isset($where['reference_name']) && (!empty($where['reference_name'])) ){
     	$referenceRecordId = $where['reference_name'];
     	$query->where('i_reference_emp_id',$referenceRecordId);
     }
     if(isset($where['login_status'])){
     	$activeStatus = $where['login_status'];
     	$query->where('t_is_active',$activeStatus);
     }
     if(isset($where['delete_check'])){
		// dd($activeStatus, 'sdsds');
     	$deleteCheck = $where['delete_check'];
     	$query->where('t_is_deleted',$deleteCheck);
     }
     if(isset($where['role_permission'])){
		if($where['role_permission'] != 'null'){
			$rolePermission = explode(',', trim($where['role_permission']));
			$query->where(function($q) use ($rolePermission) {
				$q->orwhere('i_role_permission',null);
				foreach($rolePermission as $where){
				   $q->orwhere('i_login_id',$where);
			   }
		   });
		} else {
			$query->where('i_role_permission',null);
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
     if(isset($where['state']) && (!empty($where['state'])) ){
     	$stateId = $where['state'];
     	$query->whereHas('cityCurrentInfo.stateMaster' , function($query) use($stateId) {
     		$query->where('i_id',$stateId);
     	});
     }
     if(isset($where['shift_record']) && (!empty($where['shift_record'])) ){
     	$shiftRecordId = $where['shift_record'];
     	$query->where('i_shift_id',$shiftRecordId);
     }
     if(isset($where['probation_id']) && (!empty($where['probation_id'])) ){
     	$probationRecordId = $where['probation_id'];
     	$query->where('i_probation_period_id',$probationRecordId);
     }
     if(isset($where['notice_period_id']) && (!empty($where['notice_period_id'])) ){
     	$noticePeriodRecordId = $where['notice_period_id'];
     	$query->where('i_notice_period_id',$noticePeriodRecordId);
     }
     if(isset($where['weekly_off_id']) && (!empty($where['weekly_off_id'])) ){
     	$weeklyOffRecordId = $where['weekly_off_id'];
     	$query->where('i_weekoff_id',$weeklyOffRecordId);
     }
     if(isset($where['bank_id']) && (!empty($where['bank_id'])) ){
     	$bankRecordId = $where['bank_id'];
     	$query->where('i_bank_id',$bankRecordId);
     }
     if(isset($where['leader_name']) && (!empty($where['leader_name'])) ){
     	$leaderId = $where['leader_name'];
     	$query->where('i_leader_id',$leaderId);
     }
     
     if(isset($where['weekly_off_record']) && (!empty($where['weekly_off_record'])) ){
     	$shiftRecordId = $where['weekly_off_record'];
     	$query->where('i_weekoff_id',$shiftRecordId);
     }
     
     if(isset($where['probation_to_date']) && (!empty($where['probation_to_date'])) ){
     	$probationToDate = dbDate($where['probation_to_date']);
     	$query->whereRaw("(  dt_probation_end_date <= '".$probationToDate."')");
     }
     if(isset($where['notice_period_start_date']) && (!empty($where['notice_period_start_date'])) ){
     	$noticePeriodFromDate = dbDate($where['notice_period_start_date']);
     	$query->whereRaw("(  dt_notice_period_start_date >= '".$noticePeriodFromDate."'  or dt_notice_period_end_date >= '".$noticePeriodFromDate."'  )");
     }
     if(isset($where['notice_period_end_date']) && (!empty($where['notice_period_end_date'])) ){
     	$noticePeriodToDate = dbDate($where['notice_period_end_date']);
     	$query->whereRaw("(  dt_notice_period_start_date <= '".$noticePeriodToDate."'  or dt_notice_period_end_date <= '".$noticePeriodToDate."'  )");
     }
     
     
     if(isset($where['start_working_date']) && (!empty($where['start_working_date'])) ){
     	$startWorkingDate = dbDate($where['start_working_date']);
     	$query->whereRaw("(  dt_notice_period_end_date >= '".$startWorkingDate."' )");
     }
     
     if(isset($where['end_working_date']) && (!empty($where['end_working_date'])) ){
     	$endWorkingDate = dbDate($where['end_working_date']);
     	$query->whereRaw("(  dt_notice_period_end_date <= '".$endWorkingDate."')");
     }
     
     if(isset($where['salary_group']) && (!empty($where['salary_group'])) ){
     	$salaryGroupId = $where['salary_group'];
     	$query->whereHas('salaryInfo' , function($query) use($salaryGroupId) {
     		$query->where('i_salary_group_id',$salaryGroupId);
     	});
     }
     
     
     if(isset($where['deduction_of_pf_status']) && (!empty($where['deduction_of_pf_status'])) ){
     	$deductionPFStatus = $where['deduction_of_pf_status'];
     	$query->whereHas('salaryInfo' , function($query) use($deductionPFStatus) {
     		$query->where('e_pf_deduction',$deductionPFStatus);
     	});
     }
     
     if(isset($where['date_type']) && (!empty($where['date_type'])) ){
     	$dateType = $where['date_type'];
     	$dateSelectionFromDate = ( isset($where['date_selection_from_date']) ? dbDate( $where['date_selection_from_date'] ) : null );
     	$dateSelectionToDate = ( isset($where['date_selection_to_date']) ? dbDate( $where['date_selection_to_date'] ) : null );
     	switch($dateType){
     		case config('constants.LAST_WORKING_DATE'):
     			if( (!empty($dateSelectionFromDate)) && (!empty($dateSelectionToDate)) ){
     				$query->whereIn('e_employment_status', [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS')  ]  );
     				if(!empty($dateSelectionFromDate)){
     					$query->whereRaw("(  date(dt_notice_period_end_date) >= '".$dateSelectionFromDate."' )");
     				}
     				if(!empty($dateSelectionToDate)){
     					$query->whereRaw("(  date(dt_notice_period_end_date) <= '".$dateSelectionToDate."' )");
     				}
     			}
     			break;
     		case config('constants.PF_EXPIRY_DATE'):
     			if( (!empty($dateSelectionFromDate)) && (!empty($dateSelectionToDate)) ){
     				$query->whereIn('e_employment_status', [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS')  ]  );
     				if(!empty($dateSelectionFromDate)){
     					$query->whereRaw("(  dt_pf_expiry_date >= '".$dateSelectionFromDate."' )");
     				}
     				if(!empty($dateSelectionToDate)){
     					$query->whereRaw("(  dt_pf_expiry_date <= '".$dateSelectionToDate."' )");
     				}
     			}
     			break;
     		case config('constants.RESGINATION_DATE'):
     			if( (!empty($dateSelectionFromDate)) && (!empty($dateSelectionToDate)) ){
     				$query->whereIn('e_employment_status', [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS')  ]  );
     				if(!empty($dateSelectionFromDate)){
     					$query->whereHas('latestDisplayResignHistory' , function($query) use($dateSelectionFromDate) {
     						$query->whereRaw("( CASE WHEN e_initiate_type = '".config('constants.EMPLOYEE_INITIATE_EXIT_TYPE')."' THEN date(dt_employee_notice_date) >= '".$dateSelectionFromDate."' When e_initiate_type = '".config('constants.EMPLOYER_INITIATE_EXIT_TYPE')."' THEN date(dt_termination_notice_date) >= '".$dateSelectionFromDate."'  END )");
     					});
     				}
     				if(!empty($dateSelectionToDate)){
     					$query->whereHas('latestDisplayResignHistory' , function($query) use($dateSelectionToDate) {
     						$query->whereRaw("( CASE WHEN e_initiate_type = '".config('constants.EMPLOYEE_INITIATE_EXIT_TYPE')."' THEN date(dt_employee_notice_date) <= '".$dateSelectionToDate."' When e_initiate_type = '".config('constants.EMPLOYER_INITIATE_EXIT_TYPE')."' THEN date(dt_termination_notice_date) <= '".$dateSelectionToDate."'  END )");
     						//$query->whereRaw("(  dt_employee_notice_date <= '".$dateSelectionToDate."' )");
     					});
     				}
     			}
     			break;
     					
     	}
     }
     
     if(isset($where['resignation_type']) && (!empty($where['resignation_type'])) ){
     	
     	$query->whereIn('e_employment_status', [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS')  ]  );
     	$resignationType = $where['resignation_type'];
     	$query->whereHas('latestDisplayResignHistory' , function($query) use($resignationType) {
     		$query->where('e_initiate_type',$resignationType);
     	});
     }
     
     if(isset($where['resign_status']) && (!empty($where['resign_status'])) ){
     	//$query->where('e_employment_status',config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS'));
     	$resignStatus = $where['resign_status'];
     	$query->whereHas('latestDisplayResignHistory' , function($query){
     		$query->where('e_initiate_type',config('constants.EMPLOYEE_INITIATE_EXIT_TYPE'));
     	});
     	$query->whereHas('latestDisplayResignHistory.resignation' , function($query) use($resignStatus) {
     		$query->where('i_id',$resignStatus);
     	});
     }
      
     if(isset($where['terminate_status']) && (!empty($where['terminate_status'])) ){
     	$terminateStatus = $where['terminate_status'];
     	$query->whereHas('latestDisplayResignHistory' , function($query){
     		$query->where('e_initiate_type',config('constants.EMPLOYER_INITIATE_EXIT_TYPE'));
     	});
     	$query->whereHas('latestDisplayResignHistory.termination' , function($query) use($terminateStatus) {
     		$query->where('i_id',$terminateStatus);
     	});
     }
     
     
     if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
		
			$searchString = ( $likeData['searchBy'] );
		
			$allLikeColumns = [ 'v_employee_code', 'v_employee_full_name', 'v_contact_no', 'v_personal_email_id', 'v_outlook_email_id', 'v_employee_name','v_contact_no','v_aadhar_no','v_pan_no','v_bank_account_no','v_bank_account_ifsc_code','v_uan_no','v_current_address_line_first','v_current_address_line_second','v_current_address_pincode','v_permanent_address_line_first','v_permanent_address_line_second','v_permanent_address_pincode' , 'v_education' , 'v_cgpa'];
		
			$query->where(function($q) use ($allLikeColumns,$searchString){
				foreach($allLikeColumns as $key => $allLikeColumn){
					$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
				}
			});
					
		} 
		
		if( isset($likeData['assignto_searchBy']) && (!empty($likeData['assignto_searchBy'])) ){
		
			$searchString = ( $likeData['assignto_searchBy'] );
		
			$allLikeColumns = [ 'v_contact_no', 'v_outlook_email_id'];
		
			$query->where(function($q) use ($allLikeColumns,$searchString){
				foreach($allLikeColumns as $key => $allLikeColumn){
					$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
				}
			});
					
		}
		if( isset($where['where_not_null']) && !empty($where['where_not_null']) ){
			foreach($where['where_not_null']  as  $key => $value){
				$query->whereNotNull($value);
				 
			}
		}
		if( ( isset($where['employee_leader_name']) && !empty($where['employee_leader_name'] ) ) || ( isset($where['employee_login_id']) && !empty($where['employee_login_id']) ) ){
			$employeeLeaderId = $where['employee_leader_name'];
			$employeeLoginId = $where['employee_login_id'];
			$query->orWhere('i_leader_id',$employeeLeaderId);
			$query->orWhere('i_login_id',$employeeLoginId);
			
		}
		##employment relieved status 
		if(isset($where['employment_relieved_status']) && (!empty($where['employment_relieved_status'])) ){
			$employmentRelievedStatus = $where['employment_relieved_status'];
			$query->whereNotIn('e_employment_status',$employmentRelievedStatus);
		}
		
		if(isset($where['hold_salary_status']) && (!empty($where['hold_salary_status'])) ){
			$query->where('e_hold_salary_status',$where['hold_salary_status']);
		}
		
		if(isset($where['employee_assign_role']) && (!empty($where['employee_assign_role'])) ){
			$query->where('i_role_permission',$where['employee_assign_role'] );
		}
		
		if(isset($where['role_assign_user']) && (!empty($where['role_assign_user'])) ){
			$roleRecordId = $where['role_assign_user'];
			$query->where(function($q1) use ($roleRecordId){
				$q1->orWhere('i_role_permission', $roleRecordId);
				$q1->orWhereRaw('i_role_permission is null');
			});
		}
		
		if( isset($where ['order_by']) ){
			if(!empty($where ['order_by'])){
				foreach($where ['order_by'] as  $key => $value){
					if( $key == "v_employee_code" ){
						$query->orderByRaw('CONVERT(v_employee_code, SIGNED) ' . (!empty($value) ? $value : 'DESC' ) );
					} else {
						$query->orderBy($key, (!empty($value) ? $value : 'DESC' ) );
					}
					
				}
			}
		} else {
			$query->orderBy('i_id', "DESC" ) ;
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
			$data = $query->get();
		}
			
		return $data;
  } 
    
    
  public function academicSavedLeaveInfo(){
  	return $this->hasMany(AcademicSavedLeaveInfo::class , 'i_employee_id');
  }
}
