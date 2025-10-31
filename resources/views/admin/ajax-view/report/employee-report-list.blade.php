@if(count($recordDetails) > 0 )
	@php $index = ($page_no - 1) * $perPageRecord ; @endphp
	@foreach($recordDetails as $recordDetail)
	@php $encodeRecordId = Wild_tiger::encode($recordDetail->i_id); @endphp
 		<tr class="text-left">
 			<td class="text-center">{{ ++$index }}</td>
        	<td class="employee-name-code-td">
	          	@if(!empty($recordDetail->v_employee_code))
	          		<a href="{{ route('employee-master.profile', $encodeRecordId ) }}" target="_blank" title="{{ trans('messages.view-profile')}}">{{ (!empty($recordDetail->v_employee_code) ? $recordDetail->v_employee_code :'') }}</a>
	          	@endif
          	</td>
          	<td class="employee-name-code-td employee-name-code-td-2" style="left:118px !important;">
	          	@if(!empty($recordDetail->v_employee_name))
	          		<a href="{{ route('employee-master.profile', $encodeRecordId ) }}" target="_blank" title="{{ trans('messages.view-profile')}}">{{ (!empty($recordDetail->v_employee_name) ? $recordDetail->v_employee_name :'') }}</a>
	          	@endif
          	</td>
         	<td>{{ (!empty($recordDetail->v_employee_full_name) ? $recordDetail->v_employee_full_name :'') }}</td>
        	<td>{{ (!empty($recordDetail->e_gender) ? $recordDetail->e_gender :'') }}</td>
        	<td class="text-left">{{ (!empty($recordDetail->v_blood_group) ? $recordDetail->v_blood_group :'') }}</td>
      	 	<td class="text-left">{{ (!empty($recordDetail->dt_joining_date) ? convertDateFormat($recordDetail->dt_joining_date,'d.m.Y') :'') }}</td>
      		<td class="text-left">{{ (!empty($recordDetail->designationInfo->v_value) ? $recordDetail->designationInfo->v_value :'') }}</td>
       		<td class="text-left">{{ (!empty($recordDetail->teamInfo->v_value) ? $recordDetail->teamInfo->v_value :'') }}</td>
         	<td class="text-left">{{ (!empty($recordDetail->leaderInfo->v_employee_full_name) ? $recordDetail->leaderInfo->v_employee_full_name .(!empty($recordDetail->leaderInfo->v_employee_code) ? ' (' . $recordDetail->leaderInfo->v_employee_code . ')' :''):'') }}</td>
         	<td class="text-left">{{ (!empty($recordDetail->e_employment_status) ? $recordDetail->e_employment_status :'') }}</td>
         	<td class="text-left">{!! (!empty($recordDetail->recruitmentSourceInfo->v_value) ? $recordDetail->recruitmentSourceInfo->v_value .(!empty($recordDetail->employeeInfo->v_employee_full_name) ? '<br>'.$recordDetail->employeeInfo->v_employee_full_name . ' (' . $recordDetail->employeeInfo->v_employee_code . ')' :''):'') !!}</td>
            <td class="text-left">{{ (!empty($recordDetail->v_aadhar_no) ? $recordDetail->v_aadhar_no :'') }}</td>
         	<td class="text-left">{{ (!empty($recordDetail->v_pan_no) ? $recordDetail->v_pan_no :'') }}</td>
         	<td class="text-left">{{ (!empty($recordDetail->v_education) ? $recordDetail->v_education :'') }}</td>
         	<td class="text-left">{{ (!empty($recordDetail->v_cgpa) ? $recordDetail->v_cgpa :'') }}</td>
         	<td class="text-left">{{ (!empty($recordDetail->e_marital_status) ? $recordDetail->e_marital_status :'') }}</td>
          	<td class="text-left">{{ (!empty($recordDetail->shiftInfo->v_shift_name) ? $recordDetail->shiftInfo->v_shift_name :'') }}</td>
           	<td class="text-left">{{ (!empty($recordDetail->weekOffInfo->v_weekly_off_name) ? $recordDetail->weekOffInfo->v_weekly_off_name :'') }}</td>
         	<td class="text-left">{{ (!empty($recordDetail->v_outlook_email_id) ? $recordDetail->v_outlook_email_id :'') }}</td>
         	<td class="text-left">{{ (!empty($recordDetail->v_contact_no) ? $recordDetail->v_contact_no :'') }}</td>
          	<td class="text-left">{{ (!empty($recordDetail->v_personal_email_id) ? $recordDetail->v_personal_email_id :'') }}</td>
        	<td class="text-left">{{ (!empty($recordDetail->dt_birth_date) ? convertDateFormat($recordDetail->dt_birth_date,'d.m.Y') :'') }}</td>
         	<td class="text-left">{{ (!empty($recordDetail->v_current_address_line_first) ? $recordDetail->v_current_address_line_first .(!empty($recordDetail->v_current_address_line_second) ? ', '.$recordDetail->v_current_address_line_second .(!empty($recordDetail->cityCurrentInfo->v_city_name) ? ', '.$recordDetail->cityCurrentInfo->v_city_name .(!empty($recordDetail->v_current_address_pincode) ? ', '.$recordDetail->v_current_address_pincode :'') :'') :''):'') }}</td>
          	<td class="text-left">{{ (!empty($recordDetail->v_permanent_address_line_first) ? $recordDetail->v_permanent_address_line_first .(!empty($recordDetail->v_permanent_address_line_second) ? ', '.$recordDetail->v_permanent_address_line_second .(!empty($recordDetail->cityPermanentInfo->v_city_name) ? ', '.$recordDetail->cityPermanentInfo->v_city_name .(!empty($recordDetail->v_permanent_address_pincode) ? ', '.$recordDetail->v_permanent_address_pincode :'') :''):''):'') }}</td>
         	<td class="text-left">{{ (!empty($recordDetail->probationPeriodInfo->v_probation_policy_name) ? $recordDetail->probationPeriodInfo->v_probation_policy_name . (!empty($recordDetail->probationPeriodInfo->v_probation_period_duration) ? ' - '. $recordDetail->probationPeriodInfo->v_probation_period_duration . (!empty($recordDetail->probationPeriodInfo->e_months_weeks_days) ? ' '. $recordDetail->probationPeriodInfo->e_months_weeks_days  : '' )  : '' )  :'') }}</td>
         	<td class="text-left">{{ (!empty($recordDetail->noticePeriodInfo->v_probation_policy_name) ? $recordDetail->noticePeriodInfo->v_probation_policy_name . (!empty($recordDetail->noticePeriodInfo->v_probation_period_duration) ? ' - '. $recordDetail->noticePeriodInfo->v_probation_period_duration . (!empty($recordDetail->noticePeriodInfo->e_months_weeks_days) ? ' '. $recordDetail->noticePeriodInfo->e_months_weeks_days  : '' )  : '' )   :'') }} </td>
          	<td class="text-left">{{ (!empty($recordDetail->bankInfo->v_value) ? $recordDetail->bankInfo->v_value :'') }}</td>
          	<td class="text-left">{{ (!empty($recordDetail->v_bank_account_no) ? $recordDetail->v_bank_account_no :'') }}</td>
           	<td class="text-left">{{ (!empty($recordDetail->v_bank_account_ifsc_code) ? $recordDetail->v_bank_account_ifsc_code :'') }}</td>
          	<td class="text-left">{{ (!empty($recordDetail->v_uan_no) ? $recordDetail->v_uan_no :'') }}</td>
          	<td class="text-left">{{ (isset($recordDetail->salaryInfo->salaryGroup->v_group_name) ? $recordDetail->salaryInfo->salaryGroup->v_group_name :'') }} <br>{{ (isset($recordDetail->salaryInfo->e_pf_deduction) ? $recordDetail->salaryInfo->e_pf_deduction :'') }} </td>
          	<td class="text-left">{{ (isset($recordDetail->employeeAssignRole->v_role_name) ? $recordDetail->employeeAssignRole->v_role_name :'') }}</td>
         	<td class="text-left">{{ ( ( ( isset($recordDetail->e_employment_status) ) && ( in_array( $recordDetail->e_employment_status , [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS') ] ) ) && ( isset( $recordDetail->dt_notice_period_end_date ) ) ) ? clientDate($recordDetail->dt_notice_period_end_date) : '' ) }}</td>
         	<td class="text-left">{{ ( ( ( isset($recordDetail->e_employment_status) ) && ( in_array( $recordDetail->e_employment_status , [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS') ] ) ) && ( isset( $recordDetail->dt_pf_expiry_date ) ) ) ? clientDate($recordDetail->dt_pf_expiry_date) : '' ) }}</td>
         	@if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_VIEW_SALARY'), session()->get('user_permission')  ) ) ) ) ) )
         	<td class="text-left">{{ ( isset($recordDetail->salaryInfo->d_net_pay_monthly) ?  decimalAmount($recordDetail->salaryInfo->d_net_pay_monthly) : '' ) }}</td>
         	@endif
         	<td class="text-left">{!! ( ( ( isset($recordDetail->e_employment_status) ) && ( in_array( $recordDetail->e_employment_status , [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS') ] ) ) && ( isset( $recordDetail->latestDisplayResignHistory->e_initiate_type ) ) ) ? ( (  $recordDetail->latestDisplayResignHistory->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) ?  trans('messages.resignation') . ' - ' . (!empty($recordDetail->latestDisplayResignHistory->resignation->v_value) ? ($recordDetail->latestDisplayResignHistory->resignation->v_value) : '' ) : ( ( $recordDetail->latestDisplayResignHistory->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) ? trans('messages.termination') . ' - ' . (!empty($recordDetail->latestDisplayResignHistory->termination->v_value) ? ( $recordDetail->latestDisplayResignHistory->termination->v_value ) : '' ) : '' ) )  : '' ) !!}</td>
         	<td class="text-left">{{ ( ( ( isset($recordDetail->e_employment_status) ) && ( in_array( $recordDetail->e_employment_status , [ config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') , config('constants.RELIEVED_EMPLOYMENT_STATUS') ] ) ) && ( isset( $recordDetail->latestDisplayResignHistory->e_initiate_type ) ) ) ? ( (  $recordDetail->latestDisplayResignHistory->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) ?  (!empty($recordDetail->latestDisplayResignHistory->dt_employee_notice_date) ? clientDate($recordDetail->latestDisplayResignHistory->dt_employee_notice_date) : '' ) : ( ( $recordDetail->latestDisplayResignHistory->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) ? (!empty($recordDetail->latestDisplayResignHistory->dt_termination_notice_date) ? clientDate( $recordDetail->latestDisplayResignHistory->dt_termination_notice_date ) : '' ) : '' ) )  : '' ) }}</td>
         	<td class="text-left">{{ ((!empty($recordDetail->t_is_active)) && ($recordDetail->t_is_active == 1) ? config("constants.ENABLE_STATUS") :  config("constants.DISABLE_STATUS")) }}</td>	
 		</tr>
 	@endforeach
	@if(!empty($pagination))
 	    <input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
 		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
 		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
 	@endif
@else
 	<tr>
		<td colspan="40" class="text-center">@lang('messages.no-record-found')</td>
	</tr>
@endif
@include('admin/common-display-count')						
 						