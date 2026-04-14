@extends('admin.layouts.app')
@section('title', 'Manage Services for ' . $hospital->name)
@section('content')

<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <svg class="w-6 h-6 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Hospital Services & Tests
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Managing pricing and tests for <strong>{{ $hospital->name }}</strong></p>
        </div>
        <a href="{{ route('admin.hospitals.edit', $hospital->id) }}" class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            &larr; Back to Hospital
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Forms --}}
        <div class="space-y-6">
            {{-- Bulk Import Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    Bulk Import (CSV)
                </h3>
                <form action="{{ route('admin.hospitals.services.import', $hospital->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Select CSV File</label>
                        <input type="file" name="csv_file" required accept=".csv" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-900/30 dark:file:text-emerald-400 cursor-pointer border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none">
                        <p class="text-[11px] text-gray-400 mt-1.5">Columns should contain Category, Name, Price.</p>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <input type="checkbox" name="clear_old" value="1" class="rounded border-gray-300 text-red-500 focus:ring-red-500">
                        <span class="text-xs">Clear all existing tests before importing</span>
                    </label>
                    <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
                        Upload & Import
                    </button>
                </form>
            </div>

            {{-- Manual Add Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-4">Manual Entry</h3>
                <form action="{{ route('admin.hospitals.services.store', $hospital->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1">Category</label>
                        <input type="text" name="service_category" placeholder="e.g. BLOOD BANK" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1">Service/Test Name <span class="text-red-400">*</span></label>
                        <input type="text" name="service_name" required placeholder="e.g. CBC" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1">Description (Optional SEO Snippet)</label>
                        <textarea name="description" rows="2" placeholder="Brief details about this test..." class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 outline-none focus:ring-2 focus:ring-indigo-300"></textarea>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1">Price</label>
                        <input type="text" name="price" placeholder="e.g. 500" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
                        Add Service
                    </button>
                </form>
            </div>
        </div>

        {{-- Right Column: Data Table --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-full">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50 flex-wrap gap-4"
                     x-data="{
                        generating: false,
                        progress: 0,
                        total: 0,
                        missingIds: {{ $hospital->hospitalServices()->where(function($q) { $q->whereNull('description')->orWhere('description', ''); })->pluck('id')->values()->toJson() }},
                        generateAll() {
                            if (this.missingIds.length === 0) {
                                alert('All services for this hospital already have complete descriptions!');
                                return;
                            }
                            if (!confirm('Generate AI descriptions for ALL ' + this.missingIds.length + ' missing tests in this hospital? Note: This will run across all pages in the background.')) return;
                            
                            this.generating = true;
                            this.total = this.missingIds.length;
                            this.progress = 0;
                            
                            let chunkSize = 10;
                            let chunks = [];
                            for (let i = 0; i < this.missingIds.length; i += chunkSize) {
                                chunks.push(this.missingIds.slice(i, i + chunkSize));
                            }
                            
                            this.processChunks(chunks, 0);
                        },
                        processChunks(chunks, index) {
                            if (index >= chunks.length) {
                                this.generating = false;
                                window.location.reload();
                                return;
                            }
                            
                            fetch('{{ route('admin.hospitals.services.generate-ai', $hospital->id) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ service_ids: chunks[index] })
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.progress += chunks[index].length;
                                this.processChunks(chunks, index + 1);
                            })
                            .catch(err => {
                                console.error(err);
                                alert('API Error generating descriptions.');
                                this.generating = false;
                            });
                        }
                     }">
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200">
                            Existing Services ({{ $services->total() }})
                        </h3>
                        <p class="text-[11px] text-gray-500 mt-0.5">Showing {{ $services->count() }} items per page.</p>
                    </div>

                    <div class="flex items-center gap-4">
                        @if($services->count() > 0)
                            <button @click="generateAll" :disabled="generating" class="text-xs font-bold px-3 py-1.5 rounded-lg border flex items-center gap-1 transition-colors"
                                    :class="generating ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-fuchsia-50 text-fuchsia-600 border-fuchsia-200 hover:bg-fuchsia-100 hover:border-fuchsia-300 dark:bg-fuchsia-900/30 dark:border-fuchsia-800 dark:text-fuchsia-400'">
                                <svg x-show="!generating" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <svg x-cloak x-show="generating" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span x-show="!generating" x-text="'AI Generate Descriptions (' + missingIds.length + ' missing)'"></span>
                                <span x-cloak x-show="generating" x-text="'Generating... ' + progress + '/' + total"></span>
                            </button>
                        
                            <form action="{{ route('admin.hospitals.services.clear', $hospital->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete ALL services for this hospital?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700 hover:underline">
                                    Clear All
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-semibold text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Service Name</th>
                                <th class="px-4 py-3">Price</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($services as $svc)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 font-medium whitespace-nowrap">
                                        @if($svc->service_category)
                                            <span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-[10px]">{{ $svc->service_category }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $svc->service_name }}</div>
                                        @if($svc->description)
                                            <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">{{ $svc->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-emerald-600 dark:text-emerald-400 font-semibold">{{ $svc->price ?: '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <button @click="$dispatch('open-edit-modal', {
                                                data: { name: {{ json_encode($svc->service_name) }}, cat: {{ json_encode($svc->service_category) }}, price: {{ json_encode($svc->price) }}, desc: {{ json_encode($svc->description) }} },
                                                url: '{{ route('admin.hospitals.services.update', [$hospital->id, $svc->id]) }}'
                                            })" type="button" class="text-sky-500 hover:text-sky-700 p-1 mr-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        <form action="{{ route('admin.hospitals.services.destroy', [$hospital->id, $svc->id]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this service?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-12 text-center text-gray-400">
                                        No services found. Upload a CSV or add manually.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $services->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Edit Modal --}}
<div x-data="{
        open: false,
        service: { name: '', cat: '', price: '', desc: '' },
        actionUrl: '',
        openModal(data, url) {
            this.service = data;
            this.actionUrl = url;
            this.open = true;
        }
    }" 
    @open-edit-modal.window="openModal($event.detail.data, $event.detail.url)"
    x-show="open" 
    class="relative z-50" 
    x-cloak>
    
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="open" @click.away="open = false" x-transition.opacity class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-gray-700">
                <form :action="actionUrl" method="POST">
                    @csrf @method('PUT')
                    <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Service</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Category</label>
                                <input type="text" name="service_category" x-model="service.cat" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Service Name <span class="text-red-500">*</span></label>
                                <input type="text" name="service_name" x-model="service.name" required class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Price / Charge</label>
                                <input type="text" name="price" x-model="service.price" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Description</label>
                                <textarea name="description" x-model="service.desc" rows="3" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 sm:ml-3 sm:w-auto">Save Changes</button>
                        <button type="button" @click="open = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
