		@if( isset($requestPageNo) && ($requestPageNo == 1 ) )
		<div class="card card-body">
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left employee-name-code-th" style="width:165px;min-width:165px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.designation") }}</th>
                                <th class="text-left" style="width:120px;min-width:130px;">{{ trans("messages.joining-date") }}</th>
                                @if(  isset($incrementHeaders) && ( count($incrementHeaders) > 0 )  )
                                	@foreach($incrementHeaders as $incrementHeader)
                                		<th class="text-left" style="min-width:138px;">{{ convertDateFormat( $incrementHeader , 'F-Y') }}</th>	
                                	@endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody class="pagination-view-html">
                        	@endif
                        	@if(count($recordDetails) > 0 )
                        		@php $rowIndex = ($pageNo - 1) * $perPageRecord;  @endphp
                        		@foreach($recordDetails as $recordDetail)
                        			@php
                        			$encodeEmployeeId = Wild_tiger::encode($recordDetail->i_id); 
                        			$salaryIncrementDetails = salaryIncrementReportInfo($incrementHeaders , $recordDetail); 
                        			$salaryIncrementInfo = ( isset($salaryIncrementDetails['display']) ? $salaryIncrementDetails['display'] : [] );
                        			@endphp
                        			<tr class="text-left">
		                                <td class="text-center">{{ ++$rowIndex }}</td>
		                                <td class="employee-name-code-td"><a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}" target="_blank"> {{ ( isset($recordDetail->v_employee_full_name) ? $recordDetail->v_employee_full_name : '' )   }} ({{ ( isset($recordDetail->v_employee_code) ? $recordDetail->v_employee_code : '' )   }})</a></td>
		                                <td>{{ ( isset($recordDetail->teamInfo->v_value) ? $recordDetail->teamInfo->v_value : '' )   }}</td>
		                                <td class="text-left">{{ ( isset($recordDetail->designationInfo->v_value) ? $recordDetail->designationInfo->v_value : '' )   }}</td>
		                                <td class="text-left">{{ ( isset($recordDetail->dt_joining_date) ? convertDateFormat($recordDetail->dt_joining_date , 'd.m.Y')  : '' )   }}</td>
		                                @if(  isset($incrementHeaders) && ( count($incrementHeaders) > 0 )  )
		                                	@foreach($incrementHeaders as $incrementHeader)
		                                		<td class="text-left">{{ (isset($salaryIncrementInfo[$incrementHeader]) ? decimalAmount($salaryIncrementInfo[$incrementHeader]) : '' ) }}</td>	
		                                	@endforeach
		                                @endif
		                            </tr>
                        		@endforeach
                        	@else
                        		<tr class="text-center">
                        			<td colspan="15">{{ trans('messages.no-record-found') }}</td>
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
							
							