<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminAmbulanceController extends Controller
{
    use \App\Traits\HasBulkActions;
    protected $model = \App\Models\Ambulance::class;
    public function index(Request $request)
    {
        $query = Ambulance::with('area');

        if ($request->search) {
            $query->where('provider_name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('verified')) {
            $query->where('is_verified', (bool)$request->verified);
        }
        if ($request->has('featured')) {
            $query->where('is_featured', true);
        }

        $ambulances = $query->latest()->paginate(20)->withQueryString();
        return view('admin.ambulances.index', compact('ambulances'));
    }

    public function create()
    {
        $divisions = \App\Models\Division::orderBy('id')->get();
        return view('admin.ambulances.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_name' => ['required','string','max:255'],
            'type'          => ['required','array'],
            'type.*'        => ['string','in:ac,non_ac,icu,freezing'],
            'phone'         => ['required','string','max:20'],
            'whatsapp'      => ['nullable','string','max:20'],
            'backup_phone'  => ['nullable','string','max:20'],
            'address'       => ['nullable','string','max:500'],
            'latitude'      => ['nullable','numeric'],
            'longitude'     => ['nullable','numeric'],
            'area_id'       => ['nullable','exists:areas,id'],
            'available_24h' => ['boolean'],
            'features'      => ['nullable','array'],
            'features.*'    => ['string','max:255'],
            'summary'       => ['nullable','string'],
            'notes'         => ['nullable','string'],
            'meta_title'    => ['nullable','string','max:255'],
            'meta_description' => ['nullable','string'],
            'active'        => ['boolean'],
            'is_verified'   => ['boolean'],
            'is_featured'   => ['boolean'],
            'slug'          => ['nullable','string','max:255','unique:ambulances,slug'],
            'logo'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'cover_image'     => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'gallery'         => ['nullable','array','max:10'],
            'gallery.*'       => ['image','mimes:jpeg,png,webp','max:2048'],
        ]);
        $validated['available_24h'] = $request->boolean('available_24h');
        $validated['active']        = $request->boolean('active', true);
        $validated['is_verified']   = $request->boolean('is_verified');
        $validated['is_featured']   = $request->boolean('is_featured');
        if (empty($request->slug)) {
            $validated['slug'] = Str::slug($validated['provider_name']) . '-' . Str::random(4);
        } else {
            $validated['slug'] = $request->slug;
        }

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('ambulances', 'public');
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('ambulances/covers', 'public');
        } else {
            unset($validated['cover_image']);
        }

        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = $file->store('ambulances/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        } else {
            unset($validated['gallery']);
        }

        $ambulance = Ambulance::create($validated);

        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = $request->file('seo.og_image_file')->store('seo/og', 'public');
            }
            $ambulance->updateSeo($seoData);
        }
        return redirect()->route('admin.ambulances.index')->with('success', 'Ambulance added.');
    }

    public function edit(Ambulance $ambulance)
    {
        $divisions = \App\Models\Division::orderBy('id')->get();
        return view('admin.ambulances.edit', compact('ambulance', 'divisions'));
    }

    public function update(Request $request, Ambulance $ambulance)
    {
        $validated = $request->validate([
            'provider_name' => ['required','string','max:255'],
            'type'          => ['required','array'],
            'type.*'        => ['string','in:ac,non_ac,icu,freezing'],
            'phone'         => ['required','string','max:20'],
            'whatsapp'      => ['nullable','string','max:20'],
            'backup_phone'  => ['nullable','string','max:20'],
            'address'       => ['nullable','string','max:500'],
            'latitude'      => ['nullable','numeric'],
            'longitude'     => ['nullable','numeric'],
            'area_id'       => ['nullable','exists:areas,id'],
            'available_24h' => ['boolean'],
            'features'      => ['nullable','array'],
            'features.*'    => ['string','max:255'],
            'summary'       => ['nullable','string'],
            'notes'         => ['nullable','string'],
            'meta_title'    => ['nullable','string','max:255'],
            'meta_description' => ['nullable','string'],
            'active'        => ['boolean'],
            'is_verified'   => ['boolean'],
            'is_featured'   => ['boolean'],
            'slug'          => ['nullable','string','max:255','unique:ambulances,slug,'.$ambulance->id],
            'logo'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'cover_image'     => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'gallery'         => ['nullable','array','max:10'],
            'gallery.*'       => ['image','mimes:jpeg,png,webp','max:2048'],
        ]);
        $validated['available_24h'] = $request->boolean('available_24h');
        $validated['active']        = $request->boolean('active');
        $validated['is_verified']   = $request->boolean('is_verified');
        $validated['is_featured']   = $request->boolean('is_featured');

        if ($request->filled('slug')) {
            $validated['slug'] = $request->slug;
        } elseif (empty($ambulance->slug)) {
            $validated['slug'] = Str::slug($validated['provider_name']) . '-' . Str::random(4);
        }

        if ($request->hasFile('logo')) {
            if ($ambulance->logo) {
                Storage::disk('public')->delete($ambulance->logo);
            }
            $validated['logo'] = $request->file('logo')->store('ambulances', 'public');
        } elseif ($request->boolean('remove_logo') && $ambulance->logo) {
            Storage::disk('public')->delete($ambulance->logo);
            $validated['logo'] = null;
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('cover_image')) {
            if ($ambulance->cover_image) Storage::disk('public')->delete($ambulance->cover_image);
            $validated['cover_image'] = $request->file('cover_image')->store('ambulances/covers', 'public');
        } elseif ($request->boolean('remove_cover_image') && $ambulance->cover_image) {
            Storage::disk('public')->delete($ambulance->cover_image);
            $validated['cover_image'] = null;
        } else {
            unset($validated['cover_image']);
        }

        $existingGallery = $ambulance->gallery ?? [];
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
                $existingGallery[] = $file->store('ambulances/gallery', 'public');
            }
            $validated['gallery'] = $existingGallery;
        } elseif (!isset($validated['gallery'])) {
            unset($validated['gallery']);
        }

        $ambulance->update($validated);

        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = $request->file('seo.og_image_file')->store('seo/og', 'public');
            }
            $ambulance->updateSeo($seoData);
        }
        return redirect()->route('admin.ambulances.index')->with('success', 'Ambulance updated.');
    }

    public function destroy(Ambulance $ambulance)
    {
        $ambulance->delete();
        return redirect()->route('admin.ambulances.index')->with('success', 'Deleted.');
    }

    public function show(Ambulance $ambulance)
    {
        return redirect()->route('admin.ambulances.edit', $ambulance->id);
    }
}
