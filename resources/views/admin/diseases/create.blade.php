@extends('admin.layouts.app')
@section('title', 'Add New Disease Guide')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Add New Disease Guide</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Create a new comprehensive condition guide with Mayo Clinic standard sections</p>
        </div>
        <a href="{{ route('admin.diseases.index') }}" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            ← Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('admin.diseases.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.diseases._form')
    </form>
</div>
@endsection
