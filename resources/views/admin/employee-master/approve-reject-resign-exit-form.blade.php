							@if( isset($resignInfo) && (!empty($resignInfo)) )
								
								
								@if( isset($resignInfo->e_initiate_type) && (!empty($resignInfo->e_initiate_type)) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) )
									<div class="col-12 pb-3 pt-0">
			                            <h4 class="address-title">{{ trans("messages.resignation-details") }}</h4>
			                        </div>
			                        <div class="col-12 col-lg-4 col-md-6">
			                            <label class="control-label">{{ trans("messages.employee-name") }}</label>
			                            <p>{{ ( isset($resignInfo->employee->v_employee_name) && (!empty($resignInfo->employee->v_employee_name)) ) ? $resignInfo->employee->v_employee_name : '' }} {{ ( isset($resignInfo->employee->v_employee_code) && (!empty($resignInfo->employee->v_employee_code)) ) ? ' ('.$resignInfo->employee->v_employee_code.')' : '' }}</p>
			                        </div>
			                        <div class="col-12 col-lg-4 col-md-6">
			                            <label class="control-label">Discussion with manager ?</label>
			                            <p>{{ ( isset($resignInfo->e_employee_discuss) && (!empty($resignInfo->e_employee_discuss)) ) ? ( $resignInfo->e_employee_discuss ) : ''  }}</p>
			                        </div>
			                        <div class="col-lg-4 col-md-6">
			                            <label class="control-label">Reason for resignation</label>
			                            <p>{{ ( isset($resignInfo->resignation->v_value) && (!empty($resignInfo->resignation->v_value)) ) ? ( $resignInfo->resignation->v_value ) : ''  }}</p>
			                        </div>
			                         <div class="col-lg-4 col-md-6">
			                            <label class="control-label">When did employee provide the notice of exit ?</label>
			                            <p>{{ ( isset($resignInfo->dt_employee_notice_date) && (!empty($resignInfo->dt_employee_notice_date)) ) ? convertDateFormat( $resignInfo->dt_employee_notice_date ) : ''  }}</p>
			                        </div>
			                        <div class="col-lg-4 col-md-6">
			                            <label class="control-label">Any preference on an early last working day?</label>
			                            <p>{{ ( isset($resignInfo->e_employee_discuss) && (!empty($resignInfo->e_employee_discuss)) ) ? ( $resignInfo->e_employee_discuss ) : ''  }}</p>
			                        </div>
			                        @if( isset($resignInfo->e_last_working_day) && (!empty($resignInfo->e_last_working_day)) && ( $resignInfo->e_last_working_day == config('constants.OTHER') ) && (!empty($resignInfo->dt_last_working_date)) )
			                        <div class="col-lg-4">
			                            <label class="control-label">Last working day</label>
			                            <p>{{ convertDateFormat($resignInfo->dt_last_working_date) }}</p>
			                        </div>
			                        @endif
			                        @if( isset($resignInfo->e_last_working_day) && (!empty($resignInfo->e_last_working_day)) && ( $resignInfo->e_last_working_day == config('constants.NOTICE_PERIOD') ) && (!empty($resignInfo->dt_system_last_working_date)) )
			                        <div class="col-lg-4">
			                            <label class="control-label">Last working day</label>
			                            <p>{{ convertDateFormat($resignInfo->dt_system_last_working_date) }}</p>
			                        </div>
			                        @endif
			                        @if( isset($resignInfo->v_discuss_summary) && (!empty($resignInfo->v_discuss_summary)) && ( isset($resignInfo->e_employee_discuss)) && ( $resignInfo->e_employee_discuss == config('constants.SELECTION_YES') ) )
			                        <div class="col-lg-6">
			                            <label class="control-label">Summary of discussion</label>
			                            <p>{{ ( isset($resignInfo->v_discuss_summary) && (!empty($resignInfo->v_discuss_summary)) ) ? ( $resignInfo->v_discuss_summary ) : ''  }}</p>
			                        </div>
			                        @endif
			                        @if( isset($resignInfo->v_remark) && (!empty($resignInfo->v_remark)) )
			                        <div class="col-lg-6">
			                            <label class="control-label">Comment</label>
			                            <p>{{ ( isset($resignInfo->v_remark) && (!empty($resignInfo->v_remark)) ) ? ( $resignInfo->v_remark ) : ''  }}</p>
			                        </div>
			                        @endif
			                        @if(!empty($resignInfo->v_approval_remark))
				                        <div class="col-sm-6">
				                            <label class="control-label">{{ trans("messages.approval-remark")}}</label>
				                            <p>{{ ( isset($resignInfo->v_approval_remark) && (!empty($resignInfo->v_approval_remark)) ) ? ( $resignInfo->v_approval_remark ) : ''  }}</p>
				                        </div>
				                    @endif
				                     <?php if ((isset($resignInfo)) && ($resignInfo->v_attachment)){ ?>
									<div class="col-sm-12 col-12">
					                	<label class="control-label">{{ trans("messages.attachments") }}</label>
					                    <div class="col-lg-12">
										<?php
										$documentFiles = [ $resignInfo->v_attachment  ];
										if(!empty($documentFiles)){
											foreach ($documentFiles as $documentFile){
												$documentFileName = "";
												if (!empty($documentFile) && file_exists(config('constants.FILE_STORAGE_PATH').config('constants.UPLOAD_FOLDER') . $documentFile)) {
													$documentFileName =  config('constants.FILE_STORAGE_PATH_URL') .  config('constants.UPLOAD_FOLDER') .  $documentFile;
													?>
													<div class="gallery-image-div py-2">
														<div class="row justify-content-between align-items-center">
															<div class="upload-main-image w-75">
																<label class="pr-2 image-label">{{ (isset($documentFileName) ? basename($documentFileName) : '') }}</label>
															</div>
															<div class="close-buttons">
																<a href="{{ $documentFileName }}" title='{{ trans("messages.view") }}' target="_blank"  class="btn btn-sm bg-theme btn-submit-class text-white"><i class="fa fa-eye"></i></a>
																<a href="{{ $documentFileName }}" title='{{ trans("messages.download") }}' download class="btn-success btn btn-sm btn-primary btn-submit-class text-white"><i class="fa fa-download"></i></a>
															</div>
														</div>
													</div>
								               <?php 	
												}
											}
										}
										?>
										</div>
			                        </div>	
									<?php } ?>
								
								@else
									
									<div class="col-12 pb-3 pt-0">
			                            <h4 class="address-title">{{ trans("messages.initiate-exit-details") }}</h4>
			                        </div>
			                        <div class="col-12 col-lg-4 col-md-6">
			                            <label class="control-label">{{ trans("messages.employee-name") }}</label>
			                            <p>{{ ( isset($resignInfo->employee->v_employee_name) && (!empty($resignInfo->employee->v_employee_name)) ) ? $resignInfo->employee->v_employee_name : '' }} {{ ( isset($resignInfo->employee->v_employee_code) && (!empty($resignInfo->employee->v_employee_code)) ) ? ' ('.$resignInfo->employee->v_employee_code.')' : '' }}</p>
			                        </div>
			                        <div class="col-12 col-lg-4 col-md-6">
			                            <label class="control-label">What is the reason for initiating the exit ?</label>
			                            <p>{{ ( isset($resignInfo->e_initiate_type) && (!empty($resignInfo->e_initiate_type)) ) ? ( $resignInfo->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE')  ? trans('messages.employee-wants-to-resign') :  trans('messages.company-decide-to-terminate') ) : ''  }}</p>
			                        </div>
			                        <div class="col-lg-4 col-md-6">
			                            <label class="control-label">Did you have discussion with employee on their decision ?</label>
			                            <p>{{ ( isset($resignInfo->e_employee_discuss) && (!empty($resignInfo->e_employee_discuss)) ) ? ( $resignInfo->e_employee_discuss ) : ''  }}</p>
			                        </div>
			                        @if( ( isset( $resignInfo->e_initiate_type) ) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') )  )
			                        <div class="col-lg-4 col-md-6 col-md-6">
			                            <label class="control-label">Reason for termination</label>
			                            <p>{{ ( isset($resignInfo->termination->v_value) && (!empty($resignInfo->termination->v_value)) ) ? ( $resignInfo->termination->v_value ) : ''  }}</p>
			                        </div>
			                        @endif
			                        @if( ( isset( $resignInfo->e_initiate_type) ) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') )  )
			                        <div class="col-lg-4 col-md-6">
			                            <label class="control-label">Reason of resignation</label>
			                            <p>{{ ( isset($resignInfo->resignation->v_value) && (!empty($resignInfo->resignation->v_value)) ) ? ( $resignInfo->resignation->v_value ) : ''  }}</p>
			                        </div>
			                        @endif
			                        @if( ( isset( $resignInfo->e_initiate_type) ) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') )  )
			                        <div class="col-lg-4 col-md-6">
			                            <label class="control-label">What was the termination notice date ?</label>
			                            <p>{{ ( isset($resignInfo->dt_termination_notice_date) && (!empty($resignInfo->dt_termination_notice_date)) ) ? convertDateFormat( $resignInfo->dt_termination_notice_date ) : ''  }}</p>
			                        </div>
			                        @endif
			                        @if( ( isset( $resignInfo->e_initiate_type) ) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') )  )
			                        <div class="col-lg-4 col-md-6">
			                            <label class="control-label">When did employee provide the notice of exit?</label>
			                            <p>{{ ( isset($resignInfo->dt_employee_notice_date) && (!empty($resignInfo->dt_employee_notice_date)) ) ? convertDateFormat( $resignInfo->dt_employee_notice_date ) : ''  }}</p>
			                        </div>
			                        @endif
			                        @if( ( isset( $resignInfo->e_last_working_day) ) && ( $resignInfo->e_last_working_day == config('constants.OTHER') )  )
			                        <div class="col-lg-4 col-md-6">
			                            <label class="control-label">Last working day</label>
			                            <p>{{ ( isset($resignInfo->dt_last_working_date) && (!empty($resignInfo->dt_last_working_date)) ) ? convertDateFormat( $resignInfo->dt_last_working_date ) : ''  }}</p>
			                        </div>
			                        @endif
			                         @if( ( isset( $resignInfo->e_last_working_day) ) && ( $resignInfo->e_last_working_day == config('constants.NOTICE_PERIOD') )  )
			                        <div class="col-lg-4 col-md-6">
			                            <label class="control-label">Last working day</label>
			                            <p>{{ ( isset($resignInfo->dt_system_last_working_date) && (!empty($resignInfo->dt_system_last_working_date)) ) ? convertDateFormat( $resignInfo->dt_system_last_working_date ) : ''  }}</p>
			                        </div>
			                        @endif
			                        @if( ( isset( $resignInfo->e_initiate_type) ) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') )  )
			                        <div class="col-lg-4 col-md-6">
			                            <label class="control-label">Ok to Rehire</label>
			                            <p>{{ ( isset($resignInfo->e_rehire_status) && (!empty($resignInfo->e_rehire_status)) ) ? ( $resignInfo->e_rehire_status ) : ''  }}</p>
			                        </div>
			                        @endif
			                         @if( isset($resignInfo->v_discuss_summary) && (!empty($resignInfo->v_discuss_summary)) && ( isset($resignInfo->e_employee_discuss)) && ( $resignInfo->e_employee_discuss == config('constants.SELECTION_YES') ) )
			                        <div class="col-lg-6">
			                            <label class="control-label">Summary</label>
			                            <p>{{ ( isset($resignInfo->v_discuss_summary) && (!empty($resignInfo->v_discuss_summary)) ) ? ( $resignInfo->v_discuss_summary ) : ''  }}</p>
			                        </div>
			                        @endif
			                        <div class="col-sm-6">
			                            <label class="control-label">Comment</label>
			                            <p>{{ ( isset($resignInfo->v_remark) && (!empty($resignInfo->v_remark)) ) ? ( $resignInfo->v_remark ) : ''  }}</p>
			                        </div>
			                        @if(!empty($resignInfo->v_approval_remark))
				                        <div class="col-sm-6">
				                            <label class="control-label">{{ trans("messages.approval-remark")}}</label>
				                            <p>{{ ( isset($resignInfo->v_approval_remark) && (!empty($resignInfo->v_approval_remark)) ) ? ( $resignInfo->v_approval_remark ) : ''  }}</p>
				                        </div>
				                    @endif
				                   
				                    <?php if ((isset($resignInfo)) && ($resignInfo->v_attachment)){ ?>
									<div class="col-sm-12 col-12">
					                	<label class="control-label">{{ trans("messages.attachments") }}</label>
					                    <div class="col-lg-12">
										<?php
										$documentFiles = [ $resignInfo->v_attachment  ];
										if(!empty($documentFiles)){
											foreach ($documentFiles as $documentFile){
												$documentFileName = "";
												if (!empty($documentFile) && file_exists(config('constants.FILE_STORAGE_PATH').config('constants.UPLOAD_FOLDER') . $documentFile)) {
													$documentFileName =  config('constants.FILE_STORAGE_PATH_URL') .  config('constants.UPLOAD_FOLDER') .  $documentFile;
													?>
													<div class="gallery-image-div py-2">
														<div class="row justify-content-between align-items-center">
															<div class="upload-main-image w-75">
																<label class="pr-2 image-label">{{ (isset($documentFileName) ? basename($documentFileName) : '') }}</label>
															</div>
															<div class="close-buttons">
																<a href="{{ $documentFileName }}" title='{{ trans("messages.view") }}' target="_blank"  class="btn btn-sm bg-theme btn-submit-class text-white"><i class="fa fa-eye"></i></a>
																<a href="{{ $documentFileName }}" title='{{ trans("messages.download") }}' download class="btn-success btn btn-sm btn-primary btn-submit-class text-white"><i class="fa fa-download"></i></a>
															</div>
														</div>
													</div>
								               <?php 	
												}
											}
										}
										?>
										</div>
			                        </div>	
									<?php } ?>
								
								@endif
								
								<div class="col-12 py-3">
		                            <h4 class="address-title">{{ trans("messages.take-action") }}</h4>
		                        </div>
		                    @endif
							<div class="col-lg-12">
								<div class="form-group">
									<label class="control-label" for="initiating_exit_reason">Do you want to continue with exit formalities?<span class="text-danger">*</span></label>
									<div class="d-flex">
										<div class="form-check pr-3">
											<input class="form-check-input" type="radio" name="accept_resign" onclick="showAcceptResignField(this);" id="accept-resign-yes" value="{{ config('constants.SELECTION_YES') }}" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_initiate_type)) && ( $resignInfo->e_initiate_type == config('constants.SELECTION_YES') ) ) ? 'checked' : '' )  }}>
											<label class="form-check-label" for="accept-resign-yes">{{ trans('messages.yes') }}</label>
										</div>

										<div class="form-check">
											<input class="form-check-input" type="radio" name="accept_resign" onclick="showAcceptResignField(this);"  id="accept-resign-no" value="{{ config('constants.SELECTION_NO') }}" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_initiate_type)) && ( $resignInfo->e_initiate_type == config('constants.SELECTION_NO') ) ) ? 'checked' : '' )  }}>
											<label class="form-check-label" for="accept-resign-no">{{ trans('messages.no') }}</label>
										</div>
									</div>
								</div>
                            </div>
                            @php
                            $resignApplyDate = "";
                            if( ( isset( $resignInfo->e_initiate_type) ) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') )  ){
                            	$resignApplyDate = ( ( isset($resignInfo->dt_termination_notice_date) && (!empty($resignInfo->dt_termination_notice_date)) ) ? ( $resignInfo->dt_termination_notice_date ) : '' )  ;
                            } else {
                            	$resignApplyDate = ( ( isset($resignInfo->dt_employee_notice_date) && (!empty($resignInfo->dt_employee_notice_date)) ) ? ( $resignInfo->dt_employee_notice_date ) : '' )  ;
                            }
                            @endphp
                            
                            <input type="hidden" name="resign_apply_date" value="{{ ( isset($resignApplyDate)  ? $resignApplyDate : '' ) }}">
                            <input type="hidden" name="approve_resign_emp_joining_date" value="{{ ( isset($resignInfo->employee->dt_joining_date)  ? $resignInfo->employee->dt_joining_date : '' ) }}">
                            @if( ( isset( $resignInfo->e_initiate_type) ) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') )  )
	                        <div class="col-lg-4 col-md-6 approve-reject-field" style="display: none;">
	                            <label class="control-label">What was the termination notice date ?</label>
	                            <input type="text" class="form-control" name="approve_resign_initial_exit_employee_provide_notice_exit_date"  data-notice-duration-value="{{ ( isset($resignInfo->employee->noticePeriodInfo->v_probation_period_duration) ? $resignInfo->employee->noticePeriodInfo->v_probation_period_duration : '' ) }}" data-notice-duration-selection="{{ ( isset($resignInfo->employee->noticePeriodInfo->e_months_weeks_days) ? $resignInfo->employee->noticePeriodInfo->e_months_weeks_days : '' ) }}" placeholder="{{ config('constants.DEFAULT_DATE_FORMAT') }}" value="{{ ( isset($resignInfo->dt_termination_notice_date) && (!empty($resignInfo->dt_termination_notice_date)) ) ? clientDate( $resignInfo->dt_termination_notice_date ) : ''  }}"/>
	                            <p></p>
	                        </div>
	                        @else
	                        <div class="col-lg-4 col-sm-6 approve-reject-field" style="display: none;">
                                <div class="form-group">
                                    <label class="control-label" for="employee_provide_notice_exit">When did employee provide the notice of exit?<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" data-notice-duration-value="{{ ( isset($resignInfo->employee->noticePeriodInfo->v_probation_period_duration) ? $resignInfo->employee->noticePeriodInfo->v_probation_period_duration : '' ) }}" data-notice-duration-selection="{{ ( isset($resignInfo->employee->noticePeriodInfo->e_months_weeks_days) ? $resignInfo->employee->noticePeriodInfo->e_months_weeks_days : '' ) }}" name="approve_resign_initial_exit_employee_provide_notice_exit_date" placeholder="{{ config('constants.DEFAULT_DATE_FORMAT') }}" value="{{ ( ( (isset($resignInfo) && (!empty($resignInfo->dt_employee_notice_date)) ) ) ? clientDate( $resignInfo->dt_employee_notice_date ) : '' )  }}"/>
                                </div>
                            </div>
	                        @endif
	                        
	                        
	                        @if( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) && ( isset($resignInfo->employee->latestSalaryMaster->e_pf_deduction) ) && ( $resignInfo->employee->latestSalaryMaster->e_pf_deduction == config('constants.SELECTION_YES')  ) )
	                        <div class="col-lg-4 col-sm-6 approve-reject-field" style="display: none;">
                                <div class="form-group">
                                    <label class="control-label" for="pf_exit_date">{{ trans('messages.pf-exit-date') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control"  name="pf_exit_date" placeholder="{{ config('constants.DEFAULT_DATE_FORMAT') }}" value="{{ ( ( (isset($resignInfo) && (!empty($resignInfo->employee->dt_pf_expiry_date)) ) ) ? clientDate( $resignInfo->employee->dt_pf_expiry_date ) : '' )  }}"/>
                                </div>
                            </div>
	                        @endif
                            
                            @php
                            $noticePeriodDate = '';
                            if( isset($resignInfo->employee->noticePeriodInfo->v_probation_period_duration) && isset($resignInfo->employee->noticePeriodInfo->e_months_weeks_days) ){
                            	$noticePeriodDuration = $resignInfo->employee->noticePeriodInfo->v_probation_period_duration .  (!empty($resignInfo->employee->noticePeriodInfo->e_months_weeks_days) ? ' ' . $resignInfo->employee->noticePeriodInfo->e_months_weeks_days : '' );
                            	if ( (!empty($noticePeriodDuration)) && (!empty($resignApplyDate)) ) {
                            	 	$noticePeriodDate = date('Y-m-d' , strtotime( $resignApplyDate . " +" . $noticePeriodDuration ));	
                            	}
                           	}
                           	@endphp
                            
							<div class="col-lg-12 approve-reject-field" style="display: none;">
								<div class="row">
									<div class="col-lg-6">
		                                <div class="form-group">
		                                    <label class="control-label" for="recommend_last_working_day">Choose last working day for the employee <span class="text-danger">*</span></label>
		                                    <a type="button" class="ml-2" data-toggle="tooltip" data-placement="right" title="This last working day is applicable if employee continues with the exit process."><i class="fa fa-info-circle" aria-hidden="true"></i></a>
		
		                                    <div class="">
		                                        <div class="form-check pr-3">
		                                            <input class="form-check-input" type="radio" name="approve_resign_initial_exit_recommend_last_working_day_type" onclick="showApproveRejectOtherDateDiv(this);" id="approve_resign_complete_period" value="{{ config('constants.NOTICE_PERIOD') }}" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_last_working_day)) && ( $resignInfo->e_last_working_day == config('constants.NOTICE_PERIOD') ) ) ? 'checked' : '' )  }}>
		                                            <label class="form-check-label" for="approve_resign_complete_period">Complete notice period <span class="text-muted notice-period-completion-date">{{ (!empty($noticePeriodDate) ? '('.convertDateFormat($noticePeriodDate).')'  : '' ) }}</span></label>
		                                        </div>
		                                        <div class="form-check">
		                                            <input class="form-check-input" type="radio" name="approve_resign_initial_exit_recommend_last_working_day_type" onclick="showApproveRejectOtherDateDiv(this);" id="approve_resign_other_date" value="{{ config('constants.OTHER') }}" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_last_working_day)) && ( $resignInfo->e_last_working_day == config('constants.OTHER') ) ) ? 'checked' : '' )  }}>
		                                            <label class="form-check-label" for="approve_resign_other_date">{{ trans('messages.other-date') }}</label>
		                                        </div>
		                                    </div>
		                                </div>
		                            </div>
									
									<div class="col-lg-6 approve-resign-other-last-working-date" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_last_working_day)) && ( $resignInfo->e_last_working_day == config('constants.OTHER') ) ) ? '' : "style=display:none" )  }}>
		                                <div class="form-group">
		                                    <label class="control-label" for="initial_exit_last_working_date">Last Working Day<span class="text-danger">*</span></label>
		                                    <input type="text" class="form-control" name="approve_resign_initial_exit_other_last_working_date" placeholder="{{ config('constants.DEFAULT_DATE_FORMAT') }}" value="{{  ( (  isset($resignInfo) && (!empty($resignInfo->dt_last_working_date)) )  ? clientDate( $resignInfo->dt_last_working_date ) : ( (!empty($noticePeriodDate))  ? clientDate ( $noticePeriodDate ) : '' ) ) }}" />
		                                </div>
		                            </div>
								</div>
							</div>
							@if(count($allChildEmployeeDetails) > 0 )
							<div class="col-lg-12 approve-reject-field" style="display: none;">
								<div class="row">
									<div class="col-lg-6">
		                                <div class="table-responsive">
			                                <table class="table table-sm table-hover table-bordered">
			                                	<thead>
			                                		<tr>
			                                			<td class="text-center" style="min-width:50px;max-width:50px;width:50px">{{ trans('messages.sr-no') }}</td>
			                                			<td class="text-left" style="min-width:90px;max-width:90px;width:90px">{{ trans('messages.employee') }}</td>
			                                			<td class="text-center" style="min-width:130px;max-width:130px;width:130px">{{ trans('messages.upcoming-leader') }}</td>
			                                		</tr>
			                                	</thead>
			                                	<tbody class="upcoming-leader-tbody">
			                                		@if(count($allChildEmployeeDetails) > 0 )
			                                			@php $allChildEmployeeIndex = 0; @endphp
			                                			@foreach($allChildEmployeeDetails as $allChildEmployeeDetail)
			                                				<tr>
			                                					<td class="text-center">{{ ++$allChildEmployeeIndex }}</td>
			                                					<td class="text-left">{{ ( isset($allChildEmployeeDetail->v_employee_full_name) ?  $allChildEmployeeDetail->v_employee_full_name : '' ) }}</td>
			                                					<td class="text-center">
			                                						<select name="leader_for_{{ $allChildEmployeeDetail->i_id }}" class="form-control select2 upcoming-leader-value">
			                                							<option value="">{{ trans('messages.select') }}</option>
			                                							@if(count($allEmployeeDetails) > 0 )
			                                								@foreach($allEmployeeDetails as $allEmployeeDetail)
			                                									@if( $allEmployeeDetail->i_id != $allChildEmployeeDetail->i_id )
			                                									<option value="{{ Wild_tiger::encode($allEmployeeDetail->i_id)}}">{{  ( isset($allEmployeeDetail->v_employee_full_name) ?  $allEmployeeDetail->v_employee_full_name : '' ) }}</option>
			                                									@endif	
			                                								@endforeach
			                                							
			                                							@endif
			                                						</select>
			                                					</td>
			                                				</tr>
			                                			@endforeach
			                                		@endif
			                                	</<tbody>
			                                </table>
		                                </div>
		                            </div>
								</div>
							</div>
							@endif
							<div class="col-md-6">
	                        	<div class="form-group">
	                        		<label for="resign_approve_reject_remark" class="control-label">{{ trans('messages.remark') }} <span class="text-danger">*</span></label>
	                        		<textarea class="form-control" name="approve_resign_reject_remark" placeholder="{{ trans('messages.remark') }}"></textarea>
	                        	</div>
                        	</div>