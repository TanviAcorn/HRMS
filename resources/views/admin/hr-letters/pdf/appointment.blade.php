<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Appointment Letter</title>
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
            @top-center {
                content: element(header);
            }
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
        
        @media print {
            .sheet {
                margin: 0;
                border: none;
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
            
            .inner-page {
                position: relative;
                z-index: 1;
            }
        }
        }

        /* Fallback default (in case media not applied) */
        .sheet { width: 210mm; min-height: 297mm; margin: 0 auto; background: #fff; position: relative; }
        .inner-page { position: relative; z-index: 1; padding: 30mm 20mm 25mm 20mm; }
        .letterhead-bg { position: absolute; inset: 0; z-index: 0; }
        .letterhead-bg img { width: 100%; height: 100%; object-fit: cover; }

        h2 {
            text-align: center;
            text-transform: uppercase;
            font-size: 16px;
            margin: 20px 0;
            text-decoration: underline;
        }

        p {
            text-align: justify;
            font-size: 12px;
            margin: 8px 0;
        }

        ol {
            margin-left: 20px;
            padding-left: 10px;
            font-size: 12px;
        }

        li {
            margin-bottom: 8px;
            text-align: justify;
            font-size: 12px;
        }

        .signature {
            margin-top: 60px;
        }

        .signature div {
            margin-bottom: 40px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
@php
    $letterheadPath = public_path('letter-head/Acorn_Letterhead.png');
    $letterheadUrl = asset('letter-head/Acorn_Letterhead.png');
    $letterheadData = file_exists($letterheadPath) ? ('data:image/png;base64,' . base64_encode(file_get_contents($letterheadPath))) : '';
    $empName = $employee->v_employee_full_name ?? '';
    $shortName = strtok($empName, ' ');
    $designation = strtoupper($data['designation'] ?? (data_get($employee,'designationInfo.v_value') ?: (data_get($employee,'subDesignationInfo.v_name') ?: '')));
    $joinDate = $data['joining_date'] ?? (isset($employee->dt_joining_date) ? date('d-m-Y', strtotime($employee->dt_joining_date)) : '');
    $today = $data['date'] ?? date('d-m-Y');
    
    // Get the letter number from data (passed from controller) or generate a default one
    $letterNo = $data['letter_no'] ?? null;
    
    if (!$letterNo && $data['template'] === 'appointment') {
        $prefix = 'JOI';
        $year = date('Y');
        $month = date('m');
        
        // Get the latest sequence number for this month
        $latestLetter = DB::table('hr_letters')
            ->where('template', 'appointment')
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
        $letterNo = $letterNo ?? 'JOI/' . date('Y/m') . '/001';
    }
    
    $address = $employee->v_address ?? '301 - Foras Flat, Kalpana Society, Waghodia Road, Vadodara, Gujarat - 390019';
    $companyName = config('constants.COMPANY_NAME', 'Acorn Universal Consultancy LLP');
@endphp

{{-- Print-only repeating background for all pages (dompdf honors position:fixed) --}}
@if(!empty($letterheadData))
    <div class="print-bg"><img src="{{ $letterheadData }}" alt="Letterhead" /></div>
@endif

<div class="sheet">
    @if(file_exists($letterheadPath))
        <div class="letterhead-bg">
            <img src="{{ $letterheadUrl }}" alt="Letterhead" />
        </div>
    @endif

    <div class="inner-page">
        <div style="margin-bottom: 14px;">
            <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($today)->format('d-M-Y') }}</div>
            <strong>Letter No:</strong> {{ $letterNo }}<br>
        </div>

        <h2 style="text-transform: none; text-decoration: none;">Appointment Letter</h2>

        @php
            // Determine salutation based on gender and marital status
            $salutation = 'Mr.';
            if (isset($employee->e_gender) && $employee->e_gender === 'Female') {
                $salutation = (isset($employee->e_marital_status) && in_array($employee->e_marital_status, ['Married', 'Widow', 'Widow / Widower'])) ? 'Mrs.' : 'Ms.';
            }
            
            // Get full name in original case
            $fullName = $employee->v_employee_full_name ?? $empName;
            // Ensure name is not in uppercase
            $fullName = ucwords(strtolower($fullName));
            
            // Merge address components and ensure not in uppercase
            $address = [];
            if (!empty($employee->v_permanent_address_line_first)) $address[] = ucwords(strtolower($employee->v_permanent_address_line_first));
            if (!empty($employee->v_permanent_address_line_second)) $address[] = ucwords(strtolower($employee->v_permanent_address_line_second));
            if (!empty($employee->v_permanent_address_pincode)) $address[] = $employee->v_permanent_address_pincode; // Keep pincode as is
            $fullAddress = implode(', ', array_filter($address));
        @endphp
        <p><strong>{{ $salutation }} {{ $fullName }},</strong></p>
        @if(!empty($fullAddress))
            <p>{{ $fullAddress }}</p>
        @endif

        <p>Dear {{ ucwords(strtolower($employee->v_employee_name)) }},</p>

        @php
            // Format join date
            $formattedJoinDate = \Carbon\Carbon::parse($joinDate)->format('d-M-Y');
            
            // Get designation and subdesignation
            $designationText = ucwords(strtolower($designation));
            $subDesignation = isset($employee->subDesignationInfo) ? ucwords(strtolower($employee->subDesignationInfo->v_sub_designation_name)) : null;
            $fullDesignation = $designationText . ($subDesignation ? ' - ' . $subDesignation : '');
        @endphp
        <p>With reference to your application and subsequent discussion you had with us, we are pleased to appoint you as <strong>{{ $fullDesignation }}</strong> in our organization with effect from <strong>{{ $formattedJoinDate }}</strong> on the following terms and conditions:</p>

        <!-- Page 1 -->
        <ol>
            <li> Designation: You shall be placed as <strong>{{ $fullDesignation }}</strong>.</li>
            <li> Compensation: You will be paid Rs. __________ /- CTC salary per annum as per attached annexure.</li>
            <li> Probation: Your appointment will be probationary for a period of 6 months, during which your performance, behavior, and conduct will be evaluated. Acorn Universal Consultancy LLP reserves the right to terminate your employment if these are found to be unsatisfactory during this period. Upon successful completion of the probation, and if the management and Line Manager are satisfied with your performance, your appointment will be confirmed with a probation confirmation letter.</li>
            <li> Benefits: Additionally, you will not be entitled to any company benefits during the initial six months of probation. Upon successful evaluation by your line manager after the six-month probation period, you will become eligible to receive company benefits.</li>
            <li> You will not be entitled to any other benefits during the initial six months of probation.</li>
            <li> Salaries, facilities, and other sums payable under this appointment are subject to Income-tax or any other tax and you shall be liable for the same.</li>
            <li> You will work during your employment with the company with honestly, faithfully, diligently, and efficiently to the utmost of your power and skill, devote your whole time and attention exclusively to the duties entrusted to you and will not engage directly and indirectly or allow yourself to be engaged to work for any person, firm or company in the capacity whatsoever, nor do any business without obtaining previous permission of the management in written.</li>
            <li> Upon ceasing to be in the services of the company for any reason, you shall immediately return any records, documents and other information of the company which are in your possession and shall not retain any copies (electronic or otherwise) of the same.</li>
        </ol>
    </div>
