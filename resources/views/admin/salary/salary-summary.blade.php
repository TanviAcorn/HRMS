@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.salary-summary") }}</h1>
    </div>
    <div class="container-fluid attendance-summary salary-summary pb-3">
        <div class="row py-4 align-items-center">
            <div class="col-lg-8 col-md-9 mb-md-0 mb-3">
                <h5 class="bg-title">{{ trans("messages.salary-stats") }}</h5>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group mb-0">
                    <select class="form-control" name="search_year" onchange="filterSalarySummary()">
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
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group mb-0">
                    <select class="form-control select2" name="search_team" onchange="filterSalarySummary()">
                        <option value="">All {{ trans('messages.team') }}</option>
                        @if(!empty($teamDetails))
                        	@foreach($teamDetails as $teamDetail)
                            	@php $encodeId = Wild_tiger::encode($teamDetail->i_id); @endphp 
                            	<option value="{{ $encodeId }}">{{ (!empty($teamDetail->v_value) ? $teamDetail->v_value :'') }}</option>
							@endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
		@include(config('constants.AJAX_VIEW_FOLDER') . 'salary/salary-summary-list')
    </div>
</main>
<script>
function filterSalarySummary(){
	var search_year = $.trim($("[name='search_year']").val());
	var search_team = $.trim($("[name='search_team']").val());

	$.ajax({
		type: "POST",
		url: '{{config("constants.SITE_URL")}}' + 'filterSalarySummary',
		data:{'search_year':search_year , 'search_team' : search_team },
		beforeSend: function() {
			//block ui
			showLoader();
		},
		success: function(response) {
			hideLoader();
			if( response != "" && response != null ){
				response = $.trim(response);
				$(".filter-salary-summary").html(response);
			}
		},
		error: function() {
			hideLoader();
		}
	});
}
</script>

@endsection