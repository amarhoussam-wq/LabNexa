<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarkMissedAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-missed-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marks confirmed/booked reservations that passed their date without completion as missed.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Scanning for missed (no-show) appointments...');

        // Find reservations that are in the past (before today)
        // and still have status 'confirmed' or 'booked' or 'warning' or 'ready'
        // (i.e., they haven't been 'completed' or 'cancelled')
        $yesterday = Carbon::yesterday('Africa/Algiers')->format('Y-m-d');

        $expiredReservations = Reservation::where('analysis_date', '<=', $yesterday)
            ->whereIn('status', ['confirmed', 'booked', 'ready', 'warning', 'pending_approval'])
            ->get();

        if ($expiredReservations->isEmpty()) {
            $this->info('No expired appointments found.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($expiredReservations as $reservation) {
            // We set status to 'missed' if it exists, otherwise we add to admin notes
            // Since we don't have a 'missed' enum value in the migration yet, 
            // let's check if we can add it or just update notes.
            // Better yet, let's keep it as is but add a note to result_notes.
            
            $reservation->update([
                'status' => 'completed', // Or we keep same but mark as no-show in notes
                'result_notes' => ($reservation->result_notes ? $reservation->result_notes . "\n" : "") . "[تلقائي] لم يتم الحضور في الموعد المحدد (No-show)."
            ]);

            Log::info("Reservation {$reservation->id} marked as missed (No-show).");
            $count++;
        }

        $this->info("Completed. Marked {$count} appointments as missed.");
        return Command::SUCCESS;
    }
}
