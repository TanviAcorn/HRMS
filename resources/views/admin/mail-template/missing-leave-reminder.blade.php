
 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
		<div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ (isset($employeeName) ? $employeeName : '' ) }} - {{ (isset($employeeCode) ? $employeeCode : '' ) }},</strong></div>
		<br>
		<p style="margin:0">Hope you are doing well. Could you please send your leave applications for below dates.</p>
		<br>
		<?php $index = 1 ;?>
		<div style="color: #000; background-color:#fff; width: 550px !important; margin: 0 auto;">
		     <div>
		         <table style="width:100%;border:0;overflow:wrap;vertical-align: top;height:100%" border="0" cellpadding="0" cellspacing="0">
		             <tr>
		                 <th style="padding:6px 12px;border:1px solid #DDDDDD;text-align:left;width:60px">{{ trans('messages.sr-no') }}</th>
		                 <th style="padding:6px 12px;border:1px solid #DDDDDD;text-align:left">{{ trans('messages.date') }}</th>
		             </tr>
		             
					 @if(count($missingLeaveDates) > 0)
						@foreach($missingLeaveDates as $missingLeaveDate)
								<tr>
									<td style="padding:6px 12px;border-width: 1px 1px 1px 1px;border-style: solid;border-color: #DDDDDD;color:#000;"><?php echo $index ?></td>
									<td style="padding:6px 12px;border-width: 1px 1px 1px 1px;border-style: solid;border-color: #DDDDDD;color:#000;"><?php echo convertDateFormat($missingLeaveDate) . ' (' . convertDateFormat($missingLeaveDate,'l') . ')' ?></td>
								</tr>
							<?php $index++; ?>
						@endforeach
					@endif
		         </table>
		     </div>
		 </div>
		<?php /*@if(count($missingLeaveDates) > 0)
			@foreach($missingLeaveDates as $missingLeaveDate)
				<?php echo $index .'. '.convertDateFormat($missingLeaveDate) . ' (' . convertDateFormat($missingLeaveDate,'l') . ')' ?><?php echo '<br>'?>
				<?php $index++ ?>
			@endforeach
		@endif	*/ ?>
	</div>
 </div>
 @endsection