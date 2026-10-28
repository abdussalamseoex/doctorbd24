@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $isBn = $locale === 'bn';
    $diseasesIndexRoute = $isBn ? 'bn.diseases.index' : 'diseases.index';
    $diseasesShowRoute = $isBn ? 'bn.diseases.show' : 'diseases.show';
    $symptomCheckerRoute = $isBn ? 'bn.symptom-checker.index' : 'symptom-checker.index';
@endphp

<div class="bg-gray-50 dark:bg-gray-950 min-h-screen">

    {{-- ── Hero Section ────────────────────────────────────────── --}}
    <section class="bg-[#0A2540] text-white pt-10 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs text-sky-200/80 mb-6">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">{{ $isBn ? 'হোম' : 'Home' }}</a>
                <span>/</span>
                <span class="text-white font-medium">{{ $isBn ? 'রোগ ও স্বাস্থ্য সমস্যা' : 'Diseases & Conditions' }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                {{-- Left: Heading, Intro, Search --}}
                <div class="lg:col-span-7 space-y-4">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ $isBn ? 'মেয়ো ক্লিনিক আন্তর্জাতিক মানদণ্ডে তৈরি স্বাস্থ্য নির্দেশিকা' : 'Mayo Clinic Standard Health Library' }}
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                        {{ $isBn ? 'রোগ ও স্বাস্থ্য সমস্যা নির্দেশিকা' : 'Diseases & Conditions' }}
                    </h1>
                    
                    <p class="text-sm sm:text-base text-sky-100/90 max-w-2xl font-normal leading-relaxed">
                        {{ $isBn ? 'বিভিন্ন রোগের সঠিক লক্ষণ, কারণ, প্রতিরোধ, চিকিৎসা এবং বাংলাদেশের বিশেষজ্ঞ ডাক্তারের সন্ধান সহজে বাংলায় জেনে নিন।' : 'Accurate, expert-reviewed medical guidance on symptoms, causes, prevention, treatments, and finding the right specialist doctors in Bangladesh.' }}
                    </p>

                    {{-- Search Form --}}
                    <form method="GET" action="{{ route($diseasesIndexRoute) }}" class="pt-2 max-w-xl">
                        @if($selectedLetter)
                            <input type="hidden" name="letter" value="{{ $selectedLetter }}">
                        @endif
                        <div class="relative flex items-center">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="{{ $isBn ? 'রোগের নাম লিখে খুঁজুন (যেমন: মাইগ্রেন, ডেঙ্গু, ডায়াবেটিস)...' : 'Search conditions, symptoms, or diseases...' }}"
                                   class="w-full pl-11 pr-24 py-3.5 rounded-2xl bg-white text-gray-900 placeholder-gray-400 shadow-xl focus:outline-none focus:ring-4 focus:ring-sky-400/50 text-sm">
                            <div class="absolute left-4 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <button type="submit" class="absolute right-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold transition-all">
                                {{ $isBn ? 'খুঁজুন' : 'Search' }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Right: A-Z Directory Matrix Card --}}
                <div class="lg:col-span-5 bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 shadow-2xl">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-sky-200">
                            {{ $isBn ? 'আদ্যক্ষর অনুযায়ী খুঁজুন (A-Z)' : 'Find diseases by first letter' }}
                        </h2>
                        @if($selectedLetter)
                            <a href="{{ route($diseasesIndexRoute) }}" class="text-xs font-semibold text-emerald-300 hover:text-white underline">
                                {{ $isBn ? 'সব দেখুন (Clear)' : 'Clear Filter' }}
                            </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-7 sm:grid-cols-9 gap-1.5 text-center">
                        {{-- All / Reset Button --}}
                        <a href="{{ route($diseasesIndexRoute) }}"
                           class="py-2 rounded-lg text-xs font-bold transition-all {{ empty($selectedLetter) ? 'bg-emerald-500 text-white shadow-md font-black' : 'bg-white/10 text-white/80 hover:bg-white/20' }}">
                            {{ $isBn ? 'সব' : 'All' }}
                        </a>

                        @foreach(range('A', 'Z') as $char)
                            @php
                                $count = $availableLetters[$char] ?? 0;
                                $isSelected = $selectedLetter === $char;
                            @endphp
                            <a href="{{ route($diseasesIndexRoute, ['letter' => $char]) }}"
                               class="py-2 rounded-lg text-xs font-bold transition-all {{ $isSelected ? 'bg-emerald-400 text-gray-900 shadow-lg font-black ring-2 ring-white' : ($count > 0 ? 'bg-white/15 text-white hover:bg-white/30' : 'bg-white/5 text-white/50 hover:bg-white/15') }}"
                               title="{{ $char }}: {{ $count }} {{ $isBn ? 'টি রোগ' : 'diseases' }}">
                                {{ $char }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Symptom Checker Highlight Card (Mayo Clinic Signature Banner) ── --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-800 rounded-3xl shadow-lg overflow-hidden text-white border border-white/10 p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center flex-shrink-0 border border-white/20 text-3xl">
                    🩺
                </div>
                <div>
                    <span class="text-[11px] font-bold tracking-wider uppercase bg-white/20 px-2.5 py-0.5 rounded-full text-white inline-block mb-1">
                        {{ $isBn ? 'ইন্টারঅ্যাক্টিভ লক্ষণ নির্দেশক' : 'Interactive Health Triage' }}
                    </span>
                    <h2 class="text-2xl font-bold tracking-tight text-white">
                        {{ $isBn ? 'সিম্পটম চেকার (Symptom Checker)' : 'Symptom Checker' }}
                    </h2>
                    <p class="text-sm text-teal-100 max-w-xl mt-1 leading-relaxed">
                        {{ $isBn ? 'প্রাপ্তবয়স্ক বা শিশুদের শারীরিক উপসর্গগুলো সিলেক্ট করুন। সম্ভাব্য কারণ, সতর্কতা এবং কোন বিশেষজ্ঞ ডাক্তার দেখাতে হবে তা জেনে নিন।' : 'Check adult and pediatric symptoms to discover potential causes, red flags, and recommended medical specialists in Bangladesh.' }}
                    </p>
                </div>
            </div>

            <div class="flex-shrink-0 w-full md:w-auto">
                <a href="{{ route($symptomCheckerRoute) }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-white text-emerald-800 hover:bg-emerald-50 rounded-2xl font-bold text-sm shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                    <span>{{ $isBn ? 'লক্ষণ পরীক্ষা শুরু করুন' : 'Check Symptoms' }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ── Popular & Featured Diseases ──────────────────────────── --}}
    @if($featuredDiseases->count() > 0 && empty($selectedLetter) && !request('search'))
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <span class="w-2.5 h-6 rounded-full bg-emerald-500 inline-block"></span>
                    {{ $isBn ? 'বহুল অনুসন্ধানকৃত প্রধান রোগসমূহ' : 'Featured Health Conditions' }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $isBn ? 'রোগীদের দ্বারা সচরাচর অনুসন্ধানকৃত শারীরিক সমস্যাগুলোর লক্ষণ ও চিকিৎসা নির্দেশিকা' : 'Frequently researched medical conditions with comprehensive symptoms and care guides' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredDiseases as $featured)
            <div class="group bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700/80 transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        @if($featured->primarySpecialty)
                            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-800">
                                {{ $featured->primarySpecialty->getTranslation('name', $locale) }}
                            </span>
                        @endif
                        @if($featured->is_emergency)
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400">
                                {{ $isBn ? 'জরুরি' : 'Urgent' }}
                            </span>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        <a href="{{ route($diseasesShowRoute, $featured->slug) }}">
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

                <div class="pt-5 mt-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                    <span>{{ $isBn ? 'লক্ষণ ও চিকিৎসা দেখুন' : 'Symptoms & Care' }}</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── Body System Anatomical Categories ────────────────────── --}}
    @if(empty($selectedLetter) && !request('search'))
    <section class="bg-gray-100/70 dark:bg-gray-900/50 py-12 border-y border-gray-200/60 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <span class="w-2.5 h-6 rounded-full bg-indigo-500 inline-block"></span>
                    {{ $isBn ? 'শারীরিক অঙ্গ বা সিস্টেম অনুযায়ী লক্ষণ' : 'Browse by Body Location' }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $isBn ? 'আপনার আক্রান্ত অঙ্গ নির্বাচন করে সংশ্লিষ্ট উপসর্গ ও রোগের তথ্য পরীক্ষা করুন' : 'Select an anatomical region to check specific symptoms in our symptom checker' }}
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                @foreach($bodyParts as $part)
                <a href="{{ route($symptomCheckerRoute, ['part' => $part->slug]) }}" 
                   class="bg-white dark:bg-gray-800 rounded-2xl p-4 text-center border border-gray-100 dark:border-gray-700/60 hover:border-emerald-400 dark:hover:border-emerald-500 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <span class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 block line-clamp-1">
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
    @endif

    {{-- ── All Diseases Directory List ──────────────────────────── --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{-- Section Header & Quick A-Z Filter Bar --}}
        <div class="space-y-4 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <span class="w-2.5 h-6 rounded-full bg-emerald-500 inline-block"></span>
                        {{ $isBn ? 'রোগ ও স্বাস্থ্য সমস্যার পূর্ণাঙ্গ তালিকা' : 'Complete Disease Directory' }}
                        @if($selectedLetter)
                            <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                — {{ $isBn ? 'অক্ষর:' : 'Letter:' }} {{ $selectedLetter }}
                            </span>
                        @endif
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $isBn ? 'মোট' : 'Showing' }} {{ $diseases->total() }} {{ $isBn ? 'টি রোগের তথ্য পাওয়া গেছে' : 'conditions found' }}
                    </p>
                </div>

                @if($selectedLetter || request('search'))
                    <a href="{{ route($diseasesIndexRoute) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 text-xs font-semibold self-start sm:self-auto transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ $isBn ? 'সব রোগ দেখুন (ফিল্টার মুছুন)' : 'Clear Filter' }}</span>
                    </a>
                @endif
            </div>

            {{-- Inline A-Z Letter Filter Bar (Mayo Clinic Standard) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-3 shadow-sm border border-gray-100 dark:border-gray-700 overflow-x-auto">
                <div class="flex items-center gap-1 min-w-max">
                    <a href="{{ route($diseasesIndexRoute) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ empty($selectedLetter) ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        {{ $isBn ? 'সকল' : 'All' }}
                    </a>

                    @foreach(range('A', 'Z') as $char)
                        @php
                            $c = $availableLetters[$char] ?? 0;
                            $isActive = $selectedLetter === $char;
                        @endphp
                        <a href="{{ route($diseasesIndexRoute, ['letter' => $char]) }}"
                           class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center transition-all {{ $isActive ? 'bg-emerald-500 text-white shadow font-black ring-2 ring-emerald-300' : ($c > 0 ? 'text-gray-800 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-600' : 'text-gray-300 dark:text-gray-600 hover:bg-gray-50') }}"
                           title="{{ $char }}: {{ $c }} {{ $isBn ? 'টি রোগ' : 'diseases' }}">
                            {{ $char }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Diseases Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($diseases as $item)
            <a href="{{ route($diseasesShowRoute, $item->slug) }}" 
               class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 hover:shadow-md transition-all hover:border-emerald-300 dark:hover:border-emerald-700 flex items-start gap-4 group">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-extrabold flex items-center justify-center flex-shrink-0 text-base border border-emerald-100 dark:border-emerald-900 group-hover:scale-105 transition-transform">
                    {{ $item->first_letter ?: strtoupper(substr($item->slug, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            {{ $item->getTranslation('name', $locale) }}
                        </h3>
                        @if($item->is_emergency)
                            <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0" title="Emergency / Urgent Condition"></span>
                        @endif
                    </div>

                    @if($isBn && $item->getTranslation('name', 'en') !== $item->getTranslation('name', 'bn'))
                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ $item->getTranslation('name', 'en') }}</p>
                    @endif

                    @if($item->primarySpecialty)
                        <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 mt-1.5 inline-block bg-emerald-50/70 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md">
                            🩺 {{ $item->primarySpecialty->getTranslation('name', $locale) }}
                        </span>
                    @endif
                </div>

                <div class="text-gray-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors pt-1">
                    <svg class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
            @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-3 py-16 text-center text-gray-400 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-2xl mb-3">
                    🔍
                </div>
                <p class="text-base font-bold text-gray-700 dark:text-gray-200">
                    {{ $isBn ? 'কোনো রোগের তথ্য পাওয়া যায়নি।' : 'No conditions found for this search/letter.' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    {{ $isBn ? 'অন্য কোনো অক্ষর বা নাম লিখে খুঁজুন অথবা সব রোগ দেখুন।' : 'Try selecting another letter or clear the filter to see all conditions.' }}
                </p>
                <a href="{{ route($diseasesIndexRoute) }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold mt-4 shadow-sm transition-colors">
                    <span>{{ $isBn ? 'সকল রোগ দেখুন' : 'View all conditions' }}</span>
                </a>
            </div>
            @endforelse
        </div>

        @if($diseases->hasPages())
        <div class="mt-10">
            {{ $diseases->links() }}
        </div>
        @endif
    </section>

</div>
@endsection
