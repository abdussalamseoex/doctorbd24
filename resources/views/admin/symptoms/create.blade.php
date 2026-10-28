@extends('admin.layouts.app')
@section('title', 'Add New Symptom')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Add New Symptom</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Define symptoms, body locations, and triage questions for the symptom checker</p>
        </div>
        <a href="{{ route('admin.symptoms.index') }}" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            ← Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('admin.symptoms.store') }}">
        @csrf
        @include('admin.symptoms._form')
    </form>
</div>
@endsection
