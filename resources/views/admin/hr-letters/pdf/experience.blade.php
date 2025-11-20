<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Experience Letter</title>
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
        html { background: #eee; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            line-height: 1.6;
        }
        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            position: relative;
        }
        .letterhead-bg {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
        }
        .letterhead-bg img {
            width: 100%; height: 100%; object-fit: cover;
        }
        .content-wrapper {
            position: relative;
            z-index: 1;
            padding: 30mm 20mm 25mm 20mm;
        }
        h2 {
            text-align: center;
            font-size: 16px;
            margin: 12px 0 20px;
            font-weight: bold;
        }
        p {
            text-align: justify;
            margin: 10px 0;
        }
        ol { margin-left: 15px; padding-left: 10px; }
        li { margin-bottom: 8px; text-align: justify; font-size: 12px;}
        .signature { margin-top: 40px; }
        .signature div { margin-bottom: 40px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
@php
    $letterheadPath = public_path('letter-head/Acorn_Letterhead.png');
    $letterheadUrl = asset('letter-head/Acorn_Letterhead.png');
    
    // Determine salutation
    $salutation = 'Mr.';
    if (isset($employee->e_gender) && $employee->e_gender === 'Female') {
        $salutation = (isset($employee->e_marital_status) && in_array($employee->e_marital_status, ['Married', 'Widow', 'Widow / Widower'])) ? 'Mrs.' : 'Ms.';
    }
    
    // Employee details
    $fullName = ucwords(strtolower($employee->v_employee_full_name ?? ''));
    $shortName = $employee->v_employee_name ?? $employee->v_first_name ?? strtok($fullName, ' ');
    $empCode = $employee->v_employee_code ?? 'AUC000';
    $designation = $data['designation'] ?? ($employee->designationInfo->v_value ?? '');
    $subdesignation = isset($employee->subDesignationInfo) ? ucwords(strtolower($employee->subDesignationInfo->v_sub_designation_name)) : null;
    $fullDesignation = ucwords(strtolower($designation));
    if (!empty($subdesignation)) {
        $fullDesignation .= ' - ' . $subdesignation;
    }
    
    // Format dates
    $formatDate = function($date) {
        if (empty($date) || $date === '0000-00-00') {
            return 'DD-MMM-YYYY';
        }
        return date('d-M-Y', strtotime($date));
    };
    
    $joinDate = $formatDate($employee->dt_joining_date ?? null);
    $exitDate = $formatDate($employee->dt_exit_date ?? null);
    $today = $data['date'] ?? date('d-m-Y');
    
    // Get the letter number from data (passed from controller) or generate a default one
    $letterNo = $data['letter_no'] ?? null;
    
    if (!$letterNo) {
        $prefix = 'EXP';
        $year = date('Y');
        $month = date('m');
        
        // Get the latest sequence number for this month
        $latestLetter = DB::table('hr_letters')
            ->where('template', 'experience')
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
        $letterNo = $letterNo ?? 'EXP/' . date('Y/m') . '/001';
    }
    
    $companyName = config('constants.COMPANY_NAME', 'Acorn Universal Consultancy LLP');
    $address = $employee->v_address ?? '301 - Foras Flat, Kalpana Society, Waghodia Road, Vadodara, Gujarat - 390019';
@endphp

<div class="sheet">
    @if(file_exists($letterheadPath))
    <div class="letterhead-bg"><img src="{{ $letterheadUrl }}" alt="Letterhead" /></div>
    @endif
    <div class="content-wrapper">
        <div style="margin-bottom: 20px; line-height: 1.8;">
            <div><strong>Letter No:</strong> {{ $letterNo }}</div>
            <div><strong>Date:</strong> {{ $formatDate($today) }}</div>
            <div><strong>Employee Code:</strong> {{ $empCode }}</div>
            <div><strong>Name:</strong> {{ $salutation }} {{ $fullName }}</div>
        </div>

        <h2>Experience Letter</h2>
        
        <p>To Whom It May Concern</p>
        
        @php
            $pronoun = (isset($employee->e_gender) && $employee->e_gender === 'Female') ? 'she' : 'he';
            $possessive = (isset($employee->e_gender) && $employee->e_gender === 'Female') ? 'her' : 'his';
            $possessive1 = (isset($employee->e_gender) && $employee->e_gender === 'Female') ? 'herself' : 'himself';
        @endphp
        <p>This is to certify that {{ $salutation }} {{ ucwords(strtolower($shortName)) }} has been working in our organization from {{ $joinDate }} to {{ $exitDate }} in the position of<strong> {{ $fullDesignation }} </strong>.</p>
        
        <p>During {{ $possessive }} stay, {{ $pronoun }} demonstrated {{ $possessive1 }} as a diligent and truthful person. {{ ucfirst($possessive) }} interpersonal skills are outstanding; {{ $pronoun }} has been very helpful and highly appraised by {{ $possessive }} manager.</p>
        
        <p>We wish you all success in your future endeavors.</p>
        
            <div>For, <strong>Acorn Universal Consultancy LLP</strong></div>
            <img src="{{ asset('letter-head/sign.png') }}" alt="Authorized Signatory" style="height:28px; width:108px; margin:6px 0;"/>
            <div><strong>Dr. Kishor Dholwani (General Manager)</strong></div>

    </div>
</div>
</body>
</html>
