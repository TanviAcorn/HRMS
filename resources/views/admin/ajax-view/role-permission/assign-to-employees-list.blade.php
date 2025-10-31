@if(count($recordDetails) > 0 )
	@php $index = 0; @endphp
	@foreach($recordDetails as $recordDetail)
		@php 
		$encodeRecordId = Wild_tiger::encode($recordDetail->i_id) ;
		@endphp
		<tr class="text-left has-record">
        	<td class="check-box-design">
            	<div class="form-group mb-0 text-center">
                	<div class="form-check form-check-inline mr-0 ml-1">
                    	<input class="form-check-input row-checkbox" value="{{ $encodeRecordId  }}" type="checkbox" id="salary-report-{{  $recordDetail->i_id }}" name="check_{{ $recordDetail->i_id }}" {{ isset($recordDetail->i_role_permission) && !empty($recordDetail->i_role_permission) ? 'checked' : '' }} onchange='checkAssignUser(this)'>
                        <label class="form-check-label lable-control" for="salary-report-{{ $recordDetail->i_id }}"></label>
                   	</div>
              	</div>
                </td>
                <td class="text-center">{{ ++$index }}</td>
                <td><a href="{{ route('employee-master.profile', $encodeRecordId ) }}" target="_blank"> {{ ( isset($recordDetail->v_employee_full_name)  ? $recordDetail->v_employee_full_name : '' ) }} ({{ ( isset($recordDetail->v_employee_code)  ? $recordDetail->v_employee_code : '' ) }})</a></td>
                <td>{{ ( isset($recordDetail->v_outlook_email_id)  ? $recordDetail->v_outlook_email_id : '' ) }}</td>
                <td>{{ ( isset($recordDetail->v_contact_no)  ? $recordDetail->v_contact_no : '' ) }}</td>
                <td>{{ ( isset($recordDetail->teamInfo->v_value)  ? $recordDetail->teamInfo->v_value : '' ) }}</td>
                <td class="text-left">{{ ( isset($recordDetail->designationInfo->v_value)  ? $recordDetail->designationInfo->v_value : '' ) }}</td>
                 <td class="text-left">{{ isset($recordDetail->dt_joining_date) && !empty($recordDetail->dt_joining_date) ? clientDate($recordDetail->dt_joining_date) : '' }}</td>
               
         </tr>
	
 	@endforeach
@else
 	<tr>
		<td colspan="8" class="text-center">@lang('messages.no-record-found')</td>
	</tr>
@endif		
							