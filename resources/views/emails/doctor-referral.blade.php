<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.new_referral') ?? 'Nouvelle Orientation' }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8fafc; }
        .container { background: white; padding: 0; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #1a365d, #2d3748); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 26px; }
        .content { padding: 40px 30px; }
        .welcome-msg { font-size: 18px; font-weight: bold; margin-bottom: 20px; color: #1a365d; }
        .info-grid { background: #f1f5f9; padding: 20px; border-radius: 12px; margin: 20px 0; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 8px 0; font-size: 14px; }
        .label { color: #64748b; width: 40%; }
        .value { font-weight: bold; color: #1e293b; }
        .notes { background: #fffbeb; border-right: 4px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 25px 0; }
        [dir="ltr"] .notes { border-right: none; border-left: 4px solid #f59e0b; }
        .cta-box { text-align: center; padding: 30px; background: #f8fafc; border-radius: 16px; margin: 30px 0; border: 1px dashed #cbd5e1; }
        .btn-portal { display: inline-block; background: #1a365d; color: white !important; padding: 14px 35px; border-radius: 12px; text-decoration: none; font-weight: bold; margin-top: 10px; }
        .footer { padding: 30px; text-align: center; font-size: 12px; color: #94a3b8; background: #f1f5f9; border-top: 1px solid #e2e8f0; }
        .results-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .results-table th { background: #f8fafc; color: #64748b; font-size: 12px; text-align: right; padding: 10px; border-bottom: 2px solid #e2e8f0; }
        [dir="ltr"] .results-table th { text-align: left; }
        .results-table td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .badge { padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .badge-ready { background: #c6f6d5; color: #22543d; }
        .badge-pending { background: #feebc8; color: #744210; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>labo.dz</h1>
            <p>{{ __('messages.physician_portal_notification') ?? 'Notification du Portail Médecin' }}</p>
        </div>
        
        <div class="content">
            <div class="welcome-msg">{{ __('messages.hello') ?? 'Bonjour' }}, Dr. {{ $doctor->name }}</div>
            <p>{{ __('messages.new_patient_referred_desc') ?? 'Un nouveau patient vous a été référé pour un suivi clinique.' }}</p>

            <div class="info-grid">
                <table class="info-table">
                    <tr>
                        <td class="label">{{ __('messages.patient_name') }}</td>
                        <td class="value">{{ $patient->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.gender') }}</td>
                        <td class="value">{{ __('messages.' . $patient->gender) }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('messages.visit_id') }}</td>
                        <td class="value">#{{ $reservation->id }}</td>
                    </tr>
                </table>
            </div>

            @if(!empty($eligibilitySummary))
            <div class="notes">
                <strong style="display: block; margin-bottom: 10px; color: #92400e; font-size: 16px;">
                    <i class="fas fa-clipboard-check"></i> {{ __('messages.eligibility_findings') ?? 'Résultats de l\'Éligibilité' }}
                </strong>
                <ul style="padding-right: 20px; margin: 0; color: #78350f; font-size: 14px;">
                    @foreach($eligibilitySummary as $finding)
                        <li style="margin-bottom: 5px;">{{ $finding }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <h3 style="font-size: 16px; margin-top: 30px; color: #1a365d;">{{ __('messages.analyses_results') ?? 'Résultats des Analyses' }}</h3>
            <table class="results-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.analysis') }}</th>
                        <th>{{ __('messages.result') }}</th>
                        <th>{{ __('messages.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservation->reservationAnalyses as $ra)
                    <tr>
                        <td class="value">{{ $ra->analyse->name }}</td>
                        <td style="font-family: monospace;">{{ $ra->result_value ?? '--' }} {{ $ra->analyse->unit ?? '' }}</td>
                        <td>
                            <span class="badge badge-{{ $ra->status == 'ready' ? 'ready' : 'pending' }}">
                                {{ __('messages.' . $ra->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="cta-box">
                <p style="margin-bottom: 15px; font-size: 14px;">{{ __('messages.view_detailed_report_desc') ?? 'Consultez le rapport complet, les PDF officiels et l\'analyse IA via votre portail.' }}</p>
                <a href="{{ route('access') }}" class="btn-portal">
                    {{ __('messages.access_doctor_portal') ?? 'Accéder au Portail Médecin' }}
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} LabNexa - {{ __('messages.all_rights_reserved') ?? 'Tous droits réservés' }}</p>
            <p>{{ __('messages.auto_generated_email') ?? 'Cet e-mail a été généré automatiquement.' }}</p>
        </div>
    </div>
</body>
</html>
