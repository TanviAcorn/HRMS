@extends('includes/header')

@section('pageTitle', $pageTitle)

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <h1 class="mb-0 header-title">HR Letters</h1>
            <div class="d-flex align-items-center">
                <input type="text" id="employeeSearch" class="form-control form-control-sm mr-2" placeholder="Search employee..." style="max-width:260px;">
                @php $uid = (int)(session()->get('user_id')); $isAdmin = session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN'); @endphp
                @if( (session()->has('user_id') && in_array($uid, [751, 323])) || $isAdmin )
                    <a href="{{ url('hr-letters/inbox') }}" class="btn btn-sm btn-primary">Approvals Inbox</a>
                @endif
            </div>
        </div>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section pt-4">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="employeeTable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:60px;">#</th>
                                    <th>Employee</th>
                                    <th>Designation</th>
                                    <th>Department/Team</th>
                                    <th style="width:160px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $idx => $emp)
                                <tr>
                                    <td class="text-center">{{ $idx+1 }}</td>
                                    <td>
                                        {{ $emp->v_employee_full_name }}
                                        @if(!empty($emp->v_employee_code))
                                            <small class="text-muted">(Code: {{ $emp->v_employee_code }})</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ data_get($emp,'designationInfo.v_value') ?? data_get($emp,'v_designation_name') ?? data_get($emp,'subDesignationInfo.v_name') ?? '-' }}
                                    </td>
                                    <td>{{ data_get($emp,'teamInfo.v_value') ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ url('hr-letters/' . $emp->i_id) }}" class="btn btn-sm bg-theme text-white">Generate Letter</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center">No active employees found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script>
    (function(){
        var $input = document.getElementById('employeeSearch');
        if(!$input) return;
        $input.addEventListener('input', function(){
            var q = (this.value || '').toLowerCase();
            var rows = document.querySelectorAll('#employeeTable tbody tr');
            rows.forEach(function(tr){
                var text = tr.innerText.toLowerCase();
                tr.style.display = text.indexOf(q) !== -1 ? '' : 'none';
            });
        });
    })();
</script>
@endsection
