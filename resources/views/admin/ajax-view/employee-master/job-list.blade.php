						@php $encodeEmployeeId = Wild_tiger::encode($employeeRecordInfo->i_id) @endphp
						<div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.employee-code') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->v_employee_code) ? $employeeRecordInfo->v_employee_code :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.joining-date') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->dt_joining_date) ? convertDateFormat($employeeRecordInfo->dt_joining_date) :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.designation") }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->designationInfo->v_value) ? $employeeRecordInfo->designationInfo->v_value :'') }}
                                @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )  
                                <a title="{{ trans('messages.edit') }}" href="javascript:void(0);" data-last-designation-date="{{ $employeeRecordInfo->dt_last_update_designation }}" onclick="editJobDesignation(this);" data-record-id="{{ $encodeEmployeeId }}"  class="btn btn-sm mb-1 btn-edit btn-edit-history btn-color-text"><i class="fas fa-pencil-alt"></i></a>
                                @endif
                                @if( isset($employeeRecordInfo->dt_joining_date) &&  isset($employeeRecordInfo->dt_last_update_designation) &&  ( strtotime( $employeeRecordInfo->dt_joining_date ) != strtotime($employeeRecordInfo->dt_last_update_designation) ) )
                                <a title="{{ trans('messages.history') }}" href="javascript:void(0);" data-record-type="{{ config('constants.DESIGNATION_LOOKUP') }}" onclick="getDesignationHistory(this);" data-record-id="{{ $encodeEmployeeId }}" class="btn btn-sm mb-1 btn-edit btn-edit-history btn-color-text"><i class="fas fa-history"></i></a>
                                @endif
                            </p>

                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('sub-designation') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->subDesignationInfo->v_sub_designation_name) ? $employeeRecordInfo->subDesignationInfo->v_sub_designation_name :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.team") }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->teamInfo->v_value) ? $employeeRecordInfo->teamInfo->v_value :'') }}
                            	@if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )  
                                <a title="{{ trans('messages.edit') }}" href="javascript:void(0);" data-last-team-date="{{ $employeeRecordInfo->dt_last_update_team }}" onclick="editTeam(this);" data-record-id="{{ $encodeEmployeeId }}" class="btn btn-sm mb-1 btn-edit btn-edit-history btn-color-text"><i class="fas fa-pencil-alt"></i></a>
                                @endif
                                @if( isset($employeeRecordInfo->dt_joining_date) &&  isset($employeeRecordInfo->dt_last_update_team ) &&  ( strtotime( $employeeRecordInfo->dt_joining_date ) != strtotime($employeeRecordInfo->dt_last_update_team ) ) )
                                <a title="{{ trans('messages.history') }}" href="javascript:void(0);" data-record-type="{{ config('constants.TEAM_LOOKUP') }}" onclick="getDesignationHistory(this);" data-record-id="{{ $encodeEmployeeId }}"  class="btn btn-sm mb-1 btn-edit btn-edit-history btn-color-text"><i class="fas fa-history"></i></a>
                                @endif
                            </p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.leader-name-reporting-manager") }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->leaderInfo->v_employee_full_name) ? $employeeRecordInfo->leaderInfo->v_employee_full_name :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.shift') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->shiftInfo->v_shift_name) ? $employeeRecordInfo->shiftInfo->v_shift_name :'') }}
                                @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )  
                                <a title="{{ trans('messages.edit') }}" href="javascript:void(0);" data-last-shift-date="{{ $employeeRecordInfo->dt_last_update_shift }}" onclick="editShift(this);" data-record-id="{{ $encodeEmployeeId }}"  class="btn btn-sm mb-1 btn-edit btn-edit-history btn-color-text"><i class="fas fa-pencil-alt"></i></a>
                                @endif
                                <a title="{{ trans('messages.history') }}" href="javascript:void(0);" data-record-type="{{ config('constants.SHIFT_RECORD_TYPE') }}" onclick="getDesignationHistory(this);" data-record-id="{{ $encodeEmployeeId }}" class="btn btn-sm mb-1 btn-edit btn-edit-history btn-color-text"><i class="fas fa-history"></i></a>
                                @if( isset($employeeRecordInfo->dt_joining_date) &&  isset($employeeRecordInfo->dt_last_update_shift) &&  ( strtotime( $employeeRecordInfo->dt_joining_date ) != strtotime($employeeRecordInfo->dt_last_update_shift) ) ) 
                                
                                @endif
                            </p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.weekly-off') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->weekOffInfo->v_weekly_off_name) ? $employeeRecordInfo->weekOffInfo->v_weekly_off_name :'') }}
                            	@if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )  
                                <a title="{{ trans('messages.edit') }}" href="javascript:void(0);"  data-last-weekly-off-date="{{ $employeeRecordInfo->dt_last_update_week_off }}" onclick="editWeekOff(this);" data-record-id="{{ $encodeEmployeeId }}" class="btn btn-sm mb-1 btn-edit btn-edit-history btn-color-text"><i class="fas fa-pencil-alt"></i></a>
                                @endif
                                @if( isset($employeeRecordInfo->dt_week_off_effective_date) &&  isset($employeeRecordInfo->dt_last_update_week_off) &&  ( strtotime( $employeeRecordInfo->dt_week_off_effective_date ) != strtotime($employeeRecordInfo->dt_last_update_week_off) ) )
                                <a title="{{ trans('messages.history') }}" href="javascript:void(0);" data-record-type="{{ config('constants.WEEK_OFF_RECORD_TYPE') }}" onclick="getDesignationHistory(this);" data-record-id="{{ $encodeEmployeeId }}" class="btn btn-sm mb-1 btn-edit btn-edit-history btn-color-text"><i class="fas fa-history"></i></a>
                                @endif
                            </p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.week-off-effective-date') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->dt_week_off_effective_date) ? convertDateFormat($employeeRecordInfo->dt_week_off_effective_date) :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.in-probation-question') }}</h5>
                            <p class="details-text">
                            	{{ $employeeRecordInfo->e_in_probation }}  <?php echo ( ( $employeeRecordInfo->e_in_probation == config('constants.SELECTION_YES') ) ? ' - '. convertDateFormat($employeeRecordInfo->dt_joining_date) . (!empty($employeeRecordInfo->dt_probation_end_date) ? ' - '. convertDateFormat($employeeRecordInfo->dt_probation_end_date) : '' ) : '' ) ?>
                                @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )  
                                	@if( $employeeRecordInfo->t_is_probation_completed == 0 ) 
	                                	<a title="{{ trans('messages.edit') }}" href="javascript:void(0);" data-joining-date="{{ $employeeRecordInfo->dt_joining_date }}" onclick="editProbation(this);" data-record-id="{{ $encodeEmployeeId }}" class="btn btn-sm mb-1 btn-edit btn-edit-history btn-color-text"><i class="fas fa-pencil-alt"></i></a>
    						         @endif                   
                                @endif
                                
                                <?php if( isset($employeeRecordInfo->employeeProbationHistory) && (count($employeeRecordInfo->employeeProbationHistory) > 0 ) ) { ?>
                                	<a title="{{ trans('messages.history') }}" href="javascript:void(0);" data-toggle="modal" data-target="#in_probation_question_history" onclick="showProbationPolicy(this);" data-record-id="{{ $encodeEmployeeId }}" class="btn btn-sm mb-1 btn-edit btn-edit-history btn-color-text"><i class="fas fa-history"></i></a>
                                <?php } ?>
                            </p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.notice-period') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->noticePeriodInfo->v_probation_period_duration) ? $employeeRecordInfo->noticePeriodInfo->v_probation_period_duration . ( (!empty($employeeRecordInfo->noticePeriodInfo->e_months_weeks_days) ? ' ' .$employeeRecordInfo->noticePeriodInfo->e_months_weeks_days : '' ) ) :'') }}</p>
                        </div>
                        @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )
	                        @if( isset($employeeRecordInfo->recruitmentSourceInfo->v_value) && (!empty($employeeRecordInfo->recruitmentSourceInfo->v_value)) )
	                        <div class="col-sm-6 profile-display-item">
	                            <h5 class="details-title">{{ trans('messages.recruitment-source') }}</h5>
	                            <p class="details-text">
	                                {{ $employeeRecordInfo->recruitmentSourceInfo->v_value }} 
	                                @if( isset($employeeRecordInfo->i_recruitment_source_id) && ( $employeeRecordInfo->i_recruitment_source_id == config('constants.EMPLOYEE_RECRUITMENT_SOURCE_ID')) && (  isset($employeeRecordInfo->i_reference_emp_id) ) && (!empty($employeeRecordInfo->i_reference_emp_id)) && ( isset( $employeeRecordInfo->employeeInfo->v_employee_full_name ) ) )
	                                	{{ ' - ' . $employeeRecordInfo->employeeInfo->v_employee_full_name . ' - ' . $employeeRecordInfo->employeeInfo->v_employee_code }}
	                                @endif
	                            </p>
	                        </div>
	                        @endif
                        @endif



