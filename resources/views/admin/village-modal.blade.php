 <div class="modal fade document-folder" id="village-master-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-village-modal-header-name" id="exampleModalLabel">{{ trans("messages.add-village") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-village-master-model-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                    <div class="modal-body add-village-master-html">
                        
                    </div>
                    <div class="modal-footer justify-content-end">
                    	<input type="hidden" name="village_record_id" value="">
                    	<input type="hidden" name="village_crud_module" value="">
                        <button type="button" onclick="addVillageModel()" class="btn bg-theme text-white action-button village-master-action-button btn-add" title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
<script>
  var village_module_url = '{{config("constants.VILLAGE_MASTER_URL")}}' + '/';
  $("#add-village-master-model-form").validate({
    	errorClass: "invalid-input",
		onfocusout: false,
		onkeyup: false,
        rules: {
           village_name: {required: true, noSpace: true ,validateUniqueVillageName:true },
           city: {required: true},
           state_name: {required: true},
        },
        messages: {
        	village_name: {required: "{{ trans('messages.required-village-name') }}" },
        	city: {required: "{{ trans('messages.required-city') }}" },
        	state_name: {required: "{{ trans('messages.required-state') }}"},
        },
        
    });
    function openVillageModel(thisitem){
    	editVillageModel(thisitem);
    }
    function addVillageModel(){
    	if($('#add-village-master-model-form').valid() != true){
			return false;
		}
    	//$("[name='state']").prop('disabled' , false );
        var village_record_id = $.trim($('[name="village_record_id"]').val());
		var village_name = $.trim($('[name="village_name"]').val());
		var city = $.trim($('[name="city"]').val());
		var state = $.trim($('[name="state_name"]').val());
		var village_crud_module = $.trim($('[name="village_crud_module"]').val());
		
		var confirm_box = "";
	    var confirm_box_msg = "";
	    
	    if(village_record_id == 0){
	    	confirm_box = "{{ trans('messages.add-village') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-village')]) }}";
	    } else {
	    	confirm_box = "{{ trans('messages.update-village') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-village')]) }}";
	    } 

	    alertify.confirm(confirm_box,confirm_box_msg,function() {
	    	var state = $.trim($('[name="state_name"]').val());   
			$.ajax({
				type: "POST",
				dataType: "json",
				url: village_module_url + 'add',
				data: {
					"_token": "{{ csrf_token() }}",'village_name': village_name,'city':city,
					'state':state,'village_record_id':village_record_id,'village_crud_module':village_crud_module,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),
				},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						$("#village-master-model").modal('hide');
						alertifyMessage('success',response.message);
						if(village_record_id != '' && village_record_id != null){
							$(current_row).parents('.village-record').html(response.data.html);
						}else{
							if( village_crud_module != "" && village_crud_module != null && village_crud_module == "{{ config('constants.SELECTION_NO') }}"){
								$(current_row).parents('.dependant-field-selection').find('.village-list').html(response.data.html);
								$(current_row).parents('.modal').find('.village-master-list').html(response.data.html);
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
    function editVillageModel(thisitem){
    	current_row = thisitem;
    	var village_record_id = $.trim($(thisitem).attr('data-record-id'));
    	var data_crud_module = $.trim($(thisitem).attr('data-village-module'));
    	$.ajax({
    		type: "POST",
    		url: village_module_url + 'edit',
    		data: {
    			"_token": "{{ csrf_token() }}",'village_record_id':village_record_id,
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			if(village_record_id !="" && village_record_id != null){
    				var header_name = "{{ trans('messages.update-village') }}";
    				var button_name = "{{ trans('messages.update') }}";
    				$('.add-village-master-html').html("");
    				$('.add-village-master-html').html(response);
    				$("[name='village_record_id']").val(village_record_id);
    				$("#village-master-model").find('.village-master-action-button').html(button_name);
    				$('.village-master-action-button').attr('title' , "{{ trans('messages.update') }}");
    				$("#village-master-model").find('.twt-village-modal-header-name').html(header_name);
    			} else {
    				var header_name = "{{ trans('messages.add-village') }}" ;
    				var button_name = "{{ trans('messages.add') }}" ;
    				$('.add-village-master-html').html("");
    				$('.add-village-master-html').html(response);
    				$("[name='village_record_id']").val("");
    				$("#village-master-model").find('.village-master-action-button').html(button_name);
    				$('.village-master-action-button').attr('title' , "{{ trans('messages.add') }}");
    				$("#village-master-model").find('.twt-village-modal-header-name').html(header_name);
    			}
    			openBootstrapModal('village-master-model');
    			$("[name='village_crud_module']").val(data_crud_module);
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
    $.validator.addMethod("validateUniqueVillageName", function (value, element) {
      	 
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: village_module_url +'checkUniqueVillageName',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'village_name': $.trim($("[name='village_name']").val()),'city': $.trim($("[name='city']").val()),'state': $.trim($("[name='state_name']").val()),'village_record_id': ( $.trim($("[name='village_record_id']").val()) != '' ? $.trim($("[name='village_record_id']").val()) : null) 
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
    }, '{{ trans("messages.error-unique-village-name") }}');

    function searchField(){
    	var search_by = $.trim($('[name="search_by"]').val());
    	var search_status = $.trim($('[name="search_status"]').val());
    	var search_state = $.trim($('[name="search_state"]').val());
    	var search_city = $.trim($('[name="search_city"]').val());
    	
    	var searchData = {
                'search_by':search_by,
                'search_status': search_status,
                'search_state':search_state,
                'search_city':search_city,
            }
            return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(village_module_url + 'filter' , searchFieldName);
    }
    var paginationUrl = village_module_url + 'filter'

	function getStateDetails(thisitem){
    	var state_id =	$.trim($("[name='city']").find('option:selected').attr('data-state-id'));
		if(state_id != "" && state_id != null){
			$("[name='state_name'] option[data-state-record-id='" + state_id + "']").prop("selected", true).trigger('change');
	    }
    }
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script> 
