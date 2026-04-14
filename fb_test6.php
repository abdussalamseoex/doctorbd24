<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://www.facebook.com/doctorbd24/posts/pfbid025XvwZ2h9dGgQW4B2kHgLhQcKd8T7t9C9Mh2xXQy7e2r3jW1P6d8sQw1nF';

$agents = [
    'Telegram' => 'TelegramBot (like TwitterBot)',
    'WhatsApp' => 'WhatsApp/2.21.19.21 A',
    'FB'       => 'facebookexternalhit/1.1'
];

foreach ($agents as $name => $ua) {
    echo "Testing $name...\n";
    $response = \Illuminate\Support\Facades\Http::timeout(10)->withoutVerifying()
        ->withHeaders(['User-Agent' => $ua])
        ->get($url);
        
    if ($response->successful()) {
        $html = $response->body();
        preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches);
        echo "Title ($name) : " . ($matches[1] ?? 'not found') . "\n";
        preg_match('/<meta[^>]*property=[\'"]og:title[\'"][^>]*content=[\'"]([^\'"]+)[\'"][^>]*>/i', $html, $matches);
        echo "OG:Title ($name): " . ($matches[1] ?? 'not found') . "\n";
    } else {
        echo "Failed $name (" . $response->status() . ")\n";
    }
}
