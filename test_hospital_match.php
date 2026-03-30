<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Hospital;

$testScraped = "Popular Diagnostic Center Shantinagar";

$all = Hospital::get(['id', 'name']);
echo "Total hospitals: " . $all->count() . "\n";

function simplifyName($name) {
    // remove words like "limited", "ltd", "center", "centre", "hospital", "clinic" to find the real core?
    // actually, just remove punctuation
    return strtolower(preg_replace('/[^a-z0-9]/i', '', $name));
}

$scrapedSimple = simplifyName($testScraped);

$matches = [];
foreach ($all as $h) {
    $dbSimple = simplifyName($h->name);
    
    // 1. Exact match (without punctuation)
    if ($scrapedSimple === $dbSimple) {
        $matches[] = ['type' => 'Exact', 'name' => $h->name];
        continue;
    }
    
    // 2. Scraped contains DB (e.g. "Popular Diagnostic Center Shantinagar" contains "Popular Diagnostic Center")
    if (strlen($dbSimple) > 10 && str_contains($scrapedSimple, $dbSimple)) {
        $matches[] = ['type' => 'Scraped Contains DB', 'name' => $h->name, 'len' => strlen($dbSimple)];
    }
    
    // 3. DB contains Scraped
    if (strlen($scrapedSimple) > 10 && str_contains($dbSimple, $scrapedSimple)) {
        $matches[] = ['type' => 'DB Contains Scraped', 'name' => $h->name, 'len' => strlen($dbSimple)];
    }
}

// Sort by length to prefer the most specific match
usort($matches, function($a, $b) {
    return ($b['len'] ?? 0) <=> ($a['len'] ?? 0);
});

print_r($matches);

