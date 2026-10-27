<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.patient_dashboard') }} - {{ __('messages.lab_name') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #3b82f6; /* Modern Blue */
            --primary-dark: #2563eb;
            --primary-light: #eff6ff;
            --success-color: #10b981;
            --success-light: #ecfdf5;
            --warning-color: #f59e0b;
            --warning-light: #fffbeb;
            --danger-color: #ef4444;
            --text-main: #334155;
            --text-muted: #64748b;
            --bg-body: #f8fafc;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.05), 0 4px 6px -4px rgb(0 0 0 / 0.05);
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal', system-ui, sans-serif" : "'Outfit', system-ui, sans-serif" }};
            background-color: var(--bg-body);
            color: var(--text-main);
            padding-bottom: 50px;
        }

        .navbar-patient {
            background: white;
            box-shadow: var(--shadow-sm);
            padding: 18px 0;
            margin-bottom: 35px;
            border-bottom: 1px solid #e2e8f0;
        }

        .portal-logo {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary-color);
            text-decoration: none;
        }

        .card-custom {
            background: white;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--shadow-sm);
            margin-bottom: 25px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-custom:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
            border-color: rgba(59, 130, 246, 0.1);
        }

        .card-header-custom {
            padding: 22px 25px;
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.05rem;
        }

        .status-pill {
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .status-booked { background: #f1f5f9; color: #475569; }
        .status-ready { background: #dcfce7; color: #166534; }
        .status-completed { background: #e0f2fe; color: #075985; }
        .status-blocked { background: #fee2e2; color: #991b1b; }
        .status-warning { background: #fef3c7; color: #92400e; }

        .analysis-item {
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
        }

        .analysis-item:last-child { border-bottom: none; }

        .prep-instructions {
            background: var(--warning-light);
            border-left: 4px solid var(--warning-color);
            padding: 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-top: 12px;
            color: #78350f;
        }
        
        [dir="rtl"] .prep-instructions {
            border-left: none;
            border-right: 4px solid var(--warning-color);
        }

        .btn-download {
            background: var(--primary-color);
            color: white;
            border-radius: 10px;
            padding: 12px 22px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
        }

        .btn-download:hover {
            background: var(--primary-dark);
            color: white;
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }

        .info-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .info-value {
            font-weight: 700;
            font-size: 16px;
        }

        .alert-item {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 15px;
            border-left: 4px solid;
            display: flex;
            gap: 15px;
            background: white;
            box-shadow: var(--shadow-sm);
        }
        
        [dir="rtl"] .alert-item {
            border-left: none;
            border-right: 4px solid;
        }

        .alert-info { background: var(--primary-light); border-color: var(--primary-color); color: #1e3a8a; }
        .alert-success { background: var(--success-light); border-color: var(--success-color); color: #064e3b; }

        .logout-link {
            padding: 10px 20px;
            border-radius: 8px;
            color: var(--danger-color);
            background: #fef2f2;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .logout-link:hover {
            background: var(--danger-color);
            color: white;
            box-shadow: var(--shadow-sm);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.3;
        }
    </style>
</head>
<body>

    <nav class="navbar-patient">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('home') }}" class="portal-logo">
                <i class="fas fa-microscope me-2"></i> {{ __('messages.lab_name') }}
            </a>
            <div class="d-flex align-items-center gap-3">
                    {{ __('messages.connected_as') }}: <strong>{{ $patient->name }}</strong>
                </span>
                {{-- Language Switcher Pill --}}
                <div class="d-flex bg-white rounded-pill p-1 border shadow-sm" style="min-width: 100px;">
                    <a href="{{ route('lang.switch', 'ar') }}" 
                       class="flex-fill text-center rounded-pill px-2 py-1 text-decoration-none {{ app()->getLocale() == 'ar' ? 'bg-primary text-white fw-bold shadow-sm' : 'text-muted' }}"
                       style="font-size: 11px; transition: all 0.3s;">
                        العربية
                    </a>
                    <a href="{{ route('lang.switch', 'fr') }}" 
                       class="flex-fill text-center rounded-pill px-2 py-1 text-decoration-none {{ app()->getLocale() == 'fr' ? 'bg-primary text-white fw-bold shadow-sm' : 'text-muted' }}"
                       style="font-size: 11px; transition: all 0.3s;">
                        FR
                    </a>
                </div>
                <form action="{{ route('patient.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-link border-0">
                        <i class="fas fa-sign-out-alt"></i> {{ __('messages.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <!-- Welcome Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1">{{ __('messages.welcome_patient', ['name' => $patient->name]) }}</h2>
                <p class="text-muted">{{ __('messages.portal_dashboard_desc') }}</p>
            </div>
            
            <!-- Quick Actions -->
            <div class="col-md-6">
                <div class="d-flex gap-3 justify-content-md-end">
                    @if($reservation && ($reservation->result_file_path || $hasDbResults))
                        <a href="{{ $reservation->result_file_path ? route('patient.download', $reservation->id) : route('reservation.pdf', ['id' => $reservation->id, 'type' => 'confirmed']) }}" 
                           target="_blank"
                           class="btn shadow-sm btn-success rounded-4 p-3 d-flex align-items-center gap-3">
                            <i class="fas fa-file-invoice fa-2x"></i>
                            <div class="text-start">
                                <div class="fw-bold">{{ __('messages.medical_results') }}</div>
                                <div class="small opacity-75">{{ __('messages.view_and_download') }}</div>
                            </div>
                        </a>
                    @endif

                    @if($reservation)
                        <a href="{{ route('patient.report.eligibility', ['id' => $reservation->id, 'report_type' => 'eligibility']) }}" 
                           target="_blank"
                           class="btn shadow-sm btn-primary rounded-4 p-3 d-flex align-items-center gap-3">
                            <i class="fas fa-notes-medical fa-2x"></i>
                            <div class="text-start">
                                <div class="fw-bold">{{ __('messages.eligibility_report') }}</div>
                                <div class="small opacity-75">{{ __('messages.view_report') }}</div>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if(!$reservation)
            <div class="card-custom">
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h5>{{ __('messages.no_results_yet') ?? 'No results found in your account yet.' }}</h5>
                    <p>{{ __('messages.results_will_appear_here') ?? 'Your medical reports will appear here once they are ready.' }}</p>
                </div>
            </div>
        @else
        
        <div class="row">
            @if($reservations->count() > 1)
            <div class="col-12 mb-4">
                <div class="card-custom p-3">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <span class="fw-bold text-muted small text-uppercase"><i class="fas fa-history me-1"></i> {{ __('messages.visit_history') ?? 'Visit History' }}:</span>
                        @foreach($reservations as $res)
                            <a href="?visit={{ $res->id }}" class="btn btn-sm {{ (isset($reservation) && $reservation->id == $res->id) ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">
                                {{ $res->analysis_date }} (#{{ $res->id }})
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Left Side: Reservation Summary & Results -->
            <div class="col-lg-8">
                
                <!-- Status & Visit Info -->
                <div class="card-custom">
                    <div class="card-header-custom">
                        <i class="fas fa-calendar-check text-primary"></i>
                        {{ __('messages.visit_details') }}
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-3 col-6">
                                <div class="info-label">{{ __('messages.visit_id') }}</div>
                                <div class="info-value">#{{ $reservation->id }}</div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-label">{{ __('messages.date') }}</div>
                                <div class="info-value">{{ $reservation->analysis_date }}</div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-label">{{ __('messages.time') }}</div>
                                <div class="info-value">{{ $reservation->time }}</div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-label">{{ __('messages.status') }}</div>
                                <div>
                                    <span class="status-pill status-{{ $reservation->status }}">
                                        {{ __('messages.' . $reservation->status) }}
                                    </span>
                                </div>
                            </div>
                            @if($reservation->doctor)
                            <div class="col-md-3 col-6">
                                <div class="info-label">{{ __('messages.referred_by') }}</div>
                                <div class="info-value text-primary">
                                    <i class="fas fa-user-md me-1"></i>
                                    {{ $reservation->doctor->name }}
                                </div>
                                <div class="text-muted small">{{ $reservation->doctor->specialty }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="card-custom">
                    <div class="card-header-custom">
                        <i class="fas fa-file-medical text-success"></i>
                        {{ __('messages.medical_results') }}
                    </div>
                    <div class="p-4">

                @if($reservation->result_file_path || $hasDbResults)
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 bg-light p-4 rounded-4">
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">{{ __('messages.results_ready_title') }}</h5>
                            <p class="text-muted mb-0 small">{{ __('messages.results_ready_desc') }}</p>
                        </div>
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            @if($reservation->result_file_path)
                                <a href="{{ route('patient.download', $reservation->id) }}" target="_blank" class="btn-download">
                                    <i class="fas fa-file-upload"></i>
                                    {{ __('messages.view_and_download') }}
                                </a>
                            @endif
                            
                            @if($hasDbResults)
                                <a href="{{ route('reservation.pdf', ['id' => $reservation->id, 'type' => 'confirmed']) }}" target="_blank" class="btn-download" style="background: var(--success-color);">
                                    <i class="fas fa-file-medical"></i>
                                    {{ __('messages.medical_report_pdf') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-hourglass-half"></i>
                        <h5>{{ __('messages.results_pending_title') }}</h5>
                        <p>{{ __('messages.results_pending_desc') }}</p>
                    </div>
                @endif
                    </div>
                </div>

                <!-- Preparation Section -->
                <div class="card-custom">
                    <div class="card-header-custom">
                        <i class="fas fa-clipboard-list text-warning"></i>
                        {{ __('messages.prep_and_instructions') }}
                    </div>
                    <div>
                        @foreach($reservation->reservationAnalyses as $resAnalysis)
                            <div class="analysis-item">
                                <div>
                                    <div class="fw-bold">{{ $resAnalysis->analyse->name }}</div>
                                    @if($resAnalysis->analyse->preparation_instructions)
                                        <div class="prep-instructions">
                                            <i class="fas fa-info-circle me-1"></i>
                                            {{ $resAnalysis->analyse->preparation_instructions }}
                                        </div>
                                    @else
                                        <div class="text-muted small mt-1">{{ __('messages.no_specific_instructions') }}</div>
                                    @endif
                                </div>
                                <span class="badge bg-light text-dark">{{ $resAnalysis->analyse->category ?? __('messages.analysis') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Right Side: Reports & Alerts -->
            <div class="col-lg-4">
                
                <!-- Detailed Reports -->
                <div class="card-custom">
                    <div class="card-header-custom">
                        <i class="fas fa-eye text-primary"></i>
                        {{ __('messages.visualize_reports') }}
                    </div>
                    <div class="p-4 d-grid gap-2">
                        <a href="{{ route('report.view', ['id' => $reservation->id, 'type' => 'confirmed']) }}" target="_blank" class="btn btn-outline-secondary w-100 rounded-3 text-start">
                            <i class="fas fa-file-invoice me-2 text-primary"></i> {{ __('messages.appointment_confirmation') }}
                        </a>
                        

                        @if($diagIsChecked)
                            <a href="{{ route('report.view', ['id' => $reservation->id, 'report_type' => 'eligibility']) }}" target="_blank" class="btn btn-outline-secondary w-100 rounded-3 text-start">
                                <i class="fas fa-notes-medical me-2 text-primary"></i> {{ __('messages.eligibility_report') }}
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Smart Alerts -->
                <div class="card-custom">
                    <div class="card-header-custom">
                        <i class="fas fa-bell text-warning"></i>
                        {{ __('messages.notifications_alerts') }}
                    </div>
                    <div class="p-4">
                        @if($reservation->reminders->count() > 0)
                            @foreach($reservation->reminders as $reminder)
                                <div class="alert-item alert-info">
                                    <i class="fas fa-info-circle mt-1"></i>
                                    <div>
                                        <div class="fw-bold small">{{ $reminder->sent_at }}</div>
                                        <div class="small">{{ $reminder->message }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state py-2">
                                <p class="small mb-0">{{ __('messages.no_recent_notifications') }}</p>
                            </div>
                        @endif
                        
                        <!-- Fixed Smart Instruction (Example) -->
                        <div class="alert-item alert-success">
                            <i class="fas fa-check-double mt-1"></i>
                            <div>
                                <div class="fw-bold small">{{ __('messages.smart_tip') }}</div>
                                <div class="small">{{ __('messages.drink_water_tip') }}</div>
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
