@php
    $page = $page ?? null;
@endphp
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
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
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Page Title (H1) *</label>
                    <input type="text" name="title" required value="{{ old('title', $page->title ?? '') }}" placeholder="Top 10 Heart Specialists in Dhaka"
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

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">Top Content (Intro)</label>
                        <button type="button" onclick="generateAiContent('seo_page_content_top', 'tinymce:content_top', this)" class="text-[10px] bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-violet-200 transition-colors z-50 relative">
                            ✨ Auto Generate
                        </button>
                    </div>
                    <textarea name="content_top" id="content_top" rows="6" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">{{ old('content_top', $page->content_top ?? '') }}</textarea>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">Bottom Content (Conclusion/FAQs textual)</label>
                        <button type="button" onclick="generateAiContent('seo_page_content_bottom', 'tinymce:content_bottom', this)" class="text-[10px] bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-violet-200 transition-colors z-50 relative">
                            ✨ Auto Generate
                        </button>
                    </div>
                    <textarea name="content_bottom" id="content_bottom" rows="6" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">{{ old('content_bottom', $page->content_bottom ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- SEO Meta Box -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Search Engine Meta</h2>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">Meta Title</label>
                        <button type="button" onclick="generateAiContent('seo_meta_title', 'input[name=\'meta_title\']', this)" class="text-[10px] bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-blue-200 transition-colors">✨ Auto Generate</button>
                    </div>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title ?? '') }}" maxlength="60"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">Meta Description</label>
                        <button type="button" onclick="generateAiContent('seo_meta_description', 'textarea[name=\'meta_description\']', this)" class="text-[10px] bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-blue-200 transition-colors">✨ Auto Generate</button>
                    </div>
                    <textarea name="meta_description" rows="3" maxlength="160" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
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
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
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
                            <option value="{{ $specialty->id }}" @selected(old('specialty_id', $page->specialty_id ?? '') == $specialty->id)>{{ $specialty->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Filter by Division</label>
                    <select name="division_id" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                        <option value="">Any Division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" @selected(old('division_id', $page->division_id ?? '') == $division->id)>{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Filter by District</label>
                    <select name="district_id" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                        <option value="">Any District</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" @selected(old('district_id', $page->district_id ?? '') == $district->id)>{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Filter by Area/Thana</label>
                    <select name="area_id" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                        <option value="">Any Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" @selected(old('area_id', $page->area_id ?? '') == $area->id)>{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <hr class="border-gray-100 dark:border-gray-700">
                
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $page->is_active ?? true)) class="w-4 h-4 text-violet-600 bg-gray-100 border-gray-300 rounded focus:ring-violet-500 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-300">Publish Page</span>
                </label>
            </div>
        </div>

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
