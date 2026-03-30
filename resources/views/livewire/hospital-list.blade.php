<div>
@php
    $listTopAd = \App\Models\Advertisement::where('is_active', true)->where('position', 'list_top')->inRandomOrder()->first();
    $listInlineAd = \App\Models\Advertisement::where('is_active', true)->where('position', 'list_inline')->inRandomOrder()->first();
    $sidebarTopAd = \App\Models\Advertisement::where('is_active', true)->where('position', 'sidebar_top')->inRandomOrder()->first();
    $sidebarBottomAd = \App\Models\Advertisement::where('is_active', true)->where('position', 'sidebar_bottom')->inRandomOrder()->first();
@endphp

@if($listTopAd)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-2">
    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm flex items-center justify-center relative group">
        <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-500 bg-white/80 dark:bg-gray-900/80 backdrop-blur px-2 py-0.5 rounded shadow-sm z-10">Advertisement</span>
        @if($listTopAd->target_url)
            <a href="{{ $listTopAd->target_url }}" target="_blank" class="block w-full h-full flex items-center justify-center">
                <img src="{{ asset('storage/' . $listTopAd->image_path) }}" class="w-full h-full max-h-[120px] md:max-h-[150px] object-contain">
            </a>
        @else
            <img src="{{ asset('storage/' . $listTopAd->image_path) }}" class="w-full h-full max-h-[120px] md:max-h-[150px] object-contain">
        @endif
    </div>
</div>
@endif

@if(!$hideFilters)
<div class="w-full bg-slate-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-inner relative z-20 mb-6 mt-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 md:py-4">
        
        <div class="mb-4 flex flex-col md:flex-row md:items-end justify-between gap-2">
            <h1 class="text-xl md:text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
                <span>🏥</span> <span>{{ __('Find Trusted') }} <span class="text-emerald-600">{{ __('Hospitals') }}</span> {{ __('& Diagnostics') }}</span>
            </h1>
        </div>

        {{-- Search Bar --}}
        <div class="relative group mb-3" x-data="{ open: @entangle('showSuggestions') }" @click.away="open = false">
            <div class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       wire:model.live.debounce.400ms="search" 
                       wire:keydown.enter="applySearch"
                       placeholder="{{ __('Search Name, City or Category...') }}"
                       class="block w-full pl-11 pr-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-gray-900 dark:text-white text-sm md:text-base font-medium placeholder-gray-400"
                       @focus="open = true">
                <div wire:loading wire:target="search" class="absolute right-4">
                    <svg class="animate-spin h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
            </div>

            {{-- Suggestions Dropdown --}}
            <div x-show="open && $wire.suggestions.length > 0" 
                 x-transition
                 class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                 style="display: none;">
                @foreach($suggestions as $suggestion)
                    <button wire:click="selectSuggestion('{{ $suggestion }}')"
                            class="w-full text-left px-4 py-2 hover:bg-emerald-50 dark:hover:bg-gray-700/50 transition-colors flex items-center gap-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="text-gray-700 dark:text-gray-200 text-sm font-medium">{{ $suggestion }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-2">
            {{-- Type --}}
            <div class="col-span-2 md:col-span-1 lg:col-span-1">
                <select wire:model.live="type" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="hospital">{{ __('Hospital') }}</option>
                    <option value="diagnostic">{{ __('Diagnostic') }}</option>
                    <option value="clinic">{{ __('Clinic') }}</option>
                </select>
            </div>

            {{-- Division --}}
            <div class="col-span-1">
                <select wire:model.live="division" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All Divisions') }}</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->slug }}">{{ $div->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- District --}}
            <div class="col-span-1" x-show="$wire.division" style="display: none;">
                <select wire:model.live="district" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All Districts') }}</option>
                    @foreach($districts as $dist)
                        <option value="{{ $dist->slug }}">{{ $dist->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Area --}}
            <div class="col-span-1" x-show="$wire.district" style="display: none;">
                <select wire:model.live="area" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All Areas') }}</option>
                    @foreach($areas as $ar)
                        <option value="{{ $ar->slug }}">{{ $ar->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Verified Only --}}
            <div class="col-span-2 lg:col-span-1 flex items-center lg:justify-end">
                <label class="flex items-center gap-2 cursor-pointer group bg-white dark:bg-gray-900 px-3 py-2 rounded-xl transition-colors border border-gray-200 dark:border-gray-700 shadow-sm lg:w-full lg:justify-center">
                    <input type="checkbox" wire:model.live="verified" class="w-4 h-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500 dark:bg-gray-800 transition-all">
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-emerald-600 transition-colors">{{ __('Verified') }} <span class="hidden sm:inline">{{ __('Official Only') }}</span></span>
                </label>
            </div>
        </div>

        @if($search || $type || $division || $district || $area || $verified)
        <div class="mt-4 flex justify-end">
            <button wire:click="clearFilters" class="px-4 py-1.5 rounded-lg bg-white dark:bg-gray-900 border border-red-200 dark:border-red-900/50 text-red-500 text-xs font-bold hover:bg-red-50 dark:hover:bg-red-900/20 transition-all flex items-center gap-1 shadow-sm">
                ✕ {{ __('Clear Filters') }}
            </button>
        </div>
        @endif
    </div>
