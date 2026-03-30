@extends('admin.layouts.app')
@section('title', 'Activity Logs')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Activity Logs</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Track administrative actions and system changes.</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-6">
    <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Log Name</label>
            <input type="text" name="log_name" value="{{ request('log_name') }}" placeholder="e.g. default"
                   class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-sky-500 focus:border-sky-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Causer ID</label>
            <input type="number" name="causer_id" value="{{ request('causer_id') }}" placeholder="Admin ID"
                   class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-sky-500 focus:border-sky-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Event</label>
            <select name="event" class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-sky-500 focus:border-sky-500">
                <option value="">All Events</option>
                <option value="created" @selected(request('event') == 'created')>Created</option>
                <option value="updated" @selected(request('event') == 'updated')>Updated</option>
                <option value="deleted" @selected(request('event') == 'deleted')>Deleted</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white font-bold py-2 px-4 rounded-xl transition-colors">Filter</button>
            <a href="{{ route('admin.activity-logs.index') }}" class="p-2 bg-gray-100 dark:bg-gray-700 text-gray-500 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </a>
        </div>
    </form>
</div>

{{-- Logs Table --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/50">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User (Causer)</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Properties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->created_at->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $log->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($log->causer)
                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $log->causer->name }}</div>
                            <div class="text-xs text-gray-500">ID: {{ $log->causer_id }}</div>
                        @else
                            <span class="text-xs text-gray-400 italic">System / Unknown</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($log->description == 'created') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                            @elseif($log->description == 'updated') bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400
                            @elseif($log->description == 'deleted') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-400 @endif">
                            {{ ucfirst($log->description) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            {{ class_basename($log->subject_type) }} 
                            <span class="text-gray-400">#{{ $log->subject_id }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs font-mono text-gray-500 dark:text-gray-400">
                        @if($log->changes())
                            <div class="max-w-xs truncate" title="{{ json_encode($log->changes()) }}">
                                {{ count($log->changes()['attributes'] ?? []) }} attributes changed
                            </div>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        No activity logs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection
