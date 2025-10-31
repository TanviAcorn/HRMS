<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Route::post('/announcements/store', [AnnouncementController::class, 'store'])->name('announcements.store');
// // Route::delete('/announcement/{id}', [AnnouncementController::class, 'destroy'])->name('announcement.destroy');
// Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/announcement', 'App\\Http\\Controllers\\AnnouncementController@index')->name('announcement.index');
Route::post('/announcement', 'App\\Http\\Controllers\\AnnouncementController@store')->name('announcement.store');
Route::delete('/announcement/{id}', 'App\\Http\\Controllers\\AnnouncementController@destroy')->name('announcement.destroy');
// Fallback POST delete endpoint for environments where method spoofing may fail
Route::post('/announcement/{id}/delete', 'App\\Http\\Controllers\\AnnouncementController@destroy')->name('announcement.destroy.post');

// Announcement reactions
Route::get('/announcement/{id}/reactions', 'App\\Http\\Controllers\\AnnouncementController@reactionsIndex')->name('announcement.reactions.index');
Route::post('/announcement/{id}/reactions/toggle', 'App\\Http\\Controllers\\AnnouncementController@reactionsToggle')->name('announcement.reactions.toggle');
Route::get('/announcement/{id}/reactions/{emoji}/users', 'App\\Http\\Controllers\\AnnouncementController@reactionsUsers')->name('announcement.reactions.users');

Route::get('/sample-pdf/', 'App\Http\Controllers\HomeController@samplePDF');
Route::get('/sample-form/', 'App\Http\Controllers\HomeController@sampleForm');
Route::get('/check-dbconnection/', 'App\Http\Controllers\HomeController@checkDbConnection');
Route::get('/design-dashboard', 'App\Http\Controllers\HomeController@dashboard'); 
Route::get('/login', 'App\Http\Controllers\HomeController@login')->name('login');
 
Route::get('/changepassword', 'App\Http\Controllers\HomeController@changepassword');
Route::get('/category-list', 'App\Http\Controllers\HomeController@category_list');
Route::get('/add-category', 'App\Http\Controllers\HomeController@add_category');
Route::get('/login-history', 'App\Http\Controllers\HomeController@login_history');
Route::get('/update-category', 'App\Http\Controllers\HomeController@update_category');
Route::get('/forgot-password', 'App\Http\Controllers\HomeController@forgot_password');
Route::get('/reset-password', 'App\Http\Controllers\HomeController@reset_password');
Route::get('/design-salary-components', 'App\Http\Controllers\HomeController@salary_components');
Route::get('/design-salary-groups', 'App\Http\Controllers\HomeController@salary_groups');
Route::get('/design-verify-otp', 'App\Http\Controllers\HomeController@verify_otp');



Route::get('/design-document-type', 'App\Http\Controllers\HomeController@document_type');


Route::get('users', 'App\Http\Controllers\UsersController@index');
Route::get('users/create', 'App\Http\Controllers\UsersController@create');
Route::post('users/filter','App\Http\Controllers\UsersController@filter');
Route::post('users/update','App\Http\Controllers\UsersController@update');
Route::post('users/add','App\Http\Controllers\UsersController@add');
Route::post('users/updateStatus','App\Http\Controllers\UsersController@updateStatus');
Route::get('users/edit/{id}','App\Http\Controllers\UsersController@edit')->name('user.edit');
Route::post('users/delete/{id}','App\Http\Controllers\UsersController@delete')->name('user.delete');
Route::post('checkUniqueUserEmail','App\Http\Controllers\GuestController@checkUniqueUserEmail');

Route::get('/', 'App\Http\Controllers\LoginController@showLoginForm');
Route::get('/login', 'App\Http\Controllers\LoginController@showLoginForm');
Route::post('login/checkLogin', 'App\Http\Controllers\LoginController@checkLogin');
Route::get('logout', 'App\Http\Controllers\DashboardController@logout');
Route::get('dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard')->middleware('checklogin');
Route::get('change-password', 'App\Http\Controllers\DashboardController@changePassword')->name('change-password')->middleware('checklogin');
Route::post('dashboard/updatePassword','App\Http\Controllers\DashboardController@updatePassword');
Route::post('removeRecord','App\Http\Controllers\MasterController@removeRecord');

Route::get('login-history', 'App\Http\Controllers\LoginHistoryController@index');
Route::post('login-history/filter','App\Http\Controllers\LoginHistoryController@filter');

Route::get('location-master', 'App\Http\Controllers\LookupMasterController@index')->middleware('checkpermission:view_incident_summary');
Route::get('type-master', 'App\Http\Controllers\LookupMasterController@index');
Route::post('lookup-master/filter','App\Http\Controllers\LookupMasterController@filter');
Route::post('lookup-master/delete/{id}','App\Http\Controllers\LookupMasterController@delete');
Route::post('lookup-master/updateStatus','App\Http\Controllers\LookupMasterController@updateStatus');

Route::post('add-lookup-master','App\Http\Controllers\LookupMasterController@addLookupMaster');
Route::post('lookup-master/getLookupRecordInfo','App\Http\Controllers\LookupMasterController@getLookupRecordInfo');
Route::post('lookup-master/delete/{id}','App\Http\Controllers\LookupMasterController@delete');


Route::get('/design-document-folder', 'App\Http\Controllers\HomeController@document_folder');
Route::get('/design-holiday-master', 'App\Http\Controllers\HomeController@holiday_master');
Route::get('/design-bank-master', 'App\Http\Controllers\HomeController@bank_master');
Route::get('/design-incident-report', 'App\Http\Controllers\HomeController@incident_report');
Route::get('/design-add-incident-report', 'App\Http\Controllers\HomeController@add_incident_report');
Route::get('/design-city', 'App\Http\Controllers\HomeController@city');
Route::get('/design-probation-policy-master', 'App\Http\Controllers\HomeController@probation_policy_master');
Route::get('/design-employee', 'App\Http\Controllers\HomeController@employee');
Route::get('/design-employee-report', 'App\Http\Controllers\HomeController@employee_report');
Route::get('/design-salary-report', 'App\Http\Controllers\HomeController@salary_report');
Route::get('/salary-report', 'App\Http\Controllers\HomeController@salary_report')->middleware('checkpermission:view_salary_report');
Route::get('/design-state', 'App\Http\Controllers\HomeController@state');
Route::get('/design-attendance-report', 'App\Http\Controllers\HomeController@attendance_report');
Route::get('/attendance-report', 'App\Http\Controllers\HomeController@attendance_report')->middleware('checkpermission:view_attendance_report');
Route::get('/design-attendance-report-day-wise', 'App\Http\Controllers\HomeController@attendance_report_day_wise');
Route::get('/attendance-report-day-wise', 'App\Http\Controllers\HomeController@attendance_report_day_wise');
Route::get('/design-shift-master', 'App\Http\Controllers\HomeController@shift_master');
Route::get('/design-add-shift', 'App\Http\Controllers\HomeController@add_shift');
Route::get('/design-weekly-off-master', 'App\Http\Controllers\HomeController@weekly_off_master');
Route::get('/design-on-hold-salary-report', 'App\Http\Controllers\HomeController@on_hold_salary_report');
Route::get('/on-hold-salary-report', 'App\Http\Controllers\HomeController@on_hold_salary_report');
Route::get('/design-document-report', 'App\Http\Controllers\HomeController@document_report');
Route::get('/design-leave-report', 'App\Http\Controllers\HomeController@leave_report');
Route::get('/design-time-off-report', 'App\Http\Controllers\HomeController@time_off_report');
Route::get('/design-punch-report', 'App\Http\Controllers\HomeController@punch_report');
Route::get('/punch-report', 'App\Http\Controllers\HomeController@punch_report');
Route::get('/design-notifications', 'App\Http\Controllers\HomeController@notifications');


Route::get('/slip', 'App\Http\Controllers\HomeController@slip');



















Route::get('/design-add-employee', 'App\Http\Controllers\HomeController@add_employee');

//Route::get('/design-profile', 'App\Http\Controllers\HomeController@punch_report');

