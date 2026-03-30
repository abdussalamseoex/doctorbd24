@extends('admin.layouts.app')

@section('title', 'Edit Blog Category')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.blog-categories.index') }}" class="p-2 -ml-2 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Category: {{ $blog_category->name }}</h2>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.blog-categories.update', $blog_category->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Category Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $blog_category->name) }}" required
                   class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 px-4 py-2.5 transition-colors">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center">
            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $blog_category->is_active) ? 'checked' : '' }}
                class="w-5 h-5 text-sky-600 bg-gray-100 dark:bg-gray-900 border-gray-300 dark:border-gray-700 rounded focus:ring-sky-500 dark:focus:ring-sky-600 dark:ring-offset-gray-800 focus:ring-2 disabled:opacity-50 transition-colors cursor-pointer">
            <label for="is_active" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                Category is Active
                <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5 font-normal">Inactive categories will not appear in the blog sidebar.</p>
            </label>
        </div>

        <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
            <a href="{{ route('admin.blog-categories.index') }}" class="px-5 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium text-sm">Cancel</a>
            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm shadow-sky-600/20 text-sm">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
