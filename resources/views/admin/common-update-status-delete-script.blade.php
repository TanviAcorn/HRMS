<div class="modal fade document-folder" id="add-leave-balance" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title add-leave-balance-title" id="exampleModalLabel">{{ trans("messages.add-earned-leave") }} <span class="twt-custom-modal-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'add-leave-balance-form' , 'method' => 'post'  )) !!}
            	<div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="no_of_earned_leaves" class="control-label">{{ trans('messages.no-of-leaves') }}<span class="star">*</span></label>
                                <input type="text" class="form-control" onkeyup="onlyNumberWithMinus(this);"  onchange="onlyNumberWithMinus(this);"  name="no_of_earned_leaves" placeholder="{{ trans('messages.no-of-leaves') }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="effective_from_earned" class="control-label">{{ trans('messages.effective-from') }}<span class="star">*</span></label>
                                <input type="text" class="form-control" name="effective_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="remarks_earned" class="control-label">{{ trans('messages.remarks') }}<span class="star">*</span></label>
                                <textarea name="leave_balance_remark" cols="30" rows="2" class="form-control" placeholder="{{ trans('messages.remarks') }}"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="add_balance_leave_type_id" value="">
                <input type="hidden" name="add_balance_employee_id" value="">
                <div class="modal-footer justify-content-end">
                    <button type="button" onclick="addLeaveBalance();" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<div class="modal fade document-folder" id="leave-balance-history" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title leave-balance-history-title" id="exampleModalLabel">{{ trans("messages.carry-forward-leave-history") }} <span class="twt-custom-modal-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body overflow-hidden">
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-sm table-hover table-bordered">
                            <thead>
                                <tr>
                                	<th style="min-width: 80px; text-align:center;width:80px">{{ trans('messages.application-date') }}</th>
                                    <th style="min-width: 130px;width:130px; text-align:left;">{{ trans('messages.requested-date') }}</th>
                                    
                                    <th style="min-width: 110px;text-align:left;width: 110px;">{{ trans('messages.change-days') }}</th>
                                    <th style="min-width: 110px;text-align:left;width: 110px;">{{ trans('messages.balance-days') }}</th>
                                    <th style="min-width: 150px;text-align:left;width:100px">{{ trans('messages.remarks') }}</th>
                                </tr>
                            </thead>
                            <tbody class="leave-balance-history-html">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade document-folder" id="paid_leave_history" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.paid-leave-history") }}</h5>
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
                                    <th style="min-width: 150px; text-align:center;">{{ trans('messages.date') }}</th>
                                    <th style="min-width: 150px;">{{ trans('messages.change-days') }}</th>
                                    <th style="min-width: 150px;">{{ trans('messages.balance-days') }}</th>
                                    <th style="min-width: 150px;">{{ trans('messages.remarks') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">17<sup>th</sup> dec, 1990</td>
                                    <td>-1</td>
                                    <td>0</td>
                                    <td>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Iure, dolore!</td>
                                </tr>
                                <tr>
                                    <td class="text-center">1<sup>th</sup> dec, 1990</td>
                                    <td>0</td>
                                    <td>1</td>
                                    <td>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Iure, dolore!</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade document-folder document-type" id="resign-approve-reject-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-reject-header-name" id="exampleModalLabel">{{ trans("messages.resign-approval") }}</h5>
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
                    <div class="modal-footer justify-content-end resign-approve-reject-btns">
                        <button type="button" onclick="updateResignStatus(this);" data-action="{{ config('constants.APPROVED_STATUS') }}"  class="btn bg-theme text-white action-button lookup-modal-action-button btn-add approve-button" title="{{ trans('messages.approve') }}">{{ trans('messages.approve') }}</button>
                        <button type="button" onclick="updateResignStatus(this);" data-action="{{ config('constants.REJECTED_STATUS') }}"  class="btn bg-theme text-white action-button lookup-modal-action-button btn-add reject-button" title="{{ trans('messages.reject') }}">{{ trans('messages.reject') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    
    <div class="modal fade document-folder calendar-style" id="employee-salary-view" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	     <div class="modal-dialog modal-xl modal-dialog-centered">
	         <div class="modal-content">
	             <div class="modal-header">
	                 <h5 class="modal-title" id="exampleModalLabel">{{ trans('messages.view-salary-for') }} <span class="twt-custom-modal-header">Deep Suthar (1232345)</span></h5>
	                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                     <span aria-hidden="true"><i class="fas fa-times"></i></span>
	                 </button>
	             </div>
	             <div class="modal-body overflow-hidden employee-salary-html">
	                 
	             </div>
	         </div>
	     </div>
	 </div>

{!! Form::open(array( 'id '=> 'delete-record-form' , 'method' => 'post' ,  'url' => 'removeRecord' )) !!}
	<input type="hidden" name="delete_record_id" value="">
	<input type="hidden" name="delete_module_name" value="">
{!! Form::close() !!}
<script>

function openAddLeaveBalanceModal(thisitem){
	current_selected_row = thisitem;
	var leave_type_id = $.trim($(thisitem).attr('data-leave-type-id'));
	var employee_id = $.trim($(thisitem).attr('data-emp-id'));

	//console.log("leave_type_id = " + leave_type_id );
		
	if( leave_type_id == "{{ config('constants.EARNED_LEAVE_TYPE_ID') }}"){
		$("#add-leave-balance").find(".add-leave-balance-title").html('{{ trans("messages.add-earned-leave") }}');
	} else if( leave_type_id == "{{ config('constants.CARRY_FORWARD_LEAVE_TYPE_ID') }}" ) {
		$("#add-leave-balance").find(".add-leave-balance-title").html('{{ trans("messages.add-carry-forward-leave") }}');
	}
	$("#add-leave-balance").find("[name='add_balance_leave_type_id']").val(leave_type_id);
	$("#add-leave-balance").find("[name='add_balance_employee_id']").val(employee_id);
	openBootstrapModal('add-leave-balance');
	$("[name='effective_date']").datetimepicker({
	    useCurrent: false,
	    viewMode: 'days',
	    ignoreReadonly: true,
	    format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
	    showClear: true,
	    showClose: true,
	    maxDate:moment().endOf('d'),
	    widgetPositioning: {
	        vertical: 'bottom',
	        horizontal: 'auto'

	    },
	    icons: {
	        clear: 'fa fa-trash',
	        Close: 'fa fa-trash',
	    },
	});

	var allowed_leave_min_date = getLeaveTimeOffMinDate();

	$("[name='effective_date']").data("DateTimePicker").minDate(moment(allowed_leave_min_date,'DD-MM-YYYY'));

}

function openLeaveBalanceModal(thisitem){

	var employee_id = $.trim($(thisitem).attr('data-emp-id'))
	var leave_type_id = $.trim($(thisitem).attr('data-leave-type-id'))
	var search_academic_year = $.trim($("[name='search_academic_year']").val()); 
	
	$.ajax({
		type: "POST",
		dataType: "json",
		url: site_url + 'leave/leave-type-history',
		data: { 
			'employee_id':employee_id,
			'leave_type_id':leave_type_id,
			'search_academic_year' : search_academic_year ,
		},
		beforeSend: function() {
			//block ui
			showLoader();
		},
		success: function(response) {
			hideLoader();
			if( response.status_code == 1 ){
				//console.log("leave_type_id = " + leave_type_id );
				
				if( leave_type_id == "{{ config('constants.EARNED_LEAVE_TYPE_ID') }}"){
					$("#leave-balance-history").find(".leave-balance-history-title").html('{{ trans("messages.earned-leave-history") }}');
				} else if( leave_type_id == "{{ config('constants.CARRY_FORWARD_LEAVE_TYPE_ID') }}" ) {
					$("#leave-balance-history").find(".leave-balance-history-title").html('{{ trans("messages.carry-forward-leave-history") }}');
				} else if( leave_type_id == "{{ config('constants.PAID_LEAVE_TYPE_ID') }}" ) {
					$("#leave-balance-history").find(".leave-balance-history-title").html('{{ trans("messages.paid-leave-history") }}');
				}  else if( leave_type_id == "{{ config('constants.UNPAID_LEAVE_TYPE_ID') }}" ) {
					$("#leave-balance-history").find(".leave-balance-history-title").html('{{ trans("messages.unpaid-leave-history") }}');
				}
				
				$(".leave-balance-history-html").html( response.data.html );
				openBootstrapModal('leave-balance-history');
			} else {
				alertifyMessage('error',response.message);
			}
			
		},
		error: function() {
			hideLoader();
		}
	});
	
	
}


$("#add-leave-balance-form").validate({
    errorClass: "invalid-input",
    rules: {
        no_of_earned_leaves: {
            required: true
        },
        effective_date: {
            required: true
        },
        leave_balance_remark: {
            required: true
        },
    },
    messages: {
        no_of_earned_leaves: {
            required: "{{ trans('messages.require-no-of-leaves') }}"
        },
        effective_date: {
            required: "{{ trans('messages.require-effective-date') }}"
        },
        leave_balance_remark: {
            required: "{{ trans('messages.require-remarks') }}"
        },
    },
});

function addLeaveBalance(){

	if( $("#add-leave-balance-form").valid() != true ){
		return false;
	}	

	var no_of_earned_leaves = $.trim($("[name='no_of_earned_leaves']").val());
	var leave_balance_remark = $.trim($("[name='leave_balance_remark']").val());
	var effective_date = $.trim($("[name='effective_date']").val());
	var leave_type_id = $.trim($("[name='add_balance_leave_type_id']").val());
	var employee_id = $.trim($("[name='add_balance_employee_id']").val());
	var search_academic_year = $.trim($("[name='search_academic_year']").val());

	//console.log("no_of_earned_leaves = "  + no_of_earned_leaves );

	if( no_of_earned_leaves != "" &&  no_of_earned_leaves != null ){
		if( ( no_of_earned_leaves % 0.5 == 0 ) == false ){
			alertifyMessage('error',"{{ trans('messages.invalid-leave-balance-value') }}");
			return false;
		}
	}
	
	alertify.confirm('{{ trans("messages.add-leave-balance")  }}', '{{ trans("messages.common-confirm-msg" , [ "module" => trans("messages.add-leave-balance") ] ) }}' , function(){
	
		$.ajax({
			type: "POST",
			dataType: "json",
			url: site_url + 'leave/addLeaveBalance',
			data: { 
				'no_of_earned_leaves':no_of_earned_leaves,
				'leave_balance_remark':leave_balance_remark,
				'effective_date':effective_date,
				'leave_type_id':leave_type_id,
				'employee_id':employee_id,
				'search_academic_year':search_academic_year,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if( response.status_code == 1 ){
					$("#add-leave-balance").modal('hide');
					var updated_html = response.data.html;
					var selected_leave_chart = $(current_selected_row).parents(".leave-type-count-chart-html");
					$(current_selected_row).parents(".leave-type-count-chart-html").html(updated_html);
					$(selected_leave_chart).find('.process-chart').trigger('change');
					alertifyMessage('success',response.message);
				} else {
					alertifyMessage('error',response.message);
				}
				
			},
			error: function() {
				hideLoader();
			}
		});

	}, function(){ });;
	

}


$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
function updateRecordStatus(thisitem , moduleName){
	//console.log(thisitem);
	var recordId = $(thisitem).attr("data-record-id");
	var	hitURL = site_url + moduleName + "/updateStatus";
	var	currentRow = $(thisitem);
	var	status = $(thisitem).parents('.status-class').find('.record-status').text();
	
	//temper module name for lookup_module
	
	if( moduleName == "{{ config('constants.LOOKUP_MODULE') }}" ){
		moduleName = $(thisitem).attr("data-another-module-name");
	}
	status = $.trim(status);
	var confirm_update_msg = '';
	if(status.toLowerCase() == '{{ strtolower(config("constants.ENABLE_STATUS")) }}'){
        doStatus = 'disable';
        confirm_update_msg = "{{ trans ( 'messages.update-status-msg', [ 'module' => trans('messages.disable') ] ) }}";
    } else if (status.toLowerCase() == '{{ strtolower(config("constants.DISABLE_STATUS")) }}'){
        doStatus = 'enable';
        confirm_update_msg = "{{ trans ( 'messages.update-status-msg', [ 'module' => trans('messages.enable') ] ) }}";
    } else {
    	alertifyMessage('error','{{ trans("messages.system-error") }}');
    }
	
	 
	moduleName = moduleName.replace(/_/g, ' ');
	
	alertify.confirm('{{ trans("messages.update-status") }}', confirm_update_msg , function(){	
		
		//ajax reqeust
	   jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : hitURL,
			data : { "_token": "{{ csrf_token() }}", 'record_id' : recordId , 'current_status' : status , 'lookup_module_name' : moduleName  },
			beforeSend: function() {
		        //block ui
				showLoader();
		    },success:function(response){
		    	hideLoader();
				if(response.status_code == 1) {
					if( $(document).find('.filter-button').length  > 0  ){
						filterData();
					}
					alertifyMessage('success',response.message);
					$(thisitem).parents('.status-class').find('.record-status' ).text(response.data.update_status) ;
				} else if(response.status_code == 101) {
					alertifyMessage('error',response.message);
				} else {
					alertifyMessage('error','{{ trans("messages.system-error") }}');
				}
		    },error:function(){
		    	
		    }
	   });
	}, function(){
		
		if(status.toLowerCase() == '{{ strtolower(config("constants.ENABLE_STATUS")) }}'){
			$(thisitem).prop('checked', true);
		} else if (status.toLowerCase() == '{{ strtolower(config("constants.DISABLE_STATUS")) }}'){
			$(thisitem).prop('checked', false);
		}
	});;
}

function deleteRecord(thisitem){

	var module_name = $.trim($(thisitem).attr('data-module-name'));

	
	alertify.confirm('{{ trans("messages.delete-record") }}', '{{ trans("messages.delete-record-msg") }}' , function () {
		
		var record_id = $.trim($(thisitem).attr('data-record-id'));
		var module_name = $.trim($(thisitem).attr('data-module-name'));

		if( module_name != '' && module_name != null && record_id != null &&  record_id != "" ){
			//console.log(module_name);
			if( module_name == "{{ config('constants.LOOKUP_MODULE') }}" ){
				$("[name='delete_module_name']").val( $.trim($(thisitem).attr('data-another-module-name')));
			} else {
				$("[name='delete_module_name']").val(module_name);
			}
			$("[name='delete_record_id']").val(record_id);
			
			var deleteUrl = site_url + module_name + "/delete/" + record_id ;
			$("#delete-record-form").attr('action' , deleteUrl );
			showLoader();
			$("#delete-record-form").submit();
		}
	}, function () { });
}

function updateMasterStatusRecord(thisitem , moduleName){
	
	var recordId = $(thisitem).attr("data-record-id");
	var	hitURL = site_url + moduleName + "/updateStatus";
	var	currentRow = $(thisitem);
	var	status = $(thisitem).parents('tr').find('.status-update').text();
	var module_name = $(thisitem).attr("data-module-name");
	if( moduleName == "{{ config('constants.LOOKUP_MODULE') }}" ){
		moduleName = $(thisitem).attr("data-another-module-name");
	}
	
	status = $.trim(status);
	var confirm_update_msg = '';
	if(status.toLowerCase() == '{{ strtolower(config("constants.ACTIVE_STATUS")) }}'){
        doStatus = 'inactive';
        confirm_update_msg = "{{ trans ( 'messages.update-status-msg', [ 'module' => trans('messages.inactive') ] ) }}";
    } else if (status.toLowerCase() == '{{ strtolower(config("constants.INACTIVE_STATUS")) }}'){
        doStatus = 'active';
        confirm_update_msg = "{{ trans ( 'messages.update-status-msg', [ 'module' => trans('messages.active') ] ) }}";
    } else {
    	alertifyMessage('error','{{ trans("messages.system-error") }}');
    }
	
	 
	moduleName = moduleName.replace(/_/g, ' ');
	
	alertify.confirm('{{ trans("messages.update-status") }}', confirm_update_msg , function(){	
		
		//ajax reqeust
	   jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : hitURL,
			data : { "_token": "{{ csrf_token() }}", 'record_id' : recordId , 'current_status' : status ,'module_name':module_name,'lookup_module_name':moduleName },
			beforeSend: function() {
		        //block ui
				showLoader();
		    },success:function(response){
		    	hideLoader();
				if(response.status_code == 1) {
					filterData();
					alertifyMessage('success',response.message);
				} else if(response.status_code == 101) {
					alertifyMessage('error',response.message);
				} else {
					alertifyMessage('error','{{ trans("messages.system-error") }}');
				}
		    },error:function(){
		    	
		    }
	   });
	}, function(){
		
	});;
}

