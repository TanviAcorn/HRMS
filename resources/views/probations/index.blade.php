@extends('includes/header')

@section('pageTitle', $pageTitle)

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0">Probation Assessments</h1>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <form method="GET" action="{{ route('probation.export') }}" class="d-inline mr-2">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <button type="submit" class="btn btn-success text-white button-actions-top-bar d-sm-flex align-items-center border btn-sm" title="Export to Excel">
                    <i class="fas fa-file-excel mr-sm-2"></i><span class="d-sm-block d-none">Export</span>
                </button>
            </form>
            <button type="button" class="btn btn-theme text-white button-actions-top-bar d-sm-flex align-items-center border btn-sm" data-toggle="collapse" data-target="#filter" title="Toggle Filter">
                <i class="fas fa-filter mr-sm-2"></i><span class="d-sm-block d-none">Filter</span>
            </button>
        </div>
    </div>

    <div class="container-fluid pt-3">
        @if(session('danger'))
            <div class="alert alert-danger">{{ session('danger') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="collapse" id="filter">
            <div class="card mb-3 depedent-row">
                <div class="card-body">
                    <form method="GET" action="{{ url('probation-assessments') }}">
                        <div class="row">
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Search (Name/Code)</label>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Type to search...">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Status</label>
                                    <select name="status" class="form-control select2">
                                        <option value="">All Assessments</option>
                                        <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Pending</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Drafted</option>
                                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button type="submit" class="btn btn-primary mr-2">Apply</button>
                                    <a href="{{ url('probation-assessments') }}" class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($reports->count() === 0)
                    <p>No direct reports currently in probation.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-sm">
                        <thead>
                            <tr class="text-center">
                                <th class="text-center">Sr. No.</th>
                                <th class="text-left">Employee Code</th>
                                <th class="text-left">Employee Name</th>
                                <th class="text-left">Department</th>
                                <th class="text-left">Designation</th>
                                <th class="text-left">Expected Confirmation Date</th>
                                <th class="text-left">Assessment Decision</th>
                                <th class="text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $emp)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $emp->v_employee_code }}</td>
                                    <td>{{ $emp->v_employee_full_name }}</td>
                                    <td>{{ data_get($emp, 'teamInfo.v_value', '') }}</td>
                                    <td>{{ data_get($emp, 'designationInfo.v_value', '') }}</td>
                                    <td>
                                        @php
                                            $confirmDate = '';
                                            if (!empty($emp->dt_probation_end_date)) {
                                                try { $confirmDate = \Carbon\Carbon::parse($emp->dt_probation_end_date)->format('Y-m-d'); } catch (\Exception $e) { $confirmDate = ''; }
                                            }
                                        @endphp
                                        {{ $confirmDate }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $assessment = $assessments->where('i_employee_id', $emp->i_id)->first();
                                            
                                            if ($assessment) {
                                                // Only show decision if status is not 'draft'
                                                if ($assessment->vch_status !== 'draft') {
                                                    if ($assessment->vch_decision === 'confirm') {
                                                        echo '<span class="badge badge-success">Confirmed</span>';
                                                    } elseif ($assessment->vch_decision === 'extend') {
                                                        $extendDate = $assessment->dt_extend_upto_date ? \Carbon\Carbon::parse($assessment->dt_extend_upto_date)->format('Y-m-d') : 'N/A';
                                                        echo '<span class="badge badge-warning">Extended until ' . $extendDate . '</span>';
                                                    } else {
                                                        echo '<span class="badge badge-info">Under Review</span>';
                                                    }
                                                } else {
                                                    echo '<span class="badge badge-secondary">Draft</span>';
                                                }
                                            } else {
                                                // No assessment exists yet
                                                $today = \Carbon\Carbon::now()->startOfDay();
                                                $confirmDateObj = !empty($emp->dt_probation_end_date) ? \Carbon\Carbon::parse($emp->dt_probation_end_date)->startOfDay() : null;
                                                
                                                if ($confirmDateObj && $today->greaterThanOrEqualTo($confirmDateObj)) {
                                                    echo '<span class="badge badge-secondary">Pending</span>';
                                                } else {
                                                    echo '<span class="text-muted">-</span>';
                                                }
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        @php
                                            $isAdmin = ( session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN') );
                                            $status = isset($assessmentStatusByEmp[$emp->i_id]) ? $assessmentStatusByEmp[$emp->i_id] : null; // draft/submitted/completed/null
                                            $canOpen = false;
                                            if (!$isAdmin) {
                                                try {
                                                    if (!empty($emp->dt_probation_end_date)) {
                                                        $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($emp->dt_probation_end_date), false);
                                                        // Allow when end date is in <= 15 days (including past due)
                                                        $canOpen = ($daysLeft <= 15);
                                                    }
                                                } catch (\Exception $e) { $canOpen = false; }
                                            }
                                        @endphp
                                        <div class="d-flex align-items-center gap-1">
                                            @if($isAdmin)
                                                @if($status === 'submitted' || $status === 'completed')
                                                    <a class="btn btn-sm btn-outline-success mr-1" href="{{ url('probation-assessments/' . $emp->i_id . '/xlsx') }}">Download</a>
                                                    <a class="btn btn-sm btn-outline-danger" href="{{ url('probation-assessments/' . $emp->i_id . '/pdf') }}">PDF</a>
                                                @else
                                                    <span class="badge badge-secondary text-uppercase">Pending</span>
                                                @endif
                                            @else
                                                @if($canOpen)
                                                    <a class="btn btn-sm btn-theme text-white mr-2" href="{{ url('probation-assessments/' . $emp->i_id) }}">Open Assessment</a>
                                                @else
                                                    <span class="badge badge-light text-uppercase mr-2">Not Due</span>
                                                @endif
                                                @if($status === 'submitted' || $status === 'completed')
                                                    <a class="btn btn-sm btn-outline-success mr-1" href="{{ url('probation-assessments/' . $emp->i_id . '/xlsx') }}">Download</a>
                                                    <a class="btn btn-sm btn-outline-danger" href="{{ url('probation-assessments/' . $emp->i_id . '/pdf') }}">PDF</a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection
