
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($supervisorName) ? $supervisorName : '' ) }},</strong></div>
			<p style="margin-bottom:0">Your employee {{ $employeeName }} - {{ $employeeCode }} has been terminated from the post.<br>
			<br>
		@else 
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;margin-top:12px"> Dear {{ ( isset($employeeName)  ? $employeeName : '' ) }},</strong></div>
			<p>Your termination mail has been sent to your superior.</p>
			<br>
		@endif
	</div>
 </div>
 @endsection