<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Appraisal Letter</title>
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
        @page {
            margin: 0;
            size: A4;
        }
        
        body, html {
            margin: 0;
            padding: 0;
            background: #fff;
        }
        
        .sheet { 
            position: relative;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 0;
            page-break-after: always;
            background: #fff;
        }
        
        .inner-page { 
            position: relative;
            z-index: 1;
            padding: 45mm 20mm 20mm 20mm;
        }
        
        .letterhead-bg { 
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            page-break-inside: avoid;
        }
        
        .letterhead-bg img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .text-justify {
            text-align: justify;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .mt-4 {
            margin-top: 1.5rem;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem;
        }
        
        .signature {
            margin-top: 3rem;
        }
        
        .signature img {
            height: 60px;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-weight: bold;
            margin-top: 1rem;
        }
        
        .appraisal-details {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        
        .appraisal-details th, .appraisal-details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .appraisal-details th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
@php
    $letterheadPath = public_path('letter-head/Acorn_Letterhead.png');
    $letterheadUrl = asset('letter-head/Acorn_Letterhead.png');
    $signPath = public_path('letter-head/sign.png');
    $signUrl = asset('letter-head/sign.png');
    $empName = $employee->v_employee_full_name ?? '';
    $empCode = $employee->v_employee_code ?? '';
    $designation = $employee->v_designation_name ?? '';
    $department = $employee->v_department_name ?? '';
    $date = date('d/m/Y');
    $currentDate = date('d F Y');
    
    // Format employee name with proper case
    $formattedName = ucwords(strtolower($empName));
    
    // Format address
    $address = [
        $employee->v_permanent_address_1 ?? '',
        $employee->v_permanent_address_2 ?? '',
        $employee->v_permanent_city ?? '',
        $employee->v_permanent_state ?? '',
        $employee->v_permanent_country ?? '',
        $employee->v_permanent_pincode ?? ''
    ];
    $formattedAddress = implode(', ', array_filter($address));
    
    // Appraisal details
    $appraisalDate = isset($data['appraisal_date']) ? date('d/m/Y', strtotime($data['appraisal_date'])) : date('d/m/Y');
    $effectiveDate = isset($data['effective_date']) ? date('d/m/Y', strtotime($data['effective_date'])) : date('d/m/Y', strtotime('+1 month'));
    $currentSalary = $data['current_salary'] ?? '';
    $revisedSalary = $data['revised_salary'] ?? '';
    $appraisalRating = $data['appraisal_rating'] ?? '';
    $appraisalPeriod = $data['appraisal_period'] ?? 'Annual Performance Appraisal ' . (date('Y')-1) . '-' . date('y');
    $nextAppraisalDate = date('d/m/Y', strtotime('+1 year', strtotime($effectiveDate)));
    
    // Company details
    $companyName = config('constants.COMPANY_NAME', 'Acorn Universal Consultancy LLP');
    $companyAddress = config('constants.COMPANY_ADDRESS', 'Mumbai, India');
    $companyPhone = config('constants.COMPANY_PHONE', '');
    $companyEmail = config('constants.COMPANY_EMAIL', '');
    $hrName = config('constants.HR_NAME', 'HR Manager');
    $hrDesignation = config('constants.HR_DESIGNATION', 'HR Manager');
@endphp

<!-- Page 1 -->
<div class="sheet">
    @if(file_exists($letterheadPath))
        <div class="letterhead-bg">
            <img src="{{ $letterheadUrl }}" alt="Letterhead" />
        </div>
    @endif
    
    <div class="inner-page">
        <div class="text-right">
            <p>Date: {{ $date }}</p>
        </div>
        
        <p>To,</p>
        <p><strong>{{ $formattedName }}</strong></p>
        <p>Employee ID: {{ $empCode }}</p>
        <p>Designation: {{ $designation }}</p>
        <p>Department: {{ $department }}</p>
        
        <div class="mt-4">
            <p class="text-center"><strong>APPRAISAL LETTER</strong></p>
            <p class="text-center"><strong>Ref: APP/{{ date('Y') }}/{{ $empCode }}</strong></p>
        </div>
        
        <div class="mt-4">
            <p>Dear {{ $formattedName }},</p>
            
            <p>We are pleased to inform you that your performance for the period {{ $appraisalPeriod }} has been reviewed, and we are happy to share the outcome of your appraisal.</p>
            
            <p>Based on your overall performance, you have been rated as: <strong>{{ $appraisalRating }}</strong></p>
            
            <p>We appreciate your dedication and contribution to the company. As a result of your performance, we are pleased to revise your compensation as follows:</p>
            
            <table class="appraisal-details">
                <tr>
                    <th>Particulars</th>
                    <th>Current (₹)</th>
                    <th>Revised (₹)</th>
                </tr>
                <tr>
                    <td>Basic Salary</td>
                    <td>{{ number_format($currentSalary, 2) }}</td>
                    <td>{{ number_format($revisedSalary, 2) }}</td>
                </tr>
                <!-- Add other salary components as needed -->
            </table>
            
            <p>The revised salary will be effective from <strong>{{ $effectiveDate }}</strong>.</p>
            
            <p>Your next appraisal is scheduled for <strong>{{ $nextAppraisalDate }}</strong>. We encourage you to continue your good work and strive for excellence in all your endeavors.</p>
            
            <p>Please sign the duplicate of this letter as an acknowledgment of receipt and return it to the HR department within one week from the date of this letter.</p>
            
            <p>We appreciate your contribution to the organization and look forward to your continued success.</p>
        </div>
        
        <div class="signature">
            <p>Yours sincerely,</p>
            @if(file_exists($signPath))
                <img src="{{ $signUrl }}" alt="Signature" />
            @endif
            <p><strong>{{ $hrName }}</strong></p>
            <p>{{ $hrDesignation }}</p>
            <p class="company-name">{{ $companyName }}</p>
            <p>{{ $companyAddress }}</p>
            @if($companyPhone)
                <p>Phone: {{ $companyPhone }}</p>
            @endif
            @if($companyEmail)
                <p>Email: {{ $companyEmail }}</p>
            @endif
        </div>
    </div>
</div>
</body>
</html>
