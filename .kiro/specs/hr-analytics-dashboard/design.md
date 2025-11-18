# Design Document

## Overview

The HR Analytics Dashboard feature extends the existing HRMS dashboard by adding a toggle mechanism that allows admin users to switch between a "Welcome" view (current dashboard) and an "Analytics" view containing comprehensive HR reports. The solution leverages the existing Laravel MVC architecture, Bootstrap UI framework, and introduces Chart.js for data visualizations.

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      Browser (Client)                        │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  Dashboard View (dashboard.blade.php)                  │ │
│  │  ┌──────────────┐  ┌──────────────────────────────┐   │ │
│  │  │ Toggle Button│  │  View Container              │   │ │
│  │  └──────────────┘  │  ┌────────────┬────────────┐ │   │ │
│  │                    │  │  Welcome   │ Analytics  │ │   │ │
│  │                    │  │  View      │ View       │ │   │ │
│  │                    │  └────────────┴────────────┘ │   │ │
│  │                    └──────────────────────────────┘   │ │
│  └────────────────────────────────────────────────────────┘ │
│                            │                                 │
│                            │ AJAX Requests                   │
│                            ▼                                 │
└─────────────────────────────────────────────────────────────┘
                             │
┌─────────────────────────────────────────────────────────────┐
│                   Laravel Backend (Server)                   │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  DashboardController                                   │ │
│  │  ├─ index() - Main dashboard                          │ │
│  │  ├─ getAnalyticsData() - Fetch all analytics          │ │
│  │  ├─ getEmployeeByDesignation()                        │ │
│  │  ├─ getEmployeeByDepartment()                         │ │
│  │  ├─ getAdditionsAttritions($year)                     │ │
│  │  ├─ getEmployeeByStatus()                             │ │
│  │  ├─ getCtcByDepartment()                              │ │
│  │  ├─ getGenderDistribution()                           │ │
│  │  ├─ getYearsOfServiceDistribution()                   │ │
│  │  └─ getAgeDistribution()                              │ │
│  └────────────────────────────────────────────────────────┘ │
│                            │                                 │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  Models                                                │ │
│  │  ├─ EmployeeModel                                     │ │
│  │  ├─ DesignationMasterModel                            │ │
│  │  ├─ DepartmentMasterModel                             │ │
│  │  └─ BaseModel                                         │ │
│  └────────────────────────────────────────────────────────┘ │
│                            │                                 │
└─────────────────────────────────────────────────────────────┘
                             │
┌─────────────────────────────────────────────────────────────┐
│                      MySQL Database                          │
│  ├─ tbl_employee_master                                     │
│  ├─ tbl_designation_master                                  │
│  ├─ tbl_department_master                                   │
│  ├─ tbl_employee_designation_history                        │
│  └─ tbl_employee_salary_info                                │
└─────────────────────────────────────────────────────────────┘
```

### Component Interaction Flow

1. **Initial Load**: Admin user accesses dashboard → DashboardController@index → Renders dashboard.blade.php with Welcome view visible
2. **Toggle Click**: User clicks toggle → JavaScript switches view visibility → If Analytics view empty, AJAX call to fetch data
3. **Data Fetch**: AJAX request → DashboardController@getAnalyticsData → Queries database → Returns JSON
4. **Render Charts**: JavaScript receives data → Chart.js renders visualizations → Display in Analytics view

## Components and Interfaces

### Frontend Components

#### 1. Toggle Button Component

**Location**: `resources/views/admin/dashboard/dashboard.blade.php`

**Structure**:
```html
<div class="view-toggle-wrapper">
    <button class="btn btn-toggle active" data-view="welcome">
        <i class="fa fa-home"></i> Welcome
    </button>
    <button class="btn btn-toggle" data-view="analytics">
        <i class="fa fa-chart-bar"></i> Analytics
    </button>
