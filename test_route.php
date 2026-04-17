<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$prompt = \App\Models\Setting::get('ai_translate_prompt_hospital');
$controller = new \App\Http\Controllers\Admin\AIGeneratorController();

// Simulate the logic in AIGeneratorController
$context = [
    'name' => 'Popular Diagnostic Centre Ltd. | Tangail',
    'address' => 'Akota Tower, Mymensingh Road, Sabalia, Tangail',
    'specialties' => 'Ultrasound, X-Ray, Blood Test'
];

$controller = new \App\Http\Controllers\Admin\AIGeneratorController();

$context = [
    'name' => 'Popular Diagnostic Centre Ltd. | Tangail',
    'address' => 'Akota Tower, Mymensingh Road, Sabalia, Tangail',
    'specialties' => 'Ultrasound, X-Ray, Blood Test'
];

$request = \Illuminate\Http\Request::create('/test', 'POST', [
    'prompt_type' => 'ai_translate_prompt_hospital',
    'context' => $context
]);

// Call buildPrompt using Reflection because it's private
$reflection = new \ReflectionMethod(\App\Http\Controllers\Admin\AIGeneratorController::class, 'buildPrompt');
$reflection->setAccessible(true);
$prompt = $reflection->invoke($controller, 'ai_translate_prompt_hospital', $context);

echo "------ PROMPT ------\n";
echo $prompt;
echo "\n--------------------\n";


$response = app()->handle($request);
echo $response->status();
