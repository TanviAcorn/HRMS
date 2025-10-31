@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.statutaroy-bonus-report") }}</h1>
        <span class="head-total-counts total-record-count">0</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
            <button type="button" title="{{ trans('messages.export-excel') }}" onclick="exportData(this);"  class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel mr-0 mr-sm-2 fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 depedent-row">
                <div class="row">
                	@if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_STATUTORY_REPORT'), session()->get('user_permission')  ) ) ) ) )
					<div class="col-xl-2 col-lg-4 col-12">
					<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' )  );?>
					</div>
					<div class="col-xl-3 col-lg-4 col-12">
					<?php echo statusWiseEmployeeList('search_employee_name_code' , (isset($employeeDetails) ? $employeeDetails : [] ));?>
					</div>
					@endif
                    <div class="col-xl-2 col-md-3 col-sm-6">
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

                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_year" class="control-label">{{ trans('messages.financial-year') }}</label>
                            <select class="form-control" name="search_year" onchange="filterData();">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(count($yearDetails) > 0 )
		                    	@foreach($yearDetails as $key => $year)
		                    		@php
		                    		$displayYear = "";
		                    		$getFinancialYear = getCurrentFinancialYear($key);
		                    		if((!empty($getFinancialYear))){
		                    			$getFinancialYearArray = explode("-" , $getFinancialYear );
		                    			if( isset($getFinancialYearArray[0]) && (!empty($getFinancialYearArray[0])) ){
		                    				$displayYear .= "Apr-".$getFinancialYearArray[0];
		                    			}
		                    			if( isset($getFinancialYearArray[1]) && (!empty($getFinancialYearArray[1])) ){
		                    				$displayYear .=  " to ". "Mar-".$getFinancialYearArray[1];
		                    			}
		                    		}
		                    		@endphp
		                    		<option value="{{ $key }}">{{ $displayYear }}</option>
		                    	@endforeach
		                    @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData();">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper ajax-view">
        	@include( config('constants.AJAX_VIEW_FOLDER') . 'report/statutory-report-list')
        </div>
    </div>


</main>


<script>
pagination_view_html = 'pagination-view-html';
$(document).ready(function(){
	filterData();
})

var statutory_bonus_report_url = '{{config("constants.STATUTORY_BONUS_REPORT_URL")}}' + '/';

function searchField(){

	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
	var search_employee_name_code = $.trim($('[name="search_employee_name_code"]').val());
	var search_team = $.trim($('[name="search_team"]').val());
	var search_year =  $.trim($('[name="search_year"]').val());

	var searchData = {
		'search_employment_status':search_employment_status,
        'search_employee_name_code':search_employee_name_code,
        'search_team': search_team,
    	'search_year':search_year,
    }
	return searchData;
}
function filterData(){

	var searchFieldName = searchField();
	searchAjax(statutory_bonus_report_url + 'statutoryBonusReportFilter' , searchFieldName);
}
var paginationUrl = statutory_bonus_report_url + 'statutoryBonusReportFilter';

function exportData(){
	var searchData = searchField();
	var export_info = {};
	export_info.url = statutory_bonus_report_url + 'statutoryBonusReportFilter';
	export_info.searchData = searchData;
	dataExportIntoExcel(export_info);
}

</script>

<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection