<?php

namespace App\Http\Controllers;

use App\Models\Chamber;
use App\Models\Hospital;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DoctorProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $doctor = $user->doctor;

        if (!$doctor) {
            return redirect()->route('user.dashboard')->with('error', 'No doctor profile linked to your account.');
        }

        $specialties       = Specialty::orderBy('name->en')->get();
        $divisions         = \App\Models\Division::orderBy('id')->get();
        $hospitals         = Hospital::orderBy('name')->get();
        $doctorSpecialties = $doctor->specialties->pluck('id')->toArray();

        return view('doctor.profile.edit', compact('doctor', 'specialties', 'divisions', 'hospitals', 'doctorSpecialties'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $doctor = $user->doctor;

        if (!$doctor) {
            return redirect()->route('user.dashboard')->with('error', 'No doctor profile linked to your account.');
        }

        $validated = $request->validate([
            'name'             => ['required','string','max:255'],
            'designation'      => ['nullable','string','max:255'],
            'qualifications'   => ['nullable','string'],
            'gender'           => ['required','in:male,female'],
            'phone'            => ['nullable','string','max:20'],
            'email'            => ['nullable','email','max:255'],
            'bio'              => ['nullable','string'],
            'experience_years' => ['nullable','integer','min:0'],
            'specialties'      => ['nullable','array'],
            'photo'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'cover_image'      => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'gallery'          => ['nullable','array','max:10'],
            'gallery.*'        => ['image','mimes:jpeg,png,webp','max:2048'],
            'facebook_url'     => ['nullable','url','max:255'],
            'twitter_url'      => ['nullable','url','max:255'],
            'instagram_url'    => ['nullable','url','max:255'],
            'linkedin_url'     => ['nullable','url','max:255'],
            'youtube_url'      => ['nullable','url','max:255'],
        ]);

        if ($request->hasFile('photo')) {
            if ($doctor->photo) {
                Storage::disk('public')->delete($doctor->photo);
            }
            $validated['photo'] = $request->file('photo')->store('doctors', 'public');
        } elseif ($request->boolean('remove_photo') && $doctor->photo) {
            Storage::disk('public')->delete($doctor->photo);
            $validated['photo'] = null;
        } else {
            unset($validated['photo']);
        }

        if ($request->hasFile('cover_image')) {
            if ($doctor->cover_image) Storage::disk('public')->delete($doctor->cover_image);
            $validated['cover_image'] = $request->file('cover_image')->store('doctors/covers', 'public');
        } elseif ($request->boolean('remove_cover_image') && $doctor->cover_image) {
            Storage::disk('public')->delete($doctor->cover_image);
            $validated['cover_image'] = null;
        } else {
            unset($validated['cover_image']);
        }

        $existingGallery = $doctor->gallery ?? [];
        if ($request->has('remove_gallery')) {
            $removeKeys = $request->input('remove_gallery');
            foreach ($removeKeys as $key) {
                if (isset($existingGallery[$key])) {
                    Storage::disk('public')->delete($existingGallery[$key]);
                    unset($existingGallery[$key]);
                }
            }
            $existingGallery = array_values($existingGallery);
            $validated['gallery'] = $existingGallery;
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $existingGallery[] = $file->store('doctors/gallery', 'public');
            }
            $validated['gallery'] = $existingGallery;
        } elseif (!isset($validated['gallery'])) {
            unset($validated['gallery']);
        }

        $doctor->update($validated);
        
        if (isset($validated['specialties'])) {
            $doctor->specialties()->sync($validated['specialties']);
        } else {
            $doctor->specialties()->detach();
        }

        if ($request->has('chambers')) {
            $this->saveChambers($doctor, $request->input('chambers', []));
        }

        return redirect()->route('doctor.profile.edit')->with('success', 'Your public doctor profile has been updated!');
    }

    private function saveChambers(\App\Models\Doctor $doctor, array $chambersInput): void
    {
        $submittedIds = [];

        foreach ($chambersInput as $i => $chamberData) {
            if (empty($chamberData['name'])) continue;

            $data = [
                'doctor_id'      => $doctor->id,
                'name'           => $chamberData['name'],
                'hospital_id'    => $chamberData['hospital_id'] ?: null,
                'area_id'        => $chamberData['area_id'] ?: null,
                'address'        => $chamberData['address'] ?? null,
                'visiting_hours' => $chamberData['visiting_hours'] ?? null,
                'closed_days'    => $chamberData['closed_days'] ?? null,
                'phone'          => $chamberData['phone'] ?? null,
                'lat'            => $chamberData['lat'] ?: null,
                'lng'            => $chamberData['lng'] ?: null,
                'google_maps_url'=> $chamberData['google_maps_url'] ?? null,
                'sort_order'     => $i,
            ];

            if (!empty($chamberData['id'])) {
                $chamber = Chamber::where('id', $chamberData['id'])->where('doctor_id', $doctor->id)->first();
                if ($chamber) {
                    $chamber->update($data);
                    $submittedIds[] = $chamber->id;
                }
            } else {
                $chamber = Chamber::create($data);
                $submittedIds[] = $chamber->id;
            }
        }

        $doctor->chambers()->whereNotIn('id', $submittedIds)->delete();
    }
}
