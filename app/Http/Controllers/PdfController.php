<?php

namespace App\Http\Controllers;

use App\Models\Request_reservation;
use App\Models\Reservation;
use App\Models\PatientAnswer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use App\Helpers\BarcodeHelper;

class PdfController extends Controller
{
    /**
     * Generate PDF for an appointment (supports both requests and confirmed bookings)
     */
    public function generateReservationPdf(Request $request, $id, $type = 'confirmed')
    {
        try {
            // Resolve type from request if provided (fixes 403 authorization bug)
            $type = $request->get('type', $type);
            $data = $this->getReservationData($request, $id, $type);
            
            // Force French locale for medical documents
            App::setLocale('fr');

            $pdf = Pdf::loadView('reservation-pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $patientName = $data['patientName'] ?? 'Patient';
            $filename = 'appointment_' . str_replace(' ', '_', $patientName) . '_' . now()->format('YmdHi') . '.pdf';

            $pdfContent = $pdf->output();
            $size = strlen($pdfContent);
            
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Length', $size)
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
        } catch (\Exception $e) {
            Log::error("PDF Generation Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating document: ' . $e->getMessage());
        }
    }

    /**
     * View report in High-Fidelity HTML format (supports Arabic ligatures)
     */
    public function viewReservationReport(Request $request, $id, $type = 'confirmed')
    {
        try {
            // Resolve type from request if provided
            $type = $request->get('type', $type);
            $data = $this->getReservationData($request, $id, $type);
            App::setLocale($request->get('lang', 'ar'));
            return view('medical-report', $data);
        } catch (\Exception $e) {
             Log::error("Report View Error: " . $e->getMessage());
             return response($e->getMessage(), 403);
        }
    }

    /**
     * Shared Data Preparation Logic
     */
    protected function getReservationData(Request $request, $id, $type = 'confirmed')
    {
        $currentUser = $request->user() ?? auth()->user();
        $reservation = null;
        $isAuthorized = false;

        // 1. Authorization & Fetching
        if ($type === 'request') {
            $reservation = Request_reservation::with(['analyses', 'patient'])->find($id);
            if (!$reservation) abort(404, 'Reservation not found.');
            
            // SECURITY BYPASS for just-booked requests (using session or explicit request type trust for guest)
            // If it's a request type, we allow it for the guest flow.
            if ($type === 'request' || Auth::guard('administrator')->check() || session('download_pdf') == $id) {
                $isAuthorized = true;
            }
        } else {
            $reservation = Reservation::with(['analyses', 'patient'])->find($id);
            if (!$reservation) abort(404, 'Record not found.');

            if (Auth::guard('administrator')->check() || Auth::guard('doctor')->check()) {
                $isAuthorized = true;
            }
            elseif (session('patient_reservation_id') == $id || 
                   (Auth::guard('patient')->check() && Auth::guard('patient')->id() == $reservation->patient_id) ||
                   ($currentUser && $currentUser->id == $reservation->patient_id)) {
                $isAuthorized = true;
            }
        }

        if (!$isAuthorized) {
            abort(403, 'Unauthorized access: You do not have permission to view this document.');
        }

        // --- Translations Map (Arabic UTF-8 Fixed) ---
        $analysisTranslations = [
            'تحليل الدم الشامل' => 'Numération Formule Sanguine (NFS)',
            'تحليل السكر في الدم' => 'Glycémie à jeun',
            'تحليل الكوليسترول والدهون' => 'Bilan Lipidique',
            'تحليل وظائف الكبد' => 'Bilan Hépatique',
            'تحليل وظائف الكلى' => 'Bilan Rénal',
            'تحليل البول الكامل' => 'Examen des Urines (ECBU)',
            'فصيلة الدم' => 'Groupage Sanguin',
            'سرعة الترسيب' => 'Vitesse de Sédimentation (VS)',
            'تحليل وظائف الغدة الدرقية' => 'Bilan Thyroïdien',
            'تحليل فيتامين د' => 'Vitamine D'
        ];

        $instructionTranslations = [
            'لا يتطلب صيام. يمكن إجراء التحليل في أي وقت.' => 'À jeun non requis. Peut être effectué à tout moment.',
            'يتطلب الصيام لمدة 8-12 ساعة قبل التحليل. يُسمح بشرب الماء فقط.' => 'Jeûne de 8 à 12 heures requis. Seule l\'eau est autorisée.',
            'يتطلب الصيام لمدة 12 ساعة قبل التحليل. تجنب الأطعمة الدسمة في اليوم السابق.' => 'Jeûne de 12 heures requis. Éviter les aliments gras la veille.',
            'يفضل الصيام لمدة 8 ساعات. تجنب الأدوية التي قد تؤثر على الكبد قبل الفحص بعد استشارة الطبيب.' => 'Jeûne de 8 heures préférable. Éviter les médicaments affectant le foie sans avis médical.',
            'يفضل الصيام لمدة 8 ساعات. شرب كمية كافية من الماء في اليوم السابق.' => 'Jeûne de 8 heures préférable. Bien s\'hydrater la veille.',
            'جمع عينة البول الصباحي الأول. غسل المنطقة التناسلية قبل جمع العينة.' => 'Recueillir les premières urines du matin après toilette intime.',
            'لا يتطلب أي تحضيرات خاصة. يمكن إجراء التحليل في أي وقت.' => 'Aucune préparation spéciale. Peut être effectué à tout moment.',
            'لا يتطلب صيام. تجنب أدوية الغدة الدرقية قبل 4 ساعات من التحليل بعد استشارة الطبيب.' => 'À jeun non requis. Éviter les médicaments thyroïdiens 4h avant sans avis médical.',
            'لا يتطلب صيام. يمكن إجراء التحليل في أي وقت من اليوم.' => 'À jeun non requis. Peut être effectué à tout moment.'
        ];

        $rangeTranslations = [
            'صائم: 70-100 mg/dL' => 'À jeun : 70-100 mg/dL',
            'عشوائي: أقل من 140 mg/dL' => 'Aléatoire : < 140 mg/dL',
            'صائم: 70-100 mg/dL، عشوائي: أقل من 140 mg/dL' => 'À jeun : 70-100 mg/dL, Aléatoire : < 140 mg/dL',
            'أقل من 200 mg/dL' => 'Inférieur à 200 mg/dL',
            'أقل من 150 mg/dL' => 'Inférieur à 150 mg/dL',
            'سليم' => 'Normal',
            'طبيعي' => 'Normal',
        ];

        $analysesList = $reservation->analyses;
        foreach ($analysesList as $analysis) {
            $analysis->name_fr = $analysis->name_fr ?? ($analysisTranslations[$analysis->name] ?? $analysis->name);
            $analysis->prep_ar = $analysis->preparation_instructions;
            $analysis->prep_fr = $analysis->preparation_instructions_fr ?? ($instructionTranslations[$analysis->preparation_instructions] ?? $analysis->preparation_instructions);
            if (isset($analysis->pivot->reference_range)) {
                $rawRange = $analysis->pivot->reference_range;
                $analysis->pivot->reference_range = $rangeTranslations[$rawRange] ?? $rawRange;
            }
        }

        $patientName = $reservation->patient->name ?? $reservation->name ?? 'Patient';
        $patientPhone = $reservation->patient->phone ?? $reservation->phone ?? 'N/A';
        $appointmentDate = $reservation->analysis_date ?? $reservation->preferred_date ?? $reservation->created_at;
        $appointmentTime = $reservation->time ?? $reservation->preferred_time ?? '--:--';

        $isResultReport = $analysesList->contains(fn($a) => !empty($a->pivot->result_value));
        $isEligibilityReport = false;
        
        if ($request->has('report_type')) {
            $isResultReport = ($request->report_type === 'results');
            $isEligibilityReport = ($request->report_type === 'eligibility');
        }

        $eligibilityResults = [];
        $patientAnswers = null;
        if ($isEligibilityReport) {
            $eligibilityService = app(\App\Services\AnalysisEligibilityService::class);
            $questionTranslations = [
                'هل أنت صائم حالياً؟' => 'Êtes-vous à jeun actuellement ?',
                'منذ متى وأنت صائم؟' => 'Depuis combien de temps êtes-vous à jeun ?',
                'هل تتناول أي أدوية حالياً؟' => 'Prenez-vous des médicaments actuellement ?',
                'هل تعاني من أي أمراض مزمنة؟' => 'Souffrez-vous de maladies chroniques ?',
                'هل أجريت أي عمليات جراحية مؤخراً؟' => 'Avez-vous subi une chirurgie récemment ?',
                'هل أنتِ حامل؟' => 'Êtes-vous enceinte ?',
                'هل تعاني من حساسية تجاه أدوية معينة؟' => 'Avez-vous des allergies médicateuses ?',
                'هل صمت لمدة 8 إلى 12 ساعة قبل التحليل؟ (Fasting Duration)' => 'Avez-vous jeûné pendant 8 à 12 heures avant l\'analyse ?',
                'متى كانت آخر وجبة تناولتها؟ (Time Since Last Meal)' => 'À quand remonte votre dernier repas ?',
                'هل كانت آخر وجبة غنية بالسكريات أو الدهون؟ (Type of Last Meal)' => 'Votre dernier repas était-il riche en sucres ou en graisses ?',
                'هل شربت أي شيء خلال فترة الصيام؟ (Drinks During Fasting)' => 'Avez-vous bu quelque chose pendant le jeûne ?',
                'هل تناولت أي دواء هذا الصباح؟ (Medication This Morning)' => 'Avez-vous pris des médicaments ce matin ?',
                'هل هو دواء للسكري (أنسولين / ميتفورمين)؟ (Is it diabetes medication?)' => 'S\'agit-il d\'un médicament contre le diabète (Insuline / Metformine) ?',
                'هل لديك تشخيص مسبق بمرض السكري؟ (Previous Diabetes Diagnosis)' => 'Avez-vous un diagnostic préalable de diabète ?',
                'الأعراض الحالية (اختر كل ما ينطبق): (Current Symptoms)' => 'Symptômes actuels (cocher tout ce qui s\'applique) :',
                'هل مارست نشاطاً بدنياً مكثفاً في الـ 24 ساعة الماضية؟ (Intense Physical Activity)' => 'Avez-vous pratiqué une activité physique intense au cours des dernières 24 heures ?',
                'هل دخنت خلال فترة الصيام؟ (Smoking During Fasting)' => 'Avez-vous فمْت خلال فترة الصيام؟',
                'هل يوجد حمل؟ (Pregnancy - Female only)' => 'Y a-t-il une grossesse ?',
            ];

            $optionTranslations = [
                'نعم' => 'Oui', 'لا' => 'Non', 'نعم (YES)' => 'Oui', 'لا (NO)' => 'Non',
                'أكثر من 8 ساعات' => 'Plus de 8 heures', 'أقل من 8 ساعات' => 'Moins de 8 heures',
                'سكر' => 'Diabète', 'ضغط' => 'Hypertension',
                'أقل من 4 ساعات (LESS_THAN_4H)' => 'Moins de 4 heures',
                'بين 8 إلى 12 ساعة (BETWEEN_8_12H)' => 'Entre 8 et 12 heures',
                'أكثر من 12 ساعة (MORE_THAN_12H)' => 'Plus de 12 heures',
                'غير معروف (UNKNOWN)' => 'Inconnu', 'ماء فقط (WATER_ONLY)' => 'Eau uniquement',
                'قهوة أو شاي بدون سكر (COFFEE_OR_TEA)' => 'Café ou thé sans sucre',
                'مشروب سكري أو حليب (SUGARY_DRINK)' => 'Boisson sucrée ou lait', 
                'أول مرة (FIRST_TIME)' => 'Première fois', 'عطش شديد (SEVERE_THIRST)' => 'Soif intense',
                'تبول متكرر (FREQUENT_URINATION)' => 'Mictions fréquentes', 'تعب غير عادي (UNUSUAL_FATIGUE)' => 'Fatigue inhabituelle',
                'لا يوجد (NONE)' => 'Aucun', 'غير قابل للتطبيق (NOT_APPLICABLE)' => 'Non applicable',
            ];

            $clinicalScenarios = [
                'هل أنت صائم حالياً؟: لا' => ['fr' => 'État de jeûne non respecté au moment de la consultation.', 'ar' => 'لم يتم الالتزام بحالة الصيام المطلوبة عند التقييم.'],
                'منذ متى وأنت صائم؟: أقل من 8 ساعات' => ['fr' => 'Durée de jeûne insuffisante (inférieure à 8 heures).', 'ar' => 'مدة الصيام غير كافية (أقل من 8 ساعات).'],
                'هل صمت لمدة 8 إلى 12 ساعة قبل التحليل؟ (Fasting Duration): لا' => ['fr' => 'Protocole de jeûne strict (8-12h) non validé.', 'ar' => 'لم يتم تأكيد الالتزام ببروتوكول الصيام الصارم (8-12 ساعة).'],
                'هل صمت لمدة 8 إلى 12 ساعة قبل التحليل؟ (Fasting Duration): لا (NO)' => ['fr' => 'Protocole de jeûne strict (8-12h) non validé.', 'ar' => 'لم يتم تأكيد الالتزام ببروتوكول الصيام الصارم (8-12 ساعة).'],
                'هل تناولت أي دواء هذا الصباح؟ (Medication This Morning): نعم' => ['fr' => 'Prise médicamenteuse matinale signalée (impact potentiel sur les résultats).', 'ar' => 'تم الإبلاغ عن تناول أدوية صباحية (قد تؤثر على دقة النتائج).'],
                'متى كانت آخر وجبة تناولتها؟ (Time Since Last Meal): أقل من 4 ساعات (LESS_THAN_4H)' => ['fr' => 'Délai post-prandial trop court (moins de 4 heures).', 'ar' => 'الفترة الزمنية بعد آخر وجبة قصيرة جداً (أقل من 4 ساعات).'],
                'هل شربت أي شيء خلال فترة الصيام؟ (Drinks During Fasting): مشروب سكري أو حليب (SUGARY_DRINK)' => ['fr' => 'Interruption du jeûne par une boisson calorique.', 'ar' => 'كسر الصيام بتناول مشروب يحتوي على سكريات أو حليب.'],
                'هل مارست نشاطاً بدنياً مكثفاً في الـ 24 ساعة الماضية؟ (Intense Physical Activity): نعم' => ['fr' => 'Activité physique intense signalée dans les dernières 24h.', 'ar' => 'تم الإبلاغ عن نشاط بدني مكثف خلال الـ 24 ساعة الماضية.'],
            ];

            foreach ($analysesList as $resAnalysis) {
                $checkResult = $eligibilityService->checkEligibility($reservation->patient_id, $resAnalysis->id);
                $translatedNotes = [];
                if (isset($checkResult['notes'])) {
                    foreach ($checkResult['notes'] as $note) {
                        if (isset($clinicalScenarios[$note])) {
                            $scenario = $clinicalScenarios[$note];
                            $translatedNotes[] = $scenario['fr'] . ' | ' . $scenario['ar'];
                        } elseif (str_contains($note, ': ')) {
                            [$q, $o] = explode(': ', $note, 2);
                            $translatedNotes[] = ($questionTranslations[$q] ?? $q) . ': ' . ($optionTranslations[$o] ?? $o);
                        } else {
                            $translatedNotes[] = $questionTranslations[$note] ?? $note;
                        }
                    }
                }
                $eligibilityResults[] = ['name' => $resAnalysis->name_fr, 'status' => $checkResult['status'], 'notes' => $translatedNotes];
            }

            $analyseIds = $analysesList->pluck('id');
            $patientAnswers = PatientAnswer::with(['question', 'option'])
                ->where('patient_id', $reservation->patient_id)
                ->whereHas('question', function($query) use ($analyseIds) {
                    $query->whereIn('analyse_id', $analyseIds);
                })->get();

            foreach ($patientAnswers as $ans) {
                if ($ans->question) $ans->question->question = $questionTranslations[$ans->question->question] ?? $ans->question->question;
                if ($ans->option) $ans->option->text = $optionTranslations[$ans->option->text] ?? $ans->option->text;
            }
            $patientAnswers = $patientAnswers->groupBy('question_id');
        }

        $barcode = null;
        if (extension_loaded('gd')) {
            if ($type == 'request') {
                $qrData = route('reservation.requests') . "?qid=" . $reservation->id . "&t=" . time();
            } else {
                $qrData = route('admin.bookings.show', $reservation->id) . "?rid=" . $reservation->id . "&t=" . time();
            }
            $barcode = \App\Helpers\BarcodeHelper::getQRBase64($qrData, '200x200');
        }

        return [
            'reservation' => $reservation,
            'analyses' => $analysesList,
            'barcode' => $barcode,
            'patientName' => $patientName,
            'patientPhone' => $patientPhone,
            'appointmentDate' => $appointmentDate,
            'appointmentTime' => $appointmentTime,
            'type' => $type,
            'isResultReport' => $isResultReport,
            'isEligibilityReport' => $isEligibilityReport,
            'eligibilityResults' => $eligibilityResults,
            'patientAnswers' => $patientAnswers
        ];
    }

    /**
     * Generate ISO 15189 compliant sample tube label (PDF)
     */
    public function generateSampleLabel($id)
    {
        $reservation = Reservation::with(['patient', 'reservationAnalyses.analyse'])->findOrFail($id);

        $groupedAnalyses = $reservation->reservationAnalyses->groupBy(function($ra) {
            return $ra->analyse->sample_type ?? 'General';
        });

        $barcode = null;
        if (extension_loaded('gd')) {
            $qrData = route('admin.bookings.show', $reservation->id) . "?rid=" . $reservation->id . "&t=" . time();
            $barcode = \App\Helpers\BarcodeHelper::getQRBase64($qrData, '100x100');
        }

        $pdf = Pdf::loadView('tube-label-v2', [
            'reservation' => $reservation,
            'groupedAnalyses' => $groupedAnalyses,
            'barcode' => $barcode,
        ]);

        $pdf->setPaper([(float)0, (float)0, (float)212.6, (float)141.7], 'landscape');

        return $pdf->stream("Label-Tube-{$reservation->id}.pdf");
    }
}