</div>

<!-- Page 2 (screen preview uses a new sheet) -->
<div class="sheet">
    @if(file_exists($letterheadPath))
        <div class="letterhead-bg">
            <img src="{{ $letterheadUrl }}" alt="Letterhead" />
        </div>
    @endif
    <div class="inner-page">
        <ol start="9">
            <li> You will not, whether you are in employment of the company or not, at any time or times, without consent of the company in writing disclose, divulge or make public except under legal obligations, Digital Marketing transactions or dealing of the company which ought not to be disclosed, divulged or made public whether the same be confided in you or become known to you in the course of employment the company or otherwise.</li>
            <li> You will carry out work assigned to you from time to time as per the direction of the superior. You will obey the lawful commands and direction the superior or director respects your services and to be best of your ability executive such work as your superior or directors may entrust you from time to time.</li>
            <li> You will get computer system with internet facility for Processing work only. You are not allowed to open any other page on the systems, finding to do so appropriate action will be taken. In case of any damages to the system occurs by the employee, appropriate charge will be applied. For new system within year full charge or else 50% charge.</li>
            <li> You may be posted in other shift or division in other capacity or may be assigned any other work and you may be transferred to any other department of the company if we found for the execution of any work undertake by the company and you will submit to the regulations in force from time to time in that other establishment.</li>
            <li> Your appointment and its continuance are subject to your being and remaining medically (physically and mentally) fit. The management shall have right to get your medically examined periodically or anytime by registered medical practitioner of their choice, whose opinion as to your fitness or otherwise shall be final and binding on you.</li>
            <li> Notice Period: In case you wish to terminate your employment with the company, you will have to give a minimum notice of 1 month (24 working days must). Company will not give any letters within the year of job timing and in case of no notice period served after one year job duration.</li>
            <li> The company shall have right to vary, amend and modify any items of the pay packet without adversely affecting the total pay of packet.</li>
            <li> Your compensation is confidential information of the company. Any discussion or disclosure of your compensation with anybody shall be considered as breach of agreement by you. Any unauthorized disclosure of confidential information by you shall lead to disciplinary action up to and including termination of your employment by the company without any notice or compensation.</li>
            <li> This appointment is subject to satisfactory investigation of your credentials and if is found at any time that you have made any false statement is suppressed any material information, it shall lead to termination of your employment by the company without any notice or compensation.</li>
        </ol>
    </div>
