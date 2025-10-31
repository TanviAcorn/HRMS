@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.time-off-report") }}</h1>
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
                @if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_TIME_OFF_REPORT'), session()->get('user_permission')  ) ) ) ) )
	                
						<div class="col-xl-2 col-lg-4 col-12">
						<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' )  );?>
						</div>
						<div class="col-xl-3 col-lg-4 col-12">
						<?php echo statusWiseEmployeeList( 'search_employee_name' ,  (isset($employeeDetails) ? $employeeDetails : [] ));?>
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
					
                    <div class="form-group col-lg-3 col-md-3 col-sm-6">
                        <label for="search_time_off_from_date" class="control-label">{{ trans("messages.time-off-from-date") }}</label>
                        <input type="text" class="form-control" name="search_time_off_from_date" value="{{ ( isset($startDate) ?  clientDate($startDate) : '' ) }}" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    <div class="form-group col-lg-3 col-md-3 col-sm-6">
                        <label for="search_time_off_to_date" class="control-label">{{ trans("messages.time-off-to-date") }}</label>
                        <input type="text" class="form-control date" name="search_time_off_to_date" value="{{ ( isset($endDate) ?  clientDate($endDate) : '' ) }}" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    <div class="form-group col-lg-3 col-md-3 col-sm-6">
                        <label for="search_time_off_back_from_date" class="control-label">{{ trans("messages.time-back-from-date") }}</label>
                        <input type="text" class="form-control" name="search_time_off_back_from_date" value="" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    <div class="form-group col-lg-3 col-md-3 col-sm-6">
                        <label for="search_time_off_back_to_date" class="control-label">{{ trans("messages.time-back-to-date") }}</label>
                        <input type="text" class="form-control date" name="search_time_off_back_to_date" value="" placeholder="{{ trans('messages.dd-mm-yyyy') }}" />
                    </div>
                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_leave_type">{{ trans("messages.type") }}</label>
                            <select class="form-control" name="search_leave_type" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                               	<?php 
                                if(!empty($typeInfo)){
                                	foreach ($typeInfo as $key => $type){
                                		$selected = "";
                                		if(  isset($selectedTimeOffStatus) && ( $selectedTimeOffStatus == $key ) ){
                                			$selected = "selected='selected'";
                                		}
                                		?>
                                		<option value="{{ $key }}" {{ $selected }} >{{(!empty($type) ? $type :'')}}</option>
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
                                		?>
                                		<option value="{{ $key }}">{{(!empty($staus) ? $staus :'')}}</option>
                                		<?php 
                                	}
                                }
                                ?>
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
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="width:185px;min-width:185px;">{{ trans("messages.employee-name-code") }} <br> {{ trans("messages.contact-number") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.team") }}</th>
                                <th class="text-left" style="width:180px;min-width:180px;">{{ trans("messages.date") }} <br> {{ trans("messages.from-time-to-time") }} <br> {{ trans("messages.no-of-hours") }}</th>
                                <th class="text-left" style="width:180px;min-width:180px;">{{ trans("messages.time-back-date") }} <br> {{ trans("messages.from-time-to-time") }} <br> {{ trans("messages.no-of-hours") }}</th>
                                <th class="text-left" style="width:119px;min-width:119px;">{{ trans("messages.type") }}</th>
                                <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.requested-on") }}</th>
                                <th class="text-left" style="width:110px;min-width:110px;">{{ trans("messages.status") }} <br> {{ trans("messages.action-taken-by") }}</th>
                                <th class="text-left" style="width:108px;min-width:108px;">{{ trans("messages.requested-by") }}</th>
                                <th class="text-left" style="width:113px;min-width:113px;">{{ trans("messages.action-taken-on") }}</th>
                                <th class="text-center" style="width:200px;min-width:200px;">{{ trans("messages.actions") }}</th>
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                            @include( config('constants.AJAX_VIEW_FOLDER') . 'time-off-report/time-off-report-list')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php /*?>
    <div class="modal fade document-folder document-type" id="hold-amount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hold Amount - Deep Suthar (D29042003)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-bordered table-responsive text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.month-year") }}</th>
                                <th class="text-left" style="width:200px;min-width:188px;">{{ trans("messages.hold-amount") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-left">
                                <td class="text-center">1</td>
                                <td>Dec - 2022</td>
                                <td>10,000</td>
                            </tr>
                            <tr class="text-left">
                                <td class="text-center">2</td>
                                <td>Nov - 2022</td>
                                <td>10,000</td>
                            </tr>
                            <tr class="text-left">
                                <td class="text-center">3</td>
                                <td>Oct - 2022</td>
                                <td>10,000</td>
                            </tr>
                            <tr class="text-left">
                                <td class="text-center">4</td>
                                <td>Sep - 2022</td>
                                <td>10,000</td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
<?php */ ?>
<div class="modal fade document-folder document-type" id="time-off-approve-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-time-off-modal-header-name" id="exampleModalLabel">{{ trans("messages.approve-reject-leave")}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-time-off-approve-form' , 'method' => 'post' ,  'url' => 'addTimeOffReport')) !!}
                <div class="modal-body add-time-off-html">
                    
                </div>

                <div class="modal-footer justify-content-end">
                	<input type="hidden" name="status" value="">
	                <input type="hidden" name="record_id" value="">
                    <button type="button" style='display: none' onclick="addTimeOffReport()" class="btn bg-theme text-white action-button time-off-modal-action-button btn-add" name="submit" title="{{ trans('messages.confirm') }}">{{ trans('messages.confirm') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
			{!! Form::close() !!}
            </div>
        </div>
    </div>
</main>


<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
<script>
    $(function() {
        $('[name="search_time_off_from_date"], [name="search_time_off_to_date"]').datetimepicker({
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
        
        $("[name='search_time_off_from_date']").datetimepicker().on('dp.change', function(e) {
    		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    			var incrementDay = moment((e.date)).startOf('d');
    		 	$("[name='search_time_off_to_date']").data('DateTimePicker').minDate(incrementDay);
    		} else {
    			$("[name='search_time_off_to_date']").data('DateTimePicker').minDate(false);
    		} 
    		
    	    $(this).data("DateTimePicker").hide();
    	});

        $("[name='search_time_off_to_date']").datetimepicker().on('dp.change', function(e) {
        	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    	        var decrementDay = moment((e.date)).endOf('d');
    	        $("[name='search_time_off_from_date']").data('DateTimePicker').maxDate(decrementDay);
        	} else {
        		 $("[name='search_time_off_from_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });


        $('[name="search_time_off_back_from_date"], [name="search_time_off_back_to_date"]').datetimepicker({
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
        
        $("[name='search_time_off_back_from_date']").datetimepicker().on('dp.change', function(e) {
    		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    			var incrementDay = moment((e.date)).startOf('d');
    		 	$("[name='search_time_off_back_to_date']").data('DateTimePicker').minDate(incrementDay);
    		} else {
    			$("[name='search_time_off_back_to_date']").data('DateTimePicker').minDate(false);
    		} 
    		
    	    $(this).data("DateTimePicker").hide();
    	});

        $("[name='search_time_off_back_to_date']").datetimepicker().on('dp.change', function(e) {
        	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    	        var decrementDay = moment((e.date)).endOf('d');
    	        $("[name='search_time_off_back_from_date']").data('DateTimePicker').maxDate(decrementDay);
        	} else {
        		 $("[name='search_time_off_back_from_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });
        

        
    });

    var time_off_report_url = '{{config("constants.TIME_OFF_REPORT_URL")}}' + '/';
    
    function searchField(){
    	var search_employee_name = $.trim($('[name="search_employee_name"]').val());
    	var search_team = $.trim($("[name='search_team']").val());
    	var search_time_off_from_date = $.trim($('[name="search_time_off_from_date"]').val());
    	var search_time_off_to_date  = $.trim($('[name="search_time_off_to_date"]').val());
    	var search_leave_type = $.trim($('[name="search_leave_type"]').val());
    	var search_leave_status = $.trim($('[name="search_leave_status"]').val());
    	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
    	var search_time_off_back_from_date = $.trim($('[name="search_time_off_back_from_date"]').val());
    	var search_time_off_back_to_date  = $.trim($('[name="search_time_off_back_to_date"]').val());
    	
    	var searchData = {
                'search_employee_name':search_employee_name,
                'search_team': search_team,
                'search_time_off_from_date': search_time_off_from_date,
                'search_time_off_to_date':search_time_off_to_date,
                'search_leave_type':search_leave_type,
                'search_leave_status':search_leave_status,
                'search_employment_status':search_employment_status,
                'search_time_off_back_from_date':search_time_off_back_from_date,
                'search_time_off_back_to_date':search_time_off_back_to_date,
            }
            return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(time_off_report_url + 'filter' , searchFieldName);
    }
    var paginationUrl = time_off_report_url + 'filter'
    
    function exportData(){
   		var searchData = searchField();
   		var export_info = {};
   		export_info.url = time_off_report_url + 'filter';
   		export_info.searchData = searchData;
   		dataExportIntoExcel(export_info);
   	}
    var current_row ='';
   	function openTimeOffReportModel(thisitem){
   		current_row = thisitem;
   		
   		var employee_id = $.trim($(thisitem).attr('data-employee-id'));
		var time_off_id = $.trim($(thisitem).attr('data-time-off-id'));
		var status = $.trim($(thisitem).attr('data-status'));
		
		if(status != "" && status != null){
			$("[name='status']").val(status);
		}
		$("[name='record_id']").val(time_off_id);
		$.ajax({
    		type: "POST",
    		url: time_off_report_url + 'timeOffApprove',
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'employee_id':employee_id,
    			'time_off_id':time_off_id,
    			'status' : status
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			
    			var header_name = $.trim($(thisitem).attr('title'));
    			//if(status  !="" && status != null ){
        		if(status != "{{ config('constants.VIEW_RECORD')}}"){
        			$('.time-off-modal-action-button').show();
        			//}
        		} else {
        			$('.time-off-modal-action-button').hide();
            	}
    			$('.add-time-off-html').html("");
    			$('.add-time-off-html').html(response);
    			$("#time-off-approve-modal").find('.twt-time-off-modal-header-name').html(header_name);
    			openBootstrapModal('time-off-approve-modal');
    		},
    		error: function() {
    			hideLoader();
    		}
    	});	
   	}
   	
   	$("#add-time-off-approve-form").validate({
        errorClass: "invalid-input",
        rules: {
        	approve_reject_time_off_reason: {
                required: true,noSpace:true
            },
        },
        messages: {
        	approve_reject_time_off_reason: {
                required: "{{ trans('messages.require-reason') }}"
            },

        },
        submitHandler: function(form) {
            showLoader()
            form.submit();
        }
    });
    
   	function addTimeOffReport(){
   		if($('#add-time-off-approve-form').valid() != true){
			return false;
		}
		var status = $.trim($('[name="status"]').val());
		var record_id = $.trim($('[name="record_id"]').val());

		var confirm_box = "";
		var confirm_box_msg = "";
		
		if(status !="" && status != null){
			switch(status){
				case "{{config('constants.APPROVED_STATUS')}}" :
					confirm_box = "{{ trans('messages.approve-time-off')}}";
					confirm_box_msg = "{{ trans('messages.update-status-msg',['module'=> trans('messages.approve')]) }}";
					break;
				case "{{config('constants.REJECTED_STATUS')}}" :
					confirm_box = "{{ trans('messages.reject-time-off')}}";
					confirm_box_msg = "{{ trans('messages.update-status-msg',['module'=> trans('messages.reject')]) }}";
					break;
				case "{{config('constants.CANCELLED_STATUS')}}" :
					confirm_box = "{{ trans('messages.cancel-time-off')}}";
					confirm_box_msg = "{{ trans('messages.update-status-msg',['module'=> trans('messages.cancel')]) }}";
					break;
			}
		}
	    
	    alertify.confirm(confirm_box,confirm_box_msg,function() {   
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "{{config('constants.TIME_OFF_MASTER_URL')}}" + '/updateTimeOffStatus',
				data: {
					"_token": "{{ csrf_token() }}",
					'status':status,
					'record_id':record_id,
					'approve_reject_time_off_reason':$.trim($("[name='approve_reject_time_off_reason']").val()),
					'row_index':$(current_row).parents('tr').find('.sr-col').html() 
				},
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						$("#time-off-approve-modal").modal('hide');
						alertifyMessage('success',response.message);
						if(record_id != '' && record_id != null){
							$(current_row).parents('.time-off-report-list').html(response.data.html);
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