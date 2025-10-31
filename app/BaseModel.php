<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;

class BaseModel extends Model
{
    //
    public $loggedUserId;
    public $currentDateTime;
    const CREATED_AT = 'dt_created_at';
    const UPDATED_AT = 'dt_updated_at';
    const DELETED_AT = 'dt_deleted_at';
	public function __construct(){
    	DB::enableQueryLog();
    	parent::__construct();
    	$this->loggedUserId = 999;
    	$this->currentDateTime = date('Y-m-d H:i:s');
    }
    
    public function defaultData(){
    	if( ( Session::has('user_id') ) && ( Session::get('user_id') > 0 ) ){
    		$this->loggedUserId = Session::get('user_id');
    	}
    }
    
    protected function insertDateTimeData() {
    	$this->defaultData();
    	$result ['i_created_id'] = ( !empty( $this->loggedUserId ) ) ? $this->loggedUserId : '1'; //$this->session->userdata('user_id');
    	$result ['dt_created_at'] = $this->currentDateTime;
    	$result ['i_updated_id'] = ( !empty( $this->loggedUserId ) ) ? $this->loggedUserId : '1';
    	$result ['dt_updated_at'] = $this->currentDateTime;
    	$result['v_ip'] = \Request::ip();
    	return $result;
    }
    
    protected function updateDateTimeData() {
    	$this->defaultData();
    	$result ['i_updated_id'] = $this->loggedUserId;
    	$result ['dt_updated_at'] = $this->currentDateTime;
    	$result['v_ip'] = \Request::ip();
    	return $result;
    }
    
    protected function deleteDateTimeData() {
    	$this->defaultData();
    	$result ['i_deleted_id'] = $this->loggedUserId;
    	$result ['dt_deleted_at'] = $this->currentDateTime;
    	$result ['t_is_active'] = 0;
    	$result ['t_is_deleted'] = 1;
    	$result['v_ip'] = \Request::ip();
    	return $result;
    }
    
    public  function insertTableData($tableName,$insertData){
    
    	$recordId = 0;
    	try{
    			
    		if (! empty ( $insertData )) {
    			$insertData = array_merge ( $this->insertDateTimeData () , $insertData );
    			
    			$recordId = DB::table($tableName)->insertGetId($insertData);
    		
    		}
    			
    			
    	}catch(Exception $e){
    		//$this->errorLogEntry(['action' => 'select query' , 'data' => '' , 'error_message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
    	}
    
    	return $recordId;
    }
    
    public  function updateTableData($tableName,$updateData,$whereData){
    
    	$result = false;
    
    	 
    	try{
    		
    		$updateQuery = DB::table($tableName);
    		
    		if(!empty($whereData)){
    			foreach($whereData as $key=>$value){
    				$updateQuery->where($key, $value);
    			}
    
    			$updateData = array_merge ( $this->updateDateTimeData () , $updateData );
    			
    			$result = $updateQuery->update($updateData);
    			
    			
    		}
    	}catch(Exception $e){
    		//$this->errorLogEntry(['action' => 'select query' , 'data' => '' , 'error_message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
    	}
    	return $result;
    }
    
