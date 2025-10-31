    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Leave Request for <span class="leave-submit-user-name">{{  ( session()->has('name') ? session()->get('name') : '' ) }}</span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
    </div>
    <section class="apply-leave">
        <div class="container-fluid">
            <div class="row p-0">
                <div class="card border-0 pb-3 col-12">
                    <div class="row">
                        <div class="col-md-6 apply-leave-left pt-3 px-0">
                            {!! Form::open(array( 'id '=> 'add-apply-leave-model-form' , 'method' => 'post' ,'files' => true ,  'url' => 'add')) !!}
                                <div class="form-row add-apply-leave-html">
                                   
                                </div>
                                <div class="col-12 submit-sticky px-0">
                                	<input type="hidden" name="duration_count" value="">
                                	<input type="hidden" name="apply_leave_id" value="">
                                	<input type="hidden" name="final_selected_image" value="">
            						<input type="hidden" name="remove_image" value="">
                                    <button type="button" onclick="addApplyLeaveModel()" class="btn btn-theme text-white" title="{{ trans('messages.request-leave') }}">{{ trans("messages.request-leave") }}</button>
                                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal" aria-label="Close" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</button>
                                </div>
                            {!! Form::close() !!}
                        </div>
                        <div class=" col-md-6 apply-leave-right pt-2 px-4">
                            <div class="row">
                                <div class="col-12 leave-balance">
                                    <div class="row py-0 mt-2 leave-status-card align-items-center">
                                        <div class="col-12">
                                            <h5 class="apply-card-title mb-0">{{ trans("messages.my-leave-balance") }}
                                            </h5>
                                        </div>
                                        <div class="col-12 p-2 leave-status-item">
                                            <div class="row mt-1 leave-balance-modal-html d-flex justify-content-around">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="employee-leave-list-html">
	                                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
	<script>
    	$("#add-apply-leave-model-form").validate({
            errorClass: "invalid-input",
            onfocusout: false,
    	    onkeyup: false,
            rules: {
            	leave_from_date: {
                    required: true,
                    noSpace:true,
                    checkDuplicateLeave : true
                },
                leave_to_date: {
                    required: true,
                    noSpace:true
                },
                leave_types: {
                    required: true,
                    noSpace:true,
                    checkLeaveBalance : true,
                },
                leave_note: {
                    required: true,
                    noSpace:true
                },
            },
            messages: {
            	leave_from_date: {
                    required: "{{ trans('messages.please-enter-from-date') }}"
                },
                leave_to_date: {
                    required: "{{ trans('messages.please-enter-to-date') }}"
                },
                leave_types: {
                    required: "{{ trans('messages.please-select-leave-types') }}"
                },
                leave_note: {
                    required: "{{ trans('messages.please-enter-note') }}"
                },
            }
        });

    	$.validator.addMethod("checkDuplicateLeave", function(value, element) {
    	    var result = true;

    	    var leave_from_date = $.trim($("[name='leave_from_date']").val());
            var leave_to_date =  $.trim($("[name='leave_to_date']").val());

    	    if( leave_from_date != "" && leave_from_date != null && leave_to_date != "" && leave_to_date != null ){
    	    	ajaxResponse = $.ajax({
        	        type: "POST",
        	        async: false,
        	        headers: {
        	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        	        },
        	        url: '{{config("constants.MY_LEAVES_MASTER_URL")}}' + '/checkDuplicateLeave',
        	        dataType: "json",
        	        data: {
        	            "_token": "{{ csrf_token() }}",
        	            'leave_types': $.trim($("[name='leave_types']").val()),
        	            'leave_from_date': $.trim($("[name='leave_from_date']").val()),
        	            'leave_to_date': $.trim($("[name='leave_to_date']").val()),
        	            'dual_date_from_session': $.trim($("[name='dual_date_from_session']:checked").val()),
        	            'dual_date_to_session': $.trim($("[name='dual_date_to_session']:checked").val()),
        	            'single_date_session': $.trim($("[name='single_date_session']:checked").val()),
        	            'employee_id': $.trim($("[name='apply_leave_employee_id']").val()),
        	            'record_id': ($.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null)
        	        },
        	        beforeSend: function() {
        	            //block ui
        	            //showLoader();
        	        },
        	        success: function(response) {
        	            if (response.status_code == 1) {
        	                return false;
        	            } else {
        	                result = false;
        	                return true;
        	            }
        	        }
        	    });
        	}

    	    
    	    return result;
    	}, '{{ trans("messages.error-duplicate-leave-request") }}');
		var error_leave_balance_msg = '';	
    	$.validator.addMethod("checkLeaveBalance", function(value, element) {
    	    var result = true;

    	    var leave_from_date = $.trim($("[name='leave_from_date']").val());
            var leave_to_date =  $.trim($("[name='leave_to_date']").val());

    	    if( leave_from_date != "" && leave_from_date != null && leave_to_date != "" && leave_to_date != null ){
    	    	ajaxResponse = $.ajax({
        	        type: "POST",
        	        async: false,
        	        headers: {
        	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        	        },
        	        url: '{{config("constants.MY_LEAVES_MASTER_URL")}}' + '/checkLeaveBalance',
        	        dataType: "json",
        	        data: {
        	            "_token": "{{ csrf_token() }}",
        	            'leave_types': $.trim($("[name='leave_types']").val()),
        	            'leave_from_date': $.trim($("[name='leave_from_date']").val()),
        	            'leave_to_date': $.trim($("[name='leave_to_date']").val()),
        	            'dual_date_from_session': $.trim($("[name='dual_date_from_session']:checked").val()),
        	            'dual_date_to_session': $.trim($("[name='dual_date_to_session']:checked").val()),
        	            'single_date_session': $.trim($("[name='single_date_session']:checked").val()),
        	            'employee_id': $.trim($("[name='apply_leave_employee_id']").val()),
        	            'record_id': ($.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null)
        	        },
        	        beforeSend: function() {
        	            //block ui
        	            //showLoader();
        	        },
        	        success: function(response) {
        	        	error_leave_balance_msg = response.message;
        	            if (response.status_code == 1) {
        	                return false;
        	            } else {
        	                result = false;
        	                return true;
        	            }
        	        }
        	    });
        	}

    	    
    	    return result;
    	}, function (params, element) {
    		return error_leave_balance_msg;
    	});
        
        var my_leave_module_url = '{{config("constants.MY_LEAVES_MASTER_URL")}}' + '/';
        
        function openApplyLeaveModel(thisitem){
            var apply_leave_id = $.trim($("[name='apply_leave_id']") .val());
            var final_selected_image = $.trim($("[name='final_selected_image']") .val());
            var remove_image = $.trim($("[name='remove_image']") .val());
            var employee_id = $.trim($(thisitem).attr("data-emp-id"));
            
        	$.ajax({
        		type: "POST",
        		dataType : 'json',
        		url: my_leave_module_url + 'editApplyLeave',
        		data: {
        			"_token": "{{ csrf_token() }}",
        			'apply_leave_id':apply_leave_id,
        			'employee_id' : employee_id ,
        			'remove_image':remove_image,
        			'final_selected_image':final_selected_image
        			
        		},
        		beforeSend: function() {
        			//block ui
        			showLoader();
        		},
        		success: function(response) {
        			hideLoader();

        			if( response.status_code == 1 ){
            			var employee_name = ( ( response.data.employeeName != "" && response.data.employeeName != null ) ? response.data.employeeName : '' ); 
        				$("#apply-leave-model").find('.add-apply-leave-html').html("");
        				$("#apply-leave-model").find('.add-apply-leave-html').html(response.data.leaveFormHtml);
        				$("#apply-leave-model").find('.leave-balance-modal-html').html(response.data.leaveBalanceHtml);
        				$("#apply-leave-model").find('.employee-leave-list-html').html(response.data.leaveCalendarHtml);
        				$("#apply-leave-model").find('.leave-submit-user-name').html(employee_name);
        				$("[name='apply_leave_employee_id']").val(employee_id);
						var last_salary_generate_date = ( ( response.data.allowedLastEffDate != "" && response.data.allowedLastEffDate != null ) ? response.data.allowedLastEffDate : "" ); 
        				var allowed_leave_min_date = getLeaveTimeOffMinDate();
        				var leave_employee_joining_date = ( ( response.data.employeeJoiningDate != "" && response.data.employeeJoiningDate != null ) ? response.data.employeeJoiningDate : "" );
        					
        				
        				if( last_salary_generate_date != "" && last_salary_generate_date != null ){
        					$("[name='leave_from_date']").data("DateTimePicker").minDate(moment(last_salary_generate_date,'YYYY-MM-DD'));
    						$("[name='leave_to_date']").data("DateTimePicker").minDate(moment(last_salary_generate_date,'YYYY-MM-DD'));
                		} else {
                    		if( leave_employee_joining_date != "" && leave_employee_joining_date != null ){
                    			$("[name='leave_from_date']").data("DateTimePicker").minDate(moment(leave_employee_joining_date,'YYYY-MM-DD'));
        						$("[name='leave_to_date']").data("DateTimePicker").minDate(moment(leave_employee_joining_date,'YYYY-MM-DD'));
                        	} else {
                        		$("[name='leave_from_date']").data("DateTimePicker").minDate(moment(allowed_leave_min_date,'DD-MM-YYYY'));
        						$("[name='leave_to_date']").data("DateTimePicker").minDate(moment(allowed_leave_min_date,'DD-MM-YYYY'));
                            } 

                    		//$("[name='leave_from_date']").data("DateTimePicker").minDate(moment(allowed_leave_min_date,'DD-MM-YYYY'));
    						//$("[name='leave_to_date']").data("DateTimePicker").minDate(moment(allowed_leave_min_date,'DD-MM-YYYY'));
                			
                    	}
        				
						
        				
        				//console.log("current_date = " + current_date );
        				//console.log("last_allowed_date = " + last_allowed_date );
        				//console.log("allowed_leave_min_date = " + allowed_leave_min_date );
        				//console.log("welcome = " + '<?php echo date('m-Y' ,strtotime("-1 month")) ?>');
        				
            			openBootstrapModal('apply-leave-model');
            			$("[name='leave_from_date'],[name='leave_to_date']").datetimepicker().on('dp.change', function(e) {
            		    	daysValue();
            		    });
            		} else {

                	}
        			
        			
        		
        		},
        		error: function() {
        			hideLoader();
        		}
        	});
		}

        function showLeaveDuration(){
          
        	var leave_from_date = $.trim($('[name="leave_from_date"]').val());
    		var leave_to_date = $.trim($('[name="leave_to_date"]').val());

    		var dual_date_from_session = $.trim($('[name="dual_date_from_session"]:checked').val());
			var dual_date_to_session = $.trim($('[name="dual_date_to_session"]:checked').val());
			var single_date_session = $.trim($('[name="single_date_session"]:checked').val());

    		if( leave_to_date == "" || leave_to_date == null ){
    			$('[name="leave_to_date"]').val(leave_from_date);
        	}

    		if( leave_from_date != "" && leave_from_date != null && leave_to_date !=  "" && leave_to_date != null ){
    			leave_from_date = moment(leave_from_date, '{{ config("constants.DEFAULT_DATE_FORMAT") }}') .format("YYYY-MM-DD");
    			leave_to_date = moment(leave_to_date, '{{ config("constants.DEFAULT_DATE_FORMAT") }}') .format("YYYY-MM-DD");

				leave_from_date = moment(leave_from_date);
				leave_to_date = moment(leave_to_date);
				if( moment(leave_from_date).isSame(leave_to_date) != false ){
    				var no_of_days = '{{ config("constants.HALF_LEAVE_VALUE")  }}';

    				if( single_date_session == '{{ config("constants.FULL_DAY_LEAVE") }}' ){
    					no_of_days = '{{ config("constants.FULL_LEAVE_VALUE")  }}';
        			}
    				
        		} else {
					var no_of_days = leave_to_date.diff(leave_from_date, 'days');
					if( dual_date_from_session != "" && dual_date_from_session != null && dual_date_from_session == "{{ config('constants.FIRST_HALF_LEAVE') }}" ){
						no_of_days = ( parseFloat(no_of_days) + parseFloat('{{ config("constants.HALF_LEAVE_VALUE")  }}') );
					}
					if( dual_date_to_session != "" && dual_date_to_session != null && dual_date_to_session == "{{ config('constants.SECOND_HALF_LEAVE') }}" ){
						//console.log("second half leave  = ");
						no_of_days = ( parseFloat(no_of_days) + parseFloat('{{ config("constants.HALF_LEAVE_VALUE")  }}') );
					}
					
						
            	}
    			$(".leve-request-duration-div").show();
            	$(".leve-request-duration").html(no_of_days);
        	} else {
        		$(".leve-request-duration-div").hide();
            	$(".leve-request-duration").html("");
            }

        }
        
        function daysValue(){

        	var leave_from_date = $.trim($('[name="leave_from_date"]').val());
    		var leave_to_date = $.trim($('[name="leave_to_date"]').val());
    		var first_half_leave = '{{ config("constants.FIRST_HALF_LEAVE") }}';
    		var second_half_leave = '{{ config("constants.SECOND_HALF_LEAVE") }}';
    		var full_leave = '{{ config("constants.FULL_DAY_LEAVE") }}';
    		
			if( leave_from_date != "" && leave_from_date != null && leave_to_date !=  "" && leave_to_date != null ){
    			leave_from_date = moment(leave_from_date, '{{ config("constants.DEFAULT_DATE_FORMAT") }}') .format("YYYY-MM-DD");
    			leave_to_date = moment(leave_to_date, '{{ config("constants.DEFAULT_DATE_FORMAT") }}') .format("YYYY-MM-DD");

    			if( moment(leave_from_date).isSame(leave_to_date) != false ){
					$(".dual-date-selection").hide();
					$("[name='dual_date_from_session']").prop('checked', false);
					$("[name='dual_date_to_session']").prop('checked', false);
					$(".single-date-selection").show();

					if( $.trim($("[name='single_date_session']:checked").val()) == "" || $.trim($("[name='single_date_session']:checked").val()) == null ){
						$('input[name="single_date_session"][value="' + full_leave + '"]').prop("checked", true);
					}
					
					
        		} else {
        			$(".single-date-selection").hide();
        			$(".dual-date-selection").show();
					$("[name='single_date_session']").prop('checked', false);

					if( $.trim($("[name='dual_date_from_session']:checked").val()) == "" || $.trim($("[name='dual_date_from_session']:checked").val()) == null ){
						$('input[name="dual_date_from_session"][value="' + first_half_leave + '"]').prop("checked", true);
					}
					if( $.trim($("[name='dual_date_to_session']:checked").val()) == "" || $.trim($("[name='dual_date_to_session']:checked").val()) == null ){
						$('input[name="dual_date_to_session"][value="' + second_half_leave + '"]').prop("checked", true);
					}
					
				}
    			showLeaveDuration();
			}
		} 

		function addApplyLeaveModel(){
			if($('#add-apply-leave-model-form').valid() != true){
				return false;
			}
			var formData = new FormData($('#add-apply-leave-model-form')[0]);
			var confirm_box = "";
		    var confirm_box_msg = "";
		    formData.append('employee_id' , $.trim($("[name='apply_leave_employee_id']").val()))
		    
		    
		    confirm_box = "{{ trans('messages.apply-leave') }}";
		    confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.apply-leave')]) }}";
		   
		    alertify.confirm(confirm_box,confirm_box_msg,function() {  
		    	$.ajax({
					type: "POST",
					dataType: "json",
					url: my_leave_module_url + 'addApplyLeave',
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
							$("#apply-leave-model").modal('hide');
							alertifyMessage('success',response.message);
							window.location.reload();
							
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

		function leaveCalendar(selectedMonth = null ){
	        var employee_id = $.trim($("[name='apply_leave_employee_id']").val());
	    	$.ajax({
	    		type: "POST",
	    		dataType: "json",
	    		url: '{{config("constants.MY_LEAVES_MASTER_URL")}}' + '/getLeaveCalendar',
	    		data:{ 
	        		'selected_month' : selectedMonth, 
	        		'employee_id' : employee_id 
	        	},
	    		beforeSend: function() {
	    			//block ui
	    			showLoader();
	    		},
	    		success: function(response) {
	    			hideLoader();
	    			if( response.status_code == 1 ){
	    				$("#apply-leave-model").find('.employee-leave-list-html').html(response.data.leaveCalendarHtml);
	    			} else {
	    				alertifyMessage('error',response.message);
	    			}
	    			
	    		},
	    		error: function() {
	    			hideLoader();
	    		}
	    	});
	    }

		$(".fc-today-button").click(function() {
			alert("sss");
			var select_month_start_date = calendar.getDate();
			//console.log("welcome");
			if( select_month_start_date != "" && select_month_start_date != null  ){
				var month_start_date =  moment(select_month_start_date).format('YYYY-MM-DD');
				//console.log("month_start_date = " + month_start_date );
				getCalendarInfo(month_start_date);
				
			}
		});
		
		
		</script>
   