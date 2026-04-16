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

    public function show(string $slug, $tab = 'overview')
    {
        $doctor = Doctor::published()->where('slug', $slug)
            ->with(['specialties', 'chambers.hospital', 'chambers.area.district.division', 'approvedReviews.user'])
            ->first();

        if (!$doctor) {
            $redirect = \App\Models\RedirectLog::where('from_url', 'doctor/' . $slug)->first();
            if ($redirect) {
                if ($redirect->to_url === '410') {
                    abort(410, 'Gone');
                }
                return redirect($redirect->to_url, 301);
            }
            abort(404);
        }

        $doctor->incrementViewCount();

        $hasBn = !empty($doctor->getTranslation('name', 'bn', false));
        \Illuminate\Support\Facades\View::share('has_bn_translation', $hasBn);

        if (app()->getLocale() === 'bn' && !$hasBn) {
            \Illuminate\Support\Facades\View::share('noindex_page', true);
        }

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
        
        if ($tab === 'videos') {
            $title = "Videos of {$doctor->name} | DoctorBD24";
            SEOTools::setCanonical(route('doctors.show', ['slug' => $doctor->slug, 'tab' => 'videos']));
        } elseif ($tab === 'blog') {
            $title = "Blog by {$doctor->name} | DoctorBD24";
            SEOTools::setCanonical(route('doctors.show', ['slug' => $doctor->slug, 'tab' => 'blog']));
        } else {
            SEOTools::setCanonical(route('doctors.show', $doctor->slug));
        }

        SEOTools::setTitle($title, false);
        SEOTools::setDescription(\Illuminate\Support\Str::limit($desc, 160));
        if ($seo && $seo->keywords) {
            SEOTools::metatags()->addKeyword(explode(',', $seo->keywords));
        }

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

        $related = Doctor::published()->whereHas('specialties', function ($q) use ($doctor) {
            $q->whereIn('specialties.id', $doctor->specialties->pluck('id'));
        })->where('id', '!=', $doctor->id)
          ->where('verified', true)
          ->where(function($q) { $q->whereNull('status')->orWhere('status', '!=', 'draft'); })
          ->take(4)->get();

        return view('doctors.show', compact('doctor', 'related', 'tab'));
    }
}