    public  function deleteTableData($tableName,$updateData,$whereData){
    
    	$result = false;
    
    	try{
    		$deleteQuery = DB::table($tableName);
    		
    		if(!empty($whereData)){
    			foreach($whereData as $key=>$value){
    				$deleteQuery->where($key, $value);
    			}
    
    			$updateData = array_merge (  $this->deleteDateTimeData () , $updateData );
    
    			$result = $deleteQuery->update($updateData);
    		}
    	}catch(Exception $e){
    		$this->errorLogEntry(['action' => 'select query' , 'data' => '' , 'error_message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
    	}
    	 
    
    	return $result;
    }
    
    
	public  function selectData($tableName, $selectColumns = [],$whereData = [] , $likeData = [] , $additionData = []){
	    $result = [];
	    
	    try{
	    	
	    	$query = \DB::table($tableName);
	    	
	    	$query->select($selectColumns);
	    	
	    	if( isset($whereData['getCount']) && ( $whereData['getCount'] != false ) ){
	    		$query->select(DB::raw("count(1) as record_count"));
	    		unset($whereData['getCount']);
	    	}
	    	
		    if(!empty($whereData)){
				foreach($whereData as $key => $where){
					$key = trim($key);
					switch($key){
						case 'offset':
							$query->skip($where);
							break;
						case 'limit':
							$query->take($where);
							break;
						case 'having':
							$query->havingRaw($where);
							break;
						case 'null_column':
							$query->whereNull($where);
							break;
						case 'find_in_set':
							
							break;
						case 'null_not_column':
							if(is_array($where)){
								foreach($where as $k => $v){
									$query->whereNotNull($v);
								}
							} else {
								$query->whereNotNull($where);
							}
							break;
							
						case 'custom_function':
						
							break;
						case 'group_by':
						
							break;
						case 'order_by':
							break;
						default:
							if( preg_match('/[!=><]/' , $key ) != false ){
								$key = explode(" " , $key);
								$query->where($key[0] , $key[1] , $where );
							} else {
								$query->where($key , $where );
							}
							
							break;
					}
				}
			}
			
			if((!empty($whereData)) && array_key_exists('group_by', $whereData)){
				$query->groupBy($whereData['group_by']);
			}
			
			if((!empty($whereData)) && array_key_exists('find_in_set', $whereData)){
				$findInSetColumn = $whereData['find_in_set'];
				$query->whereRaw("find_in_set(".$findInSetColumn[1].",".$findInSetColumn[0].")");
			}
			
			if((!empty($whereData)) && array_key_exists('custom_function', $whereData)){
				$customerFunctionWhere = $whereData['custom_function'];
				//echo '<pre> customerFunctionWhere';print_r($customerFunctionWhere);
				if(!empty($customerFunctionWhere)){
					if(is_array($customerFunctionWhere)){
						foreach($customerFunctionWhere as $key => $customerFunction){
							$query->whereRaw( $customerFunction );
						}
					} else {
						$query->whereRaw( $customerFunctionWhere);
					}
				}
			}
			
			if((!empty($whereData)) && array_key_exists('order_by', $whereData)){
				$orderByColumn = $whereData['order_by'];
					
				if(!empty($orderByColumn)){
					foreach($orderByColumn as  $key => $value){
						$query->orderBy($key, (!empty($value) ? $value : 'DESC' ) );
					}
				}
					
				//$orderByArray = explode(" " , $orderByColumn );
				//$query->orderBy($orderByArray[0], (!empty($orderByArray[1]) ? $orderByArray[1] : 'DESC' ) );
			}
			
	    	if(!empty($likeData)){
				$query->where(function($q) use ($likeData){
					foreach($likeData as $key => $like){
						$q->orWhere($key, 'like', '%' .$like . '%');
					}
				});
			}
			
			if(!empty($additionData)){
				foreach($additionData as $key => $addition){
					switch($key){
						case 'orWhere':
							$query->where(function($q) use ($addition){
								$firstElement = array_slice($addition, 0, 1);
								$q->where(key($firstElement), $firstElement[key($firstElement)] );
								array_shift($addition);
								foreach( $addition as $k => $v ){
									$q->orWhere($k, $v);
								}
							});
							break;
						case 'whereIn':
							$query->whereIn($addition[0] , $addition[1] );
							break;
					}
				}
			}
	    	
	    	$result  = $query->get();
	    	/*
	    	$query = DB::getQueryLog();
	    	$query = end($query);
	    	print_r($query);die;
	    	*/
	    	return $result;
	    }catch(Exception $e){
	    	//$this->errorLogEntry(['action' => 'select query' , 'data' => '' , 'error_message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
	    }
	    
	
	    return $result;
	}
	
	public  function selectJoinData($tableName, $selectColumns = [], $joinData = [] , $whereData = [] , $likeData = [] , $additionData = []){
		$result = [];
		
		try{
			
			$query = \DB::table($tableName);
			$select = "";
			$defaultWhere = ['t_is_deleted !=' => 1];
			//$selectColumns = "CONCAT(um.v_firstname,' ',um.v_lastname) as full_name";
			//$selectColumns =  [ 'um.i_id' ,  DB::raw("CONCAT(um.v_firstname,' ',um.v_lastname)  AS fullname") ]  ;
			$query->select($selectColumns);
			
			if(!empty($joinData)){
				foreach($joinData as $joinInfo){
					if( (!is_array($joinInfo['condition']))  &&  ( strpos($joinInfo['condition'], 'and') !== false) ) {
						$allJoinCondition = explode("and" , $joinInfo['condition']);
						switch(  $joinInfo['type'] ){
							case 'left':
								$query->leftJoin($joinInfo['tableName'], function($join) use ($allJoinCondition) {
									foreach($allJoinCondition  as $allJoinCond){
										$explodeCondition = explode("=" ,$allJoinCond);
										
										if (strpos($explodeCondition[1], '.') !== false) {
											$join->on(trim($explodeCondition[0]), '=', trim($explodeCondition[1]));
										} else {
											$join->on(trim($explodeCondition[0]), '=', DB::raw(trim($explodeCondition[1])) );
										}
									}
								});
							break;
							default: 
								$query->join($joinInfo['tableName'], function($join) use ($allJoinCondition) {
									foreach($allJoinCondition  as $allJoinCond){
										$explodeCondition = explode("=" ,$joinInfo['condition']);
										if (strpos($explodeCondition[1], '.') !== false) {
											$join->on(trim($explodeCondition[0]), '=', trim($explodeCondition[1]));
										} else {
											$join->on(trim($explodeCondition[0]), '=', DB::raw(trim($explodeCondition[1])) );
										}
									}
								
								});
								break;
						}
						
					} else {
						
						if(is_array($joinInfo['condition'])){
							$customJoin = $joinInfo['condition']['custom'];
							$query->leftJoin($joinInfo['tableName'], function($join) use ($customJoin) {
								 $join->on( DB::raw($customJoin) , ">" , DB::raw("'0'") );
							});
						} else {
							$explodeCondition = explode("=" ,$joinInfo['condition']);
							$query->join($joinInfo['tableName'], trim($explodeCondition[0]) , '=' , trim($explodeCondition[1]) , !empty($joinInfo['type']) ? $joinInfo['type'] : '' );
						}
						
						
					}
				}
			}
			//echo '<pre>';print_r($whereData);
			if(!empty($whereData)){
				foreach($whereData as $key => $where){
					switch($key){
						case 'offset':
							$query->skip($where);
							break;
						case 'limit':
							$query->take($where);
							break;
						case 'order_by':
							
							break;
						case 'group_by':
								
							break;
						case 'having':
							$query->havingRaw($where);
							break;
						case 'find_in_set':
						
							break;
						case 'null_column':
							$query->whereNull($where);
							break;
						case 'custom_function':
						
							break;
						case 'null_not_column':
							if(is_array($where)){
								foreach($where as $k => $v){
									$query->whereNotNull($v);
								}
							} else {
								$query->whereNotNull($where);
							}
							break;
						default:
							//$query->where($key , $where );
							if( preg_match('/[!=><]/' , $key ) != false ){
								$key = explode(" " , $key);
								$query->where($key[0] , $key[1] , $where );
							} else {
								$query->where($key , $where );
							}
							break;
					}
				}
			}
			
			if((!empty($whereData)) && array_key_exists('order_by', $whereData)){
				$orderByColumn = $whereData['order_by'];
				
				if(!empty($orderByColumn)){
					foreach($orderByColumn as  $key => $value){
						$query->orderBy($key, (!empty($value) ? $value : 'DESC' ) );
					}
				}
				/*
				$orderByArray = explode(" " , $orderByColumn );
				$query->orderBy($orderByArray[0], (!empty($orderByArray[1]) ? $orderByArray[1] : 'DESC' ) );
				*/
			}
			
			if((!empty($whereData)) && array_key_exists('find_in_set', $whereData)){
				$findInSetColumn = $whereData['find_in_set'];
				$query->whereRaw("find_in_set(".$findInSetColumn[1].",".$findInSetColumn[0].")");
			}
			
			if((!empty($whereData)) && array_key_exists('custom_function', $whereData)){
				$customerFunctionWhere = $whereData['custom_function'];
				//echo '<pre> customerFunctionWhere';print_r($customerFunctionWhere);
				if(!empty($customerFunctionWhere)){
					if(is_array($customerFunctionWhere)){
						foreach($customerFunctionWhere as $key => $customerFunction){
							$query->whereRaw( $customerFunction );
						}	
					} else {
						$query->whereRaw( $customerFunctionWhere);
					}
				}
			}
			
			//$query->whereRaw("date_format(l.v_closing_date,'%Y-%m') = ? " , [ '2020-03' ] );
			
			
			
			if((!empty($whereData)) && array_key_exists('group_by', $whereData)){
				$query->groupBy( $whereData['group_by'] );
			}
			
			
			
			if(!empty($likeData)){
				$query->where(function($q) use ($likeData){
					foreach($likeData as $key => $like){
						$q->orWhere($key, 'like', '%' .$like . '%');
					}
				});
			}
			
			if(!empty($additionData)){
				foreach($additionData as $key => $addition){
					switch($key){
						case 'orWhere':
							$query->where(function($q) use ($addition){
								$firstElement = array_slice($addition, 0, 1);
								$q->where(key($firstElement), $firstElement[key($firstElement)] );
								array_shift($addition);
								foreach( $addition as $k => $v ){
									$q->orWhere($k, $v);
								}
							});
							break;
						case 'whereIn':
							if(is_array($addition[0])){
								foreach($addition as $additionWhereIn){
									$query->whereIn($additionWhereIn[0] , $additionWhereIn[1] );
								}
							} else {
								$query->whereIn($addition[0] , $addition[1] );
							}
							
							break;
					}
				}
			}
			//dd($query);
			$result  = $query->get();
			//dd($result);
			/*
			$query = DB::getQueryLog();
			$query = end($query);
			print_r($query);die;
			*/
			//dd($result);
			return $result;
		}catch(Exception $e){
			//$this->errorLogEntry(['action' => 'select query' , 'data' => '' , 'error_message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
		}
		 
	
		return $result;
	}
	
	public  static function getSingleRecordById($tableName,$selectColumns = [],$whereData = [], $likeData = [], $additionData = []){
		$result = [];
		 
		try{
			
			$query = \DB::table($tableName);
	
			$query->select($selectColumns);
			
			if(!empty($whereData)){
				foreach($whereData as $key => $where){
					switch($key){
						case 'offset':
							$query->skip($where);
							break;
						case 'limit':
							$query->take($where);
							break;
						case 'null_column':
							$query->whereNull($where);
							break;
						case 'null_not_column':
							if(is_array($where)){
								foreach($where as $k => $v){
									$query->whereNotNull($v);
								}
							} else {
								$query->whereNotNull($where);
							}
							break;
						case 'group_by':
						
							break;
						case 'custom_function':
							break;
						case 'find_in_set':
						
							break;
						case 'order_by':
							break;
						default:
							if( preg_match('/[!=><]/' , $key ) != false ){
								$key = explode(" " , $key);
								$query->where($key[0] , $key[1] , $where );
							} else {
								$query->where($key , $where );
							}
							break;
					}
				}
			}
			
			if((!empty($whereData)) && array_key_exists('order_by', $whereData)){
					$orderByColumn = $whereData['order_by'];
					
				if(!empty($orderByColumn)){
					foreach($orderByColumn as  $key => $value){
						$query->orderBy($key, (!empty($value) ? $value : 'DESC' ) );
					}
				}
				/*
				$orderByArray = explode(" " , $orderByColumn );
				$query->orderBy($orderByArray[0], (!empty($orderByArray[1]) ? $orderByArray[1] : 'DESC' ) );
				*/
			}
			
			if((!empty($whereData)) && array_key_exists('find_in_set', $whereData)){
				$findInSetColumn = $whereData['find_in_set'];
				$query->whereRaw("find_in_set(".$findInSetColumn[1].",".$findInSetColumn[0].")");
			}
			
			if((!empty($whereData)) && array_key_exists('group_by', $whereData)){
				$query->groupBy($whereData['group_by']);
			}
			
			if((!empty($whereData)) && array_key_exists('custom_function', $whereData)){
				$customerFunctionWhere = $whereData['custom_function'];
					
				if(!empty($customerFunctionWhere)){
					if(is_array($customerFunctionWhere)){
						foreach($customerFunctionWhere as $key => $customerFunction){
							$query->whereRaw( $customerFunction );
						}
					} else {
						$query->whereRaw( $customerFunctionWhere);
					}
				}
			}
				
			if(!empty($likeData)){
				$query->where(function($q) use ($likeData){
					foreach($likeData as $key => $like){
						$q->orWhere($key, 'like', '%' .$like . '%');
					}
				});
			}
			
			if(!empty($additionData)){
				foreach($additionData as $key => $addition){
					switch($key){
						case 'orWhere':
							$query->where(function($q) use ($addition){
								$firstElement = array_slice($addition, 0, 1);
								$q->where(key($firstElement), $firstElement[key($firstElement)] );
								array_shift($addition);
								foreach( $addition as $k => $v ){
									$q->orWhere($k, $v);
								}
							});
							break;
						case 'whereIn':
							$query->whereIn($addition[0] , $addition[1] );
							break;
					}
				}
			}
	
			$result  = $query->first();
			return $result;
	
		}catch(Exception $e){
			//$this->errorLogEntry(['action' => 'select query' , 'data' => '' , 'error_message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
		}
		return $result;
	}
	
	public  function getSingleRecordWithJoinById($tableName,$selectColumns = [],$joinData = [],$whereData = [] , $likeData = [] , $additionData = []){
		$result = [];
		 
		 
		try{
			$query = \DB::table($tableName);
	
			$query->select($selectColumns);
			
		if(!empty($joinData)){
				foreach($joinData as $joinInfo){
					
				if( (!is_array($joinInfo['condition']))  &&  ( strpos($joinInfo['condition'], 'and') !== false) ) {
						$allJoinCondition = explode("and" , $joinInfo['condition']);
						switch(  $joinInfo['type'] ){
							case 'left':
								$query->leftJoin($joinInfo['tableName'], function($join) use ($allJoinCondition) {
									foreach($allJoinCondition  as $allJoinCond){
										$explodeCondition = explode("=" ,$allJoinCond);
										
										if (strpos($explodeCondition[1], '.') !== false) {
											$join->on(trim($explodeCondition[0]), '=', trim($explodeCondition[1]));
										} else {
											$join->on(trim($explodeCondition[0]), '=', DB::raw(trim($explodeCondition[1])) );
										}
									}
								});
							break;
							default: 
								$query->join($joinInfo['tableName'], function($join) use ($allJoinCondition) {
									foreach($allJoinCondition  as $allJoinCond){
										$explodeCondition = explode("=" ,$joinInfo['condition']);
										if (strpos($explodeCondition[1], '.') !== false) {
											$join->on(trim($explodeCondition[0]), '=', trim($explodeCondition[1]));
										} else {
											$join->on(trim($explodeCondition[0]), '=', DB::raw(trim($explodeCondition[1])) );
										}
									}
								
								});
								break;
						}
						
					} else {
						
						if(is_array($joinInfo['condition'])){
							$customJoin = $joinInfo['condition']['custom'];
							$query->leftJoin($joinInfo['tableName'], function($join) use ($customJoin) {
								 $join->on( DB::raw($customJoin) , ">" , DB::raw("'0'") );
							});
						} else {
							$explodeCondition = explode("=" ,$joinInfo['condition']);
							$query->join($joinInfo['tableName'], trim($explodeCondition[0]) , '=' , trim($explodeCondition[1]) , !empty($joinInfo['type']) ? $joinInfo['type'] : '' );
						}
						
						
					}
					
					
					
				}
			}
			
			if(!empty($whereData)){
				foreach($whereData as $key => $where){
					switch($key){
						case 'offset':
							$query->skip($where);
							break;
						case 'limit':
							$query->take($where);
							break;
						case 'custom_function':
							break;
						case 'group_by':
						
							break;
						case 'order_by':
							break;
						default:
							if( preg_match('/[!=><]/' , $key ) != false ){
								$key = explode(" " , $key);
								$query->where($key[0] , $key[1] , $where );
							} else {
								$query->where($key , $where );
							}
							break;
					}
				}
			}
			
			if((!empty($whereData)) && array_key_exists('order_by', $whereData)){
				$orderByColumn = $whereData['order_by'];
			
				if(!empty($orderByColumn)){
					foreach($orderByColumn as  $key => $value){
						$query->orderBy($key, (!empty($value) ? $value : 'DESC' ) );
					}
				}
			
				//$orderByArray = explode(" " , $orderByColumn );
				//$query->orderBy($orderByArray[0], (!empty($orderByArray[1]) ? $orderByArray[1] : 'DESC' ) );
			}
			
			if((!empty($whereData)) && array_key_exists('custom_function', $whereData)){
				$customerFunctionWhere = $whereData['custom_function'];
			
				if(!empty($customerFunctionWhere)){
					if(is_array($customerFunctionWhere)){
						foreach($customerFunctionWhere as $key => $customerFunction){
							$query->whereRaw( $customerFunction );
						}	
					} else {
						$query->whereRaw( $customerFunctionWhere);
					}
				}
			}
			
			
			if((!empty($whereData)) && array_key_exists('group_by', $whereData)){
				//$query->groupBy($whereData['group_by']);
				
				$query->groupBy( $whereData['group_by'] );
			}
			
			if(!empty($likeData)){
				$query->where(function($q) use ($likeData){
					foreach($likeData as $key => $like){
						$q->orWhere($key, 'like', '%' .$like . '%');
					}
				});
			}
	
			if(!empty($additionData)){
				foreach($additionData as $key => $addition){
					switch($key){
						case 'orWhere':
							$query->where(function($q) use ($addition){
								$firstElement = array_slice($addition, 0, 1);
								$q->where(key($firstElement), $firstElement[key($firstElement)] );
								array_shift($addition);
								foreach( $addition as $k => $v ){
									$q->orWhere($k, $v);
								}
							});
							break;
						case 'whereIn':
							if(is_array($addition)){
								foreach($addition as $additionWhereIn){
									$query->whereIn($additionWhereIn[0] , $additionWhereIn[1] );
								}
							} else {
								$query->whereIn($addition[0] , $addition[1] );
							}
							break;
					}
				}
			}
			
			$result  = $query->first();
			
			return $result;
		}catch(Exception $e){
			//$this->errorLogEntry(['action' => 'select query' , 'data' => '' , 'error_message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
		}
		 
		 
	
		return $result;
	}
	
	public function manageWhere($whereData , $query){
		

		if(!empty($whereData)){
			foreach($whereData as $key => $where){
				switch($key){
					case 'offset':
						$query->skip($where);
						break;
					case 'limit':
						$query->take($where);
						break;
					case 'order_by':
						break;
					default:
						if( preg_match('/[!=><]/' , $key ) != false ){
							$key = explode(" " , $key);
							$query->where($key[0] , $key[1] , $where );
						} else {
							$query->where($key , $where );
						}
						break;
				}
			}
		}
		
		if((!empty($whereData)) && array_key_exists('order_by', $whereData)){
			$orderByColumn = $whereData['order_by'];
			
			if(!empty($orderByColumn)){
				foreach($orderByColumn as  $key => $value){
					$query->orderBy($key, (!empty($value) ? $value : 'DESC' ) );
				}
			}
			
			//$orderByArray = explode(" " , $orderByColumn );
			//$query->orderBy($orderByArray[0], (!empty($orderByArray[1]) ? $orderByArray[1] : 'DESC' ) );
		}
		
		
		return $query;
		
	}
	
	public function manageLike($likeData , $query){
	
	
		if(!empty($likeData)){
			$query->where(function($q) use ($likeData){
				foreach($likeData as $key => $like){
					$q->orWhere($key, 'like', '%' .$like . '%');
				}
			});
			
		}
	
		return $query;
	
	}
	
	
	
	
	
	
	
	public function getUserDetail( $whereData = [] , $likeData = [] , $additionalData = [] ){
			
		if(isset($whereData['singleRecord'])){
			$this->singleRecord = true;
			unset($whereData['singleRecord']);
		} else {
			$this->singleRecord = false;
		}
	
		$defaultWhere = [];
		$defaultWhere['lm.t_is_deleted != ' ] = 1;
		
		$tableName = config('constants.LOGIN_MASTER_TABLE'). ' as lm';
			
		$selectData = [
				'lm.i_id',
				'lm.v_name',
				'lm.v_email',
				'lm.t_is_active',
				'lm.v_mobile',
				'lm.v_role',
		];
	
		
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
			
		//DB::enableQueryLog();
		if( $this->singleRecord == true ){
			$data = $this->getSingleRecordById( $tableName, $selectData,  $whereData, $likeData, $additionalData );
		} else {
			$data = $this->selectData( $tableName, $selectData,   $whereData, $likeData, $additionalData );
		}
		
		$query = DB::getQueryLog();
		$query = end($query);
		print_r($query);die;
			
		return $data;
			
	}
	
	
	
	
	
	
	
	
	
	
	public static function last_query(){
	
		$query = DB::getQueryLog();
		$query = end($query);
		$last_query = self::bindDataToQuery($query);
		return $last_query;
	
	}
	
	protected static function bindDataToQuery($queryItem){
		$query = $queryItem['query'];
		$bindings = $queryItem['bindings'];
		$arr = explode('?',$query);
		$res = '';
		foreach($arr as $idx => $ele){
			if($idx < count($arr) - 1){
				$res = $res.$ele."'".$bindings[$idx]."'";
			}
		}
		$res = $res.$arr[count($arr) -1];
		return $res;
	}
	
	
	
	public function multipleSearch( $fieldData , $columnName , $condition = 'OR'){
		$searchRegion = explode("," , $fieldData );
		$customWhere = ' ( ';
		foreach($searchRegion as $region){
			$customWhere.= "find_in_set(  '".$region."' , ".$columnName." ) ".$condition." ";
		}
		$customWhere = rtrim($customWhere , $condition.' ');
		$customWhere .= ' ) ';
		return $customWhere;
	}
	
	public function getSequence(){
		$count = 1;
		$result = $this->getSingleRecordById(config ( 'constants.LOOKUP_MASTER_TABLE' ) , [ DB::raw('max(i_id) as result') ] );	
		//$result = $this->db->select ( 'max(i_id) as result' )->from ( LOOKUP_MASTER_TABLE  )->get ()->row ();
			
		if (! empty ( $result )) {
			$count = ( $result->result + 1 );
		}
			
		return $count;
	}
	
	public function childEmployeeIds($empId = NULL, $onlyChildEmployeeIds = FALSE){
		if (empty($empId)){
			$empId = session()->get('user_employee_id');
		}
		
		/* $childEmployeeDetails = DB::select("select  group_concat(i_id) as all_user_ids from   (select * from ".config('constants.EMPLOYEE_MASTER_TABLE')."
         order by i_leader_id, i_id) ".config('constants.EMPLOYEE_MASTER_TABLE').",
        (select @pv := ".$empId.") initialisation
where   find_in_set(i_leader_id, @pv) > 0
and     @pv := concat(@pv, ',', i_id)"); */
		
		$query = "SELECT GROUP_CONCAT(Level SEPARATOR ',') as all_user_ids FROM (
				SELECT @Ids := (
						SELECT GROUP_CONCAT(`i_id` SEPARATOR ',')
						FROM ".config('constants.EMPLOYEE_MASTER_TABLE')."
						WHERE t_is_deleted != 1 and  FIND_IN_SET(`i_leader_id`, @Ids)
				) Level
				FROM ".config('constants.EMPLOYEE_MASTER_TABLE')."
				JOIN (SELECT @Ids := ".$empId.") temp1
		) temp2";
		
		$childEmployeeDetails = DB::select($query);
		
		$childEmployeeIds = ( isset($childEmployeeDetails[0]->all_user_ids) ? explode("," , $childEmployeeDetails[0]->all_user_ids ) : [] );
		
		
		/* $employeeDetails = EmployeeModel::with('childInfo')->select('i_id')->where('i_leader_id', $empId)->get();
		 
		$childEmployeeIds = [];
		
		if (!empty($employeeDetails) && count($employeeDetails) > 0){
			$childEmployeeIds = $this->getEmployeeChildDetails($employeeDetails);
		} */
		
		if ($onlyChildEmployeeIds != true){
			$childEmployeeIds[] = $empId;
		}
		
		return $childEmployeeIds;
	}
	
	public function getEmployeeChildDetails($employeeDetails){
		$allChildIds = [];
		
		if (!empty($employeeDetails)){
			foreach ($employeeDetails as $employeeDetail){
				$allChildIds[] = $employeeDetail->i_id;
				
				if (isset($employeeDetail->childInfo) && count($employeeDetail->childInfo) > 0){
					$childIds = $this->getEmployeeChildDetails($employeeDetail->childInfo);
				}
			}
		}
		
		if (isset($childIds) && !empty($childIds)){
			return array_merge($childIds, $allChildIds);
		}
		
		return $allChildIds;
	}
}