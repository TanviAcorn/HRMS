<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
class MenuModel extends BaseModel
{
	use HasFactory,SoftDeletes;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.MENU_MASTER_TABLE');
	}
	public function moduleInfo(){
		return $this->hasMany(Module::class,'i_menu_id');
	}
}