Route::get('/design-profile', 'App\Http\Controllers\HomeController@profile');
Route::get('/design-my-leaves', 'App\Http\Controllers\HomeController@my_leaves');
Route::get('/design-my-time-off', 'App\Http\Controllers\HomeController@my_time_off');
Route::get('/design-attendance-summary', 'App\Http\Controllers\HomeController@attendance_summary');
Route::get('/design-leave-summary', 'App\Http\Controllers\HomeController@leave_summary');
Route::get('/design-timeoff-summary', 'App\Http\Controllers\HomeController@timeoff_summary');
Route::get('/design-employees-summary', 'App\Http\Controllers\HomeController@employees_summary');
Route::get('/design-salary-summary', 'App\Http\Controllers\HomeController@salary_summary');
Route::get('/design-incident-summary', 'App\Http\Controllers\HomeController@incident_summary');
Route::get('/design-my-salary', 'App\Http\Controllers\HomeController@my_salary');
Route::get('/design-my-documents', 'App\Http\Controllers\HomeController@my_documents');
Route::get('/design-my-payslip', 'App\Http\Controllers\HomeController@my_payslip');
Route::get('/design-my-attendance', 'App\Http\Controllers\HomeController@my_attendance');
Route::get('/design-salary-increment-report', 'App\Http\Controllers\HomeController@salary_increment_report');
Route::get('/salary-increment-report', 'App\Http\Controllers\HomeController@salary_increment_report');
Route::get('/design-add-roles-permissions', 'App\Http\Controllers\HomeController@add_roles_permissions');
Route::get('/design-roles-permissions', 'App\Http\Controllers\HomeController@roles_permissions');
Route::get('/design-salary-report-for-account-team', 'App\Http\Controllers\HomeController@salary_report_for_account_team');
Route::get('/design-employee-duration-report', 'App\Http\Controllers\HomeController@employee_duration_report');
Route::get('/design-leave-report-month-wise-count', 'App\Http\Controllers\HomeController@leave_report_month_wise_count');
Route::get('/design-email-demo', 'App\Http\Controllers\HomeController@email_demo');
Route::get('/design-email-table', 'App\Http\Controllers\HomeController@email_table');
Route::get('/design-lateness-email', 'App\Http\Controllers\HomeController@lateness_email');
Route::get('/design-missing-punch-mail', 'App\Http\Controllers\HomeController@missing_punch_mail');
Route::get('/design-birthdays-email', 'App\Http\Controllers\HomeController@birthdays_email');
Route::get('/design-work-anniversary-email', 'App\Http\Controllers\HomeController@work_anniversary_email');
Route::get('/design-salary-calculation', 'App\Http\Controllers\HomeController@salary_calculation');

Route::get('bank-master', 'App\Http\Controllers\LookupMasterController@index')->middleware('checkpermission:view_bank_master');
Route::get('team-master', 'App\Http\Controllers\LookupMasterController@index')->middleware('checkpermission:view_team_master');
Route::get('designation-master', 'App\Http\Controllers\LookupMasterController@index')->middleware('checkpermission:view_designation_master');
Route::get('sub-designation-master', 'App\Http\Controllers\SubDesignationMasterController@index')->middleware('checkpermission:view_designation_master');
Route::post('sub-designation-master/add', 'App\Http\Controllers\SubDesignationMasterController@add')->middleware('checkpermission:add_designation_master');
Route::post('sub-designation-master/edit','App\Http\Controllers\SubDesignationMasterController@edit')->middleware('checkpermission:edit_designation_master');
Route::post('sub-designation-master/filter','App\Http\Controllers\SubDesignationMasterController@filter')->middleware('checkpermission:view_designation_master');
Route::post('sub-designation-master/updateStatus','App\Http\Controllers\SubDesignationMasterController@updateStatus')->middleware('checkpermission:edit_designation_master');
Route::post('sub-designation-master/delete','App\Http\Controllers\SubDesignationMasterController@delete')->middleware('checkpermission:delete_designation_master');
Route::post('sub-designation-master/checkUniqueSubDesignationName','App\Http\Controllers\SubDesignationMasterController@checkUniqueSubDesignationName')->middleware('checkpermission:add_designation_master');
Route::get('recruitment-source-master', 'App\Http\Controllers\LookupMasterController@index')->middleware('checkpermission:view_recruitment_source_master');
Route::get('termination-reasons-master', 'App\Http\Controllers\LookupMasterController@index')->middleware('checkpermission:view_termination_reasons');
Route::get('resign-reasons-master', 'App\Http\Controllers\LookupMasterController@index')->middleware('checkpermission:view_resign_reasons');

Route::post('getLookupRecordInfo','App\Http\Controllers\LookupMasterController@getLookupRecordInfo');
Route::post('filter','App\Http\Controllers\LookupMasterController@filter');

Route::get('document-folder-master','App\Http\Controllers\DocumentFolderMasterController@index')->middleware('checkpermission:view_document_folder');
Route::post('document-folder-master/add', 'App\Http\Controllers\DocumentFolderMasterController@add')->middleware('checkpermission:add_document_folder');
Route::post('document-folder-master/edit','App\Http\Controllers\DocumentFolderMasterController@edit')->middleware('checkpermission:edit_document_folder');
Route::post('document-folder-master/filter','App\Http\Controllers\DocumentFolderMasterController@filter')->middleware('checkpermission:view_document_folder');
Route::post('document-folder-master/updateStatus','App\Http\Controllers\DocumentFolderMasterController@updateStatus')->middleware('checkpermission:edit_document_folder');
Route::post('document-folder-master/delete/{id}','App\Http\Controllers\DocumentFolderMasterController@delete')->middleware('checkpermission:delete_document_folder');

Route::get('document-type-master','App\Http\Controllers\DocumentTypeController@index')->middleware('checkpermission:view_document_type');
Route::post('document-type-master/add', 'App\Http\Controllers\DocumentTypeController@add')->middleware('checkpermission:add_document_type');
Route::post('document-type-master/edit','App\Http\Controllers\DocumentTypeController@edit')->middleware('checkpermission:edit_document_type');
Route::post('document-type-master/filter','App\Http\Controllers\DocumentTypeController@filter')->middleware('checkpermission:view_document_type');
Route::post('document-type-master/updateStatus','App\Http\Controllers\DocumentTypeController@updateStatus')->middleware('checkpermission:edit_document_type');
Route::post('document-type-master/delete/{id}','App\Http\Controllers\DocumentTypeController@delete')->middleware('checkpermission:delete_document_type');

Route::get('holiday-master','App\Http\Controllers\HolidayMasterController@index')->middleware('checkpermission:view_holiday_master');
Route::post('holiday-master/add', 'App\Http\Controllers\HolidayMasterController@add')->middleware('checkpermission:add_holiday_master');
Route::post('holiday-master/edit','App\Http\Controllers\HolidayMasterController@edit')->middleware('checkpermission:edit_holiday_master');
Route::post('holiday-master/filter','App\Http\Controllers\HolidayMasterController@filter')->middleware('checkpermission:view_holiday_master');
Route::post('holiday-master/updateStatus','App\Http\Controllers\HolidayMasterController@updateStatus')->middleware('checkpermission:edit_holiday_master');
Route::post('holiday-master/delete/{id}','App\Http\Controllers\HolidayMasterController@delete')->middleware('checkpermission:delete_holiday_master');

Route::get('probation-policy-master','App\Http\Controllers\ProbationPolicyMasterController@index')->middleware('checkpermission:view_probation_policy_master');
Route::post('probation-policy-master/add', 'App\Http\Controllers\ProbationPolicyMasterController@add')->middleware('checkpermission:add_probation_policy_master');
Route::post('probation-policy-master/edit','App\Http\Controllers\ProbationPolicyMasterController@edit')->middleware('checkpermission:edit_probation_policy_master');
Route::post('probation-policy-master/filter','App\Http\Controllers\ProbationPolicyMasterController@filter')->middleware('checkpermission:view_probation_policy_master');
Route::post('probation-policy-master/updateStatus','App\Http\Controllers\ProbationPolicyMasterController@updateStatus')->middleware('checkpermission:edit_probation_policy_master');
Route::post('probation-policy-master/delete/{id}','App\Http\Controllers\ProbationPolicyMasterController@delete')->middleware('checkpermission:delete_probation_policy_master');