function reindexTable(tbody_class_name){
	var table_index = 1;
	$('.'+tbody_class_name+ ' tr').each(function(){
		$(this).find('.table-index').html(table_index);
		table_index++;
	})
	
} 
var remove_image = [];
function removeTableRrecord(thisitem){
	alertify.confirm('{{ trans("messages.delete-record") }}', '{{ trans("messages.delete-record-msg") }}' , function () {
		var tbody_class_name = $(thisitem).parents('tbody').attr('class');
		remove_image.push( $(thisitem).attr('data-remove-image-id') );
		$('[name="remove_image_id"]').val(remove_image)
		
		$(thisitem).parents('tr').remove();
		reindexTable(tbody_class_name)
	}, function () { });
}

function updateLeaveStatus(){

	var record_id = $.trim($("[name='approved_leave_record_id']").val());
	var status = $.trim($("[name='approved_leave_record_status']").val());
	
	if( $("#add-leave-balance-form").valid() != true ){
		return false;
	}	

	alertify.confirm('{{ trans("messages.update-leave-status")  }}', '{{ trans("messages.common-confirm-msg" , [ "module" => trans("messages.update-leave-status") ] ) }}' , function(){
	
		$.ajax({
			type: "POST",
			dataType: "json",
			url: site_url + 'leave/addLeaveBalance',
			data: { 
				'record_id':record_id,
				'status':status,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if( response.status_code == 1 ){
					$("#add-leave-balance").modal('hide');
					alertifyMessage('success',response.message);
				} else {
					alertifyMessage('error',response.message);
				}
				
			},
			error: function() {
				hideLoader();
			}
		});

	}, function(){ });;
}

