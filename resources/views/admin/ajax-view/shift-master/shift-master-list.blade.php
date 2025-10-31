<?php 
if(count($recordDetails) > 0 ){
	$index= ($page_no - 1) * $perPageRecord;
	foreach ($recordDetails as $recordDetail){
		$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
		?>	
		<tr>
			<td class="text-center">{{ ++$index }}</td>
			<td>{{ (!empty($recordDetail->v_shift_name) ? $recordDetail->v_shift_name :'') }}</td>
			<td>{{ (!empty($recordDetail->v_shift_code) ? $recordDetail->v_shift_code :'') }}</td>
			<td>{{ (!empty($recordDetail->e_shift_type) ? $recordDetail->e_shift_type :'') }}</td>
			<td class="text-left">{{ (!empty($recordDetail->v_description) ? $recordDetail->v_description :'') }}</td>
			<td class="text-center">{{ ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans('messages.active') : trans('messages.inactive') )}}</td>
			<td class="actions-button">
			<a title="{{ trans('messages.edit') }}" href="{{route('shift-master.edit',$encodeRecordId)}}" class="btn btn-sm mb-1 btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></a>
			<button title="{{ trans('messages.delete') }}" data-record-id="{{ $encodeRecordId }}" data-module-name="shift-master" onclick="deleteRecord(this);" class="btn btn-sm mb-1 btn-delete btn-color-text"><i class="fa fa-trash"></i></button>
			<button title="{{ (int) ($recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') }}" onclick='updateMasterStatusRecord(this,"shift-master")' data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-active btn-color-text"><i class="fa fa-eye-slash"></i></button>
			</td>
		</tr>
	<?php 
	}
	if(!empty($pagination)){
		?>
		<input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
		<?php 
	}
}else { ?>
	<tr>
		<td colspan="7" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
	<?php 
}?>