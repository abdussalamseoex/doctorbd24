@extends('admin.layouts.app')
@section('title', 'Specialties')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- Add form --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <h2 class="font-semibold text-gray-700 dark:text-gray-200 mb-4 text-sm">Add New Specialty</h2>
            <form action="{{ route('admin.specialties.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1.5">Name (English)</label>
                    <input type="text" name="name_en" required placeholder="e.g., Cardiology" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1.5">নাম (বাংলা)</label>
                    <input type="text" name="name_bn" required placeholder="যেমন: হৃদরোগ" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1.5">Icon (Emoji)</label>
                    <input type="text" name="icon" placeholder="🫀" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white text-sm font-medium hover:opacity-90 shadow transition-all">
                    Add Specialty
                </button>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="lg:col-span-3">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-semibold text-gray-700 dark:text-gray-200 text-sm">All Specialties ({{ $specialties->total() }})</h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[600px] overflow-y-auto">
                @foreach($specialties as $sp)
                <div class="px-4 py-3 flex items-center justify-between" x-data="{ editing: false }">
                    <div class="flex items-center gap-3 flex-1">
                        <span class="text-xl">{{ $sp->icon }}</span>
                        <div x-show="!editing" class="flex-1">
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-200">{{ $sp->getTranslation('name', 'en') }}</p>
                            <p class="text-xs text-gray-400">{{ $sp->getTranslation('name', 'bn') }} · {{ $sp->doctors_count }} doctors</p>
                        </div>
                        <form x-show="editing" method="POST" action="{{ route('admin.specialties.update', $sp->id) }}" class="flex-1 flex gap-2 items-center">
                            @csrf @method('PUT')
                            <input type="text" name="name_en" value="{{ $sp->getTranslation('name', 'en') }}" class="px-2 py-1 text-xs rounded border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 w-24 focus:outline-none">
                            <input type="text" name="name_bn" value="{{ $sp->getTranslation('name', 'bn') }}" class="px-2 py-1 text-xs rounded border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 w-24 focus:outline-none">
                            <input type="text" name="icon" value="{{ $sp->icon }}" class="px-2 py-1 text-xs rounded border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 w-10 focus:outline-none">
                            <button type="submit" class="px-2 py-1 rounded text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 font-medium">Save</button>
                        </form>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
                        <a href="{{ route('doctors.index') }}?specialty={{ $sp->slug }}" target="_blank" class="px-2 py-1 rounded text-xs bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 transition-colors font-medium" title="View Live">Live</a>
                        <button @click="editing = !editing" class="px-2 py-1 rounded text-xs bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 hover:bg-sky-100 transition-colors font-medium">Edit</button>
                        <button type="button" onclick="confirmDelete('{{ route('admin.specialties.destroy', $sp->id) }}')" class="px-2 py-1 rounded text-xs bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-100 transition-colors font-medium">✕</button>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $specialties->links() }}</div>
        </div>
    </div>
</div>
@endsection
