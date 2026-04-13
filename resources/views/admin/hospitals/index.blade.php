@extends('admin.layouts.app')
@section('title', 'Hospitals')
@section('content')
<div x-data="{ 
    selectedIds: [], 
    selectAll: false, 
    openImportModal: false,
    showBulkConfirm: false,
    bulkAction: '',
    toggleAll() { 
        this.selectedIds = this.selectAll ? [{{ $hospitals->pluck('id')->join(',') }}] : []; 
    },
    confirmBulk() {
        if (!this.bulkAction) return;
        this.showBulkConfirm = true;
    }
}" class="relative">
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.hospitals.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search hospitals..."
                       class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 w-56">
                
                <x-admin.location-filter :showHospital="false" />

                <div class="flex border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden bg-white dark:bg-gray-700">
                    <a href="{{ route('admin.hospitals.index', array_merge(request()->query(), ['status' => 'draft'])) }}" 
                       class="px-3 py-2 text-xs font-semibold hover:bg-fuchsia-50 dark:hover:bg-fuchsia-900/20 {{ request('status') === 'draft' ? 'bg-fuchsia-100 dark:bg-fuchsia-900/40 text-fuchsia-700 dark:text-fuchsia-400' : 'text-gray-500' }}">Drafts ({{ $counts['draft'] ?? 0 }})</a>
                    <a href="{{ route('admin.hospitals.index', array_merge(request()->query(), ['status' => 'published'])) }}" 
                       class="px-3 py-2 text-xs font-semibold border-l border-gray-200 dark:border-gray-600 hover:bg-sky-50 dark:hover:bg-sky-900/20 {{ request('status') === 'published' ? 'bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-400' : 'text-gray-500' }}">Published ({{ $counts['published'] ?? 0 }})</a>
                    <a href="{{ route('admin.hospitals.index', array_merge(request()->query(), ['featured' => 1])) }}" 
                       class="px-3 py-2 text-xs font-semibold border-l border-gray-200 dark:border-gray-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 {{ request('featured') ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400' : 'text-gray-500' }}">Featured</a>
                    <a href="{{ route('admin.hospitals.index', array_merge(request()->query(), ['verified' => 1])) }}" 
                       class="px-3 py-2 text-xs font-semibold border-l border-gray-200 dark:border-gray-600 hover:bg-green-50 dark:hover:bg-green-900/20 {{ request('verified') == '1' ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400' : 'text-gray-500' }}">Verified</a>
                </div>
                <select name="per_page" onchange="this.form.submit()" class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 per page</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                    <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200 per page</option>
                    <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500 per page</option>
                </select>
                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm hover:bg-emerald-700 transition-colors font-bold">Filter</button>
                @if(request()->anyFilled(['search', 'featured', 'verified']))
                    <a href="{{ route('admin.hospitals.index') }}" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Clear</a>
                @endif
            </form>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openImportModal = true" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Import
            </button>
            <a href="{{ route('admin.hospitals.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-medium hover:opacity-90 shadow transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Hospital
            </a>
        </div>

        {{-- Import Modal --}}
        <div x-show="openImportModal" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div @click.away="openImportModal = false" class="transform transition-all w-full max-w-lg">
                <livewire:admin.bulk-importer type="hospital" />
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
                <option value="activate">Activate Selected</option>
                <option value="deactivate">Deactivate Selected</option>
                <option value="verify">Verify Selected</option>
                <option value="feature">Feature Selected</option>
                <option value="unfeature">Remove Feature</option>
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
            
            <form action="{{ route('admin.hospitals.bulk-action') }}" method="POST">
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
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Name</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">Type</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">Phone</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Dates</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($hospitals as $hospital)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors {{ $hospital->featured ? 'bg-amber-50/30 dark:bg-amber-900/10' : '' }}" :class="selectedIds.includes('{{ $hospital->id }}') ? '!bg-sky-50/50 dark:!bg-sky-900/20' : ''">
                    <td class="px-4 py-3">
                        <input type="checkbox" value="{{ $hospital->id }}" x-model="selectedIds" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200 text-xs">{{ $hospital->name }}</td>
                    <td class="px-4 py-3 hidden md:table-cell"><span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs capitalize">{{ $hospital->type }}</span></td>
                    <td class="px-4 py-3 hidden md:table-cell text-xs text-gray-500 dark:text-gray-400">{{ $hospital->phone ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1 flex-wrap">
                            @if($hospital->status === 'draft')
                                <span class="px-2 py-0.5 rounded-full bg-fuchsia-100 dark:bg-fuchsia-900/30 text-fuchsia-700 dark:text-fuchsia-400 text-xs font-medium border border-fuchsia-200 dark:border-fuchsia-800">Draft</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 text-xs font-medium">Published</span>
                            @endif

                            @if($hospital->verified)
                                <span class="px-2 py-0.5 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium">✓ Verified</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs">Unverified</span>
                            @endif
                            @if($hospital->featured) <span class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs">⭐</span> @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex flex-col gap-1">
                            <span class="text-[11px] text-gray-400 dark:text-gray-500" title="Published">P: {{ $hospital->created_at?->format('d M y, h:i a') }}</span>
                            <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium" title="Last Updated">U: {{ $hospital->updated_at?->format('d M y, h:i a') }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('hospitals.show', $hospital->slug) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs hover:bg-emerald-100 font-medium transition-colors" title="View Live">Live</a>
                            <a href="{{ route('admin.hospitals.edit', $hospital->id) }}" class="px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 text-xs hover:bg-sky-100 font-medium transition-colors">Edit</a>
                            <button type="button" onclick="confirmDelete('{{ route('admin.hospitals.destroy', $hospital->id) }}', 'Delete this hospital?')" class="px-2.5 py-1 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs hover:bg-red-100 font-medium transition-colors">Delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">No hospitals found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $hospitals->links() }}</div>
    </div>
</div>
@endsection
