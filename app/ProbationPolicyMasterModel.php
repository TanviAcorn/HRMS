<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\MySoftDeletes;
class ProbationPolicyMasterModel extends BaseModel
{
	use MySoftDeletes;
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.PROBATION_POLICY_MASTER_TABLE');
    }
    public function getRecordDetails( $where = [] , $likeData = [] ){
    
    	$query = ProbationPolicyMasterModel::where('t_is_deleted' , 0 );
    	
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$masterRecordId = $where['master_id'];
    		$query->where('i_id','=',$masterRecordId);
    	}
    	if(isset($where['record_status']) && (!empty($where['record_status'])) ){
    		$recordStatus = $where['record_status'];
    		$query->where('e_record_status',$recordStatus);
    	}
    	if(isset($where['months_weeks_days']) && (!empty($where['months_weeks_days'])) ){
    		$monthsWeeksDays = $where['months_weeks_days'];
    		$query->where('e_months_weeks_days',$monthsWeeksDays);
    	}
    	if(isset($where['active_status'])){
    		$activeStatus = $where['active_status'];
    		$query->where('t_is_active',$activeStatus);
    	}
    	if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    		 
    		$searchString = ( $likeData['searchBy'] );
    		 
    		$allLikeColumns = [ 'v_probation_policy_name' , 'v_probation_policy_description', 'v_probation_period_duration' ];
    		 
    		$query->where(function($q) use ($allLikeColumns,$searchString){
    			foreach($allLikeColumns as $key => $allLikeColumn){
    				$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    			}
    		});
    	
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
    
    public function employeeProbationPeriodInfo(){
    	return $this->hasMany(EmployeeModel::class , 'i_probation_period_id');
    }
    
}
