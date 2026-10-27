<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PatientPortalController extends Controller
{
    /**
     * Show the login form for patients.
     */
    public function showLogin()
    {
        if (Session::has('patient_reservation_id')) {
            return redirect()->route('patient.dashboard');
        }
        return view('patient-portal.login');
    }

    /**
     * Handle patient access request.
     */
    public function access(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'reservation_id' => 'required|string',
        ]);

        // Normalize Case Number (Strip #, V-, R-, spaces)
        $inputID = strtoupper(trim($request->reservation_id));
        $inputID = str_replace(['#', ' '], '', $inputID);
        $numericID = $inputID;

        if (preg_match('/^[VR]-?(\d+)$/', $inputID, $matches)) {
            $numericID = $matches[1];
        }

        // Normalize Phone (Strip spaces and non-digits)
        $inputPhone = preg_replace('/[^0-9]/', '', $request->phone);

        $reservation = Reservation::with('patient')
            ->where('id', $numericID)
            ->whereHas('patient', function ($q) use ($inputPhone) {
                // Check against normalized phone
                $q->where('phone', $inputPhone)
                  ->orWhereRaw("REPLACE(phone, ' ', '') = ?", [$inputPhone]);
            })
            ->first();

        if (!$reservation) {
            return back()->withErrors(['access' => __('messages.invalid_patient_credentials')])->withInput();
        }

        Session::put('patient_reservation_id', $reservation->id);
        Session::put('patient_phone', $reservation->patient->phone); // Store the canonical phone

        return redirect()->route('patient.dashboard');
    }

    /**
     * Display the patient dashboard.
     */
    public function dashboard()
    {
        $patient = Auth::guard('patient')->user();
        
        if (!$patient) {
            return redirect()->route('access');
        }

        // Fetch all reservations for this patient, ordered by date
        $reservations = Reservation::with(['doctor', 'reservationAnalyses.analyse', 'reminders'])
            ->where('patient_id', $patient->id)
            ->orderBy('analysis_date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        if ($reservations->isEmpty()) {
            return view('patient-portal.dashboard', [
                'patient' => $patient,
                'reservations' => $reservations,
                'reservation' => null,
                'hasDbResults' => false,
                'diagIsChecked' => false
            ]);
        }

        // Handle specific visit selection
        $selectedId = request('visit');
        $reservation = $reservations->first(); // Default to latest

        if ($selectedId) {
            $reservation = $reservations->firstWhere('id', $selectedId) ?: $reservation;
        }

        $hasDbResults = false;
        $diagIsChecked = false;
        if (!is_null($reservation)) {
            $hasDbResults = $reservation->reservationAnalyses->contains(fn($ra) => !empty($ra->result_value));
            $diagIsChecked = $reservation->reservationAnalyses->pluck('status')->contains(fn($s) => in_array($s, ['ready', 'blocked', 'warning']));
        }

        return view('patient-portal.dashboard', compact('patient', 'reservations', 'reservation', 'hasDbResults', 'diagIsChecked'));
    }

    /**
     * Logout from the patient portal.
     */
    public function logout()
    {
        Auth::guard('patient')->logout();
        return redirect()->route('access');
    }

    /**
     * Download the result file securely.
     */
    public function downloadResult($id)
    {
        $patient = Auth::guard('patient')->user();
        
        if (!$patient) {
            abort(401);
        }

        $reservation = Reservation::where('id', $id)
            ->where('patient_id', $patient->id) // Ownership check
            ->firstOrFail();

        if (!$reservation->result_file_path || !Storage::disk('public')->exists($reservation->result_file_path)) {
            return back()->with('error', __('messages.result_not_found'));
        }

        return Storage::disk('public')->response($reservation->result_file_path);
    }
}
