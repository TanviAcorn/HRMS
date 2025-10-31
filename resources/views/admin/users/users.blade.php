@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<link rel="stylesheet" href="{{ asset ('css/dataTables.bootstrap4.css') }}" rel="stylesheet">
<script type="text/javascript" src="{{ asset ('js/jquery.dataTables.js') }}"></script>
<script type="text/javascript" src="{{ asset ('js/dataTables.bootstrap4.js') }}"></script>


<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-sm-flex p-3 border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.all-users") }}</h1>
        <div class="ml-auto pt-sm-0">
            <a href="{{ config('constants.USERS_URL') . '/create'  }}" class="btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2" title="{{ trans('messages.add-user') }}"><i class="fas fa-plus"></i> {{ trans("messages.add-user") }}</a>
            <button class="btn btn btn-theme text-white button-actions-top-bar border btn-sm" data-toggle="collapse" data-target="#filter" title="Toggle Filter"><i class="fas fa-filter"></i> {{ trans("messages.filter") }}</button>
        </div>
    </div>

    <section class="inner-wrapper-common-section main-listing-section pt-3">
        <div class="container-fluid">
            <?php
            $tableSearchPlaceholder = "Search By Name, Email, Contact No.";
            ?>
            <div class="collapse" id="filter">
                <div class="card card-body mb-3">
                    <div class="row align-items-center">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="search_user" class="control-label">{{ trans("messages.search-by") }}</label>
                                <input type="text" class="form-control twt-enter-search custom-input" name="search_user" id="search_user" placeholder="<?php echo $tableSearchPlaceholder ?>">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
                                <select class="form-control"  name="search_status">
                                    <option value="">{{ trans("messages.select") }}</option>
                                    <option value="{{ config('constants.ENABLE_STATUS') }}">{{ trans("messages.enable") }}</option>
                                    <option value="{{ config('constants.DISABLE_STATUS') }}">{{ trans("messages.disable") }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 d-flex align-items-end gap">
                            <button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white mt-3" onclick="filterData(this);">{{ trans("messages.search") }}</button>
                            <button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers mt-3">{{ trans("messages.reset") }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-result-wrapper">
                <div class="card card-body shadow-sm">
                    {{ Wild_tiger::readMessage() }}
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover" id="user-table">
                            <thead>
                                <tr>
                                    <th>{{ trans("messages.sr-no") }}</th>
                                    <th class="text-left">{{ trans("messages.name") }}</th>
                                    <th class="text-left">{{ trans("messages.email") }}</th>
                                    <th class="text-left">{{ trans("messages.contact-no") }}</th>
                                    <th>{{ trans("messages.status") }}</th>
                                    <th>{{ trans("messages.actions") }}</th>
                                </tr>
                            </thead>

                            <tbody class="ajax-view">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- </div>
            </div> -->

        </div>

    </section>
</main>



<script>
    function search_field() {

        var search_user = $.trim($("[name='search_user']").val());
        var search_status = $.trim($("[name='search_status']").val());

        var searchData = {
            'search_user': search_user,
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
	var user_module_url = '{{ config("constants.USERS_URL") }}' + '/';
    function reintDataTable(className = null) {

        var paginationUrl = user_module_url + "filter";

        var searchData = search_field();

        $('#' + className).DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "language": {
                "searchPlaceholder": "<?php echo $tableSearchPlaceholder ?>"
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
                error: function() { // error handling code

                }
            },
            'columns': [{
                    data: 'sr_no',
                    orderable: false
                },
                {
                    data: 'name',
                    className: "text-left",
                },
                {
                    data: 'email',
                    className: "text-left",
                },
                {
                    data: 'mobile',
                    className: "text-left",
                },
                {
                    data: 'status',
                    orderable: false
                },
                {
                    data: 'action',
                    orderable: false
                },
            ],
        });
    }
</script>
@endsection