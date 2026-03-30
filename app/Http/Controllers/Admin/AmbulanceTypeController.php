<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AmbulanceTypeController extends Controller
{
    public function index()
    {
        $types = AmbulanceType::latest()->get();
        return view('admin.ambulance-types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.ambulance-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (AmbulanceType::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '_' . $counter;
            $counter++;
        }
        $slug = str_replace('-', '_', $slug);

        AmbulanceType::create([
            'name' => $request->name,
            'slug' => $slug,
            'content' => $request->content,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.ambulance-types.index')->with('success', 'Ambulance Type created successfully.');
    }

    public function edit(AmbulanceType $ambulance_type)
    {
        return view('admin.ambulance-types.edit', compact('ambulance_type'));
    }

    public function update(Request $request, AmbulanceType $ambulance_type)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $slug = Str::slug($request->name);
        if ($ambulance_type->name !== $request->name) {
            $originalSlug = $slug;
            $counter = 1;
            while (AmbulanceType::where('slug', $slug)->where('id', '!=', $ambulance_type->id)->exists()) {
                $slug = $originalSlug . '_' . $counter;
                $counter++;
            }
            $slug = str_replace('-', '_', $slug);
        } else {
            $slug = $ambulance_type->slug;
        }

        $ambulance_type->update([
            'name' => $request->name,
            'slug' => $slug,
            'content' => $request->content,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.ambulance-types.index')->with('success', 'Ambulance Type updated successfully.');
    }

    public function destroy(AmbulanceType $ambulance_type)
    {
        $ambulance_type->delete();
        return redirect()->route('admin.ambulance-types.index')->with('success', 'Ambulance Type deleted successfully.');
    }
}
