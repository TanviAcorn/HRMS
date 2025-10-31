<?php
$encodeEmployeeRecordId = ( isset($recordDetail->employeeInfo->i_id)  ? Wild_tiger::encode($recordDetail->employeeInfo->i_id) : '' ) ;
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
$fromTime[] = (!empty($recordDetail->t_from_time) ? $recordDetail->t_from_time :'');
$toTime[] = (!empty($recordDetail->t_to_time) ? $recordDetail->t_to_time :'');
$totalHours = array_diff($fromTime, $toTime);
$diffBetweenTimes = differenceTimeAndHours( $recordDetail->t_from_time , $recordDetail->t_to_time );
$diffBetweenBackTimes = differenceTimeAndHours( $recordDetail->t_from_back_time , $recordDetail->t_to_back_time );
	
?>
			<td class="text-center sr-col">{{ $rowIndex }}</td>
 			<td>
	         	<?php if((!empty($recordDetail->employeeInfo->v_employee_full_name))){ ?>
	         		<?php  /* ?>
	         		@if( ( session()->get('is_supervisor') == false ) && ( $recordDetail->i_employee_id == session()->get('user_employee_id')) )
	         			{{ (!empty($recordDetail->employeeInfo->v_employee_full_name) ? $recordDetail->employeeInfo->v_employee_full_name . ( isset($recordDetail->employeeInfo->v_employee_code) ? ' ('.$recordDetail->employeeInfo->v_employee_code . ')' : '' ) :'') }} <br>
         				{{ (!empty($recordDetail->employeeInfo->v_contact_no) ? $recordDetail->employeeInfo->v_contact_no :'') }}
	         		@else
	         			<a href="{{route('employee-master.profile', $encodeEmployeeRecordId )}}" target="_blank"> {{ (!empty($recordDetail->employeeInfo->v_employee_full_name) ? $recordDetail->employeeInfo->v_employee_full_name . ( isset($recordDetail->employeeInfo->v_employee_code) ? ' ('.$recordDetail->employeeInfo->v_employee_code . ')' : '' ) :'') }}</a><br><span class="text-muted"> {{ (!empty($recordDetail->employeeInfo->v_contact_no) ? $recordDetail->employeeInfo->v_contact_no :'') }}</span>
	         		@endif
	         		<?php */ ?>
	         		<a href="{{route('employee-master.profile', $encodeEmployeeRecordId )}}" target="_blank"> {{ (!empty($recordDetail->employeeInfo->v_employee_full_name) ? $recordDetail->employeeInfo->v_employee_full_name . ( isset($recordDetail->employeeInfo->v_employee_code) ? ' ('.$recordDetail->employeeInfo->v_employee_code . ')' : '' ) :'') }}</a><br><span class="text-muted"> {{ (!empty($recordDetail->employeeInfo->v_contact_no) ? $recordDetail->employeeInfo->v_contact_no :'') }}</span>
	         		<?php } ?>
         	</td>
         	<td>{{ ( isset($recordDetail->employeeInfo->teamInfo->v_value) && !empty($recordDetail->employeeInfo->teamInfo->v_value) ? $recordDetail->employeeInfo->teamInfo->v_value : '' ) }}</td>
         	<td>{{ (!empty($recordDetail->dt_time_off_date) ? convertDateFormat($recordDetail->dt_time_off_date,'d.m.Y') :'') }} <br>{{ (!empty($recordDetail->t_from_time) ? clientTime($recordDetail->t_from_time) .(!empty($recordDetail->t_to_time) ? ' - ' .clientTime($recordDetail->t_to_time) :'' ) : '' ) }}<br> {{ (!empty($diffBetweenTimes) ? ($diffBetweenTimes) : '' ) }}</td>
         	<td>{{ (!empty($recordDetail->dt_time_off_back_date) ? convertDateFormat($recordDetail->dt_time_off_back_date,'d.m.Y') :'') }} <br>{{ (!empty($recordDetail->t_from_back_time) ? clientTime($recordDetail->t_from_back_time) .(!empty($recordDetail->t_to_back_time) ? ' - ' .clientTime($recordDetail->t_to_back_time) :'' ) : '' ) }}<br> {{ (!empty($diffBetweenBackTimes) ? ($diffBetweenBackTimes) : '' ) }}</td>
            <td>{{ (!empty($recordDetail->e_record_type) ? $recordDetail->e_record_type :'') }}</td>
            <td>{{(!empty($recordDetail->dt_created_at) ? convertDateFormat($recordDetail->dt_created_at,'d.m.Y') :'') }}</td>
            <td>{{ (!empty($recordDetail->e_status) ? $recordDetail->e_status :'') }} <br> {{ (!empty($recordDetail->approvedByInfo->v_name) ? $recordDetail->approvedByInfo->v_name :'') }}</td>
            
            	<td>{{ (!empty($recordDetail->createdInfo->v_name) ? $recordDetail->createdInfo->v_name : '') }}</td>
            
            <td>{{ (!empty($recordDetail->dt_approved_at) ? convertDateFormat($recordDetail->dt_approved_at,'d.m.Y') : '' ) }}<br> {{ (!empty($recordDetail->dt_approved_at) ? clientTime($recordDetail->dt_approved_at) : '' ) }}</td>
            <td>
             	<div class="download-link-items d-flex justify-content-center flex-wrap">
              		@if( ( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ) || ( ( isset($recordDetail->employeeInfo->leaderInfo->i_login_id) && ( $recordDetail->employeeInfo->leaderInfo->i_login_id == session()->get('user_id') ) ) ) )
	              		@if( (isset($recordDetail->e_status)) && ( ($recordDetail->e_status == config('constants.PENDING_STATUS') ) )  )
	              		<button type="button" data-employee-id="{{ $encodeEmployeeRecordId }}" data-status="{{config('constants.APPROVED_STATUS')}}" data-time-off-id="{{ $encodeRecordId }}" onclick="openTimeOffReportModel(this)" class="btn btn btn-theme text-white border btn-sm d-sm-flex align-items-center  manage-doc-btn upload-btn" title="{{ trans('messages.approve') }}"><i class="fa fa-solid fa-check"></i></button>
	                	<button type="button" data-employee-id="{{ $encodeEmployeeRecordId }}" data-status="{{config('constants.REJECTED_STATUS')}}" data-time-off-id="{{ $encodeRecordId }}" onclick="openTimeOffReportModel(this)" class="btn btn btn-theme text-white border btn-sm d-sm-flex align-items-center  manage-doc-btn" title="{{ trans('messages.reject') }}"><i class="fa fa-fw fa-times"></i></button>
	                	@endif
                	@endif
                	<button type="button" data-employee-id="{{ $encodeEmployeeRecordId }}" data-status="{{ config('constants.VIEW_RECORD') }}" data-time-off-id="{{ $encodeRecordId }}" onclick="openTimeOffReportModel(this)" class="btn btn btn-theme text-white border btn-sm  manage-doc-btn  d-sm-flex align-items-center"  title="{{ trans('messages.view') }}">{{ trans("messages.view") }} </button>
                	@if( ( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ) || ( ( session()->get('role') == config('constants.ROLE_USER') ) && ( $recordDetail->employeeInfo->i_login_id ==  session()->get('user_id') ) ) )
	                	@if( (isset($recordDetail->e_status)) && ( ($recordDetail->e_status == config('constants.PENDING_STATUS') ) )  )
	                	<button type="button" data-employee-id="{{ $encodeEmployeeRecordId }}" data-status="{{config('constants.CANCELLED_STATUS')}}" data-time-off-id="{{ $encodeRecordId }}" onclick="openTimeOffReportModel(this)" class=" manage-doc-btn btn btn btn-theme bg-warning  text-white border btn-sm d-sm-flex align-items-center manage-doc-btn upload-btn"  title="{{ trans('messages.cancel-time-off') }}"><i class="fa fa-solid fa-ban"></i></button>
	                	@endif
                	@endif
                	
                	@if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) || ( ( session()->get('role') == config('constants.ROLE_USER') ) && (isset($recordDetail->employeeInfo->leaderInfo->i_login_id)) && ( $recordDetail->employeeInfo->leaderInfo->i_login_id ==  session()->get('user_id') ) ) )
                		@if( (isset($recordDetail->e_status)) && ( ( $recordDetail->e_status == config('constants.APPROVED_STATUS') ) )  )
                			<button type="button" data-employee-id="{{ $encodeEmployeeRecordId }}" data-status="{{config('constants.CANCELLED_STATUS')}}" data-time-off-id="{{ $encodeRecordId }}" onclick="openTimeOffReportModel(this)" class=" manage-doc-btn btn btn btn-theme bg-warning  text-white border btn-sm d-sm-flex align-items-center manage-doc-btn upload-btn"  title="{{ trans('messages.cancel-time-off') }}"><i class="fa fa-solid fa-ban"></i></button>
                		@endif
                	@endif
           		</div>
       		</td>       