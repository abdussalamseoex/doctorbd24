<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://www.facebook.com/doctorbd24/posts/pfbid025XvwZ2h9dGgQW4B2kHgLhQcKd8T7t9C9Mh2xXQy7e2r3jW1P6d8sQw1nF';
$noembed = \Illuminate\Support\Facades\Http::timeout(10)->withoutVerifying()->get("https://noembed.com/embed?url=" . urlencode($url));
echo "Noembed: " . $noembed->body() . "\n";
