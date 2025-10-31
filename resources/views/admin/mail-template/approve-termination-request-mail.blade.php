
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		@if( isset($supervisorMail) && ($supervisorMail != false ) )
			<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($supervisorName) ? $supervisorName : '' ) }},</strong></div>
		@else
			
		@endif
		
		@if( isset($actionStatus) && ( in_array( $actionStatus , [ config('constants.REJECTED_STATUS') , config('constants.CANCELLED_STATUS')  ]  ) ) )
			<p style="margin-bottom:0">{{ isset($actionTakenByName) ? $actionTakenByName : 'You' }} {{ isset($userNameVerb) ? $userNameVerb : 'has'  }} {{ ucwords( $recordStatus) }} termination, kindly short out the issue.</p>
			<br>
		@else
			<p style="margin-bottom:0">{{ isset($actionTakenByName) ? $actionTakenByName : 'You' }} {{ isset($userNameVerb) ? $userNameVerb : 'has'  }} terminated one staff member. </p>
			<br>
		@endif
	</div>
 </div>
 @endsection