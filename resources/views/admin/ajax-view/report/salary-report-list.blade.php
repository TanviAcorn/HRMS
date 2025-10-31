@if(count($recordDetails) > 0 )
	@php $index = ($pageNo - 1) * $perPageRecord ; @endphp
	@foreach($recordDetails as $recordDetail)
		@php 
		$encodeRecordId = Wild_tiger::encode($recordDetail->i_id) ;
		$encodeEmployeeId  = Wild_tiger::encode($recordDetail->i_employee_id) ;
		@endphp
		<tr class="text-left has-record">
        	<?php /* <td class="check-box-design">
            	<div class="form-group mb-0 text-center">
                	<div class="form-check form-check-inline mr-0 ml-1">
                    	<input class="form-check-input row-checkbox" value="{{ $encodeRecordId  }}" type="checkbox" id="salary-report-{{  $recordDetail->i_id }}" name="check_{{ $recordDetail->i_id }}">
                        <label class="form-check-label lable-control" for="salary-report-{{ $recordDetail->i_id }}"></label>
                   	</div>
              	</div>
                </td> */ ?>
				<td class="text-center">
					<div class="form-group mb-0 text-center">
						<div class="checkbox-panel salary-cal-checkbox">
							<label for="salary-report-{{ $recordDetail->i_id }}" class="lable-control d-block"></label>
							<div class="form-check form-check-inline mr-0">
								<label class="checkbox" for="salary-report-{{ $recordDetail->i_id }}">
								<input type="checkbox" class="row-checkbox"  value="{{ $encodeRecordId  }}" id="salary-report-{{  $recordDetail->i_id }}" name="check_{{ $recordDetail->i_id }}"><span class="checkmark"></span></label>
							</div>
						</div>
					</div>
				</td>
                <td class="text-center">{{ ++$index }}</td>
                <td>{{ ( isset($recordDetail->dt_salary_month)  ? convertDateFormat($recordDetail->dt_salary_month, 'm.Y') : '' ) }}</td>
                <td class="text-center">
                	<div class="d-flex align-items-center">
                    	<a href="{{ route('view-salary', $encodeRecordId ) }}" target="_blank" class="btn-danger btn btn-sm manage-doc-btn pdf-download mr-2" title="{{ trans('messages.view-pay-slip') }}"><i class="fa fa-fw fa-file-pdf"></i></a>
                        <a href="{{ route('download-salary', $encodeRecordId ) }}" class="btn-success btn btn-sm manage-doc-btn upload-btn mr-2" title="{{ trans('messages.download-pay-slip') }}"><i class="fa fa-fw fa-download"></i></a>
                        <a href="javascript:void(0);" onclick="sendSinglePaySlip(this);" data-record-id="{{ $encodeRecordId }}" class="btn btn-sm text-white manage-doc-btn primary mr-2" title="{{ trans('messages.send-pay-slip') }}"><i class="fa fa-fw fa-location-arrow"></i></a>
                        <a href="javascript:void(0);" onclick="viewSalary(this);" data-emp-id="{{ $encodeEmployeeId }}" data-record-id="{{ $encodeRecordId }}" data-salary-month="{{ $recordDetail->dt_salary_month }}" data-emp-name="{{ ( isset($recordDetail->employee->v_employee_full_name) ? $recordDetail->employee->v_employee_full_name : '' ) }}" data-emp-code="{{ ( isset($recordDetail->employee->v_employee_code) ? $recordDetail->employee->v_employee_code : '' ) }}" class="btn btn-sm btn-theme text-white twt-btn-style align-items-center mr-2" title="{{ trans('messages.view-salary-calculation') }}"><i class="fa fa-fw fa-calculator"></i></a>
                        @if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) )
                        <?php /* ?>
                        <a href="javascript:void(0);" onclick="getEmployeeSalaryInfo(this);" data-ament-salary="{{ config('constants.SELECTION_YES') }}" data-emp-name="{{ ( isset($recordDetail->employee->v_employee_full_name) ? $recordDetail->employee->v_employee_full_name : '' ) }}" data-emp-code="{{ ( isset($recordDetail->employee->v_employee_code) ? $recordDetail->employee->v_employee_code : '' ) }}"  data-salary-month="{{ $recordDetail->dt_salary_month }}" data-emp-id="{{ Wild_tiger::encode( $recordDetail->i_employee_id ) }}"  class="btn btn-sm text-white manage-doc-btn primary mr-2" title="{{ trans('messages.amend-salary') }}"><i class="fa fa-fw fa-edit"></i></a>
                        <?php */ ?>
                        @endif
                 	</div>
               	</td>
                <td class="employee-name-code-td">
                	@if( ( session()->get('is_supervisor') == false ) && ( $recordDetail->i_employee_id == session()->get('user_employee_id')) )
                		{{ ( isset($recordDetail->employee->v_employee_full_name)  ? $recordDetail->employee->v_employee_full_name : '' ) }} ({{ ( isset($recordDetail->employee->v_employee_code)  ? $recordDetail->employee->v_employee_code : '' ) }})
                	@else
                		<a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}" target="_blank"> {{ ( isset($recordDetail->employee->v_employee_full_name)  ? $recordDetail->employee->v_employee_full_name : '' ) }} ({{ ( isset($recordDetail->employee->v_employee_code)  ? $recordDetail->employee->v_employee_code : '' ) }})</a>
                	@endif
                	
                	
                
                </td>
                <td>{{ ( isset($recordDetail->employee->teamInfo->v_value)  ? $recordDetail->employee->teamInfo->v_value : '' ) }}</td>
                <td class="text-left">{{ ( isset($recordDetail->employee->designationInfo->v_value)  ? $recordDetail->employee->designationInfo->v_value : '' ) }}</td>
                <td class="text-left">{{ ( isset($recordDetail->employee->v_contact_no)  ? $recordDetail->employee->v_contact_no : '' ) }}</td>
                <td class="text-left">{{ ( isset($recordDetail->employee->v_pan_no)  ? $recordDetail->employee->v_pan_no : '' ) }}</td>
                <td class="text-left">{{ ( isset($recordDetail->employee->v_uan_no)  ? $recordDetail->employee->v_uan_no : '' ) }}</td>
                <td class="text-left">{{ ( isset($recordDetail->employee->v_aadhar_no)  ? $recordDetail->employee->v_aadhar_no : '' ) }}</td>
                <td class="text-left">{{ ( isset($recordDetail->employee->bankInfo->v_value)  ? $recordDetail->employee->bankInfo->v_value : '' ) }}</td>
                <td class="text-left">{{ ( isset($recordDetail->employee->v_bank_account_no)  ? $recordDetail->employee->v_bank_account_no : '' ) }}</td>
                <td class="text-left">{{ ( isset($recordDetail->d_total_earning_amount)  ? decimalAmount($recordDetail->d_total_earning_amount) : '' ) }}</td>
                <td class="text-left">{{ ( isset($recordDetail->d_total_deduct_amount)  ? decimalAmount($recordDetail->d_total_deduct_amount) : '' ) }}</td>
                <td class="text-left">{{ ( isset($recordDetail->d_net_pay_amount)  ? decimalAmount($recordDetail->d_net_pay_amount) : '' ) }}</td>
         </tr>
	
 	@endforeach
	@if(!empty($pagination))
 	    <input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
 		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
 		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
 	@endif
@else
 	<tr>
		<td colspan="17" class="text-center">@lang('messages.no-record-found')</td>
	</tr>
@endif
@include('admin/common-display-count')		
							