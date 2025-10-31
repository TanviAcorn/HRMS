@extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

@section('content')
<div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
    <div style="padding:0px 30px">
        <div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ $name }} , </strong> </div> <br>
        <p style="padding-top: 3px; color: #383838;font-size: 14px; margin:0;"> Forgot your password? Please <a href="{{ $link }}" target="_blank" style="color: #008080;">Click here to set new password</a></p>
        <p style="padding-top: 3px; color: #383838;font-size: 14px; margin:0;margin-bottom:0;"> This link is valid for {{ config ( 'constants.FORGET_PASSWORD_CHECK_TIME') }} minutes only. </p>
    </div>
</div>
@endsection