<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use App\Models\Request_reservation;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

echo "--- DATA INTEGRITY REPORT ---\n";
echo "Total Patients: " . Patient::count() . "\n";
echo "Total Requests: " . Request_reservation::count() . "\n";
echo "Total Reservations: " . Reservation::count() . "\n\n";

echo "--- DUPLICATE EMAILS ---\n";
$dupEmails = Patient::select('email', DB::raw('count(*) as count'))
    ->whereNotNull('email')
    ->groupBy('email')
    ->having('count', '>', 1)
    ->get();
foreach ($dupEmails as $dup) {
    echo "Email: {$dup->email} | Count: {$dup->count}\n";
    $patients = Patient::where('email', $dup->email)->get();
    foreach ($patients as $p) {
        echo "  - ID: {$p->id} | Name: {$p->name} | Phone: {$p->phone}\n";
    }
}

echo "\n--- DUPLICATE PHONES ---\n";
$dupPhones = Patient::select('phone', DB::raw('count(*) as count'))
    ->whereNotNull('phone')
    ->groupBy('phone')
    ->having('count', '>', 1)
    ->get();
foreach ($dupPhones as $dup) {
    echo "Phone: {$dup->phone} | Count: {$dup->count}\n";
    $patients = Patient::where('phone', $dup->phone)->get();
    foreach ($patients as $p) {
        echo "  - ID: {$p->id} | Name: {$p->name} | Email: {$p->email}\n";
    }
}

echo "\n--- ORPHANED RESERVATIONS ---\n";
$orphans = Reservation::whereDoesntHave('patient')->get();
echo "Count: " . $orphans->count() . "\n";

echo "\n--- MISMATCHED REQUESTS ---\n";
// Requests where the patient_id in Request_reservation doesn't match the patient_id in the linked Reservation
$mismatches = DB::table('request_reservations')
    ->join('reservations', 'request_reservations.reservation_id', '=', 'reservations.id')
    ->whereRaw('request_reservations.patient_id != reservations.patient_id')
    ->select('request_reservations.id as request_id', 'request_reservations.patient_id as req_p_id', 'reservations.patient_id as res_p_id')
    ->get();
echo "Count: " . $mismatches->count() . "\n";
foreach ($mismatches as $m) {
    echo "  - Req ID: {$m->request_id} | Req Pat ID: {$m->req_p_id} | Res Pat ID: {$m->res_p_id}\n";
}
