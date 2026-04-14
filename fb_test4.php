<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://www.facebook.com/doctorbd24/posts/pfbid025XvwZ2h9dGgQW4B2kHgLhQcKd8T7t9C9Mh2xXQy7e2r3jW1P6d8sQw1nF';
$pluginUrl = 'https://www.facebook.com/plugins/post.php?href=' . urlencode($url);

$response = \Illuminate\Support\Facades\Http::timeout(10)->withoutVerifying()
    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36'])
    ->get($pluginUrl);
    
if ($response->successful()) {
    $html = $response->body();
    file_put_contents(__DIR__ . '/fb_output2.html', $html);
    echo "Plugin HTML saved.\n";
} else {
    echo "Failed Googlebot\n";
}
