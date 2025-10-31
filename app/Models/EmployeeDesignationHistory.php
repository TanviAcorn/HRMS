<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\EmployeeModel;
use App\LookupMaster;
use App\WeeklyOffMasterModel;
use App\ShiftMasterModel;

class EmployeeDesignationHistory extends BaseModel
{
    use HasFactory,SoftDeletes;
    const CREATED_AT = 'dt_created_at';
    const UPDATED_AT = 'dt_updated_at';
    const DELETED_AT = 'dt_deleted_at';
    
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.EMPLOYEE_DESIGNATION_HISTORY');
    }
    
    public function employeeInfo(){
    	return $this->belongsTo(EmployeeModel::class, 'i_employee_id');
    }
    
    public function designationInfo(){
    	return $this->belongsTo(LookupMaster::class, 'i_designation_id');
    }
    
    public function teamInfo(){
    	return $this->belongsTo(LookupMaster::class, 'i_designation_id');
    }
    
    public function shiftInfo(){
    	return $this->belongsTo(ShiftMasterModel::class, 'i_designation_id');
    }
    
    public function weeklyOffInfo(){
    	return $this->belongsTo(WeeklyOffMasterModel::class, 'i_designation_id');
    }
}
