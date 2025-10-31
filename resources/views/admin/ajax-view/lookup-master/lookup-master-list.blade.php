@php $index =  ( $page_no - 1 ) * $perPageRecord @endphp
@if(count($recordDetails) > 0 )
	
	@foreach ($recordDetails as $key => $recordDetail) 
		<?php $encodeRecordId = Wild_tiger::encode($recordDetail->i_id) ?> 
		<tr class="has-record">
          <td class='text-center sr-col'>{{ ++$index }}</td>
          <td class="text-left">{{  ( (!empty($recordDetail->v_value)) ? $recordDetail->v_value : '' )   }}</td>
          <td class="status-update">{{ ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans('messages.active') : trans('messages.inactive') )}}</td>
          <?php /*?>
          <td>
          	<?php 
          	$checked = '';
          	if($recordDetail->t_is_active == 1){
          		$checked = 'checked="checked"';
          	}
          	?>
          	<div class="custom-control custom-switch status-class">
				<input type="checkbox" class="custom-control-input" <?php echo $checked ?> data-record-id="{{ $encodeRecordId }} " id="disable_{{ $key }}" data-another-module-name="{{ Wild_tiger::enumText($recordDetail->v_module_name) }}" onclick="updateRecordStatus(this,'{{ config('constants.LOOKUP_MODULE') }}')">
				<label class="custom-control-label record-status" for="disable_{{ $key }}">{{ (!empty($recordDetail->t_is_active == 1) ? trans("messages.enable") : trans("messages.disable") )}}</label>
			</div>
          </td>
          */?>
          @if(isset($moduleName) && ((($moduleName == config('constants.TEAM_LOOKUP') && checkPermission('edit_team_master') != false) || ($moduleName == config('constants.DESIGNATION_LOOKUP') && checkPermission('edit_designation_master') != false) || ($moduleName == config('constants.RECRUITMENT_SOURCE_LOOKUP') && checkPermission('edit_recruitment_source_master') != false) || ($moduleName == config('constants.TERMINATION_REASONS_LOOKUP') && checkPermission('edit_termination_reasons') != false) || ($moduleName == config('constants.RESIGN_REASONS_LOOKUP') && checkPermission('edit_resign_reasons') != false) || ($moduleName == config('constants.BANK_LOOKUP') && checkPermission('edit_bank_master') != false)) || (($moduleName == config('constants.TEAM_LOOKUP') && checkPermission('delete_team_master') != false) || ($moduleName == config('constants.DESIGNATION_LOOKUP') && checkPermission('delete_designation_master') != false) || ($moduleName == config('constants.RECRUITMENT_SOURCE_LOOKUP') && checkPermission('delete_recruitment_source_master') != false) || ($moduleName == config('constants.TERMINATION_REASONS_LOOKUP') && checkPermission('delete_termination_reasons') != false) || ($moduleName == config('constants.RESIGN_REASONS_LOOKUP') && checkPermission('delete_resign_reasons') != false) || ($moduleName == config('constants.BANK_LOOKUP') && checkPermission('delete_bank_master') != false)) || !in_array($moduleName, [config('constants.TEAM_LOOKUP'), config('constants.DESIGNATION_LOOKUP'), config('constants.RECRUITMENT_SOURCE_LOOKUP'), config('constants.TERMINATION_REASONS_LOOKUP'), config('constants.RESIGN_REASONS_LOOKUP'), config('constants.BANK_LOOKUP')])))
          <td class="actions-button">
          		@if( !in_array( $recordDetail->i_id , [ config('constants.HDFC_BANK_ID') , config('constants.EMPLOYEE_RECRUITMENT_SOURCE_ID') ] ) )
				@if(isset($moduleName) && (($moduleName == config('constants.TEAM_LOOKUP') && checkPermission('edit_team_master') != false) || ($moduleName == config('constants.DESIGNATION_LOOKUP') && checkPermission('edit_designation_master') != false) || ($moduleName == config('constants.RECRUITMENT_SOURCE_LOOKUP') && checkPermission('edit_recruitment_source_master') != false) || ($moduleName == config('constants.TERMINATION_REASONS_LOOKUP') && checkPermission('edit_termination_reasons') != false) || ($moduleName == config('constants.RESIGN_REASONS_LOOKUP') && checkPermission('edit_resign_reasons') != false) || ($moduleName == config('constants.BANK_LOOKUP') && checkPermission('edit_bank_master') != false) || !in_array($moduleName, [config('constants.TEAM_LOOKUP'), config('constants.DESIGNATION_LOOKUP'), config('constants.RECRUITMENT_SOURCE_LOOKUP'), config('constants.TERMINATION_REASONS_LOOKUP'), config('constants.RESIGN_REASONS_LOOKUP'), config('constants.BANK_LOOKUP')])))
					<button title="{{ trans('messages.edit-record') }}" data-module-name="{{  $recordDetail->v_module_name }}" data-record-id="{{ $encodeRecordId }}" onclick="editLookupModal(this);" class="btn btn-sm mb-1 btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></button>
				@endif
				@if(isset($moduleName) && (($moduleName == config('constants.TEAM_LOOKUP') && checkPermission('delete_team_master') != false) || ($moduleName == config('constants.DESIGNATION_LOOKUP') && checkPermission('delete_designation_master') != false) || ($moduleName == config('constants.RECRUITMENT_SOURCE_LOOKUP') && checkPermission('delete_recruitment_source_master') != false) || ($moduleName == config('constants.TERMINATION_REASONS_LOOKUP') && checkPermission('delete_termination_reasons') != false) || ($moduleName == config('constants.RESIGN_REASONS_LOOKUP') && checkPermission('delete_resign_reasons') != false) || ($moduleName == config('constants.BANK_LOOKUP') && checkPermission('delete_bank_master') != false) || !in_array($moduleName, [config('constants.TEAM_LOOKUP'), config('constants.DESIGNATION_LOOKUP'), config('constants.RECRUITMENT_SOURCE_LOOKUP'), config('constants.TERMINATION_REASONS_LOOKUP'), config('constants.RESIGN_REASONS_LOOKUP'), config('constants.BANK_LOOKUP')])))
					<button title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="{{ config('constants.LOOKUP_MODULE') }}"  data-another-module-name="{{ Wild_tiger::enumText($recordDetail->v_module_name) }}" onclick="deleteRecord(this);" type="button" class="btn btn-sm mb-1 btn-delete btn-color-text"><i class="fas fa-trash"></i></button>
				@endif
				@endif
				@if(isset($moduleName) && (($moduleName == config('constants.TEAM_LOOKUP') && checkPermission('edit_team_master') != false) || ($moduleName == config('constants.DESIGNATION_LOOKUP') && checkPermission('edit_designation_master') != false) || ($moduleName == config('constants.RECRUITMENT_SOURCE_LOOKUP') && checkPermission('edit_recruitment_source_master') != false) || ($moduleName == config('constants.TERMINATION_REASONS_LOOKUP') && checkPermission('edit_termination_reasons') != false) || ($moduleName == config('constants.RESIGN_REASONS_LOOKUP') && checkPermission('edit_resign_reasons') != false) || ($moduleName == config('constants.BANK_LOOKUP') && checkPermission('edit_bank_master') != false) || !in_array($moduleName, [config('constants.TEAM_LOOKUP'), config('constants.DESIGNATION_LOOKUP'), config('constants.RECRUITMENT_SOURCE_LOOKUP'), config('constants.TERMINATION_REASONS_LOOKUP'), config('constants.RESIGN_REASONS_LOOKUP'), config('constants.BANK_LOOKUP')])))
					<button title="{{ (int) ($recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') }}" data-another-module-name="{{ Wild_tiger::enumText($recordDetail->v_module_name) }}" onclick='updateMasterStatusRecord(this,"{{ config("constants.LOOKUP_MODULE") }}")' data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-active btn-color-text"><i class="{{ (int) ($recordDetail->t_is_active == 1) ? 'fa fa-eye-slash' :'fa fa-eye'}}"></i></button>
				@endif
			
          </td>
          @endif
   		</tr>                                  
@endforeach
	@if(!empty($pagination))
		<input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
        <input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
        <input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
	@endif
@else
      <tr class="text-center"><td colspan="6">@lang('messages.no-record-found')</td></tr>        
@endif

@include('admin/common-display-count')										