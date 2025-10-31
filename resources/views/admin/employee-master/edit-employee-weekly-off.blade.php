<div class="modal fade document-folder" id="edit-week-off-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.edit-weekly-off") }} <span class="twt-custom-modal-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'edit-week-off-form' , 'method' => 'post')) !!}
            	<div class="modal-body edit-week-off-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" onclick="updateEmployeeWeeklyOff(this);" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<script>
$("#edit-week-off-form").validate({
    errorClass: "invalid-input",
    rules: {
        employee_weekly_off: {
            required: true
        },
        weekly_off_effective_date: {
            required: true
        },
    },
    messages: {
    	employee_weekly_off: {
            required: "{{ trans('messages.require-select-weekly-off') }}"
        },
        weekly_off_effective_date: {
            required: "{{ trans('messages.require-effective-date') }}"
        },
    },
});

function editWeekOff(thisitem){
	current_selected_row = thisitem;
	var record_id = $.trim($(thisitem).attr("data-record-id"));
	var last_shift_date = $.trim($(thisitem).attr("data-last-weekly-off-date"));
	$.ajax({
		type: "POST",
		url: employee_module_url + 'getEmployeeWeekOffInfo',
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
				$(".edit-week-off-html").html(response);
				$('#edit-week-off-modal').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
				openBootstrapModal("edit-week-off-modal");
				$(function(){
					$("[name='weekly_off_effective_date']").datetimepicker({
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
					    var week_off_effective_date_fillable = $.trim($("[name='weekly_off_effective_date']").val());
					    if( week_off_effective_date_fillable == "" || week_off_effective_date_fillable == null){
					    	$("[name='weekly_off_effective_date']").data('DateTimePicker').defaultDate(moment(last_shift_date,'YYYY-MM-DD').format('DD-MM-YYYY'));
							$("[name='weekly_off_effective_date']").val("");
					    }
				    });
					last_shift_date = moment(last_shift_date, "YYYY-MM-DD").add(1, 'days');	
					$("[name='weekly_off_effective_date']").data("DateTimePicker").minDate(moment(last_shift_date,'YYYY-MM-DD'));

					var emp_last_salary_generate_date = $.trim($("[name='emp_last_salary_generate_date']").val());
					if( emp_last_salary_generate_date != "" && emp_last_salary_generate_date != null ){
						if( moment(moment(last_shift_date,'YYYY-MM-DD')).isBefore(emp_last_salary_generate_date) ){
							$("[name='weekly_off_effective_date']").data("DateTimePicker").minDate(moment(emp_last_salary_generate_date,'YYYY-MM-DD').startOf('d'));
						}
					}
					
					var current_date = moment().format('YYYY-MM-DD');
				    if( moment(moment(last_shift_date,'YYYY-MM-DD')).isAfter(current_date) ){
					
					} else {
						 $("[name='weekly_off_effective_date']").data("DateTimePicker").maxDate(moment().endOf('d'));
					}
				})
				
			}
	 	},
		error: function() {
			hideLoader();
		}
	});
 }



 function updateEmployeeWeeklyOff(){
	if( $("#edit-week-off-form").valid() != true ){
		return false;
	}

	var update_week_off_employee_id = $.trim($("[name='update_week_off_employee_id']").val());
	var update_week_off_employee_history_id = $.trim($("[name='update_week_off_employee_history_id']").val());
	
	var employee_weekly_off = $.trim($("[name='employee_weekly_off']").val());
	var weekly_off_effective_date = $.trim($("[name='weekly_off_effective_date']").val());

	var old_weekly_off_value = $.trim($("[name='employee_weekly_off']").attr('data-old-value'));
	var new_weekly_off_value = $.trim($("[name='employee_weekly_off'] option:selected").attr('data-id'));

	//console.log("old_weekly_off_value = " + old_weekly_off_value );
	//console.log("new_weekly_off_value = " + new_weekly_off_value );

	if( update_week_off_employee_history_id == "" || update_week_off_employee_history_id == null ){
		if( old_weekly_off_value != "" && old_weekly_off_value != null &&  new_weekly_off_value != "" && new_weekly_off_value != null && ( new_weekly_off_value ==  old_weekly_off_value ) ){
			alertifyMessage('error' , '{{ trans("messages.same-as-old-value-select" , [ "module" => enumText( config("constants.WEEK_OFF_RECORD_TYPE") )  ]    ) }}');
			return false
		}
	}
	

	alertify.confirm('{{ trans("messages.update-weekly-off") }}', '{{ trans("messages.common-confirm-msg" , [ "module" => trans("messages.update-weekly-off")  ]) }}' ,function() {
		$.ajax({
			type: "POST",
			url: employee_module_url + 'updateEmployeeDataInfo',
			dataType : 'json',
			data: {
				"_token": "{{ csrf_token() }}",
				'employee_id':update_week_off_employee_id,
				'update_data_value':employee_weekly_off,
				'history_record_id' : update_week_off_employee_history_id, 
				'update_request' : '{{ config("constants.WEEK_OFF_RECORD_TYPE") }}',
				'effective_date':weekly_off_effective_date,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
		 		hideLoader();
		 		if(response.status_code == 1 ){
					alertifyMessage('success' , response.message);
					$("#edit-week-off-modal").modal('hide');
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

 function editWeekOffHistory(thisitem){
	var history_start_date = $.trim($(thisitem).attr("data-start-date"));
	var history_end_date = $.trim($(thisitem).attr("data-end-date"));

	var record_id = $.trim($(thisitem).attr("data-record-id"));
	$.ajax({
		type: "POST",
		url: employee_module_url + 'getWeekOffHistoryInfo',
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
				$(".edit-week-off-html").html(response);
				$('#edit-week-off-modal').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
				openBootstrapModal("edit-week-off-modal");
				$(function(){
					$("[name='weekly_off_effective_date']").datetimepicker({
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
				    
				    $("[name='weekly_off_effective_date']").data("DateTimePicker").minDate(moment(history_start_date,'YYYY-MM-DD'));
				    var current_date = moment().format('YYYY-MM-DD');
				    if( moment(moment(history_start_date,'YYYY-MM-DD')).isAfter(current_date) ){
					
					} else {
						 $("[name='weekly_off_effective_date']").data("DateTimePicker").maxDate(moment().endOf('d'));
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