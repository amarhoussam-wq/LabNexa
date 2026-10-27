<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendPreparationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-prep-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends preparation reminders to patients exactly 12 hours before their analysis appointment.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting 12-hour preparation reminder email scan...');

        // 1. Calculate the target time window (Around Now + 12 Hours)
        // We use a slightly wider window (2 hours) to ensure we don't miss anything if the scheduler runs slightly late
        $windowStart = Carbon::now('Africa/Algiers')->addHours(11);
        $windowEnd = Carbon::now('Africa/Algiers')->addHours(13);

        $targetDateStart = $windowStart->toDateString();
        $targetDateEnd = $windowEnd->toDateString();

        // 2. Fetch confirmed reservations for that time window
        $reservations = Reservation::with(['patient', 'analyses'])
            ->whereIn('status', ['confirmed', 'booked'])
            ->whereBetween('analysis_date', [$targetDateStart, $targetDateEnd])
            ->get()
            ->filter(function($reservation) use ($windowStart, $windowEnd) {
                $dateStr = ($reservation->analysis_date instanceof Carbon) 
                    ? $reservation->analysis_date->toDateString() 
                    : $reservation->analysis_date;
                $appointmentTime = Carbon::parse($dateStr . ' ' . ($reservation->time ?? '00:00'), 'Africa/Algiers');
                return $appointmentTime->between($windowStart, $windowEnd);
            });

        if ($reservations->isEmpty()) {
            $this->info('No appointments found matching the 12-hour window.');
            return Command::SUCCESS;
        }

        // Convert lists to lowercase for case-insensitive matching
        $fastingTestsList = array_map('mb_strtolower', config('clinical_rules.fasting', []));
        $urineCultureTestsList = array_map('mb_strtolower', config('clinical_rules.urine_culture', []));

        $sentCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        /** @var Reservation $reservation */
        foreach ($reservations as $reservation) {
            $patientEmail = $reservation->patient?->email;
            if (!$patientEmail) {
                continue;
            }

            // Check if we already sent a 12h reminder for this reservation
            $existingReminder = \App\Models\Reminder::where('reservation_id', $reservation->id)
                ->where('message', 'like', '%تذكير بموعد التحليل%')
                ->where('is_sent', true)
                ->first();

            if ($existingReminder) {
                $skippedCount++;
                continue;
            }

            // 3. Build the dynamic Preparation Instructions
            $smartMessage = [];
            $analyses = $reservation->analyses;

            $requiresFasting = $analyses->contains(function($a) use ($fastingTestsList) {
                $nameLower = mb_strtolower($a->name);
                return in_array($nameLower, $fastingTestsList) || str_contains($a->preparation_instructions, 'صيام');
            });

            $requiresUrine = $analyses->contains(function($a) use ($urineCultureTestsList) {
                $nameLower = mb_strtolower($a->name);
                return in_array($nameLower, $urineCultureTestsList) || str_contains($nameLower, 'ecbu') || str_contains($nameLower, 'بول');
            });

            if ($requiresFasting) {
                $smartMessage[] = "⚠️ الصيام: يرجى التوقف عن تناول الطعام والشراب (باستثناء الماء) لمدة 8 إلى 12 ساعة قبل موعدكم المذكور أدناه.";
            }
            if ($requiresUrine) {
                $smartMessage[] = "⚠️ تحضير البول: يرجى إحضار أول بول في الصباح، مع ضرورة غسل المنطقة جيداً بالماء والصابون، وجمع العينة من منتصف مجرى البول.";
            }
            if (empty($smartMessage)) {
                $smartMessage[] = "لا توجد تحضيرات مسبقة معقدة لتحاليلكم. يرجى الحضور في الموعد المحدد.";
            }

            // 4. Send the Email
            try {
                Mail::send('emails.booking-reminder', [
                    'patientName' => $reservation->patient?->name ?? 'Patient',
                    'analysisDate' => Carbon::parse($reservation->analysis_date)->format('d/m/Y'),
                    'analysisTime' => date('H:i', strtotime($reservation->time)),
                    'smartMessage' => $smartMessage
                ], function ($message) use ($patientEmail) {
                    $message->to($patientEmail)
                            ->subject('📍 تذكير بموعد التحليل وتعليمات التحضير - LabNexa');
                });

                // Create or update reminder record
                \App\Models\Reminder::create([
                    'reservation_id' => $reservation->id,
                    'patient_id' => $reservation->patient_id,
                    'message' => 'تذكير بموعد التحليل (12 ساعة قبل الموعد)',
                    'scheduled_for' => Carbon::now('Africa/Algiers'),
                    'sent_at' => Carbon::now('Africa/Algiers'),
                    'is_sent' => true,
                ]);

                Log::info("12hr Reminder sent to {$patientEmail} for reservation {$reservation->id}");
                $sentCount++;
                $this->info("✓ Reminder sent to: {$patientEmail}");

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("✗ Failed to send to: {$patientEmail}. Error: " . $e->getMessage());
                \App\Models\Reminder::create([
                    'reservation_id' => $reservation->id,
                    'patient_id' => $reservation->patient_id,
                    'message' => 'تذكير بموعد التحليل (12 ساعة قبل الموعد)',
                    'scheduled_for' => Carbon::now('Africa/Algiers'),
                    'is_sent' => false,
                    'error_message' => $e->getMessage(),
                ]);
                Log::error("Failed to send 12hr reminder to {$patientEmail}: " . $e->getMessage());
            }
        }

        $this->info("Completed. Sent {$sentCount} reminders. Skipped {$skippedCount} already sent. Errors: {$errorCount}");
        return Command::SUCCESS;
    }
}
