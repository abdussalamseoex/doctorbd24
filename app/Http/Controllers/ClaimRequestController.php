<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\ClaimRequest;

class ClaimRequestController extends Controller
{
    public function store(Request $request, Doctor $doctor)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to claim a profile.');
        }

        if ($doctor->user_id) {
            return back()->with('error', 'This profile has already been claimed.');
        }

        $existingClaim = auth()->user()->claimRequests()->where('doctor_id', $doctor->id)->where('status', 'pending')->exists();
        
        if ($existingClaim) {
            return back()->with('error', 'You already have a pending claim for this profile.');
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        ClaimRequest::create([
            'user_id' => auth()->id(),
            'doctor_id' => $doctor->id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your claim request has been submitted successfully and is pending admin approval.');
    }

    public function storeHospital(Request $request, \App\Models\Hospital $hospital)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to claim a profile.');
        }

        if ($hospital->user_id) {
            return back()->with('error', 'This profile has already been claimed.');
        }

        $existingClaim = auth()->user()->claimRequests()->where('hospital_id', $hospital->id)->where('status', 'pending')->exists();
        
        if ($existingClaim) {
            return back()->with('error', 'You already have a pending claim for this profile.');
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        ClaimRequest::create([
            'user_id' => auth()->id(),
            'hospital_id' => $hospital->id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your claim request has been submitted successfully and is pending admin approval.');
    }

    public function storeAmbulance(Request $request, \App\Models\Ambulance $ambulance)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to claim a profile.');
        }

        if ($ambulance->user_id) {
            return back()->with('error', 'This profile has already been claimed.');
        }

        $existingClaim = auth()->user()->claimRequests()->where('ambulance_id', $ambulance->id)->where('status', 'pending')->exists();
        
        if ($existingClaim) {
            return back()->with('error', 'You already have a pending claim for this profile.');
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        ClaimRequest::create([
            'user_id' => auth()->id(),
            'ambulance_id' => $ambulance->id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your claim request has been submitted successfully and is pending admin approval.');
    }
}
