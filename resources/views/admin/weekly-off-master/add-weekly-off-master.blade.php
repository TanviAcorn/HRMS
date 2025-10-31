						<div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="weekly_off_name" class="control-label">{{ trans('messages.weekly-off-name') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="weekly_off_name" class="form-control" placeholder="{{trans('messages.ex')}} {{trans('messages.first-and-third-saturday')}}" value="{{ old('weekly_off_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_weekly_off_name)) ? $recordInfo->v_weekly_off_name : ''  ) ) ) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="week_off_description" class="control-label">{{ trans('messages.description') }}</label>
                                    <textarea rows="3" name="week_off_description" class="form-control">{{ ( (isset($recordInfo) && (!empty($recordInfo->v_description)) ? $recordInfo->v_description : ''  ) ) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12 weekly-off-master-record-div">
                                <label class="control-label">{{ trans('messages.week-off-days') }}<span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap pt-1">
                                    @if(!empty($weekDayDetails))
                                    	@foreach($weekDayDetails as $weekKey => $weekDayDetail)
                                    		@php $columnName = 't_is_'.$weekKey;  @endphp
                                    		<div class="form-group mb-0 pr-3">
		                                        <div class="form-check form-check-inline pb-2">
		                                            <input class="form-check-input weekly-off-selection" onclick="showDayOffSelection(this)" data-day="{{ $weekKey }}" type="checkbox" id="week-day-{{ $weekKey  }}" name="{{ $weekKey }}" value="{{ config('constants.SELECTION_YES') }}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->$columnName)) && ( $recordInfo->$columnName ==  1 ) ) ? 'checked' : '' ) }}>
		                                            <label class="form-check-label lable-control" for="week-day-{{ $weekKey  }}">{{ $weekDayDetail }}</label>
		                                        </div>
		                                    </div>
                                    	@endforeach
                                    @endif
                                </div>
                            </div>
							@if(!empty($weekDayDetails))
                            	@foreach($weekDayDetails as $weekKey => $weekDayDetail)
		                            @php
		                            $columnName = 't_is_'.$weekKey; 
		                            $alternateColumnName = 'v_'.$weekKey.'_alternate_off';
		                            $allColumnName = 'v_'.$weekKey.'_all_off'; 
		                            @endphp
		                            <div class="col-sm-4 col-md-4 col-lg-3 col-12 pt-3 {{$weekKey}}-selection-div" {{ ( (  isset($recordInfo) && (!empty($recordInfo->$columnName)) && ( $recordInfo->$columnName ==  1 ) ) ? '' : 'style=display:none;' ) }}>
		                                <table class="table table-bordered">
		                                    <thead>
		                                        <tr>
		                                            <th scope="col" class="text-center">{{ $weekDayDetail  }}</th>
												</tr>
		                                    </thead>
		                                    <tbody>
		                                        <tr>
		                                            <td>
		                                                <div class="form-group mb-0">
		                                                    <div class="form-check form-check-inline pb-2">
		                                                        <input class="form-check-input form-check-radio-input week-alternate-off" type="radio" id="alternate-off-{{ $weekDayDetail }}" name="alternate_off_{{ $weekKey }}" value="{{ config('constants.ALTERNATE_STATUS') }}" {{ ( ( isset($recordInfo) &&  isset($recordInfo->weeklyOffDetail->$alternateColumnName)  && (!empty($recordInfo->weeklyOffDetail->$alternateColumnName)) && ( $recordInfo->weeklyOffDetail->$alternateColumnName == config('constants.SELECTION_YES') ) ) ? 'checked' : '' )    }} >
		                                                        <label class="form-check-label form-check-radio-lable lable-control " for="alternate-off-{{ $weekDayDetail }}">{{ trans('messages.alternate-off') }}</label>
		                                                    </div>
		                                                </div>
		
		                                            </td>
		                                        </tr>
		                                        <tr>
													<td>
		                                                <div class="form-group mb-0">
		                                                    <div class="form-check form-check-inline pb-2">
		                                                        <input class="form-check-input form-check-radio-input week-all-off" type="radio" id="all-off-{{ $weekDayDetail }}" name="alternate_off_{{ $weekKey }}" value="{{ config('constants.ALL_STATUS') }}" {{ (!isset($recordInfo) ? 'checked' : '' ) }} {{ ( ( isset($recordInfo) &&  isset($recordInfo->weeklyOffDetail->$alternateColumnName) && (!empty($recordInfo->weeklyOffDetail->$allColumnName)) && ( $recordInfo->weeklyOffDetail->$allColumnName == config('constants.SELECTION_YES') ) ) ? 'checked' : '' )    }} >
		                                                        <label class="form-check-label lable-control form-check-radio-lable " for="all-off-{{ $weekDayDetail }}">{{ trans('messages.all-off') }}</label>
		                                                    </div>
		                                            </td>
		                                        </tr>
		                                    </tbody>
		                                </table>
		                            </div>
	                            @endforeach
                            @endif
						</div>