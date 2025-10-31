@if(count($recordDetails) > 0 )
	@php $index = ($page_no - 1) * $perPageRecord ; @endphp
	@foreach($recordDetails as $recordDetail)
	@php $encodeRecordId = Wild_tiger::encode($recordDetail->i_id); @endphp
	
	<tr class="text-left">
		<td class="text-center">{{ ++$index }}</td>
		<td><?php echo (isset($recordDetail->v_employee_full_name) ? '<a href="'.route('employee-master.profile', $encodeRecordId ).'" target="_blank" title="'.trans("messages.view-profile").'">' .($recordDetail->v_employee_full_name).(!empty($recordDetail->v_employee_code) ? ' ('.$recordDetail->v_employee_code .')' : '').'</a>'  : '')?></td>
		<td class="text-left">{{ (!empty($recordDetail->teamInfo->v_value) ? $recordDetail->teamInfo->v_value :'')}}</td>
		<td>{{ (!empty($recordDetail->e_gender) ? $recordDetail->e_gender :'')}}</td>
		<td class="text-left">{{ (!empty($recordDetail->dt_birth_date) ? convertDateFormat($recordDetail->dt_birth_date,'d.m.Y') :'')}}</td>
		<td class="text-left">{{ (!empty($recordDetail->dt_joining_date) ? convertDateFormat($recordDetail->dt_joining_date,'d.m.Y') :'')}}</td>
		<td class="text-left">{{ (!empty($recordDetail->shiftInfo->v_shift_name) ? $recordDetail->shiftInfo->v_shift_name :'')}}</td>
		<td class="text-left"><?php echo ( ( (!empty($recordDetail->dt_joining_date)) && (!empty($recordDetail->dt_probation_end_date)) )  ? convertDateFormat($recordDetail->dt_joining_date,'d.m.Y') .(!empty($recordDetail->dt_probation_end_date) ? "<br>".convertDateFormat($recordDetail->dt_probation_end_date,'d.m.Y') :'') :'') ?></td>
		<td class="text-left"><?php echo (!empty($recordDetail->dt_notice_period_start_date) ? convertDateFormat($recordDetail->dt_notice_period_start_date,'d.m.Y') .(!empty($recordDetail->dt_notice_period_end_date) ? "<br>".convertDateFormat($recordDetail->dt_notice_period_end_date,'d.m.Y') :'') :'') ?></td>
		<td class="text-left">{{ (!empty($recordDetail->dt_notice_period_end_date) ? convertDateFormat($recordDetail->dt_notice_period_end_date,'d.m.Y') :'') }}</td>
	</tr>
 	@endforeach
	@if(!empty($pagination))
 	    <input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
 		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
 		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
 	@endif
@else
 	<tr>
		<td colspan="10" class="text-center">@lang('messages.no-record-found')</td>
	</tr>
@endif
@include('admin/common-display-count')						
 													
							
		