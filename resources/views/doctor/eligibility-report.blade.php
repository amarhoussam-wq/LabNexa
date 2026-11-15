<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.eligibility_report') }} - {{ __('messages.lab_name') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --doctor-primary: #0f172a;
            --doctor-secondary: #3b82f6;
            --bg-light: #f8fafc;
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal', sans-serif" : "'Outfit', sans-serif" }};
            background-color: var(--bg-light);
            padding: 40px 0;
            color: #334155;
        }

        .report-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .btn-back {
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
            transition: color 0.2s;
        }

        .btn-back:hover { color: var(--doctor-secondary); }

        .card-custom {
            background: white;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-header-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 30px 40px;
        }

        .patient-banner {
            background: #e0f2fe;
            padding: 25px 40px;
            border-bottom: 1px solid #bae6fd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-pill {
            background: rgba(255,255,255,0.7);
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .section-title {
            font-size: 18px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-eligible { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-block { background: #fee2e2; color: #991b1b; }

        .analysis-card {
            border: 1px solid #f1f5f9;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }

        .analysis-card:hover { transform: translateX(5px); border-color: #cbd5e1; }

        .question-row {
            padding: 18px 25px;
            border-bottom: 1px solid #f1f5f9;
        }

        .question-text {
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }

        .answer-badge {
            background: #f1f5f9;
            color: #1e293b;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            border: 1px solid #e2e8f0;
        }

        .qr-placeholder {
            background: white;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

    <div class="report-container container">
        
        <div class="d-flex justify-content-between align-items-center mb-2">            <a onclick="smartGoBack(); return false;" href="{{ route('doctor.dashboard') }}" class="btn-back">
                <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i> {{ __('messages.back_to_dashboard') }}
            </a>
            
            <div class="d-flex gap-2">
                <a href="{{ route('reservation.pdf', ['id' => $reservation->id, 'type' => 'confirmed', 'report_type' => 'eligibility']) }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">
                    <i class="fas fa-file-pdf me-1"></i> PDF Version
                </a>
            </div>
        </div>

        <div class="card-custom">
            <!-- Header -->
            <div class="card-header-gradient">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-extrabold mb-1"><i class="fas fa-clipboard-check me-2 text-info"></i> {{ __('messages.medical_eligibility_report') }}</h2>
                        <p class="mb-0 opacity-75 fw-medium">{{ __('messages.lab_name') }} - Patient Eligibility Diagnostic</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="d-inline-block qr-placeholder">
                            <img src="{{ $barcode }}" width="80" alt="QR Code">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Info Banner -->
            <div class="patient-banner">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">{{ $reservation->patient->name }}</h3>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <span class="info-pill"><i class="fas fa-id-badge me-1"></i> #{{ $reservation->id }}</span>
                        <span class="info-pill"><i class="fas fa-venus-mars me-1"></i> {{ __('messages.' . $reservation->patient->gender) }}</span>
                        <span class="info-pill"><i class="fas fa-calendar-day me-1"></i> {{ $reservation->analysis_date }}</span>
                    </div>
                </div>
                <div class="text-end d-none d-md-block">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Doctor Assigned</div>
                    <div class="fw-bold text-primary"><i class="fas fa-user-md me-1"></i> {{ $doctor->name }}</div>
                </div>
            </div>

            <div class="row g-0">
                <!-- Left Column: Status Summary -->
                <div class="col-lg-5 border-end p-5 bg-white">
                    <div class="section-title">
                        <i class="fas fa-microscope text-primary"></i> {{ __('messages.analyses_eligibility') ?? 'Eligibility per Analysis' }}
                    </div>

                    @foreach($results as $res)
                        <div class="analysis-card bg-light border-0 shadow-none">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="fw-bold text-dark">{{ $res['name'] }}</div>
                                <span class="status-badge badge-{{ $res['status'] }}">
                                    {{ $res['status'] == 'eligible' ? (__('messages.qualified') ?? 'Eligible') : ($res['status'] == 'block' ? (__('messages.rejected_status') ?? 'Block') : (__('messages.medical_warning') ?? 'Warning')) }}
                                </span>
                            </div>

                            @if(count($res['notes']) > 0)
                                <div class="bg-white rounded-3 p-3 border shadow-sm">
                                    <div class="small fw-extrabold text-muted mb-2 text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">Assessment Findings:</div>
                                    <ul class="mb-0 ps-3 small" style="line-height: 1.6;">
                                        @foreach($res['notes'] as $note)
                                            <li class="mb-1 text-dark">{{ $note }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="text-success small fw-bold">
                                    <i class="fas fa-check-circle me-1"></i> {{ __('messages.met_all_protocol_requirements') }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Right Column: Answers -->
                <div class="col-lg-7 p-5 bg-white">
                    <div class="section-title">
                        <i class="fas fa-list-ul text-primary"></i> {{ __('messages.detailed_patient_answers') }}
                    </div>

                    <div class="rounded-4 overflow-hidden border">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3" style="font-size: 12px; font-weight: 700; color: #64748b;">QUESTION / PARAMETER</th>
                                    <th class="py-3" style="font-size: 12px; font-weight: 700; color: #64748b;">ANSWER</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($patientAnswers) > 0)
                                    @foreach($patientAnswers as $qId => $answers)
                                        @php $question = $answers->first()->question; @endphp
                                        <tr>
                                            <td class="ps-4 py-4">
                                                <div class="question-text">{{ $question->question }}</div>
                                                <div class="small opacity-50">{{ $question->analyse ? $question->analyse->name : 'General Protocol' }}</div>
                                            </td>
                                            <td class="py-4">
                                                @foreach($answers as $ans)
                                                    <span class="answer-badge d-inline-block">{{ $ans->option->text }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="2" class="text-center py-5 opacity-50 italic">No questionnaire data available for this visit.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5 p-4 rounded-4 bg-light border-start border-4 border-info">
                        <h6 class="fw-bold mb-2"><i class="fas fa-info-circle text-info me-2"></i> Physician's Guidance</h6>
                        <p class="small text-muted mb-0">
                            These status indicators are generated based on the lab's pre-analytical protocols. 
                            Rejections (Block) require sample collection rescheduling, while Warnings suggest clinical review 
                            of results with potential analytical interference notes.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-light p-4 text-center border-top">
                <p class="small text-muted mb-0">labo.dz Medical Diagnostics Protocol v2.1 • Generated on {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

</body>
<script>
    function smartGoBack() {
        if (window.opener && !window.opener.closed) { window.close(); return; }
        if (window.history.length > 1) { window.history.back(); return; }
        window.location.href = "{{ route('doctor.dashboard') }}";
    }
</script>
</html>
