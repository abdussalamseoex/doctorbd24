@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-12">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 mb-6 w-full flex-wrap text-xs text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-sky-500">{{ __('Home') }}</a>
        <span>›</span>
        <a href="{{ route('hospitals.index') }}" class="hover:text-sky-500">{{ __('Hospitals') }}</a>
        <span>›</span>
        <a href="{{ route('hospitals.show', $hospital->slug) }}" class="hover:text-sky-500">{{ $hospital->name }}</a>
        <span>›</span>
        <a href="{{ route('hospitals.show', ['slug' => $hospital->slug, 'tab' => 'diagnostics']) }}" class="hover:text-sky-500">{{ __('Diagnostics') }}</a>
        <span>›</span>
        <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $service->service_name }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ── LEFT COLUMN ── --}}
        <div class="w-full lg:w-3/4 space-y-6">

            {{-- Header Card (Mini Version of Hospital Header) --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative mb-2 p-6 flex items-center gap-4">
                <a href="{{ route('hospitals.show', $hospital->slug) }}" class="flex-shrink-0">
                    <div class="w-16 h-16 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-1 flex items-center justify-center overflow-hidden">
                        @if($hospital->logo)
                            <img loading="lazy" src="{{ asset('storage/'.$hospital->logo) }}" alt="{{ $hospital->name }}" class="max-w-full max-h-full object-contain">
                        @else
                            <span class="text-2xl">🏥</span>
                        @endif
                    </div>
                </a>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white leading-tight mb-1">
                        <a href="{{ route('hospitals.show', $hospital->slug) }}" class="hover:text-sky-500 transition-colors">{{ $hospital->name }}</a>
                    </h2>
                    <p class="text-xs text-gray-500 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $hospital->area?->getTranslation('name', app()->getLocale()) ?? 'Bangladesh' }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('hospitals.show', ['slug' => $hospital->slug, 'tab' => 'diagnostics']) }}" class="hidden sm:flex text-xs font-semibold px-4 py-2 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg transition-colors border border-gray-200 dark:border-gray-600">
                        &larr; Back to All Tests
                    </a>
                </div>
            </div>

            {{-- Main Test Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl border border-gray-100 dark:border-gray-700 p-8 sm:p-10 relative overflow-hidden">
                {{-- Decorative Background --}}
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-sky-50 dark:bg-sky-900/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10">
                    {{-- Caregory Badge --}}
                    @if($service->service_category)
                        <span class="inline-block px-3 py-1 mb-4 text-[10px] font-black uppercase tracking-wider text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-900/30 rounded-full border border-sky-100 dark:border-sky-800/50">
                            {{ $service->service_category }}
                        </span>
                    @endif

                    <h1 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white leading-tight mb-6">
                        {{ $service->service_name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-4 mb-8">
                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border border-emerald-100 dark:border-emerald-800/50 rounded-2xl p-4 flex-1 sm:flex-none">
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Cost / Price') }}</span>
                            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                                {{ $service->price ? '৳ ' . $service->price : __('Contact for Price') }}
                            </span>
                        </div>
                        
                        @if($hospital->phone)
                        <a href="tel:{{ $hospital->phone }}" class="group bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600 rounded-2xl p-4 flex-1 sm:flex-none flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">{{ __('Call for Bookings') }}</span>
                                <span class="text-base font-bold text-gray-800 dark:text-gray-200">{{ $hospital->phone }}</span>
                            </div>
                        </a>
                        @endif
                    </div>

                    {{-- Description --}}
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">{{ __('Test Details & Description') }}</h3>
                        <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-loose">
                            @if($service->description)
                                {!! nl2br(e($service->description)) !!}
                            @else
                                <p class="italic text-gray-400">{{ __('No descriptive details have been provided for this test yet. Contact the hospital directly for preparation instructions and reporting times.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Other Tests --}}
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4">{{ __('Other Tests at') }} {{ $hospital->name }}</h3>
                <div class="flex flex-wrap gap-2">
                    @php
                        $otherTests = $hospital->hospitalServices()->where('id', '!=', $service->id)->whereNotNull('slug')->inRandomOrder()->limit(10)->get();
                    @endphp
                    @foreach($otherTests as $ot)
                        <a href="{{ route('hospitals.diagnostic.show', [$hospital->slug, $ot->slug]) }}" class="px-3 py-1.5 flex items-center gap-1.5 text-xs font-semibold bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:border-sky-300 dark:hover:border-sky-700 hover:text-sky-600 dark:hover:text-sky-400 transition-colors">
                            <span class="text-gray-400">🔬</span> {{ $ot->service_name }}
                        </a>
                    @endforeach
                    <a href="{{ route('hospitals.show', ['slug' => $hospital->slug, 'tab' => 'diagnostics']) }}" class="px-3 py-1.5 flex items-center gap-1.5 text-xs font-bold text-sky-600 hover:underline">
                        {{ __('View All') }} &rarr;
                    </a>
                </div>
            </div>

        </div> {{-- END LEFT COLUMN --}}

        {{-- ── RIGHT COLUMN: SIDEBAR ── --}}
        <div class="w-full lg:w-1/4 flex-shrink-0">
            <div class="sticky top-24 space-y-5">
                {{-- Ad Container --}}
                <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 flex flex-col items-center justify-center text-center h-[250px] shadow-sm relative overflow-hidden group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-400 bg-white dark:bg-gray-900 px-2 py-0.5 rounded shadow-sm">Ad</span>
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8"/></svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ __('Sponsor Space Available') }}</p>
                </div>
                
                {{-- Map Minified --}}
                @if($hospital->address || ($hospital->lat && $hospital->lng))
                @php
                    $mapQuery = urlencode($hospital->name . ($hospital->address ? ', ' . $hospital->address : ''));
                    $mapsLink = $hospital->google_maps_url ?: "https://www.google.com/maps/search/?api=1&query=" . $mapQuery;
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden group">
                    <a href="{{ $mapsLink }}" target="_blank" class="block relative h-40 overflow-hidden">
                        <iframe src="https://www.google.com/maps/embed/v1/place?key={{ \App\Models\Setting::get('google_maps_api_key', env('GOOGLE_MAPS_API_KEY')) }}&q={{ $mapQuery }}"
                            class="w-full h-full border-0 pointer-events-none" loading="lazy"></iframe>
                        <div class="absolute inset-0 bg-transparent flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/10 backdrop-blur-[1px]">
                            <span class="px-3 py-1.5 bg-white text-gray-800 text-xs font-bold rounded shadow-lg">Open Map</span>
                        </div>
                    </a>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
