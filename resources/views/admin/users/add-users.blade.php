@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color add-user-section">
    <div class="breadcrumb-wrapper d-lg-flex p-3 border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }}</h1>
        <nav aria-label="breadcrumb" class="d-flex mr-3">
            <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
                <li class="breadcrumb-item"><a href="{{ config('constants.USERS_URL') }}" class="category-add-link">{{ trans("messages.all-users") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
            </ol>
        </nav>
    </div>
    <section class="inner-wrapper-common-section dropdown-main main-listing-section p-3 user-section">
        <div class="card">

            <div class="card-body">
                <div class="body-form-info reset-bdy-info mt-0 pb-0">
                    {{ Wild_tiger::readMessage() }}
                    {!! Form::open(array( 'id '=> 'add-user-form' , 'method' => 'post' , 'url' => 'users/add')) !!}
                    @if (count($errors) > 0)
                    <div class="error">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="name" class="control-label">{{ trans("messages.name") }} <span class="star">*</span></label>
                                <input id="name" class="form-control" type="text" name="name" placeholder="{{ trans('messages.name') }}" value="{{old('name',  ( (isset($recordInfo) && (!empty($recordInfo->v_name))) ?  $recordInfo->v_name : '' ) )}}">
                                {{ $errors->first('name') }}
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="password" class="control-label">{{ trans("messages.password") }} <?php echo ((isset($recordInfo) && ($recordInfo->i_id > 0)) ? '' : '<span class="star">*</span>') ?></label>
                                <input id="password" class="form-control" type="password" name="password" placeholder="{{ trans('messages.password') }}" value="">
                                {{ $errors->first('password') }}
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="confirm_password" class="control-label">{{ trans("messages.confirm-password") }} <?php echo ((isset($recordInfo) && ($recordInfo->i_id > 0)) ? '' : '<span class="star">*</span>') ?></label>
                                <input id="confirm_password" class="form-control" type="password" name="confirm_password" placeholder="{{ trans('messages.confirm-password') }}" value="" autocomplete="new-password">
                                {{ $errors->first('confirm_password') }}
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="email" class="control-label">{{ trans("messages.email") }} <span class="star">*</span></label>
                                <input id="email" class="form-control" type="text" name="email" placeholder="{{ trans("messages.email") }}" value="{{old('email',  ( (isset($recordInfo) && (!empty($recordInfo->v_email))) ?  $recordInfo->v_email : '' ) )}}">
                                {{ $errors->first('email') }}
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="mobile" class="control-label">{{ trans("messages.mobile") }} <span class="star">*</span></label>
                                <input id="mobile" onkeyup="onlyNumber(this);" maxlength="10" class="form-control" type="text" name="mobile" placeholder="{{ trans('messages.mobile') }}" value="{{old('mobile', ( (isset($recordInfo) && (!empty($recordInfo->v_mobile))) ?  $recordInfo->v_mobile : '' ) )}}">
                                {{ $errors->first('mobile') }}
                            </div>
                        </div>
                    </div>
                    <div class="motadata-ftu-link">
                        <?php if (isset($recordInfo) && ($recordInfo->i_id > 0)) { ?>
                            <input type="hidden" name="record_id" value="{{ Wild_tiger::encode($recordInfo->i_id)}}">
                            <button type="submit" title="{{ trans('messages.update') }}" class="btn btn bg-theme text-white btn-wide">{{ trans("messages.update") }}</button>
                        <?php } else { ?>
                            <button type="submit" title="{{ trans('messages.submit') }}" class="btn btn bg-theme text-white btn-wide">{{ trans("messages.submit") }}</button>
                        <?php } ?>
                        <a href="{{ config('constants.USERS_URL') }}" title="{{ trans('messages.back') }}" class="btn btn-outline-secondary shadow-sm btn-wide">{{ trans("messages.back") }}</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
</main>
<script>
    $("#add-user-form").validate({
        errorClass: "invalid-input",
        rules: {
            name: {
                required: true,
                noSpace: true
            },
            password: {
                required: function(element) {
                    return ((($.trim($("[name='record_id']").val()) != null) && ($.trim($("[name='record_id']").val()) != "")) ? false : true)
                },
                noSpace: true
            },
            confirm_password: {
                required: function(element) {
                    return ((($.trim($("[name='record_id']").val()) != null) && ($.trim($("[name='record_id']").val()) != "")) ? false : true)
                },
                noSpace: true,
                equalTo: "#password"
            },
            email: {
                required: true,
                noSpace: true,
                email_regex: true,
                validateUniqueEmail: true
            },
            mobile: {
                required: true,
                noSpace: true,
                mobile_regex: true
            },
        },
        messages: {
            name: {
                required: '{{ trans("messages.required-name") }}'
            },
            password: {
                required: '{{ trans("messages.required-password") }}'
            },
            confirm_password: {
                required: '{{ trans("messages.required-confirm-password") }}',
                equalTo: '{{ trans("messages.confirm-password-not-match") }}'
            },
            email: {
                required: '{{ trans("messages.required-login-email") }}'
            },
            mobile: {
                required: '{{ trans("messages.required-enter-mobile") }}'
            },
        },
        submitHandler: function(form) {
            showLoader();
            form.submit();
        },
    });
</script>
@endsection