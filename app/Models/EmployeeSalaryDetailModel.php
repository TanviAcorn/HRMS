<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use App\SalaryComponentsModel;

class EmployeeSalaryDetailModel extends BaseModel
{
	use HasFactory,MySoftDeletes;
    const CREATED_AT = 'dt_created_at';
    const UPDATED_AT = 'dt_updated_at';
    const DELETED_AT = 'dt_deleted_at';
    
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.EMPLOYEE_SALARY_DETAIL_TABLE');
    }
    
    public function salaryComponentInfo(){
    	return $this->belongsTo(SalaryComponentsModel::class , 'i_salary_component_id');
    }
    
    public function employeeSalaryMaster(){
    	return $this->belongsTo(EmployeeSalaryModel::class , 'i_employee_salary_id');
    }
}
