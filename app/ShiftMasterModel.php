<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class ShiftMasterModel extends BaseModel
{
	use SoftDeletes;
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.SHIFT_MASTER_TABLE');
	}
   
	function shiftTimingInfo(){
		return $this->hasMany(ShiftTimingModel::class,'i_shift_master_id');
	}
	public function getRecordDetails( $where = [] , $likeData = [] ){
	
		$query = ShiftMasterModel::with(['shiftTimingInfo'])->where('t_is_deleted',0);
		
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			$query->where('i_id','=',$masterRecordId);
		}
		if(isset($where['shift_type']) && (!empty($where['shift_type'])) ){
			$shiftTypeRecord = $where['shift_type'];
			$query->where('e_shift_type',$shiftTypeRecord);
		}
		if(isset($where['active_status'])){
			$activeStatus = $where['active_status'];
			$query->where('t_is_active',$activeStatus);
		}
		if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
		
			$searchString = ( $likeData['searchBy'] );
		
			$allLikeColumns = [ 'v_shift_name', 'v_shift_code', 'v_description'];
		
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
		//$query->orderBy('i_id', "DESC" ) ;
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
