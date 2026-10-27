<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.lab_name') }} - {{ __('messages.confirm_booking') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        .success-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            max-width: 650px;
            width: 100%;
            overflow: hidden;
            animation: cardFadeIn 0.6s ease;
        }
        @keyframes cardFadeIn {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .success-header {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .success-header i.main-icon {
            font-size: 4rem;
            margin-bottom: 15px;
            animation: bounceIn 1s ease;
        }
        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .success-header h1 {
            font-size: 1.8rem;
            margin: 0;
            border: none;
            color: white;
            justify-content: center;
        }
        .success-header p {
            margin-top: 10px;
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .success-body {
            padding: 30px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #555;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-value {
            color: #2c3e50;
            font-weight: 500;
        }
        .prep-section {
            margin-top: 25px;
            background: #fff8e1;
            border: 1px solid #ffecb3;
            border-radius: 12px;
            padding: 20px;
        }
        .prep-section h3 {
            color: #f57c00;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .prep-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            border-right: 4px solid #f57c00;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .prep-item h4 {
            color: #2c3e50;
            margin: 0 0 8px 0;
        }
        .prep-item p {
            color: #666;
            margin: 0;
            line-height: 1.6;
        }
        .success-footer {
            padding: 20px 30px;
            text-align: center;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }
        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #2c3e50;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-home:hover {
            background: #34495e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            margin-left: 10px;
        }
        .btn-download:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52,152,219,0.3);
        }
        /* Notification */
        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            left: 20px;
            padding: 15px 25px;
            border-radius: 12px;
            color: white;
            z-index: 9999;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: none;
            font-weight: 600;
            text-align: center;
            animation: slideDown 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        @keyframes slideDown {
            from { transform: translateY(-100px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .notification.success { background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); }
        .notification.error { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); }
        .notification.info { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); }

        @media (max-width: 500px) {
            .success-header h1 { font-size: 1.4rem; }
            .success-footer { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div id="notification"></div>
    <div class="success-page">
        <div class="success-card">
            <div class="success-header" style="position: relative;">
                {{-- Integrated Language Toggle --}}
                <div style="position: absolute; top: 15px; {{ app()->getLocale() == 'ar' ? 'left: 15px;' : 'right: 15px;' }}">
                    <div class="d-flex bg-white rounded-pill p-1 shadow-sm" style="opacity: 0.9;">
                        <a href="{{ route('lang.switch', 'ar') }}" 
                           class="text-center rounded-pill px-2 py-0 text-decoration-none {{ app()->getLocale() == 'ar' ? 'bg-primary text-white fw-bold' : 'text-muted' }}"
                           style="font-size: 10px;">
                            AR
                        </a>
                        <a href="{{ route('lang.switch', 'fr') }}" 
                           class="text-center rounded-pill px-2 py-0 text-decoration-none {{ app()->getLocale() == 'fr' ? 'bg-primary text-white fw-bold' : 'text-muted' }}"
                           style="font-size: 10px;">
                            FR
                        </a>
                    </div>
                </div>

                <i class="fas fa-check-circle main-icon"></i>
                <h1>{{ __('messages.booking_success_title') }}</h1>
                <p>{{ __('messages.booking_success_subtitle') }}</p>
            </div>
            
            <div class="success-body">
                {{-- Top Section: Info + QR --}}
                <div style="display: flex; gap: 20px; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap;">
                    {{-- Patient Info --}}
                    <div style="flex: 1; min-width: 250px;">
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-hashtag"></i> {{ __('messages.visit_id') }}</span>
                            <span class="info-value" style="background: #eef2f7; padding: 2px 8px; border-radius: 4px; font-weight: bold; color: #2c3e50;">#{{ $reservation->id }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-user"></i> {{ __('messages.full_name') }}</span>
                            <span class="info-value">{{ $reservation->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-phone"></i> {{ __('messages.phone_number') }}</span>
                            <span class="info-value" style="direction: ltr;">{{ $reservation->phone }}</span>
                        </div>
                    </div>

                    {{-- QR Code for Lab Tech --}}
                    <div style="text-align: center; background: white; padding: 12px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #eee; width: 120px; margin: 0 auto;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('reservation.requests') . '?qid=' . $reservation->id . '&t=' . time()) }}" 
                             alt="QR Admin Link" 
                             style="width: 100%; border-radius: 5px;">
                        <div style="font-size: 9px; color: #7f8c8d; margin-top: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-qrcode"></i> LAB SCAN
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <span class="info-label"><i class="fas fa-calendar"></i> {{ __('messages.preferred_date_label') }}</span>
                    <span class="info-value">{{ $reservation->preferred_date }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-clock"></i> {{ __('messages.preferred_time_label') }}</span>
                    <span class="info-value">{{ $reservation->preferred_time }}</span>
                </div>
                
                @if($analyses->count() > 0)
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-flask"></i> {{ __('messages.analysis_types') }}</span>
                    <span class="info-value" style="color: #27ae60; font-weight: 600;">{{ $analyses->pluck('name')->implode(', ') }}</span>
                </div>
                @endif

                @if($preparations->count() > 0)
                <div class="prep-section">
                    <h3><i class="fas fa-exclamation-triangle"></i> {{ __('messages.important_preparation_instructions') }}</h3>
                    @foreach($preparations as $prep)
                    <div class="prep-item">
                        <h4><i class="fas fa-flask"></i> {{ $prep->name }}</h4>
                        <p>{{ $prep->preparation_instructions }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="success-footer" style="padding: 20px; text-align: center; gap: 15px; display: flex; flex-direction: column; align-items: center;">
                <a href="{{ route('report.view', ['id' => $reservation->id, 'type' => 'request']) }}" target="_blank" class="btn-download" style="background: linear-gradient(135deg, #2c5282 0%, #3182ce 100%); width: 100%; max-width: 400px; padding: 15px; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 10px; color: white; text-decoration: none; font-weight: 600; box-shadow: 0 4px 15px rgba(49, 130, 206, 0.3);">
                    <i class="fas fa-file-alt" style="font-size: 20px;"></i> 
                    <span>{{ __('Visualiser le Rapport & Instructions (Arabe Saliim)') }}</span>
                </a>
                
                <a href="{{ url('/') }}" class="btn-home" style="color: #4a5568; font-weight: 500; text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_home') }}
                </a>
            </div>
            <div style="text-align: center; padding: 12px; font-size: 13px; color: #718096; background: #f8fafc; border-top: 1px solid #edf2f7; border-radius: 0 0 15px 15px;">
                <i class="fas fa-info-circle"></i> {{ __('Si le rapport ne s\'ouvre pas automatiquement, cliquez sur le bouton bleu ci-dessus.') }}
            </div>
        </div>
    </div>
    <script>
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            if (notification) {
                notification.textContent = message;
                notification.className = 'notification ' + type;
                notification.style.display = 'block';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 5000);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showNotification(@json(session('success')), 'success');
            @endif

            // Auto-Download High-Fidelity PDF Report (Fixes 'It doesn't download automatically')
            @if(session('download_pdf'))
                const downloadUrl = "{{ route('patient.report.download', ['id' => $reservation->id, 'type' => 'request']) }}";
                
                // Show a clear instructions notification
                showNotification("{{ __('messages.viewing_prep_instructions') }}", 'success');

                // Optimized Trigger for Mobile/Desktop behavior
                setTimeout(function() {
                    // Method A: Hidden Link Click
                    const link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = "Report_LabNexa_{{ $reservation->id }}.pdf";
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    
                    // Fallback to Method B (Direct redirect) for strict browsers
                    setTimeout(() => {
                        document.body.removeChild(link);
                        // If we are still on the page and nothing happened (some browsers require direct location change)
                        if (!document.hidden) {
                            window.location.href = downloadUrl;
                        }
                    }, 1000);
                }, 500);
            @endif
        });
    </script>
</body>
</html>
