<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.patient_dashboard') }} — {{ __('messages.lab_name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons & Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ══════════════════════════════════════════════════════════════════════
           LabNexa — Apple Health Style Patient Portal Dashboard
           Clean • Reassuring • Precision Health Care
        ══════════════════════════════════════════════════════════════════════ */
        :root {
            --patient-primary: #0066FF;
            --patient-primary-dark: #0052CC;
            --patient-light: #EBF3FF;
            --patient-emerald: #10B981;
            --patient-emerald-light: #ECFDF5;
            --patient-amber: #F59E0B;
            --patient-amber-light: #FFFBEB;
            --patient-bg: #F8FAFC;
            --patient-surface: #FFFFFF;
            --patient-text: #0F172A;
            --patient-text-muted: #64748B;
            --patient-border: #E2E8F0;
            --radius-md: 14px;
            --radius-lg: 22px;
            --shadow-subtle: 0 1px 3px rgba(0,0,0,0.04);
            --shadow-card: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 4px 6px -2px rgba(15, 23, 42, 0.02);
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal'" : "'Inter'" }}, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--patient-bg);
            color: var(--patient-text);
            min-height: 100vh;
            padding-bottom: 60px;
        }

        /* ── Top Navbar ── */
        .patient-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--patient-border);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .patient-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--patient-text);
            font-weight: 800;
            font-size: 1.25rem;
        }

        .patient-brand img {
            height: 40px;
            width: auto;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 102, 255, 0.15);
        }

        .lang-pill-patient {
            display: flex;
            background: var(--patient-bg);
            border: 1px solid var(--patient-border);
            border-radius: 100px;
            padding: 3px;
            gap: 2px;
        }

        .lang-pill-patient a {
            padding: 5px 14px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 100px;
            text-decoration: none;
            color: var(--patient-text-muted);
            transition: all 0.2s;
        }

        .lang-pill-patient a.active {
            background: var(--patient-primary);
            color: #fff;
        }

        .btn-patient-logout {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            font-weight: 700;
            background: #FEE2E2;
            color: #DC2626;
            border: 1px solid #FECACA;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-patient-logout:hover {
            background: #DC2626;
            color: #fff;
        }

        /* ── Hero Welcome Banner ── */
        .patient-welcome-banner {
            background: linear-gradient(135deg, #0B1120 0%, #1E293B 100%);
            border-radius: var(--radius-lg);
            padding: 32px 36px;
            color: #fff;
            margin: 28px 0;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: var(--shadow-card);
        }

        .patient-welcome-banner::after {
            content: '';
            position: absolute;
            top: -40%;
            right: -10%;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(0, 102, 255, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .patient-card-modern {
            background: #fff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--patient-border);
            box-shadow: var(--shadow-card);
            margin-bottom: 24px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .patient-card-header {
            padding: 20px 26px;
            border-bottom: 1px solid var(--patient-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--patient-text);
        }

        .status-badge-patient {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .status-badge-patient.status-ready,
        .status-badge-patient.status-completed { background: var(--patient-emerald-light); color: var(--patient-emerald); }
        .status-badge-patient.status-booked,
        .status-badge-patient.status-pending   { background: var(--patient-amber-light); color: #D97706; }
        .status-badge-patient.status-blocked   { background: #FEE2E2; color: #DC2626; }

        .btn-patient-cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 24px;
            border-radius: var(--radius-md);
            font-weight: 800;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.25s;
        }

        .btn-patient-cta.primary {
            background: linear-gradient(135deg, var(--patient-primary) 0%, #0052CC 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(0, 102, 255, 0.25);
        }

        .btn-patient-cta.primary:hover {
            box-shadow: 0 8px 25px rgba(0, 102, 255, 0.4);
            transform: translateY(-2px);
            color: #fff;
        }

        .btn-patient-cta.emerald {
            background: linear-gradient(135deg, var(--patient-emerald) 0%, #059669 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
        }

        .btn-patient-cta.emerald:hover {
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            transform: translateY(-2px);
            color: #fff;
        }

        .analysis-instruction-row {
            padding: 16px 26px;
            border-bottom: 1px solid #F1F5F9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .analysis-instruction-row:last-child {
            border-bottom: none;
        }

        .prep-callout {
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.88rem;
            color: #92400E;
            margin-top: 6px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .alert-card-smart {
            background: #fff;
            border: 1px solid var(--patient-border);
            border-radius: var(--radius-md);
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            box-shadow: var(--shadow-subtle);
        }
    </style>
</head>

<body>

    <!-- Patient Top Navbar -->
    <nav class="patient-navbar">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('home') }}" class="patient-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/822/822143.png'">
                <span>{{ __('messages.lab_name') }}</span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-md-flex align-items-center gap-2" style="font-size:0.9rem; color:var(--patient-text-muted);">
                    <i class="fas fa-user-circle text-primary"></i>
                    <span>{{ __('messages.connected_as') }}: <strong>{{ $patient->name }}</strong></span>
                </div>

                <!-- Language Switcher -->
                <div class="lang-pill-patient">
                    <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() == 'ar' ? 'active' : '' }}">العربية</a>
                    <a href="{{ route('lang.switch', 'fr') }}" class="{{ app()->getLocale() == 'fr' ? 'active' : '' }}">Français</a>
                </div>

                <!-- Logout -->
                <form action="{{ route('patient.logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-patient-logout">
                        <i class="fas fa-arrow-right-from-bracket"></i>
                        <span class="d-none d-md-inline">{{ __('messages.logout') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <!-- Welcome Hero Banner -->
        <div class="patient-welcome-banner">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span style="font-size:0.8rem; font-weight:800; color:#60A5FA; background:rgba(0,102,255,0.2); padding:4px 12px; border-radius:100px; display:inline-block; margin-bottom:12px;">
                        <i class="fas fa-heartbeat me-1"></i> {{ __('messages.patient_portal') }}
                    </span>
                    <h1 style="font-size:2rem; font-weight:900; margin-bottom:8px; letter-spacing:-0.5px;">
                        {{ __('messages.welcome_patient', ['name' => $patient->name]) }}
                    </h1>
                    <p style="color:var(--patient-text-muted); font-size:1rem; margin:0; max-width:550px;">
                        {{ __('messages.portal_dashboard_desc') }}
                    </p>
                </div>

                <div class="col-lg-5 text-lg-end">
                    <div class="d-inline-flex flex-wrap gap-2 justify-content-lg-end">
                        @if($reservation && ($reservation->result_file_path || $hasDbResults))
                            <a href="{{ $reservation->result_file_path ? route('patient.download', $reservation->id) : route('reservation.pdf', ['id' => $reservation->id, 'type' => 'confirmed']) }}" 
                               target="_blank"
                               class="btn-patient-cta emerald">
                                <i class="fas fa-file-invoice"></i>
                                <span>{{ __('messages.medical_results') }} (PDF)</span>
                            </a>
                        @endif

                        @if($reservation)
                            <a href="{{ route('patient.report.eligibility', ['id' => $reservation->id, 'report_type' => 'eligibility']) }}" 
                               target="_blank"
                               class="btn-patient-cta primary">
                                <i class="fas fa-file-medical"></i>
                                <span>{{ __('messages.eligibility_report') }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(!$reservation)
            <div class="patient-card-modern p-5 text-center">
                <i class="fas fa-folder-open text-muted opacity-25" style="font-size:4rem; margin-bottom:16px; display:block;"></i>
                <h4 style="font-weight:800; color:var(--patient-text);">{{ __('messages.no_results_yet') ?? 'No results found in your account yet.' }}</h4>
                <p style="color:var(--patient-text-muted);">{{ __('messages.results_will_appear_here') ?? 'Your medical reports will appear here once they are ready.' }}</p>
            </div>
        @else

        <!-- Multi-visit switcher if available -->
        @if($reservations->count() > 1)
        <div class="patient-card-modern p-3 mb-4">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span style="font-size:0.85rem; font-weight:800; color:var(--patient-text-muted); text-transform:uppercase;">
                    <i class="fas fa-history me-1"></i> {{ __('messages.visit_history') ?? 'Historique' }}:
                </span>
                @foreach($reservations as $res)
                    <a href="?visit={{ $res->id }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ (isset($reservation) && $reservation->id == $res->id) ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ $res->analysis_date }} (#{{ $res->id }})
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="row g-4">
            <!-- Left Column: Visit details & Results -->
            <div class="col-lg-8">
                
                <!-- Visit Details -->
                <div class="patient-card-modern">
                    <div class="patient-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-calendar-check text-primary"></i>
                            <span>{{ __('messages.visit_details') }}</span>
                        </div>
                        <span class="status-badge-patient status-{{ $reservation->status }}">
                            <i class="fas fa-circle-dot"></i>
                            {{ __('messages.' . $reservation->status) }}
                        </span>
                    </div>

                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <span style="font-size:0.8rem; color:var(--patient-text-muted); display:block;">{{ __('messages.visit_id') }}</span>
                                <strong style="font-size:1.1rem; color:var(--patient-text);">#{{ $reservation->id }}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span style="font-size:0.8rem; color:var(--patient-text-muted); display:block;">{{ __('messages.date') }}</span>
                                <strong style="font-size:1.1rem; color:var(--patient-text);">{{ $reservation->analysis_date }}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span style="font-size:0.8rem; color:var(--patient-text-muted); display:block;">{{ __('messages.time') }}</span>
                                <strong style="font-size:1.1rem; color:var(--patient-text);">{{ $reservation->time }}</strong>
                            </div>
                            @if($reservation->doctor)
                            <div class="col-6 col-md-3">
                                <span style="font-size:0.8rem; color:var(--patient-text-muted); display:block;">{{ __('messages.referred_by') }}</span>
                                <strong style="font-size:1rem; color:var(--patient-primary);">Dr. {{ $reservation->doctor->name }}</strong>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Medical Results Download Box -->
                <div class="patient-card-modern">
                    <div class="patient-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-file-medical text-success"></i>
                            <span>{{ __('messages.medical_results') }}</span>
                        </div>
                    </div>

                    <div class="p-4">
                        @if($reservation->result_file_path || $hasDbResults)
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 p-4 rounded-4" style="background:#F0FDF4; border:1px solid #BBF7D0;">
                                <div>
                                    <h4 style="font-weight:900; color:#166534; font-size:1.2rem; margin-bottom:4px;">
                                        <i class="fas fa-circle-check me-1"></i> {{ __('messages.results_ready_title') }}
                                    </h4>
                                    <p style="color:#15803D; margin:0; font-size:0.95rem;">
                                        {{ __('messages.results_ready_desc') }}
                                    </p>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @if($reservation->result_file_path)
                                        <a href="{{ route('patient.download', $reservation->id) }}" target="_blank" class="btn-patient-cta emerald">
                                            <i class="fas fa-file-arrow-down"></i>
                                            <span>{{ __('messages.view_and_download') }}</span>
                                        </a>
                                    @endif

                                    @if($hasDbResults)
                                        <a href="{{ route('reservation.pdf', ['id' => $reservation->id, 'type' => 'confirmed']) }}" target="_blank" class="btn-patient-cta primary">
                                            <i class="fas fa-file-pdf"></i>
                                            <span>{{ __('messages.medical_report_pdf') }}</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-hourglass-half text-warning" style="font-size:2.5rem; margin-bottom:12px; display:block;"></i>
                                <h5 style="font-weight:800; color:var(--patient-text);">{{ __('messages.results_pending_title') }}</h5>
                                <p style="color:var(--patient-text-muted); margin:0;">{{ __('messages.results_pending_desc') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Preparation Instructions for analyses in reservation -->
                <div class="patient-card-modern">
                    <div class="patient-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-clipboard-list text-warning"></i>
                            <span>{{ __('messages.prep_and_instructions') }}</span>
                        </div>
                    </div>

                    <div>
                        @foreach($reservation->reservationAnalyses as $resAnalysis)
                            <div class="analysis-instruction-row">
                                <div style="flex:1;">
                                    <strong style="color:var(--patient-text); font-size:1rem; display:block;">{{ $resAnalysis->analyse->name }}</strong>
                                    @if($resAnalysis->analyse->preparation_instructions)
                                        <div class="prep-callout">
                                            <i class="fas fa-triangle-exclamation mt-1"></i>
                                            <span>{{ $resAnalysis->analyse->preparation_instructions }}</span>
                                        </div>
                                    @else
                                        <span style="font-size:0.85rem; color:var(--patient-text-muted); display:block; margin-top:4px;">
                                            {{ __('messages.no_specific_instructions') }}
                                        </span>
                                    @endif
                                </div>
                                <span class="badge rounded-pill bg-light text-dark px-3 py-2 border" style="font-size:0.8rem;">
                                    {{ $resAnalysis->analyse->category ?? __('messages.analysis') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Right Column: Reports & Notifications -->
            <div class="col-lg-4">
                
                <!-- Quick Reports Actions -->
                <div class="patient-card-modern">
                    <div class="patient-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-eye text-primary"></i>
                            <span>{{ __('messages.visualize_reports') }}</span>
                        </div>
                    </div>
                    <div class="p-3 d-grid gap-2">
                        <a href="{{ route('report.view', ['id' => $reservation->id, 'type' => 'confirmed']) }}" target="_blank" class="btn btn-outline-secondary text-start p-3 rounded-3 d-flex align-items-center justify-content-between">
                            <span class="fw-bold"><i class="fas fa-file-invoice text-primary me-2"></i> {{ __('messages.appointment_confirmation') }}</span>
                            <i class="fas fa-arrow-up-right-from-square small text-muted"></i>
                        </a>

                        @if($diagIsChecked)
                            <a href="{{ route('report.view', ['id' => $reservation->id, 'report_type' => 'eligibility']) }}" target="_blank" class="btn btn-outline-secondary text-start p-3 rounded-3 d-flex align-items-center justify-content-between">
                                <span class="fw-bold"><i class="fas fa-notes-medical text-primary me-2"></i> {{ __('messages.eligibility_report') }}</span>
                                <i class="fas fa-arrow-up-right-from-square small text-muted"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Notifications & Smart Reminders -->
                <div class="patient-card-modern">
                    <div class="patient-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-bell text-warning"></i>
                            <span>{{ __('messages.notifications_alerts') }}</span>
                        </div>
                    </div>
                    <div class="p-3">
                        @if($reservation->reminders->count() > 0)
                            @foreach($reservation->reminders as $reminder)
                                <div class="alert-card-smart" style="background:#EFF6FF; border-color:#BFDBFE;">
                                    <i class="fas fa-info-circle text-primary mt-1"></i>
                                    <div>
                                        <div style="font-size:0.75rem; color:var(--patient-text-muted); font-weight:700;">{{ $reminder->sent_at }}</div>
                                        <div style="font-size:0.9rem; color:var(--patient-text); line-height:1.4;">{{ $reminder->message }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3 text-muted small">
                                <i class="far fa-bell-slash mb-2" style="font-size:1.5rem; opacity:0.3; display:block;"></i>
                                {{ __('messages.no_recent_notifications') }}
                            </div>
                        @endif

                        <!-- Health Tip -->
                        <div class="alert-card-smart" style="background:#F0FDF4; border-color:#BBF7D0;">
                            <i class="fas fa-heart-pulse text-success mt-1"></i>
                            <div>
                                <strong style="display:block; color:#15803D; font-size:0.88rem;">{{ __('messages.smart_tip') }}</strong>
                                <span style="font-size:0.85rem; color:#166534;">{{ __('messages.drink_water_tip') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        @endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
