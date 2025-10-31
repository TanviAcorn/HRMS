@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ $pageTitle }}</h1>
    </div>
    <div class="container-fluid pt-3 ">
    {!!Form::open(['id' => 'add-incident-report' , 'method' => 'post' , 'files' => 'true' , 'url' => 'incident-report/add'])!!}
        <div class="filter-result-wrapper">
            <div class="card card-body pb-0">
                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-sm-6 col-12">
                            <label for="date" class="control-label">{{ trans("messages.report-date") }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" {{ ( (isset($recordInfo) && ($recordInfo->e_status == config("constants.CLOSE")) ? "disabled" : '')) }}  name="date" value="{{ old( 'date' ,  (isset($recordInfo) && !empty($recordInfo->dt_report_date) ? clientDate($recordInfo->dt_report_date) : '')) }}" placeholder="{{ trans('messages.dd-mm-yyyy') }}" autocomplete="off"/>
                        </div>

                        <div class="col-sm-6 col-12">
                            <div class="form-group">
                                <label class="control-label" for="employee">{{ trans("messages.employee-name-code") }}<span class="text-danger">*</span></label>
                                <select class="form-control select2" name="employee[]" multiple>
                                <?php 
                                	$employeeIds = ((isset($recordInfo) && (!empty($recordInfo->v_employee_ids))) ? objectToArray($recordInfo->v_employee_ids) :"" );
                                	if(count($employeeDetails) > 0){
	                                	foreach ($employeeDetails as $employeeDetail){
	                                		$encodeRecordId = (!empty($employeeDetail->i_id) ? Wild_tiger::encode($employeeDetail->i_id) : 0);
	                                		$selected = '';
	                                		if (!empty($employeeIds) && in_array($employeeDetail->i_id, $employeeIds)){
	                                			$selected = "selected='selected'";
	                                		}
	                                		?>
	                                		<option value="{{ $encodeRecordId }}" {{ $selected }}>{{ ( !empty($employeeDetail->v_employee_full_name) ? $employeeDetail->v_employee_full_name . ' ('.( !empty($employeeDetail->v_employee_code) ? $employeeDetail->v_employee_code : '' )  . ')'  : '' ) }}</option>
	                                		<?php
	                                	}
	                                }
                                ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12 col-12">
                            <div class="form-group">
                                <label class="control-label" for="subject">{{ trans("messages.subject") }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="subject" value="{{ old( 'subject' ,  (isset($recordInfo) && !empty($recordInfo->v_subject) ? ($recordInfo->v_subject) : '')) }}" placeholder="{{ trans("messages.subject") }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="what_went_wrong">{{ trans("messages.what-went-wrong-?") }}</label>
                                <textarea class="form-control" rows="10" name="what_went_wrong" >{{ old( 'what_went_wrong' ,  (isset($recordInfo) && !empty($recordInfo->v_went_wrong) ? html_entity_decode($recordInfo->v_went_wrong) : '')) }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="what_actions_have_been_taken">{{ trans("messages.what-actions-have-been-taken-?") }}</label>
                                <textarea class="form-control" rows="10" name="what_actions_have_been_taken">{{ old( 'what_actions_have_been_taken' ,  (isset($recordInfo) && !empty($recordInfo->v_actions_taken) ? html_entity_decode($recordInfo->v_actions_taken) : '')) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="what_we_do_prevent_in_future">{{ trans("messages.what-can-we-do-to-prevent-in-future-?") }}</label>
                                <textarea class="form-control" rows="6" name="what_we_do_prevent_in_future">{{ old( 'what_we_do_prevent_in_future' ,  (isset($recordInfo) && !empty($recordInfo->v_prevent_in_future) ? html_entity_decode($recordInfo->v_prevent_in_future) : '')) }}</textarea>
                            </div>
                        </div>
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
                                                    <div class="table-responsive">
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
                                                                if ((isset($recordInfo)) && count($recordInfo['incidentAttachment']) > 0 ){
                                                                	$count = 0;
                                                                	foreach ($recordInfo['incidentAttachment'] as $attachmentDetail){
                                                                		$filePath = (!empty(getUploadAsset($attachmentDetail->v_file_path)) ? getUploadAsset($attachmentDetail->v_file_path) : '');
                                                                		?>
                                                                		<!-- show at update time -->
		                                                                <tr class="list-of-other-attachment-div">
		                                                                    <td class="table-index text-center" style="width:70px;min-width:70px;">{{ ++$count }}</td>
		                                                                    <td class="text-left">
		                                                                        <div class="custom-file">
		                                                                            <input type="file" class="custom-file-input" name="edit_document_<?php echo $attachmentDetail->i_id?>" id="edit_document_<?php echo $attachmentDetail->i_id?>" onchange="imagePreview(this)">
		                                                                            <label class="custom-file-label" for="edit_document_<?php echo $attachmentDetail->i_id?>">{{ old('edit_document_'.$attachmentDetail->i_id , (!empty($attachmentDetail->v_file_path) ? basename($attachmentDetail->v_file_path) : trans('messages.choose-file'))) }}</label>
		                                                                        </div>
		                                                                    </td>
		                                                                    <td class="text-left">
		                                                                        <input type="text" class="form-control" name="edit_remark_<?php echo $attachmentDetail->i_id ?>" value="{{ old('edit_remark_'.$attachmentDetail->i_id, (!empty($attachmentDetail->v_remarks) ? $attachmentDetail->v_remarks : '')) }}">
		                                                                    </td>
		                                                                    <td class="actions-button">
		                                                                    <?php if (!empty($filePath )){ ?>
		                                                                        <div class="download-link-items">
		                                                                            <a title="{{trans('messages.view')}}" href="{{ $filePath }}" target="_blank" class="btn btn-sm bg-theme text-white mr-1 download-icon-items"><i class="fa fa-eye"></i></a>
		                                                                        </div>
		                                                                    <?php } ?>
		                                                                    </td>
		                                                                    <td style="width:70px;min-width:70px;">
		                                                                        <button type="button" title="Delete" class="btn btn-sm btn-danger m-auto d-table" data-remove-image-id="{{ (!empty($attachmentDetail->i_id) ? $attachmentDetail->i_id :'') }}" onclick="removeTableRrecord(this);"><i class="fa fa-trash fa-fw"></i></button>
		                                                                    </td>
		                                                                </tr>
                                                                		<?php
                                                                	}
                                                                }else{
                                                                ?>
                                                                <!-- show at add time -->
                                                                <tr class="list-of-other-attachment-div">
                                                                    <td class="table-index text-center" style="width:70px;min-width:70px;">1</td>
                                                                    <td class="text-left">
                                                                        <div class="custom-file">
                                                                            <input type="file" class="custom-file-input" name="document_1" id="document_1" onchange="imagePreview(this)">
                                                                            <label class="custom-file-label" for="document_1">{{ trans('messages.choose-file') }}</label>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-left">
                                                                        <input type="text" class="form-control" name="remark_1">
                                                                    </td>
                                                                    <td class="actions-button">
                                                                        
                                                                    </td>
                                                                    <td style="width:70px;min-width:70px;">
                                                                        <button type="button" title="Delete" class="btn btn-sm btn-danger m-auto d-table"><i class="fa fa-trash fa-fw" onclick="removeTableRrecord(this);"></i></button>
                                                                    </td>
                                                                </tr>
                                                                <tr class="list-of-other-attachment-div">
																	<td class="text-center table-index" style="width:70px;min-width:70px;">2</td>
                                                                    <td class="text-left">
                                                                        <div class="custom-file">
                                                                            <input type="file" class="custom-file-input" name="document_2" id="document_2" onchange="imagePreview(this)">
                                                                            <label class="custom-file-label" for="document_2">{{ trans('messages.choose-file') }}</label>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-left">
                                                                        <input type="text" class="form-control" name="remark_2">
                                                                    </td>
                                                                    <td class="actions-button">
                                                                        
                                                                    </td>
                                                                    <td style="width:70px;min-width:70px;">
	                                                                        <button type="button" title="Delete" class="btn btn-sm btn-danger m-auto d-table"><i class="fa fa-trash fa-fw" onclick="removeTableRrecord(this);"></i></button>
																	</td>
																</tr>
                                                                <?php
																}
															?>
                                                            </tbody>
                                                        </table>
                                                        <button type="button" class="btn btn-sm bg-theme text-white add-new-row" title="{{ trans('messages.add-new') }}" onclick="addAttachment();"><i class="fa fa-plus fa-fw"></i>{{ trans('messages.add-new') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<input type="hidden" name="record_id" value="{{ (isset($recordInfo) && !empty($recordInfo->i_id) ? Wild_tiger::encode($recordInfo->i_id) : '') }}">

                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="hr_comments">{{ trans("messages.hr-comments") }}</label>
                                <textarea class="form-control" rows="6" name="hr_comments">{{ old( 'hr_comments' ,  (isset($recordInfo) && !empty($recordInfo->v_comments) ? html_entity_decode($recordInfo->v_comments) : '')) }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12 submit-sticky">
	                        <?php if (isset($recordInfo) && !empty($recordInfo->i_id)){ ?>
                        	<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
                        	<?php }else{ ?>
                        	<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
                        	<?php } ?>
                            <a href="{{ config('constants.INCIDENT_REPORT_URL') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="other_attachment_count">
        <input type="hidden" name="remove_image_id" value="">
        {!!Form::close()!!}
	</div>



</main>

<script type="text/javascript" src="{{  asset('js/ckeditor/ckeditor.js') }}" charset="utf-8"></script>
<script>
    CKEDITOR.replace('what_went_wrong');
    CKEDITOR.replace('what_actions_have_been_taken');
    CKEDITOR.replace('what_we_do_prevent_in_future');
    CKEDITOR.replace('hr_comments');
</script>

<script>
    $(function() {
        $(' [name="date"]').datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            format: 'DD-MM-YYYY',
            showClear: true,
            showClose: true,
            icons: {
                clear: 'fa fa-trash',
                Close: 'fa fa-trash',
            },
        });
       $("[name='date']").data('DateTimePicker').maxDate(moment().endOf('d'));
		 
    });
</script>

<script>
<?php if( isset($recordInfo) && (!empty($recordInfo['incidentAttachment'])) ) { ?>
var maximum_attachment_id = '<?php echo (!empty($recordInfo['incidentAttachment']['i_id']) ? max(array_column(objectToArray($recordInfo['incidentAttachment']), 'i_id')) : 0)?>';
var counter = ( maximum_attachment_id > 0 ? maximum_attachment_id : 2 );
<?php } else { ?>
var counter = 2;
<?php } ?>

    $("#add-incident-report").validate({
        errorClass: "invalid-input",
        rules: {
            date: {
                required: true
            },
            'employee[]': {
                required: true
            },
            subject: {
                required: true
            },
        },
        messages: {
            date: {
                required: "{{ trans('messages.require-report-date') }}"
            },
            'employee[]': {
                required: "{{ trans('messages.require-employee-name-code') }}"
            },
            subject: {
                required: "{{ trans('messages.require-subject') }}"
            },
        },
        submitHandler: function(form) {
        	 var confirm_box = "";
             var confirm_box_msg = "";
             <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
 		          	confirm_box = "{{ trans('messages.update-incident-report') }}";
 		        	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-incident-report')]) }}";
 		           
             <?php } else {?>
 			        confirm_box = "{{ trans('messages.add-incident-report') }}";
 			        confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-incident-report')]) }}";
 			           
             <?php }?>
             alertify.confirm(confirm_box,confirm_box_msg,function() {
           	 	showLoader();
            	$("[name='other_attachment_count']").val(counter);
            	$("[name='date']").prop('disabled', false);
            	form.submit();
             },function() {});
        }
    });

function addAttachment(){
	counter++;
	var html = '';
	html += '<tr class="list-of-other-attachment-div">';
	html += '<td class="table-index text-center" style="width:70px;min-width:70px;">'+counter+'</td>';
	html += '<td class="text-left">';
	html += '<div class="custom-file">';
	html += '<input type="file" class="custom-file-input" name="document_'+counter+'" id="document_'+counter+'" onchange="imagePreview(this)">';
	html += '<label class="custom-file-label" for="document_'+counter+'">{{ trans('messages.choose-file') }}</label>';
	html += '</div>';
	html += '</td>';
	html += '<td class="text-left">';
	html += '<input type="text" class="form-control" name="remark_'+counter+'">';
	html += '</td>';
	html += '<td class="actions-button">';
	
	html += '</td>';
	html += '<td style="width:70px;min-width:70px;">';
	html += '<button type="button" title="Delete" class="btn btn-sm btn-danger m-auto d-table" onclick="removeTableRrecord(this);"><i class="fa fa-trash fa-fw"></i></button>';
	html += '</td>';
	html += '</tr>';
	
	if( $('.list-of-other-attachment-div-main').find('tr').length > 0 ){
		$(html).insertAfter($('.list-of-other-attachment-div-main').find('tr:last'));	
	} else {
		$('.list-of-other-attachment-div-main').html(html);
	}
	reindexTable('list-of-other-attachment-div-main');
}

</script>
@endsection