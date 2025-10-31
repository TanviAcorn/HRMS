<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use App\Traits\MySoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class DocumentFolderModel extends BaseModel
{
	use MySoftDeletes;
	const CREATED_AT = 'dt_created_at';
	const UPDATED_AT = 'dt_updated_at';
	const DELETED_AT = 'dt_deleted_at';
	
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.DOCUMENT_FOLDER_MASTER_TABLE');
	}
	
	public function documentType(){
		return $this->hasMany(DocumentTypeModel::class,'i_document_folder_id');
	}
	
	public function getRecordDetails( $where = [] , $likeData = [] ){
	
		$query = DocumentFolderModel::with(['documentType' , 'documentType.employeeDocumentType']);
			
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			$query->where('i_id','=',$masterRecordId);
		}
		
		if( session()->get('role') == config('constants.ROLE_USER') ){
			if(  isset($where['show_all']) && ( $where['show_all'] == true ) ){
				unset($where['show_all']);
			} else {
				
				$query->whereHas('documentType' , function($query){
					$query->whereRaw("(e_visible_to_employee = '".config('constants.SELECTION_YES')."' or e_modifiable_employee = '".config('constants.SELECTION_YES')."')");
				});
			}
			
			if( isset($where['requested_user_id']) && ($where['requested_user_id'] > 0 ) ){
				$requestedUserId = $where['requested_user_id'];
				$query->with('documentType.employeeDocumentType' , function($query) use($requestedUserId) {
					$query->where('i_employee_id','=',$requestedUserId);
				});
			} else {
				$query->with('documentType.employeeDocumentType' , function($query){
					$query->where('i_employee_id','=',session()->get('user_employee_id'));
				});
			}
			
		} else {
			if( isset($where['requested_user_id']) && ($where['requested_user_id'] > 0 ) ){
				$requestedUserId = $where['requested_user_id'];
				$query->with('documentType.employeeDocumentType' , function($query) use($requestedUserId) {
					$query->where('i_employee_id','=',$requestedUserId);
				});
			}
		}
		
		
		if(isset($where['active_status'])){
			$activeStatus = $where['active_status'];
			$query->where('t_is_active',$activeStatus);
		}
		if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
	
			$searchString = ( $likeData['searchBy'] );
	
			$allLikeColumns = [ 'v_document_folder_name','v_document_folder_description' ];
	
			$query->where(function($q) use ($allLikeColumns,$searchString){
				foreach($allLikeColumns as $key => $allLikeColumn){
					$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
				}
			});
					
		}
		$query->orderBy('i_id', "DESC" ) ;
		if( isset($where['offset']) ){
			$query->skip($where ['offset']);
		}
		
		if( isset($where['limit']) ){
			$query->take($where['limit']);
		}	
		if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
			$data = $query->first();
		} else {
			$data = $query->get();
		}
			
		return $data;
	
	}
	
}
