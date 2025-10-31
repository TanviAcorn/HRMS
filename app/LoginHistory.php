<?php

namespace App;

use App\BaseModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class LoginHistory extends BaseModel
{
    //
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected $table = 'login_history';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	protected $softDelete = true;
	
	public function getRecordDetail( $whereData = [] , $likeData = [] , $additionalData = [] ){
		
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		}
			
		$defaultWhere = [];
		$defaultWhere['lh.t_is_deleted != ' ] = 1 ;
		$defaultWhere['lm.t_is_deleted != ' ] = 1 ;
		$defaultWhere['order_by'] = ['lh.dt_created_at'=>'desc'];
		
		if(session()->get('role') == config("constants.ROLE_ADMIN")){
			$whereData['custom_function'][] = " 1=1 OR lm.v_role = '".config("constants.ROLE_ADMIN")."' ";
		}
		
		$tableName = config('constants.LOGIN_HISTORY_TABLE'). ' as lh';
			
		$selectData = [
				'lh.i_id',
				'lh.dt_created_at',
				'lh.v_ip',
				'lm.v_name',
				'lm.v_role',
				'em.i_id as employee_id',
				'em.v_employee_full_name',
				'em.v_employee_code',
				'team.v_value as team_name'
		];
			
		$joinData = [
				[
						'tableName' =>	config('constants.LOGIN_MASTER_TABLE') . ' as lm',
						'condition' =>	"lm.i_id = lh.i_login_id",
				],
				[
						'tableName' =>	config('constants.EMPLOYEE_MASTER_TABLE') . ' as em',
						'condition' =>	"em.i_login_id = lm.i_id",
						'type' => 'left'
				],
				[
						'tableName' =>	config('constants.LOOKUP_MASTER_TABLE'). ' as team',
						'condition' =>	"em.i_team_id = team.i_id",
						'type' => 'left'
				],
		];
		
		if( session()->get('role') == config("constants.ROLE_USER") ){
			if( isset($whereData['show_all']) && ( $whereData['show_all'] == true ) ){
				unset($whereData['show_all']);
			} else {
				/* $sessionEmployeeId = session()->get('user_employee_id');
				if( isset($whereData['custom_function']) ){
					$whereData['custom_function'] = array_merge($whereData['custom_function'] , [ "(em.i_id = '".$sessionEmployeeId."'  or em.i_leader_id = '".$sessionEmployeeId."' )" ]);
				} else {
					$defaultWhere['custom_function'][] = "(em.i_id = '".$sessionEmployeeId."'  or em.i_leader_id = '".$sessionEmployeeId."' )";
				} */
				
				$allChildEmployeeIds = $this->childEmployeeIds();
				if(!empty($allChildEmployeeIds)){
					$additionalData['whereIn'][] = ['em.i_id' , $allChildEmployeeIds];
				}
			}
			
			//
			//$defaultWhere['lm.i_id'] = session()->get('user_id');
		}
			
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
		//DB::enableQueryLog();
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordWithJoinById( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectJoinData( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
		}
		//echo $this->last_query();die;
		//$query = DB::getQueryLog();
		//$query = end($query);
		///print_r($query);die;
			
		return $data;
		
	}
	
}
