					<div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="designation_display" class="control-label">{{ trans('messages.current-designation') }}</label>
                                <p class="details-text font-weight-bold">{{ isset($recordInfo->designationInfo->v_value) ? $recordInfo->designationInfo->v_value : ''  }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="new_designation" class="control-label">{{ ( isset($editHistory) && ($editHistory != false ) ? trans('messages.old-designation') : trans('messages.new-designation') )  }} <span class="star">*</span></label>
                                <select class="form-control" name="employee_designation" {{  ( isset($editHistory) && ($editHistory != false ) ? 'disabled' : '' )  }}  data-old-value="{{ isset($recordInfo->i_designation_id) ? $recordInfo->i_designation_id : ''  }}">
                                    <option value="">{{ trans('messages.select') }}</option>
                                    @if(count($designationDetails) > 0 )
                                    	@foreach($designationDetails as $designationDetail)
                                    		@php 
                                    		$encodeDesignationId = Wild_tiger::encode($designationDetail->i_id);
                                    		$selected = '';
                                    		if(!empty($historyInfo)){
                                    			if( isset($historyInfo->i_designation_id)  && ( $historyInfo->i_designation_id ==  $designationDetail->i_id ) ){
                                    			$selected = "selected='selected'";	
                                    		}
                                    		} else {
	                                    		if( isset($recordInfo->i_designation_id)  && ( $designationDetail->i_id ==  $recordInfo->i_designation_id ) ){
	                                    			$selected = "selected='selected'";	
	                                    		}
                                    		}
                                    		 
                                    		@endphp
                                    		<option value="{{ $encodeDesignationId }}" data-id="{{ $designationDetail->i_id }}" {{ $selected }}>{{ $designationDetail->v_value }}</option>				
                                    	@endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="designation_effective_date" class="control-label">{{ trans('messages.effective-from') }}<span class="star">*</span></label>
                                <input type="text" class="form-control" value="{{ ( isset($editHistory) && ($editHistory != false ) ? (!empty($historyInfo->dt_end_date) ? clientDate($historyInfo->dt_end_date) : '' ) : ( ( isset($existingRecordInfo->dt_effective_date) && (!empty($existingRecordInfo->dt_effective_date)) ) ? clientDate($existingRecordInfo->dt_effective_date) : "" ) )  }}" name="designation_effective_date" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="update_designation_employee_id" value="{{ isset($recordInfo->i_id) ? Wild_tiger::encode($recordInfo->i_id) : ''  }}">
                    <input type="hidden" name="update_designation_employee_history_id" value="{{ ( isset($historyInfo) && (!empty($historyInfo->i_id)))  ? Wild_tiger::encode($historyInfo->i_id) : ''  }}">