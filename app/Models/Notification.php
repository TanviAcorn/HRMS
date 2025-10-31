<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;
use App\Login;

class Notification extends BaseModel
{
	use HasFactory,SoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.EMAIL_HISTORY_TABLE');
    }
    
    public function loginInfo(){
    	return $this->belongsTo(Login::class , 'i_login_id');
    }
    
    public function getRecordDetails( $where = [] , $likeData = [] , $additionalData = [] ){
    	$query = EmailHistory::with([ 'loginInfo' ]);
    	$query->whereNotNull('v_notification_title');
    	if(session()->get('role') == config("constants.ROLE_USER")){
    		$query->where('i_login_id','=',session()->get('user_id'));
    	}
    	if(session()->get('role') == config("constants.ROLE_ADMIN")){
    		$query->where('i_login_id','=',null);
    	}
    	
    	if(isset($where['read_status'])){
    		$query->where('t_read_notification','=',$where['read_status']);
    	}
    	
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		$masterRecordId = $where['master_id'];
    		$query->where('i_id','=',$masterRecordId);
    	}
    	if(isset($where['current_address_city_id']) && (!empty($where['current_address_city_id'])) ){
    		$currentCityId = $where['current_address_city_id'];
    		$query->where('i_current_address_city_id',$currentCityId);
    	}
    	if(isset($where['permanent_address_city_id']) && (!empty($where['permanent_address_city_id'])) ){
    		$permentCityId = $where['permanent_address_city_id'];
    		$query->where('i_permanent_address_city_id',$permentCityId);
    	}
    	if(isset($where['gender']) && (!empty($where['gender'])) ){
    		$genderRecord = $where['gender'];
    		$query->where('e_gender',$genderRecord);
    	}
    	if(isset($where['blood_group']) && (!empty($where['blood_group'])) ){
    		$bloodGroupRecord = $where['blood_group'];
    		$query->where('v_blood_group',$bloodGroupRecord);
    	}
    	if(isset($where['joining_from_date']) && (!empty($where['joining_from_date'])) ){
    		$joiningFromDate = dbDate($where['joining_from_date']);
    		//$query->where('dt_joining_date','>=',dbDate($joiningFromDate));
    		$query->whereRaw("(  dt_joining_date >= '".$joiningFromDate."')");
    	}
    	if(isset($where['joining_to_date']) && (!empty($where['joining_to_date'])) ){
    		$joiningToDate = dbDate($where['joining_to_date']);
    		//$query->where('dt_joining_date','<=',dbDate($joiningToDate));
    		$query->whereRaw("(  dt_joining_date <= '".$joiningToDate."')");
    	}
    	if(isset($where['designation']) && (!empty($where['designation'])) ){
    		$designationId = $where['designation'];
    		$query->where('i_designation_id',$designationId);
    	}
    	if(isset($where['team_record']) && (!empty($where['team_record'])) ){
    		$teamRecordId = $where['team_record'];
    		$query->where('i_team_id',$teamRecordId);
    	}
    	if(isset($where['recruitment_source']) && (!empty($where['recruitment_source'])) ){
    		$recruitmentRecordId = $where['recruitment_source'];
    		$query->where('i_recruitment_source_id',$recruitmentRecordId);
    	}
    	if(isset($where['reference_name']) && (!empty($where['reference_name'])) ){
    		$referenceRecordId = $where['reference_name'];
    		$query->where('i_reference_emp_id',$referenceRecordId);
    	}
    	if(isset($where['login_status'])){
    		$activeStatus = $where['login_status'];
    		$query->where('t_is_active',$activeStatus);
    	}
    	if(isset($where['employment_status']) && (!empty($where['employment_status'])) ){
    		$employmentStatus = $where['employment_status'];
    		$query->whereIn('e_employment_status',$employmentStatus);
    	}
    	if(isset($where['state']) && (!empty($where['state'])) ){
    		$stateId = $where['state'];
    		$query->whereHas('cityCurrentInfo.stateMaster' , function($query) use($stateId) {
    			$query->where('i_id',$stateId);
    		});
    	}
    	if(isset($where['shift_record']) && (!empty($where['shift_record'])) ){
    		$shiftRecordId = $where['shift_record'];
    		$query->where('i_shift_id',$shiftRecordId);
    	}
    	if(isset($where['probation_id']) && (!empty($where['probation_id'])) ){
    		$probationRecordId = $where['probation_id'];
    		$query->where('i_probation_period_id',$probationRecordId);
    	}
    	if(isset($where['notice_period_id']) && (!empty($where['notice_period_id'])) ){
    		$noticePeriodRecordId = $where['notice_period_id'];
    		$query->where('i_notice_period_id',$noticePeriodRecordId);
    	}
    	if(isset($where['weekly_off_id']) && (!empty($where['weekly_off_id'])) ){
    		$weeklyOffRecordId = $where['weekly_off_id'];
    		$query->where('i_weekoff_id',$weeklyOffRecordId);
    	}
    	if(isset($where['bank_id']) && (!empty($where['bank_id'])) ){
    		$bankRecordId = $where['bank_id'];
    		$query->where('i_bank_id',$bankRecordId);
    	}
    	if(isset($where['leader_name']) && (!empty($where['leader_name'])) ){
    		$leaderId = $where['leader_name'];
    		$query->where('i_leader_id',$leaderId);
    	}
    	 
    	if(isset($where['weekly_off_record']) && (!empty($where['weekly_off_record'])) ){
    		$shiftRecordId = $where['weekly_off_record'];
    		$query->where('i_weekoff_id',$shiftRecordId);
    	}
    	 
    	if(isset($where['probation_to_date']) && (!empty($where['probation_to_date'])) ){
    		$probationToDate = dbDate($where['probation_to_date']);
    		$query->whereRaw("(  dt_probation_end_date <= '".$probationToDate."'  or dt_probation_end_date <= '".$probationToDate."'  )");
    	}
    	if(isset($where['notice_period_start_date']) && (!empty($where['notice_period_start_date'])) ){
    		$noticePeriodFromDate = dbDate($where['notice_period_start_date']);
    		$query->whereRaw("(  dt_notice_period_start_date >= '".$noticePeriodFromDate."'  or dt_notice_period_end_date >= '".$noticePeriodFromDate."'  )");
    	}
    	if(isset($where['notice_period_end_date']) && (!empty($where['notice_period_end_date'])) ){
    		$noticePeriodToDate = dbDate($where['notice_period_end_date']);
    		$query->whereRaw("(  dt_notice_period_start_date <= '".$noticePeriodToDate."'  or dt_notice_period_end_date <= '".$noticePeriodToDate."'  )");
    	}
    	 
    	 
    	if(isset($where['start_working_date']) && (!empty($where['start_working_date'])) ){
    		$startWorkingDate = dbDate($where['start_working_date']);
    		$query->whereRaw("(  dt_notice_period_end_date >= '".$startWorkingDate."'  or dt_notice_period_end_date >= '".$startWorkingDate."'  )");
    	}
    	 
    	if(isset($where['end_working_date']) && (!empty($where['end_working_date'])) ){
    		$endWorkingDate = dbDate($where['end_working_date']);
    		$query->whereRaw("(  dt_notice_period_end_date <= '".$endWorkingDate."'  or dt_notice_period_end_date <= '".$endWorkingDate."'  )");
    	}
    	if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    
    		$searchString = ( $likeData['searchBy'] );
    
    		$allLikeColumns = [ 'v_employee_code', 'v_employee_full_name', 'v_contact_no', 'v_outlook_email_id','v_employee_name','v_personal_email_id','v_contact_no','v_aadhar_no','v_pan_no','v_bank_account_no','v_bank_account_ifsc_code','v_uan_no','v_current_address_line_first','v_current_address_line_second','v_current_address_pincode','v_permanent_address_line_first','v_permanent_address_line_second','v_permanent_address_pincode'];
    
    		$query->where(function($q) use ($allLikeColumns,$searchString){
    			foreach($allLikeColumns as $key => $allLikeColumn){
    				$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    			}
    		});
    				
    	}
    
    	if( isset($where ['order_by']) ){
    		if(!empty($where ['order_by'])){
    			foreach($where ['order_by'] as  $key => $value){
    				$query->orderBy($key, (!empty($value) ? $value : 'DESC' ) );
    			}
    		}
    	} else {
    		$query->orderBy('i_id', "DESC" ) ;
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
