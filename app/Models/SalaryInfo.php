<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Awobaz\Compoships\Compoships;
use App\SalaryComponentsModel;

class SalaryInfo extends BaseModel
{
	use HasFactory,SoftDeletes,Compoships;
    const CREATED_AT = 'dt_created_at';
    const UPDATED_AT = 'dt_updated_at';
    const DELETED_AT = 'dt_deleted_at';
    
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.SALARY_INFO_TABLE');
    }
    
    
	public function generateSalaryComponent(){
    	return $this->belongsTo(SalaryComponentsModel::class , 'i_component_id' );
    }
}
