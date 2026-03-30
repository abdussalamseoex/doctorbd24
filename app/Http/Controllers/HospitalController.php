<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\Specialty;
use App\Models\Division;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\JsonLd;

class HospitalController extends Controller
{
    public function index()
    {
        return view('hospitals.index');
    }

    public function show(string $slug)
    {
        $hospital = Hospital::where('slug', $slug)
            ->with(['area.district.division', 'approvedReviews.user', 'chambers.doctor.specialties'])
            ->firstOrFail();

        $hospital->incrementViewCount();

        // ── SEO ──────────────────────────────────────
        $area  = $hospital->area?->getTranslation('name', 'en') ?? '';
        $title = "{$hospital->name}" . ($area ? ", $area" : '');
        $desc  = "{$hospital->name} is a " . ucfirst($hospital->type) .
                 ($area ? " in $area, Bangladesh." : ' in Bangladesh.') .
                 ($hospital->about ? ' ' . mb_substr($hospital->about, 0, 100) . '…' : '');

        SEOTools::setTitle("$title | DoctorBD24");
        SEOTools::setDescription($desc);
        SEOTools::setCanonical(route('hospitals.show', $hospital->slug));
        OpenGraph::setUrl(route('hospitals.show', $hospital->slug));
        OpenGraph::setType('place');

        JsonLd::setType('Hospital');
        JsonLd::setTitle($hospital->name);
        JsonLd::setDescription($desc);
        JsonLd::addValue('url', route('hospitals.show', $hospital->slug));
        if ($hospital->phone) JsonLd::addValue('telephone', $hospital->phone);
        if ($hospital->address) JsonLd::addValue('address', $hospital->address);
        // ─────────────────────────────────────────────

        // get unique doctors from chambers
        $doctors = $hospital->chambers->map(fn($c) => $c->doctor)->filter()->unique('id');

        $specialtyFilter = request('specialty');
        if ($specialtyFilter) {
            $doctors = $doctors->filter(fn($d) => $d->specialties->pluck('slug')->contains($specialtyFilter));
        }

        $specialties = \App\Models\Specialty::orderBy('name->en')->get();

        return view('hospitals.show', compact('hospital', 'doctors', 'specialties'));
    }
}
