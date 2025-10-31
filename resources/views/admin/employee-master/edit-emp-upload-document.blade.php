					<input type="file" name="upload_document_file[]"  id="file" style="display:none;" onchange="galleryMultipleDocumentPreview(this)">
                       <label for="file" class="add-attach mb-1"><i class="fa fa-paperclip mr-2" aria-hidden="true"></i>{{ trans("messages.add-attachment") }}</label>
                       <a type="button" class="ml-2" data-toggle="tooltip" data-placement="right" title="{{trans('messages.document-file-type')}}"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
					   <label id="file-error" class="invalid-input d-block" for="file"></label>
                       <p class="text-muted">{{ trans('messages.maximum-file-size-allowed-info') }}</p>
						<div class="row">
						   <div class="col-lg-12">
                            	<div class="file-preview-div col-lg-12">
                            	
                            	</div>
                            </div>
						</div>
						<div class="col-lg-12">
							<?php 
							if ((isset($recordInfo)) && ($recordInfo->v_document_file)){
								$documentFiles = json_decode($recordInfo->v_document_file); 
								foreach ($documentFiles as $documentFile){
									$documentFileName = "";
									if (!empty($documentFile) && file_exists(config('constants.FILE_STORAGE_PATH').config('constants.UPLOAD_FOLDER') . $documentFile)) {
										$documentFileName =  config('constants.FILE_STORAGE_PATH_URL') .  config('constants.UPLOAD_FOLDER') .  $documentFile;
										
										?>
									<div class="gallery-image-div">
										<div class="row justify-content-between flex-nowrap">
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
							<?php 
							} ?>
						</div>
                       <label class="control-label" for="remark">{{ trans("messages.remark") }}</label>
                       <textarea class="form-control" name="remark" placeholder="{{ trans('messages.remark') }}">{{ (isset($recordInfo->v_remark) ? $recordInfo->v_remark  :'') }}</textarea>

					   <script>
						$(function () {
  							$('[data-toggle="tooltip"]').tooltip()
						})
					   </script>