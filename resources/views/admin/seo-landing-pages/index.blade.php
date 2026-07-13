@extends('admin.layouts.app')
@section('title', 'Programmatic SEO Landing Pages')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">SEO Landing Pages</h1>
        <p class="text-sm text-gray-500 mt-1">Manage dynamic, programmatic SEO landing pages.</p>
    </div>
    <div class="flex items-center gap-3 flex-wrap">
        <form action="{{ route('admin.seo-landing-pages.sync') }}" method="POST" onsubmit="return confirm('Are you sure you want to synchronize and auto-map Data Context for all programmatic SEO pages?');">
            @csrf
            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                ⚡ Sync & Map All Data Context
            </button>
        </form>
        <a href="{{ route('admin.seo-landing-pages.create') }}" class="px-4 py-2 bg-gradient-to-r from-violet-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-all shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create New Page
        </a>
    </div>
</div>

<!-- Filter & Search Bar -->
<div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row gap-4 justify-between items-center">
    <form action="{{ route('admin.seo-landing-pages.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-4 items-center">
        <!-- Search Input -->
        <div class="relative w-full sm:max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by slug, title, or keyword..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl leading-5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 sm:text-sm transition-colors">
        </div>
        
        <!-- Per Page Dropdown -->
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <label for="per_page" class="text-sm text-gray-500 whitespace-nowrap">Show:</label>
            <select name="per_page" id="per_page" onchange="this.form.submit()" class="block w-full sm:w-auto pl-3 pr-8 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors">
                <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
            </select>
        </div>

        <!-- Submit Button for Mobile / Non-JS -->
        <button type="submit" class="hidden sm:block px-4 py-2 bg-violet-500 text-white text-sm font-semibold rounded-xl hover:bg-violet-600 transition-colors shadow-sm">
            Search
        </button>
        
        @if(request('search') || request('per_page'))
            <a href="{{ route('admin.seo-landing-pages.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-violet-600 dark:hover:text-violet-400 transition-colors">
                Clear
            </a>
        @endif
    </form>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Keyword & Title</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">URL Slug</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($pages as $page)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <!-- 1. Keyword & Title -->
                    <td class="px-4 py-3">
                        <div class="font-bold text-gray-900 dark:text-white">{{ $page->keyword }}</div>
                        <div class="text-xs text-gray-500 line-clamp-1">{{ $page->title }}</div>
                    </td>

                    <!-- 2. Type -->
                    <td class="px-4 py-3 capitalize">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300 border border-violet-100 dark:border-violet-800">
                            {{ $page->type ?: 'doctor' }}
                        </span>
                    </td>

                    <!-- 3. URL Slug (Clickable View Buttons) -->
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono text-gray-500 dark:text-gray-400">/{{ $page->slug }}</span>
                            <div class="flex gap-1">
                                <a href="{{ url('/' . $page->slug) }}" target="_blank" title="View English Page"
                                   class="px-2 py-0.5 text-[10px] font-bold rounded bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/40 dark:text-blue-300 transition-colors">
                                    EN ↗
                                </a>
                                <a href="{{ url('/bn/' . $page->slug) }}" target="_blank" title="View Bangla Page"
                                   class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 transition-colors">
                                    BN ↗
                                </a>
                            </div>
                        </div>
                    </td>

                    <!-- 4. Status -->
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full {{ $page->status === 'published' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($page->status === 'scheduled' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300') }}">
                            {{ $page->status }}
                        </span>
                    </td>

                    <!-- 5. Actions -->
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ url('/' . $page->slug) }}" target="_blank" title="View Live Page"
                               class="p-1.5 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            <a href="{{ route('admin.seo-landing-pages.edit', $page) }}" title="Edit Page"
                               class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('admin.seo-landing-pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this programmatic landing page?');" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" title="Delete Page" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                        <p class="mb-2">No Programmatic SEO pages created yet.</p>
                        <a href="{{ route('admin.seo-landing-pages.create') }}" class="text-violet-600 hover:underline text-sm font-medium">Create your first landing page</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pages->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
        {{ $pages->links() }}
    </div>
    @endif
</div>
@endsection