Route::get('notice-period-policy-master','App\Http\Controllers\ProbationPolicyMasterController@index')->middleware('checkpermission:view_notice_period_policy_master');
Route::post('notice-period-policy-master/add', 'App\Http\Controllers\ProbationPolicyMasterController@add')->middleware('checkpermission:add_notice_period_policy_master');
Route::post('notice-period-policy-master/edit','App\Http\Controllers\ProbationPolicyMasterController@edit')->middleware('checkpermission:edit_notice_period_policy_master');
Route::post('notice-period-policy-master/filter','App\Http\Controllers\ProbationPolicyMasterController@filter')->middleware('checkpermission:view_notice_period_policy_master');
Route::post('notice-period-policy-master/updateStatus','App\Http\Controllers\ProbationPolicyMasterController@updateStatus')->middleware('checkpermission:edit_notice_period_policy_master');
Route::post('notice-period-policy-master/delete/{id}','App\Http\Controllers\ProbationPolicyMasterController@delete')->middleware('checkpermission:delete_notice_period_policy_master');

Route::get('state-master','App\Http\Controllers\StateMasterController@index')->middleware('checkpermission:view_state');
Route::post('state-master/add', 'App\Http\Controllers\StateMasterController@add')->middleware('checkpermission:add_state');
Route::post('state-master/edit','App\Http\Controllers\StateMasterController@edit')->middleware('checkpermission:edit_state');
Route::post('state-master/filter','App\Http\Controllers\StateMasterController@filter')->middleware('checkpermission:view_state');
Route::post('state-master/updateStatus','App\Http\Controllers\StateMasterController@updateStatus')->middleware('checkpermission:edit_state');
Route::post('state-master/delete/{id}','App\Http\Controllers\StateMasterController@delete')->middleware('checkpermission:delete_state');
Route::post('state-master/checkUniqueStateName','App\Http\Controllers\StateMasterController@checkUniqueStateName');

Route::get('city-master','App\Http\Controllers\CityMasterController@index')->middleware('checkpermission:view_city');
Route::post('city-master/add', 'App\Http\Controllers\CityMasterController@add')->middleware('checkpermission:add_city');
Route::post('city-master/edit','App\Http\Controllers\CityMasterController@edit')->middleware('checkpermission:edit_city');
Route::post('city-master/filter','App\Http\Controllers\CityMasterController@filter')->middleware('checkpermission:view_city');
Route::post('city-master/updateStatus','App\Http\Controllers\CityMasterController@updateStatus')->middleware('checkpermission:edit_city');
Route::post('city-master/delete/{id}','App\Http\Controllers\CityMasterController@delete')->middleware('checkpermission:delete_city');
Route::post('city-master/checkUniqueCityName','App\Http\Controllers\CityMasterController@checkUniqueCityName');

Route::get('salary-components','App\Http\Controllers\SalaryComponentsController@index')->middleware('checkpermission:view_salary_components');
Route::post('salary-components/add', 'App\Http\Controllers\SalaryComponentsController@add')->middleware('checkpermission:add_salary_components');
Route::post('salary-components/edit','App\Http\Controllers\SalaryComponentsController@edit')->middleware('checkpermission:edit_salary_components');
Route::post('salary-components/filter','App\Http\Controllers\SalaryComponentsController@filter')->middleware('checkpermission:view_salary_components');
Route::post('salary-components/updateStatus','App\Http\Controllers\SalaryComponentsController@updateStatus')->middleware('checkpermission:edit_salary_components');
Route::post('salary-components/delete/{id}','App\Http\Controllers\SalaryComponentsController@delete')->middleware('checkpermission:delete_salary_components');

Route::get('salary-groups','App\Http\Controllers\SalaryGroupController@index')->middleware('checkpermission:view_salary_groups');
Route::post('salary-groups/add', 'App\Http\Controllers\SalaryGroupController@add')->middleware('checkpermission:add_salary_groups');
Route::post('salary-groups/edit','App\Http\Controllers\SalaryGroupController@edit')->middleware('checkpermission:edit_salary_groups');
Route::post('salary-groups/filter','App\Http\Controllers\SalaryGroupController@filter')->middleware('checkpermission:view_salary_groups');
Route::post('salary-groups/updateStatus','App\Http\Controllers\SalaryGroupController@updateStatus')->middleware('checkpermission:edit_salary_groups');
Route::post('salary-groups/delete/{id}','App\Http\Controllers\SalaryGroupController@delete')->middleware('checkpermission:delete_salary_groups');

Route::post('holiday-master/checkUniqueHolidayDate','App\Http\Controllers\HolidayMasterController@checkUniqueHolidayDate');
Route::post('salary-groups/checkUniqueSalaryGroupName','App\Http\Controllers\SalaryGroupController@checkUniqueSalaryGroupName');
Route::post('salary-components/checkUniqueSalaryComponentsName','App\Http\Controllers\SalaryComponentsController@checkUniqueSalaryComponentsName');
Route::get('employee-master','App\Http\Controllers\EmployeeMaster@index');
Route::get('employee-master/create', 'App\Http\Controllers\EmployeeMaster@create')->middleware('checkpermission:add_employee_list');
Route::post('employee-master/add', 'App\Http\Controllers\EmployeeMaster@add')->middleware('checkpermission:add_employee_list');
Route::post('employee-master/filter','App\Http\Controllers\EmployeeMaster@filter');
Route::post('employee-master/getEmployeeCodeDetails', 'App\Http\Controllers\EmployeeMaster@getEmployeeCodeDetails')->middleware('checkpermission:view_employee_list');
Route::post('employee-master/checkUniqueEmployeeCode','App\Http\Controllers\EmployeeMaster@checkUniqueEmployeeCode')->middleware('checkpermission:add_employee_list');
Route::get('employee-master/edit/{id}','App\Http\Controllers\EmployeeMaster@edit')->name('employee-master.edit')->middleware('checkpermission:edit_employee_list');
Route::post('employee-master/delete/{id}','App\Http\Controllers\EmployeeMaster@delete')->name('employee-master.delete')->middleware('checkpermission:delete_employee_list');
Route::post('employee-master/getSubDesignationsByDesignation', 'App\Http\Controllers\EmployeeMaster@getSubDesignationsByDesignation');

Route::get('employee-master/profile/{id}','App\Http\Controllers\EmployeeMaster@profile')->name('employee-master.profile');

Route::post('employee-master/editRealtion','App\Http\Controllers\EmployeeMaster@editRealtion');
Route::post('employee-master/addRelation','App\Http\Controllers\EmployeeMaster@addRelation');
Route::post('employee-master/editPrimaryDetail','App\Http\Controllers\EmployeeMaster@editPrimaryDetail');
Route::post('employee-master/addPrimaryDetails','App\Http\Controllers\EmployeeMaster@addPrimaryDetails');
Route::post('employee-master/addUploadDocumentDetails','App\Http\Controllers\EmployeeMaster@addUploadDocumentDetails');
Route::post('employee-master/viewDocumentDetails','App\Http\Controllers\EmployeeMaster@viewDocumentDetails');
Route::post('employee-master/editContactDetails','App\Http\Controllers\EmployeeMaster@editContactDetails');
Route::post('employee-master/addContactDetails','App\Http\Controllers\EmployeeMaster@addContactDetails');
Route::post('employee-master/editAddressModel','App\Http\Controllers\EmployeeMaster@editAddressModel');
Route::post('employee-master/editBankDetails','App\Http\Controllers\EmployeeMaster@editBankDetails');
Route::post('employee-master/addBankDetails','App\Http\Controllers\EmployeeMaster@addBankDetails');
Route::post('employee-master/editIdentityPfAccountDetails','App\Http\Controllers\EmployeeMaster@editIdentityPfAccountDetails');
Route::post('employee-master/addIdentityPfAccountDetails','App\Http\Controllers\EmployeeMaster@addIdentityPfAccountDetails');
Route::post('employee-master/editJobDetails','App\Http\Controllers\EmployeeMaster@editJobDetails');
Route::post('employee-master/addJobDetails','App\Http\Controllers\EmployeeMaster@addJobDetails');
Route::post('employee-master/addAddressDetails','App\Http\Controllers\EmployeeMaster@addAddressDetails');
//Route::post('employee-master/get-emp-doc-list','App\Http\Controllers\EmployeeMaster@getEmployeeDocumentList');
Route::post('employee-master/documentDelete','App\Http\Controllers\EmployeeMaster@documentDelete');

