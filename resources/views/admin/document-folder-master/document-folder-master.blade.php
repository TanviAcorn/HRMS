@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.document-master-folder") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            @if(checkPermission('add_document_folder') != false)
                <button type="button" class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar d-sm-flex align-items-center" onclick="openDocumentFolderModel(this)" title="{{ trans('messages.add-document-master-folder') }}"><i class="fas fa-plus mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.add-document-master-folder") }}</span> </button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans("messages.search-by") }}</label>
                            <input type="text" name="search_by" class="form-control" placeholder='{{ trans("messages.search-by") }} {{ trans("messages.document-folder-name") }}, {{ trans("messages.description") }}'>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
                            <select class="form-control" name="search_status" onchange='filterData()'>
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.ACTIVE_STATUS')}}">{{ trans("messages.active") }}</option>
                                <option value="{{ config('constants.INACTIVE_STATUS')}}">{{ trans("messages.inactive") }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick='filterData()'>{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
            <div class="card card-body">
            {{ Wild_tiger::readMessage() }}
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="min-width:200px;">{{ trans("messages.document-folder-name") }}</th>
                                <th class="text-left" style="min-width:250px;">{{ trans("messages.document-folder-description") }}</th>
                                <th class="text-center" style="min-width:80px;">{{ trans("messages.status") }}</th>
                                @if((checkPermission('edit_document_folder') != false) || (checkPermission('delete_document_folder') != false))
                                <th class="actions-col" style="min-width:150px;width:150px">{{ trans("messages.actions") }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                           @include( config('constants.AJAX_VIEW_FOLDER') . 'document-folder-master/document-folder-master-list') 
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>




    <div class="modal fade document-folder" id="add-document-folder-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans("messages.add-document-master-folder") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-document-folder-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                    <div class="modal-body add-document-folder-html">
                       
                    </div>
                    <div class="modal-footer justify-content-end">
                    <input type="hidden" name="record_id" value="">
                        <button type="button" onclick='addDocumentFolderMaster()' class="btn bg-theme text-white action-button document-folder-action-button btn-add" title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>


</main>

<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
<script>
var document_folder_module_url = '{{config("constants.DOCUMENT_FOLDER_MASTER_URL")}}' + '/';
    $("#add-document-folder-form").validate({
        errorClass: "invalid-input",
        onfocusout: false,
		onkeyup: false,
        rules: {
            document_folder_name: {
                required: true,
                noSpace: true,
                validateUniqueDocumentFolderName:true,
            },
        },
        messages: {
            document_folder_name: {
                required: "{{ trans('messages.require-document-master-name') }}"
            },
        }
    });
	
    function openDocumentFolderModel(thisitem){
    	editDocumentFolderModel(thisitem);
    }

    function addDocumentFolderMaster(){
		if($('#add-document-folder-form').valid() != true){
			return false;
		}
		var record_id = $.trim($('[name="record_id"]').val());
		var document_folder_name = $.trim($('[name="document_folder_name"]').val());
		var document_folder_description = $.trim($('[name="document_folder_description"]').val());
		var confirm_box = "";
	    var confirm_box_msg = "";
	    
	    if(record_id == 0){
	    	confirm_box = "{{ trans('messages.add-document-master-folder') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-document-master-folder')]) }}";
	    } else {
	    	confirm_box = "{{ trans('messages.update-document-folder') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-document-folder')]) }}";
	    } 
	    
	    alertify.confirm(confirm_box,confirm_box_msg,function() {   
			$.ajax({
				type: "POST",
				dataType: "json",
				url: document_folder_module_url + 'add',
				data: {"_token": "{{ csrf_token() }}",
					'document_folder_name':document_folder_name,'document_folder_description':document_folder_description,'record_id':record_id,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						$("#add-document-folder-model").modal('hide');
						alertifyMessage('success',response.message);
						
						if(record_id != '' && record_id != null){
							$(current_row).parents('.document-folder-record').html(response.data.html);
						}else{
							filterData();
						}
						
					} else {
						alertifyMessage('error',response.message);
					}
					
				},
				error: function() {
					hideLoader();
				}
			});
	    },function() {});	
    }
    var current_row ='';
    function editDocumentFolderModel(thisitem){
    	current_row = thisitem;
    	var record_id = $.trim($(thisitem).attr('data-record-id'));
    	$.ajax({
    		type: "POST",
    		url: document_folder_module_url + 'edit',
    		data: {
    			"_token": "{{ csrf_token() }}",
    			record_id: record_id,
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			if(record_id !="" && record_id != null){
    				var header_name = "{{ trans('messages.update-document-folder') }}";
    				var button_name = "{{ trans('messages.update') }}";
    				$('.add-document-folder-html').html('');
    				$('.add-document-folder-html').html(response);
    				$("[name='record_id']").val(record_id);
    				$("#add-document-folder-model").find('.document-folder-action-button').html(button_name);
    				$('.document-folder-action-button').attr('title' , "{{ trans('messages.update') }}");
    				$("#add-document-folder-model").find('.twt-modal-header-name').html(header_name);
    			} else {
    				var header_name = "{{ trans('messages.add-document-master-folder') }}" ;
    				var button_name = "{{ trans('messages.add') }}" ;
    				$('.add-document-folder-html').html('');
    				$('.add-document-folder-html').html(response);
    				$("[name='record_id']").val("");
    				$("#add-document-folder-model").find('.document-folder-action-button').html(button_name);
    				$('.document-folder-action-button').attr('title' , "{{ trans('messages.add') }}");
    				$("#add-document-folder-model").find('.twt-modal-header-name').html(header_name);
    			}
    			openBootstrapModal('add-document-folder-model');
    		
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }

    function searchField(){
    	var search_by = $.trim($('[name="search_by"]').val());
    	var search_status = $.trim($('[name="search_status"]').val());
    	var searchData = {
                'search_by':search_by,
                'search_status': search_status,
            }
            return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(document_folder_module_url + 'filter' , searchFieldName);
    }
    var paginationUrl = document_folder_module_url + 'filter'

    $.validator.addMethod("validateUniqueDocumentFolderName", function (value, element) {
      	 
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: document_folder_module_url +'checkUniqueDocumentFolderName',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'document_folder_name': $.trim($("[name='document_folder_name']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
    		     },
    		beforeSend: function() {
    			
    		},
    		success: function (response) {
    			
    			if (response.status_code == 1) {
    				return false;
    			} else {
    				result = false;
    				return true;
    			}
    		}
    	});
    	return result;
    }, '{{ trans("messages.error-unique-document-folder-name") }}');
          
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection