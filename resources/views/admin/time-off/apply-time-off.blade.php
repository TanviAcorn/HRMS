<div class="document-folder modal fade" id="apply-time-off-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
    	<div class="modal-content">
        	<div class="modal-header">
			    <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.apply-time-off") }}</h5>
			    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			        <span aria-hidden="true"><i class="fas fa-times"></i></span>
			    </button>
			</div>
			{!! Form::open(array( 'id '=> 'apply-time-off-form' , 'method' => 'post'  )) !!}
				<div class="modal-body">
			        <div class="row ">
			            <div class="col-sm-6">
			                <div class="form-group">
			                    <label for="time_off_date" class="control-label">{{ trans('messages.date') }}<span class="star">*</span></label>
			                    <input type="text" class="form-control" name="time_off_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
			                </div>
			            </div>
			            <div class="col-sm-6">
			                <div class="form-group">
			                    <label class="control-label" for="time_off_type">{{ trans("messages.type") }}<span class="star">*</span></label>
			                    <select class="form-control" name="time_off_type">
			                        <option value="">{{ trans("messages.select") }}</option>
			                        @if(count($timeOffSelectionDetails) > 0 )
			                        	@foreach($timeOffSelectionDetails as $timeOffKey =>  $timeOffSelectionDetail)
			                        			<option value="{{ $timeOffKey  }}">{{ $timeOffSelectionDetail }}</option>		
			                        	@endforeach
			                        @endif	
			                    </select>
			                </div>
			            </div>
			            <div class="col-12">
			                <div class="form-group time-master">
			                    <div class="row">
			                        <div class="col-6">
			                            <label for="time_off_from_time" class="mr-2 control-label">{{ trans('messages.from-time') }}<span class="star">*</span></label>
			                            <input type="text" onchange="startTimeEndTimeValidation(this,'start_time');" name="time_off_from_time" class="start-time form-control" placeholder="{{ trans('messages.from-time') }}">
			                        </div>
			                        <div class="col-6">
			                            <label for="time_off_to_time" class="mr-2 control-label">{{ trans('messages.to-time') }}<span class="star">*</span></label>
			                            <input type="text" onchange="startTimeEndTimeValidation(this,'end_time');" name="time_off_to_time" class="end-time form-control"  placeholder="{{ trans('messages.to-time') }}">
			                        </div>
			                    </div>
			                </div>
			            </div>
			            <div class="col-sm-6 time-back-div" style="display: none;">
			                <div class="form-group">
			                    <label for="time_off_date" class="control-label">{{ trans('messages.time-back-date') }}<span class="star">*</span></label>
			                    <input type="text" class="form-control" name="time_off_back_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
			                </div>
			            </div>
			            <div class="col-12 time-back-div" style="display: none;">
			                <div class="form-group time-master">
			                    <div class="row">
			                        <div class="col-6">
			                            <label for="time_off_from_time" class="mr-2 control-label">{{ trans('messages.from-time') }}<span class="star">*</span></label>
			                            <input type="text" onchange="startTimeEndTimeValidation(this,'start_time');" name="time_off_back_from_time" class="start-time form-control" placeholder="{{ trans('messages.from-time') }}">
			                        </div>
			                        <div class="col-6">
			                            <label for="time_off_to_time" class="mr-2 control-label">{{ trans('messages.to-time') }}<span class="star">*</span></label>
			                            <input type="text" onchange="startTimeEndTimeValidation(this,'end_time');" name="time_off_back_to_time" class="end-time form-control"  placeholder="{{ trans('messages.to-time') }}">
			                        </div>
			                    </div>
			                </div>
			            </div>
			            <div class="col-12">
			                <div class="form-group">
			                    <label for="remarks" class="control-label">{{ trans('messages.remarks') }}<span class="text-danger">*</span></label>
			                    <textarea name="time_off_remark" cols="30" rows="2" class="form-control" placeholder="{{ trans('messages.remarks') }}"></textarea>
			                </div>
			            </div>
			        </div>
			    </div>
			    <input type="hidden" name="apply_time_off_employee_id" value="">
			    <div class="modal-footer justify-content-end">
			        <button type="button" onclick="addTimeOff(this);" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
			        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
			    </div>
			{!! Form::close() !!}
    	</div>
	</div>
