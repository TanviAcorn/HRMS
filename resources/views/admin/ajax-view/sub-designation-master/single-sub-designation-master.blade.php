<?php
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
?>
<td class="text-center sr-col">{{ $rowIndex }}</td>
<td>{{ (!empty($recordDetail->v_sub_designation_name) ? $recordDetail->v_sub_designation_name :'') }}</td>
<td class="text-left">{{ (!empty($recordDetail->designation) && !empty($recordDetail->designation->v_value) ? $recordDetail->designation->v_value :'') }}</td>
<td class="text-center status-update">{{ ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans('messages.active') : trans('messages.inactive') )}}</td>
@if((checkPermission('edit_designation_master') != false) || (checkPermission('delete_designation_master') != false))
<td class="actions-button">
	@if(checkPermission('edit_designation_master') != false)
 	<button title="{{ trans('messages.edit') }}" onclick="editSubDesignationModel(this)" data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></button>
	@endif
	@if(checkPermission('delete_designation_master') != false)
 	<button title="{{ trans('messages.delete') }}" data-record-id="{{ $encodeRecordId }}" data-module-name="sub-designation-master" onclick="deleteRecord(this);"  class="btn btn-sm mb-1 btn-delete btn-color-text"><i class="fa fa-trash"></i></button>
	@endif
	@if(checkPermission('edit_designation_master') != false)
 	<button title="{{ (int) ($recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') }}" onclick='updateMasterStatusRecord(this,"sub-designation-master")' data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-active btn-color-text"><i class="{{ (int) ($recordDetail->t_is_active == 1) ? 'fa fa-eye-slash' :'fa fa-eye'}}"></i></button>
	 @endif
</td>
@endif
