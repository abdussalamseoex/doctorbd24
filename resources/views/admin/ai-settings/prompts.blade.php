@extends('admin.layouts.app')
@section('title', 'Auto Translate Prompts')
@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm font-medium flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="max-w-4xl mx-auto space-y-6">
    <form method="POST" action="{{ route('admin.ai-settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-2 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600">📝</span>
                Auto Translate Prompts (Bengali)
            </h3>
            <p class="text-xs text-gray-500 mb-6">These prompts are triggered when you click "✨ Auto Translate to Bengali (AI)" inside the Add/Edit forms. The system will send the input data (name, address, SEO fields) and this prompt will direct the AI how to generate the corresponding Bengali content. <br><b>Tip:</b> Instruct the AI to explicitly return VALID JSON mimicking the original keys.</p>

            <div class="space-y-5">
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider block mb-1.5">Hospital / Diagnostic Profile Prompt</label>
                    <textarea name="ai_translate_prompt_hospital" rows="6" class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-300 transition-all font-mono" placeholder="Write the rule for translating hospital/diagnostic content exactly how you want it...">{{ old('ai_translate_prompt_hospital', $settings['ai_translate_prompt_hospital'] ?? "You are an expert native Bengali copywriter for a Bangladeshi healthcare website.\nCRITICAL INSTRUCTION: Write completely ORIGINAL, natural, and engaging Bengali content based on the provided facts.\nTONE INSTRUCTION: This is a directory/listing website. Do NOT write in first-person business tone (e.g., \"আমরা\", \"আমাদের\"). Instead, use third-person neutral directory tone (e.g., \"প্রতিষ্ঠানটি\", \"এখানে পাওয়া যায়\", \"রোগীরা নিতে পারেন\"). The content should feel like an informative profile page.\nCONTEXT: You are writing the listing content for a premium hospital or diagnostic center in Bangladesh. Write a welcoming introduction, list their services naturally, and close with a reassuring message.\nFORMAT: Your final response MUST be a STRICT JSON OBJECT with exactly the same structure/keys as the input. Do NOT add markdown blocks.") }}</textarea>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider block mb-1.5">Doctor Biography Prompt</label>
                    <textarea name="ai_translate_prompt_doctor" rows="6" class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-300 transition-all font-mono" placeholder="Write the rule for translating doctor profiles...">{{ old('ai_translate_prompt_doctor', $settings['ai_translate_prompt_doctor'] ?? "You are an expert native Bengali copywriter for a Bangladeshi healthcare website.\nCRITICAL INSTRUCTION: Write completely ORIGINAL, natural, and engaging Bengali content based on the provided facts.\nTONE INSTRUCTION: This is a directory/listing website. Do NOT write in first-person business tone (e.g., \"আমি\", \"আমার\"). Instead, use third-person neutral directory tone (e.g., \"ডাক্তার সাহেব\", \"তাঁর ক্লিনিক\"). The content should feel like an informative profile page.\nCONTEXT: You are writing the official directory biography of a reputed Bangladeshi medical specialist. Highlight their experience, specialties, and compassionate care in a professional tone.\nFORMAT: Your final response MUST be a STRICT JSON OBJECT with exactly the same structure/keys as the input. Do NOT add markdown blocks.") }}</textarea>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider block mb-1.5">Ambulance Service Prompt</label>
                    <textarea name="ai_translate_prompt_ambulance" rows="6" class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-300 transition-all font-mono" placeholder="Write the rule for ambulance services...">{{ old('ai_translate_prompt_ambulance', $settings['ai_translate_prompt_ambulance'] ?? "You are an expert native Bengali copywriter for a Bangladeshi healthcare website.\nCRITICAL INSTRUCTION: Write completely ORIGINAL, natural, and engaging Bengali content based on the provided facts.\nTONE INSTRUCTION: This is a directory/listing website. Do NOT write in first-person business tone (e.g., \"আমরা\", \"আমাদের\"). Instead, use third-person neutral directory tone (e.g., \"প্রতিষ্ঠানটি\", \"এখানে পাওয়া যায়\"). The content should feel like an informative profile page.\nCONTEXT: You are writing the directory profile for an emergency ambulance and ICU service provider in Bangladesh. Highlight their speed, 24/7 availability, and reliability.\nFORMAT: Your final response MUST be a STRICT JSON OBJECT with exactly the same structure/keys as the input. Do NOT add markdown blocks.") }}</textarea>
                </div>
                
                {{-- Reset Default Buttons via JS --}}
                <div class="flex justify-end mt-2">
                    <button type="button" onclick="resetToDefaultPrompts()" class="text-xs text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 underline font-medium">Reset All Prompts to Perfect Defaults</button>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 mt-8">
            <button type="submit" class="px-8 py-3 rounded-2xl bg-gradient-to-r from-sky-500 to-blue-600 text-white font-bold shadow-lg shadow-sky-200 dark:shadow-none hover:opacity-90 transition-all">
                Save Prompts
            </button>
        </div>
    </form>
