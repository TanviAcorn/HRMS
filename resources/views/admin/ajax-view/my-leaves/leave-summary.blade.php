	<div class="row">
	        <div class="col-lg-9 panding-leave-card mb-3">
	            <div class="card card-display border-0 px-2 py-3 h-100">
	                <div class="card-body px-2 py-0">
	                    <div class="panding-leave-display">
	                        <div class="row">
	                            <div class="col-12">
	                                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.leave-counts") }}</h5>
	                            </div>
	                            <div class="col-12 mt-2">
	                                <div class="row year-wise-leave-count">
	                                    @include(config('constants.AJAX_VIEW_FOLDER') .'my-leaves/year-wise-leave-count')
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	        <div class="col-lg-3 col-sm-6 profile-detail-card mb-3">
	            <div class="card card-display border-0 px-2 py-4 h-100">
	                <div class="card-body d-flex align-items-center px-2 py-0">
	                    <div class="row align-items-center">
	                        <div class="col-12">
	                        	<a title="{{ trans('messages.apply-leave') }}" href="javascript:void(0);" data-emp-id="{{ ( isset($employeeId) ? $employeeId : '' ) }}" class="btn bg-theme text-white" onclick="openApplyLeaveModel(this)">{{ trans("messages.apply-leave") }}</a>
	                            <a href="{{ config('constants.LEAVE_REPORT_URL') . ( isset($employeeId) ? '/' . $employeeId : '' ) }}" target="_blank" class="w-100 d-block my-2" title="{{ trans('messages.view-leave-requests') }}">{{ trans("messages.view-leave-requests") }}</a>
	                            <a href="javascript:void(0);" class="w-100 d-block my-2" onclick="openLevePolicy(this);" title="{{ trans('messages.leave-policy-explanation') }}">{{ trans("messages.leave-policy-explanation") }}</a>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	
	    <div class="row">
	        <div class="col-12 my-4">
	            <h5 class="bg-title">{{ trans("messages.leave-stats") }}</h5>
	        </div>
	        <div class="col-md-4 total-leave-card mb-3 h-100">
	            @include(config('constants.AJAX_VIEW_FOLDER') .'my-leaves/week-wise-leave-count')
	        </div>
	        <div class="col-md-8 total-leave-card mb-3">
	            @include(config('constants.AJAX_VIEW_FOLDER') .'my-leaves/month-wise-leave-count')
	        </div>
	        @if(count($leaveTypeDetails) > 0 )
	        	@foreach($leaveTypeDetails as $leaveTypeDetail)
	        		<div class="col-md-3 total-leave-card leave-type-count-chart-html">
	        			@include(config('constants.AJAX_VIEW_FOLDER') .'my-leaves/leave-type-count-chart')
			        </div>
	        	@endforeach
	        @endif
		</div>