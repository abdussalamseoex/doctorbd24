@extends('layouts.app')

@section('schema')
@php
    $locale = app()->getLocale();
    $isBn = $locale === 'bn';
    $name = $disease->getTranslation('name', $locale);
    $altName = $disease->getTranslation('name', $isBn ? 'en' : 'bn');
    $overviewText = strip_tags($disease->getTranslation('overview', $locale));
    $faqs = $disease->getLocalizedFaqs($locale);
    $tests = $disease->getLocalizedDiagnosisTests($locale);
    $treatments = $disease->getLocalizedTreatments($locale);
    $medicines = $disease->getLocalizedMedicines($locale);
    $reviewer = $disease->reviewerDoctor;
@endphp

{{-- 1. Schema: MedicalCondition --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "MedicalCondition",
  "name": "{{ $name }}",
  "alternateName": "{{ $altName }}",
  @if(!empty($disease->aliases) && is_array($disease->aliases))
  "code": [
    @foreach($disease->aliases as $alias)
      "{{ $alias }}"{{ $loop->last ? '' : ',' }}
    @endforeach
  ],
  @endif
  "description": "{{ Str::limit(addslashes($overviewText), 250) }}",
  "signOrSymptom": [
    @foreach($disease->symptoms as $sym)
      {
        "@type": "MedicalSymptom",
        "name": "{{ $sym->getTranslation('name', $locale) }}"
      }{{ $loop->last ? '' : ',' }}
    @endforeach
  ],
  @php
    $riskItems = [];
    if (!empty($disease->getTranslation('risk_factors', $locale))) {
        preg_match_all('/<li>(.*?)<\/li>/s', $disease->getTranslation('risk_factors', $locale), $rmatches);
        $riskItems = !empty($rmatches[1]) ? array_map('strip_tags', $rmatches[1]) : [strip_tags($disease->getTranslation('risk_factors', $locale))];
    }
    $riskItems = array_values(array_filter(array_map('trim', $riskItems)));
  @endphp
  @if(count($riskItems) > 0)
  "riskFactor": [
    @foreach($riskItems as $ri)
      {
        "@type": "MedicalRiskFactor",
        "name": "{{ addslashes(trim(preg_replace('/\s+/', ' ', $ri))) }}"
      }{{ $loop->last ? '' : ',' }}
    @endforeach
  ],
  @endif
  @if(count($tests) > 0)
  "typicalTest": [
    @foreach($tests as $t)
      {
        "@type": "MedicalTest",
        "name": "{{ addslashes($t['name']) }}"
      }{{ $loop->last ? '' : ',' }}
    @endforeach
  ],
  @endif
  @php
    $compItems = [];
    if (!empty($disease->getTranslation('complications', $locale))) {
        preg_match_all('/<li>(.*?)<\/li>/s', $disease->getTranslation('complications', $locale), $cmatches);
        $compItems = !empty($cmatches[1]) ? array_map('strip_tags', $cmatches[1]) : [strip_tags($disease->getTranslation('complications', $locale))];
    }
    $compItems = array_values(array_filter(array_map('trim', $compItems)));
  @endphp
  @if(count($compItems) > 0)
  "possibleComplication": [
    @foreach($compItems as $ci)
      "{{ addslashes(trim(preg_replace('/\s+/', ' ', $ci))) }}"{{ $loop->last ? '' : ',' }}
    @endforeach
  ],
  @endif
  "possibleTreatment": [
    @if(count($treatments) > 0)
      @foreach($treatments as $t)
        {
          "@type": "MedicalTherapy",
          "name": "{{ addslashes($t['name']) }}"
        }{{ $loop->last && count($medicines) === 0 ? '' : ',' }}
      @endforeach
    @endif
    @if(count($medicines) > 0)
      @foreach($medicines as $m)
        {
          "@type": "MedicalTherapy",
          "name": "{{ addslashes($m['name']) }}"
        }{{ $loop->last ? '' : ',' }}
      @endforeach
    @elseif(count($treatments) === 0)
      {
        "@type": "MedicalTherapy",
        "name": "Clinical Diagnosis & Medical Consultation"
      }
    @endif
  ],
  "datePublished": "{{ $disease->published_at ? $disease->published_at->toIso8601String() : ($disease->created_at ? $disease->created_at->toIso8601String() : now()->toIso8601String()) }}",
  "dateModified": "{{ $disease->updated_at ? $disease->updated_at->toIso8601String() : ($disease->last_reviewed_at ? $disease->last_reviewed_at->toIso8601String() : now()->toIso8601String()) }}",
  "url": "{{ url()->current() }}"
}
</script>

{{-- 2. Schema: MedicalWebPage & Reviewer E-E-A-T --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "MedicalWebPage",
  "name": "{{ $name }} - {{ $isBn ? 'লক্ষণ, কারণ ও চিকিৎসা' : 'Symptoms, Causes & Treatment' }}",
  "url": "{{ url()->current() }}",
  "inLanguage": "{{ $locale }}",
  @if($disease->last_reviewed_at)
  "lastReviewed": "{{ $disease->last_reviewed_at->format('Y-m-d') }}",
  @endif
  @if($reviewer)
  "reviewedBy": {
    "@type": "Physician",
    "name": "{{ $reviewer->getTranslation('name', $locale) }}",
    "jobTitle": "{{ $reviewer->primarySpecialty?->getTranslation('name', $locale) ?? 'Specialist Physician' }}",
    "url": "{{ route('doctors.show', $reviewer->slug) }}"
  },
  @endif
  "publisher": {
    "@type": "Organization",
    "name": "DoctorBD24",
    "url": "{{ url('/') }}",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset('images/logo.png') }}"
    }
  }
}
</script>

