<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use App\EmployeeModel;
use App\LeaveTypeMasterModel;
use DB;


class LeaveAssignHistoryModel extends BaseModel
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
    	$this->table = config('constants.LEAVE_ASSIGN_HISTORY_TABLE');
    }
    
    public function employee(){
    	return $this->belongsTo(EmployeeModel::class,'i_employee_id');
    }
    
    public function leaveType(){
    	return $this->belongsTo(LeaveTypeMasterModel::class,'i_leave_type_id');
    }
    
    public function getRecordDetails( $where = [] , $likeData = [] , $additionalData = [] ){
    	$query = LeaveAssignHistoryModel::with(['employee' , 'leaveType']);
    	$query->select( [ '*' , DB::raw('dt_effective_date as compare_date') , DB::raw('dt_created_at as action_date') ,  DB::raw('"assign_leave" as record_type') , DB::raw('v_remark as v_leave_note') ] );
    	if(isset($where['i_employee_id']) && (!empty($where['i_employee_id'])) ){
    		$query->where('i_employee_id','=',$where['i_employee_id']);
    	}
    	
    	if(isset($where['i_leave_type_id']) && (!empty($where['i_leave_type_id'])) ){
    		$query->where('i_leave_type_id','=',$where['i_leave_type_id']);
    	}
    	
    	if(isset($where['t_is_used_status']) && (strlen($where['t_is_used_status']) > 0 )  ){
    		$query->where('t_is_used_status','=',$where['t_is_used_status']);
    	}
    	
    	if(isset($where['leave_from_date']) && (!empty($where['leave_from_date'])) ){
    		$fromDate = dbDate ( $where['leave_from_date'] );
    		$query->whereRaw("(  dt_effective_date >= '".$fromDate."'  or dt_effective_date >= '".$fromDate."'  )");
    	}
    	if(isset($where['leave_to_date']) && (!empty($where['leave_to_date'])) ){
    		$toDate = dbDate ( $where['leave_to_date'] );
    		$query->whereRaw("(  dt_effective_date <= '".$toDate."'  or dt_effective_date <= '".$toDate."'  )");
    	}
    	
    	if((!empty($where)) && array_key_exists('custom_function', $where)){
    		$customerFunctionWhere = $where['custom_function'];
    		if(!empty($customerFunctionWhere)){
    			if(is_array($customerFunctionWhere)){
    				foreach($customerFunctionWhere as $key => $customerFunction){
    					$query->whereRaw( $customerFunction );
    				}
    			} else {
    				$query->whereRaw( $customerFunctionWhere);
    			}
    		}
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
