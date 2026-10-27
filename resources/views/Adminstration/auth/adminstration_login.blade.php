<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.admin_login_title') }} - {{ __('messages.lab_name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{asset('css/auth/login.css')}}">
    <!-- Version: 2026.04.07.V4 -->
    <style>
        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal', sans-serif" : "'Outfit', sans-serif" }};
        }
        .lang-switcher-container {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        [dir="ltr"] .lang-switcher-container {
            left: auto;
            right: 20px;
        }
    </style>
</head>
<body>
    <div class="lang-switcher-container">
        <div class="d-flex bg-white rounded-pill p-1 border shadow-sm" style="display: flex !important; background: white; border-radius: 50px; padding: 4px; border: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <a href="{{ route('lang.switch', 'ar') }}" 
               class="btn btn-sm rounded-pill px-3 {{ app()->getLocale() == 'ar' ? 'active-lang' : 'inactive-lang' }}"
               style="font-size: 11px; text-decoration: none; padding: 6px 15px; border-radius: 20px; {{ app()->getLocale() == 'ar' ? 'background: #3b82f6; color: white; font-weight: bold;' : 'color: #666;' }}">
                العربية
            </a>
            <a href="{{ route('lang.switch', 'fr') }}" 
               class="btn btn-sm rounded-pill px-3 {{ app()->getLocale() == 'fr' ? 'active-lang' : 'inactive-lang' }}"
               style="font-size: 11px; text-decoration: none; padding: 6px 15px; border-radius: 20px; {{ app()->getLocale() == 'fr' ? 'background: #3b82f6; color: white; font-weight: bold;' : 'color: #666;' }}">
                FR
            </a>
        </div>
    </div>

    <div class="login-container">
        <!-- Laboratory Information -->
        <div class="lab-info">
            <h3><i class="fas fa-microscope"></i> {{ __('messages.lab_name') }}</h3>
            <p>{{ __('messages.admin_panel') }}</p>
        </div>

        <!-- Login Icon -->
        <div class="login-icon">
            <i class="fas fa-lock"></i>
        </div>

        <h2>{{ __('messages.admin_login_header') }}</h2>
        <p>{{ __('messages.admin_login_subtitle') }}</p>

        <!-- Error Messages -->
        @if(session('error'))
            <div class="alert alert-danger" style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-triangle"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success" style="background: #dcfce7; color: #15803d; padding: 12px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle"></i>
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('auth.administrator') }}" id="loginForm">
            @csrf

            <div class="mb-2 text-center" style="background: #eff6ff; border: 1px dashed #3b82f6; border-radius: 8px; padding: 10px; margin-bottom: 20px; font-size: 13px;">
                <p class="mb-1" style="color: #1e40af; font-weight: bold; margin: 0;">{{ __('messages.master_test_account') ?? 'حساب التجربة الموحد' }}</p>
                <code style="color: #2563eb; font-weight: bold;">admin / admin123</code>
            </div>

            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> {{ __('messages.username_or_email') }}</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text"
                           id="username"
                           name="name"
                           class="form-control"
                           placeholder="{{ __('messages.username') }}"
                           value="{{ old('name') }}"
                           required
                           autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-key"></i> {{ __('messages.password') }}</label>
                <div class="input-icon password-container">
                    <i class="fas fa-key"></i>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control"
                           placeholder="{{ __('messages.password') }}"
                           required>
                    <button type="button" class="toggle-password" onclick="togglePassword()" style="border: none; background: transparent; cursor: pointer; position: absolute; left: 10px; top: 50%; transform: translateY(-50%);">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group" style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">
                <label style="display: inline-flex; align-items: center; font-weight: normal; cursor: pointer;">
                    <input type="checkbox" name="remember" style="{{ app()->getLocale() == 'ar' ? 'margin-left: 8px;' : 'margin-right: 8px;' }}">
                    {{ __('messages.remember_me') }}
                </label>
            </div>

            <button type="submit" class="btn btn-primary" id="loginBtn" style="background: #3b82f6; color: white; border: none; padding: 12px; border-radius: 8px; width: 100%; font-weight: bold; cursor: pointer; transition: background 0.2s;">
                <i class="fas fa-sign-in-alt"></i>
                {{ __('messages.login') ?? 'Login' }}
            </button>
        </form>

        <!-- Footer -->
        <div class="login-footer" style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; color: #666; font-size: 13px;">
            <p><i class="fas fa-shield-alt"></i> {{ __('messages.secure_system') }}</p>
            <p>{{ __('messages.all_rights_reserved_year') }}</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const icon = document.querySelector('.toggle-password i');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
