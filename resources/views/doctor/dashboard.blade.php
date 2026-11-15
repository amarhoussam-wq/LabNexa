<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.physician_dashboard') }} — {{ __('messages.lab_name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons & Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ══════════════════════════════════════════════════════════════════════
           LabNexa — Clinical Physician Portal (Doctor Console)
           Precision Clinical UI • Linear / Stripe Health Grade
        ══════════════════════════════════════════════════════════════════════ */
        :root {
            --doc-navy: #0B1120;
            --doc-sidebar-bg: #0F172A;
            --doc-primary: #0066FF;
            --doc-emerald: #10B981;
            --doc-amber: #F59E0B;
            --doc-bg: #F8FAFC;
            --doc-surface: #FFFFFF;
            --doc-text: #0F172A;
            --doc-text-muted: #64748B;
            --doc-border: #E2E8F0;
            --sidebar-width: 270px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --shadow-subtle: 0 1px 3px rgba(0,0,0,0.04);
            --shadow-card: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal'" : "'Inter'" }}, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--doc-bg);
            color: var(--doc-text);
            min-height: 100vh;
            display: flex;
        }

        /* ── Doctor Sidebar ── */
        .doctor-sidebar {
            width: var(--sidebar-width);
            background: var(--doc-sidebar-bg);
            min-height: 100vh;
            padding: 28px 20px;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            border-inline-end: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 1000;
        }

        [dir="rtl"] .doctor-sidebar {
            left: auto;
            right: 0;
        }

        .doctor-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 24px;
            margin-bottom: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
            color: #fff;
        }

        .doctor-brand img {
            height: 44px;
            width: auto;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .doctor-brand h2 {
            font-size: 1.15rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .nav-item-doctor {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 16px;
            border-radius: var(--radius-md);
            font-size: 0.92rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.2s ease;
            margin-bottom: 6px;
        }

        .nav-item-doctor:hover,
        .nav-item-doctor.active {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .nav-item-doctor.active {
            background: linear-gradient(135deg, rgba(0, 102, 255, 0.25) 0%, rgba(0, 102, 255, 0.1) 100%);
            border: 1px solid rgba(0, 102, 255, 0.35);
            color: #fff;
        }

        .doctor-profile-box {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .doctor-profile-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: var(--radius-md);
            padding: 14px;
            margin-bottom: 12px;
        }

        .doctor-avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--doc-primary), #0D9488);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
        }

        .btn-doctor-logout {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            border-radius: var(--radius-md);
            background: rgba(239, 68, 68, 0.12);
            color: #F87171;
            border: 1px solid rgba(239, 68, 68, 0.25);
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-doctor-logout:hover {
            background: #EF4444;
            color: #fff;
        }

        /* ── Main Content ── */
        .doctor-main {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 36px 40px;
        }

        [dir="rtl"] .doctor-main {
            margin-left: 0;
            margin-right: var(--sidebar-width);
        }

        .doctor-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 36px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .doctor-heading h1 {
            font-size: 1.85rem;
            font-weight: 900;
            color: var(--doc-text);
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .doctor-heading p {
            font-size: 0.95rem;
            color: var(--doc-text-muted);
            margin: 0;
        }

        .lang-pill-doc {
            display: flex;
            background: #fff;
            border: 1px solid var(--doc-border);
            border-radius: 100px;
            padding: 3px;
            gap: 2px;
            box-shadow: var(--shadow-subtle);
        }

        .lang-pill-doc a {
            padding: 6px 14px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 100px;
            text-decoration: none;
            color: var(--doc-text-muted);
            transition: all 0.2s;
        }

        .lang-pill-doc a.active {
            background: var(--doc-primary);
            color: #fff;
        }

        /* Stats Cards */
        .doctor-stat-card {
            background: #fff;
            border-radius: var(--radius-lg);
            padding: 26px;
            border: 1px solid var(--doc-border);
            box-shadow: var(--shadow-card);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .doctor-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.08);
            border-color: rgba(0, 102, 255, 0.3);
        }

        .doc-stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 16px;
        }

        .doc-stat-icon.blue { background: #EBF3FF; color: var(--doc-primary); }
        .doc-stat-icon.amber { background: #FFFBEB; color: var(--doc-amber); }
        .doc-stat-icon.emerald { background: #ECFDF5; color: var(--doc-emerald); }

        .doc-stat-num {
            font-size: 2.3rem;
            font-weight: 900;
            color: var(--doc-text);
            margin-bottom: 4px;
            line-height: 1;
            letter-spacing: -0.5px;
        }

        .doc-stat-label {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--doc-text-muted);
        }

        /* Patients Data Table */
        .table-card-modern {
            background: #fff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--doc-border);
            box-shadow: var(--shadow-card);
            margin-top: 32px;
            overflow: hidden;
        }

        .table-card-header {
            padding: 22px 28px;
            border-bottom: 1px solid var(--doc-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-card-header h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--doc-text);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-doctor-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-doctor-modern thead th {
            background: #F8FAFC;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--doc-text-muted);
            padding: 16px 24px;
            border-bottom: 1px solid var(--doc-border);
        }

        .table-doctor-modern tbody td {
            padding: 18px 24px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .table-doctor-modern tbody tr:last-child td {
            border-bottom: none;
        }

        .table-doctor-modern tbody tr:hover {
            background: #F8FAFC;
        }

        .badge-doc-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .badge-doc-status.status-ready { background: #ECFDF5; color: #059669; }
        .badge-doc-status.status-pending { background: #FFFBEB; color: #D97706; }

        .action-pill-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--radius-md);
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .action-pill-btn.primary {
            background: var(--doc-primary);
            color: #fff;
        }
        .action-pill-btn.primary:hover {
            background: #0052CC;
            transform: translateY(-1px);
            color: #fff;
        }

        .action-pill-btn.secondary {
            background: #F1F5F9;
            color: var(--doc-text);
            border-color: var(--doc-border);
        }
        .action-pill-btn.secondary:hover {
            background: #E2E8F0;
            color: var(--doc-text);
        }

        .action-pill-btn.emerald {
            background: #10B981;
            color: #fff;
        }
        .action-pill-btn.emerald:hover {
            background: #059669;
            color: #fff;
        }

        @media (max-width: 992px) {
            .doctor-sidebar {
                display: none;
            }
            .doctor-main {
                margin: 0;
                width: 100%;
                padding: 24px 16px;
            }
        }
    </style>
</head>

<body>

    <!-- Doctor Sidebar -->
    <aside class="doctor-sidebar">
        <a href="{{ route('doctor.dashboard') }}" class="doctor-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/822/822143.png'">
            <div>
                <h2>{{ __('messages.lab_name') }}</h2>
                <span style="font-size:0.75rem; color:#60A5FA; font-weight:700;">{{ __('messages.physician_portal') }}</span>
            </div>
        </a>

        <nav class="flex-grow-1">
            <a href="{{ route('doctor.dashboard') }}" class="nav-item-doctor active">
                <i class="fas fa-stethoscope"></i>
                <span>{{ __('messages.dashboard') }}</span>
            </a>
            <a href="{{ route('home') }}" class="nav-item-doctor" target="_blank">
                <i class="fas fa-globe"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'موقع المخبر' : 'Site Public' }}</span>
            </a>
        </nav>

        <div class="doctor-profile-box">
            <div class="doctor-profile-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="doctor-avatar-circle">Dr</div>
                    <div style="overflow:hidden;">
                        <strong style="display:block; color:#fff; font-size:0.9rem; text-truncate:ellipsis;">{{ $doctor->name }}</strong>
                        <span style="font-size:0.75rem; color:rgba(255,255,255,0.5);">{{ $doctor->specialty ?? 'Médecin' }}</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('doctor.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-doctor-logout">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                    <span>{{ __('messages.logout') }}</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Clinical Workspace -->
    <main class="doctor-main">
        <header class="doctor-topbar">
            <div class="doctor-heading">
                <h1>{{ __('messages.welcome_doctor', ['name' => $doctor->name]) }}</h1>
                <p>{{ __('messages.doctor_dashboard_subtitle') }}</p>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="date-badge text-muted small d-none d-md-flex align-items-center gap-2" style="background:#fff; padding:8px 16px; border-radius:100px; border:1px solid var(--doc-border);">
                    <i class="far fa-calendar-alt text-primary"></i>
                    <span>{{ \Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('l j F Y') }}</span>
                </div>

                <div class="lang-pill-doc">
                    <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() == 'ar' ? 'active' : '' }}">العربية</a>
                    <a href="{{ route('lang.switch', 'fr') }}" class="{{ app()->getLocale() == 'fr' ? 'active' : '' }}">Français</a>
                </div>
            </div>
        </header>

        <!-- Stats Overview Cards -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="doctor-stat-card">
                    <div class="doc-stat-icon blue"><i class="fas fa-user-injured"></i></div>
                    <div class="doc-stat-num">{{ $stats['total_patients'] }}</div>
                    <div class="doc-stat-label">{{ __('messages.referred_patients') }}</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="doctor-stat-card">
                    <div class="doc-stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
                    <div class="doc-stat-num">{{ $stats['pending_results'] }}</div>
                    <div class="doc-stat-label">{{ __('messages.pending_results') }}</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="doctor-stat-card">
                    <div class="doc-stat-icon emerald"><i class="fas fa-circle-check"></i></div>
                    <div class="doc-stat-num">{{ $stats['ready_results'] }}</div>
                    <div class="doc-stat-label">{{ __('messages.ready_results') }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Referrals Activity Table -->
        <div class="table-card-modern">
            <div class="table-card-header">
                <h3><i class="fas fa-users-medical" style="color:var(--doc-primary);"></i> {{ __('messages.recent_referred_activities') }}</h3>
            </div>

            <div class="table-responsive">
                <table class="table-doctor-modern">
                    <thead>
                        <tr>
                            <th>{{ __('messages.patient_name') }}</th>
                            <th>{{ __('messages.analyses') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th style="text-align:end;">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $res)
                            <tr>
                                <td>
                                    <strong style="display:block; color:var(--doc-text);">{{ $res->patient->name }}</strong>
                                    <span style="font-size:0.8rem; color:var(--doc-text-muted);">{{ $res->patient->phone }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 280px; font-weight:600; color:var(--doc-text);">
                                        {{ $res->reservationAnalyses->map(fn($ra) => $ra->analyse->name)->implode(', ') }}
                                    </div>
                                </td>
                                <td>
                                    <span style="color:var(--doc-text-muted); font-size:0.9rem;">
                                        <i class="far fa-calendar me-1"></i> {{ $res->analysis_date }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $isReady = in_array($res->status, ['ready', 'completed']) || $res->result_file_path;
                                    @endphp
                                    <span class="badge-doc-status {{ $isReady ? 'status-ready' : 'status-pending' }}">
                                        <i class="fas {{ $isReady ? 'fa-check' : 'fa-clock' }}"></i>
                                        {{ $isReady ? __('messages.ready') : __('messages.' . $res->status) }}
                                    </span>
                                </td>
                                <td style="text-align:end;">
                                    <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                                        <!-- Details -->
                                        <a href="{{ route('doctor.patient.result', $res->id) }}" class="action-pill-btn primary" title="{{ __('messages.view_details') }}">
                                            <i class="fas fa-eye"></i> <span>{{ __('messages.view_details') ?? 'Détails' }}</span>
                                        </a>

                                        <!-- Eligibility Report — same tab so back button works -->
                                        <a href="{{ route('doctor.eligibility.report', $res->id) }}" class="action-pill-btn secondary" title="{{ __('messages.eligibility_report') }}">
                                            <i class="fas fa-file-medical"></i> <span>{{ __('messages.view_report') }}</span>
                                        </a>

                                        <!-- Results -->
                                        @php
                                            $hasDbResults = $res->reservationAnalyses->contains(fn($ra) => !empty($ra->result_value));
                                        @endphp

                                        @if($res->result_file_path)
                                            <a href="{{ route('doctor.download', $res->id) }}" target="_blank" class="action-pill-btn emerald">
                                                <i class="fas fa-file-pdf"></i> <span>{{ __('messages.view_result') }}</span>
                                            </a>
                                        @elseif($hasDbResults)
                                            <a href="{{ route('report.view', ['id' => $res->id, 'type' => 'confirmed', 'report_type' => 'results']) }}" class="action-pill-btn emerald">
                                                <i class="fas fa-microscope"></i> <span>{{ app()->getLocale() == 'ar' ? 'تقرير النتائج' : 'Résultats' }}</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-folder-open text-muted opacity-25" style="font-size:3rem; margin-bottom:12px; display:block;"></i>
                                    <p class="text-muted mb-0">{{ __('messages.no_referred_patients_found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reservations->hasPages())
                <div class="p-3 bg-light border-top">
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
