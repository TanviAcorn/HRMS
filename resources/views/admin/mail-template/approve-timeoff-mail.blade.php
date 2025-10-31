
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($supervisorName) ? $supervisorName : '' ) }},</strong></div>
		@else
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($employeeName) ? $employeeName : '' ) }},</strong></div>
			<p>Thank you for your mail.</p>
		@endif
		
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			<p>{{ isset($actionTakenByName) ? $actionTakenByName : 'You' }} {{ isset($userNameVerb) ? $userNameVerb : 'has'  }} {{ ($recordStatus) }} Time Off({{ ( isset($timeoffTypeName) ? $timeoffTypeName : '' ) }}) of {{ (isset($employeeName) ? $employeeName : '' ) }} - {{ (isset($employeeCode) ? $employeeCode : '' ) }}  For Date on  {{ ( isset($timeOffDate)  ? convertDateFormat ( $timeOffDate ) : '' ) }} </p>
		@else
			<p>Your Time Off ({{ ( isset($timeoffTypeName) ? $timeoffTypeName : '' ) }}) request is {{ strtoupper($recordStatus) }}. </p>
		@endif
		<br>
		@if( isset($supervisorMail) && ($supervisorMail != false ) )	
		
		@else
			@if( isset($leaveStatus) && ($leaveStatus == config('constants.REJECTED_STATUS')) )
			<p>Kindly meet us for the reason if you are not aware.<p>
			<br>
			@endif
		@endif	
		
		
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			@if( isset($recordStatus) && ($recordStatus != config('constants.CANCELLED_STATUS')) )
			Regards,<br>
			{{ ( isset($actionTakeName) ? $actionTakeName : ( isset($supervisorName) ? $supervisorName : '' )  ) }}
			@endif
		@else
			Regards,<br>
			{{ ( isset($actionTakeName) ? $actionTakeName : ( isset($supervisorName) ? $supervisorName : '' )  ) }}
		@endif
		
    </div>
 </div>
 @endsection