<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/doctors-in-dhaka', 'GET');
$app->instance('request', $request);

$page = \App\Models\SeoLandingPage::where('slug', 'doctors-in-dhaka')->first();
$page->division_id = 1;
$page->district_id = 1;
$page->area_id = 1;
$page->save();

$component = app(\Livewire\LivewireManager::class)->mount('doctor-list', [
    'specialty' => $page->specialty->slug ?? '',
    'division' => $page->division->slug ?? '',
    'district' => $page->district->slug ?? '',
    'area' => $page->area->slug ?? '',
    'hideFilters' => true,
    'seoTitle' => $page->title,
    'seoTopContent' => $page->content_top,
    'seoBottomContent' => $page->content_bottom,
]);

echo "Component properties:\n";
echo "specialty: " . $component->specialty . "\n";
echo "division: " . $component->division . "\n";
echo "district: " . $component->district . "\n";
echo "area: " . $component->area . "\n";

$html = app(\Livewire\LivewireManager::class)->mount('doctor-list', [
    'specialty' => $page->specialty->slug ?? '',
    'division' => $page->division->slug ?? '',
    'district' => $page->district->slug ?? '',
    'area' => $page->area->slug ?? '',
    'hideFilters' => true,
    'seoTitle' => $page->title,
    'seoTopContent' => $page->content_top,
    'seoBottomContent' => $page->content_bottom,
]);
