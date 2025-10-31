
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($employeeName) ? $employeeName : '' ) }},</strong></div>
		<p>{{ trans('messages.pay-slip-content') }}</p>
	</div>
	<br>
 </div>
 @endsection