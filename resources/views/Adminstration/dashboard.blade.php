@extends('Adminstration.layout')

@section('title', __('messages.dashboard'))

@section('content')
<div class="section-header">
    <h2><i class="fas fa-tachometer-alt"></i> {{ __('messages.overview') }}</h2>
    <span>{{ \Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('l j F Y') }}</span>
</div>

<div class="stats-grid">
    <div class="stat-card" style="background: #e3f2fd; border: 1px solid #bbdefb;">
        <i class="fas fa-plus-circle" style="color: #1976d2;"></i>
        <span class="stat-number" style="color: #1565c0;">{{ $newRequests }}</span>
        <span class="stat-label" style="color: #1565c0;">{{ __('messages.reservation_requests') }}</span>
    </div>
    <div class="stat-card">
        <i class="fas fa-calendar-check"></i>
        <span class="stat-number">{{ $totalBookings }}</span>
        <span class="stat-label">{{ __('messages.total_bookings') }}</span>
    </div>
    <div class="stat-card">
        <i class="fas fa-user-clock"></i>
        <span class="stat-number">{{ $pendingBookings }}</span>
        <span class="stat-label">{{ __('messages.pending_bookings') }}</span>
    </div>
    <div class="stat-card">
        <i class="fas fa-flask"></i>
        <span class="stat-number">{{ $availableAnalyses }}</span>
        <span class="stat-label">{{ __('messages.available_analyses') }}</span>
    </div>
    <div class="stat-card">
        <i class="fas fa-users"></i>
        <span class="stat-number">{{ $totalPatients }}</span>
        <span class="stat-label">{{ __('messages.total_patients') }}</span>
    </div>
</div>
@endsection