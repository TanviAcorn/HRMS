					<div class="row">
						<?php 
						if(( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){ ?>
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label for="employee_name" class="control-label">{{ trans('messages.employee-name') }}<span class="text-danger">*</span></label>
	                                <input type="text" name="employee_name" class="form-control" placeholder="{{ trans('messages.employee-name') }}" value="{{ old('employee_name' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_employee_name)) ? $employeeRecordInfo->v_employee_name : ''  ) ) ) }}">
	                            </div>
	                        </div>
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label for="full_name" class="control-label">{{ trans('messages.full-name') }}<span class="text-danger">*</span></label>
	                                <input type="text" name="full_name" class="form-control" placeholder="{{ trans('messages.full-name') }}" value="{{ old('full_name' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_employee_full_name)) ? $employeeRecordInfo->v_employee_full_name : ''  ) ) ) }}">
	                            </div>
	                        </div>
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label class=" control-label" for="gender">{{ trans("messages.gender") }}<span class="text-danger">*</span></label>
	                                <select class="form-control" name="gender">
	                                    <option value="">{{ trans("messages.select") }}</option>
	                                    <?php 
	                                    if(!empty($genderMasterInfo)){
	                                    	foreach ($genderMasterInfo as $key => $genderMaster){
	                                    		$selected = '';
	                                    		if( isset($employeeRecordInfo->e_gender) && ( $employeeRecordInfo->e_gender == $key ) ){
	                                    			$selected = "selected='selected'";
	                                    		}
	                                    		?>
	                                    		<option value="{{ $key }}" {{ $selected }}>{{ $genderMaster }}</option>
	                                    		<?php 
	                                    	}
	                                    }
	                                    ?>
	                                    
	                                </select>
	                            </div>
	                        </div>
                        <?php 
	                    } ?>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class=" control-label" for="blood_group">{{ trans("messages.blood-group") }}</label>
                                <select class="form-control" name="blood_group">
                                    <option value="">{{ trans("messages.select") }}</option>
                                   	<?php 
                                   	if(!empty($bloodGroupInfo)){
                                   		foreach ($bloodGroupInfo as $key => $bloodGroup){
                                   			$selected = '';
                                   			if( isset($employeeRecordInfo->v_blood_group) && ( $employeeRecordInfo->v_blood_group == $key ) ){
                                   				$selected = "selected='selected'";
                                   			}
                                   			?>
                                   			<option value="{{ $key }}" {{ $selected }}>{{ $bloodGroup }}</option>
                                   			<?php 
                                   		}
                                   	}
                                   	?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="education" class="control-label">{{ trans('messages.education') }}<span class="text-danger">*</span></label>
                                <input type="text" name="education" class="form-control" placeholder="{{ trans('messages.education') }}" value="{{ old('education' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_education)) ? $employeeRecordInfo->v_education : ''  ) ) ) }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="cgpa_percentage" class="control-label">{{ trans('messages.cgpa-percentage') }}<span class="text-danger">*</span></label>
                                <input type="text" name="cgpa_percentage" class="form-control" placeholder="{{ trans('messages.cgpa-percentage') }}" value="{{ old('cgpa_percentage' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_cgpa)) ? $employeeRecordInfo->v_cgpa : ''  ) ) ) }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class=" control-label" for="marital_status">{{ trans("messages.marital-status") }}</label>
                                <select class="form-control" name="marital_status">
                                    <option value="">{{ trans("messages.select") }}</option>
                                    <?php 
                                   	if(!empty($maritalStatusInfo)){
                                   		foreach ($maritalStatusInfo as $key => $maritalStatus){
                                   			$selected = '';
                                   			if( isset($employeeRecordInfo->e_marital_status) && ( $employeeRecordInfo->e_marital_status == $key ) ){
                                   				$selected = "selected='selected'";
                                   			}
                                   			?>
                                   			<option value="{{ $key }}" {{ $selected }}>{{ $maritalStatus }}</option>
                                   			<?php 
                                   		}
                                   	}
                                   	?>
                                </select>
                            </div>
                        </div>
                        <?php 
                        if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){?>
	                        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label for="date_of_birth" class="control-label">{{ trans('messages.date-of-birth') }}<span class="star">*</span></label>
	                                <input type="text" class="form-control primary-bd" name="date_of_birth" placeholder="{{ trans('messages.date-of-birth') }}" value="{{ old('employee_name' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->dt_birth_date)) ? clientDate($employeeRecordInfo->dt_birth_date) : ''  ) ) ) }}">
	                            </div>
	                        </div>
	                   <?php
                        } ?>
                    </div>
				 <script>
				 $(document).ready(function() {
						$("[name='date_of_birth']").datetimepicker({
							useCurrent: false,
							viewMode: 'days',
							ignoreReadonly: true,
							widgetPositioning: {
								vertical: 'bottom'
							},
							format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

						});
					});
					$(function () {
						$("[name='date_of_birth']").data('DateTimePicker').maxDate(moment().endOf('d'));
					});
				</script>