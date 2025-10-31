<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\MasterController;
use CheckLogin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Login;
use App\Helpers\Twt\Wild_tiger;
use App\Rules\InternationMobileFormat;
use DB;
use App\Helpers\Twt\Zoho_crm;
use App\Lead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;
use App\BaseModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use App\MyAttendanceModel;
use App\HolidayMasterModel;
use App\EmployeeModel;
use App\Models\EmployeeDesignationHistory;
use DateTime;
use App\Models\SuspendHistory;
use App\MyLeaveModel;
use App\LeaveTypeMasterModel;
use App\Models\TimeOff;
use App\Models\LeaveBalanceModel;
use App\LookupMaster;

class DashboardController extends MasterController
{
    //
	public $loginCookieName;
	public function __construct(){
		//parent::__construct();
		$this->middleware('checklogin');
		$this->BaseModel = new BaseModel();
		$this->folderName =  config('constants.ADMIN_FOLDER') . 'dashboard/';
		$this->loginCookieName = config('constants.LOGIN_COOKIE_NAME');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
	}
	
	public function markAsReadNotification(){
		
		$updateNotificationData = [];
		$updateNotificationData['t_read_notification'] = 1;
		$updateNotificationData['dt_read_notification_at'] = date('Y-m-d H:i:s');
		
		$where = [];
		$where['i_login_id'] = (session()->get('role') == config("constants.ROLE_USER") ? session()->get('user_id') : null);
		$where['t_read_notification'] = 0;
		
		$updateStatus = $this->BaseModel->updateTableData(config('constants.EMAIL_HISTORY_TABLE'), $updateNotificationData, $where);
		
		if( $updateStatus != false ){
			Wild_tiger::setFlashMessage ( 'success', trans ( 'messages.success-mark-as-read-notification' ) ) ;
			 
			return redirect()->back();
		} else {
			Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-mark-as-read-notification'  ) );
			 
			return redirect()->back();
		}
		
	}
	
