<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use App\EmployeeModel;
use App\LeaveTypeMasterModel;

class LeaveBalanceModel extends BaseModel
{
    use HasFactory,MySoftDeletes;
    const CREATED_AT = 'dt_created_at';
    const UPDATED_AT = 'dt_updated_at';
    const DELETED_AT = 'dt_deleted_at';
    
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.LEAVE_BALANCE_TABLE');
    }
    
    public function employeeLeaveBalance(){
    	return $this->belongsTo(EmployeeModel::class,'i_employee_id');
    }
    
    public function leaveType(){
    	return $this->belongsTo(LeaveTypeMasterModel::class,'i_leave_type_id');
    }
    
    public function getRecordDetails( $where = [] , $likeData = [] , $additionalData = [] ){
    	$query = LeaveBalanceModel::with(['employeeLeaveBalance' , 'leaveType']);
    
    	if(isset($where['i_employee_id']) && (!empty($where['i_employee_id'])) ){
    		$query->where('i_employee_id','=',$where['i_employee_id']);
    	}
    	
    	
    	if(isset($where['i_leave_type_id']) && (!empty($where['i_leave_type_id'])) ){
    		$query->where('i_leave_type_id','=',$where['i_leave_type_id']);
    	}
    	
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$masterRecordId = $where['master_id'];
    		$query->where('i_id','=',$masterRecordId);
    	}
    	if(isset($where['active_status'])){
    		$activeStatus = $where['active_status'];
    		$query->where('t_is_active',$activeStatus);
    	}
    	if(isset($where['state_id']) && (!empty($where['state_id'])) ){
    		$stateMasterRecordId = $where['state_id'];
    		$query->where('i_state_id',$stateMasterRecordId);
    	}
    
    	if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    
    		$searchString = ( $likeData['searchBy'] );
    
    		$allLikeColumns = [ 'v_city_name' ];
    
    		$query->where(function($q) use ($allLikeColumns,$searchString){
    			foreach($allLikeColumns as $key => $allLikeColumn){
    				$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    			}
    		});
    				
    	}
    	if( isset($where ['order_by']) ){
			if(!empty($where ['order_by'])){
				foreach($where ['order_by'] as  $key => $value){
					$query->orderBy($key, (!empty($value) ? $value : 'DESC' ) );
					
				}
			}
		} else {
			$query->orderBy('i_id', "DESC" ) ;
		}
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
