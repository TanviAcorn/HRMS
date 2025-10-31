<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\BaseModel;
use App\Helpers\Twt\Wild_tiger;
class UniqueSalaryComponentsName implements Rule
{
	private $recordId;
	private $salaryComponentsType;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
	public function __construct($userId = null,$salaryComponentsType = '')
    {
    	$this->salaryComponentsType = $salaryComponentsType;
    	if($userId > 0 ){
        	$this->recordId = $userId;
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
        	$checkUniqueWhere['v_component_name'] = $value;
        	$checkUniqueWhere['e_salary_components_type'] = $this->salaryComponentsType;
        	$checkUniqueWhere['t_is_deleted != '] = 1;
        	if(!empty($this->recordId)){
        		$checkUniqueWhere['i_id != '] = $this->recordId;
        	}
        	$dbObject = new BaseModel();
        	
        	$getUserDetails =  $dbObject->getSingleRecordById(  config('constants.SALARY_COMPONENTS_MASTER_TABLE'),
        			[ 'i_id' ] ,
        			$checkUniqueWhere  );
        	
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
         return trans('messages.error-unique-salary-components-name');
    }
}