</div>

<script>
function resetToDefaultPrompts() {
    if (confirm('Are you sure you want to overwrite your custom prompts with the default directory-focused templates?')) {
        document.querySelector('textarea[name="ai_translate_prompt_hospital"]').value = "You are an expert native Bengali copywriter for a Bangladeshi healthcare website.\nCRITICAL INSTRUCTION: Write completely ORIGINAL, natural, and engaging Bengali content based on the provided facts.\nTONE INSTRUCTION: This is a directory/listing website. Do NOT write in first-person business tone (e.g., \"আমরা\", \"আমাদের\"). Instead, use third-person neutral directory tone (e.g., \"প্রতিষ্ঠানটি\", \"এখানে পাওয়া যায়\", \"রোগীরা নিতে পারেন\"). The content should feel like an informative profile page.\nCONTEXT: You are writing the listing content for a premium hospital or diagnostic center in Bangladesh. Write a welcoming introduction, list their services naturally, and close with a reassuring message.\nFORMAT: Your final response MUST be a STRICT JSON OBJECT with exactly the same structure/keys as the input. Do NOT add markdown blocks.";
        document.querySelector('textarea[name="ai_translate_prompt_doctor"]').value = "You are an expert native Bengali copywriter for a Bangladeshi healthcare website.\nCRITICAL INSTRUCTION: Write completely ORIGINAL, natural, and engaging Bengali content based on the provided facts.\nTONE INSTRUCTION: This is a directory/listing website. Do NOT write in first-person business tone (e.g., \"আমি\", \"আমার\"). Instead, use third-person neutral directory tone (e.g., \"ডাক্তার সাহেব\", \"তাঁর ক্লিনিক\"). The content should feel like an informative profile page.\nCONTEXT: You are writing the official directory biography of a reputed Bangladeshi medical specialist. Highlight their experience, specialties, and compassionate care in a professional tone.\nFORMAT: Your final response MUST be a STRICT JSON OBJECT with exactly the same structure/keys as the input. Do NOT add markdown blocks.";
        document.querySelector('textarea[name="ai_translate_prompt_ambulance"]').value = "You are an expert native Bengali copywriter for a Bangladeshi healthcare website.\nCRITICAL INSTRUCTION: Write completely ORIGINAL, natural, and engaging Bengali content based on the provided facts.\nTONE INSTRUCTION: This is a directory/listing website. Do NOT write in first-person business tone (e.g., \"আমরা\", \"আমাদের\"). Instead, use third-person neutral directory tone (e.g., \"প্রতিষ্ঠানটি\", \"এখানে পাওয়া যায়\"). The content should feel like an informative profile page.\nCONTEXT: You are writing the directory profile for an emergency ambulance and ICU service provider in Bangladesh. Highlight their speed, 24/7 availability, and reliability.\nFORMAT: Your final response MUST be a STRICT JSON OBJECT with exactly the same structure/keys as the input. Do NOT add markdown blocks.";
    }
}
</script>
@endsection
