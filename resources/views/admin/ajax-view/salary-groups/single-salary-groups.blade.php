<?php
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
?>
<td class="text-center sr-col">{{ $rowIndex }}</td>
<td>{{ (!empty($recordDetail->v_group_name) ? $recordDetail->v_group_name :'') }}</td>
 <td class="text-left">{{ (!empty($recordDetail->v_group_description) ? $recordDetail->v_group_description :'') }}</td>
<td class="text-center status-update">{{ ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans('messages.active') : trans('messages.inactive') )}}</td>
@if((checkPermission('edit_salary_groups') != false) || (checkPermission('delete_salary_groups') != false))
<td class="actions-button">
	@if(checkPermission('edit_salary_groups') != false)
	<button title="{{ trans('messages.edit') }}" onclick="editSalaryGroupsModel(this)" data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></button>
	@endif
	@if(checkPermission('delete_salary_groups') != false)
 	<button title="{{ trans('messages.delete') }}" data-record-id="{{ $encodeRecordId }}" data-module-name="salary-groups" onclick="deleteRecord(this);"  class="btn btn-sm mb-1 btn-delete btn-color-text"><i class="fa fa-trash"></i></button>
	@endif
	@if(checkPermission('edit_salary_groups') != false)
 	<button title="{{ (int) ($recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') }}" onclick='updateMasterStatusRecord(this,"salary-groups")' data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-active btn-color-text"><i class="{{ (int) ($recordDetail->t_is_active == 1) ? 'fa fa-eye-slash' :'fa fa-eye'}}"></i></button>
	@endif
</td>
@endif
                              
