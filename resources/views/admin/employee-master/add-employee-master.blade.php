@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
@include('admin/city-model')
@include('admin/village-modal')	
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ $pageTitle }}</h1>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="filter-result-wrapper card">
            <section class="signup-step-container">
                <div class="profile-form welcome-page-height">
                    <div class="wizard">
                        <div class="wizard-inner">
                            <div class="d-flex align-items-center">
                                <div class="wizard-button-class">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="step-content active">
                                            <a href="javascript:void(0)" class="step-one active selected-step" data-toggle="tab" data-tab-name="step1" aria-controls="step1" role="tab" aria-expanded="true">
                                                <span class="indicator" data-default="01">01</span>
                                                <span class="description">
                                                    <span>{{trans('messages.basic-details')}}</span>
                                                </span>
                                                <span class="line"></span>
                                            </a>
                                        </li>
                                        <li class="step-content step2">
                                            <a href="javascript:void(0)" class="step-one selected-step" style="cursor: default;" data-tab-name="step2"  aria-controls="step2"  aria-expanded="false">
                                                <span class="indicator" data-default="01">02</span>
                                                <span class="description">
                                                    <span>{{trans('messages.address-identity')}} <span class="details">{{trans('messages.details')}}</span> </span>
                                                </span>
                                                <span class="line"></span>
                                            </a>
                                        </li>
                                        <li class="step-content step3">
                                            <a href="javascript:void(0)" class="step-one selected-step" style="cursor: default;" data-tab-name="step3">
                                                <span class="indicator" data-default="01">03</span>
                                                <span class="description">
                                                    <span>{{trans('messages.job-details')}}</span>
                                                </span>
                                                <span class="line"></span>
                                            </a>
                                        </li>
                                        <li class="step-content step4">
                                            <a href="javascript:void(0)" class="step-one selected-step" style="cursor: default;" data-tab-name="step4">
                                                <span class="indicator" data-default="01">04</span>
                                                <span class="description">
                                                    <span class="details">{{trans('messages.salary-details')}}</span>
                                                </span>
                                                <span class="line"></span>
                                            </a>
                                        </li>
                                        <li class="step-content step5">
                                            <a href="javascript:void(0)" class="step-one selected-step" style="cursor: default;" data-tab-name="step5">
                                                <span class="indicator" data-default="01">05</span>
                                                <span class="description">
                                                    <span class="details">{{trans('messages.asset-details')}}</span>
                                                </span>
                                                <span class="line"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <a href="{{ config('constants.EMPLOYEE_MASTER_URL')}}" class="btn btn-outline-secondary ml-auto mr-3 add-employee-close" title="{{ trans('messages.cancel') }}"><span class="step-close-btn d-none d-sm-block">{{ trans('messages.cancel') }}</span> <i class="fas fa-times step-colse-icon d-block d-sm-none "></i></a>


                            </div>
                        </div>
                        <div class="card-body">
                            <div class="wizard-content">
                                <div class="row d-flex justify-content-center dependant-field-selection">
                                    <div class="col-md-12 px-sm-3 px-0">
                                        {!! Form::open(array( 'id '=> 'add-employee-master-form' , 'method' => 'post' ,  'url' => 'employee-master/add')) !!}
                                            <div class="tab-content" id="main_form">
                                                <div class="tab-pane active" role="tabpanel" id="step1" data-tab-name="step1">
                                                    @include( config('constants.ADMIN_FOLDER') . 'employee-master/basic-details')
                                                </div>
                                                <div class="tab-pane" role="tabpanel" id="step2" data-tab-name="step2">
                                                    @include( config('constants.ADMIN_FOLDER') . 'employee-master/address-details')
                                                </div>
                                                <div class="tab-pane" role="tabpanel" id="step3" data-tab-name="step3">
                                                    @include( config('constants.ADMIN_FOLDER') . 'employee-master/job-details')
                                                </div>
                                                <div class="tab-pane" role="tabpanel" id="step4" data-tab-name="step4">
                                                    @include( config('constants.ADMIN_FOLDER') . 'employee-master/salary-details')
                                                </div>
                                                <div class="tab-pane" role="tabpanel" id="step5" data-tab-name="step5">
                                                    @include( config('constants.ADMIN_FOLDER') . 'employee-master/asset-details')
                                                </div>
                                            </div>
                                            <input type="hidden" name="record_id" value="">
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>


