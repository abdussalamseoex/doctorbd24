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
}" class="relative space-y-6">

    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Health Blog Posts</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage health articles, scheduling, and publication status.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openImportModal = true" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-650 transition-all shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Import
            </button>
            <a href="{{ route('admin.blog-posts.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-violet-500 to-purple-600 text-white text-sm font-semibold hover:opacity-95 shadow transition-all">
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

    {{-- Status Navigation Tabs --}}
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 overflow-x-auto pb-1 text-sm font-medium">
        <a href="{{ route('admin.blog-posts.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
           class="px-4 py-2 rounded-xl transition-colors flex items-center gap-2 {{ ($status ?? 'all') === 'all' ? 'bg-violet-600 text-white font-semibold shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <span>All Posts</span>
            <span class="text-xs px-2 py-0.5 rounded-full {{ ($status ?? 'all') === 'all' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">{{ $counts['all'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.blog-posts.index', array_merge(request()->except('status', 'page'), ['status' => 'published'])) }}"
           class="px-4 py-2 rounded-xl transition-colors flex items-center gap-2 {{ ($status ?? '') === 'published' ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Published</span>
            <span class="text-xs px-2 py-0.5 rounded-full {{ ($status ?? '') === 'published' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">{{ $counts['published'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.blog-posts.index', array_merge(request()->except('status', 'page'), ['status' => 'scheduled'])) }}"
           class="px-4 py-2 rounded-xl transition-colors flex items-center gap-2 {{ ($status ?? '') === 'scheduled' ? 'bg-amber-500 text-white font-semibold shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <span class="w-2 h-2 rounded-full bg-amber-300"></span>
            <span>Scheduled</span>
            <span class="text-xs px-2 py-0.5 rounded-full {{ ($status ?? '') === 'scheduled' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">{{ $counts['scheduled'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.blog-posts.index', array_merge(request()->except('status', 'page'), ['status' => 'draft'])) }}"
           class="px-4 py-2 rounded-xl transition-colors flex items-center gap-2 {{ ($status ?? '') === 'draft' ? 'bg-gray-700 text-white font-semibold shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <span>Draft</span>
            <span class="text-xs px-2 py-0.5 rounded-full {{ ($status ?? '') === 'draft' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">{{ $counts['draft'] ?? 0 }}</span>
        </a>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row gap-4 justify-between items-center">
        <form action="{{ route('admin.blog-posts.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-4 items-center">
            @if(request('status') && request('status') !== 'all')
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <!-- Search Input -->
            <div class="relative w-full sm:max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles by title or slug..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl leading-5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 sm:text-sm transition-colors">
            </div>

            <!-- Per Page Dropdown -->
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <label for="per_page" class="text-xs text-gray-500 whitespace-nowrap">Show:</label>
                <select name="per_page" id="per_page" onchange="this.form.submit()" class="block w-full sm:w-auto pl-3 pr-8 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors">
                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                Filter
            </button>

            @if(request('search') || (request('status') && request('status') !== 'all') || request('per_page'))
                <a href="{{ route('admin.blog-posts.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors">
                    Reset
                </a>
            @endif
        </form>
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
            <select x-model="bulkAction" class="bg-gray-800 dark:bg-gray-100 border-gray-700 dark:border-gray-300 rounded-lg text-xs py-1.5 focus:ring-violet-500 dark:text-gray-900">
                <option value="">Select Action</option>
                <option value="delete">Delete Selected</option>
                <option value="publish">Publish Selected</option>
                <option value="draft">Move to Draft</option>
            </select>
            <button @click="confirmBulk" class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition-all">Apply</button>
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

    {{-- Posts Data Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-750 border-b border-gray-100 dark:border-gray-700">
                <tr class="text-left">
                    <th class="px-4 py-3.5 w-10">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                    </th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Article Title</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">Category</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">Views</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status & Schedule</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($posts as $post)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors" :class="selectedIds.includes('{{ $post->id }}') ? 'bg-violet-50/50 dark:bg-violet-900/10' : ''">
                    <td class="px-4 py-3.5">
                        <input type="checkbox" value="{{ $post->id }}" x-model="selectedIds" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            @if($post->image)
                                <img src="{{ asset('storage/' . $post->image) }}" class="w-10 h-10 object-cover rounded-lg flex-shrink-0 border border-gray-100 dark:border-gray-700">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-sm flex-shrink-0">📰</div>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route('admin.blog-posts.edit', $post->id) }}" class="font-bold text-gray-800 dark:text-gray-100 text-xs hover:text-violet-600 dark:hover:text-violet-400 line-clamp-1">
                                    {{ $post->title }}
                                </a>
                                <p class="text-[11px] text-gray-400 mt-0.5">by {{ $post->author->name ?? 'Admin' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell text-xs text-gray-600 dark:text-gray-300">
                        @if($post->category)
                            <span class="px-2 py-0.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs">
                                {{ $post->category->name }}
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell text-xs font-semibold text-gray-600 dark:text-gray-300">
                        {{ number_format($post->view_count) }}
                    </td>

                    {{-- Status & Schedule Column --}}
                    <td class="px-4 py-3.5">
                        @if($post->is_scheduled)
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 text-xs font-bold border border-amber-200 dark:border-amber-800/60 shadow-sm">
                                    <svg class="w-3.5 h-3.5 animate-pulse text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Scheduled
                                </span>
                                <div class="text-[11px] font-semibold text-amber-900 dark:text-amber-200 mt-1">
                                    {{ $post->published_at ? $post->published_at->format('d M, Y h:i A') : 'No date set' }}
                                </div>
                                @if($post->published_at && $post->published_at->isFuture())
                                    <div class="text-[10px] text-gray-400">
                                        ({{ $post->published_at->diffForHumans() }})
                                    </div>
                                @endif
                            </div>
                        @elseif($post->is_live)
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800/60 shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Published
                                </span>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">
                                    {{ $post->published_at ? $post->published_at->format('d M, Y h:i A') : $post->created_at->format('d M, Y') }}
                                </div>
                            </div>
                        @else
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold border border-gray-200 dark:border-gray-600">
                                    Draft
                                </span>
                                <div class="text-[11px] text-gray-400 mt-1">
                                    Created: {{ $post->created_at->format('d M, Y') }}
                                </div>
                            </div>
                        @endif
                    </td>

                    {{-- Actions Column --}}
                    <td class="px-4 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            @if($post->is_live)
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs hover:bg-emerald-100 dark:hover:bg-emerald-800/40 font-semibold transition-colors flex items-center gap-1" title="View live article on website">
                                    Live ↗
                                </a>
                            @else
                                <a href="{{ route('blog.show', ['slug' => $post->slug, 'preview' => 'admin']) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 text-xs hover:bg-amber-100 dark:hover:bg-amber-800/40 font-semibold transition-colors flex items-center gap-1" title="Review article before release">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Preview
                                </a>
                            @endif

                            <a href="{{ route('admin.blog-posts.edit', $post->id) }}" class="px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 text-xs hover:bg-sky-100 dark:hover:bg-sky-800/40 font-semibold transition-colors">
                                Edit
                            </a>

                            <button type="button" onclick="confirmDelete('{{ route('admin.blog-posts.destroy', $post->id) }}', 'Delete this blog post?')" class="px-2.5 py-1 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs hover:bg-red-100 dark:hover:bg-red-800/40 font-semibold transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-16 text-center text-gray-400">
                        <div class="text-4xl mb-2">📰</div>
                        <p class="text-sm font-medium">No blog posts found matching your criteria.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($posts->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $posts->links() }}</div>
        @endif
    </div>
</div>
@endsection
