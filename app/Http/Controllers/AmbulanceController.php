<?php

namespace App\Http\Controllers;

use App\Models\Ambulance;
use App\Models\Division;
use Illuminate\Http\Request;

class AmbulanceController extends Controller
{
    public function index()
    {
        \Artesaos\SEOTools\Facades\SEOTools::setTitle('Ambulance Services | DoctorBD24');
        \Artesaos\SEOTools\Facades\SEOTools::setDescription('Find 24/7 emergency ambulance services across Bangladesh. Search ICU, NICU, Freezing, and AC/Non-AC ambulances near you.');
        \Artesaos\SEOTools\Facades\SEOTools::setCanonical(url()->current());
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
        $ambulance = Ambulance::published()->where('slug', $slug)->with(['area.district.division', 'reviews.user'])->first();
        
        if (!$ambulance) {
            $redirect = \App\Models\RedirectLog::where('from_url', 'ambulance/' . $slug)->first();
            if ($redirect) {
                if ($redirect->to_url === '410') {
                    abort(410, 'Gone');
                }
                return redirect($redirect->to_url, 301);
            }
            abort(404);
        }
        
        $ambulance->incrementViewCount();
        
        $hasBn = !empty($ambulance->getTranslation('provider_name', 'bn', false));
        \Illuminate\Support\Facades\View::share('has_bn_translation', $hasBn);
        
        if (app()->getLocale() === 'bn' && !$hasBn) {
            \Illuminate\Support\Facades\View::share('noindex_page', true);
        }

        // ── SEO ──────────────────────────────────────
        $title = "{$ambulance->provider_name} — Ambulance Service";
        $desc = "{$ambulance->provider_name} provides emergency ambulance services.";

        $seo = $ambulance->seoMeta;
        if ($seo && $seo->title) {
            $title = $seo->title;
        } else {
            $title = "$title | DoctorBD24";
        }

        if ($seo && $seo->description) {
            $desc = $seo->description;
        }

        \Artesaos\SEOTools\Facades\SEOTools::setTitle($title, false);
        \Artesaos\SEOTools\Facades\SEOTools::setDescription(\Illuminate\Support\Str::limit($desc, 160));
        if ($seo && $seo->keywords) {
            \Artesaos\SEOTools\Facades\SEOTools::metatags()->addKeyword(explode(',', $seo->keywords));
        }

        \Artesaos\SEOTools\Facades\SEOTools::setCanonical(url()->current());
        \Artesaos\SEOTools\Facades\OpenGraph::setUrl(route('ambulances.show', $ambulance->slug));
        \Artesaos\SEOTools\Facades\OpenGraph::setType('website');

        $ogImage = ($seo && $seo->og_image) ? (str_starts_with($seo->og_image, 'http') ? $seo->og_image : asset('storage/' . $seo->og_image)) : ($ambulance->gallery && count($ambulance->gallery) > 0 ? asset('storage/' . $ambulance->gallery[0]) : null);
        if ($ogImage) \Artesaos\SEOTools\Facades\OpenGraph::addImage($ogImage);

        \Artesaos\SEOTools\Facades\JsonLdMulti::setType('LocalBusiness');
        \Artesaos\SEOTools\Facades\JsonLdMulti::setTitle($seo->title ?? $ambulance->provider_name);
        \Artesaos\SEOTools\Facades\JsonLdMulti::setDescription($desc);
        \Artesaos\SEOTools\Facades\JsonLdMulti::addValue('url', route('ambulances.show', $ambulance->slug));
        
        if (!empty($ambulance->hotline)) {
            \Artesaos\SEOTools\Facades\JsonLdMulti::addValue('telephone', $ambulance->hotline);
        }
        
        if (!empty($ambulance->base_location)) {
            \Artesaos\SEOTools\Facades\JsonLdMulti::addValue('address', [
                '@type' => 'PostalAddress',
                'streetAddress' => $ambulance->base_location,
                'addressCountry' => 'BD'
            ]);
        }
        
        if ($ogImage) \Artesaos\SEOTools\Facades\JsonLdMulti::addValue('image', $ogImage);
        // ─────────────────────────────────────────────
        
        $related = Ambulance::published()->where('type', $ambulance->type)
            ->where('id', '!=', $ambulance->id)
            ->take(4)
            ->get();
            
        return view('ambulances.show', compact('ambulance', 'related'));
    }
}
