<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Letter</title>
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
            text-transform: uppercase;
            font-size: 16px;
            margin: 20px 0;
            text-decoration: underline;
        }
        .content-wrapper { 
            position: relative; 
            z-index: 1; 
        }
        .inner-page { 
            padding: 55mm 20mm 25mm 20mm; 
        }
        .date {
            text-align: left;
            margin-bottom: 30px;
        }
        .content {
            min-height: 150mm;
        }
        .signature {
            margin-top: 30px;
        }
        .signature img {
            height: 48px;
            margin: 5px 0 10px 0;
        }
    </style>
</head>
<body>
@php
    $letterheadPath = public_path('letter-head/Acorn_Letterhead.png');
    $letterheadUrl = asset('letter-head/Acorn_Letterhead.png');
    $today = $data['date'] ?? date('d-m-Y');
    
    // Get the letter number from data (passed from controller) or generate a default one
    $letterNo = $data['letter_no'] ?? null;
    
    if (!$letterNo) {
        $prefix = 'OTH';  // Using OTH as the prefix for other letters
        $year = date('Y');
        $month = date('m');
        
        // Get the latest sequence number for this month
        $latestLetter = DB::table('hr_letters')
            ->where('template', 'other')
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
        $letterNo = $letterNo ?? 'OTH/' . date('Y/m') . '/001';
    }
@endphp

<div class="sheet">
    @if(file_exists($letterheadPath))
        <div class="letterhead-bg">
            <img src="{{ $letterheadUrl }}" alt="Letterhead" />
        </div>
    @endif

    <div class="content-wrapper inner-page">
        <div class="date">
            @if(isset($data['letter_no']) && $data['letter_no'])
                <div><strong>Letter No:</strong> {{ $data['letter_no'] }}</div>
            @endif
            <div><strong>Date:</strong> {{ $today }}</div>
        </div>

        <div class="content">
            {!! $content ?? 'Content goes here...' !!}
        </div>

        <div class="signature">
            <div>For, <strong>{{ config('constants.COMPANY_NAME') }}</strong></div>
            <img src="{{ asset('letter-head/sign.png') }}" alt="Authorized Signatory" style="height:28px; width:108px; margin:6px 0;" />
            <div><strong>Dr. Kishor Dholwani <br> (General Manager)</strong></div>
        </div>
    </div>
</div>
</body>
</html>
