<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;use App\BaseModel;
use App\Traits\MySoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\CityMasterModel;
use App\EmployeeModel;
class VillageMasterModel extends BaseModel{
    use MySoftDeletes;
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.VILLAGE_MASTER_TABLE');
	}
	
	public function cityMaster(){
    	return $this->belongsTo(CityMasterModel::class , 'i_city_id');
    }
    public function employeeCurrentVillage(){
    	return $this->hasMany(EmployeeModel::class,'i_current_village_id');
    }
    
    public function employeePermentVillage(){
    	return $this->hasMany(EmployeeModel::class,'i_permanent_village_id');
    }
	public function getRecordDetails( $where = [] , $likeData = [] , $additionalData = [] ){
		$query = VillageMasterModel::with(['cityMaster.stateMaster']);
		
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			$query->where('i_id','=',$masterRecordId);
		} else {
			if(isset($where['unique_master_id']) && (!empty($where['unique_master_id'])) ){
				$masterRecordId = $where['unique_master_id'];
				$query->where('i_id','!=',$masterRecordId);
			}
		}
		if(isset($where['active_status'])){
			$activeStatus = $where['active_status'];
			$query->where('t_is_active',$activeStatus);
		}
		if(isset($where['city_id']) && (!empty($where['city_id'])) ){
			$cityMasterRecordId = $where['city_id'];
			$query->where('i_city_id',$cityMasterRecordId);
		}
		if(isset($where['state_id']) && (!empty($where['state_id'])) ){
			$stateId = $where['state_id'];
			$query->whereHas('cityMaster' , function($query) use($stateId) {
				$query->where('i_state_id',$stateId);
			});
		}
		
		if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
			$searchString = ( $likeData['searchBy'] );
			$allLikeColumns = [ 'v_village_name' ];
			$query->where(function($q) use ($allLikeColumns,$searchString){
				foreach($allLikeColumns as $key => $allLikeColumn){
					$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
				}
			});
					
		} else {
			if(isset($where['unique_village_name']) && (!empty($where['unique_village_name'])) ){
				$villageName = $where['unique_village_name'];
				$query->where('v_village_name',$villageName);
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
