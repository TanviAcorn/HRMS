
@if( count($recordDetails) )
 	@php 
 	$index= ($page_no - 1) * $perPageRecord; 
 	$rowIndex = 0;
 	@endphp
 	@foreach ($recordDetails as $recordDetail)
 		<tr class="text-left">
 			<td class="text-center sr-col">{{ ++$rowIndex }}</td>
			<td>{{ isset( $recordDetail->dt_attendance_date) ? convertDateFormat ( $recordDetail->dt_attendance_date ) : ''   }}</td>
			<td>{{ isset( $recordDetail->v_name) ? $recordDetail->v_name : ''   }}</td>
			<td>{{ isset( $recordDetail->v_pay_code) ? $recordDetail->v_pay_code : ''   }}</td>
			<td>{{ isset( $recordDetail->v_department) ? $recordDetail->v_department : ''   }}</td>
			<td>{{ isset( $recordDetail->v_start) ? $recordDetail->v_start : ''   }}</td>
			<td>{{ isset( $recordDetail->v_shift) ? $recordDetail->v_shift : ''   }}</td>
			<td>{{ isset( $recordDetail->v_in) ? $recordDetail->v_in : ''   }}</td>
			<td>{{ isset( $recordDetail->v_out) ? $recordDetail->v_out : ''   }}</td>
			<td>{{ isset( $recordDetail->v_hour_worked) ? $recordDetail->v_hour_worked : ''   }}</td>
			<td>{{ isset( $recordDetail->v_status) ? $recordDetail->v_status : ''   }}</td>
			<td>{{ isset( $recordDetail->v_early_arrival) ? $recordDetail->v_early_arrival : ''   }}</td>
			<td>{{ isset( $recordDetail->v_shift_late) ? $recordDetail->v_shift_late : ''   }}</td>
			<td>{{ isset( $recordDetail->v_shift_early) ? $recordDetail->v_shift_early : ''   }}</td>
			<td>{{ isset( $recordDetail->v_ot) ? $recordDetail->v_ot : ''   }}</td>
			<td>{{ isset( $recordDetail->v_ot_amount) ? $recordDetail->v_ot_amount : ''   }}</td>
			<td>{{ isset( $recordDetail->v_over_stay) ? $recordDetail->v_over_stay : ''   }}</td>
			<td>{{ isset( $recordDetail->v_manual) ? $recordDetail->v_manual : ''   }}</td>
			<td>{{ isset( $recordDetail->v_in_location) ? $recordDetail->v_in_location : ''   }}</td>
			<td>{{ isset( $recordDetail->v_out_location) ? $recordDetail->v_out_location : ''   }}</td>
		</tr>
 	@endforeach
 	@if(!empty($pagination))
 		<input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
 		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
 	 	<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
 	@endif
 @else
 	<tr>
		<td colspan="21" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
 @endif
 @include('admin/common-display-count')  
                                
                                
                            