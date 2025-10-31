 						<div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="document_folder_name" class="control-label">{{ trans('messages.document-folder-name') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="document_folder_name" class="form-control" placeholder="{{ trans('messages.ex') }} {{ trans('messages.identity-documents-joining-related') }}" value="{{ old('document_folder_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_document_folder_name)) ? $recordInfo->v_document_folder_name : ''  ) ) ) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="document_folder_description" class="control-label">{{ trans('messages.document-folder-description') }}</label>
                                    <textarea class="form-control" name="document_folder_description" rows="4" placeholder="{{ trans('messages.ex') }} {{ trans('messages.identity-documents-available') }}">{{ ( (isset($recordInfo) && (!empty($recordInfo->v_document_folder_description)) ? $recordInfo->v_document_folder_description : ''  ) ) }}</textarea>
                                </div>
                            </div>
                        </div>