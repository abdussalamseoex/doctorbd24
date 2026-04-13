<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Hospital 1 Logo: " . \App\Models\Hospital::first()->logo . "\n";
echo "Hospital 1 Gallery: " . json_encode(\App\Models\Hospital::first()->gallery) . "\n";
