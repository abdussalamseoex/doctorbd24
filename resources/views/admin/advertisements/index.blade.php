@extends('admin.layouts.app')
@section('title', 'Ads & Banners')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Advertisements</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage promotional banners across the site.</p>
    </div>
    <a href="{{ route('admin.advertisements.create') }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add New Ad
    </a>
</div>

<div x-data="{ openDeleteModal: false, deleteId: null }" class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
            <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-xs uppercase font-semibold text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="px-6 py-4">Image</th>
                    <th class="px-6 py-4">Title & Details</th>
                    <th class="px-6 py-4">Position</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($advertisements as $ad)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img src="{{ asset('storage/' . $ad->image_path) }}" alt="{{ $ad->title }}" class="h-16 w-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900 dark:text-gray-100">{{ $ad->title }}</div>
                        @if($ad->target_url)
                            <a href="{{ $ad->target_url }}" target="_blank" class="text-sky-600 hover:underline text-xs flex items-center gap-1 mt-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                Link URL
                            </a>
                        @else
                            <span class="text-xs text-gray-400">No Link</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 text-xs font-semibold">{{ ucfirst(str_replace('_', ' ', $ad->position)) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($ad->is_active)
                            <span class="px-2.5 py-1 rounded-full bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold">Active</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-semibold">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.advertisements.edit', $ad->id) }}" class="p-2 text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-900/30 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <button type="button" @click="openDeleteModal = true; deleteId = {{ $ad->id }}" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        No advertisements found. <a href="{{ route('admin.advertisements.create') }}" class="text-sky-600 hover:underline">Create one</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($advertisements->hasPages())
    <div class="p-4 border-t border-gray-200 dark:border-gray-800">
        {{ $advertisements->links() }}
    </div>
    @endif

    {{-- Delete Modal --}}
    <div x-show="openDeleteModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak style="display: none;">
        <div @click.away="openDeleteModal = false" class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl w-full max-w-sm mx-4 transform transition-all">
            <div class="flex items-center gap-3 mb-4 text-red-600 dark:text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Confirm Delete</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Are you sure you want to delete this advertisement? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="openDeleteModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                <form :action="'/admin/advertisements/' + deleteId" method="POST" class="inline m-0 p-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition shadow-sm">Delete Ad</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
