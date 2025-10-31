	<div class="modal fade document-folder document-type upload-profile-image" id="initiate-exit-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.initiate-exit") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body initiate-exit-html">
                    
                </div>
            </div>
        </div>
    </div>
    <script>
	
	
	function showInitiateExitField(){
		var selected_value  = $.trim($("[name='initiating_exit_reason']:checked").val());
		if( selected_value == '{{ config("constants.EMPLOYEE_INITIATE_EXIT_TYPE") }}'){
			$(".employee-selection-div").show();
			$(".employer-selection-div").hide();
		}else if( selected_value == '{{ config("constants.EMPLOYER_INITIATE_EXIT_TYPE") }}' ) {
			$(".employee-selection-div").hide();
			$(".employer-selection-div").show();
		}
	}

	function showExitSummryDiv(){
		var selected_value = $.trim($("[name='initial_exit_discussion_with_employee']:checked").val());
		if( selected_value == '{{ config("constants.SELECTION_YES") }}'){
			$(".exit-summary-div").show();
		}else {
			$(".exit-summary-div").hide();
		}
	}

	function showOtherDateDiv(){
		var selected_value = $.trim($("[name='initial_exit_recommend_last_working_day_type']:checked").val());
		if( selected_value == '{{ config("constants.OTHER") }}'){
			$(".other-last-working-date").show();
		}else {
			$(".other-last-working-date").hide();
		}
	}

	function initiateExitForm(thisitem){
		var record_id = $.trim($(thisitem).attr("data-record-id"));
		var record_type = $.trim($(thisitem).attr("data-type"));
		
		if( record_id != "" && record_id != null ){
			$.ajax({
    	 		type: "POST",
    	 		url: employee_module_url + 'getInitiateExitInfo',
    	 		data: {
    	 			"_token": "{{ csrf_token() }}",
    	 			'record_type':record_type,
    	 			'record_id':record_id,
    	 		},
    	 		beforeSend: function() {
    	 			//block ui
    	 			showLoader();
    	 		},
    	 		success: function(response) {
    	 	 		hideLoader();
    	 	 		if( response != "" && response != null ){
    	 	 			$(".initiate-exit-html").html(response);
	    	 	 		openBootstrapModal('initiate-exit-modal');

	    	 	 		var initiating_exit_reason = $.trim($("[name=initiating_exit_reason]:checked").val())

	    	 	 		if( initiating_exit_reason == "" || initiating_exit_reason == null ){
		    	 	 		var default_initiating_exit_reason = "{{ config('constants.EMPLOYER_INITIATE_EXIT_TYPE') }}";
		    	 	 		$('input[name="initiating_exit_reason"][value="' + default_initiating_exit_reason + '"]').prop("checked", true).trigger('click');
		    	 	 		calculateTerminateNoticePeriodEndDate();
		    	 	 	}

	    	 	 		
	    	 	 		
	    	 	 		
						var employee_joining_date = $.trim($("[name='employee_joining_date']").val());
						
	    	            if( employee_joining_date != "" && employee_joining_date != null ){
		    	            $("[name='initial_exit_termination_date']").data("DateTimePicker").minDate(moment(employee_joining_date,'YYYY-MM-DD'));
	    	            	$("[name='initial_exit_other_last_working_date']").data("DateTimePicker").minDate(moment(employee_joining_date,'YYYY-MM-DD'));
							$("[name='initial_exit_employee_provide_notice_exit_date']").data("DateTimePicker").minDate(moment(employee_joining_date,'YYYY-MM-DD'));
							$("[name='pf_exit_date']").data("DateTimePicker").minDate(moment(employee_joining_date,'YYYY-MM-DD'));
		    	        }

	    	            var initial_exit_termination_date = $.trim($("[name='initial_exit_termination_date']").val());
				 	 	var initial_exit_other_last_working_date = $.trim($("[name='initial_exit_other_last_working_date']").val());

				 	 	if( initial_exit_other_last_working_date != "" && initial_exit_other_last_working_date != null ){
					 	 	if( moment(initial_exit_other_last_working_date,'DD-MM-YYYY').isBefore(moment(initial_exit_termination_date,'DD-MM-YYYY')) == true ){
					 	 		$("[name='initial_exit_other_last_working_date']").val(initial_exit_termination_date);
						 	}	
					 	} 

				 	 	if( initial_exit_termination_date != "" && initial_exit_termination_date != null ){
							$("[name='initial_exit_other_last_working_date']").data("DateTimePicker").minDate(moment(initial_exit_termination_date,'DD-MM-YYYY').format('DD-MM-YYYY'));
					 	} 
					 	
		    	        
	    	            $('[data-toggle="tooltip"]').tooltip();
	    	          
	    	 	 	}
    	 	 	},
    	 		error: function() {
    	 			hideLoader();
    	 		}
    	 	});
		}
	}

    function addInitiateExit(thisitem){
		//console.log("addInitiateExit") ;	
		if( $("#add-initiate-exit-form").valid()  != true  ){
			return false;
		}

	
    	var formData = new FormData( $('#add-initiate-exit-form')[0] );

    	var confirm_msg = "{{ trans('messages.initiate-exit') }}";
    	var confirm_text = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.initiate-exit')]) }}";


    	var record_status = $.trim($(thisitem).attr('data-record-status'));
		console.log("record_status = " + record_status );
    	if( record_status != "" && record_status != null && record_status == "{{ config('constants.APPROVED_STATUS') }}" ){
    		confirm_msg = "{{ trans('messages.update-initiate-exit-request') }}";
        	confirm_text = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-initiate-exit-request')]) }}";
        }
    	
    	
    	alertify.confirm(confirm_msg,confirm_text,function() { 
	    	 $.ajax({
	     		type: "POST",
	     		dataType :'json',
	     		url: employee_module_url + 'addInitiateExitForm',
	     		data:formData,
	     		cache: false,
			    contentType: false,
			    processData: false,
	     		beforeSend: function() {
	     			//block ui
	     			showLoader();
	     		},
	     		success: function(response) {
	     			hideLoader();
	     			if( response.status_code == 1 ){
						$("#initiate-exit-modal").modal('hide');
						$(".employee-resign-info").show();
						$(".employee-resign-info-section").show();
						
						$(".employee-resign-info-section").html(response.data.html)
						alertifyMessage('success',response.message);
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
	
		
    </script>
    
    