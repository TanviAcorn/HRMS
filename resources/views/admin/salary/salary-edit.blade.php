<div class="salary-edit h-100" {{ ( isset($showPageContent) && ( $showPageContent != false ) ) ? '' : 'style=display:none;' }}>
    <div class="row">
        <div class="col-md-4 profile-detail-card">
            <div class="card card-display border-0 px-2 py-4 h-100">
                <div class="card-body px-2 py-0 d-flex align-items-center">
                    @if( isset($salaryMasterInfo) && (!empty($salaryMasterInfo->d_net_pay_annually)) )
                    	<div class="salary-display">
	                        <h5 class="details-title">{{ trans('messages.current-salary') }}</h5>
	                        <p class="details-text mb-0">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} {{ decimalAmount($salaryMasterInfo->d_net_pay_annually) }} / Annum</p>
							<?php /* ?>
							<a href="javascript:void(0);" data-toggle="modal" data-target="#verify_password_modal" title="{{ trans('messages.verify-password') }}" class="btn btn-sm bg-color1 text-white resend-btn py-2 px-3 position-relative">{{ trans('messages.verify-password') }}</a>
							<?php */ ?>
	                    </div>
	                @else
					    {{ trans('messages.salary-not-assigned') }}    
                    @endif
                    
                    
                </div>
            </div>
        </div>
        <div class="col-md-7 profile-detail-card">
            <div class="card card-display border-0 px-2 py-4 h-100 salary-screen" data-emp-code-name="{{ isset($employeeInfo->v_employee_full_name)  ? $employeeInfo->v_employee_full_name : '' }}{{ isset($employeeInfo->v_employee_code)  ? ' ('. $employeeInfo->v_employee_code . ')' : '' }}">
                <div class="card-body px-2 py-0">
                    <div class="row align-items-center">
                        <div class="pr-2 col-xl-2 mb-xl-0 mb-2">
                            <h5>{{ trans('messages.payroll') }}</h5>
                        </div>
                        <div class="col-sm-3 col-6 mb-sm-0 mb-2">
                            <h5 class="details-title text-truncate">{{ trans('messages.legal-entity') }}</h5>
                            <p class="details-text mb-0 text-truncate" title="{{ config('constants.COMPANY_NAME') }}">{{ config('constants.COMPANY_NAME') }}</p>
                        </div>
                        @if( isset($salaryMasterInfo->salaryGroup->v_group_name) && (!empty($salaryMasterInfo->salaryGroup->v_group_name)) )
                        <div class="col-xl-2 col-sm-3 col-6 mb-sm-0 mb-2">
                            <h5 class="details-title text-truncate">{{ trans('messages.salary-group') }}</h5>
                            <p class="details-text mb-0 text-truncate" title="{{ ( isset($salaryMasterInfo->salaryGroup->v_group_name) ? $salaryMasterInfo->salaryGroup->v_group_name : '' ) }}">{{ ( isset($salaryMasterInfo->salaryGroup->v_group_name) ? $salaryMasterInfo->salaryGroup->v_group_name : '' ) }}</p>
                        </div>
                        @endif
                        <div class="col-xl-2 col-sm-3 col-6 mb-sm-0 mb-2">
                            <h5 class="details-title text-truncate">{{ trans('messages.team') }}</h5>
                            <p class="details-text mb-0 text-truncate" title="{{ ( isset($employeeInfo->teamInfo->v_value) ? $employeeInfo->teamInfo->v_value : '' ) }}">{{ ( isset($employeeInfo->teamInfo->v_value) ? $employeeInfo->teamInfo->v_value : '' ) }}</p>
                        </div>
                        
                        <div class="col-xl-2 col-sm-3 col-6 mb-sm-0 mb-2">
                            <h5 class="details-title text-truncate">{{ trans('messages.pay-cycle') }}</h5>
                            <p class="details-text mb-0 text-truncate" title="Monthly">Monthly</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-11 profile-detail-card">
            <div class="card card-display border-0 px-2 pb-4 pt-0 h-100">
                <div class="card-body px-2 py-0">
                    <!-- time line section start -->
                    <div class="row px-0 py-3">
                        <div class="col-12 profile-details-title-card gap flex-wrap">
                            <h5 class="bg-title" id="exampleModalLabel">{{ trans("messages.salary-timeline") }}</h5>
							<div class="d-flex">
								@if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.ALL_VIEW_SALARY'), session()->get('user_permission')  ) ) ) ) ) ) 
									<a href="javascript:void(0);" onclick="openOnHoldSalaryModel(this)" data-employee-name="{{ (!empty($employeeInfo->v_employee_full_name) ? $employeeInfo->v_employee_full_name :'') }}" data-employee-id="{{ Wild_tiger::encode($employeeInfo->i_id) }}" title="{{ trans('messages.on-hold-salary') }}" class="btn btn-sm bg-color1 text-white resend-btn py-2 px-3 position-relative mr-2">{{ trans("messages.on-hold-salary") }}</a>
									 
									@if(count($reviseSalaryDetails) > 0 )
										<a href="javascript:void(0);" onclick="openSalaryModel(this)" data-emp-id="{{ Wild_tiger::encode($employeeInfo->i_id) }}" title="{{ trans('messages.revise-salary') }}" class="btn btn-sm bg-color1 text-white resend-btn py-2 px-3 position-relative">{{ trans("messages.revise-salary") }}</a>
									@else
										<a href="javascript:void(0);" onclick="openSalaryModel(this)" data-emp-id="{{ Wild_tiger::encode($employeeInfo->i_id) }}" title="{{ trans('messages.assign-salary') }}" class="btn btn-sm bg-color1 text-white resend-btn py-2 px-3 position-relative">{{ trans("messages.assign-salary") }}</a>	
									@endif
								@endif
							</div>
						</div>
                    </div>
                    <div class="row time-line-section">
                        <div class="col-12">
                            <div class="time-line-part">
                                @if(count($reviseSalaryDetails) > 0 )
                                	<ul class="time-line-list list-unstyled">
                                	@php
                                	$currentSalary = false;
                                	
                                	@endphp	
                                	@foreach($reviseSalaryDetails as $reviseSalaryKey => $reviseSalaryDetail)
                                		<li class="time-line-items position-relative">
	                                        <div class="top-text d-flex font-weight-bold">
	                                            <div class="d-sm-flex">
	                                                <h5 class="details-text mb-0 text-truncate mr-3">{{ trans('messages.salary-revision') }}</h5>
	                                                <p class="details-title salary-title text-truncate" >Effective {{ ( isset($reviseSalaryDetail->dt_effective_date) ? convertDateFormat ( $reviseSalaryDetail->dt_effective_date ) : '' )  }}</p>
	                                            </div>
	                                            @if( ( $currentSalary != true ) && ( strtotime($reviseSalaryDetail->dt_effective_date) <= strtotime(date('Y-m-d') ) ) )
	                                           
	                                            <span class="salary-timeline-btn">{{ trans('messages.current') }}</span>
	                                            @endif
	                                        </div>
	                                        <div class="row align-items-center salary-total-card border px-3 ml-0">
	                                            <div class="salary-total-item order-sm-1 order-1">
	                                                <h5 class="details-title">{{ trans('messages.earning') }}</h5>
	                                                <p class="details-text mb-0">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} {{ ( isset($reviseSalaryDetail->d_total_earning) ? decimalAmount ( $reviseSalaryDetail->d_total_earning ) : 0 )  }}</p>
	                                            </div>
	                                            <div class="salary-total-item-icon d-sm-block d-none order-sm-2 order-5">
	                                                <span class="salary-total-icon"></span>
	                                            </div>
	                                            <div class="salary-total-item order-sm-3 order-3">
	                                                <h5 class="details-title text-truncate">{{ trans('messages.deduction') }}</h5>
	                                                <p class="details-text mb-0 text-truncate">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} {{ ( isset($reviseSalaryDetail->d_total_deduction) ? decimalAmount ( $reviseSalaryDetail->d_total_deduction ) : 0 )  }}</p>
	                                            </div>
	                                            <div class="salary-total-item-icon d-sm-block d-none order-sm-4 order-6">
	                                                <i class="fas fa-grip-lines"></i>
	                                            </div>
	                                            <div class="salary-total-item order-sm-5 order-4">
	                                                <h5 class="details-title text-truncate">{{ trans('messages.total') }}</h5>
	                                                <p class="details-text mb-0">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} {{ ( isset($reviseSalaryDetail->d_net_pay_monthly) ? decimalAmount ( $reviseSalaryDetail->d_net_pay_monthly ) : 0 )  }}</p>
	                                            </div>
	                                            <?php  ?>
	                                            
	                                            
	                                            <div class="salary-total-edit-icon order-sm-6 order-2 mt-0">
	                                                <div class="btn-group">
	                                                    <button type="button" class="btn" data-toggle="dropdown" aria-expanded="false">
	                                                        <i class="fas fa-ellipsis-v"></i>
	                                                    </button>
	                                                    <div class="dropdown-menu dropdown-menu-right">
	                                                    	<a href="javascript:void(0)" onclick="openSalaryModel(this);" data-mode="view" data-revise-record-id="{{ Wild_tiger::encode($reviseSalaryDetail->i_id) }}" data-emp-id="{{ Wild_tiger::encode($reviseSalaryDetail->i_employee_id) }}" class="dropdown-item" title="{{ trans('messages.view-salary') }}">{{ trans('messages.view-salary') }}</a>
	                                                        @if( ( strtotime($reviseSalaryDetail->dt_effective_date) > strtotime(date('Y-m-d')) ) || ( $currentSalary != true ) )
		                                                        @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.ALL_VIEW_SALARY'), session()->get('user_permission')  ) ) ) ) ) )
		                                                        	@if( ( ( $currentSalary != true ) && ( strtotime($reviseSalaryDetail->dt_effective_date) <= strtotime(date('Y-m-d') ) ) ) || ( ( strtotime($reviseSalaryDetail->dt_effective_date) > strtotime(date('Y-m-d') ) ) ) )
			                                                        <a href="javascript:void(0)" onclick="openSalaryModel(this);" data-revise-record-id="{{ Wild_tiger::encode($reviseSalaryDetail->i_id) }}" data-emp-id="{{ Wild_tiger::encode($reviseSalaryDetail->i_employee_id) }}" class="dropdown-item" title="{{ trans('messages.edit-salary') }}">{{ trans('messages.edit-salary') }}</a>
			                                                        @endif
			                                                        @if( strtotime($reviseSalaryDetail->dt_effective_date) > strtotime(date('Y-m-d')) )
			                                                        <a href="javascript:void(0)" onclick="deleteRevieSalary(this);"  data-record-id="{{ Wild_tiger::encode($reviseSalaryDetail->i_id) }}" class="dropdown-item" title="{{ trans('messages.delete-salary') }}">{{ trans('messages.delete-salary') }}</a>
			                                                        @endif
		                                                        @endif
	                                                        @endif
	                                                    </div>
	                                                </div>
	                                            </div>
	                                            
	                                            <?php  ?>
	                                        </div>
	                                    </li>
	                                    @if( ( $currentSalary != true ) && ( strtotime($reviseSalaryDetail->dt_effective_date) <= strtotime(date('Y-m-d') ) ) )
	                                            @php $currentSalary = true; @endphp 
	                                    @endif       
                                	@endforeach
                                	</ul>
                                @else
                                	{{ trans('messages.salary-not-assigned') }}    
                                @endif
                            </div>
                        </div>
					</div>
                    <!-- time line section end -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- revise salary section-->
