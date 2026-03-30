@extends('layouts.app')
@section('title', '404 — পেজটি পাওয়া যাচ্ছে না | DoctorBD24')
@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="text-center max-w-lg">
        {{-- Animated Icon --}}
        <div class="relative inline-block mb-8">
            <div class="w-32 h-32 rounded-3xl bg-gradient-to-br from-sky-100 to-indigo-100 dark:from-sky-900/30 dark:to-indigo-900/30 flex items-center justify-center mx-auto shadow-xl border border-sky-200 dark:border-sky-800">
                <span class="text-6xl">🔍</span>
            </div>
            <div class="absolute -top-2 -right-2 w-10 h-10 rounded-2xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center text-xl shadow-lg border border-red-200 dark:border-red-800">
                ❌
            </div>
        </div>

        {{-- Error Code --}}
        <div class="text-8xl font-black bg-gradient-to-r from-sky-500 to-indigo-600 bg-clip-text text-transparent mb-3 leading-none">
            404
        </div>

        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-3">
            পেজটি পাওয়া যাচ্ছে না
        </h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-8">
            আপনি যে পেজটি খুঁজছেন সেটি মুছে ফেলা হয়েছে, সরানো হয়েছে,<br>
            অথবা এটি কখনো বিদ্যমান ছিল না।
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-bold text-sm hover:opacity-90 shadow-lg transition-all hover:-translate-y-0.5">
                🏠 হোমপেজে যান
            </a>
            <a href="{{ route('doctors.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-medium text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                👨‍⚕️ ডাক্তার খুঁজুন
            </a>
            <a href="{{ route('hospitals.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-medium text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                🏥 হাসপাতাল খুঁজুন
            </a>
        </div>

        {{-- Back link --}}
        <button onclick="history.back()" class="mt-6 text-sm text-sky-500 hover:underline">
            ← আগের পেজে ফিরে যান
        </button>
    </div>
</div>
@endsection
