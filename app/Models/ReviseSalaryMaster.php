<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;
use App\EmployeeModel;

class ReviseSalaryMaster extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.REVISE_SALARY_MASTER_TABLE');
    }
    
    /* public function assignSalaryInfo(){
    	return $this->belongsTo(EmployeeModel::class , 'i_employee_id' );
    } */
    
    public function assignSalaryInfo(){
    	return $this->hasMany(ReviseSalaryInfo::class , 'i_employee_revise_salary_id' );
    }
    
    
}
