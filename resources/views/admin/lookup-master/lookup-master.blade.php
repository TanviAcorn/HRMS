@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

@include('admin/add-lookup-modal')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title long-name-title" id="pageTitle">{{ $pageTitle }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0">
			@if(isset($moduleName) && (($moduleName == config('constants.TEAM_LOOKUP') && checkPermission('add_team_master') != false) || ($moduleName == config('constants.DESIGNATION_LOOKUP') && checkPermission('add_designation_master') != false) || ($moduleName == config('constants.RECRUITMENT_SOURCE_LOOKUP') && checkPermission('add_recruitment_source_master') != false) || ($moduleName == config('constants.TERMINATION_REASONS_LOOKUP') && checkPermission('add_termination_reasons') != false) || ($moduleName == config('constants.RESIGN_REASONS_LOOKUP') && checkPermission('add_resign_reasons') != false) || ($moduleName == config('constants.BANK_LOOKUP') && checkPermission('add_bank_master') != false) || !in_array($moduleName, [config('constants.TEAM_LOOKUP'), config('constants.DESIGNATION_LOOKUP'), config('constants.RECRUITMENT_SOURCE_LOOKUP'), config('constants.TERMINATION_REASONS_LOOKUP'), config('constants.RESIGN_REASONS_LOOKUP'), config('constants.BANK_LOOKUP')])))
            	<button  type="button" onclick="openLookupModal(this)" data-module-name="{{ $moduleName }}" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2" title="{{ $addTitle }}" ><i class="fas fa-plus"></i> <span class="d-sm-inline-block d-none"> {{ $addTitle }}</span></button>
			@endif
            <button type="button" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm" data-toggle="collapse" data-target="#filter" title="Filter"><i class="fas fa-filter"></i> <span class="d-sm-inline-block d-none"> {{ trans("messages.filter") }}</span></button>
        </div>
    </div>

    <section class="inner-wrapper-common-section main-listing-section p-md-3 pt-3">
        <div class="container-fluid">
            <div class="collapse" id="filter">
                <div class="card card-body mb-3">
                    <div class="row align-items-center">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="search_user" class="control-label">{{ trans("messages.search-by") }}</label>
                                <input type="text" class="form-control twt-enter-search custom-input" name="search_by" id="search_user" placeholder="{{ $searchByTitle }}">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
                                <select class="form-control"  name="search_status" onchange="filterData(this);">
                                    <option value="">{{ trans("messages.select") }}</option>
                                    <option value="{{ config('constants.ACTIVE_STATUS') }}">{{ trans("messages.active") }}</option>
                                    <option value="{{ config('constants.INACTIVE_STATUS') }}">{{ trans("messages.inactive") }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 d-flex align-items-end gap">
                            <button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white mt-3" onclick="filterData(this);">{{ trans("messages.search") }}</button>
                            <button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers mt-3">{{ trans("messages.reset") }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-result-wrapper">
                <div class="card card-body shadow-sm">
                    {{ Wild_tiger::readMessage() }}
                    <div class="table-responsive fixed-tabel-body">
                        <table class="table table-sm table-bordered table-hover" id="user-table">
                            <thead>
                                <tr>
                                    <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                    <th class="text-left">{{ $columnName }}</th>
                                    <th>{{ trans("messages.status") }}</th>
                                    @if(isset($moduleName) && ((($moduleName == config('constants.TEAM_LOOKUP') && checkPermission('edit_team_master') != false) || ($moduleName == config('constants.DESIGNATION_LOOKUP') && checkPermission('edit_designation_master') != false) || ($moduleName == config('constants.RECRUITMENT_SOURCE_LOOKUP') && checkPermission('edit_recruitment_source_master') != false) || ($moduleName == config('constants.TERMINATION_REASONS_LOOKUP') && checkPermission('edit_termination_reasons') != false) || ($moduleName == config('constants.RESIGN_REASONS_LOOKUP') && checkPermission('edit_resign_reasons') != false) || ($moduleName == config('constants.BANK_LOOKUP') && checkPermission('edit_bank_master') != false)) || (($moduleName == config('constants.TEAM_LOOKUP') && checkPermission('delete_team_master') != false) || ($moduleName == config('constants.DESIGNATION_LOOKUP') && checkPermission('delete_designation_master') != false) || ($moduleName == config('constants.RECRUITMENT_SOURCE_LOOKUP') && checkPermission('delete_recruitment_source_master') != false) || ($moduleName == config('constants.TERMINATION_REASONS_LOOKUP') && checkPermission('delete_termination_reasons') != false) || ($moduleName == config('constants.RESIGN_REASONS_LOOKUP') && checkPermission('delete_resign_reasons') != false) || ($moduleName == config('constants.BANK_LOOKUP') && checkPermission('delete_bank_master') != false)) || !in_array($moduleName, [config('constants.TEAM_LOOKUP'), config('constants.DESIGNATION_LOOKUP'), config('constants.RECRUITMENT_SOURCE_LOOKUP'), config('constants.TERMINATION_REASONS_LOOKUP'), config('constants.RESIGN_REASONS_LOOKUP'), config('constants.BANK_LOOKUP')])))
                                    <th class="actions-col" style="width:150px;min-width:150px">{{ trans("messages.actions") }}</th>
                                    @endif
                                </tr>
                            </thead>
							<tbody class="ajax-view">
								 @include( config('constants.AJAX_VIEW_FOLDER')  . 'lookup-master/lookup-master-list')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- </div>
            </div> -->

        </div>

    </section>
</main>
<input type="hidden" name="module_name" value="<?php echo $moduleName ?>">
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
<script>
	function searchField(){
    	var search_by_value = $.trim( $('[name="search_by"]').val() );
    	var module_name = $.trim( $('[name="module_name"]').val() );
    	var search_status = $.trim( $('[name="search_status"]').val() );
    	
    	var searchData = {
    		'search_by_value' : search_by_value,
    		'search_status' : search_status,
    		'module_name' : module_name
    	}

    	return searchData; 
    }

	var module_url =  '{{ config("constants.LOOKUP_MASTER_URL") }}' + '/';
	
    function filterData() {
 
    	var searchFieldName = searchField();

    	searchAjax( site_url + 'filter' , searchFieldName );
	}
	
 <?php /*   function openLookupModal(thisitem){
       // console.log(thisitem);
		var module_name = $.trim($(thisitem).attr('data-module-name'));
		var header_name = 'Add ' + enumText(module_name);

		$("[name='lookup_module_name']").val(module_name);
		var action_type = $.trim($(thisitem).attr('data-action'));
		if( action_type != "" &&  action_type != null ){
			$("[name='action_type']").val(action_type);
		}
		$("[name='lookup_module_record_id']").val('');
		$('.lookup-modal-action-button').html("{{ trans('messages.add') }}");
		$('.lookup-modal-action-button').attr('title' , "{{ trans('messages.add') }}");
		$("#add-lookup-modal").find('.twt-modal-header-name').html(header_name);
		$("[name='module_value']").val("");
		$("[name='request_type']").val("{{ config('constants.ADD_REQUEST') }}");
		openBootstrapModal('add-lookup-modal');
		$("#add-lookup-form").validate({
	        errorClass: "invalid-input",
	        rules: {
	        	module_value: { required: true, noSpace : true },
	        },
	        messages: {
	        	module_value: { required: "{{ trans('messages.required-module-value') }}" },
	        },
	    });
		
    } */ ?>

    function editLookupModal(thisitem){
    	var module_name = $.trim($(thisitem).attr('data-module-name'));
    	var record_id = $.trim($(thisitem).attr('data-record-id'));
    	
    	$.ajax({
			type: "POST",
			dataType: "json",
			url: site_url + 'getLookupRecordInfo',
			data: {
				"_token": "{{ csrf_token() }}",
				record_id: record_id,
				module_name: module_name,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if( response.status_code == 1 ){
					var value = response.data.recordInfo.v_value;
					var header_name = 'Update ' + enumText(module_name);
					$("[name='module_value']").val(value);
					$("[name='lookup_module_name']").val(module_name);
					$("#add-lookup-modal").find('.twt-modal-header-name').html(header_name);
					$("[name='lookup_module_record_id']").val(record_id);
					$('.lookup-modal-action-button').html("{{ trans('messages.update') }}");
					$('.lookup-modal-action-button').attr('title' , "{{ trans('messages.update') }}");

					if( module_name != "" && module_name != null && module_name == "<?php echo config('constants.TEAM_LOOKUP')?>" ){
						$(".lookup-chart-color").show();
						//console.log(( ( response.data.recordInfo.v_chart_color != "" && response.data.recordInfo.v_chart_color != null ) ? '#' + response.data.recordInfo.v_chart_color : '' ));
						$("[name='module_chart_color']").val( ( ( response.data.recordInfo.v_chart_color != "" && response.data.recordInfo.v_chart_color != null ) ? '#' + response.data.recordInfo.v_chart_color : '' )  );
					} else {
						$(".lookup-chart-color").hide();
						$("[name='module_chart_color']").val();
					}

					openBootstrapModal('add-lookup-modal');
					$("#add-lookup-form").validate({
				        errorClass: "invalid-input",
				        rules: {
				        	module_value: { required: true, noSpace : true },
				        },
				        messages: {
				        	module_value: { required: "{{ trans('messages.required-module-value') }}" },
				        },
				    });
				} else {
					alertifyMessage('error',response.message);
				}
			},
			error: function() {
				hideLoader();
			}
		});
	}

  <?php /*  function addLookup(){

    	if( $("#add-lookup-form").valid() != true ){
			return false;
        }

        var lookup_module_name = $.trim($("[name='lookup_module_name']").val());
        var module_value = $.trim($("[name='module_value']").val());
        var record_id = $.trim($("[name='lookup_module_record_id']").val());
        var action_type = $("[name='action_type']").val();

        var confirm_box = "";
	    var confirm_box_msg = "";
	    
	    if(record_id == 0){
		    confirm_box = "{{ trans('messages.add')}} "  + enumText(lookup_module_name);
		   confirm_box_msg = "{{ trans('messages.common-confirm-add-msg',['module'=> trans('messages.add')]) }} " + enumText(lookup_module_name) + ' ?';
		    
		 } else {
	    	confirm_box = "{{ trans('messages.update') }} "  + enumText(lookup_module_name) ;
	    	confirm_box_msg = "{{ trans('messages.common-confirm-add-msg',['module'=> trans('messages.update')]) }} " + enumText(lookup_module_name) + ' ?';
	    } 
	    alertify.confirm(confirm_box,confirm_box_msg,function() {
    	$.ajax({
			type: "POST",
			dataType: "json",
			url: site_url + "add-lookup-master",
			data: {
				"_token": "{{ csrf_token() }}",
				lookup_module_name: lookup_module_name,
				module_value: module_value,
				record_id : record_id,
				request_type : $.trim($("[name='request_type']").val())
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if( response.status_code == 1 ){
					alertifyMessage('success',response.message);
					$("#add-lookup-modal").modal('hide');
				} else {
					alertifyMessage('error',response.message);
				}
				//console.log("action_type = " + action_type );
				if( action_type != "" && action_type != null ){
					filterData();
				}else if( record_id != "" && record_id != null ){
					filterData();	
				} else {
					var html = response.data.html;
					//console.log("lookup_module_name = " + lookup_module_name );
					var related_class_list = lookup_module_name + '-list';
					$('.' + related_class_list).each(function(){
						var selected_list = $.trim($(this).find('option:selected').attr('data-id'));
						$(this).html(html);
						$(this).find("option[data-id='" + selected_list + "']").prop("selected", true);
					})
				}
				
				
				
			},
			error: function() {
				hideLoader();
			}
		});
	    },function() {});
    }
 */?>
    

 <?php /*   $('.modal').on('hidden.bs.modal' , function(){
		if( $(this).find('form').length > 0 ) { 
    		$(this).find('form').validate().resetForm();
    		$(this).find('form').trigger("reset");
    		$(this).find('form .custom-file-label').html("{{ trans('messages.choose-file') }}"); 
		}
    });
    $('#add-lookup-form').on("submit",function(e){
    	e.preventDefault();
        e.stopPropagation();
    }); */ ?>
    var paginationUrl = site_url + 'filter'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection