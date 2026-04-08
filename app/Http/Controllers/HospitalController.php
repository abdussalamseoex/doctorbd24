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
        $hospital = Hospital::published()
            ->where('slug', $slug)
            ->with(['area.district.division', 'approvedReviews.user', 'chambers.doctor.specialties'])
            ->first();

        if (!$hospital) {
            $redirect = \App\Models\RedirectLog::where('from_url', 'hospital/' . $slug)->first();
            if ($redirect) {
                return redirect($redirect->to_url, 301);
            }
            abort(404);
        }

        $hospital->incrementViewCount();

        // ── SEO ──────────────────────────────────────
        $area  = $hospital->area?->getTranslation('name', 'en') ?? '';
        $title = "{$hospital->name}" . ($area ? ", $area" : '');
        $desc  = "{$hospital->name} is a " . ucfirst($hospital->type) .
                 ($area ? " in $area, Bangladesh." : ' in Bangladesh.') .
                 ($hospital->about ? ' ' . mb_substr($hospital->about, 0, 100) . '…' : '');

        $seo = $hospital->seoMeta;
        if ($seo && $seo->title) {
            $title = $seo->title;
        } else {
            $title = "$title | DoctorBD24";
        }

        if ($seo && $seo->description) {
            $desc = $seo->description;
        }

        SEOTools::setTitle($title, false);
        SEOTools::setDescription(\Illuminate\Support\Str::limit($desc, 160));
        if ($seo && $seo->keywords) {
            SEOTools::metatags()->addKeyword(explode(',', $seo->keywords));
        }

        SEOTools::setCanonical(route('hospitals.show', $hospital->slug));
        OpenGraph::setUrl(route('hospitals.show', $hospital->slug));
        OpenGraph::setType('place');

        $ogImage = ($seo && $seo->og_image) ? (str_starts_with($seo->og_image, 'http') ? $seo->og_image : asset('storage/' . $seo->og_image)) : ($hospital->logo ? asset('storage/' . $hospital->logo) : null);
        if ($ogImage) OpenGraph::addImage($ogImage);

        JsonLd::setType('Hospital');
        JsonLd::setTitle($seo->title ?? $hospital->name);
        JsonLd::setDescription($desc);
        JsonLd::addValue('url', route('hospitals.show', $hospital->slug));
        if ($hospital->phone) JsonLd::addValue('telephone', $hospital->phone);
        if ($hospital->address) JsonLd::addValue('address', $hospital->address);
        if ($ogImage) JsonLd::addValue('image', $ogImage);
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
