<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\BlogPost;
use App\Models\Specialty;
use App\Models\Division;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\JsonLd;

use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        // ── SEO ──────────────────────────────────────
        $seoTitle = Setting::get('homepage_seo_title', 'DoctorBD24 — বাংলাদেশের সেরা স্বাস্থ্যসেবা ডিরেক্টরি');
        $seoDesc  = Setting::get('homepage_seo_description', 'বাংলাদেশের সেরা ডাক্তার, হাসপাতাল, অ্যাম্বুল্যান্স এবং স্বাস্থ্যসেবার সম্পূর্ণ ডিরেক্টরি। সঠিক ডাক্তার খুঁজুন, সহজেই যোগাযোগ করুন।');
        $seoKey   = Setting::get('homepage_seo_keywords', 'doctor, hospital, ambulance, bangladesh');

        SEOTools::setTitle($seoTitle);
        SEOTools::setDescription($seoDesc);
        SEOTools::metatags()->addMeta('keywords', $seoKey);
        SEOTools::setCanonical(url()->current());

        OpenGraph::setUrl(url('/'));
        OpenGraph::setType('website');
        OpenGraph::addProperty('locale', 'bn_BD');

        JsonLd::setType('WebSite');
        JsonLd::setTitle('DoctorBD24');
        JsonLd::setDescription('বাংলাদেশের স্বাস্থ্যসেবা ডিরেক্টরি');
        JsonLd::addValue('url', url('/'));
        JsonLd::addValue('potentialAction', [
            '@type'       => 'SearchAction',
            'target'      => url('/doctors') . '?search={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ]);
        // ─────────────────────────────────────────────

        $featuredDoctors = Doctor::published()->where('verified', true)
            ->where('featured', true)
            ->with('specialties', 'chambers')
            ->take(6)
            ->get();

        $featuredHospitals = Hospital::published()->where('verified', true)
            ->where('featured', true)
            ->take(4)
            ->get();

        $recentPosts = BlogPost::published()
            ->with('category')
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        $specialties = Specialty::orderBy('name->en')->get();
        $divisions   = Division::orderBy('id')->get();

        $heroTitle    = Setting::get('homepage_hero_title', 'Find the Right Doctor');
        $heroSubtitle = Setting::get('homepage_hero_subtitle', 'Over :doctors+ doctors, :hospitals+ hospitals and complete healthcare information in one place in Bangladesh.');

        $stats = [
            'doctors'     => Doctor::published()->count(),
            'hospitals'   => Hospital::published()->count(),
            'areas'       => \App\Models\Area::count(),
            'specialties' => Specialty::count(),
        ];

        return view('home', compact(
            'featuredDoctors', 'featuredHospitals', 'recentPosts',
            'specialties', 'divisions', 'stats',
            'heroTitle', 'heroSubtitle'
        ));
    }
}
