<div class="modal fade document-folder edit-sub-designation-modal" id="edit-sub-designation-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('sub-designation') }} <span class="twt-custom-modal-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'edit-sub-designation-form' , 'method' => 'post')) !!}
                <div class="modal-body edit-sub-designation-html"></div>
                <div class="modal-footer justify-content-end">
                    <button type="button" onclick="updateEmployeeSubDesignation(this);" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
$(function(){
    $("#edit-sub-designation-form").validate({
        errorClass: "invalid-input",
        rules: {
            employee_sub_designation: { required: true },
            sub_designation_effective_date: { required: true }
        },
        messages: {
            employee_sub_designation: { required: "{{ trans('messages.select') }}" },
            sub_designation_effective_date: { required: "{{ trans('messages.require-effective-date') }}" },
        },
    });
});

function editJobSubDesignation(thisitem){
    current_selected_row = thisitem;
    var record_id = $.trim($(thisitem).attr("data-record-id"));
    $.ajax({
        type: "POST",
        url: employee_module_url + 'getEmployeeSubDesignationInfo',
        data: {
            "_token": "{{ csrf_token() }}",
            'record_id':record_id
        },
        beforeSend: function() { showLoader(); },
        success: function(response) {
            hideLoader();
            if( response != "" && response != null ){
                $(".edit-sub-designation-html").html(response);
                $('#edit-sub-designation-modal').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
                openBootstrapModal("edit-sub-designation-modal");
                $(function(){
                    $("[name='sub_designation_effective_date']").datetimepicker({
                        useCurrent: false,
                        viewMode: 'days',
                        ignoreReadonly: true,
                        format: ' {{ config("constants.DEFAULT_DATE_FORMAT") }} ',
                        showClear: true,
                        showClose: true,
                        widgetPositioning: { vertical: 'bottom', horizontal: 'auto' },
                        icons: { clear: 'fa fa-trash', Close: 'fa fa-trash' },
                    }).on('dp.show' , function(e){
                        var last_date = $.trim($("[name='sub_designation_last_update']").val());
                        if( $("[name='sub_designation_effective_date']").val() == '' ){
                            if(last_date){
                                $("[name='sub_designation_effective_date']").data('DateTimePicker').defaultDate(moment(last_date,'YYYY-MM-DD').format('DD-MM-YYYY'));
                                $("[name='sub_designation_effective_date']").val("");
                            }
                        }
                    });
                    var last_date_min = moment($.trim($("[name='sub_designation_last_update']").val()), "YYYY-MM-DD").add(1, 'days');
                    if(last_date_min.isValid()){
                        $("[name='sub_designation_effective_date']").data("DateTimePicker").minDate(last_date_min);
                    }
                    $("[name='sub_designation_effective_date']").data("DateTimePicker").maxDate(moment().endOf('d'));
                })
            }
        },
        error: function() { hideLoader(); }
    });
}

function updateEmployeeSubDesignation(){
    if( $("#edit-sub-designation-form").valid() != true ){ return false; }
    var employee_id = $.trim($("[name='update_sub_designation_employee_id']").val());
    var sub_designation = $.trim($("[name='employee_sub_designation']").val());
    var effective_date = $.trim($("[name='sub_designation_effective_date']").val());

    alertify.confirm('{{ trans("messages.update") }}', '{{ trans("messages.common-confirm-msg" , [ "module" => trans("sub-designation") ]) }}' ,function() {
        $.ajax({
            type: "POST",
            url: employee_module_url + 'updateEmployeeSubDesignation',
            dataType : 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                'employee_id':employee_id,
                'sub_designation':sub_designation,
                'effective_date':effective_date,
            },
            beforeSend: function() { showLoader(); },
            success: function(response) {
                hideLoader();
                if(response.status_code == 1 ){
                    alertifyMessage('success' , response.message);
                    $("#edit-sub-designation-modal").modal('hide');
                    $(".employee-job-record").html(response.data.html)
                } else {
                    alertifyMessage('error' , response.message);
                }
            },
            error: function() { hideLoader(); }
        });
    },function() {});
}
</script>
