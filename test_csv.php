<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$h = fopen(base_path('WordPress Data/doctor-export-2026-03-26.csv'), 'r');
$hds = fgetcsv($h);
$hds[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $hds[0]);
$hds = array_map('trim', $hds);

$i = 0;
while(($r = fgetcsv($h)) !== false && $i < 5) {
    if(count($r) != count($hds)) continue;
    $d = array_combine($hds, $r);
    $n = trim($d['post_title'] ?? ($d['doctors_name'] ?? ''));
    if(!empty($n)) {
        echo $n . ' => Featured: ' . ($d['featured_image'] ?? '') . ' | Image: ' . ($d['image'] ?? '') . "\n";
        $i++;
    }
}
