
						<div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="group_name" class="control-label">{{ trans('messages.group-name') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="group_name" class="form-control" placeholder="{{ trans('messages.group-name') }}" value="{{ old('group_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_group_name)) ? $recordInfo->v_group_name : ''  ) ) ) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="group_description" class="control-label">{{ trans('messages.group-description') }}</label>
                                    <textarea class="form-control" name="group_description" rows="4" placeholder="{{ trans('messages.group-description') }}">{{ ( (isset($recordInfo) && (!empty($recordInfo->v_group_description)) ? $recordInfo->v_group_description : ''  ) )  }}</textarea>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="form-group groups-salary-breakup">
                                    <label for="salary_components_group_name" class="control-label">{{ trans('messages.select-salary-components') }}</label>
                                    <div class="groups-class pt-3">
                                        <div class="row">
                                        	<div class="col-md-6 pr-0">
                                        		<div class="table-responsive">
		                                            <table class="table table-sm table-bordered">
		                                                <thead>
		                                                    <tr>
		                                                        <th class="text-left deduction-title" style="width:50%;">{{ trans("messages.earnings") }}<span class="text-white">*</span></th>
		                                                    </tr>
		                                                </thead>
		                                                <tbody class="salary-groups-earning-deduction">
		                                                <?php if(!empty($salaryComponentsDetails)){
		                                                		foreach ($salaryComponentsDetails as $salaryComponentsDetail){
		                                                			$encodedId = (!empty($salaryComponentsDetail->i_id) ?  Wild_tiger::encode($salaryComponentsDetail->i_id) : 0);
		                                                			$salaryComponentsErningsDetail = (!empty($salaryComponentsDetail->e_salary_components_type) ? $salaryComponentsDetail->e_salary_components_type :'');
			                                                		$salaryComponentsEarningsIds = (!empty($recordInfo->v_salary_components_earnings_ids) ? explode("," , ( $recordInfo->v_salary_components_earnings_ids ) ) : [] );
		                                        					$salaryComponentsDeductionIds = (!empty($recordInfo->v_salary_components_deduction_ids) ? explode("," , ( $recordInfo->v_salary_components_deduction_ids ) ) : [] );?>
			                                                		<?php $checked = '';
			                                                		if(in_array($salaryComponentsDetail->i_id, $salaryComponentsEarningsIds)){
			                                                			$checked = "checked = 'checked'";
			                                                		}
			                                                		if(in_array($salaryComponentsDetail->i_id, $salaryComponentsDeductionIds)){
			                                                			$checked = "checked = 'checked'";
			                                                		}
			                                                		if( in_array( $salaryComponentsDetail->i_id , [ config('constants.ON_HOLD_SALARY_COMPONENT_ID') ] ) ){
			                                                			continue;
			                                                		}
			                                                		?>
			                                                		<?php if(($salaryComponentsErningsDetail == config('constants.SALARY_COMPONENT_TYPE_EARNING'))){ ?>
			                                                		<tr>
			                                                			<td class="text-left">
			                                                				<div class="form-check form-check-inline pt-2 pb-2">
				                                                            	<input class="form-check-input salary-components-earnings-row" type="checkbox" id="salary_components_earning_{{ $salaryComponentsDetail->i_id }}" name="salary_components_earning[]" {{ $checked }} value="{{ $encodedId }}">
				                                                                <label class="form-check-label lable-control" for="salary_components_earning_{{ $salaryComponentsDetail->i_id }}">{{ (!empty($salaryComponentsDetail->v_component_name) ? $salaryComponentsDetail->v_component_name :'') }}</label>
																			</div>
			                                                            </td>
				                                                    </tr>	
				                                                    <?php } ?>
		                                                    <?php } ?>
		                                                <?php } ?>
		                                                </tbody>
		                                            </table>
		                                        </div>
                                        	</div>
                                        	<div class="col-md-6 pl-0">
                                        		<div class="table-responsive">
		                                            <table class="table table-sm table-bordered">
		                                                <thead>
		                                                    <tr>
		                                                        <th class="text-left deduction-title" style="width:50%;">{{ trans("messages.deductions") }}<span class="text-white">*</span></th>
		                                                    </tr>
		                                                </thead>
		                                                <tbody class="salary-groups-earning-deduction">
		                                                <?php if(!empty($salaryComponentsDetails)){
		                                                		foreach ($salaryComponentsDetails as $salaryComponentsDetail){
		                                                			$encodedId = (!empty($salaryComponentsDetail->i_id) ?  Wild_tiger::encode($salaryComponentsDetail->i_id) : 0);
		                                                			$salaryComponentsErningsDetail = (!empty($salaryComponentsDetail->e_salary_components_type) ? $salaryComponentsDetail->e_salary_components_type :'');
			                                                		$salaryComponentsEarningsIds = (!empty($recordInfo->v_salary_components_earnings_ids) ? explode("," , ( $recordInfo->v_salary_components_earnings_ids ) ) : [] );
		                                        					$salaryComponentsDeductionIds = (!empty($recordInfo->v_salary_components_deduction_ids) ? explode("," , ( $recordInfo->v_salary_components_deduction_ids ) ) : [] );?>
			                                                		<?php $checked = '';
			                                                		if(in_array($salaryComponentsDetail->i_id, $salaryComponentsEarningsIds)){
			                                                			$checked = "checked = 'checked'";
			                                                		}
			                                                		if(in_array($salaryComponentsDetail->i_id, $salaryComponentsDeductionIds)){
			                                                			$checked = "checked = 'checked'";
			                                                		}
			                                                		if( in_array( $salaryComponentsDetail->i_id , [ config('constants.ON_HOLD_SALARY_COMPONENT_ID') , config('constants.PF_SALARY_COMPONENT_ID') , config('constants.PT_SALARY_COMPONENT_ID') ] ) ){
			                                                			continue;
			                                                		}
			                                                		
			                                                		?>
			                                                		<?php if(($salaryComponentsErningsDetail == config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'))){ ?>
			                                                		<tr>
			                                                			<td class="text-left">
				                                                        	<div class="form-check form-check-inline pt-2 pb-2">
					                                                        	<input class="form-check-input salary-components-deduction-row" type="checkbox" id="salary_components_deduction_{{ $salaryComponentsDetail->i_id }}" name="salary_components_deduction[]" {{ $checked }} value="{{ $encodedId }}">
					                                                            <label class="form-check-label lable-control" for="salary_components_deduction_{{ $salaryComponentsDetail->i_id }}">{{ (!empty($salaryComponentsDetail->v_component_name) ? $salaryComponentsDetail->v_component_name :'')}}</label>
																			</div>
				                                                        </td>
			                                                    	</tr>
			                                                    	<?php } ?>	
		                                                    <?php } ?>
		                                                <?php } ?>
		                                                </tbody>
		                                            </table>
		                                        </div>
                                        	</div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>

                        </div>