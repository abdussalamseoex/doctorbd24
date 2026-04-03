@extends('layouts.app')
@section('title', $hospital->name . ' — Hospital | DoctorBD24')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-12">

    {{-- Breadcrumb --}}
    <nav class="flex items-center justify-between gap-2 mb-6 w-full">
        <div class="flex items-center gap-2 text-xs text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-sky-500">{{ __('Home') }}</a>
            <span>›</span>
            <a href="{{ route('hospitals.index') }}" class="hover:text-sky-500">{{ __('Hospitals') }}</a>
            <span>›</span>
            <span class="text-gray-600 dark:text-gray-300">{{ $hospital->name }}</span>
        </div>
        <div class="text-[10px] text-gray-400 font-medium hidden sm:block" title="Profile Last Updated">
            {{ __('Updated:') }} {{ $hospital->updated_at->format('d M, Y') }}
        </div>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ── LEFT COLUMN ── --}}
        <div class="w-full lg:w-3/4 space-y-6">

            {{-- Header Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden relative mb-6">
                {{-- Banner Area --}}
                <div class="h-48 sm:h-64 bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-700 relative overflow-hidden">
                    @if($hospital->banner)
                        <img src="{{ asset('storage/'.$hospital->banner) }}" alt="{{ $hospital->name }} Banner" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
                    @else
                        <div class="absolute inset-0 opacity-20">
                            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 100L100 0V100H0Z" fill="white"/></svg>
                        </div>
                    @endif

                    {{-- Share & Save Buttons On Banner --}}
                    <div class="absolute top-4 right-4 sm:top-6 sm:right-6 flex items-center gap-2 sm:gap-3 z-30">
                        @auth
                        <div x-data="{ saved: false }" x-init="
                             fetch('{{ route('favorites.check') }}?type=hospital&id={{ $hospital->id }}')
                             .then(r => r.json()).then(d => saved = d.saved)
                        ">
                            <button @click="
                                fetch('{{ route('favorites.toggle') }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({ type: 'hospital', id: {{ $hospital->id }} })
                                }).then(r => r.json()).then(d => saved = d.saved)
                            "
                            class="group w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-md border border-gray-100 dark:border-gray-700 hover:scale-105"
                            :title="saved ? '{{ __('Remove from Favorites') }}' : '{{ __('Add to Favorites') }}'">
                                <svg x-cloak x-show="!saved" class="w-5 h-5 sm:w-6 sm:h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                <svg x-cloak x-show="saved" class="w-5 h-5 sm:w-6 sm:h-6 text-red-500 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </button>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-md border border-gray-100 dark:border-gray-700 hover:scale-105" title="{{ __('Login to Save') }}">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </a>
                        @endauth

                        {{-- Social Share --}}
                        <div class="flex items-center gap-2 sm:gap-3">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"
                               class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-white dark:bg-gray-800 flex items-center justify-center text-[#1877F2] hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-md border border-gray-100 dark:border-gray-700 group hover:scale-105" title="Share on Facebook">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 512 512"><path d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.8 90.7 226.4 209.3 245V327.7h-63V256h63v-54.6c0-62.2 37-96.5 93.7-96.5 27.1 0 55.5 4.8 55.5 4.8v61h-31.3c-30.8 0-40.4 19.1-40.4 38.7V256h68.8l-11 71.7h-57.8V501C413.3 482.4 504 379.8 504 256z"/></svg>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($hospital->name . ' — ' . url()->current()) }}" target="_blank"
                               class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-white dark:bg-gray-800 flex items-center justify-center text-[#25D366] hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-md border border-gray-100 dark:border-gray-700 group hover:scale-105" title="Share on WhatsApp">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Profile Content Area --}}
                <div class="px-6 sm:px-8 pb-8 flex flex-col sm:flex-row gap-6 sm:gap-8">
                    
                    {{-- Logo --}}
                    <div class="flex-shrink-0 flex justify-center sm:justify-start -mt-16 sm:-mt-20 z-20 relative">
                        <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-[2rem] bg-white dark:bg-gray-800 shadow-xl p-2 sm:p-3 border-4 sm:border-8 border-white dark:border-gray-800 overflow-hidden group">
                            @if($hospital->logo)
                                <img src="{{ asset('storage/'.$hospital->logo) }}" alt="{{ $hospital->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-6xl bg-gray-50 dark:bg-gray-900 rounded-2xl">🏥</div>
                            @endif
                        </div>
                    </div>

                    {{-- Main Info --}}
                    <div class="flex-1 flex flex-col justify-center items-center sm:items-start gap-4 pt-2 sm:pt-4">
                        
                        <div class="text-center sm:text-left w-full">
                            <div class="flex flex-col sm:flex-row sm:flex-wrap items-center sm:items-start gap-2 sm:gap-3 mb-4">
                                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">{{ $hospital->name }}</h1>
                                <div class="flex items-center gap-2 mt-1 sm:mt-0">
                                    @if($hospital->verified)
                                        <div class="flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            {{ __('Verified') }}
                                        </div>
                                    @endif
                                    <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold uppercase tracking-wider border border-slate-200 dark:border-slate-600">{{ __($hospital->type) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- END Header Card --}}

            {{-- Contact Information & Rating --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 mt-4">
                {{-- Contact Info Box --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6 flex flex-col h-full hover:shadow-lg transition-shadow">
                    <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-5 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center text-teal-600 border border-teal-100 dark:border-teal-800/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        {{ __('Contact Information') }}
                    </h2>
                    
                    <div class="space-y-4 flex-1">
                        @if($hospital->address)
                        <div class="flex items-start gap-4 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition-colors">
                            <div class="mt-0.5 shrink-0 text-orange-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">{{ __('Address') }}</p>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 leading-relaxed">{{ $hospital->address }}</p>
                            </div>
                        </div>
                        @endif

                        @if($hospital->phone)
                        <a href="tel:{{ $hospital->phone }}" class="group flex items-start gap-4 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-transparent hover:border-emerald-200 dark:hover:border-emerald-800 transition-all cursor-pointer">
                            <div class="mt-0.5 shrink-0 text-emerald-500 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5 group-hover:text-emerald-500 transition-colors">{{ __('Call Phone') }}</p>
                                <p class="text-[15px] font-bold text-gray-800 dark:text-gray-200 tracking-wide truncate group-hover:text-emerald-600 transition-colors">{{ $hospital->phone }}</p>
                            </div>
                        </a>
                        @endif

                        @if($hospital->facebook_url || $hospital->instagram_url || $hospital->youtube_url || $hospital->website)
                        <div class="flex items-start gap-4 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-transparent hover:border-blue-200 dark:hover:border-blue-800 transition-colors pt-3">
                            <div class="mt-0.5 shrink-0 text-blue-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2.5">{{ __('Official Links') }}</p>
                                <div class="flex flex-wrap items-center gap-3">
                                    @if($hospital->facebook_url)
                                        <a href="{{ $hospital->facebook_url }}" target="_blank" class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 flex items-center justify-center text-[#1877F2] hover:bg-[#1877F2] hover:text-white hover:border-transparent transition-all shadow-sm hover:shadow hover:scale-105" title="Facebook">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.8 90.7 226.4 209.3 245V327.7h-63V256h63v-54.6c0-62.2 37-96.5 93.7-96.5 27.1 0 55.5 4.8 55.5 4.8v61h-31.3c-30.8 0-40.4 19.1-40.4 38.7V256h68.8l-11 71.7h-57.8V501C413.3 482.4 504 379.8 504 256z"/></svg>
                                        </a>
                                    @endif
                                    @if($hospital->instagram_url)
                                        <a href="{{ $hospital->instagram_url }}" target="_blank" class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 flex items-center justify-center text-pink-600 hover:bg-gradient-to-tr hover:from-[#f09433] hover:via-[#e6683c] hover:to-[#bc1888] hover:text-white hover:border-transparent transition-all shadow-sm hover:shadow hover:scale-105" title="Instagram">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
                                        </a>
                                    @endif
                                    @if($hospital->youtube_url)
                                        <a href="{{ $hospital->youtube_url }}" target="_blank" class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 flex items-center justify-center text-red-600 hover:bg-[#FF0000] hover:text-white hover:border-transparent transition-all shadow-sm hover:shadow hover:scale-105" title="YouTube">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 576 512"><path d="M549.7 124.1c-6.3-23.7-24.8-42.3-48.3-48.6C458.8 64 288 64 288 64S117.2 64 74.6 75.5c-23.5 6.3-42 24.9-48.3 48.6-11.4 42.9-11.4 132.3-11.4 132.3s0 89.4 11.4 132.3c6.3 23.7 24.8 41.5 48.3 47.8C117.2 448 288 448 288 448s170.8 0 213.4-11.5c23.5-6.3 42-24.2 48.3-47.8 11.4-42.9 11.4-132.3 11.4-132.3s0-89.4-11.4-132.3zm-317.5 213.5V175.2l142.7 81.2-142.7 81.2z"/></svg>
                                        </a>
                                    @endif
                                    @if($hospital->website)
                                        <a href="{{ $hospital->website }}" target="_blank" class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 flex items-center justify-center text-teal-600 hover:bg-[#1A6263] hover:text-white hover:border-transparent transition-all shadow-sm hover:shadow hover:scale-105" title="Website">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512"><path d="M352 256c0 22.2-1.2 43.6-3.3 64H163.3c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64h185.4c2.1 20.4 3.3 41.8 3.3 64zm28.8-64h123.1c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64H380.8c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32H376.7c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0H167.7c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26.1 20.9 58.3 27 94.7zm-209 0H18.6C48.6 85.9 112.2 29.1 190.6 8.4C165.1 42.6 145.3 96.1 135.3 160zM8.1 192H131.2c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64H8.1C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM194.7 446.6c-11.6-26.1-20.9-58.3-27-94.7h176.6c-6.1 36.4-15.5 68.6-27 94.7c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512s-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5zM135.3 352c10 63.9 29.8 117.4 55.3 151.6C112.2 482.9 48.6 426.1 18.6 352H135.3zm358.1 0c-30 74.1-93.6 130.9-171.9 151.6c25.5-34.2 45.2-87.7 55.3-151.6h116.6z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Rating & Reviews Box --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6 flex flex-col h-full hover:shadow-lg transition-shadow">
                    <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-500 border border-amber-100 dark:border-amber-800/50">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </span>
                            {{ __('Patient Ratings') }}
                        </div>
                        @if($hospital->approvedReviews->count())
                        <div class="text-[10px] font-bold uppercase tracking-wider text-amber-600 bg-amber-50 dark:bg-gray-700 dark:text-amber-400 px-3 py-1 rounded-full">{{ $hospital->approvedReviews->count() }} {{ __('Reviews') }}</div>
                        @endif
                    </h2>
                    
                    <div class="flex-1 flex flex-col items-center justify-center text-center py-2 px-6 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700 mb-5">
                        @if($hospital->approvedReviews->count())
                        <div class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-br from-amber-400 to-orange-500 drop-shadow-sm mb-2 mt-2">{{ $hospital->average_rating }}</div>
                        <div class="flex gap-1 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-6 h-6 {{ $i <= round($hospital->average_rating) ? 'text-amber-400 drop-shadow-sm' : 'text-gray-200 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        @else
                        <div class="text-sm text-gray-500 dark:text-gray-400 font-medium py-6">{{ __('No ratings currently available.') }}</div>
                        @endif
                    </div>

                    {{-- Dynamic Review Form --}}
                    @php
                        $canReview = false;
                        if(auth()->check() && !auth()->user()->hasAnyRole(['admin', 'doctor', 'hospital', 'editor', 'moderator'])) {
                            $canReview = true;
                        }
                    @endphp
                    @if(!auth()->check() || $canReview)
                    <form action="{{ auth()->check() ? route('reviews.store') : route('login') }}" method="{{ auth()->check() ? 'POST' : 'GET' }}" class="space-y-3">
                        @auth
                            @csrf
                        @endauth
                        <input type="hidden" name="type" value="hospital">
                        <input type="hidden" name="id" value="{{ $hospital->id }}">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <select name="rating" class="col-span-1 px-3 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 font-medium shadow-sm transition-all" required>
                                <option value="">{{ __('Rate') }}</option>
                                @for($i=5; $i>=1; $i--)<option value="{{ $i }}">{{ str_repeat('⭐', $i) }} {{ $i }}/5</option>@endfor
                            </select>
                            <input name="comment" type="text" placeholder="{{ __('Add a quick comment...') }}" class="col-span-1 sm:col-span-2 px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 shadow-sm transition-all placeholder:text-gray-400" required>
                        </div>
                        
                        <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-amber-400 to-orange-500 hover:from-amber-500 hover:to-orange-600 text-white text-[13px] font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                            {{ auth()->check() ? __('Submit Rating') : __('Login to Rate') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- About --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-3">{{ __('About Us') }}</h2>
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed">
                    @if(empty($hospital->about))
                        {{ __('No description available') }}
                    @elseif(strip_tags($hospital->about) === $hospital->about)
                        {!! nl2br(e($hospital->about)) !!}
                    @else
                        {!! $hospital->about !!}
                    @endif
                </div>
            </div>

            {{-- Opening Hours --}}
            @if(!empty($hospital->opening_hours))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600">⏰</span>
                    {{ __('Opening Hours') }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($hospital->opening_hours as $day => $hours)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ __($day) }}</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">{{ $hours }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Photo Gallery --}}
            @if(!empty($hospital->gallery))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-fuchsia-100 dark:bg-fuchsia-900/30 flex items-center justify-center text-fuchsia-600">🖼️</span>
                    {{ __('Photo Gallery') }}
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach($hospital->gallery as $image)
                        <a href="{{ asset('storage/' . $image) }}" target="_blank" class="block aspect-square rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group/gallery relative">
                            <img src="{{ asset('storage/' . $image) }}" alt="Gallery Image" class="w-full h-full object-cover transition-transform duration-500 group-hover/gallery:scale-110">
                            <div class="absolute inset-0 bg-black/0 group-hover/gallery:bg-black/20 transition-colors flex items-center justify-center">
                                <svg class="w-8 h-8 text-white opacity-0 group-hover/gallery:opacity-100 transition-opacity drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Doctors at this hospital --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-gray-800 dark:text-gray-100">{{ __('Doctors at this hospital') }} ({{ $doctors->count() }})</h2>
                    <form method="GET" class="flex items-center gap-2">
                        <select name="specialty" onchange="this.form.submit()" class="text-xs px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none">
                            <option value="">{{ __('All Specialties') }}</option>
                            @foreach($specialties as $sp)
                                <option value="{{ $sp->slug }}" @selected(request('specialty')===$sp->slug)>{{ $sp->getTranslation('name', app()->getLocale()) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @if($doctors->count())
                <div class="space-y-3">
                    @foreach($doctors as $doc)
                    <a href="{{ route('doctors.show', $doc->slug) }}" class="group flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-sky-300 hover:bg-sky-50 dark:hover:bg-sky-900/10 transition-all">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-100 to-indigo-100 dark:from-sky-900 dark:to-indigo-900 flex-shrink-0 flex items-center justify-center font-bold text-sky-500">{{ mb_substr($doc->name, 0, 1) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-800 dark:text-gray-100 group-hover:text-sky-600 truncate">{{ $doc->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $doc->designation }}</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($doc->specialties->take(2) as $sp)
                                    <span class="px-1.5 py-0.5 rounded-full bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 text-xs">{{ $sp->getTranslation('name', app()->getLocale()) }}</span>
                                @endforeach
                            </div>
                        </div>
                        @if($doc->verified)
                            <span class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        @endif
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400">{{ __('No Doctors Found.') }}</p>
                @endif
            </div>

            {{-- Map --}}
            @if($hospital->lat && $hospital->lng)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold text-gray-800 dark:text-gray-100">📍 {{ __('Location') }}</h2>
                    <a href="https://www.google.com/maps?q={{ $hospital->lat }},{{ $hospital->lng }}" target="_blank"
                       class="text-xs px-3 py-1.5 rounded-lg bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 hover:bg-sky-100 transition-colors border border-sky-200 dark:border-sky-800">Open in Maps →</a>
                </div>
                <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-600 h-56">
                    <iframe src="https://www.google.com/maps/embed/v1/place?key={{ env('GOOGLE_MAPS_API_KEY') }}&q={{ $hospital->lat }},{{ $hospital->lng }}"
                        class="w-full h-full border-0" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
            @endif

        </div>
        {{-- END LEFT COLUMN --}}

        {{-- ── RIGHT COLUMN: SIDEBAR ── --}}
        <div class="w-full lg:w-1/4 flex-shrink-0">
            <div class="sticky top-24 space-y-5">
                @php
                    $sidebarTopAd = \App\Models\Advertisement::where('is_active', true)->where('position', 'sidebar_top')->inRandomOrder()->first();
                    $sidebarBottomAd = \App\Models\Advertisement::where('is_active', true)->where('position', 'sidebar_bottom')->inRandomOrder()->first();
                @endphp

                @if(is_null($hospital->user_id))
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-3xl border border-emerald-100 dark:border-emerald-800/50 p-6 shadow-sm text-center relative overflow-hidden mb-5">
                        {{-- Decorative background --}}
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-200/50 dark:bg-emerald-800/30 rounded-full blur-xl pointer-events-none"></div>
                        <div class="absolute -left-6 -bottom-6 w-24 h-24 bg-teal-200/50 dark:bg-teal-800/30 rounded-full blur-xl pointer-events-none"></div>
                        
                        <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-3 shadow-md text-emerald-500 relative z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h4 class="font-black text-gray-900 dark:text-white text-lg mb-1 relative z-10">{{ __('Are you the Hospital Admin?') }}</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mb-5 relative z-10 px-2">{{ __('Claim this profile to easily manage listings, update information, and directly reach your patients.') }}</p>

                        <div class="relative z-10">
                        @auth
                            @php
                                $hasPendingClaim = auth()->user()->claimRequests()->where('hospital_id', $hospital->id)->where('status', 'pending')->exists();
                            @endphp
                            @if($hasPendingClaim)
                                <div class="w-full py-3 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 text-sm font-bold border border-amber-200 dark:border-amber-800/50 flex items-center justify-center gap-2 shadow-inner">
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ __('Claim Pending Approval') }}
                                </div>
                            @else
                                <div x-data="{ claimModal: false }">
                                    <button @click="claimModal = true" class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-sm font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                        {{ __('Claim This Hospital') }}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>

                                    {{-- Modal --}}
                                    <template x-teleport="body">
                                        <div x-show="claimModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" x-transition.opacity>
                                            <div @click.away="claimModal = false" class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden relative" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                                                <button @click="claimModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                                <div class="p-8 text-left">
                                                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center mb-5 shadow-sm border border-emerald-200 dark:border-emerald-800">
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                    </div>
                                                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">{{ __('Hospital Claim Request') }}</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                                                        {{ __('Please provide a secure message detailing your professional identity. Include official hospital ID reference or authorization letter.') }}
                                                    </p>
                                                    
                                                    <form method="POST" action="{{ route('hospitals.claim', $hospital->id) }}">
                                                        @csrf
                                                        <div class="mb-5">
                                                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">{{ __('Identity Proof / Message') }}</label>
                                                            <textarea name="message" rows="4" required placeholder="E.g., I am the administrator of this hospital. Verification link..." class="w-full border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors placeholder-gray-400"></textarea>
                                                        </div>
                                                        <div class="flex gap-3">
                                                            <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold py-3 rounded-xl shadow-md transition-all">{{ __('Submit Secure Request') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-sm font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                {{ __('Claim This Hospital') }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endauth
                        </div>
                    </div>
                @endif

                {{-- Report Duplicate Profile --}}
                <div x-data="{ reportModal: false }" class="mt-4">
                    <button @click="reportModal = true" class="w-full py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/10 hover:text-red-500 hover:border-red-200 dark:hover:border-red-800/50 transition-all text-xs font-semibold flex justify-center items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Report Duplicate or Issue
                    </button>

                    <template x-teleport="body">
                        <div x-show="reportModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" x-transition.opacity>
                            <div @click.away="reportModal = false" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                                <button @click="reportModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                <div class="p-6 text-left">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Report Profile
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                                        Did you find a duplicate of this Hospital's profile, or is there incorrect information? Let us know.
                                    </p>
                                    
                                    <form method="POST" action="{{ route('report-duplicate.store') }}">
                                        @csrf
                                        <input type="hidden" name="reportable_id" value="{{ $hospital->id }}">
                                        <input type="hidden" name="reportable_type" value="App\Models\Hospital">
                                        <div class="mb-4">
                                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Reason</label>
                                            <textarea name="reason" rows="3" required placeholder="E.g., This is a duplicate listing. The other profile is..." class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white"></textarea>
                                        </div>
                                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl shadow-sm transition-colors">Submit Report</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Reviews moved to main section --}}

                {{-- Ad Container 1 --}}
                @if($sidebarTopAd)
                <div class="w-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow relative group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-400 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm z-10">Ad</span>
                    @if($sidebarTopAd->target_url)
                        <a href="{{ $sidebarTopAd->target_url }}" target="_blank" class="block w-full h-full flex items-center justify-center">
                            <img src="{{ asset('storage/' . $sidebarTopAd->image_path) }}" alt="{{ $sidebarTopAd->title }}" class="w-full h-full max-h-[400px] object-contain">
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $sidebarTopAd->image_path) }}" alt="{{ $sidebarTopAd->title }}" class="w-full h-full max-h-[400px] object-contain">
                    @endif
                </div>
                @else
                <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 flex flex-col items-center justify-center text-center h-[250px] shadow-sm relative overflow-hidden group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-400 bg-white dark:bg-gray-900 px-2 py-0.5 rounded shadow-sm">Ad</span>
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8"/></svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ __('Sponsor Space Available') }}</p>
                    <p class="text-xs text-gray-400 mt-1 px-2">{{ __('Place your banner here to reach thousands of patients daily.') }}</p>
                </div>
                @endif

                {{-- Ad Container 2 --}}
                @if($sidebarBottomAd)
                <div class="w-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow relative group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-sky-500 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm z-10">Sponsored</span>
                    @if($sidebarBottomAd->target_url)
                        <a href="{{ $sidebarBottomAd->target_url }}" target="_blank" class="block w-full h-full flex items-center justify-center">
                            <img src="{{ asset('storage/' . $sidebarBottomAd->image_path) }}" alt="{{ $sidebarBottomAd->title }}" class="w-full h-full max-h-[400px] object-contain">
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $sidebarBottomAd->image_path) }}" alt="{{ $sidebarBottomAd->title }}" class="w-full h-full max-h-[400px] object-contain">
                    @endif
                </div>
                @else
                <div class="bg-gradient-to-br from-teal-50 to-emerald-50 dark:from-teal-900/20 dark:to-emerald-900/20 rounded-2xl border border-teal-100 dark:border-teal-800/50 p-6 flex flex-col items-center justify-center text-center h-[350px] shadow-sm relative overflow-hidden group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-teal-600 bg-white/80 dark:bg-gray-900/80 backdrop-blur px-2 py-0.5 rounded shadow-sm">Sponsored</span>
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mb-3 shadow-md group-hover:-translate-y-1 transition-transform">
                        <svg class="w-6 h-6 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">{{ __('Showcase Your Excellence') }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Premium hospital placement.') }}</p>
                </div>
                @endif

            </div>
        </div>
        {{-- END RIGHT COLUMN --}}

    </div>
    {{-- END FLEX ROW --}}

    {{-- ═══════════════════════════════════════
         PROFILE BOTTOM LEADERBOARD AD
    ═══════════════════════════════════════ --}}
    @php
        $profileBottomAd = \App\Models\Advertisement::where('is_active', true)->where('position', 'profile_bottom')->inRandomOrder()->first();
    @endphp
    @if($profileBottomAd)
    <div class="w-full mt-10 bg-gray-100 dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm flex items-center justify-center relative group flex justify-center">
        <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-500 bg-white/80 dark:bg-gray-900/80 backdrop-blur px-2 py-0.5 rounded shadow-sm z-10">Advertisement</span>
        @if($profileBottomAd->target_url)
            <a href="{{ $profileBottomAd->target_url }}" target="_blank" class="block w-full h-full flex items-center justify-center">
                <img src="{{ asset('storage/' . $profileBottomAd->image_path) }}" class="w-full h-full max-h-[120px] md:max-h-[150px] object-contain">
            </a>
        @else
            <img src="{{ asset('storage/' . $profileBottomAd->image_path) }}" class="w-full h-full max-h-[120px] md:max-h-[150px] object-contain">
        @endif
    </div>
    @endif

</div>
@endsection
