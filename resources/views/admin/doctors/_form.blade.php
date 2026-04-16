<div class="max-w-4xl mx-auto">
    <form method="POST"
          action="{{ isset($doctor) ? route('admin.doctors.update', $doctor->id) : route('admin.doctors.store') }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @if(isset($doctor)) @method('PUT') @endif

        {{-- ════ CARD: PHOTO & BASIC INFO ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Personal Details
            </h3>

            {{-- Photo Upload --}}
            @php
                $doctorPhotoUrl = (isset($doctor) && $doctor->photo) ? asset('storage/' . $doctor->photo) : '';
            @endphp
            <div x-data="{
                    preview: @json($doctorPhotoUrl),
                    dragging: false,
                    handleFile(e) {
                        const file = e.dataTransfer ? e.dataTransfer.files[0] : e.target.files[0];
                        if (!file) return;
                        this.preview = URL.createObjectURL(file);
                    }
                }" class="mb-6">
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-3">Profile Photo</label>
                <div class="flex items-start gap-5">
                    <div class="w-24 h-24 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-600 overflow-hidden bg-gray-50 dark:bg-gray-700 flex items-center justify-center flex-shrink-0"
                         :class="dragging ? 'border-sky-400 bg-sky-50' : ''">
                        <img x-show="preview" :src="preview" class="w-full h-full object-cover rounded-2xl" alt="">
                        <div x-show="!preview" class="flex flex-col items-center gap-1 text-gray-300 dark:text-gray-600">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span class="text-[10px] font-medium uppercase tracking-wider">No Photo</span>
                        </div>
                    </div>
                    <div class="flex-1 space-y-2">
                        <div class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all"
                             :class="dragging ? 'border-sky-400 bg-sky-50 dark:bg-sky-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-sky-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                             @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                             @drop.prevent="dragging=false; handleFile($event)" @click="$refs.photoInput.click()">
                            <input type="file" name="photo" x-ref="photoInput" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="handleFile($event)">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Drag & drop or <span class="text-sky-600 font-semibold">browse</span></p>
                            <p class="text-xs text-gray-400 mt-0.5">JPEG · PNG · WebP · Max 2 MB</p>
                        </div>
                        @if(isset($doctor) && $doctor->photo)
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-red-500 hover:text-red-600">
                            <input type="checkbox" name="remove_photo" value="1" class="rounded border-red-300 text-red-500 focus:ring-red-300" @change="if($el.checked) preview=''">
                            Remove current photo
                        </label>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cover Image Upload --}}
            @php
                $doctorCoverUrl = (isset($doctor) && $doctor->cover_image) ? asset('storage/' . $doctor->cover_image) : '';
            @endphp
            <div x-data="{
                    preview: @json($doctorCoverUrl),
                    dragging: false,
                    handleFile(e) {
                        const file = e.dataTransfer ? e.dataTransfer.files[0] : e.target.files[0];
                        if (!file) return;
                        this.preview = URL.createObjectURL(file);
                    }
                }" class="mb-6 border-t border-gray-100 dark:border-gray-700 pt-6">
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-3">Cover Image / Banner</label>
                <div class="flex flex-col sm:flex-row items-start gap-5">
                    <div class="w-full sm:w-48 h-24 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-600 overflow-hidden bg-gray-50 dark:bg-gray-700 flex items-center justify-center flex-shrink-0"
                         :class="dragging ? 'border-sky-400 bg-sky-50' : ''">
                        <img x-show="preview" :src="preview" class="w-full h-full object-cover rounded-xl" alt="">
                        <div x-show="!preview" class="flex flex-col items-center gap-1 text-gray-300 dark:text-gray-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-[10px] font-medium uppercase tracking-wider">No Cover</span>
                        </div>
                    </div>
                    <div class="flex-1 space-y-2 w-full">
                        <div class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all"
                             :class="dragging ? 'border-sky-400 bg-sky-50 dark:bg-sky-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-sky-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                             @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                             @drop.prevent="dragging=false; handleFile($event)" @click="$refs.coverInput.click()">
                            <input type="file" name="cover_image" x-ref="coverInput" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="handleFile($event)">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Drag & drop or <span class="text-sky-600 font-semibold">browse</span></p>
                            <p class="text-xs text-gray-400 mt-0.5">JPEG/PNG/WebP · Recommended: 1200x400px</p>
                        </div>
                        @if(isset($doctor) && $doctor->cover_image)
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-red-500 hover:text-red-600">
                            <input type="checkbox" name="remove_cover_image" value="1" class="rounded border-red-300 text-red-500 focus:ring-red-300" @change="if($el.checked) preview=''">
                            Remove cover image
                        </label>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Basic Fields --}}
            <div x-data="{ activeTab: 'en' }">
                <div class="flex gap-2 mb-5 border-b border-gray-100 dark:border-gray-700 pb-3">
                    <button type="button" @click="activeTab = 'en'" 
                            :class="activeTab === 'en' ? 'bg-sky-500 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'" 
                            class="px-5 py-2 rounded-xl text-sm font-bold transition-all focus:outline-none">
                        English (Default)
                    </button>
                    <button type="button" @click="activeTab = 'bn'" 
                            :class="activeTab === 'bn' ? 'bg-emerald-500 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'" 
                            class="px-5 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2 focus:outline-none">
                        Bengali (বাংলা)
                        <span class="text-[9px] uppercase tracking-wide bg-white/20 text-white px-1.5 py-0.5 rounded-full" x-show="activeTab === 'bn'">Translating</span>
                    </button>
                </div>

                <!-- ENGLISH TAB -->
                <div x-show="activeTab === 'en'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Full Name (English) <span class="text-red-400">*</span></label>
                        <input type="text" name="name[en]" required value="{{ old('name.en', isset($doctor) ? $doctor->getTranslation('name', 'en', false) : '') }}" placeholder="e.g. Dr. John Doe"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 transition-colors">
                        @error('name.en')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Designation (English)</label>
                        <input type="text" name="designation[en]" value="{{ old('designation.en', isset($doctor) ? $doctor->getTranslation('designation', 'en', false) : '') }}" placeholder="e.g. Senior Consultant"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Qualifications (English)</label>
                        <input type="text" name="qualifications[en]" value="{{ old('qualifications.en', isset($doctor) ? $doctor->getTranslation('qualifications', 'en', false) : '') }}" placeholder="MBBS, FCPS"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">Professional Bio (English)</label>
                            <button type="button" onclick="generateAiContent('doctor_bio', 'tinymce:bio_en', this)" class="text-[10px] bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-sky-200 transition-colors z-50 relative">
                                ✨ Auto Generate Bio
                            </button>
                        </div>
                        <textarea name="bio[en]" id="bio_en" rows="8" placeholder="Short professional background..."
                                  class="tinymce-editor w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 resize-none transition-colors">{{ old('bio.en', isset($doctor) ? $doctor->getTranslation('bio', 'en', false) : '') }}</textarea>
                    </div>
                </div>

                <!-- BENGALI TAB -->
                <div x-show="activeTab === 'bn'" style="display:none;" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-emerald-50/30 dark:bg-emerald-900/10 p-4 rounded-xl border border-emerald-100 dark:border-emerald-800/30">
                    <div class="md:col-span-2 flex justify-end">
                        <button type="button" onclick="autoTranslateToBengali(this)" class="px-3 py-1.5 text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300 rounded-lg shadow-sm border border-emerald-200 dark:border-emerald-700 hover:bg-emerald-200 dark:hover:bg-emerald-800 transition-colors flex items-center gap-1.5">
                            <span class="btn-text">✨ Auto Translate to Bengali (AI)</span>
                            <svg class="btn-spinner hidden w-4 h-4 animate-spin text-emerald-600 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-emerald-800 dark:text-emerald-300 block mb-1.5">Full Name (Bengali)</label>
                        <input type="text" name="name[bn]" value="{{ old('name.bn', isset($doctor) ? $doctor->getTranslation('name', 'bn', false) : '') }}" placeholder="উদাঃ ডাঃ জন ডো"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-colors text-gray-800 dark:text-gray-200">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-emerald-800 dark:text-emerald-300 block mb-1.5">Designation (Bengali)</label>
                        <input type="text" name="designation[bn]" value="{{ old('designation.bn', isset($doctor) ? $doctor->getTranslation('designation', 'bn', false) : '') }}" placeholder="উদাঃ সিনিয়র কনসালটেন্ট"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-colors text-gray-800 dark:text-gray-200">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-emerald-800 dark:text-emerald-300 block mb-1.5">Qualifications (Bengali)</label>
                        <input type="text" name="qualifications[bn]" value="{{ old('qualifications.bn', isset($doctor) ? $doctor->getTranslation('qualifications', 'bn', false) : '') }}" placeholder="এমবিবিএস, এফসিপিএস"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-colors text-gray-800 dark:text-gray-200">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-emerald-800 dark:text-emerald-300 block mb-1.5">Professional Bio (Bengali)</label>
                        <textarea name="bio[bn]" id="bio_bn" rows="8" placeholder="তার পেশাগত জীবন সম্পর্কে সংক্ষেপে লিখুন..."
                                  class="tinymce-editor w-full px-3 py-2 text-sm rounded-xl border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-400 resize-none transition-colors">{{ old('bio.bn', isset($doctor) ? $doctor->getTranslation('bio', 'bn', false) : '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Global / Technical Fields --}}
            <div class="mt-8 border-t border-gray-100 dark:border-gray-700 pt-6">
                <h4 class="text-xs tracking-wider uppercase font-bold text-gray-400 mb-4">Core Settings (Applies to all languages)</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Custom Slug --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Custom Slug (URL) <span class="text-gray-400 font-normal whitespace-nowrap">(Leave empty to auto-generate)</span></label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-500 text-xs text-nowrap">doctorbd24.com/doctor/</span>
                            <input type="text" name="slug" value="{{ old('slug', $doctor->slug ?? '') }}" placeholder="dr-john-doe"
                                   class="flex-1 w-full px-3 py-2 text-sm rounded-r-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 focus:bg-white dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 transition-colors">
                        </div>
                        @error('slug')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Gender <span class="text-red-400">*</span></label>
                        <select name="gender" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 transition-colors">
                            <option value="male"   @selected(old('gender', $doctor->gender ?? 'male') === 'male')>Male</option>
                            <option value="female" @selected(old('gender', $doctor->gender ?? '') === 'female')>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Phone (Personal)</label>
                        <input type="text" name="phone" value="{{ old('phone', $doctor->phone ?? '') }}" placeholder="01XXXXXXXXX"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 transition-colors">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $doctor->email ?? '') }}" placeholder="doctor@example.com"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 transition-colors">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Experience (Years)</label>
                        <input type="number" name="experience_years" value="{{ old('experience_years', $doctor->experience_years ?? 0) }}" min="0"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 transition-colors">
                    </div>
                </div>
            </div>
        </div>

        {{-- ════ CARD: SPECIALTIES ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" x-data="{ searchSp: '' }">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    Specialties
                </h3>
                <div class="relative w-full md:w-64">
                    <input type="text" x-model="searchSp" placeholder="Search specialty..." 
                           class="w-full pl-8 pr-3 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 focus:ring-2 focus:ring-indigo-300">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-[350px] overflow-y-auto pr-2">
                @foreach($specialties as $sp)
                <label x-show="'{{ strtolower($sp->getTranslation('name', 'en')) }}'.includes(searchSp.toLowerCase())"
                       class="flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-indigo-300 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10 cursor-pointer transition-colors bg-gray-50/50 dark:bg-gray-700/30">
                    <input type="checkbox" name="specialties[]" value="{{ $sp->id }}"
                           @checked(in_array($sp->id, $doctorSpecialties ?? []))
                           class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-300 flex-shrink-0">
                    <span class="text-sm text-gray-700 dark:text-gray-200 font-medium truncate flex items-center gap-2">
                        <span class="text-xl">{{ $sp->icon }}</span>
                        {{ $sp->getTranslation('name', 'en') }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- ════ CARD: CHAMBERS (ACCORDION & AUTO-FILL) ════ --}}
        @php
            // Prepare Hospital Data for Alpine Auto-fill
            $hospitalsJs = $hospitals->map(function($h) {
                return [
                    'id' => (string) $h->id,
                    'name' => $h->name,
                    'area_id' => (string) $h->area_id,
                    'address' => $h->address,
                    'phone' => $h->phone,
                    'google_maps_url' => $h->google_maps_url,
                    'lat' => $h->lat,
                    'lng' => $h->lng
                ];
            })->keyBy('id');
            
            $chambersJs = isset($doctor) ? $doctor->chambers->map(fn($c) => [
                    'id'             => $c->id,
                    'name'           => $c->name,
                    'hospital_id'    => (string) $c->hospital_id,
                    'division_id'    => (string) ($c->area?->district?->division_id ?? ''),
                    'district_id'    => (string) ($c->area?->district_id ?? ''),
                    'area_id'        => (string) $c->area_id,
                    'address'        => $c->address,
                    'visiting_hours' => $c->visiting_hours,
                    'closed_days'    => $c->closed_days ?? '',
                    'phone'          => $c->phone,
                    'google_maps_url'=> $c->google_maps_url ?? '',
                    'lat'            => $c->lat,
                    'lng'            => $c->lng,
                    'districts'      => [], // To be populated
                    'areas'          => [],     // To be populated
                ])->toArray() : [];
        @endphp

        @include('admin.shared._tinymce')
        <script>
            const setupChamberManager = () => {
                Alpine.data('chamberManager', () => {
                    return {
                        hospitalsData: @json($hospitalsJs ?: new \stdClass()),
                        chambers: @json($chambersJs),
                        
                        init() {
                            this.chambers.forEach(chamber => {
                                this.initChamber(chamber);
                            });
                        },

                        async initChamber(chamber) {
                            try {
                                if (chamber.division_id) {
                                    let res = await fetch('/api/districts?division_id=' + chamber.division_id);
                                    chamber.districts = await res.json();
                                } else {
                                    chamber.districts = [];
                                }
                                if (chamber.district_id) {
                                    let res = await fetch('/api/areas?district_id=' + chamber.district_id);
                                    chamber.areas = await res.json();
                                } else {
                                    chamber.areas = [];
                                }
                            } catch(e) { console.error(e); }
                        },

                        addChamber() {
                            this.chambers.push({ 
                                id: null, name: '', hospital_id: '', 
                                division_id: '', district_id: '', area_id: '', 
                                address: '', visiting_hours: '', closed_days: '', 
                                phone: '', google_maps_url: '', lat: '', lng: '',
                                districts: [], areas: [] 
                            });
                        },

                        removeChamber(i) {
                            this.chambers.splice(i, 1);
                        },

                        async fetchDistricts(index) {
                            let chamber = this.chambers[index];
                            chamber.districts = [];
                            chamber.areas = [];
                            chamber.district_id = '';
                            chamber.area_id = '';
                            
                            if (!chamber.division_id) return;
                            try {
                                let res = await fetch('/api/districts?division_id=' + chamber.division_id);
                                chamber.districts = await res.json();
                            } catch(e) { console.error(e); }
                        },

                        async fetchAreas(index) {
                            let chamber = this.chambers[index];
                            chamber.areas = [];
                            chamber.area_id = '';
                            
                            if (!chamber.district_id) return;
                            try {
                                let res = await fetch('/api/areas?district_id=' + chamber.district_id);
                                chamber.areas = await res.json();
                            } catch(e) { console.error(e); }
                        },

                        async fillHospitalData(index) {
                            let hospId = this.chambers[index].hospital_id;
                            if (hospId && this.hospitalsData[hospId]) {
                                let data = this.hospitalsData[hospId];
                                if(!this.chambers[index].name) this.chambers[index].name = data.name;
                                
                                if (data.area_id) {
                                    try {
                                        let res = await fetch('/api/areas/' + data.area_id);
                                        let areaDetails = await res.json();
                                        if (areaDetails) {
                                            this.chambers[index].division_id = areaDetails.division_id;
                                            await this.fetchDistricts(index);
                                            this.chambers[index].district_id = areaDetails.district_id;
                                            await this.fetchAreas(index);
                                            this.chambers[index].area_id = data.area_id;
                                        }
                                    } catch(e) { console.error(e); }
                                }
                                if(data.address) this.chambers[index].address = data.address;
                                if(data.phone) this.chambers[index].phone = data.phone;
                                if(data.google_maps_url) this.chambers[index].google_maps_url = data.google_maps_url;
                                if(data.lat) this.chambers[index].lat = data.lat;
                                if(data.lng) this.chambers[index].lng = data.lng;
                            }
                        }
                    };
                });
            };

            if (window.Alpine) {
                setupChamberManager();
            } else {
                document.addEventListener('alpine:init', setupChamberManager);
            }
        </script>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6"
             x-data="chamberManager">

            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Chambers / Clinics
                    <span class="ml-1 px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 text-xs" x-text="chambers.length"></span>
                </h3>
                <button type="button" @click="addChamber()"
                        class="flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white transition-colors font-medium shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Add Chamber
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(chamber, index) in chambers" :key="index">
                    <div x-data="{ expanded: true, showAdvanced: false }" class="border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
                        
                        {{-- Accordion Header --}}
                        <div class="flex items-center justify-between p-4 bg-gray-50/80 dark:bg-gray-700/50 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                             @click="expanded = !expanded">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200" x-text="chamber.name || 'New Chamber'"></h4>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5" x-text="chamber.hospital_id ? 'Linked to Hospital' : 'Private Chamber'"></p>
                                </div>
                            </div>
                            <button type="button" @click.stop="removeChamber(index)"
                                    class="w-8 h-8 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/30 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>

                        {{-- Accordion Body --}}
                        <div x-show="expanded" x-collapse class="p-5 border-t border-gray-100 dark:border-gray-700">
                            <input type="hidden" :name="'chambers[' + index + '][id]'" :value="chamber.id ?? ''">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Chamber Name & Link Hospital --}}
                                <div class="bg-blue-50/50 dark:bg-blue-900/10 p-4 rounded-xl border border-blue-100 dark:border-blue-800/30 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1">Chamber / Clinic Name <span class="text-red-400">*</span></label>
                                        <input type="text" :name="'chambers[' + index + '][name]'" x-model="chamber.name" required placeholder="e.g. Popular Diagnostic Center"
                                               class="w-full px-3 py-2 text-sm rounded-lg border border-blue-200 dark:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                                    </div>
                                    <div class="z-20">
                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1 flex justify-between">
                                            <span>Link to Hospital / Clinic</span>
                                            <span class="text-[10px] text-blue-500 font-normal">Auto-fills address & map</span>
                                        </label>
                                        
                                        <div x-data="{ open: false, dSearch: '' }" @click.away="open = false" class="relative">
                                            <!-- trigger -->
                                            <button type="button" @click="open = !open"
                                                    class="w-full flex justify-between items-center px-3 py-2 text-sm rounded-lg border border-blue-200 dark:border-blue-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-400 text-left">
                                                <span class="truncate pr-2" x-text="chamber.hospital_id && hospitalsData[chamber.hospital_id] ? hospitalsData[chamber.hospital_id].name : '-- Private Chamber --'"></span>
                                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <!-- hidden real input -->
                                            <input type="hidden" :name="'chambers[' + index + '][hospital_id]'" x-model="chamber.hospital_id">
                                            
                                            <!-- dropdown -->
                                            <div x-show="open" x-transition.opacity.duration.200ms
                                                 class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl overflow-hidden">
                                                <div class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                                                    <input type="text" x-model="dSearch" placeholder="Search hospital..."
                                                           class="w-full px-3 py-1.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-400 focus:outline-none text-gray-800 dark:text-white">
                                                </div>
                                                <div class="max-h-48 overflow-y-auto p-1">
                                                    <div @click="chamber.hospital_id = ''; fillHospitalData(index); open = false"
                                                         class="px-3 py-2 text-sm cursor-pointer rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                        -- Private Chamber --
                                                    </div>
                                                    <template x-for="h in Object.values(hospitalsData).filter(h => h.name.toLowerCase().includes(dSearch.toLowerCase()))" :key="h.id">
                                                        <div @click="chamber.hospital_id = h.id; fillHospitalData(index); open = false"
                                                             class="px-3 py-2 text-sm cursor-pointer rounded text-gray-800 dark:text-gray-200"
                                                             :class="chamber.hospital_id == h.id ? 'bg-blue-50 dark:bg-blue-900/30 font-medium text-blue-600 dark:text-blue-400' : 'hover:bg-blue-50 dark:hover:bg-blue-900/30'">
                                                            <span x-text="h.name"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Cascading Location --}}
                                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50/50 dark:bg-gray-900/10 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1">Division</label>
                                        <select :name="'chambers[' + index + '][division_id]'" x-model="chamber.division_id" @change="fetchDistricts(index)"
                                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-emerald-300">
                                            <option value="">-- Division --</option>
                                            @foreach($divisions as $div)
                                                <option value="{{ $div->id }}">{{ $div->getTranslation('name', 'en') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1">District</label>
                                        <select :name="'chambers[' + index + '][district_id]'" x-model="chamber.district_id" @change="fetchAreas(index)" :disabled="!chamber.division_id"
                                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-emerald-300 disabled:opacity-50">
                                            <option value="">-- District --</option>
                                            <template x-for="dist in chamber.districts" :key="dist.id">
                                                <option :value="dist.id" x-text="dist.name" :selected="dist.id == chamber.district_id"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1">Area <span class="text-red-400">*</span></label>
                                        <select :name="'chambers[' + index + '][area_id]'" x-model="chamber.area_id" :disabled="!chamber.district_id" required
                                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-emerald-300 disabled:opacity-50">
                                            <option value="">-- Area --</option>
                                            <template x-for="ar in chamber.areas" :key="ar.id">
                                                <option :value="ar.id" x-text="ar.name" :selected="ar.id == chamber.area_id"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">📞 Appointment Phone</label>
                                    <input type="text" :name="'chambers[' + index + '][phone]'" x-model="chamber.phone" placeholder="01XXXXXXXXX"
                                           class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 focus:ring-2 focus:ring-emerald-300">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Full Address</label>
                                    <input type="text" :name="'chambers[' + index + '][address]'" x-model="chamber.address" placeholder="Road 12, House 5, Dhanmondi, Dhaka"
                                           class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 focus:ring-2 focus:ring-emerald-300">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">📅 Visiting Hours</label>
                                    <input type="text" :name="'chambers[' + index + '][visiting_hours]'" x-model="chamber.visiting_hours" placeholder="e.g. Sat-Thu: 5PM-9PM"
                                           class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 focus:ring-2 focus:ring-emerald-300">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">🚫 Closed Days</label>
                                    <input type="text" :name="'chambers[' + index + '][closed_days]'" x-model="chamber.closed_days" placeholder="e.g. Friday"
                                           class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 focus:ring-2 focus:ring-emerald-300">
                                </div>

                                {{-- Advanced Location Toggle --}}
                                <div class="md:col-span-2 mt-2">
                                    <button type="button" @click="showAdvanced = !showAdvanced"
                                            class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 hover:underline">
                                        <svg class="w-4 h-4 transition-transform duration-200" :class="showAdvanced ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        Advanced Location Mapping (Google Maps, Lat/Lng)
                                    </button>
                                    
                                    <div x-show="showAdvanced" x-collapse class="mt-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">🗺️ Google Maps URL</label>
                                            <input type="url" :name="'chambers[' + index + '][google_maps_url]'" x-model="chamber.google_maps_url" placeholder="https://maps.google.com/..."
                                                   class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-emerald-300">
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Latitude</label>
                                            <input type="number" step="any" :name="'chambers[' + index + '][lat]'" x-model="chamber.lat" placeholder="23.8103"
                                                   class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-emerald-300">
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Longitude</label>
                                            <input type="number" step="any" :name="'chambers[' + index + '][lng]'" x-model="chamber.lng" placeholder="90.4125"
                                                   class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-emerald-300">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Empty state --}}
                <div x-show="chambers.length === 0"
                     class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center cursor-pointer hover:border-emerald-300 hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10 transition-colors"
                     @click="addChamber()">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center mx-auto mb-2 text-emerald-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No chambers added</p>
                    <p class="text-xs text-emerald-500 mt-1">Click here to add the first chamber</p>
                </div>
            </div>
        </div>

        {{-- ════ CARD: MEDIA & CONTENT TABS (VIDEOS AND BLOGS) ════ --}}
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/10 dark:to-purple-900/10 rounded-2xl shadow-sm border border-indigo-100 dark:border-indigo-800/50 p-6" 
             x-data="{ 
                videos: {{ isset($doctor) && $doctor->doctorVideos->count() > 0 ? $doctor->doctorVideos->map(function($v){ return ['id' => $v->id, 'title' => $v->title, 'url' => $v->video_url, 'description' => $v->description]; })->toJson() : '[]' }},
                blogs: {{ empty(isset($doctor) && $doctor->blogs) ? '[]' : (is_string($doctor->blogs[0] ?? null) ? json_encode(array_map(function($url){ return ['title'=>'Linked Content', 'url'=>$url]; }, $doctor->blogs)) : json_encode($doctor->blogs)) }},
                newVideoUrl: '',
                newChannelUrl: '',
                newBlogUrl: '',
                isFetchingVideo: false,
                isFetchingChannel: false,
                isFetchingBlog: false,
                youtubePageToken: null,
                youtubeChannelId: null,
                youtubeAllFetched: false,
                
                init() {
                    this.$watch('videos', value => {
                        document.querySelector('input[name=videos]').value = JSON.stringify(value);
                    });
                    this.$watch('blogs', value => {
                        document.querySelector('input[name=blogs]').value = JSON.stringify(value);
                    });
                    this.$watch('newChannelUrl', () => {
                        this.youtubePageToken = null;
                        this.youtubeChannelId = null;
                        this.youtubeAllFetched = false;
                    });
                },
                
                fetchMeta(url, idx, arrayName) {
                    fetch('{{ route('admin.hospitals.fetch-url-meta') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ url: url })
                    }).then(res => res.json()).then(data => {
                        if(data.title) { this[arrayName][idx].title = data.title; }
                        if(data.image) { this[arrayName][idx].image = data.image; }
                    }).catch(() => {
                        this[arrayName][idx].title = 'Linked Article/Media';
                    });
                },

                addVideo(fetchedUrl = null) {
                    let vUrl = fetchedUrl || this.newVideoUrl.trim();
                    if(vUrl && !this.videos.find(v => v.url === vUrl)) {
                        let obj = { title: 'Fetching title...', url: vUrl };
                        this.videos.push(obj);
                        let idx = this.videos.length - 1;
                        this.fetchMeta(vUrl, idx, 'videos');
                    }
                    if(!fetchedUrl) this.newVideoUrl = '';
                },
                
                fetchChannelVideo() {
                    let cUrl = this.newChannelUrl.trim();
                    if ((!cUrl && !this.youtubeChannelId) || this.youtubeAllFetched) return;
                    this.isFetchingChannel = true;
                    
                    fetch('{{ route('admin.hospitals.fetch-channel-videos') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ 
                            url: cUrl, 
                            pageToken: this.youtubePageToken,
                            channelId: this.youtubeChannelId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.videos && data.videos.length > 0) {
                            const newUniqueVideos = data.videos.filter(newVid => !this.videos.some(existing => existing.url === newVid.url));
                            this.videos = [...newUniqueVideos, ...this.videos];
                            this.youtubePageToken = data.nextPageToken || null;
                            if (data.channelId) this.youtubeChannelId = data.channelId;
                            
                            if (this.youtubePageToken) {
                                alert(`Successfully added ${newUniqueVideos.length} new unique videos! (Total now: ${this.videos.length}). You can click 'Fetch Next 50' to get more.`);
                            } else {
                                this.youtubeAllFetched = true;
                                alert(`Successfully added ${newUniqueVideos.length} new unique videos! (Total now: ${this.videos.length}). All videos from this channel have been fetched!`);
                            }
                        } else {
                            alert(data.error || 'No more videos found in this channel.');
                            this.youtubeAllFetched = true;
                        }
                    })
                    .catch(() => alert('Error fetching channel. Make sure the API is working.'))
                    .finally(() => this.isFetchingChannel = false);
                },

                addBlog(fetchedUrl = null) {
                    let bUrl = fetchedUrl || this.newBlogUrl.trim();
                    if(bUrl && !this.blogs.find(b => b.url === bUrl)) {
                        let obj = { title: 'Fetching title...', url: bUrl };
                        this.blogs.push(obj);
                        let idx = this.blogs.length - 1;
                        this.fetchMeta(bUrl, idx, 'blogs');
                    }
                    if(!fetchedUrl) this.newBlogUrl = '';
                },

                fetchVideo() {
                    let name = document.querySelector('input[name=name]').value;
                    if (!name) { alert('Please enter the Doctor Name first.'); return; }
                    this.isFetchingVideo = true;
                    fetch('{{ route('admin.hospitals.fetch-video') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ query: name })
                    }).then(res => res.json()).then(data => {
                        if (data.url) { 
                            this.addVideo(data.url); 
                        }
                        else { alert('No video found'); }
                    }).catch(() => alert('Error fetching video')).finally(() => this.isFetchingVideo = false);
                },

                fetchBlog() {
                    let name = document.querySelector('input[name=name]').value;
                    if (!name) { alert('Please enter the Doctor Name first.'); return; }
                    this.isFetchingBlog = true;
                    fetch('{{ route('admin.hospitals.fetch-blog') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ query: name })
                    }).then(res => res.json()).then(data => {
                        if (data.url) { this.addBlog(data.url); }
                    }).catch(() => alert('Error fetching blog')).finally(() => this.isFetchingBlog = false);
                },

                isGeneratingAll: false,
                generateMissingDescriptions() {
                    this.isGeneratingAll = true;
                    this.generateNextMissing(0);
                },
                generateNextMissing(index) {
                    if (index >= this.videos.length) {
                        this.isGeneratingAll = false;
                        alert('All missing descriptions have been successfully generated!');
                        return;
                    }

                    if (!this.videos[index].description) {
                        let name = document.querySelector('input[name=name]').value;
                        fetch('{{ route('admin.hospitals.generate-video-description') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ title: this.videos[index].title, hospital_name: name })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.description) {
                                this.videos[index].description = data.description;
                            }
                            this.generateNextMissing(index + 1);
                        })
                        .catch(() => this.generateNextMissing(index + 1));
                    } else {
                        this.generateNextMissing(index + 1);
                    }
                }
            }">
            <div class="mb-5">
                <h3 class="text-sm font-bold text-indigo-900 dark:text-indigo-300 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Media & Content Tabs
                </h3>
                <p class="text-xs text-indigo-600/70 dark:text-indigo-400/70 mt-1">Add videos and blog links. These will dynamically create Tabs on the Doctor's public profile.</p>
            </div>

            <input type="hidden" name="videos" :value="JSON.stringify(videos)">
            <input type="hidden" name="blogs" :value="JSON.stringify(blogs)">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Videos Section --}}
                <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col h-full">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2 flex items-center gap-1.5 focus:outline-none">
                        <span class="w-6 h-6 rounded bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-500">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </span>
                        Videos (Multi-Platform)
                        
                        <button type="button" @click="generateMissingDescriptions()" :disabled="isGeneratingAll || videos.length === 0" 
                                class="ml-auto flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-md bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                            <span x-show="isGeneratingAll" class="w-3 h-3 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></span>
                            <span x-show="!isGeneratingAll">✨ Auto-Generate AI Desc</span>
                        </button>
                    </label>
                    
                    <div class="flex flex-col gap-2 mb-3 border-b border-gray-100 dark:border-gray-700 pb-3">
                        <div class="flex gap-2">
                            <input type="url" x-model="newVideoUrl" @keydown.enter.prevent="addVideo()" placeholder="Paste a video URL..."
                                class="flex-1 w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-all">
                            <button type="button" @click="addVideo()" class="px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-semibold rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors shrink-0">
                                Add
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <input type="url" x-model="newChannelUrl" @keydown.enter.prevent="fetchChannelVideo()" placeholder="Paste Channel URL..."
                                class="flex-1 w-full px-3 py-2 text-sm border-dashed border-red-200 dark:border-red-900/50 rounded-lg border bg-red-50/50 focus:bg-white dark:bg-red-900/10 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-400 transition-all">
                            <button type="button" @click="fetchChannelVideo()" :disabled="isFetchingChannel" class="px-3 py-2 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 font-semibold rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors shrink-0 flex items-center min-w-[120px] justify-center text-xs">
                                <span x-show="isFetchingChannel" class="w-3.5 h-3.5 border-2 border-red-500 border-t-transparent rounded-full animate-spin mr-1" x-cloak></span>
                                <span x-show="!isFetchingChannel" class="whitespace-nowrap" x-text="youtubeAllFetched ? 'All Fetched' : (youtubePageToken ? 'Fetch Next ~50' : 'Fetch (~50)')"></span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5 mb-3 flex-1 overflow-y-auto max-h-[250px] pr-1">
                        <template x-for="(vid, index) in videos" :key="index">
                            <div class="group flex items-start justify-between gap-2 p-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm relative">
                                <div class="overflow-hidden w-full pr-6">
                                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate" x-text="vid.title"></p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <p class="text-[10px] text-gray-500 truncate max-w-[120px] sm:max-w-[200px]" x-text="vid.url"></p>
                                        <template x-if="vid.description">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400">📝 Desc Added</span>
                                        </template>
                                        <template x-if="!vid.description">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400">No Desc</span>
                                        </template>
                                    </div>
                                </div>
                                <button type="button" @click="videos.splice(index, 1)" class="absolute top-2.5 right-2.5 text-gray-400 hover:text-red-600 bg-white dark:bg-gray-800 rounded transition-colors p-0.5" title="Remove Video">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                        <div x-show="videos.length === 0" class="text-center py-4 border border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                            <p class="text-xs text-gray-400">No videos added yet.</p>
                        </div>
                    </div>
                    
                    <button type="button" @click="fetchVideo()" class="w-full bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800 transition-colors px-3 py-2 rounded-lg flex items-center justify-center gap-1.5 cursor-pointer text-xs font-bold shadow-sm mt-auto">
                        <span x-show="isFetchingVideo" class="w-3.5 h-3.5 border-2 border-red-500 border-t-transparent rounded-full animate-spin" x-cloak></span>
                        <span x-show="!isFetchingVideo">Search YouTube & Add Auto</span>
                    </button>
                </div>
                
                {{-- Blogs Section --}}
                <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col h-full">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2 flex items-center gap-1.5 focus:outline-none">
                        <span class="w-6 h-6 rounded bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </span>
                        Related Blog Articles
                    </label>

                    <div class="flex gap-2 mb-3">
                        <input type="url" x-model="newBlogUrl" @keydown.enter.prevent="addBlog()" placeholder="https://example.com/blog..."
                               class="flex-1 w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-all">
                        <button type="button" @click="addBlog()" class="px-3 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-semibold rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors shrink-0">
                            Add
                        </button>
                    </div>

                    <div class="space-y-1.5 mb-3 flex-1 overflow-y-auto max-h-[150px] pr-1">
                        <template x-for="(bg, index) in blogs" :key="index">
                            <div class="group flex items-start justify-between gap-2 p-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm relative">
                                <div class="w-10 h-10 shrink-0 rounded bg-gray-100 dark:bg-gray-700 overflow-hidden flex items-center justify-center text-gray-400">
                                    <template x-if="bg.image">
                                        <img :src="bg.image" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!bg.image">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.96-2.36L6.5 17h11l-3.54-4.71z"/></svg>
                                    </template>
                                </div>
                                <div class="overflow-hidden flex-1">
                                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate pr-6" x-text="bg.title"></p>
                                    <p class="text-[10px] text-gray-500 truncate" x-text="bg.url"></p>
                                </div>
                                <button type="button" @click="blogs.splice(index, 1)" class="absolute top-2.5 right-2.5 text-gray-400 hover:text-red-600 bg-white dark:bg-gray-800 rounded transition-colors p-0.5" title="Remove Blog">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                        <div x-show="blogs.length === 0" class="text-center py-4 border border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                            <p class="text-xs text-gray-400">No blogs added yet.</p>
                        </div>
                    </div>

                    <button type="button" @click="fetchBlog()" class="w-full bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 transition-colors px-3 py-2 rounded-lg flex items-center justify-center gap-1.5 cursor-pointer text-xs font-bold shadow-sm">
                        <span x-show="isFetchingBlog" class="w-3.5 h-3.5 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin" x-cloak></span>
                        <span x-show="!isFetchingBlog">Auto Map URL</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ════ CARD: SOCIAL MEDIA LINKS ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                Social Media Links (Optional)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg> Facebook URL</label>
                    <input type="url" name="facebook_url" value="{{ old('facebook_url', $doctor->facebook_url ?? '') }}" placeholder="https://facebook.com/..." class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 focus:ring-2 focus:ring-sky-300">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-sky-400" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg> Twitter / X URL</label>
                    <input type="url" name="twitter_url" value="{{ old('twitter_url', $doctor->twitter_url ?? '') }}" placeholder="https://twitter.com/..." class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 focus:ring-2 focus:ring-sky-300">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-blue-700" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg> LinkedIn URL</label>
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $doctor->linkedin_url ?? '') }}" placeholder="https://linkedin.com/..." class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 focus:ring-2 focus:ring-sky-300">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg> YouTube URL</label>
                    <input type="url" name="youtube_url" value="{{ old('youtube_url', $doctor->youtube_url ?? '') }}" placeholder="https://youtube.com/..." class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 focus:ring-2 focus:ring-sky-300">
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-pink-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg> Instagram URL</label>
                    <input type="url" name="instagram_url" value="{{ old('instagram_url', $doctor->instagram_url ?? '') }}" placeholder="https://instagram.com/..." class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 focus:ring-2 focus:ring-sky-300">
                </div>
            </div>
        </div>

        {{-- ════ CARD: GALLERY ════ --}}
        @include('admin.shared._seo_fields', ['model' => $doctor ?? null])

        {{-- ════ CARD: GALLERY ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Image Gallery (Max 10)
            </h3>
            
            <div>
                <input type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp" 
                       class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border border-gray-200 dark:border-gray-600 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 dark:file:bg-gray-700 dark:file:text-gray-300 cursor-pointer focus:outline-none">
                <p class="text-xs text-gray-400 mt-2">You can select multiple images at once. Hold Ctrl/Cmd to select multiple files.</p>
                
                @if(isset($doctor) && !empty($doctor->gallery))
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 mt-5 border-t border-gray-100 dark:border-gray-700 pt-5">
                    @foreach($doctor->gallery as $index => $image)
                        <div class="relative group rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 aspect-square">
                            <img src="{{ asset('storage/' . $image) }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" alt="Gallery image">
                            <label class="absolute top-1.5 right-1.5 bg-white/95 dark:bg-gray-800/95 rounded-md p-1.5 shadow-sm backdrop-blur cursor-pointer text-xs flex items-center gap-1.5 hover:bg-red-50 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 transition-colors border border-gray-100 dark:border-gray-700">
                                <input type="checkbox" name="remove_gallery[]" value="{{ $index }}" class="rounded text-red-500 focus:ring-red-500/30">
                                <span class="font-medium">Remove</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        @include('admin.components.publish-status', ['status' => $doctor->status ?? 'draft', 'publishedAt' => $doctor->published_at ?? null])

        {{-- ════ CARD: PUBLISH STATUS & SUBMIT ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="hidden" name="verified" value="0">
                        <input type="checkbox" name="verified" value="1" @checked(old('verified', $doctor->verified ?? false))
                               class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-300">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300 group-hover:text-green-600 transition-colors">
                            <span class="text-green-500 mr-1">✓</span> Verified Profile
                        </span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="hidden" name="featured" value="0">
                        <input type="checkbox" name="featured" value="1" @checked(old('featured', $doctor->featured ?? false))
                               class="w-4 h-4 rounded border-gray-300 text-amber-500 focus:ring-amber-300">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300 group-hover:text-amber-600 transition-colors">
                            ⭐ Featured (Top spot)
                        </span>
                    </label>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <a href="{{ route('admin.doctors.index') }}"
                       class="flex-1 md:flex-none px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="flex-1 md:flex-none px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white text-sm font-bold hover:opacity-90 shadow-md hover:shadow-lg transition-all text-center">
                        {{ isset($doctor) ? 'Update Doctor' : 'Create Doctor' }}
                    </button>
                </div>
            </div>

            @if(isset($doctor) && $doctor->created_at)
            <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Published: <strong class="text-gray-600 dark:text-gray-300">{{ $doctor->created_at->format('M d, Y - h:i A') }}</strong></span>
                </div>
                <div class="hidden sm:block text-gray-300 dark:text-gray-600">•</div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Last Updated: <strong class="text-gray-700 dark:text-gray-200">{{ $doctor->updated_at->format('M d, Y - h:i A') }}</strong></span>
                    <span class="text-[10px] bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full ml-1 font-medium">({{ $doctor->updated_at->diffForHumans() }})</span>
                </div>
            </div>
            @endif
        </div>
    </form>
</div>

<script>
    async function autoTranslateToBengali(btn) {
        const textSpan = btn.querySelector('.btn-text');
        const spinner = btn.querySelector('.btn-spinner');

        // Gather English fields
        const nameEn = document.querySelector('[name="name[en]"]').value;
        const designationEn = document.querySelector('[name="designation[en]"]').value;
        const qualificationsEn = document.querySelector('[name="qualifications[en]"]').value;
        
        // Ensure TinyMCE is loaded
        let bioEn = '';
        if (typeof tinymce !== 'undefined' && tinymce.get('bio_en')) {
            bioEn = tinymce.get('bio_en').getContent();
        } else {
            bioEn = document.querySelector('[name="bio[en]"]').value;
        }

        if (!nameEn && !bioEn) {
            alert('Please fill out the English Name or Bio first before translating.');
            return;
        }

        const payload = {
            target_language: 'Bengali',
            fields: {
                name: nameEn,
                designation: designationEn,
                qualifications: qualificationsEn,
                bio: bioEn
            }
        };

        try {
            btn.disabled = true;
            spinner.classList.remove('hidden');
            textSpan.textContent = 'Translating...';

            const response = await fetch('{{ route('admin.ai.translate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Translation failed.');
            }

            // Populate Bengali fields
            if (data.content.name && document.querySelector('[name="name[bn]"]')) document.querySelector('[name="name[bn]"]').value = data.content.name;
            if (data.content.designation && document.querySelector('[name="designation[bn]"]')) document.querySelector('[name="designation[bn]"]').value = data.content.designation;
            if (data.content.qualifications && document.querySelector('[name="qualifications[bn]"]')) document.querySelector('[name="qualifications[bn]"]').value = data.content.qualifications;

            if (data.content.bio) {
                if (typeof tinymce !== 'undefined' && tinymce.get('bio_bn')) {
                    tinymce.get('bio_bn').setContent(data.content.bio);
                } else if(document.querySelector('[name="bio[bn]"]')) {
                    document.querySelector('[name="bio[bn]"]').value = data.content.bio;
                }
            }

            // Optional success toast
            if (typeof notyf !== 'undefined') {
                notyf.success('Translated successfully to Bengali!');
            } else {
                alert('Translated successfully to Bengali!');
            }

        } catch (error) {
            console.error('Translation Error:', error);
            if (typeof notyf !== 'undefined') {
                notyf.error(error.message || 'Failed to translate.');
            } else {
                alert(error.message || 'Failed to translate.');
            }
        } finally {
            btn.disabled = false;
            spinner.classList.add('hidden');
            textSpan.textContent = '✨ Auto Translate to Bengali (AI)';
        }
    }
</script>
