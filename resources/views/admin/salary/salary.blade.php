@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper mb-3 d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.my-salary") }}</h1>
    </div>
    <div class="container-fluid my-salary pb-3">
        <div class="row">
            <div class="col-12 profile-detail-card"   >
                @include( config('constants.ADMIN_FOLDER') . 'salary/salary-edit')
            </div>
        </div>
    </div>
</main>

@endsection