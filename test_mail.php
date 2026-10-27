<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Attempting to send test email...\n";
    Mail::raw('This is a test email to verify SMTP settings after hosting update.', function ($message) {
        $message->to('houssam.amar.grp3@gmail.com')
                ->subject('SMTP Verification Test');
    });
    echo "SUCCESS: Email sent successfully!\n";
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
    Log::error("Manual Mail Test Failed: " . $e->getMessage());
}
