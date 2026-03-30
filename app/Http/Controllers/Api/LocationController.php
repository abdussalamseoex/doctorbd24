<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\District;
use App\Models\Division;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Get districts based on division ID or slug.
     */
    public function getDistricts(Request $request)
    {
        $division = null;

        if ($request->filled('division_slug')) {
            $division = Division::where('slug', $request->division_slug)->first();
        } elseif ($request->filled('division_id')) {
            $division = Division::find($request->division_id);
        }

        if (!$division) {
            return response()->json([]);
        }

        $districts = District::where('division_id', $division->id)
            ->get()
            ->map(function ($district) {
                return [
                    'id'   => $district->id,
                    'slug' => $district->slug,
                    'name' => $district->name,
                ];
            })
            ->sortBy('name')
            ->values();

        return response()->json($districts);
    }

    /**
     * Get areas based on district ID or slug.
     */
    public function getAreas(Request $request)
    {
        $district = null;

        if ($request->filled('district_slug')) {
            $district = District::where('slug', $request->district_slug)->first();
        } elseif ($request->filled('district_id')) {
            $district = District::find($request->district_id);
        }

        if (!$district) {
            return response()->json([]);
        }

        $areas = Area::where('district_id', $district->id)
            ->get()
            ->map(function ($area) {
                return [
                    'id'   => $area->id,
                    'slug' => $area->slug,
                    'name' => $area->name
                ];
            })
            ->sortBy('name')
            ->values();

        return response()->json($areas);
    }
    public function show(Area $area)
    {
        return response()->json([
            'id'          => $area->id,
            'name'        => $area->name,
            'district_id' => $area->district_id,
            'division_id' => $area->district?->division_id,
        ]);
    }
}
