<?php /*?>
            <div class="col-lg-4 col-lg-5 col-md-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 pb-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="row px-0 border-bottom">
                            <div class="col-12 profile-details-title-card">
                                <h5 class="profile-details-title mb-0">{{ trans("messages.no-time-off-taken") }}</h5>
                                <h6 class="profile-details-date mb-0 ml-auto">11/07/2022 - 12/21/2022</h6>
                            </div>
                        </div>
                        <div class="member-list next-day ">
                            <div class="member-card">
                                <div class="more-member">
                                    <div class="more-member-list more-members-body">
                                        <div class="member-card">
                                            <div class="member-img-card">
                                                <img src="{{ asset ('images/demo.jpg') }}" alt="icon" class="member-img">
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">mubassir mansuri</p>
                                                <p class="bd-time">Web Designer</p>
                                            </div>
                                            <div class="member-time pt-3">
                                                <p class="bd-time">{{ trans("messages.no-time-off-taken") }}</p>
                                            </div>
                                        </div>
                                        <div class="member-card">
                                            <div class="member-img-card member-img-none bg-3">
                                                <p class="member-img-text">JA</p>
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">Jaymin Ahir</p>
                                                <p class="bd-time">Web developer</p>
                                            </div>
                                            <div class="member-time pt-3">
                                                <p class="bd-time">{{ trans("messages.no-time-off-taken") }}</p>
                                            </div>
                                        </div>
                                        <div class="member-card">
                                            <div class="member-img-card">
                                                <img src="{{ asset ('images/demo.jpg') }}" alt="icon" class="member-img">
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">mubassir mansuri</p>
                                                <p class="bd-time">Web developer</p>
                                            </div>
                                            <div class="member-time pt-3">
                                                <p class="bd-time">{{ trans("messages.no-time-off-taken") }}</p>
                                            </div>
                                        </div>
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
                                <h5 class="profile-details-title mb-0">{{ trans("messages.most-time-off-taken") }}</h5>
                                @if( isset($searchDateRange) && (!empty($searchDateRange)) )
                                	<h6 class="profile-details-date mb-0 ml-auto">{{ $searchDateRange }}</h6>
                                @endif
                            </div>
                        </div>
                        <div class="member-list next-day ">
                            <div class="member-card">
                                <div class="more-member">
                                    <div class="more-member-list more-members-body">
                                    	@if( count($timeOffSummaryDetails) > 0  )
                                    		@foreach($timeOffSummaryDetails as $timeOffSummaryDetail)
	                                        <div class="member-card">
	                                             @php
						                         	$employeeData['employee_name'] = ( isset($timeOffSummaryDetail['employeeName']) ? $timeOffSummaryDetail['employeeName'] : "" );
												 	$employeeData['profile_pic'] = ( isset($timeOffSummaryDetail['profilePic']) ? $timeOffSummaryDetail['profilePic'] : "" );
						                         echo employeeProfilePicView($employeeData);
						                         @endphp
	                                            <div class="member-detail pt-3">
	                                                <p class="member-name">{{ ( isset($timeOffSummaryDetail['employeeName']) ? $timeOffSummaryDetail['employeeName'] : '' )  }}</p>
	                                                <p class="bd-time">{{ ( isset($timeOffSummaryDetail['designationName']) ? $timeOffSummaryDetail['designationName'] : '' )  }}</p>
	                                            </div>
	                                            <div class="member-time pt-3">
	                                                <p class="member-name">{{ ( isset($timeOffSummaryDetail['timeOffDuration']) ? convertSecondIntoHour($timeOffSummaryDetail['timeOffDuration']) .' '. trans ("messages.hours"): '' )  }}</p>
	                                                <p class="bd-time">{{ ( isset($timeOffSummaryDetail['timeOffOccurence']) ? $timeOffSummaryDetail['timeOffOccurence'] .' '. trans ("messages.instance"): 0 )  }}</p>
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