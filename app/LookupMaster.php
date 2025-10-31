<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use DB;
use App\Traits\MySoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;

class LookupMaster extends BaseModel
{
    //
    use SoftDeletes;
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected  $table = 'lookup_master';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	protected $softDelete = true;
	
	public function getRecordDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
			
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		} else {
			$this->singleRecord = false;
		}
	
		$defaultWhere = [];
		$defaultWhere['lm.t_is_deleted != ' ] = 1;
	
		$tableName = config('constants.LOOKUP_MASTER_TABLE'). ' as lm';
			
		$selectData = [
				'lm.i_id',
				'lm.v_value',
				'lm.v_module_name',
				'lm.t_is_active',
				'lm.v_chart_color',
				'lm.i_sequence',
		];
		
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
		//DB::enableQueryLog();
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordById( $tableName, $selectData,  $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectData( $tableName, $selectData,   $whereData, $likeData, $additionalData );
		}
	
		//$query = DB::getQueryLog();
		//$query = end($query);
		//print_r($query);die;
			
		return $data;
			
	}
	
	public function employeeDesignationInfo(){
		return $this->hasMany(EmployeeModel::class , 'i_designation_id');
	}
	
	public function employeeTeamInfo(){
		return $this->hasMany(EmployeeModel::class , 'i_team_id');
	}
	
	/* public function employeeLeaderInfo(){
		return $this->hasMany(EmployeeModel::class , 'i_leader_id');
	} */
	
	public function employeeRecruitmentSourceInfo(){
		return $this->hasMany(EmployeeModel::class , 'i_recruitment_source_id');
	}
	
	public function employeeNoticePeriodInfo(){
		return $this->hasMany(EmployeeModel::class , 'i_notice_period_id');
	}
	
	public function employeeBankInfo(){
		return $this->hasMany(EmployeeModel::class , 'i_bank_id');
	}
	
}
