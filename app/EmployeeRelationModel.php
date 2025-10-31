<?php
namespace App;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
class EmployeeRelationModel extends BaseModel
{
	use MySoftDeletes;
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.EMPLOYEE_RELATION_DETAILS_TABLE');
		
	}
	public function employeeRelationInfo(){
		return $this->belongsTo(EmployeeModel::class,'i_employee_id');
	}
}
