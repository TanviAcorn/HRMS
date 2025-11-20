<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- generic meta -->
    <meta name="description" content="{{ config('constants.SITE_DESCRIPTION') }}" />
    <meta name="keywords" content="{{ config('constants.SITE_KEYWORDS') }}" />
    <meta name="author" content="TWT" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- og meta -->
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ config('constants.SITE_TITLE') }}" />
    <meta property="og:description" content="{{ config('constants.SITE_DESCRIPTION') }}" />
    <meta property="og:url" content="{!! url('/') !!}" />
    <meta property="og:site_name" content="{{ config('constants.SITE_TITLE') }}" />
    <meta property="og:image" content="{{ asset('images/icon.png') }}" />
    <meta property="og:image:width" content="200" />
    <meta property="og:image:height" content="200" />
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{!! url('/') !!}">
    <meta property="twitter:title" content="{{ config('constants.SITE_TITLE')}}">
    <meta property="twitter:description" content="{{ config('constants.SITE_DESCRIPTION') }}">
    <meta property="twitter:image" content="{{ asset('images/logo.png') }}">
    <!-- theme-color: for chrome mobile -->
    <meta name="theme-color" content="#0063c1">
    <!-- favicon -->
    <link rel="icon" href="{{ asset('images/icon.png') }}">
    <!-- preconnect -->
    <link href='https://fonts.gstatic.com' crossorigin='anonymous' rel='preconnect' />
    <!-- page title -->
    <title>@yield('pageTitle') | {{ config('constants.SITE_TITLE', 'HR Management system ')}} </title>
    <!-- css -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset ('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/alertify.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/alertify.bs.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/slick.theme.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/cropper.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/mdtimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/mdtimepicker-theme.css') }}">
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
    <!-- main css -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset ('css/main.css') . '?ver=' .config('constants.CSS_JS_VERSION') }}">
    <link rel="stylesheet" href="{{ asset ('css/style.css') . '?ver=' .config('constants.CSS_JS_VERSION') }}">

    <!-- scripts -->
    <script src="{{ asset ('js/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset ('js/moment.min.js') }}"></script>
    <script src="{{ asset ('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset ('js/select2.js') }}"></script>
    <script src="{{ asset ('js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset ('js/chart.js') }}"></script>
    <!-- Fallback to CDN if local Chart.js fails -->
    <script>
        if (typeof Chart === 'undefined') {
            console.warn('Local Chart.js not loaded, loading from CDN...');
            document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"><\/script>');
        }
    </script>
    <script type="text/javascript" src="{{ asset ('js/fullcalendar.min.js') }}"></script>

    <!-- plugins -->
    <script type="text/javascript" src="{{ asset ('js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset ('js/validator-additional-methods.js') }}"></script>
    <script type="text/javascript" src="{{ asset ('js/alertify.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset ('js/slick.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset ('js/cropper.js') }}"></script>
    <script type="text/javascript" src="{{ asset ('js/mdtimepicker.min.js') }}"></script>
    

    <!-- main script -->
	<style>
	.calendar-style .leave-calendar-type .approved-leave-color {
		color : {{ config('constants.APPROVED_LEAVE_COLOR_CODE') }};
	}
	.calendar-style .leave-calendar-type .approved-half-leave-color {
		color : {{ config('constants.APPROVED_HALF_LEAVE_COLOR_CODE') }};
	}
	.calendar-style .leave-calendar-type .unpaid-half-leave-color {
		color : {{ config('constants.UNPAID_HALF_LEAVE_COLOR_CODE') }};
	}
	</style>
    <script>
        var site_url = "{{ url('/') }}" + '/';
        var current_selected_row = '';
        var calendar = '';
        var cropper  = '';
        var round_off_value_decimal = 0;
        var pagination_view_html = 'ajax-view';
        var chart_colors = ["#22976d", "#1c4d9c", "#5e416d" ,"#b8c02d"," #ffeaab","#ffa4a4","#d3ebc4","#ffebb2","#ffd0b3","#ffbdb5","#ffb5c9","#d1a4bf","#b2e1d7","#d6a6dd"];
        var month_wise_chart_colors = ["#0a79be", "#07a0e3", "#22976d", "#75b052", "#b8c02d", "#fbc62a", "#f08340", "#e75e4e", "#e4416c", "#b54b8b", "#724e8c", "#1c4d9c"];
        //console.log(site_url)
    </script>
    <script src="{{ asset ('js/messages.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
    <script src="{{ asset ('js/common.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
    <?php
    $device = detectDevice();
    $desktopClass  = '';
    if ($device == 'desktop') {
        $desktopClass  = 'desktop-remove-anchor';
    }

    ?>
</head>
<?php 
$isSuprevisor = false;
if (session()->has('is_supervisor') && session()->has('is_supervisor') != false){
	$isSuprevisor = true;
}
?>
	
