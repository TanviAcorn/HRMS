@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
   
    <div class="container-fluid pt-3 visit-history">

        <div class="filter-result-wrapper">
            <div class="card card-body notification-card">
                <div class="d-flex align-items-center notification-top-header">
                    <h1 class="mb-lg-0 mr-3 mb-0 header-title" id="pageTitle">{{ trans("messages.notifications") }}</h1>
                    <span class="head-total-counts mt-1">{{ ( isset($unReadNotificationCount) ? $unReadNotificationCount : 0 ) }}</span>
                    <div class="ml-auto">
	               @if( isset($userUnReadNotificationCount) && ($userUnReadNotificationCount > 0 ) )
	               		<?php ?>
	                    <a class="btn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" href="{{ config('app.url') . 'mark-read-all-notification' }}" title="{{ trans('messages.mark-all-as-read') }}"><i class="fa fa-solid fa-check mt-1"></i><span class="d-sm-inline-block d-none ml-1"> {{ trans('messages.mark-all-as-read') }}</span></a>
	                    <?php  ?>
	               @endif
                </div>
                </div>
                {{ Wild_tiger::readMessage() }}
                <div class="mt-3">
                    <div class="notification-card ajax-view">
						@include( config('constants.AJAX_VIEW_FOLDER') . 'notification/notification-list')
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
var notification_url = '{{config("constants.NOTIFICATION_URL")}}' + '/';
function searchField(){
	var searchData = { }
        return searchData;
}

var paginationUrl = notification_url + 'notificationFilter'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection