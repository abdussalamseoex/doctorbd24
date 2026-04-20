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
    public function index(\Illuminate\Http\Request $request)
    {
        $title = __('Find Doctor');
        $desc = "Find the best doctors, specialists, and medical experts in Bangladesh. Discover their detailed profiles, visit timings, chamber locations, and contact info.";

        $specialtyStr = '';

        if ($request->has('specialty')) {
            $specialty = \App\Models\Specialty::where('slug', $request->query('specialty'))->first();
            if ($specialty) {
                $spName = $specialty->getTranslation('name', app()->getLocale());
                $title = app()->getLocale() == 'bn' 
                    ? "সেরা {$spName} ডাক্তার খুঁজুন" 
                    : "Best {$spName} Doctors";
                $specialtyStr = $spName;
            } else {
                $title = "Doctor Specialty: " . ucfirst(str_replace('-', ' ', $request->query('specialty')));
            }
        }

        if ($request->has('search')) {
            $search = substr(strip_tags((string)$request->query('search')), 0, 50);
            $title = app()->getLocale() == 'bn' ? "{$search} এর জন্য ফলাফল" : "Search results for: {$search}";
        }

        if ($specialtyStr) {
            $desc = "Find top-rated {$specialtyStr} doctors. View patient reviews, fees, profiles, and book appointments easily on DoctorBD24.";
        }

        SEOTools::setTitle($title . ' | DoctorBD24', false);
        SEOTools::setDescription($desc);
        
        $queryParams = array_filter($request->only(['specialty', 'district', 'area', 'search', 'gender']));
        if (!empty($queryParams)) {
            SEOTools::setCanonical(url()->current() . '?' . http_build_query($queryParams));
        } else {
            SEOTools::setCanonical(url()->current());
        }

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
        $primarySpecialty = $doctor->specialties->first() ? $doctor->specialties->first()->getTranslation('name', 'en') : 'Specialist';
        $primaryChamber = $doctor->chambers->sortByDesc('is_main')->first();
        $locationName = $primaryChamber && $primaryChamber->area ? $primaryChamber->area->getTranslation('name', 'en') : ($primaryChamber && $primaryChamber->hospital ? $primaryChamber->hospital->name : 'Bangladesh');
        
        if ($doctor->designation) {
            $title = "{$doctor->name} — {$doctor->designation} in {$locationName}";
        } else {
            $title = "{$doctor->name} — {$primarySpecialty} in {$locationName}";
        }

        $desc = "{$doctor->name} is a {$doctor->designation}" .
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
            SEOTools::setCanonical(url()->current());
        } elseif ($tab === 'blog') {
            $title = "Blog by {$doctor->name} | DoctorBD24";
            SEOTools::setCanonical(url()->current());
        } else {
            SEOTools::setCanonical(url()->current());
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

        JsonLd::setType('Physician');
        JsonLd::setTitle($seo->title ?? $title);
        JsonLd::setDescription($desc);
        JsonLd::addValue('url', route('doctors.show', $doctor->slug));
        if ($ogImage) JsonLd::addValue('image', $ogImage);
        if ($spNames) JsonLd::addValue('medicalSpecialty', $spNames);
        
        if ($primaryChamber) {
            if (!empty($primaryChamber->phone)) {
                JsonLd::addValue('telephone', $primaryChamber->phone);
            }
            if (!empty($primaryChamber->address)) {
                JsonLd::addValue('address', [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $primaryChamber->address,
                    'addressCountry' => 'BD'
                ]);
            }
        }

        if ($doctor->approvedReviews && $doctor->approvedReviews->count() > 0) {
            JsonLd::addValue('aggregateRating', [
                '@type'       => 'AggregateRating',
                'ratingValue' => round((float)$doctor->average_rating, 1),
                'reviewCount' => $doctor->approvedReviews->count(),
            ]);
        }
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
