@extends('admin.layouts.app')
@section('title', 'Diseases & Conditions')

@section('content')
<div class="space-y-6">
    {{-- Header & Stats --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </span>
                Diseases & Conditions Guide
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Manage comprehensive medical condition articles, symptoms, causes & doctor mappings</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.diseases.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New Disease
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.diseases.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search disease name or slug..."
                       class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
            </div>
            <div class="sm:col-span-3">
                <select name="specialty_id" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    <option value="">All Specialties</option>
                    @foreach($specialties as $s)
                        <option value="{{ $s->id }}" @selected(request('specialty_id') == $s->id)>{{ $s->getTranslation('name', 'en') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-3">
                <select name="status" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    <option value="all" @selected(request('status') === 'all')>All Statuses ({{ $counts['all'] }})</option>
                    <option value="published" @selected(request('status') === 'published')>Published ({{ $counts['published'] }})</option>
                    <option value="draft" @selected(request('status') === 'draft')>Drafts ({{ $counts['draft'] }})</option>
                </select>
            </div>
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="w-full px-4 py-2 bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 text-white rounded-xl text-xs font-semibold transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'specialty_id', 'status']))
                    <a href="{{ route('admin.diseases.index') }}" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl text-xs font-medium hover:bg-gray-200 transition-colors flex items-center justify-center">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Diseases List Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-50/75 dark:bg-gray-700/50 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4">Disease Name</th>
                        <th class="px-6 py-4">Primary Specialty</th>
                        <th class="px-6 py-4">Linked Symptoms</th>
                        <th class="px-6 py-4">Status & Badges</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($diseases as $disease)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($disease->image)
                                    <img src="{{ asset('storage/' . $disease->image) }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 dark:border-gray-700 flex-shrink-0">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-base flex-shrink-0 border border-sky-100 dark:border-sky-800">
                                        {{ $disease->first_letter ?: 'D' }}
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('admin.diseases.edit', $disease->id) }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-sky-600 dark:hover:text-sky-400 transition-colors block">
                                        {{ $disease->getTranslation('name', 'en') }}
                                    </a>
                                    <div class="text-xs text-gray-400 mt-0.5 flex items-center gap-2">
                                        <span class="text-emerald-600 dark:text-emerald-400">{{ $disease->getTranslation('name', 'bn') }}</span>
                                        <span>•</span>
                                        <span class="font-mono text-[11px] text-gray-400">/{{ $disease->slug }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($disease->primarySpecialty)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                                    {{ $disease->primarySpecialty->getTranslation('name', 'en') }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">Not Assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                {{ $disease->symptoms->count() }} Symptoms
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @if($disease->status === 'published')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
                                        Draft
                                    </span>
                                @endif

                                @if($disease->is_featured)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">
                                        Featured
                                    </span>
                                @endif

                                @if($disease->is_emergency)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                        Emergency
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.diseases.edit', $disease->id) }}" class="p-2 text-gray-500 hover:text-sky-600 dark:hover:text-sky-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Edit Disease">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <button type="button" onclick="if(confirm('Are you sure you want to delete this disease?')) confirmDelete('{{ route('admin.diseases.destroy', $disease->id) }}')" class="p-2 text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            No diseases found matching your search.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($diseases->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $diseases->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
