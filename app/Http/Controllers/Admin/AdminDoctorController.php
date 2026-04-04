<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Chamber;
use App\Models\Hospital;
use App\Models\Specialty;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminDoctorController extends Controller
{
    use \App\Traits\HasBulkActions;
    protected $model = \App\Models\Doctor::class;
    public function index(Request $request)
    {
        $query = Doctor::with('specialties');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('verified')) {
            $query->where('verified', (bool)$request->verified);
        }
        if ($request->has('featured')) {
            $query->where('featured', true);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to all to show both published and draft, or handled via UI tabs
        }

        $doctors = $query->latest()->paginate(20)->withQueryString();
        
        $counts = [
            'all' => Doctor::count(),
            'published' => Doctor::where('status', 'published')->orWhereNull('status')->count(),
            'draft' => Doctor::where('status', 'draft')->count(),
        ];

        return view('admin.doctors.index', compact('doctors', 'counts'));
    }

    public function publish(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        Doctor::whereIn('id', $request->ids)->update(['status' => 'published']);
        return back()->with('success', count($request->ids) . ' doctors published successfully.');
    }

    public function importPopular(Request $request)
    {
        // Execute the command synchronously for simpler flow, or background
        \Illuminate\Support\Facades\Artisan::call('import:popular-doctors');
        
        return back()->with('success', 'Popular Doctors have been successfully imported into Drafts!');
    }

    public function create()
    {
        $specialties = Specialty::orderBy('name->en')->get();
        $divisions   = \App\Models\Division::orderBy('id')->get();
        $hospitals   = Hospital::orderBy('name')->get();
        return view('admin.doctors.create', compact('specialties', 'divisions', 'hospitals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => ['required','string','max:255'],
            'slug'             => ['nullable','string','max:255','unique:doctors,slug'],
            'designation'      => ['nullable','string','max:255'],
            'qualifications'   => ['nullable','string'],
            'gender'           => ['required','in:male,female'],
            'phone'            => ['nullable','string','max:20'],
            'email'            => ['nullable','email','max:255'],
            'bio'              => ['nullable','string'],
            'experience_years' => ['nullable','integer','min:0'],
            'verified'         => ['boolean'],
            'featured'         => ['boolean'],
            'specialties'      => ['nullable','array'],
            'photo'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'cover_image'      => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'gallery'          => ['nullable','array','max:10'],
            'gallery.*'        => ['image','mimes:jpeg,png,webp','max:2048'],
            'chambers'         => ['nullable','array'],
            'facebook_url'     => ['nullable','url','max:255'],
            'twitter_url'      => ['nullable','url','max:255'],
            'instagram_url'    => ['nullable','url','max:255'],
            'linkedin_url'     => ['nullable','url','max:255'],
            'youtube_url'      => ['nullable','url','max:255'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['verified'] = $request->boolean('verified');
        $validated['featured'] = $request->boolean('featured');

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('doctors', 'public');
        } else {
            unset($validated['photo']);
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('doctors/covers', 'public');
        } else {
            unset($validated['cover_image']);
        }

        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = $file->store('doctors/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        } else {
            unset($validated['gallery']);
        }

        $doctor = Doctor::create($validated);

        if (!empty($validated['specialties'])) {
            $doctor->specialties()->sync($validated['specialties']);
        }

        // Save chambers
        $this->saveChambers($doctor, $request->input('chambers', []));

        // Save SEO
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = $request->file('seo.og_image_file')->store('seo/og', 'public');
            }
            $doctor->updateSeo($seoData);
        }

        // Check for possible duplicates
        $hasDuplicate = Doctor::where('name', $doctor->name)->where('id', '!=', $doctor->id)->exists();
        $message = 'Doctor created successfully.';
        if ($hasDuplicate) {
            $message .= ' ⚠ Warning: A Doctor with the same name already exists in the system.';
        }

        return redirect()->route('admin.doctors.index')->with('success', $message);
    }

    public function edit(Doctor $doctor)
    {
        $specialties       = Specialty::orderBy('name->en')->get();
        $divisions         = \App\Models\Division::orderBy('id')->get();
        $hospitals         = Hospital::orderBy('name')->get();
        $doctorSpecialties = $doctor->specialties->pluck('id')->toArray();
        return view('admin.doctors.edit', compact('doctor', 'specialties', 'divisions', 'hospitals', 'doctorSpecialties'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name'             => ['required','string','max:255'],
            'designation'      => ['nullable','string','max:255'],
            'qualifications'   => ['nullable','string'],
            'gender'           => ['required','in:male,female'],
            'phone'            => ['nullable','string','max:20'],
            'email'            => ['nullable','email','max:255'],
            'bio'              => ['nullable','string'],
            'experience_years' => ['nullable','integer','min:0'],
            'verified'         => ['boolean'],
            'featured'         => ['boolean'],
            'specialties'      => ['nullable','array'],
            'photo'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'cover_image'      => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'gallery'          => ['nullable','array','max:10'],
            'gallery.*'        => ['image','mimes:jpeg,png,webp','max:2048'],
            'chambers'         => ['nullable','array'],
            'facebook_url'     => ['nullable','url','max:255'],
            'twitter_url'      => ['nullable','url','max:255'],
            'instagram_url'    => ['nullable','url','max:255'],
            'linkedin_url'     => ['nullable','url','max:255'],
            'youtube_url'      => ['nullable','url','max:255'],
        ]);

        $validated['verified'] = $request->boolean('verified');
        $validated['featured'] = $request->boolean('featured');
        if (empty($request->slug)) {
            if (empty($doctor->slug)) {
                $validated['slug'] = Str::slug($validated['name']);
            }
        } else {
            $validated['slug'] = $request->slug;
        }

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
        $doctor->specialties()->sync($validated['specialties'] ?? []);

        // Save chambers
        $this->saveChambers($doctor, $request->input('chambers', []));

        // Save SEO
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = $request->file('seo.og_image_file')->store('seo/og', 'public');
            }
            $doctor->updateSeo($seoData);
        }

        // Check for possible duplicates
        $hasDuplicate = Doctor::where('name', $doctor->name)->where('id', '!=', $doctor->id)->exists();
        $message = 'Doctor updated successfully.';
        if ($hasDuplicate) {
            $message .= ' ⚠ Warning: Another Doctor with the same name exists in the system.';
        }

        return redirect()->route('admin.doctors.index')->with('success', $message);
    }

    /**
     * Sync inline chambers from the form.
     * Chambers with an 'id' are updated; new ones are created.
     * Chambers removed from form are deleted.
     */
    private function saveChambers(Doctor $doctor, array $chambersInput): void
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

        // Delete chambers not in form
        $doctor->chambers()->whereNotIn('id', $submittedIds)->delete();
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('admin.doctors.index')->with('success', 'Doctor deleted.');
    }

    public function show(Doctor $doctor)
    {
        return redirect()->route('admin.doctors.edit', $doctor->id);
    }
}
