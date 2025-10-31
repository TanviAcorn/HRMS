<!--<div class="on-leave">
    <div class="card card-display border-0 mb-3 p-2 py-3">
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-8">
                    <h5 class="card-title mb-0">{{ trans("messages.on-leave-today") }}</h5>
                </div>
            </div>
        </div>
        <div class="leave-card">
            <div class="member-list col-12">
                <div class="row">
                    @if( count($onLeaveDetails) > 0 )
                    	@foreach($onLeaveDetails as $onLeaveDetail)
                    	@php 
                    	$encodeEmployeeId =  Wild_tiger::encode($onLeaveDetail->employeeInfo->i_id);
                    	@endphp
                    	
	                    	<a href="{{ route('employee-master.profile', $encodeEmployeeId ) }}" target="_blank" class="member-card col-sm-3 col-4">
	                    		
		                         @php
		                         $employeeData['employee_name'] = ( isset($onLeaveDetail->employeeInfo->v_employee_full_name) ? $onLeaveDetail->employeeInfo->v_employee_full_name : "" );
								 $employeeData['profile_pic'] = ( isset($onLeaveDetail->employeeInfo->v_profile_pic) ? $onLeaveDetail->employeeInfo->v_profile_pic : "" );
		                         echo employeeProfilePicView($employeeData);
		                         @endphp
		                        <div class="member-detail pt-3">
		                            <p class="member-name">{{ $employeeData['employee_name'] }}</p>
		                        </div>
		                       
		                    </a>
	                    
	                    @endforeach
                    @else
                    	<div class="col-12 px-4">{{ trans('messages.no-leave-record') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>-->