<?php
if(count($employeeDocumentRecordDetails) > 0 ){
	$index = 1;
	foreach ($employeeDocumentRecordDetails as $employeeDocumentRecordDetail){
		$encodeRecordId = Wild_tiger::encode($employeeDocumentRecordDetail->i_id);
		$documentFileName = "";
		if (!empty($employeeDocumentRecordDetail) && file_exists(config('constants.FILE_STORAGE_PATH').config('constants.UPLOAD_FOLDER') . $employeeDocumentRecordDetail->v_document_file)) {
			$documentFileName =  config('constants.FILE_STORAGE_PATH_URL') .  config('constants.UPLOAD_FOLDER') .  $employeeDocumentRecordDetail->v_document_file;
		}
		?>
		<tr>
			<td class="text-center sr-index">{{ $index++ }}</td>
			<td class="text-center document-file-row">{{ (!empty($documentFileName) ? basename($documentFileName) :'')}}</td>
			<td class="text-center">{{ (!empty($employeeDocumentRecordDetail->dt_created_at) ? clientDateTime($employeeDocumentRecordDetail->dt_created_at):'')}}</td>
			<td class="text-center">{{ (!empty($employeeDocumentRecordDetail->v_remark) ? $employeeDocumentRecordDetail->v_remark :'')}}</td>
			<td class="">
				<div class="download-link-items d-flex justify-content-center">
					<a href="{{ $documentFileName }}" class="btn btn btn-theme text-white border btn-sm  manage-doc-btn mr-2 ml-2  d-sm-flex align-items-center" title="{{ trans('messages.view') }}" target="_blank">{{ trans("messages.view") }} </a>
					<a href="{{ $documentFileName }}" class="btn btn btn-theme text-white border btn-sm d-sm-flex mr-2 align-items-center manage-doc-btn upload-btn" title="{{ trans('messages.download') }}" target="_blank" download>{{ trans("messages.download") }} </a>
					@if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ||  ( ( session()->get('role') == config('constants.ROLE_USER') ) &&  ( isset($employeeDocumentRecordDetail->documentType->e_modifiable_employee) ) && (  $employeeDocumentRecordDetail->documentType->e_modifiable_employee == config('constants.SELECTION_YES') )  )  )
						<a href="javascript:void(0);" class="btn btn bg-danger text-white border btn-sm d-sm-flex mr-2 align-items-center manage-doc-btn upload-btn" data-module-name="employee-master" data-record-id="{{ $encodeRecordId }}" title="{{ trans('messages.delete') }}" onclick="documentDeleteRecordInfo(this);" >{{ trans("messages.delete") }} </a>
					@endif
				</div>
			</td>
		</tr>
		<?php 
	}
} else{
	?>
	<tr>
		 <td colspan="5" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
	<?php 
}