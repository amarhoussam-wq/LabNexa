<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Reservation;
use App\Models\Analyse;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CronRobustnessTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_preparation_reminders_is_idempotent()
    {
        // 1. Setup a reservation 12 hours from now
        $patient = Patient::factory()->create(['email' => 'robust@test.com']);
        $analyse = Analyse::factory()->create(['name' => 'تحليل السكر في الدم']); // Fasting test
        
        $twelveHoursFromNow = Carbon::now('Africa/Algiers')->addHours(12);
        
        $reservation = Reservation::factory()->create([
            'patient_id' => $patient->id,
            'status' => 'confirmed',
            'analysis_date' => $twelveHoursFromNow->format('Y-m-d'),
            'time' => $twelveHoursFromNow->format('H:i:s'),
        ]);
        $reservation->analyses()->attach($analyse->id);

        // 2. Run the command first time
        $mailSentCount = 0;
        Mail::shouldReceive('send')->times(1)->andReturnUsing(function($view, $data, $callback) use (&$mailSentCount) {
            $mailSentCount++;
        });

        $this->artisan('app:send-prep-reminders')
             ->expectsOutput('Starting 12-hour preparation reminder email scan...')
             ->assertExitCode(0);

        $this->assertEquals(1, $mailSentCount);
        $this->assertDatabaseHas('reminders', [
            'reservation_id' => $reservation->id,
            'is_sent' => true
        ]);

        // 3. Run the command second time (should skip)
        // Note: shouldReceive('send')->times(1) above already covers that it only happened once 
        // across the execution if we don't reset. But to be safe, we redeclare expectation as 0.
        Mail::shouldReceive('send')->never();

        $this->artisan('app:send-prep-reminders')
             ->expectsOutput('Completed. Sent 0 reminders. Skipped 1 already sent.')
             ->assertExitCode(0);
    }

    public function test_mark_missed_appointments_works_correctly()
    {
        // 1. Setup a reservation from yesterday
        $patient = Patient::factory()->create();
        $reservation = Reservation::factory()->create([
            'patient_id' => $patient->id,
            'status' => 'confirmed',
            'analysis_date' => Carbon::yesterday('Africa/Algiers')->format('Y-m-d'),
        ]);

        // 2. Run command
        $this->artisan('app:mark-missed-appointments')
             ->expectsOutput('Scanning for missed (no-show) appointments...')
             ->assertExitCode(0);

        // 3. Verify status update and notes
        $reservation->refresh();
        $this->assertStringContainsString('لم يتم الحضور', $reservation->result_notes);
    }
}
