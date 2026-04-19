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

        \Artesaos\SEOTools\Facades\SEOTools::setTitle('All Medical Specialties | DoctorBD24');
        \Artesaos\SEOTools\Facades\SEOTools::setDescription('Browse all medical specialties and find the right specialist doctor for your health condition in Bangladesh.');
        \Artesaos\SEOTools\Facades\SEOTools::setCanonical(url()->current());

        return view('specialties.index', compact('specialties'));
    }
}
