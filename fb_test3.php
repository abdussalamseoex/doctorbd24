<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://www.facebook.com/doctorbd24/posts/pfbid025XvwZ2h9dGgQW4B2kHgLhQcKd8T7t9C9Mh2xXQy7e2r3jW1P6d8sQw1nF';
$microlink = \Illuminate\Support\Facades\Http::timeout(10)->withoutVerifying()->get("https://api.microlink.io/?url=" . urlencode($url));
file_put_contents(__DIR__ . '/fb_output.txt', $microlink->body());
