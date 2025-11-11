<div class="step-panel-class">
    <!-- <h3 class="panel-title"><i class="fa fa-user my-profile-class" aria-hidden="true"></i>Salary Details</h3> -->
    <div class="d-flex step-panel-attribute align-items-center">
        <div class="panel-attribute">
            <h3 class="panel-title"><i class="fa fa-regular fa-money-bill my-profile-class"></i>{{trans('messages.salary-details')}}</h3>
        </div>
        <div class="step-btn">
            <div class="d-flex align-items-center">
                <div class="btn-preview">
                    <div class="btn-class"><button type="button" class="default-btn prev-step" data-tab-name="step3" title="{{ trans('messages.previous') }}">{{trans('messages.previous')}}</button></div>
                </div>
                <div class="btn-next">
                    <div class="btn-class"><button type="button" onclick="salaryFormValidationDetails(this);" class="default-btn tab-next-btn" data-tab-name="step5" title="{{ trans('messages.next') }}">{{trans('messages.next')}} </button></div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-items">
        <div class="row">
            <div class="col-xl-3 col-sm-6">
                <div class="form-group ">
                    <label for="bank_name" class="lable-control">{{ trans('messages.bank-name') }}</label>
                    <select class="form-control" name="bank_name">
                        <option value="">{{ trans("messages.select") }}</option>
	                        @if (!empty($bankRecordDetails))
	                       		@foreach ($bankRecordDetails as $bankRecordDetail)
	                        		{{$bankEncodeId  = Wild_tiger::encode($bankRecordDetail->i_id);}}
	                        		<option value="{{$bankEncodeId}}">{{ (!empty($bankRecordDetail->v_value ) ? $bankRecordDetail->v_value :'')}}</option>
	                        	@endforeach
	                     @endif
                    </select>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="account_number" class="lable-control">{{ trans('messages.account-number') }}</label>
                    <input type="text" class="form-control" name="account_number" placeholder="{{ trans('messages.account-number') }}"  onkeyup="onlyNumber(this)">
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="ifsc_code" class="lable-control">{{ trans('messages.ifsc-code') }}</label>
                    <input type="text" class="form-control" name="ifsc_code" placeholder="{{ trans('messages.ifsc-code') }}">
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="uan_number" class="lable-control">{{ trans('messages.uan-number') }}</label>
                    <input type="text" class="form-control" name="uan_number" placeholder="{{ trans('messages.uan-number') }}">
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="assign_salary_employee" class="lable-control">{{ trans('messages.assign-salary-employee') }}<span class="text-danger">*</span></label>
                    <div class="radio-boxes form-row p-1 bg-white">
                        <div class="radio-box col-sm-4 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio"  name="assign_salary_employee" id="assign_salary_employee_yes" value="{{config('constants.SELECTION_YES')}}">
                                <label class="form-check-label custom-type-label btn stock-btn" for="assign_salary_employee_yes">{{ trans('messages.yes') }}</label>
                            </div>
                        </div>
                        <div class="radio-box col-sm-4 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio"  name="assign_salary_employee" id="assign_salary_employee_no" value="{{config('constants.SELECTION_NO')}}" >
                                <label class="form-check-label custom-type-label btn stock-btn" for="assign_salary_employee_no">{{ trans('messages.no') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 assign-salary" style="display:none;">
                <div class="form-group">
                    <label for="salary_group" class="lable-control">{{ trans('messages.salary-group') }}<span class="text-danger">*</span></label>
                    <select class="form-control" name="salary_group" onchange="getSalaryComponentDetail(this);">
                        <option value="">{{ trans("messages.select") }}</option>
                        @if(count($salaryGroupRecordDetails) > 0 )
                        	@foreach($salaryGroupRecordDetails as $salaryGroupRecordDetail)
                        	<?php $encodeSalaryGroupId = Wild_tiger::encode($salaryGroupRecordDetail->i_id)?>
                        	<option value="{{ $encodeSalaryGroupId }}" data-id="{{ $salaryGroupRecordDetail->i_id }}" >{{ $salaryGroupRecordDetail->v_group_name }}</option>
                        	@endforeach
                        @endif
                    </select>
                </div>
            </div>

			<?php /* ?>
            <div class="col-xl-4 col-sm-6 assign-salary" style="display:none;">
                <div class="form-group">
                    <label for="deduction_employer_from_employee" class="lable-control">{{ trans('messages.deduction-employer-from-employee') }}<span class="text-danger">*</span></label>
                    <div class="radio-boxes form-row p-1 bg-white">
                        <div class="radio-box col-sm-4 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" onclick="getSalaryGroupDetail()" name="deduction_employer_from_employee" id="deduction_employer_from_employee_yes" value="{{config('constants.SELECTION_YES')}}">
                                <label class="form-check-label custom-type-label btn stock-btn" for="deduction_employer_from_employee_yes">{{ trans('messages.yes') }}</label>
                            </div>
                        </div>
                        <div class="radio-box col-sm-4 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" onclick="getSalaryGroupDetail()" name="deduction_employer_from_employee" id="deduction_employer_from_employee_no" value="{{config('constants.SELECTION_NO')}}" checked>
                                <label class="form-check-label custom-type-label btn stock-btn" for="deduction_employer_from_employee_no">{{ trans('messages.no') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php */ ?>
            
            <div class="col-xl-4 col-sm-6 assign-salary" style="display:none;">
                <div class="form-group">
                    <label for="deduction_of_pf" class="lable-control">{{ trans('messages.deduction-of-pf') }}<span class="text-danger">*</span></label>
                    <div class="radio-boxes form-row p-1 bg-white">
                        <div class="radio-box col-sm-4 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio"  name="deduction_of_pf" id="deduction_of_pf_yes" value="{{config('constants.SELECTION_YES')}}">
                                <label class="form-check-label custom-type-label btn stock-btn" for="deduction_of_pf_yes">{{ trans('messages.yes') }}</label>
                            </div>
                        </div>
                        <div class="radio-box col-sm-4 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio"  name="deduction_of_pf" id="deduction_of_pf_no" value="{{config('constants.SELECTION_NO')}}" checked>
                                <label class="form-check-label custom-type-label btn stock-btn" for="deduction_of_pf_no">{{ trans('messages.no') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
 
            <div class="col-xl-7 pt-4 assign-salary" style="display:none;">
                <div class="form-group">
                    <h4 class="address-title">Salary Breakup</h4>
                    <div class="row pt-4">
                        <div class="col-lg-12 salary-break-up-html">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 assign-salary net-pay-table-div" style="display:none;">
                <div class="form-group">
                    <div class="row pt-xl-5 pt-0">
                        <div class="col-lg-12 pt-xl-4 pt-2">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <tbody>
                                        <tr>
                                            <th rowspan="2" class="text-left net-pay-class">{{ trans('messages.net-pay') }}</th>
                                            <th class="text-left net-pay-class">Monthly</th>
                                            <th class="text-left net-pay-class">Annually</th>
                                        </tr>
                                        <tr class="net-pay-div" style="display: none;">
                                            <th class="text-left net-pay-class monthly-net-pay">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="monthly-net-pay-amount"></span></th>
                                            <th class="text-left net-pay-class yearly-net-pay">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} <span class="yearly-net-pay-amount"></span></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-3 col-sm-6 assign-salary hold-salary-selection-div" >
                <div class="form-group">
                    <label for="hold_salary" class="lable-control">{{ trans('messages.hold-salary') }}<span class="text-danger">*</span></label>
                    <div class="radio-boxes form-row p-1 bg-white">
                        <div class="radio-box col-sm-4 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="hold_salary" id="hold_salary_yes"  checked value="{{ config('constants.SELECTION_YES') }}">
                                <label class="form-check-label custom-type-label btn stock-btn" for="hold_salary_yes">{{ trans('messages.yes') }}</label>
                            </div>
                        </div>
                        <div class="radio-box col-sm-4 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="hold_salary" id="hold_salary_no"  value="{{ config('constants.SELECTION_NO') }}">
                                <label class="form-check-label custom-type-label btn stock-btn" for="hold_salary_no">{{ trans('messages.no') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9 hold-salary-info-div" >
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-left deduction-title hold-month" style="min-width:80px;">January - 2022</th>
                                            <th class="text-left deduction-title hold-month" style="min-width:80px;">February - 2022</th>
                                            <th class="text-left deduction-title hold-month" style="min-width:80px;">March - 2022</th>
                                            <th class="text-left deduction-title hold-month" style="min-width:80px;">April - 2022</th>
                                            <th class="text-left deduction-title hold-month" style="min-width:80px;">May - 2022</th>
                                            <th class="text-left deduction-title hold-month" style="min-width:80px;">Jun - 2022</th>
                                            <th class="text-left deduction-title" style="min-width:80px;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-left"><input type="text" class="form-control hold-salary-amount" onkeyup="onlyNumber(this);" onchange="onlyNumber(this);calculateHoldSalary(this);" name="first_month_hold_salary" placeholder="{{ trans('messages.amount') }}"></td>
                                            <td class="text-left"><input type="text" class="form-control hold-salary-amount" onkeyup="onlyNumber(this);" onchange="onlyNumber(this);calculateHoldSalary(this);"  name="second_month_hold_salary" placeholder="{{ trans('messages.amount') }}"></td>
                                            <td class="text-left"><input type="text" class="form-control hold-salary-amount" onkeyup="onlyNumber(this);" onchange="onlyNumber(this);calculateHoldSalary(this);"  name="third_month_hold_salary" placeholder="{{ trans('messages.amount') }}"></td>
                                            <td class="text-left"><input type="text" class="form-control hold-salary-amount" onkeyup="onlyNumber(this);" onchange="onlyNumber(this);calculateHoldSalary(this);"  name="fourth_month_hold_salary" placeholder="{{ trans('messages.amount') }}"></td>
                                            <td class="text-left"><input type="text" class="form-control hold-salary-amount" onkeyup="onlyNumber(this);" onchange="onlyNumber(this);calculateHoldSalary(this);"  name="fifth_month_hold_salary" placeholder="{{ trans('messages.amount') }}"></td>
                                            <td class="text-left"><input type="text" class="form-control hold-salary-amount" onkeyup="onlyNumber(this);" onchange="onlyNumber(this);calculateHoldSalary(this);"  name="six_month_hold_salary" placeholder="{{ trans('messages.amount') }}"></td>
                                            <td class="total-hold-salary-amount font-weight-bold">0</td>
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