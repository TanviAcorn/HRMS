<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MySoftDeletes;

class Login extends Model
{
	use MySoftDeletes;
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected  $table = 'login_master';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	protected $softDelete = true;
}
