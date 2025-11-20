<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmployeeModel;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\View;

class HrLetterController extends Controller
{
    // List active employees with department and action
    public function index(Request $request)
    {
        $pageTitle = 'HR Letters';
        $employeeModel = new EmployeeModel();
        $employees = $employeeModel->getRecordDetails(
            [
                'delete_check' => 0,
                'order_by' => ['v_employee_full_name' => 'ASC']
            ]
        );
        return view('admin.hr-letters.index', compact('pageTitle', 'employees'));
    }

    // Show list of available letter templates for an employee
    public function employeeLetters($employeeId)
    {
        $pageTitle = 'HR Letters';
        $employee = (new EmployeeModel())->getRecordDetails(['master_id' => $employeeId, 'singleRecord' => true]);
        abort_if(!$employee, 404);

        // Define available templates (keys map to Blade partials under admin/hr-letters/templates)
        $templates = [
            [ 'key' => 'appointment', 'name' => 'Appointment Letter' ],
            [ 'key' => 'probation', 'name' => 'Probation Confirmation Letter' ],
            [ 'key' => 'transfer', 'name' => 'Department Transfer Letter' ],
            [ 'key' => 'relieving', 'name' => 'Relieving Letter' ],
            [ 'key' => 'internship', 'name' => 'Internship Letter' ],
            [ 'key' => 'experience', 'name' => 'Experience Letter' ],
            [ 'key' => 'leave_sanction', 'name' => 'Leave Sanction Letter' ],
            [ 'key' => 'suspension', 'name' => 'Suspension Letter' ],
            [ 'key' => 'appraisal', 'name' => 'Appraisal Letter' ],
            [ 'key' => 'full_and_final', 'name' => 'Full and Final Settlement Letter' ],
            [ 'key' => 'other', 'name' => 'Other Letter' ],
        ];
        return view('admin.hr-letters.employee', compact('pageTitle', 'employee', 'templates'));
    }

