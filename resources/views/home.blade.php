@extends('layouts.app')

@section('title', 'DoctorBD24 — বাংলাদেশের স্বাস্থ্যসেবা ডিরেক্টরি')
@section('meta_description', 'বাংলাদেশের ডাক্তার, হাসপাতাল, অ্যাম্বুল্যান্স সার্চ করুন। সেরা স্বাস্থ্যসেবা ডিরেক্টরি।')

@section('content')

@php
    $homeTopAd = \App\Models\Advertisement::where('is_active', true)->where('position', 'homepage_top')->inRandomOrder()->first();
    $homeMidAd = \App\Models\Advertisement::where('is_active', true)->where('position', 'homepage_mid')->inRandomOrder()->first();
@endphp

{{-- ═══════════════════════════════════════
     TOP BANNER AD
═══════════════════════════════════════ --}}
@if($homeTopAd)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow relative group flex justify-center">
        <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-400 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm z-10">Advertisement</span>
        @if($homeTopAd->target_url)
            <a href="{{ $homeTopAd->target_url }}" target="_blank" class="block w-full h-full flex items-center justify-center">
                <img src="{{ asset('storage/' . $homeTopAd->image_path) }}" alt="{{ $homeTopAd->title }}" class="w-full h-full max-h-[120px] md:max-h-[150px] object-contain">
            </a>
        @else
            <img src="{{ asset('storage/' . $homeTopAd->image_path) }}" alt="{{ $homeTopAd->title }}" class="w-full h-full max-h-[120px] md:max-h-[150px] object-contain">
        @endif
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════
     HERO SECTION
