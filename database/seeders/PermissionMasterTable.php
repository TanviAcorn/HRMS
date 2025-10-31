<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionMasterTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	$permissionModuleDetails = [
    			'Employee Summary',
    			'Employee List',
    			
    			'Manage Attendance - Manually',
    			'Present Summary',
    			'Attendance Report - Daily & Monthly',
    			'HR Attendance - Day Wise',
    			'Punch Report - Live',
    			
    			'Leave Detailed Summary',
    			'Leave Report',
    			
    			'Time Off Detailed Summary',
    			'Time Off Report',
    			
    			'Salary Summary',
    			'Salary Report',
    			'On Hold Salary Report',
    			'Salary Increment Report',
    			'Salary Calculation',
    			
    			'Documents Report',
    			
    			'Shifts',
    			'Weekly Offs',
    			
    			'Incident Summary',
    			'Incident Report',
    			
    			'Master Sheet',
    			'Employee Duration Report',
    			'Salary Report For Account Team',
    			'Leave Report - Month Wise Count',
    			'Form 16 Report',
    			'Statutory Bonus Report',
    			'Resignation Report',
    			
    			/* 'Team Master',
    			'Designation Master',
    			'Recruitment Source Master',
    			'Holiday Master',
    			'Probation Policy Master',
    			'Notice Period Policy Master',
    			'Termination Reasons',
    			'Resign Reasons',
    			
    			'Document Folder',
    			'Document Type',
    			
    			'Salary Components',
    			'Salary Groups',
    			'Bank Master',
    			
    			'State',
    			'City',
    			'Village' */
    	];
    	 
    	$excludeModuleInView = [];
    	$excludeModuleInAdd = [
    			'Master Sheet',
    			'Documents Reports',
    			'Manage Attendance - Manually',
    			'Employee Duration Report',
    			'Salary Report For Account Team', 
    			'Leave Report - Month Wise Count',
    			'Form 16 Report',
    			'Statutory Bonus Report',
    			'Resignation Report',
    			'Employee Summary',
    			'Present Summary',
    			'Time Off Detailed Summary',
    			'Leave Detailed Summary',
    			'Salary Summary',
    			'Incident Summary',
    			'Attendance Report - Daily & Monthly',
    			'HR Attendance - Day Wise',
    			'Punch Report - Live',
    			'Leave Report',
    			'Time Off Report',
    			'Salary Report',
    			'On Hold Salary Report',
    			'Salary Increment Report',
    			'Salary Calculation',
    	];
    	$excludeModuleInEdit = [
    			'Master Sheet',
    			'Manage Attendance - Manually',
    			'Employee Duration Report',
    			'Salary Report For Account Team',
    			'Leave Report - Month Wise Count',
    			'Form 16 Report',
    			'Statutory Bonus Report',
    			'Resignation Report',
    			'Employee Summary',
    			'Present Summary',
    			'Time Off Detailed Summary',
    			'Leave Detailed Summary',
    			'Salary Summary',
    			'Leave Report',
    			'Time Off Report',
    			'Incident Summary',
    			'Attendance Report - Daily & Monthly',
    			'On Hold Salary Report',
    			'Salary Calculation',
    			'HR Attendance - Day Wise',
    			'Punch Report - Live',
    			'Salary Report',
    			'Salary Increment Report',
    			'Documents Reports',
    	];
    	$excludeModuleInDelete = [
    			'Employee List',
    			'Master Sheet',
    			'Employee Duration Report',
    			'Salary Report For Account Team',
    			'Leave Report - Month Wise Count',
    			'Form 16 Report',
    			'Statutory Bonus Report',
    			'Resignation Report',
    			'Employee Summary',
    			'Present Summary',
    			'Time Off Detailed Summary',
    			'Leave Detailed Summary',
    			'Salary Summary',
    			'Incident Summary',
    			'Attendance Report - Daily & Monthly',
    			'HR Attendance - Day Wise',
    			'Punch Report - Live',
    			'Leave Report',
    			'Time Off Report',
    			'Salary Report',
    			'On Hold Salary Report',
    			'Salary Increment Report',
    			'Salary Calculation',
    			'Documents Reports',
    			'Manage Attendance - Manually',
    	];
    	
    	$insertRecordDetails = [];
    	 
    	$actions = ['View', 'Add', 'Edit', 'Delete'];
    	
    	$loopIndex = 0;
    	if (isset($permissionModuleDetails) && !empty($permissionModuleDetails)){
    		foreach ($permissionModuleDetails as $permissionModuleDetail){
    			$loopIndex++;
    			foreach ($actions as $action) {
    				if(($action == 'View' && !in_array($permissionModuleDetail, $excludeModuleInView)) ||
    						($action == 'Add' && !in_array($permissionModuleDetail, $excludeModuleInAdd)) ||
    						($action == 'Edit' && !in_array($permissionModuleDetail, $excludeModuleInEdit)) ||
    						($action == 'Delete' && !in_array($permissionModuleDetail, $excludeModuleInDelete))
    				){
    					$rowData = [];
    					$moduleName = (isset($action) && !empty($action) ? strtolower(str_replace(' ', '_', trim($action))) . '_' : '') . (isset($permissionModuleDetail) && !empty($permissionModuleDetail) ? strtolower(str_replace(' ', '_', str_replace(' - ', ' ', trim($permissionModuleDetail)))) : '');
    					$moduleTitle = (isset($action) && !empty($action) ? trim($action) . ' ' : '') . (isset($permissionModuleDetail) && !empty($permissionModuleDetail) ? trim($permissionModuleDetail) : '');
    					$rowData['i_group_id'] = $loopIndex;
    					$rowData['v_name'] = (isset($moduleName) && !empty($moduleName) ? trim($moduleName) : '');
    					$rowData['v_title'] = (isset($moduleTitle) && !empty($moduleTitle) ? trim($moduleTitle) : '');
    					$rowData['t_sort'] = $loopIndex;
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
    	 
    	if(isset($insertRecordDetails) && !empty($insertRecordDetails)){
    		DB::table(config('constants.PERMISSION_MASTER_TABLE'))->truncate();
    		DB::table(config('constants.PERMISSION_MASTER_TABLE'))->insert($insertRecordDetails);
    	}
    	
    }
}
