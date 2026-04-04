<div>
@if(!$hideFilters)
<div class="w-full bg-slate-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-inner relative z-20 mb-6 mt-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 md:py-4">
        
        <div class="mb-4 flex flex-col md:flex-row md:items-end justify-between gap-2">
            <h1 class="text-xl md:text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
                <span>🚑</span>
                @if($fixedType)
                    <span class="text-red-600">{{ $fixedType->name }}</span>
                @else
                    <span>{{ __('Emergency') }} <span class="text-red-600">{{ __('Ambulance') }}</span> {{ __('Service') }}</span>
                @endif
            </h1>
        </div>

        {{-- Search Bar --}}
        <div class="relative group mb-3" x-data="{ open: @entangle('showSuggestions') }" @click.away="open = false">
            <div class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       wire:model.live.debounce.400ms="search" 
                       wire:keydown.enter="applySearch"
                       placeholder="{{ __('Search Name, City or Category...') }}"
                       class="block w-full pl-11 pr-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all text-gray-900 dark:text-white text-sm md:text-base font-medium placeholder-gray-400"
                       @focus="open = true">
                <div wire:loading wire:target="search" class="absolute right-4">
                    <svg class="animate-spin h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
            </div>

            {{-- Suggestions Dropdown --}}
            <div x-show="open && $wire.suggestions.length > 0" 
                 x-transition
                 class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                 style="display: none;">
                @foreach($suggestions as $suggestion)
                    <button wire:click="selectSuggestion('{{ $suggestion }}')"
                            class="w-full text-left px-4 py-2 hover:bg-red-50 dark:hover:bg-gray-700/50 transition-colors flex items-center gap-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-gray-700 dark:text-gray-200 text-sm font-medium">{{ $suggestion }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-2 {{ $fixedType ? 'lg:grid-cols-4' : 'lg:grid-cols-5' }} gap-3 mb-2">
            {{-- Category --}}
            @if(!$fixedType)
            <div class="col-span-2 md:col-span-1 lg:col-span-1">
                <select wire:model.live="type" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach(\App\Models\AmbulanceType::where('is_active', true)->get() as $t)
                        <option value="{{ $t->slug }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Division --}}
            <div class="col-span-1">
                <select wire:model.live="division" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All Divisions') }}</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->slug }}">{{ $div->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- District --}}
            <div class="col-span-1" x-show="$wire.division" style="display: none;">
                <select wire:model.live="district" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All Districts') }}</option>
                    @foreach($districts as $dist)
                        <option value="{{ $dist->slug }}">{{ $dist->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Area --}}
            <div class="col-span-1" x-show="$wire.district" style="display: none;">
                <select wire:model.live="area" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 text-sm font-semibold transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                    <option value="">{{ __('All Areas') }}</option>
                    @foreach($areas as $ar)
                        <option value="{{ $ar->slug }}">{{ $ar->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 24/7 Only --}}
            <div class="col-span-2 lg:col-span-1 flex items-center lg:justify-end">
                <label class="flex items-center gap-2 cursor-pointer group bg-white dark:bg-gray-900 px-3 py-2 rounded-xl transition-colors border border-gray-200 dark:border-gray-700 shadow-sm lg:w-full lg:justify-center">
                    <input type="checkbox" wire:model.live="available24h" class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500 dark:bg-gray-800 transition-all">
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-red-600 transition-colors">🕐 24/7 <span class="hidden sm:inline">{{ __('Available') }}</span></span>
                </label>
            </div>
        </div>

        <div class="mt-4 flex justify-between items-center bg-gray-100 dark:bg-gray-900/50 p-2 rounded-xl">
            <div class="flex items-center gap-2">
                <button type="button" wire:click="$toggle('showMapView')" 
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center gap-2"
                        :class="@js($showMapView) ? 'bg-red-500 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-red-50'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    {{ __('Show Map') }}
                </button>
                <div x-data="{ loading: false, error: false }">
                    <button type="button" 
                            @click="loading = true; error = false; 
                                navigator.geolocation.getCurrentPosition(
                                    (pos) => { $wire.set('userLat', pos.coords.latitude); $wire.set('userLng', pos.coords.longitude); loading = false; }, 
                                    (err) => { error = true; loading = false; alert('Location access denied or unavailable.'); }
                                )"
                            class="px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center gap-2"
                            :class="@js((bool)$userLat) ? 'bg-blue-500 text-white shadow-blue-500/30' : 'bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 border border-gray-200 dark:border-gray-700'">
                        <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        {{ __('Near Me') }}
                    </button>
                </div>
            </div>

            @if($search || $type || $division || $district || $area || $available24h || $userLat)
            <button wire:click="clearFilters" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-900 border border-red-200 dark:border-red-900/50 text-red-500 text-xs font-bold hover:bg-red-50 dark:hover:bg-red-900/20 transition-all flex items-center gap-1 shadow-sm">
                ✕ {{ __('Clear Filters') }}
            </button>
            @endif
        </div>
    </div>
</div>
@endif

{{-- MAIN CONTENT WITH SIDEBAR --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="flex flex-col lg:flex-row gap-8">
        
        {{-- Left Column: Results --}}
        <div class="w-full {{ $showMapView ? 'lg:w-[50%]' : 'lg:w-3/4 xl:w-4/5' }}">
            
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
                <span class="text-gray-900 dark:text-white font-bold text-lg">{{ $ambulances->total() }}</span> available ambulances
            </p>
        </div>

        <div wire:loading.class="opacity-40 pointer-events-none transition-opacity duration-300" class="relative">
            @if($ambulances->count())
            <div class="space-y-4">
                @foreach($ambulances as $ambulance)
                <div class="group bg-white dark:bg-gray-800 rounded-[1.5rem] shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700 transition-all duration-300 w-full overflow-hidden flex flex-col sm:flex-row">
                    
                    {{-- Icon Container (Left Column) --}}
                    <a href="{{ route('ambulances.show', $ambulance->slug) }}" class="block w-full sm:w-48 bg-gray-50/50 dark:bg-gray-900/20 border-b sm:border-b-0 sm:border-r border-gray-100 dark:border-gray-700/50 p-6 flex flex-col items-center justify-center flex-shrink-0 hover:bg-gray-100 dark:hover:bg-gray-800/50 transition-colors">
                        <div class="w-20 h-20 rounded-full shadow-sm flex items-center justify-center text-4xl group-hover:scale-105 transition-transform duration-300 border-4
                            {{ in_array('icu', (array)$ambulance->type) ? 'bg-red-50 dark:bg-red-950 text-red-600 border-red-100 dark:border-red-900/30' : (in_array('ac', (array)$ambulance->type) ? 'bg-sky-50 dark:bg-sky-950 text-sky-600 border-sky-100 dark:border-sky-900/30' : 'bg-gray-50 dark:bg-gray-900 text-gray-500 border-gray-100 dark:border-gray-800') }}">
                            🚑
                        </div>
                        
                        @if($ambulance->available_24h)
                            <div class="mt-3 bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full px-2.5 py-0.5 flex items-center gap-1 border border-emerald-200 dark:border-emerald-800/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-[10px] font-bold tracking-widest uppercase">Live Now</span>
                            </div>
                        @endif
                    </a>

                    {{-- Details Content (Right Column) --}}
                    <div class="p-6 flex flex-col md:flex-row md:items-start justify-between gap-6 flex-1">
                        {{-- Left Side Info --}}
                        <div class="flex-1">
                            <a href="{{ route('ambulances.show', $ambulance->slug) }}" class="block">
                                <h3 class="font-black text-xl text-gray-900 dark:text-white group-hover:text-red-600 transition-colors leading-tight mb-1">{{ $ambulance->provider_name }}</h3>
                            </a>
                            
                            <div class="flex flex-wrap gap-1.5 mb-4">
                                @php $typeMap = \App\Models\Ambulance::typeMap(); @endphp
                                @forelse((array)$ambulance->type as $t)
                                    @php 
                                        $label = isset($typeMap[$t]) ? __($typeMap[$t]) : __('Ambulance');
                                        $bg = 'bg-gray-50 dark:bg-gray-900';
                                        $text = 'text-gray-500 dark:text-gray-400';
                                        $border = 'border-gray-100 dark:border-gray-800';
                                        
                                        if (in_array($t, ['icu', 'ccu', 'ventilator', 'neonatal', 'als'])) {
                                            $bg = 'bg-red-50 dark:bg-red-950'; $text = 'text-red-600 dark:text-red-400'; $border = 'border-red-100 dark:border-red-900';
                                        } elseif (in_array($t, ['ac', 'air', 'boat'])) {
                                            $bg = 'bg-sky-50 dark:bg-sky-950'; $text = 'text-sky-600 dark:text-sky-400'; $border = 'border-sky-100 dark:border-sky-900';
                                        } elseif (in_array($t, ['freezing'])) {
                                            $bg = 'bg-indigo-50 dark:bg-indigo-950'; $text = 'text-indigo-600 dark:text-indigo-400'; $border = 'border-indigo-100 dark:border-indigo-900';
                                        }
                                    @endphp
                                    <a href="{{ route('ambulances.show', $t) }}" class="inline-block px-2.5 py-1 text-[10px] sm:text-xs font-bold uppercase tracking-widest rounded-lg border {{ $bg }} {{ $text }} {{ $border }} hover:scale-105 hover:shadow-md hover:bg-opacity-80 transition-all whitespace-nowrap shadow-sm">
                                        {{ $label }}
                                    </a>
                                @empty
                                    <span class="inline-block px-2.5 py-1 text-[10px] sm:text-xs font-bold uppercase tracking-widest rounded-lg border bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 border-gray-100 dark:border-gray-800 whitespace-nowrap shadow-sm">
                                        {{ __('Ambulance') }}
                                    </span>
                                @endforelse

                                @if($ambulance->approvedReviews->count() > 0)
                                <div class="inline-flex items-center gap-1 bg-amber-50 dark:bg-amber-900/20 px-2.5 py-1 rounded-lg border border-amber-100 dark:border-amber-800/30">
                                    <div class="flex text-amber-400">
                                        @for($i=1; $i<=5; $i++)
                                            <svg class="w-2.5 h-2.5 {{ $i <= floor($ambulance->average_rating) ? 'fill-current' : 'text-gray-200 dark:text-gray-700' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-[10px] font-black text-amber-700 dark:text-amber-400">{{ $ambulance->average_rating }}</span>
                                    <span class="text-[9px] font-bold text-gray-400">({{ $ambulance->approvedReviews->count() }})</span>
                                </div>
                                @endif
                            </div>
                            
                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                @if($ambulance->area)
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        <span class="leading-relaxed">{{ $ambulance->area->getTranslation('name', app()->getLocale()) }}, {{ $ambulance->area->district->getTranslation('name', app()->getLocale()) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Right Side Action Button --}}
                        <div class="flex flex-col justify-center items-start md:items-end w-full md:w-48 flex-shrink-0 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-700/50 pt-4 md:pt-0 md:pl-6 h-full min-h-[120px]">
                            <a href="tel:{{ $ambulance->phone }}"
                               class="w-full text-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white font-bold text-sm shadow-sm hover:shadow-md active:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ __('Call Now') }}
                            </a>
                            <a href="{{ route('ambulances.show', $ambulance->slug) }}" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-bold tracking-wider uppercase text-center w-full transition-colors mt-3">
                                {{ __('View Details') }} &rarr;
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-12 bg-white dark:bg-gray-800 p-4 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                {{ $ambulances->links() }}
            </div>
            @else
            <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="text-6xl mb-6">🚑</div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">{{ __('No Ambulances Found') }}</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">{{ __('We couldn\'t find any ambulances matching your exact criteria in this location.') }}</p>
                <button wire:click="clearFilters" class="px-8 py-3.5 rounded-2xl bg-red-500 text-white font-bold hover:bg-red-600 transition-all shadow-lg shadow-red-500/20 active:scale-95">
                    {{ __('Reset All Filters') }}
                </button>
            </div>
            @endif
        </div>
    </div>

            @if($this->selectedType && $this->selectedType->content)
            <div class="mt-8 mb-4 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8 overflow-hidden">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-3 block">
                    {{ __('About') }} {{ $this->selectedType->name }} {{ __('Services') }}
                </h2>
                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed font-medium markup-content block text-sm sm:text-base break-words overflow-x-auto w-full">
                    {!! $this->selectedType->content !!}
                </div>
            </div>
            @endif

            @if($seoBottomContent)
                <div class="mt-8 mb-4 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed">
                        {!! $seoBottomContent !!}
                    </div>
                </div>
            @endif
        </div>

        {{-- Map View Column (Conditional) --}}
        @if($showMapView)
        <div class="hidden lg:block w-full lg:w-[50%] h-[calc(100vh-140px)] sticky top-24 z-10 rounded-[2rem] overflow-hidden shadow-inner border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800">
            <x-dynamic-map :locations="$this->mapLocations" />
        </div>
        @else
        {{-- Right Column: Ads / Sponsors (25%) --}}
        <div class="w-full lg:w-1/4 xl:w-1/5">
            <div class="sticky top-24 space-y-6">
                {{-- Ad Container 1 --}}
                <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 flex flex-col items-center justify-center text-center h-[250px] shadow-sm relative overflow-hidden group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-400 bg-white dark:bg-gray-900 px-2 py-0.5 rounded shadow-sm">Ad</span>
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8"/></svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ __('Sponsor Space Available') }}</p>
                    <p class="text-xs text-gray-400 mt-1 px-2">{{ __('Place your banner here to reach thousands of patients daily.') }}</p>
                </div>

                {{-- Ad Container 2 --}}
                <div class="bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 rounded-2xl border border-red-100 dark:border-red-800/50 p-6 flex flex-col items-center justify-center text-center h-[350px] shadow-sm relative overflow-hidden group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-red-500 bg-white/80 dark:bg-gray-900/80 backdrop-blur px-2 py-0.5 rounded shadow-sm">Sponsored</span>
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mb-3 shadow-md group-hover:-translate-y-1 transition-transform">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">{{ __('Grow Your Service') }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Premium placement for ambulances.') }}</p>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>