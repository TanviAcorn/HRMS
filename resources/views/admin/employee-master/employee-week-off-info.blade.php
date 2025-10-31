					<div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="weekly_off_display" class="control-label">{{ trans('messages.weekly-off') }}</label>
                                <p class="details-text font-weight-bold">{{ isset($recordInfo->weekOffInfo->v_weekly_off_name) ? $recordInfo->weekOffInfo->v_weekly_off_name : ''  }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="new_weekly_off" class="control-label">{{ ( isset($editHistory) && ($editHistory != false ) ? trans('messages.old-week-off') : trans('messages.new-week-off') )  }}<span class="star">*</span></label>
                                <select class="form-control" name="employee_weekly_off" {{  ( isset($editHistory) && ($editHistory != false ) ? 'disabled' : '' )  }}  data-old-value="{{ isset($recordInfo->i_weekoff_id) ? $recordInfo->i_weekoff_id : ''  }}">
                                    <option value="">{{ trans('messages.select') }}</option>
                                    @if(count($weekOffDetails) > 0 )
                                    	@foreach($weekOffDetails as $weekOffDetail)
                                    		@php 
                                    		$encodeWeekOffId = Wild_tiger::encode($weekOffDetail->i_id);
                                    		$selected = '';
                                    		if(!empty($historyInfo)){
                                    			if( isset($historyInfo->i_designation_id)  && ( $historyInfo->i_designation_id ==  $weekOffDetail->i_id ) ){
                                    			$selected = "selected='selected'";	
                                    		}
                                    		} else {
	                                    		if( isset($recordInfo->i_weekoff_id)  && ( $weekOffDetail->i_id ==  $recordInfo->i_weekoff_id ) ){
	                                    			$selected = "selected='selected'";	
	                                    		}
                                    		}
                                    		 
                                    		@endphp
                                    		<option value="{{ $encodeWeekOffId  }}" data-id="{{ $weekOffDetail->i_id }}" {{ $selected }}>{{ $weekOffDetail->v_weekly_off_name }}</option>				
                                    	@endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="weekly_off_effective_date" class="control-label">{{ trans('messages.effective-from') }}<span class="star">*</span></label>
                                <input type="text" class="form-control" name="weekly_off_effective_date" value="{{ ( isset($editHistory) && ($editHistory != false ) ? (!empty($historyInfo->dt_end_date) ? clientDate($historyInfo->dt_end_date) : '' ) : ( ( isset($existingRecordInfo->dt_effective_date) && (!empty($existingRecordInfo->dt_effective_date)) ) ? clientDate($existingRecordInfo->dt_effective_date) : "" ) )  }}" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="update_week_off_employee_id" value="{{ isset($recordInfo->i_id) ? Wild_tiger::encode($recordInfo->i_id) : ''  }}">
                    <input type="hidden" name="update_week_off_employee_history_id" value="{{ ( isset($historyInfo) && (!empty($historyInfo->i_id)))  ? Wild_tiger::encode($historyInfo->i_id) : ''  }}">