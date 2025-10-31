@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.hr-attendance-day-wise") }}</h1>
        <span class="head-total-counts total-record-count">0</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
            <button type="button" title="{{ trans('messages.export-excel') }}" onclick="exportData(this);" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel mr-0 mr-sm-2 fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <?php
        $tableSearchPlaceholder = "Search By Employee Code, Employee Name";
        ?>
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 depedent-row">
                <div class="row">
                	@if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_ATTENDANCE_REPORT_DAY_WISE'), session()->get('user_permission')  ) ) ) ) )
					<div class="col-xl-2 col-lg-4 col-12">
					<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' )  , (isset($allPermissionId) ? $allPermissionId : '' ) );?>
					</div>
					<div class="col-xl-3 col-lg-4 col-12">
					<?php echo statusWiseEmployeeList('search_employee' , (isset($employeeDetails) ? $employeeDetails : [] ) );?>
					</div>
					@endif
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_attendance_month">{{ trans("messages.month") }}</label>
                            <input type="text" name="search_attendance_month" value="{{ date('M-Y')}}" class="form-control" placeholder="{{ trans('messages.mm-yyyy') }}">
                        </div>
                    </div>
                    @if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_ATTENDANCE_REPORT_DAY_WISE'), session()->get('user_permission')  ) ) ) ) )
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team" onchange="filterData(this);">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($teamDetails))
                                	@foreach($teamDetails as $teamDetail)
                                		@php $encodeId = Wild_tiger::encode($teamDetail->i_id); @endphp 
                                		<option value="{{ $encodeId }}">{{ (!empty($teamDetail->v_value) ? $teamDetail->v_value :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif


                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData(this);">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper ajax-view">
            @include(config('constants.AJAX_VIEW_FOLDER') .'report/attendance-report-day-wise-list')
        </div>
    </div>


</main>


<script>
    $("[name='search_attendance_month']").datetimepicker({
        useCurrent: false,
        ignoreReadonly: true,
        format: '{{ config("constants.DEFAULT_MONTH_FORMAT") }}',
        showClose: true,
        icons: {
            clear: 'fa fa-trash',
        },
        maxDate : moment().endOf('month'),
        widgetPositioning: {
            horizontal: 'auto',
            vertical: 'bottom'
        },
    });

    $(document).ready(function(){
    	filterData();
    });


function searchField(){

	var search_employment_status = $.trim($("[name='search_employment_status']").val());
	var search_employee = $.trim($("[name='search_employee']").val());
	var search_team = $.trim($("[name='search_team']").val());
	var search_attendance_month = $.trim($("[name='search_attendance_month']").val());
	

	var searchData = {
		'search_employment_status': search_employment_status,
    	'search_employee': search_employee,
        'search_team':search_team,
        'search_attendance_month':search_attendance_month,
    }
	return searchData;
	

}

function filterData(){
	var searchFieldName = searchField();
	if( searchFieldName.search_attendance_month == "" ||  searchFieldName.search_attendance_month == null  ){
		alertifyMessage('error' , "{{ trans('messages.required-month') }}")
		return false;
	}
	searchAjax(site_url + 'filterAttendanceReportDayWise' , searchFieldName);
}
var paginationUrl = site_url + 'filterAttendanceReportDayWise';
pagination_view_html = 'pagination-view-html';
function exportData(){
	var searchData = searchField();
	var export_info = {};
	export_info.url = site_url + 'filterAttendanceReportDayWise';
	export_info.searchData = searchData;
	dataExportIntoExcel(export_info);
}
</script>

@endsection