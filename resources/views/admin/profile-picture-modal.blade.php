 <div class="modal fade document-folder document-type upload-profile-image" id="upload-profile-pic-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
 	<div class="modal-dialog modal-dialog-centered modal-lg">
 		<div class="modal-content">
 			<div class="modal-header">
 				<h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.update-profile-picture") }}</h5>
 				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
 					<span aria-hidden="true"><i class="fas fa-times"></i></span>
 				</button>
 			</div>
 			{!! Form::open(array( 'id '=> 'update-profile-picture-form' , 'method' => 'post' )) !!}
 			<div class="modal-body">
 				<?php /* ?>
 				<input type="file" id="profile_pic" style="display:none;" name="profile_pic" onchange="imagePreview(this,'image', true)">
 				<label for="profile_pic" class="upload-image-bg-class"><i class="fa fa-paperclip mr-2" aria-hidden="true"></i>{{ trans("messages.upload") }}</label>
 				<?php
					$fileName = config('constants.UPLOAD_STATIC_IMAGE_PATH');
					if (!empty($employeeRecordInfo->v_profile_pic) && file_exists(config('constants.FILE_STORAGE_PATH') . $employeeRecordInfo->v_profile_pic)) {
						$fileName =  config('constants.FILE_STORAGE_PATH_URL') . $employeeRecordInfo->v_profile_pic;
					} ?>
 				<p class="text-muted ">{{ trans('messages.maximum-file-size-allowed-info') }}</p>
 				<div class="row">
 					<div class="mb-4 pt-2 preview-image-div profile_pic-preview-div col-md-6">
 						<img src="{{ $fileName }}" class="file-upload-preview img-fluid profile_pic-preview crop_profile_pic-preview border" id="crop_profile_pic-preview" style="max-height: 150px">
 					</div>
 					<div class="img-preview-div col-md-6" style="display: none;">
 						<img src="{{  asset ('images/demo.jpg') }}" alt="Preview Crop Image" class="img-fluid crop_profile_pic-preview">
 					</div>
 				</div>
 				<?php */ ?>

				<div class="container">
                    <input type="file" id="profile-pic" style="display:none;" name="profile_pic" onchange="imagePreview(this,'image', true);">
 					<label for="profile-pic" class="upload-image-bg-class"><i class="fa fa-paperclip mr-2" aria-hidden="true"></i>{{ trans("messages.upload") }}</label>
 					<p class="text-muted ">{{ trans('messages.maximum-file-size-allowed-info') }}</p>
                  	<div class="row">
                  		<div class="col-md-4 preview-div">
                  			<img src="" class="crop_profile_pic-preview crop_profile-pic-preview">
                  		</div>
                  		<div class="col-md-8 overflow-hidden" style="display: none;">
                  			<img id="profile-pic_crop_selection" src="" alt="Preview Crop Image" class="profile-pic_crop_selection" />
		                </div>
                  	</div>
                </div>
			</div>
 			<input type="hidden" name="crop_profile_pic_image" value="">
 			<div class="modal-footer justify-content-end crop-update-button-div">
 				<button type="button" onclick="cropSelectedImage(this,'crop_profile_pic_image')" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add crop-profile-pic-button" style="display:none;" title="{{ trans('messages.crop') }}">{{ trans('messages.crop') }}</button>
 				<button type="button" onclick="updateProfilePicture(this)" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add profile-update-button update-crop-image-button" style="display: none;" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
 				<button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
 			</div>
 			{!! Form::close() !!}
 		</div>
 	</div>
 </div>
 <script>
 	var employee_url = "{{ config('constants.EMPLOYEE_MASTER_URL') }}" + "/";
	var crop_field_id = '';

 	function uploadProfilePic(thisitem) {
 	 	$("#upload-profile-pic-modal").find(".crop-profile-pic-button").hide();
 	 	$("#upload-profile-pic-modal").find(".update-crop-image-button").hide();
 	 	$("#upload-profile-pic-modal").find(".crop_profile_pic-preview").parent().hide();

 	 	var employee_id = $.trim($(thisitem).attr('data-emp-id')); 
 	 	
 	 	$.ajax({
			url: employee_module_url + 'getProfilePicInfo',
			type: 'POST',
			dataType : 'json',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: { 'employee_id' : employee_id },
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				//profile_pic
				if( response.status_code == 1 ){
					if( response.data.profile_pic != "" && response.data.profile_pic != null ){
						$(".profile_pic-preview-div").show();
						$(".crop_profile-pic-preview").show();
						
						$(".profile_pic-preview").attr("src" , response.data.profile_pic);
						$(".crop_profile_pic-preview").attr("src", response.data.profile_pic);
						$("#profile-pic_crop_selection").attr("src", response.data.profile_pic);
						crop_image_field_id = 'profile-pic_crop_selection';
						$("#profile-pic_crop_selection").parent().show();
						$(".crop_profile_pic-preview").parent().show();
						setTimeout(startCrop, 500);
					} else {
						$(".profile_pic-preview-div").hide();
						$(".profile_pic-preview").attr("src" ,"" );
						$(".crop_profile_pic-preview").attr("src", "");
						$(".crop_profile-pic-preview").hide();
					}
				}
				openBootstrapModal('upload-profile-pic-modal');
				
			},
			error: function(errorResponse) {
				hideLoader();
			}
		});
 	 	
 		
 	}

 	$('#upload-profile-pic-modal').on('hidden.bs.modal', function() {
 		$("[name='crop_profile_pic_image']").val("");
 		$(".crop-profile-pic-button").hide();
 		cropper.destroy();
 	});



 	$("#update-profile-picture-form").validate({
 		errorClass: "invalid-input",
 		rules: {
 			profile_pic: {
 				required: false,
 				extension: 'jpg|png|jpeg'
 			},
 			crop_profile_pic_image: {
 				required: true,
 			},
 		},
 		messages: {
 			profile_pic: {
 				required: "{{ trans('messages.required-upload-profile-pic') }}",
 				extension: "{{trans('messages.upload-profile-picture-valid-type')}}"
 			},
 			crop_profile_pic_image: {
 				required: "{{ trans('messages.required-crop-image') }}",
 			},

 		}
 	});

 	function updateProfilePicture() {
 		if ($("#update-profile-picture-form").valid() != true) {
 			return false;
 		}
 		var employee_id = "{{ (isset($empId) ? $empId : 0 )}}"
 		var formData = new FormData($('#update-profile-picture-form')[0]);
 		formData.append("employee_id", employee_id);
 		alertify.confirm("{{ trans('messages.update-profile-picture') }}", "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-profile-picture')]) }}", function() {
 			$.ajax({
 				url: employee_module_url + 'uploadProfilePic',
 				type: 'POST',
 				dataType: 'json',
 				headers: {
 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 				},
 				data: formData,
 				cache: false,
 				contentType: false,
 				processData: false,
 				beforeSend: function() {
 					//block ui
 					showLoader();
 				},
 				success: function(response) {
 					hideLoader();
 					if (response.status_code == 1) {
 						alertifyMessage('success', response.message);
 						$("#upload-profile-pic-modal").modal('hide');
 						var employee_profile_pic_view = response.data.mainProfileInfo;
 						$(".employee-profile-pic-view--master-div-html").html(employee_profile_pic_view);

 					} else {
 						alertifyMessage('error', response.message);
 					}
 				},
 				error: function(errorResponse) {
 					hideLoader();
 				}
 			});
 		}, function() {});
 	}
 </script>

 <script type="text/javascript" defer>
 	function cropSelectedImage(thisitem, field_name) {
 		var imgurl = cropper.getCroppedCanvas().toDataURL();
		
 		if (imgurl != "" && imgurl != null) {
 			$("[name='" + field_name + "']").val(imgurl);
 			$(".crop_profile_pic-preview").attr('src' , imgurl);
 			$(".crop_profile_pic-preview").parent().show();;
 			$(".crop_profile_pic-preview").show();;
 			$(thisitem).parents(".crop-update-button-div").find(".update-crop-image-button").show();
 		}
	}

 	 
	
 </script>