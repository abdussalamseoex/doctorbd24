<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AiSettingController extends Controller
{
    public function index()
    {
        $keys = [
            'ai_provider', 'openai_api_key', 'gemini_api_key', 'openai_base_url', 'openai_model',
            'ai_translate_prompt_hospital', 'ai_translate_prompt_doctor', 'ai_translate_prompt_ambulance'
        ];
        
        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = Setting::get($key, '');
        }

        return view('admin.ai-settings.index', compact('settings'));
    }

    public function prompts()
    {
        $keys = [
            'ai_translate_prompt_hospital', 'ai_translate_prompt_doctor', 'ai_translate_prompt_ambulance'
        ];
        
        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = Setting::get($key, '');
            
            // Set defaults if empty
            if (empty($settings[$key])) {
                if ($key == 'ai_translate_prompt_hospital') {
                    $settings[$key] = "You are an expert native Bengali copywriter for a Bangladeshi healthcare website.\nCRITICAL INSTRUCTION: Write completely ORIGINAL, natural, and engaging Bengali content based on the provided facts.\nTONE INSTRUCTION: This is a directory/listing website. Do NOT write in first-person business tone (e.g., \"আমরা\", \"আমাদের\"). Instead, use third-person neutral directory tone (e.g., \"প্রতিষ্ঠানটি\", \"এখানে পাওয়া যায়\", \"রোগীরা নিতে পারেন\"). The content should feel like an informative profile page.\nCONTEXT: You are writing the listing content for a premium hospital or diagnostic center in Bangladesh. Write a welcoming introduction, list their services naturally, and close with a reassuring message.\nFORMAT: Your final response MUST be a STRICT JSON OBJECT with exactly the same structure/keys as the input. Do NOT add markdown blocks.";
                } elseif ($key == 'ai_translate_prompt_doctor') {
                    $settings[$key] = "You are an expert native Bengali copywriter for a Bangladeshi healthcare website.\nCRITICAL INSTRUCTION: Write completely ORIGINAL, natural, and engaging Bengali content based on the provided facts.\nTONE INSTRUCTION: This is a directory/listing website. Do NOT write in first-person business tone (e.g., \"আমি\", \"আমার\"). Instead, use third-person neutral directory tone (e.g., \"ডাক্তার সাহেব\", \"তাঁর ক্লিনিক\"). The content should feel like an informative profile page.\nCONTEXT: You are writing the official directory biography of a reputed Bangladeshi medical specialist. Highlight their experience, specialties, and compassionate care in a professional tone.\nFORMAT: Your final response MUST be a STRICT JSON OBJECT with exactly the same structure/keys as the input. Do NOT add markdown blocks.";
                } elseif ($key == 'ai_translate_prompt_ambulance') {
                    $settings[$key] = "You are an expert native Bengali copywriter for a Bangladeshi healthcare website.\nCRITICAL INSTRUCTION: Write completely ORIGINAL, natural, and engaging Bengali content based on the provided facts.\nTONE INSTRUCTION: This is a directory/listing website. Do NOT write in first-person business tone (e.g., \"আমরা\", \"আমাদের\"). Instead, use third-person neutral directory tone (e.g., \"প্রতিষ্ঠানটি\", \"এখানে পাওয়া যায়\"). The content should feel like an informative profile page.\nCONTEXT: You are writing the directory profile for an emergency ambulance and ICU service provider in Bangladesh. Highlight their speed, 24/7 availability, and reliability.\nFORMAT: Your final response MUST be a STRICT JSON OBJECT with exactly the same structure/keys as the input. Do NOT add markdown blocks.";
                }
            }
        }

        return view('admin.ai-settings.prompts', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.ai-settings.index')->with('success', 'AI Settings saved successfully.');
    }
}
