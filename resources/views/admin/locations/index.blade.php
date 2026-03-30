@extends('admin.layouts.app')
@section('title', 'Location Management')
@section('content')

<div x-data="{ activeTab: 'divisions' }">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Tab Bar & Bulk Import --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-1 bg-white dark:bg-gray-800 rounded-2xl p-1.5 shadow-sm border border-gray-100 dark:border-gray-700 w-fit">
            @foreach([['divisions','Divisions (বিভাগ)','🗺️'],['districts','Districts (জেলা)','🏙️'],['areas','Areas / Upazilas (উপজেলা)','📍']] as [$tab,$label,$icon])
            <button type="button" @click="activeTab = '{{ $tab }}'"
                    :class="activeTab === '{{ $tab }}' ? 'bg-sky-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all">
                {{ $icon }} {{ $label }}
            </button>
            @endforeach
        </div>

        <button onclick="document.getElementById('importModal').showModal()" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            Bulk Import Locations
        </button>
    </div>

    {{-- Import Modal --}}
    <dialog id="importModal" class="rounded-2xl shadow-2xl p-0 border-0 bg-transparent backdrop:bg-gray-900/50">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md p-6 rounded-2xl">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Bulk Import Locations (CSV)</h3>
            <p class="text-sm text-gray-500 mb-6 font-bangla">বিভাগ, জেলা এবং উপজেলা একসাথে ইম্পোর্ট করুন। নিচের CSV টেমপ্লেটটি ডাউনলোড করে তথ্য পূরণ করে আপলোড করুন।</p>
            
            <a href="{{ route('admin.templates.location') }}" class="flex items-center gap-2 text-sky-600 dark:text-sky-400 text-sm font-medium mb-6 hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download Location CSV Template
            </a>

            <form action="{{ route('admin.import.locations') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="file" name="file" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('importModal').close()" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-sky-600 text-white rounded-xl text-sm font-bold shadow-lg hover:bg-sky-700 transition-all">Upload & Import</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- ══ DIVISIONS TAB ══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'divisions'" x-transition>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Add Form --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add New Division
                </h3>
                <form method="POST" action="{{ route('admin.locations.divisions.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Name (English) *</label>
                        <input type="text" name="name_en" required placeholder="e.g. Dhaka" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Name (বাংলা)</label>
                        <input type="text" name="name_bn" placeholder="e.g. ঢাকা" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Slug (URL)</label>
                        <input type="text" name="slug" placeholder="e.g. dhaka" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    </div>
                    <button type="submit" class="w-full py-2 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold transition-colors">Add Division</button>
                </form>
            </div>

            {{-- List --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">All Divisions <span class="ml-1 px-2 py-0.5 text-xs bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-full">{{ count($divisions) }}</span></h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($divisions as $div)
                    <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-750" x-data="{ editing: false }">
                        <div x-show="!editing" class="flex-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $div->getTranslation('name','en') }}</span>
                            <span class="mx-2 text-gray-300">|</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $div->getTranslation('name','bn') }}</span>
                            <span class="ml-2 text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded">{{ $div->districts_count }} districts</span>
                        </div>
                        <form x-show="editing" method="POST" action="{{ route('admin.locations.divisions.update', $div->id) }}" class="flex-1 flex gap-2 items-center">
                            @csrf @method('PUT')
                            <input type="text" name="name_en" value="{{ $div->getTranslation('name','en') }}" class="flex-1 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="EN Name">
                            <input type="text" name="name_bn" value="{{ $div->getTranslation('name','bn') }}" class="flex-1 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="BN Name">
                            <input type="text" name="slug" value="{{ $div->slug }}" class="flex-1 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Slug">
                            <button type="submit" class="text-xs px-2 py-1 bg-green-500 text-white rounded">Save</button>
                        </form>
                        <div class="flex gap-2 ml-3">
                            <button type="button" @click="editing = !editing" class="text-xs px-2.5 py-1 rounded bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 hover:bg-sky-100 transition-colors">Edit</button>
                            <form method="POST" action="{{ route('admin.locations.divisions.destroy', $div->id) }}" onsubmit="return confirm('Delete this division and ALL its districts/areas?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs px-2.5 py-1 rounded bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 transition-colors">Delete</button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <p class="px-5 py-8 text-center text-sm text-gray-400">No divisions found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ══ DISTRICTS TAB ══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'districts'" x-transition>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Add Form --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add New District
                </h3>
                <form method="POST" action="{{ route('admin.locations.districts.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Division *</label>
                        <select name="division_id" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            <option value="">-- Select Division --</option>
                            @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->getTranslation('name','en') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Name (English) *</label>
                        <input type="text" name="name_en" required placeholder="e.g. Dhaka" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Name (বাংলা)</label>
                        <input type="text" name="name_bn" placeholder="e.g. ঢাকা" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Slug (URL)</label>
                        <input type="text" name="slug" placeholder="e.g. dhaka" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <button type="submit" class="w-full py-2 rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold transition-colors">Add District</button>
                </form>
            </div>

            {{-- List --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">All Districts <span class="ml-1 px-2 py-0.5 text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full">{{ count($districts) }}</span></h3>
                </div>
                <div class="max-h-[600px] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($districts as $dist)
                    <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-750" x-data="{ editing: false }">
                        <div x-show="!editing" class="flex-1">
                            <span class="text-xs text-indigo-500 font-semibold mr-2">{{ $dist->division->getTranslation('name','en') }}</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $dist->getTranslation('name','en') }}</span>
                            <span class="mx-2 text-gray-300">|</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $dist->getTranslation('name','bn') }}</span>
                            <span class="ml-2 text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 px-2 py-0.5 rounded">{{ $dist->areas_count }} areas</span>
                        </div>
                        <form x-show="editing" method="POST" action="{{ route('admin.locations.districts.update', $dist->id) }}" class="flex-1 flex gap-2 items-center">
                            @csrf @method('PUT')
                            <input type="text" name="name_en" value="{{ $dist->getTranslation('name','en') }}" class="flex-1 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="EN Name">
                            <input type="text" name="name_bn" value="{{ $dist->getTranslation('name','bn') }}" class="flex-1 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="BN Name">
                            <input type="text" name="slug" value="{{ $dist->slug }}" class="flex-1 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Slug">
                            <button type="submit" class="text-xs px-2 py-1 bg-green-500 text-white rounded">Save</button>
                        </form>
                        <div class="flex gap-2 ml-3 flex-shrink-0">
                            <button type="button" @click="editing = !editing" class="text-xs px-2.5 py-1 rounded bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 hover:bg-sky-100 transition-colors">Edit</button>
                            <form method="POST" action="{{ route('admin.locations.districts.destroy', $dist->id) }}" onsubmit="return confirm('Delete this district and all areas?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs px-2.5 py-1 rounded bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 transition-colors">Delete</button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <p class="px-5 py-8 text-center text-sm text-gray-400">No districts found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ══ AREAS / UPAZILAS TAB ═══════════════════════════════ --}}
    <div x-show="activeTab === 'areas'" x-transition
         x-data="{
            divisionId: '',
            districts: [],
            async fetchDistricts() {
                this.districts = [];
                if (!this.divisionId) return;
                let r = await fetch('/api/districts?division_id=' + this.divisionId);
                this.districts = await r.json();
            }
         }">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Add Form --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add New Area / Upazila
                </h3>
                <form method="POST" action="{{ route('admin.locations.areas.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Division</label>
                        <select x-model="divisionId" @change="fetchDistricts()" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                            <option value="">-- Select Division --</option>
                            @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->getTranslation('name','en') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">District *</label>
                        <select name="district_id" required :disabled="!divisionId" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 disabled:opacity-50">
                            <option value="">-- Select District --</option>
                            <template x-for="d in districts" :key="d.id">
                                <option :value="d.id" x-text="d.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Area Name (English) *</label>
                        <input type="text" name="name_en" required placeholder="e.g. Dhanmondi" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Area Name (বাংলা)</label>
                        <input type="text" name="name_bn" placeholder="e.g. ধানমন্ডি" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Slug (URL) <span class="text-[10px] text-gray-400 font-normal">(Leave empty to auto-generate)</span></label>
                        <input type="text" name="slug" placeholder="e.g. dhanmondi" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>
                    <button type="submit" class="w-full py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold transition-colors">Add Area</button>
                </form>
            </div>

            {{-- List --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200">
                        Areas / Upazilas
                        <span class="ml-1 px-2 py-0.5 text-xs bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full">{{ $areas->total() }}</span>
                    </h3>
                </div>
                <div class="max-h-[600px] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($areas as $area)
                    <div class="flex items-center justify-between px-5 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-750" x-data="{ editing: false }">
                        <div x-show="!editing" class="flex-1 min-w-0">
                            <span class="text-xs text-gray-400 mr-1">{{ $area->district?->division?->getTranslation('name','en') }} › {{ $area->district?->getTranslation('name','en') }}</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">» {{ $area->getTranslation('name','en') }}</span>
                            <span class="mx-1 text-gray-300">|</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $area->getTranslation('name','bn') }}</span>
                        </div>
                        <form x-show="editing" method="POST" action="{{ route('admin.locations.areas.update', $area->id) }}" class="flex-1 flex gap-2 items-center">
                            @csrf @method('PUT')
                            <input type="text" name="name_en" value="{{ $area->getTranslation('name','en') }}" class="flex-1 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="EN Name">
                            <input type="text" name="name_bn" value="{{ $area->getTranslation('name','bn') }}" class="flex-1 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="BN Name">
                            <input type="text" name="slug" value="{{ $area->slug }}" class="flex-1 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Slug">
                            <button type="submit" class="text-xs px-2 py-1 bg-green-500 text-white rounded">Save</button>
                        </form>
                        <div class="flex gap-2 ml-3 flex-shrink-0">
                            <button type="button" @click="editing = !editing" class="text-xs px-2.5 py-1 rounded bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 hover:bg-sky-100 transition-colors">Edit</button>
                            <form method="POST" action="{{ route('admin.locations.areas.destroy', $area->id) }}" onsubmit="return confirm('Delete this area?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs px-2.5 py-1 rounded bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 transition-colors">Del</button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <p class="px-5 py-8 text-center text-sm text-gray-400">No areas found.</p>
                    @endforelse
                </div>
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                    {{ $areas->links() }}
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
