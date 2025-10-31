					
<div class="modal fade document-folder" id="edit-probation-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.edit-probation-period") }} <span class="twt-custom-modal-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            	{!! Form::open(array( 'id '=> 'edit-probation-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body edit-probation-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" onclick="updateProbation(this);" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            	{!! Form::close() !!}
        </div>
    </div>
</div>

<div class="modal fade document-folder" id="confirm-joining-date-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.employee-joining-date") }} <span class="twt-custom-modal-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            	{!! Form::open(array( 'id '=> 'confirm-employee-joining-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body confirm-joining-date-html">
                    <div class="row">
                    	<div class="col-md-7">
	                    	<div class="form-group">
	                            <label for="view_employee_joining_date" class="control-label">{{ trans("messages.joining-date") }}<span class="star">*</span></label>
	                            <input type="text" name="confirm_employee_joining_date" class="form-control" value="" placeholder="{{ config('constants.DEFAULT_DATE_FORMAT') }}">
	                       </div>
                       </div>
                    </div>	
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" onclick="confirmJoiningDate(this);" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            	{!! Form::close() !!}
        </div>
    </div>
</div>

<div class="modal fade document-folder" id="show-probation-history-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.in-probation-history") }} <span class="twt-custom-modal-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body overflow-hidden">
                <div class="row px-3 py-4">
                    <div class="col-12 table-responsive">
                        <table class="table table-sm table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center sr-col">{{ trans('messages.sr-no') }}</th>
                                    <th style="min-width: 120px;">{{ trans('messages.probation-from-date') }}</th>
                                    <th style="min-width: 120px;">{{ trans('messages.probation-to-date') }}</th>
                                    <th style="min-width: 180px;">{{ trans('messages.remarks') }}</th>
                                </tr>
                            </thead>
                            <tbody class="show-probation-history-html">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

$("#edit-probation-form").validate({
    errorClass: "invalid-input",
    rules: {
        probation_end_date: {
            required: true
        },
		probation_policy_id: {
			required: true
		}
    },
    messages: {
        probation_end_date: {
            required: "{{ trans('messages.require-select-probation-end-date') }}"
        },
		probation_policy_id: {
			required: "{{ trans('messages.require-select-probation-period') }}"
		}

    },
});

$("#confirm-employee-joining-form").validate({
    errorClass: "invalid-input",
    rules: {
    	confirm_employee_joining_date: {
            required: true
        }
	},
    messages: {
    	confirm_employee_joining_date: {
            required: "{{ trans('messages.require-enter-joining-date') }}"
        },
	},
});



function editProbation(thisitem){
	var record_id = $.trim($(thisitem).attr('data-record-id'));
	var joining_date = $.trim($(thisitem).attr('data-joining-date'));
	
	if( record_id  != "" && record_id != null  ){
		$.ajax({
			type: "POST",
			url: employee_module_url + 'getEmployeeProbationInfo',
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
					$(".edit-probation-html").html(response);
					openBootstrapModal("edit-probation-modal");
					$('#edit-probation-modal').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
					$(function(){
						$("[name='probation_end_date']").datetimepicker({
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
					    var modal_probation_status = $.trim($("[name='probation_status']:checked").val());
					    
						var allowed_min_extend_date =  $.trim($("[name='allowed_min_extend_date']").val());
					    $("[name='probation_end_date']").data("DateTimePicker").minDate(moment(joining_date,'YYYY-MM-DD'));
					   // console.log(allowed_min_extend_date);
					    if( allowed_min_extend_date != "" && allowed_min_extend_date != null ){
							$("[name='probation_end_date']").data("DateTimePicker").minDate(moment(allowed_min_extend_date,'YYYY-MM-DD'));
						}
						if( modal_probation_status != "" && modal_probation_status != null ){
							$("[name='probation_end_date']").data("DateTimePicker").maxDate(moment().endOf('d'));
						}
					    
					   //$("[name='probation_end_date']").data("DateTimePicker").maxDate(moment().endOf('d'));
					})
					
				}
		 	},
			error: function() {
				hideLoader();
			}
		});
	}
}

function showProbationPolicy(thisitem){
	var record_id = $.trim($(thisitem).attr('data-record-id'));
	if( record_id  != "" && record_id != null  ){
		$.ajax({
			type: "POST",
			url: employee_module_url + 'show-probation-history',
			data: {
				"_token": "{{ csrf_token() }}",
				'employee_id':record_id
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
		 		hideLoader();
				if( response != "" && response != null ){
					$(".show-probation-history-html").html(response);
					openBootstrapModal("show-probation-history-modal");
					$('#show-probation-history-modal').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
				}
		 	},
			error: function() {
				hideLoader();
			}
		});
	}
}

