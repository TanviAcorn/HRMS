<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Helpers\Twt\Wild_tiger;
use App\BaseModel;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\GuestController;
use App\Helpers\Twt\Zoho_crm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Providers\ComposerServiceProvider;
use App\Http\Middleware\CheckLogin;
use App\EmployeeModel;
use App\Models\EmployeeDesignationHistory;
use \PhpOffice\PhpSpreadsheet\Writer\Csv;
use \PhpOffice\PhpSpreadsheet\Reader\Xls;
use App\HolidayMasterModel;
use App\MyAttendanceModel;
use App\Models\TimeOff;
use App\Models\SuspendHistory;
use App\MyLeaveModel;






class MasterController extends GuestController
{
	public $loggedUserRole;
	public $perPageRecord;
	public $firstUriSegment;
	public $secondUriSegment;
	public $folderName;
	public $moduleName;
	public $redirectUrl;
	
	public function __construct(){
		parent::__construct();
		$this->middleware('checklogin');
		$this->BaseModel = new BaseModel();
		$this->guestMethod = new GuestController();
		$allUrlSegmentDetails = (!empty( request()->segments()) ?  request()->segments() : [] );
		$this->firstUriSegment = (isset($allUrlSegmentDetails[0]) ? $allUrlSegmentDetails[0] : "" );
		$this->secondUriSegment = (isset($allUrlSegmentDetails[1]) ? $allUrlSegmentDetails[1] : "" );
	}
	
	//
    public function ajaxResponse($status , $messages , $data = [] ){
    	$result = [];
    	$result['status_code'] = $status;
    	$result['message'] = $messages;
    	if(!empty($data)){
    		$result['data'] = (!empty($data) ? $data : null );
    	}
    	echo json_encode($result);die;
    }
    
    public function updateMasterStatus( $request , $tableName , $moduleName ){
    	
    	$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
    	
    	$currentStatus = trim($request->current_status);
    	
    	$updateData = [];
    	if( strtolower( $currentStatus ) ==  strtolower ( config('constants.ENABLE_STATUS') ) ){
    		$updateStatus = trans('messages.disable');
    		$updateData['t_is_active']  = 0;
    	} else if( strtolower( $currentStatus ) ==  strtolower ( config('constants.DISABLE_STATUS') ) ){
    		$updateStatus = trans('messages.enable');
    		$updateData['t_is_active']  = 1;
    	}
    	
    	$updatedmodule =  $moduleName;
    	
    	if(!empty($updateData)){
    	
    		$result = $this->BaseModel->updateTableData(  $tableName , $updateData , [ 'i_id' => $recordId ]);
    		
    		if( $result != false ){
    			$message = trans ( 'messages.success-status-update', [ 'module' => $updatedmodule ] );
    			$this->ajaxResponse( 1 , $message , [ 'update_status'  =>  ( $updateStatus ) ] );
    		}
    	
    	}
    	$message = trans ( 'messages.error-status-update', [ 'module' => $updatedmodule ] );
    	$this->ajaxResponse( 101 , $message );
    	
    }
    
    public function setLoggedUserData(){
    	$this->loggedUserRole = Session::get('role');
    	
    	if( ( $this->loggedUserRole == config('constants.ROLE_SUPERADMIN')) ||  ( $this->loggedUserRole == config('constants.ROLE_TEAM')) || (  ( $this->loggedUserRole == config('constants.ROLE_PARTNER')  ) && ( session()->get('user_type') == config('constants.ADMIN_USER_TYPE')  ) ) || (  ( $this->loggedUserRole == config('constants.ROLE_DISTRIBUTOR')  ) && ( session()->get('user_type') == config('constants.ADMIN_USER_TYPE')  ) )  ){
    		$this->checkAllowedOpt = true;
    	} else {
    		$this->checkAllowedOpt = false;
    	}
    	
    }
    
	public function uploadFile( $request , $fieldName , $folderName ,  $allowedExtensions = [ 'jpg' , 'jpeg' , 'png' ] ){
    
    	$uploadedImagePath = '';
    
    	$response = [];
    	$response['status'] = false;
    	$response['message'] = trans('messages.please-upload-file-img');
    	if($request->hasFile($fieldName)) {
    
    		$allowedMimeTypes = [];
    		if(!empty($allowedExtensions)){
    			foreach($allowedExtensions as $allowedExtension){
    				switch($allowedExtension){
    					case 'jpg':
    						$allowedMimeTypes[] = "image/jpeg";
    						break;
    					case 'jpeg':
    						$allowedMimeTypes[] = "image/jpeg";
    						break;
    					case 'png':
    						$allowedMimeTypes[] = "image/png";
    						break;
    					case 'pdf':
    						$allowedMimeTypes[] = "application/pdf";
    						break;
    					case 'xls':
    						$allowedMimeTypes[] = "application/vnd.ms-excel";
    						$allowedMimeTypes[] = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
    						break;
    					case 'xlsx':
    						$allowedMimeTypes[] = "application/vnd.ms-excel";
    						$allowedMimeTypes[] = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
    						break;
    					case 'doc':
    						$allowedMimeTypes[] = "application/msword";
    						$allowedMimeTypes[] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
    						break;
    					case 'docx':
    						$allowedMimeTypes[] = "application/msword";
    						$allowedMimeTypes[] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
    						break;
    				}
    			}
    		}
    		
    		$uploadedFileSize = $request->$fieldName->getSize();
    		
    		$sizeInMb = ( ( $uploadedFileSize > 0 ) ?  round( ( $uploadedFileSize / ( 1024 * 1024 ) ) , 0 ) :  0 );
    		
    		if( $sizeInMb > config('constants.UPLOAD_FILE_LIMIT_SIZE') ){
    			$errorMessage = trans('messages.error-maximum-file-size');
    			$response['message'] = $errorMessage;
    			return $response;
    		}
    		
    		
    		$uploadedFileMimeType = $request->$fieldName->getClientMimeType();
    
    		if(!in_array($uploadedFileMimeType,$allowedMimeTypes)){
    			$errorMessage = trans('messages.error-only-specific-are-allowed' , [ 'fileType' => implode(", " , $allowedExtensions ) ] );
    			$response['message'] = $errorMessage;
    			return $response;
    		}
    
    		$uploadedFileExtension = $request->$fieldName->getClientOriginalExtension();
    
    		if(!in_array(strtolower($uploadedFileExtension),$allowedExtensions)){
    			$errorMessage = trans('messages.error-only-specific-are-allowed' , [ 'fileType' => implode(", " , $allowedExtensions ) ] );
    			$response['message'] = $errorMessage;
    			return $response;
    		}
    
    		// Get filename with extension
    		$filenameWithExt = $request->file($fieldName)->getClientOriginalName();
    
    		// Get just filename
    		$filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
    		$filename = createSlug( strtolower($filename) );
    		// Get just ext
    		$extension = $request->file($fieldName)->getClientOriginalExtension();
    
    		//Filename to store
    		$fileNameToStore = $filename.'_'.time().'.'.$extension;
    
    
    		// Upload Image
    		$uploadedImagePath = $request->file($fieldName)->storeAs( config('constants.UPLOAD_FOLDER') .  $folderName , $fileNameToStore);
    
    		$response['status'] = true;
    		$response['filePath'] = $folderName .  $fileNameToStore;
    
    		return $response;
    		 
    	}
    
    	return $response;
    }
    
