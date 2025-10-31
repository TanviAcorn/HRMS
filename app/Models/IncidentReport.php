<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\BaseModel;
use GhanuZ\FindInSet\FindInSetRelationTrait;
use App\EmployeeModel;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class IncidentReport extends BaseModel
{
    use HasFactory;
    use FindInSetRelationTrait;
    use HasJsonRelationships;
    protected $table = '';
    protected $primaryKey = 'i_id';
    
    protected $casts = [
    		'v_employee_ids' => 'array',
    ];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.INCIDENT_REPORT_TABLE');
    }
    
    public function employee(){
    	return $this->BelongsToJson(EmployeeModel::class, 'v_employee_ids' );
    	//return $this->FindInSetMany( EmployeeModel::class , 'v_employee_ids' , 'i_id' );
    }
    
    public function incidentAttachment(){
    	return $this->hasMany( IncidentAttachment::class , 'i_incedent_id' );
    }
    
	public function getRecordDetails( $where = [] , $likeData = [] ,  $additionalData = []){
		
		if(isset($where['singleRecord'])){
    		$this->singleRecord = true;
    		unset($where['singleRecord']);
    	} else {
    		$this->singleRecord = false;
    	}
    	
    	$query = self::with(['employee' ,'employee.designationInfo' ,'employee.teamInfo', 'incidentAttachment'])->where('t_is_deleted' , 0 )->orderBy('i_id' , 'desc');
    	
    	if(session()->get('role') == config('constants.ROLE_USER')){
    		if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
    			unset($where['show_all']);
    		} else {
    			$employeeId = session()->get('user_employee_id');

    			$query->whereHas('employee' , function ($q) use($employeeId){
    				$q->where(function ($q1){
    					//$q1->whereRaw( "( i_id = '".$employeeId."' or i_leader_id = '".$employeeId."' ) " );
    					
    					$allChildEmployeeIds = $this->childEmployeeIds();
    					if(!empty($allChildEmployeeIds)){
    						$q1->whereIn('i_id', $allChildEmployeeIds);
    					}
    				});
    			});
    		}
    	}
    	
    	if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
    		$employmentStatus = $where['employment_status'];
    		$query->whereHas('employee' , function($q) use($employmentStatus) {
    			if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
    				$q->whereIn('e_employment_status',[config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')]);
    			}else {
    				$q->where('e_employment_status',$employmentStatus);
    			}
    		});
    	}
    	
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$masterRecordId = $where['master_id'];
    		if( is_array($masterRecordId)){
    			$query->whereIn('i_id', $masterRecordId);
    		} else {
    			$query->where('i_id','=',$masterRecordId);
    		}
    	}
    	
    	if(isset($where['team_record']) && (!empty($where['team_record'])) ){
    		$teamRecordId = $where['team_record'];
    		$query->whereHas('employee' , function ($q) use($teamRecordId){
    			$q->where('i_team_id',$teamRecordId);
    		});
    	}
    	
    	if (isset($likeData['v_subject']) && !empty($likeData['v_subject'])){
    		$searchString = $likeData['v_subject'];
    		
    		$allLikeColumns = [ 'v_subject' , 'v_report_no' ];
    		
    		$query->where(function($q) use ($allLikeColumns,$searchString){
    			foreach($allLikeColumns as $key => $allLikeColumn){
    				$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    			}
    		});
    	}
    	
    	if (isset($where['report_from_date']) && !empty($where['report_from_date'])){
    		$fromDate = dbDate($where['report_from_date']);
    		$query->whereRaw("(  dt_report_date >= '".$fromDate."')");
    	}
    	
    	if (isset($where['report_to_date']) && !empty($where['report_to_date'])){
    		$toDate = dbDate($where['report_to_date']);
    		$query->whereRaw("(  dt_report_date <= '".$toDate."')");
    	}
    	if (isset($where['employee_name']) && !empty($where['employee_name'])){
    		$employeeIds = $where['employee_name'];
    		if (!empty($employeeIds)){
    			$employeeSearch = "( ";
    			foreach ($employeeIds as $employeeId){
    				$employeeSearch .= "json_contains(v_employee_ids, '".$employeeId."') OR ";
    			}
    			$employeeSearch = rtrim($employeeSearch , "OR ");
    			$employeeSearch .= " )";
    			$query->whereRaw($employeeSearch);
    		}
    	}
    	if (isset($where['e_status']) && !empty($where['e_status'])){
    		$query->where('e_status' , $where['e_status']);
    	}
    	if(isset($where['designation']) && (!empty($where['designation']))){
    		$designation = $where['designation'];
    		$query->whereHas('employee.designationInfo' , function($query) use($designation) {
    			$query->where('i_designation_id',$designation);
    		});
    	}
    	if(isset($where['team_record']) && (!empty($where['team_record']))){
    		$teamId = $where['team_record'];
    		$query->whereHas('employee.teamInfo' , function($query) use($teamId) {
    			$query->where('i_team_id',$teamId);
    		});
    	}
    	$query->orderBy('i_id', "DESC" ) ;
    	if( isset($where['offset']) ){
    		$query->skip($where ['offset']);
    	}
    	if( isset($where['limit']) ){
    		$query->take($where['limit']);
    	}
    	if( isset($where['edit_record']) && ( ($where['edit_record']) == true ) ){
    		$query->groupBy('i_id');
    	}
    	if( $this->singleRecord == true ){
    		$data = $query->first();
    	} else {
    		$data = $query->get();
    	}
    	return $data;
    }
}
