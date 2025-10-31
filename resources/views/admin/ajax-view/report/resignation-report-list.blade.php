@if(count($recordDetails) > 0 )
	@php $index = ($page_no - 1) * $perPageRecord ; @endphp
	@foreach($recordDetails as $recordDetail)
	<tr class="resignation-record">
		<?php 
			$recordInfo = [];
			$recordInfo['rowIndex'] = ++$index;
			$recordInfo['recordDetail'] = $recordDetail;
			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'report/single-resignation-report')->with ( $recordInfo )->render();
			echo $html;
			?>
	</tr>
 	@endforeach
	@if(!empty($pagination))
 	    <input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
 		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
 		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
 	@endif
@else
 	<tr>
		<td colspan="12" class="text-center">@lang('messages.no-record-found')</td>
	</tr>
@endif
@include('admin/common-display-count')						
 													
							
		