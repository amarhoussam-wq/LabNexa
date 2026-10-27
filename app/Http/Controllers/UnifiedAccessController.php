<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UnifiedAccessController extends Controller
{
    /**
     * Show the unified login page.
     */
    public function show()
    {
        // If already logged in as doctor
        if (Auth::guard('doctor')->check()) {
            return redirect()->route('doctor.dashboard');
        }

        // If already logged in as patient
        if (Auth::guard('patient')->check()) {
            return redirect()->route('patient.dashboard');
        }

        return view('auth.unified-login');
    }

    /**
     * Handle authentication for both doctors and patients.
     */
    public function login(Request $request)
    {
        $type = $request->get('auth_type', 'patient');

        if ($type === 'doctor') {
            return $this->handleDoctorAuth($request);
        }

        return $this->handlePatientAuth($request);
    }

    /**
     * Doctor Authentication logic.
     */
    protected function handleDoctorAuth(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::guard('doctor')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('doctor.dashboard'));
        }

        return back()->withErrors([
            'doctor_auth' => __('auth.failed'),
        ])->withInput($request->only('email'));
    }

    /**
     * Patient Access logic (Aligned with Mobile App).
     */
    protected function handlePatientAuth(Request $request)
    {
        $request->validate([
            'email_or_phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginKey = trim($request->email_or_phone);
        $password = $request->password;

        // Find patient by name OR email OR phone
        $patient = Patient::where(function($q) use ($loginKey) {
            $q->where('name', $loginKey)
              ->orWhere('email', $loginKey)
              ->orWhere('phone', $loginKey);
            
            // Flexible phone matching (fallback)
            if (is_numeric(str_replace([' ', '-', '+'], '', $loginKey))) {
                $cleanKey = str_replace([' ', '-', '+'], '', $loginKey);
                $q->orWhere('phone', 'like', '%' . $cleanKey . '%');
            }
        })->first();

        if (!$patient || !$patient->password || !\Illuminate\Support\Facades\Hash::check($password, $patient->password)) {
            return back()->withErrors(['patient_auth' => __('auth.failed')])->withInput();
        }

        // Log them in using the 'patient' guard
        Auth::guard('patient')->login($patient, $request->remember);

        $request->session()->regenerate();

        return redirect()->route('patient.dashboard');
    }
}
