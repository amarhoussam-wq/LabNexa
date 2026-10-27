<?php

namespace Tests\Feature;

use App\Models\Analyse;
use App\Models\Patient;
use App\Models\Request_reservation;
use App\Models\Administrator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ClinicalRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable Mail sending during tests
        Mail::fake();
    }

    private function loginAsAdmin()
    {
        $admin = Administrator::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password')]);
        $this->actingAs($admin, 'administrator');
        return $admin;
    }

    public function test_reject_fasting_test_booked_too_close()
    {
        $this->loginAsAdmin();

        // Create a fasting analysis
        $fastingAnalyse = Analyse::factory()->create([
            'name' => 'تحليل السكر في الدم', // Known fasting test in reservationsController
            'preparation_instructions' => 'يتطلب الصيام لمدة 8-12 ساعة قبل التحليل.'
        ]);

        // Create a pending request
        $request = Request_reservation::factory()->create(['status' => 'pending']);
        $request->analyses()->attach($fastingAnalyse->id);

        // Try to confirm it for 2 hours from now
        $twoHoursFromNow = Carbon::now('Africa/Algiers')->addHours(2);
        
        $response = $this->post(route('reservation.requests.confirm', $request->id), [
            'analysis_date' => $twoHoursFromNow->format('Y-m-d'),
            'time' => $twoHoursFromNow->format('H:i')
        ]);

        // Should be rejected automatically
        $response->assertSessionHas('error', 'تم رفض الطلب تلقائياً لعدم كفاية الوقت للصيام (أقل من 8 ساعات).');
        
        $this->assertEquals('rejected', $request->fresh()->status);
    }

    public function test_accept_fasting_test_booked_with_enough_time()
    {
        $this->loginAsAdmin();

        $fastingAnalyse = Analyse::factory()->create([
            'name' => 'تحليل السكر في الدم',
            'preparation_instructions' => 'يتطلب الصيام لمدة 8-12 ساعة قبل التحليل.'
        ]);

        $request = Request_reservation::factory()->create(['status' => 'pending']);
        $request->analyses()->attach($fastingAnalyse->id);

        // Confirm it for tomorrow (24h from now)
        $tomorrow = Carbon::now('Africa/Algiers')->addHours(24);
        
        $response = $this->post(route('reservation.requests.confirm', $request->id), [
            'analysis_date' => $tomorrow->format('Y-m-d'),
            'time' => $tomorrow->format('H:i')
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('confirmed', $request->fresh()->status);
    }

    public function test_reject_morning_only_test_booked_late()
    {
        $this->loginAsAdmin();

        // Morning test based on the actual config rules if accessible, else mock one
        // For testing we will simulate the conditions found in reservationsController
        // Assuming 'Testosteron' or similar is in config('clinical_rules.morning_only')
        // We'll just define the config in the test to ensure it hits the rule.
        Config::set('clinical_rules.morning_only', ['تحليل هرمون الكورتيزول']);
        
        $morningAnalyse = Analyse::factory()->create([
            'name' => 'تحليل هرمون الكورتيزول',
        ]);

        $request = Request_reservation::factory()->create(['status' => 'pending']);
        $request->analyses()->attach($morningAnalyse->id);

        $tomorrowAfternoon = Carbon::now('Africa/Algiers')->addDays(1);
        
        $response = $this->post(route('reservation.requests.confirm', $request->id), [
            'analysis_date' => $tomorrowAfternoon->format('Y-m-d'),
            'time' => '11:00' // After 10:30
        ]);

        $response->assertSessionHas('error', 'تم رفض الطلب تلقائياً: التحاليل الهرمونية تتطلب سحب الدم قبل 10:30 صباحاً.');
        $this->assertEquals('rejected', $request->fresh()->status);
    }
}
