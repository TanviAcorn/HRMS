<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\EmployeeModel;
use App\LookupMaster;
use App\Login;

class EmployeeResignHistory extends BaseModel
{
    use HasFactory,SoftDeletes;
    const CREATED_AT = 'dt_created_at';
    const UPDATED_AT = 'dt_updated_at';
    const DELETED_AT = 'dt_deleted_at';
    
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.EMPLOYEE_RESIGN_HISTORY');
    }
    
    public function employee(){
    	return $this->belongsTo(EmployeeModel::class , 'i_employee_id');
    }
    
    public function resignation(){
    	return $this->belongsTo(LookupMaster::class , 'i_resign_reason_id');
    }
    
    public function termination(){
    	return $this->belongsTo(LookupMaster::class , 'i_termination_reason_id');
    }
    
    public function approveEmployeeInfo(){
    	return $this->belongsTo(Login::class , 'i_approved_by_id');
    }
    
    public function getRecordDetails( $where = [] , $likeData = [] ){
    
    	$query = EmployeeResignHistory::with(['employee' , 'resignation' , 'termination' ])->where('t_is_deleted' , 0 );
    		
    	if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
    		$recordId = $where['employee_id'];
    		$query->where('i_employee_id','=',$recordId);
    	}
    	
    	$query->orderBy('i_id', "DESC" ) ;
    	
    	if( isset($where['offset']) ){
    		$query->skip($where ['offset']);
    	}
    
    	if( isset($where['limit']) ){
    		$query->take($where['limit']);
    	}
    	
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    		
    	return $data;
    
    }
    
    
}
