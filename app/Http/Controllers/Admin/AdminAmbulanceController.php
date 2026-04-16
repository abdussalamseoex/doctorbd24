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
            $query->where(function($q) use ($request) {
                $q->where('provider_name->en', 'like', '%' . $request->search . '%')
                  ->orWhere('provider_name->bn', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->has('verified')) {
            $query->where('is_verified', (bool)$request->verified);
        }
        if ($request->has('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        } elseif ($request->filled('district_id')) {
            $query->whereHas('area', function($q) use ($request) {
                $q->where('district_id', $request->district_id);
            });
        } elseif ($request->filled('division_id')) {
            $query->whereHas('area.district', function($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        $perPage = $request->input('per_page', 20);
        if (!in_array($perPage, [20, 50, 100, 200, 500])) $perPage = 20;

        $ambulances = $query->latest()->paginate($perPage)->withQueryString();
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
            'provider_name' => ['required','array'],
            'provider_name.en' => ['required','string','max:255'],
            'provider_name.bn' => ['nullable','string','max:255'],
            'type'          => ['required','array'],
            'type.*'        => ['string','in:ac,non_ac,icu,freezing'],
            'phone'         => ['required','string','max:20'],
            'whatsapp'      => ['nullable','string','max:20'],
            'backup_phone'  => ['nullable','string','max:20'],
            'address'       => ['nullable','array'],
            'address.en'    => ['nullable','string','max:500'],
            'address.bn'    => ['nullable','string','max:500'],
            'latitude'      => ['nullable','numeric'],
            'longitude'     => ['nullable','numeric'],
            'area_id'       => ['nullable','exists:areas,id'],
            'available_24h' => ['boolean'],
            'features'      => ['nullable','array'],
            'features.*'    => ['string','max:255'],
            'summary'       => ['nullable','array'],
            'summary.en'    => ['nullable','string'],
            'summary.bn'    => ['nullable','string'],
            'notes'         => ['nullable','array'],
            'notes.en'      => ['nullable','string'],
            'notes.bn'      => ['nullable','string'],
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
        
        $validated['is_verified']   = $request->boolean('is_verified');
        $validated['is_featured']   = $request->boolean('is_featured');
        if (empty($request->slug)) {
            $validated['slug'] = Str::slug($validated['provider_name']['en'] ?? '') . '-' . Str::random(4);
        } else {
            $validated['slug'] = $request->slug;
        }

        if ($request->hasFile('logo')) {
            $validated['logo'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('logo'), 'ambulances', 800);
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('cover_image'), 'ambulances/covers', 1200);
        } else {
            unset($validated['cover_image']);
        }

        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = \App\Services\ImageOptimizerService::storeAndOptimize($file, 'ambulances/gallery', 1200);
            }
            $validated['gallery'] = $galleryPaths;
        } else {
            unset($validated['gallery']);
        }


        $validated['status'] = $request->input('status', 'draft');


        if ($validated['status'] === 'published') {


            $validated['published_at'] = now();


        } elseif ($validated['status'] === 'scheduled') {


            $validated['published_at'] = $request->input('published_at');


        } else {


            $validated['published_at'] = null;


        }

        $ambulance =
 $validated['status'] = $request->input('status', 'draft');
 if ($validated['status'] === 'published') {
     $validated['published_at'] = now();
 } elseif ($validated['status'] === 'scheduled') {
     $validated['published_at'] = $request->input('published_at');
 } else {
     $validated['published_at'] = null;
 } Ambulance::create($validated);

        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('seo.og_image_file'), 'seo/og', 1200);
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
            'provider_name' => ['required','array'],
            'provider_name.en' => ['required','string','max:255'],
            'provider_name.bn' => ['nullable','string','max:255'],
            'type'          => ['required','array'],
            'type.*'        => ['string','in:ac,non_ac,icu,freezing'],
            'phone'         => ['required','string','max:20'],
            'whatsapp'      => ['nullable','string','max:20'],
            'backup_phone'  => ['nullable','string','max:20'],
            'address'       => ['nullable','array'],
            'address.en'    => ['nullable','string','max:500'],
            'address.bn'    => ['nullable','string','max:500'],
            'latitude'      => ['nullable','numeric'],
            'longitude'     => ['nullable','numeric'],
            'area_id'       => ['nullable','exists:areas,id'],
            'available_24h' => ['boolean'],
            'features'      => ['nullable','array'],
            'features.*'    => ['string','max:255'],
            'summary'       => ['nullable','array'],
            'summary.en'    => ['nullable','string'],
            'summary.bn'    => ['nullable','string'],
            'notes'         => ['nullable','array'],
            'notes.en'      => ['nullable','string'],
            'notes.bn'      => ['nullable','string'],
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
        
        $validated['is_verified']   = $request->boolean('is_verified');
        $validated['is_featured']   = $request->boolean('is_featured');

        if ($request->filled('slug')) {
            $validated['slug'] = $request->slug;
        } elseif (empty($ambulance->slug)) {
            $validated['slug'] = Str::slug($validated['provider_name']['en'] ?? '') . '-' . Str::random(4);
        }

        if ($request->hasFile('logo')) {
            if ($ambulance->logo) {
                Storage::disk('public')->delete($ambulance->logo);
            }
            $validated['logo'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('logo'), 'ambulances', 800);
        } elseif ($request->boolean('remove_logo') && $ambulance->logo) {
            Storage::disk('public')->delete($ambulance->logo);
            $validated['logo'] = null;
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('cover_image')) {
            if ($ambulance->cover_image) Storage::disk('public')->delete($ambulance->cover_image);
            $validated['cover_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('cover_image'), 'ambulances/covers', 1200);
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
                $existingGallery[] = \App\Services\ImageOptimizerService::storeAndOptimize($file, 'ambulances/gallery', 1200);
            }
            $validated['gallery'] = $existingGallery;
        } elseif (!isset($validated['gallery'])) {
            unset($validated['gallery']);
        }


        $validated['status'] = $request->input('status', 'draft');


        if ($validated['status'] === 'published') {


            $validated['published_at'] = now();


        } elseif ($validated['status'] === 'scheduled') {


            $validated['published_at'] = $request->input('published_at');


        } else {


            $validated['published_at'] = null;


        }

        $ambulance->update($validated);

        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('seo.og_image_file'), 'seo/og', 1200);
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
