@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.salary-increment-report") }}</h1>
        <span class="head-total-counts total-record-count">1</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
            <button type="button" title="{{ trans('messages.export-excel') }}" onclick="exportData(this);" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel fa-fw mr-0 mr-sm-2"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none">{{ trans("messages.filter") }}</span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history salary-report">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 depedent-row">
                <div class="row">
                	@if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_INCREMENT_SALARY_REPORT'), session()->get('user_permission')  ) ) ) ) )
	                	<div class="col-xl-2 col-lg-4 col-12">
	                		<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' )  );?>
	                	</div>
	                    <div class="col-xl-3 col-lg-4 col-12">
	                    	<?php echo statusWiseEmployeeList('search_employee' , (isset($employeeDetails) ? $employeeDetails : [] ));?>
	                    </div>
					@endif
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team" onchange="filterData(this);">
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
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_designation" class="control-label">{{ trans('messages.designation') }}</label>
                            <select class="form-control select2" name="search_designation" onchange="filterData(this);">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($designationDetails))
                                	@foreach($designationDetails as $designationDetail)
                                		@php $encodeId = Wild_tiger::encode($designationDetail->i_id); @endphp 
                                		<option value="{{ $encodeId }}">{{ (!empty($designationDetail->v_value) ? $designationDetail->v_value :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_year" class="control-label">{{ trans('messages.year') }}</label>
                            <select class="form-control" name="search_year" onchange="filterData(this);">
                                @if(!empty($yearDetails))
                                	@foreach($yearDetails as $yearKey =>  $yearDetail)
                                		@php 
                                		$selected = "";
                                		if( isset($selectedYear) && ( $selectedYear == $yearKey ) ){
                                			$selected = "selected='selected'";
                                		}
                                		@endphp 
                                		<option value="{{ $yearKey }}" {{ $selected }} >{{ $yearKey  }}</option>
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
        <div class="filter-result-wrapper ajax-view">
        	@include( config('constants.AJAX_VIEW_FOLDER') . 'report/salary-increment-report-list')
        </div>
    </div>
</main>


<script>

var salary_increment_report_url = '{{config("constants.SITE_URL")}}' ;

function searchField(){
	
	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
	var search_employee = $.trim($('[name="search_employee"]').val());
	var search_team = $.trim($('[name="search_team"]').val());
	var search_designation = $.trim($('[name="search_designation"]').val());
	var search_year =  $.trim($('[name="search_year"]').val());
	
	var searchData = {
		'search_employment_status':search_employment_status,
        'search_employee':search_employee,
        'search_team': search_team,
        'search_designation': search_designation,
    	'search_year':search_year,
    }
	return searchData;
}
function filterData(){

	var searchFieldName = searchField();

	searchAjax(salary_increment_report_url + 'filterSalaryIncrementReport' , searchFieldName);
}

var paginationUrl = salary_increment_report_url + 'filterSalaryIncrementReport';

pagination_view_html = 'pagination-view-html';

function exportData(){
	var searchData = searchField();
	var export_info = {};
	export_info.url = salary_increment_report_url + 'filterSalaryIncrementReport';
	export_info.searchData = searchData;
	dataExportIntoExcel(export_info);
}

</script>


@endsection