			@if( isset($requestPageNo) && ($requestPageNo == 1 ) )
			<div class="card card-body">
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left employee-name-code-th" style="width:200px;min-width:200px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                @if(count($monthAllDates) > 0 )
                                	@foreach($monthAllDates as $monthAllDate)
                                		<th class="text-left" style="width:85px; max-width:85px;min-width:85px;">{{ convertDateFormat( $monthAllDate , 'd.m.Y' ) }}</th>
                                	@endforeach
                                @endif
                                <th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.absent") }}</th>
                                <th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.half-leaves") }}</th>
                                <th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.total") }}</th>

                            </tr>
                        </thead>
                        <tbody class="pagination-view-html">
                        	@endif
                            @if(count($recordDetails) > 0 )
                        		@php $rowIndex = ($page_no - 1) * $perPageRecord;  @endphp
                        		@foreach($recordDetails as $recordDetail)
                        			@php $encodeEmployeeId = Wild_tiger::encode($recordDetail->i_id) ;@endphp
                        			 <tr class="text-left">
		                                <td class="text-center">{{ ++$rowIndex }}</td>
		                                <td class="employee-name-code-td"><a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}">{{ ( isset($recordDetail->v_employee_full_name)  ? $recordDetail->v_employee_full_name : '' ) }} ({{ ( isset($recordDetail->v_employee_code)  ? $recordDetail->v_employee_code : '' ) }})</a></td>
		                                <td>{{ ( isset($recordDetail->teamInfo->v_value)  ? $recordDetail->teamInfo->v_value : '' ) }}</td>
		                                 <?php
		                                 
		                                 $attendanceInfo = attendanceDayWiseReportInfo($recordDetail, $monthAllDates , $employeeWiseSuspendRecordDetails , $employeeWiseWeekOffDates ,  $monthHolidayDates  );
		                                
		                                 $dateWiseStatus = ( isset($attendanceInfo['dateWiseStatus']) ? $attendanceInfo['dateWiseStatus'] : 0 );
		                                 $absentCount = ( isset($attendanceInfo['absentCount']) ? $attendanceInfo['absentCount'] : 0 );
		                                 $halfLeaveCount = ( isset($attendanceInfo['halfLeaveCount']) ? $attendanceInfo['halfLeaveCount'] : 0 );
		                                 ?>
		                                 @if(count($monthAllDates) > 0 )
		                                	@foreach($monthAllDates as $monthAllDate)
		                                		<td class="text-left" >{{ ( isset($dateWiseStatus[$monthAllDate]) ? $dateWiseStatus[$monthAllDate] : '' )  }}</td>
		                                	@endforeach
		                                @endif
		                                <td class="text-left">{{ ( isset($absentCount) ? $absentCount : 0 )}}</td>
		                                <td class="text-left">{{ ( isset($halfLeaveCount) ? $halfLeaveCount : 0 )}}</td>
		                                <td class="text-left">{{ ( $halfLeaveCount +   $absentCount )}}</td>
									</tr>
                        		@endforeach
                        	@else
                        		<tr class="text-center">
                        			<td colspan="35">{{ trans('messages.no-record-found') }}</td>
                        		</tr>	
                        	@endif	
                           
                            <?php if( (isset($pagination)) && !empty($pagination) ){?>
				 				<input name="current_page" type="hidden" id="current_page" value="{{ ( isset($pagination['current_page']) ? $pagination['current_page'] : '' ) }}">
				 				<input name="last_page" type="hidden" id="last_page" value="{{ ( isset($pagination['last_page']) ? $pagination['last_page'] : '' ) }}">
				 	 			<input name="per_page" type="hidden" id="per_page" value="{{ ( isset($pagination['per_page']) ? $pagination['per_page'] : '' ) }}">
				 	 		<?php } ?>
                            @if( isset($requestPageNo) && ($requestPageNo == 1 ) )	
						</tbody>
                    </table>
                </div>
            </div>
            <script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
            @endif
            @include('admin/common-display-count')