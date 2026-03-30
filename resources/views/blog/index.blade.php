@extends('layouts.app')
@section('title', __('Health Blog') . ' — DoctorBD24')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">📰 {{ __('Health Articles') }}</h1>
            <form action="{{ route('blog.index') }}" method="GET" class="flex gap-3 mb-6">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search Articles...') }}"
                       class="flex-1 px-4 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-medium hover:bg-violet-700 transition-colors">{{ __('Search') }}</button>
            </form>
            @if($posts->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($posts as $post)
                <a href="{{ route('blog.show', ['id' => $post->id, 'slug' => $post->slug]) }}"
                   class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:-translate-y-1">
                    @if($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="h-44 w-full object-cover">
                    @else
                        <div class="h-44 bg-gradient-to-br from-violet-100 to-purple-100 dark:from-violet-900/40 dark:to-purple-900/40 flex items-center justify-center text-6xl">📰</div>
                    @endif
                    <div class="p-4">
                        @if($post->category)<span class="text-xs font-medium text-violet-600 dark:text-violet-400">{{ $post->category->name }}</span>@endif
                        <h2 class="font-bold text-gray-800 dark:text-gray-100 group-hover:text-violet-600 transition-colors mt-1 text-sm line-clamp-2">{{ $post->title }}</h2>
                        @if($post->excerpt)<p class="text-xs text-gray-500 dark:text-gray-400 mt-2 line-clamp-2">{{ $post->excerpt }}</p>@endif
                        <div class="flex items-center justify-between mt-3 text-xs text-gray-400">
                            <span>{{ $post->published_at->format('d M, Y') }}</span>
                            <span class="text-violet-500 font-medium group-hover:underline">{{ __('Read More') }} →</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
            @else
            <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl border"><div class="text-5xl mb-4">😕</div><p class="text-gray-500">{{ __('No Articles Found.') }}</p></div>
            @endif
        </div>

        {{-- Sidebar categories & Ads --}}
        <aside class="w-full lg:w-56 flex-shrink-0">
            @php
                $blogSidebarAds = \App\Models\Advertisement::where('is_active', true)->where('position', 'blog_sidebar')->inRandomOrder()->take(2)->get();
                $sidebarTopAd = $blogSidebarAds->first();
                $sidebarBottomAd = $blogSidebarAds->skip(1)->first();
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-5 sticky top-20">
                <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-3 text-sm">{{ __('Categories') }}</h3>
                <ul class="space-y-1">
                    <li><a href="{{ route('blog.index') }}" class="flex items-center justify-between py-1.5 px-2 rounded-lg text-sm {{ !request('category') ? 'bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">{{ __('All') }} <span class="text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $posts->total() }}</span></a></li>
                    @foreach($categories as $cat)
                    <li><a href="{{ route('blog.index', ['category' => $cat->slug]) }}" class="flex items-center justify-between py-1.5 px-2 rounded-lg text-sm {{ request('category') === $cat->slug ? 'bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        {{ $cat->name }} <span class="text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $cat->posts_count }}</span>
                    </a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Ad Container 1 --}}
            @if($sidebarTopAd)
            <div class="w-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow relative group mt-6">
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
            <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 flex flex-col items-center justify-center text-center h-[250px] shadow-sm relative overflow-hidden group mt-6">
                <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-gray-400 bg-white dark:bg-gray-900 px-2 py-0.5 rounded shadow-sm">Ad</span>
                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8"/></svg>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ __('Sponsor Space Available') }}</p>
                <p class="text-[11px] text-gray-400 mt-1 px-2">{{ __('Reach thousands of daily readers.') }}</p>
            </div>
            @endif

            {{-- Ad Container 2 --}}
            @if($sidebarBottomAd)
            <div class="w-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow relative group mt-6">
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
            <div class="bg-gradient-to-br from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20 rounded-2xl border border-violet-100 dark:border-violet-800/50 p-6 flex flex-col items-center justify-center text-center h-[350px] shadow-sm relative overflow-hidden group mt-6">
                <span class="absolute top-2 right-2 text-[10px] font-bold uppercase text-violet-600 bg-white/80 dark:bg-gray-900/80 backdrop-blur px-2 py-0.5 rounded shadow-sm">Sponsored</span>
                <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mb-3 shadow-md group-hover:-translate-y-1 transition-transform">
                    <svg class="w-6 h-6 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">{{ __('Promote your brand') }}</h4>
                <p class="text-xs text-gray-600 dark:text-gray-300">{{ __('Advertise your health products here.') }}</p>
            </div>
            @endif
        </aside>
    </div>
</div>
@endsection
