<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Internship Letter</title>
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
            .sheet { width: 210mm; min-height: 297mm; margin: 20px auto; background: #fff; position: relative; box-shadow: 0 0 4mm rgba(0,0,0,.1); }
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
        .letterhead-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; }
        .letterhead-bg img { width: 100%; height: 100%; object-fit: cover; }
        html { background: #eee; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#000; margin: 0; line-height: 1.5; }
        .content-wrapper { position: relative; z-index: 1; }
        h2 { text-align:center; font-size:16px; margin: 0 0 16px; font-weight: bold; }
        p { text-align: justify; margin: 10px 0; }
        .signature { margin-top: 30px; }
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
    $fullName = $employee->v_employee_full_name ?? '';
    $shortName = $employee->v_employee_name ?? $employee->v_first_name ?? strtok($fullName, ' ');
    $enrollmentNo = $data['enrollment_no'] ?? '00000000000';
    $collegeName = $data['college_name'] ?? 'College Name';
    $universityName = $data['university_name'] ?? 'University Name';
    $department = $data['department'] ?? 'Department';
    $subDepartment = $data['sub_department'] ?? 'Sub Department';
    
    // Format dates
    $formatDate = function($date) {
        if (empty($date) || $date === '0000-00-00') {
            return 'DD-MM-YYYY';
        }
        return date('d-m-Y', strtotime($date));
    };
    
    $startDate = $formatDate($data['from_date'] ?? null);
    $endDate = $formatDate($data['to_date'] ?? null);
    $today = $data['date'] ?? date('d-m-Y');
    
    // Get the letter number from data (passed from controller) or generate a default one
    $letterNo = $data['letter_no'] ?? null;
    
    if (!$letterNo) {
        $prefix = 'INT';
        $year = date('Y');
        $month = date('m');
        
        // Get the latest sequence number for this month
        $latestLetter = DB::table('hr_letters')
            ->where('template', 'internship')
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
        $letterNo = $letterNo ?? 'INT/' . date('Y/m') . '/001';
    }
    
    // Combine department and subdepartment
    $departmentInfo = $department;
    if (!empty($subDepartment)) {
        $departmentInfo .= ' - ' . $subDepartment;
    }
@endphp
<div class="sheet">
    @if(file_exists($letterheadPath))
    <div class="letterhead-bg"><img src="{{ $letterheadUrl }}" alt="Letterhead" /></div>
    @endif
    <div class="content-wrapper inner-page">
        <div style="margin-bottom:14px;">
            <div><strong>Letter No:</strong> {{ $letterNo }}
             <div><strong>Date:</strong> {{ $formatDate($today) }}</div>
            <div><strong>Name:</strong> {{ $salutation }} {{ ucwords(strtolower($fullName)) }}</div>
        </div>
        
        <h2>Internship Letter</h2>
        
        <p>To Whom it May Concern</p>
        
        <p>This is to certify that {{ $salutation }} {{ ucwords(strtolower($shortName)) }}, student of {{ $collegeName }} with {{ $universityName }}, Enrolment Number: {{ $enrollmentNo }}, has successfully completed his internship at Acorn Universal Consultancy LLP, Anand. The duration of the internship was from {{ $startDate }} to {{ $endDate }}.</p>
        
        <p>During his internship, he was assigned various activities in {{ $departmentInfo }} division. We found him to be very skilful and hard working. He has been very resourceful in learning new tasks and willing to put his best efforts and get into the depth of the subject to understand it better.</p>
        
        <p>His association has been very fruitful and we wish him all the best in his future endeavours.</p>
        
        <div class="signature">
            <div>For, <strong>Acorn Universal Consultancy LLP</strong></div>
            <img src="{{ asset('letter-head/sign.png') }}" alt="Authorized Signatory" style="height:28px; width:108px; margin:6px 0;"/>
            <div><strong>Dr. Kishor Dholwani (General Manager)</strong></div>
        </div>
    </div>
</div>
</body>
</html>
