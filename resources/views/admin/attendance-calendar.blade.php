<script>
function createAttendanceCalendar(calendar_id = null , month = null , year = null ){
	//alert("welcome");
	//console.log(" month "   +month   + " year "  + year );

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
		
		//console.log("attendanceDates");
		//console.log(attendanceDates);
		//console.log('{{ ( isset($calendarStartDate) ? $calendarStartDate : '' ) }}');
		//console.log('{{ ( isset($calendarEndDate) ? $calendarEndDate : '' ) }}'); 
		if( calendar_id != "" && calendar_id != null ){
			var attendanceCalendar = document.getElementById(calendar_id);
		} else {
			//console.log(  "sssss" );
		//	console.log(  'attendance-calendar-'+month+'-' + year );
			var attendanceCalendar = document.getElementById('attendance-calendar-'+month+'-' + year);
		}
		
		//console.log("attendanceCalendar = ");
		//console.log(attendanceCalendar);
		var calendar = new FullCalendar.Calendar(attendanceCalendar, {
			timeZone: 'local',
	        headerToolbar: {
	            left: false,
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
}
</script>