
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
			<p style="margin-bottom:0">{{ isset($actionTakenByName) ? $actionTakenByName : 'You' }} {{ isset($userNameVerb) ? $userNameVerb : 'has'  }} {{ ($leaveStatus) }} Leave of {{ (isset($employeeName) ? $employeeName : '' ) }} - {{ (isset($employeeCode) ? $employeeCode : '' ) }}  For Dates {{ isset($leaveDurationText) ? $leaveDurationText : 'from' }} {{ ( isset($leaveDuration)  ? $leaveDuration : '' ) }} </p>
			<br>
		@else
			<p>Your leave(s) is (are) {{ strtoupper($leaveStatus) }}.</p>
		@endif
		
		@if( isset($supervisorMail) && ($supervisorMail != false ) )	
		
		@else
			@if( isset($leaveStatus) && ($leaveStatus == config('constants.REJECTED_STATUS')) )
				<p>Kindly meet us for the reason if you are not aware.<p>
			@endif
		@endif	
		
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			
			Regards,<br>
			{{ ( isset($actionTakeName) ? $actionTakeName : ( isset($supervisorName) ? $supervisorName : '' )  ) }}
			
		@else
			@if( isset($leaveStatus) && ($leaveStatus != config('constants.CANCELLED_STATUS')) )
			Regards,<br>
			{{ ( isset($actionTakeName) ? $actionTakeName : ( isset($supervisorName) ? $supervisorName : '' )  ) }}
			@endif
		@endif
		
    </div>
 </div>
 @endsection