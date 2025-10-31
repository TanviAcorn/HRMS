
@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
<div class="breadcrumb-wrapper d-flex border-bottom">
<div class="container-fluid">
    <h1 class="mb-0 header-title" id="pageTitle">Change Password</h1>
</div>
</div>
    <section class="inner-wrapper-common-section main-listing-section d-flex align-items-center justify-content-center chnage-password p-3">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-7 col-12">
                    <div class="card">
                        <div class="card-header custom-card-header bg-white main-inner-card">    
                            <div class="d-lg-flex d-block py-1 custom-breadcrumb">
                                <span class="text-theme h5 mb-0 breadcrumb-main-heading">{{ trans("messages.change-password") }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="body-form-info reset-bdy-info mt-0 pb-0">
                            {{ Wild_tiger::readMessage() }}
                                {!! Form::open(array( 'id '=> 'update-password-form' , 'method' => 'post' ,  'url' => 'dashboard/updatePassword')) !!}
                                    <div class="form-group">
                                        <label class="form-label" for="current_password">{{ trans("messages.current-password") }}<span class="star text-danger">*</span></label>
                                        <input id="current_password" class="form-control" type="password" placeholder="{{ trans("messages.current-password") }}" name="current_password">
                                        {{ $errors->first('current_password') }}
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="new_password">{{ trans("messages.new-password") }}<span class="star text-danger">*</span></label>
                                        <input id="new_password" class="form-control" type="password" placeholder="{{ trans("messages.new-password") }}" name="new_password">
                                        {{ $errors->first('new_password') }}
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="confirm_password">{{ trans("messages.confirm-password") }}<span class="star text-danger">*</span></label>
                                        <input id="confirm_password" class="form-control" type="password" placeholder="{{ trans("messages.confirm-password") }}" name="confirm_password">
                                        {{ $errors->first('confirm_password') }}
                                    </div>
                                    <input type="hidden" name="user_id" value="">
                                    <div class="motadata-ftu-link text-center mt-4">
                                        <button type="submit" class="btn-submit btn bg-theme text-white submit-button-custom" title="{{ trans("messages.update") }}">{{ trans("messages.update") }}</button>
                                        <a href="{{ url('dashboard') }}" class="btn btn-outline-secondary submit-button-custom shadow-sm" title="{{ trans("messages.back") }}">{{ trans("messages.back") }}</a>
                                    </div>
                                    {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script>
$("#update-password-form").validate({
	errorClass: "invalid-input",
	rules:{
		current_password :{ required : true , noSpace : true },
		new_password :{ required : true , noSpace : true },
		confirm_password :{ required : true , noSpace : true , equalTo : "#new_password"    },
	},
	messages:{
		current_password :{ required : '{{ trans("messages.required-current-password") }}'  },
		new_password :{ required : '{{ trans("messages.required-new-password") }}'  },
		confirm_password :{ required : '{{ trans("messages.required-confirm-password") }}' , equalTo : '{{ trans("messages.confirm-password-not-match") }}'   },
	},
	submitHandler: function(form) {
		showLoader();
		form.submit();
	},
});
</script>
@endsection
