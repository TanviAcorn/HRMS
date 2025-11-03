<div class="profile-header d-flex">
            <?php
            $profileFileName = '' ;
            if(!empty( $employeeRecordInfo->v_profile_pic ) && file_exists( config('constants.FILE_STORAGE_PATH') . config('constants.UPLOAD_FOLDER') . $employeeRecordInfo->v_profile_pic ) ){
                $profileFileName =  config('constants.FILE_STORAGE_PATH_URL') .  config('constants.UPLOAD_FOLDER') .  $employeeRecordInfo->v_profile_pic;
            }
            ?>
                @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) || ( session()->get('user_employee_id') == $employeeRecordInfo->i_id ) )
                <a href="javascript:void(0);" class="employee-image d-lg-block d-none position-relative" onclick="uploadProfilePic(this)" data-emp-id="{{ Wild_tiger::encode( $employeeRecordInfo->i_id ) }}">
                   @if(!empty($profileFileName))
                        <img src="{{ $profileFileName }}" alt="icon" class="profile-img">
                        <img src="{{  asset ('images/camera-bg.png') }}" alt="icon" class="camera-icon">
                   @else
                        <div class="default-profile">
                            <img src="{{  asset ('images/camera-bg.png') }}" alt="icon" class="camera-icon">{{ getInitialLetter($employeeRecordInfo->v_employee_full_name)  }}
                        </div>
                   @endif
                </a>
                @else
                <a href="javascript:void(0);" class="employee-image d-lg-block d-none position-relative" data-emp-id="{{ Wild_tiger::encode( $employeeRecordInfo->i_id ) }}">
                   @if(!empty($profileFileName))
                        <img src="{{ $profileFileName }}" alt="icon" class="profile-img">
                        <img src="{{  asset ('images/camera-bg.png') }}" alt="icon" class="camera-icon">
                   @else
                        <div class="default-profile">
                            <img src="{{  asset ('images/camera-bg.png') }}" alt="icon" class="camera-icon">{{ getInitialLetter($employeeRecordInfo->v_employee_full_name)  }}
                        </div>
                   @endif
                </a>
                @endif
                <div class="employee-card w-100">
                    <div class="card card-body pb-0 border-0">
                        <div class="employee-profile-view">
                            <div class="row d-flex border-bottom pb-2">
                                <div class="col-md-2 col-sm-3 pr-0 d-lg-none d-initial  mobile-employee-image">
                                    <a href="javascript:void(0);" onclick="uploadProfilePic(this)" data-emp-id="{{ Wild_tiger::encode( $employeeRecordInfo->i_id ) }}" class="employee-image d-lg-none d-initial position-relative">
                                        @if(!empty($profileFileName))
                                            <img src="{{ $profileFileName }}" alt="icon" class="profile-img">
                                            <img src="{{  asset ('images/camera-bg.png') }}" alt="icon" class="camera-icon">
                                       @else
                                            <div class="default-profile">
                                                <img src="{{  asset ('images/camera-bg.png') }}" alt="icon" class="camera-icon">{{ getInitialLetter($employeeRecordInfo->v_employee_full_name)  }}
                                            </div>
                                       @endif
                                    </a>
                                </div>
                                <div class="col-md-10 col-sm-9 mr-auto employee-items">
                                    <div class="d-flex flex-wrap  align-items-center">
                                        <div>
                                            <h3 class="employee-title mr-5 font-weight-normal full-name-record-info">{{ (!empty($employeeRecordInfo->v_employee_full_name) ? $employeeRecordInfo->v_employee_full_name :'') }}</h3>
                                        </div>
                                        <div>
                                            <span <?php echo "status = ".$employeeRecordInfo->t_is_suspended; ?>  class="small-title mr-sm-5 mr-3 bg-success text-white login-status" <?php echo ( ((!empty($employeeRecordInfo->t_is_active)) && ( $employeeRecordInfo->t_is_active == 1 ) && ( $employeeRecordInfo->t_is_suspended == 0 ) ) ? '' : 'style=display:none;' ) ?> >{{ ( ((!empty($employeeRecordInfo->t_is_active)) && ( $employeeRecordInfo->t_is_active == 1 ) && ( $employeeRecordInfo->t_is_suspended == 0 ) ) ? trans('messages.active') : trans('messages.active') )}}</span>
                                            <span class="small-title mr-sm-5 mr-3 bg-danger text-white suspended-status" <?php echo ( ( isset($employeeRecordInfo->t_is_suspended ) && ( $employeeRecordInfo->t_is_suspended  == 1 ) ) ? '' : 'style=display:none;' ) ?>  >{{ trans("messages.suspended") }}</span>
                                            @if( isset($employeeRecordInfo->e_employment_status) && ( $employeeRecordInfo->e_employment_status == config('constants.RELIEVED_EMPLOYMENT_STATUS') ) )
                                            <span class="small-title mr-sm-5 mr-3 bg-aqua text-white relieved-status" >{{ trans("messages.relieved") }}</span>
                                            @endif
                                            @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) ) && (!empty($employeeRecordInfo->v_outlook_email_id)) )
                                            @php
                                            $sendInviteButtonText = trans('messages.send-invite');
                                            if(  isset($employeeRecordInfo->loginInfo->v_password) && (!empty($employeeRecordInfo->loginInfo->v_password)) ){
                                                $sendInviteButtonText = trans('messages.resend-invite');
                                            }
                                           
                                            @endphp
                                                @if( isset($employeeRecordInfo->e_employment_status) && ( $employeeRecordInfo->e_employment_status != config('constants.RELIEVED_EMPLOYMENT_STATUS') ) )      
                                                <a href="javascript:void(0);" onclick="sendInvitation(this);"  data-record-id="{{ Wild_tiger::encode( $employeeRecordInfo->i_id ) }}" class="btn btn-sm bg-color1 text-white login-invitation-button resend-btn py-2 px-3 position-relative" title="{{ $sendInviteButtonText }}">{{ $sendInviteButtonText }}</a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-md-center flex-sm-row flex-column">
                                        <span class="employee-address-title state-name-record-info"><i class="fa fa-map-marker addres-icon mr-2" aria-hidden="true"></i> {{ (!empty($employeeRecordInfo->cityCurrentInfo->stateMaster->v_state_name) ? $employeeRecordInfo->cityCurrentInfo->stateMaster->v_state_name :'') }}</span>
                                        <?php
                                        $employeePersonalEmails = (!empty($employeeRecordInfo->v_outlook_email_id) ? $employeeRecordInfo->v_outlook_email_id :'');
                                        $employeeContactNo = (!empty($employeeRecordInfo->v_contact_no) ? $employeeRecordInfo->v_contact_no :'');?>
                                        <?php if(!empty($employeePersonalEmails)){ ?>
                                            <a href="mailto:{{ $employeePersonalEmails }}" class="employee-address-title main-address"><i class="fa fa-envelope addres-icon mr-2" aria-hidden="true"></i>{{ $employeePersonalEmails }}</a>
                                        <?php } ?>
                                        <?php if(!empty($employeeContactNo)){ ?>
                                            <a href="tel:{{ $employeeContactNo }}" class="employee-address-title main-address"><i class="fa fa-phone addres-icon rotate-icon mr-2" aria-hidden="true"></i>{{ $employeeContactNo  }}</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-center py-4">
                                <div class="col-md-10 info-items d-flex flex-wrap job-details-card">
                                    @if( isset($employeeRecordInfo->designationInfo->v_value) && (!empty($employeeRecordInfo->designationInfo->v_value)) )
                                    <div class="job-items">
                                        <span class="job-title">{{ trans("messages.designation") }}</span>
                                        <p class="job-text mb-0">{{ $employeeRecordInfo->designationInfo->v_value }}@if( isset($employeeRecordInfo->subDesignationInfo->v_sub_designation_name) && !empty($employeeRecordInfo->subDesignationInfo->v_sub_designation_name) ){{ ' - ' . $employeeRecordInfo->subDesignationInfo->v_sub_designation_name }}@endif</p>
                                    </div>
                                    @endif
                                    @if( isset($employeeRecordInfo->teamInfo->v_value) && (!empty($employeeRecordInfo->teamInfo->v_value)) )
                                    <div class="job-items">
                                        <span class="job-title">{{ trans("messages.team") }}</span>
                                        <p class="job-text text-truncate mb-0">{{ $employeeRecordInfo->teamInfo->v_value }}</p>
                                    </div>
                                    @endif
                                    <?php /* ?>
                                    <div class="job-items">
                                        <span class="job-title">{{ trans("messages.business-unit") }}</span>
                                        <p class="job-text mb-0">Cloud LIMS</p>
                                    </div>
                                    <?php */ ?>
                                    @if( isset($employeeRecordInfo->leaderInfo->v_employee_full_name) && (!empty($employeeRecordInfo->leaderInfo->v_employee_full_name)) )
                                    <div class="job-items">
                                        <span class="job-title">{{ trans("messages.reporting-to")  }}</span>
                                        @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) ) )
                                            <a href="{{ ( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) )  ?  config('constants.EMPLOYEE_PROFILE_LINK') . Wild_tiger::encode($employeeRecordInfo->i_leader_id) : 'javascript:void(0)' )  }}" target="_blank" class="job-text mb-0 d-block">{{ $employeeRecordInfo->leaderInfo->v_employee_full_name }}</a>
                                        @else
                                            <p class="job-text mb-0 d-block">{{ $employeeRecordInfo->leaderInfo->v_employee_full_name }}</p>
                                        @endif
                                       
                                    </div>
                                    @endif
                                    @if( isset($employeeRecordInfo->v_employee_code) && (!empty($employeeRecordInfo->v_employee_code)) )
                                    <div class="job-items">
                                        <span class="job-title">{{ trans("messages.emp-no") }}</span>
                                        <p class="job-text mb-0">{{ $employeeRecordInfo->v_employee_code }}</p>
                                    </div>
                                    @endif
                                </div>
                               
                                @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) )  ) || ( (  in_array( session()->get('role') , [ config('constants.ROLE_USER') ] )  && !isset($employeeRecordInfo->latestResignHistory)  ) && ( $employeeRecordInfo->i_login_id == session()->get('user_id') ) ) )  
                                    @if( $employeeRecordInfo->e_employment_status != config('constants.RELIEVED_EMPLOYMENT_STATUS'))
                                    <div class="col-md-2 mt-md-0 mt-3 info-button ml-auto">
                                        <div class="btn-group">
                                            <button type="button" class="btn bg-color1 text-white dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                {{ trans("messages.actions") }}
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) )  && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) )  )  
                                                    @if( !isset($employeeRecordInfo->latestResignHistory) )
                                                    <a href="javascript:void(0);" onclick="initiateExitForm(this);" data-record-id="{{ Wild_tiger::encode( $employeeRecordInfo->i_id ) }}" data-type="{{ config('constants.INITIATE_EXIT') }}"  data-toggle="modal" data-target="#initiate-exit" class="dropdown-item">{{ trans("messages.initiate-exit") }}</a>
                                                    @endif
                                                <a href="javascript:void(0);" data-selecteion-status="{{ config('constants.SELECTION_YES') }}" onclick="disableLogin(this);" data-record-id="{{ Wild_tiger::encode( $employeeRecordInfo->i_id ) }}" data-current-status="{{ ( $employeeRecordInfo->t_is_active == 1 ? config('constants.ACTIVE_STATUS') : config('constants.INACTIVE_STATUS') ) }}" class="dropdown-item update-login-status">{{ ( $employeeRecordInfo->t_is_active == 1 ? trans("messages.disable-login") : trans("messages.enable-login") ) }}</a>
                                                @endif
                                                @if( ( ( session()->get('role') ==  config('constants.ROLE_USER') ) && ( session()->get('user_employee_id') ==  $employeeRecordInfo->i_id ) ) &&  ( ( !isset($employeeRecordInfo->latestResignHistory) || ( ( empty($employeeRecordInfo->latestResignHistory) || (  (!empty($employeeRecordInfo->latestResignHistory->e_initiate_type)) && ( $employeeRecordInfo->latestResignHistory->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) ) )  ) ) ) )
                                                <a href="javascript:void(0);" onclick="resignForm(this);"  data-joining-date="{{  $employeeRecordInfo->dt_joining_date}}" data-record-id="{{ Wild_tiger::encode( $employeeRecordInfo->i_id ) }}" data-type="{{ config('constants.INITIATE_EXIT') }}"  class="dropdown-item">{{ trans("messages.resign-from-job") }}</a>
                                                @endif
                                                @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) && ( $employeeRecordInfo->t_is_suspended == 0 )  )  
                                                <a href="javascript:void(0);" data-joining-date="{{ $employeeRecordInfo->dt_joining_date }}" onclick="openSuspendModel(this)" data-employee-id="{{ Wild_tiger::encode( $employeeRecordInfo->i_id ) }}" class="dropdown-item">{{ trans("messages.suspend") }}
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="employee-info">
                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                           
                            <li class="nav-item" role="presentation">
                                <a href="javascript:void(0);" class="nav-link active" id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">{{ trans("messages.profile") }}</a>
                            </li>
                            @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) ) || (  (isset($employeeRecordInfo->i_login_id) && ( $employeeRecordInfo->i_login_id == session()->get('user_id') ) )  ) )
                            <li class="nav-item" role="presentation">
                                <a href="javascript:void(0);" class="nav-link emp-doc-tab" onclick="getEmployeeDocumentList(this)" data-record-id="{{ (isset($empId) ? $empId : 0 )}}"  data-fetch="{{ config('constants.SELECTION_NO') }}" id="pills-documents-tab" data-toggle="pill" data-target="#pills-documents" role="tab" aria-controls="pills-documents" aria-selected="true">{{ trans("messages.documents") }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="javascript:void(0);" class="nav-link" onclick="getEmployeePaySlipInfo(this)" data-record-id="{{ (isset($empId) ? $empId : 0 )}}"  data-fetch="{{ config('constants.SELECTION_NO') }}" id="pills-pay-slips-tab" data-toggle="pill" data-target="#pills-pay-slips" role="tab" aria-controls="pills-pay-slips" aria-selected="true">{{ trans("messages.pay-slips") }}</a>
                            </li>
                            @if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_VIEW_SALARY'), session()->get('user_permission')  ) ) ) )  ||  ( session()->get('user_employee_id') == $employeeRecordInfo->i_id  ) )
                            <li class="nav-item" role="presentation">
                                <a href="javascript:void(0);" class="nav-link employee-salary-tab" onclick="getEmployeeSalaryInfo(this)" data-record-id="{{ (isset($empId) ? $empId : 0 )}}"  data-fetch="{{ config('constants.SELECTION_NO') }}"  id="pills-salary-tab" data-toggle="pill" data-target="#pills-salary" role="tab" aria-controls="pills-salary" aria-selected="false">{{ trans("messages.salary") }}</a>
                            </li>
                            @endif
                            @endif
                            <li class="nav-item" role="presentation">
                                <a href="javascript:void(0);" class="nav-link" onclick="getEmployeeLeaveList(this)" data-record-id="{{ (isset($empId) ? $empId : 0 )}}"  data-fetch="{{ config('constants.SELECTION_NO') }}" id="pills-leave-tab" data-toggle="pill" data-target="#pills-leave" role="tab" aria-controls="pills-leave-attendance" aria-selected="false">{{ trans("messages.leave") }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="javascript:void(0);" class="nav-link" onclick="getEmployeeAttendanceList(this)" data-record-id="{{ (isset($empId) ? $empId : 0 )}}" data-fetch="{{ config('constants.SELECTION_NO') }}" id="pills-attendance-tab" data-toggle="pill" data-target="#pills-attendance" role="tab" aria-controls="pills-leave-attendance" aria-selected="false">{{ trans("messages.attendance") }}</a>
                            </li>
                            @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) ) || (  (isset($employeeRecordInfo->i_login_id) && ( $employeeRecordInfo->i_login_id == session()->get('user_id') ) )  ) )
                                @php
                                    $showOneMonthFeedback = false;
                                    $showSixMonthFeedback = false;
                                    
                                    if ($employeeRecordInfo && $employeeRecordInfo->dt_joining_date) {
                                        $joiningDate = \Carbon\Carbon::parse($employeeRecordInfo->dt_joining_date);
                                        $currentDate = \Carbon\Carbon::now();
                                        
                                        // Check for 1 month feedback (28-32 days after joining)
                                        $oneMonthStartDate = $joiningDate->copy()->addDays(28);
                                        $oneMonthEndDate = $joiningDate->copy()->addDays(32);
                                        if ($currentDate->between($oneMonthStartDate, $oneMonthEndDate)) {
                                            $showOneMonthFeedback = true;
                                        }
                                        
                                        // Check for 6 month feedback (175-185 days after joining)
                                        $sixMonthStartDate = $joiningDate->copy()->addDays(175);
                                        $sixMonthEndDate = $joiningDate->copy()->addDays(185);
                                        if ($currentDate->between($sixMonthStartDate, $sixMonthEndDate)) {
                                            $showSixMonthFeedback = true;
                                        }
                                        
                                        // Always show for admin regardless of date
                                        if (in_array(session()->get('role'), [config('constants.ROLE_ADMIN')])) {
                                            $showOneMonthFeedback = true;
                                            $showSixMonthFeedback = true;
                                        }
                                    }
                                @endphp
                                
                                @if($showOneMonthFeedback || $showSixMonthFeedback)
                                <li class="nav-item dropdown" role="presentation">
                                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="pills-feedback-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Feedback Forms</a>
                                    <div class="dropdown-menu" aria-labelledby="pills-feedback-dropdown">
                                        @if($showOneMonthFeedback)
                                            <a class="dropdown-item" href="{{ route('employee-feedback.show', $employeeRecordInfo->i_id) }}">1 Month Feedback</a>
                                        @endif
                                        @if($showSixMonthFeedback)
                                            <a class="dropdown-item" href="{{ route('employee-feedback-six.show', $employeeRecordInfo->i_id) }}">6 Month Feedback</a>
                                        @endif
                                    </div>
                                </li>
                                @endif
                            @endif
                        </ul>
 
                    </div>
                </div>
            </div>

 