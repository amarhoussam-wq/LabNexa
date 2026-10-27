<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Patient;
use Illuminate\Support\Facades\Hash;

$email = $argv[1] ?? null;
$password = $argv[2] ?? null;

if (!$email || !$password) {
    echo "Usage: php test_login.php <email> <password>\n";
    exit(1);
}

echo "Testing login for: {$email}\n";

$patients = Patient::where('email', $email)->get();
echo "Found " . $patients->count() . " records.\n";

foreach ($patients as $p) {
    echo "ID: {$p->id} | Name: {$p->name} | Phone: {$p->phone}\n";
    if (!$p->password) {
        echo " - NO PASSWORD SET\n";
    } else {
        $check = Hash::check($password, $p->password);
        echo " - Password check: " . ($check ? "✅ VALID" : "❌ INVALID") . "\n";
        echo " - Hash: " . substr($p->password, 0, 15) . "...\n";
    }
}
