@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.on-hold-salary-report") }}</h1>
        <span class="head-total-counts total-record-count">4</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
            <button type="button" title="{{ trans('messages.export-excel') }}" onclick="exportData(this);" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel mr-0 mr-sm-2 fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">

        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 depedent-row">
                <div class="row 1233">
	                @if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_ON_HOLD_SALARY_REPORT'), session()->get('user_permission')  ) ) ) ) )
						<div class="col-xl-2 col-lg-4 col-12">
						<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' ) , 'search_employment_status' ,  'on_hold_report'   );?>
						</div>
						<div class="col-xl-3 col-lg-4 col-12">
						<?php echo statusWiseEmployeeList('search_employee' , (isset($employeeDetails) ? $employeeDetails : [] ) );?>
						</div>
					@endif
                    <div class="col-xl-2 col-md-2 col-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team" onchange="filterData(this);">
                                <option value="">{{ trans('messages.select') }}</option>
                                @if(!empty($teamDetails))
                                	@foreach($teamDetails as $teamDetail)
                                		@php $encodeId = Wild_tiger::encode($teamDetail->i_id); @endphp 
                                		<option value="{{ $encodeId }}">{{ (!empty($teamDetail->v_value) ? $teamDetail->v_value :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-2 col-6">
                        <div class="form-group">
                            <label for="search_department" class="control-label">{{ trans('messages.designation') }}</label>
                            <select class="form-control select2" name="search_designation" onchange="filterData(this);">
                                <option value="">{{ trans('messages.select') }}</option>
                                @if(!empty($designationDetails))
                                	@foreach($designationDetails as $designationDetail)
                                		@php $encodeId = Wild_tiger::encode($designationDetail->i_id); @endphp 
                                		<option value="{{ $encodeId }}">{{ (!empty($designationDetail->v_value) ? $designationDetail->v_value :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 col-6">
                        <div class="form-group">
                            <label class="control-label" for="search_joining_from_date">{{ trans("messages.joining-from-date") }}</label>
                            <input type="text" name="search_joining_from_date" class="form-control" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">
                        <div class="form-group">
                            <label class="control-label" for="search_joining_to_date">{{ trans("messages.joining-to-date") }}</label>
                            <input type="text" name="search_joining_to_date" class="form-control" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-6">
                        <div class="form-group">
                            <label for="search_date_filter" class="control-label">{{ trans('messages.month-filter') }}</label>
                            <select class="form-control" name="search_date_filter" onchange="filterData(this);">
                                <option value="">{{ trans('messages.select') }}</option>
                                <option value="{{ config('constants.EXPECTED_RELEASE_DATE') }}">{{ trans("messages.expected-finish-month") }}</option>
                                <option value="{{ config('constants.RELEASE_DATE') }}">{{ trans("messages.finish-month") }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">
                        <div class="form-group">
                            <label class="control-label" for="search_from_month">{{ trans("messages.from-month") }}</label>
                            <input type="text" name="search_from_month" class="form-control" placeholder="{{ trans('messages.mm-yyyy') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">
                        <div class="form-group">
                            <label class="control-label" for="search_to_month">{{ trans("messages.to-month") }}</label>
                            <input type="text" name="search_to_month" class="form-control" placeholder="{{ trans('messages.mm-yyyy') }}">
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-6">
                        <div class="form-group">
                            <label for="search_hold_amount_status" class="control-label">{{ trans('messages.hold-amount-status') }}</label>
                            <select class="form-control" name="search_hold_amount_status" onchange="filterData(this);">
                                <option value="">{{ trans('messages.select') }}</option>
                                @if(!empty($holdAmountStatusDetails))
                                	@foreach($holdAmountStatusDetails as $holdAmountStatusKey =>  $holdAmountStatusDetail)
                                		<option value="{{ $holdAmountStatusKey }}">{{ $holdAmountStatusDetail  }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData(this);">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
            <div class="card card-body">
                <div class="table-responsive fixed-tabel-body append-btn-table">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="width:280px;min-width:280px;">{{ trans("messages.employee-name-code") }} <br> {{ trans("messages.contact-number") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.designation") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.joining-date") }}</th>
                                <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.planned-hold-amount") }}</th>
                                <th class="text-left" style="width:125px;min-width:125px;">{{ trans("messages.deducted-amount") }}</th>
                                <th class="text-left" style="width:95px;min-width:95px;">{{ trans("messages.left-amount") }}</th>
                                <th class="text-left" style="width:170px;min-width:170px;">{{ trans("messages.expected-finish-month") }}</th>
                                <th class="text-left" style="width:110px;min-width:110px;">{{ trans("messages.finish-month") }}</th>
                                <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.hold-amount-status") }}</th>
                                @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )
                                <th class="text-center" style="width:161px;min-width:161px;">{{ trans("messages.actions") }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="ajax-view">
                            @include(config('constants.AJAX_VIEW_FOLDER') .'report/on-hold-salary-report-list')
                        </tbody>
                    </table>
                </div>
                <div class="card card-body sticky-div border-top mt-2">
                    <div class="total-div">
                        <?php /* ?>
                        <p class="total-amount total-hold-amount"> {{ trans("messages.total-pending") }} {{ trans("messages.planned-hold-amount") }} : <span class="total-on-hold-planned-amount"></span></p>
                        <p class="total-amount total-deducted-amount"> {{ trans("messages.total-pending") }} {{ trans("messages.deducted-amount") }} : <span class="total-on-hold-deduct-amount"></span></p>
                        <?php */ ?>
                        <p class="total-amount total-left-amount left-amount-clr"> {{ trans("messages.total") }} {{ trans("messages.left-amount") }} ({{ trans("messages.pending") }}) : <span class="total-on-hold-left-amount"></span></span>
                        <p class="total-amount total-left-amount deducted-not-amount-clr"> {{ trans("messages.total-deducted-amount") }} ({{ trans("messages.not-to-pay") }}) : <span class="total-not-to-pay-amount"></span></span>
                        <p class="total-amount total-left-amount deducted-donated-amount-clr"> {{ trans("messages.total-deducted-amount") }} ({{ trans("messages.donated") }}) : <span class="total-donated-amount"></span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- hold_amount pop up -->
    <div class="modal fade document-folder document-type" id="on-hold-planned-salary-amount-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.planned-hold-amount") }} - <span class="twt-custom-modal-header"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered text-left">
                            <thead>
                                <tr>
                                    <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                    <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.month-year") }}</th>
                                    <th class="text-left" style="width:200px;min-width:188px;">{{ trans("messages.hold-amount") }}</th>
                                </tr>
                            </thead>
                            <tbody class="on-hold-planned-salary-amount-html">
                                
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- deducted_amount pop up -->

    <div class="modal fade document-folder document-type" id="on-hold-deduct-salary-amount-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.deduction-amount-history") }} - <span class="twt-custom-modal-header"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered text-left">
                            <thead>
                                <tr>
                                    <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                    <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.month-year") }}</th>
                                    <th class="text-left" style="width:200px;min-width:188px;">{{ trans("messages.deducted-amount") }}</th>
                                </tr>
                            </thead>
                            <tbody class="on-hold-deduct-salary-amount-html">
                                
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</main>

<script>
$(function() {
    $("[name='search_joining_from_date'],[name='search_joining_to_date']").datetimepicker({
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
    $("[name='search_from_month'],[name='search_to_month']").datetimepicker({
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
            clear: 'fa fa-trash',
            Close: 'fa fa-trash',
        },
    });
});


$(document).ready(function(){
	calculateOnHoldSalarySummary();
})

$(function(){
	$("[name='search_joining_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $(this).val() != "" && $(this).val() != null ){
			var incrementDay = moment((e.date)).startOf('d');
			$("[name='search_joining_to_date']").data('DateTimePicker').minDate(incrementDay);
    	} else {
    		$("[name='search_joining_to_date']").data('DateTimePicker').minDate(false);
        }
		$(this).data("DateTimePicker").hide();
	 	
	});

    $("[name='search_joining_to_date']").datetimepicker().on('dp.change', function(e) {
        if( $(this).val() != "" && $(this).val() != null ){
        	var decrementDay = moment((e.date)).endOf('d');
        	$("[name='search_joining_from_date']").data('DateTimePicker').maxDate(decrementDay);
        } else {
        	$("[name='search_joining_from_date']").data('DateTimePicker').maxDate(false);
        }
        $(this).data("DateTimePicker").hide();
    });

    $("[name='search_from_month']").datetimepicker().on('dp.change', function(e) {
		if( $(this).val() != "" && $(this).val() != null ){
			var incrementDay = moment((e.date)).startOf('month');
			
			$("[name='search_to_month']").data('DateTimePicker').minDate(incrementDay);
    	} else {
    		$("[name='search_to_month']").data('DateTimePicker').minDate(false);
        }
		$(this).data("DateTimePicker").hide();
	 	
	});

    $("[name='search_to_month']").datetimepicker().on('dp.change', function(e) {
        if( $(this).val() != "" && $(this).val() != null ){
        	var decrementDay = moment((e.date)).endOf('month');
        	$("[name='search_from_month']").data('DateTimePicker').maxDate(decrementDay);
        } else {
        	$("[name='search_from_month']").data('DateTimePicker').maxDate(false);
        }
        $(this).data("DateTimePicker").hide();
    });
		
});

