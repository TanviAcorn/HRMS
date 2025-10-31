<div class="modal fade document-folder document-type upload-profile-image" id="suspend-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-header-name" id="exampleModalLabel">{{ trans("messages.edit-suspend") }} <span class="twt-custom-modal-title"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-suspend-model-form' , 'method' => 'post' ,  'url' => 'addSuspendHistory')) !!}
                	<div class="modal-body">
                        <div class="row employee-suspend-info">
                            
                        </div>
                        <div class="modal-footer justify-content-end">
                        	<input type="hidden" name="suspend_employee_id" value="">
                        	
                            <button type="button" onclick="addSuspendHistory()" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add w-auto" title="{{ trans('messages.suspend') }}">{{ trans('messages.suspend') }}</button>
                            <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                        </div>
                	</div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <script>
    var employee_module_url = '{{config("constants.EMPLOYEE_MASTER_URL")}}' + '/';
    $("#add-suspend-model-form").validate({
        errorClass: "invalid-input",
	    onfocusout: false,
        rules: {
            suspend_from_date: {
                required: true,noSpace:true
            },
            suspend_to_date: {
                required: true,noSpace:true,validateUniqueSuspendDate:true
            },
            suspension_reason: {
                required: true,noSpace:true
            },
        },
        messages: {
            suspend_from_date: {
                required: "{{ trans('messages.please-enter-from-date') }}"
            },
            suspend_to_date: {
                required: "{{ trans('messages.please-enter-to-date') }}"
            },
            suspension_reason: {
                required: "{{ trans('messages.please-enter-suspension-reason') }}"
            },
        },
        submitHandler: function(form) {
            showLoader()
            form.submit();
        }
    });
    
    function openSuspendModel(thisitem){
		var employee_id = $.trim($(thisitem).attr('data-employee-id'));
		var suspend_record_id = $.trim($(thisitem).attr('data-record-id'));
		var joining_date = $.trim($(thisitem).attr('data-joining-date'));
		//console.log("joining_date = " + joining_date );
		
		if( employee_id != "" && employee_id != null ){
			$.ajax({
	     		type: "POST",
	     		url: employee_module_url + 'getSuspendInfo',
	     		data: { "_token": "{{ csrf_token() }}",'employee_id':employee_id , 'suspend_record_id' : suspend_record_id   },
	     		beforeSend: function() {
	     			//block ui
	     			showLoader();
	     		},
	     		success: function(response) {
	     			hideLoader();
	     			if( response != "" && response != null ){
						$(".employee-suspend-info").html(response);
						$("[name='suspend_employee_id']").val(employee_id);
						suspend_record_id = $.trim($("[name='suspend_record_id']").val());
						if( suspend_record_id != "" && suspend_record_id != null ){
							$("[name='suspend_record_id']").val(suspend_record_id);
							$("#suspend-model").find('.action-button').html("{{ trans('messages.update') }}");
							$("#suspend-model").find('.action-button').attr( 'title' ,  "{{ trans('messages.update') }}");	
							
						} else {
							$("[name='suspend_record_id']").val("");
							$("#suspend-model").find('.action-button').html("{{ trans('messages.suspend') }}");
							$("#suspend-model").find('.action-button').attr( 'title' ,  "{{ trans('messages.suspend') }}");
							
							
						}

						var suspend_from_date = $.trim($("[name='suspend_from_date']").val());
				    	var suspend_to_date = $.trim($("[name='suspend_to_date']").val());

				    	if( suspend_from_date != "" && suspend_from_date != null ){
				    		$("#suspend-model").find('.twt-header-name').html( '{{ trans("messages.edit-suspend") }}' + common_emp_modal_header_title );
					    } else {
					    	$("#suspend-model").find('.twt-header-name').html( '{{ trans("messages.suspend") }}' + common_emp_modal_header_title );
						}
				    	
						
						
						var date_before_month = moment().subtract(1, "month").startOf('d').format('YYYY-MM-DD');
						//$('#suspend-model').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
						openBootstrapModal('suspend-model');
						$(function() {
					        $('[name="suspend_from_date"], [name="suspend_to_date"]').datetimepicker({
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
					        });
							
					        if( suspend_record_id == "" || suspend_record_id == null ){
					        	
								if(moment(date_before_month).isBefore(moment(joining_date))){
					        		$("[name='suspend_from_date']").data("DateTimePicker").minDate(moment(joining_date,'YYYY-MM-DD'));
					        		$("[name='suspend_to_date']").data('DateTimePicker').minDate(moment(joining_date,'YYYY-MM-DD'));
							    } else {
							    	$("[name='suspend_from_date']").data('DateTimePicker').minDate(moment().subtract(1, "month").startOf('d'));
							        $("[name='suspend_to_date']").data('DateTimePicker').minDate(moment().subtract(1, "month").startOf('d'));
							 	}

								//console.log("else");
						    	//console.log("date_before_month = " + date_before_month );
					        	
						    } else {

						    	
							    //console.log("suspend_from_date = " + suspend_from_date );	
								if( suspend_from_date != "" && suspend_from_date != null ){
									$("[name='suspend_to_date']").data("DateTimePicker").minDate(moment(suspend_from_date,'{{ config("constants.DEFAULT_DATE_FORMAT") }}').startOf('d'));
						    	} else {
						    		$("[name='suspend_from_date']").data('DateTimePicker').minDate(false);
								}

						    	if( suspend_to_date != "" && suspend_to_date != null ){
						    		$("[name='suspend_from_date']").data("DateTimePicker").maxDate(moment(suspend_to_date,'{{ config("constants.DEFAULT_DATE_FORMAT") }}').endOf('d'));
						    	} else {
						    		$("[name='suspend_to_date']").data('DateTimePicker').maxDate(false);
								}

						    	//console.log(moment(date_before_month));
						    	//console.log(moment(joining_date));
						    	//console.log(moment(date_before_month).isBefore(moment(joining_date)));
						    	
						    	if(moment(date_before_month).isBefore(moment(joining_date))){
							    	//console.log("dddd");
					        		$("[name='suspend_from_date']").data("DateTimePicker").minDate(moment(joining_date,'YYYY-MM-DD'));
					        		if( suspend_to_date == "" || suspend_to_date == null ){
					        			$("[name='suspend_to_date']").data('DateTimePicker').minDate(moment(joining_date,'YYYY-MM-DD'));
						        	}
					        		
							    } else {
								    //console.log("ssssswww");
							    	$("[name='suspend_from_date']").data('DateTimePicker').minDate(moment().subtract(1, "month").startOf('d'));
							    	if( suspend_from_date == "" || suspend_from_date == null ){
							        	$("[name='suspend_to_date']").data('DateTimePicker').minDate(moment().subtract(1, "month").startOf('d'));
							    	}
							    	if( suspend_to_date != "" && suspend_to_date != null ){
							    		$("[name='suspend_from_date']").data("DateTimePicker").maxDate(moment(suspend_to_date,'{{ config("constants.DEFAULT_DATE_FORMAT") }}').endOf('d'));
									}
							 	}
						    	
						    	
						    	
						    	
						    	
						        
							}
					        
					        

					        $("[name='suspend_from_date']").datetimepicker().on('dp.change', function(e) {
					    		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
					    			var incrementDay = moment((e.date)).startOf('d');
					    		 	$("[name='suspend_to_date']").data('DateTimePicker').minDate(incrementDay);
					    		} else {
					    			$("[name='suspend_to_date']").data('DateTimePicker').minDate(false);
					    		} 
					    		
					    	    $(this).data("DateTimePicker").hide();
					    	});

					        $("[name='suspend_to_date']").datetimepicker().on('dp.change', function(e) {
					        	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
					    	        var decrementDay = moment((e.date)).endOf('d');
					    	        $("[name='suspend_from_date']").data('DateTimePicker').maxDate(decrementDay);
					        	} else {
					        		 $("[name='suspend_from_date']").data('DateTimePicker').maxDate(false);
					            }
					            $(this).data("DateTimePicker").hide();
					        });
					    });
					} 
	     		},
	     		error: function() {
	     			hideLoader();
	     		}
	     	});
			
			
		}
	}

	function addSuspendHistory(){
		if( $("#add-suspend-model-form").valid() != true  ){
			return false;
		}
		var employee_id = $.trim($("[name='suspend_employee_id']").val());
		var suspend_record_id = $.trim($("[name='suspend_record_id']").val());
		
		var suspend_from_date = $.trim($("[name='suspend_from_date']").val());
		
		var suspend_to_date = $.trim($("[name='suspend_to_date']").val());
		var suspension_reason = $.trim($("[name='suspension_reason']").val());
		
		alertify.confirm("{{ trans('messages.suspend-employee') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.suspend-employee')]) }}",function() { 
	    	 $.ajax({
	     		type: "POST",
	     		dataType :'json',
	     		url: employee_module_url + 'addSuspendHistory',
	     		data: { 
		     			"_token": "{{ csrf_token() }}",
		     			'employee_id':employee_id,
		     			'suspend_from_date':suspend_from_date,
		     			'suspend_to_date':suspend_to_date,
		     			'suspension_reason':suspension_reason,
		     			'suspend_record_id':suspend_record_id, 
		     		},
	     		beforeSend: function() {
	     			//block ui
	     			showLoader();
	     		},
	     		success: function(response) {
	     			hideLoader();
	     			if( response.status_code == 1 ){
						$("#suspend-model").modal('hide');
						alertifyMessage('success',response.message);
						//console.log("update status html");
						///console.log(response.data.updateSuspendHtml);
						
						if( response.data.updateSuspendHtml == true){
							$(".profile-section").find('.login-status').hide();
							$(".profile-section").find('.relieved-status').hide();
							$(".profile-section").find('.suspended-status').show();
							//console.log("welcome");
						} else {
							$(".profile-section").find('.login-status').show();
							$(".profile-section").find('.relieved-status').hide();
							$(".profile-section").find('.suspended-status').hide();
						}

						$('.employee-primary-details').html(response.data.primaryDetailsInfo);
						$('.employee-profile-pic-view--master-div-html').html(response.data.mainProfileInfo);
						
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

	$.validator.addMethod("validateUniqueSuspendDate", function (value, element) {
    	 
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: employee_module_url +'checkUniqueSuspendDate',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'suspend_from_date': $.trim($("[name='suspend_from_date']").val()),
    			'suspend_to_date': $.trim($("[name='suspend_to_date']").val()),
    			'employee_id': ( $.trim($("[name='suspend_employee_id']").val()) != '' ? $.trim($("[name='suspend_employee_id']").val()) : null),
    			'suspend_record_id': ( $.trim($("[name='suspend_record_id']").val()) != '' ? $.trim($("[name='suspend_record_id']").val()) : null)
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
    }, '{{ trans("messages.error-unique-suspend-date") }}');
	
    </script>