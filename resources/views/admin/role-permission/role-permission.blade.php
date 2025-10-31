@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.roles-permissions") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <a href="{{ config('constants.ROLES_AND_PERMISSION_MASTER_URL').'/create' }}" class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar d-sm-flex align-items-center" title="{{ trans('messages.add-roles-permissions') }}"><i class="fas fa-plus mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.add-roles-permissions") }}</span> </a>
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans("messages.search-by") }}</label>
                            <input type="text" name="search_by" class="form-control" placeholder="{{ trans('messages.search-by') }} {{ trans('messages.role-name') }}, {{ trans('messages.role-description') }}">
                        </div>
                    </div>
                    <div class="col-md  d-flex align-self-end">
                        <div class="form-group">
                            <button type="button" class="btn btn-theme text-white" title="{{ trans('messages.search') }}" onclick="filterData()">{{ trans("messages.search") }}</button>
                            <button type="button" class="btn btn-outline-secondary reset-wild-tigers" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                        </div>
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
                                <th class=" text-left" style="min-width:150px; width:150px; max-width:150px;">{{ trans("messages.role-name") }}</th>
                                <th class="text-left" style="min-width:200px;">{{ trans("messages.role-description") }}</th>
                                <th class="actions-col" style="min-width:245px;">{{ trans("messages.actions") }}</th>
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                            @include( config('constants.AJAX_VIEW_FOLDER') . 'role-permission/role-permission-list')
                        </tbody>
                       
                    </table>
                </div>
            </div>
        </div>
    </div>


</main>


<div class="modal fade document-folder" id="assign_employee" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('messages.assign-employees') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {{-- <form method="post" id="edit_weekly_off_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="employees" class="control-label">{{ trans('messages.employees') }}</label>
                                <select class="form-control select2 select2-hidden-accessible" name="employees" multiple="">
                                    <option value="1">1<sup>st</sup> and 3<sup>rd</sup> Saturday</option>
                                    <option value="2">2<sup>nd</sup> and 4<sup>th</sup> Saturday</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="submit" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            </form> --}}
            {!!Form::open(['id' => 'role-permission-employees-form' , 'method' => 'post'])!!}
                <div class="modal-body">
                	<div class="add-role-permission-employees-modal-html">

                    </div>
                </div>
                <div class="modal-footer justify-content-right">
                    <button type="submit" class="btn submit btn-secondary bg-theme btn-theme add-update-button" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
                    <button type="button" class="btn submit btn-outline-secondary modal-close-btn"data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans("messages.close") }}</button>
                </div>
                <input type="hidden" name="record_id" value="">
			{!!Form::close()!!}
        </div>
    </div>
</div>
<script>
var role_permission_module_url = '{{config("constants.ROLES_AND_PERMISSION_MASTER_URL")}}' + '/';

    $("#role-permission-employees-form").validate({
        errorClass: "invalid-input",
        onfocusout: false,
        onkeyup: false,
        rules: {
            'employees[]': {
                required: false,
            }
        },
        // messages: {     
        //     employees[]: {
        //         required: "",
        //     },
        // },
        submitHandler: function(form) {
            showLoader()
            var employees = $.trim($("[name='employees[]']").val());
            var record_id = $.trim($("[name='record_id']").val());
            
            $.ajax({
                type : 'post',
                data : { 'employees' : employees , 'record_id' : record_id},
                dataType : 'json',
                url : role_permission_module_url + 'assign-employee',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend : function(){
                    showLoader();
                },
                success : function(response){
                    hideLoader();
                    if(response.status_code == 1){
                        var searchFieldName = searchField();
                        searchAjax(role_permission_module_url + 'filter' , searchFieldName);
                        alertifyMessage('success' , response.message);
                        $('#assign_employee').modal('hide');
                    }else{
                        alertifyMessage('error' , response.message);
                    }
                },
                error : function(){
                    hideLoader();
                }
            });
        }
    });

function searchField(){
	var search_by = $.trim($('[name="search_by"]').val());
	
	var searchData = {
            'search_by':search_by
        }
        return searchData;
}
function filterData(){
	var searchFieldName = searchField();

	searchAjax(role_permission_module_url + 'filter' , searchFieldName);
}
var paginationUrl = role_permission_module_url + 'filter'

function assignEmployeesModal(thisitem){
    var record_id = $.trim($(thisitem).attr('data-record-id'));

    $.ajax({
        type : 'post',
		data : {'record_id' : record_id},
		url : role_permission_module_url + 'get-employees',
		headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	 	},
		beforeSend : function(){
			showLoader();
		},
		success : function(response){
			hideLoader();
			$('.add-role-permission-employees-modal-html').html(response)
			if(record_id != '' && record_id != null){
				$("[name='record_id']").val(record_id);
				$('.add-update-button').text('{{ trans("messages.update") }}')
				$('.add-update-button').attr('title','{{ trans("messages.update") }}')
			}else{
				$("[name='record_id']").val('');
				$('.add-update-button').text('{{ trans("messages.add") }}')
				$('.add-update-button').attr('title','{{ trans("messages.add") }}')
			}
			openBootstrapModal("assign_employee");
            $('.select2').select2();
		},
		error : function(){
			hideLoader();
		}
    })
}

</script>

<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script> 
@endsection
