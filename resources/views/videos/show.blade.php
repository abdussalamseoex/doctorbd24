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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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

    </div>
</div>
@endsection
