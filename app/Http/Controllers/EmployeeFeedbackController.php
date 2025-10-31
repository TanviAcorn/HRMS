<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\EmployeeFeedbackOneMonth;
use App\Models\EmployeeFeedbackSixMonth;
use App\EmployeeModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
class EmployeeFeedbackController extends Controller
{
    /**
     * Display the feedback form for an employee
     */
    public function showForm($employeeId)
    {
        // Add debugging
        Log::info('EmployeeFeedback showForm called with ID: ' . $employeeId);
 
        // Get employee details with relations needed for display
        $employee = EmployeeModel::with(['teamInfo','designationInfo'])->where('i_id', $employeeId)->first();
 
        if (!$employee) {
            Log::error('Employee not found with ID: ' . $employeeId);
            return redirect()->back()->with('error', 'Employee not found');
        }
 
   
 
   
 
        Log::info('Employee found: ' . $employee->v_employee_full_name);
 
        // Check if user has permission to access this form
        $currentEmployee = EmployeeModel::where('i_login_id', session()->get('user_id'))->first();
        $isAdmin = (session()->has('role') && session()->get('role') == 1); // Assuming admin role is 1
 
        Log::info('Current user role: ' . session()->get('role'));
        Log::info('Is admin: ' . ($isAdmin ? 'true' : 'false'));
 
        if (!$isAdmin && (!$currentEmployee || $currentEmployee->i_id != $employeeId)) {
            Log::error('Permission denied for employee ID: ' . $employeeId);
            return redirect('page-not-found');
        }
 
        // Enrich employee with department and designation names for one-month form
        try {
            $employee->team_name = isset($employee->teamInfo) && isset($employee->teamInfo->v_value) ? $employee->teamInfo->v_value : 'N/A';
        } catch (\Exception $e) {
            $employee->team_name = 'N/A';
        }
        try {
            $employee->designation_name = isset($employee->designationInfo) && isset($employee->designationInfo->v_value) ? $employee->designationInfo->v_value : 'N/A';
        } catch (\Exception $e) {
            $employee->designation_name = 'N/A';
        }
 
        $existingFeedback = EmployeeFeedbackOneMonth::where('v_emp_code', $employee->v_employee_code)->first();
        $viewOnly = $existingFeedback ? true : false;
 
        return view('admin.employee-feedback.form', compact('employee', 'existingFeedback', 'viewOnly'));
    }
 
    /**
     * Display the six month feedback form for an employee
     */
    public function showSixMonthForm($employeeId)
    {
        Log::info('EmployeeFeedback showSixMonthForm called with ID: ' . $employeeId);
 
        $employee = EmployeeModel::find($employeeId);
        if (!$employee) {
            Log::error('Employee not found with ID: ' . $employeeId);
            return redirect()->back()->with('error', 'Employee not found');
        }
 
        $currentEmployee = EmployeeModel::where('i_login_id', session()->get('user_id'))->first();
        $isAdmin = (session()->has('role') && session()->get('role') == 1);
        if (!$isAdmin && (!$currentEmployee || $currentEmployee->i_id != $employeeId)) {
            return redirect('page-not-found');
        }
 
        // Enrich employee with department and designation names
        try {
            $employee->team_name = isset($employee->teamInfo) && isset($employee->teamInfo->v_value) ? $employee->teamInfo->v_value : 'N/A';
        } catch (\Exception $e) {
            $employee->team_name = 'N/A';
        }
        try {
            $employee->designation_name = isset($employee->designationInfo) && isset($employee->designationInfo->v_value) ? $employee->designationInfo->v_value : 'N/A';
        } catch (\Exception $e) {
            $employee->designation_name = 'N/A';
        }
 
        // Check if already submitted for this employee code
        $existingFeedback = EmployeeFeedbackSixMonth::where('v_emp_code', $employee->v_employee_code)->first();
        $viewOnly = $existingFeedback ? true : false;
 
        return view('admin.employee-feedback.six-month-form', compact('employee', 'existingFeedback', 'viewOnly'));
    }
 
    /**
     * Store the six month feedback data
     */
    public function storeSixMonth(Request $request, $employeeId)
    {
        Log::info('EmployeeFeedback storeSixMonth called for employee ID: ' . $employeeId);
 
        // Ensure not already submitted for this employee
        $employee = EmployeeModel::find($employeeId);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }
        $alreadySubmitted = EmployeeFeedbackSixMonth::where('v_emp_code', $employee->v_employee_code)->exists();
        if ($alreadySubmitted) {
            return redirect()->route('employee-master.profile', $employeeId)
                ->with('error', 'Six month feedback already submitted.');
        }
 
