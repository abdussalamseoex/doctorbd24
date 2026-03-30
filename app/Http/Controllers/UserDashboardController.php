<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user      = auth()->user();
        $favorites = $user->favorites()->with('favoriteable')->latest()->get();
        $reviews   = $user->reviews()->with('reviewable')->latest()->get();

        return view('user.dashboard', compact('user', 'favorites', 'reviews'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        auth()->user()->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }
}
