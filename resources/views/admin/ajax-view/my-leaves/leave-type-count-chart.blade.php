		
            <div class="card card-display border-0 px-0 pt-3 pb-0 h-100 leave-type-chart" >
                <div class="card-body px-0 py-0 status-bar-mdiv">
                    <div class="status-bar-card">
                        <div class="leave-balance-title px-4 process-chart" onchange="displayLeaveCountChart(this)" data-leave-type-id="{{  $leaveTypeDetail->i_id  }}" >
                            <h5 class="status-bar-name w-100">{{ $leaveTypeDetail->v_leave_type_name  }}</h5>
							@if( !in_array( $leaveTypeDetail->i_id , [ config('constants.PAID_LEAVE_TYPE_ID') , config('constants.UNPAID_LEAVE_TYPE_ID') ] ) )
								@if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) )
									<a href="javascript:void(0);" data-emp-id="{{ ( isset($employeeId) ? $employeeId : '' ) }}" onclick="openAddLeaveBalanceModal(this)" data-leave-type-id="{{  $leaveTypeDetail->i_id  }}" title="{{ trans('messages.add') }}"><i class="fas fa-plus leave-balance-hicon addicon  mr-2"></i></a>
								@endif
							@endif
                            <a href="javascript:void(0);" data-emp-id="{{ ( isset($employeeId) ? $employeeId : '' ) }}" onclick="openLeaveBalanceModal(this)" data-leave-type-id="{{ $leaveTypeDetail->i_id  }}"  title="{{ trans('messages.history') }}"><i class="fas fa-history leave-balance-hicon"></i></a>
                        </div>
                        <div class="leave-progress leave-process circle-bg-{{  $leaveTypeDetail->i_id }}" >
                            <div class="value-leave leave-value value-leave-name" data-id="{{ $leaveTypeDetail->i_id }}"></div>
                        </div>
                    </div>
                    <table class="table table-sm table-bordered mb-0">
                        <tr>
                            @if( $leaveTypeDetail->i_id != config('constants.UNPAID_LEAVE_TYPE_ID') )
                            <td>
                                <h5 class="details-title">{{ trans("messages.available") }}</h5>
                                <p class="h5 leave-balance-value">{{ ( isset($leaveAvailableInfo[$leaveTypeDetail->i_id]) ? $leaveAvailableInfo[$leaveTypeDetail->i_id] : 0 )  }}</p>
                            </td>
                            @endif
                            <td>
                                <h5 class="details-title">{{ trans("messages.consumed") }}</h5>
                                <p class="h5 {{ ( $leaveTypeDetail->i_id ==  config('constants.UNPAID_LEAVE_TYPE_ID') ) ? 'leave-balance-value' : ''  }} ">{{ ( isset($leaveConsumeInfo[$leaveTypeDetail->i_id]) ? $leaveConsumeInfo[$leaveTypeDetail->i_id] : 0 )  }}</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>