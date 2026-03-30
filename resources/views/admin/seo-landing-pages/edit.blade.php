@extends('admin.layouts.app')
@section('title', 'Edit Programmatic SEO Landing Page')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.seo-landing-pages.index') }}" class="p-2 text-gray-500 hover:text-gray-900 dark:hover:text-white bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit SEO Landing Page</h1>
            <p class="text-sm text-gray-500">Refine the schema and AI content for this page.</p>
        </div>
    </div>

    <form action="{{ route('admin.seo-landing-pages.update', $seoLandingPage) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.seo-landing-pages._form', ['page' => $seoLandingPage])
    </form>
</div>
@endsection
