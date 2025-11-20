@extends('includes/header')

@section('pageTitle', $pageTitle)

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <h1 class="mb-0 header-title">HR Letters - Approvals</h1>
            @if(in_array(session()->get('role'), [config('constants.ROLE_ADMIN')]))
                <a href="{{ url('hr-letters') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Employee List
                </a>
            @endif
        </div>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section pt-4">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:70px;">Sr. No.</th>
                                    <th>Employee Name - Code</th>
                                    <th>Letter Type</th>
                                    <th>Status</th>
                                    <th style="width:200px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @forelse($records as $r)
                                <tr data-id="{{ $r->id }}" data-employee-id="{{ $r->employee_id }}">
                                    <td class="text-center">{{ $i++ }}</td>
                                    <td>
                                        @php
                                            $employee = \App\EmployeeModel::find($r->employee_id);
                                            $name = $employee ? $employee->v_employee_full_name : 'N/A';
                                            $code = $employee ? $employee->v_employee_code : 'N/A';
                                        @endphp
                                        {{ $name }} - {{ $code }}
                                    </td>
                                    @php
                                        $letterTypes = [
                                            'appointment' => 'Appointment Letter',
                                            'probation' => 'Probation Confirmation Letter',
                                            'transfer' => 'Department Transfer Letter',
                                            'relieving' => 'Relieving Letter',
                                            'internship' => 'Internship Letter',
                                            'experience' => 'Experience Letter',
                                            'leave_sanction' => 'Leave Sanction Letter',
                                            'other' => 'Other Letter'
                                        ];
                                        $letterType = $letterTypes[$r->template] ?? 'Custom Letter';
                                    @endphp
                                    <td>{{ $letterType }}</td>
                                    <td><span class="badge {{ $r->status === 'Approved' ? 'badge-success' : 'badge-warning' }}">{{ $r->status }}</span></td>
                                    <td class="text-center">
                                        @if(isset($inboxMode) && in_array($inboxMode, ['approver1','approver2']))
                                            <button class="btn btn-sm btn-outline-primary mr-2" onclick="viewLetter(this)">View</button>
                                            <button class="btn btn-sm btn-success mr-2" onclick="openApproveModal(this)">Approve</button>
                                            <button class="btn btn-sm btn-danger" onclick="takeAction(this, 'reject')">Reject</button>
                                        @else
                                            <button class="btn btn-sm btn-outline-primary mr-2" onclick="viewLetter(this)">View</button>
                                            @if($r->status === 'Approved')
                                                <button class="btn btn-sm btn-primary" onclick="downloadApproved(this)">Download</button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center">No records.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="viewLetterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Letter - Preview & Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    @if(isset($inboxMode) && in_array($inboxMode, ['approver1','approver2']))
                    <div class="mb-2 d-flex justify-content-end">
                        <button class="btn btn-sm btn-secondary mr-2" onclick="toggleEditMode()">Toggle Edit</button>
                        <button class="btn btn-sm btn-success" onclick="approveWithEdits()">Approve</button>
                    </div>
                    @endif
                    <iframe id="viewFrame" class="w-100" style="height:70vh;border:1px solid #e0e0e0;"></iframe>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    var hr_letters_pdf_url = "{{ route('hr-letters.pdf') }}";
    var currentLetterId = null;

    function takeAction(btn, action){
        var id = $(btn).closest('tr').data('id');
        var url = action === 'approve' ? ('{{ url('hr-letters') }}/'+id+'/approve') : ('{{ url('hr-letters') }}/'+id+'/reject');
        $.ajax({
            type: 'POST',
            url: url,
            data: { _token: '{{ csrf_token() }}' },
            beforeSend: function(){ showLoader && showLoader(); },
            success: function(resp){
                hideLoader && hideLoader();
                if(resp && resp.status == 1){
                    alertifyMessage && alertifyMessage('success', action.charAt(0).toUpperCase()+action.slice(1)+'d');
                    location.reload();
                } else {
                    alertifyMessage && alertifyMessage('error','Failed');
                }
            },
            error: function(){ hideLoader && hideLoader(); alertifyMessage && alertifyMessage('error','Failed'); }
        });
    }

    function viewLetter(btn){
        var id = $(btn).closest('tr').data('id');
        currentLetterId = id;
        $.ajax({
            type: 'GET',
            url: '{{ url('hr-letters') }}/' + id + '/html',
            beforeSend: function(){ showLoader && showLoader(); },
            success: function(resp){
                hideLoader && hideLoader();
                if(resp && resp.status == 1){
                    var iframe = document.getElementById('viewFrame');
                    var doc = iframe.contentDocument || iframe.contentWindow.document;
                    doc.open();
                    doc.write(resp.html || '<div class="p-3 text-muted">No content</div>');
                    doc.close();
                    setIframeEditable(false);
                    $('#viewLetterModal').modal('show');
                }
            },
            error: function(){ hideLoader && hideLoader(); }
        });
    }

    function openApproveModal(btn){
        viewLetter(btn);
    }

    var editMode = false;
    function toggleEditMode(){
        editMode = !editMode;
        setIframeEditable(editMode);
    }

    function setIframeEditable(isEditable){
        var iframe = document.getElementById('viewFrame');
        var doc = iframe.contentDocument || iframe.contentWindow.document;
        if(!doc || !doc.body) return;
        doc.body.setAttribute('contenteditable', isEditable ? 'true' : 'false');
        if(isEditable){ doc.body.style.outline = '2px dashed #007bff'; doc.body.style.outlineOffset = '4px'; }
        else { doc.body.style.outline = 'none'; }
    }

    function approveWithEdits(){
        if(!currentLetterId) return;
        var iframe = document.getElementById('viewFrame');
        var doc = iframe.contentDocument || iframe.contentWindow.document;
        var html = doc ? doc.documentElement.outerHTML : '';
        $.ajax({
            type: 'POST',
            url: '{{ url('hr-letters') }}/' + currentLetterId + '/approve',
            data: { _token: '{{ csrf_token() }}', html: html },
            beforeSend: function(){ showLoader && showLoader(); },
            success: function(resp){
                hideLoader && hideLoader();
                if(resp && resp.status == 1){
                    alertifyMessage && alertifyMessage('success','Approved');
                    $('#viewLetterModal').modal('hide');
                    location.reload();
                } else {
                    alertifyMessage && alertifyMessage('error','Failed');
                }
            },
            error: function(){ hideLoader && hideLoader(); alertifyMessage && alertifyMessage('error','Failed'); }
        });
    }

    function downloadApproved(btn){
        var tr = $(btn).closest('tr');
        var letterId = tr.data('id');
        var employeeId = tr.data('employee-id');
        if(!letterId || !employeeId) return;
        var $tmp = $('<form>', {action: hr_letters_pdf_url, method: 'POST', target: '_blank'});
        $tmp.append($('<input>', {type: 'hidden', name: '_token', value: '{{ csrf_token() }}'}));
        $tmp.append($('<input>', {type: 'hidden', name: 'employee_id', value: employeeId}));
        $tmp.append($('<input>', {type: 'hidden', name: 'letter_id', value: letterId}));
        $('body').append($tmp);
        $tmp.submit();
        $tmp.remove();
    }
</script>
@endsection
