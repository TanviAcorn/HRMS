					<?php if( isset($resignInfo) && (!empty($resignInfo)) ) { ?>
					<div class="row">
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
                        <div class="col-12 py-3">
                            <h4 class="address-title">{{ trans("messages.initiate-exit-form") }}</h4>
                        </div>
                    </div>
                    <?php } ?>
                    	{!! Form::open(array( 'id '=> 'add-initiate-exit-form' , 'method' => 'post' , 'files' => true )) !!}
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info"> HR will take the final action on the exit request. </div>
                            </div>
                            <div class="col-lg-12">
								<div class="form-group">
									<label class="control-label" for="initiating_exit_reason">What is the reason for initiating the exit ?<span class="text-danger">*</span></label>
									<div class="d-flex">
										<div class="form-check pr-3">
											<input class="form-check-input" type="radio" name="initiating_exit_reason" onclick="showInitiateExitField(this);" id="employee_wants" value="{{ config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') }}" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_initiate_type)) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) ) ? 'checked' : '' )  }}>
											<label class="form-check-label" for="employee_wants">
												Employee wants to resign
											</label>
										</div>

										<div class="form-check">
											<input class="form-check-input" type="radio" name="initiating_exit_reason" onclick="showInitiateExitField(this);"  id="company_decide" value="{{ config('constants.EMPLOYER_INITIATE_EXIT_TYPE') }}" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_initiate_type)) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) ) ? 'checked' : '' )  }}>
											<label class="form-check-label" for="company_decide">
												Company decides to terminate
											</label>
										</div>
									</div>
								</div>
                            </div>
							<div class="col-lg-12">
								<div class="row">
									<div class="col-sm-6 pr-0">
		                                <label class="control-label" for="discussion_with_employee">Did you have discussion with employee on their decision ?<span class="text-danger">*</span></label>
		                                <div class="d-flex form-group">
		                                    <div class="form-check pr-3">
		                                        <input class="form-check-input" data-duration="{{ ( isset($noticePeriodDuration) ? $noticePeriodDuration : '' )  }}" type="radio" value="{{ config('constants.SELECTION_YES') }}" name="initial_exit_discussion_with_employee" id="initial_exit_discussion_with_employee_yes" onclick="showExitSummryDiv(this);" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_employee_discuss)) && ( $resignInfo->e_employee_discuss == config('constants.SELECTION_YES') ) ) ? 'checked' : '' )  }}>
		                                        <label class="form-check-label" for="initial_exit_discussion_with_employee_yes">{{ trans('messages.yes') }}</label>
		                                    </div>
		
		                                    <div class="form-check">
		                                        <input class="form-check-input" type="radio" value="{{ config('constants.SELECTION_NO') }}" name="initial_exit_discussion_with_employee" {{ (!isset($resignInfo) ? 'checked' : '' )  }}  id="discussion_with_employee_no" onclick="showExitSummryDiv(this);" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_employee_discuss)) && ( $resignInfo->e_employee_discuss == config('constants.SELECTION_NO') ) ) ? 'checked' : '' )  }}>
		                                        <label class="form-check-label" for="initial_exit_discussion_with_employee_no">{{ trans('messages.no') }}</label>
		                                    </div>
		                                </div>
		                            </div>
		                            <div class="col-lg-6 col-sm-6 exit-summary-div"  {{ ( (isset($resignInfo) && (!empty($resignInfo->e_employee_discuss)) && ( $resignInfo->e_employee_discuss == config('constants.SELECTION_YES') ) ) ? '' : "style=display:none" )  }}>
		                                <div class="form-group">
		                                    <label class="control-label" for="discussion_with_employee_summary">{{ trans('messages.summary') }}<span class="text-danger">*</span></label>
		                                    <textarea class="form-control" name="initial_exit_discussion_with_employee_summary" placeholder="{{ trans('messages.summary') }}">{{ ( ( (isset($resignInfo) && (!empty($resignInfo->v_discuss_summary)) ) ) ? $resignInfo->v_discuss_summary : '' )  }}</textarea>
		                                </div>
		                            </div>
								</div>
							</div>
							<div class="col-sm-6 termination-reason-div employer-selection-div"  {{ ( (isset($resignInfo) && (!empty($resignInfo->e_initiate_type)) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) ) ? '' : "style=display:none" )  }}>
                                <div class="form-group">
                                    <label class="control-label" for="reason_for_termination">Reason for termination<span class="text-danger">*</span></label>
                                    <select class="form-control select2 required" name="initial_exit_reason_for_termination" >
                                        <option value="">Select</option>
                                        @if(count($terminationReasonDetails) > 0 )
                                        	@foreach($terminationReasonDetails as $terminationReasonDetail)
                                        		@php 
                                        		$encodeTerminationId = Wild_tiger::encode($terminationReasonDetail->i_id);
                                        		$selected = '';
                                        		if( isset($resignInfo) && ( $resignInfo->i_termination_reason_id ==  $terminationReasonDetail->i_id ) ){
                                        			$selected = "selected='selected'";
                                        		} 
                                        		@endphp
                                        		<option value="{{ $encodeTerminationId }}" {{ $selected }}>{{ $terminationReasonDetail->v_value }}</option>
                                        	@endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-5 col-sm-6 resign-reason-div employee-selection-div"  {{ ( (isset($resignInfo) && (!empty($resignInfo->e_initiate_type)) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) ) ? '' : "style=display:none" )  }}>
                                <div class="form-group">
                                    <label class="control-label" for="reason_for_resignation">Please select a reason for resignation<span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="initial_exit_reason_for_resignation">
                                        <option value="">Select</option>
                                        @if(count($resignationReasonDetails) > 0 )
                                        	@foreach($resignationReasonDetails as $resignationReasonDetail)
                                        		@php 
                                        		$encodeResignId = Wild_tiger::encode($resignationReasonDetail->i_id);
                                        		$selected = '';
                                        		if( isset($resignInfo) && ( $resignInfo->i_resign_reason_id ==  $resignationReasonDetail->i_id ) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		 
                                        		@endphp
                                        		<option value="{{ $encodeResignId }}" {{ $selected }}>{{ $resignationReasonDetail->v_value }}</option>
                                        	@endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-5 col-sm-6 termination-notice-end-date employer-selection-div" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_initiate_type)) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) ) ? '' : "style=display:none" )  }}>
                                <div class="form-group">
                                    <label class="control-label" for="termination_date">What was the termination notice date ?<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" data-notice-duration-value="{{ ( isset($employeeInfo->noticePeriodInfo->v_probation_period_duration) ? $employeeInfo->noticePeriodInfo->v_probation_period_duration : '' ) }}" data-notice-duration-selection="{{ ( isset($employeeInfo->noticePeriodInfo->e_months_weeks_days) ? $employeeInfo->noticePeriodInfo->e_months_weeks_days : '' ) }}" name="initial_exit_termination_date" placeholder="{{ config('constants.DEFAULT_DATE_FORMAT') }}"  value="{{ ( ( (isset($resignInfo) && (!empty($resignInfo->dt_termination_notice_date)) ) ) ? clientDate( $resignInfo->dt_termination_notice_date ) : clientDate(date('Y-m-d')) )  }}"/>
                                </div>
                            </div>

                            <div class="col-lg-5 col-sm-6 resign-notice-end-date employee-selection-div" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_initiate_type)) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') ) ) ? '' : "style=display:none" )  }}>
                                <div class="form-group">
                                    <label class="control-label" for="employee_provide_notice_exit">When did employee provide the notice of exit?<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="initial_exit_employee_provide_notice_exit_date" placeholder="{{ config('constants.DEFAULT_DATE_FORMAT') }}" value="{{ ( ( (isset($resignInfo) && (!empty($resignInfo->dt_employee_notice_date)) ) ) ? clientDate( $resignInfo->dt_employee_notice_date ) : clientDate(date('Y-m-d')) )  }}"/>
                                </div>
                            </div>
                            @php
                            $noticePeriodDate = '';
                          	if( isset($employeeInfo->noticePeriodInfo->v_probation_period_duration) && isset($employeeInfo->noticePeriodInfo->e_months_weeks_days) ){
                            	$noticePeriodDuration = $employeeInfo->noticePeriodInfo->v_probation_period_duration .  (!empty($employeeInfo->noticePeriodInfo->e_months_weeks_days) ? ' ' . $employeeInfo->noticePeriodInfo->e_months_weeks_days : '' );
                            	if ( (!empty($noticePeriodDuration)) && (!empty($employeeInfo->dt_joining_date)) ) {
                            	 	$noticePeriodDate = date('Y-m-d' , strtotime( $employeeInfo->dt_joining_date . " +" . $noticePeriodDuration ));	
                            	}
                           	}
                           	
                           	if( isset($resignInfo) && ( $resignInfo->e_last_working_day ==  config('constants.NOTICE_PERIOD') ) && (!empty($resignInfo->dt_system_last_working_date)) ){
                           		$noticePeriodDate = $resignInfo->dt_system_last_working_date;
                            }
                            
                            if( isset($resignInfo) && ( $resignInfo->e_last_working_day ==  config('constants.OTHER') ) && (!empty($resignInfo->dt_last_working_date)) ){
                           		$noticePeriodDate = $resignInfo->dt_last_working_date;
                            }
                            
                            if( isset($resignInfo) && (!empty($resignInfo)) ){
                            	$noticePeriodDate = $resignInfo->dt_system_last_working_date;
                            }
                           	
                            @endphp
							<div class="col-lg-12">
								<div class="row">
									<div class="col-lg-6">
		                                <div class="form-group">
		                                    <label class="control-label" for="recommend_last_working_day">Do you want to recommend a Last Working Day ? <span class="text-danger">*</span><a type="button" class="ml-2" data-toggle="tooltip" data-placement="right" title="This last working day is applicable if employee continues with the exit process."><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
		
		                                    <div class="">
		                                        <div class="form-check pr-3">
		                                            <input class="form-check-input" type="radio" name="initial_exit_recommend_last_working_day_type" <?php echo ( (!isset($resignInfo)) ? 'checked' : '' ) ?> onclick="showOtherDateDiv(this);" id="complete_period" value="{{ config('constants.NOTICE_PERIOD') }}" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_last_working_day)) && ( $resignInfo->e_last_working_day == config('constants.NOTICE_PERIOD') ) ) ? 'checked' : '' )  }}>
		                                            <label class="form-check-label" for="complete_period">Complete notice period <span class="text-muted notice-period-completion-date"> {{ (!empty($noticePeriodDate) ? '('. convertDateFormat($noticePeriodDate) . ')' : '' ) }}</span></label>
		                                        </div>
		                                        <div class="form-check">
		                                            <input class="form-check-input" type="radio" name="initial_exit_recommend_last_working_day_type"  onclick="showOtherDateDiv(this);" id="other_date" value="{{ config('constants.OTHER') }}" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_last_working_day)) && ( $resignInfo->e_last_working_day == config('constants.OTHER') ) ) ? 'checked' : '' )  }}>
		                                            <label class="form-check-label" for="other_date">{{ trans('messages.other-date') }}</label>
		                                        </div>
		                                    </div>
		                                </div>
		                            </div>
		
		                            <div class="col-lg-6 other-last-working-date" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_last_working_day)) && ( $resignInfo->e_last_working_day == config('constants.OTHER') ) ) ? '' : "style=display:none" )  }}>
		                                <div class="form-group">
		                                    <label class="control-label" for="initial_exit_last_working_date">Last Working Day<span class="text-danger">*</span></label>
		                                    <input type="text" class="form-control" name="initial_exit_other_last_working_date" placeholder="DD-MM-YYYY" value="{{ ( ( (isset($resignInfo) && (!empty($resignInfo->dt_last_working_date)) ) ) ? clientDate( $resignInfo->dt_last_working_date ) : '' )  }}" />
		                                </div>
		                            </div>
								</div>
							</div>
							<div class="col-lg-6 ok-to-hire-div employee-selection-div"  {{ ( (isset($resignInfo) && (!empty($resignInfo->e_initiate_type)) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYER_INITIATE_EXIT_TYPE') ) ) ? "style=display:none" : "" )  }} >
                                <div class="form-group custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="initiating_exit_ok_to_hire" id="initiating_exit_ok_to_hire" value="{{ config('constants.SELECTION_YES')  }}" <?php echo ( !isset($resignInfo) ? 'checked' : '' ) ?>   {{ ( (isset($resignInfo) && (!empty($resignInfo->e_rehire_status)) && ( $resignInfo->e_rehire_status == config('constants.SELECTION_YES') ) ) ? 'checked' : '' )  }}>
                                    <label class="custom-control-label" for="initiating_exit_ok_to_hire">Ok to Rehire?<span class="text-danger">*</span></label>
                                    <label id="ok_to_hire-error" class="invalid-input" for="initiating_exit_ok_to_hire"></label>
                                </div>
                            </div>
                           
                            <div class="col-lg-6">
                                <input type="file" name="initiating_exit_file_upload"  class="my-custome-file custom-file-label" id="initiating-exit-file-upload" onchange="galleryMultipleDocumentPreview(this)">
                                <label for="initiating-exit-file-upload" class="my-custome-file-label"><i class="fas fa-paperclip"></i> {{ trans("messages.add-attachment") }}</label>
                                <a type="button" class="ml-2" data-toggle="tooltip" data-placement="right" title="Allowed formats are .png, jpg, jpeg, .doc,
                             	.docx, .pdf, .xlsx, xls. The file size should not exceed {{ config('constants.UPLOAD_FILE_LIMIT_SIZE') }}MB."><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                                <div id="file-upload-filename" class="file-upload-filename"></div>
                                <div class="row">
								   <div class="col-lg-12">
		                            	<div class="initiating-exit-file-upload-preview-div col-lg-12">
			                            	<?php 
			                            	if ((isset($resignInfo)) && ($resignInfo->v_attachment)){
			                            		if (!empty($resignInfo) && file_exists(config('constants.FILE_STORAGE_PATH').config('constants.UPLOAD_FOLDER') . $resignInfo->v_attachment)) {
													$documentFileName =  config('constants.FILE_STORAGE_PATH_URL') .  config('constants.UPLOAD_FOLDER') .  $resignInfo->v_attachment;
													?>
													<div class="gallery-image-div">
													<div class="row justify-content-between align-items-center">
														<div class="upload-main-image">
															<label class="pr-2 image-label">{{ (isset($documentFileName) ? basename($documentFileName) : '') }}</label>
														</div>
													<div class="close-buttons">
														<button type="button" class="btn btn-danger button-round" onclick="removeImageHtml(this)" data-field-name="{{ (isset($documentFileName) ? basename($documentFileName) : '') }}" data-preview-name="{{ (isset($documentFileName) ? basename($documentFileName) : '') }}"><i class="fas fa-times"></i></button>
													</div>
													</div>
												</div>
													<?php 
			                            		}
			                            	} 
			                            	?>
		                            	</div>
		                            </div>
								</div>
                            </div>
                           
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="comment" class="control-label">Comment<span class="text-danger">*</span></label>
                                    <a type="button" class="ml-2" data-toggle="tooltip" data-placement="right" title="Employee will not be notified"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                                    <textarea name="initiating_exit_comment" class="form-control" cols="10" rows="3" placeholder="Comment">{{ ( ( (isset($resignInfo) && (!empty($resignInfo->v_remark)) ) ) ? $resignInfo->v_remark : '' )  }}</textarea>
                                </div>
                            </div>
                             @if( in_array(  session()->get('role') , [ config('constants.ROLE_ADMIN') ] )  &&  isset($resignInfo) && (!empty($resignInfo->e_status)) && ( $resignInfo->e_status == config('constants.APPROVED_STATUS') ) && ( isset($resignInfo->employee->latestSalaryMaster->e_pf_deduction) ) && ( $resignInfo->employee->latestSalaryMaster->e_pf_deduction == config('constants.SELECTION_YES')  ) )
                            <div class="col-lg-6 pf_exit_date 123">
								<div class="form-group">
		                        	<label class="control-label" for="pf_exit_date">{{ trans('messages.pf-exit-date') }}<span class="text-danger">*</span></label>
		                            <input type="text" class="form-control" name="pf_exit_date" placeholder="DD-MM-YYYY" value="{{ ( ( (isset($resignInfo) && (!empty($resignInfo->employee->dt_pf_expiry_date)) ) ) ? clientDate( $resignInfo->employee->dt_pf_expiry_date ) : '' )  }}" />
		                       	</div>
		                     </div>
		                     @endif
                        </div>
                         <input type="hidden" name="remove_image" value="">
                        <input type="hidden" name="notice_period_duration" value="{{ ( ( isset($noticePeriodDuration) && ($noticePeriodDuration) ) ? $noticePeriodDuration : '' ) }}">
                        <input type="hidden" name="employee_joining_date" value="{{ ( ( isset($empJoiningDate) && ($empJoiningDate) ) ? $empJoiningDate : '' ) }}">
                        <input type="hidden" name="initiate_exit_employee_id" value="{{ ( ( isset($employeeId) && ($employeeId ) ) ? Wild_tiger::encode($employeeId) : '' ) }}">
                        <div class="modal-footer justify-content-end">
                        	<?php if( isset($resignInfo) && (!empty($resignInfo)) ) { ?>
                            <button type="button" onclick="addInitiateExit(this);" data-record-status="{{ ( isset($resignInfo->e_status) ? $resignInfo->e_status : '' ) }}"  class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                            <?php } else { ?>
                            <button type="button" onclick="addInitiateExit(this);"  class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
                            <?php } ?>
                            <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.cancel') }}">{{ trans('messages.cancel') }}</button>
                        </div>
                    	{!! Form::close() !!}
                    	<script>
                    	$("#add-initiate-exit-form").validate({
	    	 		        errorClass: "invalid-input",
	    	 		        rules: {
	    	 		        	initiating_exit_reason: {
	    	 		                required: true
	    	 		            },
	    	 		            initial_exit_discussion_with_employee: {
	    	 		                required: true
	    	 		            },
	    	 		            initial_exit_discussion_with_employee_summary: {
	    	 		            	required: function(){
	    	 							return ( ( $.trim($("[name='initial_exit_discussion_with_employee']:checked").val()) == '{{ config("constants.SELECTION_YES") }}' ) ? true : false ) 
	    	 		                },
	    	 		            },
	    	 		            initial_exit_reason_for_termination: {
	    	 		                required: function(){
	    	 							return ( ( $.trim($("[name='initiating_exit_reason']:checked").val()) == '{{ config("constants.EMPLOYER_INITIATE_EXIT_TYPE") }}' ) ? true : false ) 
	    	 		                },
	    	 		            },
	    	 		            initial_exit_reason_for_resignation: {
	    	 		                required: function(){
	    	 							return ( ( $.trim($("[name='initiating_exit_reason']:checked").val()) == '{{ config("constants.EMPLOYEE_INITIATE_EXIT_TYPE") }}' ) ? true : false ) 
	    	 		                },
	    	 		            },
	    	 		            initial_exit_termination_date: {
	    	 		                required: function(){
	    	 							return ( ( $.trim($("[name='initiating_exit_reason']:checked").val()) == '{{ config("constants.EMPLOYER_INITIATE_EXIT_TYPE") }}' ) ? true : false ) 
	    	 		                },
	    	 		            },
	    	 		            initial_exit_employee_provide_notice_exit_date: {
	    	 		                required: function(){
	    	 							return ( ( $.trim($("[name='initiating_exit_reason']:checked").val()) == '{{ config("constants.EMPLOYEE_INITIATE_EXIT_TYPE") }}' ) ? true : false ) 
	    	 		                },
	    	 		            },
	    	 		           	initial_exit_recommend_last_working_day_type: {
	    	 		                required: true
	    	 		            },
	    	 		            initial_exit_other_last_working_date: {
	    	 		            	required: function(){
	    	 							return ( ( $.trim($("[name='initial_exit_recommend_last_working_day_type']:checked").val()) == '{{ config("constants.OTHER") }}' ) ? true : false ) 
	    	 		                },
	    	 		            },
								 initiating_exit_comment: {
	    	 		                required: true
	    	 		            },
	    	 		            pf_exit_date: {
	                                required:true
	                            },
	    	 		        },
	    	 		        messages: {
	    	 		        	 initiating_exit_reason: {
	    	 		                 required: "{{ trans('messages.require-initiating-exit-reason') }}"
	    	 		             },
	    	 		             initial_exit_discussion_with_employee: {
	    	 		                 required: "{{ trans('messages.require-discussion-with-employee-decision') }}"
	    	 		             },
	    	 		            initial_exit_discussion_with_employee_summary: {
	    	 		                 required: "{{ trans('messages.require-summary-of-the-discussion') }}"
	    	 		             },
	    	 		             initial_exit_reason_for_termination: {
	    	 		                 required: "{{ trans('messages.require-please-select-reason-for-termination') }}"
	    	 		             },
	    	 		             initial_exit_reason_for_resignation: {
	    	 		                 required: "{{ trans('messages.require-please-select-reason-for-resignation') }}"
	    	 		             },
	    	 		             initial_exit_termination_date: {
	    	 		                 required: "{{ trans('messages.require-termination-date') }}"
	    	 		             },
	    	 		             initial_exit_employee_provide_notice_exit_date: {
	    	 		                 required: "{{ trans('messages.require-employee-provide-notice-exit') }}"
	    	 		             },
	    	 		             initial_exit_recommend_last_working_day_type: {
	    	 		                 required: "{{ trans('messages.require-last-working-day') }}"
	    	 		             },
	    	 		             initial_exit_other_last_working_date: {
	    	 		                 required: "{{ trans('messages.require-forresign-last-working-date') }}"
	    	 		             },
	    	 		             initiating_exit_comment: {
	    	 		                required: "{{ trans('messages.required-comment') }}"
	    	 		            },
	    	 		           pf_exit_date: {
	                                 required: "{{ trans('messages.require-pf-exit-date') }}"
	                             },
	    	 		        }
	    	 		    });
	    	 	 		$(' [name="initial_exit_termination_date"], [name="initial_exit_employee_provide_notice_exit_date"], [name="initial_exit_other_last_working_date"], [name="pf_exit_date"] ').datetimepicker({
	    	                useCurrent: false,
	    	                viewMode: 'days',
	    	                ignoreReadonly: true,
	    	                format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
	    	                showClear: true,
	    	                showClose: true,
	    	                widgetPositioning: {
	    	                    vertical: 'bottom',
	    	                    horizontal: 'auto'

	    	                },
	    	                icons: {
	    	                    clear: 'fa fa-trash',
	    	                    Close: 'fa fa-trash',
	    	                },
	    	            });
	    	 	 		$('[name="initial_exit_termination_date"]').datetimepicker().on('dp.change', function(e) {
				 			calculateTerminateNoticePeriodEndDate();
				 	 	});

	    	 	 		$('[name="initial_exit_employee_provide_notice_exit_date"]').datetimepicker().on('dp.change', function(e) {
				 			calculateResignNoticePeriodEndDate();
				 	 	});
	    	 	 		
				 	 	function calculateTerminateNoticePeriodEndDate(){
					 	 	var initial_exit_termination_date = $.trim($("[name='initial_exit_termination_date']").val());
					 	 	var initial_exit_other_last_working_date = $.trim($("[name='initial_exit_other_last_working_date']").val());
					 	 	var notice_period_duration =  $.trim($("[name='initial_exit_termination_date']").attr("data-notice-duration"));
							var duration_value = $.trim($("[name='initial_exit_termination_date']").attr("data-notice-duration-value")) ;
							var duration_selection = $.trim($("[name='initial_exit_termination_date']").attr("data-notice-duration-selection")) ;	

					 	 	if( ( duration_value != "" && duration_value != null ) && ( duration_selection != "" && duration_selection != null )  ){
					 	 		 var notice_period_completed_date = moment(initial_exit_termination_date, 'DD-MM-YYYY').add( duration_value , duration_selection );
								 $(".notice-period-completion-date").html( " (" +  moment(notice_period_completed_date).format('DD MMM, YYYY') + ")" );
					 	 	}
					 	 	//console.log("last working date");
					 	 	//console.log(moment(initial_exit_termination_date,'DD-MM-YYYY').format('DD-MM-YYYY'));
					 	 	if( initial_exit_termination_date != "" && initial_exit_termination_date != null ){
								$("[name='initial_exit_other_last_working_date']").data("DateTimePicker").minDate(moment(initial_exit_termination_date,'DD-MM-YYYY').format('DD-MM-YYYY'));
						 	} else {
						 		$("[name='initial_exit_other_last_working_date']").data("DateTimePicker").minDate(false);
							}

					 	 	if( initial_exit_other_last_working_date != "" && initial_exit_other_last_working_date != null ){
						 	 	if( moment(initial_exit_other_last_working_date,'DD-MM-YYYY').isBefore(moment(initial_exit_termination_date,'DD-MM-YYYY')) == true ){
						 	 		$("[name='initial_exit_other_last_working_date']").val(initial_exit_termination_date);
							 	}	
						 	} else {

							}
							
					 	 }

				 	 	function calculateResignNoticePeriodEndDate(){
					 	 	var initial_exit_termination_date = $.trim($("[name='initial_exit_employee_provide_notice_exit_date']").val());
					 	 	var notice_period_duration =  $.trim($("[name='initial_exit_termination_date']").attr("data-notice-duration"));
							var duration_value = $.trim($("[name='initial_exit_termination_date']").attr("data-notice-duration-value")) ;
							var duration_selection = $.trim($("[name='initial_exit_termination_date']").attr("data-notice-duration-selection")) ;	

					 	 	if( ( duration_value != "" && duration_value != null ) && ( duration_selection != "" && duration_selection != null )  ){
					 	 		 var notice_period_completed_date = moment(initial_exit_termination_date, 'DD-MM-YYYY').add( duration_value , duration_selection );
								 $(".notice-period-completion-date").html( " (" +  moment(notice_period_completed_date).format('DD MMM, YYYY') + ")" );
					 	 	}

					 	 	//console.log("last working date");
					 	 	//console.log(moment(initial_exit_termination_date,'DD-MM-YYYY').format('DD-MM-YYYY'));
					 	 	
					 	 	if( initial_exit_termination_date != "" && initial_exit_termination_date != null ){
								$("[name='initial_exit_other_last_working_date']").data("DateTimePicker").minDate(moment(initial_exit_termination_date,'DD-MM-YYYY').format('DD-MM-YYYY'));
						 	} else {
						 		$("[name='initial_exit_other_last_working_date']").data("DateTimePicker").minDate(false);
							}
					 	 	
					 	 }
                    	</script>