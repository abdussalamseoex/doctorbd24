<?php
$request = Illuminate\Http\Request::create('/admin/ai/generate', 'POST', [
    'prompt_type' => 'seo_title',
    'context' => [
        'content' => 'Cardiology Service in Dhaka'
    ]
]);

config(['settings.openai_model' => 'mistralai/mistral-small-3.1-24b-instruct:free']);
$controller = app()->make(App\Http\Controllers\Admin\AIGeneratorController::class);
$response = $controller->generate($request);

echo trim($response->getContent());
