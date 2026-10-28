@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init({
            selector: '.tinymce-editor',
            height: 320,
            menubar: false,
            promotion: false,
            branding: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'visualblocks', 'code', 'fullscreen', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | table link code | removeformat',
            skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
            content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
    });
</script>
<style>
    .tox-notifications-container { display: none !important; }
    .tox-tinymce { z-index: 10 !important; border-radius: 0.75rem !important; }
</style>
@endpush

<div x-data="{
    activeTab: 'en',
    references: {{ isset($disease) && $disease->references->count() > 0 ? json_encode($disease->references->map(function($r){ return ['source_name' => $r->source_name, 'source_url' => $r->source_url, 'notes' => $r->notes]; })) : json_encode([['source_name' => 'Mayo Clinic', 'source_url' => '', 'notes' => 'Clinical Overview']]) }},
    addReference() {
        this.references.push({ source_name: '', source_url: '', notes: '' });
    },
    removeReference(index) {
        this.references.splice(index, 1);
    }
}" class="space-y-6">

    {{-- Top Language Switcher Bar --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sticky top-16 z-30 backdrop-blur-md bg-white/95 dark:bg-gray-800/95">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Editing Language:</span>
            <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-700/60 rounded-xl">
                <button type="button" @click="activeTab = 'en'" :class="activeTab === 'en' ? 'bg-white dark:bg-gray-800 text-sky-600 dark:text-sky-400 shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
                    🇺🇸 English
                </button>
                <button type="button" @click="activeTab = 'bn'" :class="activeTab === 'bn' ? 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
                    🇧🇩 বাংলা (Bengali)
                </button>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-400">You can switch tabs anytime to fill both English and Bengali content.</span>
        </div>
    </div>

    {{-- 1. Basic Information Box --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
        <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs">1</span>
            Basic Information & Classifications
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- English Name --}}
            <div>
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Disease Name (English) *</label>
                <input type="text" name="name[en]" required value="{{ old('name.en', isset($disease) ? $disease->getTranslation('name', 'en', false) : '') }}" placeholder="e.g. Migraine, Hypertension, Dengue Fever"
                       class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-300">
                @error('name.en')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Bengali Name --}}
            <div>
                <label class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 block mb-1.5">রোগের নাম (বাংলা)</label>
                <input type="text" name="name[bn]" value="{{ old('name.bn', isset($disease) ? $disease->getTranslation('name', 'bn', false) : '') }}" placeholder="যেমন: মাইগ্রেন, উচ্চ রক্তচাপ, ডেঙ্গু জ্বর"
                       class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-emerald-200 dark:border-emerald-700 bg-emerald-50/40 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-300">
            </div>

            {{-- Custom Slug --}}
            <div>
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">URL Slug (Leave blank for auto-generation)</label>
                <input type="text" name="slug" value="{{ old('slug', $disease->slug ?? '') }}" placeholder="e.g. migraine"
                       class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-300 font-mono text-xs">
                @error('slug')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Primary Specialty --}}
            <div>
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Primary Doctor Specialty *</label>
                <select name="primary_specialty_id" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    <option value="">Select Primary Specialty (e.g. Neurology, Cardiology)</option>
                    @foreach($specialties as $s)
                        <option value="{{ $s->id }}" @selected(old('primary_specialty_id', $disease->primary_specialty_id ?? '') == $s->id)>
                            {{ $s->getTranslation('name', 'en') }} ({{ $s->getTranslation('name', 'bn') }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-gray-400 mt-1">This connects the disease directly to specialist doctors in doctorbd24.com directory.</p>
            </div>
        </div>

        {{-- Secondary Specialties Multi-Select --}}
        <div>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Secondary Related Specialties (Optional)</label>
            <div class="max-h-36 overflow-y-auto p-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                @php
                    $selectedSecondary = old('secondary_specialties', isset($disease) ? $disease->specialties->pluck('id')->toArray() : []);
                @endphp
                @foreach($specialties as $s)
                    <label class="flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer hover:text-sky-600">
                        <input type="checkbox" name="secondary_specialties[]" value="{{ $s->id }}" @checked(in_array($s->id, $selectedSecondary)) class="rounded border-gray-300 text-sky-600 focus:ring-sky-400 w-3.5 h-3.5">
                        <span class="truncate">{{ $s->getTranslation('name', 'en') }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Featured Image & Status Flags --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-3 border-t border-gray-100 dark:border-gray-700">
            <div>
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Featured Image / Banner</label>
                <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                @if(isset($disease) && $disease->image)
                    <div class="mt-2 flex items-center gap-3 p-2 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                        <img src="{{ asset('storage/' . $disease->image) }}" class="w-20 h-12 object-cover rounded-lg">
                        <label class="flex items-center gap-1.5 text-xs text-red-500 cursor-pointer">
                            <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-500 w-3.5 h-3.5">
                            Remove Image
                        </label>
                    </div>
                @endif
            </div>

            <div class="space-y-3">
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1">Publishing & Badges</label>
                <div class="flex items-center gap-4 flex-wrap">
                    <label class="flex items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $disease->is_featured ?? false)) class="rounded border-gray-300 text-purple-600 focus:ring-purple-400 w-4 h-4">
                        <span>⭐ Featured on Homepage</span>
                    </label>

                    <label class="flex items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" name="is_emergency" value="1" @checked(old('is_emergency', $disease->is_emergency ?? false)) class="rounded border-gray-300 text-red-600 focus:ring-red-400 w-4 h-4">
                        <span class="text-red-600 dark:text-red-400 font-semibold">🚨 Emergency Condition</span>
                    </label>
                </div>

                <div class="pt-2">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 block mb-1">Status</label>
                    <select name="status" class="px-3 py-1.5 text-xs rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <option value="published" @selected(old('status', $disease->status ?? 'published') === 'published')>Published (Live on Website)</option>
                        <option value="draft" @selected(old('status', $disease->status ?? 'published') === 'draft')>Draft (In Review)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Modular Mayo Clinic Content Boxes --}}
    <div class="space-y-6">
        {{-- Section 2.1: Overview --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs">2</span>
                Overview / পরিচিতি
                <span x-show="activeTab === 'en'" class="text-[10px] px-2 py-0.5 rounded bg-sky-100 text-sky-800 font-semibold">English</span>
                <span x-show="activeTab === 'bn'" class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold">বাংলা</span>
            </h3>
            <p class="text-xs text-gray-400">Clear, easy-to-understand summary explaining what the disease is.</p>

            <div x-show="activeTab === 'en'">
                <textarea name="overview[en]" class="tinymce-editor w-full">{{ old('overview.en', isset($disease) ? $disease->getTranslation('overview', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'" style="display: none;">
                <textarea name="overview[bn]" class="tinymce-editor w-full">{{ old('overview.bn', isset($disease) ? $disease->getTranslation('overview', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Section 2.2: Symptoms Summary --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs">3</span>
                Symptoms & Signs / লক্ষণ ও উপসর্গসমূহ
                <span x-show="activeTab === 'en'" class="text-[10px] px-2 py-0.5 rounded bg-sky-100 text-sky-800 font-semibold">English</span>
                <span x-show="activeTab === 'bn'" class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold">বাংলা</span>
            </h3>
            <p class="text-xs text-gray-400">Bulleted checklist of primary and secondary symptoms.</p>

            <div x-show="activeTab === 'en'">
                <textarea name="symptoms_summary[en]" class="tinymce-editor w-full">{{ old('symptoms_summary.en', isset($disease) ? $disease->getTranslation('symptoms_summary', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'" style="display: none;">
                <textarea name="symptoms_summary[bn]" class="tinymce-editor w-full">{{ old('symptoms_summary.bn', isset($disease) ? $disease->getTranslation('symptoms_summary', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Section 2.3: Causes & Triggers --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs">4</span>
                Causes & Triggers / কারণসমূহ
                <span x-show="activeTab === 'en'" class="text-[10px] px-2 py-0.5 rounded bg-sky-100 text-sky-800 font-semibold">English</span>
                <span x-show="activeTab === 'bn'" class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold">বাংলা</span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="causes[en]" class="tinymce-editor w-full">{{ old('causes.en', isset($disease) ? $disease->getTranslation('causes', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'" style="display: none;">
                <textarea name="causes[bn]" class="tinymce-editor w-full">{{ old('causes.bn', isset($disease) ? $disease->getTranslation('causes', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Section 2.4: Risk Factors --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs">5</span>
                Risk Factors / ঝুঁকির বিষয়সমূহ
                <span x-show="activeTab === 'en'" class="text-[10px] px-2 py-0.5 rounded bg-sky-100 text-sky-800 font-semibold">English</span>
                <span x-show="activeTab === 'bn'" class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold">বাংলা</span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="risk_factors[en]" class="tinymce-editor w-full">{{ old('risk_factors.en', isset($disease) ? $disease->getTranslation('risk_factors', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'" style="display: none;">
                <textarea name="risk_factors[bn]" class="tinymce-editor w-full">{{ old('risk_factors.bn', isset($disease) ? $disease->getTranslation('risk_factors', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Section 2.5: Complications --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs">6</span>
                Complications / সম্ভাব্য জটিলতা
                <span x-show="activeTab === 'en'" class="text-[10px] px-2 py-0.5 rounded bg-sky-100 text-sky-800 font-semibold">English</span>
                <span x-show="activeTab === 'bn'" class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold">বাংলা</span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="complications[en]" class="tinymce-editor w-full">{{ old('complications.en', isset($disease) ? $disease->getTranslation('complications', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'" style="display: none;">
                <textarea name="complications[bn]" class="tinymce-editor w-full">{{ old('complications.bn', isset($disease) ? $disease->getTranslation('complications', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Section 2.6: Prevention & Lifestyle --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs">7</span>
                Prevention & Lifestyle / প্রতিরোধ ও জীবনাচরণ
                <span x-show="activeTab === 'en'" class="text-[10px] px-2 py-0.5 rounded bg-sky-100 text-sky-800 font-semibold">English</span>
                <span x-show="activeTab === 'bn'" class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold">বাংলা</span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="prevention[en]" class="tinymce-editor w-full">{{ old('prevention.en', isset($disease) ? $disease->getTranslation('prevention', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'" style="display: none;">
                <textarea name="prevention[bn]" class="tinymce-editor w-full">{{ old('prevention.bn', isset($disease) ? $disease->getTranslation('prevention', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Section 2.7: When to See a Doctor (Emergency Red-Flags) --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-red-200 dark:border-red-900/50 p-6 space-y-3">
            <h3 class="text-sm font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 flex items-center justify-center text-xs">8</span>
                When to See a Doctor / কখন ডাক্তার দেখাবেন (জরুরি সতর্কতা)
                <span x-show="activeTab === 'en'" class="text-[10px] px-2 py-0.5 rounded bg-red-100 text-red-800 font-semibold">English</span>
                <span x-show="activeTab === 'bn'" class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold">বাংলা</span>
            </h3>
            <p class="text-xs text-gray-400">Critical warning signs that require urgent hospital or clinic care.</p>

            <div x-show="activeTab === 'en'">
                <textarea name="when_to_see_doctor[en]" class="tinymce-editor w-full">{{ old('when_to_see_doctor.en', isset($disease) ? $disease->getTranslation('when_to_see_doctor', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'" style="display: none;">
                <textarea name="when_to_see_doctor[bn]" class="tinymce-editor w-full">{{ old('when_to_see_doctor.bn', isset($disease) ? $disease->getTranslation('when_to_see_doctor', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Section 2.8: Diagnosis & Treatment --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs">9</span>
                Diagnosis & Treatment / রোগ নির্ণয় ও চিকিৎসা পদ্ধতি
                <span x-show="activeTab === 'en'" class="text-[10px] px-2 py-0.5 rounded bg-sky-100 text-sky-800 font-semibold">English</span>
                <span x-show="activeTab === 'bn'" class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold">বাংলা</span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="diagnosis_treatment[en]" class="tinymce-editor w-full">{{ old('diagnosis_treatment.en', isset($disease) ? $disease->getTranslation('diagnosis_treatment', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'" style="display: none;">
                <textarea name="diagnosis_treatment[bn]" class="tinymce-editor w-full">{{ old('diagnosis_treatment.bn', isset($disease) ? $disease->getTranslation('diagnosis_treatment', 'bn', false) : '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- 3. Symptoms Linker (For Symptom Checker Engine) --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
        <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">10</span>
            Connect Symptoms to Disease (Symptom Checker Engine)
        </h3>
        <p class="text-xs text-gray-400">Select symptoms associated with this condition and assign match weights (1-10) for symptom checker calculation.</p>

        @php
            $attachedSymptoms = isset($disease) ? $disease->symptoms->keyBy('id') : collect();
        @endphp

        <div class="max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50/50 dark:bg-gray-700/30">
            @foreach($symptoms as $sym)
                @php
                    $isAttached = $attachedSymptoms->has($sym->id);
                    $pivot = $isAttached ? $attachedSymptoms->get($sym->id)->pivot : null;
                @endphp
                <div class="p-3 flex items-center justify-between gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <label class="flex items-center gap-3 cursor-pointer flex-1">
                        <input type="checkbox" name="symptoms[{{ $sym->id }}][selected]" value="1" @checked(old("symptoms.{$sym->id}.selected", $isAttached)) class="rounded border-gray-300 text-sky-600 focus:ring-sky-400 w-4 h-4">
                        <div>
                            <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $sym->getTranslation('name', 'en') }}</span>
                            <span class="text-xs text-emerald-600 dark:text-emerald-400 ml-1">({{ $sym->getTranslation('name', 'bn') }})</span>
                            @if($sym->bodyPart)
                                <span class="text-[10px] text-gray-400 ml-2">[{{ $sym->bodyPart->getTranslation('name', 'en') }}]</span>
                            @endif
                        </div>
                    </label>

                    <div class="flex items-center gap-3 flex-shrink-0">
                        <label class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                            <input type="checkbox" name="symptoms[{{ $sym->id }}][is_primary]" value="1" @checked(old("symptoms.{$sym->id}.is_primary", $pivot ? $pivot->is_primary : true)) class="rounded border-gray-300 text-purple-600 w-3.5 h-3.5">
                            Primary Symptom
                        </label>
                        <select name="symptoms[{{ $sym->id }}][match_weight]" class="px-2 py-1 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            @for($w = 10; $w >= 1; $w--)
                                <option value="{{ $w }}" @selected(old("symptoms.{$sym->id}.match_weight", $pivot ? $pivot->match_weight : 10) == $w)>
                                    Weight: {{ $w }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 4. References & Sources (Mayo Clinic, WHO, WebMD Citations) --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">11</span>
                Authoritative References & Sources (তথ্যসূত্র / সোর্স)
            </h3>
            <button type="button" @click="addReference()" class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold hover:bg-emerald-100 flex items-center gap-1">
                + Add Another Source
            </button>
        </div>
        <p class="text-xs text-gray-400">Cite medical sources (like Mayo Clinic, WHO, WebMD, NHS) to boost Google E-E-A-T and trustworthiness.</p>

        <div class="space-y-3">
            <template x-for="(ref, index) in references" :key="index">
                <div class="p-3.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/40 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    <div class="sm:col-span-4">
                        <label class="text-[10px] uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 block mb-1">Source Name</label>
                        <input type="text" :name="`references[${index}][source_name]`" x-model="ref.source_name" placeholder="e.g. Mayo Clinic / WHO" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                    </div>
                    <div class="sm:col-span-5">
                        <label class="text-[10px] uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 block mb-1">Article / Reference URL</label>
                        <input type="url" :name="`references[${index}][source_url]`" x-model="ref.source_url" placeholder="https://www.mayoclinic.org/..." class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 font-mono">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-[10px] uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400 block mb-1">Notes (Optional)</label>
                        <input type="text" :name="`references[${index}][notes]`" x-model="ref.notes" placeholder="e.g. Symptoms" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                    </div>
                    <div class="sm:col-span-1 text-right pt-3 sm:pt-0">
                        <button type="button" @click="removeReference(index)" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg" title="Remove Source">
                            ✕
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- 5. Bilingual SEO Optimization Box --}}
    @include('admin.shared._seo_fields', ['model' => $disease ?? null])

    {{-- Submit Buttons --}}
    <div class="flex items-center gap-3 pt-3 flex-wrap">
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white text-sm font-semibold hover:opacity-90 shadow transition-all">
            {{ isset($disease) ? 'Update Disease Guide' : 'Publish Disease Guide' }}
        </button>
        <a href="{{ route('admin.diseases.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Cancel
        </a>
    </div>
</div>
