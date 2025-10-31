					<div class="row pb-0 pt-3">
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.outlook-email-id') }}</h5>
                            @if(!empty($employeeRecordInfo->v_outlook_email_id))
                            <p class="details-text"><a href="mailto:{{ $employeeRecordInfo->v_outlook_email_id }}" class="details-text-link">{{ $employeeRecordInfo->v_outlook_email_id }}</a></p>
                            @endif
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.personal-email-id') }}</h5>
                            @if(!empty($employeeRecordInfo->v_personal_email_id))	
                            <a href="mailto:{{ $employeeRecordInfo->v_personal_email_id }}" class="details-text-link">{{ $employeeRecordInfo->v_personal_email_id }}</a>
                            @endif
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.contact-number") }}</h5>
							@if(!empty($employeeRecordInfo->v_contact_no))
                            <p class="details-text"><a href="tel:{{ $employeeRecordInfo->v_contact_no }}" class="details-text-link">{{ $employeeRecordInfo->v_contact_no }}</a></p>
                            @endif
                        </div>
                    </div>
                    <div class="row pb-0">
                        <div class="col-12 mb-2">
                            <div class="w-100 py-2">
                                <h5 class="profile-details-title profile-details-title-2">{{ trans("messages.emergency-contact") }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.person-name") }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->v_emergency_contact_person_name) ? $employeeRecordInfo->v_emergency_contact_person_name :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans("messages.person-relation") }}</h5>
                            <p class="details-text">{{ (!empty($employeeRecordInfo->v_emergency_contact_relation) ? $employeeRecordInfo->v_emergency_contact_relation :'') }}</p>
                        </div>
                        <div class="col-sm-6 profile-display-item">
                            <h5 class="details-title">{{ trans('messages.person-no') }}.</h5>
                            <p class="details-text"><a href="tel:+919292992929" class="details-text">{{ (!empty($employeeRecordInfo->v_emergency_contact_person_no) ? $employeeRecordInfo->v_emergency_contact_person_no :'') }}</a></p>
                        </div>
                    </div>