@extends('includes/header')
 
@section('pageTitle', $pageTitle)
 
@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0">Performance Appraisal Form</h1>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            @if(($appraisal && in_array($appraisal->vch_status, ['submitted','completed'])) && isset($overallScore))
                <span class="badge badge-primary mr-2 p-2">Score: {{ $overallScore }} / 100</span>
            @endif
            <a href="{{ url('performance-appraisals') }}" class="btn btn-primary btn-sm">Back to List</a>
        </div>
    </div>
 
    <div class="container-fluid pt-3">
        <form action="{{ url('performance-appraisals/' . $employee->i_id) }}" method="POST" id="appraisalForm" novalidate>
            @csrf
 
            <div class="card mb-3">
                <div class="card-header"><strong>1) Employee Details</strong> <small class="text-muted">(Auto-filled, Non-editable)</small></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="control-label">Employee Name</label>
                            <input type="text" class="form-control" value="{{ $employee->v_employee_full_name }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="control-label">Employee Code</label>
                            <input type="text" class="form-control" value="{{ $employee->v_employee_code }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="control-label">Department</label>
                            <input type="text" class="form-control" value="{{ optional($employee->teamInfo)->v_value }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="control-label">Designation</label>
                            <input type="text" class="form-control" value="{{ optional($employee->designationInfo)->v_value }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="control-label">Appraisal Period</label>
                            <input type="text" class="form-control" value="{{ optional($period)->vch_name }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="control-label">L1 Name</label>
                            <input type="text" class="form-control" value="{{ $managerName ?? '' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
 
