<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Get the current daily limits or default to 50
        $limits = [
            'booking_limit_sunday' => Setting::where('key', 'booking_limit_sunday')->value('value') ?? 50,
            'booking_limit_monday' => Setting::where('key', 'booking_limit_monday')->value('value') ?? 50,
            'booking_limit_tuesday' => Setting::where('key', 'booking_limit_tuesday')->value('value') ?? 50,
            'booking_limit_wednesday' => Setting::where('key', 'booking_limit_wednesday')->value('value') ?? 50,
            'booking_limit_thursday' => Setting::where('key', 'booking_limit_thursday')->value('value') ?? 50,
            'booking_limit_friday' => Setting::where('key', 'booking_limit_friday')->value('value') ?? 50,
            'booking_limit_saturday' => Setting::where('key', 'booking_limit_saturday')->value('value') ?? 50,
        ];

        return view('Adminstration.settings', compact('limits'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'booking_limit_sunday' => 'required|integer|min:0',
            'booking_limit_monday' => 'required|integer|min:0',
            'booking_limit_tuesday' => 'required|integer|min:0',
            'booking_limit_wednesday' => 'required|integer|min:0',
            'booking_limit_thursday' => 'required|integer|min:0',
            'booking_limit_friday' => 'required|integer|min:0',
            'booking_limit_saturday' => 'required|integer|min:0',
        ]);

        $days = [
            'booking_limit_sunday', 'booking_limit_monday', 'booking_limit_tuesday',
            'booking_limit_wednesday', 'booking_limit_thursday', 'booking_limit_friday',
            'booking_limit_saturday'
        ];

        foreach ($days as $day) {
            Setting::updateOrCreate(
                ['key' => $day],
                ['value' => $request->$day]
            );
        }

        return redirect()->route('admin.settings')->with('success', __('messages.settings_updated_success'));
    }
}
