@if( count($recordDetails) > 0 )
	@php $rowIndex = 0; @endphp
	@foreach($recordDetails as $recordDetail)
		<tr>
			<td class="text-center">{{ ( isset($recordDetail['action_date']) ? convertDateFormat($recordDetail['action_date']) : '' ) }}</td>
			<td>{{ ( isset($recordDetail['date']) ? ($recordDetail['date']) : '' ) }}</td>
			
		    <td>{{ ( isset($recordDetail['change']) ? ($recordDetail['change']) : '' ) }}</td>
		    <td>{{ ( isset($recordDetail['balance']) ? ($recordDetail['balance']) : '' ) }}</td>
		    <td>{{ ( isset($recordDetail['remark']) ? ($recordDetail['remark']) : '' ) }}</td>
		</tr>
		
    @endforeach
@else 
	<tr class="text-center">
		<td colspan="5">{{ trans('messages.no-record-found') }}</td>
	</tr>
@endif