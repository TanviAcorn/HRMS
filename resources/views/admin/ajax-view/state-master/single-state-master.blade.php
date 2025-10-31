<?php
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
?>
<td class="text-center sr-col">{{ $rowIndex }}</td>
<td class="text-left">{{ (!empty($recordDetail->v_state_name) ? $recordDetail->v_state_name :'') }}</td>
<td class="text-center status-update">{{ ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans('messages.active') : trans('messages.inactive') )}}</td>
@if((checkPermission('edit_state') != false) || (checkPermission('delete_state') != false))
<td class="actions-button">
	@if(checkPermission('edit_state') != false)
	<button title="{{ trans('messages.edit') }}" onclick="editStateModel(this)" data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></button>
	@endif
	@if(checkPermission('delete_state') != false)
 	<button title="{{ trans('messages.delete') }}" data-record-id="{{ $encodeRecordId }}" data-module-name="state-master" onclick="deleteRecord(this);"  class="btn btn-sm mb-1 btn-delete btn-color-text"><i class="fa fa-trash"></i></button>
	@endif
	@if(checkPermission('edit_state') != false)
 	<button title="{{ (int) ($recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') }}" onclick='updateMasterStatusRecord(this,"state-master")' data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-active btn-color-text"><i class="{{ (int) ($recordDetail->t_is_active == 1) ? 'fa fa-eye-slash' :'fa fa-eye'}}"></i></button>
	@endif
</td>
@endif
