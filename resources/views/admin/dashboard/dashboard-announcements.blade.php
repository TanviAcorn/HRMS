<div class="announcements">
    <div class="card card-display mb-3 border-0 p-2 py-lg-3 pt-md-3">
        <div class="card-body py-0">
            <div class="row">
                <div class="col-12">
                    <h5 class="card-title mb-0">{{ trans("messages.my-leave-balance") }}
                    </h5>
                </div>
            </div>
            <div class="row py-0 leave-status-card align-items-center">
                <div class="col-12 p-2 leave-status-item">
                    <div class="row mt-3">
                        @if(count($leaveTypeDetails) > 0 )
                        	@foreach($leaveTypeDetails as $leaveTypeDetail)
                        		@php
                        		$countValue = ( isset($leaveAvailableInfo[$leaveTypeDetail->i_id]) ? $leaveAvailableInfo[$leaveTypeDetail->i_id] : 0 );
                        		$countText = trans('messages.available');
                        		$additionaSign = "";
                        		if( $leaveTypeDetail->i_id == config('constants.UNPAID_LEAVE_TYPE_ID') ){
                        			$countValue = ( isset($leaveConsumeInfo[$leaveTypeDetail->i_id]) ? $leaveConsumeInfo[$leaveTypeDetail->i_id] : 0 );
                        			$countText = trans('messages.consumed');
                        			$additionaSign = " - ";
                        		}
                        		
                        		@endphp
                        		<div class="status-bar-card col-4 leave-type-chart" >
		                            <div class="leave-progress dashboard-leave-progress leave-process circle-bg-{{ $leaveTypeDetail->i_id }}" onchange="displayLeaveCountChart(this)">
		                                <div class="value-leave leave-balance-value ">{{ ( $countValue > 0 ? $additionaSign : '' )  }} {{ $countValue  }} Day(s) {{ $countText }}</div>
		                            </div>
		                            <h5 class="status-bar-name">{{ isset($leaveTypeDetail->v_leave_type_name) ? $leaveTypeDetail->v_leave_type_name : ''  }}</h5>
		                        </div>
                        	@endforeach
                        @else
                        	<div class="col-12 px-4">{{ trans('messages.no-record-found') }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-12 status-links">
                	@if( ( session()->has('user_employee_id') ) && ( session()->has('user_employee_id') > 0 ) )
                    	<a href="javascript:void(0);" class="status-bar-link" data-emp-id="{{ Wild_tiger::encode(session()->get('user_employee_id')) }}" onclick="openApplyLeaveModel(this);" title="{{ trans('messages.apply-leave') }}">{{ trans("messages.apply-leave") }}</a>
                    	<a href="{{ config('constants.MY_LEAVES_MASTER_URL') }}" target="blank" class="status-bar-link" title="{{ trans('messages.my-leaves') }}">{{ trans("messages.my-leaves") }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
	$(".leave-process").trigger('change');
})
</script>