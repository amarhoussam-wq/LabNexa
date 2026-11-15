@extends('Adminstration.layout')

@section('title', __('messages.dashboard'))

@section('styles')
<style>
    /* Executive KPI Dashboard Styles */
    .dashboard-welcome-banner {
        background: linear-gradient(135deg, #0B1120 0%, #1E293B 100%);
        border-radius: var(--admin-radius);
        padding: 32px 36px;
        color: #fff;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: var(--admin-shadow-lg);
    }

    .dashboard-welcome-banner::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(0, 102, 255, 0.25) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .welcome-content {
        position: relative;
        z-index: 2;
    }

    .welcome-header-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 100px;
        background: rgba(0, 102, 255, 0.2);
        border: 1px solid rgba(0, 102, 255, 0.4);
        font-size: 0.8rem;
        font-weight: 700;
        color: #60A5FA;
        margin-bottom: 14px;
    }

    .welcome-title {
        font-size: 1.85rem;
        font-weight: 900;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .welcome-date {
        color: var(--slate-400);
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* KPI Cards Grid */
    .kpi-grid-modern {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 20px;
        margin-bottom: 36px;
    }

    .kpi-card-modern {
        background: #fff;
        border-radius: var(--admin-radius);
        padding: 24px 22px;
        border: 1px solid var(--admin-border);
        box-shadow: var(--admin-shadow);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .kpi-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: var(--admin-shadow-lg);
        border-color: rgba(0, 102, 255, 0.3);
    }

    .kpi-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--card-accent, var(--admin-primary));
    }

    .kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .kpi-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--icon-bg, #EBF3FF);
        color: var(--icon-color, #0066FF);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }

    .kpi-badge-trend {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 100px;
        background: #ECFDF5;
        color: #059669;
    }

    .kpi-number {
        font-size: 2.2rem;
        font-weight: 900;
        color: var(--admin-text-main);
        line-height: 1;
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }

    .kpi-label {
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--admin-text-muted);
    }

    /* Quick Shortcuts Grid */
    .shortcuts-section {
        background: #fff;
        border-radius: var(--admin-radius);
        padding: 30px;
        border: 1px solid var(--admin-border);
        box-shadow: var(--admin-shadow);
    }

    .shortcuts-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 22px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--admin-border);
    }

    .shortcuts-header h3 {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--admin-text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .shortcuts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
    }

    .shortcut-card {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border-radius: 12px;
        background: var(--admin-bg);
        border: 1px solid var(--admin-border);
        text-decoration: none;
        color: var(--admin-text-main);
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.25s;
    }

    .shortcut-card:hover {
        background: #fff;
        border-color: var(--admin-primary);
        box-shadow: 0 4px 14px rgba(0, 102, 255, 0.1);
        transform: translateY(-2px);
        color: var(--admin-primary);
    }

    .shortcut-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: var(--admin-primary);
        border: 1px solid var(--admin-border);
        transition: all 0.25s;
    }

    .shortcut-card:hover .shortcut-icon {
        background: var(--admin-primary);
        color: #fff;
        border-color: var(--admin-primary);
    }
</style>
@endsection

@section('content')

<!-- Welcome Executive Banner -->
<div class="dashboard-welcome-banner">
    <div class="welcome-content">
        <div class="welcome-header-tag">
            <i class="fas fa-shield-halved"></i>
            <span>{{ app()->getLocale() == 'ar' ? 'لوحة القيادة الإدارية والتحكم السريري' : 'Console Administrative et Contrôle Clinique' }}</span>
        </div>
        <h2 class="welcome-title">
            {{ app()->getLocale() == 'ar' ? 'مرحباً بك في منصة' : 'Bienvenue sur' }} {{ __('messages.lab_name') }}
        </h2>
        <div class="welcome-date">
            <i class="fas fa-calendar-day"></i>
            <span>{{ \Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('l j F Y — H:i') }}</span>
        </div>
    </div>
</div>

