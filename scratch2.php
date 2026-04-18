<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$doctorPrompt = App\Models\Setting::get('ai_translate_prompt_doctor');
if (!$doctorPrompt) {
    $doctorPrompt = "You are an expert native Bengali copywriter for a Bangladeshi healthcare website.\nCRITICAL INSTRUCTION: Write completely ORIGINAL, natural, and engaging Bengali content based on the provided facts.\nTONE INSTRUCTION: This is a directory/listing website. Do NOT write in first-person business tone (e.g., \"আমি\", \"আমার\"). Instead, use third-person neutral directory tone (e.g., \"ডাক্তার সাহেব\", \"তাঁর ক্লিনিক\"). The content should feel like an informative profile page.\nCONTEXT: You are writing the official directory biography of a reputed Bangladeshi medical specialist. Highlight their experience, specialties, and compassionate care in a professional tone.\nFORMAT: Your final response MUST be a STRICT JSON OBJECT with exactly the same structure/keys as the input. Do NOT add markdown blocks.";
}
$doctorPrompt = preg_replace('/STRICT RULE 5:.*?\.\n/is', '', $doctorPrompt); // Remove if exists
$doctorPrompt .= "\nSTRICT RULE 5: TRANSLITERATION OVER TRANSLATION. For Medical Specialties, Designations, and Body Parts, DO NOT use pure dictionary Bengali words (e.g., DO NOT write 'সেক্সরোগ', 'ছিন্ন শ্বাসরোগ', 'চর্মরোগ', 'উম্নয়'). Instead, you MUST use Phonetic Transliteration of the English words in Bengali letters (e.g., 'পালমোনোলজিস্ট', 'সেক্সোলজিস্ট', 'ডার্মাটোলজিস্ট', 'সিনিয়র কনসালটেন্ট'). English medical terms are standard in Bangladesh.";

App\Models\Setting::updateOrCreate(['key'=>'ai_translate_prompt_doctor'], ['value'=>$doctorPrompt]);
echo "Updated ai_translate_prompt_doctor DB.\n";

$hospitalPrompt = App\Models\Setting::get('ai_translate_prompt_hospital');
if (!$hospitalPrompt) {
    $hospitalPrompt = "You are an expert native Bengali copywriter for a Bangladeshi healthcare website.\nCRITICAL INSTRUCTION: Write completely ORIGINAL, natural, and engaging Bengali content based on the provided facts.\nTONE INSTRUCTION: This is a directory/listing website. Do NOT write in first-person business tone (e.g., \"আমরা\", \"আমাদের\"). Instead, use third-person neutral directory tone (e.g., \"প্রতিষ্ঠানটি\", \"এখানে পাওয়া যায়\", \"রোগীরা নিতে পারেন\"). The content should feel like an informative profile page.\nCONTEXT: You are writing the listing content for a premium hospital or diagnostic center in Bangladesh. Write a welcoming introduction, list their services naturally, and close with a reassuring message.\nFORMAT: Your final response MUST be a STRICT JSON OBJECT with exactly the same structure/keys as the input. Do NOT add markdown blocks.";
}
$hospitalPrompt .= "\nSTRICT RULE 5: TRANSLITERATION OVER TRANSLATION. For Medical Departments, Ambulance Types, and Services, DO NOT use pure dictionary Bengali words. Instead, you MUST use Phonetic Transliteration of the English words in Bengali letters (e.g., 'অ্যাম্বুলেন্স', 'ইমারজেন্সি', 'আইসিইউ / ICU'). English medical terms are standard in Bangladesh.";

App\Models\Setting::updateOrCreate(['key'=>'ai_translate_prompt_hospital'], ['value'=>$hospitalPrompt]);
echo "Updated ai_translate_prompt_hospital DB.\n";
