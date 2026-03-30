@extends('layouts.app')
@section('title', 'আমার ড্যাশবোর্ড — DoctorBD24')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-16">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-sky-500 to-indigo-600 rounded-3xl p-6 md:p-8 mb-8 text-white relative overflow-hidden shadow-xl">
        <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -left-4 -bottom-4 w-32 h-32 bg-indigo-400/20 rounded-full blur-xl"></div>
        <div class="relative z-10 flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-3xl font-black shadow-inner border border-white/30">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-2xl font-black">{{ __('Hi') }}, {{ $user->name }}! 👋</h1>
                <p class="text-sky-100 text-sm mt-0.5">{{ $user->email }}</p>
                <span class="inline-block mt-1.5 px-2.5 py-0.5 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wide border border-white/30">
                    {{ $user->getRoleNames()->first() ?? 'Patient' }}
                </span>
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="relative z-10 grid grid-cols-2 sm:grid-cols-3 gap-3 mt-6">
            <div class="bg-white/15 backdrop-blur rounded-2xl p-3 text-center border border-white/20">
                <div class="text-2xl font-black">{{ $favorites->count() }}</div>
                <div class="text-xs text-sky-100 uppercase font-bold tracking-wide">{{ __('Favorites') }}</div>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-2xl p-3 text-center border border-white/20">
                <div class="text-2xl font-black">{{ $reviews->count() }}</div>
                <div class="text-xs text-sky-100 uppercase font-bold tracking-wide">{{ __('Reviews') }}</div>
            </div>
            <div class="bg-white/15 backdrop-blur rounded-2xl p-3 text-center border border-white/20 col-span-2 sm:col-span-1">
                <div class="text-2xl font-black">{{ $reviews->whereNotNull('approved_at')->count() }}</div>
                <div class="text-xs text-sky-100 uppercase font-bold tracking-wide">{{ __('Approved') }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: main content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Doctor Profile Ownership block --}}
            @if(auth()->user()->hasRole('doctor') && auth()->user()->doctor)
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl shadow-sm border border-emerald-100 dark:border-emerald-800/50 p-6 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-200/50 dark:bg-emerald-800/30 rounded-full blur-xl pointer-events-none"></div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative z-10">
                    <div>
                        <h2 class="font-black text-gray-900 dark:text-white text-lg flex items-center gap-2">
                            <span class="text-2xl">👨‍⚕️</span>
                            {{ __('Your Verified Profile') }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ __('You have gained ownership of your doctor profile. Soon you will be able to edit your details and manage chambers from here.') }}</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto mt-4 sm:mt-0">
                        <a href="{{ route('doctors.show', auth()->user()->doctor->slug) }}" target="_blank" class="w-full sm:w-auto text-center px-5 py-2.5 border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-800 rounded-xl text-emerald-600 dark:text-emerald-400 text-sm font-bold shadow-sm hover:bg-emerald-50 dark:hover:bg-gray-700 transition whitespace-nowrap">View Public Profile</a>
                        <a href="{{ route('doctor.profile.edit') }}" class="w-full sm:w-auto text-center px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition hover:-translate-y-0.5 whitespace-nowrap">Edit Details</a>
                    </div>
                </div>
            </div>
            @endif

            {{-- My Favorites --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">❤️</span>
                        {{ __('My Favorites') }}
                    </h2>
                    <a href="{{ route('favorites.index') }}" class="text-xs text-sky-600 hover:underline">{{ __('View All') }} →</a>
                </div>
                @if($favorites->count())
                    <div class="space-y-3">
                        @foreach($favorites->take(5) as $fav)
                        @php $item = $fav->favoriteable; @endphp
                        @if($item)
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0
                                {{ $fav->favoriteable_type === 'App\Models\Doctor' ? 'bg-sky-50 dark:bg-sky-900/30' : 'bg-emerald-50 dark:bg-emerald-900/30' }}">
                                {{ $fav->favoriteable_type === 'App\Models\Doctor' ? '👨‍⚕️' : '🏥' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-800 dark:text-gray-100 truncate">{{ $item->name }}</p>
                                <p class="text-xs text-gray-400">{{ $fav->favoriteable_type === 'App\Models\Doctor' ? __('Doctor') : __('Hospital') }}</p>
                            </div>
                            @if($fav->favoriteable_type === 'App\Models\Doctor')
                                <a href="{{ route('doctors.show', $item->slug) }}" class="text-xs px-3 py-1 rounded-lg bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 hover:bg-sky-100 transition-colors font-medium">{{ __('View') }}</a>
                            @else
                                <a href="{{ route('hospitals.show', $item->slug) }}" class="text-xs px-3 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 transition-colors font-medium">{{ __('View') }}</a>
                            @endif
                        </div>
                        @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-4xl mb-3">🤍</div>
                        <p class="text-sm text-gray-400">{{ __('No saved favorites yet.') }}</p>
                        <a href="{{ route('doctors.index') }}" class="mt-3 inline-block text-sm text-sky-500 hover:underline">{{ __('Browse Doctors') }} →</a>
                    </div>
                @endif
            </div>

            {{-- My Reviews --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2 mb-4">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">⭐</span>
                    {{ __('My Reviews') }}
                </h2>
                @if($reviews->count())
                    <div class="space-y-3" x-data="{ editReviewModal: false, editData: { id: null, rating: 5, comment: '' } }">
                        @foreach($reviews as $review)
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        @if($review->reviewable_type === 'App\Models\Doctor')
                                            <span class="text-xs font-bold text-sky-600 dark:text-sky-400 uppercase">{{ __('Doctor') }}</span>
                                        @elseif($review->reviewable_type === 'App\Models\Ambulance')
                                            <span class="text-xs font-bold text-red-600 dark:text-red-400 uppercase">{{ __('Ambulance') }}</span>
                                        @else
                                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase">{{ __('Hospital') }}</span>
                                        @endif
                                        <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-sm text-gray-700 dark:text-gray-200 mt-1">{{ $review->comment }}</p>
                                    @endif
                                    
                                    {{-- Edit & Delete Buttons --}}
                                    <div class="flex items-center gap-3 mt-3">
                                        <button type="button" 
                                            @click='editData = { id: {{ $review->id }}, rating: {{ $review->rating }}, comment: @json($review->comment) }; editReviewModal = true;'
                                            class="text-xs font-medium text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300 transition-colors flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            {{ __('Edit') }}
                                        </button>
                                        
                                        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Are you sure you want to delete this review?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    @if($review->approved_at)
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 font-bold uppercase">{{ __('Approved') }}</span>
                                    @else
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 font-bold uppercase">{{ __('Pending') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        {{-- Edit Modal --}}
                        <template x-teleport="body">
                            <div x-show="editReviewModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" x-transition.opacity>
                                <div @click.away="editReviewModal = false" class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden relative" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                                    <button @click="editReviewModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <div class="p-8 text-left">
                                        <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 flex items-center justify-center mb-5 shadow-sm border border-amber-200 dark:border-amber-800">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </div>
                                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">{{ __('Edit Review') }}</h3>
                                        
                                        <form :action="`{{ url('/reviews') }}/${editData.id}`" method="POST" class="space-y-4">
                                            @csrf
                                            @method('PATCH')
                                            
                                            <div>
                                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">{{ __('Rating') }}</label>
                                                <div class="flex gap-1">
                                                    <template x-for="i in 5">
                                                        <button type="button" @click="editData.rating = i" class="text-3xl transition-transform hover:scale-110">
                                                            <span x-text="editData.rating >= i ? '⭐' : '☆'" :class="editData.rating >= i ? '' : 'text-gray-300 dark:text-gray-600'"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                                <input type="hidden" name="rating" :value="editData.rating">
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">{{ __('Comment') }}</label>
                                                <textarea name="comment" x-model="editData.comment" rows="3" placeholder="{{ __('Write your review here...') }}" class="w-full border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors placeholder-gray-400 resize-none"></textarea>
                                            </div>

                                            <div class="flex gap-3 pt-2">
                                                <button type="button" @click="editReviewModal = false" class="w-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold py-3 rounded-xl shadow-sm transition-all">{{ __('Cancel') }}</button>
                                                <button type="submit" class="w-full bg-gradient-to-r from-sky-500 to-indigo-500 hover:from-sky-600 hover:to-indigo-600 text-white font-bold py-3 rounded-xl shadow-md transition-all">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-4xl mb-3">✍️</div>
                        <p class="text-sm text-gray-400">{{ __("You haven't written any reviews yet.") }}</p>
                        <a href="{{ route('doctors.index') }}" class="mt-3 inline-block text-sm text-sky-500 hover:underline">{{ __('Find a Doctor') }} →</a>
                    </div>
                @endif
            </div>

        </div>

        {{-- Right: Profile card --}}
        <div class="space-y-6">
            {{-- Profile Update --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">👤</span>
                    {{ __('My Profile') }}
                </h2>
                <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">{{ __('Full Name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">{{ __('Email') }}</label>
                        <input type="email" value="{{ $user->email }}" disabled
                            class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-400 cursor-not-allowed">
                    </div>
                    <button type="submit"
                        class="w-full py-2 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white text-sm font-bold hover:opacity-90 transition-all shadow">
                        {{ __('Update Profile') }}
                    </button>
                </form>
            </div>

            {{-- Quick Links --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center">🔗</span>
                    {{ __('Quick Links') }}
                </h2>
                <div class="space-y-2">
                    <a href="{{ route('doctors.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm text-gray-700 dark:text-gray-200 hover:bg-sky-50 dark:hover:bg-sky-900/30 hover:text-sky-600 transition-colors">
                        👨‍⚕️ <span>{{ __('Browse Doctors') }}</span>
                    </a>
                    <a href="{{ route('hospitals.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-600 transition-colors">
                        🏥 <span>{{ __('Browse Hospitals') }}</span>
                    </a>
                    <a href="{{ route('favorites.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-500 transition-colors">
                        ❤️ <span>{{ __('My Favorites') }}</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 transition-colors">
                        🔑 <span>{{ __('Change Password') }}</span>
                    </a>
                </div>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-2.5 rounded-xl border border-red-200 dark:border-red-900 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 text-sm font-medium transition-colors">
                    🚪 {{ __('Logout') }}
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
