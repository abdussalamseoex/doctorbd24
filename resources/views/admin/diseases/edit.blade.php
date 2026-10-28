@extends('admin.layouts.app')
@section('title', 'Edit Disease: ' . $disease->getTranslation('name', 'en'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                Edit: {{ $disease->getTranslation('name', 'en') }}
                <span class="text-emerald-600 dark:text-emerald-400 font-normal">({{ $disease->getTranslation('name', 'bn') }})</span>
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Update sections, symptom checker mappings, and citations</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('diseases.show', $disease->slug) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800 text-xs font-semibold hover:bg-sky-100 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View on Site
            </a>
            <a href="{{ route('admin.diseases.index') }}" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                ← Back
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.diseases.update', $disease->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.diseases._form')
    </form>
</div>
@endsection
