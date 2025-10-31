<!-- shift edit form-->

<div class="modal fade document-folder" id="edit-shift-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.edit-shift") }} <span class="twt-custom-modal-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'edit-shift-form' , 'method' => 'post')) !!}
            	<div class="modal-body edit-shift-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" onclick="updateEmployeeShift(this);"  title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<script>
    $("#edit-shift-form").validate({
        errorClass: "invalid-input",
        rules: {
        	employee_shift: {
                required: true
            },
            shift_effective_from_date: {
                required: true
            },
            shift_effective_date: {
                required: true
            },
        },
        messages: {
        	employee_shift: {
                required: "{{ trans('messages.require-select-shift') }}"
            },
            shift_effective_from_date: {
                required: "{{ trans('messages.require-start-date') }}"
            },
            shift_effective_date: {
                required: function(){
					return ( ( $.trim($("[name='update_shift_employee_history_id']").val()) != "" && $.trim($("[name='update_shift_employee_history_id']").val()) != null ) ? "{{ trans('messages.require-end-date') }}" : "{{ trans('messages.require-start-date') }}" )
                } 
            },
        },
    });
    
    function editShift(thisitem){
    	current_selected_row = thisitem;
    	var record_id = $.trim($(thisitem).attr("data-record-id"));
    	var last_shift_date = $.trim($(thisitem).attr("data-last-shift-date"));
    	$.ajax({
    		type: "POST",
    		url: employee_module_url + 'getEmployeeShiftInfo',
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'record_id':record_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    	 		hideLoader();
    			if( response != "" && response != null ){
    				$(".edit-shift-html").html(response);
    				$('#edit-shift-modal').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
    				openBootstrapModal("edit-shift-modal");
    				$(function(){
    					$("[name='shift_effective_date']").datetimepicker({
    				        useCurrent: false,
    				        viewMode: 'days',
    				        ignoreReadonly: true,
    				        format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
    				        showClear: true,
    				        showClose: true,
    				        widgetPositioning: {
    				            vertical: 'bottom',
    				            horizontal: 'auto'

    				        },
    				        icons: {
    				            clear: 'fa fa-trash',
    				            Close: 'fa fa-trash',
    				        },
    				    }).on('dp.show' , function(e){
    					    var shift_effective_date_fillable = $.trim($("[name='shift_effective_date']").val());
    					    if( shift_effective_date_fillable == "" || designation_effective_date_fillable == null){
    					    	$("[name='shift_effective_date']").data('DateTimePicker').defaultDate(moment(last_shift_date,'YYYY-MM-DD').format('DD-MM-YYYY'));
    							$("[name='shift_effective_date']").val("");
    					    }
    				    });

    					$("[name='shift_effective_from_date']").datetimepicker({
    				        useCurrent: false,
    				        viewMode: 'days',
    				        ignoreReadonly: true,
    				        format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
    				        showClear: true,
    				        showClose: true,
    				        widgetPositioning: {
    				            vertical: 'bottom',
    				            horizontal: 'auto'

    				        },
    				        icons: {
    				            clear: 'fa fa-trash',
    				            Close: 'fa fa-trash',
    				        },
    				    }).on('dp.show' , function(e){
    					    /* var shift_effective_date_fillable = $.trim($("[name='shift_effective_date']").val());
    					    if( shift_effective_date_fillable == "" || designation_effective_date_fillable == null){
    					    	$("[name='shift_effective_date']").data('DateTimePicker').defaultDate(moment(last_shift_date,'YYYY-MM-DD').format('DD-MM-YYYY'));
    							$("[name='shift_effective_date']").val("");
    					    } */
    				    });

    					

    					//console.log(last_shift_date);
    					last_shift_date = moment(last_shift_date, "YYYY-MM-DD").add(1, 'days');
    					//console.log("last_shift_date = " + last_shift_date );
    					///console.log(last_shift_date);
						$("[name='shift_effective_date']").data("DateTimePicker").minDate(moment(last_shift_date,'YYYY-MM-DD').startOf('d'));

						var emp_last_salary_generate_date = $.trim($("[name='emp_last_salary_generate_date']").val());
						if( emp_last_salary_generate_date != "" && emp_last_salary_generate_date != null ){
							if( moment(moment(last_shift_date,'YYYY-MM-DD')).isBefore(emp_last_salary_generate_date) ){
								$("[name='shift_effective_date']").data("DateTimePicker").minDate(moment(emp_last_salary_generate_date,'YYYY-MM-DD').startOf('d'));
							}
						}
						
    					var current_date = moment().format('YYYY-MM-DD');
    				    if( moment(moment(last_shift_date,'YYYY-MM-DD')).isAfter(current_date) ){
    					
    					} else {
    						 //$("[name='shift_effective_date']").data("DateTimePicker").maxDate(moment().endOf('d'));
    					}
    				})
    				
    			}
    	 	},
    		error: function() {
    			hideLoader();
    		}
    	});
     }



     function updateEmployeeShift(){
    	if( $("#edit-shift-form").valid() != true ){
    		return false;
    	}

    	var update_shift_employee_id = $.trim($("[name='update_shift_employee_id']").val());
    	var employee_shift = $.trim($("[name='employee_shift']").val());
    	var shift_effective_date = $.trim($("[name='shift_effective_date']").val());
    	var update_shift_employee_history_id = $.trim($("[name='update_shift_employee_history_id']").val());

    	var old_shift_value = $.trim($("[name='employee_shift']").attr('data-old-value'));
    	var new_shift_value = $.trim($("[name='employee_shift'] option:selected").attr('data-id'));

    	//console.log("old_shift_value = " + old_shift_value );
    	//console.log("new_shift_value = " + new_shift_value );

    	if( update_shift_employee_history_id == "" || update_shift_employee_history_id == null ){
	    	if( old_shift_value != "" && old_shift_value != null &&  new_shift_value != "" && new_shift_value != null && ( old_shift_value ==  new_shift_value ) ){
	    		alertifyMessage('error' , '{{ trans("messages.same-as-old-value-select" , [ "module" => enumText( config("constants.SHIFT_RECORD_TYPE") )  ]    ) }}');
	    		return false
	    	}
    	}

    	var shift_effective_from_date = $.trim($("[name='shift_effective_from_date']").val());

    	alertify.confirm('{{ trans("messages.update-shift") }}', '{{ trans("messages.common-confirm-msg" , [ "module" => trans("messages.update-shift")  ]) }}' ,function() {
    		$.ajax({
    			type: "POST",
    			url: employee_module_url + 'updateEmployeeDataInfo',
    			dataType : 'json',
    			data: {
    				"_token": "{{ csrf_token() }}",
    				'employee_id':update_shift_employee_id,
    				'update_data_value':employee_shift,
    				'history_record_id' : update_shift_employee_history_id , 
    				'update_request' : '{{ config("constants.SHIFT_RECORD_TYPE") }}',
    				'effective_date':shift_effective_date,
    				'shift_effective_from_date':shift_effective_from_date,
    			},
    			beforeSend: function() {
    				//block ui
    				showLoader();
    			},
    			success: function(response) {
    		 		hideLoader();
    		 		if(response.status_code == 1 ){
    					alertifyMessage('success' , response.message);
    					$("#edit-shift-modal").modal('hide');
    					$("#employee-designation-history-modal").modal("hide");
    					$(".employee-job-record").html(response.data.html)
    				} else {
    			    	alertifyMessage('error' , response.message);
    				}
    		 	},
    			error: function() {
    				hideLoader();
    			}
    		});
    	},function() {});
    }

    function editShiftHistory(thisitem){
 		var history_start_date = $.trim($(thisitem).attr("data-start-date"));
 		var history_end_date = $.trim($(thisitem).attr("data-end-date"));

 		var record_id = $.trim($(thisitem).attr("data-record-id"));
 		$.ajax({
 			type: "POST",
 			url: employee_module_url + 'getShiftHistoryInfo',
 			data: {
 				"_token": "{{ csrf_token() }}",
 				'record_id':record_id
 			},
 			beforeSend: function() {
 				//block ui
 				showLoader();
 			},
 			success: function(response) {
 		 		hideLoader();
 				if( response != "" && response != null ){
 					$(".edit-shift-html").html(response);
 					$('#edit-shift-modal').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
    				openBootstrapModal("edit-shift-modal");
 					$(function(){
 						$("[name='shift_effective_date']").datetimepicker({
 					        useCurrent: false,
 					        viewMode: 'days',
 					        ignoreReadonly: true,
 					        format: ' {{ config("constants.DEFAULT_DATE_FORMAT") }} ',
 					        showClear: true,
 					        showClose: true,
 					        widgetPositioning: {
 					            vertical: 'bottom',
 					            horizontal: 'auto'

 					        },
 					        icons: {
 					            clear: 'fa fa-trash',
 					            Close: 'fa fa-trash',
 					        },
 					    });
 						$("[name='shift_effective_from_date']").datetimepicker({
    				        useCurrent: false,
    				        viewMode: 'days',
    				        ignoreReadonly: true,
    				        format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
    				        showClear: true,
    				        showClose: true,
    				        widgetPositioning: {
    				            vertical: 'bottom',
    				            horizontal: 'auto'

    				        },
    				        icons: {
    				            clear: 'fa fa-trash',
    				            Close: 'fa fa-trash',
    				        },
    				    }).on('dp.show' , function(e){
    					    /* var shift_effective_date_fillable = $.trim($("[name='shift_effective_date']").val());
    					    if( shift_effective_date_fillable == "" || designation_effective_date_fillable == null){
    					    	$("[name='shift_effective_date']").data('DateTimePicker').defaultDate(moment(last_shift_date,'YYYY-MM-DD').format('DD-MM-YYYY'));
    							$("[name='shift_effective_date']").val("");
    					    } */
    				    });
 					    $("[name='shift_effective_date']").data("DateTimePicker").minDate(moment(history_start_date,'YYYY-MM-DD'));
 					  	 var current_date = moment().format('YYYY-MM-DD');
					    if( moment(moment(history_start_date,'YYYY-MM-DD')).isAfter(current_date) ){
						
						} else {
							 $("[name='shift_effective_date']").data("DateTimePicker").maxDate(moment().endOf('d'));
						}
 					})
 					
 				}
 		 	},
 			error: function() {
 				hideLoader();
 			}
 		});
 		
 	}
</script>