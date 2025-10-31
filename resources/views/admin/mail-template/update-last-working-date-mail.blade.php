
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($supervisorName) ? $supervisorName : '' ) }},</strong></div>
			<p>Mail sent to employee with updated last working date which is {{ ( isset($lastWorkingDate) ? convertDateFormat($lastWorkingDate) : '' )  }}.</p>
			<p>Kindly discuss with staff too.</p>
			<br>
		@else 
			<div style="padding-top:10px"><strong style="font-size: 13px;text-align: justify;color: #202020;margin-top:12px"> Dear {{ ( isset($employeeName)  ? $employeeName : '' ) }},</strong></div>
			<p>Your last working date is {{ ( isset($lastWorkingDate) ? convertDateFormat($lastWorkingDate) : '' )  }} updated by {{ ( isset($actionTakenByName) ? $actionTakenByName : (isset($supervisorName) ? $supervisorName : '' ) ) }}</p>
			<p>Kindly check carefully and meet superior on any query.</p>
			<br>
		@endif

		@if( isset($supervisorMail) && ($supervisorMail != false ) )
        	Regards,<br>
			{{ ( isset($actionTakenByName) ? $actionTakenByName : (isset($supervisorName) ? $supervisorName : '' ) ) }}
        @else
			Regards,<br>
			{{ ( isset($actionTakenByName) ? $actionTakenByName : (isset($supervisorName) ? $supervisorName : '' ) ) }}
        @endif
    </div>
 </div>
 @endsection