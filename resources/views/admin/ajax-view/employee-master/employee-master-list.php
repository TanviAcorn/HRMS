<?php 
if(count($recordDetails) > 0 ){
	foreach ($recordDetails as $recordDetail){
		$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
		?>
		<tr>
		 	<td class="text-center">1</td>
         	<td><a href="javascript:void(0)"> A23324345</a></td>
           	<td class="text-left"><a href="javascript:vooid(0)"> Lorem Ipsum is simply.</a></td>
            <td class="text-left">Male <br> A+ </td>
            <td class="text-left">25-11-2022</td>
            <td class="text-left">Lorem Ipsum <br> Lorem, ipsum </td>
            <td class="text-left">Lorem Ipsum </td>
            <td class="text-left">Lorem Ipsum <br> Lorem, ipsum. </td>
            <td class="text-left">09:00 to 06:00 AM</td>
            <td class="text-left">9999999999 <br> demo123@gmail.com</td>
            <td class="text-left">Confirmed</td>
            <td class="text-left">
            	<div class="custom-control custom-switch">
             		<input type="checkbox" class="custom-control-input" id="customSwitch1">
                  	<label class="custom-control-label" for="customSwitch1">{{ trans("messages.enable") }}</label>
            	</div>
            </td>
            <td class="actions-button">
            	<a title="{{ trans('messages.edit') }}" href="javascript:void(0);" class="btn btn-sm mb-1 btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></a>
            </td>                                       
                                              
        </tr>                       
		<?php 
		
	}
} else {
	?>
	<tr>
		<td colspan="13" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
	<?php 
}

?>
