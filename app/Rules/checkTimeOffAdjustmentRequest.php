<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\Log;

class checkTimeOffAdjustmentRequest implements Rule
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
        if(!empty($value)){
    		$value = trim($value);
    		if( isset($this->requestData['time_off_type']) &&  (!empty($this->requestData['time_off_type'])) ){
    			
    			$checkUniqueWhere =[];
    			$checkUniqueWhere['t_is_deleted != '] = 1;
    			$checkUniqueWhere['e_record_type'] = config("constants.ADJUSTMENT_TIME_OFF");
    			$checkUniqueWhere["custom_function"][] = "e_status in ( '".config('constants.PENDING_STATUS')."' , '".config('constants.APPROVED_STATUS')."' ) ";
    			if( isset($this->requestData['employee_id']) &&  (!empty($this->requestData['employee_id'])) ){
    				$checkUniqueWhere['i_employee_id'] = (int) Wild_tiger::decode($this->requestData['employee_id']);
    			} else {
    				$checkUniqueWhere['i_employee_id'] = session()->get('user_employee_id');
    			}
    			
    			if( isset($this->requestData['record_id']) &&  (!empty($this->requestData['record_id'])) ){
    				$checkUniqueWhere['i_id != '] = $this->requestData['record_id'];
    			}
    			
    			if( $this->requestData['time_off_type'] == config("constants.ADJUSTMENT_TIME_OFF") ){
    				$timeOffDate = isset($this->requestData['time_off_date']) ? (!empty($this->requestData['time_off_date']) ? dbDate($this->requestData['time_off_date']) : date('Y-m-d') ) : date('Y-m-d') ;
    				
    				//$checkUniqueWhere['custom_function'][] = "dt_time_off_date >= now() - interval ".config('constants.DUPLICATE_TIME_OFF_REQUEST_LIMIT')." month";
    				$checkUniqueWhere['custom_function'][] = "( ( dt_time_off_date between '".date('Y-m-d' ,strtotime("-".config('constants.DUPLICATE_TIME_OFF_REQUEST_LIMIT')." month"  , strtotime(  $timeOffDate ) ))."' and '".$timeOffDate."' ) or ( dt_time_off_date >= '".$timeOffDate."' ) ) ";
    				
    				
    				$dbObject = new BaseModel();
    				
    				$getRecordInfo =  $dbObject->getSingleRecordById( config('constants.TIME_OFF_MASTER_TABLE'),[ 'i_id' ] ,$checkUniqueWhere  );
    				
    				//Log::info("check duplicate adjustement quesry");
    				//Log::info($dbObject->last_query());
    				
    				if( (!empty($getRecordInfo)) && count(objectToArray($getRecordInfo)) > 0 ){
    					return false;
    				} else {
    					return true;
    				}
    				
    			} 
    		}
    		
    		return true;
    		
    		
    	}
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.error-duplicate-time-off-adjustment-request');
    }
}
