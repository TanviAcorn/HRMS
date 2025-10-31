	<?php 
		$encodeRecordId = (!empty($recordDetail) && $recordDetail['i_id'] ? Wild_tiger::encode($recordDetail['i_id']) : 0);
		//$employeeValue = (isset($recordDetail['employee']) ? array_column(objectToArray($recordDetail['employee']), 'v_employee_full_name') : []);
		//$employeeName = (!empty($employeeValue) ? implode(', ', $employeeValue) : '');
		$employeeInfo = (isset($recordDetail['employee']) ? objectToArray($recordDetail['employee']) : [] );
		$remarks = (!empty($recordDetail['v_remarks']) ? $recordDetail['v_remarks'] :'');
		$permissionFound = false;
		$reportNumber = (!empty($recordDetail['v_report_no']) ? $recordDetail['v_report_no'] :'');
	?>
	<td class="text-center sr-col">{{ $rowIndex }}</td>
	<td>{{ (!empty($recordDetail['v_report_no']) ? $recordDetail['v_report_no'] : '') }}</td>
	<td class="text-left">
		@if(!empty($employeeInfo))
			@foreach ($employeeInfo as $employee)
				@php
					$encodeEmployeeId = (!empty($employee['i_id']) ? Wild_tiger::encode($employee['i_id']) : 0);
				@endphp
			<a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}" target="_blank" title="{{ trans('messages.view-profile')}}">{{ ( !empty($employee['v_employee_full_name']) ? $employee['v_employee_full_name'] . ' ('. (  !empty($employee['v_employee_code']) ? $employee['v_employee_code'] : ''  ) . (  isset($employee['team_info']) &&  !empty($employee['team_info']['v_value']) ? ' - ' . $employee['team_info']['v_value'] : ''   ) .')' . (!$loop->last ? ', '  : '')  : '') }}</a>
			@endforeach
		@endif
	</td>
	<td class="text-left">{{ (!empty($recordDetail['v_subject']) ? $recordDetail['v_subject'] : '') }}</td>
	<td>{{ (!empty($recordDetail['dt_report_date']) ? clientDate($recordDetail['dt_report_date']) : '') }}</td>
	<td>{{ (!empty($recordDetail['dt_close_date']) ? clientDate($recordDetail['dt_close_date']) : '') }}</td>
	<td class="text-center record-status">{{ (!empty($recordDetail['e_status'])  && ( $recordDetail['e_status'] == config('constants.OPEN')) ? config('constants.OPEN') : config('constants.CLOSE') ) }}
	
	</td>
	<td class="actions-button">
		@if(!empty($recordDetail['e_status'])  &&  $recordDetail['e_status'] == config('constants.CLOSE'))
			<button type="button" data-incident-id="{{ $encodeRecordId }}" data-incident-report-no="{{ (!empty($recordDetail['v_report_no']) ? $recordDetail['v_report_no'] : '') }}" onclick="getCloseModelInfo(this)" class=" btn btn-color-text btn-smmanage-doc-btn btn-sm mb-1"  title="{{ trans('messages.view') }}"><i class="fa fa-eye"></i></button>
			@php $permissionFound = true; @endphp
		@endif
		@if(checkPermission('edit_incident_report') != false)
		<a title="{{ trans('messages.edit') }}" href="{{ config('constants.INCIDENT_REPORT_URL') . '/showEditForm/' . $encodeRecordId }}" class="btn btn-sm mb-1 btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></a>
		@php $permissionFound = true; @endphp
		@endif
		@if(checkPermission('delete_incident_report') != false)
		<button title="{{ trans('messages.delete') }}" class="btn btn-sm mb-1 btn-delete btn-color-text" data-module-name="{{ config('constants.INCIDENT_REPORT_MODULE') }}" data-record-id="{{ $encodeRecordId }}" onclick="deleteRecord(this);"><i class="fa fa-trash"></i></button>
		@php $permissionFound = true; @endphp
		@endif
		@if(checkPermission('edit_incident_report') != false)
		<?php if (!empty($recordDetail->e_status) && $recordDetail->e_status == config('constants.OPEN')){ ?>
		@php $permissionFound = true; @endphp
		<button title="{{ trans('messages.close') }}" class="btn btn-sm mb-1 btn-active btn-color-text"  data-report-number="{{ $reportNumber }}" data-record-id="{{ $encodeRecordId }}" data-current-date="{{ config('constants.CURRENT_DATE') }}" data-report-date="{{ (!empty($recordDetail['dt_report_date']) ? clientDate($recordDetail['dt_report_date']) : '') }}" onclick="openCloseModel(this)"><i class="fa fa-times"></i></button>
		<?php } ?>
		@endif
	</td>