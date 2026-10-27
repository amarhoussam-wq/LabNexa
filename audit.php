<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use App\Models\Patient;
use App\Models\Reservation;
use Carbon\Carbon;

header('Content-Type: text/plain; charset=UTF-8');

echo "--- PATIENT AUDIT ---\n";
$patients = Patient::all();
foreach ($patients as $p) {
    $hasPassword = $p->password ? "YES" : "NO";
    $resCount = $p->reservations()->count();
    echo "ID: {$p->id} | Name: {$p->name} | Email: {$p->email} | Phone: {$p->phone} | HasPass: {$hasPassword} | Res: {$resCount}\n";
}

echo "\n--- PENDING REMINDERS AUDIT ---\n";
$now = Carbon::now('Africa/Algiers');
echo "Current Server Time (Algeria): " . $now->toDateTimeString() . "\n";

$reservations = Reservation::with('patient')
    ->where('status', 'booked')
    ->get();

foreach ($reservations as $r) {
    $appointmentDateTime = Carbon::parse($r->analysis_date->toDateString() . ' ' . ($r->time ?? '00:00'), 'Africa/Algiers');
    $hoursRemaining = $now->floatDiffInHours($appointmentDateTime, false);
    $hasSentReminder = $r->reminders()->where('is_sent', true)->exists();
    
    echo "ResID: {$r->id} | Patient: " . ($r->patient->name ?? 'N/A') . " | Date: {$r->analysis_date} | Time: {$r->time} | HoursLeft: " . round($hoursRemaining, 2) . " | Sent: " . ($hasSentReminder ? 'YES' : 'NO') . "\n";
}
