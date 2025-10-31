<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeFeedbackOneMonth;
use App\EmployeeModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class EmployeeFeedbackController extends Controller
{
    /**
     * Display the feedback form for an employee
     */
    public function showForm($employeeId)
    {
        // Add debugging
        Log::info('EmployeeFeedback showForm called with ID: ' . $employeeId);

        // Get employee details
        $employee = EmployeeModel::find($employeeId);

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

        return view('admin.employee-feedback.form', compact('employee'));
    }

    /**
     * Store the feedback form data
     */
    public function store(Request $request, $employeeId)
    {
        Log::info('EmployeeFeedback store called for employee ID: ' . $employeeId);
        Log::info('Request data: ', $request->all());

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
}
