					<div class="row">
						<?php if(( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) ){ ?>
		                        <div class="col-sm-6">
		                            <div class="form-group">
		                                <label for="outlook_email_id" class="control-label">{{ trans('messages.outlook-email-id') }}</label>
		                                <input type="text" name="outlook_email_id" class="form-control" placeholder="{{ trans('messages.outlook-email-id') }}" value="{{ old('outlook_email_id' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_outlook_email_id)) ? $employeeRecordInfo->v_outlook_email_id : ''  ) ) ) }}">
		                            </div>
		                        </div>
		                        <div class="col-sm-6">
		                            <div class="form-group">
		                                <label for="personal_email_id" class="control-label">{{ trans('messages.personal-email-id') }}<span class="text-danger">*</span></label>
		                                <input type="text" name="personal_email_id" class="form-control" placeholder="{{ trans('messages.personal-email-id') }}" value="{{ old('personal_email_id' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_personal_email_id)) ? $employeeRecordInfo->v_personal_email_id : ''  ) ) ) }}">
		                            </div>
		                        </div>
		                        <div class="col-sm-6">
		                            <div class="form-group">
		                                <label for="contact_number" class="control-label">{{ trans('messages.contact-number') }}<span class="text-danger">*</span></label>
		                                <input type="text" name="contact_number" class="form-control" placeholder="{{ trans('messages.contact-number') }}" maxlength="15" onkeyup="onlyNumberWithSpaceAndPlusSign(this);" value="{{ old('contact_number' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_contact_no)) ? $employeeRecordInfo->v_contact_no : ''  ) ) ) }}">
		                            </div>
		                        </div>
                      	<?php }?>
                        <div class="col-12 py-3">
                            <h4 class="address-title">{{ trans('messages.emergency-contact') }}</h4>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="person-name" class="control-label">{{ trans('messages.person-name') }}</label>
                                <input type="text" name="person_name" class="form-control" placeholder="{{ trans('messages.person-name') }}" value="{{ old('person_name' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_emergency_contact_person_name)) ? $employeeRecordInfo->v_emergency_contact_person_name : ''  ) ) ) }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="person_relation" class="control-label">{{ trans('messages.person-relation') }}</label>
                                <input type="text" name="person_relation" class="form-control" placeholder="{{ trans('messages.person-relation') }}" value="{{ old('person_relation' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_emergency_contact_relation)) ? $employeeRecordInfo->v_emergency_contact_relation : ''  ) ) ) }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="person_no" class="control-label">{{ trans('messages.person-no') }}.</label>
                                <input type="text" name="person_no" maxlength="15" onkeyup="onlyNumberWithSpaceAndPlusSign(this);" class="form-control" placeholder="{{ trans('messages.person-no') }}." value="{{ old('person_no' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_emergency_contact_person_no)) ? $employeeRecordInfo->v_emergency_contact_person_no : ''  ) ) ) }}">
                            </div>
                        </div>
                    </div>