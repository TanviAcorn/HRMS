<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\LeaveSummaryModel;
use App\EmployeeModel;
class LeaveSummaryController extends MasterController
{
	public function __construct(){
	
		parent::__construct();
		$this->crudModel =  new LeaveSummaryModel();
		$this->moduleName = trans('messages.leave-summary');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->tableName = config('constants.LEAVE_SUMMARY_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'leave-summary/' ;
		$this->redirectUrl = config('constants.LEAVE_SUMMARY_MASTER_URL');
	}
	
	public function index(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		$data = $whereData = [];
		$startDate = monthStartDate();
		$endDate = monthEndDate();
		
		$data = $this->getLeaveSummaryDetails( $startDate , $endDate );
		
		$data['pageTitle'] = trans('messages.leave-detailed-summary');
		$data['startDate'] = $startDate;
		$data['endDate'] = $endDate;
		//dd($data);
		return view( $this->folderName . 'leave-summary')->with($data);
	}
	
	public function filterLeaveSummary(Request $request){
		
		$startDate = (!empty($request->post('search_leave_from_date')) ? dbDate($request->post('search_leave_from_date')) : monthStartDate() );
		$endDate = (!empty($request->post('search_leave_to_date')) ? dbDate($request->post('search_leave_to_date')) : monthEndDate($startDate) );
		$data = $this->getLeaveSummaryDetails( $startDate , $endDate );
		$html = view( config('constants.AJAX_VIEW_FOLDER') .'leave-summary/leave-summary-list' )->with($data)->render();
		echo $html;die;
	}
	
	private function getLeaveSummaryDetails( $startDate , $endDate ){
		
		$where = [];
		$where['startDate'] = $startDate;
		$where['endDate'] = $endDate;
		$where['leaveStatus'] = [ config('constants.APPROVED_STATUS') , config('constants.PENDING_STATUS')  ];
		$where['employment_status'] = config('constants.WORKING_EMPLOYMENT_STATUS');
		$allDates = getDatesFromRange($startDate,$endDate);
		
		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_LEAVE_SUMMARY'), session()->get('user_permission')  ) ) ){
			$where['show_all'] = true;
		}
		
		$getTakenLeaveDetails = $this->crudModel->getTakenLeaveDetails($where);
		
		$unTakenLeavewhere = [];
		$unTakenLeavewhere['having'] = "apply_leave_count is null";
		$unTakenLeavewhere['startDate'] = $startDate;
		$unTakenLeavewhere['endDate'] = $endDate;
		$unTakenLeavewhere['employment_status'] = config('constants.WORKING_EMPLOYMENT_STATUS');
		

		if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_LEAVE_SUMMARY'), session()->get('user_permission')  ) ) ){
			$unTakenLeavewhere['show_all'] = true;
		}
		
		$getNotTakenLeaveDetails = $this->crudModel->getNotTakenLeaveDetails($unTakenLeavewhere);
		
		$employeeWiseLeaveCountDetails = [];
		
		$uniqueEmployeeIds = [];
		
