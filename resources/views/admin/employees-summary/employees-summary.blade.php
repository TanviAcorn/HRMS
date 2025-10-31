@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.employees-summary") }}</h1>
    </div>
    <div class="container-fluid attendance-summary pb-3">
        <div class="row py-4 align-items-center">
            <div class="col-lg-8 col-md-6 mb-md-0 mb-3">
                <h5 class="bg-title">{{ trans("messages.employees") }}</h5>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group mb-0">
                    <select class="form-control select2" name="search_designation_name" onchange="filterEmployeeSummary()">
                        <option value="">{{ trans('messages.all-team') }}</option>
                         <?php 
						if(!empty($designationDetails)){
	                    	foreach ($designationDetails as $designationDetail){
	                        	$designationyEncodeId  = Wild_tiger::encode($designationDetail->i_id);
	                         	?>
	                            <option value="{{$designationyEncodeId}}">{{ (!empty($designationDetail->v_value ) ? $designationDetail->v_value :'')}}</option>
	                            <?php 
							}
	                  	}
	                    ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group mb-0">
                    <select class="form-control select2" name="search_city" onchange="filterEmployeeSummary()">
                     <option value="">{{ trans('messages.all-cities') }}</option>
                        <?php 
                        	if (!empty($cityDetails)){
                        		foreach ($cityDetails as $cityDetail){
                        			$cityEncodeId = Wild_tiger::encode($cityDetail->i_id);
                        		    ?>
                        			<option value="{{ $cityEncodeId }}">{{ (!empty($cityDetail->v_city_name ) ? $cityDetail->v_city_name : '')}}</option>
                        		<?php }
                        	}
                       		?>
                    </select>
                </div>
            </div>
        </div>
		<div class="filter-employee-summary">
		
		</div>
    </div>
</main>
<script>
function filterEmployeeSummary(){
	var search_employee_state_id = $.trim($("[name='search_employee_state_name']").val());
	var search_designation_id = $.trim($("[name='search_designation_name']").val());
	var search_city_id = $.trim($("[name='search_city']").val());

	$.ajax({
		type: "POST",
		url: '{{config("constants.EMPLOYEE_SUMMARY_MASTER_URL")}}' + '/filterEmployeeSummary',
		data:{'search_designation_id':search_designation_id , 'search_city_id' : search_city_id },
		beforeSend: function() {
			//block ui
			showLoader();
		},
		success: function(response) {
			hideLoader();
			if( response != "" && response != null ){
				response = $.trim(response);
				$(".filter-employee-summary").html(response);
			}
		},
		error: function() {
			hideLoader();
		}
	});
}
$(document).ready(function(){
	filterEmployeeSummary();
})
</script>
@endsection