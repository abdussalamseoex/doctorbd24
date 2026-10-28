@extends('admin.layouts.app')
@section('title', 'Symptom Checker Management')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                Symptom Checker Taxonomy
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Manage symptoms, anatomical body parts, and triage questions for the interactive symptom checker</p>
        </div>
        <a href="{{ route('admin.symptoms.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Symptom
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.symptoms.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-5">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search symptom name or slug..."
                       class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-300">
            </div>
            <div class="sm:col-span-4">
                <select name="body_part_id" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-300">
                    <option value="">All Body Areas / Organs</option>
                    @foreach($bodyParts as $bp)
                        <option value="{{ $bp->id }}" @selected(request('body_part_id') == $bp->id)>{{ $bp->getTranslation('name', 'en') }} ({{ $bp->getTranslation('name', 'bn') }})</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-3 flex gap-2">
                <button type="submit" class="w-full px-4 py-2 bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 text-white rounded-xl text-xs font-semibold transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'body_part_id']))
                    <a href="{{ route('admin.symptoms.index') }}" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl text-xs font-medium hover:bg-gray-200 transition-colors flex items-center justify-center">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Symptoms Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-50/75 dark:bg-gray-700/50 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4">Symptom</th>
                        <th class="px-6 py-4">Body Area</th>
                        <th class="px-6 py-4">Scope</th>
                        <th class="px-6 py-4">Urgency Triage</th>
                        <th class="px-6 py-4">Factors / Questions</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($symptoms as $sym)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <a href="{{ route('admin.symptoms.edit', $sym->id) }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                                    {{ $sym->getTranslation('name', 'en') }}
                                </a>
                                <div class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">
                                    {{ $sym->getTranslation('name', 'bn') }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($sym->bodyPart)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                    {{ $sym->bodyPart->getTranslation('name', 'en') }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">Whole Body</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <span class="capitalize">{{ $sym->age_group }}</span> • <span class="capitalize">{{ $sym->gender_scope }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($sym->urgency_level === 'emergency' || $sym->is_emergency)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                    🔴 Emergency
                                </span>
                            @elseif($sym->urgency_level === 'urgent')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
                                    🟡 Urgent Care
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                    🟢 Routine
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 border border-purple-100 dark:border-purple-800">
                                {{ $sym->factors->count() }} Factor Questions
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.symptoms.edit', $sym->id) }}" class="p-2 text-gray-500 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Edit Symptom">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <button type="button" onclick="if(confirm('Are you sure you want to delete this symptom?')) confirmDelete('{{ route('admin.symptoms.destroy', $sym->id) }}')" class="p-2 text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            No symptoms found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($symptoms->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $symptoms->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