{{-- 3. Schema: FAQPage (if FAQs present) --}}
@if(count($faqs) > 0)
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    @foreach($faqs as $faq)
      {
        "@type": "Question",
        "name": "{{ addslashes($faq['question']) }}",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "{{ Str::limit(addslashes(strip_tags($faq['answer'])), 300) }}"
        }
      }{{ $loop->last ? '' : ',' }}
    @endforeach
  ]
}
</script>
@endif

{{-- 4. Schema: BreadcrumbList --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "{{ $isBn ? 'হোম' : 'Home' }}",
      "item": "{{ route('home') }}"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "{{ $isBn ? 'রোগ ও স্বাস্থ্য সমস্যা' : 'Diseases & Conditions' }}",
      "item": "{{ route($isBn ? 'bn.diseases.index' : 'diseases.index') }}"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "{{ $name }}",
      "item": "{{ url()->current() }}"
    }
  ]
}
</script>
@endsection

@section('content')
@php
    $locale = app()->getLocale();
    $isBn = $locale === 'bn';
    $name = $disease->getTranslation('name', $locale);
    $altName = $disease->getTranslation('name', $isBn ? 'en' : 'bn');
    $reviewer = $disease->reviewerDoctor;
    $redFlags = $disease->getLocalizedRedFlags($locale);
    $tests = $disease->getLocalizedDiagnosisTests($locale);
    $treatments = $disease->getLocalizedTreatments($locale);
    $medicines = $disease->getLocalizedMedicines($locale);
    $procedures = $disease->getLocalizedProcedures($locale);
    $faqs = $disease->getLocalizedFaqs($locale);
    $bodyImages = $disease->getLocalizedBodyImages($locale);
    $secCount = 1;
@endphp

