<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Option;
use App\Models\Patient;
use App\Models\PatientAnswer;
use App\Models\Question;
use App\Models\Reminder;
use App\Models\Request_reservation;
use App\Models\Reservation;
use App\Models\Doctor;
use App\Helpers\BarcodeHelper;
use Illuminate\Support\Facades\Log;
use App\Models\ReservationAnalysis;
use App\Services\AnalysisEligibilityService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ReservationsController extends Controller
{
    public function reservations(Request $request)
    {
        // Start the query, eager loading patient and analyses
        $query = Reservation::with(['patient', 'reservationAnalyses.analyse']);

        // Apply date filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('analysis_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('analysis_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('analysis_date', '<=', $request->end_date);
        }

        // Filter by booking status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by first letter of patient name
        if ($request->filled('letter')) {
            $letter = $request->letter;
            $query->whereHas('patient', function ($q) use ($letter) {
                if ($letter === '#') {
                    // Filter for names starting with non-alphabetical characters
                    $q->where('name', 'regexp', '^[^a-zA-Z]');
                } else {
                    $q->where('name', 'like', "$letter%");
                }
            });
        }

        // Search for patient by name or phone
        if ($request->filled('search')) {
            $search = $request->search;
            // If search is a pure number and relatively short, it's likely an ID
            if (is_numeric($search) && strlen($search) < 10) {
                // Exact match by Reservation ID OR cross-reference Request ID
                $query->where(function ($q) use ($search) {
                    $q->where('id', $search)
                      ->orWhereHas('patient.reservationRequests', function ($sub) use ($search) {
                          $sub->where('id', $search);
                      });
                });
            } else {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%");
                });
            }
        }
        // Specific ID filters (Disambiguated rid vs qid)
        if ($request->filled('rid')) {
            Log::info("Filtering RESERVATIONS by rid: " . $request->rid);
            $query->where('id', $request->rid);
        }
        
        if ($request->filled('id')) { // Generic fallback
            $query->where('id', $request->id);
        }

        // Apply dynamic sorting
        $sortBy = $request->get('sort_by', 'analysis_date');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Define allowable sort fields
        $allowedSorts = [
            'name' => 'patient.name', // Will be handled differently for joins
            'analysis_date' => 'analysis_date',
            'time' => 'time',
            'status' => 'status'
        ];

        if ($sortBy === 'name') {
            // Join patient table for sorting by name
            $query->join('patients', 'reservations.patient_id', '=', 'patients.id')
                  ->select('reservations.*')
                  ->orderBy('patients.name', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
            
            // Secondary sort for consistency if sorting by date
            if ($sortBy === 'analysis_date') {
                $query->orderBy('time', $sortOrder);
            }
        }

        // Fetch booked reservations, paginated
        $bookings = $query->paginate(15);

        // Add Professional QR Codes to the collection (Direct Admin Links)
        foreach ($bookings as $booking) {
            $qrData = route('admin.bookings.show', $booking->id);
            $booking->barcode = BarcodeHelper::getQRUrl($qrData, '150x150');
        }

        return view('Adminstration.reservations', [
            'bookings' => $bookings,
            'doctors' => Doctor::all(),
        ]);
    }

    public function filterReservations(Request $request)
    {
        // This method handles the filter form submission
        // It redirects back to reservations with query parameters
        return $this->reservations($request);
    }

    // Add method to update booking status
    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:booked,ready,blocked,warning,pending_approval,completed',
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => $request->status]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $reservation->status,
                'message' => __('messages.booking_status_updated'),
            ]);
        }

        return redirect()->route('reservations')->with('success', __('messages.booking_status_updated'));
    }

    // Show reservation requests
    public function reservationRequests(Request $request)
    {
        $query = Request_reservation::with(['analyse']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or phone or ID
        if ($request->filled('search')) {
            $search = $request->search;
            if (is_numeric($search) && strlen($search) < 10) {
                $query->where('id', $search);
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%");
                });
            }
        }

        // Specific ID filters (Disambiguated qid)
        if ($request->filled('qid')) {
            Log::info("Filtering REQUESTS by qid: " . $request->qid);
            $query->where('id', $request->qid);
        }

        if ($request->filled('id')) { // Generic fallback
            $query->where('id', $request->id);
        }

        if ($request->filled('rid')) { // Cross-reference if a confirmed QR is scanned on requests page
             $reqId = Request_reservation::where('reservation_id', $request->rid)->value('id');
             if ($reqId) {
                 Log::info("Cross-referenced rid {$request->rid} to qid {$reqId}");
                 $query->where('id', $reqId);
             }
        }

        $requests = $query->orderByDesc('created_at')->paginate(10);

        return view('Adminstration.reservation-requests', [
            'requests' => $requests,
        ]);
    }

    // Confirm reservation request
    public function confirmRequest(Request $request, $id)
    {
        $reservationRequest = Request_reservation::with('analyses')->findOrFail($id);

        if ($reservationRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'هذا الطلب تمت معالجته بالفعل');
        }

        $request->validate([
            'analysis_date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'admin_notes' => 'nullable|string',
        ], [
            'analysis_date.after_or_equal' => __('messages.invalid_date_past'),
        ]);

        try {
            // Determine which analyses to add (pivot vs single column)
            $analyses = $reservationRequest->analyses;
            if ($analyses->isEmpty() && $reservationRequest->analyse_id) {
                $analyses = collect([$reservationRequest->analyse]);
            }

            // --- Integrated Smart Medical Validation Logic (Algeria Timezone: Africa/Algiers) ---
            $now = Carbon::now('Africa/Algiers');
            $appointmentDateTime = Carbon::parse(
                $request->analysis_date . ' ' . ($request->time ?? '09:00'),
                'Africa/Algiers'
            );
            
            $hoursRemaining = $now->floatDiffInHours($appointmentDateTime, false);
            
            // Fetch mapped rules from config
            $fastingTestsList = config('clinical_rules.fasting', []);
            $morningOnlyList = config('clinical_rules.morning_only', []);
            
            // Find which requested analyses fall into which rule category
            $requiresFasting = $analyses->contains(function($a) use ($fastingTestsList) {
                if (!$a) return false;
                // Check if name is in list or instructions mention fasting (null‑safe)
                $hasNamedRule = in_array($a->name, $fastingTestsList) || in_array($a->name_fr, $fastingTestsList);
                $hasFastingInstruction = !empty($a->preparation_instructions) && str_contains($a->preparation_instructions, 'صيام');
                return $hasNamedRule || $hasFastingInstruction;
            });
            
            $requiresMorning = $analyses->contains(function($a) use ($morningOnlyList) {
                if (!$a) return false;
                return in_array($a->name, $morningOnlyList) || in_array($a->name_fr, $morningOnlyList);
            });

            // Rule 1: Fasting Time Validation (Must have at least 8 hours beforehand)
            if ($hoursRemaining < 8 && $requiresFasting) {
                $reservationRequest->update([
                    'status' => 'rejected',
                    'admin_notes' => 'تم الرفض تلقائياً: وقت الموعد قريب جداً ولا يسمح بالصيام (أقل من 8 ساعات).'
                ]);
                
                $patientEmail = $reservationRequest->patient?->email ?: $reservationRequest->email;
                if ($patientEmail) {
                    $fastingAnalyses = $analyses->filter(function ($a) use ($fastingTestsList) {
                        if (!$a) {
                            return false;
                        }
                        $hasNamedRule = in_array($a->name, $fastingTestsList) || in_array($a->name_fr, $fastingTestsList);
                        $hasFastingInstruction = !empty($a->preparation_instructions) && str_contains($a->preparation_instructions, 'صيام');
                        return $hasNamedRule || $hasFastingInstruction;
                    });
                    Mail::send('emails.booking-too-close', [
                        'patientName' => $reservationRequest->name,
                        'fastingAnalyses' => $fastingAnalyses,
                        'analysisDate' => Carbon::parse($request->analysis_date)->format('d/m/Y'),
                        'analysisTime' => $request->time,
                        'rejectionReason' => 'تحاليلكم تتطلب صيام 8 ساعات على الأقل، والوقت المتبقي للموعد غير كافٍ.'
                    ], function ($message) use ($patientEmail) {
                        $message->to($patientEmail)
                                ->subject('تنبيه: عذراً، يجب إعادة جدولة موعد التحليل - LabNexa');
                    });
                }
                
                Log::info('Appointment too close for fasting (' . round($hoursRemaining, 2) . 'h). Auto-Rejected.');
                return redirect()->route('reservation.requests')->with('error', __('messages.rejection_insufficient_time'));
            }
            
            // Rule 2: Morning-Only Validation (Must be before 10:30 AM)
            $appointmentHour = (int) date('H', strtotime($request->time ?? '09:00'));
            $appointmentMinute = (int) date('i', strtotime($request->time ?? '00:00'));
            $timeAsDecimal = $appointmentHour + ($appointmentMinute / 60);
            
            if ($requiresMorning && $timeAsDecimal > 10.5) { // 10.5 = 10:30 AM
                $reservationRequest->update([
                    'status' => 'rejected',
                    'admin_notes' => 'تم الرفض تلقائياً: التحليل المطلوب هرموني/صباحي ويجب أن يجرى قبل 10:30 صباحاً.'
                ]);
                
                $patientEmail = $reservationRequest->patient?->email ?: $reservationRequest->email;
                if ($patientEmail) {
                    Mail::send('emails.booking-too-close', [ // Reusing the too-close template for generic rejection
                        'patientName' => $reservationRequest->name,
                        'fastingAnalyses' => $analyses->filter(fn($a) => $a && in_array($a->name, $morningOnlyList)),
                        'analysisDate' => Carbon::parse($request->analysis_date)->format('d/m/Y'),
                        'analysisTime' => $request->time,
                        'rejectionReason' => 'التحاليل الهرمونية المطلوبة يجب إجراؤها في الصباح الباكر (قبل الساعة 10:30 صباحاً) لضمان دقة النتائج.'
                    ], function ($message) use ($patientEmail) {
                        $message->to($patientEmail)
                                ->subject('تنبيه: عذراً، يجب إعادة جدولة موعد التحليل - LabNexa');
                    });
                }
                
                Log::info('Appointment booked too late for morning tests (Time: ' . $request->time . '). Auto-Rejected.');
                return redirect()->route('reservation.requests')->with('error', __('messages.rejection_hormonal_time'));
            }
            // --- End Medical Validation ---

            // Find or Create patient record
            $patient = null;
            if ($reservationRequest->patient_id) {
                $patient = Patient::find($reservationRequest->patient_id);
            }

            if (! $patient) {
                // Improved lookup: Check phone OR email, but clean phone first
                $searchPhone = preg_replace('/[^0-9]/', '', $reservationRequest->phone);
                
                $patient = Patient::where(function($q) use ($searchPhone, $reservationRequest) {
                    // Match by BOTH Name and (Phone or Email) to avoid merging family members
                    $q->where('name', $reservationRequest->name);
                    
                    $q->where(function($sub) use ($searchPhone, $reservationRequest) {
                        if ($searchPhone) {
                            $sub->where('phone', $reservationRequest->phone)
                                ->orWhere('phone', 'like', '%' . substr($searchPhone, -9));
                        }
                        if ($reservationRequest->email) {
                            $sub->orWhere('email', $reservationRequest->email);
                        }
                    });
                })->first();
            }

            if (! $patient) {
                $patient = Patient::create([
                    'name' => $reservationRequest->name,
                    'email' => $reservationRequest->email,
                    'phone' => $reservationRequest->phone,
                    'gender' => $reservationRequest->gender,
                    'birth_date' => $reservationRequest->birth_date,
                ]);
            } else {
                // Update existing patient info - always update email with the latest one provided
                $patient->update(array_filter([
                    'email'      => $reservationRequest->email ?: $patient->email,
                    'phone'      => $patient->phone      ?: $reservationRequest->phone,
                    'gender'     => $patient->gender     ?: $reservationRequest->gender,
                    'birth_date' => $patient->birth_date ?: $reservationRequest->birth_date,
                ]));
            }

            // Create ONE parent reservation
            $reservation = Reservation::create([
                'patient_id' => $patient->id,
                'analysis_date' => $request->analysis_date,
                'time' => $request->time,
                'status' => 'booked',
                'prescription_path' => $reservationRequest->prescription_path,
            ]);

            // Create linked reservation analyses
            foreach ($analyses as $analyse) {
                if (! $analyse) {
                    continue;
                }

                $resAnalysis = ReservationAnalysis::create([
                    'reservation_id' => $reservation->id,
                    'analysis_id' => $analyse->id,
                    'status' => 'booked',
                ]);

                // Also create a history record
                $history = History::create([
                    'patient_id' => $patient->id,
                    'analyse_id' => $analyse->id,
                    'analysis_date' => $request->analysis_date,
                    'time' => $request->time,
                    'status' => 'booked',
                    'result' => null,
                ]);

                // Create reminder for the reservation analysis
                Reminder::create([
                    'history_id' => $history->id,
                    'reservation_id' => $reservation->id,
                    'patient_id' => $patient->id,
                    'analyse_id' => $analyse->id,
                    'scheduled_for' => Carbon::parse($request->analysis_date)->subDay(),
                    'is_sent' => false,
                ]);
            }

            // Update reservation request
            $reservationRequest->update([
                'status' => 'confirmed',
                'patient_id' => $patient->id,
                'reservation_id' => $reservation->id,
                'admin_notes' => $request->admin_notes,
            ]);

            // Integrated Smart Reminder Logic (Email sending)
            // Always prefer the email from the booking form over stored patient email
            $patientEmail = $reservationRequest->email ?: $patient->email;
            $emailSentOk = false;
            if ($patientEmail) {
                try {
                    $smartMessage = null;
                    
                    // 2. If between 8h and 14h AND requires fasting -> Send immediate Reminder
                    if ($hoursRemaining >= 8 && $hoursRemaining < 14 && $requiresFasting) {
                        $fullHours = floor($hoursRemaining);
                        $remainingMins = round(($hoursRemaining - $fullHours) * 60);
                        $smartMessage = "تذكير: موعد التحليل يقترب! متبقي " . $fullHours . " ساعة و " . $remainingMins . " دقيقة. يرجى البدء في الصيام الآن لضمان دقة النتائج.";
                    }
                    // 3. If more than 14h AND requires fasting -> Preparation instruction
                    elseif ($hoursRemaining >= 14 && $requiresFasting) {
                        $smartMessage = "ملاحظة: هذا التحليل يتطلب الصيام لمدة 14 ساعة قبل الموعد. يرجى البدء في الصيام عند اقتراب الموعد بـ 14 ساعة لضمان النتائج.";
                    }
                    
                    // Send standard Confirmation/Reminder
                    $this->sendImmediateReminder($patient, $analyses, $request, $reservationRequest, $smartMessage);
                    $emailSentOk = true;

                } catch (\Exception $remException) {
                    Log::error('Smart Reminder Logic Error: ' . $remException->getMessage());
                }
            }

            $successMsg = __('messages.booking_confirmed_success');
            if ($patientEmail && !$emailSentOk) {
                $successMsg .= ' ⚠️ ' . __('messages.email_failed_notice');
            }

            return redirect()->route('reservation.requests')
                ->with('success', $successMsg)
                ->with('auto_download_id', $reservation->id);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تأكيد الطلب: '.$e->getMessage());
        }
    }

    /**
     * Helper to send the standard confirmation email with PDF attachment
     */
    private function sendImmediateReminder($patient, $analyses, $request, $reservationRequest, $smartMessage = null)
    {
        // Always prefer the email from the booking form (reservationRequest) over stored patient email
        $patientEmail = $reservationRequest->email ?: $patient->email;
        if (!$patientEmail) return;

        try {
            // Reload reservation request with analyses for PDF generation
            $reservationRequest->load('analyses');

            // Build translation maps
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
                'تحليل فيتامين د' => 'Vitamine D',
            ];

            $instructionTranslations = [
                'لا يتطلب صيام. يمكن إجراء التحليل في أي وقت.' => 'À jeun non requis. Peut être effectué à tout moment.',
                'يتطلب الصيام لمدة 8-12 ساعة قبل التحليل. يُسمح بشرب الماء فقط.' => 'Jeûne de 8 à 12 heures requis. Seule l\'eau est autorisée.',
                'يتطلب الصيام لمدة 12 ساعة قبل التحليل. تجنب الأطعمة الدسمة في اليوم السابق.' => 'Jeûne de 12 heures requis. Éviter les aliments gras la veille.',
                'يفضل الصيام لمدة 8 ساعات. تجنب الأدوية التي قد تؤثر على الكبد قبل الفحص بعد استشارة الطبيب.' => 'Jeûne de 8 heures préférable. Éviter les médicaments affectant le foie sans avis médical.',
                'يفضل الصيام لمدة 8 ساعات. شرب كمية كافية من الماء في اليوم السابق.' => 'Jeûne de 8 heures préférable. Bien s\'hydrater la veille.',
                'جمع عينة البول الصباحي الأول. غسل المنطقة التناسلية قبل جمع العينة.' => 'Recueillir les premières urines du matin après toilette intime.',
                'لا يتطلب أي تحضيرات خاصة. يمكن إجراء التحليل في أي وقت.' => 'Aucune preparation spéciale. Peut être effectué à tout moment.',
                'لا يتطلب صيام. تجنب أدوية الغدة الدرقية قبل 4 ساعات من التحليل بعد استشارة الطبيب.' => 'À jeun non requis. Éviter les médicaments thyroïdiens 4h avant sans avis médical.',
                'لا يتطلب صيام. يمكن إجراء التحليل في أي وقت من اليوم.' => 'À jeun non requis. Peut être effectué à tout moment.',
            ];

            // Apply translations to analyses
            foreach ($analyses as $analysis) {
                if (!$analysis) continue;
                $analysis->name_fr = $analysis->name_fr ?? ($analysisTranslations[$analysis->name] ?? $analysis->name);
                $analysis->prep_ar = $analysis->preparation_instructions;
                $analysis->prep_fr = $analysis->preparation_instructions_fr ?? ($instructionTranslations[$analysis->preparation_instructions] ?? $analysis->preparation_instructions);
            }

            // Generate QR code
            $barcode = null;
            if (extension_loaded('gd')) {
                $qrData = "VIST-" . $reservationRequest->id . " | Patient: " . $reservationRequest->name . " | Phone: " . $reservationRequest->phone;
                $barcode = BarcodeHelper::getQRBase64($qrData, '200x200');
            }

            // Generate PDF
            $pdfContent = Pdf::loadView('reservation-pdf', [
                'reservation' => $reservationRequest,
                'analyses' => $analyses,
                'barcode' => $barcode,
                'patientName' => $reservationRequest->name,
                'appointmentDate' => $request->analysis_date,
                'appointmentTime' => $request->time,
                'type' => 'request',
            ])->setPaper('A4', 'portrait')->output();


            // Save to temp file
            $tempPath = storage_path('app/temp_reservation_' . $reservationRequest->id . '.pdf');
            file_put_contents($tempPath, $pdfContent);

            // Send email with PDF attachment
            $analysisDate = Carbon::parse($request->analysis_date)->format('d/m/Y');
            $subject = $smartMessage ? 'إرشاد هام وتأكيد موعد التحليل - LabNexa' : 'تأكيد حجز التحليل - LabNexa';

            Mail::send('emails.booking-confirmed', [
                'patientName' => $reservationRequest->name,
                'reservationId' => $reservationRequest->id,
                'analysisDate' => $analysisDate,
                'analysisTime' => $request->time,
                'analyses' => $analyses,
                'smartMessage' => $smartMessage,
            ], function ($message) use ($patientEmail, $reservationRequest, $tempPath, $subject) {
                $patientName = $reservationRequest->name ?? 'Patient';
                $filename = 'preparation_reservation_' . $reservationRequest->id . '.pdf';
                $message->to($patientEmail)
                        ->subject($subject)
                        ->attach($tempPath, [
                            'as' => $filename,
                            'mime' => 'application/pdf',
                        ]);
            });

            // Clean up temp file
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            // Record Success
            $reservationRequest->update([
                'email_sent' => true,
                'email_error' => null
            ]);

            Log::info('Booking confirmation email sent to: ' . $patientEmail);
        } catch (\Exception $e) {
            // Record Failure
            $reservationRequest->update([
                'email_sent' => false,
                'email_error' => $e->getMessage()
            ]);
            Log::error("Failed to mark as completed: " . $e->getMessage());
        }
    }

    // Reject reservation request
    public function rejectRequest(Request $request, $id)
    {
        $reservationRequest = Request_reservation::findOrFail($id);

        if ($reservationRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'هذا الطلب تمت معالجته بالفعل');
        }

        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $reservationRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('reservation.requests')->with('success', __('messages.booking_rejected_success'));
    }

    /**
     * Check eligibility during the execution phase.
     */
    public function checkExecutionEligibility(AnalysisEligibilityService $eligibilityService, $id)
    {
        $resAnalysis = ReservationAnalysis::findOrFail($id);

        // Call the eligibility service
        $result = $eligibilityService->checkEligibility($resAnalysis->reservation->patient_id, $resAnalysis->analysis_id);

        $statusMap = [
            'block' => 'blocked',
            'warning' => 'warning',
            'approval' => 'pending_approval',
            'eligible' => 'ready',
        ];

        $newStatus = $statusMap[$result['status']] ?? 'ready';

        $resAnalysis->update(['status' => $newStatus]);

        return response()->json([
            'status' => $newStatus,
            'reason' => $result['reason'] ?? null,
            'notes' => $result['notes'] ?? [],
            'original_action' => $result['status'],
        ]);
    }

    /**
     * Show the eligibility check form for a specific reservation analysis.
     */
    public function showEligibilityCheck($id)
    {
        $resAnalysis = ReservationAnalysis::with(['reservation.patient', 'analyse.questions.options'])->findOrFail($id);

        $questions = Question::where('analyse_id', $resAnalysis->analysis_id)->with('options')->get();

    // Generate Professional QR Code (Base64 for reliability, Null-safe)
    $patientName = $resAnalysis->reservation->patient->name ?? $resAnalysis->reservation->name ?? 'Patient';
    $patientPhone = $resAnalysis->reservation->patient->phone ?? $resAnalysis->reservation->phone ?? 'N/A';
    $qrData = "VIST-" . $resAnalysis->reservation->id . " | Patient: " . $patientName . " | Phone: " . $patientPhone;
    $barcode = BarcodeHelper::getQRBase64($qrData, '200x200');

    return view('Adminstration.eligibility-check', [
        'booking' => $resAnalysis,
        'questions' => $questions,
        'barcode' => $barcode
    ]);
    }

    /**
     * Process the eligibility check answers.
     */
    public function submitEligibilityCheck(Request $request, AnalysisEligibilityService $eligibilityService, $id)
    {
        $resAnalysis = ReservationAnalysis::findOrFail($id);

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'exists:options,id',
        ]);

        // 1. Save answers
        foreach ($request->answers as $questionId => $optionId) {
            PatientAnswer::updateOrCreate(
                [
                    'patient_id' => $resAnalysis->reservation->patient_id,
                    'question_id' => $questionId,
                ],
                ['option_id' => $optionId]
            );
        }

        // 2. Call the eligibility service
        $result = $this->checkExecutionEligibility($eligibilityService, $id);
        $data = json_decode($result->getContent(), true);

        return redirect()->route('reservations')->with('success', __('messages.eligibility_assessed_success', ['status' => $data['status']]));
    }

    /**
     * Show the combined eligibility check form for all analyses in a reservation.
     */
    public function showFullEligibilityCheck($id)
    {
        $reservation = Reservation::with(['patient', 'reservationAnalyses.analyse.questions.options'])->findOrFail($id);

        // Generate Professional QR Code (Base64 for reliability, Null-safe)
        $patientName = $reservation->patient->name ?? $reservation->name ?? 'Patient';
        $patientPhone = $reservation->patient->phone ?? $reservation->phone ?? 'N/A';
        $qrData = "VIST-" . $reservation->id . " | Patient: " . $patientName . " | Phone: " . $patientPhone;
        $barcode = BarcodeHelper::getQRBase64($qrData, '200x200');

        return view('Adminstration.eligibility-check', [
            'reservation' => $reservation,
            'patient' => $reservation->patient,
            'barcode' => $barcode,
        ]);
    }

    /**
     * Process the eligibility check answers for all analyses in a reservation.
     */
    public function submitFullEligibilityCheck(Request $request, AnalysisEligibilityService $eligibilityService, $id)
    {
        Log::info("Starting full eligibility check for reservation: $id");
        $reservation = Reservation::with('reservationAnalyses.analyse')->findOrFail($id);

        $request->validate([
            'answers' => 'required|array',
        ]);

        // 1. Save answers for the patient
        foreach ($request->answers as $questionId => $optionData) {
            $optionIds = is_array($optionData) ? $optionData : [$optionData];

            // Remove old answers for this question to support multi-select sync
            PatientAnswer::where('patient_id', $reservation->patient_id)
                ->where('question_id', $questionId)
                ->delete();

            foreach ($optionIds as $optionId) {
                if (! $optionId) {
                    continue;
                }

                PatientAnswer::create([
                    'patient_id' => $reservation->patient_id,
                    'question_id' => $questionId,
                    'option_id' => $optionId,
                ]);
            }

            // Auto-set sub-questions if parent is 'NO' (e.g., Medication -> Diabetes Medication)
            $optionId = is_array($optionData) ? ($optionData[0] ?? null) : $optionData;
            if ($optionId) {
                $option = Option::find($optionId);
                if ($option && $option->value === 'NO') {
                    $subQs = Question::where('parent_question_id', $questionId)->get();
                    foreach ($subQs as $subQ) {
                        $noOption = Option::where('question_id', $subQ->id)->where('value', 'NO')->first();
                        if ($noOption) {
                            PatientAnswer::updateOrCreate(
                                ['patient_id' => $reservation->patient_id, 'question_id' => $subQ->id],
                                ['option_id' => $noOption->id]
                            );
                        }
                    }
                }
            }
        }

        // Run eligibility check for each analysis in the reservation
        $statusMap = [
            'block' => 'INVALID',
            'warning' => 'VALID_WITH_NOTE',
            'eligible' => 'READY',
        ];

        $viewStatusMap = [
            'INVALID' => 'blocked',
            'VALID_WITH_NOTE' => 'warning',
            'READY' => 'ready',
        ];

        // --- Start French Translation Map ---
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

        $instructionTranslations = [
            'لا يتطلب صيام. يمكن إجراء التحليل في أي وقت.' => 'À jeun non requis. Peut être effectué à tout moment.',
            'يتطلب الصيام لمدة 8-12 ساعة قبل التحليل. يُسمح بشرب الماء فقط.' => 'Jeûne de 8 à 12 heures requis. Seule l\'eau est autorisée.',
            'يتطلب الصيام لمدة 12 ساعة قبل التحليل. تجنب الأطعمة الدسمة في اليوم السابق.' => 'Jeûne de 12 heures requis. Éviter les aliments gras la veille.',
        ];
        // --- End French Translation Map ---

        $results = [];
        foreach ($reservation->reservationAnalyses as $resAnalysis) {
            try {
                $checkResult = $eligibilityService->checkEligibility($reservation->patient_id, $resAnalysis->analysis_id);

                $jsonStatus = $statusMap[$checkResult['status']] ?? 'READY';
                $viewStatus = $viewStatusMap[$jsonStatus] ?? 'ready';

                // Update database status
                $resAnalysis->update(['status' => $viewStatus]);

                // Translate notes
                $translatedNotes = [];
                if (isset($checkResult['notes'])) {
                    foreach ($checkResult['notes'] as $note) {
                        // Split note if it's "Question: Option"
                        if (str_contains($note, ': ')) {
                            [$q, $o] = explode(': ', $note, 2);
                            $translatedNotes[] = ($questionTranslations[$q] ?? $q) . ': ' . ($optionTranslations[$o] ?? $o);
                        } else {
                            $translatedNotes[] = $questionTranslations[$note] ?? $note;
                        }
                    }
                }

                $results[] = [
                    'analysis_id' => $resAnalysis->id,
                    'name' => $resAnalysis->analyse ? ($analysisTranslations[$resAnalysis->analyse->name] ?? $resAnalysis->analyse->name) : "Analysis #{$resAnalysis->analysis_id}",
                    'status' => $viewStatus,
                    'fasting_valid' => ($jsonStatus !== 'INVALID'),
                    'eligibility_status' => $jsonStatus,
                    'notes' => $translatedNotes,
                    'action' => $checkResult['status'],
                ];
            } catch (\Exception $e) {
                Log::error("Error checking eligibility for analysis {$resAnalysis->analysis_id}: " . $e->getMessage());
                $results[] = [
                    'analysis_id' => $resAnalysis->id,
                    'name' => $resAnalysis->analyse ? ($analysisTranslations[$resAnalysis->analyse->name] ?? $resAnalysis->analyse->name) : "Analysis #{$resAnalysis->analysis_id}",
                    'status' => 'ready',
                    'notes' => ["Erreur interne: " . $e->getMessage()],
                    'action' => 'eligible',
                ];
            }
        }

        Log::info("Eligibility check completed for reservation: $id. Found " . count($results) . " results.");

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'L\'évaluation a été mise à jour avec succès.',
                'results' => $results,
            ]);
        }

        return redirect()->route('reservations')->with('success', __('messages.eligibility_assessed_success', ['status' => $data['status']]));
    }

    /**
     * Update the status of a specific reservation analysis.
     */
    public function updateAnalysisStatus(Request $request, $id)
    {
        $resAnalysis = ReservationAnalysis::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:booked,ready,blocked,warning,pending_approval,completed',
        ]);

        $resAnalysis->update(['status' => $request->status]);

        // If it's AJAX, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $resAnalysis->status,
                'message' => __('messages.analysis_status_updated'),
            ]);
        }

        return back()->with('success', __('messages.analysis_status_updated'));
    }

    /**
     * Show detailed eligibility results for a reservation.
     */
    public function showEligibilityResults(AnalysisEligibilityService $eligibilityService, $id)
    {
        App::setLocale('fr');
        $reservation = Reservation::with(['patient', 'reservationAnalyses.analyse'])->findOrFail($id);

        // --- Translation Maps ---
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

        // Get all questions relevant to this reservation's analyses
        $analyseIds = $reservation->reservationAnalyses->pluck('analysis_id');
        
        // Get patient answers related to these analyses
        $patientAnswers = PatientAnswer::with(['question', 'option'])
            ->where('patient_id', $reservation->patient_id)
            ->whereHas('question', function($query) use ($analyseIds) {
                $query->whereIn('analyse_id', $analyseIds);
            })
            ->get();

        // Translate questions and options in patientAnswers
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

        // Generate Professional QR Code
        $patientName = $reservation->patient->name ?? $reservation->name ?? 'Patient';
        $patientPhone = $reservation->patient->phone ?? $reservation->phone ?? 'N/A';
        $qrData = "VIST-" . $reservation->id . " | Patient: " . $patientName . " | Phone: " . $patientPhone;
        $barcode = BarcodeHelper::getQRBase64($qrData, '200x200');

        return view('Adminstration.eligibility-results', compact('reservation', 'results', 'patientAnswers', 'barcode'));
    }

    /**
     * Show a printable diagnostic report for a reservation.
     */
    public function printEligibilityReport(AnalysisEligibilityService $eligibilityService, $id)
    {
        App::setLocale('fr');
        $reservation = Reservation::with(['patient', 'reservationAnalyses.analyse'])->findOrFail($id);

        // --- Translation Maps ---
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

        // Translate questions and options in patientAnswers
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

        // Generate Professional QR Code
        $patientName = $reservation->patient->name ?? $reservation->name ?? 'Patient';
        $patientPhone = $reservation->patient->phone ?? $reservation->phone ?? 'N/A';
        $qrData = "VIST-" . $reservation->id . " | Patient: " . $patientName . " | Phone: " . $patientPhone;
        $barcode = BarcodeHelper::getQRBase64($qrData, '200x200');

        return view('Adminstration.eligibility-report', compact('reservation', 'results', 'patientAnswers', 'barcode'));
    }

    /**
     * Link physician to reservation
     */
    public function updateReferral(Request $request, $id)
    {
        $request->validate([
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        $reservation = Reservation::with(['patient', 'reservationAnalyses.analyse'])->findOrFail($id);
        
        $reservation->update(['doctor_id' => $request->doctor_id]);

        if ($request->doctor_id) {
            $doctor = Doctor::find($request->doctor_id);
            if ($doctor && $doctor->email) {
                // Fetch eligibility findings for the report
                $eligibilitySummary = [];
                $eligibilityService = app(AnalysisEligibilityService::class);
                
                foreach ($reservation->reservationAnalyses as $ra) {
                    $res = $eligibilityService->checkEligibility($reservation->patient_id, $ra->analyse_id);
                    if (!empty($res['notes'])) {
                        // Merge unique notes
                        $eligibilitySummary = array_values(array_unique(array_merge($eligibilitySummary, $res['notes'])));
                    }
                }

                try {
                    Mail::send('emails.doctor-referral', [
                        'doctor' => $doctor,
                        'patient' => $reservation->patient,
                        'reservation' => $reservation,
                        'eligibilitySummary' => $eligibilitySummary,
                    ], function($message) use ($doctor, $reservation) {
                        $message->to($doctor->email)
                                ->subject(__('messages.new_referral') . ': ' . ($reservation->patient->name ?? 'مريض') . ' - LabNexa');
                    });
                } catch (\Exception $e) {
                    Log::error("Failed to auto-notify doctor {$doctor->id} for reservation {$id}: " . $e->getMessage());
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.doctor_linked_success'),
        ]);
    }

    /**
     * Notify participants (Patient & Doctor) about results
     */
    public function notifyParticipants(Request $request, $id)
    {
        $reservation = Reservation::with(['patient', 'doctor', 'reservationAnalyses.analyse'])->findOrFail($id);
        $target = $request->get('target', 'both'); // patient, doctor, both
        
        $notifications = [];
        $errors = [];
        $additionalNotes = $reservation->result_notes ?? '';

        // 1. Notify Patient
        if ($target == 'patient' || $target == 'both') {
            if (!$reservation->patient || !$reservation->patient->email) {
                $errors[] = __('messages.patient_no_email');
            } else {
                try {
                    Mail::send('emails.test-result', [
                        'patient' => $reservation->patient,
                        'reservation' => $reservation,
                        'additional_notes' => $additionalNotes,
                    ], function($message) use ($reservation) {
                        $message->to($reservation->patient->email)
                                ->subject(__('messages.results_ready_title') . ' - LabNexa');
                    });

                    // Create persistent reminder for the patient portal
                    Reminder::create([
                        'patient_id' => $reservation->patient_id,
                        'reservation_id' => $reservation->id,
                        'message' => __('messages.results_ready_desc'),
                        'scheduled_for' => now(),
                        'sent_at' => now(),
                        'is_sent' => true,
                    ]);

                    $notifications[] = __('messages.patient');
                } catch (\Exception $e) {
                    Log::error("Failed to notify patient for reservation {$id}: " . $e->getMessage());
                    $errors[] = "خطأ في إرسال بريد المريض: " . $e->getMessage();
                }
            }
        }

        // 2. Notify Doctor (Using detailed referral template)
        if ($target == 'doctor' || $target == 'both') {
            if (!$reservation->doctor_id) {
                $errors[] = __('messages.no_doctor_assigned');
            } elseif (!$reservation->doctor || !$reservation->doctor->email) {
                $errors[] = __('messages.doctor_no_email');
            } else {
                try {
                    // Fetch eligibility findings for the report
                    $eligibilitySummary = [];
                    $eligibilityService = app(AnalysisEligibilityService::class);
                    
                    foreach ($reservation->reservationAnalyses as $ra) {
                        $res = $eligibilityService->checkEligibility($reservation->patient_id, $ra->analyse_id);
                        if (!empty($res['notes'])) {
                            $eligibilitySummary = array_values(array_unique(array_merge($eligibilitySummary, $res['notes'])));
                        }
                    }

                    Mail::send('emails.doctor-referral', [
                        'doctor' => $reservation->doctor,
                        'patient' => $reservation->patient,
                        'reservation' => $reservation,
                        'eligibilitySummary' => $eligibilitySummary,
                    ], function($message) use ($reservation) {
                        $message->to($reservation->doctor->email)
                                ->subject('تقرير نتائج المريض: ' . ($reservation->patient->name ?? 'مريض') . ' - LabNexa');
                    });
                    $notifications[] = __('messages.doctor');
                } catch (\Exception $e) {
                    Log::error("Failed to notify doctor for reservation {$id}: " . $e->getMessage());
                    $errors[] = "خطأ في إرسال بريد الطبيب: " . $e->getMessage();
                }
            }
        }

        // Construct response message
        $messageLines = [];
        if (count($notifications) > 0) {
            $messageLines[] = 'تم إرسال التنبيهات لـ: ' . implode(', ', $notifications);
        }
        if (count($errors) > 0) {
            $messageLines[] = implode(' | ', $errors);
        }

        $finalMessage = !empty($messageLines) ? implode(' | ', $messageLines) : 'لم يتم تنفيذ أي عملية';

        return response()->json([
            'success' => count($notifications) > 0,
            'message' => $finalMessage,
        ]);
    }

    /**
     * Show the results entry form for a specific reservation
     */
    public function showResultsForm($id)
    {
        $reservation = Reservation::with(['patient', 'reservationAnalyses.analyse'])->findOrFail($id);
        
        // Ensure that each reservation analysis has a unit and reference range pre-filled if they are empty
        foreach ($reservation->reservationAnalyses as $resAnalysis) {
            if (empty($resAnalysis->unit) && !empty($resAnalysis->analyse->unit)) {
                $resAnalysis->unit = $resAnalysis->analyse->unit;
            }
            if (empty($resAnalysis->reference_range) && !empty($resAnalysis->analyse->normal_range)) {
                $resAnalysis->reference_range = $resAnalysis->analyse->normal_range;
            }
        }

        return view('Adminstration.results-form', compact('reservation'));
    }

    /**
     * Update clinical results for a reservation
     */
    public function updateResults(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $resultsData = $request->input('results', []);

        foreach ($resultsData as $analysisId => $data) {
            $resAnalysis = ReservationAnalysis::where('reservation_id', $id)
                ->where('id', $analysisId)
                ->first();

            if ($resAnalysis) {
                $value = $data['value'] ?? null;
                $clinicalStatus = 'Normal';

                // Automated Safety Check (Critical Values)
                if ($value !== null && is_numeric($value)) {
                    $analyse = $resAnalysis->analyse;
                    if ($analyse) {
                        if ($analyse->max_critical !== null && $value >= $analyse->max_critical) {
                            $clinicalStatus = 'CRITICAL';
                        } elseif ($analyse->min_critical !== null && $value <= $analyse->min_critical) {
                            $clinicalStatus = 'CRITICAL';
                        }
                    }
                }

                $resAnalysis->update([
                    'result_value' => $value,
                    'unit' => $data['unit'] ?? null,
                    'reference_range' => $data['reference_range'] ?? null,
                    'clinical_status' => $clinicalStatus,
                ]);
            }
        }

        // Update reservation status to completed since results are now present
        $reservation->update(['status' => 'completed']);

        // Trigger notification if requested
        if ($request->has('notify_patient') && $request->notify_patient == '1') {
            $this->notifyParticipants($request->merge(['target' => 'patient']), $id);
        }

        // --- AUTOMATED PDF GENERATION ---
        try {
            $this->generateAndSaveResultPdf($reservation);
        } catch (\Exception $e) {
            Log::error('Automated PDF generation failed: ' . $e->getMessage());
        }
        // --------------------------------

        return redirect()->route('reservations')->with('success', __('messages.results_updated'));
    }

    /**
     * Show dedicated details page for a specific reservation
     */
    public function showReservation($id)
    {
        $reservation = Reservation::with(['patient', 'reservationAnalyses.analyse', 'doctor'])->findOrFail($id);
        $doctors = Doctor::all();

        // Generate direct-link QR code for this specific visit
        $qrData = route('admin.bookings.show', $reservation->id) . "?rid=" . $reservation->id . "&t=" . time();
        $reservation->barcode = BarcodeHelper::getQRBase64($qrData, '150x150');

        return view('Adminstration.reservation-details', compact('reservation', 'doctors'));
    }

    /**

     * Generate AI Clinical Insight for the reservation.
     */
    public function generateAiInsight($id, \App\Services\AiService $aiService)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            
            // 1. Check if we already have a valid insight to save quota
            if (!empty($reservation->ai_insight) && $reservation->ai_status === 'ready') {
                return response()->json([
                    'success' => true,
                    'insight' => $reservation->ai_insight
                ]);
            }

            // Mark as generating
            $reservation->update(['ai_status' => 'generating']);
            
            $insight = $aiService->generateInsight($reservation);
            
            $reservation->update([
                'ai_insight' => $insight,
                'ai_status' => 'ready'
            ]);

            return response()->json([
                'success' => true,
                'insight' => $insight
            ]);
        } catch (\Exception $e) {
            Log::error("Reservation error: " . $e->getMessage());
            
            if (isset($reservation)) {
                $reservation->update(['ai_status' => 'error']);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate AI insight.'
            ], 500);
        }
    }

    /**
     * Helper to generate and save the result PDF for a reservation
     */
    private function generateAndSaveResultPdf($reservation)
    {
        // Set locale to French for the official report
        $originalLocale = App::getLocale();
        App::setLocale('fr');

        $reservation->load(['analyses', 'patient']);
        $analyses = $reservation->analyses;

        // Translation Maps (consistent with sendImmediateReminder/PdfController)
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
             'تحليل فيتامين د' => 'Vitamine D',
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

        foreach ($analyses as $analysis) {
            $analysis->name_fr = $analysis->name_fr ?? ($analysisTranslations[$analysis->name] ?? $analysis->name);
            if (isset($analysis->pivot->reference_range)) {
                $rawRange = $analysis->pivot->reference_range;
                $analysis->pivot->reference_range = $rangeTranslations[$rawRange] ?? $rawRange;
            }
        }

        // QR Code (Direct Admin Link)
        $barcode = null;
        if (extension_loaded('gd')) {
            $qrData = route('admin.bookings.show', $reservation->id);
            $barcode = BarcodeHelper::getQRBase64($qrData, '200x200');
        }

        // Generate PDF
        $pdf = Pdf::loadView('reservation-pdf', [
            'reservation' => $reservation,
            'analyses' => $analyses,
            'barcode' => $barcode,
            'patientName' => $reservation->patient->name,
            'patientPhone' => $reservation->patient->phone ?? 'N/A',
            'appointmentDate' => $reservation->analysis_date,
            'appointmentTime' => $reservation->time ?? '--:--',
            'type' => 'confirmed',
            'isResultReport' => true,
            'isEligibilityReport' => false,
            'eligibilityResults' => [],
            'patientAnswers' => null
        ])->setPaper('A4', 'portrait');

        $pdfContent = $pdf->output();
        
        // Define directory and filename
        $directory = 'public/results';
        if (!Storage::disk('public')->exists('results')) {
            Storage::disk('public')->makeDirectory('results');
        }

        $filename = 'result_' . $reservation->id . '_' . time() . '.pdf';
        $path = 'results/' . $filename;

        // Save file
        Storage::disk('public')->put($path, $pdfContent);

        // Update reservation record
        $reservation->update([
            'result_file_path' => $path
        ]);

        // Restore original locale
        App::setLocale($originalLocale);

        Log::info("Automated Result PDF generated and saved", ['path' => $path, 'reservation_id' => $reservation->id]);
    }
}
