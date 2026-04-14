@extends('layouts.app')

@section('content')
<div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="container mx-auto px-4 max-w-5xl">

        {{-- Breadcrumbs --}}
        <nav class="flex text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center hover:text-emerald-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        {{ __('Home') }}
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('hospitals.show', $hospital->slug) }}" class="ml-1 md:ml-2 hover:text-emerald-600 transition-colors">{{ $hospital->name }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 text-gray-400 dark:text-gray-500 font-medium truncate max-w-[200px]">{{ $video->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Left Content Column --}}
            <div class="flex-1 w-full lg:max-w-[75%] min-w-0">
                
                {{-- Video Player Area --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
                    <div class="relative w-full bg-black" style="padding-top: 56.25%;">
                        @if($video->youtube_id)
                            <iframe class="absolute top-0 left-0 w-full h-full" 
                                    src="https://www.youtube.com/embed/{{ $video->youtube_id }}?rel=0&autoplay=1" 
                                    title="{{ $video->title }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                        @else
                            <div class="absolute top-0 left-0 w-full h-full flex flex-col items-center justify-center text-white p-6">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xl font-bold mb-4text-center">This video cannot be played directly here.</p>
                                <a href="{{ $video->video_url }}" target="_blank" class="px-6 py-3 bg-red-600 hover:bg-red-700 rounded-full font-bold transition-colors">Watch on Source</a>
                            </div>
                        @endif
                    </div>

                    <div class="p-6 md:p-8">
                        <h1 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white leading-tight mb-4">{{ $video->title }}</h1>
                        @if($video->description)
                            <div class="prose prose-sm md:prose-base dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 mb-6 bg-gray-50/50 dark:bg-gray-700/20 p-4 rounded-xl border border-gray-100 dark:border-gray-700/50">
                                {!! nl2br(e($video->description)) !!}
                            </div>
                        @endif
                        <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-4 mt-2">
                            <a href="{{ route('hospitals.show', $hospital->slug) }}" class="flex items-center gap-3 group">
                                @if($hospital->logo)
                                    <img src="{{ Storage::url($hospital->logo) }}" alt="{{ $hospital->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-600 group-hover:scale-105 transition-transform">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition-transform"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                                @endif
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-emerald-600 transition-colors">{{ $hospital->name }}</p>
                                    <p class="text-xs">{{ $video->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Related Videos Widget --}}
                @if($relatedVideos->count() > 0)
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">More Videos from {{ $hospital->name }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($relatedVideos as $relVideo)
                        <a href="{{ route('video.show', ['hospital_slug' => $hospital->slug, 'video_slug' => $relVideo->slug]) }}" class="group block bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all">
                            <div class="relative aspect-video bg-black overflow-hidden">
                                @if($relVideo->thumbnail_url)
                                    <img src="{{ $relVideo->thumbnail_url }}" alt="{{ $relVideo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gray-800 flex items-center justify-center text-gray-600"><svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></div>
                                @endif
                                <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center">
                                        <svg class="w-5 h-5 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3">
                                <h4 class="font-bold text-gray-900 dark:text-gray-100 text-sm line-clamp-2 group-hover:text-emerald-600 transition-colors leading-snug" title="{{ $relVideo->title }}">{{ $relVideo->title }}</h4>
                                <p class="text-[10px] text-gray-500 mt-1.5">{{ $relVideo->created_at->diffForHumans() }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
                
            </div> {{-- End Left Column --}}

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
                    @endif
                </div>
            </div> {{-- End Right Sidebar --}}
            
        </div>

    </div>
</div>
@endsection
