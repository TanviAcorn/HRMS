
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($supervisorName) ? $supervisorName : '' ) }},</strong></div>
			<?php /* ?>
			@if( strtotime($leaveStartDate) > strtotime("now") )
				<p>I am going to apply for leave(s) {{ isset($leaveDurationText) ? $leaveDurationText : 'from' }} {{ ( isset($leaveDuration)  ? $leaveDuration : '' ) }} as discussed with you earlier.</p>
			@else
				<p>I had taken leave(s) {{ isset($leaveDurationText) ? $leaveDurationText : 'from' }} <strong> {{ ( isset($leaveDuration)  ? $leaveDuration : '' ) }}<strong> as discussed with you.</p>	
			@endif
			<?php */ ?>
			<p>Your Employee {{ ( isset($employeeName)  ? $employeeName : '' ) }} - {{ ( isset($employeeCode)  ? $employeeCode : '' ) }} has sent a Leave request {{ isset($leaveDurationText) ? $leaveDurationText : 'from' }} ({{ ( isset($leaveDuration)  ? $leaveDuration : '' ) }}).</p>
			<p style="margin:0;">Kindly approve or deny before time lapse.</p>	
			
		@else 
			<div>
				<strong style="font-size: 13px;text-align: justify;color: #202020;margin-top:12px"> Dear {{ ( isset($employeeName)  ? $employeeName : '' ) }},</strong>
			</div>
			<p>Your leave request has been sent. Kindly wait for approval.</p>
			<p style="margin:0;">(Taking leave without prior approval will lead to disciplinary action)</p>
			<br>
		@endif

		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			<?php /* ?>
        	<p style="font-weight:light; margin:0; font-size: 14px; color: #383838;">Kindly approve my leave.</p>
        	<p style="font-weight:light; margin:0; font-size: 14px; color: #383838;">I know that taking leave without prior approval will lead to disciplinary action.</p>
        	<?php */ ?>
        	<br>
        	Regards,<br>
			{{ ( isset($employeeName) ? $employeeName : '' ) }}
		@else
			Regards,<br>
			{{ (  ( isset($supervisorName) && (!empty($supervisorName)) ) ? $supervisorName : 'HR team' ) }}
		@endif
    </div>
 </div>
 @endsection