<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$filePath = base_path('popular_diagnostic_20260404_2118.csv');
$handle = fopen($filePath, 'r');
$headers = fgetcsv($handle);
$headers[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $headers[0]);
$headers = array_map('trim', $headers);
$row = fgetcsv($handle);
$data = array_combine($headers, $row);
print_r($data);
