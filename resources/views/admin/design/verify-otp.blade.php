@extends('includes/login-header')

@section('pageTitle', $pageTitle )

@section('content')

<style>
  .otp-icon {font-size: 50px;color: var(--primary-color);text-align: center;margin-bottom: 0;}
  .back-to-login {color: #8d191a;font-size: 15px;text-align:center}
  .login-items .background {position: relative;z-index: 1;font-weight: 700;font-size: 1.3rem;}
  .login-items button.btn.bg-theme.login-button {border: none;padding: 12px 45px;text-transform: uppercase;box-shadow: none;border-radius: 10px;width: 100%;margin:0}
  .login-items button.btn.bg-theme.login-button:hover {box-shadow: none;}
  .otp-icon{margin-bottom: 25px;}
  .otp-icon-img{width:67px;height:67px;filter: invert(18%) sepia(27%) saturate(7106%) hue-rotate(341deg) brightness(77%) contrast(98%);}
  .back-to-login:hover{color: #212529;}
  @media (max-width: 767px) {
    .otp-icon{font-size: 40px;}
    .login-page-section .login-title{padding:10px 0;}
  }
</style>

<div class="row align-items-center h-100vh mx-auto otp-main-div">
  <div class="col-lg-5 col-sm-11 col-12 d-flex align-items-center justify-content-center border-outer mx-auto p-0">
    <div class="login-items form-class body-form-info">
      <form id="verify-otp-form">
        <div class="card card-otp bg-transparents border-0">
          <div class="card-body px-lg-5 pt-lg-3 p-2">
            <div class="otp-icon text-center">
              <img src="{{ asset('images/otp.png') }}" alt="otp" class="otp-icon-img">
            </div>
            <div class="text-center mb-lg-3">
              <h3 class="background text-uppercase">
                <span>{{ trans("messages.verify-otp") }}</span>
              </h3>
              <label>{{ trans("messages.security-purpose") }}</label>
            </div>
            <hr>
            <div class="form-group mt-4">
              <label for="inputUsername" class="form-label">{{ trans("messages.enter-otp") }}<span class="text-danger">*</span></label>
              <input class="form-control" name="login_otp" type="text" placeholder="{{ trans('messages.enter-otp') }}" autofocus />
            </div>
            <button class="btn submit bg-theme login-button text-white font-weight-bold" type="submit" title="{{ trans('messages.verify-otp') }}">{{ trans("messages.verify-otp") }}</button>
          </div>
          <a href="{{ url('login') }}" class="back-to-login py-3"><i class="fa fa-arrow-left mr-2"></i>{{ trans("messages.back-to-login") }}</a>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
  $("#verify-otp-form").validate({
    errorClass: "invalid-input",
    rules: {
      login_otp: {
        required: true,
        noSpace: true
      },
    },
    messages: {
      login_otp: {
        required: "{{ trans('messages.required-otp') }}"
      },
    },
    submitHandler: function(form) {
      showLoader()
      form.submit();
    }
  });
</script>
@endsection