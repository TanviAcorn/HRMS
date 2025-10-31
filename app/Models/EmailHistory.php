<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Login;

class EmailHistory extends BaseModel
{
	use HasFactory,SoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.EMAIL_HISTORY_TABLE');
    }
    public function loginInfo(){
    	return $this->belongsTo(Login::class , 'i_login_id');
    }
}
