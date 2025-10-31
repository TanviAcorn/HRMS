
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($supervisorName) ? $supervisorName : '' ) }},</strong></div>
			<p>HR Admin has Suspended {{ ( isset($employeeName)  ? $employeeName : '' ) }} .</p>
		@else 
			<br>
			<div style="padding-top:10px"><strong style="font-size: 13px;text-align: justify;color: #202020;margin-top:12px"> Dear {{ ( isset($employeeName)  ? $employeeName : '' ) }},</strong></div>
			<p>You have been Suspended from your duty for duration {{ ( isset($suspendDuration) ? $suspendDuration : "" ) }} from your post.</p>
			<p>Kindly make note that this will be considered under payment deduction.</p>
			<br>
		@endif
	</div>
 </div>
 @endsection