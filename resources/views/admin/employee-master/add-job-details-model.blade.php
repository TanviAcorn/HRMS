					<div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="employee_code" class="control-label">{{ trans('messages.employee-code') }}<span class="star">*</span></label>
                                <input type="text" class="form-control" disabled name="employee_code" placeholder="{{ trans('messages.employee-code') }}" value="{{ old('employee_code' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_employee_code)) ? $employeeRecordInfo->v_employee_code : ''  ) ) ) }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="joining_date_edit" class="control-label">{{ trans('messages.joining-date') }}<span class="star">*</span></label>
                                <input type="text" class="form-control" name="joining_date_edit" placeholder="{{ trans('messages.joining-date') }}" value="{{ old('joining_date_edit' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->dt_joining_date)) ? clientDate($employeeRecordInfo->dt_joining_date) : ''  ) ) ) }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="edit_week_off_effective_date" class="control-label">{{ trans('messages.week-off-effective-date') }}<span class="star">*</span></label>
                                <input type="text" class="form-control" name="edit_week_off_effective_date" placeholder="{{ trans('messages.week-off-effective-date') }}" value="{{ old('edit_week_off_effective_date' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->dt_week_off_effective_date)) ? clientDate($employeeRecordInfo->dt_week_off_effective_date) : ''  ) ) ) }}">
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label for="leader_name_reporting_manager" class="control-label">{{ trans('messages.leader-name-reporting-manager') }}</label>
                                <select class="form-control select2" name="leader_name_reporting_manager">
                                    <option value="">{{ trans('messages.select') }}</option>
                                    <?php 
                                    if(!empty($ledaerEmployeeRecords)){
                                    	foreach ($ledaerEmployeeRecords as $ledaerEmployeeRecord){
                                    		$encodeId = Wild_tiger::encode($ledaerEmployeeRecord->i_id);
                                    		$selected = '';
                                    		if( isset($employeeRecordInfo->i_leader_id) && ( $employeeRecordInfo->i_leader_id == $ledaerEmployeeRecord->i_id ) ){
                                    			$selected = "selected='selected'";
                                    		}
                                    		?>
                                    		<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($ledaerEmployeeRecord->v_employee_full_name) ? $ledaerEmployeeRecord->v_employee_full_name .(!empty($ledaerEmployeeRecord->v_employee_code) ? ' ('.$ledaerEmployeeRecord->v_employee_code.')' :''):'') }}</option>
                                    		<?php 
                                    	}
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <?php /* ?>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="in_probation" class="control-label">{{ trans('messages.in-probation-question') }}<span class="star">*</span></label>
                                <select class="form-control" name="in_probation">
                                    <option value="">{{ trans('messages.select') }}</option>
                                   <?php 
                                   if(!empty($getSelectionYesNoRecordInfo)){
	                                   	foreach ($getSelectionYesNoRecordInfo as $key => $getSelectionYesNoRecord){
	                                   		$selected = '';
	                                   		if( isset($employeeRecordInfo->e_in_probation) && ( $employeeRecordInfo->e_in_probation == $key ) ){
	                                   			$selected = "selected='selected'";
	                                   		}
	                                   		?>
	                                   		<option value="{{ $key }}" {{ $selected }}>{{ $getSelectionYesNoRecord  }}</option>
	                                   		<?php 
	                                   	}
                                   }
                                   ?>
                                </select>
                            </div>
                        </div>
                        <?php */ ?>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="notice_period" class="control-label">{{ trans('messages.notice-period') }}<span class="star">*</span></label>
                                <select class="form-control" name="notice_period">
                                    <option value="">{{ trans('messages.select') }}</option>
                                    <?php 
                                    if(!empty($noticePeriodPolicyRecordDetails)){
                                    	foreach ($noticePeriodPolicyRecordDetails as $noticePeriodPolicyRecord){
                                    		$encodeId = Wild_tiger::encode($noticePeriodPolicyRecord->i_id);
                                    		$selected = '';
                                    		if( isset($employeeRecordInfo->i_notice_period_id) && ( $employeeRecordInfo->i_notice_period_id == $noticePeriodPolicyRecord->i_id ) ){
                                    			$selected = "selected='selected'";
                                    		}
                                    		?>
                                    		<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($noticePeriodPolicyRecord->v_probation_policy_name) ? $noticePeriodPolicyRecord->v_probation_policy_name :'') }}</option>
                                    		<?php 
                                    	}
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="notice_period" class="control-label">{{ trans('messages.recruitment-source') }}<span class="star">*</span></label>
                                <select class="form-control" name="edit_recruitment_source" onchange="showReferenceEmployeeSelection(this);">
                                    <option value="">{{ trans('messages.select') }}</option>
                                    <?php 
                                    if(!empty($recruitmentSourceDetails)){
                                    	foreach ($recruitmentSourceDetails as $recruitmentSourceDetail){
                                    		$encodeRecruitmentSourceId = Wild_tiger::encode($recruitmentSourceDetail->i_id);
                                    		$selected = '';
                                    		if( isset($employeeRecordInfo->i_recruitment_source_id) && ( $employeeRecordInfo->i_recruitment_source_id == $recruitmentSourceDetail->i_id ) ){
                                    			$selected = "selected='selected'";
                                    		}
                                    		?>
                                    		<option value="{{ $encodeRecruitmentSourceId }}" data-id="{{ $recruitmentSourceDetail->i_id }}" {{ $selected }}>{{ $recruitmentSourceDetail->v_value }}</option>
                                    		<?php 
                                    	}
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 recruitment-source-employee-div" <?php echo ( isset($employeeRecordInfo->i_recruitment_source_id) && ( $employeeRecordInfo->i_recruitment_source_id == config('constants.EMPLOYEE_RECRUITMENT_SOURCE_ID') ) ) ? '' : 'style=display:none;'  ?>>
                            <div class="form-group">
                                <label for="notice_period" class="control-label">{{ trans('messages.reference-name') }}<span class="star">*</span></label>
                                <select class="form-control select2" name="edit_reference_name">
                                    <option value="">{{ trans('messages.select') }}</option>
                                    <?php 
									if(!empty($ledaerEmployeeRecords)){
                                    	foreach ($ledaerEmployeeRecords as $ledaerEmployeeRecord){
                                    		$encodeId = Wild_tiger::encode($ledaerEmployeeRecord->i_id);
                                    		$selected = '';
                                    		if( isset($employeeRecordInfo->i_reference_emp_id) && ( $employeeRecordInfo->i_reference_emp_id == $ledaerEmployeeRecord->i_id ) ){
                                    			$selected = "selected='selected'";
                                    		}
                                    		?>
                                    		<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($ledaerEmployeeRecord->v_employee_full_name) ? $ledaerEmployeeRecord->v_employee_full_name .(!empty($ledaerEmployeeRecord->v_employee_code) ? ' ('.$ledaerEmployeeRecord->v_employee_code.')' :''):'') }}</option>
                                    		<?php 
                                    	}
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
					</div>