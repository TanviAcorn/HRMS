<div class="container-fluid my-leaves px-0 pt-0 pb-3">
    <div class="row pt-0 pb-4 align-items-center">
        <div class="col-lg-10 col-md-9 mb-md-0 mb-3">
            <h5 class="bg-title">{{ trans("messages.leave-summary") }}</h5>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="form-group mb-0">
                <select class="form-control" name="search_academic_year" onchange="filterLeaveDashboard();">
                	@if(count($yearDetails) > 0 )
                    	@foreach($yearDetails as $key => $year)
                    		<?php  
                    		$selected = '';
                    		if( isset($currentYear)  && ( $currentYear == $key ) ){
                    			$selected = "selected='selected'" ;
                    		} 
                    		?>
                    		<option value="{{ $key }}" {{ $selected }} >{{ $year }}</option>
                    	@endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>
	<div class="filter-leave-view-html">
		@include(config('constants.AJAX_VIEW_FOLDER') .'my-leaves/leave-summary')
	</div>
</div>
<input type="hidden" name="employee_leave_id" value="{{ isset($employeeId) ? $employeeId : Wild_tiger::encode( session()->get('user_employee_id') )  }}">
<!-- allpy leave modal-->

@include(config('constants.AJAX_VIEW_FOLDER') .'my-leaves/leave-modal')
@include('admin/leave-policy-modal')

<?php /*?>
@include(config('constants.ADMIN_FOLDER') .'my-leaves/leave-policy-modal')
<?php */?>

<script>
$(document).ready(function(){
	$(".filter-leave-view-html").find(".process-chart").trigger('change');
	filterLeaveDashboard();
	
})

	function filterLeaveDashboard(){
    	var search_academic_year = $.trim($("[name='search_academic_year']").val());
    	var employee_leave_id =  $.trim($('[name="employee_leave_id"]').val());
    	$.ajax({
    		type: "POST",
    		url: '{{config("constants.MY_LEAVES_MASTER_URL")}}' + '/filterLeaveDashboard',
    		data:{ 'academic_year' : search_academic_year , 'employee_id'  : employee_leave_id  },
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			if( response != "" && response != null ){
    				response = $.trim(response);
    				$(".filter-leave-view-html").html(response);
    				$(".filter-leave-view-html").find(".process-chart").trigger('change');
    				$(".leave-count").trigger("keyup");
    			}
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    }
    
</script>
<script>
   
</script>
