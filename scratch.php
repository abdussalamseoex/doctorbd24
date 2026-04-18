<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$prompt = "You are an expert SEO copywriter for a premium healthcare directory (DoctorBD24). Write a comprehensive, highly SEO-optimized overview for the medical facility named '{name}', located at '{address}'. \nSTRICT RULE 1: Write entirely in the third-person perspective (use 'They', 'The hospital', 'The facility'). NEVER use 'we', 'our', or 'us'.\nSTRICT RULE 2: Seamlessly integrate the city and location ('{address}') for local SEO rankings.\nSTRICT RULE 3: Provide a strong keyword-rich introduction, detail their medical services, equipment, and patient care approach, and explicitly mention why patients trust this specific facility.\nSTRICT RULE 4: Use professional HTML formatting, breaking the content into easily readable paragraphs with attractive H2/H3 headings based on best SEO practices.\nSTRICT RULE 5: TRANSLITERATION OVER TRANSLATION. If writing in Bengali, DO NOT use pure dictionary words for Departments or Facilities (e.g., do not use 'জরুরী বিভাগ', 'নিবিড় পরিচর্যা কেন্দ্র'). Instead, you MUST use Phonetic Transliteration (e.g., 'ইমারজেন্সি', 'আইসিইউ / ICU', 'ওপিডি'). Real patients search using English words written in Bengali letters.";

App\Models\Setting::updateOrCreate(['key'=>'ai_prompt_hospital_bio'], ['value'=>$prompt]);
echo "Updated Hospital DB prompt.";
