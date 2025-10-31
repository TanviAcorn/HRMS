		@php 
		$encodeRecordId = Wild_tiger::encode($recordDetail->i_id) ;
		
		$getHoldAmountInfo = getHoldAmountInfo($recordDetail);
		
		
		$totalOnHoldSalaryAmount = ( isset($getHoldAmountInfo['totalOnHoldSalaryAmount']) ? $getHoldAmountInfo['totalOnHoldSalaryAmount'] : 0 ) ;
		$decuctOnHoldSalaryAmount = ( isset($getHoldAmountInfo['deductOnHoldSalaryAmount']) ? $getHoldAmountInfo['deductOnHoldSalaryAmount'] : 0 ) ;
		$expectedReleasedDate = ( isset($getHoldAmountInfo['expectedReleaseDate']) ? $getHoldAmountInfo['expectedReleaseDate'] : null ) ;
		$releasedDate = ( isset($getHoldAmountInfo['releaseDate']) ? $getHoldAmountInfo['releaseDate'] : null ) ;
		$leftAmount = ( isset($getHoldAmountInfo['leftOnHoldSalaryAmount']) ? $getHoldAmountInfo['leftOnHoldSalaryAmount'] : 0 ) ;
		
		@endphp
		<?php //echo "<pre>";print_r($getHoldAmountInfo) ;die;?>
			<td class="text-center sr-index">{{ $rowIndex }}</td>
            <td><a href="{{ route('employee-master.profile', $encodeRecordId ) }}" target="_blank" >{{ ( isset($recordDetail->v_employee_full_name) ? $recordDetail->v_employee_full_name : '' )  }} ({{ ( isset($recordDetail->v_employee_code) ? $recordDetail->v_employee_code : '' )  }})</a> <br> {{ ( isset($recordDetail->v_contact_no) ? $recordDetail->v_contact_no : '' )  }}</td>
            <td>{{ ( isset($recordDetail->teamInfo->v_value) ? $recordDetail->teamInfo->v_value : '' )  }}</td>
            <td>{{ ( isset($recordDetail->designationInfo->v_value) ? $recordDetail->designationInfo->v_value : '' )  }}</td>
            <td class="text-left">{{ ( isset($recordDetail->dt_joining_date) ? convertDateFormat($recordDetail->dt_joining_date,'d.m.Y') : '' )  }}</td>
            <td><a class="planned-hold-amount" href="javascript:void(0)" <?php echo ( $totalOnHoldSalaryAmount > 0 ? 'onclick="showPlannedAmountHistory(this);"' : '' ) ?> data-record-id="{{ $encodeRecordId }}"  data-emp-name="{{ ( isset($recordDetail->v_employee_full_name) ? $recordDetail->v_employee_full_name : '' )  }}" data-emp-code="{{ ( isset($recordDetail->v_employee_code) ? $recordDetail->v_employee_code : '' )  }}"  data-amount="{{ (!empty($totalOnHoldSalaryAmount) ? ($totalOnHoldSalaryAmount) : 0 ) }}" >{{ (!empty($totalOnHoldSalaryAmount) ? decimalAmount($totalOnHoldSalaryAmount) : 0 ) }}</a></td>
            <td><a class="deduct-hold-amount" href="javascript:void(0)" <?php echo ( $decuctOnHoldSalaryAmount > 0 ? 'onclick="showDeductAmountHistory(this);"' : '' ) ?> data-record-id="{{ $encodeRecordId }}"  data-emp-name="{{ ( isset($recordDetail->v_employee_full_name) ? $recordDetail->v_employee_full_name : '' )  }}" data-emp-code="{{ ( isset($recordDetail->v_employee_code) ? $recordDetail->v_employee_code : '' )  }}"  data-amount="{{ (!empty($decuctOnHoldSalaryAmount) ? ($decuctOnHoldSalaryAmount) : 0 ) }}">{{ (!empty($decuctOnHoldSalaryAmount) ? decimalAmount($decuctOnHoldSalaryAmount) : 0 ) }}</a></td>
            <td>{{ (!empty($leftAmount) ? decimalAmount($leftAmount)  : 0 )  }}</td>
            <td class="text-left">{{ ( isset($expectedReleasedDate)  ? convertDateFormat($expectedReleasedDate,'M-Y') : "" ) }}</td>
            <td class="text-left">{{ ( isset($releasedDate)  ? convertDateFormat($releasedDate,'M-Y') : "" ) }}</td>
            <td class="record-status" data-amount="{{ (!empty($decuctOnHoldSalaryAmount) ? ($decuctOnHoldSalaryAmount)  : 0 )  }}" data-status="{{ ( isset($recordDetail->e_hold_salary_payment_status) ? $recordDetail->e_hold_salary_payment_status : '' ) }}">{{ ( isset($recordDetail->e_hold_salary_payment_status) ? $recordDetail->e_hold_salary_payment_status : '' ) }}</td>
            @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )
            <td>
            	@if(  (!empty($recordDetail->e_hold_salary_payment_status)) && ( !in_array( $recordDetail->e_hold_salary_payment_status , [ config('constants.PAID_STATUS') , config('constants.DONATED_STATUS') ] ) ) )
            		@if( $recordDetail->e_hold_salary_payment_status == config('constants.PENDING_STATUS') )
            			<a href="javascript:void(0);" class="btn btn btn-theme text-white border btn-sm manage-doc-btn align-items-center mb-1" onclick="updateOnHoldSalaryStatus(this);" data-update-status="{{ config('constants.NOT_TO_PAY_STATUS') }}" data-record-id="{{ $encodeRecordId }}" title="{{ trans('messages.mark-as-not-to-pay') }}">{{ trans("messages.mark-as-not-to-pay") }}</a>
            		@elseif( $recordDetail->e_hold_salary_payment_status == config('constants.NOT_TO_PAY_STATUS') )
            			<a href="javascript:void(0);" class="btn btn btn-theme text-white border btn-sm d-sm-flex mr-2 align-items-center manage-doc-btn upload-btn" onclick="updateOnHoldSalaryStatus(this);" data-update-status="{{ config('constants.DONATED_STATUS') }}" data-record-id="{{ $encodeRecordId }}" title="{{ trans('messages.mark-for-donation') }}">{{ trans("messages.mark-for-donation") }}</a>
            		@endif
            	@endif
          	</td>
          	@endif