═══════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-sky-600 via-sky-500 to-indigo-600 dark:from-gray-900 dark:via-sky-950 dark:to-indigo-950">
    {{-- Background pattern --}}
    <div class="absolute inset-0 opacity-10">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)"/>
        </svg>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm text-white text-sm font-medium mb-6">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            {{ __("Bangladesh's #1 Healthcare Directory") }}
        </div>

        <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4 leading-tight">
            @php
                $titleParts = explode('<br>', $heroTitle);
            @endphp
            {!! $heroTitle !!}
        </h1>
        <p class="text-sky-100 text-lg md:text-xl max-w-2xl mx-auto mb-10">
            {{ __($heroSubtitle, ['doctors' => $stats['doctors'], 'hospitals' => $stats['hospitals']]) }}
        </p>

        {{-- Search box --}}
        <form action="{{ route('doctors.index') }}" method="GET"
              class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-3 flex flex-col gap-3"
              x-data="{
                division: '',
                district: '',
                districts: [],
                areas: [],
                async fetchDistricts() {
                    this.districts = []; this.areas = []; this.district = '';
                    if (!this.division) return;
                    let res = await fetch('/api/districts?division_slug=' + this.division);
                    this.districts = await res.json();
                },
                async fetchAreas() {
                    this.areas = []; 
                    if (!this.district) return;
                    let res = await fetch('/api/areas?district_slug=' + this.district);
                    this.areas = await res.json();
                }
              }">
            {{-- Row 1: Specialty + Search --}}
            <div class="flex flex-col md:flex-row gap-2">
                <select name="specialty"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                    <option value="">🩺 {{ __("Select Specialty") }}</option>
                    @foreach($specialties as $sp)
                        <option value="{{ $sp->slug }}">{{ $sp->icon }} {{ $sp->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" placeholder="{{ __('Search doctor name...') }}"
                       class="flex-1 px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
            </div>
            {{-- Row 2: Cascading Location --}}
            <div class="flex flex-col md:flex-row gap-2">
                <select name="division" x-model="division" @change="fetchDistricts()"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                    <option value="">📍 {{ __("All Divisions") }}</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->slug }}">{{ $div->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
                <select name="district" x-model="district" @change="fetchAreas()" :disabled="!division"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300 disabled:opacity-50">
                    <option value="">🏙️ {{ __("All Districts") }}</option>
                    <template x-for="d in districts" :key="d.slug">
                        <option :value="d.slug" x-text="d.name"></option>
                    </template>
                </select>
                <select name="area" :disabled="!district"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300 disabled:opacity-50">
                    <option value="">📌 {{ __("All Areas") }}</option>
                    <template x-for="a in areas" :key="a.slug">
                        <option :value="a.slug" x-text="a.name"></option>
                    </template>
                </select>
                <button type="submit"
                        class="px-8 py-3 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-semibold hover:opacity-90 shadow-lg transition-all flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    {{ __("Search") }}
                </button>
            </div>
        </form>

    </div>

    {{-- Wave --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" class="fill-gray-50 dark:fill-gray-950 w-full">
            <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z"/>
        </svg>
    </div>
</section>

{{-- ═══════════════════════════════════════
     QUICK LINK CARDS
═══════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-10">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
        $quickLinks = [
            ['href' => route('doctors.index'),   'icon' => '👨‍⚕️', 'label' => __('Doctors'),    'color' => 'from-sky-500 to-blue-600',    'count' => $stats['doctors'] . (app()->getLocale() === 'bn' ? ' জন' : '')],
            ['href' => route('hospitals.index'),  'icon' => '🏥', 'label' => __('Hospitals'),   'color' => 'from-emerald-500 to-teal-600','count' => $stats['hospitals'] . (app()->getLocale() === 'bn' ? ' টি' : '')],
            ['href' => route('ambulances.index'), 'icon' => '🚑', 'label' => __('Ambulance'), 'color' => 'from-red-500 to-rose-600',    'count' => __('Services')],
            ['href' => route('blog.index'),       'icon' => '📰', 'label' => __('Blog'), 'color' => 'from-violet-500 to-purple-600','count' => __('Articles')],
        ];
        @endphp

        @foreach($quickLinks as $link)
        <a href="{{ $link['href'] }}"
           class="group bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $link['color'] }} flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform shadow-lg">
                {{ $link['icon'] }}
            </div>
            <div class="font-semibold text-gray-800 dark:text-gray-100 text-sm">
                {{ $link['label'] }}
            </div>
            <div class="text-xs text-gray-400 mt-0.5">{{ $link['count'] }}</div>
        </a>
        @endforeach
    </div>
</section>

{{-- ═══════════════════════════════════════
     SPECIALTIES GRID
═══════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-14">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __("Find by Specialty") }}</h2>
        <a href="{{ route('specialties.index') }}" class="text-sm text-sky-600 dark:text-sky-400 hover:underline font-medium">{{ __("All Specialties") }} &rarr;</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-3">
        @foreach($specialties->take(16) as $sp)
        <a href="{{ route('doctors.index', ['specialty' => $sp->slug]) }}"
           class="group bg-white dark:bg-gray-800 rounded-xl p-3 text-center shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700 hover:border-sky-300 dark:hover:border-sky-600 transition-all hover:-translate-y-0.5">
            <div class="text-2xl mb-1.5 group-hover:scale-110 transition-transform inline-block">{{ $sp->icon }}</div>
            <div class="text-xs font-medium text-gray-700 dark:text-gray-300 leading-snug">
                {{ $sp->getTranslation('name', app()->getLocale()) }}
            </div>
        </a>
        @endforeach
    </div>
</section>

{{-- ═══════════════════════════════════════
     MID BANNER AD
═══════════════════════════════════════ --}}
@if($homeMidAd)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-14">
    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow relative group flex justify-center">
        <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-400 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm z-10">Advertisement</span>
        @if($homeMidAd->target_url)
            <a href="{{ $homeMidAd->target_url }}" target="_blank" class="block w-full h-full flex items-center justify-center">
                <img src="{{ asset('storage/' . $homeMidAd->image_path) }}" alt="{{ $homeMidAd->title }}" class="w-full h-full max-h-[120px] md:max-h-[180px] object-contain">
            </a>
        @else
            <img src="{{ asset('storage/' . $homeMidAd->image_path) }}" alt="{{ $homeMidAd->title }}" class="w-full h-full max-h-[120px] md:max-h-[180px] object-contain">
        @endif
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════
     FEATURED DOCTORS
═══════════════════════════════════════ --}}
@if($featuredDoctors->count())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-14">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">⭐ {{ __("Featured Doctors") }}</h2>
        <a href="{{ route('doctors.index') }}" class="text-sm text-sky-600 dark:text-sky-400 hover:underline font-medium">{{ __("All Doctors") }} &rarr;</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($featuredDoctors as $doctor)
        <a href="{{ route('doctors.show', $doctor->slug) }}"
           class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-gray-100 dark:border-gray-700 transition-all duration-300 overflow-hidden hover:-translate-y-1">
            <div class="p-5 flex gap-4">
                {{-- Photo --}}
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-sky-100 to-indigo-100 dark:from-sky-900 dark:to-indigo-900 flex-shrink-0 overflow-hidden flex items-center justify-center text-4xl font-bold text-sky-400">
                    @if($doctor->photo)
                        <img src="{{ asset('storage/' . $doctor->photo) }}" alt="{{ $doctor->name }}" class="w-full h-full object-cover">
                    @else
                        {{ mb_substr($doctor->name, 0, 1) }}
                    @endif
                </div>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start gap-2">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors truncate text-sm">{{ $doctor->name }}</h3>
                        @if($doctor->verified)
                            <span title="Verified" class="flex-shrink-0 w-4 h-4 rounded-full bg-green-500 flex items-center justify-center mt-0.5">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $doctor->designation }}</p>
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach($doctor->specialties->take(2) as $sp)
                            <span class="px-2 py-0.5 rounded-full bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 text-xs font-medium">
                                {{ $sp->getTranslation('name', app()->getLocale()) }}
                            </span>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 616 0z"/></svg>
                        {{ $doctor->chambers->first()?->name ?? 'N/A' }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">{{ $doctor->experience_years }} {{ __("Years of Experience") }}</div>
                </div>
            </div>
            @if($doctor->featured)
            <div class="px-5 pb-3">
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2.5 py-1 rounded-full border border-amber-200 dark:border-amber-800">⭐ Featured</span>
            </div>
            @endif
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════
     FEATURED HOSPITALS
═══════════════════════════════════════ --}}
@if($featuredHospitals->count())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-14">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">🏥 {{ __("Featured Hospitals") }}</h2>
        <a href="{{ route('hospitals.index') }}" class="text-sm text-sky-600 dark:text-sky-400 hover:underline font-medium">{{ __("All Hospitals") }} &rarr;</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        @foreach($featuredHospitals as $hospital)
        <a href="{{ route('hospitals.show', $hospital->slug) }}"
           class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-gray-100 dark:border-gray-700 transition-all duration-300 overflow-hidden hover:-translate-y-1 flex gap-4 p-5">
            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-900 dark:to-teal-900 flex-shrink-0 flex items-center justify-center text-3xl">🏥</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors text-sm truncate">{{ $hospital->name }}</h3>
                    @if($hospital->verified)
                        <span class="flex-shrink-0 w-4 h-4 rounded-full bg-green-500 flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                    @endif
                </div>
                <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium capitalize">{{ $hospital->type }}</span>
                @if($hospital->address)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    {{ $hospital->address }}
                </p>
                @endif
                @if($hospital->phone)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    {{ $hospital->phone }}
                </p>
                @endif
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════
     RECENT BLOG
═══════════════════════════════════════ --}}
@if($recentPosts->count())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-14">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">📰 {{ __("Recent Articles") }}</h2>
        <a href="{{ route('blog.index') }}" class="text-sm text-sky-600 dark:text-sky-400 hover:underline font-medium">{{ __("All Articles") }} &rarr;</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($recentPosts as $post)
        <a href="{{ route('blog.show', $post->slug) }}"
           class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:-translate-y-1">
            @if($post->image)
                <div class="h-40 w-full overflow-hidden">
                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
            @else
                <div class="bg-gradient-to-br from-violet-100 to-purple-100 dark:from-violet-900/40 dark:to-purple-900/40 h-40 flex items-center justify-center text-5xl">📰</div>
            @endif
            <div class="p-5">
                @if($post->category)
                    <span class="text-xs font-medium text-violet-600 dark:text-violet-400">{{ $post->category->name }}</span>
                @endif
                <h3 class="font-bold text-gray-800 dark:text-gray-100 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors mt-1 line-clamp-2 text-sm">{{ $post->title }}</h3>
                @if($post->excerpt)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 line-clamp-2">{{ $post->excerpt }}</p>
                @endif
                <div class="flex items-center justify-between mt-4 text-xs text-gray-400">
                    <span>{{ $post->published_at->diffForHumans() }}</span>
                    <span class="text-sky-500 font-medium group-hover:underline">{{ __("Read More") }} &rarr;</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════
     STATS BAR
