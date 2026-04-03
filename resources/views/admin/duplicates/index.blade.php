@extends('admin.layouts.app')
@section('title', 'Duplicate Finder')

@section('content')
@php $totalProfiles = $duplicates->flatten(1)->count(); @endphp
<div x-data="duplicateManager({{ $totalProfiles }})">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-sky-500 to-indigo-600">
                Detected Duplicates
            </h2>
            <p class="text-gray-500 text-sm mt-1">Review and merge duplicate profiles easily without data loss.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if(!$duplicates->isEmpty())
                <div class="flex items-center gap-2 border-r border-gray-300 dark:border-gray-700 pr-3 mr-1">
                    <button @click="toggleAllGlobal" type="button" class="text-sm px-3 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        <span x-text="globalSelectedIds.length > 0 && globalSelectedIds.length == totalProfiles ? 'Deselect All' : 'Select All'"></span>
                    </button>
                    <button type="submit" form="global-dismiss-form" x-show="globalSelectedIds.length > 0" style="display: none;" class="text-sm px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition shadow-sm flex items-center gap-1">
                        Dismiss Selected (<span x-text="globalSelectedIds.length"></span>)
                    </button>
                </div>
            @endif

            <a href="{{ route('admin.duplicates.index', ['type' => 'doctor']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $type === 'doctor' ? 'bg-indigo-600 text-white shadow' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                Doctors
            </a>
            <a href="{{ route('admin.duplicates.index', ['type' => 'hospital']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $type === 'hospital' ? 'bg-indigo-600 text-white shadow' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                Hospitals
            </a>
        </div>
    </div>

    @if($duplicates->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8 text-center">
            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">All Clear!</h3>
            <p class="text-gray-500 mt-1">No duplicates detected for {{ $type }}s at this moment.</p>
        </div>
    @else
        <form method="POST" action="{{ route('admin.duplicates.ignore') }}" id="global-dismiss-form" onsubmit="return confirm('Are you sure you want to dismiss all selected profiles? They will be removed from this list.');">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="space-y-6">
                @foreach($duplicates as $name => $group)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Potential Match: "{{ $name }}"
                            </h3>
                            <div class="flex items-center gap-3">
                                <button type="button" @click="toggleGroup({{ json_encode($group->pluck('id')) }})" class="text-xs font-medium text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">
                                    Select Group
                                </button>
                                <span class="text-xs font-medium px-2.5 py-1 bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 rounded-full">
                                    {{ count($group) }} Profiles
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-5 grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @foreach($group as $item)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 relative hover:border-indigo-500 transition-colors">
                                    <label class="absolute top-2 right-2 flex items-center gap-2 cursor-pointer bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded border border-gray-200 dark:border-gray-700 hover:border-red-300 dark:hover:border-red-800 transition-colors" title="Select to Dismiss from Duplicates">
                                        <input type="checkbox" name="ids[]" value="{{ $item->id }}" x-model="globalSelectedIds" class="dismiss-checkbox w-4 h-4 text-red-500 border-gray-300 rounded focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:ring-offset-gray-800">
                                        <span class="text-xs font-medium text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400">Select to Dismiss</span>
                                    </label>
                                    <div class="flex gap-4">
                                        @if(isset($item->photo) || isset($item->logo))
                                            <img src="{{ asset('storage/' . ($item->photo ?? $item->logo)) }}" class="w-16 h-16 rounded-lg object-cover bg-gray-100" />
                                        @else
                                            <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            </div>
                                        @endif
                                        <div class="pt-1">
                                            <div class="text-xs text-gray-500">ID: #{{ $item->id }}</div>
                                            <h4 class="font-bold text-gray-900 dark:text-gray-100 pr-24">{{ $item->name }}</h4>
                                            
                                            @if($type === 'doctor')
                                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ $item->designation ?? 'N/A' }}</div>
                                                <div class="text-sm mt-1">📱 {{ $item->phone ?? 'No Phone' }}</div>
                                                <div class="text-xs mt-2 text-gray-500 font-medium">Chambers:</div>
                                                <ul class="text-xs text-gray-600 dark:text-gray-400 list-disc list-inside">
                                                    @forelse($item->chambers as $ch)
                                                        <li>{{ $ch->hospital->name ?? 'Unknown Hospital' }}</li>
                                                    @empty
                                                        <li>No Chambers</li>
                                                    @endforelse
                                                </ul>
                                            @else
                                                <div class="text-sm mt-1">📱 {{ $item->phone ?? 'No Phone' }}</div>
                                                <div class="text-sm text-gray-500">📍 {{ $item->address ?? 'No Address' }}</div>
                                            @endif
                                            <div class="mt-2 text-xs">
                                                <a href="{{ $type === 'doctor' ? route('doctors.show', $item->slug) : route('hospitals.show', $item->slug) }}" target="_blank" class="text-sky-500 hover:underline">View Public Profile &rarr;</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                            <button type="button" @click="openMergeModal({{ json_encode($group) }})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition-all flex items-center gap-2 border border-transparent">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                Review & Merge
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>

        {{-- Merge Modal --}}
        <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="modalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="modalOpen" x-transition.scale class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 dark:border-gray-700">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="modal-title">Merge Profiles</h3>
                                <p class="text-sm text-gray-500 mt-1">Select the primary profile to keep. The other will be merged and then deleted.</p>
                                
                                <form method="POST" action="{{ route('admin.duplicates.merge') }}" id="mergeForm" class="mt-6 space-y-5 text-left">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ $type }}">
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Primary Profile (Keep)</label>
                                            <select x-model="primaryId" name="primary_id" @change="updateCombinedData" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                <template x-for="item in currentGroup" :key="item.id">
                                                    <option :value="item.id" x-text="'ID: ' + item.id + ' - ' + item.name + (item.phone ? ' (' + item.phone + ')' : '')"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duplicate Profiles (Merge & Delete)</label>
                                            <div class="space-y-2 border border-red-200 dark:border-red-800 p-3 rounded-lg bg-red-50/50 dark:bg-red-900/10 max-h-48 overflow-y-auto">
                                                <template x-for="item in currentGroup" :key="'dup-'+item.id">
                                                    <label x-show="item.id != primaryId" class="flex items-center gap-2 cursor-pointer">
                                                        <input type="checkbox" name="duplicate_ids[]" :value="item.id" x-model="duplicateIds" class="w-4 h-4 text-red-600 border-red-300 rounded focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-300" x-text="'ID: ' + item.id + ' - ' + item.name"></span>
                                                    </label>
                                                </template>
                                            </div>
                                            <p class="text-xs text-gray-400 mt-1">Checked profiles will be merged and deleted.</p>
                                        </div>
                                    </div>

                                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700 space-y-4">
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Final Combined Data</h4>
                                        <p class="text-xs text-gray-500">You can edit the final phone number or bio before merging. All Chambers & Reviews will be merged automatically.</p>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Combined Phone Numbers</label>
                                            <input type="text" name="merged_phone" x-model="mergedPhone" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                        </div>
                                        
                                        <template x-if="'{{ $type }}' === 'doctor'">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Final Bio</label>
                                                <textarea name="merged_bio" x-model="mergedBio" rows="4" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white"></textarea>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-3 rounded-lg text-sm border border-red-100 dark:border-red-800/50 flex gap-2">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>Warning: This action will permanently merge relationships and <strong>delete</strong> the duplicate profile. This cannot be undone.</span>
                                    </div>

                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-indigo-600 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                            Confirm Merge
                                        </button>
                                        <button type="button" @click="modalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('duplicateManager', (total) => ({
            totalProfiles: total || 0,
            globalSelectedIds: [],
            
            modalOpen: false,
            currentGroup: [],
            primaryId: '',
            duplicateIds: [],
            mergedPhone: '',
            mergedBio: '',

            toggleAllGlobal() {
                const allBoxes = Array.from(document.querySelectorAll('.dismiss-checkbox')).map(cb => cb.value);
                if (this.globalSelectedIds.length === allBoxes.length && allBoxes.length > 0) {
                    this.globalSelectedIds = [];
                } else {
                    this.globalSelectedIds = allBoxes;
                }
            },

            toggleGroup(ids) {
                // If all items in this group are already selected, deselect them. Otherwise, select them all.
                let areAllSelected = true;
                for (let id of ids) {
                    if (!this.globalSelectedIds.includes(String(id))) {
                        areAllSelected = false;
                        break;
                    }
                }
                
                if (areAllSelected) {
                    this.globalSelectedIds = this.globalSelectedIds.filter(v => !ids.map(String).includes(v));
                } else {
                    for (let id of ids) {
                        if (!this.globalSelectedIds.includes(String(id))) {
                            this.globalSelectedIds.push(String(id));
                        }
                    }
                }
            },

            openMergeModal(group) {
                this.currentGroup = group;
                this.primaryId = group[0].id;
                this.updateCombinedData();
                this.modalOpen = true;
            },

            updateCombinedData() {
                // Auto-check all duplicates except the primary
                this.duplicateIds = this.currentGroup
                    .filter(i => parseInt(i.id) !== parseInt(this.primaryId))
                    .map(i => i.id);

                // Collect all phones
                let phones = this.currentGroup.map(i => i.phone).filter(p => p && p.trim() !== '');
                // Unique phones
                phones = [...new Set(phones)];
                this.mergedPhone = phones.join(', ');

                // Bio
                let bios = this.currentGroup.map(i => i.bio).filter(b => b && b.trim() !== '');
                // Pick the longest bio
                if(bios.length > 0) {
                    this.mergedBio = bios.reduce((a, b) => a.length > b.length ? a : b);
                } else {
                    this.mergedBio = '';
                }
            }
        }));
    });
</script>
@endpush
