@extends('includes/header')
 
@section('pageTitle', 'Employee Feedback Forms - Last 6 Months Joiners')
 
@section('content')
 
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">Employee Feedback Forms (6 Month)</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <button type="button" class="btn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilterSix" title="Filter">
                <i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none">Filter</span>
            </button>
            <button type="button" class="btn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center ml-2" data-toggle="modal" data-target="#exportExcelSixModal" title="Export to Excel">
                <i class="fas fa-file-export mr-sm-2"></i> <span class="d-sm-block d-none">Export</span>
            </button>
        </div>
    </div>
 
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilterSix">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-12">
                        <div class="form-group">
                            <label class="control-label" for="search_by_six">Search By</label>
                            <input type="text" id="search_by_six" name="search_by" class="form-control" placeholder="Search By Employee Code, Name">
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_designation_six" class="control-label">Designation</label>
                            <select class="form-control select2" id="search_designation_six" name="search_designation" onchange="filterDataSix()">
                                <option value="">Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_team_six" class="control-label">Team</label>
                            <select class="form-control select2" id="search_team_six" name="search_team" onchange="filterDataSix()">
                                <option value="">Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap">
                        <button type="button" class="btn btn-theme text-white mb-3" onclick="filterDataSix()" title="Search">Search</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" onclick="resetFilterSix()" title="Reset">Reset</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-result-wrapper">
            <div class="card card-body">
                {{ Wild_tiger::readMessage() }}
                <div class="table-responsive">
                    <table class="table table-sm table-bordered text-left" id="employee-feedback-table-six">
                        <thead>
                            <tr>
                                <th class="text-left" style="width:120px;min-width:120px;">Employee Code</th>
                                <th class="text-left" style="width:150px;min-width:150px;">Employee Name</th>
                                <th class="text-left" style="width:100px;min-width:100px;">Joining Date</th>
                                <th class="text-left" style="width:140px;min-width:140px;">Six Month Complete</th>
                                <th class="text-left" style="width:120px;min-width:120px;">Department</th>
                                <th class="text-left" style="width:120px;min-width:120px;">Designation</th>
                                <th class="actions-col" style="min-width:80px">Status</th>
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                            @forelse($employeesWithCompletionDate as $key => $employee)
                                <tr>
                                    <td>{{ $employee->v_employee_code }}</td>
                                    <td>{{ $employee->v_employee_full_name }}</td>
                                    <td>{{ $employee->joining_date_formatted ?? \Carbon\Carbon::parse($employee->dt_joining_date)->format('d M Y') }}</td>
                                    <td>
                                        @if(!empty($employee->six_month_completion_date_formatted))
                                            <span class="badge badge-info">{{ $employee->six_month_completion_date_formatted }}</span>
                                        @else
                                            <span class="badge badge-warning">Calculating...</span>
                                        @endif
                                    </td>
                                    <td>{{ $employee->teamInfo->v_value ?? 'N/A' }}</td>
                                    <td>{{ $employee->designationInfo->v_value ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        @if($employee->feedback_submitted)
                                            <span class="badge badge-success">
                                                <i class="fa fa-check-circle"></i> Submitted
                                            </span>
                                        @else
                                            <span class="badge badge-warning">
                                                <i class="fa fa-clock"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="alert alert-info">
                                            <h5><i class="fa fa-info-circle"></i> No employees found</h5>
                                            <p>No employees have joined in the last 6 months who are eligible for six-month feedback forms.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
 
                @if($employeesWithCompletionDate->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $employeesWithCompletionDate->count() }} employees
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
 
@endsection
 
<!-- Export Modal -->
<div class="modal fade" id="exportExcelSixModal" tabindex="-1" role="dialog" aria-labelledby="exportExcelSixLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exportExcelSixLabel">Export Employee Feedback (6 Month)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="exportExcelSixForm" onsubmit="return exportExcelSixSubmit(event)">
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>From</label>
                    <input type="date" class="form-control" id="export_six_from" required>
                </div>
                <div class="form-group col-md-6">
                    <label>To</label>
                    <input type="date" class="form-control" id="export_six_to" required>
                </div>
            </div>
            <small class="text-muted">Select the period based on submission date.</small>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-theme" onclick="exportExcelSixSubmit(event)"><i class="fas fa-download"></i> Download</button>
        </div>
      </form>
    </div>
  </div>
</div>
 
<script>
// Initialize date pickers for six-month export
function initExportSixDates() {
  var to = new Date();
  var from = new Date();
  from.setDate(to.getDate() - 30);
  var fmt = function(d){ return d.toISOString().slice(0,10); };
  var fromEl = document.getElementById('export_six_from');
  var toEl = document.getElementById('export_six_to');
  if(fromEl && toEl) {
    fromEl.value = fmt(from);
    toEl.value = fmt(to);
  }
}
 
// Handle six-month export form submission
function exportExcelSixSubmit(e) {
  if(e) e.preventDefault();
  var fromEl = document.getElementById('export_six_from');
  var toEl = document.getElementById('export_six_to');
  var f = fromEl ? fromEl.value : '';
  var t = toEl ? toEl.value : '';
 
  if(!f || !t) {
    alert('Please select both dates.');
    return false;
  }
 
  var url = '{{ route('feedback-forms-six.export') }}' + '?from=' + encodeURIComponent(f) + '&to=' + encodeURIComponent(t);
  window.location.href = url;
 
  // Try closing modal if Bootstrap/jQuery is present
  if(window.jQuery && jQuery.fn.modal) {
    jQuery('#exportExcelSixModal').modal('hide');
  }
 
  return false;
}
 
// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  initExportSixDates();
 
  // Initialize select2 if available
  if(window.jQuery && jQuery.fn.select2) {
    jQuery('.select2').select2({ theme: 'bootstrap4', width: '100%' });
  }
});
 
// Existing functions
function filterDataSix() {
    // Placeholder: implement actual server-side filters if needed
    window.location.reload();
}
 
function resetFilterSix(){
    if(window.jQuery) {
        jQuery('#search_by_six').val('');
        jQuery('#search_designation_six').val('').trigger('change');
        jQuery('#search_team_six').val('').trigger('change');
    } else {
        // Fallback if jQuery isn't loaded
        var inputs = ['search_by_six', 'search_designation_six', 'search_team_six'];
        inputs.forEach(function(id) {
            var el = document.getElementById(id);
            if(el) el.value = '';
        });
    }
}
</script>