<div class="container-fluid my-leaves my-attendance apply-leave calendar-style px-0 pt-0 pb-3">
    <div class="row pt-0 pb-4 align-items-center">
        <div class="col-lg-7 col-md-5 col-sm-4 mb-md-0 mb-3">

        </div>
        <div class="col-lg-2 col-md-3 col-sm-3 col-6 py-2">
            <div class="form-group mb-0">
                <input type="text" class="form-control" value="{{ ( isset($startMonth) ? $startMonth : '' ) }}" name="attendance_filter_from_month" placeholder="{{ trans('messages.from-month') }}">
            </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-3 col-6">
            <div class="form-group mb-0">
                <input type="text" class="form-control"  value="{{ ( isset($endMonth) ? $endMonth : '' ) }}" name="attendance_filter_to_month" placeholder="{{ trans('messages.to-month') }}">
            </div>
        </div>
        <div class="col-sm-1 col-12 justify-content-center d-flex pl-md-0 mt-sm-0 mt-3">
            <div class="form-group mb-0">
                <button type="button" onclick="filterData()" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.search') }}">{{ trans('messages.search') }}</button>
            </div>
        </div>
	</div>
	<input type="hidden" name="attendance_employee_id" value="{{ isset($employeeId) ? $employeeId : Wild_tiger::encode( session()->get('user_id') )  }}">
	<div class="row attendance-ajax-view">
    
    </div>
     
    <div class="row total-attendance-report" style="display:none">
        <div class="col-12 total-leave-card">
            <div class="card card-display border-0 h-100">
                <div class="card-body">
                    <div class="row px-3">
                        <div class="col-md-4 col-sm-6 d-flex align-items-center py-1 mb-1 pl-2">
                            <h5 class="details-title h4 mb-0">{{ trans("messages.total-days") }}</h5>
                            <p class="h6 ml-2 mb-0"><span class="total-attendance-days"></span></p>
                        </div>
                        <div class="col-md-4 col-sm-6 d-flex align-items-center py-1 mb-1 pl-2">
                            <h5 class="details-title h4 mb-0">{{ trans("messages.total-present-days") }}</h5>
                            <p class="h6 ml-2 mb-0"><span class="total-present-attendance-days"></span></p>
                        </div>
                        <div class="col-md-4 col-sm-6 d-flex align-items-center py-1 mb-1 pl-2">
                            <h5 class="details-title h4 mb-0">{{ trans("messages.total-leave") }}</h5>
                            <p class="h6 ml-2 mb-0"><span class="total-absent-attendance-days"></span></p>
                        </div>
                        <div class="col-md-4 col-sm-6 d-flex align-items-center py-1 mb-1 pl-2">
                            <h5 class="details-title h4 mb-0">{{ trans("messages.total-half-leave") }}</h5>
                            <p class="h6 ml-2 mb-0"><span class="total-half-leave-attendance-days"></span></p>
                        </div>
                        <div class="col-md-4 col-sm-6 d-flex align-items-center py-1 mb-1 pl-2">
                            <h5 class="details-title h4 mb-0">{{ trans("messages.total-adjustment") }}</h5>
                            <p class="h6 ml-2 mb-0"><span class="total-adjustment-attendance-days"></span></p>
                        </div>
                        <?php /* ?>
                        <div class="col-md-4 col-sm-6 d-flex align-items-center py-1 mb-1 pl-2">
                            <h5 class="details-title h4 mb-0">{{ trans("messages.total-absent") }}</h5>
                            <p class="h6 ml-2 mb-0"><span class="total-absent-attendance-days"></span></p>
                        </div>
                        <?php */ ?>
                        <div class="col-md-4 col-sm-6 d-flex align-items-center py-1 mb-1 pl-2">
                            <h5 class="details-title h4 mb-0">{{ trans("messages.total-week-off-holidays") }}</h5>
                            <p class="h6 ml-2 mb-0"><span class="total-week-off-holidays"></span></p>
                        </div>
                        <div class="col-md-4 col-sm-6 d-flex align-items-center py-1 mb-1 pl-2">
                            <h5 class="details-title h4 mb-0">{{ trans("messages.total-suspend") }}</h5>
                            <p class="h6 ml-2 mb-0"><span class="total-suspend-attendance-days"></span></p>
                        </div>
                        <div class="col-md-4 col-sm-6 d-flex align-items-center py-1 mb-1 pl-2">
                            <h5 class="details-title h4 mb-0">{{ trans("messages.total-approved-leave") }}</h5>
                            <p class="h6 ml-2 mb-0"><span class="total-approved-leave-count"></span></p>
                        </div>
                        <div class="col-md-4 col-sm-6 d-flex align-items-center py-1 mb-1 pl-2">
                            <h5 class="details-title h4 mb-0">{{ trans("messages.total-approved-half-leave") }}</h5>
                            <p class="h6 ml-2 mb-0"><span class="total-approved-half-leave-count"></span></p>
                        </div>

                        <div class="col-12 leave-calendar pl-0">
                            <div class="leave-calendar-type">
                                <div class="weekoff"><i class="fas fa-circle calendar-type-icon text-success present"></i> {{ trans("messages.present") }}</div>
                                <div class="weekoff"><i class="fas fa-circle calendar-type-icon text-success"></i> {{ trans("messages.weekoff") }}</div>
                                <div class="leave"><i class="fas fa-circle calendar-type-icon absent"></i>{{ trans("messages.absent") }}</div>
                                <div class="holiday"><i class="fas fa-circle calendar-type-icon holiday-color"></i>{{ trans("messages.holiday") }}</div>
                                <div class="holiday"><i class="fas fa-circle calendar-type-icon adjustment"></i>{{ trans("messages.adjustment") }}</div>
                                <div class="holiday"><i class="fas fa-circle calendar-type-icon half-leaves"></i>{{ trans("messages.half-leave") }}</div>
                                <div class="holiday"><i class="fas fa-circle calendar-type-icon suspend-color"></i>{{ trans("messages.suspend") }}</div>
                                <div class="holiday"><i class="fas fa-circle calendar-type-icon approved-leave-color"></i>{{ trans("messages.approved-leave") }}</div>
                                <div class="holiday"><i class="fas fa-circle calendar-type-icon approved-half-leave-color"></i>{{ trans("messages.approved-half-leave") }}</div>
                                <div class="holiday"><i class="fas fa-circle calendar-type-icon unpaid-half-leave-color"></i>{{ trans("messages.unpaid-half-leave") }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <script>
		$(document).ready(function(){
			filterData();
		})
    	$(function() {
        	$("[name='attendance_filter_from_month'],[name='attendance_filter_to_month']").datetimepicker({
                useCurrent: false,
                viewMode: 'days',
                ignoreReadonly: true,
                format: '{{ config("constants.DEFAULT_MONTH_FORMAT") }}',
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
        	$("[name='attendance_filter_from_month']").data('DateTimePicker').maxDate(moment().endOf('month'));
        	$("[name='attendance_filter_to_month']").data('DateTimePicker').maxDate(moment().endOf('month'));
        	
        	<?php if( date('d' ,strtotime("now")) >= config('constants.SALARY_CYCLE_START_DATE')  ) { ?>
        	$("[name='attendance_filter_from_month']").data('DateTimePicker').maxDate(moment().add(1,'months').endOf('month'));
        	$("[name='attendance_filter_to_month']").data('DateTimePicker').maxDate(moment().add(1,'months').endOf('month'));
        	<?php } ?>
        	
        	<?php if( isset($empJoiningDate) && (!empty($empJoiningDate)) ) { ?>
        		var attendance_emp_joining_date = '<?php echo $empJoiningDate ?>';
        		//console.log('attendance_emp_joining_date = ' + attendance_emp_joining_date );
        		//console.log(moment(attendance_emp_joining_date,'YYYY-MM-DD').format('DD-MM-YYYY'));
	        	$("[name='attendance_filter_from_month']").data('DateTimePicker').minDate(moment(attendance_emp_joining_date).startOf('month'));
	        	$("[name='attendance_filter_to_month']").data('DateTimePicker').minDate(moment(attendance_emp_joining_date).startOf('month'));
        	<?php } ?>
        });
       
         $(function(){
        	$("[name='attendance_filter_from_month']").datetimepicker().on('dp.change', function(e) {
        		if( $(this).val() != "" && $(this).val() != null ){
        			var incrementDay = moment((e.date)).startOf('d');
            	 	$("[name='attendance_filter_to_month']").data('DateTimePicker').minDate(incrementDay);
            	} else {
            		$("[name='attendance_filter_to_month']").data('DateTimePicker').minDate(false);
                }
        		
        	    $(this).data("DateTimePicker").hide();
        	});

            $("[name='attendance_filter_to_month']").datetimepicker().on('dp.change', function(e) {
            	if( $(this).val() != "" && $(this).val() != null ){
            		var decrementDay = moment((e.date)).endOf('d');
                    $("[name='attendance_filter_from_month']").data('DateTimePicker').maxDate(decrementDay);
                } else {
                	$("[name='attendance_filter_from_month']").data('DateTimePicker').maxDate(false);
                }
                
                
                $(this).data("DateTimePicker").hide();
            });
        		
        });
         
        var attendance_module_url = '{{config("constants.EMPLOYEE_ATTENDANCE_MASTER_URL")}}' + '/';
        function filterData(){
        	var attendance_filter_from_month = $.trim($('[name="attendance_filter_from_month"]').val());
    		var attendance_filter_to_month = $.trim($('[name="attendance_filter_to_month"]').val());
    		var attendance_employee_id =  $.trim($('[name="attendance_employee_id"]').val());

    		if( attendance_filter_from_month == "" || attendance_filter_from_month == null ) {
    			alertifyMessage('error', '{{ trans("messages.required-from-month") }}' );
    			$('[name="attendance_filter_from_month"]').focus()
				return false;
        	}

    		if( attendance_filter_to_month == "" || attendance_filter_to_month == null ) {
    			alertifyMessage('error', '{{ trans("messages.required-to-month") }}' );
    			$('[name="attendance_filter_to_month"]').focus()
    			return false;
        	}
    		
    		//console.log("attendance_employee_id = " + attendance_employee_id );
    		$.ajax({
				type: "POST",
				url: attendance_module_url + 'getAttendanceRecord',
				data: {
					"_token": "{{ csrf_token() }}",
					'attendance_filter_from_month':attendance_filter_from_month,
					'attendance_filter_to_month':attendance_filter_to_month,
					'employee_id':attendance_employee_id,
				},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if (response != "" && response != null) {
                       $('.attendance-ajax-view').html('');
                       $('.attendance-ajax-view').html(response);
                       $('.total-attendance-report').show();
                    } else {
                    	$('.total-attendance-report').hide();
                       }
				},
				error: function() {
					hideLoader();
				}
			});
    		$('.attendance-ajax-view').html('');
        }
    </script>
   
