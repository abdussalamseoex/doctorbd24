<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$slug = "দাঁত-নিচু-করার-খরচ";
$post = \App\Models\BlogPost::where('slug', $slug)->first();

if ($post) {
    echo "SUCCESS: Found " . $post->title . "\n";
} else {
    echo "FAILED: Could not find post with slug $slug\n";
    // Let's print all slugs to see what's in the DB
    $all = \App\Models\BlogPost::pluck('slug')->toArray();
    echo "DB SLUGS:\n";
    print_r($all);
}
