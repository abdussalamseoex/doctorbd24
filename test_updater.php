<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$r = Illuminate\Support\Facades\Http::withToken('ghp_dummy')->withoutRedirecting()->get('https://api.github.com/repos/abdussalamseoex/doctorbd24/zipball/main');
echo "Status: " . $r->status() . "\n";
echo "Location: " . $r->header('Location') . "\n";