function setProbationMinMaxDate(){

	var allowed_min_extend_date =  $.trim($("[name='allowed_min_extend_date']").val());
	var current_probation_end_date =  $.trim($("[name='current_probation_end_date']").val());
	var joining_date = $.trim($("[name='emp_joining_date']").val());
	var selected_type = $("[name='probation_status']:checked").val();
	var current_date = moment().format('YYYY-MM-DD');
	var current_end_date = $.trim($("[name='current_probation_end_date']").val());
	current_end_date = moment(current_end_date,'YYYY-MM-DD').format('YYYY-MM-DD');
	console.log("allowed_min_extend_date = " + allowed_min_extend_date );
	console.log("current_date = " + current_date );
	console.log("current_end_date = " + current_end_date );
	switch( selected_type ){
		case '{{ config("constants.EXTEND_PROBATION") }}':
			$("[name='probation_end_date']").data("DateTimePicker").maxDate(false);
			if( current_end_date != "" && current_end_date != null ){
				//console.log(moment(current_end_date).isBefore(current_date));
				if( moment(current_end_date).isBefore(current_date) ){
					$("[name='probation_end_date']").data("DateTimePicker").minDate(moment(current_end_date,'YYYY-MM-DD'));
				} else {
					$("[name='probation_end_date']").data("DateTimePicker").minDate(moment(current_end_date,'YYYY-MM-DD'));
				}
			}
			if( allowed_min_extend_date != "" && allowed_min_extend_date != null ){
				$("[name='probation_end_date']").data("DateTimePicker").minDate(moment(allowed_min_extend_date,'YYYY-MM-DD'));
			}

			if( current_probation_end_date != "" && current_probation_end_date != null ){
				$("[name='probation_end_date']").data("DateTimePicker").minDate(moment(current_probation_end_date,'YYYY-MM-DD'));
			}
			
			
			//console.log("selected_type = "  + selected_type );
			break;
		case '{{ config("constants.END_PROBATION") }}':
			$("[name='probation_end_date']").data("DateTimePicker").minDate(moment(joining_date,'YYYY-MM-DD'));
			if( allowed_min_extend_date != "" && allowed_min_extend_date != null ){
				$("[name='probation_end_date']").data("DateTimePicker").minDate(moment(allowed_min_extend_date,'YYYY-MM-DD'));
			}
			$("[name='probation_end_date']").data("DateTimePicker").maxDate(moment().endOf('d'));
			//console.log("selected_type = "  + selected_type );
			break;
	}
	$("[name='probation_end_date']").val("");
	
}

function confirmJoiningDate(){

	if( $("#confirm-employee-joining-form").valid() != true ){
		return false;
	}
	
	var confirm_employee_joining_date = $.trim($("[name='confirm_employee_joining_date']").val());
	var update_probation_employee_id  = $.trim($("[name='update_probation_employee_id']").val());
	alertify.confirm('{{ trans("messages.probation-period") }}', '{{ trans("messages.common-confirm-msg" , [ "module" => trans("messages.update-probation-period")  ]) }}' ,function() {
		submitProbationEndForm();
	},function() {});
}

function updateProbation(){

	if( $("#edit-probation-form").valid()  != true  ){
		return false;
	}
	var probation_end_date = $.trim($("[name='probation_end_date']").val());
	var current_probation_end_date = $.trim($("[name='current_probation_end_date']").val());
	var probation_end_db_date =  moment(probation_end_date,'DD-MM-YYYY').format('YYYY-MM-DD');
	var probation_status = $.trim($("[name='probation_status']:checked").val());

	console.log("probation_end_date = " + probation_end_date );
	console.log("probation_end_db_date = " + probation_end_db_date );
	console.log("current_probation_end_date = " + current_probation_end_date );

	var submitProbnationForm = true;
	if( probation_status != "" && probation_status != null && probation_status == "{{ config('constants.END_PROBATION') }}"){
		if( moment(probation_end_db_date).isBefore(current_probation_end_date) ){
			console.log("early closed");
			submitProbnationForm = false;
			alertify.confirm('{{ trans("messages.probation-period") }}', '{{ trans("messages.confirm-early-probation-alert" ) }}' ,function() {
				openBootstrapModal("confirm-joining-date-modal");
				
				$('#confirm-joining-date-modal').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
				$("[name='confirm_employee_joining_date']").datetimepicker({
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
			},function() {});
		}
	}
	
	if( submitProbnationForm != false ){
		alertify.confirm('{{ trans("messages.probation-period") }}', '{{ trans("messages.common-confirm-msg" , [ "module" => trans("messages.update-probation-period")  ]) }}' ,function() {
			submitProbationEndForm();
		},function() {});
	}
}

function submitProbationEndForm(){
	var probation_remark = $.trim($("[name='probation_remark']").val());
	var employee_id = $.trim($("[name='update_probation_employee_id']").val());
	var probation_end_date = $.trim($("[name='probation_end_date']").val());
	var probation_status = $.trim($("[name='probation_status']:checked").val());
	var confirm_employee_joining_date  = $.trim($("[name='confirm_employee_joining_date']").val());
		
	$.ajax({
		type: "POST",
		url: employee_module_url + 'updateProbation',
		dataType : 'json',
		data: {
			"_token": "{{ csrf_token() }}",
			'probation_status':probation_status,
			'probation_end_date':probation_end_date,
			'probation_remark':probation_remark,
			'employee_id':employee_id,
			'confirm_employee_joining_date' : confirm_employee_joining_date ,
			'probation_policy_id' : $.trim($("[name='probation_policy_id']").val()),
		},
		beforeSend: function() {
			//block ui
			showLoader();
		},
		success: function(response) {
	 		hideLoader();
	 		if(response.status_code == 1 ){
				alertifyMessage('success' , response.message);
				$("#edit-probation-modal").modal('hide');
				$("#confirm-joining-date-modal").modal('hide');
				$(".employee-job-record").html(response.data.html)
			} else {
		    	alertifyMessage('error' , response.message);
			}
	 	},
		error: function() {
			hideLoader();
		}
	});	
}

</script>