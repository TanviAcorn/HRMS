				<div class="row">
                     <div class="col-lg-4 salary-details-div">
                         <div class="border-left-10">
                             <h5 class="header-title font-weight-bold ml-2" id="exampleModalLabel">{{ trans('messages.days-paid') }} : <?php echo  ( isset($presentDayCount) ? ($presentDayCount) : 0 ) ?></h5>
                         </div>
                         <div class="px-sm-2 pt-3">
                             <div id="employee-salary-leave-info"></div>
                         </div>
                         <?php /* ?>
                         <div class="d-flex align-items-center py-3 pl-2">
                             <h5 class="details-title h4 mb-0">{{ trans("messages.present-day") }}</h5>
                             <p class="h6 ml-2 mb-0 salary-preset-days">{{ ( isset($getPresentDates) ? count($getPresentDates) : 0 ) }}</p>
                         </div>
                         <?php */ ?>
                         <div class="row">
                             <div class="col-12 leave-calendar">
                                 <div class="leave-calendar-type pl-0">
                                     <div class="weekoff"><i class="fas fa-circle calendar-type-icon text-success present"></i> {{ trans("messages.present") }}</div>
                                     <div class="weekoff"><i class="fas fa-circle calendar-type-icon text-success"></i> {{ trans("messages.weekoff") }}</div>
                                     <div class="leave"><i class="fas fa-circle calendar-type-icon absent"></i>{{ trans("messages.absent") }}</div>
                                     <div class="holiday"><i class="fas fa-circle calendar-type-icon holiday-color"></i>{{ trans("messages.holiday") }}</div>
                                     <div class="holiday"><i class="fas fa-circle calendar-type-icon adjustment"></i>{{ trans("messages.adjustment") }}</div>
                                     <div class="holiday"><i class="fas fa-circle calendar-type-icon half-leaves"></i>{{ trans("messages.half-leave") }}</div>
                                     <div class="holiday"><i class="fas fa-circle calendar-type-icon suspend-color"></i>{{ trans("messages.suspend") }}</div>
                                     <div class="holiday"><i class="fas fa-circle calendar-type-icon approved-leave-color"></i>{{ trans("messages.approved-leave") }}</div>
                                	 <div class="holiday"><i class="fas fa-circle calendar-type-icon approved-half-leave-color"></i>{{ trans("messages.approved-half-leave") }}</div>
                                	 <div class="holiday"><i class="fas fa-circle calendar-type-icon unpaid-half-leave-color"></i>{{ trans("messages.unpaid-half-leave") }}</div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <?php 
                     $earningHtml = $deductHtml =  $earningEditHtml = $deductEditHtml = "";
                     $amendmentEarningHtml = $amendmentDeductHtml = ""; 
                     $totalEarning = $totalDeduction = 0;
                     $earningHead = $deductHead =  [];
                     if($salaryGenerated != false ){
                     	if(!empty($generatedSalaryDetails->generatedSalaryInfo)){
                     		$generatedSalaryDetails->generatedSalaryInfo = $generatedSalaryDetails->generatedSalaryInfo->sortBy('generateSalaryComponent.i_sequence');
                     		foreach($generatedSalaryDetails->generatedSalaryInfo as $salaryDetail){
                     			switch($salaryDetail->generateSalaryComponent->e_salary_components_type){
                     				case config('constants.SALARY_COMPONENT_TYPE_EARNING'):
                     					$earningHead[] = $salaryDetail;
                     					$eamount = ( isset($salaryDetail->d_paid_amount) ? $salaryDetail->d_paid_amount : 0 );
                     					$earningHtml .= '<tr>';
                     					$earningHtml .= '<td>'.( isset($salaryDetail->generateSalaryComponent->v_component_name) ? $salaryDetail->generateSalaryComponent->v_component_name : ''  ).'</td>';
                     					$earningHtml .= '<td>'.( isset($eamount) ? decimalAmount($eamount) : ''  ).'</td>';
                     					$earningHtml .= '</tr>';
                     	
                     					if( isset($eamount) && (!empty($eamount)) ){
                     						//var_dump($eamount);die;
                     						$totalEarning += $eamount;
                     					}
                     					
                     					if($allowEditSalary != false ){
                     						$earningEditHtml .= '<tr>';
                     						$earningEditHtml .= '<td>'.( isset($salaryDetail->generateSalaryComponent->v_component_name) ? $salaryDetail->generateSalaryComponent->v_component_name : ''  ).'</td>';
                     						$earningEditHtml .= '<td><input type="text" data-component-id="'.$salaryDetail->generateSalaryComponent->i_id .'" class="form-control earning-head amount"  onkeyup="onlyNumber(this);calculateModalTotalSalary(this);" onchange="onlyNumber(this);calculateModalTotalSalary(this);" value="'.( isset($eamount) ? ($eamount) : ''  ).'" ></td>';
                     						$earningEditHtml .= '</tr>';
                     					}
                     					
                     					if( isset($allowedAmendment) && ($allowedAmendment != false )  ) {
                     						$amendmentEarningHtml .= '<tr>';
                     						$amendmentEarningHtml .= '<td>'.( isset($salaryDetail->generateSalaryComponent->v_component_name) ? $salaryDetail->generateSalaryComponent->v_component_name : ''  ).'</td>';
                     						$amendmentEarningHtml .= '<td><input type="text" name="salary_info_'.$salaryDetail->i_id.'" data-component-id="'.$salaryDetail->generateSalaryComponent->i_id .'" class="form-control earning-head amount"  onkeyup="onlyNumber(this);calculateModalTotalSalary(this);" onchange="onlyNumber(this);calculateModalTotalSalary(this);" value="'.( isset($eamount) ? ($eamount) : ''  ).'" ></td>';
                     						$amendmentEarningHtml .= '</tr>';
                     					}
                     					
                     					break;
                     				case config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'):
                     					$deductHead[] = $salaryDetail;
                     					$damount = ( isset($salaryDetail->d_paid_amount) ? $salaryDetail->d_paid_amount : 0 );
                     					$deductHtml .= '<tr>';
                     					$deductHtml .= '<td>'.( isset($salaryDetail->generateSalaryComponent->v_component_name) ? $salaryDetail->generateSalaryComponent->v_component_name : ''  ).'</td>';
                     					$deductHtml .= '<td>'.( isset($damount) ? decimalAmount($damount) : ''  ).'</td>';
                     					$deductHtml .= '</tr>';
                     					if( isset($damount) && (!empty($damount)) ){
                     						$totalDeduction += $damount;
                     					}
                     					$disableOnHoldSalary = '';
                     					if( ( $generatedSalaryDetails->employee->e_hold_salary_payment_status != config( config('constants.PENDING_STATUS')) ) && ( $salaryDetail->generateSalaryComponent->i_id == config('constants.ON_HOLD_SALARY_COMPONENT_ID')) ){
                     						//$disableOnHoldSalary = 'disabled';
                     					}
                     					
                     					if($allowEditSalary != false ){
                     						
                     						$deductEditHtml .= '<tr>';
                     						$deductEditHtml .= '<td>'.( isset($salaryDetail->generateSalaryComponent->v_component_name) ? $salaryDetail->generateSalaryComponent->v_component_name : ''  ).'</td>';
                     						$deductEditHtml .= '<td><input type="text" '.$disableOnHoldSalary.'  data-component-id="'.$salaryDetail->generateSalaryComponent->i_id .'" class="form-control deduct-head amount"  onkeyup="onlyNumber(this);calculateModalTotalSalary(this);" onchange="onlyNumber(this);calculateModalTotalSalary(this);" value="'.( isset($damount) ? ($damount) : ''  ).'" ></td>';
                     						$deductEditHtml .= '</tr>';
                     						
                     					}
                     					
                     					if( isset($allowedAmendment) && ($allowedAmendment != false )  ) {
                     						$amendmentDeductHtml .= '<tr>';
                     						$amendmentDeductHtml .= '<td>'.( isset($salaryDetail->generateSalaryComponent->v_component_name) ? $salaryDetail->generateSalaryComponent->v_component_name : ''  ).'</td>';
                     						$amendmentDeductHtml .= '<td><input type="text" '.$disableOnHoldSalary.'name="salary_info_'.$salaryDetail->i_id.'"  data-component-id="'.$salaryDetail->generateSalaryComponent->i_id .'" class="form-control deduct-head amount"  onkeyup="onlyNumber(this);calculateModalTotalSalary(this);" onchange="onlyNumber(this);calculateModalTotalSalary(this);" value="'.( isset($damount) ? ($damount) : ''  ).'" ></td>';
                     						$amendmentDeductHtml .= '</tr>';
                     					}
                     					
                     					break;
                     			}
                     		}
                     	}
                     	$netPaySalary = ( $totalEarning - $totalDeduction );
                     	$additionalDeductionRowCount = ( count($earningHead) - count($deductHead) );
                     } else {
                     	if( isset($salaryDetails->assignSalaryInfo) && (!empty($salaryDetails->assignSalaryInfo)) ){
                     		$salaryDetails->assignSalaryInfo = $salaryDetails->assignSalaryInfo->sortBy('assignSalaryComponent.i_sequence');
                     	}
                     	
                     	
                     	$salaryComponentDetails = ( isset($salaryDetails->assignSalaryInfo) ? $salaryDetails->assignSalaryInfo : [] );
                     	$presentDayCount = ( isset($getPresentDates) ? count($getPresentDates) : 0 );
                     	if(!empty($salaryComponentDetails)){
                     		foreach($salaryComponentDetails as $salaryComponentDetail){
                     			
                     			switch($salaryComponentDetail->assignSalaryComponent->e_salary_components_type){
                     				case config('constants.SALARY_COMPONENT_TYPE_EARNING'):
                     					$earningHead[] = $salaryComponentDetail;
                     					$eamount = ( isset($salaryComponentDetail->d_amount) ? $salaryComponentDetail->d_amount : 0 );
                     					//$eamount = dayWiseSalaryHeadAmount( $eamount , $presentDayCount );
                     					if(  in_array( $salaryComponentDetail->assignSalaryComponent->i_id , $currentComponentIds  ) ){
                     						$searchKey = array_search($salaryComponentDetail->assignSalaryComponent->i_id , $currentComponentIds);
                     						if( strlen($searchKey) > 0 ){
                     							if( isset($currentSalaryComponentValues[$searchKey]['salary_value']) && (!empty($currentSalaryComponentValues[$searchKey]['salary_value'])) ){
                     								$eamount = str_replace(",", "", $currentSalaryComponentValues[$searchKey]['salary_value']);
                     							}
                     						}
                     					}
                     					$earningHtml .= '<tr>';
                     					$earningHtml .= '<td>'.( isset($salaryComponentDetail->assignSalaryComponent->v_component_name) ? $salaryComponentDetail->assignSalaryComponent->v_component_name : ''  ).'</td>';
                     					$earningHtml .= '<td>'.( isset($eamount) ? decimalAmount($eamount) : ''  ).'</td>';
                     					$earningHtml .= '</tr>';
                     	
                     					$earningEditHtml .= '<tr>';
                     					$earningEditHtml .= '<td>'.( isset($salaryComponentDetail->assignSalaryComponent->v_component_name) ? $salaryComponentDetail->assignSalaryComponent->v_component_name : ''  ).'</td>';
                     					$earningEditHtml .= '<td><input type="text" data-component-id="'.$salaryComponentDetail->assignSalaryComponent->i_id .'" class="form-control earning-head amount"  onkeyup="onlyNumber(this);calculateModalTotalSalary(this);" onchange="onlyNumber(this);calculateModalTotalSalary(this);" value="'.( isset($eamount) ? ($eamount) : ''  ).'" ></td>';
                     					$earningEditHtml .= '</tr>';
                     	
                     					if( isset($eamount) && (!empty($eamount)) ){
                     						//var_dump($eamount);die;
                     						$totalEarning += $eamount;
                     					}
                     					break;
                     				case config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'):
                     					$deductHead[] = $salaryComponentDetail;
                     					$damount = ( isset($salaryComponentDetail->d_amount) ? $salaryComponentDetail->d_amount : 0 );
                     					if(  in_array( $salaryComponentDetail->assignSalaryComponent->i_id , $currentComponentIds  ) ){
                     						$searchKey = array_search($salaryComponentDetail->assignSalaryComponent->i_id , $currentComponentIds);
                     						if( strlen($searchKey) > 0 ){
                     							if( isset($currentSalaryComponentValues[$searchKey]['salary_value']) && (!empty($currentSalaryComponentValues[$searchKey]['salary_value'])) ){
                     								$damount = str_replace(",", "", $currentSalaryComponentValues[$searchKey]['salary_value']);
                     							}
                     						}
                     					}
                     					$deductHtml .= '<tr>';
                     					$deductHtml .= '<td>'.( isset($salaryComponentDetail->assignSalaryComponent->v_component_name) ? $salaryComponentDetail->assignSalaryComponent->v_component_name : ''  ).'</td>';
                     					$deductHtml .= '<td>'.( isset($damount) ? decimalAmount($damount) : ''  ).'</td>';
                     					$deductHtml .= '</tr>';
                     					if( isset($damount) && (!empty($damount)) ){
                     						$totalDeduction += $damount;
                     					}
                     					
                     					$disableOnHoldSalary = '';
                     					
                     	
                     					$deductEditHtml .= '<tr>';
                     					$deductEditHtml .= '<td>'.( isset($salaryComponentDetail->assignSalaryComponent->v_component_name) ? $salaryComponentDetail->assignSalaryComponent->v_component_name : ''  ).'</td>';
                     					$deductEditHtml .= '<td><input type="text" '.$disableOnHoldSalary.'  data-component-id="'.$salaryComponentDetail->assignSalaryComponent->i_id .'" class="form-control deduct-head amount"  onkeyup="onlyNumber(this);calculateModalTotalSalary(this);" onchange="onlyNumber(this);calculateModalTotalSalary(this);" value="'.( isset($damount) ? ($damount) : ''  ).'" ></td>';
                     					$deductEditHtml .= '</tr>';
                     	
                     					break;
                     			}
                     		}
                     	}
                     	$netPaySalary = ( ( $totalEarning - $totalDeduction ) > 0 ? ( $totalEarning - $totalDeduction ) : 0 )  ;
                     	$additionalDeductionRowCount = ( count($earningHead) - count($deductHead) );
                     }
                     
                     $masterEarningHtml = $masterDeductionHtml =  "";
                     $masterEarningAmount = $masterDeductAmount = $totalMasterEarningAmount = $totalMasterDeductAmount =  0;
                     $masterEarningHead = $masterDeductHead = [];
                     if( isset($masterSalaryDetails->assignSalaryInfo) && (!empty($masterSalaryDetails->assignSalaryInfo)) ){
                     	$masterSalaryDetails->assignSalaryInfo = $masterSalaryDetails->assignSalaryInfo->sortBy('assignSalaryComponent.i_sequence');
                     		foreach($masterSalaryDetails->assignSalaryInfo as $salaryComponentDetail){
                     			//echo "<pre>";print_r($salaryComponentDetail);
                     			if(isset($salaryComponentDetail->assignSalaryComponent->e_salary_components_type)){
                     				switch($salaryComponentDetail->assignSalaryComponent->e_salary_components_type){
                     					case config('constants.SALARY_COMPONENT_TYPE_EARNING'):
                     						$masterEarningHead[] = $salaryComponentDetail;
                     						$masterEarningAmount = ( isset($salaryComponentDetail->d_amount) ? $salaryComponentDetail->d_amount : 0 );
                     				
                     						$masterEarningHtml .= '<tr>';
                     						$masterEarningHtml .= '<td>'.( isset($salaryComponentDetail->assignSalaryComponent->v_component_name) ? $salaryComponentDetail->assignSalaryComponent->v_component_name : ''  ).'</td>';
                     						$masterEarningHtml .= '<td>'.( isset($masterEarningAmount) ? decimalAmount($masterEarningAmount) : ''  ).'</td>';
                     						$masterEarningHtml .= '</tr>';
                     				
                     						if( isset($masterEarningAmount) && (!empty($masterEarningAmount)) ){
                     							//var_dump($eamount);die;
                     							$totalMasterEarningAmount += $masterEarningAmount;
                     						}
                     						break;
                     					case config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'):
                     						$masterDeductHead[] = $salaryComponentDetail;
                     						$masterDeductAmount = ( isset($salaryComponentDetail->d_amount) ? $salaryComponentDetail->d_amount : 0 );
                     				
                     						$masterDeductionHtml .= '<tr>';
                     						$masterDeductionHtml .= '<td>'.( isset($salaryComponentDetail->assignSalaryComponent->v_component_name) ? $salaryComponentDetail->assignSalaryComponent->v_component_name : ''  ).'</td>';
                     						$masterDeductionHtml .= '<td>'.( isset($masterDeductAmount) ? decimalAmount($masterDeductAmount) : ''  ).'</td>';
                     						$masterDeductionHtml .= '</tr>';
                     						if( isset($masterDeductAmount) && (!empty($masterDeductAmount)) ){
                     							$totalMasterDeductAmount += $masterDeductAmount;
                     						}
                     						break;
                     				}
                     			}
                     			
                     		}
                     	
                     }
                     
                     //echo "sss = ".$masterDeductionHtml;
                     
                     $masterNetPaySalary = ( $totalMasterEarningAmount - $totalMasterDeductAmount );
                     $masterAdditionalDeductionRowCount = ( count($masterEarningHead) - count($masterDeductHead) );
                     
                     if( $allowEditSalary != true ){
                     	$masterEarningHtml = $earningHtml;
                     	$masterDeductionHtml = $deductHtml;
                     	$masterNetPaySalary = $netPaySalary;
                     	$masterAdditionalDeductionRowCount = $additionalDeductionRowCount;
                     	$totalMasterDeductAmount = $totalDeduction;
                     	$totalMasterEarningAmount = $totalEarning;
                     }
                     
                     //echo "<pre>";print_r($salaryComponentDetails);die;
                     ?>
                     <div class="col-lg-8">
                         <div class="row">
                             <!-- Salary Calculation -->
                             <div class="col-12">
                                 <div class="form-group mb-0">
                                     <h4 class="address-title">{{ trans("messages.salary-calculation") }}</h4>
                                     <div class="row pt-4 no-gutters salary-table-gap">
                                         <div class="col-lg-6">
                                             <div class="table-responsive">
                                                 <table class="table table-sm table-bordered">
                                                     <thead>
                                                         <tr>
                                                             <th class="text-left deduction-title" style="min-width:80px;">{{ trans("messages.earnings") }}</th>
                                                             <th class="text-left deduction-title" style="min-width:80px; width:140px;">{{ trans("messages.amount") }}</th>
                                                         </tr>
                                                     </thead>
                                                     <tbody>
                                                         <?php echo $masterEarningHtml ?>
                                                         <tr>
                                                             <th class="text-left net-pay-class">{{ trans("messages.total-earnings") }}</th>
                                                             <th class="text-left net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="total-earning-salary">{{ decimalAmount($totalMasterEarningAmount) }}</span></th>
                                                         </tr>
                                                     </tbody>
                                                 </table>
                                             </div>
                                         </div>
                                         <div class="col-lg-6">
                                             <div class="table-responsive">
                                                 <table class="table table-sm table-bordered">
                                                     <thead>
                                                         <tr>
                                                             <th class="text-left deduction-title" style="min-width:80px;">{{ trans("messages.deductions") }}</th>
                                                             <th class="text-left deduction-title" style="min-width:80px; width:140px;">{{ trans("messages.amount") }}</th>
                                                         </tr>
                                                     </thead>
                                                     <tbody>
                                                         <?php echo $masterDeductionHtml ?>
                                                         <?php 
                                                         if( $masterAdditionalDeductionRowCount > 0 ){
                                                         	for($i = 1 ; $i <= $masterAdditionalDeductionRowCount ; $i++  ){
                                                         		?>
                                                         		<tr>
                                                         			<td>&nbsp;</td>
                                                         			<td>&nbsp;</td>
                                                         		</tr>
                                                         		<?php
                                                         	}
                                                         } 
                                                         ?>
                                                         <tr>
                                                             <th class="net-pay-class">{{ trans("messages.total-deductions") }}</th>
                                                             <th class="net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="total-dedcut-salary">{{ decimalAmount($totalMasterDeductAmount) }}</span></th>
                                                         </tr>
                                                     </tbody>
                                                 </table>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <!-- Net Pay -->
                             <div class="col-12">
                                 <div class="form-group">
                                     <div class="row">
                                         <div class="col-lg-12 pt-xl-2 pt-2">
                                             <div class="table-responsive">
                                                 <table class="table table-sm table-bordered">
                                                     <tbody>
                                                         <tr>
                                                             <th rowspan="2" class="text-left net-pay-class" style="min-width:50px; width:180px;">{{ trans("messages.net-pay") }}</th>
                                                             <th class="text-left net-pay-class" style="min-width:80px; width:130px;">{{ trans("messages.amount") }}</th>
                                                             <th class="text-left net-pay-class" style="min-width:130px; width:250px;">{{ trans("messages.salary-in-words") }}</th>
                                                         </tr>
                                                         <tr>
                                                             <th class="text-left net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} {{ decimalAmount($masterNetPaySalary) }}</th>
                                                             <th class="text-left net-pay-class">{{ (!empty($masterNetPaySalary) ? convertAmountIntoWord($masterNetPaySalary) : '' ) }}</th>
                                                         </tr>
                                                     </tbody>
                                                 </table>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <?php if($allowEditSalary != false ){ ?>
                             <!-- Edit Salary -->
                             <div class="col-12">
                                 {!! Form::open(array( 'id '=> 'employee-salary-edit-form' , 'method' => 'post' )) !!}
                                     <div class="row">
                                         <div class="col-12 my-3">
                                             <h4 class="address-title">{{ trans('messages.edit-salary') }}</h4>
                                         </div>
                                         <div class="col-12">
                                             <div class="form-group">
                                                 <div class="row no-gutters edit-salary-table salary-table-gap">
                                                     <div class="col-lg-6">
                                                         <div class="table-responsive">
                                                             <table class="table table-sm table-bordered">
                                                                 <thead>
                                                                     <tr>
                                                                         <th class="text-left deduction-title" style="min-width:80px;">{{ trans("messages.earnings") }}</th>
                                                                         <th class="text-left deduction-title" style="min-width:80px; width:140px;">{{ trans("messages.amount") }}</th>
                                                                     </tr>
                                                                 </thead>
                                                                 <tbody>
                                                                     <?php echo $earningEditHtml ?>
                                                                     <tr>
                                                                         <th class="text-left net-pay-class">{{ trans("messages.total-earnings") }}</th>
                                                                         <th class="text-left net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="total-edit-earning-salary">{{ decimalAmount($totalEarning) }}</span></th>
                                                                     </tr>
                                                                 </tbody>
                                                             </table>
                                                         </div>
                                                     </div>
                                                     <div class="col-lg-6">
                                                         <div class="table-responsive">
                                                             <table class="table table-sm table-bordered">
                                                                 <thead>
                                                                     <tr>
                                                                         <th class="text-left deduction-title" style="min-width:80px;">{{ trans("messages.deductions") }}</th>
                                                                         <th class="text-left deduction-title" style="min-width:80px; width:140px;">{{ trans("messages.amount") }}</th>
                                                                     </tr>
                                                                 </thead>
                                                                 <tbody row count = <?php echo $additionalDeductionRowCount ?>>
                                                                    	<?php echo $deductEditHtml ?>
                                                                    	<?php 
				                                                         if( $additionalDeductionRowCount > 0 ){
				                                                         	for($i = 1 ; $i <= $additionalDeductionRowCount ; $i++  ){
				                                                         		?>
				                                                         		<tr>
				                                                         			<td style="height: 43px;">&nbsp;</td>
				                                                         			<td style="height: 43px;">&nbsp;</td>
				                                                         		</tr>
				                                                         		<?php
				                                                         	}
				                                                         } 
				                                                         ?>
                                                                     <tr>
                                                                         <th class="net-pay-class">{{ trans("messages.total-deductions") }}</th>
                                                                         <th class="net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="total-edit-dedcut-salary">{{ decimalAmount($totalDeduction) }}</span></th>
                                                                     </tr>
                                                                 </tbody>
                                                             </table>
                                                         </div>
                                                     </div>
                                                     <div class="col-12">
						                                 <div class="form-group">
						                                     <div class="row">
						                                         <div class="col-lg-12 pt-xl-2 pt-2">
						                                             <div class="table-responsive">
						                                                 <table class="table table-sm table-bordered">
						                                                     <tbody>
						                                                         <tr>
						                                                             <th rowspan="2" class="text-left net-pay-class" style="min-width:50px; width:180px;">{{ trans("messages.net-pay") }}</th>
						                                                             <th class="text-left net-pay-class" style="min-width:80px; width:130px;">{{ trans("messages.amount") }}</th>
						                                                             <th class="text-left net-pay-class" style="min-width:130px; width:250px;">{{ trans("messages.salary-in-words") }}</th>
						                                                         </tr>
						                                                         <tr>
						                                                             <th class="text-left net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="calculated-net-pay-amount">{{ decimalAmount($netPaySalary) }}</span></th>
						                                                             <th class="text-left net-pay-class"><span class="calculate-net-pay-amount-into-word">{{ (!empty($netPaySalary) ? convertAmountIntoWord($netPaySalary) : '' ) }}</span></th>
						                                                         </tr>
						                                                     </tbody>
						                                                 </table>
						                                             </div>
						                                         </div>
						                                     </div>
						                                 </div>
						                             </div>
                                                 </div>
                                             </div>
                                         </div>
                                         
                                     </div>
                                     <div class="modal-footer justify-content-end">
                                         <button type="button" onclick="updateModalSalaryValue(this);" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                                         <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                                     </div>
                                 {!! Form::close() !!}
                             </div>
                             
                             <?php } ?>
                             
                             <?php if( isset($allowedAmendment) && ($allowedAmendment != false )  ) { ?>
                             <div class="col-12">
                                 {!! Form::open(array( 'id '=> 'employee-salary-amendment-form' , 'method' => 'post' )) !!}
                                     <div class="row">
                                         <div class="col-12 my-3">
                                             <h4 class="address-title">{{ trans('messages.amendment-salary') }}</h4>
                                         </div>
                                         <div class="col-12">
                                             <div class="form-group">
                                                 <div class="row no-gutters edit-salary-table">
                                                     <div class="col-lg-6">
                                                         <div class="table-responsive">
                                                             <table class="table table-sm table-bordered">
                                                                 <thead>
                                                                     <tr>
                                                                         <th class="text-left deduction-title" style="min-width:80px;">{{ trans("messages.earnings") }}</th>
                                                                         <th class="text-left deduction-title" style="min-width:80px; width:140px;">{{ trans("messages.amount") }}</th>
                                                                     </tr>
                                                                 </thead>
                                                                 <tbody>
                                                                     <?php echo $amendmentEarningHtml ?>
                                                                     <tr>
                                                                         <th class="text-left net-pay-class">{{ trans("messages.total-earnings") }}</th>
                                                                         <th class="text-left net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="total-edit-earning-salary">{{ decimalAmount($totalEarning) }}</span></th>
                                                                     </tr>
                                                                 </tbody>
                                                             </table>
                                                         </div>
                                                     </div>
                                                     <div class="col-lg-6">
                                                         <div class="table-responsive">
                                                             <table class="table table-sm table-bordered">
                                                                 <thead>
                                                                     <tr>
                                                                         <th class="text-left deduction-title" style="min-width:80px;">{{ trans("messages.deductions") }}</th>
                                                                         <th class="text-left deduction-title" style="min-width:80px; width:140px;">{{ trans("messages.amount") }}</th>
                                                                     </tr>
                                                                 </thead>
                                                                 <tbody row count = <?php echo $additionalDeductionRowCount ?>>
                                                                    	<?php echo $amendmentDeductHtml ?>
                                                                    	<?php 
				                                                         if( $additionalDeductionRowCount > 0 ){
				                                                         	for($i = 1 ; $i <= $additionalDeductionRowCount ; $i++  ){
				                                                         		?>
				                                                         		<tr>
				                                                         			<td style="height: 43px;">&nbsp;</td>
				                                                         			<td style="height: 43px;">&nbsp;</td>
				                                                         		</tr>
				                                                         		<?php
				                                                         	}
				                                                         } 
				                                                         ?>
                                                                     <tr>
                                                                         <th class="net-pay-class">{{ trans("messages.total-deductions") }}</th>
                                                                         <th class="net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="total-edit-dedcut-salary">{{ decimalAmount($totalDeduction) }}</span></th>
                                                                     </tr>
                                                                 </tbody>
                                                             </table>
                                                         </div>
                                                     </div>
                                                     <div class="col-12">
						                                 <div class="form-group">
						                                     <div class="row">
						                                         <div class="col-lg-12 pt-xl-2 pt-2">
						                                             <div class="table-responsive">
						                                                 <table class="table table-sm table-bordered">
						                                                     <tbody>
						                                                         <tr>
						                                                             <th rowspan="2" class="text-left net-pay-class" style="min-width:180px; width:180px;">{{ trans("messages.net-pay") }}</th>
						                                                             <th class="text-left net-pay-class" style="min-width:80px; width:130px;">{{ trans("messages.amount") }}</th>
						                                                             <th class="text-left net-pay-class" style="min-width:130px; width:250px;">{{ trans("messages.salary-in-words") }}</th>
						                                                         </tr>
						                                                         <tr>
						                                                             <th class="text-left net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="calculated-net-pay-amount">{{ decimalAmount($netPaySalary) }}</span></th>
						                                                             <th class="text-left net-pay-class"><span class="calculate-net-pay-amount-into-word">{{ (!empty($netPaySalary) ? convertAmountIntoWord($netPaySalary) : '' ) }}</span></th>
						                                                         </tr>
						                                                     </tbody>
						                                                 </table>
						                                             </div>
						                                         </div>
						                                     </div>
						                                 </div>
						                             </div>
                                                 </div>
                                             </div>
                                         </div>
                                         
                                     </div>
                                     <input type="hidden" name="amendment_salary_id" value="{{ ( isset($generatedSalaryDetails->i_id) ? Wild_tiger::encode($generatedSalaryDetails->i_id) : '' )  }}">
                                       {!! Form::close() !!}
                                     <div class="modal-footer justify-content-end">
                                         <button type="button" onclick="updateAmmentmentSalaryValue(this);" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                                         <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                                     </div>
                                 
                             </div>
                             <?php } ?>
                             
                         </div>
                     </div>
                 </div>
                 @include(config('constants.ADMIN_FOLDER') .'attendance-calendar')
                 <script>
                 $(function(){
                	 createAttendanceCalendar('employee-salary-leave-info' , '<?php echo $month?>' , '<?php echo $year ?>');
				 });
                 </script>