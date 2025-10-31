@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.holiday-master") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            @if(checkPermission('add_holiday_master') != false)
            <button type="button" onclick='openHolidayMasterModel(this)' class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar d-sm-flex align-items-center"  title="{{ trans('messages.add-holiday') }}"><i class="fas fa-plus mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.add-holiday") }}</span> </button>
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
                            <input type="text" name="search_by" class="form-control" placeholder='{{ trans("messages.search-by") }} {{ trans("messages.holiday-name") }}'>
                        </div>
                    </div>

                    <div class="form-group col-lg-2 col-6">
                        <label for="search_from_date" class="control-label">{{ trans("messages.from-date") }}</label>
                        <input type="text" class="form-control" name="search_from_date" placeholder="DD-MM-YYYY" autocomplete="off" />
                    </div>
                    <div class="form-group col-lg-2 col-6">
                        <label for="search_to_date" class="control-label">{{ trans("messages.to-date") }}</label>
                        <input type="text" class="form-control" name="search_to_date" placeholder="DD-MM-YYYY" autocomplete="off" />
                    </div>

                    <div class="col-lg-3 col-md-6">
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
                                <th class="text-left" style="min-width:145px; width:350px">{{ trans("messages.holiday-name") }}</th>
                                <th class="text-left" style="min-width:100px;">{{ trans("messages.date") }}</th>
                                <th class="text-center">{{ trans("messages.status") }}</th>
                                @if((checkPermission('edit_holiday_master') != false) || (checkPermission('delete_holiday_master') != false))
                                <th class="actions-col">{{ trans("messages.actions") }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                            @include( config('constants.AJAX_VIEW_FOLDER') . 'holiday-master/holiday-master-list')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>




    <div class="modal fade document-folder" id="add-holiday-master-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans("messages.add-holiday") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>
                {!! Form::open(array( 'id '=> 'add-holiday-master-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                    <div class="modal-body add-holiday-master-html">
                        
                    </div>
                    <div class="modal-footer justify-content-end">
                    	<input type="hidden" name="record_id" value="">
                        <button type="button" onclick='addHolidayMasterModel()' class="btn bg-theme text-white action-button holiday-master-action-button btn-add" title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>


</main>
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
<script>

    $("#add-holiday-master-form").validate({
    	errorClass: "invalid-input",
		onfocusout: false,
		onkeyup: false,
        rules: {
            holiday_name: {
                required: true,
                noSpace: true,
            },
            holiday_date: {
                required: true,
                noSpace: true,
                validateUniqueHolidayDate:true
            },
        },
        messages: {
            holiday_name: {
                required: "{{ trans('messages.require-holiday-name') }}"
            },
            holiday_date: {
                required: "{{ trans('messages.require-holiday-date') }}"
            },
        },
        submitHandler: function(form) {
            showLoader()
            form.submit();
        }
    });

    $(function() {
        $(' [name="search_from_date"], [name="search_to_date"]').datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            format: 'DD-MM-YYYY',
            showClear: true,
            showClose: true,
            widgetPositioning: {
                vertical: 'bottom',
                horizontal: 'auto'

            },
            icons: {
                clear: 'fa fa-trash',
                Close: 'fa fa-trash',
            },
        });

        $("[name='search_from_date']").datetimepicker().on('dp.change', function(e) {
    		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    			var incrementDay = moment((e.date)).startOf('d');
    		 	$("[name='search_to_date']").data('DateTimePicker').minDate(incrementDay);
    		} else {
    			$("[name='search_to_date']").data('DateTimePicker').minDate(false);
    		} 
    		
    	    $(this).data("DateTimePicker").hide();
    	});

        $("[name='search_to_date']").datetimepicker().on('dp.change', function(e) {
        	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    	        var decrementDay = moment((e.date)).endOf('d');
    	        $("[name='search_from_date']").data('DateTimePicker').maxDate(decrementDay);
        	} else {
        		 $("[name='search_from_date']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });
    });

    var holiday_module_url = '{{config("constants.HOLIDAY_MASTER_URL")}}' + '/';
    function openHolidayMasterModel(thisitem){
    	editHolidayMasterModel(thisitem);
    }
    function addHolidayMasterModel(){
    	if($('#add-holiday-master-form').valid() != true){
			return false;
		}
    	var record_id = $.trim($('[name="record_id"]').val());
		var holiday_name = $.trim($('[name="holiday_name"]').val());
		var holiday_date = $.trim($('[name="holiday_date"]').val());
		
		var confirm_box = "";
	    var confirm_box_msg = "";
	    
	    if(record_id == 0){
	    	confirm_box = "{{ trans('messages.add-holiday') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-holiday')]) }}";
	    } else {
	    	confirm_box = "{{ trans('messages.update-holiday') }}";
	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-holiday')]) }}";
	    } 
	    
	    alertify.confirm(confirm_box,confirm_box_msg,function() {   
			$.ajax({
				type: "POST",
				dataType: "json",
				url: holiday_module_url + 'add',
				data: {"_token": "{{ csrf_token() }}",
					'holiday_name':holiday_name,'holiday_date':holiday_date,'record_id':record_id,
					'row_index':$(current_row).parents('tr').find('.sr-col').html(),
					},
				
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if( response.status_code == 1 ){
						$("#add-holiday-master-model").modal('hide');
						alertifyMessage('success',response.message);
						
						if(record_id != '' && record_id != null){
							$(current_row).parents('.holiday-record').html(response.data.html);
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
    function editHolidayMasterModel(thisitem){
    	current_row = thisitem;
    	var record_id = $.trim($(thisitem).attr('data-record-id'));
    	$.ajax({
    		type: "POST",
    		url: holiday_module_url + 'edit',
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
    				var header_name = "{{ trans('messages.update-holiday') }}";
    				var button_name = "{{ trans('messages.update') }}";
    				$('.add-holiday-master-html').html('');
    				$('.add-holiday-master-html').html(response);
    				$("[name='record_id']").val(record_id);
    				$("#add-holiday-master-model").find('.holiday-master-action-button ').html(button_name);
    				$('.holiday-master-action-button ').attr('title' , "{{ trans('messages.update') }}");
    				$("#add-holiday-master-model").find('.twt-modal-header-name').html(header_name);
    			} else {
    				var header_name = "{{ trans('messages.add-holiday') }}" ;
    				var button_name = "{{ trans('messages.add') }}" ;
    				$('.add-holiday-master-html').html('');
    				$('.add-holiday-master-html').html(response);
    				$("[name='record_id']").val("");
    				$("#add-holiday-master-model").find('.holiday-master-action-button ').html(button_name);
    				$('.holiday-master-action-button ').attr('title' , "{{ trans('messages.add') }}");
    			}
    			$("[name='holiday_date']").datetimepicker({
    		    	useCurrent: false,
    		        viewMode: 'days',
    		        ignoreReadonly: true,
    		        format: 'DD-MM-YYYY',
    		        showClear: true,
    		        showClose: true,
    		        widgetPositioning: {
    		            vertical: 'top',
    		            horizontal: 'auto'

    		        },
    		        icons: {
    		            clear: 'fa fa-trash',
    		            Close: 'fa fa-trash',
    		        },
    		    	
    		    });
    			if(record_id !="" && record_id != null){
    				$("[name='holiday_date']").data('DateTimePicker').minDate(false);
        		} else {
        			$("[name='holiday_date']").data('DateTimePicker').minDate(moment().subtract(1, "month").startOf('d'));
                }
    			openBootstrapModal('add-holiday-master-model');
    		
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
    function searchField(){
    	var search_by = $.trim($('[name="search_by"]').val());
    	var search_status = $.trim($('[name="search_status"]').val());
    	var search_from_date  = $.trim($('[name="search_from_date"]').val());
    	var search_to_date = $.trim($('[name="search_to_date"]').val());
    	var searchData = {
                'search_by':search_by,
                'search_status': search_status,
                'search_from_date':search_from_date,
                'search_to_date':search_to_date,
            }
            return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(holiday_module_url + 'filter' , searchFieldName);
    }

    $.validator.addMethod("validateUniqueHolidayDate", function (value, element) {
      	 
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: holiday_module_url +'checkUniqueHolidayDate',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'holiday_date': $.trim($("[name='holiday_date']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
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
    }, '{{ trans("messages.error-unique-holiday-date") }}');
    
    var paginationUrl = holiday_module_url + 'filter'
</script>
<script type="text/javascript" src="{{ asset ('js/twt_scroll_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection