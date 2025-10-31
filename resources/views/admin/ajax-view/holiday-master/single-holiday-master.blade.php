<?php
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
?>
<td class="text-center sr-col">{{ $rowIndex }}</td>
<td>{{ (!empty($recordDetail->v_holiday_name) ? $recordDetail->v_holiday_name :'') }}</td>
<td class="text-left">{{ (!empty($recordDetail->dt_holiday_date) ? clientDate($recordDetail->dt_holiday_date) :'') }}</td>
<td class="text-center status-update">{{ ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans('messages.active') : trans('messages.inactive') )}}</td>
@if((checkPermission('edit_holiday_master') != false) || (checkPermission('delete_holiday_master') != false))
<td class="actions-button d-flex">
	@if(checkPermission('edit_holiday_master') != false)
	<button title="{{ trans('messages.edit') }}" onclick="editHolidayMasterModel(this)" data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></button>
	@endif
	@if(checkPermission('delete_holiday_master') != false)
 	<button title="{{ trans('messages.delete') }}" data-record-id="{{ $encodeRecordId }}" data-module-name="holiday-master" onclick="deleteRecord(this);"  class="btn btn-sm mb-1 btn-delete btn-color-text"><i class="fa fa-trash"></i></button>
	@endif
	@if(checkPermission('edit_holiday_master') != false)
 	<button title="{{ (int) ($recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') }}" onclick='updateMasterStatusRecord(this,"holiday-master")' data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-active btn-color-text">
 		{!! (int) ($recordDetail->t_is_active == 1) ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>'  !!}	
 	</button>
	@endif
</td>
@endif