<div class="job-edit h-100">
    <div class="card card-display border-0 px-2 h-100">
        <div class="card-body px-2 py-0">
            <div class="row px-0 border-bottom">
                <div class="col-12 profile-details-title-card">
                    <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.job") }}</h5>
                    	@if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )  
                    	<a href="javascript:void(0);" data-emplyee-id="{{ (!empty($employeeRecordInfo->i_id) ? Wild_tiger::encode($employeeRecordInfo->i_id) : 0 ) }}" onclick="openJobDetailsModel(this)";  title="{{ trans('messages.edit') }}">
                    		{{ trans("messages.edit") }}
                    	</a>
                    	@endif
                </div>
            </div>
            <div class="row">
                <div class="col-12 py-0 profile-display-card">
                    <div class="row pb-0 pt-3 employee-job-record">
                        <?php 
						$recordInfo['employeeRecordInfo'] = $employeeRecordInfo;
						$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/job-list')->with ( $recordInfo )->render();
						echo $html;
						?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade document-folder" id="job-details-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.job") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'add-job-details-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body add-job-details-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                	<input type="hidden" name="employee_record_id" value="">
                	<input type="hidden" name="record_id" value="">
                    <button type="button" onclick="addJobDetailsModel()"; class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- in-probation-edit  -->

<div class="modal fade document-folder" id="in_probation_edit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.edit-probation-period") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <form method="post" id="in_probation">
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="submit" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- designation edit and history-->


<!-- designation history-->


<!-- designation and history edit -->


<!-- team edit and history-->
<!-- team edit form-->



<!-- team edit history-->

