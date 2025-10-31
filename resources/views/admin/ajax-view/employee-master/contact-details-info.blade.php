<div class="contact-details h-100">
    <div class="card card-display border-0 px-2 h-100">
        <div class="card-body px-2 py-0">
            
            <div class="row px-0 border-bottom">
                <div class="col-12 profile-details-title-card">
                    <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.contact-details") }}</h5> 
	                    @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) || (  (isset($employeeRecordInfo->i_login_id) && ( $employeeRecordInfo->i_login_id == session()->get('user_id') ) )  ) )
	                    <a href="javascript:void(0);" data-emplyee-id="{{ (!empty($employeeRecordInfo->i_id) ? Wild_tiger::encode($employeeRecordInfo->i_id) : 0 ) }}" onclick="openContactDetailsModel(this)"; title="{{ trans('messages.edit') }}">{{ trans("messages.edit") }}</a>
	                    @endif
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 py-0 profile-display-card employee-contact-details">
                    @include(config('constants.AJAX_VIEW_FOLDER') .'employee-master/contact-details-list')
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade document-folder" id="contact-details-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.contact-details") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'add-contact-details-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body add-contact-details-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                	<input type="hidden" name="employee_record_id" value="">
                    <button type="button" onclick="addContactDetails()" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    $("#add-contact-details-form").validate({
        errorClass: "invalid-input",
        rules: {
        	outlook_email_id : {
        		email_regex : true,
            },
            personal_email_id: {
                required: true,
                email_regex : true,
                noSpace: true
            },
            contact_number: {
                required: true,
                noSpace: true
            },
        },
        messages: {
            personal_email_id: {
                required: "{{ trans('messages.require-enter-personal-email-id') }}"
            },
            contact_number: {
                required: "{{ trans('messages.require-enter-contact-number') }}"
            },
        }
    });
    var employee_module_url = '{{config("constants.EMPLOYEE_MASTER_URL")}}' + '/';
    function openContactDetailsModel(thisitem){
    	var employee_id = $.trim($(thisitem).attr('data-emplyee-id'));
        $("[name='employee_record_id']").val(employee_id);
		
        $.ajax({
    		type: "POST",
    		url: employee_module_url + 'editContactDetails',
    		data: {
    			"_token": "{{ csrf_token() }}",'employee_id':employee_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			$('.add-contact-details-html').html(response);
    			openBootstrapModal('contact-details-model');
    		
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});

     }

    function addContactDetails(){
    	if($('#add-contact-details-form').valid() != true){
    		return false;
    	}
    	var employee_record_id = $.trim($("[name='employee_record_id']").val());
    	var outlook_email_id = $.trim($("[name='outlook_email_id']").val());
    	var personal_email_id = $.trim($("[name='personal_email_id']").val());
    	var contact_number = $.trim($("[name='contact_number']").val());
    	var person_name = $.trim($("[name='person_name']").val());
    	var person_relation = $.trim($("[name='person_relation']").val());
    	var person_no = $.trim($("[name='person_no']").val());

    	alertify.confirm("{{ trans('messages.update-contact-details') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-contact-details')]) }}",function() { 
	    	 $.ajax({
	     		type: "POST",
	     		dataType :'json',
	     		url: employee_module_url + 'addContactDetails',
	     		data:{
					"_token": "{{ csrf_token() }}",
					'employee_record_id':employee_record_id,'outlook_email_id':outlook_email_id,
					'personal_email_id':personal_email_id,'contact_number':contact_number,
					'person_name':person_name,'person_relation':person_relation,'person_no':person_no,
					
				},
				beforeSend: function() {
	     			//block ui
	     			showLoader();
	     		},
	     		success: function(response) {
	     			hideLoader();
	     			if( response.status_code == 1 ){
						$("#contact-details-model").modal('hide');
						alertifyMessage('success',response.message);
						$('.employee-contact-details').html(response.data.contactDetailsInfo);
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