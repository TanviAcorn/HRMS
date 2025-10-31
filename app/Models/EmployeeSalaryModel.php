<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use App\SalaryGroupModel;

class EmployeeSalaryModel extends BaseModel
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
    	$this->table = config('constants.EMPLOYEE_SALARY_MASTER_TABLE');
    }
    
    public function salaryGroup(){
    	return $this->belongsTo(SalaryGroupModel::class , 'i_salary_group_id');
    }
}