   	public function index(){
   		
   		//echo "<pre>" ;print_r( salaryIncrementReportHeader(2015) ) ;die;
   		$this->holidayMoldel = new HolidayMasterModel();
   		$this->myLeaveModel = new MyLeaveModel();
   		$this->timeoffModel = new TimeOff();
   		$data['pageTitle'] =  trans('messages.dashboard');
   		
   		$getHolidayWhere = [];
   		$getHolidayWhere['active_status'] = 1;
   		$getHolidayWhere['holiday_from_date'] = date('d-m-Y', strtotime('first day of january this year'));
   		$getHolidayWhere['holiday_to_date'] = date('d-m-Y', strtotime('last day of december this year'));
   		$getHolidayWhere['order_by'] = [ 'dt_holiday_date'  => 'asc'];
   		$data['holidayDetails'] = $this->holidayMoldel->getRecordDetails($getHolidayWhere);
   		
   		
   		$nextSevenStartDate = date('Y-m-d' , strtotime("+1 day"));
   		$nextSevenEndDate = date('Y-m-d' , strtotime("+7 day"));
   		
   		$data['todayBirthDayDetails'] = EmployeeModel::whereRaw("e_employment_status != '".config('constants.RELIEVED_EMPLOYMENT_STATUS')."'  and t_is_deleted = 0 and MONTH(dt_birth_date) = MONTH(NOW()) AND DAY(dt_birth_date) = DAY(NOW())")->orderBy('v_employee_full_name' , 'asc')->orderByRaw('CONVERT(v_employee_code, SIGNED) asc')->get();
   		$data['nextSevenDayBirthDayDetails'] = EmployeeModel::whereRaw("e_employment_status != '".config('constants.RELIEVED_EMPLOYMENT_STATUS')."'  and t_is_deleted = 0 and DATE_FORMAT(dt_birth_date, '%m-%d') BETWEEN DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 DAY), '%m-%d') AND DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 DAY), '%m-%d')")->orderByRaw('DATE_FORMAT(dt_birth_date, "%m-%d") asc,v_employee_full_name asc,CONVERT(v_employee_code, SIGNED) asc')->get();;
   		
   		//echo "<pre>";print_r($data['nextSevenDayBirthDayDetails']);die;
   		
   		$data['todayWorkAnniversaryDetails'] = EmployeeModel::whereRaw("e_employment_status != '".config('constants.RELIEVED_EMPLOYMENT_STATUS')."'  and t_is_deleted = 0 and dt_joining_date != CURRENT_DATE and  MONTH(dt_joining_date) = MONTH(NOW()) AND DAY(dt_joining_date) = DAY(NOW())")->orderBy('v_employee_full_name' , 'asc')->orderByRaw('CONVERT(v_employee_code, SIGNED) asc')->get();;
   		$data['nextSevenDayWorkAnniversaryDetails'] = EmployeeModel::whereRaw("e_employment_status != '".config('constants.RELIEVED_EMPLOYMENT_STATUS')."'  and t_is_deleted = 0 and YEAR(dt_joining_date) != '".date('Y')."' and  DATE_FORMAT(dt_joining_date, '%m-%d') BETWEEN DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 DAY), '%m-%d') AND DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 DAY), '%m-%d')")->orderByRaw('DATE_FORMAT(dt_joining_date, "%m-%d") asc,v_employee_full_name asc,CONVERT(v_employee_code, SIGNED) asc')->get();;;
   		
   		
   		$data['todayJoiningDetails'] = EmployeeModel::whereRaw("e_employment_status != '".config('constants.RELIEVED_EMPLOYMENT_STATUS')."'  and t_is_deleted = 0 and dt_joining_date = CURRENT_DATE")->orderBy('v_employee_full_name' , 'asc')->orderByRaw('CONVERT(v_employee_code, SIGNED) asc')->get();;
   		$data['lastSevenDayJoiningDetails'] = EmployeeModel::whereRaw("e_employment_status != '".config('constants.RELIEVED_EMPLOYMENT_STATUS')."'  and t_is_deleted = 0 and  dt_joining_date BETWEEN CURDATE()-INTERVAL 1 WEEK AND CURDATE() - INTERVAL 1 Day")->orderBy("dt_joining_date" , "desc")->orderByRaw('DATE_FORMAT(dt_joining_date, "%m-%d") desc,v_employee_full_name asc,CONVERT(v_employee_code, SIGNED) asc')->get();;;
   		
   		$getAppliedLeaveWhere = [];
   		$getAppliedLeaveWhere['leave_from_date'] = date('Y-m-d');
   		$getAppliedLeaveWhere['leave_to_date'] = date('Y-m-d');
   		$getAppliedLeaveWhere['active_employee_leave'] = true;
   		$getAppliedLeaveWhere['leave_status'] = [ config('constants.APPROVED_STATUS') , config('constants.PENDING_STATUS') ];
   		//$getAppliedLeaveWhere['employee_team'] = $employeeTeamId;
   		
   		$getAppliedLeaveDetails = $this->myLeaveModel->getRecordDetails($getAppliedLeaveWhere);
   		//echo "dd";die;
   		
   		$getYearAppliedLeaveWhere = [];
   		$getYearAppliedLeaveWhere['leave_from_date'] = date('Y-01-01');
   		$getYearAppliedLeaveWhere['leave_to_date'] = date('Y-12-31');
   		$getYearAppliedLeaveWhere['employee_id'] = ( session()->has('user_employee_id') ? session()->get('user_employee_id') : 0 );
   		$getYearAppliedLeaveWhere['leave_status'] = [ config('constants.APPROVED_STATUS') , config('constants.PENDING_STATUS')  ];
   		//$getAppliedLeaveWhere['employee_team'] = $employeeTeamId;
   		//echo "<pre>";print_r($getYearAppliedLeaveWhere);
   		$getYearAppliedLeaveDetails = $this->myLeaveModel->getRecordDetails($getYearAppliedLeaveWhere);
   		//echo "<pre>";print_r($getYearAppliedLeaveDetails);die;
   		
   		$data['onLeaveDetails'] = $getAppliedLeaveDetails;
   		
   		$leaveConsumeInfo = [];
   		$leaveConsumeInfo[config("constants.EARNED_LEAVE_TYPE_ID")] = 0;
   		$leaveConsumeInfo[config("constants.CARRY_FORWARD_LEAVE_TYPE_ID")] = 0;
   		$leaveConsumeInfo[config("constants.PAID_LEAVE_TYPE_ID")] = 0;
   		$leaveConsumeInfo[config("constants.UNPAID_LEAVE_TYPE_ID")] = 0;
   		
   		$leaveAvailableInfo = [];
   		$leaveAvailableInfo[config("constants.EARNED_LEAVE_TYPE_ID")] = 0;
   		$leaveAvailableInfo[config("constants.CARRY_FORWARD_LEAVE_TYPE_ID")] = 0;
   		$leaveAvailableInfo[config("constants.PAID_LEAVE_TYPE_ID")] = 0;
   		$leaveAvailableInfo[config("constants.UNPAID_LEAVE_TYPE_ID")] = 0;
   		$employeeId = ( session()->has('user_employee_id') ? session()->get('user_employee_id') : 0 ); 
   		
   		$leaveBalanceDetails = LeaveBalanceModel::where('t_is_deleted' , 0 )->where('i_employee_id' , $employeeId )->get();
   		
   		if(!empty($leaveBalanceDetails)){
   			foreach($leaveBalanceDetails as $leaveBalanceDetail){
   				$leaveAvailableInfo[$leaveBalanceDetail->i_leave_type_id] += $leaveBalanceDetail->d_current_balance;
   			}
   		}
   		
   		if(!empty($getYearAppliedLeaveDetails)){
   			foreach($getYearAppliedLeaveDetails as $getYearAppliedLeaveDetail){
   				$leaveConsumeInfo[$getYearAppliedLeaveDetail->i_leave_type_id] += $getYearAppliedLeaveDetail->d_no_days;
   			}
   		}
   		//dd($leaveConsumeInfo);
   		
   		$data['leaveAvailableInfo'] = $leaveAvailableInfo;
   		$data['leaveConsumeInfo'] = $leaveConsumeInfo;
   		$data['leaveTypeDetails'] = LeaveTypeMasterModel::where('t_is_deleted' , 0 )->where('t_is_show' ,1)->get();
   		
   		$data['timeOffSelectionDetails'] = timeOffSelectionInfo();
   		
   		
   		$lastAdjustmentTakenWhere = [];
   		$takenAdjustmentDetails = [];
   		if( ( session()->has('user_employee_id') ) && ( session()->get('user_employee_id') > 0 ) ){
   			
   			$lastAdjustmentTakenWhere['time_off_status'] = [ config('constants.APPROVED_STATUS') , config('constants.PENDING_STATUS') ];
   			$lastAdjustmentTakenWhere['employee_id'] = session()->get('user_employee_id');
   			$lastAdjustmentTakenWhere['time_off_from_date'] = date('Y-m-d' , strtotime("-6 month"));
   			$lastAdjustmentTakenWhere['time_off_to_date'] = date('Y-m-d');
   			$lastAdjustmentTakenWhere['time_off_type'] = config('constants.ADJUSTMENT_STATUS');
   			$takenAdjustmentDetails = $this->timeoffModel->getRecordDetails($lastAdjustmentTakenWhere);
   		}
   		
   		$data['adjustmentDetails'] = $takenAdjustmentDetails;
        $data['device'] = detectDevice();
        // Announcements Section with optional category filter
        $category = request('category');
        $annQuery = \App\Models\Announcement::query();
        if (!empty($category) && strtolower($category) !== 'all') {
            if ($category === 'Others') {
                // Include legacy rows where category is NULL as Others
                $annQuery->where(function($q){
                    $q->whereNull('category')->orWhere('category', 'Others');
                });
            } else {
                $annQuery->where('category', $category);
            }
        }
        $annQuery->orderBy('created_at', 'desc');
        // If there is future difference for admin vs user, keep hook here
        $data['announcements'] = $annQuery->paginate(5);
        //echo "<pre>";print_r($data['todayJoiningDetails']);die;
        return view( $this->folderName . 'dashboard' , $data);
	}
	
	public function getYearWiseHoliday(Request $request){
		$this->holidayMoldel = new HolidayMasterModel();
		$selectedYear = (!empty($request->post('selected_year')) ? $request->post('selected_year') : date('Y') );
		$getHolidayWhere = [];
		$getHolidayWhere['active_status'] = 1;
		$getHolidayWhere['holiday_from_date'] = date('d-m-Y', strtotime('first day of january ' . $selectedYear ));
		$getHolidayWhere['holiday_to_date'] = date('d-m-Y', strtotime('last day of december '. $selectedYear ));
		$getHolidayWhere['order_by'] = [ 'dt_holiday_date'  => 'asc'];
		$data['holidayDetails'] = $this->holidayMoldel->getRecordDetails($getHolidayWhere);
		
		$html = view( $this->folderName . 'holiday-modal-view' , $data  )->render();
		echo $html;die;
	}
	