		if(!empty($getTakenLeaveDetails)){
			foreach($getTakenLeaveDetails as $getTakenLeaveDetail){
				$leaveFromDate = $getTakenLeaveDetail->dt_leave_from_date;
				$leaveToDate = $getTakenLeaveDetail->dt_leave_to_date;
				$leaveCount = 0;
				if( ( $getTakenLeaveDetail->t_is_half_leave ==  1 ) ||  ( strtotime($leaveFromDate) == strtotime($leaveToDate)  )  ){
					$leaveCount = $getTakenLeaveDetail->d_no_days;
				} else {
					
					$leaveDurationDateDetails =  getDatesFromRange($leaveFromDate,$leaveToDate);
					if( $getTakenLeaveDetail->i_id == 5 ){
						//echo "<pre>";print_r($leaveDurationDateDetails);
					}
					if(!empty($leaveDurationDateDetails)){
						foreach($leaveDurationDateDetails as $leaveDurationDateDetail){
							if(in_array($leaveDurationDateDetail,$allDates)){
								
								
								if( ( strtotime($getTakenLeaveDetail->dt_leave_from_date) == strtotime( $leaveDurationDateDetail ) ) ||  ( strtotime($getTakenLeaveDetail->dt_leave_to_date) ==  strtotime( $leaveDurationDateDetail ) ) ){
									//echo "daadassa = ".$leaveDateRange;echo "<br><br>";
									
									if( strtotime( $getTakenLeaveDetail->dt_leave_from_date )  ==  strtotime( $leaveDurationDateDetail ) ){
										if($getTakenLeaveDetail->e_from_duration == config('constants.FIRST_HALF_LEAVE')){
											$leaveCount += config('constants.FULL_LEAVE_VALUE');
										} else {
											$leaveCount += config('constants.HALF_LEAVE_VALUE');
											//echo "count = ".$leaveCount;echo "<br><br>";
										}
									}
									
									if( strtotime( $getTakenLeaveDetail->dt_leave_to_date )  ==  strtotime( $leaveDurationDateDetail ) ){
								
										if($getTakenLeaveDetail->e_to_duration == config('constants.FIRST_HALF_LEAVE')){
											$leaveCount += config('constants.HALF_LEAVE_VALUE');
											//echo "count = ".$leaveCount;echo "<br><br>";
										} else {
											$leaveCount +=  config('constants.FULL_LEAVE_VALUE');
										}
									}
										
								} else {
									//echo "ffee = ".$leaveDurationDateDetail;echo "<br><br>";
									$leaveCount += config('constants.FULL_LEAVE_VALUE');
									
								}
								
								
								
								
								
								/* $leaveCount = config('constants.FULL_LEAVE_VALUE');
								if( strtotime($leaveDurationDateDetail) == strtotime($leaveFromDate) ){
									if( in_array($getTakenLeaveDetail->e_from_duration, [ config('constants.FIRST_HALF_LEAVE') , config('constants.SECOND_HALF_LEAVE')  ] ) ){
										$leaveCount = config('constants.HALF_LEAVE_VALUE');
									} else {
										$leaveCount = config('constants.FULL_LEAVE_VALUE');
									}
								}
									
								if( strtotime($leaveDurationDateDetail) == strtotime($leaveToDate) ){
									if( in_array($getTakenLeaveDetail->e_to_duration, [ config('constants.FIRST_HALF_LEAVE') , config('constants.SECOND_HALF_LEAVE')  ] ) ){
										$leaveCount = config('constants.HALF_LEAVE_VALUE');
									} else {
										$leaveCount = config('constants.FULL_LEAVE_VALUE');
									}
								} */
							}
						}
					}
					
					
				}
				
				//echo "leave count = ".$leaveCount;echo "<br><br>";
				//echo "employee id = ".$getTakenLeaveDetail->i_id;echo "<br><br>";
				
				if( $leaveCount > 0 ){
					if(in_array($getTakenLeaveDetail->i_employee_id , $uniqueEmployeeIds) ){
						$employeeWiseLeaveCountDetails[$getTakenLeaveDetail->i_employee_id]['leave_count'] += $leaveCount;
						$employeeWiseLeaveCountDetails[$getTakenLeaveDetail->i_employee_id]['leaveOccurence'] += 1;
					} else {
						$rowData = [];
						$rowData['profile_pic'] = ( isset($getTakenLeaveDetail->employeeInfo->v_profile_pic) ? $getTakenLeaveDetail->employeeInfo->v_profile_pic : '' );
						$rowData['employee_name'] = ( isset($getTakenLeaveDetail->employeeInfo->v_employee_full_name) ? $getTakenLeaveDetail->employeeInfo->v_employee_full_name : '' );
						$rowData['designation_name'] = ( isset($getTakenLeaveDetail->employeeInfo->designationInfo->v_value) ? $getTakenLeaveDetail->employeeInfo->designationInfo->v_value : '' );
						$rowData['leave_count'] = $leaveCount;
						$rowData['leaveOccurence'] = 1;
						$employeeWiseLeaveCountDetails[$getTakenLeaveDetail->i_employee_id] = $rowData;
						$uniqueEmployeeIds[] = $getTakenLeaveDetail->i_employee_id;
					}
				}
			}
		}
		
		if(!empty($employeeWiseLeaveCountDetails)){
			array_multisort(array_column($employeeWiseLeaveCountDetails, 'leave_count'), SORT_DESC, $employeeWiseLeaveCountDetails);
		}
		
		$result = [];
		$result['mostLeaveTakenDetails'] = $employeeWiseLeaveCountDetails;
		$result['notTakenLeaveDetails'] = $getNotTakenLeaveDetails;
		$result['searchDateRange'] = convertDateFormat($startDate,  'd/m/Y') . ' - ' . convertDateFormat($endDate,  'd/m/Y');
		return $result;
	}
    
}
