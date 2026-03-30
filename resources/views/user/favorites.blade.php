@extends('layouts.app')
@section('title', 'আমার ফেভারিট — DoctorBD24')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">❤️ আমার ফেভারিট</h1>
    @if($favorites->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($favorites as $fav)
        @php $item = $fav->favoriteable; @endphp
        @if($item)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-4 flex gap-3 items-center">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-100 to-indigo-100 dark:from-sky-900 dark:to-indigo-900 flex items-center justify-center text-xl">
                @if($fav->favoriteable_type === 'App\Models\Doctor') 👨‍⚕️ @else 🏥 @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-gray-800 dark:text-gray-100 truncate">{{ $item->name }}</p>
                <p class="text-xs text-gray-400">{{ $fav->favoriteable_type === 'App\Models\Doctor' ? 'ডাক্তার' : 'হাসপাতাল' }}</p>
            </div>
            @if($fav->favoriteable_type === 'App\Models\Doctor')
                <a href="{{ route('doctors.show', $item->slug) }}" class="text-xs text-sky-600 dark:text-sky-400 hover:underline">দেখুন →</a>
            @else
                <a href="{{ route('hospitals.show', $item->slug) }}" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">দেখুন →</a>
            @endif
        </div>
        @endif
        @endforeach
    </div>
    @else
    <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
        <div class="text-5xl mb-4">🤍</div>
        <p class="text-gray-500 dark:text-gray-400">আপনার কোনো ফেভারিট নেই।</p>
        <a href="{{ route('doctors.index') }}" class="mt-4 inline-block text-sky-500 hover:underline text-sm">ডাক্তার দেখুন →</a>
    </div>
    @endif
</div>
@endsection
