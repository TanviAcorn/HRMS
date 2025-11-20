<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Suspension Letter</title>
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
    
    // Suspension details
    $suspensionDate = isset($data['suspension_date']) ? date('d/m/Y', strtotime($data['suspension_date'])) : '';
    $suspensionReason = $data['suspension_reason'] ?? 'misconduct';
    $suspensionPeriod = $data['suspension_period'] ?? '7 days';
    $terms = $data['terms'] ?? 'During the suspension period, you are required to be available for any investigation and are not permitted to enter company premises without prior permission.';
    
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
    
    <div class="inner-page" style="padding-top: 20mm;">
        <p style="margin-bottom: 2rem;">Date: {{ $currentDate }}</p>
        
        <p>To,</p>
        <p><strong>{{ $formattedName }}</strong></p>
        <p>Employee ID: {{ $empCode }}</p>
        <p>Designation: {{ $designation }}</p>
        <p>Department: {{ $department }}</p>
        
        <div class="mt-4">
            <p class="text-center"><strong>SUSPENSION LETTER</strong></p>
            <p class="text-center"><strong>Ref: SUS/{{ date('Y') }}/{{ $empCode }}</strong></p>
        </div>
        
        <div class="mt-4">
            <p>Dear {{ $formattedName }},</p>
            
            <p>This letter is to inform you that you have been placed under suspension with immediate effect from {{ $suspensionDate }}, pending investigation into the matter of {{ $suspensionReason }}.</p>
            
            <p>During this suspension period of {{ $suspensionPeriod }}, you are required to:</p>
            <ol>
                <li>Remain available for any inquiries or meetings related to the investigation</li>
                <li>Not enter the company premises without prior written permission</li>
                <li>Not contact any clients, vendors, or other employees regarding company matters</li>
                <li>Return all company property in your possession</li>
            </ol>
            
            <p>Your salary and benefits will be on hold during the suspension period. A final decision regarding your employment will be made upon completion of the investigation.</p>
            
            <p>You are required to acknowledge receipt of this suspension letter by signing and returning a copy to the HR department within 24 hours.</p>
            
            <p>This suspension is without prejudice to any other disciplinary action that may be taken based on the findings of the investigation.</p>
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
