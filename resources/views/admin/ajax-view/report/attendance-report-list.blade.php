					@if(count($recordDetails) > 0)
					@php
						$index = ($pageNo - 1) * $perPageRecord;
					@endphp
						@foreach($recordDetails as $recordDetail)
							@php
							$totalHours = "";
							if( (!empty($recordDetail->t_start_time)) && (!empty($recordDetail->t_end_time)) && ( $recordDetail->t_start_time != config('constants.TIME_DEFAULT_VALUE') ) && ( $recordDetail->t_end_time != config('constants.TIME_DEFAULT_VALUE')  ) ){
								$totalHours = diffBetweenTime(  $recordDetail->t_start_time , $recordDetail->t_end_time );
							}
							$arrivalDepartureInfo = displayOnTime($recordDetail);
							
							$arrivalText = ( isset($arrivalDepartureInfo['arrivalInfo']) ? $arrivalDepartureInfo['arrivalInfo'] : '' ) ;
							$departureText = ( isset($arrivalDepartureInfo['departureInfo']) ? $arrivalDepartureInfo['departureInfo'] : '' )  ;
							$breakTime = ( ( (!empty($recordDetail->t_total_break_time)) && ( $recordDetail->t_total_break_time != config('constants.TIME_DEFAULT_VALUE')  ) ) ? convertSecondIntoHourMinute( strtotime($recordDetail->t_total_break_time) - strtotime('TODAY') ) : '');
							$workingHours = '';
							if( (!empty($recordDetail->t_start_time)) && (!empty($recordDetail->t_end_time) ) ){
								$workingHours = (!empty(workingHoursByTotalAndBreakTime($recordDetail)) ? workingHoursByTotalAndBreakTime($recordDetail) : '');
							}
							$encodeEmployeeId = Wild_tiger::encode($recordDetail->i_employee_id);
							$onTimeTextClass = (!empty($onTimeText) ? 'twt-v-top' : '' );
							@endphp
							<tr class="text-left">
                                <td class="text-center">{{ ++$index }}</td>
                                <td>{{ (!empty($recordDetail->dt_date) ? date('d.m.Y' , strtotime($recordDetail->dt_date)) : '') }} <br> {{ (!empty($recordDetail->dt_date) ? date('l' , strtotime($recordDetail->dt_date)) : '') }}</td>
                                <td>
                                @if( ( session()->get('is_supervisor') == false ) && ( $recordDetail->i_employee_id == session()->get('user_employee_id')) )
                                	{{ (isset($recordDetail->v_employee_full_name) ? $recordDetail->v_employee_full_name : '') }} ({{ (isset($recordDetail->v_employee_code) ? $recordDetail->v_employee_code : '') }})
                                @else
                                	<a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}" target="_blank" > {{ (isset($recordDetail->v_employee_full_name) ? $recordDetail->v_employee_full_name : '') }} ({{ (isset($recordDetail->v_employee_code) ? $recordDetail->v_employee_code : '') }})</a>
                                @endif
                                
                                </td>
                                <td>{{ (isset($recordDetail->team) ? $recordDetail->team : '') }}</td>
                                <td>{!! (!empty($recordDetail->t_original_start_time) && ( $recordDetail->t_original_start_time != config('constants.TIME_DEFAULT_VALUE')  ) ? clientTime ( $recordDetail->t_original_start_time )  . (!empty($recordDetail->t_original_end_time) && ( $recordDetail->t_original_end_time != config('constants.TIME_DEFAULT_VALUE')  ) ? ' - ' . clientTime ( $recordDetail->t_original_end_time )    : '')     : '') !!}</td>
                                
                                <td>{!! (!empty($recordDetail->t_start_time) && ( $recordDetail->t_start_time != config('constants.TIME_DEFAULT_VALUE')  ) ? clientTime ( $recordDetail->t_start_time ) . (!empty($arrivalText) ? '<br>' .$arrivalText : ''  )     : '') !!}</td>
                                <td class="text-left {{ $onTimeTextClass }}">{!! ( ( (!empty($recordDetail->t_end_time)) && ( $recordDetail->t_end_time != config('constants.TIME_DEFAULT_VALUE') ) ) ? clientTime( $recordDetail->t_end_time ) . '<br>' . $departureText  : '' ) !!}</td>
                                <td class="text-left">{{ $totalHours }}</td>
                                <td class="text-left">{{ $breakTime }}</td>
                                <td class="text-left">{{ $workingHours }}</td>
                                <td>{{ (isset($recordDetail->e_status) ? $recordDetail->e_status : '') }}</td>
                            </tr>
						@endforeach
						@if(!empty($pagination))
				 		<input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
				 		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
				 		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
				 		@endif
					@else
				 	<tr>
						<td colspan="11" class="text-center">@lang('messages.no-record-found')</td>
					</tr>
					@endif
					@include('admin/common-display-count')