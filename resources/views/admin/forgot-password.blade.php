@extends('includes/login-header')

@section('pageTitle', $pageTitle )

@section('content')
<style>
    a {color: #2748a5;font-weight: 600;}
    a:hover {text-decoration: none;}
    main:before {position: absolute;content: "";top: 0;left: 0;width: 100%;height: 50%;background: rgb(141 25 26);}
    .password-icon {font-size: 65px;color: #8d191a;margin-bottom: 15px;}
    .wrapper {align-items: center;justify-content: center;width: 100%;min-height: 100%;height: 100%;padding: 0;}
    .login-items {box-shadow: 0 10px 10px 0 rgb(0 0 0 / 30%);text-align: center;margin: 30px;width: 100%;border-radius: 0px;position: relative;background: #fff;padding: 40px;}
    .login-items button.btn.bg-theme.login-button {border: none;padding: 12px 45px;text-transform: uppercase;box-shadow: none;border-radius: 10px;margin: 1rem auto;width: 85%;}
    .login-items button.btn.bg-theme.login-button:hover {box-shadow: none;}
    .login-items input {background-color: transparent;padding: 1rem 2.7rem;margin: 0 auto;width: 85%;border: aliceblue;height: 52px;border-radius: 0;border-bottom: 1px solid #ddd;}
    .login-items div#formContent {width: 98%;margin: 0 auto;}
    .login-items .title-text {padding: 0.5em;}
    .login-items .title-text p {font-size: 1.2rem;margin: 10px 0;}
    .login-items .alert{margin-bottom: 0.25rem;}
    .login-items input:focus,
    .login-items input:focus-visible {background-color: #fff;border-bottom: 1px solid var(--primary-color);outline: none;box-shadow: none;}
    .login-items h1.background {position: relative;z-index: 1;font-weight: 700;font-size: 1.3rem;}
    .login-items h1.background:before {border-bottom: 2px solid var(--primary-color);content: "";margin: 15px auto;position: absolute;top: 60%;left: 0;right: 0;bottom: 0;width: 28%;z-index: -1;}
    .login-items img.brand-logo-img {max-width: 100%;height: auto;width: 40%;}
    .login-items .welcome_img img {width: 100%;}
    .input-icon {position: relative;margin: 0 auto;}
    .input-icon::after {position: absolute;top: 14px;left: 45px;font-family: "Font Awesome 5 Free";font-weight: 900;}
    .invalid-input {text-align: left;width: 80%;}
    .user-input::after {content: "\f0e0";}
    .password-input::after {content: "\f023";}
    .not-visible {visibility: hidden;}
    .back-to-login {color: #8d191a;font-size: 15px;}
    .back-to-login:hover{color: #212529;}

    @media (max-width: 991px) {
        .input-icon::after {left: 53px;}
    }

    @media (max-width: 768px) {
        .login-items {margin: 5px;padding: 25px 10px 30px;}
        main:before {border-radius: 0 15px 15px 0;}
        .login-items input {padding: 15px 35px;width: 95%;border-radius: 0px;}
        .input-icon::after {left: 20px;}
        .login-items button.btn.bg-theme.login-button {padding: 1rem 1rem;font-size: 0.8rem;}
        }
</style>

<div class="row no-gutters pt-3 justify-content-center">
            <div class="col-lg-5">
                <div class="login-items">                    
                    <div>
                        <div class="password-icon">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </div>
                        <div class="title-text mb-1">
                            <h1 class="background text-uppercase">{{ trans("messages.forgot-password") }}</h1>
                        </div>
                    	{!! Form::open(array( 'id '=> 'forgot-password-form' , 'method' => 'post' , 'url' =>  config('constants.LOGIN_SLUG') .  '/sendForgotPasswordMail' )) !!}
                    
                            <label>{{ trans('messages.reset-password-link-text') }}</label>                            
                            {{ Wild_tiger::readMessage() }}
                            <div class="form-group input-icon user-input mb-1">
                                <label for="login_email" class="form-label d-none"></label>
                                <input class="form-control" name="login_email" type="text" placeholder="{{ trans('messages.email-id') }}">
                            </div>
                            <button type="submit" class="btn submit bg-theme login-button text-white font-weight-bold mt-4"> {{ trans("messages.reset-password") }}</button>     
 							<br>
                            <a href="{{ config('constants.LOGIN_URL') }}" class="back-to-login"><i class="fa fa-arrow-left mr-2"></i>{{ trans("messages.back-to-login") }}</a>
                       {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>

    <script>
        $("#forgot-password-form").validate({
            errorClass: "invalid-input",
            rules: {
            	login_email: {
                    required: true,
                    noSpace: true
                },
            },
            messages: {
            	login_email: {
                    required: '{{ trans("messages.required-email-id") }}'
                },
            },
            submitHandler: function(form) {
                showLoader()
                form.submit();
            }
        });
    </script>
    @endsection