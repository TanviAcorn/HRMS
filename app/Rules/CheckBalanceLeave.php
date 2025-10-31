<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use App\MyLeaveModel;
use App\Helpers\Twt\Wild_tiger;
use App\Models\LeaveBalanceModel;
use App\Models\LeaveAssignHistoryModel;
use DB;

class CheckBalanceLeave implements Rule
{
	private $requestData = [];
	public $balanceErrorMessage = '';
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($request = [])
    {
        //
    	$this->requestData = $request->all();
    	$this->balanceErrorMessage = trans('messages.error-enought-leave-balance-issue');
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
        
       $this->crudModel =  new MyLeaveModel();
    	
    	$duplicateLeave = false;
    	 
    	//$employeeId = 1;
    	$employeeId = ( ( isset($this->requestData['employee_id']) && (!empty($this->requestData['employee_id'])) ) ? (int)Wild_tiger::decode($this->requestData['employee_id'] ) : 0 );
    	$leaveTypeId = ( ( isset($this->requestData['leave_types']) && (!empty($this->requestData['leave_types'])) ) ? (int)Wild_tiger::decode($this->requestData['leave_types'] ) : 0 );
    	
    	$leaveStartDate = ( ( isset($this->requestData['leave_from_date']) && (!empty($this->requestData['leave_from_date'])) ) ? dbDate( $this->requestData['leave_from_date'] ) : null );
    	$leaveEndDate = ( ( isset($this->requestData['leave_to_date']) && (!empty($this->requestData['leave_to_date'])) ) ? dbDate ( $this->requestData['leave_to_date'] ) : null );
    	 
    	$dualDateFromSession = ( ( isset($this->requestData['dual_date_from_session']) && (!empty($this->requestData['dual_date_from_session'])) ) ? $this->requestData['dual_date_from_session'] : null );
    	$dualDateToSession = ( ( isset($this->requestData['dual_date_to_session']) && (!empty($this->requestData['dual_date_to_session'])) ) ? $this->requestData['dual_date_to_session'] : null );
    	
    	$singleDateSession = ( ( isset($this->requestData['single_date_session']) && (!empty($this->requestData['single_date_session'])) ) ? $this->requestData['single_date_session'] : null );
    	 
    	$getAllAppliedLeaveDetails = [];
    	
    	
    	
    	if( (!empty($leaveStartDate)) && (!empty($leaveStartDate)) ){
    		$getAppliedLeaveWhere = [];
    		
    		$getAppliedLeaveWhere['leave_status'] = [ config('constants.APPROVED_STATUS') , config('constants.PENDING_STATUS') ];
    		$getAppliedLeaveWhere['leave_from_date'] = $leaveStartDate;
    		$getAppliedLeaveWhere['leave_to_date'] = $leaveEndDate;
    		$getAppliedLeaveWhere['employee_id'] = $employeeId;
    		
    		//\DB::enableQueryLog();
    		$getAppliedLeaveDetails = $this->crudModel->getRecordDetails($getAppliedLeaveWhere);
    		//echo "<pre>";print_r($getAppliedLeaveDetails);
    		//dd(\DB::getQueryLog());
    		$appliedLeavenoOfDays = 0;
    		if( strtotime($leaveStartDate)  != strtotime($leaveEndDate) ){
    			$newLeaveDuration = getDatesFromRange($leaveStartDate, $leaveEndDate);
    			$appliedLeavenoOfDays = count($newLeaveDuration);
    			if( ( in_array( $dualDateFromSession , [  config('constants.SECOND_HALF_LEAVE') ] ) ) ){
    				$appliedLeavenoOfDays -= config('constants.HALF_LEAVE_VALUE');
    			}
    			if( ( in_array( $dualDateToSession , [ config('constants.FIRST_HALF_LEAVE') ] ) ) ){
    				$appliedLeavenoOfDays -= config('constants.HALF_LEAVE_VALUE');
    			}
    			
    		} else {
    			$newLeaveDuration = [];
    			$newLeaveDuration[] = $leaveStartDate;
    			$appliedLeavenoOfDays = config('constants.FULL_LEAVE_VALUE');
    			if( in_array( $singleDateSession , [ config('constants.FIRST_HALF_LEAVE') , config('constants.SECOND_HALF_LEAVE') ] ) ){
    				$appliedLeavenoOfDays = config('constants.HALF_LEAVE_VALUE');
    			}
    		}
    		
    		if( $leaveTypeId > 0 && $leaveTypeId != config('constants.UNPAID_LEAVE_TYPE_ID') ){
    			$leaveBalanceWhere = [];
    			$leaveBalanceWhere['i_employee_id'] = $employeeId;
    			$leaveBalanceWhere['i_leave_type_id'] = $leaveTypeId;
    			$leaveBalanceWhere['t_is_deleted'] = 0;
    			$getLeaveBalance = LeaveBalanceModel::where($leaveBalanceWhere)->first();
    			
    			if( empty($getLeaveBalance) || ( $getLeaveBalance->d_current_balance < $appliedLeavenoOfDays ) ){
    				return false;
    			}
    			
    		}
    		if( $leaveTypeId > 0 && $leaveTypeId == config('constants.PAID_LEAVE_TYPE_ID') ){
    			
    			//$monthStartdate = date('Y' , strtotime($leaveStartDate)) . '-'. date('m' , strtotime($leaveStartDate)) . '-' . ( config('constants.SALARY_CYCLE_START_DATE') - 1 ) ;
    			//var_dump(attendanceEndDate('10', '2023'));
    			//var_dump($monthStartdate);
    			if( date('d' , strtotime($leaveStartDate)) >= config('constants.SALARY_CYCLE_START_DATE') ){
    				$monthStartdate = attendanceEndDate(date('m' , strtotime($leaveStartDate)), date('Y' , strtotime($leaveStartDate)));
    				$monthStartdate = date('Y-m-d' , strtotime("+1 month" , strtotime($monthStartdate)));
    			} else {
    				$monthStartdate = attendanceEndDate(date('m' , strtotime($leaveStartDate)), date('Y' , strtotime($leaveStartDate)));
    				
    			}
    			//$monthStartdate = attendanceStartDate(date('m' , strtotime($leaveStartDate)), date('Y' , strtotime($leaveStartDate)));
    			//var_dump($monthStartdate);
    			
    			//var_dump($monthStartdate);
    			$getPaidLeaveBalance['t_is_deleted'] = 0;
    			$getPaidLeaveBalance['i_employee_id'] = $employeeId;
    			$getPaidLeaveBalance['i_leave_type_id'] = $leaveTypeId;
    			$getAllAssignPaidLeaveBalanceDetails = LeaveAssignHistoryModel::where($getPaidLeaveBalance)->whereRaw( "( ( d_no_of_days_assign - d_no_of_days_used ) > 0 ) and ( date(dt_effective_date) <= '".$monthStartdate."'  )")->get();
    			$availableBalance = 0;
    			if(!empty($getAllAssignPaidLeaveBalanceDetails)){
    				foreach($getAllAssignPaidLeaveBalanceDetails as $getAllAssignPaidLeaveBalanceDetail){
    					if( ( $getAllAssignPaidLeaveBalanceDetail->d_no_of_days_assign -   $getAllAssignPaidLeaveBalanceDetail->d_no_of_days_used ) > 0 ){
    						$availableBalance += ( $getAllAssignPaidLeaveBalanceDetail->d_no_of_days_assign -   $getAllAssignPaidLeaveBalanceDetail->d_no_of_days_used );
    					}
    				}
    			}
    			
    			//var_dump($availableBalance);die;
    			if( empty($availableBalance) || ( $availableBalance < $appliedLeavenoOfDays ) ){
    				$this->balanceErrorMessage = trans('messages.error-enought-paid-leave-balance-issue' , [ 'leaveCount' => $availableBalance , 'leaveDate' => convertDateFormat($leaveStartDate ) ] ); 
    				return false;
    			} 
    			
    			
    			
    		}
    	}
    	return true;
    		
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
    	return $this->balanceErrorMessage;
        return trans('messages.error-enought-leave-balance-issue');
    }
}
