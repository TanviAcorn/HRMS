
					<div class="row">
                        <div class="col-xl-3 col-sm-6">
                            <div class="form-group">
                                <label for="effective_from" class="control-label">{{ trans('messages.effective-from') }}<span class="star">*</span></label>
                                <input type="text" class="form-control" name="effective_from" placeholder="{{ trans('messages.dd-mm-yyyy') }}" value="{{ ( ( isset($salaryMasterInfo) && ( isset($salaryMasterInfo->dt_effective_date) ) ) ? clientDate($salaryMasterInfo->dt_effective_date) : '' ) }}">
                            </div>
                        </div>
                       
                        <div class="col-xl-3 col-sm-6 only-admin-can-manage">
                            <div class="form-group">
                                <label for="salary_group" class="control-label">{{ trans('messages.salary-group') }}<span class="star">*</span></label>
                                <select class="form-control" name="salary_group" onchange="getSalaryGruopDetails(this)">
                                    <option value="">{{ trans('messages.select') }}</option>
                                    @if(!empty($salaryGroupDetails))
                                    	@foreach($salaryGroupDetails as $salaryGroupDetail)
                                    		@php 
                                    		$salaryGroupId = Wild_tiger::encode($salaryGroupDetail->i_id);
                                    		$selected = '';
                                    		if( isset($salaryMasterInfo) && ( isset($salaryMasterInfo->i_salary_group_id) ) && ( $salaryMasterInfo->i_salary_group_id == $salaryGroupDetail->i_id ) ){
                                    			$selected = "selected='selected'";
                                    		} 
                                    		@endphp
                                    		<option value="{{ $salaryGroupId }}" {{ $selected }} data-id="{{ $salaryGroupDetail->i_id }}" >{{ (!empty($salaryGroupDetail->v_group_name) ? $salaryGroupDetail->v_group_name :'') }}</option>
                                    	@endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <?php /* ?>
                        <div class="col-xl-4 col-lg-5 col-md-7 only-admin-can-manage">
                            <div class="form-group">
                                <label for="deduction_employer_from_employee" class="control-label">{{ trans('messages.deduction-employer-from-employee') }}<span class="text-danger">*</span></label>
                                <div class="radio-boxes form-row p-1 bg-white">
                                    <div class="radio-box col-sm-4 col-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="deduction_employer_from_employee" onclick="getSalaryGroupDetail(this);" id="deduction_employer_from_employee_yes" {{ ( ( isset($salaryMasterInfo) && (isset($salaryMasterInfo->e_pf_by_employer)) && ( $salaryMasterInfo->e_pf_by_employer == config("constants.SELECTION_YES") ) ) ? 'checked' : '' ) }}  value="{{ config('constants.SELECTION_YES') }}" >
                                            <label class="form-check-label custom-type-label btn stock-btn" for="deduction_employer_from_employee_yes">{{ trans('messages.yes') }}</label>
                                        </div>
                                    </div>
                                    <div class="radio-box col-sm-4 col-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="deduction_employer_from_employee" onclick="getSalaryGroupDetail(this);" id="deduction_employer_from_employee_no" {{ ( (empty($salaryMasterInfo)) ? 'checked' : '' ) }}   {{ ( ( isset($salaryMasterInfo) && (isset($salaryMasterInfo->e_pf_by_employer)) && ( $salaryMasterInfo->e_pf_by_employer == config("constants.SELECTION_NO") ) ) ? 'checked' : '' ) }} value="{{ config('constants.SELECTION_NO') }}">
                                            <label class="form-check-label custom-type-label btn stock-btn" for="deduction_employer_from_employee_no">{{ trans('messages.no') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php */ ?>
                        
                        <div class="col-xl-4 col-lg-5 col-md-7 only-admin-can-manage">
                            <div class="form-group">
                                <label for="deduction_employer_from_employee" class="control-label">{{ trans('messages.deduction-of-pf') }}<span class="text-danger">*</span></label>
                                <div class="radio-boxes form-row p-1 bg-white">
                                    <div class="radio-box col-sm-4 col-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="deduction_of_pf" id="deduction_of_pf_yes" {{ ( ( isset($salaryMasterInfo) && (isset($salaryMasterInfo->e_pf_deduction)) && ( $salaryMasterInfo->e_pf_deduction == config("constants.SELECTION_YES") ) ) ? 'checked' : '' ) }} {{ ( ( ( $allowedChangePFSelection != true ) ) ? 'disabled' : '' ) }}  value="{{ config('constants.SELECTION_YES') }}" >
                                            <label class="form-check-label custom-type-label btn stock-btn" for="deduction_of_pf_yes">{{ trans('messages.yes') }}</label>
                                        </div>
                                    </div>
                                    <div class="radio-box col-sm-4 col-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="deduction_of_pf" id="deduction_of_pf_no" {{ ( (empty($salaryMasterInfo) ) ? 'checked' : '' ) }}   {{ ( ( isset($salaryMasterInfo) && (isset($salaryMasterInfo->e_pf_deduction)) && ( $salaryMasterInfo->e_pf_deduction == config("constants.SELECTION_NO") ) ) ? 'checked' : '' ) }}  {{ ( ( ( $allowedChangePFSelection != true )  ) ? 'disabled' : '' ) }} value="{{ config('constants.SELECTION_NO') }}">
                                            <label class="form-check-label custom-type-label btn stock-btn" for="deduction_of_pf_no">{{ trans('messages.no') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                        <div class="col-12 pt-2 salary-breakup-record-show" <?php echo ( isset($salaryMasterInfo) && (!empty($salaryMasterInfo)) ) ? '' : 'style=display:none;' ?>>
                            <div class="form-group">
                                <h4 class="address-title">{{ trans('messages.salary-breakup')}}</h4>
                                <div class="row pt-4">
                                    <div class="col-lg-12 salary-group-components-record">
                                        @include(config('constants.AJAX_VIEW_FOLDER') . 'employee-master/salary-group-components-breakup')	
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 salary-breakup-record-show" <?php echo ( isset($salaryMasterInfo) && (!empty($salaryMasterInfo)) ) ? '' : 'style=display:none;' ?>>
                            <div class="form-group">
                                <div class="row pt-xl-2 pt-0">
                                    <div class="col-lg-12 pt-xl-2 pt-2">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th rowspan="2" class="text-left net-pay-class">Net Pay</th>
                                                        <th class="text-left net-pay-class">Monthly</th>
                                                        <th class="text-left net-pay-class">Annually</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-left net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="monthly-net-pay-amount"><?php echo ( ( isset($salaryMasterInfo) && (!empty($salaryMasterInfo->d_net_pay_monthly)) ) ? $salaryMasterInfo->d_net_pay_monthly : '' ) ?></span></th>
                                                        <th class="text-left net-pay-class">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="yearly-net-pay-amount"><?php echo ( ( isset($salaryMasterInfo) && (!empty($salaryMasterInfo->d_net_pay_annually)) ) ? $salaryMasterInfo->d_net_pay_annually : '' ) ?></span></th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="revise_salary_employee_id" value="{{ ( ( isset($employeeId) && (!empty($employeeId)) ) ? Wild_tiger::encode($employeeId) : '' ) }}">
                    <input type="hidden" name="revise_salary_record_id" value="{{ ( ( isset($reviseRecordId) && (!empty($reviseRecordId)) ) ? Wild_tiger::encode($reviseRecordId) : '' ) }}">
                    <script>
                    $("[name='effective_from']").datetimepicker({
	   		            useCurrent: false,
	   		            viewMode: 'days',
	   		            ignoreReadonly: true,
	   		            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
	   		            showClear: true,
	   		            showClose: true,
	   		            widgetPositioning: {
	   		                vertical: 'bottom',
	   		                horizontal: 'auto'

	   		            },
	   		            icons: {
	   		                clear: 'fa fa-trash',
	   		                Close: 'fa fa-trash',
	   		            },
	   		        });
                    var current_date = moment().format('YYYY-MM-DD');
	   		        var min_effectative_date = "{{ ( isset($minEffectativeDate) ? $minEffectativeDate : "" ) }}";
	   		        //console.log("min_effectative_date = " + min_effectative_date );
	   		    	$("[name='effective_from']").data("DateTimePicker").minDate(moment(min_effectative_date,'YYYY-MM-DD').startOf('d'));
					var effective_from = $.trim($("[name='effective_from']").val());
	   		    	if( moment(min_effectative_date).isAfter(current_date) ){
						//console.log("set mndate");
						$("[name='effective_from']").data('DateTimePicker').defaultDate(moment(min_effectative_date,'YYYY-MM-DD').format('DD-MM-YYYY'));
						$("[name='effective_from']").val("");
						if( effective_from != "" && effective_from != null ){
							$("[name='effective_from']").val(effective_from);
						}
		   		    }
	   		    	
                    <?php if( empty($data['salaryMasterInfo']) ) { ?>
                    	//$("[name='effective_from']").prop("readonly" , true);
                    	//$("[name='effective_from']").val(moment(min_effectative_date,'YYYY-MM-DD').format('DD-MM-YYYY'));
                    <?php } ?>
                    </script>
                    