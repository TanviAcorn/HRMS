
						<div class="row dependant-field-selection">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="city_name" class="control-label">{{ trans('messages.city-name') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="city_name" class="form-control" placeholder="{{ trans('messages.ex') }}{{ trans('messages.city-surat-ahmedabad-placeholder')}}" value="{{ old('city_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_city_name)) ? $recordInfo->v_city_name : ''  ) ) ) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="city_name" class="control-label">{{ trans('messages.chart-display-color') }}</label>
                                    <input type="color" name="city_chart_color" class="form-control" placeholder="{{ trans('messages.chart-display-color') }}" value="{{ old('city_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_chart_color)) ? $recordInfo->v_chart_color : ''  ) ) ) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="state">{{ trans("messages.state") }}<span class="text-danger">*</span></label>
                                    <select class="form-control" name="state">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($stateRecordDetails)){
                                        	foreach ($stateRecordDetails as $stateRecordDetail){
                                        		$stateEncodeId = Wild_tiger::encode($stateRecordDetail->i_id);
                                        		$selected ='';
                                        		if( (isset($recordInfo) ) && ($recordInfo->i_state_id == $stateRecordDetail->i_id)){
                                        			$selected="selected='selected'";
                                        		}
                                        		?>
                                        		<option value='{{ $stateEncodeId }}' {{ $selected }}>{{ (!empty($stateRecordDetail->v_state_name) ? $stateRecordDetail->v_state_name : '') }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>