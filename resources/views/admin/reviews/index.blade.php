@extends('admin.layouts.app')
@section('title', 'Reviews')
@section('content')

<div class="flex gap-2 mb-5">
    <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 rounded-lg text-xs font-medium border transition-colors {{ !request('status') ? 'bg-sky-600 text-white border-sky-600' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300' }}">All</a>
    <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-xs font-medium border transition-colors {{ request('status') === 'pending' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300' }}">Pending</a>
    <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-lg text-xs font-medium border transition-colors {{ request('status') === 'approved' ? 'bg-green-600 text-white border-green-600' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300' }}">Approved</a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-750 border-b border-gray-100 dark:border-gray-700">
            <tr class="text-left">
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">User</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Rating</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide hidden md:table-cell">Comment</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide hidden md:table-cell">For</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($reviews as $review)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                <td class="px-4 py-3 text-xs font-medium text-gray-700 dark:text-gray-200">{{ $review->user->name }}</td>
                <td class="px-4 py-3">
                    <div class="flex">
                        @for($i=1;$i<=5;$i++)
                            <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                </td>
                <td class="px-4 py-3 hidden md:table-cell text-xs text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $review->comment ?: '—' }}</td>
                <td class="px-4 py-3 hidden md:table-cell text-xs text-gray-500 dark:text-gray-400">
                    @if($review->reviewable)
                        <span>{{ class_basename($review->reviewable_type) }}:</span>
                        <span class="font-medium">{{ $review->reviewable->name }}</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $review->approved_at ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                        {{ $review->approved_at ? 'Approved' : 'Pending' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-1.5">
                        @if(!$review->approved_at)
                        <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs hover:bg-green-200 font-medium transition-colors" title="Approve">✓</button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs hover:bg-red-200 font-medium transition-colors" title="Delete">✕</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">No reviews found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $reviews->links() }}</div>
</div>
@endsection
