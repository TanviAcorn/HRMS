	<div class="container-fluid my-leaves pb-3">
        <div class="row py-4 align-items-center">
            <div class="col-lg-10 col-md-9 mb-md-0 mb-3">
                <h5 class="bg-title">{{ trans("messages.time-off-summary") }}</h5>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6">
                <div class="form-group mb-0">
                    <select class="form-control" name="search_time_off_academic_year" onchange="filterTimeOffDashboard();">
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
		<div class="filter-time-off-dashboard-html">
	        @include(config('constants.AJAX_VIEW_FOLDER') .'time-off/time-off-summary')
        </div>
    </div>
     <div class="document-folder apply-time-off">
        @include(config('constants.ADMIN_FOLDER') .'time-off/apply-time-off')
    </div>
    @include('admin/time-off-policy-modal')
    <?php /*?> @include(config('constants.ADMIN_FOLDER') .'time-off/time-off-policy') <?php */?>
    <script>
	function filterTimeOffDashboard(){
		var search_academic_year = $.trim($("[name='search_time_off_academic_year']").val());
		$.ajax({
			type: "POST",
			url: '{{config("constants.TIME_OFF_MASTER_URL")}}' + '/filterTimeOffDashboard',
			data:{ 'academic_year' : search_academic_year },
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if( response != "" && response != null ){
					response = $.trim(response);
					$(".filter-time-off-dashboard-html").html(response);
				}
			},
			error: function() {
				hideLoader();
			}
		});
	}
	
	
	</script>