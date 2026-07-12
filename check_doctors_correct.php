<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Doctor;
use App\Models\Area;
use App\Models\District;

$area = Area::where('slug', 'dhaka-district-dhanmondi')->first();
if ($area) {
    echo "Dhanmondi Area ID: " . $area->id . "\n";
    $doctorsInDhanmondi = Doctor::whereHas('chambers.area', function($q) use ($area) {
        $q->where('slug', $area->slug);
    })->count();
    echo "Doctors in Dhanmondi via chamber area: $doctorsInDhanmondi\n";
} else {
    echo "Area Dhanmondi not found by slug.\n";
}

$mirpur = Area::where('slug', 'dhaka-district-mirpur')->first();
if ($mirpur) {
    echo "Mirpur Area ID: " . $mirpur->id . "\n";
    $doctorsInMirpur = Doctor::whereHas('chambers.area', function($q) use ($mirpur) {
        $q->where('slug', $mirpur->slug);
    })->count();
    echo "Doctors in Mirpur via chamber area: $doctorsInMirpur\n";
} else {
    echo "Area Mirpur not found by slug.\n";
}

$dhaka = District::where('slug', 'dhaka-district')->first();
if ($dhaka) {
    echo "Dhaka District ID: " . $dhaka->id . "\n";
    $doctorsInDhaka = Doctor::whereHas('chambers.area.district', function($q) use ($dhaka) {
        $q->where('slug', $dhaka->slug);
    })->count();
    echo "Doctors in Dhaka via chamber district: $doctorsInDhaka\n";
} else {
    echo "District Dhaka not found by slug.\n";
}

$chambers = \App\Models\Chamber::limit(10)->get();
echo "\nChambers:\n";
foreach($chambers as $c) {
    echo "Doc ID: {$c->doctor_id}, Area ID: {$c->area_id}, District ID: {$c->district_id}\n";
}

$doctors = Doctor::limit(5)->get();
echo "\nDoctors:\n";
foreach($doctors as $d) {
    echo "Doc ID: {$d->id}, Name: {$d->name}\n";
}
