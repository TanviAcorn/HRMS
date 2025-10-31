@extends('includes/header')

@section('pageTitle', $pageTitle)

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0">Probation Assessment Form</h1>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <a href="{{ url('probation-assessments') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
        </div>
    </div>

    <div class="container-fluid pt-3">
        @if(session('danger'))
            <div class="alert alert-danger">{{ session('danger') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ url('probation-assessments/' . $employee->i_id) }}" method="POST">
            @csrf

            <div class="card mb-3">
                <div class="card-header"><strong>1) Employee Details</strong> <small class="text-muted">(Auto-filled, Non-editable)</small></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="control-label">Department Name</label>
                            <input type="text" class="form-control" value="{{ (string) data_get($employee, 'teamInfo.v_value', '') }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="control-label">Joining Date</label>
                            <input type="text" class="form-control" value="{{ (string) ($employee->dt_joining_date ? \Carbon\Carbon::parse($employee->dt_joining_date)->format('Y-m-d') : '') }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="control-label">Probation end date</label>
                            <input type="text" class="form-control" value="{{ (string) (!empty($employee->dt_probation_end_date) ? \Carbon\Carbon::parse($employee->dt_probation_end_date)->format('Y-m-d') : '') }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="control-label">Team Leader Name</label>
                            <input type="text" class="form-control" value="{{ (string) data_get($employee, 'leaderInfo.v_employee_full_name', '') }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="control-label">Employee Name</label>
                            <input type="text" class="form-control" value="{{ (string) $employee->v_employee_full_name }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="control-label">Employee Code</label>
                            <input type="text" class="form-control" value="{{ (string) $employee->v_employee_code }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="control-label">Leave in Probation (days)</label>
                            <input type="number" name="leave_in_probation" class="form-control" min="0" step="0.01" value="{{ (string) old('leave_in_probation', data_get($assessment, 'i_leave_in_probation')) }}" {{ !empty($readOnly) ? 'readonly' : '' }}>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><strong>2) Assessment</strong></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-left">Particulars</th>
                                    <th style="width:110px">Weightage</th>
                                    <th style="width:140px">Score (0-2)</th>
                                    <th class="text-left">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rows = [
                                        'quality' => 'Quality and accuracy of work',
                                        'efficiency' => 'Work Efficiency',
                                        'attendance' => 'Attendance & Time Keeping',
                                        'teamwork_communication' => 'Teamwork, Communication & Technical Skills',
                                        'competency' => 'Competency in the Role',
                                    ];
                                    // For backward compatibility, use teamwork_communication value if available, otherwise use teamwork
                                    $oldScores = old('score', []);
                                    $oldRemarks = old('remarks', []);
                                @endphp
                                @foreach($rows as $key => $label)
                                    <tr>
                                        <td class="text-left">{{ $label }}</td>
                                        <td class="text-center">2</td>
                                        <td>
                                            @if($key === 'teamwork_communication')
                                                @php
                                                    $teamworkScore = data_get($assessment, 'i_teamwork_score');
                                                    $communicationScore = data_get($assessment, 'i_communication_score');
                                                    $displayScore = old('score.'.$key, $teamworkScore ?? $communicationScore);
                                                @endphp
                                                <input type="number" name="score[{{ $key }}]" class="form-control score-input" min="0" max="2" step="0.01"
                                                       value="{{ number_format((float)$displayScore, 2, '.', '') }}" 
                                                       {{ !empty($readOnly) ? 'readonly' : '' }}>
                                            @else
                                                <input type="number" name="score[{{ $key }}]" class="form-control score-input" min="0" max="2" step="0.01"
                                                       value="{{ number_format((float)old('score.'.$key, data_get($assessment, 'i_'.$key.'_score')), 2, '.', '') }}" 
                                                       {{ !empty($readOnly) ? 'readonly' : '' }}>
                                            @endif
                                        </td>
                                        <td>
                                            @if($key === 'teamwork_communication')
                                                @php
                                                    $teamworkRemarks = data_get($assessment, 'vch_teamwork_remarks');
                                                    $communicationRemarks = data_get($assessment, 'vch_communication_remarks');
                                                    $displayRemarks = old('remarks.'.$key, $teamworkRemarks ?? $communicationRemarks);
                                                @endphp
                                                <input type="text" name="remarks[{{ $key }}]" class="form-control" 
                                                       value="{{ (string) $displayRemarks }}" 
                                                       {{ !empty($readOnly) ? 'readonly' : '' }}>
                                            @else
                                                <input type="text" name="remarks[{{ $key }}]" class="form-control" 
                                                       value="{{ (string) old('remarks.'.$key, data_get($assessment, 'vch_'.$key.'_remarks')) }}" 
                                                       {{ !empty($readOnly) ? 'readonly' : '' }}>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="text-left font-weight-bold">Total</td>
                                    <td class="text-center font-weight-bold">10</td>
                                    <td class="text-center font-weight-bold" id="total-score">0.0</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><strong>3) Objectives and Training</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="d-block">Have the objectives identified for the probationary period been met?</label>
                            <select name="objectives_met" class="form-control" {{ !empty($readOnly) ? 'disabled' : '' }}>
                                <option value="Yes" {{ old('objectives_met', data_get($assessment, 'e_objectives_met')) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('objectives_met', data_get($assessment, 'e_objectives_met')) == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>If NO, please provide details</label>
                            <input type="text" name="objectives_details" class="form-control" value="{{ (string) old('objectives_details', data_get($assessment, 'vch_objectives_details')) }}" {{ !empty($readOnly) ? 'readonly' : '' }}>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="d-block">Have the training / development needs identified for the probationary period been addressed?</label>
                            <select name="training_addressed" class="form-control" {{ !empty($readOnly) ? 'disabled' : '' }}>
                                <option value="Yes" {{ old('training_addressed', data_get($assessment, 'e_training_addressed')) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('training_addressed', data_get($assessment, 'e_training_addressed')) == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>If YES, please provide details</label>
                            <input type="text" name="training_details" class="form-control" value="{{ (string) old('training_details', data_get($assessment, 'vch_training_details')) }}" {{ !empty($readOnly) ? 'readonly' : '' }}>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><strong>4) Decision</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Decision</label>
                            <select name="decision" id="decision" class="form-control" {{ !empty($readOnly) ? 'disabled' : '' }}>
                                <option value="">-- Select Decision --</option>
                                <option value="confirm" {{ (isset($assessment) && old('decision', $assessment->vch_decision) == 'confirm') ? 'selected' : '' }}>To be Confirm</option>
                                <option value="extend" {{ (isset($assessment) && old('decision', $assessment->vch_decision) == 'extend') ? 'selected' : '' }}>Extend Probation</option>
                            </select>
                        </div>
                        <div id="extendFields" class="col-12" style="display: none;">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>Extend up to (months)</label>
                                    <input type="number" name="extend_months" id="extend_months" class="form-control" min="1" max="24" value="{{ (string) old('extend_months', data_get($assessment, 'i_extend_months')) }}" {{ !empty($readOnly) ? 'readonly' : '' }}>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Extended till Date</label>
                                    <input type="date" name="extend_upto_date" id="extend_upto_date" class="form-control" value="{{ (string) old('extend_upto_date', data_get($assessment, 'dt_extend_upto_date')) }}" {{ !empty($readOnly) ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(empty($readOnly))
            <div class="card mb-3">
                <div class="card-body d-flex gap">
                    <button type="submit" name="submit_status" value="draft" class="btn btn-secondary mr-2">Save Draft</button>
                    <button type="submit" name="submit_status" value="submit" class="btn btn-theme text-white">Submit</button>
                </div>
            </div>
            @endif
        </form>
    </div>
</main>

<script>
(function(){
    // Function to calculate extended date
    function calculateExtendedDate() {
        const extendMonths = parseInt(document.getElementById('extend_months').value);
        if (!isNaN(extendMonths) && extendMonths > 0) {
            // Get the current probation end date from the employee data
            const probationEndDate = '{{ $employee->dt_probation_end_date }}';
            if (probationEndDate) {
                const endDate = new Date(probationEndDate);
                // Add the extended months to the probation end date
                endDate.setMonth(endDate.getMonth() + extendMonths);
                
                // Format the date as YYYY-MM-DD for the date input
                const year = endDate.getFullYear();
                const month = String(endDate.getMonth() + 1).padStart(2, '0');
                const day = String(endDate.getDate()).padStart(2, '0');
                const formattedDate = `${year}-${month}-${day}`;
                
                // Set the date field value
                document.getElementById('extend_upto_date').value = formattedDate;
            }
        }
    }

    // Add event listener to extend_months field
    document.getElementById('extend_months').addEventListener('input', calculateExtendedDate);
    
    // Show/hide extend fields based on decision
    const decisionSelect = document.getElementById('decision');
    const extendFields = document.getElementById('extendFields');
    
    function toggleExtendFields() {
        if (decisionSelect.value === 'extend') {
            extendFields.style.display = 'block';
        } else {
            extendFields.style.display = 'none';
        }
    }
    
    // Initial toggle and add change event listener
    toggleExtendFields();
    decisionSelect.addEventListener('change', toggleExtendFields);
    // Calculate total score with precise decimal calculation
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.score-input').forEach(input => {
            // Convert input value to a number with 2 decimal places to avoid floating point precision issues
            const value = Math.round(parseFloat(input.value || 0) * 100) / 100;
            total = Math.round((total + value) * 100) / 100; // Keep running total precise
        });
        // Ensure exactly 2 decimal places, even if they are .00
        document.getElementById('total-score').textContent = total.toFixed(2);
    }

    // Add event listeners to all score inputs
    document.addEventListener('DOMContentLoaded', function() {
        const scoreInputs = document.querySelectorAll('.score-input');
        scoreInputs.forEach(input => {
            input.addEventListener('input', calculateTotal);
            // Validate max value
            input.addEventListener('change', function() {
                if (this.value > 2) {
                    this.value = 2;
                }
                calculateTotal();
            });
        });
        // Initial calculation
        calculateTotal();
    });

    function toggleExtend(){
        var val = document.getElementById('decision').value;
        var block = document.getElementById('extendFields');
        if(!block) return;
        block.style.display = (val === 'extend') ? 'block' : 'none';
    }
    document.addEventListener('DOMContentLoaded', function(){
        var d = document.getElementById('decision');
        if(d){ d.addEventListener('change', toggleExtend); toggleExtend(); }
    });
})();
</script>
@endsection