</div>

<!-- Page 3 (screen preview uses a new sheet) -->
<div class="sheet">
    @if(file_exists($letterheadPath))
        <div class="letterhead-bg">
            <img src="{{ $letterheadUrl }}" alt="Letterhead" />
        </div>
    @endif
    <div class="inner-page">
        <ol start="18">
            <li> Your service will be subject to rules and regulations of the company as may be framed time to time.</li>
        </ol>
        
        <!-- Acceptance Section -->
        <div style="margin-top: 40px;">
            <p>Kindly sign the duplicate copy of this letter in token of your acceptance.</p>
            <p><strong>I, {{ $fullName }}, accept the above terms and conditions and will join on {{ $formattedJoinDate }}.</strong></p>
            <p>Sign of Employee: ____________________________________</p>

            <div style="margin-top: 50px; text-align: left;">
                <div style="margin-bottom: 5px;">For, <strong>{{ $companyName }}</strong></div>
                    <img src="{{ asset('letter-head/sign.png') }}" alt="Authorized Signatory"style="height:28px; width:128px; margin:6px 0;" />
                <div><strong>Dr. Kishor Dholwani <br> (General Manager)</strong></div>
            </div>
        </div>
    </div>
</div>

<!-- New Page for Salary Details -->
<div class="sheet">
    @if(file_exists($letterheadPath))
        <div class="letterhead-bg">
            <img src="{{ $letterheadUrl }}" alt="Letterhead" />
        </div>
    @endif
    <div class="inner-page">
        <!-- Salary Details Section -->
        <div style="margin-top: 20px;">
            <h3 style="text-align: center; margin-bottom: 30px;">Annexure</h3>
            
            <!-- Employee Details -->
            <div style="margin-bottom: 30px;">
                <div style="margin-bottom: 8px;"><strong>Name:</strong> {{ $salutation }} {{ $fullName }}</div>
                <div style="margin-bottom: 8px;"><strong>Designation:</strong> {{ $fullDesignation }}</div>
                <div><strong>Date:</strong> {{ $formattedJoinDate }}</div>
            </div>

            <!-- Salary Details Table -->
            <h4 style="margin: 20px 0 15px 0;">Salary Details:</h4>
            
            <table border="1" cellspacing="0" cellpadding="8" width="100%" style="border-collapse: collapse; margin-bottom: 30px;">
                <tr>
                    <th style="padding: 10px; border: 1px solid #000; text-align: left; background-color: #f2f2f2; width: 40%;">Component</th>
                    <th style="padding: 10px; border: 1px solid #000; text-align: center; background-color: #f2f2f2; width: 30%;">Monthly (INR)</th>
                    <th style="padding: 10px; border: 1px solid #000; text-align: center; background-color: #f2f2f2; width: 30%;">CTC Yearly (INR)</th>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000;">Basic</td>
                    <td style="padding: 8px; border: 1px solid #000; text-align: center;">_______________</td>
                    <td style="padding: 8px; border: 1px solid #000; text-align: center;">_______________</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000;">HRA (40% Basic)</td>
                    <td style="padding: 8px; border: 1px solid #000; text-align: center;">_______________</td>
                    <td style="padding: 8px; border: 1px solid #000; text-align: center;">_______________</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000;">Special Allowance</td>
                    <td style="padding: 8px; border: 1px solid #000; text-align: center;">_______________</td>
                    <td style="padding: 8px; border: 1px solid #000; text-align: center;">_______________</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; font-weight: bold;">Gross</td>
                    <td style="padding: 8px; border: 1px solid #000; text-align: center; font-weight: bold;">_______________</td>
                    <td style="padding: 8px; border: 1px solid #000; text-align: center; font-weight: bold;">_______________</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; font-weight: bold;" colspan="3">Retiral Benefits</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; padding-left: 30px;">PF</td>
                    <td style="padding: 8px; border: 1px solid #000; text-align: center;">_______________</td>
                    <td style="padding: 8px; border: 1px solid #000; text-align: center;">_______________</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; font-weight: bold;">Gross CTC (Gross + Retiral)</td>
                    <td style="padding: 8px; border: 1px solid #000; text-align: center; font-weight: bold;">_______________</td>
                    <td style="padding: 8px; border: 1px solid #000; text-align: center; font-weight: bold;">_______________</td>
                </tr>
            </table>

            <!-- Signature Section -->
            <div style="margin-top: 50px; text-align: left;">
                <div style="margin-bottom: 5px;">For, <strong>{{ $companyName }}</strong></div>
                    <img src="{{ asset('letter-head/sign.png') }}" alt="Authorized Signatory"style="height:28px; width:108px; margin:6px 0;" />
                <div><strong>Dr. Kishor Dholwani <br> (General Manager)</strong></div>
            </div>


        </div>
    </div>
</div>

</body>
</html>
