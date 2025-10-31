<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\EmployeeModel;
use App\Models\AttendanceSummaryModel;
use Awobaz\Compoships\Compoships;

class Salary extends BaseModel
{
    use HasFactory,SoftDeletes,Compoships;
    
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.SALARY_MASTER_TABLE');
    }
    
    /* public function salaryMaster(){
    	return $this->belongsTo(AttendanceSummaryModel::class , 'dt_salary_month' , 'dt_month' );
    } */
    
    public function employeeSalary(){
    	return $this->belongsTo(AttendanceSummaryModel::class , 'i_attendance_summary_id' );
    }
    
    public function generatedSalaryInfo(){
    	return $this->hasMany(SalaryInfo::class , 'i_salary_master_id' );
    }
    
    public function employee(){
    	return $this->belongsTo(EmployeeModel::class , 'i_employee_id' );
    }
    
    public function getRecordDetails( $where = [] , $likeData = [] , $additionalData = [] ){
    	
    	$query = AttendanceSummaryModel::with( [ 'attendanceSalary' , 'attendanceSalary.generatedSalaryInfo' ,  'employeeAttendance' , 'employeeAttendance.salaryInfo'  ,  'employeeAttendance.salaryDetail' ,  'employeeAttendance.teamInfo' , 'employeeAttendance.designationInfo' , 'employeeAttendance.onHoldSalaryInfo' , 'employeeAttendance.generatedSalaryMaster' , 'employeeAttendance.generatedSalaryMaster.generatedSalaryInfo' , 'employeeAttendance.generatedSalaryMaster.generatedSalaryInfo.generateSalaryComponent' ]);
    	$query->whereHas('employeeAttendance.salaryInfo');
    	
    	if( isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$recordId = $where['master_id'];
    		if( is_array( $recordId ) ) {
    			$query->whereIn('i_id',$recordId);
    		} else {
    			$query->where('i_id',$recordId);
    		}
    	}
    	
    	if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
    		$employmentStatus = $where['employment_status'];
    		$query->whereHas('employeeAttendance' , function($query) use($employmentStatus) {
    			if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
    				$query->whereIn('e_employment_status',[config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')]);
    			}else {
    				$query->where('e_employment_status',$employmentStatus);
    			}
    		});
    	}
    	 
    	if(isset($where['employment_relieved_status']) && (!empty($where['employment_relieved_status'])) ){
    		$employmentRelievedStatus = $where['employment_relieved_status'];
    		$query->whereHas('employeeAttendance' , function($query) use($employmentRelievedStatus) {
    			$query->whereNotIn('e_employment_status',$employmentRelievedStatus);
    		});
    	}
    	
    	if( isset($where['salary_month']) && (!empty($where['salary_month'])) ){
    		$fromDate = dbDate ( $where['salary_month'] );
    		$query->whereRaw("(  dt_month = '".$fromDate."' )");
    	}
    	
    	if( isset($where['start_month']) && (!empty($where['start_month'])) ){
    		$fromDate = dbDate ( $where['start_month'] );
    		$query->whereRaw("(  dt_month >= '".$fromDate."')");
    	}
    	
    	if( isset($where['end_month']) && (!empty($where['end_month'])) ){
    		$toDate = dbDate ( $where['end_month'] );
    		$query->whereRaw("(  dt_month <= '".$toDate."')");
    	}
    	
    	/* if( isset($where['employee']) && (!empty($where['employee'])) ){
    		$employeeId = (int)$where['employee'];
    		$query->where('i_employee_id',$employeeId);
    	} */
    	
    	if( isset($where['designation']) && (!empty($where['designation'])) ){
    		$designationId = (int)$where['designation'];
    		$query->whereHas('employeeAttendance' , function($query) use($designationId) {
    		 	$query->where('i_designation_id',$designationId);
    		}); 
    	}
    	
    	if( isset($where['team']) && (!empty($where['team'])) ){
    		$teamId = (int)$where['team'];
    		$query->whereHas('employeeAttendance' , function($query) use($teamId) {
    			$query->where('i_team_id',$teamId);
    		});
    	}
    	
    	if( isset($where['salary_generate_status'])){
    		$salaryGenerateStatus = $where['salary_generate_status'];
    		if( $salaryGenerateStatus == 1 ){
    			$query->whereHas('attendanceSalary' , function($query) use($salaryGenerateStatus) {
    				$query->where('t_is_salary_generated',$salaryGenerateStatus);
    			});
    		} else {
    			
    			$query->doesntHave('attendanceSalary');
    		}
    	}
    	
    	if(session()->get('role') == config('constants.ROLE_USER')){
    		if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
    			unset($where['show_all']);
    		} else {
    			$employeeId = session()->get('user_employee_id');
    			$query->whereHas('employeeAttendance' , function($query) use($employeeId) {
    				//$query->whereRaw( "( i_id = '".$employeeId."' or i_leader_id = '".$employeeId."' ) " );
    				
    				$allChildEmployeeIds = $this->childEmployeeIds();
    				if(!empty($allChildEmployeeIds)){
    					$query->whereIn('i_id', $allChildEmployeeIds);
    				}
    			});
    		}
    	}
    	
    	if(isset($where['employee']) && (!empty($where['employee'])) ){
    		$employeeId = $where['employee'];
    		$query->whereHas('employeeAttendance' , function($query) use($employeeId) {
    			if( is_array($employeeId) ){
    				$query->whereIn('i_id',$employeeId);
    			} else {
    				$query->where('i_id',$employeeId);
    			}
    		});
    	}
    	
    	if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    		 
    		$searchString = ( $likeData['searchBy'] );
    	
    		$query->where(function ($q1) use($searchString){
    			$q1->whereHas('employeeAttendance' , function($query) use($searchString) {
    				$allLikeColumns = [ 'v_bank_account_no' ];
    	
    				$query->where(function($q) use ($allLikeColumns,$searchString){
    					foreach($allLikeColumns as $key => $allLikeColumn){
    						$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    					}
    				});
    			});
    		});
    	}
    	
    	//$query->where('t_is_active' ,1);
    	
    	$query->orderBy('i_id', "DESC" ) ;
    	 
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	 
    	return $data;
    }
    
    public function getSalaryRecordDetails( $where = [] , $likeData = [] ){
    
    	$query = Salary::with( [ 'generatedSalaryInfo' , 'generatedSalaryInfo.generateSalaryComponent' ,  'employee' , 'employee.designationInfo' , 'employee.teamInfo' , 'employee.bankInfo'  ] );
    	$query->where('t_is_salary_generated' , 1 );
    	 
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$recordId = $where['master_id'];
    		$query->where('i_id',$recordId);
    	}
    	
    	if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
    		$query->where('i_employee_id',$where['employee_id']);
    	}
    	
    	if(isset($where['salary_start_month']) && (!empty($where['salary_start_month'])) ){
    		$startDate = dbDate($where['salary_start_month']);
    		$query->whereRaw("(  dt_salary_month >= '".$startDate."')");
    	}
    	 
    	if(isset($where['salary_end_month']) && (!empty($where['salary_end_month'])) ){
    		$endDate = dbDate($where['salary_end_month']);
    		$query->whereRaw("(  dt_salary_month <= '".$endDate."')");
    	}
    
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	return $data;
    
    }
    
}