function getSalaryComponentDetail(thisitem){

	var salary_group_id = $.trim($(thisitem).val());
	if( salary_group_id != "" && salary_group_id != null ){
		$.ajax({
			type: "POST",
			url: '{{ config("constants.EMPLOYEE_MASTER_URL") }}' + '/getGroupComponent',
			data: { 
				'salary_group_id':salary_group_id,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if( response != "" && response != null ){
					$(".salary-break-up-html").html(response);
					$(".net-pay-table-div").show();
					$(".hold-salary-info-div").show();
					$(".hold-salary-selection-div").show();
				}
			},
			error: function() {
				hideLoader();
			}
		});
	} else {
		$(".salary-break-up-html").html("");
		$(".net-pay-table-div").hide();
		$(".hold-salary-info-div").hide();
		$(".hold-salary-selection-div").hide();
	}
}

function calculateYearlyAmount(thisitem){
	var month_amount = $.trim($(thisitem).parents('tr').find('.monthly-column').val());
	var year_amount = 0;
	if(parseFloat(month_amount) > 0 ){
		year_amount = ( parseFloat(month_amount) * 12 );
		year_amount = ( parseFloat(year_amount) > 0.00 ? parseFloat(year_amount).toFixed(2) : 0.00 );
	}
	$(thisitem).parents('tr').find('.yearly-column').html(displayValueIntoIndianCurrency(year_amount));

}

