<?php
namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;
use App\Models\VillageMasterModel;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Database\Eloquent\Model;
class UniqueVillageName implements Rule
{
	private $recordId;
	private $stateId;
	private $cityId; 
	/**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userId = null,$cityId = '' ,$stateId = '')
	 {
    	$this->cityId = $cityId;
    	$this->stateId = $stateId;
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
    		$checkUniqueWhere = [];
    		$checkUniqueWhere['singleRecord'] = true;
    		$checkUniqueWhere['unique_village_name'] = $value;
    		$checkUniqueWhere['city_id'] = $this->cityId;
    		$checkUniqueWhere['state_id'] = $this->stateId;
    		if(!empty($this->recordId)){
    			$checkUniqueWhere['unique_master_id'] = $this->recordId;
    		} 
    		$villageModel = new VillageMasterModel();
    		$getUserDetails =  $villageModel->getRecordDetails($checkUniqueWhere);
    		if( (!empty($getUserDetails)) && count(Wild_tiger::objectToArray($getUserDetails)) > 0 ){
	    			return false;
	    	} else {
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
        return trans('messages.error-unique-village-name');
    }
}
