@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.roles-permissions") }}{{ ( isset($roleInfo->v_role_name) ? ' - '.$roleInfo->v_role_name : '' )    }}</h1>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <button class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none">{{ trans("messages.filter") }}</span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history salary-report">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 depedent-row">
                <div class="row">
                    @if(session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false)
		                @if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN'),config('constants.ROLE_USER') ] ))
		                	<div class="col-xl-2 col-lg-4 col-12">
		                		<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) ); ?> 
		                	</div>
		                    <div class="col-xl-3 col-lg-4 col-12">
		                        <?php echo statusWiseEmployeeList('search_employee_name' , (isset($recordDetails) ? $recordDetails : [] ) ); ?> 
		                    </div>
						@endif
	                @endif
                    <div class="col-xl-5 col-lg-5 col-12">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans("messages.search-by") }}</label>
                            <input type="text" name="search_by" class="form-control" placeholder="{{ trans('messages.search-by') }} {{ trans('messages.email') }}, {{ trans('messages.mobile') }}">
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team" onchange="filterData(this);">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($teamDetails))
                                	@foreach($teamDetails as $teamDetail)
                                		@php $encodeId = Wild_tiger::encode($teamDetail->i_id); @endphp 
                                		<option value="{{ $encodeId }}">{{ (!empty($teamDetail->v_value) ? $teamDetail->v_value :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label for="search_designation" class="control-label">{{ trans('messages.designation') }}</label>
                            <select class="form-control select2" name="search_designation" onchange="filterData(this);">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($designationDetails))
                                	@foreach($designationDetails as $designationDetail)
                                		@php $encodeId = Wild_tiger::encode($designationDetail->i_id); @endphp 
                                		<option value="{{ $encodeId }}">{{ (!empty($designationDetail->v_value) ? $designationDetail->v_value :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData(this);">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
            <div class="card card-body">
                <div class="table-responsive fixed-tabel-body append-btn-table">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="check-box-design text-center">
                                    <div class="form-group mb-0 text-center">
                                        <div class="form-check form-check-inline ml-1 mr-0">
                                            <input class="form-check-input" type="checkbox" id="check_all" name="check_all" onclick="selectAllRowCheckbox(this);checkAllAssignUser(this)">
                                            <label class="form-check-label lable-control" for="check_all"></label>
                                        </div>
                                    </div>
                                </th>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left">{{ trans("messages.email") }}</th>
                                <th class="text-left">{{ trans("messages.mobile") }}</th>
                                <th class="text-left">{{ trans("messages.team") }}</th>
                                <th class="text-left">{{ trans("messages.designation") }}</th>
                                <th class="text-left">{{ trans("messages.joining-date") }}</th>                            </tr>
                        </thead>
                        <tbody class="ajax-view">
							@include(config('constants.AJAX_VIEW_FOLDER') .'role-permission/assign-to-employees-list')
						</tbody>
                    </table>
                </div>
                <input type="hidden" name="unassing_user[]" value=''>
                <input type="hidden" name="role_permission_id" value="<?php echo (isset($rolePermissionId) && !empty($rolePermissionId) ? $rolePermissionId : '')?>">
                @if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) )
                <div class="card card-body sticky-div border-top pb-1 sticky-record-selection" {{  ( isset($recordDetails) && (count($recordDetails) > 0 ) ) ? '' : 'style=display:none;' }} >
                    <div class="total-div">
                        <a href="javascript:void(0);" class="btn-theme text-white twt-btn-style btn btn-sm mr-2" onclick="assignToEmployees(this);" title="{{ trans('messages.assign-employees') }}">{{ trans("messages.assign-employees") }}</a>
                        <a href="{{ config('constants.ROLES_AND_PERMISSION_MASTER_URL')}}" class="btn btn-outline-secondary ml-auto mr-3" title="{{ trans('messages.cancel') }}">{{ trans('messages.cancel') }}</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div>
    
    </div>
</main>

<script>
var moduleurl = '{{ config("constants.ROLES_AND_PERMISSION_MASTER_URL") }}'
function searchField(){
	var search_by = $.trim($("[name='search_by']").val());
	var search_team = $.trim($("[name='search_team']").val());
	var search_designation = $.trim($("[name='search_designation']").val());
	var role_permission_id = $.trim($("[name='role_permission_id']").val());
	var search_employment_status = $.trim($("[name='search_employment_status']").val());
	var search_employee_name = $.trim($("[name='search_employee_name']").val());

	var searchData = {
    	'search_by':search_by,
        'search_team':search_team,
        'search_designation':search_designation,
        'record_id': role_permission_id,
        'search_employment_status' : search_employment_status ,
        'search_employee_name' : search_employee_name ,
	}
	return searchData;
}

function filterData(){
	var searchFieldName = searchField();
	$("[name='unassing_user[]']").val('');
	searchAjax(moduleurl + '/filter-employee' , searchFieldName);
}
var paginationUrl = moduleurl + 'filter-employee';

function assignToEmployees(thisitem){
	var selected_record_ids = [];
	$(".row-checkbox").each(function(){
		if( $(this).prop('checked') != false ){
			selected_record_ids.push($.trim($(this).val()));
		}
	})
	
	/* if(selected_record_ids.length == 0 ){
		alertifyMessage('error' , "{{ trans('messages.required-atleast-one-record') }}");
		return false;
	} */
	
	var role_permission_id = $.trim($("[name='role_permission_id']").val());
	var unassing_user = $.trim($("[name='unassing_user[]']").val());
	var record_id = selected_record_ids;
	var request_url  = moduleurl + '/add-assign-to-employees';

	var confirm_msg = "{{ trans('messages.assign-employees') }}";
	var confirm_msg_text = "{{ trans('messages.common-confirm-msg' , [ 'module' =>  trans('messages.assign-employees')  ]) }}";

	alertify.confirm(confirm_msg,  confirm_msg_text  ,function() {
		
		$.ajax({
			type: "POST",
			dataType : 'json',
			url: request_url ,
			data: {
				"_token": "{{ csrf_token() }}",
				'record_id':record_id,
				'role_permission_id': role_permission_id,
				'unassing_user': unassing_user
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if(response.status_code == 1 ){
					alertifyMessage('success' , response.message);
					$("[name='unassing_user[]']").val('');
				} else {
					alertifyMessage('error' , response.message);
				}
			},
			error: function() {
				hideLoader();
			}
		});
	}, function () { });
		
	/* if( record_id != "" && record_id != null ){
		
	} */
}

var removeUser = []
function checkAssignUser(thisItem){
	let currentValue = $.trim($(thisItem).val());
	if ($(thisItem).prop('checked')!= true){
		if(removeUser.includes(currentValue) != true){
			removeUser.push(currentValue);
	        $('[name="unassing_user[]"]').val(removeUser);
		}
    } else {
    	if(removeUser.includes(currentValue) != false){
    		var index = removeUser.indexOf(currentValue);
    		if (index !== -1) {
    			removeUser.splice(index, 1);
    		}
	        $('[name="unassing_user[]"]').val(removeUser);
		}
    }
}

function checkAllAssignUser(thisItem){
	$($(thisItem).parents('table').find('.row-checkbox')).each(function(){
		checkAssignUser(this);
	});
}
</script>
@endsection