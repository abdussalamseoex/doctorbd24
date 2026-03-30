@extends('admin.layouts.app')
@section('title', 'Blog Posts')
@section('content')
<div x-data="{ 
    selectedIds: [], 
    selectAll: false, 
    openImportModal: false,
    showBulkConfirm: false,
    bulkAction: '',
    toggleAll() { 
        this.selectedIds = this.selectAll ? [{{ $posts->pluck('id')->join(',') }}] : []; 
    },
    confirmBulk() {
        if (!this.bulkAction) return;
        this.showBulkConfirm = true;
    }
}" class="relative">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-sm text-gray-400 font-medium">Total: {{ $posts->total() }} posts</h2>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openImportModal = true" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Import
            </button>
            <a href="{{ route('admin.blog-posts.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-violet-500 to-purple-600 text-white text-sm font-medium hover:opacity-90 shadow transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Post
            </a>
        </div>

        {{-- Import Modal --}}
        <div x-show="openImportModal" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div @click.away="openImportModal = false" class="transform transition-all w-full max-w-lg">
                <livewire:admin.bulk-importer type="blog-post" />
            </div>
        </div>
    </div>

    {{-- Bulk Action Bar --}}
    <div x-show="selectedIds.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-10"
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-6 border border-gray-800 dark:border-gray-200">
        <span class="text-sm font-bold"><span x-text="selectedIds.length"></span> selected</span>
        <div class="flex items-center gap-3">
            <select x-model="bulkAction" class="bg-gray-800 dark:bg-gray-100 border-gray-700 dark:border-gray-300 rounded-lg text-xs py-1.5 focus:ring-sky-500 dark:text-gray-900">
                <option value="">Select Action</option>
                <option value="delete">Delete Selected</option>
                <option value="publish">Publish Selected</option>
                <option value="draft">Move to Draft</option>
            </select>
            <button @click="confirmBulk" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition-all">Apply</button>
        </div>
    </div>

    {{-- Bulk Confirmation Modal --}}
    <div x-show="showBulkConfirm" style="display: none;" class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 backdrop-blur-md">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-2xl max-w-sm w-full border border-gray-100 dark:border-gray-700 text-center">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 uppercase tracking-tight">Are you sure?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">You are about to <span class="font-bold text-red-600 dark:text-red-400" x-text="bulkAction"></span> <span x-text="selectedIds.length"></span> items. This action cannot be undone.</p>
            
            <form action="{{ route('admin.blog-posts.bulk-action') }}" method="POST">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <input type="hidden" name="action" :value="bulkAction">
                
                <div class="flex gap-3">
                    <button type="button" @click="showBulkConfirm = false" class="flex-1 px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold hover:bg-gray-200 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-red-600 text-white font-bold shadow-lg shadow-red-500/30 hover:bg-red-700 transition-all">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-750 border-b border-gray-100 dark:border-gray-700">
                <tr class="text-left">
                    <th class="px-4 py-3 w-10">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    </th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Title</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">Category</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">Views</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Published</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($posts as $post)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors" :class="selectedIds.includes('{{ $post->id }}') ? 'bg-sky-50/50 dark:bg-sky-900/10' : ''">
                    <td class="px-4 py-3">
                        <input type="checkbox" value="{{ $post->id }}" x-model="selectedIds" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-700 dark:text-gray-200 text-xs truncate max-w-xs">{{ $post->title }}</p>
                        <p class="text-xs text-gray-400">by {{ $post->author->name }}</p>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell text-xs text-gray-500 dark:text-gray-400">{{ $post->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3 hidden md:table-cell text-xs text-gray-500 dark:text-gray-400">{{ $post->view_count }}</td>
                    <td class="px-4 py-3">
                        @if($post->published_at && $post->published_at <= now())
                            <span class="px-2 py-0.5 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium">✓ Published</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs">Draft</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs hover:bg-emerald-100 font-medium transition-colors" title="View Live">Live</a>
                            <a href="{{ route('admin.blog-posts.edit', $post->id) }}" class="px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 text-xs hover:bg-sky-100 font-medium transition-colors">Edit</a>
                            <button type="button" onclick="confirmDelete('{{ route('admin.blog-posts.destroy', $post->id) }}', 'Delete this post?')" class="px-2.5 py-1 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs hover:bg-red-100 font-medium transition-colors">Delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">No blog posts.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $posts->links() }}</div>
    </div>
</div>
@endsection