function searchField(){
	var search_employment_status = $.trim($("[name='search_employment_status']").val());
	var search_employee = $.trim($("[name='search_employee']").val());
	var search_team = $.trim($("[name='search_team']").val());
	var search_designation = $.trim($("[name='search_designation']").val());
	var search_date_filter = $.trim($("[name='search_date_filter']").val());
	var search_joining_from_date = $.trim($("[name='search_joining_from_date']").val());
	var search_joining_to_date = $.trim($("[name='search_joining_to_date']").val());
	var search_from_month = $.trim($("[name='search_from_month']").val());
	var search_to_month = $.trim($("[name='search_to_month']").val());
	var search_hold_amount_status = $.trim($("[name='search_hold_amount_status']").val());

	var searchData = {
    	'search_employment_status': search_employment_status,
    	'search_employee': search_employee,
        'search_team':search_team,
        'search_designation':search_designation,
        'search_date_filter':search_date_filter,
        'search_joining_from_date':search_joining_from_date,
        'search_joining_to_date':search_joining_to_date,
        'search_from_month':search_from_month,
        'search_to_month':search_to_month,
        'search_hold_amount_status':search_hold_amount_status,
	}
	return searchData;
	

}

function filterData(){
	var searchFieldName = searchField();
	//searchAjax(site_url + 'filterOnHoldSalaryReport' , searchFieldName);

	$.ajax({
		type: "POST",
		url: site_url + 'filterOnHoldSalaryReport',
		data: searchFieldName,
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		beforeSend: function() {
			
			//block ui
			showLoader();
		},
		success: function(response) {
			hideLoader();
			$(".ajax-view").html("");
			$(".ajax-view").html(response);
			calculateOnHoldSalarySummary();
		},
		error: function() {
			hideLoader();
		}
	});
	
}
var paginationUrl = site_url + 'filterOnHoldSalaryReport';

