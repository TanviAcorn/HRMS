<div class="modal fade document-folder" id="salary-group-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-salary-group-header-name" id="exampleModalLabel">{{ trans("messages.add-salary-groups") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-salary-group-model-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                    <div class="modal-body add-salary-group-html">
                        
                    </div>
                    <div class="modal-footer justify-content-end">
                    <input type="hidden" name="salary_group_record_id" value="">
                        <button type="button" onclick="addSalaryGroupModel()" class="btn bg-theme text-white action-button salary-group-action-button btn-add" title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    
<script>
var salary_group_module_url = '{{config("constants.SALARY_GROUPS_MASTER_URL")}}' + '/';
    $("#add-salary-group-model-form").validate({
        errorClass: "invalid-input",
        onfocusout: false,
		onkeyup: false,
        rules: {
            group_name: {
                required: true,noSpace: true,validateUniqueSalaryGroupName:true
            },
        },
        messages: {
            group_name: {
                required: "{{ trans('messages.require-group-name') }}"
            },
        }
    });
    function openSalaryGroupModel(thisitem){
    	editSalaryGroupsModel(thisitem);
    }
    var current_row ='';
    function editSalaryGroupsModel(thisitem){
    	current_row = thisitem;
    	var salary_group_record_id = $.trim($(thisitem).attr('data-record-id'));
    	$.ajax({
    		type: "POST",
    		url: salary_group_module_url + 'edit',
    		data: {
    			"_token": "{{ csrf_token() }}",'salary_group_record_id':salary_group_record_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			if(salary_group_record_id !="" && salary_group_record_id != null){
    				var header_name = "{{ trans('messages.update-salary-groups') }}";
    				var button_name = "{{ trans('messages.update') }}";
    				$('.add-salary-group-html').html("");
    				$('.add-salary-group-html').html(response);
    				$("[name='salary_group_record_id']").val(salary_group_record_id);
    				$("#salary-group-model").find('.salary-group-action-button').html(button_name);
    				$('.salary-group-action-button').attr('title' , "{{ trans('messages.update') }}");
    				$("#salary-group-model").find('.twt-salary-group-header-name').html(header_name);
    			} else {
    				var header_name = "{{ trans('messages.add-salary-groups') }}" ;
    				var button_name = "{{ trans('messages.add') }}" ;
    				$('.add-salary-group-html').html("");
    				$('.add-salary-group-html').html(response);
    				$("[name='salary_group_record_id']").val("");
    				$("#salary-group-model").find('.salary-group-action-button').html(button_name);
    				$('.salary-group-action-button').attr('title' , "{{ trans('messages.add') }}");
    				$("#salary-group-model").find('.twt-salary-group-header-name').html(header_name);
    			}
    			
    			openBootstrapModal('salary-group-model');
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
    function addSalaryGroupModel(){
    	if($('#add-salary-group-model-form').valid() != true){
			return false;
		}
		var salary_components_earnings_value = $('.salary-components-earnings-row:checked').length;
		var salary_components_deduction_value = $('.salary-components-deduction-row:checked').length;
	        	
		if( ( salary_components_earnings_value > 0 ) || ( salary_components_deduction_value > 0 ) ){
        	var salary_components_earnings_status = false;
        	var salary_components_deduction_status = false;
	    	$('.salary-groups-earning-deduction tr').each(function(){
	       		salary_components_earnings_status = true;
	         	salary_components_deduction_status = true;
	        }); 
		}
        if( (salary_components_earnings_status != true ) || (salary_components_deduction_status != true ) ){
        	alertifyMessage("error","{{ trans('messages.require-salary-components-checkbox') }} ");
        	return false;
        } 	
		
    	var salary_group_record_id = $.trim($('[name="salary_group_record_id"]').val());
		var group_name = $.trim($('[name="group_name"]').val());
		var group_description = $.trim($('[name="group_description"]').val());
		var salary_components_earning = $.trim($('[name="salary_components_earning[]"]').val());
		var salary_components_deduction = $.trim($('[name="salary_components_deduction[]"]').val());
		
		var confirm_box = "";
	    var confirm_box_msg = "";
	    
	    if(salary_group_record_id == 0){
	    	confirm_box = "{{ trans('messages.add-salary-groups') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-salary-groups')]) }}";
	    } else {
	    	confirm_box = "{{ trans('messages.update-salary-groups') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-salary-groups')]) }}";
	    } 
		var formData = new FormData($('#add-salary-group-model-form')[0]);
		formData.append('row_index',$(current_row).parents('tr').find('.sr-col').html());
	    alertify.confirm(confirm_box,confirm_box_msg,function() {   
			$.ajax({
				type: "POST",
				dataType: "json",
				url: salary_group_module_url + 'add',
				data:formData,
				processData:false,
				contentType:false,
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						$("#salary-group-model").modal('hide');
						alertifyMessage('success',response.message);
						if(salary_group_record_id != '' && salary_group_record_id != null){
							$(current_row).parents('.salary-groups-record').html(response.data.html);
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

    	searchAjax(salary_group_module_url + 'filter' , searchFieldName);
    }

    $.validator.addMethod("validateUniqueSalaryGroupName", function (value, element) {
     	 
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: salary_group_module_url +'checkUniqueSalaryGroupName',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'group_name': $.trim($("[name='group_name']").val()),'salary_group_record_id': ( $.trim($("[name='salary_group_record_id']").val()) != '' ? $.trim($("[name='salary_group_record_id']").val()) : null) 
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
    }, '{{ trans("messages.error-unique-salary-group-name") }}');
    var paginationUrl = salary_group_module_url + 'filter'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script> 

    