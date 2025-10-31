<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmployeeModel;
use App\Models\ProbationAssessment;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Mail\ProbationAssessmentSubmitted;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;

class ProbationAssessmentController extends Controller
{
    // List direct reports in probation for managers with permission group 8
    public function index(Request $request)
    {
        $currentEmployee = EmployeeModel::select('i_id','i_role_permission')
            ->where('i_login_id', session()->get('user_id'))
            ->first();

        $isAdmin = ( session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN') );

        // Visible to permission group 8 and Admins (admins may not have employee record)
        if (!$isAdmin) {
            if (!$currentEmployee || (int)$currentEmployee->i_role_permission !== 4) {
                return redirect('page-not-found');
            }
        }

        $managerEmployeeId = session()->get('user_employee_id');

$reportsQuery = EmployeeModel::with(['designationInfo','teamInfo'])
    ->where('t_is_deleted', 0);

// Only apply probation status filter if not filtering by 'submitted' status
if (!$request->has('status') || $request->input('status') !== 'submitted') {
    $reportsQuery->where('e_employment_status', config('constants.PROBATION_EMPLOYMENT_STATUS'));
}

$reportsQuery->whereNotNull('dt_probation_end_date');
            
        // Check if any filters are being used
        $hasFilters = $request->has('status') || $request->has('search');
        
        // Only apply 30-day filter if no other filters are active
        if (!$hasFilters) {
$reportsQuery->whereBetween('dt_probation_end_date', [
    \Carbon\Carbon::now()->subDays(5)->startOfDay(), 
    \Carbon\Carbon::now()->endOfDay()
]);

        }
            
        if (!$isAdmin) {
            $reportsQuery->where('i_leader_id', $managerEmployeeId);
        }

        // Apply status filter if provided
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'not_started') {
                $reportsQuery->whereDoesntHave('probationAssessments');
            } elseif (in_array($status, ['draft', 'submitted', 'completed'])) {
                $reportsQuery->whereHas('probationAssessments', function($q) use ($status) {
                    $q->where('vch_status', $status);
                });
            }
        }
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $reportsQuery->where(function($query) use ($search) {
                $query->where('v_employee_full_name', 'like', "%$search%")
                      ->orWhere('v_employee_code', 'like', "%$search%");
            });
        }

        // Get all records without pagination
        $reports = $reportsQuery->orderBy('v_employee_full_name', 'asc')->get();

        // Get assessment status and data for each employee
        $assessmentStatusByEmp = [];
        $assessments = collect();
        
        if ($reports->isNotEmpty()) {
            $employeeIds = $reports->pluck('i_id')->toArray();
            $assessments = ProbationAssessment::whereIn('i_employee_id', $employeeIds)
                ->get()
                ->groupBy('i_employee_id')
                ->map(function($assessments) {
                    return $assessments->sortByDesc('created_at')->first();
                });

            foreach ($assessments as $assessment) {
                $assessmentStatusByEmp[$assessment->i_employee_id] = $assessment->vch_status ?? null;
            }
        }

        return view('probations.index', [
            'pageTitle' => 'Probation Assessments',
            'reports' => $reports,
            'assessmentStatusByEmp' => $assessmentStatusByEmp,
            'assessments' => $assessments,
        ]);
    }

    // Show form for a specific employee
    public function show($employeeId)
    {
        $currentEmployee = EmployeeModel::select('i_id','i_role_permission')
            ->where('i_login_id', session()->get('user_id'))
            ->first();
        $isAdmin = ( session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN') );
        if (!$isAdmin) {
            if (!$currentEmployee || (int)$currentEmployee->i_role_permission !== 4) {
                return redirect('page-not-found');
            }
        }

        $managerEmployeeId = session()->get('user_employee_id');
        $employeeQuery = EmployeeModel::with(['designationInfo','teamInfo','leaderInfo'])
            ->where('i_id', $employeeId)
            ->where('e_employment_status', config('constants.PROBATION_EMPLOYMENT_STATUS'));
        if (!$isAdmin) {
            $employeeQuery->where('i_leader_id', $managerEmployeeId);
        }
        $employee = $employeeQuery->first();
        if (!$employee) {
            return redirect()->back()->with('danger', 'Employee not found or not accessible.');
        }

        if ($isAdmin) {
            $assessment = ProbationAssessment::where('i_employee_id', $employee->i_id)
                ->orderBy('created_at','desc')
                ->first();
        } else {
            $assessment = ProbationAssessment::where([
                'i_employee_id' => $employee->i_id,
                'i_manager_id' => $managerEmployeeId,
            ])->first();
        }

        $readOnly = $isAdmin ? true : ($assessment && in_array($assessment->vch_status, ['submitted','completed']));

        return view('probations.form', [
            'pageTitle' => 'Probation Assessment - Form',
            'employee' => $employee,
            'assessment' => $assessment,
            'readOnly' => $readOnly,
        ]);
    }

    // Store/update
    public function store(Request $request, $employeeId)
    {
        $currentEmployee = EmployeeModel::select('i_id','i_role_permission')
            ->where('i_login_id', session()->get('user_id'))
            ->first();
        $isAdmin = ( session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN') );
        if ($isAdmin) {
            return redirect()->back()->with('danger', 'Admins can view assessments but cannot submit changes.');
        }
        if (!$currentEmployee || (int)$currentEmployee->i_role_permission !== 4) {
            return redirect('page-not-found');
        }

        $managerEmployeeId = session()->get('user_employee_id');
        $employee = EmployeeModel::where('i_id', $employeeId)
            ->where('i_leader_id', $managerEmployeeId)
            ->where('e_employment_status', config('constants.PROBATION_EMPLOYMENT_STATUS'))
            ->firstOrFail();

        try {
        
        \Log::info('ProbationAssessment store:start', [
            'employee_param' => $employeeId,
            'manager_employee_id' => session()->get('user_employee_id'),
            'payload_keys' => array_keys($request->all()),
        ]);

        $validated = $request->validate([
            'leave_in_probation' => 'nullable|numeric|min:0',
            'score.quality' => 'nullable|numeric|min:0|max:2',
            'remarks.quality' => 'nullable|string|max:500',
            'score.efficiency' => 'nullable|numeric|min:0|max:2',
            'remarks.efficiency' => 'nullable|string|max:500',
            'score.attendance' => 'nullable|numeric|min:0|max:2',
            'remarks.attendance' => 'nullable|string|max:500',
            // Combined Teamwork and Communication field
            'score.teamwork_communication' => 'nullable|numeric|min:0|max:2',
            'remarks.teamwork_communication' => 'nullable|string|max:500',
            'score.competency' => 'nullable|numeric|min:0|max:2',
            'remarks.competency' => 'nullable|string|max:500',
            'objectives_met' => 'required|in:Yes,No',
            'objectives_details' => 'nullable|string|max:1000',
            'training_addressed' => 'required|in:Yes,No',
            'training_details' => 'nullable|string|max:1000',
            'decision' => 'required|in:confirm,extend',
            'extend_months' => 'nullable|required_if:decision,extend|integer|min:1|max:24',
            'extend_upto_date' => 'nullable|required_if:decision,extend|date',
            'submit_status' => 'nullable|in:draft,submit',
        ]);
        \Log::info('ProbationAssessment store:validated', $validated);

        $assessment = ProbationAssessment::firstOrNew([
            'i_employee_id' => $employee->i_id,
            'i_manager_id' => $managerEmployeeId,
        ]);

        $assessment->fill([
            'i_employee_id' => $employee->i_id,
            'i_manager_id' => $managerEmployeeId,
            'i_leave_in_probation' => isset($validated['leave_in_probation']) ? (float)$validated['leave_in_probation'] : null,
            'i_quality_score' => data_get($validated, 'score.quality'),
            'vch_quality_remarks' => data_get($validated, 'remarks.quality'),
            'i_efficiency_score' => data_get($validated, 'score.efficiency'),
            'vch_efficiency_remarks' => data_get($validated, 'remarks.efficiency'),
            'i_attendance_score' => data_get($validated, 'score.attendance'),
            'vch_attendance_remarks' => data_get($validated, 'remarks.attendance'),
            // Save combined score to both teamwork and communication columns
            'i_teamwork_score' => data_get($validated, 'score.teamwork_communication'),
            'vch_teamwork_remarks' => data_get($validated, 'remarks.teamwork_communication'),
            'i_communication_score' => data_get($validated, 'score.teamwork_communication'),
            'vch_communication_remarks' => data_get($validated, 'remarks.teamwork_communication'),
            'i_teamwork_score' => data_get($validated, 'score.teamwork_communication'),
            'vch_teamwork_remarks' => data_get($validated, 'remarks.teamwork_communication'),
            'i_communication_score' => data_get($validated, 'score.teamwork_communication'),
            'vch_communication_remarks' => data_get($validated, 'remarks.teamwork_communication'),
            'i_competency_score' => data_get($validated, 'score.competency'),
            'vch_competency_remarks' => data_get($validated, 'remarks.competency'),
            'e_objectives_met' => $validated['objectives_met'],
            'vch_objectives_details' => $validated['objectives_details'] ?? null,
            'e_training_addressed' => $validated['training_addressed'],
            'vch_training_details' => $validated['training_details'] ?? null,
            'vch_decision' => $validated['decision'],
            'i_extend_months' => $validated['extend_months'] ?? null,
            'dt_extend_upto_date' => $validated['extend_upto_date'] ?? (
                $validated['decision'] === 'extend' && !empty($validated['extend_months']) 
                    ? \Carbon\Carbon::parse($employee->dt_probation_end_date)
                        ->addMonths($validated['extend_months'])
                        ->format('Y-m-d')
                    : null
            ),
            'vch_status' => ($request->input('submit_status') === 'submit') ? 'submitted' : 'draft',
            'i_created_by' => $assessment->exists ? $assessment->i_created_by : session()->get('user_id'),
            'i_updated_by' => session()->get('user_id'),
        ]);

        $assessment->save();
        \Log::info('ProbationAssessment store:saved', ['id' => $assessment->i_id]);

        // Send email notification if the form was submitted (not saved as draft)
        if ($request->input('submit_status') === 'submit') {
            \Log::info('Preparing to send probation assessment email', ['employee_id' => $employeeId]);
            
            $manager = EmployeeModel::find($managerEmployeeId);
            $employee = EmployeeModel::find($employeeId);
            
            \Log::info('Employee and manager data', [
                'employee_found' => $employee ? 'yes' : 'no',
                'outlook_email' => $employee ? $employee->v_outlook_email_id : 'not found',
                'manager_found' => $manager ? 'yes' : 'no'
            ]);
            
            if ($employee && !empty($employee->v_outlook_email_id)) {
                try {
                    $email = new ProbationAssessmentSubmitted(
                        $employee,
                        $manager ? $manager->v_employee_full_name : 'Your Manager',
                        $assessment
                    );
                    
                    \Log::info('Sending email to: ' . $employee->v_outlook_email_id);
                    
                    Mail::to($employee->v_outlook_email_id)->send($email);
                    
                    // Update employee's probation status based on the decision
                    $updateData = [];
                    
                    if ($assessment->vch_decision === 'confirm') {
                        $updateData = [
                            'e_in_probation' => 'No',
                            't_is_probation_completed' => 1,
                            'e_employment_status' => 'Confirmed'
                        ];
                        
                        // Log the status update
                        \Log::info('Updating employee status to Confirmed', [
                            'employee_id' => $employeeId,
                            'updates' => $updateData
                        ]);
                    } else {
                        // For extended probation or other statuses
                    $updateData = [
                        'e_in_probation' => 'Yes',
                        'dt_probation_end_date' => $assessment->dt_extend_upto_date,
                        'e_employment_status' => 'In Probation'
                    ];
                    
                    // Log the status update
                    \Log::info('Updating employee probation details', [
                        'employee_id' => $employeeId,
                        'updates' => $updateData
                    ]);
                    }
                    
                    // Update the employee record
                    $employee->update($updateData);
                    
                    \Log::info('Probation assessment email sent and employee record updated successfully', [
                        'employee_id' => $employeeId,
                        'email' => $employee->v_outlook_email_id,
                        'updates_applied' => $updateData
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send probation assessment email', [
                        'employee_id' => $employeeId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                \Log::warning('Cannot send email: employee not found or no email address', [
                    'employee_id' => $employeeId,
                    'has_email' => $employee && !empty($employee->v_outlook_email_id) ? 'yes' : 'no'
                ]);
            }
        }

        return redirect()->to(url('probation-assessments'))
            ->with('success', 'Probation assessment saved successfully');
        
        } catch (\Illuminate\Validation\ValidationException $ve) {
            \Log::error('ProbationAssessment store:validation_error', [ 'errors' => $ve->errors() ]);
            return redirect()->back()->withErrors($ve->errors())->withInput();
        } catch (\Throwable $e) {
            \Log::error('ProbationAssessment store:error', [ 'msg' => $e->getMessage(), 'trace' => $e->getTraceAsString() ]);
            return redirect()->back()->with('danger', 'Unexpected error while saving. Please try again.')->withInput();
        }
    }

    // Download as Excel (XLSX) with protection (read-only recommended)
    // Download as Excel (XLSX) with protection (read-only recommended)
    public function exportToExcel(Request $request)
    {
        $isAdmin = (session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN'));
        $managerEmployeeId = session()->get('user_employee_id');

        $query = EmployeeModel::with(['designationInfo', 'teamInfo'])
            ->where('t_is_deleted', 0)
            ->where('e_employment_status', config('constants.PROBATION_EMPLOYMENT_STATUS'))
            ->whereNotNull('dt_probation_end_date')
            ->whereBetween('dt_probation_end_date', [\Carbon\Carbon::now()->startOfDay(), \Carbon\Carbon::now()->addDays(30)->endOfDay()]);

        if (!$isAdmin) {
            $query->where('i_leader_id', $managerEmployeeId);
        }

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('v_employee_full_name', 'like', '%' . $search . '%')
                  ->orWhere('v_employee_code', 'like', '%' . $search . '%');
            });
        }

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'not_started') {
                $query->whereDoesntHave('probationAssessments');
            } else {
                $query->whereHas('probationAssessments', function($q) use ($request) {
                    $q->where('vch_status', $request->status);
                });
            }
        }

        $employees = $query->orderBy('v_employee_full_name', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'Employee Code');
        $sheet->setCellValue('B1', 'Employee Name');
        $sheet->setCellValue('C1', 'Department');
        $sheet->setCellValue('D1', 'Designation');
        $sheet->setCellValue('E1', 'Expected Confirmation Date');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8A1538']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Populate data
        $row = 2;
        foreach ($employees as $employee) {
            $confirmDate = !empty($employee->dt_probation_end_date) 
                ? \Carbon\Carbon::parse($employee->dt_probation_end_date)->format('Y-m-d')
                : '';

            $sheet->setCellValue('A' . $row, $employee->v_employee_code);
            $sheet->setCellValue('B' . $row, $employee->v_employee_full_name);
            $sheet->setCellValue('C' . $row, $employee->teamInfo->v_value ?? '');
            $sheet->setCellValue('D' . $row, $employee->designationInfo->v_value ?? '');
            $sheet->setCellValue('E' . $row, $confirmDate);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set cell borders
        $sheet->getStyle('A1:E' . ($row-1))->getBorders()
            ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Set header row height
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Create Excel file
        $writer = new XlsxWriter($spreadsheet);
        $fileName = 'probation-assessments-' . date('Y-m-d-His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }
    
public function exportXlsx($employeeId)
{
    // --- Permissions / Manager Logic ---
    $currentEmployee = EmployeeModel::select('i_id','i_role_permission')
        ->where('i_login_id', session()->get('user_id'))
        ->first();

    $isAdmin = (session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN'));

    if (!$isAdmin) {
        if (!$currentEmployee || (int)$currentEmployee->i_role_permission !== 4) {
            return redirect('page-not-found');
        }
    }

    $managerEmployeeId = session()->get('user_employee_id');

    // --- Fetch Employee + Assessment Data ---
    $employeeQuery = EmployeeModel::with([
        'designationInfo',
        'teamInfo',
        'leaderInfo',
        'subDesignationInfo' => function($q) {
            $q->select('i_id', 'v_sub_designation_name');
        }
    ])->where('i_id', $employeeId);

    if (!$isAdmin) {
        $employeeQuery->where('i_leader_id', $managerEmployeeId);
    }

    $employee = $employeeQuery->firstOrFail();

    if ($isAdmin) {
        $assessment = ProbationAssessment::where('i_employee_id', $employee->i_id)
            ->orderBy('created_at','desc')
            ->first();
    } else {
        $assessment = ProbationAssessment::where([
            'i_employee_id' => $employee->i_id,
            'i_manager_id' => $managerEmployeeId,
        ])->orderBy('created_at','desc')->first();
    }

    if (!$assessment || !in_array($assessment->vch_status, ['submitted','completed'])) {
        return redirect()->back()->with('danger', 'Excel is available only after the form is submitted.');
    }

    // --- Create Spreadsheet ---
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Probation Assessment');

    // --- Branding Colors ---
    $brandColor = '8A1538'; // maroon
    $headerFill = 'F3F3F3';
    $accentFill = 'E9ECEF';
    $borderGray = 'DDDDDD';

    $row = 1;

    // --- Title ---
    $sheet->mergeCells('A'.$row.':E'.$row);
    $sheet->setCellValue('A'.$row, 'Probation Assessment Report');
    $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(16)->getColor()->setARGB($brandColor);
    $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A'.$row.':E'.$row)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)->getColor()->setARGB($brandColor);
    $row += 2;

    // --- Employee Details Section ---
    $sheet->mergeCells('A'.$row.':E'.$row);
    $sheet->setCellValue('A'.$row, 'Employee Details');
    $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(12)->getColor()->setARGB($brandColor);
    $row++;

    $subDesignation = $employee->subDesignationInfo ? 
        $employee->subDesignationInfo->v_sub_designation_name : 
        'N/A';

    // Employee details with updated layout
    $sheet->fromArray([
        ['Employee Name', $employee->v_employee_full_name, 'Employee Code', $employee->v_employee_code],
        ['Department', data_get($employee, 'teamInfo.v_value', ''), 'Joining Date', $employee->dt_joining_date],
        ['Designation', data_get($employee, 'designationInfo.v_value', ''), 'Sub-Designation', $subDesignation],
        ['Probation End Date', $employee->dt_probation_end_date, '', '']
    ], null, 'A'.$row);

    $empStart = $row;
    $row += 5;

    // --- Summary Section ---
    $sheet->mergeCells('A'.$row.':E'.$row);
    $sheet->setCellValue('A'.$row, 'Summary');
    $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(12)->getColor()->setARGB($brandColor);
    $row++;

    $sheet->fromArray([
        ['Leave in Probation (days)', $assessment->i_leave_in_probation, 'Status', strtoupper($assessment->vch_status ?? '')],
        ['Decision', $assessment->vch_decision, 'Extended Months', $assessment->i_extend_months],
        ['Extended Till Date', $assessment->dt_extend_upto_date, '', ''],
    ], null, 'A'.$row);

    $summaryStart = $row;
    $row += 4;

    // --- Assessment Section ---
    $sheet->mergeCells('A'.$row.':E'.$row);
    $sheet->setCellValue('A'.$row, 'Assessment Details');
    $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(12)->getColor()->setARGB($brandColor);
    $row++;

    // Calculate overall score as sum of all 5 scores
    $scores = [
        'i_quality_score',
        'i_efficiency_score',
        'i_attendance_score',
        'i_teamwork_score',
        'i_communication_score'
    ];

    $totalScore = 0;
    foreach ($scores as $field) {
        if (isset($assessment->$field) && is_numeric($assessment->$field)) {
            $totalScore += (float)$assessment->$field;
        }
    }
    $overallScore = number_format($totalScore, 2);

    // Assessment data array
    $assessmentData = [
        ['Particular', 'Score', 'Remarks'],
        ['Quality and Accuracy of Work', $assessment->i_quality_score, $assessment->vch_quality_remarks],
        ['Work Efficiency', $assessment->i_efficiency_score, $assessment->vch_efficiency_remarks],
        ['Attendance & Time Keeping', $assessment->i_attendance_score, $assessment->vch_attendance_remarks],
        ['Team Work', $assessment->i_teamwork_score, $assessment->vch_teamwork_remarks],
        ['Communication Skills', $assessment->i_communication_score, $assessment->vch_communication_remarks],
        ['TOTAL SCORE', $overallScore, ''],
    ];

    // Add assessment data to sheet
    $sheet->fromArray($assessmentData, null, 'A'.$row);

    // Style the assessment table
    $lastRow = $row + count($assessmentData);
    $sheet->getStyle('A'.$row.':C'.$lastRow)->getBorders()->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle('A'.$row.':C'.$row)->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB($headerFill);
    $row = $lastRow + 2;

    // --- Objectives Section ---
    $sheet->mergeCells('A'.$row.':E'.$row);
    $sheet->setCellValue('A'.$row, 'Objectives and Training');
    $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(12)->getColor()->setARGB($brandColor);
    $row++;

    $objectivesData = [
        ['Have the Objectives identified for the probation period met?', $assessment->e_objectives_met ?? ''],
        ['Objectives Details (Remarks)', $assessment->vch_objectives_details ?? ''],
        ['Have the training/development needs for the probation period been addressed?', $assessment->e_training_addressed ?? ''],
        ['Training Details (Remarks)', $assessment->vch_training_details ?? '']
    ];

    // Add objectives data to sheet
    $sheet->fromArray($objectivesData, null, 'A'.$row);

    // Style the objectives table
    $objStart = $row;
    $objEnd = $row + count($objectivesData) - 1;
    $sheet->getStyle('A'.$objStart.':B'.$objEnd)->getBorders()->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    
    // Apply header style only to the first row (questions)
    $sheet->getStyle('A'.$objStart.':B'.$objStart)->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB($headerFill);
        
    // Make sure row 24 (if it exists in this section) has no fill
    $targetRow = 24;
    if ($objStart <= $targetRow && $targetRow <= $objEnd) {
        $sheet->getStyle('A'.$targetRow.':B'.$targetRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE);
    }
    
    $row = $objEnd + 2;

    // --- Manager's Comments ---
    if (!empty($assessment->vch_manager_comments)) {
        $sheet->mergeCells('A'.$row.':E'.$row);
        $sheet->setCellValue('A'.$row, 'Manager\'s Comments');
        $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(12)->getColor()->setARGB($brandColor);
        $row++;

        $sheet->mergeCells('A'.$row.':E'.$row);
        $sheet->setCellValue('A'.$row, $assessment->vch_manager_comments);
        $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(60);
        $row += 2;
    }

    // --- HR Comments ---
    if (!empty($assessment->vch_hr_comments)) {
        $sheet->mergeCells('A'.$row.':E'.$row);
        $sheet->setCellValue('A'.$row, 'HR Comments');
        $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(12)->getColor()->setARGB($brandColor);
        $row++;

        $sheet->mergeCells('A'.$row.':E'.$row);
        $sheet->setCellValue('A'.$row, $assessment->vch_hr_comments);
        $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(60);
        $row += 2;
    }

    // --- Set column widths ---
    $sheet->getColumnDimension('A')->setWidth(30);
    $sheet->getColumnDimension('B')->setWidth(25);
    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(30);

    // --- Set row heights and alignment for employee details ---
    for ($i = $empStart; $i < $empStart + 4; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(20);
    }

    // --- Set row heights and alignment for summary ---
    for ($i = $summaryStart; $i < $summaryStart + 3; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(20);
    }

    // --- Set protection ---
    $sheet->getProtection()->setSheet(true);
    $sheet->getStyle('A1:E1000')->getProtection()
        ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);

    // --- Set filename and headers ---
    $fileName = 'probation-assessment-' . $employee->v_employee_code . '-' . date('Y-m-d') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
}


    /**
     * Export a single employee's submitted probation assessment to PDF using mPDF
     */
    public function exportPdf($employeeId)
    {
        $currentEmployee = EmployeeModel::select('i_id','i_role_permission')
            ->where('i_login_id', session()->get('user_id'))
            ->first();

        $isAdmin = (session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN'));
        if (!$isAdmin) {
            if (!$currentEmployee || (int)$currentEmployee->i_role_permission !== 4) {
                return redirect('page-not-found');
            }
        }

        $managerEmployeeId = session()->get('user_employee_id');

        // Fetch employee and latest assessment
        $employeeQuery = EmployeeModel::with(['designationInfo','subDesignationInfo','teamInfo','leaderInfo'])
            ->where('i_id', $employeeId);
        if (!$isAdmin) {
            $employeeQuery->where('i_leader_id', $managerEmployeeId);
        }
        $employee = $employeeQuery->firstOrFail();

        if ($isAdmin) {
            $assessment = ProbationAssessment::where('i_employee_id', $employee->i_id)
                ->orderBy('created_at','desc')
                ->first();
        } else {
            $assessment = ProbationAssessment::where([
                'i_employee_id' => $employee->i_id,
                'i_manager_id' => $managerEmployeeId,
            ])->orderBy('created_at','desc')->first();
        }

        if (!$assessment || !in_array($assessment->vch_status, ['submitted','completed'])) {
            return redirect()->back()->with('danger', 'PDF is available only after the form is submitted.');
        }

        // Compute total score
        $totalScore =
            (float)($assessment->i_quality_score ?? 0) +
            (float)($assessment->i_efficiency_score ?? 0) +
            (float)($assessment->i_attendance_score ?? 0) +
            (float)($assessment->i_teamwork_score ?? 0) +
            (float)($assessment->i_competency_score ?? 0);

        // Render HTML from Blade
        $html = view('probations.pdf', [
            'employee' => $employee,
            'assessment' => $assessment,
            'generatedAt' => now(),
            'forPdf' => true,
            'totalScore' => $totalScore,
        ])->render();

        // Generate PDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 15,
            'margin_right' => 10,
            'margin_bottom' => 15,
            'margin_left' => 10,
        ]);
        // Make PDF non-editable (allow print only). Owner password prevents modifications.
        $ownerPassword = base64_encode($employee->v_employee_code . '|' . date('YmdHis'));
        $mpdf->SetProtection(['print'], '', $ownerPassword);
        $mpdf->SetTitle('Probation Assessment - ' . $employee->v_employee_code);
        $mpdf->WriteHTML($html);

        $fileName = 'probation-assessment-' . $employee->v_employee_code . '-' . date('Y-m-d') . '.pdf';
        return response($mpdf->Output($fileName, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }


}
