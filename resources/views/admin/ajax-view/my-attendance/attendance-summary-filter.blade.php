			<div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 py-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="attendance-counts border-left-2 h-100">
                            <div class="attendance-counts-card">
                                <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.total-employees") }}</h5>
                                <p class="attendance-counts-numbers">{{ isset($allEmployeeCount) ? decimalAmount($allEmployeeCount) : 0  }}</p>
                                <span class="p-1"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 py-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="attendance-counts border-left-12 h-100">
                            <div class="attendance-counts-card">
                                <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.on-leave-today") }}</h5>
                                <p class="attendance-counts-numbers">{{ isset($leaveCount) ? decimalAmount($leaveCount) : 0  }}</p>
                                @if( isset($leaveCount) && ( $leaveCount > 0 ) )
                                	@if(checkPermission('view_leave_report') != false)
                                		<a href="{{ config('constants.SITE_URL') . 'view-today-leave' }}" title="{{ trans('messages.view-employees') }}">{{ trans("messages.view-employees") }}</a>
                                	@endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 py-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="attendance-counts border-left-9 h-100">
                            <div class="attendance-counts-card">
                                <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.holiday-weekly-off") }}</h5>
                                <p class="attendance-counts-numbers">{{ isset($weekOffCount) ? decimalAmount($weekOffCount) : 0  }}</p>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 py-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="attendance-counts border-left-3 h-100">
                            <div class="attendance-counts-card">
                                <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.available-today") }}</h5>
                                <p class="attendance-counts-numbers">{{ isset($availableCount) && ( $availableCount > 0 ) ? decimalAmount($availableCount) : 0  }}</p>
                                <?php /* ?>
                                <a href="javascript:void(0);" title="{{ trans('messages.view-employees') }}">{{ trans("messages.view-employees") }}</a>
                                <?php */ ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 py-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="attendance-counts border-left-5 h-100">
                            <div class="attendance-counts-card">
                                <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.adjustment-request-today") }}</h5>
                                <p class="attendance-counts-numbers">{{ isset($adjustmentCount) ? decimalAmount($adjustmentCount) : 0  }}</p>
                                @if( isset($adjustmentCount) && ( $adjustmentCount > 0 ) )
                                	@if(checkPermission('view_time_off_report') != false)
                                		<a href="{{ config('constants.SITE_URL') . 'view-today-adjustment' }}" title="{{ trans('messages.view-employees') }}">{{ trans("messages.view-employees") }}</a>
                                	@endif
                                @endif
                                <span class="p-1"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            