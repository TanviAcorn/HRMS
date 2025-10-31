<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use App\MyLeaveModel;
use App\Helpers\Twt\Wild_tiger;
use App\Models\LeaveBalanceModel;

class CheckDuplicateLeave implements Rule
{
	private $requestData = [];
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($request = [])
    {
        //
    	$this->requestData = $request->all();
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
    		
    		
    		
    		//echo "new leave duration<pre>";print_r($newLeaveDuration);
    		
    		//echo "<pre>";print_r($getAppliedLeaveDetails);
    		
    		$appliedFullDayLeaveDetails = [];
    		$appliedHalfDayLeaveDetails = [];
    		
    		$duplicateLeaveFound = false;
    		if( (!empty($getAppliedLeaveDetails)) ){
    			foreach($getAppliedLeaveDetails as $getAppliedLeaveDetail){
    				if( strtotime($getAppliedLeaveDetail->dt_leave_from_date)  != strtotime($getAppliedLeaveDetail->dt_leave_to_date) ){
    					$getLeaveDates = getDatesFromRange($getAppliedLeaveDetail->dt_leave_from_date, $getAppliedLeaveDetail->dt_leave_to_date);
    					//echo "<pre> getLeaveDates";print_r($getLeaveDates);
    					if(!empty($getLeaveDates)){
    		
    						$durationFirstLeave = $getLeaveDates[0];
    						$durationLastLeave = end($getLeaveDates);
    						foreach($getLeaveDates as $getLeaveKey => $getLeaveDate ){
    							 
    							if( ( $getLeaveDate == $durationFirstLeave )  || ( $getLeaveDate == $durationLastLeave  ) ){
    								if( ( $getLeaveDate == $durationFirstLeave ) ){
    									if( $getAppliedLeaveDetail->e_from_duration == config('constants.SECOND_HALF_LEAVE') ){
    										$appliedHalfDayLeaveDetails[config('constants.SECOND_HALF_LEAVE')][] = $getLeaveDate;
    									} else {
    										$appliedFullDayLeaveDetails[] = $getLeaveDate;
    									}
    								}
    		
    								if( ( $getLeaveDate == $durationLastLeave ) ){
    									//echo "<pre> appliedFullDayLeaveDetails";print_r($appliedFullDayLeaveDetails);echo "<br><br><br>";
    									if( $getAppliedLeaveDetail->e_to_duration == config('constants.FIRST_HALF_LEAVE') ){
    										$appliedHalfDayLeaveDetails[config('constants.FIRST_HALF_LEAVE')][] = $getLeaveDate;
    									} else {
    										$appliedFullDayLeaveDetails[] = $getLeaveDate;
    									}
    								}
    		
    		
    							} else {
    								$appliedFullDayLeaveDetails[] = $getLeaveDate;
    								 
    							}
    						}
    					}
    				} else {
    						
    					if( in_array( $getAppliedLeaveDetail->dt_leave_from_date , $newLeaveDuration ) ){
    		
    						if( in_array( $getAppliedLeaveDetail->e_duration , [ config('constants.FIRST_HALF_LEAVE') , config('constants.SECOND_HALF_LEAVE') ] ) ){
    							$appliedHalfDayLeaveDetails[$getAppliedLeaveDetail->e_duration][] = $getAppliedLeaveDetail->dt_leave_from_date;
    						} else {
    							$appliedFullDayLeaveDetails[] = $getAppliedLeaveDetail->dt_leave_from_date;
    						}
    					}
    				}
    			}
    		}
    		
    		$onlyFirstHalfAppliedLeaveDetails = ( isset($appliedHalfDayLeaveDetails[config('constants.FIRST_HALF_LEAVE')]) ? $appliedHalfDayLeaveDetails[config('constants.FIRST_HALF_LEAVE')] : [] );
    		$onlySecondHalfAppliedLeaveDetails = ( isset($appliedHalfDayLeaveDetails[config('constants.SECOND_HALF_LEAVE')]) ? $appliedHalfDayLeaveDetails[config('constants.SECOND_HALF_LEAVE')] : [] );
    		
    		$getCommonAppliedHalfLeaveDetails = array_intersect($onlyFirstHalfAppliedLeaveDetails, $onlySecondHalfAppliedLeaveDetails);
    		
    		if(!empty($getCommonAppliedHalfLeaveDetails)){
    			$appliedFullDayLeaveDetails = (!empty($appliedFullDayLeaveDetails) ? array_merge($appliedFullDayLeaveDetails, $getCommonAppliedHalfLeaveDetails) : $getCommonAppliedHalfLeaveDetails );
    		}
    		$appliedFullDayLeaveDetails = (!empty($appliedFullDayLeaveDetails) ? array_unique($appliedFullDayLeaveDetails) : [] );
    		
    		/* echo "<pre> Full";print_r($appliedFullDayLeaveDetails);
    		echo "<pre> HalfDay";print_r($appliedHalfDayLeaveDetails);
    		echo "<pre> onlyFirstHalfAppliedLeaveDetails";print_r($onlyFirstHalfAppliedLeaveDetails);
    		echo "<pre> onlySecondHalfAppliedLeaveDetails";print_r($onlySecondHalfAppliedLeaveDetails);  */
    		
    		
    		
    		if( strtotime($leaveStartDate)  != strtotime($leaveEndDate) ){
    			$newLeaveDuration = getDatesFromRange($leaveStartDate, $leaveEndDate);
    			 
    			//echo "<pre>";print_r($newLeaveDuration);
    			 
    			if(!empty($newLeaveDuration)){
    				$durationFirstLeave = $newLeaveDuration[0];
    				$durationLastLeave = end($newLeaveDuration);
    		
    				//echo "first leave  = ".$durationFirstLeave;echo "<br><br>";
    				//echo "last leave  = ".$durationLastLeave;echo "<br><br>";
    		
    				foreach($newLeaveDuration as $newLeave){
    					if( ( $newLeave == $durationFirstLeave )  || ( $newLeave == $durationLastLeave  ) ){
    						if($newLeave == $durationFirstLeave){
    							if(!empty($dualDateFromSession) || (!empty($dualDateToSession))){
    								if(!empty($dualDateFromSession) && (in_array($newLeave,$appliedFullDayLeaveDetails))){
    									$duplicateLeave = true;
    								}
    								if(!empty($dualDateToSession) && isset($appliedHalfDayLeaveDetails[config('constants.SECOND_HALF_LEAVE')]) && (!empty($appliedHalfDayLeaveDetails[config('constants.SECOND_HALF_LEAVE')])) && (in_array($newLeave,$appliedHalfDayLeaveDetails[config('constants.SECOND_HALF_LEAVE')])) ){
    									$duplicateLeave = true;
    								}
    							} else {
    								if(in_array($newLeave,$appliedFullDayLeaveDetails)){
    									$duplicateLeave = true;
    								}
    							}
    						}
    		
    						if($newLeave == $durationLastLeave){
    							if(!empty($dualDateFromSession) || (!empty($dualDateToSession))){
    								if(!empty($dualDateToSession) && (in_array($newLeave,$appliedFullDayLeaveDetails))){
    									$duplicateLeave = true;
    								}
    								if(!empty($dualDateFromSession) && isset($appliedHalfDayLeaveDetails[config('constants.FIRST_HALF_LEAVE')]) && (!empty($appliedHalfDayLeaveDetails[config('constants.FIRST_HALF_LEAVE')])) && (in_array($newLeave,$appliedHalfDayLeaveDetails[config('constants.FIRST_HALF_LEAVE')])) ){
    									$duplicateLeave = true;
    								}
    							} else {
    								if(in_array($newLeave,$appliedFullDayLeaveDetails)){
    									$duplicateLeave = true;
    								}
    							}
    						}
    		
    					} else {
    		
    						if(in_array($newLeave,$appliedFullDayLeaveDetails)){
    							$duplicateLeave = true;
    						}
    					}
    				}
    			}
    		} else {
    			// var_dump($singleDateSession);
    			if( in_array( $singleDateSession , [ config('constants.FIRST_HALF_LEAVE') , config('constants.SECOND_HALF_LEAVE') ] ) ){
    				if( ( isset($appliedHalfDayLeaveDetails[$singleDateSession])  && (!empty($appliedHalfDayLeaveDetails[$singleDateSession])) && in_array($leaveStartDate,$appliedHalfDayLeaveDetails[$singleDateSession]) ) ){
    					$duplicateLeave = true;
    				}
    				if(in_array($leaveStartDate,$appliedFullDayLeaveDetails)){
    					$duplicateLeave = true;
    				}
    			} else {
    				if(in_array($leaveStartDate,$appliedFullDayLeaveDetails)){
    					$duplicateLeave = true;
    				}
    				if( $singleDateSession ==   config('constants.FULL_DAY_LEAVE') ){
    					if( in_array($leaveStartDate,(isset($appliedHalfDayLeaveDetails[config('constants.FIRST_HALF_LEAVE')]) ? $appliedHalfDayLeaveDetails[config('constants.FIRST_HALF_LEAVE')] :[])) ||  in_array($leaveStartDate,(isset($appliedHalfDayLeaveDetails[config('constants.SECOND_HALF_LEAVE')]) ? $appliedHalfDayLeaveDetails[config('constants.SECOND_HALF_LEAVE')] :[]))  ){
    						$duplicateLeave = true;
    					}
    				}
    			}
    		}
    	}
    	//var_dump($duplicateLeave);die;
    	if( $duplicateLeave != false ){
    		return false;
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
        return trans('messages.error-duplicate-leave-request');
    }
}
