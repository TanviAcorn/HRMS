<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\UploadDailyAttendance;
use Validator;
use DB;

class UploadDailyAttendanceController extends MasterController
{
    //
	public function __construct(){
		parent::__construct();
		$this->moduleName = trans('messages.upload-daily-attendance-summary');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'upload-daily-attendance-summary/' ;
		$this->redirectUrl = config('constants.UPLOAD_DAILY_ATTENDANCE_SUMMARY_URL');
		$this->crudModel = new UploadDailyAttendance();
	}
	
	public function index(){
		$data = [];
		$data['pageTitle'] = trans('messages.uploaded-attendance-summary');
		$data['recordDetails'] = [];
		$data['allDates'] = [];
		return view( $this->folderName . 'upload-daily-attendance-summary')->with($data);
	}
	
	public function filter(Request $request){
		
		$startDate = date('Y-m-01');
		$endDate = date('Y-m-t');
		
		///echo "<pre>";print_r($request->all());
		
		if(!empty($request->post('search_from_month'))){
			$startDate = date('Y-m-01', strtotime($request->post('search_from_month')));
		}
		
		if(!empty($request->post('search_to_month'))){
			$endDate = date('Y-m-t', strtotime($request->post('search_to_month')));
		}
		
		$allDates =  getDatesFromRange( $startDate , $endDate );
		
		$whereData['search_summary'] = true;
		
		$whereData = [];
		$whereData['search_start_date'] = $startDate;
		$whereData['search_end_date'] = $endDate;
		$recordDeails = $this->crudModel->getRecordDetails( $whereData );
		
		$data['allDates'] = $allDates;
		$data['uploadSheetDates'] = (!empty($recordDeails) ? array_column(objectToArray($recordDeails),'dt_attendance_date') : [] );
		
		$data['page_no'] = $this->defaultPage;
		
		$data['perPageRecord'] = $this->perPageRecord;
		
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'upload-daily-attendance-summary/upload-daily-attendance-summary-list' )->with ( $data )->render();
		
