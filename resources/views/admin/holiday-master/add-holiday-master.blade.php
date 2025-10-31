						<div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="holiday_name" class="control-label">{{ trans('messages.holiday-name') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="holiday_name" class="form-control" placeholder="{{ trans('messages.ex') }} {{ trans('messages.holiday-name-placeholder') }}" value="{{ old('holiday_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_holiday_name)) ? $recordInfo->v_holiday_name : ''  ) ) ) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="holiday_date" class="control-label">{{ trans('messages.date') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="holiday_date" class="form-control" placeholder="DD-MM-YYYY"  value="{{ old('holiday_date' , ( (isset($recordInfo) && (!empty($recordInfo->dt_holiday_date)) ? clientDate($recordInfo->dt_holiday_date) : ''  ) ) ) }}"/>
                                </div>
                            </div>
                        </div>
<script>

$(document).ready(function() {
	 
	
    
});
</script>  