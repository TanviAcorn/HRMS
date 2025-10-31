@extends('includes/header')

@section('pageTitle', $pageTitle)

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0">Performance Appraisals</h1>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <button type="button" class="btn btn btn-theme text-white button-actions-top-bar d-sm-flex align-items-center border btn-sm mr-2" data-toggle="collapse" data-target="#filter" title="Toggle Filter">
                <i class="fas fa-filter mr-sm-2"></i><span class="d-sm-block d-none">Filter</span>
            </button>
            <form action="{{ route('performance-appraisals.export') }}" method="GET" class="d-inline">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('department_id'))
                    <input type="hidden" name="department_id" value="{{ request('department_id') }}">
                @endif
                @if(request('designation_id'))
                    <input type="hidden" name="designation_id" value="{{ request('designation_id') }}">
                @endif
                <button type="submit" class="btn btn-success text-white button-actions-top-bar d-sm-flex align-items-center border btn-sm" title="Export to Excel">
                    <i class="fas fa-file-excel mr-sm-2"></i><span class="d-sm-block d-none">Export</span>
                </button>
            </form>
        </div>
    </div>

    <div class="container-fluid pt-3">
        <div class="collapse" id="filter">
            <div class="card mb-3 depedent-row">
                <div class="card-body">
                    <form method="GET" action="{{ url('performance-appraisals') }}">
                        <div class="row">
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Search (Name/Code)</label>
                                    <input type="text" class="form-control" name="search" value="{{ $selected['search'] ?? '' }}" placeholder="Type to search...">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Department</label>
                                    <select name="department_id" class="form-control select2">
                                        <option value="">All</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->i_id }}" {{ (isset($selected['department_id']) && (int)$selected['department_id']===(int)$dept->i_id) ? 'selected' : '' }}>{{ $dept->v_value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Designation</label>
                                    <select name="designation_id" class="form-control select2">
                                        <option value="">All</option>
                                        @foreach($designations as $des)
                                            <option value="{{ $des->i_id }}" {{ (isset($selected['designation_id']) && (int)$selected['designation_id']===(int)$des->i_id) ? 'selected' : '' }}>{{ $des->v_value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-4 col-md-6 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-theme text-white mr-2">Filter</button>
                                    <a href="{{ url('performance-appraisals') }}" class="btn btn-outline-secondary reset-wild-tigers">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                @if($reports->count() === 0)
                    <p>No direct reports found.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-sm">
                        <thead>
                            <tr class="text-center">
                                <th class="text-left">Employee Code</th>
                                <th class="text-left">Employee Name</th>
                                <th class="text-left">Department</th>
                                <th class="text-left">Designation</th>
                                <th class="text-center">Score</th>
                                <th class="text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $emp)
                                <?php 
                                    $status = isset($appraisalStatusByEmp[$emp->i_id]) ? $appraisalStatusByEmp[$emp->i_id] : null;
                                    $isSubmitted = in_array($status, ['submitted','completed']);
                                    
                                    // Check if current user is the L2 manager (manager of the manager)
                                    $isL2Manager = false;
                                    if (!$isAdmin) {
                                        $l1Manager = $emp->leaderInfo;
                                        $isL2Manager = ($l1Manager && $l1Manager->i_leader_id == session('user_employee_id'));
                                    }
                                ?>
                                <tr>
                                    <td>{{ $emp->v_employee_code }}</td>
                                    <td>{{ $emp->v_employee_full_name }}</td>
                                    <td>{{ optional($emp->teamInfo)->v_value }}</td>
                                    <td>{{ optional($emp->designationInfo)->v_value }}</td>
                                    <td class="text-center">
                                        @if(isset($scoresMap[$emp->i_id]))
                                            {{ $scoresMap[$emp->i_id] }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($isAdmin || $isL2Manager)
                                            @if($isSubmitted)
                                                <a class="btn btn-sm btn-secondary" href="{{ url('performance-appraisals/' . $emp->i_id) }}">View Form</a>
                                            @else
                                                <span class="badge badge-warning">Form not submitted by the leader</span>
                                            @endif
                                        @else
                                            <a class="btn btn-sm {{ $isSubmitted ? 'btn-secondary' : 'btn-theme text-white' }}" href="{{ url('performance-appraisals/' . $emp->i_id) }}">
                                                {{ $isSubmitted ? 'View Form' : 'Open Appraisal' }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    {{ $reports->appends($selected ?? [])->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection
