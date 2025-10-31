@extends('includes/login-header')

@section('pageTitle', $pageTitle )

@section('content')
        <div class="login-page-items">
        <div class="login-items">
          <div class="row no-gutters">
            <div class="col-lg-6">
              <div class="card  card-login mx-auto my-5 border-0  bg-transparents">
				<img src="{{ asset ('images/logo2.png') }}" alt="" srcset="" class="img-fluid login-logo">
				<div class="form-class">
				<h3 class="login-title">{{ trans("messages.login-title") }}</h3>
				{{ Wild_tiger::readMessage() }}
				<div class="body-form-info">
				{!! Form::open(array( 'id '=> 'login-form' , 'method' => 'post' ,  'url' => 'login/checkLogin')) !!}
					<div class=" form-group col-md-12 form-group {{ (!empty(old('login_email')) ? 'focused' : '' ) }}">
						<label class="form-label" for="email">{{ trans("messages.email-id") }}:</label>
						<input id="email" class="form-input form-control " autocomplete="off" type="email" placeholder="{{ trans('messages.email-id') }}" name="login_email" value="{{ old('login_email') }}">
					</div>
					<div class="form-group col-md-12">
						<label class="form-label" for="password">{{ trans("messages.password") }}:</label>
						<input id="password" class="form-input form-control" autocomplete="new-password" placeholder="{{ trans('messages.password') }}" type="password" name="login_password" value="">
					</div>
					
					<div class="form-group col-md-12">
						<button type="submit" class="btn submit-class login-page">{{ trans("messages.login") }}</button>
					</div>
					<a href="{{ url('forgot-password') }}"  class="forgot-password">Forgot Password ?</a>
					{!! Form::close() !!}
				</div>
				</div>
				</div>          
            </div>
            <div class="col-lg-6">
              <div class="overlay-items">
                <div class="overlay-panel">
                  <img src="{{ asset ('images/image-one.png') }}"  alt="Logo" class="img-fluid login-img">
                </div>
                <div class="overlay">
                  <h4 class="overlay-title">HR Management System </h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
<script>
$("#login-form").validate({
	errorClass: "invalid-input",
	rules:{
		login_email :{ required : true , noSpace : true , email_regex : false  },
		login_password :{ required : true , noSpace : true },
	},
	messages:{
		login_email :{ required : '{{ trans("messages.required-email-id") }}'  },
		login_password :{ required : '{{ trans("messages.required-login-password") }}'  },
	},
	submitHandler: function(form) {
		showLoader();
		form.submit();
	},
});
</script>
@endsection