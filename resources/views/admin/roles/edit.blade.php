@extends('admin.layouts.app')
@section('title', isset($role) ? 'Edit Role: ' . $role->name : 'Create New Role')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('admin.roles.index') }}" class="text-sm font-bold text-sky-600 hover:underline mb-2 inline-block">← Back to Roles</a>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isset($role) ? 'Edit Role: ' . $role->name : 'Create New Role' }}</h2>
    </div>
</div>

<form action="{{ isset($role) ? route('admin.roles.update', $role->id) : route('admin.roles.store') }}" method="POST" class="max-w-3xl space-y-6">
    @csrf
    @if(isset($role)) @method('PUT') @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Role Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" {{ isset($role) && in_array($role->name, ['admin', 'doctor', 'patient']) ? 'readonly' : 'required' }}
               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 focus:bg-white dark:bg-gray-700 focus:ring-2 focus:ring-sky-300 outline-none text-gray-800 dark:text-white transition-all {{ isset($role) && in_array($role->name, ['admin', 'doctor', 'patient']) ? 'opacity-70 cursor-not-allowed' : '' }}" 
               placeholder="e.g. editor, moderator">
        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        @if(isset($role) && in_array($role->name, ['admin', 'doctor', 'patient']))
            <p class="text-xs text-gray-400 mt-2">The core system roles cannot be renamed, but you can adjust their permissions below.</p>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-1">Assign Permissions</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Select which areas of the admin panel this role can manage.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($permissions as $perm)
                <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-750 cursor-pointer hover:border-indigo-300 transition-colors">
                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" 
                           @checked(in_array($perm->name, old('permissions', $rolePermissions ?? [])))
                           class="w-4 h-4 mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 block">{{ ucwords($perm->name) }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-bold shadow-md hover:shadow-lg transition-all">
            {{ isset($role) ? 'Update Role' : 'Create Role' }}
        </button>
        <a href="{{ route('admin.roles.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
    </div>
</form>
@endsection
