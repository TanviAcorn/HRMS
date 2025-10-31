			@if( isset($requestPageNo) && ($requestPageNo == 1 ) )
			<div class="card card-body">
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered text-left statutaroy-table">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left employee-name-code-th" style="width:250px;min-width:250px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="width:140px;min-width:140px;">{{ trans("messages.team") }}</th>
                                @if(count($allMonths) > 0 )
                                	@foreach($allMonths as $allMonth)
                                		<th class="text-center p-0" style="width:170px; max-width:170px;min-width:170px;">{{ convertDateFormat($allMonth , 'M-Y') }}
		                                    <table class="border-0 w-100">
		                                        <tr class="border-0">
		                                            <th class="present-th">{{ trans('messages.present') }}</th>
		                                            <th class="basic-th">{{ trans('messages.basic') }}</th>
		                                        </tr>
		                                    </table>
		                                </th>
                                	@endforeach
                                @endif
								<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.total-present") }}</th>
                                <th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.total-salary") }}</th>

                            </tr>
                        </thead>
                        <tbody class="pagination-view-html">
                        	@endif
                        	@if(count($recordDetails) > 0 )
                        		@php $rowIndex = ($page_no - 1) * $perPageRecord;  @endphp
                        		@foreach($recordDetails as $recordDetail)
                        			@php $encodeEmployeeId = Wild_tiger::encode($recordDetail['i_id']) ; @endphp
                        			<tr>
                        				<td class="text-center" >{{ ++$rowIndex  }}</td>
                        				<td class="employee-name-code-td"><a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}">{{ ( isset($recordDetail['v_employee_full_name']) ? $recordDetail['v_employee_full_name'] : '' )  }} ({{ ( isset($recordDetail['v_employee_code']) ? $recordDetail['v_employee_code'] : '' )  }})</a></td>
                        				<td>{{ ( isset($recordDetail['team']) ? $recordDetail['team'] : '' )  }}</td>
                        				@if(count($allMonths) > 0 )
		                                	@foreach($allMonths as $allMonth)
		                                		<td style="padding: 0;" colspan="1">
				                                    <table class="w-100">
				                                        <tr>
				                                            <td class="w-50 text-center border-0">{{ ( isset($recordDetail[$allMonth.'_present_day']) ? $recordDetail[$allMonth.'_present_day'] : '' )  }}</td>
				                                            <td class="w-50 text-center" style="border: none;border-left: 1px solid #ddd;">{{ ( isset($recordDetail[$allMonth.'_basic_salary']) ? decimalAmount ( $recordDetail[$allMonth.'_basic_salary'] ) : '' ) }}</td>
				                                        </tr>
				                                    </table>
				                                </td>
		                                	@endforeach
		                                @endif
		                                <td>{{ ( isset($recordDetail['d_total_present_days']) ? decimalAmount( $recordDetail['d_total_present_days']) : '' )  }}</td>
                               	 		<td>{{ ( isset($recordDetail['d_total_basic_salary']) ? decimalAmount( $recordDetail['d_total_basic_salary']) : '' )  }}</td>										
									</tr>
                        		@endforeach
                        	@else
                        		<tr class="text-center">
                        			<td colspan="30">{{ trans('messages.no-record-found') }}</td>
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