<?php
// Script to parse GSC Table.csv and insert 410 redirects
$laravelPath = dirname(__DIR__);
require $laravelPath . '/vendor/autoload.php';

$app = require_once $laravelPath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RedirectLog;

$csvFile = __DIR__ . '/Table.csv';

if (!file_exists($csvFile)) {
    die("CSV file not found: $csvFile\n");
}

$file = fopen($csvFile, 'r');
$header = fgetcsv($file); // "URL", "Last crawled"

$inserted = 0;
while (($row = fgetcsv($file)) !== false) {
    if (count($row) < 1) continue;
    $url = $row[0];
    
    // Parse URL to get path
    $parsed = parse_url($url);
    if (!isset($parsed['path'])) continue;
    
    // Path comes as /doctor/slug, we need doctor/slug
    $path = ltrim($parsed['path'], '/');
    
    // if URL has query params, we might need them or might not, but GSC usually tracks exact URLs.
    if (isset($parsed['query'])) {
        $path .= '?' . $parsed['query'];
    }

    // Insert or update redirect log
    RedirectLog::updateOrCreate(
        ['from_url' => $path],
        [
            'to_url' => '410',
            'hits' => 0,
            'last_hit_at' => now(),
        ]
    );
    
    $inserted++;
}

fclose($file);
echo "Successfully inserted $inserted URLs as 410 Gone into RedirectLog.\n";

