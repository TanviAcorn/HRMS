<div class="primary-details h-100">
    <div class="card card-display border-0 px-2 h-100">
        <div class="card-body px-2 py-0">
            <div class="row px-0 border-bottom">
                <div class="col-12 profile-details-title-card">
                    <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.primary-details") }}</h5> 
                    @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) || (  (isset($employeeRecordInfo->i_login_id) && ( $employeeRecordInfo->i_login_id == session()->get('user_id') ) )  ) )
                    	<a href="javascript:void(0);" data-emplyee-id="{{ (!empty($employeeRecordInfo->i_id) ? Wild_tiger::encode($employeeRecordInfo->i_id) : 0 ) }}" onclick="openPrimaryDetailModel(this)"; title="{{ trans('messages.edit') }}">{{ trans("messages.edit") }}</a>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-12 py-0 profile-display-card">
                	<div class="row pb-0 pt-3 employee-primary-details">
						@include(config('constants.AJAX_VIEW_FOLDER') .'employee-master/primary-details-list')
                  </div>  
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade document-folder" id="primary-details-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.primary-details") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
             {!! Form::open(array( 'id '=> 'add-primary-details-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body add-primary-details-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                	<input type="hidden" name="employee_record_id" value="">
                    <button type="button" onclick="addPrimaryDetails()"  class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
           {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    $("#add-primary-details-form").validate({
        errorClass: "invalid-input",
        rules: {
            date_of_birth: {
                required: true,
                noSpace: true
            },
            employee_name: {
                required: true,
                noSpace: true
            },
            full_name: {
                required: true,
                noSpace: true
            },
            gender: {
                required: true,
                noSpace: true
            },
            education: {required: true , noSpace: true },
            cgpa_percentage: {required: true , noSpace: true },
        },
        messages: {
            date_of_birth: {
                required: "{{ trans('messages.require-enter-date-of-birth') }}"
            },
            employee_name: {
                required: "{{ trans('messages.require-employee-name') }}"
            },
            full_name: {
                required: "{{ trans('messages.require-full-name') }}"
            },
            gender: {
                required: "{{ trans('messages.require-select-gender') }}"
            },
            education: {required: "{{ trans('messages.require-education') }}" },
            cgpa_percentage: {required: "{{ trans('messages.require-cgpa-percentage') }}" },
        }
    });
    var employee_module_url = '{{config("constants.EMPLOYEE_MASTER_URL")}}' + '/';
   
    function openPrimaryDetailModel(thisitem){
    	
    	var employee_id = $.trim($(thisitem).attr('data-emplyee-id'));
        $("[name='employee_record_id']").val(employee_id);
		
        $.ajax({
    		type: "POST",
    		url: employee_module_url + 'editPrimaryDetail',
    		data: {
    			"_token": "{{ csrf_token() }}",'employee_id':employee_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			$('.add-primary-details-html').html(response);
    			openBootstrapModal('primary-details-model');
    		
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
    function addPrimaryDetails(){
    	if($('#add-primary-details-form').valid() != true){
    		return false;
    	}
    	var employee_record_id = $.trim($("[name='employee_record_id']").val());
    	var employee_name = $.trim($("[name='employee_name']").val());
    	var full_name = $.trim($("[name='full_name']").val());
    	var gender = $.trim($("[name='gender']").val());
    	var blood_group = $.trim($("[name='blood_group']").val());
    	var marital_status = $.trim($("[name='marital_status']").val());
    	var education = $.trim($("[name='education']").val());
    	var cgpa_percentage = $.trim($("[name='cgpa_percentage']").val());
    	var date_of_birth = $.trim($("[name='date_of_birth']").val());
    	
    	alertify.confirm("{{ trans('messages.update-primary-details') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-primary-details')]) }}",function() { 
	    	 $.ajax({
	     		type: "POST",
	     		dataType :'json',
	     		url: employee_module_url + 'addPrimaryDetails',
	     		data:{
					"_token": "{{ csrf_token() }}",
					'employee_record_id':employee_record_id,
					'employee_name':employee_name,
					'full_name':full_name,
					'gender':gender,
					'blood_group':blood_group,
					'marital_status':marital_status,
					'education':education,
					'cgpa_percentage':cgpa_percentage,
					'date_of_birth':date_of_birth,
					
					
				},
				beforeSend: function() {
	     			//block ui
	     			showLoader();
	     		},
	     		success: function(response) {
	     			hideLoader();
	     			if( response.status_code == 1 ){
						$("#primary-details-model").modal('hide');
						alertifyMessage('success',response.message);
						$('.employee-primary-details').html(response.data.primaryDetailsInfo);
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
    
</script>