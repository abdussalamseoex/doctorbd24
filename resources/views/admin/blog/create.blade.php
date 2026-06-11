@extends('admin.layouts.app')
@section('title', isset($blogPost) ? 'Edit Post' : 'New Blog Post')

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
        'image | removeformat | help',
        images_upload_handler: function (blobInfo, progress) {
            return new Promise((resolve, reject) => {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.blog-posts.upload-image') }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.upload.onprogress = function (e) {
                    progress(e.loaded / e.total * 100);
                };
                xhr.onload = function() {
                    if (xhr.status === 403) { reject({ message: 'HTTP Error: 403 Forbidden', remove: true }); return; }
                    if (xhr.status === 419) { reject({ message: 'Session expired. Please refresh the page.', remove: true }); return; }
                    if (xhr.status === 422) { 
                        try {
                            var json = JSON.parse(xhr.responseText);
                            reject({ message: json.message || 'Validation Error', remove: true });
                        } catch (e) {
                            reject({ message: 'Validation Error (422)', remove: true });
                        }
                        return;
                    }
                    if (xhr.status < 200 || xhr.status >= 300) { reject({ message: 'HTTP Error: ' + xhr.status, remove: true }); return; }
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location != 'string') { reject({ message: 'Invalid JSON format', remove: true }); return; }
                        resolve(json.location);
                    } catch (err) {
                        reject({ message: 'Invalid response from server', remove: true });
                    }
                };
                xhr.onerror = function () { reject({ message: 'Image upload failed due to a network error. Code: ' + xhr.status, remove: true }); };
                formData = new FormData();
                
                var filename = blobInfo.filename();
                if (!filename.includes('.')) filename += '.png';
                
                formData.append('file', blobInfo.blob(), filename);
                xhr.send(formData);
            });
        },
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
    .tox-notifications-container { display: none !important; }
    .tox-tinymce { z-index: 40 !important; }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form x-data="{ activeTab: 'en' }" method="POST" action="{{ isset($blogPost) ? route('admin.blog-posts.update', $blogPost->id) : route('admin.blog-posts.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @if(isset($blogPost)) @method('PUT') @endif

            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Title *</label>
                <input type="text" name="title" required value="{{ old('title', $blogPost->title ?? '') }}"
                       class="w-full px-3 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
            </div>
            {{-- Custom Slug --}}
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Custom Slug (URL) <span class="text-gray-400 font-normal whitespace-nowrap">(Leave empty to auto-generate)</span></label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-500 text-xs text-nowrap">doctorbd24.com/blog/</span>
                    <input type="text" name="slug" value="{{ old('slug', $blogPost->slug ?? '') }}" placeholder="how-to-stay-healthy"
                           class="flex-1 w-full px-3 py-2.5 text-sm rounded-r-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300 transition-colors">
                </div>
                @error('slug')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Featured Image</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                @if(isset($blogPost) && $blogPost->image)
                    <div class="mt-2 flex items-center gap-3 p-2 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                        <img src="{{ asset('storage/' . $blogPost->image) }}" class="w-20 h-10 object-cover rounded-lg">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-500 focus:ring-red-300 w-4 h-4">
                            <span class="text-xs text-red-500 font-medium">Remove Image</span>
                        </label>
                    </div>
                @endif
                @error('image')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Excerpt</label>
                <textarea name="excerpt" rows="2" class="w-full px-3 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300 resize-none" placeholder="Short summary (optional)">{{ old('excerpt', $blogPost->excerpt ?? '') }}</textarea>
            </div>
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">Body *</label>
                    <button type="button" onclick="generateAiContent('blog_post', 'tinymce:content', this)" class="text-[10px] bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-violet-200 transition-colors z-50 relative">
                        ✨ Auto Generate Post
                    </button>
                </div>
                <textarea name="body" id="content" rows="18" class="w-full">{{ old('body', $blogPost->body ?? '') }}</textarea>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Category</label>
                <select name="blog_category_id" class="w-full px-3 py-2.5 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-violet-300">
                    <option value="">Uncategorized</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('blog_category_id', $blogPost->blog_category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        @include('admin.components.publish-status', ['status' => $blogPost->status ?? 'draft', 'publishedAt' => $blogPost->published_at ?? null])
            @include('admin.shared._seo_fields', ['model' => $blogPost ?? null])

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-violet-500 to-purple-600 text-white text-sm font-semibold hover:opacity-90 shadow transition-all">
                    {{ isset($blogPost) ? 'Update Post' : 'Create Post' }}
                </button>
                <a href="{{ route('admin.blog-posts.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
