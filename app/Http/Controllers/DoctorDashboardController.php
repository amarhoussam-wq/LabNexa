<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\PatientAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DoctorDashboardController extends Controller
{
    /**
     * Display the doctor dashboard with patient summaries.
     */
    public function dashboard()
    {
        $doctor = Auth::guard('doctor')->user();

        // Get reservations linked to this doctor
        $query = Reservation::with(['patient', 'reservationAnalyses.analyse'])
            ->where('doctor_id', $doctor->id);

        $stats = [
            'total_patients' => (clone $query)->distinct('patient_id')->count(),
            'pending_results' => (clone $query)->where(function($q) {
                $q->whereNotIn('status', ['ready', 'completed'])
                  ->whereNull('result_file_path')
                  ->whereDoesntHave('reservationAnalyses', function($sq) {
                      $sq->whereNotNull('result_value');
                  });
            })->count(),
            'ready_results' => (clone $query)->where(function($q) {
                $q->whereIn('status', ['ready', 'completed'])
                  ->orWhereNotNull('result_file_path')
                  ->orWhereHas('reservationAnalyses', function($sq) {
                      $sq->whereNotNull('result_value');
                  });
            })->count(),
        ];

        $reservations = $query->orderBy('analysis_date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(15);

        return view('doctor.dashboard', compact('stats', 'reservations', 'doctor'));
    }

    /**
     * View detailed results for a specific patient referal.
     */
    public function showPatientResult($id)
    {
        $doctor = Auth::guard('doctor')->user();
        
        $reservation = Reservation::with(['patient', 'reservationAnalyses.analyse'])
            ->where('doctor_id', $doctor->id)
            ->findOrFail($id);

        // Fetch eligibility findings for the view
        $eligibilitySummary = [];
        $eligibilityService = app(\App\Services\AnalysisEligibilityService::class);
        
        foreach ($reservation->reservationAnalyses as $ra) {
            $res = $eligibilityService->checkEligibility($reservation->patient_id, $ra->analyse_id);
            if (!empty($res['notes'])) {
                // Merge unique notes
                $eligibilitySummary = array_values(array_unique(array_merge($eligibilitySummary, $res['notes'])));
            }
        }

        return view('doctor.patient-result', compact('reservation', 'eligibilitySummary'));
    }

    /**
     * Download result file for doctor.
     */
    public function downloadResult($id)
    {
        $doctor = Auth::guard('doctor')->user();
        
        $reservation = Reservation::where('doctor_id', $doctor->id)
            ->findOrFail($id);

        if (!$reservation->result_file_path || !Storage::disk('public')->exists($reservation->result_file_path)) {
            return back()->with('error', __('messages.result_not_found'));
        }

        return Storage::disk('public')->response($reservation->result_file_path);
    }

    /**
     * View detailed eligibility report for a specific patient (HTML).
     */
    public function showEligibilityReport($id)
    {
        $doctor = Auth::guard('doctor')->user();
        $reservation = Reservation::with(['patient', 'reservationAnalyses.analyse'])
            ->where('doctor_id', $doctor->id)
            ->findOrFail($id);

        // --- Translation Maps (Synced with Admin Controller) ---
        $analysisTranslations = [
            'تحليل الدم الشامل' => 'Numération Formule Sanguine (NFS)',
            'تحليل السكر في الدم' => 'Glycémie à jeun',
            'تحليل الكوليسترول والدهون' => 'Bilan Lipidique',
            'تحليل وظائف الكبد' => 'Bilan Hépatique',
            'تحليل وظائف الكلى' => 'Bilan Rénal',
            'تحليل البول الكامل' => 'Examen Cytobactériologique des Urines (ECBU)',
            'فصيلة الدم' => 'Groupage Sanguin',
            'سرعة الترسيب' => 'Vitesse de Sédimentation (VS)',
            'تحليل وظائف الغدة الدرقية' => 'Bilan Thyroïdien',
            'تحليل فيتامين د' => 'Vitamine D',
        ];

        $questionTranslations = [
            'هل أنت صائم حالياً؟' => 'Êtes-vous à jeun actuellement ?',
            'منذ متى وأنت صائم؟' => 'Depuis combien de temps êtes-vous à jeun ?',
            'هل تتناول أي أدوية حالياً؟' => 'Prenez-vous des médicaments actuellement ?',
            'هل تعاني من أي أمراض مزمنة؟' => 'Souffrez-vous de maladies chroniques ?',
            'هل أجريت أي عمليات جراحية مؤخراً؟' => 'Avez-vous subi une chirurgie récemment ?',
            'هل أنتِ حامل؟' => 'Êtes-vous enceinte ?',
            'هل تعاني من حساسية تجاه أدوية معينة؟' => 'Avez-vous des allergies médicateuses ?',
            'في أي وعاء جمعت عينة البول؟' => 'Dans quel récipient avez-vous recueilli l\'échantillon d\'urine ?',
            'هل قمت بتنظيف المنطقة التناسلية قبل جمع البول؟' => 'Avez-vous nettoyé la zone génitale avant le recueil ?',
            'كيف جمعت البول؟' => 'Comment avez-vous recueilli l\'urine ?',
            'متى جمعت العينة؟' => 'Quand avez-vous recueilli l\'échantillon ?',
            'كيف حفظت العينة قبل إحضارها؟' => 'Comment avez-vous conservé l\'échantillon avant de l\'apporter ?',
            'هل تتناول مضادات حيوية حاليًا؟' => 'Prenez-vous des antibiotiques actuellement ?',
            'هل أنتِ في فترة الحيص؟' => 'Êtes-vous en période de menstruation ?',
        ];

        $optionTranslations = [
            'نعم' => 'Oui',
            'لا' => 'Non',
            'أكثر من 8 ساعات' => 'Plus de 8 heures',
            'أقل من 8 ساعات' => 'Moins de 8 heures',
            'سكر' => 'Diabète',
            'ضغط' => 'Hypertension',
            'قارورة معقمة من المخبر أو الصيدلية' => 'Flacon stérile du laboratoire ou de la pharmacie',
            'قارورة ماء عادية' => 'Bouteille d\'eau ordinaire',
            'كوب منزلي' => 'Gobelet domestique',
            'نعم بالماء والصابون' => 'Oui, avec de l\'eau et du savon',
            'نعم بالماء فقط' => 'Oui, avec de l\'eau uniquement',
            'من بداية التبول' => 'Début de miction',
            'من منتصف التبول' => 'Milieu de miction',
            'من نهاية التبول' => 'Fin de miction',
            'منذ أقل من ساعتين' => 'Moins de 2 heures',
            'منذ 3–4 ساعات' => 'Il y a 3–4 heures',
            'منذ أكثر من 6 ساعات' => 'Plus de 6 heures',
            'في الثلاجة' => 'Au réfrigérateur',
            'في درجة حرارة الغرفة' => 'À température ambiante',
            'في السيارة' => 'Dans la voiture',
            'لا أعلم' => 'Je ne sais pas',
        ];

        $results = [];
        $eligibilityService = app(\App\Services\AnalysisEligibilityService::class);
        
        foreach ($reservation->reservationAnalyses as $resAnalysis) {
            $checkResult = $eligibilityService->checkEligibility($reservation->patient_id, $resAnalysis->analysis_id);
            
            $translatedNotes = [];
            if (isset($checkResult['notes'])) {
                foreach ($checkResult['notes'] as $note) {
                    if (str_contains($note, ': ')) {
                        [$q, $o] = explode(': ', $note, 2);
                        $translatedNotes[] = ($questionTranslations[$q] ?? $q) . ': ' . ($optionTranslations[$o] ?? $o);
                    } else {
                        $translatedNotes[] = $questionTranslations[$note] ?? $note;
                    }
                }
            }

            $results[] = [
                'name' => $resAnalysis->analyse ? ($analysisTranslations[$resAnalysis->analyse->name] ?? $resAnalysis->analyse->name) : "Analysis #{$resAnalysis->analysis_id}",
                'status' => $checkResult['status'],
                'notes' => $translatedNotes
            ];
        }

        $analyseIds = $reservation->reservationAnalyses->pluck('analysis_id');
        $patientAnswers = PatientAnswer::with(['question', 'option'])
            ->where('patient_id', $reservation->patient_id)
            ->whereHas('question', function($query) use ($analyseIds) {
                $query->whereIn('analyse_id', $analyseIds);
            })
            ->get();

        /** @var PatientAnswer $ans */
        foreach ($patientAnswers as $ans) {
            if ($ans->question) {
                $ans->question->question = $questionTranslations[$ans->question->question] ?? $ans->question->question;
            }
            if ($ans->option) {
                $ans->option->text = $optionTranslations[$ans->option->text] ?? $ans->option->text;
            }
        }

        $patientAnswers = $patientAnswers->groupBy('question_id');

        // Professional QR Code
        $patientName = $reservation->patient->name ?? 'Patient';
        $qrData = "VIST-" . $reservation->id . " | Patient: " . $patientName;
        $barcode = \App\Helpers\BarcodeHelper::getQRBase64($qrData, '200x200');

        return view('doctor.eligibility-report', compact('reservation', 'results', 'patientAnswers', 'barcode', 'doctor'));
    }
}
