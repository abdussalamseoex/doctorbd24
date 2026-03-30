@extends('admin.layouts.app')
@section('title', 'Add Advertisement')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.advertisements.index') }}" class="p-2 text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-800 rounded-lg transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Add New Advertisement</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Upload a banner image and configure its link and position.</p>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 max-w-3xl">
    <form action="{{ route('admin.advertisements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Title --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Advertisement Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full bg-gray-50 dark:bg-gray-800 border {{ $errors->has('title') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }} text-gray-900 dark:text-white rounded-lg focus:ring-sky-500 focus:border-sky-500 block p-2.5">
            @error('title')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Image --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Banner Image <span class="text-red-500">*</span></label>
            <input type="file" name="image" accept="image/*" required class="w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 focus:outline-none file:mr-4 file:py-2.5 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 dark:file:bg-sky-900/30 dark:file:text-sky-400">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Supported formats: JPG, PNG, WEBP. Max size: 2MB.</p>
            @error('image')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Target URL --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Target Link URL</label>
                <input type="url" name="target_url" value="{{ old('target_url') }}" placeholder="https://example.com" class="w-full bg-gray-50 dark:bg-gray-800 border {{ $errors->has('target_url') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }} text-gray-900 dark:text-white rounded-lg focus:ring-sky-500 focus:border-sky-500 block p-2.5">
                <p class="mt-1 text-xs text-gray-500 block">Where this ad should redirect when clicked.</p>
                @error('target_url')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Position --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Display Position <span class="text-red-500">*</span></label>
                <select name="position" required class="w-full bg-gray-50 dark:bg-gray-800 border {{ $errors->has('position') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }} text-gray-900 dark:text-white rounded-lg focus:ring-sky-500 focus:border-sky-500 block p-2.5">
                    <optgroup label="Profile Pages (Doctor/Hospital)">
                        <option value="sidebar_top" {{ old('position') == 'sidebar_top' ? 'selected' : '' }}>Sidebar - Top</option>
                        <option value="sidebar_bottom" {{ old('position') == 'sidebar_bottom' ? 'selected' : '' }}>Sidebar - Bottom</option>
                        <option value="profile_bottom" {{ old('position') == 'profile_bottom' ? 'selected' : '' }}>Profile - Bottom Leaderboard</option>
                    </optgroup>
                    <optgroup label="Homepage">
                        <option value="homepage_top" {{ old('position') == 'homepage_top' ? 'selected' : '' }}>Homepage - Top Leaderboard</option>
                        <option value="homepage_mid" {{ old('position') == 'homepage_mid' ? 'selected' : '' }}>Homepage - Mid Banner</option>
                    </optgroup>
                    <optgroup label="Listings (Search Pages)">
                        <option value="list_top" {{ old('position') == 'list_top' ? 'selected' : '' }}>List Page - Top Banner</option>
                        <option value="list_inline" {{ old('position') == 'list_inline' ? 'selected' : '' }}>List Page - In-Feed/Inline Ad</option>
                    </optgroup>
                    <optgroup label="Blog Section">
                        <option value="blog_sidebar" {{ old('position') == 'blog_sidebar' ? 'selected' : '' }}>Blog - Sidebar ad</option>
                        <option value="blog_inline" {{ old('position') == 'blog_inline' ? 'selected' : '' }}>Blog - In-Article Banner</option>
                    </optgroup>
                </select>
                @error('position')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Is Active --}}
        <div class="pt-2">
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sky-300 dark:peer-focus:ring-sky-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-500"></div>
                <span class="ml-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Set as Active (Publish immediately)</span>
            </label>
        </div>

        <div class="pt-4 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
            <a href="{{ route('admin.advertisements.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Cancel</a>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-sky-600 text-white font-semibold hover:bg-sky-700 focus:ring-4 focus:ring-sky-300 transition-colors shadow-sm">Save Advertisement</button>
        </div>
    </form>
</div>
@endsection
