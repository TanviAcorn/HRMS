@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.incident-report") }}</h1>
        <span class="head-total-counts total-record-count">{{ (isset($recordDetails) ? count($recordDetails) : '') }}</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            @if(checkPermission('add_incident_report'))
                <a href="{{ config('constants.INCIDENT_REPORT_URL') . '/showAddForm' }}" class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar d-sm-flex align-items-center" title="{{ trans('messages.add-incident-report') }}"><i class="fas fa-plus mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.add-incident-report") }}</span> </a>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <?php
        $tableSearchPlaceholder = "Search By Subject, Report No.";
        ?>
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row depedent-row">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans("messages.search-by") }}</label>
                            <input type="text" name="search_by" class="form-control" placeholder="<?php echo $tableSearchPlaceholder ?>">
                        </div>
                    </div>
                    <div class="form-group col-lg-2 col-6">
                        <label for="search_from_date" class="control-label">{{ trans("messages.report-from-date") }}</label>
                        <input type="text" class="form-control" name="search_from_date" id="search_from_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    <div class="form-group col-lg-2 col-6">
                        <label for="search_to_date" class="control-label">{{ trans("messages.report-to-date") }}</label>
                        <input type="text" class="form-control" name="search_to_date" id="search_to_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>

					@if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_INCIDENT_REPORT'), session()->get('user_permission')  ) ) ) ) )
					<div class="col-xl-2 col-lg-4 col-12">
                		<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' ) );?>
                	</div>
                    <div class="col-md-6 col-lg-3 col-sm-6 col-12">
                        <div class="form-group">
                            <label class="control-label" for="search_employee">{{ trans("messages.employee-name-code") }}</label>
                            <select class="form-control select2 status-wise-emp-div" name="search_employee" multiple="multiple" onchange="filterData();">
                                <?php 
	                                if(!empty($employeeRecordDetails)){
	                                	foreach ($employeeRecordDetails as $employeeRecordDetail){
	                                		$encodeRecordId = (!empty($employeeRecordDetail->i_id) ? Wild_tiger::encode($employeeRecordDetail->i_id) : 0);
	                                		?>
	                                		<option value="{{ $encodeRecordId }}">{{ ($employeeRecordDetail->v_employee_full_name . ( ( isset($employeeRecordDetail->v_employee_code )  && (!empty($employeeRecordDetail->v_employee_code ))) ?  ' ('.$employeeRecordDetail->v_employee_code . ')' : '' )) }}</option>
	                                		<?php
	                                	}
	                                }
                                ?>
                            </select>
                        </div>
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
                    
                    <div class="col-lg-2 col-md-6 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="status">{{ trans("messages.status") }}</label>
                            <select class="form-control" name="status" onchange="filterData();">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.OPEN')  }}" {{ ( (  isset($selectedStatus) && (!empty($selectedStatus)) && ( $selectedStatus ==  config('constants.OPEN') ) ) ? 'selected' : '' ) }} >{{ trans("messages.open") }}</option>
                                <option value="{{ config('constants.CLOSE') }}" {{ ( (  isset($selectedStatus) && (!empty($selectedStatus)) && ( $selectedStatus ==  config('constants.CLOSE') ) ) ? 'selected' : '' ) }} >{{ trans("messages.close") }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData();">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper demo-actions-col">
        {{ Wild_tiger::readMessage() }}
            <div class="card card-body">
                <div class="table-responsive fixed-tabel-body incident-report-table">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="min-width:100px;">{{ trans("messages.report-no") }}.</th>
                                <th class="text-left" style="min-width:100px;">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left" style="min-width:150px;">{{ trans("messages.subject") }}</th>
                                <th style="min-width:110px; width:110px;">{{ trans("messages.report-date") }}</th>
                                <th style="min-width:110px; width:110px;">{{ trans("messages.closed-date") }}</th>
                                <th class="text-center" style="min-width:80px;">{{ trans("messages.status") }}</th>
                                <th class="actions-col" style="min-width:150px;width:150px">{{ trans("messages.actions") }}</th>
                            </tr>
                        </thead>
                        <tbody class="ajax-view">
                        	@include(config('constants.AJAX_VIEW_FOLDER') . 'incident-report/incident-report-list')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
	
	<div class="modal fade document-folder document-type" id="close_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title custom-twt-modal-header-title" id="exampleModalLabel">{{ trans('messages.close-record') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!!Form::open(['id' => 'close-report-form', 'method' => 'post'])!!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label class="control-label">Note</label>
                            <p>Are you sure you want to close this record ?</p>
                        </div>
						<div class="form-group col-lg-3 col-6">
	                        <label for="close_date" class="control-label">{{ trans("messages.close-date") }}<span class="text-danger">*</span></label>
	                        <input type="text" class="form-control" name="close_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
	                    </div>
                        <div class="col-12">
                            <label class="form-label control-label">{{trans('messages.remarks')}}</label>
                            <textarea name="remarks" cols="30" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.confirm') }}" onclick="updateReport(this);">{{ trans('messages.confirm') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
                <input type="hidden" name="record_id" value="">
                
                {!!Form::close()!!}
            </div>
        </div>
    </div>
    <div class="modal fade document-folder document-type" id="view-close-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-view-close-modal-header-name" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body add-incident-close-status-html">
	                    
	            </div>
	
				<div class="modal-footer justify-content-end">
	            	<button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
			</div>
        </div>
    </div>
    <input type="hidden" name="employee_team_id" value="{{ (isset($teamId) && !empty($teamId) ? Wild_tiger::encode($teamId) : '') }}">
   	<input type="hidden" name="incident_close_count" value="{{ (isset($incidentCloseCount) ? $incidentCloseCount : '') }}">
   	<input type="hidden" name="incident_open_count" value="{{ (isset($incidentOpenCount) ? $incidentOpenCount : '') }}">
</main>
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
<script>

$(document).ready(function() {
    $('#search_from_date,#search_to_date').datetimepicker({
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

    $("[name='search_from_date']").datetimepicker().on('dp.change', function(e) {
		if ($.trim($(this).val()) != "" && $.trim($(this).val()) != "") {
			var incrementDay = moment((e.date)).startOf('d');
			$("[name='search_to_date']").data('DateTimePicker').minDate(incrementDay);
		} else {
			$("[name='search_to_date']").data('DateTimePicker').minDate(false);
		}
		$(this).data("DateTimePicker").hide();
	});

	$("[name='search_to_date']").datetimepicker().on('dp.change', function(e) {
		if ($.trim($(this).val()) != "" && $.trim($(this).val()) != "") {
			var decrementDay = moment((e.date)).endOf('d');
			$("[name='search_from_date']").data('DateTimePicker').maxDate(decrementDay);
		} else {
			$("[name='search_from_date']").data('DateTimePicker').maxDate(false);
		}
		$(this).data("DateTimePicker").hide();
	});
    
});

   // var module_url = '{{ isset($moduleUrl) ? $moduleUrl : '' }}';
  
    function searchField(){
    	var search_by = $.trim($("[name=search_by]").val());
    	var search_from_date = $.trim($("[name=search_from_date]").val());
    	var search_to_date = $.trim($("[name=search_to_date]").val());
    	var search_employment_status = $.trim($("[name=search_employment_status]").val());
    	var search_employee = $.trim($("[name=search_employee]").val());
    	var search_team = $.trim($("[name='search_team']").val());
    	var status = $.trim($("[name=status]").val());
    	
    	var employee_team_id = $.trim($("[name=employee_team_id]").val());
    	var incident_close_count = $.trim($("[name=incident_close_count]").val());
    	var incident_open_count = $.trim($("[name=incident_open_count]").val());

    	var searchData = {
    			'search_by' : search_by, 
    			'search_from_date' : search_from_date,
    			'search_to_date' : search_to_date,
    			'search_employment_status' : search_employment_status,
    			'search_employee' : search_employee,
    			'search_team': search_team,
    			'status' : status,
    			'employee_team_id':employee_team_id,
    			'incident_close_count':incident_close_count,
    			'incident_open_count':incident_open_count,
    	}
    	
    	return searchData;
    }
    
    function filterData(){
    	
    	var searchData = searchField();
    	$.ajax({
    		type : 'post',
    		data : searchData,
    		url : module_url + 'filter',
    		headers: {
    	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    	 	},
    	 	
    		beforeSend : function(){
    			showLoader();
    		},
    		success : function(response){
    			hideLoader();
    			$(".ajax-view").html(response);
    		},
    		error : function(){
    			hideLoader();
    		}
    	});
    }
    
    var module_url = '{{ config('constants.INCIDENT_REPORT_URL') }}' + '/'; 
    var current_row = '';
	function openCloseModel(thisitem){
		current_row = thisitem;
		var record_id = $(thisitem).attr('data-record-id');
		var current_date = $(thisitem).attr('data-current-date');
		var report_date = $(thisitem).attr('data-report-date');
		var report_number = $(thisitem).attr('data-report-number');
		
		$.ajax({
			type : 'post',
			data : { 'record_id' : record_id },	
			dataType : 'json',
			url : module_url + 'view-incident-report',
			beforeSend : function(){
				showLoader();
			},
			success : function(){
				hideLoader();
				$("[name='record_id']").val(record_id);
				$("[name='close_date']").val(current_date);
				$('#close_modal').find('.custom-twt-modal-header-title').text('{{ trans("messages.close-record") }}'  + ' - ' +  report_number );
				$('#close_modal').modal('show');
				$(function() {
			        $('[name="close_date"]').datetimepicker({
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
			        $('[name="close_date"]').data('DateTimePicker').minDate(moment(report_date,'DD-MM-YYYY'));
			        $('[name="close_date"]').data('DateTimePicker').maxDate(moment().endOf('d'));
			    });
			},
			error : function(){
				hideLoader();
			}
		});
	}

	 $("#close-report-form").validate({
	        errorClass: "invalid-input",
	        rules: {
	        	close_date: {
	                required: true
	            }
	        },
	        messages: {
	        	close_date: {
	                required: "{{ trans('messages.require-report-close-date') }}"
	            }
	        }
	    });
	
	
	
	function updateReport(thisitem){

		if( $("#close-report-form").valid() != true ){
			return false;
		}

		var close_date = $.trim($("[name='close_date']").val());
		var remarks = $.trim($("[name='remarks']").val());
		var record_id = $.trim($("[name='record_id']").val());
		var row_index = $(current_row).parents('tr').find('.sr-col').html();

		 var confirm_box = "";
         var confirm_box_msg = "";

        confirm_box = "{{ trans('messages.close-report') }}";
      	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.close-report')]) }}";
		
		alertify.confirm(confirm_box,confirm_box_msg,function() {
			$.ajax({
				type : 'post',
				data : { 'close_date' : close_date , 'remarks' : remarks , 'record_id' : record_id , 'row_index' : row_index },
				dataType : 'json',
				headers: {
			        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			 	},
				url : module_url + 'updateStatus',
				beforeSend : function(){
					showLoader();
				},
				success : function(response){
					hideLoader();
					if(response.status_code == 1){
						if(record_id != '' && record_id != null){
							$(current_row).parents('.incident-report-record').html(response.data.html);
						}
						$('#close_modal').modal('hide');
						alertifyMessage('success',response.message);
					}else{
						alertifyMessage('error',response.message);
					}
				},
				error : function(){
					hideLoader();
				}
			});
		},function() {});
		
		
	}
	function getCloseModelInfo(thisitem){
		var record_id = $.trim($(thisitem).attr('data-incident-id'));
		var header_name = $.trim($(thisitem).attr('data-incident-report-no'));
		
		if(record_id !="" && record_id != null){
			$.ajax({
				type : 'post',
				data : { 'record_id' : record_id },
				headers: {
			        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			 	},
				url : module_url + 'viewIncidentStatus',
				beforeSend : function(){
					showLoader();
				},
				success : function(response){
					hideLoader();
					$('.add-incident-close-status-html').html(response);
					$('#view-close-modal').find('.twt-view-close-modal-header-name').html('{{ trans("messages.view-incident") }} ' + header_name);
					openBootstrapModal('view-close-modal');
				},
				error : function(){
					hideLoader();
				}
			});
		}
	}
	var paginationUrl = module_url + 'filter'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection