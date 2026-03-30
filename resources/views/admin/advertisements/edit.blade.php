@extends('admin.layouts.app')
@section('title', 'Edit Advertisement')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.advertisements.index') }}" class="p-2 text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-800 rounded-lg transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit Advertisement</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Update the banner image, URL, or visibility status.</p>
    </div>
</div>

<div x-data="{ openDeleteModal: false }" class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 max-w-3xl">
    <form action="{{ route('admin.advertisements.update', $advertisement->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Advertisement Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $advertisement->title) }}" required class="w-full bg-gray-50 dark:bg-gray-800 border {{ $errors->has('title') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }} text-gray-900 dark:text-white rounded-lg focus:ring-sky-500 focus:border-sky-500 block p-2.5">
            @error('title')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Current Image Preview & New Image Upload --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Update Banner Image</label>
                <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 focus:outline-none file:mr-4 file:py-2.5 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 dark:file:bg-sky-900/30 dark:file:text-sky-400">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave empty to keep the current image.</p>
                @error('image')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
            
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-gray-800/50 flex flex-col items-center">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Current Image</span>
                <img src="{{ asset('storage/' . $advertisement->image_path) }}" alt="{{ $advertisement->title }}" class="h-24 object-contain rounded border border-gray-200 dark:border-gray-700">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Target URL --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Target Link URL</label>
                <input type="url" name="target_url" value="{{ old('target_url', $advertisement->target_url) }}" placeholder="https://example.com" class="w-full bg-gray-50 dark:bg-gray-800 border {{ $errors->has('target_url') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }} text-gray-900 dark:text-white rounded-lg focus:ring-sky-500 focus:border-sky-500 block p-2.5">
                <p class="mt-1 text-xs text-gray-500 block">Where this ad should redirect when clicked.</p>
                @error('target_url')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Position --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Display Position <span class="text-red-500">*</span></label>
                <select name="position" required class="w-full bg-gray-50 dark:bg-gray-800 border {{ $errors->has('position') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }} text-gray-900 dark:text-white rounded-lg focus:ring-sky-500 focus:border-sky-500 block p-2.5">
                    <optgroup label="Profile Pages (Doctor/Hospital)">
                        <option value="sidebar_top" {{ old('position', $advertisement->position) == 'sidebar_top' ? 'selected' : '' }}>Sidebar - Top</option>
                        <option value="sidebar_bottom" {{ old('position', $advertisement->position) == 'sidebar_bottom' ? 'selected' : '' }}>Sidebar - Bottom</option>
                        <option value="profile_bottom" {{ old('position', $advertisement->position) == 'profile_bottom' ? 'selected' : '' }}>Profile - Bottom Leaderboard</option>
                    </optgroup>
                    <optgroup label="Homepage">
                        <option value="homepage_top" {{ old('position', $advertisement->position) == 'homepage_top' ? 'selected' : '' }}>Homepage - Top Leaderboard</option>
                        <option value="homepage_mid" {{ old('position', $advertisement->position) == 'homepage_mid' ? 'selected' : '' }}>Homepage - Mid Banner</option>
                    </optgroup>
                    <optgroup label="Listings (Search Pages)">
                        <option value="list_top" {{ old('position', $advertisement->position) == 'list_top' ? 'selected' : '' }}>List Page - Top Banner</option>
                        <option value="list_inline" {{ old('position', $advertisement->position) == 'list_inline' ? 'selected' : '' }}>List Page - In-Feed/Inline Ad</option>
                    </optgroup>
                    <optgroup label="Blog Section">
                        <option value="blog_sidebar" {{ old('position', $advertisement->position) == 'blog_sidebar' ? 'selected' : '' }}>Blog - Sidebar ad</option>
                        <option value="blog_inline" {{ old('position', $advertisement->position) == 'blog_inline' ? 'selected' : '' }}>Blog - In-Article Banner</option>
                    </optgroup>
                </select>
                @error('position')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Is Active --}}
        <div class="pt-2">
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $advertisement->is_active) ? 'checked' : '' }} class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sky-300 dark:peer-focus:ring-sky-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-500"></div>
                <span class="ml-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Active (Visible on frontend)</span>
            </label>
        </div>

        <div class="pt-4 border-t border-gray-200 dark:border-gray-800 flex items-center justify-between">
            <div>
                <button type="button" @click="openDeleteModal = true" class="px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 font-medium rounded-lg transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete Ad
                </button>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('admin.advertisements.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-sky-600 text-white font-semibold hover:bg-sky-700 focus:ring-4 focus:ring-sky-300 transition-colors shadow-sm">Update Advertisement</button>
            </div>
        </div>
    </form>
    
    {{-- Delete Modal --}}
    <div x-show="openDeleteModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak style="display: none;">
        <div @click.away="openDeleteModal = false" class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl w-full max-w-sm mx-4 transform transition-all text-left">
            <div class="flex items-center gap-3 mb-4 text-red-600 dark:text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Confirm Delete</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Are you sure you want to delete this advertisement? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="openDeleteModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                <form action="{{ route('admin.advertisements.destroy', $advertisement->id) }}" method="POST" class="inline m-0 p-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition shadow-sm">Delete Ad</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
