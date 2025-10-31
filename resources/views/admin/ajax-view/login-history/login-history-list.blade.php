@php $index =  ( $page_no - 1 ) * $perPageRecord @endphp
@if(count($recordDetails) > 0 )
	
	@foreach ($recordDetails as $key => $recordDetail) 
		<?php 
		$encodeRecordId = Wild_tiger::encode($recordDetail->i_id) ; 
		$encodeEmployeeRecordId =  ( (!empty($recordDetail->employee_id)) ?  Wild_tiger::encode($recordDetail->employee_id) : 0 ) ?> 
		<tr  class="has-record">
          <td class="text-center">{{ ++$index }}</td>
          <td class="text-left">
          	@if( (( session()->get('is_supervisor') == false ) && ( $recordDetail->employee_id == session()->get('user_employee_id'))) || ($recordDetail->v_role == config("constants.ROLE_ADMIN")) )
          		{{  ( (!empty($recordDetail->v_employee_full_name)) ? $recordDetail->v_employee_full_name . ' ('. ( (!empty($recordDetail->v_employee_code)) ? $recordDetail->v_employee_code : '' ) .')' : ( (!empty($recordDetail->v_name)) ? $recordDetail->v_name : '' ) )   }}
          	@else
          		<a href="{{  (!empty($encodeEmployeeRecordId) ? route('employee-master.profile', $encodeEmployeeRecordId ) : 'javascript:void(0)' )  }}" target="_blank" >{{  ( (!empty($recordDetail->v_employee_full_name)) ? $recordDetail->v_employee_full_name . ' ('. ( (!empty($recordDetail->v_employee_code)) ? $recordDetail->v_employee_code : '' ) .')' : ( (!empty($recordDetail->v_name)) ? $recordDetail->v_name : '' ) )   }}</a>
          	@endif
          	
          </td>
          <td>{{  ( (!empty($recordDetail->team_name)) ? $recordDetail->team_name : '' )   }}</td>
          <td>{{  ( (!empty($recordDetail->dt_created_at)) ? clientDateTime ( $recordDetail->dt_created_at ) : '' )   }}</td>
          <td>{{  ( (!empty($recordDetail->v_ip)) ? ( $recordDetail->v_ip ) : '' )   }}</td>
   		</tr>                                  
@endforeach
	@if(!empty($pagination))
		<input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
        <input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
        <input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
	@endif
@else
      <tr class="text-center"><td colspan="5">@lang('messages.no-record-found')</td></tr>        
@endif
@include('admin/common-display-count')	

										