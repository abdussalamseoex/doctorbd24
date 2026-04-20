<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\Specialty;
use App\Models\Division;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\JsonLdMulti;

class HospitalController extends Controller
{
    public function index()
    {
        return view('hospitals.index');
    }

    public function show(string $slug, string $tab = 'overview')
    {
        if (!in_array($tab, ['overview', 'doctors', 'diagnostics', 'video', 'blog'])) {
            abort(404);
        }
        $hospital = Hospital::published()
            ->where('slug', $slug)
            ->with(['area.district.division', 'approvedReviews.user', 'chambers.doctor.specialties'])
            ->first();

        if (!$hospital) {
            $redirect = \App\Models\RedirectLog::where('from_url', 'hospital/' . $slug)->first();
            if ($redirect) {
                if ($redirect->to_url === '410') {
                    abort(410, 'Gone');
                }
                return redirect($redirect->to_url, 301);
            }
            abort(404);
        }

        $hospital->incrementViewCount();
        
        $hasBn = !empty($hospital->getTranslation('name', 'bn', false));
        \Illuminate\Support\Facades\View::share('has_bn_translation', $hasBn);
        
        if (app()->getLocale() === 'bn' && !$hasBn) {
            \Illuminate\Support\Facades\View::share('noindex_page', true);
        }

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

        SEOTools::setCanonical(url()->current());
        OpenGraph::setUrl(route('hospitals.show', $hospital->slug));
        OpenGraph::setType('place');

        $ogImage = ($seo && $seo->og_image) ? (str_starts_with($seo->og_image, 'http') ? $seo->og_image : asset('storage/' . $seo->og_image)) : ($hospital->logo ? asset('storage/' . $hospital->logo) : null);
        if ($ogImage) OpenGraph::addImage($ogImage);

        JsonLdMulti::setType('Hospital');
        JsonLdMulti::setTitle($seo->title ?? $hospital->name);
        JsonLdMulti::setDescription($desc);
        JsonLdMulti::addValue('url', route('hospitals.show', $hospital->slug));
        
        // Add dynamic price range for hospital to resolve schema error
        JsonLdMulti::addValue('priceRange', '৳500-৳5000');
        
        if ($hospital->phone) {
            JsonLdMulti::addValue('telephone', $hospital->phone);
        }
        
        if ($hospital->address) {
            $addressData = [
                '@type' => 'PostalAddress',
                'streetAddress' => $hospital->address,
                'addressCountry' => 'BD'
            ];
            
            $areaName = $hospital->area?->getTranslation('name', 'en', false) ?: ($hospital->area?->name ?? '');
            if ($areaName) {
                $addressData['addressLocality'] = $areaName;
            }
            
            if (preg_match('/([1-9১-৯][0-9০-৯]{3})\s*$/u', $hospital->address, $matches)) {
                $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
                $en = ['0','1','2','3','4','5','6','7','8','9'];
                $addressData['postalCode'] = str_replace($bn, $en, $matches[1]);
            }
            
            JsonLdMulti::addValue('address', $addressData);
        }
        
        if ($ogImage) JsonLdMulti::addValue('image', $ogImage);
        
        $breadcrumb = \Artesaos\SEOTools\Facades\JsonLdMulti::newJsonLd();
        $breadcrumb->setType('BreadcrumbList');
        $breadcrumb->addValue('itemListElement', [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url('/')
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Hospitals',
                'item' => url('/hospitals')
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $hospital->name,
                'item' => route('hospitals.show', $hospital->slug)
            ]
        ]);
        // ─────────────────────────────────────────────

        // get unique doctors from chambers
        $doctors = $hospital->chambers->map(fn($c) => $c->doctor)->filter(fn($d) => $d && $d->is_live)->unique('id');

        $specialtyFilter = request('specialty');
        if ($specialtyFilter) {
            $doctors = $doctors->filter(fn($d) => $d->specialties->pluck('slug')->contains($specialtyFilter));
        }

        $specialties = \App\Models\Specialty::orderBy('name->en')->get();

        return view('hospitals.show', compact('hospital', 'doctors', 'specialties', 'tab'));
    }

    public function showDiagnosticTest(string $hospitalSlug, string $serviceSlug)
    {
        $hospital = Hospital::published()
            ->where('slug', $hospitalSlug)
            ->with(['area.district.division'])
            ->firstOrFail();

        $service = $hospital->hospitalServices()
            ->where('slug', $serviceSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $hospital->incrementViewCount();
        
        $hasBn = !empty($hospital->getTranslation('name', 'bn', false));
        \Illuminate\Support\Facades\View::share('has_bn_translation', $hasBn);
        
        if (app()->getLocale() === 'bn' && !$hasBn) {
            \Illuminate\Support\Facades\View::share('noindex_page', true);
        }

        // ── Dynamic SEO generation for the specific test ──────────────────────
        $areaName = $hospital->area?->getTranslation('name', 'en') ?? '';
        $locationStr = $areaName ? " $areaName," : "";
        $testName = \Illuminate\Support\Str::title($service->service_name);
        
        $title = "{$testName} Price & Details at {$hospital->name}{$locationStr} Bangladesh | DoctorBD24";
        $priceStr = $service->price ? " The current price is around Tk {$service->price}." : "";
        $desc = "Find the latest cost, procedure, and details for the {$testName} test at {$hospital->name} in{$locationStr} Bangladesh.{$priceStr} Book your appointment or call for inquiries.";

        SEOTools::setTitle($title, false);
        SEOTools::setDescription(\Illuminate\Support\Str::limit($desc, 160));
        
        $keywords = [$testName, "{$testName} test cost", "{$testName} price in {$hospital->name}", $hospital->name, $areaName];
        SEOTools::metatags()->addKeyword(array_filter($keywords));

        SEOTools::setCanonical(url()->current());
        OpenGraph::setUrl(route('hospitals.diagnostic.show', [$hospital->slug, $service->slug]));
        OpenGraph::setType('article');

        $ogImage = $hospital->logo ? asset('storage/' . $hospital->logo) : null;
        if ($ogImage) OpenGraph::addImage($ogImage);

        JsonLdMulti::setType('MedicalTest');
        JsonLdMulti::setTitle($title);
        JsonLdMulti::setDescription($desc);
        JsonLdMulti::addValue('url', route('hospitals.diagnostic.show', [$hospital->slug, $service->slug]));
        // ─────────────────────────────────────────────

        return view('hospitals.diagnostic_show', compact('hospital', 'service'));
    }
}
