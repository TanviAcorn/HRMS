
						 <?php
        				$ismultipleallowed = trans('messages.document-type-multiple-allowed');
        				$ismodifiable = trans('messages.document-type-modify');
        				?>
						<div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="document_folder">{{ trans("messages.document-folder") }}<span class="text-danger">*</span></label>
                                    <select class="form-control" name="document_folder">
                                        <option value="">{{ trans('messages.select')}}</option>
                                        <?php 
                                        if(!empty($documentFolderRecordDetails)){
                                        	foreach ($documentFolderRecordDetails as $documentFolderRecordDetail){
                                        		$encodeRecordId = Wild_tiger::encode($documentFolderRecordDetail->i_id);
                                        		$selected ='';
                                        		if( (isset($recordInfo) ) && ($recordInfo->i_document_folder_id == $documentFolderRecordDetail->i_id)){
                                        			$selected="selected='selected'";
                                        		}
                                        		?>
                                        		<option value='{{ $encodeRecordId }}' {{ $selected }}>{{ (!empty($documentFolderRecordDetail->v_document_folder_name) ? $documentFolderRecordDetail->v_document_folder_name : '') }}</option>
                                        		<?php 
                                        	}
                                        }?>
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="document_type">{{ trans("messages.document-type") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="document_type" class="form-control" placeholder="{{ trans('messages.ex') }} {{ trans('messages.pan-card') }}, {{ trans('messages.aadhaar-card') }}" value="{{ old('document_folder_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_document_type)) ? $recordInfo->v_document_type : ''  ) ) ) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="document_description" class="control-label">{{ trans('messages.document-description') }}</label>
                                    <textarea class="form-control" name="document_description" rows="4" placeholder="{{ trans('messages.document-description') }}">{{ ( (isset($recordInfo) && (!empty($recordInfo->v_document_description)) ? $recordInfo->v_document_description : ''  ) ) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="is_multiple_allowed" class="control-label">{{ $ismultipleallowed }}</label>
                                    <div class="radio-boxes form-row p-1 bg-white">
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="is_multiple_allowed" id="is_multiple_allowed_yes" value="{{  config('constants.SELECTION_YES') }}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_multiple_allowed_employee)) && ( $recordInfo->e_multiple_allowed_employee ==  config('constants.SELECTION_YES') ) ) ? 'checked' : '' ) }} >
                                                <label class="form-check-label custom-type-label btn stock-btn" for="is_multiple_allowed_yes">{{ trans('messages.yes') }}</label>
                                            </div>
                                        </div>
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="is_multiple_allowed" id="is_multiple_allowed_no"  value="{{ config('constants.SELECTION_NO')}}" {{ (!isset($recordInfo) ? 'checked' : '' ) }} {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_multiple_allowed_employee)) && ( $recordInfo->e_multiple_allowed_employee ==  config('constants.SELECTION_NO') ) ) ? 'checked' : '' ) }}>
                                                <label class="form-check-label custom-type-label btn stock-btn" for="is_multiple_allowed_no">{{ trans('messages.no') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="is_visible_to_employee" class="control-label">{{ trans("messages.is-visible-to-employee") }}</label>
                                    <div class="radio-boxes form-row p-1 bg-white">
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="is_visible_to_employee" id="is_visible_to_employee_yes" value='{{  config("constants.SELECTION_YES") }}' {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_visible_to_employee)) && ( $recordInfo->e_visible_to_employee ==  config('constants.SELECTION_YES') ) ) ? 'checked' : '' ) }}>
                                                <label class="form-check-label custom-type-label btn stock-btn" for="is_visible_to_employee_yes">{{ trans('messages.yes') }}</label>
                                            </div>
                                        </div>
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="is_visible_to_employee" id="is_visible_to_employee_no" value="{{ config('constants.SELECTION_NO')}} " {{ (!isset($recordInfo) ? 'checked' : '' ) }} {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_visible_to_employee)) && ( $recordInfo->e_visible_to_employee ==  config('constants.SELECTION_NO') ) ) ? 'checked' : '' ) }} >
                                                <label class="form-check-label custom-type-label btn stock-btn" for="is_visible_to_employee_no">{{ trans('messages.no') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="is_modifiable" class="control-label">{{ $ismodifiable }}</label>
                                    <div class="radio-boxes form-row p-1 bg-white">
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="is_modifiable" id="is_modifiable_yes" value="{{ config('constants.SELECTION_YES') }}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_modifiable_employee)) && ( $recordInfo->e_modifiable_employee ==  config('constants.SELECTION_YES') ) ) ? 'checked' : '' ) }} >
                                                <label class="form-check-label custom-type-label btn stock-btn" for="is_modifiable_yes">{{ trans('messages.yes') }}</label>
                                            </div>
                                        </div>
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="is_modifiable" id="is_modifiable_no" value="{{ config('constants.SELECTION_NO') }}" {{ (!isset($recordInfo) ? 'checked' : '' ) }} {{ ( (  isset($recordInfo) && (!empty($recordInfo->e_modifiable_employee)) && ( $recordInfo->e_modifiable_employee ==  config('constants.SELECTION_NO') ) ) ? 'checked' : '' ) }}>
                                                <label class="form-check-label custom-type-label btn stock-btn" for="is_modifiable_no">{{ trans('messages.no') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>