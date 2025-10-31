				
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.employee-name') }}</h5>
                            <p class="details-text employee-name-record">{{ (!empty($employeeRecordInfo->v_employee_name) ? $employeeRecordInfo->v_employee_name :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.full-name') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->v_employee_full_name) ? $employeeRecordInfo->v_employee_full_name :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.gender") }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->e_gender) ? $employeeRecordInfo->e_gender :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.blood-group") }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->v_blood_group) ? $employeeRecordInfo->v_blood_group :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.education") }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->v_education) ? $employeeRecordInfo->v_education :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.cgpa-percentage") }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->v_cgpa) ? $employeeRecordInfo->v_cgpa :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.marital-status") }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->e_marital_status) ? $employeeRecordInfo->e_marital_status :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.date-of-birth') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->dt_birth_date) ? convertDateFormat($employeeRecordInfo->dt_birth_date) :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.employment-status") }}</h5>
                             <p class="details-text">
                            	{{ isset($employeeRecordInfo->e_employment_status)  ? $employeeRecordInfo->e_employment_status : '' }}
                            	@if( ( isset($employeeRecordInfo->e_employment_status) && ( $employeeRecordInfo->e_employment_status == config('constants.PROBATION_EMPLOYMENT_STATUS') ) ) )
                            	{{  ( isset( $employeeRecordInfo->probationPeriodInfo->v_probation_period_duration ) ? ' - '. $employeeRecordInfo->probationPeriodInfo->v_probation_period_duration . ( isset($employeeRecordInfo->probationPeriodInfo->e_months_weeks_days) ? ' ' .$employeeRecordInfo->probationPeriodInfo->e_months_weeks_days : ''  ) : '' ) }}
                            	@endif
                            	@if( ( isset($employeeRecordInfo->e_employment_status) && ( $employeeRecordInfo->e_employment_status == config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS') ) ) )
                            	{{  ( isset( $employeeRecordInfo->noticePeriodInfo->v_probation_period_duration ) ? ' - '. $employeeRecordInfo->noticePeriodInfo->v_probation_period_duration . ( isset($employeeRecordInfo->noticePeriodInfo->e_months_weeks_days) ? ' ' .$employeeRecordInfo->noticePeriodInfo->e_months_weeks_days : ''  ) : '' ) }}
                            	@endif
                            	
                            </p>
                        </div>
                         @if(  isset($employeeRecordInfo->t_is_suspended) && ( $employeeRecordInfo->t_is_suspended == 1 ) && ( (!empty($employeeRecordInfo->dt_suspended_start_date)) ) && (!empty($employeeRecordInfo->dt_suspended_end_date)) )	
	                         <div class="col-sm-6 profile-display-item">
	                             <p class="details-text">{{ trans("messages.suspended")}} <br>{{ convertDateFormat($employeeRecordInfo->dt_suspended_start_date) }} - {{ convertDateFormat($employeeRecordInfo->dt_suspended_end_date) }}
	                            	@if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )
	                            	<a title="{{ trans('messages.edit') }}" href="javascript:void(0);" data-joining-date="{{ $employeeRecordInfo->dt_joining_date }}" onclick="openSuspendModel(this);" data-employee-id="{{ Wild_tiger::encode( $employeeRecordInfo->i_id ) }}"   data-record-id="{{ Wild_tiger::encode( $employeeRecordInfo->i_last_suspend_record_id ) }}"   class="btn btn-sm mb-1 btn-edit btn-edit-history btn-color-text">
	                            		<i class="fas fa-pencil-alt"></i>
	                            	</a>
	                            	@endif
	                            </p>
	                        </div>
                        @endif
                        
                        @if( isset($employeeRecordInfo->employeeSuspendHistory) && ( count($employeeRecordInfo->employeeSuspendHistory) > 0 ) )
                            <div class="col-sm-6 profile-display-item">
                            	 <p class="details-text">{{ trans("messages.suspension-history")}}
                            	<a title="{{ trans('messages.history') }}" href="javascript:void(0);" data-employee-id="{{ Wild_tiger::encode( $employeeRecordInfo->i_id ) }}"  onclick="openSuspendHistoryModel(this);" class="btn btn-sm mb-1 btn-edit btn-edit-history btn-color-text"><i class="fas fa-history"></i></a>
                            	</p>
                            </div>
						@endif
                    