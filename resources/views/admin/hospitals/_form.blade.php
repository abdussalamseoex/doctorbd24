
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
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
                        <svg class="w-4 h-4 text-[#FF0000]" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        YouTube
                    </label>
                    <input type="url" name="youtube_url" value="{{ old('youtube_url', $hospital->youtube_url ?? '') }}" placeholder="https://youtube.com/..."
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

        {{-- ════ CARD: SUBMIT ════ --}}
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
