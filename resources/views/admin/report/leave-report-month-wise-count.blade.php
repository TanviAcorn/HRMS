@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.leave-report-month-wise-count") }}</h1>
        <span class="head-total-counts total-record-count">1</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
            <button type="button" title="{{ trans('messages.export-excel') }}" onclick="exportData();" data-action="excel" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel fa-fw mr-0 mr-sm-2"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 depedent-row">
                <div class="row">
                	@if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_LEAVE_REPORT_MONTH_WISE_COUNT'), session()->get('user_permission')  ) ) ) ) )
					<div class="col-xl-2 col-lg-4 col-12">
					<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' )  , (isset($allPermissionId) ? $allPermissionId : '' )  );?>
					</div>
					<div class="col-xl-3 col-lg-4 col-12">
					<?php echo statusWiseEmployeeList('search_employee_name_code' , (isset($employeeDetails) ? $employeeDetails : [] ));?>
					</div>
					@endif
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team" onchange="filterData();">
                               <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($teamDetails))
                                	@foreach($teamDetails as $teamDetail)
                                		@php $encodeTeamId = Wild_tiger::encode($teamDetail->i_id) @endphp
                                		<option value="{{ $encodeTeamId }}">{{(!empty($teamDetail->v_value) ? $teamDetail->v_value :'')}}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group col-lg-2 col-md-3 col-sm-6 col-6">
                        <label for="search_year" class="control-label">{{ trans("messages.year") }}</label>
                         <select class="form-control" name="search_year" onchange="filterData();">
		                	@if(count($yearDetails) > 0 )
		                    	@foreach($yearDetails as $key => $year)
		                    		<option value="{{ $key }}">{{ $year }}</option>
		                    	@endforeach
		                    @endif
		                </select>
                    </div>
                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData();">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper ajax-view">
        	@include( config('constants.AJAX_VIEW_FOLDER') . 'report/leave-report-month-wise-count-list')
        </div>
    </div>


</main>


<script>
$(document).ready(function(){
	filterData();
})
var leave_report_month_url = '{{config("constants.LEAVE_REPORT_MONTH_WISE_COUNT_URL")}}' + '/';
function searchField(){
	
	var search_employee_name_code = $.trim($('[name="search_employee_name_code"]').val());
	var search_team = $.trim($('[name="search_team"]').val());
	var search_year = $.trim($('[name="search_year"]').val());
	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
	
	var searchData = {
        'search_employee_name_code':search_employee_name_code,
        'search_team': search_team,
    	'search_year':search_year,
    	'search_employment_status':search_employment_status
	}
	return searchData;
}
function filterData(thisitem = null){
	var searchFieldName = searchField();
	var button_action = $.trim($(thisitem).attr("data-action"));

	searchAjax(leave_report_month_url + 'leaveReportMonthFilter' , searchFieldName);
}

function exportData(){
	var searchData = searchField();
	var export_info = {};
	export_info.url = leave_report_month_url + 'leaveReportMonthFilter';
	export_info.searchData = searchData;
	dataExportIntoExcel(export_info);
}

var paginationUrl = leave_report_month_url + 'leaveReportMonthFilter'
pagination_view_html = 'pagination-view-html';
</script>


@endsection