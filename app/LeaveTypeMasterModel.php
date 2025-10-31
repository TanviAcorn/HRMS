<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use App\Models\LeaveSummaryModel;
class LeaveTypeMasterModel extends BaseModel
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
		$this->table = config('constants.LEAVE_TYPE_MASTER_TABLE');
	}
	public function myleaveInfo(){
		return $this->hasMany(MyLeaveModel::class,'i_leave_type_id');
	}
	public function leaveSummaryInfo(){
		return $this->hasMany(LeaveSummaryModel::class,'i_leave_type_id');
	}
}

