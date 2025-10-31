<?php
	$encodeEmployeeRecordId = ( isset($recordDetail->employeeInfo->i_id) ?  Wild_tiger::encode($recordDetail->employeeInfo->i_id) : '' );
	$encodeLeaveRecordId = Wild_tiger::encode($recordDetail->i_id);
	$lastSalaryMonth = lastAllowedDate($recordDetail->employeeInfo);
	$allowedEditReject = ( ( empty($lastSalaryMonth) || ( strtotime($lastSalaryMonth) <= strtotime($recordDetail->dt_leave_from_date) ) ) ?  true : false );
	
	?>
		<td class="text-center sr-col" <?php echo "date  = " . $lastSalaryMonth  ?> >{{ $rowIndex }}</td>
         	<td>
	         	<?php if((!empty($recordDetail->employeeInfo->v_employee_full_name))){ ?>
	         		<?php /* ?>
	         		@if( ( session()->get('is_supervisor') == false ) && ( $recordDetail->i_employee_id == session()->get('user_employee_id')) )
	         			{{ (!empty($recordDetail->employeeInfo->v_employee_full_name) ? $recordDetail->employeeInfo->v_employee_full_name . ( isset($recordDetail->employeeInfo->v_employee_code) ? ' ('.$recordDetail->employeeInfo->v_employee_code . ')' : '' ) :'') }} <br>
	         			{{ (!empty($recordDetail->employeeInfo->v_contact_no) ? $recordDetail->employeeInfo->v_contact_no :'') }}
	         		@else
	         			
	         		@endif
	         		<?php */ ?>
	         		<a href="{{route('employee-master.profile', $encodeEmployeeRecordId )}}" target="_blank"> {{ (!empty($recordDetail->employeeInfo->v_employee_full_name) ? $recordDetail->employeeInfo->v_employee_full_name . ( isset($recordDetail->employeeInfo->v_employee_code) ? ' ('.$recordDetail->employeeInfo->v_employee_code . ')' : '' ) :'') }}</a><br><span class="text-muted"> {{ (!empty($recordDetail->employeeInfo->v_contact_no) ? $recordDetail->employeeInfo->v_contact_no :'') }}</span>
	         		
	         	<?php } ?>
         	</td>
       		<td>{{ ( isset($recordDetail->employeeInfo->teamInfo->v_value) && !empty($recordDetail->employeeInfo->teamInfo->v_value) ? $recordDetail->employeeInfo->teamInfo->v_value : '' ) }}</td>
       		<td>
       			@if((isset($recordDetail->dt_leave_from_date)) && (isset($recordDetail->dt_leave_to_date)) && ($recordDetail->dt_leave_to_date == $recordDetail->dt_leave_from_date) )
       				{{ (!empty($recordDetail->dt_leave_from_date) ? convertDateFormat($recordDetail->dt_leave_from_date,'d.m.Y') .(isset($recordDetail->e_duration) ? ' ('.$recordDetail->e_duration .')' : '' ) : '')}}<br><span class="text-muted">{{ (!empty($recordDetail->d_no_days) ? $recordDetail->d_no_days .' '. config("constants.DAYS_DURATION") :'') }}</span>
       			@else
       				{{ (!empty($recordDetail->dt_leave_from_date) ? convertDateFormat($recordDetail->dt_leave_from_date,'d.m.Y') . (!empty($recordDetail->e_from_duration) ?  ' (' .$recordDetail->e_from_duration .')' : '' ): '')}} {{(!empty($recordDetail->dt_leave_to_date) ? ' - ' .convertDateFormat($recordDetail->dt_leave_to_date,'d.m.Y') . (!empty($recordDetail->e_to_duration) ?  ' (' .$recordDetail->e_to_duration .')' : '' ).(!empty($recordDetail->e_duration) ?  ' (' .$recordDetail->e_duration .')' : '' ): '')}}<br><span class="text-muted">{{ (!empty($recordDetail->d_no_days) ? $recordDetail->d_no_days .' '. config("constants.DAYS_DURATION") :'') }}</span>
       			@endif
       		</td>
        	<td>{{ (!empty($recordDetail->leaveTypeInfo->v_leave_type_name) ? $recordDetail->leaveTypeInfo->v_leave_type_name :'') }} <br> <span class="text-muted">{{(!empty($recordDetail->dt_created_at) ? convertDateFormat($recordDetail->dt_created_at,'d.m.Y') :'') }}</span></td>
        	
        		<td>{{ (!empty($recordDetail->createdInfo->v_name) ? $recordDetail->createdInfo->v_name : '') }}</td>
        	
			<td>{{ (!empty($recordDetail->e_status) ? $recordDetail->e_status :'') }} <br> {{ (!empty($recordDetail->approvedByInfo->v_name) ? $recordDetail->approvedByInfo->v_name : (  $recordDetail->t_is_auto_approve == 1  ? trans('messages.auto-approved') : '' ) ) }} </td>
        	<td>{{ (!empty($recordDetail->dt_approved_at) ? convertDateFormat($recordDetail->dt_approved_at,'d.m.Y') : '' ) }}<br><span class="text-muted">{{ (!empty($recordDetail->dt_approved_at) ? clientTime($recordDetail->dt_approved_at) : '' ) }}</span></td>
         	<td>
         		<div class="download-link-items d-flex justify-content-center flex-wrap">
         		@if( ( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) ) ) || ( ( isset($recordDetail->employeeInfo->leaderInfo->i_login_id) && ( $recordDetail->employeeInfo->leaderInfo->i_login_id == session()->get('user_id') ) ) ) )
              		@if( (isset($recordDetail->e_status)) && ( ($recordDetail->e_status == config('constants.PENDING_STATUS') ) )  )
              		<button type="button" data-employee-id="{{ $encodeEmployeeRecordId }}" data-status="{{config('constants.APPROVED_STATUS')}}" data-leave-id="{{ $encodeLeaveRecordId }}" onclick="openApproveModel(this)" class="btn btn btn-theme text-white border btn-sm d-sm-flex align-items-center  manage-doc-btn upload-btn" title="{{ trans('messages.approve-leave') }}"><i class="fa fa-solid fa-check"></i></button>
                	@if( $allowedEditReject != false )
                	<button type="button" data-employee-id="{{ $encodeEmployeeRecordId }}" data-status="{{config('constants.REJECTED_STATUS')}}" data-leave-id="{{ $encodeLeaveRecordId }}" onclick="openApproveModel(this)" class="btn btn btn-theme text-white border btn-sm d-sm-flex align-items-center  manage-doc-btn" title="{{ trans('messages.reject-leave') }}"><i class="fa fa-fw fa-times"></i></button>
                	@endif
                	@endif
                @endif
                	<button type="button" data-employee-id="{{ $encodeEmployeeRecordId }}" data-status="{{ config('constants.VIEW_RECORD') }}" data-leave-id="{{ $encodeLeaveRecordId }}" onclick="openApproveModel(this)" class="btn btn btn-theme text-white border btn-sm  manage-doc-btn  d-sm-flex align-items-center"  title="{{ trans('messages.view') }}">{{ trans("messages.view") }} </button>
                	@if( $allowedEditReject != false )
	                	@if( ( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) ) ) || ( ( session()->get('role') == config('constants.ROLE_USER') ) && ( $recordDetail->employeeInfo->i_login_id ==  session()->get('user_id') ) ) )
	                		@if( (isset($recordDetail->e_status)) && ( ($recordDetail->e_status == config('constants.PENDING_STATUS') ) )  )
	                			<button type="button" data-employee-id="{{ $encodeEmployeeRecordId }}" data-status="{{config('constants.CANCELLED_STATUS')}}" data-leave-id="{{ $encodeLeaveRecordId }}" onclick="openApproveModel(this)" class=" manage-doc-btn btn btn btn-theme bg-warning  text-white border btn-sm d-sm-flex align-items-center manage-doc-btn upload-btn"  title="{{ trans('messages.cancel-leave') }}"><i class="fa fa-solid fa-ban"></i></button>
	                		@endif
	                	@endif
	                	
	                	@if( ( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) ) ) || ( ( session()->get('role') == config('constants.ROLE_USER') ) && (isset($recordDetail->employeeInfo->leaderInfo->i_login_id))  && ( $recordDetail->employeeInfo->leaderInfo->i_login_id ==  session()->get('user_id') ) ) )
	                		@if( (isset($recordDetail->e_status)) && ( ( $recordDetail->e_status == config('constants.APPROVED_STATUS') ) )  )
	                			<button type="button" data-employee-id="{{ $encodeEmployeeRecordId }}" data-status="{{config('constants.CANCELLED_STATUS')}}" data-leave-id="{{ $encodeLeaveRecordId }}" onclick="openApproveModel(this)" class=" manage-doc-btn btn btn btn-theme bg-warning  text-white border btn-sm d-sm-flex align-items-center manage-doc-btn upload-btn"  title="{{ trans('messages.cancel-leave') }}"><i class="fa fa-solid fa-ban"></i></button>
	                		@endif
	                	@endif
                	@endif
           		</div>
 			</td>