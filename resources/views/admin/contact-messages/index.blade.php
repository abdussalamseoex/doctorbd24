@extends('admin.layouts.app')
@section('title', 'Contact Messages Inbox')
@section('content')

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Contact Inbox</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage inquiries received from the contact forms.</p>
    </div>
</div>

{{-- Stats/Filters --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <a href="{{ route('admin.contact-messages.index') }}" class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border {{ !request('status') ? 'border-sky-500 ring-1 ring-sky-500' : 'border-gray-200 dark:border-gray-700' }} transition-all flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Total</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $counts['total'] }}</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
    </a>
    
    <a href="{{ route('admin.contact-messages.index', ['status' => 'pending']) }}" class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border {{ request('status') === 'pending' ? 'border-amber-500 ring-1 ring-amber-500' : 'border-gray-200 dark:border-gray-700' }} transition-all flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Pending</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $counts['pending'] }}</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </a>

    <a href="{{ route('admin.contact-messages.index', ['status' => 'replied']) }}" class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border {{ request('status') === 'replied' ? 'border-green-500 ring-1 ring-green-500' : 'border-gray-200 dark:border-gray-700' }} transition-all flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Replied/Resolved</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $counts['replied'] }}</h3>
        </div>
        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
    </a>
</div>

<div x-data="{ openDeleteModal: false, deleteId: null }" class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
            <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-xs uppercase font-semibold text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="px-6 py-4">Sender</th>
                    <th class="px-6 py-4">Subject</th>
                    <th class="px-6 py-4">Date Submited</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($messages as $message)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ $message->status === 'pending' ? 'bg-sky-50/30 dark:bg-sky-900/10' : '' }}">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            @if($message->status === 'pending') <span class="w-2 h-2 rounded-full bg-sky-500"></span> @endif
                            {{ $message->name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $message->email }}</div>
                    </td>
                    <td class="px-6 py-4 max-w-xs truncate font-medium text-gray-700 dark:text-gray-200">
                        {{ $message->subject }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400 text-xs">
                        {{ $message->created_at->format('d M, Y h:ia') }}
                        <br>
                        <span class="text-[10px]">{{ $message->created_at->diffForHumans() }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($message->status === 'pending')
                            <span class="px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-semibold">Pending</span>
                        @elseif($message->status === 'replied')
                            <span class="px-2.5 py-1 rounded-full bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold">Replied</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-400 text-xs font-semibold">Resolved</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.contact-messages.show', $message->id) }}" class="p-2 text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-900/30 rounded-lg transition-colors" title="Read Message">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <button type="button" @click="openDeleteModal = true; deleteId = {{ $message->id }}" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Delete Message">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <p class="text-gray-500 dark:text-gray-400">Inbox is empty. No messages found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($messages->hasPages())
    <div class="p-4 border-t border-gray-200 dark:border-gray-800">
        {{ $messages->links() }}
    </div>
    @endif

    {{-- Delete Modal --}}
    <div x-show="openDeleteModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak style="display: none;">
        <div @click.away="openDeleteModal = false" class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl w-full max-w-sm mx-4 transform transition-all text-left">
            <div class="flex items-center gap-3 mb-4 text-red-600 dark:text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Confirm Delete</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Are you sure you want to delete this message? This cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="openDeleteModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                <form :action="'/admin/contact-messages/' + deleteId" method="POST" class="inline m-0 p-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition shadow-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
