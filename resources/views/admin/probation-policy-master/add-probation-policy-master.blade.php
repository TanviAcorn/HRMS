					 	<?php
					 	if($recordType == config("constants.NOTICE_PERIOD_POLICY")){
					 		$probationPolicyName = trans('messages.notice-policy-name');
					 		$noticeDedcription = trans('messages.notice-policy-description');
					 		$noticeDuration = trans('messages.notice-policy-duration');
					 		
					 	} else {
					 		$probationPolicyName = trans('messages.probation-policy-name');
					 		$noticeDedcription = trans('messages.probation-policy-description');
					 		$noticeDuration = trans('messages.probation-policy-duration');
					 	}
					 	?>
					 	<div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="probation_policy_name" class="control-label">{{ $probationPolicyName }}<span class="text-danger">*</span></label>
                                    <input type="text" name="probation_policy_name" class="form-control" placeholder="{{ $probationPolicyName }}" value="{{ old('probation_policy_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_probation_policy_name)) ? $recordInfo->v_probation_policy_name : ''  ) ) ) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="probation_policy_description" class="control-label">{{ $noticeDedcription }}</label>
                                    <textarea rows="2" name="probation_policy_description" class="form-control" placeholder="{{ $noticeDedcription  }}" >{{ old('probation_policy_description' , ( (isset($recordInfo) && (!empty($recordInfo->v_probation_policy_description)) ? $recordInfo->v_probation_policy_description : ''  ) ) ) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="probation_policy_duration" class="control-label">{{ $noticeDuration }}<span class="text-danger">*</span></label>
                                    <input type="text" maxlength="3" onkeyup="naturalNumber(this);"  onchange="naturalNumber(this)" name="probation_policy_duration" class="form-control" placeholder="{{ $noticeDuration }}" value="{{ old('probation_policy_duration' , ( (isset($recordInfo) && (!empty($recordInfo->v_probation_period_duration)) ? $recordInfo->v_probation_period_duration : ''  ) ) ) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="months_weeks_days">{{ trans("messages.months-weeks-days") }}<span class="text-danger">*</span></label>
                                    <select class="form-control" name="months_weeks_days">
                                        <?php 
                                        if(!empty($getMonthWeeksDaysInfo)){
                                        	foreach ($getMonthWeeksDaysInfo as $key => $getMonthWeeksDays){
                                        		$selected ='';
                                        		if( (isset($recordInfo) ) && ($recordInfo->e_months_weeks_days == $key )){
                                        			$selected="selected='selected'";
                                        		}
                                        		?>
                                        		<option value='{{ $key }}' {{ $selected }}>{{ (!empty($getMonthWeeksDays) ? $getMonthWeeksDays :'') }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>

                                    </select>
                                </div>
                            </div>
                        </div>