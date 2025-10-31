@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
@include('admin/salary-groups-model')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.salary-groups-master") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            @if(checkPermission('add_salary_groups') != false)
            <button type="button" onclick="openSalaryGroupModel(this)" class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar d-sm-flex align-items-center"  title="{{ trans('messages.add-salary-groups') }}"><i class="fas fa-plus mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.add-salary-groups") }}</span> </button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans("messages.search-by") }}</label>
                            <input type="text" name="search_by" class="form-control" placeholder='{{ trans("messages.search-by") }} {{ trans("messages.group-name") }}, {{ trans("messages.description") }}'>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
                            <select class="form-control" name="search_status" onchange='filterData()'>
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.ACTIVE_STATUS')}}">{{ trans("messages.active") }}</option>
                                <option value="{{ config('constants.INACTIVE_STATUS')}}">{{ trans("messages.inactive") }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" onclick='filterData()' class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
            <div class="card card-body">
            {{ Wild_tiger::readMessage() }}
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="min-width:200px; max-width:200px;">{{ trans("messages.group-name") }}</th>
                                <th class="text-left" style="min-width:200px; max-width:200px;">{{ trans("messages.group-description") }}</th>
                                <th class="text-center" style="min-width:80px; max-width:80px;">{{ trans("messages.status") }}</th>
                                @if((checkPermission('edit_salary_groups') != false) || (checkPermission('delete_salary_groups') != false))
                                <th class="actions-col" style="min-width:150px;width:150px">{{ trans("messages.actions") }}</th>
                                @endif
                            </tr>
                        </thead>
                       <tbody class='ajax-view'>
                            @include( config('constants.AJAX_VIEW_FOLDER') . 'salary-groups/salary-groups-list')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection