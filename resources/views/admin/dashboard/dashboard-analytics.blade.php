<style>
    /* Ensure chart containers always have dimensions */
    #analyticsView canvas {
        min-height: 300px !important;
    }
    #analyticsView .card-body > div[style*="position: relative"] {
        min-height: 400px !important;
        display: block !important;
    }
</style>

<div id="analyticsView" class="dashboard-view active">
    
    <!-- Year Filter for Additions/Attritions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <label for="yearFilter" class="mb-0 mr-2 font-weight-bold">Filter Year:</label>
                <select id="yearFilter" class="form-control w-auto">
                    @php
                        $currentYear = date('Y');
                        for ($year = $currentYear; $year >= 2020; $year--) {
                            echo "<option value='{$year}'" . ($year == $currentYear ? " selected" : "") . ">{$year}</option>";
                        }
                    @endphp
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Employee Count by Designation -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-users text-primary"></i> Employee Count by Designation
                    </h5>
                </div>
                <div class="card-body" style="min-height: 400px; position: relative;">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <canvas id="designationChart" width="600" height="400"></canvas>
                    <div id="designationTable" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Employee Count by Team -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-building text-success"></i> Employee Count by Team
                    </h5>
                </div>
                <div class="card-body" style="min-height: 400px; position: relative;">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <canvas id="departmentChart" width="600" height="400"></canvas>
                    <div id="departmentTable" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>



    <div class="row">
        <!-- Employee Count by Status -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-user-check text-warning"></i> Employee Count by Status
                    </h5>
                </div>
                <div class="card-body" style="min-height: 400px; position: relative;">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-warning" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <canvas id="statusChart" width="600" height="400"></canvas>
                    <div id="statusTable" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Annual CTC by Team -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-dollar-sign text-danger"></i> Annual CTC by Team
                    </h5>
                </div>
                <div class="card-body" style="min-height: 400px; position: relative;">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <canvas id="ctcChart" width="600" height="400"></canvas>
                    <div id="ctcTable" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gender Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-venus-mars text-purple"></i> Gender Distribution
                    </h5>
                </div>
                <div class="card-body" style="min-height: 400px; position: relative;">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-secondary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <canvas id="genderChart" width="600" height="400"></canvas>
                    <div id="genderTable" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Years of Service Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-clock text-primary"></i> Years of Service Distribution
                    </h5>
                </div>
                <div class="card-body" style="min-height: 400px; position: relative;">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <canvas id="serviceChart" width="600" height="400"></canvas>
                    <div id="serviceTable" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Age Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-birthday-cake text-success"></i> Age Distribution
                    </h5>
                </div>
                <div class="card-body" style="min-height: 400px; position: relative;">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <canvas id="ageChart" width="600" height="400"></canvas>
                    <div id="ageTable" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Monthly Additions & Attritions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-chart-line text-info"></i> Monthly Additions & Attritions
                    </h5>
                </div>
                <div class="card-body" style="height: 350px; position: relative; overflow: hidden;">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-info" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <canvas id="additionsAttritionsChart" width="600" height="300" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
