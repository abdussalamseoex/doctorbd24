<div class="max-w-6xl mx-auto pb-12">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isset($ambulance) ? 'Edit Ambulance Profile' : 'Add New Ambulance' }}</h1>
        <a href="{{ route('admin.ambulances.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Back to List</a>
    </div>

    <form method="POST" action="{{ isset($ambulance) ? route('admin.ambulances.update', $ambulance->id) : route('admin.ambulances.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if(isset($ambulance)) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content Column --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Basic Logic & Identity --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provider Name <span class="text-red-500">*</span></label>
                            <input type="text" name="provider_name" required value="{{ old('provider_name', $ambulance->provider_name ?? '') }}"
                                   class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white">
                        </div>
                        {{-- Custom Slug --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Custom Slug (URL) <span class="text-gray-400 font-normal whitespace-nowrap">(Leave empty to auto-generate)</span></label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-500 text-xs">doctorbd24.com/ambulance/</span>
                                <input type="text" name="slug" value="{{ old('slug', $ambulance->slug ?? '') }}" placeholder="example-ambulance"
                                       class="flex-1 w-full px-3 py-2 text-sm rounded-r-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white transition-colors">
                            </div>
                            @error('slug')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ambulance Type(s) <span class="text-red-500">*</span></label>
                            @php
                                $avTypes = \App\Models\Ambulance::typeMap();
                                $selectedTypes = old('type', $ambulance->type ?? []);
                                if(is_string($selectedTypes)) $selectedTypes = [$selectedTypes];
                            @endphp
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($avTypes as $val => $label)
                                    <label class="flex items-center p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors">
                                        <input type="checkbox" name="type[]" value="{{ $val }}" 
                                            class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                            @checked(in_array($val, $selectedTypes ?? []))>
                                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('type') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- ════ CARD: MEDIA & IMAGES ════ --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Media & Images
                    </h2>

                    {{-- Logo Upload --}}
                    @php
                        $ambulanceLogoUrl = (isset($ambulance) && $ambulance->logo) ? asset('storage/' . $ambulance->logo) : '';
                    @endphp
                    <div x-data="{
                            preview: @json($ambulanceLogoUrl),
                            dragging: false,
                            handleFile(e) {
                                const file = e.dataTransfer ? e.dataTransfer.files[0] : e.target.files[0];
                                if (!file) return;
                                this.preview = URL.createObjectURL(file);
                            }
                        }" class="mb-6">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-3">Logo / Avatar</label>
                        <div class="flex items-start gap-5">
                            <div class="w-20 h-20 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-600 overflow-hidden bg-gray-50 dark:bg-gray-700 flex items-center justify-center flex-shrink-0"
                                 :class="dragging ? 'border-red-400 bg-red-50' : ''">
                                <img x-show="preview" :src="preview" class="w-full h-full object-cover" alt="">
                                <div x-show="!preview" class="flex flex-col items-center gap-1 text-gray-300 dark:text-gray-500">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                            <div class="flex-1 space-y-2">
                                <div class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all"
                                     :class="dragging ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-red-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                                     @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                                     @drop.prevent="dragging=false; handleFile($event)" @click="$refs.logoInput.click()">
                                    <input type="file" name="logo" x-ref="logoInput" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="handleFile($event)">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Drag & drop or <span class="text-red-600 font-semibold">browse</span></p>
                                    <p class="text-xs text-gray-400 mt-0.5">JPEG/PNG/WebP · Auto-resized to 256x256px</p>
                                </div>
                                @if(isset($ambulance) && $ambulance->logo)
                                <label class="flex items-center gap-2 cursor-pointer text-xs text-red-500 hover:text-red-600">
                                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-red-300 text-red-500 focus:ring-red-300" @change="if($el.checked) preview=''">
                                    Remove current logo
                                </label>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Cover Image Upload --}}
                    @php
                        $ambulanceCoverUrl = (isset($ambulance) && $ambulance->cover_image) ? asset('storage/' . $ambulance->cover_image) : '';
                    @endphp
                    <div x-data="{
                            preview: @json($ambulanceCoverUrl),
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
                                 :class="dragging ? 'border-red-400 bg-red-50' : ''">
                                <img x-show="preview" :src="preview" class="w-full h-full object-cover rounded-xl" alt="">
                                <div x-show="!preview" class="flex flex-col items-center gap-1 text-gray-300 dark:text-gray-500">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                            <div class="flex-1 space-y-2 w-full">
                                <div class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all"
                                     :class="dragging ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-red-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                                     @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                                     @drop.prevent="dragging=false; handleFile($event)" @click="$refs.coverInput.click()">
                                    <input type="file" name="cover_image" x-ref="coverInput" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="handleFile($event)">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Drag & drop or <span class="text-red-600 font-semibold">browse</span></p>
                                    <p class="text-xs text-gray-400 mt-0.5">JPEG/PNG/WebP · Recommended: 1200x400px</p>
                                </div>
                                @if(isset($ambulance) && $ambulance->cover_image)
                                <label class="flex items-center gap-2 cursor-pointer text-xs text-red-500 hover:text-red-600">
                                    <input type="checkbox" name="remove_cover_image" value="1" class="rounded border-red-300 text-red-500 focus:ring-red-300" @change="if($el.checked) preview=''">
                                    Remove cover image
                                </label>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Image Gallery --}}
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-3">Image Gallery (Max 10)</label>
                        <div>
                            <input type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp" 
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border border-gray-200 dark:border-gray-600 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 dark:file:bg-gray-700 dark:file:text-gray-300 cursor-pointer focus:outline-none">
                            <p class="text-xs text-gray-400 mt-2">You can select multiple images at once. Hold Ctrl/Cmd to select multiple files.</p>
                            
                            @if(isset($ambulance) && !empty($ambulance->gallery))
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mt-5 border-t border-gray-100 dark:border-gray-700 pt-5">
                                @foreach($ambulance->gallery as $index => $image)
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
                </div>

                {{-- Biography & Description --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Profile Content</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Short Summary (1-2 sentences)</label>
                            <textarea name="summary" rows="2"
                                      class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white">{{ old('summary', $ambulance->summary ?? '') }}</textarea>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Description / Notes</label>
                                <button type="button" onclick="generateAiContent('ambulance_bio', 'textarea[name=\'notes\']', this)" class="text-[10px] bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-red-200 transition-colors">
                                    ✨ Auto Generate Copy
                                </button>
                            </div>
                            <textarea name="notes" rows="6"
                                      class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white">{{ old('notes', $ambulance->notes ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Location & Maps --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Location & Availability</h2>
                    
                    {{-- Cascading Picker --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4" x-data="locationPicker('{{ old('division_id', $ambulance->area?->district?->division_id ?? '') }}', '{{ old('district_id', $ambulance->area?->district_id ?? '') }}', '{{ old('area_id', $ambulance->area_id ?? '') }}')">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Division</label>
                            <select name="division_id" x-model="divisionId" @change="fetchDistricts()" class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white text-sm">
                                <option value="">-- Division --</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}">{{ $div->getTranslation('name', 'en') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">District</label>
                            <select name="district_id" x-model="districtId" @change="fetchAreas()" :disabled="!divisionId" class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white text-sm">
                                <option value="">-- District --</option>
                                <template x-for="dist in districts" :key="dist.id">
                                    <option :value="dist.id" x-text="dist.name" :selected="dist.id == districtId"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Area</label>
                            <select name="area_id" x-model="areaId" :disabled="!districtId" class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white text-sm">
                                <option value="">-- Area --</option>
                                <template x-for="ar in areas" :key="ar.id">
                                    <option :value="ar.id" x-text="ar.name" :selected="ar.id == areaId"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Detailed Address / Stand</label>
                        <input type="text" name="address" value="{{ old('address', $ambulance->address ?? '') }}"
                               class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Latitude</label>
                            <input type="number" step="any" name="latitude" value="{{ old('latitude', $ambulance->latitude ?? '') }}"
                                   class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white"
                                   placeholder="e.g. 23.8103">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Longitude</label>
                            <input type="number" step="any" name="longitude" value="{{ old('longitude', $ambulance->longitude ?? '') }}"
                                   class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white"
                                   placeholder="e.g. 90.4125">
                        </div>
                    </div>
                </div>

                {{-- SEO Fields --}}
                @include('admin.shared._seo_fields', ['model' => $ambulance ?? null])

            </div>

            {{-- Right Sidebar --}}
            <div class="space-y-6">
                
                {{-- Contact Block --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Contact Info</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Primary Phone <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" required value="{{ old('phone', $ambulance->phone ?? '') }}"
                                   class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">WhatsApp</label>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp', $ambulance->whatsapp ?? '') }}"
                                   placeholder="e.g. 017XXXXXX"
                                   class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Backup Phone</label>
                            <input type="text" name="backup_phone" value="{{ old('backup_phone', $ambulance->backup_phone ?? '') }}"
                                   class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-red-500 focus:border-red-500 text-gray-900 dark:text-white">
                        </div>
                    </div>
                </div>

                {{-- Status & Verification --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Toggles & Status</h2>
                    <div class="space-y-4">
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" name="active" value="1" @checked(old('active', $ambulance->active ?? true)) class="w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-3 font-medium text-gray-900 dark:text-white">Active Profile</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <input type="hidden" name="is_verified" value="0">
                            <input type="checkbox" name="is_verified" value="1" @checked(old('is_verified', $ambulance->is_verified ?? false)) class="w-5 h-5 rounded border-gray-300 text-sky-500 focus:ring-sky-500">
                            <span class="ml-3 font-medium text-gray-900 dark:text-white flex items-center gap-1.5">
                                Verified Provider
                                <svg class="w-4 h-4 text-sky-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            </span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $ambulance->is_featured ?? false)) class="w-5 h-5 rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                            <span class="ml-3 font-medium text-gray-900 dark:text-white flex items-center gap-1.5">
                                Featured Spot
                                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Feature Arrays --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Service Features</h2>
                    
                    @php
                        $availableFeatures = \App\Models\AmbulanceFeature::where('is_active', true)->pluck('name')->toArray();
                        $selectedFeatures = old('features', $ambulance->features ?? []);
                    @endphp

                    <div class="space-y-3">
                        <label class="flex items-center p-3 border border-red-100 dark:border-red-900/50 bg-red-50 dark:bg-red-900/20 rounded-xl cursor-pointer">
                            <input type="hidden" name="available_24h" value="0">
                            <input type="checkbox" name="available_24h" value="1" @checked(old('available_24h', $ambulance->available_24h ?? false)) class="w-4 h-4 rounded border-red-300 text-red-600 focus:ring-red-500">
                            <span class="ml-3 font-black text-red-700 dark:text-red-400">Available 24 Hours</span>
                        </label>

                        @foreach($availableFeatures as $key => $feature)
                            <label class="flex items-center p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                <input type="checkbox" name="features[]" value="{{ $feature }}" @checked(is_array($selectedFeatures) && in_array($feature, $selectedFeatures)) class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-gradient-to-r from-red-600 to-rose-600 rounded-2xl shadow-lg p-6 flex flex-col md:flex-row justify-between items-center text-white sticky bottom-6 z-20">
            <div>
                <h3 class="font-bold text-lg">Save Ambulance Profile</h3>
                <p class="text-sm opacity-80">Make sure all details are accurate before publishing.</p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-4">
                <a href="{{ route('admin.ambulances.index') }}" class="px-6 py-2.5 bg-white/10 hover:bg-white/20 text-white font-medium rounded-xl transition-colors backdrop-blur-sm">Cancel changes</a>
                <button type="submit" class="px-8 py-2.5 bg-white text-red-600 hover:bg-gray-50 font-bold rounded-xl shadow-md transition-all">
                    {{ isset($ambulance) ? 'Update Ambulance Data' : 'Publish Ambulance' }}
                </button>
            </div>
        </div>

    </form>
</div>

