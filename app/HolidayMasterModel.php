<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
class HolidayMasterModel extends BaseModel
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
		$this->table = config('constants.HOLIDAY_MASTER_TABLE');
	}
	public function  getRecordDetails( $where = [] , $likeData = [] ){
	
		$query = HolidayMasterModel::where('t_is_deleted' , 0 );
		 
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			$query->where('i_id','=',$masterRecordId);
		}
		
		
		if(isset($where['holiday_from_date']) && (!empty($where['holiday_from_date'])) ){
			$fromDate = dbDate ( $where['holiday_from_date'] );
			$query->whereRaw("(  dt_holiday_date >= '".$fromDate."' )");
			//$query->where('dt_holiday_date','>=',dbDate($holidayFromDate));
		}
		if(isset($where['holiday_to_date']) && (!empty($where['holiday_to_date'])) ){
			$toDate = dbDate ( $where['holiday_to_date'] );
			$query->whereRaw("(  dt_holiday_date <= '".$toDate."')");
			//$query->where('dt_holiday_date','<=',dbDate($holidayToDate));
		}
		if(isset($where['active_status'])){
			$activeStatus = $where['active_status'];
			$query->where('t_is_active',$activeStatus);
		}
		if(isset($where['holiday_date']) && (!empty($where['holiday_date'])) ){
			$holidayDate = $where['holiday_date'];
			if(is_array($holidayDate)){
				$query->whereIn('dt_holiday_date',$holidayDate);
			}
		}
		if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
			 
			$searchString = ( $likeData['searchBy'] );
			 
			$allLikeColumns = [ 'v_holiday_name' ];
			 
			$query->where(function($q) use ($allLikeColumns,$searchString){
				foreach($allLikeColumns as $key => $allLikeColumn){
					$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
				}
			});
				 
		}
		if((!empty($where)) && array_key_exists('order_by', $where)){
			$orderByColumn = $where['order_by'];
				
			if(!empty($orderByColumn)){
				foreach($orderByColumn as  $key => $value){
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
