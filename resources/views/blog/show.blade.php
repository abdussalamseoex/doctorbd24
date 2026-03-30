@extends('layouts.app')
@section('title', $post->title . ' — DoctorBD24')
@section('meta_description', $post->meta_description ?? $post->excerpt)
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        
        {{-- Main Article Content --}}
        <div class="flex-1 w-full max-w-4xl">
            <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-sky-500">Home</a>
        <span>›</span>
        <a href="{{ route('blog.index') }}" class="hover:text-sky-500">{{ __('Blog') }}</a>
        <span>›</span>
        <span class="text-gray-600 dark:text-gray-300 line-clamp-1">{{ $post->title }}</span>
    </nav>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        @if($post->image)
            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="h-64 md:h-96 w-full object-cover">
        @else
            <div class="h-64 bg-gradient-to-br from-violet-100 to-purple-100 dark:from-violet-900/40 dark:to-purple-900/40 flex items-center justify-center text-8xl">📰</div>
        @endif
        <div class="p-6 md:p-8">
            @if($post->category)<span class="text-xs font-medium text-violet-600 dark:text-violet-400 uppercase tracking-wide">{{ $post->category->name }}</span>@endif
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-gray-100 mt-2 leading-snug">{{ $post->title }}</h1>
            <div class="flex items-center gap-4 mt-3 text-xs text-gray-400 border-b border-gray-100 dark:border-gray-700 pb-4">
                <span>{{ $post->published_at->format('d M, Y') }}</span>
                @if($post->author)<span>{{ __('by') }} {{ $post->author->name }}</span>@endif
                <span>👁 {{ $post->view_count }} {{ __('views') }}</span>
                {{-- Share --}}
                <div class="ml-auto flex gap-2">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="p-1.5 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 hover:bg-blue-200 transition-colors text-xs font-medium">FB</a>
                    <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank" class="p-1.5 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200 transition-colors text-xs font-medium">WA</a>
                </div>
            </div>
            <div class="prose prose-sm dark:prose-invert max-w-none mt-5 text-gray-700 dark:text-gray-300 leading-relaxed">
                {!! $post->body !!}
            </div>
            
            {{-- IN-ARTICLE BANNER AD --}}
            @php
                $blogInlineAd = \App\Models\Advertisement::where('is_active', true)->where('position', 'blog_inline')->inRandomOrder()->first();
            @endphp
            @if($blogInlineAd)
            <div class="w-full mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                <div class="w-full bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 flex items-center justify-center relative group border border-gray-100 dark:border-gray-700/50">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-400 bg-white/80 dark:bg-gray-900/80 backdrop-blur px-2 py-0.5 rounded shadow-sm z-10">Advertisement</span>
                    @if($blogInlineAd->target_url)
                        <a href="{{ $blogInlineAd->target_url }}" target="_blank" class="block w-full h-full flex items-center justify-center hover:opacity-90 transition-opacity">
                            <img src="{{ asset('storage/' . $blogInlineAd->image_path) }}" class="w-full h-full max-h-[150px] object-contain rounded-xl">
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $blogInlineAd->image_path) }}" class="w-full h-full max-h-[150px] object-contain rounded-xl">
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Related --}}
    @if($related->count())
    <div class="mt-10">
        <h2 class="font-bold text-gray-800 dark:text-gray-100 text-lg mb-4">{{ __('Related Articles') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($related as $r)
            <a href="{{ route('blog.show', $r->slug) }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden p-4">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 group-hover:text-violet-600 transition-colors line-clamp-2">{{ $r->title }}</h3>
                <p class="text-xs text-gray-400 mt-2">{{ $r->published_at->diffForHumans() }}</p>
            </a>
            @endforeach
        </div>
    </div>
            @endif
        </div>

        {{-- Sidebar (Ads) --}}
        <aside class="w-full lg:w-1/4 xl:w-1/5 flex-shrink-0">
            @php
                $blogSidebarAds = \App\Models\Advertisement::where('is_active', true)->where('position', 'blog_sidebar')->inRandomOrder()->take(2)->get();
                $sidebarTopAd = $blogSidebarAds->first();
                $sidebarBottomAd = $blogSidebarAds->skip(1)->first();
            @endphp
            <div class="sticky top-24 space-y-6">
                {{-- Categories Widget --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-3 text-sm">{{ __('Categories') }}</h3>
                    <ul class="space-y-1">
                        <li><a href="{{ route('blog.index') }}" class="flex items-center justify-between py-1.5 px-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('All') }}</a></li>
                        @foreach($categories as $cat)
                        <li><a href="{{ route('blog.index', ['category' => $cat->slug]) }}" class="flex items-center justify-between py-1.5 px-2 rounded-lg text-sm {{ request('category') === $cat->slug ? 'bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            {{ $cat->name }} <span class="text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $cat->posts_count }}</span>
                        </a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Ad Container 1 --}}
                @if($sidebarTopAd)
                <div class="w-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow relative group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-400 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm z-10">Ad</span>
                    @if($sidebarTopAd->target_url)
                        <a href="{{ $sidebarTopAd->target_url }}" target="_blank" class="block w-full h-full flex items-center justify-center">
                            <img src="{{ asset('storage/' . $sidebarTopAd->image_path) }}" class="w-full h-full max-h-[400px] object-contain">
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $sidebarTopAd->image_path) }}" class="w-full h-full max-h-[400px] object-contain">
                    @endif
                </div>
                @else
                <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 flex flex-col items-center justify-center text-center h-[250px] shadow-sm relative overflow-hidden group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-400 bg-white dark:bg-gray-900 px-2 py-0.5 rounded shadow-sm">Ad</span>
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8"/></svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ __('Sponsor Space Available') }}</p>
                    <p class="text-[11px] text-gray-400 mt-1 px-2">{{ __('Reach thousands of daily readers.') }}</p>
                </div>
                @endif

                {{-- Ad Container 2 --}}
                @if($sidebarBottomAd)
                <div class="w-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow relative group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-violet-500 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm z-10">Sponsored</span>
                    @if($sidebarBottomAd->target_url)
                        <a href="{{ $sidebarBottomAd->target_url }}" target="_blank" class="block w-full h-full flex items-center justify-center">
                            <img src="{{ asset('storage/' . $sidebarBottomAd->image_path) }}" class="w-full h-full max-h-[400px] object-contain">
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $sidebarBottomAd->image_path) }}" class="w-full h-full max-h-[400px] object-contain">
                    @endif
                </div>
                @else
                <div class="bg-gradient-to-br from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20 rounded-2xl border border-violet-100 dark:border-violet-800/50 p-6 flex flex-col items-center justify-center text-center h-[350px] shadow-sm relative overflow-hidden group">
                    <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-violet-600 bg-white/80 dark:bg-gray-900/80 backdrop-blur px-2 py-0.5 rounded shadow-sm">Sponsored</span>
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mb-3 shadow-md group-hover:-translate-y-1 transition-transform">
                        <svg class="w-6 h-6 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">{{ __('Promote your brand') }}</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-300">{{ __('Advertise your health products here.') }}</p>
                </div>
                @endif
            </div>
        </aside>

    </div>
</div>
@endsection
