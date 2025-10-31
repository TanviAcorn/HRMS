<div class="container-fluid pt-0 px-0 employee-payslip">
    <div class="filter-result-wrapper card">
        <section class="inner-wrapper-common-sections main-listing-section pt-4">
            <div class="container-fluid">
                <div class="col-xl-3 col-sm-6 mb-4 p-0">
                    <div class="form-group">
                        <label for="search_year" class="lable-control panel-title">{{ trans('messages.select-year') }}</label>
                        	<select class="form-control" name="search_salary_slip_year" onchange="filterData()">
                            <?php /* ?>
                            <?php for($i= date('Y') ; $i >= config('constants.SYSTEM_START_YEAR') ; $i-- ){ ?>
                           	 	<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php } ?>
                            <?php */ ?>
                            
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
                <div class="row ajax-view position-relative">
                    @include(config('constants.AJAX_VIEW_FOLDER') . 'salary/my-payslip')
                </div>
            </div>
        </section>
    </div>
    <input type="hidden" name="send_payslip_employee_id" value="{{ ( isset($employeeId) && (!empty($employeeId)) ? Wild_tiger::encode($employeeId) : '' ) }}">
</div>

<script>
    var paginationUrl = '{{ config('constants.MY_PAYSLIP_URL') }}' + '/filter';
    function searchField() {
        var search_year = $.trim($("[name='search_salary_slip_year']").val());
        var employee_id = $.trim($("[name='send_payslip_employee_id']").val());

        var searchData = {
            'search_year': search_year,
            'employee_id' : employee_id 
        }

        return searchData;
    }

    function filterData() {
        var searchFieldName = searchField();
		searchAjax(paginationUrl, searchFieldName);
    }

    
</script>

