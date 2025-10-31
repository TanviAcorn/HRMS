								<div class="col-12 leave-calendar mb-3">
                                    <div class="row">
                                        <div class="col-12 mt-1  mb-1">
                                            <h5 class="apply-card-title">{{ trans("messages.team-leave-calendar") }}
                                            </h5>
                                        </div>
                                        <div class="holiday-calender shadow-sm col-md-12 col-sm-8">
                                            <div id="leave-calendar"></div>
                                        </div>
                                        <div class="col-12 leave-calendar calendar-style">
                                            <div class="leave-calendar-type">
                                                <div class="weekoff"><i class="fas fa-circle calendar-type-icon text-success week-off-color-code"></i> {{ trans("messages.weekoff") }}</div>
                                                <div class="leave"><i class="fas fa-circle calendar-type-icon absent"></i>{{ trans("messages.absent") }}</div>
                                                <div class="holiday"><i class="fas fa-circle calendar-type-icon text-primary holiday-off-color-code"></i>{{ trans("messages.holidays") }}</div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 member-list member-list-scroll">
                                    @if(count($teamLeaveDetails) > 0 )
										@foreach($teamLeaveDetails as $teamLeaveDetail)
											<div class="member-card row">
									        	<div class="col-1 px-0">
									        		@php
									        		echo employeeProfilePicView($teamLeaveDetail); 
									            	@endphp
									            </div>
									            <div class="col-11 pl-3">
									            	<div class="member-detail row">
									                	<div class="member-name col-4 text-left">{{ ( isset($teamLeaveDetail['employee_name']) ? $teamLeaveDetail['employee_name'] : '' ) }}</div>
									                    <div class="leave-date col-md-5 col-4 text-left">{{ ( isset($teamLeaveDetail['leave_duration']) ? $teamLeaveDetail['leave_duration'] : '' ) }}</div>
									                    <div class="leave-time col-md-3 col-4 text-left px-1">{{ ( isset($teamLeaveDetail['days']) ? $teamLeaveDetail['days'] . ' day(s)' : '' ) }}</div>
									              	</div>
									           	</div>
									        </div>
										@endforeach
									
									@else	
										{{ trans('messages.no-record-found') }}	
									@endif	
                                </div>
								<script>
						        $(document).ready(function(){
							         var calendar_start_date = "{{ isset($calendarStartDate)  ? $calendarStartDate : date('Y-m-d') }}"; 
						        	 var calendarEl = document.getElementById('leave-calendar');		
									 var allEventDates = [];
									 var allHolidayEvents = [];
									 var allWeekOffDates = [];
									 var allAppliedLeaveDates = [];
									 @if( isset($holidayDetails)  && ( count($holidayDetails) > 0 ) )
										@foreach($holidayDetails as $holidayDetail)
											var rowEvent = {};
									 		rowEvent = {  start: '{{ $holidayDetail->dt_holiday_date }}'};
									 		allHolidayEvents.push(rowEvent);
										@endforeach
									 @endif

									 @if( isset($weekOffDates)  && ( count($weekOffDates) > 0 ) )
										@foreach($weekOffDates as $weekOffDate)
											var rowEvent = {};
									 		rowEvent = { start: '{{ $weekOffDate }}' , overLap: false,  rendering: "background" };
									 		allWeekOffDates.push(rowEvent);
										@endforeach
									 @endif
									 @php
									 $uniqueAppliedDates = [];
									@endphp
									 @if( isset($appliedLeaveDates)  && ( count($appliedLeaveDates) > 0 ) )
										@foreach($appliedLeaveDates as $appliedLeaveDate)
											<?php if( !in_array( $appliedLeaveDate ,$uniqueAppliedDates )) { ?>
											var rowEvent = {};
									 		rowEvent = { start: '{{ $appliedLeaveDate }}'  };
									 		allAppliedLeaveDates.push(rowEvent);
									 		<?php $uniqueAppliedDates[] = $appliedLeaveDate;  ?>
											<?php } ?>
										@endforeach
									@endif
									 
									//console.log("allWeekOffDates");
									//console.log(allWeekOffDates);
									//console.log("allHolidayEvents");
									//console.log(allHolidayEvents);
									//console.log("allAppliedLeaveDates");
									//console.log(allAppliedLeaveDates);
									allEventDates.push({events:allHolidayEvents,color:'{{ config("constants.HOLIDAY_COLOR_CODE") }}'});
									allEventDates.push({events:allWeekOffDates,color:'{{ config("constants.WEEKOFF_COLOR_CODE") }}'});
									allEventDates.push({events:allAppliedLeaveDates,color:'{{ config("constants.ABSENT_COLOR_CODE") }}'});

									if( calendar != "" && calendar != null ){
										calendar.destroy();
								 	}
									
									calendar = new FullCalendar.Calendar(calendarEl, {
						                timeZone: 'local',
						                customButtons: {
						        		    myCustomButton: {
						        		      text: 'Today',
						        		      click: function() {
						        		    	  leaveCalendar("<?php echo date('Y-m-d')?>");	
						        		      }
						        			}
						        		},
						        		headerToolbar: {
						                    left: 'title',
						                    center: false,
						                    right: 'myCustomButton prev,next',
						       			},
						                initialDate: calendar_start_date,
						                navLinks: false, // can click day/week names to navigate views
						                editable: false,
						                selectable: false,
						                height: 'auto',
						                firstDay: 1,
						                eventSources: allEventDates
						            });
						        	calendar.render();
									

									
								})
								
					           </script>