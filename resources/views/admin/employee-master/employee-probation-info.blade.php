					@if( empty($recordInfo->i_probation_period_id) )
					<div class="row">	
                        <?php /* ?>
                        <div class="col-sm-6 apply-leave">
                            <div class="form-group">
                                <div><label for="probation_status" class="control-label">{{ trans('messages.probation-status') }}</label></div>
                                <input class="custom-check" type="radio" name="start_probation" id="start_probation" checked>
                                <label class="custom-check-label first-half mt-0" for="start_probation">{{ trans('messages.start') }}</label>
                            </div>
                        </div>
                        <?php */ ?>
                        <div class="col-sm-12">
                        	<div class="form-group">
                        		<label for="probation_period" class="control-label">{{ trans('messages.probation-period') }}<span class="star">*</span></label>
                        		<select name="probation_policy_id" class="form-control" onchange="calculationProbationEndDate(this);" data-joining-date="{{ $empJoiningDate }}">
                        			<option value="">{{ trans('messages.select') }}</option>
                        			@if(count($probationPolicyRecordDetails) > 0 )
                        				@foreach($probationPolicyRecordDetails as $probationPolicyRecordDetail)
                        					<option value="{{ Wild_tiger::encode($probationPolicyRecordDetail->i_id) }}" data-duration="{{ $probationPolicyRecordDetail->e_months_weeks_days }}" data-days="{{ $probationPolicyRecordDetail->v_probation_period_duration }}" >{{ (!empty($probationPolicyRecordDetail->v_probation_policy_name ) ? $probationPolicyRecordDetail->v_probation_policy_name :'')}}</option>		
                        				@endforeach
                        			@endif
                        		</select>
                        	</div>
                        </div>
                     </div>
                     @endif
                     <div class="row edit-probation-div" style="<?php echo (!empty($recordInfo->i_probation_period_id) ? '' : 'style=display:none;') ?>"> 
                        @if( !empty($recordInfo->i_probation_period_id) )
                        <div class="col-sm-7 apply-leave">
                            <div class="form-group">
                                <div><label for="probation_status" class="control-label">{{ trans('messages.probation-status') }}</label></div>
                                <input class="custom-check" type="radio" name="probation_status" id="end_probation_status" onclick="setProbationMinMaxDate(this);" checked value="{{ config('constants.END_PROBATION') }}">
                                <label class="custom-check-label first-half mt-0" for="end_probation_status">{{ trans('messages.end-probation') }}</label>
                                <input class="custom-check" type="radio" name="probation_status" id="extend_probation_status"  onclick="setProbationMinMaxDate(this);" value="{{ config('constants.EXTEND_PROBATION') }}"> 
                                <label class="custom-check-label second-half mt-0" for="extend_probation_status">{{ trans('messages.extend-probation') }}</label>
                            </div>
                        </div>
                        @endif
                       @if( !empty($currentProbationEndDate) )
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="current_probation_end_date" class="control-label">{{ trans('messages.current-probation-end-date') }}</label>
                                <p class="details-text font-weight-bold">{{ convertDateFormat($currentProbationEndDate) }} </p>
                                <input type="hidden" name="current_probation_end_date"  value="{{ $currentProbationEndDate }}">
                                <input type="hidden" name="emp_joining_date" value="{{ ( isset($recordInfo->dt_joining_date) ? $recordInfo->dt_joining_date : '' ) }}">
                            </div>
                        </div>
                        @endif
                        
                        @php
                        $probationDisplayDate = '';
                        if( isset($currentProbationEndDate) && (!empty($currentProbationEndDate)) ){
                        	if(strtotime(date('Y-m-d')) >= strtotime($currentProbationEndDate) ){
                        		$probationDisplayDate = $currentProbationEndDate;
                        	}
                        }	
                        @endphp
                        
                        
                        @if(isset($allowedMinDate))
                        <input type="hidden" name="allowed_min_extend_date" value="{{ ( $allowedMinDate ) }}">
                        @endif
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="probation_end_date" class="control-label">{{ trans('messages.probation-end-date') }}<span class="star">*</span></label>
                                <input type="text" class="form-control" <?php echo ( empty($recordInfo->i_probation_period_id) ? 'readonly' : '' ) ?> value="{{ (!empty($probationDisplayDate) ? clientDate($probationDisplayDate) : '' ) }}" name="probation_end_date" placeholder="DD-MM-YYYY" >
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="remarks" class="control-label">{{ trans('messages.remarks') }}</label>
                                <textarea name="probation_remark" cols="30" rows="2" class="form-control" placeholder="{{ trans('messages.remarks') }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="update_probation_employee_id" value="{{ isset($recordInfo->i_id) ? Wild_tiger::encode($recordInfo->i_id) : ''  }}">