    public function uploadMultipleFile( $request , $fieldName , $folderName ){
    	 
    	$uploadedImagePath = [];
    	
    	$response['status'] = false;
    	
    	if($request->hasFile($fieldName)) {
    		
    		foreach($request->file($fieldName) as $file){
    			
    			$filenameWithExt = $file->getClientOriginalName();
    			
    			$uploadedFileSize = $file->getSize();
    			
    			$sizeInMb = ( ( $uploadedFileSize > 0 ) ?  round( ( $uploadedFileSize / ( 1024 * 1024 ) ) , 0 ) :  0 );
    			
    			if( $sizeInMb > config('constants.UPLOAD_FILE_LIMIT_SIZE') ){
    				$errorMessage = trans('messages.error-maximum-file-size');
    				$response['message'] = $errorMessage;
    				return $response;
    			}
    			
    			
    			
    			$filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
    			
    			$filename = createSlug($filename);
    			
    			$extension = $file->getClientOriginalExtension();
    			
    			$fileNameToStore = $filename.'_'.time().'.'.$extension;
    			
    			$uploadedFile = $file->storeAs( config('constants.UPLOAD_FOLDER') . $folderName , $fileNameToStore);
    			
    			$uploadedImagePath[] = $folderName .  $fileNameToStore;
    		}
    	}
    	$response['status'] = true;
    	$response['uploadedFilePaths'] = $uploadedImagePath;
    	return $response;
    }
    
    public function removeRecord($tableName , $recordId , $messageModuleName ){
    	
    	if( $recordId > 0 && (!empty($tableName)) ){
    		$updateTableData = [];
    		$updateTableData['t_is_active'] = 0;
    		$updateTableData['t_is_deleted'] = 1;
    		$deletedRecord = false;
    		
    		DB::beginTransaction();
    		
    		$deletedRecord = $this->BaseModel->deleteTableData(  $tableName ,  $updateTableData , [ 'i_id' => $recordId ] );
    		
    		if( $messageModuleName == trans('messages.incident-report') ){
    			$this->BaseModel->deleteTableData( config('constants.INCIDENT_ATTACHMENT_TABLE') , $updateTableData, ['i_incedent_id' => $recordId]);
    		}
    		
    		if( $deletedRecord != false ){
    				
    			DB::commit();
    			
    			Wild_tiger::setFlashMessage ( 'success', trans ( 'messages.success-delete', [
    				'module' => $messageModuleName
				] ) );
    			
    			return redirect()->back();
    		} else {
				
				DB::rollback();
    			
    			Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-delete', [
    					'module' => $messageModuleName
    			] ) );
    			