</div>
<script>
$(document).ready(function(){
	$("[name='time_off_date'],[name='time_off_back_date']").datetimepicker({
        useCurrent: false,
        viewMode: 'days',
        ignoreReadonly: true,
        format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
        showClear: true,
        showClose: true,
        //maxDate : moment().endOf('d'),
        widgetPositioning: {
            vertical: 'bottom',
            horizontal: 'auto'

        },
        icons: {
            clear: 'fa fa-trash',
            Close: 'fa fa-trash',
        },
   });

	$("[name='time_off_from_time'],[name='time_off_to_time']").mdtimepicker({ 
		readOnly: false, 
		theme: 'blue', 
		clearBtn: true, 
		datepicker : false, 
		ampm: true, 
		format: 'h:mm tt' 
	});

	$("[name='time_off_back_from_time'],[name='time_off_back_to_time']").mdtimepicker({ 
		readOnly: false, 
		theme: 'blue', 
		clearBtn: true, 
		datepicker : false, 
		ampm: true, 
		format: 'h:mm tt' 
	});
	   
	$("[name='time_off_date']").data('DateTimePicker').minDate(moment().subtract(1, "month").startOf('d'));
	$("#apply-time-off-form").validate({
	    errorClass: "invalid-input",
	    onfocusout: false,
	    onkeyup: false,
        ignore: [],
	    rules: {
	        time_off_date: {
	            required: true,
	            checkDuplicateTimeOffAdjustmentRequest : false,
	        },
	        time_off_type: {
	            required: true,
	            checkDuplicateTimeOffAdjustmentRequest : true,
	        },
	        time_off_from_time: {
	            required: true,
	            checkDuplicateTimeOffRequest : true
	        },
	        time_off_to_time: {
	            required: true,
	        },
	        time_off_back_date: {
	            required: function(){
					return ( $.trim($('[name="time_off_type"]').val()) != '' && $.trim($('[name="time_off_type"]').val()) != null && $.trim($('[name="time_off_type"]').val()) == "{{ config('constants.ADJUSTMENT_STATUS') }}" ? true : false );
		        },
	        },
	        time_off_back_from_time: {
	            required: function(){
					return ( $.trim($('[name="time_off_type"]').val()) != '' && $.trim($('[name="time_off_type"]').val()) != null && $.trim($('[name="time_off_type"]').val()) == "{{ config('constants.ADJUSTMENT_STATUS') }}" ? true : false );
		        },
	        },
	        time_off_back_to_time: {
	            required: function(){
					return ( $.trim($('[name="time_off_type"]').val()) != '' && $.trim($('[name="time_off_type"]').val()) != null && $.trim($('[name="time_off_type"]').val()) == "{{ config('constants.ADJUSTMENT_STATUS') }}" ? true : false );
		        },
	        },
			time_off_remark: {
	            required: true,
	        }
		},
	    messages: {
	        time_off_date: {
	            required: "{{ trans('messages.please-enter-date') }}"
	        },
	        time_off_type: {
	            required: "{{ trans('messages.require-type') }}"
	        },
	        time_off_from_time: {
	            required: "{{ trans('messages.require-from-time') }}"
	        },
	        time_off_to_time: {
	            required: "{{ trans('messages.require-to-time') }}"
	        },
	        time_off_back_date: {
	            required: "{{ trans('messages.required-time-back-date') }}"
	        },
	        time_off_back_from_time: {
	            required: "{{ trans('messages.require-from-time') }}"
	        },
	        time_off_back_to_time: {
	            required: "{{ trans('messages.require-to-time') }}"
	        },
			time_off_remark: {
				required: "{{ trans('messages.require-remarks') }}"
			}
	    },
		submitHandler: function(form) {

			
			showLoader()
	        form.submit();
	    }
	});	
})

