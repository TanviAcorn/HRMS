@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-2 header-title mb-0 long-name-title" id="pageTitle">{{ $pageTitle}}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            @if(isset($recordType) && (($recordType == config('constants.NOTICE_PERIOD_POLICY') && checkPermission('add_notice_period_policy_master') != false) || ($recordType == config('constants.PROBATION_POLICY') && checkPermission('add_probation_policy_master') != false)))
                <button type="button" onclick="opneProbationPolicyModel(this)" data-record-type="{{ $recordType }}" class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar d-sm-flex align-items-center" title="{{ $addPageTitle }}"><i class="fas fa-plus mr-sm-2"></i> <span class="d-sm-block d-none"> {{ $addPageTitle }}</span> </button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <?php
        if($pageTitle == trans('messages.notice-period-policy-master')){
        	$tableSearchPlaceholder = trans('messages.search-by-notice-policy');
        } else {
        	$tableSearchPlaceholder = trans('messages.search-by-probation-policy');
        }
        ?>
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_by">{{ trans("messages.search-by") }}</label>
                            <input type="text" name="search_by" class="form-control" placeholder="{{ $tableSearchPlaceholder }}">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_months_weeks_days">{{ trans("messages.months-weeks-days") }}</label>
                            <select class="form-control" name="search_months_weeks_days" onchange="filterData()">
                               <?php 
                                if(!empty($getMonthWeeksDaysInfo)){
                                	foreach ($getMonthWeeksDaysInfo as $key => $getMonthWeeksDays){
                                  		?>
                                    	<option value='{{ $key }}'>{{ (!empty($getMonthWeeksDays) ? $getMonthWeeksDays :'') }}</option>
                                      	<?php 
                                 	}
                                }
                              ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
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
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData()">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
            <div class="card card-body">
            {{ Wild_tiger::readMessage() }}
                <div class="table-responsive fixed-tabel-body probation-table">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <?php if($pageTitle == trans('messages.notice-period-policy-master')){ ?>
                                	<th class="text-left" style="min-width:150px;">{{ trans("messages.notice-policy-name") }}</th>
                                	<th class="text-left" style="min-width:250px;">{{ trans("messages.notice-policy-description") }}</th>
                                	<th class="text-left" style="min-width:170px; width:100px">{{ trans("messages.notice-policy-duration") }}<br>{{ trans("messages.months-weeks-days") }}</th>
                                
                                <?php } else { ?>
                                	<th class="text-left" style="min-width:150px;">{{ trans("messages.probation-policy-name") }}</th>
                                	<th class="text-left" style="min-width:250px;">{{ trans("messages.probation-policy-description") }}</th>
                                	<th class="text-left" style="min-width:170px; width:100px">{{ trans("messages.probation-policy-duration") }}<br>{{ trans("messages.months-weeks-days") }}</th>
                                
                                <?php } ?>
                                <th class="text-center" style="min-width:80px;">{{ trans("messages.status") }}</th>
                                <th class="actions-col" style="min-width:150px;width:150px">{{ trans("messages.actions") }}</th>
                            </tr>
                        </thead>
                         <tbody class='ajax-view'>
                          	@include( config('constants.AJAX_VIEW_FOLDER') . 'probation-policy-master/probation-policy-master-list')
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade document-folder" id="add-probation-policy-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans("messages.add-probation-policy") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-probation-policy-master-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                    <div class="modal-body add-probation-policy-html">
                       
                    </div>
                    <div class="modal-footer justify-content-end">
                    <input type="hidden" name="record_id" value="">
                    <input type="hidden" name="record_type" value="">
                        <button type="button" onclick="addProbationPolicyMasterModel()" class="btn bg-theme text-white action-button probation-policy-model-action-button btn-add" title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>


</main>