Route::post('document-folder-master/checkUniqueDocumentFolderName','App\Http\Controllers\DocumentFolderMasterController@checkUniqueDocumentFolderName');

// Employee Feedback Form Routes
Route::get('employee-feedback/{employeeId}', 'App\Http\Controllers\EmployeeFeedbackController@showForm')->name('employee-feedback.show')->middleware('checklogin');
Route::post('employee-feedback/{employeeId}', 'App\Http\Controllers\EmployeeFeedbackController@store')->name('employee-feedback.store')->middleware('checklogin');

Route::get('shift-master','App\Http\Controllers\ShiftMasterController@index')->middleware('checkpermission:view_shifts');
Route::get('shift-master/create','App\Http\Controllers\ShiftMasterController@create')->middleware('checkpermission:add_shifts');
Route::post('shift-master/add', 'App\Http\Controllers\ShiftMasterController@add')->middleware('checkpermission:add_shifts,edit_shifts');
Route::get('shift-master/edit/{id}','App\Http\Controllers\ShiftMasterController@edit')->name('shift-master.edit')->middleware('checkpermission:edit_shifts');
Route::post('shift-master/delete/{id}','App\Http\Controllers\ShiftMasterController@delete')->name('shift-master.delete')->middleware('checkpermission:delete_shifts');
Route::post('shift-master/filter','App\Http\Controllers\ShiftMasterController@filter')->middleware('checkpermission:view_shifts');
Route::post('shift-master/updateStatus','App\Http\Controllers\ShiftMasterController@updateStatus')->middleware('checkpermission:edit_shifts');
Route::post('shift-master/checkUniqueShiftName','App\Http\Controllers\ShiftMasterController@checkUniqueShiftName');

Route::get('weekly-off-master','App\Http\Controllers\WeeklyOffMasterController@index')->middleware('checkpermission:view_weekly_offs');
Route::post('weekly-off-master/add', 'App\Http\Controllers\WeeklyOffMasterController@add')->middleware('checkpermission:add_weekly_offs,edit_weekly_offs');
Route::post('weekly-off-master/edit','App\Http\Controllers\WeeklyOffMasterController@edit')->middleware('checkpermission:add_weekly_offs,edit_weekly_offs');
Route::post('weekly-off-master/filter','App\Http\Controllers\WeeklyOffMasterController@filter')->middleware('checkpermission:view_weekly_offs');
Route::post('weekly-off-master/updateStatus','App\Http\Controllers\WeeklyOffMasterController@updateStatus')->middleware('checkpermission:edit_weekly_offs');
Route::post('weekly-off-master/delete/{id}','App\Http\Controllers\WeeklyOffMasterController@delete')->middleware('checkpermission:delete_weekly_offs');
Route::post('weekly-off-master/checkUniqueWeeklyOffName','App\Http\Controllers\WeeklyOffMasterController@checkUniqueWeeklyOffName');

Route::get('page-not-found', 'App\Http\Controllers\GuestController@customErrorPage');
Route::get('my-leaves' ,'App\Http\Controllers\MyLeaveMasterController@index');
Route::post('my-leaves/editApplyLeave','App\Http\Controllers\MyLeaveMasterController@editApplyLeave');
Route::post('my-leaves/addApplyLeave','App\Http\Controllers\MyLeaveMasterController@addApplyLeave');
Route::post('my-leaves/checkApplyValidLeaveInfo','App\Http\Controllers\MyLeaveMasterController@checkApplyValidLeaveInfo');
Route::post('my-leaves/getLeaveCalendar','App\Http\Controllers\MyLeaveMasterController@getLeaveCalendar');

Route::post('leave/addLeaveBalance','App\Http\Controllers\MyLeaveMasterController@addLeaveBalance');

Route::get('insert-attendance','App\Http\Controllers\CronController@addAttendance');
Route::get('my-attendance','App\Http\Controllers\MyAttendanceController@index');
Route::post('my-attendance/getAttendanceRecord', 'App\Http\Controllers\MyAttendanceController@getAttendanceRecord');

Route::post('my-leaves/filterLeaveDashboard','App\Http\Controllers\MyLeaveMasterController@filterLeaveDashboard');


Route::get('my-time-off' ,'App\Http\Controllers\TimeOffController@index');
Route::post('my-time-off/applyTimeOff','App\Http\Controllers\TimeOffController@applyTimeOff');
Route::post('my-time-off/filterTimeOffDashboard','App\Http\Controllers\TimeOffController@filterTimeOffDashboard');
Route::post('my-time-off/updateTimeOffStatus','App\Http\Controllers\TimeOffController@updateTimeOffStatus');
Route::post('my-time-off/checkDuplicateTimeOffAdjustmentRequest','App\Http\Controllers\TimeOffController@checkDuplicateTimeOffAdjustmentRequest');
Route::post('my-time-off/checkDuplicateTimeOffRequest','App\Http\Controllers\TimeOffController@checkDuplicateTimeOffRequest');

Route::post('employee-master/getGroupComponent', 'App\Http\Controllers\EmployeeMaster@getGroupComponent');
Route::post('employee-master/getSalaryGroup', 'App\Http\Controllers\EmployeeMaster@getSalaryGroup');


Route::get('calculate-salary', 'App\Http\Controllers\SalaryController@index')->middleware('checkpermission:view_salary_calculation');
Route::get('leave-report' ,'App\Http\Controllers\LeaveReportController@index')->middleware('checkpermission:view_leave_report');
Route::get('leave-report/{employee_id}' ,'App\Http\Controllers\LeaveReportController@index')->middleware('checkpermission:view_leave_report');
Route::get('leave-report-summary' ,'App\Http\Controllers\LeaveReportController@index')->middleware('checkpermission:view_leave_report');
Route::post('leave-report/filter','App\Http\Controllers\LeaveReportController@filter')->middleware('checkpermission:view_leave_report');
Route::post('leave-report/approveLeave','App\Http\Controllers\LeaveReportController@approveLeave');

Route::get('time-off-report' ,'App\Http\Controllers\TimeOffReportController@index')->middleware('checkpermission:view_time_off_report');
Route::get('time-off-report/{employee_id}' ,'App\Http\Controllers\TimeOffReportController@index')->middleware('checkpermission:view_time_off_report');
Route::get('time-off-report-summary' ,'App\Http\Controllers\TimeOffReportController@index')->middleware('checkpermission:view_time_off_report');
Route::post('time-off-report/filter','App\Http\Controllers\TimeOffReportController@filter')->middleware('checkpermission:view_time_off_report');
Route::post('time-off-report/timeOffApprove','App\Http\Controllers\TimeOffReportController@timeOffApprove')->middleware('checkpermission:view_time_off_report');

Route::post('salary/generate-salary', 'App\Http\Controllers\SalaryController@generateSalary');

Route::post('my-leaves/updateLeaveStatus','App\Http\Controllers\MyLeaveMasterController@updateLeaveStatus');
Route::get('employees-summary' ,'App\Http\Controllers\EmployeeSummaryController@index')->middleware('checkpermission:view_employee_summary');
//Route::post('employees-summary/filterEmployeesSummaryDashboard','App\Http\Controllers\EmployeeSummaryController@filterEmployeesSummaryDashboard')->middleware('checkpermission:view_employee_summary');
Route::post('salary/employees-salary-info' ,'App\Http\Controllers\SalaryController@employeeSalaryInfo');
Route::post('salary/filter-salary-generate' ,'App\Http\Controllers\SalaryController@filter');
Route::post('employee-master/getEmployeeDesignationInfo','App\Http\Controllers\EmployeeMaster@getEmployeeDesignationInfo');
Route::post('employee-master/getEmployeeTeamInfo','App\Http\Controllers\EmployeeMaster@getEmployeeTeamInfo');
Route::post('employee-master/getEmployeeShiftInfo','App\Http\Controllers\EmployeeMaster@getEmployeeShiftInfo');
Route::post('employee-master/getEmployeeWeekOffInfo','App\Http\Controllers\EmployeeMaster@getEmployeeWeekOffInfo');
Route::post('employee-master/updateEmployeeDataInfo','App\Http\Controllers\EmployeeMaster@updateEmployeeDataInfo');