    			return redirect()->back();
    		}
    		
    	}
    	DB::rollback();
    	Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-delete', [
    			'module' => $moduleName
    	] ) );
    	return redirect()->back();
    }
    
    public function CallRaw($procName, $parameters = [], $isExecute = false)
    {
    	
    	$syntax = '';
    	for ($i = 0; $i < count($parameters); $i++) {
    		$syntax .= (!empty($syntax) ? ',' : '') . '?';
    	}
    	$syntax = 'CALL ' . $procName . '(' . $syntax . ');';
    
    	$pdo = DB::connection()->getPdo();
    	$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
    	$stmt = $pdo->prepare($syntax,[\PDO::ATTR_CURSOR=>\PDO::CURSOR_SCROLL]);
    	for ($i = 0; $i < count($parameters); $i++) {
    		$stmt->bindValue((1 + $i), $parameters[$i]);
    	}
    	$exec = $stmt->execute();
    	if (!$exec) return $pdo->errorInfo();
    	if ($isExecute) return $exec;
    
    	$results = [];
    	do {
    		try {
    			$results[] = $stmt->fetchAll(\PDO::FETCH_OBJ);
    		} catch (\Exception $ex) {
    
    		}
    	} while ($stmt->nextRowset());
    	//echo "<pre>";print_r($results);
    	$results =  (!empty($results) ? array_filter($results) : [] );
    	//echo "<pre>";print_r($results);
    	//if (1 === count($results)) return $results[0];
    	return $results;
    }
    
    public function printLastQuery(){
    	echo BaseModel::last_query();
    }
    
    public function logLastQuery(){
    	return BaseModel::last_query();
    }
    
    public function multipleSearch( $fieldData , $columnName , $condition = 'OR'){
    	$searchRegion = explode("," , $fieldData );
    	$customWhere = ' ( ';
    	foreach($searchRegion as $region){
    		$customWhere.= "find_in_set(  '".$region."' , ".$columnName." ) ".$condition." ";
    	}
    	$customWhere = rtrim($customWhere , $condition.' ');
    	$customWhere .= ' ) ';
    	return $customWhere;
    }
    public function updateStatusMaster( $request , $tableName , $moduleName ){
    	 
    	$recordId = (!empty($request->record_id) ? (int)Wild_tiger::decode($request->record_id) : 0  );
    	 
    	$currentStatus = trim($request->current_status);
    	
    	$updateData = [];
    	if( strtolower( $currentStatus ) ==  strtolower ( config('constants.ACTIVE_STATUS') ) ){
    		$updateStatus = trans('messages.inactive');
    		$updateData['t_is_active']  = 0;
    	} else if( strtolower( $currentStatus ) ==  strtolower ( config('constants.INACTIVE_STATUS') ) ){
    		$updateStatus = trans('messages.active');
    		$updateData['t_is_active']  = 1;
    	}
    	 
    	$updatedmodule =  $moduleName;
    	 
    	if(!empty($updateData)){
    		 
    		$result = $this->BaseModel->updateTableData(  $tableName , $updateData , [ 'i_id' => $recordId ]);
    
    		if( $result != false ){
    			$message = trans ( 'messages.success-status-update', [ 'module' => $updatedmodule ] );
    			$this->ajaxResponse( 1 , $message , [ 'update_status'  =>  ( $updateStatus ) ] );
    		}
    		 
    	}
    	$message = trans ( 'messages.error-status-update', [ 'module' => $updatedmodule ] );
    	$this->ajaxResponse( 101 , $message );
    	 
    }
    public function convertFiletrRequest($request , $modulePerPage = 10 ){
    	//log_message('debug', print_r($request,true));
    	$draw = (!empty($request->post('draw')) ? $request->post('draw') : config('constants.DEFAULT_PAGE_INDEX') );
    
    	$offset = (!empty($request->post('start')) ? $request->post('start') : 0 );
    
    	$limit = (!empty($request->post('length')) ? $request->post('length') : $this->perPageRecord );// Rows display per page
    
    	$columnName = $columnIndex =  $columnSortOrder = '';
    
    	$columnIndex = (!empty($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : '' )  ; // Column index
    	$columnName = (!empty($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : '' );// Column name
    	$columnSortOrder = (!empty($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : '' )  ;// asc or desc
    
    	$searchValue = (!empty($_POST['search']['value']) ? trim($_POST['search']['value']) : '' );
    
    
    	$fieldData = [];
    	$fieldData['draw'] = $draw;
    	$fieldData['offset'] = $offset;
    	$fieldData['limit'] = $limit;
    	$fieldData['tableSearch'] = $searchValue;
    	$fieldData['columnSortOrder'] = $columnSortOrder;
    	$fieldData['sortColumnName'] = $columnName;
    
    	return $fieldData;
    
    }
    public function generateSpreadsheet( $exportInfo , $breakColumnArray = []){
    	require_once 'vendor/autoload.php';
    
    	$recordDetails = (!empty($exportInfo['record_detail']) ? $exportInfo['record_detail'] : [] );
    
    	$objPHPExcel = new Spreadsheet ();
    	$objPHPExcel->setActiveSheetIndex ( 0 );
    	if(!empty($exportInfo['title'])){
    		$objPHPExcel->getActiveSheet()->setTitle($exportInfo['title']);
    	}
    
    	$rowCount = 1;
    
    	$excelRows = Wild_tiger::DefaultExcelRow();
    	$getHeaderData = array_keys($recordDetails[0]);
    
    	$headercolumnWithKey = array_keys($recordDetails[0]);
    
    	foreach ( $getHeaderData as $key => $header ) {
    
    		if( (!empty($breakColumnArray))  && (in_array($header,$breakColumnArray)) ){
    			$columnValue = $excelRows[ $key ];
    			$objPHPExcel->getActiveSheet()->getStyle( $columnValue .'2:'. $columnValue .'256')->getAlignment()->setWrapText(true);
    		}
    
    
    		$objPHPExcel->getActiveSheet ()->SetCellValue ( $excelRows [$key] . $rowCount,  strtoupper(Wild_tiger::enumText( $header )) );
    	}
    	$rowCount++;
    
    	foreach($recordDetails as $k => $v)
    	{
    		$col = 1;
    		foreach ($headercolumnWithKey as $field)
    		{
    			$value = $v[$field];
    			//$value = ( is_float( $value ) != false ?  twoDigitAmount($value)  : $value ) ;
    			$value = str_replace('\n', "\n", $value);
    			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowCount,  $value );
    
    			$col++;
    
    		}
    		$rowCount++;
    		 
    	}
    
    	foreach($excelRows as $columnID) {
    		$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    	}
    
    	$objPHPExcel->getActiveSheet()->getStyle("1")->getFont()->setBold(true);
    	//$objPHPExcel->getActiveSheet()->getStyle("2")->getFont()->setBold(true);
    
    
    
    	$style = array(
    			'alignment' => array(
    					//'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    			)
    	);
    
    	$objPHPExcel->getActiveSheet()->getStyle("2")->applyFromArray($style);
    	$objPHPExcel->getActiveSheet()->getStyle("1")->applyFromArray($style);
    	$objPHPExcel->getDefaultStyle()->applyFromArray($style);
    
    	$writer = IOFactory::createWriter($objPHPExcel, 'Xls');
    
    
    	ob_start ();
    	//$objWriter->save ( "php://output" );
    	$writer->save('php://output');
    	$xlsData = ob_get_contents ();
    	ob_end_clean ();
    
    	return $xlsData;
    }
    
    
    public function uploadExcelFile( $request , $fieldName ){
    
    	$uploadedImagePath = '';
    
    	if($request->hasFile($fieldName)) {
    		// Get filename with extension
    		$filenameWithExt = $request->file($fieldName)->getClientOriginalName();
    		// Get just filename
    		$filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
    		// Get just ext
    		$extension = $request->file($fieldName)->getClientOriginalExtension();
    		//Filename to store
    		$fileNameToStore = $filename.'_'.time().'.'.$extension;
    		// Upload Image
    		$uploadedImagePath = $request->file($fieldName)->storeAs( config('constants.UPLOAD_FOLDER') . $fieldName, $fileNameToStore);
    		 
    	}
    
    	return $uploadedImagePath;
    }
    
    
    
    public function getAttendanceInfo( $month , $year , $employeeId ){
    	
    	$attendanceData = $holidayWhere = $presentWhere = [];
    	$attendanceData['month'] = $month;
    	$attendanceData['employee_id'] = $employeeId;
    	$attendanceData['year'] = $year;
    	
    	//employee basic info
    	$employeeBasicInfo = EmployeeModel::where('i_id' ,  $employeeId )->first();
    	$employeeJoiningDate = ( isset($employeeBasicInfo->dt_joining_date) ? $employeeBasicInfo->dt_joining_date : null );
    	$employeeReleaseDate = ( isset($employeeBasicInfo->dt_release_date) ? $employeeBasicInfo->dt_release_date : null );
    	
    	
    	//var_dump($month);echo "<br><br>";
    	//var_dump($year);echo "<br><br>";
    	//month all dates
    	$monthAllDates = Wild_tiger::getAllDateOfMonth($month , $year );
    	$monthAllDates = Wild_tiger::getAllDateOfSalaryMonth($month , $year );
    	
    	//echo "<pre>";print_r($monthAllDates);
    	
    	//Log::info('month all days');
    	//Log::info(print_r($monthAllDates,true));
    
    	//echo "<pre> monthAllDates";print_r($monthAllDates);
    	//var_dump($month);
    	//var_dump($year);
    	$attendanceStartDate = attendanceStartDate( $month , $year);
    	$attendanceEndDate = attendanceEndDate( $month , $year);
    	//var_dump($attendanceStartDate);
    	//var_dump($attendanceEndDate);die;
    	//echo "<pre>";print_r($monthAllDates);die;
    	$this->myAttendanceModule = new MyAttendanceModel();
    	
    	//get week off
    	$getEmployeeWeekOffDates = $this->getEmployeeMonthlyWeekOff( ['employeeId' => $employeeId , 'month' => $year.'-'.$month.'-01' , 'attendanceView' => true ] );
    	$monthAllWeekOfDates = ( isset($getEmployeeWeekOffDates['weekOffDates']) ? $getEmployeeWeekOffDates['weekOffDates'] : [] );
    	//Log::info('week off days');
    	//Log::info(print_r($monthAllWeekOfDates,true));
    	//echo "<pre>";print_r($monthAllWeekOfDates);
    	
    	//get all holidays
    	$monthAllHolidayDates = $this->getAllHoliDayDetails($monthAllDates);
    	//Log::info('calendar holidays');
    	//Log::info(print_r($monthAllHolidayDates,true));
    	//echo "<pre>";print_r($monthAllHolidayDates);
    	
    	//get employee adjustment
    	$employeeAdjustmentWhere = [];
    	$employeeAdjustmentWhere['t_is_deleted'] = 0;
    	$employeeAdjustmentWhere['i_employee_id'] = $employeeId;
    	$employeeAdjustmentWhere['e_record_type'] = config('constants.ADJUSTMENT_TIME_OFF');
    	$customEmployeeAdjustment = "( (  dt_time_off_date >= '".$attendanceStartDate."' ) or (  dt_time_off_date <= '".$attendanceEndDate."' ) )";
    	$getEmployeeAdjustmentDetails = TimeOff::where($employeeAdjustmentWhere)->whereRaw($customEmployeeAdjustment)->get();
    	$employeeAdjustmentDates = (!empty($getEmployeeAdjustmentDetails) ? array_column(objectToArray($getEmployeeAdjustmentDetails),'dt_time_off_date') : [] );
    	//Log::info('adjustment dates');
    	//Log::info(print_r($employeeAdjustmentDates,true));
    	//echo "<pre>";print_r($employeeAdjustmentDates);
    	
    	$presentWhere['attendance_date'] = $monthAllDates;
    	$presentWhere['employee_id'] = $employeeId;
    	
    	$allPermissionId = config('permission_constants.ALL_EMPLOYEE_LIST');
    	if( session()->has('user_permission') && ( in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    		$presentWhere['show_all'] = true;
    	}
    	
    	$getPresentDates = $this->myAttendanceModule->getRecordDetails($presentWhere);
    	
    	//echo "<pre>getPresentDates";print_r($getPresentDates);
    	
    	$allPresentDates = [];
    	$systemHalfLeaveDates = [];
    	if(!empty($getPresentDates)){
    		foreach($getPresentDates as $getPresentDate){
    			if($getPresentDate->e_status == config('constants.PRESENT_STATUS') ){
    				$allPresentDates[] = $getPresentDate->dt_date;
    			} else if($getPresentDate->e_status == config('constants.HALF_LEAVE_STATUS') ){
    				$systemHalfLeaveDates[] = $getPresentDate->dt_date;
    			}
    		}
    	}
    	//echo "<pre>allPresentDates";print_r($allPresentDates);
    	//Log::info('present dates');
    	//Log::info(print_r($allPresentDates,true));
    	
    	//Log::info('system half dates');
    	//Log::info(print_r($systemHalfLeaveDates,true));
    	
    	
    	$allAppliedLeaveDates = [];
    	$allAppliedHalfLeaveDates = [];
    	
    	$suspendWhere = [];
    	$suspendWhere['startDate'] = $attendanceStartDate;
    	$suspendWhere['endDate'] = $attendanceEndDate;
    	$suspendWhere['monthAllDates'] = $monthAllDates;
    	$suspendWhere['employeeId'] = [ $employeeId ];
    	//echo "<pre>";print_r($suspendWhere);
    	$allSuspendDates = $this->getAllSuspendDateWiseRecords($suspendWhere);
    	//echo "<pre>";print_r($allSuspendDates);die;
    	$allSuspendDates = (isset($allSuspendDates[$employeeId]) ? array_unique($allSuspendDates[$employeeId]) : [] );
    	//echo "<pre>suspend dates";print_r($allSuspendDates);die;
    	//Log::info('suspend dates');
    	//Log::info(print_r($allSuspendDates,true));
    	//echo "<pre>";print_r($allSuspendDates);
    	
    	//get all leaves
    	$employeeLeaveWhere = [];
    	$employeeLeaveWhere['startDate'] = $attendanceStartDate;
    	$employeeLeaveWhere['endDate'] = $attendanceEndDate;
    	$employeeLeaveWhere['employeeId'] = [ $employeeId ] ;
    	$employeeLeaveWhere['monthAllDates'] = $monthAllDates;
    	$employeeLeaveDetails = $this->employeeLeaveInfo($employeeLeaveWhere);
    	
    	//echo "<pre>";print_r($employeeLeaveDetails);die;
    	
    	
    	$paidLeaveDates = ( isset($employeeLeaveDetails['paidLeaveDates'][$employeeId]) ? $employeeLeaveDetails['paidLeaveDates'][$employeeId] : [] );
    	$paidHalfLeaveDates = ( isset($employeeLeaveDetails['paidHalfLeaveDates'][$employeeId]) ? $employeeLeaveDetails['paidHalfLeaveDates'][$employeeId] : [] );
    	$unPaidLeaveDates = ( isset($employeeLeaveDetails['unPaidLeaveDates'][$employeeId]) ? $employeeLeaveDetails['unPaidLeaveDates'][$employeeId] : [] );
    	$unPaidHalfLeaveDates = ( isset($employeeLeaveDetails['unPaidHalfLeaveDates'][$employeeId]) ? $employeeLeaveDetails['unPaidHalfLeaveDates'][$employeeId] : [] );
    	$paidLeaveCount = ( isset($employeeLeaveDetails['paidLeaveCount'][$employeeId]) ? $employeeLeaveDetails['paidLeaveCount'][$employeeId] : [] );
    	$unPaidLeaveCount = ( isset($employeeLeaveDetails['unPaidLeaveCount'][$employeeId]) ? $employeeLeaveDetails['unPaidLeaveCount'][$employeeId] : [] );
    	$allAppliedLeaveDates = ( isset($employeeLeaveDetails['allAppliedLeaveDates'][$employeeId]) ? $employeeLeaveDetails['allAppliedLeaveDates'][$employeeId] : [] );
    	
    	//echo "<pre> paidLeaveDates";print_r($paidLeaveDates);
    	//echo "<pre> paidHalfLeaveDates";print_r($paidHalfLeaveDates);
    	//echo "<pre>";print_r($unPaidLeaveDates);
    	//echo "<pre>";print_r($unPaidHalfLeaveDates);
    	//echo "<pre>";print_r($paidLeaveCount);
    	//echo "<pre>";print_r($unPaidLeaveCount);
    	//echo "<pre>";print_r($allAppliedLeaveDates);
    	//die;
    	
    	//Log::info('applied dates');
    	//Log::info(print_r($allAppliedLeaveDates,true));
    	//echo "<pre>";print_r($allSuspendDates);
    	
    	//echo "<pre>";print_r(array_column(objectToArray($getPresentDates), 'dt_date'));die;
    	//echo "<pre>";print_r($monthAllWeekOfDates);
    	$presentDates = $absentDates = $halfLeaveDates = $suspendDates = $adjustmentDates = $weekOffDates = $holidayDates =  [];
    	$calendarViewUnpaidHalfLeaveDates = [];
    	
    	$approvedLeaveDates = [];
    	$approvedHalfLeaveDates = [];
    	
    	$absentDayCount = 0;
    	$presentDayCount = 0;
    	$onlyPresentCount = 0;
    	if(!empty($monthAllDates)){
    		foreach($monthAllDates as $monthAllDate){
    			$getMonthDate = $monthAllDate;
    			
    			if((!empty($employeeJoiningDate)) && (strtotime($employeeJoiningDate) > strtotime($monthAllDate))){
    				continue;
    			}
    			
    			if((!empty($employeeReleaseDate)) && (strtotime($employeeReleaseDate) <= strtotime($monthAllDate))){
    				continue;
    			}
    			
    			if(in_array($getMonthDate,$employeeAdjustmentDates)){
    				$adjustmentDates[] = $getMonthDate;
    			}
    			 
    			if(in_array($getMonthDate,$allPresentDates)){
    				$presentDayCount +=  config('constants.FULL_LEAVE_VALUE');
    				$onlyPresentCount += config('constants.FULL_LEAVE_VALUE');
    				$presentDates[] = $getMonthDate;
    				continue;
    			}
    			 
    			if( (in_array($getMonthDate,$systemHalfLeaveDates) ) ){
    				$presentDayCount +=  config('constants.HALF_LEAVE_VALUE');
    				$halfLeaveDates[] = $getMonthDate;
    				
    				if( !in_array($monthAllDate,$monthAllWeekOfDates)  && !in_array($monthAllDate,$monthAllHolidayDates) ){
    					if( in_array($monthAllDate,$paidHalfLeaveDates) || in_array($monthAllDate,$unPaidHalfLeaveDates) ){
    						if( (in_array($getMonthDate,$paidHalfLeaveDates) ) ){
    							$presentDayCount +=  config('constants.HALF_LEAVE_VALUE');
    							$approvedHalfLeaveDates[] = $getMonthDate;
    							$absentDayCount +=  config('constants.HALF_LEAVE_VALUE');
    						}
    						if( (in_array($getMonthDate,$unPaidHalfLeaveDates) ) ){
    							$calendarViewUnpaidHalfLeaveDates[] = $getMonthDate;
    							$absentDayCount +=  config('constants.HALF_LEAVE_VALUE');
    						}
    					} else {
    						$calendarViewUnpaidHalfLeaveDates[] = $getMonthDate;
    					}
    				}
    				
    				
    				
    				continue;
    			}
    			
    			
    			if( ( !in_array($getMonthDate,$monthAllWeekOfDates) )  &&  ( !in_array($getMonthDate,$monthAllHolidayDates) ) ){
    				
    				if( (in_array($getMonthDate,$paidLeaveDates) ) ){
    					$presentDayCount +=  config('constants.FULL_LEAVE_VALUE');
    					$approvedLeaveDates[] = $getMonthDate;
    					$absentDayCount +=  config('constants.FULL_LEAVE_VALUE');
    					continue;
    				}
    				
    				if( (in_array($getMonthDate,$unPaidLeaveDates) ) ){
    					$absentDates[] = $getMonthDate;
    					$absentDayCount +=  config('constants.FULL_LEAVE_VALUE');
    					continue;
    				}
    				
    				if(in_array($getMonthDate,$allSuspendDates)){
    					//$suspendDates[] = $getMonthDate;
    					
    					
    					if( in_array($getMonthDate,$paidHalfLeaveDates) || in_array($getMonthDate,$unPaidHalfLeaveDates) ){
    						if( (in_array($getMonthDate,$paidHalfLeaveDates) ) ){
    							$presentDayCount +=  config('constants.HALF_LEAVE_VALUE');
    							$approvedHalfLeaveDates[] = $getMonthDate;
    							$calendarViewUnpaidHalfLeaveDates[] = $getMonthDate;
    							$absentDayCount +=  config('constants.HALF_LEAVE_VALUE');
    						}
    						if( (in_array($getMonthDate,$unPaidHalfLeaveDates) ) ){
    							$calendarViewUnpaidHalfLeaveDates[] = $getMonthDate;
    							$absentDayCount +=  config('constants.HALF_LEAVE_VALUE');
    						}
    					} else {
    						$suspendDates[] = $getMonthDate;
    						$absentDayCount +=  config('constants.FULL_LEAVE_VALUE');
    					}
    					continue;
    				}
    				
    				if( in_array($getMonthDate,$paidHalfLeaveDates) || in_array($getMonthDate,$unPaidHalfLeaveDates) ){
    					if( (in_array($getMonthDate,$paidHalfLeaveDates) ) ){
    						$presentDayCount +=  config('constants.HALF_LEAVE_VALUE');
    						$approvedHalfLeaveDates[] = $getMonthDate;
    						$calendarViewUnpaidHalfLeaveDates[] = $getMonthDate;
    						$absentDayCount +=  config('constants.HALF_LEAVE_VALUE');
    					}
    					if( (in_array($getMonthDate,$unPaidHalfLeaveDates) ) ){
    						
    						
    						if( (!in_array($getMonthDate,$systemHalfLeaveDates) )  && (!in_array($getMonthDate,$paidHalfLeaveDates) ) ){
    							$absentDates[] = $getMonthDate;
    							$absentDayCount +=  config('constants.FULL_LEAVE_VALUE');
    						} else {
    							$calendarViewUnpaidHalfLeaveDates[] = $getMonthDate;
    							$absentDayCount +=  config('constants.HALF_LEAVE_VALUE');
    						}
    						
    					}
    				} else {
    					$absentDates[] = $getMonthDate;
    					$absentDayCount +=  config('constants.FULL_LEAVE_VALUE');
    				}
    			} else {
    				if(in_array($getMonthDate,$monthAllWeekOfDates)){
    					$weekOffDates[] = $getMonthDate;
    					continue;
    				}
    				
    				if(in_array($getMonthDate,$monthAllHolidayDates)){
    					$holidayDates[] = $getMonthDate;
    					continue;
    				}
    			}
    		}
    	}
    	
    	//echo "<pre>";print_r($approvedLeaveDates);
    
    	$attendanceData['allDates'] = $monthAllDates;
    	$attendanceData['presentDates'] = $presentDates;
    	$attendanceData['absentDates'] = $absentDates;
    	$attendanceData['holidatDates'] = $holidayDates;
    	$attendanceData['weekOffDates'] = $weekOffDates;
    	$attendanceData['presentDayCount'] = $presentDayCount;
    	$attendanceData['onlyPresentCount'] = $onlyPresentCount;
    	$attendanceData['absentDayCount'] = $absentDayCount;
    	$attendanceData['approvedHalfLeaveDates'] = $approvedHalfLeaveDates;
    	$attendanceData['approvedLeaveDates'] = $approvedLeaveDates;
    	$attendanceData['suspendDates'] = $suspendDates;
    	$attendanceData['adjustmentDates'] = $adjustmentDates;
    	$attendanceData['halfLeaveDates'] = $halfLeaveDates;
    	$attendanceData['calendarViewUnpaidHalfLeaveDates'] = (!empty($calendarViewUnpaidHalfLeaveDates) ? array_unique($calendarViewUnpaidHalfLeaveDates) : [] );
    	
    	//var_dump($absentDayCount);die;
    	$absentCount = $absentDayCount;
    	$absentCount = ( $absentCount >= config('constants.SALARY_COUNT_DAYS') ? config('constants.SALARY_COUNT_DAYS') : $absentCount );
    	//var_dump($absentCount);
    	$paidLeaveCount = count($attendanceData['approvedLeaveDates']) + ( count($attendanceData['approvedHalfLeaveDates']) * 0.5 ) ;
    	//var_dump($paidLeaveCount);
    	$salaryPaidDaycount = ( config('constants.SALARY_COUNT_DAYS') - $absentCount + $paidLeaveCount );
    	$salaryPaidDaycount = ( $salaryPaidDaycount >= config('constants.SALARY_COUNT_DAYS') ? config('constants.SALARY_COUNT_DAYS') : $salaryPaidDaycount );
    	
    	$attendanceData['salaryPaidDayCount'] = $salaryPaidDaycount;
    	//var_dump($attendanceData['salaryPaidDayCount']);die;
    	$result = [];
    	$result['attendanceData'] = $attendanceData;
    
    	return $result;
    }
    
	public function convertExcelToCSV(Request $request , $fieldName  = 'upload_excel' ,  $folderName = 'upload_excel/'){
    	ini_set('memory_limit' , '-1');
    	$uploadedFilePath = "";
    	if( !empty( $_FILES[$fieldName]['name'] ) ){
    		$importFile = $this->uploadFile($request , $fieldName , $folderName ,  [ 'xls' , 'xlsx' ] );
    	}
    	
    	if( isset($importFile['status']) && ( $importFile['status'] != false ) ){
    		$uploadedFilePath = $importFile['filePath'];
    	}
    	
    	$result = [];
    	$result['excel_file_path'] = $uploadedFilePath;
    	$csvFilePath = null;
    	if( (!empty($uploadedFilePath)) ){
    		$uploadedFileExtension = strtolower( pathinfo($uploadedFilePath, PATHINFO_EXTENSION) );
    
    		if( $uploadedFileExtension == "csv" ){
    			$result['csv_file_path'] = config('constants.FILE_STORAGE_FILE_PATH') .  $uploadedFilePath;
    			return $result;
    		}
    
    
    		$fileType = ( $uploadedFileExtension == 'xlsx' ? 'Xlsx' : 'Xls' );
    
    		$uploadedFile = config('constants.FILE_STORAGE_FILE_PATH') . config('constants.UPLOAD_FOLDER') .  $uploadedFilePath;
    		
    		try{
    			//var_dump($xls_file);die;
    			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
    			$spreadsheet = $reader->load($uploadedFile);
    			 
    			$loadedSheetNames = $spreadsheet->getSheetNames();
    			//echo "<pre>";print_r($loadedSheetNames);die;
    			$writer = new Csv($spreadsheet);
    			 
    			foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {
    				$csvFilePath =  config('constants.FILE_STORAGE_FILE_PATH') . config('constants.UPLOAD_FOLDER') .  $loadedSheetName.'.csv';
    				$writer->setSheetIndex($sheetIndex);
    				$writer->save( $csvFilePath );
    			}
    		}catch(\Exception  $e){
    			var_dump($e->getMessage());die;
    		}
    
    
    
    	}
    	$result['csv_file_path'] = $csvFilePath;
    	return $result;
    }
    
    public function uploadCropFile( $request , $fieldName , $folderName ,  $allowedExtensions = [ 'jpg' , 'jpeg' , 'png' ] ){
    
    	$uploadedImagePath = '';
    
    	$response = [];
    	$response['status'] = false;
    	$response['message'] = trans('messages.please-upload-file-img');
    	
    	if($request->has($fieldName)) {
    		$fileInfo = (!empty($request->post($fieldName)) ? $request->post($fieldName) : null );
    		$type = null;
    		if (preg_match('/^data:image\/(\w+);base64,/', $fileInfo, $type)) {
    			
    			
    			$fileInfo = substr($fileInfo, strpos($fileInfo, ',') + 1);
    			$type = strtolower($type[1]); // jpg, png, gif
    				
    			if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
    				$response['message'] = 'invalid image type';
    				return $response;
    			}
    			
    			$fileInfo = str_replace( ' ', '+', $fileInfo );
    			
    			$sizeInBytes = (int) (strlen(rtrim($fileInfo, '=')) * 3 / 4);
    			$sizeInKb    = $sizeInBytes / 1024;
    			$sizeInMb    = $sizeInKb / 1024;
    			
    			if( $sizeInMb > config('constants.UPLOAD_FILE_LIMIT_SIZE') ){
    				$errorMessage = trans('messages.error-maximum-file-size');
    				$response['message'] = $errorMessage;
    				return $response;
    			}
    			
    			$fileInfo = base64_decode($fileInfo);
    			
    			if ($fileInfo === false) {
    				$response['message'] = 'crop image convert issue';
    				return $response;
    			}
    		} else {
    			$response['message'] = 'invalid file selection';
    			return $response;
    		}
    		
    		$filename = uniqid();
    		$filename = createSlug( strtolower($filename) );
    		// Get just ext
    		$extension = $type;
    
    		//Filename to store
    		$fileNameToStore = $filename.'_'.time().'.'.$extension;
    		
    		if (! file_exists(config('constants.FILE_STORAGE_PATH') .  config('constants.UPLOAD_FOLDER') .  $folderName)) {
    			mkdir(config('constants.FILE_STORAGE_PATH') .  config('constants.UPLOAD_FOLDER') .  $folderName, 0777, true);
    		}
    		
    		file_put_contents( config('constants.FILE_STORAGE_PATH') .  config('constants.UPLOAD_FOLDER') .  $folderName . $fileNameToStore , $fileInfo);
    		
    		$response['status'] = true;
    		$response['filePath'] = $folderName .  $fileNameToStore;
    
    		return $response;
    		 
    	}
    
    	return $response;
    }
    public function uploadMultipleFiles($request, $fieldName, $folderName, $finalSelectedImage = null, $removeFileImages = null)
    {
    	$uploadedImagePath = [];
    
    	$finalSelectedImage = (!empty($finalSelectedImage) ? explode(",", $finalSelectedImage) : []);
    
    	$removeFileImages = (!empty($removeFileImages) ? explode(",", $removeFileImages) : []);
   		
    	$specificFileName =  ( ( isset($request->specific_file_name) && (!empty($request->specific_file_name)) ) ? createSlug($request->specific_file_name) : null );
    	
    	$response['status'] = false; 
    	if ($request->hasFile($fieldName)) {
    
    		$uploadFileIndex = 1;
    		
    		foreach ($request->file($fieldName) as $key => $file) {
    
    			$filenameWithExt = $file->getClientOriginalName();
    			
    			if ((in_array($filenameWithExt, $finalSelectedImage)) && (!in_array($filenameWithExt, $removeFileImages))) {
    				
    				$filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
    
    				$extension = $file->getClientOriginalExtension();
    				
    				$uploadedFileSize = $file->getSize();
    				 
    				$sizeInMb = ( ( $uploadedFileSize > 0 ) ?  round( ( $uploadedFileSize / ( 1024 * 1024 ) ) , 0 ) :  0 );
    				 
    				if( $sizeInMb > config('constants.UPLOAD_FILE_LIMIT_SIZE') ){
    					$errorMessage = trans('messages.error-maximum-file-size');
    					$response['message'] = $errorMessage;
    					return $response;
    				}
    				
    
    				$fileNameToStore = (!empty($specificFileName) ? $specificFileName : $filename ) .   '-' . $uploadFileIndex . '-' . time()  . '.' . $extension;
    
    				$uploadedImagePathMain = $file->storeAs(Config::get('constants.UPLOAD_FOLDER') . $folderName, $fileNameToStore);
    
    				$uploadedImagePath[] = $folderName .  $fileNameToStore;
    				
    				$uploadFileIndex++;
    			}
    		}
    	}
    	
    	$response['status'] = true;
    	$response['uploadedImagePath'] = $uploadedImagePath;
    	
    	return $response;
    }
    
    
    
    public function getStatusWiseEmpDetails(Request $request){
    	if (!empty($request->input())){
    		$empStatus = (!empty($request->input('search_employment_status')) ? $request->input('search_employment_status') : '');
    		$allPermissionId = (!empty($request->input('all_permission_id')) ? $request->input('all_permission_id') : 0);
    		
    		$query = EmployeeModel::select('i_id' , 'v_employee_full_name' , 'v_employee_code')->where('t_is_deleted' , 0)->orderBy('v_employee_full_name' , 'asc');
    		
    		if (!empty($empStatus)){
    			if ($empStatus == config('constants.WORKING_EMPLOYMENT_STATUS')){
    				$query->whereIn('e_employment_status' , [config('constants.PROBATION_EMPLOYMENT_STATUS') , config('constants.CONFIRMED_EMPLOYMENT_STATUS') , config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')]);
    			}else {
    				$query->where('e_employment_status' , $empStatus);
    			}
    		}
    		
    		if( $request->has('on_hold_status') && ( $request->input('on_hold_status') == config('constants.SELECTION_YES') ) ){
    			$query->where('e_hold_salary_status' , config('constants.SELECTION_YES') );
    		}
    		
    		if(session()->get('role') == config('constants.ROLE_USER')){
    			if( session()->has('user_permission') && ( !in_array($allPermissionId, session()->get('user_permission')  ) ) ){
    				$employeeId = session()->get('user_employee_id');
    				//$query->whereRaw( "( i_id = '".$employeeId."' or i_leader_id = '".$employeeId."' ) " );
    				
    				$allChildEmployeeIds = $this->BaseModel->childEmployeeIds();
    				if(!empty($allChildEmployeeIds)){
    					$query->whereIn('i_id', $allChildEmployeeIds);
    				}
    				
    			}
    		}

    		$employeeDetails = $query->get();
    			
    		$html = '<option value="">'.trans("messages.select").'</option>';
    		foreach ($employeeDetails as $employeeDetail){
    			$html .= '<option value="'.Wild_tiger::encode($employeeDetail->i_id).'">'.(!empty($employeeDetail->v_employee_full_name) ? $employeeDetail->v_employee_full_name . '('.(!empty($employeeDetail->v_employee_code) ? $employeeDetail->v_employee_code : '') .')' : "").'</option>';
    		}

    		echo $html;die;
    	}
    }
    
    public function getEmployeeDropdownDetails( $where = [] ){
    	$this->employeeCrudModel = new EmployeeModel();
    	$where['order_by'] = [ 'v_employee_full_name'  => 'asc'];
    	$employeeDetails = $this->employeeCrudModel->getRecordDetails($where);
    	return $employeeDetails;
    }
}
