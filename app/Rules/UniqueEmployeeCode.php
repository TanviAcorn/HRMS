<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\BaseModel;
use App\Helpers\Twt\Wild_tiger;
class UniqueEmployeeCode implements Rule
{
	private $employeeId;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($employeeId = null)
    {
    	if($employeeId > 0 ){
        	$this->recordId = $employeeId;
        }
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
	        	$checkUniqueWhere['v_employee_code'] = $value;
	        	$checkUniqueWhere['t_is_deleted != '] = 1;
	        	if(!empty($this->recordId)){
	        		$checkUniqueWhere['i_id != '] = $this->recordId;
	        		
	        	}
	        	$dbObject = new BaseModel();
	        	$getEmployeeDetails =  $dbObject->getSingleRecordById( config('constants.EMPLOYEE_MASTER_TABLE'),[ 'i_id' ] ,$checkUniqueWhere  );
	        	 
	        	if( (!empty($getEmployeeDetails)) && count(Wild_tiger::objectToArray($getEmployeeDetails)) > 0 ){
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
         return trans('messages.error-unique-employee-code');
    }
}
