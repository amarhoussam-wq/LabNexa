<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: 75mm 50mm;
            margin: 0;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            width: 75mm;
            height: 50mm;
            background: white;
            color: #1a1a1a;
            line-height: 1.15;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .label-wrapper {
            width: 75mm;
            height: 50mm;
            position: relative;
            box-sizing: border-box;
            background: #fff;
            overflow: hidden;
            display: block;
        }
        .tube-indicator {
            position: absolute;
            left: 0;
            top: 0;
            width: 3.5mm;
            height: 50mm;
        }
        /* Contained "Island" for the content */
        .content-island {
            width: 55mm; /* Narrower width to keep left/right elements close */
            margin: 8mm auto 0 auto; /* Centered horizontally, spaced from top */
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }
        .header-section {
            width: 100%;
            border-bottom: 0.2mm solid #000;
            padding-bottom: 1.2mm;
            margin-bottom: 1.5mm;
        }
        .patient-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .patient-name {
            font-size: 9.5pt; /* Robust and visible */
            font-weight: 800;
            color: #000;
            margin: 0;
            line-height: 1.1;
            word-wrap: break-word;
        }
        .meta-row {
            font-size: 6.5pt; 
            color: #000;
            margin-top: 0.8mm;
            font-weight: 600;
        }
        .id-tag {
            background: #000;
            color: #fff;
            padding: 0.15mm 1mm;
            font-weight: bold;
            border-radius: 0.6mm;
            font-size: 7.5pt;
        }
        .qr-cell {
            width: 14mm;
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
            vertical-align: top;
        }
        .qr-code {
            width: 12.5mm; 
            height: 12.5mm;
            display: inline-block;
        }
        .tests-area {
            margin-top: 1mm;
            width: 100%;
        }
        .tests-label {
            font-weight: bold;
            font-size: 6pt;
            color: #555;
            text-transform: uppercase;
            margin-bottom: 0.5mm;
            display: block;
        }
        .tests-list {
            font-size: 8.5pt; /* Robust and visible */
            font-weight: 700;
            line-height: 1.25;
            word-wrap: break-word;
            color: #000;
        }
        .footer-area {
            margin-top: 1.5mm;
            border-top: 0.15mm solid #444;
            padding-top: 1.2mm;
            width: 100%;
        }
        .sample-badge {
            display: inline-block;
            padding: 0.4mm 1.2mm;
            border: 0.3mm solid #000;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 1mm;
            background: #fafafa;
        }
        .iso-info {
            font-size: 6pt;
            color: #444;
            margin-top: 0.8mm;
            display: block;
            font-weight: 600;
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
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
    @endphp

    <div class="label-wrapper {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
        <div class="tube-indicator" style="background-color: {{ $color }};"></div>

        <div class="content-island">
            <div class="header-section">
                <table class="patient-table">
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="patient-name">{{ $patient->name }}</div>
                            <div class="meta-row">
                                <span class="id-tag">#{{ $reservation->id }}</span> | 
                                <strong>{{ $sex }} | {{ $age }}</strong>
                            </div>
                            <div class="meta-row" style="color: #444;">
                                {{ $patient->birth_date ?? '00-00-0000' }} | {{ \Carbon\Carbon::parse($reservation->analysis_date)->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="qr-cell">
                            @if($barcode)
                                <img src="{{ $barcode }}" class="qr-code">
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="tests-area">
                <span class="tests-label">TESTS</span>
                <div class="tests-list">
                    @foreach($analyses as $ra)
                        {{ $ra->analyse->name }}{{ !$loop->last ? ' • ' : '' }}
                    @endforeach
                </div>
            </div>

            <div class="footer-area">
                <div class="sample-badge" style="border-left: 1.5mm solid {{ $color }};">
                    {{ strtoupper($sampleType ?: 'GENERAL') }}
                </div>
                <span class="iso-info">
                    ISO 15189 | COLL: {{ \Carbon\Carbon::parse($reservation->time)->format('H:i') }}
                </span>
            </div>
        </div>
    </div>
    @endforeach
</body>
</html>
