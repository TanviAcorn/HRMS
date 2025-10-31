			<div class="row">
	            <div class="col-lg-9 panding-leave-card mb-3">
	                <div class="card card-display border-0 px-2 py-3 h-100">
	                    <div class="card-body px-2 py-0">
	                        <div class="panding-leave-display">
	                            <div class="row">
	                                <div class="col-12">
	                                    <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.time-off-counts") }}</h5>
	                                </div>
	                                <div class="col-12 mt-2">
	                                    <div class="row">
	                                        <div class="col-md-2 col-sm-4 col-6">
	                                            <div class="p-leave-card w-100">
	                                                <div class="p-leave-count">
	                                                    <img src="{{ asset ('images/total-leaves.png') }}" alt="total-leave-icon" class="p-leave-icon"><span class="leave-count" onkeyup="countAnimation(this);">{{ ( isset($timeOffCountDetails['total_count']) ? $timeOffCountDetails['total_count'] : 0 )  }}</span>
	                                                </div>
	                                                <div class="p-leave-name w-100">
	                                                    <p class="details-text mb-0">{{ trans("messages.total-time-off") }}</p>
	                                                </div>
	                                            </div>
	                                        </div>
	                                        <div class="col-md-2 col-sm-4 col-6 px-xl-2 px-lg-0">
	                                            <div class="p-leave-card w-100">
	                                                <div class="p-leave-count">
	                                                    <img src="{{ asset ('images/panding-approval.png') }}" alt="panding-approval" class="p-leave-icon"><span class="leave-count" onkeyup="countAnimation(this);">{{ ( isset($timeOffCountDetails['pending_count']) ? $timeOffCountDetails['pending_count'] : 0 )  }}</span>
	                                                </div>
	                                                <div class="p-leave-name w-100">
	                                                    <p class="details-text mb-0">{{ trans("messages.pending-for-approval") }}</p>
	                                                </div>
	                                            </div>
	                                        </div>
	                                        <div class="col-md-2 col-sm-4 col-6">
	                                            <div class="p-leave-card w-100">
	                                                <div class="p-leave-count">
	                                                    <img src="{{ asset ('images/rejected.png') }}" alt="total-leave-icon" class="p-leave-icon"><span class="leave-count" onkeyup="countAnimation(this);">{{ ( isset($timeOffCountDetails['cancelled_count']) ? $timeOffCountDetails['cancelled_count'] : 0 )  }}</span>
	                                                </div>
	                                                <div class="p-leave-name w-100">
	                                                    <p class="details-text mb-0">{{ trans("messages.cancelled") }}</p>
	                                                </div>
	                                            </div>
	                                        </div>
	                                        <div class="col-md-2 col-sm-4 col-6">
	                                            <div class="p-leave-card w-100">
	                                                <div class="p-leave-count">
	                                                    <img src="{{ asset ('images/approved.png') }}" alt="total-leave-icon" class="p-leave-icon"><span class="leave-count" onkeyup="countAnimation(this);">{{ ( isset($timeOffCountDetails['approved_count']) ? $timeOffCountDetails['approved_count'] : 0 )  }}</span>
	                                                </div>
	                                                <div class="p-leave-name w-100">
	                                                    <p class="details-text mb-0">{{ trans("messages.approved") }}</p>
	                                                </div>
	                                            </div>
	                                        </div>
	                                        <div class="col-md-2 col-sm-4 col-6">
	                                            <div class="p-leave-card w-100">
	                                                <div class="p-leave-count">
	                                                    <img src="{{ asset ('images/cancelled.png') }}" alt="total-leave-icon" class="p-leave-icon"><span class="leave-count" onkeyup="countAnimation(this);">{{ ( isset($timeOffCountDetails['rejected_count']) ? $timeOffCountDetails['rejected_count'] : 0 )  }}</span>
	                                                </div>
	                                                <div class="p-leave-name w-100">
	                                                    <p class="details-text mb-0">{{ trans("messages.rejected") }}</p>
	                                                </div>
	                                            </div>
	                                        </div>
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
	                                <a title="{{ trans('messages.apply-time-off') }}" data-emp-id="{{ ( isset($employeeId) ? $employeeId : '' ) }}" href="javascript:void(0);" class="btn bg-theme text-white" onclick="openApplyTimeOffModal(this);">
	                                    {{ trans("messages.apply-time-off") }}
	                                </a>
	                                <a href="{{ config('constants.TIME_OFF_REPORT_URL') . ( isset($employeeId) ? '/' . $employeeId : '' )  }}" target="_blank" class="w-100 d-block my-2" title="{{ trans('messages.view-time-offs-requests') }}">{{ trans("messages.view-time-offs-requests") }}</a>
	                                <a href="javascript:void(0);" onclick="openTimeOffPolicy();" class="w-100 d-block my-2"title="{{ trans('messages.time-off-policy-explanation') }}">{{ trans("messages.time-off-policy-explanation") }}</a>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	
	        <div class="row">
	            <div class="col-12 my-4">
	                <h5 class="bg-title">{{ trans("messages.time-off-stats") }}</h5>
	            </div>
	            <div class="col-md-4 total-leave-card mb-3 h-100">
	                @include(config('constants.AJAX_VIEW_FOLDER') .'time-off/week-wise-time-off-chart')
	            </div>
	            <div class="col-md-8 total-leave-card mb-3">
	                @include(config('constants.AJAX_VIEW_FOLDER') .'time-off/month-wise-time-off-chart')
	            </div>
	        </div>
	    	<script>
			    $(document).ready(function(){
			    	$(".leave-count").trigger('keyup')
				});
			</script>