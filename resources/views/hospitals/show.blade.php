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
                        <img loading="lazy" decoding="async" src="{{ asset('storage/'.$hospital->banner) }}" alt="{{ $hospital->name }} Banner" class="absolute inset-0 w-full h-full object-cover">
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
                                <img loading="lazy" decoding="async" src="{{ asset('storage/'.$hospital->logo) }}" alt="{{ $hospital->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
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

            {{-- SPA TABS COMPONENT --}}
            <div x-data="hospitalTabs('{{ $tab ?? 'overview' }}', '{{ $hospital->slug }}')" class="w-full">
                
                {{-- TAB NAVIGATION BAR --}}
                <div class="flex items-center gap-2 mb-8 border-b border-gray-100 dark:border-gray-700 pb-px overflow-x-auto hide-scrollbar sticky top-[calc(var(--nav-height,0px))] z-40 bg-gray-50/80 dark:bg-gray-900/80 backdrop-blur-md pt-2">
                    <button @click.prevent="switchTab('overview')" 
                       :class="currentTab === 'overview' ? 'border-sky-500 text-sky-600 dark:text-sky-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                       class="px-5 py-3 text-sm font-bold border-b-2 transition-colors whitespace-nowrap outline-none flex items-center gap-2">
                        🏥 {{ __('Overview & Info') }}
                    </button>
                    <button @click.prevent="switchTab('doctors')" 
                       :class="currentTab === 'doctors' ? 'border-sky-500 text-sky-600 dark:text-sky-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                       class="px-5 py-3 text-sm font-bold border-b-2 transition-colors whitespace-nowrap outline-none flex items-center gap-2">
                        👨‍⚕️ {{ __('Doctors') }}
                        <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 py-0.5 px-2 rounded-full text-[10px]">{{ $doctors->count() }}</span>
                    </button>
                    <button @click.prevent="switchTab('diagnostics')" 
                       :class="currentTab === 'diagnostics' ? 'border-sky-500 text-sky-600 dark:text-sky-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                       class="px-5 py-3 text-sm font-bold border-b-2 transition-colors whitespace-nowrap outline-none flex items-center gap-2">
                        💉 {{ __('Diagnostics') }}
                    </button>
                    @if($hospital->hospitalVideos->count() > 0)
                    <button @click.prevent="switchTab('video')" 
                       :class="currentTab === 'video' ? 'border-sky-500 text-sky-600 dark:text-sky-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                       class="px-5 py-3 text-sm font-bold border-b-2 transition-colors whitespace-nowrap outline-none flex items-center gap-2">
                        🎥 {{ __('Video') }}
                        <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 py-0.5 px-2 rounded-full text-[10px]">{{ $hospital->hospitalVideos->count() }}</span>
                    </button>
                    @endif
                    @if(!empty($hospital->blogs) && count($hospital->blogs) > 0)
                    <button @click.prevent="switchTab('blog')" 
                       :class="currentTab === 'blog' ? 'border-sky-500 text-sky-600 dark:text-sky-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                       class="px-5 py-3 text-sm font-bold border-b-2 transition-colors whitespace-nowrap outline-none flex items-center gap-2">
                        📝 {{ __('Blog') }}
                        <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 py-0.5 px-2 rounded-full text-[10px]">{{ count($hospital->blogs) }}</span>
                    </button>
                    @endif
                </div>

                {{-- TAB CONTENT: OVERVIEW (Default) --}}
                <div x-show="currentTab === 'overview'" style="{{ ($tab ?? 'overview') === 'overview' ? '' : 'display: none;' }}" x-transition.opacity.duration.300ms>

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
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6"
                 x-data="{ 
                    lightboxOpen: false, 
                    activeImage: 0, 
                    images: {{ json_encode(array_map(fn($img) => asset('storage/' . $img), $hospital->gallery)) }}
                 }"
                 @keydown.window.escape="lightboxOpen = false"
                 @keydown.window.right="if(lightboxOpen) { activeImage = (activeImage + 1) % images.length }"
                 @keydown.window.left="if(lightboxOpen) { activeImage = (activeImage - 1 + images.length) % images.length }">
                 
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-fuchsia-100 dark:bg-fuchsia-900/30 flex items-center justify-center text-fuchsia-600">🖼️</span>
                    {{ __('Photo Gallery') }}
                </h2>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach($hospital->gallery as $index => $image)
                        <button type="button" @click="activeImage = {{ $index }}; lightboxOpen = true" class="block w-full aspect-square rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group/gallery relative focus:outline-none focus:ring-2 focus:ring-fuchsia-500">
                            <img loading="lazy" decoding="async" src="{{ asset('storage/' . $image) }}" alt="Gallery Image" class="w-full h-full object-cover transition-transform duration-500 group-hover/gallery:scale-110">
                            <div class="absolute inset-0 bg-black/0 group-hover/gallery:bg-black/20 transition-colors flex items-center justify-center">
                                <svg class="w-8 h-8 text-white opacity-0 group-hover/gallery:opacity-100 transition-opacity drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Lightbox Overlay --}}
                <template x-teleport="body">
                    <div x-show="lightboxOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 backdrop-blur-sm" x-transition.opacity>
                        {{-- Close Button --}}
                        <button @click="lightboxOpen = false" class="absolute top-4 right-4 sm:top-6 sm:right-6 text-white/50 hover:text-white bg-black/20 hover:bg-black/40 rounded-full p-2 transition-all">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        
                        {{-- Previous Button --}}
                        <button x-show="images.length > 1" @click="activeImage = (activeImage - 1 + images.length) % images.length" class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 text-white/50 hover:text-white bg-black/20 hover:bg-black/40 rounded-full p-3 transition-all focus:outline-none">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        {{-- Next Button --}}
                        <button x-show="images.length > 1" @click="activeImage = (activeImage + 1) % images.length" class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 text-white/50 hover:text-white bg-black/20 hover:bg-black/40 rounded-full p-3 transition-all focus:outline-none">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>

                        {{-- Image Counter --}}
                        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/70 font-medium text-sm tracking-widest bg-black/40 px-4 py-1.5 rounded-full">
                            <span x-text="activeImage + 1"></span> / <span x-text="images.length"></span>
                        </div>

                        {{-- Image Container --}}
                        <div class="w-full h-full p-4 sm:p-12 flex items-center justify-center relative" @click.self="lightboxOpen = false">
                            <img loading="lazy" decoding="async" :src="images[activeImage]" class="max-w-full max-h-full object-contain rounded-md shadow-2xl pointer-events-auto" :key="activeImage">
                        </div>
                    </div>
                </template>

            </div>
            @endif

                    {{-- Mini Doctors Preview for Overview --}}
                    @if($doctors->count())
                    <div class="mb-8 mt-4 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-lg">
                                <span class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center text-sky-600 border border-sky-100 dark:border-sky-800/50">👨‍⚕️</span>
                                {{ __('Hospital Doctors') }}
                            </h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($doctors->take(4) as $docPreview)
                                <a href="{{ route('doctors.show', $docPreview->slug) }}" class="flex items-center gap-4 p-3 rounded-2xl bg-gray-50 dark:bg-gray-700/30 border border-transparent hover:border-gray-200 dark:hover:border-gray-600 hover:bg-white dark:hover:bg-gray-700 hover:shadow-sm transition-all group">
                                    @if($docPreview->photo)
                                        <img loading="lazy" src="{{ asset('storage/' . $docPreview->photo) }}" class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200 dark:border-gray-600">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-white dark:bg-gray-800 bg-gray-200 flex items-center justify-center text-xl shadow-sm border border-gray-200 dark:border-gray-600">
                                            {{ $docPreview->gender === 'female' ? '👩‍⚕️' : '👨‍⚕️' }}
                                        </div>
                                    @endif
                                    <div class="flex-1 overflow-hidden">
                                        <h4 class="font-bold text-gray-900 dark:text-white group-hover:text-sky-500 transition-colors truncate">{{ $docPreview->name }}</h4>
                                        <p class="text-[11px] font-medium text-sky-600 dark:text-sky-400 mt-0.5 truncate uppercase tracking-widest">{{ $docPreview->specialties->first()?->getTranslation('name', app()->getLocale()) ?? 'Specialist' }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-5">
                            <button @click.prevent="switchTab('doctors')" class="w-full bg-sky-50 dark:bg-sky-900/20 hover:bg-sky-100 dark:hover:bg-sky-900/40 border border-sky-100 dark:border-sky-800/50 text-sky-700 dark:text-sky-400 py-3 rounded-xl font-bold transition-all shadow-sm flex items-center justify-center gap-2 hover:shadow-md">
                                {{ __('View All :count Doctors', ['count' => $doctors->count()]) }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                    @endif
                </div> {{-- END TAB CONTENT: OVERVIEW --}}

            {{-- TAB CONTENT: DOCTORS --}}
            <div x-show="currentTab === 'doctors'" style="{{ ($tab ?? 'overview') === 'doctors' ? '' : 'display: none;' }}" x-transition.opacity.duration.300ms x-cloak>
            <div class="mb-8" x-data="hospitalDoctorsFilter()" x-init="update()">
                <div class="mb-6">
                    @php
                        $hospitalSpecialtiesArray = [];
                        foreach($doctors as $d) {
                            foreach($d->specialties as $sp) {
                                if(!isset($hospitalSpecialtiesArray[$sp->slug])) {
                                    $hospitalSpecialtiesArray[$sp->slug] = [
                                        'name' => $sp->getTranslation('name', app()->getLocale()),
                                        'count' => 1
                                    ];
                                } else {
                                    $hospitalSpecialtiesArray[$sp->slug]['count']++;
                                }
                            }
                        }
                        $hospitalSpecialties = collect($hospitalSpecialtiesArray)->sortBy('name');
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-gray-700 shadow-sm relative">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-4">
                            <h4 class="font-bold text-gray-800 dark:text-gray-200 text-sm flex items-center gap-2">
                                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                {{ __('Filter by Specialty') }}
                            </h4>
                            <div class="relative w-full sm:w-64">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input type="text" x-model="searchSpecialty" placeholder="{{ __('Search specialty...') }}" class="w-full pl-9 pr-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-sky-500 outline-none transition-all">
                            </div>
                        </div>
                        
                        <div class="relative">
                            <div class="flex flex-wrap gap-2 overflow-hidden transition-all duration-300" :class="showAllSpecialties || searchSpecialty !== '' ? 'max-h-[600px] overflow-y-auto hide-scrollbar' : 'max-h-[80px]'">
                                <button x-show="searchSpecialty === ''" @click="setSpecialty('')" 
                                    :class="selectedSpecialty === '' ? 'bg-sky-500 text-white shadow-sm border-sky-500' : 'bg-gray-50 dark:bg-gray-800/80 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="whitespace-nowrap px-4 py-2 rounded-full text-[13px] font-bold border transition-colors flex items-center gap-1.5 focus:outline-none shrink-0">
                                    {{ __('All Specialties') }} 
                                    <span :class="selectedSpecialty === '' ? 'bg-white/20 text-white' : 'bg-gray-200/50 dark:bg-gray-700 text-gray-500 dark:text-gray-400'" class="px-1.5 py-0.5 rounded-full text-[10px]">{{ $doctors->count() }}</span>
                                </button>
                                @foreach($hospitalSpecialties as $slug => $data)
                                <button x-show="searchSpecialty === '' || '{{ strtolower($data['name']) }}'.includes(searchSpecialty.toLowerCase())" 
                                    @click="setSpecialty('{{ $slug }}')" 
                                    :class="selectedSpecialty === '{{ $slug }}' ? 'bg-sky-500 text-white shadow-sm border-sky-500' : 'bg-gray-50 dark:bg-gray-800/80 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="whitespace-nowrap px-4 py-2 rounded-full text-[13px] font-bold border transition-colors flex items-center gap-1.5 focus:outline-none shrink-0" x-cloak>
                                    {{ $data['name'] }}
                                    <span :class="selectedSpecialty === '{{ $slug }}' ? 'bg-white/20 text-white' : 'bg-gray-200/50 dark:bg-gray-700 text-gray-500 dark:text-gray-400'" class="px-1.5 py-0.5 rounded-full text-[10px]">{{ $data['count'] }}</span>
                                </button>
                                @endforeach
                            </div>
                            <!-- Gradient Fade for collapsed state -->
                            <div x-show="!showAllSpecialties && searchSpecialty === ''" class="absolute bottom-0 left-0 w-full h-8 bg-gradient-to-t from-white dark:from-gray-800 to-transparent pointer-events-none"></div>
                        </div>

                        <div x-show="searchSpecialty === ''" class="mt-3 flex justify-center border-t border-gray-100 dark:border-gray-700 pt-3">
                            <button @click="showAllSpecialties = !showAllSpecialties" class="text-xs font-bold text-sky-500 hover:text-sky-600 dark:hover:text-sky-400 flex items-center gap-1">
                                <span x-text="showAllSpecialties ? '{{ __('Show Less') }}' : '{{ __('View All Specialties') }}'"></span>
                                <svg class="w-3 h-3 transition-transform duration-300" :class="showAllSpecialties ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                @if($doctors->count())
                <div class="space-y-4" x-ref="doctorList">
                    @foreach($doctors as $doctor)
                        <div class="doctor-card-item group bg-white dark:bg-gray-800 rounded-[1.5rem] shadow-sm hover:shadow-lg border transition-all duration-300 w-full overflow-hidden flex flex-col sm:flex-row relative {{ $doctor->featured ? 'border-amber-300 dark:border-amber-600/50 shadow-amber-50 dark:shadow-amber-900/10' : 'border-gray-100 dark:border-gray-700' }}" data-specialties="{{ $doctor->specialties->pluck('slug')->implode(',') }}">

                            {{-- Featured: top gradient bar --}}
                            @if($doctor->featured)
                            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-400"></div>
                            <div class="absolute top-2 right-3 z-10">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 text-[10px] font-black uppercase tracking-wide border border-amber-200 dark:border-amber-700 shadow-sm">
                                    ⭐ {{ __('Featured') }}
                                </span>
                            </div>
                            @endif

                            {{-- Image Column --}}
                            <div class="w-full sm:w-48 bg-gray-50/50 dark:bg-gray-900/20 border-b sm:border-b-0 sm:border-r border-gray-100 dark:border-gray-700/50 p-6 flex flex-col items-center justify-center flex-shrink-0">
                                <a href="{{ route('doctors.show', $doctor->slug) }}" class="block hover:opacity-80 transition-opacity">
                                    @if($doctor->photo)
                                        <img loading="lazy" decoding="async" src="{{ asset('storage/' . $doctor->photo) }}" class="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-sm mb-3">
                                    @else
                                        <div class="w-24 h-24 rounded-full bg-white dark:bg-gray-800 border-4 border-white dark:border-gray-700 flex items-center justify-center text-4xl shadow-sm mb-3">
                                            {{ $doctor->gender === 'female' ? '👩‍⚕️' : '👨‍⚕️' }}
                                        </div>
                                    @endif
                                </a>
                                
                                @if($doctor->rating_avg >= 4.5)
                                <div class="bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 rounded-full px-2.5 py-0.5 flex items-center gap-1 border border-amber-200 dark:border-amber-800/50 mt-1 shadow-sm">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <span class="text-[10px] font-bold tracking-widest uppercase">TOP</span>
                                </div>
                                @endif
                            </div>

                            {{-- Details Content --}}
                            <div class="p-6 flex flex-col md:flex-row md:items-start justify-between gap-6 flex-1">
                                
                                {{-- Main Details --}}
                                <div class="flex-1">
                                    <a href="{{ route('doctors.show', $doctor->slug) }}" class="block">
                                        <h3 class="font-black text-xl text-gray-900 dark:text-white group-hover:text-sky-600 transition-colors leading-tight mb-1">{{ $doctor->name }}</h3>
                                    </a>
                                    <div class="flex flex-wrap items-center gap-3 mb-4">
                                        {{-- Specialties --}}
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($doctor->specialties as $sp)
                                                <a href="{{ route('doctors.index', ['specialty' => $sp->slug]) }}" class="text-[10px] font-bold text-sky-600 dark:text-sky-400 uppercase tracking-widest hover:text-sky-700 dark:hover:text-sky-300 hover:underline transition-colors bg-sky-50 dark:bg-sky-900/30 px-2 py-0.5 rounded-full border border-sky-100 dark:border-sky-800/50">
                                                    {{ $sp->getTranslation('name', app()->getLocale()) }}
                                                </a>
                                            @endforeach
                                        </div>

                                        {{-- Rating (only shown when reviews exist) --}}
                                        @if($doctor->approvedReviews && $doctor->approvedReviews->count() > 0)
                                        <div class="flex items-center gap-1 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded-lg border border-amber-100 dark:border-amber-800/30">
                                            <div class="flex text-amber-400">
                                                @for($i=1; $i<=5; $i++)
                                                    <svg class="w-2.5 h-2.5 {{ $i <= floor($doctor->average_rating) ? 'fill-current' : 'text-gray-200 dark:text-gray-700' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                @endfor
                                            </div>
                                            <span class="text-[10px] font-black text-amber-700 dark:text-amber-400">{{ $doctor->average_rating }}</span>
                                            <span class="text-[9px] font-bold text-gray-400">({{ $doctor->approvedReviews->count() }})</span>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm text-gray-600 dark:text-gray-400">
                                        @if($doctor->qualifications)
                                        <div class="flex items-start gap-2">
                                            <svg class="w-4 h-4 text-sky-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-0.5">{{ __('Qualifications') }}</span>
                                                <span class="leading-relaxed font-semibold text-gray-800 dark:text-gray-200 line-clamp-2">{{ $doctor->qualifications }}</span>
                                            </div>
                                        </div>
                                        @endif

                                        @php
                                            $chamber = $hospital->chambers->where('doctor_id', $doctor->id)->first();
                                            $visitingHours = $chamber?->visiting_hours ?: __('Contact for Schedule');
                                            $closedDays = $chamber?->closed_days ? (is_string($chamber->closed_days) ? json_decode($chamber->closed_days, true) : $chamber->closed_days) : null;
                                        @endphp
                                        <div class="flex items-start gap-2">
                                            <svg class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-0.5">{{ __('Visiting Schedule') }}</span>
                                                <div class="leading-relaxed font-semibold text-gray-800 dark:text-gray-200 text-[13px]">
                                                    {{ $visitingHours }}
                                                    @if(!empty($closedDays) && is_array($closedDays))
                                                        <span class="block text-[10px] text-red-500 font-bold tracking-wider mt-0.5 uppercase">{{ __('Closed:') }} {{ implode(', ', array_map('__', $closedDays)) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($doctor->experience_years > 0)
                                        <div class="flex items-start gap-2 md:col-span-2 mt-1">
                                            <svg class="w-4 h-4 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">{{ __('Total Experience') }}:</span>
                                                <span class="px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 font-black text-xs border border-indigo-100 dark:border-indigo-800/50">
                                                    {{ $doctor->experience_years }}+ {{ __('Years') }}
                                                </span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- Call to Action --}}
                                <div class="flex flex-col justify-center items-start md:items-end w-full md:w-48 flex-shrink-0 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-700/50 pt-4 md:pt-0 md:pl-6 h-full min-h-[120px]">
                                    <a href="{{ route('doctors.show', $doctor->slug) }}"
                                       class="w-full text-center px-4 py-3 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 text-white font-bold text-sm transition-all hover:scale-[1.02] active:scale-95 shadow-md shadow-sky-500/20 whitespace-nowrap">
                                        {{ __('Book Appointment') }} &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 text-center" x-show="visibleCount < totalCount" x-cloak>
                    <button @click="loadMore" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all shadow-sm flex items-center justify-center gap-2 mx-auto">
                        {{ __('Load More Doctors') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <p class="text-xs text-gray-400 mt-2" x-text="'Showing ' + visibleCount + ' of ' + totalCount + ' doctors'"></p>
                </div>
                @else
                <div class="text-center py-10 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="text-4xl mb-4 opacity-50">🏜️</div>
                    <p class="text-sm font-medium text-gray-400">{{ __('No Doctors Found matching the selected criteria.') }}</p>
                </div>
                @endif
            </div>
            </div> {{-- END TAB CONTENT: DOCTORS --}}

            {{-- TAB CONTENT: DIAGNOSTICS --}}
            <div x-show="currentTab === 'diagnostics'" style="{{ ($tab ?? 'overview') === 'diagnostics' ? '' : 'display: none;' }}" x-transition.opacity.duration.300ms x-cloak>
            
            @if(isset($hospital) && $hospital->hospitalServices()->count() > 0)
                <div x-data="hospitalDiagnosticServices()" class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8 mb-8">
                    
                    {{-- Header & Search --}}
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-8">
                        <div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="bg-sky-100 dark:bg-sky-900/30 text-sky-500 w-8 h-8 rounded-full flex items-center justify-center text-lg">💉</span>
                                {{ __('Diagnostic Tests & Pricing') }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Find real-time pricing for tests and services at this branch.') }}</p>
                        </div>
                        
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <!-- Category Filter -->
                            <select x-model="selectedCategory" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm font-semibold text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-sky-500 max-w-[180px]">
                                <option value="">{{ __('All Categories') }}</option>
                                <template x-for="cat in categories" :key="cat">
                                    <option :value="cat" x-text="cat"></option>
                                </template>
                            </select>
                            
                            <!-- Search -->
                            <div class="relative w-full md:w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" x-model.debounce.250ms="searchQuery" placeholder="{{ __('Search tests...') }}" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                                <button x-show="searchQuery !== ''" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Data Loading/Empty State --}}
                    <div x-show="filteredServicesCount === 0" class="text-center py-10" x-cloak>
                        <div class="text-4xl mb-4 opacity-50">🔍</div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('No tests found matching your search criteria.') }}</p>
                        <button @click="searchQuery = ''; selectedCategory = '';" class="mt-4 text-sky-500 hover:underline text-sm font-semibold">{{ __('Clear Filters') }}</button>
                    </div>

                    {{-- Data List --}}
                    <div class="space-y-6">
                        <template x-for="group in paginatedGroupedServices" :key="group.name">
                            <div class="border border-gray-100 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                                <div class="bg-gray-50/80 dark:bg-gray-800/80 px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 text-sm" x-text="group.name"></h4>
                                    <span class="text-[10px] font-bold bg-white dark:bg-gray-700 px-2.5 py-1 rounded-full text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600" x-text="group.services.length + ' {{ __('Tests') }}'"></span>
                                </div>
                                <div class="divide-y divide-gray-100 dark:divide-gray-700/50 hide-scrollbar">
                                    <template x-for="service in group.services" :key="service.id">
                                        <a :href="`/hospital/{{ $hospital->slug }}/diagnostics/${service.slug}`" class="flex flex-col sm:flex-row sm:items-center justify-between p-5 hover:bg-sky-50 dark:hover:bg-gray-700/40 transition-colors group/item block border-b border-gray-50 dark:border-gray-700/50 last:border-0 hover:shadow-sm">
                                            <div class="mb-3 sm:mb-0 pr-4 flex-1">
                                                <p class="text-base font-black text-gray-800 dark:text-gray-100 group-hover/item:text-sky-600 dark:group-hover/item:text-sky-400 transition-colors mb-1.5" x-text="service.service_name"></p>
                                                <template x-if="service.description">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed max-w-2xl" x-html="service.description"></p>
                                                </template>
                                                <div class="mt-2 text-[11px] font-bold text-sky-500 opacity-80 group-hover/item:opacity-100 flex items-center gap-1 transition-opacity">
                                                    {{ __('View Test Details') }} <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 mt-3 sm:mt-0 bg-gray-50 dark:bg-gray-800 sm:bg-transparent p-3 sm:p-0 rounded-lg sm:rounded-none border border-gray-100 dark:border-transparent sm:border-0">
                                                <div class="text-xs text-gray-400 font-semibold sm:hidden">{{ __('Cost:') }}</div>
                                                <span class="inline-block px-4 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-bold text-sm border border-emerald-100 dark:border-emerald-800/50 whitespace-nowrap shadow-sm group-hover/item:border-emerald-200 dark:group-hover/item:border-emerald-700 transition-colors" x-text="service.price ? '৳ ' + service.price : '-'"></span>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Load More Option for Diagnostics --}}
                    <div class="mt-8 text-center" x-show="filteredServicesCount > testLimit" x-cloak>
                        <button @click="testLimit += 20" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all shadow-sm flex items-center justify-center gap-2 mx-auto">
                            {{ __('Load More Tests') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <p class="text-xs text-gray-400 mt-2" x-text="'Showing ' + Math.min(testLimit, filteredServicesCount) + ' of ' + filteredServicesCount + ' matching tests'"></p>
                    </div>

                </div>
            @else
                <div class="mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 text-center py-16">
                        <div class="w-20 h-20 mx-auto bg-sky-50 dark:bg-sky-900/20 rounded-full flex items-center justify-center mb-5">
                            <span class="text-4xl">💉</span>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">{{ __('Diagnostic Tests & Services') }}</h3>
                        <p class="text-sm font-medium text-gray-500 max-w-md mx-auto leading-relaxed">{{ __('Detailed pricing and diagnostic service information for this branch will be available soon.') }}</p>
                    </div>
                </div>
            @endif
            </div> {{-- END TAB CONTENT: DIAGNOSTICS --}}

            @if($hospital->hospitalVideos->count() > 0)
            {{-- TAB CONTENT: VIDEO --}}
            <div x-show="currentTab === 'video'" style="{{ ($tab ?? 'overview') === 'video' ? '' : 'display: none;' }}" x-transition.opacity.duration.300ms x-cloak>
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8 mb-8">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2 mb-6">
                        <span class="bg-red-100 dark:bg-red-900/30 text-red-500 w-8 h-8 rounded-full flex items-center justify-center text-lg">🎥</span>
                        {{ __('Hospital Videos') }}
                    </h3>
                    
                    <div x-data="{ limit: 12 }" class="flex flex-col gap-4">
                        @foreach($hospital->hospitalVideos as $index => $video)
                        @php
                            $isFacebook = str_contains(strtolower($video->video_url ?? $video->url), 'facebook.com') || str_contains(strtolower($video->video_url ?? $video->url), 'fb.watch');
                            $videoHref = $isFacebook ? ($video->video_url ?? $video->url) : route('video.show', ['hospital_slug' => $hospital->slug, 'video_slug' => $video->slug]);
                            $targetAttr = $isFacebook ? 'target="_blank" rel="nofollow noopener"' : '';
                        @endphp
                        <a href="{{ $videoHref }}" {!! $targetAttr !!} x-show="limit > {{ $index }}" x-collapse.duration.500ms class="group flex flex-col sm:flex-row items-center gap-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700/50 hover:shadow-md transition-all" style="display: none;">
                            <div class="relative w-full sm:w-48 aspect-video rounded-xl bg-black overflow-hidden shrink-0">
                                @if($video->thumbnail_url)
                                    <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-105 transition-all duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-600 bg-gray-800"><svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></div>
                                @endif
                                <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-100 sm:opacity-80 group-hover:opacity-100 transition-opacity">
                                    <div class="w-12 h-12 rounded-full {{ $isFacebook ? 'bg-blue-600' : 'bg-red-600' }} text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                        @if($isFacebook)
                                            <svg class="w-6 h-6 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        @else
                                            <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 w-full text-center sm:text-left">
                                <h4 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1 group-hover:text-emerald-600 transition-colors leading-snug">{{ $video->title }}</h4>
                                <p class="text-xs text-gray-500 flex items-center justify-center sm:justify-start gap-1">
                                    @if($hospital->logo)
                                        <img src="{{ Storage::url($hospital->logo) }}" class="w-4 h-4 rounded-full object-cover">
                                    @endif
                                    {{ $hospital->name }} &bull; {{ $video->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                        @endforeach
                        
                        @if($hospital->hospitalVideos->count() > 12)
                        <div class="text-center mt-6">
                            <button type="button" x-show="limit < {{ $hospital->hospitalVideos->count() }}" @click.prevent="limit += 12" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                                <span>Load More Videos</span>
                                <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div> {{-- END TAB CONTENT: VIDEO --}}
            @endif

            @if(!empty($hospital->blogs) && count($hospital->blogs) > 0)
            {{-- TAB CONTENT: BLOG --}}
            <div x-show="currentTab === 'blog'" style="{{ ($tab ?? 'overview') === 'blog' ? '' : 'display: none;' }}" x-transition.opacity.duration.300ms x-cloak>
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8 mb-4">
                    <div class="text-center flex flex-col items-center mb-6">
                        <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500 rounded-full flex items-center justify-center text-3xl mb-3 shadow-sm border border-emerald-100 dark:border-emerald-800/50">📝</div>
                        <h3 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white mb-1.5">{{ __('Read our Articles & Blogs') }}</h3>
                        <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 max-w-lg mb-2">{{ __('Explore in-depth articles, health tips, and hospital updates.') }}</p>
                    </div>
                    
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                        @foreach($hospital->blogs as $index => $blogData)
                        @php
                            $bUrl = is_string($blogData) ? $blogData : ($blogData['url'] ?? '#');
                            $bTitle = is_string($blogData) ? __('Read Article') . (count($hospital->blogs) > 1 ? ' #' . ($index + 1) : '') : ($blogData['title'] ?? __('Read Article'));
                            $bImage = is_string($blogData) ? null : ($blogData['image'] ?? null);
                            $parsedUrl = parse_url($bUrl);
                            $domain = $parsedUrl['host'] ?? '';
                            $isInternal = empty($domain) || str_contains($domain, 'doctorbd24.com');
                            $relAttr = $isInternal ? 'dofollow' : 'nofollow noopener noreferrer';
                        @endphp
                        <a href="{{ $bUrl }}" target="_blank" rel="{{ $relAttr }}" class="group flex flex-col rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:border-emerald-300 dark:hover:border-emerald-700 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <div class="w-full h-40 bg-gray-100 dark:bg-gray-900 overflow-hidden relative">
                                @if($bImage)
                                    <img src="{{ $bImage }}" alt="{{ $bTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-600">
                                        <svg class="w-12 h-12 mb-2 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.96-2.36L6.5 17h11l-3.54-4.71z"/></svg>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <div class="p-5 flex flex-col flex-1">
                                <h4 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-emerald-600 transition-colors line-clamp-2 leading-snug mb-2">{{ $bTitle }}</h4>
                                <div class="mt-auto flex items-center justify-between">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                                        {{ $isInternal ? 'Internal Article' : ($domain ?: 'External Link') }}
                                    </span>
                                    <span class="text-emerald-500 shrink-0 transform group-hover:translate-x-1 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                        </div>
                </div>
            </div> {{-- END TAB CONTENT: BLOG --}}
            @endif
            </div> {{-- END SPA TABS COMPONENT --}}

            {{-- Map --}}
            @if($hospital->address || ($hospital->lat && $hospital->lng))
            @php
                // Build a smart query to show the place details (Name, Reviews, etc.) instead of just a generic pin
                $mapQuery = urlencode($hospital->name . ($hospital->address ? ', ' . $hospital->address : ''));
                if (!$hospital->address && $hospital->lat && $hospital->lng) {
                    $mapQuery = $hospital->lat . ',' . $hospital->lng;
                }
                
                // Intelligent external link
                $mapsLink = $hospital->google_maps_url ?: "https://www.google.com/maps/search/?api=1&query=" . $mapQuery;
                if (!$hospital->google_maps_url && $hospital->lat && $hospital->lng) {
                    $mapsLink = "https://www.google.com/maps?q={$hospital->lat},{$hospital->lng}";
                }
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold text-gray-800 dark:text-gray-100">📍 {{ __('Location') }}</h2>
                    <a href="{{ $mapsLink }}" target="_blank"
                       class="text-xs px-3 py-1.5 rounded-lg bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 hover:bg-sky-100 transition-colors border border-sky-200 dark:border-sky-800">Open in Maps →</a>
                </div>
                <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-600 h-56">
                    <iframe src="https://www.google.com/maps/embed/v1/place?key={{ \App\Models\Setting::get('google_maps_api_key', env('GOOGLE_MAPS_API_KEY')) }}&q={{ $mapQuery }}"
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
                            <img loading="lazy" decoding="async" src="{{ asset('storage/' . $sidebarTopAd->image_path) }}" alt="{{ $sidebarTopAd->title }}" class="w-full h-full max-h-[400px] object-contain">
                        </a>
                    @else
                        <img loading="lazy" decoding="async" src="{{ asset('storage/' . $sidebarTopAd->image_path) }}" alt="{{ $sidebarTopAd->title }}" class="w-full h-full max-h-[400px] object-contain">
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
                            <img loading="lazy" decoding="async" src="{{ asset('storage/' . $sidebarBottomAd->image_path) }}" alt="{{ $sidebarBottomAd->title }}" class="w-full h-full max-h-[400px] object-contain">
                        </a>
                    @else
                        <img loading="lazy" decoding="async" src="{{ asset('storage/' . $sidebarBottomAd->image_path) }}" alt="{{ $sidebarBottomAd->title }}" class="w-full h-full max-h-[400px] object-contain">
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
                <img loading="lazy" decoding="async" src="{{ asset('storage/' . $profileBottomAd->image_path) }}" class="w-full h-full max-h-[120px] md:max-h-[150px] object-contain">
            </a>
        @else
            <img loading="lazy" decoding="async" src="{{ asset('storage/' . $profileBottomAd->image_path) }}" class="w-full h-full max-h-[120px] md:max-h-[150px] object-contain">
        @endif
    </div>
    @endif

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('hospitalDoctorsFilter', () => ({
        selectedSpecialty: '',
        searchSpecialty: '',
        showAllSpecialties: false,
        limit: 15,
        visibleCount: 0,
        totalCount: 0,
        
        update() {
            let cards = Array.from(this.$refs.doctorList.querySelectorAll('.doctor-card-item'));
            let count = 0;
            cards.forEach(card => {
                let match = this.selectedSpecialty === '' || card.dataset.specialties.split(',').includes(this.selectedSpecialty);
                if (match) {
                    if (count < this.limit) {
                        card.style.display = '';
                        count++;
                    } else {
                        card.style.display = 'none';
                    }
                    card.classList.add('is-match');
                } else {
                    card.style.display = 'none';
                    card.classList.remove('is-match');
                }
            });
            this.visibleCount = count;
            this.totalCount = cards.filter(c => c.classList.contains('is-match')).length;
        },
        
        setSpecialty(slug) {
            this.selectedSpecialty = slug;
            this.limit = 15;
            this.update();
        },
        
        loadMore() {
            this.limit += 15;
            this.update();
        }
    }));
    Alpine.data('hospitalTabs', (initialTab, slug) => ({
        currentTab: initialTab && initialTab !== '' ? initialTab : 'overview',
        slug: slug,
        
        init() {
            // Listen for browser back/forward buttons
            window.addEventListener('popstate', (e) => {
                if (e.state && e.state.tab) {
                    this.currentTab = e.state.tab;
                } else {
                    this.currentTab = 'overview';
                }
            });
            // Update initial state without reloading (so that back button works predictably)
            let stateTab = this.currentTab === '' ? 'overview' : this.currentTab;
            window.history.replaceState({tab: stateTab}, '', window.location.pathname);
        },
        
        switchTab(tab) {
            if (this.currentTab === tab) return;
            this.currentTab = tab;
            let url = '/hospital/' + this.slug;
            if (tab !== 'overview') {
                url += '/' + tab;
            }
            window.history.pushState({tab: tab}, '', url);
            
            // Scroll to top of tabs gracefully
            window.scrollTo({ top: 250, behavior: 'smooth' });
        }
    }));

    @php
        $activeServices = $hospital->hospitalServices()
            ->select('id', 'slug', 'service_category', 'service_name', 'description', 'price')
            ->where('is_active', true)
            ->get()
            ->map(function ($service) {
                if ($service->description) {
                    $service->description = trim(strip_tags($service->description));
                }
                return $service;
            });
    @endphp

    Alpine.data('hospitalDiagnosticServices', () => ({
        searchQuery: '',
        selectedCategory: '',
        testLimit: 20,
        allServices: @json($activeServices),
        
        get categories() {
            // Extract unique categories, remove any empty/falsy ones, and sort alphabetically
            return [...new Set(this.allServices.map(s => s.service_category).filter(Boolean))].sort();
        },

        get filteredServices() {
            return this.allServices.filter(s => {
                const searchTxt = this.searchQuery.toLowerCase();
                const matchesSearch = s.service_name.toLowerCase().includes(searchTxt) || 
                                      (s.service_category || '').toLowerCase().includes(searchTxt);
                const matchesCategory = this.selectedCategory === '' || s.service_category === this.selectedCategory;
                return matchesSearch && matchesCategory;
            });
        },

        get filteredServicesCount() {
            return this.filteredServices.length;
        },

        get groupedFilteredServices() {
            // Group the filtered services by category
            const groups = {};
            this.filteredServices.forEach(s => {
                const cat = s.service_category || 'Uncategorized';
                if(!groups[cat]) groups[cat] = [];
                groups[cat].push(s);
            });
            // Convert to array and sort categories alphabetically
            return Object.keys(groups).sort().map(cat => ({
                name: cat,
                services: groups[cat]
            }));
        },

        get paginatedGroupedServices() {
            let count = 0;
            const paginatedGroups = [];
            for (let group of this.groupedFilteredServices) {
                if (count >= this.testLimit) break;
                
                let limitDifference = this.testLimit - count;
                let testsToShow = group.services.slice(0, limitDifference);
                
                paginatedGroups.push({
                    name: group.name,
                    services: testsToShow
                });
                count += testsToShow.length;
            }
            return paginatedGroups;
        }
    }));
});
</script>
@endpush

@endsection
