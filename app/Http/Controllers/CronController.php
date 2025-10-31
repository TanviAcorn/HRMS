<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BaseModel;
use App\Models\UploadDailyAttendance;
use Illuminate\Database\Eloquent\Model;
use App\EmployeeModel;
use Illuminate\Support\Facades\Log;
use DB;
use App\Models\EmployeeResignHistory;
use App\Helpers\Twt\Wild_tiger;
use App\Models\ApiAttendanceInfo;
use App\MyAttendanceModel;
use App\Models\EmployeeDesignationHistory;
use App\Models\AttendanceSummaryModel;
use App\MyLeaveModel;
use App\HolidayMasterModel;
use App\Models\ReviseSalaryMaster;
use App\Models\EmployeeSalaryModel;
use App\Models\EmployeeSalaryDetailModel;
use App\Models\PunchModel;
use App\Models\MissingPunchInfo;
use App\Models\LeaveBalanceModel;

class CronController extends GuestController
{
	public function __construct(){
	
		parent::__construct();
		$this->employeeId = 1;
		$this->curdModel =  New BaseModel();
		$this->tableName = config('constants.EMPLOYEE_ATTENDANCE_TABLE');
	
	}
	public function addAttendance(){
	
		$employeeId = $this->employeeId;
		$startDate = '2022-02-01';
		$startTime = "10:00:00";
		$endTime = "07:30:00";
		
		$lastFourMonthDates = getDatesFromRange($startDate,'2022-11-30');
		
		if(!empty($lastFourMonthDates)){
			foreach ($lastFourMonthDates as $lastFourMonthDate){
				$recordData = [];
				$recordData['i_employee_id'] = $employeeId;
				$recordData['dt_date'] = $lastFourMonthDate;
				$recordData['d_start_time'] = $startTime;
				$recordData['d_end_time'] = $endTime;
				$recordData['d_original_start_time'] = $startTime;
				$recordData['d_original_end_time'] = $endTime;
				$recordInsert = $this->curdModel->insertTableData( $this->tableName , $recordData  );
			}
		}
	}
	
	
	
	public function sendBirthdayReminder(){
		Log::info("sendBirthdayReminder = " .date('Y-m-d H:i:s'));
		$this->crudModel =  New EmployeeModel();
		$recordDetails = EmployeeModel::whereRaw("e_employment_status != '".config('constants.RELIEVED_EMPLOYMENT_STATUS')."'  and t_is_deleted = 0 and MONTH(dt_birth_date) = MONTH(NOW()) AND DAY(dt_birth_date) = DAY(NOW())")->get();
		if(!empty($recordDetails)){
			foreach($recordDetails as $recordDetail){
				if( isset($recordDetail->v_outlook_email_id) && (!empty($recordDetail->v_outlook_email_id)) ){
					$mailData = [];
					$mailData['employeeName'] =  $recordDetail->v_employee_full_name;
					
					$mailTemplate = view( $this->mailTemplateFolderPath .  'birthday', $mailData)->render();
					
					//echo $mailTemplate;echo "<br><br><br>";
					
					$subject = trans('messages.happy-birthday-mail-subject');
					//echo $subject;echo "<br><br><br>";
					
					$emailHistoryData = [];
					$emailHistoryData['i_login_id'] = $recordDetail->i_login_id;
					$emailHistoryData['v_event'] = "Birthday";
					$emailHistoryData['v_receiver_email'] = $recordDetail->v_outlook_email_id;
					$emailHistoryData['v_subject'] = $subject;
					$emailHistoryData['v_mail_content'] = $mailTemplate;
					$emailHistoryData['v_notification_title'] = $subject;
					
					$insertEmail = $this->crudModel->insertTableData(config('constants.EMAIL_HISTORY_TABLE'), $emailHistoryData);
					
					
					$config['mailData'] = $mailData;
					$config['viewName'] =  $this->mailTemplateFolderPath .  'birthday' ;
					$config['v_mail_content'] = $mailTemplate;
					$config['subject'] = $subject;
					$config['to'] = $recordDetail->v_outlook_email_id;
// Build CC list: Reporting Manager + static CCs
$cc = [];

// Add reporting manager if present and has an email
if (!empty($recordDetail->i_reporting_manager_id)) {
    $rm = \App\EmployeeModel::find($recordDetail->i_reporting_manager_id);
    if ($rm && !empty($rm->v_outlook_email_id) && $rm->v_outlook_email_id !== $recordDetail->v_outlook_email_id) {
        $cc[] = $rm->v_outlook_email_id;
    }
}

// Also include a static CC for notifications
$cc[] = 'Kishor.Dholwani@acornuniversalconsultancy.com';

// Set CC if we have any addresses
if (!empty($cc)) {
    $config['cc'] = array_unique($cc);
}				
					$sendMail = [];
					$mailSendError = null;
					try{
						$sendMail = sendMailSMTP($config);
					}catch(\Exception $e){
						$mailSendError = $e->getMessage();
					}
					
					$updateEmailData = [];
					if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
						$updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
					} else {
						$updateEmailData['e_status'] = config('constants.FAILED_STATUS');
						$updateEmailData['v_response'] = $mailSendError;
					}
					
					$this->crudModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateEmailData , [ 'i_id' => $insertEmail ] );
				}
			}
		}
	}
	
	public function sendAnniversaryReminder(){
		Log::info("sendAnniversaryReminder = " .date('Y-m-d H:i:s'));
		$this->crudModel =  New EmployeeModel();
		$recordDetails = EmployeeModel::whereRaw("e_employment_status != '".config('constants.RELIEVED_EMPLOYMENT_STATUS')."'  and t_is_deleted = 0 and dt_joining_date != CURRENT_DATE and MONTH(dt_joining_date) = MONTH(NOW()) AND DAY(dt_joining_date) = DAY(NOW())")->get();
		if(!empty($recordDetails)){
			$allNumbers = convertNumberIntoWords();
			foreach($recordDetails as $recordDetail){
				if( isset($recordDetail->v_outlook_email_id) && (!empty($recordDetail->v_outlook_email_id)) ){
					$mailData = [];
					$mailData['employeeName'] =  $recordDetail->v_employee_full_name;
					$noOfYear = date('Y') - date('Y' ,strtotime($recordDetail->dt_joining_date));
					$mailData['noOfYear'] = ( isset($allNumbers[$noOfYear]) ? $allNumbers[$noOfYear] : "" );
					
					$mailTemplate = view( $this->mailTemplateFolderPath .  'anniversary', $mailData)->render();
					
					$subject = trans('messages.anniversary-mail-subject');
					
					$emailHistoryData = [];
					$emailHistoryData['i_login_id'] = $recordDetail->i_login_id;
					$emailHistoryData['v_event'] = "Anniversary";
					$emailHistoryData['v_receiver_email'] = $recordDetail->v_outlook_email_id;
					$emailHistoryData['v_subject'] = $subject;
					$emailHistoryData['v_mail_content'] = $mailTemplate;
					$emailHistoryData['v_notification_title'] = $subject;
						
					$insertEmail = $this->crudModel->insertTableData(config('constants.EMAIL_HISTORY_TABLE'), $emailHistoryData);
						
						
					$config['mailData'] = $mailData;
					$config['viewName'] =  $this->mailTemplateFolderPath .  'anniversary' ;
					$config['v_mail_content'] = $mailTemplate;
					$config['subject'] = $subject;
					$config['to'] = $recordDetail->v_outlook_email_id;
// Build CC list: Reporting Manager + static CCs
$cc = [];

// Add reporting manager if present and has an email
if (!empty($recordDetail->i_reporting_manager_id)) {
    $rm = \App\EmployeeModel::find($recordDetail->i_reporting_manager_id);
    if ($rm && !empty($rm->v_outlook_email_id) && $rm->v_outlook_email_id !== $recordDetail->v_outlook_email_id) {
        $cc[] = $rm->v_outlook_email_id;
    }
}

// Also include a static CC for notifications
$cc[] = 'Kishor.Dholwani@acornuniversalconsultancy.com';

// Set CC if we have any addresses
if (!empty($cc)) {
    $config['cc'] = array_unique($cc);
}
						
					$sendMail = [];
					$mailSendError = null;
					try{
						$sendMail = sendMailSMTP($config);
					}catch(\Exception $e){
						//var_dump($e->getMessage());
						$mailSendError = $e->getMessage();
					}
					//var_dump($sendMail);
					$updateEmailData = [];
					if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
						$updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
					} else {
						$updateEmailData['e_status'] = config('constants.FAILED_STATUS');
						$updateEmailData['v_response'] = $mailSendError;
					}
						
					$this->crudModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateEmailData , [ 'i_id' => $insertEmail ] );
				}
			}
		}
	}
	
	
	public function updateEmployeeAttedance(){
		
		$uploadDailyAttendanceWhere = [];
		$uploadDailyAttendanceWhere['t_is_deleted'] = 0;
		$uploadDailyAttendanceWhere['t_is_manage'] = 0;
		
		
		$recordDetails = UploadDailyAttendance::where($uploadDailyAttendanceWhere)->get();
		
		if(!empty($recordDetails)){
			foreach($recordDetails as $recordDetail){
				
				$status = ( isset($recordDetail->v_status) ? $recordDetail->v_status : null ); 
				
				$data = [];
				$data['i_employee_id'] = ( isset($recordDetail->i_employee_id) ? $recordDetail->i_employee_id : null );
				$data['dt_date'] = ( isset($recordDetail->dt_attendance_date) ? $recordDetail->dt_attendance_date : null );
				$data['d_start_time'] = ( isset($recordDetail->v_in) ? $recordDetail->v_in : null );
				$data['d_end_time'] = ( isset($recordDetail->v_out) ? $recordDetail->v_out : null );
				$data['d_original_start_time'] = ( isset($recordDetail->i_employee_id) ? $recordDetail->i_employee_id : null );
				$data['d_original_end_time'] = ( isset($recordDetail->i_employee_id) ? $recordDetail->i_employee_id : null );
				$data['d_total_working_time'] = ( isset($recordDetail->i_employee_id) ? $recordDetail->i_employee_id : null );
				$data['d_break_start_time'] = ( isset($recordDetail->i_employee_id) ? $recordDetail->i_employee_id : null );
				$data['d_break_end_time'] = ( isset($recordDetail->i_employee_id) ? $recordDetail->i_employee_id : null );
				$data['d_total_break_time'] = ( isset($recordDetail->i_employee_id) ? $recordDetail->i_employee_id : null );
				$data['d_original_break_start_time'] = ( isset($recordDetail->i_employee_id) ? $recordDetail->i_employee_id : null );
				$data['d_original_break_end_time'] = ( isset($recordDetail->i_employee_id) ? $recordDetail->i_employee_id : null );
				$data['d_total_original_break_time'] = ( isset($recordDetail->i_employee_id) ? $recordDetail->i_employee_id : null );
			}
		}
		
	}
	
