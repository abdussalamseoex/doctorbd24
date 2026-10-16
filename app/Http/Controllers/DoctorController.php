<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\Division;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\JsonLdMulti;

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

        $isBn = request()->routeIs('bn.*') || app()->getLocale() === 'bn';
        $locale = $isBn ? 'bn' : 'en';

        $hasBn = !empty($doctor->getTranslation('name', 'bn', false)) || !empty($doctor->getTranslation('bio', 'bn', false));
        \Illuminate\Support\Facades\View::share('has_bn_translation', true);

        // ── SEO for profile ──────────────────────────
        $spNames = $doctor->specialties->map(fn($s) => $s->getTranslation('name', $locale) ?: $s->name)->filter()->join(', ');
        $primarySpecialty = $doctor->specialties->first() ? ($doctor->specialties->first()->getTranslation('name', $locale) ?: $doctor->specialties->first()->name) : ($isBn ? 'বিশেষজ্ঞ' : 'Specialist');
        $primaryChamber = $doctor->chambers->sortByDesc('is_main')->first();
        
        $locationName = '';
        if ($primaryChamber && $primaryChamber->area) {
            $locationName = $primaryChamber->area->getTranslation('name', $locale) ?: $primaryChamber->area->name;
        } elseif ($primaryChamber && $primaryChamber->hospital) {
            $locationName = $primaryChamber->hospital->getTranslation('name', $locale) ?: $primaryChamber->hospital->name;
        } else {
            $locationName = $isBn ? 'বাংলাদেশ' : 'Bangladesh';
        }

        $doctorName = $doctor->getTranslation('name', $locale) ?: $doctor->name;
        $doctorDesignation = $doctor->getTranslation('designation', $locale) ?: $doctor->designation;

        if ($isBn) {
            if ($doctorDesignation) {
                $title = "{$doctorName} — {$doctorDesignation}, {$locationName}";
            } else {
                $title = "{$doctorName} — {$primarySpecialty}, {$locationName}";
            }
            $desc = "{$doctorName}" . ($doctorDesignation ? " — {$doctorDesignation}" : '') .
                    ($spNames ? " ({$spNames})" : '') .
                    "। চেম্বারের ঠিকানা, সিরিয়াল ও অ্যাপয়েন্টমেন্টের সময়সূচি জানুন।";
        } else {
            if ($doctorDesignation) {
                $title = "{$doctorName} — {$doctorDesignation} in {$locationName}";
            } else {
                $title = "{$doctorName} — {$primarySpecialty} in {$locationName}";
            }
            $desc = "{$doctorName} is a {$doctorDesignation}" .
                    ($spNames ? " specializing in {$spNames}" : '') .
                    ". {$doctor->experience_years} years of experience. Find chamber locations, visiting hours & contact.";
        }

        $seo = $doctor->seoMeta;
        if ($seo) {
            $seoTitle = $seo->getTranslation('title', $locale, false) ?: $seo->title;
            $seoDesc = $seo->getTranslation('description', $locale, false) ?: $seo->description;
            if ($seoTitle) $title = $seoTitle;
            else $title = "$title | DoctorBD24";
            if ($seoDesc) $desc = $seoDesc;
        } else {
            $title = "$title | DoctorBD24";
        }

        if ($tab === 'videos') {
            $title = ($isBn ? "ভিডিও: " : "Videos of ") . "{$doctorName} | DoctorBD24";
        } elseif ($tab === 'blog') {
            $title = ($isBn ? "ব্লগ: " : "Blog by ") . "{$doctorName} | DoctorBD24";
        }
        SEOTools::setCanonical(url()->current());

        SEOTools::setTitle($title, false);
        SEOTools::setDescription(\Illuminate\Support\Str::limit($desc, 160));
        if ($seo && $seo->keywords) {
            $seoKeywords = $seo->getTranslation('keywords', $locale, false) ?: $seo->keywords;
            if ($seoKeywords) {
                SEOTools::metatags()->addKeyword(explode(',', $seoKeywords));
            }
        }

        $baseUrl = $isBn ? url('/bn') : url('/');

        OpenGraph::setUrl(url()->current());
        OpenGraph::setType('profile');
        
        $ogImage = ($seo && $seo->og_image) ? (str_starts_with($seo->og_image, 'http') ? $seo->og_image : asset('storage/' . $seo->og_image)) : ($doctor->photo ? asset('storage/' . $doctor->photo) : asset('assets/images/logo.png'));
        if ($ogImage) OpenGraph::addImage($ogImage);

        JsonLdMulti::setType('Physician');
        JsonLdMulti::setTitle($doctorName);
        JsonLdMulti::setDescription($desc);
        JsonLdMulti::addValue('url', url()->current());
        
        if ($ogImage) JsonLdMulti::addValue('image', $ogImage);
        
        if ($spNames) {
            $specialtiesArray = explode(',', $spNames);
            $specialtySchema = [];
            foreach ($specialtiesArray as $sp) {
                $specialtySchema[] = [
                    '@type' => 'MedicalSpecialty',
                    'name'  => trim($sp)
                ];
            }
            JsonLdMulti::addValue('medicalSpecialty', $specialtySchema);
        }
        
        $fee = $doctor->fee ?? $doctor->consultation_fee ?? '৳500-৳1000';
        JsonLdMulti::addValue('priceRange', (string)$fee);
        
        if ($primaryChamber) {
            if (!empty($primaryChamber->phone)) {
                JsonLdMulti::addValue('telephone', $primaryChamber->phone);
            }
            if (!empty($primaryChamber->address)) {
                $addressData = [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $primaryChamber->address,
                    'addressCountry' => 'BD'
                ];
                
                if ($primaryChamber->area) {
                    $areaName = $primaryChamber->area->getTranslation('name', 'en', false) ?: $primaryChamber->area->name;
                    if ($areaName) {
                        $addressData['addressLocality'] = $areaName;
                    }
                }
                
                if (preg_match('/([1-9১-৯][0-9০-৯]{3})\s*$/u', $primaryChamber->address, $matches)) {
                    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
                    $en = ['0','1','2','3','4','5','6','7','8','9'];
                    $addressData['postalCode'] = str_replace($bn, $en, $matches[1]);
                }
                
                JsonLdMulti::addValue('address', $addressData);
            }
        }

        if ($doctor->approvedReviews && $doctor->approvedReviews->count() > 0) {
            JsonLdMulti::addValue('aggregateRating', [
                '@type'       => 'AggregateRating',
                'ratingValue' => round((float)$doctor->average_rating, 1),
                'reviewCount' => $doctor->approvedReviews->count(),
            ]);
        }

        // Breadcrumb Schema
        $breadcrumb = \Artesaos\SEOTools\Facades\JsonLdMulti::newJsonLd();
        $breadcrumb->setType('BreadcrumbList');
        $breadcrumb->addValue('itemListElement', [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => $isBn ? 'হোম' : 'Home',
                'item' => $baseUrl
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $isBn ? 'ডাক্তারগণ' : 'Doctors',
                'item' => $baseUrl . '/doctors'
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $doctorName,
                'item' => url()->current()
            ]
        ]);
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
