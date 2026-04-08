@props(['status' => 'draft', 'publishedAt' => null])

@php
    $formattedPublishedAt = $publishedAt ? \Carbon\Carbon::parse($publishedAt)->format('Y-m-d\TH:i') : '';
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6 component-publish-status" x-data="{ currentStatus: '{{ old('status', $status) }}' }">
    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-5 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
        Publishing Status
    </h3>

    <div class="space-y-4">
        <div>
            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-2">Status</label>
            <select name="status" x-model="currentStatus" class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-gray-800 dark:text-gray-200">
                <option value="draft">Draft</option>
                <option value="scheduled">Scheduled</option>
                <option value="published">Published</option>
            </select>
        </div>

        <div x-show="currentStatus === 'scheduled'" style="display: none;">
            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-2">Publish Date & Time</label>
            <input type="datetime-local" 
                   name="published_at" 
                   class="w-full rounded-xl border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-gray-800 dark:text-gray-200" 
                   value="{{ old('published_at', $formattedPublishedAt) }}">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Select a future date & time.</p>
        </div>

        <div x-show="currentStatus === 'published'" class="p-3 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-sm rounded-xl flex items-center gap-2" style="display: none;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Item will be instantly live.
        </div>

        <div x-show="currentStatus === 'draft'" class="p-3 bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 text-sm rounded-xl flex items-center gap-2" style="display: none;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            Saved as draft (hidden).
        </div>
    </div>
</div>
