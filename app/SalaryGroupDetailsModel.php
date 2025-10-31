<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use App\SalaryComponentsModel;
use App\SalaryGroupModel;

class SalaryGroupDetailsModel extends BaseModel
{
	use MySoftDeletes;
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.SALARY_GROUP_COMPONENTS_DETAILS_TABLE');
	}
	public function salaryGroupDetails(){
		return $this->belongsTo(SalaryGroupModel::class,'i_salary_group_id');
	}
	public function salaryComponentInfo(){
		return $this->belongsTo(SalaryComponentsModel::class,'i_salary_components_id')->where('t_is_deleted' , 0 )->orderBy('i_sequence','asc');
	}
	
}
