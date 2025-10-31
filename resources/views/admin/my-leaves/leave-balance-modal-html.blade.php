@if(count($leaveBalanceDetails) > 0 )
	@foreach($leaveBalanceDetails as $leaveBalanceDetail)
		<div class="status-bar-card col-sm-3 col-6 leave-type-chart">
			<div class="leave-progress paid-leave circle-bg-{{  ( isset($leaveBalanceDetail->i_leave_type_id) ? $leaveBalanceDetail->i_leave_type_id : '' ) }}" onchange="displayLeaveCountChart(this)">
		    	<div class="value-leave paid-value leave-balance-value">{{ ( ( $leaveBalanceDetail->i_leave_type_id == config('constants.UNPAID_LEAVE_TYPE_ID') ) ? ( isset($leaveConsumeInfo[config('constants.UNPAID_LEAVE_TYPE_ID')]) ? ( $leaveConsumeInfo[config('constants.UNPAID_LEAVE_TYPE_ID')] > 0 ? ' - '. $leaveConsumeInfo[config('constants.UNPAID_LEAVE_TYPE_ID')] : 0 )  : 0 )  :  ( $leaveBalanceDetail->d_current_balance ? $leaveBalanceDetail->d_current_balance : 0 ) )  }}</div>
		   	</div>
		    <h5 class="status-bar-name">{{ ( isset($leaveBalanceDetail->leaveType->v_leave_type_name) ? $leaveBalanceDetail->leaveType->v_leave_type_name : '' )  }}</h5>
		</div>
	@endforeach
@endif
												
                                                