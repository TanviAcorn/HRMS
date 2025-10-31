@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.weekly-off-master") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
			@if(checkPermission('add_weekly_offs'))
            	<button type="button" onclick="openWeeklyOffModel(this)" class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar d-sm-flex align-items-center" title="{{ trans('messages.add-weekly-off') }}"><i class="fas fa-plus mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.add-weekly-off") }}</span> </button>
			@endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans("messages.search-by") }}</label>
                            <input type="text" name="search_by" class="form-control" placeholder='{{ trans("messages.search-by") }} {{ trans("messages.weekly-off-name") }}, {{ trans("messages.description") }} ' >
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
                            <select class="form-control" name="search_status" onchange="filterData()">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="{{ config('constants.ACTIVE_STATUS')}}">{{ trans("messages.active") }}</option>
                                <option value="{{ config('constants.INACTIVE_STATUS')}}">{{ trans("messages.inactive") }}</option>

                            </select>
                        </div>
                    </div>
                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" onclick="filterData()" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}">{{ trans("messages.search") }}</button>
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
                                <th class="text-left" style="min-width:270px;">{{ trans("messages.weekly-off-name") }}</th>
                                <th class="text-left" style="min-width:100px;">{{ trans("messages.description") }}</th>
                                <th class="text-center">{{ trans("messages.status") }}</th>
                                @if(checkPermission('edit_weekly_offs') != false || checkPermission('delete_weekly_offs') != false)
                                	<th class="actions-col" style="min-width:150px;width:150px">{{ trans("messages.actions") }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="ajax-view">
                           @include( config('constants.AJAX_VIEW_FOLDER') . 'weekly-off-master/weekly-off-master-list')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade document-folder weekly-off-modal" id="weekly-off-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-weekly-off-header-name" id="exampleModalLabel">{{ trans("messages.add-weekly-off") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-weekly-off-model-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                    <div class="modal-body add-weekly-off-html">
                        
                    </div>

                    <div class="modal-footer justify-content-end">
                    	<input type="hidden" name="record_id" value="">
                        <button type="button" onclick="addWeeklyOffModel()" class="btn bg-theme text-white action-button weekly-off-action-button btn-add" title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</main>
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
<script>
var weekly_off_module_url = '{{config("constants.WEEKLY_OFF_MASTER_URL")}}' + '/';

    $("#add-weekly-off-model-form").validate({
        errorClass: "invalid-input",
        onfocusout: false,
		onkeyup: false,
        rules: {
            weekly_off_name: {
                required: true,
                noSpace:true,
                validateUniqueWeeklyOffName:true
            },
        },
        messages: {
            weekly_off_name: {
                required: "{{ trans('messages.require-weekly-off-name') }}"
            },
        },
        submitHandler: function(form) {
            showLoader()
            form.submit();
        }
    });

    function openWeeklyOffModel(thisitem){
    	editWeeklyOffModel(thisitem);
    }

    function addWeeklyOffModel(){
    	if($('#add-weekly-off-model-form').valid() != true){
			return false;
		}
    	 var checked_weekly_length = $('.weekly-off-selection:checked').length;
         
         if(checked_weekly_length > 0){
         	var weekly_off_selection_status = false;
	            $('.weekly-off-selection:checked').each(function(){
	            	weekly_off_selection_status = true;
	            });
         }
         var found_alternate_week_off_selected = false;
         var found_all_week_off_selected = false;
		 var all_week_off_selected_length = false	
         $(".weekly-off-selection").each(function(){
             if($(this).prop('checked') != false ){
                 var field_name = $.trim($(this).attr('name'));
                 if( $("."+field_name+"-selection-div").find('.week-alternate-off:checked').length > 0 ){
                	found_alternate_week_off_selected = true; 
				 }
                 if( $("."+field_name+"-selection-div").find('.week-all-off:checked').length > 0 ){
					found_all_week_off_selected = true
				 }
            }
		});

	   if( found_alternate_week_off_selected != false && found_all_week_off_selected != true  ){
		   alertifyMessage("error","{{ trans('messages.required-one-all-off-selection-for-alternate') }} ");
		   return false
	   }
         
         //var alternate_checked_length = 
         if( weekly_off_selection_status != true ){
         	alertifyMessage("error","{{ trans('messages.required-week-off-days-selection') }} ");
     		return false;
         }
         
    	var record_id = $.trim($('[name="record_id"]').val());
    	var weekly_off_name = $.trim($('[name="weekly_off_name"]').val());
    	var week_off_description = $.trim($('[name="week_off_description"]').val());
    	var monday = $.trim($("[name='monday']:checked").val());
    	var tuesday = $.trim($("[name='tuesday']:checked").val());
    	var wednesday = $.trim($("[name='wednesday']:checked").val());
    	var thursday = $.trim($("[name='thursday']:checked").val());
    	var friday = $.trim($("[name='friday']:checked").val());
    	var saturday = $.trim($("[name='saturday']:checked").val());
    	var sunday = $.trim($("[name='sunday']:checked").val());
		
    	
    	 if(record_id == 0){
 	    	confirm_box = "{{ trans('messages.add-weekly-off') }}";
 	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-weekly-off')]) }}";
 	    } else {
 	    	confirm_box = "{{ trans('messages.update-weekly-off') }}";
 	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-weekly-off')]) }}";
 	    }

    	var form_data = new FormData($('#add-weekly-off-model-form')[0]);
    	form_data.append('record_id' , record_id );
   	     
 	    alertify.confirm(confirm_box,confirm_box_msg,function() {   
 			$.ajax({
 				type: "POST",
 				dataType: "json",
 				url: weekly_off_module_url + 'add',
 				data: form_data,
 				processData:false,
				contentType:false,
 				beforeSend: function() {
 					//block ui
 					showLoader();
 				},
 				success: function(response) {
 					hideLoader();
 					if( response.status_code == 1 ){
 						$("#weekly-off-model").modal('hide');
 						alertifyMessage('success',response.message);
 						if(record_id != '' && record_id != null){
							$(current_row).parents('.weekly-off-record').html(response.data.html);
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
	var current_row = "";
    function editWeeklyOffModel(thisitem){
    	current_row = thisitem;
    	var record_id = $.trim($(thisitem).attr('data-record-id'));
    	$.ajax({
    		type: "POST",
    		url: weekly_off_module_url + 'edit',
    		data: {
    			"_token": "{{ csrf_token() }}",'record_id':record_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			if(record_id !="" && record_id != null){
    				var header_name = "{{ trans('messages.update-weekly-off') }}";
    				var button_name = "{{ trans('messages.update') }}";
    				$('.add-weekly-off-html').html("");
    				$('.add-weekly-off-html').html(response);
    				$("[name='record_id']").val(record_id);
    				$("#weekly-off-model").find('.weekly-off-action-button').html(button_name);
    				$('.weekly-off-model-action-button').attr('title' , "{{ trans('messages.update') }}");
    				$("#weekly-off-model").find('.twt-weekly-off-header-name').html(header_name);
        			
    			} else {
    				var header_name = "{{ trans('messages.add-weekly-off') }}";
    				var button_name = "{{ trans('messages.add') }}";
    				$('.add-weekly-off-html').html("");
    				$('.add-weekly-off-html').html(response);
    				$("[name='record_id']").val("");
    				$("#weekly-off-model").find('.weekly-off-action-button').html(button_name);
    				$('.weekly-off-model-action-button').attr('title' , "{{ trans('messages.update') }}");
    				$("#weekly-off-model").find('.twt-weekly-off-header-name').html(header_name);
        			
        		}
    			openBootstrapModal('weekly-off-model');
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
    $.validator.addMethod("validateUniqueWeeklyOffName", function (value, element) {
     	 
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: weekly_off_module_url +'checkUniqueWeeklyOffName',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'weekly_off_name': $.trim($("[name='weekly_off_name']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
    }, '{{ trans("messages.error-unique-weekly-off-name") }}');
    
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

    	searchAjax(weekly_off_module_url + 'filter' , searchFieldName);
    }

    var paginationUrl = weekly_off_module_url + 'filter';

    function showDayOffSelection(thisitem){
		var selected_status = $(thisitem).prop('checked');
		var selected_day = $.trim($(thisitem).attr('data-day'));
		//console.log("selected_status = " + selected_status );
		//console.log("selected_day = " + selected_day );

		var alternate_off_field_name = 'alternate_off_' + selected_day;
	
		
		var alternate_off_selection_status = $("[name='"+alternate_off_field_name+"']").prop('checked');
		
		if( selected_status != false ){ 
			$("."+selected_day+"-selection-div").show();
			if( alternate_off_selection_status != true ){
				var all_selected_value = "{{ config('constants.ALL_STATUS') }}";
				$("[name='"+alternate_off_field_name+"'][value='"+all_selected_value+"']").prop("checked",true);
			}
		} else {
			$("."+selected_day+"-selection-div").hide();	
		}
		
    }
    
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script> 

@endsection