<body class="">
    <!-- navbar start -->

    <div id="wrapper" class="wrapper">
        <header class="d-print-none main-header">
            <nav class="navbar navbar-dark">
                <button class="navbar-toggler ripple" type="button" accesskey="m">
                    <span class="navbar-toggler-icon">
                    </span>
                    <!-- <span class="navbar-toggler-icon"></span> -->
                </button>
                @if( ( session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN') ) )
                <h4 class="super-admin-text">{{ session()->has('name') ? session()->get('name') : trans('messages.admin') }}</h4>
                @endif
                <a class="navbar-brand mr-auto d-none d-md-block" href="">
                </a>
                <div class="dropdown ml-auto admin-dropdown mr-lg-3 mr-3">
                    <a class="dropdown-toggle d-inline-block" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="img-user align-middle mr-2 rounded-circle d-blcok"><i class="fa fa-user mr-2" aria-hidden="true"></i><span id="username" class="d-inline-block align-middle login-user-name">{{ ( session()->has('name') ? session()->get('name') : trans("messages.admin") ) }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ url('change-password') }}">{{ trans("messages.change-password") }}</a>
                    </div>
                </div>
                <div class="logout logout-btn-items">
                    <a href="{{ url('logout') }}" class="logout-btn text-white text-decoration-none font-15 d-flex align-items-center"><i class="fas fa-power-off mr-2"></i> <span class="d-sm-block d-none">{{ trans("messages.logout") }}</span> </a>
                </div>
            </nav>
            <div class="sidebar" id="sidebar">
                <ul class="sidebar-nav nav-scroll">
                    <li class="px-3 d-flex align-items-center justify-content-center nav-users mb-1">
                        <!-- <img src="{{ asset ('images/logo.png') }}" alt="" srcset="" class="img-fluid big-image w-75"> -->
                        <img src="{{ asset ('images/icon.png') }}" alt="" srcset="" class="img-fluid small-image">
                    </li>
                    
                    <li class="nav-items-class">
                        <a href="{{ config('constants.DASHBORD_MASTER_URL') }}" class="nav-link first-menu" title="{{ trans('messages.dashboard') }}">
                            <i class="fa fa-tachometer-alt fa-fw"></i>
                            <span class="nav-text">{{ trans("messages.dashboard") }}</span>
                        </a>
                    </li>
					@php $uid = (int)(session()->get('user_id')); $isAdmin = session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN'); @endphp
                    @if( (session()->has('user_id') && in_array($uid, [751, 323])) || $isAdmin )
                    <li class="nav-items-class">
                        <a href="{{ (in_array($uid, [751,323])) ? url('hr-letters/inbox') : url('hr-letters') }}" class="nav-link first-menu" title="HR Letters">
                            <i class="fa fa-file-contract fa-fw"></i>
                            <span class="nav-text">HR Letters</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-items-class">
                        @inject('crudModel', 'App\Models\Notification')
                        <?php
                        $notificationCount = count($crudModel->getRecordDetails(['read_status' => 0]));
                        ?>
                        <a href="{{ config('constants.NOTIFICATION_URL') }}" class="nav-link first-menu" title="{{ trans('messages.notifications') }}">
                            <i class="fa fa-regural fa-bell fa-fw position-relative">
                            	@if( $notificationCount > 0 )
                            	<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            		<?php echo ( $notificationCount > config('constants.NOTIFICATION_DISPLAY_COUNT') ? config('constants.NOTIFICATION_DISPLAY_COUNT') . '+' : ( $notificationCount > 0 ? $notificationCount : 0 ) )   ?>
                                </span>
                                @endif
                            </i>
                            <span class="nav-text">{{ trans("messages.notifications") }}</span>
                        </a>
                    </li>
                    
					@if( ( session()->get('role') == config('constants.ROLE_USER') ) )
                    <li class="nav-items nav-items-class dropdown">
                        <a class="nav-link main-drodown-toggle dropdown-toggle first-menu collapsed <?php echo $desktopClass ?>" title="{{ trans('messages.me') }}" href="#manageMenu1" data-toggle="collapse"><i class="fa fa-user fa-fw" aria-hidden="true"></i><span class="nav-text">{{ trans('messages.me') }} </span></a>
                        <ul class="collapse navbar-collapse" id="manageMenu1">
                            <!-- <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.my-attendance') }}" href="{{ config('constants.EMPLOYEE_ATTENDANCE_MASTER_URL') }}">{{ trans('messages.my-attendance') }}</a>
                            </li>
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.my-leaves') }}" href="{{ config('constants.MY_LEAVES_MASTER_URL') }}">{{ trans('messages.my-leaves') }}</a>
                            </li>
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.my-time-offs') }}" href="{{ config('constants.TIME_OFF_MASTER_URL') }}">{{ trans('messages.my-time-offs') }}</a>
                            </li> 
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.my-salary') }}" href="{{ config('constants.SALARY_MASTER_URL') }}">{{ trans('messages.my-salary') }}</a>
                            </li> -->
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.my-documents') }}" href="{{ config('constants.MY_DOCUMENT_MASTER_URL') }}" >{{ trans('messages.my-documents') }}</a>
                            </li>
                           <!-- <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.my-pay-slip') }}" href="{{ config('constants.MY_PAYSLIP_URL') }}">{{ trans('messages.my-pay-slip') }}</a>
                            </li> -->
                        </ul>
                    </li>

                   
                    
                    @endif
                    <?php /* ?>
                    @if( $isSuprevisor != false && ( session()->get('role') == config('constants.ROLE_USER') ) )
                    <li class="nav-items-class">
                        <a href="{{ config('constants.EMPLOYEE_MASTER_URL') }}" class="nav-link first-menu" title="{{ trans('messages.employee') }}">
                            <i class="fa fa-id-card fa-fw"></i>
                            <span class="nav-text">{{ trans("messages.employee") }}</span>
                        </a>
                    </li>
                    @endif
                    <?php */ ?>
                    @if( session()->has('user_employee_id') && ( session()->get('user_employee_id') > 0  ) )
                    <li class="nav-items-class">
                        <a href="{{ config('constants.MY_PROFILE_URL') }}" class="nav-link first-menu" title="{{ trans('messages.my-profile') }}">
                            <i class="fa fa-id-card fa-fw"></i>
                            <span class="nav-text">{{ trans("messages.my-profile") }}</span>
                        </a>
                    </li>
                    @endif
                    @if(  ( $isSuprevisor != false ) || (checkPermission('view_employee_summary') != false) || (checkPermission('view_employee_list') != false) || (checkPermission('view_manage_attendance') != false) || (checkPermission('view_attendance_summary') != false) || (checkPermission('view_attendance_report') != false) || (checkPermission('view_attendance_report_day_wise') != false) || (checkPermission('view_punch_report') != false) || (checkPermission('view_leave_summary') != false) || (checkPermission('view_leave_report') != false) || (checkPermission('view_time_off_summary') != false) || (checkPermission('view_time_off_report') != false) || (checkPermission('view_salary_summary') != false) || (checkPermission('view_salary_report') != false) || (checkPermission('view_on_hold_salary_report') != false) || (checkPermission('view_salary_increment_report') != false) || (checkPermission('view_salary_calculation') != false) || (checkPermission('view_documents_report') != false))
                    <li class="nav-items nav-items-class dropdown">
                        <a class="nav-link main-drodown-toggle dropdown-toggle first-menu collapsed <?php echo $desktopClass ?>" href="#manageMenu2" title="{{ trans('messages.employees') }}" data-toggle="collapse"><i class="fa fa-users fa-fw" aria-hidden="true"></i><span class="nav-text">{{ trans("messages.employees") }}</span></a>
                        <ul class="collapse navbar-collapse sub-dropdown-collapse" id="manageMenu2">
                        	@if((checkPermission('view_employee_summary') != false) || (checkPermission('view_employee_list') != false) || ( $isSuprevisor != false ) )
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed <?php echo $desktopClass ?>" href="#collapseOne" data-toggle="collapse"> <span class="nav-text"><i class="fa fa-angle-right mr-2"></i>{{ trans("messages.employees") }}</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseOne">
                                	@if( ( checkPermission('view_employee_summary') != false  ))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item  main-drodown-toggle third-menu collapsed" title="{{ trans("messages.summary") }}" href="{{ config('constants.EMPLOYEE_SUMMARY_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans("messages.summary") }}</span></a>
                                    </li>
                                    @endif
                                    @if( ( checkPermission('view_employee_list') != false )  || ( $isSuprevisor != false )  )
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item  main-drodown-toggle third-menu collapsed" title="{{ trans("messages.employee-list") }}" href="{{ config('constants.EMPLOYEE_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans("messages.employee-list") }}</span></a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                            @endif
                            @if((checkPermission('view_manage_attendance') != false) || (checkPermission('view_attendance_summary') != false) || (checkPermission('view_attendance_report') != false) || (checkPermission('view_attendance_report_day_wise') != false) || (checkPermission('view_punch_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed <?php echo $desktopClass ?>" data-toggle="collapse" href="#collapseOne1"> <span class="nav-text"><i class="fa fa-angle-right mr-2"></i>{{ trans('messages.attendance') }}</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseOne1">
                                	@if(checkPermission('view_manage_attendance') != false)
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.manage-attendance-manually') }}" href="{{ config('constants.SITE_URL') . 'edit-attendance' }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.manage-attendance-manually') }}</span></a>
                                    </li>
                                    @endif
                                    @if(checkPermission('view_attendance_summary') != false)
                                    <!-- <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.present-summary') }}" href="{{ config('constants.SITE_URL') . 'attendance-summary' }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.present-summary') }}</span></a>
                                    </li> -->
                                    @endif
                                    @if(checkPermission('view_attendance_report') != false)
                                    <!-- <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.attendance-report-daily-monthly') }}" href="{{  config('constants.SITE_URL') . 'attendance-report' }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.attendance-report-daily-monthly') }}</span></a>
                                    </li> -->
                                    @endif
                                    @if(checkPermission('view_attendance_report_day_wise') != false)
                                    <!-- <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.hr-attendance-day-wise') }}" href="{{ config('constants.SITE_URL') . 'attendance-report-day-wise' }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.hr-attendance-day-wise') }}</span></a>
                                    </li>-->
                                    @endif
                                    @if(checkPermission('view_punch_report') != false)
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.punch-report-live') }}" href="{{ config('constants.SITE_URL') . 'punch-report'  }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.punch-report-live') }}</span></a>
                                    </li>
                                    @endif
                                    <?php /* ?>
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" href="{{ config('constants.UPLOAD_DAILY_ATTENDANCE_SUMMARY_URL') }}" title="{{ trans('messages.uploaded-attendance-summary') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.uploaded-attendance-summary') }}</span></a>
                                    </li>
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" href="{{ config('constants.UPLOAD_DAILY_ATTENDANCE_URL') }}" title="{{ trans('messages.uploaded-attendance-data') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.uploaded-attendance-data') }}</span></a>
                                    </li>
                                    <?php */ ?>
                                </ul>
                            </li>
                            @endif
                           <!-- @if((checkPermission('view_leave_summary') != false) || (checkPermission('view_leave_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed <?php echo $desktopClass ?>" data-toggle="collapse" href="#collapseOne2"> <span class="nav-text"><i class="fa fa-angle-right mr-2"></i>{{ trans('messages.leaves') }}</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseOne2">
                                	@if(checkPermission('view_leave_summary') != false)
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.leave-detailed-summary') }}" href="{{ config('constants.LEAVE_SUMMARY_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.leave-detailed-summary') }}</span></a>
                                    </li>
                                    @endif
                                    @if(checkPermission('view_leave_report') != false)
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.leave-report') }}" href="{{ config('constants.LEAVE_REPORT_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.leave-report') }}</span></a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                            @endif
                            @if((checkPermission('view_time_off_summary') != false) || (checkPermission('view_time_off_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed <?php echo $desktopClass ?>" data-toggle="collapse" href="#collapseOne3"> <span class="nav-text"><i class="fa fa-angle-right mr-2"></i>{{ trans('messages.time-off') }}</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseOne3">
                                	@if(checkPermission('view_time_off_summary') != false)
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.time-off-detailed-summary') }}" href="{{ config('constants.TIME_OFF_SUMMARY_REPORT_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.time-off-detailed-summary') }}</span></a>
                                    </li>
                                    @endif
                                    @if(checkPermission('view_time_off_report') != false)
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.time-off-report') }}" href="{{ config('constants.TIME_OFF_REPORT_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.time-off-report') }}</span></a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                            @endif
                            @if((checkPermission('view_salary_summary') != false) || (checkPermission('view_salary_report') != false) || (checkPermission('view_on_hold_salary_report') != false) || (checkPermission('view_salary_increment_report') != false) || (checkPermission('view_salary_calculation') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed <?php echo $desktopClass ?>" data-toggle="collapse" href="#collapseOne4"> <span class="nav-text"><i class="fa fa-angle-right mr-2"></i>{{ trans('messages.salary') }}</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseOne4">
                                    @if(checkPermission('view_salary_summary') != false)
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.summary') }}" href="{{ config('constants.SITE_URL') . 'salary-summary' }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.summary') }}</span></a>
                                    </li>
                                    @endif
                                    @if(checkPermission('view_salary_report') != false)
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.pay-slips-report') }}" href="{{ config('constants.SITE_URL') . 'salary-report' }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.pay-slips-report') }}</span></a>
                                    </li>
                                    @endif
                                    @if(checkPermission('view_on_hold_salary_report') != false)
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.on-hold-salary-report') }}" href="{{ config('constants.SITE_URL') . 'on-hold-salary-report' }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.on-hold-salary-report') }}</span></a>
                                    </li>
                                    @endif
                                    @if(checkPermission('view_salary_increment_report') != false)
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.salary-increment-report') }}" href="{{ config('constants.SITE_URL') . 'salary-increment-report' }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.salary-increment-report') }}</span></a>
                                    </li>
                                    @endif
                                    @if(checkPermission('view_salary_calculation') != false)
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.salary-calculation') }}" href="{{ config('constants.SITE_URL') . 'calculate-salary' }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.salary-calculation') }}</span></a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                            @endif -->
                            @if((checkPermission('view_documents_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed <?php echo $desktopClass ?>" data-toggle="collapse" href="#collapseOne5"> <span class="nav-text"><i class="fa fa-angle-right mr-2"></i>{{ trans('messages.documents') }}</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseOne5">
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.document-report') }}" href="{{ config('constants.DOCUMENT_REPORT_URL') }} "><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.document-report') }}</span></a>
                                    </li>
                                </ul>
                            </li>
							@endif
                            <!-- <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed" href="#collapseOne1"> <span class="nav-text"><i class="fa fa-angle-right mr-2"></i>Incident</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseOne1">
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" href="{{ url('design-incident-report') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>Incident Report</span></a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed" href="{{ url('design-manage-documents') }}"><span class="nav-text"><i class="fa fa-angle-right mr-2"></i>Manage Documents</span></a>
                            </li> -->
                        </ul>
                    </li>
                    
                    @endif
                    <?php
                    // Show Probation Assessment tab for Admins and permission group 8
                    $showProbationAssessment = false;
                    try {
                        if (session()->has('user_id')) {
                            $empPerm2 = \App\EmployeeModel::select('i_id','i_role_permission')
                                ->where('i_login_id', session()->get('user_id'))
                                ->first();
                            $isAdmin2 = ( session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN') );
                            if ($isAdmin2 || ($empPerm2 && (int)$empPerm2->i_role_permission === 4)) {
                                $showProbationAssessment = true;
                            }
                        }
                    } catch (\Exception $e) {}
                    ?>
                    @if($showProbationAssessment)
                    <li class="nav-items-class">
                        <a href="{{ url('probation-assessments') }}" class="nav-link first-menu" title="Probation Assessment">
                            <i class="fa fa-user-check fa-fw"></i>
                            <span class="nav-text">Probation Assessment</span>
                        </a>
                    </li>
                    @endif
                    <?php 
                    // Show Performance Appraisal tab if role permission = 8 OR admin role
                    $showPerformanceAppraisal = false;
                    try {
                        if (session()->has('user_id')) {
                            $empPerm = \App\EmployeeModel::select('i_id','i_role_permission')
                                ->where('i_login_id', session()->get('user_id'))
                                ->first();
                            $isAdmin = ( session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN') );
                            if ($isAdmin || ($empPerm && ( (int)$empPerm->i_role_permission === 4 )) ) {
                                $showPerformanceAppraisal = true;
                            }
                        }
                    } catch (\Exception $e) {}
                    ?>
                    @if($showPerformanceAppraisal)
                    <li class="nav-items-class">
                        <a href="{{ url('performance-appraisals') }}" class="nav-link first-menu" title="Performance Appraisal">
                            <i class="fa fa-star-half-alt fa-fw"></i>
                            <span class="nav-text">Performance Appraisal</span>
                        </a>
                    </li>
                    @endif
                    <?php
                    // Feedback Forms is now under Reports menu
                    $showFeedbackForms = ( session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN') );
                    ?>
                    @if((checkPermission('view_shifts') != false) || (checkPermission('view_weekly_offs') != false) || (checkPermission('view_incident_summary') != false) || (checkPermission('view_incident_report') != false))
                    <li class="nav-items nav-items-class dropdown">
                        <a class="nav-link main-drodown-toggle dropdown-toggle first-menu collapsed <?php echo $desktopClass ?>" title="{{ trans('messages.company') }}" href="#manageMenu3" data-toggle="collapse"><i class="fa fa-building fa-fw" aria-hidden="true"></i><span class="nav-text">{{ trans('messages.company') }} </span></a>
                        <ul class="collapse navbar-collapse" id="manageMenu3">
                        	@if((checkPermission('view_shifts') != false) || (checkPermission('view_weekly_offs') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed <?php echo $desktopClass ?>" href="#collapseOne" data-toggle="collapse"> <span class="nav-text "><i class="fa fa-angle-right mr-2"></i>{{ trans('messages.shift-and-weekoffs') }}</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseOne">
                            		@if((checkPermission('view_shifts') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item  main-drodown-toggle third-menu collapsed" title="{{ trans('messages.shifts') }}" href="{{ config('constants.SHIFT_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.shifts') }}</span></a>
                                    </li>
                                    @endif
                            		@if((checkPermission('view_weekly_offs') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item  main-drodown-toggle third-menu collapsed" title="{{ trans('messages.weekly-offs') }}" href="{{ config('constants.WEEKLY_OFF_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.weekly-offs') }}</span></a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                            @endif
                            @if((checkPermission('view_incident_summary') != false) || (checkPermission('view_incident_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed <?php echo $desktopClass ?>" data-toggle="collapse" href="#collapseTwo"> <span class="nav-text"><i class="fa fa-angle-right mr-2"></i>{{ trans('messages.incidents') }}</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseTwo">
                                	@if((checkPermission('view_incident_summary') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.summary') }}" href="{{ config('constants.INCIDENT_SUMMARY_URL')}}"><span class="nav-text"> <i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.summary') }}</span></a>
                                    </li>
                                    @endif
                            		@if((checkPermission('view_incident_report') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.incident-report') }}" href="{{ config('constants.INCIDENT_REPORT_URL') }}"><span class="nav-text"> <i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.incident-report') }}</span></a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                            @endif
                            @if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) )
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" href="{{ config('constants.ROLES_AND_PERMISSION_MASTER_URL') }}">{{ trans('messages.roles-permissions') }}</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if((checkPermission('view_employee_report') != false) || (checkPermission('view_employee_duration_report') != false) || (checkPermission('view_salary_report') != false) || (checkPermission('view_on_hold_salary_report') != false) || (checkPermission('view_salary_increment_report') != false) || (checkPermission('view_salary_report_for_account_team') != false) || (checkPermission('view_documents_report') != false) || (checkPermission('view_leave_report') != false) || (checkPermission('view_leave_report_month_wise_count') != false) || (checkPermission('view_form_16_report') != false) || (checkPermission('view_time_off_report') != false) || (checkPermission('view_attendance_report') != false) || (checkPermission('view_attendance_report_day_wise') != false) || (checkPermission('view_statutory_bonus_report') != false) || (checkPermission('view_punch_report') != false) || (checkPermission('view_resignation_report') != false) || (checkPermission('view_missing_punch_report') != false))
                    <li class="nav-items nav-items-class dropdown demo">
                        <a class="nav-link main-drodown-toggle dropdown-toggle first-menu collapsed <?php echo $desktopClass ?>" title="{{ trans('messages.reports') }}" href="#manageMenu4" data-toggle="collapse"><i class="fa fa-file-alt fa-fw" aria-hidden="true"></i><span class="nav-text">{{ trans('messages.reports') }} </span></a>
                        <ul class="collapse navbar-collapse reports-submenu" id="manageMenu4">
                            @if((checkPermission('view_employee_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.master-sheet') }}
                                " href="{{ config('constants.EMPLOYEE_REPORT_URL') }}">{{ trans('messages.master-sheet') }}</a>
                            </li>
                            @endif
                            @if((checkPermission('view_employee_duration_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.employee-duration-report') }}" href="{{ config('constants.EMPLOYEE_DURATION_REPORT_URL') }}">{{ trans('messages.employee-duration-report') }}</a>
                            </li>
                            @endif
                            @if((checkPermission('view_salary_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.pay-slips-report') }}" href="{{ config('constants.SITE_URL') . 'salary-report' }}">{{ trans('messages.pay-slips-report') }}</a>
                            </li>
                            @endif
                            @if((checkPermission('view_on_hold_salary_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.on-hold-salary-report') }}" href="{{ config('constants.SITE_URL') . 'on-hold-salary-report' }}">{{ trans('messages.on-hold-salary-report') }}</a>
                            </li>
                            @endif
                            @if((checkPermission('view_salary_increment_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.salary-increment-report') }}" href="{{ config('constants.SITE_URL') . 'salary-increment-report' }}">{{ trans('messages.salary-increment-report') }}</a>
                            </li>
                            @endif
                            @if((checkPermission('view_salary_report_for_account_team') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.salary-report-for-account-team') }}" href="{{ config('constants.SITE_URL') . 'salary-report-for-account-team' }}">{{ trans('messages.salary-report-for-account-team') }}</a>
                            </li>
                            @endif
                            @if((checkPermission('view_documents_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.document-report') }}" href="{{ config('constants.DOCUMENT_REPORT_URL') }}">{{ trans('messages.document-report') }}</a>
                            </li>
                            @endif
                            @if((checkPermission('view_leave_report') != false))
                            <!--<li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.leave-report') }}" href="{{ config('app.url') . 'leave-report-summary' }}">{{ trans('messages.leave-report') }}</a>
                            </li>
                            @endif
                            @if((checkPermission('view_leave_report_month_wise_count') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" href="{{ config('constants.LEAVE_REPORT_MONTH_WISE_COUNT_URL') }}" title="{{ trans('messages.leave-report-month-wise-count') }}">{{ trans('messages.leave-report-month-wise-count') }}</a>
                            </li>-->
                           @endif
                           
                            @if($showFeedbackForms)
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="1 Month Feedback" href="{{ url("feedback-forms") }}">
                                    <span class="nav-text">1 Month Feedback</span>
                                </a>
                            </li>
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="6 Month Feedback" href="{{ url("feedback-forms-six") }}">
                                    <span class="nav-text">6 Month Feedback</span>
                                </a>
                            </li>
                            @endif
                            @if((checkPermission('view_form_16_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" href="{{ config('constants.FORM_16_REPORT_URL') }}" title="{{ trans('messages.form-16-report') }}">{{ trans('messages.form-16-report') }}</a>
                            </li>
                            @endif
                             <?php /* ?>
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" href="{{ config('constants.STATUTORY_BONUS_REPORT_URL') }}" title="{{ trans('messages.statutory-bonus-report') }}">{{ trans('messages.statutory-bonus-report') }}</a>
                            </li>
                            <?php */ ?>
                            <!--@if((checkPermission('view_time_off_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.time-off-report') }}" href="{{ config('app.url') . 'time-off-report-summary' }}">{{ trans('messages.time-off-report') }}</a>
                            </li>-->
                            @endif
                            @if((checkPermission('view_attendance_report') != false))
                            <!-- <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" href="{{ config('constants.SITE_URL') . 'attendance-report' }}" title="{{ trans('messages.attendance-report-daily-monthly') }}" >{{ trans('messages.attendance-report-daily-monthly') }}</a>
                            </li>-->
                            @endif
                            @if((checkPermission('view_attendance_report_day_wise') != false))
                            <!--<li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" href="{{ config('constants.SITE_URL') . 'attendance-report-day-wise' }}" title="{{ trans('messages.hr-attendance-day-wise') }}" >{{ trans('messages.hr-attendance-day-wise') }}</a>
                            </li>-->
                            @endif
                            @if((checkPermission('view_statutory_bonus_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.statutaroy-bonus-report') }}" href="{{ config('constants.SITE_URL') . 'statutory-bonus-report' }}">{{ trans('messages.statutaroy-bonus-report') }}</a>
                            </li>
                            @endif
                            @if((checkPermission('view_punch_report') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.punch-report-live') }}" href="{{ config('constants.SITE_URL') . 'punch-report'   }}">{{ trans('messages.punch-report-live') }}</a>
                            </li>
                            @endif
                            @if( ( ( checkPermission('view_resignation_report') != false) ) )
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.resignation-report') }}" href="{{ config('constants.REGIGNATION_REPORT_URL') }}">{{ trans('messages.resignation-report') }}</a>
                            </li>
                            @endif
                            @if((checkPermission('view_missing_punch_report') != false))
                            <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                            	<a class="nav-link dropdown-item main-drodown-toggle collapsed" title="{{ trans('messages.missing-punch') }}" href="{{ config('constants.SITE_URL') . 'missing-punch'  }}">{{ trans('messages.missing-punch') }}</a>
                            </li>
                            @endif
                        </ul>
                    @endif
                    @if((checkPermission('view_team_master') != false) || (checkPermission('view_designation_master') != false) || (checkPermission('view_recruitment_source_master') != false) || (checkPermission('view_holiday_master') != false) || (checkPermission('view_probation_policy_master') != false) || (checkPermission('view_notice_period_policy_master') != false) || (checkPermission('view_termination_reasons') != false) || (checkPermission('view_resign_reasons') != false) || (checkPermission('view_document_folder') != false) || (checkPermission('view_document_type') != false) || (checkPermission('view_salary_components') != false) || (checkPermission('view_salary_groups') != false) || (checkPermission('view_bank_master') != false) || (checkPermission('view_state') != false) || (checkPermission('view_city') != false) || (checkPermission('view_village') != false))
                    <li class="nav-items nav-items-class dropdown demo">
                        <a class="nav-link main-drodown-toggle dropdown-toggle first-menu collapsed <?php echo $desktopClass ?>" title="{{ trans('messages.masters') }}" href="#mastermenu" data-toggle="collapse"><i class="fa fa-users-cog fa-fw" aria-hidden="true"></i><span class="nav-text">{{ trans('messages.masters') }} </span></a>
                        <ul class="collapse navbar-collapse" id="mastermenu">
							@if((checkPermission('view_team_master') != false) || (checkPermission('view_designation_master') != false) || (checkPermission('view_recruitment_source_master') != false) || (checkPermission('view_holiday_master') != false) || (checkPermission('view_probation_policy_master') != false) || (checkPermission('view_notice_period_policy_master') != false) || (checkPermission('view_termination_reasons') != false) || (checkPermission('view_resign_reasons') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items demo">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed <?php echo $desktopClass ?>" data-toggle="collapse" href="#collapseOne"> <span class="nav-text"><i class="fa fa-angle-right mr-2"></i>{{ trans('messages.company-master') }}</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseOne">
                                	@if((checkPermission('view_team_master') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.team-master') }}" href="{{ config('constants.TEAM_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.team-master') }}</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_designation_master') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.designation-master') }}" href="{{ config('constants.DESIGNATION_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.designation-master') }}</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_designation_master') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="Sub Designation Master" href="{{ config('constants.SUB_DESIGNATION_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>Sub Designation Master</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_recruitment_source_master') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.recruitment-source-master') }}" href="{{ config('constants.RECRUITMENT_SOURCE_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.recruitment-source-master') }}</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_holiday_master') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.holiday-master') }}" href="{{ config('constants.HOLIDAY_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.holiday-master') }}</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_probation_policy_master') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.probation-policy-master') }}" href="{{ config('constants.PROBATION_POLICY_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.probation-policy-master') }}</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_notice_period_policy_master') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.notice-period-policy-master') }}" href="{{ config('constants.NOTICE_PERIOD_POLICY_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.notice-period-policy-master') }}</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_termination_reasons') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.termination-reasons') }}" href="{{ config('constants.TERMINATION_REASONS_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.termination-reasons') }}</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_resign_reasons') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.resign-reasons') }}" href="{{ config('constants.RESIGN_REASONS_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.resign-reasons') }}</span></a>
                                    </li>
                                    @endif
                                    <?php /* ?>
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" href="javascript:void(0);"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.leave-type-master') }}</span></a>
                                    </li>
                                    <?php */ ?>
                                </ul>
                            </li>
                            @endif
							@if((checkPermission('view_document_folder') != false) || (checkPermission('view_document_type') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed <?php echo $desktopClass ?>" href="#collapseOne2" data-toggle="collapse"> <span class="nav-text "><i class="fa fa-angle-right mr-2"></i>{{ trans('messages.document-master') }}</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseOne2">
                                	@if((checkPermission('view_document_folder') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item  main-drodown-toggle third-menu collapsed" title="{{ trans('messages.document-folder') }}" href="{{ config('constants.DOCUMENT_FOLDER_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.document-folder') }}</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_document_type') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item  main-drodown-toggle third-menu collapsed" title="{{ trans('messages.document-type') }}" href="{{ config('constants.DOCUMENT_TYPE_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.document-type') }}</span></a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                            @endif
                            @if((checkPermission('view_salary_components') != false) || (checkPermission('view_salary_groups') != false) || (checkPermission('view_bank_master') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed <?php echo $desktopClass ?>" data-toggle="collapse" href="#collapseOne3"> <span class="nav-text"><i class="fa fa-angle-right mr-2"></i>{{ trans('messages.salary-master') }}</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseOne3">
                                	@if((checkPermission('view_salary_components') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.salary-components') }}" href="{{ config('constants.SALARY_COMPONENTS_MASTER_URL') }}"><span class="nav-text"> <i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.salary-components') }}</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_salary_groups') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">

                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.salary-groups') }}" href="{{ config('constants.SALARY_GROUPS_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.salary-groups') }}</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_bank_master') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.bank-master') }}" href="{{ config('constants.BANK_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.bank-master') }}</span></a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                            @endif
							@if((checkPermission('view_state') != false) || (checkPermission('view_city') != false) || (checkPermission('view_village') != false))
                            <li class="dropdown sub-dropdown-menu dropdown-sub-megamenu  nav-items">
                                <a class="nav-link dropdown-toggle  main-drodown-toggle second-menu collapsed <?php echo $desktopClass ?>" data-toggle="collapse" href="#collapseOne4"> <span class="nav-text"><i class="fa fa-angle-right mr-2"></i>{{ trans('messages.location-master') }}</span></a>
                                <ul class="collapse navbar-collapse sub-dropdown-collapse" id="collapseOne4">
                                	@if((checkPermission('view_state') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.state') }}" href="{{ config('constants.STATE_MASTER_URL') }}"><span class="nav-text"> <i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.state') }}</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_city') != false))
                                    <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.city') }}" href="{{ config('constants.CITY_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.city') }}</span></a>
                                    </li>
                                    @endif
                                    @if((checkPermission('view_village') != false))
                                     <li class="dropdown-item  dropdown nav-items dropdown-sub-megamenu third-dropdown-menu">
                                        <a class="nav-link dropdown-item main-drodown-toggle third-menu collapsed" title="{{ trans('messages.village') }}" href="{{ config('constants.VILLAGE_MASTER_URL') }}"><span class="nav-text"><i class="fa fa-dot-circle mr-2"></i>{{ trans('messages.village') }}</span></a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) )
	                     <li class="nav-items-class">
	                        <a href="{{ config('constants.SETTING_URL') }}" class="nav-link  first-menu" title="{{ trans('messages.settings') }}">
	                            <i class="fa fa-cog fa-fw"></i>
	                            <span class="nav-text">{{ trans("messages.settings") }}</span>
	                        </a>
	                    </li>
	                @endif
	                @if(session()->has('role'))
                    <li class="nav-items-class">
                        <a href="{{ url('login-history') }}" class="nav-link  first-menu" title="{{ trans('messages.login-history') }}">
                            <i class="fa fa-hourglass-half fa-fw"></i>
                            <span class="nav-text">{{ trans("messages.login-history") }}</span>
                        </a>
                    </li>
                    @endif
                   
                </ul>

                <div class="fixed-footer border-top p-2">
                    <p class="small mb-0">&copy; <?php echo Date('Y') ?> <a href="https://www.thewildtigers.com/" target="_blank" class="text-theme">TWT</a></p>
                </div>
            </div>
        </header>

        @yield('content')
        <!-- nav end  -->
        @include( config("constants.ADMIN_FOLDER") . 'common-update-status-delete-script')
        @include( config("constants.ADMIN_FOLDER") . 'common-form-validation')
        @include( config("constants.ADMIN_FOLDER") . 'add-lookup-modal')
    </div>

    <script>
        function redirectPreviousPage() {
            //console.log('previous url')
            //console.log('{{ url()->previous() }}')
            //window.location.href = '{{ url()->previous() }}';
            window.history.back();
        }
    </script>
    <!-- Notification -->
    <script>
        // before script
        var detect_open_notification = false;
        $('.icon_wrap').on('click', function() {
            detect_open_notification = true;
            if ($(this).parent().hasClass('active')) {
                $(this).parent().removeClass('active');
            } else {
                $(this).parent().addClass('active');
            }
        });

        $('main').click(function() {
            $('.notifications').removeClass('active');
        });

        $('.main-navbar-wrapper').click(function(e) {
            if (detect_open_notification != true) {
                if ($('.notifications').hasClass('active') != false) {
                    $('.notifications').removeClass('active');
                }
            } else {
                detect_open_notification = false;
            }
        });

        $.validator.addMethod("validateUniqueEmail", function(value, element) {
            var result = true;
            ajaxResponse = $.ajax({
                type: "POST",
                async: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: site_url + 'checkUniqueUserEmail',
                dataType: "json",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'email': $.trim($("[name='email']").val()),
                    'record_id': ($.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null)
                },
                beforeSend: function() {
                    //block ui
                    //showLoader();
                },
                success: function(response) {
                    if (response.status_code == 1) {
                        return false;
                    } else {
                        result = false;
                        return true;
                    }
                }
            });
            return result;
        }, 'This Email is already in use. Please try another email.');
    </script>


    <script>
        var device_type = "<?php echo $device ?>";
        // alert('hello='+ device_type);
        //device_type = 'mobile';
        $(document).ready(function() {
            if (device_type == 'mobile') {
                $('.sidebar .main-drodown-toggle').on('click', function(e) {
                    $(this).parent("li").toggleClass('active');
                    $('.sidebar li.active').not($(this).parents("li")).removeClass("active");
                });
                $(".nav-link").each(function() {
                    var current = window.location.href;
                    var href = $(this).attr("href");
                    if (href == current) {
                        $(this).parents('ul').addClass('show');
                        $($(this).parents('ul')).parents('ul').addClass('show');
                        $(this).parents('.dropdown-sub-megamenu').toggleClass('active');
                        $(this).parents('.nav-items-class').addClass('active');
                        $(this).parent().addClass("active");
                    }
                });
            } else {
                $(".desktop-remove-anchor").attr('href', 'javascript:void(0)');
                $('.nav-link').on('click', function(e) {
                    //setTimeout(function(){  console.log("ddd"); $('.navbar-collapse').toggleClass('show'); } , 200);
                });
                // console.log("hiver");
                $('.nav-items-class').on('mouseover', function() {
                    $(this).addClass('active');
                });
                $('.nav-items-class').on('mouseout', function() {
                    $(this).removeClass('active');
                    manageActiveAfterPageReload();
                });

                manageActiveAfterPageReload();

                function manageActiveAfterPageReload() {
                    $(".nav-link").each(function() {
                        var current = window.location.href;
                        var href = $(this).attr("href");
                        if (href == current) {
                            $(this).parents('.nav-items-class').addClass('active');
                        }
                    });
                }
            }

        });
    </script>

    <!-- rogressbar pandding script -->

    <script>

		function displayLeaveCountChart(thisitem){
			var leave_balance = $(thisitem).parents('.leave-type-chart').find(".leave-balance-value").html(); 
			let progressValuePaid = 0;
            let progressEndValuePaid = 100;
            let speedPaid = 50;

            if( parseFloat(progressEndValuePaid) > 0 ){
            	let progressPaid = setInterval(() => {
                    progressValuePaid++;
                    var leave_record_id = $(thisitem).parents('.leave-type-chart').find(".leave-value").attr('data-id'); 
                    if( leave_record_id != "" && leave_record_id != null &&  leave_record_id == "{{ config('constants.UNPAID_LEAVE_TYPE_ID') }}"){
                    	$(thisitem).parents('.leave-type-chart').find(".leave-value").html( ( leave_balance > 0 ? ' - ' : '' )  + leave_balance + " Days Consumed");
                    } else {
                    	$(thisitem).parents('.leave-type-chart').find(".leave-value").html( leave_balance + " Days Available");
                    } 
                    
                    
                    //$($(thisitem).parents('.leave-type-chart').find(".leave-process")).css("background" , `conic-gradient( #8d191a ${progressValuePaid * 3.6}deg, #e4e4e4 ${progressValuePaid * 3.6}deg )`);
                    if (progressValuePaid == progressEndValuePaid) {
                        clearInterval(progressPaid);
                    }
                }, speedPaid);
            }
			
			
			
			
		}
    
        // Paid leave start
        let progressBarPaid = document.querySelector(".paid-leave");
        let valueContainerPaid = document.querySelector(".paid-value");
        // console.log("progressBarPaid = " + progressBarPaid);

        if (progressBarPaid != null && progressBarPaid != "" && valueContainerPaid != "" && valueContainerPaid != null) {
            let progressValuePaid = 0;
            let progressEndValuePaid = 40;
            let speedPaid = 50;

            let progressPaid = setInterval(() => {
                progressValuePaid++;
                valueContainerPaid.innerHTML = `${progressValuePaid} Days <br>Available`;
                progressBarPaid.style.background = `conic-gradient(
        #8d191a ${progressValuePaid * 3.6}deg,
        #e4e4e4 ${progressValuePaid * 3.6}deg
            )`;
                if (progressValuePaid == progressEndValuePaid) {
                    clearInterval(progressPaid);
                }
            }, speedPaid);
        }




        // Earned leave start

        let progressBarEarned = document.querySelector(".earned-leave");
        let valueContainerEarned = document.querySelector(".earned-value");

        if (progressBarEarned != null && progressBarEarned != "" && valueContainerEarned != "" && valueContainerEarned != null) {
            let progressValueEarned = 0;
            let progressEndValueEarned = 50;
            let speedEarned = 50;

            let progressEarned = setInterval(() => {
                progressValueEarned++;
                valueContainerEarned.innerHTML = `${progressValueEarned} Days <br>Available`;
                progressBarEarned.style.background = `conic-gradient(
        #8d191a ${progressValueEarned * 3.6}deg,
        #e4e4e4 ${progressValueEarned * 3.6}deg
        )`;
                if (progressValueEarned == progressEndValueEarned) {
                    clearInterval(progressEarned);
                }
            }, speedEarned);
        }


        // Carry Forward leave start

        let progressBarCarry = document.querySelector(".carry-forward-leave");
        let valueContainerCarry = document.querySelector(".carry-forward-value");

        if (progressBarCarry != null && progressBarCarry != "" && valueContainerCarry != "" && valueContainerCarry != null) {
            let progressValueCarry = 0;
            let progressEndValueCarry = 80;
            let speedCarry = 50;

            let progressCarry = setInterval(() => {
                progressValueCarry++;
                valueContainerCarry.innerHTML = `${progressValueCarry} Days <br>Available`;
                progressBarCarry.style.background = `conic-gradient(
        #8d191a ${progressValueCarry * 3.6}deg,
        #e4e4e4 ${progressValueCarry * 3.6}deg
                )`;
                if (progressValueCarry == progressEndValueCarry) {
                    clearInterval(progressCarry);
                }
            }, speedCarry);
        }



        // adjustment start


        // let progressBarAdjus = document.querySelector(".adjustment-leave");
        // let valueContainerAdjus = document.querySelector(".adjustment-value");

        // if (progressBarAdjus != null && progressBarAdjus != "" && valueContainerAdjus != "" && valueContainerAdjus != null) {
        //     let progressValueAdjus = 0;
        //     let progressEndValueAdjus = 100;
        //     let speedAdjus = 50;

        //     let progressAdjus = setInterval(() => {
        //         progressValueAdjus++;
        //         valueContainerAdjus.innerHTML = `${progressValueAdjus} Days <br>Available`;
        //         progressBarAdjus.style.background = `conic-gradient(
        // #8d191a ${progressValueAdjus * 3.6}deg,
        // #e4e4e4 ${progressValueAdjus * 3.6}deg
        //         )`;
        //         if (progressValueAdjus == progressEndValueAdjus) {
        //             clearInterval(progressAdjus);
        //         }
        //     }, speedAdjus);
        // }
    </script>


    <script>
        $(function() {
            $('.relation-bd, .relation-bd2').datetimepicker({
                useCurrent: false,
                viewMode: 'days',
                ignoreReadonly: true,
                format: 'DD-MM-YYYY',
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

        $('body').on('click', 'button.fc-prev-button', function() {
			var select_month_start_date = calendar.getDate();
			if( select_month_start_date != "" && select_month_start_date != null  ){
				var month_start_date =  moment(select_month_start_date).format('YYYY-MM-DD');
				leaveCalendar(month_start_date);
			}
		});
		$('body').on('click', 'button.fc-next-button', function() {
			var select_month_start_date = calendar.getDate();
			if( select_month_start_date != "" && select_month_start_date != null  ){
				var month_start_date =  moment(select_month_start_date).format('YYYY-MM-DD');
				leaveCalendar(month_start_date);
			}
		});

		function diffBetweenTimeIntoJS(start_time = null , end_time = null , thisitem = null ){
			start_time = '<?php echo date('Y-m-d')?>' + ' ' + start_time ;
			end_time = '<?php echo date('Y-m-d')?>' + ' ' + end_time;

			//console.log("start_time = " + start_time );
			//console.log("end_time = " + end_time );


			var time_format = 'hh:mm'; 
			var related_field_name = 'end_time';
			if( $(thisitem).hasClass('.shift-start-time') != false ){
				related_field_name = 'start_time';
			}
			var time_diff = '';
			var diff =  Math.abs(new Date(start_time) - new Date(end_time));
			//console.log("related_field_name = " + related_field_name );
			if( start_time != "" && start_time != null && end_time != "" && end_time != null  ){

				var timeStart = new Date(start_time);
				var timeEnd = new Date(end_time);

				if(timeStart > timeEnd){
					//console.log("if");
				    var start_diff = timeStart - new Date("<?php echo date('Y-m-d')?> 23:59");
				   	var end_diff = new Date("<?php echo date('Y-m-d')?> 12:00 AM") - timeEnd;

					time_diff = start_diff + end_diff;
					diff = Math.abs(time_diff);
					//console.log("diff = " + diff );
					var seconds = Math.floor(diff/1000); //ignore any left over units smaller than a second
					seconds = ( parseFloat( seconds )  > 0 ? ( seconds + 60 ) : seconds );
				} else {
					//console.log("else");
				    time_diff = timeStart - timeEnd;
				    diff = Math.abs(time_diff);
					//console.log("diff = " + diff );
					var seconds = Math.floor(diff/1000); //ignore any left over units smaller than a second
					
				}
					
				
				
			} else {
				var seconds = 0;
			}
			
			var minutes = Math.floor(seconds/60); 
			//console.log("seconds = " + seconds );
			seconds = seconds % 60;
			var hours = Math.floor(minutes/60);
			minutes = minutes % 60;

			var timeDiff = '';
			if( parseFloat(hours)  > 0.00 && parseFloat(minutes) > 0.00 ){
				timeDiff = hours + (  parseFloat(hours) > 1 ? ' hrs ' : ' hr ' ) +   minutes + (  parseFloat(minutes) > 1 ? ' mins' : ' min' );
			} else {
				if( parseFloat(hours)  > 0 ){
					timeDiff = hours + (  parseFloat(hours) > 1 ? ' hrs ' : ' hr ' );
				}
				if( parseFloat(minutes)  > 0 ){
					timeDiff = minutes + (  parseFloat(minutes) > 1 ? ' mins' : ' min' );
				}
			}
			return timeDiff;
		}

		
		
    </script>
    <script>
$(document).ready(function () {  

	var bootstrapModalCounter = 0;
  	$('.modal').on("hidden.bs.modal", function (e) {
		--bootstrapModalCounter;
		
		if (bootstrapModalCounter > 0) {
	  		$('body').addClass('modal-open');
		}
  	}).on("show.bs.modal", function (e) {
			++bootstrapModalCounter;
			$(document).off('focusin.modal');
			const zIndex = 1050 + 10 * $('.modal:visible').length; 
			$(this).css('z-index', zIndex); 
			setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack')); 
  	});
		
});

function getLeaveTimeOffMinDate(){

	var current_date = '<?php echo date('d')?>';
	var last_allowed_date = parseInt("{{ config('constants.SALARY_CYCLE_END_DATE')}}") +  parseInt("{{ config('constants.FIX_SALARY_INFO_AFTER')}}");
	var allowed_leave_min_date = '';
	//console.log("last_allowed_date = "  + last_allowed_date );
	if( parseInt(current_date) >= parseInt(last_allowed_date) ){
		allowed_leave_min_date = "{{ config('constants.SALARY_CYCLE_START_DATE')}}" + '-' +  '<?php echo date('m-Y') ?>';
	} else {
		allowed_leave_min_date = "{{ config('constants.SALARY_CYCLE_START_DATE')}}" + '-' +  '<?php echo date('m-Y' ,strtotime("-1 month")) ?>';
	}
	return allowed_leave_min_date;
}

function getStatusWiseEmployeeDetails(thisitem){

	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
	var on_hold_status = ( $(thisitem).hasClass('on_hold_report') != false ? "{{ config('constants.SELECTION_YES') }}" : "{{ config('constants.SELECTION_NO') }}" ) ;
	var all_permission_id = $.trim($(thisitem).attr('data-all-permission-id'));
	
	$.ajax({
		type : 'post',
		data : { 'search_employment_status' : search_employment_status, 'on_hold_status' : on_hold_status , 'all_permission_id'  : all_permission_id},
		url : site_url + 'get-status-wise-emp-details',
		beforeSend : function(){
			showLoader();
		},
		success : function(response){
			hideLoader();
			if(response != '' && response != null){
				$(thisitem).parents('.depedent-row').find('.status-wise-emp-div').html(response);
			}
			filterData();
		},
		error : function(){
			hideLoader();
		}
	});
}



$(document).on('shown.bs.modal','#verify_password_modal', function(){
	$('input[name="verify_password"]').focus();
});

</script>
<script>
    $(document).ready(function () { 
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>

<script>
    $(document).ready(function() {
        if($(window).width() < 767 && $(".dataTables_wrapper").length > 0){
            $.fn.DataTable.ext.pager.numbers_length = 5;
        } 
    });
</script>

<!-- Just before </body> -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script> -->
    <!-- jQuery first -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJ+Y3pjcZ+3x8N+fZ/2LrTn1z5i4VJ6h+XoRI="
            crossorigin="anonymous"></script> -->

    <!-- Then Bootstrap -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-Piv4xVNRyMGpqkL1Z8t5AbTZkVg0qmwZ3fU5DpHrt1U9ZBwh0VFt8l1NQ9G2v4aT"
            crossorigin="anonymous"></script> -->
</body>

</html>