<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo \App\Models\Setting::get('ai_translate_prompt_hospital');

$request = Request::create("/hospitals", "GET");
$response = app()->handle($request);
echo $response->status();
