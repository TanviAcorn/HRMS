
<div class="address-details h-100">
    <div class="card card-display border-0 px-2 h-100">
        <div class="card-body px-2 py-0">
            <div class="row px-0 border-bottom">
                <div class="col-12 profile-details-title-card">
                    <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.address") }}</h5>
                    	@if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )  
                    	<a href="javascript:void(0);" data-emplyee-id="{{ (!empty($employeeRecordInfo->i_id) ? Wild_tiger::encode($employeeRecordInfo->i_id) : 0 ) }}" onclick="openAddressModel(this)";title="{{ trans('messages.edit') }}">{{ trans("messages.edit") }}</a>
                    	@endif
                </div>
            </div>
            <div class="row">
                <div class="col-12 py-0 profile-display-card">
                    <div class="row pb-0 pt-3 employee-address-record-details">
                        <?php /*
                        $recordInfo['employeeRecordInfo'] = $employeeRecordInfo;
                        $html = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/address-list')->with ( $recordInfo )->render();
                        echo $html;
                        */ ?>
                        @include(config('constants.AJAX_VIEW_FOLDER') .'employee-master/address-list')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade document-folder" id="address-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.address") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'add-address-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body add-address-model-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                	<input type="hidden" name="employee_record_id" value="">
                    <button type="button" onclick="addAddressModel()" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
	$(document).on('select2:close', '.select2', function (e) {
		var evt = "scroll.select2"
		$(e.target).parents().off(evt)
		$(window).off(evt)
	})
</script>

