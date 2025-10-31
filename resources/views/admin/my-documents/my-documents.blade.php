@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h3 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.my-documents") }}</h3>
    </div>
    <div class="filter-result-wrapper container-fluid pt-4 manage-document">
        <div class="row">
            <div class="col-12 profile-detail-card emp-document-list">
                @include(config('constants.AJAX_VIEW_FOLDER') .'employee-master/document-info')
                
            </div>
        </div>
    </div>
</main>

@include(config('constants.ADMIN_FOLDER') .'employee-master/emp-upload-document')
@include(config('constants.ADMIN_FOLDER') .'employee-master/emp-view-document')

@endsection