function openApplyTimeOffModal(thisitem){
	var employee_id = $.trim($(thisitem).attr("data-emp-id"));
	//console.log(" employee_id = " + employee_id);
	$("#apply-time-off-modal").find("[name='apply_time_off_employee_id']").val(employee_id);
	$(".time-back-div").hide();
	var allowed_leave_min_date = getLeaveTimeOffMinDate();
	$("#apply-time-off-modal").find("[name='time_off_from_time']").val("");
	$("#apply-time-off-modal").find("[name='time_off_to_time']").val("");
	$("#apply-time-off-modal").find("[name='time_off_back_from_time']").val("");
	$("#apply-time-off-modal").find("[name='time_off_back_to_time']").val("");
	$("[name='time_off_date']").data("DateTimePicker").minDate(moment(allowed_leave_min_date,'DD-MM-YYYY'));
	$("[name='time_off_back_date']").data("DateTimePicker").minDate(moment(allowed_leave_min_date,'DD-MM-YYYY'));
	openBootstrapModal('apply-time-off-modal');
}

function startTimeEndTimeValidation(thisitem , field_name = 'end_time'){
	var time_format = 'hh:mm A'; 
	var start_time = $.trim($(thisitem).parents('.time-master').find('.start-time').val());
	var end_time = $.trim($(thisitem).parents('.time-master').find('.end-time').val());
	//console.log("start_time = " + start_time );
	//console.log("end_time = " + end_time );
	if( start_time != "" && start_time != null && end_time != "" && end_time != null  ){
		start_time = moment(start_time, time_format ),
		end_time = moment(end_time,  time_format );

		//console.log("start_time = " + start_time );
		//console.log("end_time = " + end_time );
		if( field_name == "start_time" ){
			if( start_time.isAfter(end_time) != false ){
				$(thisitem).parents('.time-master').find('.start-time').val("");
				alertifyMessage('error',"{{ trans('messages.invalid-time-selection') }}");
			}
		} else {
			if( end_time.isBefore(start_time) != false ){
				$(thisitem).parents('.time-master').find('.end-time').val("");
				alertifyMessage('error',"{{ trans('messages.invalid-time-selection') }}");
			}
		}
	}
}