$('.modal').on('hidden.bs.modal' , function(){
    if( $(this).find('form').length > 0 ) {
		$(this).find('form').validate().resetForm();
		$(this).find('form').trigger("reset");
	 }
});

function countAnimation(thisitem){
	//console.log("ss = " + $(thisitem).text() );
	$(thisitem).prop('Counter', 0).animate({
    	Counter: $(thisitem).text()
  	}, {
		duration: 500,
        easing: 'swing',
		step: function(now,fx) {
			$(thisitem).text(Math.round(now*100)/100);
    	}
	});
}

function getLocationDetails(thisitem){
	 var village_id = $.trim($("[name='current_village']").val());
	 var permanent_village = $.trim($("[name='permanent_village']").val());
	
   
	var cur_village_city_id = $.trim($("[name='current_village']").find('option:selected').attr('data-cur-village-city-id'));
	var cur_village_state_id = $.trim($("[name='current_village']").find('option:selected').attr('data-cur-village-state-id'));
	var cur_village_country_id = $.trim($("[name='current_village']").find('option:selected').attr('data-cur-village-country-id'));

	var per_village_city_id = $.trim($("[name='permanent_village']").find('option:selected').attr('data-cur-village-city-id'));
	var per_village_state_id = $.trim($("[name='permanent_village']").find('option:selected').attr('data-cur-village-state-id'));
	var per_village_country_id = $.trim($("[name='permanent_village']").find('option:selected').attr('data-cur-village-country-id'));
	
	if(cur_village_city_id !="" && cur_village_city_id != null){
		$("[name='current_city'] option[data-city-id='" + cur_village_city_id + "']").prop("selected", true);
	}
	if(per_village_city_id !="" && per_village_city_id != null){
		$("[name='per_city'] option[data-city-id='" + per_village_city_id + "']").prop("selected", true)
	} 
	if(cur_village_state_id !="" && cur_village_state_id != null){
		$("[name='current_state'] option[data-state-id ='" + cur_village_state_id + "']").prop("selected", true)
	} 
	if(per_village_state_id !="" && per_village_state_id != null){
		$("[name='per_state'] option[data-per-state-id ='" + per_village_state_id + "']").prop("selected", true)
	}
	
	if(cur_village_country_id != "" && cur_village_country_id != null){
		$("[name='current_country'] option[data-country-id ='" + cur_village_country_id + "']").prop("selected", true)
	} 
	if(per_village_country_id != "" && per_village_country_id != null){
		$("[name='per_country'] option[data-country-id ='" + per_village_country_id + "']").prop("selected", true)
	} 
	 if(village_id != "" && village_id != null ){
		 ($("[name='current_city']")).attr('disabled','disabled');
	}else{
		 ($("[name='current_city']")).removeAttr('disabled');
		 //$("[name='current_city']").val("");
		 //$("[name='current_state']").val("");
		 //$("[name='current_country']").val("");
	 }

    if(permanent_village != "" && permanent_village != null ){
		 ($("[name='per_city']")).attr('disabled','disabled');
	 }else{
		($("[name='per_city']")).removeAttr('disabled');
		//$("[name='per_city']").val("");
		//$("[name='per_state']").val("");
		//$("[name='per_country']").val("");
	 }

   $(".select2").select2();
  
} 

