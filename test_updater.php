<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$docs = App\Models\Doctor::select('slug', \DB::raw('count(*) as total'))->where('import_source', 'popular_diagnostic')->groupBy('slug')->having('total', '>', 1)->get();
foreach($docs as $d) {
    echo $d->slug . " => " . $d->total . "\n";
}
