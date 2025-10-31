
				<div class="row">
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h4 class="address-title">{{ trans('messages.current-address') }}</h4>
                                </div>
                                <div class="col-12">
                                    <div class="form-group ">
                                        <label for="address_line_1" class="control-label">{{ trans('messages.address-line-1') }}<span class="star">*</span></label>
                                        <input type="text" class="form-control" name="address_line_1" placeholder="{{ trans('messages.address-line-1') }}" onchange="sameAsLocationCurrentAddress(this);"  value="{{ old('address_line_1' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_current_address_line_first)) ? $employeeRecordInfo->v_current_address_line_first : ''  ) ) ) }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="address_line_2" class="control-label">{{ trans('messages.address-line-2') }}</label>
                                        <input type="text" class="form-control" name="address_line_2" placeholder="{{ trans('messages.address-line-2') }}" onchange="sameAsLocationCurrentAddress(this);" value="{{ old('address_line_2' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_current_address_line_second)) ? $employeeRecordInfo->v_current_address_line_second : ''  ) ) ) }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="current_village" class="control-label">{{ trans('messages.village') }}</label>
                                        <div class="input-group">
                                            <div class="flex-fill">
                                                <select class="form-control select2 village-master-list" name="current_village" onchange="getLocationDetails(this);sameAsLocationCurrentAddress(this);">
                                                    <option value="">{{ trans('messages.select') }}</option>
	                                                   @if(!empty($villageRecordDetails))
					                                     	@foreach($villageRecordDetails as $villageRecordDetail)
					                                       		@php $villageEncodeId = Wild_tiger::encode($villageRecordDetail->i_id); @endphp
					                                       		{{ $selected = ''; }}
			                                              		@if( (isset($employeeRecordInfo->i_current_village_id)) && ($employeeRecordInfo->i_current_village_id == $villageRecordDetail->i_id ) )
			                                              			{{ $selected = "selected='selected'"; }}
			                                              		@endif
					                                        	<option value="{{ $villageEncodeId }}" {{ $selected }} data-cur-village-city-id="{{ (!empty($villageRecordDetail->i_city_id) ? $villageRecordDetail->i_city_id :'') }}" data-cur-village-country-id="{{  ( isset($villageRecordDetail->cityMaster->stateMaster->i_country_id) ? $villageRecordDetail->cityMaster->stateMaster->i_country_id : '' ) }}" data-cur-village-state-id="{{ (  isset($villageRecordDetail->cityMaster->i_state_id) ? $villageRecordDetail->cityMaster->i_state_id : '' )  }}" data-village-id="{{ (!empty($villageRecordDetail->i_id) ? $villageRecordDetail->i_id :'') }}">{{ (!empty($villageRecordDetail->v_village_name) ? $villageRecordDetail->v_village_name :'') }}</option>
					                                       	@endforeach
					                                 	@endif
                                                </select>
                                            </div>
                                            <button type="button" title="{{ trans('messages.add') }}" class="quick-add-btn bg-theme text-white border-0 px-3" onclick="openVillageModel(this)" data-village-module="{{config('constants.SELECTION_NO')}}"><i class="fas fa-plus text-white"></i></button>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="current_city" class="control-label">{{ trans('messages.city') }}<span class="star">*</span></label>
                                        <div class="input-group">
                                            <div class="flex-fill">
                                                <select class="form-control city-list" name="current_city" onchange="stateRecordInfo(this);countryMasterInfo(this);sameAsLocationCurrentAddress(this);">
                                                    <option value="">{{ trans('messages.select') }}</option>
                                                    <?php 
                                                    if(!empty($cityRecordDetails)){
                                                    	foreach ($cityRecordDetails as $cityRecordDetail){
                                                    		$encodedId = Wild_tiger::encode($cityRecordDetail->i_id);
                                                    		$selected = '';
                                                    		if( isset($employeeRecordInfo->i_current_address_city_id) && ( $employeeRecordInfo->i_current_address_city_id == $cityRecordDetail->i_id ) ){
                                                    			$selected = "selected='selected'";
                                                    		}
                                                    		?>
                                                    		<option value="{{ $encodedId }}" {{ $selected }} data-cur-country-id="{{  ( isset($cityRecordDetail->stateMaster->i_country_id) ? $cityRecordDetail->stateMaster->i_country_id : '' )  }}" data-cur-state-id="{{ (!empty($cityRecordDetail->i_state_id) ? $cityRecordDetail->i_state_id :'') }}" data-city-id="{{ (!empty($cityRecordDetail->i_id) ? $cityRecordDetail->i_id :'') }}">{{ (!empty($cityRecordDetail->v_city_name) ? $cityRecordDetail->v_city_name :'') }}</option>
                                                    		<?php 
                                                    	}
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <button type="button" title="{{ trans('messages.add') }}" onclick="openCityModel(this)" data-city-module="{{ config('constants.SELECTION_NO') }}"  class="quick-add-btn bg-theme text-white border-0 px-3"><i class="fas fa-plus text-white"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="current_state" class="control-label">{{ trans('messages.state') }}<span class="star">*</span></label>
                                        <select class="form-control validate-disable-field" disabled name="current_state" onchange="countryMasterInfo(this)">
                                            <option value="">{{ trans('messages.select') }}</option>
                                            <?php 
                                            if(!empty($stateRecordDetails)){
                                            	foreach ($stateRecordDetails as $stateRecordDetail){
                                              		$encodedId = Wild_tiger::encode($stateRecordDetail->i_id);
                                              		$selected = '';
                                              		if( isset($employeeRecordInfo->cityCurrentInfo->i_state_id) && ( $employeeRecordInfo->cityCurrentInfo->i_state_id == $stateRecordDetail->i_id ) ){
                                              			$selected = "selected='selected'";
                                              		}
                                            		?>
                                               		<option value="{{ $encodedId }}" {{ $selected }} data-current-state-name="{{ (!empty($stateRecordDetail->v_state_name) ? $stateRecordDetail->v_state_name :'') }}" data-current-country-record-id="{{ (!empty($stateRecordDetail->i_country_id) ? $stateRecordDetail->i_country_id :'')}}" data-state-id="{{ (!empty($stateRecordDetail->i_id) ? $stateRecordDetail->i_id :'') }}">{{ (!empty($stateRecordDetail->v_state_name) ? $stateRecordDetail->v_state_name :'') }}</option>
                                                	<?php 
                                              	}
                                          	}
                                          	?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="current_country" class="control-label">{{ trans('messages.country') }}<span class="star">*</span></label>
                                        <select class="form-control validate-disable-field" disabled name="current_country">
                                            <option value="">{{ trans('messages.select') }}</option>
                                            <?php 
                                            if(!empty($contryRecordDetails)){
                                            	foreach ($contryRecordDetails as $contryRecordDetail){
                                              		$encodedId = Wild_tiger::encode($contryRecordDetail->i_id);
                                            		$selected = '';
                                              		if( isset($employeeRecordInfo->cityCurrentInfo->stateMaster->i_country_id) && ($employeeRecordInfo->cityCurrentInfo->stateMaster->i_country_id == $contryRecordDetail->i_id ) ){
                                              			$selected = "selected='selected'";
                                              		}
                                            		?>
                                               		<option value="{{ $encodedId }}" {{ $selected }} data-current-country-id="{{ (!empty($contryRecordDetail->i_id) ? $contryRecordDetail->i_id :'') }}" data-country-id="{{ (!empty($contryRecordDetail->i_id) ? $contryRecordDetail->i_id :'') }}">{{ (!empty($contryRecordDetail->v_country_name) ? $contryRecordDetail->v_country_name :'') }}</option>
                                                	<?php 
                                              	}
                                          	}
                                          	?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="current_pincode" class="control-label">{{ trans('messages.pincode') }}</label>
                                        <input type="text" class="form-control" onkeyup="onlyNumber(this);sameAsLocationCurrentAddress(this);" maxlength="6" name="current_pincode" placeholder="{{ trans('messages.pincode') }}" value="{{ old('current_pincode' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_current_address_pincode)) ? $employeeRecordInfo->v_current_address_pincode : ''  ) ) ) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h4 class="address-title">{{ trans('messages.permanent-address') }}</h4>
                                </div>
                                <div class="col-12 checkbox-panel">
                                    <div class="form-group mb-0 d-flex">
                                        <label for="same_current_address" class="control-label d-block same-as-current-text">{{ trans('messages.same-as-current-address') }}</label>
                                        <div class="form-check form-check-inline pb-2 ml-2">
                                            <input class="form-check-input d-none" type="checkbox" id="same_current_address" name="same_current_address" {{ ( (  isset($employeeRecordInfo) && (!empty($employeeRecordInfo->e_same_current_address)) && ( $employeeRecordInfo->e_same_current_address ==  config('constants.SELECTION_YES') ) ) ? 'checked' : '' ) }} onclick="sameAsLocationCurrentAddress(this)" value="{{config('constants.SELECTION_YES')}}">
                                            <label class="form-check-label control-label" for="same_current_address"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="per_address_line_1" class="control-label">{{ trans('messages.address-line-1') }}<span class="star">*</span></label>
                                        <input type="text" class="form-control" name="per_address_line_1" placeholder="{{ trans('messages.address-line-1') }}" value="{{ old('per_address_line_1' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_permanent_address_line_first)) ? $employeeRecordInfo->v_permanent_address_line_first : ''  ) ) ) }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="per_address_line_2" class="control-label">{{ trans('messages.address-line-2') }}</label>
                                        <input type="text" class="form-control" name="per_address_line_2" placeholder="{{ trans('messages.address-line-2') }}" value="{{ old('per_address_line_2' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_permanent_address_line_second)) ? $employeeRecordInfo->v_permanent_address_line_second : ''  ) ) ) }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="permanent_village" class="control-label">{{ trans('messages.village') }}</label>
                                        <div class="input-group">
                                            <div class="flex-fill">
                                                <select class="form-control select2 village-master-list" name="permanent_village" onchange="getLocationDetails(this);">
                                                    <option value="">{{ trans('messages.select') }}</option>
	                                                   @if(!empty($villageRecordDetails))
					                                     	@foreach($villageRecordDetails as $villageRecordDetail)
					                                       		@php $villageEncodeId = Wild_tiger::encode($villageRecordDetail->i_id); @endphp 
					                                       		{{ $selected = ''; }}
			                                              		@if( isset($employeeRecordInfo->i_permanent_village_id) && ($employeeRecordInfo->i_permanent_village_id == $villageRecordDetail->i_id ) )
			                                              			{{ $selected = "selected='selected'"; }}
			                                              		@endif
					                                        	<option value="{{ $villageEncodeId }}" {{ $selected }} data-cur-village-city-id="{{ (!empty($villageRecordDetail->i_city_id) ? $villageRecordDetail->i_city_id :'') }}" data-cur-village-country-id="{{  (  isset($villageRecordDetail->cityMaster->stateMaster->i_country_id) ? $villageRecordDetail->cityMaster->stateMaster->i_country_id : '' )  }}" data-cur-village-state-id="{{ (isset($villageRecordDetail->cityMaster->i_state_id) ? $villageRecordDetail->cityMaster->i_state_id : '') }}" data-village-id="{{ (!empty($villageRecordDetail->i_id) ? $villageRecordDetail->i_id :'') }}">{{ (!empty($villageRecordDetail->v_village_name) ? $villageRecordDetail->v_village_name :'') }}</option>
					                                       	@endforeach
					                                 	@endif
                                                </select>
                                            </div>
                                            <button type="button" title="{{ trans('messages.add') }}" class="quick-add-btn bg-theme text-white border-0 px-3" onclick="openVillageModel(this)" data-village-module="{{config('constants.SELECTION_NO')}}"><i class="fas fa-plus text-white"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="per_city" class="control-label">{{ trans('messages.city') }}<span class="star">*</span></label>
                                        <div class="input-group">
                                            <div class="flex-fill">
                                                <select class="form-control city-list" name="per_city" onchange="stateRecordInfo(this),countryMasterInfo(this)">
                                                    <option value="">{{ trans('messages.select') }}</option>
                                                    <?php 
                                                    if(!empty($cityRecordDetails)){
                                                    	foreach ($cityRecordDetails as $cityRecordDetail){
                                                    		$encodedId = Wild_tiger::encode($cityRecordDetail->i_id);
                                                    		$selected = '';
                                                    		if( isset($employeeRecordInfo->i_permanent_address_city_id) && ( $employeeRecordInfo->i_permanent_address_city_id == $cityRecordDetail->i_id ) ){
                                                    			$selected = "selected='selected'";
                                                    		}
                                                    		?>
                                                    		<option value="{{ $encodedId }}" {{ $selected }} data-cur-country-id="{{ (isset($cityRecordDetail->stateMaster->i_country_id) ? $cityRecordDetail->stateMaster->i_country_id : '' ) }}" data-cur-state-id="{{ (!empty($cityRecordDetail->i_state_id) ? $cityRecordDetail->i_state_id :'') }}" data-city-id="{{ (!empty($cityRecordDetail->i_id) ? $cityRecordDetail->i_id :'') }}">{{ (!empty($cityRecordDetail->v_city_name) ? $cityRecordDetail->v_city_name :'') }}</option>
                                                    		<?php 
                                                    	}
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <button type="button" title="{{ trans('messages.add') }}" onclick="openCityModel(this)" data-city-module="{{ config('constants.SELECTION_NO') }}" class="quick-add-btn bg-theme text-white border-0 px-3"><i class="fas fa-plus text-white"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="per_state" class="control-label">{{ trans('messages.state') }}<span class="star">*</span></label>
                                        <select class="form-control validate-disable-field" disabled name="per_state" onchange="countryMasterInfo(this)">
                                            <option value="">{{ trans('messages.select') }}</option>
                                            <?php 
                                            if(!empty($stateRecordDetails)){
                                            	foreach ($stateRecordDetails as $stateRecordDetail){
                                              		$encodedId = Wild_tiger::encode($stateRecordDetail->i_id);
                                              		$selected = '';
                                              		if( isset($employeeRecordInfo->cityPermanentInfo->i_state_id) && ( $employeeRecordInfo->cityPermanentInfo->i_state_id == $stateRecordDetail->i_id ) ){
                                              			$selected = "selected='selected'";
                                              		}
                                            		?>
                                               		<option value="{{ $encodedId }}" {{ $selected }} data-per-country-record-id="{{ (!empty($stateRecordDetail->i_country_id) ? $stateRecordDetail->i_country_id :'')}}" data-per-state-id="{{ (!empty($stateRecordDetail->i_id) ? $stateRecordDetail->i_id :'') }}">{{ (!empty($stateRecordDetail->v_state_name) ? $stateRecordDetail->v_state_name :'') }}</option>
                                                	<?php 
                                              	}
                                          	}
                                          	?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="per_country" class="control-label">{{ trans('messages.country') }}<span class="star">*</span></label>
                                        <select class="form-control validate-disable-field" disabled name="per_country">
                                            <option value="">{{ trans('messages.select') }}</option>
                                            <?php 
                                            if(!empty($contryRecordDetails)){
                                            	foreach ($contryRecordDetails as $contryRecordDetail){
                                              		$encodedId = Wild_tiger::encode($contryRecordDetail->i_id);
                                              		$selected = '';
                                              		if( isset($employeeRecordInfo->cityPermanentInfo->stateMaster->i_country_id) && ($employeeRecordInfo->cityPermanentInfo->stateMaster->i_country_id == $contryRecordDetail->i_id ) ){
                                              			$selected = "selected='selected'";
                                              		}
                                              		?>
                                            		<option value="{{ $encodedId }}" {{ $selected }} data-per-country-id="{{ (!empty($contryRecordDetail->i_id) ? $contryRecordDetail->i_id :'') }}" data-country-id="{{ (!empty($contryRecordDetail->i_id) ? $contryRecordDetail->i_id :'')}}">{{ (!empty($contryRecordDetail->v_country_name) ? $contryRecordDetail->v_country_name :'') }}</option>
                                                	<?php 
                                              	}
                                          	}
                                          	?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="per_pincode" class="control-label">{{ trans('messages.pincode') }}</label>
                                        <input type="text" class="form-control"  onkeyup="onlyNumber(this)" name="per_pincode" maxlength="6"  placeholder="{{ trans('messages.pincode') }}" value="{{ old('per_pincode' , ( (isset($employeeRecordInfo) && (!empty($employeeRecordInfo->v_permanent_address_pincode)) ? $employeeRecordInfo->v_permanent_address_pincode : ''  ) ) ) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>