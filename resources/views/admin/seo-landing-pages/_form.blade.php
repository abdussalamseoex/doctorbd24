@php
    $page = $page ?? null;
@endphp
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ activeTab: 'en' }">
    <div class="lg:col-span-2 space-y-6">
        <!-- Language Tab Buttons -->
        <div class="flex gap-2 bg-white dark:bg-gray-800 p-3 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <button type="button" @click="activeTab = 'en'" 
                    :class="activeTab === 'en' ? 'bg-violet-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'" 
                    class="px-5 py-2 rounded-xl text-sm font-bold transition-all focus:outline-none">
                English [EN]
            </button>
            <button type="button" @click="activeTab = 'bn'" 
                    :class="activeTab === 'bn' ? 'bg-emerald-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'" 
                    class="px-5 py-2 rounded-xl text-sm font-bold transition-all focus:outline-none flex items-center gap-2">
                বাংলা [BN]
                <span class="text-[10px] bg-emerald-700/20 px-1.5 py-0.5 rounded text-white" x-show="activeTab === 'bn'">বাংলা পেজ</span>
            </button>
        </div>

        <!-- Main Content Box -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Page Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Target Keyword *</label>
                    <input type="text" name="keyword" required value="{{ old('keyword', $page->keyword ?? '') }}" placeholder="e.g. Best Cardiologists in Dhaka"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">URL Slug *</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-500 text-xs">/</span>
                        <input type="text" name="slug" required value="{{ old('slug', $page->slug ?? '') }}" placeholder="cardiologists-in-dhaka"
                               class="flex-1 w-full px-3 py-2 text-sm rounded-r-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                    </div>
                </div>

                <!-- ENGLISH CONTENT FIELDS -->
                <div x-show="activeTab === 'en'" class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Page Title (H1) [English] *</label>
                        <input type="text" name="title[en]" value="{{ old('title.en', isset($page) ? $page->getTranslation('title', 'en', false) : '') }}" placeholder="Top 10 Heart Specialists in Dhaka"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">Top Content (Intro) [English]</label>
                            <button type="button" onclick="generateAiContent('seo_page_content_top', 'textarea[name=\'content_top[en]\']', this)" class="text-[10px] bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-violet-200 transition-colors z-50 relative">
                                ✨ Auto Generate (EN)
                            </button>
                        </div>
                        <textarea name="content_top[en]" id="content_top_en" rows="6" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">{{ old('content_top.en', isset($page) ? $page->getTranslation('content_top', 'en', false) : '') }}</textarea>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">Bottom Content (Conclusion/FAQs) [English]</label>
                            <button type="button" onclick="generateAiContent('seo_page_content_bottom', 'textarea[name=\'content_bottom[en]\']', this)" class="text-[10px] bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-violet-200 transition-colors z-50 relative">
                                ✨ Auto Generate (EN)
                            </button>
                        </div>
                        <textarea name="content_bottom[en]" id="content_bottom_en" rows="6" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">{{ old('content_bottom.en', isset($page) ? $page->getTranslation('content_bottom', 'en', false) : '') }}</textarea>
                    </div>
                </div>

                <!-- BENGALI CONTENT FIELDS -->
                <div x-show="activeTab === 'bn'" class="space-y-4" x-cloak>
                    <div>
                        <label class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 block mb-1.5">শিরোনাম (H1) [বাংলা]</label>
                        <input type="text" name="title[bn]" value="{{ old('title.bn', isset($page) ? $page->getTranslation('title', 'bn', false) : '') }}" placeholder="উত্তরায় সেরা হৃদরোগ বিশেষজ্ঞ ডাক্তার"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 block">উপরের বিবরণ (ভূমিকা) [বাংলা]</label>
                            <button type="button" onclick="generateAiContent('seo_page_content_top', 'textarea[name=\'content_top[bn]\']', this, 'bn')" class="text-[10px] bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-emerald-200 transition-colors z-50 relative">
                                ✨ বাংলায় জেনারেট করুন (AI)
                            </button>
                        </div>
                        <textarea name="content_top[bn]" id="content_top_bn" rows="6" placeholder="এই এলাকার চিকিৎসাসেবা ও সিরিয়াল নেওয়ার নিয়ম..." class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-400">{{ old('content_top.bn', isset($page) ? $page->getTranslation('content_top', 'bn', false) : '') }}</textarea>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 block">নিচের বিবরণ (প্রশ্ন ও উত্তর) [বাংলা]</label>
                            <button type="button" onclick="generateAiContent('seo_page_content_bottom', 'textarea[name=\'content_bottom[bn]\']', this, 'bn')" class="text-[10px] bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-emerald-200 transition-colors z-50 relative">
                                ✨ বাংলায় জেনারেট করুন (AI)
                            </button>
                        </div>
                        <textarea name="content_bottom[bn]" id="content_bottom_bn" rows="6" placeholder="এই এলাকার ডাক্তারদের সিরিয়াল নেওয়ার সাধারণ প্রশ্নের উত্তর..." class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-400">{{ old('content_bottom.bn', isset($page) ? $page->getTranslation('content_bottom', 'bn', false) : '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Meta Box -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Search Engine Meta</h2>
            
            <div x-show="activeTab === 'en'" class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">Meta Title [English]</label>
                        <button type="button" onclick="generateAiContent('seo_title', 'input[name=\'meta_title[en]\']', this)" class="text-[10px] bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-blue-200 transition-colors">✨ Auto Generate (EN)</button>
                    </div>
                    <input type="text" name="meta_title[en]" value="{{ old('meta_title.en', isset($page) ? $page->getTranslation('meta_title', 'en', false) : '') }}" maxlength="60"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">Meta Description [English]</label>
                        <button type="button" onclick="generateAiContent('seo_desc', 'textarea[name=\'meta_description[en]\']', this)" class="text-[10px] bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-blue-200 transition-colors">✨ Auto Generate (EN)</button>
                    </div>
                    <textarea name="meta_description[en]" rows="3" maxlength="160" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">{{ old('meta_description.en', isset($page) ? $page->getTranslation('meta_description', 'en', false) : '') }}</textarea>
                </div>
            </div>

            <div x-show="activeTab === 'bn'" class="space-y-4" x-cloak>
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 block">মেটা টাইটেল [বাংলা]</label>
                        <button type="button" onclick="generateAiContent('seo_title', 'input[name=\'meta_title[bn]\']', this, 'bn')" class="text-[10px] bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-emerald-200 transition-colors">✨ বাংলায় মেটা টাইটেল (AI)</button>
                    </div>
                    <input type="text" name="meta_title[bn]" value="{{ old('meta_title.bn', isset($page) ? $page->getTranslation('meta_title', 'bn', false) : '') }}" maxlength="60" placeholder="উত্তরায় সেরা হৃদরোগ বিশেষজ্ঞ ডাক্তার"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 block">মেটা বিবরণ [বাংলা]</label>
                        <button type="button" onclick="generateAiContent('seo_desc', 'textarea[name=\'meta_description[bn]\']', this, 'bn')" class="text-[10px] bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-emerald-200 transition-colors">✨ বাংলায় মেটা বিবরণ (AI)</button>
                    </div>
                    <textarea name="meta_description[bn]" rows="3" maxlength="160" placeholder="উত্তরা এলাকার ভেরিফাইড বিশেষজ্ঞ ডাক্তারদের চেম্বার ও সিরিয়াল নম্বর..." class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-400">{{ old('meta_description.bn', isset($page) ? $page->getTranslation('meta_description', 'bn', false) : '') }}</textarea>
                </div>
            </div>
        </div>
        
        <!-- JSON-LD FAQ Schema Box -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">FAQ Schema (JSON-LD)</h2>
                <button type="button" onclick="generateAiContent('seo_faq_schema', 'textarea[name=\'faq_schema_raw\']', this)" class="text-[10px] bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-emerald-200 transition-colors">
                    ✨ Generate JSON FAQ
                </button>
            </div>
            <textarea name="faq_schema_raw" rows="6" placeholder='[{"question": "...", "answer": "..."}]' class="w-full px-3 py-2 text-sm font-mono rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">{{ old('faq_schema_raw', isset($page) && $page->faq_schema ? json_encode($page->faq_schema, JSON_PRETTY_PRINT) : '') }}</textarea>
        </div>
    </div>

    <!-- Sidebar Rules -->
    <div class="space-y-6">
        @php
            $getCleanName = function($name) {
                if (is_array($name)) return $name['en'] ?? ($name['bn'] ?? '');
                $decoded = json_decode($name, true);
                if (is_array($decoded)) return $decoded['en'] ?? ($decoded['bn'] ?? $name);
                return $name;
            };
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6"
             x-data="{
                 division_id: '{{ old('division_id', $page->division_id ?? '') }}',
                 district_id: '{{ old('district_id', $page->district_id ?? '') }}',
                 area_id: '{{ old('area_id', $page->area_id ?? '') }}',
                 districts: @js($districts->map(fn($d) => ['id' => $d->id, 'division_id' => $d->division_id, 'name' => $getCleanName($d->name)])->values()),
                 areas: @js($areas->map(fn($a) => ['id' => $a->id, 'district_id' => $a->district_id, 'name' => $getCleanName($a->name)])->values()),
                 get filteredDistricts() {
                     if (!this.division_id) return this.districts;
                     return this.districts.filter(d => String(d.division_id) === String(this.division_id));
                 },
                 get filteredAreas() {
                     if (!this.district_id) return this.areas;
                     return this.areas.filter(a => String(a.district_id) === String(this.district_id));
                 },
                 onDivisionChange() {
                     if (this.district_id && !this.filteredDistricts.some(d => String(d.id) === String(this.district_id))) {
                         this.district_id = '';
                         this.area_id = '';
                     }
                 },
                 onDistrictChange() {
                     if (this.area_id && !this.filteredAreas.some(a => String(a.id) === String(this.area_id))) {
                         this.area_id = '';
                     }
                 }
             }">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Data Context</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Directory Type *</label>
                    <select name="type" required class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                        <option value="doctor" @selected(old('type', $page->type ?? '') == 'doctor')>Doctor Directory</option>
                        <option value="hospital" @selected(old('type', $page->type ?? '') == 'hospital')>Hospital Directory</option>
                        <option value="ambulance" @selected(old('type', $page->type ?? '') == 'ambulance')>Ambulance Directory</option>
                        <option value="general" @selected(old('type', $page->type ?? '') == 'general')>General Landing Page</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Filter by Specialty</label>
                    <select name="specialty_id" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                        <option value="">Any Specialty</option>
                        @foreach($specialties as $specialty)
                            <option value="{{ $specialty->id }}" @selected(old('specialty_id', $page->specialty_id ?? '') == $specialty->id)>{{ $getCleanName($specialty->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Filter by Division</label>
                    <select name="division_id" x-model="division_id" @change="onDivisionChange()" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                        <option value="">Any Division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $getCleanName($division->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Filter by District</label>
                    <select name="district_id" x-model="district_id" @change="onDistrictChange()" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                        <option value="">Any District</option>
                        <template x-for="item in filteredDistricts" :key="item.id">
                            <option :value="item.id" x-text="item.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Filter by Area/Thana</label>
                    <select name="area_id" x-model="area_id" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                        <option value="">Any Area</option>
                        <template x-for="item in filteredAreas" :key="item.id">
                            <option :value="item.id" x-text="item.name"></option>
                        </template>
                    </select>
                </div>
                
            </div>
        </div>
        
        @include('admin.components.publish-status', ['status' => $page->status ?? 'draft', 'publishedAt' => $page->published_at ?? null])

        <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-bold rounded-xl hover:opacity-90 shadow border border-transparent transition-all mb-4">
            {{ isset($page) ? 'Update Landing Page' : 'Create Landing Page' }}
        </button>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#content_top, #content_bottom',
        height: 300,
        menubar: false,
        plugins: ['lists', 'link', 'code'],
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | code',
        skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
        content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
</script>
<style>
    .tox-notifications-container { display: none !important; }
</style>
@endpush
