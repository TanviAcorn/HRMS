						<div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="state_name" class="control-label">{{ trans('messages.state-name') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="state_name" class="form-control" placeholder="{{ trans('messages.ex') }}{{ trans('messages.state-place-holder') }}" value="{{ old('state_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_state_name)) ? $recordInfo->v_state_name : ''  ) ) ) }}">
                                </div>
                            </div>
                        </div>