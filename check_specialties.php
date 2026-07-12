<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Specialty;

$specialties = Specialty::all();
foreach($specialties as $s) {
    echo $s->slug . " - " . $s->name . "\n";
}
