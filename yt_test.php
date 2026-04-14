<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://www.youtube.com/@somoynews360/videos';
$response = \Illuminate\Support\Facades\Http::timeout(10)->withoutVerifying()->get($url);
    
if ($response->successful()) {
    $html = $response->body();
    preg_match_all('/"videoId":"([a-zA-Z0-9_-]{11})"/', $html, $matches);
    $ids = array_unique($matches[1]);
    echo "Found " . count($ids) . " videos via regex.\n";
    print_r(array_slice(array_values($ids), 0, 5));
}
