    <div class="event-list mb-3">
        <div class="card card-body p-0">
            <div class="tab-link-card">
            	<?php  ?>
                <div class="px-2" >{{ trans("messages.today") }}</div>
                <?php  ?>

                <div class="tab-link current" data-tab="tab-2"><img src="{{ asset ('images/cake.png') }}" alt="icon" class="link-icon"><span class="link-num">{{ ( isset($todayBirthDayDetails) ? count($todayBirthDayDetails) : 0 ) }}</span> <span class="link-text">{{ trans("messages.birthdays") }}</span></div>

                <div class="tab-link" data-tab="tab-3"><img src="{{ asset ('images/work.png') }}" alt="icon" class="link-icon"><span class="link-num">{{ ( isset($todayWorkAnniversaryDetails) ? count($todayWorkAnniversaryDetails) : 0 ) }}</span><span class="link-text">{{ trans("messages.work-anniversary") }}</span></div>

                <div class="tab-link" data-tab="tab-4"><img src="{{ asset ('images/add.png') }}" alt="icon" class="link-icon"><span class="link-num">{{ ( isset($todayJoiningDetails) ? count($todayJoiningDetails) : 0 ) }}</span><span class="link-text">{{ trans("messages.new-joinee") }}</span></div>

                <div class="card-btn ml-auto">
                    <button class="btn my-event-btn" data-toggle="collapse" data-target="#event">
                    	<i class="fas fa-angle-up my-nav-icon"></i>
                    </button>
                </div>
            </div>

            <div class="my-event collapse show px-3" id="event">
                <?php /* ?>
                <div id="tab-1" class="tab-content px-3">
                    <div class="row my-today">
                        <div class="col-12 today-card w-100">
                            <div class="row">
                                <div class="tab-title col-12">
                                    <h6>{{ trans("messages.birthdays-today") }}</h6>
                                </div>
                                <div class="member-list col-12 mt-4">
                                    <div class="row">
                                        @if(count($todayBirthDayDetails) > 0 )
                                        	@foreach($todayBirthDayDetails as  $todayBirthDayDetail)
	                                        <div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
	                                            @php
	                                            $employeeData['employee_name'] = ( isset($todayBirthDayDetail->v_employee_full_name) ? $todayBirthDayDetail->v_employee_full_name : "" );
												$employeeData['profile_pic'] = ( isset($todayBirthDayDetail->v_profile_pic) ? $todayBirthDayDetail->v_profile_pic : "" );
	                                            employeeProfilePicView($employeeData);
	                                            @endphp
	                                            <div class="member-detail pt-3">
	                                                <p class="member-name">{{ $employeeData['employee_name'] }}</p>
	                                            </div>
	                                        </div>
                                        	@endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row my-today">
                        <div class="col-12 today-card w-100">
                            <div class="row">
                                <div class="tab-title col-12">
                                    <h6>{{ trans("messages.work-anniversary-today") }}</h6>
                                </div>
                                <div class="member-list col-12 mt-4">
                                    <div class="row">
                                        <div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
                                            <div class="member-img-card">
                                                <img src="{{ asset ('images/demo.jpg') }}" alt="icon" class="member-img">
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">mubassir mansuri</p>
                                            </div>
                                        </div>
                                        <div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
                                            <div class="member-img-card">
                                                <img src="{{ asset ('images/demo.jpg') }}" alt="icon" class="member-img">
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">mubassir mansuri</p>
                                            </div>
                                        </div>
                                        <div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
                                            <div class="member-img-card member-img-none bg-2">
                                                <p class="member-img-text">MM</p>
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">mubassir mansuri</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row my-today">
                        <div class="col-12 today-card w-100">
                            <div class="row">
                                <div class="tab-title col-12">
                                    <h6>{{ trans("messages.new-joinee-today") }}</h6>
                                </div>
                                <div class="member-list col-12 mt-4">
                                    <div class="row">
                                        <div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6 ">
                                            <div class="member-img-card">
                                                <img src="{{ asset ('images/demo.jpg') }}" alt="icon" class="member-img">
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">mubassir mansuri</p>
                                            </div>
                                        </div>
                                        <div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
                                            <div class="member-img-card">
                                                <img src="{{ asset ('images/demo.jpg') }}" alt="icon" class="member-img">
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">mubassir mansuri</p>
                                            </div>
                                        </div>
                                        <div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
                                            <div class="member-img-card member-img-none bg-2">
                                                <p class="member-img-text">MM</p>
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">mubassir mansuri</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row next-day">
                        <div class="col-12 next-day-card w-100">
                            <div class="row">
                                <div class="tab-title2 col-12">
                                    <h6>{{ trans("messages.next-7-days") }}</h6>
                                </div>
                                <div class="member-list col-12 mt-4">
                                    <div class="row">
                                        <div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
                                            <div class="member-img-card">
                                                <img src="{{ asset ('images/demo.jpg') }}" alt="icon" class="member-img">
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">mubassir mansuri</p>
                                                <p class="bd-time mb-0">15<sup>th</sup> Nov, 2022</p>
                                                <p class="bd-time">{{ trans("messages.birthdays") }}</p>
                                            </div>
                                        </div>
                                        <div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
                                            <div class="member-img-card member-img-none bg-1">
                                                <p class="member-img-text">MM</p>
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">mubassir mansuri</p>
                                                <p class="bd-time mb-0">15<sup>th</sup> Nov, 2022</p>
                                                <p class="bd-time">{{ trans("messages.work-anniversary") }}</p>
                                            </div>
                                        </div>
                                        <div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
                                            <div class="member-img-card member-img-none bg-1">
                                                <p class="member-img-text">MM</p>
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">mubassir mansuri</p>
                                                <p class="bd-time mb-0">28<sup>th</sup> Nov, 2022</p>
                                                <p class="bd-time">{{ trans("messages.work-anniversary") }}</p>
                                            </div>
                                        </div>
                                        <div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
                                            <div class="member-img-card">
                                                <img src="{{ asset ('images/demo.jpg') }}" alt="icon" class="member-img">
                                            </div>
                                            <div class="member-detail pt-3">
                                                <p class="member-name">mubassir mansuri</p>
                                                <p class="bd-time mb-0">30<sup>th</sup> Nov, 2022</p>
                                                <p class="bd-time">{{ trans("messages.birthdays") }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php */ ?>
                <div id="tab-2" class="tab-content current px-3">
                    <div class="row my-today">
                        <div class="col-12 today-card">
                            <div class="row">
                                <div class="tab-title col-12">
                                    <h6>{{ trans("messages.birthdays-today") }}</h6>
                                </div>
                                <div class="member-list col-12 mt-4">
                                    <div class="row">
                                        @if(count($todayBirthDayDetails) > 0 )
                                        	@foreach($todayBirthDayDetails as  $todayBirthDayDetail)
                                        		<div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
		                                             @php
		                                            $employeeData['employee_code'] = ( isset($todayBirthDayDetail->v_employee_code) ? $todayBirthDayDetail->v_employee_code : "" );
		                                            $employeeData['employee_name'] = ( isset($todayBirthDayDetail->v_employee_full_name) ? $todayBirthDayDetail->v_employee_full_name : "" );
													$employeeData['profile_pic'] = ( isset($todayBirthDayDetail->v_profile_pic) ? $todayBirthDayDetail->v_profile_pic : "" );
		                                            echo employeeProfilePicView($employeeData);
		                                            @endphp
		                                            <div class="member-detail pt-3">
		                                                <p class="member-name ellipsis-member-name" title="{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}">{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}</p>
		                                            </div>
		                                        </div>
                                        	@endforeach
                                        
                                        @else
                                        <div class="birthday-not-found col-12">
                                            <img src="{{ asset ('images/no-birthday.png') }}" alt="" class="no-birthday-today"><span class="no-event-title">
                                                {{ trans('messages.no-birthday-today') }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row next-day">
                        <div class="col-12 next-day-card w-100">
                            <div class="row">
                                <div class="tab-title2 col-12">
                                    <h6>{{ trans("messages.next-7-days") }}</h6>
                                </div>
                                @if(count($nextSevenDayBirthDayDetails) > 0 )
                                <div class="member-list col-12 mt-4">
                                    <div class="row">
                                        @php
                                        	$additionalNextSevenBirthDayDetails = [];
                                        @endphp
                                        @if(count($nextSevenDayBirthDayDetails) > 0 )
                                        	@php
                                        		$additionalNextSevenBirthDayDetails = array_slice( objectToArray($nextSevenDayBirthDayDetails) , 4 , count($nextSevenDayBirthDayDetails)  );
                                        		$nextSevenDayBirthDayDetails = array_slice( objectToArray($nextSevenDayBirthDayDetails) , 0 , 4 );
                                        	@endphp
                                        	@foreach($nextSevenDayBirthDayDetails as  $nextSevenDayBirthDayDetail)
                                        		@php $nextSevenDayBirthDayDetail = (object)$nextSevenDayBirthDayDetail @endphp
	                                        	<div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
		                                            @php
		                                            $employeeData['employee_code'] = ( isset($nextSevenDayBirthDayDetail->v_employee_code) ? $nextSevenDayBirthDayDetail->v_employee_code : "" );
		                                            $employeeData['employee_name'] = ( isset($nextSevenDayBirthDayDetail->v_employee_full_name) ? $nextSevenDayBirthDayDetail->v_employee_full_name : "" );
													$employeeData['profile_pic'] = ( isset($nextSevenDayBirthDayDetail->v_profile_pic) ? $nextSevenDayBirthDayDetail->v_profile_pic : "" );
													echo employeeProfilePicView($employeeData);
		                                            @endphp
		                                            <div class="member-detail pt-3">
		                                                <p class="member-name ellipsis-member-name" title="{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}">{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}</p>
		                                                <p class="bd-time">{{ ( isset($nextSevenDayBirthDayDetail->dt_birth_date) ? dashboardEventDate($nextSevenDayBirthDayDetail->dt_birth_date , 'jS M') : '' ) }}</p>
		                                            </div>
		                                        </div>
	                                        @endforeach
                                        @endif
                                        @if(count($additionalNextSevenBirthDayDetails) > 0 )
                                        <div class="member-card col-sm-2 col-4">
                                            <div class="more-member">
                                                <div class="member-mdtn">
                                                    <button class="my-more-btn btn b-none" type="button" data-toggle="collapse" data-target="#moremembers" aria-expanded="false" aria-controls="moremembers">
                                                        <i class="fas fa-plus"></i>{{ count($additionalNextSevenBirthDayDetails) }}
                                                    </button>
                                                </div>

                                                <div class="collapse more-members" id="moremembers">
                                                    <div class="card more-member-list more-members-body">
                                                        @foreach($additionalNextSevenBirthDayDetails as $additionalNextSevenBirthDayDetail)
                                                        	@php $additionalNextSevenBirthDayDetail = (object)$additionalNextSevenBirthDayDetail @endphp
	                                                        <div class="member-card">
	                                                            @php
	                                                            $employeeData['employee_code'] = ( isset($additionalNextSevenBirthDayDetail->v_employee_code) ? $additionalNextSevenBirthDayDetail->v_employee_code : "" );
					                                            $employeeData['employee_name'] = ( isset($additionalNextSevenBirthDayDetail->v_employee_full_name) ? $additionalNextSevenBirthDayDetail->v_employee_full_name : "" );
																$employeeData['profile_pic'] = ( isset($additionalNextSevenBirthDayDetail->v_profile_pic) ? $additionalNextSevenBirthDayDetail->v_profile_pic : "" );
					                                            echo employeeProfilePicView($employeeData);
					                                            @endphp
	                                                            <div class="member-detail pt-3">
	                                                                <p class="member-name" title="{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}">{{ $employeeData['employee_name']  . ' ('.$employeeData['employee_code'].')' }}</p>
	                                                                <p class="bd-time">{{ ( isset($nextSevenDayBirthDayDetail->dt_birth_date) ? dashboardEventDate($nextSevenDayBirthDayDetail->dt_birth_date , 'jS M, Y' ) : '' ) }}</p>
	                                                            </div>
	                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       
                                        
                                        @endif
                                    </div>
                                </div>
                                @else
                                    <div class="col-12 mt-4">{{ trans('messages.no-record-found') }}</div>
                                @endif
                                
                            </div>
                        </div>
                    </div> -->
                </div>
                <?php 
				$allNumbers = convertNumberIntoWords();
                ?>
                <div id="tab-3" class="tab-content">
                    <div class="row my-today">
                        <div class="col-12 today-card">
                            <div class="row">
                                <div class="tab-title col-12">
                                    <h6>{{ trans("messages.work-anniversary-today") }}</h6>
                                </div>
                                <div class="member-list anniversary-list col-12 mt-4">
                                    <div class="row">
                                        @if(count($todayWorkAnniversaryDetails) > 0 )
                                        	@foreach($todayWorkAnniversaryDetails as $todayWorkAnniversaryDetail)
	                                        	<div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
		                                             @php
		                                             $noOfYear = date('Y') - date('Y' ,strtotime($todayWorkAnniversaryDetail->dt_joining_date));
		                                             $employeeData['employee_code'] = ( isset($todayWorkAnniversaryDetail->v_employee_code) ? $todayWorkAnniversaryDetail->v_employee_code : "" );
						                             $employeeData['employee_name'] = ( isset($todayWorkAnniversaryDetail->v_employee_full_name) ? $todayWorkAnniversaryDetail->v_employee_full_name : "" );
													 $employeeData['profile_pic'] = ( isset($todayWorkAnniversaryDetail->v_profile_pic) ? $todayWorkAnniversaryDetail->v_profile_pic : "" );
						                             echo employeeProfilePicView($employeeData);
						                             @endphp
		                                            <div class="member-detail pt-3">
		                                                <p class="member-name ellipsis-member-name" title="{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}">{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}</p>
		                                                <p class="bd-time mb-0">{{ ( isset($todayWorkAnniversaryDetail->dt_joining_date) ?  dashboardEventDate ( $todayWorkAnniversaryDetail->dt_joining_date , 'jS M, Y' )  : '' ) }}</p>
		                                                <p class="bd-time">{{ $noOfYear . config('constants.ANNIVERSARY_YEAR_VALUE') }}</p>
		                                            </div>
		                                        </div>
	                                        @endforeach
                                        @else
                                        	<div class="anniversary-not-found col-12">
	                                            <img src="{{ asset ('images/no-work.png') }}" alt="" class="no-anniversary-today"><span class="no-event-title">
	                                                {{ trans('messages.no-anniversary-today') }}
	                                            </span>
	                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!--<div class="row next-day">
		                        <div class="col-12 next-day-card w-100">
		                            <div class="row">
		                                <div class="tab-title2 col-12">
		                                    <h6>{{ trans("messages.next-7-days") }}</h6>
		                                </div>
		                                <div class="member-list col-12 mt-4">
		                                    @if(count($nextSevenDayWorkAnniversaryDetails) > 0 )
		                                    <div class="row">
		                                        @php
		                                        	$additionalNextSevenWorkAnniversaryDetails = [];
		                                        @endphp
		                                        @if(count($nextSevenDayWorkAnniversaryDetails) > 0 )
		                                        	@php
		                                        		$additionalNextSevenWorkAnniversaryDetails = array_slice( objectToArray($nextSevenDayWorkAnniversaryDetails) , 4 , count($nextSevenDayWorkAnniversaryDetails)  );
		                                        		$nextSevenDayBirthDayDetails = array_slice( objectToArray($nextSevenDayWorkAnniversaryDetails) , 0 , 4 );
		                                        	@endphp
		                                        	@foreach($nextSevenDayWorkAnniversaryDetails as  $nextSevenDayWorkAnniversaryDetail)
		                                        		@php 
		                                        		$nextSevenDayWorkAnniversaryDetail = (object)$nextSevenDayWorkAnniversaryDetail;
		                                        		$noOfYear = date('Y') - date('Y' ,strtotime($nextSevenDayWorkAnniversaryDetail->dt_joining_date)); 
		                                        		@endphp
			                                        	<div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
				                                            @php
				                                            $employeeData['employee_code'] = ( isset($nextSevenDayWorkAnniversaryDetail->v_employee_code) ? $nextSevenDayWorkAnniversaryDetail->v_employee_code : "" );
				                                            $employeeData['employee_name'] = ( isset($nextSevenDayWorkAnniversaryDetail->v_employee_full_name) ? $nextSevenDayWorkAnniversaryDetail->v_employee_full_name : "" );
															$employeeData['profile_pic'] = ( isset($nextSevenDayWorkAnniversaryDetail->v_profile_pic) ? $nextSevenDayWorkAnniversaryDetail->v_profile_pic : "" );
				                                            echo employeeProfilePicView($employeeData);
				                                            @endphp
				                                            <div class="member-detail pt-3">
				                                                <p class="member-name ellipsis-member-name" title="{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}">{{ $employeeData['employee_name']  . ' ('.$employeeData['employee_code'].')' }}</p>
				                                                <p class="bd-time mb-0">{{ ( isset($nextSevenDayWorkAnniversaryDetail->dt_joining_date) ?  dashboardEventDate ( $nextSevenDayWorkAnniversaryDetail->dt_joining_date , 'jS M, Y' )  : '' ) }}</p>
				                                                 <p class="bd-time">{{ $noOfYear . config('constants.ANNIVERSARY_YEAR_VALUE') }}</p>
				                                            </div>
				                                        </div>
			                                        @endforeach
		                                        @endif
		                                        @if(count($additionalNextSevenWorkAnniversaryDetails) > 0 )
		                                        <div class="member-card col-sm-2 col-4">
		                                            <div class="more-member">
		                                                <div class="member-mdtn">
		                                                    <button class="my-more-btn btn b-none" type="button" data-toggle="collapse" data-target="#moremembers" aria-expanded="false" aria-controls="moremembers">
		                                                        <i class="fas fa-plus"></i>{{ count($additionalNextSevenWorkAnniversaryDetails) }}
		                                                    </button>
		                                                </div>
		
		                                                <div class="collapse more-members" id="moremembers">
		                                                    <div class="card more-member-list more-members-body">
		                                                        @foreach($additionalNextSevenWorkAnniversaryDetails as $additionalNextSevenWorkAnniversaryDetail)
		                                                        	@php $additionalNextSevenWorkAnniversaryDetail = (object)$additionalNextSevenWorkAnniversaryDetail @endphp
			                                                        <div class="member-card">
			                                                            @php
			                                                             $noOfYear = date('Y') - date('Y' ,strtotime($additionalNextSevenWorkAnniversaryDetail->dt_joining_date));
			                                                            $employeeData['employee_code'] = ( isset($additionalNextSevenWorkAnniversaryDetail->v_employee_code) ? $additionalNextSevenWorkAnniversaryDetail->v_employee_code : "" );
							                                            $employeeData['employee_name'] = ( isset($additionalNextSevenWorkAnniversaryDetail->v_employee_full_name) ? $additionalNextSevenWorkAnniversaryDetail->v_employee_full_name : "" );
																		$employeeData['profile_pic'] = ( isset($additionalNextSevenWorkAnniversaryDetail->v_profile_pic) ? $additionalNextSevenWorkAnniversaryDetail->v_profile_pic : "" );
							                                            echo employeeProfilePicView($employeeData);
							                                            @endphp
			                                                            <div class="member-detail pt-3">
			                                                                <p class="member-name" title="{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}">{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}</p>
			                                                                <p class="bd-time mb-0">{{ ( isset($additionalNextSevenWorkAnniversaryDetail->dt_joining_date) ?  dashboardEventDate ( $additionalNextSevenWorkAnniversaryDetail->dt_joining_date , 'jS M, Y' ) : '' ) }}</p>
			                                                                <p class="bd-time">{{ $noOfYear . config('constants.ANNIVERSARY_YEAR_VALUE') }}</p>
			                                                            </div>
			                                                        </div>
		                                                        @endforeach
		                                                    </div>
		                                                </div>
		                                            </div>
		                                        </div>
		                                        @endif
		                                    </div>
		                                    @else
		                                    	{{ trans('messages.no-record-found') }}
		                                    @endif
		                                </div>
		                            </div>
		                        </div>
		                    </div>-->
                        </div>
                    </div>
                </div>
                <div id="tab-4" class="tab-content">
                    <div class="row my-today">
                        <div class="col-12 today-card">
                            <div class="row">
                                <div class="tab-title col-12">
                                    <h6>{{ trans("messages.new-joinee-today") }}</h6>
                                </div>
                                <div class="member-list col-12 mt-4">
                                    <div class="row">
                                        @if(count($todayJoiningDetails)  > 0 )
                                        	@foreach($todayJoiningDetails as $todayJoiningDetail)
                                        	<div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6">
	                                            @php
	                                            $employeeData['employee_code'] = ( isset($todayJoiningDetail->v_employee_code) ? $todayJoiningDetail->v_employee_code : "" );
		                                         $employeeData['employee_name'] = ( isset($todayJoiningDetail->v_employee_full_name) ? $todayJoiningDetail->v_employee_full_name : "" );
												 $employeeData['profile_pic'] = ( isset($todayJoiningDetail->v_profile_pic) ? $todayJoiningDetail->v_profile_pic : "" );
		                                         echo employeeProfilePicView($employeeData);
		                                         @endphp
	                                            <div class="member-detail pt-3">
	                                                <p class="member-name ellipsis-member-name" title="{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}">{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}</p>
	                                            </div>
	                                        </div>
	                                        @endforeach
                                        
                                        @else
	                                        <div class="new-joinee-not-found col-12 ">
	                                            <img src="{{ asset ('images/no-team.png') }}" alt="" class="no-new-joinee-today">
	                                            <span class="no-event-title">
	                                                {{ trans('messages.no-new-joining-today') }}
	                                            </span>
	                                        </div>
                                        
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row next-day">
                        <div class="col-12 next-day-card w-100">
                            <div class="row">
                                <div class="tab-title2 col-12">
                                    <h6>{{ trans("messages.last-7-days") }}</h6>
                                </div>
                                <div class="member-list col-12 mt-4">
                                    @if(count($lastSevenDayJoiningDetails) > 0 )
                                    <div class="row">
                                        @php
                                        	$additionalLastSevenJoiningDetails = [];
                                        @endphp
                                        @if(count($lastSevenDayJoiningDetails) > 0 )
                                        	@php
                                        		$additionalLastSevenJoiningDetails = array_slice( objectToArray($lastSevenDayJoiningDetails) , 4 , count($lastSevenDayJoiningDetails)  );
                                        		$lastSevenDayJoiningDetails = array_slice( objectToArray($lastSevenDayJoiningDetails) , 0 , 4 );
                                        	@endphp
                                        	@foreach($lastSevenDayJoiningDetails as  $lastSevenDayJoiningDetail)
                                        		@php $lastSevenDayJoiningDetail = (object)$lastSevenDayJoiningDetail @endphp
	                                        	<div class="member-card col-xl-3 col-lg-3 col-md-2 col-sm-3 col-6s">
		                                            @php
		                                            $employeeData['employee_code'] = ( isset($lastSevenDayJoiningDetail->v_employee_code) ? $lastSevenDayJoiningDetail->v_employee_code : "" );
		                                            $employeeData['employee_name'] = ( isset($lastSevenDayJoiningDetail->v_employee_full_name) ? $lastSevenDayJoiningDetail->v_employee_full_name : "" );
													$employeeData['profile_pic'] = ( isset($lastSevenDayJoiningDetail->v_profile_pic) ? $lastSevenDayJoiningDetail->v_profile_pic : "" );
		                                            echo employeeProfilePicView($employeeData);
		                                            @endphp
		                                            <div class="member-detail pt-3">
		                                                <p class="member-name ellipsis-member-name" title="{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}">{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}</p>
		                                                <p class="bd-time">{{ ( isset($lastSevenDayJoiningDetail->dt_joining_date) ? dashboardEventDate($lastSevenDayJoiningDetail->dt_joining_date ,'jS M, Y' ) : '' ) }}</p>
		                                            </div>
		                                        </div>
	                                        @endforeach
                                        @endif
                                        @if(count($additionalLastSevenJoiningDetails) > 0 )
                                        <div class="member-card col-sm-2 col-4">
                                            <div class="more-member">
                                                <div class="member-mdtn">
                                                    <button class="my-more-btn btn b-none" type="button" data-toggle="collapse" data-target="#moremembers" aria-expanded="false" aria-controls="moremembers">
                                                        <i class="fas fa-plus"></i>{{ count($additionalLastSevenJoiningDetails) }}
                                                    </button>
                                                </div>
												<div class="collapse more-members" id="moremembers">
                                                    <div class="card more-member-list more-members-body">
                                                        @foreach($additionalLastSevenJoiningDetails as $additionalLastSevenJoiningDetail)
                                                        	@php $additionalLastSevenJoiningDetail = (object)$additionalLastSevenJoiningDetail @endphp
	                                                        <div class="member-card">
	                                                            @php
	                                                            $employeeData['employee_code'] = ( isset($additionalLastSevenJoiningDetail->v_employee_code) ? $additionalLastSevenJoiningDetail->v_employee_code : "" );
					                                            $employeeData['employee_name'] = ( isset($additionalLastSevenJoiningDetail->v_employee_full_name) ? $additionalLastSevenJoiningDetail->v_employee_full_name : "" );
																$employeeData['profile_pic'] = ( isset($additionalLastSevenJoiningDetail->v_profile_pic) ? $additionalLastSevenJoiningDetail->v_profile_pic : "" );
					                                            echo employeeProfilePicView($employeeData);
					                                            @endphp
	                                                            <div class="member-detail pt-3">
	                                                                <p class="member-name" title="{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}">{{ $employeeData['employee_name'] . ' ('.$employeeData['employee_code'].')' }}</p>
	                                                                <p class="bd-time">{{ ( isset($additionalLastSevenJoiningDetail->dt_joining_date) ? dashboardEventDate($additionalLastSevenJoiningDetail->dt_joining_date , 'jS M, Y') : '' ) }}</p>
	                                                            </div>
	                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @else
                                    	{{ trans('messages.no-record-found') }}	
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $('.tab-link-card .tab-link').click(function() {
                var tab_id = $(this).attr('data-tab');

                $('.tab-link-card .tab-link').removeClass('current');
                $('.tab-content').removeClass('current');

                $(this).addClass('current');
                $(" #" + tab_id).addClass('current');
            })
        })
    </script>

    <script>
        $(document).click(function(e) {
            if (!$(e.target).is('.more-members')) {
                $('.body').collapse('hide');
            }
        });
    </script>


    <script>
        /** CLOSE MAIN NAVIGATION WHEN CLICKING OUTSIDE THE MAIN NAVIGATION AREA**/
        $(document).on('click', function(e) {
            /* bootstrap collapse js adds "in" class to your collapsible element*/
            var menu_opened = $('#moremembers').hasClass('show');

            if (!$(e.target).closest('#moremembers').length &&
                !$(e.target).is('#moremembers') &&
                menu_opened === true) {
                $('#moremembers').collapse('hide');
            }

        });
    </script>