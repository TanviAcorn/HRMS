<div class="modal fade document-folder" id="edit-team-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.edit-team") }} <span class="twt-custom-modal-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'edit-team-form' , 'method' => 'post')) !!}
            	<div class="modal-body edit-team-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" onclick="updateEmployeeTeam(this);"  class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<script>
$("#edit-team-form").validate({
    errorClass: "invalid-input",
    rules: {
    	employee_team: {
            required: true
        },
        team_effective_date: {
            required: true
        },
    },
    messages: {
    	employee_team: {
            required: "{{ trans('messages.require-select-team') }}"
        },
        team_effective_date: {
            required: "{{ trans('messages.require-effective-date') }}"
        },
    },
});

function editTeam(thisitem){
	current_selected_row = thisitem;
	var record_id = $.trim($(thisitem).attr("data-record-id"));
	var last_team_date = $.trim($(thisitem).attr("data-last-team-date"));
	$.ajax({
		type: "POST",
		url: employee_module_url + 'getEmployeeTeamInfo',
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
				$(".edit-team-html").html(response);
				$('#edit-team-modal').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
				openBootstrapModal("edit-team-modal");
				$(function(){
					$("[name='team_effective_date']").datetimepicker({
				        useCurrent: false,
				        viewMode: 'days',
				        ignoreReadonly: true,
				        format: "{{ config('constants.DEFAULT_DATE_FORMAT') }}",
				        //format: "DD-MM-YYYY",
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
					    var team_effective_date_fillable = $.trim($("[name='team_effective_date']").val());
					    if( team_effective_date_fillable == "" || team_effective_date_fillable == null){
					    	$("[name='team_effective_date']").data('DateTimePicker').defaultDate(moment(last_team_date,'YYYY-MM-DD').format('DD-MM-YYYY'));
							$("[name='team_effective_date']").val("");
					    }
				    });
					last_team_date = moment(last_team_date, "YYYY-MM-DD").add(1, 'days');	
					$("[name='team_effective_date']").data("DateTimePicker").minDate(moment(last_team_date,'YYYY-MM-DD'));

				    var current_date = moment().format('YYYY-MM-DD');
				    if( moment(moment(last_team_date,'YYYY-MM-DD')).isAfter(current_date) ){
					
					} else {
						$("[name='team_effective_date']").data("DateTimePicker").maxDate(moment().endOf('d'));
					}
					
				    
				   
				    
				})
				
			}
	 	},
		error: function() {
			hideLoader();
		}
	});
 }



 function updateEmployeeTeam(){
	if( $("#edit-team-form").valid() != true ){
		return false;
	}

	var update_team_employee_id = $.trim($("[name='update_team_employee_id']").val());
	var employee_team = $.trim($("[name='employee_team']").val());
	var team_effective_date = $.trim($("[name='team_effective_date']").val());
	var update_team_employee_history_id = $.trim($("[name='update_team_employee_history_id']").val());

	var old_team_value = $.trim($("[name='employee_team']").attr('data-old-value'));
	var new_team_value = $.trim($("[name='employee_team'] option:selected").attr('data-id'));

	//console.log("old_team_value = " + old_team_value );
	//console.log("new_team_value = " + new_team_value );

	if( update_team_employee_history_id == "" || update_team_employee_history_id == null ){
		if( old_team_value != "" && old_team_value != null &&  new_team_value != "" && new_team_value != null && ( old_team_value ==  new_team_value ) ){
			alertifyMessage('error' , '{{ trans("messages.same-as-old-value-select" , [ "module" => enumText( config("constants.TEAM_LOOKUP") )  ]    ) }}');
			return false
		}
	}
	

	alertify.confirm('{{ trans("messages.update-team") }}', '{{ trans("messages.common-confirm-msg" , [ "module" => trans("messages.update-team")  ]) }}' ,function() {
		$.ajax({
			type: "POST",
			url: employee_module_url + 'updateEmployeeDataInfo',
			dataType : 'json',
			data: {
				"_token": "{{ csrf_token() }}",
				'employee_id':update_team_employee_id,
				'update_data_value':employee_team,
				'history_record_id' : update_team_employee_history_id, 
				'update_request' : '{{ config("constants.TEAM_LOOKUP") }}',
				'effective_date':team_effective_date,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
		 		hideLoader();
		 		if(response.status_code == 1 ){
					alertifyMessage('success' , response.message);
					$("#edit-team-modal").modal('hide');
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

 function editTeamHistory(thisitem){
		var history_start_date = $.trim($(thisitem).attr("data-start-date"));
		var history_end_date = $.trim($(thisitem).attr("data-end-date"));

		var record_id = $.trim($(thisitem).attr("data-record-id"));
		$.ajax({
			type: "POST",
			url: employee_module_url + 'getTeamHistoryInfo',
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
					$(".edit-team-html").html(response);
					$('#edit-team-modal').find('.twt-custom-modal-title').html(common_emp_modal_header_title);
					openBootstrapModal("edit-team-modal");
					$(function(){
						$("[name='team_effective_date']").datetimepicker({
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
					    $("[name='team_effective_date']").data("DateTimePicker").minDate(moment(history_start_date,'YYYY-MM-DD'));

					    var current_date = moment().format('YYYY-MM-DD');
					    if( moment(moment(history_start_date,'YYYY-MM-DD')).isAfter(current_date) ){
						
						} else {
							$("[name='team_effective_date']").data("DateTimePicker").maxDate(moment().endOf('d'));
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