	public function logout(Request $request){
		
		$getCurrentSessionToken = session()->get('_token');
		
		if(!empty($getCurrentSessionToken)){
			$this->BaseModel->updateTableData(config('constants.LOGIN_HISTORY_TABLE'), [ 'dt_logout_time' => config('constants.DATE_TIME') ], [ 'i_login_id' => session()->get('user_id') , 'i_session_id' => $getCurrentSessionToken ] );
		}
		
		Cookie::queue(Cookie::forget( $this->loginCookieName . '_process_email'));
		Cookie::queue(Cookie::forget( $this->loginCookieName . '_process_password'));
		
		$request->session()->flush();
		return redirect('login');
	}
	
	public function changePassword(){
		$data['pageTitle'] =  trans('messages.change-password');
		$data['user_id'] = session()->get('user_id');
		
		return view('admin/change-password' , $data);
	}
	
	public function updatePassword(Request $request ){
		
		$validator = Validator::make($request->all(), [
				'current_password' => 'required',
				'new_password' => 'required',
				'confirm_password' => 'required|same:new_password',
		],[
				'current_password.required' => __('messages.required-current-password') ,
				'new_password.required' => __('messages.required-new-password') ,
				'confirm_password.required' => __('messages.required-confirm-password') ,
		]
		);
	
		if ($validator->fails()) {
			return redirect::back()
			->withErrors($validator)
			->withInput();
		}
	
		$requestUserId =  (!empty(session()->get('user_id')) ? (int)session()->get('user_id') : 0 );
	
		$currentPassword  = $request->input('current_password');
		$newPassword  = $request->input('new_password');
		$confirmPassword  = $request->input('confirm_password');
	
		if( $requestUserId > 0 ){
				
			if(  $newPassword ==  $confirmPassword ){
	
				$masterUserData = Login::find ( $requestUserId );
	
				
				if (password_verify($currentPassword, $masterUserData->v_password) != true ) {
					Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.invalid-current-password' ) );
					return redirect::back();
				}
				
				//$masterUserData->v_decode_password = $newPassword;
				$masterUserData->v_password = password_hash($newPassword, PASSWORD_DEFAULT);;
					
				$updateUser = $masterUserData->save ();
				
				if ($updateUser != false) {
					$request->session()->flush();
					Wild_tiger::setFlashMessage ( 'success', trans ( 'messages.success-update-password' ) );
					
					return redirect ( 'login' );
				}
	
				Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-update-password' ) );
				return redirect ( 'login' );
			}
				
			Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.confirm-password-not-match' ) );
			return redirect ( 'login' );
				
		}
	
		Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.system-error' ) );
		return redirect::back();
	}
	
	public function employeeAttendanceImportExcel(){
		$data['pageTitle'] =  trans('messages.import-ecxel');
   		return view('admin/employee-attendance-import-ecxel' , $data);
	}
	
	public  function importExcel(Request $request){
		if(!empty($_FILES)){
			$formValidation = [];
			$formValidation['upload_ecxel_file'] = 'required|mimes:xls,xlsx';
		
			$validator = Validator::make ( $request->all (), $formValidation , [
					'upload_ecxel_file.mimes' => __ ( 'messages.only-excel-file-allowed' ),
			] );
		
			if ($validator->fails ()) {
				//echo "<pre>";print_r($validator->errors());die;
				$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.upload-file') ] ) ) );
			}
			
			$importFile = "";
			if( !empty( $_FILES['upload_ecxel_file']['name'] ) ){
				$importFile = $this->uploadExcelFile($request , 'upload_ecxel_file');
			}
			if (!empty($importFile)){
			
				$uploadedFilePath = config('constants.FILE_STORAGE_PATH') .   $importFile;
				
				$successMessage = trans('messages.success-file-data-imported',['module'=> trans('messages.upload-file') ]);
				$errorMessages =  trans('messages.error-file-data-imported',['module'=> trans('messages.upload-file') ]);
	
				$result = false;
				$rowDetails = [];
				try {
						
					$filePath = $uploadedFilePath;
					$reader = ReaderEntityFactory::createReaderFromFile($filePath);
					$reader->open($filePath);
	
					$cellCount = 0;
					$finalData = [];
					foreach ($reader->getSheetIterator() as $sheet) {
						foreach ($sheet->getRowIterator() as $rowKey =>  $row) {
							$cells = $row->getCells();
							$rowData = [];
							$rowCellCount = ( $cellCount > 0 ?  $cellCount : count($cells) );
							for($i = 0; $i <  $rowCellCount ; $i++ ){
								if(isset($cells[$i])){
									$rowData[] = $cells[$i]->getValue();
								} else {
									$rowData[] = "";
								}
							}
							if(!empty($rowData)){
								$cellCount = count($rowData);
								$finalData[] = $rowData;
							}
						}
					}
					$reader->close();
					$rowDetails = [];
					$allDataInSheet = $finalData;
					$rowDetails = [];
					echo "<pre>";print_r($allDataInSheet);die;
	    			if(!empty($allDataInSheet)){
	    				foreach ($allDataInSheet as $key => $value) {
	    						
	    					if( $key > 0 ){
	    				
	    						if( $key == 1 ){
	    							$excelKeys = array_values($value);
	    						} else {
	    				
	    							$rowDetail = [];
	    							$rowDetail = array_combine($excelKeys, $value);
	    							if(!empty($rowDetail)){
	    								$rowDetails[] = $rowDetail;
	    							}
	    						}
	    				
	    				
	    					}
	    				}
	    			}
		    		}catch (Exception $e) {
		    			$this->ajaxResponse(101, $e->getMessage());
		    		}
		    		echo "<pre>";print_r($rowDetails);die;
				    $allExcelErrors = [];
		    		if(!empty($rowDetails)){
		    			foreach ($rowDetails as $key=> $rowDetail){
		    				$excelRecordNo = ( $key + 1 );
		    				$rowExcelData = [];
		    				foreach( $rowDetail as $rowKey => $rowValue){
		    					$rowKey = strtolower( trim($rowKey) );
		    					$rowKey = str_replace(" ", "_", $rowKey);
		    					$rowValue = ( trim($rowValue) );
		    					switch (trim($rowKey)){
		    						case 'start_time':
		    							$rowExcelData['d_start_time'] = (!empty($rowValue) ? $rowValue : null);
		    							break;
		    						case 'end_time':
		    							$rowExcelData['d_end_time'] = (!empty($rowValue) ? $rowValue : null);
		    							break;
		    						case 'date':
		    							$rowExcelData['dt_date'] = (!empty($rowValue) ? $rowValue : null);
		    							break;
		    						case 'employee_code':
		    							$rowExcelData['i_employee_id'] = (!empty($rowValue) ? $rowValue : null);
		    							break;
		    					}
		    		
		    				}
		    				/* if((!empty(array_filter($rowExcelData)))){
		    					if(empty($rowExcelData['v_fba_po_no'])){
		    						$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.fba-sheet')  , 'srNo' => $excelRecordNo ] ) ;
		    					}
		    					$masterExcelData[] = $rowExcelData;
		    				} */
		    			}
		    		
		    		}
    		
		  		} 
			}
     }
     
     
     public function attedanceEntry(){
     	
     	$startDate = '2023-02-01';
     	$endDate = '2023-02-28';
     	
     	$allDates = getDatesFromRange( $startDate , $endDate );
		$employeeId = 10;     	
     	$data['employeeId'] = Wild_tiger::encode($employeeId);
     	$data['allDates'] = $allDates;
     	$data['startDate'] = $startDate;
     	$data['endDate'] = $endDate;
     	$data['pageTitle'] =  trans('messages.dashboard');;
     	return view('admin/attendance-entry' , $data);
     }
     
