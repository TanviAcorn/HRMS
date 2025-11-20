<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Full and Final Settlement Letter</title>
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
        
        .settlement-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        
        .settlement-table th, .settlement-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .settlement-table th {
            background-color: #f2f2f2;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
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
    
    // Settlement details
    $relievingDate = isset($data['relieving_date']) ? date('d/m/Y', strtotime($data['relieving_date'])) : date('d/m/Y');
    $lastWorkingDay = isset($data['last_working_day']) ? date('d/m/Y', strtotime($data['last_working_day'])) : date('d/m/Y');
    $noticePeriod = $data['notice_period'] ?? '30 days';
    $noticePeriodServed = $data['notice_period_served'] ?? '30 days';
    $leavingReason = $data['leaving_reason'] ?? 'Personal Reasons';
    
    // Settlement amounts
    $basicSalary = $data['basic_salary'] ?? 0;
    $hra = $data['hra'] ?? 0;
    $lta = $data['lta'] ?? 0;
    $specialAllowance = $data['special_allowance'] ?? 0;
    $leaveEncashment = $data['leave_encashment'] ?? 0;
    $gratuity = $data['gratuity'] ?? 0;
    $otherAllowances = $data['other_allowances'] ?? 0;
    $deductions = $data['deductions'] ?? 0;
    $netPayable = $data['net_payable'] ?? 0;
    
    // Calculate totals
    $totalEarnings = $basicSalary + $hra + $lta + $specialAllowance + $leaveEncashment + $gratuity + $otherAllowances;
    $totalDeductions = $deductions;
    $netAmount = $netPayable;
    
    // Format amounts
    $formatAmount = function($amount) {
        return '₹' . number_format($amount, 2);
    };
    
    // Company details
    $companyName = config('constants.COMPANY_NAME', 'Acorn Universal Consultancy LLP');
    $companyAddress = config('constants.COMPANY_ADDRESS', 'Mumbai, India');
    $companyPhone = config('constants.COMPANY_PHONE', '');
    $companyEmail = config('constants.COMPANY_EMAIL', '');
    $hrName = config('constants.HR_NAME', 'HR Manager');
    $hrDesignation = config('constants.HR_DESIGNATION', 'HR Manager');
    $accountsContact = config('constants.ACCOUNTS_CONTACT', 'Accounts Department');
    $accountsPhone = config('constants.ACCOUNTS_PHONE', '');
    $accountsEmail = config('constants.ACCOUNTS_EMAIL', '');
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
        <p>Address: {{ $formattedAddress }}</p>
        
        <div class="mt-4">
            <p class="text-center"><strong>FULL AND FINAL SETTLEMENT LETTER</strong></p>
            <p class="text-center"><strong>Ref: FNF/{{ date('Y') }}/{{ $empCode }}</strong></p>
        </div>
        
        <div class="mt-4">
            <p>Dear {{ $formattedName }},</p>
            
            <p>With reference to your resignation and subsequent relieving from our services on <strong>{{ $lastWorkingDay }}</strong>, we have processed your full and final settlement as per the company's policies.</p>
            
            <p>Your last working day with {{ $companyName }} was <strong>{{ $lastWorkingDay }}</strong> and your full and final settlement has been calculated as follows:</p>
            
            <table class="settlement-table">
                <tr>
                    <th>Earnings</th>
                    <th class="text-right">Amount (₹)</th>
                </tr>
                <tr>
                    <td>Basic Salary ({{ $noticePeriodServed }} days)</td>
                    <td class="text-right">{{ $formatAmount($basicSalary) }}</td>
                </tr>
                <tr>
                    <td>House Rent Allowance (HRA)</td>
                    <td class="text-right">{{ $formatAmount($hra) }}</td>
                </tr>
                <tr>
                    <td>Leave Travel Allowance (LTA)</td>
                    <td class="text-right">{{ $formatAmount($lta) }}</td>
                </tr>
                <tr>
                    <td>Special Allowance</td>
                    <td class="text-right">{{ $formatAmount($specialAllowance) }}</td>
                </tr>
                <tr>
                    <td>Leave Encashment</td>
                    <td class="text-right">{{ $formatAmount($leaveEncashment) }}</td>
                </tr>
                <tr>
                    <td>Gratuity</td>
                    <td class="text-right">{{ $formatAmount($gratuity) }}</td>
                </tr>
                <tr>
                    <td>Other Allowances</td>
                    <td class="text-right">{{ $formatAmount($otherAllowances) }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Earnings</strong></td>
                    <td class="text-right"><strong>{{ $formatAmount($totalEarnings) }}</strong></td>
                </tr>
                
                <tr>
                    <th>Deductions</th>
                    <th class="text-right">Amount (₹)</th>
                </tr>
                <tr>
                    <td>Tax Deducted at Source (TDS)</td>
                    <td class="text-right">{{ $formatAmount($deductions) }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Deductions</strong></td>
                    <td class="text-right"><strong>{{ $formatAmount($totalDeductions) }}</strong></td>
                </tr>
                
                <tr class="total-row" style="font-size: 1.1em;">
                    <td><strong>Net Amount Payable</strong></td>
                    <td class="text-right"><strong>{{ $formatAmount($netAmount) }}</strong></td>
                </tr>
            </table>
            
            <p>The net amount of <strong>{{ $formatAmount($netAmount) }}</strong> will be credited to your registered bank account within 7-10 working days from the date of this letter.</p>
            
            <p>You are requested to acknowledge the receipt of this full and final settlement letter by signing the duplicate copy and returning it to the HR department at the earliest.</p>
            
            <p>For any queries regarding your full and final settlement, please contact:</p>
            <p>{{ $accountsContact }}<br>
            {{ $companyName }}<br>
            @if($accountsPhone)Phone: {{ $accountsPhone }}<br>@endif
            @if($accountsEmail)Email: {{ $accountsEmail }}@endif
            </p>
            
            <p>We thank you for your valuable contribution to {{ $companyName }} and wish you all the best for your future endeavors.</p>
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
        
        <div class="mt-4" style="page-break-before: always; padding-top: 2rem;">
            <p class="text-center"><strong>ACKNOWLEDGMENT</strong></p>
            <p>I, <strong>{{ $formattedName }}</strong>, Employee ID: <strong>{{ $empCode }}</strong>, hereby acknowledge receipt of the full and final settlement amount of <strong>{{ $formatAmount($netAmount) }}</strong> (Rupees {{ $data['amount_in_words'] ?? '' }}) from {{ $companyName }} in full and final settlement of all my dues up to {{ $lastWorkingDay }}.</p>
            
            <p>I confirm that I have no further claims against {{ $companyName }} and that I have returned all company property, documents, and information in my possession.</p>
            
            <div style="margin-top: 3rem;">
                <p>Date: _________________</p>
                <p>Place: _________________</p>
                <p style="margin-top: 2rem;">_________________________<br>Signature of Employee</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
