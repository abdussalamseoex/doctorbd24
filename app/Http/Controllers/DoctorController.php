<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\Division;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\JsonLd;

class DoctorController extends Controller
{
    public function index()
    {
        return view('doctors.index');
    }

    public function show(string $slug)
    {
        $doctor = Doctor::where('slug', $slug)
            ->with(['specialties', 'chambers.hospital', 'chambers.area.district.division', 'approvedReviews.user'])
            ->firstOrFail();

        $doctor->incrementViewCount();

        // ── SEO for profile ──────────────────────────
        $spNames = $doctor->specialties->map(fn($s) => $s->getTranslation('name', 'en'))->join(', ');
        $title   = "{$doctor->name} — {$doctor->designation}";
        $desc    = "{$doctor->name} is a {$doctor->designation}" .
                   ($spNames ? " specializing in {$spNames}" : '') .
                   ". {$doctor->experience_years} years of experience. Find chamber locations, visiting hours & contact.";

        $seo = $doctor->seoMeta;
        if ($seo && $seo->title) {
            $title = $seo->title;
        } else {
            $title = "$title | DoctorBD24";
        }

        if ($seo && $seo->description) {
            $desc = $seo->description;
        }

        SEOTools::setTitle($title, false);
        SEOTools::setDescription(Str_limit($desc, 160));
        if ($seo && $seo->keywords) {
            SEOTools::metatags()->addKeyword(explode(',', $seo->keywords));
        }

        SEOTools::setCanonical(route('doctors.show', $doctor->slug));
        OpenGraph::setUrl(route('doctors.show', $doctor->slug));
        OpenGraph::setType('profile');
        
        $ogImage = ($seo && $seo->og_image) ? (str_starts_with($seo->og_image, 'http') ? $seo->og_image : asset('storage/' . $seo->og_image)) : ($doctor->photo ? asset('storage/' . $doctor->photo) : null);
        if ($ogImage) OpenGraph::addImage($ogImage);

        JsonLd::setType('Person');
        JsonLd::setTitle($seo->title ?? $doctor->name);
        JsonLd::setDescription($desc);
        JsonLd::addValue('jobTitle', $doctor->designation);
        JsonLd::addValue('url', route('doctors.show', $doctor->slug));
        if ($ogImage) JsonLd::addValue('image', $ogImage);
        // ─────────────────────────────────────────────

        $related = Doctor::whereHas('specialties', function ($q) use ($doctor) {
            $q->whereIn('specialties.id', $doctor->specialties->pluck('id'));
        })->where('id', '!=', $doctor->id)->where('verified', true)->take(4)->get();

        return view('doctors.show', compact('doctor', 'related'));
    }
}

function Str_limit(string $value, int $limit): string
{
    if (mb_strwidth($value, 'UTF-8') <= $limit) {
        return $value;
    }
    return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . '…';
}
