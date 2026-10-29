<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\BodyPart;
use App\Models\Specialty;
use App\Models\Symptom;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiseaseController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $isBn = $locale === 'bn';

        // SEO Title & Description
        $seoTitle = $isBn
            ? 'রোগ ও স্বাস্থ্য সমস্যা নির্দেশিকা (লক্ষণ, কারণ ও বিশেষজ্ঞ ডাক্তার) | DoctorBD24'
            : 'Diseases & Conditions Guide (Symptoms, Causes & Doctors) | DoctorBD24';
            
        $seoDesc = $isBn
            ? 'সহজ বাংলায় বিভিন্ন রোগের লক্ষণ, কারণ, প্রতিরোধ, চিকিৎসা এবং বিশেষজ্ঞ ডাক্তারের সন্ধান পান। মেয়ো ক্লিনিক মানদণ্ডে তৈরি স্বাস্থ্য নির্দেশিকা।'
            : 'Find easy-to-understand medical information about symptoms, causes, diagnosis, and expert doctors in Bangladesh.';

        SEOTools::setTitle($seoTitle);
        SEOTools::setDescription($seoDesc);
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::twitter()->setSite('@doctorbd24');

        $query = Disease::query()->where('status', 'published')->with(['primarySpecialty', 'symptoms']);

        // Search Filter
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Letter Filter (A-Z)
        $selectedLetter = null;
        if ($request->filled('letter')) {
            $selectedLetter = strtoupper(substr($request->query('letter'), 0, 1));
            $query->where('first_letter', $selectedLetter);
        }

        // Specialty Filter
        if ($request->filled('specialty')) {
            $specialtySlug = $request->query('specialty');
            $query->whereHas('primarySpecialty', function ($q) use ($specialtySlug) {
                $q->where('slug', $specialtySlug);
            });
        }

        // Body System Filter
        if ($request->filled('system')) {
            $systemSlug = $request->query('system');
            $query->whereHas('bodySystem', function ($q) use ($systemSlug) {
                $q->where('slug', $systemSlug);
            });
        }

        $diseases = $query->orderBy('name->en', 'asc')->paginate(18)->withQueryString();

        // Featured Diseases for Top Highlights
        $featuredDiseases = Disease::where('status', 'published')
            ->where('is_featured', true)
            ->with(['primarySpecialty', 'bodySystem'])
            ->take(6)
            ->get();

        // Body Parts for Anatomical Category Browsing
        $bodyParts = BodyPart::where('is_active', true)
            ->orderBy('order_index')
            ->withCount('symptoms')
            ->get();

        // Available letters for A-Z bar
        $availableLetters = Disease::where('status', 'published')
            ->selectRaw('UPPER(first_letter) as letter, COUNT(*) as count')
            ->groupBy('letter')
            ->pluck('count', 'letter')
            ->toArray();

        return view('diseases.index', compact(
            'diseases',
            'featuredDiseases',
            'bodyParts',
            'availableLetters',
            'selectedLetter'
        ));
    }

    public function show(string $slug)
    {
        $disease = Disease::where('slug', $slug)
            ->where('status', 'published')
            ->with([
                'primarySpecialty',
                'specialties',
                'bodySystem',
                'reviewerDoctor.specialties',
                'reviewerDoctor.chambers',
                'symptoms.bodyPart',
                'references'
            ])
            ->firstOrFail();

        $disease->incrementViewCount();
        $locale = app()->getLocale();
        $isBn = $locale === 'bn';

        $name = $disease->getTranslation('name', $locale);
        $overviewSnippet = strip_tags($disease->getTranslation('overview', $locale));
        $seoDesc = Str::limit($overviewSnippet, 160) ?: ($isBn ? "{$name} রোগের লক্ষণ, কারণ, প্রতিরোধ ও বিশেষজ্ঞ চিকিৎসকের তথ্য জানুন।" : "Learn about {$name} symptoms, causes, diagnosis, and specialist doctors in Bangladesh.");

        // Apply SEO Meta
        $seoMeta = $disease->seoMeta;
        if ($seoMeta) {
            $customTitle = $seoMeta->getTranslation('title', $locale, false);
            $customDesc = $seoMeta->getTranslation('description', $locale, false);
            SEOTools::setTitle($customTitle ?: "{$name} - লক্ষণ, কারণ ও চিকিৎসা | DoctorBD24");
            SEOTools::setDescription($customDesc ?: $seoDesc);
        } else {
            SEOTools::setTitle($isBn ? "{$name} - লক্ষণ, কারণ, প্রতিরোধ ও চিকিৎসা | DoctorBD24" : "{$name} - Symptoms, Causes & Treatments | DoctorBD24");
            SEOTools::setDescription($seoDesc);
        }

        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'article');
        if ($disease->image) {
            SEOTools::opengraph()->addImage(asset('storage/' . $disease->image));
        }

        // Apply SEO Meta Keywords: Record's seo_keywords if present, else fallback to global default
        $recordKeywords = $disease->seo_keywords 
            ?: ($seoMeta?->getTranslation('keywords', $locale, false) ?: ($seoMeta?->keywords ?? null));
        if (!empty($recordKeywords)) {
            $kwList = is_array($recordKeywords) ? $recordKeywords : array_filter(array_map('trim', explode(',', $recordKeywords)));
            if (!empty($kwList)) {
                SEOTools::metatags()->setKeywords($kwList);
            }
        }

        // Canonical URL: Self-canonical (EN URL on EN page, BN URL on BN page)
        $canonicalUrl = $isBn ? route('bn.diseases.show', $disease->slug) : route('diseases.show', $disease->slug);
        SEOTools::setCanonical($canonicalUrl);

        // Relevant Specialist Doctors from Existing Database
        $relevantDoctors = $disease->getRelevantDoctorsQuery()
            ->take(6)
            ->get();

        // Related Diseases in the same specialty or specified related_conditions
        $relatedConditionsList = collect();
        if (!empty($disease->related_conditions) && is_array($disease->related_conditions)) {
            $relatedConditionsList = Disease::where('status', 'published')
                ->whereIn('slug', $disease->related_conditions)
                ->where('id', '!=', $disease->id)
                ->take(4)
                ->get();
        }

        if ($relatedConditionsList->isEmpty()) {
            $relatedDiseases = Disease::where('status', 'published')
                ->where('id', '!=', $disease->id)
                ->where(function ($q) use ($disease) {
                    if ($disease->primary_specialty_id) {
                        $q->where('primary_specialty_id', $disease->primary_specialty_id);
                    }
                })
                ->take(4)
                ->get();
        } else {
            $relatedDiseases = $relatedConditionsList;
        }

        // Related Hospitals if assigned
        $relatedHospitals = collect();
        if (!empty($disease->related_hospitals) && is_array($disease->related_hospitals)) {
            $relatedHospitals = \App\Models\Hospital::whereIn('id', $disease->related_hospitals)
                ->where('status', 'published')
                ->take(3)
                ->get();
        }

        // Related Articles if assigned
        $relatedArticles = collect();
        if (!empty($disease->related_articles) && is_array($disease->related_articles)) {
            $relatedArticles = \App\Models\BlogPost::whereIn('id', $disease->related_articles)
                ->where('status', 'published')
                ->take(3)
                ->get();
        }

        return view('diseases.show', compact('disease', 'relevantDoctors', 'relatedDiseases', 'relatedHospitals', 'relatedArticles'));
    }
}
