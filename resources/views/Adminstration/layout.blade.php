<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    @yield('styles')


</head>

<body>
    @auth('administrator')
    <header class="admin-header">
        <div class="header-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h1>
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px; width: auto; vertical-align: middle; border-radius: 6px;">
            </h1>
        </div>
        <div class="admin-actions d-flex align-items-center gap-2">
            {{-- Header Language Switcher --}}
            <div class="d-flex bg-white rounded-pill p-1 border shadow-sm mx-2" style="height: 36px; border: 1px solid #dee2e6 !important;">
                <a href="{{ route('lang.switch', 'ar') }}" 
                   class="btn btn-sm rounded-pill px-3 d-flex align-items-center {{ app()->getLocale() == 'ar' ? 'btn-primary text-white fw-bold' : 'btn-link text-muted text-decoration-none' }}"
                   style="font-size: 11px; white-space: nowrap;">
                    العربية
                </a>
                <a href="{{ route('lang.switch', 'fr') }}" 
                   class="btn btn-sm rounded-pill px-3 d-flex align-items-center {{ app()->getLocale() == 'fr' ? 'btn-primary text-white fw-bold' : 'btn-link text-muted text-decoration-none' }}"
                   style="font-size: 11px; white-space: nowrap;">
                    FR
                </a>
            </div>
            
            <form action="{{ route('administrator.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm px-3">
                    <i class="fas fa-sign-out-alt"></i> <span class="d-none d-md-inline">{{ __('messages.logout') }}</span>
                </button>
            </form>
        </div>
    </header>

    <div class="admin-container">
        <nav class="admin-sidebar">
            <ul class="sidebar-menu">
                {{-- Sidebar Language Switcher (Mobile/Secondary) --}}
                <li class="d-md-none border-bottom mb-2 pb-2 px-3">
                    <div class="d-flex bg-white bg-opacity-10 rounded-pill p-1 border border-white border-opacity-25" style="height: 34px;">
                        <a href="{{ route('lang.switch', 'ar') }}" 
                           class="btn btn-sm rounded-pill flex-fill {{ app()->getLocale() == 'ar' ? 'btn-light text-primary fw-bold' : 'text-white text-decoration-none' }}"
                           style="font-size: 10px;">
                            العربية
                        </a>
                        <a href="{{ route('lang.switch', 'fr') }}" 
                           class="btn btn-sm rounded-pill flex-fill {{ app()->getLocale() == 'fr' ? 'btn-light text-primary fw-bold' : 'text-white text-decoration-none' }}"
                           style="font-size: 10px;">
                            FR
                        </a>
                    </div>
                </li>
                <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> {{ __('messages.dashboard') }}
                    </a></li>
                <li><a href="{{ route('reservation.requests') }}" class="{{ request()->routeIs('reservation.requests*') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i> {{ __('messages.reservation_requests') }}
                    </a></li>
                <li><a href="{{ route('reservations') }}" class="{{ request()->routeIs('reservations') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i> {{ __('messages.manage_reservations') }}
                    </a></li>
                <li><a href="{{ route('admin.bookings.statistics') }}" class="{{ request()->routeIs('admin.bookings.statistics') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i> {{ __('messages.statistics_analytics') }}
                    </a></li>
                <li><a href="{{ route('analyses') }}" class="{{ request()->routeIs('analyses') || request()->routeIs('eligibility*') ? 'active' : '' }}">
                        <i class="fas fa-flask"></i> {{ __('messages.manage_analyses') }}
                    </a></li>
                <li><a href="{{ route('messages') }}" class="{{ request()->routeIs('messages') ? 'active' : '' }}">
                        <i class="fas fa-envelope"></i> {{ __('messages.send_messages') }}
                    </a></li>
                <li><a href="{{ route('admin.patients.index') }}" class="{{ request()->routeIs('admin.patients*') ? 'active' : '' }}">
                        <i class="fas fa-mobile-alt"></i> {{ __('messages.patient_access') }}
                    </a></li>
                <li><a href="{{ route('admin.doctors.index') }}" class="{{ request()->routeIs('admin.doctors*') ? 'active' : '' }}">
                        <i class="fas fa-user-md"></i> {{ __('messages.doctors_management') }}
                    </a></li>
                <li><a href="{{ route('admin.quality.dashboard') }}" class="{{ request()->routeIs('admin.quality*') ? 'active' : '' }}" style="color: #e65100;">
                        <i class="fas fa-shield-alt"></i> {{ __('messages.lab_quality') }}
                    </a></li>
                <li class="sidebar-divider"></li>
                <li><a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> {{ __('messages.system_settings') }}
                    </a></li>
            </ul>
        </nav>

        <main class="admin-main">
            @yield('content')
        </main>
    </div>
    @else
    <div class="login-redirect">
        <p>{{ __('messages.must_login_first') }}</p>
        <a href="/auth" class="btn btn-primary">{{ __('messages.login') }}</a> {{-- Fixed link --}}
    </div>
    @endauth

    {{-- Notifications --}}
    @if(session('success'))
    <div class="notification success show">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="notification error show">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="notification error show">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="{{ asset('js/dashboard/base.js') }}" type="module"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar logic
            const sidebarToggle = document.getElementById('sidebarToggle');
            const adminSidebar = document.querySelector('.admin-sidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            
            if (sidebarToggle && adminSidebar && sidebarBackdrop) {
                const toggleSidebar = () => {
                    adminSidebar.classList.toggle('active');
                    sidebarBackdrop.classList.toggle('active');
                };

                sidebarToggle.addEventListener('click', toggleSidebar);
                sidebarBackdrop.addEventListener('click', toggleSidebar);

                // Close sidebar when clicking a link on mobile
                adminSidebar.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth <= 768) {
                            adminSidebar.classList.remove('active');
                            sidebarBackdrop.classList.remove('active');
                        }
                    });
                });
            }

        });

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : (type === 'info' ? 'info' : 'danger')} shadow-sm border-0 mb-2 py-2 px-3 fade show`;
            toast.style.minWidth = '250px';
            toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : (type === 'info' ? 'fa-info-circle' : 'fa-exclamation-circle')} me-2"></i>
                    <span class="small">${message}</span>
                </div>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 200);
            }, 3000);
        }
    </script>

    <!-- Page Specific JS -->
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