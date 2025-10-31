<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- generic meta -->
    <meta name="description" content="{{ config('constants.SITE_DESCRIPTION') }}" />
    <meta name="keywords" content="{{ config('constants.SITE_KEYWORDS') }}" />
    <meta name="author" content="" />
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
    <title>@yield('pageTitle') | {{ config('', 'HR Management System - Acorn ')}}</title>
    <!-- css -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset ('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('css/select2.css') }}">
    <!-- main css -->
    <link rel="stylesheet" href="{{ asset ('css/main.css') . '?ver=' .config('constants.CSS_JS_VERSION') }}">
    <link rel="stylesheet" href="{{ asset ('css/style.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset ('css/login.css') }}"> -->
    <!-- scripts -->
    <script src="{{ asset ('js/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset ('js/bootstrap.bundle.min.js') }}"></script>
    <!-- plugins -->
    <script type="text/javascript" src="{{ asset ('js/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset ('js/validator-additional-methods.js') }}"></script>
    <script src="{{ asset ('js/select2.js') }}"></script>
    <!-- main script -->
    <script>    
        var site_url = "{{ url('/') }}" + '/';
    </script>
    <script src="{{ asset ('js/common.js') }}"></script>
    <style>
    	 *{margin:0; box-sizing: border-box;}
         body{font-family: 'Poppins', 'DM Sans', sans-serif;}
        :root{--primary-color: #8d191a;}
         a {color: #2748a5;font-weight: 600;}
         a:hover {text-decoration: none;}
        
        .login-page-section {height: 100vh;position: relative;background: #fff;display: flex;align-items: center; justify-content: center;}
        .login-page-section:before {position: absolute;content: "";top: 0;left: 0;width: 100%;height: 36%;background: #8d191a;}
        .login-page-section .login-title {text-align: center;padding: 15px 0;font-weight: 700;font-size: 20px;}
        .login-page-section .logo-img {max-width: 195px;padding: 18px;}
        .login-page-section .form-items {padding: 0 120px;}
        .login-page-section .login-page-items {margin: 0 70px;}
        .login-page-section .form-items {padding: 0 70px;}
        .login-page-section .overlay-title {text-align: center;font-size: 20px;font-weight: 700;color: #000; padding: 0px 20px;}
        .login-page-section .form-class {height: 100%;padding: 20px 10px}
        .login-page-section .submit-class {border-radius: 10px;border: 1px solid var(--primary-color);background-color: var(--primary-color);color: #FFFFFF;font-size: 12px;font-weight: bold;padding: 12px 45px;letter-spacing: 1px;text-transform: uppercase;transition: transform 80ms ease-in;margin: 25px auto;display: table;width: 100%;}
        .login-page-section .form-group {margin-bottom: 25px;}
        .login-page-section .login-items {background-color: #fff;border-radius: 50px;box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);position: relative;}
        .login-page-section .overlay-items {height: 100%;width: 100%;position: relative;border-radius: 0px 50px 50px 0px;background: #edf0f5;}
        .login-page-section .form-class .form-control {padding: 0px 15px;font-size: 15px;height: 45px;background: #edf0f5;border: 0;border-radius: 10px;}
        .login-page-section .form-class-input {font-size: 15px;color: #9b9b9b;font-weight: 500;}
        .login-page-section .eye-icon {position: absolute;right: 16px;top: 43px;color: #9a9a9a;font-size: 18px;}
        .login-page-section .login-img{max-width: 345px;}
        .login-page-section .overlay-panel {text-align: center;padding-top: 120px;}
        .login-page-section .overlay {padding-top: 40px;text-align: center;}
        .login-page-section .wave {position: absolute;top: 35%;left: 0;width: 100%;}
        .login-logo{width:35%;margin: 0 auto;}
      

        @media (max-width:1199px){
            .login-page-section .form-items {padding: 0 45px;}
        }
        @media (max-width:991px){
            .login-page-section .overlay-items{display: none;}
        }
        @media (max-width:767px){
            .login-page-section .login-page-items{
                margin: 0 0px;
            }
        }
        @media (max-width:576px){
            .login-page-section .form-items {padding: 0 25px;}
        }
    </style>

</head>

<body>
    <!-- navbar start -->
<main class="page-height">
  <section class="login-page-section">
    <div class="wave">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
        <path fill="#8d191a" fill-opacity="1" d="M0,96L48,101.3C96,107,192,117,288,117.3C384,117,480,107,576,90.7C672,75,768,53,864,48C960,43,1056,53,1152,80C1248,107,1344,149,1392,170.7L1440,192L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
      </svg>
    </div>
  
    <div class="container">
       @yield('content')     
    </div>
  </section>

</main>
    <!-- nav end  -->
</body>

</html>