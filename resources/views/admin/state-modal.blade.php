<div class="modal fade document-folder" id="state-master-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-state-modal-header-name" id="exampleModalLabel">{{ trans("messages.add-state") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-state-master-model-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                    <div class="modal-body add-state-master-html">
                        
                    </div>
                    <div class="modal-footer justify-content-end">
                    	<input type="hidden" name="state_record_id" value="">
                        <button type="button" onclick="addStateModel()" class="btn bg-theme text-white action-button state-master-action-button  btn-add" title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    
<script>
    var state_module_url = '{{config("constants.STATE_MASTER_URL")}}' + '/';
    
    $("#add-state-master-model-form").validate({
    	errorClass: "invalid-input",
		onfocusout: false,
		onkeyup: false,
        rules: {
            state_name: {
                required: true,noSpace: true,validateUniqueStateName:true,
            },
        },
        messages: {
            state_name: {
                required: "{{ trans('messages.required-state-name') }}"
            },
        }
    });
    function openStateModel(thisitem){
    	editStateModel(thisitem);
    }
    function addStateModel(){
    	if($('#add-state-master-model-form').valid() != true){
			return false;
		}
    	var state_record_id = $.trim($('[name="state_record_id"]').val());
		var state_name = $.trim($('[name="state_name"]').val());
		var confirm_box = "";
	    var confirm_box_msg = "";
	    
	    if(state_record_id == 0){
	    	confirm_box = "{{ trans('messages.add-state') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-state')]) }}";
	    } else {
	    	confirm_box = "{{ trans('messages.update-state') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-state')]) }}";
	    } 

	    alertify.confirm(confirm_box,confirm_box_msg,function() {   
			$.ajax({
				type: "POST",
				dataType: "json",
				url: state_module_url + 'add',
				data: {
					"_token": "{{ csrf_token() }}",
					'state_name':state_name,'state_record_id':state_record_id,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),
				},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						$("#state-master-model").modal('hide');
						alertifyMessage('success',response.message);
						if(state_record_id != '' && state_record_id != null){
							$(current_row).parents('.state-record').html(response.data.html);
						}else{
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
    function editStateModel(thisitem){
    	current_row = thisitem;
    	var state_record_id = $.trim($(thisitem).attr('data-record-id'));
    	$.ajax({
    		type: "POST",
    		url: state_module_url + 'edit',
    		data: {
    			"_token": "{{ csrf_token() }}",'state_record_id':state_record_id,
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			if(state_record_id !="" && state_record_id != null){
    				var header_name = "{{ trans('messages.update-state') }}";
    				var button_name = "{{ trans('messages.update') }}";
    				$('.add-state-master-html').html("");
    				$('.add-state-master-html').html(response);
    				$("[name='state_record_id']").val(state_record_id);
    				$("#state-master-model").find('.state-master-action-button').html(button_name);
    				$('.state-master-action-button ').attr('title' , "{{ trans('messages.update') }}");
    				$("#state-master-model").find('.twt-state-modal-header-name').html(header_name);
    			} else {
    				var header_name = "{{ trans('messages.add-state') }}" ;
    				var button_name = "{{ trans('messages.add') }}" ;
    				$('.add-state-master-html').html("");
    				$('.add-state-master-html').html(response);
    				$("[name='state_record_id']").val("");
    				$("#state-master-model").find('.state-master-action-button').html(button_name);
    				$('.state-master-action-button ').attr('title' , "{{ trans('messages.add') }}");
    				$("#state-master-model").find('.twt-state-modal-header-name').html(header_name);
    			}
    			openBootstrapModal('state-master-model');
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
    $.validator.addMethod("validateUniqueStateName", function (value, element) {
   	 
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: state_module_url +'checkUniqueStateName',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'state_name': $.trim($("[name='state_name']").val()),'state_record_id': ( $.trim($("[name='state_record_id']").val()) != '' ? $.trim($("[name='state_record_id']").val()) : null) 
    		     },
    		beforeSend: function() {
    			
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
    }, '{{ trans("messages.error-unique-state-name") }}');
    
    function searchField(){
    	var search_by = $.trim($('[name="search_by"]').val());
    	var search_status = $.trim($('[name="search_status"]').val());
    	
    	var searchData = {
                'search_by':search_by,
                'search_status': search_status
            }
            return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(state_module_url + 'filter' , searchFieldName);
    }
    var paginationUrl = state_module_url + 'filter'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script> 
