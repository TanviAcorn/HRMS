@if(count($recordDetails) > 0 )
	@php $index = ($pageNo - 1) * $perPageRecord ; @endphp
	@foreach($recordDetails as $recordDetail)
	@php 
		$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
		$encodeEmployeeId  = Wild_tiger::encode($recordDetail->i_employee_id);
	@endphp
	
 		<tr class="text-left has-record" data-record-id="{{ $recordDetail->i_login_id }}" >
 			<td class="text-center">{{ ++$index }}</td>
        	<td><a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}" target="_blank" >{{ ( isset($recordDetail->v_employee_full_name) ? $recordDetail->v_employee_full_name : '' )  }} ({{ ( isset($recordDetail->v_employee_code) ? $recordDetail->v_employee_code : '' )  }})</a></td>
        	<td>{{ ( isset($recordDetail->designationInfo->v_value) ? $recordDetail->designationInfo->v_value : '' )  }}</td>
        	<td>{{ ( isset($recordDetail->teamInfo->v_value) ? $recordDetail->teamInfo->v_value : '' )  }}</td>
        	<td>
        		<select name="role_{{  $recordDetail->i_login_id }}" class="form-control selected-role">
        			@if(count($roleDetails)  > 0 )
        				@foreach($roleDetails as $roleDetail)
        					@php
        					$selected = '';
        					if( isset($recordDetail->loginInfo->v_role) )
        					@endphp
        					<option value="{{ $roleDetail }}">{{ $roleDetail  }}</option>	
        				@endforeach
        			@endif
        		</select>
        	</td>
        </tr>
 	@endforeach
 	<?php 
 	if(!empty($pagination)){?>
 		<input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
 		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
 		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
 		<?php 
 	}
 	?>
@else
 	<tr>
		<td colspan="29" class="text-center">@lang('messages.no-record-found')</td>
	</tr>
@endif
@include('admin/common-display-count')						
 						