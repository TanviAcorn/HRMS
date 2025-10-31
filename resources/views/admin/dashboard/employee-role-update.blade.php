@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.manage-role") }}</h1>
        <span class="head-total-counts total-record-count">1</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <?php /* ?>
            <button type="button" title="{{ trans('messages.export-excel') }}" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel mr-0 mr-sm-2 fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            <?php */ ?>
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        {{ Wild_tiger::readMessage() }}
        {!! Form::open(array( 'id '=> 'update-role-form' , 'method' => 'post' ,'url' => 'update-employee-role')) !!}
        <input type="hidden" name="display_record_ids" value="">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 ">
                <div class="row depedent-row">
                    @if(session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false)
		                @if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN'),config('constants.ROLE_USER') ] ))
		                	<div class="col-xl-2 col-lg-4 col-12">
		                		<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) ); ?> 
		                	</div>
		                @endif
	                @endif
                    <div class="col-xl-3 col-lg-4 col-12">
                        <div class="form-group">
                            <label class="control-label" for="search_employee">{{ trans("messages.employee-name-code") }}</label>
                            <select name="search_employee" class="form-control select2 status-wise-emp-div" onchange="filterData();">
                                <option value="">{{ trans('messages.select') }}</option>
                                @if(!empty($employeeDetails))
                                	@foreach($employeeDetails as $employeeDetail)
                                		@php $encodeEmployeeId = Wild_tiger::encode($employeeDetail->i_id) @endphp
                                		<option value="{{ $encodeEmployeeId }}">{{(!empty($employeeDetail->v_employee_full_name) ? $employeeDetail->v_employee_full_name .(!empty($employeeDetail->v_employee_code) ? ' (' .$employeeDetail->v_employee_code . ')' : ''):'')}}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
					<div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team" onchange="filterData();">
                                <option value="">{{ trans('messages.select') }}</option>
                                @if(!empty($teamDetails))
                                	@foreach($teamDetails as $teamDetail)
                                		@php $encodeTeamId = Wild_tiger::encode($teamDetail->i_id) @endphp
                                		<option value="{{ $encodeTeamId }}">{{(!empty($teamDetail->v_value) ? $teamDetail->v_value :'')}}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.designation') }}</label>
                            <select class="form-control select2" name="search_designation" onchange="filterData();">
                                <option value="">{{ trans('messages.select') }}</option>
                                @if(!empty($designationDetails))
                                	@foreach($designationDetails as $designationDetail)
                                		@php $encodeDesignationId = Wild_tiger::encode($designationDetail->i_id) @endphp
                                		<option value="{{ $encodeDesignationId }}">{{(!empty($designationDetail->v_value) ? $designationDetail->v_value :'')}}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                   <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" onclick="filterData();" title="{{ trans('messages.search') }}">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
            
            <div class="card card-body">
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered text-left mb-0">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.designation") }}</th>
                                <th class="text-center" style="width:135px;">{{ trans("messages.role") }}</th>
                            </tr>
                        </thead>
                        <tbody class="ajax-view">
                            @include( config('constants.AJAX_VIEW_FOLDER') . 'dashboard/employee-role-update-list')
						</tbody>
                    </table>
                </div>
                <div class="card-footer bottom-sticky-div sticky-record-selection" {{  ( isset($recordDetails) && (count($recordDetails) > 0 ) ) ? '' : 'style=display:none;' }}>
                    <div class="col-md-12 d-flex justify-content-center align-items-center">
                        <button type="button" class="btn btn-sm btn-success" title="{{ trans('messages.update-role') }}" onclick="updateRole(this);">{{ trans('messages.update-role') }}</button>
                    </div>
                </div>
            </div>
             
        </div>
        {!! Form::close() !!}
    </div>
</main>
<script>
  
	function updateRole(){

    	var role_selected_error = false;

		$(".ajax-view tr").each(function(){
			var selected_role = $.trim($(this).find('.selected-role').val());

			if( selected_role == "" || selected_role == null && (  ( role_selected_error != true ) ) ){
				$(this).find('.selected-role').focus()
				role_selected_error = true;
			}
		});

        if( role_selected_error != false ){
        	alertifyMessage('error' , "{{ trans('messages.required-out-time') }}");
        	return false;	
        }

        var all_display_record_ids = [];
        $(".ajax-view .has-record").each(function(){
			var row_record_id = $.trim($(this).attr('data-record-id'));
			if( row_record_id != "" && row_record_id != null ){
				all_display_record_ids.push(row_record_id);
			}
        });
    	 alertify.confirm("{{ trans('messages.update-role') }}","{{ trans('messages.common-confirm-msg' , [ 'module' =>  trans('messages.update-role') ] ) }}",function() {
			$("[name='display_record_ids']").val(all_display_record_ids);
 			$("#update-role-form").submit();
    	 },function() {});	 

    }

	function searchField(){
    	var search_employee = $.trim($('[name="search_employee"]').val());
    	var search_team = $.trim($('[name="search_team"]').val());
    	var search_designation  = $.trim($('[name="search_designation"]').val());
    	var search_employment_status = $.trim($('[name="search_employment_status"]').val());

    	
    	
    	var searchData = {
            'search_employee':search_employee,
            'search_team': search_team,
        	'search_designation': search_designation,
        	'search_employment_status' : search_employment_status 
        }
		return searchData;
    }

    function filterData(){
    	var searchFieldName = searchField();
		searchAjax(site_url + 'filter-all-employee-list' , searchFieldName);
	}
	var  paginationUrl = site_url  + 'filter-all-employee-list';
</script>
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>

@endsection