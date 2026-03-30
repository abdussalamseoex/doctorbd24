<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\Request;

class ProviderReviewController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Review::with('user')->latest();

        if ($user->hasRole('doctor') && $user->doctor) {
            $query->where('reviewable_type', Doctor::class)
                  ->where('reviewable_id', $user->doctor->id);
        } elseif ($user->hasRole('hospital') && $user->hospital) {
            $query->where('reviewable_type', Hospital::class)
                  ->where('reviewable_id', $user->hospital->id);
        } else {
            abort(403, 'No provider profile found. You must claim or create a profile first.');
        }

        $reviews = $query->paginate(20);

        return view('provider.reviews.index', compact('reviews'));
    }
}
