@extends('admin.layouts.app')
@section('title', 'Manage Hospital Profile')

@section('content')
<div class="max-w-5xl mx-auto mb-16">
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-3xl p-6 md:p-8 mb-8 text-white relative overflow-hidden shadow-xl">
        <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -left-4 -bottom-4 w-32 h-32 bg-teal-400/20 rounded-full blur-xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-3xl font-black shadow-inner border border-white/30 overflow-hidden">
                    @if($hospital->logo)
                        <img src="{{ asset('storage/' . $hospital->logo) }}" class="w-full h-full object-cover">
                    @else
                        {{ mb_substr($hospital->name, 0, 1) }}
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-black">Manage Hospital</h1>
                    <p class="text-emerald-100 text-sm mt-0.5">Keep your institution's details up to date.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <form method="POST"
              action="{{ route('hospital.profile.update') }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @method('PUT')

            {{-- ════ CARD: BASIC INFO ════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Institution Details
                </h3>

                {{-- Logo Upload --}}
                @php
                    $hospitalLogoUrl = $hospital->logo ? asset('storage/' . $hospital->logo) : '';
                @endphp
                <div x-data="{
                        preview: @json($hospitalLogoUrl),
                        dragging: false,
                        handleFile(e) {
                            const file = e.dataTransfer ? e.dataTransfer.files[0] : e.target.files[0];
                            if (!file) return;
                            this.preview = URL.createObjectURL(file);
                        }
                    }" class="mb-6">
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-3">Institution Logo</label>
                    <div class="flex items-start gap-5">
                        <div class="w-24 h-24 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-600 overflow-hidden bg-gray-50 dark:bg-gray-700 flex items-center justify-center flex-shrink-0"
                             :class="dragging ? 'border-emerald-400 bg-emerald-50' : ''">
                            <img x-show="preview" :src="preview" class="w-full h-full object-cover rounded-2xl" alt="">
                            <div x-show="!preview" class="flex flex-col items-center gap-1 text-gray-300 dark:text-gray-600">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-[10px] font-medium uppercase tracking-wider">No Logo</span>
                            </div>
                        </div>
                        <div class="flex-1 space-y-2">
                            <div class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all"
                                 :class="dragging ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-emerald-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                                 @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                                 @drop.prevent="dragging=false; handleFile($event)" @click="$refs.photoInput.click()">
                                <input type="file" name="logo" x-ref="photoInput" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="handleFile($event)">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Drag & drop or <span class="text-emerald-600 font-semibold">browse</span></p>
                                <p class="text-xs text-gray-400 mt-0.5">JPEG/PNG/WebP, Max 2 MB</p>
                            </div>
                            @if($hospital->logo)
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-red-500 hover:text-red-600">
                                <input type="checkbox" name="remove_logo" value="1" class="rounded border-red-300 text-red-500 focus:ring-red-300" @change="if($el.checked) preview=''">
                                Remove current logo
                            </label>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Basic Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Institution Name <span class="text-red-400">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $hospital->name) }}"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Type <span class="text-red-400">*</span></label>
                        <select name="type" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                            <option value="hospital"   @selected(old('type', $hospital->type) === 'hospital')>General Hospital</option>
                            <option value="clinic"     @selected(old('type', $hospital->type) === 'clinic')>Clinic</option>
                            <option value="diagnostic" @selected(old('type', $hospital->type) === 'diagnostic')>Diagnostic Center</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Contact Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $hospital->phone) }}" placeholder="e.g. +8801XXXXXXXXX"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $hospital->email) }}" placeholder="info@hospital.com"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Website URL</label>
                        <input type="url" name="website" value="{{ old('website', $hospital->website) }}" placeholder="https://www..."
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">About</label>
                        <textarea name="about" rows="3" placeholder="Brief description of the facility and services..."
                                  class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 resize-none transition-colors">{{ old('about', $hospital->about) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ════ CARD: LOCATION ════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" 
                 x-data="locationPicker('{{ old('division_id', $hospital->area?->district?->division_id) }}', '{{ old('district_id', $hospital->area?->district_id) }}', '{{ old('area_id', $hospital->area_id) }}')">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Location & Map
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 bg-gray-50/50 dark:bg-gray-900/20 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1">Division</label>
                        <select id="division_id" x-model="divisionId" @change="fetchDistricts()" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-emerald-300">
                            <option value="">-- Division --</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}">{{ $div->getTranslation('name', 'en') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1">District</label>
                        <select id="district_id" x-model="districtId" @change="fetchAreas()" :disabled="!divisionId" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-emerald-300 disabled:opacity-50">
                            <option value="">-- District --</option>
                            <template x-for="dist in districts" :key="dist.id">
                                <option :value="dist.id" x-text="dist.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1">Area <span class="text-red-400">*</span></label>
                        <select id="area_id" name="area_id" x-model="areaId" :disabled="!districtId" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-emerald-300 disabled:opacity-50">
                            <option value="">-- Area --</option>
                            <template x-for="ar in areas" :key="ar.id">
                                <option :value="ar.id" x-text="ar.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Full Address <span class="text-red-400">*</span></label>
                        <input type="text" name="address" required value="{{ old('address', $hospital->address) }}" placeholder="Street Name, Building No."
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Google Maps URL</label>
                        <input type="url" name="google_maps_url" value="{{ old('google_maps_url', $hospital->google_maps_url) }}" placeholder="https://maps.google.com/..."
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                    </div>
                </div>
            </div>

            {{-- ════ CARD: SUBMIT ════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center justify-end gap-4">
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm font-bold hover:opacity-90 shadow-md hover:-translate-y-0.5 transition-all outline-none focus:ring-4 focus:ring-emerald-500/30">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