     public function addAttedance(Request $request){
     	
     	$this->holidayModule = new HolidayMasterModel();
     	
     	$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
     	$startDate = (!empty($request->input('start_date')) ? dbDate($request->input('start_date')) : monthStartDate() );
     	$endDate = (!empty($request->input('end_date')) ? dbDate($request->input('end_date')) : monthEndDate() );
     	//var_dump($employeeId);
     	$getEmployeeWhere = [];
     	$getEmployeeWhere['i_id'] = $employeeId;
     	$getEmployeeInfo = EmployeeModel::with(['shiftInfo' , 'shiftInfo.shiftTimingInfo' , 'weekOffInfo' ])->where($getEmployeeWhere)->first();
     	
     	$shiftLastUpdateDate = ( isset($getEmployeeInfo->dt_last_update_shift) ? $getEmployeeInfo->dt_last_update_shift : null );
     	$weekOffLastUpdateDate = ( isset($getEmployeeInfo->dt_last_update_week_off) ? $getEmployeeInfo->dt_last_update_week_off : null );
     	
     	$currentShiftInfo = ( isset($getEmployeeInfo->shiftInfo->shiftTimingInfo) ? $getEmployeeInfo->shiftInfo->shiftTimingInfo[0] : [] );
     	$currentWeekOffInfo = ( isset($getEmployeeInfo->weekOffInfo->weeklyOffDetail) ? $getEmployeeInfo->weekOffInfo->weeklyOffDetail : [] );
     	
     	$getSuspendWhere = "";
     	$getSuspendWhere = "(  dt_start_date >= '".$startDate."'  or dt_end_date >= '".$startDate."'  )  and (  dt_start_date <= '".$endDate."'  or dt_end_date <= '".$endDate."'  )"; 
     	$getSuspendHistoryDetails = SuspendHistory::whereRaw($getSuspendWhere)->get();
     	
     	$employeeWiseSuspendDetails = [];
     	if(!empty($getSuspendHistoryDetails)){
     		foreach($getSuspendHistoryDetails as $getSuspendHistoryDetail){
     			if( strtotime($getSuspendHistoryDetail->dt_start_date)  != strtotime($getSuspendHistoryDetail->dt_end_date) ){
     				$getAllSuspendDates = getDatesFromRange($getSuspendHistoryDetail->dt_start_date, $getSuspendHistoryDetail->dt_end_date);
     				if(!empty($getAllSuspendDates)){
     					foreach($getAllSuspendDates as $getAllSuspendDate){
     						$employeeWiseSuspendDetails[$getSuspendHistoryDetail->i_employee_id][] = $getAllSuspendDate;
     					}
     				}
     			} else {
     				$employeeWiseSuspendDetails[$getSuspendHistoryDetail->i_employee_id][] = $getSuspendHistoryDetail->dt_start_date;
     			}
     		}
     	}
     	
     	$allWeekDetails = weekDayDetails();
     	
     	$allWeekAlternateCount = [];
     	foreach($allWeekDetails as $allWeekKey  => $allWeekDetail){
     		$allWeekAlternateCount[$allWeekKey] = 0;
     	}
     	//echo "<pre>";print_r($getEmployeeInfo);
     	
     	
     	//echo "<pre>";print_r($currentShiftInfo);die;
     	//echo "<pre>dd";print_r($currentWeekOffInfo);
     	
     	//echo "<pre>";print_r($request->all());
     	
     	//var_dump($startDate);
     	
     	$allDates = getDatesFromRange( $startDate , $endDate );
     	
     	$holidayDetails = $this->holidayModule->getRecordDetails( [ 'holiday_from_date' =>  $startDate , 'holiday_to_date' => $endDate  ] );
     	
     	$holidayDates = [];
     	if(!empty($holidayDetails)){
     		$holidayDates = array_column(objectToArray($holidayDetails), 'dt_holiday_date');
     	}
     	
     	
     	$dayWeekOffInfo = $currentWeekOffInfo;
     	
     	/* echo  $this->holidayModule->last_query();
     	echo "<pre>";print_r($holidayDates);die; */
     	
     	$presentCount = 0;
     	$weekOffStartDate = null;
     	if(!empty($allDates)){
     		
     		$result = false;
			DB::beginTransaction();
     		
			try{
				foreach($allDates as $allDate){
				
					$rowData = [];
					$weekDay = strtolower( date('l' , strtotime($allDate) ) );
					
					$alternateColumnName = 'v_'.$weekDay.'_alternate_off';
					$allColumnName = 'v_'.$weekDay.'_all_off';
					
					//echo "week day  = "; var_dump($weekDay);
					if( strtotime($shiftLastUpdateDate) >= strtotime($allDate) ){
						$rowData['d_original_start_time'] = ( isset($currentShiftInfo['v_'.$weekDay.'_start_time']) ? $currentShiftInfo['v_'.$weekDay.'_start_time'] : null );
						$rowData['d_original_end_time'] = ( isset($currentShiftInfo['v_'.$weekDay.'_end_time']) ? $currentShiftInfo['v_'.$weekDay.'_end_time'] : null );
						$rowData['d_original_break_start_time'] = ( isset($currentShiftInfo['v_'.$weekDay.'_break_start_time']) ? $currentShiftInfo['v_'.$weekDay.'_break_start_time'] : null );
						$rowData['d_original_break_end_time'] = ( isset($currentShiftInfo['v_'.$weekDay.'_break_end_time']) ? $currentShiftInfo['v_'.$weekDay.'_break_end_time'] : null );
					} else {
						
						$getParticularDayShiftWhere = "i_employee_id = '".$employeeId."' and t_is_deleted != 1 and '".$allDate."' between dt_start_date and dt_end_date";
						//echo $getParticularDayShiftWhere;
						$getParticularDayShiftDetails = EmployeeDesignationHistory::with( [ 'shiftInfo' , 'shiftInfo.shiftTimingInfo'  ] )->whereRaw($getParticularDayShiftWhere)->first();
						//echo "<pre> getParticularDayShiftDetails";print_r($getParticularDayShiftDetails);
						if(!empty($getParticularDayShiftDetails)){
							$getParticularDayShiftInfo  = ( isset($getParticularDayShiftDetails->shiftInfo->shiftTimingInfo) ? $getParticularDayShiftDetails->shiftInfo->shiftTimingInfo[0] : [] );
							$rowData['d_original_start_time'] = ( isset($getParticularDayShiftInfo['v_'.$weekDay.'_start_time']) ? $getParticularDayShiftInfo['v_'.$weekDay.'_start_time'] : null );
							$rowData['d_original_end_time'] = ( isset($getParticularDayShiftInfo['v_'.$weekDay.'_end_time']) ? $getParticularDayShiftInfo['v_'.$weekDay.'_end_time'] : null );
							$rowData['d_original_break_start_time'] = ( isset($getParticularDayShiftInfo['v_'.$weekDay.'_break_start_time']) ? $getParticularDayShiftInfo['v_'.$weekDay.'_break_start_time'] : null );
							$rowData['d_original_break_end_time'] = ( isset($getParticularDayShiftInfo['v_'.$weekDay.'_break_end_time']) ? $getParticularDayShiftInfo['v_'.$weekDay.'_break_end_time'] : null );
						}
					}
					
					if( strtotime($weekOffLastUpdateDate) >= strtotime($allDate) ){
						$weekOffStartDate = $weekOffLastUpdateDate;
						$dayWeekOffInfo = $currentWeekOffInfo;
					} else {
						$weekOffStartDate = null;
						$dayWeekOffInfo = [];
						$getParticularDayWeekOffWhere = "i_employee_id = '".$employeeId."' and t_is_deleted != 1 and '".$allDate."' between dt_start_date and dt_end_date";
						//echo $getParticularDayShiftWhere;
						$getParticularDayWeekOffDetails = EmployeeDesignationHistory::with( [ 'weeklyOffInfo' , 'weeklyOffInfo.weeklyOffDetail'  ] )->whereRaw($getParticularDayWeekOffWhere)->first();
						//echo "<pre> getParticularDayShiftDetails";print_r($getParticularDayShiftDetails);
						if(!empty($getParticularDayWeekOffDetails)){
							$weekOffStartDate = $getParticularDayWeekOffDetails->dt_start_date;
							$dayWeekOffInfo  = ( isset($getParticularDayWeekOffDetails->weeklyOffInfo->weeklyOffDetail) ? $getParticularDayWeekOffDetails->weeklyOffInfo->weeklyOffDetail : [] );
						}
					}
					
					if( isset($dayWeekOffInfo->$alternateColumnName) && ( $dayWeekOffInfo->$alternateColumnName == config('constants.SELECTION_YES')) ){
						$allWeekAlternateCount[$weekDay] = ( $allWeekAlternateCount[$weekDay] + 1 );
					}
					
					
					$checkAttendanceWhere = [];
					$checkAttendanceWhere['i_employee_id'] = $employeeId;
					$checkAttendanceWhere['dt_date'] = $allDate;
					$checkAttendaceEntry = MyAttendanceModel::where($checkAttendanceWhere)->first();
				
					
					$rowData['d_start_time'] = (!empty($request->input('start_time_'.$allDate)) ? dbTime($request->input('start_time_'.$allDate)) : null );
					$rowData['d_end_time'] = (!empty($request->input('end_time_'.$allDate)) ? dbTime($request->input('end_time_'.$allDate)) : null );
					
					if( !empty($rowData['d_start_time']) && (!empty($rowData['d_end_time'])) ){
						if(!empty($checkAttendaceEntry)){
							if( !in_array( $checkAttendaceEntry->e_status , [ config('constants.HALF_LEAVE_STATUS') , config('constants.ADJUSTMENT_STATUS')  ] ) ){
								$rowData['e_status'] = config('constants.PRESENT_STATUS');
							}
							$this->BaseModel->updateTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData, [ 'i_id' => $checkAttendaceEntry->i_id ] );
						} else {
							$presentCount = $presentCount + config('constants.FULL_LEAVE_VALUE');
							$rowData['i_employee_id'] = $employeeId;
							$rowData['dt_date'] = $allDate;
							$rowData['e_status'] = config('constants.PRESENT_STATUS');
							$this->BaseModel->insertTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData );
						}	
					} else {
						
						if(in_array($allDate,$holidayDates)){
							
							$rowData = [];
							$rowData['dt_date'] = $allDate;
							$rowData['i_employee_id'] = $employeeId;
							$rowData['e_status'] = config('constants.HOLIDAY_STATUS');
							
							if(!empty($checkAttendaceEntry)){
								$this->BaseModel->updateTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData, [ 'i_id' => $checkAttendaceEntry->i_id ] );
							} else {
								$this->BaseModel->insertTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData );
							}
							
						} else {
							//echo "date  = " ; var_dump($allDate);
							
							$entryDone = false;
							
							
							if( isset($employeeWiseSuspendDetails[$employeeId]) && (!empty($employeeWiseSuspendDetails[$employeeId])) && (in_array(  $allDate , $employeeWiseSuspendDetails[$employeeId]) )  ){
								
								$entryDone = true;
								
								$rowData = [];
								$rowData['dt_date'] = $allDate;
								$rowData['i_employee_id'] = $employeeId;
								$rowData['e_status'] = config('constants.SUSPEND_STATUS');
								 
								if(!empty($checkAttendaceEntry)){
									$this->BaseModel->updateTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData, [ 'i_id' => $checkAttendaceEntry->i_id ] );
								} else {
									$this->BaseModel->insertTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData );
								}
							}
							