        $validator = Validator::make($request->all(), [
            'v_emp_code' => 'required|string|max:50',
            'v_employee_name' => 'required|string|max:150',
            'v_department_name' => 'required|string|max:150',
            'v_designation' => 'required|string|max:150',
            'dt_date_of_joining' => 'required|date',
            'dt_date_of_assessment' => 'required|date',
            'i_teamwork_collaboration' => 'required|integer|min:1|max:5',
            'i_team_communication' => 'required|integer|min:1|max:5',
            'i_team_support' => 'required|integer|min:1|max:5',
            'i_manager_guidance' => 'required|integer|min:1|max:5',
            'i_manager_feedback_timely' => 'required|integer|min:1|max:5',
            'i_team_meeting_effective' => 'required|integer|min:1|max:5',
            'i_efforts_recognized' => 'required|integer|min:1|max:5',
            'i_understand_mission' => 'required|integer|min:1|max:5',
            'i_company_culture_respect' => 'required|integer|min:1|max:5',
            'i_internal_communication' => 'required|integer|min:1|max:5',
            'i_growth_opportunities' => 'required|integer|min:1|max:5',
            'i_career_progression' => 'required|integer|min:1|max:5',
            'i_worklife_balance' => 'required|integer|min:1|max:5',
            'i_manager_guidance_rating' => 'required|integer|min:0|max:10',
            'i_meeting_effectiveness_rating' => 'required|integer|min:0|max:10',
            'i_manager_satisfaction_rating' => 'required|integer|min:0|max:10',
            'b_growth_opportunities_available' => 'nullable|boolean',
        ]);
 
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
 
