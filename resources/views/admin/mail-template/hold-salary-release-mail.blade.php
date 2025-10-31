
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ config('constants.SYSTEM_ADMIN_NAME') }},</strong></div>
		<p style="margin-bottom:0">I hope you are doing well. This mail is to remind you that the employee ( {{ (isset($employeeName) ? $employeeName : '' ) }} - {{ (isset($employeeCode) ? $employeeCode : '' ) }} ) is going to get his/her hold AMOUNT back.</p>
		<p style="margin-bottom:0">Kindly proceed in the upcoming pay.</p>
	</div>
 </div>
 @endsection