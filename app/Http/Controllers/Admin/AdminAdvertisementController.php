<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Advertisement;
use Illuminate\Support\Facades\Storage;

class AdminAdvertisementController extends Controller
{
    public function index()
    {
        $advertisements = Advertisement::latest()->paginate(15);
        return view('admin.advertisements.index', compact('advertisements'));
    }

    public function create()
    {
        return view('admin.advertisements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'image'      => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'target_url' => 'nullable|url|max:500',
            'position'   => 'required|string|max:50',
            'is_active'  => 'boolean'
        ]);

        $path = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('image'), 'advertisements', 1200);

        Advertisement::create([
            'title'      => $request->title,
            'image_path' => $path,
            'target_url' => $request->target_url,
            'position'   => $request->position,
            'is_active'  => $request->has('is_active')
        ]);

        return redirect()->route('admin.advertisements.index')->with('success', 'Advertisement created successfully.');
    }

    public function edit(string $id)
    {
        $advertisement = Advertisement::findOrFail($id);
        return view('admin.advertisements.edit', compact('advertisement'));
    }

    public function update(Request $request, string $id)
    {
        $advertisement = Advertisement::findOrFail($id);

        $request->validate([
            'title'      => 'required|string|max:255',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'target_url' => 'nullable|url|max:500',
            'position'   => 'required|string|max:50',
            'is_active'  => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists($advertisement->image_path)) {
                Storage::disk('public')->delete($advertisement->image_path);
            }
            $advertisement->image_path = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('image'), 'advertisements', 1200);
        }

        $advertisement->update([
            'title'      => $request->title,
            'target_url' => $request->target_url,
            'position'   => $request->position,
            'is_active'  => $request->has('is_active')
        ]);

        return redirect()->route('admin.advertisements.index')->with('success', 'Advertisement updated successfully.');
    }

    public function destroy(string $id)
    {
        $advertisement = Advertisement::findOrFail($id);
        
        try {
            if ($advertisement->image_path && Storage::disk('public')->exists($advertisement->image_path)) {
                Storage::disk('public')->delete($advertisement->image_path);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to delete advertisement file: " . $e->getMessage());
        }
        
        $advertisement->delete();

        return redirect()->route('admin.advertisements.index')->with('success', 'Advertisement deleted successfully.');
    }
}
