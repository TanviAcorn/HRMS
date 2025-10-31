<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionGroupTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	$permissionGroupDetails = [
    			[
    					'Employee Summary',
    					'Employee List'
    			],
    			[
    					'Manage Attendance - Manually',
    					'Present Summary',
    					'Attendance Report - Daily & Monthly',
    					'HR Attendance - Day Wise',
    					'Punch Report - Live'
    			],
    			[
    					'Leave Detailed Summary',
    					'Leave Report'
    			],
    			[
    					'Time Off Detailed Summary',
    					'Time Off Report'
    			],
    			[
    					'Salary Summary',
    					'Salary Report',
    					'On Hold Salary Report',
    					'Salary Increment Report',
    					'Salary Calculation'
    			],
    			'Documents Report',
    			[
    					'Shifts',
    					'Weekly Offs'
    			],
    			[
    					'Incident Summary',
    					'Incident Report'
    			],
    			'Master Sheet',
    			'Employee Duration Report',
    			'Salary Report For Account Team',
    			'Leave Report - Month Wise Count',
    			'Form 16 Report',
    			'Statutory Bonus Report',
    			'Resignation Report',
    			/* [
    					'Team Master',
    					'Designation Master',
    					'Recruitment Source Master',
    					'Holiday Master',
    					'Probation Policy Master',
    					'Notice Period Policy Master',
    					'Termination Reasons',
    					'Resign Reasons'
    			],
    			[
    					'Document Folder',
    					'Document Type'
    			],
    			[
    					'Salary Components',
    					'Salary Groups',
    					'Bank Master'
    			],
    			[
    					'State',
    					'City',
    					'Village'
    			] */
    	];
    	 
    	$insertRecordDetails = [];
    	
    	$loopIndex = 1;
    	$moduleIndex = 1;
    	if (isset($permissionGroupDetails) && !empty($permissionGroupDetails)){
    		foreach ($permissionGroupDetails as $permissionGroupDetail){
    			if(!is_array($permissionGroupDetail)){
    				$rowData = [];
    				$rowData['i_module_id'] = $moduleIndex++;
    				$rowData['v_group_name'] = (isset($permissionGroupDetail) && !empty($permissionGroupDetail) ? trim($permissionGroupDetail) : '');
    				$rowData['i_sequence'] = $loopIndex++;
    				$rowData['i_created_id'] = 1;
    				$rowData['dt_created_at'] = date('Y-m-d H:i:s');
    				$rowData['i_updated_id'] = 1;
    				$rowData['dt_updated_at'] = date('Y-m-d H:i:s');
    				$rowData['v_ip'] = '::1';
    				$insertRecordDetails[] = $rowData;
    			} else {
    				$moduleIndexChild = $moduleIndex++;
    				if(isset($permissionGroupDetail) && !empty($permissionGroupDetail)){
    					foreach ($permissionGroupDetail as $permissionGroupInfo){
    						$rowData = [];
    						$rowData['i_module_id'] = $moduleIndexChild;
    						$rowData['v_group_name'] = isset($permissionGroupInfo) && !empty($permissionGroupInfo) ? $permissionGroupInfo : '';
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
    		DB::table(config('constants.PERMISSION_GROUP_TABLE'))->truncate();
    		DB::table(config('constants.PERMISSION_GROUP_TABLE'))->insert($insertRecordDetails);
    	}
    }
}
