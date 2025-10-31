<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\Notification;

class NotificationController extends MasterController
{
    //
	public function __construct(){
		parent::__construct();
		 
		$this->crudModel =  new Notification();
		$this->moduleName = trans('messages.notification');
		$this->perPageRecord = config('constants.PER_PAGE');
		$this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
		$this->tableName = config('constants.EMAIL_HISTORY_TABLE');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'notification/' ;
		$this->redirectUrl = config('constants.NOTIFICATION_URL');
	}
	
	public function index(){
		/* if(! in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] )){
			return redirect('access-denied');
		} */
		
		/* if(  session()->get('user_employee_id') == null ||  session()->get('user_employee_id') < 0  ){
			return redirect(config('constants.DASHBORD_MASTER_URL'));
		} */
		
		$data = $whereData = [];
		$data['pageTitle'] = trans('messages.notifications');
		$page = $this->defaultPage;
		 
		#store pagination data array
		$paginationData = [];
		
		$unReadNotificationCount = count($this->crudModel->getRecordDetails( [ 'read_status' => 0  ] ) );
		$totalRecords = 0;
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
		
		$data['unReadNotificationCount'] = $totalRecords;
		
		$data['userUnReadNotificationCount'] = $unReadNotificationCount;
		
		
		 
		return view( $this->folderName . 'notification')->with($data);
	}
	
	public function employeeReportfilter(Request $request){
		//variable defined
		$whereData = $likeData = [];
		 
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		 
		if(!empty($request->post('search_joining_to_date'))){
			$whereData['joining_to_date'] = ($request->post('search_joining_to_date'));
		}
		
		if(!empty($request->post('search_team_name'))){
			$whereData['team_record'] = (int)Wild_tiger::decode($request->post('search_team_name'));
		}
		
		if ($page == $this->defaultPage) {
			 
			$totalRecords = count($this->employeeCrudModel->getRecordDetails( $whereData , $likeData ));
			 
			 
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
		 
		$data['recordDetails'] = $this->employeeCrudModel->getRecordDetails( $whereData, $likeData );
		 
		if(isset($totalRecords)){
			$data ['totalRecordCount'] = $totalRecords;
		}
		$data['pagination'] = $paginationData;
		 
		$data['page_no'] = $page;
		 
		$data['perPageRecord'] = $this->perPageRecord;
		 
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'notification/notification-list' )->with ( $data )->render();
		 
		echo $html;die;
	}
	public function notificationFilter(Request $request){
		//variable defined
		$whereData = $likeData = [];
	
		$page = (! empty($request->post('page')) ? $request->post('page') : 1);
		
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
		
		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'notification/notification-list' )->with ( $data )->render();
	
		echo $html;die;
	}
}
