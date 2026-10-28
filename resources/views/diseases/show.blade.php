@extends('layouts.app')

@section('schema')
@php
    $locale = app()->getLocale();
    $isBn = $locale === 'bn';
    $name = $disease->getTranslation('name', $locale);
    $overviewText = strip_tags($disease->getTranslation('overview', $locale));
    $symptomsText = strip_tags($disease->getTranslation('symptoms_summary', $locale));
    $causesText = strip_tags($disease->getTranslation('causes', $locale));
    $preventionText = strip_tags($disease->getTranslation('prevention', $locale));
@endphp

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "MedicalCondition",
  "name": "{{ $name }}",
  "alternateName": "{{ $disease->getTranslation('name', $isBn ? 'en' : 'bn') }}",
  "description": "{{ Str::limit($overviewText, 250) }}",
  "signOrSymptom": [
    @foreach($disease->symptoms as $index => $sym)
      {
        "@type": "MedicalSymptom",
        "name": "{{ $sym->getTranslation('name', $locale) }}"
      }{{ $loop->last ? '' : ',' }}
    @endforeach
  ],
  "possibleTreatment": [
    {
      "@type": "MedicalTherapy",
      "name": "Clinical Diagnosis & Medical Consultation"
    }
  ],
  "url": "{{ url()->current() }}"
}
</script>
@endsection

@section('content')
@php
    $locale = app()->getLocale();
    $isBn = $locale === 'bn';
@endphp

