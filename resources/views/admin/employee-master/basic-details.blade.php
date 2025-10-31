<div class="step-panel-class">
    <div class="d-flex step-panel-attribute align-items-center">
        <div class="panel-attribute">
            <h3 class="panel-title"><i class="fa fa-info-circle my-profile-class" aria-hidden="true"></i>{{trans('messages.basic-details')}}</h3>
        </div>
        <div class="step-btn">
            <div class="d-flex align-items-center">
                <div class="btn-next">
                    <div class="btn-class"><button type="button" onclick="basicFormValidationDetails(this);" data-tab-name="step2" class="default-btn tab-next-btn" title="{{ trans('messages.next') }}">{{trans('messages.next')}}</button></div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-items checkbox-panel add-employee-check">
        <div class="row">
            <div class="col-xl-2 col-sm-6">
                <div class="form-group mb-0">
                    <label for="auto_calculate_employee_code" class="lable-control d-block">{{ trans('messages.auto-calculate-employee-code') }}</label>
                    <div class="form-check form-check-inline pb-2">
                        <label class="checkbox" for="auto_calculate_employee_code">
   						<input type="checkbox" name="auto_calculate_employee_code" value="{{ config('constants.SELECTION_YES') }}"  onclick="employeeCodeInfo(this)"  checked id="auto_calculate_employee_code"><span class="checkmark"></span></label>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="employee_code" class="lable-control">{{ trans('messages.employee-code') }}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control employee-master-code-info-list" name="employee_code" readonly placeholder="{{ trans('messages.employee-code') }}" value="{{ ( isset($employeeCode) ? $employeeCode : '' ) }}">
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="employee_name" class="lable-control">{{ trans('messages.employee-name') }}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="employee_name" placeholder="{{ trans('messages.employee-name') }}">
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="full_name" class="lable-control">{{ trans('messages.full-name') }}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="full_name" placeholder="{{ trans('messages.full-name') }}">
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="gender" class="lable-control">{{ trans('messages.gender') }}<span class="text-danger">*</span></label>
                    <select class="form-control" name="gender">
                        <option value="">{{ trans("messages.select") }}</option>
                        <?php
                        if (!empty($genderRecordDetails)) {
                            foreach ($genderRecordDetails as $genderRecordDetail) { ?>
                                <option value="{{ $genderRecordDetail }}">{{ $genderRecordDetail }}</option>
                        <?php }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="blood_group" class="lable-control">{{ trans('messages.blood-group') }}</label>
                    <select class="form-control" name="blood_group">
                        <option value="">{{ trans("messages.select") }}</option>
                        <?php
                        if (!empty($bloodGroupRecordDetails)) {
                            foreach ($bloodGroupRecordDetails as $bloodGroupRecordDetail) { ?>
                                <option value="{{ $bloodGroupRecordDetail }}">{{ $bloodGroupRecordDetail }}</option>
                        <?php }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="date_of_birth" class="lable-control">{{ trans('messages.date-of-birth') }}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="date_of_birth" placeholder="DD-MM-YYYY" autocomplete="off">
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="outlook_email_id" class="lable-control">{{ trans('messages.outlook-email-id') }}</label>
                    <input type="text" class="form-control" name="outlook_email_id" placeholder="{{ trans('messages.outlook-email-id') }}">
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="personal_email_id" class="lable-control">{{ trans('messages.personal-email-id') }}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="personal_email_id" placeholder="{{ trans('messages.personal-email-id') }}">
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="contact_number" class="lable-control">{{ trans('messages.contact-number') }}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="contact_number" maxlength="15" onkeyup="onlyNumberWithSpaceAndPlusSign(this);" placeholder="{{ trans('messages.contact-number') }}">
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="education" class="lable-control">{{ trans('messages.education') }}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="education"  placeholder="{{ trans('messages.education') }}">
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="cgpa_percentage" class="lable-control">{{ trans('messages.cgpa-percentage') }}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="cgpa_percentage"  placeholder="{{ trans('messages.cgpa-percentage') }}">
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="marital_status" class="lable-control">{{ trans('messages.marital-status') }}</label>
                    <select name="marital_status" class="form-control">
                    	<option value="">{{ trans('messages.select') }}</option>
                    	@if(count($maritalStatusInfo) > 0 )
                    		@foreach($maritalStatusInfo as $maritalStatusKey =>  $maritalStatus)
                    			<option value="{{ $maritalStatusKey }}">{{ $maritalStatus }}</option>
                    		@endforeach
                    	@endif
                    </select>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("[name='date_of_birth']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            showClear: true,
            showClose: true,
            ignoreReadonly: true,
            widgetPositioning: {
                vertical: 'top'
            },
            icons: {
                clear: 'fa fa-trash',
                Close: 'fa fa-trash',
            },
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

        });
    });
    $(function() {
        $("[name='date_of_birth']").data('DateTimePicker').maxDate(moment().endOf('d'));
    });
</script>