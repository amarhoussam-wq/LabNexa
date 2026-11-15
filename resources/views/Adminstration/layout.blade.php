<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — {{ __('messages.lab_name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- CSS Framework & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Original CSS Fallbacks -->
    <link href="{{ asset('css/dashboard/base.css') }}" rel="stylesheet">

    @if(request()->routeIs('dashboard'))
    <link href="{{ asset('css/dashboard/dashboard.css') }}" rel="stylesheet">
    @elseif(request()->routeIs('analyses*'))
    <link href="{{ asset('css/dashboard/analyses.css') }}" rel="stylesheet">
    @elseif(request()->routeIs('reservation.requests*'))
    <link href="{{ asset('css/dashboard/reservation-requests.css') }}" rel="stylesheet">
    @elseif(request()->routeIs('reservations*'))
    <link href="{{ asset('css/dashboard/reservations.css') }}" rel="stylesheet">
    @elseif(request()->routeIs('messages*'))
    <link href="{{ asset('css/dashboard/messages.css') }}" rel="stylesheet">
    @endif

    <style>
        /* ══════════════════════════════════════════════════════════════════════
           LabNexa — Elite Clinical Administration Portal
           Precision Healthcare Console UI
        ══════════════════════════════════════════════════════════════════════ */
        :root {
            --admin-primary: #0066FF;
            --admin-primary-dark: #0052CC;
            --admin-navy: #0B1120;
            --admin-sidebar-bg: #0F172A;
            --admin-sidebar-border: rgba(255, 255, 255, 0.08);
            --admin-surface: #FFFFFF;
            --admin-bg: #F8FAFC;
            --admin-text-main: #0F172A;
            --admin-text-muted: #64748B;
            --admin-border: #E2E8F0;
            --admin-radius: 14px;
            --admin-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --admin-shadow-lg: 0 12px 28px -4px rgba(15, 23, 42, 0.08);
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal'" : "'Inter'" }}, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--admin-bg);
            color: var(--admin-text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ── Top Header Bar ── */
        .admin-header-modern {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--admin-border);
            padding: 12px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-brand img {
            height: 40px;
            width: auto;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 102, 255, 0.15);
        }

        .header-brand h1 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--admin-navy);
            margin: 0;
            letter-spacing: -0.3px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .lang-pill-header {
            display: flex;
            background: var(--admin-bg);
            border: 1px solid var(--admin-border);
            border-radius: 100px;
            padding: 3px;
            gap: 2px;
        }

        .lang-pill-header a {
            padding: 5px 14px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 100px;
            text-decoration: none;
            color: var(--admin-text-muted);
            transition: all 0.2s;
        }

        .lang-pill-header a.active {
            background: var(--admin-primary);
            color: #fff;
            box-shadow: 0 2px 6px rgba(0, 102, 255, 0.25);
        }

        .admin-user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 14px;
            background: var(--admin-bg);
            border: 1px solid var(--admin-border);
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--admin-text-main);
        }

        .user-avatar-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--admin-primary), #7C3AED);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 800;
        }

        .btn-logout-modern {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--admin-radius);
            font-size: 0.85rem;
            font-weight: 700;
            background: #FEE2E2;
            color: #DC2626;
            border: 1px solid #FECACA;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-logout-modern:hover {
            background: #DC2626;
            color: #fff;
            transform: translateY(-1px);
        }

        /* ── Sidebar & Layout ── */
        .admin-layout-wrapper {
            display: flex;
            flex: 1;
            min-height: calc(100vh - 65px);
        }

        .admin-sidebar-modern {
            width: 270px;
            background: var(--admin-sidebar-bg);
            border-inline-end: 1px solid var(--admin-sidebar-border);
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: sticky;
            top: 65px;
            height: calc(100vh - 65px);
            overflow-y: auto;
            flex-shrink: 0;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .admin-sidebar-modern::-webkit-scrollbar {
            width: 4px;
        }
        .admin-sidebar-modern::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }

        .sidebar-section-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.35);
            padding: 14px 12px 6px;
        }

        .sidebar-link-modern {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: var(--admin-radius);
            font-size: 0.92rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sidebar-link-modern i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
            color: rgba(255, 255, 255, 0.45);
            transition: color 0.2s;
        }

        .sidebar-link-modern:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            transform: translateX(3px);
        }
        [dir="rtl"] .sidebar-link-modern:hover {
            transform: translateX(-3px);
        }

        .sidebar-link-modern:hover i {
            color: var(--admin-primary);
        }

        .sidebar-link-modern.active {
            background: linear-gradient(135deg, rgba(0, 102, 255, 0.25) 0%, rgba(0, 102, 255, 0.1) 100%);
            color: #fff;
            border: 1px solid rgba(0, 102, 255, 0.35);
            box-shadow: 0 4px 12px rgba(0, 102, 255, 0.2);
        }

        .sidebar-link-modern.active i {
            color: #60A5FA;
        }

        .sidebar-link-modern.highlight-quality {
            color: #FB923C;
        }
        .sidebar-link-modern.highlight-quality i {
            color: #F97316;
        }

        /* ── Main Workspace ── */
        .admin-main-content {
            flex: 1;
            padding: 36px 40px;
            overflow-y: auto;
            max-width: 1440px;
            margin: 0 auto;
            width: 100%;
        }

        /* Toast Container */
        #toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 999999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        [dir="rtl"] #toast-container { right: auto; left: 24px; }

        /* Notification */
        .notification {
            position: fixed;
            top: 24px;
            right: 24px;
            padding: 14px 22px;
            border-radius: var(--admin-radius);
            font-weight: 600;
            color: #fff;
            z-index: 99999;
            box-shadow: var(--admin-shadow-lg);
            display: none;
        }
        .notification.show { display: block; }
        .notification.success { background: #10B981; }
        .notification.error { background: #EF4444; }

        /* ── Mobile Sidebar Toggle & Backdrop ── */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1010;
        }
        .sidebar-backdrop.active { display: block; }

        .btn-sidebar-toggle-modern {
            display: none;
            background: none;
            border: none;
            font-size: 1.3rem;
            color: var(--admin-text-main);
            padding: 6px;
            cursor: pointer;
        }

        @media (max-width: 992px) {
            .btn-sidebar-toggle-modern {
                display: block;
            }
            .admin-sidebar-modern {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                height: 100vh;
                z-index: 1015;
                transform: translateX(-100%);
            }
            [dir="rtl"] .admin-sidebar-modern {
                left: auto;
                right: 0;
                transform: translateX(100%);
            }
            .admin-sidebar-modern.active {
                transform: translateX(0);
            }
            .admin-main-content {
                padding: 24px 16px;
            }
        }
    </style>

    @yield('styles')
</head>

<body>
    @auth('administrator')
    <!-- Top Modern Header -->
    <header class="admin-header-modern">
        <div class="header-brand">
            <button class="btn-sidebar-toggle-modern" id="sidebarToggle" aria-label="Toggle Navigation">
                <i class="fas fa-bars"></i>
            </button>
            <a href="{{ route('dashboard') }}" style="display:flex; align-items:center; gap:12px; text-decoration:none;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/822/822143.png'">
                <h1>{{ __('messages.lab_name') }} <span style="font-size:0.75rem; font-weight:700; color:var(--admin-primary); background:rgba(0,102,255,0.1); padding:3px 8px; border-radius:100px; margin-inline-start:6px;">Admin</span></h1>
            </a>
        </div>

        <div class="header-actions">
            <!-- Language Pill Switcher -->
            <div class="lang-pill-header">
                <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() == 'ar' ? 'active' : '' }}">العربية</a>
                <a href="{{ route('lang.switch', 'fr') }}" class="{{ app()->getLocale() == 'fr' ? 'active' : '' }}">Français</a>
            </div>

            <!-- Admin Profile Indicator -->
            <div class="admin-user-pill d-none d-md-flex">
                <div class="user-avatar-circle">A</div>
                <span>{{ Auth::guard('administrator')->user()->name ?? 'Administrator' }}</span>
            </div>

            <!-- Logout -->
            <form action="{{ route('administrator.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout-modern">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                    <span class="d-none d-md-inline">{{ __('messages.logout') }}</span>
                </button>
            </form>
        </div>
    </header>

    <div class="admin-layout-wrapper">
        <!-- Sidebar Navigation -->
        <nav class="admin-sidebar-modern" id="adminSidebar">
            
            <div class="sidebar-section-label">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Général' }}</div>
            <a href="{{ route('dashboard') }}" class="sidebar-link-modern {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>{{ __('messages.dashboard') }}</span>
            </a>

            <div class="sidebar-section-label">{{ app()->getLocale() == 'ar' ? 'الحجوزات والمواعيد' : 'Réservations' }}</div>
            <a href="{{ route('reservation.requests') }}" class="sidebar-link-modern {{ request()->routeIs('reservation.requests*') ? 'active' : '' }}">
                <i class="fas fa-clock"></i>
                <span>{{ __('messages.reservation_requests') }}</span>
            </a>
            <a href="{{ route('reservations') }}" class="sidebar-link-modern {{ request()->routeIs('reservations') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span>{{ __('messages.manage_reservations') }}</span>
            </a>
            <a href="{{ route('admin.bookings.statistics') }}" class="sidebar-link-modern {{ request()->routeIs('admin.bookings.statistics') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i>
                <span>{{ __('messages.statistics_analytics') }}</span>
            </a>

            <div class="sidebar-section-label">{{ app()->getLocale() == 'ar' ? 'المخبر والتحاليل' : 'Laboratoire' }}</div>
            <a href="{{ route('analyses') }}" class="sidebar-link-modern {{ request()->routeIs('analyses') || request()->routeIs('eligibility*') ? 'active' : '' }}">
                <i class="fas fa-flask"></i>
                <span>{{ __('messages.manage_analyses') }}</span>
            </a>
            <a href="{{ route('admin.quality.dashboard') }}" class="sidebar-link-modern highlight-quality {{ request()->routeIs('admin.quality*') ? 'active' : '' }}">
                <i class="fas fa-shield-virus"></i>
                <span>{{ __('messages.lab_quality') }}</span>
            </a>

            <div class="sidebar-section-label">{{ app()->getLocale() == 'ar' ? 'المستخدمون والتواصل' : 'Utilisateurs' }}</div>
            <a href="{{ route('admin.patients.index') }}" class="sidebar-link-modern {{ request()->routeIs('admin.patients*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>{{ __('messages.patient_access') }}</span>
            </a>
            <a href="{{ route('admin.doctors.index') }}" class="sidebar-link-modern {{ request()->routeIs('admin.doctors*') ? 'active' : '' }}">
                <i class="fas fa-user-doctor"></i>
                <span>{{ __('messages.doctors_management') }}</span>
            </a>
            <a href="{{ route('messages') }}" class="sidebar-link-modern {{ request()->routeIs('messages') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i>
                <span>{{ __('messages.send_messages') }}</span>
            </a>

            <div class="sidebar-section-label">{{ app()->getLocale() == 'ar' ? 'النظام' : 'Système' }}</div>
            <a href="{{ route('admin.settings') }}" class="sidebar-link-modern {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="fas fa-sliders"></i>
                <span>{{ __('messages.system_settings') }}</span>
            </a>
            <a href="{{ route('home') }}" class="sidebar-link-modern" target="_blank">
                <i class="fas fa-globe"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'زيارة الموقع' : 'Voir le site' }}</span>
            </a>
        </nav>

        <!-- Main Workspace -->
        <main class="admin-main-content">
            @yield('content')
        </main>
    </div>

    @else
    <div style="min-height:100vh; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:16px;">
        <p style="font-size:1.1rem; color:var(--admin-text-muted);">{{ __('messages.must_login_first') }}</p>
        <a href="{{ route('access', ['tab' => 'admin']) }}" class="btn btn-primary px-4 py-2" style="border-radius:10px;">{{ __('messages.login') }}</a>
    </div>
    @endauth

    <!-- Flash Notifications -->
    @if(session('success'))
    <div class="notification success show">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="notification error show">
        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
    </div>
    @endif

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    <div id="toast-container"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="{{ asset('js/dashboard/base.js') }}" type="module"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const adminSidebar = document.getElementById('adminSidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            
            if (sidebarToggle && adminSidebar && sidebarBackdrop) {
                const toggleSidebar = () => {
                    adminSidebar.classList.toggle('active');
                    sidebarBackdrop.classList.toggle('active');
                };

                sidebarToggle.addEventListener('click', toggleSidebar);
                sidebarBackdrop.addEventListener('click', toggleSidebar);

                adminSidebar.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth <= 992) {
                            adminSidebar.classList.remove('active');
                            sidebarBackdrop.classList.remove('active');
                        }
                    });
                });
            }

            // Auto-hide session notifications
            const notifs = document.querySelectorAll('.notification.show');
            notifs.forEach(n => {
                setTimeout(() => { n.style.display = 'none'; }, 4500);
            });
        });

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : (type === 'info' ? 'info' : 'danger')} shadow border-0 py-2 px-3 fade show`;
            toast.style.borderRadius = '12px';
            toast.style.minWidth = '280px';
            toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : (type === 'info' ? 'fa-info-circle' : 'fa-exclamation-circle')} me-2"></i>
                    <span class="small fw-semibold">${message}</span>
                </div>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 200);
            }, 3200);
        }
    </script>

    @if(request()->routeIs('dashboard'))
    <script src="{{ asset('js/dashboard/dashboard.js') }}" type="module"></script>
    @elseif(request()->routeIs('analyses*'))
    <script src="{{ asset('js/dashboard/analyses.js') }}" type="module"></script>
    @elseif(request()->routeIs('reservations*'))
    <script src="{{ asset('js/dashboard/reservations.js') }}" type="module"></script>
    @elseif(request()->routeIs('messages*'))
    <script src="{{ asset('js/dashboard/messages.js') }}" type="module"></script>
    @endif

    @yield('scripts')
</body>
</html>
