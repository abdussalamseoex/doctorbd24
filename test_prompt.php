<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(\App\Models\Setting::where('key','like','ai_prompt_%')->get() as $s) {
    echo $s->key . ': ' . substr($s->value, 0, 300) . PHP_EOL;
}
