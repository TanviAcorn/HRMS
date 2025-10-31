<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use App\EmployeeModel;
use Awobaz\Compoships\Compoships;

class AttendanceSummaryModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
	use Compoships;
    const CREATED_AT = 'dt_created_at';
    const UPDATED_AT = 'dt_updated_at';
    const DELETED_AT = 'dt_deleted_at';
    
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.ATTENDANCE_SUMMARY_TABLE');
    }
    
    public function attendanceSalary(){
    	return $this->hasOne(Salary::class , [ 'dt_salary_month'  , 'i_employee_id' ]  ,  [ 'dt_month'  , 'i_employee_id' ] );
    }
    
    public function employeeAttendance(){
    	return $this->belongsTo(EmployeeModel::class , 'i_employee_id');
    }
    
   
} 
