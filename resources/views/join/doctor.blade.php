@extends('layouts.app')
@section('title', __('Join as Doctor') . ' — DoctorBD24')
@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-8">
        <div class="text-5xl mb-4">👨‍⚕️</div>
        <h1 class="text-2xl font-extrabold text-gray-800 dark:text-gray-100">{{ __('Join as Doctor') }}</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">{{ __('List your profile and reach thousands of patients.') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm">✅ {{ session('success') }}</div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 p-8">
        <form action="{{ route('join.doctor.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">{{ __('Full Name') }} *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="{{ __('Dr. Your Name') }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">{{ __('Phone Number') }} *</label>
                    <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="01XXXXXXXXX"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 @error('phone') border-red-400 @enderror">
                    @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">{{ __('Email') }} *</label>
                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="doctor@example.com"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">{{ __('Specialty') }}</label>
                    <select name="specialty" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
                        <option value="">{{ __('Choose') }}</option>
                        @foreach($specialties as $sp)
                            <option value="{{ $sp->getTranslation('name', 'en') }}" @selected(old('specialty') === $sp->getTranslation('name', 'en'))>
                                {{ $sp->icon }} {{ $sp->getTranslation('name', app()->getLocale()) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Location Selection --}}
            <div x-data="locationPicker('{{ old('division_id') }}', '{{ old('district_id') }}', '{{ old('area_id') }}')" class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-gray-50/50 dark:bg-gray-900/10 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">{{ __('Division') }}</label>
                    <select name="division_id" x-model="divisionId" @change="fetchDistricts()"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                        <option value="">{{ __('Select Division') }}</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->getTranslation('name', app()->getLocale()) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">{{ __('District') }}</label>
                    <select name="district_id" x-model="districtId" @change="fetchAreas()" :disabled="!divisionId"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300 disabled:opacity-50">
                        <option value="">{{ __('Select District') }}</option>
                        <template x-for="dist in districts" :key="dist.id">
                            <option :value="dist.id" x-text="dist.name" :selected="dist.id == districtId"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">{{ __('Area') }}</label>
                    <select name="area_id" x-model="areaId" :disabled="!districtId"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300 disabled:opacity-50">
                        <option value="">{{ __('Select Area') }}</option>
                        <template x-for="ar in areas" :key="ar.id">
                            <option :value="ar.id" x-text="ar.name" :selected="ar.id == areaId"></option>
                        </template>
                    </select>
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">{{ __('Degree / Qualifications') }}</label>
                <input type="text" name="qualifications" value="{{ old('qualifications') }}" placeholder="{{ __('e.g., MBBS, FCPS (Medicine)') }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 block mb-1.5">{{ __('Message / Additional Info') }}</label>
                <textarea name="message" rows="3" placeholder="{{ __('Enter your chamber, hospital, or other info...') }}"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-300 resize-none">{{ old('message') }}</textarea>
            </div>
            <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-bold hover:opacity-90 shadow-xl transition-all text-sm tracking-wide">
                ✉ {{ __('Submit Application') }}
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('locationPicker', (initialDiv, initialDist, initialArea) => ({
            divisionId: initialDiv,
            districtId: initialDist,
            areaId: initialArea,
            districts: [],
            areas: [],

@endsection
