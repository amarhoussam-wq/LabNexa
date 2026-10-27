<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Request_reservation;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class Labo_dzController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $analyses = Analyse::all(); // Or use your preferred method to get the data

        return view('Labo_dz', ['analyses' => $analyses]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function booking(Request $request): RedirectResponse
    {
        // Validate the request with strict rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^(05|06|07)[0-9]{8}$/'],
            'email' => 'required|email',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'analysisTypes' => 'required_without:prescription|array',
            'analysisTypes.*' => 'exists:analyses,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'prescription' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'name.required' => __('messages.name_required'),
            'phone.required' => __('messages.phone_required'),
            'phone.regex' => __('messages.invalid_phone_format'),
            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.invalid_email_format'),
            'gender.required' => __('messages.gender_required'),
            'birth_date.required' => __('messages.birth_date_required'),
            'date.required' => __('messages.date_required'),
            'date.after_or_equal' => __('messages.invalid_date_past'),
            'time.required' => __('messages.time_required'),
        ]);
        if ($validator->fails()) {
            Log::warning('Booking validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->except(['prescription', 'password'])
            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // --- Lead Time Check (14 Hours) ---
        $appointmentDateTime = \Carbon\Carbon::parse($request->date . ' ' . $request->time, 'Africa/Algiers');
        $now = \Carbon\Carbon::now('Africa/Algiers');
        $minLeadTime = $now->copy()->addHours(14);
        
        if ($appointmentDateTime->isBefore($minLeadTime)) {
            Log::info('Booking rejected: Lead time too short', [
                'appointment' => $appointmentDateTime->toDateTimeString(),
                'now' => $now->toDateTimeString(),
                'min_required' => $minLeadTime->toDateTimeString()
            ]);
            return redirect()->back()->with('error', __('messages.booking_too_soon'))->withInput();
        }

        // --- Capacity Check ---
        $requestedDate = $request->date;
        $dayOfWeek = strtolower(\Carbon\Carbon::parse($requestedDate)->format('l'));
        $settingKey = "booking_limit_{$dayOfWeek}";

        $dailyLimit = Setting::where('key', $settingKey)->value('value') ?? 50;

        $currentBookingsCount = Request_reservation::whereDate('preferred_date', $requestedDate)
            ->where('status', '!=', 'rejected')
            ->count();

        if ($currentBookingsCount >= $dailyLimit) {
            Log::info('Booking rejected: Capacity reached', [
                'date' => $requestedDate,
                'limit' => $dailyLimit,
                'current' => $currentBookingsCount
            ]);
            return redirect()->back()->with('error', __('messages.booking_limit_reached'))->withInput();
        }
        // ----------------------

        try {
            Log::info('Booking attempt started', ['request' => $request->except(['prescription', 'password'])]);

            // Handle prescription upload
            $prescriptionPath = null;
            if ($request->hasFile('prescription')) {
                $file = $request->file('prescription');

                // Sanitize filename to prevent issues with special characters or long names
                $originalName = $file->getClientOriginalName();
                $sanitizedName = preg_replace('/[^A-Za-z0-9\._-]/', '_', $originalName);
                if (empty(pathinfo($sanitizedName, PATHINFO_FILENAME))) {
                    $sanitizedName = 'prescription_' . time() . '.' . ($file->getClientOriginalExtension() ?: 'jpg');
                }

                $filename = time() . '_' . $sanitizedName;

                Log::info('Storing prescription', ['original' => $originalName, 'sanitized' => $filename]);
                $prescriptionPath = $file->storeAs('prescriptions', $filename, 'public');
            }

            // Get first analysis ID if any
            $analysisTypes = $request->analysisTypes;
            $mainAnalyseId = (is_array($analysisTypes) && count($analysisTypes) > 0) ? $analysisTypes[0] : null;

            // Create reservation request with patient info
            $requestReservation = Request_reservation::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'analyse_id' => $mainAnalyseId,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'preferred_date' => $request->date,
                'preferred_time' => $request->time,
                'prescription_path' => $prescriptionPath,
                'status' => 'pending',
            ]);

            Log::info('Reservation record created', ['id' => $requestReservation->id]);

            // Attach multiple analyses if present
            if (is_array($analysisTypes) && count($analysisTypes) > 0) {
                $requestReservation->analyses()->attach($analysisTypes);
            Log::info('Analyses attached', ['count' => count($analysisTypes)]);
            }

            // Redirect to dedicated success page with auto-download trigger
            return redirect()->route('booking.success', ['id' => $requestReservation->id])
                ->with('download_pdf', true)
                ->with('download_res_id', $requestReservation->id);
        }
        catch (\Exception $e) {
            Log::error('Booking error full trace:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['prescription', 'password'])
            ]);

            // If it's a QueryException, it's likely a database schema issue
            if ($e instanceof \Illuminate\Database\QueryException) {
                Log::error('Database Query Error:', [
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings(),
                    'error_info' => $e->errorInfo
                ]);
            }

            return redirect()->back()->with('error', __('messages.booking_error') . $e->getMessage());
        }
    }

    public function message(Request $request): RedirectResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Message::create([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
            ]);

            return redirect()->back()->with('success', __('messages.message_success'));
        }
        catch (\Exception $e) {
            Log::error('Message sending error:', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', __('messages.message_error'));
        }
    }

    public function analysisInfo(): View
    {
        $analyses = Analyse::all();

        return view('analysis-info', compact('analyses'));
    }
}
