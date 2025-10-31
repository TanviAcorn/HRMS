					 	<div class="row dependant-field-selection">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="village_name" class="control-label">{{ trans('messages.village-name') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="village_name" class="form-control" placeholder="{{ trans('messages.village-name') }}" value="{{ old('village_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_village_name)) ? $recordInfo->v_village_name : ''  ) ) ) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="city">{{ trans("messages.city") }}<span class="text-danger">*</span></label>
                                    <select class="form-control" name="city" onchange="getStateDetails(this)">
                                        <option value="">{{ trans("messages.select") }}</option>
	                                 	@if(!empty($cityRecordDetails))
	                                     	@foreach($cityRecordDetails as $cityRecordDetail)
	                                       		@php $cityEncodeId = Wild_tiger::encode($cityRecordDetail->i_id); @endphp 
	                                        	{{ $selected = ''}}
	                                        	@if( (isset($recordInfo) ) && ($recordInfo->i_city_id == $cityRecordDetail->i_id))
	                                        		{{ $selected = "selected='selected'"}}
	                                        	@endif
	                                        	<option value='{{ $cityEncodeId }}' data-state-id="{{ $cityRecordDetail->i_state_id }}" {{ $selected }}>{{ (!empty($cityRecordDetail->v_city_name) ? $cityRecordDetail->v_city_name : '') }}</option>
	                                       	@endforeach
	                                 	@endif
                                 	</select>
                             	</div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="state_name">{{ trans("messages.state") }}<span class="text-danger">*</span></label>
                                    <select class="form-control state-master-info-list" name="state_name" disabled>
                                     	<option value="">{{ trans("messages.select") }}</option>
	                                 	@if(!empty($stateRecordDetails))
		                                	@foreach($stateRecordDetails as $stateRecordDetail)
		                                		@php $stateEncodeId = Wild_tiger::encode($stateRecordDetail->i_id); @endphp  
		                                  		{{ $selected = ''}}
		                                    	@if( (isset($recordInfo) ) && ($recordInfo->cityMaster->i_state_id == $stateRecordDetail->i_id))
		                                      		{{ $selected = "selected='selected'"}}
		                                   		@endif
		                                    	<option value='{{ $stateEncodeId }}' data-state-record-id="{{ $stateRecordDetail->i_id }}" {{ $selected }}>{{ (!empty($stateRecordDetail->v_state_name) ? $stateRecordDetail->v_state_name : '') }}</option>
	                                      	@endforeach
		                             	@endif
                                     </select>
                                </div>
                            </div>
                        </div>