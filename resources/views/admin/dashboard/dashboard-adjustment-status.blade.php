<!-- <div class="adjustment-status">
    <div class="card card-display border-0 mb-3 p-2 py-3 py-lg-3 pt-md-3">
        <div class="card-body py-0">
            <div class="row">
                <div class="col-8">
                    <h5 class="card-title mb-0">{{ trans("messages.adjustment-status") }}
                    </h5>
                </div>
                <div class="col-4 text-right view-all-btn">
                    <a href="{{ config('constants.TIME_OFF_MASTER_URL') }}" title="{{ trans('messages.view-all') }}">{{ trans("messages.view-all") }}
                    </a>
                </div>
            </div>
            <div class="row py-0 mt-3 adjustment-status-card quick-links">
                <div class="col-6 mt-2 adjustment-status-item">
                    <h4 class="adjustment-status-note mb-0">{{ trans('messages.no-of-adjustment-in-last-six-months') }}</h4>
                    <div class="adjustment-leave mt-3">
                       @if(  isset($adjustmentDetails) && (count($adjustmentDetails) > 0 ) )
                           <div class="adjustment-value">
                           		<p class="mb-0">
                            		<span>{{ count($adjustmentDetails) }}</span> Days
                           		 </p>
                            </div>
						@else
                        	{{ trans('messages.no-adjustment-found') }}
						@endif
                        
                    </div>
                </div>
                <div class="col-6 mt-2 adjustment-status-item quick-link-card">
                    @if(  isset($adjustmentDetails) && (count($adjustmentDetails) > 0 ) )
	                    <h4 class="adjustment-status-note mb-0">Last adjustment taken on</h4>
	                    <p class="adjustment-date">{{ ( isset($adjustmentDetails[0]->dt_time_off_date) ? convertDateFormat($adjustmentDetails[0]->dt_time_off_date) : '' ) }}</p>
	                @else 
	                	{{ trans('messages.no-adjustment') }}
	                @endif
	                
	                 @if( ( session()->has('user_employee_id') ) && ( session()->has('user_employee_id') > 0 ) )
	                    <a title="{{ trans('messages.apply-time-off') }}" data-emp-id="{{ Wild_tiger::encode(session()->get('user_employee_id')) }}" href="javascript:void(0);" onclick="openApplyTimeOffModal(this);" class="d-block mt-1 q-links">{{ trans("messages.apply-time-off") }}</a>
	                    @endif
                </div>
            </div>
        </div>
    </div>
</div> -->
