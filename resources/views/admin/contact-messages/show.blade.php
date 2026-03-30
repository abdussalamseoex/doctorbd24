@extends('admin.layouts.app')
@section('title', 'Read Message')
@section('content')

<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.contact-messages.index') }}" class="p-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Message Details</h2>
    </div>
    
    <div x-data="{ openDeleteModal: false }">
        <button type="button" @click="openDeleteModal = true" class="flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-500 rounded-lg text-sm font-medium hover:bg-red-100 dark:hover:bg-red-900/40 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Delete
        </button>

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
                    <form action="{{ route('admin.contact-messages.destroy', $message->id) }}" method="POST" class="inline m-0 p-0">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition shadow-sm">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 md:p-8">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-800 pb-4">
                <span class="text-sm font-normal text-gray-500 block mb-1">Subject:</span>
                {{ $message->subject }}
            </h3>
            
            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ $message->message }}</div>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <h4 class="font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">Sender Information</h4>
            <div class="space-y-4">
                <div>
                    <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Name</span>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $message->name }}</p>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Email</span>
                    <a href="mailto:{{ $message->email }}" class="text-sky-600 dark:text-sky-400 font-medium hover:underline">{{ $message->email }}</a>
                </div>
                @if($message->phone)
                <div>
                    <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Phone</span>
                    <a href="tel:{{ $message->phone }}" class="text-gray-900 dark:text-white font-medium hover:text-sky-600">{{ $message->phone }}</a>
                </div>
                @endif
                <div>
                    <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Received At</span>
                    <p class="text-gray-700 dark:text-gray-300 text-sm">{{ $message->created_at->format('d M Y, h:i A') }} ({{ $message->created_at->diffForHumans() }})</p>
                </div>
            </div>
        </div>

        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-800/50 p-6">
            <h4 class="font-bold text-indigo-900 dark:text-indigo-300 border-b border-indigo-200/50 dark:border-indigo-800/50 pb-3 mb-4">Message Status</h4>
            
            <form action="{{ route('admin.contact-messages.status', $message->id) }}" method="POST" class="space-y-4">
                @csrf @method('PATCH')
                
                <select name="status" class="w-full rounded-lg border-indigo-200 dark:border-indigo-700 bg-white dark:bg-indigo-900/40 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-sm">
                    <option value="pending" {{ $message->status === 'pending' ? 'selected' : '' }}>⏳ Pending Review</option>
                    <option value="replied" {{ $message->status === 'replied' ? 'selected' : '' }}>✓ Replied</option>
                    <option value="resolved" {{ $message->status === 'resolved' ? 'selected' : '' }}>★ Resolved</option>
                </select>
                
                @if($message->replied_at)
                    <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">Marked as replied: {{ $message->replied_at->format('d M, h:i A') }}</p>
                @endif

                <button type="submit" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition shadow-sm">
                    Update Status
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
