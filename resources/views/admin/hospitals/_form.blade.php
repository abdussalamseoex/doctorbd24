
<div class="max-w-4xl mx-auto">
    <form method="POST"
          action="{{ isset($hospital) ? route('admin.hospitals.update', $hospital->id) : route('admin.hospitals.store') }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @if(isset($hospital)) @method('PUT') @endif

        {{-- ════ CARD: LOGO & BASIC INFO ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Hospital Details
            </h3>

            {{-- Logo Upload --}}
            @php
                $hospitalLogoUrl = (isset($hospital) && $hospital->logo) ? asset('storage/' . $hospital->logo) : '';
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
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-3">Logo / Photo</label>
                <div class="flex items-start gap-5">
                    <div class="w-20 h-20 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-600 overflow-hidden bg-gray-50 dark:bg-gray-700 flex items-center justify-center flex-shrink-0"
                         :class="dragging ? 'border-emerald-400 bg-emerald-50' : ''">
                        <img x-show="preview" :src="preview" class="w-full h-full object-cover" alt="">
                        <div x-show="!preview" class="flex flex-col items-center gap-1 text-gray-300 dark:text-gray-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div class="flex-1 space-y-2">
                        <div class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all"
                             :class="dragging ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-emerald-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                             @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                             @drop.prevent="dragging=false; handleFile($event)" @click="$refs.logoInput.click()">
                            <input type="file" name="logo" x-ref="logoInput" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="handleFile($event)">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Drag & drop or <span class="text-emerald-600 font-semibold">browse</span></p>
                            <p class="text-xs text-gray-400 mt-0.5">JPEG/PNG/WebP · Max 2 MB</p>
                        </div>
                        @if(isset($hospital) && $hospital->logo)
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-red-500 hover:text-red-600">
                            <input type="checkbox" name="remove_logo" value="1" class="rounded border-red-300 text-red-500 focus:ring-red-300" @change="if($el.checked) preview=''">
                            Remove current logo
                        </label>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Banner Upload --}}
            @php
                $hospitalBannerUrl = (isset($hospital) && $hospital->banner) ? asset('storage/' . $hospital->banner) : '';
            @endphp
            <div x-data="{
                    preview: @json($hospitalBannerUrl),
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
                         :class="dragging ? 'border-emerald-400 bg-emerald-50' : ''">
                        <img x-show="preview" :src="preview" class="w-full h-full object-cover rounded-xl" alt="">
                        <div x-show="!preview" class="flex flex-col items-center gap-1 text-gray-300 dark:text-gray-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div class="flex-1 space-y-2 w-full">
                        <div class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all"
                             :class="dragging ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-emerald-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                             @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                             @drop.prevent="dragging=false; handleFile($event)" @click="$refs.bannerInput.click()">
                            <input type="file" name="banner" x-ref="bannerInput" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="handleFile($event)">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Drag & drop or <span class="text-emerald-600 font-semibold">browse</span></p>
                            <p class="text-xs text-gray-400 mt-0.5">JPEG/PNG/WebP · Recommended: 1200x400px</p>
                        </div>
                        @if(isset($hospital) && $hospital->banner)
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-red-500 hover:text-red-600">
                            <input type="checkbox" name="remove_banner" value="1" class="rounded border-red-300 text-red-500 focus:ring-red-300" @change="if($el.checked) preview=''">
                            Remove banner
                        </label>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Basic Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Hospital Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required value="{{ old('name', $hospital->name ?? '') }}" placeholder="e.g. Dhaka Medical College Hospital"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                {{-- Custom Slug --}}
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Custom Slug (URL) <span class="text-gray-400 font-normal whitespace-nowrap">(Leave empty to auto-generate)</span></label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-500 text-xs">doctorbd24.com/hospital/</span>
                        <input type="text" name="slug" value="{{ old('slug', $hospital->slug ?? '') }}" placeholder="dhaka-medical-college"
                               class="flex-1 w-full px-3 py-2 text-sm rounded-r-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                    </div>
                    @error('slug')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Type <span class="text-red-400">*</span></label>
                    <select name="type" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                        <option value="hospital"   @selected(old('type', $hospital->type ?? '') === 'hospital')>🏥 Hospital</option>
                        <option value="diagnostic" @selected(old('type', $hospital->type ?? '') === 'diagnostic')>🔬 Diagnostic</option>
                        <option value="clinic"     @selected(old('type', $hospital->type ?? '') === 'clinic')>🩺 Clinic</option>
                        <option value="other"      @selected(old('type', $hospital->type ?? '') === 'other')>Other</option>
                    </select>
                </div>
                {{-- Cascading Location --}}
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50/50 dark:bg-gray-900/10 p-4 rounded-2xl border border-gray-100 dark:border-gray-700"
                     x-data="locationPicker('{{ old('division_id', $hospital->area?->district?->division_id ?? '') }}', '{{ old('district_id', $hospital->area?->district_id ?? '') }}', '{{ old('area_id', $hospital->area_id ?? '') }}')">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Division</label>
                        <select name="division_id" x-model="divisionId" @change="fetchDistricts()"
                                class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                            <option value="">-- Select Division --</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}">{{ $div->getTranslation('name', 'en') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">District</label>
                        <select name="district_id" x-model="districtId" @change="fetchAreas()" :disabled="!divisionId"
                                class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 disabled:opacity-50">
                            <option value="">-- Select District --</option>
                            <template x-for="dist in districts" :key="dist.id">
                                <option :value="dist.id" x-text="dist.name" :selected="dist.id == districtId"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Area <span class="text-red-400">*</span></label>
                        <select name="area_id" x-model="areaId" :disabled="!districtId" required
                                class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 disabled:opacity-50">
                            <option value="">-- Select Area --</option>
                            <template x-for="ar in areas" :key="ar.id">
                                <option :value="ar.id" x-text="ar.name" :selected="ar.id == areaId"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">📞 Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $hospital->phone ?? '') }}" placeholder="e.g. 02-55165088"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">✉ Email</label>
                    <input type="email" name="email" value="{{ old('email', $hospital->email ?? '') }}" placeholder="contact@hospital.com"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">🌐 Website</label>
                    <input type="url" name="website" value="{{ old('website', $hospital->website ?? '') }}" placeholder="https://example.com"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">📍 Full Address</label>
                    <input type="text" name="address" value="{{ old('address', $hospital->address ?? '') }}" placeholder="House, Road, Area..."
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors">
                </div>
                <div class="md:col-span-2">
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block">About Hospital</label>
                        <button type="button" onclick="generateAiContent('hospital_bio', 'tinymce:about', this)" class="text-[10px] bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-emerald-200 transition-colors z-50 relative">
                            ✨ Auto Generate Copy
                        </button>
                    </div>
                    <textarea name="about" id="about" rows="8" placeholder="Description of the hospital, facilities, and mission..."
                              class="tinymce-editor w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-300 resize-none transition-colors">{{ old('about', $hospital->about ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ════ CARD: SERVICES / DEPARTMENTS ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6"
             x-data="{ services: {{ json_encode(isset($hospital) && $hospital->services ? $hospital->services : []) }}, newService: '' }">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Services & Departments
            </h3>
            
            <div class="flex flex-col sm:flex-row gap-2 mb-3">
                <input type="text" x-model="newService" placeholder="e.g. Cardiology, 24/7 ICU, Dialysis..."
                       @keydown.enter.prevent="if(newService.trim()) { services.push(newService.trim()); newService=''; }"
                       class="flex-1 px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition-colors shadow-sm">
                <button type="button" @click="if(newService.trim()) { services.push(newService.trim()); newService=''; }"
                        class="px-5 py-2 rounded-xl bg-indigo-500 text-white text-sm font-semibold hover:bg-indigo-600 transition-colors shadow-sm whitespace-nowrap hidden sm:block">
                    Add Service
                </button>
            </div>
            <input type="hidden" name="services" :value="JSON.stringify(services)">

            <div class="flex flex-wrap gap-2 min-h-[50px] p-4 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <template x-for="(service, i) in services" :key="i">
                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium border border-gray-200 dark:border-gray-600 shadow-sm transition-transform hover:-translate-y-0.5">
                        <span x-text="service"></span>
                        <button type="button" @click="services.splice(i, 1)"
                                class="w-5 h-5 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-600 hover:bg-red-100 hover:text-red-500 transition-colors text-gray-400">
                            <span class="sr-only">Remove</span>
                            <svg class="w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                </template>
                <div x-show="services.length === 0" class="flex flex-col items-center justify-center w-full py-2">
                    <svg class="w-6 h-6 text-gray-300 dark:text-gray-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span class="text-xs text-gray-400 font-medium">No services added</span>
                    <span class="text-[10px] text-gray-400/70">Type in the box above and press Enter</span>
                </div>
            </div>
        </div>

        {{-- ════ CARD: OPENING HOURS ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6"
             x-data="{
                days: ['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'],
                hours: {{ empty(isset($hospital) ? $hospital->opening_hours : []) ? '{}' : json_encode($hospital->opening_hours) }}
             }">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Opening Hours
            </h3>
            <input type="hidden" name="opening_hours" :value="JSON.stringify(hours)">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <template x-for="day in days" :key="day">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700 hover:border-emerald-200 transition-colors focus-within:ring-2 focus-within:ring-amber-100 dark:focus-within:ring-amber-900/30">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200 w-24 flex-shrink-0" x-text="day"></span>
                        <input type="text" placeholder="e.g. 24/7 or Closed" x-model="hours[day]"
                               class="flex-1 w-full min-w-0 px-3 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:border-amber-300 shadow-sm transition-all">
                        <button type="button" @click="days.forEach(d => { if(d !== day) hours[d] = hours[day] })"
                                class="p-1.5 shrink-0 text-gray-400 hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-lg transition-colors focus:outline-none"
                                title="Copy to all days">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- ════ CARD: LOCATION & SOCIAL ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" x-data="{ showAdvanced: false }">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Links & Location
            </h3>

            {{-- Social Media --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        Facebook
                    </label>
                    <input type="url" name="facebook_url" value="{{ old('facebook_url', $hospital->facebook_url ?? '') }}" placeholder="https://facebook.com/..."
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#E4405F]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.358-.2 6.78-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        Instagram
                    </label>
                    <input type="url" name="instagram_url" value="{{ old('instagram_url', $hospital->instagram_url ?? '') }}" placeholder="https://instagram.com/..."
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-pink-400">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#0A66C2]" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        LinkedIn
                    </label>
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $hospital->linkedin_url ?? '') }}" placeholder="https://linkedin.com/..."
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-[#0A66C2]">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-800 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        X (Twitter)
                    </label>
                    <input type="url" name="twitter_url" value="{{ old('twitter_url', $hospital->twitter_url ?? '') }}" placeholder="https://x.com/..."
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-800">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#FF0000]" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        YouTube Channel
                    </label>
                    <input type="url" name="youtube_url" value="{{ old('youtube_url', $hospital->youtube_url ?? '') }}" placeholder="https://youtube.com/@..."
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-400">
                </div>
            </div>

            <hr class="border-gray-100 dark:border-gray-700 mb-4">

            {{-- Advanced Location --}}
            <button type="button" @click="showAdvanced = !showAdvanced"
                    class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 hover:underline mb-2">
                <svg class="w-4 h-4 transition-transform duration-200" :class="showAdvanced ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                Google Maps Embed Logic (Lat/Lng)
            </button>

            <div x-show="showAdvanced" x-collapse class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">🗺️ Google Maps URL</label>
                    <input type="url" name="google_maps_url" value="{{ old('google_maps_url', $hospital->google_maps_url ?? '') }}" placeholder="https://maps.app.goo.gl/..."
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Latitude</label>
                    <input type="number" step="any" name="lat" value="{{ old('lat', $hospital->lat ?? '') }}" placeholder="23.8103"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">Longitude</label>
                    <input type="number" step="any" name="lng" value="{{ old('lng', $hospital->lng ?? '') }}" placeholder="90.4125"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                </div>
                <div class="md:col-span-2 p-3 mt-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50 flex gap-2">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-[11px] md:text-xs text-emerald-800 dark:text-emerald-300 leading-relaxed">
                        To get latitude & longitude: Open Google Maps, right-click on the hospital's pin, select the coordinates at the top (e.g., <span class="font-mono bg-white dark:bg-gray-800 px-1 py-0.5 rounded">23.8103, 90.4125</span>).
                    </p>
                </div>
            </div>
        </div>

        {{-- ════ CARD: MEDIA & CONTENT TABS ════ --}}
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/10 dark:to-purple-900/10 rounded-2xl shadow-sm border border-indigo-100 dark:border-indigo-800/50 p-6" 
             x-data="{ 
                videos: {{ isset($hospital) && $hospital->hospitalVideos->count() > 0 ? $hospital->hospitalVideos->map(function($v){ return ['title' => $v->title, 'url' => $v->video_url]; })->toJson() : '[]' }},
                blogs: {{ empty(isset($hospital) && $hospital->blogs) ? '[]' : (is_string($hospital->blogs[0] ?? null) ? json_encode(array_map(function($url){ return ['title'=>'Linked Content', 'url'=>$url]; }, $hospital->blogs)) : json_encode($hospital->blogs)) }},
                newVideoUrl: '',
                newChannelUrl: '',
                newBlogUrl: '',
                isFetchingVideo: false,
                isFetchingChannel: false,
                isFetchingBlog: false,
                youtubePageToken: null,
                youtubeChannelId: null,
                
                init() {
                    this.$watch('videos', value => {
                        document.querySelector('input[name=videos]').value = JSON.stringify(value);
                    });
                    this.$watch('blogs', value => {
                        document.querySelector('input[name=blogs]').value = JSON.stringify(value);
                    });
                    this.$watch('newChannelUrl', () => {
                        // Reset pagination if user types a new URL
                        this.youtubePageToken = null;
                        this.youtubeChannelId = null;
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
                    if (!cUrl && !this.youtubeChannelId) return;
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
                            const existingVideos = this.videos;
                            // Prepend videos exactly as done before
                            this.videos = [...data.videos, ...existingVideos];
                            
                            // Save tokens for infinite scrolling via Official API
                            this.youtubePageToken = data.nextPageToken || null;
                            if (data.channelId) this.youtubeChannelId = data.channelId;
                            
                            if (this.youtubePageToken) {
                                alert(`Successfully added ${data.videos.length} videos! You can click 'Fetch Next 30' to get more.`);
                            } else {
                                alert(`Successfully added ${data.videos.length} videos!`);
                            }
                        } else {
                            alert(data.error || 'No videos found in this channel.');
                        }
                    })
                    .catch(() => alert('Error fetching channel. Make sure the URL is a valid YouTube Channel.'))
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
                    if (!name) { alert('Please enter the Hospital/Diagnostic Name first.'); return; }
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
                    if (!name) { alert('Please enter the Hospital/Diagnostic Name first.'); return; }
                    this.isFetchingBlog = true;
                    fetch('{{ route('admin.hospitals.fetch-blog') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ query: name })
                    }).then(res => res.json()).then(data => {
                        if (data.url) { this.addBlog(data.url); }
                    }).catch(() => alert('Error fetching blog')).finally(() => this.isFetchingBlog = false);
                }
            }">
            <div class="mb-5">
                <h3 class="text-sm font-bold text-indigo-900 dark:text-indigo-300 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Additional Profiles & Tabs
                </h3>
                <p class="text-xs text-indigo-600/70 dark:text-indigo-400/70 mt-1">Add multiple dynamic content arrays (Videos, Blogs) to automatically unlock and populate new tabs on the public hospital profile.</p>
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
                        Videos (Multi-Platform Auto Fetch)
                    </label>
                    
                    <div class="flex flex-col gap-2 mb-3 border-b border-gray-100 dark:border-gray-700 pb-3">
                        <div class="flex gap-2">
                            <input type="url" x-model="newVideoUrl" @keydown.enter.prevent="addVideo()" placeholder="Paste a single video URL..."
                                class="flex-1 w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700/50 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-all">
                            <button type="button" @click="addVideo()" class="px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-semibold rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors shrink-0">
                                Add
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <input type="url" x-model="newChannelUrl" @keydown.enter.prevent="fetchChannelVideo()" placeholder="Paste YouTube Channel URL..."
                                class="flex-1 w-full px-3 py-2 text-sm border-dashed border-red-200 dark:border-red-900/50 rounded-lg border bg-red-50/50 focus:bg-white dark:bg-red-900/10 dark:focus:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-400 transition-all">
                            <button type="button" @click="fetchChannelVideo()" :disabled="isFetchingChannel" class="px-3 py-2 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 font-semibold rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors shrink-0 flex items-center min-w-[120px] justify-center text-xs">
                                <span x-show="isFetchingChannel" class="w-3.5 h-3.5 border-2 border-red-500 border-t-transparent rounded-full animate-spin mr-1" x-cloak></span>
                                <span x-show="!isFetchingChannel" class="whitespace-nowrap" x-text="youtubePageToken ? 'Fetch Next ~50' : 'Fetch (~50)'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5 mb-3 flex-1 overflow-y-auto max-h-[250px] pr-1">
                        <template x-for="(vid, index) in videos" :key="index">
                            <div class="group flex items-start justify-between gap-2 p-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm relative">
                                <div class="overflow-hidden">
                                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate pr-6" x-text="vid.title"></p>
                                    <p class="text-[10px] text-gray-500 truncate" x-text="vid.url"></p>
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
                    
                    <button type="button" @click="fetchVideo()" class="w-full bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800 transition-colors px-3 py-2 rounded-lg flex items-center justify-center gap-1.5 cursor-pointer text-xs font-bold shadow-sm mt-auto">
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
                        Related Blog Articles Array
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

        @if(isset($hospital) && $hospital->id)
            {{-- ════ CARD: DIAGNOSTIC SERVICES & TESTS ════ --}}
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl shadow-sm border border-emerald-100 dark:border-emerald-800/50 p-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        Diagnostic Services & Pricing
                    </h3>
                    <p class="text-sm text-emerald-600/80 dark:text-emerald-400/80 mt-1">Manage tests, investigations, and bulk import service pricing.</p>
                </div>
                <a href="{{ route('admin.hospitals.services.index', $hospital->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm">
                    Manage Pricing & Tests
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endif

        @include('admin.shared._seo_fields', ['model' => $hospital ?? null])

        {{-- ════ CARD: GALLERY ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" x-data>
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Image Gallery (Max 10)
            </h3>
            
            <div>
                <input type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp" 
                       class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border border-gray-200 dark:border-gray-600 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 dark:file:bg-gray-700 dark:file:text-gray-300 cursor-pointer focus:outline-none">
                <p class="text-xs text-gray-400 mt-2">You can select multiple images at once. Hold Ctrl/Cmd to select multiple files.</p>
                
                @if(isset($hospital) && !empty($hospital->gallery))
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 mt-5 border-t border-gray-100 dark:border-gray-700 pt-5">
                    @foreach($hospital->gallery as $index => $image)
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

        @include('admin.components.publish-status', ['status' => $hospital->status ?? 'draft', 'publishedAt' => $hospital->published_at ?? null])

        {{-- ════ CARD: SUBMIT ════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="hidden" name="verified" value="0">
                        <input type="checkbox" name="verified" value="1" @checked(old('verified', $hospital->verified ?? false))
                               class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-300">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300 group-hover:text-green-600 transition-colors">
                            <span class="text-green-500 mr-1">✓</span> Verified Profile
                        </span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="hidden" name="featured" value="0">
                        <input type="checkbox" name="featured" value="1" @checked(old('featured', $hospital->featured ?? false))
                               class="w-4 h-4 rounded border-gray-300 text-amber-500 focus:ring-amber-300">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300 group-hover:text-amber-600 transition-colors">
                            ⭐ Featured (Top spot)
                        </span>
                    </label>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <a href="{{ route('admin.hospitals.index') }}"
                       class="flex-1 md:flex-none px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="flex-1 md:flex-none px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-bold hover:opacity-90 shadow-md hover:shadow-lg transition-all text-center">
                        {{ isset($hospital) ? 'Update Hospital' : 'Create Hospital' }}
                    </button>
                </div>
            </div>
            
            @if(isset($hospital) && $hospital->created_at)
            <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Published: <strong class="text-gray-600 dark:text-gray-300">{{ $hospital->created_at->format('M d, Y - h:i A') }}</strong></span>
                </div>
                <div class="hidden sm:block text-gray-300 dark:text-gray-600">•</div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Last Updated: <strong class="text-gray-700 dark:text-gray-200">{{ $hospital->updated_at->format('M d, Y - h:i A') }}</strong></span>
                    <span class="text-[10px] bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full ml-1 font-medium">({{ $hospital->updated_at->diffForHumans() }})</span>
                </div>
            </div>
            @endif
        </div>
    </form>
</div>
@include('admin.shared._tinymce')
