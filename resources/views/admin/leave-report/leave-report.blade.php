@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.leave-report") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
            <button type="button" title="{{ trans('messages.export-excel') }}" onclick="exportData()" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel mr-0 mr-sm-2 fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">

        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 depedent-row">
                <div class="row">
                	@if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_LEAVE_REPORT'), session()->get('user_permission')  ) ) ) ) )
                	<div class="col-xl-2 col-lg-4 col-12">
                		<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' )  , (isset($allPermissionId) ? $allPermissionId : '' ) ); ?> 
                	</div>
                    <div class="col-xl-3 col-lg-4 col-12">
                        <?php echo statusWiseEmployeeList('search_employee_name' , (isset($employeeDetails) ? $employeeDetails : [] ) ); ?> 
                    </div>
				
					<div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <?php 
                                if(!empty($teamRecordDetails)){
                                	foreach ($teamRecordDetails as $teamRecordDetail){
                                		$encodeId = Wild_tiger::encode($teamRecordDetail->i_id);
                                		?>
                                		<option value="{{ $encodeId }}">{{ (!empty($teamRecordDetail->v_value) ? $teamRecordDetail->v_value :'')}}</option>
                                		<?php 
                                		
                                	}
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                	@endif
               		
                    <div class="form-group col-lg-2 col-md-3 col-sm-6">
                        <label for="search_leave_from_date" class="control-label">{{ trans("messages.leave-from-date") }}</label>
                        <input type="text" class="form-control" name="search_leave_from_date" value="{{ ( isset($startDate) ? clientDate($startDate) : '' ) }}"  placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    <div class="form-group col-lg-2 col-md-3 col-sm-6">
                        <label for="search_leave_to_date" class="control-label">{{ trans("messages.leave-to-date") }}</label>
                        <input type="text" class="form-control" name="search_leave_to_date" value="{{ ( isset($endDate) ? clientDate($endDate) : '' ) }}"  placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>

                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_leave_type">{{ trans("messages.leave-type") }}</label>
                            <select class="form-control" name="search_leave_type" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <?php 
                                if(!empty($leaveTypeDetails)){
                                	foreach ($leaveTypeDetails as $leaveTypeDetail){
                                		$encodedId = Wild_tiger::encode($leaveTypeDetail->i_id);
                                		?>
                                		<option value="{{ $encodedId }}">{{(!empty($leaveTypeDetail->v_leave_type_name) ? $leaveTypeDetail->v_leave_type_name :'')}}</option>
                                		<?php 
                                	}
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_leave_status">{{ trans("messages.status") }}</label>
                            <select class="form-control" name="search_leave_status" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <?php 
                                if(!empty($stausInfo)){
                                	foreach ($stausInfo as $key => $staus){
                                		$selected = "";
                                		if( isset($selectedLeaveStatus) && in_array( $key , $selectedLeaveStatus  ) ){
                                			$selected = "selected='selected'";
                                		}
                                		
                                		?>
                                		<option value="{{ $key }}" <?php echo $selected ?> >{{(!empty($staus) ? $staus :'')}}</option>
                                		<?php 
                                	}
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_leave_duration">{{ trans("messages.duration") }}</label>
                            <select class="form-control" name="search_leave_duration" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.FULL_DAY_LEAVE')}}">{{ trans("messages.full-day") }}</option>
                                <option value="{{ config('constants.FIRST_HALF_LEAVE')}}">{{ trans("messages.first-half") }}</option>
                                <option value="{{ config('constants.SECOND_HALF_LEAVE')}}">{{ trans("messages.second-half") }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_auto_approve_leave">{{ trans("messages.auto-approve-leave") }}</label>
                            <select class="form-control" name="search_auto_approve_leave" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.SELECTION_YES')}}">{{ trans("messages.yes") }}</option>
                                <option value="{{ config('constants.SELECTION_NO')}}">{{ trans("messages.no") }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData()">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
            <div class="card card-body">
             {{ Wild_tiger::readMessage() }}
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.employee-name-code") }} <br> {{ trans("messages.contact-number") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="width:167px;min-width:167px;">{{ trans("messages.leave-dates") }} <br> {{ trans("messages.no-of-days") }}</th>
                                <th class="text-left" style="width:130px;min-width:130px;">{{ trans("messages.leave-type") }} <br> {{ trans("messages.requested-on") }}</th>
                                <th class="text-left" style="width:135px;min-width:135px;">{{ trans("messages.requested-by") }}</th>
                                <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.status") }} <br> {{ trans("messages.action-taken-by") }}</th>
                                <th class="text-left" style="width:158px;min-width:158px;">{{ trans("messages.action-taken-on") }}</th>
                                <th class="text-center" style="width:173px;min-width:173px;">{{ trans("messages.actions") }}</th>
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                            @include( config('constants.AJAX_VIEW_FOLDER') . 'leave-report/leave-report-list')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade document-folder document-type" id="leave-approve-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-approve-modal-header-name" id="exampleModalLabel">{{ trans("messages.approve-reject-leave")}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-leave-approve-form' , 'method' => 'post' ,  'url' => 'addLeaveReport')) !!}
	                <div class="modal-body add-approve-html">
	                    
	                </div>
	
	                <div class="modal-footer justify-content-end">
	                	<input type="hidden" name="status" value="">
	                	<input type="hidden" name="record_id" value="">
	                    <button type="button" style="display: none" onclick="addLeaveReport()" class="btn bg-theme text-white action-button leave-report-modal-action-button btn-add justify-content-center align-items-center" name="submit" title="{{ trans('messages.confirm') }}">{{ trans('messages.confirm') }}</button>
	                    <button type="button" class="btn btn-outline-secondary btn-add d-flex justify-content-center align-items-center" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
	                </div>
				{!! Form::close() !!}
            </div>
        </div>
    </div>
</main>
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
<script>

$(function() {
    $('[name="search_leave_from_date"], [name="search_leave_to_date"]').datetimepicker({
        useCurrent: false,
        viewMode: 'days',
        ignoreReadonly: true,
        format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
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
    $("[name='search_leave_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
			var incrementDay = moment((e.date)).startOf('d');
		 	$("[name='search_leave_to_date']").data('DateTimePicker').minDate(incrementDay);
		} else {
			$("[name='search_leave_to_date']").data('DateTimePicker').minDate(false);
		} 
		
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_leave_to_date']").datetimepicker().on('dp.change', function(e) {
    	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
	        var decrementDay = moment((e.date)).endOf('d');
	        $("[name='search_leave_from_date']").data('DateTimePicker').maxDate(decrementDay);
    	} else {
    		 $("[name='search_leave_from_date']").data('DateTimePicker').maxDate(false);
        }
        $(this).data("DateTimePicker").hide();
    });
});
 
 var leave_report_url = '{{config("constants.LEAVE_REPORT_URL")}}' + '/';
 
 function searchField(){
 	var search_employee_name = $.trim($('[name="search_employee_name"]').val());
 	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
 	var search_team = $.trim($('[name="search_team"]').val());
 	var search_leave_from_date = $.trim($('[name="search_leave_from_date"]').val());
 	var search_leave_to_date  = $.trim($('[name="search_leave_to_date"]').val());
 	var search_leave_type = $.trim($('[name="search_leave_type"]').val());
 	var search_leave_status = $.trim($('[name="search_leave_status"]').val());
 	var search_leave_duration = $.trim($('[name="search_leave_duration"]').val());
 	var search_auto_approve_leave  = $.trim($('[name="search_auto_approve_leave"]').val()); 
 	
 	var searchData = {
             'search_employee_name':search_employee_name,
             'search_employment_status':search_employment_status,
             'search_team': search_team,
             'search_leave_from_date': search_leave_from_date,
             'search_leave_to_date':search_leave_to_date,
             'search_leave_type':search_leave_type,
             'search_leave_status':search_leave_status,
             'search_leave_duration':search_leave_duration,
             'search_auto_approve_leave':search_auto_approve_leave,
         }
         return searchData;
 }
 function filterData(){
 	var searchFieldName = searchField();

 	searchAjax(leave_report_url + 'filter' , searchFieldName);
 }
 var paginationUrl = leave_report_url + 'filter'
 
 	function exportData(){
		var searchData = searchField();
		var export_info = {};
		export_info.url = leave_report_url + 'filter';
		export_info.searchData = searchData;
		dataExportIntoExcel(export_info);
	}

 $("#add-leave-approve-form").validate({
     errorClass: "invalid-input",
     rules: {
    	 leave_approve_reject_reason: {
             required: true,noSpace:true
         },
     },
     messages: {
    	 leave_approve_reject_reason: {
             required: "{{ trans('messages.require-reason') }}"
         },

     }
 });
 	var current_row ='';
	function openApproveModel(thisitem){
		current_row = thisitem;
		
		var employee_id = $.trim($(thisitem).attr('data-employee-id'));
		var leave_report_id = $.trim($(thisitem).attr('data-leave-id'));
		var status = $.trim($(thisitem).attr('data-status'));
		if(status !="" && status != null){
			$("[name='status']").val(status);
		}
		$("[name='record_id']").val(leave_report_id);
	
		$.ajax({
    		type: "POST",
    		url: leave_report_url + 'approveLeave',
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'employee_id':employee_id,
    			'status':status,
    			'leave_report_id':leave_report_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			var header_name = $.trim($(thisitem).attr('title'));
    			if(status  !="" && status != null && status != '{{ config("constants.VIEW_RECORD") }}'  ){
        			$('.leave-report-modal-action-button').show();
        		} else {
        			$('.leave-report-modal-action-button').hide();
            	}
    			$('.add-approve-html').html("");
    			$('.add-approve-html').html(response);
    			$("#leave-approve-modal").find('.twt-approve-modal-header-name').html(header_name);
    			openBootstrapModal('leave-approve-modal');
    		},
    		error: function() {
    			hideLoader();
    		}
    	});	
	}
	
	function addLeaveReport(){
		if($('#add-leave-approve-form').valid() != true){
			return false;
		}
		var status = $.trim($('[name="status"]').val());
		var leave_approve_reject_reason = $.trim($('[name="leave_approve_reject_reason"]').val());
		var record_id = $.trim($('[name="record_id"]').val());

		var confirm_box = "";
		var confirm_box_msg = "";
		
		if(status !="" && status != null){
			switch(status){
				case "{{config('constants.APPROVED_STATUS')}}" :
					confirm_box = "{{ trans('messages.approve-leave')}}";
					confirm_box_msg = "{{ trans('messages.update-status-msg',['module'=> trans('messages.approve')]) }}";
					break;
				case "{{config('constants.REJECTED_STATUS')}}" :
					confirm_box = "{{ trans('messages.reject-leave')}}";
					confirm_box_msg = "{{ trans('messages.update-status-msg',['module'=> trans('messages.reject')]) }}";
					break;
				case "{{config('constants.CANCELLED_STATUS')}}" :
					confirm_box = "{{ trans('messages.cancel-leave')}}";
					confirm_box_msg = "{{ trans('messages.update-status-msg',['module'=> trans('messages.cancel')]) }}";
					break;
			}
		}
	    
	    alertify.confirm(confirm_box,confirm_box_msg,function() {   
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "{{config('constants.MY_LEAVES_MASTER_URL')}}" + '/updateLeaveStatus',
				data: {"_token": "{{ csrf_token() }}", 'leave_approve_reject_reason'  : leave_approve_reject_reason ,  'status':status, 'record_id':record_id,'row_index':$(current_row).parents('tr').find('.sr-col').html(), },
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					
					if( response.status_code == 1 ){
						$("#leave-approve-modal").modal('hide');
						alertifyMessage('success',response.message);
						if(record_id != '' && record_id != null){
							$(current_row).parents('.leave-report-list').html(response.data.html);
						}
						
					} else {
						alertifyMessage('error',response.message);
					}
					
				},
				error: function() {
					hideLoader();
				}
			});
	    },function() {});
	}
</script>

<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection