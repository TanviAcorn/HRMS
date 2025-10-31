	@if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) )
	<button type="button" title="{{ trans('messages.download-all-document') }}" class="btn btn-theme text-white mb-2 ml-auto pull-right all-document-download-button" onclick="addDownloadAllDocument(this);" data-record-id="{{ ( isset($empId) ? $empId : '' ) }}" ><i class="fa fa-download mr-sm-2 mr-0"></i><span class="d-sm-inline-block d-none">{{ trans('messages.download-all-document') }}</span></button>
	@endif
	<div class="filter-result-wrapper container-fluid px-0 manage-document">
       	<?php
       	$findOneDocument = false;
       	if (!empty($documentRecordDetails)){
       		foreach ($documentRecordDetails as $documentRecordDetail){
       			$documentFolderName = (!empty($documentRecordDetail->v_document_folder_name) ? $documentRecordDetail->v_document_folder_name :'');
       		?>
       		<div class="card shadow-none card-body">
       			 <div class="card shadow-none border">
	               <div class="card-header">
	                   <div class="d-flex align-items-center">
	                       <h5 class="mb-lg-0 mr-3 mb-0 pb-2 font-weight-semi-bold pt-1">{{ (!empty($documentFolderName) ? $documentFolderName :'')}}</h5>
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
	                                   <th class="text-left">{{ trans("messages.status") }}</th>
	                                   <th class="actions-col">{{ trans("messages.actions") }}</th>
	                               </tr>
	                           </thead>
	                           <tbody>
	                           	<?php 
	                           	if(isset($documentRecordDetail->documentType) && (!empty($documentRecordDetail->documentType)) && (count($documentRecordDetail->documentType) > 0 )){
	                           			$docIndex = 0;
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
	                           			   
		                           			$showRecord = true;
		                           			if( session()->get('role') == config('constants.ROLE_USER') ){
		                           				if(  $documentRecordDetail->e_visible_to_employee == config('constants.SELECTION_NO') ){
		                           					$showRecord = false;
		                           				}
		                           				if( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ){
		                           					$showRecord = true;
		                           				}
		                           			}
		                           			//var_dump($showRecord);echo "<br><br><br>";
		                           			$documentStatus = trans('messages.pending');
		                           			if( isset($documentRecordDetail->employeeDocumentType) && ( count( $documentRecordDetail->employeeDocumentType ) > 0 ) ) {
		                           				$documentStatus = trans('messages.completed');
		                           			}
	                           				if( $showRecord != false ){
	                           					
			                           			?>
			                           			<tr>
				                                   <td class=" text-center" style="width:50px;min-width:50px;">{{ ++$docIndex }}</td>
				                                   <td class="text-left" style="width: 700px;min-width:170px;max-width:700px">{{ (!empty($documentRecordDetail->v_document_type) ? $documentRecordDetail->v_document_type :'')}}</td>
				                                   <td style="width: 80px;min-width: 80px;">{{ $documentStatus }}</td>
				                                   <td style="width: 160px;min-width: 160px;">
				                                       <div class="download-link-items d-flex justify-content-center">
				                                        <?php if( $documentRecordDetail->t_is_active == 1 ) { ?>
					                                        <?php  if( ( ( ( session()->get('role') == config('constants.ROLE_ADMIN') ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) || ( ( session()->get('role') == config('constants.ROLE_USER') ) && $modifyEmployee == config('constants.SELECTION_YES') )) ){?>
					                                  			<?php //var_dump(( session()->get('role') == config('constants.ROLE_ADMIN') ));echo "<br><br>";?>
					                                  			<?php //var_dump(( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ));echo "<br><br>";?>
					                                  			<?php ///var_dump(( session()->get('role') == config('constants.ROLE_USER') ) && $modifyEmployee == config('constants.SELECTION_YES'));echo "<br><br>";?>
					                                  			<a href="javascript:void(0);"  data-document-folder-name="{{ (!empty($documentFolderName) ? $documentFolderName :'')}}" data-type-name="{{ (!empty($documentRecordDetail->v_document_type) ? $documentRecordDetail->v_document_type :'')}}" data-file-type="{{$fileDataAttr}}" class="btn btn btn-theme text-white border btn-sm d-sm-flex mr-2 align-items-center manage-doc-btn upload-btn" data-document-type-record-id="{{$encodeRecordId}}" data-record-document-file-type="{{$filteType}}" title="{{ trans('messages.upload') }}" data-employee-id="{{ (!empty($employeeId) ? Wild_tiger::encode($employeeId) : 0) }}" onclick="openUploadFileDocumentModel(this)">{{ trans("messages.upload") }} </a>
					                                        <?php  } ?> 
				                                        <?php } ?>
				                                         <?php  if( ( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ) || ( ( session()->get('role') == config('constants.ROLE_USER') ) && $visibleToEmployee == config('constants.SELECTION_YES') ) ){?>
					                                        <?php if( isset($documentRecordDetail->employeeDocumentType) && ( count( $documentRecordDetail->employeeDocumentType ) > 0 ) ) { ?>
					                                        	<?php $findOneDocument = true ;?>
					                                        	<a href="javascript:void(0);" data-document-type-name="{{ (!empty($documentRecordDetail->v_document_type) ? $documentRecordDetail->v_document_type :'')}}" data-document-folder-name="{{ (!empty($documentFolderName) ? $documentFolderName :'')}}" class="btn btn btn-theme text-white border btn-sm  manage-doc-btn  d-sm-flex align-items-center" data-document-type-record-id="{{$encodeRecordId}}" title="{{ trans('messages.view') }}" onclick="openViewModel(this)">{{ trans("messages.view") }} </a>
					                                        <?php } ?>
					                                     <?php  } ?>
				                                       </div>
				                                   </td>
				                               </tr>
				                            	<?php
	                           				}
	                           		}
	                           }  else { 
	                           		?>
	                               <tr>
	                                   <td colspan="4" class="text-center">{{ trans('messages.no-record-found')}}</td>
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
 	<script>

 	<?php if($findOneDocument != true ) { ?>
		$(".all-document-download-button").hide();
 	<?php } ?>
 	
	function addDownloadAllDocument(thisitem){
		var employee_id = $.trim($(thisitem).attr('data-record-id'));
		if( employee_id != "" && employee_id != null ){
			$.ajax({
    	 		type: "POST",
    	 		url: '{{ config("app.url") }}' + 'download-employee-document',
    	 		dataType:'json',
    	 		data: {
    	 			"_token": "{{ csrf_token() }}",
    	 			'employee_id':employee_id,
    	 		},
    	 		beforeSend: function() {
    	 			//block ui
    	 			showLoader();
    	 		},
    	 		success: function(response) {
    	 	 		hideLoader();
    	 	 		if(response.status_code == 1 ){
    	 	 			var opResult = response;
			            var $a = $("<a>");
			            $a.attr("href", opResult.data.data);
			            $("body").append($a);
			            $a.attr("download", response.data.file_name);
			            $a[0].click();
			            $a.remove();
    				} else {
    			    	alertifyMessage('error' , response.message);
    				}
    	 	 	},
    	 		error: function() {
    	 			hideLoader();
    	 		}
    	 	});
		}
	}
 	</script>