<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Relieving Letter</title>
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
    </style>
</head>
<body>
@php
    $letterheadPath = public_path('letter-head/Acorn_Letterhead.png');
    $letterheadUrl = asset('letter-head/Acorn_Letterhead.png');
    $empName = $employee->v_employee_full_name ?? '';
    // Determine salutation based on gender and marital status
    $salutation = 'Mr.';
    if (isset($employee->e_gender) && $employee->e_gender === 'Female') {
        $salutation = (isset($employee->e_marital_status) && in_array($employee->e_marital_status, ['Married', 'Widow', 'Widow / Widower'])) ? 'Mrs.' : 'Ms.';
    }
    
    // Get designation and subdesignation
    $designation = data_get($employee, 'designationInfo.v_value', '');
    $subDesignation = isset($employee->subDesignationInfo) ? ucwords(strtolower($employee->subDesignationInfo->v_sub_designation_name)) : null;
    $fullDesignation = $designation;
    if (!empty($subDesignation)) {
        $fullDesignation .= ' - ' . $subDesignation;
    }
    
    // Format dates
    $formatDate = function($date) {
        if (empty($date) || $date === '0000-00-00') {
            return 'DD-MM-YYYY';
        }
        return date('d-M-Y', strtotime($date));
    };
    
    $today = $data['date'] ?? date('d-m-Y');
    
    // Get the letter number from data (passed from controller) or generate a default one
    $letterNo = $data['letter_no'] ?? null;
    
    if (!$letterNo) {
        $prefix = 'REL';
        $year = date('Y');
        $month = date('m');
        
        // Get the latest sequence number for this month
        $latestLetter = DB::table('hr_letters')
            ->where('template', 'relieving')
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
        $letterNo = $letterNo ?? 'REL/' . date('Y/m') . '/001';
    }
    $empCode = $employee->v_employee_code ?? '';
    $fullName = $employee->v_employee_full_name ?? '';
    $shortName = $employee->v_employee_name ?? $employee->v_first_name ?? strtok($fullName, ' ');
    
    // Get resignation and relieving dates with proper formatting
    $resignationDate = $formatDate($employee->dt_notice_period_start_date ?? null);
    $relieveDate = $formatDate($employee->dt_release_date ?? null);
@endphp
<div class="sheet">
    @if(file_exists($letterheadPath))
    <div class="letterhead-bg"><img src="{{ $letterheadUrl }}" alt="Letterhead" /></div>
    @endif
    <div class="content-wrapper inner-page">
        <div style="margin-bottom:14px;">
            <div><strong>Letter No:</strong> {{ $letterNo }}</div>
            <div><strong>Employee Code:</strong> {{ $empCode }}</div>
            <div><strong>Date:</strong> {{ $formatDate($today) }}</div>
            <div><strong>Name:</strong> {{ $salutation }} {{ ucwords(strtolower($fullName)) }}</div>
        </div>
        
        <h2 style="text-transform: none; font-weight: bold;">Relieving Letter</h2>
        
        <p>Dear {{ ucwords(strtolower($shortName)) }},</p>
        
        <p>This is reference to your resignation dated <strong>{{ $resignationDate }}</strong>, where in you have requested us to  relieve you on the date <strong>{{ $relieveDate }}</strong>. We wish to inform you that your resignation has been accepted and you are being relieved from your duties as <strong>{{ ucwords(strtolower($fullDesignation)) }}</strong> effective from <strong>{{ $relieveDate }}</strong>.</p>
        
        <p>We would also want to confirm that your full & final settlement would be cleared by the organization. We appreciate your contributions made to the organization and wish you all the best for your future endeavors.</p>
        
        <div class="signature">
            <div>For, <strong>Acorn Universal Consultancy LLP</strong></div>
            <img src="{{ asset('letter-head/sign.png') }}" alt="Authorized Signatory" style="height:28px; width:108px; margin:6px 0;"/>
            <div><strong>Dr. Kishor Dholwani (General Manager)</strong></div>
            
        </div>
    </div>
</div>
</body>
</html>
