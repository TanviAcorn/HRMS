@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.login-history") }}</h1><span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        <button type="button" class="btn btn btn-theme text-white button-actions-top-bar d-sm-flex align-items-center border btn-sm" data-toggle="collapse" data-target="#filter" title="Toggle Filter"><i class="fas fa-filter mr-sm-2"></i><span class="d-sm-block d-none">{{ trans("messages.filter") }}</span></button>
        </div>
    </div>
    <div class="container-fluid visit-history pt-3">
        <div class="collapse" id="filter">
            <div class="card mb-3 depedent-row">
                <div class="card-body">
                    <div class="row">
                        @if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) ) ) )
			               
			                	<div class="col-xl-2 col-lg-4 col-12">
			                		<?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' )  );?> 
			                	</div>
			                    <div class="col-xl-3 col-lg-4 col-12">
			                        <?php echo statusWiseEmployeeList('search_employee' , (isset($employeeDetails) ? $employeeDetails : [] ) , ( isset($selectedUserId) ? $selectedUserId : 0 ) ); ?> 
			                    </div>
							
								<div class="col-xl-2 col-md-3 col-sm-6">
									<div class="form-group">
										<label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
										<select class="form-control select2" name="search_team" onchange="filterData()">
											<option value="">{{ trans("messages.select") }}</option>
											<?php 
											if(!empty($teamRecordDetails)){
												foreach ($teamRecordDetails as $teamRecordDetail){
													$encodeId = Wild_tiger::encode($teamRecordDetail->i_id);
													?>
													<option value="{{ $encodeId }}">{{ (!empty($teamRecordDetail->v_value) ? $teamRecordDetail->v_value :'')}}</option>
													<?php 
													
												}
											}
											?>
										</select>
									</div>
								</div>
						@endif
						
                        <div class="col-md-2">
                            <label class="control-label">{{ trans("messages.start-date") }}</label>
                            <div class="date">
                                <input type="text" class="form-control date mb-3" name="search_start_date" placeholder="DD-MM-YYYY" />
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="control-label">{{ trans("messages.end-date") }}</label>
                            <div class="date">
                                <input type="text" class="form-control date mb-3" name="search_end_date" placeholder="DD-MM-YYYY" />
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap">
                            <a class="btn btn-theme text-white mb-3" href="javascript:void(0)" onclick="filterData()" title="{{ trans('messages.search') }}">{{ trans("messages.search") }}</a>
                            <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card card-body">
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-hover table-bordered table-sm">
                        <thead>
                            <tr class="text-center">
                                <th class="sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left">{{ trans("messages.employee-name-code") }}</th>
                                <th class="text-left">{{ trans("messages.team") }}</th>
                                <th class="text-left">{{ trans("messages.login-date") }}</th>
                                <th class="text-left">{{ trans("messages.ip-address") }}</th>
                            </tr>
                        </thead>
                        <tbody class="ajax-view">
                            @include( config('constants.AJAX_VIEW_FOLDER') . 'login-history/login-history-list')
                        </tbody>
                    </table>
                </div>
        </div>

    </div>
</main>

<script>
    var login_history_url = '{{ config("constants.LOGIN_HISTORY_URL") }}' + '/';

    $(document).ready(function() {

        //init date time picker
        $('[name="search_start_date"], [name="search_end_date"]').datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
            showClear: true,
            showClose: true,
            widgetPositioning: {
                vertical: 'bottom',
                horizontal: 'auto'

            },
            icons: {
                clear: 'fa fa-trash',
                Close: 'fa fa-trash',
            },
        });

    });

    function searchField() {
        var search_employee_status = $.trim($("[name='search_employment_status']").val());
        var search_employee = $.trim($("[name='search_employee']").val());
        var search_team = $.trim($("[name='search_team']").val());
        var search_start_date = $.trim($("[name='search_start_date']").val());
        var search_end_date = $.trim($("[name='search_end_date']").val());

        var searchData = {
            'search_employee_status': search_employee_status,
            'search_employee': search_employee,
            'search_team': search_team,
            'search_start_date': search_start_date,
            'search_end_date': search_end_date
        }

        return searchData;
    }


    //filter login history listing
    function filterData() {

        var searchFieldName = searchField();

        searchAjax(login_history_url + 'filter', searchFieldName);

    }

    //daepicker management
    $(function() {
    	$("[name='search_start_date']").datetimepicker().on('dp.change', function(e) {
    		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    			var incrementDay = moment((e.date)).startOf('d');
    		 	$("[name='search_end_date']").data('DateTimePicker').minDate(incrementDay);
    		} else {
    			$("[name='search_end_date']").data('DateTimePicker').minDate(false);
    		} 
    		
    	    $(this).data("DateTimePicker").hide();
    	});

        $("[name='search_end_date']").datetimepicker().on('dp.change', function(e) {
        	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    	        var decrementDay = moment((e.date)).endOf('d');
    	        $("[name='search_start_date']").data('DateTimePicker').maxDate(decrementDay);
        	} else {
        		 $("[name='search_start_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });
    });
    var paginationUrl  = login_history_url + 'filter';
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>

<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection