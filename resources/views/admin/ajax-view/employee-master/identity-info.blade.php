<div class="identity-pf-account-information-edit h-100">
    <div class="card card-display border-0 px-2 h-100">
        <div class="card-body px-2 py-0">
            <div class="row px-0 border-bottom">
                <div class="col-12 profile-details-title-card">
                    <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.identity-pf-account-information") }}</h5>
                    	@if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )   
                    	<a href="javascript:void(0);" data-emplyee-id="{{ (!empty($employeeRecordInfo->i_id) ? Wild_tiger::encode($employeeRecordInfo->i_id) : 0 ) }}" onclick="openIdentityPfAccountModel(this)"; title="{{ trans('messages.edit') }}">
                    		{{ trans("messages.edit") }}
                    	</a>
                    	@endif
                </div>
            </div>
            <div class="row">
                <div class="col-12 py-0 profile-display-card">
                    <div class="row pb-0 pt-3 employee-identity-pf-account">
	                    <?php 
						$recordInfo['employeeRecordInfo'] = $employeeRecordInfo;
						$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/identity-list')->with ( $recordInfo )->render();
						echo $html;
						?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade document-folder" id="identity-pf-account-information-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.identity-pf-account-information") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'add-identity-pf-account-information-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body add-identity-pf-account-information-html">
                    
                </div>
                <div class="modal-footer justify-content-end">
                	<input type="hidden" name="employee_record_id" value="">
                    <button type="button" onclick="addIdentityPfAccountDetails()" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>


<script>
    $("#add-identity-pf-account-information-form").validate({
        errorClass: "invalid-input",
        rules: {
            aadhaar_number: {
                required: true,
                noSpace: true
            },

        },
        messages: {
            aadhaar_number: {
                required: "{{ trans('messages.require-enter-aadhaar-number') }}"
            },
        }
    });

    var employee_module_url = '{{config("constants.EMPLOYEE_MASTER_URL")}}' + '/';
    function openIdentityPfAccountModel(thisitem){
    	var employee_id = $.trim($(thisitem).attr('data-emplyee-id'));
        $("[name='employee_record_id']").val(employee_id);
		
        $.ajax({
    		type: "POST",
    		url: employee_module_url + 'editIdentityPfAccountDetails',
    		data: {
    			"_token": "{{ csrf_token() }}",'employee_id':employee_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			$('.add-identity-pf-account-information-html').html(response);
    			openBootstrapModal('identity-pf-account-information-model');
    		
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
        
    }
    function addIdentityPfAccountDetails(){
    	if($('#add-identity-pf-account-information-form').valid() != true){
    		return false;
    	}
    	var employee_record_id = $.trim($("[name='employee_record_id']").val());
    	var aadhaar_number = $.trim($("[name='aadhaar_number']").val());
    	var pan_no = $.trim($("[name='pan_no']").val());
    	var uan_number = $.trim($("[name='uan_number']").val());
    	
    	alertify.confirm("{{ trans('messages.update-identity-pf-account-information') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-identity-pf-account-information')]) }}",function() { 
	    	 $.ajax({
	     		type: "POST",
	     		dataType :'json',
	     		url: employee_module_url + 'addIdentityPfAccountDetails',
	     		data:{
					"_token": "{{ csrf_token() }}",
					'employee_record_id':employee_record_id,'aadhaar_number':aadhaar_number,
					'pan_no':pan_no,'uan_number':uan_number
					
				},
				beforeSend: function() {
	     			//block ui
	     			showLoader();
	     		},
	     		success: function(response) {
	     			hideLoader();
	     			if( response.status_code == 1 ){
						$("#identity-pf-account-information-model").modal('hide');
						alertifyMessage('success',response.message);
						$('.employee-identity-pf-account').html(response.data.html);
						
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