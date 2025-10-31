							<?php 
							$earningHtml = $deductHtml =  "";
							$earningIndex = $deductIndex =  0;
							?>
							@if(count($salaryComponentDetails) > 0 )
								@foreach($salaryComponentDetails as $salaryComponentDetail)
									<?php
									if( (isset($salaryComponentDetail->salaryComponentInfo->i_id)) && ( in_array( $salaryComponentDetail->salaryComponentInfo->i_id ,[ config('constants.ON_HOLD_SALARY_COMPONENT_ID') , config('constants.PF_SALARY_COMPONENT_ID') , config('constants.PT_SALARY_COMPONENT_ID')  ] ) ) ){
										continue;
									}
									switch($salaryComponentDetail->e_type){
										case config('constants.SALARY_COMPONENT_TYPE_EARNING'):
											$earningHtml .= '<tr class="earning-row">';
		                                    $earningHtml .= '<td class="text-left">'.( isset($salaryComponentDetail->salaryComponentInfo->v_component_name)  ?  $salaryComponentDetail->salaryComponentInfo->v_component_name : '' ).'</td>';
		                                    $earningHtml .= '<td><input type="text" onkeyup="onlyNumber(this);" onchange="onlyNumber(this);calculateYearlyAmount(this);totalEarning(this);" class="form-control monthly-column" name="salary_compoent_id_'.$salaryComponentDetail->i_salary_components_id.'" placeholder="'.trans('messages.amount').'"></td>';
		                                    $earningHtml .= '<td class="text-left yearly-column"></td>';
											$earningHtml .= '</tr>';
											break;
										case config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'):
											$deductHtml .= '<tr class="deduction-row">';
											$deductHtml .= '<td class="text-left">'.( isset($salaryComponentDetail->salaryComponentInfo->v_component_name)  ?  $salaryComponentDetail->salaryComponentInfo->v_component_name : '' ).'</td>';
											$deductHtml .= '<td><input type="text" onkeyup="onlyNumber(this);" onchange="onlyNumber(this);calculateYearlyAmount(this);totalDeduction(this);" class="form-control monthly-column" name="salary_compoent_id_'.$salaryComponentDetail->i_salary_components_id.'" placeholder="'.trans('messages.amount').'"></td>';
											$deductHtml .= '<td class="text-left yearly-column"></td>';
											$deductHtml .= '</tr>';
											break;
									}
									?>
								@endforeach
							@endif
							<div class="row">
                            	@if(!empty($earningHtml))
                            	<div class="col-lg-6 pr-lg-0">
                            		<div class="table-responsive">
		                                <table class="table table-sm table-bordered">
		                                    <thead>
		                                        <tr>
		                                            <th class="text-left deduction-title" style="min-width:80px;">{{ trans("messages.earnings") }}</th>
		                                            <th class="text-left deduction-title" style="min-width:80px;">{{ trans("messages.monthly") }}</th>
		                                            <th class="text-left deduction-title" style="min-width:80px;">{{ trans("messages.annually") }}</th>
		                                        </tr>
		                                    </thead>
		                                    <tbody>
		                                       <?php echo $earningHtml ?>
		                                        <tr>
		                                            <th class="text-left net-pay-class ">{{ trans('messages.total-earnings') }}</th>
		                                            <th class="text-left net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="total-month-earning-amount" style="display: none;"></span></th>
		                                            <th class="text-left net-pay-class">INR <span class="total-yearly-earning"></span></th>
		                                        </tr>
		                                    </tbody>
		                                </table>
		                            </div>
                            	</div>
                            	@endif
                            	@if(!empty($deductHtml))
                            	<div class="col-lg-6 px-lg-0">
                            		<div class="table-responsive">
		                                <table class="table table-sm table-bordered">
		                                    <thead>
		                                        <tr>
		                                            <th class="text-left deduction-title" style="min-width:80px;">{{ trans("messages.deduction") }}</th>
		                                            <th class="text-left deduction-title" style="min-width:80px;">{{ trans("messages.monthly") }}</th>
		                                            <th class="text-left deduction-title" style="min-width:80px;">{{ trans("messages.annually") }}</th>
		                                        </tr>
		                                    </thead>
		                                    <tbody>
		                                        <?php echo $deductHtml ?>
		                                        <tr>
		                                            <th class="net-pay-class">{{ trans('messages.total-deductions') }}</th>
		                                            <th class="text-left net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="total-month-deduction-amount" style="display: none;"></span></th>
		                                            <th class="net-pay-class">INR <span class="total-yearly-deduction"></span></th>
		                                        </tr>
		                                    </tbody>
		                                </table>
		                            </div>
                            	</div>
                            	@endif
                            </div>