$.validator.addMethod("validateUniqueEmployeeCode", function (value, element) {
 	var result = true;
	$.ajax({
		type: "POST",
		async: false,
		url: employee_module_url +'checkUniqueEmployeeCode',
		dataType: "json",
		data: {
			"_token": "{{ csrf_token() }}",
			'employee_code': $.trim($("[name='employee_code']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
		     },
		beforeSend: function() {
			
		},
		success: function (response) {
			
			if (response.status_code == 1) {
				return false;
			} else {
				result = false;
				return true;
			}
		}
	});
	return result;
}, '{{ trans("messages.error-unique-employee-code") }}');

function disableLogin(thisitem){
	var current_status = $.trim($(thisitem).attr("data-current-status"));
	var record_id = $.trim($(thisitem).attr("data-record-id"));
	var main_profile_status = $.trim($(thisitem).attr("data-selecteion-status"));
	//console.log("current_status = " + current_status );
	var confirm_status_msg = "";

	var field_id = $(thisitem).attr('id');
    var field_status = document.getElementById(field_id);
	//console.log(field_status);
	
	if( current_status == "{{ config('constants.ACTIVE_STATUS') }}" ){
		confirm_status_msg = "{{ trans('messages.update-login-status-msg' , [ 'module' => trans('messages.disable') ] ) }}";
	}else if( current_status == "{{ config('constants.INACTIVE_STATUS') }}" ){
		confirm_status_msg = "{{ trans('messages.update-login-status-msg' , [ 'module' => trans('messages.enable') ] ) }}";
	}
	
	alertify.confirm("{{ trans('messages.update-status') }}", confirm_status_msg ,function() {
		$.ajax({
	 		type: "POST",
	 		url: '{{config("constants.EMPLOYEE_MASTER_URL")}}' + '/updateLoginStatus',
	 		dataType:'json',
	 		data: {
	 			"_token": "{{ csrf_token() }}",
	 			'current_status':current_status,
	 			'record_id':record_id,
	 		},
	 		beforeSend: function() {
	 			//block ui
	 			showLoader();
	 		},
	 		success: function(response) {
	 	 		hideLoader();
	 	 		if(response.status_code == 1 ){
					alertifyMessage('success' , response.message);
					$(thisitem).attr("data-current-status",response.data.update_status);
					//$(".login-status").html(response.data.update_status);

					if( response.data.update_status != "" && response.data.update_status != null ){
						switch(response.data.update_status){
							case '{{ config("constants.ACTIVE_STATUS") }}':
								//$(".login-status").addClass("bg-success");
								//$(".login-status").removeClass("bg-danger");
								if( ( $(".suspended-status").is(":visible") == false ) && ( $(".relieved-status").is(":visible") == false ) ) {
									$(".login-status").show();
								}
								break;
							case  '{{ config("constants.INACTIVE_STATUS") }}':
								//$(".login-status").removeClass("bg-success");
								//$(".login-status").addClass("bg-danger");
								$(".login-status").hide();
								break;	
						}
					}
					

					if( $(thisitem).hasClass('enable-disable-status') != false ){
						$(thisitem).parents('.login-manage-switch').find('.login-manage-text').html(response.data.enable_disable_status_text);
					} else {
						$(thisitem).html(response.data.status_text);	
					}
					
					
				} else {
			    	alertifyMessage('error' , response.message);
				}
	 			
	 		},
	 		error: function() {
	 			hideLoader();
	 		}
	 	});
	}, function () {
		if(main_profile_status == "{{ config('constants.SELECTION_YES') }}"){
			
		} else {
			if (field_status.checked == false) {
	            $(thisitem).prop('checked', true);
	        } else if (field_status.checked == true) {
	            $(thisitem).prop('checked', false);
	        }
		}
		
 	});	 
}


$.validator.addMethod("validateUniquePersonalEmailId", function (value, element) {
 	var result = true;
	$.ajax({
		type: "POST",
		async: false,
		url: employee_module_url +'checkUniquePersonalEmailId',
		dataType: "json",
		data: {
			"_token": "{{ csrf_token() }}",
			'outlook_email_id': $.trim($("[name='outlook_email_id']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
		     },
		beforeSend: function() {
			
		},
		success: function (response) {
			
			if (response.status_code == 1) {
				return false;
			} else {
				result = false;
				return true;
			}
		}
	});
	return result;
}, '{{ trans("messages.error-unique-outlook-email-id") }}');

var cropperisInitialized = false; 
function startCrop(){
	if( crop_image_field_id != "" && crop_image_field_id != null ){
		var image = document.getElementById(crop_image_field_id);
		if (cropperisInitialized == true) {
            cropper.destroy();
        }
		
		cropper = new Cropper(image, {
	        // aspect ratio for square -  1/1
	        aspectRatio: 1 / 1,
	        viewMode: 1,
	        zoomOnWheel: false,
	        cropmove: function (e) {
				setTimeout(function(){
					$(".crop-profile-pic-button").trigger("click");
				} , 1000);
				
			},
	        crop: function(e) {
	            // console.log(e.detail.x);
	            // console.log(e.detail.y);
	        },
	    });
		cropperisInitialized = true;
		$(".crop-profile-pic-button").show();
		setTimeout(function(){
			$(".crop-profile-pic-button").trigger('click');
		} , 200)
		//$("." + elementId).hide();
	} 
	

}
var current_row ='';
function openLookupModal(thisitem){
    //console.log(thisitem);
    current_row = thisitem;
	var module_name = $.trim($(thisitem).attr('data-module-name'));
	var lookup_module_no = $.trim($(thisitem).attr('data-lookup-module'));
	var header_name = 'Add ' + enumText(module_name);

	$("[name='lookup_module_name']").val(module_name);
	var action_type = $.trim($(thisitem).attr('data-action'));
	if( action_type != "" &&  action_type != null ){
		$("[name='action_type']").val(action_type);
	}
	$("[name='lookup_module_record_id']").val('');
	$('.lookup-modal-action-button').html("{{ trans('messages.add') }}");
	$('.lookup-modal-action-button').attr('title' , "{{ trans('messages.add') }}");
	$("#add-lookup-modal").find('.twt-modal-header-name').html(header_name);
	$("[name='module_value']").val("");
	$("[name='request_type']").val("{{ config('constants.ADD_REQUEST') }}");
	$("[name='lookup_crud_module']").val(lookup_module_no);

	if( module_name != "" && module_name != null && module_name == "<?php echo config('constants.TEAM_LOOKUP')?>"){
		$(".lookup-chart-color").show();
		$("[name='module_chart_color']").val("");
	} else {
		$(".lookup-chart-color").hide();
		$("[name='module_chart_color']").val("");
	}
	
	
	openBootstrapModal('add-lookup-modal');
	$("#add-lookup-form").validate({
        errorClass: "invalid-input",
        rules: {
        	module_value: { required: true, noSpace : true },
        },
        messages: {
        	module_value: { required: "{{ trans('messages.required-module-value') }}" },
        },
    });
	
}

function addLookup(){

	if( $("#add-lookup-form").valid() != true ){
		return false;
    }

    var lookup_module_name = $.trim($("[name='lookup_module_name']").val());
    var module_value = $.trim($("[name='module_value']").val());
    var record_id = $.trim($("[name='lookup_module_record_id']").val());
    var module_chart_color = $.trim($("[name='module_chart_color']").val());
    var action_type = $("[name='action_type']").val();
    var lookup_crud_module = $.trim($('[name="lookup_crud_module"]').val());
    var confirm_box = "";
    var confirm_box_msg = "";
    
    if(record_id == 0){
	    confirm_box = "{{ trans('messages.add')}} "  + enumText(lookup_module_name);
	   confirm_box_msg = "{{ trans('messages.common-confirm-lookup-add-msg',['module'=> trans('messages.add')]) }} " + enumText(lookup_module_name) + ' ?';
	    
	 } else {
    	confirm_box = "{{ trans('messages.update') }} "  + enumText(lookup_module_name) ;
    	confirm_box_msg = "{{ trans('messages.common-confirm-lookup-add-msg',['module'=> trans('messages.update')]) }} " + enumText(lookup_module_name) + ' ?';
    } 
    alertify.confirm(confirm_box,confirm_box_msg,function() {
	$.ajax({
		type: "POST",
		dataType: "json",
		url: site_url + "add-lookup-master",
		data: {
			"_token": "{{ csrf_token() }}",
			lookup_module_name: lookup_module_name,
			module_value: module_value,
			record_id : record_id,
			lookup_crud_module:lookup_crud_module,
			lookup_chart_color:module_chart_color,
			request_type : $.trim($("[name='request_type']").val())
		},
		beforeSend: function() {
			//block ui
			showLoader();
		},
		success: function(response) {
			hideLoader();
			if( response.status_code == 1 ){
				alertifyMessage('success',response.message);
				$("#add-lookup-modal").modal('hide');
				if( lookup_crud_module != "" && lookup_crud_module != null && lookup_crud_module == "{{ config('constants.SELECTION_NO') }}"){
					var list_class = '';
					switch(lookup_module_name){
						case "{{ config('constants.DESIGNATION_LOOKUP')}}":
							list_class = '.designation-list';
							break;
						case "{{ config('constants.TEAM_LOOKUP')}}":
							list_class = '.team-list';
							break;
						case "{{ config('constants.RECRUITMENT_SOURCE_LOOKUP')}}":
							list_class = '.recruitment-list';
							break;
					}
					$(current_row).parents('.dependant-field-selection').find(list_class).html(response.data.html);
					
				}
			} else {
				alertifyMessage('error',response.message);
			}
			//console.log("action_type = " + action_type );
			
			if( action_type != "" && action_type != null ){
				filterData();
				$('.recruitment-reference-master-div').hide();
			}else if( record_id != "" && record_id != null ){
				filterData();	
			} else {
				var html = response.data.html;
				//console.log("lookup_module_name = " + lookup_module_name );
				var related_class_list = lookup_module_name + '-list';
				$('.' + related_class_list).each(function(){
					var selected_list = $.trim($(this).find('option:selected').attr('data-id'));
					$(this).html(html);
					$(this).find("option[data-id='" + selected_list + "']").prop("selected", true);
				})
			}
			
			
			
		},
		error: function() {
			hideLoader();
		}
	});
    },function() {});
}
$('.modal').on('hidden.bs.modal' , function(){
	if( $(this).find('form').length > 0 ) { 
		$(this).find('form').validate().resetForm();
		$(this).find('form').trigger("reset");
		$(this).find('form .custom-file-label').html("{{ trans('messages.choose-file') }}"); 
	}
});
$('#add-lookup-form').on("submit",function(e){
	e.preventDefault();
    e.stopPropagation();
});
$('#add-state-master-model-form').on("submit",function(e){
	e.preventDefault();
    e.stopPropagation();
});
$('#add-city-master-model-form').on("submit",function(e){
	e.preventDefault();
    e.stopPropagation();
});
$('#add-village-master-model-form').on("submit",function(e){
	e.preventDefault();
    e.stopPropagation();
});
$('#add-document-type-form').on("submit",function(e){
	e.preventDefault();
    e.stopPropagation();
});
$('#add-document-folder-form').on("submit",function(e){
	e.preventDefault();
    e.stopPropagation();
});
$('#add-salary-components-model-form').on("submit",function(e){
	e.preventDefault();
    e.stopPropagation();
});
$('#add-salary-group-model-form').on("submit",function(e){
	e.preventDefault();
    e.stopPropagation();
});




var removeImage = [];
function removeImageHtml(thisitem){
	alertify.confirm( "{{ trans('messages.delete-file') }}" , "{{ trans('messages.confirm-delete-file-msg') }}", function () {
    	removeImage.push( $(thisitem).attr('data-preview-name') );
		$('[name="remove_image"]').val( removeImage )
		$(thisitem).parents('.gallery-image-div').remove();
	}, function () { });
}

var check_old_password = '{{ config("constants.CHECK_OLD_PASSWORD") }}';
var check_password_regex = '{{ config("constants.CHECK_PASSWORD_REGEX") }}';
var check_strong_password = '';
$.validator.addMethod("checkStrongPassword", function(value, element) {
    var result = true;
    var err_message = ''; 
    ajaxResponse = $.ajax({
        type: "POST",
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: site_url + 'checkStrongPassword',
        dataType: "json",
        data: {
            "_token": "{{ csrf_token() }}",
            'new_password': $.trim($("[name='new_password']").val()),
            'user_id': ($.trim($("[name='user_id']").val()) != '' ? $.trim($("[name='user_id']").val()) : null)
        },
        beforeSend: function() {
            //block ui
            //showLoader();
        },
        success: function(response) {
        	check_strong_password = response.message
            if (response.status_code == 1) {
                return false;
            } else {
                result = false;
                return true;
            }
        }
    });
    
    return result;
}, function (params, element) {
	return check_strong_password;
});

function sameAsLocationCurrentAddress(thisitem){
	var same_current_address = $.trim($("[name='same_current_address']:checked").val());
	var address_line_1 = $.trim($("[name='address_line_1']").val());
	var address_line_2 = $.trim($("[name='address_line_2']").val());
	var current_city = $.trim($("[name='current_city']").find('option:selected').attr('data-city-id'));
	var current_state = $.trim($("[name='current_state']").find('option:selected').attr('data-state-id'));
	var current_country = $.trim($("[name='current_country']").find('option:selected').attr('data-country-id'));
	var current_pincode = $.trim($("[name='current_pincode']").val());
	var pincode = $.trim($("[name='pincode']").val());
	var current_village = $.trim($("[name='current_village']").find('option:selected').attr('data-village-id'));
	
	if(same_current_address != "" && same_current_address != null  && same_current_address == "{{config('constants.SELECTION_YES')}}"){
		
		$("[name='per_address_line_1']").val(address_line_1);
		$("[name='per_address_line_2']").val(address_line_2);

		$("[name='per_pincode']").val(current_pincode);
		
		
		$("[name='per_country']").val($('[name="per_country"] option[data-per-country-id="' + current_country + '"]').val());
		$("[name='per_state']").val($('[name="per_state"] option[data-per-state-id="' + current_state + '"]').val());
		$("[name='permanent_village']").val($('[name="permanent_village"] option[data-village-id="' + current_village + '"]').val()).select2();
		$("[name='per_city']").val($('[name="per_city"] option[data-city-id="' + current_city + '"]').val());
		
		$("[name='address_permanent_line_1']").val(address_line_1); 
		$("[name='address_permanent_line_2']").val(address_line_2);
		$("[name='pincode_permanent']").val(pincode);
		
		$("[name='per_country']").val($('[name="per_country"] option[data-country-id="' + current_country + '"]').val());
		if(current_village != "" && current_village != null){
			$("[name='per_city']").attr('disabled','disabled');
		} else{
			($("[name='per_city']")).removeAttr('disabled');
			}
		
		$(".select2").select2();
	}
}
var employee_module_url = '{{config("constants.EMPLOYEE_MASTER_URL")}}' + '/';
var current_record = "";
function showResignApproveRejectModal(thisitem){
	current_record = thisitem;
	
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
 	 			var employee_name_code = $.trim($(thisitem).attr('data-employee-name'));
 	 			if(employee_name_code !="" && employee_name_code != null){
 	 				$("#resign-approve-reject-modal").find(".twt-reject-header-name").html('{{ trans("messages.resign-approval") }}' + " - " +employee_name_code);
 	 			}
 	 			
 	 			
 	 			openBootstrapModal("resign-approve-reject-modal");
				var approve_resign_emp_joining_date = $.trim($("#resign-approve-reject-modal").find("[name='approve_resign_emp_joining_date']").val());
 	 			var resign_apply_date = $.trim($("#resign-approve-reject-modal").find("[name='resign_apply_date']").val());
				$(".select2").select2();
 	 			$('[name="approve_resign_initial_exit_other_last_working_date"],[name="approve_resign_initial_exit_employee_provide_notice_exit_date"],[name="pf_exit_date"]').datetimepicker({
 	 		        useCurrent: false,
 	 		        viewMode: 'days',
 	 		        ignoreReadonly: true,
 	 		        format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
 	 		        showClose: true,
					showClear: true,
 	 		        widgetPositioning: {
 	 		            vertical: 'bottom',
 	 		            horizontal: 'auto'

 	 		        },
 	 		        icons: {
 	 		            clear: 'fa fa-trash',
 	 		            Close: 'fa fa-trash',
 	 		        },
 	 		    });
 	 			$(function() {
 	 	           $('[data-toggle="tooltip"]').tooltip()
 	 	        })
 	 			$("[name='approve_resign_initial_exit_employee_provide_notice_exit_date']").data("DateTimePicker").minDate(moment(approve_resign_emp_joining_date,'YYYY-MM-DD'));
 	 		    if( resign_apply_date != "" && resign_apply_date != null ){
 	 		    	$("[name='approve_resign_initial_exit_other_last_working_date']").data("DateTimePicker").minDate(moment(resign_apply_date,'YYYY-MM-DD'));
 	 		    	$("[name='pf_exit_date']").data("DateTimePicker").minDate(moment(resign_apply_date,'YYYY-MM-DD'));
	 	 		} else {
	 	 			$("[name='approve_resign_initial_exit_other_last_working_date']").data("DateTimePicker").minDate(moment(approve_resign_emp_joining_date,'YYYY-MM-DD'));
	 	 			$("[name='pf_exit_date']").data("DateTimePicker").minDate(moment(approve_resign_emp_joining_date,'YYYY-MM-DD'));
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
function showApproveRejectOtherDateDiv(thisitem){
	var approve_resign_initial_exit_recommend_last_working_day_type = $.trim($("[name='approve_resign_initial_exit_recommend_last_working_day_type']:checked").val());

	if( approve_resign_initial_exit_recommend_last_working_day_type != "" && approve_resign_initial_exit_recommend_last_working_day_type != null &&  approve_resign_initial_exit_recommend_last_working_day_type == "{{ config('constants.NOTICE_PERIOD') }}" ){
		$(".approve-resign-other-last-working-date").hide();
	} else {
		$(".approve-resign-other-last-working-date").show();
	}
	
}

$("#approve-reject-resign-form").validate({
    errorClass: "invalid-input",
    rules: {
    	approve_resign_reject_remark: {
            required: true
        },
        approve_resign_initial_exit_employee_provide_notice_exit_date: {
            required: true
        },
        pf_exit_date: {
            required: true
        },
    },
    messages: {
    	approve_resign_reject_remark: {
            required: "{{ trans('messages.require-remark') }}"
        },
        approve_resign_initial_exit_employee_provide_notice_exit_date: {
            required: "{{ trans('messages.require-employee-provide-notice-exit') }}"
        },
        pf_exit_date: {
            required: "{{ trans('messages.require-pf-exit-date') }}"
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
	/* if( status == "{{ config('constants.APPROVED_STATUS') }}" ){
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
	} */
	var row_index = $(current_record).parents('tr').find('.sr-col-index').html();
	
	var formData = new FormData( $('#approve-reject-resign-form')[0] );
	formData.append('employee_id' , resign_approve_reject_employee_id );
	formData.append('remark' , resign_approve_reject_remark );
	formData.append('status' , status );
	formData.append('approve_resign_initial_exit_employee_provide_notice_exit_date' , $.trim($("[name='approve_resign_initial_exit_employee_provide_notice_exit_date']").val()) );
	formData.append('approve_resign_initial_exit_other_last_working_date' , $.trim($("[name='approve_resign_initial_exit_other_last_working_date']").val()) );
	formData.append('approve_resign_initial_exit_recommend_last_working_day_type' , $.trim($("[name='approve_resign_initial_exit_recommend_last_working_day_type']:checked").val()) );
	
	formData.append('row_index' , row_index );
	
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
    	 	 		//$(".approve-reject-take-action-button").remove();
    	 	 		
    	 	 		
					alertifyMessage('success' , response.message);
					$('.employee-resign-info-section').html(response.data.initateExitHtml);
					if( status == "{{ config('constants.REJECTED_STATUS') }}" ){
						$('.employee-resign-info-section').hide();
    				}
    				
					$('.employee-primary-details').html(response.data.primaryDetailsInfo);
					$('.employee-profile-pic-view--master-div-html').html(response.data.mainProfileInfo);
					$(current_record).parents('.resignation-record').html(response.data.html);
					$(current_record).parents('.employee-resign-info-section').find('.approve-reject-take-action-button').remove();
					
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

function viewSalary(thisitem){
	var employee_id = $.trim($(thisitem).attr('data-emp-id'));
	var salary_month = $.trim($(thisitem).attr('data-salary-month'));


	var employee_code = $.trim($(thisitem).attr('data-emp-code'));
	var employee_name = $.trim($(thisitem).attr('data-emp-name'));
	
	
	salary_current_row = thisitem;
	
	if( employee_id != "" && employee_id  != null ){

		$.ajax({
			type: "POST",
			url: '{{ config("constants.SALARY_MASTER_URL") }}' + '/employees-salary-info',
			data:{ 'employee_id' : employee_id , 'salary_month' : salary_month },
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if( response != "" && response != null ){
					response = $.trim(response);
					var custom_twt_modal_header  = employee_name + ' (' +  employee_code + ')';
					$(".employee-salary-html").html(response);
					$("#employee-salary-view").find(".twt-custom-modal-header").html(custom_twt_modal_header);
					openBootstrapModal('employee-salary-view');
				}
			},
			error: function() {
				hideLoader();
			}
		});
	}
}

function sendSinglePaySlip(thisitem){
	
	var record_type = $.trim($(thisitem).attr('data-record-type'));

	if( record_type != "" && record_type != null && record_type == "multiple" ){
		var selected_record_ids = [];
		$(".row-checkbox").each(function(){
			if( $(this).prop('checked') != false ){
				selected_record_ids.push($.trim($(this).val()));
			}
		})
		
		if(selected_record_ids.length == 0 ){
			alertifyMessage('error' , "{{ trans('messages.required-atleast-one-record') }}");
			return false;
		}
		
		var record_id = selected_record_ids;
		var request_url  = '{{ config("app.url") }}' + 'send-multiple-pay-slip';
	} else {
		var record_id = $.trim($(thisitem).attr('data-record-id'));
		var request_url  = '{{ config("app.url") }}' + 'send-pay-slip';
	}
	
	if( record_id != "" && record_id != null ){
		var confirm_msg = "{{ trans('messages.send-pay-slip') }}";
		var confirm_msg_text = "{{ trans('messages.common-confirm-msg' , [ 'module' =>  trans('messages.send-pay-slip')  ]) }}";

		alertify.confirm(confirm_msg,  confirm_msg_text  ,function() {
			
			$.ajax({
    	 		type: "POST",
    	 		dataType : 'json',
    	 		url: request_url ,
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

function updateAmmentmentSalaryValue(){
	alertify.confirm('{{ trans("messages.amendment-salary") }}', '{{ trans("messages.common-confirm-msg" , [ "module"  =>  trans("messages.amend-salary") ] ) }}',function() {

		var formData = new FormData($('#employee-salary-amendment-form')[0]);
		$.ajax({
			type: "POST",
			dataType : 'json',
			url: '{{ config("constants.SALARY_MASTER_URL") }}'  + '/employee-salary-amendment',
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
					alertifyMessage('success' , response.message );
					$("#employee-salary-view").modal('hide');
					filterData(null , true );
				}else if( response.status_code == 101 ){
					alertifyMessage('error' , response.message );
				}
			},
			error: function() {
				hideLoader();
			}
		});
	},function() {});
}
</script>