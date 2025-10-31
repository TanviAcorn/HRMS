					<div class="row ">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="team_display" class="control-label">{{ trans('messages.current-team') }}</label>
                                <p class="details-text font-weight-bold">{{ isset($recordInfo->teamInfo->v_value) ? $recordInfo->teamInfo->v_value : ''  }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="new_team" class="control-label">{{ ( isset($editHistory) && ($editHistory != false ) ? trans('messages.old-team') : trans('messages.new-team') )  }}<span class="star">*</span></label>
                                <select class="form-control" name="employee_team" {{  ( isset($editHistory) && ($editHistory != false ) ? 'disabled' : '' )  }} data-old-value="{{ isset($recordInfo->i_team_id	) ? $recordInfo->i_team_id	 : ''  }}">
                                    <option value="">{{ trans('messages.select') }}</option>
                                    @if(count($teamDetails) > 0 )
                                    	@foreach($teamDetails as $teamDetail)
                                    		@php 
                                    		$encodeTeamId = Wild_tiger::encode($teamDetail->i_id);
                                    		$selected = '';
                                    		if(!empty($historyInfo)){
                                    			if( isset($historyInfo->i_designation_id)  && ( $historyInfo->i_designation_id ==  $teamDetail->i_id ) ){
                                    			$selected = "selected='selected'";	
                                    		}
                                    		} else {
	                                    		if( isset($recordInfo->i_team_id)  && ( $teamDetail->i_id ==  $recordInfo->i_team_id ) ){
	                                    			$selected = "selected='selected'";	
	                                    		}
                                    		}
                                    		 
                                    		@endphp
                                    		<option value="{{ $encodeTeamId }}" data-id="{{ $teamDetail->i_id }}" {{ $selected }}>{{ $teamDetail->v_value }}</option>				
                                    	@endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="team_effective_date" class="control-label">{{ trans('messages.effective-from') }}<span class="star">*</span></label>
                                <input type="text" value="{{ ( isset($editHistory) && ($editHistory != false ) ? (!empty($historyInfo->dt_end_date) ? clientDate($historyInfo->dt_end_date) : '' ) : ( ( isset($existingRecordInfo->dt_effective_date) && (!empty($existingRecordInfo->dt_effective_date)) ) ? clientDate($existingRecordInfo->dt_effective_date) : "" ) )  }}" class="form-control" name="team_effective_date" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="update_team_employee_id" value="{{ isset($recordInfo->i_id) ? Wild_tiger::encode($recordInfo->i_id) : ''  }}">
                    <input type="hidden" name="update_team_employee_history_id" value="{{ ( isset($historyInfo) && (!empty($historyInfo->i_id)))  ? Wild_tiger::encode($historyInfo->i_id) : ''  }}">
                    <input type="hidden" name="update_team_employee_history_start_date" value="{{ ( isset($historyInfo) && (!empty($historyInfo->dt_start_date)))  ? ($historyInfo->dt_start_date) : ''  }}">