═══════════════════════════════════════ --}}
<section class="bg-gradient-to-r from-sky-600 to-indigo-600 dark:from-sky-900 dark:to-indigo-900 mt-16 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
            <div>
                <div class="text-4xl font-extrabold">{{ number_format($stats['doctors']) }}+</div>
                <div class="text-sky-200 text-sm mt-1">{{ __("Registered Doctors") }}</div>
            </div>
            <div>
                <div class="text-4xl font-extrabold">{{ number_format($stats['hospitals']) }}+</div>
                <div class="text-sky-200 text-sm mt-1">{{ __("Hospitals & Clinics") }}</div>
            </div>
            <div>
                <div class="text-4xl font-extrabold">{{ number_format($stats['specialties']) }}+</div>
                <div class="text-sky-200 text-sm mt-1">{{ __("Medical Specialties") }}</div>
            </div>
            <div>
                <div class="text-4xl font-extrabold">{{ number_format($stats['areas']) }}+</div>
                <div class="text-sky-200 text-sm mt-1">{{ __("Area Coverage") }}</div>
            </div>
        </div>
    </div>
</section>

{{-- Join CTA --}}
<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 my-16 text-center">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-10 border border-gray-100 dark:border-gray-700">
        <div class="text-4xl mb-4">🩺</div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-3">{{ __("Are you a Doctor or Hospital Authority?") }}</h2>
        <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">{{ __("List your profile or hospital today and reach thousands of patients.") }}</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('join.doctor') }}" class="px-6 py-3 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-semibold hover:opacity-90 shadow-lg transition-all">
                👨‍⚕️ {{ __("Join as Doctor") }}
            </a>
            <a href="{{ route('join.hospital') }}" class="px-6 py-3 rounded-xl border-2 border-sky-500 text-sky-600 dark:text-sky-400 font-semibold hover:bg-sky-50 dark:hover:bg-sky-900/20 transition-all">
                🏥 {{ __("List your Hospital") }}
            </a>
        </div>
    </div>
</section>

@endsection
