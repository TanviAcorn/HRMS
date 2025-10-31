 <div class="modal fade document-folder" id="city-master-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-city-modal-header-name" id="exampleModalLabel">{{ trans("messages.add-city") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-city-master-model-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                    <div class="modal-body add-city-master-html">
                        
                    </div>
                    <div class="modal-footer justify-content-end">
                    	<input type="hidden" name="city_record_id" value="">
                    	<input type="hidden" name="city_crud_module" value="">
                        <button type="button" onclick="addCityModel()" class="btn bg-theme text-white action-button city-master-action-button btn-add" title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
<script>
var city_module_url = '{{config("constants.CITY_MASTER_URL")}}' + '/';

    $("#add-city-master-model-form").validate({
    	errorClass: "invalid-input",
		onfocusout: false,
		onkeyup: false,
        rules: {
            city_name: {
                required: true,noSpace: true,validateUniqueCityName:true
            },
            state: {
                required: true
            },
        },
        messages: {
            city_name: {
                required: "{{ trans('messages.required-city-name') }}"
            },
            state: {
                required: "{{ trans('messages.required-state') }}"
            },
        }
    });
    function openCityModel(thisitem){
    	editCityModel(thisitem);
    }
    function addCityModel(){
    	if($('#add-city-master-model-form').valid() != true){
			return false;
		}
    	var city_record_id = $.trim($('[name="city_record_id"]').val());
		var city_name = $.trim($('[name="city_name"]').val());
		var state = $.trim($('[name="state"]').val());
		var city_crud_module = $.trim($('[name="city_crud_module"]').val());
		var city_chart_color = $.trim($('[name="city_chart_color"]').val());
		
		var confirm_box = "";
	    var confirm_box_msg = "";
	    
	    if(city_record_id == 0){
	    	confirm_box = "{{ trans('messages.add-city') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-city')]) }}";
	    } else {
	    	confirm_box = "{{ trans('messages.update-city') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-city')]) }}";
	    } 

	    alertify.confirm(confirm_box,confirm_box_msg,function() {   
			$.ajax({
				type: "POST",
				dataType: "json",
				url: city_module_url + 'add',
				data: {
					"_token": "{{ csrf_token() }}",
					'city_name':city_name,
					'state':state,
					'city_record_id':city_record_id,
					'city_crud_module':city_crud_module,
					'city_chart_color' : city_chart_color,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),
				},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						$("#city-master-model").modal('hide');
						alertifyMessage('success',response.message);
						if(city_record_id != '' && city_record_id != null){
							$(current_row).parents('.city-record').html(response.data.html);
						}else{
							if( city_crud_module != "" && city_crud_module != null && city_crud_module == "{{ config('constants.SELECTION_NO') }}"){
								$(current_row).parents('.dependant-field-selection').find('.city-list').html(response.data.html);
								
								$(current_row).parents('.modal').find('.city-list').html(response.data.html);
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
    function editCityModel(thisitem){
    	current_row = thisitem;
    	var city_record_id = $.trim($(thisitem).attr('data-record-id'));
    	var data_crud_module = $.trim($(thisitem).attr('data-city-module'));
    	
    	$.ajax({
    		type: "POST",
    		url: city_module_url + 'edit',
    		data: {
    			"_token": "{{ csrf_token() }}",'city_record_id':city_record_id,
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			if(city_record_id !="" && city_record_id != null){
    				var header_name = "{{ trans('messages.update-city') }}";
    				var button_name = "{{ trans('messages.update') }}";
    				$('.add-city-master-html').html("");
    				$('.add-city-master-html').html(response);
    				$("[name='city_record_id']").val(city_record_id);
    				$("#city-master-model").find('.city-master-action-button').html(button_name);
    				$('.city-master-action-button ').attr('title' , "{{ trans('messages.update') }}");
    				$("#city-master-model").find('.twt-city-modal-header-name').html(header_name);
    			} else {
    				var header_name = "{{ trans('messages.add-city') }}" ;
    				var button_name = "{{ trans('messages.add') }}" ;
    				$('.add-city-master-html').html("");
    				$('.add-city-master-html').html(response);
    				$("[name='city_record_id']").val("");
    				$("#city-master-model").find('.city-master-action-button').html(button_name);
    				$('.city-master-action-button ').attr('title' , "{{ trans('messages.add') }}");
    				$("#city-master-model").find('.twt-city-modal-header-name').html(header_name);
    			}
    			openBootstrapModal('city-master-model');
    			$("[name='city_crud_module']").val(data_crud_module);
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
    $.validator.addMethod("validateUniqueCityName", function (value, element) {
      	 
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: city_module_url +'checkUniqueCityName',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'city_name': $.trim($("[name='city_name']").val()),'state': $.trim($("[name='state']").val()),'city_record_id': ( $.trim($("[name='city_record_id']").val()) != '' ? $.trim($("[name='city_record_id']").val()) : null) 
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
    }, '{{ trans("messages.error-unique-city-name") }}');

    function searchField(){
    	var search_by = $.trim($('[name="search_by"]').val());
    	var search_status = $.trim($('[name="search_status"]').val());
    	var search_state = $.trim($('[name="search_state"]').val());
    	
    	var searchData = {
                'search_by':search_by,
                'search_status': search_status,
                'search_state':search_state,
            }
            return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(city_module_url + 'filter' , searchFieldName);
    }
    var paginationUrl = city_module_url + 'filter'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script> 
