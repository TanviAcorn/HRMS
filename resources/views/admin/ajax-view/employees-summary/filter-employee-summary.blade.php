
 		<?php 
            $employeeCountInfo = [];
            $employeeCountInfo['team_id'] = $employeeTeamId;
            $employeeCountInfo['city_id'] = $employeeCityId;
           // $employeeCountInfo['employee_count'] = $totalEmpCount;
            $employeeCountInfoLink = Wild_tiger::encode(json_encode($employeeCountInfo));
        ?>
		<div class="row">
            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 py-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="attendance-counts border-left-2">
                            <div class="attendance-counts-card">
                                <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.total-employees") }}</h5>
                                <p class="attendance-counts-numbers">{{ (!empty($totalEmpCount) ? $totalEmpCount :0) }} </p>
                                @if(isset($totalEmpCount) && $totalEmpCount > 0 && checkPermission('view_employee_report') != false)
                               		<a href="{{ ( $totalEmpCount > 0 ? config('constants.EMPLOYEE_REPORT_URL') . '/'. $employeeCountInfoLink  : 'javascript:void(0)' )  }}" title="{{ trans('messages.view-employees') }}" target="_blank">{{ trans("messages.view-employees") }}</a>
                               	@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           <?php 
            	$probationPeriodLinkInfo = [];
            	$probationPeriodLinkInfo['status'] = config('constants.PROBATION_EMPLOYMENT_STATUS');
            	$probationPeriodLinkInfo['team_id'] = $employeeTeamId;
            	$probationPeriodLinkInfo['city_id'] = $employeeCityId;
            	//$probationPeriodLinkInfo['employee_count'] = $totalEmpCount;
            	$probationPeriodLink = Wild_tiger::encode(json_encode($probationPeriodLinkInfo));
       	 	?>
            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 py-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="attendance-counts border-left-3">
                            <div class="attendance-counts-card">
                                <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.in-probation") }}</h5>
                                <p class="attendance-counts-numbers">{{ (!empty($totalEmpInProbationCount) ? $totalEmpInProbationCount :0) }}</p>
                                @if(isset($totalEmpInProbationCount) && $totalEmpInProbationCount > 0  && checkPermission('view_employee_report') != false)
                                	<a href="{{ ( $totalEmpInProbationCount > 0 ? config('constants.EMPLOYEE_REPORT_URL') . '/'. $probationPeriodLink  : 'javascript:void(0)' )  }}" title="{{ trans('messages.view-employees') }}" target="_blank">{{ trans("messages.view-employees") }}</a>
                                @endif
 							</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
            $noticePeriodLinkInfo = [];
            $noticePeriodLinkInfo['status'] = config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS');
            $noticePeriodLinkInfo['team_id'] = $employeeTeamId;
            $noticePeriodLinkInfo['city_id'] = $employeeCityId;
            
            $noticePeriodLink = Wild_tiger::encode(json_encode($noticePeriodLinkInfo));
            ?>
            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 py-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="attendance-counts border-left-6">
                            <div class="attendance-counts-card">
                                <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.in-notice-period") }}</h5>
                                <p class="attendance-counts-numbers">{{ (!empty($totalEmpInNoticePeriodCount) ? $totalEmpInNoticePeriodCount :0) }}</p>
                                @if(isset($totalEmpInNoticePeriodCount) && $totalEmpInNoticePeriodCount > 0 && ( checkPermission('view_employee_report') != false ) )
                                	<a href="{{ ( $totalEmpInNoticePeriodCount > 0 ? config('constants.EMPLOYEE_REPORT_URL') . '/'. $noticePeriodLink : 'javascript:void(0)' ) }}" title="{{ trans('messages.view-employees') }}" target="_blank">{{ trans("messages.view-employees") }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5 col-sm-6 panding-leave-card mb-3 employee-count-designation-chart">
            	@include(config('constants.AJAX_VIEW_FOLDER') .'employees-summary/employee-count-by-team-chart')
            </div>
            <div class="col-lg-5 col-sm-6 panding-leave-card mb-3 employee-count-location-chart">
             	@include(config('constants.AJAX_VIEW_FOLDER') .'employees-summary/employee-count-by-location-chart')
            </div>
            
        </div>