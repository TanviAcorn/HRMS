
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($supervisorName) ? $supervisorName : '' ) }},</strong></div>
		@else
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($employeeName) ? $employeeName : '' ) }},</strong></div>
		@endif
		
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			@if( isset($actionStatus) && ( in_array( $actionStatus , [ config('constants.REJECTED_STATUS') , config('constants.CANCELLED_STATUS')  ]  ) ) )
				@if( $actionStatus == config('constants.CANCELLED_STATUS')  )
					<p>{{ isset($actionDoneByName) ? $actionDoneByName : 'You' }} {{ isset($userNameVerb) ? $userNameVerb : 'has'  }} cancelled  {{ (isset($employeeName) ? $employeeName : '' ) }}  - {{ (isset($employeeCode) ? $employeeCode : '' ) }}'s resignation.</p>
					<p>Kindly discuss it with the employee.</p>
				@else
					<p style="margin-bottom:0">{{ isset($actionDoneByName) ? $actionDoneByName : 'You' }} {{ isset($userNameVerb) ? $userNameVerb : 'has'  }} {{ ucwords( $recordStatus) }} resignation, kindly short out the issue.</p>
					<br>
				@endif
			@else
				<p>{{ isset($actionDoneByName) ? $actionDoneByName : 'You' }} {{ isset($userNameVerb) ? $userNameVerb : 'has'  }} accepted one resignation with last working date {{ ( isset($lastWorkingDate) ? convertDateFormat($lastWorkingDate) : '' )  }}.</p>
				<br>
			@endif
			
			
		@else
			@if( isset($actionStatus) && ( in_array( $actionStatus , [ config('constants.REJECTED_STATUS') , config('constants.CANCELLED_STATUS')  ]  ) ) )
				@if( $actionStatus == config('constants.CANCELLED_STATUS')  )
					<p>Your resignation has been cancelled.</p>
					<p>Kindly contact your superior or HR within two days if it is not discussed!</p>
					Regards,<br>
					{{ ( isset($actionTakenByName) ? $actionTakenByName : '' ) }}
				@else
					<p>Your resignation has been {{ $recordStatus }}.</p>
					<p>Kindly meet your leader.</p>
					<br>
				@endif
			@else
				<p>Your resignation has been accepted and your last working date shall be {{ ( isset($lastWorkingDate) ? convertDateFormat($lastWorkingDate) : '' )  }}</p>
				<p>Thank you for everything.</p>
			@endif
			
		@endif
		
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			Regards,<br>
			{{ ( isset($actionTakenByName) ? $actionTakenByName : ( isset($employeeName) ? $employeeName : '' ) ) }}
		@else
			@if(  in_array( $actionStatus , [ config('constants.REJECTED_STATUS') , config('constants.APPROVED_STATUS') ] ) )
			Regards,<br>
			{{ ( isset($actionTakenByName) ? $actionTakenByName : '' ) }}
			@endif	
		@endif
		
    </div>
 </div>
 @endsection