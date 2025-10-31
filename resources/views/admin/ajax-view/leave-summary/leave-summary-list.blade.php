			<?php /* ?>
			<div class="col-lg-4 col-lg-5 col-md-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 pb-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="row px-0 border-bottom">
                            <div class="col-12 profile-details-title-card">
                                <h5 class="profile-details-title mb-0">{{ trans("messages.no-leave-taken") }}</h5>
                                @if( isset($searchDateRange) && (!empty($searchDateRange)) )
                                <h6 class="profile-details-date mb-0 ml-auto">{{ $searchDateRange }}</h6>
                                @endif
                            </div>
                        </div>
                        <div class="member-list next-day ">
                            <div class="member-card">
                                <div class="more-member">
                                    <div class="more-member-list more-members-body">
                                        @if( count($notTakenLeaveDetails) > 0  )
	                                   		@foreach($notTakenLeaveDetails as $notTakenLeaveDetail)
	                                   			<div class="member-card">
		                                            @if( isset($notTakenLeaveDetail->v_employee_name) && (!empty($notTakenLeaveDetail->v_employee_name)) )
			                                            <div class="member-img-card">
			                                                <img src="{{ $notTakenLeaveDetail->v_employee_name }}" alt="icon" class="member-img">
			                                            </div>
		                                            @else
			                                            <div class="member-img-card member-img-none bg-3">
			                                                <p class="member-img-text">{{ getInitialLetter($notTakenLeaveDetail->v_employee_full_name) }} </p>
			                                            </div>
		                                            @endif
		                                            <div class="member-detail pt-3">
		                                                <p class="member-name">{{ ( isset($notTakenLeaveDetail->v_employee_full_name) ? $notTakenLeaveDetail->v_employee_full_name : '' )  }}</p>
		                                                <p class="bd-time">{{ ( isset($notTakenLeaveDetail->v_employee_full_name) ? $notTakenLeaveDetail->v_employee_full_name : '' ) }} </p>
		                                            </div>
		                                            <div class="member-time pt-3">
		                                                <p class="bd-time">{{ trans("messages.no-leave-taken") }}</p>
		                                            </div>
		                                        </div>
	                                        @endforeach
	                                   	@else
	                                   		<div>
												{{ trans('messages.no-record-found')}}
											</div>
	                                   	@endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php */ ?>
            <div class="col-lg-4 col-lg-5 col-md-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 pb-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="row px-0 border-bottom">
                            <div class="col-12 profile-details-title-card">
                                <h5 class="profile-details-title mb-0">{{ trans("messages.most-leave-taken") }}</h5>
                                @if( isset($searchDateRange) && (!empty($searchDateRange)) )
                                <h6 class="profile-details-date mb-0 ml-auto">{{ $searchDateRange }}</h6>
                                @endif
                            </div>
                        </div>
                        <div class="member-list next-day ">
                            <div class="member-card">
                                <div class="more-member">
                                    <div class="more-member-list more-members-body">
                                   	@if( count($mostLeaveTakenDetails) > 0  )
                                   		@foreach($mostLeaveTakenDetails as $mostLeaveTakenDetail)
                                   			<div class="member-card">
	                                            @php
						                         	$employeeData['employee_name'] = ( isset($mostLeaveTakenDetail['employee_name']) ? $mostLeaveTakenDetail['employee_name'] : "" );
												 	$employeeData['profile_pic'] = ( isset($mostLeaveTakenDetail['profile_pic']) ? $mostLeaveTakenDetail['profile_pic'] : "" );
						                         echo employeeProfilePicView($employeeData);
						                         @endphp
	                                            <div class="member-detail pt-3">
	                                                <p class="member-name">{{ ( isset($mostLeaveTakenDetail['employee_name']) ? $mostLeaveTakenDetail['employee_name'] : '' )  }}</p>
	                                                <p class="bd-time">{{ ( isset($mostLeaveTakenDetail['designation_name']) ? $mostLeaveTakenDetail['designation_name'] : '' ) }} </p>
	                                            </div>
	                                            <div class="member-time pt-3">
	                                                <p class="member-name">{{ ( isset($mostLeaveTakenDetail['leave_count']) ? trans('messages.leave-count-display' , [ 'count' => $mostLeaveTakenDetail['leave_count'] ] )  : '' ) }} </p>
	                                                <p class="bd-time">{{ ( isset($mostLeaveTakenDetail['leaveOccurence']) ? $mostLeaveTakenDetail['leaveOccurence'] .' '. trans ("messages.instance"): 0 )  }}</p>
	                                            </div>
	                                        </div>
                                        @endforeach
                                   	@else
                                   		<div class="mt-4">
											{{ trans('messages.no-record-found')}}
										</div>
                                   	@endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>