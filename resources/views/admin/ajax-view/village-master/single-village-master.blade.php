@php $encodeRecordId = Wild_tiger::encode($recordDetail->i_id); @endphp
<td class="text-center sr-col">{{ $rowIndex }}</td>
<td>{{ (!empty($recordDetail->v_village_name) ? $recordDetail->v_village_name :'') }}</td>
<td class="text-left">{{ (!empty($recordDetail->cityMaster->v_city_name) ? $recordDetail->cityMaster->v_city_name :'') }}</td>
<td class="text-left">{{ (!empty($recordDetail->cityMaster->stateMaster->v_state_name) ? $recordDetail->cityMaster->stateMaster->v_state_name :'') }}</td>
<td class="text-center status-update">{{ ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans('messages.active') : trans('messages.inactive') )}}</td>
@if((checkPermission('edit_village') != false) || (checkPermission('edit_village') != false))
<td class="actions-button">
	@if(checkPermission('edit_village') != false)
 	<button title="{{ trans('messages.edit') }}" onclick="editVillageModel(this)" data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></button>
	@endif
	@if(checkPermission('delete_village') != false)
 	<button title="{{ trans('messages.delete') }}" data-record-id="{{ $encodeRecordId }}" data-module-name="village-master" onclick="deleteRecord(this);"  class="btn btn-sm mb-1 btn-delete btn-color-text"><i class="fa fa-trash"></i></button>
	@endif
	@if(checkPermission('edit_village') != false)
 	<button title="{{ (int) ($recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') }}" onclick='updateMasterStatusRecord(this,"village-master")' data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-active btn-color-text"><i class="{{ (int) ($recordDetail->t_is_active == 1) ? 'fa fa-eye-slash' :'fa fa-eye'}}"></i></button>
	@endif
</td>
@endif

                