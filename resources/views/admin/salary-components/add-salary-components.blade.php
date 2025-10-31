						<div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="component_name" class="control-label">{{ trans('messages.component-name') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="component_name" class="form-control" placeholder="{{ trans('messages.ex') }}{{ trans('messages.basic-salary-hra') }}" value="{{ old('component_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_component_name)) ? $recordInfo->v_component_name : ''  ) ) ) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="component_description" class="control-label">{{ trans('messages.component-description') }}</label>
                                    <textarea class="form-control" name="component_description" rows="4" placeholder="{{ trans('messages.component-description') }}">{{ ( (isset($recordInfo) && (!empty($recordInfo->v_component_description)) ? $recordInfo->v_component_description : ''  ) )  }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="salary_components_type" class="control-label">{{ trans("messages.type") }}<span class="text-danger">*</span></label>
                                    <div class="radio-boxes form-row p-1 bg-white">
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" onclick="showPFCalculationSelection(this);"  name="salary_components_type" id="salary_components_type_yes" value="{{ config('constants.SALARY_COMPONENT_TYPE_EARNING')}}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_salary_components_type)) && ( $recordInfo->e_salary_components_type ==  config('constants.SALARY_COMPONENT_TYPE_EARNING') ) ) ? 'checked' : '' ) }}>
                                                <label class="form-check-label custom-type-label btn stock-btn" for="salary_components_type_yes">{{ trans('messages.earning') }}</label>
                                            </div>
                                        </div>
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" onclick="showPFCalculationSelection(this);" name="salary_components_type" id="salary_components_type_no" value="{{ config('constants.SALARY_COMPONENT_TYPE_DEDUCTION')}}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_salary_components_type)) && ( $recordInfo->e_salary_components_type ==  config('constants.SALARY_COMPONENT_TYPE_DEDUCTION') ) ) ? 'checked' : '' ) }}>
                                                <label class="form-check-label custom-type-label btn stock-btn" for="salary_components_type_no">{{ trans('messages.deduction') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 consider-for-pf-calculation-div" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_salary_components_type)) && ( $recordInfo->e_salary_components_type ==  config('constants.SALARY_COMPONENT_TYPE_EARNING') ) ) ? '' : 'style=display:none' ) }}>
                                <div class="form-group">
                                    <label for="consider_for_pf_calculation" class="control-label">{{ trans("messages.consider-under-pf-calculation") }}<span class="text-danger">*</span></label>
                                    <div class="radio-boxes form-row p-1 bg-white">
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="consider_for_pf_calculation" id="consider_for_pf_calculation_yes" value="{{ config('constants.SELECTION_YES')}}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_consider_for_pf_calculation)) && ( $recordInfo->e_consider_for_pf_calculation ==  config('constants.SELECTION_YES') ) ) ? 'checked' : '' ) }}>
                                                <label class="form-check-label custom-type-label btn stock-btn" for="consider_for_pf_calculation_yes">{{ trans('messages.yes') }}</label>
                                            </div>
                                        </div>
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="consider_for_pf_calculation" id="consider_for_pf_calculation_no" value="{{ config('constants.SELECTION_NO')}}"  {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_consider_for_pf_calculation)) && ( $recordInfo->e_consider_for_pf_calculation ==  config('constants.SELECTION_NO') ) ) ? 'checked' : '' ) }}>
                                                <label class="form-check-label custom-type-label btn stock-btn" for="consider_for_pf_calculation_no">{{ trans('messages.no') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <label id="consider_for_pf_calculation-error" class="invalid-input" for="consider_for_pf_calculation" style="display: none;"></label>
                                </div>
                            </div>
                            <?php /* ?>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="salary_components_frequence" class="control-label">{{ trans('messages.frequency') }}<span class="text-danger">*</span></label>
                                    <div class="radio-boxes form-row p-1 bg-white">
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="salary_components_frequence" id="salary_components_frequence_yes" value="{{ config('constants.SALARY_COMPONENT_FREQUENCY_MONTHLY')}}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_salary_components_frequence)) && ( $recordInfo->e_salary_components_frequence ==  config('constants.SALARY_COMPONENT_FREQUENCY_MONTHLY') ) ) ? 'checked' : '' ) }}>
                                                <label class="form-check-label custom-type-label btn stock-btn" for="salary_components_frequence_yes">{{ trans('messages.monthly') }}</label>
                                            </div>
                                        </div>
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="salary_components_frequence" id="salary_components_frequence_no" value="{{ config('constants.SALARY_COMPONENT_FREQUENCY_YEARLY')}}" {{ (!isset($recordInfo) ? 'checked' : '' ) }} {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_salary_components_frequence)) && ( $recordInfo->e_salary_components_frequence ==  config('constants.SALARY_COMPONENT_FREQUENCY_YEARLY') ) ) ? 'checked' : '' ) }}>
                                                <label class="form-check-label custom-type-label btn stock-btn" for="salary_components_frequence_no">{{ trans('messages.yearly') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php */ ?>
                        </div>