@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.manage-attendance-manually") }}</h1>
        <span class="head-total-counts total-record-count">1</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <?php /* ?>
            <button type="button" title="{{ trans('messages.export-excel') }}" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel mr-0 mr-sm-2 fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            <?php */ ?>
            @if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ))
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" onclick="showSyncAttendanceModal(this);" title="{{ trans('messages.sync-attendance') }}"><i class="fas fa-sync mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.sync-attendance") }} </span></button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        {{ Wild_tiger::readMessage() }}
        {!! Form::open(array( 'id '=> 'update-attendance-form' , 'method' => 'post' ,'url' => 'update-attendance')) !!}
        <input type="hidden" name="display_record_ids" value="">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 ">
                <div class="row depedent-row">
                	@if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_MANAGE_ATTENDANCE_LIST'), session()->get('user_permission')  ) ) ) ) )
                	<div class="col-xl-2 col-lg-4 col-12">
                		<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' ) ); ?> 
                	</div>
	                @endif
                    <div class="col-xl-3 col-lg-4 col-12">
                    <?php echo statusWiseEmployeeList('search_employee' , (isset($employeeDetails) ? $employeeDetails : [] ));?>
                    </div>
					<div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_from_date">{{ trans("messages.from-date") }}</label>
                            <input type="text" name="search_from_date" class="form-control" value="{{ ( isset($startDate) ? clientDate($startDate)  : '' ) }}" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_to_date">{{ trans("messages.to-date") }}</label>
                            <input type="text" name="search_to_date" class="form-control" value="{{ ( isset($endDate) ? clientDate($endDate)  : '' ) }}" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
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
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_attendance_status">{{ trans("messages.status") }}</label>
                            <select class="form-control" name="search_attendance_status" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{config('constants.PRESENT_STATUS')}}">{{ trans("messages.present") }}</option>
                                <option value="{{config('constants.ABSENT_STATUS')}}">{{ trans("messages.absent") }}</option>
                                <option value="{{config('constants.HALF_LEAVE_STATUS')}}">{{ trans("messages.half-leave") }}</option>
                            </select>
                        </div>
                    </div>
                    @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_MANAGE_ATTENDANCE_LIST'), session()->get('user_permission')  ) ) ) ) ) )
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_manually_change_status">{{ trans("messages.modified-time") }}</label>
                            <select class="form-control" name="search_manually_change_status" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{config('constants.SELECTION_YES')}}">{{ trans("messages.yes") }}</option>
                                <option value="{{config('constants.SELECTION_NO')}}">{{ trans("messages.no") }}</option>
                            </select>
                        </div>
                    </div>
                    @endif
					<div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" onclick="filterData();" title="{{ trans('messages.search') }}">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
            
            <div class="card card-body">
                <div class="table-responsive fixed-tabel-body append-btn-table">
                    <table class="table table-sm table-bordered text-left mb-0">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th style="width:120px; min-width:120px; ">{{ trans("messages.date") }}<br> {{ trans("messages.day") }}</th>
                                <th class="text-left employee-name-code-th" style="width:200px;min-width:200px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                <th style="min-width:105px;width:auto;">{{ trans("messages.device-in-time") }}</th>
                                <th class="text-left" style="min-width:105px;">{{ trans("messages.device-out-time") }}</th>
                                <th style="min-width:105px;width:auto;">{{ trans("messages.in-time") }}</th>
                                <th class="text-left" style="min-width:105px;">{{ trans("messages.out-time") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.total-hours") }}</th>
                                <th class="text-left" style="min-width:95px;">{{ trans("messages.break-time") }}</th>
                                <th class="text-left" style="min-width:90px;width:auto;">{{ trans("messages.working-hours") }}</th>
                                <th class="text-center" style="min-width:135px;">{{ trans("messages.status") }}</th>
                            </tr>
                        </thead>
                        <tbody class="ajax-view">
                            @include( config('constants.AJAX_VIEW_FOLDER') . 'my-attendance/edit-attedance-list')
						</tbody>
                    </table>
                </div>
                <div class="card-footer bottom-sticky-div sticky-record-selection" {{  ( isset($recordDetails) && (count($recordDetails) > 0 ) ) ? '' : 'style=display:none;' }}>
                    <div class="col-md-12 d-flex justify-content-center align-items-center">
                        <button type="button" class="btn btn-sm btn-success" title="{{ trans('messages.update-attendance') }}" onclick="updateAttendance(this);">{{ trans('messages.update-attendance') }}</button>
                    </div>
                </div>
            </div>
             
        </div>
        {!! Form::close() !!}
    </div>
</main>

<div class="modal fade document-folder" id="sync-attendance-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.sync-attendance") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'sync-attendance-form' , 'method' => 'post' , 'url' => 'sync-attendance' )) !!}
                <div class="modal-body add-address-model-html">
                    <div class="row">
                    	<div class="col-md-6">
                    		<div class="from-group">
                    			<label class="control-label">{{ trans('messages.attendance-date') }} <span class="text-danger">*</span></label>
                    			<input type="text" class="form-control" placeholder="{{ config('constants.DEFAULT_DATE_FORMAT') }}" name="sync_attendance_date">
                    			
                    		</div>
                    	</div>
                    	<div class="col-md-12">
                    		<label class="control-label">{{ trans('messages.sync-attendance-note') }}</label>
                    	</div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end">
                	<button type="submit"  class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    $("[name='search_from_date'],[name='search_to_date']").datetimepicker({
        useCurrent: false,
        ignoreReadonly: true,
        format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
        showClose: true,
        showClear: false,
        icons: {
            //clear: 'fa fa-trash',
        },
        //maxDate:moment().endOf('d'),
        widgetPositioning: {
            horizontal: 'auto',
            vertical: 'bottom'
        },
    });

    $("[name='sync_attendance_date']").datetimepicker({
        useCurrent: false,
        viewMode: 'days',
        ignoreReadonly: true,
        format: "{{ config('constants.DEFAULT_DATE_FORMAT') }}",
        showClear: true,
        showClose: true,
        maxDate:moment().endOf('d'),
        widgetPositioning: {
            vertical: 'bottom',
            horizontal: 'auto'

        },
        icons: {
            clear: 'fa fa-trash',
            Close: 'fa fa-trash',
        },
    });
	<?php if( isset($startDate) && (!empty(isset($startDate))) ) { ?>
    	$("[name='search_from_date']").datetimepicker().on('dp.change');
    <?php } ?>

    <?php if( isset($endDate) && (!empty(isset($endDate))) ) { ?>
		$("[name='search_to_date']").datetimepicker().on('dp.change');
	<?php } ?>

	

	$("#sync-attendance-form").validate({
        errorClass: "invalid-input",
        rules: {
        	sync_attendance_date: {
                required: true, noSpace: true
            },
        },
        messages: {
        	sync_attendance_date: {
                required: "{{ trans('messages.require-attendance-date') }}"
            },
        },
        submitHandler: function(form) {
        	alertify.confirm("{{ trans('messages.sync-attendance') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.sync-attendance')]) }}",function() {
            	showLoader()
            	form.submit();
        	},function() {});
        }
    });
	
    $(function(){
    	 $("[name='search_from_date']").datetimepicker().on('dp.change', function(e) {
     		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
     			var incrementDay = moment((e.date)).startOf('d');
     		 	$("[name='search_to_date']").data('DateTimePicker').minDate(incrementDay);
     		} else {
     			$("[name='search_to_date']").data('DateTimePicker').minDate(false);
     		} 
     		
     	    $(this).data("DateTimePicker").hide();
     	});

         $("[name='search_to_date']").datetimepicker().on('dp.change', function(e) {
         	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
     	        var decrementDay = moment((e.date)).endOf('d');
     	        $("[name='search_from_date']").data('DateTimePicker').maxDate(decrementDay);
         	} else {
         		 $("[name='search_from_date']").data('DateTimePicker').maxDate(false);
             }
             $(this).data("DateTimePicker").hide();
         });
    });

    function updateAttendance(){

    	var end_time_error = false;
    	var status_error = false;
    	var start_time_error = false;

    	$(".ajax-view tr").each(function(){
			var start_time = $.trim($(this).find('.start-time').val());
			var end_time = $.trim($(this).find('.end-time').val());
			var attendance_status = $.trim($(this).find('.attendance-status').val());

			if( start_time != "" && start_time != null ){
				if( end_time == "" || end_time == null && (  ( end_time_error != true ) && ( status_error != true ) ) ){
					$(this).find('.end-time').focus()
					end_time_error = true;
				}

				if( attendance_status == "" || attendance_status == null && (  ( end_time_error != true ) && ( status_error != true ) ) ){
					$(this).find('.attendance-status').focus()
					status_error = true;
				}	
			}

			if( ( attendance_status != "" && attendance_status != null  && attendance_status  != "{{ config('constants.ABSENT_STATUS') }}" ) && (  ( end_time_error != true ) && ( status_error != true ) )  ){
				//console.log("attendance_status = " + attendance_status );
				//console.log("start_time = " + start_time );
				if( start_time == "" || start_time == null ){
					//console.log("start_time = error " );
					$(this).find('.start-time').focus();
					start_time_error = true;
				}
			}
			
        });

        if( end_time_error != false ){
        	alertifyMessage('error' , "{{ trans('messages.required-out-time') }}");
        	return false;	
        }

        if( status_error != false ){
        	alertifyMessage('error' , "{{ trans('messages.required-status-selection') }}");
        	return false;	
        }

        if( start_time_error != false ){
        	alertifyMessage('error' , "{{ trans('messages.required-in-time') }}");
        	return false;	
        }

        var all_display_record_ids = [];
        $(".ajax-view .has-record").each(function(){
			var row_record_id = $.trim($(this).attr('data-record-id'));
			if( row_record_id != "" && row_record_id != null ){
				all_display_record_ids.push(row_record_id);
			}
        });
    	 alertify.confirm("{{ trans('messages.update-attendance') }}","{{ trans('messages.common-confirm-msg' , [ 'module' =>  trans('messages.update-attendance') ] ) }}",function() {
			$("[name='display_record_ids']").val(all_display_record_ids);
 			$("#update-attendance-form").submit();
    	 },function() {});	 

    }


    $(document).ready(function(){
    	$(".start-time,.end-time").mdtimepicker({ 
			readOnly: false, 
			theme: 'blue', 
			clearBtn: true, 
			datepicker : false, 
			ampm: true, 
			format: 'h:mm tt' 
		});
    	$(".start-time").trigger('change');
	});

    $(".start-time,.end-time").on('change' , function(){
    	calculateAttedanceDuration(this);
    });

    function calculateAttedanceDuration(thisitem){
    	var start_time = $.trim($(thisitem).parents('tr').find('.start-time').val());
		var end_time = $.trim($(thisitem).parents('tr').find('.end-time').val());

		var field_name = $.trim($(thisitem).attr("name"));

		if( start_time != "" && start_time != null && end_time != null && end_time != "" ){
			var duration = diffBetweenTimeIntoJS(start_time, end_time, thisitem);
			$(thisitem).parents('tr').find('.duration-text').html(duration);
		} else {
			$(thisitem).parents('tr').find('.duration-text').html("");
		}
	}

    function searchField(){
    	var search_employee = $.trim($('[name="search_employee"]').val());
    	var search_from_date = $.trim($('[name="search_from_date"]').val());
    	var search_to_date = $.trim($('[name="search_to_date"]').val());
    	var search_team = $.trim($('[name="search_team"]').val());
    	var search_attendance_status  = $.trim($('[name="search_attendance_status"]').val());
    	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
    	var search_manually_change_status  = $.trim($('[name="search_manually_change_status"]').val()); 

    	
    	
    	var searchData = {
            'search_employee':search_employee,
            'search_from_date': search_from_date,
        	'search_to_date': search_to_date,
        	'search_team': search_team,
        	'search_attendance_status': search_attendance_status,
        	'search_manually_change_status': search_manually_change_status,
        	'search_employment_status' : search_employment_status 
        }
		return searchData;
    }

    function filterData(){
    	var searchFieldName = searchField();

    	if( searchFieldName.search_from_date == "" || searchFieldName.search_from_date == null ){
    		alertifyMessage('error','{{ trans("messages.required-from-date") }}');
    		return false;
        }

    	if( searchFieldName.search_to_date == "" || searchFieldName.search_to_date == null ){
    		alertifyMessage('error','{{ trans("messages.required-to-date") }}');
    		return false;
        }
    	
		searchAjax(site_url + 'filter-edit-attedance' , searchFieldName);
	}
	var  paginationUrl = site_url  + 'filter-edit-attedance';

	function showSyncAttendanceModal(){
		openBootstrapModal('sync-attendance-modal');
	}
	
</script>
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>

@endsection 