<div class="modal fade document-folder edit-designation-modal" id="edit-designation-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.edit-designation") }} <span class="twt-custom-modal-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
             {!! Form::open(array( 'id '=> 'edit-designation-form' , 'method' => 'post')) !!}
            	<div class="modal-body edit-designation-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" onclick="updateEmployeeDesignation(this);" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>


<script>
$("#edit-designation-form").validate({
    errorClass: "invalid-input",
    rules: {
  	  employee_designation: {
            required: true
        },
        designation_effective_date: {
            required: true
        },
    },
    messages: {
  	  employee_designation: {
            required: "{{ trans('messages.require-select-designation') }}"
        },
        designation_effective_date: {
            required: "{{ trans('messages.require-effective-date') }}"
        },
    },
});

function editJobDesignation(thisitem){
	current_selected_row = thisitem;
	var record_id = $.trim($(thisitem).attr("data-record-id"));
	var last_designation_date = $.trim($(thisitem).attr("data-last-designation-date"));
	$.ajax({
		type: "POST",
		url: employee_module_url + 'getEmployeeDesignationInfo',
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
				$(".edit-designation-html").html(response);
				$('#edit-designation-modal').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
				openBootstrapModal("edit-designation-modal");
				$(function(){
					$("[name='designation_effective_date']").datetimepicker({
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
				    }).on('dp.show' , function(e){
					    var designation_effective_date_fillable = $.trim($("[name='designation_effective_date']").val());
					    if( designation_effective_date_fillable == "" || designation_effective_date_fillable == null){
					    	$("[name='designation_effective_date']").data('DateTimePicker').defaultDate(moment(last_designation_date,'YYYY-MM-DD').format('DD-MM-YYYY'));
							$("[name='designation_effective_date']").val("");
					    }
				    });
					last_designation_date = moment(last_designation_date, "YYYY-MM-DD").add(1, 'days');	
				    $("[name='designation_effective_date']").data("DateTimePicker").minDate(moment(last_designation_date,'YYYY-MM-DD'));

				    
				    var current_date = moment().format('YYYY-MM-DD');
				    if( moment(moment(last_designation_date,'YYYY-MM-DD')).isAfter(current_date) ){
					
					} else {
						 $("[name='designation_effective_date']").data("DateTimePicker").maxDate(moment().endOf('d'));
					}
				    
				   
				})
				
			}
	 	},
		error: function() {
			hideLoader();
		}
	});
 }



 function updateEmployeeDesignation(){
	if( $("#edit-designation-form").valid() != true ){
		return false;
	}

	var update_designation_employee_id = $.trim($("[name='update_designation_employee_id']").val());
	var update_designation_employee_history_id = $.trim($("[name='update_designation_employee_history_id']").val());
	
	var employee_designation = $.trim($("[name='employee_designation']").val());
	var designation_effective_date = $.trim($("[name='designation_effective_date']").val());

	var old_designation_value = $.trim($("[name='employee_designation']").attr('data-old-value'));
	var new_designation_value = $.trim($("[name='employee_designation'] option:selected").attr('data-id'));

	//console.log("old_designation_value = " + old_designation_value );
	//console.log("new_designation_value = " + new_designation_value );

	if( update_designation_employee_history_id == "" || update_designation_employee_history_id == null ){
		if( old_designation_value != "" && old_designation_value != null &&  new_designation_value != "" && new_designation_value != null && ( new_designation_value ==  old_designation_value ) ){
			alertifyMessage('error' , '{{ trans("messages.same-as-old-value-select" , [ "module" => enumText(config("constants.DESIGNATION_LOOKUP") ) ]    ) }}');
			return false
		}
	}
	
	alertify.confirm('{{ trans("messages.update-designation") }}', '{{ trans("messages.common-confirm-msg" , [ "module" => trans("messages.update-designation")  ]) }}' ,function() {
		
		$.ajax({
			type: "POST",
			url: employee_module_url + 'updateEmployeeDataInfo',
			dataType : 'json',
			data: {
				"_token": "{{ csrf_token() }}",
				'employee_id':update_designation_employee_id,
				'update_data_value':employee_designation,
				'history_record_id' : update_designation_employee_history_id, 
				'update_request' : '{{ config("constants.DESIGNATION_LOOKUP") }}',
				'effective_date':designation_effective_date,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
		 		hideLoader();
		 		if(response.status_code == 1 ){
					alertifyMessage('success' , response.message);
					$("#edit-designation-modal").modal('hide');
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

 

 function editJobDesignationHistory(thisitem){
		var history_start_date = $.trim($(thisitem).attr("data-start-date"));
		var history_end_date = $.trim($(thisitem).attr("data-end-date"));

		var record_id = $.trim($(thisitem).attr("data-record-id"));
		$.ajax({
			type: "POST",
			url: employee_module_url + 'getDesignationHistoryInfo',
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
					$(".edit-designation-html").html(response);
					$('#edit-designation-modal').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
					openBootstrapModal("edit-designation-modal");
					$(function(){
						$("[name='designation_effective_date']").datetimepicker({
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
					    $("[name='designation_effective_date']").data("DateTimePicker").minDate(moment(history_start_date,'YYYY-MM-DD'));

					    var current_date = moment().format('YYYY-MM-DD');
					    if( moment(moment(history_start_date,'YYYY-MM-DD')).isAfter(current_date) ){
						
						} else {
							 $("[name='designation_effective_date']").data("DateTimePicker").maxDate(moment().endOf('d'));
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