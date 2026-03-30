<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialties = Specialty::withCount('doctors')->get()->sortBy(function($specialty) {
            return $specialty->getTranslation('name', app()->getLocale());
        });


        return view('specialties.index', compact('specialties'));
    }
}
