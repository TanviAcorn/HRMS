@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<link rel="stylesheet" href="{{ asset ('css/dataTables.bootstrap4.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset ('css/fixedheader-datatables.min.css') }}">
<script type="text/javascript" src="{{ asset ('js/jquery.dataTables.js') }}"></script>
<script type="text/javascript" src="{{ asset ('js/dataTables.bootstrap4.js') }}"></script>
<script type="text/javascript" src="{{ asset ('js/datatables-fixedheader.min.js') }}"></script>


<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ $pageTitle }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) )
        		<?php /* ?>
        		<button type="button" onclick="openImportLeaveBalance(this);" class="btnbtn btn-theme d-none text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center"  title="{{ trans('messages.import-employee-leave-balance') }}"><i class="fas fa-download mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.import-employee-leave-balance") }} </span></button>
        		<?php */ ?>
        	@endif
            @if(checkPermission('add_employee_list') != false)
                <a href="{{ config('constants.EMPLOYEE_MASTER_URL').'/create'}}" class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar d-sm-flex align-items-center" title="{{ trans('messages.add-employee') }}"><i class="fas fa-plus mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.add-employee") }}</span> </a>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <?php
        $tableSearchPlaceholder = "Search By Employee Code, Name, Full Name, Contact Number, Outlook Email ID";
        ?>
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-12">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans("messages.search-by") }}</label><i class="fa fa-info-circle ml-2" data-toggle="tooltip" data-placement="right" title="<?php echo $tableSearchPlaceholder ?>"></i>
                            <input type="text" name="search_by" class="form-control" placeholder="<?php echo $tableSearchPlaceholder ?>">
                        </div>
                    </div>
                    @if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) )
					<div class="col-xl-2 col-lg-4 col-12">
					<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' ) );?>
					</div>
					@endif

                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_gender" class="control-label">{{ trans('messages.gender') }}</label>
                            <select class="form-control" name="search_gender" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <?php 
                                if(!empty($genderRecordDetails)){
                                	foreach ($genderRecordDetails as $key => $genderRecordDetail){
                                		?>
                                		<option value="{{ $key }}">{{ (isset($genderRecordDetail) ? $genderRecordDetail :'')}}</option>
                                		<?php 
                                	}
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_blood_group" class="control-label">{{ trans('messages.blood-group') }}</label>
                            <select class="form-control" name="search_blood_group" onchange="filterData()">
                                <option value=''>{{ trans("messages.select") }}</option>
                               <?php 
                                if(!empty($bloodGroupRecordDetails)){
                                	foreach ($bloodGroupRecordDetails as $key => $bloodGroupRecordDetail){
                                		?>
                                		<option value="{{ $key }}">{{ (isset($bloodGroupRecordDetail) ? $bloodGroupRecordDetail :'')}}</option>
                                		<?php 
                                	}
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-2 col-md-3 col-sm-6">
                        <label for="search_from_date" class="control-label">{{ trans("messages.joining-from-date") }}</label>
                        <input type="text" class="form-control" name="search_from_date" placeholder="DD-MM-YYYY" autocomplete="off" />
                    </div>
                    <div class="form-group col-lg-2 col-md-3 col-sm-6">
                        <label for="search_to_date" class="control-label">{{ trans("messages.joining-to-date") }}</label>
                        <input type="text" class="form-control" name="search_to_date" placeholder="DD-MM-YYYY" autocomplete="off" />
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_designation" class="control-label">{{ trans('messages.designation') }}</label>
                            <select class="form-control select2" name="search_designation" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <?php 
                                if(!empty($designationRecordDetails)){
                                	foreach ($designationRecordDetails as $designationRecordDetail){
                                		$encodeId = Wild_tiger::encode($designationRecordDetail->i_id);
                                		?>
                                		<option value="{{ $encodeId }}">{{ (!empty($designationRecordDetail->v_value) ? $designationRecordDetail->v_value :'')}}</option>
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
					@if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) ) )
                    <div class="col-xl-3 col-md-4 col-12 col-sm-6">
                        <div class="form-group">
                            <label for="search_leader_name_reporting_manager" class="control-label">{{ trans('messages.leader-name-reporting-manager') }}</label>
                            <select class="form-control select2" name="search_leader_name_reporting_manager" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($leaderDetails))
                                	@foreach($leaderDetails as $leaderDetail)
                                		<option value="{{ (!empty($leaderDetail->i_id) ? Wild_tiger::encode($leaderDetail->i_id) : 0 )}}">{{ (!empty($leaderDetail->v_employee_full_name) ? $leaderDetail->v_employee_full_name .(!empty($leaderDetail->v_employee_code) ? ' ('.$leaderDetail->v_employee_code .')'  :''):'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="col-xl-3 col-md-3 col-12 col-sm-6">
                        <div class="form-group">
                            <label for="search_recruitment_source" class="control-label">{{ trans('messages.recruitment-source') }}</label>
                            <select class="form-control" name="search_recruitment_source" onchange="showReferenceNameInfo(this),filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <?php 
                                if(!empty($recruitmentSourceDetails)){
                                	foreach ($recruitmentSourceDetails as $recruitmentSourceDetail){
                                		$encodeId = Wild_tiger::encode($recruitmentSourceDetail->i_id);
                                		?>
                                		<option value="{{ $encodeId }}" data-recruitment-id="{{ $recruitmentSourceDetail->i_id }}" >{{ (!empty($recruitmentSourceDetail->v_value) ? $recruitmentSourceDetail->v_value :'')}}</option>
                                		<?php 
                                		
                                	}
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6 reference-name-record" style="display:none">
                        <div class="form-group">
                            <label for="search_reference_name" class="control-label">{{ trans('messages.reference-name') }}</label>
                            <select class="form-control select2" name="search_reference_name" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <?php 
                                if(!empty($referenceEmployeeRecords)){
                                	foreach ($referenceEmployeeRecords as $referenceEmployeeRecord){
                                		$encodeId = Wild_tiger::encode($referenceEmployeeRecord->i_id);
                                		?>
                                		<option value="{{ $encodeId }}" >{{ (!empty($referenceEmployeeRecord->v_employee_full_name) ? $referenceEmployeeRecord->v_employee_full_name .(!empty($referenceEmployeeRecord->v_employee_code) ? ' ('.$referenceEmployeeRecord->v_employee_code .')'  :''):'') }}</option>
                                		<?php 
                                		
                                	}
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_shift" class="control-label">{{ trans('messages.shift') }}</label>
                            <select class="form-control" name="search_shift" onchange="filterData()">
                                <option value=''>{{ trans("messages.select") }}</option>
                                @if(!empty($shifyDetails))
                                	@foreach($shifyDetails as $shifyDetail)
                                		<option value="{{ (!empty($shifyDetail->i_id) ? Wild_tiger::encode($shifyDetail->i_id) : 0 )}}">{{ (!empty($shifyDetail->v_shift_name) ? $shifyDetail->v_shift_name  .' ('.$shifyDetail->e_shift_type.')' :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_weekly_off" class="control-label">{{ trans('messages.weekly-off') }}</label>
                            <select class="form-control" name="search_weekly_off" onchange="filterData()">
                                <option value=''>{{ trans("messages.select") }}</option>
                                @if(!empty($weekOffDetails))
                                	@foreach($weekOffDetails as $weekOffDetail)
                                		<option value="{{ (!empty($weekOffDetail->i_id) ? Wild_tiger::encode($weekOffDetail->i_id) : 0 )}}">{{ (!empty($weekOffDetail->v_weekly_off_name) ? $weekOffDetail->v_weekly_off_name :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_login_status">{{ trans("messages.login-status") }}</label>
                            <select class="form-control" name="search_login_status" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{config('constants.ENABLE_STATUS')}}">{{ trans("messages.enable") }}</option>
                                <option value="{{config('constants.DISABLE_STATUS')}}">{{ trans("messages.disable") }}</option>
                            </select>
                        </div>
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
            {{ Wild_tiger::readMessage() }}
                <div class="table-responsive">
                    <table class="table table-sm table-bordered text-left" id="user-table">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.employee-code") }}</th>
                                <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.name") }}</th>
                                <th class="text-left" style="width:175px;min-width:175px;">{{ trans("messages.full-name") }}</th>
                                <th class="text-left" style="min-width:93px;">{{ trans("messages.gender") }} <br> {{ trans("messages.blood-group") }}</th>
                                <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.joining-date") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.designation") }} <br> {{ trans("messages.team") }}</th>
                                <th class="text-left" style="width:140px;min-width:140px;">{{ trans("Sub-designation") }}</th>
                                <th class="text-left" style="width:214px;min-width:214px;">{{ trans("messages.leader-name") }} / {{ trans("messages.reporting-manager") }}</th>
                                <th class="text-left" style="width:170px;min-width:170px;">{{ trans("messages.recruitment-source") }} <br> {{ trans("messages.reference-name") }}</th>
                                <th class="text-left" style="min-width:138px;">{{ trans("messages.shift") }}<br> {{ trans("messages.weekly-off") }}</th>
                                <th class="text-left" style="width:215px;min-width:215px;">{{ trans("messages.contact-number") }} <br> {{ trans("messages.outlook-email-id") }}</th>
                                <th class="text-left" style="width:140px;min-width:140px;">{{ trans("messages.employment-status") }}</th>
                                <th class="text-center px-0" style="min-width:70px;">{{ trans("messages.login-status") }}</th>
                                @if(checkPermission('edit_employee_list') != false)
                                	<th class="actions-col" style="min-width:65px">{{ trans("messages.actions") }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                        	
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</main>

<div class="modal fade" id="import-employee-leave-excel-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
    	{!!Form::open(['id' => 'import-employee-leave-excel-form' , 'method' => 'post' , 'files' => 'true' , 'url' => 'employee-master/importLeaveBalance'])!!}
        	<div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('messages.import-employee-leave-balance') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row related-field">
                        <div class="col-lg-12">
                            <label for="upload_excel" class="font-weight-bold">{{ trans('messages.upload-excel') }}<span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="upload_excel" name="upload_excel" onchange="validFile(this,'excel')" >
                                <label class="custom-file-label" for="upload_excel">{{ trans('messages.choose-file') }}</label>
                            
                            </div>
                            <label id="upload_excel-error" class="invalid-input" for="upload_excel" style="display:none;"></label>
                        </div>
                        <?php /* ?>
                        <div class="col-lg-12 mb-3">
                            <div class="d-flex align-items-center">
                                <a href="<?php //echo SAMPLE_ASSET_TRACKER_EXCEL ?>" download class="text-theme btn shadow-none p-0 text-decoration-underline" title="{{ trans('messages.download-sample-link') }}">
                                <span class="text-theme ml-1">{{ trans('messages.download-sample-link') }}</span></a>
                            </div>
                        </div>
                        <?php */ ?>
                    </div>
                </div>
                
                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn bg-theme text-white" title="{{ trans('messages.upload') }}">{{ trans('messages.upload') }}</button>
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            </div>
        {!!Form::close()!!}
    </div>
</div>


<script>
$(function() {
    $(' [name="search_from_date"], [name="search_to_date"]').datetimepicker({
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

	$("#import-employee-leave-excel-form").validate({
	    errorClass: "invalid-input",
	    rules: {
	        upload_excel: {
	            required: true,
	            noSpace: true,
	            extension : 'xlsx|xls'
	        },
	    },
	    messages: {
	        upload_excel: {
	        	required: "{{ trans('messages.required-upload-file') }}",
	        	extension: "{{ trans('messages.error-only-specific-are-allowed', [ 'fileType' => ' Excel ' ]) }}"
	        },
	        
	    },
	    submitHandler: function(form) {
	    	var employee_leave_confirm_box = "{{ trans('messages.import-employee-leave-balance') }}";
	    	var employee_leave_confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.import-employee-leave-balance')]) }}";
	    	alertify.confirm( employee_leave_confirm_box,  employee_leave_confirm_box_msg , function() {
	       		showLoader()
	        	form.submit();
	    	},function() {} );
	    }
	});


 	var employee_master_url = '{{config("constants.EMPLOYEE_MASTER_URL")}}' + '/';
 	function searchField(){
	 	var search_by = $.trim($('[name="search_by"]').val());
	 	var search_gender = $.trim($('[name="search_gender"]').val());
	 	var search_blood_group = $.trim($('[name="search_blood_group"]').val());
	 	var search_from_date = $.trim($('[name="search_from_date"]').val());
	 	var search_to_date = $.trim($('[name="search_to_date"]').val());
	 	var search_designation = $.trim($('[name="search_designation"]').val());
	 	var search_team = $.trim($('[name="search_team"]').val());
	 	var search_leader_name_reporting_manager = $.trim($('[name="search_leader_name_reporting_manager"]').val());
	 	var search_recruitment_source = $.trim($('[name="search_recruitment_source"]').val());
	 	var search_reference_name = $.trim($('[name="search_reference_name"]').val());
	 	var search_shift = $.trim($('[name="search_shift"]').val());
	 	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
	 	var search_login_status = $.trim($('[name="search_login_status"]').val());
	 	var search_weekly_off = $.trim($('[name="search_weekly_off"]').val());
	 	
	 	var searchData = {
	     	'search_by':search_by,
	         'search_gender': search_gender,
	         'search_blood_group': search_blood_group,
	         'search_from_date': search_from_date,
	         'search_to_date': search_to_date,
	         'search_designation': search_designation,
	         'search_team': search_team,
	         'search_leader_name_reporting_manager': search_leader_name_reporting_manager,
	         'search_recruitment_source': search_recruitment_source,
	         'search_reference_name': search_reference_name,
	         'search_shift': search_shift,
	         'search_employment_status': search_employment_status,
	         'search_login_status': search_login_status,
	         'search_weekly_off':search_weekly_off,
	 	}
	     return searchData;
	 }
	 function filterData(){
	 	if ($.fn.DataTable.isDataTable('#user-table')) {
	            $('#user-table').DataTable().destroy();
	        }

	        reintDataTable('user-table');
	 }
	 $(document).ready(function() {
	        reintDataTable('user-table');
	   })
	 var paginationUrl = employee_master_url + 'filter'
	 
	  function reintDataTable(className = null) {

	        var paginationUrl = employee_master_url + "filter";

	        var searchData = searchField();

	        var tableColumns = [];
	    	tableColumns.push({ data: 'sr_no', orderable : false  });
	    	tableColumns.push({ data: 'employee_code'});
	    	tableColumns.push({ data: 'name'});
	    	tableColumns.push({ data: 'full_name'});
	    	tableColumns.push({ data: 'gender'});
	    	tableColumns.push({ data: 'joining_date'});
	    	tableColumns.push({ data: 'designation'});
    		tableColumns.push({ data: 'sub_designation'});
	    	tableColumns.push({ data: 'leader_name'});
	    	tableColumns.push({ data: 'recruitment_source'});
	    	tableColumns.push({ data: 'shift'});
	    	tableColumns.push({ data: 'contact_number'});
	    	tableColumns.push({ data: 'employment_status'});
	    	tableColumns.push({ data: 'login_status', orderable : false  });

	    	<?php if(checkPermission('edit_employee_list') != false) { ?>
	    	tableColumns.push({ data: 'action', orderable : false  });
			<?php } ?>	
	    	
	    	
	        
	        $('#' + className).DataTable({
	            "bProcessing": true,
	            "searching": false,
	            "bServerSide": true,
                "fixedHeader":{
                    "header": true,
                    "headerOffset": 40
                },
                "scrollX": true,
                "scrollY": 'calc(100vh - 300px)',
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    $(".dataTables_scrollBody").addClass('no-record');
                    if (aiDisplay.length > 6) {
                        $(".dataTables_scrollBody").removeClass('no-record');
                    }
                    else {
                        $(".dataTables_scrollBody").addClass('no-record');
                    }
                },
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
	                dataFilter: function(response) {
		            	hideLoader();
		            	if( response != "" && response  != null ){
		            		var response_json_data = JSON.parse(response);
		            		var total_display_record = ( ( response_json_data.iTotalDisplayRecords != "" && response_json_data.iTotalDisplayRecords != null ) ? response_json_data.iTotalDisplayRecords : 0 );
		            		$(".total-record-count").html(total_display_record);
			            } else {
			            	$(".total-record-count").html(0);	
				        }
		            	return response;
		            },
	                error: function() { // error handling code

	                }
	            },
	            'columns': tableColumns,
	        });
	    }

	function showReferenceNameInfo(thisitem){
		var search_recruitment_source = $.trim($("[name='search_recruitment_source']").find('option:selected').attr('data-recruitment-id'));
		
		if(search_recruitment_source != "" && search_recruitment_source != null){
			if(search_recruitment_source == "{{config('constants.EMPLOYEE_RECRUITMENT_SOURCE_ID')}}"){
				$('.reference-name-record').show();
			} else {
				$('.reference-name-record').hide();
			}
		} else{
			$('.reference-name-record').hide();
		}
	}

	function openImportLeaveBalance(thisitem){
		openBootstrapModal('import-employee-leave-excel-modal');
	} 
</script>


@endsection