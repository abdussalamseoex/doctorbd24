@extends('layouts.app')
@section('title', '500 — Server Error | DoctorBD24')
@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="text-center max-w-lg">
        <div class="w-32 h-32 rounded-3xl bg-gradient-to-br from-red-100 to-orange-100 dark:from-red-900/30 dark:to-orange-900/30 flex items-center justify-center mx-auto mb-8 shadow-xl border border-red-200 dark:border-red-800">
            <span class="text-6xl">⚠️</span>
        </div>

        <div class="text-8xl font-black bg-gradient-to-r from-red-500 to-orange-500 bg-clip-text text-transparent mb-3 leading-none">
            500
        </div>

        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-3">সার্ভার সমস্যা</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-8">
            আমাদের সার্ভারে কিছু একটা সমস্যা হয়েছে। আমরা এটি ঠিক করতে কাজ করছি।<br>
            অনুগ্রহ করে একটু পরে আবার চেষ্টা করুন।
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-bold text-sm hover:opacity-90 shadow-lg transition-all">
                🏠 হোমপেজে যান
            </a>
            <button onclick="location.reload()" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-medium text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                🔄 আবার চেষ্টা করুন
            </button>
        </div>
    </div>
</div>
@endsection
