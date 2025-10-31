			<div class="row">
	            @if(isset($lastSixMonthNetPayAmount) )
	            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
	                <div class="card card-display border-0 px-2 h-100">
	                    <div class="card-body px-2 py-2">
	                        <div class="salary-counts">
	                            <div class="salary-counts-card">
	                                <h5 class="profile-details-title">{{ trans("messages.last-6-months-net-pay") }}</h5>
	                                <p class="salary-counts-numbers">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} {{ decimalAmount($lastSixMonthNetPayAmount) }}</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            @endif
	            @if(isset($lastMonthNetSalaryAmount) )
	            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
	                <div class="card card-display border-0 px-2 h-100">
	                    <div class="card-body px-2 py-2">
	                        <div class="salary-counts">
	                            <div class="salary-counts-card">
	                                <div class="d-flex align-items-center">
	                                    <h5 class="profile-details-title">{{ trans("messages.salary-processed") }}</h5>
	                                    <h6 class="details-title">{{ ( isset($lastMonthSalaryMonthName) ? $lastMonthSalaryMonthName : '' ) }}</h6>
	                                </div>
	                                <p class="salary-counts-numbers">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} {{ decimalAmount($lastMonthNetSalaryAmount) }}</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            @endif
	            @if(isset($lastMonthOnHoldSalaryAmount) )
	            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
	                <div class="card card-display border-0 px-2 h-100">
	                    <div class="card-body px-2 py-2">
	                        <div class="salary-counts">
	                            <div class="salary-counts-card">
	                                <div class="d-flex align-items-center">
	                                    <h5 class="profile-details-title">{{ trans("messages.salary-retain-amount") }}</h5>
	                                    <h6 class="details-title">{{ ( isset($lastMonthSalaryMonthName) ? $lastMonthSalaryMonthName : '' ) }}</h6>
	                                </div>
	                                <p class="salary-counts-numbers">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} {{ decimalAmount($lastMonthOnHoldSalaryAmount) }}</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            @endif
	            @if(isset($teamWiseHighestSalaryAmount) )
	            <div class="col-lg-3 col-sm-6 panding-leave-card mb-3">
	                <div class="card card-display border-0 px-2 h-100">
	                    <div class="card-body px-2 py-2">
	                        <div class="salary-counts">
	                            <div class="salary-counts-card">
	                                <div class="d-flex align-items-center">
	                                    <h5 class="profile-details-title">{{ trans("messages.highest-salary-paid") }}</h5>
	                                    <h6 class="details-title">{{ ( isset($teamWiseHighestSalaryName)   ? $teamWiseHighestSalaryName : '' ) }}</h6>
	                                </div>
	                                <p class="salary-counts-numbers">{{ config('constants.SALARY_CURRENCY_SYMBOL') }} {{ decimalAmount($teamWiseHighestSalaryAmount) }}</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            @endif
	        </div>
	        <div class="row filter-salary-summary">
	        	@include(config('constants.AJAX_VIEW_FOLDER') . 'salary/filter-salary-summary')
	        </div>