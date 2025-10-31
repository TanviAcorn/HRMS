@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.present-summary") }}</h1>
    </div>
    <div class="container-fluid attendance-summary pb-3">
        <div class="row py-4 align-items-center">
            <div class="col-lg-10 col-md-9 mb-md-0 mb-3">
                <h5 class="bg-title">{{ trans("messages.todays-attendance-stats") }}</h5>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group mb-0">
                    <select class="form-control select2" name="search_team" onchange="filterAttendanceSummary(this);">
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

        <div class="row attendance-summary-filter">
            @include(config('constants.AJAX_VIEW_FOLDER') . 'my-attendance/attendance-summary-filter')
        </div>
    </div>
</main>
<script>
function filterAttendanceSummary(){
	var search_team = $.trim($("[name='search_team']").val());

	$.ajax({
		type: "POST",
		url: '{{config("constants.SITE_URL")}}' + 'attendance-summary-filter',
		data:{  'search_team' : search_team },
		beforeSend: function() {
			//block ui
			showLoader();
		},
		success: function(response) {
			hideLoader();
			if( response != "" && response != null ){
				response = $.trim(response);
				$(".attendance-summary-filter").html(response);
			}
		},
		error: function() {
			hideLoader();
		}
	});
}
</script>
@endsection 