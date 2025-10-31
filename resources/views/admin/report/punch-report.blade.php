@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.punch-report-live") }}</h1>
        <span class="head-total-counts total-record-count">1</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <button type="button" title="{{ trans('messages.export-excel') }}" onclick="exportData(this)" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel mr-0 mr-sm-2  fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">

        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row depedent-row">
                    @if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_PUNCH_REPORT'), session()->get('user_permission')  ) ) ) ) )
						<div class="col-xl-2 col-lg-3 col-12">
							<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' )  , (isset($allPermissionId) ? $allPermissionId : '' ) );?>
						</div>
						<div class="col-xl-3 col-lg-4 col-12">
							<?php echo statusWiseEmployeeList('search_employee' , (isset($employeeDetails) ? $employeeDetails : [] ) );?>
						</div>
					@endif
					<div class="form-group col-lg-2 col-md-3 col-sm-6">
                        <label for="search_from_date" class="control-label">{{ trans("messages.punch-from-date") }}</label>
                        <input type="text" class="form-control" name="search_from_date" value="{{ ( isset($startDate) ? clientDate($startDate) : '' )  }}" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    <div class="form-group col-lg-2 col-md-3 col-sm-6">
                        <label for="search_to_date" class="control-label">{{ trans("messages.punch-to-date") }}</label>
                        <input type="text" class="form-control" name="search_to_date" value="{{ ( isset($endDate) ? clientDate($endDate) : '' )  }}"  placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6">
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
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6 d-none">
                        <div class="form-group">
                            <label class="control-label" for="search_punch_type">{{ trans("messages.filter-punch-type") }}</label>
                            <select class="form-control" name="search_punch_type" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.SYSTEM_IN_STATUS') }}">{{ trans('messages.in') }}</option>
                                <option value="{{ config('constants.SYSTEM_OUT_STATUS') }}">{{ trans('messages.out') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData()" >{{ trans("messages.search") }}</button>
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
                                <th class="text-left" style="width:85px; max-width:85px;min-width:85px;">{{ trans("messages.date") }}</th>
                                <th class="text-left" style="width:400px;min-width:250px;">{{ trans("messages.employee-name-code") }} <br> {{ trans("messages.contact-number") }}</th>
                                <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.team") }}</th>
                                <?php /* ?>
                                <th class="text-left" style="width:158px;min-width:140px;">{{ trans("messages.punch-type") }}</th>
                                <?php */ ?>
                                <th class="text-left" style="width:200px;min-width:100px;">{{ trans("messages.time-stamp") }}</th>
                            </tr>
                        </thead>
                        <tbody class="ajax-view">
                            @include(config('constants.AJAX_VIEW_FOLDER') .'report/punch-report-list')
						</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>


<script>
$(function() {
    $("[name='search_from_date'],[name='search_to_date']").datetimepicker({
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
  

	<?php if( isset($endDate) && (!empty($endDate)) ) { ?>
    $("[name='search_from_date']").data('DateTimePicker').maxDate(moment('<?php echo date('Y-m-d' , strtotime($endDate) ) ?>' , 'YYYY-MM-DD').endOf('d'));
    <?php } ?>
    <?php if( isset($startDate) && (!empty($startDate)) ) { ?>
	$("[name='search_to_date']").data('DateTimePicker').minDate(moment('<?php echo date('Y-m-d' , strtotime($startDate) ) ?>' , 'YYYY-MM-DD').startOf('d'));
	<?php } ?>
	
});

$(function(){
	$("[name='search_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $(this).val() != "" && $(this).val() != null ){
			var incrementDay = moment((e.date)).startOf('d');
			$("[name='search_to_date']").data('DateTimePicker').minDate(incrementDay);
    	} else {
    		$("[name='search_to_date']").data('DateTimePicker').minDate(false);
        }
		$(this).data("DateTimePicker").hide();
	 	
	});

    $("[name='search_to_date']").datetimepicker().on('dp.change', function(e) {
        if( $(this).val() != "" && $(this).val() != null ){
        	var decrementDay = moment((e.date)).endOf('d');
        	$("[name='search_from_date']").data('DateTimePicker').maxDate(decrementDay);
        } else {
        	$("[name='search_from_date']").data('DateTimePicker').maxDate(false);
        }
        $(this).data("DateTimePicker").hide();
    });
		
});

function searchField(){
	var search_employment_status = $.trim($("[name='search_employment_status']").val());
	var search_employee = $.trim($("[name='search_employee']").val());
	var search_team = $.trim($("[name='search_team']").val());
	var search_from_date = $.trim($("[name='search_from_date']").val());
	var search_to_date = $.trim($("[name='search_to_date']").val());
	var search_punch_type =  $.trim($("[name='search_punch_type']").val());

	var searchData = {
    	'search_employment_status': search_employment_status,
        'search_employee': search_employee,
        'search_team':search_team,
        'search_start_date':search_from_date,
        'search_end_date':search_to_date,
        'search_punch_type':search_punch_type,
	}
	return searchData;
	

}

function filterData(){
	var searchFieldName = searchField();
	searchAjax(site_url + 'filterPunchReport' , searchFieldName);
	setTimeout(function(){

	}, )
}
var paginationUrl = site_url + 'filterPunchReport';

function exportData(){
	var searchData = searchField();
	var export_info = {};
	export_info.url = site_url + 'filterPunchReport';
	export_info.searchData = searchData;
	dataExportIntoExcel(export_info);
}
</script>
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection