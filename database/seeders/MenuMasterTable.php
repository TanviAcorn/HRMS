<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuMasterTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$menuDetails = [
				'Employees',
    			'Company',
    			'Report',
    			//'Masters'
    	];
    	 
    	$insertRecordDetails = [];
    	 
    	$loopIndex = 1;
    	if (isset($menuDetails) && !empty($menuDetails)){
    		foreach ($menuDetails as $menuDetail){
    			$rowData = [];
    			$rowData['v_menu_name'] = (isset($menuDetail) && !empty($menuDetail) ? trim($menuDetail) : '');
    			$rowData['i_sequence'] = $loopIndex++;
    			$rowData['i_created_id'] = 1;
    			$rowData['dt_created_at'] = date('Y-m-d H:i:s');
    			$rowData['i_updated_id'] = 1;
    			$rowData['dt_updated_at'] = date('Y-m-d H:i:s');
    			$rowData['v_ip'] = '::1';
    			$insertRecordDetails[] = $rowData;
    		}
    	}
    	 
    	if(isset($insertRecordDetails) && !empty($insertRecordDetails)){
    		DB::table(config('constants.MENU_MASTER_TABLE'))->truncate();
    		DB::table(config('constants.MENU_MASTER_TABLE'))->insert($insertRecordDetails);
    	}
    }
}
