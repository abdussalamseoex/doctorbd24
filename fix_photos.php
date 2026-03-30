<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Doctor;

$doctors = Doctor::where('photo', 'like', 'storage/%')->get();
$count = 0;
foreach($doctors as $doctor) {
    // Strip the exact 'storage/' prefix safely
    if (str_starts_with($doctor->photo, 'storage/')) {
        $doctor->photo = substr($doctor->photo, 8); 
        $doctor->save();
        $count++;
    }
}
echo "Fixed DB records: $count\n";
