					
					<div class="row">
                        <div class="col-12 col-sm-4">
                            <label class="control-label">{{ trans("messages.employee-name") }}</label>
                            <p>{{(!empty($employeeRecordInfo->employeeInfo->v_employee_full_name) ? $employeeRecordInfo->employeeInfo->v_employee_full_name .(!empty($employeeRecordInfo->employeeInfo->v_employee_code) ? ' (' .$employeeRecordInfo->employeeInfo->v_employee_code .')' : '' ) : '')}}<br> {{(!empty($employeeRecordInfo->employeeInfo->v_contact_no) ? $employeeRecordInfo->employeeInfo->v_contact_no :'')}}</p>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="control-label">{{ trans("messages.leave-dates") }}</label>
                            <p>{{(!empty($employeeRecordInfo->dt_leave_from_date) ? convertDateFormat($employeeRecordInfo->dt_leave_from_date) .(!empty($employeeRecordInfo->dt_leave_to_date) ? ' - '.convertDateFormat($employeeRecordInfo->dt_leave_to_date) :''):'')}} <br>{{ (!empty($employeeRecordInfo->e_from_duration) ? $employeeRecordInfo->e_from_duration .(!empty($employeeRecordInfo->e_to_duration) ? ' - '. $employeeRecordInfo->e_to_duration :'') :'')}} {{(!empty($employeeRecordInfo->e_duration) ? $employeeRecordInfo->e_duration :'') }}</p>
                        </div>
                        <div class="col-sm-4 col-6">
                            <label class="control-label">{{ trans("messages.no-of-days") }}</label>
                            <p>{{(!empty($employeeRecordInfo->d_no_days) ? $employeeRecordInfo->d_no_days :'')}}</p>
                        </div>
                        <?php /* ?>
                        <div class="col-sm-4 col-6">
                            <label class="control-label">{{ trans("messages.leave-reason") }}</label>
                            <p>{{(!empty($employeeRecordInfo->v_leave_note) ? $employeeRecordInfo->v_leave_note :'')}}</p>
                        </div>
                       <?php */ ?> 
                        <?php /*
                         	if( (!empty($employeeRecordInfo->v_file)) && file_exists(storage_path('app/' . $employeeRecordInfo->v_file)) ){
                       			$fileName =  config('constants.FILE_STORAGE_PATH_URL') . $employeeRecordInfo->v_file;
                         	}
						*/?>
                       
						<?php if ((isset($employeeRecordInfo)) && ($employeeRecordInfo->v_file)){ ?>
						<div class="col-sm-12 col-12">
		                	<label class="control-label">{{ trans("messages.attachments") }}</label>
		                    <div class="col-lg-12">
							<?php
							$documentFiles = json_decode($employeeRecordInfo->v_file);
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
						
                        <div class="col-12">
                            <label class="control-label">{{trans("messages.note")}}</label>
                            <p>{{(!empty($employeeRecordInfo->v_leave_note) ? $employeeRecordInfo->v_leave_note :'')}}</p>
                        </div>
                        @if( isset($employeeRecordInfo->e_status) && ( in_array( $employeeRecordInfo->e_status , [ config('constants.PENDING_STATUS')] ) ) )
                        <div class="col-12" <?php echo ( isset($requestStatus) && ($requestStatus == config('constants.VIEW_RECORD')) ? 'style="display:none"' : '' )  ?>>
                            <label class="form-label control-label" name="reason">{{trans("messages.reason")}}<span class="text-danger">*</span></label>
                            <textarea for="reason" name="leave_approve_reject_reason" cols="30" rows="3" class="form-control">{{ (!empty($employeeRecordInfo->v_approve_reject_remark) ? $employeeRecordInfo->v_approve_reject_remark :'')}}</textarea>
                        </div>
                        <?php /*?>
                         <div class="col-12">
                            <label class="form-label control-label" name="reason">{{trans("messages.reason")}}</label>
                            <textarea disabled style="resize:none;" cols="30" rows="3" class="form-control">{{ (!empty($employeeRecordInfo->v_approve_reject_remark) ? $employeeRecordInfo->v_approve_reject_remark :'')}}</textarea>
                        </div>
                        
                        <?php */?>
                        @else
	                        @if( isset($employeeRecordInfo->e_status) && ( $employeeRecordInfo->e_status == config('constants.APPROVED_STATUS') ) && isset($requestStatus) && $requestStatus != config('constants.VIEW_RECORD') )
	                        <div class="col-12">
	                            <label class="form-label control-label" name="reason">{{trans("messages.reason")}}<span class="text-danger">*</span></label>
	                            <textarea name="leave_approve_reject_reason" cols="30" rows="3" class="form-control"></textarea>
	                        </div>
	                        @else
		                        <div class="col-12">
		                            <label class="form-label control-label" name="reason">{{trans("messages.reason")}}</label>
		                            <textarea disabled style="resize:none;"  cols="30" rows="3" class="form-control">{{ (!empty($employeeRecordInfo->v_approve_reject_remark) ? $employeeRecordInfo->v_approve_reject_remark :'')}}</textarea>
		                        </div>
	                        @endif
	                   @endif
                    </div>