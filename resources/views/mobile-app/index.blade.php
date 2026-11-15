<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LabNexa Mobile</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Tajawal:wght@400;500;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ══════════════════════════════════════════════════════════════════════
           LabNexa — Pure Mobile App Interface (Designed for Smartphones)
           Ultra-Light • Instant OTA Updates • Zero Clutter • Native Touch Feel
        ══════════════════════════════════════════════════════════════════════ */
        :root {
            --primary: #0066FF;
            --primary-dark: #0052CC;
            --primary-light: #EBF3FF;
            --primary-glow: rgba(0, 102, 255, 0.25);
            
            --emerald: #10B981;
            --emerald-light: #ECFDF5;
            --amber: #F59E0B;
            --amber-light: #FFFBEB;
            --crimson: #EF4444;
            --crimson-light: #FEE2E2;

            --bg-app: #F8FAFC;
            --surface-card: #FFFFFF;
            --border-light: #E2E8F0;
            
            --slate-900: #0F172A;
            --slate-800: #1E293B;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748B;
            --slate-400: #94A3B8;
            --slate-200: #E2E8F0;
            --slate-100: #F1F5F9;

            --app-header-h: 62px;
            --app-bottom-h: 70px;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        input, textarea, select {
            user-select: auto;
        }

        html, body {
            height: 100%;
            overflow: hidden;
            background-color: var(--bg-app);
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal', 'Cairo'" : "'Inter', 'Tajawal'" }}, -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--slate-800);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* ── App Shell Container ── */
        .app-shell {
            display: flex;
            flex-direction: column;
            height: 100vh;
            max-width: 500px;
            margin: 0 auto;
            position: relative;
            background: #fff;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.08);
        }

        /* ── Top Native App Bar ── */
        .app-topbar {
            height: var(--app-header-h);
            background: #fff;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 18px;
            position: relative;
            z-index: 100;
            flex-shrink: 0;
        }
        .app-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .app-brand img {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(0, 102, 255, 0.25);
        }
        .app-brand-title {
            font-size: 1.15rem;
            font-weight: 900;
            color: var(--slate-900);
            letter-spacing: -0.3px;
        }
        .app-brand-tag {
            font-size: 0.65rem;
            background: var(--primary-light);
            color: var(--primary);
            padding: 2px 7px;
            border-radius: 100px;
            font-weight: 800;
        }
        .app-top-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .app-lang-pill {
            background: var(--slate-100);
            color: var(--slate-700);
            font-size: 0.78rem;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 100px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .app-lang-pill:active {
            background: var(--slate-200);
        }

        /* ── Scrollable Tab Views Container ── */
        .app-content-body {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: calc(var(--app-bottom-h) + 16px);
            background: var(--bg-app);
            position: relative;
        }

        .tab-view {
            display: none;
            padding: 18px;
            animation: fadeInView 0.25s ease-out;
        }
        .tab-view.active {
            display: block;
        }
        @keyframes fadeInView {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Common Mobile UI Components ── */
        .mobile-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid var(--border-light);
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
            padding: 20px;
            margin-bottom: 16px;
        }
        .mobile-title {
            font-size: 1.3rem;
            font-weight: 900;
            color: var(--slate-900);
            margin-bottom: 6px;
        }
        .mobile-subtitle {
            font-size: 0.88rem;
            color: var(--slate-500);
            margin-bottom: 18px;
        }

        /* ── TAB 1: BOOKING ── */
        .booking-input-group {
            margin-bottom: 14px;
        }
        .booking-input-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--slate-700);
            margin-bottom: 6px;
        }
        .booking-input-wrap {
            position: relative;
        }
        .booking-input-wrap i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 14px;
            color: var(--slate-400);
            font-size: 1rem;
        }
        [dir="rtl"] .booking-input-wrap i {
            left: auto;
            right: 14px;
        }
        .booking-input {
            width: 100%;
            padding: 13px 14px 13px 42px;
            border-radius: 12px;
            border: 1.5px solid var(--slate-200);
            background: var(--slate-50);
            font-family: inherit;
            font-size: 0.92rem;
            color: var(--slate-800);
            outline: none;
            transition: all 0.2s;
        }
        [dir="rtl"] .booking-input {
            padding: 13px 42px 13px 14px;
        }
        .booking-input:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        /* Analysis Search & Chips */
        .analysis-scroll-box {
            max-height: 220px;
            overflow-y: auto;
            border: 1.5px solid var(--slate-200);
            border-radius: 12px;
            padding: 8px;
            background: var(--slate-50);
        }
        .analysis-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid var(--border-light);
            margin-bottom: 6px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .analysis-item-row:hover, .analysis-item-row.selected {
            border-color: var(--primary);
            background: var(--primary-light);
        }
        .analysis-item-name {
            font-size: 0.86rem;
            font-weight: 700;
            color: var(--slate-800);
        }
        .analysis-item-row input {
            accent-color: var(--primary);
            width: 18px;
            height: 18px;
        }

        /* Prescription file upload pill */
        .prescription-upload-box {
            border: 2px dashed rgba(0, 102, 255, 0.35);
            background: rgba(0, 102, 255, 0.02);
            border-radius: 14px;
            padding: 18px;
            text-align: center;
            position: relative;
            cursor: pointer;
        }
        .prescription-upload-box input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .btn-mobile-submit {
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            border: none;
            background: linear-gradient(135deg, #0066FF 0%, #0052CC 100%);
            color: #fff;
            font-family: inherit;
            font-size: 1.05rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(0, 102, 255, 0.35);
            transition: all 0.2s;
            margin-top: 10px;
        }
        .btn-mobile-submit:active {
            transform: scale(0.98);
        }

        /* ── TAB 2 & 3: RESULTS & NUMERICAL CARDS ── */
        .visit-header-banner {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            color: #fff;
            padding: 18px 20px;
            border-radius: 16px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-badge-ready {
            background: var(--emerald);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 100px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .status-badge-pending {
            background: var(--amber);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 100px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Numerical Results Card (Instant Read) */
        .result-metric-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid var(--border-light);
            padding: 14px 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            transition: transform 0.2s;
        }
        .result-metric-left {
            flex: 1;
        }
        .result-test-name {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--slate-900);
            display: block;
        }
        .result-ref-range {
            font-size: 0.76rem;
            color: var(--slate-400);
            display: block;
            margin-top: 2px;
        }
        .result-metric-right {
            text-align: end;
        }
        .result-number-val {
            font-family: 'Inter', monospace;
            font-size: 1.3rem;
            font-weight: 900;
            color: var(--primary);
            line-height: 1;
        }
        .result-unit-tag {
            font-size: 0.72rem;
            color: var(--slate-500);
            display: block;
            margin-top: 2px;
        }

        /* AI Clinical Explanation Card in App */
        .ai-app-card {
            background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
            border: 1.5px solid #BFDBFE;
            border-radius: 16px;
            padding: 16px;
            margin-top: 14px;
        }
        .ai-app-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 6px;
        }
        .ai-app-desc {
            font-size: 0.84rem;
            line-height: 1.6;
            color: var(--slate-700);
        }

        /* ── TAB 4: PROFILE ── */
        .profile-avatar-wrap {
            text-align: center;
            padding: 20px 0;
        }
        .profile-avatar-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0066FF, #06B6D4);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 8px 20px rgba(0, 102, 255, 0.3);
            margin-bottom: 12px;
        }
        .menu-action-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            background: #fff;
            border: 1px solid var(--border-light);
            border-radius: 12px;
            margin-bottom: 8px;
            text-decoration: none;
            color: var(--slate-700);
            font-weight: 700;
            font-size: 0.92rem;
            transition: all 0.2s;
        }
        .menu-action-btn:active {
            background: var(--slate-100);
        }

        /* ── FIXED NATIVE BOTTOM NAVIGATION BAR ── */
        .app-bottom-bar {
            height: var(--app-bottom-h);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid var(--border-light);
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 0 8px;
            z-index: 100;
        }
        .bottom-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--slate-400);
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        .bottom-nav-item i {
            font-size: 1.25rem;
            margin-bottom: 3px;
            transition: transform 0.2s;
        }
        .bottom-nav-item span {
            font-size: 0.72rem;
            font-weight: 800;
        }
        .bottom-nav-item.active {
            color: var(--primary);
        }
        .bottom-nav-item.active i {
            transform: scale(1.15) translateY(-2px);
        }

        /* Toast Notice */
        .app-toast {
            position: absolute;
            top: 75px;
            left: 50%;
            transform: translateX(-50%) translateY(-20px);
            background: var(--slate-900);
            color: #fff;
            padding: 10px 20px;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 700;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 999;
            white-space: nowrap;
        }
        .app-toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    </style>
