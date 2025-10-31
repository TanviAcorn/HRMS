@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.attendance-report-daily-monthly") }}</h1>
        <span class="head-total-counts total-record-count">1</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
            <button type="button" title="{{ trans('messages.export-excel') }}" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2" onclick="exportData();"><i class="fas fa-file-excel mr-0 mr-sm-2 fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
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
                	@if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_ATTENDANCE_REPORT'), session()->get('user_permission')  ) ) ) ) )
					<div class="col-xl-2 col-lg-4 col-12">
					<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' ) );?>
					</div>
					<div class="col-xl-3 col-lg-4 col-12">
					<?php echo statusWiseEmployeeList( 'search_employee_name' ,  (isset($employeeDetails) ? $employeeDetails : [] ) , ( isset($selectedUserId) ? $selectedUserId : '' ) );?>
					</div>
					@endif
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_from_date">{{ trans("messages.from-date") }}</label>
                            <input type="text" name="search_from_date" class="form-control" value="{{ isset($startDate) ? clientDate($startDate) : '' }}" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_to_date">{{ trans("messages.to-date") }}</label>
                            <input type="text" name="search_to_date" class="form-control" value="{{ isset($endDate) ? clientDate($endDate) : '' }}" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
                        </div>
                    </div>
					
                    @if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_ATTENDANCE_REPORT'), session()->get('user_permission')  ) ) ) ) )
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team" onchange="filterData();">
                                <option value="">Select</option>
	                            @if(!empty($teamDetails))
	                            	@foreach($teamDetails as $teamDetail)
	                            	@php
	                            		$encodeRecordId = (!empty($teamDetail->i_id) ? Wild_tiger::encode($teamDetail->i_id) : 0);
	                            	@endphp
	                            		<option value="{{ $encodeRecordId }}">{{ (!empty($teamDetail->v_value) ? $teamDetail->v_value : '') }}</option>
	                            	@endforeach
	                            @endif
                            </select>
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_attendance_status">{{ trans("messages.status") }}</label>
                            <select class="form-control" name="search_attendance_status" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{config('constants.PRESENT_STATUS')}}">{{ trans("messages.present") }}</option>
                                <option value="{{config('constants.ABSENT_STATUS')}}">{{ trans("messages.absent") }}</option>
                                <option value="{{config('constants.HALF_LEAVE_STATUS')}}">{{ trans("messages.half-leave") }}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_break_time">{{ trans("messages.break-time") }}</label>
                            <select class="form-control" name="search_break_time" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{config('constants.GREATER_THAN_MIN')}}">{{ trans("messages.greater-than-45-min") }}</option>
                                <option value="{{config('constants.LESS_THAN_MIN')}}">{{ trans("messages.less-than-45-min") }}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_arrival_status">{{ trans("messages.arrival") }}</label>
                            <select class="form-control" name="search_arrival_status" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($arrivalDepartureDetails))
                                	@foreach($arrivalDepartureDetails as $arrivalDepartureKey =>  $arrivalDepartureDetail)
                                		<option value="{{ $arrivalDepartureKey }}">{{ $arrivalDepartureDetail }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_departure_status">{{ trans("messages.departure") }}</label>
                            <select class="form-control" name="search_departure_status" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($arrivalDepartureDetails))
                                	@foreach($arrivalDepartureDetails as $arrivalDepartureKey =>  $arrivalDepartureDetail)
                                		<option value="{{ $arrivalDepartureKey }}">{{ $arrivalDepartureDetail }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>


                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData()">{{ trans("messages.search") }}</button>
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
                                <th style="width:100px; min-width:100px; ">{{ trans("messages.date") }}<br> {{ trans("messages.day") }}</th>
                                <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.shift") }}</th>
                                <th style="min-width:84px;width:auto;">{{ trans("messages.in-time") }} <br> {{ trans("messages.arrival") }}</th>
                                <th class="text-left" style="min-width:100px;">{{ trans("messages.out-time") }} <br> {{ trans("messages.departure") }}</th>
                                <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.total-hours") }}</th>
                                <th class="text-left" style="min-width:95px;">{{ trans("messages.break-time") }}</th>
                                <th class="text-left" style="min-width:90px;width:auto;">{{ trans("messages.working-hours") }}</th>
                                <th class="text-left" style="min-width:90px;width:auto;">{{ trans("messages.status") }}</th>
                            </tr>
                        </thead>
                        <tbody class="ajax-view">
							@include( config('constants.AJAX_VIEW_FOLDER') . 'report/attendance-report-list')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</main>

<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
<script>
	$("[name='search_from_date'],[name='search_to_date']").datetimepicker({
	    useCurrent: false,
	    ignoreReadonly: true,
	    format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
	    showClose: true,
	    showClear: true,
	    icons: {
	        clear: 'fa fa-trash',
	    },
	    widgetPositioning: {
	        horizontal: 'auto',
	        vertical: 'bottom'
	    },
	});

	<?php if( isset($endDate) && (!empty($endDate)) ) { ?>
    $("[name='search_from_date']").data('DateTimePicker').maxDate(moment('<?php echo date('Y-m-d' , strtotime($endDate) ) ?>' , 'YYYY-MM-DD').endOf('d'));
    <?php } ?>
    <?php if( isset($startDate) && (!empty($startDate)) ) { ?>
	$("[name='search_to_date']").data('DateTimePicker').minDate(moment('<?php echo date('Y-m-d' , strtotime($startDate) ) ?>' , 'YYYY-MM-DD').startOf('d'));
	<?php } ?>
	
	$(function(){
   	 $("[name='search_from_date']").datetimepicker().on('dp.change', function(e) {
    		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    			var incrementDay = moment((e.date)).startOf('d');
    		 	$("[name='search_to_date']").data('DateTimePicker').minDate(incrementDay);
    		} else {
    			$("[name='search_to_date']").data('DateTimePicker').minDate(false);
    		} 
    		
    	    $(this).data("DateTimePicker").hide();
    	});

        $("[name='search_to_date']").datetimepicker().on('dp.change', function(e) {
        	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    	        var decrementDay = moment((e.date)).endOf('d');
    	        $("[name='search_from_date']").data('DateTimePicker').maxDate(decrementDay);
        	} else {
        		 $("[name='search_from_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });
   });
	   
    var attendance_report_url = '{{ config("constants.ATTENDANCE_REPORT_URL") }}' + '/';
    
    function searchField(){
    	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
    	var search_employee_name = $.trim($('[name="search_employee_name"]').val());
    	var search_from_date = $.trim($('[name="search_from_date"]').val());
    	var search_to_date  = $.trim($('[name="search_to_date"]').val());
    	var search_team = $.trim($('[name="search_team"]').val());
    	var search_attendance_status = $.trim($('[name="search_attendance_status"]').val());
    	var search_arrival_status = $.trim($('[name="search_arrival_status"]').val());
    	var search_departure_status = $.trim($('[name="search_departure_status"]').val());
    	var search_break_time = $.trim($('[name="search_break_time"]').val());
    	
    	var searchData = {
    			'search_employment_status':search_employment_status,
                'search_employee_name':search_employee_name,
                'search_from_date': search_from_date,
                'search_to_date':search_to_date,
                'search_team':search_team,
                'search_attendance_status':search_attendance_status,
                'search_arrival_status':search_arrival_status,
                'search_departure_status':search_departure_status,
                'search_break_time':search_break_time,
            }
            return searchData;
    }
    
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(attendance_report_url + 'attendanceReportFilter' , searchFieldName);
    }
    function exportData(){
   		var searchData = searchField();
   		var export_info = {};
   		export_info.url = attendance_report_url + 'attendanceReportFilter';
   		export_info.searchData = searchData;
   		dataExportIntoExcel(export_info);
   	}
   	var paginationUrl  = attendance_report_url + 'attendanceReportFilter';
</script>

<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection