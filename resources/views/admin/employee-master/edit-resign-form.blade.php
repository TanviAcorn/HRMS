					@if( isset($resignInfo) && (!empty($resignInfo)) )
					<div class="row">
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
                         @if( ( isset( $resignInfo->e_initiate_type) ) && ( $resignInfo->e_initiate_type == config('constants.EMPLOYEE_INITIATE_EXIT_TYPE') )  )
                        <div class="col-lg-4 col-md-6">
                            <label class="control-label">When did employee provide the notice of exit ?</label>
                            <p>{{ ( isset($resignInfo->dt_employee_notice_date) && (!empty($resignInfo->dt_employee_notice_date)) ) ? convertDateFormat( $resignInfo->dt_employee_notice_date ) : ''  }}</p>
                        </div>
                        @endif
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
                        @if( isset($resignInfo->v_discuss_summary) && (!empty($resignInfo->v_discuss_summary)) )
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
                            <h4 class="address-title">{{ trans("messages.resignation-form") }}</h4>
                        </div>
                    </div>
                    @endif

                    {!! Form::open(array( 'id '=> 'add-resign-form' , 'method' => 'post' )) !!}
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info"> This will trigger your job resignation process! It is always advisable to talk to your management first before coming here! </div>
                            </div>
                            <div class="col-12">
	                            <div class="row">
		                            <div class="col-sm-6 pr-lg-4 pr-md-0 pr-4">
		                                <div class="form-group">
		                                    <label class="control-label" for="discussion_with_manager">Did you have discussion with manager on your decision to exit?<span class="text-danger">*</span></label>
		                                    <div class="d-flex">
		                                        <div class="form-check pr-3">
		                                            <input class="form-check-input" type="radio" name="resign_discussion_with_manager" onclick="showDiscussionWithManager(this);" id="resign-discussion-with-manager-yes" value="{{ config('constants.SELECTION_YES') }}" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_employee_discuss)) && ( $resignInfo->e_employee_discuss == config('constants.SELECTION_YES') ) ) ? 'checked' : '' )  }}>
		                                            <label class="form-check-label" for="resign-discussion-with-manager-yes">{{ trans('messages.yes') }}</label>
		                                        </div>
		
		                                        <div class="form-check">
		                                            <input class="form-check-input" type="radio" name="resign_discussion_with_manager" onclick="showDiscussionWithManager(this);" id="resign-discussion-with-manager-no" {{ ( !isset($resignInfo) ?  'checked' : '' ) }}  value="{{ config('constants.SELECTION_NO') }}" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_employee_discuss)) && ( $resignInfo->e_employee_discuss == config('constants.SELECTION_NO') ) ) ? 'checked' : '' )  }}>
		                                            <label class="form-check-label" for="resign-discussion-with-manager-no">{{ trans('messages.no') }}</label>
		                                        </div>
		                                    </div>
		                                    <label id="resign_discussion_with_manager-error" class="invalid-input" for="resign_discussion_with_manager"></label>
		                                </div>
		                            </div>
		
		                            <div class="col-sm-6 resign-discussion-with-manager-summary-of-discussion" <?php echo ( (isset($resignInfo) && (!empty($resignInfo->e_employee_discuss)) && ( $resignInfo->e_employee_discuss == config('constants.SELECTION_YES') ) ) ? '' : 'style=display:none;' ) ?>>
		                                <div class="form-group">
		                                    <label class="control-label" for="summary_of_discussion">Please write summary of the discussion<span class="text-danger">*</span></label>
		                                    <textarea name="resign_summary_of_discussion" class="form-control" cols="10" rows="3">{{ ( ( (isset($resignInfo) && (!empty($resignInfo->v_discuss_summary)) ) ) ? $resignInfo->v_discuss_summary : '' )  }}</textarea>
		                                </div>
		                            </div>
	                            </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="resign_reason_for_resignation">Please select a reason for resignation<span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="resign_reason_for_resignation">
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

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="comment_resign" class="control-label">Comment<span class="text-danger">*</span></label>
                                    <textarea name="resign_comment" class="form-control" cols="10" rows="3" placeholder="Comment">{{ ( ( (isset($resignInfo) && (!empty($resignInfo->v_remark)) ) ) ? $resignInfo->v_remark : '' )  }}</textarea>
                                </div>
                            </div>
							
							@if ( isset($noticePeriodDuration) && (!empty($noticePeriodDuration)) )	
                            <div class="col-12">
                                <div class="alert alert-info">As per the company policy, you are required to serve notice period of {{ $noticePeriodDuration }}{{ (!empty($noticePeriodLastDate) ? ', till ' .convertDateFormat($noticePeriodLastDate)  : '' ) }}.</div>
                            </div>
                            @endif
								
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label class="control-label">Any preference on an early last working day? <span class="text-muted">(optional)</span></label>
											<div class="d-flex">
												<div class="form-check pr-3">
													<input class="form-check-input" type="radio" onclick="resignPreferLastWorkingDay(this);"  name="resign_preference_last_working_day" id="preference_yes" value="{{ config('constants.SELECTION_YES') }}" {{ ( (isset($resignInfo) && (!empty($resignInfo->e_last_working_day)) && ( $resignInfo->e_last_working_day == config('constants.OTHER') ) ) ? 'checked' : '' )  }}>
													<label class="form-check-label" for="preference_yes">{{ trans('messages.yes') }}</label>
												</div>
			
												<div class="form-check">
													<input class="form-check-input" type="radio" onclick="resignPreferLastWorkingDay(this);" name="resign_preference_last_working_day" id="preference_no" value="{{ config('constants.SELECTION_NO') }}" {{ ( !isset($resignInfo) ?  'checked' : '' ) }} {{ ( (isset($resignInfo) && (!empty($resignInfo->e_last_working_day)) && ( $resignInfo->e_last_working_day != config('constants.OTHER') ) ) ? 'checked' : '' )  }}>
													<label class="form-check-label" for="preference_no">{{ trans('messages.no') }}</label>
												</div>
											</div>
										</div>
		                            </div>
		
		                            <div class="col-md-6 resign-preference-last-working-date" <?php echo ( (isset($resignInfo) && (!empty($resignInfo->e_last_working_day)) && ( $resignInfo->e_last_working_day == config('constants.OTHER') ) ) ? '' : 'style=display:none;' ) ?>>
		                                <div class="form-group">
		                                    <label class="control-label" for="employee_prefer_last_working_day">Last Working Day<span class="text-danger">*</span></label>
		                                    <input type="text" class="form-control" name="resign_preference_last_working_date" placeholder="{{ config('constants.DEFAULT_DATE_FORMAT') }}" value="{{ ( ( (isset($resignInfo) && (!empty($resignInfo->dt_last_working_date)) ) ) ? clientDate( $resignInfo->dt_last_working_date ) : '' )  }}" />
		                                </div>
		                            </div>
								</div>
							</div>		
                            
							<input type="hidden" name="resign_employee_id" value="{{ ( isset($employeeId) ? $employeeId : '' ) }}">
                            <div class="col-12">
                                <div class="alert alert-info">Company reserves the right to choose the last working day.</div>
                            </div>
                            
                            <?php if(  isset($resignInfo) && (isset($resignInfo->e_status)) && ( $resignInfo->e_status == config('constants.APPROVED_STATUS')  ) && ( isset($resignInfo->employee->latestSalaryMaster->e_pf_deduction) ) && ( $resignInfo->employee->latestSalaryMaster->e_pf_deduction == config('constants.SELECTION_YES')  ) ) { ?>
                            @if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) )
                            <div class="col-md-6 ">
		                       	<div class="form-group">
		                            <label class="control-label" for="pf_exit_date">{{ trans('messages.pf-exit-date') }}<span class="text-danger">*</span></label>
		                        	<input type="text" class="form-control" name="pf_exit_date" placeholder="{{ config('constants.DEFAULT_DATE_FORMAT') }}" value="{{ ( ( (isset($resignInfo) && (!empty($resignInfo->employee->dt_pf_expiry_date)) ) ) ? clientDate( $resignInfo->employee->dt_pf_expiry_date ) : '' )  }}" />
		                   		</div>
		                   </div>
		                   @endif
                            @if(count($allChildEmployeeDetails) > 0 )
	                            <div class="col-lg-12 approve-reject-field" >
									<div class="row">
										<div class="col-lg-6">
			                                <div class="table-responsive">
				                                <table class="table table-sm table-hover table-bordered">
				                                	<thead>
				                                		<tr>
				                                			<td class="text-center">{{ trans('messages.sr-no') }}</td>
				                                			<td class="text-left">{{ trans('messages.employee') }}</td>
				                                			<td class="text-center">{{ trans('messages.upcoming-leader') }}</td>
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
				                                								@php 
				                                								$allSelectedUpcomingLeaderDetails = (!empty($resignInfo->v_upcoming_leader_info) ? json_decode($resignInfo->v_upcoming_leader_info,true) : [] );
				                                								$allAssignedUpcomingLeaderDetails = (!empty($allSelectedUpcomingLeaderDetails) ? array_column($allSelectedUpcomingLeaderDetails,'employee_id') : [] ); 
				                                								@endphp
				                                								@foreach($allEmployeeDetails as $allEmployeeDetail)
				                                									@if( $allEmployeeDetail->i_id != $allChildEmployeeDetail->i_id )
				                                										@php
				                                										$selected = '';
				                                										if( in_array( $allChildEmployeeDetail->i_id , $allAssignedUpcomingLeaderDetails ) ){
				                                											$searchKey = array_search( $allChildEmployeeDetail->i_id , $allAssignedUpcomingLeaderDetails );
				                                											if(strlen($searchKey) > 0 ){
				                                												$assignedUpcomingLeaderId = ( isset($allSelectedUpcomingLeaderDetails[$searchKey]['leader_id']) ? $allSelectedUpcomingLeaderDetails[$searchKey]['leader_id'] : 0 );
				                                												if( $assignedUpcomingLeaderId == $allEmployeeDetail->i_id ){
				                                													$selected = "selected='selected'";
				                                												}
				                                											}
				                                											 	
				                                										}
				                                										@endphp
				                                										<option value="{{ Wild_tiger::encode($allEmployeeDetail->i_id)}}" {{ $selected }} >{{  ( isset($allEmployeeDetail->v_employee_full_name) ?  $allEmployeeDetail->v_employee_full_name : '' ) }}</option>
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
                            <?php } ?>
                        </div>
                        <div class="modal-footer justify-content-end">
                            <button type="button" onclick="addResignForm(this);" data-record-status="{{ ( isset($resignInfo->e_status) ? $resignInfo->e_status : '' ) }}" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.confirm') }}">{{ trans('messages.confirm') }}</button>
                            <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.cancel') }}">{{ trans('messages.cancel') }}</button>
                        </div>
                    {!! Form::close() !!}
                    <script>
                    $("#add-resign-form").validate({
                        errorClass: "invalid-input",
                        rules: {
                        	resign_discussion_with_manager: {
                                required: true
                            },
                           resign_summary_of_discussion: {
                                required: true
                            },
                           resign_reason_for_resignation: {
                                required: true
                            },
                           resign_comment: {
                                required: true
                            },
                           resign_preference_last_working_day: {
                                required: true
                            },
                           resign_preference_last_working_date: {
                                required: function(){
                					return ( $.trim($("[name='resign_preference_last_working_day']:checked").val()) == '{{ config("constants.SELECTION_YES") }}' ? true : false );
                	           	}
                            },
                            pf_exit_date: {
                                required:true
                            },
                        },
                        messages: {
                        	resign_discussion_with_manager: {
                                 required: "{{ trans('messages.require-discussion-with-manager') }}"
                             },
                            resign_summary_of_discussion: {
                                 required: "{{ trans('messages.require-summary-of-the-discussion') }}"
                             },
                            resign_reason_for_resignation: {
                                 required: "{{ trans('messages.require-please-select-reason-for-resignation') }}"
                             },
                            resign_comment: {
                                 required: "{{ trans('messages.require-resign-comment') }}"
                             },
                            resign_preference_last_working_day: {
                                 required: "{{ trans('messages.required-early-last-working-day') }}"
                             },
                            resign_preference_last_working_date: {
                                 required: "{{ trans('messages.require-forresign-last-working-date') }}"
                             },
                             pf_exit_date: {
                                 required: "{{ trans('messages.require-pf-exit-date') }}"
                             },
                        }
                    });
					$(' [name="resign_preference_last_working_date"],[name="pf_exit_date"]').datetimepicker({
    	                useCurrent: false,
    	                viewMode: 'days',
    	                ignoreReadonly: true,
    	                format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
    	                showClear: true,
    	                showClose: true,
    	                widgetPositioning: {
    	                    vertical: 'top',
    	                    horizontal: 'auto'

    	                },
    	                icons: {
    	                    clear: 'fa fa-trash',
    	                    Close: 'fa fa-trash',
    	                },
    	            });
                    </script>