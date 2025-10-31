					
					<div class="row">
						<div class="col-12 col-sm-4">
                            <label class="control-label">{{ trans("messages.report-no") }}</label>
                            <p>{{(!empty($incidentReportInfo->v_report_no) ? ($incidentReportInfo->v_report_no) :'')}}</p>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="control-label">{{ trans("messages.report-date") }}</label>
                            <p>{{(!empty($incidentReportInfo->dt_report_date) ? convertDateFormat($incidentReportInfo->dt_report_date) :'')}}</p>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="control-label">{{ trans("messages.employee-name") }}</label>
                            <p>
                            @if(!empty($incidentReportInfo->employee))
                            	@foreach ($incidentReportInfo->employee as $employeeInfo)
                            		{{ (!empty($employeeInfo->v_employee_full_name) ? $employeeInfo->v_employee_full_name .
                            			(!empty($employeeInfo->v_employee_code) ? ' (' .$employeeInfo->v_employee_code .')' .(!$loop->last ? ', '  : '') : '' )  : ''); 
                            		}}
                            	@endforeach
                            	@endif
                           </p>
                        </div>
                        @if(!empty($incidentReportInfo->v_subject))
	                        <div class="col-12 col-sm-4">
	                            <label class="control-label">{{ trans("messages.subject") }}</label>
	                            <p>{{(!empty($incidentReportInfo->v_subject) ? ($incidentReportInfo->v_subject) :'')}}</p>
	                        </div>
                        @endif
                        @if(!empty($incidentReportInfo->v_went_wrong))
	                        <div class="col-12">
	                            <label class="control-label">{{ trans("messages.what-went-wrong-?") }}</label>
	                            <span>{!! (!empty($incidentReportInfo->v_went_wrong) ? html_entity_decode($incidentReportInfo->v_went_wrong) :'') !!}</span>
	                        </div>
                        @endif
                        @if(!empty($incidentReportInfo->v_actions_taken))
	                        <div class="col-12">
	                            <label class="control-label">{{ trans("messages.what-actions-have-been-taken-?") }}</label>
	                            <span>{!! (!empty($incidentReportInfo->v_actions_taken) ? html_entity_decode($incidentReportInfo->v_actions_taken) :'') !!}</span>
	                        </div>
                        @endif
                        @if(!empty($incidentReportInfo->v_prevent_in_future))
	                        <div class="col-12">
	                            <label class="control-label">{{ trans("messages.what-can-we-do-to-prevent-in-future-?") }}</label>
	                            <span>{!! (!empty($incidentReportInfo->v_prevent_in_future) ? html_entity_decode($incidentReportInfo->v_prevent_in_future) :'') !!}</span>
	                        </div>
                        @endif
                        @if(!empty($incidentReportInfo->v_comments))
	                        <div class="col-12">
	                            <label class="control-label">{{ trans("messages.hr-comments") }}</label>
	                            <span>{!! (!empty($incidentReportInfo->v_comments) ? html_entity_decode($incidentReportInfo->v_comments) :'') !!}</span>
	                        </div>
                        @endif
                        
                        <div class="col-sm-4 col-6">
                            <label class="control-label">{{ trans("messages.status") }}</label>
                            <p>{{(!empty($incidentReportInfo->e_status) ? ($incidentReportInfo->e_status) :'')}}</p>
                        </div>
                       @if(!empty($incidentReportInfo->dt_close_date))
	                       <div class="col-sm-4 col-6">
	                            <label class="control-label">{{ trans("messages.closed-date") }}</label>
	                            <p>{{(!empty($incidentReportInfo->dt_close_date) ? convertDateFormat($incidentReportInfo->dt_close_date) :'')}}</p>
	                        </div>
                        @endif
                        @if(!empty($incidentReportInfo->v_remarks))
	                        <div class="col-12">
	                            <label class="control-label">{{trans("messages.remarks")}}</label>
	                            <p>{{(!empty($incidentReportInfo->v_remarks) ? $incidentReportInfo->v_remarks :'')}}</p>
	                        </div>
                       @endif
                       @if(!empty($incidentReportInfo->incidentAttachment) && count($incidentReportInfo->incidentAttachment) > 0 )
                       <div id="documents" class="col-12">
                            <div class="pb-0">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group pb-3 pt-3">
                                            <div class="card shadow-none border">
                                                <div class="card-header">
                                                    <h5 class="partner-tilte">
                                                        {{ trans("messages.attachment") }}
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive overflow-y-hidden">
                                                        <table class="table table-hover table-bordered table-sm pb-4">
                                                            <thead>
                                                                <tr class="text-center">
                                                                    <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                                                    <th style="max-width:250px;min-width:250px;">{{ trans("messages.documents") }} </th>
                                                                    <th style="max-width:250px;min-width:200px;">{{ trans("messages.remarks") }} </th>
                                                                    <th style="width:70px;min-width:70px;">{{ trans("messages.view") }}</th>
                                                                    <th class="actions-col" style="width:70px;min-width:70px;">{{ trans("messages.action") }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="list-of-other-attachment-div-main">
                                                                <?php 
                                                                if(!empty($incidentReportInfo->incidentAttachment)){
                                                                	$count= 0;
																	foreach ($incidentReportInfo->incidentAttachment as $documentFile){
																		
																		$documentFileName = "";
																		if (!empty($documentFile) && file_exists(config('constants.FILE_STORAGE_PATH').config('constants.UPLOAD_FOLDER') . $documentFile->v_file_path)) {
																			$documentFileName =  config('constants.FILE_STORAGE_PATH_URL') .  config('constants.UPLOAD_FOLDER') .  $documentFile->v_file_path;
																			?>
                                                                		<!-- show at update time -->
		                                                                <tr class="list-of-other-attachment-div">
		                                                                    <td class="table-index text-center" style="width:70px;min-width:70px;">{{ ++$count }}</td>
		                                                                    <td class="text-left">
		                                                                        <div class="custom-file overflow-visible">
		                                                                             <label class="pr-2 image-label">{{ (isset($documentFileName) ? basename($documentFileName) : '') }}</label>
		                                                                        </div>
		                                                                    </td>
		                                                                    <td class="text-left">
		                                                                        <input type="text" class="form-control" disabled value="{{(!empty($documentFile->v_remarks) ? ($documentFile->v_remarks) :'')}}">
		                                                                    </td>
		                                                                    <td class="actions-button">
		                                                                        <div class="download-link-items">
		                                                                            <a href="{{ $documentFileName }}" title='{{ trans("messages.view") }}' target="_blank"  class="btn btn-sm bg-theme btn-submit-class text-white"><i class="fa fa-eye"></i></a>
																				
		                                                                        </div>
		                                                                    </td>
		                                                                    <td style="width:70px;min-width:70px;" class="text-center">
																				<a href="{{ $documentFileName }}" title='{{ trans("messages.download") }}' download class="btn-success btn btn-sm btn-primary btn-submit-class text-white"><i class="fa fa-download"></i></a>
																			
		                                                                    </td>
		                                                                </tr>
                                                                		<?php
																		}
                                                                	}
                                                                } else{ ?>
                                                                	<tr>
																		<td colspan="5" class="text-center">{{ trans('messages.no-record-found')}}</td>
																	</tr>
                                                                	<?php 
                                                                	
                                                                }
															?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>