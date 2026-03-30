<?php

namespace App\Http\Controllers;

use App\Models\Ambulance;
use App\Models\Division;
use Illuminate\Http\Request;

class AmbulanceController extends Controller
{
    public function index()
    {
        return view('ambulances.index');
    }

    public function resolve(string $slug)
    {
        // First check if it's an Ambulance Type category
        $type = \App\Models\AmbulanceType::where('slug', $slug)->first();
        if ($type) {
            return $this->typeIndex($slug);
        }

        // If not a type, it must be a specific ambulance profile
        return $this->show($slug);
    }

    public function typeIndex(string $slug)
    {
        $type = \App\Models\AmbulanceType::where('slug', $slug)->firstOrFail();
        return view('ambulances.type', compact('type'));
    }

    public function show(string $slug)
    {
        $ambulance = Ambulance::where('slug', $slug)->with(['area.district.division', 'reviews.user'])->firstOrFail();
        
        $ambulance->incrementViewCount();
        
        $related = Ambulance::where('type', $ambulance->type)
            ->where('id', '!=', $ambulance->id)
            ->take(4)
            ->get();
            
        return view('ambulances.show', compact('ambulance', 'related'));
    }
}