Route::post('employee-master/getEmployeeDesignationHistory','App\Http\Controllers\EmployeeMaster@getEmployeeDesignationHistory');
Route::post('employee-master/getDesignationHistoryInfo','App\Http\Controllers\EmployeeMaster@getDesignationHistoryInfo');
Route::post('employee-master/getTeamHistoryInfo','App\Http\Controllers\EmployeeMaster@getTeamHistoryInfo');
Route::post('employee-master/getShiftHistoryInfo','App\Http\Controllers\EmployeeMaster@getShiftHistoryInfo');
Route::post('employee-master/getWeekOffHistoryInfo','App\Http\Controllers\EmployeeMaster@getWeekOffHistoryInfo');


Route::post('employee-master/getEmployeeProbationInfo','App\Http\Controllers\EmployeeMaster@getEmployeeProbationInfo');
Route::post('employee-master/updateProbation','App\Http\Controllers\EmployeeMaster@updateProbation');
Route::post('employee-master/updateLoginStatus','App\Http\Controllers\EmployeeMaster@updateLoginStatus');

Route::post('employee-master/getInitiateExitInfo','App\Http\Controllers\EmployeeMaster@getInitiateExitInfo');
Route::post('employee-master/addInitiateExitForm','App\Http\Controllers\EmployeeMaster@addInitiateExitForm');

Route::post('employee-master/getResignInfo','App\Http\Controllers\EmployeeMaster@getResignInfo');
Route::post('employee-master/addResignForm','App\Http\Controllers\EmployeeMaster@addResignForm');
Route::post('employees-summary/filterDesignationWiseDashboard','App\Http\Controllers\EmployeeSummaryController@filterDesignationWiseDashboard')->middleware('checkpermission:view_employee_summary');
Route::post('employees-summary/filterStateWiseDashboard','App\Http\Controllers\EmployeeSummaryController@filterStateWiseDashboard')->middleware('checkpermission:view_employee_summary');
Route::get('dashboard/employeeAttendanceImportExcel', 'App\Http\Controllers\DashboardController@employeeAttendanceImportExcel');
Route::post('dashboard/importExcel', 'App\Http\Controllers\DashboardController@importExcel');

Route::post('employee-master/getEmployeeLeaveList','App\Http\Controllers\MyLeaveMasterController@index');
Route::post('employee-master/getEmployeeTimeOffList','App\Http\Controllers\TimeOffController@index');
Route::post('employee-master/getEmployeeAttendanceList','App\Http\Controllers\MyAttendanceController@index');

Route::post('employee-master/updateResignStatus','App\Http\Controllers\EmployeeMaster@updateResignStatus');
Route::post('employees-summary/filterEmployeeSummary','App\Http\Controllers\EmployeeSummaryController@filterEmployeeSummary')->middleware('checkpermission:view_employee_summary');

Route::get('employee-master/probation-period-employee','App\Http\Controllers\EmployeeMaster@empFilterByStatus');
Route::get('employee-master/notice-period-employee','App\Http\Controllers\EmployeeMaster@empFilterByStatus');

Route::get('leave-summary' ,'App\Http\Controllers\LeaveSummaryController@index')->middleware('checkpermission:view_leave_summary');
Route::post('leave-summary/filterLeaveSummary','App\Http\Controllers\LeaveSummaryController@filterLeaveSummary')->middleware('checkpermission:view_leave_summary');

Route::get('attendance-entry' ,'App\Http\Controllers\DashboardController@attedanceEntry');
Route::post('add-attendance' ,'App\Http\Controllers\DashboardController@addAttedance');
Route::post('employee-master/uploadProfilePic','App\Http\Controllers\EmployeeMaster@uploadProfilePic');
Route::get('village-master','App\Http\Controllers\VillageMasterController@index')->middleware('checkpermission:view_village');
Route::post('village-master/add', 'App\Http\Controllers\VillageMasterController@add')->middleware('checkpermission:add_village');
Route::post('village-master/edit','App\Http\Controllers\VillageMasterController@edit')->middleware('checkpermission:edit_village');
Route::post('village-master/filter','App\Http\Controllers\VillageMasterController@filter')->middleware('checkpermission:add_village');
Route::post('village-master/updateStatus','App\Http\Controllers\VillageMasterController@updateStatus')->middleware('checkpermission:edit_village');
Route::post('village-master/delete/{id}','App\Http\Controllers\VillageMasterController@delete')->middleware('checkpermission:delete_village');
Route::post('village-master/checkUniqueVillageName','App\Http\Controllers\VillageMasterController@checkUniqueVillageName');
Route::get('probation-assessments/{employeeId}/xlsx', 'App\\Http\\Controllers\\ProbationAssessmentController@exportXlsx')->middleware('checklogin');
// Probation Assessments (permission group 8 only - enforced in controller)
Route::get('probation-assessments', 'App\\Http\\Controllers\\ProbationAssessmentController@index')->middleware('checklogin');
Route::get('probation-assessments/{employeeId}', 'App\\Http\\Controllers\\ProbationAssessmentController@show')->middleware('checklogin');
Route::post('probation-assessments/{employeeId}', 'App\\Http\\Controllers\\ProbationAssessmentController@store')->middleware('checklogin');
Route::get('probation-assessments/{employeeId}/pdf', 'App\\Http\\Controllers\\ProbationAssessmentController@exportPdf')->middleware('checklogin');
// View-only docs (quick-links)
Route::middleware(['checklogin'])->group(function() {
    Route::get('docs/view/{filename}', 'App\\Http\\Controllers\\DocsController@viewQuickLink')
        ->where('filename', '[A-Za-z0-9._-]+');
    Route::get('docs/stream/{filename}', 'App\\Http\\Controllers\\DocsController@streamQuickLink')
        ->where('filename', '[A-Za-z0-9._-]+');
});

// Performance Appraisals
Route::get('performance-appraisals', 'App\\Http\\Controllers\\PerformanceAppraisalController@index')->middleware('checklogin');
Route::get('performance-appraisals/export', 'App\\Http\\Controllers\\PerformanceAppraisalController@exportToExcel')->middleware('checklogin')->name('performance-appraisals.export');
Route::get('performance-appraisals/{employeeId}', 'App\\Http\\Controllers\\PerformanceAppraisalController@show')->middleware('checklogin');
Route::post('performance-appraisals/{employeeId}', 'App\\Http\\Controllers\\PerformanceAppraisalController@store')->middleware('checklogin');
Route::get('performance-appraisals/export', 'App\\Http\\Controllers\\PerformanceAppraisalController@exportToExcel')->middleware('checklogin')->name('performance-appraisals.export');
Route::post('employee-master/addSuspendHistory','App\Http\Controllers\EmployeeMaster@addSuspendHistory');
Route::post('employee-master/getSuspendInfo','App\Http\Controllers\EmployeeMaster@getSuspendInfo');
Route::post('employee-master/checkSuspendedDateInfo','App\Http\Controllers\EmployeeMaster@checkSuspendedDateInfo');

Route::get('duplicate-leave-check' ,'App\Http\Controllers\DashboardController@duplicateLeaveCheck');

Route::post('my-leaves/checkDuplicateLeave','App\Http\Controllers\MyLeaveMasterController@checkDuplicateLeave');
Route::post('employee-master/checkUniqueSuspendDate','App\Http\Controllers\EmployeeMaster@checkUniqueSuspendDate');
Route::get('employee-report' , 'App\Http\Controllers\ReportController@employeeReport')->middleware('checkpermission:view_employee_report');
Route::post('employee-report/employeeReportfilter' , 'App\Http\Controllers\ReportController@employeeReportfilter')->middleware('checkpermission:view_employee_report');

Route::get('document-report' , 'App\Http\Controllers\ReportController@documentReport')->middleware('checkpermission:view_documents_report');

