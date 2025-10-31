<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleMasterTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$moduleDetails = [
    			[
    					'Employees',
    					'Attendance',
    					'Leaves',
    					'Time Off',
    					'Salary',
    					'Documents',
    			],
				[
						'Shift And WeekOffs',
						'Incidents',
				],
    			[
    					'Master Sheet',
		    			'Employee Duration Report',
		    			'Salary Report For Account Team',
		    			'Leave Report - Month Wise Count',
		    			'Form 16 Report',
		    			'Statutory Bonus Report',
		    			'Resignation Report',
    			],
    			/* [
    					'Company Master',
    					'Document Master',
    					'Salary Master',
    					'Location Master'
    			] */
    	];
    	 
    	$insertRecordDetails = [];
    	 
    	$loopIndex = 1;
    	$menuIndex = 1;
    	if (isset($moduleDetails) && !empty($moduleDetails)){    		
	    	foreach ($moduleDetails as $moduleDetail){
	    		if(!is_array($moduleDetail)){
		    		$rowData = [];
		    		$rowData['i_menu_id'] = $menuIndex++;
		    		$rowData['v_module_name'] = (isset($moduleDetail) && !empty($moduleDetail) ? trim($moduleDetail) : '');
		    		$rowData['i_sequence'] = $loopIndex++;
		    		$rowData['i_created_id'] = 1;
		    		$rowData['dt_created_at'] = date('Y-m-d H:i:s');
		    		$rowData['i_updated_id'] = 1;
		    		$rowData['dt_updated_at'] = date('Y-m-d H:i:s');
		    		$rowData['v_ip'] = '::1';
		    		$insertRecordDetails[] = $rowData;    			
	    		} else {
	    			$menuIndexParent = $menuIndex++;
	    			if(isset($moduleDetail) && !empty($moduleDetail)){
		    			foreach ($moduleDetail as $moduleInfo){
			    			$rowData = [];
			    			$rowData['i_menu_id'] = $menuIndexParent;
			    			$rowData['v_module_name'] = isset($moduleInfo) && !empty($moduleInfo) ? trim($moduleInfo) : '';
			    			$rowData['i_sequence'] = $loopIndex++;
			    			$rowData['i_created_id'] = 1;
			    			$rowData['dt_created_at'] = date('Y-m-d H:i:s');
			    			$rowData['i_updated_id'] = 1;
			    			$rowData['dt_updated_at'] = date('Y-m-d H:i:s');
			    			$rowData['v_ip'] = '::1';
			    			$insertRecordDetails[] = $rowData;    				
		    			}	    				
	    			}
	    		}
	    	}
    	}
    	
    	if(isset($insertRecordDetails) && !empty($insertRecordDetails)){
    		DB::table(config('constants.MODULE_MASTER_TABLE'))->truncate();
    		DB::table(config('constants.MODULE_MASTER_TABLE'))->insert($insertRecordDetails);
    	}
    }
}