		echo $html;die;
	}
	
	public function uploadDailyAttendance(Request $request){
		
		
		$formValidation = [];
		$formValidation['upload_daily_attendance'] = 'required';
		$formValidation['upload_daily_attendance_date'] = 'required';
			
		$validator = Validator::make ( $request->all (), $formValidation , [
				'upload_daily_attendance.required' => __ ( 'messages.required-upload-file' ),
				'upload_daily_attendance_date.required' => __ ( 'messages.please-enter-date' ),
		] );
			
		if ($validator->fails ()) {
			$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() :  trans ( 'messages.error-create', [ 'module' => $dataSheetRecordName ] ) ) );
		}
		
		if( !empty( $_FILES['upload_daily_attendance']['name'] ) ){
		
			//$successMessage = trans('messages.success-file-data-imported',['module'=> $dataSheetRecordName ]);
			//$errorMessages =  trans('messages.error-file-data-imported',['module'=> $dataSheetRecordName ]);
			$successMessage = trans('messages.success-sheet-import');
			$errorMessages = trans('messages.error-sheet-import');
			$result = false;
			$rowDetails = [];
		
			$convertExcelToCSV = $this->convertExcelToCSV($request);
			
			$csvFilePath = ( isset($convertExcelToCSV['csv_file_path']) ? $convertExcelToCSV['csv_file_path'] : null );
			$importFile = ( isset($convertExcelToCSV['excel_file_path']) ? $convertExcelToCSV['excel_file_path'] : null );
		
			$rowDetails = [];
			$excelKeys = [];
			if( (!empty($csvFilePath)) && file_exists($csvFilePath) ){
				$row = 1;
					
				if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
					while ( ( $data = fgetcsv($handle, 1000, ",")) !== FALSE ) {
						if( $row == 4 ){
							//echo "<pre>";print_r($data);
							$excelKeys = array_values($data);
							//echo "<pre>";print_r($excelKeys);die;
						} else {
							if(!empty($excelKeys)){
								$rowDetail = [];
								$rowDetail = array_combine($excelKeys, $data);
								if(!empty($rowDetail)){
									$rowDetails[] = $rowDetail;
								}
							}
						} 
						$row++;
					}
					fclose($handle);
				}
			}
			$finalExcelData = [];
			if(!empty($rowDetails)){
				foreach($rowDetails as $rowKey =>  $rowDetail){
					$rowExcelData = [];
					foreach( $rowDetail as $rowKey => $rowValue){
						$rowKey = strtolower( trim($rowKey) );
						$rowKey = str_replace(" ", "_", $rowKey);
						$rowValue = ( trim($rowValue) );
						switch($rowKey){
							case 'paycode':
								$rowExcelData['v_pay_code'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'department':
								$rowExcelData['v_department'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'name':
								$rowExcelData['v_name'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'shift':
								$rowExcelData['v_shift'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'start':
								$rowExcelData['v_start'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'in':
								$rowExcelData['v_in'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'out':
								$rowExcelData['v_out'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'hours_worked':
								$rowExcelData['v_hour_worked'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'status':
								$rowExcelData['v_status'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'early_arrival':
								$rowExcelData['v_early_arrival'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'shift_late':
								$rowExcelData['v_shift_late'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'shift_early':
								$rowExcelData['v_shift_early'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'ot':
								$rowExcelData['v_ot'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'ot_amount':
								$rowExcelData['v_ot_amount'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'overstay':
								$rowExcelData['v_over_stay'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'manual':
								$rowExcelData['v_manual'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'in_location':
								$rowExcelData['v_in_location'] = (!empty($rowValue) ? $rowValue : null);
								break;
							case 'out_location':
								$rowExcelData['v_out_location'] = (!empty($rowValue) ? $rowValue : null);
								break;
						}
					}
					$finalExcelData[] = $rowExcelData;
				}
			}
			$attedanceDate = (!empty($request->input('upload_daily_attendance_date')) ? dbDate($request->input('upload_daily_attendance_date')) : null );
			
			
			if(!empty($finalExcelData)){
				
				$result = false;
				DB::beginTransaction();
				
				try{
					foreach($finalExcelData as $finalExcel){
						$finalExcel['dt_attendance_date'] = $attedanceDate;
						$checkRecordWhere = [];
						$checkRecordWhere['dt_attendance_date'] = $attedanceDate;
						$checkRecordWhere['v_pay_code'] = ( isset($finalExcel['v_pay_code']) ? $finalExcel['v_pay_code'] : '' ) ;
						
						if(!empty($checkRecordWhere['v_pay_code'])){
							$checkRecordExist = UploadDailyAttendance::where($checkRecordWhere)->first();
								
							if(!empty($checkRecordExist)){
								$this->crudModel->updateTableData(config('constants.UPLOAD_DAILY_ATTENDANCE_TABLE'), $finalExcel , [ 'i_id' => $checkRecordExist->i_id ] );
							} else {
								$this->crudModel->insertTableData(config('constants.UPLOAD_DAILY_ATTENDANCE_TABLE'), $finalExcel);
							}
						}
					}
					$result = true;
				}catch(\Exception $e){
					$result = false;
					DB::rollback();
				}
				
				if($result != false){
					DB::commit();
					$this->ajaxResponse(1, $successMessage);
				}else {
					DB::rollback();
					$this->ajaxResponse(101, $errorMessages);
				}
				
				
			}
			$this->ajaxResponse(101, trans('messages.no-record-found-for-import'));
		}
		
	}
	
	public function attendanceData(){
		$data = [];
		$data['pageTitle'] = trans('messages.uploaded-attendance-data');
		
		$selectedDate = null;
		
		if( session()->has('selected_attendance_date') && (!empty(session()->get('selected_attendance_date'))) ){
			$selectedDate = session()->get('selected_attendance_date');
			$data['selectedDate'] = $selectedDate;
			$whereData['search_start_date'] = $selectedDate;
			$whereData['search_end_date'] = $selectedDate;
		}
		
		
		$page = $this->defaultPage;
		
		#store pagination data array
		$whereData = $paginationData = [];
		
		#get pagination data for first page
		if($page == $this->defaultPage ){
		
			$totalRecords = count($this->crudModel->getRecordDetails($whereData));
		
			$lastPage = ceil($totalRecords/$this->perPageRecord);
		
			$paginationData['current_page'] = $this->defaultPage;
		
			$paginationData['per_page'] = $this->perPageRecord;
		
			$paginationData ['last_page'] = $lastPage;
		
		}
		$whereData ['limit'] = $this->perPageRecord;
		
		$data['recordDetails'] = $this->crudModel->getRecordDetails( $whereData );
		
		$data['pagination'] = $paginationData;
			
		$data['page_no'] = $page;
			
		$data['perPageRecord'] = $this->perPageRecord;
		
		$data['totalRecordCount'] = $totalRecords;
		
		return view( $this->folderName . 'upload-daily-attendance')->with($data);
	}
	
	public function filterAttendanceData(Request $request){
	
		$whereData = $likeData = [];
		
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		
		if(!empty($request->post('search_from_date'))){
			$whereData['search_start_date'] = dbDate($request->post('search_from_date'));
		}
		if(!empty($request->post('search_to_date'))){
			$whereData['search_end_date'] = dbDate($request->post('search_to_date'));
		}
		
		
		$exportAction = (!empty($request->input('custom_export_action')) ? trim($request->input('custom_export_action')) : '');
		if ($exportAction == config('constants.EXCEL_EXPORT')) {
			$finalExportData = [];
				
			$getExportRecordDetails = $this->crudModel->getRecordDetails($whereData, $likeData);
		
			if (!empty($getExportRecordDetails)) {
				$excelIndex = 0;
				foreach ($getExportRecordDetails as $getExportRecordDetail) {
					
					$rowExcelData = [];
					$rowExcelData['sr_no'] = ++$excelIndex;
					$rowExcelData['pay_code'] = (!empty($getExportRecordDetail->v_pay_code) ? $getExportRecordDetail->v_pay_code :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$rowExcelData['type'] = (!empty($getExportRecordDetail->e_record_type) ? $getExportRecordDetail->e_record_type :'');
					$finalExportData[] = $rowExcelData;
				}
			}
		
			if (!empty($finalExportData)) {
		
				$fileName = trans('messages.export-module-file-name', ['moduleName' => $this->moduleName ]);
		
				$xlsData = $this->generateSpreadsheet(['record_detail' => $finalExportData, 'title' => trans('messages.uploaded-attendance-data')]);
				$response = ['status_code' => 1, 'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData), 'file_name' => $fileName];
			} else {
		
				$response = ['status_code' => 101, 'message' => trans('messages.no-record-found')];
			}
		
			return Response::json($response);
			die;
		}
		$paginationData = [];
		
		if ($page == $this->defaultPage) {
		
			$totalRecords = count($this->crudModel->getRecordDetails( $whereData , $likeData ));
			
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
		
		$data['recordDetails'] = $this->crudModel->getRecordDetails( $whereData, $likeData );
		
		if(isset($totalRecords)){
			$data ['totalRecordCount'] = $totalRecords;
		}
		$data['pagination'] = $paginationData;
		
		$data['page_no'] = $page;
		
		$data['perPageRecord'] = $this->perPageRecord;
		
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'upload-daily-attendance-summary/upload-attendance-list' )->with ( $data )->render();
		
		echo $html;die;
		
	}
	
	public function setAttedanceDate($filterDate = null){
		
		if(!empty($filterDate)){
			session()->flash('selected_attendance_date' , $filterDate );
		}
		
		return redirect( config('app.url') . 'upload-daily-attendance' );
		
	}
	
}
