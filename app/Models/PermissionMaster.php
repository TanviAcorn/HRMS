<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;

class PermissionMaster extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.PERMISSION_MASTER_TABLE');
    }
    
    public function groupName(){
    	return $this->belongsTo(PermissionGroup::class , 'i_group_id');
    }
    
}
