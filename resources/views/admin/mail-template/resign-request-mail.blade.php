
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($supervisorName) ? $supervisorName : '' ) }},</strong></div>
			<p>Your employee {{ $employeeName }} - {{ $employeeCode }} has resigned from the post.</p>
			<p style="margin-bottom:10px">Kindly check the details and update staff.</p>
		@else 
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;margin-top:12px"> Dear {{ ( isset($employeeName)  ? $employeeName : '' ) }},</strong></div>
			<p>Your resignation mail has been sent to your superior.</p>
			<p>Kindly discuss it with your superior too.</p>
		@endif

		<?php /* ?>
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
        	Thanks,<br>
			{{ ( isset($employeeName) ? $employeeName : '' ) }}
        @endif
        <?php */ ?>
    </div>
 </div>
 @endsection