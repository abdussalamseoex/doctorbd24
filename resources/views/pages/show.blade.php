@extends('layouts.app')
@section('title', $page->title . ' | DoctorBD24')

@section('content')
<div class="bg-gray-50 dark:bg-gray-900 py-12 md:py-20 border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4">{{ $page->title }}</h1>
        <p class="text-lg text-gray-500 dark:text-gray-400">Last updated: {{ $page->updated_at->format('F d, Y') }}</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    <div class="prose prose-lg dark:prose-invert max-w-none text-gray-600 dark:text-gray-400 trumbowyg-content">
        {!! $page->content !!}
    </div>
</div>
@endsection
