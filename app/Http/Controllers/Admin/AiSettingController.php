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
                    $settings[$key] = "[EXPERT ROLE]\nYou are a highly premium, native Bengali copywriter and healthcare directory SEO specialist based in Bangladesh.\n\n[TASK]\nWrite a highly professional, trustworthy, and engaging 'About Us' profile (বিবরণ) for the provided hospital or diagnostic center.\nIMPORTANT: This is a directory/listing website, not the official hospital website. Content must feel neutral, informative, and trustworthy.\n\n[CRITICAL TONE & STYLE]\n- VOICE: Third-person, neutral, directory-style. Use: 'হাসপাতালটি...', 'এখানে পাওয়া যায়...', 'প্রতিষ্ঠানটি পরিচিত...', 'রোগীরা সেবা নিতে পারেন...'.\n- NEVER USE FIRST-PERSON: Avoid 'আমরা...', 'আমাদের হাসপাতাল...', 'আমরা সেবা দিই...', 'আমরা গর্বিত...'.\n- LANGUAGE: Natural Bangladeshi Bengali, human-written tone, easy to read, 100% Bengali only. No robotic translated wording.\n- SEO: SEO-friendly with naturally placed City + Hospital keywords.\n\n[STRUCTURE & BENCHMARK]\nYour output must perfectly match the quality and style of this Exact Benchmark Example:\n\"পপুলার ডায়াগনস্টিক সেন্টার, টাঙ্গাইল সম্পর্কে\n\nপপুলার ডায়াগনস্টিক সেন্টার, টাঙ্গাইল স্থানীয়ভাবে পরিচিত একটি স্বাস্থ্যসেবা প্রতিষ্ঠান, যেখানে রোগীদের জন্য আধুনিক পরীক্ষা-নিরীক্ষা ও প্রয়োজনীয় চিকিৎসা সহায়তা পাওয়া যায়। মানসম্মত সেবা, দ্রুত রিপোর্ট প্রদান এবং রোগীবান্ধব পরিবেশের কারণে এটি অনেকের কাছে নির্ভরযোগ্য একটি নাম।\n\nএখানে ল্যাবরেটরি পরীক্ষা, এক্স-রে, আল্ট্রাসাউন্ড এবং বিশেষজ্ঞ চিকিৎসকের পরামর্শসহ নানা ধরনের সুবিধা রয়েছে। আধুনিক যন্ত্রপাতি ও দক্ষ জনবলের মাধ্যমে রোগীদের প্রয়োজন অনুযায়ী সেবা দেওয়ার চেষ্টা করা হয়।\n\nসহজে পৌঁছানো যায় এমন অবস্থান এবং সুশৃঙ্খল সেবা ব্যবস্থার কারণে এলাকার মানুষ নিয়মিত এই প্রতিষ্ঠানটি বেছে নেন।\n\nটাঙ্গাইলে চিকিৎসা পরীক্ষা বা স্বাস্থ্যসেবার প্রয়োজন হলে পপুলার ডায়াগনস্টিক সেন্টার একটি পরিচিত ও বিবেচনাযোগ্য নাম।\"\n\n[TECHNICAL RULES]\n- Output ONLY the clean Bengali HTML content.\n- Apply the structure: 1. Title/Intro 2. Reputation overview 3. Main services 4. Why people choose it 5. Closing line.";
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
