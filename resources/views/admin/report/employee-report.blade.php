@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.master-sheet") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
            <button type="button" onclick="exportData();" title="{{ trans('messages.export-excel') }}" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel fa-fw mr-0 mr-sm-2"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row depedent-row">
					<div class="col-lg-4 col-12">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans("messages.search-by") }}</label><i class="fa fa-info-circle ml-2" data-toggle="tooltip" data-placement="right" title="{{ trans('messages.search-by-employee-report') }}"></i>
                            <input type="text" name="search_by" class="form-control twt-enter-search" placeholder="{{ trans('messages.search-by-employee-report') }}">
                        </div>
                    </div>
					@if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_REPORT'), session()->get('user_permission')  ) ) ) ) )
                	<div class="col-xl-2 col-lg-4 col-12">
                		<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' )  );?>
                	</div>
                    <div class="col-xl-3 col-lg-4 col-12">
                    	<?php echo statusWiseEmployeeList('search_employee_name' , (isset($employeeDetails) ? $employeeDetails : [] ));?>
                    </div>
	                @endif

                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_gender" class="control-label">{{ trans('messages.gender') }}</label>
                            <select class="form-control" name="search_gender" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($genderDetails))
                                	@foreach($genderDetails as $key => $genderDetail)
                                		<option value="{{ $key }}">{{ $genderDetail }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_blood_group" class="control-label">{{ trans('messages.blood-group') }}</label>
                            <select class="form-control" name="search_blood_group" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($bloodGroupDetails))
                                	@foreach($bloodGroupDetails as $key => $bloodGroupDetail)
                                		<option value="{{ $key }}">{{ $bloodGroupDetail }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_marital_status" class="control-label">{{ trans('messages.marital-status') }}</label>
                            <select class="form-control" name="search_marital_status" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($maritalStatusDetails))
                                	@foreach($maritalStatusDetails as $key => $maritalStatusDetail)
                                		<option value="{{ $key }}">{{ $maritalStatusDetail }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-2 col-md-3 col-sm-6 col-6">
                        <label for="search_joining_from_date" class="control-label">{{ trans("messages.joining-from-date") }}</label>
                        <input type="text" class="form-control" name="search_joining_from_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" autocomplete="off" />
                    </div>
                    <div class="form-group col-lg-2 col-md-3 col-sm-6 col-6">
                        <label for="search_joining_to_date" class="control-label">{{ trans("messages.joining-to-date") }}</label>
                        <input type="text" class="form-control date" name="search_joining_to_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" autocomplete="off" />
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_team_name" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team_name" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($teamDetails))
                                	@foreach($teamDetails as $teamDetail)
                                		<option value="{{ (!empty($teamDetail->i_id) ? Wild_tiger::encode($teamDetail->i_id) : 0) }}">{{ (!empty($teamDetail->v_value) ? $teamDetail->v_value :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_designation" class="control-label">{{ trans('messages.designation') }}</label>
                            <select class="form-control select2" name="search_designation" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($designationDetails))
                                	@foreach($designationDetails as $designationDetail)
                                		<option value="{{ (!empty($designationDetail->i_id) ? Wild_tiger::encode($designationDetail->i_id) : 0) }}">{{ (!empty($designationDetail->v_value) ? $designationDetail->v_value :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>

					@if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_REPORT'), session()->get('user_permission')  ) ) ) ) ) )
                    <div class="col-xl-3 col-md-4 col-12 col-sm-6">
                        <div class="form-group">
                            <label for="search_leader_name_reporting_manager" class="control-label">{{ trans('messages.leader-name-reporting-manager') }}</label>
                            <select class="form-control select2" name="search_leader_name_reporting_manager" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($leaderDetails))
                                	@foreach($leaderDetails as $leaderDetail)
                                		<option value="{{ (!empty($leaderDetail->i_id) ? Wild_tiger::encode($leaderDetail->i_id) : 0) }}">{{ (!empty($leaderDetail->v_employee_full_name) ? $leaderDetail->v_employee_full_name .(!empty($leaderDetail->v_employee_code) ? ' (' .$leaderDetail->v_employee_code .')' :''):'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="col-xl-3 col-md-3 col-12 col-sm-6">
                        <div class="form-group">
                            <label for="search_recruitment_source" class="control-label">{{ trans('messages.recruitment-source') }}</label>
                            <select class="form-control" name="search_recruitment_source" onchange="showReferenceNameInfo();filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($recruitmentSourceDetails))
                                	@foreach($recruitmentSourceDetails as $recruitmentSourceDetail)
                                		<option value="{{ (!empty($recruitmentSourceDetail->i_id) ? Wild_tiger::encode($recruitmentSourceDetail->i_id) : 0) }}" data-recruitment-id="{{ $recruitmentSourceDetail->i_id }}">{{ (!empty($recruitmentSourceDetail->v_value) ? $recruitmentSourceDetail->v_value :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3 col-12 col-sm-6 reference-name-record" style="display:none">
                        <div class="form-group">
                            <label for="search_reference_name" class="control-label">{{ trans('messages.reference-name') }}</label>
                            <select class="form-control select2 " name="search_reference_name" onchange="filterData();">
                               <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($referenceEmployeeDetails))
                                	@foreach($referenceEmployeeDetails as $referenceEmployeeDetail)
                                		<option value="{{ (!empty($referenceEmployeeDetail->i_id) ? Wild_tiger::encode($referenceEmployeeDetail->i_id) : 0) }}">{{ (!empty($referenceEmployeeDetail->v_employee_full_name) ? $referenceEmployeeDetail->v_employee_full_name :'') }} ({{ (!empty($referenceEmployeeDetail->v_employee_code) ? $referenceEmployeeDetail->v_employee_code :'') }})</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-3 col-12 col-sm-6">
                        <div class="form-group">
                            <label for="search_shift" class="control-label">{{ trans('messages.shift') }}</label>
                            <select class="form-control" name="search_shift" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($shiftDetails))
                                	@foreach($shiftDetails as $shiftDetail)
                                		<option value="{{ (!empty($shiftDetail->i_id) ? Wild_tiger::encode($shiftDetail->i_id) : 0) }}">{{ (!empty($shiftDetail->v_shift_name) ? $shiftDetail->v_shift_name :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_probation_period" class="control-label">{{ trans('messages.probation-period') }}</label>
                            <select class="form-control" name="search_probation_period" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($probationPolicyDetails))
                                	@foreach($probationPolicyDetails as $probationPolicyDetail)
                                		<option value="{{ (!empty($probationPolicyDetail->i_id) ? Wild_tiger::encode($probationPolicyDetail->i_id) : 0) }}">{{ (!empty($probationPolicyDetail->v_probation_policy_name) ? $probationPolicyDetail->v_probation_policy_name :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_notice_period" class="control-label">{{ trans('messages.notice-period') }}</label>
                            <select class="form-control" name="search_notice_period" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($noticePeriodPolicyDetails))
                                	@foreach($noticePeriodPolicyDetails as $noticePeriodPolicyDetail)
                                		<option value="{{ (!empty($noticePeriodPolicyDetail->i_id) ? Wild_tiger::encode($noticePeriodPolicyDetail->i_id) : 0) }}">{{ (!empty($noticePeriodPolicyDetail->v_probation_policy_name) ? $noticePeriodPolicyDetail->v_probation_policy_name :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_weekly_off" class="control-label">{{ trans('messages.weekly-off') }}</label>
                            <select class="form-control" name="search_weekly_off" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($weeeklyOffDetails))
                                	@foreach($weeeklyOffDetails as $weeeklyOffDetail)
                                		<option value="{{ (!empty($weeeklyOffDetail->i_id) ? Wild_tiger::encode($weeeklyOffDetail->i_id) : 0) }}">{{ (!empty($weeeklyOffDetail->v_weekly_off_name) ? $weeeklyOffDetail->v_weekly_off_name :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
					<div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_bank_name" class="control-label">{{ trans('messages.bank') }}</label>
                            <select class="form-control" name="search_bank_name" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($bankDetails))
                                	@foreach($bankDetails as $bankDetail)
                                		<option value="{{ (!empty($bankDetail->i_id) ? Wild_tiger::encode($bankDetail->i_id) : 0) }}">{{ (!empty($bankDetail->v_value) ? $bankDetail->v_value :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_REPORT'), session()->get('user_permission')  ) ) ) ) ) )
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_bank_name" class="control-label">{{ trans('messages.salary-group') }}</label>
                            <select class="form-control" name="search_salary_group" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($salaryGroupDetails))
                                	@foreach($salaryGroupDetails as $salaryGroupDetail)
                                		<option value="{{ (!empty($salaryGroupDetail->i_id) ? Wild_tiger::encode($salaryGroupDetail->i_id) : 0) }}">{{ (!empty($salaryGroupDetail->v_group_name) ? $salaryGroupDetail->v_group_name :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_login_status">{{ trans("messages.deduction-of-pf") }}</label>
                            <select class="form-control" name="search_deduction_of_pf_status" onchange="filterData();">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.SELECTION_YES') }}">{{ trans("messages.yes") }}</option>
                                <option value="{{ config('constants.SELECTION_NO') }}">{{ trans("messages.no") }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_assign_role" class="control-label">{{ trans('messages.role') }}</label>
                            <select class="form-control" name="search_assign_role" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($roleDetails))
                                	@foreach($roleDetails as $roleDetail)
                                		<option value="{{ (!empty($roleDetail->i_id) ? Wild_tiger::encode($roleDetail->i_id) : 0) }}">{{ (!empty($roleDetail->v_role_name) ? $roleDetail->v_role_name :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-3 col-sm-6" >
                        <div class="form-group">
                            <label class="control-label" for="search_type">{{ trans("messages.date") }}</label>
                            <select name="search_date_type" class="form-control" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.LAST_WORKING_DATE') }}">{{ trans('messages.last-working-date') }}</option>
                                <option value="{{ config('constants.PF_EXPIRY_DATE') }}">{{ trans('messages.pf-exit-date') }}</option>
                                <option value="{{ config('constants.RESGINATION_DATE') }}">{{ trans('messages.resignation-date') }}</option>
                                
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-lg-2 col-md-3 col-sm-6 col-6" >
                        <label for="search_date_selection_from_date" class="control-label">{{ trans("messages.from-date") }}</label>
                        <input type="text" class="form-control" name="search_date_selection_from_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" autocomplete="off" />
                    </div>
                    <div class="form-group col-lg-2 col-md-3 col-sm-6 col-6" >
                        <label for="search_date_selection_to_date" class="control-label">{{ trans("messages.to-date") }}</label>
                        <input type="text" class="form-control date" name="search_date_selection_to_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" autocomplete="off" />
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_resignation_type">{{ trans("messages.exit-type") }}</label>
                            <select name="search_resignation_type" class="form-control" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') }}">{{ trans('messages.resignation') }}</option>
                                <option value="{{ config('constants.EMPLOYER_INITIATE_EXIT_TYPE') }}">{{ trans('messages.termination') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 resign-status-div" style="display:none;">
                        <div class="form-group">
                            <label class="control-label" for="search_status">{{ trans("messages.reason-for-leaving") }}</label>
                            <select name="search_resign_status" class="form-control" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($resignationReasonDetails))
                                	@foreach($resignationReasonDetails as $key=> $resignationResignDetail)
                                		@php $encodeResignId = Wild_tiger::encode($resignationResignDetail->i_id); @endphp
                                		<option value="{{ $encodeResignId }}">{{ (!empty($resignationResignDetail->v_value) ? $resignationResignDetail->v_value : '') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 terminate-status-div" style="display:none;">
                        <div class="form-group">
                            <label class="control-label" for="search_status">{{ trans("messages.termination-reason") }}</label>
                            <select name="search_terminate_status" class="form-control" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($terminationReasonDetails))
                                	@foreach($terminationReasonDetails as $key=> $terminationReasonDetail)
                                		@php $encodeTerminateId = Wild_tiger::encode($terminationReasonDetail->i_id); @endphp
                                		<option value="{{ $encodeTerminateId }}">{{ (!empty($terminationReasonDetail->v_value) ? $terminationReasonDetail->v_value : '') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif
                    <?php /*?>
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_current_city" class="control-label">{{ trans('messages.current-city') }}</label>
                            <select class="form-control select2" name="search_current_city" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($cityDetails))
                                	@foreach($cityDetails as $cityDetail)
                                		<option value="{{ (!empty($cityDetail->i_id) ? Wild_tiger::encode($cityDetail->i_id) : 0) }}">{{ (!empty($cityDetail->v_city_name) ? $cityDetail->v_city_name :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_perm_city" class="control-label">{{ trans('messages.permanent-city') }}</label>
                            <select class="form-control select2" name="search_perm_city" onchange="filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                 @if(!empty($cityDetails))
                                	@foreach($cityDetails as $cityDetail)
                                		<option value="{{ (!empty($cityDetail->i_id) ? Wild_tiger::encode($cityDetail->i_id) : 0) }}">{{ (!empty($cityDetail->v_city_name) ? $cityDetail->v_city_name :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <?php */ ?>
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_login_status">{{ trans("messages.login-status") }}</label>
                            <select class="form-control" name="search_login_status" onchange="filterData();">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.ENABLE_STATUS') }}">{{ trans("messages.enable") }}</option>
                                <option value="{{ config('constants.DISABLE_STATUS') }}">{{ trans("messages.disable") }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" onclick="filterData()" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
            <div class="card card-body">
                <div class="table-responsive fixed-tabel-body employee-report-table">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left employee-name-code-th" style="width:120px;min-width:120px;">{{ trans("messages.employee-code") }}</th>
                                <th class="text-left employee-name-code-th employee-name-code-th-2" style="width:165px;min-width:165px;">{{ trans("messages.employee-name") }}</th>
                                <th class="text-left" style="width:165px;min-width:165px;">{{ trans("messages.full-name") }}</th>
                                <th class="text-left" style="min-width:100px;">{{ trans("messages.gender") }}</th>
                                <th class="text-left" style="min-width:100px;">{{ trans("messages.blood-group") }}</th>
                                <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.joining-date") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.designation") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="width:214px;min-width:214px;">{{ trans("messages.leader-name") }} / {{ trans("messages.reporting-manager") }}</th>
                                <th class="text-left" style="width:140px;min-width:140px;">{{ trans("messages.employment-status") }}</th>
                                <th class="text-left" style="width:170px;min-width:170px;">{{ trans("messages.recruitment-source") }} <br> {{ trans("messages.reference-name") }}</th>
                                <th class="text-left" style="min-width:138px;">{{ trans("messages.aadhaar-number") }}</th>
                                <th class="text-left" style="min-width:138px;">{{ trans("messages.pan") }}</th>
                                <th class="text-left" style="min-width:138px;">{{ trans("messages.education") }}</th>
                                <th class="text-left" style="min-width:138px;">{{ trans("messages.cgpa-percentage") }}</th>
                                <th class="text-left" style="min-width:138px;">{{ trans("messages.marital-status") }}</th>
                                <th class="text-left" style="min-width:138px;">{{ trans("messages.shift") }}</th>
                                <th class="text-left" style="min-width:166px;">{{ trans("messages.weekly-off") }}</th>
                                <th class="text-left" style="width:215px;min-width:215px;">{{ trans("messages.outlook-email-id") }}</th>
                                <th class="text-left" style="width:140px;min-width:140px;">{{ trans("messages.contact-number") }}</th>
                                <th class="text-left" style="width:215px;min-width:215px;">{{ trans("messages.personal-email-id") }}</th>
                                <th class="text-left" style="min-width:108px;">{{ trans("messages.date-of-birth") }}</th>
                                <th class="text-left" style="width:215px;min-width:215px;">{{ trans("messages.current-address") }}</th>
                                <th class="text-left" style="width:215px;min-width:215px;">{{ trans("messages.permanent-address") }}</th>
                                <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.probation-period") }}</th>
                                <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.notice-period") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.bank-name") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.account-number") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.ifsc-code") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.uan-number") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.salary-group") }} <br>{{ trans("messages.deduction-of-pf") }} </th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.role") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.last-working-date") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.pf-exit-date") }}</th>
                                @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_VIEW_SALARY'), session()->get('user_permission')  ) ) ) ) ) )
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.monthly-salary") }}</th>
                                @endif
                                
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.exit-type") }} - {{ trans("messages.reason-for-leaving") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.resignation-date") }}</th>
                                <th class="text-left" style="min-width:100px; width:100px;">{{ trans("messages.login-status") }}</th>
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                        	@include( config('constants.AJAX_VIEW_FOLDER') . 'report/employee-report-list')
                       </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="employee_team_id" value="{{ (isset($teamRecordId) && !empty($teamRecordId) ? Wild_tiger::encode($teamRecordId) : '') }}">
    <input type="hidden" name="employee_current_city_id" value="{{ (isset($currentAddressCityId) && !empty($currentAddressCityId) ? Wild_tiger::encode($currentAddressCityId) : '') }}">
    
</main>

<script>
   
    $(function() {
        $(' [name="search_joining_from_date"], [name="search_joining_to_date"], [name="search_date_selection_from_date"], [name="search_date_selection_to_date"]').datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            format: 'DD-MM-YYYY',
            showClear: true,
            showClose: true,
            widgetPositioning: {
                vertical: 'bottom',
                horizontal: 'auto'

            },
            icons: {
                clear: 'fa fa-trash',
                Close: 'fa fa-trash',
            },
        });
        

        $("[name='search_joining_from_date']").datetimepicker().on('dp.change', function(e) {
    		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    			var incrementDay = moment((e.date)).startOf('d');
    		 	$("[name='search_joining_to_date']").data('DateTimePicker').minDate(incrementDay);
    		} else {
    			$("[name='search_joining_to_date']").data('DateTimePicker').minDate(false);
    		} 
    		
    	    $(this).data("DateTimePicker").hide();
    	});

        $("[name='search_joining_to_date']").datetimepicker().on('dp.change', function(e) {
        	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    	        var decrementDay = moment((e.date)).endOf('d');
    	        $("[name='search_joining_from_date']").data('DateTimePicker').maxDate(decrementDay);
        	} else {
        		 $("[name='search_joining_from_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });


        $("[name='search_date_selection_from_date']").datetimepicker().on('dp.change', function(e) {
    		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    			var incrementDay = moment((e.date)).startOf('d');
    		 	$("[name='search_date_selection_to_date']").data('DateTimePicker').minDate(incrementDay);
    		} else {
    			$("[name='search_date_selection_to_date']").data('DateTimePicker').minDate(false);
    		} 
    		
    	    $(this).data("DateTimePicker").hide();
    	});

        $("[name='search_date_selection_to_date']").datetimepicker().on('dp.change', function(e) {
        	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    	        var decrementDay = moment((e.date)).endOf('d');
    	        $("[name='search_date_selection_from_date']").data('DateTimePicker').maxDate(decrementDay);
        	} else {
        		 $("[name='search_date_selection_from_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });
        
    });
    function searchField(){
    	var search_by = $.trim($('[name="search_by"]').val());
    	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
    	var search_employee_name = $.trim($('[name="search_employee_name"]').val());
    	var search_gender = $.trim($('[name="search_gender"]').val());
    	var search_blood_group = $.trim($('[name="search_blood_group"]').val());
    	var search_joining_from_date = $.trim($('[name="search_joining_from_date"]').val());
    	var search_joining_to_date = $.trim($('[name="search_joining_to_date"]').val());
    	var search_team_name = $.trim($('[name="search_team_name"]').val());
    	var search_designation = $.trim($('[name="search_designation"]').val());
    	var search_leader_name_reporting_manager = $.trim($('[name="search_leader_name_reporting_manager"]').val());
    	var search_recruitment_source = $.trim($('[name="search_recruitment_source"]').val());
    	var search_reference_name = $.trim($('[name="search_reference_name"]').val());
    	var search_shift = $.trim($('[name="search_shift"]').val());
    	var search_probation_period = $.trim($('[name="search_probation_period"]').val());
    	var search_notice_period = $.trim($('[name="search_notice_period"]').val());
    	var search_weekly_off = $.trim($('[name="search_weekly_off"]').val());
    	var search_login_status = $.trim($('[name="search_login_status"]').val());
    	var search_bank_name = $.trim($('[name="search_bank_name"]').val());
    	var search_current_city = $.trim($('[name="search_current_city"]').val());
    	var search_perm_city = $.trim($('[name="search_perm_city"]').val());
    	var employee_team_id = $.trim($('[name="employee_team_id"]').val());
    	var employee_current_city_id = $.trim($('[name="employee_current_city_id"]').val());
    	var search_salary_group  = $.trim($('[name="search_salary_group"]').val()); 
    	var search_deduction_of_pf_status  = $.trim($('[name="search_deduction_of_pf_status"]').val()); 
    	var search_date_type = $.trim($('[name="search_date_type"]').val());
    	var search_date_selection_from_date = $.trim($('[name="search_date_selection_from_date"]').val());
    	var search_date_selection_to_date = $.trim($('[name="search_date_selection_to_date"]').val());
    	var search_resignation_type = $.trim($('[name="search_resignation_type"]').val());
    	var search_resign_status = $.trim($('[name="search_resign_status"]').val());
    	var search_terminate_status = $.trim($('[name="search_terminate_status"]').val());
    	var search_assign_role = $.trim($('[name="search_assign_role"]').val());
    	var search_marital_status  = $.trim($('[name="search_marital_status"]').val()); 
    	
    	
    	var searchData = {
                'search_by':search_by,
                'search_employment_status': search_employment_status,
                'search_employee_name':search_employee_name,
                'search_gender': search_gender,
                'search_blood_group': search_blood_group,
                'search_joining_from_date': search_joining_from_date,
                'search_joining_to_date': search_joining_to_date,
                'search_team_name':search_team_name,
                'search_designation': search_designation,
                'search_leader_name_reporting_manager': search_leader_name_reporting_manager,
                'search_recruitment_source': search_recruitment_source,
                'search_reference_name': search_reference_name,
                'search_shift': search_shift,
                'search_probation_period': search_probation_period,
                'search_notice_period': search_notice_period,
                'search_weekly_off': search_weekly_off,
                'search_login_status': search_login_status,
                'search_bank_name':search_bank_name,
                'search_current_city':search_current_city,
                'search_perm_city':search_perm_city,
				'employee_team_id':employee_team_id,
                'employee_current_city_id':employee_current_city_id,
                'search_salary_group':search_salary_group,
                'search_deduction_of_pf_status':search_deduction_of_pf_status,
                'search_date_type':search_date_type,
                'search_date_selection_from_date':search_date_selection_from_date,
                'search_date_selection_to_date':search_date_selection_to_date,
                'search_resignation_type':search_resignation_type,
                'search_resign_status':search_resign_status,
                'search_terminate_status':search_terminate_status,
                'search_assign_role':search_assign_role,
                'search_marital_status':search_marital_status,
            }
            return searchData;
    }
    var employee_report_url = '{{config("constants.EMPLOYEE_REPORT_URL")}}' + '/';
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(employee_report_url + 'employeeReportfilter' , searchFieldName);
    }
    var paginationUrl = employee_report_url + 'employeeReportfilter'
    
    function exportData(){
    	var searchData = searchField();
    	var export_info = {};
    	export_info.url = employee_report_url + 'employeeReportfilter';
    	export_info.searchData = searchData;
    	dataExportIntoExcel(export_info);
    }

    function showReferenceNameInfo(thisitem){
		var search_recruitment_source = $.trim($("[name='search_recruitment_source']").find('option:selected').attr('data-recruitment-id'));
		//console.log("search_recruitment_source = " + search_recruitment_source );
		if(search_recruitment_source != "" && search_recruitment_source != null){
			if(search_recruitment_source == "{{config('constants.EMPLOYEE_RECRUITMENT_SOURCE_ID')}}"){
				$('.reference-name-record').show();
			} else {
				$('.reference-name-record').hide();
			}
		} else{
			$('.reference-name-record').hide();
		}
	}

    $("[name='search_resignation_type']").on("change" , function(){
		var type = $.trim($("[name='search_resignation_type']").val());
		console.log(type);
		if( type != "" && type != null ){
			switch(type){
				case "{{ config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') }}":
					$(".resign-status-div").show();
					$(".terminate-status-div").hide();
					break;
				case "{{ config('constants.EMPLOYER_INITIATE_EXIT_TYPE') }}":
					$(".resign-status-div").hide();
					$(".terminate-status-div").show();
					break;
				default:
					$(".resign-status-div").hide();
					$(".terminate-status-div").hide();
					break;	
			}
		} else {
			$(".resign-status-div").hide();
			$(".terminate-status-div").hide();
		}

   	})
</script>
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>


@endsection