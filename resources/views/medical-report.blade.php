<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Report - {{ $patientName }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c5282;
            --secondary-color: #edf2f7;
            --text-color: #2d3748;
            --border-color: #e2e8f0;
            --success-color: #38a169;
            --danger-color: #e53e3e;
            --accent-color: #3182ce;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        body {
            font-family: 'Inter', 'Cairo', sans-serif;
            background-color: #f7fafc;
            color: var(--text-color);
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        /* Container for A4 feel on screen */
        .report-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }

        /* Toolbar */
        .toolbar {
            background: var(--primary-color);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .btn-print {
            background: white;
            color: var(--primary-color);
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s;
        }

        .btn-print:hover {
            transform: scale(1.05);
        }

        /* Report Content */
        .report-content {
            padding: 40px;
        }

        /* Header Layout */
        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .lab-brand h1 {
            color: var(--primary-color);
            margin: 0;
            font-size: 24px;
            font-weight: 800;
        }

        .lab-brand p {
            margin: 5px 0 0;
            color: #718096;
            font-size: 14px;
        }

        .qr-code img {
            width: 100px;
            height: 100px;
            border: 1px solid var(--border-color);
            padding: 5px;
            border-radius: 4px;
        }

        /* Info Section */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            background: var(--secondary-color);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .info-item label {
            display: block;
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .info-item span {
            font-weight: 600;
            font-size: 16px;
        }

        /* Result Sections */
        .section-title {
            background: #f1f5f9;
            padding: 10px 15px;
            border-left: 5px solid var(--primary-color);
            margin: 30px 0 15px;
            font-weight: 700;
            font-size: 18px;
        }
        [dir="rtl"] .section-title {
            border-left: none;
            border-right: 5px solid var(--primary-color);
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #f8fafc;
            text-align: left;
            padding: 12px;
            font-size: 13px;
            color: #64748b;
            border-bottom: 2px solid var(--border-color);
        }
        [dir="rtl"] th {
            text-align: right;
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-eligible { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        .clinical-note {
            margin-top: 5px;
            font-size: 13px;
            color: #4a5568;
            font-style: italic;
        }

        footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
        }

        /* Print Specifics */
        @media print {
            body { background: white; padding: 0 !important; margin: 0 !important; }
            .report-wrapper { box-shadow: none; max-width: 100%; border-radius: 0; }
            .toolbar { display: none; }
            .report-content { padding: 20px !important; }
            header { margin-bottom: 20px; }
            .info-grid { padding: 15px; margin-bottom: 20px; }
            footer { position: fixed; bottom: 10px; width: 100%; }
        }
    </style>
</head>
<body>

    <div class="report-wrapper">
        <div class="toolbar">
            <div class="title">
                <i class="fas fa-file-contract"></i> 
                {{ $isEligibilityReport ? __('Evaluation de l\'Eligibilité') : __('Rapport d\'Analyses Médicales') }}
            </div>
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i> 
                {{ __('Imprimer le Rapport') }}
            </button>
        </div>

        <div class="report-content">
            <header>
                <div class="lab-brand">
                    <h1>LAB NEXA</h1>
                    <p>Système de Diagnostic Médical Avancé</p>
                    <p><small>{{ __('Date d\'édition') }}: {{ now()->format('d/m/Y H:i') }}</small></p>
                </div>
                <div class="qr-code">
                    @if($barcode)
                        <img src="{{ $barcode }}" alt="QR Code">
                    @endif
                </div>
            </header>

            <div class="info-grid">
                <div class="info-item">
                    <label>{{ __('Patient') }}</label>
                    <span style="font-family: 'Cairo', sans-serif;">{{ $patientName }}</span>
                </div>
                <div class="info-item">
                    <label>{{ __('Numéro de Dossier') }}</label>
                    <span>#{{ $reservation->id }}</span>
                </div>
                <div class="info-item">
                    <label>{{ __('Date d\'Analyse') }}</label>
                    <span>{{ \Carbon\Carbon::parse($appointmentDate)->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <label>{{ __('Heure') }}</label>
                    <span>{{ $appointmentTime }}</span>
                </div>
            </div>

            @if($isEligibilityReport)
                <div class="section-title">{{ __('Évaluation de l\'Éligibilité (Notes d\'Évaluation)') }}</div>
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('Analyse') }}</th>
                            <th>{{ __('Statut') }}</th>
                            <th>{{ __('Observations Médicales') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($eligibilityResults as $res)
                            <tr>
                                <td><strong>{{ $res['name'] }}</strong></td>
                                <td>
                                    <span class="status-badge {{ $res['status'] == 'eligible' ? 'status-eligible' : 'status-rejected' }}">
                                        {{ $res['status'] == 'eligible' ? 'ÉLIGIBLE' : 'REJETÉ' }}
                                    </span>
                                </td>
                                <td>
                                    @foreach($res['notes'] as $note)
                                        <div class="clinical-note" style="font-family: 'Cairo', sans-serif;">{{ $note }}</div>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if($isResultReport)
                <div class="section-title">{{ __('Résultats des Analyses') }}</div>
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('Test') }}</th>
                            <th>{{ __('Valeur') }}</th>
                            <th>{{ __('Unité') }}</th>
                            <th>{{ __('Intervalle de Référence') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($analyses as $analyse)
                            <tr>
                                <td><strong>{{ $analyse->name_fr }}</strong></td>
                                <td><span style="font-weight: 700; color: var(--accent-color);">{{ $analyse->pivot->result_value ?? '--' }}</span></td>
                                <td>{{ $analyse->pivot->result_unit ?? 'mg/dL' }}</td>
                                <td><small>{{ $analyse->pivot->reference_range ?? '--' }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if(!$isResultReport && !$isEligibilityReport)
                <div class="section-title">{{ __('Détails de la Réservation') }}</div>
                <ul style="list-style: none; padding: 0;">
                    @foreach($analyses as $analyse)
                        <li style="margin-bottom: 20px; padding: 15px; background: #f8fafc; border-radius: 6px;">
                            <strong>{{ $analyse->name_fr }}</strong><br>
                            <p class="clinical-note" style="margin-top: 10px;">
                                <i class="fas fa-info-circle"></i> {{ $analyse->prep_fr }}<br>
                                <span style="font-family: 'Cairo', sans-serif; display: block; margin-top: 5px;">{{ $analyse->prep_ar }}</span>
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif

            <footer>
                <p>Ce document est généré électroniquement par LAB NEXA Portal.</p>
                <p>Validité vérifiable via le code QR ci-dessus.</p>
            </footer>
        </div>
    </div>

</body>
</html>
