<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;

class PermissionGroup extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.PERMISSION_GROUP_TABLE');
    }
    
    public function groupPermission(){
    	return $this->hasMany(PermissionMaster::class , 'i_group_id');
    }
    
    public function groupModuleInfo(){
    	return $this->belongsTo(Module::class , 'i_module_id');
    }
    
    
}
