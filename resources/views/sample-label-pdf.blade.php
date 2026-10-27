<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: 50mm auto;
            margin: 0;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            width: 50mm;
            font-size: 6pt;
            background: white;
            color: #1a1a1a;
            line-height: 1.2;
        }
        .label-wrapper {
            width: 50mm;
            position: relative;
            padding: 2mm 2mm 2mm 5mm;
            box-sizing: border-box;
            page-break-after: always;
            background: #fff;
        }
        .tube-indicator {
            position: absolute;
            left: 0;
            top: 0;
            width: 2.5mm;
            height: 100%;
        }
        .header-section {
            width: 100%;
            margin-bottom: 1mm;
            border-bottom: 0.1mm solid #eee;
            padding-bottom: 1mm;
        }
        .patient-info {
            float: left;
            width: 32mm;
        }
        .qr-section {
            float: right;
            width: 10mm;
            text-align: right;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .patient-name {
            font-size: 8pt;
            font-weight: bold;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.1;
            display: block;
            word-wrap: break-word;
        }
        .id-tag {
            background: #333;
            color: #fff;
            padding: 0.2mm 0.8mm;
            font-weight: bold;
            border-radius: 0.4mm;
            font-size: 5.5pt;
        }
        .meta-text {
            font-size: 5.5pt;
            color: #444;
            margin-top: 0.5mm;
        }
        .qr-image {
            width: 10mm;
            height: 10mm;
        }
        .tests-container {
            margin-top: 1mm;
            width: 100%;
        }
        .tests-label {
            font-weight: bold;
            font-size: 5pt;
            color: #666;
            text-transform: uppercase;
            display: block;
            margin-bottom: 0.3mm;
        }
        .tests-list {
            font-size: 5.5pt;
            color: #000;
            word-wrap: break-word;
            line-height: 1.3;
        }
        .footer-section {
            margin-top: 2mm;
            border-top: 0.1mm solid #eee;
            padding-top: 1mm;
        }
        .tube-badge {
            display: inline-block;
            padding: 0.4mm 1.2mm;
            border: 0.2mm solid #1a1a1a;
            font-size: 5.5pt;
            font-weight: bold;
            border-radius: 0.4mm;
            margin-bottom: 1mm;
        }
        .iso-text {
            font-size: 4pt;
            color: #888;
            display: block;
        }
        .rtl { direction: rtl; text-align: right; }
        .ltr { direction: ltr; text-align: left; }
    </style>
</head>
<body>
    @foreach($groupedAnalyses as $sampleType => $analyses)
    @php
        $patient = $reservation->patient;
        $firstRA = $analyses->first();
        $firstAnalyse = $firstRA->analyse;

        $dob = $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date) : null;
        $age = $dob ? $dob->age . 'Y' : 'N/A';
        $sex = $patient->gender == 'male' ? 'M' : ($patient->gender == 'female' ? 'F' : 'U');

        $color = $firstAnalyse->tube_color ?? '#ffffff';
        if (!$firstAnalyse->tube_color) {
            $typeLow = strtolower($sampleType);
            if (str_contains($typeLow, 'edta') || str_contains($typeLow, 'بنفسجي')) $color = '#9c27b0';
            elseif (str_contains($typeLow, 'citrate') || str_contains($typeLow, 'أزرق')) $color = '#2196f3';
            elseif (str_contains($typeLow, 'heparin') || str_contains($typeLow, 'أخضر')) $color = '#4caf50';
            elseif (str_contains($typeLow, 'dry') || str_contains($typeLow, 'red') || str_contains($typeLow, 'أحمر')) $color = '#f44336';
            elseif (str_contains($typeLow, 'urine') || str_contains($typeLow, 'بول')) $color = '#ffeb3b';
        }
    @endphp

    <div class="label-wrapper {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
        {{-- Side Color Strip --}}
        <div class="tube-indicator" style="background-color: {{ $color }};"></div>

        <div class="header-section clearfix">
            <div class="patient-info">
                <div class="patient-name">{{ $patient->name }}</div>
                <div class="meta-text">
                    <span class="id-tag">#{{ $reservation->id }}</span> | 
                    <strong>{{ $sex }}</strong> | 
                    <strong>{{ $age }}</strong>
                </div>
                <div class="meta-text" style="color: #666;">
                    {{ $patient->birth_date ?? '00-00-0000' }} | {{ \Carbon\Carbon::parse($reservation->analysis_date)->format('d/m/Y') }}
                </div>
            </div>
            
            <div class="qr-section">
                @if($barcode)
                    <img src="{{ $barcode }}" class="qr-image">
                @endif
            </div>
        </div>

        <div class="tests-container">
            <span class="tests-label">Tests</span>
            <div class="tests-list">
                @foreach($analyses as $ra)
                    {{ $ra->analyse->name }}{{ !$loop->last ? ' • ' : '' }}
                @endforeach
            </div>
        </div>

        <div class="footer-section">
            <div class="tube-badge" style="border-left: 1.2mm solid {{ $color }}; color: #000;">
                {{ strtoupper($sampleType ?: 'GENERAL') }}
            </div>
            <span class="iso-text">
                ISO 15189 | COLL: {{ \Carbon\Carbon::parse($reservation->time)->format('H:i') }}
            </span>
        </div>
    </div>
    @endforeach
</body>
</html>
