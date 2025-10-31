					
					<div class="row">
                        <div class="col-12 col-sm-4">
                            <label class="control-label">{{ trans("messages.employee-name") }}</label>
                            <p>{{(!empty($employeeRecordInfo->employeeInfo->v_employee_full_name) ? $employeeRecordInfo->employeeInfo->v_employee_full_name .(!empty($employeeRecordInfo->employeeInfo->v_employee_code) ? ' (' .$employeeRecordInfo->employeeInfo->v_employee_code .')' : '' ) : '')}}<br> {{(!empty($employeeRecordInfo->employeeInfo->v_contact_no) ? $employeeRecordInfo->employeeInfo->v_contact_no :'')}}</p>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="control-label">{{ trans("messages.date") }} <br> {{ trans("messages.from-time-to-time") }} <br> {{ trans("messages.no-of-hours") }}</label>
                            <p>{{(!empty($employeeRecordInfo->dt_time_off_date) ? convertDateFormat($employeeRecordInfo->dt_time_off_date) :'')}} <br>{{ (!empty($employeeRecordInfo->t_from_time) ? clientTime($employeeRecordInfo->t_from_time) .(!empty($employeeRecordInfo->t_to_time) ? ' - '. clientTime($employeeRecordInfo->t_to_time) :'') :'')}} <br> {{ (!empty($totalHour) ? $totalHour :'')}}</p>
                        </div>
                       @if( isset($employeeRecordInfo->e_record_type) && ( $employeeRecordInfo->e_record_type == config('constants.ADJUSTMENT_STATUS')) )
                        <div class="col-12 col-sm-4">
                            <label class="control-label">{{ trans("messages.back-date") }} <br> {{ trans("messages.from-time-to-time") }} <br> {{ trans("messages.no-of-hours") }}</label>
                            <p>{{(!empty($employeeRecordInfo->dt_time_off_back_date) ? convertDateFormat($employeeRecordInfo->dt_time_off_back_date) :'')}} <br>{{ (!empty($employeeRecordInfo->t_from_back_time) ? clientTime($employeeRecordInfo->t_from_back_time) .(!empty($employeeRecordInfo->t_to_back_time) ? ' - '. clientTime($employeeRecordInfo->t_to_back_time) :'') :'')}} <br> {{ (!empty($totalBackHour) ? $totalBackHour :'')}} </p>
                        </div>
                        @endif
                        
                        <div class="col-sm-4 col-6">
                            <label class="control-label">{{ trans("messages.type") }}</label>
                            <p>{{(!empty($employeeRecordInfo->e_record_type) ? $employeeRecordInfo->e_record_type :'')}}</p>
                        </div>
                        <div class="col-12">
                            <label class="control-label">{{trans("messages.note")}}</label>
                            <p>{{(!empty($employeeRecordInfo->v_remark) ? $employeeRecordInfo->v_remark :'')}}</p>
                        </div>
                        @if( isset($employeeRecordInfo->e_status) && ( $employeeRecordInfo->e_status == config('constants.PENDING_STATUS') ) )
                        <div class="col-12" <?php echo ( isset($requestStatus) && ($requestStatus == config('constants.VIEW_RECORD')) ? 'style="display:none"' : '' )  ?>>
                            <label class="form-label control-label" name="reason">{{trans("messages.reason")}}<span class="text-danger">*</span></label>
                            <textarea name="approve_reject_time_off_reason" cols="30" rows="3" class="form-control"></textarea>
                        </div>
                       <?php /*
                        <div class="col-12">
                            <label class="form-label control-label" name="reason">{{trans("messages.reason")}}</label>
                            <textarea disabled style="resize:none;"  cols="30" rows="3" class="form-control">{{ (!empty($employeeRecordInfo->v_approve_reject_remark) ? $employeeRecordInfo->v_approve_reject_remark :'')}}</textarea>
                        </div>
                       */?>
                      	@else
	                        @if( isset($employeeRecordInfo->e_status) && ( $employeeRecordInfo->e_status == config('constants.APPROVED_STATUS') ) && isset($requestStatus) && $requestStatus != config('constants.VIEW_RECORD') )
	                        <div class="col-12">
	                            <label class="form-label control-label" name="reason">{{trans("messages.reason")}}<span class="text-danger">*</span></label>
	                            <textarea name="approve_reject_time_off_reason" cols="30" rows="3" class="form-control"></textarea>
	                        </div>
	                        @else
		                        <div class="col-12">
		                            <label class="form-label control-label" name="reason">{{trans("messages.reason")}}</label>
		                            <textarea disabled style="resize:none;"  cols="30" rows="3" class="form-control">{{ (!empty($employeeRecordInfo->v_approve_reject_remark) ? $employeeRecordInfo->v_approve_reject_remark :'')}}</textarea>
		                        </div>
	                        @endif
	                   @endif
                    </div>