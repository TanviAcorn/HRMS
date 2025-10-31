@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.uploaded-attendance-summary") }}</h1>
        <?php /* ?>
        <span class="head-total-counts total-record-count"></span>
        <?php */ ?>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            @if(checkPermission('add_uploaded_attendance_summary') != false)
                <button type="button" title="{{ trans('messages.upload-daily-attendance') }}" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2" onclick="openDailyAttendanceUploadModal(this);" ><i class="fas fa-upload mr-0 mr-sm-2 fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.upload-daily-attendance") }}</span></button>
            @endif
            <button class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_from_month">{{ trans("messages.from-month") }}</label>
                            <input type="text" name="search_from_month" class="form-control" value="{{ date('M-Y')}}" placeholder="{{ trans('messages.mm-yyyy') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_to_month">{{ trans("messages.to-month") }}</label>
                            <input type="text" name="search_to_month" class="form-control" value="{{ date('M-Y')}}" placeholder="{{ trans('messages.mm-yyyy') }}">
                        </div>
                    </div>
					<div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}" onclick="filterData();" >{{ trans("messages.search") }}</button>
                        <button class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
            <div class="card card-body">
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="width:200px;min-width:200px;">{{ trans("messages.date") }}</th>
                                <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.status") }}</th>
                                @if(checkPermission('add_uploaded_attendance_summary') != false)
                                    <th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.action") }}</th>
                                @endif
							</tr>
                        </thead>
                        <tbody class="ajax-view">
                            @include( config('constants.AJAX_VIEW_FOLDER') . 'upload-daily-attendance-summary/upload-daily-attendance-summary-list')	
						</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>


<div class="modal fade" id="upload-daily-attendance-modal" role="dialog" aria-modal="true" >
    <div class="modal-dialog modal-dialog-centered" role="document">
        {!! Form::open(array( 'id '=> 'upload-daily-attendance-form' , 'method' => 'post' )) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('messages.upload-daily-attendance') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   		<span aria-hidden="true"><i class="fas fa-times"></i></span>
                	</button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row related-field">
                            <div class="col-lg-12">
                            	<div class="form-group">
                                	<label for="upload_daily_attendance_date" class="font-weight-bold">{{ trans('messages.attendance-date') }}<span class="text-danger">*</span></label>
                                	<input type="text" class="form-control" readonly placeholder="{{ trans('messages.attendance-date') }}" name="upload_daily_attendance_date">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <label for="upload_excel" class="font-weight-bold">{{ trans('messages.upload-excel') }}<span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="upload_daily_attendance" name="upload_daily_attendance">
                                    <label class="custom-file-label" for="upload_daily_attendance">{{ trans('messages.choose-file') }}</label>
                                </div>
                                <label id="upload_daily_attendance-error" class="invalid-input" for="upload_daily_attendance" style="display:none;"></label>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0)" download="" class="text-theme btn shadow-none p-0 text-decoration-underline" title="{{ trans('messages.download-sample-excel') }}">
                                    <span class="text-theme ml-1">{{ trans('messages.download-sample-excel') }}</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn bg-theme text-white" onclick="uploadDailyAttendanceFile(this);"  title="{{ trans('messages.upload') }}">{{ trans('messages.upload') }}</button>
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            </div>
        {!! Form::close() !!}    
	</div>
</div>
<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
<script>
    $("[name='search_from_month'],[name='search_to_month']").datetimepicker({
        useCurrent: false,
        ignoreReadonly: true,
        format: 'MMM-YYYY',
        showClose: true,
        showClear: true,
        icons: {
            clear: 'fa fa-trash',
        },
        widgetPositioning: {
            horizontal: 'auto',
            vertical: 'bottom'
        },
        maxDate:moment().endOf('m'),
    });

    $("[name='upload_daily_attendance_date']").datetimepicker({
        useCurrent: false,
        ignoreReadonly: true,
        format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
        showClose: true,
        showClear: true,
        icons: {
            clear: 'fa fa-trash',
        },
        widgetPositioning: {
            horizontal: 'auto',
            vertical: 'bottom'
        },
        maxDate:moment().endOf('d'),
    });

    $(function(){

    	$("[name='search_from_month']").datetimepicker().on('dp.change', function(e) {
    		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    			var incrementDay = moment((e.date)).startOf('d');
    		 	$("[name='search_to_month']").data('DateTimePicker').minDate(incrementDay);
    		} else {
    			$("[name='search_to_month']").data('DateTimePicker').minDate(false);
    		} 
    		
    	    $(this).data("DateTimePicker").hide();
    	});

        $("[name='search_to_month']").datetimepicker().on('dp.change', function(e) {
        	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
    	        var decrementDay = moment((e.date)).endOf('d');
    	        $("[name='search_from_month']").data('DateTimePicker').maxDate(decrementDay);
        	} else {
        		 $("[name='search_from_month']").data('DateTimePicker').maxDate(false);
            }
            $(this).data("DateTimePicker").hide();
        });

   	})
   	
   	$("#upload-daily-attendance-form").validate({
        errorClass: "invalid-input",
        rules: {
        	upload_daily_attendance: {required: true , noSpace: true, extension: 'xls|xlsx|csv'},
        	upload_daily_attendance_date: {required: true , noSpace: true },
        },
        messages: {
        	upload_daily_attendance: { required: "{{ trans('messages.required-upload-file') }}" , extension : "{{ trans('messages.error-only-specific-are-allowed' , [ 'fileType' => 'Excel' ] ) }}"  },
        	upload_daily_attendance_date: { required: "{{ trans('messages.please-enter-date') }}" },
        }
   	});

    $(document).ready(function(){
   		filterData();
   	});
   	

	var upload_daily_attendance_url = '{{config("constants.UPLOAD_DAILY_ATTENDANCE_SUMMARY_URL")}}' + '/';
    
    function searchField(){
    	var search_from_month = $.trim($('[name="search_from_month"]').val());
    	var search_to_month = $.trim($('[name="search_to_month"]').val());

    	var searchData = {
            'search_from_month': search_from_month,
            'search_to_month': search_to_month,
        }
        return searchData;
    }
    function filterData(){
    	var searchFieldName = searchField();

    	searchAjax(upload_daily_attendance_url + 'filter' , searchFieldName);
    }

    function openDailyAttendanceUploadModal(thisitem){
		var selected_date = $.trim($(thisitem).attr('data-date'));
		//console.log("selected_date = " + selected_date );
		$("#upload-daily-attendance-modal").find("[name='upload_daily_attendance_date']").val(selected_date);
    	openBootstrapModal('upload-daily-attendance-modal');
    }

    function uploadDailyAttendanceFile(){

    	if ( $("#upload-daily-attendance-form").valid() != true ){
			return false;
        }

    	var formData = new FormData( $('#upload-daily-attendance-form')[0] );
    	
    	alertify.confirm("{{ trans('messages.upload-daily-attendance') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.upload-daily-attendance')]) }}",function() { 
	    	 $.ajax({
	     		type: "POST",
	     		dataType :'json',
	     		url: upload_daily_attendance_url + 'uploadDailyAttendance',
	     		data:formData,
				processData:false,
				contentType:false,
	     		beforeSend: function() {
	     			//block ui
	     			showLoader();
	     		},
	     		success: function(response) {
	     			hideLoader();
	     			if( response.status_code == 1 ){
						alertifyMessage('success',response.message);
						$("#upload-daily-attendance-modal").modal('hide');
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
    
    var paginationUrl = upload_daily_attendance_url + 'filter'
</script>


@endsection