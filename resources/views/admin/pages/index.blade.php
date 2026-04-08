@extends('admin.layouts.app')
@section('title', 'Pages')
@section('content')
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Dynamic Pages</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage static pages like About Us, Privacy Policy, etc.</p>
    </div>
    <a href="{{ route('admin.pages.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-sky-500 to-indigo-600 text-white text-sm font-medium hover:opacity-90 shadow transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Page
    </a>
</div>

<div x-data="{ openDeleteModal: false, deleteId: null }" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 dark:bg-gray-750 border-b border-gray-100 dark:border-gray-700">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Slug / URL</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($pages as $page)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">{{ $page->title }}</td>
                <td class="px-6 py-4 hidden md:table-cell text-gray-500 dark:text-gray-400 font-mono text-xs">/{{ $page->slug }}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $page->status === 'published' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($page->status === 'scheduled' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300') }}">
                        {{ ucfirst($page->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('page.show', $page->slug ?? 'temp') }}" target="_blank" class="p-2 text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition" title="View Page">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('admin.pages.edit', $page->id) }}" class="p-2 text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-900/30 rounded-lg transition" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <button type="button" @click="openDeleteModal = true; deleteId = {{ $page->id }}" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    No pages have been created yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($pages->hasPages())
    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
        {{ $pages->links() }}
    </div>
    @endif

    {{-- Delete Modal --}}
    <div x-show="openDeleteModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak style="display: none;">
        <div @click.away="openDeleteModal = false" class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl w-full max-w-sm mx-4 transform transition-all text-left">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Are you sure you want to delete this page? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button @click="openDeleteModal = false" type="button" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">Cancel</button>
                <form :action="`/admin/pages/${deleteId}`" method="POST" class="inline m-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">Delete Page</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
