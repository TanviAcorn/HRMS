@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<link rel="stylesheet" href="{{ asset ('css/dataTables.bootstrap4.css') }}" rel="stylesheet">
<script type="text/javascript" src="{{ asset ('js/jquery.dataTables.js') }}"></script>
<script type="text/javascript" src="{{ asset ('js/dataTables.bootstrap4.js') }}"></script>
<script type="text/javascript" src="{{ asset ('js/datatables-fixedheader.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset ('css/fixedheader-datatables.min.css') }}">

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.shift-master") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            @if(checkPermission('add_shifts'))
                <a href="{{ config('constants.SHIFT_MASTER_URL') .'/create' }}" class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar d-sm-flex align-items-center" title="{{ trans('messages.add-shift') }}"><i class="fas fa-plus mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.add-shift") }}</span> </a>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <?php
        $tableSearchPlaceholder = trans('messages.search-by-shift-name-code-description');
        ?>
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans("messages.search-by") }}</label>
                            <input type="text" name="search_by" class="form-control" placeholder="{{ $tableSearchPlaceholder }}">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_shift_type">{{ trans("messages.shift-type") }}</label>
                            <select class="form-control" name="search_shift_type" onchange="filterData();">
                                <option value="">{{ trans("messages.select") }}</option>
                                <?php
                                if (!empty($typeOfShiftInfo)) {
                                    foreach ($typeOfShiftInfo as $key =>  $typeOfShift) {
                                ?>
                                        <option value="{{ $key }}">{{ $typeOfShift }}</option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>


                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
                            <select class="form-control" name="search_status" onchange="filterData();">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.ACTIVE_STATUS') }}">{{ trans("messages.active") }}</option>
                                <option value="{{ config('constants.INACTIVE_STATUS') }}">{{ trans("messages.inactive") }}</option>

                            </select>
                        </div>
                    </div>
                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" onclick="filterData()" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
            <div class="card card-body">
                {{ Wild_tiger::readMessage() }}
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="user-table">
                        <thead>
                            <tr>
                                <th class="text-center sr-col" style="width: 55px;max-width: 55px;min-width: 55px;">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="min-width:200px; width:200px;">{{ trans("messages.shift-name") }}</th>
                                <th class="text-left" style="min-width:100px; width:100px;">{{ trans("messages.shift-code") }}</th>
                                <th class="text-left" style="min-width:155px; width:155px;">{{ trans("messages.shift-type") }}</th>
                                <th class="text-left" style="min-width:250px;">{{ trans("messages.description") }}</th>
                                <th class="text-center">{{ trans("messages.status") }}</th>
                                @if((checkPermission('edit_shifts') != false ) || (checkPermission('delete_shifts') != false))
                                <th class="actions-col text-center" style="max-width: 100px !important;min-width: 100pxpx!important;">{{ trans("messages.actions") }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="ajax-view">
                            <?php /*?>@include(config('constants.AJAX_VIEW_FOLDER') .'shift-master/shift-master-list')*/ ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</main>



<script>
    var shift_master_url = '{{config("constants.SHIFT_MASTER_URL")}}' + '/';

    function searchField() {
        var search_by = $.trim($('[name="search_by"]').val());
        var search_shift_type = $.trim($('[name="search_shift_type"]').val());
        var search_status = $.trim($('[name="search_status"]').val());

        var searchData = {
            'search_by': search_by,
            'search_shift_type': search_shift_type,
            'search_status': search_status,

        }
        return searchData;
    }

    function filterData() {
        if ($.fn.DataTable.isDataTable('#user-table')) {
            $('#user-table').DataTable().destroy();
        }

        reintDataTable('user-table');
    }
    $(document).ready(function() {
        reintDataTable('user-table');
    })
    var paginationUrl = shift_master_url + 'filter'

    function reintDataTable(className = null) {

        var paginationUrl = shift_master_url + "filter";

        var searchData = searchField();

        $('#' + className).DataTable({
            "bProcessing": true,
            "searching": false,
            "fixedHeader":{
                "header": true,
                "headerOffset": 40
            },
            "bServerSide": true,
            "scrollX": true,
            "scrollY": 'calc(100vh - 300px)',
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                $(".dataTables_scrollBody").addClass('no-record');
                if (aiDisplay.length > 8) {
                    $(".dataTables_scrollBody").removeClass('no-record');
                }
                else {
                    $(".dataTables_scrollBody").addClass('no-record');
                }
            },
            "language": {
                "searchPlaceholder": "{{ $tableSearchPlaceholder }}"
            },
            "iDisplayLength": 25,
            "order": [],
            "order": [],
            "ajax": {
                url: paginationUrl, // json datasource
                type: "post", // type of method  , by default would be get
                data: searchData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataFilter: function(response) {
                    hideLoader();
                    if (response != "" && response != null) {
                        var response_json_data = JSON.parse(response);
                        var total_display_record = ((response_json_data.iTotalDisplayRecords != "" && response_json_data.iTotalDisplayRecords != null) ? response_json_data.iTotalDisplayRecords : 0);
                        $(".total-record-count").html(total_display_record);
                    } else {
                        $(".total-record-count").html(0);
                    }
                    return response;
                },
                error: function() { // error handling code

                }
            },
            'columns': [{
                    data: 'sr_no',
                    orderable: false
                },
                {
                    data: 'shift_name'
                },
                {
                    data: 'shift_code'
                },
                {
                    data: 'shift_type'
                },
                {
                    data: 'description'
                },
                {
                    data: 'status',
                    orderable: false
                },
                @if((checkPermission('edit_shifts') != false ) || (checkPermission('delete_shifts') != false))
                {
                    data: 'action',
                    orderable: false
                },
                @endif
            ],
        });
    }
</script>

@endsection