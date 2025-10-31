	<div class="modal fade document-folder document-type manage-doc-view-model" id="upload-document-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.upload-document") }} <span class="custom-twt-modal-header"></span></h5>
                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                       <span aria-hidden="true"><i class="fas fa-times"></i></span>
                   </button>
               </div>
               {!! Form::open(array( 'id '=> 'add-upload-document-form' , 'method' => 'post' ,'files' => true ,  'url' => 'add')) !!}
                   <div class="modal-body new-document-master add-emp-document-master-html">
                       
                   </div>
                    <input type="hidden" name="document_type_record_id" value="">
                    <input type="hidden" name="employee_id" value="">
                    <input type="hidden" name="final_selected_image" value="">
            		<input type="hidden" name="remove_image" value="">
                   <div class="modal-footer justify-content-end">
                       <button type="button" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.add') }}" onclick="uploadDcoumentFileTypeInfo()">{{ trans('messages.add') }}</button>
                       <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                   </div>
                {!! Form::close() !!}
           </div>
       </div>
   </div>
   <script>
   $("#add-upload-document-form").validate({
	   ignore : [],
       errorClass: "invalid-input",
       rules: {
    	   'upload_document_file[]': { required: true, extension :'jpg|png|pdf|jpeg|pdf|doc|docx|xlsx|xls'},
           remark: {required: false, noSpace:true }
       },
       messages: {
    	   'upload_document_file[]': { required: "{{ trans('messages.required-upload-document-file') }}" , extension:"{{trans('messages.required-upload-document-file-valid')}}"},
    	   remark: { required: "{{ trans('messages.require-remarks') }}" },
       }
   });
   function openUploadFileDocumentModel(thisitem){ 
    	var document_type = $.trim($(thisitem).attr('data-record-document-file-type'));
        var document_file_type = $.trim($(thisitem).attr('data-file-type'));
        var employee_id = $.trim($(thisitem).attr('data-employee-id'));
        var document_type_id = $.trim($(thisitem).attr('data-document-type-record-id'));
        var header_name = $.trim($(thisitem).attr('data-type-name'));
        var header_employee_name = $.trim($(thisitem).attr('data-employee-name'));
        var document_folder_name = $.trim($(thisitem).attr('data-document-folder-name'));
       
		$.ajax({
    		type: "POST",
    		url: employee_module_url + 'openUploadFileDocumentModel',
    		data: {
    			"_token": "{{ csrf_token() }}",'employee_id':employee_id,'document_type_id':document_type_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			$('.add-emp-document-master-html').html("");
    			$('.add-emp-document-master-html').html(response);
    			if(document_file_type != "" && document_file_type != null  && document_file_type == "{{config('constants.MULTIPLE_ATTRIBUTES_SET')}}"){
    	        	($("[name='upload_document_file[]']")).attr('multiple','multiple');
    			}
    	    	var documet_id = $.trim($(thisitem).attr('data-document-type-record-id'));
    	    	$("[name='document_type_record_id']").val(documet_id);
    	    	$("[name='employee_id']").val(employee_id);
    	    	<?php /*?>var employee_name = "";
    	    	if(header_employee_name !="" && header_employee_name != null){
        	    	employee_name = ' - ' +header_employee_name;
        	    }else {
					employee_name = common_emp_modal_header_title;
				} <?php */?>
    	    	$("#upload-document-model").find(".custom-twt-modal-header").html(' - '+document_folder_name+' - ' +header_name);
    			
    			openBootstrapModal('upload-document-model');
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
   }

   function uploadDcoumentFileTypeInfo(){
	   if( $("#add-upload-document-form").valid() != true ){
			return false;
		}
		
		var formData = new FormData( $('#add-upload-document-form')[0] );
		
		alertify.confirm("{{ trans('messages.upload-document') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.upload-document')]) }}",function() { 
		$.ajax({
			url:employee_module_url + 'addUploadDocumentDetails',
			type: 'POST',
			dataType:'json',
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
			data: formData,
			cache: false,
		    contentType: false,
		    processData: false,
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if(response.status_code == 1 ){
					alertifyMessage('success' , response.message);
					$("#upload-document-model").modal('hide');
					$(".emp-doc-tab").attr('data-fetch' , '{{ config("constants.SELECTION_NO") }}');
					$(".emp-doc-tab").trigger('click');
					var current_tab_link = window.location.href;
					if( ( current_tab_link.includes("my-documents") != false ) ){
						getEmployeeDocumentList();
					}
				} else {
			    	alertifyMessage('error' , response.message);
				}
			},
			error: function(errorResponse) {
				hideLoader();
			}
		});
	}, function () { });
 }
   </script>