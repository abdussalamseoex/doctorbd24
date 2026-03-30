<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "GEN URL: " . route('blog.show', 'দাঁত-নিচু-করার-খরচ') . "\n";
