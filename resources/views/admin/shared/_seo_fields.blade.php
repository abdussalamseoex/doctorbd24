@php
    $seo = $model ? $model->seoMeta : null;
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-5 flex items-center gap-2">
        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        SEO Meta Optimization
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
            <div class="flex justify-between items-center mb-1.5">
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">SEO Title</label>
                <button type="button" onclick="generateAiContent('seo_title', 'input[name=\'seo[title]\']', this)" class="text-[10px] bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-indigo-200 transition-colors">
                    ✨ Auto Generate
                </button>
            </div>
            <input type="text" name="seo[title]" value="{{ old('seo.title', $seo->title ?? '') }}" placeholder="Meta Title (recommended < 60 chars)"
                   class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-300 transition-colors">
            <p class="text-[10px] text-gray-400 mt-1">If empty, the model name will be used.</p>
        </div>

        <div class="md:col-span-2">
            <div class="flex justify-between items-center mb-1.5">
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">SEO Description</label>
                <button type="button" onclick="generateAiContent('seo_desc', 'textarea[name=\'seo[description]\']', this)" class="text-[10px] bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-indigo-200 transition-colors">
                    ✨ Auto Generate
                </button>
            </div>
            <textarea name="seo[description]" rows="3" placeholder="Meta Description (recommended < 160 chars)"
                      class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-300 resize-none transition-colors">{{ old('seo.description', $seo->description ?? '') }}</textarea>
        </div>

        <div>
            <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">SEO Keywords</label>
            <input type="text" name="seo[keywords]" value="{{ old('seo.keywords', $seo->keywords ?? '') }}" placeholder="keyword1, keyword2, keyword3"
                   class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-300 transition-colors">
        </div>

        <div>
            <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">OG Image (Upload)</label>
            <input type="file" name="seo[og_image_file]" accept="image/*"
                   class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 dark:file:bg-gray-700 dark:file:text-gray-300">
            @if(!empty($seo->og_image))
                <div class="mt-2 flex items-center gap-2">
                    <img src="{{ Str::startsWith($seo->og_image, 'http') ? $seo->og_image : asset('storage/' . $seo->og_image) }}" class="w-10 h-10 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                    <span class="text-[10px] text-gray-400 line-clamp-1">Current OG Image</span>
                </div>
            @endif
        </div>

        <div>
            <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">OG Image URL (Fallback)</label>
            <input type="text" name="seo[og_image]" value="{{ old('seo.og_image', $seo->og_image ?? '') }}" placeholder="https://..."
                   class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-300 transition-colors">
        </div>
    </div>
</div>
