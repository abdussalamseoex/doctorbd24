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
                <span>👨‍⚕️</span> <span>{{ __('Find the Best') }} <span class="text-sky-600">{{ __('Doctors') }}</span></span>
            </h1>
        </div>

        {{-- Search Bar --}}
        <div class="relative group mb-3" x-data="{ open: @entangle('showSuggestions') }" @click.away="open = false">
            <div class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       wire:model.live.debounce.400ms="search" 
                       wire:keydown.enter="applySearch"
                       placeholder="{{ __('Search by Name, Spec., or Keyword...') }}"
                       class="block w-full pl-11 pr-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition-all text-gray-900 dark:text-white text-sm md:text-base font-medium placeholder-gray-400"
                       @focus="open = true">
                <div wire:loading wire:target="search" class="absolute right-4">
                    <svg class="animate-spin h-5 w-5 text-sky-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
            </div>

            {{-- Suggestions Dropdown --}}
            <div x-show="open && $wire.suggestions.length > 0" 
                 x-transition
                 class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                 style="display: none;">
                @foreach($suggestions as $suggestion)
                    <button wire:click="selectSuggestion('{{ $suggestion }}')"
                            class="w-full text-left px-4 py-2 hover:bg-sky-50 dark:hover:bg-gray-700/50 transition-colors flex items-center gap-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="text-gray-700 dark:text-gray-200 text-sm font-medium">{{ $suggestion }}</span>
                    </button>
                @endforeach
            </div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
            {{-- Specialty --}}
            <div class="col-span-2 md:col-span-1">
                <select wire:model.live="specialty" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All Specialisms') }}</option>
                    @foreach($specialties as $s)
                        <option value="{{ $s->slug }}">{{ $s->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Division --}}
            <div class="col-span-1">
                <select wire:model.live="division" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All Divisions') }}</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->slug }}">{{ $div->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- District --}}
            <div class="col-span-1" x-show="$wire.division" style="display: none;">
                <select wire:model.live="district" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All Districts') }}</option>
                    @foreach($districts as $dist)
                        <option value="{{ $dist->slug }}">{{ $dist->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Area --}}
            <div class="col-span-1" x-show="$wire.district" style="display: none;">
                <select wire:model.live="area" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All Areas') }}</option>
                    @foreach($areas as $ar)
                        <option value="{{ $ar->slug }}">{{ $ar->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Gender --}}
            <div class="col-span-1">
                 <select wire:model.live="gender" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('Any Gender') }}</option>
                    <option value="male">{{ __('Male') }}</option>
                    <option value="female">{{ __('Female') }}</option>
                </select>
            </div>

            {{-- Experience --}}
            <div class="col-span-1">
                <select wire:model.live="minExperience" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('Any Exp.') }}</option>
                    <option value="5">5+ {{ __('Years') }}</option>
                    <option value="10">10+ {{ __('Years') }}</option>
                    <option value="15">15+ {{ __('Years') }}</option>
                    <option value="20">20+ {{ __('Years') }}</option>
                </select>
            </div>
        </div>

        @if($search || $specialty || $division || $district || $area || $gender || $minExperience)
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
                <div class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4 px-2">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                <span class="text-gray-900 dark:text-white font-bold text-lg">{{ $doctors->total() }}</span> available specialists found
            </p>
            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-2 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Sort By') }}:</label>
                <select wire:model.live="sort" class="bg-transparent border-none focus:ring-0 text-sm font-bold text-sky-600 dark:text-sky-400 cursor-pointer pr-8 py-0">
                    <option value="latest">Latest Addition</option>
                    <option value="experience">Most Experience</option>
                    <option value="rating">Highest Rating</option>
                    <option value="fees_low">Consultation Fee: Low to High</option>
                </select>
            </div>
        </div>

        <div wire:loading.class="opacity-40 pointer-events-none transition-opacity duration-300" class="relative">
            @if($doctors->count())
                <div class="space-y-4">
                    @foreach($doctors as $doctor)
                        <div class="group bg-white dark:bg-gray-800 rounded-[1.5rem] shadow-sm hover:shadow-md border transition-all duration-300 w-full overflow-hidden flex flex-col sm:flex-row relative
                            {{ $doctor->featured ? 'border-amber-300 dark:border-amber-600/50 shadow-amber-50 dark:shadow-amber-900/10' : 'border-gray-100 dark:border-gray-700' }}">

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
                                        <img src="{{ Storage::url($doctor->photo) }}" class="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-sm mb-3">
                                    @else
                                        <div class="w-24 h-24 rounded-full bg-white dark:bg-gray-700 border-4 border-white dark:border-gray-600 flex items-center justify-center text-4xl shadow-sm mb-3">
                                            {{ $doctor->gender === 'female' ? '👩‍⚕️' : '👨‍⚕️' }}
                                        </div>
                                    @endif
                                </a>
                                
                                @if($doctor->rating_avg >= 4.5)
                                <div class="bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 rounded-full px-2.5 py-0.5 flex items-center gap-1 border border-amber-200 dark:border-amber-800/50 mt-1">
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
                                                <a href="{{ route('doctors.index', ['specialty' => $sp->slug]) }}" class="text-[10px] font-bold text-sky-500 dark:text-sky-400 uppercase tracking-widest hover:text-sky-700 dark:hover:text-sky-300 hover:underline transition-colors">
                                                    {{ $sp->getTranslation('name', app()->getLocale()) }}@if(!$loop->last) <span class="mx-1 text-gray-300 dark:text-gray-600">•</span> @endif
                                                </a>
                                            @endforeach
                                        </div>

                                        {{-- Rating (only shown when reviews exist) --}}
                                        @if($doctor->approvedReviews->count() > 0)
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
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm text-gray-600 dark:text-gray-400">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-4 h-4 text-sky-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-0.5">{{ __('Qualifications') }}</span>
                                                <span class="leading-relaxed font-semibold text-gray-800 dark:text-gray-200 line-clamp-1">{{ $doctor->qualifications }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <svg class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-0.5">{{ __('Location') }}</span>
                                                <span class="leading-relaxed font-semibold text-gray-800 dark:text-gray-200">{{ $doctor->chambers->first()?->area?->getTranslation('name', app()->getLocale()) }}, {{ $doctor->chambers->first()?->area?->district?->getTranslation('name', app()->getLocale()) }}</span>
                                            </div>
                                        </div>
                                        
                                        @if($doctor->experience_years > 0)
                                        <div class="flex items-start gap-2 sm:col-span-2 mt-1">
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
                                
                                {{-- Call to Action & Pricing --}}
                                <div class="flex flex-col justify-center items-start md:items-end w-full md:w-48 flex-shrink-0 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-700/50 pt-4 md:pt-0 md:pl-6 h-full min-h-[120px]">
                                    <a href="{{ route('doctors.show', $doctor->slug) }}"
                                       class="w-full text-center px-4 py-2.5 rounded-xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 font-bold text-sm transition-all hover:bg-sky-500 hover:text-white dark:hover:bg-sky-500 dark:hover:text-white active:scale-95 border border-sky-100 dark:border-sky-800">
                                        {{ __('Book Appointment') }} &rarr;
                                    </a>
                                </div>
                            </div>
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
                    {{ $doctors->links() }}
                </div>
            @else
                <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="text-6xl mb-6">🏜️</div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">{{ __('No Specialists Found') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">{{ __('We couldn\'t find any doctors matching your exact criteria. Try removing some filters or checking back later.') }}</p>
                    <button wire:click="clearFilters" class="px-8 py-3.5 rounded-2xl bg-sky-500 text-white font-bold hover:bg-sky-600 transition-all shadow-lg shadow-sky-500/20 active:scale-95">
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
                <div class="bg-gradient-to-br from-sky-50 to-indigo-50 dark:from-sky-900/20 dark:to-indigo-900/20 rounded-2xl border border-sky-100 dark:border-sky-800/50 p-6 flex flex-col items-center justify-center text-center h-[350px] shadow-sm relative overflow-hidden group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-sky-500 bg-white/80 dark:bg-gray-900/80 backdrop-blur px-2 py-0.5 rounded shadow-sm">Sponsored</span>
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mb-3 shadow-md group-hover:-translate-y-1 transition-transform">
                        <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">{{ __('Grow Your Practice') }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Premium placement for hospitals & doctors.') }}</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
</div>