</div>
```

**Behavior**:
- Only visible when `session('role') == config('constants.ROLE_ADMIN')`
- Active button has `.active` class
- Click triggers view switch via JavaScript

#### 2. Welcome View Container

**Location**: `resources/views/admin/dashboard/dashboard.blade.php`

**Structure**:
```html
<div id="welcomeView" class="dashboard-view active">
    <!-- Existing dashboard content -->
    <!-- Holidays, Quick Links, Events, Announcements, Leave Balance -->
</div>
```

#### 3. Analytics View Container

**Location**: `resources/views/admin/dashboard/dashboard-analytics.blade.php` (new partial)

**Structure**:
```html
<div id="analyticsView" class="dashboard-view" style="display:none;">
    <div class="row">
        <!-- Year Filter for Additions/Attritions -->
        <div class="col-12 mb-3">
            <select id="yearFilter" class="form-control w-auto">
                <!-- Years 2020 to current -->
            </select>
        </div>
    </div>
    
    <div class="row">
        <!-- Chart Cards -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">Employee Count by Designation</div>
                <div class="card-body">
                    <canvas id="designationChart"></canvas>
                    <div id="designationTable" class="mt-3"></div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">Employee Count by Department</div>
                <div class="card-body">
                    <canvas id="departmentChart"></canvas>
                    <div id="departmentTable" class="mt-3"></div>
                </div>
            </div>
        </div>
        
        <!-- Additional chart cards for other reports -->
    </div>
</div>
```

#### 4. Chart Rendering Component

**Location**: `public/js/dashboard-analytics.js` (new file)

**Key Functions**:
```javascript
class DashboardAnalytics {
    constructor() {
        this.charts = {};
        this.data = null;
    }
    
    async loadAnalyticsData(year = null) {
        // Fetch data via AJAX
    }
    
    renderDesignationChart(data) {
        // Render bar chart using Chart.js
    }
    
    renderDepartmentChart(data) {
        // Render pie chart using Chart.js
    }
    
    renderAdditionsAttritionsChart(data) {
        // Render line chart using Chart.js
    }
    
    renderStatusChart(data) {
        // Render doughnut chart using Chart.js
    }
    
    renderCtcChart(data) {
        // Render horizontal bar chart using Chart.js
    }
    
    renderGenderChart(data) {
        // Render pie chart using Chart.js
    }
    
    renderServiceChart(data) {
        // Render bar chart using Chart.js
    }
    
    renderAgeChart(data) {
        // Render bar chart using Chart.js
    }
    
    renderDataTable(containerId, data, columns) {
        // Render HTML table
    }
    
    destroyCharts() {
        // Clean up chart instances
    }
}
```

### Backend Components

#### 1. DashboardController Methods

**Location**: `app/Http/Controllers/DashboardController.php`

**New Methods**:

```php
/**
 * Get all analytics data in one call
 * @param Request $request
 * @return JsonResponse
 */
public function getAnalyticsData(Request $request)
{
    $year = $request->input('year', date('Y'));
    
    $data = [
        'employeeByDesignation' => $this->getEmployeeByDesignation(),
        'employeeByDepartment' => $this->getEmployeeByDepartment(),
        'additionsAttritions' => $this->getAdditionsAttritions($year),
        'employeeByStatus' => $this->getEmployeeByStatus(),
        'ctcByDepartment' => $this->getCtcByDepartment(),
        'genderDistribution' => $this->getGenderDistribution(),
        'serviceDistribution' => $this->getYearsOfServiceDistribution(),
        'ageDistribution' => $this->getAgeDistribution(),
    ];
    
    return response()->json($data);
}

/**
 * Get employee count grouped by designation
 * @return array
 */
private function getEmployeeByDesignation()
{
    $query = "SELECT 
                dm.v_designation_name as designation,
                COUNT(em.i_id) as count
              FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . " em
              LEFT JOIN " . config('constants.DESIGNATION_MASTER_TABLE') . " dm 
                ON em.i_designation_id = dm.i_id
              WHERE em.t_is_deleted = 0 
                AND em.e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
              GROUP BY em.i_designation_id, dm.v_designation_name
              ORDER BY count DESC";
    
    return DB::select($query);
}

