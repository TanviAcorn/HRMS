@extends('includes/header')

@section('pageTitle', $pageTitle)

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper">
        <div class="container-fluid">
            <div class="d-flex justify-content-end">
                <a href="{{ url('hr-letters') }}" class="btn" style="background-color: #8d191a; color: #ffffff; border-color: #8d191a;">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <section class="py-2">
        <div class="container-fluid">
            <div class="card border-0 shadow-sm m-3">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-file-alt" style="color: #8d191a; font-size: 1.5rem;"></i>
                        </div>
                        <h5 class="mb-0 ml-3">Letter Templates for {{ $employee->v_employee_full_name }}</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        @foreach($templates as $tpl)
                        @php
                            $icons = [
                                'appointment' => 'file-contract',
                                'probation' => 'user-check',
                                'transfer' => 'exchange-alt',
                                'relieving' => 'file-export',
                                'internship' => 'user-graduate',
                                'experience' => 'certificate',
                                'other' => 'file-alt'
                            ];
                            $icon = $icons[$tpl['key']] ?? 'file-alt';
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm rounded-3 transition-all" 
                                 style="transition: transform 0.2s ease, box-shadow 0.2s ease; border: 1px solid #8d191a;"
                                 onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.1), 0 4px 8px rgba(0,0,0,0.1)'; this.style.borderColor='#8d191a'"
                                 onmouseout="this.style.transform=''; this.style.boxShadow=''; this.style.borderColor='#8d191a'">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-start">
                                        <div class="me-4">
                                            <div style="width: 45px; height: 45px; background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-{{ $icon }}" style="font-size: 1.5rem; color: #8d191a;"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 font-weight-bold">{{ $tpl['name'] }}</h6>
                                            <p class="text-muted small mb-3"></p>
                                            <div class="d-flex">
                                                <button class="btn btn-outline-danger btn-sm mr-2" onclick="previewLetter('{{ $tpl['key'] }}')" style="border-color: #8d191a; color: #8d191a;">
                                                    <i class="far fa-eye mr-1"></i> Preview
                                                </button>
                                                <button class="btn btn-sm" onclick="openTemplateModal('{{ $tpl['key'] }}')" style="background-color: #8d191a; border-color: #8d191a; color: #ffffff;">
                                                    <i class="far fa-edit mr-1"></i> Customize
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Template Customization Modal -->
    <div class="modal fade" id="letterTemplateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #8d191a;">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i> Customize Letter
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 letter-template-html">
                    <div class="text-center py-5">
                        <div class="spinner-border" role="status" style="color: #8d191a;">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading template...</p>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="button" class="btn" onclick="previewFromForm()" style="background-color: #8d191a; border-color: #8d191a; color: #ffffff;">
                        <i class="far fa-eye mr-1"></i> Preview Letter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview & Edit Modal -->
    <div class="modal fade" id="letterPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #8d191a;">
                    <h5 class="modal-title">
                        <i class="far fa-file-alt mr-2"></i> Preview & Edit Letter
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary" id="toggleEditBtn" onclick="toggleEditMode()">
                                <i class="fas fa-edit mr-1"></i> Enable Edit Mode
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm" onclick="submitForApproval()" style="background-color: #8d191a; border-color: #8d191a; color: #ffffff;">
                            <i class="fas fa-paper-plane mr-1"></i> Send for Approval
                        </button>
                    </div>
                    <div class="p-3">
                        <div class="alert alert-info small mb-3">
                            <i class="fas fa-info-circle mr-2"></i> 
                            Use the "Enable Edit Mode" button to make direct changes to the letter content.
                        </div>
                        <iframe id="previewFrame" class="w-100 bg-white" style="height: 70vh; border: 1px solid #e0e0e0; border-radius: 4px;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    var hr_letters_render_url = "{{ route('hr-letters.render') }}";
    var hr_letters_pdf_url = "{{ route('hr-letters.pdf') }}";
    var hr_letters_preview_url = "{{ route('hr-letters.preview') }}";
    var hr_letters_submit_url = "{{ route('hr-letters.submit') }}";
    var employee_id = {{ (int)$employee->i_id }};
    var currentTemplateKey = null;
    var lastSubmittedLetterId = null;

    function openTemplateModal(templateKey){
        console.log('Opening template modal for:', templateKey);
        console.log('Using URL:', hr_letters_render_url);
        
        // Show loading state
        $('.letter-template-html').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-danger" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-3">Loading template, please wait...</p>
            </div>
        `);
        
        // Show the modal immediately
        $('#letterTemplateModal').modal('show');
        
        // Make the AJAX request
        $.ajax({
            type: 'POST',
            url: hr_letters_render_url,
            data: {
                _token: '{{ csrf_token() }}',
                employee_id: employee_id,
                template: templateKey
            },
            dataType: 'json',
            beforeSend: function(){
                console.log('Sending request...');
                showLoader && showLoader();
            },
            success: function(resp){
                console.log('Response received:', resp);
                hideLoader && hideLoader();
                
                if(resp && resp.status == 1 && resp.html){
                    $('.letter-template-html').html(resp.html);
                    console.log('Template loaded successfully');
                } else {
                    console.error('Invalid response format or missing HTML:', resp);
                    $('.letter-template-html').html(`
                        <div class="alert alert-danger">
                            <h5>Error Loading Template</h5>
                            <p>Could not load the template. Please try again.</p>
                            <p>Status: ${resp ? resp.status : 'no status'}</p>
                            <p>Message: ${resp ? (resp.message || 'No error message') : 'No response'}</p>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error){
                console.error('AJAX Error:', status, error);
                hideLoader && hideLoader();
                $('.letter-template-html').html(`
                    <div class="alert alert-danger">
                        <h5>Error Loading Template</h5>
                        <p>Status: ${status}</p>
                        <p>Error: ${error || 'Unknown error occurred'}</p>
                        <p>Response: ${xhr.responseText || 'No response'}</p>
                    </div>
                `);
            }
        });
    }

    function submitForApproval(){
        var iframe = document.getElementById('previewFrame');
        var doc = iframe.contentDocument || iframe.contentWindow.document;
        if(!doc) return;
        var html = doc.documentElement.outerHTML;
        $.ajax({
            type: 'POST',
            url: hr_letters_submit_url,
            data: {
                _token: '{{ csrf_token() }}',
                employee_id: employee_id,
                template: currentTemplateKey,
                html: html
            },
            beforeSend: function(){ showLoader && showLoader(); },
            success: function(resp){
                hideLoader && hideLoader();
                if(resp && resp.status == 1){
                    lastSubmittedLetterId = resp.letter_id || null;
                    alertifyMessage && alertifyMessage('success', resp.message);
                    if(lastSubmittedLetterId){
                        $('#downloadApprovedBtn').removeClass('d-none');
                    }
                } else {
                    alertifyMessage && alertifyMessage('error', (resp && resp.message) ? resp.message : 'Failed to submit');
                }
            },
            error: function(){ hideLoader && hideLoader(); alertifyMessage && alertifyMessage('error','Failed to submit'); }
        });
    }

    // Removed download before approval; downloads are available from Approvals Inbox after approval

    function previewFromForm(){
        var $form = $('#letter-template-form');
        if(!$form.length) return;
        var template = $form.find('input[name="template"]').val();
        previewLetter(template);
    }

    function previewLetter(templateKey){
        currentTemplateKey = templateKey;
        // Prefer existing form values if already opened; else just preview with defaults
        var payload = { _token: '{{ csrf_token() }}', employee_id: employee_id, template: templateKey };
        var $form = $('#letter-template-form');
        if($form.length){
            $form.serializeArray().forEach(function(f){ payload[f.name] = f.value; });
        }
        $.ajax({
            type: 'POST',
            url: hr_letters_preview_url,
            data: payload,
            beforeSend: function(){ showLoader && showLoader(); },
            success: function(resp){
                hideLoader && hideLoader();
                if(resp && resp.status == 1){
                    // Load HTML into iframe
                    var iframe = document.getElementById('previewFrame');
                    var doc = iframe.contentDocument || iframe.contentWindow.document;
                    doc.open();
                    doc.write(resp.html);
                    doc.close();
                    // Start in non-edit mode
                    setIframeEditable(false);
                    $('#letterPreviewModal').modal('show');
                }
            },
            error: function(){ hideLoader && hideLoader(); }
        });
    }

    function setIframeEditable(isEditable){
        var iframe = document.getElementById('previewFrame');
        var doc = iframe.contentDocument || iframe.contentWindow.document;
        if(!doc || !doc.body) return;
        try { doc.designMode = isEditable ? 'on' : 'off'; } catch(e) {}
        doc.body.setAttribute('contenteditable', isEditable ? 'true' : 'false');
        // Simple style cue when editing
        if(isEditable){
            doc.body.style.outline = '2px dashed #007bff';
            doc.body.style.outlineOffset = '4px';
            // try to focus caret inside
            setTimeout(function(){
                try { (doc.body.querySelector('[contenteditable="true"]') || doc.body).focus(); } catch(e) {}
            }, 50);
        } else {
            doc.body.style.outline = 'none';
        }
    }

    var editMode = false;
    function toggleEditMode(){
        editMode = !editMode;
        setIframeEditable(editMode);
        var button = document.getElementById('toggleEditBtn');
        var icon = button.querySelector('i');
        if (editMode) {
            icon.className = 'fas fa-times-circle mr-1';
            button.innerHTML = '<i class="fas fa-times-circle mr-1"></i> Disable Edit Mode';
        } else {
            icon.className = 'fas fa-edit mr-1';
            button.innerHTML = '<i class="fas fa-edit mr-1"></i> Enable Edit Mode';
        }
    }

    // Download action removed from preview; download only available after approval from Inbox
</script>
@endsection
