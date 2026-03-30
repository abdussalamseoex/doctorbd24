<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HospitalProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $hospital = $user->hospital;

        if (!$hospital) {
            return redirect()->route('admin.dashboard')->with('error', 'No hospital profile linked to your account.');
        }

        $divisions = \App\Models\Division::orderBy('id')->get();

        return view('hospital.profile.edit', compact('hospital', 'divisions'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $hospital = $user->hospital;

        if (!$hospital) {
            return redirect()->route('admin.dashboard')->with('error', 'No hospital profile linked to your account.');
        }

        $validated = $request->validate([
            'name'            => ['required','string','max:255'],
            'type'            => ['required','in:hospital,clinic,diagnostic'],
            'about'           => ['nullable','string'],
            'phone'           => ['nullable','string','max:255'],
            'email'           => ['nullable','email','max:255'],
            'website'         => ['nullable','url','max:255'],
            'address'         => ['required','string','max:255'],
            'area_id'         => ['required','exists:areas,id'],
            'lat'             => ['nullable','numeric'],
            'lng'             => ['nullable','numeric'],
            'google_maps_url' => ['nullable','url','max:500'],
            'logo'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($hospital->logo) {
                Storage::disk('public')->delete($hospital->logo);
            }
            $validated['logo'] = $request->file('logo')->store('hospitals/logos', 'public');
        } elseif ($request->boolean('remove_logo') && $hospital->logo) {
            Storage::disk('public')->delete($hospital->logo);
            $validated['logo'] = null;
        } else {
            unset($validated['logo']);
        }

        $hospital->update($validated);

        return redirect()->route('hospital.profile.edit')->with('success', 'Your hospital profile has been successfully updated!');
    }
}
