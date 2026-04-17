@extends('layouts.app')
@section('title', $doctor->name . ' — ' . $doctor->designation . ' | DoctorBD24')
@section('meta_description', $doctor->name . ' — ' . $doctor->qualifications . ' — ' . $doctor->designation)

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Physician",
  "name": "{{ $doctor->name }}",
  "description": "{{ $doctor->bio }}",
  "medicalSpecialty": "{{ $doctor->specialties->pluck('name')->map(fn($n) => $n['en'] ?? $n)->implode(', ') }}"
}
</script>
@endsection

@section('content')
<style>
    @media (min-width: 768px) {
        .layout-wrapper { display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; gap: 2rem !important; align-items: flex-start !important; width: 100% !important; }
        .main-col { flex: 0 0 70% !important; max-width: 70% !important; width: 70% !important; }
        .side-col { flex: 0 0 30% !important; max-width: 30% !important; width: 30% !important; }
    }
</style>

<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-12">

    {{-- Breadcrumb --}}
    <nav class="flex items-center justify-between gap-2 mb-6 w-full">
        <div class="flex items-center gap-2 text-xs text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-sky-500">Home</a>
            <span>›</span>
            <a href="{{ route('doctors.index') }}" class="hover:text-sky-500">Doctors</a>
            <span>›</span>
            <span class="text-gray-600 dark:text-gray-300">{{ $doctor->name }}</span>
        </div>
        <div class="text-[10px] text-gray-400 font-medium hidden sm:block" title="Profile Last Updated">
            {{ __('Updated:') }} {{ $doctor->updated_at->format('d M, Y') }}
        </div>
    </nav>

    <div class="layout-wrapper flex flex-col w-full">

        {{-- ── MAIN CONTENT (70%) --}}
        <div class="main-col w-full space-y-5">

            {{-- Header Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                {{-- Banner --}}
                <div class="h-48 md:h-64 bg-gradient-to-r from-sky-500 via-sky-400 to-indigo-500 relative overflow-hidden">
                    @if($doctor->cover_image)
                        <img loading="lazy" decoding="async" src="{{ asset('storage/' . $doctor->cover_image) }}" alt="{{ $doctor->name }} Cover" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/10"></div>
                    @else
                        {{-- Subtle Pattern Texture --}}
                        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.4\" fill-rule=\"evenodd\"%3E%3Ccircle cx=\"3\" cy=\"3\" r=\"3\"/%3E%3Ccircle cx=\"13\" cy=\"13\" r=\"3\"/%3E%3C/g%3E%3C/svg%3E');"></div>
                    @endif
                    
                    <div class="absolute top-4 right-4 flex gap-2 z-20">
                        {{-- Favorite Toggle (Premium SVG) --}}
                        @auth
                        <div x-data="{ saved: false }" x-init="
                             fetch('{{ route('favorites.check') }}?type=doctor&id={{ $doctor->id }}')
                             .then(r => r.json()).then(d => saved = d.saved)
                        ">
                            <button @click="
                                fetch('{{ route('favorites.toggle') }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({ type: 'doctor', id: {{ $doctor->id }} })
                                }).then(r => r.json()).then(d => saved = d.saved)
                            "
                            class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white hover:bg-white/40 transition-all shadow-sm border border-white/30"
                            :title="saved ? '{{ __('Remove from Favorites') }}' : '{{ __('Add to Favorites') }}'">
                                <svg x-cloak x-show="!saved" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                <svg x-cloak x-show="saved" class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </button>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white hover:bg-white/40 transition-all shadow-sm border border-white/30" title="{{ __('Login to Save') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </a>
                        @endauth

                        {{-- Share --}}
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"
                           class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white hover:bg-white/30 transition-colors" title="Share on Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($doctor->name . ' — ' . url()->current()) }}" target="_blank"
                           class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white hover:bg-white/30 transition-colors" title="Share on WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.558 4.12 1.533 5.845L0 24l6.335-1.51A11.957 11.957 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.935 0-3.748-.523-5.306-1.435L2.2 21.8l1.268-4.41A9.937 9.937 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Header Content --}}
                <div class="p-6 md:p-8 pt-0 -mt-12 flex flex-col md:flex-row gap-6 relative z-10">
                    {{-- Avatar --}}
                    <div class="w-24 h-24 rounded-2xl bg-white dark:bg-gray-900 border-4 border-white dark:border-gray-800 shadow-xl flex items-center justify-center flex-shrink-0 overflow-hidden">
                        @if($doctor->photo)
                            <img loading="lazy" decoding="async" src="{{ asset('storage/'.$doctor->photo) }}" alt="{{ $doctor->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-sky-50 dark:bg-sky-900/30 flex flex-col items-center justify-center text-sky-400">
                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                <span class="text-[10px] font-bold uppercase tracking-widest mt-0.5">{{ mb_substr($doctor->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 mt-2 md:mt-14">
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-tight">{{ $doctor->name }}</h1>
                            @if($doctor->verified)
                                <span class="flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[10px] uppercase tracking-wider font-black border border-green-200 dark:border-green-800 shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    Verified
                                </span>
                            @endif
                            @if($doctor->featured)
                                <span class="px-3 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] uppercase tracking-wider font-black border border-amber-200 dark:border-amber-800 shadow-sm">⭐ Featured</span>
                            @endif
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-base font-semibold group-hover:text-sky-600 transition-colors">{{ $doctor->designation }}</p>
                        @if($doctor->qualifications)
                            <p class="text-[11px] text-sky-700 dark:text-sky-400 font-bold mt-1 opacity-80 border-l-2 border-sky-300 dark:border-sky-700 pl-2 leading-tight">{{ $doctor->qualifications }}</p>
                        @endif

                        {{-- Compact Stats Row (Fixed Alignment) --}}
                        <div class="flex flex-wrap items-center gap-y-2 gap-x-5 mt-4">
                            <div class="flex items-center gap-1.5" title="{{ __('Experience') }}">
                                <span class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center text-sky-600 text-xs leading-none">📅</span>
                                <span class="text-[13px] font-black text-gray-700 dark:text-gray-200 leading-none">{{ $doctor->experience_years }}+</span>
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-tighter leading-none">{{ __('Years') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5" title="{{ __('Rating') }}">
                                <span class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-500 text-xs leading-none">⭐</span>
                                <span class="text-[13px] font-black text-gray-700 dark:text-gray-200 leading-none">{{ $doctor->average_rating ?: '—' }}</span>
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-tighter leading-none">{{ __('Rating') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5" title="{{ __('Views') }}">
                                <span class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-500 text-xs leading-none">👁️</span>
                                <span class="text-[13px] font-black text-gray-700 dark:text-gray-200 leading-none">{{ $doctor->view_count }}</span>
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-tighter leading-none">{{ __('Views') }}</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-6">
                            @foreach($doctor->specialties as $sp)
                                <a href="{{ route('doctors.index', ['specialty' => $sp->slug]) }}" class="px-3 py-1.5 rounded-xl bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-300 text-[11px] font-black uppercase tracking-tight hover:bg-sky-500 hover:text-white border border-sky-100 dark:border-sky-800 transition-all shadow-sm">
                                    {{ $sp->icon }} {{ $sp->getTranslation('name', app()->getLocale()) }}
                                </a>
                            @endforeach
                        </div>

                        @if($doctor->facebook_url || $doctor->twitter_url || $doctor->linkedin_url || $doctor->youtube_url || $doctor->instagram_url)
                        <div class="flex flex-wrap gap-2 mt-4">
                            @if($doctor->facebook_url)
                                <a href="{{ $doctor->facebook_url }}" target="_blank" class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors shadow-sm" title="Facebook">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                                </a>
                            @endif
                            @if($doctor->twitter_url)
                                <a href="{{ $doctor->twitter_url }}" target="_blank" class="w-8 h-8 rounded-full bg-sky-50 dark:bg-sky-900/20 flex items-center justify-center text-sky-500 hover:bg-sky-100 dark:hover:bg-sky-900/40 transition-colors shadow-sm" title="Twitter / X">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                </a>
                            @endif
                            @if($doctor->linkedin_url)
                                <a href="{{ $doctor->linkedin_url }}" target="_blank" class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-700 dark:text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors shadow-sm" title="LinkedIn">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                </a>
                            @endif
                            @if($doctor->youtube_url)
                                <a href="{{ $doctor->youtube_url }}" target="_blank" class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors shadow-sm" title="YouTube">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            @endif
                            @if($doctor->instagram_url)
                                <a href="{{ $doctor->instagram_url }}" target="_blank" class="w-8 h-8 rounded-full bg-pink-50 dark:bg-pink-900/20 flex items-center justify-center text-pink-600 dark:text-pink-400 hover:bg-pink-100 dark:hover:bg-pink-900/40 transition-colors shadow-sm" title="Instagram">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            {{-- END Profile Header Card --}}

            {{-- SPA TABS COMPONENT --}}
            <div x-data="doctorTabs('{{ $tab ?? 'overview' }}', '{{ $doctor->slug }}')" class="w-full">
            @php
                $hasVideos = isset($doctor) && $doctor->doctorVideos->count() > 0;
                $hasBlogs = isset($doctor) && !empty($doctor->blogs);
                $showTabs = $hasVideos || $hasBlogs;
            @endphp
            @if($showTabs)
                {{-- TAB NAVIGATION BAR --}}
                <div class="flex items-center gap-2 mb-8 border-b border-gray-100 dark:border-gray-700 pb-px overflow-x-auto hide-scrollbar sticky top-[calc(var(--nav-height,0px))] z-40 bg-gray-50/80 dark:bg-gray-900/80 backdrop-blur-md pt-2">
                    <button @click.prevent="switchTab('overview')" 
                       :class="currentTab === 'overview' ? 'border-sky-500 text-sky-600 dark:text-sky-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                       class="px-5 py-3 text-sm font-bold border-b-2 transition-colors whitespace-nowrap outline-none flex items-center gap-2">
                        👨‍⚕️ {{ __('Overview & Info') }}
                    </button>
                    @if($hasVideos)
                    <button @click.prevent="switchTab('videos')" 
                       :class="currentTab === 'videos' ? 'border-sky-500 text-sky-600 dark:text-sky-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                       class="px-5 py-3 text-sm font-bold border-b-2 transition-colors whitespace-nowrap outline-none flex items-center gap-2">
                        🎥 {{ __('Video') }}
                        <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 py-0.5 px-2 rounded-full text-[10px]">{{ $doctor->doctorVideos->count() }}</span>
                    </button>
                    @endif
                    @if($hasBlogs)
                    <button @click.prevent="switchTab('blog')" 
                       :class="currentTab === 'blog' ? 'border-sky-500 text-sky-600 dark:text-sky-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                       class="px-5 py-3 text-sm font-bold border-b-2 transition-colors whitespace-nowrap outline-none flex items-center gap-2">
                        📝 {{ __('Blog') }}
                        <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 py-0.5 px-2 rounded-full text-[10px]">{{ count($doctor->blogs) }}</span>
                    </button>
                    @endif
                </div>
            @endif

            <div x-show="currentTab === 'overview'" style="{{ ($tab ?? 'overview') === 'overview' ? '' : 'display: none;' }}" x-transition.opacity.duration.300ms class="space-y-5">

            {{-- Bio --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="font-black text-gray-900 dark:text-white text-lg mb-3 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center text-sky-600 text-sm">ℹ</span>
                    {{ __('About Member') }}
                </h2>
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed px-1">
                    @if(empty($doctor->bio))
                        {{ __('No bio available') }}
                    @elseif(strip_tags($doctor->bio) === $doctor->bio)
                        {!! nl2br(e($doctor->bio)) !!}
                    @else
                        {!! $doctor->bio !!}
                    @endif
                </div>
            </div>

            {{-- Photo Gallery --}}
            @if(!empty($doctor->gallery))
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6"
                 x-data="{ 
                    lightboxOpen: false, 
                    activeImage: 0, 
                    images: {{ json_encode(array_map(fn($img) => asset('storage/' . $img), $doctor->gallery)) }}
                 }"
                 @keydown.window.escape="lightboxOpen = false"
                 @keydown.window.right="if(lightboxOpen) { activeImage = (activeImage + 1) % images.length }"
                 @keydown.window.left="if(lightboxOpen) { activeImage = (activeImage - 1 + images.length) % images.length }">
                 
                <h2 class="font-black text-gray-900 dark:text-white text-lg mb-4 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-fuchsia-100 dark:bg-fuchsia-900/30 flex items-center justify-center text-fuchsia-600 text-sm">🖼️</span>
                    {{ __('Photo Gallery') }}
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach($doctor->gallery as $index => $image)
                        <button type="button" @click="activeImage = {{ $index }}; lightboxOpen = true" class="block w-full aspect-square rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group/gallery relative focus:outline-none focus:ring-2 focus:ring-fuchsia-500">
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

            {{-- Chambers --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 font-bold">🏥</span>
                    {{ __('Chamber / Hospital') }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($doctor->chambers as $chamber)
                    <div class="group flex flex-col p-5 rounded-2xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 hover:bg-white dark:hover:bg-gray-800 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                        {{-- Decorative Top Line --}}
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-400 to-teal-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        {{-- Header Row --}}
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl justify-center items-center flex bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex-shrink-0 shadow-sm group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-emerald-600 transition-colors leading-tight">
                                    @if($chamber->hospital)
                                        <a href="{{ route('hospitals.show', $chamber->hospital->slug) }}" class="hover:underline">
                                            {{ $chamber->hospital->name }}
                                        </a>
                                    @else
                                        {{ $chamber->name }}
                                    @endif
                                </h3>
                                @if($chamber->name && $chamber->hospital && $chamber->name !== $chamber->hospital->name)
                                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium mt-0.5 truncate">{{ $chamber->name }}</p>
                                @endif
                                {{-- Chamber Type Badge --}}
                                <div class="mt-2">
                                    <span class="px-2 py-1 rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 text-xs font-bold uppercase">
                                        {{ $chamber->is_main ? 'Main Chamber' : 'Visiting Chamber' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Details List --}}
                        <div class="space-y-3 flex-1 mb-5">
                            {{-- Address --}}
                            <div class="flex items-start gap-2.5">
                                <div class="w-5 flex justify-center mt-0.5">
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-semibold text-gray-600 dark:text-gray-300 leading-snug">{{ __('Address') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $chamber->address ?: 'N/A' }}</p>
                                </div>
                            </div>

                            {{-- Visiting Hours --}}
                            <div class="flex items-start gap-2.5">
                                <div class="w-5 flex justify-center mt-0.5">
                                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-semibold text-gray-600 dark:text-gray-300 leading-snug">{{ __('Visiting Hours') }}</p>
                                    <p class="text-xs text-sky-600 dark:text-sky-400 font-bold mt-0.5 bg-sky-50 dark:bg-sky-900/20 inline-block px-1.5 py-0.5 rounded">{{ $chamber->visiting_hours ? format_bangla_time($chamber->visiting_hours) : 'N/A' }}</p>
                                </div>
                            </div>
                            
                            {{-- Closed Days --}}
                            @if($chamber->closed_days)
                            <div class="flex items-start gap-2.5">
                                <div class="w-5 flex justify-center mt-0.5">
                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-semibold text-gray-600 dark:text-gray-300 leading-snug">{{ __('Closed Days') }}</p>
                                    <p class="text-xs text-red-500 dark:text-red-400 font-bold mt-0.5">{{ $chamber->closed_days }}</p>
                                </div>
                            </div>
                            @endif

                            {{-- Phone --}}
                            @if($chamber->phone)
                            <div class="flex items-start gap-2.5">
                                <div class="w-5 flex justify-center mt-0.5">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-semibold text-gray-600 dark:text-gray-300 leading-snug">{{ __('Contact Provider') }}</p>
                                    <a href="tel:{{ $chamber->phone }}" class="text-xs text-emerald-600 dark:text-emerald-400 font-black mt-0.5 hover:underline inline-block">{{ $chamber->phone }}</a>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Footer Action --}}
                        <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ $chamber->google_maps_url ?: 'https://www.google.com/maps/search/?api=1&query=' . urlencode(($chamber->hospital ? $chamber->hospital->name : $chamber->name) . ' ' . $chamber->address) }}" 
                               target="_blank"
                               class="w-full justify-center px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-xs font-bold text-gray-600 dark:text-gray-300 hover:text-white hover:bg-emerald-600 hover:border-emerald-600 dark:hover:bg-emerald-500 transition-all flex items-center gap-2 shadow-sm bg-white dark:bg-gray-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                {{ __('Find on Map') }}
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Reviews --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">⭐</span>
                    {{ __('Reviews & Ratings') }} ({{ $doctor->approvedReviews->count() }})
                </h2>
                
                {{-- Rating Summary --}}
                <div class="flex items-center gap-4 mb-6 p-4 bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-100 dark:border-amber-800/30">
                    <div class="text-3xl font-black text-amber-700 dark:text-amber-400">{{ number_format($doctor->average_rating, 1) }}</div>
                    <div>
                        <div class="flex mb-1">
                            @for($i=1; $i<=5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $doctor->average_rating ? 'text-amber-400' : ($i-0.5 <= $doctor->average_rating ? 'text-amber-300' : 'text-gray-300 dark:text-gray-600') }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="text-xs text-amber-700 dark:text-amber-400 font-bold">{{ $doctor->approvedReviews->count() }} {{ __('reviews') }}</p>
                    </div>
                </div>
                
                @if($doctor->approvedReviews->count())
                <div class="space-y-3 mb-6">
                    @foreach($doctor->approvedReviews->take(5) as $review)
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
                            
                            {{-- Share Buttons --}}
                            <div class="flex gap-2 mt-3">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}&quote={{ urlencode($review->comment) }}" target="_blank" class="w-6 h-6 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($review->comment) }}&url={{ urlencode(url()->current()) }}" target="_blank" class="w-6 h-6 rounded-full bg-sky-50 dark:bg-sky-900/20 flex items-center justify-center text-sky-500 hover:bg-sky-100 dark:hover:bg-sky-900/30 transition-colors">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                </a>
                            </div>
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
                    <input type="hidden" name="type" value="doctor">
                    <input type="hidden" name="id" value="{{ $doctor->id }}">
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
                              class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 resize-none"></textarea>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium transition-colors">{{ __('Submit Review') }}</button>
                </form>
                @endif
                @else
                <p class="text-xs text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-4">{{ __('To submit a review') }} <a href="{{ route('login') }}" class="text-sky-500 hover:underline">{{ __('Please login') }}</a></p>
                @endauth
            </div>

            </div>{{-- END OVERVIEW TAB --}}

            @if(isset($doctor) && $doctor->doctorVideos->count() > 0)
            {{-- TAB CONTENT: VIDEO --}}
            <div x-show="currentTab === 'videos'" style="{{ ($tab ?? 'overview') === 'videos' ? '' : 'display: none;' }}" x-transition.opacity.duration.300ms x-cloak>
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8 mb-8 mt-4">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2 mb-6">
                        <span class="bg-red-100 dark:bg-red-900/30 text-red-500 w-8 h-8 rounded-full flex items-center justify-center text-lg">🎥</span>
                        {{ __('Doctor Videos') }}
                    </h3>
                    
                    <div x-data="{ limit: 12 }" class="flex flex-col gap-4">
                        @foreach($doctor->doctorVideos as $index => $video)
                        @php
                            $isFacebook = str_contains(strtolower($video->video_url ?? $video->url ?? ''), 'facebook.com') || str_contains(strtolower($video->video_url ?? $video->url ?? ''), 'fb.watch');
                            $videoHref = $isFacebook ? ($video->video_url ?? $video->url ?? '#') : route('doctor.video.show', ['doctor_slug' => $doctor->slug, 'video_slug' => $video->slug]);
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
                                @if($video->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">{{ $video->description }}</p>
                                @endif
                                <p class="text-xs text-gray-500 flex items-center justify-center sm:justify-start gap-1">
                                    @if($doctor->photo)
                                        <img src="{{ Storage::url($doctor->photo) }}" class="w-4 h-4 rounded-full object-cover">
                                    @endif
                                    {{ $doctor->name }} &bull; {{ $video->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                        @endforeach
                        
                        @if($doctor->doctorVideos->count() > 12)
                        <div class="text-center mt-6">
                            <button type="button" x-show="limit < {{ $doctor->doctorVideos->count() }}" @click.prevent="limit += 12" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                                <span>Load More Videos</span>
                                <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if(isset($doctor) && !empty($doctor->blogs) && count($doctor->blogs) > 0)
            {{-- TAB CONTENT: BLOG --}}
            <div x-show="currentTab === 'blog'" style="{{ ($tab ?? 'overview') === 'blog' ? '' : 'display: none;' }}" x-transition.opacity.duration.300ms x-cloak>
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8 mb-4">
                    <div class="text-center flex flex-col items-center mb-6">
                        <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500 rounded-full flex items-center justify-center text-3xl mb-3 shadow-sm border border-emerald-100 dark:border-emerald-800/50">📝</div>
                        <h3 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white mb-1.5">{{ __('Read our Articles & Blogs') }}</h3>
                        <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 max-w-lg mb-2">{{ __('Explore in-depth articles, health tips, and updates.') }}</p>
                    </div>
                    
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                        @foreach($doctor->blogs as $index => $blogData)
                        @php
                            $bUrl = is_string($blogData) ? $blogData : ($blogData['url'] ?? '#');
                            $bTitle = is_string($blogData) ? __('Read Article') . (count($doctor->blogs) > 1 ? ' #' . ($index + 1) : '') : ($blogData['title'] ?? __('Read Article'));
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

            </div> {{-- END TABS WRAPPER --}}

        </div>
        {{-- END LEFT COLUMN --}}

        {{-- ── SIDEBAR (30%) --}}
        <div class="side-col w-full">
            <div class="md:sticky md:top-24 space-y-5">
                @if(is_null($doctor->user_id))
                    <div class="bg-gradient-to-br from-indigo-50 to-sky-50 dark:from-indigo-900/20 dark:to-sky-900/20 rounded-3xl border border-indigo-100 dark:border-indigo-800/50 p-6 shadow-sm text-center relative overflow-hidden">
                        {{-- Decorative background --}}
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-200/50 dark:bg-indigo-800/30 rounded-full blur-xl pointer-events-none"></div>
                        <div class="absolute -left-6 -bottom-6 w-24 h-24 bg-sky-200/50 dark:bg-sky-800/30 rounded-full blur-xl pointer-events-none"></div>
                        
                        <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-3 shadow-md text-indigo-500 relative z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h4 class="font-black text-gray-900 dark:text-white text-lg mb-1 relative z-10">{{ __('Are you this Doctor?') }}</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mb-5 relative z-10 px-2">{{ __('Claim this profile to update your details, manage appointments, and connect directly with patients.') }}</p>

                        <div class="relative z-10">
                        @auth
                            @php
                                $hasPendingClaim = auth()->user()->claimRequests()->where('doctor_id', $doctor->id)->where('status', 'pending')->exists();
                            @endphp
                            @if($hasPendingClaim)
                                <div class="w-full py-3 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 text-sm font-bold border border-amber-200 dark:border-amber-800/50 flex items-center justify-center gap-2 shadow-inner">
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ __('Claim Pending Approval') }}
                                </div>
                            @else
                                <div x-data="{ claimModal: false }">
                                    <button @click="claimModal = true" class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-500 hover:from-indigo-600 hover:to-sky-600 text-white text-sm font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                        {{ __('Claim This Profile') }}
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
                                                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 flex items-center justify-center mb-5 shadow-sm border border-indigo-200 dark:border-indigo-800">
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                    </div>
                                                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">{{ __('Claim Profile Request') }}</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                                                        {{ __('Please provide a secure message detailing your professional identity. For swift approval, include your BMDC registration number, LinkedIn profile, or official hospital ID reference.') }}
                                                    </p>
                                                    
                                                    <form method="POST" action="{{ route('doctors.claim', $doctor->id) }}">
                                                        @csrf
                                                        <div class="mb-5">
                                                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">{{ __('Identity Proof / Message') }}</label>
                                                            <textarea name="message" rows="4" required placeholder="E.g., My BMDC number is 12345. You can verify me at linkedin.com/in/doctorname" class="w-full border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors placeholder-gray-400"></textarea>
                                                        </div>
                                                        <div class="flex gap-3">
                                                            <button type="submit" class="w-full bg-gradient-to-r from-indigo-500 to-sky-500 hover:from-indigo-600 hover:to-sky-600 text-white font-bold py-3 rounded-xl shadow-md transition-all">{{ __('Submit Secure Request') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-500 hover:from-indigo-600 hover:to-sky-600 text-white text-sm font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                {{ __('Claim This Profile') }}
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
                                        Did you find a duplicate of this Doctor's profile, or is there incorrect information? Let us know.
                                    </p>
                                    
                                    <form method="POST" action="{{ route('report-duplicate.store') }}">
                                        @csrf
                                        <input type="hidden" name="reportable_id" value="{{ $doctor->id }}">
                                        <input type="hidden" name="reportable_type" value="App\Models\Doctor">
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

                @php
                    $sidebarTopAd = \App\Models\Advertisement::where('is_active', true)->where('position', 'sidebar_top')->inRandomOrder()->first();
                    $sidebarBottomAd = \App\Models\Advertisement::where('is_active', true)->where('position', 'sidebar_bottom')->inRandomOrder()->first();
                @endphp

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
                <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-4 flex flex-col items-center justify-center text-center h-[280px] shadow-sm relative overflow-hidden group">
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
                <div class="bg-gradient-to-br from-sky-50 to-indigo-50 dark:from-sky-900/20 dark:to-indigo-900/20 rounded-2xl border border-sky-100 dark:border-sky-800/50 p-6 flex flex-col items-center justify-center text-center h-[350px] shadow-sm relative overflow-hidden group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-sky-500 bg-white/80 dark:bg-gray-900/80 backdrop-blur px-2 py-0.5 rounded shadow-sm">Sponsored</span>
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mb-3 shadow-md group-hover:-translate-y-1 transition-transform">
                        <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">{{ __('Grow Your Practice') }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Premium placement for hospitals & doctors.') }}</p>
                </div>
                @endif

                {{-- SILO Links (Programmatic SEO) --}}
                @php
                    $siloLinks = \App\Models\SeoLandingPage::published()
                        ->where('type', 'doctor')
                        ->where(function($query) use ($doctor) {
                            $specialtyIds = $doctor->specialties->pluck('id')->toArray();
                            if(!empty($specialtyIds)) {
                                $query->whereIn('specialty_id', $specialtyIds);
                            }
                        })
                        ->inRandomOrder()
                        ->take(5)
                        ->get();
                @endphp
                
                @if($siloLinks->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-sm flex items-center gap-2">
                        <span class="text-sky-500">🔗</span> {{ __('Popular Searches') }}
                    </h3>
                    <ul class="space-y-2">
                        @foreach($siloLinks as $silo)
                        <li>
                            <a href="/{{ $silo->slug }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-sky-600 dark:hover:text-sky-400 flex items-center gap-2 group transition-colors">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-300 dark:bg-sky-700 group-hover:bg-sky-500 transition-colors"></span>
                                {{ $silo->keyword }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

            </div>
        </div>
        {{-- END RIGHT COLUMN --}}
    </div> {{-- END FLEX ROW (layout-wrapper) --}}

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
    @endif    {{-- Related doctors --}}
    @if($related->count())
    <div class="mt-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center text-sky-600">👨‍⚕️</span>
                {{ __('Related Specialists') }}
            </h2>
            <a href="{{ route('doctors.index') }}" class="text-sm font-bold text-sky-600 hover:text-sky-700 dark:text-sky-400 flex items-center gap-1 group">
                {{ __('View All') }}
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($related as $r)
            <div class="group bg-white dark:bg-gray-800 rounded-3xl shadow-sm hover:shadow-xl border border-gray-100 dark:border-gray-700 p-5 transition-all duration-300 hover:-translate-y-2 flex flex-col relative overflow-hidden">
                {{-- Decorative Background --}}
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-sky-50 dark:bg-sky-900/10 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                <div class="flex items-start gap-4 mb-4 relative z-10">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-sky-100 to-indigo-100 dark:from-sky-900/40 dark:to-indigo-900/40 flex-shrink-0 flex items-center justify-center overflow-hidden shadow-inner border border-white dark:border-gray-700">
                        @if($r->photo)
                            <img loading="lazy" decoding="async" src="{{ Storage::url($r->photo) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl font-black text-sky-400">{{ mb_substr($r->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0 pt-1">
                        <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-sky-600 transition-colors truncate">
                            <a href="{{ route('doctors.show', $r->slug) }}">{{ $r->name }}</a>
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium truncate mt-0.5">{{ $r->designation }}</p>
                        @if($r->experience_years > 0)
                            <div class="mt-2 flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 w-fit">
                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase">{{ $r->experience_years }}+ {{ __('Years Exp.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap gap-1.5 mb-5 relative z-10">
                    @foreach($r->specialties->take(2) as $sp)
                        <span class="px-2 py-0.5 rounded-md bg-gray-50 dark:bg-gray-700/50 text-[10px] font-bold text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-600 uppercase tracking-tighter">
                            {{ $sp->getTranslation('name', app()->getLocale()) }}
                        </span>
                    @endforeach
                    @if($r->specialties->count() > 2)
                        <span class="px-2 py-0.5 rounded-md bg-gray-50 dark:bg-gray-700/50 text-[10px] font-bold text-gray-400 uppercase tracking-tighter cursor-default">
                            +{{ $r->specialties->count() - 2 }}
                        </span>
                    @endif
                </div>

                <div class="mt-auto pt-4 border-t border-gray-50 dark:border-gray-700/50 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-1.5">
                        <div class="flex items-center">
                            @for($i=1; $i<=5; $i++)
                                <svg class="w-3 h-3 {{ $i <= floor($r->average_rating ?: 0) ? 'text-amber-400' : 'text-gray-200 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span class="text-[11px] font-bold text-gray-400">({{ $r->view_count }})</span>
                    </div>
                    <a href="{{ route('doctors.show', $r->slug) }}" class="p-2 rounded-xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 hover:bg-sky-500 hover:text-white transition-all shadow-sm group/btn">
                        <svg class="w-4 h-4 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('doctorTabs', (initialTab, slug) => ({
        currentTab: initialTab,
        slug: slug,
        
        init() {
            // Handle browser back/forward buttons
            window.addEventListener('popstate', (e) => {
                if (e.state && e.state.tab) {
                    this.currentTab = e.state.tab;
                } else {
                    this.currentTab = 'overview';
                }
            });
            // Update initial state without reloading 
            let stateTab = this.currentTab === '' ? 'overview' : this.currentTab;
            window.history.replaceState({tab: stateTab}, '', window.location.pathname);
        },
        
        switchTab(tab) {
            if (this.currentTab === tab) return;
            this.currentTab = tab;
            let url = '/doctor/' + this.slug;
            if (tab !== 'overview') {
                url += '/' + tab;
            }
            window.history.pushState({tab: tab}, '', url);
            
            // Scroll to top of tabs gracefully
            window.scrollTo({ top: 250, behavior: 'smooth' });
        }
    }));
});
</script>
@endpush

@endsection
