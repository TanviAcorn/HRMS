					@if(count($holidayDetails) > 0 )
            			@foreach($holidayDetails as $holidayDetail)
            				@php
            				$pastHolidayClass  = '';
            				if( strtotime($holidayDetail->dt_holiday_date) < strtotime(date('Y-m-d'))  ){
            					$pastHolidayClass  = 'past-holiday';
            				}		
            				@endphp
            				<div class="col-sm-6 holiday-card">
		                        <div class="all-holiday  {{ $pastHolidayClass }}">
		                            <div class="media py-2">
		                                <div class="date-view border-{{ strtolower(date('M' ,strtotime($holidayDetail->dt_holiday_date) )) }} text-center">
		                                    <div class="short-month text-white text-uppercase  p-1 bg-{{ strtolower(date('M' ,strtotime($holidayDetail->dt_holiday_date) )) }}">{{ ( isset($holidayDetail->dt_holiday_date) ? date('M' ,strtotime($holidayDetail->dt_holiday_date) ) : '' )  }}</div>
		                                    <div class="f-20">{{ ( isset($holidayDetail->dt_holiday_date) ? date('d' ,strtotime($holidayDetail->dt_holiday_date) ) : '' )  }}</div>
		                                </div>
		                                <div class="media-body">
		                                    <div class="ml-3">
		                                        <h5 class="m-0 holiday-name">{{ ( isset($holidayDetail->v_holiday_name) ? $holidayDetail->v_holiday_name : '' )  }} </h5>
		                                        <p class="mb-0 holiday-date">{{ ( isset($holidayDetail->dt_holiday_date) ? date('l' , strtotime($holidayDetail->dt_holiday_date)) : '' )  }}</p>
		                                    </div>
		                                </div>
		                            </div>
		                        </div>
		                    </div>
            			@endforeach
            		@else
						<div class="ml-3">{{ trans('messages.no-holiday-record')  }}</div>
            		@endif