<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
<script>
var twt_record_type = "{{ isset($recordType)  ? $recordType : "" }}";
    $("#add-probation-policy-master-form").validate({
        errorClass: "invalid-input",
        rules: {
            probation_policy_name: {
                required: true,
                noSpace: true,
            },
            probation_policy_description: {
                required: false,
                noSpace: true,
            },
            probation_policy_duration: {
                required: true,
                noSpace: true,
            },
            months_weeks_days: {
                required: true,
                noSpace: true,
            },
        },
        messages: {
            probation_policy_name: {
    			required: function(){
    				return ( ( $.trim($('[name="record_type"]').val()) == '{{config("constants.NOTICE_PERIOD_POLICY")}}' ) ?  "{{ trans('messages.require-notice-policy-name') }}" :  "{{ trans('messages.require-probation-policy-name') }}" );
    			} 
    		},
            probation_policy_description: {
            	required: function(){
    				return ( ( $.trim($('[name="record_type"]').val()) == '{{config("constants.NOTICE_PERIOD_POLICY")}}' ) ?  "{{ trans('messages.require-notice-policy-description') }}" :  "{{ trans('messages.require-probation-policy-description') }}" );
    			} 
            },
            probation_policy_duration: {
            	required: function(){
    				return ( ( $.trim($('[name="record_type"]').val()) == '{{config("constants.NOTICE_PERIOD_POLICY")}}' ) ?  "{{ trans('messages.require-notice-period-duration') }}" :  "{{ trans('messages.require-probation-period-duration') }}" );
    			} 
            },
            months_weeks_days: {
                required: "{{ trans('messages.require-months-weeks-days') }}"
            },
        },
        submitHandler: function(form) {
            showLoader()
            form.submit();
        }
    });
    var probation_policy_module_url = '{{config("constants.PROBATION_POLICY_MASTER_URL")}}' + '/';
    function opneProbationPolicyModel(thisitem){
    	editProbationPolicyModel(thisitem);
    	
    }
    function addProbationPolicyMasterModel(){

    	if($('#add-probation-policy-master-form').valid() != true){
			return false;
		}
    	var record_type = $.trim($('[name="record_type"]').val());
    	var record_id = $.trim($('[name="record_id"]').val());
		var probation_policy_name = $.trim($('[name="probation_policy_name"]').val());
		var probation_policy_description = $.trim($('[name="probation_policy_description"]').val());
		var probation_policy_duration = $.trim($('[name="probation_policy_duration"]').val());
		var months_weeks_days = $.trim($('[name="months_weeks_days"]').val());
		
		var confirm_box = "";
	    var confirm_box_msg = "";
	    
	    if(record_id == 0){
		    if(record_type == '{{ config("constants.NOTICE_PERIOD_POLICY")}}'){
		    	confirm_box = "{{ trans('messages.add-notice-period-policy') }}";
		    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-notice-period-policy')]) }}";
		    } else {
		    	confirm_box = "{{ trans('messages.add-probation-policy') }}";
		    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-probation-policy')]) }}";
		    }
	    	
	    } else {
	    	if(record_type == '{{ config("constants.NOTICE_PERIOD_POLICY")}}'){
		    	confirm_box = "{{ trans('messages.update-notice-period-policy') }}";
		    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-notice-period-policy')]) }}";
		    } else {
		    	confirm_box = "{{ trans('messages.update-probation-policy') }}";
		    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-probation-policy')]) }}";
		    }
	    	
	    } 
	    
	    alertify.confirm(confirm_box,confirm_box_msg,function() {   
			$.ajax({
				type: "POST",
				dataType: "json",
				url: probation_policy_module_url + 'add',
				data: {
					"_token": "{{ csrf_token() }}",
					'probation_policy_name':probation_policy_name,'probation_policy_description':probation_policy_description,'record_id':record_id,
					'probation_policy_duration':probation_policy_duration,'months_weeks_days':months_weeks_days,'record_type':record_type,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),
				},
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						$("#add-probation-policy-model").modal('hide');
						alertifyMessage('success',response.message);
						if(record_id != '' && record_id != null){
							$(current_row).parents('.probation-policy-record').html(response.data.html);
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
    function editProbationPolicyModel(thisitem){
    	current_row = thisitem;
    	var record_id = $.trim($(thisitem).attr('data-record-id'));
		var record_type = $.trim($(thisitem).attr('data-record-type'));
		
		$("[name='record_type']").val(record_type);
    	$.ajax({
    		type: "POST",
    		url: probation_policy_module_url + 'edit',
    		data: {
    			"_token": "{{ csrf_token() }}",
    			record_id: record_id,
    			record_type:record_type,
    			
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			if(record_id !="" && record_id != null){
    				if(record_type == '{{config("constants.NOTICE_PERIOD_POLICY")}}'){
    					var header_name = "{{ trans('messages.update-notice-period-policy') }}" ;
        			} else {
        				var header_name = "{{ trans('messages.update-probation-policy') }}";
        				
        			}
    				var button_name = "{{ trans('messages.update') }}";
    				$('.add-probation-policy-html').html("");
    				$('.add-probation-policy-html').html(response);
    				$("[name='record_id']").val(record_id);
    				$("#add-probation-policy-model").find('.probation-policy-model-action-button').html(button_name);
    				$('.probation-policy-model-action-button').attr('title' , "{{ trans('messages.update') }}");
    				$("#add-probation-policy-model").find('.twt-modal-header-name').html(header_name);
    			} else {
        			if(record_type == '{{config("constants.NOTICE_PERIOD_POLICY")}}'){
    					var header_name = "{{ trans('messages.add-notice-period-policy') }}" ;
        			} else {
        				var header_name = "{{ trans('messages.add-probation-policy') }}" ;
        			}
    				var button_name = "{{ trans('messages.add') }}" ;
    				$('.add-probation-policy-html').html("");
    				$('.add-probation-policy-html').html(response);
    				$("[name='record_id']").val("");
    				$("#add-probation-policy-model").find('.probation-policy-model-action-button').html(button_name);
    				$('.probation-policy-model-action-button').attr('title' , "{{ trans('messages.add') }}");
    				$("#add-probation-policy-model").find('.twt-modal-header-name').html(header_name);
    			}
    			openBootstrapModal('add-probation-policy-model');
    		
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
    function searchField(){
    	var search_by = $.trim($('[name="search_by"]').val());
    	var search_status = $.trim($('[name="search_status"]').val());
    	var search_months_weeks_days = $.trim($('[name="search_months_weeks_days"]').val());
    	var record_type = twt_record_type;
    	var searchData = {
                'search_by':search_by,
                'search_status': search_status,
                'search_months_weeks_days':search_months_weeks_days,
                'record_type':record_type,
                
            }
            return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(probation_policy_module_url + 'filter' , searchFieldName);
    }
    var paginationUrl = probation_policy_module_url + 'filter'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection