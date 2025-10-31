					<div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="aadhaar_number" class="control-label">{{ trans('messages.aadhaar-number') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" maxlength="14" onkeyup="onlyNumberWithSpaceSign(this);" maxlength="14" name="aadhaar_number" placeholder="{{ trans('messages.aadhaar-number') }}" value="{{ old('aadhaar_number' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_aadhar_no)) ? $employeeRecordInfo->v_aadhar_no : ''  ) ) ) }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="pan_no" class="control-label">{{ trans('messages.pan') }}</label>
                                <input type="text" class="form-control" maxlength="10" name="pan_no" placeholder="{{ trans('messages.pan') }}" value="{{ old('pan_no' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_pan_no)) ? $employeeRecordInfo->v_pan_no : ''  ) ) ) }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="uan_number" class="control-label">{{ trans('messages.uan-number') }}<a type="button" class="ml-1" data-toggle="tooltip" data-placement="right" title="{{trans('messages.uan-full-form')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
                                <input type="text" class="form-control" maxlength="30" name="uan_number" placeholder="{{ trans('messages.uan-number') }}" value="{{ old('uan_number' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_uan_no)) ? $employeeRecordInfo->v_uan_no : ''  ) ) ) }}">
                            </div>
                        </div>
                    </div>
                    <script>
						$(function () {
  							$('[data-toggle="tooltip"]').tooltip()
						})
					</script>