<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://www.facebook.com/doctorbd24/posts/pfbid025XvwZ2h9dGgQW4B2kHgLhQcKd8T7t9C9Mh2xXQy7e2r3jW1P6d8sQw1nF';
$response = \Illuminate\Support\Facades\Http::timeout(10)->withoutVerifying()
    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'])
    ->get($url);
    
if ($response->successful()) {
    $html = $response->body();
    preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches);
    echo "Title 1 (Googlebot): " . ($matches[1] ?? 'not found') . "\n";
    preg_match('/<meta[^>]*property=[\'"]og:image[\'"][^>]*content=[\'"]([^\'"]+)[\'"][^>]*>/i', $html, $matches);
    echo "Image 1 (Googlebot): " . ($matches[1] ?? 'not found') . "\n";
} else {
    echo "Failed Googlebot\n";
}

$response = \Illuminate\Support\Facades\Http::timeout(10)->withoutVerifying()
    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36'])
    ->get($url);
    
if ($response->successful()) {
    $html = $response->body();
    preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches);
    echo "Title 2 (Chrome): " . ($matches[1] ?? 'not found') . "\n";
}