<div class="modal fade document-folder" id="revise-salary-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ ( count($reviseSalaryDetails) > 0 ? trans("messages.revise-salary") : trans("messages.assign-salary")  )  }}<span class="custom-twt-modal-header"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
           	 {!! Form::open(array( 'id '=> 'revise-salary-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body add-salary-model-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                	<input type="hidden" name="employee_id" value="{{ ( isset($employeeId) ? Wild_tiger::encode($employeeId) : '' )  }}">
                     <button type="button" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add revise-salary-action-button" title="{{ trans('messages.add') }}" onclick="updateReviseSalary(this);">{{ trans('messages.add') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
             {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- revise salary section end -->


<!-- verify password modal-->

<div class="modal fade document-folder" id="verify_password_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="close ml-auto mr-4 mt-3" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
            <div class="modal-body p-0">

                <div class="row no-gutters justify-content-center">
                    <div class="col-12">
                        <div class="verify-items">
                            <div>
                                <div class="password-icon">
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                </div>
                                <div class="title-text mb-3">
                                    <h1 class="background text-uppercase">{{ trans("messages.verify-password") }}</h1>
                                </div>
                                {!! Form::open(array( 'id '=> 'verify-password-form' , 'method' => 'post'  )) !!}
                                	<label>Verify that it is you !! You are trying to access Confidential Information. So please Enter your Password!!</label>
                                    <div class="form-group input-icon user-input mb-1">
                                        <label for="verify_password" class="form-label not-visible"></label>
                                        <input class="form-control" name="verify_password" type="password" placeholder="{{ trans('messages.password') }}" >
                                    </div>
                                    <input type="hidden" name="verify_password_employee_id" value="{{ ( isset($employeeId) ? Wild_tiger::encode($employeeId) : '' )  }}">
                                    <button type="button" onclick="verifyPassword(this);" class="btn submit bg-theme login-button text-white font-weight-bold mt-4">{{ trans("messages.verify") }}</button>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade document-folder" id="on-hold-salary-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title twt-on-hold-modal-header-name" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
           	 {!! Form::open(array( 'id '=> 'on-hold-salary-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body add-on-hold-salary-model-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                	<input type="hidden" name="employee_id" value="">
                	<input type="hidden" name="record_id" value="">
                     <button type="button" onclick="addOnHoldSalary()" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add onhold-salary-action-button" title="{{ trans('messages.add') }}" >{{ trans('messages.add') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
                <input type="hidden" name="on_hold_salary_count" value="">
                <input type="hidden" name="remove_data_id" value="">
             {!! Form::close() !!}
        </div>
    </div>
</div>
<script>
    $(function() {
        $('.effective-from-date').datetimepicker({
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
    });
</script>

<script>
    $("#revise-salary-form").validate({
        errorClass: "invalid-input",
        rules: {
            effective_from: {
                required: true
            },
            salary_group: {
                required: true
            },
            deduction_employer_from_employee: {
                required: true
            },

        },
        messages: {
            effective_from: {
                required: "{{ trans('messages.require-effective-date') }}"
            },
            salary_group: {
                required: "{{ trans('messages.require-select-salary-group') }}"
            },
            deduction_employer_from_employee: {
                required: "{{ trans('messages.require-select-deduction-employer-from-employee') }}"
            },
        }
    });
</script>

<script>
    $("#verify-password-form").validate({
        errorClass: "invalid-input",
        rules: {
            verify_password: {
                required: true
            },
        },
        messages: {
            verify_password: {
                required: "{{ trans('messages.required-login-password') }}"
            },
        }
    });

    $('#verify-password-form').on("submit",function(e){
    	e.preventDefault();
        e.stopPropagation();
    });

    $(document).ready(function(){
		var show_content = "{{ isset($showPageContent)  ? $showPageContent : false }}";
		//console.log("show_content = " + show_content );
		if( show_content != true ){
			//console.log("show popup");
			openBootstrapModal('verify_password_modal');
			
		} else {
			//console.log("hide popup");
		}

    });
    var salary_module_url = '{{config("constants.SALARY_MASTER_URL")}}' + '/';
    var ajax_request = "{{ isset($ajaxRequest) ? $ajaxRequest : false  }}";
    
    function verifyPassword(thisitem){

	   if(  $("#verify-password-form").valid() != true ){
			return false;
	   } 		
	   
	   var employee_id = $.trim($("[name='verify_password_employee_id']").val());
	   var verify_password = $.trim($("[name='verify_password']").val());

	   
   		$.ajax({
	   		type: "POST",
	   		url: salary_module_url +'verifyPassword',
	   		dataType: "json",
	   		data: {"_token": "{{ csrf_token() }}",'employee_id': employee_id ,'verify_password': verify_password },
	   		beforeSend: function() {
	   			
	   		},
	   		success: function (response) {
	   			if (response.status_code == 1) {
	   				alertifyMessage('success',response.message);
	   				$("#verify_password_modal").modal('hide');
	   				$(".modal-backdrop").remove();
	   				$("body").removeClass("modal-open")
	   				if( ajax_request != false ){
		   				
						$(".employee-salary-tab").trigger('click');
		   			} else {
		   				window.location.reload();
			   		}
					
	   			} else {
	   				alertifyMessage('error',response.message);
	   			}
	   		}
	   	});
	}


	function updateReviseSalary(thisitem){

		if( $("#revise-salary-form").valid()  != true  ){
			return false;
		}

		var earning_head_value = false;
		$(".earning-row").each(function(){
			if(parseFloat($(this).find('.monthly-column').val()) > 0.00 ){
				earning_head_value = true;
			}
		})
		
		if( earning_head_value != true ){
			alertifyMessage('error' , '{{ trans("messages.error-atleast-one-earning-head-value") }}');
			return false
		}

		var confirm_msg = '';
		var confirm_msg_text = '';

		<?php if( count($reviseSalaryDetails) > 0 )  { ?>
			confirm_msg = "{{ trans('messages.revise-salary') }}";
			confirm_msg_text = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.revise-salary')]) }}";
		<?php } else { ?>
			confirm_msg = "{{ trans('messages.assign-salary') }}";
			confirm_msg_text = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.assign-salary')]) }}";
		<?php } ?>
		

		alertify.confirm( confirm_msg , confirm_msg_text ,function() {
			$($("#revise-salary-form").find("[name='deduction_of_pf']")).prop('disabled', false);
			var formData = new FormData( $('#revise-salary-form')[0] );

			$.ajax({
		   		type: "POST",
		   		url: salary_module_url + 'updateReviseSalary',
		   		data:formData,
		   		dataType : 'json',
				processData:false,
				contentType:false,
		   		beforeSend: function() {
		   			showLoader();
		   		},
		   		success: function (response) {
			   		hideLoader();
			   		if( response.status_code == 1 ){
			   			alertifyMessage('success',response.message);
			   			$("#revise-salary-model").modal('hide')
			   			$(".employee-salary-tab").trigger('click');
			   			//window.location.reload();
				   	} else {
				   		alertifyMessage('error',response.message);
					}
			   	}
		   	});
		},function() {});
		
	} 

	function openSalaryModel(thisitem){
	   var employee_id = $.trim($(thisitem).attr("data-emp-id"));
	   var revise_record_id = $.trim($(thisitem).attr("data-revise-record-id"));
	   var view_mode = $.trim($(thisitem).attr("data-mode"));

	   
	   if(employee_id !="" && employee_id != null){
	   	   $.ajax({
		   		type: "POST",
		   		url: salary_module_url +'openSalaryModel',
		   		data: {"_token": "{{ csrf_token() }}",'employee_id': employee_id , 'revise_record_id' : revise_record_id  },
		   		beforeSend: function() {
			   		showLoader();
		   		},
		   		success: function (response) {
					hideLoader();
			   		$('.add-salary-model-html').html(response);
			   		var effective_from = $.trim($("[name='effective_from']").val());
			   		if( effective_from != "" && effective_from != null ){

				   	}
				   	var salary_modal_header_name = '';
					var salary_emp_code_name = $.trim($('.salary-screen').attr('data-emp-code-name')); 
			   		if( revise_record_id != "" && revise_record_id != null ){
						$(".revise-salary-action-button").attr("title" , "{{ trans('messages.update') }}");
						$(".revise-salary-action-button").html("{{ trans('messages.update') }}");
				   	} else {
				   		$(".revise-salary-action-button").attr("title" , "{{ trans('messages.add') }}");
						$(".revise-salary-action-button").html("{{ trans('messages.add') }}");
					}

			   		if( view_mode != "" && view_mode != null && view_mode == "view"){
			   			salary_modal_header_name = "{{ trans('messages.view-salary-breakup') }}";
			   			<?php if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission')  && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.ALL_VIEW_SALARY'), session()->get('user_permission')  ) ) ) ) ) ) {  ?>
			   				$("#revise-salary-model").find('.only-admin-can-manage').show();
			   			<?php } else {  ?>
			   				$("#revise-salary-model").find('.only-admin-can-manage').hide();
			   			<?php } ?>
			   			
			   			$(".revise-salary-action-button").hide();
			   			$($("#revise-salary-model").find('input[type="text"], textarea, select')).each(function(){
			   	            $(this).prop('readonly', true);
			   	        });
			   			$($("#revise-salary-model").find('input[type="text"], textarea, select')).each(function(){
			   	            $(this).prop('disabled', true);
			   	        });
			   			$($("#revise-salary-model").find("select")).prop('disabled', true);
			   			$($("#revise-salary-model").find("[name='deduction_of_pf']")).prop('disabled', true);
				   	} else {
					   
				   		$("#revise-salary-model").find('.only-admin-can-manage').show();
				   		$(".revise-salary-action-button").show();
				   		$($("#revise-salary-model").find('input[type="text"], textarea, select')).each(function(){
			   	            $(this).prop('readonly', false);
			   	        });
				   		$($("#revise-salary-model").find('input[type="text"], textarea, select')).each(function(){
			   	            $(this).prop('disabled', false);
			   	        });
				   		$($("#revise-salary-model").find("select")).prop('disabled', false);
				   		
				   		if( $($("#revise-salary-model").find("[name='deduction_of_pf']")).prop('disabled') != true ){
				   			$($("#revise-salary-model").find("[name='deduction_of_pf']")).prop('disabled', false);
						}
			   			

			   			if( revise_record_id != "" && revise_record_id != null ){
			   				salary_modal_header_name = "{{ trans('messages.edit-salary') }}";
						} else {
							var selecetde_salary_group_id = $("#revise-salary-form").find("[name='salary_group']").val();
							if( selecetde_salary_group_id != "" && selecetde_salary_group_id != null ){
								salary_modal_header_name = "{{ trans('messages.revise-salary') }}";
							} else {
								salary_modal_header_name = "{{ trans('messages.assign-salary') }}";
							}
					   		
						}
					}
					
				   	$("#revise-salary-model").find(".modal-title").html( salary_modal_header_name + ' - ' + salary_emp_code_name  );
	   				$(".add-salary-model-html").find(".monthly-column").trigger('keyup');
	   				openBootstrapModal('revise-salary-model');
			   		
		   		}
		   	});
	    }
		
	}
	function getSalaryGruopDetails(thisitem){
		var salary_group = $.trim($("[name='salary_group']").val());
		
		if(salary_group != "" && salary_group != null){
			var employee_id = $.trim($("[name='revise_salary_employee_id']").val());
			var revise_record_id = $.trim($("[name='revise_salary_record_id']").val());
			$(".salary-breakup-record-show").show();

			$.ajax({
		   		type: "POST",
		   		async: false,
		   		url: salary_module_url +'getGroupSalaryComponent',
		   		data: {"_token": "{{ csrf_token() }}",'salary_group': salary_group , 'employee_id'  : employee_id , 'revise_record_id'  : revise_record_id },
		   		beforeSend: function() {
		   			
		   		},
		   		success: function (response) {
			   		if(salary_group !="" && salary_group != null){
		   				$('.salary-group-components-record').html(response);
		   				totalEarning();
		   				totalDeduction();
			   		}
		   		}
		   	});

		   	
		} else {
			$(".salary-breakup-record-show").hide();
			totalEarning();
			totalDeduction();
		}
	}

	function deleteRevieSalary(thisitem){
		var revise_record_id = $.trim($(thisitem).attr("data-record-id"));
		if( revise_record_id != "" && revise_record_id != null ){
			alertify.confirm('{{ trans("messages.delete-record") }}', '{{ trans("messages.delete-record-msg") }}' , function () {
				$.ajax({
			   		type: "POST",
			   		dataType : 'json',
			   		url: salary_module_url +'delete-revise-salary-record',
			   		data: {"_token": "{{ csrf_token() }}",'revise_record_id': revise_record_id },
			   		beforeSend: function() {
			   			showLoader();
			   		},
			   		success: function (response) {
				   		hideLoader();
				   		if( response.status_code == 1 ){
				   			alertifyMessage('success',response.message);
				   			$(".employee-salary-tab").trigger('click');
				   			//window.location.reload();
					   	} else {
					   		alertifyMessage('error',response.message);
						}
			   		}
			   	});
			},function() {});	
		}
	}
	
	function openOnHoldSalaryModel(thisitem){
		var employee_record_id = $.trim($(thisitem).attr("data-employee-id"));
		var header_name = $.trim($(thisitem).attr("data-employee-name"));
		$("[name='employee_id']").val(employee_record_id);
		
		if(employee_record_id !="" && employee_record_id != null){
	   	   $.ajax({
		   		type: "POST",
		   		url: salary_module_url +'editOnHoldSalaryModel',
		   		data: {"_token": "{{ csrf_token() }}",'employee_record_id': employee_record_id },
		   		beforeSend: function() {
			   		showLoader();
		   		},
		   		success: function (response) {
					hideLoader();
					if(response !="" && response != null ){
			   			$('.add-on-hold-salary-model-html').html(response);
			   			var salary_emp_code_name = $.trim($('.salary-screen').attr('data-emp-code-name')); 
			   			$("#on-hold-salary-model").find('.twt-on-hold-modal-header-name').html("{{ trans('messages.on-hold-salary')  }}" +" - "+ salary_emp_code_name);
			   			openBootstrapModal('on-hold-salary-model');
					}
		   		}
		   	});
	    }
	}
	var on_hold_salary_count = 2;
	function addNewRow(){
		var on_hold_joining_date = $.trim($("[name='on_hold_joining_date']").val());
		
		on_hold_salary_count++;
		var html = '';
		//html += '<div class="salary-on-hold-master-div">';
		html += '<div class="row month-master-row">';
		html += '<div class="col-6 mt-2">';
		if($('.month-master-row').length == 0){
			html += '<label>{{ trans('messages.month') }}</label>';
		}
		html += '<input type="text" name="month_'+on_hold_salary_count+'" class="form-control onhold-salary-month month-record-row unique-month" placeholder="{{ config("constants.ON_HOLD_SALARY_DEFAULT_MONTH_FORMAT")}}" value="">'; 
		html += '</div>';
		html += '<div class="col-4 mt-2">';
		if($('.month-master-row').length == 0){
			html += '<label>{{ trans('messages.amount') }}</label>';
		}
		html += '<input type="text" name="amount_'+on_hold_salary_count+'" onkeyup="onlyNumber(this)" onchange="onlyNumber(this)" class="form-control amount-record-row" placeholder="{{ trans("messages.amount")}}" value="">'; 
		html += '</div>';
		if($('.month-master-row').length == 0){
			html += '<div class="col-2 mt-4 pt-2">';
			html += '<button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeHtml(this)" title="{{ trans("messages.delete")}}"><i class="fas fa-trash"></i></button>';
			html += '</div>';    
		} else {
			html += '<div class="col-2 mt-1">';
			html += '<button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeHtml(this)" title="{{ trans("messages.delete")}}"><i class="fas fa-trash"></i></button>';
			html += '</div>';   
		}
		            
		html += '</div>';
		//html += '</div>';
		
		var investorDivClass = $('.salary-on-hold-master-div').find('.month-master-row').length
		if( investorDivClass == 0 ){
			$('.salary-on-hold-master-div').html(html);
			
		} else {
			$(html).insertAfter($('.salary-on-hold-master-div').find('.month-master-row:last'));
			
		}
		$('[name="month_'+on_hold_salary_count+'"]').datetimepicker({
	          useCurrent: false,
	          viewMode: 'days',
	          ignoreReadonly: true,
	          format: 'MMM-YYYY',
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
		 $('[name="month_'+on_hold_salary_count+'"]').data('DateTimePicker').minDate(moment(on_hold_joining_date,'MMM-YYYY'));    
		  
	}

	var remove_data = []; 
	function removeHtml(thisitem){
		alertify.confirm('{{ trans("messages.delete-record") }}', '{{ trans("messages.delete-record-msg") }}' , function () {
			
			$(thisitem).parents('.month-master-row').remove();
			remove_data.push( $(thisitem).attr('data-remove-id') );
			//console.log("remove_data" );
			//console.log(remove_data);
			$('[name="remove_data_id"]').val(remove_data)
			
		}, function () { });
	}
	function addOnHoldSalary(){
		if($('#on-hold-salary-form').valid() != true){
			return false;
		}
		var record_id = $.trim($('[name="record_id"]').val());
		var remove_data_id = $.trim($('[name="remove_data_id"]').val());
		
		var month_unique_msg ='';
	    month_unique_msg = "{{ trans('messages.unique-month-name')}}";
		var unique_month_name = true;
		
		var selected_month = [];
		var error_amount_field = false;
		var duplicate_month_name = '';	
		$('.unique-month').each(function(index, value) {
			
			var month_value = $(this).parents('.month-master-row').find('.month-record-row').val();
			var amount_value = $(this).parents('.month-master-row').find('.amount-record-row').val();
			
			
			if( month_value != "" && month_value != null ){
				
				if(selected_month.indexOf(month_value) !== -1){
					if( unique_month_name != false ){
						unique_month_name = false;
						duplicate_month_name = $(this).find('.month-record-row').html();
						$(this).find('.month-record-row').focus();
					}
				} else {
					
					selected_month.push(month_value);
				}

				if( ( amount_value == "" || amount_value == null ) && ( error_amount_field != true ) ){
					$(this).parents('.month-master-row').find('.amount-record-row').focus()
					error_amount_field = true;
				}
				
			}
		});

		<?php /* if( selected_month.length == 0 ){
			alertifyMessage("error","{{ trans('messages.required-atleast-one-month')}}");
			return false;
		} */ ?>
		
		if( error_amount_field != false ){
			alertifyMessage("error","{{ trans('messages.required-amount')}}");
			return false;
		}
		
		if( unique_month_name == false ) {
			alertifyMessage('error', month_unique_msg.replace(duplicate_month_name ) );
			return false;
		}

		var unique_month_for_on_hold_salary = true;
		
		$.ajax({
			type: "POST",
			dataType: "json",
			url: salary_module_url + 'checkUniqueMonthName',
			async: false,
			data:{"_token": "{{ csrf_token() }}",'selected_month':selected_month , record_id : record_id},
			beforeSend: function() {
				//block ui
				//showLoader();
			},
			success: function(response) {
				//hideLoader();
				if( response.status_code == 101 ){
					unique_month_for_on_hold_salary  = false;
					alertifyMessage('error',response.message);
				} 
			},
			error: function() {
				hideLoader();
			}
		});
		if( unique_month_for_on_hold_salary != false ){
			$("[name='on_hold_salary_count']").val(on_hold_salary_count);
			
			var formData = new FormData( $('#on-hold-salary-form')[0] );
			
			alertify.confirm("{{ trans('messages.manage-on-hold-salary') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.manage-on-hold-salary')]) }}",function() { 
		    	 $.ajax({
		     		type: "POST",
		     		dataType :'json',
		     		url: salary_module_url + 'addOnHoldSalary',
		     		data:formData,
					processData:false,
					contentType:false,
		     		beforeSend: function() {
		     			//block ui
		     			showLoader();
		     		},
		     		success: function(response) {
		     			hideLoader();
		     			if( response.status_code == 1 ){
							$("#on-hold-salary-model").modal('hide');
							alertifyMessage('success',response.message);
							
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
	}
	
</script>
@include( config('constants.ADMIN_FOLDER') . 'salary-calculation')