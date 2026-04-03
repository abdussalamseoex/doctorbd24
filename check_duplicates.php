<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$doctorCount = \App\Models\Doctor::select('name', \DB::raw('count(*) as count'))
    ->groupBy('name')
    ->havingRaw('COUNT(*) > 1')
    ->get()
    ->count();

$hospitalCount = \App\Models\Hospital::select('name', \DB::raw('count(*) as count'))
    ->groupBy('name')
    ->havingRaw('COUNT(*) > 1')
    ->get()
    ->count();

echo "Doctors with duplicate names: $doctorCount\n";
echo "Hospitals with duplicate names: $hospitalCount\n";
