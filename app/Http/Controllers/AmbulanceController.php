<?php

namespace App\Http\Controllers;

use App\Models\Ambulance;
use App\Models\Division;
use Illuminate\Http\Request;

class AmbulanceController extends Controller
{
    public function index()
    {
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
        $ambulance = Ambulance::published()->where('slug', $slug)->with(['area.district.division', 'reviews.user'])->firstOrFail();
        
        $ambulance->incrementViewCount();

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

        \Artesaos\SEOTools\Facades\SEOTools::setCanonical(route('ambulances.show', $ambulance->slug));
        \Artesaos\SEOTools\Facades\OpenGraph::setUrl(route('ambulances.show', $ambulance->slug));
        \Artesaos\SEOTools\Facades\OpenGraph::setType('website');

        $ogImage = ($seo && $seo->og_image) ? (str_starts_with($seo->og_image, 'http') ? $seo->og_image : asset('storage/' . $seo->og_image)) : ($ambulance->gallery && count($ambulance->gallery) > 0 ? asset('storage/' . $ambulance->gallery[0]) : null);
        if ($ogImage) \Artesaos\SEOTools\Facades\OpenGraph::addImage($ogImage);

        \Artesaos\SEOTools\Facades\JsonLd::setTitle($seo->title ?? $ambulance->provider_name);
        \Artesaos\SEOTools\Facades\JsonLd::setDescription($desc);
        \Artesaos\SEOTools\Facades\JsonLd::addValue('url', route('ambulances.show', $ambulance->slug));
        if ($ogImage) \Artesaos\SEOTools\Facades\JsonLd::addValue('image', $ogImage);
        // ─────────────────────────────────────────────
        
        $related = Ambulance::published()->where('type', $ambulance->type)
            ->where('id', '!=', $ambulance->id)
            ->take(4)
            ->get();
            
        return view('ambulances.show', compact('ambulance', 'related'));
    }
}
