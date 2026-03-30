@extends('admin.layouts.app')
@section('title', 'My Reviews')
@section('content')
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
        <h2 class="font-bold text-gray-800 dark:text-gray-100 text-lg">My Reviews</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View the feedback left by users on your profile.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50/50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 font-medium">
                <tr>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Rating</th>
                    <th class="px-6 py-4">Review</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($reviews as $review)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $review->user->name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex text-amber-400">
                            @for($i=1; $i<=5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            @endfor
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-normal min-w-[300px]">
                        <p class="text-gray-600 dark:text-gray-300">{{ $review->comment }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $review->approved_at ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400' }}">
                            {{ $review->approved_at ? 'Approved' : 'Pending' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-gray-500 dark:text-gray-400">
                        {{ $review->created_at->format('M d, Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <div class="text-5xl mb-4 opacity-50">💬</div>
                        <p class="text-gray-400 dark:text-gray-500 text-base">No reviews yet.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reviews->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
        {{ $reviews->links() }}
    </div>
    @endif
</div>
@endsection
