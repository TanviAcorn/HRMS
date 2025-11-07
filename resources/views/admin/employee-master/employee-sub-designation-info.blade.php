<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label class="control-label">{{ trans('messages.current-designation') }}</label>
            <p class="details-text font-weight-bold">{{ isset($recordInfo->designationInfo->v_value) ? $recordInfo->designationInfo->v_value : '' }}</p>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label class="control-label">{{ trans('sub-designation') }}</label>
            <p class="details-text font-weight-bold">{{ isset($recordInfo->subDesignationInfo->v_sub_designation_name) ? $recordInfo->subDesignationInfo->v_sub_designation_name : '' }}</p>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label class="control-label">{{ trans('messages.new') }} {{ trans('sub-designation') }} <span class="star">*</span></label>
            <select class="form-control" name="employee_sub_designation">
                <option value="">{{ trans('messages.select') }}</option>
                @if(!empty($subDesignationDetails))
                    @foreach($subDesignationDetails as $row)
                        @php 
                            $encodeId = Wild_tiger::encode($row->i_id);
                            $selected = '';
                            if(isset($recordInfo->i_sub_designation_id) && $recordInfo->i_sub_designation_id == $row->i_id){
                                $selected = "selected='selected'";
                            }
                        @endphp
                        <option value="{{ $encodeId }}" {{ $selected }}>{{ $row->v_sub_designation_name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="sub_designation_effective_date" class="control-label">{{ trans('messages.effective-from') }}<span class="star">*</span></label>
            <input type="text" class="form-control" name="sub_designation_effective_date" placeholder="DD-MM-YYYY">
        </div>
    </div>
</div>
<input type="hidden" name="update_sub_designation_employee_id" value="{{ isset($recordInfo->i_id) ? Wild_tiger::encode($recordInfo->i_id) : '' }}">
<input type="hidden" name="sub_designation_last_update" value="{{ isset($recordInfo->dt_last_update_designation) ? $recordInfo->dt_last_update_designation : '' }}">
