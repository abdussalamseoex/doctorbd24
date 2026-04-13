<?php
ini_set('memory_limit', '-1');

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Doctor;
use App\Models\Hospital;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

echo "Starting optimization...\n";
$manager = new ImageManager(new Driver());

function optimizeFile($manager, $path, $directory, $maxWidth) {
    if (!trim($path) || !Storage::disk('public')->exists($path)) {
        return null;
    }
    try {
        $absolutePath = Storage::disk('public')->path($path);
        
        // If it's already a webP, skip or not? Actually we only pass non-webps.
        $image = $manager->decode($absolutePath);

        $filename = \Illuminate\Support\Str::uuid() . '.webp';
        $newRelativePath = $directory . '/' . $filename;
        $newAbsolutePath = Storage::disk('public')->path($newRelativePath);

        if ($maxWidth && $image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        Storage::disk('public')->makeDirectory($directory);
        $image->save($newAbsolutePath, quality: 80);

        Storage::disk('public')->delete($path);

        return $newRelativePath;
    } catch (\Exception $e) {
        echo "Failed $path: " . $e->getMessage() . "\n";
        return null;
    }
}

// Hospitals
$hospitals = Hospital::all();
foreach ($hospitals as $hospital) {
    $updates = [];
    if ($hospital->logo && !str_ends_with($hospital->logo, '.webp')) {
        $n = optimizeFile($manager, $hospital->logo, 'hospitals', 800);
        if ($n) $updates['logo'] = $n;
    }
    if ($hospital->banner && !str_ends_with($hospital->banner, '.webp')) {
        $n = optimizeFile($manager, $hospital->banner, 'hospitals/covers', 1200);
        if ($n) $updates['banner'] = $n;
    }
    if (!empty($updates)) {
        $hospital->update($updates);
        echo "Updated hospital: {$hospital->name}\n";
    }
}

// Doctors
$doctors = Doctor::all();
foreach ($doctors as $doctor) {
    $updates = [];
    if ($doctor->photo && !str_ends_with($doctor->photo, '.webp')) {
        $n = optimizeFile($manager, $doctor->photo, 'doctors', 800);
        if ($n) $updates['photo'] = $n;
    }
    if ($doctor->cover_image && !str_ends_with($doctor->cover_image, '.webp')) {
        $n = optimizeFile($manager, $doctor->cover_image, 'doctors/covers', 1200);
        if ($n) $updates['cover_image'] = $n;
    }
    if (!empty($updates)) {
        $doctor->update($updates);
        echo "Updated doctor: {$doctor->name}\n";
    }
}

echo "Done!\n";
