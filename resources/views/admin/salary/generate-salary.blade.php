@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.salary-calculation") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
	        <div class="hide-show-export-excel-class" style="display: none;">
	        	<button type="button" title="{{ trans('messages.export-excel') }}" onclick="exportData()" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2 "><i class="fas fa-file-excel mr-0 mr-sm-2 fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
	        </div>
	        @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none">{{ trans("messages.filter") }}</span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history salary-report">
        {!! Form::open(array( 'id '=> 'generate-salary-form' , 'method' => 'post' ,  'url' => 'salary/generate-salary')) !!}
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 depedent-row">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-12">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans("messages.search-by") }}</label>
                            <input type="text" name="search_by" class="form-control twt-enter-search" placeholder="{{ trans('messages.search-by') }} {{ trans('messages.account-number') }}">
                        </div>
                    </div>
                    @if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_SALARY_CALCULATION'), session()->get('user_permission')  ) ) ) ) )
					<div class="col-xl-2 col-lg-4 col-12">
					<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' ) );?>
					</div>
					<div class="col-xl-3 col-lg-4 col-12">
					<?php echo statusWiseEmployeeList( 'search_employee' ,  (isset($employeeDetails) ? $employeeDetails : [] ) );?>
					</div>
                    @endif
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team" onchange="filterData(this)";>
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(count($teamDetails) > 0 )
                                	@foreach($teamDetails as $teamDetail)
                                		@php 
                                		$encodeTeamId = Wild_tiger::encode($teamDetail->i_id);
                                		@endphp
                                		<option value="{{ $encodeTeamId  }}">{{ $teamDetail->v_value }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_designation" class="control-label">{{ trans('messages.designation') }}</label>
                            <select class="form-control select2" name="search_designation" onchange="filterData(this)";>
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(count($designationDetails) > 0 )
                                	@foreach($designationDetails as $designationDetail)
                                		@php 
                                		$encodeTeamId = Wild_tiger::encode($designationDetail->i_id);
                                		@endphp
                                		<option value="{{ $encodeTeamId  }}">{{ $designationDetail->v_value }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">
                        <div class="form-group">
                            <label class="control-label" for="search_start_month">{{ trans("messages.month") }}</label>
                            <input type="text" name="search_start_month" value="{{ ( isset($startMonth) ? $startMonth : '' ) }}" class="form-control" placeholder="{{ trans('messages.mm-yyyy') }}">
                        </div>
                    </div>
                    <?php /* ?>
                    <div class="col-lg-2 col-md-6 col-6">
                        <div class="form-group">
                            <label class="control-label" for="search_end_month">{{ trans("messages.end-month") }}</label>
                            <input type="text" name="search_end_month" value="{{ ( isset($endMonth) ? $endMonth : '' ) }}"  class="form-control" placeholder="{{ trans('messages.mm-yyyy') }}">
                        </div>
                    </div>
                    <?php */ ?>
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_salary_generate_status">{{ trans("messages.payslip-generated") }}</label>
                            <select class="form-control" name="search_salary_generate_status" onchange="filterData(this);{{ (!in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) ? 'hideShowExportExcelButton(this);' : '') }}">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{config('constants.SELECTION_YES')}}">{{ trans("messages.yes") }}</option>
                                <option value="{{config('constants.SELECTION_NO')}}">{{ trans("messages.no") }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData(this)";>{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
        	{{ Wild_tiger::readMessage() }}
            <div class="card card-body generate-salary-list">
            	<div class="table-responsive fixed-tabel-body append-btn-table">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <div class="form-group mb-0 text-center">
                                        <div class="checkbox-panel salary-cal-checkbox">
                                            <?php /* <input class="form-check-input" type="checkbox" id="check_all" onclick="selectAllRowCheckbox(this);">
                                            <label class="form-check-label lable-control" for="check_all"></label> */ ?>
											<label for="check_all" class="lable-control d-block"></label>
											<div class="form-check form-check-inline mr-0">
												<label class="checkbox" for="check_all">
												<input type="checkbox"  onclick="selectAllRowCheckbox(this)" id="check_all"><span class="checkmark"></span></label>
											</div>
                                        </div>
                                    </div>
                                </th>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th style="min-width:80px;width:auto;">{{ trans("messages.month") }}</th>
                                <th class="text-left employee-name-code-th" style="width:165px;min-width:165px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.designation") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.bank") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.account-number") }}</th>
                                <th class="text-left" style="width:120px;min-width:100px;">{{ trans("messages.working-days") }}</th>
                                <th class="text-left" style="min-width:110px;">{{ trans("messages.attend-days") }}</th>
                                <th class="text-left" style="min-width:110px;">{{ trans("messages.paid-half-leave-short-form") }}</th>
                                <th class="text-left" style="min-width:110px;">{{ trans("messages.paid-full-leave-short-form") }}</th>
                                <th class="text-left" style="min-width:110px;">{{ trans("messages.total-paid-days") }}</th>
                                <th class="text-left" style="min-width:90px;">{{ trans("messages.salary") }}</th>
                                <th class="text-left" style="min-width:90px;">{{ trans("messages.full-day") }}</th>
                                <th class="text-left" style="min-width:90px;">{{ trans("messages.half-day") }}</th>
                                <?php
                                $earningHeadWithoutPFHtml = "";
                                $earningHeadHtml = ""; 
                                ?>
                                @if(count($earningComponentDetails) > 0 )
                                	@foreach($earningComponentDetails as $earningComponentDetail)
                                		<?php
                                		if(  isset($earningComponentDetail->e_consider_for_pf_calculation) && ( $earningComponentDetail->e_consider_for_pf_calculation == config('constants.SELECTION_YES') ) ){
                                			$earningHeadHtml .= '<th class="text-left" style="min-width:110px;">'. ( isset($earningComponentDetail['v_component_name']) ? $earningComponentDetail['v_component_name'] : '' )  .'</th>';
                                		} else {
                                			$earningHeadWithoutPFHtml .= '<th class="text-left" style="min-width:110px;">'. ( isset($earningComponentDetail['v_component_name']) ? $earningComponentDetail['v_component_name'] : '' )  .'</th>';
                                		}
                                		 
                                		?>
                                		<th class="text-left" style="min-width:90px;">{{  ( isset($earningComponentDetail['v_component_name']) ? $earningComponentDetail['v_component_name'] : '' )  }}</th>
                                	@endforeach
                                	<th class="text-left" style="min-width:90px;">{{ trans("messages.total-earnings") }}</th>
                                	<?php echo $earningHeadHtml ?>
                                	<th class="text-left" style="min-width:90px;">{{ trans("messages.total-earnings") }}</th>
                                	<th class="text-left" style="min-width:90px;">{{ trans("messages.salary") }} <i class="fa fa-info-circle ml-2" data-toggle="tooltip" data-placement="right" title="Total Earning - HRA = Salary"></i></th>
                                @endif
                                
                                <?php $deductComponentHtml = ""; ?>
                                @if(count($deductComponentDetails) > 0 )
                                	@foreach($deductComponentDetails as $deductComponentDetail)
                                		<th class="text-left" style="min-width:110px;">{{  ( isset($deductComponentDetail['v_component_name']) ? $deductComponentDetail['v_component_name'] : '' )  }}</th>
                                	@endforeach
                                	<th class="text-left" style="min-width:90px;">{{ trans("messages.total-deductions") }}</th>
                                @endif
                                <th class="text-left" style="min-width:100px;">{{ trans("messages.total-pay-amount") }}</th>
                                <?php echo $earningHeadWithoutPFHtml ?>
                                <th class="text-left" style="min-width:100px;">{{ trans("messages.net-payable-amount") }}</th>

                            </tr>
                        </thead>
                        <tbody class="ajax-view">
                			@include(config('constants.AJAX_VIEW_FOLDER') .'salary/generate-salary-list')
                		</tbody>
                	</table>
                </div>
                <div class="card card-body sticky-div border-top pb-1">
                    <div class="total-div">
                        <button onclick="viewPendingLeaveForApproved(this)" type="button" <?php echo ( count($recordDetails) > 0 ? '' : 'style=display:none;' ) ?>  class="btn-danger btn btn-sm twt-btn-style  text-white view-pending-leave-button" title="{{ trans('messages.view-pending-leaves') }}">{{ trans("messages.view-pending-leaves") }}</button>
                        <button onclick="autoApprovePendingLeave(this)" type="button"   style="display: none;"  class="bg-primary btn btn-sm twt-btn-style text-white auto-approve-leave-button" title="{{ trans('messages.auto-approve-leaves') }}">{{ trans("messages.auto-approve-leaves") }}</button>
                        <button onclick="generateSalary(this)" type="button" style="display: none;" data-action="{{ config('constants.SAVE_ACTION') }}" class="btn-theme btn btn-sm twt-btn-style text-white save-salary-button" title="{{ trans('messages.save') }}">{{ trans("messages.save") }}</button>
                        <button onclick="generateSalary(this)" type="button"  style="display: none;" data-action="{{ config('constants.SUBMIT_ACTION') }}" class="btn-success btn btn-sm twt-btn-style save-salary-button" title="{{ trans('messages.generate-pay-slip') }}">{{ trans("messages.generate-pay-slip") }}</button>
                        <?php /* ?>
                        <a href="javascript:void(0);" class="btn btn-theme text-white btn-sm twt-btn-style align-items-center" title="{{ trans('messages.delete') }}">{{ trans("messages.delete") }} </a>
                        <?php */ ?>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="button_action" value="">
        {!! Form::close() !!}
    </div>
    <div>
        
    </div>
