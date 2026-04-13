@extends('admin.layouts.app')

@section('title', 'Media Manager')

@section('content')
<div x-data="mediaManager" class="max-w-7xl mx-auto space-y-6">

    {{-- Header & Top Actions --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-sky-500 to-indigo-500 bg-clip-text text-transparent">Media Library</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage and optimize your media assets across the platform.</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
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
                <div class="relative group cursor-pointer aspect-square rounded-xl overflow-hidden border-2 transition-all duration-200"
                     :class="selected.includes('{{ $file['path'] }}') ? 'border-sky-500 scale-95 shadow-md shadow-sky-500/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-500'">
                    
                    <img src="{{ Str::endsWith($file['name'], '.svg') ? $file['url'] : $file['url'].'?w=300&h=300&fit=crop' }}" 
                         alt="{{ $file['name'] }}" 
                         loading="lazy" 
                         class="w-full h-full object-cover bg-gray-50 dark:bg-gray-800"
                         @click="toggleSelect('{{ $file['path'] }}')">
                         
                    {{-- Overlay Details --}}
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-gray-900/90 to-transparent p-3 pt-8 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end"
                         :class="selected.includes('{{ $file['path'] }}') ? 'opacity-100' : ''">
                        <p class="text-xs text-white truncate font-medium" title="{{ $file['name'] }}">{{ $file['name'] }}</p>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-[10px] text-gray-300">{{ number_format($file['size'] / 1024, 0) }} KB</span>
                            <button @click.stop="openRenameModal('{{ $file['path'] }}', '{{ $file['name'] }}', '{{ $file['url'] }}')" 
                                    class="text-sky-300 hover:text-white text-xs bg-black/40 px-2 py-0.5 rounded">Rename</button>
                        </div>
                    </div>

                    {{-- Checkbox Indicator --}}
                    <div class="absolute top-2 left-2 w-5 h-5 rounded-full border-2 bg-white flex items-center justify-center transition-opacity"
                         :class="selected.includes('{{ $file['path'] }}') ? 'border-sky-500 opacity-100' : 'border-gray-300 opacity-0 group-hover:opacity-100'"
                         @click.stop="toggleSelect('{{ $file['path'] }}')">
                        <svg x-show="selected.includes('{{ $file['path'] }}')" class="w-3 h-3 text-sky-500" viewBox="0 0 12 12" fill="currentColor"><path fill-rule="evenodd" d="M10.28 2.28a.75.75 0 010 1.06l-5.5 5.5a.75.75 0 01-1.06 0l-2.5-2.5a.75.75 0 011.06-1.06L4.25 7.19l4.97-4.97a.75.75 0 011.06 0z" clip-rule="evenodd" /></svg>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $paginatedFiles->links() }}
        </div>
    @endif

    {{-- Custom Alpine Modal for Renaming --}}
    <div x-show="isRenameModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isRenameModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="closeRenameModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="isRenameModalOpen" x-transition class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100 dark:border-gray-700">
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-start gap-4">
                        <div class="w-24 h-24 flex-shrink-0 bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                            <img :src="renameData.url" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 w-full">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">Rename File for SEO</h3>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Filename <span class="text-xs font-normal text-gray-500">(with extension)</span></label>
                                <input type="text" x-model="renameData.newName" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-sky-500" placeholder="e.g. best-heart-specialist.webp">
                            </div>
                            <div class="mt-3 p-3 bg-sky-50 dark:bg-sky-900/20 rounded-lg relative">
                                <p class="text-xs text-sky-800 dark:text-sky-300 font-medium leading-relaxed">
                                    <span class="block mb-1">✨ Smart Update Active:</span>
                                    Renaming this file will automatically search through all Doctors, Hospitals, Ambulances, and Blog posts and securely update their links to this new name. No broken images!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" @click="closeRenameModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">Cancel</button>
                    <button type="button" @click="submitRename" class="px-5 py-2 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition-colors shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 flex items-center gap-2">
                        <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="isSubmitting ? 'Updating...' : 'Rename & Update Links'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mediaManager', () => ({
        files: @json($paginatedFiles->pluck('path')),
        selected: [],
        isRenameModalOpen: false,
        isSubmitting: false,
        renameData: {
            oldPath: '',
            newName: '',
            url: ''
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

        openRenameModal(path, name, url) {
            this.renameData.oldPath = path;
            this.renameData.newName = name;
            this.renameData.url = url;
            this.isRenameModalOpen = true;
        },

        closeRenameModal() {
            this.isRenameModalOpen = false;
        },

        submitRename() {
            if (!this.renameData.newName) return alert('Filename is required.');
            this.isSubmitting = true;

            fetch("{{ route('admin.media.rename') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    old_path: this.renameData.oldPath,
                    new_name: this.renameData.newName
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

        bulkDelete() {
            if (!confirm(`Are you sure you want to permanently delete ${this.selected.length} items? This action cannot be undone and may break links if files are actively used.`)) {
                return;
            }

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
            .catch(err => {
                alert('Request failed');
                console.error(err);
            });
        }
    }));
});
</script>
@endsection
