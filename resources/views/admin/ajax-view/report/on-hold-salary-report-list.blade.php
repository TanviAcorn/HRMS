@if(count($recordDetails) > 0 )
	@php $index = ($pageNo - 1) * $perPageRecord ; @endphp
	@foreach($recordDetails as $recordDetail)
		<tr class="text-left">
			<?php 
			$recordInfo = [];
			$recordInfo['rowIndex'] = ++$index;
			$recordInfo['recordDetail'] = $recordDetail;
			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/single-on-hold-salary-report')->with ( $recordInfo )->render();
			echo $html;
			?>
        </tr>
	@endforeach
@else
 	<tr>
		<td colspan="15" class="text-center">@lang('messages.no-record-found')</td>
	</tr>
@endif
@include('admin/common-display-count')
							
                           