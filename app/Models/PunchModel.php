<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;
use App\EmployeeModel;

class PunchModel extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.PUNCH_INFO_TABLE');
    }
    
    public function punchEmployee(){
    	return $this->belongsTo(EmployeeModel::class , 'i_employee_id');
    }
}
