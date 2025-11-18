# Requirements Document

## Introduction

This document outlines the requirements for implementing an HR Analytics Dashboard feature in the HRMS system. The feature will provide admin users with a toggle mechanism to switch between a "Welcome" view (current dashboard) and an "Analytics" view containing comprehensive HR reports and metrics. This functionality will be exclusive to admin users and will not be available to regular employees.

## Glossary

- **HRMS System**: The Human Resource Management System application
- **Admin User**: A user with administrative privileges (role = ROLE_ADMIN)
- **Employee User**: A regular user without administrative privileges
- **Welcome View**: The current dashboard view showing holidays, quick links, announcements, leave balance, and events
- **Analytics View**: A new dashboard view displaying HR metrics and reports with charts and visualizations
- **Toggle Button**: A UI control that switches between Welcome and Analytics views
- **Dashboard Controller**: The Laravel controller handling dashboard routing and data (DashboardController.php)
- **Chart Library**: A JavaScript library for rendering data visualizations (e.g., Chart.js)

## Requirements

### Requirement 1

**User Story:** As an admin user, I want to see a toggle button on the dashboard, so that I can switch between the Welcome view and the Analytics view

#### Acceptance Criteria

1. WHEN THE Admin_User accesses the dashboard, THE HRMS_System SHALL display a toggle button in the breadcrumb area
2. WHEN THE Admin_User clicks the toggle button, THE HRMS_System SHALL switch between Welcome and Analytics views without page reload
3. WHERE THE user is an Employee_User, THE HRMS_System SHALL NOT display the toggle button
4. WHEN THE Admin_User switches views, THE HRMS_System SHALL persist the selected view preference in the session

### Requirement 2

**User Story:** As an admin user, I want to view employee count by designation (grade), so that I can understand the organizational structure distribution

#### Acceptance Criteria

1. WHEN THE Admin_User views the Analytics view, THE HRMS_System SHALL display a chart showing employee count grouped by designation
2. THE HRMS_System SHALL exclude employees with employment status equal to RELIEVED_EMPLOYMENT_STATUS from the count
3. THE HRMS_System SHALL display both a bar chart and a data table with designation names and counts
4. THE HRMS_System SHALL sort designations by employee count in descending order

### Requirement 3

**User Story:** As an admin user, I want to view employee count by department, so that I can analyze departmental workforce distribution

#### Acceptance Criteria

1. WHEN THE Admin_User views the Analytics view, THE HRMS_System SHALL display a chart showing employee count grouped by department
2. THE HRMS_System SHALL exclude employees with employment status equal to RELIEVED_EMPLOYMENT_STATUS from the count
3. THE HRMS_System SHALL display both a pie chart and a data table with department names and counts
4. THE HRMS_System SHALL handle employees without assigned departments by grouping them as "Unassigned"

### Requirement 4

**User Story:** As an admin user, I want to view additions and attritions filtered by year, so that I can track employee turnover trends

#### Acceptance Criteria

1. WHEN THE Admin_User views the Analytics view, THE HRMS_System SHALL display a year filter dropdown with years from 2020 to current year
2. WHEN THE Admin_User selects a year, THE HRMS_System SHALL display the count of employees who joined in that year
3. WHEN THE Admin_User selects a year, THE HRMS_System SHALL display the count of employees who left in that year
4. THE HRMS_System SHALL display a line chart showing monthly additions and attritions for the selected year
5. THE HRMS_System SHALL calculate attritions based on employees with employment status equal to RELIEVED_EMPLOYMENT_STATUS and relieving date within the selected year

### Requirement 5

**User Story:** As an admin user, I want to view employee count by status, so that I can understand the current workforce composition

#### Acceptance Criteria

1. WHEN THE Admin_User views the Analytics view, THE HRMS_System SHALL display a chart showing employee count grouped by employment status
2. THE HRMS_System SHALL include all employment statuses (Active, Probation, Notice Period, Relieved, Suspended)
3. THE HRMS_System SHALL display both a doughnut chart and a data table with status names and counts
4. THE HRMS_System SHALL use distinct colors for each employment status

### Requirement 6

**User Story:** As an admin user, I want to view annual CTC by department, so that I can analyze salary distribution across departments

#### Acceptance Criteria

1. WHEN THE Admin_User views the Analytics view, THE HRMS_System SHALL display a chart showing total annual CTC grouped by department
2. THE HRMS_System SHALL exclude employees with employment status equal to RELIEVED_EMPLOYMENT_STATUS from the calculation
3. THE HRMS_System SHALL display both a horizontal bar chart and a data table with department names and total CTC
4. THE HRMS_System SHALL format CTC values in currency format with appropriate separators
5. THE HRMS_System SHALL calculate annual CTC from the employee salary information

### Requirement 7

**User Story:** As an admin user, I want to view gender distribution, so that I can monitor diversity metrics

#### Acceptance Criteria

1. WHEN THE Admin_User views the Analytics view, THE HRMS_System SHALL display a chart showing employee count grouped by gender
2. THE HRMS_System SHALL exclude employees with employment status equal to RELIEVED_EMPLOYMENT_STATUS from the count
3. THE HRMS_System SHALL display both a pie chart and a data table with gender categories and counts
4. THE HRMS_System SHALL handle employees without assigned gender by grouping them as "Not Specified"

### Requirement 8

**User Story:** As an admin user, I want to view years of service distribution, so that I can understand employee tenure patterns

#### Acceptance Criteria

1. WHEN THE Admin_User views the Analytics view, THE HRMS_System SHALL display a chart showing employee count grouped by years of service ranges
2. THE HRMS_System SHALL exclude employees with employment status equal to RELIEVED_EMPLOYMENT_STATUS from the count
3. THE HRMS_System SHALL group employees into ranges: 0-1 years, 1-3 years, 3-5 years, 5-10 years, 10+ years
4. THE HRMS_System SHALL calculate years of service from the joining date to current date
5. THE HRMS_System SHALL display both a bar chart and a data table with service ranges and counts

### Requirement 9

**User Story:** As an admin user, I want to view age distribution, so that I can analyze workforce demographics

#### Acceptance Criteria

1. WHEN THE Admin_User views the Analytics view, THE HRMS_System SHALL display a chart showing employee count grouped by age ranges
2. THE HRMS_System SHALL exclude employees with employment status equal to RELIEVED_EMPLOYMENT_STATUS from the count
3. THE HRMS_System SHALL group employees into ranges: 18-25, 26-35, 36-45, 46-55, 56+ years
4. THE HRMS_System SHALL calculate age from the birth date to current date
5. THE HRMS_System SHALL display both a bar chart and a data table with age ranges and counts

### Requirement 10

**User Story:** As an admin user, I want the Analytics view to load efficiently, so that I can access reports without performance delays

#### Acceptance Criteria

1. WHEN THE Admin_User switches to the Analytics view, THE HRMS_System SHALL load all chart data within 3 seconds
2. THE HRMS_System SHALL use AJAX requests to fetch chart data asynchronously
3. THE HRMS_System SHALL display loading indicators while fetching chart data
4. THE HRMS_System SHALL cache chart data in the session for 5 minutes to improve performance
5. THE HRMS_System SHALL optimize database queries using proper indexing and joins
