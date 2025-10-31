@extends('includes/header')
 
@section('pageTitle', 'Six Month Employee Feedback Form')
 
@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">Six Month Employee Feedback</h1>
        <div class="ml-auto">
            <a href="{{ route('employee-master.profile', $employee->i_id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back to Employee Profile
            </a>
        </div>
    </div>
 
    <div class="container-fluid pt-3">
        @if(!empty($viewOnly) && $viewOnly)
            <div class="alert alert-success mb-3">
                <i class="fa fa-check-circle"></i> Feedback already submitted. The form is in view-only mode.
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Six Month Feedback - {{ $employee->v_employee_full_name }} ({{ $employee->v_employee_code }})</h5>
            </div>
            <div class="card-body">
                <form id="sixMonthFeedbackForm" method="POST" action="{{ route('employee-feedback-six.store', $employee->i_id) }}">
                    @csrf
 
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="font-weight-bold">Employee Details</h6>
                            <hr>
                        </div>
                    </div>
 
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Employee Code *</label>
                            <input type="text" class="form-control" name="v_emp_code" value="{{ $employee->v_employee_code }}" readonly style="background-color:#f8f9fa;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Employee Name *</label>
                            <input type="text" class="form-control" name="v_employee_name" value="{{ $employee->v_employee_full_name }}" readonly style="background-color:#f8f9fa;">
                        </div>
                    </div>
 
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Department Name *</label>
                            <input type="text" class="form-control" name="v_department_name" value="{{ $employee->teamInfo->v_value ?? '' }}" readonly style="background-color:#f8f9fa;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Designation *</label>
                            <input type="text" class="form-control" name="v_designation" value="{{ $employee->designationInfo->v_value ?? '' }}" readonly style="background-color:#f8f9fa;">
                        </div>
                    </div>
 
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Date Of Joining *</label>
                            <input type="date" class="form-control" name="dt_date_of_joining" value="{{ $employee->dt_joining_date }}" readonly style="background-color:#f8f9fa;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date Of Assessment *</label>
                            <input type="date" class="form-control" name="dt_date_of_assessment" value="{{ isset($existingFeedback->dt_date_of_assessment) ? $existingFeedback->dt_date_of_assessment : date('Y-m-d') }}" {{ (!empty($viewOnly) && $viewOnly) ? 'readonly' : '' }}>
                        </div>
                    </div>
 
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="font-weight-bold">7. Team Collaboration & Communication (1-5)</h6>
                            <hr>
                        </div>
                    </div>
                    @php $disabledAttr = (!empty($viewOnly) && $viewOnly) ? 'disabled' : ''; @endphp
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50%">Question</th>
                                            <th class="text-center">5</th>
                                            <th class="text-center">4</th>
                                            <th class="text-center">3</th>
                                            <th class="text-center">2</th>
                                            <th class="text-center">1</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>I feel a strong sense of teamwork and collaboration within my team.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center">
                                                    <input type="radio" name="i_teamwork_collaboration" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_teamwork_collaboration == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}>
                                                </td>
                                            @endfor
                                        </tr>
                                        <tr>
                                            <td>Team communication is clear, timely, and effective.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center">
                                                    <input type="radio" name="i_team_communication" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_team_communication == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}>
                                                </td>
                                            @endfor
                                        </tr>
                                        <tr>
                                            <td>My team members are supportive and respectful.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center">
                                                    <input type="radio" name="i_team_support" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_team_support == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}>
                                                </td>
                                            @endfor
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
 
                    <div class="form-group mb-4">
                        <label>Are there any team-related issues or conflicts you'd like to share?</label>
                        <textarea class="form-control" name="t_team_issues_conflicts" rows="3" {{ $disabledAttr }}>{{ $existingFeedback->t_team_issues_conflicts ?? '' }}</textarea>
                    </div>
 
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="font-weight-bold">8. Manager Feedback (1-5)</h6>
                            <hr>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td width="50%">My manager provides clear guidance and sets expectations effectively.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center"><input type="radio" name="i_manager_guidance" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_manager_guidance == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}></td>
                                            @endfor
                                        </tr>
                                        <tr>
                                            <td>My manager provides timely feedback on my work.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center"><input type="radio" name="i_manager_feedback_timely" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_manager_feedback_timely == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}></td>
                                            @endfor
                                        </tr>
                                        <tr>
                                            <td>Team meetings are productive and promote open discussion.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center"><input type="radio" name="i_team_meeting_effective" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_team_meeting_effective == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}></td>
                                            @endfor
                                        </tr>
                                        <tr>
                                            <td>My efforts are recognized and appreciated by my manager.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center"><input type="radio" name="i_efforts_recognized" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_efforts_recognized == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}></td>
                                            @endfor
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
 
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="font-weight-bold">9. Company Culture & Alignment (1-5)</h6>
                            <hr>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td width="50%">I understand and feel aligned with the company's mission, vision, and values.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center"><input type="radio" name="i_understand_mission" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_understand_mission == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}></td>
                                            @endfor
                                        </tr>
                                        <tr>
                                            <td>The company culture promotes inclusion, respect, and collaboration.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center"><input type="radio" name="i_company_culture_respect" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_company_culture_respect == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}></td>
                                            @endfor
                                        </tr>
                                        <tr>
                                            <td>Internal communication from leadership and HR is effective.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center"><input type="radio" name="i_internal_communication" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_internal_communication == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}></td>
                                            @endfor
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
 
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="font-weight-bold">10. Growth & Development (1-5)</h6>
                            <hr>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td width="50%">I have access to growth and skill development opportunities.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center"><input type="radio" name="i_growth_opportunities" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_growth_opportunities == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}></td>
                                            @endfor
                                        </tr>
                                        <tr>
                                            <td>I am satisfied with my career progression conversations so far.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center"><input type="radio" name="i_career_progression" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_career_progression == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}></td>
                                            @endfor
                                        </tr>
                                        <tr>
                                            <td>I am satisfied with the company's approach to work-life balance.</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td class="text-center"><input type="radio" name="i_worklife_balance" value="{{ $i }}" {{ (isset($existingFeedback) && $existingFeedback->i_worklife_balance == $i) ? 'checked' : (!isset($existingFeedback) && $i==3 ? 'checked' : '') }} {{ $disabledAttr }}></td>
                                            @endfor
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
 
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="font-weight-bold">11. Ratings (0-10)</h6>
                            <hr>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Manager guidance ability</label>
                            <input type="number" min="0" max="10" class="form-control" name="i_manager_guidance_rating" value="{{ $existingFeedback->i_manager_guidance_rating ?? 5 }}" {{ $disabledAttr }}>
                        </div>
                        <div class="col-md-4">
                            <label>Team meeting effectiveness</label>
                            <input type="number" min="0" max="10" class="form-control" name="i_meeting_effectiveness_rating" value="{{ $existingFeedback->i_meeting_effectiveness_rating ?? 5 }}" {{ $disabledAttr }}>
                        </div>
                        <div class="col-md-4">
                            <label>Overall manager satisfaction</label>
                            <input type="number" min="0" max="10" class="form-control" name="i_manager_satisfaction_rating" value="{{ $existingFeedback->i_manager_satisfaction_rating ?? 5 }}" {{ $disabledAttr }}>
                        </div>
                    </div>
 
                    <div class="form-group mb-3">
                        <label>Are there any processes, tools, or systems that you think need improvement?</label>
                        <textarea class="form-control" name="t_improvement_suggestions" rows="3" {{ $disabledAttr }}>{{ $existingFeedback->t_improvement_suggestions ?? '' }}</textarea>
                    </div>
 
                    <div class="form-group mb-3">
                        <label>Do you feel that the company provides sufficient opportunities for professional growth and Development?</label>
                        <div>
                            <label class="mr-3"><input type="radio" name="b_growth_opportunities_available" value="1" {{ (isset($existingFeedback) && $existingFeedback->b_growth_opportunities_available==1)?'checked':'' }} {{ $disabledAttr }}> Yes</label>
                            <label class="mr-3"><input type="radio" name="b_growth_opportunities_available" value="0" {{ (isset($existingFeedback) && $existingFeedback->b_growth_opportunities_available==0)?'checked':'' }} {{ $disabledAttr }}> No</label>
                        </div>
                    </div>
 
                    <div class="form-group mb-3">
                        <label>Other growth opportunities</label>
                        <textarea class="form-control" name="t_growth_opportunities_other" rows="2" {{ $disabledAttr }}>{{ $existingFeedback->t_growth_opportunities_other ?? '' }}</textarea>
                    </div>
 
                    <div class="form-group mb-4">
                        <label>Any ideas or suggestions that could help improve productivity or efficiency?</label>
                        <textarea class="form-control" name="t_productivity_suggestions" rows="3" {{ $disabledAttr }}>{{ $existingFeedback->t_productivity_suggestions ?? '' }}</textarea>
                    </div>
 
                    @if(empty($viewOnly) || !$viewOnly)
                        <button type="submit" class="btn btn-theme text-white">Submit</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</main>
@endsection