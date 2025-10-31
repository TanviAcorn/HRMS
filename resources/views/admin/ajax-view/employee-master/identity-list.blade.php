					<div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.aadhaar-number') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->v_aadhar_no) ? $employeeRecordInfo->v_aadhar_no :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.pan') }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->v_pan_no) ? $employeeRecordInfo->v_pan_no :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.uan-number") }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->v_uan_no) ? $employeeRecordInfo->v_uan_no :'') }}</p>
                        </div>