/**
 * Get employee count grouped by department
 * @return array
 */
private function getEmployeeByDepartment()
{
    $query = "SELECT 
                COALESCE(dept.v_department_name, 'Unassigned') as department,
                COUNT(em.i_id) as count
              FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . " em
              LEFT JOIN " . config('constants.DEPARTMENT_MASTER_TABLE') . " dept 
                ON em.i_department_id = dept.i_id
              WHERE em.t_is_deleted = 0 
                AND em.e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
              GROUP BY em.i_department_id, dept.v_department_name
              ORDER BY count DESC";
    
    return DB::select($query);
}

/**
 * Get additions and attritions by month for a year
 * @param int $year
 * @return array
 */
private function getAdditionsAttritions($year)
{
    // Additions query
    $additionsQuery = "SELECT 
                        MONTH(dt_joining_date) as month,
                        COUNT(*) as count
                       FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
                       WHERE YEAR(dt_joining_date) = ?
                         AND t_is_deleted = 0
                       GROUP BY MONTH(dt_joining_date)";
    
    $additions = DB::select($additionsQuery, [$year]);
    
    // Attritions query
    $attritionsQuery = "SELECT 
                         MONTH(dt_relieving_date) as month,
                         COUNT(*) as count
                        FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
                        WHERE YEAR(dt_relieving_date) = ?
                          AND e_employment_status = '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
                          AND t_is_deleted = 0
                        GROUP BY MONTH(dt_relieving_date)";
    
    $attritions = DB::select($attritionsQuery, [$year]);
    
    // Format data for all 12 months
    $monthlyData = [];
    for ($i = 1; $i <= 12; $i++) {
        $monthlyData[] = [
            'month' => date('M', mktime(0, 0, 0, $i, 1)),
            'additions' => 0,
            'attritions' => 0
        ];
    }
    
    foreach ($additions as $addition) {
        $monthlyData[$addition->month - 1]['additions'] = $addition->count;
    }
    
    foreach ($attritions as $attrition) {
        $monthlyData[$attrition->month - 1]['attritions'] = $attrition->count;
    }
    
    return $monthlyData;
}

/**
 * Get employee count grouped by employment status
 * @return array
 */
private function getEmployeeByStatus()
{
    $query = "SELECT 
                e_employment_status as status,
                COUNT(*) as count
              FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
              WHERE t_is_deleted = 0
              GROUP BY e_employment_status
              ORDER BY count DESC";
    
    return DB::select($query);
}

/**
 * Get total annual CTC grouped by department
 * @return array
 */
private function getCtcByDepartment()
{
    $query = "SELECT 
                COALESCE(dept.v_department_name, 'Unassigned') as department,
                SUM(COALESCE(esi.d_annual_ctc, 0)) as total_ctc
              FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . " em
              LEFT JOIN " . config('constants.DEPARTMENT_MASTER_TABLE') . " dept 
                ON em.i_department_id = dept.i_id
              LEFT JOIN " . config('constants.EMPLOYEE_SALARY_INFO_TABLE') . " esi 
                ON em.i_id = esi.i_employee_id AND esi.t_is_deleted = 0
              WHERE em.t_is_deleted = 0 
                AND em.e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
              GROUP BY em.i_department_id, dept.v_department_name
              ORDER BY total_ctc DESC";
    
    return DB::select($query);
}

/**
 * Get employee count grouped by gender
 * @return array
 */
private function getGenderDistribution()
{
    $query = "SELECT 
                COALESCE(e_gender, 'Not Specified') as gender,
                COUNT(*) as count
              FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
              WHERE t_is_deleted = 0 
                AND e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
              GROUP BY e_gender
              ORDER BY count DESC";
    
    return DB::select($query);
}

