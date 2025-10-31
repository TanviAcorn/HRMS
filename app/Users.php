<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use DB;
use App\Traits\MySoftDeletes;
use Illuminate\Support\Facades\Config;

class Users extends BaseModel
{
    //
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected  $table = 'team_master';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	protected $softDelete = true;
	
	
}