<div class="card mb-3">
                <div class="card-header"><strong>2) Job Roles & Responsibilities (50 marks)</strong></div>
                <div class="card-body">
                    @if(($roleItems ?? collect())->count() === 0 && empty($readOnly))
                        <div class="alert alert-info">Add up to 5 key roles for this employee, then click "Save Roles" to start rating them.</div>
                        <div class="row role-editor">
                            @for($r=1;$r<=5;$r++)
                                <div class="col-md-12 mb-2">
                                    <input type="text" name="new_roles[]" class="form-control role-input" placeholder="Role {{ $r }}">
                                </div>
                            @endfor
                        </div>
                        <button type="submit" class="btn btn-primary save-roles-btn" name="submit_status" value="save_roles" disabled>Save Roles</button>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-sm mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width:60px">Sr No.</th>
                                        <th class="text-left" style="width:45%">Role Description</th>
                                        <th>5 (Outstanding)</th>
                                    <th>4 (Exceeds Expectations)</th>
                                    <th>3 (Meets Expectation)</th>
                                    <th>2 (Needs Improvement)</th>
                                    <th>1 (Below Expectation)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sr2=1; @endphp
                                    @forelse($roleItems as $item)
                                        <tr class="text-center">
                                            <td>{{ $sr2++ }}</td>
                                            <td class="text-left">{{ $item->vch_role }}</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td>
                                                    <input type="radio" name="job_role[{{ $item->i_id ?? $item->id ?? $item->i_id }}]" value="{{ $i }}" {{ (isset($existingRatings['job_role'][$item->i_id ?? $item->id ?? $item->i_id]) && (int)$existingRatings['job_role'][$item->i_id ?? $item->id ?? $item->i_id] === $i) ? 'checked' : '' }} {{ !empty($readOnly) ? 'disabled' : '' }}>
                                                </td>
                                            @endfor
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No roles added yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(empty($readOnly) && ($roleItems ?? collect())->count() > 0 && ($appraisal && $appraisal->vch_status === 'draft'))
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse" data-target="#edit-roles-box">Edit Roles</button>
                            </div>
                            <div id="edit-roles-box" class="collapse mt-2">
                                <div class="alert alert-info py-2 mb-2">Update the 5 roles and click "Save Roles". Exactly 5 roles are required.</div>
                                <div class="row role-editor">
                                    @php
                                        $prefilled = ($roleItems ?? collect())->pluck('vch_role')->values()->toArray();
                                        for($pad=count($prefilled); $pad<5; $pad++){ $prefilled[$pad] = ''; }
                                    @endphp
                                    @for($r=0;$r<5;$r++)
                                        <div class="col-md-12 mb-2">
                                            <input type="text" name="new_roles[]" class="form-control role-input" placeholder="Role {{ $r+1 }}" value="{{ $prefilled[$r] }}">
                                        </div>
                                    @endfor
                                </div>
                                <button type="submit" class="btn btn-primary save-roles-btn" name="submit_status" value="save_roles" disabled>Save Roles</button>
                            </div>
                        @endif
                        @if(empty($readOnly) && ($roleItems ?? collect())->count() === 0)
                            <small class="text-muted">Add roles to enable rating.</small>
                        @endif
                    @endif
                </div>
            </div>
 
            <div class="card mb-3">
                <div class="card-header"><strong>3) Job Attributes (40 marks)</strong></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th style="width:60px">Sr No.</th>
                                    <th class="text-left" style="width:45%">Attribute</th>
                                    <th>5 (Outstanding)</th>
                                    <th>4 (Exceeds Expectations)</th>
                                    <th>3 (Meets Expectation)</th>
                                    <th>2 (Needs Improvement)</th>
                                    <th>1 (Below Expectation)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sr=1; @endphp
                                @foreach($attributes as $attr)
                                    @if(!isset($attr->is_auto_calculated) || !$attr->is_auto_calculated)
                                        <tr class="text-center">
                                            <td>{{ $sr++ }}</td>
                                            <td class="text-left">{{ $attr->vch_name }}</td>
                                            @for($i=5;$i>=1;$i--)
                                                <td>
                                                    <input type="radio" name="attribute[{{ $attr->i_id }}]" value="{{ $i }}" {{ (isset($existingRatings['attribute'][$attr->i_id]) && (int)$existingRatings['attribute'][$attr->i_id] === $i) ? 'checked' : '' }} {{ !empty($readOnly) ? 'disabled' : '' }}>
                                                </td>
                                            @endfor
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
 
            <!-- HR Section -->
            <div class="card mb-3">
                <div class="card-header"><strong>4) HR Section (10 marks)</strong></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th style="width:60px">#</th>
                                    <th class="text-left">Parameter</th>
                                    <th style="width:200px">Rating (1-10)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $hrAttribute = $attributes->first(fn($attr) => isset($attr->is_auto_calculated) && $attr->is_auto_calculated) @endphp
                                @if($hrAttribute)
                                    <tr>
                                        <td>1</td>
                                        <td class="text-left">HR Rating</td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div class="text-center">
                                                    <h4 class="mb-0">{{ $hrAttribute->hr_rating ?? 'N/A' }} / 10</h4>
                                                    <small class="text-muted">HR Section Score</small>
                                                </div>
                                            </div>
                                            <input type="hidden" name="attribute[{{ $hrAttribute->i_id }}]" value="{{ $hrAttribute->calculated_rating ?? 3 }}">
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
 
            @if(empty($readOnly))
            <div class="card mb-3">
                <div class="card-header"><strong>4) L2 Manager Consultation</strong> <span class="text-danger">*</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Have you consulted this with L2 Manager of the Employee? <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input l2-consultation" type="radio" name="l2_consultation" id="l2_yes" value="yes" required>
                            <label class="form-check-label" for="l2_yes">
                                Yes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input l2-consultation" type="radio" name="l2_consultation" id="l2_no" value="no">
                            <label class="form-check-label" for="l2_no">
                                No
                            </label>
                        </div>
                        <div id="l2-warning" class="alert alert-warning mt-2 mb-0">
                            Please consult with L2 manager before submitting. The form can only be submitted after selecting 'Yes'.
                        </div>
                    </div>
                </div>
            </div>
 
            <div class="card mb-3">
                <div class="card-body d-flex gap">
                    <button type="button" id="clear-ratings" class="btn btn-outline-danger mr-3">Clear Selection</button>
                    <button type="submit" name="submit_status" value="draft" class="btn btn-secondary mr-2">Save Draft</button>
                    <button type="submit" name="submit_status" value="submit" id="submit-btn" class="btn btn-theme text-white" disabled>Submit</button>
                </div>
            </div>
            @endif
        </form>
    </div>
