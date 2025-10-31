<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;
use App\SalaryComponentsModel;

class ReviseSalaryInfo extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.REVISE_SALARY_INFO_TABLE');
    }
    
    public function reviseSalaryMaster(){
    	return $this->belongsTo(ReviseSalaryMaster::class , 'i_employee_revise_salary_id');
    }
    
    public function assignSalaryComponent(){
    	return $this->belongsTo(SalaryComponentsModel::class , 'i_salary_component_id' );
    }
    
}
