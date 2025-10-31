<?php $encodeRecordId = Wild_tiger::encode($recordDetail->i_id);?>
<td class="text-center sr-col">{{ $rowIndex }}</td>
<td>{{ (!empty($recordDetail->v_component_name) ? $recordDetail->v_component_name :'') }}</td>
<td class="text-left">{{ (!empty($recordDetail->v_component_description) ? $recordDetail->v_component_description : null) }}</td> 
<td>{{ (!empty($recordDetail->e_salary_components_type) ? $recordDetail->e_salary_components_type :'') }}</td>
<td>{{ (!empty($recordDetail->e_consider_for_pf_calculation) ? $recordDetail->e_consider_for_pf_calculation :'') }}</td> 
<?php /*?><td>{{ (!empty($recordDetail->e_salary_components_frequence) ? $recordDetail->e_salary_components_frequence :'') }}</td> <?php */?>
<td class="text-center status-update">{{ ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans('messages.active') : trans('messages.inactive') )}}</td>
@if((checkPermission('edit_salary_components') != false) || (checkPermission('delete_salary_components') != false))                               
<td class="actions-button">
   	
		@if(checkPermission('edit_salary_components') != false)
	   	<button title="{{ trans('messages.edit') }}" onclick="editSalaryComponentsModel(this)" data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></button>
		@endif
		@if( !in_array( $recordDetail->i_id , [ config('constants.PF_SALARY_COMPONENT_ID'), config('constants.BASIC_SALARY_COMPONENT_ID') , config('constants.HRA_SALARY_COMPONENT_ID'), config('constants.PT_SALARY_COMPONENT_ID') , config('constants.ON_HOLD_SALARY_COMPONENT_ID')  ] ) )
			@if(checkPermission('delete_salary_components') != false)
		 	<button title="{{ trans('messages.delete') }}" data-record-id="{{ $encodeRecordId }}" data-module-name="salary-components" onclick="deleteRecord(this);"  class="btn btn-sm mb-1 btn-delete btn-color-text"><i class="fa fa-trash"></i></button>
			@endif
		@endif
 	
	@if(checkPermission('edit_salary_components') != false)
 	<button title="{{ (int) ($recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') }}" onclick='updateMasterStatusRecord(this,"salary-components")' data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-active btn-color-text"><i class="{{ (int) ($recordDetail->t_is_active == 1) ? 'fa fa-eye-slash' :'fa fa-eye'}}"></i></button>
	@endif
</td>
@endif                                 
                                                                                                  
                               
                               
                           