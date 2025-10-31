<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\BaseModel;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\Log;

class checkTimeOffRequest implements Rule
{
	private $requestData = [];
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct( $request = [] )
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
    	if(!empty($value)){
    		$value = trim($value);
    		if( isset($this->requestData['time_off_type']) &&  (!empty($this->requestData['time_off_type'])) ){
    			 
    			$checkUniqueWhere =[];
    			$checkUniqueWhere['t_is_deleted != '] = 1;
    			$checkUniqueWhere['e_record_type'] = config("constants.OFFICIAL_WORK_TIME_OFF");
    			$checkUniqueWhere["custom_function"][] = "e_status in ( '".config('constants.PENDING_STATUS')."' , '".config('constants.APPROVED_STATUS')."' ) ";
    			if( isset($this->requestData['employee_id']) &&  (!empty($this->requestData['employee_id'])) ){
    				$checkUniqueWhere['i_employee_id'] = $this->requestData['employee_id'];
    			}
    			
    			if( isset($this->requestData['time_off_date']) &&  (!empty($this->requestData['time_off_date'])) ){
    				$checkUniqueWhere['dt_time_off_date'] = dbDate( $this->requestData['time_off_date'] );
    			} 
    			 
    			if( isset($this->requestData['record_id']) &&  (!empty($this->requestData['record_id'])) ){
    				$checkUniqueWhere['i_id != '] = $this->requestData['record_id'];
    			}
    			 
    			if( $this->requestData['time_off_type'] == config("constants.OFFICIAL_WORK_TIME_OFF") ){
    				if(isset($this->requestData['time_off_from']) && (!empty($this->requestData['time_off_from'])) ){
    					$fromTime = dbTime( $this->requestData['time_off_from'] );
    					
    					$checkUniqueWhere['custom_function'][] = "(  t_from_time >= '".$fromTime."'  or t_to_time >= '".$fromTime."'  )";
    				}
    				if(isset($this->requestData['time_off_to']) && (!empty($this->requestData['time_off_to'])) ){
    					$toTime = dbTime ( $this->requestData['time_off_to'] );
    					$checkUniqueWhere['custom_function'][] = "(  t_from_time <= '".$toTime."'  or t_to_time <= '".$toTime."'  )";
    				}
    				
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
        return trans('messages.error-duplicate-time-off-request');
    }
}
