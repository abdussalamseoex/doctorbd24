@extends('layouts.app')

@section('title', __('All Specialties') . ' — DoctorBD24')
@section('meta_description', __('Browse comprehensive list of medical specialties and find the right doctor for your needs.'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 border-b border-gray-200 dark:border-gray-800 pb-5">
        <h1 class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 flex items-center gap-3">
            <span class="text-4xl">🩺</span> {{ __('All Specialties') }}
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mt-2">{{ __('Browse our complete directory of medical specialties to find the exact care you need.') }}</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse($specialties as $sp)
            <a href="{{ route('doctors.index', ['specialty' => $sp->slug]) }}"
               class="group bg-white dark:bg-gray-800 rounded-2xl p-4 text-center shadow-sm hover:shadow-xl border border-gray-100 dark:border-gray-700 hover:border-sky-300 dark:hover:border-sky-600 transition-all duration-300 flex flex-col items-center justify-center hover:-translate-y-1">
                <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">
                    {{ $sp->icon }}
                </div>
                <h3 class="font-bold text-gray-800 dark:text-gray-100 text-sm mb-1 group-hover:text-sky-600 dark:group-hover:text-sky-400">
                    {{ $sp->getTranslation('name', app()->getLocale()) }}
                </h3>
                <span class="text-xs text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                    {{ $sp->doctors_count }} {{ app()->getLocale() === 'bn' ? ' জন ডাক্তার' : __('Doctors') }}
                </span>
            </a>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="text-4xl mb-3">🩺</div>
                <p>{{ __('No Specialties Found.') }}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
