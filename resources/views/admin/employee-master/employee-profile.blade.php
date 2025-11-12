@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
@include('admin/profile-picture-modal')	
@include('admin/suspend-model')	
@include('admin/suspend-history-model')
@include('admin/village-modal')
@include('admin/city-model')	
<main class="page-height bg-light-color">
    <script>
    var employee_url = "{{ config('constants.EMPLOYEE_MASTER_URL') }}" + "/"	;
    </script>
    
    <div class="breadcrumb-wrapper d-flex">
        <div class="container-fluid">
            <h1 class="mb-0 header-title" id="pageTitle">{{ $pageTitle }}</h1>
        </div>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section profile-section pt-4">
        <div class="container-fluid">
            <div class="employee-profile-pic-view--master-div-html">
            @include(config('constants.AJAX_VIEW_FOLDER') .'employee-master/main-profile-info')
            </div>
            @if( isset($employeeRecordInfo->e_employment_status) && ( $employeeRecordInfo->e_employment_status != config('constants.RELIEVED_EMPLOYMENT_STATUS') ) )  
            <div class="row mt-3 employee-resign-info" <?php echo ( isset($employeeRecordInfo->latestResignHistory) && (isset($employeeRecordInfo->latestResignHistory->e_status)) && ( in_array( $employeeRecordInfo->latestResignHistory->e_status , [ config('constants.PENDING_STATUS') , config('constants.APPROVED_STATUS') ]  ) ) ) ? '' : 'style=display:none' ?> >
                <div class="col-12 employee-resign-info-section">
                    @include(config('constants.ADMIN_FOLDER') .'employee-master/employee-notice-period-alert')
                </div>
            </div>
            @endif
            
            
            <div class="employee-tab pt-4">
                <div class="tab-content" id="pills-tabContent">
                    
                    <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <div class="row">
                            <div class="col-lg-6 profile-detail-card primary-details-row">
                             	@include( config('constants.AJAX_VIEW_FOLDER') . 'employee-master/primary-details-info')
                            </div>
                            <div class="col-lg-6 profile-detail-card">
                                @include( config('constants.AJAX_VIEW_FOLDER') . 'employee-master/contact-details-info')
                            </div>
                            <div class="col-lg-6 profile-detail-card">
                                @include( config('constants.AJAX_VIEW_FOLDER') . 'employee-master/address-info')
                            </div>
                            <div class="col-lg-6 profile-detail-card">
                                @include( config('constants.AJAX_VIEW_FOLDER') . 'employee-master/relation-info')
                            </div>
                            <div class="col-lg-6 profile-detail-card">
                                @include( config('constants.AJAX_VIEW_FOLDER') . 'employee-master/job-info')
                            </div>
                            <div class="col-lg-6 profile-detail-card">
                                @include( config('constants.AJAX_VIEW_FOLDER') . 'employee-master/bank-details-info')
                            </div>
                            <div class="col-lg-6 profile-detail-card">
                                @include( config('constants.AJAX_VIEW_FOLDER') . 'employee-master/identity-info')
                            </div>
                        </div>
                    </div>
                    @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) ) || (  (isset($employeeRecordInfo->i_login_id) && ( $employeeRecordInfo->i_login_id == session()->get('user_id') ) )  ) )
					<div class="tab-pane fade show" id="pills-documents" role="tabpanel" aria-labelledby="pills-documents-tab">
						<div class="row">
							<div class="col-12 profile-detail-card emp-document-list">
								@include(config('constants.AJAX_VIEW_FOLDER') .'employee-master/documents-details-list')
							</div>
						</div>
					</div>
                    <div class="tab-pane fade show" id="pills-pay-slips" role="tabpanel" aria-labelledby="pills-pay-slips-tab">
                        <div class="row">
						
                            <div class="col-12 profile-detail-card employee-pay-slip-info">
                               
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade show" id="pills-salary" role="tabpanel" aria-labelledby="pills-salary-tab">
                        <div class="row">
                            <div class="col-12 profile-detail-card employee-salary-info">
                                
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="tab-pane fade" id="pills-leave" role="tabpanel" aria-labelledby="pills-leave-tab">
                        <div class="row">
                            <div class="col-12 profile-detail-card employee-leave-info">
                                
                                
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-attendance" role="tabpanel" aria-labelledby="pills-attendance-tab">
                        <div class="row">
                            <div class="col-12 profile-detail-card employee-attendance-info">
                               
                            </div>
                        </div>
                    </div>
                    @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) ) || (  (isset($employeeRecordInfo->i_login_id) && ( $employeeRecordInfo->i_login_id == session()->get('user_id') ) )  ) )
                    <div class="tab-pane fade" id="pills-assets" role="tabpanel" aria-labelledby="pills-assets-tab">
                        <div class="row">
                            <div class="col-12 profile-detail-card">
                                @include(config('constants.AJAX_VIEW_FOLDER') .'employee-master/assets-info')
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </section>
    <input type="hidden" name="emp_last_salary_generate_date" value="{{ ( isset($allowedLastEffDate) ? $allowedLastEffDate : '' ) }}">
    @include(config('constants.ADMIN_FOLDER') .'employee-master/edit-employee-designation')
    @include(config('constants.ADMIN_FOLDER') .'employee-master/edit-employee-sub-designation')
    @include(config('constants.ADMIN_FOLDER') .'employee-master/edit-employee-team')
    @include(config('constants.ADMIN_FOLDER') .'employee-master/edit-employee-shift')
    @include(config('constants.ADMIN_FOLDER') .'employee-master/edit-employee-weekly-off')
    @include(config('constants.ADMIN_FOLDER') .'employee-master/edit-employee-probation')
    
	<div class="modal fade document-folder" id="employee-designation-history-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	    <div class="modal-dialog modal-lg modal-dialog-centered">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title employee-designation-history-modal-title" id="exampleModalLabel">{{ trans("messages.designation-history") }}</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
	                </button>
	            </div>
	            <div class="modal-body overflow-hidden">
	                <div class="row px-3 py-4">
	                    <div class="col-12 table-responsive">
	                        <table class="table table-sm table-hover table-bordered">
	                            <thead>
	                                <tr>
	                                    <th class="text-center sr-col">{{ trans('messages.sr-no') }}</th>
	                                    <th style="min-width: 180px;" class="employee-designation-history-title">{{ trans('messages.designation') }}</th>
	                                    <th style="min-width: 120px;">{{ trans('messages.from-date') }}</th>
	                                    <th style="min-width: 120px;">{{ trans('messages.to-date') }}</th>
	                                    @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )  
	                                    <th style="min-width: 60px;" class="text-center">{{ trans('messages.actions') }}</th>
	                                    @endif
	                                </tr>
	                            </thead>
	                            <tbody class="employee-designation-history-html">
	                                
	                            </tbody>
	                        </table>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
	
     <?php /*
     <div class="modal fade document-folder document-type" id="resign-approve-reject-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.resign-approval") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'approve-reject-resign-form' , 'method' => 'post' )) !!}
                    <div class="modal-body">
                        <div class="row resign-approve-reject-html">
                        	
                        </div>
                    </div>
                    <input type="hidden" name="resign_approve_reject_employee_id" value="">
                    <div class="modal-footer justify-content-end">
                        <button type="button" onclick="updateResignStatus(this);" data-action="{{ config('constants.APPROVED_STATUS') }}"  class="btn bg-theme text-white action-button lookup-modal-action-button btn-add approve-button" title="{{ trans('messages.approve') }}">{{ trans('messages.approve') }}</button>
                        <button type="button" onclick="updateResignStatus(this);" data-action="{{ config('constants.REJECTED_STATUS') }}"  class="btn bg-theme text-white action-button lookup-modal-action-button btn-add reject-button" title="{{ trans('messages.reject') }}">{{ trans('messages.reject') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
     */?>


    <!-- Initiate Exit pop up -->

    @include(config('constants.ADMIN_FOLDER') .'employee-master/initiate-exit-form')
    
    @include(config('constants.ADMIN_FOLDER') .'employee-master/emp-resign-form')

 	@include(config('constants.ADMIN_FOLDER') .'employee-master/emp-upload-document')
    
    @include(config('constants.ADMIN_FOLDER') .'employee-master/emp-view-document')
    
    <!-- Organization Chart Modal -->
    <div class="modal fade document-folder" id="org-chart-modal" tabindex="-1" aria-labelledby="orgChartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orgChartModalLabel">{{ trans("messages.organization-chart") }}</h5>
                    <div class="ml-auto mr-3 d-flex align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary mr-2" onclick="zoomInOrgChart()" title="Zoom In">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary mr-2" onclick="zoomOutOrgChart()" title="Zoom Out">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary mr-2" onclick="resetZoomOrgChart()" title="Reset Zoom">
                            <i class="fas fa-redo"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-success" onclick="downloadOrgChartJPG()" title="Download as JPG">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;" id="org-chart-modal-body">
                    <div class="employee-org-chart-modal-content" id="org-chart-content" style="transform-origin: top center; transition: transform 0.2s ease;">
                        <div class="text-center py-5">
                            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                            <p class="mt-2 text-muted">Loading organization chart...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
   
   <script>
    	var common_emp_modal_header_title = " - " + "{{ ( isset($employeeRecordInfo->v_employee_full_name)  ?  $employeeRecordInfo->v_employee_full_name . ( isset($employeeRecordInfo->v_employee_code)  ? ' - ' . $employeeRecordInfo->v_employee_code : '' ) : '' ) }}";

		$(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
    
	<script>

    	$(document).ready(function(){
			<?php if( in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) && (  (isset($employeeRecordInfo->i_login_id) && ( $employeeRecordInfo->i_login_id != session()->get('user_id') ) )  ) ) { ?>
				//console.log("suprevisor");
				//$(".nav-pills").find("a:first").click();
			<?php } ?> 
        });
        

		function getEmployeeLeaveList(thisitem){
			var record_id = $.trim($(thisitem).attr("data-record-id"));
			var data_fetch_status = $.trim($(thisitem).attr("data-fetch")); 

			if( data_fetch_status != "" && data_fetch_status != null && data_fetch_status == "{{ config('constants.SELECTION_NO') }}" ){
				$.ajax({
	    	 		type: "POST",
	    	 		url: employee_module_url + 'getEmployeeLeaveList',
	    	 		data: {
	    	 			"_token": "{{ csrf_token() }}",
	    	 			'record_id':record_id,
	    	 		},
	    	 		beforeSend: function() {
	    	 			//block ui
	    	 			showLoader();
	    	 		},
	    	 		success: function(response) {
	    	 	 		hideLoader();
	    	 	 		if( response != "" && response != null ){
	    	 	 			$(".employee-leave-info").html(response);
	    	 	 			//$(thisitem).attr("data-fetch" , "{{ config('constants.SELECTION_YES') }}" )
	        	 	 	}
	    	 	 	},
	    	 		error: function() {
	    	 			hideLoader();
	    	 		}
	    	 	});
			}
			
		}

		function getEmployeeAttendanceList(thisitem){
			var record_id = $.trim($(thisitem).attr("data-record-id"));
			var data_fetch_status = $.trim($(thisitem).attr("data-fetch")); 

			if( data_fetch_status != "" && data_fetch_status != null && data_fetch_status == "{{ config('constants.SELECTION_NO') }}" ){
				$.ajax({
	    	 		type: "POST",
	    	 		
	    	 		url: employee_module_url + 'getEmployeeAttendanceList',
	    	 		data: {
	    	 			"_token": "{{ csrf_token() }}",
	    	 			'record_id':record_id,
	    	 		},
	    	 		beforeSend: function() {
	    	 			//block ui
	    	 			showLoader();
	    	 		},
	    	 		success: function(response) {
	    	 	 		hideLoader();
	    	 	 		if( response != "" && response != null ){
	    	 	 			$(".employee-attendance-info").html(response);
	    	 	 			//$(thisitem).attr("data-fetch" , "{{ config('constants.SELECTION_YES') }}" )
	        	 	 	}
	    	 	 	},
	    	 		error: function() {
	    	 			hideLoader();
	    	 		}
	    	 	});
			}
			
		}

		function getEmployeeTimeoffList(thisitem){
			var record_id = $.trim($(thisitem).attr("data-record-id"));
			$.ajax({
    	 		type: "POST",
    	 		url: employee_module_url + 'getEmployeeTimeOffList',
    	 		data: {
    	 			"_token": "{{ csrf_token() }}",
    	 			'record_id':record_id,
    	 		},
    	 		beforeSend: function() {
    	 			//block ui
    	 			showLoader();
    	 		},
    	 		success: function(response) {
    	 	 		hideLoader();
    	 	 		if( response != "" && response != null ){
    	 	 			$(".employee-leave-info").html(response);
        	 	 	}
    	 	 	},
    	 		error: function() {
    	 			hideLoader();
    	 		}
    	 	});
		}