</main>


<div class="modal fade" id="update-salary-day-count-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.paid-days") }}<span class="twt-custom-modal-header"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'update-salary-day-count-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body">
                    <div class="form-group">
                    	<div class="col-sm-6">
                            <div class="form-group">
                                <label for="outlook_email_id" class="control-label">{{ trans("messages.paid-days") }}</label>
                                <input type="text" name="update_salary_paid_count" onkeyup="onlyDecimal(this);" onchange="onlyDecimal(this);" class="form-control " placeholder="{{ trans('messages.paid-days') }}" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end">
                	<input type="hidden" name="days_paid_salary_record_id" value="">
                    <button type="button" onclick="updateSalaryDayValue(this)" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

{!! Form::open(array( 'id '=> 'view-pending-leave-form' , 'target' => '_blank' ,  'method' => 'post' ,  'url' => 'leave/viewPendingLeave')) !!}
<input type="hidden" name="view_pending_leave_employee" value="">
<input type="hidden" name="view_pending_leave_start_date" value="">
<input type="hidden" name="view_pending_leave_end_date" value="">
<input type="hidden" name="view_pending_leave_team" value="">
<input type="hidden" name="view_pending_leave_designation" value="">
{!! Form::close() !!}

<script>
    $(function() {
        $("[name='search_start_month']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            format: '{{ config("constants.DEFAULT_MONTH_FORMAT") }}',
            showClear: true,
            showClose: true,
            widgetPositioning: {
                vertical: 'bottom',
                horizontal: 'auto'

            },
            icons: {
                Close: 'fa fa-trash',
                clear: 'fa fa-trash',
            },
        });
        <?php if(date('d') >= config('constants.SALARY_CYCLE_START_DATE') ) { ?>
       		$("[name='search_start_month']").data('DateTimePicker').maxDate(moment().add(1, "month").endOf('month'));
        <?php } else { ?>
        	$("[name='search_start_month']").data('DateTimePicker').maxDate(moment().endOf('month'));
        <?php } ?>
    	//$("[name='search_end_month']").data('DateTimePicker').maxDate(moment().endOf('month'));
    });

   

    function generateSalary(thisitem){

    	var button_action = $.trim($(thisitem).attr("data-action"));

    	var confirm_popup = '';
    	var confirm_txt = '';

    	switch(button_action){
    		case '{{ config("constants.SAVE_ACTION") }}':
    			confirm_popup = "{{ trans('messages.save-salary') }}";
    			confirm_txt = '{{ trans("messages.common-confirm-msg" , [ "module"  =>  trans("messages.save-salary") ] ) }}';
        		break;
    		case  '{{ config("constants.SUBMIT_ACTION") }}':
    			confirm_popup = "{{ trans('messages.generate-pay-slip') }}";
    			confirm_txt = '{{ trans("messages.common-confirm-msg" , [ "module"  =>  trans("messages.generate-pay-slip") ] ) }}';
        		break;
    	}

    	var selected_checkbox = $(".salary-checkbox:checked").length;

    	if( selected_checkbox == 0 ){
			alertifyMessage('error', '{{ trans("messages.required-atleast-one-record") }}' );
    		return false;
    		
        }
    	
    	
    	
    	alertify.confirm(confirm_popup, confirm_txt,function() {
    		showLoader();
    		$(".pt-salary-head").prop('disabled' , false);
        	$("[name='button_action']").val(button_action);
			$("#generate-salary-form").submit();
    	},function() {});
    }

    $(document).ready(function(){
    	$(".given-salary-amount").prop('readonly' , true);
		$(".salary-value").prop('readonly' , true);
     });

    function calculateTotalSalary(thisitem){
		var total_earning = 0;
		var total_earning_for_pf = 0;
		var total_earning_for_without_pf = 0;
		var total_deduction = 0;
		var hra_amount = 0;

		var view_earning_head_total = 0;

		$( $(thisitem).parents('tr').find('.salary-value.earning-head') ).each(function(){
			var salary_value = $.trim($(this).html());

			if(parseFloat(salary_value) > 0.00 ){
				view_earning_head_total = parseFloat(view_earning_head_total) + parseFloat(salary_value);
			}
		})
		//console.log("view_earning_head_total = " + view_earning_head_total );
		
		
		$( $(thisitem).parents('tr').find('.amount') ).each(function(){
			var earning_class = $(this).hasClass('earning-head');
			var deduction_class = $(this).hasClass('deduct-head');
			var salary_component_id = $.trim($(this).attr('data-component-id'));
			var consider_pf_status = $.trim($(this).attr('data-consider-pf-status')); 

			if( earning_class != false ){
				if(parseFloat($(this).val()) > 0.00 ){
					if( salary_component_id != "" && salary_component_id  != null && salary_component_id == "{{ config('constants.HRA_SALARY_COMPONENT_ID') }}" ){
						hra_amount = $(this).val();
					}
					if( consider_pf_status != "" && consider_pf_status != null && consider_pf_status == "{{ config('constants.SELECTION_YES') }}" ){
						total_earning_for_pf = parseFloat(total_earning_for_pf) + parseFloat($(this).val());
					} else {
						total_earning_for_without_pf = parseFloat(total_earning_for_without_pf) + parseFloat($(this).val());
						
					}
					total_earning = parseFloat(total_earning) + parseFloat($(this).val());
				}
				
			}

			if( deduction_class != false ){
				if(parseFloat($(this).val()) > 0.00 ){
					total_deduction = parseFloat(total_deduction) + parseFloat($(this).val());
				}
			}	
			
			//console.log("earning_class = " + earning_class );
			//console.log("deduction_class = " + deduction_class );
		})

		console.log("total_earning_for_pf = " + total_earning_for_pf );
		console.log("total_earning_for_without_pf = " + total_earning_for_without_pf );
		
		
		total_earning_for_pf = ( parseFloat(total_earning_for_pf) > 0.00 ? parseFloat(total_earning_for_pf).toFixed(round_off_value_decimal) : 0.00 );


		var pt_amount = 0;
		if( parseFloat(total_earning_for_pf) >= "{{ config('constants.PT_AMOUNT_LIMIT') }}" ){
			pt_amount = "{{ config('constants.PT_AMOUNT') }}";
		}

		total_earning = convertAmountIntoDouble(total_earning_for_pf);
		total_deduction = ( parseFloat(total_deduction) > 0.00 ? parseFloat(total_deduction).toFixed(2) : 0.00 );
		total_deduction = convertAmountIntoDouble(total_deduction);

		var total_pay = ( parseFloat(total_earning) -  parseFloat(total_deduction) ) ;
		total_pay = ( parseFloat(total_pay) > 0.00 ? parseFloat(total_pay).toFixed(round_off_value_decimal) : 0.00 );
		total_pay = convertAmountIntoDouble(total_pay);
		console.log("total_pay = " + total_pay );

		total_earning_for_without_pf = ( parseFloat(total_earning_for_without_pf) > 0.00 ? parseFloat(total_earning_for_without_pf).toFixed(round_off_value_decimal) : 0.00 );
		
		console.log("total_earning = " + total_earning );
		console.log("total_deduction = " + total_deduction );
		console.log("total_earning_for_without_pf = " + total_earning_for_without_pf );	
		var net_pay = ( parseFloat(total_earning) -  parseFloat(total_deduction) + parseFloat(total_earning_for_without_pf) ) ;
		net_pay = ( parseFloat(net_pay) > 0.00 ? parseFloat(net_pay).toFixed(round_off_value_decimal) : 0.00 );
		net_pay = convertAmountIntoDouble(net_pay);

		console.log("net_pay = " + net_pay );

		$(thisitem).parents('tr').find('.total-earning-amount').html(displayValueIntoIndianCurrency(total_earning));
		var row_hra_amount = 0.00;
		if( parseFloat(hra_amount) > 0.00 ){
			row_hra_amount = ( parseFloat( total_earning ) - parseFloat(hra_amount) );
			row_hra_amount = ( parseFloat(row_hra_amount) > 0.00 ? parseFloat(row_hra_amount).toFixed(round_off_value_decimal) : 0.00 );
			row_hra_amount = convertAmountIntoDouble(row_hra_amount);
			$(thisitem).parents('tr').find('.salary-after-remove-hra').html(displayValueIntoIndianCurrency(row_hra_amount));
		} else {
			$(thisitem).parents('tr').find('.salary-after-remove-hra').html(displayValueIntoIndianCurrency(total_earning));
		}

		var salary_after_hra = ( parseFloat( total_earning_for_pf ) - parseFloat(hra_amount) );
		salary_after_hra = ( parseFloat(salary_after_hra) > 0.00 ? parseFloat(salary_after_hra).toFixed(round_off_value_decimal) : 0.00 );
		//console.log("total_earning = " + total_earning );
		//console.log("hra_amount = " + hra_amount );
		//console.log("salary_after_hra = " + salary_after_hra );		

		var pf_deduction_status = $.trim($(thisitem).parents('.record-list').attr('data-pf-deduction-status'));
		//console.log("pf_deduction_status = " + pf_deduction_status );		
		//console.log("row_hra_amount = " + row_hra_amount );
		
		var pf_amount = ( ( parseFloat(salary_after_hra) * 0.12 ) );
		pf_amount = ( parseFloat(pf_amount) > 0.00 ? parseFloat(pf_amount).toFixed(round_off_value_decimal) : 0.00 );
		if( parseFloat(pf_amount) > "{{ config('constants.MAXIMUM_ALLOWED_PF_AMOUNT') }}" ){
			pf_amount = "{{ config('constants.MAXIMUM_ALLOWED_PF_AMOUNT') }}";
		}

		
		
		pf_amount = convertAmountIntoDouble(pf_amount);
		if( pf_deduction_status != "" && pf_deduction_status != null  && ( pf_deduction_status == "{{ config('constants.SELECTION_YES') }}" )){
			$(thisitem).parents('tr').find('.pf-salary-head').val((pf_amount));
			if( $('.save-salary-button').is(":visible") != false ){
				$(thisitem).parents('tr').find('.pf-salary-head').prop('readonly' , false);
			} else {
				$(thisitem).parents('tr').find('.pf-salary-head').prop('readonly' , true);
			}
			
			
			
		} else {
			$(thisitem).parents('tr').find('.pf-salary-head').val((0));
			$(thisitem).parents('tr').find('.pf-salary-head').prop('readonly' , true);
		}
		pt_amount = ( parseFloat(pt_amount) > 0.00 ? parseFloat(pt_amount).toFixed(round_off_value_decimal) : 0.00 );
		pt_amount = convertAmountIntoDouble(pt_amount);
		$(thisitem).parents('tr').find('.pt-salary-head').val(pt_amount)
		
		//console.log("pf_amount = " + pf_amount );
		
		
		$(thisitem).parents('tr').find('.total-deduct-amount').html(displayValueIntoIndianCurrency(total_deduction));
		
		$(thisitem).parents('tr').find('.total-pay-amount').html(displayValueIntoIndianCurrency(total_pay));
		$(thisitem).parents('tr').find('.net-amount').html(displayValueIntoIndianCurrency(net_pay));
		
		//console.log("total_earning = " + total_earning );
		//console.log("total_earning = " + total_earning );
		//console.log("row_hra_amount = " + row_hra_amount );

		
		
    }
	var salary_module_url = '{{ config("constants.SALARY_MASTER_URL") }}' + '/';
	var salary_current_row = '';
    function getEmployeeSalaryInfo(thisitem){
		var employee_id = $.trim($(thisitem).attr('data-emp-id'));
		var salary_month = $.trim($(thisitem).attr('data-salary-month'));
		var ament_salary = $.trim($(thisitem).attr('data-ament-salary'));


		var employee_code = $.trim($(thisitem).attr('data-emp-code'));
		var employee_name = $.trim($(thisitem).attr('data-emp-name'));
		
		
		salary_current_row = thisitem;
		
		if( employee_id != "" && employee_id  != null ){

			var component_wise_salary_value = [];
			$($(salary_current_row).parents('tr').find(".salary-value.earning-head.given-salary-amount")).each(function(){
				//console.log($(this));
				//console.log("www");
				var component_id = $.trim($(this).attr('data-component-id'));
				if( component_id != "" && component_id != null ){
					component_wise_salary_value.push({'component_id' : component_id , 'salary_value' :  $.trim($(this).val())});
				}
			})
			$($(salary_current_row).parents('tr').find(".salary-value.deduct-head")).each(function(){
				//console.log($(this));
				//console.log("www");
				var component_id = $.trim($(this).attr('data-component-id'));
				if( component_id != "" && component_id != null ){
					component_wise_salary_value.push({'component_id' : component_id , 'salary_value' :  $.trim($(this).val())});
				}
			})
			
			
			//console.log("component_wise_salary_value");
			//console.log(component_wise_salary_value);
			$.ajax({
				type: "POST",
				url: salary_module_url + 'employees-salary-info',
				data:{ 'ament_salary' : ament_salary ,   'employee_id' : employee_id , 'salary_month' : salary_month , component_wise_salary_value : component_wise_salary_value   },
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response != "" && response != null ){
						response = $.trim(response);
						var custom_twt_modal_header  = employee_name + ' (' +  employee_code + ')';
						$(".employee-salary-html").html(response);
						$("#employee-salary-view").find(".twt-custom-modal-header").html(custom_twt_modal_header);
						openBootstrapModal('employee-salary-view');
						
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
					}
				},
				error: function() {
					hideLoader();
				}
			});
		}
    }

    function calculateModalTotalSalary(thisitem){
		var total_earning = 0;
		var total_deduction = 0;
		$( $(thisitem).parents('.edit-salary-table').find('.amount') ).each(function(){
			var earning_class = $(this).hasClass('earning-head');
			var deduction_class = $(this).hasClass('deduct-head');

			if( earning_class != false ){
				if(parseFloat($(this).val()) > 0.00 ){
					total_earning = parseFloat(total_earning) + parseFloat($(this).val());
				}
			}

			if( deduction_class != false ){
				if(parseFloat($(this).val()) > 0.00 ){
					total_deduction = parseFloat(total_deduction) + parseFloat($(this).val());
				}
			}	
		})

		
		total_earning = ( parseFloat(total_earning) > 0.00 ? parseFloat(total_earning).toFixed(2) : 0.00 );
		total_deduction = ( parseFloat(total_deduction) > 0.00 ? parseFloat(total_deduction).toFixed(2) : 0.00 );
		var net_pay = ( parseFloat(total_earning) -  parseFloat(total_deduction) ) ;
		net_pay = ( parseFloat(net_pay) > 0.00 ? parseFloat(net_pay).toFixed(2) : 0.00 );

		$(thisitem).parents('.edit-salary-table').find('.total-edit-earning-salary').html(total_earning);
		$(thisitem).parents('.edit-salary-table').find('.total-edit-dedcut-salary').html(total_deduction);
		$(thisitem).parents('.edit-salary-table').find('.calculated-net-pay-amount').html(net_pay);
		var net_pay_into_word = convertAmountIntoWords(net_pay);
		$(thisitem).parents('.edit-salary-table').find('.calculate-net-pay-amount-into-word').html(net_pay_into_word);
//calculate-net-pay-amount-into-word
		//$(thisitem).parents('.edit-salary-table').find('.net-amount').html(net_pay);
	}

    function updateModalSalaryValue(){
      //  console.log("current_row ");
       // console.log($(salary_current_row).parents('tr').html());
        
        
    	alertify.confirm('{{ trans("messages.update-salary") }}', '{{ trans("messages.common-confirm-msg" , [ "module"  =>  trans("messages.update-salary") ] ) }}',function() {
	        $( $("#employee-salary-view").find('.amount') ).each(function(){
	        	var component_id = $.trim($(this).attr("data-component-id"));
	        	$(salary_current_row).parents('tr').find(".salary-value[data-component-id='" + component_id + "']").html($(this).val())
	        	$(salary_current_row).parents('tr').find(".salary-value.earning-head[data-component-id='" + component_id + "']").val($(this).val())
	        	$(salary_current_row).parents('tr').find(".salary-value.deduct-head[data-component-id='" + component_id + "']").val($(this).val())
	        })
			$(salary_current_row).parents('tr').find('.amount:first').trigger('change');
			$("#employee-salary-view").modal('hide');
    	},function() {});
    }

	var selected_current_row = '';
    function openUpdateSalaryDayCount(thisitem){
    	selected_current_row = thisitem;
    	var employee_code = $.trim($(thisitem).attr('data-emp-code'));
		var employee_name = $.trim($(thisitem).attr('data-emp-name'));
		var summary_record_id = $.trim($(thisitem).attr('data-summary-record-id')); 

		var custom_twt_modal_header  = employee_name + ' (' +  employee_code + ')';
        
    	var day_value = $.trim($(thisitem).attr('data-value'));
    	$("[name='update_salary_paid_count']").val(day_value);

    	$("#update-salary-day-count-modal").find(".twt-custom-modal-header").html(" - " + custom_twt_modal_header );
    	$("[name='days_paid_salary_record_id']").val(summary_record_id);
    	openBootstrapModal('update-salary-day-count-modal');
    	
	}

    $("#update-salary-day-count-form").validate({
        errorClass: "invalid-input",
        rules: {
        	update_salary_paid_count: {
                required: true, noSpace: true
            }
        },
        messages: {
        	update_salary_paid_count: {
                required: "{{ trans('messages.require-paid-days') }}"
            }
        }
    });
    
    function updateSalaryDayValue(thisitem){
		var day_value = $.trim($(thisitem).attr('data-value'));

		if( $("#update-salary-day-count-form").valid() != true ){
			return false;
		}

		var update_salary_paid_count = $.trim($("[name='update_salary_paid_count']").val());
		var salary_record_id = $.trim($("[name='days_paid_salary_record_id']").val());

		alertify.confirm('{{ trans("messages.update-paid-days") }}', '{{ trans("messages.common-confirm-msg" , [ "module"  =>  trans("messages.update-paid-days") ] ) }}',function() {
			$.ajax({
				type: "POST",
				dataType : 'json',
				url: salary_module_url + 'update-salary-paid-day',
				data:{ 'salary_record_id' : salary_record_id , 'update_salary_paid_count' : update_salary_paid_count },
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						alertifyMessage('success' , response.message );
						$("#update-salary-day-count-modal").modal('hide');
						if( $('.save-salary-button').is(":visible") != false ){
							filterData(null , true );
						} else {
							filterData();
						}
						
					}else if( response.status_code == 101 ){
						alertifyMessage('error' , response.message );
					}
				},
				error: function() {
					hideLoader();
				}
			});
		},function() {});
    }

    function searchField(){
    	var search_by = $.trim($("[name='search_by']").val());
    	var search_employment_status = $.trim($("[name='search_employment_status']").val());
		var search_employee = $.trim($("[name='search_employee']").val());
		var search_team = $.trim($("[name='search_team']").val());
		var search_designation = $.trim($("[name='search_designation']").val());
		var search_start_month = $.trim($("[name='search_start_month']").val());
		var search_end_month = $.trim($("[name='search_end_month']").val());
		var search_salary_generate_status =  $.trim($("[name='search_salary_generate_status']").val());

		var searchData = {
        	'search_by':search_by,
        	'search_employment_status':search_employment_status,
            'search_employee': search_employee,
            'search_team':search_team,
            'search_designation':search_designation,
            'search_start_month':search_start_month,
            'search_end_month':search_end_month,
            'search_salary_generate_status':search_salary_generate_status,
		}
		return searchData;
		

    }
    
    function filterData(thisitem, show_salary_genearte_button = false ){
    	var searchFieldName = searchField();
    	searchFieldName.show_salary_genearte_button = show_salary_genearte_button;
    	searchAjax(salary_module_url + 'filter-salary-generate' , searchFieldName);
    }

    function updateHeadSalary(thisitem){
		var updated_days = $.trim($(thisitem).val());
		if( parseFloat( updated_days ) > 0.00 ){
			$( $(thisitem).parents('tr').find('.amount') ).each(function(){
				var earning_class = $(this).hasClass('earning-head');
				var original_value = $(this).attr('data-original-salary');

				if( earning_class != false ){
					if(parseFloat($(this).val()) > 0.00 ){
						var conveted_value = ( ( parseFloat(updated_days) * parseFloat(original_value) ) / parseFloat('{{ config("constants.SALARY_COUNT_DAYS") }}') ) ; 
						conveted_value = ( parseFloat(conveted_value) > 0.00 ? parseFloat(conveted_value).toFixed(2) : 0.00 );
						$(this).val(conveted_value);
					}
				}
			})
		}
    }

    function viewPendingLeaveForApproved(){

    	var search_employee = $.trim($("[name='search_employee']").val());
		var search_team = $.trim($("[name='search_team']").val());
		var search_designation = $.trim($("[name='search_designation']").val());
		var search_start_month = $.trim($("[name='search_start_month']").val());
		var search_end_month = $.trim($("[name='search_end_month']").val());

		$("[name='view_pending_leave_employee']").val(search_employee);
		$("[name='view_pending_leave_start_date']").val(search_start_month);
		$("[name='view_pending_leave_end_date']").val(search_end_month);
		$("[name='view_pending_leave_team']").val(search_team);
		$("[name='view_pending_leave_designation']").val(search_designation);
		
	    $("#view-pending-leave-form").submit();	

	    $(".auto-approve-leave-button").show();
    	
    }

    function autoApprovePendingLeave(){

    	var search_employee = $.trim($("[name='search_employee']").val());
		var search_team = $.trim($("[name='search_team']").val());
		var search_designation = $.trim($("[name='search_designation']").val());
		var search_start_month = $.trim($("[name='search_start_month']").val());
		var search_end_month = $.trim($("[name='search_end_month']").val());

		alertify.confirm('{{ trans("messages.auto-approve-pending-leaves") }}', '{{ trans("messages.common-confirm-msg" , [ "module"  =>  trans("messages.auto-approve-pending-leaves") ] ) }}',function() {
		
			$.ajax({
				type: "POST",
				dataType : 'json',
				url: salary_module_url + 'auto-approve-pending-leave',
				data:{ 'search_employee' : search_employee , 'search_team' : search_team , 'search_designation' : search_designation , 'search_start_month' : search_start_month , 'search_end_month' : search_end_month   },
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						alertifyMessage('success' , response.message );
						$(".save-salary-button").show();
						filterData(null , true );
					}else if( response.status_code == 101 ){
						alertifyMessage('error' , response.message );
					}
				},
				error: function() {
					hideLoader();
				}
			});
		},function() {});
    }
    
    function exportData(){
		var searchData = searchField();
		var export_info = {};
		export_info.url = salary_module_url + 'filter-salary-generate';
		export_info.searchData = searchData;
		dataExportIntoExcel(export_info);
	}
    
    function hideShowExportExcelButton(thisitem){
    	var value = $.trim($(thisitem).val());
    	if(value != '' && value != null && value == "{{ config('constants.SELECTION_YES') }}"){
            $('.hide-show-export-excel-class').show();
		}else{
			$('.hide-show-export-excel-class').hide();
		}
	}
</script>
@endsection