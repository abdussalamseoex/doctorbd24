<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SeoLandingPage;

$mapped = SeoLandingPage::whereNotNull('area_id')->orWhereNotNull('district_id')->orWhereNotNull('division_id')->orWhereNotNull('specialty_id')->count();
$unmapped = SeoLandingPage::whereNull('area_id')->whereNull('district_id')->whereNull('division_id')->whereNull('specialty_id')->count();

echo "Mapped: $mapped\n";
echo "Unmapped: $unmapped\n";

$unmappedPages = SeoLandingPage::whereNull('area_id')->whereNull('district_id')->whereNull('division_id')->whereNull('specialty_id')->limit(20)->get(['slug']);
echo "\nUnmapped Slugs:\n";
foreach($unmappedPages as $p) {
    echo $p->slug . "\n";
}