</main>

<script>
	var employee_module_url = '{{config("constants.EMPLOYEE_MASTER_URL")}}' + '/';
    $("#add-employee-master-form").validate({
        errorClass: "invalid-input",
        onfocusout: false,
		onkeyup: false,
		ignore: [],
        rules: {
            employee_code: {required: true , noSpace: true, validateUniqueEmployeeCode:true },
            employee_name: {required: true , noSpace: true },
            full_name: {  required: true , noSpace: true },
            gender: {required: true , noSpace: true },
            date_of_birth: {required: true , noSpace: true },
            personal_email_id: {required: true , noSpace: true , email_regex : true },
            outlook_email_id: { noSpace: true ,validateUniquePersonalEmailId:true , email_regex : true },
            contact_number: {required: true , noSpace: true },
            education: {required: true , noSpace: true },
            cgpa_percentage: {required: true , noSpace: true },
            address_line_1: {required: true , noSpace: true },
            //current_city: {required: true, noSpace: true },
            current_state: {required: false , noSpace: true },
            current_country: {required: false , noSpace: true },
            aadhaar_number: {required: true , noSpace: true },
            joining_date: {required: true , noSpace: true },
            designation: {required: true , noSpace: true },
            team: {required: true , noSpace: true},
            leader_name_reporting_manager: {required: false , noSpace: true},
            recruitment_source: {required: true , noSpace: true},
            reference_name: {required: false , noSpace: true},
            notice_period: {required: true , noSpace: true },
            assign_salary_employee: { required: true , noSpace: true },
            address_permanent_line_1: {required: true , noSpace: true },
           // per_city: {required: true, noSpace: true },
            per_state: {required: false , noSpace: true },
            per_country: {required: false , noSpace: true },
            shift: {required: true , noSpace: true },
            new_weekly_off: {required: true , noSpace: true },
            week_off_effective_date: {required: true , noSpace: true },

            current_village: {required: false, noSpace: true },
            permanent_village: {required: false, noSpace: true },
            reference_name: {
    		    required: function(element){
    				return ( $.trim($("[name='recruitment_source']").find("option:selected").attr("data-recruitment-source")) == '{{ config("constants.EMPLOYEE_RECRUITMENT_SOURCE_ID") }}' ? true : false )
    			},
    		    noSpace: true
    	    },
            current_city: {
    		    required: function(element){
    				return ( $.trim($("[name='current_village']").val()) == '' ? true : false )
    			},
    		    noSpace: true
    	    },
    	    per_city: {
    		    required: function(element){
    				return ( $.trim($("[name='permanent_village']").val()) == '' ? true : false )
    			},
    		    noSpace: true
    	    },
    	    salary_group: {
    		    required: function(element){
    				return ( $.trim($("[name='assign_salary_employee']:checked").val()) == '{{ config("constants.SELECTION_YES") }}' ? true : false )
    			},
    		    noSpace: true
    	    },
        },
        messages: {
            employee_code: { required: "{{ trans('messages.require-employee-code') }}"  },
            employee_name: { required: "{{ trans('messages.require-employee-name') }}" },
            full_name: {required: "{{ trans('messages.require-full-name') }}"},
            gender: { required: "{{ trans('messages.require-select-gender') }}" },
            date_of_birth: {required: "{{ trans('messages.require-enter-date-of-birth') }}" },
            personal_email_id: {required: "{{ trans('messages.require-enter-personal-email-id') }}" },
            contact_number: {required: "{{ trans('messages.require-enter-contact-number') }}" },
            education: {required: "{{ trans('messages.require-education') }}" },
            cgpa_percentage: {required: "{{ trans('messages.require-cgpa-percentage') }}" },
            address_line_1: {required: "{{ trans('messages.require-enter-address-line-1') }}" },
            current_city: { required: "{{ trans('messages.require-select-city') }}" },
            current_state: {required: "{{ trans('messages.require-select-state') }}" },
            current_country: {required: "{{ trans('messages.require-select-country') }}" },
            aadhaar_number: {required: "{{ trans('messages.require-enter-aadhaar-number') }}" },
            joining_date: {required: "{{ trans('messages.require-enter-joining-date') }}" },
            designation: { required: "{{ trans('messages.require-select-designation') }}"},
            team: { required: "{{ trans('messages.require-select-team') }}"},
            leader_name_reporting_manager: { required: "{{ trans('messages.require-select-leader-name-reporting-manager') }}"},
            recruitment_source: {required: "{{ trans('messages.require-select-recruitment-source') }}"},
            reference_name: {required: "{{ trans('messages.require-select-reference-name') }}"},
            notice_period: { required: "{{ trans('messages.require-select-notice-period') }}" },
            assign_salary_employee: {required: "{{ trans('messages.require-select-assign-salary-employee') }}"},
            address_permanent_line_1: {required: "{{ trans('messages.require-enter-address-line-1') }}" },
            current_city: { required: "{{ trans('messages.require-select-city') }}" },
            per_city: { required: "{{ trans('messages.require-select-city') }}" },
            per_state: {required: "{{ trans('messages.require-select-state') }}" },
            per_country: {required: "{{ trans('messages.require-select-country') }}" },
		    shift: {required: "{{ trans('messages.require-select-shift') }}" },
            new_weekly_off: {required: "{{ trans('messages.require-select-weekly-off') }}" },
            current_village: { required: "{{ trans('messages.require-select-village') }}" },
            permanent_village: { required: "{{ trans('messages.require-select-village') }}" },
            salary_group: { required: "{{ trans('messages.require-select-salary-group') }}" },
            outlook_email_id: { required: "{{ trans('messages.require-enter-outlook-email-id') }}" },
            week_off_effective_date: { required: "{{ trans('messages.require-week-off-effective-date') }}" },
            
        },
        submitHandler: function(form) {
         showLoader()
		 $("[name='current_state']").prop('disabled' , false );
		 $("[name='country']").prop('disabled' , false );
		 $("[name='per_state']").prop('disabled' , false );
		 $("[name='per_country']").prop('disabled' , false );
		 $("[name='current_city']").prop('disabled' , false );
		 $("[name='per_city']").prop('disabled' , false );
		 $("[name='employee_code']").prop('disabled' , false );
		 form.submit();
   		
       	}
   	});
    
