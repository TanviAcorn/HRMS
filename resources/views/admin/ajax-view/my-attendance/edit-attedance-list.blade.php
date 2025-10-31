@if(count($recordDetails) > 0 )
	@php $index = ($pageNo - 1) * $perPageRecord ; @endphp
	@foreach($recordDetails as $recordDetail)
	@php 
		$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
		$encodeEmployeeId  = Wild_tiger::encode($recordDetail->i_employee_id);
		$duration  = "";
		if( ( isset($recordDetail->t_start_time) && (  $recordDetail->t_start_time != '00:00:00' )  ) && ( isset($recordDetail->t_end_time) && (  $recordDetail->t_end_time != '00:00:00' )  ) ){
			$duration  = diffBetweenTime(  $recordDetail->t_start_time , $recordDetail->t_end_time );
		}
		$totalHours = "";
		if( (!empty($recordDetail->t_start_time)) && (!empty($recordDetail->t_end_time)) && ( $recordDetail->t_start_time != config('constants.TIME_DEFAULT_VALUE') ) && ( $recordDetail->t_end_time != config('constants.TIME_DEFAULT_VALUE')  ) ){
			$totalHours = diffBetweenTime(  $recordDetail->t_start_time , $recordDetail->t_end_time );
		}
		$breakTime = ( ( (!empty($recordDetail->t_total_break_time)) && ( $recordDetail->t_total_break_time != config('constants.TIME_DEFAULT_VALUE')  ) ) ? convertSecondIntoHourMinute( strtotime($recordDetail->t_total_break_time) - strtotime('TODAY') ) : '');
		$workingHours = '';
		if( (!empty($recordDetail->t_start_time)) && (!empty($recordDetail->t_end_time) ) ){
			$workingHours = (!empty(workingHoursByTotalAndBreakTime($recordDetail)) ? workingHoursByTotalAndBreakTime($recordDetail) : '');
		}
		
	@endphp
	
 		<tr class="text-left has-record" data-record-id="{{ $recordDetail->i_id }}">
 			<td class="text-center">{{ ++$index }}</td>
        	<td>
        		{!! ( isset($recordDetail->dt_date) ? convertDateFormat($recordDetail->dt_date) : '' ) . '<br>' .  ( isset($recordDetail->dt_date) ? convertDateFormat($recordDetail->dt_date,'l') : '' )   !!}
        		<?php if( isset($employeeWiseSuspendRecordDetails[$recordDetail->i_employee_id]) && in_array( $recordDetail->dt_date ,  $employeeWiseSuspendRecordDetails[$recordDetail->i_employee_id] )  ) {  ?>
        			<br> <span class="text-danger">{{ trans('messages.suspended') }}</span>	
        		<?php } ?>	
        	</td>
        	<td class="employee-name-code-th"><a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}" target="_blank" >{{ ( isset($recordDetail->v_employee_full_name) ? $recordDetail->v_employee_full_name : '' )  }} ({{ ( isset($recordDetail->v_employee_code) ? $recordDetail->v_employee_code : '' )  }})</a></td>
        	<td>{{ ( isset($recordDetail->team) ? $recordDetail->team : '' )  }}</td>
        	<td >{{ ( isset($recordDetail->dt_matrix_start_time) && (  $recordDetail->dt_matrix_start_time != '00:00:00' ) ? clientCalendarTime( $recordDetail->dt_matrix_start_time ) : '' )  }}</td>
        	<td>{{ ( isset($recordDetail->dt_matrix_end_time) && (  $recordDetail->dt_matrix_end_time != '00:00:00' ) ? clientCalendarTime( $recordDetail->dt_matrix_end_time ) : '' )  }}</td>
        	<td><input type="text" readonly name="start_time_{{ $recordDetail->i_id }}" class="form-control start-time" value="{{ ( isset($recordDetail->t_start_time) && (  $recordDetail->t_start_time != '00:00:00' ) ? clientCalendarTime( $recordDetail->t_start_time ) : '' )  }}" ></td>
        	<td><input type="text" readonly name="end_time_{{ $recordDetail->i_id }}" class="form-control end-time" value="{{ ( isset($recordDetail->t_end_time) && (  $recordDetail->t_end_time != '00:00:00' )  ? clientCalendarTime ( $recordDetail->t_end_time ) : '' )  }}" ></td>
        	<?php /* ?>
        	<td class="duration-text">{{ ( isset($recordDetail->t_total_working_time) && (  $recordDetail->t_total_working_time != '00:00:00' ) ? clientCalendarTime( $recordDetail->t_total_working_time ) : '' ) }}</td>
        	<?php */ ?>
        	<td class="duration-text">{{ $totalHours }} </td>
        	
        	<td class="text-left">{{ $breakTime }}</td>
        	<td class="text-left">{{ $workingHours }}</td>
        	<td>
        		<select name="status_{{  $recordDetail->i_id }}" class="form-control attendance-status">
        			<option value="" >{{ trans('messages.select') }}</option>
        			@if(count($attendanceStatusDetails) > 0 )
        				@foreach($attendanceStatusDetails as $attendanceStatusKey =>  $attendanceStatusDetail)
        					@php
        						$selected = '';
        						if(  isset($recordDetail->e_status) && ( $recordDetail->e_status == $attendanceStatusKey ) ){
        							$selected = "selected='selected'";
        						}
        						
        						
        					@endphp
        					<option value="{{ $attendanceStatusKey }}" {{ $selected }}>{{ $attendanceStatusDetail }}</option>
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
 						