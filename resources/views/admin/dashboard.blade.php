@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')

@if(isset($stats))
{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @php
    $metrics = [
        ['label' => 'Total Doctors',   'value' => $stats['doctors'],   'icon' => '👨‍⚕️', 'color' => 'from-sky-500 to-blue-600',     'link' => route('admin.doctors.index')],
        ['label' => 'Hospitals',       'value' => $stats['hospitals'],  'icon' => '🏥', 'color' => 'from-emerald-500 to-teal-600','link' => route('admin.hospitals.index')],
        ['label' => 'Ambulances',      'value' => $stats['ambulances'], 'icon' => '🚑', 'color' => 'from-red-500 to-rose-600',     'link' => route('admin.ambulances.index')],
        ['label' => 'Blog Posts',      'value' => $stats['blog_posts'], 'icon' => '📰', 'color' => 'from-violet-500 to-purple-600','link' => route('admin.blog-posts.index')],
        ['label' => 'Pending Doctors', 'value' => $stats['pending_doctors'],  'icon' => '⏳', 'color' => 'from-amber-500 to-orange-600','link' => route('admin.join-requests.index')],
        ['label' => 'Pending Reviews', 'value' => $stats['pending_reviews'],  'icon' => '💬', 'color' => 'from-indigo-500 to-violet-600','link' => route('admin.reviews.index')],
        ['label' => 'Users',           'value' => $stats['users'],      'icon' => '👤', 'color' => 'from-pink-500 to-rose-600',    'link' => '#'],
        ['label' => 'Specialties',     'value' => $stats['specialties'],'icon' => '🩺', 'color' => 'from-teal-500 to-cyan-600',   'link' => route('admin.specialties.index')],
    ];
    @endphp

    @foreach($metrics as $m)
    <a href="{{ $m['link'] }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $m['color'] }} flex items-center justify-center text-xl shadow-md group-hover:scale-110 transition-transform">{{ $m['icon'] }}</div>
        </div>
        <div class="text-2xl font-extrabold text-gray-800 dark:text-gray-100">{{ number_format($m['value']) }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $m['label'] }}</div>
    </a>
    @endforeach
</div>

<!-- Platform Growth Chart -->
<div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-bold text-gray-700 dark:text-gray-200 text-sm">📈 Platform Growth (New Users Last 30 Days)</h2>
    </div>
    <div class="relative h-72 w-full">
        <canvas id="growthChart"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Recent Join Requests --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-bold text-gray-700 dark:text-gray-200 text-sm">⏳ Recent Join Requests</h2>
            <a href="{{ route('admin.join-requests.index') }}" class="text-xs text-sky-600 dark:text-sky-400 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($recentRequests as $req)
            <div class="px-5 py-3 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-sm">{{ $req->type === 'doctor' ? '👨‍⚕️' : '🏥' }}</div>
                    <div>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $req->name }}</p>
                        <p class="text-xs text-gray-400">{{ ucfirst($req->type) }} · {{ $req->phone }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $req->status === 'pending' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' }}">{{ ucfirst($req->status) }}</span>
                    <span class="text-xs text-gray-400">{{ $req->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @empty
            <div class="px-5 py-6 text-center text-xs text-gray-400">No pending requests.</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Reviews --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-bold text-gray-700 dark:text-gray-200 text-sm">💬 Pending Reviews</h2>
            <a href="{{ route('admin.reviews.index') }}" class="text-xs text-sky-600 dark:text-sky-400 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($pendingReviews as $review)
            <div class="px-5 py-3 flex items-center justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $review->user->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $review->comment }}</p>
                    <div class="flex mt-0.5">
                        @for($i=1;$i<=5;$i++)
                            <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                </div>
                <div class="flex gap-1.5 flex-shrink-0">
                    <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs hover:bg-green-200 transition-colors font-medium">✓ Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs hover:bg-red-200 transition-colors font-medium">✕</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="px-5 py-6 text-center text-xs text-gray-400">No pending reviews.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- Recent Doctors / Hospitals row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    {{-- Recent Doctors --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-bold text-gray-700 dark:text-gray-200 text-sm">👨‍⚕️ Latest Doctors</h2>
            <a href="{{ route('admin.doctors.index') }}" class="text-xs text-sky-600 dark:text-sky-400 hover:underline">Manage</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($recentDoctors as $doc)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $doc->name }}</p>
                    <p class="text-xs text-gray-400">{{ $doc->designation }}</p>
                </div>
                <div class="flex gap-2 items-center">
                    @if($doc->verified) <span class="text-xs text-green-600 bg-green-100 dark:bg-green-900/30 px-2 py-0.5 rounded-full">Verified</span> @endif
                    @if($doc->featured) <span class="text-xs text-amber-600 bg-amber-100 dark:bg-amber-900/30 px-2 py-0.5 rounded-full">⭐</span> @endif
                    <a href="{{ route('admin.doctors.edit', $doc->id) }}" class="text-xs text-sky-600 dark:text-sky-400 hover:underline">Edit</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Hospitals --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-bold text-gray-700 dark:text-gray-200 text-sm">🏥 Latest Hospitals</h2>
            <a href="{{ route('admin.hospitals.index') }}" class="text-xs text-sky-600 dark:text-sky-400 hover:underline">Manage</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($recentHospitals as $hosp)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $hosp->name }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ $hosp->type }}</p>
                </div>
                <div class="flex gap-2 items-center">
                    @if($hosp->verified) <span class="text-xs text-green-600 bg-green-100 dark:bg-green-900/30 px-2 py-0.5 rounded-full">Verified</span> @endif
                    <a href="{{ route('admin.hospitals.edit', $hosp->id) }}" class="text-xs text-sky-600 dark:text-sky-400 hover:underline">Edit</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('growthChart').getContext('2d');
    
    // Gradient
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(56, 189, 248, 0.4)'); // Tailwind sky-400
    gradient.addColorStop(1, 'rgba(56, 189, 248, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartDates) !!},
            datasets: [{
                label: 'New Users',
                data: {!! json_encode($chartData) !!},
                borderColor: '#38bdf8', // sky-400
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#38bdf8',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 12,
                    displayColors: false,
                    cornerRadius: 8,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, color: '#9ca3af' },
                    grid: { color: 'rgba(156, 163, 175, 0.1)', drawBorder: false }
                },
                x: {
                    ticks: { color: '#9ca3af', maxTicksLimit: 10 },
                    grid: { display: false }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        }
    });
});
</script>
@endpush
@else
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 text-center max-w-2xl mx-auto mt-10">
    <div class="w-20 h-20 bg-gradient-to-br from-sky-500 to-indigo-600 rounded-full flex items-center justify-center text-4xl text-white mx-auto mb-6 shadow-lg">
        👋
    </div>
    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Welcome, {{ auth()->user()->name }}!</h2>
    <p class="text-gray-500 dark:text-gray-400 mb-6">You are logged into the {{ ucfirst(auth()->user()->roles->first()?->name ?? 'System') }} dashboard. Use the sidebar menu to manage your profile and access permitted features.</p>
    @if(auth()->user()->hasRole('doctor'))
        <a href="{{ route('doctor.profile.edit') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-medium rounded-xl transition-colors shadow-sm focus:ring-4 focus:ring-sky-500/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            Edit Profile
        </a>
    @elseif(auth()->user()->hasRole('hospital') && Route::has('hospital.profile.edit'))
        <a href="{{ route('hospital.profile.edit') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            Manage Hospital
        </a>
    @endif
</div>
@endif

@endsection
