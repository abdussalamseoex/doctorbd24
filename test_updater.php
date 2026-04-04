<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Doctors: " . App\Models\Doctor::where('import_source', 'popular_diagnostic')->count() . "\n";
echo "Specialties: " . App\Models\Specialty::count() . "\n";
