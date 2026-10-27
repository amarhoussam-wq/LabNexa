<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Reservation;
use App\Models\History;
use App\Models\Reminder;
use App\Models\Request_reservation;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PatientsController extends Controller
{
    /**
     * Display all patients with password status for the mobile app.
     */
    public function index(Request $request)
    {
        $query = Patient::withCount('reservations');

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $patients = $query->latest()->paginate(20);

        return view('Adminstration.patients', compact('patients'));
    }

    /**
     * Update/set the patient's mobile app password.
     */
    public function setPassword(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required'     => __('messages.email_required'),
            'password.required'  => __('messages.password_required'),
            'password.min'       => __('messages.password_min'),
            'password.confirmed' => __('messages.password_confirmed'),
        ]);

        $patient = Patient::findOrFail($id);
        $patient->update([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Optional notification email (only if it's a valid email address)
        $mailFailed = false;
        $mailWasSent = false;
        
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::send('emails.patient-credentials', [
                    'patient' => $patient,
                    'email' => $request->email,
                    'password' => $request->password, // Added this
                ], function ($message) use ($patient) {
                    $message->to($patient->email)
                            ->subject(__('messages.credentials_email_subject'));
                });
                $mailWasSent = true;
            } catch (\Exception $e) {
                $mailFailed = true;
                Log::error('Failed to send credential email: ' . $e->getMessage());
            }
        }

        $mailInfo = '';
        if ($mailWasSent) {
            $mailInfo = __('messages.email_sent_notice');
        } elseif ($mailFailed) {
            $mailInfo = __('messages.email_failed_notice');
        } else {
            // Case where its a username, not an email
            $mailInfo = ''; // No notice needed, or could say "Username saved"
        }

        return back()->with(
            'success',
            __('messages.credentials_updated_success', ['name' => $patient->name, 'mailInfo' => $mailInfo])
        );
    }

    /**
     * Update patient profile basic info (Email/Phone).
     */
    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|string',
            'phone' => 'required|string',
        ]);

        $patient = Patient::findOrFail($id);
        $patient->update($request->only(['email', 'phone']));

        return back()->with('success', __('messages.profile_updated_success'));
    }

    /**
     * Remove the patient's mobile app password (revoke app access).
     */
    public function revokePassword($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->update(['password' => null]);

        return back()->with('success', __('messages.access_revoked_success', ['name' => $patient->name]));
    }

    /**
     * Merge a source patient into a target patient.
     */
    public function mergePatients(Request $request)
    {
        $request->validate([
            'source_id' => 'required|exists:patients,id',
            'target_id' => 'required|exists:patients,id|different:source_id',
        ]);

        $sourceId = $request->source_id;
        $targetId = $request->target_id;

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $source = Patient::findOrFail($sourceId);
            $target = Patient::findOrFail($targetId);

            // Move all related data
            Reservation::where('patient_id', $sourceId)->update(['patient_id' => $targetId]);
            History::where('patient_id', $sourceId)->update(['patient_id' => $targetId]);
            Reminder::where('patient_id', $sourceId)->update(['patient_id' => $targetId]);
            Request_reservation::where('patient_id', $sourceId)->update(['patient_id' => $targetId]);
            \Illuminate\Support\Facades\DB::table('patient_answers')->where('patient_id', $sourceId)->update(['patient_id' => $targetId]);

            // Copy missing fields to target if available in source
            $target->update(array_filter([
                'email' => $target->email ?: $source->email,
                'phone' => $target->phone ?: $source->phone,
                'gender' => $target->gender ?: $source->gender,
                'birth_date' => $target->birth_date ?: $source->birth_date,
            ]));

            $source->delete();

            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', __('messages.merge_success'));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', __('messages.merge_error') . $e->getMessage());
        }
    }
}
