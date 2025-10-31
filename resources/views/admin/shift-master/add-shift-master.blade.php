@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ $pageTitle }}</h1>

    </div>
    {!! Form::open(array( 'id '=> 'add-shift-master-form' , 'method' => 'post' ,'url' => 'shift-master/add')) !!}
    @if (count($errors) > 0)
    <div class="error">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="container-fluid pt-3 ">
	    <div class="filter-result-wrapper">
	        <div class="card card-body pb-0">
	            <div class="form-group">
	                <div class="row">
	                    <div class="form-group col-sm-4 col-12">
	                        <label for="shift_name" class="control-label">{{ trans("messages.shift-name") }}<span class="text-danger">*</span></label>
	                        <input type="text" class="form-control" name="shift_name" placeholder='{{ trans("messages.shift-name") }}' value="{{ old('shift_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_shift_name)) ? $recordInfo->v_shift_name : ''  ) ) ) }}">
	                    </div>
	                    <div class="form-group col-sm-2 col-12">
	                        <label for="shift_code" class="control-label">{{ trans("messages.shift-code") }}<span class="text-danger">*</span></label>
	                        <input type="text" class="form-control" name="shift_code" placeholder='{{ trans("messages.shift-code") }}' value="{{ old('shift_code' , ( (isset($recordInfo) && (!empty($recordInfo->v_shift_code)) ? $recordInfo->v_shift_code : ''  ) ) ) }}">
	                    </div>
	                    <div class="form-group col-sm-6 col-12">
	                        <label for="shift_type" class="control-label">{{ trans("messages.type-of-shift") }}<span class="text-danger">*</span></label>
	                        <div class="radio-boxes form-row p-1 bg-white">
	                            <div class="radio-box col-lg-4 col-sm-4 col-6 mb-2">
	                                <div class="form-check">
	                                    <input class="form-check-input" type="radio" name="shift_type" id="morning_shift" value="{{ config('constants.MORNING_SHIFT') }}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_shift_type)) && ( $recordInfo->e_shift_type ==  config('constants.MORNING_SHIFT') ) ) ? 'checked' : '' ) }}>
	                                    <label class="form-check-label custom-type-label btn stock-btn" for="morning_shift">{{ trans("messages.morning-shift") }}</label>
	                                </div>
	                            </div>
	                            <div class="radio-box col-lg-4 col-sm-4 col-6 mb-2">
	                                <div class="form-check">
	                                    <input class="form-check-input" type="radio" name="shift_type" id="afternoon_shift" value="{{ config('constants.AFTERNOON_SHIFT') }}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_shift_type)) && ( $recordInfo->e_shift_type ==  config('constants.AFTERNOON_SHIFT') ) ) ? 'checked' : '' ) }}>
	                                    <label class="form-check-label custom-type-label btn stock-btn" for="afternoon_shift">{{ trans("messages.afternoon-shift") }}</label>
	                                </div>
	                            </div>
	                            <div class="radio-box col-lg-4 col-sm-4 col-6 mb-2">
	                                <div class="form-check">
	                                    <input class="form-check-input" type="radio" name="shift_type" id="night_shift" value="{{ config('constants.NIGHT_SHIFT') }}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_shift_type)) && ( $recordInfo->e_shift_type ==  config('constants.NIGHT_SHIFT') ) ) ? 'checked' : '' ) }}>
	                                    <label class="form-check-label custom-type-label btn stock-btn" for="night_shift">{{ trans("messages.night-shift") }}</label>
	                                </div>
	                            </div>
	                            
	
	                        </div>
	                    </div>
	                    <div class="form-group col-sm-12 col-12">
	                        <label for="description" class="control-label">{{ trans("messages.description") }}</label>
	                        <textarea type="text" class="form-control" name="description">{{ ( (isset($recordInfo) && (!empty($recordInfo->v_description)) ? $recordInfo->v_description : ''  ) )  }}</textarea>
	                    </div>
	                    <div class="col-12">
	                        <h4>{{ trans("messages.shift-simings") }}</h4>
	                        <span class="text-muted">{{ trans("messages.configure-shift-timing") }}</span>
	
	                        <table class="table table-sm table-bordered mt-3 table-responsive">
	                            <thead>
	                                <tr>
	                                    <th class="text-center" style="min-width:198px; width:198px;"></th>
	                                    <th class="text-left text-uppercase" style=" width:190px; min-width:190px;">{{ trans("messages.monday") }}</th>
	                                    <th class="text-left text-uppercase" style=" width:190px; min-width:190px;">{{ trans("messages.tuesday") }}</th>
	                                    <th class="text-left text-uppercase" style=" width:190px; min-width:190px;">{{ trans("messages.wednesday") }}</th>
	                                    <th class="text-left text-uppercase" style=" width:190px; min-width:190px;">{{ trans("messages.thursday") }}</th>
	                                    <th class="text-left text-uppercase" style=" width:190px; min-width:190px;">{{ trans("messages.friday") }}</th>
	                                    <th class="text-left text-uppercase" style=" width:190px; min-width:190px;">{{ trans("messages.saturday") }}</th>
	                                    <th class="text-left text-uppercase" style=" width:190px; min-width:190px;">{{ trans("messages.sunday") }}</th>
	                                </tr>
	                            </thead>
	                            <tbody class="shift-start-time-tbody">
	                                <tr>
	                                    <td>
	                                        <p>{{ trans("messages.shift-start-time") }}<span class="text-danger">*</span></p>
	                                        <?php /* <label><input type="checkbox" name="shift_start_time_checkbox" value="{{ config('constants.SELECTION_YES') }}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? 'checked' : '' ) }} onclick="addShiftStartTimeInfo(this)">{{ trans("messages.different-for-days") }}</label> */ ?>
	                                        <div class="panel-items checkbox-panel">
	                                            <div class="form-group mb-1">
	                                                <div class="form-check form-check-inline">
														<label class="checkbox" for="shift_start_time_checkbox">
   														<input type="checkbox" name="shift_start_time_checkbox" value="{{ config('constants.SELECTION_YES') }}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? 'checked' : '' ) }} onclick="addShiftStartTimeInfo(this)" id="shift_start_time_checkbox"><span class="checkmark"></span>{{ trans("messages.different-for-days") }}</label>
	                                                </div>
	                                            </div>
	                                        </div>
	
	
	                                    </td>
	                                    <td>
	                                        <div class=" add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time" name="monday_shift_start_time" onchange="addShiftStartTimeInfo(this)" value="{{ old('monday_shift_start_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_monday_start_time)) ? clientTime( $shiftTimingInfo->v_monday_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time" name="monday_shift_end_time" onchange="addShiftStartTimeInfo(this)" value="{{ old('monday_shift_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_monday_end_time)) ? clientTime( $shiftTimingInfo->v_monday_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class=" add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time shift-start-time-record" name="tuesday_shift_start_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('tuesday_shift_start_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_tuesday_start_time)) ? clientTime( $shiftTimingInfo->v_tuesday_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time shift-end-time-record" name="tuesday_shift_end_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('tuesday_shift_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_tuesday_end_time)) ? clientTime( $shiftTimingInfo->v_tuesday_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class=" add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time shift-start-time-record" name="wednesday_shift_start_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('wednesday_shift_start_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_wednesday_start_time)) ? clientTime( $shiftTimingInfo->v_wednesday_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time shift-end-time-record" name="wednesday_shift_end_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('wednesday_shift_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_wednesday_end_time)) ? clientTime( $shiftTimingInfo->v_wednesday_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class=" add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time shift-start-time-record" name="thursday_shift_start_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('thursday_shift_start_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_thursday_start_time)) ? clientTime( $shiftTimingInfo->v_thursday_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time shift-end-time-record" name="thursday_shift_end_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('thursday_shift_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_thursday_end_time)) ? clientTime( $shiftTimingInfo->v_thursday_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class=" add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time shift-start-time-record" name="friday_shift_start_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('friday_shift_start_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_friday_start_time)) ? clientTime( $shiftTimingInfo->v_friday_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time shift-end-time-record" name="friday_shift_end_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('friday_shift_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_friday_end_time)) ? clientTime( $shiftTimingInfo->v_friday_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class=" add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time shift-start-time-record" name="saturday_shift_start_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('saturday_shift_start_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_saturday_start_time)) ? clientTime( $shiftTimingInfo->v_saturday_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time shift-end-time-record" name="saturday_shift_end_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('saturday_shift_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_saturday_end_time)) ? clientTime( $shiftTimingInfo->v_saturday_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class=" add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time shift-start-time-record" name="sunday_shift_start_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('sunday_shift_start_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_sunday_start_time)) ? clientTime( $shiftTimingInfo->v_sunday_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time shift-end-time-record" name="sunday_shift_end_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_time)) && ( $recordInfo->e_different_week_day_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('sunday_shift_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_sunday_end_time)) ? clientTime( $shiftTimingInfo->v_sunday_end_time ) : ''  ) ) ) }}">
												</div>

	                                            <p class="text-muted shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                </tr>
	
	                                <tr>
	                                    <td>
	                                        <p>{{ trans("messages.shift-break-time") }}</p>
	                                        <?php /* <label><input type="checkbox" name="shift_break_time_checkbox" value="{{ config('constants.SELECTION_YES') }}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? 'checked' : '' ) }} onclick="addShiftBreakTimeInfo(this)">{{ trans("messages.different-for-days") }}</label> */ ?>
	                                        <div class="panel-items checkbox-panel">
	                                            <div class="form-group mb-1">
	                                                <div class="form-check form-check-inline">
														<label class="checkbox" for="shift_break_time_checkbox">
   														<input type="checkbox" name="shift_break_time_checkbox" value="{{ config('constants.SELECTION_YES') }}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? 'checked' : '' ) }} onclick="addShiftBreakTimeInfo(this)" id="shift_break_time_checkbox"><span class="checkmark"></span>{{ trans("messages.different-for-days") }}</label>
	                                                </div>
	                                            </div>
	                                        </div>
	
	                                    </td>
	                                    <td>
	                                        <div class="add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time" name="monday_shift_break_time" onchange="addShiftBreakTimeInfo(this)" value="{{ old('monday_shift_break_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_monday_break_start_time)) ? clientTime( $shiftTimingInfo->v_monday_break_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time" name="monday_shift_break_end_time" onchange="addShiftBreakTimeInfo(this)" value="{{ old('monday_shift_break_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_monday_break_end_time)) ? clientTime( $shiftTimingInfo->v_monday_break_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted break-shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class="add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time shift-break-time-record" name="tuesday_shift_break_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('tuesday_shift_break_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_tuesday_break_start_time)) ? clientTime( $shiftTimingInfo->v_tuesday_break_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time shift-break-end-time-record" name="tuesday_shift_break_end_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('tuesday_shift_break_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_tuesday_break_end_time)) ? clientTime( $shiftTimingInfo->v_tuesday_break_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted break-shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class="add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time shift-break-time-record" name="wednesday_shift_break_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('wednesday_shift_break_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_wednesday_break_start_time)) ? clientTime( $shiftTimingInfo->v_wednesday_break_start_time  ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time shift-break-end-time-record" name="wednesday_shift_break_end_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('wednesday_shift_break_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_wednesday_break_end_time)) ?  clientTime( $shiftTimingInfo->v_wednesday_break_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted break-shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class="add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time shift-break-time-record" name="thursday_shift_break_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('thursday_shift_break_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_thursday_break_start_time)) ? clientTime( $shiftTimingInfo->v_thursday_break_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time shift-break-end-time-record" name="thursday_shift_break_end_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('thursday_shift_break_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_thursday_break_end_time)) ? clientTime( $shiftTimingInfo->v_thursday_break_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted break-shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class="add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time shift-break-time-record" name="friday_shift_break_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('friday_shift_break_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_friday_break_start_time)) ? clientTime( $shiftTimingInfo->v_friday_break_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time shift-break-end-time-record" name="friday_shift_break_end_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('friday_shift_break_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_friday_break_end_time)) ? clientTime( $shiftTimingInfo->v_friday_break_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted break-shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class="add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time shift-break-time-record" name="saturday_shift_break_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('saturday_shift_break_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_saturday_break_start_time)) ? clientTime( $shiftTimingInfo->v_saturday_break_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time shift-break-end-time-record" name="saturday_shift_break_end_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('saturday_shift_break_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_saturday_break_end_time)) ? clientTime( $shiftTimingInfo->v_saturday_break_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted break-shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class="add-shift-time">
												<div class="d-flex align-items-center">
													<input type="text" class="form-control shift-start-time shift-break-time-record" name="sunday_shift_break_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('sunday_shift_break_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_sunday_break_start_time)) ? clientTime( $shiftTimingInfo->v_sunday_break_start_time ) : ''  ) ) ) }}">
													<span>-</span>
													<input type="text" class="form-control shift-end-time shift-break-end-time-record" name="sunday_shift_break_end_time" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_different_week_day_break_time)) && ( $recordInfo->e_different_week_day_break_time ==  config('constants.SELECTION_YES') ) ) ? '' : 'disabled' ) }} value="{{ old('sunday_shift_break_end_time' , ( (isset($shiftTimingInfo) && (!empty($shiftTimingInfo->v_sunday_break_end_time)) ? clientTime( $shiftTimingInfo->v_sunday_break_end_time ) : ''  ) ) ) }}">
												</div>
	                                            <p class="text-muted break-shift shift-duration-text mb-0"></p>
	                                        </div>
	                                    </td>
	                                </tr>
	
	
	                            </tbody>
	                        </table>
	                    </div>
	                    <div class="col-md-12 submit-sticky">
	                        <?php if (isset($recordInfo) && ($recordInfo->i_id > 0)) { ?>
	                            <input type="hidden" name="record_id" value="{{ Wild_tiger::encode($recordInfo->i_id) }}">
	                            <button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
	                        <?php } else { ?>
	                            <button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
	                        <?php } ?>
	                        <a href="{{ config('constants.SHIFT_MASTER_URL') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
	                    </div>
	
	                </div>
	            </div>
	        </div>
	    </div>
    </div>
    {!! Form::close() !!}
