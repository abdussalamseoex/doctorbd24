@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $isBn = $locale === 'bn';
    $diseasesIndexRoute = $isBn ? 'bn.diseases.index' : 'diseases.index';
    $symptomCheckerCheckRoute = $isBn ? 'bn.symptom-checker.check' : 'symptom-checker.check';
@endphp

<div class="bg-gray-50 dark:bg-gray-950 min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" 
         x-data="{
            step: 1,
            profile: { age_group: 'adult', gender: 'male' },
            selectedBodyPart: null,
            symptomScopeFilter: 'all',
            symptomSearch: '',
            selectedSymptoms: [],
            selectedFactors: [],
            bodyParts: {{ json_encode($bodyParts) }},
            loading: false,
            results: null,

            setProfile(ageGroup, gender) {
                this.profile = { age_group: ageGroup, gender: gender };
                this.symptomScopeFilter = ageGroup;
                this.step = 2;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            toggleSymptom(id) {
                const idx = this.selectedSymptoms.indexOf(id);
                if (idx > -1) {
                    this.selectedSymptoms.splice(idx, 1);
                } else {
                    this.selectedSymptoms.push(id);
                }
            },

            isSymptomSelected(id) {
                return this.selectedSymptoms.includes(id);
            },

            toggleFactorOption(optId) {
                const idx = this.selectedFactors.indexOf(optId);
                if (idx > -1) {
                    this.selectedFactors.splice(idx, 1);
                } else {
                    this.selectedFactors.push(optId);
                }
            },

            isFactorSelected(optId) {
                return this.selectedFactors.includes(optId);
            },

            getSelectedSymptomsList() {
                let list = [];
                this.bodyParts.forEach(bp => {
                    if (bp.symptoms) {
                        bp.symptoms.forEach(sym => {
                            if (this.selectedSymptoms.includes(sym.id)) {
                                list.push(sym);
                            }
                        });
                    }
                });
                return list;
            },

            hasFactors() {
                const list = this.getSelectedSymptomsList();
                return list.some(sym => sym.factors && sym.factors.length > 0);
            },

            goToStep3OrSubmit() {
                if (this.hasFactors()) {
                    this.step = 3;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    this.submitCheck();
                }
            },

            async submitCheck() {
                this.loading = true;
                this.step = 4;
                window.scrollTo({ top: 0, behavior: 'smooth' });
                try {
                    const response = await fetch('{{ route($symptomCheckerCheckRoute) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            symptom_ids: this.selectedSymptoms,
                            age_group: this.profile.age_group,
                            gender: this.profile.gender,
                            factor_answers: this.selectedFactors
                        })
                    });
                    this.results = await response.json();
                } catch (err) {
                    console.error('Symptom check failed:', err);
                } finally {
                    this.loading = false;
                }
            },

            resetChecker() {
                this.step = 1;
                this.selectedSymptoms = [];
                this.selectedFactors = [];
                this.results = null;
                this.selectedBodyPart = null;
                this.symptomSearch = '';
                this.symptomScopeFilter = 'all';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
         }">

        {{-- Top Navigation Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">{{ $isBn ? 'হোম' : 'Home' }}</a>
            <span>/</span>
            <a href="{{ route($diseasesIndexRoute) }}" class="hover:text-emerald-600 transition-colors">{{ $isBn ? 'রোগ ও স্বাস্থ্য সমস্যা' : 'Diseases & Conditions' }}</a>
            <span>/</span>
            <span class="text-gray-800 dark:text-gray-200 font-semibold">{{ $isBn ? 'সিম্পটম চেকার' : 'Symptom Checker' }}</span>
        </nav>

        {{-- Header & Progress Stepper --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <span class="text-[11px] font-bold tracking-wider uppercase text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full border border-emerald-200 dark:border-emerald-800">
                        {{ $isBn ? 'মেয়ো ক্লিনিক অনুসরণে ৩-ধাপের লক্ষণ নির্দেশক' : 'Mayo Clinic 3-Step Guided Triage' }}
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-gray-100 mt-2">
                        {{ $isBn ? 'লক্ষণ পরীক্ষক (Symptom Checker)' : 'Interactive Symptom Checker' }}
                    </h1>
                </div>

                <button type="button" @click="resetChecker()" class="text-xs font-semibold text-gray-400 hover:text-red-500 transition-colors flex items-center gap-1 self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ $isBn ? 'শুরু থেকে করুন' : 'Start Over' }}
                </button>
            </div>

            {{-- 4-Step Progress Indicator --}}
            <div class="grid grid-cols-4 gap-2 text-center text-xs font-semibold">
                <div class="pb-2 border-b-4 transition-all" :class="step >= 1 ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-gray-200 dark:border-gray-700 text-gray-400'">
                    1. {{ $isBn ? 'রোগীর প্রোফাইল' : 'Profile' }}
                </div>
                <div class="pb-2 border-b-4 transition-all" :class="step >= 2 ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-gray-200 dark:border-gray-700 text-gray-400'">
                    2. {{ $isBn ? 'উপসর্গ বাছাই' : 'Symptoms' }}
                </div>
                <div class="pb-2 border-b-4 transition-all" :class="step >= 3 ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-gray-200 dark:border-gray-700 text-gray-400'">
                    3. {{ $isBn ? 'ফ্যাক্টর প্রশ্নাবলি' : 'Related Factors' }}
                </div>
                <div class="pb-2 border-b-4 transition-all" :class="step >= 4 ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-gray-200 dark:border-gray-700 text-gray-400'">
                    4. {{ $isBn ? 'ফলাফল ও ডাক্তার' : 'Results & Care' }}
                </div>
            </div>
        </div>

        {{-- ── Step 1: Patient Profile ──────────────────────────────── --}}
        <div x-show="step === 1" class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $isBn ? 'লক্ষণগুলো কার জন্য দেখা হচ্ছে?' : 'Who is this symptom check for?' }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $isBn ? 'বয়স ও লিঙ্গের ওপর ভিত্তি করে রোগের ঝুঁকি ও লক্ষণের তারতম্য ঘটে। উপযুক্ত ক্যাটাগরি বেছে নিন।' : 'Select the patient profile to view age-appropriate symptoms and medical conditions.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button type="button" @click="setProfile('adult', 'male')"
                        class="p-5 rounded-2xl border-2 text-left transition-all hover:border-emerald-500 hover:shadow-md flex items-center gap-4 bg-gray-50/50 dark:bg-gray-700/40 border-gray-200 dark:border-gray-600 group">
                    <span class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform">👨</span>
                    <div>
                        <span class="font-bold text-sm text-gray-900 dark:text-gray-100 block">{{ $isBn ? 'প্রাপ্তবয়স্ক পুরুষ (১৮+ বছর)' : 'Adult Male (18+)' }}</span>
                        <span class="text-xs text-gray-500">{{ $isBn ? 'প্রাপ্তবয়স্ক পুরুষদের উপসর্গ ও সাধারণ রোগ' : 'Adult male health symptoms' }}</span>
                    </div>
                </button>

                <button type="button" @click="setProfile('adult', 'female')"
                        class="p-5 rounded-2xl border-2 text-left transition-all hover:border-emerald-500 hover:shadow-md flex items-center gap-4 bg-gray-50/50 dark:bg-gray-700/40 border-gray-200 dark:border-gray-600 group">
                    <span class="w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-900/50 text-pink-600 dark:text-pink-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform">👩</span>
                    <div>
                        <span class="font-bold text-sm text-gray-900 dark:text-gray-100 block">{{ $isBn ? 'প্রাপ্তবয়স্ক নারী (১৮+ বছর)' : 'Adult Female (18+)' }}</span>
                        <span class="text-xs text-gray-500">{{ $isBn ? 'প্রাপ্তবয়স্ক নারীদের উপসর্গ ও গাইনি স্বাস্থ্য' : 'Adult female health symptoms' }}</span>
                    </div>
                </button>

                <button type="button" @click="setProfile('child', 'male')"
                        class="p-5 rounded-2xl border-2 text-left transition-all hover:border-emerald-500 hover:shadow-md flex items-center gap-4 bg-gray-50/50 dark:bg-gray-700/40 border-gray-200 dark:border-gray-600 group">
                    <span class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform">👦</span>
                    <div>
                        <span class="font-bold text-sm text-gray-900 dark:text-gray-100 block">{{ $isBn ? 'শিশু / কিশোর (ছেলে)' : 'Child / Pediatric (Boy)' }}</span>
                        <span class="text-xs text-gray-500">{{ $isBn ? '১৮ বছরের নিচে শিশুদের লক্ষণ' : 'Pediatric boy health symptoms' }}</span>
                    </div>
                </button>

                <button type="button" @click="setProfile('child', 'female')"
                        class="p-5 rounded-2xl border-2 text-left transition-all hover:border-emerald-500 hover:shadow-md flex items-center gap-4 bg-gray-50/50 dark:bg-gray-700/40 border-gray-200 dark:border-gray-600 group">
                    <span class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform">👧</span>
                    <div>
                        <span class="font-bold text-sm text-gray-900 dark:text-gray-100 block">{{ $isBn ? 'শিশু / কিশোর (মেয়ে)' : 'Child / Pediatric (Girl)' }}</span>
                        <span class="text-xs text-gray-500">{{ $isBn ? '১৮ বছরের নিচে শিশুদের লক্ষণ' : 'Pediatric girl health symptoms' }}</span>
                    </div>
                </button>
            </div>
        </div>

        {{-- ── Step 2: Choose Body Location & Symptoms ─────────────── --}}
        <div x-show="step === 2" class="space-y-6" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $isBn ? 'শরীরে কী কী লক্ষণ বা উপসর্গ দেখা দিচ্ছে?' : 'Select your symptoms' }}
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $isBn ? 'শারীরিক অঙ্গ নির্বাচন করুন অথবা সরাসরি সার্চ করুন। আপনি একাধিক লক্ষণ বেছে নিতে পারেন।' : 'Select a body location or search. You can select multiple symptoms.' }}
                        </p>
                    </div>

                    {{-- Selected counter --}}
                    <div class="flex items-center gap-2">
                        <span class="px-3.5 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800">
                            {{ $isBn ? 'নির্বাচিত লক্ষণ:' : 'Selected:' }} <span x-text="selectedSymptoms.length"></span>
                        </span>
                    </div>
                </div>

                {{-- Search & Scope Filter Bar --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 pt-2">
                    {{-- Search Input --}}
                    <div class="md:col-span-7 relative">
                        <input type="text" x-model="symptomSearch" 
                               placeholder="{{ $isBn ? 'লক্ষণ লিখে খুঁজুন (যেমন: মাথাব্যথা, জ্বর, কাশি, বুকে ব্যথা)...' : 'Filter symptoms (e.g., headache, fever, cough)...' }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <div class="absolute left-3 top-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    {{-- Scope Toggle: All / Adult / Child --}}
                    <div class="md:col-span-5 flex items-center gap-1 bg-gray-100 dark:bg-gray-700 p-1 rounded-xl text-xs font-semibold">
                        <button type="button" @click="symptomScopeFilter = 'all'"
                                :class="symptomScopeFilter === 'all' ? 'bg-white dark:bg-gray-800 text-emerald-600 shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'"
                                class="flex-1 py-1.5 rounded-lg transition-all text-center">
                            {{ $isBn ? 'সকল' : 'All' }}
                        </button>
                        <button type="button" @click="symptomScopeFilter = 'adult'"
                                :class="symptomScopeFilter === 'adult' ? 'bg-white dark:bg-gray-800 text-emerald-600 shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'"
                                class="flex-1 py-1.5 rounded-lg transition-all text-center">
                            {{ $isBn ? 'প্রাপ্তবয়স্ক' : 'Adult' }}
                        </button>
                        <button type="button" @click="symptomScopeFilter = 'child'"
                                :class="symptomScopeFilter === 'child' ? 'bg-white dark:bg-gray-800 text-emerald-600 shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'"
                                class="flex-1 py-1.5 rounded-lg transition-all text-center">
                            {{ $isBn ? 'শিশু' : 'Child' }}
                        </button>
                    </div>
                </div>

                {{-- Body Parts Pills --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-2">
                    <button type="button" @click="selectedBodyPart = null"
                            :class="selectedBodyPart === null ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200'"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap">
                        {{ $isBn ? 'সকল শারীরিক অঙ্গ' : 'All Locations' }}
                    </button>
                    <template x-for="bp in bodyParts" :key="bp.id">
                        <button type="button" @click="selectedBodyPart = bp.id"
                                :class="selectedBodyPart === bp.id ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap"
                                x-text="bp.name.{{ $locale }} || bp.name.en">
                        </button>
                    </template>
                </div>

                {{-- Symptoms Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="bp in bodyParts" :key="bp.id">
                        <template x-for="sym in bp.symptoms" :key="sym.id">
                            <div x-show="(selectedBodyPart === null || selectedBodyPart === bp.id) &&
                                         (symptomScopeFilter === 'all' || sym.age_group === 'all' || sym.age_group === symptomScopeFilter) &&
                                         (symptomSearch === '' || (sym.name.{{ $locale }} || sym.name.en).toLowerCase().includes(symptomSearch.toLowerCase()))"
                                 @click="toggleSymptom(sym.id)"
                                 :class="isSymptomSelected(sym.id) ? 'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-950 dark:text-emerald-100 shadow-sm' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:border-emerald-300'"
                                 class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex items-start gap-3 select-none">
                                <div class="mt-0.5">
                                    <div :class="isSymptomSelected(sym.id) ? 'bg-emerald-500 text-white border-emerald-500' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700'" class="w-5 h-5 rounded-lg border flex items-center justify-center text-xs">
                                        <svg x-show="isSymptomSelected(sym.id)" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <h4 class="font-bold text-sm leading-tight text-gray-900 dark:text-gray-100" x-text="sym.name.{{ $locale }} || sym.name.en"></h4>
                                        <template x-if="sym.is_emergency">
                                            <span class="inline-block text-[10px] text-red-600 dark:text-red-400 font-bold">🔴 {{ $isBn ? 'জরুরি' : 'Urgent' }}</span>
                                        </template>
                                    </div>

                                    <p class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5" x-text="bp.name.{{ $locale }} || bp.name.en"></p>

                                    {{-- Short Description --}}
                                    <template x-if="sym.description && (sym.description.{{ $locale }} || sym.description.en)">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed line-clamp-2" x-text="sym.description.{{ $locale }} || sym.description.en"></p>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>

                {{-- Action Bar --}}
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <button type="button" @click="step = 1; window.scrollTo({ top: 0, behavior: 'smooth' });" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100">
                        ← {{ $isBn ? 'পূর্ববর্তী' : 'Back' }}
                    </button>

                    <button type="button" @click="goToStep3OrSubmit()" :disabled="selectedSymptoms.length === 0"
                            :class="selectedSymptoms.length > 0 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white hover:opacity-90 shadow-md' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed'"
                            class="px-6 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                        <span>{{ $isBn ? 'পরবর্তী ধাপে যান' : 'Next Step' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Step 3: Related Factors & Questions (Mayo Clinic Step 3) ─ --}}
        <div x-show="step === 3" class="space-y-6" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                <div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">{{ $isBn ? 'ধাপ ৩: সুনির্দিষ্ট প্রশ্নাবলি' : 'Step 3: Fine-Tune Triage' }}</span>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                        {{ $isBn ? 'উপসর্গ সম্পর্কিত বিষয় ও ব্যথার ধরন' : 'Select Related Symptom Factors' }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $isBn ? 'এই প্রশ্নগুলোর উত্তর দিলে আমাদের সিস্টেম সম্ভাব্য রোগ আরও নিখুঁতভাবে চিহ্নিত করতে পারবে।' : 'These specific questions help refine potential causes and rule out critical emergencies.' }}
                    </p>
                </div>

                {{-- Factors Loop --}}
                <div class="space-y-6">
                    <template x-for="sym in getSelectedSymptomsList()" :key="sym.id">
                        <template x-if="sym.factors && sym.factors.length > 0">
                            <div class="p-5 rounded-2xl bg-gray-50/60 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-700 space-y-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <h3 class="font-bold text-sm text-gray-900 dark:text-gray-100" x-text="`{{ $isBn ? 'প্রশ্নাবলি: ' : 'Questions for: ' }}${sym.name.{{ $locale }} || sym.name.en}`"></h3>
                                </div>

                                <template x-for="factor in sym.factors" :key="factor.id">
                                    <div class="space-y-2.5 pl-4 border-l-2 border-emerald-300 dark:border-emerald-700">
                                        <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300" x-text="factor.question.{{ $locale }} || factor.question.en"></h4>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <template x-for="opt in factor.options" :key="opt.id">
                                                <div @click="toggleFactorOption(opt.id)"
                                                     :class="isFactorSelected(opt.id) ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-100 font-semibold' : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-emerald-300'"
                                                     class="p-3 rounded-xl border cursor-pointer text-xs transition-all flex items-center justify-between">
                                                    <span x-text="(opt.label && (opt.label.{{ $locale }} || opt.label.en)) || opt.text_{{ $locale }} || opt.text_en"></span>
                                                    <template x-if="opt.red_flag || opt.is_red_flag">
                                                        <span class="text-[10px] text-red-500 font-bold ml-2">⚠️ {{ $isBn ? 'জরুরি সংকেত' : 'Red Flag' }}</span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </template>
                </div>

                {{-- Action Bar --}}
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <button type="button" @click="step = 2; window.scrollTo({ top: 0, behavior: 'smooth' });" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100">
                        ← {{ $isBn ? 'পূর্ববর্তী' : 'Back' }}
                    </button>

                    <button type="button" @click="submitCheck()"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-bold shadow-md hover:opacity-90 transition-all flex items-center gap-1.5">
                        <span>{{ $isBn ? 'ফলাফল ও পরামর্শ দেখুন' : 'Get Assessment & Causes' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Step 4: Results & Doctor Recommendations ─────────────── --}}
        <div x-show="step === 4" class="space-y-6" style="display: none;">

            {{-- Loading State --}}
            <div x-show="loading" class="bg-white dark:bg-gray-800 rounded-3xl p-12 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-emerald-500 border-t-transparent mx-auto mb-4"></div>
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-200">{{ $isBn ? 'মেডিকেল ডাটাবেজ বিশ্লেষণ করা হচ্ছে...' : 'Analyzing clinical indicators...' }}</h3>
                <p class="text-xs text-gray-400 mt-1">{{ $isBn ? 'অনুগ্রহ করে অপেক্ষা করুন, সম্ভাব্য কারণসমূহ সাজানো হচ্ছে' : 'Matching conditions and determining urgent care triage...' }}</p>
            </div>

            {{-- Results Content --}}
            <template x-if="!loading && results">
                <div class="space-y-6">

                    {{-- 1. Triage Urgency Level Alert --}}
                    <div :class="{
                        'bg-red-50 dark:bg-red-950/30 border-red-500 text-red-900 dark:text-red-200': results.triage_level === 'emergency',
                        'bg-amber-50 dark:bg-amber-950/30 border-amber-500 text-amber-900 dark:text-amber-200': results.triage_level === 'urgent',
                        'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-500 text-emerald-900 dark:text-emerald-200': results.triage_level === 'routine'
                    }" class="p-6 rounded-3xl border-l-4 shadow-sm">
                        <div class="flex items-start gap-4">
                            <span class="text-3xl">
                                <span x-show="results.triage_level === 'emergency'">🚨</span>
                                <span x-show="results.triage_level === 'urgent'">⚠️</span>
                                <span x-show="results.triage_level === 'routine'">🟢</span>
                            </span>
                            <div>
                                <h3 class="text-lg font-extrabold" x-show="results.triage_level === 'emergency'">
                                    {{ $isBn ? 'জরুরি চিকিৎসা সতর্কতা! অবিলম্বে হাসপাতালে যান' : 'Emergency Notice: Seek Immediate Medical Care' }}
                                </h3>
                                <h3 class="text-lg font-extrabold" x-show="results.triage_level === 'urgent'">
                                    {{ $isBn ? 'শীঘ্রই বিশেষজ্ঞ চিকিৎসকের পরামর্শ নিন' : 'Prompt Medical Evaluation Recommended' }}
                                </h3>
                                <h3 class="text-lg font-extrabold" x-show="results.triage_level === 'routine'">
                                    {{ $isBn ? 'সাধারণ লক্ষণ ও নিয়মিত স্বাস্থ্য পরামর্শ' : 'Routine Care & Lifestyle Management' }}
                                </h3>

                                <p class="text-xs mt-1 leading-relaxed" x-show="results.triage_level === 'emergency'">
                                    {{ $isBn ? 'আপনার নির্বাচিত কিছু উপসর্গ জরুরি চিকিৎসার ইঙ্গিত দিচ্ছে। বিলম্ব না করে নিকটস্থ হাসপাতালের ইমার্জেন্সিতে যান বা জরুরি অ্যাম্বুলেন্স ডাকুন।' : 'Some of your reported symptoms match high-risk clinical red flags. Do not delay seeking emergency room treatment.' }}
                                </p>
                                <p class="text-xs mt-1 leading-relaxed" x-show="results.triage_level === 'urgent'">
                                    {{ $isBn ? 'এই লক্ষণগুলো অবহেলা করা উচিত নয়। আগামী ২৪-৪৮ ঘণ্টার মধ্যে সংশ্লিষ্ট বিশেষজ্ঞ ডাক্তার দেখানো বাঞ্ছনীয়।' : 'These symptoms should be evaluated by a certified physician within 24 to 48 hours to prevent potential complications.' }}
                                </p>
                                <p class="text-xs mt-1 leading-relaxed" x-show="results.triage_level === 'routine'">
                                    {{ $isBn ? 'সাধারণ ঘরোয়া যত্ন ও পর্যাপ্ত বিশ্রাম গ্রহণ করুন। তবে লক্ষণগুলো দীর্ঘস্থায়ী হলে ডাক্তারের পরামর্শ নিন।' : 'Stay well hydrated and maintain proper rest. If symptoms persist for more than a few days, schedule a clinical visit.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Potential Causes & Diseases List --}}
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $isBn ? 'সম্ভাব্য কারণ ও স্বাস্থ্য সমস্যাসমূহ' : 'Possible Conditions' }}
                                </h3>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $isBn ? 'লক্ষণ ও ফ্যাক্টর ম্যাচিং স্কোরের ভিত্তিতে সাজানো' : 'Ranked by symptom correlation and match probability' }}
                                </p>
                            </div>
                            <span class="text-xs font-semibold text-gray-500" x-text="`${results.conditions.length} matches`"></span>
                        </div>

                        <div class="space-y-4">
                            <template x-for="item in results.conditions" :key="item.id">
                                <div class="p-5 rounded-2xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 hover:border-emerald-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1.5 flex-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-base text-gray-900 dark:text-gray-100 hover:text-emerald-600">
                                                <a :href="item.url" x-text="item.name.{{ $locale }} || item.name.en"></a>
                                            </h4>
                                            <template x-if="item.specialty">
                                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-800" x-text="item.specialty.name.{{ $locale }} || item.specialty.name.en"></span>
                                            </template>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2" x-text="item.overview"></p>
                                    </div>

                                    <div class="flex items-center gap-4 flex-shrink-0">
                                        <div class="text-right">
                                            <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400" x-text="`${item.match_percentage}%`"></span>
                                            <span class="block text-[10px] text-gray-400">{{ $isBn ? 'ম্যাচ স্কোর' : 'Match score' }}</span>
                                        </div>
                                        <a :href="item.url" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 border border-gray-200 dark:border-gray-600 hover:bg-emerald-50 text-xs font-bold transition-colors">
                                            {{ $isBn ? 'বিস্তারিত পড়ুন →' : 'Read Guide →' }}
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- 3. Recommended Specialists from doctorbd24 Directory --}}
                    <template x-if="results.recommended_doctors && results.recommended_doctors.length > 0">
                        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                        <span>🩺</span>
                                        {{ $isBn ? 'এই সমস্যার জন্য প্রস্তাবিত বিশেষজ্ঞ ডাক্তার' : 'Recommended Specialists for Consultation' }}
                                    </h3>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $isBn ? 'সঠিক চিকিৎসার জন্য DoctorBD24 ডিরেক্টরি থেকে বিশেষজ্ঞ বেছে নিন' : 'Verified specialist doctors in Bangladesh available for appointment' }}
                                    </p>
                                </div>
                                <a href="{{ route('doctors.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">
                                    {{ $isBn ? 'সকল ডাক্তার দেখুন →' : 'View all doctors →' }}
                                </a>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <template x-for="doc in results.recommended_doctors" :key="doc.slug">
                                    <div class="p-4 rounded-2xl bg-gray-50/60 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700 hover:border-emerald-300 transition-colors flex items-center gap-3">
                                        <template x-if="doc.photo">
                                            <img :src="doc.photo" class="w-12 h-12 rounded-xl object-cover flex-shrink-0 border border-gray-200 dark:border-gray-600">
                                        </template>
                                        <template x-if="!doc.photo">
                                            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-sm flex-shrink-0" x-text="doc.name.charAt(0)"></div>
                                        </template>

                                        <div class="flex-1 min-w-0">
                                            <a :href="doc.url" class="font-bold text-sm text-gray-900 dark:text-gray-100 hover:text-emerald-600 block truncate" x-text="doc.name"></a>
                                            <p class="text-[11px] text-emerald-600 dark:text-emerald-400 truncate" x-text="doc.specialty"></p>
                                            <p class="text-[10px] text-gray-400 truncate" x-text="`📍 ${doc.district}`"></p>
                                        </div>

                                        <a :href="doc.url" class="px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 text-xs font-bold hover:bg-emerald-100 transition-colors">
                                            {{ $isBn ? 'সিরিয়াল' : 'Profile' }}
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- 4. Disclaimer & Reset Bar --}}
                    <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300">
                        <strong>{{ $isBn ? 'সতর্কতা:' : 'Notice:' }}</strong> {{ $isBn ? 'এই ফলাফল কেবল সাধারণ তথ্যের উদ্দেশ্যে আন্তর্জাতিক চিকিৎসা গাইডলাইন অনুযায়ী প্রস্তুত। এটি বিশেষজ্ঞ চিকিৎসকের সরাসরি পরামর্শ বা প্রেসক্রিপশনের বিকল্প নয়।' : 'This symptom assessment is intended for informational and educational purposes only and does not constitute a formal medical diagnosis.' }}
                    </div>

                    <div class="text-center pt-2">
                        <button type="button" @click="resetChecker()" class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100">
                            🔄 {{ $isBn ? 'নতুন করে লক্ষণ পরীক্ষা করুন' : 'Start Another Symptom Check' }}
                        </button>
                    </div>

                </div>
            </template>
        </div>

    </div>
</div>
@endsection