<div class="modal fade document-folder" id="team_history" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.team-history") }}</h5>
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
                                    <th style="min-width: 180px;">{{ trans('messages.team') }}</th>
                                    <th style="min-width: 120px;">{{ trans('messages.from-date') }}</th>
                                    <th style="min-width: 120px;">{{ trans('messages.to-date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>Web Designer</td>
                                    <td>1<sup>st</sup> Jan, 2023</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Web Designer</td>
                                    <td>1<sup>st</sup> Nov, 2021</td>
                                    <td>1<sup>st</sup> Jan, 2023</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- team edit and history end-->


<!-- shift edit and history-->


<div class="modal fade document-folder" id="shift_history" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.shift-history") }}</h5>
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
                                    <th style="min-width: 180px;">{{ trans('messages.shift') }}</th>
                                    <th style="min-width: 120px;">{{ trans('messages.from-date') }}</th>
                                    <th style="min-width: 120px;">{{ trans('messages.to-date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>10:00 am to 7:30 pm</td>
                                    <td>1<sup>st</sup> Jan, 2023</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>9:30 pm to 7:00 am</td>
                                    <td>1<sup>st</sup> Nov, 2021</td>
                                    <td>1<sup>st</sup> Jan, 2023</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- shift edit and history end-->


<!-- weekly-off edit and history-->
<!-- weekly-off edit form-->

<div class="modal fade document-folder" id="edit_weekly_off" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.edit-weekly-off") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <form method="post" id="edit_weekly_off_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="weekly_off_display" class="control-label">{{ trans('messages.weekly-off') }}</label>
                                <p class="details-text font-weight-bold">1<sup>st</sup> and 3<sup>rd</sup> Saturday</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="new_weekly_off" class="control-label">{{ trans('messages.weekly-off') }}<span class="star">*</span></label>
                                <select class="form-control" name="new_weekly_off">
                                    <option value="">{{ trans('messages.select') }}</option>
                                    <option value="1">1<sup>st</sup> and 3<sup>rd</sup> Saturday</option>
                                    <option value="2">2<sup>nd</sup> and 4<sup>th</sup> Saturday</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="effective_from_weekly_off" class="control-label">{{ trans('messages.effective-from') }}<span class="star">*</span></label>
                                <input type="text" class="form-control edit-date" name="effective_from_weekly_off" placeholder="{{ trans('messages.effective-from') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="submit" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- team edit history-->

<div class="modal fade document-folder" id="weekly_off_history" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.weekly-off-history") }}</h5>
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
                                    <th style="min-width: 180px;">{{ trans('messages.weekly-off') }}</th>
                                    <th style="min-width: 120px;">{{ trans('messages.from-date') }}</th>
                                    <th style="min-width: 120px;">{{ trans('messages.to-date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>2<sup>nd</sup> and 4<sup>th</sup> Saturday</td>
                                    <td>1<sup>st</sup> Jan, 2023</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>1<sup>st</sup> and 3<sup>rd</sup> Saturday</td>
                                    <td>1<sup>st</sup> Nov, 2021</td>
                                    <td>1<sup>st</sup> Jan, 2023</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- in probation ? history-->



<!-- weekly-off edit and history end-->


<script>
    $(function() {
        $('.edit-date').datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            format: 'DD-MM-YYYY',
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
    });
</script>

<!-- job edit form validation -->

<script>
    $("#add-job-details-form").validate({
        errorClass: "invalid-input",
        rules: {
            joining_date_edit: {
                required: true
            },
            leader_name_reporting_manager: {
                required: false
            },
            edit_week_off_effective_date: {
                required: true
            },
            
            notice_period: {
                required: true
            },
           /*  weekly_off: {
                required: true
            }, */
            in_probation: {
                required: false
            },
           employee_code: {required: false , noSpace: true, validateUniqueEmployeeCode:false },
           edit_recruitment_source: {
               required: true,
           },
           edit_reference_name: {
               required: function(){
					return ( (  ( $.trim($("[name='edit_recruitment_source']").val()) != "" ) && ( $.trim($("[name='edit_recruitment_source']").val()) != null ) && ( $.trim($("[name='edit_recruitment_source']").find('option:selected').attr('data-id')) == "{{ config('constants.EMPLOYEE_RECRUITMENT_SOURCE_ID') }}" ) ) ? true : false );
               }
           },

        },
        messages: {
            joining_date_edit: {
                required: "{{ trans('messages.require-enter-joining-date') }}"
            },
            leader_name_reporting_manager: {
                required: "{{ trans('messages.require-select-leader-name-reporting-manager') }}"
            },
           /*  weekly_off: {
                required: "{{ trans('messages.require-select-weekly-off') }}"
            }, */
            in_probation: {
                required: "{{ trans('messages.require-select-probation') }}"
            },
            notice_period: {
                required: "{{ trans('messages.require-select-notice-period') }}"
            },
            employee_code: { required: "{{ trans('messages.require-employee-code') }}"  },
            edit_week_off_effective_date: { required: "{{ trans('messages.require-week-off-effective-date') }}"  },
            edit_recruitment_source: {required: "{{ trans('messages.require-select-recruitment-source') }}"},
            edit_reference_name: {required: "{{ trans('messages.require-select-reference-name') }}"},
        },
        submitHandler: function(form) {
            showLoader()
            form.submit();
        }
    });
</script>

<!-- in_probation form validation -->

<script>
    $("#in_probation").validate({
        errorClass: "invalid-input",
        rules: {
            probation_end_date: {
                required: true
            },
        },
        messages: {
            probation_end_date: {
                required: "{{ trans('messages.require-select-probation-end-date') }}"
            },

        },
    });
</script>

<!-- designation form validation -->

<script>
    
</script>



<!-- shift form validation -->
<script>
	var employee_module_url = '{{config("constants.EMPLOYEE_MASTER_URL")}}' + '/';
    function openJobDetailsModel(thisitem){
    	var employee_id = $.trim($(thisitem).attr('data-emplyee-id'));
        $("[name='employee_record_id']").val(employee_id);
        $("[name='record_id']").val(employee_id);
		
        $.ajax({
    		type: "POST",
    		url: employee_module_url + 'editJobDetails',
    		data: {
    			"_token": "{{ csrf_token() }}",'employee_id':employee_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			$('.add-job-details-html').html(response);
    			openBootstrapModal('job-details-model');
    			$(".select2").select2();

    			 $("[name='joining_date_edit']").datetimepicker({
    			    	useCurrent: false,
    			        viewMode: 'days',
    			        ignoreReadonly: true,
    			        format: 'DD-MM-YYYY',
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

    			 var joining_date_edit = $.trim($("[name='joining_date_edit']").val());
    			 var edit_week_off_effective_date = $.trim($("[name='edit_week_off_effective_date']").val());

   			    
    			
    			$('[name="edit_week_off_effective_date"]').datetimepicker({
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

    			$("[name='joining_date_edit']").datetimepicker().on('dp.change', function(e) {
    				if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    					var incrementDay = moment((e.date)).startOf('d');
    				 	$("[name='edit_week_off_effective_date']").data('DateTimePicker').minDate(incrementDay);
    				} else {
    					$("[name='edit_week_off_effective_date']").data('DateTimePicker').minDate(false);
    				} 
    				
    			    $(this).data("DateTimePicker").hide();
    			});

    		    $("[name='edit_week_off_effective_date']").datetimepicker().on('dp.change', function(e) {
    		    	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    			        var decrementDay = moment((e.date)).endOf('d');
    			        $("[name='joining_date_edit']").data('DateTimePicker').maxDate(decrementDay);
    		    	} else {
    		    		 $("[name='joining_date_edit']").data('DateTimePicker').maxDate(false);
    		        }
    		        $(this).data("DateTimePicker").hide();
    		    });

    		    if( joining_date_edit != "" && joining_date_edit != null ){
        		    $("[name='edit_week_off_effective_date']").data("DateTimePicker").minDate(moment(joining_date_edit,'{{ config("constants.DEFAULT_DATE_FORMAT") }}').startOf('d'));
        		}

    		    if( edit_week_off_effective_date != "" && edit_week_off_effective_date != null ){
    		    	$("[name='joining_date_edit']").data("DateTimePicker").maxDate(moment(edit_week_off_effective_date,'{{ config("constants.DEFAULT_DATE_FORMAT") }}').startOf('d'));
        		}
        		
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
     }

    function addJobDetailsModel(){
    	if($('#add-job-details-form').valid() != true){
    		return false;
    	}
    	var employee_record_id = $.trim($("[name='employee_record_id']").val());
    	var employee_code = $.trim($("[name='employee_code']").val());
    	var joining_date_edit = $.trim($("[name='joining_date_edit']").val());
    	var edit_week_off_effective_date = $.trim($("[name='edit_week_off_effective_date']").val());
    	var leader_name_reporting_manager = $.trim($("[name='leader_name_reporting_manager']").val());
    	var in_probation = $.trim($("[name='in_probation']").val());
    	var notice_period = $.trim($("[name='notice_period']").val());
    	var edit_recruitment_source = $.trim($("[name='edit_recruitment_source']").val());
    	var edit_reference_name = $.trim($("[name='edit_reference_name']").val());
    	
    	alertify.confirm("{{ trans('messages.update-job') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-job')]) }}",function() { 
	    	 $.ajax({
	     		type: "POST",
	     		dataType :'json',
	     		url: employee_module_url + 'addJobDetails',
	     		data:{
					"_token": "{{ csrf_token() }}",
					'employee_record_id':employee_record_id,'employee_code':employee_code,
					'joining_date_edit':joining_date_edit,'leader_name_reporting_manager':leader_name_reporting_manager,
					'in_probation':in_probation,
					'edit_week_off_effective_date':edit_week_off_effective_date,
					'notice_period':notice_period,
					'recruitment_source':edit_recruitment_source,
					'reference_name':edit_reference_name,
					
				},
				beforeSend: function() {
	     			//block ui
	     			showLoader();
	     		},
	     		success: function(response) {
	     			hideLoader();
	     			if( response.status_code == 1 ){
						$("#job-details-model").modal('hide');
						alertifyMessage('success',response.message);
						$('.employee-job-record').html(response.data.html);
						
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

    function getDesignationHistory(thisitem){
    	current_selected_row = thisitem;
    	var record_id = $.trim($(thisitem).attr("data-record-id"));
    	var record_type = $.trim($(thisitem).attr("data-record-type"));
    	$.ajax({
    		type: "POST",
    		url: employee_module_url + 'getEmployeeDesignationHistory',
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'employee_id':record_id,
    			'record_type' : record_type 
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    	 		hideLoader();
    			if( response != "" && response != null ){
        			switch(record_type){
        				case '{{ config("constants.DESIGNATION_LOOKUP") }}':
        					$(".employee-designation-history-modal-title").html("{{ trans('messages.designation-history') }}" + common_emp_modal_header_title);
        				    $(".employee-designation-history-title").html("{{ trans('messages.designation') }}");
            				break;
        				case '{{ config("constants.TEAM_LOOKUP") }}':
        					$(".employee-designation-history-modal-title").html("{{ trans('messages.team-history') }}" + common_emp_modal_header_title );
        				    $(".employee-designation-history-title").html("{{ trans('messages.team') }}");
            				break;
        				case '{{ config("constants.SHIFT_RECORD_TYPE") }}':
        					$(".employee-designation-history-modal-title").html("{{ trans('messages.shift-history') }}" + common_emp_modal_header_title);
        				    $(".employee-designation-history-title").html("{{ trans('messages.shift') }}");
            				break;
        				case '{{ config("constants.WEEK_OFF_RECORD_TYPE") }}':
        					$(".employee-designation-history-modal-title").html("{{ trans('messages.weekly-off-history') }}" + common_emp_modal_header_title);
        				    $(".employee-designation-history-title").html("{{ trans('messages.weekly-off') }}");
            				break; 
        			}
        			$(".employee-designation-history-html").html(response);
    				openBootstrapModal("employee-designation-history-modal");
    			}
    	 	},
    		error: function() {
    			hideLoader();
    		}
    	});
     }

    function calculationProbationEndDate(thisitem){
		var emp_joining_date = $.trim($(thisitem).attr('data-joining-date'));
		var selected_duration = $.trim($("[name='probation_policy_id']").find("option:selected").attr("data-duration"));
		var selected_days = $.trim($("[name='probation_policy_id']").find("option:selected").attr("data-days"));

		if( emp_joining_date != "" && selected_duration != null && selected_days != null ){
			var calculated_end_probation_date = moment(emp_joining_date, 'YYYY-MM-DD').add( selected_days , selected_duration );
			$("[name='probation_end_date']").val(moment(calculated_end_probation_date).format('DD-MM-YYYY'));
		}
	}

	function showReferenceEmployeeSelection(){
		var edit_recruitment_source = $.trim($("[name='edit_recruitment_source']").find('option:selected').attr('data-id'));
		console.log("edit_recruitment_source")
		if( edit_recruitment_source != "" && edit_recruitment_source != null &&  edit_recruitment_source == "{{ config('constants.EMPLOYEE_RECRUITMENT_SOURCE_ID') }}" ){
			$(".recruitment-source-employee-div").show();
		} else {
			$(".recruitment-source-employee-div").hide();
		}	 
	}
</script>