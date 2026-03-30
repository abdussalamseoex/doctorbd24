@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">User Management</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Manage system users, assign staff roles, and oversee account access.</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="p-4 border-b border-gray-100 dark:border-gray-700 space-y-4 md:space-y-0 md:flex items-center justify-between">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex-1 max-w-lg flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." 
                   class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 px-4 py-2 text-sm">
            <select name="role" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-sky-500 px-3 py-2 text-sm">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">Apply</button>
            @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl text-sm font-medium transition-colors">Clear</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700">
                <tr>
                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">User</th>
                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Role</th>
                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Joined Date</th>
                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Status</th>
                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-sm">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->roles->count() > 0)
                            <span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 font-bold uppercase tracking-wider text-[11px] border border-indigo-100 dark:border-indigo-800">
                                {{ $user->roles->first()->name }}
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 font-medium text-[11px] border border-gray-200 dark:border-gray-700">
                                User
                            </span>
                        @endif
                        
                        @if($user->id === auth()->id())
                            <span class="text-xs text-gray-400 italic ml-2">(You)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs text-center sm:text-left">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($user->banned_at)
                            <span class="px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-medium border border-red-200 dark:border-red-800">Banned</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium border border-green-200 dark:border-green-800">Active</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @if($user->id !== auth()->id())
                            <div x-data="{ openRoleModal: false }" class="inline-block relative text-left whitespace-normal">
                                <button type="button" @click="openRoleModal = true" class="inline-flex p-2 items-center justify-center rounded-lg text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-500/10 transition-colors" title="Manage Role">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </button>
                                
                                {{-- Role Modal --}}
                                <div x-show="openRoleModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="openRoleModal" x-transition.opacity class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity" @click="openRoleModal = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="openRoleModal" x-transition class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100 dark:border-gray-700">
                                            <form method="POST" action="{{ route('admin.users.role', $user->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <div class="px-6 pt-6 pb-4">
                                                    <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">Manage Role for {{ $user->name }}</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Assign a system role to this user to control their access level.</p>
                                                    <div class="mt-4">
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Select User Role</label>
                                                        <select name="role" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-sky-500 px-4 py-2.5 text-sm outline-none">
                                                            <option value="">-- No Special Role (User) --</option>
                                                            @foreach($roles as $role)
                                                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                                    {{ ucfirst($role->name) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex items-center justify-end gap-3">
                                                    <button type="button" @click="openRoleModal = false" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition">Save Role</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('admin.users.toggle-ban', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to {{ $user->banned_at ? 'unban' : 'ban' }} this user?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex p-2 items-center justify-center rounded-lg {{ $user->banned_at ? 'text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-500/10' : 'text-orange-600 hover:bg-orange-50 dark:text-orange-400 dark:hover:bg-orange-500/10' }} transition-colors" title="{{ $user->banned_at ? 'Unban' : 'Ban' }} User">
                                    @if($user->banned_at)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    @endif
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to permanently delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex p-2 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors" title="Delete User">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-gray-400 italic">Current Session</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        No users found matching your criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
