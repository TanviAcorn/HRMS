	 	<div class="col-lg-4 col-md-6 total-leave-card">
            <div class="card card-display border-0 px-0 pt-3 pb-0 h-100">
                <div class="card-body  py-3">
                    <div class="px-sm-2">
                        <div id="attendance-calendar-{{ $month }}-{{ $year }}"></div>
                    </div>
                    <?php /* ?>
                    <div class="d-flex align-items-center py-3 pl-2">
                        <h5 class="details-title h4 mb-0">{{ trans("messages.present-days") }}:</h5>
                        <p class="h6 ml-2 mb-0 mr-2"><span class="month-wise-prsent-attendance-days-{{ $month }}-{{ $year }}"></span></p>
                        <h5 class="details-title h4 mb-0">{{ trans("messages.paid-days") }}:</h5>
                        <p class="h6 ml-2 mb-0 "><span class="month-wise-paid-attendance-days-{{ $month }}-{{ $year }}"></span></p>
                    </div>
                    <?php */ ?>
                </div>
            </div>
        </div>
<script>



$(function(){
	var total_days_count = '{{ (isset($totalDays) ? $totalDays : '' ) }}';
	var total_display_day_count =  '{{ (isset($totalDisplayDays) ? $totalDisplayDays : '' ) }}';
  	var total_week_off_holiday_days_count = '{{ (isset($totalWeekOfHolidayDays) ? $totalWeekOfHolidayDays : '' ) }}';
  	var total_present_days_count = '{{ (isset($totalPresentDays) ? $totalPresentDays : '' ) }}';
  	var total_half_leave_days_count = '{{ (isset($totalHalfLeaveDays) ? $totalHalfLeaveDays : '' ) }}';
  	var total_absent_days_count = '{{ (isset($totalAbsentDays) ? $totalAbsentDays : '' ) }}';
  	var total_suspend_days_count = '{{ (isset($totalSuspendDays) ? $totalSuspendDays : '' ) }}';
  	var total_adjustment_days_count = '{{ (isset($totalAdjustmentDays) ? $totalAdjustmentDays : '' ) }}';
  	var total_approve_leave_count = '{{ ( isset($totalApproveLeaveCount) ? $totalApproveLeaveCount : 0 ) }}';
  	var total_approve_half_leave_count = '{{ ( isset($totalApproveHalfLeaveCount) ? $totalApproveHalfLeaveCount : 0 ) }}';
  	var paid_days_count = '{{ ( isset($salaryPaidDayCount) ? $salaryPaidDayCount : 0 ) }}'; 

  	$('.total-attendance-days').html(total_display_day_count);
	$('.total-week-off-holidays').html(total_week_off_holiday_days_count);
	$('.total-present-attendance-days').html(total_present_days_count);
	$('.total-half-leave-attendance-days').html(total_half_leave_days_count);
	$('.total-absent-attendance-days').html(total_absent_days_count);
	$('.total-suspend-attendance-days').html(total_suspend_days_count);
	$('.total-adjustment-attendance-days').html(total_adjustment_days_count);
	$('.total-approved-leave-count').html(total_approve_leave_count);
	$('.total-approved-half-leave-count').html(total_approve_half_leave_count);
	var present_day_count  = '{{ (isset($presentDayCount) ? $presentDayCount : 0 ) }}';
  	
	$('.month-wise-prsent-attendance-days-{{ $month }}-{{ $year }}').html(present_day_count);
	$('.month-wise-paid-attendance-days-{{ $month }}-{{ $year }}').html(paid_days_count);
	
	$(document).ready(function(){
		<?php if( isset($month) && (!empty($month)) ) { ?>
		var month = '<?php echo $month ?>';
		var year = '<?php echo $year ?>'; 
		if( month != "" && month != null && year != "" && year != null ){
			 var attendanceDates = [];
			 var attendanceHolidayEvents = [];
			 var attendancePresentDates = [];
			 var attendanceAbsentDates = [];
			 var attendanceHalfLeaveDates = [];
			 var attendanceSuspendDates = [];
			 var attendanceAdjustmentDates = [];
			 var attendanceWeekOffDates = [];
			 var attendanceApproveLeaveDates = [];
			 var attendanceApproveHalfLeaveDates = [];
			 var unapplyHalfLeaveDates = [];

			 @if( isset($holidatDates)  && ( count($holidatDates) > 0 ) )
				@foreach($holidatDates as $holidatDate)
					var rowEvent = {};
				 	rowEvent = {  start: '{{ $holidatDate }}'  , title : '{{ config("constants.HOLIDAY_SYMBOL")}}'};
				 	attendanceHolidayEvents.push(rowEvent);
				@endforeach
			@endif

			@if( isset($presentDates)  && ( count($presentDates) > 0 ) )
				@foreach($presentDates as $presentDate)
					var rowEvent = {};
				 	rowEvent = {  start: '{{ $presentDate }}' , title : '{{ config("constants.PRESENT_SYMBOL")}}' };
				 	attendancePresentDates.push(rowEvent);
				@endforeach
			@endif

			@if( isset($absentDates)  && ( count($absentDates) > 0 ) )
				@foreach($absentDates as $absentDate)
					var rowEvent = {};
				 	rowEvent = {  start: '{{ $absentDate }}'  , title : '{{ config("constants.ABSENT_SYMBOL")}}' };
				 	attendanceAbsentDates.push(rowEvent);
				@endforeach
			@endif

			@if( isset($halfLeaveDates)  && ( count($halfLeaveDates) > 0 ) )
				@foreach($halfLeaveDates as $halfLeaveDate)
					var rowEvent = {};
				 	rowEvent = {  start: '{{ $halfLeaveDate }}'  , title : '{{ config("constants.HALF_LEAVE_SYMBOL")}}'};
				 	attendanceHalfLeaveDates.push(rowEvent);
				@endforeach
			@endif

			@if( isset($suspendDates)  && ( count($suspendDates) > 0 ) )
				@foreach($suspendDates as $suspendDate)
					var rowEvent = {};
				 	rowEvent = {  start: '{{ $suspendDate }}'  , title : '{{ config("constants.SUSPEND_SYMBOL")}}' };
				 	attendanceSuspendDates.push(rowEvent);
				@endforeach
			@endif

			@if( isset($adjustmentDates)  && ( count($adjustmentDates) > 0 ) )
				@foreach($adjustmentDates as $adjustmentDate)
					var rowEvent = {};
				 	rowEvent = {  start: '{{ $adjustmentDate }}'  , title : '{{ config("constants.ADJUSTMENT_SYMBOL")}}' };
				 	attendanceAdjustmentDates.push(rowEvent);
				@endforeach
			@endif

			@if( isset($weekOffDates)  && ( count($weekOffDates) > 0 ) )
				@foreach($weekOffDates as $weekOffDate)
					var rowEvent = {};
				 	rowEvent = {  start: '{{ $weekOffDate }}'  , title : '{{ config("constants.WEEKOFF_SYMBOL")}}' };
				 	attendanceWeekOffDates.push(rowEvent);
				@endforeach
			@endif

			@if( isset($approvedLeaveDates)  && ( count($approvedLeaveDates) > 0 ) )
				@foreach($approvedLeaveDates as $approvedLeaveDate)
					var rowEvent = {};
				 	rowEvent = {  start: '{{ $approvedLeaveDate }}'  , title : '{{ config("constants.APPROVED_LEAVE_SYMBOL")}}' };
				 	attendanceApproveLeaveDates.push(rowEvent);
				@endforeach
			@endif

			@if( isset($approvedHalfLeaveDates)  && ( count($approvedHalfLeaveDates) > 0 ) )
				@foreach($approvedHalfLeaveDates as $approvedHalfLeaveDate)
					var rowEvent = {};
				 	rowEvent = {  start: '{{ $approvedHalfLeaveDate }}'  , title : '{{ config("constants.APPROVED_HALF_LEAVE_SYMBOL")}}' };
				 	attendanceApproveHalfLeaveDates.push(rowEvent);
				@endforeach
			@endif

			@if( isset($calendarViewUnpaidHalfLeaveDates)  && ( count($calendarViewUnpaidHalfLeaveDates) > 0 ) )
				@foreach($calendarViewUnpaidHalfLeaveDates as $calendarViewUnpaidHalfLeaveDate)
					var rowEvent = {};
				 	rowEvent = {  start: '{{ $calendarViewUnpaidHalfLeaveDate }}'  , title : '{{ config("constants.UNPAID_HALF_LEAVE_SYMBOL")}}' };
				 	unapplyHalfLeaveDates.push(rowEvent);
				@endforeach
			@endif

			if(  attendanceHolidayEvents.length > 0  ){
				attendanceDates.push({ events:attendanceHolidayEvents, color:'{{ config("constants.HOLIDAY_COLOR_CODE") }}'});
			}
				
			if(  attendancePresentDates.length > 0  ){
				attendanceDates.push({ events:attendancePresentDates, color:'{{ config("constants.PRESENT_COLOR_CODE") }}'});
			}
			
			if(  attendanceAbsentDates.length > 0  ){
				attendanceDates.push({ events:attendanceAbsentDates, color:'{{ config("constants.ABSENT_COLOR_CODE") }}'});
			}

			if(  attendanceHalfLeaveDates.length > 0  ){
				attendanceDates.push({events:attendanceHalfLeaveDates,color:'{{ config("constants.HALF_LEAVE_COLOR_CODE") }}'});
			}
			
			if(  attendanceSuspendDates.length > 0  ){
				attendanceDates.push({ events:attendanceSuspendDates, color:'{{ config("constants.SUSPEND_COLOR_CODE") }}'});
			}
			
			if(  attendanceAdjustmentDates.length > 0  ){
				attendanceDates.push({ events:attendanceAdjustmentDates, color:'{{ config("constants.ADJUSTMENT_COLOR_CODE") }}'});
			}
			
			if(  attendanceWeekOffDates.length > 0  ){
				attendanceDates.push({ events:attendanceWeekOffDates, color:'{{ config("constants.WEEKOFF_COLOR_CODE") }}'});
			}

			if(  attendanceApproveLeaveDates.length > 0  ){
				attendanceDates.push({ events:attendanceApproveLeaveDates, color:'{{ config("constants.APPROVED_LEAVE_COLOR_CODE") }}'});
			}

			if(  attendanceApproveHalfLeaveDates.length > 0  ){
				attendanceDates.push({ events:attendanceApproveHalfLeaveDates, color:'{{ config("constants.APPROVED_HALF_LEAVE_COLOR_CODE") }}'});
			}

			if(  unapplyHalfLeaveDates.length > 0  ){
				attendanceDates.push({ events:unapplyHalfLeaveDates, color:'{{ config("constants.UNPAID_HALF_LEAVE_COLOR_CODE") }}'});
			}
			
			///console.log("attendanceDates");
			//console.log(attendanceDates);
			//console.log('{{ ( isset($calendarStartDate) ? $calendarStartDate : '' ) }}');
			//console.log('{{ ( isset($calendarEndDate) ? $calendarEndDate : '' ) }}'); 
			calendar_id = "";
			if( calendar_id != "" && calendar_id != null ){
				var attendanceCalendar = document.getElementById(calendar_id);
			} else {
				//console.log(  "sssss" );
				//console.log(  'attendance-calendar-'+month+'-' + year );
				var attendanceCalendar = document.getElementById('attendance-calendar-'+month+'-' + year);
			}
			
			//console.log("attendanceCalendar = ");
			//console.log(attendanceCalendar);
			var calendar = new FullCalendar.Calendar(attendanceCalendar, {
				timeZone: 'local',
				customButtons: {
			    myCustomButton: {
			    		text: '{{ ( isset($calendarStartDate) ? convertDateFormat( date("Y-m-d" ,strtotime("+1 Month" , strtotime($calendarStartDate))) , "F-Y" ) : '' ) }}',
			    	}
			  	},
		        headerToolbar: {
		            left: false ,
		            center: 'title',
		            right: 'prev,next',
		        },
		        initialDate: '{{ ( isset($calendarStartDate) ? $calendarStartDate : '' ) }}',
		        viewRender: function(view) {
		            var title = view.title;
		            $("#externalTitle").html("mitesh");
		        },
		        validRange: {
		      	    start: '{{ ( isset($calendarStartDate) ? $calendarStartDate : '' ) }}',
		      	    end: '{{ ( isset($calendarEndDate) ? date("Y-m-d" ,strtotime("+1 days" , strtotime($calendarEndDate))) : '' ) }}'
		      	},
		       	navLinks: false, 
		        editable: false,
		        selectable: false,
		        height: 'auto',
		        firstDay: 1,
		        eventSources: attendanceDates
		    });

		    calendar.render();	
		}


		
		//createAttendanceCalendar( null , '<?php echo $month ?>' , '<?php echo $year ?>');
		<?php } ?>
	});
	
});
 
  </script>
  
 