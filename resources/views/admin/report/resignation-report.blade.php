@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.resignation-report") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
            <button type="button" title="{{ trans('messages.export-excel') }}" onclick="exportData()" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel fa-fw mr-0 mr-sm-2"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 depedent-row">
                <div class="row">
                 @if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN'),config('constants.ROLE_USER') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_RESIGNATION_REPORT'), session()->get('user_permission')  ) ) ) ))
					<div class="col-xl-2 col-lg-4 col-12">
					<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' )  );?>
					</div>
					<div class="col-xl-3 col-lg-4 col-12">
					<?php echo statusWiseEmployeeList('search_employee_name_code' , (isset($employeeDetails) ? $employeeDetails : [] ));?>
					</div>
                   @endif
                    <div class="col-xl-2 col-md-3 col-sm-6 col-12">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team" onchange="filterData()">
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
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_designation" class="control-label">{{ trans('messages.designation') }}</label>
                            <select class="form-control select2" name="search_designation" onchange="filterData()">
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
                    @if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_RESIGNATION_REPORT'), session()->get('user_permission')  ) ) ) ) )
                    <div class="col-xl-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_report_to">{{ trans("messages.leader-name-reporting-manager") }}</label>
                            <select name="search_report_to" class="form-control select2" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($leaderDetails))
                                	@foreach($leaderDetails as $leaderDetail)
                                		@php $encodeId = Wild_tiger::encode($leaderDetail->i_id); @endphp 
                                		<option value="{{ $encodeId }}">{{ (!empty($leaderDetail->v_employee_full_name) ? $leaderDetail->v_employee_full_name .(!empty($leaderDetail->v_employee_code) ? ' ('.$leaderDetail->v_employee_code .')' : ''): '') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
                            <select name="search_status" class="form-control" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($stausInfo))
                                	@foreach($stausInfo as $key=> $staus)
                                		<option value="{{ $key }}">{{ (!empty($staus) ? $staus : '') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_RESIGNATION_REPORT'), session()->get('user_permission')  ) ) ) ) )
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_type">{{ trans("messages.exit-type") }}</label>
                            <select name="search_type" class="form-control" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') }}">{{ trans('messages.resignation') }}</option>
                                <option value="{{ config('constants.EMPLOYER_INITIATE_EXIT_TYPE') }}">{{ trans('messages.termination') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 resign-status-div" style="display:none;">
                        <div class="form-group">
                            <label class="control-label" for="search_status">{{ trans("messages.reason-for-leaving") }}</label>
                            <select name="search_resign_status" class="form-control" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($resignationReasonDetails))
                                	@foreach($resignationReasonDetails as $key=> $resignationResignDetail)
                                		@php $encodeResignId = Wild_tiger::encode($resignationResignDetail->i_id); @endphp
                                		<option value="{{ $encodeResignId }}">{{ (!empty($resignationResignDetail->v_value) ? $resignationResignDetail->v_value : '') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 terminate-status-div" style="display:none;">
                        <div class="form-group">
                            <label class="control-label" for="search_status">{{ trans("messages.termination-reason") }}</label>
                            <select name="search_terminate_status" class="form-control" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($terminationReasonDetails))
                                	@foreach($terminationReasonDetails as $key=> $terminationReasonDetail)
                                		@php $encodeTerminateId = Wild_tiger::encode($terminationReasonDetail->i_id); @endphp
                                		<option value="{{ $encodeTerminateId }}">{{ (!empty($terminationReasonDetail->v_value) ? $terminationReasonDetail->v_value : '') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="form-group col-lg-3 col-md-3 col-sm-6 col-6">
                        <label for="search_notice_period_start_from_date" class="control-label">{{ trans("messages.notice-period-start-from-date") }}</label>
                        <input type="text" class="form-control" name="search_notice_period_start_from_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    <div class="form-group col-lg-3 col-md-3 col-sm-6 col-6">
                        <label for="search_notice_period_start_to_date" class="control-label">{{ trans("messages.notice-period-start-to-date") }}</label>
                        <input type="text" class="form-control date" name="search_notice_period_start_to_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    <div class="form-group col-lg-3 col-md-3 col-sm-6 col-6">
                        <label for="search_notice_period_end_from_date" class="control-label">{{ trans("messages.notice-period-expected-end-from-date") }}</label>
                        <input type="text" class="form-control" name="search_notice_period_end_from_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    <div class="form-group col-lg-3 col-md-3 col-sm-6 col-6">
                        <label for="search_notice_period_end_to_date" class="control-label">{{ trans("messages.notice-period-expected-end-to-date") }}</label>
                        <input type="text" class="form-control date" name="search_notice_period_end_to_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" onclick="filterData()" title="{{ trans('messages.search') }}">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
            <div class="card card-body">
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                @if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_RESIGNATION_REPORT'), session()->get('user_permission')  ) ) ) ) )
                                <th class="text-left" style="min-width:70px;">{{ trans("messages.exit-type") }} - {{ trans("messages.reason-for-leaving") }}</th>
                                @endif
                                <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="min-width:70px;">{{ trans("messages.designation") }}</th>
                                <th class="text-left" style="width:100px; min-width:108px;">{{ trans("messages.leader-name-reporting-manager") }}</th>
                                <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.mobile") }}</th>
                                <th class="text-left" style="min-width:160px;">{{ trans("messages.email") }}</th>
                                <th class="text-left" style="min-width:160px;">{{ trans("messages.notice-period-start-date") }}</th>
                                <th class="text-left" style="min-width:150px;">{{ trans("messages.notice-period-expected-end-date") }}</th>
                                <th class="text-left"  style="min-width:180px;">{{ trans("messages.status") }} <br> {{ trans("messages.action-taken-by") }} <br> {{ trans("messages.remark") }} </th>
                                <th class="text-center" style="min-width:130px;">{{ trans("messages.actions") }}</th>
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                        	@include( config('constants.AJAX_VIEW_FOLDER') . 'report/resignation-report-list')
                       </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</main>

@include( config('constants.ADMIN_FOLDER') . 'employee-master/emp-resign-form')


<script>
var resignation_report_url = '{{config("constants.REGIGNATION_REPORT_URL")}}' + '/';

    	$(function() {
    	  $('[name="search_notice_period_start_from_date"], [name="search_notice_period_start_to_date"]').datetimepicker({
    	        useCurrent: false,
    	        viewMode: 'days',
    	        ignoreReadonly: true,
    	        format: 'DD-MM-YYYY',
    	        showClear: true,
    	        showClose: true,
    	        widgetPositioning: {
    	            vertical: 'bottom',
    	            horizontal: 'auto'

    	        },
    	        icons: {
    	            clear: 'fa fa-trash',
    	            Close: 'fa fa-trash',
    	        },
    	    });

        $("[name='search_notice_period_start_from_date']").datetimepicker().on('dp.change', function(e) {
    		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    			var incrementDay = moment((e.date)).startOf('d');
    		 	$("[name='search_notice_period_start_to_date']").data('DateTimePicker').minDate(incrementDay);
    		} else {
    			$("[name='search_notice_period_start_to_date']").data('DateTimePicker').minDate(false);
    		} 
    		
    	    $(this).data("DateTimePicker").hide();
    	});

        $("[name='search_notice_period_start_to_date']").datetimepicker().on('dp.change', function(e) {
        	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    	        var decrementDay = moment((e.date)).endOf('d');
    	        $("[name='search_notice_period_start_from_date']").data('DateTimePicker').maxDate(decrementDay);
        	} else {
        		 $("[name='search_notice_period_start_from_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });
    });

    	$(function() {
      	  $('[name="search_notice_period_end_from_date"], [name="search_notice_period_end_to_date"]').datetimepicker({
      	        useCurrent: false,
      	        viewMode: 'days',
      	        ignoreReadonly: true,
      	        format: 'DD-MM-YYYY',
      	        showClear: true,
      	        showClose: true,
      	        widgetPositioning: {
      	            vertical: 'bottom',
      	            horizontal: 'auto'

      	        },
      	        icons: {
      	            clear: 'fa fa-trash',
      	            Close: 'fa fa-trash',
      	        },
      	    });

          $("[name='search_notice_period_end_from_date']").datetimepicker().on('dp.change', function(e) {
      		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
      			var incrementDay = moment((e.date)).startOf('d');
      		 	$("[name='search_notice_period_end_to_date']").data('DateTimePicker').minDate(incrementDay);
      		} else {
      			$("[name='search_notice_period_end_to_date']").data('DateTimePicker').minDate(false);
      		} 
      		
      	    $(this).data("DateTimePicker").hide();
      	});

          $("[name='search_notice_period_end_to_date']").datetimepicker().on('dp.change', function(e) {
          	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
      	        var decrementDay = moment((e.date)).endOf('d');
      	        $("[name='search_notice_period_end_from_date']").data('DateTimePicker').maxDate(decrementDay);
          	} else {
          		 $("[name='search_notice_period_end_from_date']").data('DateTimePicker').maxDate(false);
              }
              $(this).data("DateTimePicker").hide();
          });
      });
        
    function searchField(){
    	var search_employee_name_code = $.trim($('[name="search_employee_name_code"]').val());
    	var search_team = $.trim($('[name="search_team"]').val());
    	var search_designation = $.trim($('[name="search_designation"]').val());
    	var search_notice_period_start_from_date = $.trim($('[name="search_notice_period_start_from_date"]').val());
    	var search_notice_period_start_to_date = $.trim($('[name="search_notice_period_start_to_date"]').val());
    	var search_report_to = $.trim($('[name="search_report_to"]').val());
    	var search_status = $.trim($('[name="search_status"]').val());
    	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
    	var search_type = $.trim($('[name="search_type"]').val());
    	var search_resign_status = $.trim($('[name="search_resign_status"]').val());
    	var search_terminate_status = $.trim($('[name="search_terminate_status"]').val());
    	var search_notice_period_end_from_date = $.trim($('[name="search_notice_period_end_from_date"]').val());
    	var search_notice_period_end_to_date = $.trim($('[name="search_notice_period_end_to_date"]').val());
    	
    	var searchData = {
                'search_employee_name_code':search_employee_name_code,
                'search_team': search_team,
                'search_designation':search_designation,
                'search_notice_period_start_from_date':search_notice_period_start_from_date,
                'search_notice_period_start_to_date':search_notice_period_start_to_date,
                'search_report_to':search_report_to,
                'search_status':search_status,
                'search_employment_status':search_employment_status,
                'search_type':search_type,
                'search_resign_status':search_resign_status,
                'search_terminate_status':search_terminate_status,
                'search_notice_period_end_from_date':search_notice_period_end_from_date,
                'search_notice_period_end_to_date':search_notice_period_end_to_date,
                
            }
            return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(resignation_report_url + 'resignationReportFilter' , searchFieldName);
    }
    var paginationUrl = resignation_report_url + 'resignationReportFilter'
   
    function exportData(){
   		var searchData = searchField();
   		var export_info = {};
   		export_info.url = resignation_report_url + 'resignationReportFilter';
   		export_info.searchData = searchData;
   		dataExportIntoExcel(export_info);
   	}

   	$("[name='search_type']").on("change" , function(){
		var type = $.trim($("[name='search_type']").val());
		console.log(type);
		if( type != "" && type != null ){
			switch(type){
				case "{{ config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') }}":
					$(".resign-status-div").show();
					$(".terminate-status-div").hide();
					break;
				case "{{ config('constants.EMPLOYER_INITIATE_EXIT_TYPE') }}":
					$(".resign-status-div").hide();
					$(".terminate-status-div").show();
					break;
				default:
					$(".resign-status-div").hide();
					$(".terminate-status-div").hide();
					break;	
			}
		} else {
			$(".resign-status-div").hide();
			$(".terminate-status-div").hide();
		}

   	})
</script>
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script> 


@endsection