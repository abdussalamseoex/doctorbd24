@php
    $isBangla = app()->getLocale() === 'bn';
    $hasTranslation = !empty($page->getTranslation('content', 'bn', false));
    $showTranslationWarning = $isBangla && !$hasTranslation;
@endphp

@extends('layouts.app', ['noindex_page' => $showTranslationWarning, 'has_bn_translation' => $hasTranslation])

@section('title', $page->title . ' | DoctorBD24')

@section('content')
<div class="bg-gray-50 dark:bg-gray-900 py-12 md:py-20 border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4">{{ $page->title }}</h1>
        <p class="text-lg text-gray-500 dark:text-gray-400">Last updated: {{ $page->updated_at->format('F d, Y') }}</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    @if($showTranslationWarning)
    <div class="mb-8 p-4 rounded-xl border border-sky-100 dark:border-sky-800/50 bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-300 flex items-start gap-4">
        <div class="w-10 h-10 rounded-full bg-sky-100 dark:bg-sky-800/50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <h4 class="font-bold text-sm mb-1">অসম্পূর্ণ অনুবাদ (Incomplete Translation)</h4>
            <p class="text-xs opacity-90 leading-relaxed">এই পেজটির বাংলা অনুবাদ এখনো আপডেট করা হয়নি। আপনার সুবিধার্থে আপাতত ইংরেজি ভার্সনটি দেখানো হচ্ছে। খুব শীঘ্রই বাংলা কন্টেন্ট যুক্ত করা হবে।</p>
        </div>
    </div>
    @endif

    <div class="prose prose-lg dark:prose-invert max-w-none text-gray-600 dark:text-gray-400 trumbowyg-content">
        {!! $page->content !!}
    </div>
</div>
@endsection
