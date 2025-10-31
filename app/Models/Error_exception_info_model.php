<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;

class Error_exception_info_model extends BaseModel
{
	use HasFactory,SoftDeletes;	
    protected $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    protected $softDelete = true;
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.ERROR_EXCEPTION_INFO_TABLE');
    }
}
