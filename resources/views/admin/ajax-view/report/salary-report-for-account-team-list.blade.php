@if(count($recordDetails) > 0 )
	@php $index = ($pageNo - 1) * $perPageRecord ; @endphp
	@foreach($recordDetails as $recordDetail)
		@php 
		$encodeRecordId = Wild_tiger::encode($recordDetail->i_id) ;
		$encodeEmployeeId  = Wild_tiger::encode($recordDetail->i_employee_id) ;
		@endphp
		<tr class="text-left">
        	<td class="text-center">{{ ++$index }}</td>
        	<td class="text-left">{{ ( ( isset($recordDetail->dt_salary_month))  ? convertDateFormat($recordDetail->dt_salary_month , 'm.Y' )  : '' ) }}</td>
            <td><a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}" target="_blank">{{ ( isset($recordDetail->employee->v_employee_full_name) ? $recordDetail->employee->v_employee_full_name : '' ) }} ({{ ( isset($recordDetail->employee->v_employee_code) ? $recordDetail->employee->v_employee_code : '' ) }})</a></td>
            <td>{{ ( isset($recordDetail->employee->teamInfo->v_value) && !empty($recordDetail->employee->teamInfo->v_value) ? $recordDetail->employee->teamInfo->v_value : '' ) }}</td>
            <td class="text-left">{{ ( isset($recordDetail->employee->bankInfo->v_value)  ? $recordDetail->employee->bankInfo->v_value : '' ) }}</td>
            <td class="text-left">{{ ( ( isset($recordDetail->employee->bankInfo->i_id) && ( $recordDetail->employee->bankInfo->i_id == config('constants.HDFC_BANK_ID') ) )  ? (  isset( $recordDetail->employee->v_bank_account_no) ?  $recordDetail->employee->v_bank_account_no : '-'  ) : '-' ) }}</td>
            <td>{{ ( isset($recordDetail->d_net_pay_amount)  ? decimalAmount($recordDetail->d_net_pay_amount) : '' ) }}</td>
            @php
            $professionTaxAmount = "";
            if( isset($recordDetail->generatedSalaryInfo) && (!empty($recordDetail->generatedSalaryInfo)) ){
            	foreach($recordDetail->generatedSalaryInfo as $salaryDetails){
            		if(  ( isset($salaryDetails->i_component_id) && ( $salaryDetails->i_component_id == config('constants.PT_SALARY_COMPONENT_ID') ) ) ){
            			$professionTaxAmount = ( isset($salaryDetails->d_paid_amount) ? ($salaryDetails->d_paid_amount) : '' );
            		}
            	}
            }	
            @endphp
            <td>{{ (!empty($professionTaxAmount) ? decimalAmount($professionTaxAmount) : '' ) }}</td>
     	</tr>
	@endforeach
	@if(!empty($pagination))
 	    <input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
 		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
 		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
 	@endif
@else
 	<tr>
		<td colspan="8" class="text-center">@lang('messages.no-record-found')</td>
	</tr>
@endif
@include('admin/common-display-count')		
							