</div>
@endif

{{-- MAIN CONTENT WITH SIDEBAR --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="flex flex-col lg:flex-row gap-8">
        
        {{-- Left Column: Results (75%) --}}
        <div class="w-full lg:w-3/4 xl:w-4/5">
            
            @if($seoTitle)
                <div class="mb-6 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8 flex items-center justify-center">
                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white text-center leading-tight">{{ $seoTitle }}</h1>
                </div>
            @endif

            @if($seoTopContent)
                <div class="mb-8 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed">
                        {!! $seoTopContent !!}
                    </div>
                </div>
            @endif

            {{-- ═══════════════════════════════════════
                 RESULTS GRID
            ═══════════════════════════════════════ --}}
            <div>
                <div class="flex items-center justify-between mb-8 px-2">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                <span class="text-gray-900 dark:text-white font-bold text-lg">{{ $hospitals->total() }}</span> centers found
            </p>
        </div>

        <div wire:loading.class="opacity-40 pointer-events-none transition-opacity duration-300" class="relative">
            @if($hospitals->count())
            <div class="space-y-4">
                @foreach($hospitals as $hospital)
                <div class="relative">
                @if($hospital->featured)
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-400 rounded-t-[1.5rem] z-10"></div>
                @endif
                <a href="{{ route('hospitals.show', $hospital->slug) }}"
                   class="group bg-white dark:bg-gray-800 rounded-[1.5rem] shadow-sm hover:shadow-md border transition-all duration-300 w-full overflow-hidden flex flex-col sm:flex-row block
                       {{ $hospital->featured ? 'border-amber-300 dark:border-amber-600/50' : 'border-gray-100 dark:border-gray-700' }}">
                    
                    {{-- Icon Container (Left Column) --}}
                    <div class="w-full sm:w-48 bg-gray-50/50 dark:bg-gray-900/20 border-b sm:border-b-0 sm:border-r border-gray-100 dark:border-gray-700/50 p-6 flex flex-col items-center justify-center flex-shrink-0">
                        <div class="w-20 h-20 rounded-2xl bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-4xl group-hover:scale-105 transition-transform duration-300 border-4 border-emerald-50 dark:border-emerald-900/30 overflow-hidden">
                            @if($hospital->logo)
                                <img src="{{ asset('storage/' . $hospital->logo) }}" alt="{{ $hospital->name }}" class="w-full h-full object-cover">
                            @else
                                🏥
                            @endif
                        </div>
                        
                        @if($hospital->featured)
                            <div class="mt-3 bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 rounded-full px-2.5 py-0.5 flex items-center gap-1 border border-amber-200 dark:border-amber-800/50">
                                <span class="text-[10px] font-bold tracking-widest uppercase">⭐ Featured</span>
                            </div>
                        @endif
                    </div>

                    {{-- Details Content (Right Column) --}}
                    <div class="p-6 flex flex-col md:flex-row md:items-start justify-between gap-6 flex-1">
                        {{-- Left Side Info --}}
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-black text-xl text-gray-900 dark:text-white group-hover:text-emerald-600 transition-colors leading-tight">{{ $hospital->name }}</h3>
                                @if($hospital->verified)
                                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center shadow-sm">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="flex items-center flex-wrap gap-2 mb-4">
                                <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase tracking-widest rounded border
                                    {{ $hospital->type === 'hospital' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : '' }}
                                    {{ $hospital->type === 'diagnostic' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800' : '' }}
                                    {{ $hospital->type === 'clinic' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400 border-violet-100 dark:border-violet-800' : '' }}
                                ">{{ $hospital->type }}</span>

                                @if($hospital->approvedReviews->count() > 0)
                                <div class="inline-flex items-center gap-1 bg-amber-50 dark:bg-amber-900/20 px-2.5 py-1 rounded-lg border border-amber-100 dark:border-amber-800/30">
                                    <div class="flex text-amber-400">
                                        @for($i=1; $i<=5; $i++)
                                            <svg class="w-2.5 h-2.5 {{ $i <= floor($hospital->average_rating) ? 'fill-current' : 'text-gray-200 dark:text-gray-700' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-[10px] font-black text-amber-700 dark:text-amber-400">{{ $hospital->average_rating }}</span>
                                    <span class="text-[9px] font-bold text-gray-400">({{ $hospital->approvedReviews->count() }})</span>
                                </div>
                                @endif
                            </div>
                            
                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                @if($hospital->address)
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        <span class="leading-relaxed">{{ $hospital->address }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Right Side Action Button --}}
                        <div class="flex flex-col justify-center items-start md:items-end w-full md:w-48 flex-shrink-0 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-700/50 pt-4 md:pt-0 md:pl-6 h-full min-h-[120px]">
                            <span class="w-full text-center px-4 py-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-bold text-sm transition-all hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-500 dark:hover:text-white border border-emerald-100 dark:border-emerald-800 active:scale-95">
                                {{ __('View Details') }} &rarr;
                            </span>
                        </div>
                    </div>
                </a>
                </div>

                @if($listInlineAd && $loop->iteration % 5 === 0)
                <div class="group bg-gray-50 dark:bg-gray-800 rounded-[1.5rem] shadow-sm border border-gray-100 dark:border-gray-700/50 relative overflow-hidden flex flex-col items-center justify-center p-4">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-400 bg-white/80 dark:bg-gray-900/80 backdrop-blur px-2 py-0.5 rounded shadow-sm z-10">Advertisement</span>
                    @if($listInlineAd->target_url)
                        <a href="{{ $listInlineAd->target_url }}" target="_blank" class="block w-full h-full flex items-center justify-center hover:opacity-90 transition-opacity">
                            <img src="{{ asset('storage/' . $listInlineAd->image_path) }}" class="w-full h-full max-h-[120px] object-contain rounded-xl">
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $listInlineAd->image_path) }}" class="w-full h-full max-h-[120px] object-contain rounded-xl">
                    @endif
                </div>
                @endif

                @endforeach
            </div>
            <div class="mt-12 bg-white dark:bg-gray-800 p-4 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                {{ $hospitals->links() }}
            </div>
            @else
            <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="text-6xl mb-6">🏥</div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">{{ __('No Facilities Found') }}</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">{{ __('We couldn\'t find any hospitals or diagnostics matching your exact criteria.') }}</p>
                <button wire:click="clearFilters" class="px-8 py-3.5 rounded-2xl bg-emerald-500 text-white font-bold hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                    {{ __('Reset All Filters') }}
                </button>
            </div>
            @endif
        </div>
    </div>

            @if($seoBottomContent)
                <div class="mt-8 mb-4 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed">
                        {!! $seoBottomContent !!}
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: Ads / Sponsors (25%) --}}
        <div class="w-full lg:w-1/4 xl:w-1/5">
            <div class="sticky top-24 space-y-6">
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
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-emerald-500 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm z-10">Sponsored</span>
                    @if($sidebarBottomAd->target_url)
                        <a href="{{ $sidebarBottomAd->target_url }}" target="_blank" class="block w-full h-full flex items-center justify-center">
                            <img src="{{ asset('storage/' . $sidebarBottomAd->image_path) }}" alt="{{ $sidebarBottomAd->title }}" class="w-full h-full max-h-[400px] object-contain">
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $sidebarBottomAd->image_path) }}" alt="{{ $sidebarBottomAd->title }}" class="w-full h-full max-h-[400px] object-contain">
                    @endif
                </div>
                @else
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-800/50 p-6 flex flex-col items-center justify-center text-center h-[350px] shadow-sm relative overflow-hidden group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-emerald-500 bg-white/80 dark:bg-gray-900/80 backdrop-blur px-2 py-0.5 rounded shadow-sm">Sponsored</span>
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mb-3 shadow-md group-hover:-translate-y-1 transition-transform">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">{{ __('Grow Your Business') }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Premium placement for hospitals & clinics.') }}</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>