/**
 * Get employee count grouped by years of service ranges
 * @return array
 */
private function getYearsOfServiceDistribution()
{
    $query = "SELECT 
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 1 THEN '0-1 years'
                    WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 3 THEN '1-3 years'
                    WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 5 THEN '3-5 years'
                    WHEN TIMESTAMPDIFF(YEAR, dt_joining_date, CURDATE()) < 10 THEN '5-10 years'
                    ELSE '10+ years'
                END as service_range,
                COUNT(*) as count
              FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
              WHERE t_is_deleted = 0 
                AND e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
              GROUP BY service_range
              ORDER BY FIELD(service_range, '0-1 years', '1-3 years', '3-5 years', '5-10 years', '10+ years')";
    
    return DB::select($query);
}

/**
 * Get employee count grouped by age ranges
 * @return array
 */
private function getAgeDistribution()
{
    $query = "SELECT 
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 26 THEN '18-25'
                    WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 36 THEN '26-35'
                    WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 46 THEN '36-45'
                    WHEN TIMESTAMPDIFF(YEAR, dt_birth_date, CURDATE()) < 56 THEN '46-55'
                    ELSE '56+'
                END as age_range,
                COUNT(*) as count
              FROM " . config('constants.EMPLOYEE_MASTER_TABLE') . "
              WHERE t_is_deleted = 0 
                AND e_employment_status != '" . config('constants.RELIEVED_EMPLOYMENT_STATUS') . "'
                AND dt_birth_date IS NOT NULL
              GROUP BY age_range
              ORDER BY FIELD(age_range, '18-25', '26-35', '36-45', '46-55', '56+')";
    
    return DB::select($query);
}
```

## Data Models

### Employee Model

**Table**: `tbl_employee_master`

**Key Fields**:
- `i_id` - Primary key
- `v_employee_code` - Employee code
- `v_employee_full_name` - Full name
- `i_designation_id` - Foreign key to designation
- `i_department_id` - Foreign key to department
- `e_employment_status` - Employment status (Active, Probation, Relieved, etc.)
- `e_gender` - Gender
- `dt_birth_date` - Birth date
- `dt_joining_date` - Joining date
- `dt_relieving_date` - Relieving date
- `t_is_deleted` - Soft delete flag

### Designation Model

**Table**: `tbl_designation_master`

**Key Fields**:
- `i_id` - Primary key
- `v_designation_name` - Designation name
- `t_is_deleted` - Soft delete flag

### Department Model

**Table**: `tbl_department_master`

**Key Fields**:
- `i_id` - Primary key
- `v_department_name` - Department name
- `t_is_deleted` - Soft delete flag

### Employee Salary Info Model

**Table**: `tbl_employee_salary_info`

**Key Fields**:
- `i_id` - Primary key
- `i_employee_id` - Foreign key to employee
- `d_annual_ctc` - Annual CTC amount
- `t_is_deleted` - Soft delete flag

## Error Handling

### Frontend Error Handling

1. **AJAX Request Failures**:
   - Display user-friendly error message in toast/alert
   - Log error to console for debugging
   - Provide retry button

2. **Chart Rendering Failures**:
   - Display "Unable to load chart" message in card
   - Log error details to console
   - Gracefully degrade to data table only

3. **No Data Scenarios**:
   - Display "No data available" message
   - Show empty state illustration
   - Provide helpful text explaining why data might be missing

### Backend Error Handling

1. **Database Query Failures**:
   - Log error with context (query, parameters)
   - Return empty array with success flag false
   - Send appropriate HTTP status code (500)

2. **Invalid Year Parameter**:
   - Validate year is between 2020 and current year
   - Default to current year if invalid
   - Return validation error message

3. **Permission Errors**:
   - Check user role before processing request
   - Return 403 Forbidden if not admin
   - Redirect to dashboard with error message

## Testing Strategy

### Unit Tests

1. **Controller Method Tests**:
   - Test each analytics method returns correct data structure
   - Test with various employee data scenarios
   - Test with empty database
   - Test year parameter validation

2. **Model Query Tests**:
   - Test employee filtering by status
   - Test date range calculations
   - Test aggregation accuracy

### Integration Tests

1. **AJAX Endpoint Tests**:
   - Test getAnalyticsData endpoint returns valid JSON
   - Test authentication and authorization
   - Test response time under load

2. **View Rendering Tests**:
   - Test toggle button visibility for admin vs employee
   - Test view switching functionality
   - Test chart container rendering

### Manual Testing Checklist

1. **Admin User Flow**:
   - [ ] Toggle button visible on dashboard
   - [ ] Click toggle switches to Analytics view
   - [ ] All charts load and display correctly
   - [ ] Year filter updates additions/attritions chart
   - [ ] Data tables display below charts
   - [ ] Toggle back to Welcome view works
   - [ ] View preference persists on page refresh

2. **Employee User Flow**:
   - [ ] Toggle button NOT visible
   - [ ] Only Welcome view accessible
   - [ ] No analytics data loaded

3. **Data Accuracy**:
   - [ ] Employee counts match database queries
   - [ ] CTC calculations are correct
   - [ ] Date range filters work properly
   - [ ] Age and service calculations are accurate

4. **Performance**:
   - [ ] Analytics view loads within 3 seconds
   - [ ] No memory leaks from chart instances
   - [ ] Smooth transitions between views

5. **Responsive Design**:
   - [ ] Charts display correctly on mobile
   - [ ] Toggle button accessible on small screens
   - [ ] Data tables are scrollable on mobile

## Performance Considerations

### Database Optimization

1. **Indexes**:
   - Add index on `e_employment_status` in employee table
   - Add index on `dt_joining_date` in employee table
   - Add index on `dt_relieving_date` in employee table
   - Add composite index on `(t_is_deleted, e_employment_status)`

2. **Query Optimization**:
   - Use LEFT JOIN instead of subqueries
   - Limit result sets where appropriate
   - Use COALESCE for null handling
   - Avoid N+1 queries

### Caching Strategy

1. **Session Caching**:
   - Cache analytics data for 5 minutes
   - Invalidate cache on employee data changes
   - Store cache key with timestamp

2. **Browser Caching**:
   - Cache Chart.js library
   - Cache dashboard-analytics.js with versioning
   - Use CDN for Chart.js

### Frontend Optimization

1. **Lazy Loading**:
   - Load Chart.js only when Analytics view is accessed
   - Defer chart rendering until view is visible
   - Load data on-demand, not on initial page load

2. **Chart Performance**:
   - Limit data points for large datasets
   - Use animation: false for faster rendering
   - Destroy chart instances when switching views

## Security Considerations

1. **Authorization**:
   - Verify admin role on every analytics endpoint
   - Use middleware for route protection
   - Check session validity

2. **Input Validation**:
   - Validate year parameter (2020 - current year)
   - Sanitize all user inputs
   - Use parameterized queries to prevent SQL injection

3. **Data Exposure**:
   - Only expose aggregated data, not individual records
   - Mask sensitive salary information if needed
   - Implement rate limiting on analytics endpoints

## Deployment Considerations

1. **Database Migration**:
   - No schema changes required
   - Add indexes via migration file
   - Test migration on staging environment

2. **Asset Deployment**:
   - Add Chart.js to package.json or use CDN
   - Compile and minify dashboard-analytics.js
   - Update asset version for cache busting

3. **Configuration**:
   - Add analytics cache duration to config
   - Add Chart.js CDN URL to config
   - Add feature flag for analytics dashboard

4. **Rollback Plan**:
   - Keep toggle button hidden via feature flag
   - Revert routes if needed
   - No database rollback required
