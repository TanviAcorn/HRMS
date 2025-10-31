 <div class="modal fade document-folder" id="salary-components-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-salary-components-header-name" id="exampleModalLabel">{{ trans("messages.add-salary-components") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-salary-components-model-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                    <div class="modal-body add-salary-components-html">
                        
                    </div>
                    <div class="modal-footer justify-content-end">
                    	<input type="hidden" name="salary_components_record_id" value="">
                        <button type="button" onclick="addSalaryComponentsModel()" class="btn bg-theme text-white action-button salary-components-action-button btn-add" title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    
<script>
var salary_components_module_url = '{{config("constants.SALARY_COMPONENTS_MASTER_URL")}}' + '/';

	$("#add-salary-components-model-form").validate({
		errorClass: "invalid-input",
		onfocusout: false,
		onkeyup: false,
        rules: {
            component_name: {
                required: true,noSpace: true,validateUniqueSalaryComponentsName:true
            },
            salary_components_type: {
                required: true,noSpace: true
            },
            consider_for_pf_calculation: {
                required: function(){
					return ( $("[name='salary_components_type']:checked").val() == "{{config('constants.SALARY_COMPONENT_TYPE_EARNING') }}" ? true : false )
               } 
            },
        },
        messages: {
            component_name: {
                required: "{{ trans('messages.require-component-name') }}"
            },
            salary_components_type: {
                required: "{{ trans('messages.require-type') }}"
            },
            consider_for_pf_calculation: {
                required: "{{ trans('messages.require-under-pf-calculation') }}"
            },
        }
    });
    function openSalaryComponentsModel(thisitem){
    	editSalaryComponentsModel(thisitem)
    }

    function addSalaryComponentsModel(){
    	if($('#add-salary-components-model-form').valid() != true){
			return false;
		}
    	var salary_components_record_id = $.trim($('[name="salary_components_record_id"]').val());
    	var component_name = $.trim($('[name="component_name"]').val());
		var component_description = $.trim($('[name="component_description"]').val());
		var salary_components_type = $.trim($('[name="salary_components_type"]:checked').val());
		var consider_for_pf_calculation = $.trim($('[name="consider_for_pf_calculation"]:checked').val());
		
		var confirm_box = "";
	    var confirm_box_msg = "";
	    
	    if(salary_components_record_id == 0){
	    	confirm_box = "{{ trans('messages.add-salary-components') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-salary-components')]) }}";
	    } else {
	    	confirm_box = "{{ trans('messages.update-salary-components') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-salary-components')]) }}";
	    } 
	    alertify.confirm(confirm_box,confirm_box_msg,function() {   
			$.ajax({
				type: "POST",
				dataType: "json",
				url: salary_components_module_url + 'add',
				data: {
					"_token": "{{ csrf_token() }}",
					'consider_for_pf_calculation' : consider_for_pf_calculation , 
					'component_name':component_name,'component_description':component_description,'salary_components_record_id':salary_components_record_id,'salary_components_type':salary_components_type,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),
				},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						$("#salary-components-model").modal('hide');
						alertifyMessage('success',response.message);
						if(salary_components_record_id != '' && salary_components_record_id != null){
							$(current_row).parents('.salary-components-record').html(response.data.html);
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
    function editSalaryComponentsModel(thisitem){
    	current_row = thisitem;
    	var salary_components_record_id = $.trim($(thisitem).attr('data-record-id'));
    	$.ajax({
    		type: "POST",
    		url: salary_components_module_url + 'edit',
    		data: {
    			"_token": "{{ csrf_token() }}",'salary_components_record_id':salary_components_record_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			if(salary_components_record_id !="" && salary_components_record_id != null){
    				var header_name = "{{ trans('messages.update-salary-components') }}";
    				var button_name = "{{ trans('messages.update') }}";
    				$('.add-salary-components-html').html("");
    				$('.add-salary-components-html').html(response);
    				$("[name='salary_components_record_id']").val(salary_components_record_id);
    				$("#salary-components-model").find('.salary-components-action-button').html(button_name);
    				$('.salary-components-action-button').attr('title' , "{{ trans('messages.update') }}");
    				$("#salary-components-model").find('.twt-salary-components-header-name').html(header_name);
    			} else {
    				var header_name = "{{ trans('messages.add-salary-components') }}" ;
    				var button_name = "{{ trans('messages.add') }}" ;
    				$('.add-salary-components-html').html("");
    				$('.add-salary-components-html').html(response);
    				$("[name='salary_components_record_id']").val("");
    				$("#salary-components-model").find('.salary-components-action-button').html(button_name);
    				$('.salary-components-action-button ').attr('title' , "{{ trans('messages.add') }}");
    				$("#salary-components-model").find('.twt-salary-components-header-name').html(header_name);
    			}
    			openBootstrapModal('salary-components-model');
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
    function searchField(){
    	var search_by = $.trim($('[name="search_by"]').val());
    	var search_salary_components_type = $.trim($('[name="search_salary_components_type"]').val());
    	var search_status = $.trim($('[name="search_status"]').val());
    	var search_consider_under_pf = $.trim($('[name="search_consider_under_pf"]').val());
    	
    	var searchData = {
        	'search_by':search_by,
        	'search_salary_components_type':search_salary_components_type,
        	'search_consider_under_pf':search_consider_under_pf,
        	'search_status': search_status,
                
        }
    	return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(salary_components_module_url + 'filter' , searchFieldName);
    }
    $.validator.addMethod("validateUniqueSalaryComponentsName", function (value, element) {
     	 
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: salary_components_module_url +'checkUniqueSalaryComponentsName',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'component_name': $.trim($("[name='component_name']").val()),'salary_components_type': $.trim($("[name='salary_components_type']:checked").val()),'salary_components_record_id': ( $.trim($("[name='salary_components_record_id']").val()) != '' ? $.trim($("[name='salary_components_record_id']").val()) : null) 
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
    }, '{{ trans("messages.error-unique-salary-components-name") }}');
    var paginationUrl = salary_components_module_url + 'filter'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script> 
