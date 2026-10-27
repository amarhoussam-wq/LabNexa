<?php

use App\Models\Analyse;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$count = 0;
Analyse::all()->each(function ($analyse) use (&$count) {
    if (str_contains($analyse->preparation_instructions, 'صيام') || 
        str_contains($analyse->preparation_instructions, 'fasting') ||
        str_contains(strtolower($analyse->name), 'fasting') ||
        str_contains(strtolower($analyse->name_fr), 'jeûne')) {
        $analyse->update(['requires_fasting' => true]);
        $count++;
    }
});

echo "Updated $count analyses to require fasting.\n";
