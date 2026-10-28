@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init({
            selector: '.tinymce-editor',
            height: 280,
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

@php
    $redFlagsInit = old('red_flags', isset($disease) && is_array($disease->red_flags) ? $disease->red_flags : []);
    if (empty($redFlagsInit)) {
        $redFlagsInit = [['en' => '', 'bn' => '']];
    }

    $testsInit = old('diagnosis_tests', isset($disease) && is_array($disease->diagnosis_tests) ? $disease->diagnosis_tests : []);
    if (empty($testsInit)) {
        $testsInit = [['name_en' => '', 'name_bn' => '', 'desc_en' => '', 'desc_bn' => '']];
    }

    $treatmentsInit = old('treatments', isset($disease) && is_array($disease->treatments) ? $disease->treatments : []);
    if (empty($treatmentsInit)) {
        $treatmentsInit = [['category_en' => '', 'category_bn' => '', 'name_en' => '', 'name_bn' => '', 'desc_en' => '', 'desc_bn' => '']];
    }

    $medicinesInit = old('medicines', isset($disease) && is_array($disease->medicines) ? $disease->medicines : []);
    if (empty($medicinesInit)) {
        $medicinesInit = [['type' => 'prescription', 'name_en' => '', 'name_bn' => '', 'dosage_notes_en' => '', 'dosage_notes_bn' => '']];
    }

    $proceduresInit = old('procedures', isset($disease) && is_array($disease->procedures) ? $disease->procedures : []);
    if (empty($proceduresInit)) {
        $proceduresInit = [['name_en' => '', 'name_bn' => '', 'desc_en' => '', 'desc_bn' => '']];
    }

    $faqsInit = old('faqs', isset($disease) && is_array($disease->faqs) ? $disease->faqs : []);
    if (empty($faqsInit)) {
        $faqsInit = [['question_en' => '', 'question_bn' => '', 'answer_en' => '', 'answer_bn' => '']];
    }

    $referencesInit = isset($disease) && $disease->references->count() > 0 
        ? $disease->references->map(function($r){ return ['source_name' => $r->source_name, 'source_url' => $r->source_url, 'notes' => $r->notes]; })->toArray() 
        : [['source_name' => 'Mayo Clinic', 'source_url' => '', 'notes' => 'Clinical Overview']];
@endphp

<div x-data="{
    activeTab: 'en',
    currentSection: 'basic',
    redFlags: {{ json_encode($redFlagsInit) }},
    tests: {{ json_encode($testsInit) }},
    treatments: {{ json_encode($treatmentsInit) }},
    medicines: {{ json_encode($medicinesInit) }},
    procedures: {{ json_encode($proceduresInit) }},
    faqs: {{ json_encode($faqsInit) }},
    references: {{ json_encode($referencesInit) }},

    addRedFlag() { this.redFlags.push({ en: '', bn: '' }); },
    removeRedFlag(i) { this.redFlags.splice(i, 1); },

    addTest() { this.tests.push({ name_en: '', name_bn: '', desc_en: '', desc_bn: '' }); },
    removeTest(i) { this.tests.splice(i, 1); },

    addTreatment() { this.treatments.push({ category_en: '', category_bn: '', name_en: '', name_bn: '', desc_en: '', desc_bn: '' }); },
    removeTreatment(i) { this.treatments.splice(i, 1); },

    addMedicine() { this.medicines.push({ type: 'prescription', name_en: '', name_bn: '', dosage_notes_en: '', dosage_notes_bn: '' }); },
    removeMedicine(i) { this.medicines.splice(i, 1); },

    addProcedure() { this.procedures.push({ name_en: '', name_bn: '', desc_en: '', desc_bn: '' }); },
    removeProcedure(i) { this.procedures.splice(i, 1); },

    addFaq() { this.faqs.push({ question_en: '', question_bn: '', answer_en: '', answer_bn: '' }); },
    removeFaq(i) { this.faqs.splice(i, 1); },

    addReference() { this.references.push({ source_name: '', source_url: '', notes: '' }); },
    removeReference(i) { this.references.splice(i, 1); }
}" class="space-y-6">

    {{-- Top Sticky Navigation & Language Selector --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sticky top-16 z-30 backdrop-blur-md bg-white/95 dark:bg-gray-800/95 flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        {{-- Section Quick Jumps --}}
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
            <button type="button" @click="currentSection = 'basic'" :class="currentSection === 'basic' ? 'bg-sky-600 text-white font-bold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap">
                1. Basic Info
            </button>
            <button type="button" @click="currentSection = 'symptoms'" :class="currentSection === 'symptoms' ? 'bg-sky-600 text-white font-bold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap">
                2. Symptoms & Red Flags
            </button>
            <button type="button" @click="currentSection = 'causes'" :class="currentSection === 'causes' ? 'bg-sky-600 text-white font-bold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap">
                3. Causes & Risks
            </button>
            <button type="button" @click="currentSection = 'diagnosis'" :class="currentSection === 'diagnosis' ? 'bg-sky-600 text-white font-bold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap">
                4. Diagnosis & Tests
            </button>
            <button type="button" @click="currentSection = 'treatments'" :class="currentSection === 'treatments' ? 'bg-sky-600 text-white font-bold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap">
                5. Treatments & Drugs
            </button>
            <button type="button" @click="currentSection = 'lifestyle'" :class="currentSection === 'lifestyle' ? 'bg-sky-600 text-white font-bold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap">
                6. Lifestyle & Coping
            </button>
            <button type="button" @click="currentSection = 'reviewers'" :class="currentSection === 'reviewers' ? 'bg-sky-600 text-white font-bold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap">
                7. Doctor & Reviewer
            </button>
            <button type="button" @click="currentSection = 'faqs_seo'" :class="currentSection === 'faqs_seo' ? 'bg-sky-600 text-white font-bold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap">
                8. FAQs & SEO
            </button>
        </div>

        {{-- Bilingual Language Toggle --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Language:</span>
            <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-700/60 rounded-xl">
                <button type="button" @click="activeTab = 'en'" :class="activeTab === 'en' ? 'bg-white dark:bg-gray-800 text-sky-600 dark:text-sky-400 shadow-sm font-bold' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'" class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5">
                    🇺🇸 English
                </button>
                <button type="button" @click="activeTab = 'bn'" :class="activeTab === 'bn' ? 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-sm font-bold' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'" class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5">
                    🇧🇩 বাংলা (Bengali)
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 1: BASIC INFORMATION & TAXONOMY --}}
    {{-- ========================================================================= --}}
    <div x-show="currentSection === 'basic'" class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs">1</span>
                Basic Disease Identification & Taxonomy
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- English Name --}}
                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Disease Name (English) *</label>
                    <input type="text" name="name[en]" required value="{{ old('name.en', isset($disease) ? $disease->getTranslation('name', 'en', false) : '') }}" placeholder="e.g. Migraine, Hypertension"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    @error('name.en')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Bengali Name --}}
                <div>
                    <label class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 block mb-1.5">রোগের নাম (বাংলা)</label>
                    <input type="text" name="name[bn]" value="{{ old('name.bn', isset($disease) ? $disease->getTranslation('name', 'bn', false) : '') }}" placeholder="যেমন: মাইগ্রেন, উচ্চ রক্তচাপ"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-emerald-200 dark:border-emerald-700 bg-emerald-50/40 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                </div>

                {{-- Custom Slug --}}
                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">URL Slug (e.g. migraine)</label>
                    <input type="text" name="slug" value="{{ old('slug', $disease->slug ?? '') }}" placeholder="e.g. migraine"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-300 font-mono text-xs">
                    @error('slug')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Body System / Part --}}
                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Body System (শারীরিক অঙ্গ / তন্ত্র)</label>
                    <select name="body_system_id" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-300">
                        <option value="">Select Body System (e.g. Brain & Nerves, Heart)</option>
                        @foreach($bodySystems as $bs)
                            <option value="{{ $bs->id }}" @selected(old('body_system_id', $disease->body_system_id ?? '') == $bs->id)>
                                {{ $bs->getTranslation('name', 'en') }} ({{ $bs->getTranslation('name', 'bn') }})
                            </option>
                        @endforeach
                    </select>
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
                </div>

                {{-- Aliases / Also Known As --}}
                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Aliases / Alternative Names (Comma Separated)</label>
                    @php
                        $aliasesVal = isset($disease) && is_array($disease->aliases) ? implode(', ', $disease->aliases) : (old('aliases') ? (is_array(old('aliases')) ? implode(', ', old('aliases')) : old('aliases')) : '');
                    @endphp
                    <input type="text" name="aliases" value="{{ $aliasesVal }}" placeholder="e.g. Hemicrania, Sick headache"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    <p class="text-[11px] text-gray-400 mt-1">Searchers using these alternative names or abbreviations will find this page.</p>
                </div>
            </div>

            {{-- Short Description (EN & BN) --}}
            <div class="pt-2 border-t border-gray-100 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1">Short Description / Quick Summary (English)</label>
                    <textarea name="short_description[en]" rows="2" placeholder="1-2 sentence quick summary for search results & cards" class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-100">{{ old('short_description.en', isset($disease) ? $disease->getTranslation('short_description', 'en', false) : '') }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 block mb-1">সংক্ষিপ্ত বিবরণ (বাংলা)</label>
                    <textarea name="short_description[bn]" rows="2" placeholder="১-২ বাক্যে সংক্ষেপিত পরিচিতি" class="w-full px-3.5 py-2 text-xs rounded-xl border border-emerald-200 dark:border-emerald-700 bg-emerald-50/40 dark:bg-gray-700 text-gray-800 dark:text-gray-100">{{ old('short_description.bn', isset($disease) ? $disease->getTranslation('short_description', 'bn', false) : '') }}</textarea>
                </div>
            </div>

            {{-- Secondary Specialties Multi-Select --}}
            <div>
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Secondary Related Specialties (Optional)</label>
                <div class="max-h-32 overflow-y-auto p-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
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

            {{-- Featured Image, Alt & Status Flags --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-3 border-t border-gray-100 dark:border-gray-700">
                <div class="space-y-3">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block">Featured Image / Banner</label>
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
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <input type="text" name="featured_image_alt[en]" value="{{ old('featured_image_alt.en', isset($disease) ? $disease->getTranslation('featured_image_alt', 'en', false) : '') }}" placeholder="Image Alt Text (EN)" class="px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                        <input type="text" name="featured_image_alt[bn]" value="{{ old('featured_image_alt.bn', isset($disease) ? $disease->getTranslation('featured_image_alt', 'bn', false) : '') }}" placeholder="ইমেজ অল্ট টেক্সট (BN)" class="px-2.5 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1">Publishing Status & Badges</label>
                    <div class="flex items-center gap-4 flex-wrap">
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $disease->is_featured ?? false)) class="rounded border-gray-300 text-purple-600 focus:ring-purple-400 w-4 h-4">
                            <span>⭐ Featured Disease</span>
                        </label>

                        <label class="flex items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input type="checkbox" name="is_emergency" value="1" @checked(old('is_emergency', $disease->is_emergency ?? false)) class="rounded border-gray-300 text-red-600 focus:ring-red-400 w-4 h-4">
                            <span class="text-red-600 dark:text-red-400 font-semibold">🚨 Emergency Notice</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 block mb-1">Status (Draft / Published)</label>
                        <select name="status" class="px-3 py-1.5 text-xs rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            <option value="draft" @selected(old('status', $disease->status ?? 'draft') === 'draft')>Draft (In Review / Incomplete)</option>
                            <option value="published" @selected(old('status', $disease->status ?? 'draft') === 'published')>Published (Live on Website)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 2: SYMPTOMS & RED FLAGS --}}
    {{-- ========================================================================= --}}
    <div x-show="currentSection === 'symptoms'" class="space-y-6" style="display: none;">
        
        {{-- Overview Editor --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Overview / পরিচিতি (Clinical Summary)</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="overview[en]" class="tinymce-editor w-full">{{ old('overview.en', isset($disease) ? $disease->getTranslation('overview', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="overview[bn]" class="tinymce-editor w-full">{{ old('overview.bn', isset($disease) ? $disease->getTranslation('overview', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Symptoms Summary Editor --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Symptoms & Signs / লক্ষণ ও উপসর্গসমূহ</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="symptoms_summary[en]" class="tinymce-editor w-full">{{ old('symptoms_summary.en', isset($disease) ? $disease->getTranslation('symptoms_summary', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="symptoms_summary[bn]" class="tinymce-editor w-full">{{ old('symptoms_summary.bn', isset($disease) ? $disease->getTranslation('symptoms_summary', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Symptoms Detailed / Stages --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Symptoms Detailed & Stages / ধাপভিত্তিক বিস্তারিত লক্ষণ (Optional)</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="symptoms_detailed[en]" class="tinymce-editor w-full">{{ old('symptoms_detailed.en', isset($disease) ? $disease->getTranslation('symptoms_detailed', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="symptoms_detailed[bn]" class="tinymce-editor w-full">{{ old('symptoms_detailed.bn', isset($disease) ? $disease->getTranslation('symptoms_detailed', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Red Flags Repeater (Critical Warning Signs) --}}
        <div class="bg-red-50/40 dark:bg-red-950/20 rounded-2xl border border-red-200 dark:border-red-900/40 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
                        <span>🚨 Red Flags / বিপদচিহ্ন ও জরুরি সতর্কতা</span>
                    </h3>
                    <p class="text-xs text-gray-500">Critical warning signs requiring immediate emergency room attention.</p>
                </div>
                <button type="button" @click="addRedFlag()" class="px-3 py-1.5 rounded-xl bg-red-100 text-red-700 text-xs font-semibold hover:bg-red-200">
                    + Add Warning Sign
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(rf, index) in redFlags" :key="index">
                    <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-red-100 dark:border-red-900/30 flex items-center gap-3">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" :name="`red_flags[${index}][en]`" x-model="rf.en" placeholder="Emergency sign (English)" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-700">
                            <input type="text" :name="`red_flags[${index}][bn]`" x-model="rf.bn" placeholder="জরুরি বিপদচিহ্ন (বাংলা)" class="px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">
                        </div>
                        <button type="button" @click="removeRedFlag(index)" class="text-red-500 hover:text-red-700 p-1 text-sm font-bold">✕</button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Connected Symptoms (Symptom Checker Engine) --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">🔗</span>
                Connect Symptoms to Disease (Symptom Checker Matcher)
            </h3>
            <p class="text-xs text-gray-400">Select symptoms and assign match weights (1-10) for the DoctorBD24 Symptom Checker algorithm.</p>

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
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 3: CAUSES, RISKS & COMPLICATIONS --}}
    {{-- ========================================================================= --}}
    <div x-show="currentSection === 'causes'" class="space-y-6" style="display: none;">
        
        {{-- Causes & Triggers --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Causes & Triggers / কারণসমূহ ও উদ্দীপক</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="causes[en]" class="tinymce-editor w-full">{{ old('causes.en', isset($disease) ? $disease->getTranslation('causes', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="causes[bn]" class="tinymce-editor w-full">{{ old('causes.bn', isset($disease) ? $disease->getTranslation('causes', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Risk Factors --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Risk Factors / ঝুঁকির বিষয়সমূহ</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="risk_factors[en]" class="tinymce-editor w-full">{{ old('risk_factors.en', isset($disease) ? $disease->getTranslation('risk_factors', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="risk_factors[bn]" class="tinymce-editor w-full">{{ old('risk_factors.bn', isset($disease) ? $disease->getTranslation('risk_factors', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Complications --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Complications / সম্ভাব্য জটিলতা</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="complications[en]" class="tinymce-editor w-full">{{ old('complications.en', isset($disease) ? $disease->getTranslation('complications', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="complications[bn]" class="tinymce-editor w-full">{{ old('complications.bn', isset($disease) ? $disease->getTranslation('complications', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Prevention --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Prevention / প্রতিরোধ</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="prevention[en]" class="tinymce-editor w-full">{{ old('prevention.en', isset($disease) ? $disease->getTranslation('prevention', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="prevention[bn]" class="tinymce-editor w-full">{{ old('prevention.bn', isset($disease) ? $disease->getTranslation('prevention', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- When to See a Doctor --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>When to See a Doctor / কখন ডাক্তার দেখাবেন</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="when_to_see_doctor[en]" class="tinymce-editor w-full">{{ old('when_to_see_doctor.en', isset($disease) ? $disease->getTranslation('when_to_see_doctor', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="when_to_see_doctor[bn]" class="tinymce-editor w-full">{{ old('when_to_see_doctor.bn', isset($disease) ? $disease->getTranslation('when_to_see_doctor', 'bn', false) : '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 4: DIAGNOSIS & TESTS --}}
    {{-- ========================================================================= --}}
    <div x-show="currentSection === 'diagnosis'" class="space-y-6" style="display: none;">
        
        {{-- Diagnosis Overview --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Diagnosis Overview / রোগ নির্ণয় পদ্ধতি সারসংক্ষেপ</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="diagnosis_overview[en]" class="tinymce-editor w-full">{{ old('diagnosis_overview.en', isset($disease) ? $disease->getTranslation('diagnosis_overview', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="diagnosis_overview[bn]" class="tinymce-editor w-full">{{ old('diagnosis_overview.bn', isset($disease) ? $disease->getTranslation('diagnosis_overview', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Diagnostic Tests Repeater --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-sky-700 dark:text-sky-400">Diagnostic Tests & Procedures (ডায়াগনস্টিক টেস্ট তালিকা)</h3>
                    <p class="text-xs text-gray-400">List specific blood tests, imaging (MRI, CT, Ultrasound), biopsies, or endoscopic exams.</p>
                </div>
                <button type="button" @click="addTest()" class="px-3 py-1.5 rounded-xl bg-sky-50 text-sky-700 text-xs font-semibold hover:bg-sky-100">
                    + Add Test
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(test, index) in tests" :key="index">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-500 font-mono">Test #<span x-text="index + 1"></span></span>
                            <button type="button" @click="removeTest(index)" class="text-red-500 hover:text-red-700 text-xs font-bold">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 block mb-1">Test Name (EN)</label>
                                <input type="text" :name="`diagnosis_tests[${index}][name_en]`" x-model="test.name_en" placeholder="e.g. MRI scan of the brain" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block mb-1">টেস্টের নাম (BN)</label>
                                <input type="text" :name="`diagnosis_tests[${index}][name_bn]`" x-model="test.name_bn" placeholder="যেমন: ব্রেন এমআরআই (MRI)" class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 block mb-1">Purpose / Details (EN)</label>
                                <input type="text" :name="`diagnosis_tests[${index}][desc_en]`" x-model="test.desc_en" placeholder="Detects tumors, bleeding, stroke or structural abnormalities" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block mb-1">উদ্দেশ্য / বিবরণ (BN)</label>
                                <input type="text" :name="`diagnosis_tests[${index}][desc_bn]`" x-model="test.desc_bn" placeholder="মস্তিষ্কের রক্তক্ষরণ, টিউমার বা স্নায়ুর বিশদ চিত্র দেখতে" class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 5: TREATMENTS & MEDICINES --}}
    {{-- ========================================================================= --}}
    <div x-show="currentSection === 'treatments'" class="space-y-6" style="display: none;">
        
        {{-- Treatment Overview --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Treatment Overview / চিকিৎসা পদ্ধতির সারসংক্ষেপ</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="treatment_overview[en]" class="tinymce-editor w-full">{{ old('treatment_overview.en', isset($disease) ? $disease->getTranslation('treatment_overview', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="treatment_overview[bn]" class="tinymce-editor w-full">{{ old('treatment_overview.bn', isset($disease) ? $disease->getTranslation('treatment_overview', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Treatments Repeater --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-indigo-700 dark:text-indigo-400">Treatment Strategies & Regimens (চিকিৎসা পদ্ধতিসমূহ)</h3>
                    <p class="text-xs text-gray-400">Acute abortive treatments, preventive therapy, or procedural interventions.</p>
                </div>
                <button type="button" @click="addTreatment()" class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-semibold hover:bg-indigo-100">
                    + Add Treatment
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(tr, index) in treatments" :key="index">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-500 font-mono">Treatment Strategy #<span x-text="index + 1"></span></span>
                            <button type="button" @click="removeTreatment(index)" class="text-red-500 hover:text-red-700 text-xs font-bold">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 block mb-1">Category (EN)</label>
                                <input type="text" :name="`treatments[${index}][category_en]`" x-model="tr.category_en" placeholder="e.g. Acute Relief / Preventive" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block mb-1">ক্যাটাগরি (BN)</label>
                                <input type="text" :name="`treatments[${index}][category_bn]`" x-model="tr.category_bn" placeholder="যেমন: তাৎক্ষণিক উপশম / দীর্ঘমেয়াদি প্রতিরোধ" class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 block mb-1">Treatment Name (EN)</label>
                                <input type="text" :name="`treatments[${index}][name_en]`" x-model="tr.name_en" placeholder="e.g. Triptans Therapy" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block mb-1">চিকিৎসার নাম (BN)</label>
                                <input type="text" :name="`treatments[${index}][name_bn]`" x-model="tr.name_bn" placeholder="যেমন: ট্রিপট্যান জাতীয় ওষুধ থেরাপি" class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">
                            </div>
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <input type="text" :name="`treatments[${index}][desc_en]`" x-model="tr.desc_en" placeholder="Clinical mechanism & instructions (EN)" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                                <input type="text" :name="`treatments[${index}][desc_bn]`" x-model="tr.desc_bn" placeholder="চিকিৎসা পদ্ধতি ও নির্দেশিকা (BN)" class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Medicines Repeater --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-teal-700 dark:text-teal-400">Medicines & Drug Classes (ওষুধের তালিকা)</h3>
                    <p class="text-xs text-gray-400">OTC painkillers, prescription medications, dosages and precautions.</p>
                </div>
                <button type="button" @click="addMedicine()" class="px-3 py-1.5 rounded-xl bg-teal-50 text-teal-700 text-xs font-semibold hover:bg-teal-100">
                    + Add Medicine
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(med, index) in medicines" :key="index">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-gray-500 font-mono">Medicine #<span x-text="index + 1"></span></span>
                                <select :name="`medicines[${index}][type]`" x-model="med.type" class="px-2 py-0.5 text-[11px] rounded border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                                    <option value="otc">Over-the-Counter (OTC)</option>
                                    <option value="prescription">Prescription Only (Rx)</option>
                                </select>
                            </div>
                            <button type="button" @click="removeMedicine(index)" class="text-red-500 hover:text-red-700 text-xs font-bold">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 block mb-1">Generic / Class Name (EN)</label>
                                <input type="text" :name="`medicines[${index}][name_en]`" x-model="med.name_en" placeholder="e.g. Sumatriptan, Zolmitriptan" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block mb-1">ওষুধের নাম (BN)</label>
                                <input type="text" :name="`medicines[${index}][name_bn]`" x-model="med.name_bn" placeholder="যেমন: সুমাট্রিপট্যান, জলমিট্রিপট্যান" class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 block mb-1">Dosage / Precaution Notes (EN)</label>
                                <input type="text" :name="`medicines[${index}][dosage_notes_en]`" x-model="med.dosage_notes_en" placeholder="Take immediately at headache onset. Do not exceed..." class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block mb-1">সেবনবিধি ও সতর্কতা (BN)</label>
                                <input type="text" :name="`medicines[${index}][dosage_notes_bn]`" x-model="med.dosage_notes_bn" placeholder="ব্যথা শুরু হওয়া মাত্র গ্রহণ করুন। ডাক্তারের পরামর্শ ছাড়া..." class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Procedures Repeater --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-purple-700 dark:text-purple-400">Procedures, Injections & Therapies (থেরাপি ও বিশেষ পদ্ধতি)</h3>
                    <p class="text-xs text-gray-400">E.g. Botox injections, nerve blocks, neuromodulation devices, acupuncture.</p>
                </div>
                <button type="button" @click="addProcedure()" class="px-3 py-1.5 rounded-xl bg-purple-50 text-purple-700 text-xs font-semibold hover:bg-purple-100">
                    + Add Procedure
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(proc, index) in procedures" :key="index">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-500 font-mono">Procedure #<span x-text="index + 1"></span></span>
                            <button type="button" @click="removeProcedure(index)" class="text-red-500 hover:text-red-700 text-xs font-bold">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <input type="text" :name="`procedures[${index}][name_en]`" x-model="proc.name_en" placeholder="Procedure Name (EN) e.g. Botox Injections" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <input type="text" :name="`procedures[${index}][name_bn]`" x-model="proc.name_bn" placeholder="পদ্ধতির নাম (BN) যেমন: বোটক্স ইনজেকশন" class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">
                            </div>
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <input type="text" :name="`procedures[${index}][desc_en]`" x-model="proc.desc_en" placeholder="Details & frequency (EN) e.g. Given every 12 weeks for chronic migraine" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                                <input type="text" :name="`procedures[${index}][desc_bn]`" x-model="proc.desc_bn" placeholder="বিবরণ (BN) যেমন: প্রতি ১২ সপ্তাহে ক্রনিক মাইগ্রেনের ক্ষেত্রে প্রয়োগ করা হয়" class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Clinical Trials --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Clinical Trials / ক্লিনিক্যাল ট্রায়াল ও উদ্ভাবন (Optional)</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="clinical_trials[en]" class="tinymce-editor w-full">{{ old('clinical_trials.en', isset($disease) ? $disease->getTranslation('clinical_trials', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="clinical_trials[bn]" class="tinymce-editor w-full">{{ old('clinical_trials.bn', isset($disease) ? $disease->getTranslation('clinical_trials', 'bn', false) : '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 6: LIFESTYLE, COPING & PREPARATION --}}
    {{-- ========================================================================= --}}
    <div x-show="currentSection === 'lifestyle'" class="space-y-6" style="display: none;">
        
        {{-- Lifestyle & Home Remedies --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Lifestyle & Home Remedies / ঘরোয়া যত্ন ও জীবনাচরণ</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="lifestyle_home_remedies[en]" class="tinymce-editor w-full">{{ old('lifestyle_home_remedies.en', isset($disease) ? $disease->getTranslation('lifestyle_home_remedies', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="lifestyle_home_remedies[bn]" class="tinymce-editor w-full">{{ old('lifestyle_home_remedies.bn', isset($disease) ? $disease->getTranslation('lifestyle_home_remedies', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Alternative Medicine --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Alternative Medicine / পরিপূরক ও বিকল্প চিকিৎসা</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="alternative_medicine[en]" class="tinymce-editor w-full">{{ old('alternative_medicine.en', isset($disease) ? $disease->getTranslation('alternative_medicine', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="alternative_medicine[bn]" class="tinymce-editor w-full">{{ old('alternative_medicine.bn', isset($disease) ? $disease->getTranslation('alternative_medicine', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Coping & Support --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Coping & Support / মানসিক সহায়তা ও কেয়ার</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="coping_support[en]" class="tinymce-editor w-full">{{ old('coping_support.en', isset($disease) ? $disease->getTranslation('coping_support', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="coping_support[bn]" class="tinymce-editor w-full">{{ old('coping_support.bn', isset($disease) ? $disease->getTranslation('coping_support', 'bn', false) : '') }}</textarea>
            </div>
        </div>

        {{-- Preparing for Appointment --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                <span>Preparing for Appointment / ডাক্তারের কাছে যাওয়ার প্রস্তুতি</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="activeTab === 'en' ? 'Showing English Editor' : 'Showing Bengali Editor'"></span>
            </h3>
            <div x-show="activeTab === 'en'">
                <textarea name="preparing_for_appointment[en]" class="tinymce-editor w-full">{{ old('preparing_for_appointment.en', isset($disease) ? $disease->getTranslation('preparing_for_appointment', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'">
                <textarea name="preparing_for_appointment[bn]" class="tinymce-editor w-full">{{ old('preparing_for_appointment.bn', isset($disease) ? $disease->getTranslation('preparing_for_appointment', 'bn', false) : '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 7: DOCTOR, REVIEWER & REFERENCES --}}
    {{-- ========================================================================= --}}
    <div x-show="currentSection === 'reviewers'" class="space-y-6" style="display: none;">
        
        {{-- Medical Reviewer Doctor (E-E-A-T) --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">🩺</span>
                Medical Reviewer & E-E-A-T Trust Credential
            </h3>
            <p class="text-xs text-gray-400">Assign a registered specialist doctor from the directory who clinically reviewed this content.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Reviewer Doctor</label>
                    <select name="reviewer_doctor_id" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-300">
                        <option value="">No Doctor Reviewer Assigned</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" @selected(old('reviewer_doctor_id', $disease->reviewer_doctor_id ?? '') == $doc->id)>
                                {{ $doc->getTranslation('name', 'en') }} ({{ $doc->primarySpecialty?->getTranslation('name', 'en') ?? 'Specialist' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Last Clinically Reviewed Date</label>
                    <input type="date" name="last_reviewed_at" value="{{ old('last_reviewed_at', isset($disease) && $disease->last_reviewed_at ? $disease->last_reviewed_at->format('Y-m-d') : date('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-300">
                </div>
            </div>

            {{-- Related Conditions Slugs --}}
            <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1">Related Condition Slugs (Comma Separated)</label>
                @php
                    $relCond = isset($disease) && is_array($disease->related_conditions) ? implode(', ', $disease->related_conditions) : (is_array(old('related_conditions')) ? implode(', ', old('related_conditions')) : old('related_conditions', ''));
                @endphp
                <input type="text" name="related_conditions" value="{{ $relCond }}" placeholder="e.g. tension-headache, cluster-headache, sinusitis" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
            </div>

            {{-- Related Hospitals Picker --}}
            @php
                $hospitalsList = $hospitals ?? \App\Models\Hospital::orderBy('name')->get();
                $selectedHospitals = old('related_hospitals', isset($disease) && is_array($disease->related_hospitals) ? $disease->related_hospitals : []);
            @endphp
            <div class="pt-3 border-t border-gray-100 dark:border-gray-700 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                        <span>🏥 Related Hospitals & Clinics (হাসপাতাল নির্বাচন করুন)</span>
                    </label>
                    <span class="text-[11px] text-gray-400">Select specialized centers treating this condition</span>
                </div>
                <div class="max-h-40 overflow-y-auto p-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($hospitalsList as $h)
                        <label class="flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer hover:text-sky-600 p-1 rounded hover:bg-white dark:hover:bg-gray-700">
                            <input type="checkbox" name="related_hospitals[]" value="{{ $h->id }}" @checked(in_array($h->id, $selectedHospitals)) class="rounded border-gray-300 text-sky-600 focus:ring-sky-400 w-3.5 h-3.5">
                            <span class="truncate">{{ $h->getTranslation('name', 'en') }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Related Health Articles (Blog Posts) Picker --}}
            @php
                $articlesList = $articles ?? \App\Models\BlogPost::where('status', 'published')->orderBy('title')->get();
                $selectedArticles = old('related_articles', isset($disease) && is_array($disease->related_articles) ? $disease->related_articles : []);
            @endphp
            <div class="pt-3 border-t border-gray-100 dark:border-gray-700 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                        <span>📰 Related Health Articles & Guides (সম্পর্কিত ব্লগ আর্টিকেল নির্বাচন করুন)</span>
                    </label>
                    <span class="text-[11px] text-gray-400">Select published health articles related to this condition</span>
                </div>
                <div class="max-h-40 overflow-y-auto p-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($articlesList as $art)
                        <label class="flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer hover:text-sky-600 p-1 rounded hover:bg-white dark:hover:bg-gray-700">
                            <input type="checkbox" name="related_articles[]" value="{{ $art->id }}" @checked(in_array($art->id, $selectedArticles)) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400 w-3.5 h-3.5">
                            <span class="truncate">{{ $art->getTranslation('title', 'en') }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Disclaimer Override --}}
            <div class="pt-2 border-t border-gray-100 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1">Custom Disclaimer Override (EN - Optional)</label>
                    <textarea name="disclaimer[en]" rows="2" placeholder="Leave blank to use default DoctorBD24 medical disclaimer" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">{{ old('disclaimer.en', isset($disease) ? $disease->getTranslation('disclaimer', 'en', false) : '') }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 block mb-1">কাস্টম ডিসক্লেইমার (BN - ঐচ্ছিক)</label>
                    <textarea name="disclaimer[bn]" rows="2" placeholder="খালি রাখলে ডিফল্ট মেডিকেল ডিসক্লেইমার প্রদর্শিত হবে" class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">{{ old('disclaimer.bn', isset($disease) ? $disease->getTranslation('disclaimer', 'bn', false) : '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Authoritative References & Citations --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">📚</span>
                        Authoritative Medical References & Citations (তথ্যসূত্র)
                    </h3>
                    <p class="text-xs text-gray-400">Cite Mayo Clinic, WHO, BMJ, PubMed or NHS guidelines to satisfy Google Medical Guidelines.</p>
                </div>
                <button type="button" @click="addReference()" class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold hover:bg-emerald-100 flex items-center gap-1">
                    + Add Source
                </button>
            </div>

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
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 8: FAQS & SEO --}}
    {{-- ========================================================================= --}}
    <div x-show="currentSection === 'faqs_seo'" class="space-y-6" style="display: none;">
        
        {{-- Frequently Asked Questions (FAQPage Schema) --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">❓</span>
                        Frequently Asked Questions (FAQ) - Google Rich Snippets
                    </h3>
                    <p class="text-xs text-gray-400">These questions and answers automatically power the Google FAQPage schema.</p>
                </div>
                <button type="button" @click="addFaq()" class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 text-xs font-semibold hover:bg-amber-100">
                    + Add FAQ
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(faq, index) in faqs" :key="index">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-500 font-mono">FAQ Question #<span x-text="index + 1"></span></span>
                            <button type="button" @click="removeFaq(index)" class="text-red-500 hover:text-red-700 text-xs font-bold">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 block mb-1">Question (EN)</label>
                                <input type="text" :name="`faqs[${index}][question_en]`" x-model="faq.question_en" placeholder="e.g. Can migraine be permanently cured?" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block mb-1">প্রশ্ন (BN)</label>
                                <input type="text" :name="`faqs[${index}][question_bn]`" x-model="faq.question_bn" placeholder="যেমন: মাইগ্রেন কি পুরোপুরি সেরে যায়?" class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-600 dark:text-gray-300 block mb-1">Answer (EN)</label>
                                <textarea :name="`faqs[${index}][answer_en]`" x-model="faq.answer_en" rows="2" placeholder="Clear, concise medical answer..." class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700"></textarea>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block mb-1">উত্তর (BN)</label>
                                <textarea :name="`faqs[${index}][answer_bn]`" x-model="faq.answer_bn" rows="2" placeholder="সহজবোধ্য উত্তর..." class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700"></textarea>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Bilingual SEO Optimization Box --}}
        @include('admin.shared._seo_fields', ['model' => $disease ?? null])
    </div>

    {{-- Bottom Submit Action Bar --}}
    <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700 flex-wrap">
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white text-sm font-semibold hover:opacity-90 shadow transition-all">
            {{ isset($disease) ? 'Update Disease Guide' : 'Publish Disease Guide' }}
        </button>
        <a href="{{ route('admin.diseases.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Cancel
        </a>
    </div>
</div>
