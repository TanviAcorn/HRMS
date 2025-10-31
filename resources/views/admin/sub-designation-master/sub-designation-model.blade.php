<div class="modal fade document-folder" id="sub-designation-master-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title twt-sub-designation-modal-header-name" id="exampleModalLabel">{{ trans('messages.add') }} Sub Designation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'add-sub-designation-master-model-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body add-sub-designation-master-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                    <input type="hidden" name="record_id" value="">
                    <input type="hidden" name="crud_module" value="">
                    <button type="button" onclick="addSubDesignationModel()" class="btn bg-theme text-white action-button sub-designation-master-action-button btn-add" title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<script>
var sub_designation_module_url = '{{config('constants.SUB_DESIGNATION_MASTER_URL')}}' + '/';

$("#add-sub-designation-master-model-form").validate({
    errorClass: "invalid-input",
    onfocusout: false,
    onkeyup: false,
    rules: {
        sub_designation_name: {
            required: true,noSpace: true,validateUniqueSubDesignationName:true
        },
        designation: {
            required: true
        },
    },
    messages: {
        sub_designation_name: {
            required: "{{ trans('messages.required-name') }}"
        },
        designation: {
            required: "{{ trans('messages.required-designation') }}"
        },
    }
});

function openSubDesignationModel(thisitem){
    editSubDesignationModel(thisitem);
}

function addSubDesignationModel(){
    if($('#add-sub-designation-master-model-form').valid() != true){
        return false;
    }
    var record_id = $.trim($('[name="record_id"]').val());
    var sub_designation_name = $.trim($('[name="sub_designation_name"]').val());
    var designation = $.trim($('[name="designation"]').val());
    var crud_module = $.trim($('[name="crud_module"]').val());
    var chart_color = $.trim($('[name="chart_color"]').val());

    var confirm_box = "";
    var confirm_box_msg = "";

    if(record_id == 0){
        confirm_box = "{{ trans('messages.add') }}";
        confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add')]) }}";
    } else {
        confirm_box = "{{ trans('messages.update') }}";
        confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update')]) }}";
    }

    alertify.confirm(confirm_box,confirm_box_msg,function() {   
        $.ajax({
            type: "POST",
            dataType: "json",
            url: sub_designation_module_url + 'add',
            data: {
                "_token": "{{ csrf_token() }}",
                'sub_designation_name':sub_designation_name,
                'designation':designation,
                'record_id':record_id,
                'crud_module':crud_module,
                'chart_color' : chart_color,
                'row_index':$(current_row).parents('tr').find('.sr-col').html(),
            },
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if( response.status_code == 1 ){
                    $("#sub-designation-master-model").modal('hide');
                    alertifyMessage('success',response.message);
                    if(record_id != '' && record_id != null){
                        $(current_row).parents('.sub-designation-record').html(response.data.html);
                    }else{
                        if( crud_module != "" && crud_module != null && crud_module == "{{ config('constants.SELECTION_NO') }}"){
                            $(current_row).parents('.dependant-field-selection').find('.sub-designation-list').html(response.data.html);
                            $(current_row).parents('.modal').find('.sub-designation-list').html(response.data.html);
                        }
                        filterData();
                    }
                } else {
                    alertifyMessage('error',response.message);
                }
            },
            error: function() {
                hideLoader();
            }
        });
    },function() {});
}
var current_row ='';
function editSubDesignationModel(thisitem){
    current_row = thisitem;
    var record_id = $.trim($(thisitem).attr('data-record-id'));
    var data_crud_module = $.trim($(thisitem).attr('data-sub-designation-module'));

    $.ajax({
        type: "POST",
        url: sub_designation_module_url + 'edit',
        data: {
            "_token": "{{ csrf_token() }}",'record_id':record_id,
        },
        beforeSend: function() {
            showLoader();
        },
        success: function(response) {
            hideLoader();
            if(record_id !="" && record_id != null){
                var header_name = "{{ trans('messages.update') }} Sub Designation";
                var button_name = "{{ trans('messages.update') }}";
                $('.add-sub-designation-master-html').html("");
                $('.add-sub-designation-master-html').html(response);
                $("[name='record_id']").val(record_id);
                $("#sub-designation-master-model").find('.sub-designation-master-action-button').html(button_name);
                $('.sub-designation-master-action-button ').attr('title' , "{{ trans('messages.update') }}");
                $("#sub-designation-master-model").find('.twt-sub-designation-modal-header-name').html(header_name);
            } else {
                var header_name = "{{ trans('messages.add') }} Sub Designation" ;
                var button_name = "{{ trans('messages.add') }}" ;
                $('.add-sub-designation-master-html').html("");
                $('.add-sub-designation-master-html').html(response);
                $("[name='record_id']").val("");
                $("#sub-designation-master-model").find('.sub-designation-master-action-button').html(button_name);
                $('.sub-designation-master-action-button ').attr('title' , "{{ trans('messages.add') }}");
                $("#sub-designation-master-model").find('.twt-sub-designation-modal-header-name').html(header_name);
            }
            openBootstrapModal('sub-designation-master-model');
            $("[name='crud_module']").val(data_crud_module);
        },
        error: function() {
            hideLoader();
        }
    });
}

$.validator.addMethod("validateUniqueSubDesignationName", function (value, element) {
    var result = true;
    $.ajax({
        type: "POST",
        async: false,
        url: sub_designation_module_url +'checkUniqueSubDesignationName',
        dataType: "json",
        data: {
            "_token": "{{ csrf_token() }}",
            'sub_designation_name': $.trim($("[name='sub_designation_name']").val()),
            'designation': $.trim($("[name='designation']").val()),
            'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null)
        },
        success: function (response) {
            if (response.status_code == 1) {
                return false;
            } else {
                result = false;
                return true;
            }
        }
    });
    return result;
}, '{{ trans('messages.error') }}');
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