Route::get('incident-report' , 'App\Http\Controllers\IncidentReportController@index')->middleware('checkpermission:view_incident_report');
Route::get('incident-report/showAddForm' , 'App\Http\Controllers\IncidentReportController@showAddForm')->middleware('checkpermission:add_incident_report');
Route::post('incident-report/add' , 'App\Http\Controllers\IncidentReportController@add')->middleware('checkpermission:add_incident_report,edit_incident_report');
Route::get('incident-report/showEditForm/{id}' , 'App\Http\Controllers\IncidentReportController@showEditForm')->middleware('checkpermission:edit_incident_report');
Route::post('incident-report/delete/{id}' , 'App\Http\Controllers\IncidentReportController@delete')->middleware('checkpermission:delete_incident_report');
Route::post('incident-report/view-incident-report' , 'App\Http\Controllers\IncidentReportController@viewIncidentReport')->middleware('checkpermission:view_incident_report');
Route::post('incident-report/updateStatus' , 'App\Http\Controllers\IncidentReportController@updateStatus')->middleware('checkpermission:edit_incident_report');
Route::post('incident-report/filter' , 'App\Http\Controllers\IncidentReportController@filter')->middleware('checkpermission:view_incident_report');

Route::post('employee-master/getEmployeeDocumentList','App\Http\Controllers\MyDocumentMasterController@index');
Route::get('my-documents','App\Http\Controllers\MyDocumentMasterController@index');


Route::get('upload-daily-attendance-summary','App\Http\Controllers\UploadDailyAttendanceController@index')->middleware('checkpermission:view_uploaded_attendance_summary');
Route::post('upload-daily-attendance-summary/filter','App\Http\Controllers\UploadDailyAttendanceController@filter')->middleware('checkpermission:view_uploaded_attendance_summary');
Route::post('upload-daily-attendance-summary/uploadDailyAttendance','App\Http\Controllers\UploadDailyAttendanceController@uploadDailyAttendance')->middleware('checkpermission:add_uploaded_attendance_summary');

Route::get('upload-daily-attendance','App\Http\Controllers\UploadDailyAttendanceController@attendanceData')->middleware('checkpermission:view_uploaded_attendance_data');
Route::post('upload-daily-attendance/filterAttendanceData','App\Http\Controllers\UploadDailyAttendanceController@filterAttendanceData')->middleware('checkpermission:view_uploaded_attendance_data');

Route::get('upload-daily-attendance/{filterDate}','App\Http\Controllers\UploadDailyAttendanceController@setAttedanceDate')->middleware('checkpermission:view_uploaded_attendance_data');

Route::post('get-year-wise-holiday','App\Http\Controllers\DashboardController@getYearWiseHoliday');
Route::post('employee-master/checkUniquePersonalEmailId','App\Http\Controllers\EmployeeMaster@checkUniquePersonalEmailId');

Route::post('employee-master/show-probation-history','App\Http\Controllers\EmployeeMaster@showProbationHistory');
Route::get('my-profile','App\Http\Controllers\EmployeeMaster@profile');

Route::post('employee-master/sendLoginInvitation','App\Http\Controllers\EmployeeMaster@sendLoginInvitation');

Route::get('send-birthday-mail','App\Http\Controllers\CronController@sendBirthdayReminder');
Route::get('send-anniversary-mail','App\Http\Controllers\CronController@sendAnniversaryReminder');

Route::get('employee-report/{emp_status}' , 'App\Http\Controllers\ReportController@employeeReportStatusFilter')->middleware('checkpermission:view_employee_report');
Route::get('employee-report/{emp_status}/{team_id}' , 'App\Http\Controllers\ReportController@employeeReportStatusFilter')->middleware('checkpermission:view_employee_report');
Route::get('employee-report/{emp_status}/{team_id}/{city_id}' , 'App\Http\Controllers\ReportController@employeeReportStatusFilter')->middleware('checkpermission:view_employee_report');

Route::post('employee-master/getResignTerminateRequestInfo' , 'App\Http\Controllers\EmployeeMaster@getResignTerminateRequestInfo');
Route::post('employee-master/cancelResignation' , 'App\Http\Controllers\EmployeeMaster@cancelResignation');

Route::get('employee-duration-report' , 'App\Http\Controllers\ReportController@employeeDurationReport')->middleware('checkpermission:view_employee_duration_report');
Route::post('employee-duration-report/employeeDurationFilter' , 'App\Http\Controllers\ReportController@employeeDurationFilter')->middleware('checkpermission:view_employee_duration_report');

Route::get('incident-summary' , 'App\Http\Controllers\IncidentReportController@incidentSummary')->middleware('checkpermission:view_incident_summary');
Route::post('incident-summary/incidentSummaryFilter' , 'App\Http\Controllers\IncidentReportController@incidentSummaryFilter')->middleware('checkpermission:view_incident_summary');

Route::get('time-off-summary' ,'App\Http\Controllers\TimeOffReportController@timeOffSummaryIndex')->middleware('checkpermission:view_time_off_summary');
Route::post('time-off-summary/timeOffSummaryfilter','App\Http\Controllers\TimeOffReportController@timeOffSummaryfilter')->middleware('checkpermission:view_time_off_summary');  

Route::post('employee-master/showSuspendHistory','App\Http\Controllers\EmployeeMaster@showSuspendHistory');

Route::get('role-permission/create','App\Http\Controllers\RolePermissionController@create');
Route::post('employee-master/openUploadFileDocumentModel','App\Http\Controllers\EmployeeMaster@editUploadFileDocument');

Route::get('access-denied','App\Http\Controllers\GuestController@accessDenidePage')->name('access-denied-page');
Route::post('document-report/documentReportFilter' , 'App\Http\Controllers\ReportController@documentReportFilter')->middleware('checkpermission:view_documents_report');

Route::post('employee-master/getProfilePicInfo','App\Http\Controllers\EmployeeMaster@getProfilePicInfo');

Route::get('mark-as-relive-employee','App\Http\Controllers\CronController@updateEmployeeRelivedStatus');
Route::get('notification','App\Http\Controllers\NotificationController@index');

Route::post('notification/filter','App\Http\Controllers\NotificationController@filter');

Route::get('settings','App\Http\Controllers\SettingsController@index');
Route::post('settings/add', 'App\Http\Controllers\SettingsController@add');


Route::get('role-permission','App\Http\Controllers\RolePermissionController@index');
Route::get('forgot-password', 'App\Http\Controllers\LoginController@forgotpassword');
Route::post(config('constants.LOGIN_SLUG') .  '/sendForgotPasswordMail', 'App\Http\Controllers\LoginController@sendForgotPasswordMail');
Route::get(config('constants.LOGIN_SLUG') .  '/newPassword/{encode_data}', 'App\Http\Controllers\LoginController@newPassword');
Route::post(config('constants.LOGIN_SLUG') .  '/updatePassword', 'App\Http\Controllers\LoginController@updatePassword');
Route::get(config('constants.LOGIN_SLUG') .  '/verifyOtp/{user_id}', 'App\Http\Controllers\LoginController@verifyOtp');
Route::post(config('constants.LOGIN_SLUG') .  '/checkotp', 'App\Http\Controllers\LoginController@checkotp');
Route::post('checkStrongPassword','App\Http\Controllers\GuestController@checkStrongPassword');

Route::post('role-permission/add','App\Http\Controllers\RolePermissionController@add');
Route::get('role-permission/showEditForm/{id}','App\Http\Controllers\RolePermissionController@showEditForm')->name('role-permission.showEditForm');
Route::post('role-permission/filter','App\Http\Controllers\RolePermissionController@filter');
Route::post('role-permission/checkUniqueRoleName','App\Http\Controllers\RolePermissionController@checkUniqueRoleName');

Route::post('role-permission/delete/{id}','App\Http\Controllers\RolePermissionController@delete')->name('role-permission.delete');

Route::get('salary','App\Http\Controllers\SalaryController@viewSalary');


Route::post('salary/verifyPassword', 'App\Http\Controllers\SalaryController@verifyPassword');
Route::get('redirect-salary-info', 'App\Http\Controllers\SalaryController@redirectViewSalaryInfo');

Route::post('salary/openSalaryModel', 'App\Http\Controllers\SalaryController@editSalaryModel');
Route::post('salary/getGroupSalaryComponent', 'App\Http\Controllers\SalaryController@getGroupSalaryComponent');

