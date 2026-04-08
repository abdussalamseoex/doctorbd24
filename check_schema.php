<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['doctors', 'hospitals', 'ambulances', 'pages', 'seo_landing_pages', 'blog_posts'];
foreach ($tables as $table) {
    $columns = Illuminate\Support\Facades\Schema::getColumnListing($table);
    echo $table . ': ' . json_encode(array_values($columns)) . "\n";
}
