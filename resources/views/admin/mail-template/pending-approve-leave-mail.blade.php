
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($employeeName) ? $employeeName : '' ) }} - {{ (isset($employeeCode) ? $employeeCode : '' ) }},</strong></div>
		<br>
		<p style="margin:0">Hope you are doing well. Kindly approve below leave applications.</p>
		<br>
		<?php $index = 1 ;?>
		<div style="color: #000; background-color:#fff; width: 550px !important; margin: 0 auto;">
		     <div>
		         <table style="width:100%;border:0;overflow:wrap;vertical-align: top;height:100%" border="0" cellpadding="0" cellspacing="0">
		             <tr>
		                 <th style="padding:6px 12px;border:1px solid #DDDDDD;text-align:left">{{ trans('messages.sr-no') }}</th>
		                 <th style="padding:6px 12px;border:1px solid #DDDDDD;text-align:left">{{ trans('messages.employee-name') }} ({{ trans('messages.employee-code') }})</th>
		                 <th style="padding:6px 12px;border:1px solid #DDDDDD;text-align:left">{{ trans('messages.date') }}</th>
		             </tr>
		             
		             @if(count($pendingApproveLeaveDates) > 0)
						@foreach($pendingApproveLeaveDates as $pendingApproveLeaveDate)
							<?php 
							$employeeName = ( isset($pendingApproveLeaveDate['employeeName']) ? $pendingApproveLeaveDate['employeeName'] : "" ) ;;
							$employeeCode = ( isset($pendingApproveLeaveDate['employeeCode']) ? $pendingApproveLeaveDate['employeeCode'] : "" ) ;;
							$leaveDates = ( isset($pendingApproveLeaveDate['pendingApproveLeaveDates']) ? $pendingApproveLeaveDate['pendingApproveLeaveDates'] : "" ) ;;
							if(!empty($leaveDates)){
								foreach($leaveDates as $leaveDate){
									?>
									<tr>
						                 <td style="padding:6px 12px;border-width: 1px 1px 1px 1px;border-style: solid;border-color: #DDDDDD;color:#000;">{{ $index }}</td>
						                 <td style="padding:6px 12px;border-width: 1px 1px 1px 1px;border-style: solid;border-color: #DDDDDD;color:#000;">{{ $employeeName }} - {{ $employeeCode }}</td>
						                 <td style="padding:6px 12px;border-width: 1px 1px 1px 1px;border-style: solid;border-color: #DDDDDD;color:#000;">{{ convertDateFormat($leaveDate) . ' (' . convertDateFormat($leaveDate,'l') . ')' }}</td>
						             </tr>
									<?php
									$index++;
								}
							}
							?>
						@endforeach
					@endif
		             
		              
		             
		         </table>
		     </div>
		 </div>
	</div>
 </div>
 @endsection