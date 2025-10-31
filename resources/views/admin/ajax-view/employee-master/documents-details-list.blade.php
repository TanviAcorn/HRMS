	<div class="filter-result-wrapper container-fluid px-0 manage-document">
       	<?php 
       	if (!empty($documentRecordDetails)){
       		foreach ($documentRecordDetails as $documentRecordDetail){
       		?>
       		<div class="card shadow-none card-body">
       			 <div class="card shadow-none border">
	               <div class="card-header">
	                   <div class="d-flex align-items-center">
	                       <h5 class="mb-lg-0 mr-3 mb-0 pb-2 font-weight-semi-bold pt-1">{{ (!empty($documentRecordDetail->v_document_folder_name) ? $documentRecordDetail->v_document_folder_name :'')}}</h5>
	                       <div class="ml-auto d-flex align-items-center slide-icon">
	                           <a title="{{trans('messages.folder')}}" href="javascript:void(0);" class="btn btn-sm btn-theme"><i class="fa fa-regular fa-folder-open"></i></a>
	                       </div>
	                   </div>
	               </div>
	               <div class="card-body">
	                   <div class="table-responsive">
	                       <table class="table table-hover table-bordered table-sm mb-0">
	                           <thead>
	                               <tr>
	                                   <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
	                                   <th class="text-left">{{ trans("messages.documents") }}</th>
	                                   <th class="actions-col">{{ trans("messages.actions") }}</th>
	                               </tr>
	                           </thead>
	                           <tbody>
	                           	<?php 
	                           	if(isset($documentRecordDetail->documentType) && (!empty($documentRecordDetail->documentType)) && (count($documentRecordDetail->documentType) > 0 )){
	                           			foreach ($documentRecordDetail->documentType as $countKey => $documentRecordDetail){
	                           				$columIndex  = ( $countKey +  1 );
	                           				$encodeRecordId = Wild_tiger::encode($documentRecordDetail->i_id);
	                           				$multipleAllowedEmployee = (!empty($documentRecordDetail->e_multiple_allowed_employee) ? $documentRecordDetail->e_multiple_allowed_employee :'');
	                           				$visibleToEmployee = (!empty($documentRecordDetail->e_visible_to_employee) ? $documentRecordDetail->e_visible_to_employee :'');
	                           				$modifyEmployee = (!empty($documentRecordDetail->e_modifiable_employee) ? $documentRecordDetail->e_modifiable_employee :'');
	                           				$fileDataAttr = '';
	                           				$filteType = '';
	                           				//if( session()->get('role') == config('constants.ROLE_USER') || (session()->get('role') == config('constants.ROLE_ADMIN'))){ 
		                           				if ($modifyEmployee == config('constants.SELECTION_YES') || ($multipleAllowedEmployee == config('constants.SELECTION_YES'))){
		                           					if ($multipleAllowedEmployee == config('constants.SELECTION_YES')){
		                           						$fileDataAttr = 'multiple';
		                           						$filteType = $multipleAllowedEmployee;
		                           					}else {
		                           						$filteType = $modifyEmployee;
		                           					}
		                           				}
	                           			   //}
	                           				?>
		                           			<tr>
			                                   <td class=" text-center" style="width:70px;min-width:70px;">{{ $columIndex }}</td>
			                                   <td class="text-left" style="width: auto;min-width:170px;">{{ (!empty($documentRecordDetail->v_document_type) ? $documentRecordDetail->v_document_type :'')}}</td>
			                                   <td style="width: 160px;min-width: 160px;">
			                                       <div class="download-link-items d-flex justify-content-center">
			                                        <?php  if( (( session()->get('role') == config('constants.ROLE_ADMIN') ) || ( ( session()->get('role') == config('constants.ROLE_USER') ) && $modifyEmployee == config('constants.SELECTION_YES') )) && checkPermission('add_documents_reports') != false ){?>
			                                  			<a href="javascript:void(0);" data-file-type="{{$fileDataAttr}}" class="btn btn btn-theme text-white border btn-sm d-sm-flex mr-2 align-items-center manage-doc-btn upload-btn" data-document-type-record-id="{{$encodeRecordId}}" data-record-document-file-type="{{$filteType}}" title="{{ trans('messages.upload') }}" onclick="openUploadFileDocumentModel(this)">{{ trans("messages.upload") }} </a>
			                                        <?php  }?> 
			                                         <?php  if( ( session()->get('role') == config('constants.ROLE_ADMIN') ) || ( ( session()->get('role') == config('constants.ROLE_USER') ) && $visibleToEmployee == config('constants.SELECTION_YES') ) ){?>
				                                        <a href="javascript:void(0);" class="btn btn btn-theme text-white border btn-sm  manage-doc-btn  d-sm-flex align-items-center" data-document-type-record-id="{{$encodeRecordId}}" title="{{ trans('messages.view') }}" onclick="openViewModel(this)">{{ trans("messages.view") }} </a>
				                                     <?php  } ?>
			                                       </div>
			                                   </td>
			                               </tr>
		                           		<?php 
	                           		}
	                           }  else { 
	                           		?>
	                               <tr>
	                                   <td colspan="3" class="text-center">{{ trans('messages.no-record-found')}}</td>
	                               </tr>
	                           		<?php 
	                           } 
	                           ?>
	                           </tbody>
	                       </table>
	                   </div>
	               </div>
	           </div>
		</div>
       	<?php 
  		}
 	} 
 	?>
 	</div>


   

   

   