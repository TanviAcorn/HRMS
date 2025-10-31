<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GhanuZ\FindInSet\FindInSetRelationTrait;
class RolePermission extends BaseModel
{
	use HasFactory,SoftDeletes;
	use FindInSetRelationTrait;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.ROLE_PERMISSION_TABLE');
		$this->perPage = config ( 'constants.PER_PAGE' );
	}
	public function permissionMasterInfo(){
		return $this->FindInSetMany(PermissionMaster::class , 'v_permission_ids' , 'i_id' );
	}
	public function getRecordDetails($where = [], $likeData = []){
		
		$query = RolePermission::with(['permissionMasterInfo'])->where('t_is_deleted',0);
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			$query->where('i_id','=',$masterRecordId);
		}
		
		if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
		
			$searchString = ( $likeData['searchBy'] );
		
			$allLikeColumns = [ 'v_role_name','v_role_description' ];
		
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
}
