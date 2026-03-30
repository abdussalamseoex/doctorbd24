<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceFeature;
use Illuminate\Http\Request;

class AmbulanceFeatureController extends Controller
{
    public function index()
    {
        $features = AmbulanceFeature::latest()->get();
        return view('admin.ambulance-features.index', compact('features'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ambulance_features,name',
            'is_active' => 'boolean'
        ]);

        AmbulanceFeature::create([
            'name' => $request->name,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.ambulance-features.index')->with('success', 'Service Feature created successfully.');
    }

    public function update(Request $request, AmbulanceFeature $ambulance_feature)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ambulance_features,name,' . $ambulance_feature->id,
            'is_active' => 'boolean'
        ]);

        $ambulance_feature->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.ambulance-features.index')->with('success', 'Service Feature updated successfully.');
    }

    public function destroy(AmbulanceFeature $ambulance_feature)
    {
        $ambulance_feature->delete();
        return redirect()->route('admin.ambulance-features.index')->with('success', 'Service Feature deleted successfully.');
    }
}
