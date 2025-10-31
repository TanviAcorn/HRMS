
 									<div class="form-group col-sm-6 radio-apply-width	 from-date-group">
                                        <label for="leave_from_date" class="control-label">{{ trans("messages.from-date") }}<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="leave_from_date"  placeholder="{{ trans('messages.dd-mm-yyyy') }}" autocomplete="off" value="{{ old('leave_from_date' , ( (isset($recordInfo) && (!empty($recordInfo->dt_leave_from_date)) ? clientDate($recordInfo->dt_leave_from_date) : ''  ) ) ) }}" />
                                    </div>
                                    <div class="d-flex align-items-center apply-leave-to-tag">
                                        <label for="to_date" class="control-label mt-3 mr-1 d-lg-block d-none">{{ trans("messages.to") }}</label>
                                    </div>
                                    <div class="form-group col-sm-6 radio-apply-width">
                                        <label for="leave_to_date" class="control-label">{{ trans("messages.to-date") }}<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="leave_to_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" autocomplete="off" value="{{ old('leave_to_date' , ( (isset($recordInfo) && (!empty($recordInfo->dt_leave_to_date)) ? clientDate($recordInfo->dt_leave_to_date) : ''  ) ) ) }}" />
                                    </div>
                                    <div class="form-group radio-apply-width col-sm-6 dual-date-selection from-date-group position-relative" style="display:none;">
	                                        <div><label for="from_date" class="control-label">{{ trans("messages.from-session") }}<span class="text-danger">*</span></label></div>
	                                        <input class="custom-check" type="radio" name="dual_date_from_session" id="duat_date_from_first_half" value="{{ config('constants.FIRST_HALF_LEAVE') }}" onclick="showLeaveDuration();" >
	                                        <label class="custom-check-label first-half" for="duat_date_from_first_half">{{ trans("messages.first-half") }}</label>
	                                        <input class="custom-check" type="radio" name="dual_date_from_session" id="duat_date_from_second_half" value="{{ config('constants.SECOND_HALF_LEAVE') }}" onclick="showLeaveDuration();">
	                                        <label class="custom-check-label second-half" for="duat_date_from_second_half">{{ trans("messages.second-half") }}</label>
											<div class="dual-date-selection form-to-session" style="display:none;">
	                                        <label for="to_date" class="control-label mt-4 mr-1 d-lg-block d-none">To</label>
	                                    </div>
	                                    </div>
	                                    <div class="form-group radio-apply-width col-sm-6 pb-0 dual-date-selection" style="display:none;">
	                                        <div><label for="from_date" class="control-label">{{ trans("messages.to-session") }}<span class="text-danger">*</span></label></div>
	                                        <input class="custom-check" type="radio" name="dual_date_to_session" id="duat_date_to_first_half" value="{{ config('constants.FIRST_HALF_LEAVE') }}" onclick="showLeaveDuration();">
	                                        <label class="custom-check-label first-half" for="duat_date_to_first_half">{{ trans("messages.first-half") }}</label>
	                                        <input class="custom-check" type="radio" name="dual_date_to_session" id="duat_date_to_second_half" value="{{ config('constants.SECOND_HALF_LEAVE') }}" onclick="showLeaveDuration();" >
	                                        <label class="custom-check-label second-half" for="duat_date_to_second_half">{{ trans("messages.second-half") }}</label>
	                                    </div>
                                    
                                    	<div class="form-group col-sm-12 single-date-selection mt-2" style="display:none;">
	                                        <input class="custom-check" type="radio" name="single_date_session" id="single_date_full_day" value="{{ config('constants.FULL_DAY_LEAVE') }}" onclick="showLeaveDuration();">
	                                        <label class="custom-check-label first-half full-day" for="single_date_full_day">{{ trans("messages.full-day") }}</label>
	                                        <input class="custom-check" type="radio" name="single_date_session" id="single_date_first_half" value="{{ config('constants.FIRST_HALF_LEAVE') }}" onclick="showLeaveDuration();">
	                                        <label class="custom-check-label first-half" for="single_date_first_half">{{ trans("messages.first-half") }}</label>
	                                        <input class="custom-check" type="radio" name="single_date_session" id="single_date_second_half" value="{{ config('constants.SECOND_HALF_LEAVE') }}" onclick="showLeaveDuration();">
	                                        <label class="custom-check-label second-half" for="single_date_second_half">{{ trans("messages.second-half") }}</label>
	                                    </div>
	                                
                                    <div class="form-group col-12 leve-request-duration-div" style="display:none;">
                                        <div class="alert alert-primary mb-0" role="alert">
                                            Leave request is for <span class="leve-request-duration">1</span> day.
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-12">
                                        <label class="control-label" for="leave_types">{{ trans("messages.select-available-leave-types") }}<span class="text-danger">*</span></label>
                                        <select class="form-control" name="leave_types">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <?php
                                            $showUnPaidLeave = true;
                                            if(!empty($leaveBalanceDetails)){
                                            	foreach ($leaveBalanceDetails as $leaveBalanceDetail){
                                            		$encodeLeaveTypeId = (Wild_tiger::encode($leaveBalanceDetail->leaveType->i_id));
                                            		$selected ='';
                                            		if(isset($recordInfo) && ($recordInfo->i_leave_type_id == $leaveBalanceDetail->leaveType->i_id)){
                                            			$selected = "selected='selected'";
                                            		}
                                            		if( $leaveBalanceDetail->d_current_balance > 0 ){
                                            			$showUnPaidLeave = false;
	                                            		?>
	                                            		<option value="{{ $encodeLeaveTypeId }}" {{ $selected }}>{{ $leaveBalanceDetail->leaveType->v_leave_type_name }}</option>
	                                            		<?php
                                            		}
                                            	}
                                            }
                                            ///if( $showUnPaidLeave != false ){
                                            	?>
                                            	<option value="{{ Wild_tiger::encode( config('constants.UNPAID_LEAVE_TYPE_ID') ) }}" >{{ trans('messages.unpaid-leave') }}</option>
                                            	<?php 
                                          //  }
                                            
                                            ?>
                                            
                                        </select>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="leave_note" class="control-label">{{ trans("messages.note") }}<span class="text-danger">*</span></label>
                                        <textarea name="leave_note" class="form-control" rows="3" placeholder="{{ trans('messages.note') }}">{{ ( (isset($recordInfo) && (!empty($recordInfo->v_leave_note)) ? ($recordInfo->v_leave_note) : ''  ) )}}</textarea>
                                    </div>
                                    <?php 
                                  	if( (!empty($recordInfo->v_file)) && file_exists(storage_path('app/' . $recordInfo->v_file)) ){
                                    	$fileName =  config('constants.FILE_STORAGE_PATH_URL') . config('constants.UPLOAD_FOLDER') . $recordInfo->v_file;
                                    }
									?>
                                    <div class="form-group col-12">
                                        <input type="file" name="file_upload[]" multiple id="file_upload" class="my-custome-file" onchange="galleryMultipleDocumentPreview(this)">
                                        <label for="file_upload" class="my-custome-file-label"><i class="fas fa-paperclip"></i>{{ trans("messages.add-attachment") }}</label>
                                        <div id="file-upload-filename" class="file-upload-filename">{{ (!empty($fileName) ? basename($fileName) : '') }}</div>
	                                    <div class="row">
										   <div class="col-lg-12">
				                            	<div class="file_upload-preview-div col-lg-12">
				                            	
				                            	</div>
				                            </div>
										</div>
                                    </div>
                                    <input type="hidden" name="apply_leave_employee_id" value="">
                                    
                                    
                                    
	<script>
		$("[name='leave_from_date'],[name='leave_to_date']").datetimepicker({
    		useCurrent: false,
    		viewMode: 'days',
    		ignoreReadonly: true,
			showClear: true,
            showClose: true,
    		widgetPositioning: {
    			vertical: 'bottom'
    		},
			icons: {
                clear: 'fa fa-trash',
                Close: 'fa fa-trash',
            },
    		format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

    	});
		$("[name='leave_from_date']").data('DateTimePicker').minDate(moment().subtract(1, "month").startOf('d'));
		$("[name='leave_to_date']").data('DateTimePicker').minDate(moment().subtract(1, "month").startOf('d'));
		//console.log("weeeeeeee");
		<?php if( isset($allowedLastEffDate) && (!empty($allowedLastEffDate)) && (config('constants.LAST_SALARY_GENERATED_DATE_CHECK') == 1 ) ) { ?>
			//console.log('<?php echo $allowedLastEffDate ?>');
			$("[name='leave_from_date']").data('DateTimePicker').minDate(moment().subtract(1, "month").startOf('d'));
		<?php } ?>
		
        $("[name='leave_from_date']").datetimepicker().on('dp.change', function(e) {
    		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    			var incrementDay = moment((e.date)).startOf('d');
    		 	$("[name='leave_to_date']").data('DateTimePicker').minDate(incrementDay);
    		} else {
    			$("[name='leave_to_date']").data('DateTimePicker').minDate(false);
            }
    	    $(this).data("DateTimePicker").hide();
    	});

        $("[name='leave_to_date']").datetimepicker().on('dp.change', function(e) {
        	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    	        var decrementDay = moment((e.date)).endOf('d');
    	        $("[name='leave_from_date']").data('DateTimePicker').maxDate(decrementDay);
        	}else {
        		 $("[name='leave_from_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });
    </script>
                                    