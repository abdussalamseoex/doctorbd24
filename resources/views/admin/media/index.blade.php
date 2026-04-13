@extends('admin.layouts.app')

@section('title', 'Media Manager')

@section('content')
<div x-data="mediaManager" class="max-w-7xl mx-auto space-y-6 pb-20">

    {{-- Header & Top Actions --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-sky-500 to-indigo-500 bg-clip-text text-transparent">Media Library</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View, manage, and rename images for SEO.</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <a href="{{ route('admin.media.optimize') }}" target="_blank" onclick="return confirm('This will bulk convert all old JPG/PNG images to WebP. It may take a few minutes. Proceed?')" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium rounded-lg shadow-sm transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Auto-Optimize WebP
            </a>

            <form method="GET" action="{{ route('admin.media.index') }}" class="flex-1 md:w-64">
                <select name="folder" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-sky-500 focus:border-sky-500">
                    <option value="">All Folders</option>
                    @foreach($directories as $dir)
                        <option value="{{ $dir === 'Root' ? '' : $dir }}" {{ request('folder') === ($dir === 'Root' ? '' : $dir) ? 'selected' : '' }}>
                            {{ $dir }}
                        </option>
                    @endforeach
                </select>
            </form>
            
            <button x-show="selected.length > 0" @click="bulkDelete" class="flex items-center gap-2 px-4 py-2 bg-red-500/10 text-red-600 hover:bg-red-500 hover:text-white rounded-lg transition-colors text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span x-text="`Delete (${selected.length})`"></span>
            </button>
            <button @click="selectAll" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition-colors text-sm font-medium whitespace-nowrap">
                <span x-text="selected.length === files.length && files.length > 0 ? 'Deselect All' : 'Select All'"></span>
            </button>
        </div>
    </div>

    {{-- Media Grid --}}
    @if($paginatedFiles->isEmpty())
        <div class="bg-white dark:bg-gray-900 py-20 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 text-center">
            <svg class="mx-auto w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <h3 class="text-xl font-medium text-gray-900 dark:text-white">No media found</h3>
            <p class="text-gray-500 mt-2">Upload images via the respective profile or blog creation pages.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($paginatedFiles as $index => $file)
                {{-- Card Click Opens the Modal --}}
                <div class="relative group aspect-square rounded-xl overflow-hidden border-2 transition-all duration-200 bg-gray-100 dark:bg-gray-800 flex items-center justify-center cursor-zoom-in"
                     :class="selected.includes('{{ $file['path'] }}') ? 'border-sky-500 scale-95 shadow-md shadow-sky-500/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-500'"
                     @click="openMediaModal('{{ $file['path'] }}', '{{ $file['name'] }}', '{{ $file['url'] }}', '{{ number_format($file['size'] / 1024, 0) }}', '{{ \Carbon\Carbon::createFromTimestamp($file['last_modified'])->format('d M Y') }}')">
                    
                    <img src="{{ Str::endsWith($file['name'], '.svg') ? $file['url'] : $file['url'].'?w=300&h=300&fit=crop' }}" 
                         alt="{{ $file['name'] }}" 
                         loading="lazy" 
                         class="max-w-full max-h-full object-contain">
                         
                    {{-- File Name Bar --}}
                    <div class="absolute inset-x-0 bottom-0 bg-gray-900/80 p-2 flex flex-col justify-end pointer-events-none">
                        <p class="text-xs text-white truncate font-medium text-center" title="{{ $file['name'] }}">{{ Str::limit($file['name'], 20) }}</p>
                    </div>

                    {{-- Checkbox (Stop Propagation to select) --}}
                    <div class="absolute top-2 left-2 w-7 h-7 rounded border-2 bg-white/90 dark:bg-gray-800/90 flex items-center justify-center transition-colors shadow-sm cursor-pointer"
                         :class="selected.includes('{{ $file['path'] }}') ? 'border-sky-500 bg-sky-50 dark:bg-sky-900/30' : 'border-gray-300 dark:border-gray-600'"
                         @click.stop="toggleSelect('{{ $file['path'] }}')">
                        <svg x-show="selected.includes('{{ $file['path'] }}')" class="w-4 h-4 text-sky-500" viewBox="0 0 12 12" fill="currentColor"><path fill-rule="evenodd" d="M10.28 2.28a.75.75 0 010 1.06l-5.5 5.5a.75.75 0 01-1.06 0l-2.5-2.5a.75.75 0 011.06-1.06L4.25 7.19l4.97-4.97a.75.75 0 011.06 0z" clip-rule="evenodd" /></svg>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $paginatedFiles->links() }}
        </div>
    @endif

    {{-- File View & Detail Modal --}}
    <template x-teleport="body">
        <div x-show="isMediaModalOpen" style="display: none; z-index: 99999;" class="fixed inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="isMediaModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm transition-opacity" style="z-index: 99999;" @click="closeMediaModal"></div>
            
            <div x-show="isMediaModalOpen" x-transition class="relative inline-block bg-white dark:bg-gray-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 align-middle w-full max-w-4xl border border-gray-100 dark:border-gray-800" style="z-index: 100000;">
                
                {{-- Close Button --}}
                <button @click="closeMediaModal" class="absolute top-4 right-4 p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-full transition z-10">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div class="flex flex-col md:flex-row">
                    {{-- Image Preview Side --}}
                    <div class="w-full md:w-3/5 bg-gray-100 dark:bg-black/50 p-6 flex items-center justify-center min-h-[300px]">
                        <img :src="activeMedia.url" class="max-w-full max-h-[60vh] object-contain rounded drop-shadow-lg">
                    </div>
                    
                    {{-- Details & Actions Side --}}
                    <div class="w-full md:w-2/5 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Media Details</h3>
                            
                            {{-- Info List --}}
                            <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400 mb-6">
                                <div class="flex justify-between border-b border-gray-100 dark:border-gray-800 pb-2">
                                    <span class="font-medium text-gray-500">File Size:</span>
                                    <span x-text="activeMedia.size + ' KB'"></span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 dark:border-gray-800 pb-2">
                                    <span class="font-medium text-gray-500">Uploaded On:</span>
                                    <span x-text="activeMedia.date"></span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-500 block mb-1">File URL:</span>
                                    <div class="flex items-center gap-2">
                                        <input type="text" readonly :value="activeMedia.url" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs py-1.5 focus:ring-0">
                                        <button @click="navigator.clipboard.writeText(activeMedia.url); alert('Copied!')" class="p-1.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded text-gray-700 dark:text-gray-200" title="Copy URL">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Rename Block --}}
                            <div class="bg-sky-50 dark:bg-sky-900/10 p-4 rounded-xl border border-sky-100 dark:border-sky-800/30">
                                <label class="block text-sm font-semibold text-sky-900 dark:text-sky-300 mb-2">Change File Name</label>
                                <p class="text-xs text-sky-700 dark:text-sky-400 mb-3">Renaming ensures the new name is updated in the database automatically. No broken links!</p>
                                <div class="flex gap-2">
                                    <input type="text" x-model="activeMedia.newName" class="w-full text-sm rounded-lg border-sky-200 dark:border-sky-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-sky-500 focus:ring-sky-500 py-2">
                                </div>
                                <button type="button" @click="submitRename" :disabled="isSubmitting" class="mt-3 w-full px-4 py-2 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition-colors flex items-center justify-center gap-2">
                                    <svg x-show="isSubmitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="isSubmitting ? 'Updating Links...' : 'Save New Name'"></span>
                                </button>
                            </div>
                        </div>
                        
                        {{-- Delete Quick Action --}}
                        <div class="mt-8 text-center border-t border-gray-100 dark:border-gray-800 pt-4">
                            <button @click="deleteSingle" class="text-sm font-medium text-red-500 hover:text-red-600 transition-colors">Permanently Delete File</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mediaManager', () => ({
        files: @json($paginatedFiles->pluck('path')),
        selected: [],
        
        isMediaModalOpen: false,
        isSubmitting: false,
        activeMedia: {
            oldPath: '',
            newName: '',
            url: '',
            size: '',
            date: ''
        },
        
        toggleSelect(path) {
            if (this.selected.includes(path)) {
                this.selected = this.selected.filter(i => i !== path);
            } else {
                this.selected.push(path);
            }
        },
        
        selectAll() {
            if (this.selected.length === this.files.length) {
                this.selected = [];
            } else {
                this.selected = [...this.files];
            }
        },

        openMediaModal(path, name, url, size, date) {
            this.activeMedia.oldPath = path;
            this.activeMedia.newName = name;
            this.activeMedia.url = url;
            this.activeMedia.size = size;
            this.activeMedia.date = date;
            this.isMediaModalOpen = true;
        },

        closeMediaModal() {
            this.isMediaModalOpen = false;
        },

        submitRename() {
            if (!this.activeMedia.newName) return alert('Filename is required.');
            this.isSubmitting = true;

            fetch("{{ route('admin.media.rename') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    old_path: this.activeMedia.oldPath,
                    new_name: this.activeMedia.newName
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isSubmitting = false;
                if(data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Something went wrong.');
                }
            })
            .catch(err => {
                this.isSubmitting = false;
                alert('Request failed');
                console.error(err);
            });
        },

        deleteSingle() {
            if (!confirm('Are you sure you want to permanently delete this file? This will break any links pointing to it!')) return;
            
            fetch("{{ route('admin.media.bulk-delete') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    paths: [this.activeMedia.oldPath]
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Something went wrong.');
                }
            })
            .catch(err => console.error(err));
        },

        bulkDelete() {
            if (!confirm(`Are you sure you want to permanently delete ${this.selected.length} items? This action cannot be undone.`)) return;

            fetch("{{ route('admin.media.bulk-delete') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    paths: this.selected
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Something went wrong.');
                }
            })
            .catch(err => console.error(err));
        }
    }));
});
</script>
@endsection
