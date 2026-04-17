<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = new \App\Http\Controllers\Admin\AIGeneratorController();

$context = [
    'name' => 'Popular Diagnostic Centre Ltd. | Tangail',
    'bn_name' => 'পপুলার ডায়াগনস্টিক সেন্টার লিমিটেড | টাঙ্গাইল',
    'address' => 'Akota Tower, Mymensingh Road, Sabalia Tangail',
    'bn_address' => 'একতা টাওয়ার, ময়মনসিংহ রোড, সাবালিয়া টাঙ্গাইল',
    'target_language' => 'Bengali'
];

$request = \Illuminate\Http\Request::create('/test', 'POST', [
    'prompt_type' => 'ai_translate_prompt_hospital',
    'context' => $context
]);

// Let's call the buildPrompt method via reflection to see the exact text
$reflection = new \ReflectionMethod(\App\Http\Controllers\Admin\AIGeneratorController::class, 'buildPrompt');
$reflection->setAccessible(true);
$prompt = $reflection->invoke($controller, 'ai_translate_prompt_hospital', $context);

echo "------------- PROMPT -------------\n";
echo $prompt;
echo "\n----------------------------------\n";
