<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\Patient;
use App\Models\Reservation;
use App\Models\Request_reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MobileAppController extends Controller
{
    /**
     * Display the Dedicated Mobile App Shell
     */
    public function index(Request $request)
    {
        $analyses = Analyse::all();
        $patient = Auth::guard('patient')->user();
        $reservations = collect();
        $latestReservation = null;

        if ($patient) {
            $reservations = Reservation::with(['doctor', 'reservationAnalyses.analyse'])
                ->where('patient_id', $patient->id)
                ->orderBy('analysis_date', 'desc')
                ->orderBy('time', 'desc')
                ->get();
            $latestReservation = $reservations->first();
        }

        return view('mobile-app.index', compact('analyses', 'patient', 'reservations', 'latestReservation'));
    }

    /**
     * Instant lookup by Reservation ID or Phone (No full login required)
     */
    public function track(Request $request)
    {
        $query = trim($request->input('query', ''));
        $clean = preg_replace('/[^0-9]/', '', $query);

        if (empty($clean)) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() == 'ar' ? 'يرجى إدخال رقم هاتف أو رقم حجز صالح.' : 'Veuillez saisir un numéro valide.'
            ], 422);
        }

        // Try lookup by reservation ID, request_reservation ID, or patient phone
        $reservation = Reservation::with(['patient', 'doctor', 'reservationAnalyses.analyse'])
            ->where('id', $clean)
            ->orWhereHas('patient', function ($q) use ($clean) {
                $q->where('phone', 'like', "%{$clean}%");
            })
            ->latest('analysis_date')
            ->first();

        if ($reservation) {
            $analyses = $reservation->reservationAnalyses->map(function ($ra) {
                return [
                    'name' => $ra->analyse->name ?? 'تحليل مخبري',
                    'name_fr' => $ra->analyse->name_fr ?? $ra->analyse->name ?? 'Analyse',
                    'result' => $ra->result_value ?? null,
                    'unit' => $ra->unit ?? ($ra->result_unit ?? ($ra->analyse->unit ?? '')),
                    'reference' => $ra->reference_range ?? ($ra->analyse->normal_range ?? '--'),
                    'status' => $ra->status ?? 'pending',
                ];
            });

            return response()->json([
                'success' => true,
                'found' => true,
                'type' => 'confirmed',
                'reservation' => [
                    'id' => $reservation->id,
                    'date' => $reservation->analysis_date,
                    'time' => $reservation->time,
                    'status' => in_array($reservation->status, ['ready', 'completed']) ? 'ready' : 'pending',
                    'patient_name' => $reservation->patient->name ?? 'مريض',
                    'doctor_name' => $reservation->doctor->name ?? null,
                    'ai_insight' => $reservation->ai_insight ?? null,
                    'analyses' => $analyses
                ]
            ]);
        }

        // If not in confirmed reservations, check request_reservations (pending appointments)
        $reqRes = Request_reservation::with(['analyses'])
            ->where('id', $clean)
            ->orWhere('phone', 'like', "%{$clean}%")
            ->latest('created_at')
            ->first();

        if ($reqRes) {
            $analyses = $reqRes->analyses->map(function ($a) {
                return [
                    'name' => $a->name,
                    'name_fr' => $a->name_fr ?? $a->name,
                    'result' => null,
                    'unit' => $a->unit ?? '',
                    'reference' => $a->normal_range ?? '--',
                    'status' => 'pending_confirmation',
                ];
            });

            return response()->json([
                'success' => true,
                'found' => true,
                'type' => 'request',
                'reservation' => [
                    'id' => $reqRes->id,
                    'date' => $reqRes->date,
                    'time' => $reqRes->time,
                    'status' => 'pending',
                    'patient_name' => $reqRes->name,
                    'doctor_name' => null,
                    'ai_insight' => null,
                    'analyses' => $analyses
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => app()->getLocale() == 'ar' 
                ? 'لم يتم العثور على أي نتائج أو مواعيد مطابقة لهذا الرقم.' 
                : 'Aucun résultat trouvé pour ce numéro.'
        ], 404);
    }

    /**
     * In-App Quick Login
     */
    public function inAppLogin(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = trim($request->identifier);
        $patient = Patient::where('phone', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if ($patient && Hash::check($request->password, $patient->password)) {
            Auth::guard('patient')->login($patient, true);
            return response()->json([
                'success' => true,
                'message' => app()->getLocale() == 'ar' ? 'تم تسجيل الدخول بنجاح!' : 'Connexion réussie !'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => app()->getLocale() == 'ar' ? 'بيانات الدخول غير صحيحة.' : 'Identifiants incorrects.'
        ], 401);
    }

    /**
     * In-App Logout
     */
    public function inAppLogout()
    {
        Auth::guard('patient')->logout();
        return redirect()->route('mobile.app');
    }
}
