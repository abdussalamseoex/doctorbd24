@extends('admin.layouts.app')

@section('title', 'Profile Claim Requests')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Profile Claim Requests</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Review and manage ownership claims submitted by unregistered doctors.</p>
    </div>
    
    <div class="flex bg-white dark:bg-gray-800 rounded-lg p-1 shadow-sm border border-gray-100 dark:border-gray-700">
        <a href="{{ route('admin.claim-requests.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-md {{ !request('status') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">All</a>
        <a href="{{ route('admin.claim-requests.index', ['status' => 'pending']) }}" class="px-3 py-1.5 text-xs font-semibold rounded-md {{ request('status') === 'pending' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">Pending</a>
        <a href="{{ route('admin.claim-requests.index', ['status' => 'approved']) }}" class="px-3 py-1.5 text-xs font-semibold rounded-md {{ request('status') === 'approved' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">Approved</a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700">
                <tr>
                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">User (Claimant)</th>
                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Target Profile</th>
                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Message / Proof</th>
                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Status</th>
                    <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($requests as $req)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900 dark:text-white">{{ $req->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $req->user->email }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $isAmbulance = !is_null($req->ambulance_id);
                            $isHospital = !is_null($req->hospital_id);
                            
                            if ($isAmbulance) {
                                $target = $req->ambulance;
                                $route = $target ? route('ambulances.show', $target->slug) : '#';
                                $label = 'Ambulance';
                                $colorClass = 'text-red-600 bg-red-100';
                                $nameField = 'provider_name';
                                $subtext = 'Ambulance';
                            } elseif ($isHospital) {
                                $target = $req->hospital;
                                $route = $target ? route('hospitals.show', $target->slug) : '#';
                                $label = 'Hospital';
                                $colorClass = 'text-emerald-600 bg-emerald-100';
                                $nameField = 'name';
                                $subtext = 'Hospital';
                            } else {
                                $target = $req->doctor;
                                $route = $target ? route('doctors.show', $target->slug) : '#';
                                $label = 'Doctor';
                                $colorClass = 'text-indigo-600 bg-indigo-100';
                                $nameField = 'name';
                                $subtext = $target ? $target->designation : 'Doctor';
                            }
                        @endphp
                        @if($target)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg {{ $colorClass }} dark:bg-gray-900/30 flex items-center justify-center font-bold text-xs overflow-hidden">
                                @if(($isHospital || $isAmbulance) && $target->logo)
                                    <img src="{{ asset('storage/'.$target->logo) }}" class="w-full h-full object-cover">
                                @elseif(!$isHospital && !$isAmbulance && $target->photo)
                                    <img src="{{ asset('storage/'.$target->photo) }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($target->$nameField, 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <a href="{{ $route }}" target="_blank" class="font-bold {{ $isAmbulance ? 'text-red-600' : ($isHospital ? 'text-emerald-600' : 'text-sky-600') }} hover:underline">{{ $target->$nameField }}</a>
                                <p class="text-[10px] text-gray-500 uppercase">{{ $subtext }}</p>
                            </div>
                        </div>
                        @else
                            <span class="text-red-500 text-xs italic">Profile Deleted</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 max-w-xs whitespace-normal">
                        <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2" title="{{ $req->message }}">{{ $req->message ?: 'No message provided' }}</p>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $req->created_at->diffForHumans() }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($req->status === 'pending')
                            <span class="px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-bold border border-amber-200 dark:border-amber-800">Pending</span>
                        @elseif($req->status === 'approved')
                            <span class="px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold border border-green-200 dark:border-green-800">Approved</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-bold border border-red-200 dark:border-red-800">Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($req->status === 'pending')
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('admin.claim-requests.status', $req->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/40 text-green-600 dark:text-green-400 text-xs font-bold border border-green-200 dark:border-green-800 transition-colors">Approve</button>
                                </form>
                                <form action="{{ route('admin.claim-requests.status', $req->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 text-xs font-bold border border-red-200 dark:border-red-800 transition-colors">Reject</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('admin.claim-requests.status', $req->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $req->status === 'approved' ? 'rejected' : 'approved' }}">
                                <button type="submit" class="text-xs text-gray-400 hover:text-sky-500 focus:outline-none underline decoration-dotted">
                                    Change to {{ $req->status === 'approved' ? 'Rejected' : 'Approved' }}
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <div class="text-3xl mb-2">📬</div>
                        No claim requests found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
