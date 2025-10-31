@if(count($recordDetails) > 0 )
	@php $index = ($pageNo - 1) * $perPageRecord ; @endphp
	@foreach($recordDetails as $recordDetail)
		@php 
		$encodeRecordId = Wild_tiger::encode($recordDetail->i_id) ;
		$encodeEmployeeId  = Wild_tiger::encode($recordDetail->i_employee_id) ;
		@endphp
			<tr class="text-left">
				<td class="text-center">{{ ++$index }}</td>
                <td>{{ ( isset($recordDetail->dt_entry_date_time)  ? convertDateFormat($recordDetail->dt_entry_date_time,'d.m.Y') : '' ) }}</td>
                <td>
                
                @if( ( session()->get('is_supervisor') == false ) && ( $recordDetail->i_employee_id == session()->get('user_employee_id')) )
                	{{ ( isset($recordDetail->punchEmployee->v_employee_full_name)  ? $recordDetail->punchEmployee->v_employee_full_name : '' ) }} ({{ ( isset($recordDetail->punchEmployee->v_employee_code)  ? $recordDetail->punchEmployee->v_employee_code : '' ) }})
                @else
                	<a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}" target="_blank"> {{ ( isset($recordDetail->punchEmployee->v_employee_full_name)  ? $recordDetail->punchEmployee->v_employee_full_name : '' ) }} ({{ ( isset($recordDetail->punchEmployee->v_employee_code)  ? $recordDetail->punchEmployee->v_employee_code : '' ) }})</a>
                @endif
                <br> {{  ( isset($recordDetail->punchEmployee->v_contact_no) ?  $recordDetail->punchEmployee->v_contact_no  : '' ) }}
                 
                
                </td>
                <td>{{ ( isset($recordDetail->punchEmployee->teamInfo->v_value)  ? $recordDetail->punchEmployee->teamInfo->v_value : '' ) }}</td>
                <?php /* ?>
                <td>In</td>
                <?php */ ?>
                <td>{{ ( isset($recordDetail->dt_entry_date_time)  ? convertDateFormat ( $recordDetail->dt_entry_date_time, 'h:i A' ) : '' ) }}</td>
            </tr>
		@endforeach
	@if(!empty($pagination))
 	    <input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
 		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
 		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
 	@endif
@else
 	<tr>
		<td colspan="15" class="text-center">@lang('messages.no-record-found')</td>
	</tr>
@endif
@include('admin/common-display-count')