<div class="bank-details-edit h-100">
    <div class="card card-display border-0 px-2 h-100">
        <div class="card-body px-2 py-0">
            <div class="row px-0 border-bottom">
                <div class="col-12 profile-details-title-card">
                    <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.bank-details") }}</h5>
                    	@if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ) 
                    	<a href="javascript:void(0);" data-emplyee-id="{{ (!empty($employeeRecordInfo->i_id) ? Wild_tiger::encode($employeeRecordInfo->i_id) : 0 ) }}" onclick="openBankDetailsModel(this)"; title="{{ trans('messages.edit') }}">
                    		{{ trans("messages.edit") }}
                    	</a>
                    	@endif
                </div>
            </div>
            <div class="row">
                <div class="col-12 py-0 profile-display-card">
                    <div class="row pb-0 pt-3 employee-bank-details">
                        <?php 
						$recordInfo['employeeRecordInfo'] = $employeeRecordInfo;
						$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/bank-details-list')->with ( $recordInfo )->render();
						echo $html;
						?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade document-folder" id="bank-details-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.bank-details") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'add-bank-details-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body add-bank-details-html">
                   
                </div>
                <div class="modal-footer justify-content-end">
                	<input type="hidden" name="employee_record_id" value="">
                    <button type="button" onclick="addBankDetails()" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    $(function() {
        $('.primary-bd').datetimepicker({
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

    function openBankDetailsModel(thisitem){
    	var employee_id = $.trim($(thisitem).attr('data-emplyee-id'));
        $("[name='employee_record_id']").val(employee_id);
		
        $.ajax({
    		type: "POST",
    		url: employee_module_url + 'editBankDetails',
    		data: {
    			"_token": "{{ csrf_token() }}",'employee_id':employee_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			$('.add-bank-details-html').html(response);
    			openBootstrapModal('bank-details-model');
    		
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
    function addBankDetails(){
    	var employee_record_id = $.trim($("[name='employee_record_id']").val());
    	var bank_name = $.trim($("[name='bank_name']").val());
    	var account_number = $.trim($("[name='account_number']").val());
    	var ifsc_code = $.trim($("[name='ifsc_code']").val());
    	
    	alertify.confirm("{{ trans('messages.update-bank-details') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-bank-details')]) }}",function() { 
	    	 $.ajax({
	     		type: "POST",
	     		dataType :'json',
	     		url: employee_module_url + 'addBankDetails',
	     		data:{
					"_token": "{{ csrf_token() }}",
					'employee_record_id':employee_record_id,'bank_name':bank_name,
					'account_number':account_number,'ifsc_code':ifsc_code
					
				},
				beforeSend: function() {
	     			//block ui
	     			showLoader();
	     		},
	     		success: function(response) {
	     			hideLoader();
	     			if( response.status_code == 1 ){
						$("#bank-details-model").modal('hide');
						alertifyMessage('success',response.message);
						$('.employee-bank-details').html(response.data.html);
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