<div class="bg-gray-50 dark:bg-gray-950 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">{{ $isBn ? 'হোম' : 'Home' }}</a>
            <span>/</span>
            <a href="{{ route($isBn ? 'bn.diseases.index' : 'diseases.index') }}" class="hover:text-emerald-600 transition-colors">{{ $isBn ? 'রোগ ও স্বাস্থ্য সমস্যা' : 'Diseases & Conditions' }}</a>
            <span>/</span>
            <span class="text-gray-800 dark:text-gray-200 font-semibold truncate">{{ $disease->getTranslation('name', $locale) }}</span>
        </nav>

        {{-- Emergency Banner (If applicable) --}}
        @if($disease->is_emergency)
        <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-950/40 border-l-4 border-red-500 text-red-800 dark:text-red-300 flex items-start gap-3 shadow-sm">
            <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <h4 class="font-bold text-sm text-red-900 dark:text-red-200">
                    {{ $isBn ? 'জরুরি চিকিৎসা সতর্কতা' : 'Urgent Medical Attention Notice' }}
                </h4>
                <p class="text-xs text-red-700 dark:text-red-300 mt-0.5">
                    {{ $isBn ? 'এই রোগের তীব্র লক্ষণ দেখা দিলে কোনো বিলম্ব না করে অবিলম্বে নিকটস্থ হাসপাতালের ইমার্জেন্সিতে যান বা জরুরি অ্যাম্বুলেন্স সেবা গ্রহণ করুন।' : 'Severe symptoms of this condition require immediate emergency department care. Do not delay seeking medical help.' }}
                </p>
            </div>
        </div>
        @endif

        {{-- Article Main Header Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-3 flex-wrap">
                        @if($disease->primarySpecialty)
                            <a href="{{ route('doctors.index', ['specialty' => $disease->primarySpecialty->slug]) }}" 
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-semibold bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 border border-sky-100 dark:border-sky-800 hover:bg-sky-100">
                                <span>🩺 {{ $disease->primarySpecialty->getTranslation('name', $locale) }}</span>
                            </a>
                        @endif

                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300">
                            {{ $isBn ? 'চিকিৎসক পর্যালোচিত' : 'Expert Reviewed' }}
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">
                        {{ $disease->getTranslation('name', $locale) }}
                    </h1>

                    @if($disease->getTranslation('name', 'en') !== $disease->getTranslation('name', 'bn'))
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">
                            {{ $isBn ? 'English:' : 'বাংলা নাম:' }} {{ $disease->getTranslation('name', $isBn ? 'en' : 'bn') }}
                        </p>
                    @endif
                </div>

                {{-- Action Widget: Check Symptoms or Find Doctor --}}
                <div class="flex items-center gap-3 flex-shrink-0">
                    <a href="{{ route('symptom-checker.index') }}" 
                       class="px-4 py-2.5 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 text-xs font-bold hover:bg-teal-100 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $isBn ? 'লক্ষণ মিলিয়ে দেখুন' : 'Check Symptoms' }}
                    </a>

                    @if($disease->primarySpecialty)
                        <a href="#specialist-doctors" 
                           class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $isBn ? 'বিশেষজ্ঞ ডাক্তার খুঁজুন' : 'Find Specialists' }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Quick Jump Table of Contents --}}
            <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-700 flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <span class="font-bold text-gray-400 uppercase tracking-wider text-[11px] mr-1">{{ $isBn ? 'সূচিপত্র:' : 'Jump to:' }}</span>
                <a href="#overview" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'পরিচিতি' : 'Overview' }}</a>
                <a href="#symptoms" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'লক্ষণসমূহ' : 'Symptoms' }}</a>
                <a href="#causes" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'কারণ' : 'Causes' }}</a>
                <a href="#prevention" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'প্রতিরোধ' : 'Prevention' }}</a>
                <a href="#when-to-see-doctor" class="px-3 py-1.5 rounded-xl bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 hover:bg-red-100 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'কখন ডাক্তার দেখাবেন' : 'When to See Doctor' }}</a>
                <a href="#specialist-doctors" class="px-3 py-1.5 rounded-xl bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 font-medium hover:bg-sky-100 transition-colors whitespace-nowrap">{{ $isBn ? 'বিশেষজ্ঞ ডাক্তার' : 'Doctors' }}</a>
            </div>
        </div>

        {{-- Layout Grid: 8 Cols Article Content, 4 Cols Doctor & Hospital Sidebar --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- ── Left Main Content (8 Cols) ── --}}
            <div class="lg:col-span-8 space-y-8">

                {{-- 1. Overview --}}
                @if($disease->getTranslation('overview', $locale))
                <section id="overview" class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80 scroll-mt-24">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm font-bold">1</span>
                        {{ $isBn ? 'ওভারভিউ / রোগ পরিচিতি' : 'Overview' }}
                    </h2>
                    <div class="prose prose-sky dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('overview', $locale) !!}
                    </div>
                </section>
                @endif

                {{-- 2. Symptoms --}}
                @if($disease->getTranslation('symptoms_summary', $locale) || $disease->symptoms->count() > 0)
                <section id="symptoms" class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80 scroll-mt-24">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm font-bold">2</span>
                        {{ $isBn ? 'লক্ষণ ও উপসর্গসমূহ (Symptoms)' : 'Symptoms & Signs' }}
                    </h2>

                    {{-- Linked Symptoms Badges --}}
                    @if($disease->symptoms->count() > 0)
                    <div class="mb-5 flex flex-wrap gap-2">
                        @foreach($disease->symptoms as $sym)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold {{ $sym->pivot->is_primary ? 'bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            @if($sym->pivot->is_primary)
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            @endif
                            {{ $sym->getTranslation('name', $locale) }}
                        </span>
                        @endforeach
                    </div>
                    @endif

                    <div class="prose prose-sky dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('symptoms_summary', $locale) !!}
                    </div>
                </section>
                @endif

                {{-- 3. Causes --}}
                @if($disease->getTranslation('causes', $locale))
                <section id="causes" class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80 scroll-mt-24">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm font-bold">3</span>
                        {{ $isBn ? 'রোগের কারণসমূহ (Causes)' : 'Causes' }}
                    </h2>
                    <div class="prose prose-sky dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('causes', $locale) !!}
                    </div>
                </section>
                @endif

                {{-- 4. Risk Factors & Complications Grid --}}
                @if($disease->getTranslation('risk_factors', $locale) || $disease->getTranslation('complications', $locale))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($disease->getTranslation('risk_factors', $locale))
                    <section class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/80">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <span class="w-2 h-5 rounded-full bg-amber-500"></span>
                            {{ $isBn ? 'ঝুঁকির কারণ (Risk Factors)' : 'Risk Factors' }}
                        </h3>
                        <div class="prose prose-amber dark:prose-invert max-w-none text-sm text-gray-700 dark:text-gray-200">
                            {!! $disease->getTranslation('risk_factors', $locale) !!}
                        </div>
                    </section>
                    @endif

                    @if($disease->getTranslation('complications', $locale))
                    <section class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/80">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <span class="w-2 h-5 rounded-full bg-red-500"></span>
                            {{ $isBn ? 'সম্ভাব্য জটিলতা (Complications)' : 'Complications' }}
                        </h3>
                        <div class="prose prose-red dark:prose-invert max-w-none text-sm text-gray-700 dark:text-gray-200">
                            {!! $disease->getTranslation('complications', $locale) !!}
                        </div>
                    </section>
                    @endif
                </div>
                @endif

                {{-- 5. Prevention --}}
                @if($disease->getTranslation('prevention', $locale))
                <section id="prevention" class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80 scroll-mt-24">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm font-bold">4</span>
                        {{ $isBn ? 'প্রতিরোধ ও সুস্থ জীবনধারা (Prevention)' : 'Prevention' }}
                    </h2>
                    <div class="prose prose-emerald dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('prevention', $locale) !!}
                    </div>
                </section>
                @endif

                {{-- 6. When to See a Doctor --}}
                @if($disease->getTranslation('when_to_see_doctor', $locale))
                <section id="when-to-see-doctor" class="bg-red-50/60 dark:bg-red-950/20 rounded-3xl p-6 sm:p-8 border border-red-200 dark:border-red-900/40 scroll-mt-24">
                    <h2 class="text-xl sm:text-2xl font-bold text-red-600 dark:text-red-400 mb-4 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 flex items-center justify-center text-sm font-bold">⚠️</span>
                        {{ $isBn ? 'কখন জরুরিভাবে ডাক্তার দেখাবেন (When to See a Doctor)' : 'When to See a Doctor' }}
                    </h2>
                    <div class="prose prose-red dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('when_to_see_doctor', $locale) !!}
                    </div>
                </section>
                @endif

                {{-- 7. Diagnosis & Treatment --}}
                @if($disease->getTranslation('diagnosis_treatment', $locale))
                <section class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm font-bold">5</span>
                        {{ $isBn ? 'রোগ নির্ণয় ও চিকিৎসা পদ্ধতি (Diagnosis & Treatment)' : 'Diagnosis & Treatment' }}
                    </h2>
                    <div class="prose prose-indigo dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('diagnosis_treatment', $locale) !!}
                    </div>
                </section>
                @endif

                {{-- 8. Authoritative References & Citations (Mayo Clinic / WHO) --}}
                @if($disease->references->count() > 0)
                <section class="bg-gray-100/70 dark:bg-gray-900/40 rounded-3xl p-6 border border-gray-200/60 dark:border-gray-800">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ $isBn ? 'তথ্যসূত্র ও রেফারেন্স (References)' : 'References & Academic Sources' }}
                    </h3>
                    <ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
                        @foreach($disease->references as $ref)
                        <li class="flex items-start gap-2">
                            <span class="text-gray-400 font-mono">[{{ $loop->iteration }}]</span>
                            <div>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $ref->source_name }}</span>
                                @if($ref->notes)
                                    <span class="text-gray-500">— {{ $ref->notes }}</span>
                                @endif
                                @if($ref->source_url)
                                    <a href="{{ $ref->source_url }}" target="_blank" rel="nofollow noopener noreferrer" class="text-sky-600 dark:text-sky-400 hover:underline ml-1 inline-flex items-center gap-0.5">
                                        <span>[Link]</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </section>
                @endif

                {{-- Medical Disclaimer --}}
                <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                    <strong class="font-bold block mb-1">📢 {{ $isBn ? 'মেডিকেল ডিসক্লেইমার / সতর্কবার্তা:' : 'Medical Disclaimer:' }}</strong>
                    {{ $isBn ? 'DoctorBD24-এর এই তথ্যসমূহ সাধারণ সচেতনতা ও স্বাস্থ্য শিক্ষার উদ্দেশ্যে আন্তর্জাতিক চিকিৎসা নির্দেশিকা অনুসরণে প্রস্তুত। এটি চিকিৎসকের সরাসরি প্রেসক্রিপশন বা বিকল্প নয়। যেকোনো অসুস্থতায় অবিলম্বে রেজিস্টার্ড চিকিৎসকের পরামর্শ নিন।' : 'This content is strictly for informational and educational purposes only. It is not intended to be a substitute for professional medical advice, diagnosis, or treatment.' }}
                </div>
            </div>

            {{-- ── Right Sidebar: Relevant Specialist Doctors (4 Cols) ── --}}
            <div id="specialist-doctors" class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

                {{-- Specialist Doctors Box --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/80">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-base flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                            {{ $isBn ? 'এই রোগের বিশেষজ্ঞ ডাক্তার' : 'Recommended Specialists' }}
                        </h3>
                    </div>

                    @if($relevantDoctors->count() > 0)
                    <div class="space-y-4">
                        @foreach($relevantDoctors as $doc)
                        <div class="p-3.5 rounded-2xl bg-gray-50/75 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700 hover:border-sky-300 transition-colors flex items-center gap-3">
                            @if($doc->photo)
                                <img src="{{ asset('storage/' . $doc->photo) }}" class="w-12 h-12 rounded-xl object-cover flex-shrink-0 border border-gray-200 dark:border-gray-600">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-sky-100 dark:bg-sky-900/60 text-sky-700 dark:text-sky-300 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    {{ mb_substr($doc->getTranslation('name', $locale), 0, 1) }}
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <a href="{{ route('doctors.show', $doc->slug) }}" class="font-bold text-sm text-gray-900 dark:text-gray-100 hover:text-sky-600 block truncate">
                                    {{ $doc->getTranslation('name', $locale) }}
                                </a>
                                @if($doc->specialties->first())
                                    <p class="text-[11px] text-sky-600 dark:text-sky-400 truncate">
                                        {{ $doc->specialties->first()->getTranslation('name', $locale) }}
                                    </p>
                                @endif
                                @php
                                    $chamber = $doc->chambers->first();
                                @endphp
                                @if($chamber && $chamber->district)
                                    <p class="text-[10px] text-gray-400 truncate">
                                        📍 {{ $chamber->district->getTranslation('name', $locale) }}
                                    </p>
                                @endif
                            </div>

                            <a href="{{ route('doctors.show', $doc->slug) }}" class="p-2 text-sky-600 hover:bg-sky-50 dark:hover:bg-gray-600 rounded-lg" title="View Doctor Profile">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                        @endforeach
                    </div>

                    @if($disease->primarySpecialty)
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('doctors.index', ['specialty' => $disease->primarySpecialty->slug]) }}" 
                           class="w-full text-center block px-4 py-2.5 rounded-xl bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 hover:bg-sky-100 text-xs font-bold transition-colors">
                            {{ $isBn ? 'সকল ' . $disease->primarySpecialty->getTranslation('name', $locale) . ' ডাক্তার দেখুন →' : 'View all ' . $disease->primarySpecialty->getTranslation('name', $locale) . ' Doctors →' }}
                        </a>
                    </div>
                    @endif

                    @else
                    <p class="text-xs text-gray-400 py-4 text-center">
                        {{ $isBn ? 'এই মুহূর্তে কোনো নির্দিষ্ট ডাক্তার তালিকায় নেই।' : 'No specialists found for this specialty yet.' }}
                    </p>
                    <a href="{{ route('doctors.index') }}" class="w-full text-center block px-4 py-2 rounded-xl bg-sky-50 text-sky-700 text-xs font-semibold">
                        {{ $isBn ? 'সকল ডাক্তার ব্রাউজ করুন' : 'Browse All Doctors' }}
                    </a>
                    @endif
                </div>

                {{-- Symptom Checker CTA Widget --}}
                <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-3xl p-6 text-white shadow-md">
                    <span class="text-[10px] uppercase tracking-wider font-extrabold bg-white/20 px-2 py-0.5 rounded-full inline-block mb-2">
                        {{ $isBn ? 'লক্ষণ পরীক্ষক' : 'Free Tool' }}
                    </span>
                    <h4 class="font-bold text-lg mb-1">
                        {{ $isBn ? 'আপনার অন্য কোনো লক্ষণ আছে?' : 'Experiencing other signs?' }}
                    </h4>
                    <p class="text-xs text-teal-100 mb-4 leading-relaxed">
                        {{ $isBn ? 'আমাদের ফ্রি সিম্পটম চেকার দিয়ে শারীরিক লক্ষণগুলো মিলিয়ে সম্ভাব্য রোগ জানুন।' : 'Assess your symptoms step by step and understand possible causes.' }}
                    </p>
                    <a href="{{ route($isBn ? 'bn.symptom-checker.index' : 'symptom-checker.index') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-teal-800 rounded-xl text-xs font-bold shadow hover:bg-teal-50 transition-colors">
                        <span>{{ $isBn ? 'সিম্পটম চেকার খুলুন' : 'Open Symptom Checker' }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                {{-- Related Conditions in Same Specialty --}}
                @if($relatedDiseases->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/80">
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm mb-3">
                        {{ $isBn ? 'সংশ্লিষ্ট অন্যান্য রোগসমূহ' : 'Related Conditions' }}
                    </h3>
                    <ul class="space-y-2 text-xs">
                        @foreach($relatedDiseases as $rel)
                        <li>
                            <a href="{{ route($isBn ? 'bn.diseases.show' : 'diseases.show', $rel->slug) }}" class="text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium flex items-center gap-2 transition-colors">
                                <span class="text-sky-500">•</span>
                                <span>{{ $rel->getTranslation('name', $locale) }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
