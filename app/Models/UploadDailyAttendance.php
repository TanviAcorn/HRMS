<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;

class UploadDailyAttendance extends BaseModel
{
    use HasFactory,SoftDeletes;
   	protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.UPLOAD_DAILY_ATTENDANCE_TABLE');
    }
    
    
    public function getRecordDetails( $where = [] , $likeData = [] , $additionalData = []  ){
    
    	$query = UploadDailyAttendance::query();
    	
    	
    	if(isset($where['search_start_date']) && (!empty($where['search_start_date'])) ){
    		$startDate = ( $where['search_start_date'] );
    		$query->whereRaw("(  dt_attendance_date >= '".$startDate."' )");
    	}
    	
    	if(isset($where['search_end_date']) && (!empty($where['search_end_date'])) ){
    		$endDate = ( $where['search_end_date'] );
    		$query->whereRaw("(  dt_attendance_date <= '".$endDate."' )");
    	}
    	
    	if(isset($where['search_summary']) && (!empty($where['search_summary'])) ){
    		$query->groupBy("dt_attendance_date");
    	}
    	
    	if( isset($where['offset']) ){
    		$query->skip($where ['offset']);
    	}
    	
    	if( isset($where['limit']) ){
    		$query->take($where['limit']);
    	}
    	
    	if( isset($where['singleRecord']) && ( $where['singleRecord'] != false ) ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	 
    	return $data;
    	
    }
    
    
    
    
}
