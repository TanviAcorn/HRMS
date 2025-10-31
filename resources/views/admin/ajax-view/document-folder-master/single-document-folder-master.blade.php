<?php
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
 ?>
	<td class="text-center sr-col">{{ $rowIndex}}</td>
    <td>{{ (!empty($recordDetail->v_document_folder_name) ? $recordDetail->v_document_folder_name :'') }}</td>
    <td class="text-left">{{ (!empty($recordDetail->v_document_folder_description) ? $recordDetail->v_document_folder_description :'') }}</td>
    <td class="text-center status-update">{{ ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans('messages.active') : trans('messages.inactive') )}}</td>
    @if((checkPermission('edit_document_folder') != false) || (checkPermission('delete_document_folder') != false))
    <td class="actions-button">
        @if(checkPermission('edit_document_folder') != false)
    	<button type="button" title='{{ trans("messages.edit") }}' class="btn btn-sm mb-1 btn-edit btn-color-text" onclick="editDocumentFolderModel(this)" data-record-id="{{ $encodeRecordId }}"><i class="fas fa-pencil-alt"></i></button>
        @endif
        @if(checkPermission('delete_document_folder') != false)
    	<button title="{{ trans('messages.delete') }}" data-record-id="{{ $encodeRecordId }}" data-module-name="document-folder-master" onclick="deleteRecord(this);" class="btn btn-sm mb-1 btn-delete btn-color-text"><i class="fa fa-trash"></i></button>
        @endif
        @if(checkPermission('edit_document_folder') != false)
    	<button title="{{ (int) ($recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') }}" onclick='updateMasterStatusRecord(this,"document-folder-master")' data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-active btn-color-text"><i class="{{ (int) ($recordDetail->t_is_active == 1) ? 'fa fa-eye-slash' :'fa fa-eye'}}"></i></button>
        @endif
   </td>
   @endif