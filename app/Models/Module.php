<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;

class Module extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.MODULE_MASTER_TABLE');
    }
    
    public function modulePermissionGroup(){
    	return $this->hasMany(PermissionGroup::class , 'i_module_id');
    }
    
    public function modulePermission(){
    	return $this->hasManyThrough( PermissionMaster::class  , PermissionGroup::class , 'i_module_id' , 'i_group_id');
    }
    
    public function menuMasterInfo(){
    	return $this->belongsTo(MenuModel::class,'i_menu_id');
    }
    public function getModuleDetails($where = [] , $likeData = [] ){
    	$data = MenuModel::with(['moduleInfo', 'moduleInfo.modulePermissionGroup', 'moduleInfo.modulePermissionGroup.groupPermission'])->where('t_is_deleted' , 0 )->get();
		return $data;
    }
}
