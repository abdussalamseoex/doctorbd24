@extends('admin.layouts.app')
@section('title', 'Join Requests')
@section('content')

<div class="flex gap-2 mb-5">
    <a href="{{ route('admin.join-requests.index') }}" class="px-4 py-2 rounded-lg text-xs font-medium border transition-colors {{ !request('status') ? 'bg-sky-600 text-white border-sky-600' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50' }}">All</a>
    <a href="{{ route('admin.join-requests.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-xs font-medium border transition-colors {{ request('status') === 'pending' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50' }}">Pending</a>
    <a href="{{ route('admin.join-requests.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-lg text-xs font-medium border transition-colors {{ request('status') === 'approved' ? 'bg-green-600 text-white border-green-600' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50' }}">Approved</a>
    <a href="{{ route('admin.join-requests.index', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-lg text-xs font-medium border transition-colors {{ request('status') === 'rejected' ? 'bg-red-600 text-white border-red-600' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50' }}">Rejected</a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-750 border-b border-gray-100 dark:border-gray-700">
            <tr class="text-left">
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Type</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Name</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide hidden md:table-cell">Contact</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide hidden md:table-cell">Specialty</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Location</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($requests as $req)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                <td class="px-4 py-3">
                    <span class="text-lg">{{ $req->type === 'doctor' ? '👨‍⚕️' : '🏥' }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ ucfirst($req->type) }}</span>
                </td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-700 dark:text-gray-200 text-xs">{{ $req->name }}</p>
                    @if($req->message)<p class="text-xs text-gray-400 truncate max-w-[200px]">{{ $req->message }}</p>@endif
                </td>
                <td class="px-4 py-3 hidden md:table-cell text-xs text-gray-600 dark:text-gray-300">
                    <p>{{ $req->phone }}</p>
                    <p class="text-gray-400">{{ $req->email }}</p>
                </td>
                <td class="px-4 py-3">
                    <div class="text-[10px] text-gray-500 uppercase font-bold">{{ $req->division?->name ?? '—' }}</div>
                    <div class="text-xs text-gray-700 dark:text-gray-300 font-medium">{{ $req->district?->name ?? '—' }}</div>
                    <div class="text-[11px] text-gray-400">{{ $req->area?->name ?? '—' }}</div>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $req->status === 'pending'  ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' : '' }}
                        {{ $req->status === 'approved' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : '' }}
                        {{ $req->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : '' }}
                    ">{{ ucfirst($req->status) }}</span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-1.5">
                        @if($req->status === 'pending')
                        <form method="POST" action="{{ route('admin.join-requests.status', [$req->id, 'approve']) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs hover:bg-green-200 font-medium transition-colors">✓ Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.join-requests.status', [$req->id, 'reject']) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs hover:bg-red-200 font-medium transition-colors">✕ Reject</button>
                        </form>
                        @endif
                        <span class="text-xs text-gray-400">{{ $req->created_at->diffForHumans() }}</span>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">No requests found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $requests->links() }}</div>
</div>
@endsection
