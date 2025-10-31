@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.employee-duration-report") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
            <button type="button" title="{{ trans('messages.export-excel') }}" onclick="exportData()" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel fa-fw mr-0 mr-sm-2"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 depedent-row">
                <div class="row">
	                @if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_DURATION_REPORT'), session()->get('user_permission')  ) ) ) ) )
                	<div class="col-xl-2 col-lg-4 col-12">
                		<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' ) );?>
                	</div>
                    <div class="col-xl-3 col-lg-4 col-12">
                    	<?php echo statusWiseEmployeeList('search_employee_name_code' , (isset($employeeDetails) ? $employeeDetails : [] ));?>
                    </div>
	                @endif
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team" onchange="filterData()">
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
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_gender" class="control-label">{{ trans('messages.gender') }}</label>
                            <select class="form-control" name="search_gender" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($genderDetails))
                                	@foreach($genderDetails as $key => $genderDetail)
                                		<option value="{{ $key }}">{{ (!empty($genderDetail) ? $genderDetail :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_shift" class="control-label">{{ trans('messages.shift') }}</label>
                            <select class="form-control" name="search_shift" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($shiftDetails))
                                	@foreach($shiftDetails as $shiftDetail)
                                		@php $shiftEncodeId = Wild_tiger::encode($shiftDetail->i_id); @endphp 
                                		<option value="{{ $shiftEncodeId }}">{{ (!empty($shiftDetail->v_shift_name) ? $shiftDetail->v_shift_name :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-6">
                        <div class="form-group">
                            <label for="search_date_filter" class="control-label">{{ trans('messages.date-filter') }}</label>
                            <select class="form-control" name="search_date_filter" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($employmentDateStatusInfo))
                                	@foreach($employmentDateStatusInfo as $key => $employmentDateStatus)
                                		<option value="{{ $key }}">{{ (!empty($employmentDateStatus) ? $employmentDateStatus :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-lg-2 col-md-3 col-sm-6 col-6">
                        <label for="search_from_date" class="control-label">{{ trans("messages.from-date") }}</label>
                        <input type="text" class="form-control" name="search_from_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    <div class="form-group col-lg-2 col-md-3 col-sm-6 col-6">
                        <label for="search_to_date" class="control-label">{{ trans("messages.to-date") }}</label>
                        <input type="text" class="form-control date" name="search_to_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    
                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" onclick="filterData()" title="{{ trans('messages.search') }}">{{ trans("messages.search") }}</button>
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
                                <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="min-width:70px;">{{ trans("messages.gender") }}</th>
                                <th class="text-left" style="width:100px; min-width:108px;">{{ trans("messages.date-of-birth") }}</th>
                                <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.joining-date") }}</th>
                                <th class="text-left" style="min-width:160px;">{{ trans("messages.shift") }}</th>
                                <th class="text-left" style="min-width:150px;">{{ trans("messages.probation-start-date") }} <br>{{ trans("messages.probation-end-date") }}</th>
                                <th class="text-left" style="min-width:160px;">{{ trans("messages.notice-period-start-date") }} <br>{{ trans("messages.notice-period-end-date") }}</th>
                                <th class="text-left" style="min-width:130px;">{{ trans("messages.last-working-date") }}</th>
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                        	@include( config('constants.AJAX_VIEW_FOLDER') . 'report/employee-duration-report-list')
                       </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</main>


<script>
var employee_duration_report_url = '{{config("constants.EMPLOYEE_DURATION_REPORT_URL")}}' + '/';

    	$(function() {
    	  $('[name="search_from_date"], [name="search_to_date"]').datetimepicker({
    	        useCurrent: false,
    	        viewMode: 'days',
    	        ignoreReadonly: true,
    	        format: 'DD-MM-YYYY',
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
    function searchField(){
    	var search_employee_name_code = $.trim($('[name="search_employee_name_code"]').val());
    	var search_team = $.trim($('[name="search_team"]').val());
    	var search_gender = $.trim($('[name="search_gender"]').val());
    	var search_shift = $.trim($('[name="search_shift"]').val());
    	var search_date_filter = $.trim($('[name="search_date_filter"]').val());
    	var search_date_filter = $.trim($('[name="search_date_filter"]').val());
    	var search_from_date = $.trim($('[name="search_from_date"]').val());
    	var search_to_date = $.trim($('[name="search_to_date"]').val());
    	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
    	
    	var searchData = {
                'search_employee_name_code':search_employee_name_code,
                'search_team': search_team,
                'search_gender':search_gender,
                'search_shift':search_shift,
                'search_date_filter': search_date_filter,
                'search_from_date':search_from_date,
                'search_to_date':search_to_date,
                'search_employment_status':search_employment_status
            }
            return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(employee_duration_report_url + 'employeeDurationFilter' , searchFieldName);
    }
    var paginationUrl = employee_duration_report_url + 'employeeDurationFilter'
   
    function exportData(){
   		var searchData = searchField();
   		var export_info = {};
   		export_info.url = employee_duration_report_url + 'employeeDurationFilter';
   		export_info.searchData = searchData;
   		dataExportIntoExcel(export_info);
   	}
</script>
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script> 


@endsection