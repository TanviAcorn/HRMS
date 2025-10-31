<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\EmployeeModel;
use App\Traits\MySoftDeletes;
class OnHoldSalaryModel extends BaseModel
{
	use MySoftDeletes;
	
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.EMPLOYEE_HOLD_SALARY_INFO');
	}
	
	public function onHoldSalaryMaster(){
	 	return $this->belongsTo(EmployeeModel::class , 'i_employee_id' );
	} 
}
