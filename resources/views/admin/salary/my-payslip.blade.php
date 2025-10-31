@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.my-pay-slip") }}</h1>
    </div>
    <div class="container-fluid pt-4 employee-payslip">
        <div class="row">
            <div class="col-12 profile-detail-card">
                <!-- @include( config('constants.ADMIN_FOLDER') . 'salary/employee-payslip') -->
            </div>
        </div>
    </div>
</main>

@endsection