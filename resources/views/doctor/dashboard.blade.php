<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.physician_dashboard') }} - {{ __('messages.lab_name') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --doctor-primary: #0f172a; /* Deeper slate */
            --doctor-secondary: #3b82f6; /* Modern Blue */
            --doctor-accent: #e0f2fe; /* Light sky */
            --bg-light: #f8fafc;
            --sidebar-width: 280px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal', system-ui, sans-serif" : "'Outfit', system-ui, sans-serif" }};
            background-color: var(--bg-light);
            display: flex;
            color: #334155;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--doctor-primary);
            min-height: 100vh;
            color: white;
            padding: 30px 20px;
            position: fixed;
            left: 0;
            top: 0;
        }
        
        [dir="rtl"] .sidebar {
            left: auto;
            right: 0;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 40px;
        }
        
        [dir="rtl"] .main-content {
            margin-left: 0;
            margin-right: var(--sidebar-width);
        }

        .brand-logo {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link-medical {
            color: rgba(255,255,255,0.7);
            padding: 14px 18px;
            border-radius: 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
        }

        .nav-link-medical:hover, .nav-link-medical.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 28px;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover { 
            transform: translateY(-4px); 
            box-shadow: var(--shadow);
            border-color: rgba(59, 130, 246, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .icon-patients { background: #ebf8ff; color: #3182ce; }
        .icon-pending { background: #fffaf0; color: #dd6b20; }
        .icon-ready { background: #f0fff4; color: #38a169; }

        /* Data Table */
        .card-table {
            background: white;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: var(--shadow);
            margin-top: 30px;
            overflow: hidden;
        }

        .table thead th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: #64748b;
            padding: 18px 25px;
        }

        .table tbody td {
            padding: 18px 25px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .badge-status {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-ready { background: #c6f6d5; color: #22543d; }
        .status-pending { background: #feebc8; color: #744210; }

        .btn-view-result {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            background: #f1f5f9;
            color: #475569;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
        }

        .btn-view-result:hover {
            background: var(--doctor-secondary);
            color: white;
            box-shadow: var(--shadow);
            transform: translateY(-1px);
        }

        .logout-btn {
            margin-top: auto;
            color: #fc8181;
            background: rgba(254, 129, 129, 0.1);
        }
    </style>
</head>
<body>

    <div class="sidebar d-flex flex-column">
        <div class="brand-logo justify-content-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 80px; width: auto; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
        </div>

        <nav class="flex-grow-1">
            <a href="#" class="nav-link-medical active">
                <i class="fas fa-th-large"></i> {{ __('messages.dashboard') }}
            </a>
        </nav>

        <div class="user-info mt-auto pt-4 border-top border-secondary">
            <div class="small opacity-75 mb-1">{{ __('messages.connected_as') }}</div>
            <div class="fw-bold mb-3">{{ $doctor->name }}</div>
            
            <form action="{{ route('doctor.logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link-medical logout-btn w-100 border-0">
                    <i class="fas fa-sign-out-alt"></i> {{ __('messages.logout') }}
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="fw-bold h3 mb-1">{{ __('messages.welcome_doctor', ['name' => $doctor->name]) }}</h1>
                <p class="text-muted mb-0">{{ __('messages.doctor_dashboard_subtitle') }}</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="date-display text-muted small d-none d-md-block">
                    <i class="far fa-calendar-alt me-2"></i> {{ date('D, d M Y') }}
                </div>
                {{-- Language Switcher Pill --}}
                <div class="d-flex bg-white rounded-pill p-1 border shadow-sm" style="min-width: 100px;">
                    <a href="{{ route('lang.switch', 'ar') }}" 
                       class="flex-fill text-center rounded-pill px-3 py-1 text-decoration-none {{ app()->getLocale() == 'ar' ? 'btn-primary fw-bold shadow-sm text-white' : 'text-muted' }}"
                       style="font-size: 11px; transition: all 0.3s;">
                        العربية
                    </a>
                    <a href="{{ route('lang.switch', 'fr') }}" 
                       class="flex-fill text-center rounded-pill px-3 py-1 text-decoration-none {{ app()->getLocale() == 'fr' ? 'btn-primary fw-bold shadow-sm text-white' : 'text-muted' }}"
                       style="font-size: 11px; transition: all 0.3s;">
                        FR
                    </a>
                </div>
            </div>
        </header>

        <!-- Stats Overview -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon icon-patients"><i class="fas fa-user-injured"></i></div>
                    <div class="text-muted small fw-bold">{{ __('messages.referred_patients') }}</div>
                    <div class="h2 fw-bold mb-0">{{ $stats['total_patients'] }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon icon-pending"><i class="fas fa-hourglass-start"></i></div>
                    <div class="text-muted small fw-bold">{{ __('messages.pending_results') }}</div>
                    <div class="h2 fw-bold mb-0">{{ $stats['pending_results'] }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon icon-ready"><i class="fas fa-check-circle"></i></div>
                    <div class="text-muted small fw-bold">{{ __('messages.ready_results') }}</div>
                    <div class="h2 fw-bold mb-0">{{ $stats['ready_results'] }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Referrals Table -->
        <div class="card-table">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">{{ __('messages.recent_referred_activities') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.patient_name') }}</th>
                            <th>{{ __('messages.analyses') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{__('messages.patient_name')}}</th>
                            <th>{{__('messages.analyses')}}</th>
                            <th>{{__('messages.date')}}</th>
                            <th>{{__('messages.status')}}</th>
                            <th>{{__('messages.actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $res)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{$res->patient->name}}</div>
                                    <div class="text-muted small">{{$res->patient->phone}}</div>
                                </td>
                                <td>
                                    <div class="small text-truncate" style="max-width: 250px;">
                                        {{ $res->reservationAnalyses->map(fn($ra) => $ra->analyse->name)->implode(', ') }}
                                    </div>
                                </td>
                                <td>{{ $res->analysis_date }}</td>
                                <td>
                                    @php
                                        $isReady = in_array($res->status, ['ready', 'completed']) || $res->result_file_path;
                                    @endphp
                                    <span class="badge-status status-{{ $isReady ? 'ready' : 'pending' }}">
                                        {{ $isReady ? __('messages.ready') : __('messages.' . $res->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        {{-- 0. Main Detail View (New) --}}
                                        <a href="{{ route('doctor.patient.result', $res->id) }}" class="btn-view-result fw-bold" style="background: var(--doctor-primary); color: white;" title="{{ __('messages.view_details') ?? 'View Full Report' }}">
                                            <i class="fas fa-eye me-1"></i> {{ __('messages.view_details') ?? 'Détails' }}
                                        </a>

                                        {{-- 1. Eligibility Report (Now HTML based for clarity) --}}
                                        <a href="{{ route('doctor.eligibility.report', $res->id) }}" target="_blank" class="btn-view-result" title="{{ __('messages.eligibility_report') }}">
                                            <i class="fas fa-file-medical me-1"></i> {{ __('messages.view_report') }}
                                        </a>

                                        {{-- 2. Medical Results (Check DB results or uploaded file) --}}
                                        @php
                                            $hasDbResults = $res->reservationAnalyses->contains(fn($ra) => !empty($ra->result_value));
                                        @endphp

                                        @if($res->result_file_path)
                                            <a href="{{ route('doctor.download', $res->id) }}" target="_blank" class="btn-view-result" style="background: var(--doctor-secondary); color: white;">
                                                <i class="fas fa-file-pdf me-1"></i> {{ __('messages.view_result') }}
                                            </a>
                                        @elseif($hasDbResults)
                                            <a href="{{ route('reservation.pdf', ['id' => $res->id, 'type' => 'confirmed', 'report_type' => 'results']) }}" target="_blank" class="btn-view-result" style="background: #2f855a; color: white;">
                                                <i class="fas fa-microscope me-1"></i> {{ __('messages.medical_report_pdf') }}
                                            </a>
                                        @else
                                            <span class="text-muted small align-self-center py-1"><i class="fas fa-clock me-1"></i> {{ __('messages.waiting') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-folder-open f-large opacity-25 mb-3 d-block"></i>
                                    <p class="text-muted">{{ __('messages.no_referred_patients_found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($reservations->hasPages())
                <div class="p-4 bg-light">
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>
    </div>

</body>
</html>
