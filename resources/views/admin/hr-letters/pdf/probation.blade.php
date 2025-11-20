<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Probation Confirmation Letter</title>
    @php
        use Illuminate\Support\Facades\DB;
    @endphp
    <style>
        /* Set A4 page with correct margins */
        @page { size: A4; margin: 20mm 20mm 25mm 20mm; }

        html, body {
            margin: 0;
            padding: 0;
            background: #fff;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            line-height: 1.6;
        }

        /* Screen preview: center true A4 sheet */
        @media screen {
            body { background: #eee; }
            .sheet { width: 210mm; min-height: 297mm; margin: 0 auto; background: #fff; position: relative; box-shadow: 0 0 4mm rgba(0,0,0,.1); }
            .inner-page { padding: 55mm 20mm 25mm 20mm; }
            .letterhead-bg { display: block; }
            .print-bg { display: none; }
        }

        /* Dompdf/print: use fixed background so it repeats on every page */
        @media print {
            .sheet { width: 100%; min-height: 100%; position: relative; }
            .inner-page { padding: 55mm 20mm 25mm 20mm; }
            .letterhead-bg { display: none; }
            .print-bg { display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; }
            .print-bg img { width: 100%; height: 100%; object-fit: cover; }
        }

        /* Fallback default (in case media not applied) */
        .sheet { 
            width: 210mm; 
            min-height: 297mm; 
            margin: 0 auto; 
            background: #fff; 
            position: relative; 
            page-break-after: auto;
        }
        .inner-page { position: relative; z-index: 1; padding: 30mm 20mm 25mm 20mm; }
        .letterhead-bg { position: absolute; inset: 0; z-index: 0; }
        .letterhead-bg img { width: 100%; height: 100%; object-fit: cover; }

        h2 {
            text-align: center;
            font-size: 16px;
            margin: 20px 0;
        }
        
        p { 
            text-align: justify;
            margin: 10px 0;
        }
        
        .meta { 
            margin-bottom: 20px; 
            line-height: 1.6;
        }
        
        .signature { 
            margin-top: 40px;
        }
        
        .signature img { 
            height: 48px; 
            margin-top: 5px; 
        }
    </style>
</head>
<body>
@php
    $letterheadPath = public_path('letter-head/Acorn_Letterhead.png');
    $letterheadUrl = asset('letter-head/Acorn_Letterhead.png');
    $empName = $employee->v_employee_full_name ?? '';
    $shortName = $employee->v_first_name ?? strtok($empName, ' ');
    $empCode = $employee->v_employee_code ?? '';
    $department = data_get($employee, 'departmentInfo.v_name', '');
    $subDepartment = data_get($employee, 'subDepartmentInfo.v_name', '');
    $designation = $data['designation'] ?? (data_get($employee,'designationInfo.v_value') ?: (data_get($employee,'subDesignationInfo.v_name') ?: ''));
    $confirmDate = $data['confirmation_date'] ?? date('d-M-Y');
    $today = $data['date'] ?? date('d-M-Y');
    $probationEndDate = $employee->dt_probation_end_date ? \Carbon\Carbon::parse($employee->dt_probation_end_date)->format('d-M-Y') : '';
    
    // Get the letter number from data (passed from controller) or generate a default one
    $letterNo = $data['letter_no'] ?? null;
    
    if (!$letterNo) {
        $prefix = 'PRO';
        $year = date('Y');
        $month = date('m');
        
        // Get the latest sequence number for this month
        $latestLetter = DB::table('hr_letters')
            ->where('template', 'probation')
            ->where('letter_number', 'LIKE', "{$prefix}/{$year}/{$month}/%")
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = 1;
        if ($latestLetter && $latestLetter->letter_number) {
            // Extract the sequence number from the letter number (last 3 digits)
            $parts = explode('/', $latestLetter->letter_number);
            $lastPart = end($parts);
            $sequence = (int)$lastPart + 1;
        }
        
        $letterNo = sprintf('%s/%s/%s/%03d', $prefix, $year, $month, $sequence);
    } else {
        $letterNo = $letterNo ?? 'PRO/' . date('Y/m') . '/001';
    }
@endphp

<div class="sheet">
    @if(file_exists($letterheadPath))
        <div class="letterhead-bg"><img src="{{ $letterheadUrl }}" alt="Letterhead" /></div>
    @endif

    <div class="content-wrapper inner-page">
        <div class="meta">
            <div><strong>Letter No:</strong> {{ $letterNo }}</div>
            <div><strong>Date:</strong> {{ $today }}</div>
            <div><strong>Employee Code:</strong> {{ $empCode }}</div>
            @php
                $designation = data_get($employee, 'designationInfo.v_value', '');
                $subDesignation = data_get($employee, 'subDesignationInfo.v_sub_designation_name', '');
                $team = data_get($employee, 'teamInfo.v_value', '');
                $fullDesignation = $designation . ($subDesignation ? ' - ' . $subDesignation : '');
            @endphp
            @if(!empty($team))
            <div><strong>Department:</strong> {{ ucwords(strtoupper($team)) }}</div>
            @endif
                    @php
            $salutation = 'Mr.';
            if (isset($employee->e_gender) && $employee->e_gender === 'Female') {
                $salutation = (isset($employee->e_marital_status) && in_array($employee->e_marital_status, ['Married', 'Widow', 'Widow / Widower'])) ? 'Mrs.' : 'Ms.';
            }
        @endphp
        <div><strong>Name:</strong> {{ $salutation }} {{ ucwords(strtolower($empName)) }}</div>
        </div>



        <h2 style="text-transform: none; font-weight: bold;">Probation Confirmation Letter</h2>

        <p>Dear {{ ucwords(strtolower($employee->v_employee_name)) }},</p>

        <p>Congratulations! We are delighted to notify you that you have successfully completed your probationary period with <strong>Acorn Universal Consultancy LLP</strong>.</p>

        <p>Based on your exceptional performance and dedication during the probationary period, we are pleased to confirm your probation effective from <strong>{{ $probationEndDate }}</strong>.</p>

        <p>As a confirmed employee, you are now eligible for benefits of <strong>paid leaves</strong> and <strong>Mediclaim coverage</strong>. The paid leaves are designed to support your work-life balance, while the Mediclaim coverage ensures your medical and hospitalization needs are well-supported, offering peace of mind and security.</p>

        <p>Your contributions and commitment to our organization have been invaluable, and we are confident that you will continue to excel in your role.</p>

        <p>Once again, congratulations on successfully completing your probation.</p>

        <div class="signature">
            <div>For, <strong>Acorn Universal Consultancy LLP</strong></div>
            <img src="{{ asset('letter-head/sign.png') }}" alt="Authorized Signatory"style="height:28px; width:108px; margin:6px 0;" />
            <div><strong>Dr. Kishor Dholwani <br> (General Manager)</strong></div>
            
        </div>
    </div>
</div>
</body>
</html>
