					<div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="shift_display" class="control-label">{{ trans('messages.current-shift') }}</label>
                                <p class="details-text font-weight-bold">{{ isset($recordInfo->shiftInfo->v_shift_name) ? $recordInfo->shiftInfo->v_shift_name : ''  }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="new_shift" class="control-label">{{ ( isset($editHistory) && ($editHistory != false ) ? trans('messages.old-shift') : trans('messages.new-shift') )  }}<span class="star">*</span></label>
                                <select class="form-control" name="employee_shift" {{  ( isset($editHistory) && ($editHistory != false ) ? 'disabled' : '' )  }} data-old-value="{{ isset($recordInfo->i_shift_id	) ? $recordInfo->i_shift_id  : ''  }}">
                                    <option value="">{{ trans('messages.select') }}</option>
                                    @if(count($shiftDetails) > 0 )
                                    	@foreach($shiftDetails as $shiftDetail)
                                    		@php 
                                    		$encodeShiftId = Wild_tiger::encode($shiftDetail->i_id);
                                    		$selected = '';
                                    		if(!empty($historyInfo)){
                                    			if( isset($historyInfo->i_designation_id)  && ( $historyInfo->i_designation_id ==  $shiftDetail->i_id ) ){
                                    			$selected = "selected='selected'";	
                                    		}
                                    		} else {
	                                    		if( isset($recordInfo->i_shift_id)  && ( $shiftDetail->i_id ==  $recordInfo->i_shift_id ) ){
	                                    			$selected = "selected='selected'";	
	                                    		}
                                    		}
                                    		 
                                    		@endphp
                                    		<option value="{{ $encodeShiftId  }}" data-id="{{ $shiftDetail->i_id }}" {{ $selected }}>{{ $shiftDetail->v_shift_name }}</option>				
                                    	@endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        @php
                        $endDateTitle = trans('messages.start-date');
                        if( ( isset($editHistory) ) && ($editHistory != false ) ){
                        	$endDateTitle = trans('messages.end-date');
                        }
                        @endphp
                        @if( ( isset($editHistory) ) && ( $editHistory != false ) && (  $editRecordType == config('constants.SHIFT_RECORD_TYPE') ) )
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="designation_effective_from_date" class="control-label">{{ trans('messages.start-date') }}<span class="star">*</span></label>
                                <input type="text" class="form-control" value="{{ ( ( isset($editHistory) && ( $editHistory != false ) ) ? (!empty($historyInfo->dt_start_date) ? clientDate( $historyInfo->dt_start_date ) : '' ) : '' ) }}" name="shift_effective_from_date" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
                        @endif
                        @if( ( ( isset($editHistory) ) && ( $editHistory != false ) && (  $editRecordType == config('constants.SHIFT_RECORD_TYPE') ) && (!empty($historyInfo->dt_end_date)) ) || ( !isset($editHistory) ) )
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="shift_effective_date" class="control-label">{{  $endDateTitle  }}<span class="star">*</span></label>
                                <input type="text" class="form-control" name="shift_effective_date" value="{{ ( isset($editHistory) && ($editHistory != false ) ? (!empty($historyInfo->dt_end_date) ? clientDate($historyInfo->dt_end_date) : '' ) : ( ( isset($existingRecordInfo->dt_effective_date) && (!empty($existingRecordInfo->dt_effective_date)) ) ? clientDate($existingRecordInfo->dt_effective_date) : "" ) )  }}" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
                        @endif
                    </div>
                    <input type="hidden" name="update_shift_employee_id" value="{{ isset($recordInfo->i_id) ? Wild_tiger::encode($recordInfo->i_id) : ''  }}">
                    <input type="hidden" name="update_shift_employee_history_id" value="{{ ( isset($historyInfo) && (!empty($historyInfo->i_id)))  ? Wild_tiger::encode($historyInfo->i_id) : ''  }}">