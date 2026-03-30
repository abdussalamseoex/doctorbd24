<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Ambulance;
use App\Models\BlogPost;
use App\Models\JoinRequest;
use App\Models\Review;
use App\Models\Specialty;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        
        if (!$user->hasRole('admin') && !$user->can('view analytics')) {
            return view('admin.dashboard', [
                'stats' => null, 'recentRequests' => null, 'pendingReviews' => null, 
                'recentDoctors' => null, 'recentHospitals' => null, 'chartDates' => null, 'chartData' => null
            ]);
        }

        $stats = [
            'doctors'         => Doctor::count(),
            'hospitals'       => Hospital::count(),
            'ambulances'      => Ambulance::count(),
            'blog_posts'      => BlogPost::count(),
            'pending_doctors' => JoinRequest::where('status', 'pending')->count(),
            'pending_reviews' => Review::whereNull('approved_at')->count(),
            'users'           => \App\Models\User::count(),
            'specialties'     => Specialty::count(),
        ];

        $recentRequests  = JoinRequest::latest()->take(5)->get();
        $pendingReviews  = Review::with('user')->whereNull('approved_at')->latest()->take(5)->get();
        $recentDoctors   = Doctor::latest()->take(5)->get();
        $recentHospitals = Hospital::latest()->take(5)->get();

        // Analytics: User Growth (Last 30 Days)
        $userGrowth = \App\Models\User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $chartDates = [];
        $chartData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartDates[] = now()->subDays($i)->format('M d');
            
            $dayData = $userGrowth->firstWhere('date', $date);
            $chartData[] = $dayData ? $dayData->count : 0;
        }

        return view('admin.dashboard', compact(
            'stats', 'recentRequests', 'pendingReviews', 'recentDoctors', 'recentHospitals',
            'chartDates', 'chartData'
        ));
    }
}
