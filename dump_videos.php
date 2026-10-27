<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$analyses = App\Models\Analyse::whereNotNull('preparation_video')->get(['id', 'name', 'preparation_video']);
foreach ($analyses as $a) {
    echo "ID: {$a->id}, Name: {$a->name}, Video: {$a->preparation_video}\n";
}
