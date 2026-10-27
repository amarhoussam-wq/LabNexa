<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.lab_name') }} — {{ __('messages.portal_welcome_subtitle') ?? "Portail d'accès" }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --medical-blue: #005EB8; /* NHS Blue / Medical Standard */
            --medical-blue-dark: #003087;
            --medical-blue-light: #E8F1F8;
            --text-dark: #1A202C;
            --text-muted: #64748B;
            --success-green: #22C55E;
            --admin-purple: #6B21A8;
            --bg-body: #F0F4F8;
        }

        * { box-sizing: border-box; }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal', sans-serif" : "'Outfit', sans-serif" }};
            background-color: var(--bg-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--text-dark);
        }

        /* Medical Background Decorative Element */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; height: 35vh;
            background: linear-gradient(135deg, var(--medical-blue) 0%, var(--medical-blue-dark) 100%);
            z-index: 0;
            border-bottom-left-radius: 100px;
            border-bottom-right-radius: 100px;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
        }

        /* Logo Header */
        .portal-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .portal-header img {
            height: 80px;
            width: auto;
            margin-bottom: 20px;
            background: white;
            padding: 10px;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .portal-header h1 {
            color: white;
            font-size: 28px;
            font-weight: 800;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .portal-header p {
            color: rgba(255,255,255,0.9);
            font-size: 14px;
            margin: 8px 0 0;
            font-weight: 500;
        }

        /* Portal Selection Cards */
        .portal-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .portal-card {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 16px;
            padding: 20px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            color: var(--text-muted);
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .portal-card:hover {
            transform: translateY(-4px);
            background: white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .portal-card.active {
            background: white;
            border-width: 2px;
            color: var(--medical-blue);
            box-shadow: 0 10px 20px rgba(0,94,184,0.15);
        }

        .portal-card.active[data-type="patient"] { border-color: var(--medical-blue); }
        .portal-card.active[data-type="doctor"]  { border-color: var(--success-green); color: var(--success-green); }
        .portal-card.active[data-type="admin"]   { border-color: var(--admin-purple); color: var(--admin-purple); }

        .portal-card i {
            font-size: 28px;
            display: block;
            margin-bottom: 10px;
        }

        .portal-card span {
            font-size: 13px;
            font-weight: 700;
            display: block;
        }

        /* Login Card */
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid #E2E8F0;
        }

        .login-card-title {
            color: var(--text-dark);
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 2px solid var(--medical-blue-light);
            padding-bottom: 15px;
        }

        .login-card-title i { font-size: 18px; color: var(--medical-blue); }

        .form-label {
            color: var(--text-dark);
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .form-control {
            background: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            padding: 14px 16px;
            color: var(--text-dark);
            font-size: 15px;
            transition: all 0.2s;
        }

        .form-control::placeholder { color: #A0AEC0; }

        .form-control:focus {
            background: white;
            border-color: var(--medical-blue);
            box-shadow: 0 0 0 4px rgba(0,94,184,0.1);
            outline: none;
        }

        .input-group-text {
            background: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px 0 0 12px;
            color: var(--text-muted);
            padding: 0 15px;
        }

        [dir="rtl"] .input-group-text {
            border-radius: 0 12px 12px 0;
            border-left: none;
            border-right: 1.5px solid #E2E8F0;
        }

        .input-group .form-control {
            border-left: none;
            border-start-start-radius: 0;
            border-end-start-radius: 0;
        }

        [dir="rtl"] .input-group .form-control {
            border-left: 1.5px solid #E2E8F0;
            border-right: none;
            border-radius: 12px 0 0 12px;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 16px;
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .btn-login.patient { background-color: var(--medical-blue); }
        .btn-login.doctor  { background-color: var(--success-green); }
        .btn-login.admin   { background-color: var(--admin-purple); }

        .btn-login:hover { 
            filter: brightness(1.05); 
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .form-check-label { color: var(--text-muted); font-size: 14px; font-weight: 500; }
        .form-check-input:checked { background-color: var(--medical-blue); border-color: var(--medical-blue); }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.2s;
        }

        .back-link a:hover { color: var(--medical-blue); }

        .alert-danger {
            background: #FFF5F5;
            border: 1px solid #FED7D7;
            border-radius: 12px;
            color: #C53030;
            font-size: 14px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        /* Language Switch */
        .lang-switch {
            position: fixed;
            top: 25px;
            right: 25px;
            z-index: 100;
        }

        [dir="rtl"] .lang-switch { right: auto; left: 25px; }

        .lang-pill {
            display: flex;
            background: white;
            border: 1px solid #E2E8F0;
            border-radius: 50px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .lang-pill a {
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            color: var(--text-muted);
            transition: all 0.2s;
        }

        .lang-pill a.active { background: var(--medical-blue); color: white; }
    </style>
</head>
<body>

    {{-- Language --}}
    <div class="lang-switch">
        <div class="lang-pill">
            <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() == 'ar' ? 'active' : '' }}">عربية</a>
            <a href="{{ route('lang.switch', 'fr') }}" class="{{ app()->getLocale() == 'fr' ? 'active' : '' }}">Français</a>
        </div>
    </div>

    <div class="login-wrapper">

        {{-- Header --}}
        <div class="portal-header">
            <img src="{{ asset('images/logo.png') }}" alt="{{ __('messages.lab_name') }}" onerror="this.src='https://cdn-icons-png.flaticon.com/512/822/822143.png'">
            <h1>{{ __('messages.lab_name') }}</h1>
            <p>{{ __('messages.portal_welcome_subtitle') ?? 'Système de gestion du laboratoire médical' }}</p>
        </div>

        {{-- 3 Portal Selector --}}
        <div class="portal-selector">
            <div class="portal-card active" data-type="patient" onclick="switchPortal('patient')">
                <i class="fas fa-user-circle"></i>
                <span>{{ __('messages.patient') ?? 'Patient' }}</span>
            </div>
            <div class="portal-card" data-type="doctor" onclick="switchPortal('doctor')">
                <i class="fas fa-user-md"></i>
                <span>{{ __('messages.doctor') ?? 'Médecin' }}</span>
            </div>
            <div class="portal-card" data-type="admin" onclick="switchPortal('admin')">
                <i class="fas fa-user-shield"></i>
                <span>{{ __('messages.admin') ?? 'Admin' }}</span>
            </div>
        </div>

        {{-- Login Card --}}
        <div class="login-card">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Patient Form --}}
            <form id="patientForm" action="{{ route('access.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="auth_type" value="patient">
                <div class="login-card-title">
                    <i class="fas fa-id-card"></i> {{ __('messages.patient_portal') }}
                </div>
                <div class="mb-4 p-3 text-center rounded shadow-sm" style="background: #f0f9ff; border: 2px dashed #005EB8;">
                    <span class="d-block fw-bold text-primary mb-3" style="font-size: 18px;"><i class="fas fa-info-circle me-1"></i> معلومات الدخول للجنة التقييم</span>
                    <div class="d-flex flex-column align-items-center gap-2" style="font-size: 16px;">
                        <div class="d-flex align-items-center justify-content-between w-100 px-3 border-bottom border-primary border-opacity-25 pb-2">
                            <span class="text-dark fw-bold">اسم المستخدم :</span>
                            <code class="fs-5 bg-white px-4 py-1 rounded text-primary fw-bold shadow-sm" style="border: 1px solid #005EB8;">admin</code>
                        </div>
                        <div class="d-flex align-items-center justify-content-between w-100 px-3 pt-1">
                            <span class="text-dark fw-bold">كلمة المرور :</span>
                            <code class="fs-5 bg-white px-4 py-1 rounded text-primary fw-bold shadow-sm" style="border: 1px solid #005EB8;">admin123</code>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.username_or_email') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="email_or_phone" class="form-control" placeholder="{{ __('messages.name') }} / {{ __('messages.email') }} / {{ __('messages.phone_number') }}" value="{{ old('email_or_phone') }}" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">{{ __('messages.password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="rememberPatient">
                    <label class="form-check-label" for="rememberPatient">{{ __('messages.remember_me') }}</label>
                </div>
                <button type="submit" class="btn-login patient">
                    {{ __('messages.login') }}
                </button>
            </form>

            {{-- Doctor Form --}}
            <form id="doctorForm" action="{{ route('access.submit') }}" method="POST" style="display:none;" novalidate>
                @csrf
                <input type="hidden" name="auth_type" value="doctor">
                <div class="login-card-title" style="border-color: #DCFCE7;">
                    <i class="fas fa-user-md" style="color: var(--success-green);"></i> {{ __('messages.physician_portal') }}
                </div>
                <div class="mb-4 p-3 text-center rounded shadow-sm" style="background: #f0fdf4; border: 2px dashed #22C55E;">
                    <span class="d-block fw-bold text-success mb-3" style="font-size: 18px;"><i class="fas fa-info-circle me-1"></i> معلومات الدخول للجنة التقييم</span>
                    <div class="d-flex flex-column align-items-center gap-2" style="font-size: 16px;">
                        <div class="d-flex align-items-center justify-content-between w-100 px-3 border-bottom border-success border-opacity-25 pb-2">
                            <span class="text-dark fw-bold">اسم المستخدم :</span>
                            <code class="fs-5 bg-white px-4 py-1 rounded text-success fw-bold shadow-sm" style="border: 1px solid #22C55E;">admin</code>
                        </div>
                        <div class="d-flex align-items-center justify-content-between w-100 px-3 pt-1">
                            <span class="text-dark fw-bold">كلمة المرور :</span>
                            <code class="fs-5 bg-white px-4 py-1 rounded text-success fw-bold shadow-sm" style="border: 1px solid #22C55E;">admin123</code>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.username_or_email') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="text" name="email" class="form-control" placeholder="admin / doctor@lab.dz" value="{{ old('email') }}">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">{{ __('messages.password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="btn-login doctor">
                    {{ __('messages.login_as_doctor') }}
                </button>
            </form>

            {{-- Admin Form --}}
            <form id="adminForm" action="{{ route('auth.administrator') }}" method="POST" style="display:none;">
                @csrf
                <div class="login-card-title" style="border-color: #F3E8FF;">
                    <i class="fas fa-shield-alt" style="color: var(--admin-purple);"></i> {{ __('messages.admin_panel') }}
                </div>
                <div class="mb-4 p-3 text-center rounded shadow-sm" style="background: #f5f3ff; border: 2px dashed #6B21A8;">
                    <span class="d-block fw-bold mb-3" style="color: #6B21A8; font-size: 18px;"><i class="fas fa-info-circle me-1"></i> معلومات الدخول للجنة التقييم</span>
                    <div class="d-flex flex-column align-items-center gap-2" style="font-size: 16px;">
                        <div class="d-flex align-items-center justify-content-between w-100 px-3 border-bottom pb-2" style="border-color: rgba(107, 33, 168, 0.25) !important;">
                            <span class="text-dark fw-bold">اسم المستخدم :</span>
                            <code class="fs-5 bg-white px-4 py-1 rounded fw-bold shadow-sm" style="color: #6B21A8; border: 1px solid #6B21A8;">admin</code>
                        </div>
                        <div class="d-flex align-items-center justify-content-between w-100 px-3 pt-1">
                            <span class="text-dark fw-bold">كلمة المرور :</span>
                            <code class="fs-5 bg-white px-4 py-1 rounded fw-bold shadow-sm" style="color: #6B21A8; border: 1px solid #6B21A8;">admin123</code>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.username_or_email') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                        <input type="text" name="name" class="form-control" placeholder="admin" value="{{ old('name') }}" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">{{ __('messages.password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="btn-login admin">
                    {{ __('messages.login') }} (Admin)
                </button>
            </form>

        </div>

        <div class="back-link">
            <a href="{{ route('home') }}">
                <i class="fas fa-chevron-left me-1"></i>
                {{ __('messages.back_to_home') ?? 'Retour à l\'accueil' }}
            </a>
        </div>

    </div>

    <script>
        function switchPortal(type) {
            // Update cards
            document.querySelectorAll('.portal-card').forEach(c => c.classList.remove('active'));
            document.querySelector(`.portal-card[data-type="${type}"]`).classList.add('active');

            // Show/hide forms
            document.getElementById('patientForm').style.display = 'none';
            document.getElementById('doctorForm').style.display  = 'none';
            document.getElementById('adminForm').style.display   = 'none';
            document.getElementById(type + 'Form').style.display = 'block';
        }

        // Restore state on validation error or query parameter
        @if(request('tab') == 'admin')
            switchPortal('admin');
        @elseif(old('auth_type') == 'doctor')
            switchPortal('doctor');
        @elseif($errors->has('name') || old('name'))
            switchPortal('admin');
        @endif
    </script>
</body>
</html>