public function upcomingEndProbationPeriod(){
    
    $this->crudModel =  New EmployeeModel();
    $where = [];
    $where['t_is_deleted'] = 0;
    $where['dt_probation_end_date'] = date('Y-m-d' ,strtotime("+15 days"));
    $where['t_is_probation_completed'] = 0;
    $where['e_employment_status'] = config('constants.PROBATION_EMPLOYMENT_STATUS');
    
    // Eager load the leaderInfo relationship
    $getAllEmployeeDetails = EmployeeModel::with('leaderInfo')->where($where)->get();
    
    if(!empty($getAllEmployeeDetails)){
        foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
            
            $employeeName = $getAllEmployeeDetail->v_employee_full_name ?? 'User';
            $employeeId = $getAllEmployeeDetail->v_employee_code ?? 'Id';
            
            // Get reporting manager's details
            $supervisorName = $getAllEmployeeDetail->leaderInfo->v_employee_full_name ?? config('constants.SYSTEM_ADMIN_NAME');
            $reportingManagerEmail = $getAllEmployeeDetail->leaderInfo->v_outlook_email_id ?? null;
            
            // Set email recipients - include both system admin and reporting manager
            $toEmails = [config('constants.SYSTEM_ADMIN_EMAIL')];
            if ($reportingManagerEmail) {
                $toEmails[] = $reportingManagerEmail;
            }
            
            $mailData = [];
            $mailData['employeeName'] = $employeeName;
            $mailData['employeeCode'] = $employeeId;
            $mailData['supervisorName'] = $supervisorName;
            
            $mailTemplate = view($this->mailTemplateFolderPath . 'upcoming-probation-period-mail', $mailData)->render();
            $viewName = $this->mailTemplateFolderPath . 'upcoming-probation-period-mail';
            $subject = trans('messages.upcoming-probation-period-mail-subject', [
                'employeeId' => $employeeId,
                'employeeName' => $employeeName
            ]);
            
            // Send email to each recipient
            foreach ($toEmails as $email) {
                $emailHistoryData = [];
                $emailHistoryData['i_login_id'] = config('constants.ADMIN_LOGIN_ID');
                $emailHistoryData['v_event'] = "Probation End";
                $emailHistoryData['v_receiver_email'] = $email;
                $emailHistoryData['v_subject'] = $subject;
                $emailHistoryData['v_mail_content'] = $mailTemplate;
                $emailHistoryData['v_notification_title'] = $subject;
                
                $insertEmail = $this->crudModel->insertTableData(
                    config('constants.EMAIL_HISTORY_TABLE'), 
                    $emailHistoryData
                );
                
                $config = [];
                $config['mailData'] = $mailData;
                $config['viewName'] = $viewName;
                $config['v_mail_content'] = $mailTemplate;
                $config['subject'] = $subject;
                $config['to'] = $email;
                
                $sendMail = [];
                $mailSendError = null;
                try {
                    $sendMail = sendMailSMTP($config);
                } catch(\Exception $e) {
                    $mailSendError = $e->getMessage();
                }
                
                $updateEmailData = [];
                if(isset($sendMail['status']) && ($sendMail['status'] != false)) {
                    $updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
                } else {
                    $updateEmailData['e_status'] = config('constants.FAILED_STATUS');
                    $updateEmailData['v_response'] = $mailSendError;
                }
                
                $this->crudModel->updateTableData(
                    config('constants.EMAIL_HISTORY_TABLE'), 
                    $updateEmailData, 
                    ['i_id' => $insertEmail]
                );
            }
        }
    }
}
	public function upcomingEndNoticePeriod(){
	
		$this->crudModel =  New EmployeeModel();
		$where = [];
		$where['t_is_deleted'] = 0;
		$where['dt_notice_period_end_date'] = date('Y-m-d' ,strtotime("+7 days"));
		$where['e_employment_status'] = config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS');
	
		$getAllEmployeeDetails = EmployeeModel::where($where)->get();
		if(!empty($getAllEmployeeDetails)){
			foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
	
				$employeeName = ( isset($getAllEmployeeDetail->v_employee_full_name) ? $getAllEmployeeDetail->v_employee_full_name : 'User' );
				$employeeId = ( isset($getAllEmployeeDetail->v_employee_code) ? $getAllEmployeeDetail->v_employee_code : 'Id' );
				$supervisorName = config('constants.SYSTEM_ADMIN_NAME');
	
				$mailData = [];
				$mailData['employeeName'] =  $employeeName;
				$mailData['employeeCode'] =  $employeeId;
				$mailData['supervisorName'] =  $supervisorName;
	
				$mailTemplate = view( $this->mailTemplateFolderPath .  'upcoming-notice-period-mail', $mailData)->render();
				$viewName = $this->mailTemplateFolderPath .  'upcoming-notice-period-mail';
				$subject = trans('messages.upcoming-notice-period-mail-subject' , [ 'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
	
	
	
				$emailHistoryData = [];
				$emailHistoryData['i_login_id'] = config('constants.ADMIN_LOGIN_ID');
				$emailHistoryData['v_event'] = "Probation End";
				$emailHistoryData['v_receiver_email'] = config('constants.SYSTEM_ADMIN_EMAIL');
				$emailHistoryData['v_subject'] = $subject;
				$emailHistoryData['v_mail_content'] = $mailTemplate;
				$emailHistoryData['v_notification_title'] = $subject;
	
				$insertEmail = $this->crudModel->insertTableData(config('constants.EMAIL_HISTORY_TABLE'), $emailHistoryData);
	
	
				$config['mailData'] = $mailData;
				$config['viewName'] =  $viewName ;
				$config['v_mail_content'] = $mailTemplate;
				$config['subject'] = $subject;
				$config['to'] = config('constants.SYSTEM_ADMIN_EMAIL');;
	
				$sendMail = [];
				$mailSendError = null;
				try{
					$sendMail = sendMailSMTP($config);
				}catch(\Exception $e){
					$mailSendError = $e->getMessage();
				}
	
				$updateEmailData = [];
				if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
					$updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
				} else {
					$updateEmailData['e_status'] = config('constants.FAILED_STATUS');
					$updateEmailData['v_response'] = $mailSendError;
				}
	
				$this->crudModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateEmailData , [ 'i_id' => $insertEmail ] );
	
	
			}
		}
	}
	
	
	public function updateEmployeeNoticePeriodStatus(){
		Log::info("startEmployeeSuspension = " .date('Y-m-d H:i:s'));
		$this->crudModel =  New EmployeeModel();
		$getAllEmployeeDetails = EmployeeModel::where('t_is_deleted',0)->where('dt_notice_period_start_date' , date('Y-m-d'))->get();
		if(!empty($getAllEmployeeDetails)){
			foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
				$updateEmployeeData = [];
				$updateEmployeeData['e_employment_status'] = config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS');
				$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateEmployeeData , [ 'i_id' => $getAllEmployeeDetail->i_id  ] );
			}
		}
	}
	
	
	public function startEmployeeSuspension(){
		Log::info("startEmployeeSuspension = " .date('Y-m-d H:i:s'));
		$this->crudModel =  New EmployeeModel();
		$getAllSuspendEmployeeDetails = EmployeeModel::where('t_is_deleted',0)->where('dt_suspended_start_date' , date('Y-m-d'))->get();
		if(!empty($getAllSuspendEmployeeDetails)){
			foreach($getAllSuspendEmployeeDetails as $getAllSuspendEmployeeDetail){
				$updateEmployeeData = [];
				$updateEmployeeData['t_is_suspended'] = 1;
				$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateEmployeeData , [ 'i_id' => $getAllSuspendEmployeeDetail->i_id , 't_is_suspended' => 0 ] );
			}
		}
	}
	
	public function endEmployeeSuspension(){
		Log::info("endEmployeeSuspension = " .date('Y-m-d H:i:s'));
		$this->crudModel =  New EmployeeModel();
		$lastDate = date('Y-m-d', strtotime("-1 day"));
		$getAllSuspendEmployeeDetails = EmployeeModel::where('t_is_deleted',0)->where('dt_suspended_end_date' , $lastDate )->get();
		if(!empty($getAllSuspendEmployeeDetails)){
			foreach($getAllSuspendEmployeeDetails as $getAllSuspendEmployeeDetail){
				
				$updateEmployeeData = [];
				$updateEmployeeData['t_is_suspended'] = 0;
				$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateEmployeeData  , [ 'i_id' => $getAllSuspendEmployeeDetail->i_id ] );
			}
		}
	}
	
	public function updateEmployeeRelivedStatus(){
		Log::info("updateEmployeeRelivedStatus = " .date('Y-m-d H:i:s'));
		$this->crudModel =  New EmployeeModel();
		$lastDate = date('Y-m-d', strtotime("-1 day"));
		
		$employeeWhere = null;
		$employeeWhere = " t_is_deleted = 0 and  e_employment_status = '".config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')."' and date(dt_notice_period_end_date) <=  '".$lastDate."'";
		
		
		$getAllSuspendEmployeeDetails = EmployeeModel::whereRaw($employeeWhere)->get();
		//echo "<pre>";print_r($getAllSuspendEmployeeDetails);
		if(!empty($getAllSuspendEmployeeDetails)){
			foreach($getAllSuspendEmployeeDetails as $getAllSuspendEmployeeDetail){
			
				$getLateResignInfo = EmployeeResignHistory::where('i_employee_id' ,$getAllSuspendEmployeeDetail->i_id )->where('t_is_deleted' , 0 )->orderBy('i_id' , 'desc')->limit(1)->first();
				
				
				$upcomingLeaderInfo = (!empty($getLateResignInfo->v_upcoming_leader_info) ? json_decode($getLateResignInfo->v_upcoming_leader_info,true) : [] );
				//echo "<pre>";print_r($upcomingLeaderInfo);
				$updateEmployeeData = [];
				$updateEmployeeData['e_employment_status'] = config('constants.RELIEVED_EMPLOYMENT_STATUS');
				$updateEmployeeData['dt_release_date'] = date('Y-m-d');
				$updateEmployeeData['t_is_active'] = 0;
				$updateEmployeeData['i_role_permission'] = null;
				$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateEmployeeData  , [ 'i_id' => $getAllSuspendEmployeeDetail->i_id ] );
				//echo $this->crudModel->last_query();echo "<br><br><br>";
				$getAllChildEmployeeDetails = 
				
				removeSession($getAllSuspendEmployeeDetail->i_login_id);
				
				$getAllChildEmployeeDetails = EmployeeModel::where('i_leader_id' , $getAllSuspendEmployeeDetail->i_id )->where('e_employment_status' , '!=' , config('constants.RELIEVED_EMPLOYMENT_STATUS'))->get();
				$specifyUpcoingLeaderEmployeeId = (!empty($upcomingLeaderInfo) ?  array_column($upcomingLeaderInfo,'employee_id') : [] ); 
				
				//echo "<pre>";print_r($getAllChildEmployeeDetails);
				
				if(!empty($getAllChildEmployeeDetails)){
					foreach($getAllChildEmployeeDetails as $getAllChildEmployeeDetail){
						if(in_array($getAllChildEmployeeDetail->i_id , $specifyUpcoingLeaderEmployeeId ) ){
							$searchKey = array_search($getAllChildEmployeeDetail->i_id  , $specifyUpcoingLeaderEmployeeId ) ;
							if(strlen($searchKey) > 0 ){
								$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), ['i_leader_id' => isset($upcomingLeaderInfo[$searchKey]['leader_id']) ? $upcomingLeaderInfo[$searchKey]['leader_id'] : config('constants.ADMIN_LOGIN_ID')  ]  , [ 'i_id' => $getAllChildEmployeeDetail->i_id ] );
								//echo $this->crudModel->last_query();echo "<br><br><br>";
							} else {
								$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), ['i_leader_id' => config('constants.ADMIN_LOGIN_ID')   ]  , [ 'i_id' => $getAllChildEmployeeDetail->i_id ] );
								//echo $this->crudModel->last_query();echo "<br><br><br>";
							}
						} else {
							$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), ['i_leader_id' => config('constants.ADMIN_LOGIN_ID')   ]  , [ 'i_id' => $getAllChildEmployeeDetail->i_id ] );
							//echo $this->crudModel->last_query();echo "<br><br><br>";
						}
					}
				}
			}
		}
	}
	
	public function addMonthlyPaidLeaveBalance( $particularDate = null ){
		Log::info("addMonthlyPaidLeaveBalance = " .date('Y-m-d H:i:s'));
		$this->crudModel = new BaseModel();
		$employeeWhere = [];
		//$employeeWhere['e_employment_status'] = config("constants.CONFIRMED_EMPLOYMENT_STATUS");
		$employeeWhere['t_is_deleted'] = 0;
		$allEmployeeDetails = EmployeeModel::where($employeeWhere)->whereIn('e_employment_status' ,  [ config("constants.CONFIRMED_EMPLOYMENT_STATUS") , config("constants.NOTICE_PERIOD_EMPLOYMENT_STATUS")  ] )->get();
		//echo "<pre>";print_r($allEmployeeDetails);die;
		$leaveAddedDate = (!empty($particularDate) ? $particularDate :  date("Y-m-d")  );
		$effectiveDate = $leaveAddedDate;
		$leaveTypeId = config("constants.PAID_LEAVE_TYPE_ID");
		$noOfLeave = 1;
		if(!empty($allEmployeeDetails)){
			DB::beginTransaction();
			$result = false;
			try{
				foreach($allEmployeeDetails as $allEmployeeDetail){
					$employeeId = $allEmployeeDetail->i_id;
					$leaveBalanceData = [];
					$leaveBalanceData['i_employee_id'] = $employeeId;
					$leaveBalanceData['i_leave_type_id'] = $leaveTypeId;
					$leaveBalanceData['dt_effective_date'] = $effectiveDate;
					$leaveBalanceData['d_no_of_days_assign'] = $noOfLeave;
					$leaveBalanceData['v_remark'] = "Leave Balance";
				
					$checkLeaveAssignHistoryWhere = [];
					$checkLeaveAssignHistoryWhere['i_employee_id'] = $employeeId;
					$checkLeaveAssignHistoryWhere['i_leave_type_id'] = $leaveTypeId;
					$checkLeaveAssignHistoryWhere['dt_effective_date'] = $effectiveDate;
					$checkLeaveAssignHistoryWhere['t_is_deleted'] = 0;
					$checkLeaveAssignHistory = $this->crudModel->getSingleRecordById(config('constants.LEAVE_ASSIGN_HISTORY_TABLE'), [ 'i_id' ] , $checkLeaveAssignHistoryWhere );
				
					if(empty($checkLeaveAssignHistory)){
						$insertLeaveASsign = $this->crudModel->insertTableData(config('constants.LEAVE_ASSIGN_HISTORY_TABLE'), $leaveBalanceData );
				
						$leaveBalanceWhere  = [];
						$leaveBalanceWhere['i_employee_id'] = $employeeId;
						$leaveBalanceWhere['i_leave_type_id'] = $leaveTypeId;
						$leaveBalanceWhere['t_is_deleted != '] = 1;
						$checkLeaveAssigned = $this->crudModel->getSingleRecordById(config('constants.LEAVE_BALANCE_TABLE') , [ 'i_id' ] ,  $leaveBalanceWhere );
				
						if(!empty($checkLeaveAssigned)){
							$updateLeaveBalance = [];
							$updateLeaveBalance['d_current_balance'] = DB::raw("CONCAT(d_current_balance+".$noOfLeave.")");
							$this->crudModel->updateTableData(config('constants.LEAVE_BALANCE_TABLE'), $updateLeaveBalance , [ 'i_id' =>  $checkLeaveAssigned->i_id  ] );
							//echo $this->crudModel->last_query();echo "<br><br><br>";
						} else {
							$insertLeaveBalance = [];
							$insertLeaveBalance['i_employee_id'] = $employeeId;
							$insertLeaveBalance['i_leave_type_id'] = $leaveTypeId;
							$insertLeaveBalance['d_current_balance'] = $noOfLeave;
								
							$this->crudModel->insertTableData(config('constants.LEAVE_BALANCE_TABLE'), $insertLeaveBalance );
							//echo $this->crudModel->last_query();echo "<br><br><br>";
						}
					}
				}
				$result = true;
			}catch(\Exception $e){
				$result = false;
				DB::rollback();
			}
			
			if( $result != false ){
				DB::commit();
			} else {
				DB::rollback();
			}
			
		}
	}
	
	public function assignUnPaidLeaveType(){
		Log::info("addMonthlyPaidLeaveBalance = " .date('Y-m-d H:i:s'));
		$this->crudModel = new BaseModel();
		$employeeWhere = [];
		$employeeWhere['t_is_deleted'] = 0;
		$allEmployeeDetails = EmployeeModel::where($employeeWhere)->get();
		//echo "<pre>";print_r($allEmployeeDetails);die;
		$leaveTypeId = config("constants.UNPAID_LEAVE_TYPE_ID");
		$noOfLeave = 1;
		if(!empty($allEmployeeDetails)){
			DB::beginTransaction();
			$result = false;
			try{
				foreach($allEmployeeDetails as $allEmployeeDetail){
					$employeeId = $allEmployeeDetail->i_id;
					
					$leaveBalanceWhere  = [];
					$leaveBalanceWhere['i_employee_id'] = $employeeId;
					$leaveBalanceWhere['i_leave_type_id'] = $leaveTypeId;
					$leaveBalanceWhere['t_is_deleted != '] = 1;
					$checkLeaveAssigned = $this->crudModel->getSingleRecordById(config('constants.LEAVE_BALANCE_TABLE') , [ 'i_id' ] ,  $leaveBalanceWhere );
					
					if(empty($checkLeaveAssigned)){
						$insertLeaveBalance = [];
						$insertLeaveBalance['i_employee_id'] = $employeeId;
						$insertLeaveBalance['i_leave_type_id'] = $leaveTypeId;
						$insertLeaveBalance['d_current_balance'] = 0;
						
						$this->crudModel->insertTableData(config('constants.LEAVE_BALANCE_TABLE'), $insertLeaveBalance );
					}
				}
				$result = true;
			}catch(\Exception $e){
				$result = false;
				DB::rollback();
			}
				
			if( $result != false ){
				DB::commit();
			} else {
				DB::rollback();
			}
				
		}
	}
	
	public function updateExistingEmployeeWorkingDate(){
		Log::info("addMonthlyPaidLeaveBalance = " .date('Y-m-d H:i:s'));
		$this->crudModel = new BaseModel();
		$employeeWhere = [];
		$employeeWhere['t_is_deleted'] = 0;
		$allEmployeeDetails = EmployeeModel::where($employeeWhere)->get();
		//echo "<pre>";print_r($allEmployeeDetails);die;
		$leaveTypeId = config("constants.UNPAID_LEAVE_TYPE_ID");
		$noOfLeave = 1;
		if(!empty($allEmployeeDetails)){
			DB::beginTransaction();
			$result = false;
			try{
				foreach($allEmployeeDetails as $allEmployeeDetail){
					$employeeId = $allEmployeeDetail->i_id;
						
					$getHistoryRecordWhere  = [];
					$getHistoryRecordWhere['i_employee_id'] = $employeeId;
					$getHistoryRecordWhere['e_record_type'] = config('constants.WEEK_OFF_RECORD_TYPE');
					$getHistoryRecordWhere['t_is_deleted != '] = 1;
					$getHistoryRecordWhere['order_by'] =  [ 'i_id' => 'asc' ];
					$getHistoryRecordInfo = $this->crudModel->getSingleRecordById(config('constants.EMPLOYEE_DESIGNATION_HISTORY') , [ 'i_id' , 'dt_start_date' ] ,  $getHistoryRecordWhere );
					
					if(!empty($getHistoryRecordInfo)){
						$this->crudModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), [ 'dt_week_off_effective_date' => $getHistoryRecordInfo->dt_start_date  ] , [ 'i_id' => $employeeId ] );
					}
				}
				$result = true;
			}catch(\Exception $e){
				$result = false;
				DB::rollback();
			}
	
			if( $result != false ){
				DB::commit();
				echo "done";
			} else {
				DB::rollback();
			}
	
		}
	}
	
	public function fetchEmployeeDailyAttendance( $startDate = null  ){
		$attendanceDate = (!empty($startDate) ? date('dmY', strtotime($startDate)) : date('dmY'));
		ini_set("memory_limit", "-1");
		ini_set("max_execution_time", "3600");
		//$url = "http://111.93.87.11:818/COSEC/api.svc/v2/attendance-daily?action=get;format=json;date-range=$attedanceDate-$attedanceDate;field-name=userid,username,orgid,processdate,punch1_time,outpunch_time,firsthalf,secondhalf,lunchstart,lunchend,lunchduration_hhmm,worktime_hhmm";
		//$url = "http://103.206.209.119/COSEC/api.svc/v2/attendance-daily?action=get;format=json;range=user;id=176:date-range=$attedanceDate-$attedanceDate;field-name=orgid,processdate,punch1_time,outpunch_time,firsthalf,secondhalf,lunchstart,lunchend,lunchduration_hhmm,worktime_hhmm;";
		//echo $url;
		//$url = "http://111.93.87.11:818/COSEC/api.svc/v2/attendance-daily?action=get;date-range=11072023-11072023;format=json;field-name=userid,username,orgid,workingshift,processdate,punch1_time,punch2_time,punch3_time,punch4_time,firsthalf,secondhalf,worktime_hhmm";
		$this->apiAttenndance($attendanceDate);
		
		if( strtotime( 'now' ) <= strtotime( date('15:00')) ){
			$convertAttendanceEndDate = \DateTime::createFromFormat('dmY', $attendanceDate);
			$newAttendanceEndDate =  $convertAttendanceEndDate->format('Y-m-d');
			$attendanceEndDate = date('dmY', strtotime("-1 day" , strtotime($newAttendanceEndDate)));
			$this->apiAttenndance($attendanceEndDate);
		}
		
	}
	
	public function addEmployeeDailyAttendance( $attedanceDate = null , $redirectUrl = false ){
		$this->crudModel =  New MyAttendanceModel();
		$allDate = (!empty($attedanceDate) ? $attedanceDate :   date('Y-m-d') ) ;
		$getAllEmployeeDetails = EmployeeModel::where('t_is_deleted' , 0 )->where( 'dt_joining_date' ,'<=' , $allDate )->where('e_employment_status' , '!=' , config('constants.RELIEVED_EMPLOYMENT_STATUS'))->orderBy('v_employee_full_name' , 'asc')->get();
		
		
		if(strtotime($attedanceDate) >= strtotime(date('Y-m-d'))){
			//die("not allowed");
		}
		
		
		if(!empty($getAllEmployeeDetails)){
			
			$result = false;
			DB::beginTransaction();
			
			try{
				foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
					$employeeId = $getAllEmployeeDetail->i_id;
					$shiftLastUpdateDate = ( isset($getAllEmployeeDetail->dt_last_update_shift) ? $getAllEmployeeDetail->dt_last_update_shift : null );
					$weekOffLastUpdateDate = ( isset($getAllEmployeeDetail->dt_last_update_week_off) ? $getAllEmployeeDetail->dt_last_update_week_off : null );
					
					//$shiftLastUpdateDate = "2023-06-10";
					
					$currentShiftInfo = ( isset($getAllEmployeeDetail->shiftInfo->shiftTimingInfo) ? $getAllEmployeeDetail->shiftInfo->shiftTimingInfo[0] : [] );
					$currentWeekOffInfo = ( isset($getAllEmployeeDetail->weekOffInfo->weeklyOffDetail) ? $getAllEmployeeDetail->weekOffInfo->weeklyOffDetail : [] );
				
					$rowData = [];
					$weekDay = strtolower( date('l' , strtotime($allDate) ) );
						
					$alternateColumnName = 'v_'.$weekDay.'_alternate_off';
					$allColumnName = 'v_'.$weekDay.'_all_off';
					
					//echo "<pre> date= ";print_r($allDate);
					//echo "<pre> last update ";print_r($shiftLastUpdateDate);
					
					//echo "week day  = "; var_dump($weekDay);
					if( strtotime($shiftLastUpdateDate) <= strtotime($allDate) ){
						$rowData['t_original_start_time'] = ( isset($currentShiftInfo['v_'.$weekDay.'_start_time']) ? $currentShiftInfo['v_'.$weekDay.'_start_time'] : null );
						$rowData['t_original_end_time'] = ( isset($currentShiftInfo['v_'.$weekDay.'_end_time']) ? $currentShiftInfo['v_'.$weekDay.'_end_time'] : null );
						$rowData['t_original_break_start_time'] = ( isset($currentShiftInfo['v_'.$weekDay.'_break_start_time']) ? $currentShiftInfo['v_'.$weekDay.'_break_start_time'] : null );
						$rowData['t_original_break_end_time'] = ( isset($currentShiftInfo['v_'.$weekDay.'_break_end_time']) ? $currentShiftInfo['v_'.$weekDay.'_break_end_time'] : null );
					} else {
				
						$getParticularDayShiftWhere = "i_employee_id = '".$employeeId."' and t_is_deleted != 1 and '".$allDate."' between dt_start_date and dt_end_date";
						//echo $getParticularDayShiftWhere;
						$getParticularDayShiftDetails = EmployeeDesignationHistory::with( [ 'shiftInfo' , 'shiftInfo.shiftTimingInfo'  ] )->whereRaw($getParticularDayShiftWhere)->first();
						
						//echo "<pre> getParticularDayShiftDetails";print_r($getParticularDayShiftDetails);die;
						
						//echo "<pre> getParticularDayShiftDetails";print_r($getParticularDayShiftDetails);
						if(!empty($getParticularDayShiftDetails)){
							$getParticularDayShiftInfo  = ( isset($getParticularDayShiftDetails->shiftInfo->shiftTimingInfo) ? $getParticularDayShiftDetails->shiftInfo->shiftTimingInfo[0] : [] );
							$rowData['t_original_start_time'] = ( isset($getParticularDayShiftInfo['v_'.$weekDay.'_start_time']) ? $getParticularDayShiftInfo['v_'.$weekDay.'_start_time'] : null );
							$rowData['t_original_end_time'] = ( isset($getParticularDayShiftInfo['v_'.$weekDay.'_end_time']) ? $getParticularDayShiftInfo['v_'.$weekDay.'_end_time'] : null );
							$rowData['t_original_break_start_time'] = ( isset($getParticularDayShiftInfo['v_'.$weekDay.'_break_start_time']) ? $getParticularDayShiftInfo['v_'.$weekDay.'_break_start_time'] : null );
							$rowData['t_original_break_end_time'] = ( isset($getParticularDayShiftInfo['v_'.$weekDay.'_break_end_time']) ? $getParticularDayShiftInfo['v_'.$weekDay.'_break_end_time'] : null );
						}
					}
					//echo "<pre>";print_r($rowData);
					if(!empty($rowData)){
						$checkRecordWhere = [];
						$checkRecordWhere['i_employee_id'] = $employeeId;
						$checkRecordWhere['dt_date'] = $allDate;
						$checkRecordWhere['t_is_deleted'] = 0;
						$checkRecordExist = MyAttendanceModel::where($checkRecordWhere)->first();
							
						if(!empty($checkRecordExist)){
							$this->crudModel->updateTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData , [ 'i_id' => $checkRecordExist->i_id ] );
						} else {
							$rowData['i_employee_id'] = $employeeId;
							$rowData['dt_date'] = $allDate;
							$this->crudModel->insertTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData);
						}
					}
					$result = true;
				
				}
			}catch(\Exception $e){
				$result = false;
				DB::rollback();
				Log::info('error occured while daily attendance entry');
				Log::info($e->getMessage());
			}
			
			if( $result != false ){
				DB::commit();
				echo "success daily attendance entry";
				Log::info('success daily attendance entry');
				if( $redirectUrl != false ){
					Wild_tiger::setFlashMessage ('success', trans('messages.success-sync-attendance')  );
					return  redirect()->back();
				}
			} else {
				Log::info('error occured while daily attendance entry');
				echo "error occured while daily attendance entry";
				DB::rollback();
				if( $redirectUrl != false ){
					Wild_tiger::setFlashMessage ('danger', trans('messages.error-sync-attendance')   );
					return  redirect()->back();
				}
				
			}
		}
		if( $redirectUrl != false ){
			Wild_tiger::setFlashMessage ('danger', trans('messages.system-error')  );
			return redirect()->back();
		}
	}
	
	
	public function manualAttendanceEntry(){
		$date = "2023-08-20";
		$month = date('m' , strtotime($date));
		$year = date('Y' , strtotime($date));
			
		$employeeId = 50;
			
		$getLastMonthStartDate = attendanceStartDate($month, $year);
		$getLastMonthEndDate = attendanceEndDate($month, $year);
		
		$employeeAttendanceDetails = MyAttendanceModel::where('dt_date' , '>=' , $getLastMonthStartDate )->where('dt_date' , '<=' , $getLastMonthEndDate )->where('i_employee_id' , $employeeId)->get();
		
		$employeeAppliedLeaveDetails = [];
		$employeeAppliedLeaveQuery = MyLeaveModel::where('t_is_deleted' , 0 )->whereIn( 'e_status' , [ config('constants.APPROVED_STATUS')  ] )->whereIn(  'i_leave_type_id' ,   [ config('constants.PAID_LEAVE_TYPE_ID')  , config('constants.EARNED_LEAVE_TYPE_ID')  , config('constants.CARRY_FORWARD_LEAVE_TYPE_ID') , config('constants.UNPAID_LEAVE_TYPE_ID')  ] );
		if( $particularEmployeeId > 0 ) {
			$employeeAppliedLeaveQuery = MyLeaveModel::where('t_is_deleted' , 0 )->whereIn( 'e_status' , [ config('constants.PENDING_STATUS') , config('constants.APPROVED_STATUS')  ] )->whereIn(  'i_leave_type_id' ,   [ config('constants.PAID_LEAVE_TYPE_ID')  , config('constants.EARNED_LEAVE_TYPE_ID')  , config('constants.CARRY_FORWARD_LEAVE_TYPE_ID') , config('constants.UNPAID_LEAVE_TYPE_ID')  ] )->where('i_employee_id',$particularEmployeeId);
		}
		$employeeAppliedLeaveQuery->whereRaw("(  dt_leave_from_date >= '".$getLastMonthStartDate."'  or dt_leave_to_date >= '".$getLastMonthStartDate."'  )");
		$employeeAppliedLeaveQuery->whereRaw("(  dt_leave_from_date <= '".$getLastMonthEndDate."'  or dt_leave_to_date <= '".$getLastMonthEndDate."'  )");
		$employeeAppliedLeaveDetails =  $employeeAppliedLeaveQuery->get();
			
		$monthAllDates = Wild_tiger::getAllDateOfSalaryMonth($month , $year );
		
	}
	
	public function manageAttendanceSummary( $attedanceDate = null , $employeeId = null ){
		$this->crudModel =  New MyAttendanceModel();
		$date =  (!empty($attedanceDate) ? $attedanceDate :   date('Y-m-d') ); 
		//$date = '2023-08-20';
		if( in_array( date('d' , strtotime($date)) , [ 16 , 17 , 18 , 19 , 20  ] ) ){
			
			$particularEmployeeId = [];
			if(!empty($employeeId)){
				$particularEmployeeId = [ $employeeId ] ;
			}
			$this->commonAttendanceSummary($date ,  $particularEmployeeId );
		}
	}
	
	
	//approve penidng leaves
	
	public function apporvePendingLeave(){
	
		$startDate = attendanceStartDate($month, $year);
		$getLastMonthEndDate = attendanceEndDate($month, $year);
		
		$employeeAppliedLeaveDetails = [];
		$employeeAppliedLeaveQuery = MyLeaveModel::where('t_is_deleted' , 0 )->whereIn( 'e_status' , [ config('constants.PENDING_STATUS')  ] );
		$employeeAppliedLeaveQuery->whereRaw("(  dt_leave_from_date >= '".$startDate."'  or dt_leave_to_date >= '".$startDate."'  )");
		$employeeAppliedLeaveQuery->whereRaw("(  dt_leave_from_date <= '".$endDate."'  or dt_leave_to_date <= '".$endDate."'  )");
		$employeeAppliedLeaveDetails =  $employeeAppliedLeaveQuery->get();
		
		if(!empty($employeeAppliedLeaveDetails)){
			$result = true;
			DB::beginTransaction();
			
			try{
				foreach($employeeAppliedLeaveDetails as $employeeAppliedLeaveDetail){
				
					$updateLeaveData = [];
					$updateLeaveData['e_status'] = config("constants.APPROVED_STATUS");
					$updateLeaveData['v_approve_reject_remark'] = config('constants.AUTO_APPROVE_LEAVE_REMARK');
					$updateLeaveData['i_approved_by_id'] = config('constants.AUTO_APPROVE_USER_ID');
					$updateLeaveData['dt_approved_at'] = date("Y-m-d H:i:s");
					
					$this->crudModel->updateTableData(config('constants.APPLY_LEAVE_MASTER_TABLE'), $updateLeaveData , [ 'i_id' =>  $employeeAppliedLeaveDetail->i_id , 'e_status' =>  config('constants.PENDING_STATUS')  ] );
				}
				$result = true;
			}catch(\Exception $e){
				DB::rollback();
				$result = false;
			}
			
			if($result != false ){
				DB::commit();
				Log::info('approve Leaved Successfully');
				Log::info('approve Leaved Successfully');
			} else {
				DB::rollback();
				Log::info('Error occured while leave approve');
			}
			
			
		}
		
	}
	
	public function sendMissingLeaveReminder($particularDate = null ){
		die("welcome");
		$date = (!empty($particularDate) ? $particularDate :  date('Y-m-d'));
		$year = date('Y' , strtotime($date));
		$month = date('m'  , strtotime($date) );
		
		if( in_array( date('d' , strtotime($date)) , [ 10, 11, 12, 13 , 14, 15, 16 , 17 , 18 ] ) ){
			$attendanceStartDate = attendanceStartDate( $month , $year );
			$attendanceEndDate = attendanceEndDate( $month ,$year  );
			
			$monthAllDates = Wild_tiger::getAllDateOfSalaryMonth(  $month , $year );
			
			//var_dump($attendanceStartDate);echo "<br><br>";
			//var_dump($attendanceEndDate);echo "<br><br>";
			$employeeDetails = EmployeeModel::where('t_is_deleted' , 0 )->get();
			//$employeeDetails = EmployeeModel::where('t_is_deleted' , 0 )->where('i_id' , 18)->get();
			if(!empty($employeeDetails)){
				foreach($employeeDetails as $employeeDetail){
			
					$employeeId = $employeeDetail->i_id;
			
					$presentWhere = [];
					$presentWhere['i_employee_id'] = $employeeId;
					$attendanceDetails = MyAttendanceModel::where($presentWhere)->where('e_status' , '!=' , config('constants.PRESENT_STATUS'))->where('dt_date','>=',$attendanceStartDate)->where('dt_date','<=',$attendanceEndDate)->get();
			
					$employeeLeaveWhere = [];
					$employeeLeaveWhere['startDate'] = $attendanceStartDate;
					$employeeLeaveWhere['endDate'] = $attendanceEndDate;
					$employeeLeaveWhere['employeeId'] = [ $employeeId ];
					$employeeLeaveWhere['monthAllDates'] = $monthAllDates;
					$employeeLeaveDetails = $this->employeeLeaveInfo($employeeLeaveWhere);
					//echo "<pre>";print_r($employeeLeaveDetails);
					$allAppliedLeaveDates = ( isset($employeeLeaveDetails['allAppliedLeaveDates'][$employeeId]) ? $employeeLeaveDetails['allAppliedLeaveDates'][$employeeId] : [] );
			
					if(!empty($allAppliedLeaveDates) && !is_array($allAppliedLeaveDates)){
						//$allAppliedLeaveDates = [ $allAppliedLeaveDates ];
					}
					
					$suspendWhere = [];
					$suspendWhere['startDate'] = $attendanceStartDate;
					$suspendWhere['endDate'] = $attendanceEndDate;
					$suspendWhere['monthAllDates'] = $monthAllDates;
					$suspendWhere['employeeId'] = [ $employeeId ];
						
					$allSuspendDates = $this->getAllSuspendDateWiseRecords($suspendWhere);
					$allSuspendDates = ( isset($allSuspendDates[$employeeId]) ? $allSuspendDates[$employeeId] : [] ); 
			
					$getEmployeeWeekOffDates = $this->getEmployeeMonthlyWeekOff( ['employeeId' => $employeeId , 'month' => $year.'-'.$month.'-01' , 'attendanceView' => true ] );
					$monthAllWeekOfDates = ( isset($getEmployeeWeekOffDates['weekOffDates']) ? $getEmployeeWeekOffDates['weekOffDates'] : [] );
			
					$monthAllHolidayDates = $this->getAllHoliDayDetails($monthAllDates);
			
					///echo "<pre> monthAllHolidayDates";print_r($monthAllHolidayDates);
					///echo "<pre> allSuspendDates ";print_r($allSuspendDates);
					//echo "<pre> allAppliedLeaveDates";print_r($allAppliedLeaveDates);
					//echo "<pre>attendanceDetails";print_r($attendanceDetails);
					$missingLeaveDates = [];
					if(!empty($attendanceDetails)){
						foreach($attendanceDetails as $attendanceDetail){
							if( strtotime($attendanceDetail->dt_date) >= strtotime(date('Y-m-d'))){
								continue;
							}
							if( (!in_array($attendanceDetail->dt_date , $monthAllHolidayDates)) && (!in_array($attendanceDetail->dt_date , $monthAllWeekOfDates)) ){
								if( (!in_array($attendanceDetail->dt_date , $allAppliedLeaveDates)) && (!in_array($attendanceDetail->dt_date , $allSuspendDates)) ){
									$missingLeaveDates[] = $attendanceDetail->dt_date;
								}
							}
						}
					}
					//echo "<pre>";print_r($missingLeaveDates);
					if(!empty($missingLeaveDates)){
							
						$employeeName = ( isset($employeeDetail->v_employee_full_name) ? $employeeDetail->v_employee_full_name : 'User' );
						$employeeEmail = ( isset($employeeDetail->v_outlook_email_id) ? $employeeDetail->v_outlook_email_id : '' );
						$employeeId = ( isset($employeeDetail->v_employee_code) ? $employeeDetail->v_employee_code : 'Id' );
							
							
						$mailData = [];
						$mailData['employeeName'] =  $employeeName;
						$mailData['employeeCode'] =  $employeeId;
						$mailData['missingLeaveDates'] =  $missingLeaveDates;
							
							
						$mailTemplate = view( $this->mailTemplateFolderPath .  'missing-leave-reminder', $mailData)->render();
					//	echo $mailTemplate;die;	
						$viewName = $this->mailTemplateFolderPath .  'missing-leave-reminder';
						$subject = trans('messages.missing-leave-reminder-mail-subject' , [ 'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
							
							
							
						$emailHistoryData = [];
						$emailHistoryData['i_login_id'] = $employeeDetail->i_login_id;
						$emailHistoryData['v_event'] = "Missing Leave Reminder";
						$emailHistoryData['v_receiver_email'] = $employeeEmail;
						$emailHistoryData['v_subject'] = $subject;
						$emailHistoryData['v_mail_content'] = $mailTemplate;
						//$emailHistoryData['v_notification_title'] = $subject;
							
						$insertEmail = $this->curdModel->insertTableData(config('constants.EMAIL_HISTORY_TABLE'), $emailHistoryData);
							
							
						$config['mailData'] = $mailData;
						$config['viewName'] =  $viewName ;
						$config['v_mail_content'] = $mailTemplate;
						$config['subject'] = $subject;
						$config['to'] = $employeeEmail;;
							
						$sendMail = [];
						$mailSendError = null;
						try{
							$sendMail = sendMailSMTP($config);
						}catch(\Exception $e){
							$mailSendError = $e->getMessage();
						}
							
						$updateEmailData = [];
						if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
							$updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
						} else {
							$updateEmailData['e_status'] = config('constants.FAILED_STATUS');
							$updateEmailData['v_response'] = $mailSendError;
						}
							
						$this->curdModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateEmailData , [ 'i_id' => $insertEmail ] );
					}
				}
			}
		}
	}
	
	public function sendPendingLeaveReminder($particularDate = null ){
		die("sendPendingLeaveReminder");
		$date = (!empty($particularDate) ? $particularDate :  date('Y-m-d'));
		$year = date('Y' , strtotime($date));
		$month = date('m'  , strtotime($date) );
	
		if( in_array( date('d' , strtotime($date)) , [ 10, 11, 12, 13 , 14, 15, 16 , 17 , 18 ] ) ){
			$attendanceStartDate = attendanceStartDate( $month , $year );
			$attendanceEndDate = attendanceEndDate( $month ,$year  );
			
			$monthAllDates = Wild_tiger::getAllDateOfSalaryMonth(  $month , $year );
			//var_dump($attendanceStartDate);
			//var_dump($attendanceEndDate);
			$employeeDetails = EmployeeModel::with(['leaderInfo'])->where('t_is_deleted' , 0 )->get();
			//$employeeDetails = EmployeeModel::where('t_is_deleted' , 0 )->where('i_id' , 2)->get();
			$leaderWiseEmailDatas  = [];
			if(!empty($employeeDetails)){
				foreach($employeeDetails as $employeeDetail){
					$pendingApproveLeaveDates = [];	
					$employeeId = $employeeDetail->i_id;
						
					$employeeAppliedLeaveQuery = MyLeaveModel::where('t_is_deleted' , 0 )->whereIn( 'e_status' , [ config('constants.PENDING_STATUS')  ] )->whereIn(  'i_leave_type_id' ,   [ config('constants.PAID_LEAVE_TYPE_ID')  , config('constants.EARNED_LEAVE_TYPE_ID')  , config('constants.CARRY_FORWARD_LEAVE_TYPE_ID') , config('constants.UNPAID_LEAVE_TYPE_ID')  ] );
					$employeeAppliedLeaveQuery->where('i_employee_id',$employeeId);
					$employeeAppliedLeaveQuery->whereRaw("(  dt_leave_from_date >= '".$attendanceStartDate."'  or dt_leave_to_date >= '".$attendanceStartDate."'  )");
					$employeeAppliedLeaveQuery->whereRaw("(  dt_leave_from_date <= '".$attendanceEndDate."'  or dt_leave_to_date <= '".$attendanceEndDate."'  )");
					$employeeAppliedLeaveDetails =  $employeeAppliedLeaveQuery->get();
					
					if(!empty($employeeAppliedLeaveDetails)){
						foreach($employeeAppliedLeaveDetails as $employeeAppliedLeaveDetail){
							$pendingApproveLeaveDates[] = $employeeAppliedLeaveDetail->dt_leave_from_date;
						}
					}
					
					$employeeName = ( isset($employeeDetail->v_employee_full_name) ? $employeeDetail->v_employee_full_name : 'User' );
					$employeeEmail = ( isset($employeeDetail->v_outlook_email_id) ? $employeeDetail->v_outlook_email_id : '' );
					$employeeId = ( isset($employeeDetail->v_employee_code) ? $employeeDetail->v_employee_code : 'Id' );
					
					$rowData = [];
					$rowData['employeeName'] = $employeeName;
					$rowData['employeeCode'] = $employeeId;
					$rowData['pendingApproveLeaveDates'] = $pendingApproveLeaveDates;
					$rowData['leaderId'] = ( isset($employeeDetail->leaderInfo->i_id) && (!empty($employeeDetail->leaderInfo->i_id))  ? $employeeDetail->leaderInfo->i_id : '' );
					$rowData['leaderLoginId'] = ( isset($employeeDetail->leaderInfo->i_login_id) && (!empty($employeeDetail->leaderInfo->i_login_id))  ? $employeeDetail->leaderInfo->i_login_id : '' );
					$rowData['leaderName'] = ( isset($employeeDetail->leaderInfo->v_employee_full_name) && (!empty($employeeDetail->leaderInfo->v_employee_full_name))  ? $employeeDetail->leaderInfo->v_employee_full_name : '' );
					$rowData['leaderCode'] = ( isset($employeeDetail->leaderInfo->v_employee_code) && (!empty($employeeDetail->leaderInfo->v_employee_code))  ? $employeeDetail->leaderInfo->v_employee_code : '' );
					$rowData['leaderEmail'] = ( isset($employeeDetail->leaderInfo->v_outlook_email_id) && (!empty($employeeDetail->leaderInfo->v_outlook_email_id))  ? $employeeDetail->leaderInfo->v_outlook_email_id : '' );
					
					if( (!empty($pendingApproveLeaveDates)) &&  (!empty($rowData['leaderId'])) && (!empty($rowData['leaderEmail']))  ){
						$leaderWiseEmailDatas[$rowData['leaderName']][] = $rowData;
					}
				}
				
				//echo "<pre>";print_r($leaderWiseEmailDatas);die;
				if(!empty($leaderWiseEmailDatas)){
					foreach($leaderWiseEmailDatas as $leaderWiseEmailData){
						//echo "<pre>";print_r($leaderWiseEmailData);
						$leaderId = ( isset($leaderWiseEmailData[0]['leaderId']) ? $leaderWiseEmailData[0]['leaderId'] : 0 );
						$leaderLoginId = ( isset($leaderWiseEmailData[0]['leaderLoginId']) ? $leaderWiseEmailData[0]['leaderLoginId'] : 0 );
						$leaderEmail =  ( isset($leaderWiseEmailData[0]['leaderEmail']) ? $leaderWiseEmailData[0]['leaderEmail'] : "User" ) ;
						
						$mailData = [];
						$mailData['employeeName'] =  ( isset($leaderWiseEmailData[0]['leaderName']) ? $leaderWiseEmailData[0]['leaderName'] : "User" ) ;
						$mailData['employeeCode'] =  ( isset($leaderWiseEmailData[0]['leaderCode']) ? $leaderWiseEmailData[0]['leaderCode'] : "User" ) ;
						$mailData['pendingApproveLeaveDates'] =  $leaderWiseEmailData;
						
						//echo "<pre>";print_r($mailData);die;
						$mailTemplate = view( $this->mailTemplateFolderPath .  'pending-approve-leave-mail', $mailData)->render();
						//echo $mailTemplate;die;
						
						$viewName = $this->mailTemplateFolderPath .  'pending-approve-leave-mail';
						$subject = trans('messages.pending-approve-leave-reminder-mail-subject' , [ 'employeeId' => $mailData['employeeCode'] , 'employeeName' => $mailData['employeeName'] ] );
						
						
						
						$emailHistoryData = [];
						$emailHistoryData['i_login_id'] = $leaderLoginId;
						$emailHistoryData['v_event'] = "Pending Leave Approval Reminder";
						$emailHistoryData['v_receiver_email'] = $leaderEmail;
						$emailHistoryData['v_subject'] = $subject;
						$emailHistoryData['v_mail_content'] = $mailTemplate;
						//$emailHistoryData['v_notification_title'] = $subject;
						
						$insertEmail = $this->curdModel->insertTableData(config('constants.EMAIL_HISTORY_TABLE'), $emailHistoryData);
						
						
						$config['mailData'] = $mailData;
						$config['viewName'] =  $viewName ;
						$config['v_mail_content'] = $mailTemplate;
						$config['subject'] = $subject;
						$config['to'] = $leaderEmail;;
						
						$sendMail = [];
						$mailSendError = null;
						try{
							$sendMail = sendMailSMTP($config);
						}catch(\Exception $e){
							$mailSendError = $e->getMessage();
						}
						var_dump($sendMail);
						$updateEmailData = [];
						if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
							$updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
						} else {
							$updateEmailData['e_status'] = config('constants.FAILED_STATUS');
							$updateEmailData['v_response'] = $mailSendError;
						}
						
						$this->curdModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateEmailData , [ 'i_id' => $insertEmail ] );
					}	
				}
			}
		}
	}
	
	public function sendHoldSalaryReleaseReminderMail(){
		
		$date = date('Y-m-d' , strtotime("-2 year"));
		$date = date('Y-m-d', strtotime("+20 days" , strtotime($date)));
		
		$where = [];
		$where['e_hold_salary_status'] = config('constants.SELECTION_YES');
		$where['dt_joining_date'] = $date;
		$where['t_is_deleted'] = 0;
		
		$getAllEmployeeDetails = EmployeeModel::where( $where )->whereRaw("e_employment_status != '".config('constants.RELIEVED_EMPLOYMENT_STATUS')."' and  t_is_deleted = 0")->get();
		//var_dump(count($getAllEmployeeDetails));die;
		if(!empty($getAllEmployeeDetails)){
			foreach($getAllEmployeeDetails as $employeeDetail){
				
				$employeeName = ( isset($employeeDetail->v_employee_full_name) ? $employeeDetail->v_employee_full_name : 'User' );
				$employeeEmail = ( isset($employeeDetail->v_outlook_email_id) ? $employeeDetail->v_outlook_email_id : '' );
				$employeeId = ( isset($employeeDetail->v_employee_code) ? $employeeDetail->v_employee_code : 'Id' );
					
					
				$mailData = [];
				$mailData['employeeName'] =  $employeeName;
				$mailData['employeeCode'] =  $employeeId;
				
				$mailTemplate = view( $this->mailTemplateFolderPath .  'hold-salary-release-mail', $mailData)->render();
					
				$viewName = $this->mailTemplateFolderPath .  'hold-salary-release-mail';
				$subject = trans('messages.hold-salary-release-mail-subject' , [ 'employeeId' => $employeeId , 'employeeName' => $employeeName ] );
					
					
					
				$emailHistoryData = [];
				$emailHistoryData['i_login_id'] = config('constants.ADMIN_LOGIN_ID');
				$emailHistoryData['i_related_record_id'] = $employeeDetail->i_login_id;
				$emailHistoryData['v_event'] = "Hold Salary Reminder";
				$emailHistoryData['v_receiver_email'] = config('constants.SYSTEM_ADMIN_EMAIL');;
				$emailHistoryData['v_subject'] = $subject;
				$emailHistoryData['v_mail_content'] = $mailTemplate;
				//$emailHistoryData['v_notification_title'] = $subject;
					
				$insertEmail = $this->curdModel->insertTableData(config('constants.EMAIL_HISTORY_TABLE'), $emailHistoryData);
					
					
				$config['mailData'] = $mailData;
				$config['viewName'] =  $viewName ;
				$config['v_mail_content'] = $mailTemplate;
				$config['subject'] = $subject;
				$config['to'] = config('constants.SYSTEM_ADMIN_EMAIL');;;;
					
				$sendMail = [];
				$mailSendError = null;
				try{
					$sendMail = sendMailSMTP($config);
				}catch(\Exception $e){
					$mailSendError = $e->getMessage();
				}
					
				$updateEmailData = [];
				if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
					$updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
				} else {
					$updateEmailData['e_status'] = config('constants.FAILED_STATUS');
					$updateEmailData['v_response'] = $mailSendError;
				}
					
				$this->curdModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateEmailData , [ 'i_id' => $insertEmail ] );
				
			}
		}
		
	}
	
	public function updateSalaryIntoMaster( $requestDate = null ){
		
		$date = (!empty($requestDate) ? $requestDate :  date('Y-m-d')  ) ;
		$getAllSalaryDetails = ReviseSalaryMaster::with('assignSalaryInfo')->where('t_is_deleted' , 0 )->where('dt_effective_date' ,  $date )->get();
		//echo "<pre>";print_r($getAllSalaryDetails);
		if(!empty($getAllSalaryDetails)){
			foreach($getAllSalaryDetails as $getAllSalaryDetail){
				$employeeId = $getAllSalaryDetail->i_employee_id;
				$salaryMasterData = [];
				$employeeSalaryMaster = [];
				
				$employeeSalaryMaster['i_salary_group_id'] = $getAllSalaryDetail->i_salary_group_id;;
				$employeeSalaryMaster['e_pf_by_employer'] = $getAllSalaryDetail->e_pf_by_employer;;
				$employeeSalaryMaster['e_pf_deduction'] = $getAllSalaryDetail->e_pf_deduction;;
				$employeeSalaryMaster['d_total_earning'] = $getAllSalaryDetail->d_total_earning;;
				$employeeSalaryMaster['d_total_deduction'] = $getAllSalaryDetail->d_total_deduction;;
				$employeeSalaryMaster['d_net_pay_monthly'] = $getAllSalaryDetail->d_net_pay_monthly;;
				$employeeSalaryMaster['d_net_pay_annually'] = $getAllSalaryDetail->d_net_pay_annually;;
				
				$allSalaryDetails = [];
				if( isset($getAllSalaryDetail->assignSalaryInfo) && (!empty($getAllSalaryDetail->assignSalaryInfo)) ){
					foreach($getAllSalaryDetail->assignSalaryInfo as $assignSalaryInfo){
						$rowSalaryData = [];
						$rowSalaryData['i_salary_component_id'] = $assignSalaryInfo->i_salary_component_id;
						$rowSalaryData['d_amount'] = $assignSalaryInfo->d_amount;
						$allSalaryDetails[] = $rowSalaryData;
					}
				}
				
				if(!empty($allSalaryDetails)){
					
					$checkSalaryMasterExist = EmployeeSalaryModel::where('i_employee_id' , $employeeId )->where('t_is_deleted' , 0 )->first();
					
					if(!empty($checkSalaryMasterExist)){
						$salaryMasterId = $checkSalaryMasterExist->i_id;
						$this->curdModel->updateTableData(config('constants.EMPLOYEE_SALARY_MASTER_TABLE'), $employeeSalaryMaster, ['i_id' => $checkSalaryMasterExist->i_id]);
					} else {
						$employeeSalaryMaster['i_employee_id'] = $getAllSalaryDetail->i_employee_id;
						$salaryMasterId = $this->curdModel->insertTableData(config('constants.EMPLOYEE_SALARY_MASTER_TABLE'), $employeeSalaryMaster);
					}
					
					$newSalaryDetails  = $allSalaryDetails ;
					$newSalaryDetailsCompoentIds = (!empty($newSalaryDetails) ? array_column($newSalaryDetails, 'i_salary_component_id') : []);
					echo "<pre>";print_r($newSalaryDetails);
					$employeeSalary = EmployeeSalaryDetailModel::with(['employeeSalaryMaster']);
					
					$employeeSalary->whereHas('employeeSalaryMaster', function ($employeeSalary) use ($employeeId) {
						$employeeSalary->where('i_employee_id', $employeeId);
					});
					
					$previousSalaryDetails = $employeeSalary->get();
						
					if (!empty($previousSalaryDetails)) {
						foreach ($previousSalaryDetails as $previousSalaryDetails) {
								if (in_array($previousSalaryDetails->i_salary_component_id, $newSalaryDetailsCompoentIds)) {
									$searchKey = array_search($previousSalaryDetails->i_salary_component_id, $newSalaryDetailsCompoentIds);
									if (strlen($searchKey) > 0) {
										$updateValue = ( isset($newSalaryDetails[$searchKey]['d_amount']) ? $newSalaryDetails[$searchKey]['d_amount'] : 0 ) ;
										$this->curdModel->updateTableData(config('constants.EMPLOYEE_SALARY_DETAIL_TABLE'), ['d_amount' => $updateValue], ['i_id' => $previousSalaryDetails->i_id]);
										unset($newSalaryDetails[$searchKey]);
									}
								} else {
									$this->curdModel->deleteTableData(config('constants.EMPLOYEE_SALARY_DETAIL_TABLE'), ['t_is_active' => 0, 't_is_deleted' => 1], ['i_id' => $previousSalaryDetails->i_id]);
								}
							} 
						}
					}
						
					$salaryInfo = [];
					if (!empty($newSalaryDetails)) {
						foreach ($newSalaryDetails as $newSalaryDetail) {
							$salaryRowData = [];
							$salaryRowData['i_employee_salary_id'] = $salaryMasterId;
							$salaryRowData['i_salary_component_id'] = $newSalaryDetail['i_salary_component_id'];
							$salaryRowData['d_amount'] = $newSalaryDetail['d_amount'];
							$salaryInfo[] = $salaryRowData;
						}
					}
					echo "<pre>";print_r($salaryInfo);	
					DB::table(config('constants.EMPLOYEE_SALARY_DETAIL_TABLE'))->insert($salaryInfo);
				}
			}
		
		
	}
	
	
	public function storeSavedLeave(){
		
		
		
	}
	
	public function retriveNotification( $userId = null , $startDate = null  ){
		
		$getAllEmployeeDetails = EmployeeModel::where('t_is_deleted' , 0 )->get();
		//$getAllEmployeeDetails = EmployeeModel::where('t_is_deleted' , 0 )->whereIn('v_employee_code' , [ 437 , 101, 315 ,  176 ])->get();
		//echo "<pre>";print_r($getAllEmployeeDetails);
		if(!empty($getAllEmployeeDetails)){
			$missingPunchDetails = [];
			$insertRecordIds = [];
			$this->crudModel =  New MissingPunchInfo();
			$result = false;
			DB::beginTransaction();
			foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
				if(!empty($getAllEmployeeDetail->v_employee_code)){
					$attedanceDate = (!empty($startDate) ? date('dmY', strtotime($startDate)) : date('dmY'));
					ini_set("memory_limit", "-1");
					ini_set("max_execution_time", "3600");
					//$url = "http://111.93.87.11:818/COSEC/api.svc/v2/attendance-daily?action=get;format=json;date-range=$attedanceDate-$attedanceDate;field-name=userid,username,orgid,processdate,punch1_time,outpunch_time,firsthalf,secondhalf,lunchstart,lunchend,lunchduration_hhmm,worktime_hhmm";
					//$url = "http://103.206.209.119/COSEC/api.svc/v2/attendance-daily?action=get;format=json;range=user;id=176:date-range=$attedanceDate-$attedanceDate;field-name=orgid,processdate,punch1_time,outpunch_time,firsthalf,secondhalf,lunchstart,lunchend,lunchduration_hhmm,worktime_hhmm;";
					//echo $url;
					//$url = "http://111.93.87.11:818/COSEC/api.svc/v2/attendance-daily?action=get;date-range=11072023-11072023;format=json;field-name=userid,username,orgid,workingshift,processdate,punch1_time,punch2_time,punch3_time,punch4_time,firsthalf,secondhalf,worktime_hhmm";
					$url = config('constants.MATRIX_API_BASE_URL');
					$url .= 'notifications'. '?';
					
					$apiParams = [];
					$apiParams['action'] = 'get';
					$apiParams['format'] = 'json';
					$apiParams['userid'] = $getAllEmployeeDetail->v_employee_code;
					
					$params = "";
					if(!empty($apiParams)){
						foreach($apiParams as $apiParamKey =>  $apiParam){
							$params .= $apiParamKey.'='.$apiParam.';';
						}
						$params = rtrim($params,";");
					}
					$url .= $params;
					
					echo "api url  = ".$url;echo "<br><br>";
					//var_dump($params);die;
					
					$getApiDetails = Wild_tiger::curlRequest($url , [] , [ "Content-Type: application/json" ] );
					echo "<pre>";print_r($getApiDetails);
					
					if( isset($getApiDetails['status']) && ( $getApiDetails['status'] != false ) ){
					
						$apiResponse = ( isset($getApiDetails['msg']) ? json_decode( $getApiDetails['msg'] , true  ) : [] );
						
						$notificationDetails = ( isset($apiResponse['notifications']) ? $apiResponse['notifications'] : [] );
						echo "<pre>";print_r($notificationDetails);
						$allDetails = [];
						if(!empty($notificationDetails)){
							try{
								foreach($notificationDetails as $notificationDetail){
									$rowData = [];
									$userId = ( isset($notificationDetail['user-id']) ? $notificationDetail['user-id'] : null );
									$rowData['v_user_id'] = $userId;
									$rowData['i_employee_id'] = $getAllEmployeeDetail->i_id;
									$rowData['action_date'] =  ( (  isset($notificationDetail['generation-date']) && (!empty($notificationDetail['generation-date'])) ) ? date('Y-m-d' , strtotime($notificationDetail['generation-date'])) : null ); ;
									$rowData['time'] =  ( (  isset($notificationDetail['generation-time']) && (!empty($notificationDetail['generation-time'])) ) ? date('H:i:s' , strtotime($notificationDetail['generation-time'])) : null ); ;
									$rowData['message'] =  ( (  isset($notificationDetail['message']) && (!empty($notificationDetail['message'])) ) ? trim($notificationDetail['message']) : null ); ;
									$rowData['v_response_info'] = json_encode($notificationDetail);
									echo "<pre>";print_r($rowData);
										
									if(!empty($rowData['i_employee_id'])){
										$checkRecordWhere = [];
										$checkRecordWhere['i_employee_id'] = $rowData['i_employee_id'];
										$checkRecordWhere['action_date'] = $rowData['action_date'];
										$checkRecordWhere['time'] = $rowData['time'];
										$checkRecordWhere['t_is_deleted'] = 0;
										$checkRecordExist = MissingPunchInfo::where($checkRecordWhere)->first();
						
										if(!empty($checkRecordExist)){
											$this->crudModel->updateTableData(config('constants.MISSING_PUNCH_INFO_TABLE'), $rowData , [ 'i_id' => $checkRecordExist->i_id ] );
										} else {
											$insertRecord = $this->crudModel->insertTableData(config('constants.MISSING_PUNCH_INFO_TABLE'), $rowData);
											$insertRecordIds[] = $insertRecord;
											$missingPunchRow  = [];
											$missingPunchRow['employeeName'] = ( isset($getAllEmployeeDetail->v_employee_full_name) ? $getAllEmployeeDetail->v_employee_full_name : 'User' );
											$missingPunchRow['employeeCode'] = ( isset($getAllEmployeeDetail->v_employee_code) ? $getAllEmployeeDetail->v_employee_code : '' );
											$missingPunchRow['missingPunchRemark'] = ( isset($rowData['message']) ? $rowData['message'] : '' );
											$missingPunchDetails[] = $missingPunchRow;
										}
									}
									$result = true;
								}
							}catch(\Exception $e){
								var_dump($e->getMessage());die;
								$result = false;
								DB::rollback();
							}
							
								
						}
					}
				}
			}
			
			if(!empty($missingPunchDetails)){
				//echo "<pre>";print_r($missingPunchDetails);die;
				//var_dump($result);
				if( $result != false ){
					
					DB::commit();
					
					$mailData = [];
					$mailData['missingPunchDetails'] = $missingPunchDetails;
					$mailTemplate = view( $this->mailTemplateFolderPath .  'missing-punch-mail', $mailData)->render();
						
					$viewName = $this->mailTemplateFolderPath .  'missing-punch-mail';
					$subject = trans('messages.missing-punch-mail-subject');
						
					$emailHistoryData = [];
					$emailHistoryData['i_login_id'] = config('constants.ADMIN_LOGIN_ID');
					$emailHistoryData['v_event'] = "Missing Punch";
					$emailHistoryData['v_receiver_email'] = config('constants.SYSTEM_ADMIN_EMAIL');;
					$emailHistoryData['v_subject'] = $subject;
					$emailHistoryData['v_mail_content'] = $mailTemplate;
						
					$insertEmail = $this->curdModel->insertTableData(config('constants.EMAIL_HISTORY_TABLE'), $emailHistoryData);
						
						
					$config['mailData'] = $mailData;
					$config['viewName'] =  $viewName ;
					$config['v_mail_content'] = $mailTemplate;
					$config['subject'] = $subject;
					$config['to'] = config('constants.SYSTEM_ADMIN_EMAIL');
						
					$sendMail = [];
					$mailSendError = null;
					try{
						$sendMail = sendMailSMTP($config);
					}catch(\Exception $e){
						$mailSendError = $e->getMessage();
					}
					
					//var_dump($sendMail);
					
					$updateEmailData = [];
					if( isset($sendMail['status']) && ( $sendMail['status'] != false ) ){
						$updateEmailData['e_status'] = config('constants.SUCCESS_STATUS');
					} else {
						$updateEmailData['e_status'] = config('constants.FAILED_STATUS');
						$updateEmailData['v_response'] = $mailSendError;
					}
						
					$this->curdModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateEmailData , [ 'i_id' => $insertEmail ] );
					
					if(!empty($insertRecordIds)){
						MissingPunchInfo::whereIn('i_id',$insertRecordIds)->update(['t_is_send_status'=>1]);
					}
				} else {
					DB::rollback();
				}
			}
		}
	}
	
	public function retiveTimeAttendanceEventsOld(  $startDate = null , $userId = null   ){
		$attedanceDate = (!empty($startDate) ? date('dmY', strtotime($startDate)) : date('dmY'));
		ini_set("memory_limit", "-1");
		ini_set("max_execution_time", "3600");
		//$url = "http://111.93.87.11:818/COSEC/api.svc/v2/attendance-daily?action=get;format=json;date-range=$attedanceDate-$attedanceDate;field-name=userid,username,orgid,processdate,punch1_time,outpunch_time,firsthalf,secondhalf,lunchstart,lunchend,lunchduration_hhmm,worktime_hhmm";
		//$url = "http://103.206.209.119/COSEC/api.svc/v2/attendance-daily?action=get;format=json;range=user;id=176:date-range=$attedanceDate-$attedanceDate;field-name=orgid,processdate,punch1_time,outpunch_time,firsthalf,secondhalf,lunchstart,lunchend,lunchduration_hhmm,worktime_hhmm;";
		//echo $url;
		//$url = "http://111.93.87.11:818/COSEC/api.svc/v2/attendance-daily?action=get;date-range=11072023-11072023;format=json;field-name=userid,username,orgid,workingshift,processdate,punch1_time,punch2_time,punch3_time,punch4_time,firsthalf,secondhalf,worktime_hhmm";
		$url = config('constants.MATRIX_API_BASE_URL');
		$url .= 'event-ta'. '?';
		
		$startTime = "000000";
		$endTime = "235959";
	
		$fieldNames = [];
		$fieldNames[] = 'userid';
		$fieldNames[] = 'orgid';
		$fieldNames[] = 'processdate';
		$fieldNames[] = 'punch1_time';
		$fieldNames[] = 'outpunch_time';
		$fieldNames[] = 'firsthalf';
		$fieldNames[] = 'secondhalf';
		$fieldNames[] = 'lunchstart';
		$fieldNames[] = 'lunchend';
		$fieldNames[] = 'lunchduration_hhmm';
		$fieldNames[] = 'worktime_hhmm';
		
		$apiParams = [];
		$apiParams['action'] = 'get';
		$apiParams['format'] = 'json';
		$apiParams['userid'] = '568';
		$apiParams['date-range'] = $attedanceDate.$startTime.'-'.$attedanceDate.$endTime;
		//$apiParams['userid'] = (!empty($userId) ? $userId : 176);
		//$apiParams['field-name'] = implode("," , $fieldNames );
		
		
		
	
	
		$params = "";
		if(!empty($apiParams)){
			foreach($apiParams as $apiParamKey =>  $apiParam){
				$params .= $apiParamKey.'='.$apiParam.';';
			}
			$params = rtrim($params,";");
		}
		$url .= $params;
	
		echo "api url  = ".$url;echo "<br><br>";
		//var_dump($params);die;
	
		$getApiDetails = Wild_tiger::curlRequest($url , [] , [ "Content-Type: application/json" ] );
		
	
		if( isset($getApiDetails['status']) && ( $getApiDetails['status'] != false ) ){
	
			$this->crudModel =  New ApiAttendanceInfo();
	
			$apiResponse = ( isset($getApiDetails['msg']) ? json_decode( $getApiDetails['msg'] , true  ) : [] );
			echo "<pre>";print_r($apiResponse);die;
		} else {
			echo "error";
		}
	}
	
	public function retiveTimeAttendanceEvents(  $startDate = null , $userId = null   ){
		$attedanceDate = (!empty($startDate) ? date('dmY', strtotime($startDate)) : date('dmY'));
		ini_set("memory_limit", "-1");
		ini_set("max_execution_time", "3600");
		//$url = "http://111.93.87.11:818/COSEC/api.svc/v2/attendance-daily?action=get;format=json;date-range=$attedanceDate-$attedanceDate;field-name=userid,username,orgid,processdate,punch1_time,outpunch_time,firsthalf,secondhalf,lunchstart,lunchend,lunchduration_hhmm,worktime_hhmm";
		//$url = "http://103.206.209.119/COSEC/api.svc/v2/attendance-daily?action=get;format=json;range=user;id=176:date-range=$attedanceDate-$attedanceDate;field-name=orgid,processdate,punch1_time,outpunch_time,firsthalf,secondhalf,lunchstart,lunchend,lunchduration_hhmm,worktime_hhmm;";
		//echo $url;
		//$url = "http://111.93.87.11:818/COSEC/api.svc/v2/attendance-daily?action=get;date-range=11072023-11072023;format=json;field-name=userid,username,orgid,workingshift,processdate,punch1_time,punch2_time,punch3_time,punch4_time,firsthalf,secondhalf,worktime_hhmm";
		$url = config('constants.MATRIX_API_BASE_URL');
		$url .= 'event-ta'. '?';
	
		$startTime = "000000";
		$endTime = "235959";
	
		$apiParams = [];
		$apiParams['action'] = 'get';
		$apiParams['format'] = 'json';
		$apiParams['date-range'] = $attedanceDate.$startTime.'-'.$attedanceDate.$endTime;
		
		$params = "";
		if(!empty($apiParams)){
			foreach($apiParams as $apiParamKey =>  $apiParam){
				$params .= $apiParamKey.'='.$apiParam.';';
			}
			$params = rtrim($params,";");
		}
		$url .= $params;
	
		echo "api url  = ".$url;echo "<br><br>";
		//var_dump($params);die;
	
		$getApiDetails = Wild_tiger::curlRequest($url , [] , [ "Content-Type: application/json" ] );
		
	
		if( isset($getApiDetails['status']) && ( $getApiDetails['status'] != false ) ){
	
			$this->crudModel =  New ApiAttendanceInfo();
	
			$apiResponse = ( isset($getApiDetails['msg']) ? json_decode( $getApiDetails['msg'] , true  ) : [] );
			
			
			$getAllEmployeeDetails = EmployeeModel::where('t_is_deleted' , 0 )->get();
			$allEmployeeCodeDetails = [];
			if(!empty($getAllEmployeeDetails)){
				foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
					if( $getAllEmployeeDetail->e_auto_generate_no == config('constants.SELECTION_YES') ){
						$allEmployeeCodeDetails[] = threeNumberSeries($getAllEmployeeDetail->v_employee_code);
					} else {
						$allEmployeeCodeDetails[] = ($getAllEmployeeDetail->v_employee_code);
					}
				}
			}
			
			
			$notificationDetails = ( isset($apiResponse['event-ta']) ? $apiResponse['event-ta'] : [] ); 
			
			//echo "<pre> api data";print_r($notificationDetails);die;
			
			$allDetails = [];
			if(!empty($notificationDetails)){
				
				
				$result = false;
     			DB::beginTransaction();
     			try{
     				foreach($notificationDetails as $notificationDetail){
     					$rowData = [];
     					$userId = ( isset($notificationDetail['userid']) ? $notificationDetail['userid'] : null );
     					$rowData['v_user_id'] = $userId;
     					$indexNo = ( isset($notificationDetail['indexno']) ? $notificationDetail['indexno'] : null );
     					if( in_array($userId ,  $allEmployeeCodeDetails) ){
     						$searchKey = array_search($userId ,  $allEmployeeCodeDetails);
     						if(strlen($searchKey) > 0 ){
     							$rowData['i_employee_id'] = ( isset($getAllEmployeeDetails[$searchKey]->i_id) ? $getAllEmployeeDetails[$searchKey]->i_id : 0 );
     						}
     					}
     					$rowData['v_entry_exit_type'] = ( isset($notificationDetail['v_entry_exit_type']) ? $notificationDetail['v_entry_exit_type'] : null );
     					$rowData['dt_entry_date_time'] =  ( (  isset($notificationDetail['eventdatetime']) && (!empty($notificationDetail['eventdatetime'])) ) ? date('Y-m-d H:i:s' , strtotime(str_replace("/", "-", $notificationDetail['eventdatetime']))) : null ); ;
     					$rowData['dt_i_date_time'] =  ( (  isset($notificationDetail['idatetime']) && (!empty($notificationDetail['idatetime'])) ) ? date('Y-m-d H:i:s' , strtotime(str_replace("/", "-", $notificationDetail['idatetime']))) : null ); ;
     					$rowData['v_response_info'] = json_encode($notificationDetail);
     					echo "<pre>";print_r($rowData);	
     					if(!empty($indexNo)){
     				
     						$checkRecordWhere = [];
     						$checkRecordWhere['v_index_no'] = $indexNo;
     						$checkRecordWhere['t_is_deleted'] = 0;
     						$checkRecordExist = PunchModel::where($checkRecordWhere)->first();
     				
     						if(!empty($checkRecordExist)){
     							$this->crudModel->updateTableData(config('constants.PUNCH_INFO_TABLE'), $rowData , [ 'i_id' => $checkRecordExist->i_id ] );
     						} else {
     							$rowData['v_index_no'] = $indexNo;
     							$this->crudModel->insertTableData(config('constants.PUNCH_INFO_TABLE'), $rowData);
     						}
     				
     					}
     					$result = true;
     				}
     			}catch(\Exception $e){
     				var_dump($e->getMessage());die;
     				$result = false;
     				DB::rollback();
     			}
     			var_dump($result);
     			if( $result != false ){
     				DB::commit();
     			} else {
     				DB::rollback();
     			}
				
			}
			
		} else {
			echo "error";
		}
	}
	
	
	public function updateEmployeeShift($todayDate = null , $employeeId = null ){
		
		$date = (!empty($todayDate) ? $todayDate : date('Y-m-d'));
		
		$getUpcomingRecordWhere = [];
		$getUpcomingRecordWhere['t_is_deleted'] = 0;
		$getUpcomingRecordWhere['e_record_type'] = config('constants.SHIFT_RECORD_TYPE')  ;
		$getUpcomingRecordWhere['dt_start_date'] = $date;
		$getUpcomingRecordWhere['dt_end_date'] = null;
		if(!empty($employeeId)){
			$getUpcomingRecordWhere['i_employee_id'] = $employeeId;
		}
		$getUpcomingRecordDetails = EmployeeDesignationHistory::where($getUpcomingRecordWhere)->get();
		
		if(!empty($getUpcomingRecordDetails)){
			foreach($getUpcomingRecordDetails as $getUpcomingRecordDetail){
				$updateData = [];
				$updateData['i_shift_id'] = $getUpcomingRecordDetail->i_designation_id;
				$updateData['dt_last_update_shift'] = $getUpcomingRecordDetail->dt_start_date;
				
				$getOldRecordWhere = [];
				$getOldRecordWhere['t_is_deleted'] = 0;
				$getOldRecordWhere['dt_end_date'] = null;
				$getOldRecordWhere['i_employee_id'] = $getUpcomingRecordDetail->i_employee_id;
				$getOldRecordWhere['custom_function'][] = "( dt_start_date <= '".$date."')";
				$getOldRecordWhere['e_record_type'] = config('constants.SHIFT_RECORD_TYPE');
				//$getOldRecordWhere['order_by'] = ['i_id' => 'DESC'];
				$getOldWorkingRecord = $this->curdModel->getSingleRecordById(config('constants.EMPLOYEE_DESIGNATION_HISTORY') , [ 'i_id' ] , $getOldRecordWhere  );
				//echo $this->curdModel->last_query();echo "<br><br>";
				$this->curdModel->updateTableData(config('constants.EMPLOYEE_MASTER_TABLE'), $updateData , [ 'i_id' => $getUpcomingRecordDetail->i_employee_id ] );
				
				if(!empty($getOldWorkingRecord)){
					$this->curdModel->updateTableData(config('constants.EMPLOYEE_DESIGNATION_HISTORY'), ['dt_end_date' => date('Y-m-d' , strtotime("-1 days" , strtotime($date)) ) ] , [ 'i_id' => $getOldWorkingRecord->i_id , 'dt_end_date' => null ] );
					//echo $this->curdModel->last_query();echo "<br><br>";
				}
				
			}
		}
	}
	
	
	public function manageCarryForwardLeave($requestDate = null ){
		
		$todayDate = (!empty($requestDate) ? $requestDate :  date('Y-m-d') ) ;
		
		if( (  date('d' , strtotime($todayDate)) == 16 ) && ( date('m' , strtotime($todayDate)) == 12 )  ){
			$getAllEmployeeDetails = EmployeeModel::where('t_is_deleted' , 0 )->get();
			
			if(!empty($getAllEmployeeDetails)){
				$result = false;
				DB::beginTransaction();
				foreach($getAllEmployeeDetails as $getAllEmployeeDetail){
					$employeeId = $getAllEmployeeDetail->i_id;
					if(!empty($employeeId)){
						$leaveBalanceWhere = [];
						$leaveBalanceWhere['t_is_deleted'] = 0;
						$leaveBalanceWhere['i_employee_id'] = $employeeId;
						$leaveBalanceDetails = LeaveBalanceModel::with(['leaveType'])->where($leaveBalanceWhere)->whereIn('i_leave_type_id' , [ config('constants.PAID_LEAVE_TYPE_ID') , config('constants.EARNED_LEAVE_TYPE_ID') ])->get();
			
						$totalLeaveBalance = 0;
						if(!empty($leaveBalanceDetails)){
							$reduceLeaveDetails = [];
							foreach($leaveBalanceDetails as $leaveBalanceDetail){
								if(!empty($leaveBalanceDetail->d_current_balance) && ( $leaveBalanceDetail->d_current_balance > 0 ) ){
									$totalLeaveBalance += $leaveBalanceDetail->d_current_balance;
									$rowReduceLeaveDetail['i_id'] = $leaveBalanceDetail->i_id;
									$rowReduceLeaveDetail['i_leave_type_id'] = $leaveBalanceDetail->i_leave_type_id;
									$rowReduceLeaveDetail['d_current_balance'] = $leaveBalanceDetail->d_current_balance;
									$rowReduceLeaveDetail['leave_name'] = ( isset($leaveBalanceDetail->leaveType->v_leave_type_name) ? $leaveBalanceDetail->leaveType->v_leave_type_name : null );
									$reduceLeaveDetails[] = $rowReduceLeaveDetail;
								}
							}
							if( $totalLeaveBalance >  0 ){
									
			
								try{
									foreach($reduceLeaveDetails as $reduceLeaveDetail){
										$transferInfo = [];
										$transferInfo['i_employee_id'] = $employeeId;
										$transferInfo['i_leave_type_id'] = $reduceLeaveDetail['i_leave_type_id'];
										$transferInfo['d_no_of_days_assign'] = "-" .$reduceLeaveDetail['d_current_balance'];
										$transferInfo['dt_effective_date'] = $todayDate;
										$transferInfo['v_remark'] = "Transfer Into Carry Forward Leave";
			
										$this->curdModel->insertTableData(config('constants.LEAVE_ASSIGN_HISTORY_TABLE'), $transferInfo );
			
										$this->curdModel->updateTableData(config('constants.LEAVE_BALANCE_TABLE'), ['d_current_balance' => 0 ] , [ 'i_id' => $reduceLeaveDetail['i_id']  ] );
			
										$transferInfo = [];
										$transferInfo['i_employee_id'] = $employeeId;
										$transferInfo['i_leave_type_id'] = config('constants.CARRY_FORWARD_LEAVE_TYPE_ID');
										$transferInfo['d_no_of_days_assign'] = $reduceLeaveDetail['d_current_balance'];
										$transferInfo['dt_effective_date'] = $todayDate;
										$transferInfo['v_remark'] = "Transfer From ". (!empty($reduceLeaveDetail['leave_name']) ? $reduceLeaveDetail['leave_name'] : $reduceLeaveDetail['i_leave_type_id'] );
			
										$this->curdModel->insertTableData(config('constants.LEAVE_ASSIGN_HISTORY_TABLE'), $transferInfo );
			
			
									}
									if( $totalLeaveBalance > 0 ){
										$this->curdModel->updateTableData(config('constants.LEAVE_BALANCE_TABLE'), ['d_current_balance' => DB::raw("CONCAT(d_current_balance + '".$totalLeaveBalance."')") ] , [ 'i_leave_type_id' => config('constants.CARRY_FORWARD_LEAVE_TYPE_ID') , 'i_employee_id' => $employeeId  ] );
			
										$savedLeaveInfo = [];
										$savedLeaveInfo['i_employee_id'] = $employeeId;
										$savedLeaveInfo['v_year'] = date('Y' , strtotime($todayDate));
										$savedLeaveInfo['d_save_leave'] = $totalLeaveBalance;
										$savedLeaveInfo['v_info'] = json_encode($reduceLeaveDetails);
			
										$this->curdModel->insertTableData(config('constants.ACADEMIC_SAVED_LEAVE_INFO'), $savedLeaveInfo );
			
										//echo $this->curdModel->last_query();
									}
									$result = true;
								}catch(\Exception $e){
									$result = false;
									DB::rollback();
								}
							}
						}
					}
				}
				if($result != false){
					//die("welcome");
					DB::commit();
					echo "success";
				} else {
					DB::rollback();
					echo "done";
				}
			}
		}
	}
}
