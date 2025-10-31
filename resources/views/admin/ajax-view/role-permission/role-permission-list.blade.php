@if(count($recordDetails) > 0 )
 	@php $index = ($page_no - 1) * $perPageRecord ; @endphp
	@foreach($recordDetails as $recordDetail)
	@php $encodeRecordId = (isset($recordDetail->i_id) ? Wild_tiger::encode($recordDetail->i_id) : 0 ) @endphp
 	 	<tr>
        	<td class="text-center">{{ ++$index }}</td>
         	<td>{{ (isset($recordDetail->v_role_name) ? $recordDetail->v_role_name :'')}}</td>
          	<td>{{ (isset($recordDetail->v_role_description) ? $recordDetail->v_role_description :null)}}</td>
          	<td class="actions-button rolepermission-action-col">
          		<a title="{{ trans('messages.edit') }}" href="{{ config('constants.ROLES_AND_PERMISSION_MASTER_URL').'/showEditForm'.'/'. $encodeRecordId }}" class="btn btn-sm btn-edit btn-color-text"><i class="fas fa-pencil-alt"></i></a>
           		@if( !in_array( $recordDetail->i_id , [ config('constants.DEFAULT_EMPLOYEE_ROLE_ID') ] ) )
           		<button title="{{ trans('messages.delete') }}" class="btn btn-sm btn-delete btn-color-text" data-record-id="{{ $encodeRecordId }}" data-module-name="role-permission" onclick="deleteRecord(this);"><i class="fa fa-trash"></i></button>
           		@endif
           		<?php /* ?>
           		<a href="javascript:void(0);" data-record-id="{{ $encodeRecordId }}" class="btn btn btn-theme text-white border btn-sm mr-2 align-items-center manage-doc-btn upload-btn" onclick="assignEmployeesModal(this)" title="{{ trans('messages.assign-employees') }}">{{ trans('messages.assign-employees') }}</a>
           		<?php */ ?>
           		<a href="{{ config('constants.ROLES_AND_PERMISSION_MASTER_URL').'/assign-to-employees'.'/'. $encodeRecordId }}" data-record-id="{{ $encodeRecordId }}" class="btn btn btn-theme text-white border btn-sm mr-2 align-items-center manage-doc-btn upload-btn" title="{{ trans('messages.assign-employees') }}">{{ trans('messages.assign-employees') }}</a>
           		
           	</td>
       	</tr>
 	@endforeach
	@if(!empty($pagination))
 	    <input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
 		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
 		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
 	@endif
@else
 	<tr>
		<td colspan="4" class="text-center">@lang('messages.no-record-found')</td>
	</tr>
@endif
@include('admin/common-display-count')
