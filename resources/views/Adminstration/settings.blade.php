@extends('Adminstration.layout')

@section('title', __('messages.system_settings'))

@section('styles')
<style>
    .settings-container {
        padding: 30px;
        max-width: 800px;
        margin: 0 auto;
    }
    .settings-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
        padding: 30px;
    }
    .settings-header {
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 25px;
        padding-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .settings-header h2 {
        font-size: 1.5rem;
        margin: 0;
        color: #1e293b;
        font-weight: 700;
    }
    .settings-icon {
        width: 50px;
        height: 50px;
        background: #eff6ff;
        color: #3b82f6;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .form-group label {
        font-weight: 600;
        color: #475569;
        margin-bottom: 10px;
    }
    .form-control {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .help-text {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 8px;
    }
    .btn-save {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-top: 20px;
    }
    .btn-save:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
    }
    .sidebar-divider {
        height: 1px;
        background: rgba(255,255,255,0.1);
        margin: 15px 0;
    }
</style>
@endsection

@section('content')
<div class="settings-container">
    <div class="settings-card">
        <div class="settings-header">
            <div class="settings-icon">
                <i class="fas fa-users-cog"></i>
            </div>
            <div>
                <h2>{{ __('messages.booking_capacity_settings') }}</h2>
                <p class="text-muted mb-0">{{ __('messages.booking_capacity_desc') ?? 'Control your laboratory capacity' }}</p>
            </div>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            
            <div class="row">
                <!-- Sunday -->
                <div class="col-md-6 form-group mb-4">
                    <label for="booking_limit_sunday">
                        <i class="fas fa-calendar-day text-primary me-2"></i>
                        {{ __('messages.max_limit_sun') }}
                    </label>
                    <input type="number" class="form-control" id="booking_limit_sunday" name="booking_limit_sunday" 
                           value="{{ old('booking_limit_sunday', $limits['booking_limit_sunday'] ?? 50) }}" min="0" required>
                </div>

                <!-- Monday -->
                <div class="col-md-6 form-group mb-4">
                    <label for="booking_limit_monday">
                        <i class="fas fa-calendar-day text-primary me-2"></i>
                        {{ __('messages.max_limit_mon') }}
                    </label>
                    <input type="number" class="form-control" id="booking_limit_monday" name="booking_limit_monday" 
                           value="{{ old('booking_limit_monday', $limits['booking_limit_monday'] ?? 50) }}" min="0" required>
                </div>

                <!-- Tuesday -->
                <div class="col-md-6 form-group mb-4">
                    <label for="booking_limit_tuesday">
                        <i class="fas fa-calendar-day text-primary me-2"></i>
                        {{ __('messages.max_limit_tue') }}
                    </label>
                    <input type="number" class="form-control" id="booking_limit_tuesday" name="booking_limit_tuesday" 
                           value="{{ old('booking_limit_tuesday', $limits['booking_limit_tuesday'] ?? 50) }}" min="0" required>
                </div>

                <!-- Wednesday -->
                <div class="col-md-6 form-group mb-4">
                    <label for="booking_limit_wednesday">
                        <i class="fas fa-calendar-day text-primary me-2"></i>
                        {{ __('messages.max_limit_wed') }}
                    </label>
                    <input type="number" class="form-control" id="booking_limit_wednesday" name="booking_limit_wednesday" 
                           value="{{ old('booking_limit_wednesday', $limits['booking_limit_wednesday'] ?? 50) }}" min="0" required>
                </div>

                <!-- Thursday -->
                <div class="col-md-6 form-group mb-4">
                    <label for="booking_limit_thursday">
                        <i class="fas fa-calendar-day text-primary me-2"></i>
                        {{ __('messages.max_limit_thu') }}
                    </label>
                    <input type="number" class="form-control" id="booking_limit_thursday" name="booking_limit_thursday" 
                           value="{{ old('booking_limit_thursday', $limits['booking_limit_thursday'] ?? 50) }}" min="0" required>
                </div>

                <!-- Friday -->
                <div class="col-md-6 form-group mb-4">
                    <label for="booking_limit_friday">
                        <i class="fas fa-calendar-day text-primary me-2"></i>
                        {{ __('messages.max_limit_fri') }}
                    </label>
                    <input type="number" class="form-control" id="booking_limit_friday" name="booking_limit_friday" 
                           value="{{ old('booking_limit_friday', $limits['booking_limit_friday'] ?? 50) }}" min="0" required>
                </div>

                <!-- Saturday -->
                <div class="col-md-6 form-group mb-4">
                    <label for="booking_limit_saturday">
                        <i class="fas fa-calendar-day text-primary me-2"></i>
                        {{ __('messages.max_limit_sat') }}
                    </label>
                    <input type="number" class="form-control" id="booking_limit_saturday" name="booking_limit_saturday" 
                           value="{{ old('booking_limit_saturday', $limits['booking_limit_saturday'] ?? 50) }}" min="0" required>
                </div>
            </div>

            <div class="help-text mb-4">
                <i class="fas fa-info-circle me-1"></i>
                {{ __('messages.capacity_help_text') }}
            </div>

            <div class="text-end">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save me-2"></i>
                    {{ __('messages.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
