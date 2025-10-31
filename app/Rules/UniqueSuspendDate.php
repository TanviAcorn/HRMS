<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
class UniqueSuspendDate implements Rule
{
	private $requestData = [];
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($request = [])
    {
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
    		$checkUniqueWhere =[];
    		
    		if(isset($this->requestData['suspend_record_id']) && (!empty($this->requestData['suspend_record_id'])) ){
    			$suspendRecordId = (int)Wild_tiger::decode($this->requestData['suspend_record_id']);
    			if( $suspendRecordId > 0 ){
    				$checkUniqueWhere['i_id != '] = $suspendRecordId;
    			}
    		}
    		
    		if(isset($this->requestData['employee_id']) && (!empty($this->requestData['employee_id'])) ){
    			$suspendEmployeeId = (int)Wild_tiger::decode($this->requestData['employee_id']);
    			if( $suspendEmployeeId > 0 ){
    				$checkUniqueWhere['i_employee_id'] = $suspendEmployeeId;
    			}
    		}
    		
    		
    		if(isset($this->requestData['suspend_from_date']) && (!empty($this->requestData['suspend_from_date'])) ){
    			$fromTime = dbDate( $this->requestData['suspend_from_date'] );
    			$checkUniqueWhere['custom_function'][] = "(  dt_start_date >= '".$fromTime."'  or dt_end_date >= '".$fromTime."'  )";
    		}
    		if(isset($this->requestData['suspend_to_date']) && (!empty($this->requestData['suspend_to_date'])) ){
    			$toTime = dbDate( $this->requestData['suspend_to_date'] );
    			$checkUniqueWhere['custom_function'][] = "(  dt_start_date <= '".$toTime."'  or dt_end_date <= '".$toTime."'  )";
    		}
    		
    		$checkUniqueWhere['t_is_deleted != '] = 1;
    		$checkUniqueWhere['t_is_cancelled != '] = 1;
    		$dbObject = new BaseModel();

    		$getUserDetails =  $dbObject->getSingleRecordById(  config('constants.SUSPEND_HISTORY_TABLE'),
    				[ 'i_id'] ,
    			$checkUniqueWhere  ); 
    		
    		//echo $dbObject->last_query();die;
    		if( (!empty($getUserDetails)) && count(Wild_tiger::objectToArray($getUserDetails)) > 0 ){
    			return false;
    		} else {
    			return true;
    		}
    		
    	}
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
       return trans('messages.error-unique-suspend-date');
    }
}
