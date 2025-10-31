<?php 
if(count($recordDetails) > 0){
	$index= ($page_no - 1) * $perPageRecord;
	foreach ($recordDetails as $recordDetail){
		?>
		<tr class="probation-policy-record">
			<?php 
			$recordInfo = [];
			$recordInfo['rowIndex'] = ++$index;
			$recordInfo['recordDetail'] =$recordDetail;
			$html = view (config('constants.AJAX_VIEW_FOLDER') . 'probation-policy-master/single-probation-policy-master')->with ( $recordInfo )->render();
			echo $html;
			?>
		</tr>
		<?php 
	}
	if(!empty($pagination)){?>
		<input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
		<?php 
	}
} else {
	?>
	<tr>
		<td colspan="6" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
	<?php 
}
?>
@include('admin/common-display-count')	