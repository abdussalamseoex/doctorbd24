@extends('admin.layouts.app')

@section('title', 'Redirect Logs')

@section('content')
@extends('admin.layouts.app')

@section('title', 'Redirect Logs')

@section('content')
<div x-data="{ showAddModal: false }">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-sky-500 to-indigo-600 flex items-center gap-2">
                SEO Redirect Logs
            </h2>
            <p class="text-gray-500 text-sm mt-1">Manage manual and automatic 404 redirects.</p>
        </div>
        <button @click="showAddModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Redirect
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/50 uppercase border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Requested URL / Slug</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Redirected To</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Hits</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Last Hit At</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4 flex items-center gap-3">
                            @if($log->hits == 0)
                                <span class="px-2 py-1 text-[10px] font-bold tracking-wider rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-400 uppercase">Manual</span>
                            @else
                                <span class="px-2 py-1 text-[10px] font-bold tracking-wider rounded bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-400 uppercase">Auto</span>
                            @endif
                            <span class="text-red-500 dark:text-red-400 font-medium break-all">{{ $log->from_url }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ $log->to_url }}" target="_blank" class="text-green-600 dark:text-green-400 hover:underline inline-flex items-center gap-1 font-medium max-w-xs truncate" title="{{ $log->to_url }}">
                                {{ \Illuminate\Support\Str::limit($log->to_url, 45) }}
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ $log->hits }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                            {{ $log->last_hit_at ? \Carbon\Carbon::parse($log->last_hit_at)->diffForHumans() : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.redirect-logs.destroy', $log) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this log entry?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete Log">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4 text-gray-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">No Redirects Found</h3>
                            <p class="text-gray-500 mt-1">There are no SEO redirect logs recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    {{-- Add Redirect Modal --}}
    <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="showAddModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showAddModal" x-transition.scale class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700">
                <form action="{{ route('admin.redirect-logs.store') }}" method="POST">
                    @csrf
                    <div class="bg-indigo-600 px-4 py-4 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white relative z-10" id="modal-title">Add Manual Redirect</h3>
                        <button type="button" @click="showAddModal = false" class="text-white hover:text-indigo-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <!-- decorative pattern -->
                        <div class="absolute inset-x-0 top-0 h-full overflow-hidden opacity-10 pointer-events-none">
                            <svg class="absolute -top-1/2 -right-1/2 w-full h-[200%]" fill="none" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0 100 C 20 0 50 0 100 100 Z" fill="currentColor"/></svg>
                        </div>
                    </div>
                    <div class="px-4 pt-5 pb-6 sm:p-6 sm:pb-6">
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 p-3 rounded-lg text-sm border border-indigo-100 dark:border-indigo-800/50 mb-5 flex gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Format: <code>doctor/slug</code> or <code>hospital/slug</code>. Do not include full domain in 'From URL'.</span>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">From URL (Relative Path)</label>
                                <div class="flex rounded-lg shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                                        {{ str_replace(['http://', 'https://'], '', url('/')) }}/
                                    </span>
                                    <input type="text" name="from_url" required placeholder="e.g. hospital/popular-diagnostic"
                                           class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">To URL (Full Target Path)</label>
                                <input type="url" name="to_url" required placeholder="e.g. {{ url('/hospital/popular-diagnostic-centre') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Save Redirect
                        </button>
                        <button type="button" @click="showAddModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