</main>
<script>
    $("#add-shift-master-form").validate({
        errorClass: "invalid-input",
        onfocusout: false,
        onkeyup: false,
        rules: {
            shift_name: {
                required: true,
                noSpace: true,
                validateUniqueShiftName: true,
            },
            shift_code: {
                required: true,
                noSpace: true
            },
            shift_type: {
                required: true,
                noSpace: true
            },

        },
        messages: {
            shift_name: {
                required: "{{ trans('messages.require-shift-name') }}"
            },
            shift_code: {
                required: "{{ trans('messages.require-shift-code') }}"
            },
            shift_type: {
                required: "{{ trans('messages.require-shift-type') }}"
            },

        },
        submitHandler: function(form) {
            var monday_shift_start_time_status = true;
            var monday_shift_end_time_status = false;
            var tuesday_shift_start_time_status = false;
            var tuesday_shift_end_time_status = false;
            var wednesday_shift_start_time_status = false;
            var wednesday_shift_end_time_status = false;
            var thursday_shift_start_time_status = false;
            var thursday_shift_end_time_status = false;
            var friday_shift_start_time_status = false;
            var friday_shift_end_time_status = false;

            $('.shift-start-time-tbody tr').each(function() {
                var monday_shift_start_time = $.trim($("[name='monday_shift_start_time']").val());
                var monday_shift_end_time = $.trim($("[name='monday_shift_end_time']").val());
                var tuesday_shift_start_time = $.trim($("[name='tuesday_shift_start_time']").val());
                var tuesday_shift_end_time = $.trim($("[name='tuesday_shift_end_time']").val());
                var wednesday_shift_start_time = $.trim($("[name='wednesday_shift_start_time']").val());
                var wednesday_shift_end_time = $.trim($("[name='wednesday_shift_end_time']").val());
                var thursday_shift_start_time = $.trim($("[name='thursday_shift_start_time']").val());
                var thursday_shift_end_time = $.trim($("[name='thursday_shift_end_time']").val());
                var friday_shift_start_time = $.trim($("[name='friday_shift_start_time']").val());
                var friday_shift_end_time = $.trim($("[name='friday_shift_end_time']").val());

                if (monday_shift_start_time != "" && monday_shift_start_time != null) {
                    monday_shift_start_time_status = false;
                    if ((monday_shift_end_time == "" || monday_shift_end_time == null) && (monday_shift_end_time_status != true)) {
                        $("[name='monday_shift_end_time']").focus();
                        monday_shift_end_time_status = true;
                    }
                    if ((tuesday_shift_start_time == "" || tuesday_shift_start_time == null) && (monday_shift_end_time_status != true && tuesday_shift_start_time_status != true)) {
                        $("[name='tuesday_shift_start_time']").focus();
                        tuesday_shift_start_time_status = true;
                    }
                    if ((tuesday_shift_end_time == "" || tuesday_shift_end_time == null) && (monday_shift_end_time_status != true && tuesday_shift_start_time_status != true && tuesday_shift_end_time_status != true)) {
                        $("[name='tuesday_shift_end_time']").focus();
                        tuesday_shift_end_time_status = true;
                    }

                    if ((wednesday_shift_start_time == "" || wednesday_shift_start_time == null) && (monday_shift_end_time_status != true && tuesday_shift_start_time_status != true && tuesday_shift_end_time_status != true && wednesday_shift_start_time_status != true)) {
                        $("[name='wednesday_shift_start_time']").focus();
                        wednesday_shift_start_time_status = true;
                    }
                    if ((wednesday_shift_end_time == "" || wednesday_shift_end_time == null) && (monday_shift_end_time_status != true && tuesday_shift_start_time_status != true && tuesday_shift_end_time_status != true && wednesday_shift_start_time_status != true && wednesday_shift_end_time_status != true)) {
                        $("[name='wednesday_shift_end_time']").focus();
                        wednesday_shift_end_time_status = true;
                    }
                    if ((thursday_shift_start_time == "" || thursday_shift_start_time == null) && (monday_shift_end_time_status != true && tuesday_shift_start_time_status != true && tuesday_shift_end_time_status != true && wednesday_shift_start_time_status != true && wednesday_shift_end_time_status != true && thursday_shift_start_time_status != true)) {
                        $("[name='thursday_shift_start_time']").focus();
                        thursday_shift_start_time_status = true;
                    }

                    if ((thursday_shift_end_time == "" || thursday_shift_end_time == null) && (monday_shift_end_time_status != true && tuesday_shift_start_time_status != true && tuesday_shift_end_time_status != true && wednesday_shift_start_time_status != true && wednesday_shift_end_time_status != true && thursday_shift_start_time_status != true && thursday_shift_end_time_status != true)) {
                        $("[name='thursday_shift_end_time']").focus();
                        thursday_shift_end_time_status = true;
                    }
                    if ((friday_shift_start_time == "" || friday_shift_start_time == null) && (monday_shift_end_time_status != true && tuesday_shift_start_time_status != true && tuesday_shift_end_time_status != true && wednesday_shift_start_time_status != true && wednesday_shift_end_time_status != true && thursday_shift_start_time_status != true && thursday_shift_end_time_status != true && friday_shift_start_time_status != true)) {
                        $("[name='friday_shift_start_time']").focus();
                        friday_shift_start_time_status = true;
                    }

                    if ((friday_shift_end_time == "" || friday_shift_end_time == null) && (monday_shift_end_time_status != true && tuesday_shift_start_time_status != true && tuesday_shift_end_time_status != true && wednesday_shift_start_time_status != true && wednesday_shift_end_time_status != true && thursday_shift_start_time_status != true && thursday_shift_end_time_status != true && friday_shift_start_time_status != true && friday_shift_end_time_status != true)) {
                        $("[name='friday_shift_end_time']").focus();
                        friday_shift_end_time_status = true;
                    }
                }
            });
            if (monday_shift_start_time_status != false) {
                $("[name='monday_shift_start_time']").focus();
                alertifyMessage("error", "{{ trans('messages.common-start-shift-msg',['module'=> trans('messages.shift-monday')]) }}");
                return false;
            }
            if (monday_shift_end_time_status != false) {
                alertifyMessage("error", "{{ trans('messages.common-end-shift-msg',['module'=> trans('messages.shift-monday')]) }}");
                return false;
            }
            if (tuesday_shift_start_time_status != false) {
                alertifyMessage("error", "{{ trans('messages.common-start-shift-msg',['module'=> trans('messages.shift-tuesday')]) }}");
                return false;
            }
            if (tuesday_shift_end_time_status != false) {
                alertifyMessage("error", "{{ trans('messages.common-end-shift-msg',['module'=> trans('messages.shift-tuesday')]) }}");
                return false;
            }
            if (wednesday_shift_start_time_status != false) {
                alertifyMessage("error", "{{ trans('messages.common-start-shift-msg',['module'=> trans('messages.shift-wednesday')]) }}");
                return false;
            }
            if (wednesday_shift_end_time_status != false) {
                alertifyMessage("error", "{{ trans('messages.common-end-shift-msg',['module'=> trans('messages.shift-wednesday')]) }}");
                return false;
            }
            if (thursday_shift_start_time_status != false) {
                alertifyMessage("error", "{{ trans('messages.common-start-shift-msg',['module'=> trans('messages.shift-thursday')]) }}");
                return false;
            }
            if (thursday_shift_end_time_status != false) {
                alertifyMessage("error", "{{ trans('messages.common-end-shift-msg',['module'=> trans('messages.shift-thursday')]) }}");
                return false;
            }
            if (friday_shift_start_time_status != false) {
                alertifyMessage("error", "{{ trans('messages.common-start-shift-msg',['module'=> trans('messages.shift-friday')]) }}");
                return false;
            }
            if (friday_shift_end_time_status != false) {
                alertifyMessage("error", "{{ trans('messages.common-end-shift-msg',['module'=> trans('messages.shift-friday')]) }}");
                return false;
            }
            var confirm_box = "";
            var confirm_box_msg = "";
            <?php
            if (isset($recordInfo) && ($recordInfo->i_id > 0)) { ?>
                confirm_box = "{{ trans('messages.update-shift') }}";
                confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-shift')]) }}";
            <?php
            } else {
            ?>
                confirm_box = "{{ trans('messages.add-shift') }}";
                confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-shift')]) }}";

            <?php
            }
            ?>
            alertify.confirm(confirm_box, confirm_box_msg, function() {
                $(".shift-start-time-record").prop('disabled', false);
                $(".shift-end-time-record").prop('disabled', false);
                $(".shift-break-time-record").prop('disabled', false);
                $(".shift-break-end-time-record").prop('disabled', false);
                showLoader()
                form.submit();
            }, function() {});
        }
    });

    function addShiftStartTimeInfo(thisitem) {
        var shift_start_time_checkbox = $.trim($("[name='shift_start_time_checkbox']:checked").val());
        var monday_shift_start_time = $.trim($("[name='monday_shift_start_time']").val());
        var monday_shift_end_time = $.trim($("[name='monday_shift_end_time']").val());

        var shift_duration_text = $.trim( $("[name='monday_shift_start_time']").parents('.add-shift-time').find('.shift-duration-text').text());
        
        if (shift_start_time_checkbox != "" && shift_start_time_checkbox != null) {
        	if (shift_start_time_checkbox == '{{config("constants.SELECTION_YES")}}') {
                //$(".shift-start-time").trigger('change');
                
                $(".shift-start-time").each(function(){
                	calculateShiftDuration(this);	
				})
                
                $(".shift-start-time-record").prop('disabled', false);
                $(".shift-end-time-record").prop('disabled', false);
            } else {
                $(".shift-start-time-record").prop('disabled', true);
                $(".shift-end-time-record").prop('disabled', true);
			}

        } else {
           	
			$(".shift-start-time-record").prop('disabled', true);
            $(".shift-end-time-record").prop('disabled', true);

            if ((monday_shift_start_time != "" && monday_shift_start_time != null) || (monday_shift_end_time != "" && monday_shift_end_time != null)) {
                $(".shift-start-time-record").val(monday_shift_start_time);
                $(".shift-end-time-record").val(monday_shift_end_time);
                $(".shift.shift-duration-text").text(shift_duration_text);

            } else {
                $(".shift-start-time-record").val('');
                $(".shift-end-time-record").val('');
            }
        }
		
    }

    function addShiftBreakTimeInfo(thisitem) {
        var shift_break_time_checkbox = $.trim($("[name='shift_break_time_checkbox']:checked").val());
        var monday_shift_break_time = $.trim($("[name='monday_shift_break_time']").val());
        var monday_shift_break_end_time = $.trim($("[name='monday_shift_break_end_time']").val());

        var shift_duration_text = $.trim( $("[name='monday_shift_break_time']").parents('.add-shift-time').find('.shift-duration-text').text());
        
        if (shift_break_time_checkbox != "" && shift_break_time_checkbox != null) {

            if (shift_break_time_checkbox == '{{config("constants.SELECTION_YES")}}') {
                $(".shift-break-time-record").prop('disabled', false);
                $(".shift-break-end-time-record").prop('disabled', false);
            } else {
                $(".shift-break-time-record").prop('disabled', true);
                $(".shift-break-end-time-record").prop('disabled', true);
            }
            
        } else {
            $(".shift-break-time-record").prop('disabled', true);
            $(".shift-break-end-time-record").prop('disabled', true);

            if ((monday_shift_break_time != "" && monday_shift_break_time != null) || (monday_shift_break_end_time != "" && monday_shift_break_end_time != null)) {
                $(".shift-break-time-record").val(monday_shift_break_time);
                $(".shift-break-end-time-record").val(monday_shift_break_end_time);
                $(".break-shift.shift-duration-text").text(shift_duration_text);

            } else {
                $(".shift-break-time-record").val('');
                $(".shift-break-end-time-record").val('');
            }
        }
       
    }

    var shift_master_url = '{{config("constants.SHIFT_MASTER_URL")}}' + '/';

    $.validator.addMethod("validateUniqueShiftName", function(value, element) {

        var result = true;
        $.ajax({
            type: "POST",
            async: false,
            url: shift_master_url + 'checkUniqueShiftName',
            dataType: "json",
            data: {
                "_token": "{{ csrf_token() }}",
                'shift_name': $.trim($("[name='shift_name']").val()),
                'shift_type': $.trim($("[name='shift_type']:checked").val()),
                'record_id': ($.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null)
            },
            beforeSend: function() {

            },
            success: function(response) {

                if (response.status_code == 1) {
                    return false;
                } else {
                    result = false;
                    return true;
                }
            }
        });
        return result;
    }, '{{ trans("messages.error-unique-shift-name") }}');

    function calculateShiftDuration(thisitem){
    	var start_time = $.trim($(thisitem).parents('.add-shift-time').find('.shift-start-time').val());
		var end_time = $.trim($(thisitem).parents('.add-shift-time').find('.shift-end-time').val());

		var field_name = $.trim($(thisitem).attr("name"));

		if( start_time != "" && start_time != null && end_time != null && end_time != "" ){
			var duration = diffBetweenTimeIntoJS(start_time,end_time , thisitem);
			$(thisitem).parents('.add-shift-time').find('.shift-duration-text').html(duration);
		} else {
			$(thisitem).parents('.add-shift-time').find('.shift-duration-text').html("");
		}

		var shift_start_time_checkbox = $.trim($("[name='shift_start_time_checkbox']:checked").val());
		
		if( field_name == "monday_shift_start_time"  || field_name == "monday_shift_end_time" ){
			var shift_start_time_checkbox = $.trim($("[name='shift_start_time_checkbox']:checked").val());
			var shift_duration_text = $.trim( $("[name='monday_shift_start_time']").parents('.add-shift-time').find('.shift-duration-text').text());
			if( shift_start_time_checkbox != "" && shift_start_time_checkbox != null  ){
				
			} else {
				$(".shift.shift-duration-text").text(shift_duration_text);
			}
		}

		if( field_name == "monday_shift_break_time"  || field_name == "monday_shift_break_end_time" ){
			var shift_start_time_checkbox = $.trim($("[name='shift_break_time_checkbox']:checked").val());
			var shift_duration_text = $.trim( $("[name='monday_shift_break_time']").parents('.add-shift-time').find('.shift-duration-text').text());

			if( shift_start_time_checkbox != "" && shift_start_time_checkbox != null  ){
				
			} else {
				$(".break-shift.shift-duration-text").text(shift_duration_text);
			}
		}
	}
    
    $(".shift-start-time,.shift-end-time").on('change' , function(){
    	calculateShiftDuration(this);
		/* var start_time = $.trim($(this).parents('.add-shift-time').find('.shift-start-time').val());
		var end_time = $.trim($(this).parents('.add-shift-time').find('.shift-end-time').val());

		var field_name = $.trim($(this).attr("name"));

		if( start_time != "" && start_time != null && end_time != null && end_time != "" ){
			var duration = diffBetweenTimeIntoJS(start_time,end_time , this);
			$(this).parents('.add-shift-time').find('.shift-duration-text').html(duration);
		} else {
			$(this).parents('.add-shift-time').find('.shift-duration-text').html("");
		}

		var shift_start_time_checkbox = $.trim($("[name='shift_start_time_checkbox']:checked").val());
		
		if( field_name == "monday_shift_start_time"  || field_name == "monday_shift_end_time" ){
			var shift_start_time_checkbox = $.trim($("[name='shift_start_time_checkbox']:checked").val());
			var shift_duration_text = $.trim( $("[name='monday_shift_start_time']").parents('.add-shift-time').find('.shift-duration-text').text());
			if( shift_start_time_checkbox != "" && shift_start_time_checkbox != null  ){
				
			} else {
				$(".shift.shift-duration-text").text(shift_duration_text);
			}
		}

		if( field_name == "monday_shift_break_time"  || field_name == "monday_shift_break_end_time" ){
			var shift_start_time_checkbox = $.trim($("[name='shift_break_time_checkbox']:checked").val());
			var shift_duration_text = $.trim( $("[name='monday_shift_break_time']").parents('.add-shift-time').find('.shift-duration-text').text());

			if( shift_start_time_checkbox != "" && shift_start_time_checkbox != null  ){
				
			} else {
				$(".break-shift.shift-duration-text").text(shift_duration_text);
			}
		} */
		/* var time_format = 'hh:mm'; 
		start_time = moment(start_time, time_format ),
		end_time = moment(end_time,  time_format );
		var related_field_name = '';
		related_field_name = "end_time";
		if( $(this).hasClass('shift-start-time') != false ){
			related_field_name = "start_time";
		}
		
		if( related_field_name == "start_time" ){
			if( start_time.isAfter(end_time) != false ){
				$(this).parents('.add-shift-time').find('.shift-start-time').val("");
				alertifyMessage('error',"{{ trans('messages.invalid-time-selection') }}");
			}
		} else {
			if( end_time.isBefore(start_time) != false ){
				$(this).parents('.add-shift-time').find('.shift-end-time').val("");
				alertifyMessage('error',"{{ trans('messages.invalid-time-selection') }}");
			}
		} */ 
		
		
	})
	
	$(document).ready(function(){
		var record_id = '<?php echo ( ( isset($recordInfo) && ($recordInfo->i_id > 0) ) ? $recordInfo->i_id : 0 )  ?>';
		if( record_id != "" && record_id != null && record_id > 0 ){
			$("[name='monday_shift_start_time']").trigger('change');
			$("[name='monday_shift_break_time']").trigger('change');	
		}
		$(".shift-start-time,.shift-end-time").mdtimepicker({ 
			readOnly: false, 
			theme: 'blue', 
			clearBtn: true, 
			datepicker : false, 
			ampm: true, 
			format: 'h:mm tt' 
		});
		
	})
    
</script>

@endsection