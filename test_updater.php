<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$docs = App\Models\Doctor::orderBy('id', 'desc')->take(20)->get();
foreach($docs as $d) {
    echo $d->id . ' - ' . $d->name . ' - ' . $d->created_at . "\n";
}
