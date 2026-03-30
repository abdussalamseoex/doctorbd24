<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\District;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminLocationController extends Controller
{
    // ── DIVISIONS ────────────────────────────────────────────────

    public function index()
    {
        $divisions = Division::withCount('districts')->orderBy('id')->get();
        $districts = District::with('division')->withCount('areas')->orderBy('division_id')->get();
        $areas     = Area::with('district.division')->latest()->paginate(50);
        return view('admin.locations.index', compact('divisions', 'districts', 'areas'));
    }

    // Division store
    public function storeDivision(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:100',
            'slug'    => 'nullable|string|max:100|unique:divisions,slug',
        ]);
        $slug = $request->slug ?: Str::slug($request->name_en);
        Division::create([
            'name' => ['en' => $request->name_en, 'bn' => $request->name_bn ?? $request->name_en],
            'slug' => $slug,
        ]);
        return back()->with('success', 'Division added successfully.');
    }

    public function updateDivision(Request $request, Division $division)
    {
        $request->validate([
            'name_en' => 'required|string|max:100',
            'slug'    => 'nullable|string|max:100|unique:divisions,slug,'.$division->id,
        ]);
        $division->update([
            'name' => ['en' => $request->name_en, 'bn' => $request->name_bn ?? $request->name_en],
            'slug' => $request->slug ?: Str::slug($request->name_en),
        ]);
        return back()->with('success', 'Division updated.');
    }

    public function destroyDivision(Division $division)
    {
        $division->delete();
        return back()->with('success', 'Division deleted.');
    }

    // ── DISTRICTS ────────────────────────────────────────────────

    public function storeDistrict(Request $request)
    {
        $request->validate([
            'name_en'     => 'required|string|max:100',
            'division_id' => 'required|exists:divisions,id',
            'slug'        => 'nullable|string|max:100|unique:districts,slug',
        ]);
        $slug = $request->slug ?: Str::slug($request->name_en);
        District::create([
            'division_id' => $request->division_id,
            'name'        => ['en' => $request->name_en, 'bn' => $request->name_bn ?? $request->name_en],
            'slug'        => $slug,
        ]);
        return back()->with('success', 'District added successfully.');
    }

    public function updateDistrict(Request $request, District $district)
    {
        $request->validate([
            'name_en' => 'required|string|max:100',
            'slug'    => 'nullable|string|max:100|unique:districts,slug,'.$district->id,
        ]);
        $district->update([
            'division_id' => $request->division_id ?? $district->division_id,
            'name'        => ['en' => $request->name_en, 'bn' => $request->name_bn ?? $request->name_en],
            'slug'        => $request->slug ?: Str::slug($request->name_en),
        ]);
        return back()->with('success', 'District updated.');
    }

    public function destroyDistrict(District $district)
    {
        $district->delete();
        return back()->with('success', 'District deleted.');
    }

    // ── AREAS ────────────────────────────────────────────────────

    public function storeArea(Request $request)
    {
        $request->validate([
            'name_en'     => 'required|string|max:100',
            'district_id' => 'required|exists:districts,id',
            'slug'        => 'nullable|string|max:100|unique:areas,slug',
        ]);
        $district = District::find($request->district_id);
        $slug = $request->slug ?: ($district->slug . '-' . Str::slug($request->name_en));
        Area::create([
            'district_id' => $request->district_id,
            'name'        => ['en' => $request->name_en, 'bn' => $request->name_bn ?? $request->name_en],
            'slug'        => $slug,
        ]);
        return back()->with('success', 'Area added successfully.');
    }

    public function updateArea(Request $request, Area $area)
    {
        $request->validate([
            'name_en' => 'required|string|max:100',
            'slug'    => 'nullable|string|max:100|unique:areas,slug,'.$area->id,
        ]);
        $area->update([
            'district_id' => $request->district_id ?? $area->district_id,
            'name'        => ['en' => $request->name_en, 'bn' => $request->name_bn ?? $request->name_en],
            'slug'        => $request->slug ?: $area->slug,
        ]);
        return back()->with('success', 'Area updated.');
    }

    public function destroyArea(Area $area)
    {
        $area->delete();
        return back()->with('success', 'Area deleted.');
    }
}
