@extends('includes/header')
@section('pageTitle', $pageTitle )
@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <button type="button" class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar d-sm-flex align-items-center" onclick="uploadFileExcel(this)" title="{{ trans('messages.import-ecxel') }}"><i class="fas fa-plus mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.import-ecxel") }}</span> </button>
        </div>
    </div>
 </main>
<div class="modal fade bd-example-modal-lg" id="upload-file-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.import-ecxel') }}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
				</div>
				 {!! Form::open(array( 'id '=> 'upload-ecxel-form' , 'method' => 'post' , 'files' => true )) !!}
				<form method="post">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group mb-0">
									<label for="upload_ecxel_file" class="control-label">{{trans('messages.upload-file')}} <span class="text-danger">*</span></label>
									<div class="custom-file">
										<input type="file" onchange="validFile(this,'excel');" class="custom-file-input" name="upload_ecxel_file">
										<label class="custom-file-label">{{ trans('messages.choose-file') }}</label>
									</div>
									<label id="upload_ecxel_file-error" class="invalid-input" for="upload_ecxel_file" style="display: none;"></label>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer justify-content-center">
						<button type="button" onclick="uploadEcxelSheet(this);"  class="btn bg-theme text-white action-button dimension-modal-action-button" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
<script>
function uploadFileExcel(){
	openBootstrapModal('upload-file-modal');
}
$("#upload-ecxel-form").validate({
	 errorClass: "invalid-input",
	rules: {
		upload_ecxel_file: {
			required : true,  
		},
	},
	messages: {
		upload_ecxel_file: {
			required : "{{ trans('messages.required-upload-file') }}",
		},
	},
 });

 function uploadEcxelSheet(){
	if($("#upload-ecxel-form").valid() != true ){
		return false;
	}
	var formData = new FormData( $('#upload-ecxel-form')[0] );
   alertify.confirm('<?php echo e(trans("messages.upload-file")); ?>', '<?php echo e(trans("messages.upload-files-record-msg")); ?>' , function () {
		$.ajax({
			type : 'post',
			url: '{{config("constants.DASHBORD_MASTER_URL")}}' + '/importExcel',
			dataType : 'json',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			data : formData,
			processData: false,
			contentType: false,
			beforeSend : function(){
				showLoader();
			},
			success : function(response){
				hideLoader();
				if( response.status_code == 1 ){
					alertifyMessage('success' , response.message);
					$("#upload-file-modal").modal('hide');
					$("#upload-ecxel-form").validate().resetForm();
				} else if( response.status_code == 101 ){
					alertifyMessage('error' , response.message  );
					
				}
			
			}
		});
  }, function () { });
}
</script>

@endsection