<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Support\Facades\Log;
use App\Models\Analyse;
use App\Models\Reservation;
use App\Models\ReservationAnalysis;
use App\Models\History;
use App\Models\Reminder;
use App\Models\Question;
use App\Models\PatientAnswer;
use App\Models\AnalysisRule;
use App\Models\Request_reservation;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class PatientApiController extends Controller
{
    // ========================
    // Helper to build reliable URLs
    // ========================
    private function getFullUrl($request, $path)
    {
        if (!$path) return null;
        
        // Use our stable file serving route instead of 'storage/' symlink
        // This bypasses symlink issues in containerized environments like Coolify
        $cleanPath = str_replace(['public/', 'storage/'], '', $path);
        $baseUrl = $request->getSchemeAndHttpHost();
        return $baseUrl . '/api/v1/patient/file/' . $cleanPath;
    }

    // ========================
    // Securely serve files from storage
    // ========================
    public function serveFile(Request $request, $path)
    {
        // Security: block path traversal
        if (str_contains($path, '..')) {
            abort(403, 'Path traversal detected');
        }

        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            $fullPath = storage_path('app/' . $path);
        }

        if (!file_exists($fullPath)) {
            $fullPath = base_path($path);
        }

        if (!file_exists($fullPath)) {
            abort(404, 'File not found');
        }

        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $headers = [];

        // Force APK mime type to prevent phones from seeing it as ZIP
        if (strtolower($extension) === 'apk') {
            $headers = [
                'Content-Type' => 'application/vnd.android.package-archive',
                'Content-Disposition' => 'attachment; filename="' . basename($fullPath) . '"',
            ];
        }

        return response()->file($fullPath, $headers);
    }

    // ========================
    // POST /v1/patient/register
    // ========================
    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email',
            'phone'                 => 'required|string|max:20',
            'password'              => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed'    => 'كلمة المرور غير متطابقة.',
            'password.min'          => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
        ]);

        // Check if patient already exists (by phone or email)
        $patient = Patient::where('phone', $request->phone)
                          ->orWhere('email', $request->email)
                          ->first();

        if ($patient) {
            // Case 1: Patient exists and already has a password set
            if ($patient->password) {
                return response()->json([
                    'message' => 'هذا البريد الإلكتروني أو الهاتف مسجل بالفعل وله كلمة سر. يرجى تسجيل الدخول.'
                ], 422);
            }

            // Case 2: Patient exists (e.g. from manual lab entry) but has no app password
            $patient->update([
                'name'     => $request->name, // Update to preferred name
                'email'    => $request->email,
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
            ]);
        } else {
            // Case 3: Completely new patient
            $patient = Patient::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
            ]);
        }

        $token = $patient->createToken('patient-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'message' => __('messages.api_register_link_success'),
            'patient' => [
                'id'    => $patient->id,
                'name'  => $patient->name,
                'email' => $patient->email,
                'phone' => $patient->phone,
            ]
        ], 201);
    }

    // ========================
    // POST /v1/patient/forgot-password
    // ========================
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'phone'                 => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed'    => 'كلمة المرور غير متطابقة.',
            'password.min'          => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
        ]);

        // Clean input strings to avoid whitespace and bidi control chars
        $email = trim(preg_replace('/[\x{00A0}\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}\x{FEFF}]/u', '', $request->email));
        $phone = trim(preg_replace('/[\x{00A0}\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}\x{FEFF}\s\-]/u', '', $request->phone));

        $patient = Patient::where('email', $email)
                          ->where('phone', $phone)
                          ->first();

        if (!$patient) {
            return response()->json([
                'message' => 'لم نتمكن من العثور على حساب يطابق هذا البريد الإلكتروني ورقم الهاتف معاً.'
            ], 442); // Using 442 or 404
        }

        $patient->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'تم إعادة تعيين كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول.'
        ], 200);
    }

    // ========================
    // POST /v1/patient/login
    // ========================
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find patient by email OR phone, prioritizing those with a password set
        $loginKey = trim($request->email);
        $password = trim($request->password);
        
        $patient = Patient::where(function($q) use ($loginKey) {
                                // Direct match
                                $q->where('name', $loginKey)
                                  ->orWhere('email', $loginKey)
                                  ->orWhere('phone', $loginKey);
                                
                                // Also try matching phone without spaces or dashes if it looks like a number
                                if (is_numeric(str_replace([' ', '-', '+'], '', $loginKey))) {
                                    $cleanKey = str_replace([' ', '-', '+'], '', $loginKey);
                                    $q->orWhere('phone', 'like', '%' . $cleanKey . '%');
                                }
                          })
                          ->orderByRaw('password IS NULL ASC')
                          ->first();

        if (!$patient) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة (المستخدم غير موجود).'], 401);
        }

        if (!$patient->password) {
            return response()->json(['message' => 'هذا الحساب موجود ولكن لم يتم تعيين كلمة سر له بعد.'], 401);
        }

        if (!Hash::check($password, $patient->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة (كلمة المرور خاطئة).'], 401);
        }

        $token = $patient->createToken('patient-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'email' => $patient->email,
                'phone' => $patient->phone,
            ]
        ]);
    }

    // ========================
    // GET /v1/patient/profile
    // ========================
    public function profile(Request $request)
    {
        $patient = $request->user();
        return response()->json(['patient' => $patient]);
    }

    // ========================
    // GET /v1/patient/analyses
    // ========================
    public function analyses()
    {
        $analyses = Analyse::where('availability', true)
            ->select(['id', 'name', 'name_fr', 'description', 'preparation_instructions', 'preparation_video', 'duration', 'unit', 'normal_range', 'min_critical', 'max_critical', 'price', 'code'])
            ->get()
            ->makeVisible(['normalized_video_url', 'youtube_video_id', 'standard_video_url']);

        // Explicitly append them
        foreach ($analyses as $analyse) {
            $analyse->append(['normalized_video_url', 'youtube_video_id', 'standard_video_url']);
        }

        return response()->json(['data' => $analyses]);
    }

    // ==============================
    // GET /v1/patient/analyses/{id}
    // ==============================
    public function analysisDetail($id)
    {
        $analysis = Analyse::findOrFail($id);
        $analysis->append(['normalized_video_url', 'youtube_video_id', 'standard_video_url']);
        return response()->json(['data' => $analysis]);
    }

    // ================================
    // GET /v1/patient/reservations
    // ================================
    public function reservations(Request $request)
    {
        $patient = $request->user();

        // Ensure storage symlink exists (prevents 404 after redeploy)
        if (!file_exists(public_path('storage'))) {
            try {
                if (function_exists('symlink')) {
                    @symlink(storage_path('app/public'), public_path('storage'));
                } else {
                    app('files')->link(storage_path('app/public'), public_path('storage'));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to create storage link: " . $e->getMessage());
            }
        }

        $reservations = Reservation::where('patient_id', $patient->id)
            ->with(['analyses'])
            ->orderByDesc('analysis_date')
            ->get()
            ->map(function($res) {
                // Determine status: if results exist, it should be green (confirmed in app terms)
                $hasResults = $res->result_file_path || $res->analyses->contains(function($a) {
                    return !empty($a->pivot->result_value);
                });
                
                // Map to 'confirmed' for ready results so app shows green label
                $status = $hasResults ? 'confirmed' : ($res->status ?? 'confirmed');

                return [
                    'id' => $res->id,
                    'patient_id' => $res->patient_id,
                    'analysis_date' => $res->analysis_date instanceof \Carbon\Carbon
                        ? $res->analysis_date->format('Y-m-d')
                        : $res->analysis_date,
                    'time' => $res->time,
                    'status' => $status,
                    'is_request' => false,
                    'analyses' => $res->analyses,
                    'created_at' => $res->created_at,
                ];
            });

        $requests = Request_reservation::where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->with(['analyses'])
            ->orderByDesc('preferred_date')
            ->get()
            ->map(function($req) {
                return [
                    'id' => $req->id,
                    'patient_id' => $req->patient_id,
                    'analysis_date' => $req->preferred_date->format('Y-m-d'),
                    'time' => $req->preferred_time ? $req->preferred_time->format('H:i') : null,
                    'status' => $req->status,
                    'is_request' => true,
                    'analyses' => $req->analyses,
                    'created_at' => $req->created_at,
                ];
            });

        $combined = $reservations->concat($requests)->sortByDesc(function ($item) {
            return $item['analysis_date'];
        })->values();

        return response()->json(['data' => $combined]);
    }

    // ================================
    // POST /v1/patient/reservations
    // ================================
    public function createReservation(Request $request)
    {
        $request->validate([
            'analysis_date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'analysis_ids' => 'required|array|min:1',
            'analysis_ids.*' => 'exists:analyses,id',
        ]);

        $patient = $request->user();

        // 12 Hours Lead Time Check
        $appointmentDateTime = \Carbon\Carbon::parse($request->analysis_date . ' ' . $request->time);
        if ($appointmentDateTime->diffInHours(\Carbon\Carbon::now()) < 12 && $appointmentDateTime->isAfter(\Carbon\Carbon::now())) {
            return response()->json(['message' => 'عذراً، يجب أن يكون الحجز قبل 12 ساعة على الأقل من الموعد المختار.'], 422);
        }

        // Daily Limit Check
        $dayOfWeek = strtolower(\Carbon\Carbon::parse($request->analysis_date)->englishDayOfWeek);
        $settingKey = "booking_limit_{$dayOfWeek}";
        $dailyLimit = Setting::where('key', $settingKey)->value('value') ?? 50;

        $currentBookingsCount = Request_reservation::whereDate('preferred_date', $request->analysis_date)
            ->where('status', '!=', 'rejected')
            ->count();

        if ($currentBookingsCount >= $dailyLimit) {
            return response()->json(['message' => 'عذراً، لقد اكتمل العدد المسموح به من الحجوزات لهذا اليوم. يرجى اختيار يوم آخر.'], 422);
        }

        $requestReservation = Request_reservation::create([
            'patient_id' => $patient->id,
            'name' => $patient->name,
            'email' => $patient->email,
            'phone' => $patient->phone,
            'gender' => $patient->gender ?? 'male',
            'birth_date' => $patient->birth_date,
            'preferred_date' => $request->analysis_date,
            'preferred_time' => $request->time,
            'status' => 'pending',
            'analyse_id' => $request->analysis_ids[0] ?? null,
        ]);

        $requestReservation->analyses()->attach($request->analysis_ids);

        return response()->json([
            'message' => __('messages.api_booking_success'),
            'data' => $requestReservation
        ], 201);
    }

    // ================================
    // GET /v1/patient/results
    // ================================
    public function results(Request $request)
    {
        $patient = $request->user();
        $reservations = Reservation::where('patient_id', $patient->id)
            ->whereHas('analyses', fn($q) => $q->whereNotNull('result_value'))
            ->with(['analyses:id,name,name_fr,normal_range,unit,min_critical,max_critical'])
            ->orderByDesc('analysis_date')
            ->get();

        // Harmonize structure for Mobile App
        $data = $reservations->map(function($res) use ($request) {
            $analyses = $res->analyses->map(function($a) {
                return [
                    'id' => $a->pivot->id ?? null,
                    'result' => $a->pivot->result_value,
                    'analysis' => [
                        'id' => $a->id,
                        'name' => $a->name,
                        'name_fr' => $a->name_fr,
                        'unit' => $a->pivot->unit ?? $a->unit,
                        'normal_range' => $a->pivot->reference_range ?? $a->normal_range,
                        'min_critical' => $a->min_critical,
                        'max_critical' => $a->max_critical,
                    ]
                ];
            });

            return [
                'id' => $res->id,
                'analysis_date' => $res->analysis_date instanceof \Carbon\Carbon
                    ? $res->analysis_date->format('Y-m-d')
                    : $res->analysis_date,
                'result_pdf_url' => $this->getFullUrl($request, $res->result_file_path),
                'analyses' => $analyses,
                'eligibility_report_url' => $this->getFullUrl($request, $res->eligibility_report_path),
            ];
        });

        return response()->json(['data' => $data]);
    }

    // =========================================
    // GET /v1/common/version
    // =========================================
    public function getLatestVersion(Request $request)
    {
        return response()->json([
            'version' => '1.1.4',
            'download_url' => $this->getFullUrl($request, 'labnexa_v1.1.4.apk'),
            'release_notes' => "تحديث الهوية البصرية: تم تطبيق الشعار الجديد LABNEXA في جميع واجهات النظام وتطبيق الموبايل.",
            'force_update' => false
        ]);
    }

    // =========================================
    // GET /v1/patient/notifications
    // =========================================
    public function notifications(Request $request)
    {
        $patient = $request->user();
        
        $notifications = Reminder::where('patient_id', $patient->id)
            ->where(function($q) {
                $q->where('is_sent', true)
                  ->orWhere('scheduled_for', '<=', now());
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function($rem) {
                return [
                    'id' => $rem->id,
                    'message' => $rem->message,
                    'type' => $rem->reminder_type ?? 'info',
                    'date' => $rem->sent_at ?? $rem->scheduled_for,
                    'is_read' => $rem->is_sent,
                    'reservation_id' => $rem->reservation_id,
                ];
            });

        return response()->json($notifications);
    }

    public function markNotificationRead(Request $request, $id)
    {
        $patient = $request->user();
        $reminder = Reminder::where('id', $id)->where('patient_id', $patient->id)->first();
        
        if ($reminder) {
            $reminder->update(['is_sent' => true, 'sent_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    // =========================================
    // GET /v1/patient/eligibility-report/{id}
    // =========================================
    public function eligibilityReport(Request $request, $id)
    {
        $patient = $request->user();
        
        $isOwned = Request_reservation::where('id', $id)->where('patient_id', $patient->id)->exists() ||
                   Reservation::where('id', $id)->where('patient_id', $patient->id)->exists();

        if (!$isOwned) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        $type = Request_reservation::where('id', $id)->exists() ? 'request' : 'confirmed';
        $request->merge(['report_type' => 'eligibility']);

        return app(\App\Http\Controllers\PdfController::class)->generateReservationPdf($request, $id, $type);
    }

    // =========================================
    // POST /v1/patient/logout
    // =========================================
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => __('messages.logout_success')]);
    }
}