Route::post('leave/leave-type-history', 'App\Http\Controllers\MyLeaveMasterController@leaveTypeHistory');
Route::post('salary/updateReviseSalary', 'App\Http\Controllers\SalaryController@updateReviseSalary');

Route::post('employee-master/getEmployeeSalaryInfo','App\Http\Controllers\SalaryController@viewSalary');
Route::post('salary/delete-revise-salary-record','App\Http\Controllers\SalaryController@deleteReviseSalaryRecord');

Route::get('/my-payslip', 'App\Http\Controllers\SalaryController@myPayslip');


Route::post('/my-payslip/filter', 'App\Http\Controllers\SalaryController@myPayslipFilter');
Route::post('role-permission/get-employees', 'App\Http\Controllers\RolePermissionController@getEmployees');
Route::post('role-permission/assign-employee','App\Http\Controllers\RolePermissionController@assignEmployee');

Route::post('employee-master/getEmployeePaySlipInfo','App\Http\Controllers\SalaryController@myPayslip');
Route::get('employee-monthly-paid-leave-balance','App\Http\Controllers\CronController@addMonthlyPaidLeaveBalance');
Route::post('notification/notificationFilter','App\Http\Controllers\NotificationController@notificationFilter');

Route::get('incident-report/{emp_status}' , 'App\Http\Controllers\IncidentReportController@incidentReportStatusFilter')->middleware('checkpermission:view_incident_report');

Route::post('salary/editOnHoldSalaryModel','App\Http\Controllers\SalaryController@editOnHoldSalaryModel');
Route::post('salary/addOnHoldSalary','App\Http\Controllers\SalaryController@addOnHoldSalary');
Route::post('salary/checkUniqueMonthName','App\Http\Controllers\SalaryController@checkUniqueMonthName');


Route::get('show-leave-record/{record_id}','App\Http\Controllers\LeaveReportController@showLeaveNotificationRecord')->middleware('checkpermission:view_leave_report');
Route::get('show-timeoff-record/{record_id}','App\Http\Controllers\TimeOffReportController@showTimeoffNotificationRecord')->middleware('checkpermission:view_time_off_summary');

Route::get('show-leave-record/{notification_id}/{record_id}','App\Http\Controllers\LeaveReportController@showLeaveNotificationRecord')->middleware('checkpermission:view_leave_report');
Route::get('show-timeoff-record/{notification_id}/{record_id}','App\Http\Controllers\TimeOffReportController@showTimeoffNotificationRecord')->middleware('checkpermission:view_time_off_summary');

Route::get('mark-read-all-notification','App\Http\Controllers\DashboardController@markAsReadNotification');

Route::post('download-employee-document','App\Http\Controllers\MyDocumentMasterController@downloadEmployeeDocument');
Route::post('send-salary-slip','App\Http\Controllers\SalaryController@sendSalarySlip');
Route::post('generate-salary-slip','App\Http\Controllers\SalaryController@generateSalarySlip');

Route::post('my-leaves/checkLeaveBalance','App\Http\Controllers\MyLeaveMasterController@checkLeaveBalance');
Route::get('/design-statutory-bonus-report', 'App\Http\Controllers\HomeController@statutaroy_bonus_report');

Route::get('leave-report-month-wise-count' , 'App\Http\Controllers\ReportController@employeeLeaveReport')->middleware('checkpermission:view_leave_report_month_wise_count');
Route::post('leave-report-month-wise-count/leaveReportMonthFilter' , 'App\Http\Controllers\ReportController@leaveReportMonthFilter')->middleware('checkpermission:view_leave_report_month_wise_count');


Route::get('form-16-report' , 'App\Http\Controllers\ReportController@form16Report')->middleware('checkpermission:view_form_16_report');
Route::post('form-16-report/form16ReportFilter' , 'App\Http\Controllers\ReportController@form16ReportFilter')->middleware('checkpermission:view_form_16_report');

Route::get('statutory-bonus-report' , 'App\Http\Controllers\ReportController@statutoryBonusReport')->middleware('checkpermission:view_statutory_bonus_report');
Route::post('statutory-bonus-report/statutoryBonusReportFilter' , 'App\Http\Controllers\ReportController@statutoryBonusReportFilter')->middleware('checkpermission:view_statutory_bonus_report');


Route::get('upcoming-notice-period-mail','App\Http\Controllers\CronController@upcomingEndNoticePeriod');
Route::get('upcoming-probation-period-mail','App\Http\Controllers\CronController@upcomingEndProbationPeriod');

Route::get('resignation-report' , 'App\Http\Controllers\ReportController@resignationReport')->middleware('checkpermission:view_resignation_report');
Route::post('resignation-report/resignationReportFilter' , 'App\Http\Controllers\ReportController@resignationReportFilter')->middleware('checkpermission:view_resignation_report');

//Route::post('incident-report/view-close-status-model' , 'App\Http\Controllers\IncidentReportController@viewIncidentStatus');

Route::get('show-resignation-report/{notification_id}/{record_id}','App\Http\Controllers\ReportController@showResignationNotificationRecord')->middleware('checkpermission:view_resignation_report');

Route::post('incident-report/viewIncidentStatus' , 'App\Http\Controllers\IncidentReportController@viewIncidentStatus');
Route::get('assign-unpaid-leave','App\Http\Controllers\CronController@assignUnPaidLeaveType');


Route::post('employee-master/cancelSuspension' , 'App\Http\Controllers\EmployeeMaster@cancelSuspension');
Route::get('update-existing-employee-working-date' , 'App\Http\Controllers\CronController@updateExistingEmployeeWorkingDate');

Route::get('start-employee-suspension' , 'App\Http\Controllers\CronController@startEmployeeSuspension');

Route::get('fetch-employee-daily-attedance' , 'App\Http\Controllers\CronController@fetchEmployeeDailyAttendance');
Route::get('fetch-employee-daily-attedance/{date}' , 'App\Http\Controllers\CronController@fetchEmployeeDailyAttendance');
Route::get('fetch-employee-daily-attedance/{date}/{status}' , 'App\Http\Controllers\CronController@fetchEmployeeDailyAttendance');
Route::get('add-employee-daily-attedance' , 'App\Http\Controllers\CronController@addEmployeeDailyAttendance');
Route::get('add-employee-daily-attedance/{date}' , 'App\Http\Controllers\CronController@addEmployeeDailyAttendance');

Route::get('manage-attedance-summary' , 'App\Http\Controllers\CronController@manageAttendanceSummary');
Route::get('manage-attedance-summary/{date}' , 'App\Http\Controllers\CronController@manageAttendanceSummary');
Route::get('manage-attedance-summary/{date}/{employee_id}' , 'App\Http\Controllers\CronController@manageAttendanceSummary');

Route::get('edit-attendance' , 'App\Http\Controllers\MyAttendanceController@editAttendance')->middleware('checkpermission:view_manage_attendance');
Route::post('filter-edit-attedance' , 'App\Http\Controllers\MyAttendanceController@filterEditAttendance');
Route::post('update-attendance' , 'App\Http\Controllers\MyAttendanceController@updateAttendance');

Route::get('salary-report' , 'App\Http\Controllers\ReportController@salaryReport')->middleware('checkpermission:view_salary_report');
Route::post('filterSalaryReport' , 'App\Http\Controllers\ReportController@filterSalaryReport')->middleware('checkpermission:view_salary_report');

Route::get('salary-report-for-account-team' , 'App\Http\Controllers\ReportController@accountTeamSalaryReport')->middleware('checkpermission:view_salary_report_for_account_team');
Route::post('filterAccountTeamSalaryReport' , 'App\Http\Controllers\ReportController@filterAccountTeamSalaryReport')->middleware('checkpermission:view_salary_report_for_account_team');

Route::get('on-hold-salary-report' , 'App\Http\Controllers\ReportController@onHoldSalaryReport')->middleware('checkpermission:view_on_hold_salary_report');
Route::post('filterOnHoldSalaryReport' , 'App\Http\Controllers\ReportController@filterOnHoldSalaryReport')->middleware('checkpermission:view_on_hold_salary_report');



