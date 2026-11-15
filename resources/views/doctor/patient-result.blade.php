<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.view_result') }} - {{ __('messages.lab_name') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --doctor-primary: #1a365d;
            --bg-light: #f7fafc;
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal', sans-serif" : "'Outfit', sans-serif" }};
            background-color: var(--bg-light);
            padding: 40px 0;
        }

        .result-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .card-medical {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header-medical {
            background: var(--doctor-primary);
            color: white;
            padding: 25px 35px;
        }

        .analysis-status-badge {
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-ready { background: #c6f6d5; color: #22543d; }
        .status-pending { background: #feebc8; color: #744210; }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            padding: 30px 35px;
            background: #f8fafc;
            border-bottom: 1px solid #edf2f7;
        }

        .info-label {
            font-size: 13px;
            color: #718096;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .info-value {
            font-weight: 700;
            color: #2d3748;
            font-size: 16px;
        }

        .analysis-list {
            padding: 0;
            margin: 0;
        }

        .analysis-row {
            padding: 20px 35px;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .analysis-row:last-child { border-bottom: none; }

        .btn-download-result {
            background: var(--doctor-primary);
            color: white;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }

        .btn-download-result:hover {
            opacity: 0.9;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="result-container container">
        
        <div class="mb-4">
            <a onclick="smartGoBack(); return false;" href="{{ route('doctor.dashboard') }}" class="text-decoration-none text-muted">
                <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} me-2"></i> {{ __('messages.back_to_dashboard') }}
            </a>
        </div>

        {{-- Floating Language Toggle Pill --}}
        <div class="position-fixed top-0 end-0 p-4" style="z-index: 1050;">
            <div class="d-flex bg-white rounded-pill p-1 border shadow" style="min-width: 120px;">
                <a href="{{ route('lang.switch', 'ar') }}" 
                   class="flex-fill text-center rounded-pill px-3 py-1 text-decoration-none {{ app()->getLocale() == 'ar' ? 'bg-primary text-white fw-bold shadow-sm' : 'text-muted' }}"
                   style="font-size: 13px; transition: all 0.3s;">
                    العربية
                </a>
                <a href="{{ route('lang.switch', 'fr') }}" 
                   class="flex-fill text-center rounded-pill px-3 py-1 text-decoration-none {{ app()->getLocale() == 'fr' ? 'bg-primary text-white fw-bold shadow-sm' : 'text-muted' }}"
                   style="font-size: 13px; transition: all 0.3s;">
                    FR
                </a>
            </div>
        </div>

        <div class="card-medical mb-5">
            <div class="card-header-medical d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <h4 class="fw-bold mb-0 text-white"><i class="fas fa-file-medical me-2"></i> {{ __('messages.patient_report') }}</h4>
                <div class="analysis-status-badge status-{{ in_array($reservation->status, ['ready', 'completed']) ? 'ready' : 'pending' }}">
                    {{ __('messages.' . $reservation->status) }}
                </div>
            </div>

            {{-- 1. Patient Details Section 👤 --}}
            <div class="p-5 pb-4 border-bottom bg-white">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fas fa-user-circle h3 mb-0 text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-0">{{ __('messages.patient_details') }}</h5>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="small text-muted mb-1">{{ __('messages.patient_name') }}</div>
                        <div class="fw-bold text-dark">{{ $reservation->patient->name }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="small text-muted mb-1">{{ __('messages.visit_id') }}</div>
                        <div class="fw-bold text-dark">#{{ $reservation->id }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="small text-muted mb-1">{{ __('messages.gender') }}</div>
                        <div class="fw-bold text-dark">{{ __('messages.' . $reservation->patient->gender) }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="small text-muted mb-1">{{ __('messages.date') }}</div>
                        <div class="fw-bold text-dark">{{ $reservation->analysis_date }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="small text-muted mb-1">{{ __('messages.birth_date') }}</div>
                        <div class="fw-bold text-dark">{{ $reservation->patient->birth_date }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="small text-muted mb-1">{{ __('messages.phone_number') }}</div>
                        <div class="fw-bold text-dark">{{ $reservation->patient->phone }}</div>
                    </div>
                    @if($reservation->doctor)
                    <div class="col-md-6 col-sm-12">
                        <div class="small text-muted mb-1">{{ __('messages.referred_by') }}</div>
                        <div class="fw-bold text-primary"><i class="fas fa-user-md me-1"></i> {{ $reservation->doctor->name }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- 2. Eligibility Summary Section 📋 --}}
            @if(!empty($eligibilitySummary))
            <div class="p-5 py-4 border-bottom" style="background: #fffbeb;">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning bg-opacity-20 p-2 rounded-3 me-3">
                        <i class="fas fa-clipboard-check text-warning fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-0" style="color: #92400e;">{{ __('messages.eligibility_findings') }}</h5>
                </div>
                <div class="row">
                    <div class="col-12">
                        <ul class="mb-0 ps-3" style="color: #78350f; font-size: 15px; line-height: 1.8;">
                            @foreach($eligibilitySummary as $finding)
                                <li class="mb-2">{{ $finding }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- 3. Analysis Results Table 🧪 --}}
            <div class="p-5 py-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="fas fa-microscope text-success fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-success">{{ __('messages.analyses_results') }}</h5>
                </div>
                
                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 ps-4" style="font-size: 12px; color: #64748b;">{{ __('messages.analysis') }}</th>
                                <th class="py-3" style="font-size: 12px; color: #64748b;">{{ __('messages.result') }}</th>
                                <th class="py-3 text-center" style="font-size: 12px; color: #64748b; width: 150px;">{{ __('messages.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservation->reservationAnalyses as $ra)
                                <tr>
                                    <td class="py-3 ps-4">
                                        <div class="fw-bold text-dark">{{ $ra->analyse->name }}</div>
                                        <div class="text-muted small">{{ $ra->analyse->code }}</div>
                                    </td>
                                    <td class="py-3">
                                        @if($ra->result_value)
                                            <span class="fw-bold text-primary fs-5">{{ $ra->result_value }}</span>
                                            <span class="text-muted small ms-1">{{ $ra->analyse->unit }}</span>
                                        @else
                                            <span class="text-muted small italic opacity-50">-- {{ __('messages.waiting') }} --</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge {{ $ra->status == 'ready' ? 'bg-success' : 'bg-warning' }} p-2 px-3 rounded-pill" style="font-size: 11px;">
                                            {{ __('messages.' . $ra->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 4. Smart AI Insight Section 🤖 --}}
            <div id="ai-insight-card" class="m-5 mt-0 p-5 rounded-4 shadow-sm {{ $reservation->ai_insight ? '' : 'd-none' }}" style="background: linear-gradient(to right, #f8fafc, #f1f5f9); border-left: 5px solid #0f172a !important;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-dark p-2 rounded-circle me-3">
                            <i class="fas fa-robot text-info fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-primary">{{ __('messages.ai_clinical_insight_report') }}</h5>
                    </div>
                    <span class="badge bg-primary px-3 rounded-pill shadow-sm">{{ __('messages.ai_powered') }}</span>
                </div>
                <div id="ai-insight-content" class="text-dark bg-white p-4 rounded-3 border" style="font-size: 15px; line-height: 1.8; white-space: pre-wrap;">{{ $reservation->ai_insight }}</div>
                
                <div class="mt-4 p-3 bg-light rounded-3 border">
                    <div class="small text-muted" style="font-size: 12px;">
                        <i class="fas fa-info-circle me-1 text-primary"></i>
                        <strong>{{ __('messages.physician_notice') }}:</strong> 
                        {{ __('messages.ai_interpretation_disclaimer') }}
                    </div>
                </div>
            </div>

            {{-- 5. Footer Actions (PDF Downloads) --}}
            <div class="p-5 bg-light border-top d-flex flex-wrap justify-content-center gap-3">
                @php
                    $hasDbResults = $reservation->reservationAnalyses->contains(fn($ra) => !empty($ra->result_value));
                @endphp

                {{-- Eligibility Report (Available once diagnostic check is done) --}}
                <a href="{{ route('reservation.pdf', ['id' => $reservation->id, 'type' => 'confirmed', 'report_type' => 'eligibility']) }}" target="_blank" class="btn btn-outline-primary shadow-sm rounded-3 py-2 px-4 fw-bold">
                    <i class="fas fa-file-medical me-2"></i> {{ __('messages.eligibility_report') }}
                </a>

                {{-- Medical Results (Uploaded File or DB Results) --}}
                @if($reservation->result_file_path)
                    <a href="{{ route('doctor.download', $reservation->id) }}" target="_blank" class="btn-download-result shadow-sm">
                        <i class="fas fa-file-pdf"></i>
                        {{ __('messages.download_official_result_pdf') }}
                    </a>
                @elseif($hasDbResults)
                    <a href="{{ route('reservation.pdf', ['id' => $reservation->id, 'type' => 'confirmed', 'report_type' => 'results']) }}" target="_blank" class="btn-download-result shadow-sm" style="background: #2f855a;">
                        <i class="fas fa-microscope me-2"></i> {{ __('messages.medical_report_pdf') }}
                    </a>
                @else
                    <div class="text-center text-muted w-100">
                        <i class="fas fa-hourglass-half mb-2 d-block h3 opacity-50"></i>
                        <p class="mb-0">{{ __('messages.results_not_yet_validated') }}</p>
                    </div>
                @endif

                @if($hasDbResults)
                    <button id="generate-ai-btn" class="btn btn-dark shadow-sm rounded-3 py-2 px-4 fw-bold" style="background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%); border:none;">
                        <i class="fas fa-robot me-2 text-info"></i> {{ __('messages.generate_ai_insight') }}
                    </button>
                @endif
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function smartGoBack() {
            if (window.opener && !window.opener.closed) { window.close(); return; }
            if (window.history.length > 1) { window.history.back(); return; }
            window.location.href = "{{ route('doctor.dashboard') }}";
        }

        document.addEventListener('DOMContentLoaded', function() {
            const aiBtn = document.getElementById('generate-ai-btn');
            const aiCard = document.getElementById('ai-insight-card');
            const aiContent = document.getElementById('ai-insight-content');

            if (aiBtn) {
                aiBtn.addEventListener('click', function() {
                    aiBtn.disabled = true;
                    aiBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> {{ __("messages.generating") }}';
                    
                    fetch('{{ route("doctor.bookings.ai-insight", $reservation->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        aiBtn.disabled = false;
                        aiBtn.innerHTML = '<i class="fas fa-robot me-2 text-info"></i> {{ __("messages.generate_ai_insight") }}';
                        
                        if (data.success) {
                            aiCard.classList.remove('d-none');
                            aiContent.innerHTML = data.insight.replace(/\n/g, '<br>');
                            aiCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else {
                            alert(data.message || 'AI processing failed');
                        }
                    })
                    .catch(error => {
                        aiBtn.disabled = false;
                        aiBtn.innerHTML = '<i class="fas fa-robot me-2 text-info"></i> {{ __("messages.generate_ai_insight") }}';
                        alert('Connection error with AI service');
                    });
                });
            }
        });
    </script>
</html>