<?php /*?>
		function showApproveRejectOtherDateDiv(thisitem){
			var approve_resign_initial_exit_recommend_last_working_day_type = $.trim($("[name='approve_resign_initial_exit_recommend_last_working_day_type']:checked").val());

			if( approve_resign_initial_exit_recommend_last_working_day_type != "" && approve_resign_initial_exit_recommend_last_working_day_type != null &&  approve_resign_initial_exit_recommend_last_working_day_type == "{{ config('constants.NOTICE_PERIOD') }}" ){
				$(".approve-resign-other-last-working-date").hide();
			} else {
				$(".approve-resign-other-last-working-date").show();
			}
			
		}
		<?php */?>
		<?php /*?>

		function showResignApproveRejectModal(thisitem){
			var employee_id = $.trim($(thisitem).attr('data-record-id'));;
			$("[name='resign_approve_reject_employee_id']").val(employee_id);

			$.ajax({
    	 		type: "POST",
    	 		url: employee_module_url + 'getResignTerminateRequestInfo',
    	 		data: {
    	 			"_token": "{{ csrf_token() }}",
    	 			'employee_id':employee_id,
    	 		},
    	 		beforeSend: function() {
    	 			//block ui
    	 			showLoader();
    	 		},
    	 		success: function(response) {
    	 	 		hideLoader();
    	 	 		if(response != "" && response != null ){
    	 	 			$(".resign-approve-reject-html").html(response);
    	 	 			
    	 	 			$("#resign-approve-reject-modal").find(".approve-button").hide();
    	 	 			$("#resign-approve-reject-modal").find(".reject-button").hide();
    	 	 			openBootstrapModal("resign-approve-reject-modal");
						var approve_resign_emp_joining_date = $.trim($("#resign-approve-reject-modal").find("[name='approve_resign_emp_joining_date']").val());
    	 	 			var resign_apply_date = $.trim($("#resign-approve-reject-modal").find("[name='resign_apply_date']").val());
						$(".select2").select2();
    	 	 			$('[name="approve_resign_initial_exit_other_last_working_date"],[name="approve_resign_initial_exit_employee_provide_notice_exit_date"]').datetimepicker({
    	 	 		        useCurrent: false,
    	 	 		        viewMode: 'days',
    	 	 		        ignoreReadonly: true,
    	 	 		        format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
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
    	 	 			$("[name='approve_resign_initial_exit_employee_provide_notice_exit_date']").data("DateTimePicker").minDate(moment(approve_resign_emp_joining_date,'YYYY-MM-DD'));
    	 	 		    if( resign_apply_date != "" && resign_apply_date != null ){
    	 	 		    	$("[name='approve_resign_initial_exit_other_last_working_date']").data("DateTimePicker").minDate(moment(resign_apply_date,'YYYY-MM-DD'));
        	 	 		} else {
        	 	 			$("[name='approve_resign_initial_exit_other_last_working_date']").data("DateTimePicker").minDate(moment(approve_resign_emp_joining_date,'YYYY-MM-DD'));
            	 	 	}

    	 	 		  	$('[name="approve_resign_initial_exit_employee_provide_notice_exit_date"]').datetimepicker().on('dp.change', function(e) {
				 			calculateApproveRejectTerminateNoticePeriodEndDate();
				 	 	});

	    	 	 		
	    	 	 		function calculateApproveRejectTerminateNoticePeriodEndDate(){
					 	 	var initial_exit_termination_date = $.trim($("[name='approve_resign_initial_exit_employee_provide_notice_exit_date']").val());
					 	 	var initial_exit_other_last_working_date = $.trim($("[name='approve_resign_initial_exit_other_last_working_date']").val());
					 	 	var notice_period_duration =  $.trim($("[name='initial_exit_termination_date']").attr("data-notice-duration"));
							var duration_value = $.trim($("[name='approve_resign_initial_exit_employee_provide_notice_exit_date']").attr("data-notice-duration-value")) ;
							var duration_selection = $.trim($("[name='approve_resign_initial_exit_employee_provide_notice_exit_date']").attr("data-notice-duration-selection")) ;	

					 	 	if( ( duration_value != "" && duration_value != null ) && ( duration_selection != "" && duration_selection != null )  ){
					 	 		 var notice_period_completed_date = moment(initial_exit_termination_date, 'DD-MM-YYYY').add( duration_value , duration_selection );
								 $(".notice-period-completion-date").html( " (" +  moment(notice_period_completed_date).format('DD MMM, YYYY') + ")" );
					 	 	}

					 	 	if( initial_exit_termination_date != "" && initial_exit_termination_date != null ){
								$("[name='approve_resign_initial_exit_other_last_working_date']").data("DateTimePicker").minDate(moment(initial_exit_termination_date,'DD-MM-YYYY').format('DD-MM-YYYY'));
						 	} else {
						 		$("[name='approve_resign_initial_exit_other_last_working_date']").data("DateTimePicker").minDate(false);
							}

					 	 	if( initial_exit_other_last_working_date != "" && initial_exit_other_last_working_date != null ){
						 	 	if( moment(initial_exit_other_last_working_date,'DD-MM-YYYY').isBefore(moment(initial_exit_termination_date,'DD-MM-YYYY')) == true ){
						 	 		$("[name='approve_resign_initial_exit_other_last_working_date']").val(initial_exit_termination_date);
							 	}	
						 	} else {

							}
							
					 	 }
					} 
    	 	 	},
    	 		error: function() {
    	 			hideLoader();
    	 		}
    	 	});
			
			
		}
	<?php */?>
		<?php /*
		$("#approve-reject-resign-form").validate({
            errorClass: "invalid-input",
            rules: {
            	approve_resign_reject_remark: {
                    required: true
                },
            },
            messages: {
            	approve_resign_reject_remark: {
                    required: "{{ trans('messages.require-remark') }}"
                },
            }
        });

		function updateResignStatus(thisitem){

			if(  $("#approve-reject-resign-form").valid() != true ){
				return false;
			}


			var resign_approve_reject_remark = $.trim($("[name='approve_resign_reject_remark']").val());
			var resign_approve_reject_employee_id = $.trim($("[name='resign_approve_reject_employee_id']").val());
			var status = $.trim($(thisitem).attr('data-action'));
			
			var confirm_msg = '';
			var confirm_msg_text = '';

			if( status == "{{ config('constants.APPROVED_STATUS') }}" ){
				confirm_msg = "{{ trans('messages.approve-resign-request') }}";
				confirm_msg_text = "{{ trans('messages.common-confirm-msg' , [ 'module' => trans('messages.approve-resign-request') ] ) }}";
			}else if( status == "{{ config('constants.REJECTED_STATUS') }}" ){
				confirm_msg = "{{ trans('messages.reject-resign-request') }}";
				confirm_msg_text = "{{ trans('messages.common-confirm-msg' , [ 'module' => trans('messages.reject-resign-request') ] ) }}";
			}

			var upcoming_leader_status = true;
			if( status == "{{ config('constants.APPROVED_STATUS') }}" ){
				$(".upcoming-leader-tbody tr").each(function(){
					var upcoming_leader_value = $.trim($(this).find('.upcoming-leader-value').val());
					if( ( upcoming_leader_status != false ) && ( upcoming_leader_value == "" || upcoming_leader_value == null  ) ){
						upcoming_leader_status = false;
						$(this).find('.upcoming-leader-value').focus()
					}
				});
			}

			if( upcoming_leader_status != true ){
				alertifyMessage('error' , "{{ trans('messages.required-leader-for-each-employee') }}");
				return false
			}

			var formData = new FormData( $('#approve-reject-resign-form')[0] );
			formData.append('employee_id' , resign_approve_reject_employee_id );
			formData.append('remark' , resign_approve_reject_remark );
			formData.append('status' , status );
			formData.append('approve_resign_initial_exit_employee_provide_notice_exit_date' , $.trim($("[name='approve_resign_initial_exit_employee_provide_notice_exit_date']").val()) );
			formData.append('approve_resign_initial_exit_other_last_working_date' , $.trim($("[name='approve_resign_initial_exit_other_last_working_date']").val()) );
			formData.append('approve_resign_initial_exit_recommend_last_working_day_type' , $.trim($("[name='approve_resign_initial_exit_recommend_last_working_day_type']:checked").val()) );

			alertify.confirm(confirm_msg, confirm_msg_text ,function() {

				
				
				$.ajax({
	    	 		type: "POST",
	    	 		url: employee_module_url + 'updateResignStatus',
	    	 		dataType:'json',
	    	 		data: formData,
	    	 		processData:false,
					contentType:false,
	    	 		beforeSend: function() {
	    	 			//block ui
	    	 			showLoader();
	    	 		},
	    	 		success: function(response) {
	    	 	 		hideLoader();
	    	 	 		if(response.status_code == 1 ){
		    	 	 		$("#resign-approve-reject-modal").modal('hide');
		    	 	 		$(".approve-reject-take-action-button").remove();
	    					alertifyMessage('success' , response.message);
	    					$('.employee-resign-info-section').html(response.data.initateExitHtml);
	    					if( status == "{{ config('constants.REJECTED_STATUS') }}" ){
	    						$('.employee-resign-info-section').hide();
		    				}
	    					$('.employee-primary-details').html(response.data.primaryDetailsInfo);
							$('.employee-profile-pic-view--master-div-html').html(response.data.mainProfileInfo);
	    				} else {
	    			    	alertifyMessage('error' , response.message);
	    				}
	    	 	 	},
	    	 		error: function() {
	    	 			hideLoader();
	    	 		}
	    	 	});
			}, function () { });	 
		} 
		
		
		*/?>

		function sendInvitation(thisitem){
			var record_id = $.trim($(thisitem).attr('data-record-id'));
			var button_text = $.trim($(thisitem).attr('title'));
			if( record_id != "" && record_id != null ){

				if( button_text == "{{ trans('messages.send-invite') }}" ){
					confirm_msg = "{{ trans('messages.send-invitation') }}";
					confirm_msg_text = "{{ trans('messages.common-confirm-msg' , [ 'module' => trans('messages.send-invitation') ] ) }}";
				}else if( button_text == "{{ trans('messages.resend-invite') }}" ){
					confirm_msg = "{{ trans('messages.resend-invitation') }}";
					confirm_msg_text = "{{ trans('messages.common-confirm-msg' , [ 'module' => trans('messages.resend-invitation') ] ) }}";
				}
				
				
				alertify.confirm(confirm_msg,  confirm_msg_text  ,function() {
					$.ajax({
		    	 		type: "POST",
		    	 		url: employee_module_url + 'sendLoginInvitation',
		    	 		dataType:'json',
		    	 		data: {
		    	 			"_token": "{{ csrf_token() }}",
		    	 			'employee_id':record_id,
		    	 		},
		    	 		beforeSend: function() {
		    	 			//block ui
		    	 			showLoader();
		    	 		},
		    	 		success: function(response) {
		    	 	 		hideLoader();
		    	 	 		if(response.status_code == 1 ){
			    	 	 		$(thisitem).attr("title" , "{{ trans('messages.resend-invite') }}");
			    	 	 		$(thisitem).html( "{{ trans('messages.resend-invite') }}");
		    					alertifyMessage('success' , response.message);
		    				} else {
		    			    	alertifyMessage('error' , response.message);
		    				}
		    	 	 	},
		    	 		error: function() {
		    	 			hideLoader();
		    	 		}
		    	 	});
				}, function () { });
			}
		}

		<?php /*
		function showAcceptResignField(){
			var accept_resign = $.trim($("[name='accept_resign']:checked").val());

			if( accept_resign != "" && accept_resign != null && accept_resign == "{{ config('constants.SELECTION_YES')  }}"){
				$("#resign-approve-reject-modal").find(".approve-reject-field").show();
				$("#resign-approve-reject-modal").find(".approve-button").show();
				$("#resign-approve-reject-modal").find(".reject-button").hide();
			} else {
				$("#resign-approve-reject-modal").find(".approve-reject-field").hide();
				$("#resign-approve-reject-modal").find(".approve-button").hide();
				$("#resign-approve-reject-modal").find(".reject-button").show();
			}
		}
		*/?>

		function cancelResign(thisitem){
			var record_type = $.trim($(thisitem).attr('data-record-type'));
			var record_id = $.trim($(thisitem).attr('data-record-id'));

			var confirm_msg = '';
			var confirm_box_msg = '';
			
			switch(record_type){
				case '{{ config("constants.EMPLOYER_INITIATE_EXIT_TYPE") }}':
					confirm_msg = "{{ trans('messages.cancel-termination') }}";
					confirm_msg_text = "{{ trans('messages.common-confirm-msg' , [ 'module' =>  trans('messages.cancel-termination')  ]) }}";
					break;
				case  '{{ config("constants.EMPLOYEE_INITIATE_EXIT_TYPE") }}':
					confirm_msg = "{{ trans('messages.cancel-resignation') }}";
					confirm_msg_text = "{{ trans('messages.common-confirm-msg' , [ 'module' =>  trans('messages.cancel-resignation')  ]) }}";
					break;
			}

			alertify.confirm(confirm_msg,  confirm_msg_text  ,function() {
				$.ajax({
	    	 		type: "POST",
	    	 		url: employee_module_url + 'cancelResignation',
	    	 		dataType:'json',
	    	 		data: {
	    	 			"_token": "{{ csrf_token() }}",
	    	 			'record_id':record_id,
	    	 			'record_type':record_type,
	    	 		},
	    	 		beforeSend: function() {
	    	 			//block ui
	    	 			showLoader();
	    	 		},
	    	 		success: function(response) {
	    	 	 		hideLoader();
	    	 	 		if(response.status_code == 1 ){
		    	 	 		$(".employee-resign-info").hide();
	    					alertifyMessage('success' , response.message);
	    					$('.employee-primary-details').html(response.data.primaryDetailsInfo);
							$('.employee-profile-pic-view--master-div-html').html(response.data.mainProfileInfo);
	    				} else {
	    			    	alertifyMessage('error' , response.message);
	    				}
	    	 	 	},
	    	 		error: function() {
	    	 			hideLoader();
	    	 		}
	    	 	});
			}, function () { });
		}

		function getEmployeeSalaryInfo(thisitem){
			var record_id = $.trim($(thisitem).attr("data-record-id"));
			$.ajax({
    	 		type: "POST",
    	 		url: employee_module_url + 'getEmployeeSalaryInfo',
    	 		dataType : 'json',
    	 		data: {
    	 			"_token": "{{ csrf_token() }}",
    	 			'record_id':record_id,
    	 		},
    	 		beforeSend: function() {
    	 			//block ui
    	 			showLoader();
    	 		},
    	 		success: function(response) {
    	 	 		hideLoader();

    	 	 		if(response.status_code == 1 ){
    	 	 			$(".employee-salary-info").html(response.data.html);
	    	 	 	}

    	 	 		/* if( response != "" && response != null ){
    	 	 			$(".employee-salary-info").html(response);
        	 	 	} */
    	 	 	},
    	 		error: function() {
    	 			hideLoader();
    	 		}
    	 	});
		}

		function cancelSuspension(thisitem){
			var record_id = $.trim($(thisitem).attr("data-record-id"));

			var confirm_msg = "{{ trans('messages.cancel-suspension') }}";
			var confirm_msg_text = "{{ trans('messages.common-confirm-msg' , [ 'module' =>  trans('messages.cancel-suspension')  ]) }}";

			alertify.confirm(confirm_msg,  confirm_msg_text  ,function() {
				$.ajax({
	    	 		type: "POST",
	    	 		url: employee_module_url + 'cancelSuspension',
	    	 		dataType : 'json',
	    	 		data: {
	    	 			"_token": "{{ csrf_token() }}",
	    	 			'record_id':record_id,
	    	 		},
	    	 		beforeSend: function() {
	    	 			//block ui
	    	 			showLoader();
	    	 		},
	    	 		success: function(response) {
	    	 	 		hideLoader();
	
	    	 	 		if( response.status_code == 1 ){
	    	 	 			alertifyMessage('success' , response.message);
	    	 	 			var selected_td = $(thisitem).parent('td');
	    	 	 			$(thisitem).remove();
	    	 	 			$(selected_td).html("{{ trans('messages.cancelled') }}");

	    	 	 			if( response.data.updateSuspendHtml == true){
								$(".profile-section").find('.login-status').hide();
								$(".profile-section").find('.relieved-status').hide();
								$(".profile-section").find('.suspended-status').show();
								$('.employee-primary-details').html(response.data.primaryDetailsInfo);
								$('.employee-profile-pic-view--master-div-html').html(response.data.mainProfileInfo);
							}

	    	 	 			
	        	 	 	} else {
	        	 	 		alertifyMessage('error' , response.message);
	            	 	}
	    	 	 	},
	    	 		error: function() {
	    	 			hideLoader();
	    	 		}
	    	 	});
			}, function () { });
		}

		function getEmployeePaySlipInfo(thisitem){
			var record_id = $.trim($(thisitem).attr("data-record-id"));
			$.ajax({
    	 		type: "POST",
    	 		url: employee_module_url + 'getEmployeePaySlipInfo',
    	 		data: {
    	 			"_token": "{{ csrf_token() }}",
    	 			'record_id':record_id,
    	 		},
    	 		beforeSend: function() {
    	 			//block ui
    	 			showLoader();
    	 		},
    	 		success: function(response) {
    	 	 		hideLoader();
    	 	 		if( response != "" && response != null ){
    	 	 			$(".employee-pay-slip-info").html(response);
        	 	 	}
    	 	 	},
    	 		error: function() {
    	 			hideLoader();
    	 		}
    	 	});
		}

		// function selectPaySlip(thisitem){
            // console.log($(thisitem));
			//  if($('input[type="checkbox"]').prop("checked", true)) {
            //     $(this).prop("checked", false);
            // } else {
            //     $(this).prop("checked", true);
            // }
        // }
		
		
		var orgChartZoom = 1;
		
		function openOrgChartModal(thisitem){
			var record_id = $.trim($(thisitem).attr("data-record-id"));
			
			// Reset zoom
			orgChartZoom = 1;
			
			// Open modal
			$('#org-chart-modal').modal('show');
			
			// Load org chart data
			$.ajax({
    	 		type: "POST",
    	 		url: employee_module_url + 'getEmployeeOrgChart',
    	 		data: {
    	 			"_token": "{{ csrf_token() }}",
    	 			'record_id':record_id,
    	 		},
    	 		beforeSend: function() {
    	 			$(".employee-org-chart-modal-content").html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="mt-2 text-muted">Loading organization chart...</p></div>');
    	 		},
    	 		success: function(response) {
    	 	 		if( response != "" && response != null ){
    	 	 			$(".employee-org-chart-modal-content").html(response);
        	 	 	}
    	 	 	},
    	 		error: function() {
    	 			$(".employee-org-chart-modal-content").html('<div class="alert alert-danger">Failed to load organization chart.</div>');
    	 		}
    	 	});
		}
		
		function zoomInOrgChart() {
			orgChartZoom += 0.1;
			if(orgChartZoom > 2) orgChartZoom = 2;
			applyOrgChartZoom();
		}
		
		function zoomOutOrgChart() {
			orgChartZoom -= 0.1;
			if(orgChartZoom < 0.5) orgChartZoom = 0.5;
			applyOrgChartZoom();
		}
		
		function resetZoomOrgChart() {
			orgChartZoom = 1;
			applyOrgChartZoom();
		}
		
		function applyOrgChartZoom() {
			$('#org-chart-content').css('transform', 'scale(' + orgChartZoom + ')');
		}
		
		function downloadOrgChartJPG() {
			showLoader();
			
			// Get the element
			const element = document.getElementById('org-chart-content');
			
			// Reset zoom for capture
			const originalTransform = element.style.transform;
			element.style.transform = 'scale(1)';
			
			// Use html2canvas to capture the element
			if (typeof html2canvas === 'undefined') {
				// Load html2canvas library dynamically
				var script = document.createElement('script');
				script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
				script.onload = function() {
					captureAndDownloadJPG(element, originalTransform);
				};
				document.head.appendChild(script);
			} else {
				captureAndDownloadJPG(element, originalTransform);
			}
		}
		
		function captureAndDownloadJPG(element, originalTransform) {
			html2canvas(element, {
				scale: 3,
				useCORS: true,
				allowTaint: true,
				backgroundColor: '#ffffff',
				logging: false,
				width: element.scrollWidth,
				height: element.scrollHeight
			}).then(function(canvas) {
				// Convert canvas to JPG
				const imgData = canvas.toDataURL('image/jpeg', 0.95);
				
				// Create download link
				const link = document.createElement('a');
				link.download = 'organization-chart.jpg';
				link.href = imgData;
				link.click();
				
				// Restore original transform
				element.style.transform = originalTransform;
				
				hideLoader();
				alertifyMessage('success', 'Organization chart downloaded successfully!');
			}).catch(function(error) {
				// Restore original transform
				element.style.transform = originalTransform;
				
				hideLoader();
				alertifyMessage('error', 'Failed to generate image');
				console.error('Error:', error);
			});
		}
		

	</script>
</main>

@endsection