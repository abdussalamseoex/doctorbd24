<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://www.facebook.com/doctorbd24/posts/pfbid025XvwZ2h9dGgQW4B2kHgLhQcKd8T7t9C9Mh2xXQy7e2r3jW1P6d8sQw1nF';
$urlParts = parse_url($url);
$mbasicUrl = $urlParts['scheme'] . '://mbasic.facebook.com' . $urlParts['path'] . (isset($urlParts['query']) ? '?' . $urlParts['query'] : '');

$response = \Illuminate\Support\Facades\Http::timeout(10)->withoutVerifying()
    ->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Mobile Safari/537.36',
        'Accept-Language' => 'en-US,en;q=0.9',
        'Cache-Control' => 'no-cache'
    ])
    ->get($mbasicUrl);
    
if ($response->successful()) {
    $html = $response->body();
    preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches);
    echo "Title: " . ($matches[1] ?? 'not found') . "\n";
} else {
    echo "Failed mbasic\n";
}
