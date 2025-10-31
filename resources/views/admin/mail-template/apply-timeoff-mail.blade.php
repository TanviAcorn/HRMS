
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($supervisorName) ? $supervisorName : '' ) }},</strong></div>
			<p>Your Employee {{ ( isset($employeeName)  ? $employeeName : '' ) }} - {{ ( isset($employeeCode)  ? $employeeCode : '' ) }} has sent a Time Off ({{ ( isset($timeoffTypeName) ? $timeoffTypeName : '' ) }}) request.</p>	
			<p>Kindly approve or deny before time lapse.</p>
			<?php if( isset($backDuration) && (!empty($backDuration)) ) { ?>
				<p>Time Back Details : {{ $backDuration }}</p>
			<?php } ?>
		@else 
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;margin-top:12px"> Dear {{ ( isset($employeeName)  ? $employeeName : '' ) }},</strong></div>
			<p>Your Time off ({{ ( isset($timeoffTypeName) ? $timeoffTypeName : '' ) }}) request has been sent. Kindly wait for approval.</p>
			<?php if( isset($backDuration) && (!empty($backDuration)) ) { ?>
				<p>Time Back Details : {{ $backDuration }}</p>
			<?php } ?>
			
		@endif

		@if( isset($supervisorMail) && ($supervisorMail != false ) )
        	Regards,<br>
			{{ ( isset($employeeName) ? $employeeName : '' ) }}
        @endif
    </div>
 </div>
 @endsection