function exportData(){
	var searchData = searchField();
	var export_info = {};
	export_info.url = site_url + 'filterOnHoldSalaryReport';
	export_info.searchData = searchData;
	dataExportIntoExcel(export_info);
}
var report_url = "{{ config('constants.SITE_URL') }}";
function updateOnHoldSalaryStatus(thisitem){
	var row_index = $.trim($(thisitem).parents('tr').find('.sr-index').html());
	var record_id = $.trim($(thisitem).attr('data-record-id'));
	var update_status = $.trim($(thisitem).attr('data-update-status'));

	var confirm_msg = '';
	var confirm_text = '';
	switch(update_status){
		case "{{ config('constants.NOT_TO_PAY_STATUS') }}":
			confirm_msg = '{{ trans("messages.update-mark-as-not-to-pay-status") }}';
			confirm_text = '{{ trans("messages.common-confirm-add-msg" , [ 'module' => trans("messages.update-mark-as-not-to-pay-status")  ]) }}';
			break;
		case "{{ config('constants.DONATED_STATUS') }}":
			confirm_msg = '{{ trans("messages.update-donation-status") }}';
			confirm_text = '{{ trans("messages.common-confirm-add-msg" , [ 'module' => trans("messages.update-donation-status")  ]) }}';
			break;
		default:
			alertifyMessage('error' , "{{ trans('messages.system-error') }}");
			return false;
	}
	
	
	 alertify.confirm( confirm_msg ,  confirm_text ,function() {
		 $.ajax({
 	 		type: "POST",
 	 		url: report_url + 'updte-on-hold-salary-status',
 	 		dataType : 'json',
 	 		data: {
 	 			"_token": "{{ csrf_token() }}",
 	 			'record_id':record_id,
 	 			'row_index' : row_index ,
 	 			'update_status' : update_status 
 	 		},
 	 		beforeSend: function() {
 	 			//block ui
 	 			showLoader();
 	 		},
 	 		success: function(response) {
 	 	 		hideLoader();

 	 	 		if( response.status_code == 1 ){
 	 	 			alertifyMessage('success' , response.message);
 	 	 			$(thisitem).parents('tr').html(response.data.html);
 	 	 			calculateOnHoldSalarySummary();
				} else {
     	 	 		alertifyMessage('error' , response.message);
         	 	}
 	 	 	},
 	 		error: function() {
 	 			hideLoader();
 	 		}
 	 	});
	 },function() {});
}

