	<div class="modal fade document-folder document-type upload-profile-image" id="resign-job" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.resign-from-job") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body resign-job-html">
                    
                </div>
            </div>
        </div>
    </div>
    <script>

    function resignForm(thisitem){
		var record_id = $.trim($(thisitem).attr("data-record-id"));
		var record_type = $.trim($(thisitem).attr("data-type"));
		var joining_date = $.trim($(thisitem).attr("data-joining-date"));
		if( record_id != "" && record_id != null ){
			$.ajax({
    	 		type: "POST",
    	 		url: employee_module_url + 'getResignInfo',
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
    	 	 			$(".resign-job-html").html(response);
	    	 	 		openBootstrapModal('resign-job');
						if( joining_date != "" && joining_date != null ){
	    	            	$("[name='resign_preference_last_working_date']").data('DateTimePicker').minDate(moment().startOf('d'));
	    	            	$("[name='pf_exit_date']").data('DateTimePicker').minDate(moment().startOf('d'));
	    	            }
	    	          
	    	 	 	}
    	 	 	},
    	 		error: function() {
    	 			hideLoader();
    	 		}
    	 	});
		}
	}

    function showDiscussionWithManager(){
		var resign_discussion_with_manager = $.trim($("[name='resign_discussion_with_manager']:checked").val());

		if( resign_discussion_with_manager == "{{ config('constants.SELECTION_YES') }}" ){
			$(".resign-discussion-with-manager-summary-of-discussion").show();
		} else {
			$(".resign-discussion-with-manager-summary-of-discussion").hide();
		}
		
    }

    function resignPreferLastWorkingDay(){
		var resign_preference_last_working_day = $.trim($("[name='resign_preference_last_working_day']:checked").val());

		if( resign_preference_last_working_day == "{{ config('constants.SELECTION_YES') }}" ){
			$(".resign-preference-last-working-date").show();
		} else {
			$(".resign-preference-last-working-date").hide();
		}
		
    }
	
    
    function addResignForm(thisitem){
		//console.log("addResignForm") ;	
		if( $("#add-resign-form").valid()  != true  ){
			return false;
		}

		var confirm_msg = "{{ trans('messages.resign') }}";
    	var confirm_text = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.resign')]) }}";


    	var record_status = $.trim($(thisitem).attr('data-record-status'));

		if( record_status != "" && record_status != null && record_status == "{{ config('constants.APPROVED_STATUS') }}" ){
    		confirm_msg = "{{ trans('messages.update-resign-request') }}";
        	confirm_text = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-resign-request')]) }}";
        }

		var formData = new FormData( $('#add-resign-form')[0] );
    	
    	alertify.confirm(confirm_msg, confirm_text ,function() { 
	    	 $.ajax({
	     		type: "POST",
	     		dataType :'json',
	     		url: employee_module_url + 'addResignForm',
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
						$("#initiate-exit-modal").modal('hide');
						$("#resign-job").modal('hide');
						$('.employee-primary-details').html(response.data.primaryDetailsInfo);
						$('.employee-profile-pic-view--master-div-html').html(response.data.mainProfileInfo);
						$(".employee-resign-info").show();
						$('.employee-resign-info-section').html(response.data.initateExitHtml);
						
						
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