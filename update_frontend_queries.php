<?php
$files = [
    __DIR__ . '/app/Http/Controllers/DoctorController.php',
    __DIR__ . '/app/Http/Controllers/HospitalController.php',
    __DIR__ . '/app/Http/Controllers/HomeController.php',
    __DIR__ . '/app/Http/Controllers/PageController.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Replace where('is_active', true) with published()
    $content = str_replace("where('is_active', true)", "published()", $content);
    $content = str_replace("where('active', true)", "published()", $content);
    $content = str_replace("where('active', 1)", "published()", $content);
    
    // In DoctorController it uses whereNull('status')->orWhere(...)
    $content = preg_replace("/->where\(function\s*\(\\$q\)\s*\{\s*\\$q->whereNull\('status'\)->orWhere\('status',\s*'\!=',\s*'draft'\);\s*\}\)/", "->published()", $content);

    // In HospitalController it uses whereNull('status')->orWhere(...)
    $content = preg_replace("/->where\(function\s*\(\\$q\)\s*\{\s*\\$q->whereNull\('status'\)->orWhere\('status',\s*'\!=',\s*'draft'\);\s*\}\)/", "->published()", $content);

    // In HomeController
    $content = preg_replace("/Doctor::where\('verified',\s*true\)/", "Doctor::published()->where('verified', true)", $content);
    $content = preg_replace("/Hospital::where\('verified',\s*true\)/", "Hospital::published()->where('verified', true)", $content);

    file_put_contents($file, $content);
    echo "Updated $file\n";
}
