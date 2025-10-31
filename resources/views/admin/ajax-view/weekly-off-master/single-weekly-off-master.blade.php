<?php $encodeRecordId = Wild_tiger::encode($recordDetail->i_id); ?>
<td class="text-center sr-col">{{ $rowIndex }}</td>
<?php /*?><td>1<sup>st</sup> and 3<sup>rd</sup> Saturday</td> <?php */?>
<td>{{ (!empty($recordDetail->v_weekly_off_name) ? $recordDetail->v_weekly_off_name :'') }}</td>
<td class="text-left">{{ (!empty($recordDetail->v_description) ? $recordDetail->v_description : null) }}</td>
<td class="text-center status-update">{{ ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans('messages.active') : trans('messages.inactive') )}}</td>
@if(checkPermission('edit_weekly_offs') != false || checkPermission('delete_weekly_offs') != false)
<td class="actions-button">
	@if(checkPermission('edit_weekly_offs') != false)
		<button title="{{ trans('messages.edit') }}" onclick="editWeeklyOffModel(this)" data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></button>
	@endif
	@if(checkPermission('delete_weekly_offs') != false)
 		<button title="{{ trans('messages.delete') }}" data-record-id="{{ $encodeRecordId }}" data-module-name="weekly-off-master" onclick="deleteRecord(this);"  class="btn btn-sm mb-1 btn-delete btn-color-text"><i class="fa fa-trash"></i></button>
	@endif
	@if(checkPermission('edit_weekly_offs') != false)
 		<button title="{{ (int) ($recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') }}" onclick='updateMasterStatusRecord(this,"weekly-off-master")' data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-active btn-color-text"><i class="{{ (int) ($recordDetail->t_is_active == 1) ? 'fa fa-eye-slash' :'fa fa-eye'}}"></i></button>
	 @endif
</td>
@endif