<!-- Modern KPI Metrics Cards -->
<div class="kpi-grid-modern">
    <!-- 1. New Requests -->
    <div class="kpi-card-modern" style="--card-accent:#0066FF; --icon-bg:#EBF3FF; --icon-color:#0066FF;">
        <div class="kpi-top">
            <div class="kpi-icon-wrap"><i class="fas fa-inbox"></i></div>
            <span class="kpi-badge-trend" style="background:#EBF3FF; color:#0066FF;">Live</span>
        </div>
        <div>
            <div class="kpi-number">{{ $newRequests }}</div>
            <div class="kpi-label">{{ __('messages.reservation_requests') }}</div>
        </div>
    </div>

    <!-- 2. Total Bookings -->
    <div class="kpi-card-modern" style="--card-accent:#10B981; --icon-bg:#ECFDF5; --icon-color:#10B981;">
        <div class="kpi-top">
            <div class="kpi-icon-wrap"><i class="fas fa-calendar-check"></i></div>
            <span class="kpi-badge-trend"><i class="fas fa-arrow-trend-up"></i> +12%</span>
        </div>
        <div>
            <div class="kpi-number">{{ $totalBookings }}</div>
            <div class="kpi-label">{{ __('messages.total_bookings') }}</div>
        </div>
    </div>

    <!-- 3. Pending Bookings -->
    <div class="kpi-card-modern" style="--card-accent:#F59E0B; --icon-bg:#FFFBEB; --icon-color:#D97706;">
        <div class="kpi-top">
            <div class="kpi-icon-wrap"><i class="fas fa-user-clock"></i></div>
            <span class="kpi-badge-trend" style="background:#FFFBEB; color:#D97706;">{{ app()->getLocale() == 'ar' ? 'قيد الانتظار' : 'En attente' }}</span>
        </div>
        <div>
            <div class="kpi-number">{{ $pendingBookings }}</div>
            <div class="kpi-label">{{ __('messages.pending_bookings') }}</div>
        </div>
    </div>

    <!-- 4. Available Analyses -->
    <div class="kpi-card-modern" style="--card-accent:#7C3AED; --icon-bg:#F5F3FF; --icon-color:#7C3AED;">
        <div class="kpi-top">
            <div class="kpi-icon-wrap"><i class="fas fa-flask"></i></div>
            <span class="kpi-badge-trend" style="background:#F5F3FF; color:#7C3AED;">Active</span>
        </div>
        <div>
            <div class="kpi-number">{{ $availableAnalyses }}</div>
            <div class="kpi-label">{{ __('messages.available_analyses') }}</div>
        </div>
    </div>

    <!-- 5. Total Patients -->
    <div class="kpi-card-modern" style="--card-accent:#0D9488; --icon-bg:#F0FDFA; --icon-color:#0D9488;">
        <div class="kpi-top">
            <div class="kpi-icon-wrap"><i class="fas fa-hospital-user"></i></div>
            <span class="kpi-badge-trend"><i class="fas fa-check"></i> Synced</span>
        </div>
        <div>
            <div class="kpi-number">{{ $totalPatients }}</div>
            <div class="kpi-label">{{ __('messages.total_patients') }}</div>
        </div>
    </div>
</div>

<!-- Quick Shortcuts -->
<div class="shortcuts-section">
    <div class="shortcuts-header">
        <h3><i class="fas fa-bolt" style="color:var(--admin-primary);"></i> {{ app()->getLocale() == 'ar' ? 'إجراءات سريعة ومسارات العمل' : 'Raccourcis et Actions Rapides' }}</h3>
    </div>

    <div class="shortcuts-grid">
        <a href="{{ route('reservation.requests') }}" class="shortcut-card">
            <div class="shortcut-icon"><i class="fas fa-clock"></i></div>
            <span>{{ __('messages.reservation_requests') }}</span>
        </a>
        <a href="{{ route('reservations') }}" class="shortcut-card">
            <div class="shortcut-icon"><i class="fas fa-calendar-check"></i></div>
            <span>{{ __('messages.manage_reservations') }}</span>
        </a>
        <a href="{{ route('analyses') }}" class="shortcut-card">
            <div class="shortcut-icon"><i class="fas fa-flask"></i></div>
            <span>{{ __('messages.manage_analyses') }}</span>
        </a>
        <a href="{{ route('admin.quality.dashboard') }}" class="shortcut-card">
            <div class="shortcut-icon" style="color:#EA580C;"><i class="fas fa-shield-virus"></i></div>
            <span>{{ __('messages.lab_quality') }}</span>
        </a>
        <a href="{{ route('admin.patients.index') }}" class="shortcut-card">
            <div class="shortcut-icon"><i class="fas fa-users"></i></div>
            <span>{{ __('messages.patient_access') }}</span>
        </a>
        <a href="{{ route('admin.doctors.index') }}" class="shortcut-card">
            <div class="shortcut-icon"><i class="fas fa-user-doctor"></i></div>
            <span>{{ __('messages.doctors_management') }}</span>
        </a>
        <a href="{{ route('admin.bookings.statistics') }}" class="shortcut-card">
            <div class="shortcut-icon"><i class="fas fa-chart-pie"></i></div>
            <span>{{ __('messages.statistics_analytics') }}</span>
        </a>
        <a href="{{ route('admin.settings') }}" class="shortcut-card">
            <div class="shortcut-icon"><i class="fas fa-sliders"></i></div>
            <span>{{ __('messages.system_settings') }}</span>
        </a>
    </div>
</div>

@endsection