function showDeductAmountHistory(thisitem){
	var record_id = $.trim($(thisitem).attr('data-record-id'));
	var employee_name = $.trim($(thisitem).attr('data-emp-name'));
	var employee_code = $.trim($(thisitem).attr('data-emp-code'));
	var custom_modal_header = employee_name + ' (' +  employee_code + ')';

	$.ajax({
		type: "POST",
		url: site_url + 'deducted-on-hold-salary-history',
		data: { 'record_id' : record_id  },
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		beforeSend: function() {
			//block ui
			showLoader();
		},
		success: function(response) {
			hideLoader();
			$(".on-hold-deduct-salary-amount-html").html("");
			$(".on-hold-deduct-salary-amount-html").html(response);
			$("#on-hold-deduct-salary-amount-modal").find('.twt-custom-modal-header').html(custom_modal_header);
			openBootstrapModal('on-hold-deduct-salary-amount-modal');
		},
		error: function() {
			hideLoader();
		}
	});
	
}

function showPlannedAmountHistory(thisitem){
	var record_id = $.trim($(thisitem).attr('data-record-id'));
	var employee_name = $.trim($(thisitem).attr('data-emp-name'));
	var employee_code = $.trim($(thisitem).attr('data-emp-code'));
	var custom_modal_header = employee_name + ' (' +  employee_code + ')';

	$.ajax({
		type: "POST",
		url: site_url + 'planned-on-hold-salary-history',
		data: { 'record_id' : record_id  },
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		beforeSend: function() {
			//block ui
			showLoader();
		},
		success: function(response) {
			hideLoader();
			$(".on-hold-planned-salary-amount-html").html("");
			$(".on-hold-planned-salary-amount-html").html(response);
			$("#on-hold-planned-salary-amount-modal").find('.twt-custom-modal-header').html(custom_modal_header);
			openBootstrapModal('on-hold-planned-salary-amount-modal');
		},
		error: function() {
			hideLoader();
		}
	});
	
}


function calculateOnHoldSalarySummary(){
	var total_planned_hold_amount = 0;
	var total_deduct_amount = 0;
	var total_not_to_pay_amount = 0;
	var total_donated_amount = 0;
	$(".ajax-view tr").each(function(){
		var planned_amount = $.trim($(this).find(".planned-hold-amount").attr("data-amount"));
		var deduct_amount = $.trim($(this).find(".deduct-hold-amount").attr("data-amount"));
		var record_status = $.trim($(this).find(".record-status").attr("data-status"));

		if( record_status != "" && record_status != null && record_status == "{{ config('constants.PENDING_STATUS') }}"  ){
			if( parseFloat(planned_amount) > 0.00 ){
				total_planned_hold_amount = parseFloat(planned_amount) + parseFloat(total_planned_hold_amount);
			}

			if( parseFloat(deduct_amount) > 0.00 ){
				total_deduct_amount = parseFloat(deduct_amount) + parseFloat(total_deduct_amount);
			}
		}
		if( record_status != "" && record_status != null && record_status == "{{ config('constants.NOT_TO_PAY_STATUS') }}"  ){
			var amount = $.trim($(this).find(".record-status").attr("data-amount"));
			if( parseFloat(amount) > 0.00 ){
				total_not_to_pay_amount = parseFloat(total_not_to_pay_amount) + parseFloat(amount);
			}
		}
		if( record_status != "" && record_status != null && record_status == "{{ config('constants.DONATED_STATUS') }}"  ){
			var amount = $.trim($(this).find(".record-status").attr("data-amount"));
			if( parseFloat(amount) > 0.00 ){
				total_donated_amount = parseFloat(total_donated_amount) + parseFloat(amount);
			}
		}

	})

	total_deduct_amount = ( parseFloat(total_deduct_amount) > 0.00 ? parseFloat(total_deduct_amount).toFixed(2) : 0.00 );
	total_planned_hold_amount = ( parseFloat(total_planned_hold_amount) > 0.00 ? parseFloat(total_planned_hold_amount).toFixed(2) : 0.00 );

	var total_left_amount = parseFloat(total_planned_hold_amount) - parseFloat(total_deduct_amount);
	total_left_amount = ( parseFloat(total_left_amount) > 0.00 ? parseFloat(total_left_amount).toFixed(2) : 0.00 );

	total_donated_amount = ( parseFloat(total_donated_amount) > 0.00 ? parseFloat(total_donated_amount).toFixed(2) : 0.00 );
	total_not_to_pay_amount = ( parseFloat(total_not_to_pay_amount) > 0.00 ? parseFloat(total_not_to_pay_amount).toFixed(2) : 0.00 );
	

	$(".total-on-hold-planned-amount").html(displayValueIntoIndianCurrency(total_planned_hold_amount));
	$(".total-on-hold-deduct-amount").html(displayValueIntoIndianCurrency(total_deduct_amount));
	$(".total-on-hold-left-amount").html(displayValueIntoIndianCurrency(total_left_amount));
	$(".total-not-to-pay-amount").html(displayValueIntoIndianCurrency(total_not_to_pay_amount));
	$(".total-donated-amount").html(displayValueIntoIndianCurrency(total_donated_amount));
}
</script>

@endsection