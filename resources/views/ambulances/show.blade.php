@extends('layouts.app')
@section('title', $ambulance->provider_name . ' — Ambulance Service | DoctorBD24')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-12">
    
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-red-500">Home</a>
        <span>›</span>
        <a href="{{ route('ambulances.index') }}" class="hover:text-red-500">Ambulances</a>
        <span>›</span>
        <span class="text-gray-600 dark:text-gray-300">{{ $ambulance->provider_name }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">
        
        {{-- Left Column: Main Profile --}}
        <div class="w-full lg:w-3/4 xl:w-4/5 space-y-6">
            
            {{-- Header Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative">
                <div class="h-48 md:h-64 bg-gradient-to-r from-red-500 to-rose-600 relative overflow-hidden">
                    @if($ambulance->cover_image)
                        <img src="{{ asset('storage/' . $ambulance->cover_image) }}" alt="{{ $ambulance->provider_name }} Cover" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/10"></div>
                    @endif
                </div>
                
                <div class="p-6 md:p-8 pt-0 -mt-12 flex flex-col md:flex-row gap-6 relative z-10">
                    {{-- Icon/Avatar --}}
                    <div class="w-24 h-24 rounded-2xl bg-white dark:bg-gray-900 border-4 border-white dark:border-gray-800 shadow-md flex items-center justify-center text-4xl flex-shrink-0 overflow-hidden">
                        @if($ambulance->logo)
                            <img src="{{ asset('storage/' . $ambulance->logo) }}" alt="{{ $ambulance->provider_name }}" class="w-full h-full object-cover">
                        @else
                            🚑
                        @endif
                    </div>
                    
                    <div class="flex-1 mt-2 md:mt-14">
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">{{ $ambulance->provider_name }}</h1>
                            @php $typeMap = \App\Models\Ambulance::typeMap(); @endphp
                            @forelse((array)$ambulance->type as $t)
                                @php 
                                    $label = isset($typeMap[$t]) ? __($typeMap[$t]) : __('Ambulance');
                                    $bg = 'bg-gray-50 dark:bg-gray-800';
                                    $text = 'text-gray-600 dark:text-gray-300';
                                    $border = 'border-gray-200 dark:border-gray-700';
                                    
                                    if (in_array($t, ['icu', 'ccu', 'ventilator', 'neonatal', 'als'])) {
                                        $bg = 'bg-red-50 dark:bg-red-900/30';
                                        $text = 'text-red-600 dark:text-red-400';
                                        $border = 'border-red-200 dark:border-red-800';
                                    } elseif (in_array($t, ['ac', 'air', 'boat'])) {
                                        $bg = 'bg-sky-50 dark:bg-sky-900/30';
                                        $text = 'text-sky-600 dark:text-sky-400';
                                        $border = 'border-sky-200 dark:border-sky-800';
                                    } elseif (in_array($t, ['freezing'])) {
                                        $bg = 'bg-indigo-50 dark:bg-indigo-900/30';
                                        $text = 'text-indigo-600 dark:text-indigo-400';
                                        $border = 'border-indigo-200 dark:border-indigo-800';
                                    }
                                @endphp
                                <a href="{{ route('ambulances.show', $t) }}" class="inline-block px-2.5 py-1 text-[10px] sm:text-xs font-bold uppercase tracking-wider rounded-lg border {{ $bg }} {{ $text }} {{ $border }} hover:scale-105 hover:shadow-md hover:bg-opacity-80 transition-all whitespace-nowrap shadow-sm">
                                    {{ $label }}
                                </a>
                            @empty
                                <span class="px-2.5 py-1 text-[10px] sm:text-xs font-bold uppercase tracking-wider rounded-lg border bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-700 whitespace-nowrap shadow-sm">
                                    {{ __('Ambulance') }}
                                </span>
                            @endforelse
                            @if($ambulance->is_verified)
                                <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 border border-blue-200 dark:border-blue-800">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Verified
                                </span>
                            @endif
                            @if($ambulance->available_24h)
                                <span class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1.5 border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    24/7 Available
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mt-4">
                            @if($ambulance->average_rating > 0)
                                <div class="flex items-center gap-1.5 bg-yellow-50 dark:bg-yellow-900/20 px-3 py-1 rounded-full border border-yellow-200 dark:border-yellow-800/50">
                                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span class="font-bold text-yellow-700 dark:text-yellow-500">{{ $ambulance->average_rating }}</span>
                                    <span class="text-xs text-gray-500">({{ $ambulance->reviews()->count() }} {{ \Illuminate\Support\Str::plural('Review', $ambulance->reviews()->count()) }})</span>
                                </div>
                            @endif
                            @if($ambulance->area)
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    {{ $ambulance->area->getTranslation('name', app()->getLocale()) }}, {{ $ambulance->area->district->getTranslation('name', app()->getLocale()) }}
                                </div>
                            @endif
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ $ambulance->view_count }} Views
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Details Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- About / Address --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-600">📍</span>
                        {{ __('Location & Info') }}
                    </h2>
                    
                    @if($ambulance->address)
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Head Office / Stand') }}</p>
                            <p class="text-gray-800 dark:text-gray-200 mt-1">{{ $ambulance->address }}</p>
                        </div>
                    @endif
                    
                    @if($ambulance->summary)
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Short Summary') }}</p>
                            <p class="text-gray-800 dark:text-gray-200 mt-1 text-sm leading-relaxed font-bold">{{ $ambulance->summary }}</p>
                        </div>
                    @endif
                    
                    @if($ambulance->notes)
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</p>
                            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 mt-1 leading-relaxed">
                                @if(strip_tags($ambulance->notes) === $ambulance->notes)
                                    {!! nl2br(e($ambulance->notes)) !!}
                                @else
                                    {!! $ambulance->notes !!}
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Contact Box --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-500 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('Emergency Contact') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">{{ __('Call this number directly for booking or emergency medical transport.') }}</p>
                    <div class="w-full flex flex-col gap-3">
                        <a href="tel:{{ $ambulance->phone }}" class="w-full text-center px-6 py-4 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white font-black text-lg sm:text-xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all active:scale-95 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $ambulance->phone }}
                        </a>
                        
                        @if($ambulance->backup_phone)
                            <a href="tel:{{ $ambulance->backup_phone }}" class="w-full text-center px-6 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 font-bold flex items-center justify-center gap-2 transition-all">
                                {{ $ambulance->backup_phone }}
                            </a>
                        @endif

                        @if($ambulance->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ambulance->whatsapp) }}" target="_blank" class="w-full text-center px-6 py-3 rounded-xl bg-[#25D366]/10 hover:bg-[#25D366]/20 text-[#25D366] border border-[#25D366]/30 font-bold flex items-center justify-center gap-2 transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Map/Location --}}
            @if($ambulance->latitude && $ambulance->longitude)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mt-6">
                    <div class="p-6 pb-0 mb-4">
                        <h2 class="font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600">📌</span>
                            {{ __('Map Location') }}
                        </h2>
                    </div>
                    <div class="w-full h-[300px] bg-gray-100 dark:bg-gray-800 relative z-0">
                        <iframe width="100%" height="100%" style="border:0;" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.com/maps?q={{ $ambulance->latitude }},{{ $ambulance->longitude }}&hl=en&z=15&amp;output=embed"></iframe>
                    </div>
                </div>
            @endif

            {{-- Photo Gallery --}}
            @if(!empty($ambulance->gallery))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mt-6">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-fuchsia-50 dark:bg-fuchsia-900/30 flex items-center justify-center text-fuchsia-600">🖼️</span>
                    {{ __('Photo Gallery') }}
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($ambulance->gallery as $image)
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

            {{-- Features & Amenities --}}
            @if(is_array($ambulance->features) && count($ambulance->features) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mt-6">
                    <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600">⚡</span>
                        {{ __('Features & Amenities') }}
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6">
                        @foreach($ambulance->features as $feature)
                        <div class="flex items-start gap-2.5">
                            <div class="mt-0.5 w-5 h-5 rounded-full bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0 text-indigo-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Reviews --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6 mt-8">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">⭐</span>
                    {{ __('Reviews & Ratings') }} ({{ $ambulance->approvedReviews->count() }})
                </h2>
                
                {{-- Rating Summary --}}
                <div class="flex items-center gap-4 mb-6 p-4 bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-100 dark:border-amber-800/30">
                    <div class="text-3xl font-black text-amber-700 dark:text-amber-400">{{ number_format($ambulance->average_rating, 1) }}</div>
                    <div>
                        <div class="flex mb-1">
                            @for($i=1; $i<=5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $ambulance->average_rating ? 'text-amber-400' : ($i-0.5 <= $ambulance->average_rating ? 'text-amber-300' : 'text-gray-300 dark:text-gray-600') }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="text-xs text-amber-700 dark:text-amber-400 font-bold">{{ $ambulance->approvedReviews->count() }} {{ __('reviews') }}</p>
                    </div>
                </div>
                
                @if($ambulance->approvedReviews->count())
                <div class="space-y-3 mb-6">
                    @foreach($ambulance->approvedReviews->take(5) as $review)
                    <div class="flex gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/40">
                        <div class="w-9 h-9 rounded-full bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center text-sm font-bold text-sky-600 flex-shrink-0">
                            {{ mb_substr($review->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $review->user->name }}</span>
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">{{ $review->comment }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400 mb-4">{{ __('No reviews yet.') }}</p>
                @endif
                {{-- Submit review --}}
                @auth
                @if(!auth()->user()->hasAnyRole(['admin', 'doctor', 'hospital', 'editor', 'moderator']))
                <form action="{{ route('reviews.store') }}" method="POST" class="border-t border-gray-100 dark:border-gray-700 pt-4 space-y-3">
                    @csrf
                    <input type="hidden" name="type" value="ambulance">
                    <input type="hidden" name="id" value="{{ $ambulance->id }}">
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-1">{{ __('Give a Rating') }}</label>
                        <div x-data="{ rating: 0 }" class="flex gap-1">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button" @click="rating = {{ $i }}" class="text-3xl transition-transform hover:scale-110">
                                <span x-text="rating >= {{ $i }} ? '⭐' : '☆'"></span>
                            </button>
                            @endfor
                            <input type="hidden" name="rating" x-bind:value="rating">
                        </div>
                    </div>
                    <textarea name="comment" rows="2" placeholder="{{ __('Write your review (optional)...') }}"
                              class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"></textarea>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white text-sm font-medium transition-colors">{{ __('Submit Review') }}</button>
                </form>
                @endif
                @else
                <p class="text-xs text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-4">{{ __('To submit a review') }} <a href="{{ route('login') }}" class="text-red-500 hover:underline">{{ __('Please login') }}</a></p>
                @endauth
            </div>

            {{-- Related Ambulances --}}
            @if($related->count())
            <div class="mt-8">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-4">{{ __('Other :type Ambulances', ['type' => $ambulance->getTypeLabel()]) }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($related as $rel)
                        <a href="{{ route('ambulances.show', $rel->slug) }}" class="flex items-center gap-4 p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                            <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🚑</div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm group-hover:text-red-500 transition-colors">{{ $rel->provider_name }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $rel->area?->getTranslation('name', app()->getLocale()) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Right Column: Ads / Sponsors (25%) --}}
        <div class="w-full lg:w-1/4 xl:w-1/5">
            <div class="sticky top-24 space-y-6">
                @if(is_null($ambulance->user_id))
                    <div class="bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 rounded-3xl border border-red-100 dark:border-red-800/50 p-6 shadow-sm text-center relative overflow-hidden mb-5">
                        {{-- Decorative background --}}
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-200/50 dark:bg-red-800/30 rounded-full blur-xl pointer-events-none"></div>
                        <div class="absolute -left-6 -bottom-6 w-24 h-24 bg-rose-200/50 dark:bg-rose-800/30 rounded-full blur-xl pointer-events-none"></div>
                        
                        <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-3 shadow-md text-red-500 relative z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <h4 class="font-black text-gray-900 dark:text-white text-lg mb-1 relative z-10">{{ __('Are you the Owner?') }}</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mb-5 relative z-10 px-2">{{ __('Claim this ambulance profile to easily manage listings, update information, and directly reach your patients.') }}</p>

                        <div class="relative z-10">
                        @auth
                            @php
                                $hasPendingClaim = auth()->user()->claimRequests()->where('ambulance_id', $ambulance->id)->where('status', 'pending')->exists();
                            @endphp
                            @if($hasPendingClaim)
                                <div class="w-full py-3 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 text-sm font-bold border border-amber-200 dark:border-amber-800/50 flex items-center justify-center gap-2 shadow-inner">
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ __('Claim Pending Approval') }}
                                </div>
                            @else
                                <div x-data="{ claimModal: false }">
                                    <button @click="claimModal = true" class="w-full py-3 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white text-sm font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                        {{ __('Claim This Ambulance') }}
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
                                                    <div class="w-14 h-14 rounded-2xl bg-red-100 dark:bg-red-900/40 text-red-600 flex items-center justify-center mb-5 shadow-sm border border-red-200 dark:border-red-800">
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                    </div>
                                                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">{{ __('Ambulance Claim Request') }}</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                                                        {{ __('Please provide a secure message detailing your professional identity. Include vehicle registration documents or authorization letter.') }}
                                                    </p>
                                                    
                                                    <form method="POST" action="{{ route('ambulances.claim', $ambulance->id) }}">
                                                        @csrf
                                                        <div class="mb-5">
                                                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">{{ __('Identity Proof / Message') }}</label>
                                                            <textarea name="message" rows="4" required placeholder="E.g., I am the owner of this ambulance. Registration No: XYZ-XXXX..." class="w-full border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors placeholder-gray-400"></textarea>
                                                        </div>
                                                        <div class="flex gap-3">
                                                            <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-bold py-3 rounded-xl shadow-md transition-all">{{ __('Submit Secure Request') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="w-full py-3 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white text-sm font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                {{ __('Claim This Ambulance') }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endauth
                        </div>
                    </div>
                @endif

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

    </div>
</div>
@endsection