</main>
<script>
    // L2 Manager Consultation Validation
    (function(){
        function updateFormState() {
            var l2NoRadio = document.getElementById('l2_no');
            var l2YesRadio = document.getElementById('l2_yes');
            var l2Warning = document.getElementById('l2-warning');
            var submitBtn = document.getElementById('submit-btn');
           
            if (l2YesRadio && l2YesRadio.checked) {
                l2Warning.style.display = 'none';
                if (submitBtn) submitBtn.disabled = false;
            } else {
                l2Warning.style.display = 'block';
                if (submitBtn) submitBtn.disabled = true;
            }
        }
       
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            var l2NoRadio = document.getElementById('l2_no');
            var l2YesRadio = document.getElementById('l2_yes');
           
            if (l2NoRadio) l2NoRadio.addEventListener('change', updateFormState);
            if (l2YesRadio) l2YesRadio.addEventListener('change', updateFormState);
           
            // Initial state check
            updateFormState();
           
            // Form submit handler
            var form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    var submitBtn = e.submitter || document.activeElement;
                    var isSavingRoles = submitBtn && submitBtn.name === 'submit_status' && submitBtn.value === 'save_roles';
                    var isSavingDraft = submitBtn && submitBtn.name === 'submit_status' && submitBtn.value === 'draft';
                   
                    // For role saving, bypass all validations and submit immediately
                    if (isSavingRoles) {
                        e.preventDefault(); // Prevent default form submission
                       
                        // Create a new form specifically for role saving
                        var roleForm = document.createElement('form');
                        roleForm.method = 'POST';
                        roleForm.action = form.action;
                        roleForm.style.display = 'none';
                       
                        // Add CSRF token
                        var csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = document.querySelector('input[name="_token"]').value;
                        roleForm.appendChild(csrfInput);
                       
                        // Add submit status
                        var statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'submit_status';
                        statusInput.value = 'save_roles';
                        roleForm.appendChild(statusInput);
                       
                        // Add role inputs
                        var roleInputs = document.querySelectorAll('input[name^="new_roles"]');
                        roleInputs.forEach(function(input) {
                            var newInput = document.createElement('input');
                            newInput.type = 'hidden';
                            newInput.name = input.name;
                            newInput.value = input.value;
                            roleForm.appendChild(newInput);
                        });
                       
                        // Submit the form
                        document.body.appendChild(roleForm);
                        roleForm.submit();
                        return false;
                    }
                   
                    // For draft saving, bypass L2 validation
                    if (isSavingDraft) {
                        return true;
                    }
                   
                    // For final submission, check L2 consultation
                    var l2NoRadio = document.getElementById('l2_no');
                    if (l2NoRadio && l2NoRadio.checked) {
                        e.preventDefault();
                        return false;
                    }
                   
                    return true;
                });
            }
        });
    })();
 
    // Clear ratings functionality
    (function(){
        var btn = document.getElementById('clear-ratings');
        if (!btn) return;
        btn.addEventListener('click', function(){
            var form = btn.closest('form');
            if (!form) return;
 
            // Get all attribute radio buttons
            var allAttrRadios = form.querySelectorAll('input[type="radio"][name^="attribute["]');
 
            // Group by attribute name to handle each attribute separately
            var attrGroups = {};
            allAttrRadios.forEach(function(radio) {
                var attrMatch = radio.name.match(/attribute\[(\d+)\]/);
                if (attrMatch) {
                    var attrId = attrMatch[1];
                    if (!attrGroups[attrId]) {
                        attrGroups[attrId] = [];
                    }
                    attrGroups[attrId].push(radio);
                }
            });
 
            // Clear all ratings first
            allAttrRadios.forEach(function(radio) { radio.checked = false; });
 
            // Re-check HR Ratings (Attendance/Discipline) to calculated rating
            Object.keys(attrGroups).forEach(function(attrId) {
                var radios = attrGroups[attrId];
                var hrRatingsRadio = radios.find(function(radio) {
                    var row = radio.closest('tr');
                    return row && row.cells[1] && row.cells[1].textContent.trim().toLowerCase() === 'hr ratings (attendance/discipline)';
                });
 
                if (hrRatingsRadio) {
                    // Find the row and check if it has auto-calculated data
                    var row = hrRatingsRadio.closest('tr');
                    // HR rating is now handled in a separate section
                }
            });
 
            // Clear all job role ratings
            var ratingRadios = form.querySelectorAll('input[type="radio"][name^="job_role["]');
            ratingRadios.forEach(function(i){ i.checked = false; });
 
            // Also clear any editable text/textarea fields (non-readonly, non-disabled) if present
            var textInputs = form.querySelectorAll('input[type="text"]:not([readonly]):not([disabled]), input[type="number"]:not([readonly]):not([disabled])');
            textInputs.forEach(function(t){ t.value = ''; });
            var textAreas = form.querySelectorAll('textarea:not([readonly]):not([disabled])');
            textAreas.forEach(function(t){ t.value = ''; });
        });
    })();
 
    // Enable corresponding Save Roles button only when its 5 role inputs are non-empty
    (function(){
        var containers = Array.prototype.slice.call(document.querySelectorAll('.role-editor'));
        function bind(container){
            var inputs = Array.prototype.slice.call(container.querySelectorAll('input[name="new_roles[]"]'));
            var btn = container.parentElement.querySelector('.save-roles-btn');
            if (!btn) return;
            function update(){
                var filled = inputs.filter(function(i){ return (i.value || '').trim().length > 0; }).length;
                btn.disabled = (filled !== 5);
            }
            inputs.forEach(function(i){ i.addEventListener('input', update); });
            update();
        }
        containers.forEach(bind);
    })();
</script>
@endsection
