@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
@include('admin/sub-designation-master/sub-designation-model') 
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ $pageTitle }}</h1>
        <span class="head-total-counts total-record-count">{{ (int) ($totalRecordCount ?? 0) }}</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            @if(checkPermission('add_designation_master') != false)
            <button type="button" onclick="openSubDesignationModel(this)" class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar d-sm-flex align-items-center" title="{{ trans('messages.add') }}"><i class="fas fa-plus mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans('messages.add') }}</span> </button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans('messages.filter') }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans('messages.search-by') }}</label>
                            <input type="text" name="search_by" class="form-control" placeholder="{{ trans('messages.search-by') }} Sub Designation">
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_designation">{{ trans('messages.designation') }}</label>
                            <select class="form-control" name="search_designation" onchange='filterData()'>
                                <option value="">{{ trans('messages.select') }}</option>
                                <?php 
                                if(!empty($designationRecordDetails)){
                                    foreach ($designationRecordDetails as $designation){
                                        $encodeId = Wild_tiger::encode($designation->i_id);
                                        ?>
                                        <option value='{{ $encodeId }}'>{{ (!empty($designation->v_value) ? $designation->v_value : '') }}</option>
                                        <?php 
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_status">{{ trans('messages.status') }}</label>
                            <select class="form-control" name="search_status" onchange='filterData()'>
                                <option value="">{{ trans('messages.select') }}</option>
                                <option value="{{ config('constants.ACTIVE_STATUS')}}">{{ trans('messages.active') }}</option>
                                <option value="{{ config('constants.INACTIVE_STATUS')}}">{{ trans('messages.inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" onclick="filterData()" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}">{{ trans('messages.search') }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans('messages.reset') }}</button>
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
                                <th class="text-center sr-col">{{ trans('messages.sr-no') }}</th>
                                <th class="text-left" style="min-width:180px;">Sub Designation</th>
                                <th class="text-left" style="min-width:140px;">{{ trans('messages.designation') }}</th>
                                <th class="text-center">{{ trans('messages.status') }}</th>
                                @if((checkPermission('edit_designation_master') != false) || (checkPermission('delete_designation_master') != false))
                                <th class="actions-col" style="min-width:150px;width:150px">{{ trans('messages.actions') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                            @include( config('constants.AJAX_VIEW_FOLDER') . 'sub-designation-master/sub-designation-master-list')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</main>
<script>
var sub_designation_module_url = '{{config('constants.SUB_DESIGNATION_MASTER_URL')}}' + '/';

function searchField(){
    var search_by = $.trim($('[name="search_by"]').val());
    var search_status = $.trim($('[name="search_status"]').val());
    var search_designation = $.trim($('[name="search_designation"]').val());
    var searchData = {
        'search_by':search_by,
        'search_status': search_status,
        'search_designation':search_designation,
    }
    return searchData;
}
function filterData(){
    var searchFieldName = searchField();
    searchAjax(sub_designation_module_url + 'filter' , searchFieldName);
}
var paginationUrl = sub_designation_module_url + 'filter'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script> 
@endsection