function addTimeOff(){

	if( $("#apply-time-off-form").valid() != true ){
		return false;
	}	

	var time_off_date = $.trim($("[name='time_off_date']").val());
	var time_off_type = $.trim($("[name='time_off_type']").val());
	var time_off_from = $.trim($("[name='time_off_from_time']").val());
	var time_off_to = $.trim($("[name='time_off_to_time']").val());
	var time_off_remark = $.trim($("[name='time_off_remark']").val());

	var time_off_back_date = $.trim($("[name='time_off_back_date']").val());
	var time_off_back_from_time = $.trim($("[name='time_off_back_from_time']").val());
	var time_off_back_to_time = $.trim($("[name='time_off_back_to_time']").val());
	
	var search_academic_year = $.trim($("[name='search_academic_year']").val());

	//console.log("time_off_type = " + time_off_type );
	if( time_off_type ==  "{{ config('constants.ADJUSTMENT_TIME_OFF') }}"  ){

		var time_format = 'hh:mm'; 
		
		start_time = moment(time_off_from, time_format ),
		end_time = moment(time_off_to,  time_format );

		var diff_into_second = end_time.diff(start_time,'seconds');
		//console.log("diff_into_second  = " + diff_into_second );	
		if( diff_into_second > "{{ config('constants.ADJUSTMENT_TIME_OFF_REQUEST_LIMIT') }}" ){
			alertifyMessage('error','{{ trans("messages.error-adjustment-time-limit") }}');
			return false;
		}
	}
	var employee_id = $.trim($("#apply-time-off-modal").find("[name='apply_time_off_employee_id']").val());
	alertify.confirm('{{ trans("messages.apply-time-off")  }}', '{{ trans("messages.common-confirm-msg" , [ "module" => trans("messages.apply-time-off") ] ) }}' , function(){
		$.ajax({
			type: "POST",
			dataType: "json",
			url: '{{ config("constants.TIME_OFF_MASTER_URL") }}' + '/applyTimeOff',
			data: { 
				'time_off_date':time_off_date,
				'time_off_type':time_off_type,
				'time_off_from':time_off_from,
				'time_off_to':time_off_to,
				'time_off_remark':time_off_remark,
				'time_off_back_date':time_off_back_date,
				'time_off_back_from_time':time_off_back_from_time,
				'time_off_back_to_time':time_off_back_to_time,
				'employee_id':employee_id,
				'search_academic_year':search_academic_year,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				
				if( response.status_code == 1 ){
					setTimeout(function(){ hideLoader() } , 500 );
					$("#apply-time-off-modal").modal('hide');
					var updated_html = response.data.html;
					$(".filter-time-off-dashboard-html").html(updated_html);
					alertifyMessage('success',response.message);
				} else {
					hideLoader();
					alertifyMessage('error',response.message);
				}
				
			},
			error: function() {
				hideLoader();
			}
		});
	}, function(){ });;
	

}


$.validator.addMethod("checkDuplicateTimeOffAdjustmentRequest", function(value, element) {
    var result = true;
    ajaxResponse = $.ajax({
        type: "POST",
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{config("constants.TIME_OFF_MASTER_URL")}}' + '/checkDuplicateTimeOffAdjustmentRequest',
        dataType: "json",
        data: {
            "_token": "{{ csrf_token() }}",
            'time_off_date': $.trim($("[name='time_off_date']").val()),
            'time_off_type': $.trim($("[name='time_off_type']").val()),
            'employee_id': $.trim($("[name='apply_time_off_employee_id']").val()),
            'record_id': ($.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null)
        },
        beforeSend: function() {
            //block ui
            //showLoader();
        },
        success: function(response) {
            if (response.status_code == 1) {
                return false;
            } else {
                result = false;
                return true;
            }
        }
    });
    return result;
}, '{{ trans("messages.error-duplicate-time-off-adjustment-request") }}');

$.validator.addMethod("checkDuplicateTimeOffRequest", function(value, element) {
    var result = true;
    ajaxResponse = $.ajax({
        type: "POST",
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{config("constants.TIME_OFF_MASTER_URL")}}' + '/checkDuplicateTimeOffAdjustmentRequest',
        dataType: "json",
        data: {
            "_token": "{{ csrf_token() }}",
            'time_off_date': $.trim($("[name='time_off_date']").val()),
            'time_off_type': $.trim($("[name='time_off_type']").val()),
            'time_off_from': $.trim($("[name='time_off_from_time']").val()),
            'time_off_to': $.trim($("[name='time_off_to_time']").val()),
            'employee_id': $.trim($("[name='apply_time_off_employee_id']").val()),
            'record_id': ($.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null)
        },
        beforeSend: function() {
            //block ui
            //showLoader();
        },
        success: function(response) {
            if (response.status_code == 1) {
                return false;
            } else {
                result = false;
                return true;
            }
        }
    });
    return result;
}, '{{ trans("messages.error-duplicate-time-off-request") }}');

$(document).on('change' , "[name='time_off_type']" , function(){
	var time_off_type = $.trim($("[name='time_off_type']").val());
	if( time_off_type != "" && time_off_type != null && time_off_type == "{{ config('constants.ADJUSTMENT_STATUS') }}"){
		$(".time-back-div").show();
	} else {
		$(".time-back-div").hide();
	}
});
</script>