<div class="bg-gray-50 dark:bg-gray-950 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- ── Breadcrumb ── --}}
        <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">{{ $isBn ? 'হোম' : 'Home' }}</a>
            <span>/</span>
            <a href="{{ route($isBn ? 'bn.diseases.index' : 'diseases.index') }}" class="hover:text-emerald-600 transition-colors">{{ $isBn ? 'রোগ ও স্বাস্থ্য সমস্যা' : 'Diseases & Conditions' }}</a>
            @if($disease->bodySystem)
                <span>/</span>
                <span class="text-gray-400">{{ $disease->bodySystem->getTranslation('name', $locale) }}</span>
            @endif
            <span>/</span>
            <span class="text-gray-900 dark:text-gray-100 font-semibold truncate">{{ $name }}</span>
        </nav>

        {{-- ── Emergency Notice Banner ── --}}
        @if($disease->is_emergency)
        <div class="p-4 sm:p-5 rounded-2xl bg-red-50 dark:bg-red-950/40 border-l-4 border-red-500 text-red-800 dark:text-red-300 flex items-start gap-3.5 shadow-sm">
            <div class="w-8 h-8 rounded-xl bg-red-100 dark:bg-red-900/60 text-red-600 dark:text-red-400 flex items-center justify-center flex-shrink-0 font-bold text-base">🚨</div>
            <div>
                <h4 class="font-bold text-sm text-red-900 dark:text-red-200">
                    {{ $isBn ? 'জরুরি চিকিৎসা সতর্কতা' : 'Urgent Medical Attention Notice' }}
                </h4>
                <p class="text-xs text-red-700 dark:text-red-300 mt-1 leading-relaxed">
                    {{ $isBn ? 'এই রোগের তীব্র বা আশঙ্কাজনক লক্ষণ দেখা দিলে কোনো বিলম্ব না করে অবিলম্বে নিকটস্থ হাসপাতালের জরুরি বিভাগ (Emergency) বা বিশেষজ্ঞ চিকিৎসকের শরণাপন্ন হোন।' : 'Severe or acute symptoms of this condition require immediate emergency department care. Do not delay seeking professional medical help.' }}
                </p>
            </div>
        </div>
        @endif

        {{-- ── Main Header Hero Card ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80 space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    {{-- Taxonomy & Verification Badges --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($disease->primarySpecialty)
                            <a href="{{ route('doctors.index', ['specialty' => $disease->primarySpecialty->slug]) }}" 
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-semibold bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-100 dark:border-sky-800 hover:bg-sky-100 transition-colors">
                                <span>🩺 {{ $disease->primarySpecialty->getTranslation('name', $locale) }}</span>
                            </a>
                        @endif

                        @if($disease->bodySystem)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-medium bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-100 dark:border-purple-800">
                                <span>🧬 {{ $disease->bodySystem->getTranslation('name', $locale) }}</span>
                            </span>
                        @endif

                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-800">
                            <span>✓ {{ $isBn ? 'মেডিকেল রিভিউ সম্পন্ন' : 'Clinically Reviewed' }}</span>
                        </span>
                    </div>

                    {{-- Title H1 --}}
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">
                        {{ $name }}
                    </h1>

                    {{-- Alternate Name / Bilingual sub --}}
                    @if($name !== $altName)
                        <p class="text-base font-medium text-gray-500 dark:text-gray-400">
                            <span class="text-xs uppercase font-semibold text-gray-400 tracking-wider">{{ $isBn ? 'ইংরেজি নাম:' : 'বাংলা নাম:' }}</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ $altName }}</span>
                        </p>
                    @endif

                    {{-- Aliases / Also Known As --}}
                    @if(!empty($disease->aliases) && is_array($disease->aliases) && count($disease->aliases) > 0)
                        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 flex-wrap pt-1">
                            <span class="font-semibold">{{ $isBn ? 'অন্যান্য নাম:' : 'Also called:' }}</span>
                            @foreach($disease->aliases as $alias)
                                <span class="px-2 py-0.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-mono text-[11px]">{{ $alias }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Action CTA & Share / Print Buttons --}}
                <div class="flex flex-col sm:flex-row lg:flex-col sm:items-center lg:items-end gap-3 flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('symptom-checker.index') }}" 
                           class="px-4 py-2.5 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 text-xs font-bold hover:bg-teal-100 transition-all flex items-center gap-1.5 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $isBn ? 'লক্ষণ মিলিয়ে দেখুন' : 'Check Symptoms' }}
                        </a>

                        @if($disease->primarySpecialty)
                            <a href="#specialist-doctors" 
                               class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white text-xs font-bold shadow transition-all flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $isBn ? 'বিশেষজ্ঞ ডাক্তার' : 'Find Specialists' }}
                            </a>
                        @endif
                    </div>

                    {{-- Share & Print Utility Bar --}}
                    <div class="flex items-center gap-2 text-xs text-gray-500 pt-1" x-data="{ copied: false }">
                        <button type="button" @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-colors flex items-center gap-1" :title="copied ? 'Copied!' : 'Copy Link'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span x-text="copied ? 'Copied' : 'Share'"></span>
                        </button>
                        <a :href="`https://api.whatsapp.com/send?text=${encodeURIComponent(document.title + ' ' + window.location.href)}`" target="_blank" rel="noopener noreferrer" class="p-2 rounded-xl hover:bg-emerald-50 text-emerald-600 dark:hover:bg-emerald-950 transition-colors" title="Share on WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        </a>
                        <button type="button" onclick="window.print()" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-colors" title="Print this guide">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Clinically Reviewed E-E-A-T Card (If reviewer assigned) --}}
            @if($reviewer || $disease->last_reviewed_at)
            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-gray-600 dark:text-gray-300 bg-gray-50/60 dark:bg-gray-700/30 p-3 rounded-2xl">
                <div class="flex items-center gap-3">
                    @if($reviewer && $reviewer->photo)
                        <img src="{{ asset('storage/' . $reviewer->photo) }}" alt="{{ $reviewer->getTranslation('name', $locale) }}" class="w-9 h-9 rounded-full object-cover border border-emerald-300 shadow-sm">
                    @else
                        <div class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-sm">
                            🩺
                        </div>
                    @endif
                    <div>
                        @if($reviewer)
                            <div class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-1.5">
                                <span>{{ $isBn ? 'চিকিৎসাগত পর্যালোচনা:' : 'Clinically Reviewed by:' }}</span>
                                <a href="{{ route('doctors.show', $reviewer->slug) }}" class="text-sky-600 dark:text-sky-400 hover:underline">
                                    {{ $reviewer->getTranslation('name', $locale) }}
                                </a>
                                @if($reviewer->primarySpecialty)
                                    <span class="text-gray-400 font-normal">({{ $reviewer->primarySpecialty->getTranslation('name', $locale) }})</span>
                                @endif
                            </div>
                        @else
                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ $isBn ? 'DoctorBD24 মেডিকেল বোর্ড দ্বারা পর্যালোচিত' : 'Reviewed by DoctorBD24 Clinical Editorial Board' }}
                            </div>
                        @endif
                        <p class="text-[11px] text-gray-400">
                            {{ $isBn ? 'মেয়ো ক্লিনিক ও ডব্লিউএইচও নির্দেশিকা অনুসরণে তথ্য যাচাইকৃত' : 'Evidence-based content verified against Mayo Clinic & WHO guidelines' }}
                        </p>
                    </div>
                </div>

                @if($disease->last_reviewed_at)
                <div class="text-[11px] text-gray-500 dark:text-gray-400 font-mono flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ $isBn ? 'সর্বশেষ পর্যালোচনা:' : 'Last Reviewed:' }} {{ $disease->last_reviewed_at->format('M d, Y') }}</span>
                </div>
                @endif
            </div>
            @endif

            {{-- ── Quick Jump Horizontal Navigation Bar ── --}}
            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center gap-2 overflow-x-auto pb-1 text-xs no-scrollbar">
                <span class="font-bold text-gray-400 uppercase tracking-wider text-[11px] mr-1 flex-shrink-0">{{ $isBn ? 'সূচিপত্র:' : 'Jump to:' }}</span>
                
                @if(!empty($disease->getTranslation('overview', $locale)))
                    <a href="#overview" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'পরিচিতি' : 'Overview' }}</a>
                @endif

                @if(!empty($disease->getTranslation('symptoms_summary', $locale)) || $disease->symptoms->count() > 0)
                    <a href="#symptoms" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'লক্ষণসমূহ' : 'Symptoms' }}</a>
                @endif

                @if(count($redFlags) > 0)
                    <a href="#red-flags" class="px-3 py-1.5 rounded-xl bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 hover:bg-red-100 font-medium transition-colors whitespace-nowrap">{{ $isBn ? '🚨 বিপদচিহ্ন' : '🚨 Red Flags' }}</a>
                @endif

                @if(!empty($disease->getTranslation('causes', $locale)))
                    <a href="#causes" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'কারণ' : 'Causes' }}</a>
                @endif

                @if(!empty($disease->getTranslation('risk_factors', $locale)) || !empty($disease->getTranslation('complications', $locale)))
                    <a href="#risks-complications" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'ঝুঁকি ও জটিলতা' : 'Risks & Complications' }}</a>
                @endif

                @if(!empty($disease->getTranslation('prevention', $locale)))
                    <a href="#prevention" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'প্রতিরোধ' : 'Prevention' }}</a>
                @endif

                @if(!empty($disease->getTranslation('diagnosis_overview', $locale)) || count($tests) > 0)
                    <a href="#diagnosis" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'রোগ নির্ণয়' : 'Diagnosis' }}</a>
                @endif

                @if(!empty($disease->getTranslation('treatment_overview', $locale)) || count($treatments) > 0 || count($medicines) > 0 || !empty($disease->getTranslation('diagnosis_treatment', $locale)))
                    <a href="#treatments" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'চিকিৎসা ও ওষুধ' : 'Treatments & Drugs' }}</a>
                @endif

                @if(!empty($disease->getTranslation('lifestyle_home_remedies', $locale)))
                    <a href="#lifestyle" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'ঘরোয়া যত্ন' : 'Home Remedies' }}</a>
                @endif

                @if(!empty($disease->getTranslation('preparing_for_appointment', $locale)))
                    <a href="#prep-appointment" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-sky-50 hover:text-sky-600 dark:hover:text-sky-400 font-medium transition-colors whitespace-nowrap">{{ $isBn ? 'ডাক্তারের প্রস্তুতি' : 'Appointment Prep' }}</a>
                @endif

                @if(count($faqs) > 0)
                    <a href="#faqs" class="px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-medium hover:bg-amber-100 transition-colors whitespace-nowrap">{{ $isBn ? 'প্রশ্নোত্তর (FAQ)' : 'FAQs' }}</a>
                @endif

                <a href="#specialist-doctors" class="px-3 py-1.5 rounded-xl bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 font-medium hover:bg-sky-100 transition-colors whitespace-nowrap">{{ $isBn ? 'ডাক্তার তালিকা' : 'Doctors' }}</a>
            </div>
        </div>

        {{-- ── Main Two-Column Grid: Content (8 Cols) vs Doctors Sidebar (4 Cols) ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- ════════════════════════════════════════════════════════════════ --}}
            {{-- LEFT MAIN ARTICLE CONTENT (8 COLS) --}}
            {{-- ════════════════════════════════════════════════════════════════ --}}
            <div class="lg:col-span-8 space-y-8">

                {{-- Featured Image (If uploaded) --}}
                @if($disease->image)
                <div class="rounded-3xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-800">
                    <img src="{{ asset('storage/' . $disease->image) }}" 
                         alt="{{ $disease->getTranslation('featured_image_alt', $locale) ?: $name }}" 
                         class="w-full h-auto max-h-[420px] object-cover">
                    @if($disease->getTranslation('featured_image_caption', $locale))
                        <div class="p-3 bg-gray-50 dark:bg-gray-800/80 text-xs text-gray-500 italic text-center border-t border-gray-100 dark:border-gray-700">
                            {{ $disease->getTranslation('featured_image_caption', $locale) }}
                        </div>
                    @endif
                </div>
                @endif

                {{-- SECTION 1: OVERVIEW --}}
                @if(!empty($disease->getTranslation('overview', $locale)))
                <section id="overview" class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80 scroll-mt-24">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm font-bold">{{ $secCount++ }}</span>
                        {{ $isBn ? 'ওভারভিউ / রোগ পরিচিতি' : 'Overview' }}
                    </h2>
                    <div class="prose prose-sky dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('overview', $locale) !!}
                    </div>
                </section>
                @endif

                {{-- SECTION 2: SYMPTOMS & SIGNS --}}
                @if(!empty($disease->getTranslation('symptoms_summary', $locale)) || $disease->symptoms->count() > 0 || !empty($disease->getTranslation('symptoms_detailed', $locale)))
                <section id="symptoms" class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80 scroll-mt-24 space-y-5">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm font-bold">{{ $secCount++ }}</span>
                        {{ $isBn ? 'লক্ষণ ও উপসর্গসমূহ (Symptoms & Signs)' : 'Symptoms & Signs' }}
                    </h2>

                    {{-- Linked Symptoms Badges --}}
                    @if($disease->symptoms->count() > 0)
                    <div class="flex flex-wrap gap-2">
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

                    @if(!empty($disease->getTranslation('symptoms_summary', $locale)))
                    <div class="prose prose-sky dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('symptoms_summary', $locale) !!}
                    </div>
                    @endif

                    @if(!empty($disease->getTranslation('symptoms_detailed', $locale)))
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                        <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-2">
                            {{ $isBn ? 'ধাপভিত্তিক বিস্তারিত লক্ষণ (Stages & Progression)' : 'Stages & Progression' }}
                        </h3>
                        <div class="prose prose-sky dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                            {!! $disease->getTranslation('symptoms_detailed', $locale) !!}
                        </div>
                    </div>
                    @endif
                </section>
                @endif

                {{-- 2B. RED FLAGS WARNING ALERT BOX --}}
                @if(count($redFlags) > 0)
                <section id="red-flags" class="bg-red-50/70 dark:bg-red-950/30 rounded-3xl p-6 sm:p-8 border-2 border-red-200 dark:border-red-900/60 scroll-mt-24 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-red-100 dark:bg-red-900/80 text-red-600 dark:text-red-300 flex items-center justify-center font-bold text-lg">
                            🚨
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-red-700 dark:text-red-300">
                                {{ $isBn ? 'বিপদচিহ্ন ও জরুরি সতর্কতা (Red Flags)' : 'Emergency Warning Signs (Red Flags)' }}
                            </h2>
                            <p class="text-xs text-red-600 dark:text-red-400">
                                {{ $isBn ? 'নিম্নোক্ত লক্ষণগুলো দেখা দিলে তাৎক্ষণিক জরুরি হাসপাতালে যোগাযোগ করুন:' : 'Seek immediate medical attention if you experience any of the following:' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        @foreach($redFlags as $rf)
                        <div class="p-3.5 bg-white dark:bg-gray-800 rounded-2xl border border-red-100 dark:border-red-900/30 flex items-start gap-2.5 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-red-500 mt-1.5 flex-shrink-0"></span>
                            <span class="text-xs font-semibold text-gray-800 dark:text-gray-200 leading-snug">{{ $rf }}</span>
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- SECTION 3: CAUSES & TRIGGERS --}}
                @if(!empty($disease->getTranslation('causes', $locale)))
                <section id="causes" class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80 scroll-mt-24">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm font-bold">{{ $secCount++ }}</span>
                        {{ $isBn ? 'রোগের কারণসমূহ ও উদ্দীপক (Causes & Triggers)' : 'Causes & Triggers' }}
                    </h2>
                    <div class="prose prose-sky dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('causes', $locale) !!}
                    </div>
                </section>
                @endif

                {{-- RISK FACTORS & COMPLICATIONS --}}
                @if(!empty($disease->getTranslation('risk_factors', $locale)) || !empty($disease->getTranslation('complications', $locale)))
                <div id="risks-complications" class="grid grid-cols-1 md:grid-cols-2 gap-6 scroll-mt-24">
                    @if(!empty($disease->getTranslation('risk_factors', $locale)))
                    <section class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/80">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <span class="w-2 h-5 rounded-full bg-amber-500"></span>
                            {{ $isBn ? 'ঝুঁকির কারণ (Risk Factors)' : 'Risk Factors' }}
                        </h3>
                        <div class="prose prose-amber dark:prose-invert max-w-none text-xs sm:text-sm text-gray-700 dark:text-gray-200">
                            {!! $disease->getTranslation('risk_factors', $locale) !!}
                        </div>
                    </section>
                    @endif

                    @if(!empty($disease->getTranslation('complications', $locale)))
                    <section class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/80">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <span class="w-2 h-5 rounded-full bg-red-500"></span>
                            {{ $isBn ? 'সম্ভাব্য জটিলতা (Complications)' : 'Complications' }}
                        </h3>
                        <div class="prose prose-red dark:prose-invert max-w-none text-xs sm:text-sm text-gray-700 dark:text-gray-200">
                            {!! $disease->getTranslation('complications', $locale) !!}
                        </div>
                    </section>
                    @endif
                </div>
                @endif

                {{-- SECTION: PREVENTION --}}
                @if(!empty($disease->getTranslation('prevention', $locale)))
                <section id="prevention" class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80 scroll-mt-24">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm font-bold">{{ $secCount++ }}</span>
                        {{ $isBn ? 'প্রতিরোধ ও সতর্কতা (Prevention)' : 'Prevention' }}
                    </h2>
                    <div class="prose prose-emerald dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('prevention', $locale) !!}
                    </div>
                </section>
                @endif

                {{-- WHEN TO SEE A DOCTOR --}}
                @if(!empty($disease->getTranslation('when_to_see_doctor', $locale)))
                <section class="bg-amber-50/60 dark:bg-amber-950/20 rounded-3xl p-6 sm:p-8 border border-amber-200 dark:border-amber-900/40">
                    <h2 class="text-lg sm:text-xl font-bold text-amber-800 dark:text-amber-300 mb-3 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 flex items-center justify-center text-sm font-bold">🩺</span>
                        {{ $isBn ? 'কখন ডাক্তার দেখাবেন (When to See a Doctor)' : 'When to See a Doctor' }}
                    </h2>
                    <div class="prose prose-amber dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 leading-relaxed text-xs sm:text-sm">
                        {!! $disease->getTranslation('when_to_see_doctor', $locale) !!}
                    </div>
                </section>
                @endif

                {{-- SECTION: DIAGNOSIS & TESTS --}}
                @if(!empty($disease->getTranslation('diagnosis_overview', $locale)) || count($tests) > 0)
                <section id="diagnosis" class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80 scroll-mt-24 space-y-5">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm font-bold">{{ $secCount++ }}</span>
                        {{ $isBn ? 'রোগ নির্ণয় ও পরীক্ষাসমূহ (Diagnosis & Tests)' : 'Diagnosis & Tests' }}
                    </h2>

                    @if(!empty($disease->getTranslation('diagnosis_overview', $locale)))
                    <div class="prose prose-sky dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('diagnosis_overview', $locale) !!}
                    </div>
                    @endif

                    {{-- Diagnostic Tests Cards --}}
                    @if(count($tests) > 0)
                    <div class="pt-2 space-y-3">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider text-xs">
                            {{ $isBn ? 'প্রধান ডায়াগনস্টিক টেস্টসমূহ:' : 'Common Diagnostic Tests:' }}
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            @foreach($tests as $test)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/40 rounded-2xl border border-gray-100 dark:border-gray-700 space-y-1">
                                <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                    {{ $test['name'] }}
                                </h4>
                                @if(!empty($test['desc']))
                                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">{{ $test['desc'] }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </section>
                @endif

                {{-- SECTION: TREATMENTS, MEDICINES & PROCEDURES --}}
                @if(!empty($disease->getTranslation('treatment_overview', $locale)) || count($treatments) > 0 || count($medicines) > 0 || count($procedures) > 0 || !empty($disease->getTranslation('clinical_trials', $locale)) || !empty($disease->getTranslation('diagnosis_treatment', $locale)))
                <section id="treatments" class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80 scroll-mt-24 space-y-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm font-bold">{{ $secCount++ }}</span>
                        {{ $isBn ? 'চিকিৎসা ও ওষুধ নির্দেশিকা (Treatments & Medications)' : 'Treatments & Medications' }}
                    </h2>

                    @if(!empty($disease->getTranslation('treatment_overview', $locale)))
                    <div class="prose prose-indigo dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('treatment_overview', $locale) !!}
                    </div>
                    @elseif(!empty($disease->getTranslation('diagnosis_treatment', $locale)))
                    <div class="prose prose-indigo dark:prose-invert max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-sm sm:text-base">
                        {!! $disease->getTranslation('diagnosis_treatment', $locale) !!}
                    </div>
                    @endif

                    {{-- Treatment Strategies Cards --}}
                    @if(count($treatments) > 0)
                    <div class="space-y-3 pt-2">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider text-xs">
                            {{ $isBn ? 'চিকিৎসা পদ্ধতিসমূহ:' : 'Treatment Strategies:' }}
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            @foreach($treatments as $tr)
                            <div class="p-4 bg-indigo-50/50 dark:bg-indigo-950/20 rounded-2xl border border-indigo-100 dark:border-indigo-900/40 space-y-1.5">
                                @if(!empty($tr['category']))
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                                        {{ $tr['category'] }}
                                    </span>
                                @endif
                                <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100">{{ $tr['name'] }}</h4>
                                @if(!empty($tr['desc']))
                                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">{{ $tr['desc'] }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Medicines & Drug Classes Table / Cards --}}
                    @if(count($medicines) > 0)
                    <div class="space-y-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider text-xs flex items-center gap-1.5">
                            <span>💊 {{ $isBn ? 'ব্যবহৃত ওষুধ ও ক্যাটাগরি:' : 'Medications & Drug Classes:' }}</span>
                        </h3>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden bg-gray-50/40 dark:bg-gray-800/60">
                            @foreach($medicines as $med)
                            <div class="p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900 dark:text-gray-100 text-sm">{{ $med['name'] }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ ($med['type'] ?? '') === 'otc' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' }}">
                                            {{ ($med['type'] ?? '') === 'otc' ? 'OTC' : 'Prescription' }}
                                        </span>
                                    </div>
                                    @if(!empty($med['dosage_notes']))
                                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">{{ $med['dosage_notes'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Procedures & Therapies --}}
                    @if(count($procedures) > 0)
                    <div class="space-y-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider text-xs">
                            {{ $isBn ? 'থেরাপি ও বিশেষ পদ্ধতিসমূহ:' : 'Procedures & Advanced Therapies:' }}
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            @foreach($procedures as $proc)
                            <div class="p-4 bg-purple-50/50 dark:bg-purple-950/20 rounded-2xl border border-purple-100 dark:border-purple-900/40 space-y-1">
                                <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100">{{ $proc['name'] }}</h4>
                                @if(!empty($proc['desc']))
                                    <p class="text-xs text-gray-600 dark:text-gray-300">{{ $proc['desc'] }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Clinical Trials (If available) --}}
                    @if(!empty($disease->getTranslation('clinical_trials', $locale)))
                    <div class="p-4 rounded-2xl bg-sky-50 dark:bg-sky-950/30 border border-sky-100 dark:border-sky-800 text-xs text-sky-900 dark:text-sky-300">
                        <strong class="font-bold block mb-1">🔬 {{ $isBn ? 'ক্লিনিক্যাল ট্রায়াল ও আধুনিক গবেষণা:' : 'Clinical Trials & Emerging Therapies:' }}</strong>
                        {!! $disease->getTranslation('clinical_trials', $locale) !!}
                    </div>
                    @endif
                </section>
                @endif

                {{-- LIFESTYLE & HOME REMEDIES --}}
                @if(!empty($disease->getTranslation('lifestyle_home_remedies', $locale)) || !empty($disease->getTranslation('alternative_medicine', $locale)))
                <div id="lifestyle" class="grid grid-cols-1 md:grid-cols-2 gap-6 scroll-mt-24">
                    @if(!empty($disease->getTranslation('lifestyle_home_remedies', $locale)))
                    <section class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/80">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <span class="w-2 h-5 rounded-full bg-teal-500"></span>
                            {{ $isBn ? 'ঘরোয়া প্রতিকার ও জীবনাচরণ' : 'Lifestyle & Home Remedies' }}
                        </h3>
                        <div class="prose prose-teal dark:prose-invert max-w-none text-xs sm:text-sm text-gray-700 dark:text-gray-200">
                            {!! $disease->getTranslation('lifestyle_home_remedies', $locale) !!}
                        </div>
                    </section>
                    @endif

                    @if(!empty($disease->getTranslation('alternative_medicine', $locale)))
                    <section class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/80">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <span class="w-2 h-5 rounded-full bg-emerald-500"></span>
                            {{ $isBn ? 'পরিপূরক ও বিকল্প চিকিৎসা' : 'Alternative Medicine' }}
                        </h3>
                        <div class="prose prose-emerald dark:prose-invert max-w-none text-xs sm:text-sm text-gray-700 dark:text-gray-200">
                            {!! $disease->getTranslation('alternative_medicine', $locale) !!}
                        </div>
                    </section>
                    @endif
                </div>
                @endif

                {{-- COPING & SUPPORT + PREPARING FOR APPOINTMENT --}}
                @if(!empty($disease->getTranslation('coping_support', $locale)) || !empty($disease->getTranslation('preparing_for_appointment', $locale)))
                <div id="prep-appointment" class="grid grid-cols-1 md:grid-cols-2 gap-6 scroll-mt-24">
                    @if(!empty($disease->getTranslation('coping_support', $locale)))
                    <section class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/80">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <span class="w-2 h-5 rounded-full bg-indigo-500"></span>
                            {{ $isBn ? 'মানসিক সহায়তা ও কেয়ার' : 'Coping & Support' }}
                        </h3>
                        <div class="prose prose-indigo dark:prose-invert max-w-none text-xs sm:text-sm text-gray-700 dark:text-gray-200">
                            {!! $disease->getTranslation('coping_support', $locale) !!}
                        </div>
                    </section>
                    @endif

                    @if(!empty($disease->getTranslation('preparing_for_appointment', $locale)))
                    <section class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/80">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <span class="w-2 h-5 rounded-full bg-sky-500"></span>
                            {{ $isBn ? 'ডাক্তারের কাছে যাওয়ার প্রস্তুতি' : 'Preparing for Appointment' }}
                        </h3>
                        <div class="prose prose-sky dark:prose-invert max-w-none text-xs sm:text-sm text-gray-700 dark:text-gray-200">
                            {!! $disease->getTranslation('preparing_for_appointment', $locale) !!}
                        </div>
                    </section>
                    @endif
                </div>
                @endif

                {{-- FREQUENTLY ASKED QUESTIONS (ACCORDION) --}}
                @if(count($faqs) > 0)
                <section id="faqs" class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700/80 scroll-mt-24 space-y-4" x-data="{ openFaq: 0 }">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm font-bold">?</span>
                        {{ $isBn ? 'সাধারণ জিজ্ঞাসা ও প্রশ্নোত্তর (FAQs)' : 'Frequently Asked Questions (FAQs)' }}
                    </h2>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
                        @foreach($faqs as $fIndex => $faq)
                        <div class="bg-white dark:bg-gray-800">
                            <button type="button" @click="openFaq = (openFaq === {{ $fIndex }} ? null : {{ $fIndex }})" class="w-full p-4 text-left flex items-center justify-between gap-4 font-semibold text-sm text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <span>{{ $faq['question'] }}</span>
                                <span class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center justify-center text-xs flex-shrink-0" x-text="openFaq === {{ $fIndex }} ? '−' : '+'"></span>
                            </button>
                            <div x-show="openFaq === {{ $fIndex }}" class="px-4 pb-4 text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed" style="display: none;">
                                {!! nl2br(e($faq['answer'])) !!}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- AUTHORITATIVE REFERENCES & CITATIONS --}}
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

                {{-- MEDICAL DISCLAIMER --}}
                <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                    <strong class="font-bold block mb-1">📢 {{ $isBn ? 'মেডিকেল ডিসক্লেইমার / সতর্কবার্তা:' : 'Medical Disclaimer:' }}</strong>
                    @if(!empty($disease->getTranslation('disclaimer', $locale)))
                        {!! $disease->getTranslation('disclaimer', $locale) !!}
                    @else
                        {{ $isBn ? 'DoctorBD24-এর এই তথ্যসমূহ সাধারণ সচেতনতা ও স্বাস্থ্য শিক্ষার উদ্দেশ্যে আন্তর্জাতিক মেয়ো ক্লিনিক ও ডব্লিউএইচও নির্দেশিকা অনুসরণে প্রস্তুত। এটি চিকিৎসকের সরাসরি প্রেসক্রিপশন বা বিকল্প নয়। যেকোনো স্বাস্থ্য সমস্যায় সর্বদা বিএমডিসি নিবন্ধিত চিকিৎসকের পরামর্শ নিন।' : 'This content is strictly for informational and educational purposes only based on international medical guidelines. It is not intended to substitute professional medical advice, diagnosis, or treatment. Always seek the advice of your qualified physician.' }}
                    @endif
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════════════ --}}
            {{-- RIGHT SIDEBAR: DOCTORS, REVIEWER & RELATED ENTITIES (4 COLS) --}}
            {{-- ════════════════════════════════════════════════════════════════ --}}
            <div id="specialist-doctors" class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

                {{-- Featured Reviewer Doctor Card (If reviewer assigned) --}}
                @if($reviewer)
                <div class="bg-gradient-to-br from-emerald-500 to-teal-700 text-white rounded-3xl p-6 shadow-md space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-full bg-white/20 text-[10px] font-bold uppercase tracking-wider backdrop-blur-sm">
                            ✓ {{ $isBn ? 'মেডিকেল রিভিউয়ার' : 'Medical Reviewer' }}
                        </span>
                        @if($reviewer->verified)
                            <span class="text-xs bg-white text-emerald-800 font-bold px-2 py-0.5 rounded-full">BMDC Verified</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3.5">
                        @if($reviewer->photo)
                            <img src="{{ asset('storage/' . $reviewer->photo) }}" alt="{{ $reviewer->getTranslation('name', $locale) }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-white/60 shadow">
                        @else
                            <div class="w-16 h-16 rounded-2xl bg-white/20 text-white flex items-center justify-center font-bold text-2xl border-2 border-white/40">
                                🩺
                            </div>
                        @endif
                        <div>
                            <h3 class="font-bold text-base text-white leading-tight">
                                {{ $reviewer->getTranslation('name', $locale) }}
                            </h3>
                            <p class="text-xs text-emerald-100 mt-0.5">
                                {{ $reviewer->getTranslation('designation', $locale) ?: ($reviewer->primarySpecialty?->getTranslation('name', $locale) ?? 'Specialist') }}
                            </p>
                            @if($reviewer->degrees)
                                <p class="text-[11px] text-emerald-200 mt-0.5 font-mono truncate max-w-[180px]">{{ $reviewer->degrees }}</p>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('doctors.show', $reviewer->slug) }}" class="block w-full text-center py-2.5 rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 text-xs font-bold transition-all shadow-sm">
                        {{ $isBn ? 'ডাক্তারের প্রোফাইল ও সিরিয়াল' : 'View Profile & Book' }}
                    </a>
                </div>
                @endif

                {{-- Relevant Specialist Doctors Directory --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/80 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <span>👨‍⚕️ {{ $isBn ? 'বিশেষজ্ঞ ডাক্তার' : 'Specialist Doctors' }}</span>
                        </h3>
                        @if($disease->primarySpecialty)
                        <a href="{{ route('doctors.index', ['specialty' => $disease->primarySpecialty->slug]) }}" class="text-xs font-semibold text-sky-600 dark:text-sky-400 hover:underline">
                            {{ $isBn ? 'সব দেখুন' : 'View all' }} →
                        </a>
                        @endif
                    </div>

                    <p class="text-xs text-gray-400">
                        {{ $isBn ? 'এই রোগের পরামর্শ ও চিকিৎসার জন্য নিবন্ধিত বিশেষজ্ঞ ডাক্তার:' : 'Registered specialists available for consultation:' }}
                    </p>

                    <div class="space-y-3.5">
                        @forelse($relevantDoctors as $doc)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-2xl border border-gray-100 dark:border-gray-700 flex items-center gap-3 hover:bg-sky-50/50 transition-colors">
                            @if($doc->photo)
                                <img src="{{ asset('storage/' . $doc->photo) }}" alt="{{ $doc->getTranslation('name', $locale) }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 dark:border-gray-600 flex-shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-300 flex items-center justify-center font-bold text-base flex-shrink-0">
                                    {{ strtoupper(substr($doc->slug, 0, 1)) }}
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <a href="{{ route('doctors.show', $doc->slug) }}" class="font-bold text-xs text-gray-900 dark:text-gray-100 hover:text-sky-600 dark:hover:text-sky-400 truncate block">
                                    {{ $doc->getTranslation('name', $locale) }}
                                </a>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                    {{ $doc->primarySpecialty?->getTranslation('name', $locale) ?? 'Specialist' }}
                                </p>
                                @if($doc->chambers->count() > 0)
                                    <p class="text-[10px] text-gray-400 truncate">
                                        📍 {{ $doc->chambers->first()->chamber_name }}
                                    </p>
                                @endif
                            </div>

                            <a href="{{ route('doctors.show', $doc->slug) }}" class="px-2.5 py-1.5 rounded-lg bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-300 hover:bg-sky-100 text-[11px] font-semibold flex-shrink-0">
                                {{ $isBn ? 'সিরিয়াল' : 'Book' }}
                            </a>
                        </div>
                        @empty
                        <p class="text-xs text-gray-400 italic py-2">
                            {{ $isBn ? 'এই মুহূর্তে সরাসরি ডাক্তার তালিকাভুক্ত নেই। সাধারণ মেডিসিন বিশেষজ্ঞের পরামর্শ নিন।' : 'No direct specialists listed yet. Please consult a General Physician.' }}
                        </p>
                        @endforelse
                    </div>

                    @if($disease->primarySpecialty)
                    <a href="{{ route('doctors.index', ['specialty' => $disease->primarySpecialty->slug]) }}" 
                       class="block text-center w-full py-2.5 rounded-xl border border-sky-200 dark:border-sky-800 text-sky-600 dark:text-sky-400 hover:bg-sky-50 dark:hover:bg-sky-950 text-xs font-bold transition-all">
                        {{ $isBn ? 'সকল ' . $disease->primarySpecialty->getTranslation('name', $locale) . ' ডাক্তার দেখুন' : 'View All ' . $disease->primarySpecialty->getTranslation('name', $locale) . ' Doctors' }} →
                    </a>
                    @endif
                </div>

                {{-- Interactive Symptom Checker Card --}}
                <div class="bg-gradient-to-br from-teal-500 to-emerald-600 rounded-3xl p-6 text-white shadow-sm space-y-3">
                    <span class="px-2 py-0.5 rounded bg-white/20 text-[10px] font-bold uppercase tracking-wider backdrop-blur-sm">
                        {{ $isBn ? 'ইন্টারেক্টিভ টুল' : 'AI Health Tool' }}
                    </span>
                    <h3 class="font-extrabold text-base leading-tight">
                        {{ $isBn ? 'আপনার শারীরিক লক্ষণ কি মিলে যাচ্ছে?' : 'Experiencing Similar Symptoms?' }}
                    </h3>
                    <p class="text-xs text-teal-50 leading-relaxed">
                        {{ $isBn ? 'আমাদের ফ্রি সিম্পটম চেকার টুল ব্যবহার করে শরীরের লক্ষণ মিলিয়ে সম্ভাব্য কারণ জেনে নিন।' : 'Use our free interactive symptom checker to find potential conditions and recommended specialists.' }}
                    </p>
                    <a href="{{ route('symptom-checker.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white text-teal-800 hover:bg-teal-50 text-xs font-bold transition-all shadow-sm">
                        <span>{{ $isBn ? 'সিম্পটম চেকার শুরু করুন' : 'Start Symptom Checker' }}</span>
                        <span>→</span>
                    </a>
                </div>

                {{-- Related Diseases & Conditions --}}
                @if(isset($relatedDiseases) && $relatedDiseases->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700/80 space-y-3">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">
                        {{ $isBn ? 'সম্পর্কিত অন্যান্য রোগ ও সমস্যা' : 'Related Conditions' }}
                    </h3>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($relatedDiseases as $rel)
                        <a href="{{ route($isBn ? 'bn.diseases.show' : 'diseases.show', $rel->slug) }}" class="py-2.5 flex items-center justify-between text-xs text-gray-700 dark:text-gray-300 hover:text-sky-600 dark:hover:text-sky-400 group">
                            <span class="group-hover:translate-x-1 transition-transform truncate">{{ $rel->getTranslation('name', $locale) }}</span>
                            <span class="text-gray-400 text-xs">→</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
