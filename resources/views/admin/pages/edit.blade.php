@extends('admin.layouts.app')
@section('title', 'Edit Page')

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 600,
        menubar: true,
        promotion: false,
        branding: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | help',
        skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
        content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
</script>
<style>
    /* Hide the no-api-key notification warning which acts as an overlay and can trap focus/clicks */
    .tox-notifications-container { display: none !important; }
    /* Ensure the editor stays on top of any floating tailwind layouts */
    .tox-tinymce { z-index: 40 !important; }
</style>
@endpush

@section('content')
<div class="mb-5 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.pages.index') }}" class="p-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit Page: {{ $page->title }}</h2>
    </div>
    <a href="{{ route('page.show', $page->slug ?? 'temp') }}" target="_blank" class="text-sm font-medium text-sky-600 hover:underline">View Live Page →</a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8 max-w-5xl">
    <form method="POST" action="{{ route('admin.pages.update', $page->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Page Title *</label>
                <input type="text" id="title" name="title" required value="{{ old('title', $page->title) }}"
                       class="w-full px-4 py-2.5 rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-sky-500 focus:border-sky-500 shadow-sm transition-colors text-sm">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Custom Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $page->slug) }}"
                       class="w-full px-4 py-2.5 rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-sky-500 focus:border-sky-500 shadow-sm transition-colors text-sm font-mono">
                <p class="text-xs text-gray-500 mt-1 dark:text-gray-400">Leave blank to auto-generate from title.</p>
                @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="content" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Page Content</label>
            <textarea id="content" name="content" rows="15" class="w-full">{{ old('content', $page->content) }}</textarea>
            @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 mt-4">
            <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $page->is_active))
                   class="w-5 h-5 text-sky-600 border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-900 focus:ring-sky-500">
            <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">Published / Active</label>
        </div>

        <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex gap-4">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-semibold rounded-xl text-sm hover:opacity-90 shadow-sm transition-all">Update Page</button>
            <a href="{{ route('admin.pages.index') }}" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
