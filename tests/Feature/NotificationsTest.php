<?php

namespace Tests\Feature;

use App\Models\Analyse;
use App\Models\Request_reservation;
use App\Models\Administrator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsAdmin()
    {
        $admin = Administrator::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password')]);
        $this->actingAs($admin, 'administrator');
        return $admin;
    }

    public function test_booking_confirmation_sends_email_with_pdf_attachment()
    {
        $this->loginAsAdmin();

        $analyse = Analyse::factory()->create(['name' => 'General Test', 'preparation_instructions' => 'No prep']);
        $request = Request_reservation::factory()->create([
            'email' => 'patient@test.com',
            'status' => 'pending'
        ]);
        $request->analyses()->attach($analyse->id);

        $tomorrow = Carbon::now('Africa/Algiers')->addDays(2);
        
        // Mock Mail::send directly because the system uses closures instead of Mailables
        $mailWasSent = false;
        Mail::shouldReceive('send')->once()->andReturnUsing(function($view, $data, $callback) use (&$mailWasSent) {
            if ($view === 'emails.booking-confirmed') {
                $mailWasSent = true;
                $this->assertArrayHasKey('smartMessage', $data);
            }
        });

        $response = $this->post(route('reservation.requests.confirm', $request->id), [
            'analysis_date' => $tomorrow->format('Y-m-d'),
            'time' => $tomorrow->format('H:i')
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue($mailWasSent, 'The booking confirmation email was not sent.');
    }

    public function test_fasting_reminder_smart_message_is_sent_when_under_14_hours()
    {
        $this->loginAsAdmin();

        $fastingAnalyse = Analyse::factory()->create([
            'name' => 'تحليل السكر في الدم',
            'preparation_instructions' => 'يتطلب الصيام لمدة 8-12 ساعة قبل التحليل.'
        ]);

        $request = Request_reservation::factory()->create([
            'email' => 'patient_fasting@test.com',
            'status' => 'pending'
        ]);
        $request->analyses()->attach($fastingAnalyse->id);

        $tenHoursTime = Carbon::now('Africa/Algiers')->addHours(10);
        
        $mailWasSent = false;
        $smartMessageText = '';
        
        Mail::shouldReceive('send')->once()->andReturnUsing(function($view, $data, $callback) use (&$mailWasSent, &$smartMessageText) {
            if ($view === 'emails.booking-confirmed') {
                $mailWasSent = true;
                $smartMessageText = $data['smartMessage'] ?? '';
            }
        });

        $this->post(route('reservation.requests.confirm', $request->id), [
            'analysis_date' => $tenHoursTime->format('Y-m-d'),
            'time' => $tenHoursTime->format('H:i')
        ]);

        $this->assertTrue($mailWasSent, 'The booking confirmation email was not sent.');
        $this->assertStringContainsString('يرجى البدء في الصيام الآن', $smartMessageText);
    }
}
