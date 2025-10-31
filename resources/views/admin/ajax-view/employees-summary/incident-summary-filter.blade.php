		<?php 
            $incidentTotalCountInfo = [];
            $incidentTotalCountInfo['team_id'] = $employeeTeamId;
           	$incidentCountInfoLink = Wild_tiger::encode(json_encode($incidentTotalCountInfo));
        ?>

		<div class="row">
            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 py-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="attendance-counts border-left-2">
                            <div class="attendance-counts-card">
                                <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.total-employees") }}</h5>
                                <p class="attendance-counts-numbers"><span class="employee-count-info">{{ (!empty($employeeCountInfo) ? $employeeCountInfo : 0) }}</span></p>
                                <span class="p-1"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 py-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="attendance-counts border-left-3">
                            <div class="attendance-counts-card">
                                <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.total-incidents") }}</h5>
                                <p class="attendance-counts-numbers"><span class="incident-count-info">{{ (!empty($incidentCountInfo) ? $incidentCountInfo : 0) }}</span></p>
                                @if((isset($incidentCountInfo)) && ( $incidentCountInfo > 0 ) && ( checkPermission('view_incident_report') != false ) )
                                	<a class="incident-summary-total-count" href="{{ ( $incidentCountInfo > 0 ? config('constants.INCIDENT_REPORT_URL') . '/'. $incidentCountInfoLink  : 'javascript:void(0)' )  }}" target="_blank" title="{{ trans('messages.view-incidents') }}">{{ trans("messages.view-incidents") }}</a>
                               @endif 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
            $incidentOpenTotalCountInfo  = [];
            $incidentOpenTotalCountInfo['team_id'] = $employeeTeamId;
            $incidentOpenTotalCountInfo['incident_open_status'] = config("constants.OPEN");
           	$incidentOpenInfoLink = Wild_tiger::encode(json_encode($incidentOpenTotalCountInfo));
            ?>
            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 py-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="attendance-counts border-left-6">
                            <div class="attendance-counts-card">
                                <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.open-incidents") }}</h5>
                                <p class="attendance-counts-numbers"><span class="incident-open-count-info">{{ (!empty($incidentOpenCountInfo) ? $incidentOpenCountInfo : 0) }}</span></p>
                                @if((isset($incidentOpenCountInfo)) && ($incidentOpenCountInfo > 0 ) && ( checkPermission('view_incident_report') != false ) )
                                	<a class="incident-summary-open-count" href="{{ ( $incidentOpenCountInfo > 0 ? config('constants.INCIDENT_REPORT_URL') . '/'. $incidentOpenInfoLink  : 'javascript:void(0)' )  }}" target="_blank" title="{{ trans('messages.view-incidents') }}">{{ trans("messages.view-incidents") }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
            $incidentCloseTotalCountInfo = [];
            $incidentCloseTotalCountInfo['team_id'] = $employeeTeamId;
            $incidentCloseTotalCountInfo['incident_close_status'] = config("constants.CLOSE");
            $incidentCloseInfoLink = Wild_tiger::encode(json_encode($incidentCloseTotalCountInfo));
            
            ?>
            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
                <div class="card card-display border-0 px-2 py-3 h-100">
                    <div class="card-body px-2 py-0">
                        <div class="attendance-counts border-left-10">
                            <div class="attendance-counts-card">
                                <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.closed-incidents") }}</h5>
                                <p class="attendance-counts-numbers"><span class="incident-close-count-info">{{ (!empty($incidentCloseCountInfo) ? $incidentCloseCountInfo : 0) }}</span></p>
                                @if((isset($incidentCloseCountInfo)) && ($incidentCloseCountInfo > 0 ) && ( checkPermission('view_incident_report') != false ) )
                                	<a class="incident-summary-close-count" href="{{ ( $incidentCloseCountInfo > 0 ? config('constants.INCIDENT_REPORT_URL') . '/'. $incidentCloseInfoLink  : 'javascript:void(0)' )  }}" target="_blank" title="{{ trans('messages.view-incidents') }}">{{ trans("messages.view-incidents") }}</a>
                                @endif
                                <span class="p-1"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>