@extends('admin.layouts.app')
@section('title', 'Programmatic SEO Landing Pages')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">SEO Landing Pages</h1>
        <p class="text-sm text-gray-500 mt-1">Manage dynamic, programmatic SEO landing pages.</p>
    </div>
    <a href="{{ route('admin.seo-landing-pages.create') }}" class="px-4 py-2 bg-gradient-to-r from-violet-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-all shadow-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Create New Page
    </a>
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
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $page->keyword }}</div>
                        <div class="text-xs text-gray-500">{{ $page->title }}</div>
                    </td>
                    <td class="px-4 py-3 capitalize">
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-400">
                            {{ $page->type }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs">
                        <a href="/{{ $page->slug }}" target="_blank" class="text-violet-500 hover:text-violet-600 hover:underline">/{{ $page->slug }}</a>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-full {{ $page->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                            {{ $page->is_active ? 'Active' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex justify-end gap-2">
                        <a href="{{ route('admin.seo-landing-pages.edit', $page) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                        <form action="{{ route('admin.seo-landing-pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this programmatic landing page?');">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
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
