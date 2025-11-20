@php
    use Illuminate\Support\Facades\DB;
    
    // Determine salutation based on gender and marital status
    $salutation = 'Mr.';
    if (isset($employee->e_gender) && $employee->e_gender === 'Female') {
        $salutation = (isset($employee->e_marital_status) && in_array($employee->e_marital_status, ['Married', 'Widow', 'Widow / Widower'])) ? 'Mrs.' : 'Ms.';
    }
    
    // Get full name in original case with salutation
    $empName = $employee->v_employee_full_name ?? '';
    $empName = $salutation . ' ' . ucwords(strtolower($empName));
    
    // Get employee name for salutation
    $salutationName = isset($employee->v_employee_name) ? ucwords(strtolower($employee->v_employee_name)) : (ucwords(strtolower($employee->v_first_name ?? strtok($empName, ' '))));
    $empCode = $employee->v_employee_code ?? '';
    $designation = ($data['designation'] ?? (data_get($employee,'designationInfo.v_value') ?: ''));
    $subDesignation = isset($employee->subDesignationInfo) ? ucwords(strtolower($employee->subDesignationInfo->v_sub_designation_name)) : null;
    $fullDesignation = ucwords(strtolower($designation)) . ($subDesignation ? ' - ' . $subDesignation : '');
    $fromDate = isset($data['from_date']) ? \Carbon\Carbon::parse($data['from_date'])->format('d-M-Y') : '';
    $toDate = isset($data['to_date']) ? \Carbon\Carbon::parse($data['to_date'])->format('d-M-Y') : '';
    $returnDate = isset($data['to_date']) ? \Carbon\Carbon::parse($data['to_date'])->addDay()->format('d-M-Y') : '';
    $purpose = $data['reason'] ?? 'personal travel';
    $today = isset($data['date']) ? \Carbon\Carbon::parse($data['date'])->format('d-M-Y') : date('d-M-Y');
    $joiningDate = isset($employee->dt_joining_date) ? \Carbon\Carbon::parse($employee->dt_joining_date)->format('d-M-Y') : 'N/A';
    
    // Get the letter number from data (passed from controller) or generate a default one
    $letterNo = $data['letter_no'] ?? null;
    
    if (!$letterNo) {
        $prefix = 'LVS';
        $year = date('Y');
        $month = date('m');
        
        // Get the latest sequence number for this month
        $latestLetter = DB::table('hr_letters')
            ->where('template', 'leave_sanction')
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
        $letterNo = $letterNo ?? 'LVS/' . date('Y/m') . '/001';
    }
    
    // Determine gender pronouns
    $gender = (isset($employee->e_gender) && $employee->e_gender === 'Female') ? 'She' : 'He';
    $possessive = (isset($employee->e_gender) && $employee->e_gender === 'Female') ? 'her' : 'his';
    
    $companyName = config('constants.COMPANY_NAME', 'Acorn Universal Consultancy LLP');
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Leave Sanction Letter</title>
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
            .content-wrapper { padding: 55mm 20mm 25mm 20mm; position: relative; z-index: 1; }
            .letterhead-bg { display: block; }
            .print-bg { display: none; }
        }

        /* Dompdf/print: use fixed background so it repeats on every page */
        @media print {
            .sheet { width: 100%; min-height: 100%; position: relative; }
            .content-wrapper { padding: 55mm 20mm 25mm 20mm; position: relative; z-index: 1; }
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
        .content-wrapper { position: relative; z-index: 1; padding: 30mm 20mm 25mm 20mm; }
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
<div class="sheet">
    @if(file_exists(public_path('letter-head/Acorn_Letterhead.png')))
        <div class="letterhead-bg">
            <img src="{{ asset('letter-head/Acorn_Letterhead.png') }}" alt="Letterhead" />
        </div>
        <div class="print-bg">
            <img src="{{ asset('letter-head/Acorn_Letterhead.png') }}" alt="Letterhead" />
        </div>
    @endif

    <div class="content-wrapper">
        <div class="meta">
            <div><strong>Letter No:</strong> {{ $letterNo }}</div>
            <div><strong>Date:</strong> {{ $today }}</div>
            <div><strong>Employee Code:</strong> {{ $empCode }}</div>
            <div><strong>Name:</strong> {{ $empName }}</div>
        </div>

        <h2>Leave Sanction</h2>

        <p>To Whom It May Concern,</p>

        <p>This is to inform that <strong> {{ $salutation }} </strong> <strong>{{ $employee->v_first_name ?? $salutationName }}</strong> is employed with <strong>{{ $companyName }}</strong> as <strong>{{ucwords($fullDesignation) }}</strong>. {{ $gender }} has been with our organization since <strong>{{ $joiningDate }}</strong> and is currently employed on a full-time basis.</p>

        <p>We have approved {{ $possessive }} leave from DD-MM-YYYY to DD-MM-YYYY for the purpose of {{ $purpose }}. {{ $gender }} is expected to return to work on <strong>{{ $returnDate }}</strong> and resume {{ $possessive }} duties as per company policy.</p>

        <div class="signature">
            <div>For, <strong>{{ $companyName }}</strong></div>
            @if(file_exists(public_path('letter-head/sign.png')))
                <img src="{{ asset('letter-head/sign.png') }}" alt="Authorized Signatory" style="height:28px; width:108px; margin:6px 0;"/>
            @endif
            <div><strong>Dr. Kishor Dholwani (General Manager)</strong></div>
        </div>
    </div>
</div>
</body>
</html>
