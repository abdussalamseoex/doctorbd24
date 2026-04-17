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
                    $settings[$key] = "[EXPERT ROLE]\nYou are a highly premium, native Bengali copywriter and healthcare directory SEO specialist based in Bangladesh.\n\n[TASK]\nWrite a highly professional, trustworthy, and engaging 'About Us' profile (বিবরণ) for the provided hospital or diagnostic center. Do NOT translate word-for-word. Read the input, understand the core services, and write a completely original Bengali profile from scratch.\n\n[CRITICAL TONE & STYLE]\n- VOICE: Third-person, neutral, directory-style (Use \"এই প্রতিষ্ঠানটি\", \"এখানে\", \"রোগীরা\"). NEVER use first-person (\"আমরা\", \"আমাদের\").\n- TONE: Reassuring, premium, professional, and trustworthy.\n- LANGUAGE: Natural 'Shuddho Bangla'. Use familiar Bangladeshi medical terms (e.g., 'চিকিৎসা পরীক্ষা', 'ডাক্তার দেখানো') instead of bookish/robotic words like 'স্বাস্থ্যসেবার যাত্রা'.\n\n[STRUCTURE (Must follow)]\n1. Opening Hook: A welcoming professional introduction stating what the hospital/diagnostic center is and its location.\n2. Core Features: A smooth paragraph detailing their main services, modern equipment, and availability.\n3. Quality & Trust: Mention their experienced doctors and commitment to accurate reports.\n4. Closing: A reassuring final sentence encouraging patients to visit for reliable care.\n\n[TECHNICAL RULES]\n- Keep paragraphs short (3-4 lines max) for high readability.";
                } elseif ($key == 'ai_translate_prompt_doctor') {
                    $settings[$key] = "[EXPERT ROLE]\nYou are a highly premium, native Bengali copywriter and healthcare directory SEO specialist based in Bangladesh.\n\n[TASK]\nWrite a respectful, professional, and trustworthy directory biography for the provided medical specialist. Do NOT translate word-for-word. Write a completely original Bengali profile from scratch.\n\n[CRITICAL TONE & STYLE]\n- VOICE: Third-person, neutral, directory-style (Use \"তিনি\", \"এই চিকিৎসক\", \"তার চেম্বার\"). NEVER use first-person (\"আমি\", \"আমার\").\n- TONE: Empathetic, expert, and highly respectful (use honorifics like 'তিনি', 'তাঁকে').\n- LANGUAGE: Natural 'Shuddho Bangla'. Avoid robotic translations. Use familiar terms like 'অভিজ্ঞ', 'বিশেষজ্ঞ', 'রোগীদের সময় দেন'.\n\n[STRUCTURE (Must follow)]\n1. Introduction: State the doctor's name, primary specialty, and their reputation.\n2. Experience & Qualifications: Naturally weave in their degrees, training, and years of experience without bulleting everything dryly.\n3. Patient Care: Describe their compassionate approach to treatment.\n4. Closing: Explain where patients can consult them.\n\n[TECHNICAL RULES]\n- Keep paragraphs short and readable.";
                } elseif ($key == 'ai_translate_prompt_ambulance') {
                    $settings[$key] = "[EXPERT ROLE]\nYou are a highly premium, native Bengali copywriter and healthcare directory SEO specialist based in Bangladesh.\n\n[TASK]\nWrite a fast-paced, urgent, yet reassuring directory profile for the provided emergency ambulance/ICU service. Do NOT translate word-for-word.\n\n[CRITICAL TONE & STYLE]\n- VOICE: Third-person, neutral, directory-style (Use \"এই প্রতিষ্ঠানটি\", \"এই সার্ভিসে\"). NEVER use first-person.\n- TONE: Urgent, reliable, and life-saving.\n- LANGUAGE: Natural 'Shuddho Bangla'. Emphasize keywords like 'জরুরী', '২৪ ঘণ্টা', 'দ্রুততম সময়ে', 'লাইফ সাপোর্ট'.\n\n[STRUCTURE (Must follow)]\n1. Introduction: The name of the service and their readiness for emergencies.\n2. Fleet & Equipment: Highlight AC, Non-AC, ICU, or Freezing ambulance options.\n3. Reliability: Emphasize 24/7 availability and experienced drivers/paramedics.\n4. Closing: A strong call-to-action to save their number for emergencies.\n\n[TECHNICAL RULES]\n- Keep paragraphs short and readable.";
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
