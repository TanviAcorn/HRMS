<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\MySoftDeletes;
class DocumentTypeModel extends BaseModel
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
		$this->table = config('constants.DOCUMENT_TYPE_MASTER_TABLE');
	}
	
	public function documentFolderMaster(){
		return $this->belongsTo(DocumentFolderModel::class,'i_document_folder_id');
	}
	
	public function getRecordDetails( $where = [] , $likeData = [] , $additionalData = [] ){
		$query = DocumentTypeModel::with(['documentFolderMaster']);
		
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			$query->where('i_id','=',$masterRecordId);
		}
		
		if(isset($where['document_folder_id']) && (!empty($where['document_folder_id'])) ){
			$documentFolderId = $where['document_folder_id'];
			$query->where('i_document_folder_id',$documentFolderId);
		}
		if(isset($where['multiple_allowed_employee']) && (!empty($where['multiple_allowed_employee'])) ){
			$multipleAllowedEmployee = $where['multiple_allowed_employee'];
			$query->where('e_multiple_allowed_employee',$multipleAllowedEmployee);
		}
		if(isset($where['visible_to_employee']) && (!empty($where['visible_to_employee'])) ){
			$visibleToEmployee = $where['visible_to_employee'];
			$query->where('e_visible_to_employee',$visibleToEmployee);
		}
		if(isset($where['modifiable_employee']) && (!empty($where['modifiable_employee'])) ){
			$modifiableEmployee = $where['modifiable_employee'];
			$query->where('e_modifiable_employee',$modifiableEmployee);
		}
		if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
		
			$searchString = ( $likeData['searchBy'] );
		
			$allLikeColumns = [ 'v_document_type','v_document_description' ];
		
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
	
	
	public function employeeDocumentType(){
		return $this->hasMany(EmployeeDocumentModel::class,'i_document_type_id');
	}
	
}