    // Return HTML for the selected letter template form (for modal)
    public function renderTemplate(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'template' => 'required|string',
        ]);
        $employee = (new EmployeeModel())->getRecordDetails(['master_id' => $request->employee_id, 'singleRecord' => true]);
        abort_if(!$employee, 404);

        $template = $request->template;
        $viewName = 'admin.hr-letters.templates.' . $template;
        if (!View::exists($viewName)) {
            abort(404);
        }
        $html = view($viewName, compact('employee'))->render();
        return response()->json(['status' => 1, 'html' => $html]);
    }

    // Generate the PDF with filled details
    public function generatePdf(Request $request)
    {
        @set_time_limit(120);
        $request->validate([
            'employee_id' => 'required|integer',
            'template' => 'required_without_all:html,letter_id|string',
            'letter_id' => 'nullable|integer',
        ]);
        $employee = (new EmployeeModel())->getRecordDetails(['master_id' => $request->employee_id, 'singleRecord' => true]);
        abort_if(!$employee, 404);

        // If a letter record is provided, enforce approval before download
        $letterId = (int) $request->get('letter_id', 0);
        if ($letterId > 0) {
            $letter = DB::table('hr_letters')->where('id', $letterId)->first();
            abort_unless($letter, 404);
            // Only creator or approver/admin can view; and must be Approved for download
            $currentUserId = (int) session()->get('user_id');
            if (!in_array(session()->get('role'), [config('constants.ROLE_ADMIN')]) && $currentUserId !== (int) $letter->created_by && $currentUserId !== (int) $letter->approver_id) {
                abort(403);
            }
            if (strcasecmp($letter->status, 'Approved') !== 0) {
                abort(403, 'Letter is not approved yet.');
            }
            $filename = 'HR-Letter-' . ($letter->template ?: 'custom') . '-' . ($employee->v_employee_full_name ?? 'Employee') . '.pdf';
            $html = $letter->html ?: '';
            if ($html === '') {
                // fallback render from stored data_json + template
                $data = json_decode($letter->data_json ?: '{}', true);
                $viewName = 'admin.hr-letters.pdf.' . $letter->template;
                abort_unless(View::exists($viewName), 404);
                $html = view($viewName, ['employee' => $employee, 'data' => $data])->render();
            }
            // Ensure letterhead and signature are embedded for Dompdf
            try {
                $lhPath = public_path('letter-head/Acorn_Letterhead.png');
                $signPath = public_path('letter-head/sign.png');
                if (file_exists($lhPath)) {
                    $dataUrl = 'data:image/png;base64,' . base64_encode(@file_get_contents($lhPath));
                    $assetUrl = asset('letter-head/Acorn_Letterhead.png');
                    $html = str_replace(["src=\"$assetUrl\"", "src='$assetUrl'", 'src="/letter-head/Acorn_Letterhead.png"', "src='/letter-head/Acorn_Letterhead.png'"],
                        'src="' . $dataUrl . '"', $html);
                }
                if (file_exists($signPath)) {
                    $signDataUrl = 'data:image/png;base64,' . base64_encode(@file_get_contents($signPath));
                    $signAssetUrl = asset('letter-head/sign.png');
                    $html = str_replace(["src=\"$signAssetUrl\"", "src='$signAssetUrl'", 'src="/letter-head/sign.png"', "src='/letter-head/sign.png'"],
                        'src="' . $signDataUrl . '"', $html);
                }
            } catch (\Throwable $e) {}

            // Fallback if GD not available in web SAPI: strip images to avoid Dompdf fatal
            if (!extension_loaded('gd')) {
                $html = preg_replace('/<img[^>]*>/i', '', $html);
            }
            $pdf = PDF::loadHTML($html)
                ->setPaper('a4')
                ->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                ]);
            return $pdf->download($filename);
        }

        // Otherwise approval is required; allow Admin to bypass
        if (!in_array(session()->get('role'), [config('constants.ROLE_ADMIN')])) {
            abort(422, 'Please submit for approval and download after approval.');
        }

        // Admin bypass path
        $data = $request->all();
        $template = $request->get('template');
        $filename = 'HR-Letter-' . ($template ?: 'custom') . '-' . ($employee->v_employee_full_name ?? 'Employee') . '.pdf';
        if ($request->filled('html')) {
            $html = $request->get('html');
            try {
                $lhPath = public_path('letter-head/Acorn_Letterhead.png');
                $signPath = public_path('letter-head/sign.png');
                if (file_exists($lhPath)) {
                    $dataUrl = 'data:image/png;base64,' . base64_encode(@file_get_contents($lhPath));
                    $assetUrl = asset('letter-head/Acorn_Letterhead.png');
                    $html = str_replace(["src=\"$assetUrl\"", "src='$assetUrl'", 'src="/letter-head/Acorn_Letterhead.png"', "src='/letter-head/Acorn_Letterhead.png'"],
                        'src="' . $dataUrl . '"', $html);
                }
                if (file_exists($signPath)) {
                    $signDataUrl = 'data:image/png;base64,' . base64_encode(@file_get_contents($signPath));
                    $signAssetUrl = asset('letter-head/sign.png');
                    $html = str_replace(["src=\"$signAssetUrl\"", "src='$signAssetUrl'", 'src="/letter-head/sign.png"', "src='/letter-head/sign.png'"],
                        'src="' . $signDataUrl . '"', $html);
                }
            } catch (\Throwable $e) {}
            $pdf = PDF::loadHTML($html)
                ->setPaper('a4')
                ->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                ]);
            return $pdf->download($filename);
        }
        $viewName = 'admin.hr-letters.pdf.' . $template;
        abort_unless(View::exists($viewName), 404);
        $html = view($viewName, compact('employee', 'data'))->render();
        try {
            $lhPath = public_path('letter-head/Acorn_Letterhead.png');
            $signPath = public_path('letter-head/sign.png');
            if (file_exists($lhPath)) {
                $dataUrl = 'data:image/png;base64,' . base64_encode(@file_get_contents($lhPath));
                $assetUrl = asset('letter-head/Acorn_Letterhead.png');
                $html = str_replace(["src=\"$assetUrl\"", "src='$assetUrl'", 'src="/letter-head/Acorn_Letterhead.png"', "src='/letter-head/Acorn_Letterhead.png'"],
                    'src="' . $dataUrl . '"', $html);
            }
            if (file_exists($signPath)) {
                $signDataUrl = 'data:image/png;base64,' . base64_encode(@file_get_contents($signPath));
                $signAssetUrl = asset('letter-head/sign.png');
                $html = str_replace(["src=\"$signAssetUrl\"", "src='$signAssetUrl'", 'src="/letter-head/sign.png"', "src='/letter-head/sign.png'"],
                    'src="' . $signDataUrl . '"', $html);
            }
        } catch (\Throwable $e) {}
        if (!extension_loaded('gd')) {
            $html = preg_replace('/<img[^>]*>/i', '', $html);
        }
        $pdf = PDF::loadHTML($html)
            ->setPaper('a4')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ]);
        return $pdf->download($filename);
    }

    // Build HTML preview from submitted form data
    public function previewLetter(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'template' => 'required|string',
        ]);
        $employee = (new EmployeeModel())->getRecordDetails(['master_id' => $request->employee_id, 'singleRecord' => true]);
        abort_if(!$employee, 404);

        $template = $request->template;
        $viewName = 'admin.hr-letters.pdf.' . $template;
        if (!View::exists($viewName)) {
            return response()->json(['status' => 0, 'message' => 'Template not found'], 404);
        }
        $data = $request->all();
        $html = view($viewName, compact('employee', 'data'))->render();
        return response()->json(['status' => 1, 'html' => $html]);
    }

    public function submitForApproval(Request $request)

    {

        $request->validate([

            'employee_id' => 'required|integer',

            'template' => 'required|string',

        ]);

        $employee = (new EmployeeModel())->getRecordDetails(['master_id' => $request->employee_id, 'singleRecord' => true]);

        abort_if(!$employee, 404);



        $currentUserId = (int) session()->get('user_id');

        $approverId = 751; // fixed per requirements



        // Prefer edited HTML if provided; otherwise render from template and data

        $html = $request->get('html');

        if (empty($html)) {

            $viewName = 'admin.hr-letters.pdf.' . $request->template;

            abort_unless(View::exists($viewName), 404);

            $data = $request->all();

            $html = view($viewName, compact('employee', 'data'))->render();

        }



        $letterId = DB::table('hr_letters')->insertGetId([

            'employee_id' => (int) $request->employee_id,

            'created_by' => $currentUserId,

            'approver_id' => $approverId,

            'template' => $request->template,

            'status' => 'Pending',

            'data_json' => json_encode($request->all()),

            'html' => $html,

            'created_at' => now(),

            'updated_at' => now(),

        ]);



        return response()->json(['status' => 1, 'message' => 'Submitted for approval', 'letter_id' => $letterId]);

    }
    public function inbox(Request $request)
    {
        $currentUserId = (int) session()->get('user_id');
        // Only approvers (751, 323) or admin can view inbox
        abort_unless(in_array($currentUserId, [751, 323]) || in_array(session()->get('role'), [config('constants.ROLE_ADMIN')]), 403);

        if ($currentUserId === 751) {
            // Stage 1 approval inbox
            $records = DB::table('hr_letters')
                ->where('approver_id', 751)
                ->where('status', 'Pending')
                ->orderByDesc('id')
                ->get();
            $mode = 'approver1';
        } elseif ($currentUserId === 323) {
            // Stage 2 approval inbox
            $records = DB::table('hr_letters')
                ->where('approver_id', 323)
                ->where('status', 'Pending-Stage2')
                ->orderByDesc('id')
                ->get();
            $mode = 'approver2';
        } else {
            // Admin view: show approved letters ready for download
            $records = DB::table('hr_letters')
                ->where('status', 'Approved')
                ->orderByDesc('approved_at')
                ->get();
            $mode = 'admin';
        }

        return view('admin.hr-letters.inbox', [
            'pageTitle' => 'HR Letters - Approvals',
            'records' => $records,
            'inboxMode' => $mode,
        ]);
    }

    public function approve($id)
    {
        $currentUserId = (int) session()->get('user_id');
        abort_unless(in_array($currentUserId, [751, 323]) || in_array(session()->get('role'), [config('constants.ROLE_ADMIN')]), 403);

        $letter = DB::table('hr_letters')->where('id', (int)$id)->first();
        abort_unless($letter, 404);

        $payload = ['updated_at' => now()];
        if (request()->filled('html')) {
            $payload['html'] = request()->get('html');
        }

        if ($currentUserId === 751 && $letter->status === 'Pending') {
            // Move to stage 2
            $payload['status'] = 'Pending-Stage2';
            $payload['approver_id'] = 323;
        } elseif ($currentUserId === 323 && $letter->status === 'Pending-Stage2') {
            // Final approval
            $payload['status'] = 'Approved';
            $payload['approved_at'] = now();
        } elseif (in_array(session()->get('role'), [config('constants.ROLE_ADMIN')])) {
            // Admin override to directly approve
            $payload['status'] = 'Approved';
            $payload['approved_at'] = now();
        } else {
            return response()->json(['status' => 0]);
        }

        $updated = DB::table('hr_letters')->where('id', (int)$id)->update($payload);
        return response()->json(['status' => $updated ? 1 : 0]);
    }

    public function reject($id)
    {
        $currentUserId = (int) session()->get('user_id');
        abort_unless(in_array($currentUserId, [751, 323]) || in_array(session()->get('role'), [config('constants.ROLE_ADMIN')]), 403);
        $letter = DB::table('hr_letters')->where('id', (int)$id)->first();
        abort_unless($letter, 404);
        // Only current approver (matching approver_id) or admin can reject
        if ($currentUserId !== (int)$letter->approver_id && !in_array(session()->get('role'), [config('constants.ROLE_ADMIN')])) {
            return response()->json(['status' => 0]);
        }
        $updated = DB::table('hr_letters')->where('id', (int)$id)->update([
            'status' => 'Rejected',
            'updated_at' => now(),
        ]);
        return response()->json(['status' => $updated ? 1 : 0]);
    }

    public function getLetterHtml($id)
    {
        $currentUserId = (int) session()->get('user_id');
        abort_unless(in_array($currentUserId, [751, 323]) || in_array(session()->get('role'), [config('constants.ROLE_ADMIN')]), 403);
        $letter = DB::table('hr_letters')->where('id', (int)$id)->first();
        abort_unless($letter, 404);
        return response()->json(['status' => 1, 'html' => $letter->html]);
    }
}
