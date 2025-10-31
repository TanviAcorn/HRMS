<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Login;
use DB;
use Illuminate\Http\Request;
use App\BaseModel;
use Illuminate\Database\Eloquent\Model;

class UniqueEmail implements Rule
{
	private $registerUserId;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userId = null)
    {
        //
        if($userId > 0 ){
        	$this->registerUserId = $userId;
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
        //
    	//return true;
    	if(!empty($value)){
    		$value = trim($value);
        	$emailCheckWhere = [];
        	$emailCheckWhere['lm.v_email'] = $value;
        	$emailCheckWhere['lm.t_is_deleted !='] = 1;
        	
        	if( $this->registerUserId > 0 ){
        		$emailCheckWhere['lm.i_id != '] = $this->registerUserId;
        	}
        	
        	$dbObject = new BaseModel();
        	
        	$getUserDetails =  $dbObject->getSingleRecordById(  config('constants.LOGIN_MASTER_TABLE') . ' as lm' ,
        			[ 'lm.i_id' ] ,
        			$emailCheckWhere  );
        	
        	if(count(objectToArray($getUserDetails)) > 0 ){
        		return false;
        	} else {
        		return true;
        	}
        	
        }
        
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.error-duplicate-email');
    }
}