</head>

<body>

    <div class="app-shell">
        
        <!-- App Toast Notification -->
        <div class="app-toast" id="appToast">مرحباً بك في LabNexa Mobile</div>

        <!-- ══════════════ 1. TOP NATIVE APP BAR ══════════════ -->
        <header class="app-topbar">
            <div class="app-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/822/822143.png'">
                <div>
                    <span class="app-brand-title">{{ __('messages.lab_name') }}</span>
                    <span class="app-brand-tag">APP v1.2</span>
                </div>
            </div>

            <div class="app-top-actions">
                <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'fr' : 'ar') }}" class="app-lang-pill">
                    {{ app()->getLocale() == 'ar' ? 'FR' : 'العربية' }}
                </a>
            </div>
        </header>

        <!-- ══════════════ 2. TAB VIEWS CONTENT BODY ══════════════ -->
        <main class="app-content-body">
            
            @if(session('success'))
                <div style="margin: 14px 18px 0; padding: 14px 16px; border-radius: 14px; background: #ECFDF5; border: 1.5px solid #A7F3D0; color: #065F46; font-size: 0.9rem; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-circle-check" style="font-size: 1.2rem; color: var(--emerald);"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div style="margin: 14px 18px 0; padding: 14px 16px; border-radius: 14px; background: #FEF2F2; border: 1.5px solid #FECACA; color: #991B1B; font-size: 0.88rem; font-weight: 700;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <i class="fas fa-circle-exclamation" style="color: var(--crimson);"></i>
                        <span>{{ session('error') ?? __('messages.booking_failed') }}</span>
                    </div>
                    @if($errors->any())
                        <ul style="padding-inline-start: 22px; font-size: 0.82rem; font-weight: 600; opacity: 0.9;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <!-- ──────── TAB 1: BOOKING (حجز موعد) ──────── -->
            <section class="tab-view active" id="view-booking">
                <div class="mobile-card">
                    <h2 class="mobile-title">
                        <i class="fas fa-calendar-plus text-primary me-2"></i>
                        {{ app()->getLocale() == 'ar' ? 'حجز موعد سريع' : 'Prendre Rendez-vous' }}
                    </h2>
                    <p class="mobile-subtitle">
                        {{ app()->getLocale() == 'ar' ? 'اختر تحاليلك وحدد موعدك في أقل من دقيقة' : 'Choisissez vos analyses et fixez votre créneau' }}
                    </p>

                    <form id="mobileBookingForm" action="{{ route('booking') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Full Name -->
                        <div class="booking-input-group">
                            <label class="booking-input-label">{{ __('messages.full_name') }} *</label>
                            <div class="booking-input-wrap">
                                <i class="fas fa-user"></i>
                                <input type="text" name="name" class="booking-input" placeholder="{{ __('messages.full_name') }}" value="{{ $patient->name ?? '' }}" required>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="booking-input-group">
                            <label class="booking-input-label">{{ __('messages.phone_number') }} *</label>
                            <div class="booking-input-wrap">
                                <i class="fas fa-phone"></i>
                                <input type="tel" name="phone" id="mPhone" class="booking-input" placeholder="05XXXXXXXX / 06XXXXXXXX" value="{{ $patient->phone ?? '' }}" required>
                            </div>
                        </div>

                        <!-- Gender & Birth date -->
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                            <div class="booking-input-group">
                                <label class="booking-input-label">{{ __('messages.gender') }} *</label>
                                <div class="booking-input-wrap">
                                    <i class="fas fa-venus-mars"></i>
                                    <select name="gender" class="booking-input" required>
                                        <option value="male">{{ __('messages.male') }}</option>
                                        <option value="female">{{ __('messages.female') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="booking-input-group">
                                <label class="booking-input-label">{{ __('messages.birth_date') }} *</label>
                                <div class="booking-input-wrap">
                                    <i class="fas fa-calendar"></i>
                                    <input type="date" name="birth_date" class="booking-input" value="1995-01-01" required>
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="booking-input-group">
                            <label class="booking-input-label">{{ __('messages.email') }} *</label>
                            <div class="booking-input-wrap">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" class="booking-input" placeholder="patient@mail.com" value="{{ $patient->email ?? '' }}" required>
                            </div>
                        </div>

                        <!-- Analysis Selection with Live Filter -->
                        <div class="booking-input-group">
                            <label class="booking-input-label">
                                {{ __('messages.select_analyses') }} 
                                <span style="font-weight:600; color:var(--primary);" id="selectedAnalysesCount">(0 محدد)</span>
                            </label>
                            <input type="text" id="mAnalysisFilter" class="booking-input" style="padding-left:14px; padding-right:14px; margin-bottom:8px; font-size:0.85rem;" placeholder="{{ app()->getLocale() == 'ar' ? '🔍 ابحث عن الفحص (NFS, Glycémie...)' : '🔍 Rechercher...' }}">
                            
                            <div class="analysis-scroll-box" id="mAnalysisScroll">
                                @foreach($analyses as $analyse)
                                    <label class="analysis-item-row" data-name="{{ strtolower($analyse->name . ' ' . $analyse->name_fr) }}">
                                        <span class="analysis-item-name">{{ app()->getLocale() == 'ar' ? ($analyse->name_fr ? $analyse->name_fr . ' — ' . $analyse->name : $analyse->name) : ($analyse->name_fr ?? $analyse->name) }}</span>
                                        <input type="checkbox" name="analysisTypes[]" value="{{ $analyse->id }}">
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Prescription Photo Upload -->
                        <div class="booking-input-group">
                            <label class="booking-input-label">{{ __('messages.prescription_label') }} ({{ app()->getLocale() == 'ar' ? 'اختياري' : 'Optionnel' }})</label>
                            <div class="prescription-upload-box">
                                <input type="file" name="prescription" id="mPrescription" accept="image/*,application/pdf">
                                <i class="fas fa-camera text-primary" style="font-size:1.8rem; margin-bottom:6px;"></i>
                                <div style="font-size:0.86rem; font-weight:800; color:var(--slate-800);" id="mPrescLabel">
                                    {{ app()->getLocale() == 'ar' ? 'التقط أو اختر صورة الوصفة' : 'Prendre en photo l\'ordonnance' }}
                                </div>
                            </div>
                        </div>

                        <!-- Date & Time Slots -->
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                            <div class="booking-input-group">
                                <label class="booking-input-label">{{ __('messages.preferred_date') }} *</label>
                                <div class="booking-input-wrap">
                                    <i class="fas fa-calendar-day"></i>
                                    <input type="date" name="date" class="booking-input" id="mDate" required>
                                </div>
                            </div>

                            <div class="booking-input-group">
                                <label class="booking-input-label">{{ __('messages.preferred_time') }} *</label>
                                <div class="booking-input-wrap">
                                    <i class="fas fa-clock"></i>
                                    <select name="time" class="booking-input" required>
                                        <option value="08:00">08:00</option>
                                        <option value="08:30">08:30</option>
                                        <option value="09:00">09:00</option>
                                        <option value="09:30">09:30</option>
                                        <option value="10:00">10:00</option>
                                        <option value="10:30">10:30</option>
                                        <option value="11:00">11:00</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-mobile-submit">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ __('messages.confirm_booking') }}</span>
                        </button>

                    </form>
                </div>
            </section>

            <!-- ──────── TAB 2: RESULTS (النتائج بالأرقام) ──────── -->
            <section class="tab-view" id="view-results">
                @if($patient && $latestReservation)
                    
                    <!-- Visit Header Banner -->
                    <div class="visit-header-banner">
                        <div>
                            <span style="font-size:0.8rem; opacity:0.8; display:block;">{{ __('messages.visit_id') }} #{{ $latestReservation->id }}</span>
                            <strong style="font-size:1.05rem;">{{ $latestReservation->analysis_date }}</strong>
                            @if($latestReservation->doctor)
                                <span style="font-size:0.75rem; color:#93C5FD; display:block;">Dr. {{ $latestReservation->doctor->name }}</span>
                            @endif
                        </div>
                        <div>
                            @php
                                $isReady = in_array($latestReservation->status, ['ready', 'completed']);
                            @endphp
                            <span class="{{ $isReady ? 'status-badge-ready' : 'status-badge-pending' }}">
                                <i class="fas {{ $isReady ? 'fa-circle-check' : 'fa-hourglass-half' }}"></i>
                                {{ $isReady ? __('messages.ready') : __('messages.' . $latestReservation->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Numerical Test Results Cards -->
                    <h3 style="font-size:0.95rem; font-weight:800; color:var(--slate-500); margin-bottom:10px; text-transform:uppercase;">
                        {{ app()->getLocale() == 'ar' ? 'القيم البيولوجية المقاسة' : 'Valeurs Biologiques' }}
                    </h3>

                    @foreach($latestReservation->reservationAnalyses as $ra)
                        <div class="result-metric-card">
                            <div class="result-metric-left">
                                <span class="result-test-name">{{ app()->getLocale() == 'ar' ? ($ra->analyse->name_fr ?? $ra->analyse->name) : ($ra->analyse->name_fr ?? $ra->analyse->name) }}</span>
                                <span class="result-ref-range">Ref: {{ $ra->reference_range ?? ($ra->analyse->normal_range ?? '--') }}</span>
                            </div>
                            <div class="result-metric-right">
                                <span class="result-number-val">{{ $ra->result_value ?? '--' }}</span>
                                <span class="result-unit-tag">{{ $ra->unit ?? ($ra->result_unit ?? ($ra->analyse->unit ?? '')) }}</span>
                            </div>
                        </div>
                    @endforeach

                    <!-- AI Clinical Summary -->
                    @if($latestReservation->ai_insight)
                        <div class="ai-app-card">
                            <div class="ai-app-title">
                                <i class="fas fa-brain"></i>
                                <span>{{ app()->getLocale() == 'ar' ? 'شرح الذكاء الاصطناعي السريري' : 'Interprétation Clinique IA' }}</span>
                            </div>
                            <p class="ai-app-desc">{{ $latestReservation->ai_insight }}</p>
                        </div>
                    @endif

                @else
                    <!-- Not Logged In State or No Results Yet -->
                    <div class="mobile-card text-center" style="padding:32px 20px; text-align:center;">
                        <i class="fas fa-microscope text-primary" style="font-size:3rem; margin-bottom:14px; opacity:0.8;"></i>
                        <h3 class="mobile-title" style="font-size:1.15rem;">{{ app()->getLocale() == 'ar' ? 'اطّلع على نتائجك الفورية' : 'Consulter vos résultats' }}</h3>
                        <p class="mobile-subtitle" style="font-size:0.86rem; margin-bottom:20px;">
                            {{ app()->getLocale() == 'ar' 
                                ? 'يمكنك استعراض نتائجك كأرقام فورية بإدخال رقم الهاتف أو رقم الحجز في تبويب التتبع، أو تسجيل الدخول لحسابك.' 
                                : 'Suivez vos résultats en direct avec votre numéro de dossier.' 
                            }}
                        </p>

                        <button class="btn-mobile-submit" style="margin-bottom:12px;" onclick="switchAppTab('view-track')">
                            <i class="fas fa-search"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'تتبع برقم الحجز أو الهاتف' : 'Rechercher par dossier' }}</span>
                        </button>
                        
                        <button class="btn-mobile-submit" style="background:#fff; border:1.5px solid var(--slate-200); color:var(--slate-800); box-shadow:none;" onclick="switchAppTab('view-profile')">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'تسجيل الدخول للحساب' : 'Se connecter' }}</span>
                        </button>
                    </div>
                @endif
            </section>

            <!-- ──────── TAB 3: QUICK TRACK (تتبع فوري) ──────── -->
            <section class="tab-view" id="view-track">
                <div class="mobile-card">
                    <h2 class="mobile-title">
                        <i class="fas fa-magnifying-glass text-primary me-2"></i>
                        {{ app()->getLocale() == 'ar' ? 'تتبع النتائج فوري' : 'Suivi Express' }}
                    </h2>
                    <p class="mobile-subtitle">
                        {{ app()->getLocale() == 'ar' ? 'أدخل رقم هاتفك أو رقم الوصل (#ID) للاطلاع على نتائجك فوراً' : 'Entrez votre numéro de téléphone ou ID' }}
                    </p>

                    <div class="booking-input-group">
                        <div class="booking-input-wrap">
                            <i class="fas fa-hashtag"></i>
                            <input type="text" id="trackInput" class="booking-input" placeholder="05XXXXXXXX / #222">
                        </div>
                    </div>

                    <button type="button" class="btn-mobile-submit" id="btnDoTrack">
                        <i class="fas fa-arrow-left"></i>
                        <span>{{ app()->getLocale() == 'ar' ? 'استعراض النتيجة بالأرقام' : 'Voir mes résultats' }}</span>
                    </button>
                </div>

                <!-- Dynamic Track Results Container -->
                <div id="trackResultContainer" style="display:none;"></div>
            </section>

            <!-- ──────── TAB 4: PROFILE / SETTINGS (حسابي) ──────── -->
            <section class="tab-view" id="view-profile">
                <div class="mobile-card">
                    
                    @if($patient)
                        <div class="profile-avatar-wrap">
                            <div class="profile-avatar-icon"><i class="fas fa-user-check"></i></div>
                            <h3 style="font-weight:900; font-size:1.2rem; color:var(--slate-900);">{{ $patient->name }}</h3>
                            <span style="font-size:0.85rem; color:var(--slate-500);"><i class="fas fa-phone me-1"></i> {{ $patient->phone }}</span>
                        </div>

                        <a href="tel:0550000000" class="menu-action-btn">
                            <span><i class="fas fa-headset text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'الاتصال بالمخبر مباشرة' : 'Appeler le labo' }}</span>
                            <i class="fas fa-chevron-left text-muted"></i>
                        </a>

                        <form action="{{ route('mobile.app.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="menu-action-btn" style="color:var(--crimson); border-color:#FEE2E2;">
                                <span><i class="fas fa-arrow-right-from-bracket me-2"></i> {{ app()->getLocale() == 'ar' ? 'تسجيل الخروج' : 'Se déconnecter' }}</span>
                            </button>
                        </form>

                    @else
                        <!-- In-App Login Form -->
                        <div class="profile-avatar-wrap">
                            <div class="profile-avatar-icon" style="background:var(--slate-100); color:var(--slate-600);"><i class="fas fa-user-lock"></i></div>
                            <h3 style="font-weight:900; font-size:1.15rem; color:var(--slate-900);">{{ app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Connexion Patient' }}</h3>
                            <span style="font-size:0.85rem; color:var(--slate-500);">{{ app()->getLocale() == 'ar' ? 'لمزامنة ملفك الطبي ونتائجك الدائمة' : 'Accédez à votre historique' }}</span>
                        </div>

                        <form id="inAppLoginForm">
                            <div class="booking-input-group">
                                <label class="booking-input-label">{{ app()->getLocale() == 'ar' ? 'الهاتف أو البريد' : 'Téléphone ou email' }}</label>
                                <div class="booking-input-wrap">
                                    <i class="fas fa-phone"></i>
                                    <input type="text" id="loginId" class="booking-input" placeholder="05XXXXXXXX" required>
                                </div>
                            </div>

                            <div class="booking-input-group">
                                <label class="booking-input-label">{{ __('messages.password') }}</label>
                                <div class="booking-input-wrap">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="loginPass" class="booking-input" placeholder="••••••" required>
                                </div>
                            </div>

                            <button type="submit" class="btn-mobile-submit">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>{{ __('messages.login') }}</span>
                            </button>
                        </form>
                    @endif

                </div>
            </section>

        </main>

        <!-- ══════════════ 3. FIXED NATIVE BOTTOM NAVIGATION BAR ══════════════ -->
        <nav class="app-bottom-bar">
            
            <a class="bottom-nav-item active" data-view="view-booking">
                <i class="fas fa-calendar-check"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'الحجز' : 'Rendez-vous' }}</span>
            </a>

            <a class="bottom-nav-item" data-view="view-results">
                <i class="fas fa-file-waveform"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'نتائجي' : 'Résultats' }}</span>
            </a>

            <a class="bottom-nav-item" data-view="view-track">
                <i class="fas fa-magnifying-glass"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'تتبع فوري' : 'Suivi' }}</span>
            </a>

            <a class="bottom-nav-item" data-view="view-profile">
                <i class="fas fa-user-circle"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'حسابي' : 'Compte' }}</span>
            </a>

        </nav>

    </div>

    <!-- ══════════════ 4. MOBILE INTERACTIVE SCRIPTS ══════════════ -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // ── Toast Helper ──
        function showToast(msg) {
            const toast = document.getElementById('appToast');
            if (toast) {
                toast.textContent = msg;
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 3500);
            }
        }

        // ── Native Tab Switching ──
        window.switchAppTab = function(targetViewId) {
            document.querySelectorAll('.tab-view').forEach(view => view.classList.remove('active'));
            document.querySelectorAll('.bottom-nav-item').forEach(btn => btn.classList.remove('active'));

            const targetView = document.getElementById(targetViewId);
            const targetBtn = document.querySelector(`.bottom-nav-item[data-view="${targetViewId}"]`);

            if (targetView) targetView.classList.add('active');
            if (targetBtn) targetBtn.classList.add('active');

            // Scroll to top of tab
            document.querySelector('.app-content-body').scrollTop = 0;
        };

        document.querySelectorAll('.bottom-nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const targetViewId = this.getAttribute('data-view');
                switchAppTab(targetViewId);
            });
        });

        // ── Date Min Constraint ──
        const dateInput = document.getElementById('mDate');
        if (dateInput) {
            const tmr = new Date();
            tmr.setDate(tmr.getDate() + 1);
            dateInput.min = new Date().toISOString().split('T')[0];
            dateInput.value = tmr.toISOString().split('T')[0];
        }

        // ── Analysis Search & Count ──
        const filterInput = document.getElementById('mAnalysisFilter');
        const countSpan = document.getElementById('selectedAnalysesCount');
        const scrollBox = document.getElementById('mAnalysisScroll');

        if (filterInput && scrollBox) {
            filterInput.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                scrollBox.querySelectorAll('.analysis-item-row').forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    row.style.display = name.includes(q) ? 'flex' : 'none';
                });
            });
        }

        if (scrollBox && countSpan) {
            scrollBox.querySelectorAll('input[type="checkbox"]').forEach(chk => {
                chk.addEventListener('change', function() {
                    const count = scrollBox.querySelectorAll('input[type="checkbox"]:checked').length;
                    countSpan.textContent = `(${count} محدد)`;
                });
            });
        }

        // ── Prescription Photo Name Display ──
        const prescInput = document.getElementById('mPrescription');
        const prescLabel = document.getElementById('mPrescLabel');
        if (prescInput && prescLabel) {
            prescInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    prescLabel.innerHTML = '<span style="color:var(--emerald);"><i class="fas fa-check-circle me-1"></i> ' + this.files[0].name + '</span>';
                    showToast('تم إرفاق الوصفة بنجاح 📄');
                }
            });
        }

        // ── Instant AJAX Track by Phone or Code (#ID) ──
        const btnTrack = document.getElementById('btnDoTrack');
        const trackInput = document.getElementById('trackInput');
        const trackContainer = document.getElementById('trackResultContainer');

        if (btnTrack && trackInput && trackContainer) {
            btnTrack.addEventListener('click', function() {
                const q = trackInput.value.trim();
                if (!q) {
                    showToast('يرجى كتابة رقم الهاتف أو كود الحجز');
                    return;
                }

                btnTrack.disabled = true;
                btnTrack.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري البحث...';

                fetch('{{ route("mobile.app.track") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ query: q })
                })
                .then(res => res.json())
                .then(data => {
                    btnTrack.disabled = false;
                    btnTrack.innerHTML = '<i class="fas fa-arrow-left"></i> <span>{{ app()->getLocale() == "ar" ? "استعراض النتيجة بالأرقام" : "Voir mes résultats" }}</span>';

                    if (data.success && data.found) {
                        const res = data.reservation;
                        let analysesHtml = '';
                        res.analyses.forEach(a => {
                            analysesHtml += `
                                <div class="result-metric-card">
                                    <div class="result-metric-left">
                                        <span class="result-test-name">${a.name_fr || a.name}</span>
                                        <span class="result-ref-range">Ref: ${a.reference}</span>
                                    </div>
                                    <div class="result-metric-right">
                                        <span class="result-number-val">${a.result || '--'}</span>
                                        <span class="result-unit-tag">${a.unit || ''}</span>
                                    </div>
                                </div>
                            `;
                        });

                        const aiHtml = res.ai_insight ? `
                            <div class="ai-app-card">
                                <div class="ai-app-title"><i class="fas fa-brain"></i> شرح الذكاء الاصطناعي</div>
                                <p class="ai-app-desc">${res.ai_insight}</p>
                            </div>
                        ` : '';

                        const statusBadge = res.status === 'ready' 
                            ? '<span class="status-badge-ready"><i class="fas fa-circle-check"></i> جاهز</span>' 
                            : '<span class="status-badge-pending"><i class="fas fa-clock"></i> قيد المعالجة</span>';

                        trackContainer.innerHTML = `
                            <div class="visit-header-banner" style="margin-top:16px;">
                                <div>
                                    <span style="font-size:0.8rem; opacity:0.8; display:block;">حجز #${res.id} • ${res.patient_name}</span>
                                    <strong style="font-size:1.05rem;">${res.date} (${res.time})</strong>
                                </div>
                                <div>${statusBadge}</div>
                            </div>
                            ${analysesHtml}
                            ${aiHtml}
                        `;
                        trackContainer.style.display = 'block';
                        showToast('تم العثور على النتائج بنجاح! 🧪');
                    } else {
                        trackContainer.style.display = 'none';
                        showToast(data.message || 'لم يتم العثور على نتائج');
                    }
                })
                .catch(err => {
                    btnTrack.disabled = false;
                    btnTrack.innerHTML = '<i class="fas fa-arrow-left"></i> <span>استعراض النتيجة بالأرقام</span>';
                    showToast('خطأ في الاتصال بالمخبر');
                });
            });
        }

        // ── In-App Quick Login ──
        const loginForm = document.getElementById('inAppLoginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('loginId').value.trim();
                const pass = document.getElementById('loginPass').value.trim();

                fetch('{{ route("mobile.app.login") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ identifier: id, password: pass })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('تم تسجيل الدخول! جار التحديث...');
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        showToast(data.message || 'بيانات خاطئة');
                    }
                })
                .catch(() => showToast('خطأ في الاتصال'));
            });
        }

        // Handle URL hash tab switching if provided (e.g. #results)
        if (window.location.hash) {
            const h = window.location.hash.replace('#', '');
            if (h === 'results') switchAppTab('view-results');
            else if (h === 'track') switchAppTab('view-track');
            else if (h === 'profile') switchAppTab('view-profile');
        }

    });
    </script>

</body>
</html>
