		@php 
			$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
			$encodeEmployeeId = Wild_tiger::encode($recordDetail->i_employee_id); 
		@endphp
		
		
		<td class="text-center sr-col-index">{{ $rowIndex }}</td>
		@if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_RESIGNATION_REPORT'), session()->get('user_permission')  ) ) ) ) )
        	<td class="text-left" style="min-width:70px;">{!! ( ( $recordDetail->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE')  ) ? trans('messages.resignation') . ( isset($recordDetail->resignation->v_value) ? ' - ' . $recordDetail->resignation->v_value : ''  )  :  trans('messages.termination') . ( isset($recordDetail->termination->v_value) ? ' - ' . $recordDetail->termination->v_value : ''  ) )  !!}</td>
        @endif
		<td><?php echo (isset($recordDetail->employee->v_employee_full_name) ? '<a href="'.route('employee-master.profile', $encodeEmployeeId ).'" target="_blank" title="'.trans("messages.view-profile").'">' .($recordDetail->employee->v_employee_full_name).(!empty($recordDetail->employee->v_employee_code) ? ' ('.$recordDetail->employee->v_employee_code .')' : '').'</a>'  : '')?></td>
		<td class="text-left">{{ (!empty($recordDetail->employee->teamInfo->v_value) ? $recordDetail->employee->teamInfo->v_value :'')}}</td>
		<td>{{ (!empty($recordDetail->employee->designationInfo->v_value) ? $recordDetail->employee->designationInfo->v_value :'')}}</td>
		<td class="text-left">{!! (!empty($recordDetail->employee->leaderInfo->v_employee_full_name) ? ($recordDetail->employee->leaderInfo->v_employee_full_name) .  (isset($recordDetail->employee->leaderInfo->v_employee_code) ? ' ('. ($recordDetail->employee->leaderInfo->v_employee_code) . ')' : '' ) :'') !!} </td>
		<td class="text-left">{{ (!empty($recordDetail->employee->v_contact_no) ? ($recordDetail->employee->v_contact_no) :'')}}</td>
		<td class="text-left">{{ (!empty($recordDetail->employee->v_outlook_email_id) ? ($recordDetail->employee->v_outlook_email_id) :'')}}</td>
		<td class="text-left"><?php echo (!empty($recordDetail->dt_notice_start_date) ? convertDateFormat($recordDetail->dt_notice_start_date,'d.m.Y') :'') ?></td>
		<td class="text-left">{{ (!empty($recordDetail->dt_notice_end_date) ? convertDateFormat($recordDetail->dt_notice_end_date,'d.m.Y') :'') }}</td>
		<td>{!! ( isset($recordDetail->e_status )  ? $recordDetail->e_status . ( isset($recordDetail->approveEmployeeInfo->v_name)  ? '<br>' . $recordDetail->approveEmployeeInfo->v_name : '' ) . ( isset($recordDetail->v_approval_remark)  ? '<br>' . $recordDetail->v_approval_remark : '' )   : '' ) !!}</td>
		<td class="text-center">
			@if( in_array( $recordDetail->e_status , [ config('constants.PENDING_STATUS') , config('constants.APPROVED_STATUS')  ]  )  )
				@if( ( $recordDetail->e_status == config('constants.PENDING_STATUS') ) && ( ( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_RESIGNATION_REPORT'), session()->get('user_permission')  ) ) ) ) ) ) || ( ( session()->get('user_employee_id') == $recordDetail->employee->i_leader_id  ) && ( $recordDetail->e_initiate_type ==  config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) ) ) )
					<button title="{{ trans('messages.take-action') }}" data-employee-name="{{ (!empty($recordDetail->employee->v_employee_full_name) ? $recordDetail->employee->v_employee_full_name .(!empty($recordDetail->employee->v_employee_code) ? ' - '.$recordDetail->employee->v_employee_code : ''): '' ) }}" onclick="showResignApproveRejectModal(this);" data-record-id="{{ $encodeEmployeeId }}" class="btn bg-color1 text-white mb-1 approve-reject-take-action-button">{{ trans("messages.take-action") }}</button>
				@endif
				@if(  ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_RESIGNATION_REPORT'), session()->get('user_permission')  ) ) ) ) ) && ( $recordDetail->e_status == config('constants.APPROVED_STATUS') ) ) 
					@if( $recordDetail->e_initiate_type ==  config('constants.EMPLOYEE_INITIATE_EXIT_TYPE')  )
						<button title="{{ trans('messages.update') }}" onclick="resignForm(this);" data-joining-date="{{ ( isset($recordDetail->employee->dt_joining_date) ? $recordDetail->employee->dt_joining_date : '' ) }}"  data-record-id="{{ $encodeEmployeeId }}" class="btn bg-color1 text-white">{{ trans("messages.update") }}</button>
					@endif
				@endif
			@endif
		</td>