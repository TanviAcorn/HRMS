<?php
namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;
use App\BaseModel;
use App\Login;
use DB;
use Illuminate\Http\Request;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Support\Facades\Log;

class CheckLastPassword implements Rule
{
	private $requestUserId;
	private $checkRegex;
	private $requestData = [];
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($request = [])
    {
    	$this->requestData = $request->all();
    	$this->checkRegex = false;
        //
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
    		if(  ( config('constants.CHECK_OLD_PASSWORD')  == 1 ) ||  ( config('constants.CHECK_PASSWORD_REGEX')  == 1 ) ){
    			if( config('constants.CHECK_PASSWORD_REGEX') == 1 ){
    	
    				$checkPasswordRegex = preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.* )(?=.*[^a-zA-Z0-9]).{8,16}$/m", $value);
    				//Log::info("passw rodr refes  = " . $checkPasswordRegex );
    				if( $checkPasswordRegex != true ){
    					$this->checkRegex = true;
    					//Log::info("errro found" );
    					return false;
    				}
    			}
    			 
    			if( config('constants.CHECK_OLD_PASSWORD') == 1 ){
    				 
    				if( isset($this->requestData['user_id']) &&  (!empty($this->requestData['user_id'])) ){
    					$this->requestUserId = (int)Wild_tiger::decode($this->requestData['user_id']);
    				}
    	
    				$checkRecordWhere = [];
    				$checkRecordWhere['i_id'] = $this->requestUserId;
    				$checkRecordWhere['t_is_deleted'] = 0;
    	
    				$getRecordInfo = Login::where($checkRecordWhere)->first();
    	
    				if(count(objectToArray($getRecordInfo)) > 0 ){
    	
    					$oldPassword = $getRecordInfo->v_password;
    	
    					if( password_verify($value, $oldPassword)){
    						//Log::info("errroddd found" );
    						return false;
    					}
    					return true;
    				} else {
    					return false;
    				}
    			}
    		} else {
    			return true;
    		}
    		 
    	}
        //
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if( $this->checkRegex  != false ){
    		return trans('messages.error-strong-password');
    	}
    	return trans('messages.error-last-password-same');
    }
}
