@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex mb-4 align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.my-leaves") }}</h1>
    </div>
    <div class="container-fluid my-leaves pb-3">
        <div class="row">
            <!--<div class="col-12 profile-detail-card">
            	 @include(config('constants.AJAX_VIEW_FOLDER') .'my-leaves/my-leaves-main-list') 
            </div>-->
        </div>
    </div>
</main>

@endsection