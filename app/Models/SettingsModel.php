<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
class SettingsModel extends BaseModel{
	protected $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.SETTING_TABLE');
    }
    public function getRecordDetails($where = [] , $likeData = [] , $additionalData = []){
    	$query = SettingsModel::where('t_is_deleted',0);
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	return  $data;
    }
}
