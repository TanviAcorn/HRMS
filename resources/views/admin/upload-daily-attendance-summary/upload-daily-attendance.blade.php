@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.uploaded-attendance-data") }}</h1>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <button type="button" title="{{ trans('messages.export-excel') }}" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2" onclick="openDailyAttendanceUploadModal(this);" ><i class="fas  fa-file-excel mr-0 mr-sm-2 fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            <button class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_from_date">{{ trans("messages.from-date") }}</label>
                            <input type="text" name="search_from_date" class="form-control" value="{{ ( isset($selectedDate) ? clientDate($selectedDate) : '' )  }}" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_to_date">{{ trans("messages.to-date") }}</label>
                            <input type="text" name="search_to_date" class="form-control" value="{{ ( isset($selectedDate) ? clientDate($selectedDate) : '' )  }}" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
                        </div>
                    </div>
					<div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData();" >{{ trans("messages.search") }}</button>
                        <button class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
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
                                <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.date") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.employee-name") }}</th>
                                <th class="text-left">{{ trans("messages.pay-code") }}</th>
                                <th class="text-left">{{ trans("messages.department") }}</th>
                                <th class="text-left">{{ trans("messages.shift") }}</th>
                                <th class="text-left">{{ trans("messages.start") }}</th>
                                <th class="text-left">{{ trans("messages.in") }}</th>
                                <th class="text-left">{{ trans("messages.out") }}</th>
                                <th class="text-left">{{ trans("messages.hours-worked") }}</th>
                                <th class="text-left">{{ trans("messages.status") }}</th>
                                <th class="text-left">{{ trans("messages.early-arrival") }}</th>
                                <th class="text-left">{{ trans("messages.shift-late") }}</th>
                                <th class="text-left">{{ trans("messages.shift-early") }}</th>
                                <th class="text-left">{{ trans("messages.ot") }}</th>
                                <th class="text-left">{{ trans("messages.ot-amount") }}</th>
                                <th class="text-left">{{ trans("messages.over-stay") }}</th>
                                <th class="text-left">{{ trans("messages.manual") }}</th>
                                <th class="text-left">{{ trans("messages.in-location") }}</th>
                                <th class="text-left">{{ trans("messages.out-location") }}</th>
							</tr>
                        </thead>
                        <tbody class="ajax-view">
                            @include( config('constants.AJAX_VIEW_FOLDER') . 'upload-daily-attendance-summary/upload-attendance-list')	
						</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>


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
        maxDate:moment().endOf('m'),
    });

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

   	})
   	
   	
   	
	var upload_daily_attendance_url = '{{config("constants.UPLOAD_DAILY_ATTENDANCE_URL")}}' + '/';
    
    function searchField(){
    	var search_from_date = $.trim($('[name="search_from_date"]').val());
    	var search_to_date = $.trim($('[name="search_to_date"]').val());

    	var searchData = {
            'search_from_date': search_from_date,
            'search_to_date': search_to_date,
        }
        return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(upload_daily_attendance_url + 'filterAttendanceData' , searchFieldName);
    }

    var paginationUrl = upload_daily_attendance_url + 'filterAttendanceData'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>

@endsection