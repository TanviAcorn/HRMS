
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ $employeeName }},</strong></div><br>
        <p style="font-weight:light; margin:0; font-size: 14px; margin-bottom:5px; color: #383838; font-family: 'Open Sans', sans-serif; ">We are pleased to give you an access of HR Portal.</p>
        <p style="font-weight:light; margin:0; font-size: 14px; margin-bottom:10px; color: #383838; font-family: 'Open Sans', sans-serif; ">Your Login Credentials are</p>
        <p style="font-weight:light; margin:0; font-size: 14px; margin-bottom:10px; color: #383838; font-family: 'Open Sans', sans-serif; ">{{ trans('messages.email-id') }} : {{ isset($email) ? $email : ''  }}</p>
        <p style="font-weight:light; margin:0; font-size: 14px; color: #383838; font-family: 'Open Sans', sans-serif; ">{{ trans('messages.password') }} : {{ isset($password) ? $password : ''  }}</p><br>
        <a href="{{ isset($link) ? $link : ''  }}" style=" font-size:14px; border-radius: 5px; background-color:#007bff; padding:5px 10px; color:#fff; text-decoration:none; border-bottom:3px solid #0050a7;">Login Now</a>
        <!-- <p style="font-weight:light; margin:0; font-size: 14px; color: #383838; font-family: 'Open Sans', sans-serif; ">{{ trans('messages.link') }} : {{ isset($link) ? $link : ''  }}</p> -->
        <br><br>
	</div>
 </div>
 @endsection