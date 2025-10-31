		@if( isset($requestPageNo) && ($requestPageNo == 1 ) )
		<div class="card card-body">
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col" style="min-width:50px;width:50px">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="width:140px;min-width:140px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="width:92px;min-width:92px;">{{ trans("messages.joining-date") }}</th>
                               	@if(count($allMonths) > 0 )
                        			@foreach($allMonths as $allMonth)
                        				<th class="text-left" style="min-width:80px;">{{ (  $allMonth ?  leaveMonthReportValue ( $allMonth ) : '' )  }}</th>
                        			@endforeach
								@endif
                                
                                <th class="text-left" style="width:85px;min-width:85px;">{{ trans("messages.total") }}</th>
                                <th class="text-left" style="width:85px;min-width:85px;">{{ trans("messages.saved-leaves") }}</th>
                            </tr>
                        </thead>
                        <tbody class="pagination-view-html">
                        @endif
                        	@if(count($recordDetails) > 0 )
                        		@php 
                        		$rowIndex = ($page_no - 1) * $perPageRecord; 
                        		@endphp
                        		@foreach($recordDetails as $recordDetail)
                        			<tr>
                        				<td class="text-center">{{ ++$rowIndex  }}</td>
                        				<td>
                        					<a href="{{ route('employee-master.profile', (!empty($recordDetail['i_employee_id']) ? Wild_tiger::encode($recordDetail['i_employee_id']) :0  ) ) }}" target="_blank" title="{{ trans('messages.view-profile')}}">{{ (!empty($recordDetail['v_employee_full_name']) ? $recordDetail['v_employee_full_name'] .(!empty($recordDetail['v_employee_code']) ? ' (' .$recordDetail['v_employee_code'] .')' :'' ): '') }}</a>
                        				</td>
                        				<td>{{ ( isset($recordDetail['v_designation_name']) ? $recordDetail['v_designation_name'] : '' )  }}</td>
                        				<td>{{ ( isset($recordDetail['dt_joining_date']) ? $recordDetail['dt_joining_date'] : '' )  }}</td>
                        				@if(count($allMonths) > 0 )
                        					@foreach($allMonths as $allMonth)
                        						<td>{{ ( isset($recordDetail[$allMonth]) ? $recordDetail[$allMonth] : '' )  }}</td>
                        					@endforeach
                        				@endif
                        				<td>{{ ( isset($recordDetail['d_total_leave']) ? $recordDetail['d_total_leave'] : '' )  }}</td>
                        				<td>{{ ( isset($recordDetail['d_save_leave']) ? $recordDetail['d_save_leave'] : '' )  }}</td>
                        			</tr>
                        		@endforeach
                        	@else
                        		<tr class="text-center">
                        			<td colspan="20">{{ trans('messages.no-record-found') }}</td>
                        		</tr>	
                        	@endif
                        	@if( isset($requestPageNo) && ($requestPageNo == 1 ) )
                        </tbody>
                    </table>
                </div>
            </div>
            <script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
            @endif
            <?php if( (isset($pagination)) && !empty($pagination) ){?>
 				<input name="current_page" type="hidden" id="current_page" value="{{ ( isset($pagination['current_page']) ? $pagination['current_page'] : '' ) }}">
 				<input name="last_page" type="hidden" id="last_page" value="{{ ( isset($pagination['last_page']) ? $pagination['last_page'] : '' ) }}">
 	 			<input name="per_page" type="hidden" id="per_page" value="{{ ( isset($pagination['per_page']) ? $pagination['per_page'] : '' ) }}">
 	 		<?php } ?>
 	 		 @include('admin/common-display-count')