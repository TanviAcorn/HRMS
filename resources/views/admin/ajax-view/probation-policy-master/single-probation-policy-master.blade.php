<?php
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
?>
<td class="text-center sr-col">{{ $rowIndex}}</td>
<td>{{ (!empty($recordDetail->v_probation_policy_name) ? $recordDetail->v_probation_policy_name :'') }}</td>
<td>{{ (!empty($recordDetail->v_probation_policy_description) ? $recordDetail->v_probation_policy_description :'') }}</td>
<td class="text-left">{{ (!empty($recordDetail->v_probation_period_duration) ? $recordDetail->v_probation_period_duration .'  '.(!empty($recordDetail->e_months_weeks_days) ? $recordDetail->e_months_weeks_days : '') :'') }}</td>
<td class="text-center status-update">{{ ( ((!empty($recordDetail->t_is_active)) && ( $recordDetail->t_is_active == 1 )) ? trans('messages.active') : trans('messages.inactive') )}}</td>
<?php 
$recordStatus = (!empty($recordDetail->e_record_status) ? $recordDetail->e_record_status :'' );
$permissionFound = false;
if($recordStatus == config('constants.PROBATION_POLICY')){
	$moduleName = 'probation-policy-master';
} else {
	$moduleName = 'notice-period-policy-master';
	
}?>
<td class="actions-button">
	@if(isset($recordStatus) && (($recordStatus == config('constants.NOTICE_PERIOD_POLICY') && checkPermission('edit_notice_period_policy_master') != false) || ($recordStatus == config('constants.PROBATION_POLICY') && checkPermission('edit_probation_policy_master') != false)))
	@php $permissionFound = true; @endphp
	<button title="{{ trans('messages.edit') }}"  data-record-type="{{(!empty($recordStatus) ? $recordStatus :'' )}}"  class="btn btn-sm mb-1 btn-edit btn-color-text" onclick="editProbationPolicyModel(this)" data-record-id="<?php echo $encodeRecordId?>"><i class="fas fa-pencil-alt"></i></button>
	@endif
	@if(isset($recordStatus) && (($recordStatus == config('constants.NOTICE_PERIOD_POLICY') && checkPermission('delete_notice_period_policy_master') != false) || ($recordStatus == config('constants.PROBATION_POLICY') && checkPermission('delete_probation_policy_master') != false)))
	@php $permissionFound = true; @endphp
	<button title="{{ trans('messages.delete') }}" data-record-id="{{ $encodeRecordId }}" data-module-name="{{ $moduleName }}" onclick="deleteRecord(this);" class="btn btn-sm mb-1 btn-delete btn-color-text"><i class="fa fa-trash"></i></button>
	@endif
	@if(isset($recordStatus) && (($recordStatus == config('constants.NOTICE_PERIOD_POLICY') && checkPermission('edit_notice_period_policy_master') != false) || ($recordStatus == config('constants.PROBATION_POLICY') && checkPermission('edit_probation_policy_master') != false)))
	@php $permissionFound = true; @endphp
 	<button title="{{ (int) ($recordDetail->t_is_active == 1) ? trans('messages.active') : trans('messages.inactive') }}" onclick='updateMasterStatusRecord(this,"{{ $moduleName }}")' data-module-name='{{ $moduleName }}' data-record-id="{{ $encodeRecordId }}" class="btn btn-sm mb-1 btn-active btn-color-text"><i class="{{ (int) ($recordDetail->t_is_active == 1) ? 'fa fa-eye-slash' :'fa fa-eye'}}"></i></button>
	@endif
</td>