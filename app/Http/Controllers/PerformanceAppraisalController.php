<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\EmployeeModel;
use App\Models\Appraisal;
use App\Models\AppraisalRating;
use App\Models\JobAttribute;
use App\Models\JobRole;
use App\Models\AppraisalPeriod;
use App\LookupMaster;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;

class PerformanceAppraisalController extends Controller
{
    public function index(Request $request)
    {
        // Ensure only allowed users can access: role permission 8, employee i_id=106, or admin
        $currentEmployee = EmployeeModel::select('i_id','i_role_permission')
            ->where('i_login_id', session()->get('user_id'))
            ->first();
        $isAdmin = ( session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN') );
        if ( !$isAdmin && ( !$currentEmployee || ( (int)$currentEmployee->i_role_permission !== 4 ) ) ) {
            return redirect('page-not-found');
        }

        $managerEmployeeId = session()->get('user_employee_id');
        // Get the IDs of employees who report to the current user (L1 managers)
        $l1ManagerIds = EmployeeModel::where('i_leader_id', $managerEmployeeId)
            ->where('t_is_deleted', 0)
            ->pluck('i_id')
            ->toArray();
            
        $reportsQuery = EmployeeModel::with(['designationInfo','teamInfo'])
            ->where('t_is_deleted', 0)
            ->whereDate('dt_joining_date', '<=', '2025-09-30')
            ->whereIn('e_employment_status', ['In Probation', 'Confirmed'])
            ->where(function($query) use ($managerEmployeeId, $l1ManagerIds, $isAdmin) {
                // Show direct reports (for L1 managers)
                $query->where('i_leader_id', $managerEmployeeId);
                
                // Also show employees who report to the current user's direct reports (for L2 managers)
                if (!empty($l1ManagerIds)) {
                    $query->orWhereIn('i_leader_id', $l1ManagerIds);
                }
                
                // Admins can see all
                if ($isAdmin) {
                    $query->orWhereRaw('1=1');
                }
            });

        // Filters
        $search = trim((string)$request->get('search'));
        $filterDept = $request->get('department_id');
        $filterDesig = $request->get('designation_id');
        if ($search !== '') {
            $reportsQuery->where(function($q) use ($search){
                $q->orWhere('v_employee_full_name', 'like', "%$search%")
                  ->orWhere('v_employee_code', 'like', "%$search%");
            });
        }
        if (!empty($filterDept)) {
            $reportsQuery->where('i_team_id', $filterDept);
        }
        if (!empty($filterDesig)) {
            $reportsQuery->where('i_designation_id', $filterDesig);
        }
        $reports = $reportsQuery->orderBy('v_employee_full_name', 'asc')->paginate(25);

        // Build filter option datasets
        $deptIds = EmployeeModel::where('t_is_deleted', 0)
            ->whereNotNull('i_team_id')
            ->pluck('i_team_id')->unique()->filter()->values()->all();
        $desigIds = EmployeeModel::where('t_is_deleted', 0)
            ->whereNotNull('i_designation_id')
            ->pluck('i_designation_id')->unique()->filter()->values()->all();
        $departments = [];
        $designations = [];
        if (!empty($deptIds)) {
            $departments = LookupMaster::whereIn('i_id', $deptIds)->orderBy('v_value','asc')->get(['i_id','v_value']);
        }
        if (!empty($desigIds)) {
            $designations = LookupMaster::whereIn('i_id', $desigIds)->orderBy('v_value','asc')->get(['i_id','v_value']);
        }

        // Build maps for appraisal status and scores for current period
        $period = AppraisalPeriod::where('vch_name', 'April 2025 - March 2026')->first();
        $statusMap = [];
        $scoresMap = [];
        
        if ($period && $reports->count() > 0) {
            $empIds = collect($reports->items())->pluck('i_id')->all();
            
            // Get appraisals with their ratings
            $appraisals = Appraisal::with('ratings')
                ->whereIn('i_employee_id', $empIds)
                ->where('i_period_id', $period->i_id)
                ->get();
            
            foreach ($appraisals as $appraisal) {
                $empId = (int)$appraisal->i_employee_id;
                $statusMap[$empId] = $appraisal->vch_status;
                
                // Calculate average score if ratings exist
                if ($appraisal->ratings->isNotEmpty()) {
                    $avgScore = $appraisal->ratings->avg('i_rating');
                    // Convert 1-5 scale to 0-100 scale
                    $scoresMap[$empId] = round(($avgScore / 5) * 100, 2);
                }
            }
        }

        $data = [];
        $data['pageTitle'] = 'Performance Appraisals';
        $data['reports'] = $reports;
        $data['appraisalStatusByEmp'] = $statusMap;
        $data['scoresMap'] = $scoresMap;
        $data['isAdmin'] = $isAdmin;
        $data['departments'] = $departments;
        $data['designations'] = $designations;
        $data['selected'] = [
            'search' => $search,
            'department_id' => $filterDept,
            'designation_id' => $filterDesig,
        ];
        return view('appraisals.index', $data);
    }

    public function show($employeeId)
    {
        $currentEmployee = EmployeeModel::select('i_id', 'i_role_permission')
            ->where('i_login_id', session()->get('user_id'))
            ->first();
            
        $isAdmin = (session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN'));
        $currentEmployeeId = session()->get('user_employee_id');
        
        // Get the employee and their direct manager (L1)
        $employee = EmployeeModel::with(['designationInfo', 'teamInfo', 'leaderInfo'])
            ->where('i_id', $employeeId)
            ->first();
            
        if (!$employee) {
            return redirect()->back()->with('danger', 'Employee not found.');
        }
        
        // Check access rights
        $isDirectManager = ($employee->i_leader_id == $currentEmployeeId);
        $isL2Manager = false;
        $isViewOnly = false;
        
        // Check if current user is the L2 manager (manager of the manager)
        if (!$isAdmin && !$isDirectManager) {
            $l1Manager = EmployeeModel::find($employee->i_leader_id);
            if ($l1Manager && $l1Manager->i_leader_id == $currentEmployeeId) {
                $isL2Manager = true;
                $isViewOnly = true; // L2 can only view, not edit
            } else if (!$isAdmin) {
                return redirect('page-not-found');
            }
        }

        $period = AppraisalPeriod::where('vch_name', 'April 2025 - March 2026')->first();
        // Attributes
        $attributes = JobAttribute::where('i_status', 1)->orderBy('i_display_order','asc')->get();
        // Manager-defined Job Roles (per appraisal)
        // Loaded later after we ensure $appraisal

        // If appraisal exists, fetch ratings
        $appraisalQuery = Appraisal::where([
            'i_employee_id' => $employee->i_id,
            'i_period_id' => optional($period)->i_id,
        ]);
        
        // For L2 managers, only show submitted/completed appraisals
        if ($isL2Manager) {
            $appraisalQuery->whereIn('vch_status', ['submitted', 'completed']);
        }
        // For L1 managers, only show their own appraisals
        else if (!$isAdmin) {
            $appraisalQuery->where('i_manager_id', $currentEmployeeId);
        }
        
        // Prefer submitted/completed, else any latest
        $appraisal = $appraisalQuery
            ->orderByRaw("FIELD(vch_status, 'submitted', 'completed', 'draft') ASC")
            ->latest('i_id')
            ->first();
            
        // If L2 manager is trying to view but no submitted appraisal exists
        if ($isL2Manager && !$appraisal) {
            return redirect()->back()->with('danger', 'The appraisal form has not been submitted yet.');
        }

        $existingRatings = [
            'attribute' => [],
            'job_role' => []
        ];
        if ($appraisal) {
            $ratings = AppraisalRating::where('i_appraisal_id', $appraisal->i_id)->get();
            foreach ($ratings as $r) {
                $existingRatings[$r->vch_type][$r->i_reference_id] = $r->i_rating;
            }
        }

        // Fetch manager-defined role items for this appraisal
        $roleItems = collect();
        if ($appraisal) {
            $roleItems = DB::table('appraisal_role_items')
                ->where('i_appraisal_id', $appraisal->i_id)
                ->orderBy('i_display_order', 'asc')
                ->get();
        }

        // Calculate overall score for submitted/completed appraisals (shown to Admin and reporting manager)
        $overallScore = null;
        if ($appraisal && in_array($appraisal->vch_status, ['submitted', 'completed'])) {
            // Attributes score (max 50)
            $attrIds = $attributes->pluck('i_id')->all();
            $attrRatingsSelected = 0;
            $attrRatingsSum = 0;
            foreach ($attrIds as $aid) {
                if (isset($existingRatings['attribute'][$aid])) {
                    $attrRatingsSelected++;
                    $attrRatingsSum += (int)$existingRatings['attribute'][$aid];
                }
            }
            $attrScore = 0.0;
            if ($attrRatingsSelected > 0) {
                $attrAvg = $attrRatingsSum / $attrRatingsSelected; // 1..5
                $attrScore = ($attrAvg / 5.0) * 50.0;
            }

            // Roles score (max 50)
            $roleScore = 0.0;
            $roleCount = ($roleItems ? $roleItems->count() : 0);
            if ($roleCount > 0) {
                $roleSum = 0;
                $roleSelected = 0;
                foreach ($roleItems as $ri) {
                    $rid = isset($ri->i_id) ? $ri->i_id : (isset($ri->id) ? $ri->id : null);
                    if ($rid !== null && isset($existingRatings['job_role'][$rid])) {
                        $roleSelected++;
                        $roleSum += (int)$existingRatings['job_role'][$rid];
                    }
                }
                if ($roleSelected > 0) {
                    $roleAvg = $roleSum / $roleSelected;
                    $roleScore = ($roleAvg / 5.0) * 50.0;
                }
            }
            $overallScore = round($attrScore + $roleScore, 2);
        }

        $data = [];
        $data['pageTitle'] = 'Performance Appraisal - Form';
        $data['employee'] = $employee;
        $data['period'] = $period;
        $data['attributes'] = $attributes;
        $data['roleItems'] = $roleItems;
        $data['appraisal'] = $appraisal;
        $data['existingRatings'] = $existingRatings;
        // Set readOnly mode for admin, L2 managers, or submitted/completed appraisals
        $data['readOnly'] = ($isAdmin || $isViewOnly || ($appraisal && in_array($appraisal->vch_status, ['submitted','completed'])));
        $data['managerName'] = optional($employee->leaderInfo)->v_employee_full_name;
        $data['isAdmin'] = $isAdmin;
        if ($overallScore !== null) { $data['overallScore'] = $overallScore; }

        return view('appraisals.form', $data);
    }

    public function exportToExcel(Request $request)
    {
        $isAdmin = (session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN'));
        $currentEmployeeId = session()->get('user_employee_id');
        
        // Get the same data as the index method
        $search = $request->input('search');
        $filterDept = $request->input('department_id');
        $filterDesig = $request->input('designation_id');
        
        // Build the base query
        $reports = EmployeeModel::with(['designationInfo', 'teamInfo', 'leaderInfo'])
            ->where('t_is_deleted', 0);
            
        // Apply filters
        if (!empty($search)) {
            $reports->where(function($q) use ($search) {
                $q->where('v_employee_full_name', 'like', '%' . $search . '%')
                  ->orWhere('v_employee_code', 'like', '%' . $search . '%');
            });
        }
        
        if (!empty($filterDept)) {
            $reports->where('i_team_id', $filterDept);
        }
        
        if (!empty($filterDesig)) {
            $reports->where('i_designation_id', $filterDesig);
        }
        
        // For non-admin users, only show their direct reports or L2 reports
        if (!$isAdmin) {
            $l1ManagerIds = EmployeeModel::where('i_leader_id', $currentEmployeeId)
                ->where('t_is_deleted', 0)
                ->pluck('i_id')
                ->toArray();
                
            $allManagerIds = array_merge([$currentEmployeeId], $l1ManagerIds);
            $reports->where(function($query) use ($allManagerIds) {
                $query->whereIn('i_leader_id', $allManagerIds);
            });
        }
        
        $reports = $reports->orderBy('v_employee_full_name', 'asc')->get();
        
        // Get appraisal data
        $period = AppraisalPeriod::where('vch_name', 'April 2025 - March 2026')->first();
        $scoresMap = [];
        
        if ($period && $reports->count() > 0) {
            $appraisals = Appraisal::with('ratings')
                ->whereIn('i_employee_id', $reports->pluck('i_id'))
                ->where('i_period_id', $period->i_id)
                ->get();
                
            foreach ($appraisals as $appraisal) {
                if ($appraisal->ratings->isNotEmpty()) {
                    $avgScore = $appraisal->ratings->avg('i_rating');
                    $scoresMap[$appraisal->i_employee_id] = round(($avgScore / 5) * 100, 2);
                }
            }
        }
        
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('HRMS')
            ->setTitle('Performance Appraisals Export - ' . date('Y-m-d'));
            
        // Add headers
        $headers = [
            'Employee Code',
            'Employee Name',
            'Department',
            'Designation',
            'Score',
            'Status'
        ];
        
        // Set header row
        $sheet->fromArray($headers, null, 'A1');
        
        // Style header row
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9D9D9'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A1:' . chr(65 + count($headers) - 1) . '1')->applyFromArray($headerStyle);
        
        // Add data rows
        $row = 2;
        foreach ($reports as $emp) {
            $status = isset($scoresMap[$emp->i_id]) ? 'Completed' : 'Pending';
            $score = $scoresMap[$emp->i_id] ?? '-';
            
            $sheet->setCellValue('A' . $row, $emp->v_employee_code);
            $sheet->setCellValue('B' . $row, $emp->v_employee_full_name);
            $sheet->setCellValue('C' . $row, optional($emp->teamInfo)->v_value);
            $sheet->setCellValue('D' . $row, optional($emp->designationInfo)->v_value);
            $sheet->setCellValue('E' . $row, $score);
            $sheet->setCellValue('F' . $row, $status);
            
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Center align score column
        $sheet->getStyle('E2:E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Set the file name and create the writer
        $fileName = 'performance_appraisals_' . date('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        // Set the headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        // Save the file to PHP output
        $writer->save('php://output');
        exit;
    }
    
    public function store(Request $request, $employeeId)
    {
        // permission gate
        $currentEmployee = EmployeeModel::select('i_id','i_role_permission')
            ->where('i_login_id', session()->get('user_id'))
            ->first();
        $isAdmin = ( session()->has('role') && session()->get('role') == config('constants.ROLE_ADMIN') );
        if (!$currentEmployee || ( !$isAdmin && ((int)$currentEmployee->i_role_permission !== 4) ) ) {
            return redirect('page-not-found');
        }
        // Admin cannot edit
        if ($isAdmin) {
            return redirect()->back()->with('danger', 'Admin can only view the appraisal form.');
        }

        $managerEmployeeId = session()->get('user_employee_id');
        $employee = EmployeeModel::where('i_id', $employeeId)
            ->where('i_leader_id', $managerEmployeeId)
            ->firstOrFail();

        $period = AppraisalPeriod::where('vch_name', 'April 2025 - March 2026')->firstOrFail();

        // Create or get appraisal and block edits if already submitted/completed
        $appraisal = Appraisal::where([
            'i_employee_id' => $employee->i_id,
            'i_manager_id' => $managerEmployeeId,
            'i_period_id' => $period->i_id,
        ])->first();
        if ($appraisal && in_array($appraisal->vch_status, ['submitted','completed'])) {
            return redirect()->back()->with('danger', 'This appraisal has already been submitted and cannot be edited.');
        }
        if (!$appraisal) {
            $appraisal = Appraisal::create([
                'i_employee_id' => $employee->i_id,
                'i_manager_id' => $managerEmployeeId,
                'i_period_id' => $period->i_id,
                'vch_status' => 'draft',
                'i_created_by' => session()->get('user_id'),
            ]);
        }

        // Save role items only
        if ($request->input('submit_status') === 'save_roles') {
            $rolesInput = array_map('trim', (array)$request->input('new_roles', []));
            $rolesInput = array_slice($rolesInput, 0, 5);
            // Filter to non-empty
            $nonEmpty = array_values(array_filter($rolesInput, function($v){ return $v !== ''; }));
            if (count($nonEmpty) !== 5) {
                return redirect()->back()->with('danger', 'Please provide exactly 5 roles to proceed.');
            }

            DB::table('appraisal_role_items')->where('i_appraisal_id', $appraisal->i_id)->delete();
            $now = now();
            $bulk = [];
            $order = 1;
            foreach ($nonEmpty as $roleText) {
                $bulk[] = [
                    'i_appraisal_id' => $appraisal->i_id,
                    'vch_role' => mb_substr($roleText, 0, 255),
                    'i_display_order' => $order++,
                    'dt_created_at' => $now,
                    'dt_updated_at' => $now,
                ];
            }
            DB::table('appraisal_role_items')->insert($bulk);
            return redirect()->back()->with('success', 'Job roles saved successfully.');
        }

        // Save ratings and status
        $attrRatings = (array) $request->input('attribute', []);
        foreach ($attrRatings as $refId => $rating) {
            $rating = (int)$rating;
            if ($rating >= 1 && $rating <= 5) {
                AppraisalRating::updateOrCreate(
                    [
                        'i_appraisal_id' => $appraisal->i_id,
                        'vch_type' => 'attribute',
                        'i_reference_id' => (int)$refId,
                    ],
                    [ 'i_rating' => $rating ]
                );
            }
        }

        $roleRatings = (array) $request->input('job_role', []);
        foreach ($roleRatings as $refId => $rating) {
            $rating = (int)$rating;
            if ($rating >= 1 && $rating <= 5) {
                AppraisalRating::updateOrCreate(
                    [
                        'i_appraisal_id' => $appraisal->i_id,
                        'vch_type' => 'job_role',
                        'i_reference_id' => (int)$refId,
                    ],
                    [ 'i_rating' => $rating ]
                );
            }
        }

        $submitStatus = $request->input('submit_status');
        if ($submitStatus === 'submit') {
            $appraisal->vch_status = 'submitted';
            $appraisal->dt_submitted_at = now();
        } else {
            $appraisal->vch_status = 'draft';
        }
        $appraisal->i_updated_by = session()->get('user_id');
        $appraisal->save();

        return redirect()->to(url('performance-appraisals'))
            ->with('success', 'Appraisal saved successfully');
    }
}
