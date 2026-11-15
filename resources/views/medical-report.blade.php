<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.lab_name') }} — {{ $patientName }} (#{{ $reservation->id }})</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ══════════════════════════════════════════════════════════════════════
           LabNexa — High-Fidelity Medical & Eligibility Report View
           Native Crisp Arabic Typography • Zero PDF Glitches • Print Optimized
        ══════════════════════════════════════════════════════════════════════ */
        :root {
            --primary: #0066FF;
            --primary-dark: #0052CC;
            --primary-light: #EBF3FF;
            --emerald: #10B981;
            --emerald-light: #ECFDF5;
            --amber: #F59E0B;
            --amber-light: #FFFBEB;
            --red: #EF4444;
            --red-light: #FEE2E2;
            --slate-900: #0F172A;
            --slate-800: #1E293B;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748B;
            --slate-200: #E2E8F0;
            --slate-100: #F1F5F9;
            --slate-50:  #F8FAFC;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal', 'Cairo'" : "'Inter', 'Tajawal'" }}, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #EEF2F6;
            color: var(--slate-800);
            min-height: 100vh;
            padding: 24px;
            line-height: 1.6;
        }

        /* ── Sticky Interactive Toolbar ── */
        .report-toolbar {
            max-width: 900px;
            margin: 0 auto 20px;
            background: #fff;
            padding: 14px 24px;
            border-radius: 14px;
            border: 1px solid var(--slate-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
            position: sticky;
            top: 16px;
            z-index: 100;
        }

        .toolbar-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--slate-900);
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            font-size: 0.92rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 102, 255, 0.25);
            transition: all 0.2s;
        }

        .btn-print:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: 10px;
            background: var(--slate-100);
            color: var(--slate-700);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
            border: 1px solid var(--slate-200);
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: var(--slate-200);
            color: var(--slate-900);
        }

        /* ── Report Paper Container ── */
        .report-sheet {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--slate-200);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            overflow: hidden;
            padding: 44px;
        }

        /* Header */
        .report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 24px;
            border-bottom: 2px solid var(--primary);
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .brand-block {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-block img {
            height: 60px;
            width: auto;
            border-radius: 12px;
        }

        .brand-text h1 {
            font-size: 1.6rem;
            font-weight: 900;
            color: var(--slate-900);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .brand-text p {
            color: var(--slate-500);
            font-size: 0.85rem;
            margin: 2px 0 0;
        }

        .qr-tracker-box {
            text-align: center;
            background: #fff;
            padding: 8px 12px;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
        }

        .qr-tracker-box img {
            width: 85px;
            height: 85px;
            display: block;
            margin: 0 auto 4px;
        }

        .qr-tracker-box span {
            font-size: 9px;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Patient Banner */
        .patient-banner-card {
            background: linear-gradient(135deg, #0B1120 0%, #1E293B 100%);
            color: #fff;
            border-radius: 14px;
            padding: 24px 28px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .patient-info-left h2 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 8px;
            color: #fff;
        }

        .patient-meta-row {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 0.9rem;
            color: var(--slate-300);
        }

        .patient-meta-row span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .patient-meta-row i {
            color: #60A5FA;
        }

        .dossier-pill {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 18px;
            border-radius: 100px;
            font-weight: 800;
            font-size: 0.95rem;
            color: #fff;
            white-space: nowrap;
        }

        /* Sections */
        .section-header-block {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 28px 0 16px;
            padding-bottom: 8px;
            border-bottom: 1.5px solid var(--slate-200);
        }

        .section-header-block h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--slate-900);
            margin: 0;
        }

        .section-header-block i {
            color: var(--primary);
            font-size: 1.2rem;
        }

        /* Results & Status Tables */
        .report-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid var(--slate-200);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .report-table thead th {
            background: var(--slate-50);
            color: var(--slate-700);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 14px 18px;
            border-bottom: 1px solid var(--slate-200);
            text-align: start;
        }

        .report-table tbody td {
            padding: 14px 18px;
            border-bottom: 1px solid var(--slate-100);
            font-size: 0.95rem;
            vertical-align: middle;
        }

        .report-table tbody tr:last-child td {
            border-bottom: none;
        }

        .report-table tbody tr:nth-child(even) {
            background: #FAFAFC;
        }

        /* Badges */
        .status-pill-result {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 100px;
            font-size: 0.78rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .status-pill-result.qualified { background: var(--emerald-light); color: var(--emerald); }
        .status-pill-result.warning   { background: var(--amber-light); color: #B45309; }
        .status-pill-result.rejected  { background: var(--red-light); color: var(--red); }

        .clinical-alert-note {
            background: #FFFBEB;
            border-inline-start: 4px solid var(--amber);
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 0.88rem;
            color: #92400E;
            margin-top: 6px;
        }

        /* Footer & Signature */
        .report-footer {
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid var(--slate-200);
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 24px;
        }

        .signature-box {
            text-align: center;
            min-width: 220px;
        }

        .signature-line {
            width: 100%;
            height: 1px;
            background: var(--slate-300);
            margin: 50px 0 8px;
        }

        .signature-box span {
            font-size: 0.82rem;
            color: var(--slate-500);
            font-weight: 700;
        }

        .legal-notice {
            font-size: 0.8rem;
            color: var(--slate-400);
            line-height: 1.5;
            max-width: 450px;
        }

        /* ── Print Stylesheet ── */
        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .report-toolbar {
                display: none !important;
            }
            .report-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
        }
    </style>
</head>

<body>

    <!-- Sticky Floating Action Toolbar -->
    <div class="report-toolbar">
        <div class="toolbar-title">
            <i class="fas fa-file-medical text-primary" style="font-size: 1.25rem;"></i>
            <span>
                @if($isEligibilityReport)
                    {{ app()->getLocale() == 'ar' ? 'تقرير الأهلية الطبية السريرية' : 'Rapport d\'Éligibilité Médicale' }}
                @elseif($isResultReport)
                    {{ app()->getLocale() == 'ar' ? 'تقرير نتائج التحاليل المخبرية' : 'Rapport de Résultats d\'Analyses' }}
                @else
                    {{ app()->getLocale() == 'ar' ? 'بطاقة تأكيد الموعد والتعليمات' : 'Confirmation de Rendez-vous' }}
                @endif
            </span>
        </div>

        <div class="toolbar-actions">
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'طباعة / حفظ كـ PDF' : 'Imprimer / Sauvegarder PDF' }}</span>
            </button>
            <button onclick="window.history.back()" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'رجوع' : 'Retour' }}</span>
            </button>
        </div>
    </div>

    <!-- Official Report Document Paper -->
    <div class="report-sheet">
        
        <!-- Header -->
        <header class="report-header">
            <div class="brand-block">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/822/822143.png'">
                <div class="brand-text">
                    <h1>{{ __('messages.lab_name') }}</h1>
                    <p>{{ __('messages.portal_welcome_subtitle') ?? 'Système de Diagnostic Médical Avancé' }}</p>
                    <p style="font-size:0.75rem; color:var(--slate-400); margin-top:2px;">
                        <i class="fas fa-certificate" style="color:var(--amber);"></i> ISO 15189 Quality Accredited Laboratory
                    </p>
                </div>
            </div>

            @if($barcode)
            <div class="qr-tracker-box">
                <img src="{{ $barcode }}" alt="QR Tracker">
                <span>Scanner Ready</span>
            </div>
            @endif
        </header>

        <!-- Patient Info Card -->
        <div class="patient-banner-card">
            <div class="patient-info-left">
                <h2>{{ $patientName }}</h2>
                <div class="patient-meta-row">
                    <span><i class="fas fa-phone-alt"></i> {{ $patientPhone }}</span>
                    <span><i class="fas fa-calendar-day"></i> {{ \Carbon\Carbon::parse($appointmentDate)->translatedFormat('d F Y') }}</span>
                    <span><i class="fas fa-clock"></i> {{ $appointmentTime }}</span>
                    @if(isset($reservation->doctor))
                    <span><i class="fas fa-user-md"></i> Dr. {{ $reservation->doctor->name }}</span>
                    @endif
                </div>
            </div>

            <div class="dossier-pill">
                #{{ $reservation->id }} {{ __('messages.visit_number') ?? 'Dossier' }}
            </div>
        </div>

        {{-- ═══ CASE 1: ELIGIBILITY REPORT ═══ --}}
        @if($isEligibilityReport)
            <div class="section-header-block">
                <i class="fas fa-poll"></i>
                <h3>{{ __('messages.required_analyses_status') }}</h3>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">{{ __('messages.analysis') }}</th>
                        <th style="width: 20%;">{{ __('messages.status') }}</th>
                        <th style="width: 45%;">{{ __('messages.assessment_notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eligibilityResults as $res)
                        @php
                            $status = $res['status'] ?? 'eligible';
                            $statusClass = ($status == 'block' || $status == 'rejected') ? 'rejected' : (($status == 'warning') ? 'warning' : 'qualified');
                            $statusLabel = ($statusClass == 'rejected') ? __('messages.rejected_status') : (($statusClass == 'warning') ? __('messages.medical_warning') : __('messages.qualified'));
                        @endphp
                        <tr>
                            <td><strong>{{ $res['name'] }}</strong></td>
                            <td>
                                <span class="status-pill-result {{ $statusClass }}">
                                    <i class="fas {{ $statusClass == 'qualified' ? 'fa-check' : ($statusClass == 'warning' ? 'fa-triangle-exclamation' : 'fa-xmark') }}"></i>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>
                                @if(!empty($res['notes']))
                                    @foreach($res['notes'] as $note)
                                        <div class="clinical-alert-note">{{ $note }}</div>
                                    @endforeach
                                @else
                                    <span style="color:var(--emerald); font-weight:600; font-size:0.9rem;">
                                        <i class="fas fa-circle-check me-1"></i> {{ __('messages.met_all_protocol_requirements') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if(!empty($patientAnswers))
            <div class="section-header-block" style="margin-top:36px;">
                <i class="fas fa-clipboard-check"></i>
                <h3>{{ __('messages.detailed_patient_answers') }}</h3>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 55%;">{{ __('messages.question') }}</th>
                        <th style="width: 45%;">{{ __('messages.answer') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patientAnswers as $questionId => $answers)
                        @php $q = $answers->first()->question; @endphp
                        <tr>
                            <td>
                                <strong style="color:var(--slate-900);">{{ $q->question }}</strong>
                            </td>
                            <td>
                                @foreach($answers as $ans)
                                    <span class="status-pill-result" style="background:#F1F5F9; color:var(--slate-800); border:1px solid var(--slate-200);">
                                        {{ $ans->option->text }}
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

        {{-- ═══ CASE 2: MEDICAL TEST RESULTS REPORT ═══ --}}
        @elseif($isResultReport)
            <div class="section-header-block">
                <i class="fas fa-microscope"></i>
                <h3>{{ __('messages.medical_results') }}</h3>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">{{ app()->getLocale() == 'ar' ? 'التحليل المخبري' : 'Test / Analyse' }}</th>
                        <th style="width: 20%;">{{ app()->getLocale() == 'ar' ? 'النتيجة المقاسة' : 'Valeur Mesurée' }}</th>
                        <th style="width: 15%;">{{ app()->getLocale() == 'ar' ? 'الوحدة' : 'Unité' }}</th>
                        <th style="width: 30%;">{{ app()->getLocale() == 'ar' ? 'المعدل الطبيعي (Reference)' : 'Intervalle de Référence' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analyses as $analyse)
                        <tr>
                            <td>
                                <strong style="color:var(--slate-900);">{{ $analyse->name_fr ?? $analyse->name }}</strong>
                                @if(isset($analyse->code))
                                <span style="font-size:0.75rem; color:var(--slate-400); display:block;">Code: {{ $analyse->code }}</span>
                                @endif
                            </td>
                            <td>
                                <strong style="font-size:1.15rem; color:var(--primary);">
                                    {{ $analyse->pivot->result_value ?? '--' }}
                                </strong>
                            </td>
                            <td>
                                <span style="font-weight:600; color:var(--slate-600);">
                                    {{ $analyse->pivot->result_unit ?? ($analyse->unit ?? 'mg/dL') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:0.9rem; color:var(--slate-500);">
                                    {{ $analyse->pivot->reference_range ?? '--' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        {{-- ═══ CASE 3: APPOINTMENT CONFIRMATION & PROTOCOL ═══ --}}
        @else
            <div class="section-header-block">
                <i class="fas fa-list-check"></i>
                <h3>{{ __('messages.prep_and_instructions') }}</h3>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">{{ __('messages.analysis') }}</th>
                        <th style="width: 65%;">{{ app()->getLocale() == 'ar' ? 'تعليمات التحضير وشروط الصيام' : 'Instructions & Jeûne' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analyses as $analyse)
                        <tr>
                            <td>
                                <strong style="color:var(--slate-900); font-size:1rem;">{{ $analyse->name_fr ?? $analyse->name }}</strong>
                            </td>
                            <td>
                                @if(!empty($analyse->prep_ar) || !empty($analyse->prep_fr))
                                    <div class="clinical-alert-note" style="margin-top:0;">
                                        <i class="fas fa-info-circle me-1"></i>
                                        {{ app()->getLocale() == 'ar' ? ($analyse->prep_ar ?? $analyse->prep_fr) : ($analyse->prep_fr ?? $analyse->prep_ar) }}
                                    </div>
                                @else
                                    <span style="color:var(--slate-500); font-size:0.9rem;">
                                        {{ __('messages.no_specific_instructions') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Footer & Biologist Signature -->
        <footer class="report-footer">
            <div class="legal-notice">
                <p><strong>{{ __('messages.lab_name') }}</strong> — Système Informatique Hospitalier</p>
                <p>{{ app()->getLocale() == 'ar' ? 'وثيقة صادرة إلكترونياً ومصادق عليها. يمكن التحقق من صحتها عبر الـ QR Code أعلاه.' : 'Document généré électroniquement et certifié conforme. Validité vérifiable via le code QR ci-dessus.' }}</p>
                <p style="margin-top:4px; font-size:0.75rem; color:var(--slate-400);">
                    {{ app()->getLocale() == 'ar' ? 'تاريخ الإصدار' : 'Édité le' }}: {{ now()->translatedFormat('d F Y — H:i') }}
                </p>
            </div>

            <div class="signature-box">
                <div class="signature-line"></div>
                <span>{{ app()->getLocale() == 'ar' ? 'توقيع وختم الطبيب البيولوجي' : 'Cachet & Signature du Biologiste' }}</span>
            </div>
        </footer>

    </div>

</body>
</html>