							if( (!empty($weekOffStartDate)) && (!empty($dayWeekOffInfo)) ){
								
								if( isset($dayWeekOffInfo->$allColumnName) && ( $dayWeekOffInfo->$allColumnName == config('constants.SELECTION_YES')) ){
		                         	
									$entryDone = true;
									
									$rowData = [];
		                         	$rowData['dt_date'] = $allDate;
		                         	$rowData['i_employee_id'] = $employeeId;
		                         	$rowData['e_status'] = config('constants.WEEK_OFF_STATUS');
		                         		
		                         	if(!empty($checkAttendaceEntry)){
		                         		$this->BaseModel->updateTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData, [ 'i_id' => $checkAttendaceEntry->i_id ] );
		                         	} else {
		                         		$this->BaseModel->insertTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData );
		                         	}
		                         }
		                         
		                         if( isset($dayWeekOffInfo->$alternateColumnName) && ( $dayWeekOffInfo->$alternateColumnName == config('constants.SELECTION_YES')) ){
		                         	
		                         	//echo "alter nate off date =".$allDate;echo "<br><br>";
		                         	
		                         	//$allWeekAlternateCount[$weekDay] = ( $allWeekAlternateCount[$weekDay] + 1 );
		                         	
		                         	if( $allWeekAlternateCount[$weekDay] % 2 != 0 ){
		                         		$entryDone = true;
		                         		
		                         		$rowData = [];
		                         		$rowData['dt_date'] = $allDate;
		                         		$rowData['i_employee_id'] = $employeeId;
		                         		$rowData['e_status'] = config('constants.WEEK_OFF_STATUS');
		                         		
		                         		//echo "<pre> week off entry";print_r($rowData);
		                         		
		                         		if(!empty($checkAttendaceEntry)){
		                         			$this->BaseModel->updateTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData, [ 'i_id' => $checkAttendaceEntry->i_id ] );
		                         		} else {
		                         			$this->BaseModel->insertTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData );
		                         		}
		                         		
		                         	}
		                         	
		                         }
		                         
							}
							
							
							//echo "<pre> ddd";print_r($dayWeekOffInfo);die;
							if( $entryDone != true ){
								$rowData = [];
								$rowData['dt_date'] = $allDate;
								$rowData['i_employee_id'] = $employeeId;
								$rowData['e_status'] = config('constants.ABSENT_STATUS');
								
								if(!empty($checkAttendaceEntry)){
									$this->BaseModel->updateTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData, [ 'i_id' => $checkAttendaceEntry->i_id ] );
								} else {
									$this->BaseModel->insertTableData(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), $rowData );
								}
							
							}
						}
						
					}
				}
				$result = true;
			}catch(\Exception $e){
				var_dump($e->getMessage());die;
				DB::rollback();
			}
     		
     		//die("welcoem");
     		if($result != false){
     			//die("welcome");
     			DB::commit();
     			Wild_tiger::setFlashMessage('success', "success");
     			return redirect( config('app.url') . 'attendance-entry' );
     		}
     		DB::rollback();
     		Wild_tiger::setFlashMessage('danger', "error");
     		return redirect( config('app.url') . 'attendance-entry' );
     		
     	}
     }
     
     public function duplicateLeaveCheck(){
     		//$employeeId = $this->employeeId;
     		//$leaveStartDate = (!empty($request->input('leave_from_date')) ? dbDate($request->input('leave_from_date')) : null );
     		//$leaveEndDate = (!empty($request->input('leave_to_date')) ? dbDate($request->input('leave_to_date')) : null );
     			
     		//$dualDateFromSession = (!empty($request->input('dual_date_from_session')) ? trim($request->input('dual_date_from_session')) : null );
     		//$dualDateToSession = (!empty($request->input('dual_date_to_session')) ? trim($request->input('dual_date_to_session')) : null );
     		//$singleDateSession = (!empty($request->input('single_date_session')) ? trim($request->input('single_date_session')) : null );
     	
     		$this->crudModel =  new MyLeaveModel();
     	
     		$duplicateLeave = false;
     		
     		$employeeId = 1;
     		$leaveStartDate = '2023-03-28';
     		$leaveEndDate = '2023-03-30';
     		
     		$dualDateFromSession = config('constants.FIRST_HALF_LEAVE'); // config('constants.FIRST_HALF_LEAVE')
     		$dualDateToSession = config('constants.SECOND_HALF_LEAVE'); //config('constants.SECOND_HALF_LEAVE')
     		$dualDateFromSession = $dualDateToSession = null;
     		$singleDateSession = config('constants.SECOND_HALF_LEAVE'); //config('constants.FULL_DAY_LEAVE')
     		
     		$getAllAppliedLeaveDetails = [];
     			
     		$getAppliedLeaveWhere = [];
     		$getAppliedLeaveWhere['leave_from_date'] = $leaveStartDate;
     		$getAppliedLeaveWhere['leave_to_date'] = $leaveEndDate;
     		$getAppliedLeaveWhere['employee_id'] = $employeeId;
     		
     		//\DB::enableQueryLog();
     		$getAppliedLeaveDetails = $this->crudModel->getRecordDetails($getAppliedLeaveWhere);
     		//dd(\DB::getQueryLog());
     		if( strtotime($leaveStartDate)  != strtotime($leaveEndDate) ){
     			$newLeaveDuration = getDatesFromRange($leaveStartDate, $leaveEndDate);
     		} else {
     			$newLeaveDuration = [];
     			$newLeaveDuration[] = $leaveStartDate;
     		}
     		
     		echo "new leave duration<pre>";print_r($newLeaveDuration);
     		
     		//echo "<pre>";print_r($getAppliedLeaveDetails);
     		
     		$appliedFullDayLeaveDetails = [];
     		$appliedHalfDayLeaveDetails = [];
     		
     		$duplicateLeaveFound = false;
     		if( (!empty($getAppliedLeaveDetails)) ){
     			foreach($getAppliedLeaveDetails as $getAppliedLeaveDetail){
     				if( strtotime($getAppliedLeaveDetail->dt_leave_from_date)  != strtotime($getAppliedLeaveDetail->dt_leave_to_date) ){
     					$getLeaveDates = getDatesFromRange($getAppliedLeaveDetail->dt_leave_from_date, $getAppliedLeaveDetail->dt_leave_to_date);
     					echo "<pre> getLeaveDates";print_r($getLeaveDates);
     					if(!empty($getLeaveDates)){
     						
     						$durationFirstLeave = $getLeaveDates[0];
     						$durationLastLeave = end($getLeaveDates);
     						foreach($getLeaveDates as $getLeaveKey => $getLeaveDate ){
     							
     							if( ( $getLeaveDate == $durationFirstLeave )  || ( $getLeaveDate == $durationLastLeave  ) ){
     								if( ( $getLeaveDate == $durationFirstLeave ) ){
     									if( $getAppliedLeaveDetail->e_from_duration == config('constants.SECOND_HALF_LEAVE') ){
     										$appliedHalfDayLeaveDetails[config('constants.SECOND_HALF_LEAVE')][] = $getLeaveDate;
     									} else {
     										$appliedFullDayLeaveDetails[] = $getLeaveDate;
     									}
     								}  
     								
     								if( ( $getLeaveDate == $durationLastLeave ) ){
     									//echo "<pre> appliedFullDayLeaveDetails";print_r($appliedFullDayLeaveDetails);echo "<br><br><br>";
     									if( $getAppliedLeaveDetail->e_to_duration == config('constants.FIRST_HALF_LEAVE') ){
     										$appliedHalfDayLeaveDetails[config('constants.FIRST_HALF_LEAVE')][] = $getLeaveDate;
     									} else {
     										$appliedFullDayLeaveDetails[] = $getLeaveDate;
     									}
     								} 
     								
     								
     							} else {
     								$appliedFullDayLeaveDetails[] = $getLeaveDate;
     							
     							}
     						}
     					}
     				} else {
     
     					if( in_array( $getAppliedLeaveDetail->dt_leave_from_date , $newLeaveDuration ) ){
     						
     						if( in_array( $getAppliedLeaveDetail->e_duration , [ config('constants.FIRST_HALF_LEAVE') , config('constants.SECOND_HALF_LEAVE') ] ) ){
     							$appliedHalfDayLeaveDetails[$getAppliedLeaveDetail->e_duration][] = $getAppliedLeaveDetail->dt_leave_from_date;
     						} else {
     							$appliedFullDayLeaveDetails[] = $getAppliedLeaveDetail->dt_leave_from_date;
     						}
     					}
     				}
     			}
     		}
     		$appliedFullDayLeaveDetails = (!empty($appliedFullDayLeaveDetails) ? array_unique($appliedFullDayLeaveDetails) : [] );
     		echo "<pre> Full";print_r($appliedFullDayLeaveDetails);
     		echo "<pre> HalfDay";print_r($appliedHalfDayLeaveDetails);
     		
     		
     		
     		if( strtotime($leaveStartDate)  != strtotime($leaveEndDate) ){
     			$newLeaveDuration = getDatesFromRange($leaveStartDate, $leaveEndDate);
     			
     			echo "<pre>";print_r($newLeaveDuration);
     			
     			if(!empty($newLeaveDuration)){
     				$durationFirstLeave = $newLeaveDuration[0];
     				$durationLastLeave = end($newLeaveDuration);
     				
     				echo "first leave  = ".$durationFirstLeave;echo "<br><br>";
     				echo "last leave  = ".$durationLastLeave;echo "<br><br>";
     				
     				foreach($newLeaveDuration as $newLeave){
     					if( ( $newLeave == $durationFirstLeave )  || ( $newLeave == $durationLastLeave  ) ){
     						if($newLeave == $durationFirstLeave){
     							if(!empty($dualDateFromSession) || (!empty($dualDateToSession))){
     								if(!empty($dualDateFromSession) && (in_array($newLeave,$appliedFullDayLeaveDetails))){
     									$duplicateLeave = true;
     								}
     								if(!empty($dualDateToSession) && isset($appliedHalfDayLeaveDetails[config('constants.SECOND_HALF_LEAVE')]) && (!empty($appliedHalfDayLeaveDetails[config('constants.SECOND_HALF_LEAVE')])) && (in_array($newLeave,$appliedHalfDayLeaveDetails[config('constants.SECOND_HALF_LEAVE')])) ){
     									$duplicateLeave = true;
     								}
     							} else {
     								if(in_array($newLeave,$appliedFullDayLeaveDetails)){
     									$duplicateLeave = true;
     								}
     							}
     						}
     						
     						if($newLeave == $durationLastLeave){
     							if(!empty($dualDateFromSession) || (!empty($dualDateToSession))){
     								if(!empty($dualDateToSession) && (in_array($newLeave,$appliedFullDayLeaveDetails))){
     									$duplicateLeave = true;
     								}
     								if(!empty($dualDateFromSession) && isset($appliedHalfDayLeaveDetails[config('constants.FIRST_HALF_LEAVE')]) && (!empty($appliedHalfDayLeaveDetails[config('constants.FIRST_HALF_LEAVE')])) && (in_array($newLeave,$appliedHalfDayLeaveDetails[config('constants.FIRST_HALF_LEAVE')])) ){
     									$duplicateLeave = true;
     								}
     							} else {
     								if(in_array($newLeave,$appliedFullDayLeaveDetails)){
     									$duplicateLeave = true;
     								}
     							}
     						}
     						
     					} else {
     						
     						if(in_array($newLeave,$appliedFullDayLeaveDetails)){
     							$duplicateLeave = true;
     						}
     					}
     				}
     			}
     		} else {
     			
     			if( in_array( $singleDateSession , [ config('constants.FIRST_HALF_LEAVE') , config('constants.FIRST_HALF_LEAVE') ] ) ){
     				if( ( isset($appliedHalfDayLeaveDetails[$singleDateSession])  && (!empty($appliedHalfDayLeaveDetails[$singleDateSession])) && in_array($leaveStartDate,$appliedHalfDayLeaveDetails[$singleDateSession]) ) ){
     					$duplicateLeave = true;
     				}
     			} else {
     				if(in_array($leaveStartDate,$appliedFullDayLeaveDetails)){
     					$duplicateLeave = true;
     				}
     			}
     		}
     		
     		echo "dupliacate status = ".$duplicateLeave;	
     		var_dump($duplicateLeave);die;
     		if( $duplicateLeave != false ){
     			$this->ajaxResponse(101, trans('messages.error-duplicate-leave'));
     		}
     			
     		$this->ajaxResponse(1, trans('messages.success'));
     			
     			
     	
     }
     
     public function viewAllEmployeeList(){
     	
     	if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
     		return redirect('access-denied');
     	}
     	$this->employeeModel = new EmployeeModel();
     	
     	$data = [];
     	$data['pageTitle'] = trans('messages.manage-role');
     	
     	$page = $this->defaultPage ;
     	
     	$selectedEmployeeStatus = config('constants.WORKING_EMPLOYMENT_STATUS');
     	$data['selectedEmployeeStatus'] = $selectedEmployeeStatus;
     	$whereData['employment_status'] = $selectedEmployeeStatus;
     	
     	$paginationData = [];
     	#get pagination data for first page
     	if($page == $this->defaultPage ){
     	
     		$totalRecords = count($this->employeeModel->getRecordDetails($whereData));
     	
     		$lastPage = ceil($totalRecords/$this->perPageRecord);
     	
     		$paginationData['current_page'] = $this->defaultPage;
     	
     		$paginationData['per_page'] = $this->perPageRecord;
     	
     		$paginationData ['last_page'] = $lastPage;
     	
     	}
     	$whereData ['limit'] = $this->perPageRecord;
     	
     	
     	
     	$data['recordDetails'] = $this->employeeModel->getRecordDetails( $whereData );
     	$data['pageNo'] = $page;
     	$data['perPageRecord'] = $this->perPageRecord;;
     	$data['teamDetails'] = LookupMaster::where('v_module_name',config('constants.TEAM_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
     	$data['designationDetails'] = LookupMaster::where('v_module_name',config('constants.DESIGNATION_LOOKUP'))->where('t_is_deleted',0)->orderBy('v_value', 'ASC')->get();
     	//$data['employeeDetails'] = EmployeeModel::where('t_is_deleted',0)->orderBy('v_employee_full_name', 'ASC')->get();
     	
     	$employeeWhere = [];
     	$employeeWhere['employment_status'] = $selectedEmployeeStatus;
     	$data['employeeDetails'] = $this->getEmployeeDropdownDetails($employeeWhere);
     	
     	
     	$data['totalRecordCount'] = $totalRecords;
     	$data['pagination'] = $paginationData;
     	
     	$data['roleDetails'] = roleList();
     	
     	return view( $this->folderName . 'employee-role-update' , $data);
     }
     
     public function filterAllEmployeelist(Request $request){
     	
     	$this->employeeModel = new EmployeeModel();
     	
     	$page = (! empty($request->post('page')) ? $request->post('page') : 1);
     	
     	$whereData =  $paginationData = [];
     	
     	if(!empty($request->post('search_employee'))){
     		$whereData['master_id'] = (int)Wild_tiger::decode($request->post('search_employee'));
     	}
     	if(!empty($request->post('search_team'))){
     		$whereData['team_record'] = (int)Wild_tiger::decode($request->post('search_team'));
     	}
     	if(!empty($request->post('search_designation'))){
     		$whereData['designation'] = (int)Wild_tiger::decode($request->post('search_designation'));
     	}
     	## employment status filter
     	if(!empty($request->post('search_employment_status'))){
     		$whereData['employment_status'] =  trim($request->post('search_employment_status'));
     	}
     	
     	if ($page == $this->defaultPage) {
     	
     		$totalRecords = count($this->employeeModel->getRecordDetails( $whereData ));
     	
     		$lastpage = ceil($totalRecords / $this->perPageRecord);
     	
     		$paginationData['current_page'] = $this->defaultPage;
     	
     		$paginationData['per_page'] = $this->perPageRecord;
     	
     		$paginationData['last_page'] = $lastpage;
     	}
     	
     	if ($page == $this->defaultPage) {
     		$whereData['offset'] = 0;
     		$whereData['limit'] = $this->perPageRecord;
     	
     	} else if ($page > $this->defaultPage) {
     		$whereData['offset'] = ($page - 1) * $this->perPageRecord;
     		$whereData['limit'] = $this->perPageRecord;
     	}
     	
     	
     	$data['recordDetails'] = $this->employeeModel->getRecordDetails( $whereData );
     
     	if(isset($totalRecords)){
     		$data ['totalRecordCount'] = $totalRecords;
     	}
     	
     	$data['pagination'] = $paginationData;
     		
     	$data['pageNo'] = $page;
     	
     	$data['perPageRecord'] = $this->perPageRecord;
     	
     	$data['roleDetails'] = roleList();
     	
     	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'dashboard/employee-role-update-list' )->with ( $data )->render();
     		
     	echo $html;die;
     }
     
     public function updateEmployeeRole(Request $request){
     	
     	$data['roleDetails'] = roleList();
     	$employeeWhere = [];
     	$employeeWhere['t_is_deleted'] = 0;
     	$recordDetails = EmployeeModel::where($employeeWhere)->get();
     	
     	$displayRecordEncodeIds = ($request->has('display_record_ids') ? explode("," , $request->input('display_record_ids') ) : [] );
     	
     	$displayRecordIds = [];
     	if(!empty($displayRecordEncodeIds)){
     		$displayRecordIds = array_map(function($displayRecordEncodeId){
     			return (int)($displayRecordEncodeId);
     		}, $displayRecordEncodeIds);
     	}
     	
     	
     	if(!empty($recordDetails)){
     		
     		$successMessage =  trans('messages.success-update',['module'=> trans('messages.employee-role')]);
     		$errorMessages = trans('messages.error-update',['module'=> trans('messages.employee-role') ]);
     		
     		$result = false;
     		DB::beginTransaction();
     		
     		
     		try{
     			foreach($recordDetails as $recordDetail){
     				if(in_array( $recordDetail->i_login_id , $displayRecordEncodeIds ) ){
     					if($request->has('role_'.$recordDetail->i_login_id ) ){
     						$updateData = [];
     						$updateData['v_role'] = trim($request->input('role_'.$recordDetail->i_login_id ));
     						$this->BaseModel->updateTableData(config('constants.LOGIN_MASTER_TABLE'), $updateData , [ 'i_id' => $recordDetail->i_login_id ] );
     					}	
     				}
     			}
     			$result = true;
     		}catch(\Exception $e){
     			$result = false;
     			DB::rollback();
     		}
     		//var_dump($result);die;
     		if( $result != false ){
     			DB::commit();
     			Wild_tiger::setFlashMessage ('success', $successMessage  );
     		} else {
     			DB::rollback();
     			Wild_tiger::setFlashMessage ('danger', $errorMessages  );
     		}
     		
     		return redirect()->back();
     	}
     	Wild_tiger::setFlashMessage ('danger', trans('messages.system-error')  );
     	return redirect()->back();
     }
}

