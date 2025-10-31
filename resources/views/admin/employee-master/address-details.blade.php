    <div class="step-panel-class">
        <div class="d-flex step-panel-attribute align-items-center">
            <div class="panel-attribute">
                <h3 class="panel-title"><i class="fa fa-map-marker my-profile-class" aria-hidden="true"></i>{{trans('messages.address-identity-details')}}</h3>
            </div>
            <div class="step-btn">
                <div class="d-flex align-items-center">
                    <div class="btn-preview">
                        <div class="btn-class"><button type="button" class="default-btn prev-step" data-tab-name="step1" title="{{ trans('messages.previous') }}">{{trans('messages.previous')}}</button></div>
                    </div>
                    <div class="btn-next">
                        <div class="btn-class"><button type="button" onclick="addressIdentityDetails(this);" data-tab-name="step3" class="default-btn tab-next-btn" title="{{ trans('messages.next') }}">{{trans('messages.next')}} </button></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-items">
            <h4 class="address-title">{{trans('messages.current-address')}}</h4>
            <div class="panel-items-box">
                <div class="row pt-3">
                    <div class="col-xl-3 col-sm-6">
                        <div class="form-group ">
                            <label for="address_line_1" class="lable-control">{{ trans('messages.address-line-1') }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="address_line_1" placeholder="{{ trans('messages.address-line-1') }}" onchange="sameAsLocationCurrentAddress(this)">
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="form-group">
                            <label for="address_line_2" class="lable-control">{{ trans('messages.address-line-2') }}</label>
                            <input type="text" class="form-control" name="address_line_2" placeholder="{{ trans('messages.address-line-2') }}" onchange="sameAsLocationCurrentAddress(this)">
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="form-group">
                            <label for="current_village" class="lable-control">{{ trans('messages.village') }}</label>
                            <div class="input-group">
                                <div class="flex-fill flex-fill-demo">
                                    <select class="form-control select2 select2-button select2-button-width village-list" name="current_village" onchange="getLocationDetails(this);sameAsLocationCurrentAddress(this);">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        @if(!empty($villageRecordDetails))
	                                     	@foreach($villageRecordDetails as $villageRecordDetail)
	                                       		@php  $villageEncodeId = Wild_tiger::encode($villageRecordDetail->i_id); @endphp
	                                        	<option value="{{ $villageEncodeId }}" data-cur-village-city-id="{{ (!empty($villageRecordDetail->i_city_id) ? $villageRecordDetail->i_city_id :'') }}" data-cur-village-country-id="{{  ( isset($villageRecordDetail->cityMaster->stateMaster->i_country_id) ? $villageRecordDetail->cityMaster->stateMaster->i_country_id : '' ) }}" data-cur-village-state-id="{{ (isset($villageRecordDetail->cityMaster->i_state_id) ? $villageRecordDetail->cityMaster->i_state_id : '' )  }}" data-village-id="{{ (isset($villageRecordDetail->i_id) ? $villageRecordDetail->i_id :'' ) }}">{{ (!empty($villageRecordDetail->v_village_name) ? $villageRecordDetail->v_village_name :'') }}</option>
	                                       	@endforeach
	                                 	@endif
                                    </select>
                                </div>
                                <button type="button" title="{{ trans('messages.add') }}" class="quick-add-btn bg-theme text-white border-0 px-3" onclick="openVillageModel(this);sameAsLocationCurrentAddress(this);" data-village-module="{{config('constants.SELECTION_NO')}}"><i class="fas fa-plus text-white"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="form-group">
                            <label for="current_city" class="lable-control">{{ trans('messages.city') }}<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="flex-fill flex-fill-demo">
                                    <select class="form-control select2 select2-button select2-button-width city-list" name="current_city" onchange="stateMasterInfo(this);sameAsLocationCurrentAddress(this);">
                                        <option value="">{{ trans("messages.select") }}</option>
                                          @if (!empty($cityRecordDetails))
                                        		@foreach ($cityRecordDetails as $cityRecordDetail)
                                        			@php $cityEncodeId = Wild_tiger::encode($cityRecordDetail->i_id); @endphp
                                        			<option value="{{ $cityEncodeId }}" data-cur-country-id="{{ (isset($cityRecordDetail->stateMaster->i_country_id) ? $cityRecordDetail->stateMaster->i_country_id :'') }}" data-cur-state-id="{{ (isset($cityRecordDetail->i_state_id) ? $cityRecordDetail->i_state_id :'')}}" data-city-id="{{ (isset($cityRecordDetail->i_id) ? $cityRecordDetail->i_id :'')}}">{{ (!empty($cityRecordDetail->v_city_name) ? $cityRecordDetail->v_city_name :'') }}</option>
                                        		@endforeach
                                       		@endif
                                    </select>
                                </div>
                                <button type="button" title="{{ trans('messages.add') }}" class="quick-add-btn bg-theme text-white border-0 px-3" data-city-module="{{ config('constants.SELECTION_NO') }}" onclick="openCityModel(this)"><i class="fas fa-plus text-white"></i></button>
                            </div>
                            <label id="current_city-error" class="invalid-input" for="current_city"></label>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="form-group">
                            <label for="current_state" class="lable-control">{{ trans('messages.state') }}<span class="text-danger">*</span></label>
                            <select class="form-control state-name" name="current_state" disabled>
                                <option value="">{{ trans("messages.select") }}</option>
                                @if (!empty($stateRecordDetails))
                                	@foreach ($stateRecordDetails as $stateRecordDetail)
                                		@php $stateEncodeId = Wild_tiger::encode($stateRecordDetail->i_id); @endphp
                                	 	<option value="{{ $stateEncodeId }}" data-state-record-id="{{ (isset($stateRecordDetail->i_country_id) ? $stateRecordDetail->i_country_id :'') }}" data-state-id="{{(isset($stateRecordDetail->i_id) ? $stateRecordDetail->i_id :'')}}">{{ (!empty($stateRecordDetail->v_state_name) ? $stateRecordDetail->v_state_name :'') }}</option>
                                	@endforeach
                               @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="form-group">
                            <label for="current_country" class="lable-control">{{ trans('messages.country') }}<span class="text-danger">*</span></label>
                            <select class="form-control country-name" disabled name="current_country" >
                                <option value="">{{ trans("messages.select") }}</option>
                                	@if(!empty($countryRecordDetails))
                                		@foreach ($countryRecordDetails as $countryRecordDetail)
                                			 @php  $countryEncodeId = Wild_tiger::encode($countryRecordDetail->i_id); @endphp
                                			 <option value="{{$countryEncodeId}}" data-country-record-id="{{ (isset($countryRecordDetail->i_id) ? $countryRecordDetail->i_id :'') }}" data-country-id="{{ (isset($countryRecordDetail->i_id) ? $countryRecordDetail->i_id :'') }}">{{ (!empty($countryRecordDetail->v_country_name) ? $countryRecordDetail->v_country_name :'') }}</option>
                                		@endforeach
                               		@endif
                             </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="form-group">
                            <label for="pincode" class="lable-control">{{ trans('messages.pincode') }}</label>
                            <input type="text" class="form-control" onkeyup="onlyNumber(this);sameAsLocationCurrentAddress(this);" maxlength="6" name="pincode" placeholder="{{ trans('messages.pincode') }}" >
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-items">
            <h4 class="address-title">{{trans('messages.permanent-address')}}</h4>
            <div class="panel-items-box checkbox-panel add-employee-check">
                <div class="row pt-3">
                    <div class="col-xl-2 col-sm-6">
                        <div class="form-group mb-0">
                            <label for="same_current_address" class="lable-control d-block">{{trans('messages.same-as-current-address')}}</label>
                            <div class="form-check form-check-inline pb-2">
                                <label class="checkbox" for="same_current_address">
   						        <input type="checkbox" name="same_current_address" value="{{ config('constants.SELECTION_YES') }}"  onclick="sameAsLocationCurrentAddress(this)" id="same_current_address"><span class="checkmark"></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="form-group">
                            <label for="address_line_1" class="lable-control">{{ trans('messages.address-line-1') }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="address_permanent_line_1" placeholder="{{ trans('messages.address-line-1') }}" >
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="form-group">
                            <label for="address_line_2" class="lable-control">{{ trans('messages.address-line-2') }}</label>
                            <input type="text" class="form-control" name="address_permanent_line_2" placeholder="{{ trans('messages.address-line-2') }}" >
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="form-group">
                            <label for="permanent_village" class="lable-control">{{ trans('messages.village') }}</label>
                            <div class="input-group">
                                <div class="flex-fill flex-fill-demo">
                                    <select class="form-control select2 select2-button select2-button-width village-list" name="permanent_village" onchange="getLocationDetails(this);">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        @if(!empty($villageRecordDetails))
	                                     	@foreach($villageRecordDetails as $villageRecordDetail)
	                                       		@php  $villageEncodeId = Wild_tiger::encode($villageRecordDetail->i_id); @endphp
	                                        	<option value="{{ $villageEncodeId }}" data-cur-village-city-id="{{  (isset($villageRecordDetail->i_city_id) ? $villageRecordDetail->i_city_id :'') }}" data-cur-village-country-id="{{  ( isset($villageRecordDetail->cityMaster->stateMaster->i_country_id) ? $villageRecordDetail->cityMaster->stateMaster->i_country_id : '' ) }}" data-cur-village-state-id="{{ ( isset($villageRecordDetail->cityMaster->i_state_id) ? $villageRecordDetail->cityMaster->i_state_id : '' ) }}" data-village-id="{{ (isset($villageRecordDetail->i_id) ? $villageRecordDetail->i_id :'') }}">{{ (!empty($villageRecordDetail->v_village_name) ? $villageRecordDetail->v_village_name :'') }}</option>
	                                       	@endforeach
	                                 	@endif
                                    </select>
                                </div>
                                <button type="button" title="{{ trans('messages.add') }}" class="quick-add-btn bg-theme text-white border-0 px-3" onclick="openVillageModel(this)" data-village-module="{{config('constants.SELECTION_NO')}}"><i class="fas fa-plus text-white"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="form-group">
                            <label for="per_city" class="lable-control">{{ trans('messages.city') }}<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="flex-fill flex-fill-demo">
                                    <select class="form-control select2 select2-button-width city-list" name="per_city" onchange="stateMasterInfo(this)">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        	@if (!empty($cityRecordDetails))
                                        		@foreach ($cityRecordDetails as $cityRecordDetail)
                                        			@php  $cityEncodeId = Wild_tiger::encode($cityRecordDetail->i_id); @endphp
                                        			<option value="{{ $cityEncodeId }}" data-cur-country-id="{{ (isset($cityRecordDetail->stateMaster->i_country_id) ? $cityRecordDetail->stateMaster->i_country_id :'') }}" data-cur-state-id="{{ (isset($cityRecordDetail->i_state_id) ? $cityRecordDetail->i_state_id :'')}}" data-city-id="{{ (isset($cityRecordDetail->i_id) ? $cityRecordDetail->i_id :'') }}">{{ (!empty($cityRecordDetail->v_city_name) ? $cityRecordDetail->v_city_name :'') }}</option>
                                        		@endforeach
                                       		@endif
                                    </select>
                                </div>
                                <button type="button" title="{{ trans('messages.add') }}" class="quick-add-btn bg-theme text-white border-0 px-3" data-city-module="{{ config('constants.SELECTION_NO') }}" onclick="openCityModel(this)"><i class="fas fa-plus text-white"></i></button>
                            </div>
                            <label id="per_city-error" class="invalid-input" for="per_city"></label>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="form-group">
                            <label for="per_state" class="lable-control">{{ trans('messages.state') }}<span class="text-danger">*</span></label>
                            <select class="form-control state-name" name="per_state" disabled >
                            <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($stateRecordDetails))
                                	@foreach ($stateRecordDetails as $stateRecordDetail)
                                		@php  $stateEncodeId = Wild_tiger::encode($stateRecordDetail->i_id); @endphp
                                	 	<option value="{{ $stateEncodeId }}" data-country-record-id="{{ (!empty($stateRecordDetail->i_country_id) ? $stateRecordDetail->i_country_id :'') }}" data-per-state-id="{{ (!empty($stateRecordDetail->i_id) ? $stateRecordDetail->i_id :'') }}">{{ (!empty($stateRecordDetail->v_state_name) ? $stateRecordDetail->v_state_name :'') }}</option>
                                	@endforeach
                               @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="form-group">
                            <label for="per_country" class="lable-control">{{ trans('messages.country') }}<span class="text-danger">*</span></label>
                            <select class="form-control country-name" disabled name="per_country" >
                               <option value="">{{ trans("messages.select") }}</option>
                                @if(!empty($countryRecordDetails))
                               		@foreach ($countryRecordDetails as $countryRecordDetail)
                                		@php  $countryEncodeId = Wild_tiger::encode($countryRecordDetail->i_id); @endphp
                                		<option value="{{$countryEncodeId}}" data-country-record-id="{{ (!empty($countryRecordDetail->i_id) ? $countryRecordDetail->i_id :'') }}" data-country-id="{{ (!empty($countryRecordDetail->i_id) ? $countryRecordDetail->i_id :'') }}">{{ (!empty($countryRecordDetail->v_country_name) ? $countryRecordDetail->v_country_name :'') }}</option>
                                 	@endforeach
                               @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="form-group">
                            <label for="pincode" class="lable-control">{{ trans('messages.pincode') }}</label>
                            <input type="text" class="form-control" onkeyup="onlyNumber(this);" maxlength="6" name="pincode_permanent" placeholder="{{ trans('messages.pincode') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
	        <div class="row pt-3">
	            <div class="col-xl-2 col-sm-6">
	                <div class="form-group">
	                    <label for="aadhaar_number" class="lable-control">{{ trans('messages.aadhaar-number') }}<span class="text-danger">*</span></label>
	                    <input type="text" class="form-control" maxlength="14" name="aadhaar_number" onkeyup="onlyNumberWithSpaceSign(this)"  placeholder="{{ trans('messages.aadhaar-number') }}">
	                </div>
	            </div>
	            <div class="col-xl-2 col-sm-6">
	                <div class="form-group">
	                    <label for="pan" class="lable-control">{{ trans('messages.pan') }}</label>
	                    <input type="text" class="form-control" maxlength="10"  name="pan_number" placeholder="{{ trans('messages.pan') }}">
	                </div>
	            </div>
	        </div>
		
    </div>
