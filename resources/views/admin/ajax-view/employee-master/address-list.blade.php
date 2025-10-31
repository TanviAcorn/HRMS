						<div class="col-sm-6 pb-2">
                            <div class="col-12 px-0 profile-display-item">
                                <h5 class="details-title">{{ trans('messages.current-address') }}</h5>
                                <p class="details-text">{{ (!empty($employeeRecordInfo->v_current_address_line_first) ? $employeeRecordInfo->v_current_address_line_first :'') }}</p>
                                <p class="details-text">{{ (!empty($employeeRecordInfo->v_current_address_line_second) ? $employeeRecordInfo->v_current_address_line_second :'') }}</p>

                                <p>{{(!empty($employeeRecordInfo->currentVillageInfo->v_village_name) ? $employeeRecordInfo->currentVillageInfo->v_village_name .', ':''). ' ' .(!empty($employeeRecordInfo->cityCurrentInfo->v_city_name) ? $employeeRecordInfo->cityCurrentInfo->v_city_name . (!empty($employeeRecordInfo->cityCurrentInfo->stateMaster->v_state_name) ? ', '.$employeeRecordInfo->cityCurrentInfo->stateMaster->v_state_name . (!empty($employeeRecordInfo->cityCurrentInfo->stateMaster->countryMaster->v_country_name) ? ', '.$employeeRecordInfo->cityCurrentInfo->stateMaster->countryMaster->v_country_name . (!empty($employeeRecordInfo->v_current_address_pincode) ? ', '.$employeeRecordInfo->v_current_address_pincode :''):'') :'') :'')}}</p>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <div class="col-12 px-0 profile-display-item">
                                <h5 class="details-title">{{ trans('messages.permanent-address') }}</h5>
                                <p class="details-text">{{ (!empty($employeeRecordInfo->v_permanent_address_line_first) ? $employeeRecordInfo->v_permanent_address_line_first :'') }}</p>
                                <p class="details-text">{{ (!empty($employeeRecordInfo->v_permanent_address_line_second) ? $employeeRecordInfo->v_permanent_address_line_second :'') }}</p>

                                <p>{{(!empty($employeeRecordInfo->permentVillageInfo->v_village_name) ? $employeeRecordInfo->permentVillageInfo->v_village_name .', ': '') .' '.  (!empty($employeeRecordInfo->cityPermanentInfo->v_city_name) ? $employeeRecordInfo->cityPermanentInfo->v_city_name . (!empty($employeeRecordInfo->cityPermanentInfo->stateMaster->v_state_name) ? ', '.$employeeRecordInfo->cityPermanentInfo->stateMaster->v_state_name . (!empty($employeeRecordInfo->cityPermanentInfo->stateMaster->countryMaster->v_country_name) ? ', '.$employeeRecordInfo->cityPermanentInfo->stateMaster->countryMaster->v_country_name . (!empty($employeeRecordInfo->v_permanent_address_pincode) ? ', '.$employeeRecordInfo->v_permanent_address_pincode :''):''):''):'')   }}</p>
                            </div>
                        </div>