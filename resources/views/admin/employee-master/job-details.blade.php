<div class="step-panel-class">
    <div class="d-flex step-panel-attribute align-items-center">
        <div class="panel-attribute">
            <h3 class="panel-title"><i class="fa fa-suitcase my-profile-class" aria-hidden="true"></i> {{trans('messages.job-details')}}</h3>
        </div>
        <div class="step-btn">
            <div class="d-flex align-items-center">
                <div class="btn-preview">
                    <div class="btn-class"><button type="button" class="default-btn prev-step" data-tab-name="step2" title="{{ trans('messages.previous') }}">{{trans('messages.previous')}}</button></div>
                </div>
                <div class="btn-next">
                    <div class="btn-class"><button type="button" onclick="jobFormValidationDetails(this);" data-tab-name="step4" class="default-btn tab-next-btn" title="{{ trans('messages.next') }}">{{trans('messages.next')}}</button></div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-items">
        <div class="row dependant-field-selection">
            <div class="col-xl-3 col-sm-6">
                <div class="form-group ">
                    <label for="joining_date" class="lable-control">{{ trans('messages.joining-date') }}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="joining_date" placeholder="DD-MM-YYYY">
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="designation" class="lable-control">{{ trans('messages.designation') }}<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="flex-fill flex-fill-demo fill-width">
                            <select class="form-control designation-list select2" name="designation">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if (!empty($designationRecordDetails))
                                    @foreach ($designationRecordDetails as $designationRecordDetail)
                                    @php $designationyEncodeId = Wild_tiger::encode($designationRecordDetail->i_id); @endphp
                                    <option value="{{$designationyEncodeId}}">{{ (!empty($designationRecordDetail->v_value ) ? $designationRecordDetail->v_value :'')}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="input-group-append">
                         <button type="button" title="{{ trans('messages.add') }}" onclick="openLookupModal(this)" data-lookup-module="{{ config('constants.SELECTION_NO')}}" data-module-name="{{ config('constants.DESIGNATION_LOOKUP')}}" class="quick-add-btn bg-theme text-white border-0 px-3"><i class="fas fa-plus text-white"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="sub_designation" class="lable-control">{{ __('Sub Designation') }}</label>
                    <select class="form-control sub-designation-list select2" name="sub_designation">
                        <option value="">{{ trans('messages.select') }}</option>
                    </select>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="team" class="lable-control">{{ trans('messages.team') }}<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="flex-fill flex-fill-demo fill-width">
                            <select class="form-control team-list select2" name="team">
                                <option value="">{{ trans("messages.select") }}</option>
                                @if (!empty($teamRecordDetails))
                                    @foreach ($teamRecordDetails as $teamRecordDetail)
                                    @php $teamEncodeId = Wild_tiger::encode($teamRecordDetail->i_id); @endphp
                                    <option value="{{$teamEncodeId}}">{{ (!empty($teamRecordDetail->v_value ) ? $teamRecordDetail->v_value :'')}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="input-group-append">
                         <button type="button" title="{{ trans('messages.add') }}" onclick="openLookupModal(this)" data-lookup-module="{{ config('constants.SELECTION_NO')}}" data-module-name="{{ config('constants.TEAM_LOOKUP')}}" class="quick-add-btn bg-theme text-white border-0 px-3"><i class="fas fa-plus text-white"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="leader_name_reporting_manager" class="lable-control">{{ trans('messages.leader-name-reporting-manager') }}</label>
                    <select class="form-control select2" name="leader_name_reporting_manager">
                        <option value="">{{ trans('messages.select') }}</option>
	                        @if (!empty($leaderRecordDetails))
		                        @foreach ($leaderRecordDetails as $leaderRecordDetail)
		                        @php $leaderEncodeId = Wild_tiger::encode($leaderRecordDetail->i_id); @endphp
		                        <option value="{{$leaderEncodeId}}">{{ (!empty($leaderRecordDetail->v_employee_full_name) ? $leaderRecordDetail->v_employee_full_name .(!empty($leaderRecordDetail->v_employee_code) ? ' (' .$leaderRecordDetail->v_employee_code .')' : '' ): '' ) }}</option>
		                        @endforeach
	                        @endif
                    </select>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="form-group">
                    <label for="recruitment_source" class="lable-control">{{ trans('messages.recruitment-source') }}<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="flex-fill flex-fill-demo fill-width">
                            <select class="form-control recruitment-list" name="recruitment_source" onclick="employeeRecruitmentSourceInfo(this);">
                                <option value="">{{ trans("messages.select") }}</option>
	                                @if (!empty($recruitmentSourceRecordDetails))
		                                @foreach ($recruitmentSourceRecordDetails as $recruitmentSourceRecordDetail)
		                                @php  $recruitmentSourceEncodeId = Wild_tiger::encode($recruitmentSourceRecordDetail->i_id); @endphp
		                                <option value="{{$recruitmentSourceEncodeId}}" data-recruitment-source="{{(isset($recruitmentSourceRecordDetail->i_id) ? $recruitmentSourceRecordDetail->i_id :'')}}">{{ (!empty($recruitmentSourceRecordDetail->v_value ) ? $recruitmentSourceRecordDetail->v_value :'')}}</option>
		                                @endforeach
	                                @endif
                            </select>
                        </div>
                        <div class="input-group-append">
                          <button type="button" title="{{ trans('messages.add') }}" onclick="openLookupModal(this)" data-lookup-module="{{ config('constants.SELECTION_NO')}}" data-module-name="{{ config('constants.RECRUITMENT_SOURCE_LOOKUP')}}" class="quick-add-btn bg-theme text-white border-0 px-3"><i class="fas fa-plus text-white"></i></button>
                        </div>
                    </div>
                        <!-- <label id="recruitment_source-error" class="invalid-input" for="recruitment_source"></label> -->
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 recruitment-reference-master-div" style="display: none;"">
                <div class=" form-group">
                <label for="reference_name" class="lable-control">{{ trans('messages.reference-name') }}<span class="text-danger">*</span></label>
                <select class="form-control select2" name="reference_name">
                    <option value="">{{ trans("messages.select") }}</option>
	                    @if(!empty($referenceEmployeeRecords))
		                    @foreach ($referenceEmployeeRecords as $referenceEmployeeRecord)
		                    @php $encodeId = Wild_tiger::encode($referenceEmployeeRecord->i_id); @endphp
		                    <option value="{{ $encodeId }}">{{ (!empty($referenceEmployeeRecord->v_employee_full_name) ? $referenceEmployeeRecord->v_employee_full_name .(!empty($referenceEmployeeRecord->v_employee_code) ? ' (' .$referenceEmployeeRecord->v_employee_code .')' : '' ):'')}}</option>
		                    @endforeach
	                    @endif
                </select>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="form-group">
                <label for="shift" class="lable-control">{{ trans('messages.shift') }}<span class="text-danger">*</span></label>
                <select class="form-control" name="shift">
                    <option value="">{{ trans('messages.select') }}</option>
	                    @if(!empty($shifyDetails))
		                    @foreach($shifyDetails as $shifyDetail)
		                    <option value="{{ (!empty($shifyDetail->i_id) ? Wild_tiger::encode($shifyDetail->i_id) : 0 )}}">{{ (!empty($shifyDetail->v_shift_name) ? $shifyDetail->v_shift_name . ' ('.$shifyDetail->e_shift_type.')' :'') }}</option>
		                    @endforeach
	                    @endif
                </select>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="form-group">
                <label for="new_weekly_off" class="lable-control">{{ trans('messages.weekly-off') }}<span class="text-danger">*</span></label>
                <select class="form-control" name="new_weekly_off">
                    <option value="">{{ trans('messages.select') }}</option>
	                    @if(!empty($weekOffDetails))
		                    @foreach($weekOffDetails as $weekOffDetail)
		                    <option value="{{ (!empty($weekOffDetail->i_id) ? Wild_tiger::encode($weekOffDetail->i_id) : 0 )}}">{{ (!empty($weekOffDetail->v_weekly_off_name) ? $weekOffDetail->v_weekly_off_name :'') }}</option>
		                    @endforeach
	                    @endif
                </select>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
                <div class="form-group ">
                    <label for="week_off_effective_date" class="lable-control">{{ trans('messages.week-off-effective-date') }}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="week_off_effective_date" placeholder="DD-MM-YYYY" >
                </div>
            </div>
        <div class="col-xl-3 col-sm-6">
            <div class="form-group">
                <label for="probation_period" class="lable-control">{{ trans('messages.probation-period') }}</label>
                <select class="form-control" name="probation_period">
                    <option value="">{{ trans("messages.select") }}</option>
	                    @if (!empty($probationPolicyRecordDetails))
                            @foreach ($probationPolicyRecordDetails as $probationPolicyRecordDetail)
		                    @php  $probationPolicyEncodeId =  Wild_tiger::encode($probationPolicyRecordDetail->i_id); @endphp
		                    <option value="{{$probationPolicyEncodeId}}">{{ (!empty($probationPolicyRecordDetail->v_probation_policy_name ) ? $probationPolicyRecordDetail->v_probation_policy_name :'')}}</option>
		                    @endforeach
	                    @endif
                </select>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="form-group">
                <label for="notice_period" class="lable-control">{{ trans('messages.notice-period') }}<span class="text-danger">*</span></label>
                <select class="form-control" name="notice_period">
                    <option value="">{{ trans("messages.select") }}</option>
	                    @if (!empty($noticePeriodPolicyRecordDetails))
		                    @foreach ($noticePeriodPolicyRecordDetails as $noticePeriodPolicyRecordDetail)
		                    @php  $noticePeriodPolicyEncodeId = Wild_tiger::encode($noticePeriodPolicyRecordDetail->i_id); @endphp
		                    <option value="{{$noticePeriodPolicyEncodeId}}">{{ (!empty($noticePeriodPolicyRecordDetail->v_probation_policy_name ) ? $noticePeriodPolicyRecordDetail->v_probation_policy_name :'')}}</option>
		                    @endforeach
	                    @endif
                </select>
            </div>
        </div>
    </div>
</div>
</div>