Route::get('view-salary/{salary_record_id}','App\Http\Controllers\SalaryController@viewSalarySlip')->name('view-salary');
Route::get('download-salary/{salary_record_id}','App\Http\Controllers\SalaryController@downloadSalary')->name('download-salary');
Route::post('send-pay-slip' , 'App\Http\Controllers\SalaryController@sendSinglePaySlip');
Route::post('send-multiple-pay-slip' , 'App\Http\Controllers\SalaryController@sendMultiplePaySlip');

Route::post('updte-on-hold-salary-status' , 'App\Http\Controllers\ReportController@updateOnHoldSalaryStatus');
Route::post('deducted-on-hold-salary-history' , 'App\Http\Controllers\ReportController@deductedOnHoldSalaryHistory');
Route::post('planned-on-hold-salary-history' , 'App\Http\Controllers\ReportController@plannedOnHoldSalaryHistory');

Route::get('attendance-report-day-wise' , 'App\Http\Controllers\ReportController@attendanceReportDayWise')->middleware('checkpermission:view_attendance_report_day_wise');
Route::post('filterAttendanceReportDayWise' , 'App\Http\Controllers\ReportController@filterAttendanceReportDayWise')->middleware('checkpermission:view_attendance_report_day_wise');

Route::get('salary-increment-report' , 'App\Http\Controllers\ReportController@salaryIncrementReport')->middleware('checkpermission:view_salary_increment_report');
Route::post('filterSalaryIncrementReport' , 'App\Http\Controllers\ReportController@filterSalaryIncrementReport')->middleware('checkpermission:view_salary_increment_report');

Route::get('salary-summary' , 'App\Http\Controllers\SalaryController@summary')->middleware('checkpermission:view_salary_summary');
Route::post('filterSalarySummary' , 'App\Http\Controllers\SalaryController@filterSalarySummary')->middleware('checkpermission:view_salary_summary');











Route::get('attendance-report' , 'App\Http\Controllers\ReportController@attendenceReport')->middleware('checkpermission:view_attendance_report');
Route::post('attendance-report/attendanceReportFilter' , 'App\Http\Controllers\ReportController@attendanceReportFilter')->middleware('checkpermission:view_attendance_report');

Route::get('attendance-report' , 'App\Http\Controllers\ReportController@attendenceReport')->middleware('checkpermission:view_attendance_report');


Route::post('leave-report/get-status-wise-emp-details' , 'App\Http\Controllers\LeaveReportController@getStatusWiseEmpDetails')->middleware('checkpermission:view_leave_report');
Route::post('get-status-wise-emp-details' , 'App\Http\Controllers\MasterController@getStatusWiseEmpDetails');

Route::get('send-missing-leave-reminder' , 'App\Http\Controllers\CronController@sendMissingLeaveReminder');
Route::get('send-missing-leave-reminder/{date}' , 'App\Http\Controllers\CronController@sendMissingLeaveReminder');
Route::post('leave/viewPendingLeave' , 'App\Http\Controllers\LeaveReportController@viewPendingLeave')->middleware('checkpermission:view_leave_report');

Route::post('salary/auto-approve-pending-leave' , 'App\Http\Controllers\SalaryController@autoApprovePendingLeave');
Route::get('send-hold-salary-release-reminder' , 'App\Http\Controllers\CronController@sendHoldSalaryReleaseReminderMail');

Route::get('attendance-summary' , 'App\Http\Controllers\MyAttendanceController@attendanceSummary')->middleware('checkpermission:view_attendance_summary');
Route::post('attendance-summary-filter' , 'App\Http\Controllers\MyAttendanceController@attendanceSummaryFilter')->middleware('checkpermission:view_attendance_summary');

Route::get('view-today-leave' , 'App\Http\Controllers\LeaveReportController@viewTodayLeave')->middleware('checkpermission:view_leave_report');
Route::get('view-today-adjustment' , 'App\Http\Controllers\TimeOffReportController@viewTodayAdjustment')->middleware('checkpermission:view_time_off_summary');

Route::get('/role-permission/assign-to-employees/{id}' , 'App\Http\Controllers\RolePermissionController@viewAssignToEmployees');
Route::post('/role-permission/filter-employee' , 'App\Http\Controllers\RolePermissionController@filterEmployee');
Route::post('/role-permission/add-assign-to-employees' , 'App\Http\Controllers\RolePermissionController@addAssginToEmployee');

Route::post('salary/employee-salary-amendment' , 'App\Http\Controllers\SalaryController@amendmentSalary');
Route::get('employee-monthly-paid-leave-balance/{date}','App\Http\Controllers\CronController@addMonthlyPaidLeaveBalance');


Route::get('send-pending-leave-approve-reminder' , 'App\Http\Controllers\CronController@sendPendingLeaveReminder');
Route::get('send-pending-leave-approve-reminder/{date}' , 'App\Http\Controllers\CronController@sendPendingLeaveReminder');
Route::get('update-salary-into-master' , 'App\Http\Controllers\CronController@updateSalaryIntoMaster');
Route::get('update-salary-into-master/{date}' , 'App\Http\Controllers\CronController@updateSalaryIntoMaster');

Route::post('salary/update-salary-paid-day' , 'App\Http\Controllers\SalaryController@updateSalaryPaidDay');

Route::get('retrive-notification','App\Http\Controllers\CronController@retriveNotification');
Route::get('retrive-notification/{user_id}','App\Http\Controllers\CronController@retriveNotification');
Route::get('retrive-notification/{user_id}/{date}','App\Http\Controllers\CronController@retriveNotification');
Route::get('retrive-time-attendance-events','App\Http\Controllers\CronController@retiveTimeAttendanceEvents');
Route::get('retrive-time-attendance-events/{date}','App\Http\Controllers\CronController@retiveTimeAttendanceEvents');
Route::get('retrive-time-attendance-events/{date}/{user_id}','App\Http\Controllers\CronController@retiveTimeAttendanceEvents');


Route::get('punch-report' , 'App\Http\Controllers\ReportController@punchReport')->middleware('checkpermission:view_punch_report');
Route::post('filterPunchReport' , 'App\Http\Controllers\ReportController@filterPunchReport')->middleware('checkpermission:view_punch_report'); 

Route::post('employee-master/importLeaveBalance' , 'App\Http\Controllers\EmployeeMaster@importLeaveBalance');

Route::get('view-all-employee-list' , 'App\Http\Controllers\DashboardController@viewAllEmployeeList');
Route::post('filter-all-employee-list' , 'App\Http\Controllers\DashboardController@filterAllEmployeelist');
Route::post('update-employee-role' , 'App\Http\Controllers\DashboardController@updateEmployeeRole');

Route::post('document-report/getDocumentTypes' , 'App\Http\Controllers\ReportController@getDocumentTypes')->middleware('checkpermission:view_documents_report');

Route::post('sync-attendance' , 'App\Http\Controllers\MyAttendanceController@syncAttendanceDate');

        
    // Export probation assessments to Excel
    Route::get('probation-assessments-export', 'App\Http\Controllers\ProbationAssessmentController@exportToExcel')
        ->name('probation.export');

Route::get('update-employee-shift' , 'App\Http\Controllers\CronController@updateEmployeeShift');
Route::get('update-employee-shift/{date}' , 'App\Http\Controllers\CronController@updateEmployeeShift');
Route::get('update-employee-shift/{date}/{employee_id}' , 'App\Http\Controllers\CronController@updateEmployeeShift');

Route::get('missing-punch' , 'App\Http\Controllers\ReportController@missingPunch')->middleware('checkpermission:view_missing_punch_report');
Route::post('filterMissingPunch' , 'App\Http\Controllers\ReportController@filterMissingPunch')->middleware('checkpermission:view_missing_punch_report');

Route::get('manage-carry-forward-leave-balance' , 'App\Http\Controllers\CronController@manageCarryForwardLeave');
//Route::get('manage-carry-forward-leave-balance/{employee_id}' , 'App\Http\Controllers\CronController@manageCarryForwardLeave');
Route::get('manage-carry-forward-leave-balance/{leave_date}' , 'App\Http\Controllers\CronController@manageCarryForwardLeave');

Route::post('login/checkOtp', 'App\Http\Controllers\LoginController@checkOtp');
Route::get('login/verifyOtp/{id}', 'App\Http\Controllers\LoginController@verifyOtp');