<script>
    
    var employee_module_url = '{{config("constants.EMPLOYEE_MASTER_URL")}}' + '/';
    function openAddressModel(thisitem){
    	var employee_id = $.trim($(thisitem).attr('data-emplyee-id'));
        $("[name='employee_record_id']").val(employee_id);
		
        $.ajax({
    		type: "POST",
    		url: employee_module_url + 'editAddressModel',
    		data: {
    			"_token": "{{ csrf_token() }}",'employee_id':employee_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			$('.add-address-model-html').html(response);
    			getLocationDetails();
    			openBootstrapModal('address-model');
    			$(function(){
    				$('.select2').select2();
    			})
    			$("#add-address-form").validate({
    		        errorClass: "invalid-input",
    		        rules: {
    		            address_line_1: {
    		                required: true, noSpace: true
    		            },
    		            current_city: {
    		                required: true, noSpace: true
    		            },
    		            current_state: {
    		                required: true, noSpace: true
    		            },
    		            current_country: {
    		                required: true, noSpace: true
    		            },
    		            per_address_line_1: {
    		                required: true, noSpace: true
    		            },
    		            per_city: {
    		                required: true, noSpace: true
    		            },
    		            per_state: {
    		                required: true, noSpace: true
    		            },
    		            per_country: {
    		                required: true, noSpace: true
    		            },
    		        },
    		        messages: {
    		            address_line_1: {
    		                required: "{{ trans('messages.require-enter-address-line-1') }}"
    		            },
    		            current_city: {
    		                required: "{{ trans('messages.require-select-city') }}"
    		            },
    		            current_state: {
    		                required: "{{ trans('messages.require-select-state') }}"
    		            },
    		            current_country: {
    		                required: "{{ trans('messages.require-select-country') }}"
    		            },
    		            per_address_line_1: {
    		                required: "{{ trans('messages.require-enter-address-line-1') }}"
    		            },
    		            per_city: {
    		                required: "{{ trans('messages.require-select-city') }}"
    		            },
    		            per_state: {
    		                required: "{{ trans('messages.require-select-state') }}"
    		            },
    		            per_country: {
    		                required: "{{ trans('messages.require-select-country') }}"
    		            },
    		        }
    		    });
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
        
    }
    
    function addAddressModel(){

        $(".validate-disable-field").removeAttr("disabled");
    	if($('#add-address-form').valid() != true){
    		$(".validate-disable-field").attr("disabled","disabled");
    		return false;
    	}
    	$(".validate-disable-field").attr("disabled","disabled");
    	var employee_record_id = $.trim($("[name='employee_record_id']").val());
    	var address_line_1 = $.trim($("[name='address_line_1']").val());
		var address_line_2 = $.trim($("[name='address_line_2']").val());
		var current_city = $.trim($("[name='current_city']").val());
		var current_state = $.trim($("[name='current_state']").val());
		var current_country = $.trim($("[name='current_country']").val());
		var current_pincode = $.trim($("[name='current_pincode']").val());
		var per_address_line_1 = $.trim($("[name='per_address_line_1']").val());
		var per_address_line_2 = $.trim($("[name='per_address_line_2']").val());
		var per_city = $.trim($("[name='per_city']").val());
		var per_state = $.trim($("[name='per_state']").val());
		var per_country = $.trim($("[name='per_country']").val());
		var per_pincode = $.trim($("[name='per_pincode']").val());
		var same_current_address = $.trim($("[name='same_current_address']:checked").val());

		var current_village = $.trim($("[name='current_village']").val());
		var permanent_village = $.trim($("[name='permanent_village']").val());
		
		alertify.confirm("{{ trans('messages.update-address') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-address')]) }}",function() { 
	    	 $.ajax({
	     		type: "POST",
	     		dataType :'json',
	     		url: employee_module_url + 'addAddressDetails',
	     		data:{
					"_token": "{{ csrf_token() }}",
					'employee_record_id':employee_record_id,'address_line_1':address_line_1,
					'address_line_2':address_line_2,'current_city':current_city,
					'current_state':current_state,'current_country':current_country,'current_pincode':current_pincode,
					'per_address_line_1':per_address_line_1,'per_address_line_2':per_address_line_2,'per_city':per_city,
					'per_state':per_state,'per_country':per_country,'per_pincode':per_pincode ,'permanent_village':permanent_village,'current_village':current_village,'same_current_address':same_current_address
					
				},
				beforeSend: function() {
	     			//block ui
	     			showLoader();
	     		},
	     		success: function(response) {
	     			hideLoader();
	     			if( response.status_code == 1 ){
						$("#address-model").modal('hide');
						alertifyMessage('success',response.message);
						$('.employee-address-record-details').html(response.data.addressRecordInfo);
						$('.employee-profile-pic-view--master-div-html').html(response.data.mainProfileInfo);
						
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
    function stateRecordInfo(thisitem){
    	var current_state_record_id = $.trim($("[name='current_city']").find('option:selected').attr('data-cur-state-id'));
    	var per_state_record_id = $.trim($("[name='per_city']").find('option:selected').attr('data-cur-state-id'));
    		
    	if((current_state_record_id != "" && current_state_record_id != null)){
    		$("[name='current_state'] option[data-state-id='" + current_state_record_id + "']").prop("selected", true).trigger('change');
    	} else {
    		$("[name='current_state']").val("");
    	}
    	if(per_state_record_id != "" && per_state_record_id != null){
    		$("[name='per_state'] option[data-per-state-id='" + per_state_record_id + "']").prop("selected", true).trigger('change');
    	} else {
    		$("[name='per_state']").val("");
    	}
		
    }
    function countryMasterInfo(thisitem){
        
    	var current_country_record_id = $.trim($("[name='current_state']").find('option:selected').attr('data-current-country-record-id'));
    	var per_country_record_id = $.trim($("[name='per_state']").find('option:selected').attr('data-per-country-record-id'));

    	if(current_country_record_id != "" && current_country_record_id != null){
    		$("[name='current_country'] option[data-current-country-id='" + current_country_record_id + "']").prop("selected", true).trigger('change');
    	}else {
    		$("[name='current_country']").val("");
    	}

    	if(per_country_record_id != "" && per_country_record_id != null){
    		$("[name='per_country'] option[data-per-country-id='" + per_country_record_id + "']").prop("selected", true).trigger('change');
    	}else {
    		$("[name='per_country']").val("");
    	}
    }

  
</script>