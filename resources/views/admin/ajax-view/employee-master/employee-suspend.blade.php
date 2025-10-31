							
							@if( isset($suspendInfo) && (!empty($suspendInfo->dt_end_date)) && ( isset($suspendRecordId) && ( $suspendRecordId ==  0  ) ) )
							<div class="col-12">
                                <div class="form-group">
                                    <label for="current_probation_end_date" class="control-label">{{ trans('messages.current-suspension-end-date') }}</label>
                                    <p class="details-text font-weight-bold">{{ convertDateFormat($suspendInfo->dt_end_date) }}</p>
                                </div>
                            </div>
                            @endif
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="suspend_from_date">{{ trans("messages.from-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="suspend_from_date" placeholder="DD-MM-YYYY" autocomplete="off" value="{{ ( ( isset($suspendInfo) && (!empty($suspendInfo->dt_start_date)) ) ? clientDate($suspendInfo->dt_start_date) : '' )  }}" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="suspend_to_date">{{ trans("messages.to-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="suspend_to_date" placeholder="DD-MM-YYYY" autocomplete="off" value="{{ ( ( isset($suspendInfo) && (!empty($suspendInfo->dt_end_date)) ) ? clientDate($suspendInfo->dt_end_date) : '' )  }}" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="control-label" for="suspension_reason">{{ trans("messages.suspension-reason") }}<span class="text-danger">*</span></label>
                                    <textarea name="suspension_reason" class="form-control" rows="3" placeholder="{{ trans('messages.suspension-reason') }}">{{ ( ( isset($suspendInfo) && (!empty($suspendInfo->v_suspend_reason)) ) ? ($suspendInfo->v_suspend_reason) : '' )  }}</textarea>
                                </div>
                            </div>
                            <input type="hidden" name="suspend_record_id" value="{{ ( isset($suspendRecordId) ? Wild_tiger::encode($suspendRecordId) : 0 ) }}"> 