        try {
            $data = $request->all();
            // Ensure correct employee code is stored
            $data['v_emp_code'] = $employee->v_employee_code;
            // Optional timestamps if present in table
            $data['dt_created_at'] = $data['dt_created_at'] ?? now();
            $data['dt_updated_at'] = $data['dt_updated_at'] ?? now();
            $feedback = EmployeeFeedbackSixMonth::create($data);
 
            Log::info('Six month feedback saved with ID: ' . $feedback->i_id);
            return redirect()->route('employee-master.profile', $employeeId)
                ->with('success', 'Six month feedback submitted successfully!');
        } catch (\Exception $e) {
            Log::error('Error saving six month feedback: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error submitting six month feedback: ' . $e->getMessage())
                ->withInput();
        }
    }
 
    /**
     * Export six-month feedback submissions to Excel for a date range
     */
    public function exportSixMonthExcel(Request $request)
    {
        try {
            $from = $request->query('from');
            $to   = $request->query('to');
 
            $fromDate = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->startOfMonth();
            $toDate   = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();
 
            $records = EmployeeFeedbackSixMonth::whereBetween('dt_created_at', [$fromDate->format('Y-m-d H:i:s'), $toDate->format('Y-m-d H:i:s')])
                ->orderBy('dt_created_at', 'desc')
                ->get();
 
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
 
            $headers = [
                'S.No',
                'Employee Name',
                'Employee Code',
                'Department',
                'Designation',
                'Date of Joining',
                'Date of Assessment',
                'Teamwork & Collaboration',
                'Team Communication',
                'Team Support',
                'Team Issues/Conflicts',
                'Manager Guidance',
                'Manager Feedback Timely',
                'Team Meeting Effective',
                'Efforts Recognized',
                'Understand Mission',
                'Company Culture Respect',
                'Internal Communication',
                'Growth Opportunities',
                'Career Progression',
                'Worklife Balance',
                'Manager Guidance Rating',
                'Meeting Effectiveness Rating',
                'Manager Satisfaction Rating',
                'Improvement Suggestions',
                'Growth Opportunities Available',
                'Growth Opportunities Other',
                'Productivity Suggestions',
                'Created At',
                'Updated At',
            ];
 
            $sheet->fromArray($headers, null, 'A1');
 
            $row = 2; $serial = 1;
            foreach ($records as $rec) {
                $sheet->fromArray([
                    $serial,
                    $rec->v_employee_name,
                    $rec->v_emp_code,
                    $rec->v_department_name,
                    $rec->v_designation,
                    $rec->dt_date_of_joining,
                    $rec->dt_date_of_assessment,
                    $rec->i_teamwork_collaboration,
                    $rec->i_team_communication,
                    $rec->i_team_support,
                    $rec->t_team_issues_conflicts,
                    $rec->i_manager_guidance,
                    $rec->i_manager_feedback_timely,
                    $rec->i_team_meeting_effective,
                    $rec->i_efforts_recognized,
                    $rec->i_understand_mission,
                    $rec->i_company_culture_respect,
                    $rec->i_internal_communication,
                    $rec->i_growth_opportunities,
                    $rec->i_career_progression,
                    $rec->i_worklife_balance,
                    $rec->i_manager_guidance_rating,
                    $rec->i_meeting_effectiveness_rating,
                    $rec->i_manager_satisfaction_rating,
                    $rec->t_improvement_suggestions,
                    $rec->b_growth_opportunities_available,
                    $rec->t_growth_opportunities_other,
                    $rec->t_productivity_suggestions,
                    $rec->dt_created_at,
                    $rec->dt_updated_at,
                ], null, 'A'.$row);
                $row++; $serial++;
            }
 
            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
 
            $fileName = 'employee_feedback_six_month_' . $fromDate->format('Ymd') . '_' . $toDate->format('Ymd') . '.xlsx';
            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]);
        } catch (\Exception $e) {
            Log::error('Six Month Export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export Excel: ' . $e->getMessage());
        }
    }
 
    /**
     * Export one-month feedback submissions to Excel for a date range
     */
    public function exportOneMonthExcel(Request $request)
    {
        try {
            $from = $request->query('from');
            $to   = $request->query('to');
 
            // Default to current month if not provided
            $fromDate = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->startOfMonth();
            $toDate   = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();
 
            $records = EmployeeFeedbackOneMonth::whereBetween('dt_created_at', [$fromDate->format('Y-m-d H:i:s'), $toDate->format('Y-m-d H:i:s')])
                ->orderBy('dt_created_at', 'desc')
                ->get();
 
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
 
            // Headers
            $headers = [
                'S.No',
                'Employee Name',
                'Employee Code',
                'Department',
                'Designation',
                'Date of Joining',
                'Understand Onboarding Process',
                'Understand Company Policy',
                'Well Trained About Process',
                'Aware Department Process',
                'Trained For Responsibilities',
                'Interested And Motivated',
                'Responsibilities Assigned',
                'Feel Welcomed By Team',
                'Team Bonding',
                'Team Motivates',
                'Comfortable Giving Feedback',
                'Manager Supportive',
                'Learn From Manager',
                'Understand Goals',
                'Joining Designation',
                'Joining DOJ',
                'Joining ID Card',
                'Joining Bank Account',
                'Doc Appointment Letter',
                'Doc List Of Holidays',
                'Doc HRMS Login',
                'Team Leader Intro',
                'Team Intro',
                'Teamwork Allocation',
                'Team Satisfaction',
                'Work Satisfaction',
                'Created At',
                'Updated At',
            ];
 
            $sheet->fromArray($headers, null, 'A1');
 
            $row = 2;
            $serial = 1;
            foreach ($records as $rec) {
                $dataRow = [
                    $serial,
                    $rec->v_employee_name,
                    $rec->v_emp_code,
                    $rec->v_department,
                    $rec->v_designation,
                    $rec->d_date_of_joining,
                    $rec->i_understand_onboarding_process,
                    $rec->i_understand_company_policy,
                    $rec->i_well_trained_about_process,
                    $rec->i_aware_department_process,
                    $rec->i_trained_for_responsibilities,
                    $rec->i_interested_and_motivated,
                    $rec->i_responsibilities_assigned,
                    $rec->i_feel_welcomed_by_team,
                    $rec->i_team_bonding,
                    $rec->i_team_motivates,
                    $rec->i_comfortable_giving_feedback,
                    $rec->i_manager_supportive,
                    $rec->i_learn_from_manager,
                    $rec->i_understand_goals,
                    $rec->b_joining_designation,
                    $rec->b_joining_doj,
                    $rec->b_joining_id_card,
                    $rec->b_joining_bank_account,
                    $rec->b_doc_appointment_letter,
                    $rec->b_doc_list_of_holidays,
                    $rec->b_doc_hrms_login,
                    $rec->b_team_leader_intro,
                    $rec->b_team_intro,
                    $rec->b_teamwork_allocation,
                    $rec->b_team_satisfaction,
                    $rec->b_work_satisfaction,
                    $rec->dt_created_at,
                    $rec->dt_updated_at,
                ];
                $sheet->fromArray($dataRow, null, 'A' . $row);
                $row++;
                $serial++;
            }
 
            // Autosize columns (supports columns beyond Z)
            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
 
            $fileName = 'employee_feedback_one_month_' . $fromDate->format('Ymd') . '_' . $toDate->format('Ymd') . '.xlsx';
 
            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]);
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export Excel: ' . $e->getMessage());
        }
    }
 
    /**
     * Store the feedback form data
     */
    public function store(Request $request, $employeeId)
    {
        Log::info('EmployeeFeedback store called for employee ID: ' . $employeeId);
        Log::info('Request data: ', $request->all());
 
        // Prevent duplicate submission
        $employee = EmployeeModel::find($employeeId);
        if ($employee) {
            $alreadySubmitted = EmployeeFeedbackOneMonth::where('v_emp_code', $employee->v_employee_code)->exists();
            if ($alreadySubmitted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Feedback already submitted.'
                ], 409);
            }
        }
 
        // Validate the request
        $validator = Validator::make($request->all(), [
            'v_employee_name' => 'required|string|max:255',
            'v_emp_code' => 'required|string|max:100',
            'v_department' => 'required|string|max:255',
            'v_designation' => 'required|string|max:255',
            'd_date_of_joining' => 'required|date',
            'i_understand_onboarding_process' => 'required|integer|min:1|max:5',
            'i_understand_company_policy' => 'required|integer|min:1|max:5',
            'i_well_trained_about_process' => 'required|integer|min:1|max:5',
            'i_aware_department_process' => 'required|integer|min:1|max:5',
            'i_trained_for_responsibilities' => 'required|integer|min:1|max:5',
            'i_interested_and_motivated' => 'required|integer|min:1|max:5',
            'i_responsibilities_assigned' => 'required|integer|min:1|max:5',
            'i_feel_welcomed_by_team' => 'required|integer|min:1|max:5',
            'i_team_bonding' => 'required|integer|min:1|max:5',
            'i_team_motivates' => 'required|integer|min:1|max:5',
            'i_comfortable_giving_feedback' => 'required|integer|min:1|max:5',
            'i_manager_supportive' => 'required|integer|min:1|max:5',
            'i_learn_from_manager' => 'required|integer|min:1|max:5',
            'i_understand_goals' => 'required|integer|min:1|max:5',
            'b_joining_designation' => 'nullable|boolean',
            'b_joining_doj' => 'nullable|boolean',
            'b_joining_id_card' => 'nullable|boolean',
            'b_joining_bank_account' => 'nullable|boolean',
            'b_doc_appointment_letter' => 'nullable|boolean',
            'b_doc_list_of_holidays' => 'nullable|boolean',
            'b_doc_hrms_login' => 'nullable|boolean',
            'b_team_leader_intro' => 'nullable|boolean',
            'b_team_intro' => 'nullable|boolean',
            'b_teamwork_allocation' => 'nullable|boolean',
            'b_team_satisfaction' => 'nullable|boolean',
            'b_work_satisfaction' => 'nullable|boolean',
            't_suggestion' => 'nullable|string',
        ]);
 
        if ($validator->fails()) {
            Log::error('Validation failed: ', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
 
        try {
            // Radio buttons will always have a value (1 for Yes, 0 for No)
            // No need for special processing since they're always included in the request
 
            $data = $request->all();
            // Employee code is already included in the form data as v_emp_code
            // No need to add employee ID since the table uses employee code
 
            Log::info('Processed data for saving: ', $data);
 
            // Create feedback record
            $feedback = EmployeeFeedbackOneMonth::create($data);
 
            Log::info('Feedback saved successfully with ID: ' . $feedback->i_id);
 
            return response()->json([
                'success' => true,
                'message' => 'Feedback submitted successfully!'
            ]);
 
        } catch (\Exception $e) {
            Log::error('Error saving feedback: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error submitting feedback: ' . $e->getMessage()
            ], 500);
        }
    }
 
    /**
     * List employees who joined in the last 30 days for feedback forms
     */
    public function listFeedbackForms()
    {
        Log::info('EmployeeFeedback listFeedbackForms called');
 
        try {
            // Get current date
            $today = Carbon::now();
            $thirtyDaysAgo = Carbon::now()->subDays(30);
 
            Log::info('Date range - From: ' . $thirtyDaysAgo->format('Y-m-d') . ' To: ' . $today->format('Y-m-d'));
 
            // Get employees who joined in the last 30 days
            $employees = EmployeeModel::with([
                'loginInfo',
                'leaderInfo.loginInfo',
                'designationInfo',
                'subDesignationInfo',
                'teamInfo',
                'shiftInfo',
                'weekOffInfo'
            ])
            ->where('dt_joining_date', '>=', $thirtyDaysAgo->format('Y-m-d'))
            ->where('dt_joining_date', '<=', $today->format('Y-m-d'))
            ->where('t_is_deleted', 0)
            ->whereIn('e_employment_status', [
                config('constants.PROBATION_EMPLOYMENT_STATUS'),
                config('constants.CONFIRMED_EMPLOYMENT_STATUS')
            ])
            ->orderBy('dt_joining_date', 'desc')
            ->get();
 
            Log::info('Found ' . $employees->count() . ' employees who joined in the last 30 days');
 
            // Add one month completion date and submission status to each employee
            $employeesWithCompletionDate = $employees->map(function ($employee) {
                // Calculate one month completion date
                $joiningDate = Carbon::parse($employee->dt_joining_date);
                $oneMonthCompletionDate = $joiningDate->copy()->addMonth();
 
                // Check if employee has submitted feedback form
                $feedbackSubmitted = EmployeeFeedbackOneMonth::where('v_emp_code', $employee->v_employee_code)->exists();
 
                // Add calculated fields to employee object
                $employee->one_month_completion_date = $oneMonthCompletionDate->format('Y-m-d');
                $employee->one_month_completion_date_formatted = $oneMonthCompletionDate->format('d M Y');
                $employee->joining_date_formatted = $joiningDate->format('d M Y');
                $employee->days_until_completion = $joiningDate->diffInDays($oneMonthCompletionDate);
                $employee->feedback_status = $feedbackSubmitted ? 'Submitted' : 'Pending';
                $employee->feedback_submitted = $feedbackSubmitted;
 
                return $employee;
            });
 
            Log::info('Successfully calculated one month completion dates for all employees');
 
            return view('admin.employee-feedback.index', compact('employeesWithCompletionDate'));
 
        } catch (\Exception $e) {
            Log::error('Error in listFeedbackForms: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
 
            return redirect()->back()->with('error', 'Error loading feedback forms list: ' . $e->getMessage());
        }
    }
 
    /**
     * List employees who joined in the last 6 months for six-month feedback forms
     */
    public function listFeedbackFormsSixMonth()
    {
        Log::info('EmployeeFeedback listFeedbackFormsSixMonth called');
 
        try {
            $today = Carbon::now();
            $sixMonthsAgo = Carbon::now()->subMonths(6);
 
            // Get employees who joined in the last 6 months
            $employees = EmployeeModel::with([
                'loginInfo',
                'leaderInfo.loginInfo',
                'designationInfo',
                'subDesignationInfo',
                'teamInfo',
                'shiftInfo',
                'weekOffInfo'
            ])
            ->where('dt_joining_date', '>=', $sixMonthsAgo->format('Y-m-d'))
            ->where('dt_joining_date', '<=', $today->format('Y-m-d'))
            ->where('t_is_deleted', 0)
            ->whereIn('e_employment_status', [
                config('constants.PROBATION_EMPLOYMENT_STATUS'),
                config('constants.CONFIRMED_EMPLOYMENT_STATUS')
            ])
            ->orderBy('dt_joining_date', 'desc')
            ->get();
 
            // Compute six-month completion and submission status
            $employeesWithCompletionDate = $employees->map(function ($employee) {
                $joiningDate = Carbon::parse($employee->dt_joining_date);
                $sixMonthCompletionDate = $joiningDate->copy()->addMonths(6);
 
                $feedbackSubmitted = EmployeeFeedbackSixMonth::where('v_emp_code', $employee->v_employee_code)->exists();
 
                $employee->six_month_completion_date = $sixMonthCompletionDate->format('Y-m-d');
                $employee->six_month_completion_date_formatted = $sixMonthCompletionDate->format('d M Y');
                $employee->joining_date_formatted = $joiningDate->format('d M Y');
                $employee->days_until_completion = $joiningDate->diffInDays($sixMonthCompletionDate);
                $employee->feedback_status = $feedbackSubmitted ? 'Submitted' : 'Pending';
                $employee->feedback_submitted = $feedbackSubmitted;
 
                return $employee;
            });
 
            return view('admin.employee-feedback.index-six', compact('employeesWithCompletionDate'));
 
        } catch (\Exception $e) {
            Log::error('Error in listFeedbackFormsSixMonth: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error loading six-month feedback forms list: ' . $e->getMessage());
        }
    }
}