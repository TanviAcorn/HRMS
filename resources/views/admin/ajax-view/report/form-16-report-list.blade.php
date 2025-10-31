		@if( isset($requestPageNo) && ($requestPageNo == 1 ) )
		
		<div class="card card-body">
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left employee-name-code-th" style="width:200px;min-width:200px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="width:140px;min-width:140px;">{{ trans("messages.designation") }}</th>
                                <th class="text-left" style="width:140px;min-width:140px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.joining-date") }}</th>
                                <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.pan") }}</th>
                                <th class="text-left" style="width:180px;min-width:180px;">{{ trans("messages.duration") }}</th>
                               	@if(count($earningComponentDetails) > 0 )
                        			@foreach($earningComponentDetails as $earningComponentDetail)
                        				<th class="text-left" style="min-width:105px;">{{ (  isset($earningComponentDetail->v_component_name) ?  $earningComponentDetail->v_component_name : '' )  }}</th>
                        			@endforeach
								@endif
								<th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.total-earnings") }}</th>
								@if(count($deductComponentDetails) > 0 )
                        			@foreach($deductComponentDetails as $deductComponentDetail)
                        				<th class="text-left" style="min-width:105px;">{{ (  isset($deductComponentDetail->v_component_name) ?  $deductComponentDetail->v_component_name : '' )  }}</th>
                        			@endforeach
								@endif
								<th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.total-deductions") }}</th>
                                <th class="text-left" style="width:105px;min-width:105px;">{{ trans("messages.total-net-pay") }}</th>
                            </tr>
                        </thead>
                        <tbody class="pagination-view-html">
                        	@endif
                        	@if(count($recordDetails) > 0 )
                        		@php $rowIndex = ($page_no - 1) * $perPageRecord;  @endphp
                        		@foreach($recordDetails as $recordDetail)
                        			@php $encodeEmployeeId = Wild_tiger::encode($recordDetail['i_id']); @endphp
                        			
                        			<tr>
                        				<td class="text-center">{{ ++$rowIndex  }}</td>
                        				<td class="employee-name-code-td"><a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}" target="_blank">{{ ( isset($recordDetail['v_employee_full_name']) ? $recordDetail['v_employee_full_name'] : '' )  }}</a></td>
                        				<td>{{ ( isset($recordDetail['v_designation_name']) ? $recordDetail['v_designation_name'] : '' )  }}</td>
                        				<td>{{ ( isset($recordDetail['v_team_name']) ? $recordDetail['v_team_name'] : '' )  }}</td>
                        				<td>{{ ( isset($recordDetail['dt_joining_date']) ? $recordDetail['dt_joining_date'] : '' )  }}</td>
                        				<td>{{ ( isset($recordDetail['v_pan_no']) ? $recordDetail['v_pan_no'] : '' )  }}</td>
										<td style="width:180px;min-width:180px;">{{ ( isset($recordDetail['v_duration']) ? $recordDetail['v_duration'] : '' )  }}</td>										
										@if(count($earningComponentDetails) > 0 )
		                        			@foreach($earningComponentDetails as $earningComponentDetail)
		                        				<td class="text-left" style="min-width:105px;">{{ (  isset( $recordDetail['salary_'.$earningComponentDetail->i_id]) ?  decimalAmount($recordDetail['salary_'.$earningComponentDetail->i_id]) : '' )  }}</td>
		                        			@endforeach
										@endif
										
										<td class="text-left" style="width:100px;min-width:100px;">{{ ( isset($recordDetail['d_total_earning']) ? decimalAmount( $recordDetail['d_total_earning']) : '' )  }}</td>
										
										@if(count($deductComponentDetails) > 0 )
		                        			@foreach($deductComponentDetails as $deductComponentDetail)
		                        				<td class="text-left" style="min-width:105px;">{{ (  isset( $recordDetail['salary_'.$deductComponentDetail->i_id]) ?  decimalAmount($recordDetail['salary_'.$deductComponentDetail->i_id]) : '' )  }}</td>
		                        			@endforeach
										@endif
                        				
                        				<td class="text-left" style="width:100px;min-width:100px;">{{ ( isset($recordDetail['d_total_deduct']) ? decimalAmount( $recordDetail['d_total_deduct']) : '' )  }}</td>
                        				<td class="text-left" style="width:100px;min-width:100px;">{{ ( isset($recordDetail['d_total_net_pay']) ? decimalAmount( $recordDetail['d_total_net_pay']) : '' )  }}</td>
                        			</tr>
                        		@endforeach
                        	@else
                        		<tr class="text-center">
                        			<td colspan="50">{{ trans('messages.no-record-found') }}</td>
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