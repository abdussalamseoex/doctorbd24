<div x-data="{
    activeTab: 'en',
    factors: {{ isset($symptom) && $symptom->factors->count() > 0 ? json_encode($symptom->factors->map(function($f){ return ['factor_type' => $f->factor_type, 'question' => ['en' => $f->getTranslation('question', 'en', false), 'bn' => $f->getTranslation('question', 'bn', false)], 'options' => $f->options ?? []]; })) : json_encode([['factor_type' => 'quality', 'question' => ['en' => '', 'bn' => ''], 'options' => [['text_en' => '', 'text_bn' => '', 'red_flag' => false, 'score' => 5]]]]) }},
    addFactor() {
        this.factors.push({
            factor_type: 'accompanying',
            question: { en: '', bn: '' },
            options: [{ text_en: '', text_bn: '', red_flag: false, score: 5 }]
        });
    },
    removeFactor(index) {
        this.factors.splice(index, 1);
    },
    addOption(factorIndex) {
        this.factors[factorIndex].options.push({ text_en: '', text_bn: '', red_flag: false, score: 5 });
    },
    removeOption(factorIndex, optIndex) {
        this.factors[factorIndex].options.splice(optIndex, 1);
    }
}" class="space-y-6">

    {{-- Top Language Switcher Bar --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 flex items-center justify-between gap-3 sticky top-16 z-30 backdrop-blur-md bg-white/95 dark:bg-gray-800/95">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Language:</span>
            <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-700/60 rounded-xl">
                <button type="button" @click="activeTab = 'en'" :class="activeTab === 'en' ? 'bg-white dark:bg-gray-800 text-purple-600 dark:text-purple-400 shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all">
                    🇺🇸 English
                </button>
                <button type="button" @click="activeTab = 'bn'" :class="activeTab === 'bn' ? 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all">
                    🇧🇩 বাংলা (Bengali)
                </button>
            </div>
        </div>
    </div>

    {{-- 1. Basic Symptom Details --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
        <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs">1</span>
            Symptom Definition
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Symptom Name (English) *</label>
                <input type="text" name="name[en]" required value="{{ old('name.en', isset($symptom) ? $symptom->getTranslation('name', 'en', false) : '') }}" placeholder="e.g. Headache, Chest Pain, Fever"
                       class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-300">
            </div>

            <div>
                <label class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 block mb-1.5">লক্ষণ বা উপসর্গের নাম (বাংলা)</label>
                <input type="text" name="name[bn]" value="{{ old('name.bn', isset($symptom) ? $symptom->getTranslation('name', 'bn', false) : '') }}" placeholder="যেমন: মাথাব্যথা, বুকে ব্যথা, জ্বর"
                       class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-emerald-200 dark:border-emerald-700 bg-emerald-50/40 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-300">
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">URL Slug (Leave blank for auto-generation)</label>
                <input type="text" name="slug" value="{{ old('slug', $symptom->slug ?? '') }}" placeholder="e.g. headache"
                       class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-300 font-mono text-xs">
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Anatomical Body Area / Organ *</label>
                <select name="body_part_id" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-300">
                    <option value="">Select Body Area (e.g. Head & Brain, Chest, Abdomen)</option>
                    @foreach($bodyParts as $bp)
                        <option value="{{ $bp->id }}" @selected(old('body_part_id', $symptom->body_part_id ?? '') == $bp->id)>
                            {{ $bp->getTranslation('name', 'en') }} ({{ $bp->getTranslation('name', 'bn') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Urgency Level (Triage Level) *</label>
                <select name="urgency_level" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-300">
                    <option value="routine" @selected(old('urgency_level', $symptom->urgency_level ?? '') === 'routine')>🟢 Routine / Self-care</option>
                    <option value="urgent" @selected(old('urgency_level', $symptom->urgency_level ?? '') === 'urgent')>🟡 Urgent (See Doctor Promptly)</option>
                    <option value="emergency" @selected(old('urgency_level', $symptom->urgency_level ?? '') === 'emergency')>🔴 Emergency (Immediate Hospital Care)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Age Group</label>
                    <select name="age_group" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                        <option value="all" @selected(old('age_group', $symptom->age_group ?? '') === 'all')>All Ages</option>
                        <option value="adult" @selected(old('age_group', $symptom->age_group ?? '') === 'adult')>Adults Only</option>
                        <option value="child" @selected(old('age_group', $symptom->age_group ?? '') === 'child')>Children / Pediatric</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Gender Scope</label>
                    <select name="gender_scope" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                        <option value="all" @selected(old('gender_scope', $symptom->gender_scope ?? '') === 'all')>All Genders</option>
                        <option value="male" @selected(old('gender_scope', $symptom->gender_scope ?? '') === 'male')>Male Only</option>
                        <option value="female" @selected(old('gender_scope', $symptom->gender_scope ?? '') === 'female')>Female Only</option>
                    </select>
                </div>
            </div>
        </div>

        <div>
            <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 block mb-1.5">Description (English & Bengali)</label>
            <div x-show="activeTab === 'en'">
                <textarea name="description[en]" rows="3" placeholder="Clinical explanation of this symptom..." class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-100">{{ old('description.en', isset($symptom) ? $symptom->getTranslation('description', 'en', false) : '') }}</textarea>
            </div>
            <div x-show="activeTab === 'bn'" style="display: none;">
                <textarea name="description[bn]" rows="3" placeholder="এই উপসর্গের সহজ বাংলা ব্যাখ্যা..." class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-emerald-200 dark:border-emerald-700 bg-emerald-50/30 dark:bg-gray-700 text-gray-800 dark:text-gray-100">{{ old('description.bn', isset($symptom) ? $symptom->getTranslation('description', 'bn', false) : '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- 2. Step 3 Factors & Follow-up Questions (Symptom Checker Engine) --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">2</span>
                    Symptom-Related Factors & Questions (Mayo Clinic Step 3)
                </h3>
                <p class="text-xs text-gray-400 mt-1">Configure questions (character of pain, timing, accompanying signs) asked when a user picks this symptom.</p>
            </div>
            <button type="button" @click="addFactor()" class="px-3.5 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 text-xs font-semibold hover:bg-indigo-100 flex items-center gap-1">
                + Add Question
            </button>
        </div>

        <div class="space-y-4">
            <template x-for="(factor, fIndex) in factors" :key="fIndex">
                <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700/30 space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 flex-1">
                            <span class="text-xs font-bold text-gray-500" x-text="`Q${fIndex + 1}`"></span>
                            <select :name="`factors[${fIndex}][factor_type]`" x-model="factor.factor_type" class="px-2.5 py-1 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <option value="quality">Quality / Character of Pain</option>
                                <option value="duration">Onset & Duration</option>
                                <option value="accompanying">Accompanying Symptoms</option>
                                <option value="trigger">Triggers & Relievers</option>
                            </select>
                        </div>
                        <button type="button" @click="removeFactor(fIndex)" class="p-1 text-red-500 hover:text-red-700 text-xs" title="Remove Question">
                            Remove Question ✕
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] uppercase tracking-wider font-semibold text-gray-500 block mb-1">Question (English)</label>
                            <input type="text" :name="`factors[${fIndex}][question][en]`" x-model="factor.question.en" placeholder="e.g. What does the pain feel like?" class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase tracking-wider font-semibold text-emerald-600 block mb-1">প্রশ্ন (বাংলা)</label>
                            <input type="text" :name="`factors[${fIndex}][question][bn]`" x-model="factor.question.bn" placeholder="যেমন: ব্যথার অনুভূতিটা কেমন?" class="w-full px-3 py-1.5 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-emerald-50/40 dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                        </div>
                    </div>

                    {{-- Options List --}}
                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-gray-600 dark:text-gray-300">Answer Options:</span>
                            <button type="button" @click="addOption(fIndex)" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-semibold">
                                + Add Option
                            </button>
                        </div>

                        <template x-for="(opt, optIndex) in factor.options" :key="optIndex">
                            <div class="flex items-center gap-2">
                                <input type="text" :name="`factors[${fIndex}][options][${optIndex}][text_en]`" x-model="opt.text_en" placeholder="Option EN" class="flex-1 px-2.5 py-1 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                                <input type="text" :name="`factors[${fIndex}][options][${optIndex}][text_bn]`" x-model="opt.text_bn" placeholder="অপশন BN" class="flex-1 px-2.5 py-1 text-xs rounded-lg border border-emerald-200 dark:border-emerald-700 bg-emerald-50/30 dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                                
                                <label class="flex items-center gap-1 text-[11px] text-red-600 cursor-pointer flex-shrink-0">
                                    <input type="checkbox" :name="`factors[${fIndex}][options][${optIndex}][red_flag]`" value="1" x-model="opt.red_flag" class="rounded border-gray-300 text-red-600 w-3 h-3">
                                    Red Flag!
                                </label>

                                <button type="button" @click="removeOption(fIndex, optIndex)" class="p-1 text-gray-400 hover:text-red-500 text-xs">
                                    ✕
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Submit Buttons --}}
    <div class="flex items-center gap-3 pt-3 flex-wrap">
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 text-white text-sm font-semibold hover:opacity-90 shadow transition-all">
            {{ isset($symptom) ? 'Update Symptom' : 'Save Symptom' }}
        </button>
        <a href="{{ route('admin.symptoms.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Cancel
        </a>
    </div>
</div>
