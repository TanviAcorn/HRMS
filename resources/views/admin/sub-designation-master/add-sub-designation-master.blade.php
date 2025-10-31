
<div class="row dependant-field-selection">
    <div class="col-md-12">
        <div class="form-group">
            <label for="sub_designation_name" class="control-label">{{ trans('Sub-designation') }}<span class="text-danger">*</span></label>
            <input type="text" name="sub_designation_name" class="form-control" placeholder="{{ trans('messages.ex') }} Sub Designation Name" value="{{ old('sub_designation_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_sub_designation_name)) ? $recordInfo->v_sub_designation_name : ''  ) ) ) }}">
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label" for="designation">{{ trans('messages.designation') }}<span class="text-danger">*</span></label>
            <select class="form-control" name="designation">
                <option value="">{{ trans('messages.select') }}</option>
                <?php 
                if(!empty($designationRecordDetails)){
                	foreach ($designationRecordDetails as $designationRecordDetail){
                		$encodeId = Wild_tiger::encode($designationRecordDetail->i_id);
                		$selected ='';
                		if( (isset($recordInfo) ) && ($recordInfo->i_designation_id == $designationRecordDetail->i_id)){
                			$selected="selected='selected'";
                		}
                		?>
                		<option value='{{ $encodeId }}' {{ $selected }}>{{ (!empty($designationRecordDetail->v_value) ? $designationRecordDetail->v_value : '') }}</option>
                		<?php 
                	}
                }
                ?>
            </select>
        </div>
    </div>
</div>
