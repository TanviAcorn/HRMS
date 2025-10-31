@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.leave-detailed-summary") }}</h1>
    </div>
    <div class="container-fluid leave-summary pb-3">
        <div class="row py-4 align-items-center">
            <div class="col-lg-7 col-md-5 col-sm-4 mb-md-0 mb-3">
                <h5 class="bg-title">{{ trans("messages.leave-stats") }}</h5>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 col-6">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" name="search_leave_from_date" placeholder="{{ trans('messages.from-date') }}" value="{{ (!empty($startDate) ? clientDate($startDate) :'')}}">
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 col-6">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" name="search_leave_to_date" placeholder="{{ trans('messages.to-date') }}" value="{{ (!empty($endDate) ? clientDate($endDate) :'')}}">
                </div>
            </div>
            <div class="col-sm-1 col-6 pl-md-0 mt-sm-0 mt-3">
                <div class="form-group mb-0">
                    <button type="button" onclick="filterLeaveSummary();" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.search') }}">{{ trans('messages.search') }}</button>
                </div>
            </div>
        </div>
        <div class="row filter-leave-summary">
            @include(config('constants.AJAX_VIEW_FOLDER') .'leave-summary/leave-summary-list')	
        </div>
    </div>
</main>

<script>
    $(function() {
    	$('[name="search_leave_from_date"], [name="search_leave_to_date"]').datetimepicker({
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
    	$("[name='search_leave_from_date']").datetimepicker().on('dp.change', function(e) {
    		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    			var incrementDay = moment((e.date)).startOf('d');
    		 	$("[name='search_leave_to_date']").data('DateTimePicker').minDate(incrementDay);
    		} else {
    			$("[name='search_leave_to_date']").data('DateTimePicker').minDate(false);
    		} 
    		
    	    $(this).data("DateTimePicker").hide();
    	});

        $("[name='search_leave_to_date']").datetimepicker().on('dp.change', function(e) {
        	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    	        var decrementDay = moment((e.date)).endOf('d');
    	        $("[name='search_leave_from_date']").data('DateTimePicker').maxDate(decrementDay);
        	} else {
        		 $("[name='search_leave_from_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });
    });

    function searchField(){
    	var search_leave_from_date = $.trim($("[name='search_leave_from_date']").val());
    	var search_leave_to_date = $.trim($("[name='search_leave_to_date']").val());

    	var searchData = {
					'search_leave_from_date':search_leave_from_date,
					'search_leave_to_date':search_leave_to_date,
    	    	};
    	return searchData;
    }
    
   	function filterLeaveSummary(){
    	
		var searchData = searchField();

		if( ( searchData.search_leave_from_date == "" || searchData.search_leave_from_date == null ) && ( searchData.search_leave_to_date == "" || searchData.search_leave_to_date == null ) ){
			alertifyMessage("error" , "{{ trans('messages.required-one-search-criteria') }}");
			return false;
		}
		
    	$.ajax({
    		type: "POST",
    		url: '{{config("constants.LEAVE_SUMMARY_MASTER_URL")}}' + '/filterLeaveSummary',
    		data:searchData,
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			if( response != "" && response != null ){
    				response = $.trim(response);
    				$(".filter-leave-summary").html(response);
    			}
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
</script>


@endsection