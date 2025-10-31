                        	@if(count($recordDetails) > 0 )
                        		@php $recordIndex = 0;   @endphp
                        		@foreach($recordDetails as $recordDetail)
                        			@php
                        			$encodeEmployeeId = Wild_tiger::encode($recordDetail->i_employee_id);
                        			$attendanceSummaryRecordId = Wild_tiger::encode($recordDetail->i_id);
                        			$uniqueRecordId = $recordDetail->i_id;
                        			$generateSalaryInfo = ( ( isset($recordDetail->attendanceSalary) && (!empty($recordDetail->attendanceSalary)) ) ? $recordDetail->attendanceSalary : [] );
                        			$generateSalaryDetails = ( ( isset($recordDetail->attendanceSalary->generatedSalaryInfo) && (!empty($recordDetail->attendanceSalary->generatedSalaryInfo)) ) ? $recordDetail->attendanceSalary->generatedSalaryInfo : [] );
                        			
                        			$assignSalaryDetails = assignMonthWiseSalaryInfo( $recordDetail->i_employee_id , $recordDetail->dt_month );
                        			$employeeAllOnHoldSalaryDetails = ( isset($recordDetail->employeeAttendance->onHoldSalaryInfo) ? $recordDetail->employeeAttendance->onHoldSalaryInfo : [] ); 
                        			$allOnHoldSalaryMonths = (!empty($employeeAllOnHoldSalaryDetails) ? array_column(objectToArray($employeeAllOnHoldSalaryDetails),'dt_month') : [] );
                        			
                        			$totalOnHoldSalaryAmount = 0;
                        			if( isset($recordDetail->employeeAttendance->onHoldSalaryInfo) && (!empty($recordDetail->employeeAttendance->onHoldSalaryInfo)) ){
										foreach($recordDetail->employeeAttendance->onHoldSalaryInfo as $onHoldSalaryAmount){
											if ( isset($onHoldSalaryAmount->d_amount) && (!empty($onHoldSalaryAmount->d_amount)) ){
												if(  strtotime($onHoldSalaryAmount->dt_month) <= strtotime($recordDetail->dt_month) ){
													$totalOnHoldSalaryAmount +=  $onHoldSalaryAmount->d_amount;
												}
												
											}
										}
									}
									$decuctOnHoldSalaryAmount = 0;
									if( isset($recordDetail->employeeAttendance->generatedSalaryMaster) && (!empty($recordDetail->employeeAttendance->generatedSalaryMaster)) ){
										foreach( $recordDetail->employeeAttendance->generatedSalaryMaster  as $salaryMaster){
											if( $salaryMaster->t_is_salary_generated == 1  ){
												if( isset($salaryMaster->generatedSalaryInfo) && (!empty($salaryMaster->generatedSalaryInfo)) ){
													foreach( $salaryMaster->generatedSalaryInfo  as $generatedSalaryAmount ){
														if ( isset($generatedSalaryAmount->d_paid_amount) && (!empty($generatedSalaryAmount->d_paid_amount)) && ( $generatedSalaryAmount->i_component_id == config('constants.ON_HOLD_SALARY_COMPONENT_ID') ) ){
															$decuctOnHoldSalaryAmount +=  $generatedSalaryAmount->d_paid_amount;
														}
													}
												}
											}
										}
									}
                        			
                        			
                        			$onHoldSalaryAmount = "0";
                        			if( in_array( $recordDetail->dt_month ,  $allOnHoldSalaryMonths ) ){
                        				$searchOnHoldSalaryInfoKey = array_search($recordDetail->dt_month ,  $allOnHoldSalaryMonths);
                        				if( ( strlen($searchOnHoldSalaryInfoKey) > 0 ) && (isset($employeeAllOnHoldSalaryDetails[$searchOnHoldSalaryInfoKey]->d_amount)) && (!empty($employeeAllOnHoldSalaryDetails[$searchOnHoldSalaryInfoKey]->d_amount))  ){
                        					$onHoldSalaryAmount = $employeeAllOnHoldSalaryDetails[$searchOnHoldSalaryInfoKey]->d_amount;	
                        				}  
                        			}
                        			$onHoldSalaryAmount = ( ( ( $totalOnHoldSalaryAmount - $decuctOnHoldSalaryAmount ) > 0 ) ? ( $totalOnHoldSalaryAmount - $decuctOnHoldSalaryAmount ) : 0 );
                        			//var_dump($onHoldSalaryAmount);die;
                        			$generateSalaryComponentIds = (!empty($generateSalaryDetails) ? array_column(objectToArray($generateSalaryDetails),'i_component_id') : [] ); 
                        			$disabled = '';
                        			if( !empty($generateSalaryInfo) && ( $generateSalaryInfo->t_is_salary_generated == 1  ) ){
                        				$disabled = 'disabled';
                        			}
                        			$encodeId = Wild_tiger::encode($recordDetail->i_id);
                        			$allocatedComponentId =  ( ( isset($assignSalaryDetails->assignSalaryInfo) && (!empty($assignSalaryDetails->assignSalaryInfo)) ) ? array_column(objectToArray($assignSalaryDetails->assignSalaryInfo),'i_salary_component_id') : [] ); 
                        			
                        			$totalPresentDays = 0;
                        			$salary = ( ( isset($assignSalaryDetails) && (!empty($assignSalaryDetails->d_total_earning)) ) ? $assignSalaryDetails->d_total_earning : 0 );
                        			$fullDaySalary = (!empty($salary) ? round( ( $salary /  config('constants.SALARY_COUNT_DAYS') ) , 0  ) : 0 );
                        			$halfDaySalary = (!empty($fullDaySalary) ? round( ( $fullDaySalary /  2 ) , 0  ) : 0 );
                        			if(!empty($recordDetail->d_present_count)){
                        				$totalPresentDays += $recordDetail->d_present_count;
                        			}
                        			$disabledSalary = '';
                        			
                        			if( isset( $recordDetail->attendanceSalary->t_is_salary_generated ) && ( $recordDetail->attendanceSalary->t_is_salary_generated == 1 ) ){
                        				$disabledSalary = 'disabled';
                        			}
                        			$pfDeductionStatus = ( isset($assignSalaryDetails->e_pf_deduction) ? $assignSalaryDetails->e_pf_deduction : config('constants.SELECTION_NO') ); 	
	                        		if(!empty($generateSalaryInfo) && (isset($generateSalaryInfo->e_pf_deduction)) ){
	                        			$pfDeductionStatus = $generateSalaryInfo->e_pf_deduction;
	                        		}
	                        		//Log::info('total = '.$totalOnHoldSalaryAmount);
	                        		//Log::info('dedcuat = '.$decuctOnHoldSalaryAmount);
	                        		//Log::info('on hold = '.$onHoldSalaryAmount);
	                        		$paidHalfLeaveCount = ( isset($recordDetail->d_paid_half_leave_count) ? $recordDetail->d_paid_half_leave_count : 0 );
	                        		$paidFullLeaveCount = ( isset($recordDetail->d_paid_full_leave_count) ? $recordDetail->d_paid_full_leave_count : 0 );
	                        		$attendDayCount = ( isset($recordDetail->d_present_count) ? $recordDetail->d_present_count : 0 ) ;
	                        		if(!empty($paidFullLeaveCount)){
	                        			$attendDayCount = ( $attendDayCount - $paidFullLeaveCount );
	                        		}
	                        		if(!empty($paidHalfLeaveCount)){
	                        			$attendDayCount = ( $attendDayCount - $paidHalfLeaveCount );
	                        		}
	                        		@endphp
	                        		
                        			<tr class="text-left record-list" data-pf-deduction-status="{{ $pfDeductionStatus }}" >
		                                <td class="">
		                                    @if(empty($disabledSalary))
		                                    <div class="form-group mb-0 text-center">
		                                        <div class="checkbox-panel salary-cal-checkbox">
		                                 		 	<?php /* <input class="form-check-input salary-checkbox row-checkbox" type="checkbox" {{ $disabledSalary }} id="check_{{ $encodeId  }}" name="employee_selection[]" value="<?php echo $encodeId ?>">
		                                            <label class="form-check-label lable-control" for="check_{{ $encodeId  }}"></label> */ ?>
													<label for="check_{{ $encodeId  }}" class="lable-control d-block"></label>
													<div class="form-check form-check-inline mr-0">
														<label class="checkbox" for="check_{{ $encodeId  }}">
														<input type="checkbox" {{ $disabledSalary }} class="salary-checkbox row-checkbox"  id="check_{{ $encodeId  }}" name="employee_selection[]"  value="<?php echo $attendanceSummaryRecordId ?>"><span class="checkmark"></span></label>
													</div>
		                                        </div>
		                                    </div>
		                                    @else
		                                    	@if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) )
		                                    	<div class="d-flex align-items-center">
                    								<a href="javascript:void(0);" onclick="getEmployeeSalaryInfo(this);" data-ament-salary="{{ config('constants.SELECTION_YES') }}" data-emp-name="{{ ( isset($recordDetail->employeeAttendance->v_employee_full_name) ? $recordDetail->employeeAttendance->v_employee_full_name : '' ) }}" data-emp-code="{{ ( isset($recordDetail->employeeAttendance->v_employee_code) ? $recordDetail->employeeAttendance->v_employee_code : '' ) }}"  data-salary-month="{{ $recordDetail->dt_month }}" data-emp-id="{{ Wild_tiger::encode( $recordDetail->i_employee_id ) }}"  class="btn btn-sm text-white manage-doc-btn primary" title="{{ trans('messages.amend-salary') }}"><i class="fa fa-fw fa-edit"></i></a>
												</div>
												@endif
		                                    
		                                    
		                                    
		                                    @endif
		                                </td>
		                                <td class="text-center">{{ ++$recordIndex }}</td>
		                                <td>{{ (!empty($recordDetail->dt_month) ? date('m.Y', strtotime($recordDetail->dt_month) ) : '' ) }}</td>
		                                <td class="employee-name-code-td"><a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}" target="_blank" > {{ (!empty($recordDetail->employeeAttendance->v_employee_full_name) ? $recordDetail->employeeAttendance->v_employee_full_name : '' ) }} ({{ (!empty($recordDetail->employeeAttendance->v_employee_code) ? $recordDetail->employeeAttendance->v_employee_code : '' ) }})</a></td>
		                                <td>{{ (isset($recordDetail->employeeAttendance->teamInfo->v_value) ? $recordDetail->employeeAttendance->teamInfo->v_value : '' ) }}</td>
		                                <td class="text-left">{{ (isset($recordDetail->employeeAttendance->designationInfo->v_value) ? $recordDetail->employeeAttendance->designationInfo->v_value : '' ) }}</td>
		                                <td class="text-left">{{ (isset($recordDetail->employeeAttendance->bankInfo->v_value) ? $recordDetail->employeeAttendance->bankInfo->v_value : '' ) }}</td>
		                                <td class="text-left">{{ ( ( isset($recordDetail->employeeAttendance->bankInfo->i_id) && ( $recordDetail->employeeAttendance->bankInfo->i_id == config('constants.HDFC_BANK_ID') ) )  ? (  isset( $recordDetail->employeeAttendance->v_bank_account_no) ?  $recordDetail->employeeAttendance->v_bank_account_no : '-'  ) : '-' ) }}</td>
		                                
		                                @if( ( config('constants.ALLOWED_SALARY_PAID_CHANGE') == 1 ) && ( empty($disabledSalary ) ) && ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) )
		                                	<td class="text-left"><a href="javascript:void(0);" data-summary-record-id="{{ $attendanceSummaryRecordId }}" data-value="{{ $totalPresentDays  }}"  onclick="openUpdateSalaryDayCount(this)" data-emp-name="{{ ( isset($recordDetail->employeeAttendance->v_employee_full_name) ? $recordDetail->employeeAttendance->v_employee_full_name : '' ) }}" data-emp-code="{{ ( isset($recordDetail->employeeAttendance->v_employee_code) ? $recordDetail->employeeAttendance->v_employee_code : '' ) }}"  data-salary-month="{{ $recordDetail->dt_month }}" data-emp-id="{{ Wild_tiger::encode( $recordDetail->i_employee_id ) }}"  title="{{ trans('messages.view') }}">{{ config('constants.SALARY_COUNT_DAYS') }}</a></td>
		                                @else
		                                	<td class="text-left">{{ config('constants.SALARY_COUNT_DAYS') }}</td>
		                                @endif
		                                <td class="text-left">{{  ( $attendDayCount ) }}</td>
		                                <td class="text-left">{{  ( $paidHalfLeaveCount ) }}</td>
		                                <td class="text-left">{{  ( $paidFullLeaveCount ) }}</td>
		                                <td class="text-left"><a href="javascript:void(0);" data-summary-record-id="{{ $attendanceSummaryRecordId }}" data-value="{{ $totalPresentDays  }}"  onclick="getEmployeeSalaryInfo(this)" data-emp-name="{{ ( isset($recordDetail->employeeAttendance->v_employee_full_name) ? $recordDetail->employeeAttendance->v_employee_full_name : '' ) }}" data-emp-code="{{ ( isset($recordDetail->employeeAttendance->v_employee_code) ? $recordDetail->employeeAttendance->v_employee_code : '' ) }}"  data-salary-month="{{ $recordDetail->dt_month }}" data-emp-id="{{ Wild_tiger::encode( $recordDetail->i_employee_id ) }}"  title="{{ trans('messages.view') }}">{{ $totalPresentDays }}</a></td>
		                                <td class="text-left">{{ decimalAmount ( $salary ) }}</td>
		                                <td class="text-left">{{ decimalAmount ( $fullDaySalary ) }}</td>
		                                <td class="text-left">{{ decimalAmount( $halfDaySalary  )}}</td>
		                                <?php
		                                $earningHeadWithoutPFHtml = "";
		                                $totalEarningWithoutPfValue = 0;
		                               
		                                $earningHeadHtml = ""; $totalOriginalEarningValue = $totalOriginalDeductionValue = $totalEarningValue = $totalDeductionValue = $hraAmount = $dayWiseTotalEarning =  0;  
		                                ?>
		                                @if(count($earningComponentDetails) > 0 )
		                                	@foreach($earningComponentDetails as $earningComponentDetail)
		                                		<?php
		                                		$encodeId = Wild_tiger::encode($earningComponentDetail->i_id);
		                                		$assignedValue = config('constants.DEFAULT_SALARY_VALUE');
		                                		$enteredValue = config('constants.DEFAULT_SALARY_VALUE');
		                                		$dayWiseCalculateAmount  = config('constants.DEFAULT_SALARY_VALUE');
		                                		if(!empty($generateSalaryDetails)){
		                                			
		                                			$searchHeadKey = array_search($earningComponentDetail->i_id, $generateSalaryComponentIds);
		                                			if(strlen($searchHeadKey)  > 0 ){
		                                				$assignedValue = ( isset($recordDetail->attendanceSalary->generatedSalaryInfo[$searchHeadKey]->d_actual_amount) ? $recordDetail->attendanceSalary->generatedSalaryInfo[$searchHeadKey]->d_actual_amount : config('constants.DEFAULT_SALARY_VALUE') );
		                                				$enteredValue = ( isset($recordDetail->attendanceSalary->generatedSalaryInfo[$searchHeadKey]->d_paid_amount) ? $recordDetail->attendanceSalary->generatedSalaryInfo[$searchHeadKey]->d_paid_amount : config('constants.DEFAULT_SALARY_VALUE') );
		                                				if(!empty($assignedValue)){
		                                					$totalOriginalEarningValue += $assignedValue;
		                                				}
		                                				if(!empty($enteredValue)){
		                                					if( $earningComponentDetail->e_consider_for_pf_calculation == config('constants.SELECTION_YES') ){
		                                						$dayWiseTotalEarning += $enteredValue;
		                                						$totalEarningValue += $enteredValue;
		                                					} else {
		                                						$totalEarningWithoutPfValue += $enteredValue;
		                                					}
		                                					
		                                				}
		                                			}
		                                			$dayWiseCalculateAmount = $enteredValue;
		                                		} else {
		                                			$searchHeadKey = array_search($earningComponentDetail->i_id, $allocatedComponentId);
		                                			if(strlen($searchHeadKey)  > 0 ){
		                                				$assignedValue = ( isset($assignSalaryDetails->assignSalaryInfo[$searchHeadKey]->d_amount) ? $assignSalaryDetails->assignSalaryInfo[$searchHeadKey]->d_amount : config('constants.DEFAULT_SALARY_VALUE') );
		                                				$dayWiseCalculateAmount = $assignedValue;
		                                				if(!empty($assignedValue)){
		                                					$dayWiseCalculateAmount = dayWiseSalaryHeadAmount( $assignedValue , $totalPresentDays );
		                                					
		                                					
		                                					if( $earningComponentDetail->e_consider_for_pf_calculation == config('constants.SELECTION_YES') ){
		                                						$totalEarningValue += $assignedValue;
		                                						$dayWiseTotalEarning += (!empty($dayWiseCalculateAmount) ? $dayWiseCalculateAmount : 0 );
		                                					} else {
		                                						$totalEarningWithoutPfValue += $assignedValue;
		                                					}
		                                					
		                                					//$totalEarningValue += $assignedValue;
		                                					$totalOriginalEarningValue += $assignedValue;
		                                				}
		                                				$enteredValue = $assignedValue;
		                                			}
		                                		}
		                                		if( $earningComponentDetail->i_id == config('constants.HRA_SALARY_COMPONENT_ID') ){
		                                			$hraAmount = $dayWiseCalculateAmount;
		                                		}
		                                		
		                                		if(  isset($earningComponentDetail->e_consider_for_pf_calculation) && ( $earningComponentDetail->e_consider_for_pf_calculation == config('constants.SELECTION_YES') ) ){
		                                			$earningHeadHtml .= '<td class="text-left" '.$disabledSalary.' style="min-width:100px;"><input data-consider-pf-status="'.( isset($earningComponentDetail->e_consider_for_pf_calculation) ? $earningComponentDetail->e_consider_for_pf_calculation : config("constants.SELECTION_NO") ).'" data-original-salary="'.$assignedValue.'" type="text" data-component-id="'.$earningComponentDetail->i_id .'" value="'.$dayWiseCalculateAmount .'" class="form-control earning-head amount salary-value given-salary-amount" name="salary_amount_'.$uniqueRecordId.'_'.$earningComponentDetail->i_id.'" onkeyup="onlyNumber(this);calculateTotalSalary(this);" '.$disabled.' onchange="onlyDecimal(this);calculateTotalSalary(this);" placeholder="'.$earningComponentDetail['v_component_name'].'" ></td>';
		                                		} else {
		                                			$earningHeadWithoutPFHtml .= '<td class="text-left" '.$disabledSalary.' style="min-width:100px;"><input data-consider-pf-status="'.( isset($earningComponentDetail->e_consider_for_pf_calculation) ? $earningComponentDetail->e_consider_for_pf_calculation : config("constants.SELECTION_NO") ).'" data-original-salary="'.$assignedValue.'" type="text" data-component-id="'.$earningComponentDetail->i_id .'" value="'.$enteredValue .'" class="form-control earning-head amount salary-value given-salary-amount" name="salary_amount_'.$uniqueRecordId.'_'.$earningComponentDetail->i_id.'" onkeyup="onlyNumber(this);calculateTotalSalary(this);" '.$disabled.' onchange="onlyDecimal(this);calculateTotalSalary(this);" placeholder="'.$earningComponentDetail['v_component_name'].'" ></td>';
		                                			
		                                		}
		                                		?>
		                                		<td class="text-left salary-value earning-head" data-consider-pf-status="{{ ( isset($earningComponentDetail->e_consider_for_pf_calculation) ? $earningComponentDetail->e_consider_for_pf_calculation : config('constants.SELECTION_NO') ) }}"  data-component-id="{{ ( isset($earningComponentDetail->i_id) ? $earningComponentDetail->i_id : '' ) }}" style="min-width:90px;">{{  decimalAmount ( $assignedValue )  }}</td>
		                                	@endforeach
		                                	<td class="text-left" style="min-width:90px;">{{ decimalAmount ( $totalOriginalEarningValue ) }}</td>
		                                	<?php echo $earningHeadHtml ?>
		                                	<td class="text-left total-earning-amount" style="min-width:90px;">{{ decimalAmount ( $dayWiseTotalEarning ) }}</td>
		                                	<td class="text-left salary-after-remove-hra" style="min-width:90px;">{{ decimalAmount ( $dayWiseTotalEarning - (!empty($hraAmount) ? $hraAmount : 0 ) ) }}</td>
		                                @endif
		                                
		                                <?php $deductComponentHtml = ""; ?>
		                                @if(count($deductComponentDetails) > 0 )
		                                	@foreach($deductComponentDetails as $deductComponentDetail)
		                                		<?php 
		                                		//var_dump($generateSalaryDetails);die;
		                                		$disablePfSalary = "";
		                                		$disablePtSalary = "";
		                                		$pfDeductionClass = '';
		                                		$ptDeductionClass = '';
		                                		if( $deductComponentDetail->i_id == config('constants.PF_SALARY_COMPONENT_ID') ){
		                                			$pfDeductionClass = 'pf-salary-head';
		                                		}
		                                		
		                                		if( $deductComponentDetail->i_id == config('constants.PT_SALARY_COMPONENT_ID') ){
		                                			$disablePtSalary = "disabled";
		                                			$ptDeductionClass = 'pt-salary-head';
		                                		}
		                                		
		                                		$assignedValue = config('constants.DEFAULT_SALARY_VALUE');
		                                	
		                                		if(!empty($generateSalaryDetails)){
		                                			$searchHeadKey = array_search($deductComponentDetail->i_id, $generateSalaryComponentIds);
		                                			if(strlen($searchHeadKey)  > 0 ){
		                                				$assignedValue = ( isset($recordDetail->attendanceSalary->generatedSalaryInfo[$searchHeadKey]->d_actual_amount) ? $recordDetail->attendanceSalary->generatedSalaryInfo[$searchHeadKey]->d_actual_amount : config('constants.DEFAULT_SALARY_VALUE') );
		                                				$enteredValue = ( isset($recordDetail->attendanceSalary->generatedSalaryInfo[$searchHeadKey]->d_paid_amount) ? $recordDetail->attendanceSalary->generatedSalaryInfo[$searchHeadKey]->d_paid_amount : config('constants.DEFAULT_SALARY_VALUE') );
		                                				/* if(!empty($assignedValue)){
		                                					$totalOriginalDeductionValue += $assignedValue;
		                                				} */
		                                				
		                                				/* if( in_array( $deductComponentDetail->i_id , [ config('constants.ON_HOLD_SALARY_COMPONENT_ID') ] ) ){
		                                					$assignedValue = $onHoldSalaryAmount;
		                                				} */
		                                				
		                                				
		                                				
		                                				if( $deductComponentDetail->i_id == config('constants.PF_SALARY_COMPONENT_ID') ){
		                                					if( $pfDeductionStatus == config('constants.SELECTION_NO') ){
		                                						$disablePfSalary = 'disabled';
		                                						$assignedValue = 0;
		                                					} else {
		                                						$assignedValue = $enteredValue;
		                                					}
		                                				} else {
		                                					$assignedValue = $enteredValue;
		                                				}
		                                				
		                                				if(!empty($assignedValue)){
		                                					$totalDeductionValue += $assignedValue;
		                                				}
		                                			} else {
		                                				if( $deductComponentDetail->i_id == config('constants.PF_SALARY_COMPONENT_ID') ){
		                                					 
		                                					if( $pfDeductionStatus == config('constants.SELECTION_NO') ){
		                                						$disablePfSalary = 'disabled';
		                                						$assignedValue = 0;
		                                					} else {
		                                						$assignedValue = getPFValue($assignSalaryDetails,$totalPresentDays);
		                                					}
		                                					if(!empty($assignedValue)){
		                                						$totalDeductionValue += $assignedValue;
		                                					}
		                                				}
		                                			}
		                                			
		                                		} else {
		                                			//echo "<pre>";print_r($allocatedComponentId);die;
		                                			$searchHeadKey = array_search($deductComponentDetail->i_id, $allocatedComponentId);
		                                			if(strlen($searchHeadKey)  > 0 ){
		                                				$assignedValue = ( isset($recordDetail->employeeAttendance->salaryDetail[$searchHeadKey]->d_amount) ? $recordDetail->employeeAttendance->salaryDetail[$searchHeadKey]->d_amount : config('constants.DEFAULT_SALARY_VALUE') );
		                                				
		                                				if( in_array( $deductComponentDetail->i_id , [ config('constants.ON_HOLD_SALARY_COMPONENT_ID') ] ) ){
		                                					$assignedValue = $onHoldSalaryAmount;
		                                				}
		                                				
		                                				if( $deductComponentDetail->i_id == config('constants.PT_SALARY_COMPONENT_ID') ){
		                                					if( $dayWiseTotalEarning >= config('constants.PT_AMOUNT_LIMIT') ){
		                                						$assignedValue = config('constants.PT_AMOUNT') ; 
		                                					} else {
		                                						$assignedValue = 0;
		                                					}
		                                				}
		                                				
		                                				if( $deductComponentDetail->i_id == config('constants.PF_SALARY_COMPONENT_ID') ){
		                                					if( $pfDeductionStatus == config('constants.SELECTION_NO') ){
		                                						$disablePfSalary = 'disabled';
		                                						$assignedValue = 0;
		                                					} else {
		                                						$assignedValue = getPFValue($assignSalaryDetails,$totalPresentDays);
		                                					}
		                                				}
		                                				
		                                				if(!empty($assignedValue)){
		                                					$totalDeductionValue += $assignedValue;
		                                				}
		                                			} else {
		                                				
		                                				if( in_array( $deductComponentDetail->i_id , [ config('constants.PT_SALARY_COMPONENT_ID') ] ) ){
		                                					if( $dayWiseTotalEarning >= config('constants.PT_AMOUNT_LIMIT') ){
		                                						$assignedValue = config('constants.PT_AMOUNT') ;
		                                					} else {
		                                						$assignedValue = 0;
		                                					}
		                                				}
		                                				
		                                				if( in_array( $deductComponentDetail->i_id , [ config('constants.ON_HOLD_SALARY_COMPONENT_ID') ] ) ){
		                                					$assignedValue = $onHoldSalaryAmount;
		                                				}
		                                				if( $deductComponentDetail->i_id == config('constants.PF_SALARY_COMPONENT_ID') ){
		                                					//var_dump($deductComponentDetail->i_id);echo "<br><br><br>";
		                                					if( $pfDeductionStatus == config('constants.SELECTION_NO') ){
		                                						$disablePfSalary = 'disabled';
		                                						$assignedValue = 0;
		                                					} else {
		                                						$assignedValue = getPFValue($assignSalaryDetails,$totalPresentDays);
		                                					}
		                                				}
		                                				if(!empty($assignedValue)){
		                                					$totalDeductionValue += $assignedValue;
		                                				}
		                                			}
		                                		}
		                                		$disableOnHoldSalary = '';
		                                		if( ( $recordDetail->employeeAttendance->e_hold_salary_payment_status != config( config('constants.PENDING_STATUS')) ) && ( $deductComponentDetail->i_id == config('constants.ON_HOLD_SALARY_COMPONENT_ID')) ){
		                                			//$disableOnHoldSalary = 'disabled';
		                                		}
		                                		?>
		                                		<td class="text-left"   style="min-width:100px;"><input  {{ $disablePtSalary }}  data-consider-pf-status="{{ ( isset($earningComponentDetail->e_consider_for_pf_calculation) ? $earningComponentDetail->e_consider_for_pf_calculation : config('constants.SELECTION_NO') ) }}"  {{ $disabledSalary }} {{ $disablePfSalary }}  {{ $disableOnHoldSalary }} type="text" data-component-id="{{ ( isset($deductComponentDetail->i_id) ? $deductComponentDetail->i_id : '' ) }}" data-original-salary="{{ $assignedValue }}" {{ $disabled }} data-component-id="{{ $deductComponentDetail->i_id }}"  value="{{ $assignedValue }}" class="form-control deduct-head amount salary-value {{ $pfDeductionClass }} {{ $ptDeductionClass }}" name="salary_amount_<?php echo $uniqueRecordId ?>_<?php echo $deductComponentDetail->i_id ?>" onkeyup="onlyNumber(this);calculateTotalSalary(this);" onchange="onlyNumber(this);calculateTotalSalary(this);" placeholder="{{ ( isset($deductComponentDetail['v_component_name']) ? $deductComponentDetail['v_component_name'] : '' ) }}" ></td>
		                                	@endforeach
		                                	
		                                	<td class="text-left total-deduct-amount" style="min-width:90px;">{{ decimalAmount ( $totalDeductionValue ) }}</td>
		                                @endif
		                               	@php
		                               		if(!empty($generateSalaryDetails)){
		                               			$totalPayAmount =  ( $totalEarningValue - $totalDeductionValue );
		                               			$netPayAmount =  ( $totalEarningValue - $totalDeductionValue + $totalEarningWithoutPfValue );
		                               		}  else {
		                               			$totalPayAmount =  ( ( ( $dayWiseTotalEarning - $totalDeductionValue ) > 0 ) ? ( $dayWiseTotalEarning - $totalDeductionValue ) : 0 ) ;
		                               			
		                               			$netPayAmount =  ( ( ( $dayWiseTotalEarning - $totalDeductionValue + $totalEarningWithoutPfValue ) > 0 ) ? ( $dayWiseTotalEarning - $totalDeductionValue + $totalEarningWithoutPfValue ) : 0 ) ;
		                               		}
		                               	
		                               	@endphp
		                               	<td class="text-left total-pay-amount">{{ (!empty($totalPayAmount) ?  decimalAmount($totalPayAmount) : 0 )  }}</td>
		                               	<?php echo $earningHeadWithoutPFHtml ?>
		                                <td class="text-left net-amount">{{ (!empty($netPayAmount) ?  decimalAmount($netPayAmount) : 0 )  }}</td>
		                            </tr>
		                           
                        		@endforeach
                        	@else
                        		<tr class="text-center">
                        			<td colspan="70">{{ trans('messages.no-record-found') }}</td>
                        		</tr>
                        	@endif
                        	@include('admin/common-display-count')