</script>

<script>
    // ------------step-wizard-------------
    $(document).ready(function() {
        $('.nav-tabs > li a[title]').tooltip();
		$(".prev-step").click(function(e) {
			previousTWTWTab(this);
		});
		$('[name="joining_date"]').datetimepicker({
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
		$('[name="week_off_effective_date"]').datetimepicker({
	        useCurrent: false,
	        viewMode: 'days',
	        ignoreReadonly: true,
	        format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
	        showClear: true,
	        showClose: true,
	        widgetPositioning: {
	            vertical: 'top',
	            horizontal: 'auto'

	        },
	        icons: {
	            clear: 'fa fa-trash',
	            Close: 'fa fa-trash',
	        },
	    });
	    //$("[name='auto_calculate_employee_code']").trigger('click');
		$(function () {
			  //$("[name='joining_date']").data('DateTimePicker').maxDate(moment().endOf('d'));


			$("[name='joining_date']").datetimepicker().on('dp.change', function(e) {
				if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
					var incrementDay = moment((e.date)).startOf('d');
				 	$("[name='week_off_effective_date']").data('DateTimePicker').minDate(incrementDay);
				} else {
					$("[name='week_off_effective_date']").data('DateTimePicker').minDate(false);
				} 
				
			    $(this).data("DateTimePicker").hide();
			});

		    $("[name='week_off_effective_date']").datetimepicker().on('dp.change', function(e) {
		    	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
			        var decrementDay = moment((e.date)).endOf('d');
			        $("[name='joining_date']").data('DateTimePicker').maxDate(decrementDay);
		    	} else {
		    		 $("[name='joining_date']").data('DateTimePicker').maxDate(false);
		        }
		        $(this).data("DateTimePicker").hide();
		    });
			  
		});
    });


    function showBasicTab(){
    	$(document).find('.wizard .nav-tabs li.active').removeClass('active');
    	$(document).find('.wizard .nav-tabs li:first').addClass('active');
    	$(".tab-content").find(".tab-pane").removeClass('active');
    	$(".tab-content").find(".tab-pane:first").addClass('active');
    }
    
	function nextTWTWTab(thisitem) {
		var next_tab_name = $.trim($(thisitem).attr("data-tab-name"));
		//console.log("next_tab_name = " + next_tab_name  );
		$(".selected-step").removeClass('active');
		
		$(".step-content." + next_tab_name).prev('.step-content').addClass('active');
		var active = $(document).find('.wizard .nav-tabs li.active');
		//console.log("active");
		//console.log(active);
		active.next().removeClass('disabled');

		
		$(document).find('.wizard .nav-tabs li.active').next().addClass('active')
		$(document).find('.wizard .nav-tabs li.active').next().find('.next-screen').addClass('active')
        active.removeClass('active');
        active.next().addClass('active');
		$(".tab-content").find(".tab-pane").removeClass('active');
        $(".tab-content").find("[data-tab-name='" + next_tab_name + "']").addClass('active');
        $(".selected-step[data-tab-name='" + next_tab_name + "']").addClass('active');
        $(".selected-step[data-tab-name='" + next_tab_name + "']").parent().addClass('active');
    }

	function previousTWTWTab(thisitem) {
		var next_tab_name = $.trim($(thisitem).attr("data-tab-name"));
		//console.log("next_tab_name = " + next_tab_name );
		var active = $(document).find('.wizard .nav-tabs li.active');
		active.prev().removeClass('disabled');
		$(document).find('.wizard .nav-tabs li.active').prev().addClass('active')
		//$(document).find('.wizard .nav-tabs li.active').prev().find('.next-screen').addClass('active')
        active.removeClass('active');
        //active.next().addClass('active');
		$(".tab-content").find(".tab-pane").removeClass('active');
        $(".tab-content").find("[data-tab-name='" + next_tab_name + "']").addClass('active');
    }

	function employeeCodeInfo(thisitem){
		var auto_calculate_employee_code = $.trim($("[name=auto_calculate_employee_code]:checked").val())
		
		if(auto_calculate_employee_code != "" && auto_calculate_employee_code != null  && auto_calculate_employee_code == "{{config('constants.SELECTION_YES')}}"){
			$("[name='employee_code']").attr('disabled','disabled');
			$("[name='employee_code']").prop('readonly',true);
		}else{
			 $("[name='employee_code']").removeAttr('disabled');
			 $("[name='employee_code']").prop('readonly',false);
			 $.trim($("[name=employee_code]").val(''))
		 }
		 
		if(auto_calculate_employee_code != "" && auto_calculate_employee_code != null  && auto_calculate_employee_code == "{{config('constants.SELECTION_YES')}}"){
			$.ajax({
				url:employee_module_url + 'getEmployeeCodeDetails',
				type: 'POST',
				dataType :'json',
				data: {
					'auto_calculate_employee_code': auto_calculate_employee_code,
					"_token":"{{csrf_token()}}"
				},
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if(response !="" && response != null){
						var employee_code = ((response.data.recordInfo !="" && response.data.recordInfo != null) ? response.data.recordInfo :'');
						$.trim($("[name=employee_code]").val(employee_code))
					}else{
						$.trim($("[name=employee_code]").val(''))
						
					}
							
				},
				error: function(errorResponse) {
					hideLoader();
				}
			});
		} 
	}
	function basicFormValidationDetails(thisitem){
		var all_validation_fields = [ 'employee_code' , 'employee_name' ,'full_name','gender' ,'blood_group' , 'date_of_birth' ,'outlook_email_id','personal_email_id','contact_number' ,'education' , 'cgpa_percentage' ] ;
		//var all_validation_fields = [ 'employee_code' , 'employee_name' ,'full_name' ] ;
		var tab_validation = true;
	    $(all_validation_fields).each(function(i, v){
	        if( ( $("[name='"+v+"']").valid() == false ) && ( tab_validation != false )  ){
	            tab_validation = false;
	        }
	    });
		var formData = new FormData( $('#add-employee-master-form')[0] );
		var emp_active_tab_name = $(".step-one.active").attr('data-tab-name');
		//console.log();
		//console.log("tab_validation  = " + tab_validation );
		if( tab_validation != false ){
			/* if( emp_active_tab_name != "" && emp_active_tab_name != null && emp_active_tab_name != "step2"){ */
				nextTWTWTab(thisitem);
			/* } */
			
	    }    
    }
	

	function countryMasterInfo(thisitem){
		var state_record_id = $.trim($("[name='current_state']").find('option:selected').attr('data-state-record-id'));
		var state_permanent_record_id = $.trim($("[name='per_state']").find('option:selected').attr('data-state-record-id'));
		
		if(state_record_id != "" && state_record_id != null  || (state_permanent_record_id != "" && state_permanent_record_id != null) ){
			$("[name='current_country'] option[data-country-record-id ='" + state_record_id + "']").prop("selected", true).trigger('change');
			$("[name='per_country'] option[data-country-record-id ='" + state_permanent_record_id + "']").prop("selected", true).trigger('change');
		}
	}
	function stateMasterInfo(thisitem){
		var cur_state_id = $.trim($("[name='current_city']").find('option:selected').attr('data-cur-state-id'));
		var per_state_id = $.trim($("[name='per_city']").find('option:selected').attr('data-cur-state-id'));
		
		var cur_country_id = $.trim($("[name='current_city']").find('option:selected').attr('data-cur-country-id'));
		var per_country_id = $.trim($("[name='per_city']").find('option:selected').attr('data-cur-country-id'));

		if(cur_state_id !="" && cur_state_id  != null){
			$("[name='current_state'] option[data-state-id='" + cur_state_id + "']").prop("selected", true).trigger('change');
		} else {
			$("[name='current_state']").val("");
		}
		if( per_state_id !="" && per_state_id  != null ){
			$("[name='per_state'] option[data-per-state-id='" + per_state_id + "']").prop("selected", true).trigger('change');
		} else {
			$("[name='per_state']").val("");
		}
		if(cur_country_id !="" && cur_country_id  != null){
			$("[name='current_country'] option[data-country-record-id ='" + cur_country_id + "']").prop("selected", true).trigger('change');
		} else {
			$("[name='current_country']").val("");
		}
		if(per_country_id !="" && per_country_id  != null){
			$("[name='per_country'] option[data-country-record-id ='" + per_country_id + "']").prop("selected", true).trigger('change');
		} else {
			$("[name='per_country']").val("");
		}
		

	 }
	function addressIdentityDetails(thisitem){
		var all_validation_fields = [ 'address_line_1' , 'address_line_2' ,'current_city','current_state' ,'current_country' , 'pincode' ,'aadhaar_number' ,'pan_number' ,'address_permanent_line_1' ,'address_permanent_line_2','per_city','per_state','per_country','pincode_permanent','current_village','permanent_village'] ;
		//var all_validation_fields = [ 'address_line_1' , 'address_line_2'  ] ;
		var tab_validation = true;
	    $(all_validation_fields).each(function(i, v){
	        if( ( $("[name='"+v+"']").valid() == false ) && ( tab_validation != false )  ){
	            tab_validation = false;
	        }
	    });
	    //console.log("tab_validation");
	   // console.log(tab_validation);
	    var emp_active_tab_name = $(".step-one.active").attr('data-tab-name');
	    var formData = new FormData( $('#add-employee-master-form')[0] );
		if( tab_validation != false ){
			/* if( emp_active_tab_name != "" && emp_active_tab_name != null && emp_active_tab_name != "step3"){ */
				nextTWTWTab(thisitem);
			/* } */
	    } else {
			//$(".tab-next-btn:visible").click();
			//$(".tab-next-btn:visible").click();
		}  
    }

   function copyAddressInfo(thisitem){
		var same_current_address = $.trim($("[name=same_current_address]:checked").val())
		var village = $.trim($("[name='current_village']").find('option:selected').attr('data-village-id'));
		var city = $.trim($("[name='current_city']").find('option:selected').attr('data-city-id'));
		var state = $.trim($("[name='current_state']").find('option:selected').attr('data-state-id'));
		var country = $.trim($("[name='current_country']").find('option:selected').attr('data-country-id'));
		var address_line_1 = $.trim($("[name=address_line_1]").val())
		var pincode = $.trim($("[name=pincode]").val())
		var address_line_2 = $.trim($("[name=address_line_2]").val())
		
		if(same_current_address != "" && same_current_address != null  && same_current_address == "{{config('constants.SELECTION_YES')}}"){
			$("[name='address_permanent_line_1']").val(address_line_1); 
			$("[name='address_permanent_line_2']").val(address_line_2);
			$("[name='pincode_permanent']").val(pincode);
			$('[name="per_city"] option[data-city-id="' + city + '"]').prop("selected", true).trigger('change');
			$('[name="per_state"] option[data-per-state-id="' + state + '"]').prop("selected", true).trigger('change');
			$('[name="per_country"] option[data-country-id="' + country + '"]').prop("selected", true).trigger('change');
			$('[name="permanent_village"] option[data-village-id="' + village + '"]').prop("selected", true).trigger('change');
			($("[name='per_city']")).attr('disabled','disabled');
		} 
		<?php /*?>
		else{
			//console.log('hii hello');
			$("[name='address_permanent_line_1']").val(''); 
			$("[name='address_permanent_line_2']").val('');
			$("[name='pincode_permanent']").val('');
			$("[name='per_city']").val("").trigger('change');
			$("[name='per_state']").val("").trigger('change');
			$("[name='per_country']").val("").trigger('change');
			$("[name='permanent_village']").val("").trigger('change');
			$("[name='per_city']").removeAttr('disabled');
	 }
	 <?php */?>
   }
	function jobFormValidationDetails(thisitem){
		var all_validation_fields = [ 'joining_date' , 'week_off_effective_date' ,  'designation' ,'team','leader_name_reporting_manager' ,'recruitment_source' ,'reference_name', 'shift','new_weekly_off','probation_period' ,'notice_period'] ;
		//var all_validation_fields = [ 'joining_date' , 'designation' ,'team' ] ;
		var tab_validation = true;
	    $(all_validation_fields).each(function(i, v){
	        if( ( $("[name='"+v+"']").valid() == false ) && ( tab_validation != false )  ){
	            tab_validation = false;
	        }
	    });
	    var formData = new FormData( $('#add-employee-master-form')[0] );
		if( tab_validation != false ){
			nextTWTWTab(thisitem);
			var selected_yes_value = "{{ config('constants.SELECTION_YES') }}"; 
			$("[name='assign_salary_employee'][value='" + selected_yes_value + "']").trigger('click');
			$("[name='hold_salary'][value='" + selected_yes_value + "']").trigger('click');
		}  
	 }

	 function salaryFormValidationDetails(thisitem){
			var all_validation_fields = [ 'bank_name' , 'account_number' ,'ifsc_code','uan_number' ,'assign_salary_employee' , 'salary_group' ] ;
			var tab_validation = true;
		    $(all_validation_fields).each(function(i, v){
		        if( ( $("[name='"+v+"']").valid() == false ) && ( tab_validation != false )  ){
		            tab_validation = false;
		        }
		    });

		    var formData = new FormData( $('#add-employee-master-form')[0] );

			if( tab_validation != false ){

				var assign_salary_employee = $.trim($("[name='assign_salary_employee']:checked").val());

			    if( assign_salary_employee == "{{ config('constants.SELECTION_YES') }}" ){
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

					var hold_salary = $.trim($("[name='hold_salary']:checked").val());

				    if( hold_salary == "{{ config('constants.SELECTION_YES') }}" ){
					    var hold_salary_month_value = false;
						$(".hold-salary-amount").each(function(){
							if(parseFloat($(this).val()) > 0.00 ){
								hold_salary_month_value = true;
							}
						})
						if( hold_salary_month_value != true ){
							alertifyMessage('error' , '{{ trans("messages.error-atleast-one-hold-salary-value") }}');
							return false
						}
					}
				}
				
				nextTWTWTab(thisitem);
			 }  
	 }

	 function assetFormValidationDetails(thisitem){
		var formData = new FormData( $('#add-employee-master-form')[0] );
		
		alertify.confirm("{{ trans('messages.add-employee') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-employee')]) }}",function() { 
			$("#add-employee-master-form").submit();
		}, function () { });
	 }

	function employeeRecruitmentSourceInfo(thisitem){
		 var recruitment_source = $.trim($("[name='recruitment_source']").find('option:selected').attr('data-recruitment-source'));
		 if( recruitment_source != "" && recruitment_source != null && recruitment_source == "{{config('constants.EMPLOYEE_RECRUITMENT_SOURCE_ID')}}"){
			$('.recruitment-reference-master-div').show();
		} else {
			$('.recruitment-reference-master-div').hide();
		}
	 }

	$("[name='hold_salary']").on('click', function(){
		var selected_value = $.trim($(this).val());	
		//console.log("selected_value = " + selected_value );
		if( selected_value != "" && selected_value != null && selected_value == "{{ config('constants.SELECTION_YES') }}"){
			$(".hold-salary-info-div").show();
		} else {
			$(".hold-salary-info-div").hide();
		}
	 });

	 function calculateHoldSalary(){
		var hold_salary_total = 0;
		$(".hold-salary-amount").each(function(){
			var hold_salary = $.trim($(this).val());
			if( parseFloat(hold_salary)  > 0.00 ){
				hold_salary_total = ( parseFloat(hold_salary_total) + parseFloat(hold_salary) );
			}
		})
		$(".total-hold-salary-amount").html(displayValueIntoIndianCurrency(hold_salary_total));
	 }

	 function getSalaryGroupDetail(){

		var deduction_employer_from_employee = $.trim($("[name='deduction_employer_from_employee']:checked").val());
		//console.log("deduction_employer_from_employee = " + deduction_employer_from_employee );
		if( deduction_employer_from_employee != "" && deduction_employer_from_employee != null ){
			$.ajax({
				type: "POST",
				url: '{{ config("constants.EMPLOYEE_MASTER_URL") }}' + '/getSalaryGroup',
				data: { 
					'deduction_employer_from_employee':deduction_employer_from_employee,
				},
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response != "" && response != null ){
						var previous_value = $.trim($("[name='salary_group']").find('option:selected').attr('data-id'));
						$("[name='salary_group']").html(response);
						if( previous_value != "" && previous_value != null ){
							$("[name='salary_group'] option[data-id='" + previous_value + "']").prop("selected", true);
						}
					}
				},
				error: function() {
					hideLoader();
				}
			});
		} else {
			$("[name='salary_group']").html("");
		}
	}

	$("[name='assign_salary_employee']").on('click' , function(){
		var assign_salary_employee = $.trim($("[name='assign_salary_employee']:checked").val());

		if( assign_salary_employee != "" && assign_salary_employee != null && assign_salary_employee == "{{ config('constants.SELECTION_YES') }}" ){
			$(".assign-salary").show();
			var hold_salary = $.trim($("[name='hold_salary']:checked").val());
			$("[name='hold_salary'][value='" + hold_salary + "']").trigger('click');
		} else {
			$(".assign-salary").hide();
			$(".hold-salary-info-div").hide();
		}
		
	})	
	
	

	//$("[name='joining_date']").datetimepicker().on('dp.change', function(e) {
	$("[name='joining_date']").on('blur', function(e) {
		var selected_date = $.trim($(this).val());
		if( selected_date != "" && selected_date != null ){
			var joining_date = moment(selected_date, 'DD-MM-YYYY').format("YYYY-MM-DD");
			var selected_month = moment(selected_date, 'DD-MM-YYYY').month();
			var selected_year = moment(selected_date, 'DD-MM-YYYY').year();

			var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
			var all_months = [];
			var today = new Date(joining_date);
			var d;
			var month;
			
			for(var i = 0; i <= 5; i += 1) {
			  d = new Date(today.getFullYear(), today.getMonth() + i, 1);
			  month = monthNames[d.getMonth()];
			  all_months.push(month + ' - ' + d.getFullYear() );
			}
			$(".hold-month").each(function(index,value){
				$(this).html( ( ( all_months[index] != "" && all_months[index] != null  ) ? all_months[index] : ""  ) );
			});
			
		}
	});

	$(".selected-step").on('click' , function(){

	});

	// Dynamic Sub-Designation: fetch when Designation changes and on initial load
	$(document).on('change', "[name='designation']", function(){
		var designation = $.trim($(this).val());
		var $subDesig = $("[name='sub_designation']");
		$subDesig.html('<option value="">{{ trans('messages.select') }}</option>').trigger('change');
		if(designation !== ''){
			$.ajax({
				type: 'POST',
				url: employee_module_url + 'getSubDesignationsByDesignation',
				dataType: 'json',
				data: { designation: designation, _token: "{{ csrf_token() }}" },
				beforeSend: function(){ showLoader(); },
				success: function(response){
					hideLoader();
					if(response && response.html){
						$subDesig.html(response.html).trigger('change');
					}
				},
				error: function(){ hideLoader(); }
			});
		}
	});

	// Initial load for edit case
	$(function(){
		var preSelectedDesignation = $.trim($("[name='designation']").val());
		if(preSelectedDesignation !== ''){
			$("[name='designation']").trigger('change');
		}
	});

</script>
@include( config('constants.ADMIN_FOLDER') . 'salary-calculation')
@endsection