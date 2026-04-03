<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminHospitalController extends Controller
{
    use \App\Traits\HasBulkActions;
    protected $model = \App\Models\Hospital::class;
    public function index(Request $request)
    {
        $query = Hospital::with('area');
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        if ($request->has('verified')) {
            $query->where('verified', (bool)$request->verified);
        }
        if ($request->has('featured')) {
            $query->where('featured', true);
        }
        $hospitals = $query->latest()->paginate(20)->withQueryString();
        return view('admin.hospitals.index', compact('hospitals'));
    }

    public function create()
    {
        $divisions = \App\Models\Division::orderBy('id')->get();
        return view('admin.hospitals.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required','string','max:255'],
            'type'            => ['required','in:hospital,diagnostic,clinic,other'],
            'phone'           => ['nullable','string','max:20'],
            'email'           => ['nullable','email'],
            'address'         => ['nullable','string'],
            'website'         => ['nullable','url'],
            'area_id'         => ['nullable','exists:areas,id'],
            'slug'            => ['nullable','string','max:255','unique:hospitals,slug'],
            'verified'        => ['boolean'],
            'featured'        => ['boolean'],
            'about'           => ['nullable','string'],
            'lat'             => ['nullable','numeric'],
            'lng'             => ['nullable','numeric'],
            'google_maps_url' => ['nullable','url'],
            'facebook_url'    => ['nullable','url'],
            'instagram_url'   => ['nullable','url'],
            'youtube_url'     => ['nullable','url'],
            'logo'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'banner'          => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'gallery'         => ['nullable','array','max:10'],
            'gallery.*'       => ['image','mimes:jpeg,png,webp','max:2048'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        }
        $validated['verified'] = $request->boolean('verified');
        $validated['featured'] = $request->boolean('featured');

        // Handle services JSON (sent as stringified JSON from Alpine.js)
        $validated['services']      = $this->parseJsonField($request->input('services'));
        $validated['opening_hours'] = $this->parseJsonField($request->input('opening_hours'));

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('hospitals', 'public');
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('hospitals/covers', 'public');
        } else {
            unset($validated['banner']);
        }

        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = $file->store('hospitals/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        } else {
            unset($validated['gallery']);
        }

        $hospital = Hospital::create($validated);
        
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = $request->file('seo.og_image_file')->store('seo/og', 'public');
            }
            $hospital->updateSeo($seoData);
        }
        $hasDuplicate = Hospital::where('name', $hospital->name)->where('id', '!=', $hospital->id)->exists();
        $message = 'Hospital added successfully.';
        if ($hasDuplicate) {
            $message .= ' ⚠ Warning: Another Hospital with the same name exists in the system.';
        }

        return redirect()->route('admin.hospitals.index')->with('success', $message);
    }

    public function edit(Hospital $hospital)
    {
        $divisions = \App\Models\Division::orderBy('id')->get();
        return view('admin.hospitals.edit', compact('hospital', 'divisions'));
    }

    public function update(Request $request, Hospital $hospital)
    {
        $validated = $request->validate([
            'name'            => ['required','string','max:255'],
            'type'            => ['required','in:hospital,diagnostic,clinic,other'],
            'phone'           => ['nullable','string','max:20'],
            'email'           => ['nullable','email'],
            'address'         => ['nullable','string'],
            'website'         => ['nullable','url'],
            'area_id'         => ['nullable','exists:areas,id'],
            'slug'            => ['nullable','string','max:255','unique:hospitals,slug,'.$hospital->id],
            'verified'        => ['boolean'],
            'featured'        => ['boolean'],
            'about'           => ['nullable','string'],
            'lat'             => ['nullable','numeric'],
            'lng'             => ['nullable','numeric'],
            'google_maps_url' => ['nullable','url'],
            'facebook_url'    => ['nullable','url'],
            'instagram_url'   => ['nullable','url'],
            'youtube_url'     => ['nullable','url'],
            'logo'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'banner'          => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'gallery'         => ['nullable','array','max:10'],
            'gallery.*'       => ['image','mimes:jpeg,png,webp','max:2048'],
        ]);

        $validated['verified'] = $request->boolean('verified');
        $validated['opening_hours'] = $this->parseJsonField($request->input('opening_hours'));

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        }

        if ($request->hasFile('logo')) {
            if ($hospital->logo) {
                Storage::disk('public')->delete($hospital->logo);
            }
            $validated['logo'] = $request->file('logo')->store('hospitals', 'public');
        } elseif ($request->boolean('remove_logo') && $hospital->logo) {
            Storage::disk('public')->delete($hospital->logo);
            $validated['logo'] = null;
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('banner')) {
            if ($hospital->banner) Storage::disk('public')->delete($hospital->banner);
            $validated['banner'] = $request->file('banner')->store('hospitals/covers', 'public');
        } elseif ($request->boolean('remove_banner') && $hospital->banner) {
            Storage::disk('public')->delete($hospital->banner);
            $validated['banner'] = null;
        } else {
            unset($validated['banner']);
        }

        $existingGallery = $hospital->gallery ?? [];
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
                $existingGallery[] = $file->store('hospitals/gallery', 'public');
            }
            $validated['gallery'] = $existingGallery;
        } elseif (!isset($validated['gallery'])) {
            unset($validated['gallery']);
        }

        $hospital->update($validated);
        
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = $request->file('seo.og_image_file')->store('seo/og', 'public');
            }
            $hospital->updateSeo($seoData);
        }
        $hasDuplicate = Hospital::where('name', $hospital->name)->where('id', '!=', $hospital->id)->exists();
        $message = 'Hospital updated successfully.';
        if ($hasDuplicate) {
            $message .= ' ⚠ Warning: Another Hospital with the same name exists in the system.';
        }

        return redirect()->route('admin.hospitals.index')->with('success', $message);
    }

    public function destroy(Hospital $hospital)
    {
        $hospital->delete();
        return redirect()->route('admin.hospitals.index')->with('success', 'Hospital deleted.');
    }

    public function show(Hospital $hospital)
    {
        return redirect()->route('admin.hospitals.edit', $hospital->id);
    }

    /**
     * Parse JSON string from Alpine.js hidden input.
     */
    private function parseJsonField(?string $value): ?array
    {
        if (!$value) return null;
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : null;
    }
}
