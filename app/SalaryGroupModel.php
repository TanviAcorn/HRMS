<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;
class SalaryGroupModel extends BaseModel
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
		$this->table = config('constants.SALARY_GROUP_MASTER_TABLE');
	}
	public function salaryGroupDetails(){
		return $this->hasMany(SalaryGroupDetailsModel::class,'i_salary_group_id');
	}
	public function getRecordDetails( $where = [] , $likeData = [] ){
	
		//$query = SalaryGroupModel::where('t_is_deleted' , 0 );
		$query = SalaryGroupModel::with(['salaryGroupDetails']);
		
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			$query->where('i_id','=',$masterRecordId);
		}
		if(isset($where['active_status'])){
			$activeStatus = $where['active_status'];
			$query->where('t_is_active',$activeStatus);
		}
		if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
	
			$searchString = ( $likeData['searchBy'] );
	
			$allLikeColumns = [ 'v_group_name','v_group_description' ];
	
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
