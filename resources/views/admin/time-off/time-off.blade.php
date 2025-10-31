@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.my-time-off") }}</h1>
    </div>
    <!-- @include(config('constants.ADMIN_FOLDER') .'time-off/time-off-info') -->
</main>

@endsection