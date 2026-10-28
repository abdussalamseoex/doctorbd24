@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $isBn = $locale === 'bn';
@endphp

<div class="bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 min-h-screen">

    {{-- ── Hero Section ────────────────────────────────────────── --}}
    <section class="relative bg-[#0A2540] text-white pt-12 pb-20 overflow-hidden">
        {{-- Subtle background medical graphics --}}
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs text-sky-200/80 mb-6">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">{{ $isBn ? 'হোম' : 'Home' }}</a>
                <span>/</span>
                <span class="text-white font-medium">{{ $isBn ? 'রোগ ও স্বাস্থ্য সমস্যা' : 'Diseases & Conditions' }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-sky-500/20 text-sky-300 border border-sky-400/30">
                        <svg class="w-3.5 h-3.5 text-sky-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ $isBn ? 'মেয়ো ক্লিনিক আন্তর্জাতিক মানদণ্ডে তৈরি' : 'Mayo Clinic Standard Health Library' }}
                    </span>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                        {{ $isBn ? 'রোগ ও স্বাস্থ্য সমস্যা নির্দেশিকা' : 'Diseases & Conditions' }}
                    </h1>
                    
                    <p class="text-base sm:text-lg text-sky-100/90 max-w-2xl font-normal leading-relaxed">
                        {{ $isBn ? 'বিভিন্ন রোগের সঠিক লক্ষণ, কারণ, প্রতিরোধ, চিকিৎসা এবং বিশেষজ্ঞ ডাক্তারের সন্ধান সহজে বাংলায় জেনে নিন।' : 'Easy-to-understand, expert-reviewed answers about diseases, medical conditions, and finding the right doctors in Bangladesh.' }}
                    </p>

                    {{-- Search Form --}}
                    <form method="GET" action="{{ route('diseases.index') }}" class="pt-2 max-w-xl">
                        <div class="relative flex items-center">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="{{ $isBn ? 'রোগের নাম বা কোনো লক্ষণ লিখে খুঁজুন (যেমন: মাইগ্রেন, ডেঙ্গু)...' : 'Search diseases, symptoms, or conditions...' }}"
                                   class="w-full pl-11 pr-24 py-3.5 rounded-2xl bg-white text-gray-900 placeholder-gray-400 shadow-xl focus:outline-none focus:ring-4 focus:ring-sky-400/50 text-sm">
                            <div class="absolute left-4 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <button type="submit" class="absolute right-2 px-4 py-2 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white rounded-xl text-xs font-semibold transition-all">
                                {{ $isBn ? 'খুঁজুন' : 'Search' }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- A-Z Letter Directory Grid (Mayo Clinic Header Style) --}}
                <div class="lg:col-span-5 bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 shadow-2xl">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-sky-200 mb-3 flex items-center justify-between">
                        <span>{{ $isBn ? 'আদ্যক্ষর অনুযায়ী রোগ খুঁজুন (A-Z)' : 'Find diseases by first letter' }}</span>
                        @if($selectedLetter)
                            <a href="{{ route('diseases.index') }}" class="text-xs text-sky-300 hover:underline">
                                {{ $isBn ? 'সব দেখুন' : 'Clear' }}
                            </a>
                        @endif
                    </h2>

                    <div class="grid grid-cols-7 sm:grid-cols-9 gap-1.5 text-center">
                        @foreach(range('A', 'Z') as $char)
                            @php
                                $hasCount = isset($availableLetters[$char]);
                                $isSelected = $selectedLetter === $char;
                            @endphp
                            <a href="{{ route('diseases.index', ['letter' => $char]) }}"
                               class="py-2 rounded-lg text-xs font-bold transition-all {{ $isSelected ? 'bg-sky-400 text-gray-900 shadow-md font-extrabold' : ($hasCount ? 'bg-white/15 text-white hover:bg-white/30' : 'bg-white/5 text-white/30 cursor-not-allowed') }}">
                                {{ $char }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Symptom Checker Highlight Card (Mayo Clinic Signature Banner) ── --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">
        <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 rounded-3xl shadow-xl overflow-hidden text-white border border-white/10 p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center flex-shrink-0 border border-white/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-xs font-bold tracking-wider uppercase bg-white/20 px-2.5 py-0.5 rounded-full text-white inline-block mb-1">
                        {{ $isBn ? 'ইন্টারঅ্যাক্টিভ লক্ষণ পরীক্ষক' : 'Interactive Tool' }}
                    </span>
                    <h2 class="text-2xl font-bold tracking-tight text-white">
                        {{ $isBn ? 'সিম্পটম চেকার (Symptom Checker)' : 'Symptom Checker' }}
                    </h2>
                    <p class="text-sm text-teal-100 max-w-xl mt-1">
                        {{ $isBn ? 'আপনার শারীরিক উপসর্গগুলো সিলেক্ট করুন। আমাদের স্মার্ট চেকার সম্ভাব্য কারণ এবং কোন বিশেষজ্ঞ ডাক্তার দেখাতে হবে তা জানিয়ে দেবে।' : 'Find out what could be causing your symptoms and discover which specialist doctor you should consult.' }}
                    </p>
                </div>
            </div>

            <div class="flex-shrink-0 w-full md:w-auto">
                <a href="{{ route('symptom-checker.index') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white text-teal-800 hover:bg-teal-50 rounded-2xl font-bold text-sm shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                    <span>{{ $isBn ? 'লক্ষণ পরীক্ষা করুন' : 'Check Symptoms' }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ── Popular & Featured Diseases ──────────────────────────── --}}
    @if($featuredDiseases->count() > 0)
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <span class="w-2.5 h-6 rounded-full bg-sky-500 inline-block"></span>
                    {{ $isBn ? 'বহুল প্রচলিত রোগ ও স্বাস্থ্য সমস্যা' : 'Featured Health Conditions' }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $isBn ? 'সচরাচর পরিলক্ষিত প্রধান শারীরিক সমস্যাগুলোর সঠিক লক্ষণ ও প্রতিকার' : 'Common health conditions frequently researched by patients' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredDiseases as $featured)
            <div class="group bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700/80 transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        @if($featured->primarySpecialty)
                            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-100 dark:border-sky-800">
                                {{ $featured->primarySpecialty->getTranslation('name', $locale) }}
                            </span>
                        @endif
                        @if($featured->is_emergency)
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400">
                                {{ $isBn ? 'জরুরি' : 'Urgent' }}
                            </span>
                        @endif
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors">
                        <a href="{{ route('diseases.show', $featured->slug) }}">
                            {{ $featured->getTranslation('name', $locale) }}
                        </a>
                    </h3>

                    @if($isBn && $featured->getTranslation('name', 'en') !== $featured->getTranslation('name', 'bn'))
                        <p class="text-xs font-medium text-gray-400 mt-0.5">{{ $featured->getTranslation('name', 'en') }}</p>
                    @endif

                    <div class="text-xs text-gray-600 dark:text-gray-300 mt-3 line-clamp-3 leading-relaxed">
                        {{ strip_tags($featured->getTranslation('overview', $locale)) }}
                    </div>
                </div>

                <div class="pt-5 mt-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-xs font-semibold text-sky-600 dark:text-sky-400">
                    <span>{{ $isBn ? 'লক্ষণ ও চিকিৎসা দেখুন' : 'Symptoms & Care' }}</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── Body System Anatomical Categories ────────────────────── --}}
    <section class="bg-gray-100/70 dark:bg-gray-900/50 py-14 border-y border-gray-200/60 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <span class="w-2.5 h-6 rounded-full bg-indigo-500 inline-block"></span>
                    {{ $isBn ? 'শরীরের অঙ্গ বা বিভাগ অনুযায়ী লক্ষণ' : 'Browse by Body Location' }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $isBn ? 'আপনার আক্রান্ত অঙ্গ নির্বাচন করে সংশ্লিষ্ট উপসর্গ ও রোগের তালিকা দেখুন' : 'Explore anatomical regions to check specific symptoms' }}
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                @foreach($bodyParts as $part)
                <a href="{{ route('symptom-checker.index', ['part' => $part->slug]) }}" 
                   class="bg-white dark:bg-gray-800 rounded-2xl p-4 text-center border border-gray-100 dark:border-gray-700/60 hover:border-sky-300 dark:hover:border-sky-600 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <span class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200 group-hover:text-sky-600 dark:group-hover:text-sky-400 block line-clamp-1">
                        {{ $part->getTranslation('name', $locale) }}
                    </span>
                    <span class="text-[11px] text-gray-400 mt-0.5 block">
                        {{ $part->symptoms_count }} {{ $isBn ? 'টি লক্ষণ' : 'symptoms' }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── All Diseases Directory List ──────────────────────────── --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <span class="w-2.5 h-6 rounded-full bg-emerald-500 inline-block"></span>
                    {{ $isBn ? 'সকল রোগ ও সমস্যার তালিকা' : 'Complete Directory' }}
                    @if($selectedLetter)
                        <span class="text-sm font-normal text-sky-600 dark:text-sky-400">
                            ({{ $isBn ? 'অক্ষর:' : 'Letter:' }} {{ $selectedLetter }})
                        </span>
                    @endif
                </h2>
                <p class="text-xs text-gray-400 mt-1">
                    {{ $isBn ? 'মোট' : 'Showing' }} {{ $diseases->total() }} {{ $isBn ? 'টি রোগের তথ্য পাওয়া গেছে' : 'conditions available' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($diseases as $item)
            <a href="{{ route('diseases.show', $item->slug) }}" 
               class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 hover:shadow-md transition-all hover:border-sky-200 dark:hover:border-sky-800 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 font-bold flex items-center justify-center flex-shrink-0 text-sm border border-sky-100 dark:border-sky-900">
                    {{ $item->first_letter ?: 'D' }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 truncate hover:text-sky-600">
                        {{ $item->getTranslation('name', $locale) }}
                    </h3>
                    @if($isBn && $item->getTranslation('name', 'en') !== $item->getTranslation('name', 'bn'))
                        <p class="text-xs text-gray-400 truncate">{{ $item->getTranslation('name', 'en') }}</p>
                    @endif
                    @if($item->primarySpecialty)
                        <span class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 mt-1 inline-block">
                            {{ $item->primarySpecialty->getTranslation('name', $locale) }}
                        </span>
                    @endif
                </div>
            </a>
            @empty
            <div class="col-span-3 py-16 text-center text-gray-400 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                <p class="text-base font-medium">{{ $isBn ? 'কোনো রোগের তথ্য পাওয়া যায়নি।' : 'No conditions found.' }}</p>
                <a href="{{ route('diseases.index') }}" class="text-xs text-sky-500 font-semibold mt-2 inline-block hover:underline">
                    {{ $isBn ? 'সব রোগ দেখুন' : 'View all conditions' }}
                </a>
            </div>
            @endforelse
        </div>

        @if($diseases->hasPages())
        <div class="mt-8">
            {{ $diseases->links() }}
        </div>
        @endif
    </section>

</div>
@endsection
