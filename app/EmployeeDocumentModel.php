<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;

class EmployeeDocumentModel extends BaseModel{
    use MySoftDeletes;
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.EMPLOYEE_DOCUMENT_MASTER_DETAILS_TABLE');
	}
	
	public function documentType(){
		return $this->belongsTo(DocumentTypeModel::class ,'i_document_type_id');
	}
	public function employeeInfo(){
		return $this->belongsTo(EmployeeModel::class,'i_employee_id');
	}
	public function getRecordDetails( $where = [] , $likeData = [] ){
		$query = EmployeeDocumentModel::with(['documentType.documentFolderMaster','employeeInfo.designationInfo','employeeInfo.teamInfo']);

		if(session()->get('role') == config('constants.ROLE_USER')){
			if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
				unset($where['show_all']);
			} else {
				$employeeId = session()->get('user_employee_id');
				$query->whereHas('employeeInfo' , function ($q) use($employeeId){
					$q->where(function ($q1)use($employeeId){
						/* $q1->where('i_id',$employeeId);
						$q1->orWhere('i_leader_id',$employeeId); */
						
						$allChildEmployeeIds = $this->childEmployeeIds();
						if(!empty($allChildEmployeeIds)){
							$q1->whereIn('i_id', $allChildEmployeeIds);
						}
					});
				});
			}
			
		}
		
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			$query->where('i_document_type_id','=',$masterRecordId);
		}
		if(isset($where['employee_id']) && (!empty($where['employee_id'])) ){
			$employeeId = $where['employee_id'];
			$query->where('i_employee_id',$employeeId);
		}
		if(isset($where['joining_from_date']) && (!empty($where['joining_from_date'])) ){
			$joiningFromDate = dbDate($where['joining_from_date']);
			$query->whereHas('employeeInfo' , function($query) use($joiningFromDate) {
				$query->whereRaw("(  dt_joining_date >= '".$joiningFromDate."'  or dt_joining_date >= '".$joiningFromDate."'  )");
			});
			
		}
		if(isset($where['joining_to_date']) && (!empty($where['joining_to_date'])) ){
			$joiningToDate = dbDate($where['joining_to_date']);
			$query->whereHas('employeeInfo' , function($query) use($joiningToDate) {
				$query->whereRaw("(  dt_joining_date <= '".$joiningToDate."'  or dt_joining_date <= '".$joiningToDate."'  )");
			});
		}
		
		if(isset($where['designation_id']) && (!empty($where['designation_id'])) ){
			$designationId = $where['designation_id'];
			$query->whereHas('employeeInfo.designationInfo',function ($query) use ($designationId){
				$query->where('i_designation_id',$designationId);
			});
		}
		if(isset($where['team_record_id']) && (!empty($where['team_record_id'])) ){
			$teamRecordId = $where['team_record_id'];
			$query->whereHas('employeeInfo.teamInfo',function ($query) use ($teamRecordId){
				$query->where('i_team_id',$teamRecordId);
			});
			
		}
		if(isset($where['document_folder_id']) && (!empty($where['document_folder_id'])) ){
			$documentFolderRecordId = $where['document_folder_id'];
			$query->whereHas('documentType.documentFolderMaster',function ($query) use ($documentFolderRecordId){
				$query->where('i_document_folder_id',$documentFolderRecordId);
			});
					
		}
		
		
		if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
			$employmentStatus = $where['employment_status'];
			$query->whereHas('employeeInfo' , function($query) use($employmentStatus) {
				if ($employmentStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
					$query->whereIn('e_employment_status',[config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')]);
				}else {
					$query->where('e_employment_status',$employmentStatus);
				}
			});
		}
		
		if(isset($where['employment_relieved_status']) && (!empty($where['employment_relieved_status'])) ){
			$employmentRelievedStatus = $where['employment_relieved_status'];
			$query->whereHas('employeeInfo' , function($query) use($employmentRelievedStatus) {
				$query->whereNotIn('e_employment_status',$employmentRelievedStatus);
			});
		}
		
		if( isset($where['edit_record']) && ( ($where['edit_record']) == true ) ){
			$query->groupBy('i_document_type_id');
			$query->groupBy('i_employee_id');
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
