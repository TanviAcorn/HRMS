@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.salary-report-for-account-team") }}</h1>
        <span class="head-total-counts total-record-count">3</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
            <button type="button" title="{{ trans('messages.export-excel') }}" onclick="exportData(this);" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel fa-fw mr-0 mr-sm-2"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history salary-report-account-team">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 depedent-row">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-12">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans("messages.search-by") }}</label>
                            <input type="text" name="search_by" class="form-control" placeholder="{{ trans('messages.search-by') }} {{ trans('messages.account-number') }}">
                        </div>
                    </div>
                    @if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_SALARY_REPORT_FOR_ACCOUNT_TEAM'), session()->get('user_permission')  ) ) ) ) )
						<div class="col-xl-2 col-lg-4 col-12">
						<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' )  , (isset($allPermissionId) ? $allPermissionId : '' ) );?>
						</div>
						<div class="col-xl-3 col-lg-4 col-12">
						<?php echo statusWiseEmployeeList('search_employee' , (isset($employeeDetails) ? $employeeDetails : [] ));?>
						</div>
						
						<div class="col-xl-2 col-md-3 col-sm-6">
	                        <div class="form-group">
	                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
	                            <select class="form-control select2" name="search_team" onchange="filterData()">
	                                <option value="">{{ trans("messages.select") }}</option>
	                                <?php 
	                                if(!empty($teamRecordDetails)){
	                                	foreach ($teamRecordDetails as $teamRecordDetail){
	                                		$encodeId = Wild_tiger::encode($teamRecordDetail->i_id);
	                                		?>
	                                		<option value="{{ $encodeId }}">{{ (!empty($teamRecordDetail->v_value) ? $teamRecordDetail->v_value :'')}}</option>
	                                		<?php 
	                                		
	                                	}
	                                }
	                                ?>
	                            </select>
	                        </div>
	                    </div>
					@endif
					
					<div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.bank') }}</label>
                            <select class="form-control" name="search_bank" onchange="filterData(this);">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.HDFC_BANK') }}">{{ trans("messages.hdfc-bank") }}</option>
                                <option value="{{ config('constants.OTHER_BANK') }}">{{ trans("messages.other-bank") }}</option>
                               
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">
                        <div class="form-group">
                            <label class="control-label" for="search_start_month">{{ trans("messages.from-month") }}</label>
                            <input type="text" name="search_start_month" class="form-control" value="{{ ( isset($startMonth) ? $startMonth : '' ) }}" placeholder="{{ trans('messages.mm-yyyy') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">
                        <div class="form-group">
                            <label class="control-label" for="search_end_month">{{ trans("messages.to-month") }}</label>
                            <input type="text" name="search_end_month" class="form-control" value="{{ ( isset($endMonth) ? $endMonth : '' ) }}" placeholder="{{ trans('messages.mm-yyyy') }}">
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
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-center sr-col">{{ trans("messages.month") }}</th>
                                <th class="text-left" style="min-width:165px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="min-width:120px;">{{ trans("messages.bank") }}</th>
                                <th class="text-left" style="min-width:120px;">{{ trans("messages.account-number") }}</th>
                                <th class="text-left" style="min-width:165px;">{{ trans("messages.net-pay") }}</th>
                                <th class="text-left" style="min-width:165px;">{{ trans("messages.professional-tax") }}</th>
                            </tr>
                        </thead>
                        <tbody class="ajax-view">
                            @include(config('constants.AJAX_VIEW_FOLDER') .'report/salary-report-for-account-team-list')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</main>
<script>

$(function() {
    $("[name='search_start_month'],[name='search_end_month']").datetimepicker({
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
    

	<?php if( isset($endMonth) && (!empty($endMonth)) ) { ?>
    $("[name='search_start_month']").data('DateTimePicker').maxDate(moment('<?php echo date('Y-m-d' , strtotime($endMonth) ) ?>' , 'YYYY-MM-DD').endOf('month'));
    <?php } ?>
    <?php if( isset($startMonth) && (!empty($startMonth)) ) { ?>
	$("[name='search_end_month']").data('DateTimePicker').minDate(moment('<?php echo date('Y-m-d' , strtotime($startMonth) ) ?>' , 'YYYY-MM-DD').startOf('month'));
	<?php } ?>
});

$(function(){
	$("[name='search_start_month']").datetimepicker().on('dp.change', function(e) {
		if( $(this).val() != "" && $(this).val() != null ){
			var incrementDay = moment((e.date)).startOf('d');
			$("[name='search_end_month']").data('DateTimePicker').minDate(incrementDay);
    	} else {
    		$("[name='search_end_month']").data('DateTimePicker').minDate(false);
        }
		$(this).data("DateTimePicker").hide();
	 	
	});

    $("[name='search_end_month']").datetimepicker().on('dp.change', function(e) {
        if( $(this).val() != "" && $(this).val() != null ){
        	var decrementDay = moment((e.date)).endOf('d');
        	$("[name='search_start_month']").data('DateTimePicker').maxDate(decrementDay);
        } else {
        	$("[name='search_start_month']").data('DateTimePicker').maxDate(false);
        }
        $(this).data("DateTimePicker").hide();
    });
		
});

function searchField(){
	var search_by = $.trim($("[name='search_by']").val());
	var search_employment_status = $.trim($("[name='search_employment_status']").val());
	var search_employee = $.trim($("[name='search_employee']").val());
	var search_team = $.trim($("[name='search_team']").val());
	var search_start_month  = $.trim($("[name='search_start_month']").val());
	var search_end_month  = $.trim($("[name='search_end_month']").val());
	var search_bank   = $.trim($("[name='search_bank']").val());
	
	
	var searchData = {
    	'search_by':search_by,
    	'search_employment_status': search_employment_status,
        'search_employee': search_employee,
        'search_team': search_team,
        'search_start_month': search_start_month,
        'search_end_month': search_end_month,
        'search_bank': search_bank,
     }
	return searchData;
	

}

function filterData(){
	var searchFieldName = searchField();
	searchAjax(site_url + 'filterAccountTeamSalaryReport' , searchFieldName);
}
var paginationUrl = site_url + 'filterAccountTeamSalaryReport';

function exportData(){
	var searchData = searchField();
	var export_info = {};
	export_info.url = site_url + 'filterAccountTeamSalaryReport';
	export_info.searchData = searchData;
	dataExportIntoExcel(export_info);
}
</script>

<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection