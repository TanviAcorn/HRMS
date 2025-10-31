@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
@include(config('constants.ADMIN_FOLDER') .'employee-master/emp-upload-document')
@include(config('constants.ADMIN_FOLDER') .'employee-master/emp-view-document')	
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.document-report") }}</h1>
        <span class="head-total-counts total-record-count"></span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if( !in_array( session()->get('role') , [ config('constants.ROLE_USER') ] ) )
            <button type="button" title="{{ trans('messages.export-excel') }}" onclick="exportData()" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel mr-0 mr-sm-2 fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">

        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3 depedent-row">
                <div class="row filter-rows">
                	@if( ( session()->has('is_supervisor') && !empty(session()->get('is_supervisor')) && session()->get('is_supervisor') != false ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_DOCUMENT_REPORT'), session()->get('user_permission')  ) ) ) ) )
	                <div class="col-xl-2 col-lg-4 col-12">
	                <?php echo employeeStatusFilter( (isset($selectedEmployeeStatus) ? $selectedEmployeeStatus : '' ) , (isset($allPermissionId) ? $allPermissionId : '' )  );?>
	                </div>
	                <div class="col-xl-3 col-lg-4 col-12">
	                <?php echo statusWiseEmployeeList( 'search_employee_name' , (isset($employeeDetails) ? $employeeDetails : [] ) );?>
	                </div>
	               
                    <div class="form-group col-lg-2 col-md-3 col-sm-6">
                        <label for="search_joining_from_date" class="control-label">{{ trans("messages.joining-from-date") }}</label>
                        <input type="text" class="form-control" name="search_joining_from_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" autocomplete="off"/>
                    </div>
                    <div class="form-group col-lg-2 col-md-3 col-sm-6">
                        <label for="search_joining_to_date" class="control-label">{{ trans("messages.joining-to-date") }}</label>
                        <input type="text" class="form-control date" name="search_joining_to_date" placeholder="{{ trans('messages.dd-mm-yyyy') }}" autocomplete="off"/>
                    </div>
                     
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control select2" name="search_team" onchange="filterData()">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($teamDetails))
                                	@foreach($teamDetails as $teamDetail)
                                		<option value="{{ (!empty($teamDetail->i_id) ? Wild_tiger::encode($teamDetail->i_id) : 0) }}">{{ (!empty($teamDetail->v_value) ? $teamDetail->v_value :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_designation" class="control-label">{{ trans('messages.designation') }}</label>
                            <select class="form-control select2" name="search_designation" onchange="filterData()">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($designationDetails))
                                	@foreach($designationDetails as $designationDetail)
                                		<option value="{{ (!empty($designationDetail->i_id) ? Wild_tiger::encode($designationDetail->i_id) : 0) }}">{{ (!empty($designationDetail->v_value) ? $designationDetail->v_value :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_document_folder" class="control-label">{{ trans('messages.document-folder') }}</label>
                            <select class="form-control" name="search_document_folder" onchange="getDocumentType(this); filterData();">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($documentFolderDetails))
                                	@foreach($documentFolderDetails as $documentFolderDetail)
                                		<option value="{{ (!empty($documentFolderDetail->i_id) ? Wild_tiger::encode($documentFolderDetail->i_id) : 0) }}">{{ (!empty($documentFolderDetail->v_document_folder_name) ? $documentFolderDetail->v_document_folder_name :'') }}</option>
                                	@endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_document_name" class="control-label">{{ trans('messages.document-type') }}</label>
                            <select class="form-control select2 document-type-filter" name="search_document_name" onchange="filterData()">
                                <option value="">{{ trans("messages.select")}}</option>
                                @if(!empty($documentTypeDetails))
                                	@foreach($documentTypeDetails as $documentTypeDetail)
                                		<option value="{{ (!empty($documentTypeDetail->i_id) ? Wild_tiger::encode($documentTypeDetail->i_id) : 0) }}">{{ (!empty($documentTypeDetail->v_document_type) ? $documentTypeDetail->v_document_type :'') }}</option>
                                	@endforeach
                                @endif
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
                <div class="table-responsive fixed-tabel-body">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col" style="min-width:55px; width:55px;">{{ trans("messages.sr-no") }}</th>
                                <th class="text-left" style="min-width:150px; width:150px;">{{ trans("messages.document-folder") }}</th>
                                <th class="text-left" style="width:300px;min-width:250px;">{{ trans("messages.employee-name-code") }} <br> {{ trans("messages.employee-designation") }}</th>
                                <th class="text-left" style="width:200px;min-width:170px;">{{ trans("messages.employee-team") }} <br> {{ trans("messages.employee-joining-date") }}</th>
                                <th class="text-left" style="width:170px;min-width:170px;">{{ trans("messages.document-type") }}</th>
                                <th class="text-center" style="width:158px;min-width:158px;">{{ trans("messages.actions") }}</th>
                            </tr>
                        </thead>
                        <tbody class='ajax-view'>
                        	@include( config('constants.AJAX_VIEW_FOLDER') . 'report/document-report-list')
                       </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>


<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
<script>
var document_report_url = "{{ config('constants.DOCUMENT_REPORT_URL')}}" + '/';
$(function() {
    $(' [name="search_joining_from_date"], [name="search_joining_to_date"]').datetimepicker({
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

    $("[name='search_joining_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
			var incrementDay = moment((e.date)).startOf('d');
		 	$("[name='search_joining_to_date']").data('DateTimePicker').minDate(incrementDay);
		} else {
			$("[name='search_joining_to_date']").data('DateTimePicker').minDate(false);
		} 
		
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_joining_to_date']").datetimepicker().on('dp.change', function(e) {
    	if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
	        var decrementDay = moment((e.date)).endOf('d');
	        $("[name='search_joining_from_date']").data('DateTimePicker').maxDate(decrementDay);
    	} else {
    		 $("[name='search_joining_from_date']").data('DateTimePicker').maxDate(false);
        }
        $(this).data("DateTimePicker").hide();
    });
});


function searchField(){
	var search_employee_name = $.trim($('[name="search_employee_name"]').val());
	var search_joining_from_date = $.trim($('[name="search_joining_from_date"]').val());
	var search_joining_to_date = $.trim($('[name="search_joining_to_date"]').val());
	var search_team = $.trim($('[name="search_team"]').val());
	var search_designation = $.trim($('[name="search_designation"]').val());
	var search_document_folder = $.trim($('[name="search_document_folder"]').val());
	var search_document_name = $.trim($('[name="search_document_name"]').val());
	var search_employment_status = $.trim($('[name="search_employment_status"]').val());
	
	var searchData = {
            'search_employee_name':search_employee_name,
            'search_joining_from_date': search_joining_from_date,
            'search_joining_to_date':search_joining_to_date,
            'search_team':search_team,
            'search_designation':search_designation,
            'search_document_folder':search_document_folder,
            'search_document_name':search_document_name,
            'search_employment_status':search_employment_status
        }
        return searchData;
}
function filterData(){
	var searchFieldName = searchField();

	searchAjax(document_report_url + 'documentReportFilter' , searchFieldName);
}
var paginationUrl = document_report_url + 'documentReportFilter'

function exportData(){
		var searchData = searchField();
		var export_info = {};
		export_info.url = document_report_url + 'documentReportFilter';
		export_info.searchData = searchData;
		dataExportIntoExcel(export_info);
	}

function getDocumentType(thisitem) {
	
	var document_folder_id = $.trim($(thisitem).val());
	
	$.ajax({
		url : document_report_url + 'getDocumentTypes',
		type : "post",
		async : false,
		data : {
			'document_folder_id' : document_folder_id , 
		},
		beforeSend: function() {
			showLoader();
		},
		success : function (response){
			hideLoader();
			if(response != null && response != "") {
				$(thisitem).parents('.filter-rows').find('.document-type-filter').html(response);
			}
		}
	});
}
</script>

<script type="text/javascript" src="{{ asset ('js/fixed-table-scroll-pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script> 
@endsection