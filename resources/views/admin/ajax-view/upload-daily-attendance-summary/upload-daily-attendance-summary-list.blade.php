
@if( count($allDates) )
 	@php 
 	$index= ($page_no - 1) * $perPageRecord; 
 	$rowIndex = 0;
 	@endphp
 	@foreach ($allDates as $allDate)
 		@php
 			$status = trans('messages.no'); 
 			if( in_array( $allDate , $uploadSheetDates ) ){
 				$status = '<a href="'.config('constants.UPLOAD_DAILY_ATTENDANCE_URL') . '/' . $allDate.'" target="_blank" title="'.trans('messages.view-attendance-data').'">';
 				$status .= trans('messages.yes');
 				$status .= '</a>';
 			}
 		@endphp
 		<tr class="text-left">
 			<td class="text-center sr-col">{{ ++$rowIndex }}</td>
			<td>{{ convertDateFormat( $allDate ) }}</td>
			<td>{!! $status !!}</td>
			@if(checkPermission('add_uploaded_attendance_summary') != false)
				<td>
					<div class="">
						<button type="button" class="btn btn-sm btn-primary" onclick="openDailyAttendanceUploadModal(this);"  data-date="{{ clientDate( $allDate ) }}" title="{{ trans('messages.upload-daily-attendance') }}"><i class="fa fa-upload"></i></button>
					</div>
				</td>
			@endif
		</tr>
 	@endforeach
 @else
 	<tr>
		<td colspan="9" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
 @endif
 @include('admin/common-display-count')  
                                
                                
                            