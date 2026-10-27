<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Patient;

$patients = Patient::all(['id', 'name', 'email', 'phone', 'password']);
foreach ($patients as $p) {
    $hasPass = $p->password ? 'YES (' . substr($p->password, 0, 10) . '...)' : 'NO';
    echo "ID: {$p->id} | Name: {$p->name} | Email: {$p->email} | Pass: {$hasPass}\n";
}
