@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.document-type-master") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            @if(checkPermission('add_document_type') != false)
            <button type="button" class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar d-sm-flex align-items-center" onclick='opneDocumentTypeModel(this)' title="{{ trans('messages.add-document-master-type') }}"><i class="fas fa-plus mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.add-document-master-type") }}</span> </button>
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
                            <input type="text" name="search_by" class="form-control" placeholder='{{ trans("messages.search-by") }} {{ trans("messages.document-description") }}, {{ trans("messages.type") }}'>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_document_folder">{{ trans("messages.document-folder") }}</label>
                            <select class="form-control" name="search_document_folder" onchange='filterData()'>
                                <option value="">{{ trans('messages.select')}}</option>
                                <?php 
                                if(!empty($documentFolderRecordDetails)){
                               		foreach ($documentFolderRecordDetails as $documentFolderRecordDetail){
                                		$encodeRecordId = Wild_tiger::encode($documentFolderRecordDetail->i_id);?>
                                   		<option value='{{ $encodeRecordId }}'>{{ (!empty($documentFolderRecordDetail->v_document_folder_name) ? $documentFolderRecordDetail->v_document_folder_name : '') }}</option>
                                    	<?php 
                                     }
                               	}
                              	?>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_is_multiple_allowed">{{ trans("messages.is-multiple-allowed") }}</label>
                            <select class="form-control" name="search_is_multiple_allowed" onchange='filterData()'>
                                <option value="">{{ trans('messages.select')}}</option>
                                <?php 
                                if(!empty($getSelectionYesNoRecordInfo)){
                                	foreach ($getSelectionYesNoRecordInfo as $key => $getSelectionYesNoRecord){?>
                                		<option value='{{ $key }}'>{{ (!empty($getSelectionYesNoRecord) ? $getSelectionYesNoRecord :'') }}</option>
                                	<?php 
                                	}
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_is_visible_to_employee">{{ trans("messages.search-is-visible-to-employee") }}</label>
                            <select class="form-control" name="search_is_visible_to_employee" onchange='filterData()'>
                                <option value="">{{ trans('messages.select')}}</option>
                                <?php 
                                if(!empty($getSelectionYesNoRecordInfo)){
                                	foreach ($getSelectionYesNoRecordInfo as $key => $getSelectionYesNoRecord){?>
                                		<option value='{{ $key }}'>{{ (!empty($getSelectionYesNoRecord) ? $getSelectionYesNoRecord :'') }}</option>
                                	<?php 
                                	}
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="search_is_modifiable">{{ trans("messages.is-modifiable") }}</label>
                            <select class="form-control" name="search_is_modifiable" onchange='filterData()'>
                                <option value="">{{ trans('messages.select')}}</option>
                                <?php 
                                if(!empty($getSelectionYesNoRecordInfo)){
                                	foreach ($getSelectionYesNoRecordInfo as $key => $getSelectionYesNoRecord){?>
                                		<option value='{{ $key }}'>{{ (!empty($getSelectionYesNoRecord) ? $getSelectionYesNoRecord :'') }}</option>
                                	<?php 
                                	}
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 pt-lg-1 d-flex align-items-end gap justify-content-sm-start justify-content-center">
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
                                <th class="text-left" style="min-width:120px;">{{ trans("messages.document-folder") }}</th>
                                <th class="text-left" style="min-width:120px;">{{ trans("messages.document-type") }}</th>
                                <th class="text-left" style="min-width:250px;">{{ trans("messages.document-description") }}</th>
                                <th class="text-left" style="min-width:130px;">{{ trans("messages.is-multiple-allowed") }}</th>
                                <th class="text-left" style="min-width:80px;">{{ trans("messages.is-visible") }}</th>
                                <th class="text-left" style="min-width:100px;">{{ trans("messages.is-modifiable") }}</th>
                                <th class="text-center" style="min-width:100px;">{{ trans("messages.status") }}</th>
                                <th class="actions-col" style="min-width:150px;width:150px">{{ trans("messages.actions") }}</th>
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                        	@include( config('constants.AJAX_VIEW_FOLDER') . 'document-type-master/document-type-master-list')
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade document-folder document-type " id="add-document-type-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans("messages.add-document-master-type") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-document-type-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                    <div class="modal-body add-document-type-html">
                        
                    </div>
                    <div class="modal-footer justify-content-end">
                     <input type="hidden" name="record_id" value="">
                        <button type="button" class="btn bg-theme text-white action-button document-type-action-button btn-add" onclick='addDocumentTypeModel()' title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>


</main>

<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
<script>
var document_type_module_url = '{{config("constants.DOCUMENT_TYPE_MASTER_URL")}}' + '/';
    $("#add-document-type-form").validate({
        errorClass: "invalid-input",
        rules: {
            document_type: {
                required: true,
                noSpace: true,
            },
            document_folder: {
                required: true,
                noSpace: true,
            },
        },
        messages: {
            document_type: {
                required: "{{ trans('messages.please-enter-document-type') }}"
            },
            document_folder: {
                required: "{{ trans('messages.please-select-document-folder') }}"
            },
        }
    });

    function opneDocumentTypeModel(thisitem){
    	editDocumentTypeModel(thisitem);
    }

    function addDocumentTypeModel(){
    	if($('#add-document-type-form').valid() != true){
			return false;
		}
    	var record_id = $.trim($('[name="record_id"]').val());
		var document_folder = $.trim($('[name="document_folder"]').val());
		var document_type = $.trim($('[name="document_type"]').val());
		var document_description = $.trim($('[name="document_description"]').val());
		var is_multiple_allowed = $.trim($('[name="is_multiple_allowed"]:checked').val());
		var is_visible_to_employee = $.trim($('[name="is_visible_to_employee"]:checked').val());
		var is_modifiable = $.trim($('[name="is_modifiable"]:checked').val());
		var confirm_box = "";
	    var confirm_box_msg = "";
	    
	    if(record_id == 0){
	    	confirm_box = "{{ trans('messages.add-document-master-type') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-document-master-type')]) }}";
	    } else {
	    	confirm_box = "{{ trans('messages.update-document-type') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-document-type')]) }}";
	    } 
	    
	    alertify.confirm(confirm_box,confirm_box_msg,function() {   
			$.ajax({
				type: "POST",
				dataType: "json",
				url: document_type_module_url + 'add',
				data: {"_token": "{{ csrf_token() }}",
					'document_folder':document_folder,'document_type':document_type,'record_id':record_id,'document_description':document_description,
					'is_multiple_allowed':is_multiple_allowed,'is_visible_to_employee':is_visible_to_employee,'is_modifiable':is_modifiable,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),
					},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						$("#add-document-type-model").modal('hide');
						alertifyMessage('success',response.message);
						
						if(record_id != '' && record_id != null){
							$(current_row).parents('.document-type-record').html(response.data.html);
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
    function editDocumentTypeModel(thisitem){
    	current_row = thisitem;
    	var record_id = $.trim($(thisitem).attr('data-record-id'));
    	$.ajax({
    		type: "POST",
    		url: document_type_module_url + 'edit',
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
    				var header_name = "{{ trans('messages.update-document-type') }}";
    				var button_name = "{{ trans('messages.update') }}";
    				$('.add-document-type-html').html('');
    				$('.add-document-type-html').html(response);
    				$("[name='record_id']").val(record_id);
    				$("#add-document-type-model").find('.document-type-action-button').html(button_name);
    				$('.document-type-action-button').attr('title' , "{{ trans('messages.update') }}");
    				$("#add-document-type-model").find('.twt-modal-header-name').html(header_name);
    			} else {
    				var header_name = "{{ trans('messages.add-document-master-type') }}" ;
    				var button_name = "{{ trans('messages.add') }}" ;
    				$('.add-document-type-html').html('');
    				$('.add-document-type-html').html(response);
    				$("[name='record_id']").val("");
    				$("#add-document-type-model").find('.document-type-action-button').html(button_name);
    				$('.document-type-action-button').attr('title' , "{{ trans('messages.add') }}");
    				$("#add-document-type-model").find('.twt-modal-header-name').html(header_name);
    			}
    			openBootstrapModal('add-document-type-model');
    		
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
    function searchField(){
    	var search_by = $.trim($('[name="search_by"]').val());
    	var search_document_folder = $.trim($('[name="search_document_folder"]').val());
    	var search_is_multiple_allowed = $.trim($('[name="search_is_multiple_allowed"]').val());
    	var search_is_visible_to_employee = $.trim($('[name="search_is_visible_to_employee"]').val());
    	var search_is_modifiable = $.trim($('[name="search_is_modifiable"]').val());
    	var searchData = {
                'search_by':search_by,
                'search_document_folder': search_document_folder,
                'search_is_multiple_allowed': search_is_multiple_allowed,
                'search_is_visible_to_employee': search_is_visible_to_employee,
                'search_is_modifiable': search_is_modifiable,
            }
            return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(document_type_module_url + 'filter' , searchFieldName);
    }
    var paginationUrl = document_type_module_url + 'filter'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection