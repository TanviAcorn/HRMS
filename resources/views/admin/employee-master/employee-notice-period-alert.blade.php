@if( isset($employeeRecordInfo) && (!empty($employeeRecordInfo)) )
<div class="primary-details h-100">
    <div class="card card-display border-0 px-2 h-100">
        <div class="card-body px-2 py-0">
            <div class="row px-0 border-bottom">
                <div class="col-12 p-3">
                    <div class="alert-primary alert-danger alert-card">
                        <div class="row align-items-center">
                        <?php $className = 'col-lg-8' ?>
                        	@if( (isset($employeeRecordInfo->latestResignHistory->e_status)) && ( ( $employeeRecordInfo->i_id  == session()->get('user_employee_id') ) ) && ( in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )  )
                        		<?php 
                        		if( ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ){
                        			$className = 'col-lg-8';
                        		} else {
                        			$className = 'col-lg-12';
                        		}
                        		?>
                        	@endif
                        	@if( (isset($employeeRecordInfo->latestResignHistory->e_status)) && ( ( $employeeRecordInfo->latestResignHistory->e_status  == config('constants.APPROVED_STATUS')  ) ) && ( in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )  )
                        		<?php 
                        		if( ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ){
                        			$className = 'col-lg-8';
                        		} else {
                        			$className = 'col-lg-12';
                        		}
                        		?>
                        	@endif
                        	
                        	@if( ( isset($employeeRecordInfo->latestResignHistory->e_status) && ( $employeeRecordInfo->latestResignHistory->e_status == config('constants.PENDING_STATUS') ) ) && ( ( in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) ) && ( ( session()->get('user_employee_id') ==  $employeeRecordInfo->i_leader_id  ) && ( $employeeRecordInfo->latestResignHistory->e_initiate_type == config("constants.EMPLOYER_INITIATE_EXIT_TYPE") )  ) ) )
                        		<?php 
                        		if( ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ){
                        			$className = 'col-lg-8';
                        		} else {
                        			$className = 'col-lg-12';
                        		}
                        		?>
                        	@endif
                            <div class="{{ $className }}">
                            	<?php // echo "<pre>";print_r($employeeRecordInfo->latestResignHistory)?>
                            	@php
                            	$noticePeriodStartDate = '';
                            	if( isset($employeeRecordInfo->latestResignHistory->e_initiate_type) && ( $employeeRecordInfo->latestResignHistory->e_initiate_type == config("constants.EMPLOYER_INITIATE_EXIT_TYPE") ) ){
                            		$noticePeriodStartDate = ( isset($employeeRecordInfo->latestResignHistory->dt_termination_notice_date) ? convertDateFormat ( $employeeRecordInfo->latestResignHistory->dt_termination_notice_date ) : null) ;
                            	}
                            	
                            	if( isset($employeeRecordInfo->latestResignHistory->e_initiate_type) && ( $employeeRecordInfo->latestResignHistory->e_initiate_type == config("constants.EMPLOYEE_INITIATE_EXIT_TYPE") ) ){
                            		$noticePeriodStartDate = ( isset($employeeRecordInfo->latestResignHistory->dt_employee_notice_date) ? convertDateFormat ( $employeeRecordInfo->latestResignHistory->dt_employee_notice_date ) : null) ;
                            	}
                            	$noticePeriodEndDate = "";
                            	if( isset($employeeRecordInfo->latestResignHistory->e_last_working_day) && ( $employeeRecordInfo->latestResignHistory->e_last_working_day == config("constants.NOTICE_PERIOD") ) ){
                            		$noticePeriodEndDate = ( isset($employeeRecordInfo->latestResignHistory->dt_system_last_working_date) ? convertDateFormat ( $employeeRecordInfo->latestResignHistory->dt_system_last_working_date ) : null) ;
                            	}
                            	
                            	if( isset($employeeRecordInfo->latestResignHistory->e_last_working_day) && ( $employeeRecordInfo->latestResignHistory->e_last_working_day == config("constants.OTHER") ) ){
                            		$noticePeriodEndDate = ( isset($employeeRecordInfo->latestResignHistory->dt_last_working_date) ? convertDateFormat ( $employeeRecordInfo->latestResignHistory->dt_last_working_date ) : null) ;
                            	}
                            	
                            	 
                            	$gender = ( isset($employeeRecordInfo->e_gender) ? ( $employeeRecordInfo->e_gender == config('constants.GENDER_FEMALE') ? 'She' : 'He' ) : '' ); 
                            	
                            	@endphp
                            	<?php // echo "ssss = " . $noticePeriodStartDate;?>
                            	@if( in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) && ( session()->get('user_employee_id') ==  $employeeRecordInfo->i_id ) )
                            		@if( isset($employeeRecordInfo->latestResignHistory->e_status) && ( $employeeRecordInfo->latestResignHistory->e_status == config('constants.APPROVED_STATUS') ) )
                            			@if( $employeeRecordInfo->latestResignHistory->e_initiate_type == config("constants.EMPLOYEE_INITIATE_EXIT_TYPE") )
                            				<p class="mb-0">{{ trans('messages.employee-resign-login-notice-period-info' , [ 'startDate' => $noticePeriodStartDate , 'gender' => $gender , 'endDate' => $noticePeriodEndDate ] ) }}</p>
                            			@else
                            				<p class="mb-0">{{ trans('messages.employee-resign-login-notice-period-info' , [ 'startDate' => $noticePeriodStartDate , 'gender' => $gender , 'endDate' => $noticePeriodEndDate ] ) }}</p>
                            			@endif
                            			
                            		@else
                            			@if(  isset($employeeRecordInfo->latestResignHistory->e_initiate_type) )
	                            			@if(   $employeeRecordInfo->latestResignHistory->e_initiate_type == config("constants.EMPLOYEE_INITIATE_EXIT_TYPE") )
	                            				<p class="mb-0">{{ trans('messages.resignation-under-review-msg') }}</p>
	                            			@else
	                            				<p class="mb-0">{{ trans('messages.termination-under-review-msg') }}</p>	
	                            			@endif
                            			@endif
                            		
                            		@endif
                            		
                            	@else
                            		<p class="mb-0">{{ trans('messages.employee-notice-period-info' , [ 'startDate' => $noticePeriodStartDate , 'gender' => $gender , 'endDate' => $noticePeriodEndDate ] ) }}</p>
                            	@endif  
                            	
                            </div>
                            <div class="alert-btn col-lg-4 mt-xl-0 mt-3 take-actions-btn">
                               
                                @if( ( isset($employeeRecordInfo->latestResignHistory->e_status) && ( $employeeRecordInfo->latestResignHistory->e_status == config('constants.PENDING_STATUS') ) ) && ( ( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ) || ( ( session()->get('user_employee_id') ==  $employeeRecordInfo->i_leader_id  ) && ( $employeeRecordInfo->latestResignHistory->e_initiate_type == config("constants.EMPLOYEE_INITIATE_EXIT_TYPE") )  ) ) )
                                <button title="{{ trans('messages.take-action') }}" onclick="showResignApproveRejectModal(this);" data-record-id="{{ Wild_tiger::encode($employeeRecordInfo->i_id) }}" class="btn bg-color1 text-white mr-1 approve-reject-take-action-button">
                                    {{ trans("messages.take-action") }}
                                </button>
                                @endif
                                
                                @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ))
	                                @if( isset($employeeRecordInfo->latestResignHistory->e_initiate_type) && ( $employeeRecordInfo->latestResignHistory->e_initiate_type == config("constants.EMPLOYER_INITIATE_EXIT_TYPE") ) )
	                                <button title="{{ trans('messages.update') }}" onclick="initiateExitForm(this);" data-record-id="{{ Wild_tiger::encode($employeeRecordInfo->i_id) }}" class="btn bg-color1 text-white mr-2">
	                                    {{ trans("messages.update") }}
	                                </button>
	                                @endif
	                                
	                                @if( isset($employeeRecordInfo->latestResignHistory->e_initiate_type) && ( $employeeRecordInfo->latestResignHistory->e_initiate_type == config("constants.EMPLOYEE_INITIATE_EXIT_TYPE") ) )
	                                <button title="{{ trans('messages.update') }}" onclick="resignForm(this);" data-joining-date="{{ ( isset($employeeRecordInfo->dt_joining_date) ? $employeeRecordInfo->dt_joining_date : '' ) }}" data-record-id="{{ Wild_tiger::encode($employeeRecordInfo->i_id) }}" class="btn bg-color1 text-white mr-2">
	                                    {{ trans("messages.update") }}
	                                </button>
	                                @endif
                                @endif
                                
                                @if( ( ( isset($employeeRecordInfo->latestResignHistory->e_initiate_type) && ( $employeeRecordInfo->latestResignHistory->e_initiate_type == config("constants.EMPLOYEE_INITIATE_EXIT_TYPE") ) ) ) && ( ( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ) ) )
                                <a title="{{ trans('messages.cancel-resignation') }}" onclick="cancelResign(this);" data-record-type="{{ ( isset($employeeRecordInfo->latestResignHistory->e_initiate_type) ? $employeeRecordInfo->latestResignHistory->e_initiate_type : '' ) }}" data-record-id="{{  ( isset($employeeRecordInfo->latestResignHistory->i_id)  ? Wild_tiger::encode($employeeRecordInfo->latestResignHistory->i_id) : '' )}}" href="javascript:void(0);" class="btn btn-outline-secondary ml-1" data-toggle="dropdown" aria-expanded="false">
                                    {{ trans("messages.cancel-resignation") }}
                                </a>
                                @endif
                                
                                @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) && ( ( isset($employeeRecordInfo->latestResignHistory->e_initiate_type) && ( $employeeRecordInfo->latestResignHistory->e_initiate_type == config("constants.EMPLOYER_INITIATE_EXIT_TYPE") ) ) ) )
	                                @if( isset($employeeRecordInfo->latestResignHistory->e_status) && ( $employeeRecordInfo->latestResignHistory->e_status == config('constants.APPROVED_STATUS') ) )
	                                	<a title="{{ trans('messages.cancel-termination') }}" onclick="cancelResign(this);" data-record-type="{{ ( isset($employeeRecordInfo->latestResignHistory->e_initiate_type) ? $employeeRecordInfo->latestResignHistory->e_initiate_type : '' ) }}" data-record-id="{{  ( isset($employeeRecordInfo->latestResignHistory->i_id)  ? Wild_tiger::encode($employeeRecordInfo->latestResignHistory->i_id) : '' )}}" href="javascript:void(0);" class="btn btn-outline-secondary" data-toggle="dropdown" aria-expanded="false">
	                                    	{{ trans("messages.cancel-termination") }}
	                                	</a>
	                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif