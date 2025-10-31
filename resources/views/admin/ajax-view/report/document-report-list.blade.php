@if(count($recordDetails) > 0 )
	@php $index= ($page_no - 1) * $perPageRecord ; @endphp
	@foreach($recordDetails as $recordDetail)
	@php $encodeRecordId = Wild_tiger::encode($recordDetail->i_id); 
		 $documentTypeId = Wild_tiger::encode($recordDetail->i_document_type_id) @endphp
		 <?php 
		 $visibleToEmployee = (!empty($recordDetail->documentType->e_visible_to_employee) ? $recordDetail->documentType->e_visible_to_employee :'');
		 $modifyEmployee = (!empty($recordDetail->documentType->e_modifiable_employee) ? $recordDetail->documentType->e_modifiable_employee :'');
		 ?>
 		<tr class="text-left">
 			<td class="text-center">{{ ++$index }}</td>
 			<td>{{ (!empty($recordDetail->documentType->documentFolderMaster->v_document_folder_name) ? $recordDetail->documentType->documentFolderMaster->v_document_folder_name :'') }}</td>
 			<td>
 				@if(!empty($recordDetail->employeeInfo->v_employee_full_name))
 					<a href="{{ route('employee-master.profile', (!empty($recordDetail->employeeInfo->i_id) ? Wild_tiger::encode($recordDetail->employeeInfo->i_id) :0  ) ) }}" target="_blank" title="{{ trans('messages.view-profile')}}">{{ (!empty($recordDetail->employeeInfo->v_employee_full_name) ? $recordDetail->employeeInfo->v_employee_full_name .(!empty($recordDetail->employeeInfo->v_employee_code) ? ' (' .$recordDetail->employeeInfo->v_employee_code .')' :'' ): '') }}</a> <br> {{ (!empty($recordDetail->employeeInfo->designationInfo->v_value) ? $recordDetail->employeeInfo->designationInfo->v_value :'') }}
 				@endif
 			</td>
 			<td>{{ (!empty($recordDetail->employeeInfo->teamInfo->v_value) ? $recordDetail->employeeInfo->teamInfo->v_value :'') }}<br> {{ (!empty($recordDetail->employeeInfo->dt_joining_date) ? convertDateFormat($recordDetail->employeeInfo->dt_joining_date ,'d.m.Y') :'') }}</td>
           	<td>{{ (!empty($recordDetail->documentType->v_document_type) ? $recordDetail->documentType->v_document_type :'') }}</td>
           	<td>
            	<div class="download-link-items d-flex justify-content-center">
              		<?php  if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_DOCUMENT_REPORT'), session()->get('user_permission')  ) ) ) ) || ( ( session()->get('role') == config('constants.ROLE_USER') ) && $visibleToEmployee == config('constants.SELECTION_YES') ) ){?>
              			<a href="javascript:void(0);" data-employee-id="{{ ( isset($recordDetail->i_employee_id) ? Wild_tiger::encode($recordDetail->i_employee_id) : '' ) }}" data-employee-name="{{ (!empty($recordDetail->employeeInfo->v_employee_full_name) ? $recordDetail->employeeInfo->v_employee_full_name :'') }}" data-document-folder-name="{{ (!empty($recordDetail->documentType->documentFolderMaster->v_document_folder_name) ? $recordDetail->documentType->documentFolderMaster->v_document_folder_name :'') }}" onclick="openViewModel(this)" data-document-type-name="{{ (!empty($recordDetail->documentType->v_document_type) ? $recordDetail->documentType->v_document_type :'') }}" data-document-type-record-id="{{ (!empty($documentTypeId) ? $documentTypeId :'')}}" class="btn btn btn-theme text-white border btn-sm  manage-doc-btn mr-2 d-sm-flex align-items-center" title="{{ trans('messages.view') }}">{{ trans("messages.view") }} </a>
              		<?php } ?>
                	<?php if( isset($recordDetail->documentType->t_is_active) && ( $recordDetail->documentType->t_is_active == 1 ) ) { ?>
                		<?php  if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) || ( ( session()->get('role') == config('constants.ROLE_USER') ) && $modifyEmployee == config('constants.SELECTION_YES') ) ){?>
                			<a href="javascript:void(0);"  data-employee-name="{{ (!empty($recordDetail->employeeInfo->v_employee_full_name) ? $recordDetail->employeeInfo->v_employee_full_name :'') }}"onclick="openUploadFileDocumentModel(this)" data-employee-id="{{ (!empty($recordDetail->employeeInfo->i_id) ? Wild_tiger::encode($recordDetail->employeeInfo->i_id) :0  ) }}" data-document-folder-name="{{ (!empty($recordDetail->documentType->documentFolderMaster->v_document_folder_name) ? $recordDetail->documentType->documentFolderMaster->v_document_folder_name :'') }}" data-type-name="{{ (!empty($recordDetail->documentType->v_document_type) ? $recordDetail->documentType->v_document_type :'') }}" data-document-type-record-id="{{ (!empty($documentTypeId) ? $documentTypeId :'')}}" class="btn btn btn-theme text-white border btn-sm d-sm-flex align-items-center manage-doc-btn upload-btn" title="{{ trans('messages.upload') }}">{{ trans("messages.upload") }} </a>
                		<?php } ?>
                	<?php } ?>
  				</div>
        	</td>
        </tr>
 	@endforeach
	@if(!empty($pagination))
 	    <input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
 		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
 		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
 	@endif
@else
 	<tr>
		<td colspan="6" class="text-center">@lang('messages.no-record-found')</td>
	</tr>
@endif
@include('admin/common-display-count')						
 													
							
		
                                
                               
                                
                                

                           				
							
