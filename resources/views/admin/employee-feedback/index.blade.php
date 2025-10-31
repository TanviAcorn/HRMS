@extends('includes/header')
 
@section('pageTitle', 'Employee Feedback Forms - Recent Joiners')
 
@section('content')
 
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">Employee Feedback Forms</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <button type="button" class="btn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="Filter">
                <i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none">Filter</span>
            </button>
            <button type="button" class="btn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center ml-2" data-toggle="modal" data-target="#exportExcelModal" title="Export to Excel">
                <i class="fas fa-file-export mr-sm-2"></i> <span class="d-sm-block d-none">Export</span>
            </button>
        </div>
    </div>
 
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-12">
                        <div class="form-group">
                            <label class="control-label" for="search_by">Search By</label>
                            <input type="text" name="search_by" class="form-control" placeholder="Search By Employee Code, Name">
                        </div>
                    </div>
 
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_designation" class="control-label">Designation</label>
                            <select class="form-control select2" name="search_designation" onchange="filterData()">
                                <option value="">Select</option>
                                <!-- Add designation options here if needed -->
                            </select>
                        </div>
                    </div>
 
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">Team</label>
                            <select class="form-control select2" name="search_team" onchange="filterData()">
                                <option value="">Select</option>
                                <!-- Add team options here if needed -->
                            </select>
                        </div>
                    </div>
 
                    <div class="col-md-3 d-flex align-items-end gap">
                        <button type="button" class="btn btn-theme text-white mb-3" onclick="filterData()" title="Search">Search</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="Reset">Reset</button>
                    </div>
                </div>
            </div>
        </div>
 
        <div class="filter-result-wrapper">
            <div class="card card-body">
                {{ Wild_tiger::readMessage() }}
                <div class="table-responsive">
                    <table class="table table-sm table-bordered text-left" id="employee-feedback-table">
                        <thead>
                            <tr>
                                <th class="text-left" style="width:120px;min-width:120px;">Employee Code</th>
                                <th class="text-left" style="width:150px;min-width:150px;">Employee Name</th>
                                <th class="text-left" style="width:100px;min-width:100px;">Joining Date</th>
                                <th class="text-left" style="width:120px;min-width:120px;">One Month Complete</th>
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
                                        @if($employee->one_month_completion_date_formatted)
                                            <span class="badge badge-info">{{ $employee->one_month_completion_date_formatted }}</span>
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
                                            <p>No employees have joined in the last 30 days who are eligible for feedback forms.</p>
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
    <!-- Export Modal -->
    <div class="modal fade" id="exportExcelModal" tabindex="-1" role="dialog" aria-labelledby="exportExcelLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exportExcelLabel">Export Employee Feedback (1 Month)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="exportExcelForm" onsubmit="return exportExcelSubmit(event)">
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>From</label>
                    <input type="date" class="form-control" id="export_from" required>
                </div>
                <div class="form-group col-md-6">
                    <label>To</label>
                    <input type="date" class="form-control" id="export_to" required>
                </div>
            </div>
            <small class="text-muted">Select the period based on submission date.</small>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" onclick="exportExcelSubmit(event)"><i class="fa fa-download"></i> Download</button>
        </div>
      </form>
    </div>
  </div>
 
</div>
 
@endsection
 
<script>
function filterData(){ window.location.reload(); }
 
// Prefill dates when DOM ready
document.addEventListener('DOMContentLoaded', function(){
  var to = new Date();
  var from = new Date();
  from.setDate(to.getDate() - 30);
  var fmt = function(d){ return d.toISOString().slice(0,10); };
  var fromEl = document.getElementById('export_from');
  var toEl = document.getElementById('export_to');
  if(fromEl && toEl){ fromEl.value = fmt(from); toEl.value = fmt(to); }
});
 
function exportExcelSubmit(e){
  if(e) e.preventDefault();
  var fromEl = document.getElementById('export_from');
  var toEl = document.getElementById('export_to');
  var f = fromEl ? fromEl.value : '';
  var t = toEl ? toEl.value : '';
  if(!f || !t){ alert('Please select both dates.'); return false; }
  var url = '{{ route('feedback-forms.export') }}' + '?from=' + encodeURIComponent(f) + '&to=' + encodeURIComponent(t);
  window.location.href = url;
  // Try closing modal if Bootstrap/jQuery is present
  if(window.jQuery && jQuery.fn.modal){ jQuery('#exportExcelModal').modal('hide'); }
  return false;
}
</script>
 
@include( config("constants.ADMIN_FOLDER") . 'common-update-status-delete-script')
@include( config("constants.ADMIN_FOLDER") . 'common-form-validation')