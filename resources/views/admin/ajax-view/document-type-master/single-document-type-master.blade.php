<?php
	$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
?>
	<td class="text-center sr-col">{{ $rowIndex}}</td>
	<td>{{ (!empty($recordDetail->documentFolderMaster->v_document_folder_name) ? $recordDetail->documentFolderMaster->v_document_folder_name :'') }}</td>
	<td>{{ (!empty($recordDetail->v_document_type) ? $recordDetail->v_document_type :'') }}</td>
	<td class="text-left">{{ (!empty($recordDetail->v_document_description) ? $recordDetail->v_document_description :'') }}</td>
	<td>{{ (!empty($recordDetail->e_multiple_allowed_employee) ? $recordDetail->e_multiple_allowed_employee :'') }}</td>
	<td>{{ (!empty($recordDetail->e_visible_to_employee) ? $recordDetail->e_visible_to_employee :'') }}</td>
 	<td>{{ (!empty($recordDetail->e_modifiable_employee) ? $recordDetail->e_modifiable_employee :'') }}</td>
	<td class="text-center status-update">{{ ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans('messages.active') : trans('messages.inactive') )}}</td>
	@if((checkPermission('edit_document_type') != false) || (checkPermission('delete_document_type') != false)) 
	<td class="actions-button">
		@if(checkPermission('edit_document_type') != false)
		<button title="{{ trans('messages.edit') }}" class="btn btn-sm mb-1 btn-edit btn-color-text" data-record-id='{{ $encodeRecordId }}' onclick='editDocumentTypeModel(this)'><i class="fas fa-pencil-alt"></i></button>
		@endif
		@if(checkPermission('delete_document_type') != false)
 		<button title="{{ trans('messages.delete') }}" data-record-id="{{ $encodeRecordId }}" data-module-name="document-type-master" onclick="deleteRecord(this);" class="btn btn-sm mb-1 btn-delete btn-color-text"><i class="fa fa-trash"></i></button>
		@endif
		@if(checkPermission('edit_document_type') != false)
 		<button title="{{ (int) ($recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') }}" onclick='updateMasterStatusRecord(this,"document-type-master")' data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-active btn-color-text"><i class="{{ (int) ($recordDetail->t_is_active == 1) ? 'fa fa-eye-slash' :'fa fa-eye'}}"></i></button>
		@endif
	</td>
	@endif