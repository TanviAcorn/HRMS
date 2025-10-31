<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\DocumentFolderModel;
use App\Helpers\Twt\Wild_tiger;
use App\EmployeeModel;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
class MyDocumentMasterController extends MasterController {
   
	public function __construct(){
	parent::__construct();
		$this->documentTypeModel =  new DocumentFolderModel();
		$this->folderName = config('constants.ADMIN_FOLDER'). 'my-documents/' ;
	}
	public function index( Request $request ){
		$ajaxRequest = false;
		if($request->ajax()){
			$ajaxRequest = true;
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : session()->get('user_employee_id') );
		}else {
			$employeeId = session()->get('user_employee_id');
		} 
		$data = [];
		$data['pageTitle'] = trans('messages.my-documents');
	
		$documentWhere = [];
		if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ){
			$documentWhere['show_all'] = true;
		}
		$documentWhere['requested_user_id'] = $employeeId;
		//echo "<pre>";print_r($documentWhere);
		$data['documentRecordDetails'] = $this->documentTypeModel->getRecordDetails( $documentWhere );
		//echo "<pre>";print_r($data['documentRecordDetails']);die;
		
		
		$data['employeeId'] = $employeeId;
		$data['empId'] = Wild_tiger::encode($employeeId);
		if( $ajaxRequest != false ){
			$html = view (  config('constants.AJAX_VIEW_FOLDER') . 'employee-master/document-info' )->with ( $data )->render();
			echo $html;die;
		}
		return view( $this->folderName . 'my-documents')->with($data);
	}
	
	public function downloadEmployeeDocument(Request $request){
		if(!empty($request->input())){
			
			$employeeId = (!empty($request->input('employee_id')) ? (int)Wild_tiger::decode($request->input('employee_id')) : 0 );
			
			if( $employeeId > 0 ){
				
				$documentWhere = [];
				if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ){
					$documentWhere['show_all'] = true;
				}
				$documentWhere['requested_user_id'] = $employeeId;
				$documentRecordDetails = $this->documentTypeModel->getRecordDetails( $documentWhere  );
				
				$allUploadedFilePaths = [];
				if (!empty($documentRecordDetails)){
					foreach ($documentRecordDetails as $documentRecordDetail){
						if(isset($documentRecordDetail->documentType) && (!empty($documentRecordDetail->documentType)) && (count($documentRecordDetail->documentType) > 0 )){
							foreach ($documentRecordDetail->documentType as $countKey => $documentRecord){
								if( isset($documentRecord->employeeDocumentType) && ( count( $documentRecord->employeeDocumentType ) > 0 ) ) {
									foreach($documentRecord->employeeDocumentType as $document){
										if (!empty($document) && file_exists(config('constants.FILE_STORAGE_PATH').config('constants.UPLOAD_FOLDER') . $document->v_document_file)) {
											$allUploadedFilePaths[] =  config('constants.FILE_STORAGE_PATH') .  config('constants.UPLOAD_FOLDER') .  $document->v_document_file;
										}
									}
								}
							}
						}
					}
				}
				
				//echo "<pre>";print_r($allUploadedFilePaths);
				
				$getEmployeeInfo = EmployeeModel::where('i_id' , $employeeId)->first();
				$employeeCode = ( ( (!empty($getEmployeeInfo) && isset($getEmployeeInfo->v_employee_code)) ) ? $getEmployeeInfo->v_employee_code : '' );
				$folderPath = config('constants.FILE_STORAGE_PATH') . config('constants.UPLOAD_FOLDER') . 'document/' . $employeeCode;
				$zipPath = config('constants.FILE_STORAGE_PATH') . config('constants.UPLOAD_FOLDER') . 'document/' . $employeeCode . '/' . $employeeCode.'.zip' ;
				
				$rootPath = $folderPath;
				//var_dump($rootPath);
				try{
					// Initialize archive object
					$zip = new \ZipArchive();
					$zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
					
					// Create recursive directory iterator
					/** @var SplFileInfo[] $files */
					$files = new RecursiveIteratorIterator(
							new RecursiveDirectoryIterator($rootPath),
							RecursiveIteratorIterator::LEAVES_ONLY
					);
					//echo "<pre>";print_r($files);die;
					foreach ($files as $name => $file)
					{
						// Skip directories (they would be added automatically)
						if (!$file->isDir())
						{
							// Get real and relative path for current file
							$filePath = $file->getRealPath();
							$relativePath = substr($filePath, strlen($rootPath) + 1);
							
							//echo "file = ".$filePath;echo "<br><br>";
							
							// Add current file to archive
							//if( (!empty($filePath)) && in_array($filePath,$allUploadedFilePaths) ){
								$zip->addFile($filePath, $relativePath);
							//}
							
							
							
						}
					}
					
					// Zip archive will be created only after closing object
					$zip->close();
					
					
					
				}catch(\Exception $e){
					//var_dump($e->getMessage());die;
				}
				//die("welcome");
				if(file_exists($zipPath)){
					
					$fileInfo = file_get_contents($zipPath);
					unlink($zipPath);
					$this->ajaxResponse(1, trans('messages.success') , [ 'data' => "data:application/zip;base64," . base64_encode ( $fileInfo ) , 'file_name' => $employeeCode . '.zip'   ] );
				} else {
					$this->ajaxResponse(101, trans('messages.system-error'));
				}
			}
			$this->ajaxResponse(101, trans('messages.system-error'));
			
		}
	}
	
}
