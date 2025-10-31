@extends('includes/header')

@section('pageTitle', 'Employee Feedback Form')

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">Employee Feedback Form</h1>
        <div class="ml-auto">
            <a href="{{ route('employee-master.profile', $employee->i_id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back to Employee Profile
            </a>
        </div>
    </div>

    <div class="container-fluid pt-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">One Month Employee Feedback - {{ $employee->v_employee_full_name }} ({{ $employee->v_employee_code }})</h5>
            </div>
            <div class="card-body">
                <form id="feedbackForm" method="POST" action="{{ route('employee-feedback.store', $employee->i_id) }}">
                    @csrf

                    <!-- Employee Details Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="font-weight-bold">Employee Details</h6>
                            <hr>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="v_employee_name" class="form-label">Employee Name *</label>
                            <input type="text" class="form-control" id="v_employee_name" name="v_employee_name"
                                   value="{{ $employee->v_employee_full_name ?? '' }}" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="col-md-6">
                            <label for="v_emp_code" class="form-label">Employee Code *</label>
                            <input type="text" class="form-control" id="v_emp_code" name="v_emp_code"
                                   value="{{ $employee->v_employee_code ?? '' }}" readonly style="background-color: #f8f9fa;">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="v_department" class="form-label">Department *</label>
                            <input type="text" class="form-control" id="v_department" name="v_department"
                                   value="{{ $employee->teamInfo ? $employee->teamInfo->v_value : 'N/A' }}" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="col-md-6">
                            <label for="v_designation" class="form-label">Designation *</label>
                            <input type="text" class="form-control" id="v_designation" name="v_designation"
                                   value="{{ $employee->designationInfo ? $employee->designationInfo->v_value : 'N/A' }}" readonly style="background-color: #f8f9fa;">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="d_date_of_joining" class="form-label">Date of Joining *</label>
                            <input type="date" class="form-control" id="d_date_of_joining" name="d_date_of_joining"
                                   value="{{ $employee->dt_joining_date ?? '' }}" readonly style="background-color: #f8f9fa;">
                        </div>
                    </div>

                    <!-- Onboarding Process & Training Rating Scale in Table Format -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="font-weight-bold">Onboarding Process & Training (Rate 1-5)</h6>
                            <hr>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40%" class="text-center">Question</th>
                                            <th width="12%" class="text-center">5<br><small>Strongly Agree</small></th>
                                            <th width="12%" class="text-center">4<br><small>Agree</small></th>
                                            <th width="12%" class="text-center">3<br><small>Neutral</small></th>
                                            <th width="12%" class="text-center">2<br><small>Disagree</small></th>
                                            <th width="12%" class="text-center">1<br><small>Strongly Disagree</small></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Have you understood the onboarding process?(Induction & Orientation Plan, Welcome kit, Offer & Appointment letter)</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_onboarding_process" value="5" id="understand_onboarding_5">
                                                    <label class="form-check-label" for="understand_onboarding_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_onboarding_process" value="4" id="understand_onboarding_4">
                                                    <label class="form-check-label" for="understand_onboarding_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_onboarding_process" value="3" id="understand_onboarding_3" checked>
                                                    <label class="form-check-label" for="understand_onboarding_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_onboarding_process" value="2" id="understand_onboarding_2">
                                                    <label class="form-check-label" for="understand_onboarding_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_onboarding_process" value="1" id="understand_onboarding_1">
                                                    <label class="form-check-label" for="understand_onboarding_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>I understand all company policy, procedure, and work rules, NDA.</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_company_policy" value="5" id="understand_policy_5">
                                                    <label class="form-check-label" for="understand_policy_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_company_policy" value="4" id="understand_policy_4">
                                                    <label class="form-check-label" for="understand_policy_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_company_policy" value="3" id="understand_policy_3" checked>
                                                    <label class="form-check-label" for="understand_policy_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_company_policy" value="2" id="understand_policy_2">
                                                    <label class="form-check-label" for="understand_policy_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_company_policy" value="1" id="understand_policy_1">
                                                    <label class="form-check-label" for="understand_policy_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>I am well trained about the process and work.</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_well_trained_about_process" value="5" id="trained_process_5">
                                                    <label class="form-check-label" for="trained_process_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_well_trained_about_process" value="4" id="trained_process_4">
                                                    <label class="form-check-label" for="trained_process_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_well_trained_about_process" value="3" id="trained_process_3" checked>
                                                    <label class="form-check-label" for="trained_process_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_well_trained_about_process" value="2" id="trained_process_2">
                                                    <label class="form-check-label" for="trained_process_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_well_trained_about_process" value="1" id="trained_process_1">
                                                    <label class="form-check-label" for="trained_process_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>I am aware of my whole department process</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_aware_department_process" value="5" id="aware_dept_5">
                                                    <label class="form-check-label" for="aware_dept_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_aware_department_process" value="4" id="aware_dept_4">
                                                    <label class="form-check-label" for="aware_dept_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_aware_department_process" value="3" id="aware_dept_3" checked>
                                                    <label class="form-check-label" for="aware_dept_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_aware_department_process" value="2" id="aware_dept_2">
                                                    <label class="form-check-label" for="aware_dept_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_aware_department_process" value="1" id="aware_dept_1">
                                                    <label class="form-check-label" for="aware_dept_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>I am trained enough for my responsibilities and work in my team/department.</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_trained_for_responsibilities" value="5" id="trained_resp_5">
                                                    <label class="form-check-label" for="trained_resp_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_trained_for_responsibilities" value="4" id="trained_resp_4">
                                                    <label class="form-check-label" for="trained_resp_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_trained_for_responsibilities" value="3" id="trained_resp_3" checked>
                                                    <label class="form-check-label" for="trained_resp_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_trained_for_responsibilities" value="2" id="trained_resp_2">
                                                    <label class="form-check-label" for="trained_resp_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_trained_for_responsibilities" value="1" id="trained_resp_1">
                                                    <label class="form-check-label" for="trained_resp_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>I feel interested and motivated in my work.</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_interested_and_motivated" value="5" id="interested_5">
                                                    <label class="form-check-label" for="interested_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_interested_and_motivated" value="4" id="interested_4">
                                                    <label class="form-check-label" for="interested_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_interested_and_motivated" value="3" id="interested_3" checked>
                                                    <label class="form-check-label" for="interested_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_interested_and_motivated" value="2" id="interested_2">
                                                    <label class="form-check-label" for="interested_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_interested_and_motivated" value="1" id="interested_1">
                                                    <label class="form-check-label" for="interested_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>My responsibilities are clearly assigned by the leader.</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_responsibilities_assigned" value="5" id="responsibilities_5">
                                                    <label class="form-check-label" for="responsibilities_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_responsibilities_assigned" value="4" id="responsibilities_4">
                                                    <label class="form-check-label" for="responsibilities_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_responsibilities_assigned" value="3" id="responsibilities_3" checked>
                                                    <label class="form-check-label" for="responsibilities_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_responsibilities_assigned" value="2" id="responsibilities_2">
                                                    <label class="form-check-label" for="responsibilities_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_responsibilities_assigned" value="1" id="responsibilities_1">
                                                    <label class="form-check-label" for="responsibilities_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>I feel welcomed by the team</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_feel_welcomed_by_team" value="5" id="welcomed_5">
                                                    <label class="form-check-label" for="welcomed_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_feel_welcomed_by_team" value="4" id="welcomed_4">
                                                    <label class="form-check-label" for="welcomed_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_feel_welcomed_by_team" value="3" id="welcomed_3" checked>
                                                    <label class="form-check-label" for="welcomed_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_feel_welcomed_by_team" value="2" id="welcomed_2">
                                                    <label class="form-check-label" for="welcomed_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_feel_welcomed_by_team" value="1" id="welcomed_1">
                                                    <label class="form-check-label" for="welcomed_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>There is good team bonding and i know everyone.</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_team_bonding" value="5" id="bonding_5">
                                                    <label class="form-check-label" for="bonding_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_team_bonding" value="4" id="bonding_4">
                                                    <label class="form-check-label" for="bonding_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_team_bonding" value="3" id="bonding_3" checked>
                                                    <label class="form-check-label" for="bonding_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_team_bonding" value="2" id="bonding_2">
                                                    <label class="form-check-label" for="bonding_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_team_bonding" value="1" id="bonding_1">
                                                    <label class="form-check-label" for="bonding_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>The team motivates me in my work.</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_team_motivates" value="5" id="motivates_5">
                                                    <label class="form-check-label" for="motivates_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_team_motivates" value="4" id="motivates_4">
                                                    <label class="form-check-label" for="motivates_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_team_motivates" value="3" id="motivates_3" checked>
                                                    <label class="form-check-label" for="motivates_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_team_motivates" value="2" id="motivates_2">
                                                    <label class="form-check-label" for="motivates_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_team_motivates" value="1" id="motivates_1">
                                                    <label class="form-check-label" for="motivates_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>I am comfortable giving feedback to my leader.</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_comfortable_giving_feedback" value="5" id="feedback_5">
                                                    <label class="form-check-label" for="feedback_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_comfortable_giving_feedback" value="4" id="feedback_4">
                                                    <label class="form-check-label" for="feedback_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_comfortable_giving_feedback" value="3" id="feedback_3" checked>
                                                    <label class="form-check-label" for="feedback_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_comfortable_giving_feedback" value="2" id="feedback_2">
                                                    <label class="form-check-label" for="feedback_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_comfortable_giving_feedback" value="1" id="feedback_1">
                                                    <label class="form-check-label" for="feedback_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>My manager is supportive and approachable regarding queries.</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_manager_supportive" value="5" id="manager_5">
                                                    <label class="form-check-label" for="manager_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_manager_supportive" value="4" id="manager_4">
                                                    <label class="form-check-label" for="manager_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_manager_supportive" value="3" id="manager_3" checked>
                                                    <label class="form-check-label" for="manager_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_manager_supportive" value="2" id="manager_2">
                                                    <label class="form-check-label" for="manager_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_manager_supportive" value="1" id="manager_1">
                                                    <label class="form-check-label" for="manager_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>I learn skills from my manager</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_learn_from_manager" value="5" id="learn_5">
                                                    <label class="form-check-label" for="learn_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_learn_from_manager" value="4" id="learn_4">
                                                    <label class="form-check-label" for="learn_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_learn_from_manager" value="3" id="learn_3" checked>
                                                    <label class="form-check-label" for="learn_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_learn_from_manager" value="2" id="learn_2">
                                                    <label class="form-check-label" for="learn_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_learn_from_manager" value="1" id="learn_1">
                                                    <label class="form-check-label" for="learn_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>I understand my work, Departmental Goals and targets for myself to achieve.</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_goals" value="5" id="goals_5">
                                                    <label class="form-check-label" for="goals_5"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_goals" value="4" id="goals_4">
                                                    <label class="form-check-label" for="goals_4"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_goals" value="3" id="goals_3" checked>
                                                    <label class="form-check-label" for="goals_3"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_goals" value="2" id="goals_2">
                                                    <label class="form-check-label" for="goals_2"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="i_understand_goals" value="1" id="goals_1">
                                                    <label class="form-check-label" for="goals_1"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Process & Documentation Checklist in Table Format -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="font-weight-bold">Process & Documentation Checklist</h6>
                            <hr>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50%">Question</th>
                                            <th width="25%" class="text-center">Yes</th>
                                            <th width="25%" class="text-center">No</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Joining Process -->
                                        <tr>
                                            <td><strong>Joining Process</strong></td>
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td>Joining Designation</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_joining_designation" value="1" id="joining_designation_yes" checked>
                                                    <label class="form-check-label" for="joining_designation_yes"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_joining_designation" value="0" id="joining_designation_no">
                                                    <label class="form-check-label" for="joining_designation_no"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Date of Joining</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_joining_doj" value="1" id="joining_doj_yes" checked>
                                                    <label class="form-check-label" for="joining_doj_yes"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_joining_doj" value="0" id="joining_doj_no">
                                                    <label class="form-check-label" for="joining_doj_no"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ID Card</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_joining_id_card" value="1" id="joining_id_card_yes" checked>
                                                    <label class="form-check-label" for="joining_id_card_yes"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_joining_id_card" value="0" id="joining_id_card_no">
                                                    <label class="form-check-label" for="joining_id_card_no"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Bank Account</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_joining_bank_account" value="1" id="joining_bank_account_yes" checked>
                                                    <label class="form-check-label" for="joining_bank_account_yes"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_joining_bank_account" value="0" id="joining_bank_account_no">
                                                    <label class="form-check-label" for="joining_bank_account_no"></label>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Documentation -->
                                        <tr>
                                            <td><strong>Documentation</strong></td>
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td>Appointment Letter</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_doc_appointment_letter" value="1" id="doc_appointment_yes" checked>
                                                    <label class="form-check-label" for="doc_appointment_yes"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_doc_appointment_letter" value="0" id="doc_appointment_no">
                                                    <label class="form-check-label" for="doc_appointment_no"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>List of Holidays</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_doc_list_of_holidays" value="1" id="doc_holidays_yes" checked>
                                                    <label class="form-check-label" for="doc_holidays_yes"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_doc_list_of_holidays" value="0" id="doc_holidays_no">
                                                    <label class="form-check-label" for="doc_holidays_no"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>HRMS Login</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_doc_hrms_login" value="1" id="doc_hrms_yes" checked>
                                                    <label class="form-check-label" for="doc_hrms_yes"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_doc_hrms_login" value="0" id="doc_hrms_no">
                                                    <label class="form-check-label" for="doc_hrms_no"></label>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Team -->
                                        <tr>
                                            <td><strong>Team Integration</strong></td>
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td>Team Leader Introduction</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_team_leader_intro" value="1" id="team_leader_yes" checked>
                                                    <label class="form-check-label" for="team_leader_yes"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_team_leader_intro" value="0" id="team_leader_no">
                                                    <label class="form-check-label" for="team_leader_no"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Team Introduction</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_team_intro" value="1" id="team_intro_yes" checked>
                                                    <label class="form-check-label" for="team_intro_yes"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_team_intro" value="0" id="team_intro_no">
                                                    <label class="form-check-label" for="team_intro_no"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Teamwork Allocation</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_teamwork_allocation" value="1" id="teamwork_yes" checked>
                                                    <label class="form-check-label" for="teamwork_yes"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_teamwork_allocation" value="0" id="teamwork_no">
                                                    <label class="form-check-label" for="teamwork_no"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Team Satisfaction</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_team_satisfaction" value="1" id="team_satisfaction_yes" checked>
                                                    <label class="form-check-label" for="team_satisfaction_yes"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_team_satisfaction" value="0" id="team_satisfaction_no">
                                                    <label class="form-check-label" for="team_satisfaction_no"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Work Satisfaction</td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_work_satisfaction" value="1" id="work_satisfaction_yes" checked>
                                                    <label class="form-check-label" for="work_satisfaction_yes"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-inline-block">
                                                    <input class="form-check-input" type="radio" name="b_work_satisfaction" value="0" id="work_satisfaction_no">
                                                    <label class="form-check-label" for="work_satisfaction_no"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Suggestion Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="font-weight-bold">Suggestions</h6>
                            <hr>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="t_suggestion" class="form-label">Suggestions/Comments</label>
                            <textarea class="form-control" id="t_suggestion" name="t_suggestion" rows="4"
                                      placeholder="Please share any suggestions or comments..."></textarea>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-2"></i>Submit Feedback
                            </button>
                            <a href="{{ route('employee-master.profile', $employee->i_id) }}" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
// Form submission with AJAX
$('#feedbackForm').on('submit', function(e) {
    e.preventDefault();

    // Create FormData and ensure all radio buttons are included
    var formData = new FormData(this);

    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                alert('Feedback submitted successfully!');
                window.location.href = '/my-profile';
            } else {
                alert('Error submitting feedback: ' + response.message);
            }
        },
        error: function(xhr) {
            console.log('XHR Error:', xhr);
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = 'Please fix the following errors:\n';
                for (var field in errors) {
                    errorMessage += errors[field].join(', ') + '\n';
                }
                alert(errorMessage);
            } else if (xhr.status === 500) {
                alert('Internal server error. Please check the console for more details.');
            } else {
                alert('Error submitting feedback. Please try again. Status: ' + xhr.status);
            }
        }
    });
});
</script>

@endsection
